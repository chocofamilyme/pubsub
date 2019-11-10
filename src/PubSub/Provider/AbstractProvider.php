<?php

namespace Chocofamily\PubSub\Provider;

use Chocofamily\PubSub\RepeaterInterface;

/**
 * Class AbstractProvider
 *
 * @package Chocofamily\PubSub\Provider
 * @author  Kulumbayev Kairzhan <kulumbayev.k@chocolife.kz>
 */
abstract class AbstractProvider implements Adapter
{
    /**
     * @var array $instance
     */
    private static $instance;

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var RepeaterInterface $repeater
     */
    protected $repeater;

    /**
     * AbstractProvider constructor.
     *
     * @param array             $config
     * @param RepeaterInterface $repeater
     */
    final public function __construct(array $config, RepeaterInterface $repeater)
    {
        $this->config   = $config;
        $this->repeater = $repeater;

        $this->connect();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @param array             $config
     * @param RepeaterInterface $repeater
     *
     * @return Adapter
     */
    final public static function getInstance(array $config, RepeaterInterface $repeater): Adapter
    {
        $class = static::class;
        if (!isset(self::$instance[$class])) {
            $instance               = new $class($config, $repeater);
            self::$instance[$class] = $instance;
        }

        return self::$instance[$class];
    }
}
