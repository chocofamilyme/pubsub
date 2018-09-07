<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Publisher;
use \Chocofamily\PubSub\Models\Event as EventModel;

/**
 * Class Event
 *
 * Отправляет событие
 *
 * @package Chocofamily\PubSub\Services
 */
class EventPublish
{
    /** @var EventModel */
    private $event;
    private $publisher;

    public function __construct($eventSource, EventModel $model = null)
    {
        if ($model) {
            $this->event = new Event($model);
        }

        $this->publisher = new Publisher($eventSource);
    }


    /**
     * @param EventModel $model
     */
    public function setModel(EventModel $model)
    {
        $this->event = new Event($model);
    }

    /**
     * @param string $to
     *
     * @throws \ErrorException
     */
    public function publish(string $to)
    {
        $this->publisher->send($this->event->getPayload(), $to);
        $this->event->setSent();
    }
}
