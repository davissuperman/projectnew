<?php

class LeaveAction extends UserAction {

    public function _initialize() {
        parent::_initialize();
        $token_open = M('token_open')->field('queryname')->where(array('token' => session('token')))->find();
        if (!strpos($token_open['queryname'], 'weixin')) {
            $this->error('您还开启该模块的使用权,请到功能模块中添加', U('Function/index', array('token' => session('token'), 'id' => session('wxid'))));
        }
    }

    public function Index() {
        $token = $this->_get('token', 'trim');
        $token = !empty($token) ? $token : exit;
        if (IS_POST) {
            $title = $this->Gl($_POST['title']);
            $info = $this->Gl($_POST['info']);
            $picurl = $this->Gl($_POST['picurl']);
            $apiurl = $this->Gl($_POST['apiurl']);
            $token = $this->Gl($_POST['token']);
            $M = M('message_uleave');
            $S = $M->where(array('token' => $token))->find();
            if (empty($S)) {
                $data['utitle'] = $title;
                $data['udescription'] = $info;
                $data['upicurl'] = $picurl;
                $data['uurl'] = $apiurl;
                $data['token'] = $token;
                $id = $M->add($data);
                if ($id) {
                    $this->success('回复添加成功', U('User/Leave/index', array('token' => $token)));
                    exit;
                } else {
                    $this->error('回复添加失败', U('User/Leave/index', array('token' => $token)));
                }
            } else {
                $data['utitle'] = $title;
                $data['udescription'] = $info;
                $data['upicurl'] = $picurl;
                $data['uurl'] = $apiurl;
                $data['token'] = $token;
                $m = $M->where(array('token' => $token))->save($data);
                if ($m) {
                    $this->success('回复更新添加成功', U('User/Leave/index', array('token' => $token)));
                    exit;
                } else {
                    $this->error('回复更新添加失败', U('User/Leave/index', array('token' => $token)));
                }
            }
        } else {
            $d = M('message_uleave');
            $in = $d->where(array('token' => $token))->find();
            $this->Assign('title', $in['utitle']);
            $this->Assign('info', $in['udescription']);
            $this->Assign('picurl', $in['upicurl']);
            $this->Assign('apiurl', $in['uurl']);
            //$this->Assign( 'token',$in[ 'token' ] );	
        }
        $this->Display();
    }

    public function info() {
        $token = $this->_get('token', 'trim');
        $M = M('message_leave');
        $count = $M->where(array('token' => $token))->count();
        $page = new Page($count, 15);
        $info = $M->where(array('token' => $token))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->Assign('list', $info);
        $this->Display();
    }

    public function oDel() {
        $id = $this->_get('id', 'intval');
        $token = $this->_get('token', 'trim');
        $m = M('message_leave');
        if ($m->where(array('lid' => $id, 'token' => $token))->delete()) {
            $D = M('message_leaver');
            if ($D->where(array('fid' => $id, 'token' => $token))->delete()) {
                $this->success('操作成功', U('User/Leave/info', array('token' => $token)));
            } else {
                $this->error('子操作失败', U('User/Leave/info', array('token' => $token)));
            }
        } else {
            $this->error('操作失败', U('User/Leave/info', array('token' => $token)));
        }
    }

    public function i() {
        $id = $this->_get('id', 'intval');
        $token = $this->_get('token', 'trim');
        $M = M('message_leaver');
        $count = $M->where(array('token' => $token, 'fid' => $id))->count();
        $page = new Page($count, 15);
        $info = $M->where(array('token' => $token))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->Assign('list', $info);
        $this->Assign('fid', $id);
        $this->Display();
    }

    public function iD() {
        $id = $this->_get('id', 'intval');
        $token = $this->_get('token', 'trim');
        $fid = $this->_get('fid', 'intval');
        $m = M('message_leaver');
        if ($m->where(array('id' => $id))->delete()) {
            $this->success('操作成功', U('User/Leave/i', array('token' => $token, 'id' => $fid)));
        } else {
            $this->error('操作失败', U('User/Leave/i', array('token' => $token, 'id' => $fid)));
        }
    }

    Private function Gl($g) {
        $G = !empty($g) ? trim($g) : exit;
        $G = htmlspecialchars($G, ENT_QUOTES);
        if (!get_magic_quotes_gpc()) {
            $G = addslashes($G);
        }
        return $G;
    }

}
?>