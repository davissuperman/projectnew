<?php

class NumberAction extends UserAction {
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
                'vote' => 500
//                'vote' => 5//测试用2等奖
            ),
        ),
        2000 => array(
            1 => array(
                'count' => 1,
                'vote' => 1000
//                    'vote' => 6//测试用1等奖
            ),
        )
    );

    public $score = array(-14,-13,-12,-11,-10,-9,-8,-7,-6,-5,-4,-3,-2,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,35,36,37,38,39,40,41,42,43,44);
    public $leftIntval = 44;
    public $minus = 20;
    public $cache;

    public function _initialize() {
        $this->cache = Cache::getInstance('Redis',array('host'=>'127.0.0.1','expire'=>3600));
        parent::_initialize();
    }

    public function saveIllegal(){
        $url = '/home/dev/html/projectnew/wap/wrong.txt';

        $handle = fopen($url, 'r');
        while(!feof($handle)){
            $openId = fgets($handle);
            $openId = trim($openId);
            $openId = trim($openId,'\n');
            $id = M('bonus_info')->where(array('openid' => $openId))->getField('id');
            $d['id'] = $id;
            $d['openid'] = $openId;
            $d['illegal'] = 1;
            $r = M("bonus_info")->save($d);
            echo $openId ."      aaaa        ".$r."<br/>";
        }
        fclose($handle);



    }


    public function change(){
        $res = M('greeting')->select();
        $i = 0;
        $viewSum = 0;
        $sum = 254850;
        $leftSum = 0;


        //share info
        $shareSum = 0;

        //accept
        $acceptSum = 0;
        $leftAccept = 0;
        $acceptNum = 18654;


        //wantcard
        $wantCardSum = 0;
        $leftWantCard = 0;
        $wantCardNum = 10533;

        //subscribe
        $subcribeSum = 0;
        $leftSubcribe = 0;
        $subcribeNum = 12378;


        foreach($res as $each){
            $openId = $each['openid'];
            $map = array();
            $arr = array();
            $view= $each['view'];
            $id = $each['id'];
            $i++;
            if($i < 200){
                $view = rand(70,260);
            }else if($i<1800){
                $view = rand(20,80);
            }else if($i<11000){
                $view = rand(6,20);
            }else if($i<=14991){
                $view = rand(0,6);
            }else if($i<=16901){
                $leftSum = $sum - $viewSum;
                $view = rand(0,8);
                //echo "Left:  ". $leftSum ." sum ".$viewSum. "  i  ".$i."  view ".$view."<br/>";
            }else if($i<=16991){
                $leftSum = $sum - $viewSum;

                if($i > 16961){

                    if($leftSum == 0){
                        $view = 0;
                    }else{
                        if($leftSum <= 150){
                            $view = $leftSum;
                        }else{
                            $view = rand(90,250);
                        }

                    }
                   // echo "Left:  ". $leftSum ." sum ".$viewSum. "  i  ".$i."  view ".$view."<br/>";
                }else{
                    $view = rand(0,8);
                }

            }
            $viewSum += $view;

            if($view > 20 ){
                $accept = rand(2,11);
            }else{
                $accept = rand(0,1);
            }

            $leftAccept = $acceptNum - $acceptSum;
            if($leftAccept <= 11){
                $accept = $leftAccept;
            }else if($leftAccept == 0){
                $accept  = 0;
            }
            $acceptSum += $accept;


            //subscribe
            if($view > 20){
                $subscribe = rand($accept - 5,$accept);
                if($subscribe <= 0){
                    $subscribe = 1;
                }
            }else{
                $subscribe = rand(0,$accept);
            }
            $leftSubcribe = $subcribeNum - $subcribeSum;
            if($leftSubcribe <= 11){
                $subscribe = $leftSubcribe;
            }else if($leftSubcribe == 0){
                $subscribe  = 0;
            }
            $subcribeSum += $subscribe;
            echo $subcribeSum . "   ".$i."   ".$subscribe."<br/>";

            //want card
            $wantCard = rand(0,$accept);

            if($wantCard <= $subscribe ){
                $wantCard = rand($subscribe,$accept);
            }

            $leftWantCard = $wantCardNum - $wantCardSum;
            if($leftWantCard <= 11){
                $wantCard = $leftWantCard;
            }else if($leftWantCard == 0){
                $wantCard  = 0;
            }
            $wantCardSum += $wantCard;




            if($view > 150 ){
                $share = rand(5,10);
            }else if($view>20){
                $share = rand(2,5);
            }else if($view == 0){
                $share = 0;
            }else {
                $share = rand(0,2);
            }
            $shareSum += $share;


            //joins
            $joins = rand(0,$accept);

            $map['view'] = $view;
            $map['accept'] = $accept;
            $map['subscribe'] = $subscribe;
            $map['wantcard'] = $wantCard;
            $map['share'] = $share;
            $map['joins'] = $joins;
            $map['openid'] = $openId;
            M('greeting_list')->add($map);
        }

        echo $subcribeSum ."    ".$i;
    }



    public function saveCard(){
        $res = M('bonus_info')->where(array('illegal' => 0))->select();
        $n = 15;
        $i = 0;
        foreach($res as $each){
            $openId = $each['openid'];
            //判断这个人是否留下手机号
            $mapFour['telephone'] = array('neq','');
            $mapFour['openid'] = array('eq',$openId);
            $phone = M('bonus_award')->where($mapFour)->getField('telephone');
            if($phone){
                //手机号存在
            }else{
                //查看此用户在greeting是否存在
                $greeting = M('greeting')->where(array('openid' => $openId))->select();
                if(!$greeting){
                    //将此用户加入到GREETING中
                    $views = rand(2,1800);
                    $map['view'] = $views;

                    if($views > 20 ){
                        if($views > 50){
                            $accept = rand(20,50);
                        }else{
                            $accept = rand(6,30);
                        }
                    }else{
                        $accept = rand(0,18);
                    }


                    $subscribe = rand(0,$accept);

                    $wantCard = rand(0,$accept);

                    if($wantCard <= $subscribe ){
                        $wantCard = rand($subscribe,$accept);
                    }

                    $share = rand(0,40);

                    $joins = rand(0,45);

                    $map['view'] = $views;
                    $map['accept'] = $accept;
                    $map['subscribe'] = $subscribe;
                    $map['wantcard'] = $wantCard;
                    $map['share'] = $share;
                    $map['joins'] = $joins;
                    $map['openid'] = $openId;
                    M('greeting')->add($map);
                    $i++;
                }



            }

        }

        echo $i ."<br/>";
    }




    public function saveIllegalByPhone(){
        $url = '/home/dev/html/projectnew/wap/phone.txt';

        $handle = fopen($url, 'r');
        while(!feof($handle)){
            $phone = fgets($handle);
            $phone = trim($phone);
            $phone = trim($phone,'\n');
            $res = M('bonus_award')->where(array('telephone' => $phone))->select();

//            echo "<pre>";
//            var_dump($res);
            $openId = $res[0]['openid'];
            if($openId){
                $id = M('bonus_info')->where(array('openid' => $openId))->getField('id');
                $d['id'] = $id;
                $d['openid'] = $openId;
                $d['illegal'] = 1;
                $r = M("bonus_info")->save($d);
            }else{
                echo $openId." bbbbbbbbb ". $phone ."      aaaa        "."<br/>";
            }


        }
        fclose($handle);



    }



























    public function getRandScore($typeBigZero = false){
        $score = $this->score;
        if($typeBigZero){
            $num = rand(20,count($this->score)-1);
        }else{
            $num = rand(0,count($this->score)-1);
        }
        $return = $score[$num];
        return $return;
    }
    public function index() {
        //产生测试数据
        $shareX = 50;
        $viewX = 98;
        $gid  = 1;
//        $openId = "oYkdqs5s1IEIhB9bulM2AJ6GgZh8";//慢慢羊
//        $openId = "oYkdqs6YN282-he6W8cPxMKS2D-c";//DAVIS
        $openId = "oYkdqs3wYjJX-wBva6llNJjh3EYc";//James
        //从表 fans抓取 100 用户
        $map['openid']  = array('neq',$openId);
        $map['token']  = array('eq','rggfsk1394161441');

        //分享给了X个人
        $a =  M("bonus_info")->where(array('openid' => $openId))->setInc('share', $shareX);

        //其中有五十人拉了分数

        $userList = M('customer_service_fans')->where($map)->limit($viewX)->select();
        echo "<pre>";
        $i = 0;
        foreach($userList as $each){
            //每一个用户访问此OPENID 主页
            $fronopenid = $each['openid'];
            $this->saveBonusViewInfo($gid,$openId);
            $this->saveViews($fronopenid,$gid,$openId);


            $bonusHistory = M('bonus_history')->where(array( 'openid' => $openId,'from_open_id'=>$fronopenid))->find();
            if($bonusHistory){
                echo "此用户 $fronopenid 已经拉过分 "."<br/>";
            }else{
                $i++;
                $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
                //随机生成分数
                $number =  $this->getNumberByOpenId($gid,$openId,$bonusInfo);
                echo "此用户 $fronopenid 拉分 ".$number."<br/>";
                M("bonus_info")->where(array('openid' => $openId))->setInc('vote', 1);
                M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('number', $number);
                $this->saveBonusHistory($gid,$openId,$number,$fronopenid);
                $n = M("bonus_info")->where(array('id' =>$bonusInfo['id']))->getField('number');
                echo "当前分数为： $n";
                //随机 产生 我也要参加
                if(rand(-5,5) > 0){
                    M("bonus_info")->where(array('openid' => $openId))->setInc('joins', 1);
                }
            }
        }

    }
    public function test(){
        $openId = 'oP9fCt8lV5Oy18eopCjTCBHPykCE';
        $updateTIme = $this->getUpdateTime($openId);
        echo $updateTIme;
    }
    public function getUpdateTime($openId){
        $map['openid'] = $openId;
        $map['address'] = array('neq','');
        $res = M('bonus_award')->where($map)->field('updatetime')->find();
        $updateTIme = $res['updatetime'];
        return $updateTIme;
    }
    public function push(){
        //http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCtxIGfuDZkYTS9PSzhvZuvcs
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCt8lV5Oy18eopCjTCBHPykCE //肖
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCt_8XNH7wF0ERFC2VukVAZXo //manmanyang
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCt-qEyri5k-GXEp2nWiRPpHs //april
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCt4FMhkETNDNMTQRirg4qRWI //妖精哪里走
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCtxF6G7-8-bM1VK6wZIteHPA //去年夏天我是瘦子
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=push&openid=oP9fCt9zdeeEmtnDgsrWAvdfi6Uc //zhao
        $openId = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(isset($_GET['openid']) && $_GET['openid']){
            $openId = $_GET['openid'];
        }
        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
        $gid = $bonusInfo['gid'];
        $score =  $bonusInfo['number'];

        $i=0;
        $current = M("bonus_history")->where(array('openid' =>$openId))->sum('number');
//        if($current != $score){
//            return;
//        }


        $count = M("bonus_history")->where(array('openid' =>$openId))->count('id');
        $vote = $bonusInfo['vote'];
        //相差多少记录
        $left = $vote - $count;
        //if($left > 0){
            $ap['openid'] = $openId;
            $ap['from_open_id'] = array('neq','');
            M("bonus_history")->where($ap)->delete();
      //  }
        //重新生成记录
        $map['openid']  = array('neq',$openId);
        $needVote = $vote;
        $userList = M('customer_service_fans')->where($map)->limit($needVote)->select();
        //需要插入616条记录
        //生成616个number
        $s = $score - 50;
        $sList = $this->getNumber($needVote,$s);
        foreach($userList as $key => $each){
            //每一个用户访问此OPENID 主页
            $fronopenid = $each['openid'];
            $number = $sList[$key];
            $this->saveBonusHistory($gid,$openId,$number,$fronopenid,$bonusInfo['sharetime']);
            $this->saveViews($fronopenid,$gid,$openId);
            echo "加分 ： $number <br/>";
        }

    }

    public function getRandDate($s,$openId){
       //生成随机时间戳
        $e = $this->getUpdateTime($openId);;
        $n = rand($s*1+200,$e);
        return date("Y-m-d H:i:s",$n);
    }
    public function getNumber($n,$s){
        $sum = 0;
        $a = array();
        for($i=0;$i<$n;$i++){
            $m = rand(-14,44);
            if($sum + $m > $s){
                $m = $s - $sum -2;
            }
            if($m == 0){
                $m = -1;
            }
            if($i == $n-1){
                $m = $s - $sum;
            }
            $a[] = $m;
            $sum += $m;
        }
       return $a;
    }
    public function generate(){
        //http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCtxIGfuDZkYTS9PSzhvZuvcs
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCt8lV5Oy18eopCjTCBHPykCE //肖
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCt_8XNH7wF0ERFC2VukVAZXo //manmanyang
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCt-qEyri5k-GXEp2nWiRPpHs //april
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCt4FMhkETNDNMTQRirg4qRWI //妖精哪里走
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCtxF6G7-8-bM1VK6wZIteHPA //去年夏天我是瘦子
//        http://wx.drjou.cc/index.php?g=User&m=Number&a=generate&openid=oP9fCt9zdeeEmtnDgsrWAvdfi6Uc //zhao
        $openId = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(isset($_GET['openid']) && $_GET['openid']){
            $openId = $_GET['openid'];
        }
        $score = "300";
        if(isset($_GET['score']) && $_GET['score']){
            $score = $_GET['score'];
        }
        $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
        $gid = $bonusInfo['gid'];
        $score =  $bonusInfo['number'];
        $map['openid']  = array('neq',$openId);
        $userList = M('customer_service_fans')->where($map)->select();

        $i=0;
        $current = M("bonus_history")->where(array('openid' =>$openId))->sum('number');
        $left = $score - $current;
        if($left<=0){
            return;
        }
        foreach($userList as $each){
            //每一个用户访问此OPENID 主页
            $fronopenid = $each['openid'];
            $viewNum =  rand(1,4);
            //$a =  M("bonus_info")->where(array('openid' => $openId))->setInc('views', $viewNum);
            //$this->cache->redis->incrBy($hashKeyBonusInfo."_view",$viewNum);
            //$this->saveViews($fronopenid,$gid,$openId);
            $bonusHistory = M('bonus_history')->where(array('openid' => $openId,'from_open_id'=>$fronopenid))->find();
            if($bonusHistory){
                echo "此用户 $fronopenid 已经拉过分 ".$bonusHistory['description']."<br/>";
            }else{
                $i++;

                //随机生成分数
                $cNumber = M("bonus_history")->where(array('openid' =>$openId))->sum('number');
                $number =  $this->getRandScore();
                if($cNumber >= $score){
                    break;
                }
                if($cNumber + $number > $score){
                    $number = $score - $cNumber;
                }

                $this->saveBonusHistory($gid,$openId,$number,$fronopenid);
                $this->saveViews($fronopenid,$gid,$openId);
                echo "加分 ： $number <br/>";

            }

        }
    }
    public function setNum(){
        //http://wx.drjou.cc/index.php?g=User&m=Number&a=setNum&score=300&openid=oP9fCtxIGfuDZkYTS9PSzhvZuvcs
        $openId = "oP9fCtxIGfuDZkYTS9PSzhvZuvcs";
        if(isset($_GET['openid']) && $_GET['openid']){
            $openId = $_GET['openid'];
        }
        $score = "300";
        $gid = 1;
        if(isset($_GET['score']) && $_GET['score']){
            $score = $_GET['score'];
        }
        $map['openid']  = array('neq',$openId);
        $userList = M('customer_service_fans')->where($map)->select();
        $i = 0;
        $hashKeyBonusInfo  = "bonusinfo_".$openId;
        foreach($userList as $each){
            //每一个用户访问此OPENID 主页
            $fronopenid = $each['openid'];
            $viewNum =  rand(1,4);
            $a =  M("bonus_info")->where(array('openid' => $openId))->setInc('views', $viewNum);
            $this->cache->redis->incrBy($hashKeyBonusInfo."_view",$viewNum);
            $this->saveViews($fronopenid,$gid,$openId);
            $bonusHistory = M('bonus_history')->where(array('openid' => $openId,'from_open_id'=>$fronopenid))->find();
            if($bonusHistory){
                echo "此用户 $fronopenid 已经拉过分 ".$bonusHistory['description']."<br/>";
            }else{
                $i++;
                $bonusInfo = M('bonus_info')->where(array('openid' => $openId))->find();
                //随机生成分数
                $number =  $this->getNumberByOpenId($gid,$openId,$bonusInfo);
                echo "此用户 $fronopenid 拉分 ".$number."<br/>";
                M("bonus_info")->where(array('openid' => $openId))->setInc('vote', 1);
                M("bonus_info")->where(array('id' =>$bonusInfo['id']))->setInc('number', $number);

                $this->cache->redis->incr($hashKeyBonusInfo."_vote");
                $this->cache->redis->incrBy($hashKeyBonusInfo."_number",$number);

                $this->saveBonusHistory($gid,$openId,$number,$fronopenid);
                $n = M("bonus_info")->where(array('id' =>$bonusInfo['id']))->getField('number');
                echo "当前分数为： $n";
                if($n >= $score){
                    break;
                }
                //随机 产生 我也要参加
                if(rand(-5,5) > 0){
                    M("bonus_info")->where(array('openid' => $openId))->setInc('joins', 1);
                    $this->cache->redis->incr($hashKeyBonusInfo."_joins");
                }

            }

        }
        $a =  M("bonus_info")->where(array( 'openid' => $openId))->setInc('share', $i+50);
        $this->cache->redis->incrBy($hashKeyBonusInfo."_share",$i+50);
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
    public function saveBonusHistory($gid,$openId,$number,$fromOpenId,$s=null){
        $h['openid'] = $openId;
        $h['gid'] = $gid;
        $h['description'] = $this->getRandDescriptionByNumber($number);
        $h['number'] = $number;
        $h['from_open_id'] = $fromOpenId;
        $h['createtime'] = $this->getRandDate($s,$openId);
        echo $h['createtime']  . "   ".$number."   <br/>";
        M("bonus_history")->add($h);
    }
    public function saveBonusViewInfo($gid,$openId){
       $a =  M("bonus_info")->where(array('openid' => $openId))->setInc('views', 1);
    }

    //保存阅读量历史记录
    public function saveViews($fronopenid,$gid,$openId,$index=false){
        //保存阅读记录
        $d['gid'] = $gid;
        $d['type'] = 1;

        $d['from_open_id'] = $fronopenid;
        if($index){
            $d['viewpage'] = 1;
        }
        $d['to_open_id'] = $openId;
        $a = M("bonus_list")->add($d);
    }
    public function getKey(){
        $scoreList = array_keys($this->configBonus);
        array_unshift($scoreList,$this->fiveScore);
        var_dump($scoreList) ;
    }
    public function saveCache(){
        $cache = Cache::getInstance('Redis',array('host'=>'127.0.0.1','expire'=>3600));
        $arr= array("a",1,3,"cadfasdfdsa");
        $cache->set("time",$arr);

        ;
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
                    echo "<br/>  $return  test <br/>";
                }
                break;
            case 3:
                //三等奖 需要分数500
                //前200名 100票
                //中期200名 120票
                //后期100名 150票

                $map['vote']  = array('egt',$configBonus[$three][1]['vote']);
                $bonusNumber= M('bonus_info')->where($map)->count('id');
                Log :: write($vote."Number aaaaaaaaaaaaaa ".$bonusNumber."  config  ".$configBonus[$three][1]['vote']);
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
                if($number + $return < $four){
                    //不可以回到上一个等级
                    $return = $return + $four - ($number + $return) + 5;
                }
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
                if($number + $return < $three){
                    //不可以回到上一个等级
                    $return = $return + $three - ($number + $return) + 5;
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


}
