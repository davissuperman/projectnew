<?php

class PrettyAction extends SjzAction {
    public $title = '携手森田.找回美丽';
    public $bonusdesc = '只要30票， 人人拉得到，比手快不比票多，7500片免费面膜等你来拿！';
    public $eachVote = 10;
    public $imageUrl;
    public $shareImageUrl;
    public $endtime="2015-10-21 24:00:00"; //活动结束时间
    public $debug = true; //上线后应该改成false

    public function _initialize() {
        parent :: _initialize();
        $this->url= C('site_url');
        $this->imageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/present/images/logo1.jpg';
        $this->shareImageUrl = "http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/present/images/logo1.jpg';
    }

    public function getShareUrl(){
        $userOpenId= cookie('user_openid');
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
        if($info){
            return $this->url."/index.php?g=Wap&m=Pretty&a=sharefriend&uid=".$info['id']."&gid=$gid";
        }else{
            return $this->url."/index.php?g=Wap&m=Pretty&a=index&gid=$gid";
        }
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
            $d['sequence'] = 0;
            $d['createtime'] = time();
            M("pretty")->add($d);
        }
    }

        public function setEndTime(){
            $endtime =strtotime( $this->endtime );
            if (time() > $endtime) {//活动是否结束

                echo <<<HTML
                <center>
<h1>活动已经结束！</h1>
<h2>
登记收货地址信息截止至
7月26日24：00<br/>
登记方法：<br/>
在森田微信服务号上点击
”查询排名“菜单查看排名，<br/>
点击“填写/查询领奖信息”
请认真填写您的收货信息！<br/>
我们将于8月初开始寄送奖品！
</h2>
</center>
HTML;

                exit();
            }
        }

        public function index() {
        $gid = $_GET['gid'];
        if(!$gid){
            $gid = 1;
        }
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        $userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
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
                            $url = $this->url."/index.php?g=Wap&m=Pretty&a=index&gid=$gid";
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
                $url = urlencode($this->url."/index.php?g=Wap&m=Pretty&a=index");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                exit;
            }
        }




            $nickname = $selfUserInfo['nickname'];
            $imageProfile = $selfUserInfo['headimgurl'];

        //首先判断当前用户是否有玩过第一次
        $info = M('Pretty')->where(array('openid' => $userOpenId))->find();
//        $firstStart = true;
//        if($info){
//                //判断当前用户 sequence 并且 手机已经提交,并且判断用户是否有资格继续玩
//            $vote = $info['vote'];
//            $phone = $info['phone'];
//            $currentSequence = $info['sequence'];
//            if($phone && ($currentSequence>=4 ||  $vote<$this->eachVote || floor($vote/$this->eachVote ) <= $currentSequence)){
//                //没有资格继续完
//                $firstStart = false;
//            }
//            if($firstStart == true){
//                //判断当前机会是否已经使用过
//                $infoList = M('Pretty_list')->where(array('openid' => $userOpenId,'sequence' => $currentSequence))->find();
//                if($infoList && $infoList['number']){
//                    //已经玩过
//                    $firstStart = false;
//                }
//            }
//
//        }else{
//            $this->saveInfo($gid,$userOpenId,$nickname,$imageProfile);
//        }



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

        $urlGame = "http://wx.drjou.cc" ."/index.php?g=Wap&m=Pretty&a=game";
