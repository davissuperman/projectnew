<?php

/*
Wap下面的YuangongAction
需要调取用户信息
yhlist 用户信息
grlist 个人信息
grtplist 针对每个用户投票
grpllist 针对个人评论信息
*/
class YuangongAction extends BaseAction{
	
	public function index(){
		echo '1111111';
	   //echo '111111';exit;
	   $token   =$_REQUEST['token'];
	   $wecha_id=$_REQUEST['wecha_id'];
	   //echo $wecha_id;
	   //$this->assign("token",$token);
	   //$this->assign("wecha_id",$wecha_id);
	   //$this->display();
    }
	//用户信息
	public function yhlist(){
	       
			$this->display();
	}
   //grlist个人信息
	public function grlist(){
		   import("@.ORG.Page");
		   $wedding_staff=M('wedding_staff');
		   $sid=$_GET['id'];//通过微信二维码扫描玩以后过来的连接地址展示用户个人信息
	       //echo $sid;
		   $token=$_GET['token'];
           $numck=explode('_',cookie('sid'));
		   //print_r($numck);
		   if(in_array($sid,$numck)){
			   $this->assign('sid',$sid);
			}else{
			    $this->assign('sid','');
			}
		   $rzt=$wedding_staff->where("sid='$sid' and token='$token'")->find();//根据传过来的id查询记录用户信息
		   $did=$rzt['s_did'];//获取父类部门信息id
           $ssid=$rzt['sid'];//获取当前自己的id用户id
           $wedding_staff_department=M('wedding_staff_department');//部门信息表
           $bmxx=$wedding_staff_department->where("did='$did'")->find();  //根据上面给的用户父类id查询出相关部门  
		   $wedding_staff_participate=M('wedding_staff_participate');
		   $list=$wedding_staff_participate->where("sid='$ssid'")->find();//先查出来有没有记录
		   $wedding_staff_comment=M('wedding_staff_comment');//查询每个用户的评论信息
	       $pllist=$wedding_staff_comment->where("sid='$ssid'")->order("cdate desc")->select();
		   $num=count($pllist);
		   $fl=ceil($num/8);
		   
		   //$this->assign('num',$num);   
		   $Page       = new Page($num,8);// 实例化分页类 传入总记录数和每页显示的记录数
		   $show       = $Page->show();// 分页显示输出
		   $pllist1 = $wedding_staff_comment->where("sid='$ssid'")->order("cdate desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		   //var_dump($rzt); 
		   foreach($pllist1 as$k =>$v){

		    $pllist1[$k]['sj']=tranTime($v['cdate']);
		   }
		   $this->assign('rzt',$rzt);
		   $this->assign('bmxx',$bmxx);
		   $this->assign('list',$list);
		   $this->assign('pllist',$pllist1);//用户评论
		   $this->assign('page',$show);//用户评论
		   $this->assign('fl',$fl);//最大页数
		   $this->display();
	}
	//grtplist 针对每个用户投票评分
     public function grtplist(){
		     import("@.ORG.Page");
		     $wedding_staff_participate=M('wedding_staff_participate');
	         $dj=$_REQUEST['dj'];
	         $sid=$_REQUEST['sid'];
	         $token=$_REQUEST['token'];
	         $username=$_REQUEST['username'];
	         $rs=$_REQUEST['rs'];
			 //echo $dj.$sid.$token.$username.$rs;exit;
			 $rrr=$wedding_staff_participate->where("sid='$sid'")->find();//先查出来有没有记录
			 if(empty($rrr)){
					
                   $data['vote']=$dj;
				   $data['sid']=$sid;
				   $data['jlrs']=$rs;
				   //$data['token']=$token;
				  // var_dump($data);exit;
				   $qry=$wedding_staff_participate->add($data); //没有就添加
				    
				   /*cookie('dj',$dj,3600);*/
				  if(cookie('sid')){
						    $sd=cookie('sid');
                            cookie('sid',$sd.'_'.$sid,315360000);
					 }  else{
					      cookie('sid',$sid,315360000);
					
				   }
				  
				   $pfvote=$wedding_staff_participate->where("sid='$sid'")->field('vote,jlrs')->find();//添加完成以后查出来新的分数返回去页面
				   $pfvo['vote']=$pfvote['vote']/$pfvote['jlrs'];//平均数
				   //print_r($pfvo['vote']);exit;
				   $data1['pzvote']=$pfvo['vote'];
                   $pz=$wedding_staff_participate->where("sid='$sid'")->save($data1);

				   $info=sprintf("<div>%s</div>",$pfvo['vote']);
				   echo $info;
				   exit;
				   if($pfvote){
					 $this->ajaxReturn(0,$pfvote,1);
				   }else{
					 $this->ajaxReturn(0,'wocao',-1);
				   }
				   
                   

			 }else{
				
				
				 $zzz=$wedding_staff_participate->where("sid='$sid'")->field('vote,jlrs')->find();//先查出来有没有记录
				 $kkk=$zzz['vote'];
				 $jlnum=$zzz['jlrs'];
				 $dataarr['vote']=$dj+$kkk;//如果原来里面不是空的直接修改里面的分数
				 $dataarr['jlrs']=$rs+$jlnum;//如果原来里面不是空的直接修改里面的人数
                 $zxpzs=($dj+$kkk)/($rs+$jlnum);//最新平均数
				 //echo $zxpzs;exit;
				 $dataarr['pzvote']=$zxpzs;
				 $ss=$wedding_staff_participate->where("sid='$sid'")->save($dataarr);//修改分数
			     
				
				 if($ss){
					
					if(cookie('sid')){
						    $sd=cookie('sid');
                            cookie('sid',$sd.'_'.$sid,315360000);
						}else{
						   cookie('sid',$sid,315360000);
					      
					 }
				   
					 //cookie('username',$username,3600);
					 $xgvote=$wedding_staff_participate->where("sid='$sid'")->field('vote,jlrs,pzvote')->find();//修改以后的分数在查询出来
					 //$xgvo['vote']=$xgvote['vote']/$xgvote['jlrs']; 平均数
					  $info=sprintf("<div>%s</div>",$xgvote['pzvote']);
					  echo $info;
				      exit;
					 if($xgvote){
						 $this->ajaxReturn(0,$xgvote,1);
					  }else{
						 $this->ajaxReturn(0,'cao',-1);
					  }
				 }else{
				     echo '失败'; 
				 }
				 
				 
                 
			       
			 }
			
              
			
	}
	//grpllist 针对个人评论信息
	public function grpllist(){
		    import("@.ORG.Page");
	        $sid=$_REQUEST['ssid'];
	        $token=$_REQUEST['stoken'];
	        $jianjie=$_REQUEST['jianjie'];
			//echo $jianjie.'--'.$sid.'--'.$token;
			$data['sid']=$sid;
			$data['cdate']=time();
			$data['message']=$jianjie;
			$wedding_staff_comment=M('wedding_staff_comment');
            $qry=$wedding_staff_comment->add($data);
			if($qry){
                $pllist=$wedding_staff_comment->where("sid='$sid'")->order('cdate desc')->select();
			    //print_r($pllist);exit;
				//$info=json_encode($pllist);
				foreach($pllist as $key =>$v){
					     /*$info.="<li>";
						 $info.="<span class='red'>匿名：</span>";
						 $info.="<span>".$v['message']."</span>";
						 $info.="<span style='float:right;color:#545454;'><font size='-6px'>".tranTime($v['cdate'])."</font></span>";
						 $info.="</li>";*/

						 $info.="<li>";
						 $info.="<span class='red'>匿名：</span>";
						 $info.="<span>".$v['message']."</span>";
						 $info.="<span style='float:right;color:#545454;'><font size='-6px'>".tranTime($v['cdate'])."</font></span>";
						 $info.="</li>";
					
				   
				}
				
					 echo $info;
			}else{
			    $info=sprintf("<div>%s</div>",'失败');
			    echo $info;
			}
	}


   
}


function tranTime($time) {
    $rtime = date("m-d H:i", $time);
    $htime = date("H:i", $time);
    $time = time() - $time;
    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h = floor($time / (60 * 60));
        $str = $h . '小时前 ';
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time / (60 * 60 * 24));
        if ($d == 1)
            $str = '昨天 ';
        else
            $str = '前天 ';
    } else {
        $str = $rtime;
    }
    return $str;
}

?>