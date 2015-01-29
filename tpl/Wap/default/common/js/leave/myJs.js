$(document).ready(function () { 
			$("#showcard1").click(function () { 
				var btn = $(this);
				var wxname = $("#wxname1").val();
				if (wxname  == '') {
					alert("请输入昵称");
					return;
				}
				var info = $("#info1").val();
					if (info == '') {
					alert("请输入内容");
					return;
				}
				var token = $( '#token' ).val();
				if( token == '' ){
						alert( 'Token error!' );
				}
				var submitData = {
					nickname:wxname,
					info: info,
					action: token
				};
				$.post('/index.php?g=Wap&m=Leave&a=oT', submitData,
					function(data) {
					if (data == 1) {
							alert('留言成功');
							setTimeout('window.location.href=location.href',1000);
						return;
					} else { alert( '留言失败' ); }
				},
				"json")
			}); 
			//
			$("#showcard2").click(function () { 
				var btn = $(this);
				var wxname = $("#wxname2").val();
					if (wxname  == '') {
					alert("请输入昵称");
					return;
				}
				var info = $("#info2").val();
					if (info == '') {
					alert("请输入内容");
					return;
				}
				var token = $( '#token' ).val();
				if( token == '' ){
						alert( 'Token error!' );
				}
				var submitData = {
					nickname:wxname,
					info: info,
					action: token
				};
				$.post('/index.php?g=Wap&m=Leave&a=oT', submitData,
					function(data) {
						if (data == 1) {
						alert( '留言成功' );
						setTimeout('window.location.href=location.href',1000);
						return;
					} else { alert( '留言失败' ); }
				},
				"json")
			});  
			//
			$(".hhsubmit").click(function () { 
				var objid = $(this).attr("date");
				var info = $(".hly"+objid).val();
					if (info == '') {
					alert("请输入内容");
					return;
				}
				var token = $( '#token' ).val();
				if( token == '' ){
						alert( 'Token error!' );
				}
				var submitData = {
					fid:objid,
					info: info,
					action: token
				};
				$.post('/index.php?g=Wap&m=Leave&a=wT', submitData,
				function(data) {
					if (data == 1) {
						alert('回复成功');
						setTimeout('window.location.href=location.href',1000);
						return;
				} else { alert( '回复失败' ); }
				},
				"json")
			});  
			//
			$(".hfinfo").click(function () { 
				var objid = $(this).attr("date");
				$(".hhly"+objid).slideToggle();
			}); 
			//
			$(".hhbt").click(function () { 
				var objid = $(this).attr("date");
				$(".hhly"+objid).slideToggle();
			});
			//
			$("#windowclosebutton").click(function () { 
				$("#windowcenter").slideUp(500);
			});
			//
			$("#alertclose").click(function () { 
				$("#windowcenter").slideUp(500);
			});
		}); 
		//
		function alert(title){ 
			window.scrollTo(0, -1);
			$("#windowcenter").slideToggle("slow"); 
			$("#txt").html(title);
			setTimeout(function(){$("#windowcenter").slideUp(500);},4000);
		}
		//
		$(document).ready(function(){
			$(".first1").click(function(){
				$(".ly1").slideToggle();
			});
			$(".first2").click(function(){
				$(".ly2").slideToggle();
			});
		});

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        window.shareData = {  
            "imgUrl": "http://www.weimob.com/templates/kindeditor/attached/30/7e/9c/image/20130927/20130927164819_12005.png", 
            "timeLineLink": "http://www.weimob.com/Webmessage/Comment?wechatid=K5AOI95BHUT7G-UPPY-K&wxid=48412de755f813d5f40c58a617c93679",
            "sendFriendLink": "http://www.weimob.com/Webmessage/Comment?wechatid=K5AOI95BHUT7G-UPPY-K&wxid=48412de755f813d5f40c58a617c93679",
            "weiboLink": "http://www.weimob.com/Webmessage/Comment?wechatid=K5AOI95BHUT7G-UPPY-K&wxid=48412de755f813d5f40c58a617c93679",
            "tTitle": "留言板",
            "tContent": "留言板",
            "fTitle": "留言板",
            "fContent": "留言板",
            "wContent": "留言板" 
        };
        // 发送给好友
        WeixinJSBridge.on('menu:share:appmessage', function (argv) {
            WeixinJSBridge.invoke('sendAppMessage', { 
                "img_url": window.shareData.imgUrl,
                "img_width": "640",
                "img_height": "640",
                "link": window.shareData.sendFriendLink,
                "desc": window.shareData.fContent,
                "title": window.shareData.fTitle
            }, function (res) {
                _report('send_msg', res.err_msg);
            })
        });

        // 分享到朋友圈
        WeixinJSBridge.on('menu:share:timeline', function (argv) {
            WeixinJSBridge.invoke('shareTimeline', {
                "img_url": window.shareData.imgUrl,
                "img_width": "640",
                "img_height": "640",
                "link": window.shareData.timeLineLink,
                "desc": window.shareData.tContent,
                "title": window.shareData.tTitle
            }, function (res) {
                _report('timeline', res.err_msg);
            });
        });

        // 分享到微博
        WeixinJSBridge.on('menu:share:weibo', function (argv) {
            WeixinJSBridge.invoke('shareWeibo', {
                "content": window.shareData.wContent,
                "url": window.shareData.weiboLink,
            }, function (res) {
                _report('weibo', res.err_msg);
            });
        });
        }, false)