//        if($firstStart == false){
//            $urlGame = "http://wx.drjou.cc"."/index.php?g=Wap&m=Pretty&a=sharenumber";
//        }
        $this->assign('urlgame',$urlGame);

        //view自增
        M("pretty")->where(array('id' => $info['id']))->setInc('views');

        $this->display();
    }

    public function upload(){
//        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        $userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
        $gid = $info['gid'];
//        $firstStart = true;
//        if($info){
//            $phone = $info['phone'];
//            //判断当前用户 sequence
//            $currentSequence = $info['sequence'];
//            //同时判断此用户是否有再玩一次的机会
//            $vote = $info['vote'];
//            //是否有机会 并且判断是否已经玩过
//
//            if($phone && ( $vote<$this->eachVote || floor($vote/$this->eachVote ) < $currentSequence)){
//                $firstStart = false;
//                header("location:$this->url/index.php?g=Wap&m=Pretty&a=sharenumber&gid=$gid");
//                exit();
//            }
//
//            //判断当前机会是否已经使用过
//            $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence' => $currentSequence))->find();
//            if($infoList && $infoList['number']){
//                //已经玩过
//                header("location:$this->url/index.php?g=Wap&m=Pretty&a=sharenumber");
//                exit();
//            }
//
//        }else{
//            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
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
        M("pretty")->where(array('id' => $info['id']))->setInc('views');
        $this->display();
    }

    public function saveImage(){
        $img = $_POST['image'];
        $savePath = './PUBLIC/imagess/';

        $base64_body = substr(strstr($img,','),1);
        $userOpenId= cookie('user_openid');
        $userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';

        $data= base64_decode($base64_body );
        $file = $savePath ."$userOpenId.jpeg";

       echo  file_put_contents($file, $data);
    }

    public function rule(){
        $userOpenId= cookie('user_openid');
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
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
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views

        $this->display();
    }

    public function share(){
        $userOpenId= cookie('user_openid');
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index");
            exit();
        }

        //首先判断当前用户是否有玩过第一次
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
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
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views

        $this->display();
    }


    public function score(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        $number = $_GET['number'];
        if(!$userOpenId || !$number || ($number > 200)){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index");
            return;
        }
        $info = M('countmask')->where(array('openid' => $userOpenId))->find();
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


        $phone = $info['phone'];
        $currentSequence = $info['sequence'];
        if(!$phone){
            $currentSequence = 0;
        }

        switch($currentSequence){
            case 0:
                $d = array();
                $d['sequence'] = 1;
                $d['number'] = $number;
                $d['id'] = $info['id'];
                M('countmask')->save($d);

                $list = M('countmask_list')->where(array('openid' => $userOpenId,'sequence' => 0))->find();
                if(!$list){
                    $l = array();
                    $l['openid'] = $userOpenId;
                    $l['number'] = $number;
                    $l['sequence'] = 0;// default
                    $l['createtime'] = time();
                    $l['vote'] = 0;//default
                    M('countmask_list')->add($l);
                }else{
                    //更新
                    $l = array();
                    $l['id'] = $list['id'];
                    $l['number'] = $number;
                    $l['sequence'] = 0;
                    M('countmask_list')->save($l);
                }
                break;
            case 1://第一次满10票后
                //判断用户是否满足条件
                $vote = $info['vote'];
                if($vote < $this->eachVote){
                    //非法提交 转到首页
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    return;
                }
                $infoList = M('countmask_list')->where(array('openid' => $userOpenId,'sequence'=>1))->find();
                if($infoList && $infoList['number']){
                    //已经提交分数 无法再次提交
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    exit();
                }
                M("pretty")->where(array('id' =>$info['id']))->setInc('number', $number);
                //更新sequence
                M("pretty")->where(array('id' =>$info['id']))->setInc('sequence');
                if($infoList){
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('pretty_list')->save($m);
                }else{
                    $m = array();
                    $m['number'] = $number;
                    $m['openid'] = $userOpenId;
                    $m['createtime'] = time();
                    $m['updatetime'] = time();
                    $m['sequence'] = 1;
                    M('pretty_list')->add($m);
                }
                break;
            case 2://第二次满10票后
                //判断用户是否满足条件
                $vote = $info['vote'];
                if($vote < $this->eachVote * 2){
                    //非法提交 转到首页
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    return;
                }
                $infoList = M('pretty_list')->where(array('openid' => $userOpenId,'sequence'=>2))->find();
                if($infoList && $infoList['number']){
                    //已经提交分数 无法再次提交
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    exit;
                }
                M("pretty")->where(array('id' =>$info['id']))->setInc('number', $number);
                //更新sequence
                M("pretty")->where(array('id' =>$info['id']))->setInc('sequence');
                if($infoList){
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('pretty_list')->save($m);
                }else{
                    $m = array();
                    $m['number'] = $number;
                    $m['openid'] = $userOpenId;
                    $m['createtime'] = time();
                    $m['updatetime'] = time();
                    $m['sequence'] = 2;
                    M('pretty_list')->add($m);
                }
                break;
            case 3://第三次满10票后
                //判断用户是否满足条件
                $vote = $info['vote'];
                if($vote < $this->eachVote * 3){
                    //非法提交 转到首页
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    return;
                }
                $infoList = M('pretty_list')->where(array('openid' => $userOpenId,'sequence'=>3))->find();
                if($infoList && $infoList['number']){
                    //已经提交分数 无法再次提交
                    header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
                    return;
                }
                M("pretty")->where(array('id' =>$info['id']))->setInc('number', $number);
                //更新sequence
                M("pretty")->where(array('id' =>$info['id']))->setInc('sequence');
                if($infoList){
                    $m = array();
                    $m['number'] = $number;
                    $m['id'] = $infoList['id'];
                    $m['updatetime'] = time();
                    M('pretty_list')->save($m);
                }else{
                    $m = array();
                    $m['number'] = $number;
                    $m['openid'] = $userOpenId;
                    $m['createtime'] = time();
                    $m['updatetime'] = time();
                    $m['sequence'] = 3;
                    M('pretty_list')->add($m);
                }
                break;
            default:
                //没有机会再次增加分数
        }

        $this->assign('number',$number);

        //同时将数据保存到tp_pretty_list
        //判断是否存在default的记录


        $phone = false;
        if($info['phone']){
            $phone = $info['phone'];
        }
        $this->assign('phone', $phone);
        if($currentSequence == 0){
            $oportunityleft = 3;
        }else{
            $oportunityleft = 3-$currentSequence;
        }
        if($oportunityleft<0){
            $oportunityleft = 0;
        }
        $this->assign('oportunityleft', $oportunityleft);

        //begin views
        if($info){
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views
        $this->display();
    }
    public function sharenumber(){
//        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        //$userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $phone = $_GET['phone'];
        $info = M('pretty')->where(array('openid' => $userOpenId))->find();
        if($_GET['gid']){
            $gid =  $_GET['gid'];
        }elseif($info){
            $gid = $info['gid'];
        }else{
            $gid=1;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
            return;
        }
        if(!$phone && (!$info || ($info && !$info['phone'])) ){
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
            return;
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

        if($info['phone']){
            //已经提交过手机号
        }else{
            $uid = $info['id'];
            $d = array();
            $d['phone'] = $phone;
            $d['phonetime'] = time();
            $d['id'] = $info['id'];
            M('pretty')->save($d);
        }

       //current sequence
        $sequence = $info['sequence'];
        // $sequence = 1 : 还没有进行过投票
        // $sequence = 2 : 已经用过一次
        // $sequence = 3 : 已经用过二次
        // $sequence = 4 : 已经用过三次
        $couldCountMaskAgain = false;
        $currentNeedVote = 0;

        $firstUsed = '';
        $secondUsed = '';
        $thirdUsed = '';

        $showCountMaskAgain = 1;
        switch($sequence){
            case 1:
                //正在争取第一次机会
                $vote = $info['vote'];
                if($vote >= $this->eachVote){
                    //查看是否已经提交了分数
                    $infoList = M('pretty_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                    if($infoList['number']){
                        //已经提交分数，无法再次提交
                        $firstUsed = 'disabled';
                    }else{
                        $couldCountMaskAgain = true;
                    }
                }else{
                    //未获得第一次机会
                    $currentNeedVote = $this->eachVote*3 - $vote;
                }
                break;
            case 2:
                $firstUsed = 'disabled';
                //正在争取第二次机会
                $vote = $info['vote'];
                if($vote >= $this->eachVote * 2){
                    //查看是否已经提交了分数
                    $infoList = M('pretty_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                    if($infoList['number']){
                        //已经提交分数，无法再次提交
                        $secondUsed = 'disabled';
                    }else{
                        $couldCountMaskAgain = true;
                    }
                }else{
                    //未获得第二次机会
                    $currentNeedVote = $this->eachVote * 3 - $vote;
                }
                break;
            case 3:
                //正在争取第三次机会
                $firstUsed = 'disabled';
                $secondUsed = 'disabled';
                //正在争取第二次机会
                $vote = $info['vote'];
                if($vote >= $this->eachVote * 3){
                    //查看是否已经提交了分数
                    $infoList = M('pretty_list')->where(array('openid' => $userOpenId,'sequence'=>$sequence))->find();
                    if($infoList['number']){
                        //已经提交分数，无法再次提交
                        $thirdUsed = 'disabled';
                    }else{
                        $couldCountMaskAgain = true;
                    }
                }else{
                    //未获得第二次机会
                    $currentNeedVote = $this->eachVote * 3 - $vote;
                }
                break;
            case 4:
                //三次机会用完，在数一次按钮隐藏
                $showCountMaskAgain = 0;
                $firstUsed = 'disabled';
                $secondUsed = 'disabled';
                $thirdUsed = 'disabled';
                break;
            default:

        }

        if($currentNeedVote < 0){
            $currentNeedVote = 0;
        }

        $this->assign('firstused', $firstUsed);
        $this->assign('secondused', $secondUsed);
        $this->assign('thirdused', $thirdUsed);

        $this->assign('needvote', $currentNeedVote);
        $this->assign('username', $info['name']);
        $this->assign('totalnumber', $info['number']);
        $this->assign('couldcountmaskagain', $couldCountMaskAgain);
        $this->assign('couldprettyagainbutton', $showCountMaskAgain);

        //begin views
        if($info){
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views

        $this->display();
    }

    public function sharefriend(){
        $this->setEndTime();
        $userOpenId= cookie('user_openid');
        //$userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
        $fansInfo = null;
        $selfUserInfo = array();
        $uid  = $_GET['uid'];
        $infoTO = M('pretty')->where(array('id' => $uid))->find();
        if( !$infoTO ){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index");
            return;
        }

        $gid = $infoTO['gid'];

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
                            $url = $this->url."/index.php?g=Wap&m=Pretty&a=index&gid=$gid";
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
                $url = urlencode($this->url."/index.php?g=Wap&m=Pretty&a=sharefriend&uid=$uid");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect");
                exit;
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
        $this->assign("shareurl",$this->url."/index.php?g=Wap&m=Pretty&a=sharefriend&uid=".$uid);
        $this->assign('gid', $gid);


        $this->assign('title',$infoTO['name'].$this->title);

        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end



        //local open id $userOpenId

        //如果 sequence=1 and phone为null 则需要重定向到首页 非法页面
        $sequence = $infoTO['sequence'];
//        if(!$infoTO['phone']){
//            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
//        }

        $toUserOpenId = $infoTO['openid'];
        $userName = $infoTO['name'];
        $number = $infoTO['number'];
        $this->assign('name', $userName);
        $this->assign('number', $number);

        $leftVote = $this->eachVote*3;
        if($sequence >= 1){
            //第一次 还差多少票
//            $infoList = M('pretty_list')->where(array('openid' => $toUserOpenId,'sequence'=>$sequence))->find();
            $vote = $infoTO['vote'];
            $leftVote = $this->eachVote*3 - $vote;
        }
        if($leftVote<0){
            $leftVote = 0;
        }
        $this->assign('leftvote', $leftVote);
        $this->assign('fromopenid', $userOpenId);
        $this->assign('toopenid', $toUserOpenId);
        $this->assign('sequence', $sequence);

        //是否显示 帮忙投票
        $showVoteButton = 1;
        if($userOpenId == $toUserOpenId || $sequence >=4 ){
            $showVoteButton = 0;
        }
        $this->assign('showvotebutton', $showVoteButton);
        //end

        //是否已经投过票
        $hasVotedForThisUid = 1;
        $voteList = M('pretty_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$toUserOpenId))->find();
        if($voteList){
            $hasVotedForThisUid = 0;
        }

        $uniqueViewlist = M('pretty_uniqueviewlist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$toUserOpenId))->find();
        if($uniqueViewlist){
            //不需要增加uniqueviews
        }else{
            M("pretty")->where(array('id' => $infoTO['id']))->setInc('uniqueviews');
            $n = array();
            $n['fromopenid'] = $userOpenId;
            $n['toopenid'] = $toUserOpenId;
            $n['createtime'] = time();
            M('pretty_uniqueviewlist')->add($n);
        }

        $this->assign('hasvotedforthisuid', $hasVotedForThisUid);


        //local info
        $infoLocal = M('pretty')->where(array('openid' => $userOpenId))->find();
        if($infoLocal){
            //这个用户是否参加过，已经参加
            $this->assign('localuid', $infoLocal['uid']);
            $this->assign('redirecturl', $this->url."/index.php?g=Wap&m=Pretty&a=sharenumber&gid=$gid");
        }else{
            $this->assign('redirecturl', $this->url."/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
        }

        //当前UID对应的views自增
        M("pretty")->where(array('id' => $infoTO['id']))->setInc('views');


        $selftOpen = 0;
        if($userOpenId == $infoTO['openid']){
            //是自己本身打开了自己的主页
            $selftOpen = 1;
        }
        $this->assign('selfopen', $selftOpen);
        $this->assign('gid',  $gid);
        $this->display();
    }

    //TODO add random str to avoid auto submit
    public function saveVote(){
        $this->setEndTime();
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
        $voteList = M('pretty_votelist')->where(array('fromopenid' => $fromOpenIdFromPost,'toopenid'=>$toOpenIdFromPost  ))->find();
        if(!$voteList){
            //投票
            $d = array();
            $d['fromopenid'] = $fromOpenIdFromPost;
            $d['toopenid'] = $toOpenIdFromPost;
            $d['sequence'] = $tousersequence;
            $d['createtime'] = time();
            M('pretty_votelist')->add($d);

            //更新当前主页人的pretty_list
            $info = M('pretty')->where(array('openid' => $toOpenIdFromPost))->find();
            $sequence = $info['sequence'];

            $c = M('pretty_list')->where(array('openid' => $toOpenIdFromPost,'sequence' => $sequence))->find();
            //是否存在这次机会
            if(!$c){
                $tempArr = array();
                $tempArr['openid'] = $toOpenIdFromPost;
                $tempArr['sequence'] = $sequence;
                $tempArr['number'] = 0;
                $tempArr['vote'] = 1;
                $tempArr['createtime'] = time();
                $tempArr['updatetime'] = time();
                M('pretty_list')->add($tempArr);
            }else{
                M("pretty_list")->where(array('openid' => $toOpenIdFromPost,'sequence' => $sequence))->setInc('vote');
            }

            $return = 1;
        }

        echo $return;
    }

    public function rank(){
        Log :: write($_COOKIE['user_openid'] .'  open id from cookie');
        $userOpenId= cookie('user_openid');
        if(!$userOpenId){
            $userOpenId = $_COOKIE['user_openid'];
        }

        //获取OPENID 用户没有感知
        if(!$userOpenId){
            $apidata = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
            $code = trim($_GET["code"]);
            $state = trim($_GET['state']);

            $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
            if ($code && $state == 'sentian') {
                if(empty($fansInfo)){
                    $webCreatetime = $apidata['web_createtime'];
                    $web_access_token = '';

                    //重新获取
                    $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                    if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                        //code 有错误 需要重定向
                        $url = $this->url."/index.php?g=Wap&m=Pretty&a=getOpenId";
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
                    Log :: write($userOpenId.' rank get openid by base');

                }
            } else {
                $url = urlencode($this->url."/index.php?g=Wap&m=Pretty&a=rank");
                header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
                exit;
            }
        }
        //END
        Log :: write($userOpenId .'  next open id');

        $info = M('pretty')->where(array('openid' => $userOpenId))->find();

        if($_GET['gid']){
            $gid =  $_GET['gid'];
        }elseif($info){
            $gid = $info['gid'];
        }else{
            $gid=1;
        }
        if(!$userOpenId){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
            exit;
        }
        if(!$info || !$info['number']){
            //redirect
            header("location:$this->url/index.php?g=Wap&m=Pretty&a=index&gid=$gid");
            exit;
        }
//        $userOpenId='oP9fCtxIGfuDZkYTS9PSzhvZuvcs';
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

        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //统计第50名
        $count = M('pretty')->where(array('phone'=>array("neq",'')))->count();
        $firstLevel = 50;
        $year = 0;
        if($count < 50){
            $numberfirlevel = 0;
            $sharefirlevel = 0;
            $sharetimefirlevel = '';

        }else{
            $firstLevelInfo = M('pretty')->query("select number,share,phonetime from tp_pretty where phone != '' order by number desc,share desc,phonetime asc limit 49,1");
            if($firstLevelInfo){
                $firstLevelInfo = $firstLevelInfo[0];
                $numberfirlevel = $firstLevelInfo['number'];
                $sharefirlevel = $firstLevelInfo['share'];
                $year = date("Y",$firstLevelInfo['phonetime']);
                $sharetimefirlevel = date("Y-m-d H:i",$firstLevelInfo['phonetime']);
            }
        }

        if($year*1 <= 1970){
            $sharetimefirlevel = '';
        }
        $this->assign('firstlevel',$firstLevel);
        $this->assign('numberfirlevel',$numberfirlevel);
        $this->assign('shares',$sharefirlevel);
        $this->assign('phonetime',$sharetimefirlevel);
        //统计第1050名
        $showSecondLevel = 1;
        if($count < 1050){
            $numberseclevel = '0';
            $shareseclevel = 0;
            $sharetimeseclevel = '';
        }else{
            $secondLevelInfo = M('pretty')->query("select number,share,phonetime from tp_pretty where phone != '' order by number desc,share desc,phonetime asc limit 1049,1");
            if($secondLevelInfo){
                $secondLevelInfo = $secondLevelInfo[0];
                $numberseclevel = $secondLevelInfo['number'];
                $shareseclevel = $secondLevelInfo['share'];
                $sharetimeseclevel = date("Y-m-d H:i",$secondLevelInfo['phonetime']);
            }

        }
        $this->assign('secondlevel',$showSecondLevel);

        $this->assign('numbersecond',$numberseclevel);
        $this->assign('sharessecond',$shareseclevel);
        $this->assign('phonetimesecond',$sharetimeseclevel);

        $info = M('pretty')->where(array('openid' => $userOpenId))->find();
        $number = $info['number'];
        $phoneTime = $info['phonetime'];
        $shareSelf = $info['share'];
        $myCount = $this->getOrderByOpenId($userOpenId);
        $this->assign('count', $myCount);
        $this->assign('number', $number);
        $this->assign('share', $info['share']);

        //begin views
        if($info){
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views
        $this->display();
    }

    public function getOrderByOpenId($openId=null){
        $list = M('pretty')->query("select openid, number,share,phonetime from tp_pretty where phone != '' order by number desc,share desc,phonetime asc ");

        $orderList = array();
        foreach($list as $each){
            $orderList[] = $each['openid'];
        }

        $key = array_search($openId, $orderList);
        return $key*1+1;
    }

    public function award(){
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
        $info = M('pretty')->where(array('openid' => $userOpenId))->find();
        $this->assign('title',$info['name'].$this->title);
        $this->assign('bonusdesc',$this->bonusdesc);
        $this->assign("imageUrl",$this->imageUrl);
        $this->assign("shareimageurl",$this->shareImageUrl);
        //end

        //begin views


        if($info){
            M("pretty")->where(array('id' => $info['id']))->setInc('views');
        }
        // end views

        $this->assign('selfpage',$this->url."/index.php?g=Wap&m=Pretty&a=sharenumber");

        $award = M('pretty_award')->where(array('openid' => $userOpenId))->find();
        if($award){
            $this->assign('name',$award['name']);
            $this->assign('phone',$award['phone']);
            $this->assign('province',$award['province']);
            $this->assign('city',$award['city']);
            $this->assign('address',$award['address']);
        }


        $this->display();
    }

    public function  saveAward(){
        $return = 0;
        $userOpenId= cookie('user_openid');
        if(!$userOpenId){
            echo 0;
            return;
        }
        $award = M('pretty_award')->where(array('openid' => $userOpenId))->find();
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
            M('pretty_award')->add($m);
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
            M('pretty_award')->save($m);
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


    /*
     * 记录转发次数
     */
    public function saveShareNumberToFriends(){
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        $userOpenId= cookie('user_openid');
        $info = M('pretty')->where(array('openid' => $userOpenId))->find();
        if($info){
            $id = $info['id'];
            if(!$info['sharetime']){
                $m = array();
                $m['id'] = $info['id'];
                $m['sharetime'] = time();
                M("pretty")->save($m);
            }
            M("pretty")->where(array('id' => $id))->setInc('share');
            echo 1;
        }else{
            echo 0;
        }

    }

    /*
    * 记录 我也要参加 次数
    */
    public function saveWantJoin(){
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        $toOpenId = $_POST['toopenid'];
        $info = M('pretty')->where(array('openid' => $toOpenId))->find();
        M("pretty")->where(array('id' => $info['id']))->setInc('joins');

    }

    /*
    * 记录 帮忙投票 次数
    */
    public function saveHelpVote(){
        $return = 0;
        $endtime =strtotime( $this->endtime );
        if(time() > $endtime ){
            echo 0;
            exit;
        }
        //帮忙投票 点击次数
        $toOpenId = $_POST['toopenid'];
        $userOpenId = cookie('user_openid');
        $voteList = M('pretty_votelist')->where(array('fromopenid' => $userOpenId,'toopenid'=>$toOpenId))->find();
        if(!$voteList){
            $info = M('pretty')->where(array('openid' => $toOpenId))->find();
            M("pretty")->where(array('id' => $info['id']))->setInc('vote');

            //投票
            $d = array();
            $d['fromopenid'] = $userOpenId;
            $d['toopenid'] = $toOpenId;
            $d['sequence'] = $info['sequence'];
            $d['createtime'] = time();
            M('pretty_votelist')->add($d);
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
        $url = $this->url."/index.php?g=Wap&m=Pretty&a=index";
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

        $fansInfo = M('customer_service_fans')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
        if ($code && $state == 'sentian') {
            if(empty($fansInfo)){
                $webCreatetime = $apidata['web_createtime'];
                $web_access_token = '';

                    //重新获取
                    $userinfoFromApi = $this->getUserInfo($code, $apidata['appid'], $apidata['appsecret']);
                    if(isset($userinfoFromApi['errcode']) && $userinfoFromApi['errcode']){
                        //code 有错误 需要重定向
                        $url = $this->url."/index.php?g=Wap&m=Pretty&a=getOpenId";
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
                    Log :: write($userOpenId.' base  sssssssssssssss');

            }
        } else {
            $url = urlencode($this->url."/index.php?g=Wap&m=Pretty&a=getOpenId");
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $apidata['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=sentian#wechat_redirect");
            exit;
        }
    }


}
