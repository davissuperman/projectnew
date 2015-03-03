<?php

class WomensdayAction extends BonusAction {


    public $cache;
    public $titleInWeixin = '森田药妆-38女人节特别奉献';



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
       // $this->cache = Cache::getInstance('Redis',array('host'=>'127.0.0.1','expire'=>1296000));
    }

    public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $gid = 1;
        //统计end

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
                                $url = $this->url."/index.php?g=Wap&m=womensday&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=womensday&a=index");
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


        $this->assign('title',$selfUserInfo['nickname']."38女人节特别奉献!");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);

        $url = "http://". $this->_server('HTTP_HOST');
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        //$userOpenId 是否在表womensday中存在 不存在则创建模板
        $this->saveInfo($userOpenId);
        $this->assign("siteurl",$this->url);

        $this->display();
    }
    /*
     * 开户
     */
    public function saveInfo($openId){
        //首先查看此OPENID 是否存在 无论gid
        $bonusInfo = M('womensday')->where(array('openid' => $openId))->find();
        if(!$bonusInfo){
            //创建个人主页
            $d['openid'] = $openId;
            $d['item1'] = 0;
            $d['item2'] = 0;
            $d['item3'] = 0;
            $d['item4'] = 0;
            $d['clicksum'] = 4;
            $d['createtime'] = time();
            M("womensday")->add($d);
        }

    }
    public function getReward(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if ($_GET['show']) {
            $userOpenId= $_GET['openid'];
            $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();

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
                                $url = $this->url."/index.php?g=Wap&m=womensday&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=womensday&a=index");
                    header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                    exit;
                }
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


        $this->assign('title',$selfUserInfo['nickname']."38女人节特别奉献!");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);

        $this->assign("siteurl",$this->url);

        //$userOpenId在表womensday中是否存在 如果不存在则是第一次参见 创建模板


        $this->display();
    }
    public function page2() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $this->display();
    }
    public function page3() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $this->display();
    }
    public function page4() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $this->display();
    }
    public function page5() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $this->display();
    }
    public function page6() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        //统计添加浏览数和浏览记录 tel 相当与open_id
        $userOpenId= cookie('user_openid');
        if(!$userOpenId)
            $userOpenId = 'localenv';

        $this->display();
    }


    function saveIdInfo($id){
        M("greeting")->where(array('id' => $id))->setInc('view', 1);
    }

    public function saveShareInfo(){
//        $type = $_GET['type'];
        $userOpenId= cookie('user_openid');
        $a = M("greeting")->where(array('openid' => $userOpenId))->setInc('share', 1);
        if($a){
            echo 1;
        }else{
            echo 0;
        }
//        $m['type'] = $type;
//        if(!$userOpenId){
//            $userOpenId = "local";
//        }
//        $m['openid'] = $userOpenId;
//        $fanModel =  M('greeting_data');
//        $fanModel->add($m);
    }

    function getIdByOpenId($openId){
        $greetingId = M('greeting')->where(array('openid' => $openId))->getField('id');
        if($greetingId){
            return $greetingId;
        }else{
            //插入数据 取得id
            $m['openid'] = $openId;
         //   $lastInsId = M('greeting')->add($m);
            return $lastInsId;
        }
    }

    function show(){
        $userOpenId= cookie('user_openid');
        $gid =1;
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
                                $url = $this->url."/index.php?g=Wap&m=Greeting&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=Greeting&a=index");
                    header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                    exit;
                }
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


        $this->assign('gid', $gid);
        $this->assign('openid', $userOpenId);
        $this->assign('userinfo', $selfUserInfo);
        $this->assign('nickname',  $selfUserInfo['nickname']);
        $titleArr = $this->titleInWeixin;
        $titleUsed = $selfUserInfo['nickname'];
        $titleUsed .= " ".$titleArr[rand(0,count($titleArr)-1)];

        $bonusDes = $this->getTitleAndImageByGid(1);
        $this->assign('title',$selfUserInfo['nickname']."给您拜年啦!");
        $this->assign('bonusdesc','');
        $this->assign("imageUrl",$bonusDes['img']);
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/year/images/logo1.jpg";
        $this->assign("shareimage",$img);
        $recName = $_POST['rec-name'];
        $sendName = $_POST['send-name'];
        $this->assign("recName",$recName);
        $this->assign("sendName",$sendName);
        //保存记录
        if(!$userOpenId){
            $userOpenId = 'localenv';
        }
        $cardArr['openid'] = $userOpenId;
        $cardArr['fromname'] = $sendName;
        $cardArr['toname'] = $recName;
        M('greeting_card')->add($cardArr);
        M("greeting")->where(array('openid' =>$userOpenId))->setInc('joins', 1);
        $url = "http://". $this->_server('HTTP_HOST');
        //根据openid获取ID
        $id = $this->getIdByOpenId($userOpenId);
        $shareUrl = "http://". $this->_server('HTTP_HOST')."/index.php?g=Wap&m=Greeting&a=index&t=$recName&f=$sendName&id=$id";
        $this->assign("url",$shareUrl);


        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign("siteurl",$this->url);
        $this->display();
    }

    public function inputdata(){
        $url = "http://". $this->_server('HTTP_HOST');
        $postUrl = $url."/index.php?g=Wap&m=Greeting&a=show";
        $this->assign("posturl",$postUrl);
        $this->display();
    }

    public function saveData(){
        $type = $_GET['type'];
        $userOpenId= cookie('user_openid');
        $m['type'] = $type;
        if(!$userOpenId){
            $userOpenId = "local";
        }
        $m['openid'] = $userOpenId;
        $fanModel =  M('greeting_data');
        $fanModel->add($m);
        $greeting = M('greeting')->where(array('openid' => $userOpenId))->select();
        if($greeting){
            if($type == 1){
                M("greeting")->where(array('openid' =>$userOpenId))->setInc('accept', 1);
            }else if($type == 2){
                M("greeting")->where(array('openid' =>$userOpenId))->setInc('subscribe', 1);
            }else if($type == 3){
                M("greeting")->where(array('openid' =>$userOpenId))->setInc('wantcard', 1);
            }
        }else{
            //添加
            $n['openid'] = $userOpenId;
            if($type == 1){
                $n['accept'] = 1;;
            }else if($type == 2){
                $n['wantcard'] = 1;;
            }else if($type == 3){
                $n['subscribe'] = 1;;
            }
            M('greeting')->add($n);
        }

    }
    public function getTitleAndImageByGid($gid){
        $condition['gid'] = $gid;
        $bonusDes = M('bonus')->where($condition)->field('title,img,desc')->find();
        return $bonusDes;
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

}
