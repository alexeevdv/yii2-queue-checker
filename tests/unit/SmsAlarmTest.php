<?php

namespace tests\unit;

use alexeevdv\yii\queue\checker\SmsAlarm;
use Codeception\Stub\Expected;
use mikk150\sms\MessageInterface;
use mikk150\sms\ProviderInterface;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter;

class SmsAlarmTest extends \Codeception\Test\Unit
{
    public function testProviderEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new SmsAlarm;
    }

    public function testFormatterEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new SmsAlarm(['provider' => $this->makeEmpty(ProviderInterface::class)]);
    }

    public function testSuccessfulInstantiation()
    {
        $alarm = new SmsAlarm([
            'provider' => $this->makeEmpty(ProviderInterface::class),
            'formatter' => $this->makeEmpty(Formatter::class),
        ]);
        $this->assertNotNull($alarm->template, 'Default template should be available');
    }

    public function testSendWithoutAnyAdditioalConfig()
    {
        $alarm = new SmsAlarm([
            'formatter' => $this->makeEmpty(Formatter::class, [
                'asDuration' => 'formatted',
            ]),
            'provider' => $this->makeEmpty(ProviderInterface::class, [
                'compose' => Expected::once(function ($template, $params) {
                    $this->assertNotNull($template);
                    $this->assertArrayHasKey('downtime', $params);
                    $this->assertEquals('formatted', $params['downtime']);
                    return $this->makeEmpty(MessageInterface::class, [
                        'setTo' => Expected::never(),
                        'setFrom' => Expected::never(),
                        'send' => Expected::once(),
                    ]);
                }),
            ]),
        ]);
        $alarm->send(333);
    }

    public function testSendWithCustomToAndCustomFrom()
    {
        $alarm = new SmsAlarm([
            'from' => 'Sender',
            'to' => [
                '111',
                '222',
            ],
            'formatter' => $this->makeEmpty(Formatter::class, [
                'asDuration' => 'formatted',
            ]),
            'provider' => $this->makeEmpty(ProviderInterface::class, [
                'compose' => Expected::once(function ($template, $params) {
                    $this->assertNotNull($template);
                    $this->assertArrayHasKey('downtime', $params);
                    $this->assertEquals('formatted', $params['downtime']);
                    return $this->makeEmpty(MessageInterface::class, [
                        'setTo' => Expected::once(function ($to) {
                            $this->assertEquals(['111', '222'], $to);
                        }),
                        'setFrom' => Expected::once(function ($from) {
                            $this->assertEquals('Sender', $from);
                        }),
                        'send' => Expected::once(),
                    ]);
                }),
            ]),
        ]);
        $alarm->send(333);
    }
}
