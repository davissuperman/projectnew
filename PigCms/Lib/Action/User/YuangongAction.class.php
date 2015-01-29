<?php

/*
  员工信息添加
  用到的类是YuangongAction
  方法分类 index是部门添加
  addyg 是员工信息添加
  addbm 是添加部门信息
  yglist 员工信息列表
  ygpl   员工评论信息
  ygdel  员工管理的删除
  ygupdate 员工管理修改
  ygpldel    员工评论管理删除
  ygpllist   员工评论查询
  ygewm    员工二维码

  Wap下面的YuangongAction
  需要调取用户信息
  yhlist 用户信息
  grlist 个人信息
  grtplist 针对每个用户投票
  grpllist 针对个人评论信息
 */

class YuangongAction extends UserAction {

    public function index() {
        //echo '111111';exit;
        $token = $_REQUEST['token'];
        $wecha_id = $_REQUEST['wecha_id'];
        //echo $wecha_id;
        $this->assign("token", $token);
        $this->assign("wecha_id", $wecha_id);
        $this->display();
    }

    public function addyg() {
        $wedding_staff = M('wedding_staff');
        if ($_POST) {
            $token = $_POST['token'];
            $data['token'] = $_POST['token'];
            $data['username'] = $_POST['wname']; //名字
            $data['usernum'] = $_POST['bianhao']; //编号
            $data['tel'] = $_POST['shoujihao']; //手机号
            $data['sex'] = $_POST['sex']; //性别
            $data['s_did'] = $_POST['catid']; //类别那个部门的
            $data['avatar'] = $_POST['logourl']; //头像
            $data['jianjie'] = $_POST['jianjie']; //员工介绍
            $data['cdate'] = time(); //头像

            $qry = $wedding_staff->add($data);

            if ($qry) {
                //echo '/index.php?g=Wap&m=Yuangong&a=grlist&token='.$token.'&id='.$qry;echo '<br>';
                //echo $qry;exit;
                //echo $qry; echo '<br>';
                //二维码图片
                $imgSrc = generateQRfromGoogle(C('site_url') . '/index.php?g=Wap&m=Yuangong&a=grlist&token=' . $token . '&id=' . $qry);
                $supda['ewma'] = $imgSrc;
                $wedding_staff->where("token='$token' and sid='$qry'")->save($supda);
                $this->success('操作成功', U('Yuangong/yglist', array('token' => $token)));
            } else {
                $this->error('操作失败', U('Yuangong/addyg', array('token' => $token)));
            }
        } else {
            $token = $_GET['token'];
            $wedding_staff_department = M('wedding_staff_department');
            $rzt = $wedding_staff_department->where("token='$token'")->select();
            $this->assign('rzt', $rzt);
            $this->display();
        }
    }

    //员工部门添加
    public function addbm() {

        $wedding_staff_department = M('wedding_staff_department');
        $token = $_REQUEST['token'];
        $data['token'] = $_REQUEST['token'];
        $data['departmentname'] = $_POST['wname'];
        $data['jieshao'] = $_POST['des'];
        $qry = $wedding_staff_department->add($data);
        if ($qry) {
            $this->success('操作成功', U('Yuangong/addyg', array('token' => $token)));
        } else {
            $this->error('操作失败', U('Yuangong/index', array('token' => $token)));
        }
    }

    //员工信息列表
    public function yglist() {
        $token = $_GET['token'];
        $wedding_staff_department = M('wedding_staff_department');
        $wedding_staff = M('wedding_staff');
        $wedding_staff_participate = M('wedding_staff_participate');
        //  $rzt = $wedding_staff_department->where("token='$token'")->field('did,departmentname')->select();
        $sql = "select * from tp_wedding_staff b  left join  tp_wedding_staff_department a on a.did=b.s_did    where a.token='$token'";
        
        $list = $wedding_staff->query($sql);
     
        $this->assign('list', $list);
        $this->display();
    }

