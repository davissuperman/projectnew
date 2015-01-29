<?php
/**
 *语音回复
**/
class VoiceresponseAction extends UserAction{
	public function index(){
	/*	$where['uid']=session('uid');
		$res=M('Voiceresponse')->where($where)->select();
		$this->assign('info',$res);
		$this->display();*/
		
		
		$db=D('Voiceresponse');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
	
		$page=new Page($count,15);
		$info=$db->where($where)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
		$this->display();
	}
	public function edit(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$res=D('Voiceresponse')->where($where)->find();
		$this->assign('info',$res);
		$this->display();
	}
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		
	if($this->c_users['voicenum']>=$this->c_users['textcount']){		
			$this->error('您的音频自定义回复数已用完！请联系相关人员');
		}else{	

			$this->all_insert();
			M('Users')->where(array('id'=>$_SESSION['uid']))->setInc('voicenum');		
		}
		
	
	}
	public function upsave(){
		$this->all_save();
	}
}
?>