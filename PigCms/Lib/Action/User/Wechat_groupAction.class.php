<?php

class Wechat_groupAction extends UserAction {

    public $thisWxUser;

    public function _initialize() {
        parent :: _initialize();
        $where = array('token' => $this->token);
        $this->thisWxUser = M('Diymen_set')->where($where)->find();
        if ($this->thisWxUser == false) {
            $this->error('必须审请微信高级接口认证<br />若您已经审请了该接口，请填写高级接口配置文件', U('Diymen/index'));
        }
    }

    public function index() {
        $id = $_GET['wechatgroupid'];
        $check = M('wechat_group')->field('sql')->where(array('id' => $id))->find();
    
        if ($check) {
            $list1 = M('member_user')->query($check['sql']);
            $num = count($list1);
            $p = new Page($num, 25);
            $firstRow = $p->firstRow;
            $listRows = $p->listRows;
            $sql1 = $check['sql'] . " limit {$firstRow},{$listRows}";
            $list = M('member_user')->query($sql1);
            $page = $p->show();
            $this->assign('page', $page);
            $this->assign('list', $list);
        }
        $this->display();
    }

    public function send() {
        if (IS_GET) {
            $access_token = $this->_getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token;
            if (isset($_GET['next_openid'])) {
                $url .= '&next_openid=' . $_GET['next_openid'];
            }
            $json_token = json_decode($this->curlGet($url));
            $arrayData = $json_token->data->openid;
            $nextOpenID = $json_token->next_openid;
            $a = 0;
            $b = 0;
            foreach ($arrayData as $data) {
                $check = M('Wechat_group_list')->field('openid')->where(array('openid' => $data))->find();
                if ($check == false) {
                    M('Wechat_group_list')->data(array('openid' => $data, 'token' => $this->token))->add();
                    $a++;
                } else {
                    $b++;
                }
            }
            if (strlen($nextOpenID)) {
                $this->success('本次更新' . $a . '条,重复' . $b = $b == 1 ? 0 : $b . '条，正在获取下一批粉丝数据', '?g=User&m=Wechat_group&a=send&token=' . $this->token . '&next_openid=' . $nextOpenID);
            } else {
                $this->success('更新完成,现在获取粉丝详细信息', '?g=User&m=Wechat_group&a=send_info&token=' . $this->token);
            }
        } else {
            $this->error('非法操作');
        }
    }

