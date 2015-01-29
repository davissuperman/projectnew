var _vote = {
	_n: 3,k:3,
    _add: function() {
        if (this._n >= 15) {
            _system._toast("最多只能添加15项");
            return;
        }
        this._n++;
        var _input = document.createElement("input"), _div = document.createElement("p");
        _input.type = "text";
        _input.id = "vote_option_" + this._n;
		_input.name = "option_" + this._n;
        _input.maxlength = "50";
        _input.placeholder = "自定义投票选项";
        _input.className = "circle";
        _div.className = "same";
        _div.appendChild(_input);
        $l("vote_options").insertBefore(_div, $l("vote_add"));
    },
	_logoadd: function() {
        if (this._k >= 15) {
            _system._toast("最多只能添加15项");
            return;
        }
        this._k++;
        var _input = document.createElement("input"), _div = document.createElement("p"),_a = document.createElement("a"),_atext = document.createTextNode("上传");
        _input.type = "text";
        _input.id = "url_" + this._k;
		_input.name = "logourl_" + this._k;
        _input.maxlength = "50";
        _input.className = "circle";
		_a.className = "a_upload";
		_a.setAttribute("onclick","upyunPicUpload($(this).prev().attr(\'id\'),\'pic\',700,420,\'qtclsw1382672973\')");
		_a.href = "###";
		_a.appendChild(_atext);
        _div.className = "same";
        _div.appendChild(_input);
		_div.appendChild(_a);
        $l("vote_logo").insertBefore(_div, $l("vote_logoadd"));
		$l("url").insertBefore(_a, $l("vote_logoadd"));
    },
	_p:function(){
		var _options = "";
		var _logos = "";
        for (var i = 1; i <= this._n; i++) {
            var _option = $l("vote_option_" + i).value.trim().replace(/\^/, "");
            if (_option != "") {
                _options += _option + "^";
            }
        }
		for (var i = 1; i <= this._n; i++) {
            var _logo = $l("url_" + i).value.trim().replace(/\^/, "");
            if (_logo != "") {
                _logos += _logo + "^";
            }
        }
		$l( "hid" ).value = _options;
		$l( "lhi" ).value = _logos;
	}
};