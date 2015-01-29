<?php
class UpdatepsdAction extends UserAction{

	
	public function index(){
		
	    // var_dump($_REQUEST);
	   
         $this->display();
	}

	public function updatep(){
	  //var_dump($_REQUEST);
	  $uname=$_SESSION['uname'];
	  //echo $uname;
	  $data['password']=$_POST['psdname'];
	  $data['password']=md5($_POST['psdname1']);
	  $tpusers=M('users');
	  $qry=$tpusers->where("username='$uname'")->data($data)->save();
	  if($qry){
	    $this->success('操作成功');	
	  }else{
	    $this->error('修改失败');
	  }

	}

	//ajax验证用户密码
	public function updatepp(){
		
       $uname=$_SESSION['uname'];
	   $loginpwd=$_REQUEST['loginpwd'];
	   //echo $loginpwd;exit;
	   $users=M("users");
	   $vo = $users->where( "username=" . "'" . $uname . "' and password=" . "'" . md5 ($loginpwd) . "'" )->find();
	   //echo $vo;exit;
	   //var_dump($vo);exit;
		if($vo){
			echo '1';exit;
			//$this->success("密码正确！");
		}else{
			echo '2';exit;
			//$this->error("密码不对，请重新输入！");
		}
	
	}

 
	

}


?>