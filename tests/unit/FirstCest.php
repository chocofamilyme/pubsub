<?php

namespace Unit;

use Chocofamily\Http\CorrelationId;

class FirstCest
{
    public function _before(\UnitTester $I)
    {
    }

    public function _after(\UnitTester $I)
    {
    }

    // tests
    public function tryToTest(\UnitTester $I)
    {
        $I->wantTo("first test");
        $r = CorrelationId::getInstance()->getCurrentQueryParams();
        print_r($r);

    }
}
