<?php

class XiezhuangAction extends SjzAction {
    public $title = '赢新品试用大奖！轻轻一“拭”,开启便捷卸妆体验';
    public $bonusdesc = '肌肤保养从卸妆开始，森田药妆®[玻尿酸卸妆棉]新品上市！';
    public $eachVote = 10;
    public $imageUrl;
    public $shareImageUrl;
    public $endtime="2016-09-23 23:59:59"; //活动结束时间
    public $debug = true; //上线后应该改成false
    public $defalutGid = 110;
    public $xiezhuangCount = 20;

    public function _initialize() {
        parent :: _initialize();
        $this->url= C('site_url');
        $this->imageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/xiezhuang/images/logo1.jpg';
        $this->shareImageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/xiezhuang/images/logo1.jpg';

        $ip=get_client_ip();
        $userOpenId= cookie('user_openid');
        if($userOpenId){
            $uid = $this->getUidByOpenid($userOpenId);
            if($uid){
                //判断此IP是否访问过
                $id = M('xiezhuang_iplist')->where("ip='$ip'")->getField('id');
                if(!$id){
                    $n = array();
                    $n['uid'] = $uid;
                    $n['ip'] = $ip;
                    M('xiezhuang_iplist')->add($n);
                }
            }
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
            $userOpenId= cookie('user_openid');
            $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
            $mainId = $info['id'];
            $mainGid = $info['gid'];
        }
        return $this->url."/index.php?g=Wap&m=Xiezhuang&a=sharefriend&uid=".$mainId."&gid=$mainGid";
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
        if($gid < 110 || $gid > 120){
            Log :: write( $openId."  ".$nickname." $gid 不存在" ,'ERR','','test.log');
            return null;
        }
        //首先查看此OPENID 是否存在 无论gid
        $bonusInfo = M('xiezhuang')->where(array('openid' => $openId))->find();
        $lastInsertId = null;
        if(!$bonusInfo){
            //创建个人主页
            $d['gid'] = $gid;
            $d['openid'] = $openId;
            $d['name'] = $nickname;
            $d['headimgurl'] = $imageProfile;
            $d['views'] = 1;
            $d['createtime'] = time();
            $lastInsertId = M("xiezhuang")->add($d);
        }
        return $lastInsertId;
    }
    public function setEndTime2(){
        $endtime =strtotime( "2016-09-25 23:59:59" );
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
        $userOpenId= cookie('user_openid');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $fansInfo = null;
        $selfUserInfo = array();
        $fansInfo = M('customer_service_fans')->field('openid,nickname,headimgurl')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
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

                    if(false){//$webCreatetime>(time()-7200) && $userOpenId
                        //未过期
                        //$web_access_token = $apidata['web_access_token'];
                    }else{
                        //重新获取
                        $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                        if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                            //code 有错误 需要重定向
                            $url = $this->url."/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid";
                            header("location:$url");
                        }
//                        $m['id'] = $apidata['id'];
//                        $m['web_access_token'] = $userinfoFromApi['access_token'];
//                        $m['refresh_token'] = $userinfoFromApi['refresh_token'];
//                        $m['web_createtime'] = time();
//                        $m['refresh_token_createtime'] = time();
//                        M('Diymen_set')->save($m);
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
                $url = urlencode($this->url."/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                exit;
            }
        }




        $nickname = $selfUserInfo['nickname'];
        $imageProfile = $selfUserInfo['headimgurl'];

        //首先判断当前用户是否有玩过第一次
        $info = M('Xiezhuang')->where(array('openid' => $userOpenId))->find();
        $vote = null;
        $lastInsertId = null;
        $views = null;
        if($info){
            $lastInsertId = $info['id'];
            $vote = $info['vote'];
            $views = $info['views'];
        }

