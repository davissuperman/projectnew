var _party = {
    _post: function() {
        var _subject = $("party_subject").value.trim();
        var _atime = $("party_atime").value.trim();
        var _aadds = $("party_aadds").value.trim();
        var _message = $("party_message").value.trim();
        var _username = $("party_username").value.trim();
        var _password = $("party_password").value.trim();
        var _style = $("party_style").value.trim();
        var d = new Date(Date.parse(_atime.replace(/-/g, "/")));
        var curDate = new Date();
        if (_subject == "") {
            _system._toast("没有输入活动标题");
            return;
        }
        if (_subject.len() > 70) {
            _system._toast("标题请在70字节以内");
            return;
        }
        if (d <= curDate) {
            _system._toast("请选择大于现在时间的活动时间！");
            return;
        }
        if (_aadds == "") {
            _system._toast("地址输入不正确");
            return;
        }
        if (_message.len() < 10) {
            _system._toast("活动详情写得太少了");
            return;
        }
        if (_message.len() > 5000) {
            _system._toast("活动详情写得有点多了");
            return;
        }
        if (_password == "") {
            _system._toast("请设置管理密码");
            return;
        }
        if (_password.len() < 6 || _password.len() > 16) {
            _system._toast("密码请设在6-16字节");
            return;
        }       
        var formdate = new FormData();
        formdate.append("subject", _subject);
        formdate.append("atime", _atime);
        formdate.append("aadds", _aadds);
        formdate.append("message", _message);
        formdate.append("username", _username);
        formdate.append("style", _style);
        formdate.append("password", _password);
        
        var filesize = $('file').files;
        for (i = 0; i < filesize.length; i++) {
            formdate.append("file[]", $('file').files[i]);
        }
        _$("./party.php?f=post", formdate, "请稍候", "_party._ok");
    },
    _ok: function(json) {
        if (json.state == "0") {
            _system._toast("你填写的内容有问题");
            return;
        }
        if (json.state == "100") {
            _system._toast("内容包含敏感词，请修改后再发送");
            return;
        }
        localStorage.party_subject = "";
        localStorage.party_message = "";
        dataForWeixin.MsgImg = "http://www.baokulife.com/images/party_msg.png";
        dataForWeixin.TLImg = "http://www.baokulife.com/images/party.png";
        dataForWeixin.path = "./party.php?a=index&f=view&acid=" + json.acid;
        dataForWeixin.title = $("party_subject").value.trim().htmlencode();
        dataForWeixin.desc = $("party_message").value.trim().left(88).htmlencode();
        dataForWeixin.callback = function() {
            _$("./share.php?f=party&id=" + json.acid, "", "");
        };
        _system._guide();
    }

};
//更改皮肤-显示上传图片
var _load = {
    _select: function() {
        _style = $("party_style").value;
        _themes = $("partythemes");
        _themes.setAttribute("href", './static/weixin/css/' + _style + '/active.css');
    },
    _fileselect: function(evt) {//选择图片显示到页面
        var files = evt.target.files;
        for (var i = 0, f; f = files[i]; i++) {
            if (!f.type.match('image.*')) {
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var span = document.createElement('span');
                    span.innerHTML = ['<img  class="user" src="', e.target.result,
                        '" title="', escape(theFile.name), '" />'].join('');
                    $('imglist').insertBefore(span, null);
                };
            })(f);
            reader.readAsDataURL(f);
        }
    }

};
//分享给朋友
(function() {
    $('file').addEventListener('change', _load._fileselect, false);//上传图选图事件
    $('party_style').addEventListener('change', _load._select, false);//选择样式    
    jq('#party_atime').scroller({preset: 'datetime', minDate: new Date(), maxDate: new Date(2020, 7, 30, 15, 44), stepMinute: 5, theme: 'default', mode: 'mode', display: 'top', lang: 'zh', dateFormat: 'yy-mm-dd'});//日期时间选择事件
    dataForWeixin.path = "./party.php";
    dataForWeixin.title = "宝酷助手在微信组织活动";
    dataForWeixin.desc = "K歌、聚餐、户外、会议，轻松发起各类活动。随时查看报名信息、精确统计报名人数";
    $("party_subject").value = localStorage.party_subject || "";
    $("party_message").value = localStorage.party_message || "";
})();
