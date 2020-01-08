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

    public function getHeader($key, $default = null)
    {
        return $this->headers[$key] ?: $default;
    }

    public function getPayload()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
