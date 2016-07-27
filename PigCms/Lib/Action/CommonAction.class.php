<?php

/**
 * 公共类
 *
 * @author Administrator
 */
class CommonAction extends Action {

    public $token;
    public $fun;
    public $data = array();
    public $my = '品微';

    /**
     * 对接第三方
     * @param type $key
     * @return type
     */
    function api($key) {
        $data2 = M('api')->field('url')->where(array('token' => $this->token))->find();
        if ($data2['url'] != '') {
            //$Content=$_POST['message']; 
            $time = date("Ymdhis");
            $uri = $data2['url'];
            // 参数数组
            $data = array(
                'ToUserName' => $this->data ['ToUserName'],
                'FromUserName' => $this->data ['FromUserName'],
                'CreateTime' => $time,
                'Content' => $key
            );
            $ch = curl_init();
            $header[] = "Content-type: text/xml"; //定义content-type为xml
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $return = curl_exec($ch);
            curl_close($ch);
            return $return;
        }
    }

    function keyword($key,$obj=null) {

        $this->selectService();
        $like ['keyword'] = array('like', '%' . $key . '%');
        $like ['token'] = $this->token;
        $data = M('keyword')->where($like)->order('id desc')->find();
        if ($data != false) {
            switch ($data ['module']) {
                case 'Img' :
                    $this->requestdata('imgnum');
                    $img_db = M($data ['module']);
                    $back = $img_db->field('id,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
                    $idsWhere = 'id in (';
                    $comma = '';
                    foreach ($back as $keya => $infot) {
                        $idsWhere .= $comma . $infot ['id'];
                        $comma = ',';
                        if ($infot ['url'] != false) {
                            if (!(strpos($infot ['url'], 'http') === FALSE)) {
                                $url = html_entity_decode($infot ['url']);
                                if(strstr($url,'=OPENID')){
                                    $openId = (string)$obj['FromUserName'];
                                    $url = str_replace('OPENID',$openId,$url);
                                }
                            } else {
                                $urlInfos = explode(' ', $infot ['url']);
                                switch ($urlInfos [0]) {
                                    case '刮刮卡' :
                                        $url = C('site_url') . U('Wap/Guajiang/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '大转盘' :
                                        $url = C('site_url') . U('Wap/Lottery/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '商家订单' :
                                        $url = C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&hid=' . $urlInfos [1];
                                        break;
                                    case '优惠券' :
                                        $url = C('site_url') . U('Wap/Coupon/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '会员卡' :
                                        $url = C('site_url') . U('Wap/Card/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']));
                                        break;
                                    case '首页' :
                                        $url = rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'];
                                        break;
                                }
                            }
                        } else {
                            $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot ['id']));
                        }
                        $return [] = array($infot ['title'], $infot ['text'], $infot ['pic'], $url);
                    }
                    $idsWhere .= ')';
                    if ($back) {
                        $img_db->where($idsWhere)->setInc('click');
                    }
                    return array($return, 'news');
                    break;
                case 'Host' :
                    $this->requestdata('other');
                    $host = M('Host')->where(array('id' => $data ['pid']))->find();
                    return array(array(array($host ['name'], $host ['info'], $host ['ppicurl'], C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&hid=' . $data ['pid'])), 'news');
                    break;
                case 'Text' :
                    $this->requestdata('textnum');
                    $info = M($data ['module'])->order('id desc')->find($data ['pid']);
                    return array(htmlspecialchars_decode($info ['text']), 'text');
                    break;
                case 'Product' :
                    $this->requestdata('other');
                    $infos = M('Product')->limit(9)->order('id desc')->where($like)->select();
                    if ($infos) {
                        $return = array();
                        foreach ($infos as $info) {
                            $return [] = array($info ['name'], strip_tags(htmlspecialchars_decode($info ['intro'])), $info ['logourl'], C('site_url') . '/index.php?g=Wap&m=Product&a=product&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&id=' . $info ['id']);
                        }
                    }
                    return array($return, 'news');
                    break;
                case 'Selfform' :
                    $this->requestdata('other');
                    $pro = M('Selfform')->where(array('id' => $data ['pid']))->find();
                    return array(array(array($pro ['name'], strip_tags(htmlspecialchars_decode($pro ['intro'])), $pro ['logourl'], C('site_url') . '/index.php?g=Wap&m=Selfform&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&id=' . $data ['pid'])), 'news');
                    break;
                case 'Wdiaoyan' :
                    $this->requestdata('textnum');
                    $pro = M('wdyform')->where(array('id' => $data ['pid']))->find();
                    return array(array(array($pro ['name'], strip_tags(htmlspecialchars_decode($pro ['intro'])), $pro ['logourl'], C('site_url') . '/index.php?g=Wap&m=Wdiaoyan&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&id=' . $data ['pid'])), 'news');
                    break;
                case 'Wedding': $this->requestdata('other');
                    $wedding = M('Wedding')->where(array('id' => $data['pid']))->find();
                    return array(array(array($wedding['title'], strip_tags(htmlspecialchars_decode($wedding['word'])), $wedding['coverurl'], C('site_url') . '/index.php?g=Wap&m=Wedding&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'), array('查看我的来宾', strip_tags(htmlspecialchars_decode($wedding['word'])), $wedding['picurl'], C('site_url') . '/index.php?g=Wap&m=Wedding&a=check&type=1&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'), array('查看我的祝福', strip_tags(htmlspecialchars_decode($wedding['word'])), $wedding['picurl'], C('site_url') . '/index.php?g=Wap&m=Wedding&a=check&type=2&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'),), 'news');
                    break;
                case 'Estate': $this->requestdata('other');
                    $Estate = M('Estate')->where(array('id' => $data['pid']))->find();
                    return array(array(array($Estate['title'], $Estate['estate_desc'], $Estate['cover'], C('site_url') . '/index.php?g=Wap&m=Estate&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'), array('楼盘介绍', $Estate['estate_desc'], $Estate['house_banner'], C('site_url') . '/index.php?g=Wap&m=Estate&a=index&&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'), array('专家点评', $Estate['estate_desc'], $Estate['cover'], C('site_url') . '/index.php?g=Wap&m=Estate&a=impress&&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'), array('楼盘3D全景', $Estate['estate_desc'], $Estate['banner'], C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'), array('楼盘动态', $Estate['estate_desc'], $Estate['house_banner'], C('site_url') . '/index.php?g=Wap&m=Index&a=lists&classid=' . $data['classify_id'] . '&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'),), 'news');
                    break;
                case 'Lottery' :
                    $this->requestdata('other');
                    $info = M('Lottery')->find($data ['pid']);
                    if ($info == false || $info ['status'] == 3) {
                        return array('活动可能已经结束或者被删除了', 'text');
                    }
                    switch ($info ['type']) {
                        case 1 :
                            $model = 'Lottery';
                            break;
                        case 2 :
                            $model = 'Guajiang';
                            break;
                        case 3 :
                            $model = 'Coupon';
                    }
                    $id = $info ['id'];
                    $type = $info ['type'];
                    if ($info ['status'] == 1) {
                        $picurl = $info ['starpicurl'];
                        $title = $info ['title'];
                        $id = $info ['id'];
                        $info = $info ['info'];
                    } else {
                        $picurl = $info ['endpicurl'];
                        $title = $info ['endtite'];
                        $info = $info ['endinfo'];
                    }
                    $url = C('site_url') . U('Wap/' . $model . '/index', array('token' => $this->token, 'type' => $type, 'wecha_id' => $this->data ['FromUserName'], 'id' => $id, 'type' => $type));
                    return array(array(array($title, $info, $picurl, $url)), 'news');
                default :
                    $this->requestdata('videonum');
                    $info = M($data ['module'])->order('id desc')->find($data ['pid']);
                    return array(array($info ['title'], $info ['keyword'], $info ['musicurl'], $info ['hqmusicurl']), 'music');
            }
        } else {
            if (!strpos($this->fun, 'liaotian')) {
                $other = M('Other')->where(array('token' => $this->token))->find();
                if ($other == false) {
                    return array('回复帮助，可了解所有功能', 'text');
                } else {
                    if (empty($other ['keyword'])) {
                        //$pro1=$this->api($key);
                        //file_put_contents('./b.txt',$pro1);exit;
                        $data1 = M('api')->field('url')->where(array('token' => $this->token))->find();
                        //var_dump($data1);
                        if ($data1['url'] != '') {
                            $pro1 = $this->api($key);
                            echo $pro1;
                            exit;
                        } else {
                            return array($other ['info'], 'text');
                        }
                    } else {


                        $img = M('Img')->field('id,text,pic,url,title')->limit(5)->order('id desc')->where(array('token' => $this->token, 'keyword' => array('like', '%' . $other ['keyword'] . '%')))->select();
                        if ($img == false) {
                            $funcdate = array("团购", "首页", "主页", "地图", "最近的", "帮助", "help", "会员卡", "会员", "相册", "全景", "商城", "活动", "订餐", "微官网");
                            if (in_array($other ['keyword'], $funcdate)) {
                                return $this->functionlist($other ['keyword']);
                            } else {
                                return array('无此图文信息,请提醒商家，重新设定关键词', 'text');
                            }
                        }
                        foreach ($img as $keya => $infot) {
                            if ($infot ['url'] != false) {
                                $url = $infot ['url'];
                            } else {
                                $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot ['id']));
                            }
                            $return [] = array($infot ['title'], $infot ['text'], $infot ['pic'], $url);
                        }
                        return array($return, 'news');
                    }
                }
            }
            $data3 = M('api')->field('url')->where(array('token' => $this->token))->find();
            if ($data3['url'] != '') {
                $pro2 = $this->api($key);
                echo $pro1;
                exit;
            } else {
                return array($this->chat($key), 'text');
            }
        }
    }

    function fenxiang() {
        $data ['utitle'] = '婚纱照！';
        $data ['udescription'] = '婚纱照分享';
        $data ['url'] = rtrim(C('site_url'), '/') . U('Wap/Vote/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']));
        $data ['upicurl'] = 'http://www.pinv.cc/PUBLIC/fenxiang.gif';
        return array(array(array($data ['utitle'], $data ['udescription'], $data ['upicurl'], $data ['url'])), 'news');
    }

    function weixin() {
        $photo = M('message_uleave')->where(array('token' => $this->token, 'status' => 1))->find();
        $data ['utitle'] = $photo ['utitle'];
        $data ['udescription'] = $photo ['udescription'];
        $data ['url'] = rtrim(C('site_url'), '/') . U('Wap/Leave/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']));
        $data ['upicurl'] = $photo ['upicurl'] ? $photo ['upicurl'] : rtrim(C('site_url'), '/') . '/tpl/static/images/liuyan.jpg';
        return array(array(array($data ['utitle'], $data ['udescription'], $data ['upicurl'], $data ['url'])), 'news');
    }

