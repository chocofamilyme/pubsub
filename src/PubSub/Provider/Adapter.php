<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

interface Adapter
{
    public function connect();

    public function disconnect();

    public function publish();

    public function subscribe($callback, array $params = [], string $consumerTag = '');

    public function setMessage(array $message, array $headers = []);

    public function setCurrentExchange(string $queue);

    public function addConfig(array $params = []);
}
