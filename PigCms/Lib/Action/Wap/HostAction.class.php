<?php
class HostAction extends BaseAction{
    public function index(){
        //$agent = $_SERVER['HTTP_USER_AGENT']; 
        //if(!strpos($agent,"MicroMessenger")) {
          //  echo '此功能只能在微信浏览器中使用';exit;
        //}
        $token      = $this->_get('token'); 
        $hid         = $this->_get('hid'); 
        $wecha_id         = $this->_get('wecha_id'); 
        $where      = array('token'=>$token,'hid'=>$hid);             
        $list_add     = M('Host_list_add')->where($where)->select();   //房间类型查询
        $hostset =  M('Host')->where(array('token'=>$token,'id'=>$hid))->find();
		$host_order=M('host_order');//订单表
		//$list1=$host_order->where("token='$token' and wecha_id='$wecha_id'  and fjid='$fjid' and hid='$hid'")->select();
		//统计订单数量
		$list1=$host_order->where("token='$token' and wecha_id='$wecha_id' and hid='$hid'")->select();
	    $num=count($list1);
        $this->assign('list',$list_add);
        $this->assign('num',$num);
        //company info
        //$company_db=M('Company');
        //$thisCompany=$company_db->where(array('token'=>$token,'isbranch'=>0))->find();
        //$hostset['address']=$thisCompany['address'];
		//print_r($hostset['jingdu']);
        $this->assign('set',$hostset);
      //  $this->assign('isAndroid',isAndroid());
        $this->display();
    }
    
    //商家详情
    public function online(){
        //$agent = $_SERVER['HTTP_USER_AGENT']; 
        //if(!strpos($agent,"MicroMessenger")) {
          //  echo '此功能只能在微信浏览器中使用';exit;
        //}
        $token      = $this->_get('token'); 
        if(empty($token))  $this->error('非法操作');

        $where      = array('token'=>$token); 
        $data=M('Host');
        //$count      = $data->where( $where )->count();
        //$Page       = new Page($count,7);
        //$show       = $Page->show();
        //$list = $data->where( $where )->limit($Page->firstRow.','.$Page->listRows)->select();

        //$this->assign('list',$list);
        //$this->assign('show',$show);
        //
        $hid         = $this->_get('hid'); 
        $hostset =  M('Host')->where(array('token'=>$token,'id'=>$hid))->find();
        $this->assign('set',$hostset);
      
        $this->display();
        
    }

	
   
	
    public function lists(){
       //$agent = $_SERVER['HTTP_USER_AGENT']; 
        //if(!strpos($agent,"MicroMessenger")) {
          //  echo '此功能只能在微信浏览器中使用';exit;
        //}
       $hostinput=M('host_input');
       $id    = $this->_get('id');
       $token = $this->_get('token');
       $hid = $this->_get('hid');
       $wecha_id = $this->_get('wecha_id');
       $userinfo = M('userinfo')->where(array('wecha_id'=>$wecha_id,'token'=>$token))->find();

       $host = M('Host')->where(array('id'=>$hid,'token'=>$token))->find();
       $where = array('id'=>$id,'token'=>$token);
       $types = M('Host_list_add')->where($where)->find();
	   $inputlist=$hostinput->where("token='$token' and b_fid='$hid'")->select();
       //print_r($inputlist); 
	   //dump($types);
       $this->assign('types',$types);
       $save_monery = $types['price'] - $types['yhprice']; 
       $this->assign('userinfo',$userinfo);
       $this->assign('saves',$save_monery );
       $this->assign('inputlist',$inputlist );//类型
       $this->assign('host',$host);
        
        $this->display();

    }

