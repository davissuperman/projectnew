<?php

class CountmaskAction extends SjzAction {

    public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
//        if (!strpos($agent, "MicroMessenger") && !isset($_GET['show'])) {
//            echo '此功能只能在微信浏览器中使用';
//            exit;
//        }
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
        $noncestr = "Wm3WZYTPz0wzccnW";
        $timestamp = time();
//        $timestamp = '1414587457';
//        $ticket = 'sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qg';
//        $url = "http://". $this->_server('HTTP_HOST');
        $url = $this->get_url();;
//        $url = 'http://mp.weixin.qq.com';
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;

        $signature = sha1($str);
        $this->assign("appid",$apidata['appid']);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$noncestr);
        $this->assign("signature",$signature);
        $this->assign("url",$url);

        $this->assign('gid', $gid);
        $titleArr = $this->titleInWeixin;


        $this->assign('title','shu mian mo');
        $this->assign('bonusdesc','shu mian mo');
        $this->assign("imageUrl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/sjz/images/logo1.jpg');
        $this->assign("shareimageurl","http://".$this->_server('HTTP_HOST').'/tpl/Wap/default/common/sjz/images/logo1.jpg');

        $this->display();
    }

}
