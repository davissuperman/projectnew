<?php
/**
 * 微餐饮点菜类
 * add by miaoyunpeng 2014/3/22
 */
class DiancaiAction extends UserAction
{
	
    
	public function _initialize() {
		
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		//var_dump($token_open);exit;
		if(!strpos($token_open['queryname'],'Diancai')){
            	//$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
		//$this->selfform_model=M('Selfform');
		//$this->selfform_input_model=M('Selfform_input');
		//$this->selfform_value_model=M('Selfform_value');
		//$this->token=session('token');
		//$this->assign('token',$this->token);
		//$this->assign('module','Selfform');
	}
	/**
	 * 店铺管理
	 */
	public function index(){
		   $token=$_GET['token'];
		   $diancai=M('diancai');
		   $qry=$diancai->where("token='$token'")->select();
		   $num=count($qry);
		   $Page       = new Page($num,10);
	       $show       = $Page->show();
           $list = $diancai->where("token='$token'")->limit($Page->firstRow.','.$Page->listRows)->select(); 
		   $this->assign('page',$show);		
		   $this->assign('list',$list);
		   $this->display();
	}

	//添加菜品类型信息
	public function add(){
	       $diancai=M('diancai');
	       $diancai_input=M('diancai_input');
	       $keyword=M('keyword');
		   if($_POST){
               //print_r($_REQUEST);exit;
			   $token          =$_POST['token'];
			   $data['token']=$_POST['token'];
			   $data['keyword']=$_POST['keyword'];
			   $data['title']  =$_POST['title'];
			   $data['address']=$_POST['address'];
			   $data['tel']    =$_POST['tel'];
			   $data['index_pic']=$_POST['index_pic'];
			   $data['headpic']=$_POST['headpic'];
			   $data['info']=$_POST['info'];
			   $data['gsgonggao']=$_POST['gongsigonggao'];
			   $data['dituaddress']=$_POST['dituaddress'];
			   $data['jingdu']=$_POST['jingdu'];
			   $data['weidu']=$_POST['weidu'];
			   $data['creattime']=time();
			   
			   $qry=$diancai->add($data);
               if($qry){
				    for($i=0;$i<count($_REQUEST['zdyname']);$i++){
					  
						$data2['zdynr']  =$_REQUEST['zdynr'][$i];
						$data2['zdyname']=$_REQUEST['zdyname'][$i];
						$data2['d_fid']=$qry;
						$data2['token']=$token;
						$diancai_input->add($data2);
					}
				    $data1['keyword']=$_POST['keyword'];
				    $data1['pid']=$qry;
				    $data1['token']=$_POST['token'];
				    $data1['module']='Diancai';
					$keyword->add($data1);
                    $this->success('操作成功',U('Diancai/index',array('token'=>$token)));
			   }else{
                      $this->error('操作失败');
			   }
		   
		   }else{  
	       $this->display();

		   }
	}

    //首页回复信息修改
	public function set(){
	       $id=$_GET['id'];
           $token=$_GET['token']; 
		   $diancai=M('diancai');
		   $diancai_input=M('diancai_input');
		   $keyword=M('keyword');
		   if($_POST){
               
               $id=$_POST['id'];
			   $token=$_POST['token'];
			   $data['token']=$_POST['token'];
			   $data['keyword']=$_POST['keyword'];
			   $data['title']  =$_POST['title'];
			   $data['address']=$_POST['address'];
			   $data['tel']    =$_POST['tel'];
			   $data['index_pic']=$_POST['index_pic'];
			   $data['headpic']=$_POST['headpic'];
			   $data['info']=$_POST['info'];
			   $data['gsgonggao']=$_POST['gongsigonggao'];
			   $data['dituaddress']=$_POST['dituaddress'];
			   $data['jingdu']=$_POST['jingdu'];
			   $data['weidu']=$_POST['weidu'];
			   $data['creattime']=time();
			   
			   $qry=$diancai->where("token='$token' and id='$id'")->save($data);
               if($qry){
				    //如果有新的添加进来就先删除数据库记录
					 $diancai_input->where("token='$token' and d_fid='$id'")->delete();
				    //之后在进行循环添加
					 for($i=0;$i<count($_REQUEST['zdyname']);$i++){
					        //echo '1111111';exit; 
							$data3['zdynr']  =$_REQUEST['zdynr'][$i];
							$data3['zdyname']=$_REQUEST['zdyname'][$i];
							
							$data3['d_fid']  =$id;
							$data3['token']  =$token;
							//$inputid=$_REQUEST['inputid'][$i];
							//$data3['id']=$_REQUEST['inputid'][$i];
							$diancai_input->add($data3);
						   //print_r($data2);
					 }
				 
				  
					$data1['keyword']=$_POST['keyword'];
				    $data1['pid']=$id;
				    $data1['token']=$_POST['token'];
				    $data1['module']='Diancai';
					//print_R($data1);exit;
					$keyword->where("pid='$id' and token='$token'")->save($data1);
			        $this->success('修改成功',U('Diancai/index',array('token'=>$token)));

			   }else{
			        $this->error('修改失败');
			   
			   }
			   
					 
		   }else{ 
           $set=$diancai->where("id='$id' and token='$token'")->find();
		   //print_r($set);
		   //echo '<br>';
		   $list=$diancai_input->where("token='$token' and d_fid='$id'")->select();
		   //print_r($list);exit;
		   $this->assign("set",$set);
		   $this->assign("list",$list);
		   $this->display();
		   }
	}


	//删除克隆添加的分类
	public function delklfenl(){
	        $token=$_REQUEST['token'];
	        $fid=$_REQUEST['fid'];
	        $zjid=$_REQUEST['zjid'];
	        //echo $zjid.'...'.$token.'....'.$fid;  exit;
			$diancai_input=M('diancai_input'); 
            $qry=$diancai_input->where("d_fid='$fid' and token='$token' and id='$zjid'")->delete();
			if($qry){
				echo '1';exit;
              
			}else{
				echo '2';exit;
			   
			}
            
	}
	//回复信息 删除
	public function index_del(){
	       $id=$_GET['id'];
           $token=$_GET['token']; 
		   $diancai=M('diancai');
		   
	       $diancai_input=M('diancai_input');
	       $rzt=$diancai->where("token='$token' and id='$id'")->delete();
          if($rzt){
			   
	           $diancai_input->where("token='$token' and d_fid='$id'")->delete(); 
               $this->success('删除成功',U('Diancai/index',array('token'=> $token)));
		  }else{
                $this->error('删除失败',U('Diancai/index',array('token'=> $token)));     
		  }  
	        
	}

	/**
	 * 菜品分类管理
	 */
	public function cpfllist(){
           $token=$_GET['token'];
		   $diancai_fenlei=M('diancai_fenlei');
           $qry=$diancai_fenlei->where("token='$token'")->select();
		   $num=count($qry);
		   $Page       = new Page($num,10);
	       $show       = $Page->show();
           $list = $diancai_fenlei->where("token='$token'")->limit($Page->firstRow.','.$Page->listRows)->select(); 
		   
		   $this->assign('page',$show);		
		   $this->assign('list',$list);
		   $this->display();
	}
	//添加菜品分类
	public function cpfladd(){
	       $diancai_fenlei=M('diancai_fenlei');
           if($_POST){
               $token=$_POST['token'];  
               $data['token']=$_POST['token'];  
               $data['caipname']=$_POST['cpflname'];  
               $data['jieshao']=$_POST['cpfldes'];
               $data['ctime']=time();
			   $qry=$diancai_fenlei->add($data);
		       if($qry){
                      $this->success('操作成功',U('Diancai/cpfllist',array('token'=>$token)));
			   }else{
                       $this->error('操作失败');
			   }  
		   }else{   
	       $this->display();
		   }
	}
	//菜品分类修改
	public function cpflset(){
            $diancai_fenlei=M('diancai_fenlei');
           if($_POST){
               $token=$_POST['token'];  
               $cpflid=$_POST['cpflid'];  
               $data['token']=$_POST['token'];  
               $data['caipname']=$_POST['cpflname'];  
               $data['jieshao']=$_POST['cpfldes'];
               $data['ctime']=time();
			   $qry=$diancai_fenlei->where("token='$token' and cid='$cpflid'")->save($data);
		       if($qry){
                      $this->success('操作成功',U('Diancai/cpfllist',array('token'=>$token)));
			   }else{
                       $this->error('操作失败');
			   }  
		   }else{   
           $cpflid=$_GET['id'];
           $token=$_GET['token'];
           $rzt=$diancai_fenlei->where("token='$token' and cid='$cpflid'")->find();
	       $this->assign("rzt",$rzt); 
		   $this->display();
		   }
	     
	}

	//菜品分类删除
	public function cpfldel(){
	       $cpflid=$_GET['id'];
           $token=$_GET['token']; 
		   $diancai_fenlei=M('diancai_fenlei');
	       $rzt=$diancai_fenlei->where("token='$token' and cid='$cpflid'")->delete();
          if($rzt){
			
               $this->success('删除成功',U('Diancai/cpfllist',array('token'=> $token)));
		  }else{
                $this->error('删除失败',U('Diancai/cpfllist',array('token'=> $token)));     
		  }  
	}

	/**
	 * 菜品管理
	 */
	public function caipinlist(){
           $token=$_GET['token'];
		   $diancai_caipin=M('diancai_fenlei');
           
	       $diancai_caipin=M('diancai_caipin');  
		   $sql1="SELECT * FROM tp_diancai_caipin a left join tp_diancai_fenlei b on a.c_did=b.cid  
           WHERE a.token='$token'";
           $qry=$diancai_caipin->query($sql1);
		   $num=count($qry);
		   $p = new Page($num,8); 
		   $firstRow=$p->firstRow;
           $listRows=$p->listRows;
		   $sql="SELECT * FROM tp_diancai_caipin a left join tp_diancai_fenlei b on a.c_did=b.cid  
           WHERE a.token='$token' LIMIT $firstRow,$listRows";
		   //$tp_requestdata=M('requestdata');
		   $list=$diancai_caipin->query($sql);
		   //print_r($list);
		   $page=$p->show();
          //$logininfo = $loginmodel->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->findAll();
		  $this->assign('list',$list);
	      $this->assign('page',$page);
		   
		 $this->display();
	}

	//菜品添加
	public function addcaipin(){
		   $token=$_GET['token'];
	       $diancai_caipin=M('diancai_caipin');  
	       $diancai_fenlei=M('diancai_fenlei');
		   if($_POST){
			   //print_r($_POST);exit;
			   $token=$_POST['token'];  
               $data['token']=$_POST['token'];  
               $data['cpname']=$_POST['cpname'];  
               $data['c_did']=$_POST['catid'];
               $data['price']=$_POST['price'];
               $data['cpurl']=$_POST['logourl'];
               $data['cpjianjie']=$_POST['info'];
               $data['cdate']=time();
			   $qry=$diancai_caipin->add($data);
		       if($qry){
                      $this->success('操作成功',U('Diancai/caipinlist',array('token'=>$token)));
			   }else{
                       $this->error('操作失败');
			   }  
		   }else{
                $rzt=$diancai_fenlei->where("token='$token'")->select();  
			    $this->assign('rzt',$rzt);
				$this->display(); 
		   }
	      
	}

	//菜品修改
	public function caipinset(){
	       
	       $diancai_caipin=M('diancai_caipin');  
	       $diancai_fenlei=M('diancai_fenlei');
		   if($_POST){
			   
			   //print_r($_POST);exit;
			   $token=$_POST['token'];  
               $cpid =$_POST['cpid'];  
               $data['token']=$_POST['token'];  
               $data['cpname']=$_POST['cpname'];  
               $data['c_did']=$_POST['catid'];
               $data['goods_price']=$_POST['price'];
               $data['cpurl']=$_POST['logourl'];
               $data['cpjianjie']=$_POST['info'];
               $data['cdate']=time();
			  
			   $qry=$diancai_caipin->where("token='$token' and goods_id='$cpid'")->save($data);
			 
		       if($qry){
                      $this->success('操作成功',U('Diancai/caipinlist',array('token'=>$token)));
			   }else{
                       $this->error('操作失败');
			   }  
		   }else{
		   
		    $token=$_GET['token'];
	        $cpid=$_GET['id'];
		    $rzt=$diancai_caipin->where("token='$token' and goods_id='$cpid'")->find();
           //$did=$rzt['s_did'];
		    $qry=$diancai_fenlei->where("token='$token'")->select();
		   //var_dump($qry);
		   $this->assign('rzt',$rzt);
		   $this->assign('qry',$qry);
		   $this->display();
		   }
	       
	}
    //菜品删除
    public  function caipindel(){
	
          $token=$_GET['token'];
          $id=$_GET['id'];
		
		  $diancai_caipin=M('diancai_caipin');
		  $rzt=$diancai_caipin->where("token='$token' and cpid='$id'")->delete();
          if($rzt){
			
               $this->success('删除成功',U('Diancai/caipinlist',array('token'=> $token)));
		  }else{
                $this->error('删除失败',U('Diancai/caipinlist',array('token'=> $token)));     
		  } 
	}
    //订单管理
	public function admin(){
		   $diancai_order=M('diancai_order');
	       $token=$_GET['token'];
	       $cid  =$_GET['id'];
		   $count      = $diancai_order->where(array('token'=>$token,'cid'=>$cid))->count();
		   $ok_count      = $diancai_order->where(array('token'=>$token,'order_status'=>1,'cid'=>$cid))->count();
           $lost_count      = $diancai_order->where(array('token'=>$token,'order_status'=>2,'cid'=>$cid))->count();
           $no_count      = $diancai_order->where(array('token'=>$token,'order_status'=>3,'cid'=>$cid))->count();
          //用户取消的订单
		  $yhqux_count      = $diancai_order->where(array('token'=>$token,'order_status'=>4,'cid'=>$cid))->count();
		   $li1=$diancai_order->where("token='$token' and cid='$cid'")->select();
		   $num=count($li1);
		   $Page       = new Page($num,20);
           $show       = $Page->show();
           $li = $diancai_order->where("token='$token' and cid='$cid'")->limit($Page->firstRow.','.$Page->listRows)->order("checktime desc")->select(); 

		    $this->assign('count',$count);
		   $this->assign('ok_count',$ok_count);
		   $this->assign('no_count',$no_count);
		 	$this->assign('yhqux_count',$yhqux_count);
           $this->assign('lost_count',$lost_count);
		   $this->assign('li',$li);
		   $this->assign('page',$show); 
		   if(IS_POST){
          
           $da['order_status'] = $this->_post('status');
           $id = $this->_post('id');
           $cid = $this->_post('cid');
           $token = session('token');
           M('diancai_order')->where(array('id'=>$id,'token'=>$token))->save($da);
           $this->success('操作成功',U('Diancai/admin',array('token'=>session('token'),'id'=>$cid)));
			   
			}else{
				$this->display();
			}
	     
	}
    //菜品详细信息
	public function shoplist(){
	       $wecha_id=$_GET['wecha_id'];
	       $token=$_GET['token'];
	       $order_sn=$_GET['order_sn'];
	       $diancai_order_caipin=M('diancai_order_caipin');
		   $list1=$diancai_order_caipin->where("token='$token' and sub_order_sn='$order_sn' and wecha_id='$wecha_id'")->select();
		   $num=count($list1);
		   $Page       = new Page($num,20);
           $show       = $Page->show();
		   $list=$diancai_order_caipin->where("token='$token' and sub_order_sn='$order_sn' and wecha_id='$wecha_id'")->limit($Page->firstRow.','.$Page->listRows)->select();
		   $this->assign('list',$list);
		   $this->assign('page',$show);
	       $this->display();
	}
	/**
	 * 订单管理
	 */
	public function orders()
	{
		$this->display();
	}

	
}