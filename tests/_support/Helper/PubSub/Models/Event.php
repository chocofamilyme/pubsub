<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Helper\PubSub\Models;

use Chocofamily\PubSub\Models\ModelInterface;

/**
 * Class Event
 *
 * Хранит собития
 *
 * @package Chocofamily\Models
 */
class Event implements ModelInterface
{

    const NEW  = 0;
    const SENT = 1;

    static public $id = 0;

    /**
     * @var integer
     */
    public $type;

    /**
     * @var array
     */
    public $payload;

    /**
     * @var integer
     */
    public $status;

    /**
     * @var integer
     */
    public $model_id;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * @var string
     */
    public $exchange;

    /**
     * @var string
     */
    public $routing_key;

    public function __construct()
    {
        self::$id++;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return self::$id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getExchange(): string
    {
        return $this->exchange;
    }

    /**
     * @return string
     */
    public function getRoutingKey(): string
    {
        return $this->routing_key;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getModelId(): int
    {
        return $this->model_id;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function save($data = null, $whiteList = null)
    {
    }

    public function update($data = null, $whiteList = null)
    {
    }
}
