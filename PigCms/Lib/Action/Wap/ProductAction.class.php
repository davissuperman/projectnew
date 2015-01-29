<?php
class ProductAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $product_model;
	public $product_cat_model;
	public $isDining;
	public $Tt;
	public function __construct(){	
		//$agent = $_SERVER['HTTP_USER_AGENT']; 
		//if(!strpos($agent,"MicroMessenger")) {
		//	echo '此功能只能在微信浏览器中使用';exit;
		//}	
		$this->token		= $this->_get('token');
		$this->assign('token',$this->token);
		$this->wecha_id	= $this->_get('wecha_id');
		if ( empty($this->wecha_id) && !isset( $this->wecha_id ) ){
				if( !isset( $_COOKIE[ 'o2o_re_url' ] ) || empty( $_COOKIE[ 'o2o_re_url' ] ) ){
									$yy = dechex(rand(100000000000,999999999999));
									$this->wecha_id = $yy;
									setcookie("o2o_re_url",$yy, time()+3600*24*30);
				}else{
						$this->wecha_id = $_COOKIE[ 'o2o_re_url' ];
				}	
		} 
		$logo = M( "product_pic" )->where( array( 'token'=>$this->token ) )->find();
		$this->Assign( 'pic',$logo[ 'Shopics' ] );
		$this->Assign( 'token',$this->token );
		$this->assign('wecha_id',$this->wecha_id);
		$this->product_model=M('Product');
		$this->product_cat_model=M('Product_cat');
		foreach( $_SESSION[ $this->token ] as $key=>$val ){
							foreach( $val as $k=>$l ){
									if( $l[ 'catid' ] == 0 ){
										$c = $l[ 'time' ];	
										$Jt = 1;
									}else if( !$Jt ){
											echo $Jt;
											$c = -1;
									}					
							}						
		}
		$this->Assign( 'T',time() );	
		$this->Assign( 'C',intval( $c ) );
		$this->Assign( "Tt",$_SESSION[ 'i' ] );
		//define('RES',THEME_PATH.'common');
		//define('STATICS',TMPL_PATH.'static');
		$this->assign('staticFilePath',str_replace('./','/',THEME_PATH.'common/css/product'));
		//购物车
		
	}
	public function Index(){
			$m = M( "product_pic" )->where( array('token'=>$this->token) )->find();
			$this->Assign( "Shopic",$m[ 'Shopic' ] );	
			$this->Assign( "Shopics",$m[ 'Shopics' ] );
			//$d = M( "product_cat" )->where( array('token'=>$this->token,'sid'=>0) )->select();
/* 			$this->Assign( "l",$l );
			$this->Assign( "i",$i );
			$this->Assign( "t",$t ); */
			$this->Assign( 'token',$this->_get('token') );
			$this->Assign( 'wecha_id',$this->_get('wecha_id') );
			$this->Display();
	}
	public function all(){
			$token = $this->_get( 'token',trim );
			$all_p = M( "product_cat" );
			$sql = "SELECT * FROM tp_product_cat WHERE token='{$token}' and sid != 0 order by sort ASC";
			$li = $all_p->query( $sql );
			$this->Assign( 'all',$li );
			$this->Assign( "token",$token );
			$this->Assign( "wecha_id",$this->_get( "wecha_id",trim ) );	
			$this->Display();
	}
	public function News(){
			$token = $this->_get( "token",trim );
			$all_p = M( "product_cat" );
			$sql = "SELECT * FROM tp_product_cat WHERE token='{$token}' and sid != 0 order by sort ASC";
			$li = $all_p->query( $sql );
			$N_L = array();
			foreach( $li as $key=>$val ){
						$split = explode( ",",$val[ 'host' ] );
						if( $split[ 0 ] == 'host' || $split[ 1 ] == 'host' ){
								$N_L[] = $li[ $key ];
						}
			}
			$this->Assign( "n",$N_L );
			$this->Display();
	}
		public function h(){
			$shop = M( 'product_shop' );
			$token = $this->_get( "token",trim );
			$r1 = M( 'product_rule' )->where( array( 'token'=>$token,'show'=>1 ) )->find();
			$id = $this->_get( 'id',intval );
			if( !empty( $id ) )unset( $_SESSION[ 'T' ][ $id ] );
			$all_p = M( "product" );
			$LI = date( "Y-m-d" );
			$Li = strtotime( $LI );
			$lI = $Li + ( 3*24*60*60 );
			$sql = "SELECT * FROM tp_product WHERE token='{$token}' and catid=0  AND time_start<'{$lI}'  AND time_end >".time()." order by time_start asc ";
			$li = $all_p->query( $sql );
			$sq = $shop->Where( array( 'token'=>$token,'wecha_id'=>$this->wecha_id ) )->select();
			foreach( $sq as $key=>$val ){
						$t = unserialize( $val[ 'il' ] );
						if( $t[ 'catid' ] == 0 ){
								$idArr[] = $t[ 'ic' ]; 
						}
			}
			$this->Assign( 'idA',json_encode( $idArr ) );
			$this->Assign( 'images_l',$r1[ 'image' ] );
			$this->Assign( "list",$li );			
			$this->Display();
	}
	public function trend(){
			$token = $this->_get( "token",trim );
			$N = M( "product_cat" )->where( array( "token"=>$token,'host'=>'trend' ) )->find();
			$N_L = M( "product_cat" )->where( array( "token"=>$token,'sid'=>$N[ 'id' ] ) )->select();
			$this->Assign( "n",$N_L );
			$this->Display();
	}
	public function truedlist(){
			$token = $this->_get( 'token',trim );
			$id = $this->_get( 'id',intval );
			$T = M( "product" )->where( array( "token"=>$token,'catid'=>$id ) )->select();
			$l = M( "product_cat" )->where( array( "token"=>$token,'id'=>$id ) )->find();
			$this->Assign( "lo",$l[ 'logourl' ] );
			$goods_json = json_encode( $T );
			$this->Assign( "goods",$goods_json );
			$this->Display();
	}
	public function lists(){
			//$pic = M( "product_cat" )->where( array( "token"=>$this->token,'host'=>'news' ) )->find();
		//	$this->Assign( "log",$pic[ 'logourl' ] );
			$token = $this->_get( "token",trim );
			$g = M( "product" );
			$sql = "SELECT * FROM tp_product WHERE token='{$token}' AND putaway =1  order by time desc limit 0,10 ";
			$goods = $g->query( $sql );
			$goods_json = json_encode( $goods );
			$this->Assign( "goods",$goods_json );				
			$this->Display();
	}
	public function plists(){
			$token = $this->_get( 'token',trim );
			$id = $this->_get( "id",intval );
			$sql = "SELECT * FROM tp_product WHERE token='{$token}' AND putaway =1  order by sort asc";
			//$i = M( "product" )->where( array( "token"=>$token,'catid'=>$id,'putaway'=>1 ) )->select();
			$i = M( "product" );
			$ic = $i->query( $sql );
			$goods_json = json_encode( $ic );
			$this->Assign( "goods",$goods_json );
			$this->Display();
	}
	public function shopping(){
			$yy = array();	
			$id = $this->_get( "id",intval );
			$token = $this->_get( "token",trim );
			$single = M( "product" )->where( array( "id"=>$id,"token"=>$token ) )->find();
			$this->Tt = $single[ 'catid' ];
			$_SESSION[ 'i' ] = $single[ 'catid' ];
			$express = M( "product_express" )->Where( array( 'token'=>$token ) )->select();
			$singles = explode("^", $single[ 'etalon' ]);
			foreach( $singles as $key=>$val ){
					$snum[] = explode( ",",$val );	
			}
			$out = count( $snum );
			unset( $snum[ $out-1 ] );
			$logo = explode( "^",$single[ 'logourl' ] );
			$count = count( $logo );
			$c_s = count( $singles );
			unset( $logo[$count-1] );
			unset( $singles[ $count-1 ] );
			if( ($c_s-1)==0 ){
					$P[ 'pList' ][] = $singles[ $i ];
					$b = $singles[ $i ];
					$c = $single[ 'color' ];
					if( empty( $c ) ){
							$c = "无";
					}
					$availSku[ "颜色:$c" ][ 'pid' ] = $single[ 'id' ];
					$availSku[ "颜色:$c" ][ 'stockCount' ] = $single[ 'repertory' ];
					$availSku[ "颜色:$c" ][ 'stockPrice' ] = $single[ 'price' ].'00';
					$availSku[ "颜色:$c" ][ 'maketPrice' ] = $single[ 'oprice' ].'00';
			}else{
				for( $i=0;$i<$c_s-1;$i++ ){
					$P[ 'pList' ][] = $snum[ $i ][ 0 ];
					$b = $snum[ $i ][ 0 ];
					$c = $single[ 'color' ];
					if( empty( $c ) ){
							$c = "无";
					}	
					if( $snum[ $i ][ 1 ]<0 ){
							$snum[ $i ][ 1 ] = 0;	
					}
					$availSku[ "颜色:$c|规格:$b" ][ 'pid' ] = $single[ 'id' ];
					$availSku[ "颜色:$c|规格:$b" ][ 'stockCount' ] = $snum[ $i ][ 1 ];
					$availSku[ "颜色:$c|规格:$b" ][ 'stockPrice' ] = $single[ 'price' ].'00';
					$availSku[ "颜色:$c|规格:$b" ][ 'maketPrice' ] = $single[ 'oprice' ].'00';	
				}
			}
			$P[ 'pName' ] = '规格';
			if( empty( $single[ 'color' ] ) )$single[ 'color' ] = "无";
			$P1[ 'pList' ][] = $single[ 'color' ];
			$P1[ 'pName' ] = "颜色";
			foreach( $express as $key=>$val ){
						$P2[ 'pList' ][] = $val[ 'name' ] . ":¥" . $val[ 'price' ];
			}
			$yy[] = $P1;
			if(($c_s-1)!=0)$yy[] = $P;
			$json = json_encode($logo);
			$json = str_replace("\"","",$json);
			/***************************************/
			$bc_1 = 0;
				foreach( $_SESSION[ $token ][ $id ] as $ke=>$v){
							if( $single[ 'catid' ] == 0 )$bc_l += $v[ 'bc' ];						
				}
				if( $bc_l >= $single[ 'limit' ] ){
						$bc_l = 1;
				}
			$this->Assign( 'bc_l',$bc_l );
			$this->Assign( 'exp',json_encode( $P2 ) );
			$this->Assign( "p",json_encode( $yy ) );
			$this->Assign( "availSku",json_encode( $availSku ) );	
			$this->Assign( "name",$single[ 'name' ] );
			$this->Assign( 'oprice',sprintf("%01.2f",$single[ 'price' ]) );
			$this->Assign( "price",sprintf("%01.2f",$single[ 'oprice' ]) );
			$this->Assign( 'intro',$single[ 'intro' ] );
			$this->Assign( 'l',$logo );
			$this->Assign( "logo",$json );
			$this->Assign( "id",$single[ 'id' ] );
			$this->Assign( 'end',date( "Y/m/d H:i:s",$single[ 'time_end' ] ) );
			$this->Assign( "token",$token );
			$this->Assign( 'count',$single[ 'repertory' ] );
			$this->Assign( "i_logo",$single[ 'index_logo' ] );
			$this->Assign( "freight",$single['freight'] );
			$this->Assign( "catid",$single['catid'] );
			$this->Assign( "Seconds",$single[ 'limit' ] );
			$this->Assign( "Tt",$this->Tt );
			$this->Display();
	}
	public function cat_s(){
				$sing = array();
				$attr = trim( $_POST[ 'attr' ] );
				$count = intval( $_POST[ 'bc' ] );
				$goods = intval( $_POST[ 'ic' ] );
				$price = intval( $_POST[ 'oprice' ] );
				$catid = intval( $_POST[ 'catid' ] );
				$freight = intval( $_POST[ 'freight' ] );
				$name = trim( $_POST[ 'name' ] );
				$img = trim( $_POST[ 'i_logo' ] );
				$token = trim( $_POST[ 'token' ] );
				$wecha_id = trim( $_POST[ 'wecha_id' ] );
				$re = $this->addItem( $goods,$name,$price,$count,$img,$token,$attr,$freight,$catid );
				$goodsnum =$this->getNum($token);//获取购物车商品的总数
				$goodsprice = $this->getPrice($token);//获取购物车商品的总价格
				$sing[ 'data' ][ 'disTotalFee' ] = $price;
				$sing[ 'data' ][ 'totalFee' ] = $goodsprice;
				$sing[ 'errCode' ] = $re;
				$sing[ 'token' ] = $token;
				$sing[ 'wecha_id' ] = $wecha_id;
				echo json_encode( $sing );		
	}
	/*
   添加商品
    param int $id 商品主键
          string $name 商品名称
          float $price 商品价格
          int $num 购物数量
    */
    public  function addItem($id,$name,$price,$num,$img,$token,$attr,$freight,$catid) {
	        //如果该商品已存在则直接加其数量
        if (isset($_SESSION[$token][$id][ $attr ])) {
           $this->incNum($id,$num,$token,$attr);
	            return;
        }		$T_t = explode( "|",$attr );
				$t_L = explode( ":",$T_t[1] );
				$t_L = $t_L[ 1 ];	
				$item = array();
				$item['ic'] = $id;
				$item['name'] = $name;
				$item['price'] = $price;
				$item['bc'] = $num;
				$item['pic'] = $img;
				$item[ 'attr' ] = $attr;
				$item[ 'catid' ] = $catid;
				$item[ 'freight' ] = $freight;
				$item[ 'etalon' ] = $t_L.",".$item['bc'];
				$item[ 'time' ] = time();
				$_SESSION[$token][$id][ $attr ] = $item;
				if( !empty( $_SESSION[$token][$id][ $attr ] ) )return 0;
    }
	 /*
	    查询购物车中商品的个数
	    */
	    public function getNum($token){
	        if ($this->getCnt($token) == 0) {
	            //种数为0，个数也为0
	            return 0;
	        } 
	        $sum = 0;
	        $data = $_SESSION[$token];
	        foreach ($data as $item) {
					foreach( $item as $k=>$v ){
								 $sum += $v['bc'];
					}
	        }
	        return $sum;
	    }
		public function getPrice($token) {
	        //数量为0，价钱为0
	        if ($this->getCnt($token) == 0) {
	            return 0;
	        }
	        $price = 0.00;
	        $data = $_SESSION[$token];
	        foreach ($data as $item) {
							foreach( $item as $k=>$l ){
										$price += $l['bc'] * $l['price'];
							}
	        }
	        return sprintf("%01.2f", $price);
	    }
	/* public function incNum($id,$num=0,$token,$attr) {
        if (isset($_SESSION[$token][$id][ $attr ])) {
			$T_t = explode( "|",$attr );
			$t_L = explode( ":",$T_t[1] );
			$t_L = $t_L[ 1 ];
			$_SESSION[$token][$id][ $attr ]['bc'] += $num;
			$_SESSION[$token][$id][ $attr ]['etalon'] = $t_L.",".$_SESSION[$token][$id][ $attr ]['bc'];
        }
    } */
	public function incNum($id,$num=0,$token,$attr) {
        if (isset($_SESSION[$token][$id][ $attr ])) {
			$enalon = $_SESSION[$token][$id][ $attr ][ 'etalon' ];
			$product_Ta = M( "product" );
			$T_t = explode( "|",$attr );
			$t_L = explode( ":",$T_t[1] );
			$t_L = $t_L[ 1 ];
			$tg = $product_Ta->where( array( 'token'=>$token,'id'=>$id ) )->find();	
							$G = explode( "^",$tg[ 'etalon' ] );
							for( $j=0;$j<( count( $G )-1);$j++ ){
								$eat = explode( ",",$G[ $j ] );
								$one_e = explode( ',',$enalon );
									if( $eat[ 0 ]==$one_e[ 0 ] ){//0为规格
									if( $eat[1]<=( $num+$one_e[ 1 ] ) ){
										$_SESSION[$token][$id][ $attr ]['bc'] = $eat[1];		
										$_SESSION[$token][$id][ $attr ]['etalon'] = $t_L.",".$_SESSION[$token][$id][ $attr ]['bc'];
											}else{
													$_SESSION[$token][$id][ $attr ]['bc'] += $num;
													$_SESSION[$token][$id][ $attr ]['etalon'] = $t_L.",".$_SESSION[$token][$id][ $attr ]['bc'];
											}		
							} 																
					}  
        }
    }
	
	
		  /*
	    查询购物车中商品的种类
	    */
	public function getCnt($token) {
	        return count($_SESSION[$token]);
	   }
	 public function cats(){
		/* $token = $this->_get( "token",trim );
		$wecha_id = $this->_get( 'wecha_id',trim );
		$goodsprice = $this->getPrice($token);
		$this->Assign( "list",$_SESSION[$token] );
		$this->Assign( 'wecha_id',$wecha_id );
		$this->Assign( "cat",json_encode( $_SESSION[$token] ) );
		$this->Assign( "token",$token );
		$this->Assign( 'cats',count(  $_SESSION[$token] ) );
		$this->Assign( 'Tt',$_SESSION[ 'i' ] );
		$this->Assign( 'price',$goodsprice );
		$this->Display();	 */ 
		$token = $this->_get( "token",trim );
		$wecha_id = $this->_get( 'wecha_id',trim );
		$goodsprice = $this->getPrice($token);
		$c = 0;
		$s2 = array();
		$s1 = array();
		foreach( $_SESSION[$token] as $k=>$v ){
			$c += count( $_SESSION[$token][ $k ] );
			foreach( $v as $key=>$val ){
						if( $val[ 'catid' ] == 0 ){
								$s1[ $k ][ $key ] = $val;
						}else{
							$s2[ $k ][ $key ] = $val;	
						}			
				}
		}
		$se3 = array_merge( $s1,$s2 );
		$this->Assign( "list",$se3 );
		$this->Assign( 'wecha_id',$wecha_id );
		$this->Assign( "cat",json_encode( $se3 ) );
		$this->Assign( "token",$token );
		$this->Assign( 'cats',$c );
		$this->Assign( "num",-1 );
		$this->Assign( 'price',$goodsprice );
		$this->Display();	 
	 }
	/*
	    删除商品 
	    */
	    public function delItem() {
			/* $id = $_POST[ 'id' ];//接受的ID
			$token = $_POST[ 'mid' ];//接受的TOKEN
			if( isset( $_SESSION[$token] ) && !empty( $_SESSION[$token] ) ){
					unset( $_SESSION[ $token ][ $id ] );
					$err[ 'errCode' ] = 0;
					$err[ 'retCode' ] = 0;
					$err[ 'msgType' ] = 0;
					$err[ 'errMsg' ] = "";
					$err[ 'data' ] = "Remove Item in Cmdy success.";
					echo json_encode( $err );
			}else{
						$err[ 'errCode' ] = 1;
						echo json_encode( $err );
			}	 */
			$id = $_POST[ 'id' ];//接受的ID
			$token = $_POST[ 'mid' ];//接受的TOKEN
			$attr = $_POST[ 'sa' ];
			if( isset( $_SESSION[$token] ) && !empty( $_SESSION[$token] ) ){
					unset( $_SESSION[ $token ][ $id ][ $attr ] );
					if( empty( $_SESSION[ $token ][ $id ] ) ){
								unset( $_SESSION[ $token ][ $id ] );
					}
					$err[ 'errCode' ] = 0;
					$err[ 'retCode' ] = 0;
					$err[ 'msgType' ] = 0;
					$err[ 'errMsg' ] = "";
					$err[ 'data' ] = "Remove Item in Cmdy success.";
					echo json_encode( $err );
			}else{
						$err[ 'errCode' ] = 1;
						echo json_encode( $err );
			}	
	  }
	public function test(){
		 	$a[ 'errCode' ]= 0;
			$a[ 'retCode' ]= 0;
			$a[ 'msgType' ]= 0;
			$a[ 'errMsg' ]= "";
			$a[ 'data' ]= count( $_SESSION[ $this->_get( 't',trim ) ] );
			echo json_encode( $a );
	}
	public function user(){
			$token = $_POST[ 'token' ];
			$user = $_POST[ 'user' ];
			$u = M( "product_order" )->where( array( "token"=>$token,'wecha_id'=>$user ) )->select();
		 	$c = count( $u );
				if( $c == 0 ){
					echo 0;
				}else{
					echo 1;
				} 
	}
	public function buy_now(){
			$token = $this->_get( 'token',trim );
			$wecha_id = $this->_get( 'wecha_id',trim );
			$attr = trim( $_POST[ 'attr' ] );
			$count = intval( $_POST[ 'bc' ] );
			$goods = intval( $_POST[ 'ic' ] );
			$price = intval( $_POST[ 'oprice' ] );
			$freight = $_POST[ 'freight' ];
			$catid = $_POST[ 'catid' ];
			$bc_l = $_POST[ 'bc_l' ];
			$name = trim( $_POST[ 'name' ] );
			$img = trim( $_POST[ 'i_logo' ] );
			if( $bc_l != 1 )$this->addItem( $goods,$name,$price,$count,$img,$token,$attr,$freight,$catid );
			$u = M( "product_order" )->where( array( "token"=>$token,'wecha_id'=>$wecha_id ) )->select();
			$c = count( $u );
				if( $c == 0 ){
					header( "location:index.php?g=Wap&m=Product&a=useradress&token=$token&wecha_id=$wecha_id" );			
				}else{
					header( "location:index.php?g=Wap&m=Product&a=ordertrue&token=$token&wecha_id=$wecha_id" );
				} 
	}
	public function useradress(){
			$m = M( 'product_order' );
			$this->Display();
	}
	public function museradress(){
					$m = M( 'product_order' );
					$token = $this->_get( 'token',trim );				
					$ls = $m->Where( array( 'token'=>$token,'wecha_id'=>$this->wecha_id ) )->count();
					$wecha_id = $this->wecha_id;	
					$ls = intval( $ls );
					if( $ls != 0 ){
						header("location:index.php?g=Wap&m=Product&a=h&token=$token&wecha_id=$wecha_id");	
					}else{
						$this->Display();
					}		
	}
	public function ordertrue(){
			$token = $this->_get( "token",trim );
			$wechat = $this->_get( "wecha_id",trim );
			$adid = $this->_get( 'adid',intval );
			if( !empty( $adid ) ){
				$order = M( "product_order" )->where( array( "token"=>$token,"wecha_id"=>$wechat,'id'=>$adid ) )->find();
			}else{
				$order = M( "product_order" )->where( array( "token"=>$token,"wecha_id"=>$wechat ) )->find();
			}
			if( $order[ 'pvid' ] == $order[ 'ctid' ] ){
				$province = M( "city" )->field( 'name' )->where( array( "id"=>$order[ 'pvid' ] ) )->find();
				$city = M( "district" )->field( "name" )->where( array( "id"=>$order[ 'regionId' ] ) )->find();
			}else{
				$province = M( "province" )->field( 'name' )->where( array( "id"=>$order[ 'pvid' ] ) )->find();
				$district = M( "city" )->field( 'name' )->where( array( "id"=>$order[ 'ctid' ] ) )->find();
				$city = M( "district" )->field( "name" )->where( array( "id"=>$order[ 'regionId' ] ) )->find();
			}
			$goodprice = explode( '.',$this->getPrice($token) );
			$goodprice[0] = $goodprice[0].'00';
			$B = 0;
			foreach( $_SESSION[$token] as $key=>$val ){
					$shopid .=  $key ."-";	
					foreach( $val as $k=>$v ){
								if( $v[ 'freight' ]==1 ){
										$B = 1;
									 
								} 
					}
			}
			$express = M( 'product_express' )->Where( array( 'token'=>$token ) )->select();
			if( empty( $district ) )$district=NULL;
			$address = $province['name'].$district['name'].$city['name'].$order[ 'address' ];
			$this->Assign( "name",$order[ 'name' ] );
			$this->Assign( "address",$address );
			$this->Assign( "price",$goodprice[0] );
			$this->Assign( "mobile",$order[ 'mobile' ] );
			$this->Assign( "shopping",$_SESSION[$token] );
			$this->Assign( 'wecha_id',$wechat );
			$this->Assign( "shopid",$shopid );
			$this->Assign( 'adid',$order[ 'id' ] );
			$this->Assign( 'ex',$express );
			$this->Assign( 'b',$B );
			$this->Display();
	}
	public function la(){
			echo "{\"errCode\":0,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\",\"data\":{\"payType\":0,\"dealCode\":66147,\"minipayPo\":null,\"payChannel\":0,\"tenpayUrl\":\"/cmd/pay_tips.html?msg_ty=2&order_id=66147\"}}";
			//$this->redirect(U('Alipay/pay',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'success'=>1,'price'=>$totalFee,'orderName'=>$orderName,'orderid'=>$orderid)));
			//header("location:index.php?g=Wap&m=Product&a=ordertrue&token=xosooe1384219311&wecha_id=$wechat"); 	
		
	}
	public function province(){//省
			$c = $this->_get( "pvid",intval );
			$city = M( "city" )->where( array( "ProvinceID"=>$c ) )->field( "id,name" )->select();
			$json = json_encode( $city );
			echo "{\"errCode\":0,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\",\"data\":{$json}}";exit;
	}
	public function city(){//市区县
			$districtID = $this->_get( "ctid",intval );
			$districtList = M( "district" )->where( array( "CityID"=>$districtID ) )->field( "id,name" )->select();
			$json = json_encode( $districtList );
			echo "{\"errCode\":0,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\",\"data\":{$json}}";exit;	
	}
	public function SaveAdress(){
					$row = array();
					$order = M( 'product_order' );	
					$wechat = $this->_get( "wecha_id",trim );
					$token = $this->_get( 'token',trim );
					$more = $this->_get( 'more',intval );
					$Seconds = $this->_get( 'Seconds',intval );
					$id = $_POST[ 'id' ];
					$row[ 'name' ] = $_POST[ "name" ];
					$row[ 'pvid' ] = $_POST[ 'pvid' ];
					$row[ 'ctid' ] = $_POST[ 'ctid' ];
					$row[ 'regionId' ] = $_POST[ 'regionId' ];
					$row[ 'mobile' ] = $_POST[ 'mobile' ];
					$row[ 'address' ] = $_POST[ 'address' ];
					$row[ 'token' ] = $token;
					$row[ 'wecha_id' ] = $wechat;
					if( !empty( $id ) ){
						$s = $order->where( array( 'token'=>$token,'wecha_id'=>$wechat,'id'=>$id ) )->save( $row );
					}else{
						$s = $order->add( $row );
					}
					if( $s ){
						if( $Seconds ){
								header("location:index.php?g=Wap&m=Product&a=h&token=$token&wecha_id=$wechat");
						}else{
								if( $more ){
								header("location:index.php?g=Wap&m=Product&a=newadress&token=$token&wecha_id=$wechat"); 
								}else{
								header("location:index.php?g=Wap&m=Product&a=ordertrue&token=$token&wecha_id=$wechat"); 
							}
						}		
					}else{
							die( "系统错误" );
					}					
	}
	public function usermember(){
			$token = $this->_get( "token" );
			$wecha_id = $this->_get( "wecha_id" );
			$s = M( "product_shop" )->where( array( "token"=>$token,"wecha_id"=>$wecha_id ) )->select();
			$count = count( $s );
			foreach( $s as $key=>$val ){
					$jsonArr[ 'bid' ] = $val[ 'bid' ];
					$jsonArr[ 'dt' ] = $val[ 'dt' ]."00";
					$jsonArr[ 'ds' ] = $val[ 'ds' ];
					$jsonArr[ 'il' ] = unserialize( $val[ 'il' ] );
					$jsonArr[ 'pt' ] = $val[ 'pt' ];
					$jsonArr[ 'sc' ] = $val[ 'sc' ];
					if( $key == ( $count-1 ) ){
							if( $jsonArr[ 'sc' ]==4 ){
										$jsonclose .= json_encode( $jsonArr );
							}else{
									$json .= json_encode( $jsonArr );	
							}						
					}else{
						if( $jsonArr[ 'sc' ]==4 ){
										$jsonclose .= json_encode( $jsonArr ).",";
							}else{
									$json .= json_encode( $jsonArr ).",";	
							}		
					}		
			}
			$json = str_replace("il\":","il\":[",$json);
			$json = str_replace(",\"pt","],\"pt",$json);
			$jsonclose = str_replace("il\":","il\":[",$jsonclose);
			$jsonclose = str_replace(",\"pt","],\"pt",$jsonclose);
			$this->Assign( 'json',$json );
			$this->Assign( 'jsonclose',$jsonclose );
			$this->Assign( "token",$token );
			$this->Assign( "wecha_id",$wecha_id );
			$this->Display();
	}
	public function OrderFrom(){
			$s = M( "product_shop" );
			$product_Ta = M( "product" );
			$token = $_POST[ 'adid' ];
			$wecha_id = $_POST[ 'cart' ];
			$exp = $_POST[ 'mtype' ];
			$indent = $_POST[ 'orderStrList' ];
			$shopid = $_POST[ 'shopid' ];
			$shopid = explode( "-",$shopid );
			unset( $shopid[ count( $shopid )-1 ] );
			$errcode = 0;	
			$rand = mt_rand( 100,6000 );
			$SESSION = $_SESSION[ $token ];
			for( $i=0;$i<count( $shopid );$i++ ){
				foreach( $SESSION[ $shopid[ $i ] ] as $key=>$val ){
				 	$tg = $product_Ta->where( array( 'token'=>$token,'id'=>$shopid[ $i ] ) )->find();
							$G = explode( "^",$tg[ 'etalon' ] );
							for( $j=0;$j<( count( $G )-1);$j++ ){
								$eat = explode( ",",$G[ $j ] );
								$one_e = explode( ',',$val[ 'etalon' ] );
									if( $eat[ 0 ]==$one_e[ 0 ] ){//0为规格
												 if( $eat[ 1 ]<=0 ){//1为数量
															unset( $_SESSION[ $token ][ $shopid[ $i ] ][ $key ] );//先清空购物车不存在的记录
															if( empty( $_SESSION[ $token ][ $shopid[ $i ] ] ) )unset( $_SESSION[ $token ][ $shopid[ $i ] ] );
												 }else{
														$lon = $eat[ 0 ].",".( $eat[1]-$one_e[1] );
														$GH = explode( "^",$tg[ 'etalon' ] );
														$GH[ $j ] = $lon;
														$nEw = implode( "^",$GH );
													$sql_h = "UPDATE tp_product SET repertory = repertory-{$one_e[1]},etalon='{$nEw}'  WHERE id={$shopid[ $i ]} AND token='{$token}'";
													$product_Ta->query( $sql_h );
												}
										} 																
								} 
						}				
				}
			unset( $lon );
			unset( $GH );
			unset( $SESSION );
			if( empty( $_SESSION[ $token ] ) )$errcode = 1;
			if( $errcode ){
				echo "{\"errCode\":1,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\"}";exit;
			}
			 for( $i=0;$i<count( $shopid );$i++ ){
					foreach( $_SESSION[ $token ][ $shopid[ $i ] ] as $y=>$F_L ){	
					 	$shop[ 'bid' ] = $rand;
						$shop[ 'dt' ] = $F_L[ 'price' ]*$F_L[ 'bc' ];
						$shop[ 'ds' ] = 63;
						$shop[ 'il' ] = serialize( $F_L );
						$shop[ 'token' ] = $token;
						$shop[ 'wecha_id' ] = $wecha_id;
						$shop[ 'number' ] = $indent.time();
						$shop[ 'user' ] = $_POST[ 'name' ];
						$shop[ 'moble' ] = $_POST[ 'moble' ];
						$shop[ 'address' ] = $_POST[ 'address' ];
						$shop[ 'oprice' ] = ( intval( $_POST[ 'oprice' ] )/100 ) ;
						$shop[ 'pc' ] = $_POST[ 'pc' ];
						$shop[ 'ex' ] = $exp;
						$shop[ 'time' ] = time();
						$continue = $s->add( $shop );
						if( !$continue )die( "error!" );
						$rand++;
					}		
			}
			unset( $_SESSION[ $token ] );
			 if( $_POST[ 'pc' ] == 1016 ){
				$URL = "/index.php?g=Wap&m=Product&a=pay&token={$token}&wecha_id={$wecha_id}";
				echo "{\"errCode\":0,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\",\"data\":{\"payType\":0,\"dealCode\":66147,\"minipayPo\":null,\"payChannel\":0,\"tenpayUrl\":\"".$URL."\"}}";	
			}else{
					if ($_POST[ 'pc' ] == 1014){
				$URL = "/index.php?g=Wap&m=Tenpay&a=pay&token={$token}&wecha_id={$wecha_id}&success=1&orderName={$token}&orderid={$indent}&price=1";
				echo "{\"errCode\":0,\"retCode\":0,\"msgType\":0,\"errMsg\":\"\",\"data\":{\"payType\":0,\"dealCode\":66147,\"minipayPo\":null,\"payChannel\":0,\"tenpayUrl\":\"".$URL."\"}}";
					}else{
							
					}	
			}
			//if ($alipayConfig['open']){
			
				//$this->redirect(U('Alipay/pay',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'success'=>1,'price'=>$totalFee,'orderName'=>$orderName,'orderid'=>$indent)));
		//	}else {
			//	$this->redirect(U('Product/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'success'=>1)));
			//}
			
			//echo "{\"errCode\":0,\"retCode\":0,\"msgType\":".$a.",\"errMsg\":\"\",\"data\":{\"payType\":0,\"dealCode\":66147,\"minipayPo\":null,\"payChannel\":0,\"tenpayUrl\":\"".$URL."\"}}";	
			//$this->Display();
	}
	public function lineitem(){
					$token = $this->_get( "token",trim );
					$wecha = $this->_get( "wecha_id",trim );
					$orderid = $this->_get( "orderid" );
					$id = M( 'product_shop' )->where( array( 'token'=>$token,'wecha_id'=>$wecha,'bid'=>$orderid ) )->find();			
					$order = M( "product_order" )->where( array( "token"=>$token,"wecha_id"=>$wecha ) )->find();
					if( $order[ 'pvid' ] == $order[ 'ctid' ] ){
					$province = M( "city" )->field( 'name' )->where( array( "id"=>$order[ 'pvid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$order[ 'regionId' ] ) )->find();
					}else{
					$province = M( "province" )->field( 'name' )->where( array( "id"=>$order[ 'pvid' ] ) )->find();
					$district = M( "city" )->field( 'name' )->where( array( "id"=>$order[ 'ctid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$order[ 'regionId' ] ) )->find();
					}
					$address = $province['name'].$district['name'].$city['name'].$order[ 'address' ];
					$this->Assign( "mobile",$order[ 'mobile' ] );
					$this->Assign( "names",$order[ 'name' ] );
					$this->Assign( 'address',$address );
					$goods = unserialize( $id[ 'il' ] );
					$this->Assign( 'name',$goods[ 'name' ] );
					$this->Assign( 'attr',$goods[ 'attr' ] );
					$this->Assign( 'bc',$goods[ 'bc' ] );
					$this->Assign( 'logo',$goods[ 'pic' ] );
					$this->Assign( 'dt',$id[ 'dt' ] );
					$this->Assign( 'number',$id[ 'number' ] );
					$this->Assign( 'time',$id[ 'time' ] );
					$this->Assign( 'etalon',$goods[ 'etalon' ] );
					$this->Assign( 'i',$id[ 'id' ] );
					$this->Assign( 'sc',$id[ 'sc' ] );
					$this->Assign( 'ic',$goods[ 'ic' ] );
					$this->Assign( 'token',$token );
					$this->Assign( 'wecha_id',$wecha );	
					$this->Display();
	}
	public function orderCancel(){
				$id = $_POST[ 'id' ];
				$data[ 'sc' ] = 4;
				$data[ 'pc' ] = 1013;
				$attr = $_POST[ 'attr' ];
				$token = $_POST[ 'token' ];
				$wecha_id = $_POST[ 'wecha_id' ];
				$ic = $_POST[ 'ic' ];
				$product_Ta = M( "product" );
				/********************************************/
				$tg = $product_Ta->where( array( 'token'=>$token,'id'=>$ic ) )->find();	
							$G = explode( "^",$tg[ 'etalon' ] );
							for( $j=0;$j<( count( $G )-1);$j++ ){
								$eat = explode( ",",$G[ $j ] );
								$one_e = explode( ',',$attr );
									if( $eat[ 0 ]==$one_e[ 0 ] ){//0为规格
										$lon = $eat[ 0 ].",".( $eat[1]+$one_e[1] );
										$GH = explode( "^",$tg[ 'etalon' ] );
										$GH[ $j ] = $lon;
										$nEw = implode( "^",$GH );
										unset( $lon );
										unset( $GH );
										$sql_h = "UPDATE tp_product SET repertory = repertory+{$one_e[1]},etalon='{$nEw}'  WHERE id={$ic} AND token='{$token}'";
										$product_Ta->query( $sql_h );			
										} 																
								} 
			/*********************************************/			
				$t = M( 'product_shop' )->data( $data )->where( array( 'token'=>$token,'wecha_id'=>$wecha_id,'id'=>$id ) )->save();
				if( $t ){
					$json[ 'error' ] = 1;
					echo json_encode( $json );
				}else{
					$json[ 'error' ] = 0;
					echo json_encode( $json );	
				}
	}
/* 	public function pay(){
			$p = M( 'product_shop' )->Where( array( 'token'=>$this->_get( 'token',trim ),'wecha_id'=>$this->_get( 'wecha_id' ),'pc'=>1016 ) )->select();
			$pay = 0;
			foreach( $p as $key=>$val ){
					$order .= "[" . $val[ 'bid' ] . "]" . '  ';
					$pay += $val[ 'dt' ];
			}
			$this->Assign( 'o',$order );
			$this->Assign( 'p',$p[ 0 ][ 'oprice' ] );
			$this->Assign( 'pay',$pay );
			$this->Display();
	} */
	public function pay(){
			$p = M( 'product_shop' )->Where( array( 'token'=>$this->_get( 'token',trim ),'wecha_id'=>$this->_get( 'wecha_id' ),'pc'=>1016 ) )->select();
			$pay = 0;
			foreach( $p as $key=>$val ){
					$order .= "[" . $val[ 'bid' ] . "]" . '  ';
					$pay = $val[ 'oprice' ];
			}
			$this->Assign( 'o',$order );
			$this->Assign( 'p',$p[ 0 ][ 'oprice' ] );
			$this->Assign( 'pay',$pay );
			$this->Display();
	}
	public function brand(){
				$i = M( "product_brand" )->where( array( 'token'=>$this->token ) )->find();
				$this->Assign( 'url',$i[ 'url' ] );
				$this->Assign( 't',$i[ 'text' ] );	
				$this->Display();
	}
	public function cookie(){
			header( "Location:/index.php?g=Wap&m=Product&a=index&token={$this->token}&wecha_id={$this->wecha_id}" );
	}
	public function newadress(){
			$order = M( 'product_order' );
			$token = $this->_get( 'token',trim );
			$wecha_id = $this->_get( 'wecha_id',trim );
			$orderList = $order->where( array( 'token'=>$token,'wecha_id'=>$wecha_id ) )->limit( 10 )->select();
			foreach( $orderList as $key=>$val ){
			if( $val[ 'pvid' ] == $val[ 'ctid' ] ){
					$province = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}else{
					$province = M( "province" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$district = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'ctid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}
					$address = $province['name'].$district['name'].$city['name'].$val[ 'address' ];
				$orderList[ $key ][ 'display' ] = $address;
				}
			$this->Assign( 'o',$orderList );	
			$this->Display();
	}
	public function addNew(){
			$id = $this->_get( 'id',trim );
			$list = M("product_order")->where( array( 'id'=>$id ) )->find();
			$this->Assign( 'id',$id );
			$this->Assign( 'li',$list );
			$this->Display();												
	}
	public function DelNew(){
			$token = $this->_get( 'token',trim );
			$wecha_id = $this->_get( 'wecha_id',trim );
			$id = $this->_get( 'id',intval );
			$d = M( "product_order" )->Where( array( 'token'=>$token,'wecha_id'=>$wecha_id,'id'=>$id ) )->delete();
			if( $d ){
					header( "Location:/index.php?g=Wap&m=Product&a=newadress&token={$token}&wecha_id={$wecha_id}" );
			}
	}
	public function select(){
			$order = M( 'product_order' );
			$adid = $this->_get( 'adid',intval );
			$token = $this->_get( 'token',trim );
			$wecha_id = $this->_get( 'wecha_id',trim );
			$orderList = $order->where( array( 'token'=>$token,'wecha_id'=>$wecha_id ) )->limit( 10 )->select();
			foreach( $orderList as $key=>$val ){
			if( $val[ 'pvid' ] == $val[ 'ctid' ] ){
					$province = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}else{
					$province = M( "province" )->field( 'name' )->where( array( "id"=>$val[ 'pvid' ] ) )->find();
					$district = M( "city" )->field( 'name' )->where( array( "id"=>$val[ 'ctid' ] ) )->find();
					$city = M( "district" )->field( "name" )->where( array( "id"=>$val[ 'regionId' ] ) )->find();
					}
					$address = $province['name'].$district['name'].$city['name'].$val[ 'address' ];
				$orderList[ $key ][ 'display' ] = $address;
				}
			$this->Assign( 'o',$orderList );
			$this->Assign( 'adid',$adid );	
			$this->Display();
	}
	public function join(){	
			$token = $this->_get( 'token',trim );	
			foreach( $_SESSION[ $token ] as $key=>$val ){
							foreach( $val as $k=>$l ){
							if( $l[ 'catid' ] == 0 ){
								$time = 1200-( time()-$l[ 'time' ] );
								if( $time<=0 ){	
								$_SESSION[ 'T' ][ $k ] = $l;			
								unset( $_SESSION[ $token ][ $key ][ $k ] );
								}
						}
				}
				if( empty( $_SESSION[ $token ][ $key ] ) ){
										unset( $_SESSION[ $token ][ $key ] );
								}	
						
			}
			
			$this->Assign( 'LI',count( $_SESSION[ 'T' ] ) );
			$this->Assign( 'l',$_SESSION[ 'T' ] );
			$this->Display();
	}
	public function join_Del(){
					$I = $_POST[ 'id' ];
				 unset( $_SESSION[ 'T' ][ $I ] );
				if( !isset( $_SESSION[ 'T' ][ $I ] ) ){
						echo 1;
				}else{
						echo 0;
				}
	}
	public function rule(){
			$token = $this->_get( 'token',trim );
			$r = M( 'product_rule' )->where( array( 'token'=>$token,'show'=>1 ) )->find();
			$this->Assign( 'title',$r[ 'title' ] );
			$this->Assign( 'count',$r[ 'content' ] );
			$this->Assign( 'time',$r[ 'time' ] );	
			$this->Display();
	}
}	
?>