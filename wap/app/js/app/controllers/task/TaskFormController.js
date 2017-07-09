define(['angularAMD', 'app/services/task/TaskService', 'app/services/order/OrderService', 'app/filters/common/CommonFilter','css!../../../../css/app/task/task'], function (angularAMD) {
    'use strict';
    return ['$scope','$rootScope','$state', '$location','TaskService','OrderService' , function ($scope,$rootScope,$state,$location,TaskService,OrderService) {
		$scope.countDown = function(time){
			$scope.timeCount = time;
			$scope.startCountDown = true;
			if ($scope.timeCount<=0){
				$scope.startCountDown = false;
			} else {
				$scope.timeCount--;
				setTimeout (function(){
					$scope.$apply(function () {
						$scope.countDown($scope.timeCount);
					});
				}, 1000);
			}
		}
		
		$scope.sendYzCode = function(task){
			task = task || {};
			if(!task.phone){
				$.Pop.alerts('请输入手机号码');
				return false;
			}
			
			if(!/^1[3|4|5|7|8][0-9]\d{8}$/.test(task.phone)){
				$.Pop.alerts("请输入正确的手机号！");
				return;
			}
			$scope.countDown(60);
		} 
		
		$scope.showGuizePage = function(){
			$("#btnShowGuizePage").click();
		}
		
		$scope.showHowCopyHrefPage = function(){
			$("#btnShowHowCopyHrefPageDialog").click();
		}
		
		$scope.goPayPage = function(){
			$scope.taskPay = {
				money:$scope.task.payMoney,
				balance:0.00,
				needPayMoney:$scope.task.payMoney - 0,
				payWay:1
			};
			$("#btnShowPayPage").click();
		}
		
		$scope.createTask = function(task,formHorizontal) {
			$.layerLoading.show();
			TaskService.create(task).then(function(response) {
				$.layerLoading.hide();
				$scope.goPayPage();
			}, function(response) {
				$.layerLoading.hide();
				$.Pop.alerts('网络繁忙');
			});

		};
		
		
		$scope.saveTask = function(task,formHorizontal) {
			task = task || {};
			if(!task.href){
				$.Pop.alerts('请输入公众号文章链接');
				return false;
			}
			
			if(!(/^http(s?):\/\/mp\.weixin\.qq\.com/.test(task.href))){//http://mp.weixin.qq.com
    			$.Pop.alerts("请输入正确的公众号文章链接！");
    			return;
    		}
			
			if(!task.name){
				$.Pop.alerts('请输入公众号文章标题');
				return false;
			}
			
			if(task.timeType == 1){
				task.startTime = $.getDateStr(0);
				task.endTime = $.getDateStr(2);
			}
			
			if(!task.phone){
				$.Pop.alerts('请输入手机号码');
				return false;
			}
			
			if(!/^1[3|4|5|7|8][0-9]\d{8}$/.test(task.phone)){
				$.Pop.alerts("请输入正确的手机号！");
				return;
			}
			
			if(!task.yzcode){
				$.Pop.alerts('请输入验证码');
				return false;
			}
			
			$scope.createTask(task,formHorizontal);
		};
		
		$scope.savePay = function(taskPay,formHorizontal){
			taskPay = taskPay || {};
			$.layerLoading.show();
			OrderService.pay(taskPay).then(function(response) {
				$.layerLoading.hide();
				
			}, function(response) {
				$.layerLoading.hide();
				$.Pop.alerts('网络繁忙');
			});
		}
		
		$scope.checkTaskNumber = function(){
			if(!$scope.task.number || $scope.task.number < 1000){
				$scope.task.number = 1000;
			}
			$scope.calPayMoney();
		}
		
		$scope.calPayMoney = function(){
			$scope.task.payMoney = $scope.task.number / 10;
		}
		
		$scope.initAppPage = function(){
			$scope.appPage = {
				title:'发布任务'
			};
		}
		
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = false;
			$scope.task = {
				number:1000,
				payMoney:100,
				timeType:1,
				startTime : $.getDateStr(0),//Number(new Date().getTime()),
				endTime : $.getDateStr(2)//Number(new Date().getTime())+3600*48,
			};
			$scope.initAppPage();
		}
		$scope.init();
    }];
});
