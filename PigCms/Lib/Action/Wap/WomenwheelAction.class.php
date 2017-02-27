<?php

class WomenwheelAction extends SjzAction {
    public $title = '和森田一起点亮幸运摩天轮!';
    public $bonusdesc = '特等奖20盒面膜,共1200份奖品, 我已经使出洪荒之力,你还在等什么,马上开始吧!';
    public $eachVote = 10;
    public $imageUrl;
    public $shareImageUrl;
    public $endtime="2017-03-30 23:59:59"; //活动结束时间
    public $debug = true; //上线后应该改成false
    public $defalutGid = 115;



    //womenwheel
    public $womenwheelCount = 24;
    public $teDengJiangCount = 20;
    public $yiDengJiangCount = 1000;
    public $eachChouJiangVote = 8;
    public $totalChouJiangVote = 15;
    public $totalDrawCount = 3;

    public function _initialize() {
        parent :: _initialize();
        $this->url= C('site_url');
        $this->imageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/womenwheel/images/logo1.jpg';
        $this->shareImageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/womenwheel/images/logo1.jpg';

        $ip=get_client_ip();
        $userOpenId= cookie('user_openid_new');
        if($userOpenId){
            $uid = $this->getUidByOpenid($userOpenId);
            if($uid){
                //判断此IP是否访问过
                $id = M('womenwheel_iplist')->where("ip='$ip'")->getField('id');
                if(!$id){
                    $n = array();
                    $n['uid'] = $uid;
                    $n['ip'] = $ip;
                    M('womenwheel_iplist')->add($n);
                }
            }
        }
    }

