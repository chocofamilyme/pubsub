<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

use Chocofamily\PubSub\Repeater;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use Chocofamily\PubSub\Cache;
use Chocofamily\PubSub\Exceptions\RetryException;
use Chocofamily\PubSub\Exceptions\ValidateException;
use Chocofamily\PubSub\Provider\RabbitMQ\Message\Output as OutputMessage;
use Chocofamily\PubSub\Provider\RabbitMQ\Exchange;
use Chocofamily\PubSub\Provider\RabbitMQ\Message\Input as InputMessage;

/**
 * Class RabbitMQ
 * Работает с брокером сообщений RabbitMQ
 *
 * @package Chocofamily\PubSub\Provider
 */
class RabbitMQ implements Adapter
{
    const REDELIVERY_COUNT = 5;
    const CACHE_LIFETIME   = 1800;

    private static $instance;

    private $config;

    /**
     * Маршрутизация. По умолчанию топик
     *
     * @var string
     */
    private $type = 'topic';

    /**
     *
     * @var bool
     */
    private $passive = false;

    /**
     * Сохранять на диск
     *
     * @var bool
     */
    private $durable = true;

    /**
     * Удаление exchange если нет подключений к нему
     *
     * @var bool
     */
    private $auto_delete = false;

    /** @var AMQPStreamConnection */
    private $connection;

    /** @var AMQPChannel */
    private $currentChannel;

    /** @var Exchange */
    private $currentExchange;

    private $exchanges = [];
    private $channels  = [];

    private $repeater;

    /** @var OutputMessage */
    private $message;

    /** @var bool */
    private $isConnected = false;

    /** @var callable */
    private $callback;

    /**
     * RabbitMQ constructor.
     *
     * @param array    $config
     * @param Repeater $repeater
     */
    private function __construct(array $config, Repeater $repeater)
    {
        $this->config = $config;
        $this->connect();
        $this->repeater = $repeater;
    }

    /**
     * @param array $config
     *
     * @param Cache $cache
     *
     * @return Adapter
     */
    public static function getInstance(array $config, Cache $cache): Adapter
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config, $cache);
        }

        return self::$instance;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'],
            $this->config['port'],
            $this->config['user'],
            $this->config['password']
        );

        $this->isConnected = true;
    }

    public function disconnect()
    {
        if ($this->isConnected()) {
            foreach ($this->channels as $channel) {
                $channel->close();
            }

            $this->connection->close();
            $this->isConnected = false;
        }
    }

    /**
     * Опубликовать сообщение
     *
     */
    public function publish()
    {
        $this->exchangeDeclare();

        $this->currentChannel->basic_publish(
            $this->message->getPayload(),
            $this->currentExchange->getName(),
            $this->currentExchange->getRoute()
        );
    }


    /**
     * Подписка на событие
     *
     * @param callable $callback    — Пользовательская функция для обработки сообщения
     * @param array    $params      — Настройки очереди подписчика
     * @param string   $consumerTag — Уникальное имя подписчика
     *
     * @throws ValidateException
     */
    public function subscribe($callback, array $params = [], string $consumerTag = '')
    {
        if (empty($params['queue_name'])) {
            throw new ValidateException('Имя очереди обязательный параметр');
        }

        $this->exchangeDeclare();

        $this->config = array_merge($params, $this->config);

        $queueName =
            $this->currentChannel->queue_declare(
                $params['queue_name'],
                false,
                $this->getConfig('durable', true),
                false,
                false,
                false,
                new AMQPTable($this->getConfig('queue', []))
            );

        $this->currentChannel->queue_bind(
            $queueName[0],
            $this->currentExchange->getName(),
            $this->currentExchange->getRoute()
        );

        $this->currentChannel->basic_qos(
            null,
            $this->getConfig('prefetch_count', 1),
            null
        );

        $this->currentChannel->basic_consume(
            $queueName[0],
            $consumerTag,
            false,
            false,
            false,
            false,
            [$this, 'callbackWrapper']
        );

        $this->callback = $callback;

        while (count($this->currentChannel->callbacks)) {
            $this->currentChannel->wait();
        }
    }


    /**
     * Объявление точки входа и канала
     */
    private function exchangeDeclare()
    {
        $key = $this->currentExchange->getName();

        if (isset($this->exchanges[$key]) == false) {
            $this->channels[$key] = $this->connection->channel();
            $this->channels[$key]->exchange_declare(
                $this->currentExchange->getName(),
                $this->type,
                $this->passive,
                $this->durable,
                $this->auto_delete
            );
            $this->exchanges[$this->currentExchange->getName()] = true;
        }

        $this->currentChannel = $this->channels[$key];
    }


    /**
     * @param AMQPMessage $msg
     */
    public function callbackWrapper(AMQPMessage $msg)
    {
        /** @var AMQPChannel $deliveryChannel */
        $deliveryChannel = $msg->delivery_info['channel'];

        $isNoAck = $this->getConfig('no_ack', false);

        $message = new InputMessage($msg);

        try {
            call_user_func($this->callback, $message);

        } catch (RetryException $e) {
            if ($isNoAck == false) {
                $repeat = $this->repeater->isRepeatable($message);
                $deliveryChannel->basic_reject($msg->delivery_info['delivery_tag'], $repeat);

                return;
            }
        } catch (\Exception $e) {
            $deliveryChannel->basic_reject($msg->delivery_info['delivery_tag'], false);

            return;
        }

        if ($isNoAck == false) {
            $deliveryChannel->basic_ack($msg->delivery_info['delivery_tag']);
        }
    }

    /**
     * @param string $queue
     */
    public function setCurrentExchange(string $queue)
    {
        $name                  = explode('.', $queue)[0];
        $this->currentExchange = new Exchange($name, $queue);
    }


    /**
     * @param array $message
     * @param array $headers
     */
    public function setMessage(array $message, array $headers = [])
    {
        $defaultHeaders = ['app_id' => $this->getConfig('app_id')];
        $headers        = array_merge($headers, $defaultHeaders);
        $this->message  = new OutputMessage($message, $headers);
    }


    /**
     * @param string $key
     * @param        $default
     *
     * @return mixed
     */
    private function getConfig(string $key, $default = '')
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
}
