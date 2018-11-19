#Yii2 queue checker

# Usage example


```php
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
],
```

```bash
./yii queue-checker
```
