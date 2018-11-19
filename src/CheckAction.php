<?php

namespace alexeevdv\yii\queue\checker;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\console\ExitCode;
use yii\di\Instance;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class CheckAction
 * @package alexeevdv\yii\queue\checker
 */
class CheckAction extends Action
{
    const LAST_ALARM_TIMESTAMP_CACHE_KEY = self::class;

    /**
     * @var CacheInterface|array|string
     */
    public $cache = 'cache';

    /**
     * @var int Seconds before queue considered to be down
     */
    public $queueDownTimeout = 600;

    /**
     * @var int Seconds from last alarm
     */
    public $alarmInterval = 300;

    /**
     * @var JobInterface|array|string
     */
    public $job = [
        'class' => CheckJob::class,
    ];

    /**
     * @var Queue|array|string
     */
    public $queue = 'queue';

    /**
     * @var AlarmInterface|array|string
     */
    public $alarm = 'queueCheckerAlarm';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, CacheInterface::class);
        $this->job = Instance::ensure($this->job, JobInterface::class);
        $this->queue = Instance::ensure($this->queue, Queue::class);
        $this->alarm = Instance::ensure($this->alarm, AlarmInterface::class);
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->queue->push($this->job);

        $lastQueueCheck = $this->cache->getOrSet(CheckJob::LAST_QUEUE_CHECK_TIMESTAMP_CACHE_KEY, function () {
            return time();
        });
        if (time() - $lastQueueCheck < $this->queueDownTimeout) {
            return ExitCode::OK;
        }

        $lastAlarm = $this->cache->get(self::LAST_ALARM_TIMESTAMP_CACHE_KEY);
        if ($lastAlarm && time() - $lastAlarm < $this->alarmInterval) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->alarm->send(time() - $lastQueueCheck);
        $this->cache->set(self::LAST_ALARM_TIMESTAMP_CACHE_KEY, time());
        return ExitCode::UNSPECIFIED_ERROR;
    }
}
