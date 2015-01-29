<?php

class ServiceAction extends CommonAction {

    public function _initialize() {
        parent :: _initialize();
    }

    /**
     * 点击粉丝 返回聊天记录 和这个人的信息
     */
    public function return_fance_info() {
        //1.得到粉丝信息 2.得到粉丝说的话 3.修改粉丝对话状态  4. 修改客服服务人数   
        $openid = $_POST['openid'];
        $where = array('openid' => $openid, 'token' => session('token'));
        //状态修改
        M('customer_service_fans_status')->where($where)->setField('status', '3');
        //修改客服服务人数
    
        M('customer_service_users')->where(array('uid' => session('userId')))->setField('openid', $openid);
        //粉丝信息
        $fanceinfo = M('customer_service_fans')->field('nickname,city,headimgurl,gid,remark')->where($where)->find();
        $fance['fance'] = $fanceinfo;
        //聊天记录信息
        $where['uid'] = session('userId');
        $message = M('customer_service_userfans_message')->field('message,type,time')->where($where)->order('time desc')->limit('0,10')->select();
        $message = array_reverse($message);
      //  file_put_contents('d:/q.txt', var_export($message, TRUE));
        foreach ($message as $k => $v) {
            $message[$k]['time'] = date('Y-m-d H:i', $v['time']);
        }
        $fance['message'] = $message;
        $json = json_encode($fance, TRUE);
        echo $json;
        exit;
    }

