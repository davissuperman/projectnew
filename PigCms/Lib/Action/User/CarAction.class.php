<?php
/**
 * 微汽车部分
 * add by wuhaiyan 2014/3/26       
 */
class CarAction extends UserAction
{
	private $token;
    public $salestype = array();

	public function _initialize() {
        parent::_initialize();
        $this->token = session('token');
        //判断权限
		if( $this->token != $_GET['token'] ){
			exit();
		}
        $this->assign( 'token', $this->token );
        //变速箱类型
        $box = array(
            1 => '自动变速箱(AT)',
            2 => '手动变速箱(MT)',
            3 => '手自一体',
            4 => '无级变速箱(CVT)',
            5 => '无级变速(VDT)',
            6 => '双离合变速箱(DCT)',
            7 => '序列变速箱(AMT)',
            );
        $this->assign( 'box',$box );
        //年款
        $years = array();
        $y = 22;
        for( $yi = 2022;$yi > 1999;$yi--,$y-- ){
            $years[$y] = $yi;
        }
        $this->assign( 'years',$years );
        //销售顾问类型
        $salestype = array(
            1 => '售前',
            2 => '售后',
            3 => '售前/售后',
            );
        $this->salestype = $salestype;
        $this->assign( 'salestype',$salestype );
    }

    /**
     * 车品牌列表
     */
    public function index()
    {
    	$data = M('Car_brand');
        $where['token'] = $this->token;
        $count = $data->where( $where )->count();
        $Page = new Page( $count,10 );
        $show = $Page->show();
        $brands = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('page',$show);
        $this->assign('brands',$brands);
        $this->display();
    }

