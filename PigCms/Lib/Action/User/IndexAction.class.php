<?php
class IndexAction extends UserAction{
	//公众帐号列表
	public function index(){
		
		$where['uid']=session('uid');
		$group=D('User_group')->select();
		foreach($group as $key=>$val){
			$groups[$val['id']]['did']=$val['diynum'];
			$groups[$val['id']]['cid']=$val['connectnum'];
		}
		unset($group);
		$db=M('Wxuser');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('info',$info);
		$this->assign('group',$groups);
		$this->assign('page',$page->show());
		$this->display();
	}
	//添加公众帐号
	public function add(){
		$randLength=6;
		$chars='abcdefghijklmnopqrstuvwxyz';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$tokenvalue=$randStr.time();
		$this->assign('tokenvalue',$tokenvalue);
		$this->assign('email',time().'@yourdomain.com');
		
		//地理信息
		//if (C('baidu_map_api')){
			//$locationInfo=json_decode(file_get_contents('http://api.map.baidu.com/location/ip?ip='.$_SERVER['REMOTE_ADDR'].'&coor=bd09ll&ak='.C('baidu_map_api')),1);
			///$this->assign('province',$locationInfo['content']['address_detail']['province']);
			//$this->assign('city',$locationInfo['content']['address_detail']['city']);
			//var_export($locationInfo);
	//	}
	
		
		$this->display();
	}
	public function edit(){
		//$id=$this->_get('id','intval');//程云注释掉 因为一个帐号只允许一个公众号
		$where['uid']=session('uid');
		$res=M('Wxuser')->where($where)->find();//$res=M('Wxuser')->where($where)->find($id)
		$this->assign('info',$res);
		$this->display();
	}
	
