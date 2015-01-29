$( function() {
	$(":text").focus( function() {
		$(this).attr("value", "");
	});

	$("#inputPassword").hide();
	$(".tel").hide();
	$(".look").click( function() {
		$(".show3").hide();
		$(".out").show();
		$(".showBox").show();
		$(".show1").show();
	});
	$(".cancel").click( function() {
		$(".out").hide();
	});
	$(".apply").click( function() {
		$(".show1").hide();
		$(".out").show();
		$(".showBox").show();
		$(".show3").show();
	});
	$("#inputPwdSubmit").click( function() {//输入密码
				$.ajax( {
					cache : true,
					type : "POST",
					url : 'index.php?&g=Wap&m=Activity&a=viewajax',
					data : $('#inputPassword').serialize(),
					async : false,
					error : function(request) {
						alert("error");
					},
					success : function(data) {
						if (data != 0) {
							$(".tel").show();
							$(".out").hide();
							$(".showBox").hide();
							$(".show1").hide();
						} else {
							$(".out").show();
							$(".show2").html('密码不对!')
							$(".show2").show().delay(1000).hide(1);

						}

					}
				});
			});
	$("#inputNameSubmit").click( function() {//报名

				var username = $("#username").val();
				if ($.trim(username) == "" || $.trim(username) == '输入你的名字') {
					$(".out").show();
					$(".show2").html('输入你的名字!!')
					$(".show2").show().delay(1000).hide(1);
					return false;
				}
				var tel = $("#tel").val();
				if ($.trim(tel) == "" || $.trim(tel) == '手机号，别轻易向别人提'||(!tel.match(/^1[3|4|5|8][0-9]\d{4,8}$/))) {
					$(".out").show();
					$(".show2").html('输入你的手机号!!')
					$(".show2").show().delay(1000).hide(1);
					return false;
				}
				$.ajax( {
					cache : true,
					type : "POST",
					url : 'index.php?&g=Wap&m=Activity&a=submitajax',
					data : $('#inputname').serialize(),
					async : false,
					success : function(data) {
						if (data != 0) {
							$(".tel").hide();
							$(".out").hide();
							$(".showBox").hide();
							$(".show3").hide();
							location.reload() ;
						}

					}
				});
			});
	
$(".send").click(function(){
		$(".out").show();
		$(".showBox").css({"filter":"alpha(opacity=80)","-moz-opacity":"0.8","opacity":"0.8"}).show();
		$(".showSpecial").show();		
	
	});
	
	
//分享到朋友圈	
$(".sendX").click(function(){
		$(".out").show();
		$(".showBox").css({"filter":"alpha(opacity=80)","-moz-opacity":"0.8","opacity":"0.8"}).show();
		$(".showSpecial").show();		
	
	});
 
})