    function xiangce() {
        $photo = M('Photo')->where(array('token' => $this->token, 'status' => 1))->find();
        $data ['title'] = $photo ['title'];
        $data ['keyword'] = $photo ['info'];
        $data ['url'] = rtrim(C('site_url'), '/') . U('Wap/Photo/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']));
        $data ['picurl'] = $photo ['picurl'] ? $photo ['picurl'] : rtrim(C('site_url'), '/') . '/tpl/static/images/yj.jpg';
        return array(array(array($data ['title'], $data ['keyword'], $data ['picurl'], $data ['url'])), 'news');
    }

    function companyMap() {
        import("Home.Action.MapAction");
        $mapAction = new MapAction ( );
        return $mapAction->staticCompanyMap();
    }

    function shenhe($name) {
        $name = implode('', $name);
        if (empty($name)) {
            return '正确的审核帐号方式是：审核+帐号';
        } else {
            $user = M('Users')->field('id')->where(array('username' => $name))->find();
            if ($user == false) {
                return '主人' . $this->my . "提醒您,您还没注册吧\n正确的审核帐号方式是：审核+帐号,不含+号";
            } else {
                $up = M('users')->where(array('id' => $user ['id']))->save(array('status' => 1, 'viptime' => strtotime("+1 day")));
                if ($up != false) {
                    return '主人' . $this->my . '恭喜您,您的帐号已经审核,您现在可以登陆平台测试功能啦!';
                } else {
                    return '服务器繁忙请稍后再试';
                }
            }
        }
    }

    function huiyuanka($name) {
        return $this->member();
    }

    function member() {
        $card = M('member_card_create')->where(array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']))->find();
        $cardInfo = M('member_card_set')->where(array('token' => $this->token))->find();
        $reply_info_db = M('Reply_info');
        if ($card == false) {
            $where_unmember = array('token' => $this->token, 'infotype' => 'membercard_nouse');
            $unmemberConfig = $reply_info_db->where($where_unmember)->find();
            if (!$unmemberConfig) {
                $unmemberConfig = array();
                $unmemberConfig['picurl'] = rtrim(C('site_url'), '/') . '/tpl/static/images/member.jpg';
                $unmemberConfig['title'] = '申请成为会员';
                $unmemberConfig['info'] = '申请成为会员，享受更多优惠';
            }
            $data['picurl'] = $unmemberConfig['picurl'];
            $data['title'] = $unmemberConfig['title'];
            $data['keyword'] = $unmemberConfig['info'];
            if (!$unmemberConfig['apiurl']) {
                $data['url'] = rtrim(C('site_url'), '/') . U('Wap/Card/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
            } else {
                $data['url'] = str_replace('{wechat_id}', $this->data['FromUserName'], $unmemberConfig['apiurl']);
            }
        } else {
            $where_member = array('token' => $this->token, 'infotype' => 'membercard');
            $memberConfig = $reply_info_db->where($where_member)->find();
            if (!$memberConfig) {
                $memberConfig = array();
                $memberConfig['picurl'] = rtrim(C('site_url'), '/') . '/tpl/static/images/vip.jpg';
                $memberConfig['title'] = '会员卡,省钱，打折,促销，优先知道,有奖励哦';
                $memberConfig['info'] = '尊贵vip，是您消费身份的体现,会员卡,省钱，打折,促销，优先知道,有奖励哦';
            }
            $data['picurl'] = $memberConfig['picurl'];
            $data['title'] = $memberConfig['title'];
            $data['keyword'] = $memberConfig['info'];
            if (!$memberConfig['apiurl']) {
                $data['url'] = rtrim(C('site_url'), '/') . U('Wap/Card/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
            } else {
                $data['url'] = str_replace('{wechat_id}', $this->data['FromUserName'], $memberConfig['apiurl']);
            }
        }
        return array(array(array($data['title'], $data['keyword'], $data['picurl'], $data['url'])), 'news');
    }

    function choujiang($name) {
        $data = M('lottery')->field('id,keyword,info,title,starpicurl')->where(array('token' => $this->token, 'status' => 1, 'type' => 1))->order('id desc')->find();
        if ($data == false) {
            return array('暂无抽奖活动', 'text');
        }
        $pic = $data ['starpicurl'] ? $data ['starpicurl'] : rtrim(C('site_url'), '/') . '/tpl/User/default/common/images/img/activity-lottery-start.jpg';
        $url = rtrim(C('site_url'), '/') . U('Wap/Lottery/index', array('type' => 1, 'token' => $this->token, 'id' => $data ['id'], 'wecha_id' => $this->data ['FromUserName']));
        return array(array(array($data ['title'], $data ['info'], $pic, $url)), 'news');
    }

    function home() {
        return $this->shouye();
    }

    function shouye($name) {
        $home = M('Home')->where(array('token' => $this->token))->find();
        if ($home == false) {
            return array('商家未做首页配置，请稍后再试', 'text');
        } else {
            $imgurl = $home ['picurl'];
            if ($home ['apiurl'] == false) {
                $url = rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'];
            } else {
                $url = $home ['apiurl'];
            }
        }
        return array(array(array($home ['title'], $home ['info'], $imgurl, $url)), 'news');
    }

    function kuaidi($data) {
        $data = array_merge($data);
        $str = file_get_contents('http://www.weinxinma.com/api/index.php?m=Express&a=index&name=' . $data [0] . '&number=' . $data [1]);
        return $str;
    }

    function langdu($data) {
        $data = implode('', $data);
        $mp3url = 'http://www.apiwx.com/aaa.php?w=' . urlencode($data);
        return array(array($data, '点听收听', $mp3url, $mp3url), 'music');
    }

    function jiankang($data) {
        if (empty($data))
            return '主人，' . $this->my . "提醒您\n正确的查询方式是:\n健康+身高,+体重\n例如：健康170,65";
        $height = $data [1] / 100;
        $weight = $data [2];
        $Broca = ($height * 100 - 80) * 0.7;
        $kaluli = 66 + 13.7 * $weight + 5 * $height * 100 - 6.8 * 25;
        $chao = $weight - $Broca;
        $zhibiao = $chao * 0.1;
        $res = round($weight / ($height * $height), 1);
        if ($res < 18.5) {
            $info = '您的体形属于骨感型，需要增加体重' . $chao . '公斤哦!';
            $pic = 1;
        } elseif ($res < 24) {
            $info = '您的体形属于圆滑型的身材，需要减少体重' . $chao . '公斤哦!';
        } elseif ($res > 24) {
            $info = '您的体形属于肥胖型，需要减少体重' . $chao . '公斤哦!';
        } elseif ($res > 28) {
            $info = '您的体形属于严重肥胖，请加强锻炼，或者使用我们推荐的减肥方案进行减肥';
        }
        return $info;
    }

    function fujin($keyword) {
        $keyword = implode('', $keyword);
        if ($keyword == false) {
            return $this->my . "很难过,无法识别主人的指令,正确使用方法是:输入【附近+关键词】当" . $this->my . '提醒您输入地理位置的时候就OK啦';
        }
        $data = array();
        $data ['time'] = time();
        $data ['token'] = $this->_get('token');
        $data ['keyword'] = $keyword;
        $data ['uid'] = $this->data ['FromUserName'];
        $re = M('Nearby_user');
        $user = $re->where(array('token' => $this->_get('token'), 'uid' => $data ['uid']))->find();
        if ($user == false) {
            $re->data($data)->add();
        } else {
            $id ['id'] = $user ['id'];
            $re->where($id)->save($data);
        }
        return "主人【" . $this->my . "】已经接收到你的指令\n请发送您的地理位置给我哈";
    }

    function recordLastRequest($key, $msgtype = 'text') {
        $rdata = array();
        $rdata ['time'] = time();
        $rdata ['token'] = $this->_get('token');
        $rdata ['keyword'] = $key;
        $rdata ['msgtype'] = $msgtype;
        $rdata ['uid'] = $this->data ['FromUserName'];
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => $msgtype, 'uid' => $rdata ['uid']))->find();
        if (!$user_request_row) {
            $user_request_model->add($rdata);
        } else {
            $rid ['id'] = $user_request_row ['id'];
            $user_request_model->where($rid)->save($rdata);
        }
    }

    function map($x, $y) {
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => 'text', 'uid' => $this->data ['FromUserName']))->find();
        if (!(strpos($user_request_row ['keyword'], '附近') === FALSE)) {
            $user = M('Nearby_user')->where(array('token' => $this->_get('token'), 'uid' => $this->data ['FromUserName']))->find();
            $keyword = $user ['keyword'];
            $radius = 2000;
            $str = file_get_contents(C('site_url') . '/map.php?keyword=' . urlencode($keyword) . '&x=' . $x . '&y=' . $y);
            $array = json_decode($str);
            $map = array();
            foreach ($array as $key => $vo) {
                $map [] = array($vo->title, $key, rtrim(C('site_url'), '/') . '/tpl/static/images/home.jpg', $vo->url);
            }
            return array($map, 'news');
        } else {
            import("Home.Action.MapAction");
            $mapAction = new MapAction ( );
            if (!(strpos($user_request_row ['keyword'], '开车去') === FALSE) || !(strpos($user_request_row ['keyword'], '坐公交') === FALSE) || !(strpos($user_request_row ['keyword'], '步行去') === FALSE)) {
                if (!(strpos($user_request_row ['keyword'], '步行去') === FALSE)) {
                    $companyid = str_replace('步行去', '', $user_request_row ['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->walk($x, $y, $companyid);
                }
                if (!(strpos($user_request_row ['keyword'], '开车去') === FALSE)) {
                    $companyid = str_replace('开车去', '', $user_request_row ['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->drive($x, $y, $companyid);
                }
                if (!(strpos($user_request_row ['keyword'], '坐公交') === FALSE)) {
                    $companyid = str_replace('坐公交', '', $user_request_row ['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->bus($x, $y, $companyid);
                }
            } else {
                switch ($user_request_row ['keyword']) {
                    case '最近的' :
                        return $mapAction->nearest($x, $y);
                        break;
                }
            }
        }
    }

    function suanming($name) {
        $name = implode('', $name);
        if (empty($name)) {
            return '主人' . $this->my . '提醒您正确的使用方法是[算命+姓名]';
        }
        $data = require_once (CONF_PATH . 'suanming.php');
        $num = mt_rand(0, 80);
        return $name . "\n" . trim($data [$num]);
    }

    function yinle($name) {
        $name = implode('', $name);
        $url = 'http://httop1.duapp.com/mp3.php?musicName=' . $name;
        $str = file_get_contents($url);
        $obj = json_decode($str);
        return array(array($name, $name, $obj->url, $obj->url), 'music');
    }

    function geci($n) {
        $name = implode('', $n);
        @$str = 'http://www.xiaojo.com/api3.php?chat=' . urlencode('歌词' . $name) . '&db=viicmstest&pw=viicmstest';
        $cont = file_get_contents($str);
        $cont = str_replace('<?xml version="1.0" encoding="utf-8"?><weixen><responce>', '', $cont);
        $cont = str_replace('</responce></weixen>', '', $cont);
        return $cont;
    }

    function tianqi($n) {
        $n = implode('', $n);
        $data = M('Weather')->field('code')->where(array('name' => $n))->find();
        $json = file_get_contents("http://m.weather.com.cn/data/101010100.html");
        $name = json_decode($json);
        $str .= $name->weatherinfo->date_y . $name->weatherinfo->week . $name->weatherinfo->city . '的天气是' . $name->weatherinfo->temp1 . $name->weatherinfo->weather1 . $name->weatherinfo->index_d;
        $str .= $name->weatherinfo->city . ' 明天的天气是' . $name->weatherinfo->temp2 . $name->weatherinfo->weather2 . $name->weatherinfo->index48_d;
        $str .= $name->weatherinfo->city . '后天的天气是' . $name->weatherinfo->temp3 . $name->weatherinfo->weather3 . $name->weatherinfo->index48_uv;
        return $str;
    }

    function shouji($n) {
        $n = implode('', $n);
        if (count($n) > 1) {
            $this->error_msg($n);
            return false;
        }
        ;
        $str = file_get_contents('http://www.096.me/api.php?phone=' . $n . '&mode=txt');
        if ($str !== iconv('UTF-8', 'UTF-8', iconv('UTF-8', 'UTF-8', $str)))
            $str = iconv('GBK', 'UTF-8', $str);
        return str_replace('\t', '', str_replace('|', "\n", $str));
    }

    function shenfenzheng($n) {
        $n = implode('', $n);
        if (count($n) > 1) {
            $this->error_msg($n);
            return false;
        }
        $VISIT_URL = "http://api.k780.com/?app=idcard.get&idcard=%s&appkey=10154&sign=2a4843ef82d07863bdc314e47f433b25&format=json";

        $url = sprintf($VISIT_URL, $n);
        $count = @file_get_contents($url);
        //$countArray = json_decode ( $count ); 	
        return $count;
    }

    function gongjiao($data) {
        $data = array_merge($data);
        if (count($data) != 3) {
            $this->error_msg();
            return false;
        }
        $json = file_get_contents("http://www.twototwo.cn/bus/Service.aspx?format=json&action=QueryBusByLine&key=5da453b2-b154-4ef1-8f36-806ee58580f6&zone=" . $data [0] . "&line=" . $data [1]);
        $data = json_decode($json);
        $xianlu = $data->Response->Head->XianLu;
        $xdata = get_object_vars($xianlu->ShouMoBanShiJian);
        $xdata = $xdata ['#cdata-section'];
        $piaojia = get_object_vars($xianlu->PiaoJia);
        $xdata = $xdata . ' -- ' . $piaojia ['#cdata-section'];
        $main = $data->Response->Main->Item->FangXiang;
        $xianlu = $main [0]->ZhanDian;
        $str = "【本公交途经】\n";
        for ($i = 0; $i < count($xianlu); $i ++) {
            $str .= "\n" . trim($xianlu [$i]->ZhanDianMingCheng);
        }
        return $str;
    }

    function huoche($data, $time = '') {
        $data = array_merge($data);
        $data [2] = date('Y', time()) . $time;
        if (count($data) != 3) {
            $this->error_msg($data [0] . '至' . $data [1]);
            return false;
        }
        ;
        $time = empty($time) ? date('Y-m-d', time()) : date('Y-', time()) . $time;
        $json = file_get_contents("http://www.twototwo.cn/train/Service.aspx?format=json&action=QueryTrainScheduleByTwoStation&key=5da453b2-b154-4ef1-8f36-806ee58580f6&startStation=" . $data [0] . "&arriveStation=" . $data [1] . "&startDate=" . $data [2] . "&ignoreStartDate=0&like=1&more=0");
        if ($json) {
            $data = json_decode($json);
            $main = $data->Response->Main->Item;
            if (count($main) > 10) {
                $conunt = 10;
            } else {
                $conunt = count($main);
            }
            for ($i = 0; $i < $conunt; $i ++) {
                $str .= "\n 【编号】" . $main [$i]->CheCiMingCheng . "\n 【类型】" . $main [$i]->CheXingMingCheng . "\n【发车时间】:　" . $time . ' ' . $main [$i]->FaShi . "\n【耗时】" . $main [$i]->LiShi . ' 小时';
                $str .= "\n----------------------";
            }
        } else {
            $str = '没有找到 ' . $name . ' 至 ' . $toname . ' 的列车';
        }
        return $str;
    }

    function fanyi($name) {
        $name = array_merge($name);
        $url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=kylV2rmog90fKNbMTuVsL934&q=" . $name [0] . "&from=auto&to=auto";
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json);
        $str = $json->trans_result;
        if ($str [0]->dst == false)
            return $this->error_msg($name [0]);
        $mp3url = 'http://www.apiwx.com/aaa.php?w=' . $str [0]->dst;
        return array(array($str [0]->src, $str [0]->dst, $mp3url, $mp3url), 'music');
    }

    function caipiao($name) {
        $name = array_merge($name);
        $url = "http://api2.sinaapp.com/search/lottery/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword=" . $name [0];
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json, true);
        $str = $json ['text'] ['content'];
        return $str;
    }

    function mengjian($name) {
        $name = array_merge($name);
        if (empty($name))
            return '周公睡着了,无法解此梦,这年头神仙也偷懒';
        $data = M('Dream')->field('content')->where("`title` LIKE '%" . $name [0] . "%'")->find();
        if (empty($data))
            return '周公睡着了,无法解此梦,这年头神仙也偷懒';
        return $data ['content'];
    }

    function test($name, $data) {
        file_put_contents($name, $data);
    }

    function gupiao($name) {
        $name = array_merge($name);
        $url = "http://api2.sinaapp.com/search/stock/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword=" . $name [0];
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json, true);
        $str = $json ['text'] ['content'];
        return $str;
    }

    function getmp3($data) {
        $obj = new getYu ( );
        $ContentString = $obj->getGoogleTTS($data);
        $randfilestring = 'mp3/' . time() . '_' . sprintf('%02d', rand(0, 999)) . ".mp3";
        file_put_contents($randfilestring, $ContentString);
        return rtrim(C('site_url'), '/') . $randfilestring;
    }

    function xiaohua() {
        $this->chat('笑话');
    }

    function liaotian($name) {
        $name = array_merge($name);
        $this->chat($name [0]);
    }

    function chat($name) {
        $this->requestdata('textnum');
        $check = $this->user('connectnum');
        if ($check ['connectnum'] != 1) {
            return C('connectout');
        }
        if ($name == "你叫什么" || $name == "你是谁") {
            return '咳咳，我是聪明与智慧并存的美女，主人你可以叫我' . $this->my . ',人家刚交男朋友,你不可追我啦';
        } elseif ($name == "你父母是谁" || $name == "你爸爸是谁" || $name == "你妈妈是谁") {
            return '主人,' . $this->my . '是品微创造的,所以他们是我的父母,不过主人我属于你的';
        } elseif ($name == '糗事') {
            $name = '笑话';
        } elseif ($name == '网站' || $name == '官网' || $name == '网址' || $name == '3g网址') {
            return "【品微官网网址】\nwww.pinv.cc\n【品微服务综旨】\n化繁为简,让菜鸟也能使用强大的系统!";
        }
        return "没有你要的内容";
    }

    public function fistMe($data) {
        if ('event' == $data ['MsgType'] && 'subscribe' == $data ['Event']) {
            return $this->help();
        }
    }

    public function help() {
        $data = M('Areply')->where(array('token' => $this->token))->find();
        return array(preg_replace("/(\015\012)|(\015)|(\012)/", "\n", $data ['content']), 'text');
    }

    function error_msg($data) {
        return '没有找到' . $data . '相关的数据';
    }

    public function user($action, $keyword = '') {
        $user = M('Wxuser')->field('uid')->where(array('token' => $this->token))->find();
        $usersdata = M('Users');
        $dataarray = array('id' => $user ['uid']);
        $users = $usersdata->field('gid,diynum,connectnum,activitynum,viptime')->where(array('id' => $user ['uid']))->find();
        $group = M('User_group')->where(array('id' => $users ['gid']))->find();
        if ($users ['diynum'] < $group ['diynum']) {
            $data ['diynum'] = 1;
            if ($action == 'diynum') {
                $usersdata->where($dataarray)->setInc('diynum');
            }
        }
        if ($users ['connectnum'] < $group ['connectnum']) {
            $data ['connectnum'] = 1;
            if ($action == 'connectnum') {
                $usersdata->where($dataarray)->setInc('connectnum');
            }
        }
        if ($users ['viptime'] > time()) {
            $data ['viptime'] = 1;
        }
        return $data;
    }

    public function requestdata($field) {
        $Dao = M("requestdata");
        $data["token"] = $this->token;
        $data["year"] = date("Y");
        $data["month"] = date("m");
        $data["day"] = date("d");
        $data["time"] = time();
        $data[$field] = 1;
        $Dao->add($data);
    }

    function baike($name) {
        $name = implode('', $name);
        if ($name == 'pinvi') {
            return '世界上最牛B的微信营销系统，两天前被腾讯收购，当然这只是一个笑话';
        }
        $name_gbk = iconv('utf-8', 'gbk', $name);
        $encode = urlencode($name_gbk);
        $url = 'http://baike.baidu.com/list-php/dispose/searchword.php?word=' . $encode . '&pic=1';
        $get_contents = $this->httpGetRequest_baike($url);
        $get_contents_gbk = iconv('gbk', 'utf-8', $get_contents);
        preg_match("/URL=(\S+)'>/s", $get_contents_gbk, $out);
        $real_link = 'http://baike.baidu.com' . $out [1];
        $get_contents2 = $this->httpGetRequest_baike($real_link);
        preg_match('#"Description"\scontent="(.+?)"\s\/\>#is', $get_contents2, $matchresult);
        if (isset($matchresult [1]) && $matchresult [1] != "") {
            return htmlspecialchars_decode($matchresult [1]);
        } else {
            return "抱歉，没有找到与“" . $name . "”相关的百科结果。";
        }
    }

    function httpGetRequest_baike($url) {
        $headers = array("User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Accept-Language: en-us,en;q=0.5", "Referer: http://www.baidu.com/");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output === FALSE) {
            return "cURL Error: " . curl_error($ch);
        }
        return $output;
    }

    public function get_tags($title, $num = 10) {
        vendor('Pscws.Pscws4', '', '.class.php');
        $pscws = new PSCWS4 ( );
        $pscws->set_dict(CONF_PATH . 'etc/dict.utf8.xdb');
        $pscws->set_rule(CONF_PATH . 'etc/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags [] = $val ['word'];
        }
        return implode(',', $tags);
    }
    function newYearCard($name=null) {
        $text = "http://wx.drjou.cc/index.php?g=Wap&m=Greeting&a=index";
        return array($text, 'text');
    }
    function nianzhong($name=null) {
        $text = "http://wx.drjou.cc/index.php?g=Wap&m=Bonus&a=index&gid=1";
        return array($text, 'text');
    }
    function womensday($name=null) {
        $text = "http://wx.drjou.cc/index.php?g=Wap&m=Womensday&a=index";
        return array($text, 'text');
    }
    function nianzhong2($name=null) {
        $text = "http://wx.drjou.cc/index.php?g=Wap&m=Bonus&a=index&gid=2";
        return array($text, 'text');
    }
    function auth($name=null) {
        $text = "http://wx.drjou.cc/auth.php";
        return array($text, 'text');
    }
    function hangzhou($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Hangzhou&a=index&openid=$openId";
        $text = "<a href='$url'>杭州线下活动入口</a>";
        return array($text, 'text');
    }
    function yucai($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Yucai&a=index&openid=$openId";
        $text = "<a href='$url'>杭州线下活动入口</a>";
        return array($text, 'text');
    }
    function taiyuan($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Taiyuan&a=index&openid=$openId";
        $text = "<a href='$url'>太原线下活动入口</a>";
        return array($text, 'text');
    }
    function productsecond($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Productsecond&a=index&openid=$openId";
        $text = "<a href='$url'>product second</a>";
        return array($text, 'text');
    }
    function shijiazhuang($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Shijiazhuang&a=index&openid=$openId";
        $text = "<a href='$url'>石家庄线下活动入口</a>";
        return array($text, 'text');
    }
    function product0612($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Productsecond&a=index&openid=$openId";
        $text = "<a href='$url'>点击参与森田药妆沭阳线下活动</a>";
        return array($text, 'text');
    }
    function shumianmo($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Countmask&a=index";
        $text = "<a href='$url'>shumianmo</a>";
        return array($text, 'text');
    }
    function ningbo($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Ningbo&a=index&openid=$openId";
        $text = "<a href='$url'>宁波线下活动入口</a>";
        return array($text, 'text');
    }
    function nanchang($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Nanchang&a=index&openid=$openId";
        $text = "<a href='$url'>南昌天虹百货京东店线下活动入口</a>";
        return array($text, 'text');
    }
    function damo($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Damo&a=index&openid=$openId";
        $text = "<a href='$url'>桂林惠之林线下活动入口</a>";
        return array($text, 'text');
    }
    function meibohui($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Meibohui&a=index&openid=$openId";
        $text = "<a href='$url'>上海新国际博览中心线下活动入口</a>";
        return array($text, 'text');
    }
    function hongtongcheng($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Hongtongcheng&a=index&openid=$openId";
        $text = "<a href='$url'>上海贵阳市鸿通城购物中心线下活动入口</a>";
        return array($text, 'text');
    }
    function huaite($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Huaite&a=index&openid=$openId";
        $text = "<a href='$url'>石家庄怀特广场线下活动入口</a>";
        return array($text, 'text');
    }
    function sjz($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Sjz&a=index";
        $text = "<a href='$url'>sjz</a>";
        return array($text, 'text');
    }
    function Beijingxinshijie($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Beijingxinshijie&a=index&openid=$openId";
        $text = "<a href='$url'>北京新世界利莹线下活动</a>";
        return array($text, 'text');
    }
    function checkWrongCode($keyword,$data){
        $text = "请输入16位防伪码";
        return array($text, 'text');
    }
    function Yinzuo($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Yinzuo&a=index&openid=$openId";
        $text = "<a href='$url'>济南泉城广场山东银座</a>";
        return array($text, 'text');
    }
    function getTest($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Countmask&a=getOpenId";
        $text = "<a href='$url'>get test</a>";
        return array($text, 'text');
    }
    function Suqiandarunfa($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Suqiandarunfa&a=index&openid=$openId";
        $text = "<a href='$url'>宿迁大润发</a>";
        return array($text, 'text');
    }
    function Dinghai($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Dinghai&a=index&openid=$openId";
        $text = "<a href='$url'>华之友---定海店</a>";
        return array($text, 'text');
    }
    function Shenjiamen($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Shenjiamen&a=index&openid=$openId";
        $text = "<a href='$url'>沈家门店</a>";
        return array($text, 'text');
    }
    function Xingjian($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Xingjian&a=index&openid=$openId";
        $text = "<a href='$url'>兴建店</a>";
        return array($text, 'text');
    }
    function Kaihong($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Kaihong&a=index&openid=$openId";
        $text = "<a href='$url'>凯虹广场城市超市</a>";
        return array($text, 'text');
    }
    function Kunshanqianjin($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Kunshanqianjin&a=index&openid=$openId";
        $text = "<a href='$url'>昆山前进大润发</a>";
        return array($text, 'text');
    }
    function Beizhengjie($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Beizhengjie&a=index&openid=$openId";
        $text = "<a href='$url'>玉溪市澄江县北正街雅韵化妆品店</a>";
        return array($text, 'text');
    }

    function Mumayi($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Mumayi&a=index&openid=$openId";
        $text = "<a href='$url'>木蚂蚁杭州下沙店</a>";
        return array($text, 'text');
    }

    function Diweisi($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Diweisi&a=index&openid=$openId";
        $text = "<a href='$url'>陕西缔威斯小寨军区合作社</a>";
        return array($text, 'text');
    }


    function Yangpudarunfa($keyword,$data){
        $openId = (string)$data['FromUserName'];
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Yangpudarunfa&a=index&openid=$openId";
        $text = "<a href='$url'>上海杨浦大润发线下活动</a>";
        return array($text, 'text');
    }
    function anni(){
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Anniversary";
        $text = "<a href='$url'>81周年</a>";
        return array($text, 'text');
    }
    function pretty(){
        $url =  "http://wx.drjou.cc/index.php?g=Wap&m=Pretty";
        $text = "<a href='$url'>pretty</a>";
        return array($text, 'text');
    }
    function checkCode($keyword,$data){

        $openId = (string)$data['FromUserName'];

        $keyword = str_replace(" ","",$keyword);
        $ws = "http://digitcode.yesno.com.cn/CCNOutService/OutDigitCodeService.asmx?wsdl";//webservice服务的地址
        $client = new SoapClient ($ws);
        $param['directoryName'] = '9457';
        $param['mima'] = 'ST@47A4SKE';
        $param['code'] = $keyword;
        $param['ip'] = '115.29.185.117';
        $param['language'] = 1;
        $param['channel'] = 'X';
        $result=$client->Get_CodeIsTrueByChannel($param);

        $replyStatus = (string)$result->systemState;
        if($replyStatus*1 == 2){
            //判断这个用户是否是重新查询的
           // $list = M('secode')->where(array('code' => $keyword))->find();
            $list = M('secode')->Distinct(true)->field('openid')->where(array('code' => $keyword))->select();
            if(count($list) == 1 && $list[0]['openid'] == $openId){
                return array("您已成功查询，是真品", 'text');
            }
        }
        //记录数据
        $d['openid'] = $openId;
        $d['code'] = $keyword;
        $d['status'] = (string)$result->systemState;
        $d['des'] = (string)$result->reply;
        M("secode")->add($d);

        return array((string)$result->reply, 'text');
    }
    function functionlist($keyword,$data=null) {

        switch ($keyword) {
            case '首页' :
                return $this->home();
                break;
            case '测试年终奖' :
                return $this->nianzhong();
                break;
            case '38特别' :
                return $this->womensday();
                break;
            case 'hangzhou' :
                return $this->hangzhou($keyword,$data);
                break;
            case 'yucai' :
                return $this->yucai($keyword,$data);
                break;
            case 'taiyuan' :
                return $this->taiyuan($keyword,$data);
                break;
            case 'shijiazhuang' :
                return $this->shijiazhuang($keyword,$data);
                break;
            case '0612' :
                return $this->product0612($keyword,$data);
                break;
            case '0626' :
                return $this->Yangpudarunfa($keyword,$data);
                break;
            case '0627' :
                return $this->Beijingxinshijie($keyword,$data);
                break;
            case '0709' :
                return $this->Yinzuo($keyword,$data);
                break;
            case 'gettest' :
                return $this->getTest($keyword,$data);
                break;
            case '0722' :
                return $this->Suqiandarunfa($keyword,$data);
                break;
            case '07311' :
                return $this->Dinghai($keyword,$data);
                break;
            case '07312' :
                return $this->Shenjiamen($keyword,$data);
                break;
            case '07313' :
                return $this->Xingjian($keyword,$data);
                break;
            case '07314' :
                return $this->Kaihong($keyword,$data);
                break;
            case '0805' :
                return $this->Kunshanqianjin($keyword,$data);
                break;
            case '0724' :
                return $this->Beizhengjie($keyword,$data);
                break;
            case '0917' :
                return $this->Mumayi($keyword,$data);
                break;
            case '0919' :
                return $this->Diweisi($keyword,$data);
                break;
            case '银座' :
                return $this->Yinzuo($keyword,$data);
                break;
            case '222222' :
                return $this->productsecond($keyword,$data);
                break;
            case 'shumianmo' :
                return $this->shumianmo($keyword,$data);
                break;
            case 'ningbo' :
                return $this->ningbo($keyword,$data);
                break;
            case 'nanchang' :
                return $this->nanchang($keyword,$data);
                break;
            case 'damo' :
                return $this->damo($keyword,$data);
                break;
            case 'meibohui' :
                return $this->meibohui($keyword,$data);
                break;
            case 'hongtongcheng' :
                return $this->hongtongcheng($keyword,$data);
                break;
            case 'huaite' :
                return $this->huaite($keyword,$data);
                break;
            case 'sjz' :
                return $this->sjz($keyword,$data);
                break;
            case 'auth' :
                return $this->auth();
                break;
            case '新年卡' :
                return $this->newYearCard();
                break;
            case '测试2' :
                return $this->nianzhong2();
                break;
            case 'pretty' :
                return $this->pretty();
                break;
            case 'anni' :
                return $this->anni();
                break;
            case '主页' :
                return $this->home();
                break;
            case '地图' :
                return $this->companyMap();
            case '最近的' :
                $this->recordLastRequest($keyword);
                $user_request_model = M('User_request');
                $loctionInfo = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => 'location', 'uid' => $this->data ['FromUserName']))->find();
                if ($loctionInfo && intval($loctionInfo ['time'] > (time() - 60))) {
                    $latLng = explode(',', $loctionInfo ['keyword']);
                    return $this->map($latLng [1], $latLng [0]);
                }
                return array('请发送您所在的位置', 'text');
                break;
            case '帮助' :
                return $this->help();
                break;
            case 'help' :
                return $this->help();
                break;
            case '会员卡' :
                return $this->member();
                break;
            case '会员' :
                return $this->member();
                break;
            case '相册' :
                return $this->xiangce();
                break;
            case '全景' :
                $pro = M('reply_info')->where(array('infotype' => 'panorama', 'token' => $this->token))->find();
                return array(array(array($pro ['title'], strip_tags(htmlspecialchars_decode($pro ['info'])), $pro ['picurl'], C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '相册' :
                return $this->xiangce();
                break;
            case '商城' :
                $pro = M('reply_info')->where(array('infotype' => 'Shop', 'token' => $this->token))->find();
                return array(array(array($pro ['title'], strip_tags(htmlspecialchars_decode($pro ['info'])), $pro ['picurl'], C('site_url') . '/index.php?g=Wap&m=Store&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '活动' :
                return array(array(array('组织活动', '1.组织活动  2.拥抱自然  3.公益活动  4.快乐派对', 'http://mmsns.qpic.cn/mmsns/K0cXugj2RNzwmmkOibv4EbadbATdK4rTT4JvNogt6Tl8icELOia0Q4aUQ/0', C('site_url') . '/index.php?&g=Wap&m=Activity&a=alist&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '微官网' :
                return array(array(array($pro ['title'], strip_tags(htmlspecialchars_decode($pro ['info'])), $pro ['picurl'], C('site_url') . '/cms/index.php?token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '订餐' :
                $pro = M('reply_info')->where(array('infotype' => 'Dining', 'token' => $this->token))->find();
                return array(array(array($pro ['title'], strip_tags(htmlspecialchars_decode($pro ['info'])), $pro ['picurl'], C('site_url') . '/index.php?g=Wap&m=Product&a=dining&dining=1&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '团购' :
                $pro = M('reply_info')->where(array('infotype' => 'Groupon', 'token' => $this->token))->find();
                return array(array(array($pro ['title'], strip_tags(htmlspecialchars_decode($pro ['info'])), $pro ['picurl'], C('site_url') . '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'])), 'news');
                break;
            case '微房产': $Estate = M('Estate')->where(array('token' => $this->token))->find();
                return array(array(array($Estate['title'], str_replace('&nbsp;', '', strip_tags(htmlspecialchars_decode($Estate['estate_desc']))), $Estate['cover'], C('site_url') . '/index.php?g=Wap&m=Estate&a=index&&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $Estate['id'] . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            case is_numeric( str_replace(" ","",$keyword)  )&&((strlen(str_replace(" ","",$keyword)) < 16) || (strlen(str_replace(" ","",$keyword)) > 16) ):
                //验证防伪码
                return $this->checkWrongCode($keyword);
                break;
            case is_numeric( str_replace(" ","",$keyword)  ) && (strlen(str_replace(" ","",$keyword)) == 16):
                //验证防伪码
                return $this->checkCode($keyword,$data);
                break;
            default :

                $check = $this->user('diynum', $keyword);
                if ($check ['diynum'] != 1) {
                    return array(C('connectout'), 'text');
                } else {
                    //file_put_contents('d:/8.txt', var_export($keyword, TRUE));
                    return $this->keyword($keyword,$data);
                }
        }
    }

    /**
     * 2014-03-19----------------------------------------------客服相关代码
     */
    function updateMemberEndTime($openid) {
        $mysql = M('Wehcat_member_enddate');
        $id = $mysql->field('id')->where(array('openid' => $openid))->find();
        $data['enddate'] = time();
        $data['openid'] = $openid;
        $data['token'] = $this->token;
        if ($id == false) {
            $mysql->add($data);
        } else {
            $data['id'] = $id['id'];
            $mysql->save($data);
        }
    }

    function selectService() {
        $this->behaviordata('chat', '');
        $time = time() - (30 * 60);
        $where['token'] = $this->token;
        $serviceUser = M('Service_user')->field('id')->where('`token` = "' . $this->token . '" and `status` = 0 and `endJoinDate` > ' . $time)->select();
        if ($serviceUser != false) {
            $list = M('wechat_group_list')->field('id')->where(array('openid' => $this->data['FromUserName']))->find();
            if (empty($list)) {
                $this->adddUserInfo();
            }
            $serviceJoinDate = M('wehcat_member_enddate')->field('id,uid,joinUpDate')->where(array('token' => $this->token, 'openid' => $this->data['FromUserName']))->find();
            if ($serviceJoinDate['uid'] == false) {
                foreach ($serviceUser as $key => $users) {
                    $user[] = $users['id'];
                }
                if (count($user) == 1) {
                    $id = $user[0];
                } else {
                    $rand = mt_rand(0, count($user) - 1);
                    $id = $user[$rand];
                }
                $where['id'] = $serviceJoinDate['id'];
                $where['uid'] = $id;
                M('wehcat_member_enddate')->data($where)->save();
            } else {
                $endtime = 30 * 60;
                $now = $time - $serviceJoinDate['joinUpDate'];
                if ($now < $endtime) {
                    exit();
                }
            }
        }
    }

    /**
     * 统计相关
     * @param type $field
     * @param type $id
     * @param type $type
     */
    function behaviordata($field, $id = '', $type = '') {
        $data['date'] = date('Y-m-d', time());
        $data['token'] = $this->token;
        $data['openid'] = $this->data['FromUserName'];
        $data['keyword'] = $this->data['Content'];
        if (!$data['keyword']) {
            $data['keyword'] = '用户关注';
        }
        $data['model'] = $field;
        if ($id != false) {
            $data['fid'] = $id;
        }
        if ($type != false) {
            $data['type'] = 1;
        }
        $mysql = M('Behavior');
        $check = $mysql->field('id')->where($data)->find();
        $this->updateMemberEndTime($data['openid']);
        if ($check == false) {
            $data['enddate'] = time();
            $mysql->add($data);
        } else {
            $mysql->where($data)->setInc('num');
        }
    }

    /**
     * 程云添加用于获得粉丝信息
     */
    function adddUserInfo() {
        $access_token = $this->getAccessToken();
        $url2 = 'https://api.weixin.qq.com/cgi-bin/user/info?openid=' . $this->data['FromUserName'] . '&access_token=' . $access_token;
        $classData = json_decode($this->curlGet($url2));
        if ($classData->subscribe == 1) {
            $data['nickname'] = str_replace("'", '', $classData->nickname);
            $data['sex'] = $classData->sex;
            $data['city'] = $classData->city;
            $data['province'] = $classData->province;
            $data['headimgurl'] = $classData->headimgurl;
            $data['subscribe_time'] = $classData->subscribe_time;
            $data['openid'] = $this->data['FromUserName'];
            $data['token'] = $this->token;
            $url3 = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=' . $access_token;
            $json2 = json_decode($this->curlGet($url3, 'post', '{"openid":"' . $data['openid'] . '"}'));
            $data['g_id'] = $json2->groupid;
            M('wechat_group_list')->add($data);
        }
    }

    /**
     * 获得微信给他token
     * @return type
     */
    function getAccessToken() {
        $where = array('token' => $this->token);
        $this->thisWxUser = M('Diymen_set')->where($where)->find();
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->thisWxUser['appid'] . '&secret=' . $this->thisWxUser['appsecret'];
        $json = json_decode($this->curlGet($url_get));
        if (!$json->errmsg) {
            return $json->access_token;
        } else {
            $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
        }
        return $json->access_token;
    }

    /**
     * curl get post 请求
     * @param type $url
     * @param type $method
     * @param type $data
     * @return type
     */
    function curlGet($url, $method = 'get', $data = '') {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

}
