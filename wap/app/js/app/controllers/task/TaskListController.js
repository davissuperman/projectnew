define(['angularAMD'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location' , function ($scope,$rootScope,$state,$location) {    
		
		$scope.initTaskList = function(){
			
		}
		
		$scope.initAppPage = function(){
			$scope.appPage = {
				title:'推广'
			};
		}
		
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = true;
			$scope.initAppPage();
			//$scope.initTaskList();
		}
		$scope.init();
    }];
});
