<?php

declare(strict_types=1);

use Inspire\Config\Config;
use Inspire\Support\Message\Serialize\JsonMessage;
use Inspire\Support\Message\Serialize\ArrayMessage;
use Inspire\Queue\Queue;

define('APP_NAME', 'test');
include dirname(__DIR__) . '/vendor/autoload.php';
Config::loadFromFolder('config');

$message1 = new JsonMessage([
    'test' => 'json message serializaer test'
]);
$message2 = new ArrayMessage([
    'test' => 'Array message serializaer test'
]);
Queue::on('track')->add($message1);
Queue::on('track')->add($message2);

Queue::on('redisteste')->add($message1);
Queue::on('redisteste')->add($message2);
Queue::on('redisteste')->addString("Some simples message");
Queue::on('track')->addString("Some simples message");
// Queue::on('track')->consume(function ($msg) {
//     var_dump($msg->getBody());
//     var_dump($msg->getMessageId());
// });
// Queue::on('redisteste')->consume(function ($msg) {
//     var_dump($msg->getBody());
//     var_dump($msg->getMessageId());
// });
