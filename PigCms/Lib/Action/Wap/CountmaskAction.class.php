<?php

class CountmaskAction extends SjzAction {
    public $title = '森田药妆数面膜';
    public $bonusdesc = '森田药妆数面膜';
    public $eachVote = 10;

    public function getDiymenSet(){
        $gid = 1;
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
        $appId = $apidata['appid'];
        return array($ticket,$appId,$gid);
    }

    public function saveInfo($gid,$openId,$nickname,$imageProfile){
        //首先查看此OPENID 是否存在 无论gid
        $bonusInfo = M('countmask')->where(array('openid' => $openId))->find();
        if(!$bonusInfo){
            //创建个人主页
            $d['gid'] = $gid;
            $d['openid'] = $openId;
            $d['name'] = $nickname;
            $d['headimgurl'] = $imageProfile;
            $d['views'] = 1;
            $d['createtime'] = time();
            M("countmask")->add($d);
        }
    }



        public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
        $userOpenId= cookie('user_openid');
        $fansInfo = null;
        $selfUserInfo = array();
        $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if($userOpenId && $fansInfo){
            $selfUserInfo['headimgurl'] = $fansInfo['headimgurl'];
            $selfUserInfo['nickname'] = $fansInfo['nickname'];
        }else{
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            if ($code && $state == 'sentian') {
                if(empty($fansInfo)){
                    $webCreatetime = $apidata['web_createtime'];
                    $web_access_token = '';

                    if($webCreatetime>(time()-7200) && $userOpenId){
                        //未过期
                        $web_access_token = $apidata['web_access_token'];
                    }else{
                        //重新获取
                        $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                        if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                            //code 有错误 需要重定向
                            $url = $this->url."/index.php?g=Wap&m=Countmask&a=index";
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
                $url = urlencode($this->url."/index.php?g=Wap&m=Countmask&a=index");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                exit;
            }
        }



            $gid = $_GET['gid'];
            if(!$gid){
                $gid = 1;
            }
            $nickname = $selfUserInfo['nickname'];
            $imageProfile = $selfUserInfo['headimgurl'];
        $this->saveInfo($gid,$userOpenId,$nickname,$imageProfile);

        list($ticket,$appId,$gid) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("url",$url);
        $this->assign('gid', $gid);

        $this->assign('title',$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/present/images/logo1.jpg');
        $this->assign("shareimageurl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/present/images/logo1.jpg');

        $this->display();
    }

    public function game(){
        $this->display();
    }
    public function rule(){
        $this->display();
    }
    public function score(){
        $userOpenId= cookie('user_openid');
        $number = $_GET['number'];
        if(!$userOpenId || !$number || ($number > 200)){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Countmask&a=index");
        }

        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        $currentSequence = $info['sequence'];
        switch($currentSequence){
            case 0:
                $d = array();
                $d['sequence'] = 1;
                $d['number'] = $number;
                $d['id'] = $info['id'];
                M('countmask')->save($d);
                break;
            case 1://第一次满10票后
                //判断用户是否满足条件
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>1))->find();
                if($infoList['vote'] == $this->eachVote){
                    M("countmask")->where(array('id' =>$info['id']))->setInc('number', $number);

                    //更新sequence
                    M("countmask")->where(array('id' =>$info['id']))->setInc('sequence');

                    //表 countmask_list更新每一次机会获取的分数
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('countmask_list')->save($m);
                }
                break;
            case 2://第二次满10票后
                //判断用户是否满足条件
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>2))->find();
                if($infoList['vote'] == $this->eachVote){
                    M("countmask")->where(array('id' =>$info['id']))->setInc('number', $number);

                    //更新sequence
                    M("countmask")->where(array('id' =>$info['id']))->setInc('sequence');

                    //表 countmask_list更新每一次机会获取的分数
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('countmask_list')->save($m);
                }
                break;
            case 3://第三次满10票后
                //判断用户是否满足条件
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>3))->find();
                if($infoList['vote'] == $this->eachVote){
                    M("countmask")->where(array('id' =>$info['id']))->setInc('number', $number);

                    //更新sequence
                    M("countmask")->where(array('id' =>$info['id']))->setInc('sequence');

                    //表 countmask_list更新每一次机会获取的分数
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('countmask_list')->save($m);
                }
                break;
            default:
                //没有机会再次增加分数
        }

        $this->assign('number',$number);

        //同时将数据保存到tp_countmask_list
        //判断是否存在default的记录
        $list = M('countmask_list')->where(array('openid' => $userOpenId,'sequence' => 1))->find();
        if(!$list){
            $l = array();
            $l['openid'] = $userOpenId;
            $l['number'] = $number;
            $l['sequence'] = 1;// default
            $l['createtime'] = time();
            $l['vote'] = 0;//default
            M('countmask_list')->add($l);
        }

        $this->display();
    }
    public function sharenumber(){
        $userOpenId= cookie('user_openid');
        $phone = $_GET['phone'];
        if(!$userOpenId || !$phone){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Countmask&a=index");
        }
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        $uid = $info['id'];
        $d = array();
        $d['phone'] = $phone;
        $d['phonetime'] = time();
        $d['id'] = $info['id'];
        M('countmask')->save($d);

        list($ticket,$appId,$gid) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("url",$this->url."/index.php?g=Wap&m=Countmask&a=sharefriend&uid=".$uid);
        $this->assign('gid', $gid);
        $this->assign('username', $info['name']);
        $this->assign('totalnumber', $info['number']);


       //current sequence
        $sequence = $info['sequence'];
        // $sequence = 1 : 还没有进行过投票
        // $sequence = 2 : 已经用过一次
        // $sequence = 3 : 已经用过二次
        // $sequence = 4 : 已经用过三次
        $couldCountMaskAgain = false;
        $currentNeedVote = 0;
        switch($sequence){
            case 1:
                //正在争取第一次机会
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                if($infoList){
                    $currentNeedVote = $this->eachVote - $infoList['vote'];
                }else{
                    $currentNeedVote = 10;
                }
                if($infoList['vote'] == $this->eachVote){
                    $couldCountMaskAgain = true;
                }
                break;
            case 2:
                //正在争取第二次机会
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                if($infoList){
                    $currentNeedVote = $this->eachVote - $infoList['vote'];
                }else{
                    $currentNeedVote = 10;
                }
                if($infoList['vote'] == $this->eachVote){
                    $couldCountMaskAgain = true;
                }
                break;
            case 3:
                //正在争取第三次机会
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                if($infoList){
                    $currentNeedVote = $this->eachVote - $infoList['vote'];
                }else{
                    $currentNeedVote = 10;
                }
                if($infoList['vote'] == $this->eachVote){
                    $couldCountMaskAgain = true;
                }
                break;
        }


        $this->assign('needvote', $currentNeedVote);
        $this->assign('couldcountmaskagain', $couldCountMaskAgain);
        $this->display();
    }

    public function sharefriend(){
        $userOpenId= cookie('user_openid');
        $fansInfo = null;
        $selfUserInfo = array();
        $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if($userOpenId && $fansInfo){
            $selfUserInfo['headimgurl'] = $fansInfo['headimgurl'];
            $selfUserInfo['nickname'] = $fansInfo['nickname'];
        }else{
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            if ($code && $state == 'sentian') {
                if(empty($fansInfo)){
                    $webCreatetime = $apidata['web_createtime'];
                    $web_access_token = '';

                    if($webCreatetime>(time()-7200) && $userOpenId){
                        //未过期
                        $web_access_token = $apidata['web_access_token'];
                    }else{
                        //重新获取
                        $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                        if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                            //code 有错误 需要重定向
                            $url = $this->url."/index.php?g=Wap&m=Countmask&a=index";
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
                $url = urlencode($this->url."/index.php?g=Wap&m=Countmask&a=sharefriend");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                exit;
            }
        }
        //local open id $userOpenId

        $uid  = $_GET['uid'];
        $infoTO = M('countmask')->where(array('id' => $uid))->find();
        $toUserOpenId = $infoTO['openid'];
        $userName = $infoTO['name'];
        $number = $infoTO['number'];
        $this->assign('name', $userName);
        $this->assign('number', $number);
        $sequence = $infoTO['sequence'];
        if($sequence == 1){
            //第一次 还差多少票
            $infoList = M('countmask_list')->where(array('openid' => $toUserOpenId,'sequence'=>$sequence))->find();
            $vote = $infoList['vote'];
            $leftVote = $this->eachVote - $vote;
        }
        $this->assign('leftvote', $leftVote);
        $this->assign('fromopenid', $userOpenId);
        $this->assign('toopenid', $toUserOpenId);
        $this->assign('sequence', $sequence);
        $this->display();
    }

    //TODO add random str to avoid auto submit
    public function saveVote(){

        $return = 0;
        $localUserOpenIdFromCookie= cookie('user_openid');
        $fromOpenIdFromPost = $_POST['fromopenid'];
        $toOpenIdFromPost = $_POST['toopenid'];
        $tousersequence = $_POST['tousersequence'];
        if(!$localUserOpenIdFromCookie || ($localUserOpenIdFromCookie != $fromOpenIdFromPost)){
            //非法投票
            exit();
        }
        //检查此 local openid 是否投过票
        $voteList = M('countmask_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        if(!$voteList){
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['sequence'] = $tousersequence;
            $d['createtime'] = time();
            M('countmask_vote')->add($d);
            $return = 1;
        }

        echo $return;
    }

    public function rank(){
        $this->display();
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
     * 获得微信用户信息
     * @param type $code
     * @param type $appid
     * @param type $appsecret
     * @return type
     */
    public function getUserInfo($code, $appid, $appsecret) {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);
        return $access_token_array;
    }

    public function https_request($url, $method = 'get', $data = '') {
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
