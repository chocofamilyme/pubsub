<?php

declare(strict_types=1);

namespace Chocofamily\PubSub;

use PhpAmqpLib\Message\AMQPMessage;

interface InputMessage extends Message
{
    public function getMessage(): AMQPMessage;
}
