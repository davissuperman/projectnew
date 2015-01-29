var $=function(id){return document.getElementById(id);};
		var color=function(ss){
			ss.style.color="#000";
			
			};
		$("party_title").onfocus=function(){
			color(this);
		};
		$("party_content").onfocus=function(){
			color(this);
		};
		$("text1").onfocus=function(){
			color(this);
		};
		$("text2").onfocus=function(){
			color(this);
		};
		$("faceBox").onclick=function(){
			this.style.border="1px #4eb856 solid"
			}
		var _part={
					_post:function(){
						 var _title=$("party_title").value.trim(),
							 _content=$("party_content").value;
					  	 if(_title=="")
						 {
							 _system._toast("没有输入投票标题");
							 return;
						 }
					  	 if(_title.len()>70)
						 {
							 _system._toast("标题请在70字节以内");
							 return;
						 }
					  	 if(_content.len()<10)
						 {
							 _system._toast("事件介绍写的太少了");
							 return;
						 }
					  	 if(_content.len()>5000)
						 {
							 _system._toast("事件介绍写得有点多了");
							 return;
						 }
					}
			
				  };
		