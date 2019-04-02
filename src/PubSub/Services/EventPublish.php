<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Publisher;
use Chocofamily\PubSub\Services\Event as EventService;

/**
 * Class Event
 *
 * Отправляет событие
 *
 * @package Chocofamily\PubSub\Services
 */
class EventPublish
{
    /** @var \Chocofamily\PubSub\Models\Event */
    private $event;
    private $publisher;

    /**
     * EventPublish constructor.
     *
     * @param                                       $eventSource
     * @param \Chocofamily\PubSub\Models\Event|null $model
     */
    public function __construct($eventSource, $model = null)
    {
        if ($model) {
            $this->event = new EventService($model);
        }

        $this->publisher = new Publisher($eventSource);
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->publisher->setHeader($headers);
    }

    /**
     * @param \Chocofamily\PubSub\Models\Event $model
     */
    public function setModel($model)
    {
        $this->event = new EventService($model);
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
