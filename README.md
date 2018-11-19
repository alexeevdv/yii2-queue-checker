#Yii2 queue checker

# Usage example

```php

return [
// ...
   'controllerMap' => [
       'queue-checker' => [
           'class' => \alexeevdv\yii\queue\checker\CheckController::class,
           'checkActionConfig' => [
               'class' => \alexeevdv\yii\queue\checker\CheckAction::class,
               'alarm' => [
                   'class' => \alexeevdv\yii\queue\checker\SmsAlarm::class,
                   'provider' => [
                       'class' => \mikk150\messentesms\Provider::class,
                       'username' => 'hurr',
                       'password' => 'durr',
                       'messageConfig' => [
                           'from' => 'My application',
                           'to' => [
                               '+123456789',
                               '+987654321',
                           ],
                       ],
                   ],
               ],
           ],
       ],
   ],
// ...
],
```

```bash
./yii queue-checker
```
