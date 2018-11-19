<?php

namespace tests\unit;

use alexeevdv\yii\queue\checker\CheckJob;
use Codeception\Stub\Expected;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\queue\file\Queue;

class CheckJobTest extends \Codeception\Test\Unit
{
    public function testCacheComponentEnsuredInvalid()
    {
        $this->expectException(InvalidConfigException::class);
        new CheckJob(['cache' => 'invalid']);
    }

    public function testCacheComponentEnsuredValid()
    {
        new CheckJob(['cache' => $this->makeEmpty(CacheInterface::class)]);
    }

    public function testCacheTimestampIsUpdated()
    {
        $job = new CheckJob([
            'cache' => $this->makeEmpty(CacheInterface::class, [
                'set' => Expected::once(),
            ])
        ]);
        $job->execute($this->makeEmpty(Queue::class));
    }
}
