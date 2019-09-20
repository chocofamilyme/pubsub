<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Models\ModelInterface;
use Chocofamily\PubSub\Provider\Adapter;
use Chocofamily\PubSub\Publisher;
use Chocofamily\PubSub\Services\Event as EventService;
use ErrorException;

/**
 * Class Event
 *
 * Отправляет событие
 *
 * @package Chocofamily\PubSub\Services
 */
class EventPublish
{
    /** @var ModelInterface */
    private $event;
    private $publisher;

    /**
     * EventPublish constructor.
     *
     * @param  Adapter $eventSource
     * @param  ModelInterface|null $model
     */
    public function __construct(Adapter $eventSource, ?ModelInterface $model = null)
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
     * @param string $route
     *
     * @param string $exchangeName
     *
     * @throws ErrorException
     */
    public function publish(string $route, string $exchangeName = '')
    {
        $this->publisher->send($this->event->getPayload(), $route, $exchangeName);
        $this->event->setSent();
    }
}
