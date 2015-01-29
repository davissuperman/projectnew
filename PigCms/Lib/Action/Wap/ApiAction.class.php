<?php

/*
  网站预约
  API
 */

class ApiAction extends Action {

    public function index() {
        if (IS_POST) {
            $name = $this->test_input($_POST['name']);
            $tel = $this->test_input($_POST['tel']);
            $address = $this->test_input($_POST['address']);
            $info = array();
            if (empty($name) || strlen($name) > 12 || strlen($name) <= 0) {
                $info['message'] = '姓名填写不正确';
                $info['stats'] = '0';
                $json = json_encode($info, TRUE);
                echo $json;
                exit;
            }
            if (empty($tel)|| strlen($tel) > 11 || strlen($tel) <= 0) {
                $info['message'] = '手机号不正确！';
                $info['stats'] = '1';
                $json = json_encode($info, TRUE);
                echo $json;
                exit;
            }
            if (empty($address) || strlen($address) <= 0 || strlen($address) > 50) {
                $info['message'] = '地址不正确！';
                $info['stats'] = '2';
                $json = json_encode($info, TRUE);
                echo $json;
                exit;
            }
            $memberinfo = M('member_user')->where(array('u_iphone' => $tel))->find();
            if ($memberinfo && $memberinfo['u_iphone']) {
                $info['message'] = '用户已存在！';
                $info['stats'] = '3';
                $json = json_encode($info, TRUE);
                echo $json;
                exit;
            } else {
                $d['u_address'] = $address;
                $d['u_iphone'] = $tel;
                $d['u_username'] = $name;
                $d['u_sex'] = 0;
                $d['u_money'] = 0;
                $d['u_member'] = 0;
                $d['u_form'] = '官网';
                $d['token'] = 'rggfsk1394161441';
                $d['starttime'] = 0;
                $x = M('member_user')->add($d);
                if ($x) {
                    $info['message'] = '注册成功！';
                    $info['stats'] = '4';
                    $json = json_encode($info, TRUE);
                    echo $json;
                    exit;
                } else {
                    $info['message'] = '服务器繁忙请稍后！';
                    $info['stats'] = '5';
                    $json = json_encode($info, TRUE);
                    echo $json;
                    exit;
                }
            }
        }
    }

    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}
