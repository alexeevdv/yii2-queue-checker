<?php

namespace tests\unit;

use alexeevdv\yii\queue\checker\AlarmInterface;
use alexeevdv\yii\queue\checker\CheckAction;
use Codeception\Stub\Expected;
use yii\base\Controller;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\console\ExitCode;
use yii\queue\file\Queue;
use yii\queue\JobInterface;

class CheckActionTest extends \Codeception\Test\Unit
{
    public function testCacheEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new CheckAction('id', $this->makeEmpty(Controller::class));
    }

    public function testJobEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new CheckAction('id', $this->makeEmpty(Controller::class), [
            'cache' => $this->makeEmpty(CacheInterface::class),
        ]);
    }

    public function testQueueEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new CheckAction('id', $this->makeEmpty(Controller::class), [
            'cache' => $this->makeEmpty(CacheInterface::class),
            'job' => $this->makeEmpty(JobInterface::class),
        ]);
    }

    public function testAlarmsEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new CheckAction('id', $this->makeEmpty(Controller::class), [
            'cache' => $this->makeEmpty(CacheInterface::class),
            'job' => $this->makeEmpty(JobInterface::class),
            'queue' => $this->makeEmpty(Queue::class),
        ]);
    }

    public function testSuccessfulInstantiation()
    {
        new CheckAction('id', $this->makeEmpty(Controller::class), [
            'cache' => $this->makeEmpty(CacheInterface::class),
            'job' => $this->makeEmpty(JobInterface::class),
            'queue' => $this->makeEmpty(Queue::class),
            'alarms' => [
                $this->makeEmpty(AlarmInterface::class),
                $this->makeEmpty(AlarmInterface::class),
            ],
        ]);
    }

    public function testJobPushedToQueueOnRun()
    {
        $action = $this->make(CheckAction::class, [
            'queue' => $this->makeEmpty(Queue::class, [
                'push' => Expected::once(),
            ]),
            'cache' => $this->makeEmpty(CacheInterface::class, [
                'getOrSet' => function ($key, $callback) {
                    return call_user_func($callback);
                },
            ]),
        ]);
        $this->assertEquals(ExitCode::OK, $action->run());
    }

    public function testQueueWorks()
    {
        $action = $this->make(CheckAction::class, [
            'queue' => $this->makeEmpty(Queue::class),
            'cache' => $this->makeEmpty(CacheInterface::class, [
                'getOrSet' => time() - 10,
            ]),
        ]);
        $this->assertEquals(ExitCode::OK, $action->run());
    }

    public function testQueueDown()
    {
        $action = $this->make(CheckAction::class, [
            'queue' => $this->makeEmpty(Queue::class),
            'cache' => $this->makeEmpty(CacheInterface::class, [
                'getOrSet' => time() - 1000,
            ]),
            'alarms' => [
                $this->makeEmpty(AlarmInterface::class, [
                    'send' => Expected::once(),
                ]),
            ],
        ]);
        $this->assertEquals(ExitCode::UNSPECIFIED_ERROR, $action->run());
    }

    public function testQueueDownAndAlarmIsSentAlready()
    {
        $action = $this->make(CheckAction::class, [
            'queue' => $this->makeEmpty(Queue::class),
            'cache' => $this->makeEmpty(CacheInterface::class, [
                'getOrSet' => time() - 1000,
                'get' => time() - 10,
            ]),
            'alarms' => [
                $this->makeEmpty(AlarmInterface::class, [
                    'send' => Expected::never(),
                ]),
            ],
        ]);
        $this->assertEquals(ExitCode::UNSPECIFIED_ERROR, $action->run());
    }
}
