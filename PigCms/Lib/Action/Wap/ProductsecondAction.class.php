<?php

class ProductsecondAction extends BonusAction {


    public $cache;
    public $titleInWeixin = '森田药妆';
    public $url;


    public function _initialize() {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'static');
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
        $this->url= C('site_url');

       // $this->cache = Cache::getInstance('Redis',array('host'=>'127.0.0.1','expire'=>1296000));

    }

    public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }

        $openId = $_GET['openid'];
//         if (time() > $this->gameinfo['end']) {//活动是否结束
//                exit('<center>游戏已经结束！谢谢你的参与</center>');
//          }
        if($openId){

        }else{

        }
        $this->assign("siteurl",$this->url);
        $this->assign("openid",$openId);
        $this->display();
    }

    public function updatejiangping(){
        $openId = $_GET['openid'];
        $p = M('Productsecond_index')->where(array('openid' => $openId))->find();
        $return = 0;
        if($p){
            M('Productsecond_index')->where(array('id' =>  $p['id'] ))->setInc('lingjiangsum', 1);
            if(1*$p['award'] == 0){
                //更新获奖状态
                $d['id'] = $p['id'];
                $d['award'] = 1;
                M('Productsecond_index')->save($d);
            }

            $return = 1;
        }
        echo $return;
    }

    public function saveHangzhouPhone(){
        $msg = '';
        $openId = $_POST['openid'];
        $success = false;
        if($openId){
            $fansInfo = M('member_user')->where(array('openid' => $openId,'token'=>'rggfsk1394161441'))->find();
            if($fansInfo){
                //此用户已经添加 显示页面
                $phone = null;
                if(isset($_POST['phone']) && $_POST['phone']){
                    //提交手机号
                    //是否存在手机号
                    $phone = $_POST['phone'];
                    $p1 = M('Productsecond_index')->where(array('phone' => $phone))->find();
                    $p2 = M('Productsecond_index')->where(array('openid' => $openId))->find();
                    if($p1 || $p2){
                        if($p1['award'] == 1 || $p2['award'] == 1){
                            $msg = "非常抱歉，您的微信账号或登记的电话号码，已经领取过奖品。";
                        }else{
                            $success = true;
                        }

                    }else{
                        $d['openid'] = $openId;
                        $d['phone'] = $phone;
                        M("Productsecond_index")->add($d);
                        $msg = "提交成功";
                        M("Productsecond_num")->where(array('id' =>1))->setInc('number', 1);
                        $success = true;
                    }
                }else{

                }

            }else{
                $msg = "非常抱歉，您的微信账号或登记的电话号码，已经领取过奖品。";
            }
        }else{
            $msg = "非常抱歉，您的微信账号或登记的电话号码，已经领取过奖品。";
        }
        $this->assign("openid",$openId);
        $this->assign("msg",$msg);
        $this->assign("success",$success);
        $this->display();
    }

    /**
     * @return 当前网页的URL
     */
    function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }


    /**
     * 获得微信用户信息
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

    public function curlGetV2($get_user_info_url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function culrGetV3($url,$method="get",$postfields = null, $headers = array(), $debug = true){
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        curl_close($ci);
        return array($http_code, $response);
    }
}
