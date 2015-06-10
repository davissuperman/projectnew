$(document).ready(function(e) {
    initPageFontSize();
	closeLayer();
});
$(window).resize(function(){
	initPageFontSize();
});
function initPageFontSize(){
	$("html").css({"font-size":(100*$(window).width()) / 640});
	var wh = $(window).height(),dh = $("#contents").height();
}

/*close layer*/
function closeLayer(){
	var $layerbox = $("#layer-box"),$btn_close = $layerbox.find(".btn-close");
	$btn_close.bind('click',function(){
		//$layerbox.hide();
		var wini = document.documentElement.clientWidth+"px";
		$layerbox.animate({right:'-'+wini}, 'fast', '', function(){$layerbox.hide();});
	});
}
/*show layer*/
function showLayer($elem,title){
	var $layerbox = $("#layer-box"),
	$layerbg = $layerbox.find(".layer-bg"),
	$title = $layerbox.find(".layer-title .title"),
	$layerwrap = $layerbox.find(".layer-wrap");
	$title.html(title);
	
	var wini = document.documentElement.clientWidth+"px";
	$layerbox.show().css({right:'-'+wini});
	$layerbox.animate({right:0}, 'fast', '' ,'');
	$elem.show().siblings().hide();
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




