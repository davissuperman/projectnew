<?php
class CompanyAction extends BaseAction{
	public $token;
	public $apikey;
	public function _initialize() {
		$this->token=$this->_get('token');
		$this->assign('token',$this->token);
		$this->apikey=C('baidu_map_api');
		$this->assign('apikey',$this->apikey);
		$this->assign('staticFilePath',str_replace('./','/',THEME_PATH.'common/css/product'));
	}
	public function map(){
		//店铺信息
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}		
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		//分店信息
		/*$branchStores=$company_model->where(array('token'=>$this->token,'isbranch'=>1))->order('taxis ASC')->select();
		$branchStoreCount=count($branchStores);
		$this->assign('branchStoreCount',$branchStoreCount);
		$this->assign('branchStores',$branchStores);*/
		$this->assign('metaTitle','地图');
		$this->assign('companyid',$thisCompany['id'] );
		$this->assign('openid',trim($_GET['openid']) );
		$this->assign('type',intval($_GET['type']) );
		$this->display();
	}
	public function walk($display=1){
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		$this->assign('metaTitle','步行路线');
		if ($display){
		$this->display();
		}
	}
	public function bus(){
		$this->walk(0);
		$this->assign('metaTitle','公交地铁路线');
		$this->display('bus');
	}
	public function drive(){
		$this->walk(0);
		$this->assign('metaTitle','开车路线');
		$this->display('drive');
	}

	public function countCompanyNav()
	{
		$cid = intval($_GET['companyid']);
		if( $cid < 0 || empty($_GET['type']) || empty($_GET['openid']) || empty($this->token) ){
			exit;
		}
		$model = M('Company_nav');
		$data['type'] = intval($_GET['type']);
		$data['openid'] = trim($_GET['openid']);
		$data['token'] = $this->token;
		$data['shop_id'] = $cid;
		$data['time'] = time();
		$data['date'] = date('Y-m-d');
		$data['hour'] = date('H');
		$res = $model->add($data);
		exit;
	}
}


?>