<?php
class TokenAction extends BackAction{
	public function index(){
		$map = array();
		if( IS_POST && strlen($_POST['keyword'])>0 ){
			$map['wxname'] = array( 'like','%'.trim($_POST['keyword']).'%' );
		}
		$UserDB = D('Wxuser');
		$count = $UserDB->where($map)->count();
		$Page       = new Page($count,5);// 实例化分页类 传入总记录数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$list = $UserDB->where($map)->order('id ASC')->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		foreach($list as $key=>$value){
			$user=M('Users')->field('id,gid,username')->where(array('id'=>$value['uid']))->find();
			if($user){
				$list[$key]['user']['username']=$user['username'];
				$list[$key]['user']['gid']=$user['gid']-1;
			}
		}
		//dump($list);
		$this->assign('list',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		
		
	}
	public function del(){
		$id=$this->_get('id','intval',0);
		$wx=M('Wxuser')->where(array('id'=>$id))->find();	
		$Darr = array( 'activity','adma','alipay_config','api','areply','classify','company','diymen_class','diymen_set','flash','home','host','host_list_add','host_order','keyword','lottery','lottery_record','member','member_card_contact','member_card_coupon','member_card_create','member_card_exchange','member_card_info','member_card_integral','member_card_set','member_card_sign','member_card_vip','moopha_article','moopha_channel','moopha_site','nearby_user','ordering_class','ordering_set','other','photo','photo_list','product','product_cart','product_cart_list','product_cat','product_diningtable','reply_info','requestdata','selfform','site_plugmenu','snccode','taobao','text','token_open','upyun_attachement','user_request','userinfo','voiceresponse','wecha_user' );//删除关于TOKEN相关的表
		foreach( $Darr as $val ){
		M( "{$val}" )->where( array('token'=>$wx['token']) )->delete();			
		}	
		$diy = M('Wxuser')->where(array('token'=>$wx['token']))->delete();
		if($diy){
			$this->success('操作成功','/index.php?g=System&m=Token');
		}else{
			$this->error('操作失败');
		}
	}
	public function edit(){
		if(IS_POST){
			$this->all_save();
		}else{
			$id=$this->_get('id','intval',0);
			if($id==0)$this->error('非法操作');
			$this->assign('tpltitle','编辑');
			$fun=M('Function')->where(array('id'=>$id))->find();
			$this->assign('info',$fun);
			$group=D('User_group')->getAllGroup('status=1');
			$this->assign('group',$group);
			$this->display('add');
		}
	}
	//用户功能显示 autho:libin
	public function fun(){
		$UserF = M('Function');
		$F = $UserF->select();
		$this->Assign( 'val',$F );
		$id = trim($this->_get('id'));
		$fundate=M('token_open')->field('token,queryname')->where(" token = '{$id}' ")->select();
		$ar= explode(',',trim($fundate[0]['queryname']));
		$this->assign( 'va',$ar);
		$this->Assign( 'token',trim( $fundate[0]['token'] ) );
		$this->display();	
	}
	//用户功能更新
	public function Ajax(){
	$fun = trim( $_POST['name'] );
	$tokens = trim( $_POST[ 'tok' ] );
	$Dataupdate = M( 'token_open' );
	$ar['queryname'] = $fun;
	$sucess = $Dataupdate->where(" token = '{$tokens}' ")->save( $ar );
	if( $sucess ){
		echo 1;
	}else{
		echo 0;
		}
	}
	//管理模块
	public function Manage(){
			$token = trim( $_GET[ 'token' ] );
			$M = M( 'Wxuser' );
			$iNfo = $M->where( array( 'token'=>$token ) )->find();
			$D = M( 'users' );
			$iN = $D->where( array( 'id'=>$iNfo[ 'uid' ] ) )->find();
			$this->success('跳转成功','/index.php?g=Home&m=Users&a=checklogin&logo='.$iN['email'].'&pwd='.$iN['password'].'&administrator=1');
			//print_r( json_encode( $iN ) );
	
	}
	//用户空间大小修改
	public function upadd(){
	     $token=$_REQUEST['id'];
		 //var_dump($token);
		 $wxuser=M('wxuser');
		 $qry=$wxuser->where("token='$token'")->find();
		 //var_dump($qry);
		 $this->assign('qry',$qry);
		 $this->display();
	}
	//添加用户空间
	public function uploadadd(){
	  $token          =$_POST['token'];
	  $numadd         =$_POST['numadd'];
	  $num            =$_POST['num'];
	  $usednum        =$_POST['usednum'];
	  //var_dump($token);exit;
	  //echo $wxname.$numadd.'<br>'.$num.'<br>'.$usednum;exit;
	  $data['num']    =$num+$numadd;
	  $data['usednum']=$usednum+$numadd;
	  $wxuser=M('wxuser');
	  $qry=$wxuser->where("token='$token'")->data($data)->save();
	  if($qry){
	     $this->success('操作成功','/index.php?g=System&m=Token');
	  }else{
	     $this->error('提交失败',U('Token/upadd')); 
	  }
	  //echo $wxname.$num.$usednum;
	   //var_dump($_REQUEST);
	}

	//添加用户模块
	public function gongnengadd(){
		$token=$_REQUEST['id'];
	    $tphome=M('home');
		$qry=$tphome->where("token='$token'")->find();
		$this->assign('qry',$qry);
		$this->display();
	}

	public function upgnadd(){
	      $token=$_POST['token'];
	      $advancetpl=$_POST['advancetpl'];
		  
		  $thome=M('home');

		  
		  if($advancetpl){
			  
		       $data['advancetpl']=1;
			   $rzt=$thome->where("token='$token'")->data($data)->save();
			   //var_dump($rzt);exit;
               if($rzt){
				   $this->success('添加cms模块成功','/index.php?g=System&m=Token');
			   }else{
			       $this->error('微信号没配置微网站自动回复！',U('Token/gongnengadd')); 
			   }
               
		  }else{
		  	
		  		$data['advancetpl']=0;
			   $rzt=$thome->where("token='$token'")->data($data)->save();
		       $this->success('成功取消cms','/index.php?g=System&m=Token');
		  }
		 
	}
}
?>