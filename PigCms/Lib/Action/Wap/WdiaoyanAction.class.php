<?php
class WdiaoyanAction extends BaseAction{

	
	public function index(){
	      
		   
           $token=$_GET['token'];
		   $wecha_id=$_GET['wecha_id'];
		   $wdy_bfenlei=M('wdy_bfenlei');
           $qry=$wdy_bfenlei->where("token='$token' and wecha_id='$wecha_id'")->find();
	       if($qry){
			   $this->redirect("/index.php?g=Wap&m=Wdiaoyan&a=over&wecha_id=$wecha_id&token=$token");exit;
		   }else{
		   $this->display();
	       }
	}
	public function login(){
		   $wdyvalues=M('wdy_value');
		   $wdyinput=M('wdy_input');
		   if($_POST){
			   //var_dump($_POST);exit;
			   $token   =$_POST['token'];
			   $id      =$_POST['id'];
			   $wecha_id=$_POST['wecha_id'];

			   $data['name']=$_POST['xingming'];
			   $data['values']=$_POST['shoujihao'];
			   $data['token']=$_POST['token'];
			   $data['wecha_id']=$_POST['wecha_id'];
			   $data['formid']=$_POST['id'];
			   $data['time']=time();
			  // var_dump($data);exit;
			   $qry=$wdyvalues->add($data);
			   //var_dump($qry);exit;
			   if($qry){
				  
			     $this->redirect("/index.php?g=Wap&m=Wdiaoyan&a=xuanxiang&wecha_id=$wecha_id&token=$token&id=$id");
			   }
		   }else{
			   $id=$_GET['id'];
			   $token=$_GET['token'];
			   
			   $rzt=$wdyinput->where("formid='$id' and token='$token'")->select();
			   $this->assign("rzt",$rzt);
		     
		   }
	      
	       $this->display();
	}
	//选择框
	public function xuanxiang(){
	       $id=$_REQUEST['id'];
		   $token=$_REQUEST['token'];
		   $wecha_id=$_REQUEST['wecha_id'];
		   $formid=$_REQUEST['formid'];
		   //echo $id;
           $wdybt=M('wdy_biaoti');
		   $wdyinput=M('wdy_input');
		   $wdyfenlei=M('wdy_bfenlei');
		   $data['formid'] = $id;
		   $data['token']  = $token;
		   
		   $displayname = $_REQUEST['displayname'];
		   //var_dump($displayname);exit;
		   foreach($displayname as $key => $val){
				$disAr[$key] = explode('_', $val);
				$dataAr['formid'] = $disAr[$key][3];
				$dataAr['b_fid'] = $disAr[$key][2];
				$dataAr['zjid'] = $disAr[$key][1];
				$dataAr['displayname'] = $disAr[$key][0];
				$wdyfenlei->add($dataAr);
		   }
		   $page = isset($_REQUEST['hidden'])? $_REQUEST['hidden'] : 0;
		   $rzt=$wdybt->where($data)->field('id,bname,formid')->limit($page, 1)->select();
		   $rzt[0]['fenlei'] = M("wdy_input")->where(array('b_fid'=>$rzt[0]['id']))->select();
		  
		   if($rzt[0]['fenlei']==NULL){
		       $this->redirect("/index.php?g=Wap&m=Wdiaoyan&a=yijian&wecha_id=$wecha_id&token=$token&id=$id");
		   }
		   //$num = count($rzt);
		   $this->assign('list',$rzt[0]);
		   $this->assign('page',$page+1);

		   $this->assign('token',$token);
		   $this->assign('id',$id);
		   $this->assign('formid',$formid);
		   $this->assign('wecha_id',$wecha_id);
		   //foreach(){}
		   //for($i=0;$i<count($rzt);$i++){
		   //$rzt[$i]['fenlei'] = M("wdy_input")->where(array('b_fid'=>$rzt[$i]['id']))->select();
		   //}	
           //$this->assign('list1',$rzt);
		   
		   $this->display();
	       
	}
	public function xxtj(){
		  var_dump($_REQUEST);exit;
		//var_dump($_POST['displayname1']);
		$user_root=$_REQUEST['displayname'];
		//var_dump($user_root); exit;
		/*for($i=0;$i<count($user_root);$i++)
			{
			$shareAn[]=$user_root[$i];
			}*/
        $str1=implode(",",$user_root);
	    //$display=implode(',',$_POST['displayname']);
	      
		   //var_dump($display);
		   
		   //var_dump($str1);
	}
	//提意见页面
	public function yijian(){
		  $yijian=M('wdy_bfenlei');
		  $wdy_yuyuexx=M('wdy_yuyuexx');
		  $token=$_GET['token'];
		  $list=$wdy_yuyuexx->where("token='$token'")->field("keyid")->find();
		  $kid=$list['keyid'];
		  $lottery=M('lottery');
		  $klist=$lottery->where("token='$token' and id='$kid'")->find();
		   $type=$klist['type'];
		   $keyid=$list['keyid'];
		   $this->assign('type',$type); 
		   $this->assign('keyid',$keyid); 
		   if($_POST){
			   $data['formid']=$_POST['id'];
			   $data['yijian']=$_POST['te'];
			   $data['wecha_id']=$_POST['wecha_id'];
			   $wecha_id=$_POST['wecha_id'];
			   $data['token']=$_POST['token'];
			   $token=$_POST['token'];
			   $type1=$_POST['type'];
			   $keyid1=$_POST['keyid'];
			   
			   $qry=$yijian->add($data);
			   if($qry){
				   if($type1=='2'){
				     $this->redirect("./index.php?g=Wap&m=Guajiang&a=index&wecha_id=$wecha_id&token=$token&id=$keyid1");
				   }elseif($type1=='1'){
				        $this->redirect("./index.php?g=Wap&m=Lottery&a=index&wecha_id=$wecha_id&token=$token&id=$keyid1");
				   }
				   //echo 'OK';
			   }else{
				   echo 'NO';
			   }
		   }
	       $this->display();
	   
	}


	//调研以后下次进来的页面
	public function over(){
	       $token=$_GET['token'];
	       $wdy_addxinxi=M('wdy_addxinxi'); 
	       $list=$wdy_addxinxi->where("token='$token'")->find();
	       $this->assign('list',$list); 
	        
	       $this->display();
	}
	
}

?>