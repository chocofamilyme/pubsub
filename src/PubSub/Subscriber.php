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
    private $routes;

    /** @var string */
    private $exchangeName;

    /** @var array */
    private $params;

    /** @var string */
    private $consumerTag;

    /** @var callable */
    private $callback;

    /**
     * Subscriber constructor.
     *
     * @param Adapter      $provider
     * @param string|array $routes
     * @param array        $params
     * @param string       $consumerTag
     * @param string       $exchangeName
     */
    public function __construct(
        Adapter $provider,
        $routes,
        array $params = [],
        string $consumerTag = '',
        string $exchangeName = ''
    ) {
        $this->provider     = $provider;
        $this->routes       = $routes;
        $this->params       = $params;
        $this->consumerTag  = $consumerTag;
        $this->exchangeName = $exchangeName;
    }


    public function subscribe($callback)
    {
        $this->provider->setCurrentExchange($this->routes, $this->exchangeName);

        $this->callback = $callback;

        $this->provider->subscribe([$this, 'callback'], $this->params, $this->consumerTag);
    }


    public function callback(Message $message)
    {
        $id = $message->getHeader('correlation_id');
        if (empty($id)) {
            $id = (new \Phalcon\Security\Random())->uuid();
        }

        $spanId = $message->getHeader('span_id', 0);
        CorrelationId::getInstance()->setCorrelation($id, $spanId);

        call_user_func($this->callback, $message->getHeaders(), $message->getPayload());
    }
}
