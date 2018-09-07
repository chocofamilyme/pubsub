<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;


interface Cache
{
    public function get(string $key);

    public function set(string $key, $value, int $lifetime);
}