<?php
class ChoujiangAction extends BaseAction{
	public function index(){
	   //echo '111111';exit;
	   $token   =$_REQUEST['token'];
	   $wecha_id=$_REQUEST['wecha_id'];
	   //echo $wecha_id;
	   $this->assign("token",$token);
	   $this->assign("wecha_id",$wecha_id);
	   $this->display();
    }

	public function choujiang(){
	
	  $num=$_REQUEST['num'];
	  $wkid=$_REQUEST['wkid'];
	  $choujiang=M('choujiang');
      $arr=$choujiang->where("num='$num'")->find();
      $arr1=$choujiang->where("wkid='$wkid'")->find();
          if($arr['zhuangt']==2){
		  
                  echo '此奖项已经被抽走';
		   
	  }elseif($arr1['wkid']!=''){
	           
	       echo '您已经抽过奖了';
	  }else{
		  
	    if($arr){
		     
		     echo '您中奖了';
		     $data['zhuangt']=2;
		     $data['wkid']=$wkid;
		     $choujiang->add($data);
			 $choujiang->where("num='$num'")->data($data)->save();

		  }else{
			 $data['wkid']=$wkid;
		     $choujiang->add($data);
		     echo '您没中奖';
		  }
	    }
	}

	public function chakancj(){
	  $num1=$_REQUEST['num1'];
	  echo $num1;exit;
	}

	public function jieshao(){
	
	  $this->display();
	}

	
	public function cjdenglu(){
		
	  //echo $_REQUEST['wecha_id'];
	
	  $this->display();
	}
	//登陆界面
	public function denglu(){
	  //var_dump($_REQUEST);exit;
	  $psd=$_REQUEST['psd'];
	  //var_dump($psd);exit;
	  $wecha_id=$_REQUEST['wecha_id'];
	  //echo $wecha_id;
	  $username=$_REQUEST['username']; 
	  //echo $wecha_id;
	  $password='8888';
	  $arrr=array(
			'a'=>'邢艳艳',
			'b'=>'严妍',
			'c'=>'李东颖',
			'd'=>'杨彦来',
			'e'=>'赵艳',
		    'g'=>'吕军岩',
			'h'=>'王丹丹',
			'j'=>'章婷婷',
			'k'=>'赵延珂',
			'l'=>'程云',
			'm'=>'马苗苗',
			'n'=>'李建维',
			'o'=>'刘磊',
			'p'=>'董晓艳',
			'q'=>'王欣',
			'r'=>'刘蕊',
			's'=>'张伟',
			't'=>'张蕾蕾',
			'w'=>'李楠楠',
			'x'=>'苗云鹏',
			'y'=>'薛经晶',
			'z'=>'王平',
			);
      
      $username1=in_array($username,$arrr);
	  if(in_array($username,$arrr)){
		    $user=$username;
	  }else{
	      echo 'NO';
	  }	
	  
	  //var_dump($wecha_id);exit;
	  if($psd==$password&&$username1){
	     $_SESSION['num']=1;
		  echo 'YES';
		  echo '11111111111';
		  $this->display();
		 //$_REQUEST['wecha_id']; 
		 //$num=$_SESSION['num'];
		 //$this->assign("name",'1');
	     //$this->redirect("/index.php?g=Wap&m=Choujiang&a=cjsuiji&wecha_id=$wecha_id&username=$user");
	  }else{
	     echo '没有用户名OR密码不对';
	  }
	 // $this->display();
	  //echo $password;exit;
	}

	public function cjsuiji(){
	
	   $wecha_id=$_REQUEST['wecha_id'];
	   $username=$_REQUEST['username'];
	   //echo $username;
	  $choujiang=M('choujiang');
      $rzt=$choujiang->where("wkid='$wecha_id'")->find();
      $rzt1=$choujiang->where("username='$username'")->find();
      $this->assign('rzt',$rzt); 
      $this->assign('rzt1',$rzt1); 

	  //var_dump($rzt);exit;
	  //$cjnum='';
	  if($rzt['wkid']!=''||$rzt1['username']!=''){
	      //$cjnum=$rzt['num'];
		   $num=$rzt1['num'];
		   $rzt1username=$rzt1['username'];
		   $rzt1wkid=$rzt1['wkid'];
	       $this->assign("num",$num);     
	       $this->assign("rzt1username",$rzt1username);     
	       $this->assign("rzt1wkid",$rzt1wkid); 
		   //echo $rztwkid;
	       //echo '此用户已经抽过了';
	  }else{
		   
	     	  $arr=$choujiang->where("wkid=''")->select();
			  $counts = count($arr);
			  $dataAr = array();
			  //var_dump($counts);
			  //var_dump($counts);exit;
			  if($counts==0){
			    echo '已经抽完了';exit;
			  }else{
			  foreach($arr as $key => $val){
				$dataAr = $arr[mt_rand(0,$counts-1)];
			   }
			   $cjnum=$dataAr['num'];
			   
			   $data['wkid']=$wecha_id;
			   $data['username']=$username;
			   //var_dump($data);exit;
			   $choujiang->where("num='$cjnum'")->data($data)->save();
			  }
		  $rzt2=$choujiang->where("username='$username' and wkid='$wecha_id'")->find();
		  $cjnum=$rzt2['num'];
		  $cjusername=$rzt2['username'];
		  $cjwkid=$rzt2['wkid'];
		  $this->assign('cjnum',$cjnum);
		  $this->assign('cjusername',$cjusername);
		  $this->assign('cjwkid',$cjwkid);

	  }
	  
	  
	  

	   
	   
	   
	   
	   $this->display();
	}
} 

?>