#Yii2 queue checker

[![Build Status](https://travis-ci.org/alexeevdv/yii2-queue-checker.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-queue-checker) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-queue-checker/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-queue-checker)
![PHP 5.4](https://img.shields.io/badge/PHP-5.4-green.svg) 
![PHP 5.5](https://img.shields.io/badge/PHP-5.5-green.svg) 
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg) 
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)

## Usage example


```php
<?php
return [
// ...
    'components' => [
        'smsProvider' => [
            'class' => \mikk150\sms\ProviderInterface::class,       
        ],
        'mailer' => [
            'class' => \yii\mail\MailerInterface::class,
        ],
    ],
    'controllerMap' => [
        'queue-checker' => [
            'class' => \alexeevdv\yii\queue\checker\CheckController::class,
            'checkActionConfig' => [
                'class' => \alexeevdv\yii\queue\checker\CheckAction::class,
                'alarms' => [
                    [
                        'class' => \alexeevdv\yii\queue\checker\SmsAlarm::class,
                        'provider' => 'smsProvider',
                        'from' => 'Queue',
                        'to' => [
                            '+123456789',
                            '+987654321',
                        ],
                    ],
                    [
                        'class' => \alexeevdv\yii\queue\checker\MailerAlarm::class,
                        'subject' => 'Queue status notication',
                        'from' => 'Queue',
                        'to' => [
                            'admin@example.org',
                            'suppoer@example.org',
                        ],
                    ],
                ],
            ],
        ],
    ],
// ...
];
```

```bash
./yii queue-checker
```

## Custom alarm example

```php
<?php
namespace common\components;

use alexeevdv\yii\queue\checker\AlarmInterface;
use yii\base\BaseObject;
use yii\di\Instance;
use yii\httpclient\Client;

class WebhookAlarm extends BaseObject implements AlarmInterface 
{
    public $httpClient = Client::class;
    
    public $webHook;

    public function send($downtime)
    {
        Instance::ensure($this->httpClient, Client::class)->post($this->webhook, [
            'downtime' => $downtime,
        ]);
    }
}
```

```php
<?php
return [
// ...
   'controllerMap' => [
       'queue-checker' => [
           'class' => \alexeevdv\yii\queue\checker\CheckController::class,
           'checkActionConfig' => [
               'class' => \alexeevdv\yii\queue\checker\CheckAction::class,
               'alarms' => [
                   [
                        'class' => \common\components\WebhookAlarm::class,
                        'webhook' => 'http://your-webhook-here',
                   ],
               ],
           ],
       ],
   ],
// ...
];
```
