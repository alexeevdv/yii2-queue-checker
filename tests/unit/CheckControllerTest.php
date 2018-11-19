<?php

namespace tests\unit;

use alexeevdv\yii\queue\checker\CheckController;
use yii\base\Module;

class CheckControllerTest extends \Codeception\Test\Unit
{
    public function testDefaultActionIsCheckAction()
    {
        $controller = new CheckController('id', $this->makeEmpty(Module::class), [
            'checkActionName' => 'custom',
        ]);
        $this->assertEquals('custom', $controller->defaultAction);
    }

    public function testCheckActionConfigIsRespected()
    {
        $controller = new CheckController('id', $this->makeEmpty(Module::class), [
            'checkActionName' => 'custom',
            'checkActionConfig' => self::class,
        ]);
        $this->assertEquals(self::class, $controller->actions()['custom']);
    }
}
