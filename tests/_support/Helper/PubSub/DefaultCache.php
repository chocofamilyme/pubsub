<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub;

use Chocofamily\PubSub\Cache;

class DefaultCache implements Cache
{
    /** @var array */
    private $data = [];

    public function get(string $key)
    {
        if (isset($this->data[$key]) && $this->data[$key]['expire_time'] > time()) {
            return $this->data[$key]['value'];
        } else {
            unset($this->data[$key]);
            return null;
        }
    }

    /**
     * @param string $key
     * @param        $value
     * @param int    $lifetime - seconds
     */
    public function set(string $key, $value, int $lifetime)
    {
        $this->data[$key] = [
            'expire_time' => time() + $lifetime,
            'value' => $value
        ];
    }
}