        if($vote>=1){

        }else{
            if($views == 0){
                //此用户不存在
                $lastInsertId = $this->saveInfo($gid,$userOpenId,$nickname,$imageProfile);
            }
        }

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
        $this->display();
    }

    public function game(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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

        $this->setIncViews($info['id']);
        //判断用户是否上传过图片
        $savePath = './PUBLIC/imagess/';
        $t = $info['uploadimagetime'];
        $uploadImageSrc = $savePath ."$userOpenId"."_$t".".jpeg";
        $uploadImage = 0;
        if(file_exists($uploadImageSrc)){
            $uploadImage = 1;
        }
        $this->assign("uploadimage",$uploadImage);
        $this->assign('uploadimagesrc',$uploadImageSrc);
        $this->assign("gid",$gid);
        $this->display();
    }

//    public function saveImage(){
//        $img = $_POST['image'];
//        $savePath = './PUBLIC/imagess/';
//
//        $base64_body = substr(strstr($img,','),1);
//        $userOpenId= cookie('user_openid');
//
//        $data= base64_decode($base64_body );
//
//
//        //保存uploadimagetime
//        $uid = $this->getUidByOpenid($userOpenId);
//        $t = time();
//        if($uid){
//            $n = array();
//            $n['id'] = $uid;
//            $n['uploadimagetime'] = time();
//            M("xiezhuang")->save($n);
//        }
//        $file = $savePath ."$userOpenId"."_$t".".jpeg";
//       echo  file_put_contents($file, $data);
//    }

    public function game1(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
      //  $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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
        if($info){
            $this->setIncViews($info['id']);
        }

        $vote = $info['vote'];
        $leftVote = $this->xiezhuangCount  - $vote;
        if($leftVote<=0){
            $leftVote = 0;
        }
        // end views
        $this->assign("gid",$gid);
        $this->assign("leftvote",$leftVote);
        $this->display();
    }

    public function success(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        //  $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
            exit();
        }

        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        //begin views
        if($info){
            $this->setIncViews($info['id']);
        }

        $getPost = null;
        if(isset($_GET['post'])  && $_GET['post'] == 1   ){
            //是否满足20票
            if($info['vote'] < $this->xiezhuangCount){
                exit;
            }
            //更新phone
            //更新表 xiezhuang
            $id = $info['id'];
            if($info['phone'] != 1){
                $m = array();
                $t = time();
                $m['phone'] = 1;
                $m['id'] = $id;
                $m['phonetime'] = $t;
                M('xiezhuang')->save($m);
            }

            //插入表 xiezhuang_phonelist
            $phoneList = M('xiezhuang_phonelist')->where(array('uid' => $id))->find();
            if(!$phoneList){
                $n = array();
                $n['uid'] = $id;
                $n['phone'] = 1;
                $n['createtime'] = time();
                M('xiezhuang_phonelist')->add($n);
            }
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




        $this->display();
    }

    public function rule(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
//        $userOpenId= "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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
        $userOpenId= cookie('user_openid');
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
        $userOpenId= cookie('user_openid');
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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
        $userOpenId= cookie('user_openid');
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views
        $this->assign("gid",$gid);
        $this->display();
    }
    public function share(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }
        $gid = $info['gid'];
        //图片是否存在
//        $savePath = './PUBLIC/imagess/';
//        $t = $info['uploadimagetime'];
//        $uploadImageSrc= $savePath."$userOpenId"."_$t".".jpeg";
//        if(!file_exists($uploadImageSrc)){
//            //redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
//            exit();
//        }
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
        $m = array();
        $m['id'] = $info['id'];
        $m['click'] = 1;
        M('xiezhuang')->save($m);
        //begin views
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views


        //获取当前已经有了多少拼图
        $imgNums = (int)$this->xiezhuangCount;
        $vote = $info['vote'];
        $share = $info['share'];
        $imgNums = (int)$this->xiezhuangCount - $vote;
        if($vote >= $this->xiezhuangCount && !$info['phone']){
            //这里有错误，需要报错，不应该达到票数 但是没有名词
//            跳转到sharephone
//            redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
//            exit();
        }
        if($vote >= $this->xiezhuangCount && $info['phone']){
            //form 信息是否已经提交
            $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
            if($award){
                //已经提交
                header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
                exit();
            }else{
                header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
                exit();
            }

        }

        if($imgNums < 0 ){
            $imgNums = 0;
        }
        $this->assign('imgnums',$imgNums);
        if($vote == 0){
            $vote = 0;
        }
        $this->assign('needimgnums',$vote);

        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
        $start = date("Y-m-d H:i:s",$start );
        $end = date("Y-m-d H:i:s",$end );
