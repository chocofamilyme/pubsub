<?php
require_once __DIR__.'/vendor/autoload.php';
require_once 'functions.php';

use Chocofamily\PubSub\Publisher;

$publisher = new Publisher(getProvider());

$payload = [
    'event_id' => 11995,
    'name' => 'docx',
    'age' => 25
];

$routeKey = 'book.reserved';

$publisher->send($payload, $routeKey);

echo "OK\n";
