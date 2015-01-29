var _vote = {
    _n: 3, _kind: 1,
    _select: function(value) {
        if (value == 0) {
            $("vote_kind_1").className = "choicBtn btnOver";
            $("vote_kind_2").className = "choicBtn";
            this._kind = 0;
        } else {
            $("vote_kind_1").className = "choicBtn";
            $("vote_kind_2").className = "choicBtn btnOver";
            this._kind = 1;
        }
    },
    _add: function() {
        if (this._n >= 15) {
            _system._toast("最多只能添加15项");
            return;
        }
        this._n++;
        var _input = document.createElement("input"), _div = document.createElement("p");
        _input.type = "text";
        _input.id = "vote_option_" + this._n;
        _input.maxlength = "50";
        _input.placeholder = "自定义投票选项";
        _input.className = "case";
        _div.className = "sameP";
        _div.appendChild(_input);
        $("vote_options").insertBefore(_div, $("vote_add"));
    },
    _checkOptionsLen: function() {
        for (var i = 1; i <= this._n; i++) {
            if ($("vote_option_" + i).value.trim().len() > 50) {
                return i;
                break;
            }
        }
        return 0;
    },
    _post: function() {
        var _title = $("vote_title").value.trim(), _content = $("vote_content").value, _style = $("vote_style").value,_time=$( 'vote_time' ).value,_address=$("vote_address").value,_token=$("token").value;
	   if (_title == "") {
            _system._toast("没有输入投票标题");
            return;
        }
        if (_title.len() > 70) {
            _system._toast("标题请在70字节以内");
            return;
        }
        if (_content.len() > 1500) {
            _system._toast("补充说明写得有点多了");
            return;
        }
        var _n = this._checkOptionsLen();
        if (_n > 0) {
            _system._toast("第" + _n + "选项写多了，请在50字节内");
            return;
        }
        var _options = "";
        for (var i = 1; i <= this._n; i++) {
            var _option = $("vote_option_" + i).value.trim().replace(/\^/, "");
            if (_option != "") {
                _options += _option + "^";
            }
        }
        if (_options.match(/(.*\^.*){2,}/) == null) {
            _system._toast("至少需要输入2个投票选项");
            return;
        }

        if (_system._forbidden(_title) || _system._forbidden(_content)) {
            _system._toast("请不要涉及敏感政治话题");
            return;
        }
      
        var formdate = new FormData();
        formdate.append("title", _title);
        formdate.append("content", _content);
        formdate.append("kind", this._kind);
        formdate.append("options", _options);
        formdate.append("style", _style);
		formdate.append("time",_time);
		formdate.append("address",_address);
		formdate.append("token",_token);
        var filesize = $('file').files;
        for (i = 0; i < filesize.length; i++) {
            formdate.append("file[]", $('file').files[i]);
        }
        _$("/newP/index.php?g=Wap&m=Vote&a=VoteAjax", formdate, "请稍候", "_vote._ok");
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
        localStorage.vote_title = "";
        localStorage.vote_content = "";
        dataForWeixin.MsgImg = "http://www.baokulife.com/images/vote_msg.png";
        dataForWeixin.TLImg = "http://www.baokulife.com/images/vote.png";
        dataForWeixin.path = "./newP/index.php?g=Wap&m=Vote&a=commentList&id=" + json.vid + "&token=" + json.token;
        dataForWeixin.title = $("vote_title").value.trim().htmlencode();
        dataForWeixin.desc = $("vote_content").value.trim().left(88).htmlencode();
        dataForWeixin.desc = dataForWeixin.desc || dataForWeixin.title;
        dataForWeixin.callback = function() {
            _$("./newP/index.php?g=Wap&m=Vote&a=commentList&id=" + json.vid+"&token=" + json.token, "", "");
        };
        _system._guide();
    }
};
var _load = {
    _select: function() {
        _style = $("vote_style").value;
        _themes = $("votethemes");
        _themes.setAttribute("href", './tpl/Wap/default/common/css/vote/static/' + _style + '/vote.css');
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
(function() {
    $('file').addEventListener('change', _load._fileselect, false);//上传图选图事件
    $('vote_style').addEventListener('change', _load._select, false);//选择样式    
    dataForWeixin.path = "./vote.php";
    dataForWeixin.title = "宝酷助手在微信发起投票";
    dataForWeixin.desc = "快速在微信群、朋友圈发起投票、调查、测试。直观展现投票结果，轻松掌握投票数据";
    $("vote_title").value = localStorage.vote_title || "";
    $("vote_content").value = localStorage.vote_content || "";

})();