//        $uniqueViewSql = "SELECT * from tp_xiezhuang_uniqueviewlist where   createtime >= '$start' and createtime<'$end' and fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewSql = "SELECT * from tp_xiezhuang_votelist where  fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewlist = M('xiezhuang_votelist')->query($uniqueViewSql);
        $haveVoted = 0;

        //多次投票开启
        if($uniqueViewlist){
            $haveVoted = 1;
            //自己已经给自己投过票，但是未满20票 跳转到分享引导页
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share2&gid=$gid");
            exit();
        }
        //多次投票结束

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
        $this->display();
    }

    public function share2(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }
        $gid = $info['gid'];
        //图片是否存在
//        $savePath = './PUBLIC/imagess/';
//        $t = $info['uploadimagetime'];
//        $uploadImageSrc= $savePath."$userOpenId"."_$t".".jpeg";
//        if(!file_exists($uploadImageSrc)){
//            //redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
//            exit();
//        }
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


        //获取当前已经有了多少拼图
        $imgNums = (int)$this->xiezhuangCount;
        $vote = $info['vote'];
        $share = $info['share'];
        $imgNums = (int)$this->xiezhuangCount - $vote;
        if($vote >= $this->xiezhuangCount && !$info['phone']){
//            跳转到sharephone
//            redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
//            exit();
        }
        if($vote >= $this->xiezhuangCount && $info['phone']){
            //form 信息是否已经提交
            $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
            if($award){
                //已经提交
                header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
                exit();
            }else{
                header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
                exit();
            }

        }

        if($imgNums < 0 ){
            $imgNums = 0;
        }
        $this->assign('imgnums',$imgNums);
        if($vote == 0){
            $vote = 0;
        }
        $this->assign('needimgnums',$vote);

        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
        $start = date("Y-m-d H:i:s",$start );
        $end = date("Y-m-d H:i:s",$end );
