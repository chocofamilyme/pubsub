<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Unit;

use Chocofamily\PubSub\Subscriber;
use Helper\PubSub\DefaultProvider;
use Helper\PubSub\Message\Input as InputMessage;

class SubscriberCest
{
    public function tryToCallback(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantTo("Subscriber callback test");

        $headers = [
            'correlation_id' => '2679bb181c99887e0662ff8465c66a4f',
            'app_id'         => 'service.app.com',
            'message_id'     => 1599,
            'span_id'        => 0,
        ];

        $payload = [
            'event_id' => 1599,
            'user_id'  => 11166541,
            'name'     => 'docx',
        ];

        $message = new InputMessage($headers, $payload);

        $provider = new DefaultProvider();
        $callback = function (array $inHeaders, array $inPayload) use ($I, $headers, $payload) {
            $I->assertEquals($headers, $inHeaders);
            $I->assertEquals($payload, $inPayload);
        };

        $subscriber = new Subscriber($provider, 'order.created');
        $helper->invokeProperty($subscriber, 'callback', $callback);

        $subscriber->callback($message);
    }
}