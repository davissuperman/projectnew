// 定义变量
var screenHeight=$(window).height();
var pageNumber=0;
var currentDistance=0;
var contentList=$("#content-list");
var tmpEndY,tmpStartY;
var isFlip=[0,2,2,2,0,0,0];
var showTheLast=0;



// 判断是否安卓

var sUserAgent = navigator.userAgent.toLowerCase();
var bIsAndroid = sUserAgent.match(/android/i) == "android";

// 判断是否短屏手机
var isShort;
if($(window).height()<=416){
    isShort=true;
}
$(document).ready(function(e) {
	cal_con_hg();//计算页面内容高度
	init();
	var contentList=$("#content-list");
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
	controlMusic();
});

//模拟alert
function mnAlert (mes){
	var htmlStr = "<div class=\"layerBox\">";
		htmlStr += "<div class=\"layerBg\">";
		htmlStr += "<p class=\"layerMsg\">"+mes+"</p>";
		htmlStr += "<button class=\"layerBtn\" onClick=\"javascript:$(this).closest('.layerBox').remove();\">我知道了</button>";
		htmlStr += "</div></div>";
	$("body").append(htmlStr);
}


$(window).resize(function(){
	cal_con_hg();
});
function cal_con_hg(){
	var wh = $(window).height(),$content_list = $("#content-list");
	$content_list.parent().height(wh);
	$content_list.find("li").height(wh);
}
function checkPhone(mb){
	var mobile = mb;
	if(mobile != '' && mobile.length == 11){
		var reg = /^((\(\d{3}\))|(\d{3}\-))?13\d{9}|14[57]\d{8}|15\d{9}|18\d{9}|176\d{8}|177\d{8}|178\d{8}|1700\d{7}|1705\d{7}|1709\d{7}$/;  
		return reg.test(mobile);
	}
	return false;
}
// 上一屏
function screenBack(){

    var translateString,transitionString;
	if(pageNumber>=6){
		return false;
	}
    pageNumber--;

    if(pageNumber<0){
        pageNumber=0;
    }
	
    currentDistance=screenHeight*pageNumber;
    translateString="translate3d(0, -"+currentDistance+"px, 0)";
    transitionString="all 0.5s ease-in";

    $("#content-list").css({"-webkit-transform":translateString,"transform":translateString,"-webkit-transition":transitionString,"transition":transitionString});
	
}

// 下一屏
function screenForward(eve){
	
	if(pageNumber == 1){
		$(".wmn-wrap").find(".mm").animate({"right":+ 0 +'rem'}, 1000, '', function(){});
		window.setTimeout('F19();', 100);
	}
    var translateString,transitionString;
    pageNumber++;
	if(eve == 'touch'){
		if(pageNumber > 6){
			pageNumber=6;
			return false;
		}
	}
	
    
    currentDistance=screenHeight*pageNumber;
    translateString="translate3d(0, -"+currentDistance+"px, 0)";
    transitionString="all 0.5s ease-in";
    $("#content-list").css({"-webkit-transform":translateString,"transform":translateString,"-webkit-transition":transitionString,"transition":transitionString});

}

