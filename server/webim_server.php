<?php
define('DEBUG', 'on');
define("WEBPATH", realpath(__DIR__ . '/../'));
require __DIR__ . '/../Swoole/lib_config.php';

class WebIM extends Swoole\Network\Protocol\WebSocket
{
    /**
     * 下线时，通知所有人
     */
    function onClose($serv, $client_id, $from_id)
    {
	
		if($this->connections[$client_id]['type']=='s'){
			$resMsg = array(
				'cmd' => 'offline',
				'type'=>$this->connections[$client_id]['type'],
				'fd' => $client_id,
				'from' => 0,
				'channal' => 0,
				'data' => $this->connections[$client_id]['name'] . "下线了。。",
			);
			//将下线消息发送给所有人
			$this->log("onOffline: " . $client_id);
			$this->broadcast($client_id, $resMsg);
		}
        parent::onClose($serv, $client_id, $from_id);
    }	
	/*给客服群发消息*/	
    function broadcast($client_id, $msg)
    {
        foreach ($this->connections as $clid => $info) {
            if ($client_id != $clid&&$info['type']=='s') {
				$this->log("type: " . $info['type']);
                $this->send($clid, json_encode($msg));
            }
        }
    }
	//给指定客服发信息
	function send_uid($uid,$resMsg){
		foreach ($this->connections as $clid => $info) {
				if($info['uid']==$uid){
				    $this->log("type: " . $info['type'].'===uid:'.$info['name']);
					$this->send($clid, json_encode($resMsg));
					break;
				}
		}
		
	}
    /**
     * 接收到消息时
     * @see WSProtocol::onMessage()
     */
    function onMessage($client_id, $ws)
    {
        $this->log("onMessage: " . $ws['message']);
        $msg = json_decode($ws['message'], true);
		if ($msg['type']=='s'){//是客服操作	
				if ($msg['cmd'] == 'login') {
					$this->connections[$client_id]['name'] = $msg['name'];
					$this->connections[$client_id]['avatar'] = $msg['avatar'];
					$this->connections[$client_id]['uid'] = $msg['uid'];
					$this->connections[$client_id]['type'] ='s';
					//回复给登录用户
					$resMsg = array(
						'cmd' => 'login',
						'type' =>'s',
						'fd' => $client_id,
						'name' => $msg['name'],
						'avatar' => $msg['avatar'],
					);
					$this->send($client_id, json_encode($resMsg));
					//广播给其它在线客服用户
					$resMsg['cmd'] = 'newUser';
					$resMsg['type'] = 's';
					$loginMsg = array(
						'cmd' => 'fromMsg',
						'from' => 0,
						'type' =>'s',
						'channal' => 0,
						'data' => $msg['name'] . "上线了。。",
					);					
					$this->broadcast($client_id, $resMsg);
					//将上线消息发送给所有人
					$this->broadcast($client_id, $loginMsg);
				}  elseif ($msg['cmd'] == 'getOnline') {//获取客服在线列表
					$resMsg = array(
						'cmd' => 'getOnline',
						'type' =>'s',
					);
					foreach ($this->connections as $clid => $info) {
						if($info['type']=='s'){
							$resMsg['list'][] = array(
								'fd' => $clid,
								'type' =>'s',
								'name' => $info['name'],
								'avatar' => $info['avatar'],
							);
						}
					}
					$this->send($client_id, json_encode($resMsg));
				}  elseif ($msg['cmd'] == 'message') {//发送信息请求
					$resMsg = $msg;
					$resMsg['cmd'] = 'fromMsg';
					
					//表示客服群发
					if ($msg['channal'] == 0) {
						foreach ($this->connections as $clid => $info) {
							if($info['type']=='s'){
								$this->send($clid, json_encode($resMsg));
							}
						}

					} //表示客服私聊
					elseif ($msg['channal'] == 1) {
						$this->send($msg['to'], json_encode($resMsg));
						$this->send($msg['from'], json_encode($resMsg));
					}
				}
		}else{//是粉丝操作
			$this->log("粉丝发信息了start: ".$msg['cmd']);
			if ($msg['cmd'] == 'send') {
				$this->log("粉丝发信息了: ");
				$this->connections[$client_id]['type'] ='f';
				$resMsg['cmd'] = 'fromMsg';
				$resMsg['type'] = 's';
				$resMsg['channal'] =1;
				$resMsg['data'] =$msg['data'];
				$resMsg['from'] =$msg['openid'];			 
				$this->send_uid($msg['uid'],$resMsg);
				$this->log("粉丝发信息了:end ");
			}
	
	
		}	
		
    }
}

$AppSvr = new WebIM();
$AppSvr->loadSetting(__DIR__."/swoole.ini"); //加载配置文件
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

/**
 * 如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台
 * SelectTCP 使用select做事件循环，支持windows平台
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$server = new \Swoole\Network\Server('0.0.0.0', 9503);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 0));