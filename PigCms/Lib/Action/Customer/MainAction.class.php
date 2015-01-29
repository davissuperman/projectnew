<?php

session_start();

/**
 * 
 * 客服操作
 * @author chengyun
 */
class MainAction extends CommonAction {

    public function _initialize() {
        parent :: _initialize();
        //$this->token = $_SESSION['token'];
    }

    /**
     * 客服主界面
     */
    public function index() {
        //粉丝分组
        $group = M('customer_service_fans_group')->where(array('token' => session('token')))->order('status asc')->select();
        $this->assign('group', $group);
        //通话类型
        $list = M('customer_service_fans_visitors_type')->where(array('token' => session('token')))->select();
        $this->assign('list', $list);
        //和我通话的粉丝列表       
        $where = array('token' => session('token'), 'uid' => array('neq', session('userId')));
        $servicelist = M('customer_service_fans')->where($where)->select();        
        $this->assign('fancelist', $servicelist); 
        
        $this->assign('times', time());  
        $this->display();
    }

}
