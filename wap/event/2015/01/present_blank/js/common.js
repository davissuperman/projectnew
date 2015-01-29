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
	$("#character-list").children("li").each(function(index, element) {
        var $li = $(this),hg = $li.find(".des").height();
		if(hg >= 20){
			$li.find(".des").css({"margin-top":"-14px;"});
		}else{
			$li.find(".des").css({"margin-top":"-8px;"});
		}
    });
});

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
