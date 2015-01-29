<?php

/*
  客服系统登录验证
 */

class CommonAction extends Action {

    public function _initialize() {        
        if (!session('userName')&&!session('token')&&!session('userId')) { 
            $this->error('您必须登陆后才能操作', U('Index/index'));
        }
    }

}
