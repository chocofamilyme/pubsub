<?php
/**
 * @package Chocolife.me
 * @author  Muratbekov Aibek <muratbekov.a@chocolife.kz>
 */

namespace Chocofamily\PubSub\Services;

use Chocofamily\PubSub\Exceptions\ModelException;
use Chocofamily\PubSub\Models\Event;
use Phalcon\Di;

/**
 * Class EventCleaner
 *
 * @package Chocofamily\PubSub\Services
 */
class EventCleaner
{
    /**
     * @var \DateTime|null
     */
    protected $dateTime;
    /**
     * @var mixed
     */
    protected $manager;

    /**
     * EventCleaner constructor.
     *
     * @param \DateTime|null $dateTime
     *
     * @throws \Exception
     */
    public function __construct(\DateTime $dateTime = null)
    {
        if (is_null($dateTime)) {
            $dateTime = new \DateTime();
            $dateTime->modify('-1 month');
        }
        $this->dateTime = $dateTime;
        $this->manager  = Di::getDefault()->get('modelsManager');
    }

    /**
     * @throws ModelException
     */
    public function clean()
    {
        $phql = "DELETE FROM Chocofamily\PubSub\Models\Event WHERE status = :status: AND created_at < :start_at:";

        $result = $this->manager->executeQuery($phql, [
            'status'   => Event::SENT,
            'start_at' => $this->dateTime->format('Y-m-d H:i:s'),
        ]);

        if ($result->success() === false) {
            $messages = $result->getMessages();
            throw new ModelException("Не удалось очистить событие ".$messages[0]);
        }

        return $result;
    }
}
