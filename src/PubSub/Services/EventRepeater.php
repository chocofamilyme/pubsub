<?php
/**
 * @package Chocolife.me
 * @author  Muratbekov Aibek <muratbekov.a@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;


use Chocofamily\PubSub\Models\ModelInterface;
use Chocofamily\PubSub\Provider\RepeaterDataProviderInterface;
use DateTime;

/**
 * Class EventRepeater
 *
 * @package Chocofamily\PubSub\Services
 */
class EventRepeater
{
    /** @var RepeaterDataProviderInterface */
    protected $provider;

    /**
     * @var string
     */
    protected $defaultExchange;

    /**
     * @string
     */
    protected $defaultRoute;

    /**
     * EventRepeater constructor.
     *
     * @param RepeaterDataProviderInterface $dataProvider
     */
    public function __construct(RepeaterDataProviderInterface $dataProvider)
    {
        $this->provider = $dataProvider;
    }

    /**
     * @return string
     */
    public function getDefaultExchange()
    {
        return $this->defaultExchange;
    }

    /**
     * @param string $defaultExchange
     */
    public function setDefaultExchange(string $defaultExchange)
    {
        $this->defaultExchange = $defaultExchange;
    }

    /**
     * @return string
     */
    public function getDefaultRoute()
    {
        return $this->defaultRoute;
    }

    /**
     * @param string $defaultRoute
     */
    public function setDefaultRoute(string $defaultRoute)
    {
        $this->defaultRoute = $defaultRoute;
    }

    /**
     * @throws \ErrorException
     */
    public function retry()
    {
        do {
            $provider = $this->provider;
            $events   = $provider->getData();

            /** @var ModelInterface $event */
            foreach ($events as $event) {
                $eventPublish = new EventPublish($provider->getSource(), $event);
                $eventPublish->publish(
                    $this->checkRouteKey($event->getRoutingKey()), $this->checkExchange($event->getExchange())
                );
            }
        } while (count($events) >= $provider->getLimit());
    }

    /**
     * @param string $exchange
     *
     * @return string
     */
    private function checkExchange(string $exchange): string
    {
        return empty($exchange) ? $this->defaultExchange : $exchange;
    }

    /**
     * @param string $routeKey
     *
     * @return string
     */
    private function checkRouteKey(string $routeKey): string
    {
        return empty($routeKey) ? $this->defaultRoute : $routeKey;
    }
}
