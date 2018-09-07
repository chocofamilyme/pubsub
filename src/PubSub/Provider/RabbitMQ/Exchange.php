<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider\RabbitMQ;

/**
 * Class Exchange
 *
 * Определять маршрут сообщения
 *
 * @package Chocofamily\PubSub\Provider\RabbitMQ
 */
class Exchange
{

    /** @var string */
    private $name;

    /** @var string */
    private $route;

    public function __construct(string $name, string $route)
    {
        $this->name  = $name;
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRoute(): string
    {
        return $this->route;
    }
}
