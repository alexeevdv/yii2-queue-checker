<?php

namespace alexeevdv\yii\queue\checker;

use mikk150\sms\ProviderInterface;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\i18n\Formatter;

/**
 * Class SmsAlarm
 * @package alexeevdv\yii\queue\checker
 */
class SmsAlarm extends BaseObject implements AlarmInterface
{
    /**
     * @var ProviderInterface|array|string
     */
    public $provider;

    /**
     * @var Formatter|array|string
     */
    public $formatter = 'formatter';

    /**
     * @var string
     */
    public $template = 'Queue is down for {downtime}';

    /**
     * @var array|string Recipients
     */
    public $to;

    /**
     * @var string Sender
     */
    public $from;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->provider = Instance::ensure($this->provider, ProviderInterface::class);
        $this->formatter = Instance::ensure($this->formatter, Formatter::class);
    }

    /**
     * @inheritdoc
     */
    public function send($downtime)
    {
        $message = $this->provider->compose($this->template, ['downtime' => $this->formatter->asDuration($downtime)]);
        if ($this->to !== null) {
            $message->setTo($this->to);
        }
        if ($this->from !== null) {
            $message->setFrom($this->from);
        }
        $message->send();
    }
}
