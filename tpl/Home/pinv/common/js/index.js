$(document).ready(function(){
		//导航特效
		
		$(".listBox li").mouseover(function(){
			
				$(".over").removeClass("over");
				$(this).addClass("over")
			})
		
		//图片幻灯 
		var index = 0;
		
		$("#imgBj").addClass("imgBj0");
		
		$("#operate span:first").addClass("hov");
	
		$("#operate span").mouseover(function(){
	
		  index  =  $("#operate span").index(this);
	
		  showImg(index);
		
		});	

		$("#showimg").hover(function(){
			//alert(1)
	
				if(MyTime){
					
				   clearInterval(MyTime);
	
				}
	
		},function(){
	
				MyTime = setInterval(function(){
	
				  showImg(index)
	
				  index++;
	
				  if(index==6){index=0;}
	
				} ,4000);

		});

	

		var MyTime = setInterval(function(){
	
		  showImg(index)
	
		  index++;
	
		  if(index==6){index=0;}
	
		}, 4000);
		
		//左侧菜单悬浮
		$(".cotetLeft li").parent()
		$(".cotetLeft li").mouseover(function(){
				$(".overX").removeClass("overX");
				$(this).addClass("overX");
				$(".caseBox").children().eq($(this).index()).show().siblings().hide();
			
			})
		
		//案例分享图内部变换
		
		//$(".showList li").eq(0).find("img").addClass("liang");
		$(".showList li").mouseover(function(){
				$(this).parent().find("li").find(".liang").removeClass("liang");
				
				$(this).find("img").addClass("liang");
				
				$(this).parent().next().children().eq($(this).index()).show().siblings().hide();

			})
		

})

	function showImg(i){
	
		$("#showimg img").eq(i).stop(true,true).fadeIn(500).parent().siblings().find("img").hide();
		//$("#msg li").eq(i).stop(true,true).fadeIn(500).siblings().hide();
	
		$("#operate span") .eq(i).addClass("hov").siblings().removeClass("hov");
	
	}

