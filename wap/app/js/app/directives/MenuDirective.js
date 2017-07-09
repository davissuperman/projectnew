define(['app','angularAMD'], function (app,angularAMD) {
    'use strict';
    angularAMD.directive('menuDirective',['$state','$stateParams', function($state,$stateParams) {
    	return {
    		restrict : 'EA',
    		scope:{
    			menuList:"=",
    		},
    		controller: function($scope,$rootScope, $element){
    	    	
    		},  
    		templateUrl : "html/common/MenuDirective.html?v=" + appConfig.appVersion,
    		link: function(scope, elem, attrs) {
    			
		    }
    	}
    }]);
});
