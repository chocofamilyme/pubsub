<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Unit;

use Helper\PubSub\DefaultCache;
use Chocofamily\PubSub\Repeater;
use Helper\PubSub\Message\Input as InputMessage;

class RepeaterCest
{
    public function tryToIsRepeatable(\UnitTester $I)
    {
        $I->wantTo('Repeater test');
        $cache = new DefaultCache();
        $repeater = new Repeater($cache);
        $message = new InputMessage(['app_id' => 45, 'message_id' => 885], []);

        $I->assertTrue($repeater->isRepeatable($message));
        $cache->save($repeater->getCacheKey($message), 10, 5000);
        $I->assertFalse($repeater->isRepeatable($message));
    }
}
