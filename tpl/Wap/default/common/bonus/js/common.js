$(document).ready(function(e) {
    if(document.getElementById("scrolling")){
		var scrollup = new ScrollText("scrolling");
		scrollup.LineHeight = 30;//单排文字滚动的高度
		scrollup.Amount = 3; //注意:子模块(LineHeight)一定要能整除Amount.
		scrollup.Delay = 200;//延时
		scrollup.Direction = "up";//文字向上滚动
		scrollup.Start();//开始滚动
	}
	goBack();
	cal_con_hg();//计算页面内容高度
	closeLayer();
	getAward();
	share_weixin();
	calProgress();
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
function checkPhone(mb){
	var mobile = mb;
	if(mobile != '' && mobile.length == 11){
		var reg = /^((\(\d{3}\))|(\d{3}\-))?13\d{9}|14[57]\d{8}|15\d{9}|18\d{9}|176\d{8}|177\d{8}|178\d{8}|1700\d{7}|1705\d{7}|1709\d{7}$/;  
		return reg.test(mobile);
	}
	return false;
}
function calProgress(){
	var $presentlist = $(".present-list"),$progress = $presentlist.find(".progress"),
	$s1 = $progress.find(".s1"),$s2 = $progress.find(".s2"),
	$s3 = $progress.find(".s3"),$s4 = $progress.find(".s4"),
	$s5 = $progress.find(".s5"),$cur = $progress.find(".cur"),
	$peopos = $presentlist.find(".peo-pos"),
	sh1 = $s1.height(),
	sh2 = $s2.height(),
	sh3 = $s3.height(),
	sh4 = $s4.height(),
	sh5 = $s5.height(),
	bottom = 0;
	if($s5.css("display") != 'none'){
		bottom = sh1 + sh2 + sh3 + sh4 + sh5;
	}else if($s4.css("display") != 'none'){
		bottom = sh1 + sh2 + sh3 + sh4;
	}else if($s3.css("display") != 'none'){
		bottom = sh1 + sh2 + sh3;
	}else if($s2.css("display") != 'none'){
		bottom = sh1 + sh2;
	}else if($s1.css("display") != 'none'){
		bottom = sh1;
	}
	$peopos.css({"bottom":bottom});
	$cur.css({"bottom":bottom});
}

function share_weixin(){
	var $btn_share = $(".btn-share");
	$btn_share.bind('click',function(){
		showLayer($("#share-weixin"),'1');
	});
}
function getAward(){
	var $get_award = $(".get-award");
	$get_award.bind('click',function(){
		var $this = $(this);
		if(!$this.hasClass("btn-disabled")){
            if($this.hasClass("NOTop1")){
                showLayer($("#award-box-NOTop1"));
            }else if($this.hasClass("NO1")){
				showLayer($("#award-box-NO1"));
			}else if($this.hasClass("NO2")){
				showLayer($("#award-box-NO2"));
			}else if($this.hasClass("NO3")){
				showLayer($("#award-box-NO3"));
			}else if($this.hasClass("NO4")){
				showLayer($("#award-box-NO4"));
			}
		}
	});
}

/*close layer*/
function closeLayer(){
	var $layerbox = $("#layer-box"),$btn_close = $layerbox.find(".btn-close");
	$btn_close.bind('click',function(){
		$("#contents").show();
		//$layerbox.hide();
		var wini = document.documentElement.clientWidth+"px";
		$layerbox.animate({right:'-'+wini}, 'fast', '', function(){$layerbox.hide();});
	});
}
/*show layer*/
function showLayer($elem,isBlack,title){
	var $layerbox = $("#layer-box"),
	$layerbg = $layerbox.find(".layer-bg"),
	$title = $layerbox.find(".layer-title .title"),
	$layerwrap = $layerbox.find(".layer-wrap");
	$("#contents").hide();
	$title.html(title);
	if(isBlack == '1'){
		$layerbg.css({"background":"#c70d25"});
	}else if(isBlack == '2'){
		$layerbg.css({"background":"#000","opacity":"0.9"});
	}else{
		$layerbg.css({"background":"#fff7da","opacity":"1"});
	}
	//$layerbox.show();
	var wini = document.documentElement.clientWidth+"px";
	$layerbox.show().css({right:'-'+wini});
	$layerbox.animate({right:0}, 'fast', '' ,'');
	$elem.show().siblings().hide();
	document.body.scrollTop = 0;
}

function cal_con_hg(){
	var wh = $(window).height() - 20;
	$("#contents").css({"min-height":wh});
}
function goBack(){
	$("#go-back").bind('click',function(){
		window.history.back();
	});
}

$(window).resize(function(){
	cal_con_hg();
});

function ScrollText(content, btnPrevious, btnNext, autoStart) {
  this.Delay = 10;
  this.LineHeight = 20;
  this.Amount = 1; //注意:LineHeight一定要能整除Amount.
  this.Direction = "up";
  this.Timeout = 2000;
  this.ScrollContent = this.$(content);
  this.ScrollContent.innerHTML += this.ScrollContent.innerHTML;
  //this.ScrollContent.scrollTop = 0;
  if (btnNext) {
    this.NextButton = this.$(btnNext);
    this.NextButton.onclick = this.GetFunction(this, "Next");
    this.NextButton.onmouseover = this.GetFunction(this, "Stop");
    this.NextButton.onmouseout = this.GetFunction(this, "Start");
  }
  if (btnPrevious) {
    this.PreviousButton = this.$(btnPrevious);
    this.PreviousButton.onclick = this.GetFunction(this, "Previous");
    this.PreviousButton.onmouseover = this.GetFunction(this, "Stop");
    this.PreviousButton.onmouseout = this.GetFunction(this, "Start");
  }
  this.ScrollContent.onmouseover = this.GetFunction(this, "Stop");
  this.ScrollContent.onmouseout = this.GetFunction(this, "Start");
  if (autoStart) {
    this.Start();
  }
}

ScrollText.prototype.$ = function(element) {
  return document.getElementById(element);
}

ScrollText.prototype.Previous = function() {
  clearTimeout(this.AutoScrollTimer);
  clearTimeout(this.ScrollTimer);
  this.Scroll("up");
}

ScrollText.prototype.Next = function() {
  clearTimeout(this.AutoScrollTimer);
  clearTimeout(this.ScrollTimer);
  this.Scroll("down");
}

ScrollText.prototype.Start = function() {
  clearTimeout(this.AutoScrollTimer);
  this.AutoScrollTimer = setTimeout(this.GetFunction(this, "AutoScroll"), this.Timeout);
}

ScrollText.prototype.Stop = function() {
  clearTimeout(this.ScrollTimer);
  clearTimeout(this.AutoScrollTimer);
}

ScrollText.prototype.AutoScroll = function() {
  if (this.Direction == "up") {
    if (parseInt(this.ScrollContent.scrollTop) >= parseInt(this.ScrollContent.scrollHeight) / 2) {
      this.ScrollContent.scrollTop = 0;
    }
    this.ScrollContent.scrollTop += this.Amount;
  } else {
    if (parseInt(this.ScrollContent.scrollTop) <= 0) {
      this.ScrollContent.scrollTop = parseInt(this.ScrollContent.scrollHeight) / 2;
    }
    this.ScrollContent.scrollTop -= this.Amount;
  }
  if (parseInt(this.ScrollContent.scrollTop) % this.LineHeight != 0) {
    this.ScrollTimer = setTimeout(this.GetFunction(this, "AutoScroll"), this.Delay);
  } else {
    this.AutoScrollTimer = setTimeout(this.GetFunction(this, "AutoScroll"), this.Timeout);
  }
}

ScrollText.prototype.Scroll = function(direction) {
  if (direction == "up") {
    if (this.ScrollContent.scrollTop == 0) {
      this.ScrollContent.scrollTop = parseInt(this.ScrollContent.scrollHeight) / 2;
    }
    this.ScrollContent.scrollTop -= this.Amount;
  } else {
    this.ScrollContent.scrollTop += this.Amount;
  }
  if (parseInt(this.ScrollContent.scrollTop) >= parseInt(this.ScrollContent.scrollHeight) / 2) {
    this.ScrollContent.scrollTop = 0;
  }
  if (parseInt(this.ScrollContent.scrollTop) % this.LineHeight != 0) {
    this.ScrollTimer = setTimeout(this.GetFunction(this, "Scroll", direction), this.Delay);
  }
}

ScrollText.prototype.GetFunction = function(variable, method, param) {
  return function() {
    variable[method](param);
  }
}
