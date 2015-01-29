<?php
class WdiaoyanAction extends UserAction{

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
		  import("@.ORG.Page");
          $token=$_GET['token'];
		  $wdy=M("wdyform");
		  $list1=$wdy->where("token='$token'")->select();
		  $num=count($list1);
		  //echo $num;
		  
		  $Page       = new Page($num,8);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  $list = $wdy->where("token='$token'")->order('time desc  ')->limit($Page->firstRow.','.$Page->listRows)->select();
		  //var_dump($list);
		  $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出
	      //var_dump($list);
		 
		  $this->display();
	}

	//添加微调研类别
	public function wdyadd(){
		   $keyword_model=M('Keyword');
		   $wdy=M("wdyform");
		   if($_POST){
			   //var_dump($_POST);
			   $token          =$_POST['token'];
			   $data['token']  =$_POST['token'];
			   $data['name']   =$_POST['uname'];
			   $data['keyword']=$_POST['keyword'];
			   $data['intro']  =$_POST['intro'];
			   $data['content']=$_POST['content'];
			   $data['endtime']=strtotime($_POST['enddate']);
			   $data['logourl']=$_POST['logourl'];
			   $data['content']=$_POST['content'];
			   $data['time']=time();
                  
               $qry=$wdy->add($data);
			 
			 
			   if($qry){
				 $data['keyword']=$_POST['keyword'];
				 $data['pid']    =$qry;
				 $data['token']  =$_POST['token'];
				 $data['module'] ='Wdiaoyan';
				 $keyword_model->add($data);
                   $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));
				   
			   }else{
				   $this->error('操作失败');
			   }
			 
		   }else{
           $this->display();
	   }
	}

	//修改微调研关键字
	public function xiugai(){
	       $keyword_model=M('Keyword');
	      $id=$_GET['id'];
		  $token=$_GET['token'];
		  $wdy=M('wdyform');
		  if($_POST){
			   $token          =$_POST['token'];
			   $id          =$_POST['id'];
			   $data['token']  =$_POST['token'];
			   $data['name']   =$_POST['uname'];
			   $data['keyword']=$_POST['keyword'];
			   $data['intro']  =$_POST['intro'];
			   $data['content']=$_POST['content'];
			   $data['endtime']=strtotime($_POST['enddate']);
			   $data['logourl']=$_POST['logourl'];
			   $data['content']=$_POST['content'];
			   $data['time']=time();
			   $qry=$wdy->where("id='$id' and token='$token'")->save($data);
			   
			   if($qry){
				 $data['keyword']=$_POST['keyword'];
				 $data['pid']    =$id;
				 $data['token']  =$_POST['token'];
				 $data['module'] ='Wdiaoyan';
				 $keyword_model->where("pid='$id' and token='$token'")->save($data);
			      $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));exit;
			   }else{
			      $this->error('操作失败'); exit;
			   }
		  }else{
          $rzt=$wdy->where("id='$id' and token='$token'")->find();
		  $this->assign('set',$rzt);
		  //var_dump($rzt);
		  }
		  $this->display();
	}
	//删除
	public function del(){
	        $id=$_GET['id'];
			$keyword_model=M('Keyword');
		    //$id=$_GET['id'];
		    $token=$_GET['token'];
		    $wdy=M('wdyform');
		    $wdy_input=M('wdy_input');
		    $wdy_biaoti=M('wdy_biaoti');
			$qry=$wdy->where("id='$id' and token='$token'")->delete();
            if($qry){
                 $wdy_input->where("formid='$id' and token='$token'")->delete(); 
                 $wdy_biaoti->where("formid='$id' and token='$token'")->delete(); 
				 $keyword_model->where("pid='$id' and token='$token'")->delete();
                 $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));
			}else{
			     $this->error('操作失败'); 
			}

	}
	//修改添加用户名
	public function xupdate(){
		    $wdy=M('wdy_input');
		   if($_POST){
			   $id   =$_POST['id'];
			   $token=$_POST['token'];
			   $data['displayname']=$_POST['displayname'];
			   $data['fieldname']=$_POST['fieldname'];
			   $data['inputtype']=$_POST['inputtype'];
			   $data['token']=$_POST['token'];
			   $qry=$wdy->where("id='$id' and token='$token'")->save($data);
			   if($qry){
                     $this->success('操作成功');
			   }else{
                     $this->error('操作失败'); 
			   }
		   }else{
	       $id=$_GET['id']; 
	       $token=$_GET['token']; 
		   
		   $rzt=$wdy->where("id='$id' and token='$token'")->find();
		   $this->assign("list",$rzt);
		   }
		   $this->display();
	}
	//删除用户名手机号
	public function xdelete(){
	       $id=$_GET['id']; 
	       $token=$_GET['token'];
		   
		   $wdy=M('wdy_bfenlei');
		   $rzt=$wdy->where("id='$id'")->delete();
		   if($rzt){
                 $this->success('操作成功');
		   }else{
                  $this->error('操作失败'); 
		   }
	}

	//修改标题选项
	public function supdate(){
	       $wdy=M("wdy_biaoti");
		   if($_POST){
			   //var_dump($_POST);exit;
		           $token=$_POST['token'];
				   $fid   =$_POST['id'];
				   $data['bname']=$_POST['bname'];
				 
				   $data['token']=$_POST['token'];
				   //var_dump($data);exit;
				   $qry=$wdy->where("id='$fid' and token='$token'")->save($data);
                    //var_dump($qry);exit;
				   if($qry){
					   $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));
				   }else{
					   $this->error('操作失败'); 
				   }
		   }else{
		   
		   
		   $id=$_GET['id'];
		   $token=$_GET['token'];
		   $rzt=$wdy->where("id='$id' and token='$token'")->find();
		   $this->assign('list',$rzt);
		   
	       }
           $this->display();
	}
	//删除标题选项
	public function sdelete(){
		   $wdy=M("wdy_biaoti");//标题表
		   $wdy_input=M("wdy_input");
		   
	       $id=$_GET['id'];  
	       $token=$_GET['token']; 
		   $rzt=$wdy->where("id='$id' and token='$token'")->delete();
		    if($rzt){
               $wdy_input->where("b_fid='$id' and token='$token'")->delete(); 
			   $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));
		   }else{
			   $this->error('操作失败'); 
		   }
	}
	//修改标题里面列表选项
	public function lupdate(){
	        $wdyinput=M('wdy_input');   
           if($_POST){
			   //var_dump($_POST);exit;
			   $id   =$_POST['id'];
			   $token=$_POST['token'];
			   
			   $data['displayname']=$_POST['displayname'];
			   $data['fieldname']=$_POST['fieldname'];
			   $data['inputtype']=$_POST['inputtype'];
			   $data['token']=$_POST['token'];
			   $qry=$wdyinput->where("id='$id' and token='$token'")->save($data);
               if($qry){
			      $this->success('操作成功');exit;
				   
			   }else{
			       $this->error('操作失败');exit;
			   }
		   }else{
		       $id=$_GET['id'];  
	           $token=$_GET['token']; 
		       $rzt=$wdyinput->where("id='$id' and token='$token'")->find();
		       $this->assign("list",$rzt);
		   }

           $this->display();
           
	}
	//删除标题里面的选项列表
	public function ldelete(){
	        $wdy=M("wdy_input");
	       $id=$_GET['id'];  
	       $token=$_GET['token']; 
		   $rzt=$wdy->where("id='$id' and token='$token'")->delete();
		    if($rzt){
			   $this->success('操作成功',U('Wdiaoyan/index',array('token'=> $token)));
		   }else{
			   $this->error('操作失败'); 
		   }
	}
   //添加调研列表
	public function xuanxadd(){
           $id=$_GET['id'];
		   $this->assign('id',$id);
		   import("@.ORG.Page");
           $token=$_GET['token'];
		   $wdy=M("wdy_input");
		   $list1=$wdy->where("token='$token' and formid='$id'")->select();
		   $num=count($list1);
		  //echo $num;
		  
		  $Page       = new Page($num,15);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  $list = $wdy->where("token='$token' and formid='$id'")->order('id desc  ')->limit($Page->firstRow.','.$Page->listRows)->select();
		  //var_dump($list);
		  $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出
	      //var_dump($list);
           $this->display(); 	
	}

   //添加功能列表
   public function wdyinputadd(){
             $wdyinput=M('wdy_input');   
           if($_POST){
			   //var_dump($_POST);exit;
			   $fid   =$_POST['fid'];
			   $token=$_POST['token'];
			   $data['formid']=$_POST['fid'];
			   $data['displayname']=$_POST['displayname'];
			   $data['fieldname']=$_POST['fieldname'];
			   $data['inputtype']=$_POST['inputtype'];
			   $data['token']=$_POST['token'];
			   $qry=$wdyinput->add($data);
               if($qry){
			      $this->success('操作成功',U('Wdiaoyan/xuanxadd',array('token'=> $token,'id'=>$fid)));
				   
			   }else{
			       $this->error('操作失败');
			   }
		   }else{
           $this->display();
           }

   }
   // 添加文章标题
   public function wdybiaotiadd(){
	        $wdybiati=M('wdy_biaoti');
             if($_POST){
				   //var_dump($_POST);
				   $token=$_POST['token'];
				   $fid   =$_POST['fid'];
				   $data['bname']=$_POST['bname'];
				   $data['formid']=$_POST['fid'];
				   $data['token']=$_POST['token'];
                   $qry=$wdybiati->add($data);
			        if($qry){
					    $this->success('操作成功',U('Wdiaoyan/biaotilist',array('token'=> $token,'id'=>$fid)));
					}else{
					    $this->error('操作失败');
					} 
			 }else{
             $this->display();
             }

   }

   //标题列表页面
   public function biaotilist(){
	       $id=$_GET['id'];
		   $this->assign('id',$id);
		   import("@.ORG.Page");
           $token=$_GET['token'];
		   $wdy=M("wdy_biaoti");
		   $list1=$wdy->where("token='$token' and formid='$id'")->select();
		   $num=count($list1);
		  //echo $num;
		  
		  $Page       = new Page($num,15);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  $list = $wdy->where("token='$token' and formid='$id'")->order('id desc  ')->limit($Page->firstRow.','.$Page->listRows)->select();
		  //var_dump($list);
		  $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出
           $this->display();
   }
   
   // 添加标题里面选项的列表
   public function biaotixunx(){
	      //$id=$_GET['id'];
          $wdy=M("wdy_input");
		  if($_POST){
			  //var_dump($_POST);exit;
			  $fid  =$_POST['fid'];
			  $token=$_POST['token'];
			  $data['b_fid']=$_POST['fid'];
			  $data['token']=$_POST['token'];
			  $data['displayname']=$_POST['displayname'];
			  $data['fieldname']=$_POST['fieldname'];
			  $data['inputtype']=$_POST['inputtype'];

			  $qry=$wdy->add($data);
			  //var_dump($qry);exit;
			  if($qry){
                  $this->success('操作成功');exit;
			  }else{
			       $this->error('操作失败');exit;
			  }
		  }else{
		  
		  }
          $this->display(); 
   }

   //每个标题对应的选项列表
   public function xuanxlist(){
           $id=$_GET['id'];
		   //$this->assign('id',$id);
		   import("@.ORG.Page");
           $token=$_GET['token'];
		   $wdy=M("wdy_input");
		   $list1=$wdy->where("token='$token' and b_fid='$id'")->select();
		   $num=count($list1);
		  //echo $num;
		  
		  $Page       = new Page($num,15);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  $list = $wdy->where("token='$token' and b_fid='$id'")->order('id desc  ')->limit($Page->firstRow.','.$Page->listRows)->select();
		  //var_dump($list);
		  $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出   
          $this->display();
   }

   //投票结果表
   public function tplist(){
	      import("@.ORG.Page");
          $fenlei=M('wdy_bfenlei');
		  $bfid=$_REQUEST['id'];
		  $formid=$_REQUEST['formid'];

          $rzt=$fenlei->where("b_fid='$bfid' and formid='$formid'")->select();
		  $num=count($rzt);
		  $Page       = new Page($num,8);// 实例化分页类 传入总记录数和每页显示的记录数
		  $show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		  $list =$fenlei->where("b_fid='$bfid' and formid='$formid'")->order('id desc  ')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		  //var_dump($list);
		  $this->assign('list',$list);// 赋值数据集
		  $this->assign('page',$show);// 赋值分页输出   
          //$this->assign('list',$rzt);
		  //echo '<pre>';
		  //var_dump($rzt);exit;
          $this->display();
   }



   //活动选择
	public function yuyuebd(){
	           $wdy_yuyuexx=M('wdy_yuyuexx');
			   $lottery=M('lottery'); 
			   $token=$_GET['token'];
			   $kid=$_POST['kid'];
			   $qry=$wdy_yuyuexx->where("token='$token'")->find();
			   $sql="select * from tp_lottery where token='$token' and type in(1,2)";
			   $list=$lottery->query($sql);
			   $this->assign('list',$list);
			   if(empty($list)){
                    $this->error('您尚未添加预约信息，请您到预约模块添加',U('lottery/index',array('token'=>$_GET['token'])));
			   }else{  
			   if($_POST){
                     if(!empty($kid)){

							//echo '22222222';exit;
							$kid=$_POST['kid'];
							$data['keyid']=$_POST['keyword'];
							$data['token']=$_POST['token'];
							//var_dump($data);
							$qry=$wdy_yuyuexx->where("keyid='$kid'")->save($data);
							//echo $qry;exit;
							if($qry){
								$this->success('修改成功');exit;
							}else{
								$this->error('修改失败');     
							}
						 }else{
							//var_dump($_POST);exit;
							$data['keyid']=$_POST['keyword'];
							$data['token']=$_POST['token'];
							$qry=$wdy_yuyuexx->add($data);
							if($qry){
								$this->success('成功');exit;
							}else{
								$this->error('失败');     
							}
						
						
						}
			   }else{
			   
				if($qry){
        
					     $lottery=M('lottery'); 
						
						 $this->assign('list',$list);
						 $this->assign('qry',$qry);


					     //var_dump($qry);
					   
			}
	     }
         
			
	 }	
	 $this->display();
		
	}



	//添加过以后再次进来以后页面信息显示
	public function addxinxi(){
		   $wdy_addxinxi=M('wdy_addxinxi');
		   $token=$_GET['token'];
		 
		   $list=$wdy_addxinxi->where("token='$token'")->find();
		   $this->assign("list",$list); 
		  
		   if($_POST){
			   $token1=$_POST['token1'];
			   if(!empty($token1)){
					  
					$data['token']=$_POST['token'];
					$data['addxx']=$_POST['intro'];
					$qry=$wdy_addxinxi->where("token='$token1'")->save($data);
					if($qry){
						 $this->success('更新成功');exit;
					}else{
						 $this->error('更新失败');exit;     
					}
			   }else{
				
					
					$data['token']=$_POST['token'];
					$data['addxx']=$_POST['intro'];
					$qry=$wdy_addxinxi->add($data);
					if($qry){
						 $this->success('成功');exit;
					}else{
						 $this->error('失败');exit;
					}
				
		     }
		   }else{
		   
		     $list=$wdy_addxinxi->where("token='$token'")->find();
		     $this->assign("list",$list); 
		   }

		   $this->display();
	       
	}

}


?>