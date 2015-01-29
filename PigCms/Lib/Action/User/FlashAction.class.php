<?php
/**
 *首页幻灯片回复
**/
class FlashAction extends UserAction{
	public function index(){
		$db=D('Flash');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order("id asc")->order("orders asc")->limit($page->firstRow.','.$page->listRows)->select();
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
		$res=D('Flash')->where($where)->find();
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
	 
		//C('TOKEN_ON',false);
		$this->all_insert();
	}
	public function upsave(){
		$this->all_save();
	}
	
	public function updateOrder() {
		
		$json = htmlspecialchars_decode($_POST['json']);
		$json_obj = json_decode($json, true);

		foreach ($json_obj as $obj) {
			$db=D('Flash');
			$where['token']=session('token');
			$where['id'] = $obj['id'];
			$data['orders'] = $obj['order'];
			$info=$db->where($where)->data($data)->save();
		}
		
		echo 1;
	}

}
?>