<?php
/**
 * @package Chocolife.me
 * @author  Muratbekov Aibek <muratbekov.a@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;


use DateTime;

/**
 * Class EventRepeater
 *
 * @package Chocofamily\PubSub\Services
 */
class EventRepeater
{
    /**
     * @DateTime
     */
    protected $startDate;

    /**
     * @var
     */
    protected $source;

    /**
     * @var int
     */
    protected $limit;

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
     * @param $startDate
     * @param $source
     * @param $limit
     */
    public function __construct($source, DateTime $startDate, int $limit = 200)
    {
        $this->source    = $source;
        $this->startDate = $startDate;
        $this->limit     = $limit;
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
    public function reTry()
    {
        do {
            $events = Event::getFailMessage($this->startDate, $this->limit);
            foreach ($events as $event) {
                $eventPublish = new EventPublish($this->source, $event);
                $eventPublish->publish(
                    $this->chechRouteKey($event->getRoutingKey()),
                    $this->checkExchange($event->getExchange())
                );
            }
        } while (count($events) >= $this->limit);
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
