<?php

namespace alexeevdv\yii\queue\checker;

use yii\console\Controller;

/**
 * Class CheckController
 * @package alexeevdv\yii\queue\checker
 */
class CheckController extends Controller
{
    /**
     * @var array
     */
    public $checkActionConfig = [
        'class' => CheckAction::class,
    ];

    /**
     * @var string
     */
    public $checkActionName = 'check';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->defaultAction = $this->checkActionName;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            $this->checkActionName => $this->checkActionConfig,
        ];
    }
}
