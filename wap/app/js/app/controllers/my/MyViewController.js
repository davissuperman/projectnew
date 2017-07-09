define(['angularAMD', 'app/filters/common/CommonFilter','css!../../../../css/app/my/my'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location' , function ($scope,$rootScope,$state,$location) {    
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = false;
			$scope.appPage = {
				title:'个人信息'
			};
		}
		$scope.init();
    }];
});
