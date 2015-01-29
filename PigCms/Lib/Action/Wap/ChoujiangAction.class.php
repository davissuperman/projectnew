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
	  $password='20146868';
	  $arrr=array(
			'a'=>'姚遥',
			'b'=>'李国鑫',
			'c'=>'陆文龙',
			'd'=>'赵杰',
			'e'=>'冯晶',
		    'g'=>'闫迎华',
			'h'=>'曲彦儒',
			'j'=>'贾阿青',
			'k'=>'胡培敏',
			'l'=>'林绪',
			'm'=>'赵懂',
			'n'=>'张雪翠',
			'o'=>'梁鲜',
			'p'=>'伦文兰',
			'q'=>'黄珊',
			'r'=>'管婷婷',
			's'=>'任元元',
			't'=>'张朋',
			'w'=>'金锋',
			'x'=>'刘莹',
			'y'=>'李建',
			'z'=>'刘超',
			'aa'=>'朱裔骅',
			'bb'=>'马佳',
			'cc'=>'刘金满',
			'dd'=>'李志灿',
			'ee'=>'曾海鸥',
			'ff'=>'常青',
			'gg'=>'杨洋',
			'hh'=>'宋晓宁',
			'ii'=>'曹静',
			'jj'=>'王金晶',
			'kk'=>'李莉莉',
			'll'=>'徐赛',
			'mm'=>'张宏武',
			'nn'=>'吕玫',
			'oo'=>'董晓艳',
			'pp'=>'王欣',
			'qq'=>'刘蕊',
			'rr'=>'张伟',
			'ss'=>'张蕾蕾',
			'tt'=>'李楠楠',
			'uu'=>'潘跃红',
			'vv'=>'吕雪梅',
			'ww'=>'李东颖',
			'xx'=>'彭津',
			'yy'=>'严妍',
			'zz'=>'吕凌慧',
			'aaa'=>'卢克锋',
			'bbb'=>'温宇',
			'ccc'=>'赵爱华',
			'ddd'=>'杨彦来',
			'eee'=>'薛经晶',
			'fff'=>'黄广欣',
			'ggg'=>'朱甜甜',
			'hhh'=>'周键',
			'iii'=>'耿振月',
			'jjj'=>'赵丽',
			'kkk'=>'邾平',
			'lll'=>'耿胜坤',
			'mmm'=>'赵艳',
			'nnn'=>'王平',
			'ooo'=>'吕军岩',
			'ppp'=>'王丹丹',
			'qqq'=>'章婷婷',
			'rrr'=>'赵宪彬',
			'sss'=>'冯艳斌',
			'ttt'=>'刘国财',
			'nuu'=>'徐风',
			'vvv'=>'胡海涛',
			'www'=>'赵延珂',
			'xxx'=>'程云',
			'yyy'=>'肖林艳',
			'zzz'=>'苗云鹏',
			'aaaa'=>'李建维',
			'bbbb'=>'杨金伟',
			'cccc'=>'刘磊',
			'dddd'=>'郭启洋',
			'eeee'=>'刘怀庚',
			'ffff'=>'屈英楠',
			'gggg'=>'程亚涛',
			'hhhh'=>'毕江伟',
			'iiii'=>'崔鹿',
			'jjjj'=>'李占堂',
			'kkkk'=>'陈春朋',
			'llll'=>'舒新',
			'mmmm'=>'管哲',
			'nnnn'=>'罗丽娟',
			'oooo'=>'牛朝熙',
			'pppp'=>'刘硕',
			'qqqq'=>'房海菲',
			'rrrr'=>'吴天翔',
			'ssss'=>'梅立国',
			'tttt'=>'汪雨辰',
			'uuuu'=>'鲁迪',
			'vvvv'=>'徐国辉',
			'wwww'=>'衡连政',
			'xxxx'=>'朱擎',
			'aaaaaa'=>'张俊杰',
			'bbbbbb'=>'杨建江',
			'cccccc'=>'潘权',
			'dddddd'=>'刘钊',
			'eeeeee'=>'刘书宏',
			'ffffff'=>'魏鹏飞',
			'gggggg'=>'赵靖海',
			
			);
      
      $username1=in_array($username,$arrr);
	  if(in_array($username,$arrr)){
		    $user=$username;
			
	  }	
	  
	  //var_dump($wecha_id);exit;
	  if($psd==$password&&$username1){
	     //$_SESSION['num']=1;
		 //echo 'YES';
		
		 $_SESSION['username11']=$user;
		 //$_REQUEST['wecha_id']; 
		 //$num=$_SESSION['num'];
		 //$this->assign("name",'1');
	     $this->redirect("/index.php?g=Wap&m=Choujiang&a=cjsuiji&wecha_id=$wecha_id");
	  }else{
		 echo '<script>alert("用户名OR密码不对");</script>';
		 exit;	
		 //echo '没有用户名OR密码不对';
	  }
	 
	  //echo $password;exit;
	}

	public function cjsuiji(){
	
	   $wecha_id=$_REQUEST['wecha_id'];
	   $username=$_SESSION['username11'];
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
                echo '<script>alert("已经抽完了");</script>';
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