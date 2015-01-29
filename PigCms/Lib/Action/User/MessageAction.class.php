<?php

class MessageAction extends UserAction {

    public $token;
    private $appid;
    private $appsecret;

    public function _initialize() {
        parent :: _initialize();
        $this->token = session('token');
        $infos = M("diymen_set")->where("token='" . $this->token . "'")->find();
        $this->appid = $infos['appid'];
        // $this->appid="wxb538c5b0df6072c9";
        $this->appsecret = $infos['appsecret'];
        // $this->appsecret="ecf7d8d90ac6d059af62ec619c2120b9";
    }

    public function index() {
        $token = $this->token;
        $where = "token='$token'";
        $this->groups = M("wechat_group")->where($where)->select();
        $this->imgs = M("img")->where($where)->select();
        $this->token = $token;

        if (IS_POST) {
            $row = array();
            $row['msgtype'] = $this->_post('msgtype');
            $row['mediasrc'] = $this->_post('mediasrc');
            $row['text'] = $this->_post('text');
            $row['imgids'] = $this->_post('imgids');
            $row['token'] = $this->token;
            $row['time'] = time();
            if ($row['msgtype'] != 'text' && $row['msgtype'] != 'news' && strpos($_SERVER['HTTP_HOST'], 'pigcms')) {
                $this->error('演示站禁止文件上传，所以请测试文本消息和图文消息的发送，谢谢配合');
            }
            //
            if (isset($_POST['mediasrc']) && trim($_POST['mediasrc'])) {
                $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
                $json = json_decode($this->curlGet($url_get));
                if (!$json->errmsg) {
                    $postMedia = array();
                    $postMedia['access_token'] = $json->access_token;
                    $postMedia['type'] = $row['msgtype'];
                    $postMedia['media'] = $_SERVER['DOCUMENT_ROOT'] . str_replace('http://' . $_SERVER['HTTP_HOST'], '', $row['mediasrc']);
                    $rt = $this->curlPost('http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $postMedia['access_token'] . '&type=' . $postMedia['type'], array('media' => '@' . $postMedia['media']));
                    if ($rt['rt'] == false) {
                        $this->error('操作失败,curl_error:' . $rt['errorno']);
                    } else {
                        $media_id = $rt['media_id'];
                        $row['mediaid'] = $media_id;
                    }
                } else {
                    $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
                }
            }
            $id = M('Send_message')->add($row);
            $this->success('添加成功，现在开始发送信息', U('Message/send', array('id' => $id)));
        } else {

            $this->display();
        }
    }

    public function send() {
        $fans = M('Wechat_group_list')->where(array('token' => $this->token))->order('id ASC')->select();
        //$fans=array(array('openid'=>'oCsUfuC0mqT4VM6JjbggaLvzGEXI'));
        $i = intval($_GET['i']);
        $count = count($fans);
        $thisMessage = M('Send_message')->where(array('id' => intval($_GET['id'])))->find();
        if ($i < $count) {
            $fan = $fans[$i];
            $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
            $json = json_decode($this->curlGet($url_get));
            if (!$json->errmsg) {

                switch ($thisMessage['msgtype']) {
                    case 'text':
                        $data = '{"touser":"' . $fan['openid'] . '","msgtype":"text","text":{"content":"' . $thisMessage['text'] . '"}}';
                        break;
                    case 'image':
                    case 'voice':
                        $data = '{"touser":"' . $fan['openid'] . '","msgtype":"' . $thisMessage['msgtype'] . '","' . $thisMessage['msgtype'] . '":{"media_id":"' . $thisMessage['mediaid'] . '"}}';
                        break;
                    case 'video':
                        $data = '{"touser":"' . $fan['openid'] . '","msgtype":"' . $thisMessage['msgtype'] . '","' . $thisMessage['msgtype'] . '":{"media_id":"' . $thisMessage['mediaid'] . '","description":"' . $thisMessage['text'] . '","title":"' . $thisMessage['title'] . '"}}';
                        break;
                    case 'music':
                        $data = '{"touser":"' . $fan['openid'] . '","msgtype":"' . $thisMessage['msgtype'] . '","' . $thisMessage['msgtype'] . '":{"media_id":"' . $thisMessage['mediaid'] . '"}}';
                        break;
                    case 'news':
                        $imgids = explode(',', $thisMessage['imgids']);
                        $imgID = 0;
                        if ($imgids) {
                            foreach ($imgids as $ii) {
                                if (intval($ii)) {
                                    $imgID = $ii;
                                }
                            }
                        }
                        $thisNews = M('Img')->where(array('id' => $imgID))->find();
                        if ($thisNews['url']) {
                            $url = str_replace(array('{wechat_id}', '{siteUrl}', '&amp;'), array($fan['openid'], C('site_url'), '&'), $thisNews['url']);
                        } else {
                            $url = C('site_url') . U('Wap/Index/content', array('token' => $this->token, 'wecha_id' => $fan['openid'], 'id' => $thisNews['id']));
                        }
                        $data = '{"touser":"' . $fan['openid'] . '","msgtype":"' . $thisMessage['msgtype'] . '","news":{"articles":[{"title":"' . $thisNews['title'] . '","description":"' . $thisNews['text'] . '","url":"' . $url . '","picurl":"' . $thisNews['pic'] . '"}]}}';
                        break;
                }
                //
                $rt = $this->curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $json->access_token, $data, 0);
                if ($rt['rt'] == false) {
                    //$this->error('操作失败,curl_error:'.$rt['errorno']);
                } else {
                    M('Send_message')->where(array('id' => intval($thisMessage['id'])))->setInc('reachcount');
                }
                $i++;
                $this->success('发送中:' . $i . '/' . $count, U('Message/send', array('id' => $thisMessage['id'], 'i' => $i)));
            } else {
                $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
            }
        } else {
            $this->success('发送完成，发送成功' . $thisMessage['reachcount'] . '条', U('Message/sendHistory', array('token' => $this->token)));
        }
    }

