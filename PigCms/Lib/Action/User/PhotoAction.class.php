<?php
class PhotoAction extends UserAction{
	public function index(){		
		
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		//dump($token_open);
		if(!strpos($token_open['queryname'],'adma')){$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));}
		//相册列表
		$data=M('Photo');
		$count      = $data->where(array('token'=>$_SESSION['token']))->count();
		$Page       = new Page($count,12);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);		
		$this->assign('photo',$list);
		$this->display();		
	}
	public function edit(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$data=D('Photo');
		if(IS_POST){
			$this->all_save('Photo','/list_add?token='.$_SESSION['token'].'&id='.$this->_get('id'));
		}else{
			$photo=$data->where(array('token'=>session('token'),'id'=>$this->_get('id')))->find();
			if($photo==false){
				$this->error('相册不存在');
			}else{
				$this->assign('photo',$photo);
			}
			$this->display();		
		
		}
	}
	public function list_edit(){
		if($this->_get('token')!=session('token')){
			$this->error('非法操作');
		}
			$check=M('Photo_list')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>$this->_post('id')))->find();
		if($check==false){
			$this->error('照片不存在');
		}
		if(IS_POST){
			$this->all_save('Photo_list','/list_add?token='.$_SESSION['token'].'&id='.$check['pid']);
		}else{
			$this->error('非法操作');
		}
	}
	public function list_del(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$check=M('Photo_list')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->find();
		if($check==false){$this->error('服务器繁忙');}
		if(empty($_POST['edit'])){
			if(M('Photo_list')->where(array('id'=>$check['id']))->delete()){
				M('Photo')->where(array('id'=>$check['pid']))->setDec('num');
				$this->success('操作成功');
			}else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	}
	public function list_add(){
		
		$checkdata=M('Photo')->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('pid')))->find();
		
		if($checkdata==false){$this->error('相册不存在');}
		if(IS_POST){			
			M('Photo')->where(array('token'=>session('token'),'id'=>$this->_post('pid')))->setInc('num');
			
			$photolist=M('photo_list');
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['token']=$_SESSION['token'];
			$data['pid']=$this->_request('pid');
			$data['info']=$_POST['info'];
			$data['sort']=$_POST['sort'];
			$data['status']=$_POST['status'];
			$qry=$photolist->add($data);
			if($qry){
					
			    $this->success('操作成功',U('User/Photo/list_add',array('token'=>session('token'),'id'=>$this->_request('pid'))));
			}
			//$this->all_insert('Photo_list');			
		}else{
			$data=M('Photo_list');
			$count      = $data->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('id')))->count();
			$Page       = new Page($count,15);
			$show       = $Page->show();
			$list = $data->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('id')))->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();		
			$this->assign('photo',$list);
			$this->assign('page',$show);
			$this->display();			
		}
		
	}
	public function add(){
		if(IS_POST){			
			$this->all_insert('Photo','/index?token='.$_SESSION['token']);			
		}else{
			$this->display();	
		
		}
		
	}
	public function del(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$check=M('Photo')->field('id')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->find();
		if($check==false){$this->error('服务器繁忙');}
		if(empty($_POST['edit'])){
			if(M('Photo')->where(array('id'=>$check['id']))->delete()){
				M('Photo_list')->where(array('pid'=>$check['id']))->delete();
				$this->success('操作成功');
			}else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	
	}

	public function show(){
		$this->display();
	}
	public function requestImg(){
		$id=$_GET['id'];
		$page=$_GET['page'];
		$token=$_GET['token'];
		$img=M('Photo_list')->field('picurl,title')->where(array('token'=>$token,'pid'=>$id))->limit(20*$page,20*($page+1))->select();
		echo json_encode($img);
	}


}


?>