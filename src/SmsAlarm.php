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
    public $template;

    /**
     * @var string
     */
    private $_defaultTemplate = 'Queue is down for: {downtime}';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->provider = Instance::ensure($this->provider, ProviderInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function send($downtime)
    {
        $message = $this->provider->compose($this->template, ['downtime' => $downtime]);
        if ($this->template === null) {
            $message->setBody(strtr($this->_defaultTemplate, [
                'downtime' => $this->formatter->asDuration($downtime),
            ]));
        }
        $message->send();
    }
}
