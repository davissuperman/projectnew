<?php

class GreetingAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('bonus')->count();
        $page = new Page($count, 25);
        $list = M('greeting')->query("SELECT* from tp_greeting order by view desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $listArr = array();
        foreach($list as $each){
            $awardInfo = '';
            $tmp = $each;
            $condition['openid'] = $each['openid'];
            $tmp['awardlist'] = $awardInfo;
            $name = M('customer_service_fans')->where(array('openid' => $condition['openid'],'token'=>'rggfsk1394161441'))->getField('nickname');
            $tmp['name'] = $name;
            $listArr[] = $tmp;
        }
        $this->assign('info', $listArr);
        $this->display();
    }

}
