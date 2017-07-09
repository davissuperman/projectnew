/**
 * Created with joe
 * To change this template use File | Settings | File Templates.
 */
/* 共用 js 模块 *

/* 时间插件 使用 */
var fast_date_obj = {
	model : {
		timeRangeList : []
	},
	init:function(elem){
		var $elem = $(elem);
		this.setStartTime(elem);
	    this.setEndTime(elem);
	    this.datepickerjQ(elem);
		
	    //var val = $.trim($elem.val());
	    var daterange = $.trim($elem.attr("dateRange"));
	    
	    if(daterange && daterange.indexOf(',') != -1){
	    	var dataRangeArr = daterange.split(",");
			$("#startDate").val(dataRangeArr[0]);
			$("#endDate").val(dataRangeArr[1]);
		}else{
			fast_date_obj.initDatetime($elem);
		}
	},
	initDatetime : function($elem){
		var nowDate = new Date();
	    var timeStr = nowDate.getFullYear() + '-' + (nowDate.getMonth()+1) + '-' + nowDate.getDate();
	    nowDate.setDate(nowDate.getDate()+parseInt(-1));
	    var endDateStr = nowDate.getFullYear() + '-'+  (nowDate.getMonth()+1) + '-' + nowDate.getDate();
	   // $elem.val(endDateStr +"至"+ timeStr);
		$("#startDate").val(endDateStr);
		$("#endDate").val(timeStr);
	},
	createDatePanel:function(elem){
		$(".ui-datepicker-css").remove();
		var timeRangeList = fast_date_obj.model.timeRangeList;
		var html = '<div class="ui-datepicker-css">';
		html += '<div class="ui-datepicker-quick">';
		html += '<p>快捷日期<a class="ui-close-date">X</a></p>';
	    html += '<div class="clrfix">';
	    /*for(var i = 0; i < timeRangeList.length; i++){
	    	html += '<input class="ui-date-quick-button btn btn-default btn-xs" type="button" value="'+timeRangeList[i].dicItemValue+'" alt="' + timeRangeList[i].id + '"  name=""/>';
	    } */       
	    
	    html += '<select class="form-control">';
		for(var i = 0; i < timeRangeList.length; i++){
			html += '<option value="' + timeRangeList[i].id + '">' + timeRangeList[i].dicItemValue + '</option>';
		}
		html += '</select>';
		html += '<button type="button" class="btn fl btn-primary btn-xs" id="confirmNumberFast">确定</button>';
	    
	    html += '</div></div>';
	    	
	    html += '<div class="ui-datepicker-choose"><p>输入日期</p><div class="ui-input-date clrfix">'	
	    html += '<span class="fl ml-10">近</span><input id="inputDate" class="form-control ml-5" type="text" placeholder="请输入天数"><span class="fl ml-5">日</span>';
	    
	    /*if(unitDictItemKey){
			var paramsHtml = fast_date_obj.buildUnitSelectHtml(unitDictItemKey);
			html += paramsHtml;
		}*/
	    
	    html += '<button type="button" class="btn btn-primary btn-xs ml-10" id="confirmDate">确定</button>';
	    html += '</div></div>';
	    	
	            
	    html += '<div class="ui-datepicker-choose"><p>自选日期</p><div class="ui-datepicker-date clrfix">';
	    html += '<input name="startDate" id="startDate" class="startDate form-control ml-5" readonly type="text">';    
	    html += '<span>至</span>';
	    html += '<input name="endDate" id="endDate" class="endDate form-control" readonly type="text" >';  
	    html += '<button type="button" class="btn btn-primary btn-xs" onclick="fast_date_obj.datePickers(this);">确定</button>';
	    html += '</div></div></div>';      	
	    $(elem).after(html);    
	    $(".ui-datepicker-css").css("display","block");
	    this.init(elem);
	    
	    if(window.parent && window.parent.iFrameScrollHeight){
			window.setTimeout(window.parent.iFrameScrollHeight,200);
		}
	},
	buildUnitSelectHtml:function(dictItemKey){
		var dictItemList = appConfig.dicMap[dictItemKey];
		var html = '<select class="form-control unit">';
		for(var i = 0; i < dictItemList.length; i++){
			html += '<option value="' + dictItemList[i].dicItemKey + '">' + dictItemList[i].dicItemValue + '</option>';
		}
		html += '</select>';
		return html;
	},
	setStartTime:function(){
		$('#startDate').datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: "+d",
            onClose : function(dateText, inst) {
                $("#endDate").datepicker( "show" );
                if (dateText > $("#endDate").val()){
                	$("#startDate").addClass("input-error");
                	$("#endDate").addClass("input-error");
                }else{
                	$("#startDate").removeClass("input-error");
                	$("#endDate").removeClass("input-error");
                }
            },
			onSelect:function(dateText, inst) {
                $("#endDate").datepicker( "option","minDate",dateText );
            },
			
        });
	},
	setEndTime:function(){
		$("#endDate").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: "+d",
			defaultDate : new Date(),
            onClose : function(dateText, inst) {
                if (dateText < $("#startDate").val()){
                	$("#endDate").datepicker( "show" );
				    //$.Pop.alerts("结束日期不能小于开始日期。");
					//$("#endDate").val(newdate);
                	$("#startDate").addClass("input-error");
                	$("#endDate").addClass("input-error");
                }else{
                	$("#startDate").removeClass("input-error");
                	$("#endDate").removeClass("input-error");
                }
            }
        });
	},
	datepickerjQ:function(elem){
		var $elem = $(elem);
		$(".ui-datepicker-time").on("click",function(){
           $(".ui-datepicker-css").css("display","block");
        });
        $(".ui-kydtype li").on("click",function(){
            $(".ui-kydtype li").removeClass("on").filter($(this)).addClass("on");
        });
        $(".ui-datepicker-quick input").on("click",function(){
        	var $this = $(this);
            var thisAlt = $this.attr("alt");
            var thisVal = $this.val();
            $elem.val(thisVal);
            $elem.attr("dateRange",thisAlt);
            $elem.attr("dateRangeUnit",'日');
            $elem.removeClass("input-error");
            $(".ui-datepicker-css").remove();
			$("#ui-datepicker-div").css("display","none");
        });
        
        $("#confirmNumberFast").click(function(){
        	var $this = $(this);
        	var $select = $this.siblings("select");
            var thisVal = $select.val();
            var thisText = $select.find('option:selected').text();
            $elem.val(thisText);
            $elem.attr("dateRange",thisVal);
            $elem.attr("dateRangeUnit","");
            $elem.removeClass("input-error");
            $(".ui-datepicker-css").remove();
        });
        
        $(".ui-close-date").on("click",function(){
        	$(".ui-datepicker-css").remove();
			 $("#ui-datepicker-div").css("display","none");
        });
		 $(".startDate").on("click",function(){
            $(".endDate").attr("disabled",false);
        });
		
		$("#confirmDate").on("click",function(){
			var $inputDate = $("#inputDate");
			var inputVal = $.trim($inputDate.val());
			if(inputVal == ""){
				$.Pop.alerts("天数不能为空。");
				//$elem.addClass("input-error");
				return false;
			}else if(!$.checkNumberIsInteger(inputVal)){
				//$elem.addClass("input-error");
				$.Pop.alerts("天数必须为正整数。");
				return false;
			}
			
			var displayInputVal = $inputDate.prev().html() + inputVal + $inputDate.next().html();
			$elem.val(displayInputVal);
            $elem.attr("dateRange",inputVal);
            $elem.attr("dateRangeUnit",'日');
            $elem.removeClass("input-error");
            $(".ui-datepicker-css").remove();
			$("#ui-datepicker-div").css("display","none");
		});
	},
	date:function(){
		$('.date').datepicker(
			$.extend({showMonthAfterYear:true}, $.datepicker.regional['zh-CN'],
            {'showAnim':'','dateFormat':'yy-mm-dd','changeMonth':'true','changeYear':'true',
                'showButtonPanel':'true'}
        ));
	},
	timeConfig : function(time){
		var time = "-" + time;
		//快捷菜单的控制
	    var nowDate = new Date();
	    timeStr = '至' + nowDate.getFullYear() + '-' + (nowDate.getMonth()+1) + '-' + nowDate.getDate();
	    nowDate.setDate(nowDate.getDate()+parseInt(time));
	    var endDateStr = nowDate.getFullYear() + '-'+  (nowDate.getMonth()+1) + '-' + nowDate.getDate();
	    if(time == -1){
	        endDateStr += '至' + endDateStr;
	    }else{
	        endDateStr += timeStr;
	    }
	    return endDateStr;
	},
	datePickers:function(elem){
		var $elem = $(elem);
		var $endDate = $elem.siblings(".endDate");
		var $startDate = $elem.siblings(".startDate");
		var $ui_datepicker_time = $elem.parentsUntil(".ui-datepicker-css").parent().prev();
		//自定义菜单
	    var startDate = $startDate.val();
	    var endDate = $endDate.val();
	    if(startDate && endDate){
	    	var startDateArr = startDate.split("-");
	    	var endDateArr = endDate.split("-");
	    	var startDateNum = startDateArr[0].toString() + startDateArr[1].toString() + startDateArr[2].toString();
	    	var endDateNum = endDateArr[0].toString() + endDateArr[1].toString() + endDateArr[2].toString();
	    	if(startDateNum <= endDateNum){
	    		var dateList = startDate +'至'+ endDate;
	    	    $ui_datepicker_time.val(dateList);
	    	    $ui_datepicker_time.attr("dateRange",startDate + ',' + endDate);
	    	    $ui_datepicker_time.attr("dateRangeUnit",'');
	    	    $ui_datepicker_time.removeClass("input-error");
	    	    $(".ui-datepicker-css").remove();
	    	}else{
	    		//$ui_datepicker_time.addClass("input-error");
	    		$.Pop.alerts("结束日期不能小于开始日期。");
	    	}
	    }else{
	    	//$ui_datepicker_time.addClass("input-error");
	    	$.Pop.alerts("请选择日期范围。");
	    }
	}
}