var SHAKE_THRESHOLD = 400;
var last_update = 0;
var index=0;
var x = y = z = last_x = last_y = last_z = 0;
var w_curTime=0;
function init() {
	if (window.DeviceMotionEvent) {
		
		window.addEventListener('devicemotion', deviceMotionHandler, false);

	} else {
		mnAlert ('您的手机不支持摇一摇,请点击摇一摇按钮');
	}
	$("#btn-shake").bind('click',function(){
		doResult();
	});
	$("#btn-isend").bind('click',function(){
		 $(this).parents(".page-item").next().show().siblings().hide();
	});

	
	
	
	$("#btn-send-zhufu").bind('click',function(){
		var $this = $(this);
		var $rec_name = $("#rec-name"),$send_name = $("#send-name"),
		rec_name = $.trim($rec_name.val()),send_name = $.trim($send_name.val());
		if(rec_name == ""){
			mnAlert ('填写您想祝福的人的姓名');
			return false;
		}else if(send_name == ""){
			mnAlert ('填写您的姓名');
			return false;
		}
        $("#formzhufu").submit();
		//$this.parents(".page-item").next().show().siblings().hide();
	});
	
}
function deviceMotionHandler(eventData) {
	
	var acceleration = eventData.accelerationIncludingGravity;
	var curTime = new Date().getTime();
	if ((curTime - last_update) > 100) {
		var diffTime = curTime - last_update;
		last_update = curTime;
		x = acceleration.x;
		y = acceleration.y;
		z = acceleration.z;
		var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;

		if (speed > SHAKE_THRESHOLD) { 					
			if((curTime-w_curTime)>2000){						
				w_curTime!=0 && doResult();
				w_curTime=curTime;															
			} 
		}
		last_x = x;
		last_y = y;
		last_z = z;
	}
}
 function doResult() {	
 	if($("#content-list").css("-webkit-transform") != 'none'){
		var atrr = $.trim($("#content-list").css("-webkit-transform")).split(",");
		if($.trim(atrr[1]) == '0px'){
			var $content_list = $("#content-list");
			$content_list.find(".p0 .cloud1").addClass("zoomOutUp");
			$content_list.find(".p0 .cloud4").addClass("zoomOutUp");
			$content_list.find(".p0 .cloud3").addClass("zoomOutUp");
			$content_list.find(".p0 .cloud2").addClass("zoomOutLeft");
			$content_list.find(".p0 .cloud6").addClass("zoomOutDown");
			$content_list.find(".p0 .cloud7").addClass("zoomOutDown");
			$content_list.find(".p0 .cloud5").addClass("zoomOutRight");
			setTimeout(function(){
				screenForward('touch');
			},2000);
		}
		
	}else{
		var $content_list = $("#content-list");
		$content_list.find(".p0 .cloud1").addClass("zoomOutUp");
		$content_list.find(".p0 .cloud4").addClass("zoomOutUp");
		$content_list.find(".p0 .cloud3").addClass("zoomOutUp");
		$content_list.find(".p0 .cloud2").addClass("zoomOutLeft");
		$content_list.find(".p0 .cloud6").addClass("zoomOutDown");
		$content_list.find(".p0 .cloud7").addClass("zoomOutDown");
		$content_list.find(".p0 .cloud5").addClass("zoomOutRight");
		setTimeout(function(){
			screenForward('touch');
		},2000);
	}
 	
}
function swithPage(page){
	var $content_list = $("#content-list"),hg = $content_list.parent().height();
	hg = page * hg;
	setTimeout(function(){
		$content_list.animate({"top":'-'+hg + 'px'}, 1000, '', function(){});
		//$content_list.addClass("fadeOutUp");
	},2000);
}




function startTouch(event) {
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
        //console.log(pageNumber+":"+isFlip[pageNumber]);
        //if(isFlip[pageNumber]<=1){
            //screenForward();
			screenForward('touch');
            //$(".notice-swipe-up").removeClass("swipeMove");

       // }else{
            //flipCard();
       // }
		
    }else if(endY && endY !== startY && result >=25){
        //console.log(pageNumber+":"+isFlip[pageNumber]);
		screenBack();
       // if(!isFlip[pageNumber] || isFlip[pageNumber]===2){
        //    screenBack();
        //}else{
           // flipCard();
        //}

    }
}

function controlMusic(){
	// 控制声音
	$(".speaker").on("click",function(){
		var audioEle=document.querySelector("audio");
		if(audioEle.paused){
			$(".speaker").removeClass("speaker_muted");
			audioEle.play();
		}else{
			$(".speaker").addClass("speaker_muted");
			audioEle.pause();
		}
	});
}




const NUMBER_OF_LEAVES = 50;
 
function initXue()
	{
		
		var container = document.getElementById('leafContainer');
	   
		for (var i = 0; i < NUMBER_OF_LEAVES; i++) 
		{
			container.appendChild(createALeaf());
		}
	}

	function randomInteger(low, high)
	{
		return low + Math.floor(Math.random() * (high - low));
	}

	 
	function randomFloat(low, high)
	{
		return low + Math.random() * (high - low);
	}

	 
	function pixelValue(value)
	{
		return value + 'px';
	}
	 
	function durationValue(value)
	{
		return value + 's';
	}
	 
	function createALeaf()
	{
    
    var leafDiv = document.createElement('div');
    var image = document.createElement('img');
    
   
    image.src ='images/p3/snow' + randomInteger(1, 5) + '.png';
    
    leafDiv.style.top = "-10px";

    
    leafDiv.style.left = pixelValue(randomInteger(0, 1000));
    
   
    var spinAnimationName = (Math.random() < 0.5) ? 'clockwiseSpin' : 'counterclockwiseSpinAndFlip';
    
    
    leafDiv.style.webkitAnimationName = 'fade, drop';
    image.style.webkitAnimationName = spinAnimationName;
    
   
    var fadeAndDropDuration = durationValue(randomFloat(5, 11));
   
    var spinDuration = durationValue(randomFloat(4, 8));
     
    leafDiv.style.webkitAnimationDuration = fadeAndDropDuration + ', ' + fadeAndDropDuration;

    var leafDelay = durationValue(randomFloat(0, 5));
    leafDiv.style.webkitAnimationDelay = leafDelay + ', ' + leafDelay;

    image.style.webkitAnimationDuration = spinDuration;
 
    leafDiv.appendChild(image);
 
    return leafDiv;
	}
 
window.addEventListener('load', initXue);

$(document).ready(function(e) {
    $("html").css({"font-size":(100*$(window).width()) / 640});
});
$(window).resize(function(){
	$("html").css({"font-size":(100*$(window).width()) / 640});
});

