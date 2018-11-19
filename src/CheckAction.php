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
    /**
     * @var CacheInterface|array|string
     */
    public $cache = 'cache';

    /**
     * @var int
     */
    public $alarmDelay = 600;

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
        $this->cache = Instance::ensure(CacheInterface::class, $this->cache);
        $this->job = Instance::ensure(JobInterface::class, $this->job);
        $this->queue = Instance::ensure(Queue::class, $this->queue);
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->queue->push($this->job);

        $queueLastCheck = $this->cache->getOrSet(CheckJob::LAST_QUEUE_CHECK_TIMESTAMP_CACHE_KEY, function () {
            return time();
        });

        if (time() - $queueLastCheck < $this->alarmDelay) {
            return ExitCode::OK;
        }

        $this->alarm->send(time() - $queueLastCheck);
        $this->cache->set(CheckJob::LAST_QUEUE_CHECK_TIMESTAMP_CACHE_KEY, time());
        return ExitCode::UNSPECIFIED_ERROR;
    }
}
