define(['angularAMD', 'app/services/my/MyService', 'app/services/task/TaskService', 'app/filters/common/CommonFilter','css!../../../../css/app/my/my'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location','MyService','TaskService', function ($scope,$rootScope,$state,$location,MyService,TaskService) {    
		$scope.loadingNextPage = function(){
			$scope.queryTaskList();
		}
		
		$scope.queryTaskList = function(){
			$scope.isLoadingTaskList = true;
			var params = _.extend($scope.taskParams,$scope.Filter); 
			
			var response = {"total":14,"rows":[
				{
					id:'1',
					type:'wen',
					status:1,
					calStatus:0,
					name:'邀请好友',
					money:88.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'2',
					type:'wen',
					status:2,
					calStatus:1,
					name:'端午节遇上儿童节',
					money:78.86,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'3',
					type:'tu',
					status:3,
					calStatus:2,
					name:'实用法宝帮您镇住大姨妈',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'4',
					type:'wen',
					status:3,
					calStatus:3,
					name:'粽多惊喜',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				},
				{
					id:'5',
					type:'tu',
					status:3,
					calStatus:-1,
					name:'邀请好友',
					money:78.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'6',
					type:'wen',
					status:3,
					calStatus:0,
					name:'甜蜜蜜',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'7',
					type:'wen',
					status:3,
					calStatus:1,
					name:'邀请好友',
					money:78.80,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'8',
					type:'tu',
					status:3,
					calStatus:3,
					name:'邀请好友',
					money:28.85,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'9',
					type:'wen',
					status:1,
					calStatus:0,
					name:'分享获取返现',
					money:38.55,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				,
				{
					id:'10',
					type:'wen',
					status:1,
					calStatus:1,
					name:'邀请好友',
					money:28.8,
					des:'官方认证',
					remaining :555,
					forwardPeople:643,
					startTime:'2017-05-27',
					endTime:'2017-06-27'
				}
				
			]}
			
			if(response && response.rows && _.isArray($scope.taskList) && _.isArray(response.rows)){
				$scope.taskList = _.union($scope.taskList,response.rows);
			}
			$scope.totalPage = Math.ceil(response.total / $scope.taskParams.rows); 
			$scope.taskParams.page++;
			
			/*$.layerLoading.show();
			TaskService.queryMyTasks(params).then(function(response) {
				$.layerLoading.hide();
				$scope.isLoadingTaskList = false;
			}, function(response) {
				$.layerLoading.hide();
				$scope.isLoadingTaskList = false;
				//$.Pop.alerts('网络繁忙');
			});*/
			$scope.isLoadingTaskList = false;
		}
		$scope.init = function(){
			$scope.totalPage = 0;
			$scope.taskParams = {
				page : 1,
				rows : 10
			};
			$scope.Filter = {
				
			};
			$scope.taskList = [];
			$scope.queryTaskList();
			$rootScope.isNeedFooterMenu = false;
			$scope.appPage = {
				title:'我的任务'
			};
		}
		$scope.init();
    }];
});
