<?php
class TestAction extends BaseAction{
	 

	public function index(){
	import("@.ORG.Swoole.WebSocket");
	
	
	$client = new WebSocket('127.0.0.1', 9503, '/');
	if(!$client->connect())
{
    echo "connect to server failed.\n";
    exit;
}else{
	$resMsg = array(
				'cmd' => 'send',
				'type' =>'f',
				'uid' => 100,
				'openid' => '微信昵称',
				'data' => '你好呀！！',
	);
	
    $client->send($resMsg);
	echo '0k';

}

	 
	}
	 
}