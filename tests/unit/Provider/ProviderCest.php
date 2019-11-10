<?php

namespace Unit\Provider;

use Chocofamily\PubSub\Provider\AbstractProvider;
use Chocofamily\PubSub\Repeater;
use Helper\PubSub\DefaultExtendedProvider;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Cache\Frontend\Data;

class ProviderCest
{
    public function tryToCreateInstance(\UnitTester $I)
    {
        $cacheConfig = [
            'servers'  => [
                [
                    'host'   => 'localhost',
                    'port'   => 11211,
                    'weight' => 100,
                ],
            ],
            'prefix'   => 'restapi_cache_',
            'cacheDir' => '../storage/cache',
        ];
        $cache       = new Libmemcached(
            new Data(['lifetime' => 86400]), $cacheConfig
        );

        $testProvider = DefaultExtendedProvider::getInstance([], new Repeater($cache));

        $I->assertEquals(get_class($testProvider), DefaultExtendedProvider::class);

        $class    = new \ReflectionClass(AbstractProvider::class);
        $property = $class->getProperty('instance');
        $property->setAccessible(true);

        $multiton = $property->getValue($testProvider);

        $I->assertIsArray($multiton, 'Instance is not array');

        $I->assertEquals(count($multiton), 1);

        $I->assertArrayHasKey(DefaultExtendedProvider::class, $multiton);

        $I->assertEquals($testProvider, $multiton[DefaultExtendedProvider::class]);
    }
}
