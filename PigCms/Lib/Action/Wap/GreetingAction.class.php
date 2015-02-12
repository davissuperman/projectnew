<?php

class GreetingAction extends Action {


    public $cache;
    public $titleInWeixin = '森田药妆-新年贺卡';



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
//        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
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

        $this->display();
    }

}
