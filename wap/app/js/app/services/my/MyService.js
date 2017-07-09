define(['app','ngload!restangular','app/services/common/CommonService'], function (app) {
  'use strict';
  app.factory('MyService', ['Restangular', 'CommonService', '$q', function (Restangular, CommonService, $q) {
    return {
	    getById : function(id) {
			var random = CommonService.getRandomNum(1,100000000);
			return Restangular.one('/my/detail', id).customGET("", {random: random});
		},
    };
  }]);
});

