<?php
class JiekouAction extends UserAction{
	
	/*public function _initialize() {
		
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'diymen_set')){
            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
		$this->selfform_model=M('Selfform');
		$this->selfform_input_model=M('Selfform_input');
		$this->selfform_value_model=M('Selfform_value');
		$this->token=session('token');
		$this->assign('token',$this->token);
		$this->assign('module','Selfform');
	}*/

	public function index(){

		   //phpinfo();
		   $this->display();
	}
	public function jiekou(){
		  /*$Touser= $_POST['Touser'];
		  $openid= $_POST['openid'];
		  $catetime= $_POST['catetime'];
		  //$msgtype=$_POST['text'];
		  $content=$_POST['message'];
		  $Event=$_POST['Event'];
		  $EventKey=$_POST['EventKey'];
		  //var_dump($_POST);*/
          //$ToUserName='11'; 
          //$FromUserName='22'; 
          //$CreateTime='201403131005'; 
          $Content=$_POST['message']; 
          $time=date("Ymdhis");
		 
		  $uri = "http://www.henhaochina.cn/weixin/open";
		// 参数数组
			$data = array (
					'ToUserName' => '11',
			        'FromUserName' => '22',
			        'CreateTime' =>$time,
			        'Content' =>$Content
			);

			 
			$ch = curl_init ();
			//print_r($ch);
			$header[] = "Content-type: text/xml";//定义content-type为xml
			curl_setopt ( $ch, CURLOPT_URL, $uri );
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
			$return = curl_exec ($ch);
			//$arr=json_decode(json_encode($xml_array),TRUE);
			curl_close ($ch);
			print_r($return);exit;
		 
	}
}


?>