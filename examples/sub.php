<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once 'functions.php';

use Chocofamily\PubSub\Subscriber;
use Chocofamily\PubSub\Exceptions\RetryException;

$params = [
    'queue_name' => 'book',
];

$taskName = 'your_task_name';

$subscriber = new Subscriber(getProvider(), 'book.reserved', $params, $taskName);

$subscriber->subscribe(function ($headers, $body) {
    echo print_r($headers, 1). PHP_EOL;
    echo print_r($body, 1). PHP_EOL;
    # throw new RetryException('go to dead queue');
});
