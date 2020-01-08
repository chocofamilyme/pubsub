<?php

use Chocofamily\PubSub\Provider\RabbitMQ;
use Chocofamily\PubSub\Repeater;

class MemcachedCache extends \Memcached implements \Chocofamily\PubSub\CacheInterface {}

function getCacheInstance()
{
    $cacheConfig = [
        'servers' => [
            [
                'host'   => 'localhost',
                'port'   => 11211,
                'weight' => 100,
            ],
        ],
        'prefix'   => 'restapi_cache_',
        'cacheDir' => '../storage/cache',
    ];

    $cache = new MemcachedCache();
    if (empty($cache->getServerList())) {
        $cache->addServer('localhost', 11211);
    }

    return $cache;
}

function getProvider()
{
    $config = [
        'adapter'  => 'RabbitMQ',
        'host'     => 'localhost',
        'port'     => 5674,
        'user'     => 'guest',
        'password' => 'guest',
        'app_id'   => 'service.example.com',
    ];

    $cache = getCacheInstance();

    $repeater = new Repeater($cache);
    return RabbitMQ::getInstance($config, $repeater);
}
