<?php

namespace app\modules\v1;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\v1\controllers';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
        \Yii::$app->user->enableSession = false;
    }
}
