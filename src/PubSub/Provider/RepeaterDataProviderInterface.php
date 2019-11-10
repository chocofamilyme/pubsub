<?php
/**
 * Created by IntelliJ IDEA.
 * User: User
 * Date: 11/9/2019
 * Time: 11:54 AM
 */

namespace Chocofamily\PubSub\Provider;


use Chocofamily\PubSub\Models\ModelInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

interface RepeaterDataProviderInterface
{
    public function __construct(Adapter $adapter, \DateTime $startDate, int $limit);

    public function getSource(): Adapter;

    public function getLimit(): int;

    /**
     * @return ModelInterface[]|ResultsetInterface
     */
    public function getData(): ResultsetInterface;
}
