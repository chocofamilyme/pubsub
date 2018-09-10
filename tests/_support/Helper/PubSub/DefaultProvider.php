<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub;

use Chocofamily\PubSub\Provider\Adapter;

class DefaultProvider implements Adapter
{
    public function connect()
    {
    }

    public function disconnect()
    {
    }

    public function publish()
    {
    }

    public function subscribe($callback, array $params = [], string $consumerTag = '')
    {
    }

    public function setMessage(array $message, array $headers = [])
    {
    }

    public function setCurrentExchange(string $queue)
    {
    }
}