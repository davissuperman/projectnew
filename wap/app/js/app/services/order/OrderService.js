define(['app','ngload!restangular','app/services/common/CommonService'], function (app) {
  'use strict';
  app.factory('OrderService', ['Restangular', 'CommonService', '$q', function (Restangular, CommonService, $q) {
    return {
	    query : function(params) {
	    	return Restangular.all('/order/orders').getList(params);			
		},
		create : function(task) {
			return Restangular.all('/order/orders').post(task, {}, {'Content-Type' : 'application/json'});
		},
		getById : function(id) {
			var random = CommonService.getRandomNum(1,100000000);
			return Restangular.one('/order/orders', id).customGET("", {random: random});
		},
		update : function(order) {
			return order.put();
		},
		remove : function(order) {
			return Restangular.one('/order/orders', order.id).customDELETE();
		},
		pay : function(order) {//支付订单
			return Restangular.all('/order/orders/pay').post(order, {}, {'Content-Type' : 'application/json'});
		},
    };
  }]);
});

