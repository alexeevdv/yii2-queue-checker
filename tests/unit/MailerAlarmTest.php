<?php

namespace tests\unit;

use alexeevdv\yii\queue\checker\MailerAlarm;
use Codeception\Stub\Expected;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;

class MailerAlarmTest extends \Codeception\Test\Unit
{
    public function testMailerEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new MailerAlarm;
    }

    public function testFormatterEnsured()
    {
        $this->expectException(InvalidConfigException::class);
        new MailerAlarm(['mailer' => $this->makeEmpty(MailerInterface::class)]);

    }

    public function testSuccessfulInstantiation()
    {
        $alarm = new MailerAlarm([
            'mailer' => $this->makeEmpty(MailerInterface::class),
            'formatter' => $this->makeEmpty(Formatter::class),
        ]);
        $this->assertNotNull($alarm->subject, 'Default subject should be available');
        $this->assertNotNull($alarm->template, 'Default template should be available');
    }

    public function testSendWithoutAdditionalConfigNoView()
    {
        $alarm = new MailerAlarm([
            'formatter' => $this->makeEmpty(Formatter::class, [
                'asDuration' => 'formatted',
            ]),
            'template' => 'downtime {downtime}',
            'mailer' => $this->makeEmpty(MailerInterface::class, [
                'compose' => Expected::once(function ($view, $params) {
                    $this->assertNull($view);
                    $this->assertArrayHasKey('downtime', $params);
                    $this->assertEquals(333, $params['downtime']);
                    return $this->makeEmpty(MessageInterface::class, [
                        'setTo' => Expected::never(),
                        'setFrom' => Expected::never(),
                        'setSubject' => Expected::once(),
                        'send' => Expected::once(),
                        'setTextBody' => Expected::once(function ($text) {
                            $this->assertEquals('downtime formatted', $text);
                        }),
                    ]);
                }),
            ]),
        ]);
        $alarm->send(333);
    }

    public function testSendWithoutAdditionalConfigWithView()
    {
        $alarm = new MailerAlarm([
            'formatter' => $this->makeEmpty(Formatter::class, [
                'asDuration' => 'formatted',
            ]),
            'view' => '@app/view',
            'mailer' => $this->makeEmpty(MailerInterface::class, [
                'compose' => Expected::once(function ($view, $params) {
                    $this->assertNotNull($view);
                    $this->assertArrayHasKey('downtime', $params);
                    $this->assertEquals(333, $params['downtime']);
                    return $this->makeEmpty(MessageInterface::class, [
                        'setTo' => Expected::never(),
                        'setFrom' => Expected::never(),
                        'setSubject' => Expected::once(),
                        'send' => Expected::once(),
                        'setTextBody' => Expected::never(),
                    ]);
                }),
            ]),
        ]);
        $alarm->send(333);
    }

    public function testSendWithAdditionalConfig()
    {
        $alarm = new MailerAlarm([
            'formatter' => $this->makeEmpty(Formatter::class, [
                'asDuration' => 'formatted',
            ]),
            'subject' => 'Sbj',
            'from' => 'Frm',
            'view' => '@app/view',
            'to' => ['a', 'b'],
            'mailer' => $this->makeEmpty(MailerInterface::class, [
                'compose' => Expected::once(function ($view, $params) {
                    $this->assertNotNull($view);
                    $this->assertArrayHasKey('downtime', $params);
                    $this->assertEquals(333, $params['downtime']);
                    return $this->makeEmpty(MessageInterface::class, [
                        'setTo' => Expected::once(function ($to) {
                            $this->assertEquals(['a', 'b'], $to);
                        }),
                        'setFrom' => Expected::once(function ($from) {
                            $this->assertEquals('Frm', $from);
                        }),
                        'setSubject' => Expected::once(function ($subject) {
                            $this->assertEquals('Sbj', $subject);
                        }),
                        'send' => Expected::once(),
                        'setTextBody' => Expected::never(),
                    ]);
                }),
            ]),
        ]);
        $alarm->send(333);
    }
}
