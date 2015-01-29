<?php

/**
 * server
 */
require('class_ws.php');
require('function.php');
$server = new Ws('192.168.100.49', '8080', 10);
$server->function['send'] = 'send_callback';
//$server->function['close'] = 'close_callback';
$server->start_server();

