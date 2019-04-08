<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Models\ModelInterface;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Chocofamily\PubSub\Models\Event as EventModel;
use Chocofamily\PubSub\SerializerInterface;

/**
 * Class EventPrepare
 *
 * Создает событие из бизнес модели
 *
 * @package Chocofamily\PubSub
 */
class EventPrepare
{
    /**
     * @var ModelInterface
     */
    private $model;

    /** @var SerializerInterface */
    private $modelSerializer;

    private $eventType;

    public function __construct(ModelInterface $model, SerializerInterface $modelSerializer, int $eventType)
    {
        $this->model           = $model;
        $this->modelSerializer = $modelSerializer;
        $this->eventType       = $eventType;
    }

    /**
     * Отправить событие
     *
     * @param        $eventSource
     *
     * @param string $route
     *
     * @param array  $headers
     *
     * @param string $exchange
     *
     * @throws \ErrorException
     */
    public function up($eventSource, string $route, array $headers = [], string $exchange = '')
    {
        $exchangeName = $this->getExchangeName($route, $exchange);
        $model        = $this->create($route, $exchangeName);

        $eventPublish = new EventPublish($eventSource, $model);
        $eventPublish->setHeaders($headers);
        $eventPublish->publish($model->getRoutingKey(), $model->getExchange());
    }

    /**
     * Создать запись о событии
     *
     * @param string $route
     * @param string $exchange
     *
     * @return EventModel
     */
    public function create(string $route, string $exchange)
    {
        $manager     = new TxManager();
        $transaction = $manager->get();

        $this->model->setTransaction($transaction);

        if ($this->model->save() === false) {
            $messages = $this->model->getMessages();
            $transaction->rollback($messages[0]->getMessage());
        }
        $this->model->refresh();

        $eventModel = new EventModel();

        $eventModel->setTransaction($transaction);

        $eventModel->model_id    = $this->model->getId();
        $eventModel->type        = $this->eventType;
        $eventModel->status      = EventModel::NEW;
        $eventModel->routing_key = $route;
        $eventModel->exchange    = $exchange;

        $eventModel->payload = $this->modelSerializer->getAttributes($this->model);

        if ($eventModel->save() === false) {
            $messages = $eventModel->getMessages();
            $transaction->rollback($messages[0]->getMessage());
        }

        $transaction->commit();

        $eventModel->refresh();

        //TODO с версии Phalcon 3.4 afterFetch вызывается автоматически
        $eventModel->afterFetch();

        return $eventModel;
    }

    /**
     * @param $route
     * @param $exchangeName
     *
     * @return string
     */
    protected function getExchangeName($route, $exchangeName): string
    {
        if (empty($exchangeName)) {
            $exchangeName = explode('.', $route)[0];
        }

        return $exchangeName;
    }
}
