<?php
/**
 * Created by IntelliJ IDEA.
 * User: User
 * Date: 11/11/2019
 * Time: 1:30 PM
 */

namespace Chocofamily\PubSub\Models;


interface EventInterface extends ModelInterface
{
    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return string
     */
    public function getExchange(): string;

    /**
     * @return string
     */
    public function getRoutingKey(): string;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @return mixed
     */
    public function getModelId();

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param mixed $model_id
     */
    public function setModelId($model_id);

    /**
     * @return string
     */
    public function getModelType(): string;
}
