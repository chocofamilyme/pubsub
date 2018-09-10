<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Chocofamily\PubSub;

class Repeater
{
    const REDELIVERY_COUNT = 5;
    const CACHE_LIFETIME   = 1800;

    /** @var Cache */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Message $inputMessage
     *
     * @return bool
     */
    public function isRepeatable(Message $inputMessage): bool
    {
        $key = $this->getCacheKey($inputMessage);

        $redeliveryCount = $this->cache->get($key);

        if (empty($redeliveryCount)) {
            $redeliveryCount = 1;
        }

        $redeliveryCount++;

        $this->cache->set($key, $redeliveryCount, self::CACHE_LIFETIME);

        return ($redeliveryCount <= self::REDELIVERY_COUNT);
    }

    /**
     * @param Message $inputMessage
     *
     * @return string
     */
    public function getCacheKey(Message $inputMessage) : string
    {
        return 'ev_'.$inputMessage->getHeader('app_id').'_'.$inputMessage->getHeader('message_id');
    }
}