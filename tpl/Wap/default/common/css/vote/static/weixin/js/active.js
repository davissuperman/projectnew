var $=function(id){return document.getElementById(id);};
		var color=function(ss){
			ss.style.color="#000";
			
			};
		$("_active_title").onfocus=function(){
			color(this);
		};
		$("_active_content").onfocus=function(){
			color(this);
		};
		$("_active_address").onfocus=function(){
			color(this);
		};
		$("_active_passWord").onfocus=function(){
			color(this);
		};
		
		var _active={
					_post:function(){
						 var _title=$("_active_title").value,
							 _content=$("_active_content").value,
							 _address=("_active_address").value,
							 _passWord=("_active_passWord").value;
					  	 if(_title=="")
						 {
							 _system._toast("没有输入活动标题");
							 return;
						 }
					  	 if(_title.len()>70)
						 {
							 _system._toast("标题请在70字节以内");
							 return;
						 }
						 if(_address="")
						 {
							 _system._toast("还没有输入活动地址");
							 return;
						 }
					  	 if(_content.len()<10)
						 {
							 _system._toast("活动详情写的太少了");
							 return;
						 }
					  	 if(_content.len()>5000)
						 {
							 _system._toast("活动详情写得有点多了");
							 return;
						 }
						 if(_passWord="")
						 {
							 _system._toast("还没有设置管理密码");
							 return;
						 }
					}
			
				  };
		