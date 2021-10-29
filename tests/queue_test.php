<?php
declare(strict_types = 1);
use Inspire\Core\System\Config;
use Inspire\Queue\Factories\ProducerFactory;
use Inspire\Core\Message\JsonMessage;
use Inspire\Core\Message\ArrayMessage;
use Inspire\Queue\Queue;

define('APP_NAME', 'test');
include dirname(__DIR__) . '/vendor/autoload.php';
Inspire\Core\System\Config::loadFromFolder('config');

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
Queue::on('track')->consume(function () {});
Queue::on('redisteste')->consume(function () {});
