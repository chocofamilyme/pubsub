<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub;

use Chocofamily\PubSub\Provider\Adapter;

/**
 * Class DefaultProvider
 *
 * @package Helper\PubSub
 */
class DefaultProvider implements Adapter
{
    public $queue    = [];
    public $exchange = '';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var array
     */
    public $headers = [];

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

    public function setCurrentExchange(string $queue)
    {
        $this->exchange = $queue;
    }

    public function addConfig(array $params = [])
    {
    }
}
