<?php

class DoingAction extends Action {

    private $gid;
    private $gameinfo;
    private $url;

    public function _initialize() {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'static');
        $this->gid = $_REQUEST['gid'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
        $this->url= C('site_url');
        $data = M('doing')->where(array('gid' => $this->gid))->find();
        if ($data) {
            $this->gameinfo = $data;
            $this->assign('gameinfo', $this->gameinfo);
            
        }
    }

    public function index() {
         if (time() > $this->gameinfo['end']) {//活动是否结束
                exit('<center>游戏已经结束！谢谢你的参与</center>');
          }
        //统计添加浏览数和浏览记录
        if ($_GET["tel"] != "") {
            $tel = $_GET["tel"];
            $gamedata = M('doing_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
            if ($gamedata) {
                M("doing_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('jons', 1);
            } else {
                $d['gid'] = $this->gid;
                $d['tel'] = $tel;
                $d['jons'] = 1;
                $d['createdate'] = date("Y-m-d");
                M("doing_date")->add($d);
            }
            M("doing_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('joins', 1);
        }
        //统计end
        $userinfo = cookie('user_openid');
        if ($userinfo || $_GET['show']) {
            $this->assign('gid', $this->gid);
            $this->assign('openid', $userinfo);
            $this->display();
        } else {
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            if ($code && $state == 'sentian') {
                $userinfo = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                cookie('user_openid', $userinfo['openid'], 315360000);
                $this->assign('gid', $this->gid);
                $this->assign('openid', $userinfo['openid']);
                $this->display();
                exit;
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Doing&a=index&gid=$this->gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }
    }

    /**
     * 按钮点击统计
     */
    public function date() {
        $t = $_GET['t'];
        $doing_info_date = M('doing_info_date')->where(array('dateinfo' => date("Y-m-d")))->find();
        if ($doing_info_date) {
            if ($t == 'p') {
                M("doing_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('participate', 1);
            } else if ($t == 's') {
                M("doing_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('start', 1);
            } else if ($t == 'sub') {
                M("doing_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('submit', 1);
            } else if ($t == 'm') {
                M("doing_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('more', 1);
            }
        } else {
            $d = array();
            if ($t == 'p') {
                $d['participate'] = 1;
            } else if ($t == 's') {
                $d['start'] = 1;
            } else if ($t == 'sub') {
                $d['submit'] = 1;
            } else if ($t == 'm') {
                $d['more'] = 1;
            }
            $d['dateinfo'] = date("Y-m-d");
            M("doing_info_date")->add($d);
            exit;
        }
    }

    /**
     * 填写信息页面
     */
    public function input() {
        $openid = trim($_GET["openid"]);
        $number = trim($_GET["number"]);
        $this->assign('gid', $this->gid);
        $this->assign('openid', $openid);
        $this->assign('number', $number);
        $this->display();
    }

    /**
     * 领取奖品
     */
    public function receive() {
        
        if (IS_POST) {
            $data['tel'] = trim($_POST['tel']);
            $data['gid'] = $_POST['gid'];
            $data['temptel'] = $_POST['time'];
            $data['order'] = $_POST['order'];
            $data['number'] = $_POST['number'];
            $data['name'] = $_POST['name'];
            $data['addres'] = $_POST['addres'];
            $data['email'] = $_POST['email'];
            $data['city'] = $_POST['city'];
            $data['createtime'] = time();
            $time = $data['temptel'];
            $doing_list = M('doing_list')->where(array('temptel' => $time))->find();
            if (!$doing_list) {
                $qry = M("doing_list")->add($data);
                if ($qry) {
                    exit("<center><h1>登记成功</h1></center>");
                    header("location:$this->url/index.php?g=Wap&m=Doing&a=share&gid=$this->gid&time=$time");
                    exit;
                } else {
                    exit("操作失败");
                }
            } else {
                $qry = M("doing_list")->where(array('id' => $doing_list['id']))->save($data);
                 exit("<center><h1>登记成功</h1></center>");
                header("location:$this->url/index.php?g=Wap&m=Doing&a=share&gid=$this->gid&time=$time");
                exit;
            }
        }
        $this->assign('gid', $this->gid);
        $this->assign('time', $_GET['time']);
        $this->assign('order', $_GET['order']);
        $this->assign('number', $_GET['number']);
        $this->display();
    }

    /*
     * 检测手机号是否存在存在了提醒15670290085
     */

    public function chicktel() {
        $tel = trim($_GET['tel']);
        $qry = M("doing_info")->where(array('tel' => $tel))->find();
        if ($qry) {
            if($qry['views']<$qry['vote']||$qry['tel']=='13376612948'||$qry['tel']=='13083984221'){
                echo '4';
            }else{
                echo '1';
            }
            
        } else {
            echo '0';
        }
        exit;
    }

    /**
     * 保存用户信息
     */
    public function add() {
        if (IS_POST) {
            $data['tel'] = trim($_POST['tel']);
            $data['number'] = $_POST['usercoreNum'];
            $data['gid'] = $_POST['gid'];
            $data['createtime'] = time();
            $data['openid'] = $_POST['openid'];
            $data['name'] = $_POST['name'];
            $doing_info = M('doing_info')->where(array('tel' => $data['tel']))->find();
            if ($doing_info) {
                $this->assign('gid', $doing_info['gid']);
                $this->assign('number', $data['number']);
                $this->assign('tel', $data['tel']);
                $this->display();
            } else {
                $qry = M("doing_info")->add($data);
                if ($qry) {
                    //同步到crm
                    $memberinfo = M('member_user')->where(array('openid' => $data['openid']))->find();
                    if (!$memberinfo) {
                        $u['u_money'] = 0;
                        $u['u_member'] = 0;
                        $u['u_iphone'] = $data['tel'];
                        $u['u_name'] = $data['name'];
                        $u['signscore'] = 0;
                        $u['u_sex'] = 0;
                        $u['token'] = 'rggfsk1394161441';
                        $u['openid'] = $data['openid'];
                        $u['u_form'] = '活动';
                        $u['starttime'] = time();
                        M('member_user')->add($u);
                    } else if (empty($memberinfo['u_iphone'])) {
                        $is = M('member_user')->where(array('openid' => $data['openid']))->save(array('u_iphone' => $data['tel']));
                    }
                    //同步到crm end 
                    $this->assign('gid', $this->gid);
                    $this->assign('number', $data['number']);
                    $this->assign('tel', $data['tel']);
                    $this->display();
                    exit;
                }
            }
        }
    }

    /**
     * 查询排名
     */
    public function search() {

        if (IS_POST) {
            $tel = $_POST['tel'];
            //查出用户积分
            $data = M('doing_info')->where(array('tel' => $tel))->find();
            if ($data) {
                $number = $data['share'] * 10 + $data['number'];
                
                $this->assign('number', $number);
                //查出排名
                $info = M('doing_info')->query('SELECT count(*) as c,share*10+number as n FROM `tp_doing_info` WHERE share*10+number>' . $number . ' order by n desc');
                $order = $info[0]['c'] + 1;
                $this->assign('order', $order);
                $one = M('doing_info')->query("SELECT  tel,share*10+number as n FROM `tp_doing_info`  order by n desc limit 9,1"); //第10名
                if ($one) {
                    $this->assign('one', $one[0]);
                }
                $two = M('doing_info')->query("SELECT  share*10+number as n FROM `tp_doing_info`  order by n desc limit 3009,1"); //第10名
                if ($two) {
                    $this->assign('two', $two[0]);
                }
                $this->assign('gid', $data['gid']);
                $this->assign('tel', $data['tel']);
                $this->display('over');
                exit;
            } else {
                $this->assign('isover', 1);
                $this->display('over');
                exit;
            }
        }
        $this->display();
        exit;
    }

    public function share() {
        $tel = trim($_GET['time']);
        $userinfo = cookie('user_openid');
        if ($userinfo || $_GET['show']) {//有openid
            $data = M('doing_info')->where(array('gid' => $this->gid, 'tel' => $tel))->find();
            $number = $data['share'] * 10 + $data['number'];
            $this->assign('number', $number); //积分
            $this->assign('name', $data['name']);
            $this->assign('tel', $tel);
            $this->assign('gid', $this->gid);
            $this->assign('openid', $userinfo);
            if (time() > $this->gameinfo['end']) {//活动是否结束
                $this->assign('end', '1'); //活动结束不在参加活动了
            }
            if ($data['sharetime']) {//转发过
                $my = time() - $data['sharetime'];
                $this->assign('share', '1');
                if ($my < 259200) {//72小时内
                    $jishi = $data['sharetime'] + 259200;
                    $jishi = date('Y-m-d H:i:s', $jishi);
                    $this->assign('jishi', $jishi);
                    $this->assign('oldtime', '1');
                    $d = M('doing_info_vote')->where(array('gid' => $this->gid, 'tel' => $tel, 'openid' => $userinfo))->find();
                    if ($d) {//投票过
                        $this->assign('isvote', '1');
                    }
                    //统计添加浏览数和浏览记录
                    $gamedata = M('doing_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
                    if ($gamedata) {
                        M("doing_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('views', 1);
                    } else {
                        $d['gid'] = $this->gid;
                        $d['tel'] = $tel;
                        $d['views'] = 1;
                        $d['createdate'] = date("Y-m-d");
                        M("doing_date")->add($d);
                    }
                    M("doing_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('views', 1);
                }
            } else {//没转发过
                $d = M('doing_info_vote')->where(array('gid' => $this->gid, 'tel' => $tel, 'openid' => $userinfo))->find();
                if ($d) {//投票过
                    $this->assign('isvote', '1');
                }
                $jishi = time() + 259200;
                $jishi = date('Y-m-d H:i:s', $jishi);
                $this->assign('jishi', $jishi);
                //统计添加浏览数和浏览记录

                $gamedata = M('doing_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
                if ($gamedata) {
                    M("doing_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('views', 1);
                } else {
                    $d['gid'] = $this->gid;
                    $d['tel'] = $tel;
                    $d['views'] = 1;
                    $d['createdate'] = date("Y-m-d");
                    M("doing_date")->add($d);
                }
                M("doing_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('views', 1);
            }
            $this->display();
        } else {
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            if ($code && $state == 'sentian') {
                $userinfo = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                cookie('user_openid', $userinfo['openid'], 315360000);
                header("location:$this->url/index.php?g=Wap&m=Doing&a=share&gid=$this->gid&time=$tel");
                exit;
            } else {
                $url = urlencode("$this->url/index.php?g=Wap&m=Doing&a=share&gid=$this->gid&time=$tel");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }
    }

    /**
     * 点击帮他补水
     */
    public function shareget() {

        $doing_info = M("doing_info")->field("sharetime,share")->where(array('gid' => $this->gid, 'tel' => $_GET['tel']))->find();
        $my = time() - $doing_info['sharetime'];
        if ($doing_info && $my < 259200) {//在72小时内能帮他补水
            $data = M('doing_info_vote')->where(array('gid' => $this->gid, 'tel' => $_GET['tel'], 'openid' => $_GET['openid']))->find();
            if (!$data) {//投票过
                $data['tel'] = $_GET['tel'];
                $data['openid'] = $_GET['openid'];
                $data['gid'] = $_GET['gid'];
                $data['createtime'] = time();
                $qry = M("doing_info_vote")->add($data);
                $gamedata = M('doing_date')->where(array('gid' => $this->gid, 'tel' => $data['tel'], 'createdate' => date("Y-m-d")))->find();
                if ($gamedata) {
                    M("doing_date")->where(array('gid' => $this->gid, 'tel' => $data['tel'], 'createdate' => date("Y-m-d")))->setInc('votes', 1);
                } else {
                    $d['gid'] = $this->gid;
                    $d['tel'] = $data['tel'];
                    $d['votes'] = 1;
                    $d['createdate'] = date("Y-m-d");
                    M("doing_date")->add($d);
                }
                $d['vote'] = array('exp', 'vote+1');
                $d['number'] = array('exp', 'number+5');
                M("doing_info")->where(array('gid' => $this->gid, 'tel' => $data['tel']))->save($d);
            }
            echo '1';
            exit;
        } else {
            echo '0';
            exit;
        }
    }

    /**
     * 用户转发到朋友圈 得到转发数
     */
    public function sharetimeline() {
        $tel = $_GET['tel'];
        //添加每天转发数记录
        $gamedata = M('doing_date')->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->find();
        if ($gamedata) {
            $x = M("doing_date")->where(array('gid' => $this->gid, 'tel' => $tel, 'createdate' => date("Y-m-d")))->setInc('shares', 1);
        } else {
            $d['gid'] = $this->gid;
            $d['tel'] = $tel;
            $d['shares'] = 1;
            $d['createdate'] = date("Y-m-d");
            M("doing_date")->add($d);
        }
        //end 
        $qry = M("doing_info")->field("sharetime,share")->where(array('gid' => $this->gid, 'tel' => $tel))->find();
        if ($qry['sharetime']) {
            $my = time() - $qry['sharetime'];
            if ($my < 259200 && $qry['share'] < 21) {
                $y = M("doing_info")->where(array('gid' => $this->gid, 'tel' => $tel))->setInc('share', 1);
            }
        } else {
            $y = M("doing_info")->where(array('gid' => $this->gid, 'tel' => $tel))->save(array('sharetime' => time(), 'share' => 1));
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
