<?php

/**
 * 文本回复
 * */
class ImgAction extends UserAction {

    public function index() {
        $db = D('Img');
        $where ['uid'] = session('uid');
        $where ['token'] = session('token');
        $count = $db->where($where)->count();
        $page = new Page($count, 15);
        $info = $db->where($where)->order('createtime DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->assign('info', $info);
        $this->display();
    }

    public function add() {
        $class = M('Classify')->where(array('token' => session('token')))->select();
        if ($class == false) {
            $this->error('请先添加微网站分类', U('Classify/index', array('token' => session('token'))));
        }
        $db = M('Classify');
        $where ['token'] = session('token');
        $info = $db->where($where)->select();
        $this->assign('info', $info);
        $this->display();
    }

    public function edit() {
        $db = M('Classify');
        $where ['token'] = session('token');
        $info = $db->where($where)->select();
        $where ['id'] = $this->_get('id', 'intval');
        $where ['uid'] = session('uid');
        $res = D('Img')->where($where)->find();
        $this->assign('info', $res);
        $this->assign('res', $info);
        $this->display();
    }

    public function del() {
        $where ['id'] = $this->_get('id', 'intval');
        $where ['uid'] = session('uid');
        if (D(MODULE_NAME)->where($where)->delete()) {
            M('Keyword')->where(array('pid' => $this->_get('id', 'intval'), 'token' => session('token'), 'module' => 'Img'))->delete();
            $this->success('操作成功', U(MODULE_NAME . '/index'));
        } else {
            $this->error('操作失败', U(MODULE_NAME . '/index'));
        }
    }

    public function insert() {
        
            $pat = "/<(\/?)(script|i?frame|style|html|body|title|font|strong|span|div|marquee|link|meta|\?|\%)([^>]*?)>/isU";
            $_POST ['info'] = preg_replace($pat, "", $_POST ['info']);
            //$_POST['info']=strip_tags($this->_post('info'),'<a> <p> <br>');  
            //dump($_POST['info']);
            $this->all_insert();
            M('Users')->where(array('id' => $_SESSION ['uid']))->setInc('imgnum');
      
    }

    public function upsave() {
        $this->all_save();
    }

    public function sort() {

        $sorts = $_REQUEST['sortname'];
        foreach ($sorts as $k => $v) {

            if (is_numeric($v) && $v > 0) {

                M('img')->where(array('id' => $k))->save(array('sort' => $v));
                
            }
        }
        $this->success('操作成功', U('Img/index'));
    }

}

?>