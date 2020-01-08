<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;

interface Message
{
    public function getPayload();

    public function getHeader($key, $default = null);

    public function getHeaders();
}
