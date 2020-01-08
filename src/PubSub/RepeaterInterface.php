<?php
/**
 * @package Chocolife.me
 * @author  docxplusgmoon <nurgabylov.d@chocolife.kz>
 */

namespace Chocofamily\PubSub;

interface RepeaterInterface
{
    /**
     * @param Message $inputMessage
     *
     * @return bool
     */
    public function isRepeatable(Message $inputMessage);
}
