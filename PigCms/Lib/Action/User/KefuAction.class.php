<?php

/*
  人工客服
 */

class KefuAction extends UserAction {

   
    /**
     * 客服列表
     */
    public function index() {
		
		$customer_service_users=M('customer_service_users');
        $token = $_GET['token'];
        $rzt = $customer_service_users->where("token='$token'")->select();
        $count=count($rzt);
		
        $page = new Page($count, 25);
       
        $show = $page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	    $list = $customer_service_users->where("token='$token'")->order('uid desc  ')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

	//体力活
    public function decode($buffer) {
        $len = $masks = $data = $decoded = null;
		//这个的意思是 如果这个超过127 就删除 127 对应的asicc 删除delete
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
			//截取字符串长度
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        }else{
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }
     //客服页面
	 public function set(){
	 
	        $this->display();
	 }

    /**
     * 添加客服
     */
    public function add() {
           $customer_service_users=M('customer_service_users');
		   if($_POST){
               
			   $token=$_POST['token'];
			   $data['token']=$_POST['token'];
			   $data['name']=$_POST['name'];
			   $data['username']=$_POST['userName'];
			   $data['userpwd']=md5($_POST['userPwd']);
			   $data['type']=$_POST['jibie'];
			   $qry=$customer_service_users->add($data);
			   if($qry){
                       $this->success('操作成功',U('Kefu/index',array('token'=> $token)));
			   }else{
				        $this->error('操作失败');
			   }
		   }else{
		   $this->display();

		   }
    } 

	//ajax验证昵称
	public function chaxunuser(){
	
	   $uname= $_REQUEST['uname'];
	   $token= $_REQUEST['token'];
	   //echo $uname.$token;exit;
	    $users=M("customer_service_users");
	   //echo $uname;exit;
	   $vo = $users->where("name='$uname' and token='$token'")->find();
	   //echo $vo;exit;
	   //var_dump($vo);exit;
		if($vo){
			echo '1';exit;
			
		}else{
			echo '2';exit;
			
		}

	}


	//ajax验证用户名
	public function chaxunuser1(){
	
	   $username= $_REQUEST['username'];
	   $token= $_REQUEST['token'];
	   //echo $username.$token;exit;
	    $users=M("customer_service_users");
	   //echo $uname;exit;
	   $vo = $users->where("username='$username' and token='$token'")->find();
	   //echo $vo;exit;
	   //var_dump($vo);exit;
		if($vo){
			echo '1';exit;
			
		}else{
			echo '2';exit;
			
		}

	}
  

  //修改昵称
  public function updateuser(){
           $customer_service_users=M('customer_service_users');
           $nn=$_REQUEST['nn'];
           $token=$_REQUEST['token'];
           $username=$_REQUEST['username'];
		   //echo $nn.$token.$username;exit;
		   $data['name']=$nn;
           $rzt=$customer_service_users->where("token='$token' and username='$username'")->save($data);
		   if($rzt){
			    echo '1';exit;
		   }else{
		        echo '2';exit;
		   }
  }
   

    /**
     * 删除
     */
    public function del() {
		$token=$_GET['token'];
        $customer_service_users=M('customer_service_users');
		$customer_service_userfans_message=M('customer_service_userfans_message');
        $uid = $this->_get('id', 'intval');        
        if ($customer_service_users->where("token='$token' and uid='$uid'")->delete()) {          $customer_service_userfans_message->where("token='$token' and uid='$uid'")->delete();
            $this->success('操作成功',U('Kefu/index',array('token'=> $token)));
        } else {
            $this->error('操作失败',U('Kefu/index',array('token'=> $token)));
        }
    }

   //聊天记录
    public function ltiaojl(){
		    $token=$_GET['token'];
		    $customer_service_users=M('customer_service_users');
		    $customer_service_userfans_message=M('customer_service_userfans_message');
            $list=$customer_service_users->where("token='$token'")->select();
			
			$date=time();
			//一个星期的时间戳
			$xq=60*60*24*7;
			//一个星期之前的准确时间
			$xqsj=$date-$xq;
			$xqdate=date('Y-m-d',$xqsj);
			//echo $xqdate; echo '<br>';
			//当前时间
			$date3=date('Y-m-d',$date);
			$this->assign("xqdate",$xqdate);
			$this->assign("date3",$date3);
			//echo $date3;
			$userfansjl=$_SESSION['userfansjl'];
			
		    $this->assign("lists",$userfansjl);
			if($_POST){
				   if($_REQUEST['xz']=='0'){
					   //一天的时间戳
					   $sjc=60*60*24;
					   $token      =$_REQUEST['token'];
					   $uid      =$_REQUEST['xz'];
					   $ks   =$_REQUEST['statdate'];
					   $jieshu=$_REQUEST['enddate'];
					   $_SESSION['ks']=$ks;
					   $_SESSION['jieshu']=$ks;
					   $starttime=strtotime($_REQUEST['statdate']);
					   $lasttime =strtotime($_REQUEST['enddate']);
					   $lasttimenew=$lasttime+$sjc;
                       /*$rzt1=$customer_service_userfans_message->table("tp_customer_service_userfans_message a")->join("tp_customer_service_fans b on a.openid=b.openid")->join("tp_customer_service_users c a.uid=c.uid")->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->select();*/
					   $rzt1=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->select();
					   //echo '2222222222';
					   //exit;
						//print_r($rzt1);exit;
				        $num=count($rzt1);
					    $page = new Page($num, 20);
       
					    $show = $page->show();// 分页显示输出
					   // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
					    $rzt=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->limit($page->firstRow.','.$page->listRows)->select();
						/*$rzt = $customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->limit($page->firstRow.','.$page->listRows)->select();*/
						//print_r($rzt);exit;
						$this->assign('page', $show);
						$this->assign('rzt', $rzt);
						$this->assign('uid', $uid);
						$this->assign('uidd', $uid);
						$this->assign('ks', $ks);
						$this->assign('jieshu', $jieshu);
				  
					   //$this->assign("rzt",$rzt);
					    $this->assign("list",$list); 
				   }else{
				   //一天的时间戳
				   $sjc=60*60*24;
				   $uid      =$_REQUEST['xz'];
				   $token      =$_REQUEST['token'];
				   $ks   =$_REQUEST['statdate'];
				   $jieshu=$_REQUEST['enddate'];
				   $_SESSION['ks']=$ks;
					$_SESSION['jieshu']=$ks;
				   $starttime=strtotime($_REQUEST['statdate']);
				   $lasttime =strtotime($_REQUEST['enddate']);
				   $lasttimenew=$lasttime+$sjc;
				   
				   /*$rzt1=$customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("uid='$uid' and token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->select();*/
				   $rzt1=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.uid='$uid' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->select();
				   //print_r($rzt1);exit;
				   $num=count($rzt1);
				   $page = new Page($num, 20);
       
					$show = $page->show();// 分页显示输出
				    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
				   $rzt=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.uid='$uid' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->limit($page->firstRow.','.$page->listRows)->select();
				   /*$rzt=$customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("uid='$uid' and token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->limit($page->firstRow.','.$page->listRows)->select();*/
				   $this->assign("rzt",$rzt);
				   $this->assign("page",$show);
				   $this->assign("list",$list);
				   $this->assign('uidd', $uid);
				   $this->assign('ks', $ks);
				   $this->assign('jieshu', $jieshu);
				}
			}else{
                
				if($_REQUEST['xz']=='0'){
					   $sjc=60*60*24;
					   $token      =$_REQUEST['token'];
					   $uid      =$_REQUEST['uid'];
					   $ks   =$_REQUEST['statdate'];
					   $jieshu=$_REQUEST['enddate'];
					   $_SESSION['ks']=$ks;
					   $_SESSION['jieshu']=$ks;
					   $starttime=strtotime($_REQUEST['statdate']);
					   $lasttime =strtotime($_REQUEST['enddate']);
					   $lasttimenew=$lasttime+$sjc;
                       /*$rzt1=$customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->select();*/
					   $rzt1=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->select();
				       $num=count($rzt1);
					   $page = new Page($num, 20);
       
					   $show = $page->show();// 分页显示输出
					   // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
					    /*$rzt = $customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->limit($page->firstRow.','.$page->listRows)->select();*/
						$rzt=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->limit($page->firstRow.','.$page->listRows)->select();
						$this->assign('page', $show);
						$this->assign('rzt', $rzt);
						$this->assign('uid', $uid);
						$this->assign('uidd', $uid);
						$this->assign('ks', $ks);
						$this->assign('jieshu', $jieshu);
				  
					   //$this->assign("rzt",$rzt);
					   $this->assign("list",$list); 
				   }else{
				   $sjc=60*60*24;
				   $uid      =$_REQUEST['xz'];
				   $token      =$_REQUEST['token'];
				   $ks   =$_REQUEST['statdate'];
				   $jieshu=$_REQUEST['enddate'];
				    $_SESSION['ks']=$ks;
				    $_SESSION['jieshu']=$ks;
				   $starttime=strtotime($_REQUEST['statdate']);
				   $lasttime =strtotime($_REQUEST['enddate']);
				    $lasttimenew=$lasttime+$sjc;
				   
				  /* $rzt1=$customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("uid='$uid' and token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->select();*/
				  $rzt1=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.uid='$uid' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->select();
				   //print_r($customer_service_fans_visitors);
				    $num=count($rzt1);
				   $page = new Page($num, 20);
       
					$show = $page->show();// 分页显示输出
				    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                    /*$rzt=$customer_service_userfans_message->group("FROM_UNIXTIME(time,'%Y/%m/%d')")->where("uid='$uid' and token='$token' and time>'$starttime' and time<'$lasttimenew'")->order("time desc")->limit($page->firstRow.','.$page->listRows)->select();*/
					 $rzt=$customer_service_userfans_message->table('tp_customer_service_userfans_message a')->join('tp_customer_service_fans b on a.openid=b.openid')->join('tp_customer_service_users
 c on c.uid=a.uid')->group("FROM_UNIXTIME(a.time,'%Y/%m/%d')")->where("a.token='$token' and a.uid='$uid' and a.time>'$starttime' and a.time<'$lasttimenew'")->order("a.time desc")->limit($page->firstRow.','.$page->listRows)->select();
				   $this->assign("rzt",$rzt);
				   $this->assign("page",$show);
				   $this->assign("list",$list);
				   $this->assign('uidd', $uid);
				   $this->assign('ks', $ks);
				   $this->assign('jieshu', $jieshu);
				}
			
			}
			
	        $this->display();
	
	}

	//对话类型设置
	public function duihualeix(){
		   $customer_service_fans_visitors_type=M('customer_service_fans_visitors_type');
	       $token=$_GET['token'];
           $list=$customer_service_fans_visitors_type->where("token='$token'")->select();
		   $this->assign('list',$list);
	       $this->display(); 
	}
	//对话类型修改
	public function duihualeixxiugai(){
		    $customer_service_fans_visitors_type=M('customer_service_fans_visitors_type');
	        $token=$_REQUEST['token'];
	        $tid=$_REQUEST['tid'];
	        $name=$_REQUEST['nn'];
			//echo $tid.$token.$name;exit;
			$data['name']=$name;
            $list=$customer_service_fans_visitors_type->where("tid='$tid' and token='$token'")->save($data);
            if($list){
				echo '1';
			}else{
			    echo '2';
			}
	}
	//聊天记录全部导出
	public function quanbudc(){
		   
		   $customer_service_userfans_message=M('customer_service_userfans_message');
		   if($_REQUEST['item']==''){
			    $this->error('没有选中任何时间');exit;
		   }else{
		   //一天的时间戳
		    $xq=60*60*24;  
	        $shijian=$_REQUEST['item'];
	        $uid=$_REQUEST['uid'];
	        $token=$_REQUEST['token'];
			//echo $uid.'>>>>'.$token;exit;
			foreach($shijian as $k =>$v){
			       $arr[]= strtotime($v);
			}
			
           $max = array_search(max($arr), $arr);
           $min = array_search(min($arr), $arr);
	       //最大时间
		   $max1=$arr[$max];
		   //最大时间
		   $maxnew=$max1+$xq;
		   //最小时间
		   $min1=$arr[$min];
		   //echo date("Y-m-d",$min1);
		   //echo date("Y-m-d",$maxnew);
		   //exit;
		   $_SESSION['ks']=date("Y-m-d",$min1);
		   
		   $_SESSION['jieshu']=date("Y-m-d",$maxnew);
		   
		   if($uid=='0'){
			  // echo '1111';exit;
               $sql="select a.type as leix,a.time as atime,a.message as amessges,b.nickname as bnickname,c.name as aname from tp_customer_service_userfans_message a left join tp_customer_service_fans b on a.openid=b.openid left join tp_customer_service_users c on a.uid=c.uid where a.time>'$min1' and a.time<'$maxnew' and a.token='$token' order by a.time asc";
			   $list=$customer_service_userfans_message->query($sql);
			   //echo $sql;exit;
			   //print_r($list);exit;
		   }else{
               //echo '1111111';exit;
               $sql="select a.type as leix,a.time as atime,a.message as amessges,b.nickname as bnickname,c.name as aname from tp_customer_service_userfans_message a left join tp_customer_service_fans b on a.openid=b.openid left join tp_customer_service_users c on a.uid=c.uid where a.time>'$min1' and a.time<'$maxnew' and a.token='$token' and a.uid='$uid' order by a.time asc";
			   //echo $sql;exit;
			   //$sql="select * from tp_customer_service_userfans_message where uid='$uid' and time>'$min1' and time<'$maxnew' and token='$token' order by time asc";
			   
			   //$sql1="select * from tp_customer_service_userfans_message where uid='$uid' and time>'$min1' and time<'$maxnew' and token='$token' order by time asc";
			   //echo $sql;
               $list=$customer_service_userfans_message->query($sql);
			   //print_r($list);exit;
			   //echo $sql;exit;
			   //$list=$customer_service_userfans_message->where("uid='$uid' and time>'$min1' and time<'$maxnew' token='$token'")->order("time asc")->select();
               //print_r($list);exit;
		     }
		   }
		   
		   return $list;
	}
	//导出全部聊天记录调用上面方法
	public function daochuquanbu(){
	      $list=$this->quanbudc();
		  //print_r($list);exit;
          Header( "Content-type:   application/octet-stream "); 
		  Header( "Accept-Ranges:   bytes "); 
		  header( "Content-Disposition:   attachment;   filename=".$_SESSION['ks'].'-'.$_SESSION['jieshu'].".txt "); 
		  //header( "Content-Disposition:   attachment;   filename=".$_SESSION['ks'].'-'.$_SESSION['jieshu'].".txt"); 
		  header( "Expires:   0 "); 
		  header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 "); 
		  header( "Pragma:   public "); 
		  foreach($list as $k =>$v){
		         if($v['leix']=='1'){
                 
			
			         echo "时间:".date("Y-m-d H:i:s",$v['atime'])."\r\n";
			         echo "粉丝:".$v['bnickname']."\r\n";
					 echo $v['amessges']."\r\n";
		     

			   }else{
				      echo "时间:".date("Y-m-d H:i:s",$v['atime'])."\r\n";
					  echo "客服:".$v['aname']."\r\n";
					  echo $v['amessges']."\r\n";
				   
				 
			   }
		  }
		  unset($_SESSION['ks']);
	      unset($_SESSION['jieshu']);
		   
	}
	public function daochu(){
	  Header("Content-type: application/octet-stream");
      Header("Accept-Ranges: bytes"); 
	  //Header( "Content-type:application/octet-stream "); 
	  //Header( "Accept-Ranges:bytes "); 
	  header( "Content-Disposition:   attachment;   filename=".$_SESSION['ks'].'-'.$_SESSION['jieshu'].".txt "); 
	  header( "Expires:   0 "); 
	  header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 "); 
	  header( "Pragma:   public "); 
	  $userfansjl=$_SESSION['userfansjl'];
      //print_r($userfansjl);exit;
	  foreach($userfansjl as $k =>$v){
	       if($v['leix']=='1'){
                 
			      echo "时间:".date("Y-m-d H:i:s",$v['atime'])."\r\n";
				  echo "粉丝:".$v['bnickname']."\r\n";
				  echo $v['amessges']."\r\n";
		     

		   }else{
		          echo "时间:".date("Y-m-d H:i:s",$v['atime'])."\r\n";
				  echo "客服:".$v['aname']."\r\n";
				  echo $v['amessges']."\r\n";
		       
		     
		   }
	      
	  }
	  unset($_SESSION['userfansjl']);
	  unset($_SESSION['ks']);
	  unset($_SESSION['jieshu']);
	 
	}
	//ajax查询粉丝和客户通话记录
	public function ajaxthjl(){
		   $customer_service_userfans_message=M('customer_service_userfans_message');
	      
	       $uid=$_REQUEST['uid'];
	       $token=$_REQUEST['token'];
	       $lasttime=$_REQUEST['lasttime'];
		   //echo $uid.'----'.$token.'----'.$lasttime;
		   //exit;
		   //一天的时间戳
		   $xq=60*60*24;        	
		   $starttime=strtotime($lasttime);
		 
		   
		   //清晨的加上一天的时间戳=一天的时间
		   $zhsj=$starttime+$xq;
		   //echo $starttime.'--'.$zhsj;exit;
		   if($uid=='0'){
              $sql="select a.type as leix,a.time as atime,a.message as amessges,b.nickname as bnickname,c.name as aname from tp_customer_service_userfans_message a left join tp_customer_service_fans b on a.openid=b.openid left join tp_customer_service_users c on a.uid=c.uid where a.time>'$starttime' and a.time<'$zhsj' and a.token='$token' order by a.time asc";
			   $userlist=$customer_service_userfans_message->query($sql);
			   //print_r($userlist);exit;
			   //echo $uid;exit;
		      //$userlist=$customer_service_userfans_message->where("token='$token' and '$starttime'<time and time<'$zhsj'")->order("time asc")->select();
			  //print_r($userlist);exit;
		   }else{
			   $sql="select a.type as leix,a.time as atime,a.message as amessges,b.nickname as bnickname,c.name as aname from tp_customer_service_userfans_message a left join tp_customer_service_fans b on a.openid=b.openid left join tp_customer_service_users c on a.uid=c.uid where a.time>'$starttime' and a.time<'$zhsj' and a.token='$token' and a.uid='$uid' order by a.time asc";
			   //echo $sql;exit;
			   $userlist=$customer_service_userfans_message->query($sql);
		      //$userlist=$customer_service_userfans_message->where("token='$token' and uid='$uid' and '$starttime'<time and time<'$zhsj'")->order("time asc")->select();
		      //print_r($userlist);exit;
		   }
           
		   $_SESSION['userfansjl']=$userlist;
		  
		   
		   if($userlist){
			    //print_r($userlist);exit;
			    foreach($userlist as $key =>$v){
					     
					    $time=date("Y-m-d H:i:s",$v['atime']);
						$info.="<div class='seLeft' style='display:block;clear:both'>";
						$info.="<span class='seLeft'>日期：".$time."</span>";
						$info.="</div>";
						 
						if($v['leix']=='1'){
							  //echo $v['leix'];exit;
								  
						       $info.=" <div class='common' style='margin-bottom:20px;display:block;clear:both'>";
							   $info.="<div class='seLeft' style='display:block;clear:both'>粉丝：".$v['bnickname']."</div>";
							   //$info.="<br>";
						       $info.="<span class='seLeft' style='display:block;clear:both'>".$v['amessges']."</span>";
						       $info.="</div>";
							  
							   //echo $info;exit;
					   }else{  
						       $info.=" <div class='common' style='margin-bottom:20px;display:block;clear:both'>";
							   $info.="<div class='seLeft' style='display:block;clear:both'>客服：".$v['aname']."</div>";
						       //$info.="<br>";
							   $info.="<span class='seLeft' style='display:block;clear:both'>".$v['amessges']."</span>";
                               $info.="</div>";
							  // echo $info;exit;
					    } 
					   
				   
				}
				 //echo 'wcao';exit;
				  //echo $info;exit;
				  echo $info;
		   }else{
		          echo '2';
		   }

		 
	         
	       
	}

	//查询客服和粉丝通话记录
	public function thjl(){
	       $token=$_GET['token'];
		   $customer_service_users_message=M('customer_service_users_message');
	       $customer_service_fans_message=M('customer_service_fans_message');
           $userlist=$customer_service_users_message->where("token='rggfsk1394161441' and uid='3'")->select();
           $fanslist=$customer_service_fans_message->where("token='rggfsk1394161441' and uid='3'")->select();
		  
		   foreach ($userlist as $key => $List) {
			    
				$userlist[$key]['type'] = 1;
			}
		   foreach ($fanslist as $key => $List) {
				$fanslist[$key]['type'] = 2;
			}
		  $newarray = array_merge($userlist, $fanslist);
		   /*print_r($userlist);
		   echo "<br>";
		   print_r($fanslist);
		   echo "<br>";
		   print_r($newarray);
		   */
		   $enddata = $fanslist ? $this->array2sort($newarray, 'time') : $userlist;
		   //print_r($enddata);
		   //一天的时间戳
		   $xq=60*60*24;
		   $starttime=strtotime('20140405');
		   //清晨的加上一天的时间戳=一天的时间
		   $zhsj=$starttime+$xq;

          $this->success('操作成功',U('Kefu/fkgl',array('token'=> $token)));
	      
	}


	public function array2sort($a, $sort, $d = '') {
        $num = count($a);
        if (!$d) {
            for ($i = 0; $i < $num; $i++) {
                for ($j = 0; $j < $num - 1; $j++) {
                    if ($a[$j][$sort] > $a[$j + 1][$sort]) {
                        foreach ($a[$j] as $key => $temp) {
                            $t = $a[$j + 1][$key];
                            $a[$j + 1][$key] = $a[$j][$key];
                            $a[$j][$key] = $t;
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < $num; $i++) {
                for ($j = 0; $j < $num - 1; $j++) {
                    if ($a[$j][$sort] < $a[$j + 1][$sort]) {
                        foreach ($a[$j] as $key => $temp) {
                            $t = $a[$j + 1][$key];
                            $a[$j + 1][$key] = $a[$j][$key];
                            $a[$j][$key] = $t;
                        }
                    }
                }
            }
        }
        return $a;
    }

	//访客管理
	public function fkgl(){
		   
	       $token=$_GET['token'];
		   $customer_service_fans_group=M('customer_service_fans_group');
		   $rzt=$customer_service_fans_group->where("token='$token' and gid !='11'")->select();
		   //print_r($list);
            $count=count($rzt);
		
			$page = new Page($count, 20);
		   
			$show = $page->show();// 分页显示输出
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $customer_service_fans_group->where("token='$token' and gid!='11'")->order('status asc  ')->limit($page->firstRow.','.$page->listRows)->select();
			//print_r($list);
		   $this->assign('page', $show);
		   $this->assign('list', $list);
	       $this->display();
	}
	//访客分组
	public function fkfzadd(){
	    $customer_service_fans_group=M('customer_service_fans_group');
	      if($_POST){
			 
			  $token=$_POST['token'];
			  $data['token']=$_POST['token'];
			  $data['groupname']=$_POST['shuxing'];
			  $rzt=$customer_service_fans_group->add($data);
              if($rzt){
				     $data1['status']=$rzt;
				     $customer_service_fans_group->where("gid='$rzt'")->save($data1);
                     $this->success('操作成功',U('Kefu/fkgl',array('token'=> $token)));
			  }else{
			         $this->error('操作成功',U('Kefu/fkgl',array('token'=> $token)));
			  }
		  }
	}
	//修改分组
	public function updatefkfz(){
	
	       $customer_service_fans_group=M('customer_service_fans_group');
           $gid=$_REQUEST['gid'];
           $token=$_REQUEST['token'];
           $groupname=$_REQUEST['groupname'];
		   //echo $gid.'>>>>'.$token.'>>>>'.$groupname;exit;
		   $data['groupname']=$groupname;
           $rzt=$customer_service_fans_group->where("token='$token' and gid='$gid'")->save($data);
		   if($rzt){
			    echo '1';exit;
		   }else{
		        echo '2';exit;
		   }
	}
   //向上移动分组
   public function upp(){
	       $customer_service_fans_group=M('customer_service_fans_group');
           //当前的gid
		   $gid=$_REQUEST['gid'];
           $token=$_REQUEST['token'];
           //当前的状态id
		   $status=$_REQUEST['status'];
           //上一个的状态id
		   $statusid=$_REQUEST['statusid'];
           //上一个的gid
		   $gggid=$_REQUEST['gggid'];
		  
		    //echo $gid.'>>>>'.$token.'>>>>'.$status.'>>'.$status1.'>>>>'.$gggid.'>>>'.$statusid;exit;
		   $data['status']=$statusid;
		   $data1['status']=$status;
		   $rzt=$customer_service_fans_group->where("token='$token' and gid='$gid'")->save($data);
		   $rrr=$customer_service_fans_group->where("token='$token' and gid='$gggid'")->save($data1);
		   if($rzt&&$rrr){
			 
			 echo '1';exit;
		   }else{
		     echo '2';exit;
		   }
           
       


   }
   //向下移动分组
   public function downnn(){
           $customer_service_fans_group=M('customer_service_fans_group');
           //当前的gid
		   $gid=$_REQUEST['gid'];
           $token=$_REQUEST['token'];
           //当前的状态id
		   $status=$_REQUEST['status'];
           //上一个的状态id
		   $statusid=$_REQUEST['statusid'];
           //上一个的gid
		   $gggid=$_REQUEST['gggid'];
		  
		    //echo $gid.'>>>>'.$token.'>>>>'.$status.'>>'.$status1.'>>>>'.$gggid.'>>>'.$statusid;exit;
		   $data['status']=$statusid;
		   $data1['status']=$status;
		   $customer_service_fans_group->where("token='$token' and gid='$gid'")->save($data);
		   $customer_service_fans_group->where("token='$token' and gid='$gggid'")->save($data1);
       
   }

   public function sort(){
		$sort=htmlspecialchars_decode($_POST['sort']);
		$sort=json_decode($sort,true);
		for($i=0;$i<count($sort)-1;$i++){
			$data=array('status'=>$sort[$i]['sort']);
			M('customer_service_fans_group')->where(array('gid'=>$sort[$i]['id']))->save($data);
		}
	}
	//删除访客分组
	public function fkfzdel(){
	       $token=$_GET['token'];
           $customer_service_fans_group=M('customer_service_fans_group');
           $customer_service_fans=M('customer_service_fans');
           $gid = $this->_get('gid', 'intval');       
		   $data['gid']='11';
		   if($gid=='11'){
			     $this->error('不能被删除',U('Kefu/fkgl',array('token'=> $token)));
		   }else{
		  
           if ($customer_service_fans_group->where("token='$token' and gid='$gid'")->delete()) {  
               $customer_service_fans->where("token='$token' and gid='$gid'")->save($data);
			   $this->success('操作成功',U('Kefu/fkgl',array('token'=> $token)));
           } else {
             $this->error('操作失败',U('Kefu/fkgl',array('token'=> $token)));
           }
		  }
	   
	}

	//访客名片导出
	public function fkdc(){
		   $token=$_REQUEST['token'];
	       $customer_service_users=M('customer_service_users');//客服表
		   $customer_service_fans_group=M('customer_service_fans_group');//分组表
		   $customer_service_fans_visitors=M('customer_service_fans_visitors');//联系记录表
		   $customer_service_fans=M('customer_service_fans');//粉丝表

	       $userlist=$customer_service_users->where("token='$token'")->select();
	       $fansgrouplist=$customer_service_fans_group->where("token='$token'")->select();
		   if($_POST){
			   if($_REQUEST['uid']=='0'&&$_REQUEST['gid']=='0'){
				     $token=$_REQUEST['token'];
				     $uid=$_REQUEST['uid'];
				     $gid=$_REQUEST['gid'];
					 $_SESSION['mingzi']="所有";
					 $_SESSION['fenzu']="所有";
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and c.type='1'")->select();
                       //$sql="SELECT * FROM tp_customer_service_fans a LEFT JOIN tp_customer_service_fans_group b ON a.gid=b.gid LEFT JOIN tp_customer_service_userfans_message c ON a.openid=c.openid LEFT JOIN tp_customer_service_users d ON c.uid=d.uid WHERE a.token='$token'";
					   //print_r($customer_service_fans->query($sql));
                       //exit;
                     	//print_r($groupfans1);exit;
                      
					   //print_r($groupfans);
					   $num=count($groupfans1);
					   $page = new Page($num,30);
		   
						$show = $page->show();// 分页显示输出
						// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
					    $groupfans =$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();                                                $_SESSION['groupfans']=$groupfans;
						//print_r($groupfans);exit;
					   $this->assign('page', $show);
					   $this->assign('groupfans', $groupfans);
					   $this->assign('uidd', $uid);
					   $this->assign('gidd', $gid);
					
					
					
			   }elseif($_REQUEST['uid']!='0'&&$_REQUEST['gid']=='0'){
				     $uid=$_REQUEST['uid'];
					 $token=$_REQUEST['token'];
					 $gid=$_REQUEST['gid'];
					 $kefumz=$customer_service_users->where("uid='$uid' and token='$token'")->find();
					 $_SESSION['fenzu']="所有";
					 $_SESSION['mingzi']=$kefumz['name'];
					/*$groupfans1=$customer_service_users->table('tp_customer_service_users a')->join('tp_customer_service_userfans_message b on a.uid=b.uid')->join('tp_customer_service_fans
 c on c.openid=b.openid')->where("a.token='$token' and a.uid='$uid'")->select();
                    print_r($groupfans1);exit;*/
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_userfans_message b on a.openid=b.openid')->join('tp_customer_service_users
 c on b.uid=c.uid')->where("a.token='$token' and c.uid='$uid' and b.type='1'")->select();
	                 $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_userfans_message b on a.openid=b.openid')->join('tp_customer_service_users
 c on b.uid=c.uid')->where("a.token='$token' and c.uid='$uid' and b.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                      $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;

					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
			   }elseif($_REQUEST['uid']=='0'&&$_REQUEST['gid']!='0'){
			         $gid=$_REQUEST['gid'];
			         $uid=$_REQUEST['uid'];
					 $token=$_REQUEST['token'];
				     $fenzumz=$customer_service_fans_group->where("gid='$gid' and token='$token'")->find();
					 $_SESSION['fenzu']=$fenzumz['groupname'];
					 $_SESSION['mingzi']="所有";
				
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and c.type='1'")->select();
                     //print_r($groupfans1);exit;
	                 
					 $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                     $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;
					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
			   }else{
				    $uid=$_REQUEST['uid'];
			        $gid=$_REQUEST['gid'];
			        $token=$_REQUEST['token'];
					$fenzumz=$customer_service_fans_group->where("gid='$gid' and token='$token'")->find();
					$kefumz=$customer_service_users->where("uid='$uid' and token='$token'")->find();
					 $_SESSION['fenzu']=$fenzumz['groupname'];
					 $_SESSION['mingzi']=$kefumz['name'];
					$groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and d.uid='$uid' and c.type='1'")->select();
                     $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and d.uid='$uid' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                    $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;
					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
					// print_r($groupfans1);exit;
				    
			   }
		   }else{
                 if($_REQUEST['uid']=='0'&&$_REQUEST['gid']=='0'){
				     $token=$_REQUEST['token'];
					 $uid=$_REQUEST['uid'];
			         $gid=$_REQUEST['gid'];
					 $_SESSION['mingzi']="所有";
					 $_SESSION['fenzu']="所有";
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and c.type='1'")->select();
                       //$sql="SELECT * FROM tp_customer_service_fans a LEFT JOIN tp_customer_service_fans_group b ON a.gid=b.gid LEFT JOIN tp_customer_service_userfans_message c ON a.openid=c.openid LEFT JOIN tp_customer_service_users d ON c.uid=d.uid WHERE a.token='$token'";
					   //print_r($customer_service_fans->query($sql));
                       //exit;
                     	//print_r($groupfans1);exit;
                      
					   //print_r($groupfans);
					   $num=count($groupfans1);
					   $page = new Page($num,30);
		   
						$show = $page->show();// 分页显示输出
						// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
					    $groupfans =$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();                                                $_SESSION['groupfans']=$groupfans;
						//print_r($groupfans);exit;
					   $this->assign('page', $show);
					   $this->assign('groupfans', $groupfans);
					   $this->assign('uidd', $uid);
					   $this->assign('gidd', $gid);
					
					
					
			   }elseif($_REQUEST['uid']!='0'&&$_REQUEST['gid']=='0'){
				     $uid=$_REQUEST['uid'];
					
			         $gid=$_REQUEST['gid'];
					 $token=$_REQUEST['token'];
					 $kefumz=$customer_service_users->where("uid='$uid' and token='$token'")->find();
					 $_SESSION['fenzu']="所有";
					 $_SESSION['mingzi']=$kefumz['name'];
					 /*$groupfans1=$customer_service_users->table('tp_customer_service_users a')->join('tp_customer_service_userfans_message b on a.uid=b.uid')->join('tp_customer_service_fans
 c on c.openid=b.openid')->where("a.token='$token' and a.uid='$uid'")->select();
                    print_r($groupfans1);exit;*/
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_userfans_message b on a.openid=b.openid')->join('tp_customer_service_users
 c on b.uid=c.uid')->where("a.token='$token' and c.uid='$uid' and b.type='1'")->select();
	                 $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_userfans_message b on a.openid=b.openid')->join('tp_customer_service_users
 c on b.uid=c.uid')->where("a.token='$token' and c.uid='$uid' and b.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                      $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;
					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
			   }elseif($_REQUEST['uid']=='0'&&$_REQUEST['gid']!='0'){
			         $gid=$_REQUEST['gid'];
			         $uid=$_REQUEST['uid'];
					 $token=$_REQUEST['token'];
				     $fenzumz=$customer_service_fans_group->where("gid='$gid' and token='$token'")->find();
					 $_SESSION['fenzu']=$fenzumz['groupname'];
					 $_SESSION['mingzi']="所有";
				
					 $groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and c.type='1'")->select();
                     //print_r($groupfans1);exit;
	                 
					 $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                     $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;
					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
			   }else{
				    $uid=$_REQUEST['uid'];
			        $gid=$_REQUEST['gid'];
			        $token=$_REQUEST['token'];
					$fenzumz=$customer_service_fans_group->where("gid='$gid' and token='$token'")->find();
					$kefumz=$customer_service_users->where("uid='$uid' and token='$token'")->find();
					 $_SESSION['fenzu']=$fenzumz['groupname'];
					 $_SESSION['mingzi']=$kefumz['name'];
					$groupfans1=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and d.uid='$uid' and c.type='1'")->select();
                     $num=count($groupfans1);
					 $page = new Page($num,30);   
					 $show = $page->show();// 分页显示输出
                     $groupfans=$customer_service_fans->table('tp_customer_service_fans a')->join('tp_customer_service_fans_group b on a.gid=b.gid')->join('tp_customer_service_userfans_message c on a.openid=c.openid')->join('tp_customer_service_users
 d on c.uid=d.uid')->where("a.token='$token' and b.gid='$gid' and d.uid='$uid' and c.type='1'")->limit($page->firstRow.','.$page->listRows)->select();
                    $_SESSION['groupfans']=$groupfans;   
                     //print_r($groupfans1);exit;
					$this->assign('page', $show);
					$this->assign('groupfans', $groupfans);
					$this->assign('uidd', $uid);
					$this->assign('gidd', $gid);
					// print_r($groupfans1);exit;
				    
			   } 
		   
		   }
		   //print_r($userlist);
		   $this->assign("userlist",$userlist);
		   $this->assign("fansgrouplist",$fansgrouplist);   
		   $this->display();
	}

	//访客导出
    public function fkdaochu(){
		    error_reporting(E_ALL);

			date_default_timezone_set('Europe/London');
            $groupfans=$_SESSION['groupfans'];
			/** Include PHPExcel */
			require_once '/Classes/PHPExcel.php';


			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
										 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
						 ->setCellValue('A1', '时间')
						 ->setCellValue('B1', '昵称')
					     ->setCellValue('C1', '地区')
						 ->setCellValue('D1', '分组')
						 ->setCellValue('E1', '备注')
						 ->setCellValue('F1', '客服名字');

			// Miscellaneous glyphs, UTF-8
			
				 for($i=0;$i<count($groupfans);$i++){
					 $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.($i+2), date("Y-m-d H:i:s",$groupfans[$i]['time']))
                        ->setCellValue('B'.($i+2), $groupfans[$i]['nickname'])
                        ->setCellValue('C'.($i+2), $groupfans[$i]['province'])
                        ->setCellValue('D'.($i+2), $groupfans[$i]['groupname'])
                        ->setCellValue('E'.($i+2), $groupfans[$i]['remark'])
                        ->setCellValue('F'.($i+2), $groupfans[$i]['name']);
                        
				         
			     }
						

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Simple');


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename=".$_SESSION['mingzi'].'-'.$_SESSION['fenzu'].".xls");
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');

					   
		   unset($_SESSION['groupfans']);
		   unset($_SESSION['mingzi']);
		   unset($_SESSION['fenzu']);
	
	
	}

}
