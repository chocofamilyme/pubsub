<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Models\ModelInterface;
use ErrorException;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Chocofamily\PubSub\Models\Event as EventModel;
use Chocofamily\PubSub\SerializerInterface;
use Phalcon\Mvc\Model\TransactionInterface;

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

    /** @var TxManager */
    private $transactionManager;

    private $transaction;

    public function __construct(ModelInterface $model, SerializerInterface $modelSerializer, int $eventType)
    {
        $this->model              = $model;
        $this->modelSerializer    = $modelSerializer;
        $this->eventType          = $eventType;
        $this->transactionManager = new TxManager();
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
     * @throws ErrorException
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
        $transaction = $this->getTransaction();

        $this->model->setTransaction($transaction);

        if ($this->model->save() === false) {
            $messages = $this->model->getMessages();
            $transaction->rollback($messages[0]->getMessage());
        }
        $this->model->refresh();

        $eventModel = new EventModel();
        $eventModel->setTransaction($transaction);

        $eventModel->type        = $this->eventType;
        $eventModel->status      = EventModel::NEW;
        $eventModel->routing_key = $route;
        $eventModel->exchange    = $exchange;
        $eventModel->payload     = $this->modelSerializer->getAttributes($this->model);
        $eventModel->setModelId($this->model->getId());
        $eventModel->setModelType(get_class($this->model));

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
     * @return TransactionInterface
     */
    public function getTransaction(): TransactionInterface
    {

        if ($this->transaction) {
            return $this->transaction;
        }

        $this->transaction = $this->transactionManager->get();

        return $this->transaction;
    }

    /**
     * @param TransactionInterface $transaction
     */
    public function setTransaction(TransactionInterface $transaction)
    {
        $this->transaction = $transaction;
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