    public function getInfo(){
        $userOpenId = null;
        $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $code = trim($_GET["code"]);
        $state = trim($_GET['state']);

        $fansInfo = M('customer_service_fans')->field('openid,nickname')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if ($code && $state == 'sentian') {
            if(empty($fansInfo)){
                $webCreatetime = $apidata['web_createtime'];
                $web_access_token = '';

                //重新获取
                $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                    //code 有错误 需要重定向
                    $url = $this->url."/index.php?g=Wap&m=Womenwheel&a=getInfo";
                    header("location:$url");
                }
                $m['id'] = $apidata['id'];
                $m['web_access_token'] = $userinfoFromApi['access_token'];
                $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                $m['web_createtime'] = time();
                $m['refresh_token_createtime'] = time();
                M('Diymen_set')->save($m);
                $web_access_token = $userinfoFromApi['access_token'];
                cookie('user_openid_new', $userinfoFromApi['openid'], 315360000);
                $userOpenId = $userinfoFromApi['openid'];
                echo $userOpenId;
            }
        } else {
            $url = urlencode($this->url."/index.php?g=Wap&m=Womenwheel&a=getInfo");
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
            exit;
        }
    }
    public function getShareUrl(){
        $mainId = null;
        $mainGid = null;
        //首先判断main uid是否存在
        $uid = $_GET['uid'];
        if($uid && is_numeric($uid)){
            $mainId = $uid;
            if($_GET['uid'] && is_numeric($_GET['uid'])){
                $mainGid = $_GET['uid'];
            }
        }else{
            $userOpenId= cookie('user_openid_new');
            $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
            $mainId = $info['id'];
            $mainGid = $info['gid'];
        }
        return $this->url."/index.php?g=Wap&m=Womenwheel&a=sharefriend&uid=".$mainId."&gid=$mainGid";
    }
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
                $fdata['ticket_time'] = time();
                M('Diymen_set')->save($fdata);
            }
        }
        $appId = $apidata['appid'];
        return array($ticket,$appId,$gid);
    }

    public function saveInfo($gid,$openId,$nickname,$imageProfile){
//        if($gid < 110 || $gid > 120){
//            Log :: write( $openId."  ".$nickname." $gid 不存在" ,'ERR','','test.log');
//            return null;
//        }
        //首先查看此OPENID 是否存在 无论gid
        $bonusInfo = M('womenwheel')->where(array('openid' => $openId))->find();
        $lastInsertId = null;
        if(!$bonusInfo){
            //创建个人主页
            $d['gid'] = $gid;
            $d['openid'] = $openId;
            $d['name'] = $nickname;
            $d['headimgurl'] = $imageProfile;
            $d['views'] = 1;
            $d['createtime'] = time();
            $lastInsertId = M("womenwheel")->add($d);
        }
        return $lastInsertId;
    }
    public function setEndTime2(){
        $endtime =strtotime( "2017-03-25 23:59:59" );
        if (time() > $endtime) {//活动是否结束

            echo <<<HTML
                <center>
<h1>活动已经结束！</h1>
</center>
HTML;

            exit();
        }
    }
        public function setEndTime(){
            $endtime =strtotime( $this->endtime );
            if (time() > $endtime) {//活动是否结束

                echo <<<HTML
                <center>
<h1>活动已经结束！</h1>
</center>
HTML;

                exit();
            }
        }

        public function index() {
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        $this->setEndTime();
        $userOpenId= cookie('user_openid_new');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $fansInfo = null;
        $selfUserInfo = array();
        $fansInfo = M('customer_service_fans')->field('openid,nickname,headimgurl')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if($userOpenId && $fansInfo){
            $selfUserInfo['headimgurl'] ='';
            $selfUserInfo['nickname'] = '';
        }else{
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);
            if ($code && $state == 'sentian') {
                if(empty($fansInfo)){
                    $webCreatetime = $apidata['web_createtime'];
                    $web_access_token = '';

                    //重新获取
                    $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                    if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                        //code 有错误 需要重定向
                        $url = $this->url."/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid";
                        header("location:$url");
                    }
                    $m['id'] = $apidata['id'];
                    $m['web_access_token'] = $userinfoFromApi['access_token'];
                    $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                    $m['web_createtime'] = time();
                    $m['refresh_token_createtime'] = time();
                    M('Diymen_set')->save($m);
                    $web_access_token = $userinfoFromApi['access_token'];
                    cookie('user_openid_new', $userinfoFromApi['openid'], 315360000);
                    $userOpenId = $userinfoFromApi['openid'];

//                    $selfUserInfo['headimgurl'] = $json->headimgurl;
//                    $selfUserInfo['nickname'] = $json->nickname;
                }
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }




        $nickname = '';
        $imageProfile = '';

        //首先判断当前用户是否有玩过第一次
        $info = M('Womenwheel')->where(array('openid' => $userOpenId))->find();
        $vote = null;
        $lastInsertId = null;
        $views = null;
        if($info){
            $lastInsertId = $info['id'];
            $vote = $info['vote'];
            $views = $info['views'];
        }else{
            //此用户不存在
            $lastInsertId = $this->saveInfo($gid,$userOpenId,$nickname,$imageProfile);
            $info = M('Womenwheel')->where(array('openid' => $userOpenId))->find();
        }
        //如果VOTE未满5票 调到分享引导页P3
        $this->haveFinishThreeOrHavePrizeOrVoteSmallThanFive($info,false,true);

        //begin 分享出去的URL
        list($ticket,$appId,$testgid) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);
        $this->assign('title',$nickname.$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //view自增
        if($lastInsertId){
            $this->setIncViews($lastInsertId);
        }

        $this->assign("vote",$vote);
        $this->assign("gid",$gid);

        $this->assign("teDengJiangCount",$this->teDengJiangCount);

        $draw = $info['draw'];
        $leftVote = 0;
        $leftDraw = 3 - $draw;

        $leftVote = $this->getLeftVote($draw,$vote);
        if($leftVote < 0 ){
            $leftVote = 0;
        }
        if($leftDraw < 0 ){
            $leftDraw = 0;
        }
        $this->assign("leftVote",$leftVote);
        $this->assign("leftDraw",$leftDraw);

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }
        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        $this->assign("leftTeDengJiang",$leftTeDengJiang);
        $this->assign("leftYiDengJiang",$leftYiDengJiang);
        $this->display();
    }

    public function getLeftVote($draw,$vote){
        $leftVote = 0;
        if($draw == 0){
            //还未抽过奖
            $leftVote = $this->eachChouJiangVote - $vote;

        }elseif($draw == 1){
            //已经抽过一次奖了
            $leftVote = $this->eachChouJiangVote*2 - $vote;
        }elseif($draw == 2){
            //已经抽过二次奖了
            $leftVote = $this->eachChouJiangVote*3 - $vote;
        }
        if($leftVote < 0 ){
            $leftVote = 0;
        }
        return $leftVote;
    }
    public function success(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid_new');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index");
            exit();
        }

        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $draw = $info['draw'];
        $gid = $info['gid'];
        //begin views
        if($info){
            $this->setIncViews($info['id']);
        }
        $prize = $info['prize'];
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end
        $this->assign("prize",$prize);
        $this->assign("openid",$userOpenId);

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }
        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        $this->assign("leftTeDengJiang",$leftTeDengJiang);
        $this->assign("leftYiDengJiang",$leftYiDengJiang);
        $this->assign("draw",$draw);
        $this->display();
    }

    public function rule(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid_new');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //begin views
        $userOpenId= cookie('user_openid_new');
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views
        $this->assign("gid",$gid);
        $this->display();
    }


    public function rank1(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid_new');
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //begin views
        $userOpenId= cookie('user_openid_new');
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views
        $this->assign("gid",$gid);
        $this->display();
    }

    public function haveFinishThreeOrHavePrizeOrVoteSmallThanFive($info,$shareSelf = false,$indexSelf=false){
        $vote = $info['vote'];

        if(!$info && !$indexSelf){
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index");
            exit();
        }
        $prize = $info['prize'];
        $draw = $info['draw'];
        if($prize == 1 || $prize == 2){//特等奖 or 一等奖
            //redirect
            $url = "http://mp.weixin.qq.com/s/Cqw5hmJtxCrgvyUaVZNEyw";
            header("location:$url");
            exit();
        }else{
            if($draw == 3){
                $url = "http://mp.weixin.qq.com/s/o4Vu9MOSe2dYaUgxV77Dew";
                header("location:$url");
                exit();
            }
        }

        if($indexSelf){
            //首先查看是否为自己投过票
            $userOpenId= cookie('user_openid_new');
           // $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
            $voteListSql = "SELECT id from tp_womenwheel_votelist where fromopenid='$userOpenId' and toopenid='$userOpenId'";
            $voteView = M('womenwheel_votelist')->query($voteListSql);
            if($voteView){
                //已经为自己投过票
                if($vote<5 ){
                    header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=share&shareclick=1");
                    exit();
                }else{
                    header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=share");
                    exit();
                }
            }else{
                return;
            }
        }
        if($vote<5 && !$shareSelf){
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=share&shareclick=1");
            exit();
        }

    }
    public function share(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid_new');
//       $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }

        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }
        $gid = $info['gid'];
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views
        $vote = $info['vote'];
        $share = $info['share'];
        $prize = $info['prize'];
        $this->haveFinishThreeOrHavePrizeOrVoteSmallThanFive($info,true);

        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
        $start = date("Y-m-d H:i:s",$start );
        $end = date("Y-m-d H:i:s",$end );
