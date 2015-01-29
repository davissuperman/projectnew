<?php
class HostAction extends UserAction{
	public function index(){		
		
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		 if(!strpos($token_open['queryname'],'host_kev')){
                $this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
	
		$data=M('Host');
		$count      = $data->where(array('token'=>$_SESSION['token']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
        $list = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->select(); 
        if(IS_POST){
             
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['keyword|title|tel2|tel'] = array('like',"%$key%"); 
            $list = M('Host')->where($map)->select();             
             
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		$this->display();		
	}
	//删除克隆添加的分类
	public function delklfenl(){
	        $token=$_REQUEST['token'];
	        $fid=$_REQUEST['fid'];
	        $zjid=$_REQUEST['zjid'];
	        //echo $zjid.'...'.$token.'....'.$fid;  
			$host_input=M('host_input'); 
            $qry=$host_input->where("b_fid='$fid' and token='$token' and id='$zjid'")->delete();
			if($qry){
				echo '1';exit;
              
			}else{
				echo '2';exit;
			   
			}
            
	}
	public function alldel(){
		$id=implode(',', $_REQUEST['delid']);
		M('Host_order')->where(array('id'=>array('in',$id)))->delete();
		$this->success('删除成功',U('Host/admin',array('token'=>session('token'),'id'=>$_REQUEST['jdid'])));
	}
	public function set(){
	    $host=M('Host');
		$host_input=M('host_input'); 
		$keyword=M('keyword'); 
		$id=$_GET['id'];
        $token=$_GET['token'];  
		if($_POST){
			  //print_r($_REQUEST);exit;
               $token          =$_POST['token'];
               $inputid          =$_POST['inputid'];//文本框id
               $id             =$_POST['id'];
			   $data['token']=$_POST['token'];
			   $data['keyword']=$_POST['keyword'];
			   $data['title']  =$_POST['title'];
			   $data['address']=$_POST['address'];
			   $data['tel']    =$_POST['tel'];
			   $data['tel2']   =$_POST['tel2'];
			   $data['ppicurl']=$_POST['ppicurl'];
			   $data['headpic']=$_POST['headpic'];
			   //$data['name']=$_POST['name'];
			   //$data['picurl']=$_POST['picurl'];
			   //$data['url']=$_POST['url'];
			   $data['wenxin']=$_POST['wenxin'];
			   $data['info']=$_POST['info'];
			   $data['dituaddress']=$_POST['dituaddress'];
			   $data['jingdu']=$_POST['jingdu'];
			   $data['weidu']=$_POST['weidu'];
			   $data['orderinfo']=$_POST['orderinfo'];
			   $data['creattime']=time();
			   
			   $qry=$host->where("id='$id' and token='$token'")->save($data);
			   if($qry){
				   //如果是自定义选项做修改不管修改什么都是先删除在去修改
				    $host_input->where("b_fid='$id' and token='$token'")->delete();
					for($i=0;$i<count($_REQUEST['zdyname']);$i++){
					    //print_r($_REQUEST);exit;
						$data2['zdynr']  =$_REQUEST['zdynr'][$i];
						$data2['zdyname']=$_REQUEST['zdyname'][$i];
						$data2['zdylx']=$_REQUEST['xz'][$i];
						//$inpid=$_REQUEST['inputid'][$i];
						//$data2['id']=$_REQUEST['inputid'][$i];
						$data2['b_fid']=$id;
						$data2['token']=$token;
						$host_input->add($data2);
					}
					//echo '<br>';
					//print_r($_REQUEST);exit;
				    $data1['keyword']=$_POST['keyword'];
				    $data1['pid']=$id;
				    $data1['token']=$_POST['token'];
				    $data1['module']='Host';
					//print_R($data1);exit;
					$keyword->where("pid='$id' and token='$token'")->save($data1);
			       $this->success('修改成功',U('Host/index',array('token'=>$token)));
			   }else{
			        $this->error('修改失败');
			   }
		}else{
		  $checkdata = $host->where(array('token'=>$token,'id'=>$id))->find();
          $qry=$host_input->where("b_fid='$id' and token='$token'")->select();
		  //print_r($qry);
		  $this->assign('set',$checkdata);
		  $this->assign('qry',$qry);

		  $this->display();
		}
		
		
	}
    public function add(){
	       $host=M('host'); 
	       $keyword=M('keyword'); 
	       $host_input=M('host_input'); 
		   if($_POST){
			   $token          =$_POST['token'];
			   $data['token']=$_POST['token'];
			   $data['keyword']=$_POST['keyword'];
			   $data['title']  =$_POST['title'];
			   $data['address']=$_POST['address'];
			   $data['tel']    =$_POST['tel'];
			   $data['tel2']   =$_POST['tel2'];
			   $data['ppicurl']=$_POST['ppicurl'];
			   $data['headpic']=$_POST['headpic'];
			   //$data['name']=$_POST['name'];
			   //$data['picurl']=$_POST['picurl'];
			   //$data['url']=$_POST['url'];
			   $data['wenxin']=$_POST['wenxin'];
			   $data['info']=$_POST['info'];
			   $data['orderinfo']=$_POST['orderinfo'];
			   $data['dituaddress']=$_POST['dituaddress'];
			   $data['jingdu']=$_POST['jingdu'];
			   $data['weidu']=$_POST['weidu'];
			   $data['creattime']=time();
			   
			   $qry=$host->add($data);
               if($qry){
				    for($i=0;$i<count($_REQUEST['zdyname']);$i++){
					  
						$data2['zdynr']  =$_REQUEST['zdynr'][$i];
						$data2['zdyname']=$_REQUEST['zdyname'][$i];
						$data2['zdylx']=$_REQUEST['xz'][$i];
						$data2['b_fid']=$qry;
						$data2['token']=$token;
						$host_input->add($data2);
					}
				    $data1['keyword']=$_POST['keyword'];
				    $data1['pid']=$qry;
				    $data1['token']=$_POST['token'];
				    $data1['module']='Host';
					$keyword->add($data1);
                    $this->success('操作成功',U('Host/index',array('token'=>$token)));
			   }else{
                      $this->error('操作失败');
			   }
		   }else{
	       $this->display();
	      } 
	}
	/*public function add(){ 
          if(IS_POST){   
            $this->all_insert('Host'); 
          }else{
			$this->display('set');
		  }
	}*/

	public function index_del(){

		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');

        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Host');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');

            $back=$data->where($wehre)->delete();
            if($back==true){
                M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Host'))->delete();
				M('host_input')->where(array('b_fid'=>$id,'token'=>session('token')))->delete();
                $this->success('操作成功',U('Host/index',array('token'=>session('token'))));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Host/index',array('token'=>session('token'))));
            }
        }        
	}

    public function lists(){
        $data=M('Host_list_add');
        $hid = $this->_get('id');
        $count      = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->count();
        $Page       = new Page($count,12);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->limit($Page->firstRow.','.$Page->listRows)->select(); 

        $this->assign('page',$show);        
        $this->assign('li',$li);
        $this->assign('hid',$hid);
        $this->display();

    }

    public function list_add(){
        $hostadd=M('Host_list_add');
		 $token = session('token');
        if(IS_POST){
            
             $data['type']    = $this->_post('type');            
             $data['hid']    = $this->_post('id');            
             $data['typeinfo']= $this->_post('typeinfo');
             $data['price']   = $this->_post('price');
             $data['yhprice'] = $this->_post('yhprice');
             $data['name']    = $this->_post('name');
             $data['picurl']  = $this->_post('picurl');
             $data['fjnum']  = $this->_post('fjnum');
             $data['url']     = $this->_post('url');
             $data['info']    = $this->_post('info');
             $data['token']   = session('token');
             if(empty($data['type']) || 
                empty($data['typeinfo'])||
                empty($data['price'])|| 
                empty($data['yhprice'])|| 
                empty($data['info'])||
				empty($data['fjnum'])
                ) {
                    $this->error("不能为空.");exit;
             }
             $qry=$hostadd ->data($data)->add();
             if($qry){
				 
			   $this->success('操作成功',U('Host/index',array('token'=>$token)));
			 }else{
				  $this->error("操作失败.");exit;
			 }
             //$this->display('list');
        }else{
        	$hid = $this->_get('hid');
        	$this->assign('hid',$hid);
			$this->display();
		}
    }

     public function list_edit(){
        
            $id = $this->_get('id');
            $token = session('token');
			$list_add = M('Host_list_add')->where(array('id'=>$id,'token'=>$token))->find();
            if(IS_POST){
                 $data['type']    = $this->_post('type');
                 $data['typeinfo']= $this->_post('typeinfo');
                 $data['price']   = $this->_post('price');
                 $data['yhprice'] = $this->_post('yhprice');
                 $data['fjnum'] = $this->_post('fjnum');
                 $data['name']    = $this->_post('name');
                 $data['picurl']  = $this->_post('picurl');
                 $data['url']     = $this->_post('url');
                 $data['info']    = $this->_post('info');                  
                 if(empty($data['type']) || 
                    empty($data['typeinfo'])||
                    empty($data['price'])|| 
                    empty($data['yhprice'])|| 
                    empty($data['info'])|| 
					 empty($data['fjnum'])
                    ) {
                        $this->error("不能为空.");exit;
                 }
                 $where = array('id'=>$id,'token'=>session('token'));                 
                 M('Host_list_add')->where($where)->save($data);
                 $this->success('操作成功',U('Host/index',array('token'=>session('token'))));

            }else{
            	$hid = $this->_get('hid');
	        	$this->assign('hid',$hid);
				$this->assign('list',$list_add);
				$this->display();
			}
    }
	public function list_del(){
		$id = $this->_get('id');
            $token = session('token');
		 $data = M('Host_list_add')->where(array('id'=>$id,'token'=>$token))->delete();
		if($data==false){
			$this->error('删除失败');
		}else{
			$this->success('操作成功');
		}
	
	}
    public function admin(){

        $hid = $this->_get('id');        
        $data=M('Host_order');
        $count      = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->count();
        $ok_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>1,'hid'=>$hid))->count();
        $lost_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>2,'hid'=>$hid))->count();
        $no_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>3,'hid'=>$hid))->count();
        //用户取消的订单
		$yhqux_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>4,'hid'=>$hid))->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->limit($Page->firstRow.','.$Page->listRows)->order("book_time desc")->select(); 
        $this->assign('count',$count);
        $this->assign('ok_count',$ok_count);
        $this->assign('no_count',$no_count);
        $this->assign('yhqux_count',$yhqux_count);
        $this->assign('lost_count',$lost_count);
        $this->assign('page',$show);     
        $this->assign('li',$li);
        $this->assign('id',$li[0]['hid']);
        if(IS_POST){
        	if($_POST['search']=="搜索"){
        		$where=array('hid'=>$_POST['hhid'],'room_type'=>array('like','%'.$_POST['room_type'].'%'));
        		$sday=strtotime($_POST['check_in']);
        		$eday=$sday+3600*24;
        		if($_POST['check_in'] && $_POST['tel']){
        			$where['check_in']=array(array('egt',$sday),array('lt',$eday),'and');
        			$where['tel']=array('tel'=>$_POST['tel']);
        		}
        		if($_POST['check_in']){
        			$where['check_in']=array(array('egt',$sday),array('lt',$eday),'and');
        		}
        		if($_POST['tel']){
        			$where['tel']=$_POST['tel'];
        		}
        		$count      = M('Host_order')->where($where)->count();
		        $Page       = new Page($count,20);
		        $show       = $Page->show();
        		$li=M('Host_order')->where($where)->order("book_time desc")->select();
        		$lis=M('Host_order')->where(array('hid'=>23))->order("book_time desc")->select();
        		$this->assign('li',$li);
        		$this->assign('page',$show);
        					$this->display();
        	}else{
           $da['check_in']     = strtotime($this->_post('check_in'));
           $da['order_status'] = $this->_post('status');
           $id = $this->_post('id');
           $hid = $this->_post('hid');
           $token = session('token');
           M('Host_order')->where(array('id'=>$id,'token'=>$token))->save($da);
           $this->success('操作成功',U('Host/admin',array('token'=>session('token'),'id'=>$hid)));
        	}
           
        }else{
			$this->display();
		}
    }

    public function modremark(){
    	M('host_order')->save(array('id'=>$_POST['id'],'service_remark'=>$_POST['val']));
    }
    public function orderDetail(){
    	$id=$_GET['id'];
    	$orders=M('host_order')->where("id=$id")->find();
    	$this->orders=$orders;
    	$this->display();
    }
}


?>