    /**
     * 完成通话
     */
    public function overmessage() {
        $openid = $_POST['openid'];
        $visitors_type = $_POST['visitors_type'];
        $change = $_POST['change'];
        if (!$change) {//直接通话结束
            //1. 把客服转换为退出通话模式 
            $where = array('openid' => $openid, 'token' => session('token'));
            $valuex = array('status' => 0, 'uid' => 0);
            $isstatic = M('customer_service_fans_status')->where($where)->save($valuex);
            // 2. 修改通话结束时间 和修改 和本次通话类型           
            $infos = M('customer_service_fans_visitors')->field('vid')->where($where)->order('vid desc')->limit('0,1')->find();
            $value = array('lasttime' => time(), 'tid' => $visitors_type);
            $info = M('customer_service_fans_visitors')->where(array('vid' => $infos['vid']))->save($value);
            M('customer_service_users')->where(array('uid' => session('userId')))->setDec('services', 1);
            M('customer_service_users')->where(array('uid' => session('userId')))->save(array('openid' => ''));
            //修改客服队列


            if ($info && $isstatic) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {//转接后点击通话完成
            
            $uid = $_POST['uid']; //转给谁        
            $what = $_POST['what']; //为什么
            file_put_contents('D:/u.txt', $uid);
            //修改客户对应的客服
            $where = array('openid' => $openid, 'token' => session('token'));
            $valuex = array('uid' => $uid);
            $isstatic = M('customer_service_fans_status')->where($where)->save($valuex);
            // 2. 修改通话结束时间 和修改 和本次通话类型           
            $infos = M('customer_service_fans_visitors')->field('vid')->where($where)->order('vid desc')->limit('0,1')->find();
            $value = array('lasttime' => time(), 'tid' => $visitors_type);
            $info = M('customer_service_fans_visitors')->where(array('vid' => $infos['vid']))->save($value);
            //修改客服队列
            M('customer_service_users')->where(array('uid' => session('userId')))->setDec('services', 1);
            M('customer_service_users')->where(array('uid' => session('userId')))->save(array('openid' => ''));
            //2.记录转接记录
            $message['openid'] = $openid;
            $message['what'] = $what;
            $message['touid'] = $uid;
            $message['uid'] = session('userId');
            $message['time'] = time();
            $ischange = M('customer_service_users_change')->add($message);
            //创建通话记录
            $message['token'] = session('token');
            $message['openid'] = $openid;
            $message['starttime'] = time();
            $message['lasttime'] = time();
            $message['uid'] = $uid;
            $message['time'] = time();
            M('customer_service_fans_visitors')->add($message);
            //修改客服队列
            $users = M('customer_service_users')->where(array('uid' => $uid))->setInc('services', 1);
            if ($info) {
                echo 'success';
            } else {
                echo 'error';
            }
        }

        exit;
    }

    /**
     * 修改粉丝组 和备注
     */
    public function fanceremark() {
        $openid = $_POST['openid'];
        $inforemark = $_POST['inforemark'];
        $gid = $_POST['gid'];
        $where = array('openid' => $openid, 'token' => session('token'));
        $value = array('gid' => $gid, 'remark' => $inforemark);
        $fanceinfo = M('customer_service_fans')->where($where)->save($value);
        if ($fanceinfo) {
            echo 'success';
        } else {
            echo 'error';
        }
        exit;
    }

    /**
     * 根据openid 或电话获取订单信息 精确匹配
     * @param string      $openid 
     * @param string/int  $mobile       客户电话
     * @param json        $data  $data['status'] 状态 -1无此客户订单 0查询到符合条件的订单
     *  $data['orders'] 该用户的所有订单信息
     */
    public function getOrders() {
        $openid = trim($_POST['openid']);
        $mobile = trim($_POST['mobile']);

        $token = session('token');
        $where['token'] = $token;
        if ($openid) {
            $where['wecha_id'] = $openid;
        }
        if ($mobile) {
            $where['moble'] = $mobile; //这里不是少写一个字母，是数据库里的字段名拼错了
        }

        $data = array();
        $orders = array();
        $model = M('Product_shop');
        //订单ID 订单金额 下单时间  商品名称 id 图片

        $orders = $model->field('number,oprice,time,sc')->where($where)->order('time desc')->group('number')->select();
        if (empty($orders)) {
            $data['status'] = -1; //如果没有查出结果则返回
            echo json_encode($data);
            exit;
        }
        for ($i = 0; $i < count($orders); $i++) {
            $orders[$i]['time'] = date('Y-m-d H:m:s', $orders[$i]['time']);
            $products = M('Product_shop')->field('il')->where(array('token' => $token, 'number' => $orders[$i]['number']))->select();
            for ($j = 0; $j < count($products); $j++) {
                $products[$j] = unserialize($products[$j]['il']);
            }
            $orders[$i]['products'] = $products;
        }
        $data['status'] = 0;
        $data['orders'] = $orders;
        echo json_encode($data);
    }

    /**
     * 通过高级接口向指定用户发信息
     * @param type $openid
     * @param type $content
     */
    function send_fance_info() {
        $openid = trim($_POST['openid']);
        $content = trim($_POST['content']);
        $api = M('Diymen_set')->where(array('token' => session('token')))->find();
        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api ['appid'] . '&secret=' . $api['appsecret'];
        $json = json_decode($this->curlGet($url_get));
        $qrcode_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $json->access_token;
        $data = '{"touser":"' . $openid . '","msgtype":"text","text":{ "content":"' . $content . '"	}}';
        $post = $this->curlGet($qrcode_url, 'post', $data);
        $json_decode = json_decode($post);
        if ($json_decode->errmsg == 'ok') {
            $message['token'] = session('token');
            $message['openid'] = $openid;
            $message['message'] = $content;
            $message['uid'] = session('userId');
            $message['type'] = 2;
            $message['time'] = time();
            M('customer_service_userfans_message')->add($message);
            echo 'success';
            exit;
        } else {
            echo 'error';
            exit;
            // file_put_contents('D:/error.txt', $json_decode->errmsg);
        }
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
        $header[] = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

    /**
     *  头像
     */
    function showExternalPic() {
        $url = htmlspecialchars($_GET['url']);
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
        $types = array('gif' => 'image/gif', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'jpe' => 'image/jpeg', 'png' => 'image/png',);
        $ext = 'jpg';
        $type = $types[$ext] ? $types[$ext] : 'image/jpeg';
        header("Content-type: " . $type);
        echo $data;
    }

}
