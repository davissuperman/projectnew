<?php

/**
 * 客服登录 只针对森田 不让输入token就可以了
 */
class IndexAction extends Action {

    /**
     * 客服登录
     */
    public function index() {
        if (IS_POST) {
            $data['userpwd'] = md5($this->_post('userpwd', 'htmlspecialchars'));
            $data['username'] = $this->_post('username', 'htmlspecialchars');
            $data['cookietime'] = $this->_post('cookietime', 'htmlspecialchars');
            if ($data['username'] == false || $data['userpwd'] == false) {
                $this->error('帐号必须填写');
            }
            $back = M('customer_service_users')->where($data)->find();
            if ($back && $back['username'] == $data['username'] && $back['userpwd'] == $data['userpwd']) {
                if ($data['cookietime']) {
                    session('userId', $back['uid']);
                    session('number', $back['uid']);
                    session('name', $back['name']);
                    session('type', $back['type']);
                    session('token', $data['token']);
                    session('userName', $back['username']);
                    session('cookietime', $data['cookietime']);
                    cookie('userId', $back['uid'], 604800);
                    cookie('number', $back['uid'], 604800);
                    cookie('name', $back['name'], 604800);
                    cookie('type', $back['type'], 604800);
                    cookie('token', $data['token'], 604800);
                    cookie('userName', $back['username'], 604800);
                    cookie('userpwd', $this->_post('userpwd', 'htmlspecialchars'), 604800);
                    cookie('cookietime', $data['cookietime'], 604800);
                } else {
                    session('userId', $back['uid']);
                    session('number', $back['uid']);
                    session('name', $back['name']);
                    session('type', $back['type']);
                    session('token', $back['token']);
                    session('userName', $back['username']);
                    session('cookietime', $back['cookietime']);
                }
                $this->connect();
                $this->success('登陆成功', U('Main/index'));
            } else {
                $this->error('您的登陆信息错误请！');
            }
        } else {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (!strpos($agent, "Firefox") && !strpos($agent, "Chrome")) {
                echo '<center style="color:red">此功能只能用 Firefox , Chrome 浏览器操作</center>';               
            }         
            $data['userpwd'] = cookie('userpwd');
            $data['username'] = cookie('userName');
            $this->assign('username', $data['username']);
            $this->assign('userpwd', $data['userpwd']);
            $this->display();
        }
    }

    /**
     * 退出
     */
    public function logout() {
        //修改客服状态
        M('customer_service_users')->where(array('uid' => session('userId')))->save(array('status' => 0, 'services' => 0, 'queueus' => 0, 'openid' => ''));
        //修改和我聊天的人状态
        M('customer_service_fans_status')->where(array('uid' => session('userId')))->save(array('status' => 0, 'uid' => 0));
       
        
        session('userId', '');
        session('number', '');
        session('name', '');
        session('type', '');
        session('token', '');
        session('userName', '');
        session('cookietime', '');
        $this->success('成功退出', U('Index/index'));
    }

    /**
     * 用户点击浏览器退出
     */
    public function closeie() {
        //修改客服状态
        M('customer_service_users')->where(array('uid' => session('userId')))->save(array('status' => 0, 'services' => 0, 'queueus' => 0, 'openid' => ''));
        //修改和我聊天的人状态
        M('customer_service_fans_status')->where(array('uid' => session('userId')))->save(array('status' => 0, 'uid' => 0));
    }

    /**
     * 连接成功
     */
    public function connect() {
        M('customer_service_users')->where(array('uid' => session('userId')))->save(array('status' => 1));
        return true;
    }

}

?>