<?php

require_once dirname(__FILE__) . "/../vendor/autoload.php";
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$client = \Yii::createObject(\Yzchan\Yii2Predis\Connection::class, [
    'hostname' => '127.0.0.1',
    'port' => 6379,
    'database' => 0,
]);

/* @var $client \Predis\Client */

var_dump($client->get('test'));