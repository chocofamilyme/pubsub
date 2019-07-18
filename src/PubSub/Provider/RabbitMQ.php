<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

use Chocofamily\PubSub\Exceptions\ConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

use Chocofamily\PubSub\RepeaterInterface;
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
    const REDELIVERY_COUNT      = 5;
    const CACHE_LIFETIME        = 1800;
    const DEFAULT_EXCHANGE_TYPE = 'topic';

    private static $instance;

    private $config;

    /**
     *
     * @var bool
     */
    private $passive = false;

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

    private $unacknowledged = 0;

    /**
     * RabbitMQ constructor.
     *
     * @param array             $config
     * @param RepeaterInterface $repeater
     */
    private function __construct(array $config, RepeaterInterface $repeater)
    {
        $this->config   = $config;
        $this->repeater = $repeater;
    }

    /**
     * @param array             $config
     *
     * @param RepeaterInterface $repeater
     *
     * @return Adapter
     */
    public static function getInstance(array $config, RepeaterInterface $repeater): Adapter
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config, $repeater);
        }

        return self::$instance;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @throws ConnectionException
     */
    public function connect()
    {
        try {
            $this->connection = new AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['password']
            );
        } catch (\Exception $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }


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
     * @throws ConnectionException
     */
    public function publish()
    {
        $this->connect();
        $this->exchangeDeclare();

        $this->currentChannel->basic_publish(
            $this->message->getPayload(),
            $this->currentExchange->getName(),
            $this->currentExchange->getRoutes()[0]
        );
    }


    /**
     * Подписка на событие
     *
     * @param callable $callback    — Пользовательская функция для обработки сообщения
     * @param array    $params      — Настройки очереди подписчика
     * @param string   $consumerTag — Уникальное имя подписчика
     *
     * @throws ConnectionException
     * @throws ValidateException
     */
    public function subscribe($callback, array $params = [], string $consumerTag = '')
    {
        $this->connect();
        if (empty($params['queue_name'])) {
            throw new ValidateException('Имя очереди обязательный параметр');
        }

        $this->addConfig($params);

        $this->exchangeDeclare();

        $queueName =
            $this->currentChannel->queue_declare(
                $params['queue_name'],
                false,
                $this->getConfig('durable', true),
                $this->getConfig('exclusive', false),
                false,
                false,
                new AMQPTable($this->getConfig('queue', []))
            );

        foreach ($this->currentExchange->getRoutes() as $route) {
            $this->currentChannel->queue_bind(
                $queueName[0],
                $this->currentExchange->getName(),
                $route
            );
        }

        $this->currentChannel->basic_qos(
            null,
            $this->getConfig('prefetch_count', 1),
            null
        );

        $this->currentChannel->basic_consume(
            $queueName[0],
            $consumerTag,
            false,
            $this->getConfig('no_ack', false),
            $this->getConfig('basic_consume_exclusive', false),
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
                $this->getConfig('exchange_type', self::DEFAULT_EXCHANGE_TYPE),
                $this->passive,
                $this->getConfig('durable', true),
                $this->auto_delete
            );
            $this->exchanges[$this->currentExchange->getName()] = true;
        }

        $this->currentChannel = $this->channels[$key];
    }


    /**
     * @param AMQPMessage $msg
     *
     * @throws \Exception
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

            throw $e;
        }

        $this->unacknowledged++;
        if ($isNoAck == false and $this->unacknowledged == $this->getConfig('prefetch_count', 1)) {
            $this->unacknowledged = 0;
            $deliveryChannel->basic_ack(
                $msg->delivery_info['delivery_tag'],
                $this->getConfig('prefetch_count', 1) > 1
            );
        }
    }

    /**
     * @param string|array $route
     * @param string $exchangeName
     */
    public function setCurrentExchange($route, string $exchangeName = '')
    {
        if (false == is_array($route)) {
            $route = [$route];
        }

        if (empty($exchangeName)) {
            $exchangeName = explode('.', $route[0])[0];
        }

        $this->currentExchange = new Exchange($exchangeName, $route);
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
     * @param array $params
     */
    public function addConfig(array $params = [])
    {
        $this->config = array_merge($params, $this->config);
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
}
