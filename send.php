<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->confirm_select();
$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage(date("H:i:s"));
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->set_ack_handler(function (AMQPMessage $message) {
    echo "Message acked with content: {$message->body}".PHP_EOL;
});
$channel->set_nack_handler(function (AMQPMessage $msg) {
    echo "FAIL!! with content: {$msg->body}".PHP_EOL;
});

$channel->wait_for_pending_acks();
$channel->close();
$connection->close();