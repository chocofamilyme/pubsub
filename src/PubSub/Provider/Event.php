<?php
/**
 * @package Chocolife.me
 * @author  Kulumbayev Kairzhan <kulumbayev.k@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

use \Chocofamily\PubSub\Services\Event as EventRepository;

class Event implements RepeaterDataProviderInterface
{
    /** @var ProviderInterface */
    private $queueProvider;

    /** @var \DateTime */
    private $startDate;

    /** @var int */
    private $limit;

    public function __construct(ProviderInterface $adapter, \DateTime $startDate, $limit = 200)
    {
        $this->queueProvider = $adapter;
        $this->startDate     = $startDate;
        $this->limit         = $limit;
    }

    public function getSource()
    {
        return $this->queueProvider;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getData()
    {
        return EventRepository::getFailMessage($this->startDate, $this->limit);
    }
}
