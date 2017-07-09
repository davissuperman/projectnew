define(['angularAMD', 'app/services/order/OrderService', 'app/filters/common/CommonFilter','css!../../../../css/app/order/order'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location' ,'OrderService', function ($scope,$rootScope,$state,$location,OrderService) {    
		$scope.loadingNextPage = function(){
			$scope.queryOrderList();
		}
		
		$scope.queryOrderList = function(){
			$scope.isLoadingOrderList = true;
			var params = _.extend($scope.orderParams,$scope.orderFilter); 
			
			var response = {"total":14,"rows":[
				{
					id:'1',
					name:'邀请好友',
					money:88.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				},
				{
					id:'2',
					name:'端午节遇上儿童节',
					money:78.86,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				},
				{
					id:'3',
					name:'实用法宝帮您镇住大姨妈',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				},
				{
					id:'4',
					name:'粽多惊喜',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				},
				{
					id:'5',
					name:'邀请好友',
					money:78.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				,
				{
					id:'6',
					name:'甜蜜蜜',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				,
				{
					id:'7',
					name:'邀请好友',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				,
				{
					id:'8',
					name:'邀请好友',
					money:28.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				,
				{
					id:'9',
					name:'分享获取返现',
					money:38.55,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				,
				{
					id:'10',
					name:'邀请好友',
					money:28.8,
					des:'官方认证',
					remaining :555,
					forwardPeople:643
				}
				
			]}
			
			if(response && response.rows && _.isArray($scope.orderList) && _.isArray(response.rows)){
				$scope.orderList = _.union($scope.orderList,response.rows);
			}
			$scope.totalPage = Math.ceil(response.total / $scope.orderParams.rows); 
			$scope.orderParams.page++;
			
			/*$.layerLoading.show();
			OrderService.query(params).then(function(response) {
				$.layerLoading.hide();
				$scope.isLoadingOrderList = false;
			}, function(response) {
				$.layerLoading.hide();
				$scope.isLoadingOrderList = false;
				//$.Pop.alerts('网络繁忙');
			});*/
			$scope.isLoadingOrderList = false;
		}
		$scope.init = function(){
			$scope.totalPage = 0;
			$scope.orderParams = {
				page : 1,
				rows : 10
			};
			$scope.orderFilter = {
				
			};
			
			$scope.orderList = [];
			$scope.queryOrderList();
			$rootScope.isNeedFooterMenu = true;
			$scope.appPage = {
				title:'接单'
			};
		}
		$scope.init();
    }];
});
