<?php
class UsersAction extends BaseAction{
	public function index(){
		header("Location: /");
	}

	public function checklogin(){
		$administrator = $this->_get( 'administrator','intval' );
		$db=D('Users');
		if( !empty( $administrator ) ){
		$where[ 'email' ] = $this->_get( 'logo','trim' );
		$pwd = $this->_get( 'pwd','trim' );			
		}else{
		$where1[ 'email' ] = $this->_post( 'username','trim' );
		$where['phone']=$this->_post('username','trim');
		$pwd=$this->_post('password','trim,md5');
		}
		$res=$db->where($where)->find();
		if( empty( $res ) ){
		$res=$db->where($where1)->find();		
		}
		if($res&&($pwd===$res['password'])){
			if($res['status']==0){
				$this->error('请联系在线客户，为你人工审核帐号');exit;
			}
			session('uid',$res['id']);
			session('gid',$res['gid']);
			session('uname',$res['username']);
			$info=M('user_group')->find($res['gid']);
			session('diynum',$res['diynum']);
			session('connectnum',$res['connectnum']);
			session('activitynum',$res['activitynum']);
			session('viptime',$res['viptime']);
			session('gname',$info['name']);
			$tt=getdate();
			if($tt['mday']===1){//程云修改过 之前不对   ---每月1号请求数 都归0
				$data['id']=$res['id'];
				$data['diynum']=0;
				$data['connectnum']=0;
				$data['textnum']=0;
				$data['imgnum']=0;
				$data['voicenum']=0;				
				$data['activitynum']=0;
				$db->save($data);
			}
			$db->where(array('id'=>$res['id']))->save(array('lasttime'=>time(),'lastip'=>$_SERVER['REMOTE_ADDR']));//最后登录时间
			//程云添加
			$user_weixin_info=M('Wxuser')->field('token')->where(array('uid'=>session('uid')))->find();
			if($user_weixin_info){//如果存在toke 说明已经添加了公众号
				session('token',$user_weixin_info['token']);
				$this->success('登录成功',U('User/Function/index'));
			}else{//没添加公众号返回到添加公众号页面
				
				$this->success('登录成功',U('User/Index/add'));
			}
			
			
		}else{
			$this->error('帐号密码错误',U('Index/login'));
		}
	}
	public function checkreg(){
		$db=D('Users');
		$info=M('User_group')->find(1);
		if($db->create()){
			$id=$db->add();
			if($id){				
				if(C('ischeckuser')!='true'){
					$this->success('注册成功,请联系在线客服审核帐号',U('User/Index/index'));exit;
				}
				session('uid',$id);
				session('gid',1);
				session('uname',$_POST['username']);
				session('diynum',0);
				session('connectnum',0);
				session('activitynum',0);
				session('gname',$info['name']);
				$this->success('注册成功',U('User/Index/index'));
			}else{
				$this->error('注册失败',U('Index/reg'));
			}
		}else{
			$this->error($db->getError(),U('Index/reg'));
		}
	}
	
	public function checkpwd(){
		$where['username']=$this->_post('username');
		$where['email']=$this->_post('email');
		$db=D('Users');
		$list=$db->where($where)->find();
		if($list==false) $this->error('邮箱和帐号不正确',U('Index/regpwd'));	
		$smtpserver = C('email_server'); 
		$port = C('email_port');
		$smtpuser = C('email_user');
		$smtppwd = C('email_pwd');
		$mailtype = "TXT";
		$sender = C('email_user');
		$smtp = new Smtp($smtpserver,$port,true,$smtpuser,$smtppwd,$sender); 
		$to = $list['email']; 
		$subject = C('pwd_email_title');
		$code = C('site_url').U('Index/resetpwd',array('uid'=>$list['id'],'code'=>md5($list['id'].$list['password'].$list['email']),'resettime'=>time()));
		$fetchcontent = C('pwd_email_content');
		$fetchcontent = str_replace('{username}',$where['username'],$fetchcontent);
		$fetchcontent = str_replace('{time}',date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),$fetchcontent);
		$fetchcontent = str_replace('{code}',$code,$fetchcontent);
		$body=$fetchcontent;
		//$body = iconv('UTF-8','gb2312',$fetchcontent);
		$send=$smtp->sendmail($to,$sender,$subject,$body,$mailtype);
		$this->success('请访问你的邮箱 '.$list['email'].' 验证邮箱后登录!<br/>');
		
	}	
	public function resetpwd(){
		$where['id']=$this->_post('uid','intval');
		$where['password']=$this->_post('password','md5');
		if(M('Users')->save($where)){
			$this->success('修改成功，请登录！',U('Index/login'));
		}else{
			$this->error('密码修改失败！',U('Index/index'));
		}
	}
	public function User(){//李建维，注册重写
				$phOne = $this->Gl( $_POST[ 'u' ] );
				$Field = $this->Gl( $_POST[ 'field' ] );
				$M = M( 'users' );
				$mInfo = $M->where( array( "{$Field}"=>$phOne ) )->getField( "{$Field}" );
					if( !$mInfo ){
						echo 1; 
					}else{
						echo 0;	
					}	
	}
	Private function Gl( $g ){//过滤
			$G = !empty( $g ) ? trim( $g ) : exit; 
			$G = htmlspecialchars($G, ENT_QUOTES);
			if( !get_magic_quotes_gpc() ){
				$G = addslashes( $G );
			}
				return $G;	
	}	
}