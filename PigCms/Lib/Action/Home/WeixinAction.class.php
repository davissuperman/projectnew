<?php

class WeixinAction extends CommonAction {

    public function index() {
        $this->token = $this->_get('token');
        $weixin = new Wechat($this->token);
        $data = $weixin->request();
        $this->data = $weixin->request();
        $this->my = C('site_my');
        list ( $content, $type ) = $this->reply($data);
        $weixin->response($content, $type);
    }

    private function reply($data) {
        $tmpData = $data;
        $memberinfo = M('member_user')->where(array('token' => $this->token, 'openid' => $data['FromUserName']))->find();
        if ($memberinfo) {
            $u['interaction'] = time();
            M('member_user')->where(array('token' => $this->token, 'openid' => $data['FromUserName']))->save($u);
        }
        switch ($data ['Event']) {
            case 'CLICK':
                $data ['Content'] = $data ['EventKey'];
                //点击菜单拉取消息时的事件推送记录保存

                if ($data ['Content']) {
                    $menuClickData['keyword'] = $data ['Content'];
                    $menuClickData['click_time'] = time();
                    $menuClickData['token'] = $this->token;
                    $menuClickData['openid'] = $data ['FromUserName'];
                    //根据获取的keyword和token查到对应的菜单名称、id
                    $twhere['keyword'] = $data ['Content'];
                    $twhere['token'] = $this->token;
                    $tdata = M('diymen_class')->where($twhere)->find();
                    if ($tdata['id']) {
                        $menuClickData['mid'] = $tdata['id'];
                        $menuClickData['mpid'] = $tdata['pid'];
                        $menuClickData['title'] = $tdata['title'];
                        if ($tdata['url']) {
                            $menuClickData['url'] = $tdata['url'];
                        }
                        $menuClickData['is_reached'] = 1;
                        M('User_menu_click')->add($menuClickData);
                    }
                }

                //用户活跃度统计
                $this->userAction('active_user');
                break;
            case 'VIEW':
                $clickUrl = $data ['EventKey'];
                //点击菜单跳转链接时的事件推送记录保存
                if ($clickUrl) {
                    $menuClickData['url'] = htmlspecialchars_decode($clickUrl); //必须这么处理，把html实体转为字符，即把&amp;
                    $menuClickData['click_time'] = time();
                    $menuClickData['token'] = $this->token;
                    $menuClickData['openid'] = $data ['FromUserName'];
                    if(stristr($clickUrl,'rank') && stristr($clickUrl,'countmask')){
                        $menuClickData['url'] = $menuClickData['url']."&openid=".$menuClickData['openid'];
                    }
                    //根据获取的url和token查到对应的菜单名称、关键词、id
                    $strPos = strrpos($menuClickData['url'], '&mid');
                    if ($strPos) {
                        $twhere['url'] = substr($menuClickData['url'], 0, $strPos); //去掉传给weixin的mid
                        $twhere['token'] = $this->token;
                        $tdata = M('diymen_class')->where($twhere)->find();
                        if ($tdata['id']) {
                            $menuClickData['mid'] = $tdata['id'];
                            $menuClickData['mpid'] = $tdata['pid'];
                            $menuClickData['title'] = $tdata['title'];
                            if ($tdata['keyword']) {
                                $menuClickData['keyword'] = $tdata['keyword'];
                            }
                            $menuClickData['is_reached'] = 0; //这里面暂时都设置为都到达了
                            M('User_menu_click')->add($menuClickData);
                        }
                    }
                }
                //用户活跃度统计
                $this->userAction('active_user');
                break;
            case 'subscribe':
                $this->requestdata('follownum');
                $this->userAction('subscribe'); //关注
                $fanModel = M('customer_service_fans'); //向粉丝表中添加此用户信息
                $ffind = $fanModel->where(array('openid' => $data['FromUserName'], 'token' => $this->token))->find();

                if ($ffind) {
                    $this->subscribe(1); //如果粉丝表中有此用户则重新关注
                } else {
                    //从微信拉取用户基本信息
                    $access_token = $this->getAccessToken();
                    $gUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $data['FromUserName'] . '&lang=zh_CN';
                    $json = json_decode($this->curlGet($gUrl)); //这样转化过来的是object类型，内容必须用object->action形式输出
                    if ($json->errcode) {//有些用户没有高级接口，不能获得用户信息
                    } else {
                        $fdata['openid'] = $data['FromUserName'];
                        $fdata['token'] = $this->token;
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
                        $fdata['subscribe_time'] = $json->subscribe_time;
                        $fdata['subscribe'] = 1;
                        $fanModel->add($fdata);
                        //同时吧粉丝资料放到 tp_member_user 表
                        $memberinfo = M('member_user')->where(array('token' => $this->token, 'openid' => $data['FromUserName']))->find();
                        $u['u_name'] = $fdata['nickname'];
                        $u['u_sex'] = $fdata['sex'];
                        $u['u_money'] = 0;
                        $u['u_address'] = $fdata['city'];
                        $u['u_member'] = 1;
                        if ($memberinfo) {
                            M('member_user')->where(array('token' => $this->token, 'openid' => $u['openid']))->save($u);
                        } else {
                            $u['token'] = $this->token;
                            $u['openid'] = $data['FromUserName'];
                            $u['u_form'] = '微信';
                            $u['starttime'] = time();
                            $u['subscribe'] = 1;
                            $u['interaction'] = time();
                            M('member_user')->add($u);
                        }
                    }
                }

                $data = M('Areply')->field('home,keyword,content')->where(array('token' => $this->token))->find();
                if ($data ['home'] == 1) {
                    $like ['keyword'] = array('like', '%' . $data ['keyword'] . '%');
                    $like ['token'] = $this->token;
                    $back = M('Img')->field('id,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
                    foreach ($back as $keya => $infot) {
                        if ($infot ['url'] != false) {
                            $url = $infot ['url'];
                        } else {
                            $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot ['id']));
                        }
                        $return [] = array($infot ['title'], $infot ['text'], $infot ['pic'], $url);
                    }
                    return array($return, 'news');
                } else {
                    if ($data ['keyword']) {
                        return $this->functionlist($data ['keyword']);
                    } else {
                        return array($data ['content'], 'text');
                    }
                }
                break;
            case 'unsubscribe':
                $this->requestdata('unfollownum');
                $this->userAction('unsubscribe');
                $this->subscribe(0); //更新粉丝状态
                break;
            case 'MASSSENDJOBFINISH':
                M('Send_message')->where(array('msg_id' => $this->data['MsgID']))->save(array('sentcount' => $this->data['SentCount'], 'errorcount' => $this->data['ErrorCount']));
                exit;
            case 'LOCATION':
                $this->lbsRequestLocation($this->data);
                break;
            default:
                break;
        }
        $Pin = new GetPin ();
        $key = $data ['Content'];
        $open = M('Token_open')->where(array('token' => $this->_get('token')))->find();
        $this->fun = $open['queryname'];
        $datafun = explode(',', $open['queryname']);
        if ($key != '客服') {
            $tags = $this->get_tags($key);
        } else {
            $tags = '多客服';
        }
        if ($key != '多客服') {
            $tags = $this->get_tags($key);
        } else {
            $tags = '多客服';
        }
        $back = explode(',', $tags);
        foreach ($back as $keydata => $data) {
            $string = $Pin->Pinyin($data);
            if (in_array($string, $datafun)) {
                $check = $this->user('connectnum');
                if ($string == 'fujin') {
                    $this->recordLastRequest($key);
                }
                $this->requestdata('textnum');
                if ($check ['connectnum'] != 1) {
                    $return = C('connectout');
                    continue;
                }
                unset($back [$keydata]);
                eval('$return= $this->' . $string . '($back);');
                continue;
            }
        }

