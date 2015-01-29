<?php
/**
 *语音回复
**/
class ClassifyAction extends UserAction{
	public function index(){
		$db=D('Classify');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('sorts asc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Classify')->find($id);
		$this->assign('info',$info);
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
		$this->all_insert();
	}
	public function upsave(){
		$this->all_save();
	}

	public function sort(){
		$sort=htmlspecialchars_decode($_POST['sort']);
		$sort=json_decode($sort,true);
		for($i=0;$i<count($sort)-1;$i++){
			$data=array('sorts'=>$sort[$i]['sort']);
			M('classify')->where(array('id'=>$sort[$i]['id']))->save($data);
		}
	}
}
?>