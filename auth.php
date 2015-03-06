<?php
$appid = 'wxe90005aeb957b4f6';
$appsecret = 'd0c3f2b74bf64a3c70bc3fabc1588c9c';
$redirectUrl = urlencode ("http://wx.drjou.cc/auth.php");
$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&$redirectUrl=REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=sentian#wechat_redirect";
$code = trim($_GET["code"]);
$state = trim($_GET['state']);
if ($code && $state == 'sentian') {
    echo $code.' aaaaaaa';
}else{
    header("Location: $url");
}


