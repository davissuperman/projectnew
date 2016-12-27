$(document).ready(function(e) {
	goBack();
	initPageFontSize();
	closeLayer();
	changeWheelStatus();
	luckDrawClick();
	brightFlash();
});

function luckDrawClick(){
	var $luck_draw = $("#luck-draw");
	$luck_draw.bind('click',function(){
		if($luck_draw.hasClass("opacity-gray") || $luck_draw.parent().hasClass("opacity-gray")){
			return false;
		}else{
			//投票api
			//alert('投票成功');
		}
	});
}

function brightFlash(){
	var $wheel_box = $("#wheel-box");
	$wheel_box.find(".light").addClass("flash");
	$wheel_box.find(".bright").each(function(index, element) {
        var $this = $(this);
		if(index % 2 == 0){
			$this.addClass("flash");
		}else{
			$this.addClass("flash1");
		}
    });
}

function changeWheelStatus(){
	var $wheel_box = $("#wheel-box");
	var $need_ticket = $("#need-ticket");
	var $need_times = $("#need-times");
	var need_ticket = $need_ticket.html();
	var need_times = $need_times.html();
	var needNums = need_times * 5 - (5-need_ticket);
	//if(needNums < 15){
	if(need_ticket == 0){
		$wheel_box.removeClass("opacity-gray");
		changeLuckDrawStatus(needNums);
		changeBallStatus(needNums);
		//brightFlash();
	}else{
		$wheel_box.addClass("opacity-gray");
	}
}

function changeBallStatus(needNums){
	var $wheel_box = $("#wheel-box");
	var has_number = 15 - needNums;
	$wheel_box.find(".img").each(function(index){
		var $curImg = $(this);
		if(index < needNums){
			$curImg.addClass("opacity-gray");
		}else{
			$curImg.removeClass("opacity-gray");
		}
	});
}

function changeLuckDrawStatus(needNums){
	var $luck_draw = $("#luck-draw");
	var r = (15 - needNums) % 5;
	if(r > 0){
		$luck_draw.removeClass("bounceIn animated");
		$luck_draw.addClass("opacity-gray");
	}else{
		$luck_draw.removeClass("opacity-gray");
		$luck_draw.addClass("bounceIn animated");
	}
}

function hideHoverBox(){
	if($(".com-box-hover").css("display") != 'none'){
		$(".com-box-hover").hide();	
	}else{
		$(".com-box-hover").show();		
	}
	var theTimer=setTimeout (arguments.callee, 1000);
}

function playAudio(){
	var audio = document.getElementById('audio'); 
 	audio.play();
}

function countdown ($elem,time){
	var obj = $elem;
	this.doTiming = function (){
		var st;
		if (time<=0){
			clearTimeout(st);
		} else {
			
			time--;
			st=setTimeout (arguments.callee, 1000);
			obj.html(time);
			if(time == 2){
				$("#time-page1").hide();
				$("#time-page2").show();
				$("#time-page3").hide();
			}else if(time == 0){
				//gameover();
				$("#time-page1").hide();
				$("#time-page2").hide();
				$("#time-page3").show();
			}
		}
	}
}

function urlPara (v){
	var url = window.location.search;
	if (url.indexOf(v) != -1){
		var start = url.indexOf(v)+v.length;
		var end = url.indexOf('&',start) == -1 ? url.length : url.indexOf('&',start);
		return url.substring(start,end);
	} else { return '';}
}

$(window).resize(function(){
	initPageFontSize();
});
function initPageFontSize(){
	$("html").css({"font-size":(100*$(window).width()) / 640});
	var wh = $(window).height(),dh = $("#contents").height();
	var dh = $(document).height();

	$("#body").height(wh);
	$(".upload-panel").height(dh);
}
//模拟alert
function mnAlert (mes){
	var htmlStr = "<div class=\"layerBox\">";
		htmlStr += "<div class=\"layerBg\">";
		htmlStr += "<div class=\"layerMsg\">"+mes+"</div>";
		htmlStr += "<button class=\"layerBtn\" onClick=\"javascript:$(this).closest('.layerBox').remove();\">我知道了</button>";
		htmlStr += "</div></div>";
	$("body").append(htmlStr);
}
function checkPhone(mb){
	var mobile = mb;
	if(mobile != '' && mobile.length == 11){
		var reg = /^((\(\d{3}\))|(\d{3}\-))?13\d{9}|14[57]\d{8}|15\d{9}|18\d{9}|176\d{8}|177\d{8}|178\d{8}|1700\d{7}|1705\d{7}|1709\d{7}$/;  
		return reg.test(mobile);
	}
	return false;
}


/*close layer*/
function closeLayer(){
	var $layerbox = $("#layer-box"),$btn_close = $layerbox.find(".btn-close");
	$btn_close.bind('click',function(){
		$layerbox.hide();
		//var wini = document.documentElement.clientWidth+"px";
		//$layerbox.animate({right:'-'+wini}, 'fast', '', function(){$layerbox.hide();});
		
	});
	$layerbox.bind('click',function(){
		$layerbox.hide();
	});
}

function showLayer($elem){
	var $layerbox = $("#layer-box");
	$layerbox.show();
	$elem.show().siblings().hide();
}

function goBack(){
	$("#go-back").bind('click',function(){
		window.history.back();
	});
}

var Pop=function(){
    var oBg=document.createElement('div');
    var oDiv=document.createElement('div');
    oBg.className='pop-bg';
    oDiv.className="pop-box";
    var createDiv = function(text,btn,fn){
        var text = text || '';
            var windowObj = window.parent || window;
            var htmlChild =windowObj.document.getElementsByTagName('html')[0];
            var html = '<div class="head">消息</div>';
            if(fn){
            	html += '<div class="pop-text confirms-text"><div class="text">'+text+'</div>'+btn+'</div>';
            }else{
            	html += '<div class="pop-text alerts-text"><div class="text">'+text+'</div>'+btn+'</div>';;
            }
            
            oDiv.innerHTML= html;
            htmlChild.appendChild(oBg);
            htmlChild.appendChild(oDiv);
            var oDivW = oDiv.offsetWidth/2;
            var oDivH = oDiv.offsetHeight/2;
            var windowH = (htmlChild.offsetHeight || window.outerHeight)/3;
            //var windowH = htmlChild.offsetHeight/3 === 0 ?400 : htmlChild.offsetHeight/3;
            var windowW = htmlChild.offsetWidth/2;

            //oDiv.style.left = windowW - oDivW+'px';
           // oDiv.style.top = windowH - oDivH+'px';  
            //console.log(oDiv.childNodes[1].childNodes[0]);
            oDiv.childNodes[1].childNodes[1].childNodes[0].onclick=function(){
                htmlChild.removeChild(oBg);
                htmlChild.removeChild(oDiv);
                if(fn){fn();}
            };
            if(fn){
                oDiv.childNodes[1].childNodes[1].childNodes[1].onclick=function(){
                    htmlChild.removeChild(oBg);
                    htmlChild.removeChild(oDiv);
                };
            }
    };

    var obj = {
        alerts:function(text){
            var btn ='<div class="btn-box clrfix"><a href="javascript:void(0)" id="confirms" class="btn btn-info btn-sm">确认</a></div>';
            createDiv(text,btn);
        },
        confirms:function(text,fn){
            var btn ='<div class="btn-box clrfix"><a href="javascript:void(0)" id="confirms" class="btn btn-info btn-sm">确认</a><a href="javascript:void(0)" id="cancel" class="btn btn-default btn-sm ml-10">取消</a></div>';
            createDiv(text,btn,fn);
        }
    };
    return obj;
}();

