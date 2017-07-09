define(['app','ngload!restangular','app/services/common/CommonService'], function (app) {
  'use strict';
  app.factory('TaskService', ['Restangular', 'CommonService', '$q', function (Restangular, CommonService, $q) {
    return {
	    query : function(params) {
	    	return Restangular.all('/task/tasks').getList(params);			
		},
		queryMyTasks : function(params) {
	    	return Restangular.all('/task/tasks/my').getList(params);			
		},
		create : function(task) {
			return Restangular.all('/task/tasks').post(task, {}, {'Content-Type' : 'application/json'});
		},
		getById : function(id) {
			var random = CommonService.getRandomNum(1,100000000);
			return Restangular.one('/task/tasks', id).customGET("", {random: random});
		},
		update : function(task) {
			return task.put();
		},
		remove : function(task) {
			return Restangular.one('/task/tasks', task.id).customDELETE();
			//return task.remove();
		},
		uploadTaskSaoImages : function(files) {//上传图片
			return Restangular.all('/task/tasks/uploadFiles').post(files, {}, {'Content-Type' : 'application/json'});
		}
    };
  }]);
});

