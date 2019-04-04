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

    public function __construct(Adapter $provider, array $params = [])
    {
        $this->provider = $provider;
        $this->provider->addConfig($params);

        $this->headers['correlation_id']                 = CorrelationId::getInstance()->getCorrelationId();
        $this->headers['application_headers']['span_id'] = CorrelationId::getInstance()->getNextSpanId();
    }

    /**
     * @param array  $message
     * @param string $route
     * @param string $exchange
     */
    public function send(array $message, string $route, string $exchange = '')
    {
        $this->provider->setMessage($message, $this->headers);

        $this->provider->setCurrentExchange($route, $exchange);

        $this->provider->publish();
    }

    public function setHeader(array $headers)
    {
        if (isset($headers['application_headers'])) {
            $this->headers['application_headers'] = array_merge(
                $headers['application_headers'],
                $this->headers['application_headers']
            );
        }

        $this->headers = array_merge($headers, $this->headers);
    }

    public function getHeader(): array
    {
        return $this->headers;
    }
}
