define([], function () {
  'use strict';
  return [{
	    state : 'tasks',
		url : '/task/tasks',
		templateUrl : 'html/task/list/TaskList.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/task/TaskListController'
		
	}, {
		state : 'task_form',
		url : '/task/tasks/form/new',
		templateUrl : 'html/task/form/TaskForm.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/task/TaskFormController'
		
	}, {
		state : 'task_sao_form',
		url : '/task/tasks/sao/form/new',
		templateUrl : 'html/task/form/TaskSaoForm.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/task/TaskSaoFormController'
		
	}, {
		state : 'task_view',
		url : '/task/tasks/view/:taskId',
		templateUrl : 'html/task/view/TaskView.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/task/TaskViewController'
		
	}];
});

