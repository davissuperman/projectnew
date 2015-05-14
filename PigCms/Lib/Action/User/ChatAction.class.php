<?php

/*
  人工客服
 */

class ChatAction extends UserAction {

    public $token;
    private $data;
    public $thisWxUser;

    public function _initialize() {
        parent :: _initialize();
        $this->thisWxUser = M('Diymen_set')->where($where)->find();
        $this->token = session('token');
        $this->data = D('service_user');
    }

    /**
     * 客服列表
     */
    public function index() {
        $wehre['token'] = session('token');
        $count = $this->data->where($wehre)->count();
        $page = new Page($count, 25);
        $list = $this->data->where($wehre)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 添加客服
     */
    public function add() {

        if (IS_POST) {
            $db = D("Service_user");
            if ($db->create() === false) {
                $this->error($db->getError());
            } else {
                $id = $db->add();
                if ($id == true) {
                    // M('Users')->where(array('id' => session('uid')))->setInc('serviceUserNum');
                    $this->success('操作成功');
                } else {
                    $this->error('操作失败');
                }
            }
        } else {
            $count = $this->data->where(array('token' => session('token')))->count();
            if ($count >= 2) {
                $this->error('您只能添加两个客服,如有其他需求请联系客服', U('Chat/index', array('token' => session('token'))));
            }
            $this->display();
        }
    }

    /**
     * 编辑
     */
    public function edit() {
        if (IS_POST) {
            if (empty($_POST['userPwd'])) {
                unset($_POST['userPwd']);
            }
            $_POST['id'] = $this->_get('id', 'intval');
            $this->all_save('service_user', '/index');
        } else {
            $where['id'] = $this->_get('id', 'intval');
            $where['session'] = session('session');
            $info = M('Service_user')->where($where)->find();
            $this->assign('serviceUser', $info);
            $this->display('add');
        }
    }

    /**
     * 删除
     */
    public function del() {
        $where ['id'] = $this->_get('id', 'intval');
        if ($this->data->where($where)->delete()) {
            $this->success('操作成功', U(MODULE_NAME . '/index'));
        } else {
            $this->error('操作失败', U(MODULE_NAME . '/index'));
        }
    }

    public function chat_log() {
        $data = M('service_logs');
        $wehre['token'] = session('token');
        $count = $data->where($wehre)->count();
        $page = new Page($count, 25);
        $list = $data->where($wehre)->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        foreach ($list as $key => $vo) {
            // $list[$key]['name'] = D('Service_user')->getServiceUser($vo['pid']);
        }
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('type', 'list');
        $this->display();
    }

    /* 会员列表 */

    public function member() {
        $member_user = M('member_user');
        $tel = $_REQUEST['tel'];
        $token = $_REQUEST['token'];
        $sex = $_REQUEST['sex'];
        $form = $_REQUEST['form'];
        $member = $_REQUEST['member'];
        $money = $_REQUEST['money'];
        $signscore = $_REQUEST['signscore'];
        $code_num = $_REQUEST['code_num'];
        $number = $_REQUEST['number'];
        $sql = "select * from tp_member_user where token='$token'";
        if ($tel) {
            if ($tel == 2) {
                $sql = $sql . " and u_iphone='' ";
            } else {
                $sql = $sql . " and u_iphone<>'' ";
            }
        }
        if ($sex) {
            if ($sex == 1) {
                $sql = $sql . " and u_sex=1 ";
            } else if ($sex == 2) {
                $sql = $sql . " and u_sex=2 ";
            } else {
                $sql = $sql . " and u_sex=3 ";
            }
        }
        if ($form) {
            if ($form == 1) {
                $sql = $sql . " and u_form='微信' ";
            } else if ($form == 2) {
                $sql = $sql . " and u_form='活动' ";
            } else if ($form == 3) {
                $sql = $sql . " and u_form='商城' ";
            } else if ($form == 4) {
                $sql = $sql . " and u_form='官网' ";
            }
        }
        if ($member) {
            if ($member == 1) {
                $sql = $sql . " and u_member<3 ";
            } else if ($member == 2) {
                $sql = $sql . " and u_member=3 ";
            } else if ($member == 3) {
                $sql = $sql . " and u_member=4 ";
            }
        }
        if ($money) {
            if ($money == 1) {
                $sql = $sql . "  ORDER BY  u_money ASC ";
            } else {
                $sql = $sql . "  ORDER BY  u_money desc ";
            }
        }
        $order = strpos($sql, 'ORDER');
        if ($signscore) {
            if ($order) {
                if ($signscore == 1) {
                    $sql = $sql . " , signscore ASC ";
                } else {
                    $sql = $sql . " , signscore desc ";
                }
            } else {
                if ($signscore == 1) {
                    $sql = $sql . " ORDER BY  signscore ASC ";
                } else {
                    $sql = $sql . " ORDER BY  signscore desc";
                }
            }
        }
        $ordertwo = strpos($sql, 'ORDER');
        if ($code_num) {
            if ($ordertwo) {
                if ($code_num == 1) {
                    $sql = $sql . " , code_num ASC ";
                } else {
                    $sql = $sql . " , code_num desc ";
                }
            } else {
                if ($code_num == 1) {
                    $sql = $sql . " ORDER BY  code_num ASC ";
                } else {
                    $sql = $sql . " ORDER BY  code_num desc";
                }
            }
        }
        $orderth = strpos($sql, 'ORDER');
        if ($number) {
            if ($orderth) {
                if ($number == 1) {
                    $sql = $sql . " , u_number ASC ";
                } else {
                    $sql = $sql . " , u_number desc ";
                }
            } else {
                if ($number == 1) {
                    $sql = $sql . " ORDER BY  u_number ASC ";
                } else {
                    $sql = $sql . " ORDER BY  u_number desc";
                }
            }
        }

        $list1 = $member_user->query($sql);
        $num = count($list1);
        $p = new Page($num, 25);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sqlAll = $sql;
        $sql1 = $sql . " limit {$firstRow},{$listRows}";
        $list = $member_user->query($sql1);
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('list', $list);
        $idArr = array();
        $listAll = $member_user->query($sqlAll);
        foreach($listAll as $eachItem){
            $idArr[] = $eachItem['uid'] ;
        }
        //$idStr = implode(',',$idArr);
        session('idlistall',$idArr);

        $this->display();
    }

    /**
     * 会员增减
     */
    public function memberadd() {

        $member_user = M('member_user');
        $tel = $_REQUEST['tel'];
        $token = $_REQUEST['token'];
        $sex = $_REQUEST['sex'];
        $form = $_REQUEST['form'];
        $member = $_REQUEST['member'];
        $money = $_REQUEST['money'];
        $signscore = $_REQUEST['signscore'];
        $code_num = $_REQUEST['code_num'];
        $number = $_REQUEST['number'];
        $subscribe = $_REQUEST['subscribe'];
        $start = strtotime($_REQUEST['start']);
        $end = strtotime($_REQUEST['end']);


        $sql = "select * from tp_member_user where token='$token'";
        if ($tel) {
            if ($tel == 2) {
                $sql = $sql . " and u_iphone='' ";
            } else {
                $sql = $sql . " and u_iphone<>'' ";
            }
        }
        if ($sex) {
            if ($sex == 1) {
                $sql = $sql . " and u_sex=1 ";
            } else if ($sex == 2) {
                $sql = $sql . " and u_sex=2 ";
            } else {
                $sql = $sql . " and u_sex=3 ";
            }
        }
        if ($form) {
            if ($form == 1) {
                $sql = $sql . " and u_form='微信' ";
            } else if ($form == 2) {
                $sql = $sql . " and u_form='活动' ";
            } else if ($form == 3) {
                $sql = $sql . " and u_form='商城' ";
            } else if ($form == 4) {
                $sql = $sql . " and u_form='官网' ";
            }
        }
        if ($member) {
            if ($member == 1) {
                $sql = $sql . " and u_member<3 ";
            } else if ($member == 2) {
                $sql = $sql . " and u_member=3 ";
            } else if ($member == 3) {
                $sql = $sql . " and u_member=4 ";
            }
        }
        if ($subscribe) {
            if ($subscribe == 1) {
                $sql = $sql . " and subscribe=1 ";
            } else {
                $sql = $sql . " and subscribe=0 ";
            }
        }

        if ($start) {

            if ($end) {

                $sql = $sql . " and starttime>$start and starttime< $end";
            } else {
                $sql = $sql . " and starttime>$start ";
            }
        }

        $sql = $sql . "  ORDER BY  starttime desc ";

        if ($signscore) {

            if ($signscore == 1) {
                $sql = $sql . " , signscore ASC ";
            } else {
                $sql = $sql . " , signscore desc ";
            }
        }

        if ($code_num) {
            if ($code_num == 1) {
                $sql = $sql . " , code_num ASC ";
            } else {
                $sql = $sql . " , code_num desc ";
            }
        }

        if ($number) {

            if ($number == 1) {
                $sql = $sql . " , u_number ASC ";
            } else {
                $sql = $sql . " , u_number desc ";
            }
        }


        $list1 = $member_user->query($sql);
        $num = count($list1);
        $p = new Page($num, 25);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = $sql . " limit {$firstRow},{$listRows}";
        $list = $member_user->query($sql1);
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 会员互动
     */
    public function memberint() {
        $member_user = M('member_user');
        $tel = $_REQUEST['tel'];
        $token = $_REQUEST['token'];
        $sex = $_REQUEST['sex'];
        $form = $_REQUEST['form'];
        $member = $_REQUEST['member'];
        $money = $_REQUEST['money'];
        $signscore = $_REQUEST['signscore'];
        $code_num = $_REQUEST['code_num'];
        $number = $_REQUEST['number'];
        $subscribe = $_REQUEST['subscribe'];
        $start = strtotime($_REQUEST['start']);
        $end = strtotime($_REQUEST['end']);
        $group = trim($_REQUEST['group']);



        $sql = "select * from tp_member_user where token='$token'";
        if ($tel) {
            if ($tel == 2) {
                $sql = $sql . " and u_iphone='' ";
            } else {
                $sql = $sql . " and u_iphone<>'' ";
            }
        }
        if ($sex) {
            if ($sex == 1) {
                $sql = $sql . " and u_sex=1 ";
            } else if ($sex == 2) {
                $sql = $sql . " and u_sex=2 ";
            } else {
                $sql = $sql . " and u_sex=3 ";
            }
        }
        if ($form) {
            if ($form == 1) {
                $sql = $sql . " and u_form='微信' ";
            } else if ($form == 2) {
                $sql = $sql . " and u_form='活动' ";
            } else if ($form == 3) {
                $sql = $sql . " and u_form='商城' ";
            } else if ($form == 4) {
                $sql = $sql . " and u_form='官网' ";
            }
        }
        if ($member) {
            if ($member == 1) {
                $sql = $sql . " and u_member<3 ";
            } else if ($member == 2) {
                $sql = $sql . " and u_member=3 ";
            } else if ($member == 3) {
                $sql = $sql . " and u_member=4 ";
            }
        }

        if ($start) {

            if ($end) {

                $sql = $sql . " and interaction>$start and interaction< $end";
            } else {
                $sql = $sql . " and interaction>$start ";
            }
        }

        $sql = $sql . "  ORDER BY  interaction desc ";

        if ($signscore) {

            if ($signscore == 1) {
                $sql = $sql . " , signscore ASC ";
            } else {
                $sql = $sql . " , signscore desc ";
            }
        }

        if ($code_num) {
            if ($code_num == 1) {
                $sql = $sql . " , code_num ASC ";
            } else {
                $sql = $sql . " , code_num desc ";
            }
        }

        if ($number) {

            if ($number == 1) {
                $sql = $sql . " , u_number ASC ";
            } else {
                $sql = $sql . " , u_number desc ";
            }
        }
        if (!empty($group)) {
            $wechat_group_db = M('Wechat_group');
            $arr = array();
            $arr['name'] = $group;
            $arr['intro'] = '';
            $arr['token'] = $token;
            $arr['sql'] = $sql;
            $arr['intro'] ='CRM48小时';
            $wechat_group_db->add($arr);
            $this->success('操作成功', U('Wechat_group/groups'));exit;
        }


        $list1 = $member_user->query($sql);
        $num = count($list1);
        $p = new Page($num, 25);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = $sql . " limit {$firstRow},{$listRows}";
        $list = $member_user->query($sql1);
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 会员消费
     */
    public function membercom() {

        $member_user = M('member_user');
        $tel = $_REQUEST['tel'];
        $token = $_REQUEST['token'];
        $sex = $_REQUEST['sex'];
        $form = $_REQUEST['form'];
        $member = $_REQUEST['member'];
        $money = $_REQUEST['money'];
        $signscore = $_REQUEST['signscore'];
        $code_num = $_REQUEST['code_num'];
        $number = $_REQUEST['number'];
        $subscribe = $_REQUEST['subscribe'];
        $start = strtotime($_REQUEST['start']);
        $end = strtotime($_REQUEST['end']);


        $sql = "select * from tp_member_user where token='$token'";
        if ($tel) {
            if ($tel == 2) {
                $sql = $sql . " and u_iphone='' ";
            } else {
                $sql = $sql . " and u_iphone<>'' ";
            }
        }
        if ($sex) {
            if ($sex == 1) {
                $sql = $sql . " and u_sex=1 ";
            } else if ($sex == 2) {
                $sql = $sql . " and u_sex=2 ";
            } else {
                $sql = $sql . " and u_sex=3 ";
            }
        }
        if ($form) {
            if ($form == 1) {
                $sql = $sql . " and u_form='微信' ";
            } else if ($form == 2) {
                $sql = $sql . " and u_form='活动' ";
            } else if ($form == 3) {
                $sql = $sql . " and u_form='商城' ";
            } else if ($form == 4) {
                $sql = $sql . " and u_form='官网' ";
            }
        }
        if ($member) {
            if ($member == 1) {
                $sql = $sql . " and u_member<3 ";
            } else if ($member == 2) {
                $sql = $sql . " and u_member=3 ";
            } else if ($member == 3) {
                $sql = $sql . " and u_member=4 ";
            }
        }

        if ($start) {

            if ($end) {

                $sql = $sql . " and consumption>$start and consumption< $end";
            } else {
                $sql = $sql . " and consumption>$start ";
            }
        }

        $sql = $sql . "  ORDER BY  consumption desc ";

        if ($signscore) {

            if ($signscore == 1) {
                $sql = $sql . " , signscore ASC ";
            } else {
                $sql = $sql . " , signscore desc ";
            }
        }

        if ($code_num) {
            if ($code_num == 1) {
                $sql = $sql . " , code_num ASC ";
            } else {
                $sql = $sql . " , code_num desc ";
            }
        }

        if ($number) {

            if ($number == 1) {
                $sql = $sql . " , u_number ASC ";
            } else {
                $sql = $sql . " , u_number desc ";
            }
        }


        $list1 = $member_user->query($sql);
        $num = count($list1);
        $p = new Page($num, 25);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = $sql . " limit {$firstRow},{$listRows}";
        $list = $member_user->query($sql1);
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }

    public function member_set() {
        $token = $_GET['token'];
        $member_user = M('member_user');
        if ($_POST) {

            $token = $_POST['token'];
            $uid = $_POST['uid'];
            $data['u_number'] = $_POST['u_number'];
            $data['u_name'] = $_POST['u_name'];
            $data['u_username'] = $_POST['u_username'];
            $data['u_iphone'] = $_POST['u_iphone'];
            $data['u_sex'] = $_POST['u_sex'];
            $data['u_money'] = $_POST['u_money'];
            $data['u_address'] = $_POST['u_address'];
            $data['u_form'] = $_POST['u_form'];
            $data['u_email'] = $_POST['u_email'];
            $data['u_lxaddress'] = $_POST['u_lxaddress'];
            $data['u_member'] = $_POST['u_member'];
            $ll = $member_user->where("token='$token' and uid='$uid'")->save($data);
            if ($ll) {
                $this->success('修改成功', U('Chat/member', array('token' => $token)));
            } else {
                $this->error('操作失败', U('Chat/member', array('token' => $token)));
            }
        } else {
            $uid = $_GET['id'];
            $set = $member_user->where("token='$token' and uid='$uid'")->find();
            $this->assign('set', $set);
            $this->display();
        }
    }

    //单条删除
    public function member_del() {
        $token = $_GET['token'];
        $uid = $_GET['id'];
        $member_user = M('member_user');
        $member_xfmoney = M('member_xfmoney');
        $list1 = $member_user->where("token='$token' and uid='$uid'")->find();
        $openid = $list1['openid'];
        $list = $member_user->where("token='$token' and uid='$uid'")->delete();
        if ($list) {
            $member_xfmoney->where("token='$token' and openid='$openid'")->delete();
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //会员批量删除
    public function memberdedel() {
        $member_user = M('member_user');
        $member_xfmoney = M('member_xfmoney');
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何东西');
        } else {
            $token = $_REQUEST['token'];
            $uid = $_REQUEST['item'];
            $uuid = implode(',', $uid);
            $dll = $member_user->where("token='$token' and openid !='' and uid in(" . $uuid . ")")->select();
            $dll1 = $member_user->where("token='$token' and openid ='' and uid in(" . $uuid . ")")->delete();
            foreach ($dll as $k => $v) {
                $member_xfmoney->where("token='$token' and openid=" . $v['openid'])->delete();
            }
            $dl = $member_user->where("token='$token' and uid in(" . $uuid . ")")->delete();
            if ($dl || $dll1) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    //导出数据
    public function memberdc() {
        $member_user = M('member_user');
        $str1 = substr(THINK_PATH, 0, -1);
        $from = $_REQUEST['from'];
        if(!$from){
            $from = 0;
        }
        $to = $_REQUEST['to'];
        if(!$to){
            $to = 10000;
        }
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
//        if ($_REQUEST['item'] == '') {
//            $this->error('没有选中事件');
//        } else {
            $token = $_REQUEST['token'];
//            $uid = $_REQUEST['item'];
            $uidArr =  session("idlistall");
            $middleArr = array_slice($uidArr,$from,($to-$from));
            $uuid = implode(',', $middleArr);
            $dl = $member_user->where("token='$token' and uid in(" . $uuid . ")")->select();
            Log :: write(print_r($uuid,true));
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '会员号')
                    ->setCellValue('B1', '用户名')
                    ->setCellValue('C1', '姓名')
                    ->setCellValue('D1', '电话号码')
                    ->setCellValue('E1', '性别')
                    ->setCellValue('F1', '消费金额')
                    ->setCellValue('G1', '地区')
                    ->setCellValue('H1', '来源')
                    ->setCellValue('I1', '邮箱')
                    ->setCellValue('J1', '联系地址')
                    ->setCellValue('K1', '会员级别');

            // Miscellaneous glyphs, UTF-8
            $count = count($dl);
            for ($i = 0; $i < $count ; $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $dl[$i]['uid'])
                        ->setCellValue('B' . ($i + 2), $dl[$i]['u_name'])
                        ->setCellValue('C' . ($i + 2), $dl[$i]['u_username'])
                        ->setCellValue('D' . ($i + 2), $dl[$i]['u_iphone']);
                if ($dl[$i]['u_sex'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '男');
                } elseif ($dl[$i]['u_sex'] == '2') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '女');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '未知');
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('F' . ($i + 2), $dl[$i]['u_money'])
                        ->setCellValue('G' . ($i + 2), $dl[$i]['u_address'])
                        ->setCellValue('H' . ($i + 2), $dl[$i]['u_form'])
                        ->setCellValue('I' . ($i + 2), $dl[$i]['u_email'])
                        ->setCellValue('J' . ($i + 2), $dl[$i]['u_lxaddress']);
                if ($dl[$i]['u_member'] == '1' || $dl[$i]['u_member'] == '2') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . ($i + 2), '初级会员');
                } elseif ($dl[$i]['u_member'] == '3') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . ($i + 2), '中级会员');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . ($i + 2), '高级会员');
                }
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=member.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
//        }
    }

    public function memberxiazai() {



        $file_name = "member.xls";
        $file_dir = C('site_url') . "/PUBLIC/";


        /* $file = fopen($file_dir.$file_name,"r");// 打开文件 
          // 输入文件标签
          Header("Content-type: application/octet-stream");
          Header("Accept-Ranges: bytes");
          Header("Accept-Length: ".filesize($file_dir.$file_name));
          Header("Content-Disposition: attachment; filename=".$file_name);
          // 输出文件内容
          echo fread($file,filesize($file_dir.$file_name));
          fclose($file); */

        $file = $file_dir . $file_name;
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:" . filesize($file));
        Header("Content-Disposition: attachment; filename=" . $file_name);
        readfile($file);
    }

    //excel 导入数据
    public function drsj() {
        set_time_limit(0);
        $member_user = M('member_user');
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        if ($_POST['imgname'] == '') {

            $this->error("没有任何东西");
        } else {
            $objPHPExcel = PHPExcel_IOFactory::load($_FILES["inputExcel"]["tmp_name"]);
            //内容转换为数组 
            //excel  sheet个数
            $num = $objPHPExcel->getSheetCount();
            //for($i=0;$i<$num;$i++){
            $indata = $objPHPExcel->getSheet(0)->toArray();
            //print_r($indata);exit;
            foreach ($indata as $k => $v) {

                //print_r($v);exit;
                //foreach($v as $kk => $vv){
                //print_r($v);
                if ($k != 0) {
                    $data['token'] = $_SESSION['token'];
                    $data['u_number'] = $v[0];
                    $data['u_name'] = $v[1];
                    $data['u_username'] = $v[2];
                    $data['u_iphone'] = $v[3];
                    if ($v[4] == '男') {
                        $data['u_sex'] = '1';
                    } elseif ($v[4] == '女') {
                        $data['u_sex'] = '2';
                    } else {
                        $data['u_sex'] = '0';
                    }
                    $data['u_money'] = $v[5];
                    $data['u_address'] = $v[6];
                    $data['u_form'] = $v[7];
                    $data['u_email'] = $v[8];
                    $data['u_lxaddress'] = $v[9];
                    if ($v[10] == '初级') {
                        $data['u_member'] = '0';
                    } elseif ($v[10] == '中级') {
                        $data['u_member'] = '0';
                    } else {
                        $data['u_member'] = '0';
                    }
                    $data['starttime'] = time();
                    //print_r($data);exit;
                    $aa = $member_user->add($data);
                }
                //}
            }
            $this->success('导入成功', U('Chat/member', array('token' => $_SESSION['token'])));
            // }
            //print_r($_FILES["inputExcel"]);
        }
    }

    //积分
    public function jifen() {
        $member_jifen = M('member_jifen');
        if ($_POST) {
            $token = $_REQUEST['token'];
            $ll = $member_jifen->where("token='$token'")->find();
            if ($ll) {
                if (strtotime($_REQUEST['startime']) > strtotime($_REQUEST['endtime'])) {
                    $this->error("开始时间不能大于结束时间");
                } else {
                    $data['token'] = $_REQUEST['token'];
                    $data['j_jifen'] = $_REQUEST['j_jifen'];
                    $data['j_time'] = $_REQUEST['j_time'];
                    $data['j_cjmember'] = $_REQUEST['j_cjmember'];
                    $data['j_zjmember'] = $_REQUEST['j_zjmember'];
                    $data['j_gjmember'] = $_REQUEST['j_gjmember'];
                    $data['stime'] = strtotime($_REQUEST['startime']);
                    $data['etime'] = strtotime($_REQUEST['endtime']);
                    $data['j_wx'] = $_REQUEST['j_wx'];
                    $data['j_shop'] = $_REQUEST['j_shop'];
                    $data['j_guize'] = $_REQUEST['info'];
                    $qqq = $member_jifen->where("token='$token'")->save($data);
                    if ($qqq) {
                        $this->success('更新成功', U('Chat/jifen', array('token' => $_SESSION['token'])));
                    } else {
                        $this->error('更新失败', U('Chat/jifen', array('token' => $_SESSION['token'])));
                    }
                }
            } else {
                if (strtotime($_REQUEST['startime']) > strtotime($_REQUEST['endtime'])) {
                    $this->error("开始时间不能大于结束时间");
                } else {
                    $data['token'] = $_REQUEST['token'];
                    $data['j_jifen'] = $_REQUEST['j_jifen'];
                    $data['j_time'] = $_REQUEST['j_time'];
                    $data['j_cjmember'] = $_REQUEST['j_cjmember'];
                    $data['j_zjmember'] = $_REQUEST['j_zjmember'];
                    $data['j_gjmember'] = $_REQUEST['j_gjmember'];
                    $data['stime'] = strtotime($_REQUEST['startime']);
                    $data['etime'] = strtotime($_REQUEST['endtime']);
                    $data['j_wx'] = $_REQUEST['j_wx'];
                    $data['j_shop'] = $_REQUEST['j_shop'];
                    $data['j_guize'] = $_REQUEST['info'];
                    $list = $member_jifen->add($data);
                    if ($list) {
                        $this->success('设置成功', U('Chat/jifen', array('token' => $_SESSION['token'])));
                    } else {
                        $this->error('设置失败', U('Chat/jifen', array('token' => $_SESSION['token'])));
                    }
                }
            }
        } else {
            $token = $_GET['token'];
            $list = $member_jifen->where("token='$token'")->find();
            $kssj = date("Y-m-d", $list['stime']);
            $jssj = date("Y-m-d", $list['etime']);
            $this->assign("kssj", $kssj);
            $this->assign("jssj", $jssj);
            $this->assign("list", $list);
            //print_R($list);
            $this->display();
        }
    }

    //活跃度
    public function vitality() {
        $new_product_cart = M('Member_card_sign');
        $token = $_GET['token'];
        $wecha_id = $_GET['openid'];
        $uid = $_GET['uid'];

        $userInfo = M('member_user')->where(array('uid' => $uid))->find();
        $list1 = $new_product_cart->where(array('wecha_id' => $wecha_id, 'token' => $token))->select();
        $count = count($list1);

        $page = new Page($count, 25);
        $list = $new_product_cart->where(array('wecha_id' => $wecha_id, 'token' => $token))->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        //print_r($list);
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('userinfo', $userInfo);


        $this->display();
    }

    //消费管理
    public function money() {


        $new_product_cart = M('new_product_cart');

        $token = $_GET['token'];
        $wecha_id = $_GET['openid'];
        $uid = $_GET['uid'];
        $list1 = $new_product_cart->where(array('wecha_id' => $wecha_id, 'token' => $token))->select();
        $count = count($list1);

        $page = new Page($count, 25);
        $list = $new_product_cart->where(array('wecha_id' => $wecha_id, 'token' => $token))->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        //print_r($list);
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('uid', $uid);


        $this->display();
    }

    //扫码管理
    public function code() {
        $token = $_GET['token'];
        $wecha_id = $_GET['openid'];
        $uid = $_GET['uid'];
        $npic_twocode_tongji = M('npic_twocode_tongji');
        $npic_twocode_qudao = M('npic_twocode_qudao');
        $npic_twocode_huodong = M('npic_twocode_huodong');
        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_qdleix = M('npic_twocode_qdleix');

        $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();
        $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
        $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();

        $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

        $this->assign('hdlist', $hdlist);
        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign("qdlxlist", $qdlxlist);
        $this->assign('cpxxcitylist', $cpxxcitylist);

        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();

        $list1 = $npic_twocode_tongji->where(array('wecha_id' => $wecha_id, 'token' => $token))->select();
        //print_r($list1);exit;
        $count = count($list1);
        //echo $count;exit;
        $page = new Page($count, 25);
        //exit('111');
        $list = $npic_twocode_tongji->where(array('wecha_id' => $wecha_id, 'token' => $token))->limit($page->firstRow . ',' . $page->listRows)->order('tid desc')->select();
        //print_r($list);
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('uid', $uid);
        $this->assign('qudao_list', $qudao_list);
        $this->display();
    }

    //消费管理模板下载
    public function moneyxiazai() {



        $file_name = "money.xls";
        $file_dir = C('site_url') . "/PUBLIC/";


        /* $file = $file_dir.$file_name;// 打开文件 
          // 输入文件标签
          Header("Content-type: application/octet-stream");
          Header("Accept-Ranges: bytes");
          Header("Accept-Length: ".filesize($file_dir.$file_name));
          Header("Content-Disposition: attachment; filename=".$file_name);
          // 输出文件内容
          echo fread($file,filesize($file_dir.$file_name));
          fclose($file); */
        $file = $file_dir . $file_name;
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:" . filesize($file));
        Header("Content-Disposition: attachment; filename=" . $file_name);
        readfile($file);
    }

    //导入消费记录
    public function drxfjl() {

        set_time_limit(0);
        $member_xfmoney = M('member_xfmoney');
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        if ($_POST['imgname'] == '') {
            $this->error("没有东西导入");
        } else {
            $objPHPExcel = PHPExcel_IOFactory::load($_FILES["inputExcel"]["tmp_name"]);
            //内容转换为数组 
            //excel  sheet个数
            $num = $objPHPExcel->getSheetCount();
            //for($i=0;$i<$num;$i++){
            $indata = $objPHPExcel->getSheet(0)->toArray();
            //print_r($indata);exit;
            foreach ($indata as $k => $v) {

                //print_r($v);exit;
                //foreach($v as $kk => $vv){
                //print_r($v);
                if ($k != 0) {
                    $data['token'] = $_SESSION['token'];
                    $data['x_number'] = $v[0];
                    $data['x_nicheng'] = $v[1];
                    $data['x_username'] = $v[2];
                    $data['x_iphone'] = $v[3];
                    if ($v[4] == '男') {
                        $data['x_sex'] = '1';
                    } elseif ($v[4] == '女') {
                        $data['x_sex'] = '2';
                    } else {
                        $data['x_sex'] = '0';
                    }
                    $data['x_address'] = $v[5];
                    $data['x_lxaddress'] = $v[6];
                    $data['starttime'] = $v[7];
                    $data['x_money'] = $v[8];
                    $data['x_form'] = $v[9];
                    $data['x_memberjf'] = $v[10];

                    //print_r($data);exit;
                    $aa = $member_xfmoney->add($data);
                }
                //}
            }
            $this->success('导入成功', U('Chat/money', array('token' => $_SESSION['token'])));
            // }
            //print_r($_FILES["inputExcel"]);
        }
    }

    //导出消费记录
    public function xfdc() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        if ($_REQUEST['item'] == '') {
            $this->error("没有选中任何东西");
        } else {
            $member_xfmoney = M('member_xfmoney');
            $token = $_REQUEST['token'];
            $xid = $_REQUEST['item'];
            $xxid = implode(',', $xid);
            $dl = $member_xfmoney->where("token='$token' and xid in(" . $xxid . ")")->select();

            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '会员号')
                    ->setCellValue('B1', '昵称')
                    ->setCellValue('C1', '姓名')
                    ->setCellValue('D1', '电话号码')
                    ->setCellValue('E1', '性别')
                    ->setCellValue('F1', '地区')
                    ->setCellValue('G1', '联系地址')
                    ->setCellValue('H1', '消费时间')
                    ->setCellValue('I1', '金额')
                    ->setCellValue('J1', '消费渠道')
                    ->setCellValue('K1', '当前会员积分');

            // Miscellaneous glyphs, UTF-8

            for ($i = 0; $i < count($dl); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $dl[$i]['x_number'])
                        ->setCellValue('B' . ($i + 2), $dl[$i]['x_nicheng'])
                        ->setCellValue('C' . ($i + 2), $dl[$i]['x_username'])
                        ->setCellValue('D' . ($i + 2), $dl[$i]['x_iphone']);
                if ($dl[$i]['x_sex'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '男');
                } elseif ($dl[$i]['x_sex'] == '2') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '女');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . ($i + 2), '未知');
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('F' . ($i + 2), $dl[$i]['x_address'])
                        ->setCellValue('G' . ($i + 2), $dl[$i]['x_lxaddress'])
                        ->setCellValue('H' . ($i + 2), date("Y-m-d H:i:s", $dl[$i]['starttime']))
                        ->setCellValue('I' . ($i + 2), $dl[$i]['x_money'])
                        ->setCellValue('J' . ($i + 2), $dl[$i]['x_form'])
                        ->setCellValue('K' . ($i + 2), $dl[$i]['x_memberjf']);
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=test.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    public function tongzhi() {

        $token = $_GET['token'];
        $where = "token='$token'";
        $this->groups = M("wechat_group")->where($where)->select();
        $this->imgs = M("img")->where($where)->select();
        $this->token = $token;

        if (IS_POST) {
            $row = array();
            $row['msgtype'] = $this->_post('msgtype');
            $row['mediasrc'] = $this->_post('mediasrc');
            $row['text'] = $this->_post('text');
            $row['imgids'] = $this->_post('imgids');
            $row['token'] = $this->token;
            $row['time'] = time();
            if ($row['msgtype'] != 'text' && $row['msgtype'] != 'news' && strpos($_SERVER['HTTP_HOST'], 'pigcms')) {
                $this->error('演示站禁止文件上传，所以请测试文本消息和图文消息的发送，谢谢配合');
            }
            //
            if (isset($_POST['mediasrc']) && trim($_POST['mediasrc'])) {
                $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
                $json = json_decode($this->curlGet($url_get));
                if (!$json->errmsg) {
                    $postMedia = array();
                    $postMedia['access_token'] = $json->access_token;
                    $postMedia['type'] = $row['msgtype'];
                    $postMedia['media'] = $_SERVER['DOCUMENT_ROOT'] . str_replace('http://' . $_SERVER['HTTP_HOST'], '', $row['mediasrc']);
                    $rt = $this->curlPost('http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $postMedia['access_token'] . '&type=' . $postMedia['type'], array('media' => '@' . $postMedia['media']));
                    if ($rt['rt'] == false) {
                        $this->error('操作失败,curl_error:' . $rt['errorno']);
                    } else {
                        $media_id = $rt['media_id'];
                        $row['mediaid'] = $media_id;
                    }
                } else {
                    $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
                }
            }
            $id = M('Send_message')->add($row);
            $this->success('添加成功，现在开始发送信息', U('Message/send', array('id' => $id)));
        } else {

            $this->display();
        }
    }

    public function lipinq() {
        $member_lpq = M('member_lpq');
        $token = $_GET['token'];
        //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
        //$list=$member_lpq->query($sql);
        $list1 = $member_lpq->where("token='$token'")->group('l_name')->select();
        $num = count($list1);
        $page = new Page($num, 25);
        $list = $member_lpq->where("token='$token'")->limit($page->firstRow . ',' . $page->listRows)->order('lid desc')->group('l_name')->select();
        $npic_twocode_qudao = M('npic_twocode_qudao');
        $npic_twocode_active = M('npic_twocode_active'); //活动：
        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();
        $active_list = $npic_twocode_active->where("token='$token'")->select();

        $this->assign('qudao_list', $qudao_list);
        $this->assign('active_list', $active_list);
        //print_r($list);
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('tab', 'stastic');
        $this->display();
    }

    public function addlpk() {
        $token = $_GET['token'];
        $npic_twocode_qudao = M('npic_twocode_qudao');


        $npic_twocode_huodong = M('npic_twocode_huodong');
        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_qdleix = M('npic_twocode_qdleix');
        $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


        $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

        $this->assign('hdlist', $hdlist);
        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign("qdlxlist", $qdlxlist);
        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


        $this->assign('qudao_list', $qudao_list);


        $this->display();
    }

    //添加卡券
    public function lpkadd() {
        $member_lpq = M('member_lpq');
        if ($_REQUEST) {
            if (strtotime($_POST['startime']) > strtotime($_POST['endtime'])) {
                $this->error("开始时间不能大于结束时间");
            }
            for ($i = 0; $i < $_POST['num']; $i++) {
                $data['l_name'] = $_POST['lpqname'];
                $data['token'] = $_POST['token'];
                $data['l_leix'] = $_POST['lpqlx'];
                $data['starttime'] = strtotime($_POST['startime']);
                $data['endtime'] = strtotime($_POST['endtime']);

                if ($_POST['fid']) {
                    $data['l_qudao'] = $_POST['fid'];
                }
                if ($_POST['qdlx']) {
                    $data['l_huodongleixing'] = $_POST['qdlx'];
                }
                if ($_POST['f_city']) {
                    $data['l_city'] = $_POST['f_city'];
                }
                if ($_POST['hid']) {
                    $data['l_huodong'] = $_POST['hid'];
                }

                if ($_REQUEST['l_cjmember']) {
                    $data['l_cjmember'] = $_REQUEST['l_cjmember'];
                }
                if ($_REQUEST['l_zjmember']) {
                    $data['l_zjmember'] = $_REQUEST['l_zjmember'];
                }
                if ($_REQUEST['l_gjmember']) {
                    $data['l_gjmember'] = $_REQUEST['l_gjmember'];
                }

                $data['l_num'] = $_POST['num'];
                $data['l_number'] = $_POST['khname'] . mt_rand(1000, 9999);
                $data['l_shuom'] = $_POST['info'];

                $member_lpq->add($data);
            }

            $this->success("添加成功", U('Chat/lipinq', array('token' => $_SESSION['token'])));
        }
    }

    //修改礼品卡

    public function setlpk() {
        $member_lpq = M('member_lpq');
        $member_kqj = M('member_kqj'); //卡券夹
        if ($_POST) {
            if (strtotime($_POST['startime']) > strtotime($_POST['endtime'])) {
                $this->error("开始时间不能大于结束时间");
            } else {
                $data['l_name'] = $_POST['lpqname'];
                $l_name = $_POST['lpqname'];
                $token1 = $_POST['token'];
                $data['token'] = $_POST['token'];
                $data['l_leix'] = $_POST['lpqlx'];
                $data['starttime'] = strtotime($_POST['startime']);
                $data['endtime'] = strtotime($_POST['endtime']);

                if ($_POST['fid']) {
                    $data['l_qudao'] = $_POST['fid'];
                }
                if ($_POST['qdlx']) {
                    $data['l_huodongleixing'] = $_POST['qdlx'];
                }
                if ($_POST['f_city']) {
                    $data['l_city'] = $_POST['f_city'];
                }
                if ($_POST['hid']) {
                    $data['l_huodong'] = $_POST['hid'];
                }
                if (isset($_REQUEST['l_cjmember'])) {
                    $data['l_cjmember'] = $_REQUEST['l_cjmember'];
                } else {
                    $data['l_cjmember'] = '';
                }
                if (isset($_REQUEST['l_zjmember'])) {
                    $data['l_zjmember'] = $_REQUEST['l_zjmember'];
                } else {
                    $data['l_zjmember'] = '';
                }
                if (isset($_REQUEST['l_gjmember'])) {
                    $data['l_gjmember'] = $_REQUEST['l_gjmember'];
                } else {
                    $data['l_gjmember'] = '';
                }


                $data['l_shuom'] = $_POST['info'];
                $qry = $member_lpq->where("l_name='$l_name' and token='$token1'")->save($data);
                if ($qry) {
                    $kqj_list = $member_kqj->where("token='$token1' and k_name='$l_name'")->select();
                    if ($kqj_list) {
                        $data1['k_qudao'] = $_POST['fid'];
                        $data1['k_leix'] = $_POST['lpqlx'];
                        $data1['starttime'] = strtotime($_POST['startime']);
                        $data1['endtime'] = strtotime($_POST['endtime']);
                        $member_kqj->where("k_name='$l_name' and token='$token1'")->save($data1);
                    }
                    $this->success("修改成功", U('Chat/lipinq', array('token' => $_SESSION['token'])));
                } else {
                    $this->error("修改失败", U('Chat/lipinq', array('token' => $_SESSION['token'])));
                }
            }
        } else {
            $lid = $_GET['id'];
            $l_name = $_GET['l_name'];
            $token = $_GET['token'];

            $list = $member_lpq->where("token='$token' and lid='$lid'")->find();
            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


            $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

            $this->assign('hdlist', $hdlist);
            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign("qdlxlist", $qdlxlist);
            $npic_twocode_qudao = M('npic_twocode_qudao');


            $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


            $this->assign('qudao_list', $qudao_list);


            $this->assign("list", $list);
            $this->display();
        }
    }

    //发送卡券
    public function faslpk() {
        $member_lpq = M('member_lpq');
        $member_user = M('member_user');
        $member_kqj = M('member_kqj'); //卡券夹
        $l_name = $_REQUEST['l_name'];
        $token = $_REQUEST['token'];
        $lid = $_REQUEST['lid'];

        $list = $member_lpq->where("token='$token' and l_name='$l_name' and lid='$lid'")->find();
        //$num=count($arr);

        if ($list['l_cjmember']) {
            //echo 1;
            $arr = $member_lpq->where("token='$token' and l_name='$l_name' and type=0")->select();
            $cj = $member_user->where("token='$token' and (u_member=1 or u_member=2)")->select();
            //print_r($cj);
            foreach ($arr as $k => $v) {
                foreach ($cj as $k1 => $v1) {
                    if ($k == $k1) {
                        $newlid = $v['lid'];
                        $openid = $v1['openid'];
                        $data1['type'] = 1;

                        $datakq['token'] = $token;
                        $datakq['lid'] = $newlid;
                        $datakq['openid'] = $openid;
                        $datakq['k_name'] = $v['l_name'];
                        $datakq['k_leix'] = $v['l_leix'];
                        $datakq['k_qudao'] = $v['l_qudao'];
                        $datakq['k_shuom'] = $v['l_shuom'];
                        $datakq['starttime'] = $v['starttime'];
                        $datakq['endtime'] = $v['endtime'];
                        $datakq['cjtime'] = time();

                        $member_kqj->add($datakq);
                        $member_lpq->where("token='$token' and lid='$newlid'")->save($data1);
                    }
                }
            }
        }
        if ($list['l_zjmember']) {

            $arr1 = $member_lpq->where("token='$token' and l_name='$l_name' and type=0")->select();
            $cj1 = $member_user->where("token='$token' and u_member=3")->select();
            //print_r($cj);
            foreach ($arr1 as $kk => $vv) {
                foreach ($cj1 as $kk1 => $vv1) {

                    if ($kk == $kk1) {
                        $newlid1 = $vv['lid'];
                        $openid1 = $vv1['openid'];
                        $data2['type'] = 1;

                        $datakq1['token'] = $token;
                        $datakq1['lid'] = $newlid1;
                        $datakq1['openid'] = $openid1;
                        $datakq1['k_name'] = $vv['l_name'];
                        $datakq1['k_leix'] = $vv['l_leix'];
                        $datakq1['k_qudao'] = $vv['l_qudao'];
                        $datakq1['k_shuom'] = $vv['l_shuom'];
                        $datakq1['starttime'] = $vv['starttime'];
                        $datakq1['endtime'] = $vv['endtime'];
                        $datakq1['cjtime'] = time();
                        $member_kqj->add($datakq1);
                        $member_lpq->where("token='$token' and lid='$newlid1'")->save($data2);
                    }
                }
            }
        }
        if ($list['l_gjmember']) {
            $arr2 = $member_lpq->where("token='$token' and l_name='$l_name' and type=0")->select();
            $cj2 = $member_user->where("token='$token' and u_member=4")->select();
            //print_r($cj);
            foreach ($arr2 as $kkk => $vvv) {
                foreach ($cj2 as $kkk1 => $vvv1) {

                    if ($kkk == $kkk1) {
                        $newlid2 = $vvv['lid'];
                        $openid2 = $vvv1['openid'];
                        $data2['type'] = 1;

                        $datakq2['token'] = $token;
                        $datakq2['lid'] = $newlid2;
                        $datakq2['openid'] = $openid2;
                        $datakq2['k_name'] = $vvv['l_name'];
                        $datakq2['k_leix'] = $vvv['l_leix'];
                        $datakq2['k_qudao'] = $vvv['l_qudao'];
                        $datakq2['k_shuom'] = $vvv['l_shuom'];
                        $datakq2['starttime'] = $vvv['starttime'];
                        $datakq2['endtime'] = $vvv['endtime'];
                        $datakq2['cjtime'] = time();
                        $member_kqj->add($datakq2);
                        $member_lpq->where("token='$token' and lid='$newlid2'")->save($data2);
                    }
                }
            }
        }
        $this->success("发送成功", U('Chat/lipinq', array('token' => $token)));
    }

    //添加设置随机卡号
    /* public function lpkadd(){
      $wedding_time_kahao=M('wedding_time_kahao');
      if($_POST){
      //print_r($_POST);exit;
      $token=$_POST['token'];
      $starttime=strtotime($_POST['starttime']);
      $endtime=strtotime($_POST['endtime']);
      $title=$_POST['title'];
      $stat=$_POST['stat'];
      $end=$_POST['end'];
      if($starttime>$endtime){
      $this->error("开始时间不能大于结束时间");
      }
      for($i=0;$i<$end;$i++){
      $data['token']=$token;
      $data['starttime']=$starttime;
      $data['endtime']=$endtime;
      $data['number']=$title.mt_rand(1000,9999);
      $wedding_time_kahao->add($data);

      }
      $this->success("添加成功",U('Weddingfx/index',array('token'=> $token)));



      }else{
      $this->display();
      }
      } */
    public function xfk() {
        $member_xfj = M('member_xfj');
        $token = $_GET['token'];
        //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
        //$list=$member_lpq->query($sql);
        $list1 = $member_xfj->where("token='$token'")->group('x_name')->select();
        $num = count($list1);
        $page = new Page($num, 25);
        $list = $member_xfj->where("token='$token'")->limit($page->firstRow . ',' . $page->listRows)->order('xid desc')->group('x_name')->select();

        //print_r($list);
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('tab', 'records');
        $this->display();
    }

    //礼品券删除
    public function lpq_del() {
        $member_lpq = M('member_lpq');
        $member_kqj = M('member_kqj');
        $l_name = $_REQUEST['l_name'];
        $token = $_REQUEST['token'];
        $rzt = $member_lpq->where("token='$token' and l_name='$l_name'")->delete();
        if ($rzt) {
            $member_kqj->where("token='$token' and k_name='$l_name'")->delete();
            $this->success();
        } else {
            $this->error();
        }
    }

    //礼品卡统计
    public function lpktongji() {
        $member_lpq = M('member_lpq');
        if ($_POST) {
            $sous = $_REQUEST['sous'];
            $token = $_REQUEST['token'];

            //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
            //$list=$member_lpq->query($sql);
            $list1 = $member_lpq->where("token='$token' and l_name like '%$sous%'")->group('l_name')->select();
            $num = count($list1);
            $page = new Page($num, 25);
            $list = $member_lpq->where("token='$token' and l_name like '%$sous%'")->limit($page->firstRow . ',' . $page->listRows)->order('lid desc')->group('l_name')->select();
            $dqtime = time();
            // print_r($list);
            $this->assign('page', $page->show());
            $this->assign('list', $list);
        } else {

            $token = $_REQUEST['token'];
            //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
            //$list=$member_lpq->query($sql);
            $list1 = $member_lpq->where("token='$token'")->group('l_name')->select();
            $num = count($list1);

            $page = new Page($num, 25);
            $list = $member_lpq->where("token='$token'")->limit($page->firstRow . ',' . $page->listRows)->order('lid desc')->group('l_name')->select();
            $dqtime = time();

            $this->assign('page', $page->show());
            $this->assign('list', $list);

            $this->assign('dqtime', $dqtime);
        }
        $this->display();
    }

    //统计导出
    public function tongjidc() {

        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        if ($_REQUEST['item'] == '') {
            $this->error("没有选中任何东西");
        } else {

            $member_lpq = M('member_lpq');
            $token = $_REQUEST['token'];
            $l_name = $_REQUEST['item'];
            // print_r( $l_name);
            //$ll_name = "'".implode(',', $l_name)."'";
            $ll_name = "'" . implode("','", $l_name) . "'";
            //echo $ll_name;exit;
            $sql = "SELECT * FROM tp_member_lpq WHERE token='$token' and type=1 and l_name in($ll_name)";
            $dl = $member_lpq->query($sql);
            //print_r($dl);exit;


            $dqtime = time();

            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '卡券名称')
                    ->setCellValue('B1', '卡券类型')
                    ->setCellValue('C1', '活动时间')
                    ->setCellValue('D1', '到达率')
                    ->setCellValue('E1', '打开率')
                    ->setCellValue('F1', '使用率')
                    ->setCellValue('G1', '状态');


            // Miscellaneous glyphs, UTF-8

            for ($i = 0; $i < count($dl); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $dl[$i]['l_name']);
                if ($dl[$i]['l_leix'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '试用品券');
                } elseif ($dl[$i]['l_leix'] == '2') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '礼品券');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '促销券');
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('C' . ($i + 2), date("Y-m-d:H:i:s", $dl[$i]['endtime']));

                if ($dl[$i]['type'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . ($i + 2), '到达');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . ($i + 2), "未到达");
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('E' . ($i + 2), '无')
                        ->setCellValue('F' . ($i + 2), '无');
                if ($dqtime > $dl[$i]['endtime']) {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('G' . ($i + 2), '进行中');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('G' . ($i + 2), '已结束');
                }
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=test.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    //礼品卡发送详细信息
    public function lpklist() {
        $member_lpq = M('member_lpq');
        $member_kqj = M('member_kqj');
        $member_user = M('member_user');
        $token = $_GET['token'];
        $l_name = $_GET['l_name'];
        $rzt = $member_user->table('tp_member_user a')->join('tp_member_kqj b on a.openid=b.openid')->join('tp_member_lpq
 c on b.lid=c.lid')->where("c.token='$token' and c.l_name='$l_name' and c.type=1")->select();
        $num = count($rzt);

        $page = new Page($num, 20);

        $show = $page->show(); // 分页显示输出
        $list = $member_user->table('tp_member_user a')->join('tp_member_kqj b on a.openid=b.openid')->join('tp_member_lpq
 c on b.lid=c.lid')->where("c.token='$token' and c.l_name='$l_name' and c.type=1")->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        //echo $l_name.$token;
        $this->display();
    }

    //
    public function addxfk() {

        $this->display();
    }

    //消费卡添加
    public function xfkadd() {

        $member_xfj = M('member_xfj');
        if ($_REQUEST) {
            if (strtotime($_POST['startime']) > strtotime($_POST['endtime'])) {
                $this->error("开始时间不能大于结束时间");
            }
            for ($i = 0; $i < $_POST['num']; $i++) {
                $data['x_name'] = $_POST['xfkname'];
                $data['token'] = $_POST['token'];
                $data['x_leix'] = $_POST['xfklx'];
                $data['x_me'] = $_POST['xfkprice'];
                $data['starttime'] = strtotime($_POST['startime']);
                $data['endtime'] = strtotime($_POST['endtime']);
                $data['x_num'] = $_POST['num'];
                $data['x_number'] = $_POST['khname'] . mt_rand(1000, 9999);
                if ($_REQUEST['x_cjmember']) {
                    $data['x_cjmember'] = $_REQUEST['x_cjmember'];
                }
                if ($_REQUEST['x_zjmember']) {
                    $data['x_zjmember'] = $_REQUEST['x_zjmember'];
                }
                if ($_REQUEST['x_gjmember']) {
                    $data['x_gjmember'] = $_REQUEST['x_gjmember'];
                }
                $data['x_shuom'] = $_POST['info'];
                $data['x_dhkq'] = $_POST['xfkduihuan'];
                $member_xfj->add($data);
            }
            $this->success("添加成功", U('Chat/xfk', array('token' => $_SESSION['token'])));
        }
    }

    //消费卡修改
    public function setxfk() {
        $x_name = $_REQUEST['x_name'];
        $token = $_REQUEST['token'];
        $xid = $_REQUEST['xid'];
        $member_xfj = M('member_xfj');
        $member_kqj = M('member_kqj');
        if ($_POST) {

            if (strtotime($_POST['startime']) > strtotime($_POST['endtime'])) {
                $this->error("开始时间不能大于结束时间");
            } else {

                $data['x_name'] = $_POST['xfkname'];
                $x_name = $_POST['xfkname'];
                $token1 = $_POST['token'];
                $data['token'] = $_POST['token'];
                $data['x_leix'] = $_POST['xfklx'];
                $data['x_me'] = $_POST['xfkprice'];
                $data['starttime'] = strtotime($_POST['startime']);
                $data['endtime'] = strtotime($_POST['endtime']);

                if (isset($_REQUEST['x_cjmember'])) {
                    $data['x_cjmember'] = $_REQUEST['x_cjmember'];
                } else {

                    $data['x_cjmember'] = '';
                }
                if (isset($_REQUEST['x_zjmember'])) {
                    $data['x_zjmember'] = $_REQUEST['x_zjmember'];
                } else {
                    $data['x_zjmember'] = '';
                }
                if (isset($_REQUEST['x_gjmember'])) {
                    $data['x_gjmember'] = $_REQUEST['x_gjmember'];
                } else {

                    $data['x_gjmember'] = '';
                }
                $data['x_shuom'] = $_POST['info'];
                $data['x_dhkq'] = $_POST['xfkduihuan'];
                //print_r($data);exit;
                $qry = $member_xfj->where("token='$token1' and x_name='$x_name'")->save($data);
                if ($qry) {
                    $kqj_list = $member_kqj->where("token='$token1' and k_name='$x_name'")->select();
                    if ($kqj_list) {
                        $data1['k_me'] = $_POST['xfkprice'];
                        $data1['k_leix'] = $_POST['xfklx'];
                        $data1['starttime'] = strtotime($_POST['startime']);
                        $data1['endtime'] = strtotime($_POST['endtime']);
                        $member_kqj->where("k_name='$x_name' and token='$token1'")->save($data1);
                    }
                    $this->success("修改成功", U('Chat/xfk', array('token' => $_SESSION['token'])));
                } else {
                    $this->error("修改失败", U('Chat/xfk', array('token' => $_SESSION['token'])));
                }
            }
        } else {
            $list = $member_xfj->where("token='$token' and xid='$xid'")->find();
            $this->assign('list', $list);
            $this->display();
        }
    }

    //消费卡删除
    public function xfj_del() {
        $member_xfj = M('member_xfj');
        $member_kqj = M('member_kqj');
        $x_name = $_REQUEST['x_name'];
        $token = $_REQUEST['token'];
        $rzt = $member_xfj->where("token='$token' and x_name='$x_name'")->delete();
        if ($rzt) {
            $member_kqj->where("token='$token' and k_name='$x_name'")->delete();
            $this->success();
        } else {
            $this->error();
        }
    }

    //消费卡数据统计
    public function xfktongji() {
        $member_xfj = M('member_xfj');
        if ($_POST) {
            $sous = $_REQUEST['sous'];
            $token = $_REQUEST['token'];

            //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
            //$list=$member_lpq->query($sql);
            $list1 = $member_xfj->where("token='$token' and x_name like '%$sous%'")->group('x_name')->select();
            $num = count($list1);
            $page = new Page($num, 25);
            $list = $member_xfj->where("token='$token' and x_name like '%$sous%'")->limit($page->firstRow . ',' . $page->listRows)->order('xid desc')->group('x_name')->select();
            $dqtime = time();
            // print_r($list);
            $this->assign('page', $page->show());
            $this->assign('list', $list);
        } else {

            $token = $_REQUEST['token'];
            //$sql="SELECT * , count( lid ) AS num FROM tp_member_lpq WHERE token = '$token' GROUP BY l_name";
            //$list=$member_lpq->query($sql);
            $list1 = $member_xfj->where("token='$token'")->group('x_name')->select();
            $num = count($list1);
            $page = new Page($num, 25);
            $list = $member_xfj->where("token='$token'")->limit($page->firstRow . ',' . $page->listRows)->order('xid desc')->group('x_name')->select();
            $dqtime = time();
            // print_r($list);
            $this->assign('page', $page->show());
            $this->assign('list', $list);
            $this->assign('dqtime', $dqtime);
        }
        $this->display();
    }

    //消费卡导出
    public function xfkdc() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        if ($_REQUEST['item'] == '') {
            $this->error("没有选中任何东西");
        } else {
            $member_xfj = M('member_xfj');
            $token = $_REQUEST['token'];
            $x_name = $_REQUEST['item'];
            //$xx_name = implode(',', $x_name);
            //$dl = $member_xfj->where("token='$token' and x_name in(" . $xx_name . ") and type=1")->select();
            $xx_name = "'" . implode("','", $x_name) . "'";
            //echo $ll_name;exit;
            $sql = "SELECT * FROM tp_member_xfj WHERE token='$token' and type=1 and x_name in($xx_name)";
            $dl = $member_xfj->query($sql);
            $dqtime = time();
            //print_r($dl);exit;
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '卡券名称')
                    ->setCellValue('B1', '卡券类型')
                    ->setCellValue('C1', '活动时间')
                    ->setCellValue('D1', '到达率')
                    ->setCellValue('E1', '打开率')
                    ->setCellValue('F1', '使用率')
                    ->setCellValue('G1', '状态');


            // Miscellaneous glyphs, UTF-8

            for ($i = 0; $i < count($dl); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $dl[$i]['x_name']);
                if ($dl[$i]['x_leix'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '试用品券');
                } elseif ($dl[$i]['x_leix'] == '2') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '礼品券');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . ($i + 2), '促销券');
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('C' . ($i + 2), date("Y-m-d:H:i:s", $dl[$i]['endtime']));

                if ($dl[$i]['type'] == '1') {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . ($i + 2), '到达');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . ($i + 2), "未到达");
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('E' . ($i + 2), '无')
                        ->setCellValue('F' . ($i + 2), '无');
                if ($dqtime > $dl[$i]['endtime']) {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('G' . ($i + 2), '进行中');
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('G' . ($i + 2), '已结束');
                }
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=test.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    //消费卡详细情况
    public function xfklist() {
        $member_xfj = M('member_xfj');
        $member_kqj = M('member_kqj');
        $member_user = M('member_user');
        $token = $_GET['token'];
        $x_name = $_GET['x_name'];
        $rzt = $member_user->table('tp_member_user a')->join('tp_member_kqj b on a.openid=b.openid')->join('tp_member_xfj
 c on b.xid=c.xid')->where("c.token='$token' and c.x_name='$x_name' and c.type=1")->select();
        // print_r($rzt);
        $num = count($rzt);

        $page = new Page($num, 20);

        $show = $page->show(); // 分页显示输出
        $list = $member_user->table('tp_member_user a')->join('tp_member_kqj b on a.openid=b.openid')->join('tp_member_xfj
 c on b.xid=c.xid')->where("c.token='$token' and c.x_name='$x_name' and c.type=1")->limit($page->firstRow . ',' . $page->listRows)->select();
        //print_r($list);
        $this->assign('page', $show);
        $this->assign('list', $list);
        //echo $l_name.$token;

        $this->display();
    }

    //消费卡发送
    public function fasxfk() {
        //print_r($_REQUEST);exit;
        $member_xfj = M('member_xfj');
        $member_user = M('member_user');
        $member_kqj = M('member_kqj'); //卡券夹
        $x_name = $_REQUEST['x_name'];
        $token = $_REQUEST['token'];
        $xid = $_REQUEST['xid'];

        $list = $member_xfj->where("token='$token' and x_name='$x_name' and xid='$xid'")->find();
        //$num=count($arr);
        //print_r($list);exit;



        if ($list['x_cjmember']) {
            //echo 1;
            $arr = $member_xfj->where("token='$token' and x_name='$x_name' and type=0")->select();
            $cj = $member_user->where("token='$token' and (u_member=1 or u_member=2)")->select();
            //print_r($cj);
            foreach ($arr as $k => $v) {
                foreach ($cj as $k1 => $v1) {

                    if ($k == $k1) {
                        $newxid = $v['xid'];
                        $openid = $v1['openid'];
                        $data1['type'] = 1;

                        $datakq['token'] = $token;
                        $datakq['xid'] = $newxid;
                        $datakq['openid'] = $openid;
                        $datakq['k_name'] = $v['x_name'];
                        $datakq['k_leix'] = $v['x_leix'];
                        $datakq['k_me'] = $v['x_me'];

                        $datakq['k_shuom'] = $v['x_shuom'];
                        $datakq['starttime'] = $v['starttime'];
                        $datakq['endtime'] = $v['endtime'];
                        $datakq['cjtime'] = time();
                        $member_kqj->add($datakq);
                        $member_xfj->where("token='$token' and xid='$newxid'")->save($data1);
                    }
                }
            }
        }

        if ($list['x_zjmember']) {

            $arr1 = $member_xfj->where("token='$token' and x_name='$x_name' and type=0")->select();
            $cj1 = $member_user->where("token='$token' and u_member=3")->select();
            //print_r($cj);
            foreach ($arr1 as $kk => $vv) {
                foreach ($cj1 as $kk1 => $vv1) {

                    if ($kk == $kk1) {
                        $newxid1 = $vv['xid'];
                        $openid1 = $vv1['openid'];
                        $data2['type'] = 1;

                        $datakq1['token'] = $token;
                        $datakq1['xid'] = $newxid1;
                        $datakq1['openid'] = $openid1;
                        $datakq1['k_name'] = $vv['x_name'];
                        $datakq1['k_leix'] = $vv['x_leix'];
                        $datakq1['k_me'] = $vv['x_me'];
                        $datakq1['k_shuom'] = $vv['x_shuom'];
                        $datakq1['starttime'] = $vv['starttime'];
                        $datakq1['endtime'] = $vv['endtime'];
                        $datakq1['cjtime'] = time();
                        $member_kqj->add($datakq1);
                        $member_xfj->where("token='$token' and xid='$newxid1'")->save($data2);
                    }
                }
            }
        }

        if ($list['x_gjmember']) {
            $arr2 = $member_xfj->where("token='$token' and x_name='$x_name' and type=0")->select();
            $cj2 = $member_user->where("token='$token' and u_member=4")->select();
            //print_r($cj2);exit;
            foreach ($arr2 as $kkk => $vvv) {
                foreach ($cj2 as $kkk1 => $vvv1) {

                    if ($kkk == $kkk1) {

                        $newxid2 = $vvv['xid'];
                        $openid2 = $vvv1['openid'];
                        $data2['type'] = 1;

                        $datakq2['token'] = $token;
                        $datakq2['xid'] = $newxid2;
                        $datakq2['openid'] = $openid2;
                        $datakq2['k_name'] = $vvv['x_name'];
                        $datakq2['k_leix'] = $vvv['x_leix'];
                        $datakq2['k_me'] = $vvv['x_me'];
                        $datakq2['k_shuom'] = $vvv['x_shuom'];
                        $datakq2['starttime'] = $vvv['starttime'];
                        $datakq2['endtime'] = $vvv['endtime'];
                        $datakq2['cjtime'] = time();

                        $member_kqj->add($datakq2);
                        //exit('2222');
                        $member_xfj->where("token='$token' and xid='$newxid2'")->save($data2);
                    }
                }
            }
        }

        $this->success("发送成功", U('Chat/xfk', array('token' => $token)));
    }

    function _getAccessToken() {
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->thisWxUser['appid'] . '&secret=' . $this->thisWxUser['appsecret'];
        $json = json_decode($this->curlGet($url_get));
        if (!$json->errmsg) {
            
        } else {
            $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
        }
        return $json->access_token;
    }

    function curlGet($url, $method = 'get', $data = '') {
        $ch = curl_init();
        $header[] = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

}
