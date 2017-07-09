define(['app','angularAMD'], function (app,angularAMD) {
    'use strict';
    angularAMD.directive('tdStringHtml', ['$parse',function($parse) {
    	return {
    		restrict : 'EA',
    		
    		scope:{
    			struct:"=",
    			htmlCallback:"&"
    		},
    		link: function(scope, elem, attrs) {
				scope.$watch('struct',function(html) {
					elem.html(html || '');
    			},true);
				
				elem.bind('click',function(){
					if(angular.isFunction(scope.htmlCallback)){
    	            	scope.htmlCallback()(scope.struct);
    	            }
				});
		    }
    	}
    }]);
});

