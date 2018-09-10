<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Helper\PubSub\Models;

/**
 * Class Event
 *
 * Хранит собития
 *
 * @package Chocofamily\Models
 */
class Event
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
