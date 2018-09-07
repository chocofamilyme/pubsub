<?php

/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;

use Chocofamily\Http\CorrelationId;
use Chocofamily\PubSub\Provider\Adapter;

class Subscriber
{
    /** @var Adapter */
    private $provider;

    /** @var string */
    private $route;

    /** @var array */
    private $params;

    /** @var string */
    private $consumerTag;

    /** @var callable */
    private $callback;

    /**
     * Subscriber constructor.
     *
     * @param Adapter $provider
     * @param string  $route
     * @param array   $params
     * @param string  $consumerTag
     */
    public function __construct(Adapter $provider, string $route, array $params = [], string $consumerTag = '')
    {
        $this->provider    = $provider;
        $this->route       = $route;
        $this->params      = $params;
        $this->consumerTag = $consumerTag;
    }


    public function subscribe($callback)
    {
        $this->provider->setCurrentExchange($this->route);

        $this->callback = $callback;

        $this->provider->subscribe([$this, 'callback'], $this->params, $this->consumerTag);
    }


    public function callback(Message $message)
    {
        $id = $message->getHeader('correlation_id');
        $spanId = $message->getHeader('span_id');
        CorrelationId::getInstance()->setCorrelation($id, $spanId);

        call_user_func($this->callback, $message->getHeader(), $message->getPayload());
    }
}
