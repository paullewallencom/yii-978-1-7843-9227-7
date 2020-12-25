<?php

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
        'authkey' => 'valid authkey'
    ]
];