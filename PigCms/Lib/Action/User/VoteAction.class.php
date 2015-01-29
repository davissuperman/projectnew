<?php
class VoteAction extends UserAction {

    public function Index() {//用户管理控制器
    
	
	}
	//投票列表
	public function tplist(){
	        $wedding_vote=M('wedding_vote');
			$token=$_GET['token'];
	        $list=$wedding_vote->where("token='$token'")->select();
            $this->assign('list',$list);
			$this->display();   
	}

	//投票里面的相信内容
	public function tpxxlist(){
	       $vid=$_GET['id'];
		   $wedding_vote_option=M('wedding_vote_option');
		   $sql="select * from tp_wedding_vote_option a left join tp_wedding_vote b on a.vid=b.vid where a.vid='$vid'";
           //$list=$wedding_vote_option->where("vid='$vid'")->select(); 
	       $list=$wedding_vote_option->query($sql);
		   $this->assign('list',$list);
		   $this->display();
	}

	//删除详细内容
	public function tpxxDele(){
	       $oid=$_GET['id']; 
		   $token=$_GET['token'];
           $wedding_vote_option=M('wedding_vote_option');   
		   $rzt=$wedding_vote_option->where("oid='$oid'")->delete();
		   if($rzt){
			   $this->success('删除成功');
		   }else{
				$this->error('删除失败');     
		   }
	      
	}

    /*
     *  $Token 用户当前的唯一值
     *  $VoteID 用户删除投票选项的ID
     */

    public function voteDele() {//投票删除
		   $vid=$_GET['id'];
		   $token=$_GET['token'];
           $wedding_vote=M('wedding_vote');   
		   $wedding_vote_attachment=M('wedding_vote_attachment');   
           $wedding_vote_option=M('wedding_vote_option');   
           $wedding_vote_comment=M('wedding_vote_comment');   
		   $rzt=$wedding_vote->where("vid='$vid'")->delete();
		   if($rzt){
			   $rzt1=$wedding_vote_attachment->where("vid='$vid'")->delete();
		      $rzt2=$wedding_vote_option->where("vid='$vid'")->delete();
		      $rzt3=$wedding_vote_comment->where("vid='$vid'")->delete();
			   $this->success('删除成功');
		   }else{
				$this->error('删除失败');     
		   }
    }

	//投票评论信息
	public function pllist(){
		   $vid=$_GET['id']; 
		   $token=$_GET['token'];
	       $wedding_vote_comment=M('wedding_vote_comment');
           $list=$wedding_vote_comment->where("vid='$vid'")->select();
		   // echo '<pre>';
		   //var_dump($list);exit;
	       $this->assign('list',$list);
		   $this->display();
	}

	//投票评论删除
	public function voteplDele(){
	       $cid=$_GET['id'];
		   $token=$_GET['token'];
		   $wedding_vote_comment=M('wedding_vote_comment');   
		   $rzt=$wedding_vote_comment->where("cid='$cid'")->delete();
		   if($rzt){
			   $this->success('删除成功');
		   }else{
				$this->error('删除失败');     
		   } 
	
	
	}
//预约万能表单
	public function yuyuebd(){
	           $vote_yuyuexx=M('wedding_vote_yuyuexx');
			   $selfform=M('selfform'); 
			   $token=$_GET['token'];
			   $kid=$_POST['kid'];
			   $qry=$vote_yuyuexx->where("token='$token'")->find();
			   $list=$selfform->where("token='$token'")->select();
			   $this->assign('list',$list);
               if(empty($list)){
                    $this->error('您尚未添加预约信息，请您到预约模块添加',U('Selfform/index',array('token'=>$_GET['token'],'bid'=>1)));
			   }else{  
			   if($_POST){
                     if(!empty($kid)){

							$kid=$_POST['kid'];
							$data['keyid']=$_POST['keyword'];
							$data['token']=$_POST['token'];
							$qry=$vote_yuyuexx->where("keyid='$kid'")->save($data);
							if($qry){
								$this->success('修改成功');exit;
							}else{
								$this->error('修改失败');     
							}
						 }else{
							
							$data['keyid']=$_POST['keyword'];
							$data['token']=$_POST['token'];
							$qry=$vote_yuyuexx->add($data);
							if($qry){
								$this->success('成功');exit;
							}else{
								$this->error('失败');     
							}
						
						
						}
			   }else{
			   
				if($qry){

						 $this->assign('list',$list);
						 $this->assign('qry',$qry);
                        
			}
	     }
         
			
	 }	
	 $this->display();
		
	}

    /*
     * $Token 用户当前的唯一值
     * $Value 要查找的值
     */

    public function Search() {//投票搜索
    }

    /*
     * $Token 	用户当前的唯一值
     * $RadioCount 单选投票汇总
     * $CheckBox_Count 多选用户投票汇总
     */

    public function VoteStat() {//用户投票统计
    }

}

?>