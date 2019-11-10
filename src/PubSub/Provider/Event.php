<?php

namespace Chocofamily\PubSub\Provider;

use \Chocofamily\PubSub\Services\Event as EventRepository;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Class Event
 *
 * @package Chocofamily\PubSub\Provider
 * @author  Kulumbayev Kairzhan <kulumbayev.k@chocolife.kz>
 */
class Event implements RepeaterDataProviderInterface
{
    /** @var Adapter */
    private $queueProvider;

    /** @var \DateTime */
    private $startDate;

    /** @var int */
    private $limit;

    public function __construct(Adapter $adapter, \DateTime $startDate, int $limit = 200)
    {
        $this->queueProvider = $adapter;
        $this->startDate     = $startDate;
        $this->limit         = $limit;
    }

    public function getSource(): Adapter
    {
        return $this->queueProvider;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getData(): ResultsetInterface
    {
        return EventRepository::getFailMessage($this->startDate, $this->limit);
    }

}
