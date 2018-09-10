<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Unit\Provider\RabbitMQ\Message;

use Chocofamily\PubSub\Provider\RabbitMQ\Message\Output as OutputMessage;
use PhpAmqpLib\Message\AMQPMessage;

class OutputCest
{
    public function tryToCreateOutputMessage(\UnitTester $I)
    {
        $headers = [
            'app_id'              => 'my_app',
            'application_headers' => [
                'span_id' => 7,
                'name'    => 'docx',
                'age'     => 25,
            ],
        ];

        $body = [
            'event_id' => 1545,
            'id'       => 1224,
            'amount'   => 7500,
            'user_id'  => 11166541,
        ];

        $message = new OutputMessage($body, $headers);

        $I->assertInstanceOf(AMQPMessage::class, $message->getPayload());
        $I->assertEquals($message->getHeader('message_id'), $body['event_id']);
    }
}