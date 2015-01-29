<?php
/**
 * 微汽车部分
 * add by wuhaiyan 2014/3/26       
 */
class CarAction extends BaseAction
{
	private $token;
    public  $salestype = array();
    private $wecha_id;
	public function _initialize()
    {
        parent::_initialize();
        $this->token = $_GET['token'];
        $this->assign( 'token', $this->token );
        $this->wecha_id = $_GET['wecha_id'];
        $this->assign( 'wecha_id',$this->wecha_id );

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
     * 前台首页
     */
    public function index()
    {
        $img = M('Car_set')->field('head_url')->where( array('token'=>$this->token) )->find();
        $this->assign('head_url',$img['head_url']);
        $this->display();
    }

    /**
     * 车品牌列表
     */
    public function brands()
    {
    	$data = M('Car_brand');
        $where['token'] = $this->token;
        //$count = $data->where( $where )->count();
        //$Page = new Page( $count,10 );
        //$show = $Page->show();
        $brand = $data->where($where)->order('sort desc')->select(); 
        //$brands = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        //$this->assign('page',$show);
        $this->assign('brand',$brand);
        $this->display();
    }

    /**
     * 车系列表
     */
    public function series()
    {
        if( $_GET['bid']){
            $bid = intval($_GET['bid']);
            $where['brand_id'] = $bid;
        }
        $where['token'] = $this->token;
        $brand = M('Car_brand')->field('brand,logo')->where( array( 'id'=>$bid,'token'=>$this->token ) )->find();
        $this->assign( 'brand',$brand );
        $data = M('Car_series');
        $series = $data->where( $where )->order('sort desc')->select();
        for( $i=0;$i< count($series);$i++ ){
            $series[$i]['models'] = M('Car_model')->field('id,model,guide_price,dealer_price')->where( array('id'=>$series[$i]['id'],'token'=>$this->token) )->select();
        }
        $this->assign('series',$series);
        $this->display();
    }

    /**
     * 车型列表
     */
    public function models()
    {
        $sid = intval($_GET['sid']);
        $data = M('Car_model');
        $where['s_id'] = $sid;
        $where['token'] = $this->token;
        $thisSeries = M('Car_series')->field('id,name,brand_id,picture')->where( array('id' => $sid,'token'=>$this->token) )->find();
        $this->assign( 'series',$thisSeries );
        $brand = M('Car_brand')->field('logo')->where( array('id'=> $thisSeries['brand_id'],'token'=>$this->token ) )->find();
        $this->assign( 'brand_logo',$brand['logo'] );
        $models = $data->where( $where )->order('sort desc')->select(); 
        $this->assign('models',$models);
        $this->display();
    }

    /**
     * 销售顾问列表
     */
    public function salers()
    {
        $data = M('Car_saler');
        $where['token'] = $this->token;
        $salers = $data->where( $where )->order('sort desc')->select();
        $this->assign('salers',$salers);
        $this->display();
    }

    /**
     * 进入预约
     *
     */
    public function CarReserveBook()
    {
        $my = M('Car_owners')->where( array('wecha_id' => $this->wecha_id ) )->find();
        $this->assign( 'my',$my );
        $addtype = $_GET['addtype'];
        $where['token'] = $this->token;
        $where['addtype'] = $addtype;
        $model = M('Car_reservebook');
        $find = $model->where( $where )->find();

        //获取预约内容相关信息
        $resInfo = M('Car_reservation')->field('id,info,address,tel')->where( array('token'=>$this->token,'addtype'=>$addtype) )->find();
        if( empty($resInfo) ){//如果商家没有填写预约信息这里报错
            $this->error('暂无预约服务或预约已关闭');
        }
        $this->assign( 'resInfo',$resInfo );


        //品牌、车系调用
        $brandModel = M('Car_brand');
        $allbrand = $brandModel->field('id,brand')->where( array('token'=>$this->token) )->select();
        $this->assign( 'allbrand',$allbrand );

        if( $find['brand_id'] ){
            $brand_id = $find['brand_id'];
        }
        else{
            $firstbrand = current($allbrand);
            $brand_id = $firstbrand['id'];
        }
        
        $this->assign( 'brand_id',$brand_id );
        $swhere['token'] = $this->token;
        $swhere['brand_id'] = $brand_id;

        $thisSeries = M('Car_series')->where( $swhere )->select();
        $this->assign( 'series',$thisSeries );

        if( $find['series_id'] ){
            $sid = $find['series_id'];
        }
        else{
            $firstseries = current($thisSeries);
            $sid = $firstseries['id'];
        }
        $this->assign( 'sid',$sid );
        if( IS_POST ){
            if( $find ){
                if( $this->wecha_id != $find['wecha_id'] ){
                    $this->error('非法操作');
                }
                $_POST['dateline'] = strtotime( trim($_POST['dateline']) );
                $_POST['booktime'] = time(); 
                $res = $model->where( array('token'=>$this->token,'id'=>$find['id'] ) )->save($_POST);
                if( $res ){
                    $this->success('预约修改成功');
                }else{
                    $this->error('预约修改失败或无任何修改');
                }
            }else{
                $addData['token'] = $this->token;
                $addData['rid'] = $resInfo['id'];
                $addData['wecha_id'] = $this->wecha_id;
                $addData['truename'] = $_POST['truename'];
                $addData['tel'] = $_POST['tel'];
                $addData['addtype'] = $addtype;
                $addData['brand_id'] = $_POST['brand_id'];
                $addData['series_id'] = $_POST['series_id'];
                $addData['dateline'] = strtotime( trim($_POST['dateline']) );
                $addData['timepart'] = trim($_POST['timepart']); 
                $addData['info'] = trim($_POST['info']);
                $addData['booktime'] = time();     
                $addres = $model->add( $addData );
                if( $addres ){
                    $this->success( '预约成功' );
                }else{
                    $this->error( '预约失败' );
                }      
            }
        }else{
            $this->assign( 'reser',$find );
            $this->display();
        }
    }

    /**
     * 修改个人资料
     *
     */
    public function ownerInfo()
    {
        $wecha_id = $_GET['wecha_id'];
        $model = M('Car_owners');
        $find = $model->where( array('token'=>$this->token,'wecha_id'=>$wecha_id ) )->find();
        $brandModel = M('Car_brand');
        $allbrand = $brandModel->field('id,brand')->where( array('token'=>$this->token) )->select();
        $this->assign( 'allbrand',$allbrand );
        $this->assign( 'brand_id',$find['brand_id'] );
        $swhere['token'] = $this->token;
        $swhere['brand_id'] = $find['brand_id'];
        $swhere['token'] = $this->token;
        if( empty($find) ){
            $firstbrand = current($allbrand);
            $swhere['brand_id'] = $firstbrand['id'];
        }
        $thisSeries = M('Car_series')->where( $swhere )->select();
        $this->assign( 'series',$thisSeries );
        $this->assign( 'sid',$find['series_id'] );
        if( IS_POST ){
            if( $find ){
                $_POST['car_startTime'] = strtotime( trim($_POST['car_startTime']) );
                $_POST['car_buyTime'] = strtotime( trim($_POST['car_buyTime']) );
                $res = $model->where( array('token'=>$this->token,'wecha_id'=>$wecha_id) )->save($_POST);
                if( $res ){
                    $this->success('个人资料修改成功');
                }else{
                    $this->error('修改失败或无任何修改');
                }
            }else{
                $_POST['token'] = $this->token;
                $_POST['wecha_id'] = $wecha_id;
                $_POST['car_startTime'] = strtotime( trim($_POST['car_startTime']) );
                $_POST['car_buyTime'] = strtotime( trim($_POST['car_buyTime']) );
                $result = $model->create($_POST);
                if( $result ){
                    $ress = $model->add($_POST);
                    if( $ress ){
                        $this->success( '个人资料添加成功' );
                    }else{
                        $this->error( '个人资料添加失败' );
                    }
                }else{
                    $this->error( '操作失败' );
                }
            }
        }else{
            $this->assign('user',$find);
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
}