    public function send_info() {
        if (IS_GET) {
            $refreshAll = isset($_GET['all']) ? 1 : 0;
            $access_token = $this->_getAccessToken();
            if ($refreshAll) {
                $fansCount = M('Wechat_group_list')->where(array('token' => session('token')))->count();
                $i = intval($_GET['i']);
                $step = 20;
                $fans = M('Wechat_group_list')->where(array('token' => session('token')))->order('id DESC')->limit($i, $step)->select();
                if ($fans) {
                    foreach ($fans as $data_all) {
                        $url2 = 'https://api.weixin.qq.com/cgi-bin/user/info?openid=' . $data_all['openid'] . '&access_token=' . $access_token;
                        $classData = json_decode($this->curlGet($url2));
                        if ($classData->subscribe == 1) {
                            $data['nickname'] = str_replace("'", '', $classData->nickname);
                            $data['sex'] = $classData->sex;
                            $data['city'] = $classData->city;
                            $data['province'] = $classData->province;
                            $data['headimgurl'] = $classData->headimgurl;
                            $data['subscribe_time'] = $classData->subscribe_time;
                            S($this->token . '_' . $data_all['openid'], null);
                            $url3 = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=' . $access_token;
                            $json2 = json_decode($this->curlGet($url3, 'post', '{"openid":"' . $data['openid'] . '"}'));
                            $data['g_id'] = $json->groupid;
                            M('wechat_group_list')->where(array('id' => $data_all['id']))->save($data);
                            //同时吧粉丝资料放到 tp_member_user 表
                            $u['token'] = $this->token;
                            $u['openid'] = $data['openid'];
                            $u['u_name'] = $data['nickname'];
                            $u['u_sex'] = $data['sex'];
                            $u['u_money'] = 0;
                            $u['u_address'] = $data['city'];
                            $u['u_form'] = '微信';
                            $u['u_member'] = 1;
                            $u['starttime'] = time();
                            M('member_user')->add($u);
                        } else {
                            M('wechat_group_list')->delete($data_all['id']);
                        }
                    }
                    $i = $step + $i;
                    $this->success('更新中请勿关闭...进度：' . $i . '/' . $fansCount, '?g=User&m=Wechat_group&a=send_info&token=' . $this->token . '&all=1&i=' . $i);
                } else {
                    $this->success('更新完毕', '?g=User&m=Wechat_group&a=index&token=' . $this->token);
                    exit();
                }
            } else {
                $dataAll = M('Wechat_group_list')->field('openid,id')->where(array('token' => session('token'), 'subscribe_time' => ''))->order('id desc')->limit(20)->select();
                if ($dataAll == false) {
                    $this->success('更新完毕', '?g=User&m=Wechat_group&a=index&token=' . $this->token);
                    exit();
                }
                $i = 0;
                foreach ($dataAll as $data_all) {
                    $url2 = 'https://api.weixin.qq.com/cgi-bin/user/info?openid=' . $data_all['openid'] . '&access_token=' . $access_token;
                    $classData = json_decode($this->curlGet($url2));
                    if ($classData->subscribe == 1) {
                        $data['openid'] = $classData->openid;
                        $data['nickname'] = str_replace("'", '', $classData->nickname);
                        $data['sex'] = $classData->sex;
                        $data['city'] = $classData->city;
                        $data['province'] = $classData->province;
                        $data['headimgurl'] = $classData->headimgurl;
                        $data['subscribe_time'] = $classData->subscribe_time;
                        $data['token'] = session('token');
                        $data['id'] = $data_all['id'];
                        S($this->token . '_' . $data_all['openid'], null);
                        $url3 = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=' . $access_token;
                        $json2 = json_decode($this->curlGet($url3, 'post', '{"openid":"' . $data['openid'] . '"}'));
                        $data['g_id'] = $json->groupid;
                        M('wechat_group_list')->save($data);
                        //同时吧粉丝资料放到 tp_member_user 表
                        $u['token'] = $this->token;
                        $u['openid'] = $data['openid'];
                        $u['u_name'] = $data['nickname'];
                        $u['u_sex'] = $data['sex'];
                        $u['u_money'] = 0;
                        $u['u_address'] = $data['city'];
                        $u['u_form'] = '微信';
                        $u['u_member'] = 1;
                        $u['starttime'] = time();
                        M('member_user')->add($u);
                        $i++;
                    } else {
                        M('wechat_group_list')->delete($data_all['id']);
                    }
                }
                $count = M('Wechat_group_list')->field('id')->where(array('token' => session('token'), 'subscribe_time' => ''))->count();
                $this->success('还有' . $count . '个粉丝信息没有更新,<br />请耐心等待', U('Wechat_group/send_info'));
            }
        } else {
            $this->error('非法操作');
        }
    }

    public function setGroup() {
        if (IS_POST) {
            $wechat_group_list_db = M('wechat_group_list');
            $wechatgroupid = intval($this->_post('wechatgroupid'));
            $access_token = $this->_getAccessToken();
            foreach ($_POST as $k => $v) {
                if (!(strpos($k, 'id_') === FALSE)) {
                    $id = intval(str_replace('id_', '', $k));
                    $thisFans = $wechat_group_list_db->where(array('id' => $id, 'token' => $this->token))->find();
                    $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $access_token;
                    $json = json_decode($this->curlGet($url, 'post', '{"openid":"' . $thisFans['openid'] . '","to_groupid":' . $wechatgroupid . '}'));
                    $wechat_group_list_db->where(array('id' => $id))->save(array('g_id' => $wechatgroupid));
                }
            }
            $this->success('设置完毕', '?g=User&m=Wechat_group&a=index&token=' . $this->token);
        }
    }

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

