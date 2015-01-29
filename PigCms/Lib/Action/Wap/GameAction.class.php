<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GameAction extends BaseAction {

    private $gid;
    private $gameinfo;

    public function _initialize() {
        parent::_initialize();
        $this->gid = $_REQUEST['id'];

        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }

        $data = M('game')->where(array('gid' => $this->gid))->find();
        if ($data) {
            $this->gameinfo = $data;
        } else {
            echo "<center>活动不存在</center>";
            exit;
        }
    }

    public function index() {

        if ($_GET["tel"] != "") {

            $tel = $_GET["tel"];
            //统计添加浏览数和浏览记录
            $gamedata = M('game_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
            if ($gamedata) {
                M("game_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('jons', 1);
            } else {
                $d['gid'] = $this->gid;
                $d['tel'] = $tel;
                $d['jons'] = 1;
                $d['createdate'] = date("Y-m-d");
                M("game_date")->add($d);
            }
            //end
            M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('joins', 1);
        }

        $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $code = trim($_GET["code"]);
        $state = trim($_GET['state']);
        if ($code && $state == 'sentian') {
            $userinfo = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
            $this->assign('gid', $this->gid);
            $this->assign('openid', $userinfo['openid']);
            $this->assign('gameinfo', $this->gameinfo);
            $this->display();
            exit;
        } else {
            $url = urlencode("http://wx.drjou.cc/index.php?g=Wap&m=Game&a=index&id=$this->gid");
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
            exit;
        }
    }

    public function input() {
        $this->assign('gid', $this->gid);
        $openid = trim($_GET["openid"]);
        $number = trim($_GET["number"]);
        $this->assign('openid', $openid);
        $this->assign('number', $number);
        $this->display();
    }

    /**
     * 提交个人信息
     */
    public function add() {
        if (IS_POST) {
            $data['tel'] = $_POST['tel'];
            $data['number'] = $_POST['usercoreNum'];
            $data['gid'] = $_POST['id'];
            $data['createtime'] = time();
            $data['openid'] = $_POST['openid'];
            $data['name'] = $_POST['name'];
            $qry = M("game_info")->add($data);
            if ($qry) {
                //同步到crm
                $memberinfo = M('member_user')->where(array('openid' => $data['openid']))->find();
                if (!$memberinfo) {
                    $u['u_money'] = 0;
                    $u['u_member'] = 1;
                    $u['token'] = 'rggfsk1394161441';
                    $u['openid'] = $data['openid'];
                    $u['u_form'] = '游戏';
                    $u['starttime'] = time();
                    M('member_user')->add($u);
                }
                //end 
                
                $this->assign('gid', $this->gid);
                $this->assign('number', $data['number']);
                $this->assign('name', $data['name']);
                $this->assign('tel', $data['tel']);
                $this->assign('gameinfo', $this->gameinfo);
                $this->display();
                exit;
            } else {
                exit("操作失败");
            }
            exit("提交完成");
        }
    }

    /**
     * 查询后得到add
     * @param type $param
     */
    public function searchadd() {
        $this->assign('gid', $this->gid);
        $this->assign('tel', $_GET['time']);
        $data = M('game_info')->where(array('gid' => $this->gid, 'tel' => trim($_GET['time'])))->find();
        $this->assign('number', $data['number'] + $data['share'] * 10);
        $this->assign('name', $data['name']);
        $this->assign('gameinfo', $this->gameinfo);
        $this->display('add');
    }

    /**
     * 点击帮他补水
     */
    public function shareget() {
        $data = M('game_info_vote')->where(array('gid' => $this->gid, 'tel' => $_GET['time'], 'openid' => $_GET['openid']))->find();

        if (!$data) {//投票过
            $data['tel'] = $_GET['time'];
            $data['openid'] = $_GET['openid'];
            $data['gid'] = $_GET['id'];
            $data['createtime'] = time();
            $qry = M("game_info_vote")->add($data);

            $gamedata = M('game_date')->where(array('gid' => $this->gid, 'tel' => $data['tel'], 'createdate' => date("Y-m-d")))->find();
            if ($gamedata) {
                M("game_date")->where(array('gid' => $this->gid, 'tel' => $data['tel'], 'createdate' => date("Y-m-d")))->setInc('votes', 1);
            } else {
                $d['gid'] = $this->gid;
                $d['tel'] = $data['tel'];
                $d['votes'] = 1;
                $d['createdate'] = date("Y-m-d");
                M("game_date")->add($d);
            }
            M("game_info")->where(array('gid' => $this->gid, 'tel' => $data['tel']))->setInc('vote', 1);
            if ($qry) {
                $this->assign('gid', $this->gid);
                $this->assign('number', $data['number']);
                $this->assign('tel', $data['tel']);
                $this->assign('gameinfo', $this->gameinfo);
                $this->display();
                exit;
            }
        }
    }

    /**
     * 朋友圈页面
     */
    public function share() {
        $tel = $_GET['time'];
        $this->assign('gid', $this->gid);
        $this->assign('time', $tel);
        $data = M('game_info')->where(array('gid' => $this->gid, 'tel' => $tel))->find();

        $number = $data['share'] * 10 + $data['number'];
        $this->assign('number', $number); //积分
        $this->assign('name', $data['name']); //积分
        if (time() > $this->gameinfo['end']) {//活动是否结束
            $this->assign('end', '1'); //活动结束不能在参加活动了
        }
        $my = time() - $data['sharetime'];
        if ($my < 259200) {//72小时之内
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            //1.得到浏览者的openid
            if ($code && $state == 'sentian') {
                //2.查询openid 是否已经投票过
                $userinfo = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                $d = M('game_info_vote')->where(array('gid' => $this->gid, 'tel' => $tel, 'openid' => $userinfo['openid']))->find();
                if ($d) {//投票过
                    $this->assign('oldtime', '1');
                }
                $this->assign('openid', $userinfo['openid']);

                //统计添加浏览数和浏览记录
                $gamedata = M('game_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
                if ($gamedata) {
                    M("game_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('views', 1);
                } else {
                    $d['gid'] = $this->gid;
                    $d['tel'] = $tel;
                    $d['views'] = 1;
                    $d['createdate'] = date("Y-m-d");
                    M("game_date")->add($d);
                }
                M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('views', 1);
                //end

                $this->display();
            } else {
                $url = urlencode("http://wx.drjou.cc/index.php?g=Wap&m=Game&a=share&id=$this->gid&time=$tel");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        } else {
            //统计添加浏览数和浏览记录
            $gamedata = M('game_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
            if ($gamedata) {
                M("game_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('views', 1);
            } else {
                $d['gid'] = $this->gid;
                $d['tel'] = $tel;
                $d['views'] = 1;
                $d['createdate'] = date("Y-m-d");
                M("game_date")->add($d);
            }
            M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('views', 1);
            //end
            $this->display();
        }
    }

    public function x() {
        $this->display('index');
    }

    /**
     * 领取奖品
     */
    public function receive() {
        if (IS_POST) {
            $data['tel'] = $_POST['tel'];
            $data['gid'] = $_POST['id'];
            $data['tel'] = $_POST['time'];
            $data['order'] = $_POST['order'];
            $data['city'] = $_POST['city'];
            $data['phone'] = $_POST['tel'];
            $data['name'] = $_POST['name'];
            $data['addres'] = $_POST['addres'];
            $data['email'] = $_POST['email'];
            $data['number'] = $_POST['number'];
            $data['createtime'] = time();
            $qry = M("game_list")->add($data);
            if ($qry) {
                $this->display('receive_over');
                exit;
            } else {
                exit("操作失败");
            }
        }
        $this->assign('gid', $this->gid);
        $this->assign('time', $_GET['time']);
        $this->assign('order', $_GET['order']);
        $this->assign('number', $_GET['number']);
        $this->display();
    }

    /**
     * 查询排名
     */
    public function search() {
        $this->assign('gid', $this->gid);
        if (IS_POST) {
            $tel = $_POST['tel'];
            //查出用户积分
            $data = M('game_info')->where(array('gid' => $this->gid, 'tel' => $tel))->find();

            $number = $data['share'] * 10 + $data['number'];
            $this->assign('number', $number);
            //查出排名
            $info = M('game_info')->query('SELECT count(*) as c,share*10+number as n FROM `tp_game_info` WHERE share*10+number>' . $number . ' order by n desc');
            $order = $info[0]['c'] + 1;
            $this->assign('order', $order);
            $one = M('game_info')->query("SELECT  tel,share*10+number as n FROM `tp_game_info`  order by n desc limit 9,1"); //第一名你前面的排名
            $two = M('game_info')->query("SELECT  tel,share*10+number as n FROM `tp_game_info`  order by n desc limit 2999,1"); //第二名和你最近的
            $this->assign('one', $one[0]);
            if ($two) {
                $this->assign('two', $two[0]);
            }
            if ($order > 3000) {
                $createtime = $data['createtime'];
                $top = M('game_info')->query("SELECT count(*) as c  FROM `tp_game_info` where createtime<$createtime ORDER BY createtime asc");
                if ($top) {
                    $this->assign('top', $top[0]['c']);
                }
            }
            $this->assign('tel', $tel);
            $this->display('over');
            exit;
        }
        $this->display();
    }

    /**
     * 用户转发到朋友圈 得到转发数
     */
    public function sharetimeline() {
        $tel = $_GET['time'];
        $qry = M("game_info")->field("sharetime,share")->where(array('gid' => $this->gid, 'tel' => $tel))->find();
        //添加每天转发数记录

        $gamedata = M('game_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
        if ($gamedata) {
            M("game_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('shares', 1);
        } else {
            $d['gid'] = $this->gid;
            $d['tel'] = $tel;
            $d['shares'] = 1;
            $d['createdate'] = date("Y-m-d");
            M("game_date")->add($d);
        }
        //end 
        if ($qry['sharetime']) {
            if ($qry['share'] < 20) {
                M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('share', 1);
            }
        } else {
            M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('share', 1);
            M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->save(array('sharetime' => time()));
        }
    }

    /*
     * 检测手机号是否存在存在了提醒
     */

    public function chicktel() {
        $tel = trim($_GET['tel']);
        $qry = M("game_info")->where(array('gid' => $this->gid, 'tel' => $tel))->find();
        if ($qry) {
            echo '1';
        } else {
            echo '0';
        }
        exit;
    }

    /**
     * 获得微信用户信息
     * @param type $code
     * @param type $appid
     * @param type $appsecret
     * @return type
     */
    private function getUserInfo($code, $appid, $appsecret) {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);

        return $access_token_array;
    }

    private function https_request($url, $method = 'get', $data = '') {
        $ch = curl_init();
        $header[] = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
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
