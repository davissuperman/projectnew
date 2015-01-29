<?php
class ProductAction extends UserAction{
	public $token;
	public $product_model;
	public $product_cat_model;
	public $isDining;
	public function _initialize() {
		parent::_initialize();
		//
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		//修改权限控制
		if($_GET['dining']==1){
				if(strpos($token_open['queryname'],'dx')){
				  //echo 'YES11';exit;
			}else{
			   $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
			} 
		}else{
			if(strpos($token_open['queryname'],'shop')){
				//echo 'YES';exit;
			}else{
				$this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));  
			}
		}
		//是否是餐饮
		if (isset($_GET['dining'])&&intval($_GET['dining'])){
			$this->isDining=1;
		}else {
			$this->isDining=0;
		}
		$this->assign('isDining',$this->isDining);
	}
		public function index(){
			$catid=$_GET['catid'];
			$catid=$catid==null?0:$catid;
			$product_model=M('Product');
			$product_cat_model=M('Product_cat');
			$where=array('token'=>session('token'));
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			$cats=$product_cat_model->where($catWhere)->select();
			$catsOptions=$this->catOptions($cats,0);
			if ($catid){
				$where['catid']=$catid;
			}else{
				$where['catid']=array('neq',0);
			}
	        if(IS_POST){
				$where = " Where 1=1";
				if( !empty( $_POST[ 'startime' ] ) )$where .=" and time>=" . strtotime( $_POST[ 'startime' ] );
				if( !empty( $_POST[ 'endtime' ] ) )$where .=" and time<=" . strtotime( $_POST[ 'endtime' ] );
				if( !empty( $_POST[ 'catid' ] ) )$where .=" and catid=" . $_POST[ 'catid' ];
			/*	if( !empty( $_POST[ 'putaway' ] ) )*/$where .=" and putaway=" . $_POST[ 'putaway' ];
				// if( !empty( $_POST[ 'p' ] ) )$where .=" and price>=" . $_POST[ 'p' ];
				// if( !empty( $_POST[ 't' ] ) )$where .=" and price<=" . $_POST[ 't' ];
				$sq = "SELECT * FROM tp_product" . $where;
			//	echo $sq;echo strtotime( "2014-04-15 21:55:57" );echo date( "Y-m-d","1397059200" );return false;
	            $lists = $product_model->query( $sq );
	            // var_dump($lists);die;
				$sql1 = "SELECT count(*) as c FROM tp_product" . $where;
	            $count      = $product_model->query( $sql1 );
				$count = $count[0][ 'c' ];
	            $Page       = new Page($count,15);
	        	$show       = $Page->show();
	        }else{
	        	$count      = $product_model->where($where)->count();
	        	$Page       = new Page($count,15);
	        	$show       = $Page->show();
	        	$lists = $product_model->where($where)->order('sort asc')->limit($Page->firstRow.','.$Page->listRows)->select();
	        }
			$cat=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->select();
			$cou=count($lists);
			for ($i=0; $i <$cou; $i++) { 
				for ($j=0; $j <count($cat) ; $j++) { 
					# code...
					if($lists[$i]['catid']==$cat[$j]['id']){
						if($cat[$j]['host']=="activ"){
							unset($lists[$i]);
						}
						else{
							$lists[ $i ][ 'classname' ] = $cat[ $j ][ 'name' ];
						}
						continue;
					}
				}
			}
			$this->assign('catsOptions',$catsOptions);
			$this->options=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->select();
			$this->assign('page',$show);		
			$this->assign('list',$lists);
			$this->assign('isProductPage',1);
			$this->display();		
		}

	/*public function modTop(){
		$id=$_GET['id'];
		$name=$_GET['val'];
		M('product_cat')->where(array('id'=>$id))->save(array('name'=>$name));
	}*/
	public function modremark(){
		$res=M("product_shop")->save(array("id"=>$_POST['id'],"service_remark"=>$_POST['val']));
	}
	public function cats(){		
		$token=session('token');
		$categories=M('product_cat')->where(array('sid'=>0,'token'=>$token))->find();
		if(!$categories){
			M('product_cat')->add(array('token'=>$token,'sid'=>0,'name'=>'热卖','host'=>'host'));
			M('product_cat')->add(array('token'=>$token,'sid'=>0,'name'=>'促销','host'=>'activ'));
		}
		$this->categories=M('product_cat')->where(array('sid'=>0,'token'=>$token))->select();
		/*
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'adma')){
            $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));}
		 */
		$parentid=intval($_GET['parentid']);
		$parentid=$parentid==''?0:$parentid;
		$data=M('Product_cat');
		$where=array('token'=>session('token'));
        if(IS_POST){
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['name|des'] = array('like',"%$key%"); 
            $count      = $data->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
            $list_1 = $data->where($where)->select();
            $classA_2 = $data->where($map)->select(); 
            for( $i=0;$i<=count( $classA_2 );$i++ ){
					for( $j=0;$j<count( $list_1 );$j++ ){
								if( $classA_2[ $i ][ 'sid' ] == $list_1[ $j ][ 'id' ] ){
											$classA_2[ $i ][ 'classname' ] = $list_1[ $j ][ 'name' ];
								}
						}
					}
					$this->assign('list',$classA_2);//修改
        }else{
			$count      = $data->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $data->where(array('token'=>session('token'),'sid'=>array('eq',0),'host'=>array('neq','activ')))->order("sort asc")->limit($Page->firstRow.','.$Page->listRows)->select();
        	$list_1 = $data->where(array('token'=>session('token'),'sid'=>array('neq',0)))->limit($Page->firstRow.','.$Page->listRows)->order("sort asc")->select();
			//$lista = $data->where($where)->select();
				for( $i=0;$i<count( $list_1 );$i++ ){
								if( count(explode(',',$list_1[$i]['sid']))==1 ){
									if($list_1[$i]['sid']>0){
										if($list_1[$i]['sid']==$list[0]['id'])
											$list_1[ $i ][ 'classname' ] = $list[0][ 'name' ];
										else
											$list_1[ $i ][ 'classname' ] = $list[1][ 'name' ];
									}else
											$list_1[ $i ][ 'classname' ] = '未分类';
								}else{
									$list_1[ $i ][ 'classname' ] = $list[0][ 'name' ].' '.$list[1][ 'name' ];
								}
						
					}
					$this->assign('list',$list_1);//修改
			}
				
		$this->assign('page',$show);
		$this->assign('color',$list_1['color']);
		$this->tops=$list;
		
		if ($parentid){
			$parentCat = $data->where(array('id'=>$parentid))->find();
		}
			//$product_cat_model=M('Product_cat');
			$class = $data->where( array( $where ) )->select();
			$count = $data->where( array( 'sid'=>0,$where ) )->select();
		for( $i=0;$i<count( $count );$i++ ){
						for( $j=0;$j<count( $class );$j++ ){
											if( $count[ $i ][ 'id' ] == $class[ $j ][ 'sid' ] ){
													$count[ $i ][ 'son' ][] = $class[ $j ];
											}
						}
			}
			unset( $class );
		$this->assign( 'class',$count );
		$this->assign('parentCat',$parentCat);
		$this->assign('parentid',$parentid);
		$this->display();		
	}
	public function catsL(){
			$shop = M( "product_cat" )->where( array( 'token'=>$this->_get( 'token',trim ),'sid'=>0 ) )->select();
			$this->Assign( 's',$shop );
			$this->Display();
	}
	public function catAdd(){
		if(IS_POST){
			if ($this->isDining){
				$this->insert('Product_cat','/cats?dining=1&parentid='.$this->_post('parentid'));
			}else {
			$this->insert('Product_cat','/cats?parentid='.$this->_post('parentid'));
			}
		}else{
			$product_cat_model=M('Product_cat');
			$class = $product_cat_model->where( array( 'token'=>$this->_get( 'token',trim ) ) )->select();
			$count = $product_cat_model->where( array( 'sid'=>0,'token'=>$this->_get( 'token',trim ) ) )->select();
		for( $i=0;$i<count( $count );$i++ ){
						for( $j=0;$j<count( $class );$j++ ){
											if( $count[ $i ][ 'id' ] == $class[ $j ][ 'sid' ] ){
													$count[ $i ][ 'son' ][] = $class[ $j ];
											}
						}
			}
			unset( $class );
			$this->assign( 'class',$count );
			$this->assign( 'sid',$class[ 'sid' ] );
			$parentid=intval($_GET['parentid']);
			$parentid=$parentid==''?0:$parentid;
			$this->assign('parentid',$parentid);
			$this->display('catSet');
		}
	}
	public function catDel(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Product');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Product/cats',array('token'=>session('token'),'dining'=>$this->isDining)));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	if (!$this->isDining){
                $this->success('操作成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$check['parentid'])));
            	}else {
            		$this->success('操作成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$check['parentid'],'dining'=>1)));
            	}
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/cats',array('token'=>session('token'))));
            }
        }        
	}
	public function catSet(){
        $id = $this->_get('id'); 
		$checkdata = M('Product_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Product/catAdd'));
        }
		if(IS_POST){ 
            $data=D('Product_cat');
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					if (!$this->isDining){
						$this->success('修改成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$this->_post('parentid'))));
					}else {
						$this->success('修改成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$this->_post('parentid'),'dining'=>1)));
					}
					
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$product_cat_model=M('Product_cat');
			$class = $product_cat_model->where( array( 'token'=>$this->_get( 'token',trim ) ) )->select();
			$count = $product_cat_model->where( array( 'sid'=>0,'token'=>$this->_get( 'token',trim ) ) )->select();
		for( $i=0;$i<count( $count );$i++ ){
						for( $j=0;$j<count( $class );$j++ ){
											if( $count[ $i ][ 'id' ] == $class[ $j ][ 'sid' ] ){
													$count[ $i ][ 'son' ][] = $class[ $j ];
											}
						}
			}
			$this->assign( 'class',$count );
			$this->assign( 'sid',$class[ 'sid' ] );
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display();	
		}
	}
	public function alldel(){
		$ids=implode(',', $_REQUEST['alldel']);
		if(!$ids){
			$this->error("没有勾选商品");
			return;
		}
		M('Product')->where(array('id'=>array('in',$ids)))->delete();
		$this->success('删除成功',U('index',array('token'=>session('token'))));
	}
	public function add(){ 
		if(IS_POST){
			$_POST['keyword']='';
			if(!$_POST['discount'])
				$_POST['discount']=null;
			$this->all_insert('Product','/index?token='.session('token').'&dining='.$this->isDining);
		}else{
			//分类
			$data=M('Product_cat');
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			if ($this->isDining){
				$catWhere['dining']=1;
			}else {
				$catWhere['dining']=0;
			}
			$cats=$data->where($catWhere)->select();
			if (!$cats){
				 $this->error("请先添加分类",U('Product/catAdd',array('token'=>session('token'),'dining'=>$this->isDining)));
				 exit();
			}
			$this->assign('cats',$cats);
			$catsOptions=$this->catOptions($cats,0);
			$this->assign('catsOptions',$catsOptions);
			$this->options=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->select();
			//
			$this->assign('isProductPage',1);
			$this->display('set');
		}
	}
	/**
	 * 商品类别ajax select
	 *
	 */
	public function ajaxCatOptions(){
		$parentid=intval($_GET['parentid']);
		$data=M('Product_cat');
		$catWhere=array('parentid'=>$parentid,'token'=>session('token'));
		$cats=$data->where($catWhere)->select();
		$str='';
		if ($cats){
			foreach ($cats as $c){
				$str.='<option value="'.$c['id'].'">'.$c['name'].'</option>';
			}
		}
		$this->show($str);
	}
		public function set(){//修改
	        $id = $this->_get('id'); 
	        $product_model=M('Product');
	        $product_cat_model=M('Product_cat');
			$checkdata = $product_model->where(array('id'=>$id))->find();
			if(empty($checkdata)){
	            $this->error("没有相应记录.您现在可以添加.",U('Product/add'));
	        }
			if(IS_POST){
				if(!$_POST['discount'])
				$_POST['discount']=null;
	            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
				$check=$product_model->where($where)->find();
				if($check==false)$this->error('非法操作');
				if($product_model->create()){
					if($product_model->where($where)->save($_POST)){
						$this->success('修改成功',U('Product/index',array('token'=>session('token'),'dining'=>$this->isDining)));
						$keyword_model=M('Keyword');
						$keyword_model->where(array('token'=>session('token'),'pid'=>$this->_post('id'),'module'=>'Product'))->save(array('keyword'=>$this->_post('keyword')));
					}else{
						$this->error('操作失败');
					}
				}else{
					$this->error($product_model->getError());
				}
			}else{
				//分类
				$catWhere=array('parentid'=>0,'token'=>session('token'));
				if ($this->isDining){
					$catWhere['dining']=1;
				}
				$cats=$product_cat_model->where($catWhere)->select();
				$this->assign('cats',$cats);
				
				$thisCat=$product_cat_model->where(array('id'=>$checkdata['catid']))->find();
				$childCats=$product_cat_model->where(array('parentid'=>$thisCat['parentid']))->select();
			/*	$etalon=explode('^', $checkdata['etalon']);
				$num=count($etalon);
				unset($etalon[$num-1]);*/

				$eta=explode('^', $checkdata['etalon']);
				unset($eta[count($eta)-1]);
				for($i=0;$i<count($eta);$i++){
					$eta[$i]=explode(',',$eta[$i]);
				}
				$checkdata['etalon']=$eta;
				$this->etaFirst=$checkdata['etalon'][0];
				$etaCount=count($checkdata['etalon']);
				if($etaCount>1){
					array_shift($checkdata['etalon']);
					$this->etalons=$checkdata['etalon'];
				}
				$this->logourl=explode('^', $checkdata['logourl']);
				$this->assign('thisCat',$thisCat);
				$this->assign('parentCatid',$thisCat['parentid']);
				$this->assign('n',$num-1);
				// $this->assign('etalon',$etalon);
				$this->assign('childCats',$childCats);
				$this->assign('isUpdate',1);
				$catsOptions=$this->catOptions($cats,$checkdata['catid']);
				$this->putaway=$checkdata['putaway'];
				$this->assign('catsOptions',$catsOptions);
				$this->assign('catid',$checkdata['catid']);
				$this->options=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->select();
				//
				$this->assign('set',$checkdata);
				$this->assign('isProductPage',1);
				$this->display();	
			
			}
		}
	//商品类别下拉列表
	public function catOptions($cats,$selectedid){
		$str='';
		if ($cats){
			foreach( $cats as $val ){
						if( $val[ 'sid' ] == 0  ){
								$v[] = $val;
						}
			}	
			for( $i = 0;$i<count( $v );$i++ ){
						for( $j = 0;$j<count( $cats );$j++ ){
								if( $v[ $i ][ 'id' ] == $cats[ $j ][ 'sid' ] ){
										$cs[ $v[ $i ][ 'name' ] ][] = $cats[ $j ];
								}
						}
			}
			foreach( $cs as $key=>$c ){
						$str .='<option value="0">|-'.$key.'</option>';
							foreach( $c as $va ){
								if( $selectedid == $va[ 'id' ] ){
									$str .='<option value="'.$va[ 'id' ].'"selected=\"selected\">&nbsp&nbsp|--'.$va[ 'name' ].'</option>';
								}else{
									$str .='<option value="'.$va[ 'id' ].'">&nbsp&nbsp|--'.$va[ 'name' ].'</option>';
								}				
					}
			}
		}
		return $str;
	}
	public function del(){
		$product_model=M('Product');
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false)   $this->error('非法操作');

            $back=$product_model->where($wehre)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success('操作成功',U('Product/index',array('token'=>session('token'),'dining'=>$this->isDining)));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/index',array('token'=>session('token'))));
            }
        }        
	}
	public function orders(){
		$product_cart_model=M('product_cart');
		if (IS_POST){
			if ($_POST['token']!=$this->_session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success('操作成功',U('Product/orders',array('token'=>session('token'),'dining'=>$this->isDining)));
		}else{
			

			$where=array('token'=>$this->_session('token'));
			if ($this->isDining){
				$where['dining']=1;
			}else {
				$where['dining']=0;
			}
			$where['groupon']=array('neq',1);
			if(IS_POST){
				$key = $this->_post('searchkey');
				if(empty($key)){
					$this->error("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->limit($Page->firstRow.','.$Page->listRows)->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
			}else {
				if (isset($_GET['handled'])){
					$where['handled']=intval($_GET['handled']);
				}
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
				$orders=$product_cart_model->where($where)->order('time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}


			$unHandledCount=$product_cart_model->where(array('token'=>$this->_session('token'),'handled'=>0))->count();
			$this->assign('unhandledCount',$unHandledCount);


			$this->assign('orders',$orders);

			$this->assign('page',$show);
			$this->display();
		}
	}
	public function orderInfo(){
		$this->product_model=M('Product');
		$this->product_cat_model=M('Product_cat');
		$product_cart_model=M('product_cart');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		if (strtolower($thisOrder['token'])!=strtolower($this->_session('token'))){
			exit();
		}
		if (IS_POST){
			if (intval($_POST['sent'])){
				$_POST['handled']=1;
			}
			$product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('sent'=>intval($_POST['sent']),'logistics'=>$_POST['logistics'],'logisticsid'=>$_POST['logisticsid'],'handled'=>1));
			
			$this->success('修改成功',U('Product/orderInfo',array('token'=>session('token'),'id'=>$thisOrder['id'])));
		}else {
			//订餐信息
			$product_diningtable_model=M('product_diningtable');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}
			//
			$this->assign('thisOrder',$thisOrder);
			$carts=unserialize($thisOrder['info']);

			//
			$totalFee=0;
			$totalCount=0;
			$products=array();
			$ids=array();
			foreach ($carts as $k=>$c){
				if (is_array($c)){
					$productid=$k;
					$price=$c['price'];
					$count=$c['count'];
					//
					if (!in_array($productid,$ids)){
						array_push($ids,$productid);
					}
					$totalFee+=$price*$count;
					$totalCount+=$count;
				}
			}
			if (count($ids)){
				$list=$this->product_model->where(array('id'=>array('in',$ids)))->select();
			}
			if ($list){
				$i=0;
				foreach ($list as $p){
					$list[$i]['count']=$carts[$p['id']]['count'];
					$i++;
				}
			}
			$this->assign('products',$list);
			//
			$this->assign('totalFee',$totalFee);
			$this->display();
		}
	}
	public function deleteOrder(){
		$product_model=M('product');
		$product_cart_model=M('product_cart');
		$product_cart_list_model=M('product_cart_list');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		$id=$thisOrder['id'];
		if ($thisOrder['token']!=$this->_session('token')){
			exit();
		}
		//
		//删除订单和订单列表
		$product_cart_model->where(array('id'=>$id))->delete();
		$product_cart_list_model->where(array('cartid'=>$id))->delete();
		//商品销量做相应的减少
		$carts=unserialize($thisOrder['info']);
		foreach ($carts as $k=>$c){
			if (is_array($c)){
				$productid=$k;
				$price=$c['price'];
				$count=$c['count'];
				$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
			}
		}
		$this->success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	//桌台管理
	public function tables(){
		$product_diningtable_model=M('product_diningtable');
		if (IS_POST){
			if ($_POST['token']!=$this->_session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success('操作成功',U('Product/orders',array('token'=>session('token'))));
		}else{
			

			$where=array('token'=>$this->_session('token'));
			if(IS_POST){
				$key = $this->_post('searchkey');
				if(empty($key)){
					$this->error("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
			}else {
				
				$count      = $product_diningtable_model->where($where)->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
				$tables=$product_diningtable_model->where($where)->order('taxis ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}

			$this->assign('tables',$tables);
			$this->assign('page',$show);
			$this->display();
		}
	}
	public function tableAdd(){ 
		if(IS_POST){
			$this->insert('Product_diningtable','/tables?dining=1');
		}else{
			$this->display('tableSet');
		}
	}
	public function tableSet(){
		$product_diningtable_model=M('product_diningtable');
        $id = $this->_get('id'); 
		$checkdata = $product_diningtable_model->where(array('id'=>$id))->find();
		if(IS_POST){ 
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$product_diningtable_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($product_diningtable_model->create()){
				if($product_diningtable_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	public function tableDel(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $product_diningtable_model=M('product_diningtable');
            $check=$product_diningtable_model->where($where)->find();
            if($check==false)   $this->error('非法操作');
           
            $back=$product_diningtable_model->where($wehre)->delete();
            if($back==true){
            	$this->success('操作成功',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
            }
        }        
	}
	public function reply(){
				$token = $this->_get( 'token','trim' );
				$token = !empty( $token ) ? $token : exit;
					 if( IS_POST ){
						$title = $this->Gl( $_POST[ 'title' ] );
						$info = $this->Gl( $_POST[ 'info' ] );
						$picurl = $this->Gl( $_POST[ 'picurl' ] );
						$apiurl = $this->Gl( $_POST[ 'apiurl' ] );
						$token = $this->Gl( $_POST[ 'token' ] );
						$M = M( 'product_reply' );
						$S = $M->where( array( 'token'=>$token ) )->find();
						if( empty( $S ) ){
								$data[ 'utitle' ] = $title;
								$data[ 'udescription' ] = $info;
								$data[ 'upicurl' ] = $picurl;
								$data[ 'uurl' ] = $apiurl;
								$data[ 'token' ] =$token;
								$id = $M->add( $data );
								if( $id ){
									$this->success('回复添加成功',U('User/Product/reply',array('token'=>$token)));exit;							
								}else{
									$this->error( '回复添加失败',U( 'User/Product/reply',array('token'=>$token) ) );	
																							}							
										}else{
											$data[ 'utitle' ] = $title;
											$data[ 'udescription' ] = $info;
											$data[ 'upicurl' ] = $picurl;
											$data[ 'uurl' ] = $apiurl;
											$data[ 'token' ] =$token;
											$m = $M->where( array( 'token'=>$token ) )->save( $data );
											if( $m ){
													$this->success('回复更新添加成功',U('User/Product/reply',array('token'=>$token)));exit;
																	}else{
																	$this->error( '回复更新添加失败',U( 'User/Product/reply',array('token'=>$token) ) );		
															}
											}
								}else {
										$d = M( 'product_reply' );
										$in = $d->where( array( 'token'=>$token ) )->find();
										$this->Assign( 'title',$in[ 'utitle' ] );
										$this->Assign( 'info',$in[ 'udescription' ] );
										$this->Assign( 'picurl',$in[ 'upicurl' ] );
										$this->Assign( 'apiurl',$in[ 'uurl' ] );
										$this->Assign( 'token',$token );	
							} 
							$this->Display();	
	}
	Private function Gl( $g ){
			$G = !empty( $g ) ? trim( $g ) : exit; 
			$G = htmlspecialchars($G, ENT_QUOTES);
			if( !get_magic_quotes_gpc() ){
				$G = addslashes( $G );
			}
				return $G;	
	}	
	public function shop_index(){
		$i = M( "product_pic" )->Where( array( 'token'=>session('token') ) )->find();
		if( IS_POST ){
				if( empty( $_POST[ 'id' ] ) ){
					$this->all_insert('Product_pic','/shop_index?token='.session('token'));exit;	
				}else{
					$Data[ 'Shopic' ] = $_POST[ 'Shopic' ];
					$Data[ 'Shopics' ] = $_POST[ 'Shopics' ];
					$t = M( "product_pic" )->where( array( 'id'=>$_POST[ 'id' ],'token'=>session('token') ) )->save( $Data );
				}
				if( $t ){
                $this->success('操作成功',U('Product/shop_index',array('token'=>session('token'))));exit;
				}else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/shop_index',array('token'=>session('token'))));exit;
            }
		}
		$this->Assign( "token",session('token') );
		$this->Assign( "i",$i );
		$this->Display();
	}
	public function Insert(){
			$product_cat_model=M('Product_cat');
			$I[ 'name' ] = trim( $_POST[ 'name' ] );
			$I[ 'sid' ] = $_POST[ 'catid' ]==true? $_POST[ 'catid' ]:-1;
			$I[ 'Ename' ] = trim( $_POST[ 'b' ] );
			$I[ 'token' ] = trim( $_POST[ 'token' ] );
			$I[ 'logourl' ] = trim( $_POST[ 'e' ] );
			$I[ 'host' ] = $_POST[ 'c' ]==true?trim( $_POST[ 'c' ] ):-1;
			$I[ 'color' ] = trim( $_POST[ 'd' ] );
			$I[ 'time' ] = time();
			if( $_POST[ 'save' ] ){
				$iz = $product_cat_model->where( array( "id"=>$_POST[ 'save' ] ) )->save( $I );		
			}else{
							$iz = $product_cat_model->add( $I );	
			}
			if( $iz ){
				echo 1;
			}else{
				echo 0;	
			} 	
	}
	public function edit(){
		$product_cat_model=M('Product_cat');
		$id = $_POST[ 'id' ];
		$token = $_POST[ 'token' ];
		$value = $product_cat_model->where( array( 'token'=>$token,'id'=>$id ) )->find();
		echo json_encode( $value );	
	}
	public function take(){
			$order = M( 'product_order' )->where( array( 'token'=>$this->_get( 'token',trim ) ) )->select();
			foreach( $order as $key=>$val ){
					if( $val[ 'pvid' ] == $val[ 'ctid' ] ){
					$province = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}else{
					$province = M( "province" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$district = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'ctid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}
					$order[ $key ][ 'test' ] = $province[ 'name' ].$district[ 'name' ].$city[ 'name' ];	
			}
			$this->Assign( 't',$order );
			$this->Display();
	}
		public function usertake(){
			$order = M( 'product_order' )->where( array( 'token'=>$this->_get( 'token',trim ) ) )->select();
			foreach( $order as $key=>$val ){
					if( $val[ 'pvid' ] == $val[ 'ctid' ] ){
					$province = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}else{
					$province = M( "province" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$district = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'ctid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}
					$order[ $key ][ 'test' ] = $province[ 'name' ].$district[ 'name' ].$city[ 'name' ];	
			}
			$this->Assign( 't',$order );
			$this->Display();
	}
	public function express(){
			$token = $this->_get( 'token',trim );
			$list = M( "product_express" )->where( array( 'token'=>$token ) )->select();
			$this->Assign( 'li',$list );	
			$this->Display();
	}
	public function expressAdd(){
				$i = M( "product_express" );
				$add['token'] = $_POST[ 'token' ];
				$add['name'] = $_POST[ 'name' ];
				$add['price'] = $_POST[ 'b' ];
				$add['sort'] = $_POST[ 'd' ];
				$add[ 'is' ] = $_POST[ 'c' ];
				if( $_POST[ 'save' ] ){
						$iz = $i->where( array( 'id'=>$_POST[ 'save' ] ) )->save( $add );		
				}else{
						$iz = $i->add( $add );
				}
				if( $iz ){
					echo 1;
				}else{
					echo 0;	
				}				
	}
	public function ia(){
			$l = M( "product_express" )->where( array( 'token'=>$_POST[ 'token' ],'id'=>$_POST[ 'id' ] ) )->find();
			echo json_encode( $l );
	}
	public function iDe(){
			$m = M( 'product_express' );
			$i = $m->where( array( 'id'=>$this->_get( 'id',intval ) ) )->delete();
			if($i){
                $this->success('操作成功',U('Product/express',array('token'=>session('token'))));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/express',array('token'=>session('token'))));
            }
	}
	public function management(){
				$w = 'Where 1=1 ';
				$m = M( 'product_shop' );
				if( IS_POST ){
							if( !empty( $_POST[ 'ordernumber' ] ) )$w .= "and number='".$_POST[ 'ordernumber' ]."'";
							if( !empty( $_POST[ 'username' ] ) )$w .= " and user='".$_POST[ 'username' ]."'";
							if( !empty( $_POST[ 'state' ] ) )$w .= " and sc=".$_POST[ 'state' ];
							if( !empty( $_POST[ 'starttime' ] ) )$w .= " and time>=".strtotime( $_POST[ 'starttime' ] );
							if( !empty( $_POST[ 'endtime' ] ) )$w .= " and time<=".strtotime( $_POST[ 'endtime' ] );
							if( !empty( $_POST[ 'hprice' ] ) )$w .= " and dt>=".$_POST[ 'hprice' ];
							if( !empty( $_POST[ 'fprice' ] ) )$w .= " and dt<=".$_POST[ 'fprice' ];
							if( !empty( $_POST[ 'delivery_status' ]  )|| isset($_POST[ 'delivery_status' ] ) )$w .= " and delivery_status=".$_POST[ 'delivery_status' ];
							$w .= " and token='".$this->_get( 'token',trim )."'";
							$sql = "SELECT * FROM tp_product_shop ".$w;
							//echo $sql;exit;
							$orDerList = $m->query( $sql );
				}else{
							$orDerList = $m->Where( array( 'token'=>$this->_get( 'token',trim ) ) )->select();
				}
				foreach( $orDerList as $key=>$val ){
							$orDer = unserialize( $val[ 'il' ] );
							$orDerList[ $key ][ 'price' ] = ( $orDer[ 'price' ]*$orDer[ 'bc' ] );	
				}
				$this->Assign( 'oRder',$orDerList );
				$this->Display();
	}
	public function delivery(){
		$data['id']=$_POST['id'];
		$data['delivery_status']=$_POST['delivery_status'];
		$res=M('product_shop')->save($data);
		if($res){
		echo 1;
		}
		else echo 0;
	}
	public function manaDel(){
				$m = M( 'product_shop' );
				$orDerList = $m->Where( array( 'token'=>$this->_get( 'token',trim ),'id'=>$this->_get( 'id',intval ) ) )->delete();
				if($orDerList){
                $this->success('操作成功',U('Product/management',array('token'=>session('token'))));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/management',array('token'=>session('token'))));
            }
	}
	public function manalist(){
			$id = $this->_get( 'id',intval );
			$token = $this->_get( 'token',trim );
			$li = M( "product_shop" )->where( array( 'id'=>$id,'token'=>$token ) )->find();
			$lis = unserialize( $li[ 'il' ] );
			$this->Assign( 'i',$lis );
			$this->Assign( 'l',$li );	
			$this->Display();
	}
	public function brand(){
		$i = M( "product_brand" )->Where( array( 'token'=>session('token') ) )->find();
		if( IS_POST ){
				if( empty( $_POST[ 'id' ] ) ){
					$this->all_insert('Product_brand','/brand?token='.session('token'));exit;	
				}else{
					$Data[ 'url' ] = $_POST[ 'url' ];
					$Data[ 'text' ] = $_POST[ 'text' ];
					$t = M( "product_brand" )->where( array( 'id'=>$_POST[ 'id' ],'token'=>session('token') ) )->save( $Data );
				}
				if( $t ){
                $this->success('操作成功',U('Product/brand',array('token'=>session('token'))));exit;
				}else{
                 $this->error('服务器繁忙,请稍后再试',U('Product/brand',array('token'=>session('token'))));exit;
            }
		}
				$this->Assign( 'url',$i[ 'url' ] );
				$this->Assign( 'text',$i[ 'text' ] );
				$this->Assign( 'id',$i[ 'id' ] );
				$this->Assign( 'token',$this->_get( 'token',trim ) );	
				$this->Display();
	}	
	public function miaogou(){
		if(IS_POST){
			$where=array('token'=>session('token'),'limit'=>array('neq',0),/*'catid'=>$_POST['catid'],*/'name'=>array('like','%'.$_POST['keyWord'].'%'));
			if($_POST['price1'] && $_POST['price2'])
				$where['price']=array(array('EGT',$_POST['price1']),array('ELT',$_POST['price2']),'AND');
		}else{
			$where=array('token'=>session('token'),'limit'=>array('neq',0));
		}
			import('ORG.Util.Page');// 导入分页类
			$count= M('product')->where($where)->count();// 查询满足要求的总记录数
			$Page= new Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
			$show= $Page->show();// 分页显示输出

			$miaogous=M('product')->where($where)->field('id,name,catid,price,oprice,limit')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			$cats=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0),'host'=>array(array('eq','activ'),array('eq','activ,host'),'or')))->field('id,name')->select();
			foreach($cats as $cat){
				foreach($miaogous as $k=>$miaogou){
					if($miaogou['catid']==$cat['id']){
						$miaogous[$k]['catid']=$cat['name'];
						continue;
					}
				}
			}
			$this->page=$show;
			$this->miaogous=$miaogous;
		$id=M('product_cat')->where(array('token'=>session('token'),'sid'=>0,'host'=>'activ'))->getField('id');
		$sids=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->field('id,name,sid')->select();
		$cats=array();
		foreach($sids as $sid){
			if(in_array($id,explode(',',$sid['sid']))){
				$cats[]=$sid;
			}
		}
		$this->cats=$cats;
		$this->display();
	}
	public function miaogouAdd(){
		if(IS_POST){
			$_POST['token']=session('token');
			$_POST['time_start']=strtotime($_POST['time_start']);
			$_POST['time_end']=strtotime($_POST['time_end']);
			M('product')->create();
			$res=M('product')->add();
			if($res){
				$this->success('添加成功~',U('Product/miaogou',array('token'=>session('token'))));
			}else{
				$this->error('未知错误,添加失败~');
			}
		}else{
			$id=M('product_cat')->where(array('token'=>session('token'),'sid'=>0,'host'=>'activ'))->getField('id');
			$sids=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->field('id,name,sid')->select();
			$cats=array();
			foreach($sids as $sid){
				if(in_array($id,explode(',',$sid['sid']))){
					$cats[]=$sid;
				}
			}
			// var_dump($cats);die;
			/*if(!$cats)
				$this->error('请先创建分类');*/
			$this->cats=$cats;
			$this->display();
		}
	}
	public function miaogouSet(){
		if(IS_POST){
			$_POST['token']=session('token');
			$_POST['time_start']=strtotime($_POST['time_start']);
			$_POST['time_end']=strtotime($_POST['time_end']);
			M('product')->create();
			$res=M('product')->save();
			if($res){
				$this->success('修改成功~',U('Product/miaogou',array('token'=>session('token'))));
			}else{
				$this->error('未知错误,添加失败~');
			}
		}else{
			$set=M('product')->where(array('id'=>$_GET['id'],'session'=>session('token')))->find();
			$id=M('product_cat')->where(array('token'=>session('token'),'sid'=>0,'host'=>'activ'))->getField('id');
			$sids=M('product_cat')->where(array('token'=>session('token'),'sid'=>array('neq',0)))->field('id,name,sid')->select();
			$cats=array();
			foreach($sids as $sid){
				if(in_array($id,explode(',',$sid['sid']))){
					$cats[]=$sid;
				}
			}
			$eta=explode('^', $set['etalon']);
			unset($eta[count($eta)-1]);
			for($i=0;$i<count($eta);$i++){
				$eta[$i]=explode(',',$eta[$i]);
			}
			$set['etalon']=$eta;
			$this->etaFirst=$set['etalon'][0];
			$etaCount=count($set['etalon']);
			if($etaCount>1){
				array_shift($set['etalon']);
				$this->etalons=$set['etalon'];
			}
			$set['time_start']=date('Y-m-d H:i:s',$set['time_start']);
			$set['time_end']=date('Y-m-d H:i:s',$set['time_end']);
			$this->cats=$cats;
			$this->set=$set;
			$this->isUpdate=1;
			$this->display('miaogouAdd');
		}
	}
	public function miaogouDel(){
		M('product')->where(array('id'=>$_GET['id'],'token'=>$_GET['token']))->delete();
		$this->success('删除成功~',U('Product/miaogou',array('token'=>$_GET['token'])));
	}
	public function miaogouRule(){
		import('ORG.Util.Page');// 导入分页类
		$count=M('product_rule')->where(array('token'=>session('token')))->count();// 查询满足要求的总记录数
		$Page= new Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$show= $Page->show();// 分页显示输出
		$rules=M('product_rule')->where(array('token'=>session('token')))->field('image,time',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->rules=$rules;
		$this->page=$show;
		$this->display();
	}
	public function miaogouShow(){
		M('product_rule')->where(array('id'=>$_POST['id']))->save(array('time'=>time(),'show'=>1));
		M('product_rule')->where(array('id'=>array('neq',$_POST['id']),'token'=>session('token')))->save(array('show'=>0));
	}
	public function miaogouRuleAdd(){
		if(IS_POST){
			$_POST['token']=session('token');
			M('product_rule')->create();
			M('product_rule')->add();
			$this->success('添加成功~',U('Product/miaogouRule',array('token'=>session('token'))));
		}else{
			$this->display();
		}
	}
	public function miaogouRuleSet(){
		if(IS_POST){
			M('product_rule')->create();
			M('product_rule')->save();
			$this->success('修改成功~',U('Product/miaogouRule',array('token'=>session('token'))));
		}else{
			$set=M('product_rule')->where(array('id'=>$_GET['id']))->find();
			$this->set=$set;
			$this->isUpdate=1;
			$this->display('miaogouRuleAdd');
		}
	}
	public function miaogouRuleDel(){
		M('product_rule')->where(array('id'=>$_GET['id'],'token'=>$_GET['token']))->delete();
		$this->success('删除成功~',U('Product/miaogouRule',array('token'=>$_GET['token'])));
	}
	public function sort(){
			$sort=htmlspecialchars_decode($_POST['sort']);
			$sort=json_decode($sort,true);
			for($i=0;$i<count($sort);$i++){
				$data=array('sort'=>$sort[$i]['sort']);
 				M('product')->where(array('id'=>$sort[$i]['id']))->save($data);
			}
	}
		public function csort(){
				$sort=htmlspecialchars_decode($_POST['sort']);
				$sort=json_decode($sort,true);
				for($i=0;$i<count($sort);$i++){
					$data=array('sort'=>$sort[$i]['sort']);
	 				echo M('product_cat')->where(array('id'=>$sort[$i]['id']))->save($data);
				}
		}
}
?>