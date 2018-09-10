<?php

/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;

use Chocofamily\Http\CorrelationId;
use Chocofamily\PubSub\Provider\Adapter;

class Publisher
{
    /** @var Adapter */
    private $provider;

    private $headers = [];

    public function __construct(Adapter $provider)
    {
        $this->provider = $provider;

        $this->headers['correlation_id']                 = CorrelationId::getInstance()->getCorrelationId();
        $this->headers['application_headers']['span_id'] = CorrelationId::getInstance()->getNextSpanId();
    }

    /**
     * @param array  $message
     * @param string $to
     */
    public function send(array $message, string $to)
    {
        $this->provider->setMessage($message, $this->headers);

        $this->provider->setCurrentExchange($to);

        $this->provider->publish();
    }

    public function setHeader(array $headers)
    {
        $this->headers = array_merge($headers, $this->headers);
    }

    public function getHeader(): array
    {
        return $this->headers;
    }
}
