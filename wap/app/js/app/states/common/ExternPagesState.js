define([], function () {
  'use strict';
  return [
     {
	    state : 'admins_ep',
		url : '/admin/tenant/ep',
		templateUrl : 'html/common/externPages.html?v=' + appConfig.appVersion,
		controllerUrl : 'app/controllers/common/ExternPagesController',
		ncyBreadcrumb: {
		    label: '权限管理',
		    parent: 'admin'
		}
	}];
});

