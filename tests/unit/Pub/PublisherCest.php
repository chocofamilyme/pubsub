<?php

namespace Unit\Pub;

use Chocofamily\PubSub\Publisher;
use Helper\PubSub\DefaultProvider;

class PublisherCest
{
    /**
     * Отправка данных
     *
     * @param \UnitTester $I
     */
    public function tryToSend(\UnitTester $I)
    {
        $provider  = new DefaultProvider();
        $publisher = new Publisher($provider);

        $publisher->setHeader(['test' => 1]);
        $publisher->send([
            'event_id' => 1,
            'text'     => 'Hello',
        ], 'test.route', 'test');

        $I->assertEquals($provider->queue['test.route']['message'], '{"event_id":1,"text":"Hello"}');

        $I->assertArrayHasKey('test', $publisher->getHeader());
        $I->assertArrayHasKey('correlation_id', $publisher->getHeader());
        $I->assertArrayHasKey('application_headers', $publisher->getHeader());
    }
}
