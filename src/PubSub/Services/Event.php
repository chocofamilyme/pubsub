<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Exceptions\ModelException;
use Chocofamily\PubSub\Exceptions\ValidateException;
use Chocofamily\PubSub\Models\Event as EventModel;

/**
 * Class Event
 *
 * Работет с данными события
 *
 * @package Chocofamily\PubSub\Services
 */
class Event
{
    /** @var EventModel */
    private $model;

    /**
     * Event constructor.
     *
     * @param \Chocofamily\PubSub\Models\Event|null $model
     */
    public function __construct($model = null)
    {
        $this->model     = $model;
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getPayload(): array
    {
        if (empty($this->model->getId())) {
            throw new ValidateException('Модель не сохранена');
        }

        return array_merge(
            [
                'event_id' => $this->model->getId(),
            ],
            $this->model->getPayload()
        );
    }

    /**
     * @param \Chocofamily\PubSub\Models\Event $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Обновить статус на отправлено
     *
     * @throws \ErrorException
     */
    public function setSent()
    {
        $this->model->status = EventModel::SENT;
        if ($this->model->update() === false) {
            foreach ($this->model->getMessages() as $message) {
                throw new ModelException($message);
            }
        }
    }

    /**
     * Не отправленные события
     *
     * @param \DateTime        $from
     * @param int              $limit
     *
     * @return array
     */
    public static function getFailMessage(
        \DateTime $from,
        int $limit = 200
    ) {
        return EventModel::find([
            'status = :no_send: AND created_at > :start_at:',
            'bind' => [
                'no_send'  => EventModel::NEW,
                'start_at' => $from->format('Y-m-d H:i:s'),
            ],
            'limit'    => $limit,
            'order'    => 'id',
        ]);
    }
}