        //把用户输入的关键词保存到数据库
        if ($key) {
            $clickData['keyword'] = $key;
            $find = M('keyword')->where(array('token' => $this->token, 'keyword' => $clickData['keyword']))->find();
            $clickData['click_time'] = time();
            $clickData['token'] = $this->token;
            $clickData['openid'] = $this->data ['FromUserName'];
            $clickData['matched'] = $find ? 1 : 0; //0为未到达，1为已到达
            $clickData['click_date'] = date('Y-m-d');
            $clickData['click_hour'] = date('H');
            M('user_keyword_click')->add($clickData);
        }

        if (!empty($return)) {
            if (is_array($return)) {
                return $return;
            } else {
                return array($return, 'text');
            }
        } else {
            if ($this->data ['Location_X']) {
                //$this->recordLastRequest($this->data ['Location_Y'] . ',' . $this->data ['Location_X'], 'location');
                $this->data['Longitude'] = $this->data ['Location_Y'];
                $this->data['Latitude'] = $this->data ['Location_X'];

                $this->lbsRequestLocation($this->data);
                // file_put_contents('d:/d.txt', var_export($this->data, true) );
            }

            return $this->functionlist($key,$tmpData);
        }
    }

    //腾讯多客服系统对接
    function duokefu() {
        return array('呼叫客服', 'transfer_customer_service');
    }

    /**
     * 输入客服 进入客服模式
     */
    function kefu() {
        return array('呼叫客服', 'transfer_customer_service');
    }

    //LBS----------------------------------------------------------------------开始
    /**
     * 附近的店店铺关键词 店铺
     */
    function dianpu() {

        $user_location = M('company_location');
        $user_row = $user_location->field(array('lasttime', 'latitude', 'longitude'))->where(array('token' => $this->_get('token'), 'openid' => $this->data ['FromUserName']))->find();

        if ($user_row) {
            if ((time() - $user_row['lasttime']) > 600) {
                return array('你的位置信息过期！请输入你的位。 点击菜单附近的店', 'text');
            } else {
                //file_put_contents('d:/321.txt', var_export($user_row, true) );
                return $this->lbscompanyMap($user_row['latitude'], $user_row['longitude']);
            }
        } else {
            return array('没有检测到你的位置信息！请输入你的位。 点击菜单附近的店', 'text');
        }
    }

    /**
     * 附近的活动
     * @return type
     */
    function huodong() {
        $user_location = M('company_location');
        $user_row = $user_location->field(array('lasttime', 'latitude', 'longitude'))->where(array('token' => $this->_get('token'), 'openid' => $this->data ['FromUserName']))->find();
        if ($user_row) {
            if ((time() - $user_row['lasttime']) > 600) {
                return array('你的位置信息过期！请输入你的位。 点击菜单附近活动', 'text');
            } else {
                return $this->lbscompanyMap($user_row['latitude'], $user_row['longitude'], 'huodong');
            }
        } else {
            return array('没有检测到你的位置信息！请输入你的位。 点击菜单附近活动！！', 'text');
        }
    }

    /**
     * 距离你最近的店铺/活动
     * @return type
     */
    function lbscompanyMap($x, $y, $action = 'lbs') {
        import("Home.Action.MapAction");
        $mapAction = new MapAction();
        return $mapAction->$action($x, $y, $this->data['FromUserName']);
    }

    /**
     * 记录用户位置
     * @param type $key
     * @param type $msgtype
     */
    function lbsRequestLocation($info) {
        $rdata = array();
        $rdata ['lasttime'] = time();
        $rdata ['token'] = $this->_get('token');
        $rdata ['longitude'] = $info ['Longitude'];
        $rdata ['latitude'] = $info ['Latitude'];
        $rdata ['openid'] = $info ['FromUserName'];
        $user_request_model = M('company_location');
        $user_request_row = $user_request_model->field('lasttime')->where(array('token' => $this->$rdata ['token'], 'openid' => $rdata ['openid']))->find();
        if (!$user_request_row) {
            $user_request_model->add($rdata);
        } else {
            if ($user_request_row && (time() - $user_request_row['lasttime']) > 60) {
                $user_request_model->where(array('token' => $this->$rdata ['token'], 'openid' => $rdata ['openid']))->save($rdata);
            }
        }
        exit;
    }

    //end-LBS----------------------------------------------------------------------

    /**
     * 综合统计数据
     */
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

    /**
     * 关注和取消关注时tp_fans表的关注状态更新
     * add by wuhaiyan 2014/4/11
     * @param int $do 动作，1关注，0取消关注 3:member_user 表关注
     */
    public function subscribe($do) {

        $fan = M('Customer_service_fans');
        $fdata['subscribe'] = $do;
        $fwhere['openid'] = $this->data['FromUserName'];
        $fwhere['token'] = $this->token;
        $fdata['subscribe_time'] = time();
        $find = $fan->where($fwhere)->find();
        if ($find) {
            $fan->where($fwhere)->save($fdata);
        }
        if ($do == 0) {
            M('member_user')->where($fwhere)->save(array('subscribe' => 0, 'u_member' => 0));
        }
        if ($do == 1) {
            $x = M('member_user')->where($fwhere)->find();
            if ($x['u_money'] > 0) {
                M('member_user')->where($fwhere)->save(array('subscribe' => 1, 'u_member' => 4));
            } else {
                if ($x['tel']) {
                    M('member_user')->where($fwhere)->save(array('subscribe' => 1, 'u_member' => 3));
                } else {
                    M('member_user')->where($fwhere)->save(array('subscribe' => 1, 'u_member' => 1));
                }
            }
        }
    }

    /**
     * 用户关注、取消关注、绑定会员卡等操作记录
     * add by wuhaiyan 2014/4/11
     * @param string $type 操作类型
     */
    public function userAction($type) {
        $action = M('User_action');
        if ($type == 'active_user' || $type == 'binding') {
            $find = $action->where(array('token' => $this->token, 'openid' => $this->data['FromUserName'], 'type' => $type, 'date' => date('Y-m-d')))->find();
            if ($find) {
                return;
            }
        }
        $data['type'] = $type;
        $data['openid'] = $this->data['FromUserName'];
        $data['token'] = $this->token;
        $data['time'] = time();
        $data['date'] = date('Y-m-d');
        $data['hour'] = date('H');
        $action->add($data);
    }

    /**
     * 获得微信给他accessToken
     * @return string $json->access_token
     */
    public function getAccessToken() {
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
     * @param  string $url
     * @param  string $method post or get
     * @param  type   $data
     * @return array  $temp
     */
    public function curlGet($url, $method = 'get', $data = '') {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
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

?>