    /**
     * 车品牌添加
     */
    public function addBrand()
    {
    	if(IS_POST){
            if( strlen($_POST['brand']) < 1 ){
                $this->error('品牌名称不能为空');
            }
            if( strlen($_POST['url']) < 1 ){
                $this->error('品牌官网不能为空');
            }
            $find = M('Car_brand')->where( array('token'=>$this->token,'brand'=>trim($_POST['brand'])) )->find();
            if( $find ){
                $this->error('此品牌已存在');
            }

            $data = D('Car_brand');     
            $_POST['token'] = $this->token;
            if( $data->create() != false ){
                if( $id = $data->add( $_POST ) ){
                    $this->success('添加成功',U('Car/index',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->display();
        }
    }

    /**
     * 车品牌修改
     */
    public function editBrand()
    {
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['bid'] );
        $data = M('Car_brand');
        $have = $data->where( $where )->find();        
        if( $have == false ){
            $this->error('非法操作');
        }
    	if(IS_POST){
            if( strlen($_POST['brand']) < 1 ){
                $this->error('品牌名称不能为空');
            }
            if( strlen($_POST['url']) < 1 ){
                $this->error('品牌官网不能为空');
            }
            $find = M('Car_brand')->where( array('token'=>$this->token,'brand'=>trim($_POST['brand'])) )->find();
            if( $find && ($find['id'] != $have['id']) ){
                $this->error('此品牌已存在');
            }

            if( $data->create() ){
                $ok = $data->where( $where )->save( $_POST );
                if( $ok ){
                    $this->success('修改成功',U('Car/index',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('操作失败');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->assign( 'brand',$have );
            $this->display();
        }
    }

    /**
     * 车品牌删除
     */
    public function delBrand()
    {
        $model = M('Car_brand');
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['bid'] );        
        $result = $model->where( $where )->find();
        if( $result ){
            $model->where( array('id'=>$result['id']) )->delete();
            $this->success('删除成功',U('Car/index',array( 'token'=>$this->token ) ) );
        }else{
            $this->error('非法操作！');
        }
    }

    /**
     * 车系列表
     */
    public function series()
    {
        $data = M('Car_series');
        $where['token'] = $this->token;
        if( $_GET['brand_id'] ){
            $where['brand_id'] = intval($_GET['brand_id']);
        }
        $count = $data->where( $where )->count();
        $Page = new Page( $count,10 );
        $show = $Page->show();
        $series = $data->where( $where )->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('page',$show);
        $this->assign('series',$series);
        $this->display();
    }

    /**
     * 车系添加
     */
    public function addSeries()
    {
    	$where['token'] = $this->token;
        $brand_model = M('Car_brand');
        $brands = $brand_model->where( $where )->field('id,brand')->select();
        $this->assign( 'brands',$brands );
        if(IS_POST){
            if( empty($_POST['name']) ){
                $this->error('车系名称不能为空');
            }
            $select_brand = explode( '--',$_POST['brand'] );
            $model = M('Car_series');
            if( empty($_POST['shortname']) ){
                $_POST['shortname'] = $_POST['name'];
            }
            $_POST['token'] = $this->token;
            $_POST['brand_id'] = $select_brand[0];
            $ff = M('Car_series')->where( array('token'=>$this->token,'brand_id'=>$_POST['brand_id'],'name'=>$_POST['name']) )->find();
            if( $ff ){
                $this->error('此车系已存在');
            }

            $_POST['brand'] = $select_brand[1];
            if( $model->create() != false ){
                if( $model->add( $_POST ) ){
                    $this->success('添加成功',U('Car/series',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error( $model->getError() );
            }
        }else{
            $this->display();   
        }
    }

    /**
     * 车系修改
     */
    public function editSeries()
    {
    	$where['token'] = $this->token;
        $where['id'] = intval( $_GET['sid'] );
        $data = M('Car_series');
        $have = $data->where( $where )->find();
        if( $have == false ){
            $this->error('非法操作');
        }
        $this->assign('thisbid',$have['brand_id']);   
        
        $brand_model = M('Car_brand');
        $brands = $brand_model->where( array('token'=>$this->token) )->field('id,brand')->select();
        $this->assign( 'brands',$brands );
        if(IS_POST){
            if( empty($_POST['name']) ){
                $this->error('车系名称不能为空');
            }
            $select_brand = explode( '--',$_POST['brand'] );
            $_POST['token'] = $this->token;
            $_POST['brand_id'] = $select_brand[0];
            $_POST['brand'] = $select_brand[1];
            $ff = M('Car_series')->where( array('token'=>$this->token,'brand_id'=>$_POST['brand_id'],'name'=>$_POST['name']) )->find();
            if( $ff && ($ff['id'] != $have['id'])  ){
                $this->error('此车系已存在');
            }

            if( $data->create() ){
                $ok = $data->where( $where )->save( $_POST );
                if( $ok ){
                    $this->success('修改成功',U('Car/series',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('操作失败或无任何更改');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->assign( 'series',$have );
            $this->display();
        }
    }

    /**
     * 车系删除
     */
    public function delSeries()
    {
    	$model = M('Car_series');
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['sid'] );        
        $result = $model->where( $where )->find();
        if( $result ){
            $model->where( array('id'=>$result['id']) )->delete();
            $this->success('删除成功',U('Car/series',array( 'token'=>$this->token ) ) );
        }else{
            $this->error('非法操作！');
        }
    }

    /**
     * 车型列表
     */
    public function models()
    {
        $data = M('Car_model');
        $where['token'] = $this->token;
        if( $_GET['s_id'] ){
            $where['s_id'] = intval($_GET['s_id']);
        }
        $count = $data->where($where)->count();
        $Page = new Page( $count,10 );
        $show = $Page->show();
        $models = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('page',$show);
        $this->assign('models',$models);
        $this->display();
    }

    /**
     * 车型添加
     */
    public function addModel()
    {
    	$brand_model = M('Car_brand');
        $brands = $brand_model->where( array( 'token'=>$this->token ) )->field('id,brand')->select();
        $this->assign( 'brands',$brands );
        $series = M( 'Car_series' )->field( 'id,name' )->where( array( 'token'=>$this->token ,'brand_id'=>$brands[0]['id']))->select();
        $this->assign( 'series',$series );
        $nowyear = date('Y');
        $this->assign('nowyear',$nowyear);        
        if( IS_POST ){
            if( empty($_POST['model']) ){
                $this->error('车型名称不能为空');
            }
            if( empty($_POST['series']) ){
                $this->error('请选择车系');
            }

            $select_series = explode('--',$_POST['series']);
            $data = M('Car_model');
            $_POST['token'] = $this->token;
            $_POST['s_id'] = intval( $select_series[0] );
            $mm = M('Car_model')->where( array('token'=>$this->token,'s_id'=>$_POST['s_id'],'model'=>$_POST['model']) )->find();
            if( $mm ){
                $this->error('车型已存在');
            }

            $_POST['brand_series'] = $select_series[1];
            $_POST['panorama_id'] = $_POST['panorama_id'] ? intval($_POST['panorama_id']) : 0;
            if( $data->create() != false ){
                if( $data->add( $_POST ) ){
                    $this->success('添加成功',U('Car/models',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
           $this->display(); 
        }
    }

    /**
     * 通过车品牌调出车系
     */
    public function getSeries()
    {
    	$where['brand_id'] = intval( $_POST['bid'] );
        $where['token'] = $this->token;
        $data = M('Car_series');
        $series = $data->field('id,name')->where( $where )->select();
        echo json_encode($series);
    }

    /**
     * 车型修改
     */
    public function editModel()
    {
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['mid'] );
        $data = M('Car_model');
        $have = $data->where( $where )->find();        
        if( $have == false ){
            $this->error('非法操作');
        }
        //查出所有品牌
        $brand_model = M('Car_brand');
        $brands = $brand_model->where( array( 'token'=>$this->token ) )->field('id,brand')->select();
        $this->assign( 'brands',$brands );

        //找出当前车型的品牌
        $thisSeries = M('Car_series')->field('brand_id')->where( array('id'=>$have['s_id'],'token'=>$this->token) )->find();
        $brand_id = $thisSeries['brand_id'];
        $this->assign( 'brand_id',$brand_id );
        $this->assign( 'thisSid',$have['s_id'] );
        
        //查出此车型对应品牌下的所有车系，以便进行车系更改
        $series = M( 'Car_series' )->field( 'id,name' )->where( array('brand_id'=>$brand_id,'token'=>$this->token) )->select();
        $this->assign( 'series',$series );
        
        if(IS_POST){
            if( empty($_POST['model']) ){
                $this->error('车型名称不能为空');
            }
            if( empty($_POST['series']) ){
                $this->error('请选择车系');
            }

            $select_series = explode('--',$_POST['series']);
            $_POST['token'] = $this->token;
            $_POST['s_id'] = intval( $select_series[0] );
            $_POST['brand_series'] = $select_series[1];
            $_POST['panorama_id'] = $_POST['panorama_id'] ? intval($_POST['panorama_id']) : 0;
            $mm = M('Car_model')->where( array('token'=>$this->token,'s_id'=>$_POST['s_id'],'model'=>$_POST['model']) )->find();
            if( $mm && ($mm['id'] != $have['id']) ){
                $this->error('车型已存在');
            }            

            if( $data->create() ){
                $ok = $data->where( $where )->save( $_POST );
                if( $ok ){
                    $this->success('修改成功',U('Car/models',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('操作失败或无任何更改');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->assign( 'model',$have );
            $this->display();
        }
    }

    /**
     * 车型删除
     */
    public function delModel()
    {
    	$model = M('Car_model');
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['mid'] );        
        $result = $model->where( $where )->find();
        if( $result ){
            $model->where( array('id'=>$result['id']) )->delete();
            $this->success('删除成功',U('Car/models',array( 'token'=>$this->token ) ) );
        }else{
            $this->error('非法操作！');
        }
    }

    /**
     * 销售顾问列表
     */
    public function salers()
    {
        $data = M('Car_saler');
        $where['token'] = $this->token;
        $count = $data->where( $where )->count();
        $Page = new Page( $count,10 );
        $show = $Page->show();
        $this->assign('page',$show);
        $salers = $data->where( $where )->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $salestype = $this->salestype;
        for( $i = 0;$i < count($salers) ;$i++ ){
            $salers[$i]['salestype'] = $salestype[$salers[$i]['salestype']];
        }
        $this->assign('salers',$salers);
        $this->display();
    }

    /**
     * 销售顾问添加表单加载
     */
    public function addSaler()
    {
        if(IS_POST){
            $data = D('Car_saler');     
            $_POST['token'] = $this->token; 
            if( $data->create() != false ){
                if( $id = $data->add( $_POST ) ){
                    $this->success('添加成功',U('Car/salers',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->display();
        }
    }

    /**
     * 销售顾问修改
     */
    public function editSaler()
    {
    	$where['token'] = $this->token;
        $where['id'] = intval( $_GET['sid'] );
        $data = M('Car_saler');
        $have = $data->where( $where )->find();        
        if( $have == false ){
            $this->error('非法操作');
        }
        if(IS_POST){
            if( $data->create() ){
                $ok = $data->where( $where )->save( $_POST );
                if( $ok ){
                    $this->success('修改成功',U('Car/salers',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('操作失败或无任何更改');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->assign( 'saler',$have );
            $this->display();
        }
    }

    /**
     * 销售顾问删除
     */
    public function delSaler()
    {
    	$model = M('Car_saler');
        $where['token'] = $this->token;
        $where['id'] = intval( $_GET['sid'] );        
        $result = $model->where( $where )->find();
        if( $result ){
            $model->where( array('id'=>$result['id']) )->delete();
            $this->success('删除成功',U('Car/salers',array( 'token'=>$this->token ) ) );
        }else{
            $this->error('非法操作！');
        }
    }

    /**
     * 车主关怀添加/修改
     */
    public function ownerCare()
    {
    	$where = array( 'token'=>$this->token );
    	$model = M( 'car_owner_care' );
    	$thisCare = $model->where( $where )->find();
    	if( IS_POST ){
    		if( empty( $_POST['title'] ) ){
    			$this->error('标题不能为空');
    		}
    		$_POST['token'] = $this->token;
            $_POST['keyword'] = '车主关怀';
    		if( !$thisCare ){
                $coid = $model->add($_POST);
                if( $coid ){
                    $kdata['keyword'] = '车主关怀';
                    $kdata['pid'] = $coid;
                    $kdata['token'] = $this->token;
                    $kdata['module'] = 'Car';
                }
                M('Keyword')->add( $kdata );
                $this->success( '设置成功',U('Car/ownerCare',array( 'token'=>$this->token ) ) );
			}else {
				if( $model->create() ){
					if( $model->where( $where )->save( $_POST ) ){
						$this->success( '修改成功',U('Car/ownerCare',array( 'token'=>$this->token ) ) );
					}else{
						$this->error('操作失败');
					}
				}else{
					$this->error( $model->getError() );
				}
			}
    	}else{
    		$this->assign( 'care',$thisCare );
    		$this->display();
    	}
    }

    /**
     * 汽车回复设置
     */
    public function set()
    {
    	$where = array( 'token'=>$this->token );
    	$setModel = M( 'Car_set' );
    	$thisCarSet = $setModel->where( $where )->find();
    	if( IS_POST ){
    		$_POST['token'] = $this->token;
    		if( !$thisCarSet ){
                $set_id = $setModel->add($_POST);
                if( $set_id ){
                    $kdata['keyword'] = '汽车';
                    $kdata['pid'] = $set_id;
                    $kdata['token'] = $this->token;
                    $kdata['module'] = 'Car';
                }
                M('Keyword')->add( $kdata );
				$this->success( '设置成功',U('Car/set',array( 'token'=>$this->token ) ) );
			}else {
				if( $setModel->create() ){
					if( $setModel->where( $where )->save( $_POST ) ){
						$this->success( '修改成功',U('Car/set',array( 'token'=>$this->token ) ) );
					}else{
						$this->error('操作失败');
					}
				}else{
					$this->error( $setModel->getError() );
				}
			}
    	}else{
    		$this->assign( 'set',$thisCarSet );
    		$this->display();
    	}
    }
    
    /**
     * 汽车文章
     */
    public function news()
    {
        $where['token'] = $this->token;
        //调出文章分类
        $t_classify = M('Classify');
        $classify = $t_classify->where( $where )->order('id desc')->select();
        $this->assign( 'classify',$classify );
        //调用所有相册
        $Photo = M( "Photo" );
        $photo = $Photo->where( $where )->order( 'id desc' )->select();
        $this->assign( 'photo',$photo );
        //调出此商家的所有店铺
        $company = M('Company')->where( $where )->field('id,name')->select();
        $this->assign( 'company',$company );
        $data = M( 'Car_news' );
        $carnews = $data->where( $where )->find();
        if(IS_POST){
            if( $carnews == null ){
                $_POST['token'] = $this->token;
                $res = $data->add( $_POST );
                if( $res ){
                    $this->success('添加成功',U('Car/news',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                if( $data->where( $where )->save($_POST) ){
                    $this->success('修改成功',U( 'Car/news',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('操作失败');
                }
           }
        }else{
          $this->assign( 'news',$carnews );
          $this->display();
        }
    }

    /**
     * 预约管理
     */
    public function reservation()
    {

    	$data = M("Car_reservation");
        $where = "`token`='".$this->token."' AND (`addtype`='drive' OR `addtype`='maintain')";
        $count = $data->where( $where )->count();
        $Page = new Page( $count,10 );
        $show = $Page->show();
        $reslist = $data->where( $where )->order('id DESC')->limit( $Page->firstRow.','.$Page->listRows)->select(); 
        $drive_count = $data->where(array('addtype' => 'drive','token'=>$this->token ))->count();
        $maintain_count = $data->where(array('addtype' => 'maintain','token'=>$this->token ))->count();
        $this->assign('drive_count',$drive_count);
        $this->assign('maintain_count',$maintain_count);
        $this->assign('reslist',$reslist);
        $this->display();
    }

    /**
     * 添加预约
     */
    public function addRes()
    {
    	$_POST['token'] = $this->token;
        $addtype = $_GET['addtype'];
        $this->assign('addtype',$addtype);
        if( IS_POST ){
            $data = M('Car_reservation');
            $_POST['addtype'] = $addtype;
            if( $data->create() != false ){
                if( $id = $data->data($_POST)->add() ){
                    $data1['pid'] = $id;
                    $data1['module'] = 'Reservation';
                    $data1['token'] = $this->token;
                    if( $addtype == 'drive'){
                        $data1['keyword'] = '预约试驾';
                    }else{
                        $data1['keyword'] = '预约保养';
                    }                    
                    M('Keyword')->add( $data1 );
                    $this->success('添加成功',U('Car/reservation',array( 'token'=>$this->token ) ) );
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error( $data->getError() );
            }
        }else{
            $this->display();
        }
    }

    /**
     * 修改预约
     */
    public function editRes()
    {
    	$_POST['token'] = $this->token;
    	$_POST['addtype'] = $_GET['addtype'];
        $addtype = $_GET['addtype'];
        $this->assign('addtype',$addtype);
     	$id = intval( $_GET['rid'] );
    	$data = M('Car_reservation');
        $where['token'] = $this->token;
        $where['id'] = $id;
        $check = $data->where( $where)->find();
        if( $check == false ) $this->error('非法操作');
        if(IS_POST){
            if( $data->create() ){
                if($data->where( $where )->save($_POST)){
                    $data1['pid'] = $id;
                    $data1['module']='Reservation';
                    $data1['token'] = $this->token;
                    $da['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->where( $data1 )->save($da);
                    $this->success('修改成功',U('Car/reservation',array('token'=>$this->token )));
                }else{
                    $this->error('操作失败');
                }
            }else{
                $this->error($data->getError());
            }
        }else{
            $this->assign('res',$check);
            $this->display();
        }
    }

    /**
     * 删除预约
     */
    public function delRes()
    {
        $id = intval( $_GET['rid'] );
        $res = M('Car_reservation');
        $find = array( 'id'=>$id,'token'=>$this->token );
        $result = $res->where( $find )->find();
        if( $result ){
            $res->where('id='.$result['id'])->delete();
            $where = array( 'pid'=>$id ,'module'=>'Reservation','token'=>$this->token );
            M('Keyword')->where( $where )->delete();
            $this->success('删除成功',U('Car/reservation',array('token'=>$this->token )));
        }else{
            $this->error('非法操作！');
        }
    }

    /**
     * 预约订单管理
     */
    public function resManage()
    {
        $this->display();
    }

    /**
     * 车主信息列表
     */
    public function owners()
    {
        $model = M('Car_owners');
        $count = $model->where(array('token' => $this->token))->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $this->assign('page', $show);

        $owners = $model->where( array('token'=>$this->token) )->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for( $i = 0;$i < count($owners);$i++ ){
            $brand = M('Car_brand')->where( array('token'=>$this->token,'id'=>$owners[$i]['brand_id']) )->find();
            $owners[$i]['brand_id'] = $brand['brand'];
            $series = M('Car_series')->where( array('token'=>$this->token,'id'=>$owners[$i]['series_id']) )->find();
            $owners[$i]['series_id'] = $series['name'];
        }
        $this->assign('owners',$owners);
        $this->display();
    }
}