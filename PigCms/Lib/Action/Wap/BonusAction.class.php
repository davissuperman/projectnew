<?php

class BonusAction extends Action {

    private $gid;
    private $gameinfo;
    private $url;
    //前几票都是加法
    public $before = 1;
    public $secondLevelNumber = 5;//二等奖的个数
    public $firstLevelNumber = 3;//一等奖的个数
    public $fourLevelNumber = 3000;//四等奖的个数
    public $threeLevelNumber = 300;//三等奖的个数

    public $fiveScore = 50;
    public $configBonus = array(
        300 => array(
            1 => array(
                'count' => 1000,
                'vote' => 20
//                 'vote' => 3 //测试用四等奖
            ),
            2 => array(
                'count' => 3000,
                'vote' => 25
            ),
            3 => array(
                'count' => 5000,
                'vote' => 30
            )
        ),
        600 => array(
            1 => array(
                'count' => 200,
                'vote' => 60
//                'vote' => 4//测试用3等奖
            ),
            2 => array(
                'count' => 300,
                'vote' => 80
            ),
            3 => array(
                'count' => 500,
                'vote' => 110
            )
        ),
        1000 => array(
            1 => array(
                'count' => 1,
//                'vote' => 500
                'vote' => 5000
//                'vote' => 5//测试用2等奖
            ),
        ),
        2000 => array(
            1 => array(
                'count' => 1,
                'vote' => 8000
//                    'vote' => 6//测试用1等奖
            ),
        )
    );

    public $titleInWeixin = array(
        '森田药妆年终奖大派送，我的IPHONE6，IPAD就差你一票！',
        '还在羡慕别人年终奖？森田药妆邀您领IPHONE6回家过年！',
        '年前最后一波！一大波IPHONE6、Ipad在这里！礼多人人有份！',
    );

    public $hashKeyBonusInfo;

//    public $score = array(-14,-13,-12,-11,-10,-9,-8,-7,-6,-5,-4,-3,-2,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,35,36,37,38,39,40,41,42,43,44);
    public $score = array(-11,-10,-9,-8,-7,-6,-5,-4,-3,-2,1,2,3,4,5,6,7,8,9,10,11,12,13,14);
    public $leftIntval = 14;
    public $minus = 20;

    public $cache;

    public function _initialize() {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'static');
        $this->gid = $_REQUEST['gid'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
        $this->url= C('site_url');
        $this->cache = Cache::getInstance('Redis',array('host'=>'127.0.0.1','expire'=>1296000));
    }

    public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $gid = $_GET['gid'];
        //统计end
        $userOpenId= cookie('user_openid');
        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if ($_GET['show']) {
            $userOpenId= $_GET['openid'];
            $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
                //测试的时候使用
            //如果存在 则不需要再通过weixin API调取用户信息
            $selfUserInfo['headimgurl'] = $fansInfo['headimgurl'];
            $selfUserInfo['nickname'] = $fansInfo['nickname'];
            $this->assign('gid', $gid);
            $this->assign('openid', $userOpenId);
            $this->assign('userinfo', $selfUserInfo);
            $this->assign('title', $selfUserInfo['nickname']."我要年终奖");
        } else {
            //根据open id获取用户信息，查看是否存在
            $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
            if($userOpenId && $fansInfo){
                //如果存在 则不需要再通过weixin API调取用户信息
                $selfUserInfo['headimgurl'] = $fansInfo['headimgurl'];
                $selfUserInfo['nickname'] = $fansInfo['nickname'];
            }else{
                $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
                /*
                 * (
    [id] => 57
    [token] => rggfsk1394161441
    [appid] => wx36026301d4b1cb01
    [appsecret] => 79311ea02ea318af5f228492bf119104
    [access_token] => OLm6xxe7ilv67WAYOPI7BRuShRcBsy_HsQQWzzSAGgSrA2CRQvuzbosrRfBkyLqVVvfKPQ9OPh7ea1MwqjSAr59OQfuTE-2PaGnWqNqcDgw
    [create_time] => 1422539837
    [ticket] => sM4AOVdWfPE4DxkXGEs8VOWW8mZFJb5M5d1VppLZ6KpY7ppbstCaFh5Dt8mBW84M0RpNX4Zk-bVNlmsLM4NFYQ
    [ticket_time] => 1422539837
)

                 */
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期

                    /**
                     * $userinfoFromApi
                     * (
                    [access_token] => OezXcEiiBSKSxW0eoylIeIBDFlSdQvVjGi6djtyA0hoTzn6QsnB97jvbYipE6P3cILGN4uV6t_i_Kri7t5p4qpiC0DDbojA1Nr0U1OIZHt4y2Xmwrdz1EJ8TybUEwbEKXbc_pWX85Tv6mzPgmk9IKw
                    [expires_in] => 7200
                    [refresh_token] => OezXcEiiBSKSxW0eoylIeIBDFlSdQvVjGi6djtyA0hoTzn6QsnB97jvbYipE6P3cjBcKADJNNNUNLEUqTkrqX_TK6Tc-SzJpfb9MR9Nojab9QRstwo9mSX5BgwYjWe5JAeD-yLHj1rBpkMdYjlTXvQ
                    [openid] => oYkdqs5s1IEIhB9bulM2AJ6GgZh8
                    [scope] => snsapi_userinfo
                    )
                     */
                    if(empty($fansInfo)){
                        $webCreatetime = $apidata['web_createtime'];
                        $web_access_token = '';

                        if($webCreatetime>(time()-7200) && $userOpenId){
                            //未过期
                            $web_access_token = $apidata['web_access_token'];
                        }else if($webCreatetime<=(time()-7200) && $userOpenId && isset($apidata['refresh_token']) && ($apidata['refresh_token_createtime']>(time()-7*3600*24))  ){
                                //从新获取通过
                                $urlRefreshToken = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.
                                    $apidata['appid'].'&grant_type=refresh_token&refresh_token='.$apidata['refresh_token'];
                                $jsonRefresh = json_decode($this->curlGet($urlRefreshToken));
                            $web_access_token = $jsonRefresh->access_token;
                            $refresh_token = $jsonRefresh->refresh_token;
                            $m['id'] = $apidata['id'];
                            $m['web_access_token'] = $web_access_token;
                            $m['refresh_token'] =$refresh_token;
                            $m['web_createtime'] = time();
                            $m['refresh_token_createtime'] = time();
                            M('Diymen_set')->save($m);
                        }else{
                            //重新获取
                            $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                            if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                                //code 有错误 需要重定向
                                $url = $this->url."/index.php?g=Wap&m=Bonus&a=index&gid=$this->gid";
                                header("location:$url");
                            }
                            $m['id'] = $apidata['id'];
                            $m['web_access_token'] = $userinfoFromApi['access_token'];
                            $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                            $m['web_createtime'] = time();
                            $m['refresh_token_createtime'] = time();
                            M('Diymen_set')->save($m);
                            $web_access_token = $userinfoFromApi['access_token'];
                            cookie('user_openid', $userinfoFromApi['openid'], 315360000);
                            $userOpenId = $userinfoFromApi['openid'];
                        }

                        //根据access_token 拉到用户基本信息
                        $gUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$web_access_token.'&openid='.$userOpenId.'&lang=zh_CN';
                        $json = json_decode($this->curlGet($gUrl));

                        $this->saveUserInfo($json);
                        $selfUserInfo['headimgurl'] = $json->headimgurl;
                        $selfUserInfo['nickname'] = $json->nickname;
                    }
                } else {
                    $url = urlencode($this->url."/index.php?g=Wap&m=Bonus&a=index&gid=$this->gid");
                    header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                    exit;
                }
            }

            //OPEN URL ID存在 则保存相关信息
            if (isset($_GET["openid"]) && $_GET["openid"]) {
                $openid = $_GET["openid"];
                $this->saveBonusViewInfo($gid,$openid);
                $this->saveViews($gid,$openid,true);
            }
        }
        $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $accessToken = $apidata['access_token'];
        $ticket = $apidata['ticket'];
        if($apidata['access_token']==null || (time()-1*$apidata['create_time']>7200)){
            //需要重新获取access_token
            $json = $this->getAccessToken($apidata['appid'],$apidata['appsecret']);
            $fdata['id'] = $apidata['id'];
            $fdata['token'] = 'rggfsk1394161441';
            $fdata['access_token'] = $json['access_token'];
            $accessToken = $fdata['access_token'];
            $fdata['create_time'] = time();
            M('Diymen_set')->save($fdata);
        }
        if($apidata['ticket']==null || (time()-1*$apidata['ticket_time']>7200)){
            $token = $accessToken;
            $jsonTicket = $this->getJsTicket($token);
            if($jsonTicket['errmsg'] == 'ok'){
                $fdata['id'] = $apidata['id'];
                $fdata['token'] = 'rggfsk1394161441';
                $fdata['ticket'] = $jsonTicket['ticket'];
                $ticket = $fdata['ticket'];
                $fdata['ticket_time'] = time();
                M('Diymen_set')->save($fdata);
            }
        }
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
//        $timestamp = '1414587457';
//        $ticket = 'sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qg';
//        $url = "http://". $this->_server('HTTP_HOST');
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("url",$url);

        $this->assign('gid', $gid);
        $this->assign('openid', $userOpenId);
        $this->assign('userinfo', $selfUserInfo);
        $this->assign('nickname',  $selfUserInfo['nickname']);
        $titleArr = $this->titleInWeixin;
        $titleUsed = $selfUserInfo['nickname'];
        $titleUsed .= " ".$titleArr[rand(0,count($titleArr)-1)];

        $bonusDes = $this->getTitleAndImageByGid($gid);
        $this->assign('title',$selfUserInfo['nickname'].":".$bonusDes['desc']);
        $this->assign('bonusdesc','');
        $this->assign("imageUrl",$bonusDes['img']);
        $this->assign("shareimageurl",$bonusDes['img']);
