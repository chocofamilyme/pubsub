<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once 'functions.php';

use Chocofamily\PubSub\Subscriber;

$params = [
    'exchange_type' => 'fanout',
    'queue_name' => 'dead-messages',
];

$taskName = 'dead_task';

$provider = getProvider();

$subscriber = new Subscriber($provider, 'dlx', $params, $taskName);

$subscriber->subscribe(function ($headers, $body) {
    echo print_r($headers, 1). PHP_EOL;
    echo print_r($body, 1). PHP_EOL;
    echo "\n";
});
