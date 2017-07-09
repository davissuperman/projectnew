define(['angularAMD', 'app/services/order/OrderService', 'app/filters/common/CommonFilter','css!../../../../css/app/order/order'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location' ,'OrderService', function ($scope,$rootScope,$state,$location,OrderService) {  
		$scope.goBackPage = function(){
			history.go(-1);
		}
		 
		$scope.showTaskExplainPage = function(){
			$("#btnShowTaskExplainPage").click();
		} 
		
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = false;
			$scope.appPage = {
				title:'任务详情'
			};
		}
		$scope.init();
    }];
});
