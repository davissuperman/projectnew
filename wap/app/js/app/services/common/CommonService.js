define(['app', 'ngload!restangular'], function (app) {
  'use strict';
  app.factory('CommonService', ['Restangular', function (Restangular) {
    return {
    	buildQueryParams : function(params) {
    		var random = this.getRandomNum(1,100000000);
	    	var queryObj = {
	    		page : params.page(),
	    		rows : params.count(),	
	    		random : random 
	    	};
	    	angular.extend(queryObj, params.filter());
	    	return queryObj;			
		},
		getRandomNum :function(min,max){   
			var range = max - min;   
			var rand = Math.random();   
			return(min + Math.round(rand * range)); 
		},
		getDict : function(dicName) {
    		var queryObj = {
    				dicName : dicName
    		};
			return Restangular.all('/admin/dics/dicList').getList(queryObj);
			
    	}
    };
  }]);
});

