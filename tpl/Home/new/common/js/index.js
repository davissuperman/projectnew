$(function() {
	var navObjId = "";
	if(window.location.href.indexOf("#Portfolio") > 0 )
	{navObjId = "#Portfolio";}
	if(window.location.href.indexOf("#services") > 0 )
	{navObjId = "#services";}
	if(window.location.href.indexOf("#about") > 0 ) {
		navObjId = "#about";
		if(window.location.href.indexOf("#aboutjob") > 0 )
		{showPagei(4,4,1);}
	}
	if(window.location.href.indexOf("#contact") > 0 )
	{navObjId = "#contact";}
	if (navObjId != "")
	{
		//$('html,body').animate({scrollTop: $(id).offset().top - 64 + "px"},2000, 'easeInOutQuint');
		$(".top_nav a[href="+navObjId+"]").trigger("click");
		xOnclick=0; 
		//return false;
	}
	$("#focusIndex1").show();
	$("#focusBar li").css("width",$(window).width());
	$("a.arrL").mouseover(function(){stopFocusAm();}).mouseout(function(){starFocustAm();});
	$("a.arrR").mouseover(function(){stopFocusAm();}).mouseout(function(){starFocustAm();});
	$("#focusBar li").mouseover(function(){stopFocusAm();}).mouseout(function(){starFocustAm();});
	starFocustAm();
	
	$("[title=contactForm]").focus(function(){
	  $(this).css("color","#777777");
	  $(this).css("border-color","#fd8200");
	});
	$("[title=contactForm]").blur(function(){
	  $(this).css("color","#cccccc");
	  $(this).css("border-color","#d9d9d9");
	});

	$(window).bind('scroll resize', function(e){
		var _scrolltop=$(window).scrollTop();
		var _headerTop = _scrolltop+128;
		var _colTitBarId = "Home"; 
		$(".colTitBar").each(function(){
			var offset = $(this).offset();
			if(_headerTop >= offset.top) {
				_colTitBarId = $(this).attr('id');
			}
		});
		if(_colTitBarId == 'about') {
			//if(!$(".colorImg").is(":visible"))    
				$(".colorImg").fadeIn(1500);

		} else {
			//if($(".colorImg").is(":visible"))
		$(".colorImg").stop(false,true); 
				$(".colorImg").fadeOut(1500);
                       //$(".colorImg").animate({opacity: 'hide' }, { duration: 1500 });
		}
		if(xOnclick == 1) return;
		if(!$(".top_nav a[href=#"+_colTitBarId+"]").hasClass('on')) {
			$(".navBg").css("display","none");
			$(".top_nav a[href=#"+_colTitBarId+"]").children().css("display","block");
			$(".navBg").removeAttr("id");
			$(".top_nav a[href=#"+_colTitBarId+"]").children().attr("id","navBgOn");
			$(".top_nav a").removeClass();
			$(".top_nav a[href=#"+_colTitBarId+"]").addClass("on");
		}
	});
	$(window).bind('resize', function(e){
		changeFocus(true);
		$(".fullScreen").css("width",$(window).width());
		$(".fullScreen").css("height",$(window).height());
	});
	$(window).bind('scroll', function(e){
		if($.browser.msie &&  6 == $.browser.version) {
			$(".fullScreen").css("top",$(window).scrollTop());
		}
	});
	$(".fullScreen").css("width",$(window).width());
	$(".fullScreen").css("height",$(window).height());
	$(".fullScreen").css("left",$(window).width());
	$(".fullScreen").css("top",0);
});
	
var xOnclick=0;
$(".nav a").click(
   function() {
	  $(".navBg").css("display","none");
	  $(this).children().css("display","block");
	  $(".navBg").removeAttr("id");
	  $(this).children().attr("id","navBgOn");
	  $(".nav a").removeClass();
	  $(this).addClass("on");
	  xOnclick = 1;
	   });

$(".nav a").hover(
  function () {
  $(this).children().stop(false,true);
  $(this).children().fadeIn("normal");
  },
  function () {
  var navBgStatus=($(this).children().attr("id"))
  //alert (navBgStatus)
	if (xOnclick != 1 && navBgStatus != "navBgOn"){
 $(this).children().stop(false,true);
 $(this).children().fadeOut("normal");
 }
 else 
  {
	  xOnclick=0; 
	  }
});
//---------------
var navmenu = $("#menuNav a");
		navmenu.click(function(e){
		e.preventDefault();
		var id = $(this).attr("href");
        $('html,body').animate({scrollTop: $(id).offset().top - 64 + "px"},2000, 'easeInOutQuint');
		return false;
	});
	$(window).scroll(function(){
		$(".nav").addClass("top_scroll");
	})
	//$("#get").click(function(e){
//		e.preventDefault();
//		$("#menu a[href='#contact']").click();
//	})


/*--portfolioMenu---*/
var mOnclick=0;
$(".portfolioMenu a").click(
   function() {
	  $(".menuBg").css("display","none");
	  $(this).children().css("display","block");
	  $(".menuBg").removeAttr("title");
	  $(this).children().attr("title","menuBgOn");
	  mOnclick = 1;
	   });
$(".portfolioMenu a").hover(
  function () {	 
	$(this).children().stop(false,true);
    $(this).children().fadeIn("normal");
  },
  function () {
 var menuBgStatus=($(this).children().attr("title"))
 if (mOnclick != 1 && menuBgStatus != "menuBgOn"){
 $(this).children().stop(false,true);
 $(this).children().fadeOut("normal");}
 else 
  {
	  mOnclick=0; 
	  }	
	
	
  });
//services
$(".serBox").hover(
  function () {
	 $(this).children().stop(false,true);
	 $(this).children(".serBoxOn").fadeIn("slow");
     $(this).children(".pic1").animate({right: -110},400);
     $(this).children(".pic2").animate({left: 105},400);
     $(this).children(".txt1").animate({left: -240},400);
     $(this).children(".txt2").animate({right: 40},400);	 
	 }, 
  function () {
	 $(this).children().stop(false,true);
	 $(this).children(".serBoxOn").fadeOut("slow");
	 $(this).children(".pic1").animate({right:105},400);
     $(this).children(".pic2").animate({left: -110},400);
     $(this).children(".txt1").animate({left: 40},400);
     $(this).children(".txt2").animate({right: -240},400);	
  }
);


function serFocus(i) {
	$(".servicesPop").slideDown("normal");
	changeflash(i);
	}
function closeSerPop() {$(".servicesPop").slideUp("fast");}	

