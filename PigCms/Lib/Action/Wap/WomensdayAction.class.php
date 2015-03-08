<?php

class WomensdayAction extends BonusAction {


    public $cache;
    public $titleInWeixin = '森田药妆-38女人节特别奉献';
    public $url;


    public function _initialize() {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'static');
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
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

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
            $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
                //测试的时候使用
            //如果存在 则不需要再通过weixin API调取用户信息
            $selfUserInfo['headimgurl'] = $fansInfo['headimgurl'];
            $selfUserInfo['nickname'] = $fansInfo['nickname'];
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
/*
 * else if($webCreatetime<=(time()-7200) && $userOpenId && isset($apidata['refresh_token']) && ($apidata['refresh_token_createtime']>(time()-7*3600*24))  ){
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
                        }
 */
                        if($webCreatetime>(time()-7200) && $userOpenId){
                            //未过期
                            $web_access_token = $apidata['web_access_token'];
                        }else{
                            //重新获取
                            $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                            if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                                //code 有错误 需要重定向
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                        $json = json_decode($this->curlGetV2($gUrl));
                        $this->saveUserInfo($json);
                        $selfUserInfo['headimgurl'] = $json->headimgurl;
                        $selfUserInfo['nickname'] = $json->nickname;
                    }
                } else {
                    $url = urlencode($this->url."/index.php?g=Wap&m=Womensday&a=index");
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


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);

        $url = "http://". $this->_server('HTTP_HOST');
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");
        $this->assign('shareurl',  $this->get_url());

        //$userOpenId 是否在表womensday中存在 不存在则创建模板
        $this->saveInfo($userOpenId);
        $this->assign("siteurl",$this->url);
        //查看是否收集到4中元素
        $itemInfo = M('womensday')->where(array('openid' => $userOpenId))->find();
        $totalItem = false;
        if($itemInfo){
            if($itemInfo['item1'] && $itemInfo['item2'] && $itemInfo['item3'] && $itemInfo['item4']){
                $totalItem = true;
            }
        }
        $this->assign('totalitem',  $totalItem);

        //是否提交过个人信息
        $infoAward = M('womensday_award')->where(array('openid' => $userOpenId))->find();
        $submitTelephone = false;
        if($infoAward){
            $submitTelephone = true;
        }
        $this->assign('submitphone',  $submitTelephone);
        //今天次数是否用完
        $leftNum = $this->getLeftNumber($userOpenId);
        $this->assign('leftnum',  $leftNum);
        if($userOpenId){
            $this->savePageViews($itemInfo['id']);
        }
        $this->display();
    }

    public function savePageViews($id){
        M("womensday")->where(array('id' =>$id))->setInc('views', 1);
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
            $d['clicksum'] = 0;
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

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
            
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=Womensday&a=index");
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


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");
        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url = "http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        //判断今天的点击数 是否还有剩余
        //获取今天的开始 和 结束时间
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
        $numberForSecond= M('womensday_list')->where("`createtime` >= '$start' and `createtime` <= '$end' and `openid`= '$userOpenId'")->count('id');
        $item = 0;
        $totalNumber = 4;
        //判断是否有分享
        $numberShare= M('womensday_share')->where("`sharetime` >= '$start' and `sharetime` <= '$end' and `openid`= '$userOpenId'")->count('id');
        if($numberShare){
            $totalNumber = 5;
        }
        $hasOportunity = false;
        if($numberForSecond >= $totalNumber){
            //当天已经没有机会

        }else{
            $hasOportunity = true;
            $left = $totalNumber - $numberForSecond -1;
            //插入记录
            $this->saveList($userOpenId,$numberForSecond);
            //总点击数加1
            M("womensday")->where(array('id' => $info['id']))->setInc('clicksum', 1);

            //取得的东西
            $clickCount = $info['clicksum']*1 + 1;
            if($clickCount == 1){
                $item = 1;
            }else if($clickCount == 2){
                $item = 2;
            }else if($clickCount == 3){
                $item = 3;
            }else if($clickCount == 4){
                $item = rand(1,3);
            }else if($clickCount == 5){
                $item = 4;
            }
            //根据item更新个数
            M("womensday")->where(array('id' => $info['id']))->setInc('item'.$item, 1);
        }
        if($item){
            //根据item 取得元素
            $itemInfo = M('womensday_item')->where(array('id' => $item))->find();
            $this->assign('itemname',$itemInfo['des']);
            $this->assign('itemsrc',$itemInfo['src']);
        }
        $this->assign('oportunity',$hasOportunity);
        $this->assign('left',$left);

        $itemInfo = M('womensday')->where(array('openid' => $userOpenId))->find();
        $totalItem = false;
        if($itemInfo){
            if($itemInfo['item1'] && $itemInfo['item2'] && $itemInfo['item3'] && $itemInfo['item4']){
                $totalItem = true;
            }
        }
        $this->assign('totalitem',$totalItem);
        if($userOpenId){
            $this->savePageViews($itemInfo['id']);
        }
        $this->display();
    }
    public function getLeftNumber($openId){
        $totalNumber = 4;
        //判断是否有分享
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
        $numberShare= M('womensday_share')->where("`sharetime` >= '$start' and `sharetime` <= '$end' and `openid`= '$openId'")->count('id');
        if($numberShare){
            $totalNumber = 5;
        }
        $numberForSecond= M('womensday_list')->where("`createtime` >= '$start' and `createtime` <= '$end' and `openid`= '$openId'")->count('id');

        if($numberForSecond >= $totalNumber){
            //当天已经没有机会
            $left = 0;
        }else{
            $left = $totalNumber - $numberForSecond;
        }
        return $left;
    }
    public function mysucai(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=Womensday&a=index");
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


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");
        $this->assign('shareurl',  $this->get_url());
        $this->assign("siteurl",$this->url);

        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url = "http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        $this->assign('item1',$info['item1']);
        $this->assign('item2',$info['item2']);
        $this->assign('item3',$info['item3']);
        $this->assign('item4',$info['item4']);
        //判断四种素材是否都有
        $fourItems =  false;
        if($info['item1'] && $info['item2'] && $info['item3'] && $info['item4']){
            $fourItems = true;
        }
        $this->assign('fouritems',$fourItems);

        $submitTelephone = false;
        //判断是否已经提交过个人信息
        $infoAward = M('womensday_award')->where(array('openid' => $userOpenId))->find();
        if($infoAward){
            $submitTelephone = true;
        }
        $this->assign('submittelephone',$submitTelephone);
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    //保存电话和地址信息
    public function reg(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) &&  $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode($this->url."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url = "http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }

        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    //成功页面
    public function successpage(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
            
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        $username = $_POST['username'];
        $telephone = $_POST['telephone'];
        $province = $_POST['province'];
        $address = $_POST['address'];
        //判断此人是否提交郭信息
        $info = M('womensday_award')->where(array('openid' => $userOpenId))->find();
        if($info){
            $errorMsg = "已经提交过个人信息!";
        }else{
            //判断此手机号是否存在
            $list = M('womensday_award')->where(array('telephone' => $telephone))->find();
            if(!$list){
                $d['openid'] = $userOpenId;
                $d['username'] = $username;
                $d['telephone'] = $telephone;
                $d['province'] = $province;
                $d['address'] = $address;
                M("womensday_award")->add($d);
                $errorMsg = null;
            }else{
                $errorMsg = "手机号已经存在！";
            }
        }

        $this->assign('errormsg',$errorMsg);
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    //中奖页面
    //成功页面
    public function lottery(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        //判断此人是否收集到四个元素
        $totalItem = false;
        $telephone = null;
        if($info){
            if($info['item1'] && $info['item2'] && $info['item3'] && $info['item4']){
                $totalItem = true;
            }
        }
        if($totalItem){
            //判断此人是否提交过手机号码，如果没有提交
            $list = M('womensday_award')->where(array('openid' => $userOpenId))->find();
            if($list){
                $telephone = $list['telephone'];
            }
        }

        $award = false;
        $isPost = false;
        if(isset( $_POST['telephonehidden'] )  &&  $_POST['telephonehidden']){
            $telephoneFromForm = $_POST['telephonehidden'];
            $listAward = M('womensday_award')->where(array('openid' => $userOpenId,'telephone'=>$telephoneFromForm,'award'=>1))->find();
            if($listAward){
                $award = true;
            }
            $isPost = true;

        }
        $this->assign("ispost",$isPost);
        $this->assign("award",$award);
        $this->assign("totalitem",$totalItem);
        $this->assign("telephone",$telephone);

        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    public function whatisitem(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    public function nocolpage(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }

        $itemInfo = M('womensday')->where(array('openid' => $userOpenId))->find();
        $totalItem = false;
        if($itemInfo){
            if($itemInfo['item1'] && $itemInfo['item2'] && $itemInfo['item3'] && $itemInfo['item4']){
                $totalItem = true;
            }
        }
        $this->assign("totalitem",$totalItem);
        $this->assign("left",$this->getLeftNumber($userOpenId));
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }

    public function rule(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }
    //item 3 - 玻尿酸
    public function item3(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }
//天羽丝
    public function item5(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }
//item 4 竹炭颗粒
    public function item4(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }
    //item 6 蝴蝶眼膜
    public function item6(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        $userOpenId= cookie('user_openid');

        $gid = 1;
        //统计end

        //即使存在与cookie但是fans中不存在必须重新获取
        $selfUserInfo = array();
        if (isset($_GET['show']) && $_GET['show']) {
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
                $code = trim($_GET["code"]);
                $state = trim($_GET['state']);
                if ($code && $state == 'sentian') {
                    //查看web_acccess_token是否过期
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
                                $url = $this->url."/index.php?g=Wap&m=Womensday&a=index";
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
                    $url = urlencode("http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index");
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
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);


        $this->assign('title',$selfUserInfo['nickname']."寻找密集系列的奇迹 获取四种素材可获得森田药妆水活新颜女神定制礼盒");
        $this->assign('bonusdesc','');
        $img = "http://wx.drjou.cc/tpl/Wap/default/common/womensday/images/logo3.png";
        $this->assign("shareimage",$img);
        $this->assign('url',  $url."/index.php?g=Wap&m=Womensday&a=index");

        $this->assign("siteurl",$this->url);
        $this->assign('shareurl',  $this->get_url());
        //$userOpenId
        //判断OPENID是否存在 在表womensday中
        $info = M('womensday')->where(array('openid' => $userOpenId))->find();
        $left = 0;
        if(!$info){
            //重定向到首页
            $url ="http://wx.drjou.cc"."/index.php?g=Wap&m=Womensday&a=index";
            header("location:$url");
        }
        if($userOpenId){
            $this->savePageViews($info['id']);
        }
        $this->display();
    }
    public function shareInfo(){
        //只要分享了 额外获取一次机会
        $userOpenId= cookie('user_openid');
        if($userOpenId){
            //判断OPENID是否存在
            $info = M('womensday')->where(array('openid' => $userOpenId))->find();
            if($info){
                M("womensday")->where(array('id' =>$info['id']))->setInc('shares', 1);
                //判断是否已经分享过
                $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
                $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
                $number= M('womensday_share')->where("openid='$userOpenId' and sharetime>='$start' and sharetime <= '$end'")->count('id');
                if($number){
                    //今天已经分享过
                    echo 1;
                }else{
                    $d['openid'] = $userOpenId;
                    M("womensday_share")->add($d);
                    echo 2;
                }
            }else{
                echo 0;
            }

        }else{
            echo 0;
        }

    }

    public function saveGetSucaiClickNum(){
        $openId = cookie('user_openid');
        if($openId){
            M("womensday")->where(array('openid' =>$openId))->setInc('getsucaiclicknum', 1);
        }
    }

    public function saveList($openId,$numberForSecond=0){
        $d['openid'] = $openId;
        $d['click'] = $numberForSecond*1 + 1;
        M("womensday_list")->add($d);
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

        $this->display();
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

    public function curlGetV2($get_user_info_url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function culrGetV3($url,$method="get",$postfields = null, $headers = array(), $debug = true){
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        curl_close($ci);
        return array($http_code, $response);
    }
}
