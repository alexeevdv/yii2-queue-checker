<?php

namespace alexeevdv\yii\queue\checker;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\di\Instance;
use yii\queue\JobInterface;

/**
 * Class CheckJob
 * @package alexeevdv\yii\queue\checker
 */
class CheckJob extends BaseObject implements JobInterface
{
    const LAST_QUEUE_CHECK_TIMESTAMP_CACHE_KEY = self::class;

    /**
     * @var CacheInterface|array|string
     */
    public $cache = 'cache';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure(CacheInterface::class, $this->cache);
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $this->cache->set(self::LAST_QUEUE_CHECK_TIMESTAMP_CACHE_KEY, time());
    }
}