//        $uniqueViewSql = "SELECT * from tp_womenwheel_uniqueviewlist where   createtime >= '$start' and createtime<'$end' and fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewSql = "SELECT * from tp_womenwheel_votelist where  fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewlist = M('womenwheel_votelist')->query($uniqueViewSql);
        $haveVoted = 0;


        $this->assign('sharenumberindatabase',$share);
        $this->assign('havevoted',$haveVoted);
        if($share <= 3){
            $leftShare = 3 - $share;
        }else{
            $leftShare = 0;
        }
        $this->assign('leftshare',$leftShare);

        $phoneExist = 0;
        if($info['phone']){
            $phoneExist = 1;
        }
        $this->assign('phonexist',$phoneExist);
        $this->assign("gid",$gid);
        $this->assign("uid",$info['id']);
        $this->assign("mainopenid",$userOpenId);
        $shareDivShow = 0;
        if(isset($_GET['shareclick']) && $_GET['shareclick'] == 1){
            $shareDivShow = 1;
        }
        $this->assign("sharedivshow",$shareDivShow);
        $this->assign("teDengJiangCount",$this->teDengJiangCount);

        $draw = $info['draw'];
        $leftVote = 0;
        $leftDraw = 3 - $draw;

        $leftVote = $this->getLeftVote($draw,$vote);
        if($leftVote < 0 ){
            $leftVote = 0;
        }
        if($leftDraw < 0 ){
            $leftDraw = 0;
        }
        $this->assign("leftVote",$leftVote);
        $this->assign("leftDraw",$leftDraw);

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }
        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        $this->assign("leftTeDengJiang",$leftTeDengJiang);
        $this->assign("leftYiDengJiang",$leftYiDengJiang);
        $this->assign("draw",$draw);

        $this->display();
    }


    public function sharefriend(){
        $this->setEndTime();
        //这里是隐性获取OPENID 是朋友圈里面的人打开这个页面
        //获取OPENID 用户没有感知
        $userOpenId= cookie('user_openid_new');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        $uid = $_GET['uid'];
        if(!is_numeric($uid)){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }


        if(!$userOpenId){
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);

            $fansInfo = M('customer_service_fans')->field('openid,nickname,headimgurl')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
            if ($code && $state == 'sentian') {
                if(empty($fansInfo)){
                    $webCreatetime = $apidata['web_createtime'];
                    $web_access_token = '';

                    //重新获取
                    $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                    if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                        //code 有错误 需要重定向
                        $url = $this->url."/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid";
                        header("location:$url");
                    }
                    $m['id'] = $apidata['id'];
                    $m['web_access_token'] = $userinfoFromApi['access_token'];
                    $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                    $m['web_createtime'] = time();
                    $m['refresh_token_createtime'] = time();
                    M('Diymen_set')->save($m);
                    $web_access_token = $userinfoFromApi['access_token'];
                    cookie('user_openid_new', $userinfoFromApi['openid'], 315360000);
                    $userOpenId = $userinfoFromApi['openid'];

                }
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Womenwheel&a=sharefriend&uid=$uid&gid=$gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }

        $info = M('womenwheel')->where(array('id' => $uid))->find();
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }


        if(!$gid && isset($info['gid']) && $info['gid'] ){
            $gid = $info['gid'];
        }
        $MainOpenId = $info['openid'];
        $share = $info['share'];

        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
        $start = date("Y-m-d H:i:s",$start );
        $end = date("Y-m-d H:i:s",$end );
        $uniqueViewSql = "SELECT * from tp_womenwheel_uniqueviewlist where   createtime >= '$start' and createtime<'$end' and fromopenid='$userOpenId' and toopenid='$MainOpenId'";
        $uniqueViewlist = M('womenwheel_uniqueviewlist')->query($uniqueViewSql);
        $haveVoted = 1;
        if($uniqueViewlist){
            //不需要增加uniqueviews
        }else{
            M("womenwheel")->where(array('id' => $info['id']))->setInc('uniqueviews');
            $n = array();
            $n['fromopenid'] = $userOpenId;
            $n['toopenid'] = $MainOpenId;
            M('womenwheel_uniqueviewlist')->add($n);
        }
        if($userOpenId == $MainOpenId){
            //自己访问自己的主页 跳转到share页面
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=share&gid=$gid");
            exit();
        }

        //直接跳转

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }

        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        if($leftTeDengJiang == 0 && $leftYiDengJiang == 0){
            //活动结束
            $url = "http://mp.weixin.qq.com/s/t1d87DU4hId-5PuIj2YUmQ";
            header("location:$url");
            exit();
        }
        //直接跳转结束
        $voteListSql = "SELECT * from tp_womenwheel_votelist where fromopenid='$userOpenId' and toopenid='$MainOpenId'";
        $voteView = M('womenwheel_votelist')->query($voteListSql);

        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //begin views
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views

        $this->assign('openid',$userOpenId);
        $this->assign('mainopenid',$MainOpenId);

        $this->assign('uid',$uid);
        $this->assign('gid',$gid);

        //判断当前用户是否已经投过票
        $voteList = M('womenwheel_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$MainOpenId  ))->find();
        $voteThisUid = 0;
        if($voteList){
            $voteThisUid = 1;
            //已经投过票 跳转到自己的主页
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }

        $this->assign('sharenumberindatabase',$share);
        $this->assign('votetothisuid',$voteThisUid);


        $this->assign('havevoted',$haveVoted);
        $this->assign("gid",$gid);



        //womenwheel
        $vote = $info['vote'];
        $this->assign("teDengJiangCount",$this->teDengJiangCount);
        $this->assign("yiDengJiangCount",$this->yiDengJiangCountDengJiangCount);

        $draw = $info['draw'];
        $leftVote = 0;
        $leftDraw = 3 - $draw;

        $leftVote = $this->getLeftVote($draw,$vote);
        if($leftVote < 0 ){
            $leftVote = 0;
        }
        if($leftDraw < 0 ){
            $leftDraw = 0;
        }
        $this->assign("leftVote",$leftVote);
        $this->assign("leftDraw",$leftDraw);

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }
        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        $this->assign("leftTeDengJiang",$leftTeDengJiang);
        $this->assign("leftYiDengJiang",$leftYiDengJiang);

        $this->display();
    }

    //根据UID获取OPENID
    public function getOpenIdByUid($uid){
        $openId = M('womenwheel')->where("id=$uid")->getField('openid');
        return $openId;
    }
    public function getUidByOpenid($openid){
        $uid = M('womenwheel')->where("openid='$openid'")->getField('id');
        return $uid;
    }
    public function saveVote2(){
        $this->setEndTime();
        $return = 0;
        $fromOpenIdFromPost= cookie('user_openid_new');
//        $fromOpenIdFromPost = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $toUid = $_POST['uid'];
        if(!$fromOpenIdFromPost){
            //非法投票
            exit();
        }
        $vote = M('womenwheel')->where("id=$toUid")->getField('vote');
        if($vote >= 15){
            echo 3;
            return;
        }
        $toOpenIdFromPost = $this->getOpenIdByUid($toUid);
        //检查此 local openid 是否投过票
        $voteList = M('womenwheel_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        //多次投票
        if(!$voteList){
//        if(true){
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['createtime'] = time();
            M('womenwheel_votelist')->add($d);
            M("womenwheel")->where(array('id' => $toUid))->setInc('vote');
            $return = 1;

//            $info = M('womenwheel')->where(array('id' => $toUid))->find();

        }else{
            //已经投过票
            $return = 2;
        }
        echo $return;
    }

    public function test(){
        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        echo $teDengJiangCount;
    }

    public function saveDraw(){
        $this->setEndTime();
        $return = 0;
        $paiming = null;
        $prize = null;
        $returnMessage = null;
        $fromOpenIdFromPost= cookie('user_openid_new');
     //   $fromOpenIdFromPost = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $info = M('womenwheel')->where(array('openid' => $fromOpenIdFromPost))->find();
        if(!$info){
            exit;
        }
        //查看当前的用户的名次
        $number = $info['number'];

        //用户是否已经获得奖
        $prize = $info['prize'];

        //查看抽奖次数
        $drawCount = $info['draw'];
        $voteCount = $info['vote'];
        if($prize == 1){
            //已获得特等奖，无法再抽奖
            $returnMessage = "已获特等奖,无法再抽奖";
            echo json_encode(array($returnMessage,$prize,$drawCount+1)) ;
            return;
        }
        if($prize == 2){
            //已获得特等奖，无法再抽奖
            $returnMessage = "已获一等奖,无法再抽奖";
            echo json_encode(array($returnMessage,$prize,$drawCount+1)) ;
            return;
        }
        //还未抽中奖
        if(!$prize){
            if(!$drawCount && $voteCount>=5){
                //第一次抽
                $p = array();
                $p['uid'] =  $info['id'];
                $p['position'] =  1;
                $paiming = M('womenwheel_drawlist')->add($p);



                $prize = $this->whetherDraw($paiming);
                if($prize == 1){
                    //查看已经有多少人获取了特等奖 最多10个
                    $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
                    if($teDengJiangCount<=9){
                        M('womenwheel_jiang')->where('id=1')->setInc('tedengjiang');
                        $returnMessage = "恭喜抽中特等奖！";
                    }else{
                        $prize = 3;//本应获得特等奖 但是 奖没了
                        $returnMessage = "未中奖";
                    }

                }elseif($prize == 2){
                    $yiDengJiangCount =  M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
                    if($yiDengJiangCount <= 999){
                        M('womenwheel_jiang')->where('id=1')->setInc('yidengjiang');
                        $returnMessage = "恭喜抽中一等奖！";
                    }else{
                        $prize = 4;//本应获得一等奖 但是 奖没了
                        $returnMessage = "未中奖";
                    }
                }else {
                    $returnMessage = "未中奖<br/>";
                    $yiDengJiangCount =  M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
                    $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
                    $leftDraw = $this->totalDrawCount - $drawCount - 1;
                    if($leftDraw < 0 ){
                        $leftDraw = 0;
                    }
                    $returnMessage .= "奖池剩余".$leftYiDengJiang."盒面膜/眼膜 ,您还有".$leftDraw."次抽奖机会";
                }
                $i = array();
                $i['id'] = $info['id'];
                $i['rank1'] = $paiming;
                $i['prize'] = $prize;
                $i['draw'] = 1;
                M('womenwheel')->save($i);
            }elseif($drawCount == 1 &&  $voteCount>=10){
                //第二次抽奖
                $p = array();
                $p['uid'] =  $info['id'];
                $p['position'] =  2;
                $paiming = M('womenwheel_drawlist')->add($p);
                $prize = $this->whetherDraw($paiming);
                if($prize == 1){
                    $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
                    if($teDengJiangCount<=9){
                        M('womenwheel_jiang')->where('id=1')->setInc('tedengjiang');
                        $returnMessage = "恭喜抽中特等奖！";
                    }else{
                        $prize = 3;//本应获得特等奖 但是 奖没了
                        $returnMessage = "未中奖";
                    }

                }elseif($prize == 2){
                    $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
                    if($yiDengJiangCount <= 999){
                        M('womenwheel_jiang')->where('id=1')->setInc('yidengjiang');
                        $returnMessage = "恭喜抽中一等奖！";
                    }else{
                        $prize = 4;//本应获得一等奖 但是 奖没了
                        $returnMessage = "未中奖";
                    }
                }else {
                    $returnMessage = "未中奖<br/>";
                    $yiDengJiangCount =  M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
                    $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
                    $leftDraw = $this->totalDrawCount - $drawCount - 1;
                    if($leftDraw < 0 ){
                        $leftDraw = 0;
                    }
                    $returnMessage .= "奖池剩余".$leftYiDengJiang."盒面膜/眼膜 ,您还有".$leftDraw."次抽奖机会";
                }
                $i = array();
                $i['id'] = $info['id'];
                $i['rank2'] = $paiming;
                $i['prize'] = $prize;
                $i['draw'] = 2;
                M('womenwheel')->save($i);
            }else if($drawCount == 2  && $voteCount>= 15){
                //第三次抽奖
                $i['id'] = $info['id'];
                $i['draw'] = 3;
                $i['thirdtime'] = date('Y-m-d H:i:s');
                M('womenwheel')->save($i);
                $returnMessage = "遗憾错过!";
            }else{
                $returnMessage = "提交过于频繁，请稍后再试!";
            }
        }

        echo json_encode(array($returnMessage,$prize,$drawCount+1)) ;
    }

    function whetherDraw($paiming){
        $return = null;
        if($paiming*1 == 66 || $paiming*1 == 566 || $paiming*1 == 1166 || $paiming*1 == 3166 || $paiming*1 == 4566 || $paiming*1 == 5966 || $paiming*1 == 6966 || $paiming*1 == 7966 || $paiming*1 == 8966 || $paiming*1 == 9566){
            $return = 1;
        }elseif(strrchr((string)"$paiming","7") == "7"){
            $return = 2;
        }
        return $return;
    }
    //TODO add random str to avoid auto submit
    public function saveVote(){
        $this->setEndTime();
        $return = 0;
        $leftVote = 0;
        $fromOpenIdFromPost= cookie('user_openid_new');
     //   $fromOpenIdFromPost = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $toOpenIdFromPost = $fromOpenIdFromPost;
        //检查此 local openid 是否投过票
        $voteList = M('womenwheel_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        if(!$voteList){//
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['createtime'] = time();
            M('womenwheel_votelist')->add($d);
            M("womenwheel")->where(array('openid' => $toOpenIdFromPost))->setInc('vote');
            $info = M('womenwheel')->where(array('openid' => $fromOpenIdFromPost))->find();
            if(!$info){
                exit;
            }
            $leftVote = $this->getLeftVote($info['draw'],$info['vote']);
            $return = 1;
        }else{
            //已经投过票
            $return = 2;
        }
        $arrTmp = array($return,$leftVote);
        echo json_encode($arrTmp);
    }
    public function saveFormInfo(){
        $this->setEndTime2();
        $return = 0;
        $userOpenId= cookie('user_openid_new');
     //   $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        if(!$userOpenId){
            //非法投票
            exit();
        }
        $username = $_POST['username'];
        $telphone = $_POST['telphone'];
        $province = $_POST['province'];
        $city = $_POST['city'];
        $county = $_POST['county'];
        $address = $_POST['address'];
        $award = M('womenwheel_award')->where(array('openid' => $userOpenId))->find();
        if(!$award){
            $m = array();
            $m['openid'] = $userOpenId;
            $m['name'] = $username;
            $m['phone'] =$telphone;
            $m['province'] =$province;
            $m['city'] =$city;
            $m['county'] =$county;
            $m['address'] =$address;
            $m['createtime'] = time();
            M('womenwheel_award')->add($m);
        }else{
                $m = array();
                $m['id'] = $award['id'];
                $m['name'] = $username;
                $m['phone'] =$telphone;
                $m['province'] =$province;
                $m['city'] =$city;
                $m['county'] =$county;
                $m['address'] =$address;
                $m['updatetime'] = time();
                M('womenwheel_award')->save($m);
                $return = 1;
        }
    }
    public function rank(){
        $userOpenId= cookie('user_openid_new');
        //        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index");
            exit();
        }
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }

        //如果没有满足16票 跳转到再接再厉
        if($info['vote'] <$this->womenwheelCount){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=rank1&gid=$gid");
            exit();
        }

        if($info && !$info['phone']){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=share&gid=$gid");
            exit();
        }
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end


        $vote = $info['vote'];
        $selfPhoneTime = $info['phonetime'];
        $p = $info['phone'];

        $uid = $info['id'];
        //查询此用户的排名
        $pValue = M('womenwheel_phonelist')->where(array('uid' => $uid))->getField('id');
        $vote = $this->womenwheelCount;
        $this->assign("vote",$vote);
        $this->assign("selforder",$pValue);
        $this->assign("paiming",$pValue);
        $this->assign("gid",$gid);
        $this->display();
    }

    public function form(){
        $this->setEndTime2();
        $userOpenId= cookie('user_openid_new');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Womenwheel&a=index&gid=$gid");
            exit();
        }
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', $gid);

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end


        $vote = $info['vote'];
        $gid = $info['gid'];

        $award = M('womenwheel_award')->where(array('openid' => $userOpenId))->find();
        $name = '';
        $phone = '';
        $address = '';
        $city = '';
        $country = '';
        $province = '';
        $a = 0;
        if($award){
            $name = $award['name'];
            $phone = $award['phone'];
            $address = $award['address'];
            $city =  $award['city'];
            $country = $award['county'];
            $province = $award['province'];
            $a = 1;
        }
        $this->assign("name",$name);
        $this->assign("address",$address);
        $this->assign("phone",$phone);
        $this->assign("city",$city);
        $this->assign("country",$country);
        $this->assign("province",$province);
        $this->assign("gid",$gid);
        $this->assign("whetheraward",$a);

        $teDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('tedengjiang');
        $leftTeDengJiang = $this->teDengJiangCount - $teDengJiangCount;
        if($leftTeDengJiang < 0 ){
            $leftTeDengJiang = 0;
        }
        $yiDengJiangCount = M('womenwheel_jiang')->where('id=1')->getField('yidengjiang');
        $leftYiDengJiang = $this->yiDengJiangCount - $yiDengJiangCount;
        if($leftYiDengJiang < 0 ){
            $leftYiDengJiang = 0;
        }
        $this->assign("leftTeDengJiang",$leftTeDengJiang);
        $this->assign("leftYiDengJiang",$leftYiDengJiang);


        $this->display();
    }

    public function getOrderByOpenId($openId=null){
        $list = M('womenwheel')->query("select openid, number,share,phonetime from tp_womenwheel where phone != '' order by number desc,share desc,phonetime asc ");

        $orderList = array();
        foreach($list as $each){
            $orderList[] = $each['openid'];
        }

        $key = array_search($openId, $orderList);
        return $key*1+1;
    }

    public function award(){
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        $userOpenId= cookie('user_openid_new');
        //begin 分享出去的URL
        list($ticket,$appId,$gidFromDiymenset) = $this->getDiymenSet();
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
        $url = $this->get_url();;
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($str);
        $this->assign("appid",$appId);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("shareurl",$this->getShareUrl());
        $this->assign('gid', 1);
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //begin views


        if($info){
            $this->setIncViews($info['id']);
        }
        // end views

        $this->assign('selfpage',$this->url."/index.php?g=Wap&m=Womenwheel&a=sharefriend&gid=$gid");

        $award = M('womenwheel_award')->where(array('openid' => $userOpenId))->find();
        if($award){
            $this->assign('name',$award['name']);
            $this->assign('phone',$award['phone']);
            $this->assign('province',$award['province']);
            $this->assign('city',$award['city']);
            $this->assign('address',$award['address']);
        }


        $this->display();
    }

    public function setIncViews($id){
        M("womenwheel")->where(array('id' => $id))->setInc('views');
        $m = array();
        $m['uid'] = $id;
        M('womenwheel_viewlist')->add($m);
    }

    public function  saveAward(){
        $return = 0;
        $userOpenId= cookie('user_openid_new');
        if(!$userOpenId){
            echo 0;
            return;
        }
        $award = M('womenwheel_award')->where(array('openid' => $userOpenId))->find();
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $province = $_POST['province'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        if(!$award){
            $m = array();
            $m['name'] = $name;
            $m['phone'] = $phone;
            $m['openid'] = $userOpenId;
            $m['province'] = $province;
            $m['city'] = $city;
            $m['address'] = $address;
            $m['createtime'] = time();
            M('womenwheel_award')->add($m);
            $return = 1;
        }else{
            $m = array();
            $m['id'] = $award['id'];
            $m['name'] = $name;
            $m['phone'] = $phone;
            $m['openid'] = $userOpenId;
            $m['province'] = $province;
            $m['city'] = $city;
            $m['address'] = $address;
            $m['createtime'] = time();
            M('womenwheel_award')->save($m);
            $return = 1;
        }

        echo $return;

    }
    /**
     * 获取用户基本信息并且保存到数据库
     */
    public function saveUserInfo($json){
        $token = "rggfsk1394161441";
        $fanModel = M('customer_service_fans'); //向粉丝表中添加此用户信息
        $ffind = $fanModel->field('openid,nickname,headimgurl')->where(array('openid' => $json->openid, 'token' => $token))->find();

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


    /*
     * 助力成功
     */
    public function saveZhuli(){
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        $userOpenId= cookie('user_openid_new');
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        if($info){
            $id = $info['id'];
            if(!$info['sharetime']){
                $m = array();
                $m['id'] = $info['id'];
                $m['sharetime'] = time();
                M("womenwheel")->save($m);
            }
            M("womenwheel")->where(array('id' => $id))->setInc('share');
            echo 1;
        }else{
            echo 0;
        }

    }


    /*
     * 记录转发次数
     */
    public function saveShareNumberToFriends(){
        $this->setEndTime();
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        $userOpenId= cookie('user_openid_new');
        $info = M('womenwheel')->where(array('openid' => $userOpenId))->find();
        if($info){
            $id = $info['id'];
            if(!$info['sharetime']){
                $m = array();
                $m['id'] = $info['id'];
                $m['sharetime'] = time();
                M("womenwheel")->save($m);
            }
            M("womenwheel")->where(array('id' => $id))->setInc('share');
            echo 1;
        }else{
            echo 0;
        }

    }
    public function saveSharePoll(){
        M("womenwheel_polldata")->where(array('id' => 1))->setInc('sharenumber');
    }
    /*
    * 记录 我也要参加 次数
    */
    public function saveWantJoin(){
        $uid = $_POST['uid'];
        M("womenwheel")->where(array('id' => $uid ))->setInc('joins');
    }

    /*
    * 记录 帮忙投票 次数
    */
    public function saveHelpVote(){
        $this->setEndTime();
        $return = 0;
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        //帮忙投票 点击次数
        $toOpenId = $_POST['toopenid'];
        $uid = $_POST['uid'];
        $userOpenId = cookie('user_openid_new');
        $voteList = M('womenwheel_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$toOpenId))->find();

        //多次投票开启
        if(!$voteList){
//        if(true){
        //多次投票结束
            $info = M('womenwheel')->where(array('id' => $uid))->find();
            M("womenwheel")->where(array('id' => $info['id']))->setInc('vote');

            //投票
            $d = array();
            $d['fromopenid'] = $userOpenId;
            $d['toopenid'] = $toOpenId;
            $d['sequence'] = $info['sequence'];
            $d['createtime'] = time();
            M('womenwheel_votelist')->add($d);
            $return = 1;
        }
        echo $return;
    }

    public function auth(){
        if($this->debug){
            return;
        }


        //判断是否在微信打开
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
        //获取OPENID
        $appid = "wx36026301d4b1cb01";
        $appsecret = "79311ea02ea318af5f228492bf119104";

        $openId = $_COOKIE['openid'];
        $url = $this->url."/index.php?g=Wap&m=Womenwheel&a=index";
        if(!$openId){
            $code =  $_GET['code'];
            $state = $_GET['state'];
            if ($code && $state == 'sentian') {
                $userinfoFromApi = $this->getUserInfo($code, $appid, $appsecret);
                if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                    //code 有错误 需要重定向
                    header("location:$url");
                }
                $web_access_token = $userinfoFromApi['access_token'];
                $openId = $userinfoFromApi['openid'];
                setcookie('openid', $openId, time()+3600*24*100);

            } else {
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }

        if(!$openId){
            //
            exit('此功能只能在微信浏览器中使用') ;
        }
        $strToTime = strtotime('2015-08-08 24:00:00');
        if(time() > $strToTime){
            exit('<center>游戏已经结束！谢谢你的参与</center>');
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

    public function getOpenId(){
        $userOpenId = null;
        $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $code = trim($_GET["code"]);
        $state = trim($_GET['state']);

        $fansInfo = M('customer_service_fans')->field('openid,nickname')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if ($code && $state == 'sentian') {
            if(empty($fansInfo)){
                $webCreatetime = $apidata['web_createtime'];
                $web_access_token = '';

                    //重新获取
                    $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                    if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                        //code 有错误 需要重定向
                        $url = $this->url."/index.php?g=Wap&m=Womenwheel&a=getOpenId";
                        header("location:$url");
                    }
                    $m['id'] = $apidata['id'];
                    $m['web_access_token'] = $userinfoFromApi['access_token'];
                    $m['refresh_token'] = $userinfoFromApi['refresh_token'];
                    $m['web_createtime'] = time();
                    $m['refresh_token_createtime'] = time();
                    M('Diymen_set')->save($m);
                    $web_access_token = $userinfoFromApi['access_token'];
                    cookie('user_openid_new', $userinfoFromApi['openid'], 315360000);
                    $userOpenId = $userinfoFromApi['openid'];

            }
        } else {
            $url = urlencode($this->url."/index.php?g=Wap&m=Womenwheel&a=getOpenId");
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
            exit;
        }
    }


}
