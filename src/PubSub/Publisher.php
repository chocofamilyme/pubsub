<?php

/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;

use Chocofamily\PubSub\Provider\ProviderInterface;

class Publisher
{
    /** @var ProviderInterface */
    private $provider;

    /** @var array  */
    private $headers = [];

    public function __construct(ProviderInterface $provider, array $params = [])
    {
        $this->provider = $provider;
        $this->provider->addConfig($params);
    }

    /**
     * @param array  $message
     * @param string $route
     * @param string $exchange
     */
    public function send(array $message, $route, $exchange = '')
    {
        $this->provider->setMessage($message, $this->headers);
        $this->provider->setCurrentExchange($route, $exchange);
        $this->provider->publish();
    }

    /**
     * @param array $headers
     */
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

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->headers;
    }
}
