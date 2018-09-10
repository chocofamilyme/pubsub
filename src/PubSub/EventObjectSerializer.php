<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub;

class EventObjectSerializer implements SerializerInterface
{
    const FIELDS = [];

    private $customData = [];

    /**
     * AbstractSerializer constructor.
     *
     * @param array $customData
     */
    public function __construct(array $customData = [])
    {
        $this->customData = $customData;
    }

    /**
     * @param \Phalcon\Mvc\Model $model
     *
     * @return mixed
     */
    public function getAttributes($model)
    {
        return array_merge($this->customData, $model->toArray(static::FIELDS));
    }
}