    function showExternalPic() {
        $types = array('gif' => 'image/gif', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'jpe' => 'image/jpeg', 'png' => 'image/png',);
        $wecha_id = $this->_get('wecha_id');
        $token = $this->_get('token');
        $imgData = S($token . '_' . $wecha_id);
        if (!$imgData) {
            $url = $_GET['url'];
            $dir = pathinfo($url);
            $host = $dir['dirname'];
            $refer = 'http://www.qq.com/';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_REFERER, $refer);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $ext = strtolower(substr(strrchr($url, '.'), 1, 10));
            $ext = 'jpg';
            $type = $types[$ext] ? $types[$ext] : 'image/jpeg';
            S($token . '_' . $wecha_id, $data);
            header("Content-type: " . $type);
            echo $data;
        } else {
            $ext = 'jpg';
            $type = $types[$ext] ? $types[$ext] : 'image/jpeg';
            header("Content-type: " . $type);
            echo $imgData;
        }
    }

    function groups() {
        $wechat_group_db = M('Wechat_group');
        $groups = $wechat_group_db->where(array('token' => $this->token))->order('id ASC')->select();
        $this->assign('groups', $groups);
        $this->display();
    }
    function groupdel(){
           $id=$_GET['id'];
           $wechat_group_db = M('Wechat_group');
           $groups = $wechat_group_db->where(array('token' => $this->token,'id'=>$id))->delete();
           if($groups){
               $this->success("删除成功");
           }else{
               $this->error("删除失败");
           }
           
    }
                function sysGroups() {
        $wechat_group_db = M('Wechat_group');
        $access_token = $this->_getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . $access_token;
        $json = json_decode($this->curlGet($url));
        $wechat_groups = $json->groups;
        $wechat_groups_ids = array();
        if ($wechat_groups) {
            foreach ($wechat_groups as $g) {
                $thisGroupInDb = $wechat_group_db->where(array('token' => $this->token, 'wechatgroupid' => $g->id))->find();
                $arr = array('token' => $this->token, 'wechatgroupid' => $g->id, 'name' => $g->name, 'fanscount' => $g->count);
                if (!$thisGroupInDb) {
                    $wechat_group_db->add($arr);
                } else {
                    $wechat_group_db->where(array('id' => $thisGroupInDb['id']))->save($arr);
                }
                array_push($wechat_groups_ids, $g->id);
            }
        }
        $groups = $wechat_group_db->where(array('token' => $this->token))->order('id ASC')->select();
        if ($groups) {
            foreach ($groups as $g) {
                if (!in_array($g['wechatgroupid'], $wechat_groups_ids)) {
                    $wechat_group_db->where(array('id' => $g['id']))->delete();
                }
            }
        }
        $this->success('操作成功', U('Wechat_group/groups'));
    }

    function groupSet() {
        $wechat_group_db = M('Wechat_group');
        $thisGroup = $wechat_group_db->where(array('id' => intval($_GET['id'])))->find();
        if ($thisGroup && $thisGroup['token'] != $this->token) {
            $this->error('非法操作');
        }
        if (IS_POST) {
            $arr = array();
            $arr['name'] = $this->_post('name');
            $arr['intro'] = $this->_post('intro');
            $arr['token'] = $this->token;
            if (isset($_POST['id'])) {  
                $wechat_group_db->where(array('id' => intval($_POST['id'])))->save($arr);
            } else {
                $wechat_group_db->add($arr);
            }
            $this->success('操作成功', U('Wechat_group/groups'));
        } else {

            $this->assign('thisGroup', $thisGroup);
            $this->display();
        }
    }
    function _getAccessToken() {
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->thisWxUser['appid'] . '&secret=' . $this->thisWxUser['appsecret'];
        $json = json_decode($this->curlGet($url_get));
        if (!$json->errmsg) {
            
        } else {
            $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
        }
        return $json->access_token;
    }

}

?>