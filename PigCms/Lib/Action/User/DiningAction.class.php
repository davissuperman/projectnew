<?php
/**
 * 微餐饮点菜类
 * add by wuhaiyan 2014/3/22
 */
class DiningAction extends UserAction
{
	public $token;
	public function _initialize() {
		parent::_initialize();
		$this->token=session('token');
		$this->assign('token',$this->token);
	}

	/**
	 * 店铺管理
	 */
	public function index()
	{
		$this->display();
	}

	/**
	 * 菜品分类管理
	 */
	public function cates()
	{
		$this->display();
	}

	/**
	 * 菜品管理
	 */
	public function dishes()
	{
		$this->display();
	}

	/**
	 * 订单管理
	 */
	public function orders()
	{
		$this->display();
	}

	
}