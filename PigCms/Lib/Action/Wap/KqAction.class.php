<?php
class KqAction extends BaseAction{
   
    public function index(){
		 $member_lpq=M('member_lpq');
		
         $member_user=M('member_user');
         $member_kqj =M('member_kqj');//卡券夹
        $token      = $this->_get('token'); 
      
        $wecha_id         = $this->_get('wecha_id');
		
		$list=$member_kqj->table('tp_member_kqj a')->join('tp_member_lpq b on a.lid=b.lid')->where("a.token='$token' and a.lid !=0 and a.openid='$wecha_id'")->select();
	
		$this->assign('list',$list);

	    $this->assign("tab",'lpk');
	    $this->display();
	}

	public function xfkindex(){
		 $member_xfj =M('member_xfj');
         $member_user=M('member_user');
         $member_kqj =M('member_kqj');//卡券夹
         $token      = $this->_get('token'); 
      
         $wecha_id         = $this->_get('wecha_id');
		
		$list=$member_kqj->table('tp_member_kqj a')->join('tp_member_xfj b on a.xid=b.xid')->where("a.token='$token' and a.xid !=0 and a.openid='$wecha_id'")->select();
	
		
		$this->assign('list',$list);
	    $this->assign("tab",'xfk');
	    $this->display();
	}


	//点菜页面查看详细菜品列表
	public function diancailist(){
		   //include('/cart.php');
		   import ("Wap.Action.CartAction" );
		   $hleper_model = new CartAction();
		   $info = $hleper_model->getCartInfo();//取出cookie里面的物品信息
		   //获得购物车的商品的总数量和总价格
           $goods_number_price = $hleper_model->getNumberPrice();
		   $num=$goods_number_price['number'];
		  
	       $token      = $this->_get('token'); 
           $cid        = $this->_get('cid'); 
           $wecha_id         = $this->_get('wecha_id');
		   //echo $token.'>>>'.$cid.'>>>>>>>'.$wecha_id;
	       $diancai_fenlei=M('diancai_fenlei');
		   $list=$diancai_fenlei->where("token='$token'")->select();
		    //菜品分类信息查询
			$diancai_fenlei=M('diancai_fenlei');
			$diancai_caipin=M('diancai_caipin');
		    $list=$diancai_fenlei->where("token='$token'")->select();
			//查询详细菜品信息根据关联cpflid查询
            $cplist1=$diancai_caipin->where("token='$token'")->select(); 
			$count=count($cplist1);
            $Page       = new Page($count,8);
            $show       = $Page->show();
            
		   //print_r($list);
		    $cplist=$diancai_caipin->where("token='$token'")->limit($Page->firstRow.','.$Page->listRows)->select(); 
		    $this->assign("cplist",$cplist);
		    $this->assign("page",$show);
		    $this->assign("list",$list);
		    $this->assign("num",$num);//购物车的数量输出到页面
		    $this->display(); 
	}
	
  
}
    
?>