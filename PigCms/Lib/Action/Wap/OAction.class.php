<?php

/**
 * 
 */
class OAction extends Action {

    private $grouponurl = "http://wx.drjou.cc/index.php?g=Wap&m=Groupon&a=grouponIndex&token=rggfsk1394161441&wecha_id="; //团购地址
    private $shopurl = "http://wx.drjou.cc/index.php?g=Wap&m=Store&a=index&token=rggfsk1394161441&wecha_id="; //商城地址
    private $orderurl = "http://wx.drjou.cc/index.php?g=Wap&m=Store&a=my&token=rggfsk1394161441&wecha_id="; //订单
    private $cardurl = "http://wx.drjou.cc/index.php?g=Wap&m=Kq&a=index&token=rggfsk1394161441&wecha_id="; //卡
    private $code = "http://wx.drjou.cc/index.php?g=Wap&m=Code&a=index&token=rggfsk1394161441"; //二维码
    private $t = "";
    private $url = "";
    /**
     * 检查是否有cokie
     */
    public function __construct() {
        $userinfo = cookie('user_openid');
        $type = trim($_GET['t']);
        $cid = trim($_GET['id']);
        if (!empty($_GET['url'])) {
            $this->url = trim($_GET['url']);
        }
        $this->t = $type;
        $this->code = $this->code . '&id=' . $cid . '&wecha_id=';
        if ($userinfo) {
            $this->redirect_url($userinfo);
            exit;
        }
    }

    /**
     * 如果没有cookie 就提示授权
     */
    public function index() {
        $data = M('Diymen_set')->where(array('token' => 'rggfsk1394161441'))->find(); //这token 写死了
        $code = trim($_GET["code"]);
        $state = trim($_GET['state']);
        if ($code && $state == 'npic') {
            $userinfo = $this->getUserInfo($code, $data['appid'], $data['appsecret']);
            cookie('user_openid', $userinfo['openid'], 315360000);
            $this->redirect_url($userinfo['openid']);
           exit;
        } else {
            $id = $_GET['id'];
            if ($this->t != 'code') {
                $url = urlencode("http://wx.drjou.cc/index.php?g=Wap&m=O&a=index&t=$this->t");
            } else {
                $url = urlencode("http://wx.drjou.cc/index.php?g=Wap&m=O&a=index&t=$this->t&id=$id");
            }
            header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $data['appid'] . "&redirect_uri=$url&response_type=code&scope=snsapi_base&state=npic#wechat_redirect");
            exit;
        }
        exit;
    }

    /**
     * 页面跳转
     * @param type $openid
     */
    private function redirect_url($openid = "") {
        if ($this->t == 'shop') {
            if ($this->url) {
                $this->shopurl = $this->url . '&wecha_id=' . $openid;
            } else {
                $this->shopurl = $this->shopurl . $openid;
            }
            header("location:$this->shopurl");
            exit;
        }
        if ($this->t == 'groupon') {
            $this->grouponurl = $this->grouponurl . $openid;
            header("location:$this->grouponurl");
            exit;
        }
        if ($this->t == 'order') {
            $this->orderurl = $this->orderurl . $openid;
            header("location:$this->orderurl");
            exit;
        }
        if ($this->t == 'card') {
            $this->cardurl = $this->cardurl . $openid;
            header("location:$this->cardurl");
            exit;
        }
        if ($this->t == 'code') {
            $this->code = $this->code . $openid;
            header("location:$this->code");
            exit;
        }
    }

    /**
     * 获得openid
     * @param type $code
     * @param type $appid
     * @param type $appsecret
     * @return type
     */
    private function getUserInfo($code, $appid, $appsecret) {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);
        return $access_token_array;
    }

    private function https_request($url, $method = 'get', $data = '') {
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
