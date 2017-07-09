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
		
		$scope.validTaskForm = function(task){
			var isValid = true;
			task = task || {};
			
			if($scope.taskSaoPage.step == 1){
				if(task.uploadedImages.length == 0){
					$.Pop.alerts('请上传您的宣传海报');
					isValid = false;
				}else if(!task.href){
					$.Pop.alerts('请输入推广链接');
					isValid = false;
				}else if(!(/^http(s?):\/\//.test(task.href))){//http://mp.weixin.qq.com
					$.Pop.alerts("请输入正确的推广链接");
					isValid = false;
				}
			}else if($scope.taskSaoPage.step == 2){
				if(task.timeType == 1){
					task.startTime = $.getDateStr(0);
					task.endTime = $.getDateStr(2);
				}
				
				if($scope.task.payMoney < 100){
					$.Pop.alerts('活动总金额不能低于100元');
					isValid = false;
				}else if(!task.phone){
					$.Pop.alerts('请输入手机号码');
					isValid = false;
				}else if(!/^1[3|4|5|7|8][0-9]\d{8}$/.test(task.phone)){
					$.Pop.alerts("请输入正确的手机号！");
					isValid = false;
				}else if(!task.yzcode){
					$.Pop.alerts('请输入验证码');
					isValid = false;
				}
			}
			
			return isValid;
		};
		
		$scope.saveTask = function(task,formHorizontal) {
			var isValid = $scope.validTaskForm(task);
			if(isValid){
				$scope.createTask(task,formHorizontal);
			}
		};
		
		$scope.checkTaskNumber = function(){
			if($scope.task.rewardType == 1){
				var number = $scope.task.payMoney / 2;
				if($scope.task.number > number){
					$scope.task.number = number;
				}
			}else if($scope.task.rewardType == 2){
				$scope.task.payMoney = $scope.task.paySingleMoney * $scope.task.number;
			}
			
		}
		
		$scope.checkPaySingleMoney = function(){
			if(!$scope.task.paySingleMoney || $scope.task.paySingleMoney < 2){
				$scope.task.paySingleMoney = 2;
			}
			$scope.task.payMoney = $scope.task.paySingleMoney * $scope.task.number;
			
		}
		
		$scope.calPayMoney = function(){
			if(!$scope.task.payMoney || $scope.task.payMoney < 100){
				$scope.task.payMoney = 100;
			}
		}
		
		$scope.changeRewardType = function(){
			$scope.checkTaskNumber();
		}
		
		$scope.prevStepTask = function(){
			$scope.taskSaoPage.step--;
		}
		
		$scope.nextStepTask = function(task,formHorizontal) {
			var isValid = $scope.validTaskForm(task);
			if(isValid){
				$scope.taskSaoPage.step++;
			}
		}
		
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
		
		$scope.removeUploadedImage = function($index){
			var uploadedImages = $scope.task.uploadedImages;
			if(uploadedImages && uploadedImages.length > 0){
				for(var i = 0;i < uploadedImages.length;i++){
					if(i == $index){
						uploadedImages.splice(i, 1);
						break;
					}
				}
			}
		}
		
		$scope.uploadSelectedImagesApi = function(files) {
			$.layerLoading.show();
			TaskService.uploadTaskSaoImages(files).then(function(response) {
				$.layerLoading.hide();
			}, function(response) {
				$.layerLoading.hide();
				$.Pop.alerts('网络繁忙');
			});

		};
		
		/*预览图片*/
    	$scope.previewImages = function(files){
			if(files && files.length > 0){
				for(var i = 0 ;i < files.length;i++){
					var file = files[i];
					if (file && !file.$error){
						var reader = new FileReader();
						$.layerLoading.show();
						reader.onload = function(evt){
							$.layerLoading.hide();
							$scope.$apply(function (){
								$scope.task.uploadedImages.push({
									dataImg:evt.target.result
								});
								
							});
						}
						reader.readAsDataURL(file);
					}
				}
			}
            
    	}
		
		$scope.uploadSelectedImages = function(element){
			//console.dir([$scope.task]);
			//console.dir(element.files);
			var files = element.files;
			var filesNum = files.length;
			var totalNum = $scope.task.uploadedImages.length + filesNum;  //总的数量
			if(filesNum > 5 || totalNum > 5){
				$.Pop.alerts('最多只能上传5张广告图片');
				return false;
			}
			$scope.previewImages(files);//预览图片
			$scope.uploadSelectedImagesApi(files);//上传图片
			
		}

		$scope.initAppPage = function(){
			$scope.appPage = {
				title:'任务要求'
			};
		}
		
		$scope.init = function(){
			$rootScope.isNeedFooterMenu = false;
			$scope.taskSaoPage = {
				step:1
			};
			$scope.selectedImages = [];
			$scope.task = {
				needForward:1,//需要转发
				uploadedImages : [],//已经上传的图片,
				rewardType:1,//奖励方式
				number:50,
				paySingleMoney:2,
				payMoney:100,
				timeType:1,
				startTime : $.getDateStr(0),
				endTime : $.getDateStr(2)
			};
			$scope.initAppPage();
		}
		$scope.init();
    }];
});