    public function  orderadd(){
	       $host_order=M('host_order');
	       $host_list_add=M('host_list_add');
	       $host_order_fenlei=M('host_order_fenlei');
		   $id    = $_POST['fjid'];
           $token = $_POST['token'];
           $wecha_id = $_POST['wecha_id'];
           $hid = $_POST['hid'];
		   $where = array('id'=>$id,'token'=>$token);
           $types = M('Host_list_add')->where($where)->find();
		   //var_dump($types['fjnum']);exit;
		   if($types['fjnum']=='0'){
                 echo '<script>alert("房间已经没有了");</script>';exit;	
			    //$this->redirect("/index.php?g=Wap&m=Host&a=index&wecha_id=$wecha_id&token=$token&hid=$hid");
					      
		   }else{
	       if($_POST){
		        //print_r($_REQUEST);exit;
				$wecha_id           =  $this->_post('wecha_id');
				$data['wecha_id']   =  $this->_post('wecha_id');
				$token              = $this->_post('token');
				$data['token']      = $this->_post('token');
				$data['fjid']       = $this->_post('fjid');
				$fjid               = $this->_post('fjid');
				$data['book_people']  =  $this->_post('book_people'); 
				$data['tel']        = $this->_post('tel'); 
				$data['check_in']   = strtotime($this->_post('check_in')); 
				$data['check_out']  = strtotime($this->_post('check_out')); 
				$data['remarks']    =  $this->_post('remarks');  
				$data['book_num']   = $this->_post('book_num'); 
				$book_num           = $this->_post('book_num'); 
				$data['price']   = $this->_post('price'); 
				$data['book_time']  = time();           
				//$id                 = $this->_post('id');
				$data['room_type'] = $this->_post('room_type'); 
				$data['order_status'] = 3 ;
				$data['hid'] = $this->_post('hid');
				$hid         = $this->_post('hid');
                          $data['sn']=mt_rand(A,Z).time().mt_rand(1000,9999);
                $qry=$host_order->add($data);
				if($qry){
				     $fjxx=$host_list_add->where("id='$fjid' and token='$token' and hid='$hid'")->find();//查询单个房间信息
					 $fjnum=$fjxx['fjnum'];
                     $data1['fjnum']=$fjnum-$book_num;
					 $host_list_add->where("id='$fjid' and token='$token' and hid='$hid'")->save($data1);//修改单个房间剩余房间数量
					 for($i=0;$i<count($_REQUEST['leixname']);$i++){
						     //print_r($_REQUEST);exit;
					          $data2['leixname']  =$_REQUEST['leixname'][$i];
						      $data2['leix']=$_REQUEST['leix'][$i];
						      $data2['token']=$_REQUEST['token'];
						      $data2['wecha_id']=$_REQUEST['wecha_id'];
						      $data2['order_fid']=$qry;
                             $host_order_fenlei->add($data2);
					 }
					  $this->redirect("/index.php?g=Wap&m=Host&a=myorder&wecha_id=$wecha_id&token=$token&orderid=$qry&hid=$hid&fjid=$fjid");

				}else{
				      echo '<script>alert("预定失败");</script>';
					  exit;	    
				}
		   }

		   }
	} 

	//订单查询
	public function myorder(){
	       $fjid     =$_GET['fjid'];//预定的那个房间的id
	       $hid      =$_GET['hid'];//那个酒店的id
	       $orderid  =$_GET['orderid'];//生成订单id
	       $token    =$_GET['token'];
	       $wecha_id =$_GET['wecha_id'];
		   $host=M('host');//酒店信息表
		   $host_order=M('host_order');//订单表
	       $host_order_fenlei=M('host_order_fenlei');//订单分类信息
		   //$list1=$host_order->where("token='$token' and wecha_id='$wecha_id'  and fjid='$fjid' and hid='$hid'")->select();
		   //查询酒店信息
		   $hostlist=$host->where("token='$token' and id='$hid'")->find();
		   $list1=$host_order->where("token='$token' and wecha_id='$wecha_id' and hid='$hid'")->select();
	       import("@.ORG.Page");
		   $num=count($list1);
		  
		  
		  $Page       = new Page($num,4);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  //$list =$host_order->where("token='$token' and wecha_id='$wecha_id' and fjid='$fjid' and hid='$hid'")->order('book_time desc  ')->limit($Page->firstRow.','.$Page->listRows)->select(); 
		  $list =$host_order->where("token='$token' and wecha_id='$wecha_id' and hid='$hid'")->order('book_time desc  ')->limit($Page->firstRow.','.$Page->listRows)->select(); 
		  //print_r($list);
          $this->assign('hostlist',$hostlist);// 酒店信息
          $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出
	      //var_dump($list); 
          $this->display();
	}

    //取消订单操作
	public function quxiaoorder(){
		    $hostorder=M("host_order");
	        $wecha_id=$_REQUEST['wecha_id'];
		    $token=$_REQUEST['token'];
		    $hid=$_REQUEST['hid'];
		    $orderid=$_REQUEST['orderid']; 
		    //echo $wecha_id.$token.$hid.$orderid;
            $data['order_status']='4';
			$qry=$hostorder->where("id='$orderid' and token='$token' and wecha_id='$wecha_id' and hid='$hid'")->save($data);
			//echo $qry;exit;
			if($qry){
				echo '1';
			}else{
			    echo '2';  
			}
			exit;
			//
	
	
	}
    
    //在线预定
    public function book(){ 
        $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")) {
            echo '此功能只能在微信浏览器中使用';exit;
        }
        if($_POST['action'] == 'book'){           

            $data['wecha_id']  =  $this->_post('wecha_id');
            $data['book_people']  =  $this->_post('truename'); 
            $data['remarks']   =  $this->_post('remarks');  
            $data['tel']       = $this->_post('tel');  
            $data['book_num']      = $this->_post('nums'); 
            $data['book_time']  = strtotime($this->_post('dateline'));           
            $id       = $this->_post('id');
            $data['room_type'] = $this->_post('roomtype'); 
            $data['order_status'] = 3 ;
            $data['hid'] = $this->_get('hid');
            $data['token'] = $this->_get('token');

            $price = M('Host_list_add')->where(array('id'=>$id,'token'=>$data['token'],'hid'=>$data['hid']))->find();

            $data['price'] = $price['yhprice'] * $data['book_num'];
                    
          
            $order = M('Host_order')->data($data)->add();    

            if($order){
                echo'{"success":1,"msg":"恭喜,预定成功。"}';
            }else{
                echo'{"success":0,"msg":"请从新预定。"}';
            }            
            exit;
        }    
            
        
    }
}
    
?>