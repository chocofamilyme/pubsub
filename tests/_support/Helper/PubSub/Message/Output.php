<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub\Message;

use Chocofamily\PubSub\Message;

class Output implements Message
{
    private $headers = [];

    public $body = [];

    public function getHeader(string $key = '')
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $this->headers;
    }

    public function getPayload()
    {
        return $this->body;
    }
}