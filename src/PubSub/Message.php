<?php

declare(strict_types=1);

namespace Chocofamily\PubSub;

use PhpAmqpLib\Message\AMQPMessage;

interface Message
{
    public function getPayload();

    public function getHeader(string $key, $default = null);

    public function getHeaders();

    public function getMessage(): AMQPMessage;
}
