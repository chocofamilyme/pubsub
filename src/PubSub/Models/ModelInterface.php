<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\PubSub\Models;

/**
 * Interface ModelInterface
 *
 * @package Chocofamily\PubSub\Models
 */
interface ModelInterface extends \Phalcon\Mvc\ModelInterface
{
    public function getId();
}
