<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Unit;

use Chocofamily\PubSub\Subscriber;
use Codeception\Stub;
use Helper\PubSub\DefaultProvider;

class SubscriberCest
{
    public function _before(\UnitTester $I)
    {
    }

    public function _after(\UnitTester $I)
    {
    }

    // tests
    public function tryToCallback(\UnitTester $I, \Helper\Unit $helper)
    {
        $I->wantTo("Subscriber callback test");

        $payload = [
            'event_id' => 1599,
            'user_id'  => 11166541,
            'name'     => 'docx',
        ];

        $headers = [
            'correlation_id' => '2679bb181c99887e0662ff8465c66a4f',
            'app_id' => 'service.app.com',
            'message_id' => 1599,
        ];

        $provider = new DefaultProvider();
        $subscriber = Stub::make(Subscriber::class, [$provider, 'order.created']);
        $helper->invokeProperty($subscriber, 'callback');

        $subscriber->callback = function (array $body, array $headers) use ($I) {
            echo get_class($I);
            echo print_r($headers) . PHP_EOL . print_r($body) . "\n";
        };

        $subscriber->callback($message);
    }
}