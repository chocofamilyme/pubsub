<?php
/**
 * @package Chocolife.me
 * @author  Kulumbayev Kairzhan <kulumbayev.k@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

use Chocofamily\PubSub\RepeaterInterface;

abstract class AbstractProvider implements Adapter
{
    /**
     * @var array $instance
     */
    private static $instance;

    /**
     * @var bool $isConnected
     */
    protected $isConnected = false;

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
     * @throws \ReflectionException
     */
    final public static function getInstance(array $config, RepeaterInterface $repeater): Adapter
    {
        $class = static::class;
        if (!isset(self::$instance[$class])) {
            $reflectionClass = new \ReflectionClass($class);

            self::$instance[$class] = $reflectionClass->newInstanceArgs([$config, $repeater]);
        }

        return self::$instance[$class];
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
}
