<?php
class RhjiekouAction extends UserAction{
	
	public function _initialize() {
		
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		
		if(!strpos($token_open['queryname'],'Rhjiekou')){
            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
	}

	public function index(){
           //var_dump($_SESSION['uid']);
		   //phpinfo();
		   $token=$_GET['token'];
		   $api=M('api'); 
		   $jiekou=$api->where("token='$token'")->select();
		  $this->assign('list',$jiekou);
		   $this->display();
	}

	public function jiekouadd(){
		   $api=M('api'); 
		   
		   if($_POST){
                $data['keyword']=$_POST['keyword'];
			    $data['number']=$_POST['leixing'];
			    $data['token']=$_POST['token'];
			    $token=$_POST['token'];
			    $data['url']=$_POST['url'];
				$data['uid']=$_SESSION['uid'];
		        $data['time']=time();
			    $qry=$api->add($data);
				if($qry){
                    $this->success('操作成功',U('Rhjiekou/index',array('token'=>$token)));
				}else{
				    $this->error('操作失败');
				}
				//var_dump($data);

		   }else{
		  
	       $this->display();
		   }
	}

	public function xiugai(){
	       $token=$_GET['token'];  
	       $id=$_GET['id'];
		   $api=M('api');
		   if($_POST){
                   
			   $data['keyword']=$_POST['keyword'];
			   $data['number']=$_POST['leixing'];
			   $data['token']=$_POST['token'];
			   $token=$_POST['token'];
			   $data['url']=$_POST['url'];
			   $data['uid']=$_SESSION['uid'];
			   $id=$_POST['id'];
			   $data['time']=time();

			   $qry=$api->where("token='$token' and id='$id'")->save($data);
			   if($qry){
				    $this->success('操作成功',U('Rhjiekou/index',array('token'=>$token)));
				}else{
                     $this->error('操作失败');
				}
		   }else{
			   $list=$api->where("token='$token' and id='$id'")->find(); 
		       $this->assign("list",$list);
			   $this->display();
		   }
	      
	}

	public function del(){
	       $token=$_GET['token'];  
	       $id=$_GET['id'];
		   $api=M('api');
           $qry=$api->where("token='$token' and id='$id'")->delete();
		   if($qry){
		        $this->success('操作成功');
		   }else{
		         $this->error('操作失败');
		   }
	       
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