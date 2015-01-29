<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'lib/class.websocket_client.php';
$clients = new WebsocketClient;
$clients->connect('127.0.0.1', 8080, '/demo', 'foo.lh');
$clients->sendData('ddddddddddddd');
 