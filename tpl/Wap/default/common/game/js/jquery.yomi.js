/** 
 * 湖南雅商网络科技有限公司
 * 倒计时插件
 * 刘文建
 */
(function($){
$.fn.yomi=function(){
	var data="";
	var _DOM=null;
	var TIMER;
	createdom =function(dom){
		_DOM=dom;
		data=$(dom).attr("data");
		data = data.replace(/-/g,"/");
		data = Math.round((new Date(data)).getTime()/1000);
		$(_DOM).append("<ul class='yomi'><li class='yomiday'></li><li class='yomihour'></li><li class='yomimin'></li><li class='yomisec'></li></ul>")
		reflash();

	};
	reflash=function(){
		var	range  	= data-Math.round((new Date()).getTime()/1000),
					secday = 86400, sechour = 3600,
					days 	= parseInt(range/secday),
					hours	= parseInt((range%secday)/sechour),
					min		= parseInt(((range%secday)%sechour)/60),
					sec		= ((range%secday)%sechour)%60;
             if(days>0){
                    $(_DOM).find(".yomiday").html(nol(days));
                 }else{
                 	$(_DOM).find(".yomiday").html(nol(0));
                 }
                 if(hours>0){
			$(_DOM).find(".yomihour").html(nol(hours));
                 }else{
			$(_DOM).find(".yomihour").html(nol(0));
                 }
                 if(min>0){
			$(_DOM).find(".yomimin").html(nol(min));
                 }else{
                 	$(_DOM).find(".yomimin").html(nol(0));
                 }
                 if(sec>0){
			$(_DOM).find(".yomisec").html(nol(sec));
                 }else{
			$(_DOM).find(".yomisec").html(nol(0));
                 }
	};
	TIMER = setInterval( reflash,1000 );
	nol = function(h){
					return h>9?h:'0'+h;
	}
	return this.each(function(){
		var $box = $(this);
		createdom($box);
	});
}
})(jQuery);
$(function(){
	$(".yomibox").each(function(){
		$(this).yomi();
	});
	//$("head").append("<style type='text/css'>.yomi {list-style:none;overflow: hidden;width: 185px;margin: auto;}.yomi li{float:left;background:url('http://wx.drjou.cc/tpl/Wap/default/common/game/img/numBjs.png') no-repeat center top;background-size:100% 100%;color:red;padding:10px;font-size:14px; font-weight:bold;margin: 5px 6px;}.yomi li.split{background:none;margin:10px 0;padding:10px 0;color:#000000;}</style>")
});