    //员工评论列表
    public function ygpl() {
        import("@.ORG.Page");
        $wedding_staff_comment = M('wedding_staff_comment');
        $token = $_GET['token'];
        $id = $_GET['id'];
        //echo $id;
        $rzt = $wedding_staff_comment->where("sid='$id'")->select();
        $num = count($rzt);
        $Page = new Page($num, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $pllist1 = $wedding_staff_comment->where("sid='$id'")->order("cdate desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //var_dump($rzt);
        $this->assign('rzt', $pllist1);
        $this->assign('page', $show);
        $this->display();
    }

//问题1
    //员工管理的删除
    public function ygdel() {
        $token = $_GET['token'];
        $id = $_GET['id'];
        $wedding_staff = M('wedding_staff');
        $wedding_staff_participate = M('wedding_staff_participate');
        $wedding_staff_comment=M('wedding_staff_comment');
        $rzt = $wedding_staff->where("token='$token' and sid='$id'")->delete();
        if ($rzt) {
            $wedding_staff_participate->where("sid='$id'")->delete();
            $wedding_staff_comment->where("sid='$id'")->delete();
            $this->success('删除成功', U('Yuangong/yglist', array('token' => $token)));
        } else {
            $this->error('删除失败', U('Yuangong/yglist', array('token' => $token)));
        }
    }

    //员工管理修改
    public function ygupdate() {
        $wedding_staff = M('wedding_staff');
        $wedding_staff_department = M('wedding_staff_department');
        if ($_POST) {
            $token = $_POST['token'];
            $id = $_POST['id'];
            $data['token'] = $_POST['token'];
            $data['username'] = $_POST['wname']; //名字
            $data['usernum'] = $_POST['bianhao']; //编号
            $data['tel'] = $_POST['shoujihao']; //手机号
            $data['sex'] = $_POST['sex']; //性别
            $data['s_did'] = $_POST['catid']; //类别那个部门的
            $data['avatar'] = $_POST['logourl']; //头像
            $data['cdate'] = time(); //头像
            $qry = $wedding_staff->where("token='$token' and sid='$id'")->save($data);
            if ($qry) {
                $this->success('操作成功', U('Yuangong/yglist', array('token' => $token)));
            } else {
                $this->error('操作失败', U('Yuangong/ygupdate', array('token' => $token)));
            }
        } else {
            $token = $_GET['token'];
            $sid = $_GET['id'];


            $rzt = $wedding_staff->where("token='$token' and sid='$sid'")->find();
            $did = $rzt['s_did'];
            $qry = $wedding_staff_department->where("token='$token'")->select();
            //var_dump($qry);
            $this->assign('rzt', $rzt);
            $this->assign('qry', $qry);
            $this->display();
        }
    }

    //员工评论管理删除
    public function ygpldel() {
        $cid = $_GET['id'];
        $token = $_GET['token'];
        $wedding_staff_comment = M('wedding_staff_comment');
        $rzt = $wedding_staff_comment->where("cid='$cid'")->delete();
        if ($rzt) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //员工评论管理修改
    public function ygplupdate() {
        $wedding_staff_comment = M('wedding_staff_comment');
        if ($_POST) {
            $token = $_POST['token'];
            $cid = $_POST['cid'];
            $data['message'] = $_POST['message'];
            $qry = $wedding_staff_comment->where("cid='$cid'")->save($data);
            if ($qry) {
                $this->success('操作成功', U('Yuangong/yglist', array('token' => $token)));
            } else {
                $this->error('操作失败', U('Yuangong/yglist', array('token' => $token)));
            }
        } else {
            $cid = $_GET['id'];
            $token = $_GET['token'];
            $rzt = $wedding_staff_comment->where("cid='$cid'")->find();
            $this->assign('rzt', $rzt);
            $this->display();
        }
    }

    //员工评分查询
    public function ygpflist() {
        $wedding_staff_participate = M('wedding_staff_participate');
        $sid = $_GET['id'];
        $token = $_GET['token'];
        $rzt = $wedding_staff_participate->where("sid='$sid'")->select();
        $this->assign('rzt', $rzt);
        $this->display();
    }

    //员工评分ygpfupdate修改
    public function ygpfupdate() {
        $wedding_staff_participate = M('wedding_staff_participate');
        if ($_POST) {
            $token = $_POST['token'];
            $pid = $_POST['pid'];
            $data['vote'] = $_POST['vote'];
            $qry = $wedding_staff_participate->where("pid='$pid'")->save($data);
            if ($qry) {
                $this->success('操作成功', U('Yuangong/yglist', array('token' => $token)));
            } else {
                $this->error('操作失败', U('Yuangong/yglist', array('token' => $token)));
            }
        } else {


            $pid = $_GET['id'];
            $token = $_GET['token'];

            $rzt = $wedding_staff_participate->where("pid='$pid'")->find();
            $this->assign('rzt', $rzt);
            $this->display();
        }
    }

    //员工评分删除ygpfdel
    public function ygpfdel() {
        $pid = $_GET['id'];
        $token = $_GET['token'];
        $wedding_staff_participate = M('wedding_staff_participate');
        $rzt = $wedding_staff_participate->where("pid='$pid'")->delete();
        if ($rzt) {
            $this->success('删除成功', U('Yuangong/yglist', array('token' => $token)));
        } else {
            $this->error('删除失败', U('Yuangong/yglist', array('token' => $token)));
        }
    }

}

/**
 * 二维码操作
 * @param type $chl
 * @param type $widhtHeight
 * @param type $EC_level
 * @param type $margin
 * @return stringer
 */
function generateQRfromGoogle($chl, $widhtHeight = '150', $EC_level = 'L', $margin = '0') {
    $chl = urlencode($chl);
    $src = 'http://chart.apis.google.com/chart?chs=' . $widhtHeight . 'x' . $widhtHeight . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $chl;
    return $src;
}

?>