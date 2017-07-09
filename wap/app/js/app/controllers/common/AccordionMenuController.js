define(['angularAMD'], function (angularAMD,nlsRoot) {
  'use strict';
	angularAMD.controller('AccordionMenuController', ['$scope','$rootScope', '$state','$http','$location', function ($scope,$rootScope, $state,$http,$location) {
	$scope.$on('$stateChangeSuccess', 
		function(event, toState, toParams, fromState, fromParams){
			$scope.currentUrl = $state.current.url || '';
	});
	
	$scope.scanImage = function(){
		//alert('扫图API');
	}
	
	$scope.init = function(){
		$scope.menuList = [{
			id:'task',
			name:'推广',
			state:'tasks'
		},{
			id:'order',
			name:'接单',
			state:'orders'
		},{
			id:'scan',
			name:'扫图',
			state:''
		},{
			id:'my',
			name:'我的',
			state:'my'
		}];
	}
	$scope.init();
}]);

angularAMD.directive('accordionMenu', function ($window) {
	return {
    	restrict: 'A',
      	controller: 'AccordionMenuController',
      	templateUrl: 'html/common/accordion.html?v='+appConfig.appVersion,
      	link: function(scope, elem, attrs) {
    	 
	  	}
    	};
  	});
});
