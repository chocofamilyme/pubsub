<?php

namespace Unit\Pub\Event;

use Helper\PubSub\Models\Event;
use Chocofamily\PubSub\Services\EventPublish;
use Helper\PubSub\DefaultProvider;

class PublisherCest
{
    /**
     * Отправка данных
     *
     * @param \UnitTester $I
     *
     * @throws \ErrorException
     */
    public function tryToSend(\UnitTester $I)
    {
        $provider        = new DefaultProvider();
        $event           = new Event();
        $event->type     = 1;
        $event->status   = Event::NEW;
        $event->model_id = 1;
        $event->payload  = [
            'id'   => 1,
            'text' => 'Hello',
        ];


        $eventPublish = new EventPublish($provider, $event);

        $eventPublish->publish('test.queue');

        $I->assertEquals($provider->queue['test.queue']['message'], '{"event_id":1,"id":1,"text":"Hello"}');
        $I->assertEquals($event->getStatus(), Event::SENT);
    }
}
