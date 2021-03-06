<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Models;

/**
 * Class Event
 *
 * Хранит собития
 *
 * @package Chocofamily\Models
 */
class Event extends \Phalcon\Mvc\Model implements EventInterface
{
    const NEW  = 0;
    const SENT = 1;

    /**
     * @var mixed
     */
    public $id;

    /**
     * @var integer
     */
    public $type;

    /**
     * @var array
     */
    public $payload = [];

    /**
     * @var integer
     */
    public $status;

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

    /**
     * @var mixed
     */
    public $model_id = null;

    /**
     * @var string
     */
    public $model_type = null;

    /**
     * Returns table name mapped in the model.
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getSource()
    {
        return 'events';
    }

    /**
     * Initialize method for model.
     *
     * @codeCoverageIgnore
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->useDynamicUpdate(true);

        $this->skipAttributesOnCreate(
            [
                'updated_at',
                'created_at',
            ]
        );

        $this->skipAttributesOnUpdate(
            [
                'created_at',
            ]
        );
    }

    public function beforeSave()
    {
        $this->encodePayload();
    }

    public function afterFetch()
    {
        $this->decodePayload();
    }

    public function afterSave()
    {
        $this->decodePayload();
    }

    /**
     * @codeCoverageIgnore
     */
    private function decodePayload()
    {
        if (is_string($this->payload)) {
            $this->payload = \json_decode($this->payload, true);
            if (is_null($this->payload)) {
                $this->payload = [];
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function encodePayload()
    {
        if (is_array($this->payload)) {
            $this->payload = \json_encode($this->payload, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return mixed
     */
    public function getModelId()
    {
        return $this->model_id;
    }

    /**
     * @param mixed $model_id
     */
    public function setModelId($model_id)
    {
        $this->model_id = $model_id;
    }

    /**
     * @return string
     */
    public function getModelType(): string
    {
        return $this->model_type;
    }

    /**
     * @param string $model_type
     */
    public function setModelType(string $model_type)
    {
        if ($this->hasAttribute('model_type')) {
            $this->model_type = $model_type;
        }
    }

    protected function hasAttribute($attribute)
    {
        return $this->getModelsMetaData()->hasAttribute($this, $attribute);
    }
}