//        $this->assign("imageUrl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/bonus/images/profile1.png');

        //获取获奖名单
        $condition['type'] = array('elt',2);
        $resAwardList = M('bonus_award')->where($condition)->select();
        $awardList = array();
        $hasAward = false;
        if($resAwardList){
            $hasAward = true;
            foreach($resAwardList as $eachAward){
                $tmp = array();
                $tmp['username'] = $eachAward['username'];
                if($eachAward['type'] == 2){
                    $tmp['type'] = "IPAD";
                }elseif($eachAward['type'] == 1){
                    $tmp['type'] = "IPHONE";
                }
                $awardList[] = $tmp;
            }
        }
        $this->assign('hasaward',$hasAward);
        $this->assign('awardlist',$awardList);
        $this->display();
    }

    public function getTitleAndImageByGid($gid){
        $condition['gid'] = $gid;
        $bonusDes = M('bonus')->where($condition)->field('title,img,desc')->find();
        return $bonusDes;
    }

    public function confirm(){
        $phone = $_POST['phone'];
        $error = '';
        if(IS_POST){
            if($phone == ''){
                $error = "请输入手机号";
            }
        }


        $fourlevel = false;
        $threelevel = false;
        $fourOrderId = '';
        $threeOrderId = '';

        $fourlevelId = '';
        $threelevelId = '';
        $show = false;
        $res = M('bonus_award')->where(array('telephone' => $phone))->select();

        $cheat = false;
        if($res){
            $show = true;
            foreach($res as $each){
                $type = $each['type'];
                if($type == 4){
                    //四等奖存在
                    //根据openid 取得阅读数和投票数 查看是否是作弊数据
                    $openId = $each['openid'];
                    $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
                    $views = $bonusInfo['views'];
                    $vote = $bonusInfo['vote'];
                    if($views < $vote){
                        //作弊数据
                        $cheat = true;
                        break;
                    }
                    $fourlevel = true;
                    $fourOrderId = $each['orderid'];
                    $fourlevelId = $each['id'];
                    $fourTime = $each['createtime'];
                    $fourOrderTime = $each['order_time'];
                }
                if($type == 3){
                    //三等奖存在
                    $openId = $each['openid'];
                    $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
                    $views = $bonusInfo['views'];
                    $vote = $bonusInfo['vote'];
                    if($views < $vote){
                        //作弊数据
                        $cheat = true;
                        break;
                    }
                    $threelevel = true;
                    $threeOrderId = $each['orderid'];
                    $threelevelId = $each['id'];
                    $threeTime = $each['createtime'];
                    $threeOrderTime = $each['order_time'];
                }
            }
        }else{
            if($phone){
                $error = "此手机号暂无领奖信息";
            }
        }

        $this->assign('cheat',$cheat);
        $this->assign('show',$show);
        $this->assign('error',$error);
        $this->assign('phone',$phone);
        $this->assign('fourlevel',$fourlevel);
        $this->assign('fourlevelid',$fourlevelId);
        $this->assign('fourorderid',$fourOrderId);

        $this->assign('threelevel',$threelevel);
        $this->assign('threeorderid',$threeOrderId);
        $this->assign('threelevelid',$threelevelId);

        $this->assign('fourtime',date("Y-m-d H:i:s",$fourTime));
        $this->assign('fourtimediff',$this->timediff($fourTime,time()));
        if($fourOrderTime){
            $this->assign('fourordertime',date("Y-m-d H:i:s",$fourOrderTime));
        }else{
            $this->assign('fourordertime',false);
        }

        $this->assign('threetime',date("Y-m-d H:i:s",$threeTime));
        $this->assign('threetimediff',$this->timediff($threeTime,time()));
        if($threeOrderTime){
            $this->assign('threeordertime',date("Y-m-d H:i:s",$threeOrderTime));
        }else{
            $this->assign('threeordertime',false);
        }


        $this->display();
    }
    function timediff($begin_time,$end_time)
    {
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }
        else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        $timediff = $endtime-$starttime;
        $days = intval($timediff/86400);
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        $remain = $remain%3600;
        $mins = intval($remain/60);
        $secs = $remain%60;
        $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
        return $res;
    }
    public function checkPhone(){
        $phone = $_POST['phone'];
        //检查这个$phone是否中奖
        $res = M('bonus_award')->where(array('telephone' => $phone))->select();
        echo json_encode($res);
    }
    public function saveOrderId(){
        $phone = $_POST['phone'];
        $type = $_POST['type'];
        $orderid = $_POST['orderid'];
        $id = $_POST['id'];
        $h['id'] = $id;
        $h['orderid'] = $orderid;
        $h['order_time'] = time();
        $return = M("bonus_award")->where(array('id' =>$id))->save($h);

        echo $return;
    }
    /**
     * 获取用户基本信息并且保存到数据库
     */
    public function saveUserInfo($json){
        $token = "rggfsk1394161441";
        $fanModel = M('customer_service_fans'); //向粉丝表中添加此用户信息
        $ffind = $fanModel->where(array('openid' => $json->openid, 'token' => $token))->find();

        if ($ffind) {
            //$this->subscribe(1); //如果粉丝表中有此用户则重新关注
        } else {
            if (property_exists($json, 'errcode') && $json->errcode) {//有些用户没有高级接口，不能获得用户信息
            } else {
                $fdata['openid'] =  $json->openid;
                $fdata['token'] = $token;
                $fdata['nickname'] = $json->nickname;
                $fdata['sex'] = $json->sex;
                $fdata['country'] = $json->country ? $json->country : '未知';
                if ($fdata['country'] != '中国') {
                    $fdata['province'] = $fdata['country'];
                    $fdata['city'] = $json->province ? $json->province : $fdata['province'];
                } else {
                    $fdata['province'] = $json->province ? $json->province : $fdata['country'];
                    $fdata['city'] = $json->city ? $json->city : $fdata['province'];
                }
                $fdata['headimgurl'] = $json->headimgurl;
                $fdata['subscribe_time'] = time();
                $fdata['subscribe'] = 1;
                $fdata['u_from'] = 2;
                $fanModel->add($fdata);
                //同时吧粉丝资料放到 tp_member_user 表
                $memberinfo = M('member_user')->where(array('token' => $this->token, 'openid' => $json->openid))->find();
                $u['u_name'] = $fdata['nickname'];
                $u['u_sex'] = $fdata['sex'];
                $u['u_money'] = 0;
                $u['u_address'] = $fdata['city'];
                $u['u_member'] = 1;
                if ($memberinfo) {
                    M('member_user')->where(array('token' => $token, 'openid' => $json->openid))->save($u);
                } else {
                    $u['token'] = $token;
                    $u['openid'] = $json->openid;
                    $u['u_form'] = '网页授权';
                    $u['starttime'] = time();
                    $u['subscribe'] = 1;
                    $u['interaction'] = time();
                    M('member_user')->add($u);
                }
            }
        }
    }
    /**
     * 按钮点击统计
     */
    public function date() {
        $t = $_GET['t'];
        $doing_info_date = M('bonus_info_date')->where(array('dateinfo' => date("Y-m-d")))->find();
        if ($doing_info_date) {
            if ($t == 'p') {
                M("bonus_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('participate', 1);
            } else if ($t == 'sub') {
                M("bonus_info_date")->where(array('dateinfo' => date("Y-m-d")))->setInc('submit', 1);
            }
        } else {
            $d = array();
            if ($t == 'p') {
                $d['participate'] = 1;
            }  else if ($t == 'sub') {
                $d['submit'] = 1;
            }
            $d['dateinfo'] = date("Y-m-d");
            M("bonus_info_date")->add($d);
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
                    header("location:$this->url/index.php?g=Wap&m=Doing&a=share&gid=$this->gid&time=$time");
                    exit;
                } else {
                    exit("操作失败");
                }
            } else {
                $qry = M("doing_list")->where(array('id' => $doing_list['id']))->save($data);
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
     * 检测手机号是否存在存在了提醒
     */

    public function chicktel() {
        $tel = trim($_GET['tel']);
        $qry = M("doing_info")->where(array('tel' => $tel))->find();
        if ($qry) {
            echo '1';
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
     * bonus history
     */
    public function saveBonusHistory($gid,$openId,$number,$fromOpenId,$bonusInfoRedisKey=null){
        $h['openid'] = $openId;
        $h['gid'] = $gid;
        $h['description'] = $this->getRandDescriptionByNumber($number);
        $h['number'] = $number;
        $h['from_open_id'] = $fromOpenId;
        M("bonus_history")->add($h);
        //添加历史到REDIS
        //此时 需要获取 from_open_id的nickname headimage url 和 description
        $bonusInfoRedisKey = "bonusinfo_".$fromOpenId;
        $bonusInfoName = $this->cache->redis->hget($bonusInfoRedisKey,'name');
        if($bonusInfoName){
            //此用户信息存在与redis中
            $h['headimgurl'] = $this->cache->redis->hget($bonusInfoRedisKey,'headimgurl');
            $h['nickname'] = $this->cache->redis->hget($bonusInfoRedisKey,'name');
        }else{
            //不存在 需要通过数据库 bonus_info获取
            $fansInfo = M('customer_service_fans')->where(array('openid' => $fromOpenId,'token'=>'rggfsk1394161441'))->find();
            $h['nickname'] = $fansInfo['nickname'];
            $h['headimgurl'] = $fansInfo['headimgurl'];
            $this->cache->redis->hset($bonusInfoRedisKey,'headimgurl',$fansInfo['headimgurl']);
            $this->cache->redis->hset($bonusInfoRedisKey,'nickname',$fansInfo['nickname']);
        }
        //URL OPEN ID的加分历史加一
        $this->cache->redis->lPush("bonusinfo_".$openId."_history",json_encode($h));
    }

    /**
     * @param $number
     * @return mixed
     * 从数据库中随机获取描述
     */
    public function getRandDescriptionByNumber($number){
        $list = array();
        if($number > 0){
            //正数
            $bonusDescriptionPositive = "positive_description";
            $positiveList = $this->cache->get($bonusDescriptionPositive);
            if(!$positiveList){
                //从数据库 tp_bonus_description
                $res = M("bonus_description")->where(array('number'=>1))->select();
                $arr = array();
                foreach($res as $each){
                    $tmp = array();
                    $arr[] = $each['description'];
                }
                $positiveList = $arr;
                $this->cache->set($bonusDescriptionPositive,$arr);
            }

            $list = $positiveList;
        }else{
            //负数 negative
            $bonusDescriptionNegative = "negative_description";
            $negativeList = $this->cache->get($bonusDescriptionNegative);
            if(!$negativeList){
                //从数据库 tp_bonus_description
                $res = M("bonus_description")->where(array('number'=>0))->select();
                $arr = array();
                foreach($res as $each){
                    $tmp = array();
                    $arr[] = $each['description'];
                }
                $negativeList = $arr;
                $this->cache->set($bonusDescriptionNegative,$arr);
            }
            $list = $negativeList;
        }

        $count = count($list);
        $n = rand(0,$count-1);
        return $list[$n];
    }
    /**
     * 拉人品
     */
    public function shareget() {
        $openId = $_GET['openid'];
        $gid = $_GET['gid'];
        $bonusInfoRedisKey = "bonusinfo_".$openId;
        $selfOpenId = cookie("user_openid");
        if(!cookie("user_openid")){
            $selfOpenId = "localenv";
        }
        $return = 1;//2: 已经加过分数
        //首先判断此用户是否已经给URL OPENID 加过分
        $bonusHistory = M('bonus_history')->where(array('gid' => $gid, 'openid' => $openId,'from_open_id'=>$selfOpenId))->find();
        if($bonusHistory){
            //已经加过分数
            $return = 2;
        }else{
            $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
            $vote = $bonusInfo['vote'];
            $views = $bonusInfo['views'];
            $number = $bonusInfo['number'];
            if($views < $vote){
                //非法数据
                $r=9;
                return $r;
            }else{
                //随机生成分数
                $number =  $this->getNumberByOpenId($gid,$openId,$bonusInfo);
                //投票个数加一
                M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('vote', 1);
                $this->cache->redis->incr($bonusInfoRedisKey."_vote");
                //给URL OPEN ID 加分
                M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('number', $number);
                $this->cache->redis->incrBy($bonusInfoRedisKey."_number",$number);
                //保存拉人品记录历史
                $this->saveLaRenPing($gid,$openId,$number);
                $this->saveBonusHistory($gid,$openId,$number,$selfOpenId,$bonusInfoRedisKey);
            }


        }
        echo $return;
    }

    public function getRandScore($typeBigZero = false){
        $score = $this->score;
        if($typeBigZero){
            $num = rand(14,count($this->score)-1);
        }else{
            $num = rand(0,count($this->score)-1);
        }
        $return = $score[$num];
        return $return;
    }
    public function getNumberByOpenId($gid,$openId,$bonusInfo,$type=1){
        $configBonus = $this->configBonus;
        $score = $this->score;
        //获得当前票数
        $vote = $bonusInfo['vote'];//当前票数
        $vote = $vote*1 + 1;
        $number = $bonusInfo['number'];
        $return = 0;
        $scoreList = array_keys($this->configBonus);
        array_unshift($scoreList,$this->fiveScore);
        $five = $scoreList[0];
        $four =  $scoreList[1];
        $three =  $scoreList[2];
        $second =  $scoreList[3];
        $first =  $scoreList[4];

        $before = $this->before;

        //二等奖 每多一人 需要多的票数
        $xSecondLevel = 50;

        //一等奖 每多一人 需要多的票数
        $xFirstLevel = 100;

        if($number < $five){
            //五等奖
            $type = 5;
        }elseif($number >= $five && $number < $four){
            //四等奖
            $type = 4;
        }elseif($number >= $four && $number < $three){
            //三等奖
            $type = 3;
        }elseif($number >= $three && $number < $second){
            //二等奖
            $type = 2;
        }elseif( $number >= $second){
            //一等奖
            $type = 1;
        }


        switch($type){
            case 4:
                //四等奖需要分数250
                //前1000名 20票获得250积分
                //中期2000名25票获得250积分
                //后期2000名30票获得250积分
                $map['vote']  = array('egt',$configBonus[$four][1]['vote']);
                $bonusNumber= M('bonus_info')->where($map)->count('id');
                if($bonusNumber <= $configBonus[$four][1]['count'] ){
                    if($vote<=$before){
                        $return = $this->getRandScore(true);
                    }else if($vote < $configBonus[$four][1]['vote']){
                        $left = $four - $number;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }else if($vote >= $configBonus[$four][1]['vote']){
                        $return = $four - $number;
                    }
                }else if($bonusNumber <= $configBonus[$four][2]['count'] && $bonusNumber>$configBonus[$four][1]['count'] ){
                    //中期 2000名 25票获得250积分
                    if($vote<=$before){
                        $return = $this->getRandScore(true);;
                    }else if($vote < $configBonus[$four][2]['vote']){
                        $left = $four - $number;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }else if($vote >= $configBonus[$four][2]['vote']){
                        $return = $first - $number;
                    }
                }else if ( $bonusNumber > $configBonus[$four][2]['count'] ){
                    //后期 2000名 30票获得250积分
                    if($vote<=$before){
                        $return = $this->getRandScore(true);;
                    }else if($vote <  $configBonus[$four][3]['vote']){
                        $left = $four - $number;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();;
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }else if($vote >=  $configBonus[$four][3]['vote']){
                        $return = $four - $number;
                    }
                }
                if($number + $return < $five){
                    //不可以回到上一个等级
                    $return = $return + $five - ($number + $return) + 25;
                }
                break;
            case 3:
                //三等奖 需要分数500
                //前200名 100票
                //中期200名 120票
                //后期100名 150票

                $map['vote']  = array('egt',$configBonus[$three][1]['vote']);
                $bonusNumber= M('bonus_info')->where($map)->count('id');
                if($bonusNumber <= $configBonus[$three][1]['count']){
                    //100票
                    if($vote < $configBonus[$three][1]['vote']){
                        $left = $three*1 - $number*1;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }elseif($vote >= $configBonus[$three][1]['vote']){
                        $left = $three*1 - $number*1;
                        $return =  $left;
                    }
                }else if($bonusNumber > $configBonus[$three][1]['count'] && $bonusNumber <= $configBonus[$three][2]['count']){
                    //120
                    if($vote <  $configBonus[$three][2]['vote']){
                        $left = $three*1 - $number*1;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }elseif($vote >= $configBonus[$three][2]['vote']){
                        $left = $three*1 - $number*1;
                        $return =  $left;
                    }
                }else{
                    //150
                    if($vote < $configBonus[$three][3]['vote']){
                        $left = $three*1 - $number*1;
                        if($left > $this->leftIntval){
                            $return = $this->getRandScore();
                        }else{
                            $return = rand(0,$left) - $this->minus;
                        }
                    }elseif($vote >= $configBonus[$three][3]['vote']){
                        $left = $three*1 - $number*1;
                        $return =  $left;
                    }
                }
//                if($number + $return < $four){
//                    //不可以回到上一个等级
//                    $return = $return + $four - ($number + $return) + 5;
//                }
                break;
            case 2: //二等奖
                $map['vote']  = array('egt',$configBonus[$second][1]['vote']);
                $needVoteForSecond = $configBonus[$second][1]['vote'];

                //判断已经有多少人达到500票
                $bonusNumber= M('bonus_info')->where($map)->count('id');

                $leftNumber = $second - $number;//需要多少分数
                $needVote = $needVoteForSecond*1 + $xSecondLevel*$bonusNumber;
                if($vote < $needVote){
                    if($leftNumber > $this->leftIntval){
                        $return = $this->getRandScore();
                    }else{
                        $return = rand(0,$leftNumber)  - $this->minus;
                    }
                }else if($vote >= $needVote){
                    $return = $leftNumber;
                }
//                if($number + $return < $three){
//                    //不可以回到上一个等级
//                    $return = $return + $three - ($number + $return) + 5;
//                }
                if($return > 18) {
                    $return = -5;
                }
                break;
            case 1://一等将
                $map['vote']  = array('egt',$configBonus[$first][1]['vote']);
                $needVoteForSecond = $configBonus[$first][1]['vote'];

                //判断已经有多少人达到1000票
                $bonusNumber= M('bonus_info')->where($map)->count('id');

                $leftNumber = $first - $number;//需要多少分数
                $needVote = $needVoteForSecond*1 + $xFirstLevel*$bonusNumber;
                if($vote < $needVote){
                    if($leftNumber > $this->leftIntval){
                        $return = $this->getRandScore();
                    }else{
                        $return = rand(0,$leftNumber) - $this->minus;
                    }
                }else if($vote >= $needVote){
                    $return = $leftNumber;
                }
                if($number + $return < $second){
                    //不可以回到上一个等级
                    $return = $return + $second - ($number + $return) + 5;
                }
                break;


        }


        return $return;

        //判断当前有20票的用户数
    }


    /**
     * 用户转发到朋友圈 得到转发数
     */
    public function sharetimeline() {
        //需要考虑的问题 如果A 分享了 B 的主页


        $urlOpenId = $_GET['openid'];//当前转发的openid
        $gid = $_GET['gid'];//当前转发的openid
        $openId = cookie("user_openid");
        $bonusInfoRedisKey = "bonusinfo_".$openId;
        //判断selfOpenId是否有个人主页，如果有 则selfOpenId获得第一次分享的奖励
        $bonusInfo = M('bonus_info')->where(array( 'openid' => $openId))->find();
        $return = 1;
        if($bonusInfo){

            //是否已经获得过奖励
            if($bonusInfo['sharetime']){
                //已经获得过奖励，分享数加一
                M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('share', 1);
                $this->cache->redis->incr($bonusInfoRedisKey."_share");
                $return = 2;
            }else{
                //获得初次奖励的积分
                $d['id'] = $bonusInfo['id'];
                $d['openid'] = $openId;
                $d['sharetime'] = time();
                $d['share'] = 1;
                $d['number'] = 50;
                M("bonus_info")->save($d);

                //将积分记录到表bonus_history
                $h['openid'] = $openId;
                $h['gid'] = $gid;
                $h['headimgurl'] = $bonusInfo['headimgurl'];
                $h['nickname'] = $bonusInfo['name'];
                $h['description'] = "首次分享获取积分";
                $h['number'] = 50;
                M("bonus_history")->add($h);
                //将此历史记录推入redis
                $this->cache->lpush($bonusInfoRedisKey."_history",$h);
                $this->cache->redis->set($bonusInfoRedisKey."_share",1);
                $this->cache->redis->set($bonusInfoRedisKey."_number",50);

                $return = 1;
            }


        }
        echo $return;

    }

    public function saveBonusDate($openId){
        $gamedata = M('bonus_date')->where(array('openid' => $openId, 'createdate' => date("Y-m-d")))->find();
        if ($gamedata) {
            M("bonus_date")->where(array('openid' => $openId, 'createdate' => date("Y-m-d")))->setInc('views', 1);
        } else {
            $d['gid'] = $this->gid;
            $d['openid'] = $openId;
            $d['views'] = 1;
            $d['createdate'] = date("Y-m-d");
            M("bonus_date")->add($d);
        }
        //M("bonus_info")->where(array('gid' => $this->gid, 'openid' => $openId))->setInc('joins', 1);
    }

    public function saveBonusViewInfo($gid,$openId){
        M("bonus_info")->where(array('openid' => $openId))->setInc('views', 1);
        $this->cache->redis->incr($this->hashKeyBonusInfo."_view");
    }
    /*
     * 开户
     */
    public function saveBonusInfo($gid,$openId,$nickname,$imageProfile){
        //首先查看此OPENID 是否存在 无论gid
        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
        if(!$bonusInfo){
            //创建个人主页
            $d['gid'] = $gid;
            $d['openid'] = $openId;
            $d['name'] = $nickname;
            $d['headimgurl'] = $imageProfile;
            $d['views'] = 1;
            $this->cache->redis->set($this->hashKeyBonusInfo."_view",1);
            $d['createtime'] = time();
            M("bonus_info")->add($d);
        }

    }
    //保存阅读量历史记录
    public function saveViews($gid,$openId,$index=false){
        //保存阅读记录
        $d['gid'] = $gid;
        $d['type'] = 1;

        if( cookie("user_openid") ){
            $d['from_open_id'] = cookie("user_openid");
        }else{
            $d['from_open_id'] = 'localenv';
        }
        if($index){
            $d['viewpage'] = 1;
        }
        $d['to_open_id'] = $openId;
        M("bonus_list")->add($d);
    }

    public function getCurrentNumber(){
        //四等奖人数
        $mapFour['type'] = 4;
        $mapFour['orderid'] = array('neq','');
        $fourBonusNumber= M('bonus_award')->where($mapFour)->count('id');

        //三等奖人数
        $mapThree['type'] = 3;
        $mapThree['orderid'] = array('neq','');
        $threeBonusNumber= M('bonus_award')->where($mapThree)->count('id');

        //二等奖人数
        $mapSecond['type'] = 2;
        $mapSecond['confirm'] = 1;
        $secondBonusNumber= M('bonus_award')->where($mapSecond)->count('id');

        //一等奖人数
        $mapFirst['type'] = 1;
        $mapFirst['confirm'] = 1;
        $firstBonusNumber= M('bonus_award')->where($mapFirst)->count('id');

        return array($fourBonusNumber,$threeBonusNumber,$secondBonusNumber,$firstBonusNumber);

    }

    //保存拉人品历史记录
    public function saveLaRenPing($gid,$openId,$number){
        //保存阅读记录
        $d['gid'] = $gid;
        $d['type'] = 2;

        if( cookie("user_openid") ){
            $d['from_open_id'] = cookie("user_openid");
        }else{
            $d['from_open_id'] = 'localenv';
        }
        $d['to_open_id'] = $openId;
        $d['number'] = $number;
        M("bonus_list")->add($d);
    }
    public function present(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
        $gid = $_GET['gid'];
        $str = '';
        $nickname = "";
        $voteNumber = 0;
        $imageProfile = "";
        $urlOpenId = false;
        $bonusType = 0;
        $awardPhone = false;
        $myselfopenid = cookie("user_openid");//如果没有 需要弹出页面授权
        $redirect = 0;
        if(isset($_GET['redirect']) && $_GET['redirect']){
            $redirect = $_GET['redirect'];
        }
//        $myselfopenid = null;//如果没有 需要弹出页面授权
        if(isset( $_GET['openid'] ) &&  $_GET['openid']){
            //获取当前的openid
            $openid = $_GET['openid'];//当前人的首页
            //查看此OPENID 是否在表fans中存在
            $infoFromUrlOpenId= M('customer_service_fans')->where(array('openid' => $openid,'token'=>'rggfsk1394161441'))->find();
            if($infoFromUrlOpenId){
                $openIdUrl = "&openid=$openid";
                $urlOpenId = true;
                //判断当前用户是否开过户
                $bonusInfoRedisKey = "bonusinfo_".$openid;
                $this->hashKeyBonusInfo = $bonusInfoRedisKey;
                $bonusInfoName = $this->cache->redis->hget($bonusInfoRedisKey,'name');
                if($bonusInfoName){
                    //已经开过户
                    $bonusType = $this->cache->redis->hget($bonusInfoRedisKey,'bonustype');
                    $imageProfile = $this->cache->redis->hget($bonusInfoRedisKey,'headimgurl');
                    $nickname = $bonusInfoName;
                    $voteNumber = $this->cache->get($this->hashKeyBonusInfo."_vote");
                }else{

                    $bonusInfo = M('bonus_info')->where(array('openid' => $openid))->find();
                    if(!$bonusInfo){
                        //如果当前人ID 存在， 则获取当前人的信息
                        $nickname = $infoFromUrlOpenId['nickname'];
                        $imageProfile = $infoFromUrlOpenId['headimgurl'];
                        $this->saveBonusInfo($gid,$openid,$nickname,$imageProfile);
                        //存储数据到redis
                        $this->cache->redis->hset($bonusInfoRedisKey,'gid',$gid);
                        $this->cache->redis->hset($bonusInfoRedisKey,'name',$nickname);
                        $this->cache->redis->hset($bonusInfoRedisKey,'headimgurl',$imageProfile);
                        $this->cache->redis->hset($bonusInfoRedisKey,'openid',$openid);
                    }else{
                        $nickname = $bonusInfo['name'];
                        $voteNumber = $bonusInfo['vote'];
                        $imageProfile = $bonusInfo['headimgurl'];
                        //存储数据到redis
                        $this->cache->redis->set($bonusInfoRedisKey."_vote",$bonusInfo['vote']);
                        $this->cache->redis->set($bonusInfoRedisKey."_share",$bonusInfo['share']);
                        $this->cache->redis->set($bonusInfoRedisKey."_joins",$bonusInfo['joins']);
                        $this->cache->redis->set($bonusInfoRedisKey."_number",$bonusInfo['number']);
                        $this->cache->redis->set($bonusInfoRedisKey."_view",$bonusInfo['views']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'id',$bonusInfo['id']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'gid',$bonusInfo['gid']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'tel',$bonusInfo['tel']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'name',$bonusInfo['name']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'headimgurl',$bonusInfo['headimgurl']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'openid',$bonusInfo['openid']);
                        $this->cache->redis->hset($bonusInfoRedisKey,'bonustype',$bonusInfo['bonustype']);
                    }
                }

                $this->saveBonusViewInfo($gid,$openid);
                $this->saveViews($gid,$openid);

                //获取URL open id 分数
                $numberByUrlOpenId = M('bonus_info')->where(array('openid' => $openid))->getField('number');

                //保存我也要参加 - 加一
                if(isset( $_GET['joins'] ) &&  $_GET['joins'] == 1){
                    M("bonus_info")->where(array('openid' => $_GET['preopenid']))->setInc('joins', 1);
                    $this->cache->redis->incr($this->hashKeyBonusInfo."_joins");
                }

                //查看获得的奖项
                list($fourAward,$threeAward,$secondAward,$firstAward,$awardPhone) = $this->getAward($openid);
            }else{
                //此人不存在
                $url = $this->url."/index.php?g=Wap&m=Bonus&a=index&gid=$this->gid";
                header("location:$url");
            }


        }else{
            //没有openid 系统有误  重定向到首页
            $url = $this->url."/index.php?g=Wap&m=Bonus&a=index&gid=$this->gid";
            header("location:$url");
        }
        $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $openIdUrl = "&openid=$openid";
        if(isset( $_GET['show'] ) &&  $_GET['show']){
            $myselfopenid = $openid;
        }else{
            if(!$myselfopenid ){
                //self用户不存在 需要重新输入
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                        $web_access_token = '';
                        //重新获取
                        $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                        if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                            //code 有错误 需要重定向
                            $url = $this->url."/index.php?g=Wap&m=Bonus&a=present$openIdUrl&gid=$this->gid";
                            header("location:$url");
                        }
                        $m['id'] = $apidata['id'];
                        $m['web_access_token'] = $userinfoFromApi['access_token'];
                        $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                        M('Diymen_set')->save($m);
                        $web_access_token = $userinfoFromApi['access_token'];
                        $myselfopenid = $userinfoFromApi['openid'];
                        cookie('user_openid', $userinfoFromApi['openid'], 315360000);
                        /**
                         * $userinfo
                         * (
                        [access_token] => OezXcEiiBSKSxW0eoylIeIBDFlSdQvVjGi6djtyA0hoTzn6QsnB97jvbYipE6P3cILGN4uV6t_i_Kri7t5p4qpiC0DDbojA1Nr0U1OIZHt4y2Xmwrdz1EJ8TybUEwbEKXbc_pWX85Tv6mzPgmk9IKw
                        [expires_in] => 7200
                        [refresh_token] => OezXcEiiBSKSxW0eoylIeIBDFlSdQvVjGi6djtyA0hoTzn6QsnB97jvbYipE6P3cjBcKADJNNNUNLEUqTkrqX_TK6Tc-SzJpfb9MR9Nojab9QRstwo9mSX5BgwYjWe5JAeD-yLHj1rBpkMdYjlTXvQ
                        [openid] => oYkdqs5s1IEIhB9bulM2AJ6GgZh8
                        [scope] => snsapi_userinfo
                        )
                         */
                        //根据access_token 拉到用户基本信息
                        $gUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$web_access_token.'&openid='.$myselfopenid.'&lang=zh_CN';
                        $json = json_decode($this->curlGet($gUrl));
                        /*$json 用户信息
                         * (
                            [openid] => oYkdqs5s1IEIhB9bulM2AJ6GgZh8
                            [nickname] => 慢慢羊
                            [sex] => 2
                            [language] => zh_CN
                            [city] =>
                            [province] =>
                            [country] => 埃及
                            [headimgurl] => http://wx.qlogo.cn/mmopen/ib5dhAucxDgklImqj0d3PDdYjaR0jYjibhGR6JVGS0mGoqPkFIqweib7UIb4amYYDo0NOzvy4pqOShD1FlJib4Zc8gibQpCzP6eU8/0
                            [privilege] => Array
                                (
                                )

                        )
                         */

                        if(!$nickname){
                            $nickname = $json->nickname;
                        }

                        if($myselfopenid == $openid){
                            $imageProfile = $json->headimgurl;
                        }
                        $this->saveUserInfo($json);

                } else {
                    $url = urlencode($this->url."/index.php?g=Wap&m=Bonus&a=present$openIdUrl&gid=$this->gid");
                    header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                    exit;
                }
            }else{
                //$myselfopenid 存在 但是在表fans 中不存在
                $fansInfo = M('customer_service_fans')->where(array('openid' => $myselfopenid,'token'=>'rggfsk1394161441'))->find();
                if(!$fansInfo){
                    //需要重新获取用户信息
                    $code = trim($_GET["code"]);
                    $state = trim($_GET['state']);
                    if ($code && $state == 'sentian') {
                        $webCreatetime = $apidata['web_createtime'];
                        $web_access_token = '';

                        if($webCreatetime>(time()-7200) && $myselfopenid){
                            //未过期
                            $web_access_token = $apidata['web_access_token'];
                        }else if($webCreatetime<=(time()-7200) && $myselfopenid && isset($apidata['refresh_token']) && ($apidata['refresh_token_createtime']>(time()-7*3600*24))  ){
                            //从新获取通过
                            $urlRefreshToken = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.
                                $apidata['appid'].'&grant_type=refresh_token&refresh_token='.$apidata['refresh_token'];
                            $jsonRefresh = json_decode($this->curlGet($urlRefreshToken));
                            $web_access_token = $jsonRefresh->access_token;
                            $refresh_token = $jsonRefresh->refresh_token;
                            $m['id'] = $apidata['id'];
                            $m['web_access_token'] = $web_access_token;
                            $m['refresh_token'] =$refresh_token;
                            $m['web_createtime'] = time();
                            $m['refresh_token_createtime'] = time();
                            M('Diymen_set')->save($m);
                        }else{
                            //重新获取
                            $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                            if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                                //code 有错误 需要重定向
                                $url = $this->url."/index.php?g=Wap&m=Bonus&a=present$openIdUrl&gid=$this->gid";
                                header("location:$url");
                            }
                            $m['id'] = $apidata['id'];
                            $m['web_access_token'] = $userinfoFromApi['access_token'];
                            $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                            M('Diymen_set')->where(array('id' => $apidata['id']))->save($m);
                            $web_access_token = $userinfoFromApi['access_token'];
                            $myselfopenid = $userinfoFromApi['openid'];
                            cookie('user_openid', $userinfoFromApi['openid'], 315360000);
                        }
                        $gUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$web_access_token.'&openid='.$myselfopenid.'&lang=zh_CN';
                        $json = json_decode($this->curlGet($gUrl));
                        if(!$nickname){
                            $nickname = $json->nickname;
                        }

                        if($myselfopenid == $openid){
                            $imageProfile = $json->headimgurl;
                        }
                        $this->saveUserInfo($json);
                    }else{
                        $url = urlencode($this->url."/index.php?g=Wap&m=Bonus&a=present$openIdUrl&gid=$this->gid");
                        header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                        exit;
                    }
                }
            }
            $accessToken = $apidata['access_token'];
            $ticket = $apidata['ticket'];
            if($apidata['access_token']==null || (time()-1*$apidata['create_time']>7200)){
                //需要重新获取access_token
                $json = $this->getAccessToken($apidata['appid'],$apidata['appsecret']);
                $fdata['id'] = $apidata['id'];
                $fdata['token'] = 'rggfsk1394161441';
                $fdata['access_token'] = $json['access_token'];
                $accessToken = $fdata['access_token'];
                $fdata['create_time'] = time();
                M('Diymen_set')->save($fdata);
            }
            if($apidata['ticket']==null || (time()-1*$apidata['ticket_time']>7200)){
                $token = $accessToken;
                $jsonTicket = $this->getJsTicket($token);
                if($jsonTicket['errmsg'] == 'ok'){
                    $fdata['id'] = $apidata['id'];
                    $fdata['token'] = 'rggfsk1394161441';
                    $fdata['ticket'] = $jsonTicket['ticket'];
                    $ticket = $fdata['ticket'];
                    $fdata['ticket_time'] = time();
                    M('Diymen_set')->save($fdata);
                }
            }
        }


        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
//        $timestamp = '1414587457';
//        $ticket = 'sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qg';
//        $url = "http://". $this->_server('HTTP_HOST');
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("url",$url);
        $this->assign("siteurl",$this->url);
        $this->assign("gid",$gid);
        $this->assign("openid",$openid);
        $this->assign("tel",$myselfopenid);
        $this->assign("nickname",$nickname);

        $titleArr = $this->titleInWeixin;
        $titleUsed = $nickname;
        $titleUsed .= " ".$titleArr[rand(0,count($titleArr)-1)];
        $bonusDes = $this->getTitleAndImageByGid($gid);
        $this->assign('title',$nickname.":".$bonusDes['desc']);
        $this->assign('bonusdesc','');
        $this->assign("imageUrl",$bonusDes['img']);
        $this->assign("shareimageurl",$bonusDes['img']);
//        $this->assign("title",$titleUsed);
//        $this->assign("imageUrl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/bonus/images/profile1.png');

        //如果是当前用户 与  UEL OPEN ID 一致  获取 积分历史记录
        $history = null;
        $isSelf = false;
        $historyCount = 0;
        if($myselfopenid == $openid){
            $isSelf  = true;
            $historyArr = array();
            $idForHistoryArr = $this->hashKeyBonusInfo."_history";
            $historyArrLen = $this->cache->redis->llen($idForHistoryArr);
            //首先查看此historyArr是否存在缓存中
            if($historyArrLen<= 0){
                //缓存不存在 需要PUSH
                $history = M('bonus_history')->where(array('openid' => $openid))->order('createtime desc')->select();
                $historyCount = count($history);
                $m = 0;
                foreach($history as $each){
                    $nickNameTmp = '';
                    $imageTmp = '';
                    $tmpArr = array();
                    $tmpArr['id'] = $each['id'];
                    $tmpArr['description'] = $each['description'];
                    if($each['from_open_id']){
                        $tmpArr['from_open_id'] = $each['from_open_id'];
                        //根据openid获取昵称和头像
                        $fansInfo = M('customer_service_fans')->where(array('openid' => $each['from_open_id'],'token'=>'rggfsk1394161441'))->find();
                        $nickNameTmp = $fansInfo['nickname'];
                        $imageTmp = $fansInfo['headimgurl'];
                    }else{
                        $nickNameTmp = $nickname;
                        $imageTmp = $imageProfile;
                    }
                    $tmpArr['nickname'] = $nickNameTmp;
                    $tmpArr['headimgurl'] = $imageTmp;
                    $tmpArr['number'] = $each['number'];
                    $tmpArr['createtime'] = $each['createtime'];
                    if($m < 10){
                        $historyArr[] = $tmpArr;
                    }
                    $this->cache->rpush($idForHistoryArr,$tmpArr);
                    $m++;
                }
            }else{
                $historyArr = $this->cache->lrange($idForHistoryArr,0,9);
                $historyCount = $this->cache->redis->llen($idForHistoryArr);
            }

        }
        $this->assign("imageprofile",$imageProfile);
        $this->assign("history",$historyArr);
        $this->assign("historycount",$historyCount);
        $this->assign("isself",$isSelf);
        $this->assign("number",$numberByUrlOpenId);
        $this->assign("bonusType",$bonusType);
        $this->assign("vote",$voteNumber);
        $buttonDisable = '';
        $buttonDisableForFirst = '';
        $buttonDisableForSecond = '';
        if($bonusType == 2){
            $buttonDisableForFirst = "btn-disabled";
        }
        if($bonusType == 1){
            $buttonDisableForSecond = "btn-disabled";
        }
        $this->assign("buttondisable",$buttonDisable);
        $this->assign("buttonDisableForFirst",$buttonDisableForFirst);
        $this->assign("buttonDisableForSecond",$buttonDisableForSecond);
        //显示需要的score
        $scoreList = array_keys($this->configBonus);
        array_unshift($scoreList,$this->fiveScore);
        $this->assign("scoreList",$scoreList);

        $height = $this->getHeightDiv($numberByUrlOpenId,$scoreList);
        $this->assign("height",$height);

        //已经领奖信息
        $this->assign("fouraward",$fourAward);
        $this->assign("threeaward",$threeAward);
        $this->assign("secondaward",$secondAward);
        $this->assign("firstaward",$firstAward);

        //查看是否已经加过油
        $voteInfo = false;
        if( ($myselfopenid != $openid) && $this->checkVote($openid,$myselfopenid,$gid)){
            $voteInfo = true;
        }
        $this->assign("voteinfo",$voteInfo);

        //获得三四等奖的手机号
        $this->assign("awardphone",$awardPhone);

        //重定向页面
        if($redirect == 3){
            header('Location: http://mp.weixin.qq.com/s?__biz=MzA4Mjk5OTYxNQ==&mid=204136724&idx=1&sn=3528d785ae3d1f179339d8bfb8ad2606&key=79cf83ea5128c3e52dd08bb48805d5d9a414a6ed0c95b948fc15a3e0c1da2f54735493dec4987c77fc02bf29425103a7&ascene=1&uin=MTU2NjU5Mjc2Mw%3D%3D&devicetype=webwx&version=70000001&pass_ticket=bYdmw9sWe7e4D8EzqjZcxyDcK9ctRYxCKuoE6bnnzDYuJLbCWxaKt5Xpe4OBZvgh');
        }else if($redirect == 4){
            header('Location: http://mp.weixin.qq.com/s?__biz=MzA4Mjk5OTYxNQ==&mid=204135881&idx=1&sn=9de48901e372517b6df47567eaf12db7&key=79cf83ea5128c3e5ebc3820644240638ac8d1d1fc0973441774626ae76b54bb5c99c5aec5a344b4033e3f6ce0560cb6d&ascene=1&uin=MTU2NjU5Mjc2Mw%3D%3D&devicetype=webwx&version=70000001&pass_ticket=bYdmw9sWe7e4D8EzqjZcxyDcK9ctRYxCKuoE6bnnzDYuJLbCWxaKt5Xpe4OBZvgh');
        }else if($redirect == 2 || $redirect == 1 ){
            header('Location: http://mp.weixin.qq.com/s?__biz=MzA4Mjk5OTYxNQ==&mid=204136816&idx=1&sn=c303154d60fa85db0e02e5c34630b4b8&key=79cf83ea5128c3e5030c6f84deb44928b238a8430d75db85b3fb163d3e824ee6b2123137cb9b7496262f71bd726d898f&ascene=1&uin=MTU2NjU5Mjc2Mw%3D%3D&devicetype=webwx&version=70000001&pass_ticket=bYdmw9sWe7e4D8EzqjZcxyDcK9ctRYxCKuoE6bnnzDYuJLbCWxaKt5Xpe4OBZvgh');
        }

        $this->assign("redirect",$redirect);

        //奖品个数
        list($fourBonusNumber,$threeBonusNumber,$secondBonusNumber,$firstBonusNumber) = $this->getCurrentNumber();
        $this->assign("fourleft",$this->fourLevelNumber - $fourBonusNumber*1);
        $this->assign("threeleft",$this->threeLevelNumber - $threeBonusNumber*1);
        $this->assign("secondleft",$this->secondLevelNumber - $secondBonusNumber*1);
        $this->assign("firstleft",$this->firstLevelNumber - $firstBonusNumber*1);

        $this->display();
    }
    //查看用户是否已经加过油
    public function checkVote($openId,$fromOpenId,$gid){
        $bonusHistory = M('bonus_history')->where(array('openid' => $openId,'from_open_id'=>$fromOpenId))->field('id')->find();
        return $bonusHistory;
    }
    //查看获得的奖项
    public function getAward($openId,$gid=null){
        $fourAward = false;
        $threeAward = false;
        $phone = false;
        $secondAward = false;
        $firstAward = false;
        $awardList = array();
        $res = M('bonus_award')->where(array(  'openid' => $openId ))->field('type,telephone')->select();
        if($res){
            foreach($res as $each){
                if($each['type'] == 2 && $each['confirm'] == 1){
                    $awardList[] = $each['type'];
                }else if($each['type'] == 1 && $each['confirm'] == 1){
                    $awardList[] = $each['type'];
                }else if($each['type'] == 3){
                    $awardList[] = $each['type'];
                }else if($each['type'] == 4){
                    $awardList[] = $each['type'];
                }

                if( $each['type'] == 4){
                    $phone = $each['telephone'];
                }
                if( !$phone && $each['type'] == 3){
                    $phone = $each['telephone'];
                }
                if( !$phone && $each['type'] == 2){
                    $phone = $each['telephone'];
                }
                if( !$phone && $each['type'] == 1){
                    $phone = $each['telephone'];
                }
            }
            if(in_array(4,$awardList)){
                //四等奖已经领奖
                $fourAward = true;
            }
            if(in_array(3,$awardList)){
                //四等奖已经领奖
                $threeAward = true;
            }
            if(in_array(2,$awardList)){
                //四等奖已经领奖
                $secondAward = true;
            }
            if(in_array(1,$awardList)){
                //四等奖已经领奖
                $firstAward = true;
            }
        }
        return array($fourAward,$threeAward,$secondAward,$firstAward,$phone);
    }

    public function getHeightDiv($numberByUrlOpenId,$scoreList){
        //array(5) { [0] => int(50) [1] => int(300) [2] => int(500) [3] => int(800) [4] => int(1000) }
        $height = 0;
        if($numberByUrlOpenId > $scoreList[0] && $numberByUrlOpenId <=$scoreList[1]){
            //例如 200
            $height = ($numberByUrlOpenId-$scoreList[0])/($scoreList[1] - $scoreList[0]) * 69;
        }else if($numberByUrlOpenId > $scoreList[1] && $numberByUrlOpenId <=$scoreList[2]){
            // 350
            $height = ($numberByUrlOpenId-$scoreList[1])/($scoreList[2] - $scoreList[1]) * 69;
        }else if($numberByUrlOpenId > $scoreList[2] && $numberByUrlOpenId <= $scoreList[3]){
            //550
            $height = ($numberByUrlOpenId-$scoreList[2])/($scoreList[3] - $scoreList[2]) * 83;
        }else if($numberByUrlOpenId > $scoreList[3] && $numberByUrlOpenId <=$scoreList[4]){
            //880
            $height = ($numberByUrlOpenId-$scoreList[3])/($scoreList[4] - $scoreList[3]) * 95;
        }else if($numberByUrlOpenId > $scoreList[4]){
            //1200
            $height = ($numberByUrlOpenId-$scoreList[3])/($scoreList[4] - $scoreList[3]) * 95;
        }
        return $height;
    }
    public function showAllHistory(){
        $openId = $_GET['openid'];
        $gId = $_GET['gid'];
        $historyArr = array();
        $hashKeyBonusInfo = "bonusinfo_".$openId;
        $idForHistoryArr = $hashKeyBonusInfo."_history";
        $historyArrLen = $this->cache->redis->llen($idForHistoryArr);
        //首先查看此historyArr是否存在缓存中
        if($historyArrLen<= 0){
            //缓存不存在
            $history = M('bonus_history')->where(array('openid' => $openId))->select();
            $historyCount = count($history);
            $m = 0;
            foreach($history as $each){
                $nickNameTmp = '';
                $imageTmp = '';
                $tmpArr = array();
                $tmpArr['id'] = $each['id'];
                $tmpArr['description'] = $each['description'];
                if($each['from_open_id']){
                    $tmpArr['from_open_id'] = $each['from_open_id'];
                    //根据openid获取昵称和头像
                    $fansInfo = M('customer_service_fans')->where(array('openid' => $each['from_open_id'],'token'=>'rggfsk1394161441'))->find();
                    $nickNameTmp = $fansInfo['nickname'];
                    $imageTmp = $fansInfo['headimgurl'];
                }
                $tmpArr['nickname'] = $nickNameTmp;
                $tmpArr['headimgurl'] = $imageTmp;
                $tmpArr['number'] = $each['number'];
                $tmpArr['createtime'] = $each['createtime'];
                if($m >= 10){
                    $historyArr[] = $tmpArr;
                }
                $m++;
                $this->cache->lpush($idForHistoryArr,$tmpArr);
            }
        }else{
            $historyArr = $this->cache->lrange($idForHistoryArr,10,-1);
        }

        echo json_encode($historyArr);
    }
    /**
     * 保存二等奖的用户信息
     */
    /**
     * 领取奖品
     */
    public function saveAwardInfo() {
        $r = 0;
        $gid = $_REQUEST['gid'];
        $openId = $_REQUEST['openid'];
        $type =2;
        $bonusInfoRedisKey = "bonusinfo_".$openId;
        if(isset($_REQUEST['type']) && $_REQUEST['type']){
            $type = $_REQUEST['type']*1;
        }
        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
        $vote = $bonusInfo['vote'];
        $views = $bonusInfo['views'];
        $number = $bonusInfo['number'];
        if($views < $vote){
            //非法数据
            $r=9;
            return $r;
        }else if(($type==2 && $number<1000)){//($type==2 && $vote<1000) ||
            return 9;
        }else if(($type==1 && $number<2000)){//$type==1 && $vote<2000||
            return 9;
        }

        $telephone = (isset($_REQUEST['telephone']) && $_REQUEST['telephone'])?$_REQUEST['telephone']:'';
        $username = (isset($_REQUEST['username']) && $_REQUEST['username'])?$_REQUEST['username']:'' ;
        $address = (isset($_REQUEST['address']) && $_REQUEST['address'])?$_REQUEST['address']:''   ;
        $province = (isset($_REQUEST['province']) && $_REQUEST['province'])?$_REQUEST['province']:''    ;

        //判断此telephone 和 type 是否存在系统中
        $data = M('bonus_award')->where(array('telephone' =>$telephone,'type' => $type))->find();
        if($data){
            $r = 10;
        }else{
            switch($type){
                case 2:
                    //需要查看此次IPAD 是否还有
                    $map['type']  = array('eq',2);
                    $map['confirm']  = array('eq',1);
                    $numberForSecond= M('bonus_award')->where($map)->count('id');
                    if($numberForSecond >= $this->secondLevelNumber){
                        //IPAD已经被抢光
                        $r = 3;
                    }else{
                        $bonusAwardId = M('bonus_award')->where(array('openid' => $openId,'type' => 2))->getField('id');
                        if($bonusAwardId){
                            //则更新
                            $d['id'] = $bonusAwardId;
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['username'] = $username;
                            $d['address'] = $address;
                            $d['province'] = $province;
                            $d['updatetime'] = time();
                            $d['type'] = 2;
                            $d['confirm'] = 1;
                            $r = M('bonus_award')->save($d);
                        }else{
                            //插入数据
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['username'] = $username;
                            $d['address'] = $address;
                            $d['province'] = $province;
                            $d['type'] = 2;
                            $d['confirm'] = 1;
                            $d['createtime'] = time();
                            $d['updatetime'] = time();
                            $r = M('bonus_award')->add($d);
                        }
                        //此用户是中奖用户，需要清空此用户的分数 活动结束
                        $bonusInfo = M('bonus_info')->where(array( 'openid' => $openId))->field('id,bonustype')->find();
                        if($bonusInfo['bonustype'] == 0 || $bonusInfo['bonustype'] >2){
                            $h['bonustype'] = 2;
                            $h['id'] = $bonusInfo['id'];
                            M('bonus_info')->save($h);
                            $this->cache->redis->hset($bonusInfoRedisKey,'bonustype',2);
                        }
                        $r = 2;
                    }
                    break;
                case 1:
                    $map['type']  = array('eq',1);
                    $map['confirm']  = array('eq',1);
                    $numberForSecond= M('bonus_award')->where($map)->count('id');
                    if($numberForSecond >= $this->firstLevelNumber){
                        //IPHONE已经被抢光
                        $r = 3;
                    }else{
                        $bonusAwardId = M('bonus_award')->where(array('openid' => $openId,'type' => 1))->getField('id');
                        if($bonusAwardId){
                            //则更新
                            $d['id'] = $bonusAwardId;
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['username'] = $username;
                            $d['address'] = $address;
                            $d['province'] = $province;
                            $d['updatetime'] = time();
                            $d['type'] = 1;
                            $d['confirm'] = 1;
                            $r = M('bonus_award')->save($d);
                        }else{
                            //插入数据
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['username'] = $username;
                            $d['address'] = $address;
                            $d['province'] = $province;
                            $d['type'] = 1;
                            $d['createtime'] = time();
                            $d['updatetime'] = time();
                            $d['confirm'] = 1;
                            $r = M('bonus_award')->add($d);
                        }
                        //此用户是中奖用户，需要清空此用户的分数 活动结束
                        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->field('id,bonustype')->find();
                        if($bonusInfo['bonustype'] == 0 || $bonusInfo['bonustype'] >1){
                            $h['bonustype'] = 1;
                            $h['id'] = $bonusInfo['id'];
                            M('bonus_info')->save($h);
                            $this->cache->redis->hset($bonusInfoRedisKey,'bonustype',1);
                        }
                        $r = 2;
                    }
                    break;
                case 4:
                    $map['type']  = array('eq',4);
                    $numberForSecond= M('bonus_award')->where($map)->count('id');
                    if($numberForSecond >= $this->fourLevelNumber){
                        //四等奖已经被抢光
                        $r = 3;
                    }else{
                        $bonusAwardId = M('bonus_award')->where(array( 'openid' => $openId,'type' => 4))->getField('id');
                        if($bonusAwardId){
                            //则更新
                            $d['id'] = $bonusAwardId;
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['updatetime'] = time();
                            $d['type'] = 4;
                            $r = M('bonus_award')->save($d);
                        }else{
                            //插入数据
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['type'] = 4;
                            $d['createtime'] = time();
                            $d['updatetime'] = time();
                            $r = M('bonus_award')->add($d);
                        }
                        //此用户是中奖用户，需要清空此用户的分数 活动结束
//                        $bonusInfoId = M('bonus_info')->where(array('gid' => $gid, 'openid' => $openId))->getField('id');
                        $bonusInfo = M('bonus_info')->where(array( 'openid' => $openId))->field('id,bonustype')->find();
                        if($bonusInfo['bonustype'] == 0 || $bonusInfo['bonustype'] >4){
                            $h['bonustype'] = 4;
                            $h['id'] = $bonusInfo['id'];
                            M('bonus_info')->save($h);
                            $this->cache->redis->hset($bonusInfoRedisKey,'bonustype',4);
                        }
                        $r = 2;
                    }

                    break;
                case 3:
                    $map['type']  = array('eq',3);
                    $numberForSecond= M('bonus_award')->where($map)->count('id');
                    if($numberForSecond >= $this->fourLevelNumber){
                        //四等奖已经被抢光
                        $r = 3;
                    }else{
                        $bonusAwardId = M('bonus_award')->where(array('openid' => $openId,'type' => 3))->getField('id');
                        if($bonusAwardId){
                            //则更新
                            $d['id'] = $bonusAwardId;
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['updatetime'] = time();
                            $d['type'] = 3;
                            $r = M('bonus_award')->save($d);
                        }else{
                            //插入数据
                            $d['openid'] = $openId;
                            $d['gid'] = $gid;
                            $d['telephone'] = $telephone;
                            $d['type'] = 3;
                            $d['createtime'] = time();
                            $d['updatetime'] = time();
                            $r = M('bonus_award')->add($d);
                        }
                        //此用户是中奖用户，需要清空此用户的分数 活动结束
                        $bonusInfo = M('bonus_info')->where(array( 'openid' => $openId))->field('id,bonustype')->find();
                        if($bonusInfo['bonustype'] == 0 || $bonusInfo['bonustype'] >3){
                            $h['bonustype'] = 3;
                            $h['id'] = $bonusInfo['id'];
                            M('bonus_info')->save($h);
                            $this->cache->redis->hset($bonusInfoRedisKey,'bonustype',3);
                        }
                        $r = 2;
                    }

                    break;

            }
        }

        echo  $r;
    }
    public function submitTelephone(){
        $openId = $_GET['openid'];
        $phone = $_GET['phone'];
        $gid = $_GET['gid'];

        $id = M('bonus_info')->where(array('openid' => $openId))->getField('id');

        $d['id'] = $id;
        $d['openid'] = $openId;
        $d['tel'] = $phone;
        $d['gid'] = $gid;
        $d['updatetime'] = time();
        $r = M("bonus_info")->save($d);
        echo $r;
    }

    /**
     * @return 当前网页的URL
     */
    function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
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
    public function curlGet($url, $method = 'get', $data = '') {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
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
    public function getAccessToken($appid,$appsecret){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo;
    }

    public function getJsTicket($token){
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo;
    }
    public function pull(){
        echo '<pre>';

// Outputs all the result of shellcommand "ls", and returns
// the last output line into $last_line. Stores the return value
// of the shell command in $retval.
        $last_line = system('pullsentian', $retval);

// Printing additional info
        echo  $retval;
    }

    public function test(){
//        echo 1111111111111;
        $openId = 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';//davis
        $gid = 6;
        //generate test data
        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
        //随机生成分数
        echo $bonusInfo['number']."<br/>";
        $number =  $this->getNumberByOpenId($gid,$openId,$bonusInfo);
        M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('vote', 1);
        //给URL OPEN ID 加分
        M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('number', $number);
        Log :: write($number);
        echo $number;
    }
}
