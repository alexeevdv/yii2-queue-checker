#Yii2 queue checker

# Usage example


```php
<?php
return [
// ...
   'components' => [
       'smsProvider' => [
            'class' => \mikk150\sms\ProviderInterface::class,       
       ],
//     'mailer' => [
//         'class' => \yii\mailer\MailerInterface::class,
//     ],
   ],
   'controllerMap' => [
       'queue-checker' => [
           'class' => \alexeevdv\yii\queue\checker\CheckController::class,
           'checkActionConfig' => [
               'class' => \alexeevdv\yii\queue\checker\CheckAction::class,
               'alarm' => [
                    'class' => \alexeevdv\yii\queue\checker\SmsAlarm::class,
                    'provider' => 'smsProvider',
                    'from' => 'Queue',
                    'to' => [
                        '+123456789',
                        '+987654321',
                    ],
               ],
//             'alarm' => [
//                  'class' => \alexeevdv\yii\queue\checker\MailerAlarm::class,
//                  'from' => 'Queue',
//                  'to' => [
//                      'admin@example.org',
//                      'suppoer@example.org',
//                  ],
//                  'subject' => 'Queue status notication',
//             ],
           ],
       ],
   ],
// ...
];
```

```bash
./yii queue-checker
```

### Custom alarm example

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
               'alarm' => [
                    'class' => \common\components\WebhookAlarm::class,
                    'webhook' => 'http://your-webhook-here',
               ],
           ],
       ],
   ],
// ...
];
```
