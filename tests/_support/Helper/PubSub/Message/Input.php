<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub\Message;

use Chocofamily\PubSub\Message;

class Input implements Message
{
    private $headers = [];

    public $body = [];

    public function __construct(array $headers, array $body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getHeader(string $key = '')
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $this->headers;
    }

    public function getPayload()
    {
        return $this->body;
    }
}