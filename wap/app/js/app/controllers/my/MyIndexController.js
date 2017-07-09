define(['angularAMD', 'app/filters/common/CommonFilter','css!../../../../css/app/my/my'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location' , function ($scope,$rootScope,$state,$location) {    
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = true;
			$scope.appPage = {
				title:'我的'
			};
		}
		$scope.init();
    }];
});
