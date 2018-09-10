<?php

namespace Unit\Pub;

use Chocofamily\PubSub\Provider\RabbitMQ;
use Helper\PubSub\DefaultCache;
use Chocofamily\PubSub\Publisher;

class PublisherCest
{
    // tests
    public function tryToSend(\UnitTester $I)
    {
        $config = [
            'host'     => 'localhost',
            'port'     => '5674',
            'user'     => 'guest',
            'password' => 'guest',
        ];

        $I->declareExchange(
            'test', // exchange name
            'topic' // exchange type
        );

        $I->declareQueue(
            'test' // queue name
        );

        $I->bindQueueToExchange(
            'test', // name of the queue
            'test', // exchange name to bind to
            'test.queue' // Optionally, provide a binding key
        );

        $provider = RabbitMQ::getInstance($config, new DefaultCache());

        $publisher = new Publisher($provider);
        $publisher->send([
            'event_id' => 1,
            'text'     => 'Hello',
        ], 'test.queue');


        $message = $I->grabMessageFromQueue('test');

        codecept_debug($message);

        $I->purgeQueue('test');
    }
}
