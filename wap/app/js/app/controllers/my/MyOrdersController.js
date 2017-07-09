define(['angularAMD', 'app/services/my/MyService', 'app/services/order/OrderService', 'app/filters/common/CommonFilter','css!../../../../css/app/my/my'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location','MyService','OrderService' , function ($scope,$rootScope,$state,$location,MyService,OrderService) { 
		$scope.payOrder = function(){
			$.layerLoading.show();
			var order = $scope.myOrderDetails;
			OrderService.pay(order).then(function(response){
				$.layerLoading.hide();
			},function(response) {
				$.layerLoading.hide();
				if(response.data != null && !response.data.success){
					$.Pop.alerts(response.data.msg);
				}
			});
		}
		 
		$scope.removeOrder = function(){
			$.Pop.confirms('确定要删除订单？',function(){
				var order = $scope.myOrderDetails;
				$.layerLoading.show();
				OrderService.remove(order).then(function(response){
					$.layerLoading.hide();
				},function(response) {
					$.layerLoading.hide();
					if(response.data != null && !response.data.success){
						$.Pop.alerts(response.data.msg);
					}
				});
			});
		}
	
		$scope.showMyOrderDialog = function(order){
			$("#btnShowMyOrderDialog").click();
			$scope.myOrderDetails = order;
			$.layerLoading.show();
			OrderService.getById(order.id).then(function(response) {
				$.layerLoading.hide();
				$scope.myOrderDetails = response;
			}, function(response) {
				$.layerLoading.hide();
				//$.Pop.alerts('网络繁忙');
			});
		}
	  
		$scope.loadingNextPage = function(){
			$scope.queryOrderList();
		}
		
		$scope.queryOrderList = function(){
			$scope.isLoadingOrderList = true;
			var params = _.extend($scope.orderParams,$scope.orderFilter); 
			
			var response = {"total":14,"rows":[
				{
					id:'1',
					code:'20999999999',
					type:'wen',
					status:1,
					calStatus:0,
					name:'邀请好友',
					money:88.85,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'2',
					code:'20999999999',
					type:'wen',
					status:2,
					calStatus:1,
					name:'端午节遇上儿童节',
					money:78.86,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'3',
					code:'20999999999',
					type:'tu',
					status:3,
					calStatus:2,
					name:'实用法宝帮您镇住大姨妈',
					money:78.80,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'4',
					code:'20999999999',
					type:'wen',
					status:0,
					calStatus:3,
					name:'粽多惊喜',
					money:78.80,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'5',
					code:'20999999999',
					type:'tu',
					status:4,
					calStatus:-1,
					name:'邀请好友',
					money:78.85,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'6',
					code:'20999999999',
					type:'wen',
					status:5,
					calStatus:0,
					name:'甜蜜蜜',
					money:78.80,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'7',
					code:'20999999999',
					type:'wen',
					status:3,
					calStatus:1,
					name:'邀请好友',
					money:78.80,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'8',
					code:'20999999999',
					type:'tu',
					status:3,
					calStatus:3,
					name:'邀请好友',
					money:28.85,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'9',
					code:'20999999999',
					type:'wen',
					status:1,
					calStatus:0,
					name:'分享获取返现',
					money:38.55,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'10',
					code:'20999999999',
					type:'wen',
					status:1,
					calStatus:1,
					name:'邀请好友',
					money:28.8,
					des:'官方认证',
					number :555,
					forwardPeople:643,
					time:'2017-05-27 00:22:22',
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				
			]}
			
			if(response && response.rows && _.isArray($scope.orderList) && _.isArray(response.rows)){
				$scope.orderList = _.union($scope.orderList,response.rows);
			}
			$scope.totalPage = Math.ceil(response.total / $scope.orderParams.rows); 
			$scope.orderParams.page++;
			
			/*$.layerLoading.show();
			OrderService.queryMyOrders(params).then(function(response) {
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
			$rootScope.isNeedFooterMenu = false;
			$scope.appPage = {
				title:'我的订单'
			};
		}
		$scope.init();
    }];
});