	public function del(){
	exit;//程云
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D('Wxuser')->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function upsave(){//更新操作token不变
		$this->all_save('Wxuser');
		
	}
	
	public function insert(){
		$this->Ins($_POST['token']);//调用自动插入数据
		$data=M('User_group')->field('wechat_card_num')->where(array('id'=>session('gid')))->find();
		$users=M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->find();
		if($users['wechat_card_num']<$data['wechat_card_num']){
			
		}else{
			$this->error('您的VIP等级所能创建的公众号数量已经到达上线，请购买后再创建',U('User/Index/index'));exit();
		}
		//$this->all_insert('Wxuser');
		//
		$db=D('Wxuser');
		if($db->create()===false){
			$this->error($db->getError());
		}else{
			$id=$db->add();
			if($id){
				M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->setInc('wechat_card_num');
				$this->addfc();
				//							
				session('token',$_POST['token']);//程云添加 当用户提交了公众信息 吧token放到session 跳转到
				$this->success('公众账号信息添加成功',U('User/Function/index',array('id'=>$id,'token'=>$_POST['token'])));
					
			}else{
				$this->error('操作失败',U('Index/add'));
			}
		}
		
	}
 public function Ins( $token ){//预定义数据	
				$this->Insmr( $token,'微预约','微预约',1,$arr = array( '1'=>array( '1'=>'名字','2'=>'name','3'=>'text','4'=>1,'5'=>'1','6'=>'1','7'=>'名字不能为空' ),
																'2'=>array( '1'=>'邮箱','2'=>'email','3'=>'text','4'=>1,'5'=>'/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/','6'=>'2','7'=>'邮箱验证错误！' ),
																'3'=>array( '1'=>'手机号','2'=>'phone','3'=>'text','4'=>1,'5'=>'/^1[3|4|5|8][0-9]\d{4,8}$/','6'=>'3','7'=>'手机号错误！' ),
																'4'=>array( '1'=>'身份证号','2'=>'paper','3'=>'text','4'=>1,'5'=>'/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/','6'=>'4','7'=>'身份证位数不对' ),
																'5'=>array( '1'=>'简介','2'=>'Brief','3'=>'textarea','4'=>1,'5'=>'/^\s*\S((.){0,50}\S)?\s*$/','6'=>'5','7'=>'您的字数超出了！' )
																));
				$this->Insmr( $token,'微留言','微留言',2,$arr = array( '1'=>array( '1'=>'姓名','2'=>'name','3'=>'text','4'=>1,'5'=>'1','6'=>'1','7'=>'姓名不能为空' ),
																'2'=>array( '1'=>'性别','2'=>'sex','3'=>'text','4'=>1,'5'=>'1','6'=>'2','7'=>'性别不能为空' ),
																'3'=>array( '1'=>'标题','2'=>'title','3'=>'text','4'=>1,'5'=>'1','6'=>'3','7'=>'标题不能为空' ),
																'4'=>array( '1'=>'留言内容','2'=>'text','3'=>'textarea','4'=>1,'5'=>'1','6'=>'4','7'=>'内容超限' )
																));
				$this->Insmr( $token,'微报名','微报名',3,$arr = array( '1'=>array( '1'=>'名字','2'=>'name','3'=>'text','4'=>1,'5'=>'1','6'=>'1','7'=>'名字不能为空' ),
																'2'=>array( '1'=>'邮箱','2'=>'email','3'=>'text','4'=>1,'5'=>'/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/','6'=>'2','7'=>'邮箱验证错误！' ),
																'3'=>array( '1'=>'手机号','2'=>'phone','3'=>'text','4'=>1,'5'=>'/^1[3|4|5|8][0-9]\d{4,8}$/','6'=>'3','7'=>'手机号错误！' ),
																'4'=>array( '1'=>'身份证号','2'=>'paper','3'=>'text','4'=>1,'5'=>'/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/','6'=>'4','7'=>'身份证位数不对' )
																));												
	}			
	public function Insmr( $token,$name,$keyword,$bid,$arr ){//一级信息
			$m = M( 'selfform' );
			$time = time()+(7*24*60*60);
			$data[ 'name' ]	= $name;
			$data[ 'keyword' ]	= $keyword;
			$data[ 'endtime' ] = $time;
			$data[ 'token' ] = $token;
			$data[ 'bid' ] = $bid;
			$id=$m->add( $data );
				if( !empty( $id ) ){
					$yy['pid'] = $id;
                    $yy['module'] = 'Selfform';
                    $yy['token'] = $token;
                    $yy['keyword'] = $keyword;
                    M('Keyword')->add($yy);	
				for( $i = 1;$i<=count( $arr );$i++ ){
				$this->Insm( $id,$arr[ $i ][ 1 ],$arr[ $i ][ 2 ],$arr[ $i ][ 3 ],$arr[ $i ][ 4 ],$arr[ $i ][ 5 ],$arr[ $i ][ 6 ],$arr[ $i ][ 7 ] );	
						}									
				}
	}		
	public function Insm( $formid,$displayname,$fieldname,$inputtype,$require,$regex,$taxis,$error ){//二级信息
				$mm = M( 'selfform_input' );
				$da[ 'formid' ] = $formid;
				$da[ 'displayname' ] = $displayname;
				$da[ 'fieldname' ] = $fieldname;
				$da[ 'inputtype' ] = $inputtype;
				$da[ 'require' ] = $require;
				$da[ 'regex' ] = $regex;
				$da[ 'taxis' ] = $taxis;
				$da[ 'errortip' ] = $error;		
				$sid=$mm->add( $da );	
	}				
	//功能
	public function autos(){
		$this->display();
	}
	
	public function addfc(){
		$token_open=M('Token_open');
		$open['uid']=session('uid');
		$open['token']=$_POST['token'];
		$gid=session('gid');
		$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$gid)->select();
		foreach($fun as $key=>$vo){
			$queryname.=$vo['funname'].',';
		}
		$open['queryname']=rtrim($queryname,',');
		$token_open->data($open)->add();
	}
	
	public function usersave(){
		$pwd=$this->_post('password');
		if($pwd!=false){
			$data['password']=md5($pwd);
			$data['id']=$_SESSION['uid'];
			if(M('Users')->save($data)){
				$this->success('密码修改成功！',U('Index/index'));
			}else{
				$this->error('密码修改失败！',U('Index/index'));
			}
		}else{
			$this->error('密码不能为空!',U('Index/useredit'));
		}
	}
	public function help() {
		$this->display();
	}
}
?>