define([], function () {
  'use strict';
  return [{
	    state : 'my',
		url : '/my/index',
		templateUrl : 'html/my/list/MyIndex.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/my/MyIndexController'
		
	}, {
		state : 'my_view',
		url : '/my/view',
		templateUrl : 'html/my/view/MyView.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/my/MyViewController'
		
	}, {
		state : 'my_orders',
		url : '/my/orders',
		templateUrl : 'html/my/list/MyOrders.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/my/MyOrdersController'
		
	}, {
		state : 'my_tasks',
		url : '/my/tasks',
		templateUrl : 'html/my/list/MyTasks.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/my/MyTasksController'
		
	}];
});

