$(document).ready(function(e) {
	goBack();
	initPageFontSize();
	closeLayer();
	//hideHoverBox();
});


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
			if(time == 0){
				gameover();
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
