var tmpEndY,tmpStartY;
numbers = 0;
$(document).ready(function(e) {
	goBack();
	initPageFontSize();
	closeLayer();
	showMmNumer();
	if($("#mm-box").length > 0){
		var cd = new initGame();
		cd.going();
	}
});

function playAudio(){
	var audio = document.getElementById('audio'); 
 	audio.play();
}

function initGame(){
	var $mm_box = $("#mm-box"),$shou = $mm_box.find(".shou");
	this.going = function (){
		var top = 1+"rem";
		$shou.animate({top:'-'+top}, 'fast', '', function(){
			$shou.css({"top":"-0.2rem"});
		});
		var st=setTimeout (arguments.callee, 1000);
	}
}


function startGame(){
	playAudio();
	var $mm_box = $("#mm-box"),$animation = $mm_box.find(".animation"),$face_hover = $("#face-hover"),$time = $("#time");
	if(!$mm_box.hasClass("dis")){
		$mm_box.addClass("dis");
		var cd = new countdown($time,10);
		cd.doTiming();
	}
	//if(!$mm_box.hasClass("gameover")){
		numbers++;
		
		var $face_hover = $("#face-hover"),$face_hover2 = $("#face-hover2");
		if(numbers > 19){
			$face_hover2.hide();
			if(numbers > 30){
				$face_hover.hide();
			}
		}
		
		$("#mm-nums").html(numbers);
		$animation.show();
		var top = 2+"rem",height = 0.5 + 'rem';
		$animation.animate({top:'-'+top,height:height}, 'fast', '', function(){
			$animation.hide();
			$animation.css({"top":"0.5rem","height":"2.5rem"});
			//$face_hover.hide();
			//setTimeout ("$('#face-hover').show();",100);
		});
	//}else{
	//}
}
$(document).ready(function(e) {
	//e.preventDefault();
	var $face_hover = $("#game-box");
	$face_hover.on("touchstart",function(event){
		event.preventDefault();
	});
	var contentList= $("#mm-box");
	// 绑定翻页
	contentList.on("touchstart",function(e){
		startTouch(e);
	});
	contentList.on("touchmove",function(e){
		moveTouch(e);
	});
	contentList.on("touchend",function(e){
		endTouch(e);
	});
});
function startTouch(event) {
	event.preventDefault();
    if (!event.touches.length) {
        return;
    }
    tmpEndY = 0;
    var touch = event.touches[0];
    tmpStartY = touch.pageY;
}

function moveTouch(event) {
    event.preventDefault();
    if (!event.touches.length) {
        return;
    }
    var touch = event.touches[0];
    tmpEndY = touch.pageY;
}

// 触摸结束时判断执行上翻或者下翻
function endTouch() {
    var endY = tmpEndY;
    var startY = tmpStartY;
	var result = parseFloat(endY) - parseFloat(startY);
    if (endY && endY !== startY && result <= -25){
		startGame();
    }else if(endY && endY !== startY && result >=25){
    }
}
function showMmNumer(){
	var number = urlPara('number='),$mmNumber = $("#mmNumber");
	if(number != '' && $mmNumber != undefined){
		$mmNumber.html(number);
	}	
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
}
//模拟alert
function mnAlert (mes){
	var htmlStr = "<div class=\"layerBox\">";
		htmlStr += "<div class=\"layerBg\">";
		htmlStr += "<p class=\"layerMsg\">"+mes+"</p>";
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

function goBack(){
	$("#go-back").bind('click',function(){
		window.history.back();
	});
}
