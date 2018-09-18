<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Exceptions\ValidateException;
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
     * @var \Phalcon\Mvc\Model
     */
    private $model;

    /** @var SerializerInterface */
    private $modelSerializer;

    private $eventType;

    public function __construct(\Phalcon\Mvc\Model $model, SerializerInterface $modelSerializer, int $eventType)
    {
        $this->model           = $model;
        $this->modelSerializer = $modelSerializer;
        $this->eventType       = $eventType;
    }

    /**
     * Отправить событие
     *
     * @param string $to
     *
     * @param        $eventSource
     *
     * @throws \ErrorException
     */
    public function up($eventSource, string $to)
    {
        $model = $this->create();

        $eventPublish = new EventPublish($eventSource, $model);
        $eventPublish->publish($to);
    }

    /**
     * Создать запись о событии
     *
     * @return EventModel
     * @throws ValidateException
     */
    public function create()
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

        $eventModel->model_id = $this->model->getId();
        $eventModel->type     = $this->eventType;
        $eventModel->status   = EventModel::NEW;

        $eventModel->payload = $this->modelSerializer->getAttributes($this->model);

        if ($eventModel->save() === false) {
            $messages = $eventModel->getMessages();
            $transaction->rollback($messages[0]->getMessage());
        }

        $transaction->commit();

        $eventModel->refresh();

        return $eventModel;
    }
}
