$(document).ready(function() {

	/* 模塊變色 */
	$(".pv-modules-list li").hover ( 
		function() {
			$(this).find(".module-list-desc").css("color", "#FFF");
		},
		function() {
			$(this).find(".module-list-desc").css("color", "#666");
		}
	);
	
	/* Scroll adjustment */
	var scrollOver = false;
	$( window ).scroll(function() {
		if ( $(window).scrollTop() > $("header").height())
		{
			if (!scrollOver) {
				$("#header-wrapper").css("height", "72px");
				$("#nav-menu").css("position", "fixed").css("top","-1px");
				scrollOver = true;
			}
		}
		else
		{
			if (scrollOver) {
				$("#wrapper").css("height", "auto");
				$("#nav-menu").css("position", "relative").css("top", "0px");
				scrollOver = false;
			}
		}
	});	
	
	var box_height = $(".box").height();
	var new_height = box_height - 70;
	$(".box").css("min-height", new_height+"px");
	
		//左侧菜单特效
		$(".leftList li ul li").hover(function(){
			   
				$(this).find("a").css("color","#ea533f");
			
			},function(){
				$(this).find("a").css("color","#777");
				
		});
				
		$(".my p").hover(function(){
			
				$(this).addClass("over");
			},function(){
				$(this).removeClass("over");
				});
		
		var markGet = $("#markGet").html();
		$("li").has("ul").find("ul").hide();
		$("li").has("ul").find("ul#mark"+markGet).show();
		 
		$(".my p").toggle(function(){
			$(this).parent().find("ul").slideDown();
			$(this).find("img").attr("src",SITEURL+"/img/trangX.gif")
			
			},function(){
				$(this).parent().find("ul").slideUp();
				$(this).find("img").attr("src",SITEURL+"/img/trang.gif")
				});
	
		
	
				
		/*$(".my p").eq(0).toggle(function(){
			$(this).parent().find("ul").slideDown();
			$(this).find("img").attr("src",SITEURL+"/img/trangX.gif")
			
			},function(){
				$(this).parent().find("ul").slideUp();
				$(this).find("img").attr("src",SITEURL+"/img/trang.gif")
				})
		*/
		//内容展示
		$(".serve li").hover(function(){
				$(this).find("a").children().eq(0).animate({"top":"-"+76+"px"},300);
				$(this).find("a").children().eq(1).animate({"top":"0px"},300)
			
			},function(){
				$(this).find("a").children().eq(0).animate({"top":"0px"},300);
				$(this).find("a").children().eq(1).animate({"top":"76px"},300)
				})
		
		//问号内容显示
		$(".question1").hover(function(){
			$(this).next().show();
			},function(){
				$(this).next().hide();
				})
		$(".question2").hover(function(){
			$(this).next().show();
			},function(){
				$(this).next().hide();
				})
		$(".smallImg").hover(function(){
			$(this).next().show();
			},function(){
				$(this).next().hide();
				});
				
		//鼠标悬浮手机背景变化
		$(".showBox li").hover(function(){
			$(this).addClass("mover");
		},function(){
			$(this).removeClass("mover");
			})


		
		$(".list li").eq(0).addClass("clickOver");		
		$(".list li").click(function(){
				$(this).addClass("clickOver").siblings().removeClass("clickOver");
				$(".temp").children().eq($(this).index()).show().siblings().hide();
			
			})
			
		//相册悬浮效果
		$(".imgBox li").hover(function(){
				$(this).find(".editBox").show();
			
			},function(){
				$(this).find(".editBox").hide();
				
				})
		//团购配置tab切换
	/*	$(".listBox li").click(function(){
				$(this).addClass("onOver").siblings().removeClass("onOver");
				$(".showBox").children().eq($(this).index()).show().siblings().hide();
			})
		*/
		//表情显示
		$(".faceShow").hide();
		$(".faceBox").hover(function(){
				$(".faceShow").show();
			
			},function(){
				$(".faceShow").hide();
				})
				
		$(".faceShow").hover(function(){
				$(this).show()
			
			},function(){
				
				$(this).hide();
				})
				//360全景
			/* hover tooltips */
	$(".hover-tooltip-usd").tooltip({
		title: '消费记录',
		placement:'bottom',
		trigger: 'hover'
	});
	
	$(".hover-tooltip-pencil").tooltip({
		title: '签到记录',
		placement:'bottom',
		trigger: 'hover'
	});
	
	$(".hover-tooltip-edit").tooltip({
		title: '编辑',
		placement:'bottom',
		trigger: 'hover'
	});
	
	$(".hover-tooltip-trash").tooltip({
		title: '删除',
		placement:'bottom',
		trigger: 'hover'
	});
	
	$(".hover-tooltip-stats").tooltip({
		title: '统计',
		placement:'bottom',
		trigger: 'hover'
	});

	$(".hover-tooltip-order").tooltip({
		title: '订单管理',
		placement:'bottom',
		trigger: 'hover'
	});
	
	$(".hover-tooltip-wrench").tooltip({
		title: '房间设置',
		placement:'bottom',
		trigger: 'hover'
	});

	$(".hover-tooltip-stop").tooltip({
		title: '停用',
		placement:'bottom',
		trigger: 'hover'
	});

	$(".hover-tooltip-ok").tooltip({
		title: '启用',
		placement:'bottom',
		trigger: 'hover'
	});

	/* hover popover init */
	$(".hover-popover").popover();
		
});
	
function jsbq(srt){
		document.getElementById("Hfcontent").value=document.getElementById("Hfcontent").value+"/"+srt;
		$(".faceShow").hide();
	}