    public function notice() {
        $token = $this->token;
        $where = "token='$token'";
        $this->groups = M("wechat_group")->where($where)->select();
        $this->imgs = M("img")->where($where)->select();
        $this->token = $token;
        $this->display();
    }

    public function noticesend() {
        $token = $this->token;
        $imgids = $_REQUEST['s'];
        $title = $_REQUEST['title'];
        $groupid = $_REQUEST['groupid'];
        if (count($imgids)) {
            $imgs = M('img')->where(array('id' => array('in', $imgids)))->select();
        }
        $api = M('Diymen_set')->where(array('token' => session('token')))->find();
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api ['appid'] . '&secret=' . $api['appsecret'];
        $json = json_decode($this->curlGet($url_get));
        $qrcode_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $json->access_token;
        $news = array();
        foreach ($imgs as $k => $v) {
            $news[$k]['title'] = $v['text'];
            $news[$k]['description'] = $v['text'];
            $news[$k]['url'] =C('site_url').'/index.php?g=Wap&m=Index&a=content&id='.$v['id'].'&token=rggfsk1394161441';
            $news[$k]['picurl'] = $v['pic'];
        }
        $news = json_encode($news);
        $check = M('wechat_group')->field('sql')->where(array('wechatgroupid' => $groupid))->find();
        $user = M('member_user')->query($check['sql']);
        $e = 0;
        $s = 0;
        foreach ($user as $key => $value) {
            if ($value['openid']) {
                $senddata = '{"touser":"'.$value['openid'].'","msgtype":"news","news":{ "articles":' . $news . '}}';
                $post = $this->curlGet($qrcode_url, 'post', $senddata);
                $json_decode = json_decode($post);
            }
            if ($json_decode->errmsg == 'ok') {//发送成功
                $s = $s + 1;
            } else {//发送失败
                $e = $e + 1;
            }
        }
   
        $d['title'] = $title . '--48小时';
        $d['errorcount'] = $e;
        $d['token'] = $token;
        $d['msgtype'] = '图文';
        
        $d['sentcount'] = $s;
        $d['groupid'] = $groupid;
        $d['imgids'] =implode(',', $imgids) ;
        $d['time']=time();
        $y = M('send_message')->add($d);
        
        $this->success('48小时群发微信！成功' . $s . ' 人 ，失败' . $e . '人');exit;
    }
    public function history() {
        $db = M('Send_message');
        $where['token'] = $this->token;
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->assign('info', $info);
        $this->display();
    }

