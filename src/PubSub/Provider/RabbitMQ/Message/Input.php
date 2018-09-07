<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider\RabbitMQ\Message;

use PhpAmqpLib\Message\AMQPMessage;
use Chocofamily\PubSub\Message;

class Input implements Message
{
    private $headers;

    private $body;


    public function __construct(AMQPMessage $message)
    {
        $this->headers = array_merge(
            $message->get_properties(),
            $message->get('application_headers')->getNativeData()
        );

        unset($this->headers['application_headers']);

        $this->body = \json_decode($message->body, true);
    }

    public function getPayload(): array
    {
        return $this->body;
    }


    public function getHeader(string $key = '')
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $this->headers;
    }
}