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
    public function saveBonusHistory($gid,$openId,$number,$fromOpenId){
        $h['openid'] = $openId;
        $h['gid'] = $gid;
        $h['description'] = $this->getRandDescriptionByNumber($number);
        $h['number'] = $number;
        $h['from_open_id'] = $fromOpenId;
        M("bonus_history")->add($h);

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
