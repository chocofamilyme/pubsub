<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Helper\PubSub;

use \Phalcon\Cache\BackendInterface as Cache;

class DefaultCache implements Cache
{
    /** @var array */
    private $data = [];

    public function get($key, $lifetime = null)
    {
        if (isset($this->data[$key]) && $this->data[$key]['expire_time'] > time()) {
            return $this->data[$key]['value'];
        } else {
            unset($this->data[$key]);

            return null;
        }
    }

    /**
     * @param null $key
     * @param null $content
     * @param int  $lifetime - seconds
     * @param null $stopBuffer
     */
    public function save($key = null, $content = null, $lifetime = null, $stopBuffer = null)
    {
        $this->data[$key] = [
            'expire_time' => time() + $lifetime,
            'value'       => $content,
        ];
    }

    public function start($keyName, $lifetime = null)
    {
        // TODO: Implement start() method.
    }

    public function stop($stopBuffer = null)
    {
        // TODO: Implement stop() method.
    }

    public function getFrontend()
    {
        // TODO: Implement getFrontend() method.
    }

    public function getOptions()
    {
        // TODO: Implement getOptions() method.
    }

    public function isFresh()
    {
        // TODO: Implement isFresh() method.
    }

    public function isStarted()
    {
        // TODO: Implement isStarted() method.
    }

    public function setLastKey($lastKey)
    {
        // TODO: Implement setLastKey() method.
    }

    public function getLastKey()
    {
        // TODO: Implement getLastKey() method.
    }

    public function delete($keyName)
    {
        // TODO: Implement delete() method.
    }

    public function queryKeys($prefix = null)
    {
        // TODO: Implement queryKeys() method.
    }

    public function exists($keyName = null, $lifetime = null)
    {
        // TODO: Implement exists() method.
    }
}