//        $uniqueViewSql = "SELECT * from tp_xiezhuang_uniqueviewlist where   createtime >= '$start' and createtime<'$end' and fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewSql = "SELECT * from tp_xiezhuang_votelist where  fromopenid='$userOpenId' and toopenid='$userOpenId'";
        $uniqueViewlist = M('xiezhuang_votelist')->query($uniqueViewSql);
        $haveVoted = 0;

        //多次投票开启
        if($uniqueViewlist){
            $haveVoted = 1;
            //自己已经给自己投过票，但是未满20票 跳转到分享引导页
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=game1&gid=$gid");
//            exit();
        }
        //多次投票结束

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
        $this->display();
    }

    public function sharefriend(){
        $this->setEndTime();
        //这里是隐性获取OPENID 是朋友圈里面的人打开这个页面
        //获取OPENID 用户没有感知
        $userOpenId= cookie('user_openid');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        $uid = $_GET['uid'];
        if(!is_numeric($uid)){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
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
                        $url = $this->url."/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid";
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
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Xiezhuang&a=sharefriend&uid=$uid&gid=$gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }

        $info = M('xiezhuang')->where(array('id' => $uid))->find();
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        if(!$gid && isset($info['gid']) && $info['gid'] ){
            $gid = $info['gid'];
        }
        $MainOpenId = $info['openid'];
        $share = $info['share'];
        //图片是否存在
//        $savePath = './PUBLIC/imagess/';
//        $t = $info['uploadimagetime'];
//        $uploadImageSrc= $savePath."$MainOpenId"."_$t".".jpeg";
//        if(!file_exists($uploadImageSrc)){
//            //redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
//            exit();
//        }
//        $this->assign('uploadimagesrc',$uploadImageSrc);

        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
        $start = date("Y-m-d H:i:s",$start );
        $end = date("Y-m-d H:i:s",$end );
        $uniqueViewSql = "SELECT * from tp_xiezhuang_uniqueviewlist where   createtime >= '$start' and createtime<'$end' and fromopenid='$userOpenId' and toopenid='$MainOpenId'";
        $uniqueViewlist = M('xiezhuang_uniqueviewlist')->query($uniqueViewSql);
        $haveVoted = 1;
        if($uniqueViewlist){
            //不需要增加uniqueviews
        }else{
            M("xiezhuang")->where(array('id' => $info['id']))->setInc('uniqueviews');
            $n = array();
            $n['fromopenid'] = $userOpenId;
            $n['toopenid'] = $MainOpenId;
            M('xiezhuang_uniqueviewlist')->add($n);
        }
        if($userOpenId == $MainOpenId){
            //自己访问自己的主页 跳转到share页面
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share&gid=$gid");
            exit();
        }
        $voteListSql = "SELECT * from tp_xiezhuang_votelist where fromopenid='$userOpenId' and toopenid='$MainOpenId'";
        $voteView = M('xiezhuang_votelist')->query($voteListSql);
        if($voteView){
            $haveVoted = 0;
            $infoLocal = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
            if($infoLocal){
                if($infoLocal['click'] == 1){
                    header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share&gid=$gid");
                    exit();
                }else{
                    header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
                    exit();
                }
            }

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

        //begin views
        if($info){
            $this->setIncViews($info['id']);
        }
        // end views




        //获取当前已经有了多少拼图
        $imgNums = (int)$this->xiezhuangCount;
        $vote = $info['vote'];
        if($vote >= $this->xiezhuangCount && $userOpenId != $MainOpenId ){
            //当前主页已经为20票， 跳转首页
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        $imgNums = (int)$this->xiezhuangCount - $vote;
        if($vote >= $this->xiezhuangCount && $userOpenId==$MainOpenId){
            //跳转到sharephone
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=sharephone&gid=$gid");
            exit();
        }
        if($imgNums < 0 ){
            $imgNums = 0;
        }
        $this->assign('imgnums',$imgNums);
        $this->assign('needimgnums',$vote);
        $this->assign('openid',$userOpenId);
        $this->assign('mainopenid',$MainOpenId);

        $this->assign('uid',$uid);
        $this->assign('gid',$gid);

        //判断当前用户是否已经投过票
        $voteList = M('xiezhuang_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$MainOpenId  ))->find();
        $voteThisUid = 0;
        if($voteList){
            $voteThisUid = 1;
        }

        //判断COOKIE用户是否参加过活动
        //如果参见过活动，直接跳转到互动页面，否则是首页
        $cookieId = M('xiezhuang')->where("openid='$userOpenId'")->getField('id');
        $cookieJoin = 0;
        if($cookieId){
            $cookieJoin = 1;
        }

        $this->assign('sharenumberindatabase',$share);
        $this->assign('cookiejoin',$cookieJoin);
        $this->assign('votetothisuid',$voteThisUid);


        $this->assign('havevoted',$haveVoted);
        $this->assign("gid",$gid);
        $this->display();
    }

    public function vote(){
        $this->setEndTime2();
        $userOpenId= cookie('user_openid');
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
//        $userOpenId= 'oP9fCt-JvXkTShBQIin7jtYF0i6U';
        M("xiezhuang_polldata")->where(array('id' => 1))->setInc('pv');
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
                        $url = $this->url."/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid";
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
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Xiezhuang&a=vote&gid=$gid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }


        //begin 分享出去的URL
        $gid = 22;
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
        $this->assign("shareurl",$this->url."/index.php?g=Wap&m=Xiezhuang&a=vote");
        $this->assign('gid', $gid);

        $this->assign('title','“携手森田.找回美丽”第二阶段票选活动');
        $this->assign('bonusdesc','朋友们快来帮我投票吧！');
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        $list = M('xiezhuang_poll')->query( "select * from tp_xiezhuang_poll order by id asc") ;
        $slist = array();
        $savePath = './PUBLIC/imagess/';
        foreach($list as $each){
            $tmp = array();
            $id = $each['uid'];
            $info = M('xiezhuang')->where(array('id' => $id))->find();
            $tmp['name'] = $info['name'];

            $openid = $info['openid'];
            $t = $info['uploadimagetime'];
            $uploadImageSrc= $savePath."$openid"."_$t".".jpeg";
            $tmp['imgsrc'] = $uploadImageSrc;

            $tmp['vote'] = $each['vote'];

            $tmp['id'] = $each['id'];
            $tmp['uid'] = $each['uid'];
            $slist[] = $tmp;

        }
        $this->assign('info', $slist);
        $this->assign("gid",$gid);
        $this->display();
    }
    public function sharePhone(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        $vote = $info['vote'];
//        if($vote >= $this->xiezhuangCount && isset($info['phone']) && $info['phone']){
//            //redirect
//            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share");
//            exit();
//        }

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

        $vote = $info['vote'];
        if($vote >= $this->xiezhuangCount){
            //成功，继续提交手机号
        }else{
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        $phoneExist = 0;
        $paiming = null;
        if($info['phone']){
            //排名已经获取，根据UID获取排名
            $phoneExist = 1;
            $paimingArray = M('xiezhuang_phonelist')->where(array('uid' => $info['id']))->find();
            $paiming = $paimingArray['id'];
        }else{
            //跳转
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share&gid=$gid");
            exit();
        }
        $this->assign('paiming',$paiming);
        $this->assign('phone',$info['phone']);
        $this->assign('phoneexist',$phoneExist);
        $this->assign('uid',$info['id']);
        $this->assign("gid",$gid);
        $this->display();
    }
    //根据UID获取OPENID
    public function getOpenIdByUid($uid){
        $openId = M('xiezhuang')->where("id=$uid")->getField('openid');
        return $openId;
    }
    public function getUidByOpenid($openid){
        $uid = M('xiezhuang')->where("openid='$openid'")->getField('id');
        return $uid;
    }
    public function savePhoneInPage(){
        $this->setEndTime();
        $phone = $_POST['phoneinpage'];
        $fromOpenIdFromPost= cookie('user_openid');
        if(!$fromOpenIdFromPost){
            //非法投票
            exit();
        }
        //查看当前提交了手机号数码
//        $countNum = M('xiezhuang')->where("phone != ''")->count('id');
//        if($countNum >= 10000){
//            echo 2;
//            return;
//            exit();
//        }
        $id = M('xiezhuang')->where("openid='$fromOpenIdFromPost'")->getField('id');
        //判断此用户是否已经提交了手机号
        $phoneList = M('xiezhuang_phonelist')->where(array('uid' => $id))->find();
        if($phoneList){
            echo 3;
            //已经存在，不需要保存
        }else{
            //更新表 xiezhuang
            $m = array();
            $t = time();
            $m['phone'] = $phone;
            $m['id'] =$id;
            $m['phonetime'] = $t;
            M('xiezhuang')->save($m);

            //插入表 xiezhuang_phonelist
            $n = array();
            $n['uid'] = $id;
            $n['phone'] = $phone;
            $n['createtime'] = $t;
            M('xiezhuang_phonelist')->add($n);
            echo 1;
        }





    }
    public function saveVote2(){
        $this->setEndTime();
        $return = 0;
        $fromOpenIdFromPost= cookie('user_openid');
//        $fromOpenIdFromPost = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $toUid = $_POST['uid'];
        if(!$fromOpenIdFromPost){
            //非法投票
            exit();
        }

        $toOpenIdFromPost = $this->getOpenIdByUid($toUid);
        //检查此 local openid 是否投过票
        $voteList = M('xiezhuang_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        //多次投票
        if(!$voteList){
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['createtime'] = time();
            M('xiezhuang_votelist')->add($d);
            M("xiezhuang")->where(array('openid' => $toOpenIdFromPost))->setInc('vote');
            $return = 1;
            //是否已经有20票
            $info = M('xiezhuang')->where(array('id' => $toUid))->find();
            if($info['vote'] >= $this->xiezhuangCount  && !$info['phone']){
                $pUid = $info['id'];
                $phoneList = M('xiezhuang_phonelist')->where(array('uid' => $pUid))->find();
                if(!$phoneList){
                    $p = array();
                    $p['uid'] =  $info['id'];
                    $p['phone'] =  1;
                    $p['createtime'] =  time();
                    $paiming = M('xiezhuang_phonelist')->add($p);
                    $i = array();
                    $i['id'] = $info['id'];
                    $i['phone'] = 1;
                    M('xiezhuang')->save($i);
                }
            }
        }else{
            //已经投过票
            $return = 2;
        }
        echo $return;
    }
    //TODO add random str to avoid auto submit
    public function saveVote(){
        $this->setEndTime();
        $return = 0;
        $fromOpenIdFromPost= cookie('user_openid');
//        $fromOpenIdFromPost = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        $toOpenIdFromPost = $fromOpenIdFromPost;
        //检查此 local openid 是否投过票
        $voteList = M('xiezhuang_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        if(!$voteList){//
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['createtime'] = time();
            M('xiezhuang_votelist')->add($d);
            M("xiezhuang")->where(array('openid' => $toOpenIdFromPost))->setInc('vote');
            $info = M('xiezhuang')->where(array('openid' => $fromOpenIdFromPost))->find();
            if($info['vote'] >= $this->xiezhuangCount  && !$info['phone']){
                $pUid = $info['id'];
                $phoneList = M('xiezhuang_phonelist')->where(array('uid' => $pUid))->find();
                if(!$phoneList){
                    $p = array();
                    $p['uid'] =  $info['id'];
                    $p['phone'] =  1;
                    $p['createtime'] =  time();
                    $paiming = M('xiezhuang_phonelist')->add($p);
                    $i = array();
                    $i['id'] = $info['id'];
                    $i['phone'] = 1;
                    M('xiezhuang')->save($i);
                }
            }
            $return = 1;
        }else{
            //已经投过票
            $return = 2;
        }

        echo $return;
    }
    public function savePoll(){
        $this->setEndTime2();
        $return = 0;
        $fromOpenIdFromPost= cookie('user_openid');
//        $fromOpenIdFromPost= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $toId = $_POST['id'];
        if(!$toId){
            exit();
        }
        //根据ID取得UID
        $uid = M('xiezhuang_poll')->where("id=$toId")->getField('uid');
        if(!$uid){
            exit();
        }
        $userInfo = M('xiezhuang')->where("id=$uid")->find();
        if(!$userInfo){
            //非法投票
            exit();
        }

        $toOpenIdFromPost = $userInfo['openid'];
        //检查此 local openid 是否投过票
        //当天是否访问过
        $today = time();
        $start = mktime(0,0,0,date("m",$today),date("d",$today),date("Y",$today));
        $end = mktime(23,59,59,date("m",$today),date("d",$today),date("Y",$today));
//        $start = date("Y-m-d H:i:s",$start );
//        $end = date("Y-m-d H:i:s",$end );

        $sql = "SELECT id from tp_xiezhuang_polllist where   createtime >= '$start' and createtime<'$end' and fromopenid='$fromOpenIdFromPost'";
        $voteList = M('xiezhuang_polllist')->query($sql);
//        $voteList = M('xiezhuang_polllist')->where(array('fromopenid' => $fromOpenIdFromPost, ))->find();//'toopenid'=>$toOpenIdFromPost
        if(!$voteList){//
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['createtime'] = time();
            M('xiezhuang_polllist')->add($d);
            M("xiezhuang_poll")->where(array('id' => $toId))->setInc('vote');
            $return = 1;
        }else{
            //已经投过票
            $return = 2;
        }

        echo $return;
    }
    public function saveFormInfo(){
        $this->setEndTime2();
        $return = 0;
        $userOpenId= cookie('user_openid');
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
        $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
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
            M('xiezhuang_award')->add($m);
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
                M('xiezhuang_award')->save($m);
                $return = 1;
        }
    }
    public function rank(){
        $userOpenId= cookie('user_openid');
        //        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index");
            exit();
        }
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$info){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }

        //如果没有满足16票 跳转到再接再厉
        if($info['vote'] <$this->xiezhuangCount){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=rank1&gid=$gid");
            exit();
        }

        if($info && !$info['phone']){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share&gid=$gid");
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
        $pValue = M('xiezhuang_phonelist')->where(array('uid' => $uid))->getField('id');
        $vote = $this->xiezhuangCount;
        $this->assign("vote",$vote);
        $this->assign("selforder",$pValue);
        $this->assign("paiming",$pValue);
        $this->assign("gid",$gid);
        $this->display();
    }

    public function form(){
        $this->setEndTime2();
        $userOpenId= cookie('user_openid');
//        $userOpenId= 'oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = $this->defalutGid;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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
        if($vote < $this->xiezhuangCount){
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=index&gid=$gid");
            exit();
        }
        if(!$info['phone']){
            header("location:$this->url/index.php?g=Wap&m=Xiezhuang&a=share&gid=$gid");
            exit();
        }

        $this->assign("vote",$vote);
        $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
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
        $this->display();
    }

    public function getOrderByOpenId($openId=null){
        $list = M('xiezhuang')->query("select openid, number,share,phonetime from tp_xiezhuang where phone != '' order by number desc,share desc,phonetime asc ");

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
        $userOpenId= cookie('user_openid');
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
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
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

        $this->assign('selfpage',$this->url."/index.php?g=Wap&m=Xiezhuang&a=sharefriend&gid=$gid");

        $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
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
        M("xiezhuang")->where(array('id' => $id))->setInc('views');
        $m = array();
        $m['uid'] = $id;
        M('xiezhuang_viewlist')->add($m);
    }

    public function  saveAward(){
        $return = 0;
        $userOpenId= cookie('user_openid');
        if(!$userOpenId){
            echo 0;
            return;
        }
        $award = M('xiezhuang_award')->where(array('openid' => $userOpenId))->find();
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
            M('xiezhuang_award')->add($m);
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
            M('xiezhuang_award')->save($m);
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
        $userOpenId= cookie('user_openid');
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        if($info){
            $id = $info['id'];
            if(!$info['sharetime']){
                $m = array();
                $m['id'] = $info['id'];
                $m['sharetime'] = time();
                M("xiezhuang")->save($m);
            }
            M("xiezhuang")->where(array('id' => $id))->setInc('share');
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
        $userOpenId= cookie('user_openid');
        $info = M('xiezhuang')->where(array('openid' => $userOpenId))->find();
        if($info){
            $id = $info['id'];
            if(!$info['sharetime']){
                $m = array();
                $m['id'] = $info['id'];
                $m['sharetime'] = time();
                M("xiezhuang")->save($m);
            }
            M("xiezhuang")->where(array('id' => $id))->setInc('share');
            echo 1;
        }else{
            echo 0;
        }

    }
    public function saveSharePoll(){
        M("xiezhuang_polldata")->where(array('id' => 1))->setInc('sharenumber');
    }
    /*
    * 记录 我也要参加 次数
    */
    public function saveWantJoin(){
        $uid = $_POST['uid'];
        M("xiezhuang")->where(array('id' => $uid ))->setInc('joins');
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
        $userOpenId = cookie('user_openid');
        $voteList = M('xiezhuang_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$toOpenId))->find();

        //多次投票开启
        if(!$voteList){
//        if(true){
        //多次投票结束
            $info = M('xiezhuang')->where(array('id' => $uid))->find();
            M("xiezhuang")->where(array('id' => $info['id']))->setInc('vote');

            //投票
            $d = array();
            $d['fromopenid'] = $userOpenId;
            $d['toopenid'] = $toOpenId;
            $d['sequence'] = $info['sequence'];
            $d['createtime'] = time();
            M('xiezhuang_votelist')->add($d);
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
        $url = $this->url."/index.php?g=Wap&m=Xiezhuang&a=index";
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
                        $url = $this->url."/index.php?g=Wap&m=Xiezhuang&a=getOpenId";
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
        } else {
            $url = urlencode($this->url."/index.php?g=Wap&m=Xiezhuang&a=getOpenId");
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
            exit;
        }
    }


}
