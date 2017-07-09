define([], function () {
  'use strict';
  return [{
	    state : 'orders',
		url : '/order/orders',
		templateUrl : 'html/order/list/OrderList.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/order/OrderListController'
		
	}, {
		state : 'order_view',
		url : '/order/orders/view/:orderId',
		templateUrl : 'html/order/view/OrderView.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/order/OrderViewController'
		
	}];
});

