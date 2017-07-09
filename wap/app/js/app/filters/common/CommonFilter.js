define(['app'], function (app) {
  'use strict';
  app.filter('DateFormatDefault',function(){
	    return function(d){
	    	var formatStr = ""; 
	    	if(d != null){
	    		var sign = '-';
		    	var myDate = new Date(d);
		    	var fullYear = myDate.getFullYear();
		    	var month = myDate.getMonth()+1; 
		    	var date = myDate.getDate(); 
		    	var hours = myDate.getHours();       //获取当前小时数(0-23)
		    	var minutes = myDate.getMinutes();     //获取当前分钟数(0-59)
		    	var seconds = myDate.getSeconds();     //获取当前秒数(0-59)
		    	//myDate.getMilliseconds();    //获取当前毫秒数(0-999)
		    	if(sign == undefined){
		    		sign = '';
		    	}
		    	if(month < 10){
		    		month = '0' + month;
		    	}
		    	if(date < 10){
		    		date = '0' + date;
		    	}
		    	if(hours < 10){
		    		hours = '0' + hours;
		    	}
		    	if(minutes < 10){
		    		minutes = '0' + minutes;
		    	}
		    	if(seconds < 10){
		    		seconds = '0' + seconds;
		    	}
		    	formatStr = fullYear + sign + month + sign + date;
		    	//formatStr.ymd = fullYear + sign + month + sign + date;
		    	formatStr = formatStr + ' ' + hours + ':' + minutes + ':' + seconds;
	    	}
	    	return formatStr;
	    }
  });
  
  app.filter('DateFormatYYYYMMDD',function(){
	    return function(d){
	    	var formatStr = ""; 
	    	if(d != null){
	    		var sign = '-';
		    	var myDate = new Date(d);
		    	var fullYear = myDate.getFullYear();
		    	var month = myDate.getMonth()+1; 
		    	var date = myDate.getDate(); 
		    	//myDate.getMilliseconds();    //获取当前毫秒数(0-999)
		    	if(sign == undefined){
		    		sign = '';
		    	}
		    	if(month < 10){
		    		month = '0' + month;
		    	}
		    	if(date < 10){
		    		date = '0' + date;
		    	}
		    	
		    	formatStr = fullYear + sign + month + sign + date;
		    	//formatStr.ymd = fullYear + sign + month + sign + date;
	    	}
	    	return formatStr;
	    }
});
  
  app.filter('DictFormatter',function(){
	    return function(dicValue,params){
	    	var dicName = dicValue || "";
	        var args = Array.prototype.slice.call(arguments);  
	        var dicId = '';
	        if (args.length > 0 && args[1] != undefined) {//args[1]传入的字典参数
	            dicId = args[1];
	            var dictList = appConfig.dicMap[dicId];
	            if(dictList){
	            	for(var i = 0; i < dictList.length; i++){
		 	    		if(dictList[i].dicItemKey == dicValue){
		 	    			dicName = dictList[i].dicItemValue;
		 	    		}
		 	    	}
	            }
	 	    	return dicName;
	        }
	    }
	});
  
  	app.filter('trusted', ['$sce', function ($sce) {
	    return function(url) {
	        return $sce.trustAsResourceUrl(url);
	    };
	}]);
  	
  	app.filter('pageUrl', [function () {
	    return function(url) {
	        return url.substring(url.indexOf('url=') + 4);;
	    };
	}]);
	
	app.filter('taskCalStatusFormat', [function () {
  		return function(calStatus) {
  			var format = '';
			if(0 == calStatus){
				format = '未认领';
			}else if (1 == calStatus) {
  				format = '已认领';
			} else if(2 == calStatus) {
				format = '进行中';
			} else if(3 == calStatus) {
				format = '待审核';
			} else if(-1 == calStatus) {
				format = '申诉中 ';
			}
  			return format;
  		};
  	}]);
	
	app.filter('taskStatusFormat', [function () {
  		return function(status) {
  			var format = '';
			if (1 == status) {
  				format = '待推荐';
			} else if(2 == status) {
				format = '100人民币';
			} else if(3 == status) {
				format = '随机红包';
			}
  			return format;
  		};
  	}]);
	
	app.filter('orderStatusFormat', [function () {
  		return function(status) {
  			var format = '';
			if(0 == status){
				format = '未通过';
			}else if (1 == status) {
  				format = '审核中';
			} else if(2 == status) {
				format = '进行中';
			} else if(3 == status) {
				format = '重新编辑';
			} else if(4 == status) {
				format = '已结束 ';
			}else if(5 == status) {
				format = '未支付 ';
			}
  			return format;
  		};
  	}]);
  
});





