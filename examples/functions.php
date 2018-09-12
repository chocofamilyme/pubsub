<?php
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Backend\Libmemcached;
use Chocofamily\PubSub\Provider\RabbitMQ;
use Chocofamily\PubSub\Repeater;

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
    return new Libmemcached(
        new Data(['lifetime' => 86400]),
        $cacheConfig
    );
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