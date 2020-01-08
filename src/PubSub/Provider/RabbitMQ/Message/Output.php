<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider\RabbitMQ\Message;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Output implements \Chocofamily\PubSub\Message
{
    /** @var AMQPMessage */
    private $payload;

    /** @var array */
    private $body;

    /** @var array */
    private $headers = ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];

    public function __construct(array $body, array $headers = [])
    {
        $this->setBody($body);

        $this->headers['message_id'] = $body['event_id'];

        $this->headers = array_merge($this->headers, $headers);

        $table = new AMQPTable($this->headers['application_headers']);
        unset($this->headers['application_headers']);

        $this->payload = new AMQPMessage(\json_encode($this->getBody()), $this->headers);
        $this->payload->set('application_headers', $table);
    }

    /**
     * @return mixed
     */
    private function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    private function setBody(array $body)
    {
        $this->body = $body;
    }

    public function getHeader($key, $default = null)
    {
        return $this->headers[$key] ?: $default;
    }

    /**
     * @return AMQPMessage
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
