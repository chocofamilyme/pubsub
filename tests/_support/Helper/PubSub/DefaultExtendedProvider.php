<?php

namespace Helper\PubSub;

use Chocofamily\PubSub\Provider\AbstractProvider;

/**
 * Class DefaultExtendedProvider
 *
 * @package Helper\PubSub
 */
class DefaultExtendedProvider extends AbstractProvider
{
    public $queue    = [];
    public $exchange = '';
    public $message  = '';
    public $headers  = [];

    public function connect()
    {
    }

    public function disconnect()
    {
    }

    public function publish()
    {
        $data = [
            'message' => $this->message,
            'headers' => $this->headers,
        ];

        $this->queue[$this->exchange] = $data;
    }

    public function subscribe($callback, array $params = [], string $consumerTag = '')
    {
    }

    public function setMessage(array $message, array $headers = [])
    {
        $this->message = \json_encode($message, JSON_UNESCAPED_UNICODE);
    }

    public function setCurrentExchange($route, string $exchangeName = '')
    {
        $this->exchange = $route;
    }

    public function addConfig(array $params = [])
    {
    }

    public function isConnected(): bool
    {
        return true;
    }

}
