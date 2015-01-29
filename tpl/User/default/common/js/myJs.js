$(function(){
	
		//左侧菜单特效
		$(".leftList li ul li").hover(function(){
			
				$(this).find("a").css("color","#ea533f");
			
			},function(){
				$(this).find("a").css("color","#777");
				
				});
				
		$(".leftList p").hover(function(){
				$(this).addClass("over");
			},function(){
				$(this).removeClass("over");
				});
		
		$("li").has("ul").find("ul").hide();
		$("li").has("ul").eq(0).find("ul").show();
		$(".leftList p").toggle(function(){
			$(this).parent().find("ul").slideDown();
			$(this).find("img").attr("src","common/img/trangX.gif")
			
			},function(){
				$(this).parent().find("ul").slideUp();
				$(this).find("img").attr("src","common/img/trang.gif")
				});
				
		$(".leftList p").eq(0).toggle(function(){
			$(this).parent().find("ul").slideDown();
			$(this).find("img").attr("src","common/img/trangX.gif")
			
			},function(){
				$(this).parent().find("ul").slideUp();
				$(this).find("img").attr("src","common/img/trang.gif")
				})
		
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
		
		$(".showBox li img").not($(".showBox li img").eq(0)).hover(function(){
				var mm=$(this).attr("alt");
				$(this).attr("src","common/img/"+mm+"Over.gif");
			
			},function(){
				var mm=$(this).attr("alt");
				$(this).attr("src","common/img/"+mm+".gif");
				})
		
		
		$(".list li").eq(0).addClass("clickOver");		
		$(".list li").click(function(){
				$(this).addClass("clickOver").siblings().removeClass("clickOver");
				$(".showBox").children().eq($(this).index()).show().siblings().hide();
			
			})
			
		//相册悬浮效果
		$(".imgBox li").hover(function(){
				$(this).find(".editBox").show();
			
			},function(){
				$(this).find(".editBox").hide();
				
				})
		//团购配置tab切换
		$(".listBox li").click(function(){
				$(this).addClass("onOver").siblings().removeClass("onOver");
				$(".showBox").children().eq($(this).index()).show().siblings().hide();
			})
		
		
	})