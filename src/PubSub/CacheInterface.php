<?php
/**
 * Created by Chocolife.me.
 * User: User
 * Date: 08.01.2020
 * Time: 14:55
 */

namespace Chocofamily\PubSub;


interface CacheInterface
{
    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $data
     * @param $lifetime
     *
     * @return mixed
     */
    public function set($key, $data, $lifetime);
}
