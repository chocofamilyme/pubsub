<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

interface ProviderInterface
{
    public function connect();

    public function disconnect();

    public function publish();

    public function subscribe($callback, array $params = [], $consumerTag = '');

    public function setMessage(array $message, array $headers = []);

    public function setCurrentExchange($route, $exchangeName = '');

    public function addConfig(array $params = []);

    public function isConnected();
}
