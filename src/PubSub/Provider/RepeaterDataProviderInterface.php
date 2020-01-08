<?php
/**
 * @package Chocolife.me
 * @author  Kulumbayev Kairzhan <kulumbayev.k@chocolife.kz>
 */

namespace Chocofamily\PubSub\Provider;

interface RepeaterDataProviderInterface
{
    /**
     * @return ProviderInterface
     */
    public function getSource();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return mixed
     */
    public function getData();
}