    public function sendHistory() {
        $db = M('Send_message');
        $where['token'] = $this->token;
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->assign('info', $info);
        $this->display();
    }

    public function item() {
        if (isset($_GET['id'])) {
            $info = M('Send_message')->where(array('token' => $this->token, 'id' => intval($_GET['id'])))->find();
            $this->assign('info', $info);
        }
        if ($info['msgtype'] == 'news') {
            $imgids = explode(',', $info['imgids']);
            $imgID = 0;
            if ($imgids) {
                foreach ($imgids as $ii) {
                    if (intval($ii)) {
                        $imgID = $ii;
                    }
                }
            }
            $thisNews = M('Img')->where(array('id' => $imgID))->find();
            $this->assign('img', $thisNews);
        }
        $this->display();
    }

    public function fSend() {
        // var_dump($_REQUEST);
        $token = $this->token;
        $imgids = $_REQUEST['imgids'];
        $title = $_REQUEST['title'];
        $groupid = $_REQUEST['groupid'];
        if (count($imgids)) {
            $imgs = M('img')->where(array('id' => array('in', $imgids)))->select();
        }
        if (!$imgs)
            $this->error('请选择图文消息', U('Message/index', array("token" => $token)));
        $url_at = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret;
        $json = json_decode($this->curlGet($url_at));
        $mediaids = '';
        if (!$json->errmsg) {
            $postMedia = array();
            $postMedia['access_token'] = $json->access_token;
            $postMedia['type'] = 'image';
            foreach ($imgs as $img) {
                $postMedia['media'] = $_SERVER['DOCUMENT_ROOT'] . str_replace('http://' . $_SERVER['HTTP_HOST'], '', $img['pic']);
                $rt = $this->curlPost('http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $postMedia['access_token'] . '&type=' . $postMedia['type'], array('media' => '@' . $postMedia['media']));
                if ($rt['rt'] == false) {
                    $this->error('操作失败,curl_error:' . $rt['errorno']);
                } else {
                    $mediaids .= $comma . $rt['media_id'];
                    $comma = ',';
                }
            }
        } else {
            $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
        }
        $this->success('图片素材上传完毕，现在开始发送信息', U('Message/tosendAll', array('imgids' => $imgids, 'title' => $title, 'groupid' => $groupid, 'mediaids' => $mediaids)));
    }

    public function tosendAll() {
        // if (IS_POST){
        $row = array();
        $row['msgtype'] = 'news';
        $row['title'] = $_REQUEST['title'];
        $row['groupid'] = $_REQUEST['groupid'];

        $row['imgids'] = array();
        foreach ($_REQUEST['imgids'] as $val) {
            if ($val) {
                array_push($row['imgids'], $val);
            }
        }
        $row['token'] = $this->token;
        $row['time'] = time();
        $mediaids = explode(',', $_GET['mediaids']);
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
        $json = json_decode($this->curlGet($url_get));
        if (!$json->errmsg) {
            $postMedia = array();
            $postMedia['access_token'] = $json->access_token;
            // $imgidsArr = explode(',', $row['imgids']);
            // $imgids = array();
            // $imgID = 0;
            // if ($imgidsArr){
            //     foreach ($imgidsArr as $ii){
            //         if (intval($ii)){
            //             array_push($imgids, $ii);
            //         }
            //     }
            // }
            $imgids = $row['imgids'];
            if (count($imgids)) {
                $imgs = M('Img')->where(array('id' => array('in', $imgids)))->select();
            }
            if ($imgs) {
                $str = '{"articles": [';
                $comma = '';
                $i = 0;
                foreach ($imgs as $img) {
                    if ($img['url']) {
                        
                    } else {
                        
                    }
                    $str .= $comma . '{"thumb_media_id":"' . $mediaids[$i] . '","author":"","title":"' . $img['title'] . '","content_source_url":"","content":"' . $img['info'] . '","digest":"' . $img['text'] . '"}';
                    $comma = ',';
                    $i++;
                }
                $str .= ']}';
            } else {
                $this->error('请选择图文消息', U('Message/index'));
            }
            $rt = $this->curlPost('https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $postMedia['access_token'], $str);
            // var_dump($rt);die;
            if ($rt['rt'] == false) {
                $this->error('操作失败,curl_error:' . $rt['errorno']);
            } else {
                $media_id = $rt['media_id'];
                $row['mediaid'] = $media_id;
            }
        } else {
            $this->error('获取access_token发生错误：错误代码' . $json->errcode . ',微信返回错误信息：' . $json->errmsg);
        }
        // var_dump($row);die;
        $row['imgids'] = implode(',', $row['imgids']);
        $id = M('Send_message')->add($row);
        $sendrt = $this->curlPost('https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $postMedia['access_token'], '{"filter":{"group_id":"' . $_REQUEST['groupid'] . '"},"mpnews":{"media_id":"' . $row['mediaid'] . '"},"msgtype":"mpnews"}');
        // var_dump($sendrt);die;
        if ($sendrt['rt'] == false) {
            $this->error('操作失败,curl_error:' . $sendrt['errorno']);
        } else {
            $msg_id = $rt['msg_id'];
            M('Send_message')->where(array('id' => $id))->save(array('msg_id' => $msg_id));
            $this->success('发送任务已经启动，群发可能会在20分钟左右完成，您可以关闭该页面了', U('Message/index', array("token" => session("token"))));
        }
        // }
    }

    public function index2() {
        $this->display();
    }

    public function img() {
        $db = M('Img');
        $where = array('token' => $this->token);
        $count = $db->where($where)->count();
        $Page = new Page($count, 5);
        $show = $Page->show();
        $list = $db->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('id DESC')->select();
        //
        $items = array();
        if ($list) {
            foreach ($list as $item) {
                array_push($items, array('id' => $item['id'], 'name' => $item['title'], 'pic' => $item['pic'], 'info' => $item['text'], 'linkcode' => C('site_url') . '/index.php?g=Wap&m=Index&a=content&token=' . $this->token . '&id=' . $item['id'], 'linkurl' => '', 'keyword' => $item['keyword']));
            }
        }
        //
        $this->assign('list', $items);
        $this->assign('page', $show);
        $this->display();
    }

    private function page($where, $db, $order) {
        import('ORG.Util.Page'); // 导入分页类
        $count = M($db)->where($where)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = M($db)->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
    }

    private function curlPost($url, $data, $showError = 1) {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);
        if ($errorno) {
            return array('rt' => false, 'errorno' => $errorno);
        } else {
            $js = json_decode($tmpInfo, 1);
            if (intval($js['errcode'] == 0)) {
                return array('rt' => true, 'errorno' => 0, 'media_id' => $js['media_id'], 'msg_id' => $js['msg_id']);
            } else {
                if ($showError) {
                    $this->error('发生了Post错误：错误代码' . $js['errcode'] . ',微信返回错误信息：' . $js['errmsg']);
                }
            }
        }
    }

    public function curlGet($url, $method = 'get', $data = '') {
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

}
