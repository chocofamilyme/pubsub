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

    /** @var array */
    private $routes = [];

    public function __construct($name, array $routes)
    {
        $this->name   = $name;
        $this->routes = $routes;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
