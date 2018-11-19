<?php

namespace alexeevdv\yii\queue\checker;

/**
 * Interface AlarmInterface
 * @package alexeevdv\yii\queue\checker
 */
interface AlarmInterface
{
    /**
     * @param int $downtime In seconds
     */
    public function send($downtime);
}
