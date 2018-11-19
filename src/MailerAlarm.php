<?php

namespace alexeevdv\yii\queue\checker;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\i18n\Formatter;
use yii\mail\MailerInterface;

/**
 * Class MailerAlarm
 * @package alexeevdv\yii\queue\checker
 */
class MailerAlarm extends BaseObject implements AlarmInterface
{
    /**
     * @var MailerInterface|array|string
     */
    public $mailer = 'mailer';

    /**
     * @var string
     */
    public $view = null;

    /**
     * @var string
     */
    public $template = 'Queue is down for: {downtime}';

    /**
     * @var Formatter|array|string
     */
    public $formatter = 'formatter';

    /**
     * @var string|array Recipients
     */
    public $from;

    /**
     * @var string|array Sender
     */
    public $to;

    /**
     * @var string
     */
    public $subject = 'Queue status notification';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->mailer = Instance::ensure($this->mailer, MailerInterface::class);
        $this->formatter = Instance::ensure($this->formatter, Formatter::class);
    }

    /**
     * @inheritdoc
     */
    public function send($downtime)
    {
        $message = $this->mailer->compose($this->view, ['downtime' => $downtime]);
        if ($this->view === null) {
            $message->setTextBody(strtr($this->template, [
                '{downtime}' => $this->formatter->asDuration($downtime)
            ]));
        }
        if ($this->to !== null) {
            $message->setTo($this->to);
        }
        if ($this->from !== null) {
            $message->setFrom($this->from);
        }
        $message->setSubject($this->subject);
        $message->send();
    }
}
