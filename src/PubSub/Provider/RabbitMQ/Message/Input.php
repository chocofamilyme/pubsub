<?php

declare(strict_types=1);

namespace Chocofamily\PubSub\Provider\RabbitMQ\Message;

use PhpAmqpLib\Message\AMQPMessage;
use Chocofamily\PubSub\InputMessage;

class Input implements InputMessage
{
    private $headers;

    private $body;

    /** @var AMQPMessage */
    private $message;

    public function __construct(AMQPMessage $message)
    {
        $this->message = $message;
    }

    public function getPayload(): array
    {
        if (null === $this->body) {
            $this->body = \json_decode($this->message->body, true);
        }

        return $this->body;
    }

    public function getHeader(string $key, $default = null)
    {
        return $this->getHeaders()[$key] ?? $default;
    }

    public function getHeaders(): array
    {
        if (null === $this->headers) {
            $this->headers = array_merge(
                $this->message->get_properties(),
                $this->message->get('application_headers')->getNativeData()
            );

            $this->headers['routing_key'] = $this->message->getRoutingKey();
            unset($this->headers['application_headers']);
        }

        return $this->headers;
    }

    /**
     * @return AMQPMessage
     */
    public function getMessage(): AMQPMessage
    {
        return $this->message;
    }
}
