define([ 'common', 'app/states/common/state'], function(angularAMD, state) {
	'use strict';
	var app = angular.module('myApp', [ 'ui.router',
			 'mgcrea.ngStrap','restangular']);
	app.config([
			'$stateProvider',
			'$urlRouterProvider',
			'RestangularProvider',
			function($stateProvider, $urlRouterProvider, RestangularProvider) {
				//console.dir(['state', state]);
				angular.forEach(state, function(item) {
					$stateProvider.state(item.state, angularAMD.route(item));
                });
				
				// // Else
				$urlRouterProvider.otherwise('/task/tasks');

//				// set base url
				var urlMap = angular.copy(appConfig.urlMap);
				if(urlMap && urlMap.dmpUrl){
					RestangularProvider.setBaseUrl(urlMap.dmpUrl);
				}else{
					RestangularProvider.setBaseUrl('/dmp-web');
				}

				// add a response interceptor
				RestangularProvider.addResponseInterceptor(function(data,operation, what, url, response, deferred) {

					var extractedData;
					// .. to look for getList operations
					if (operation === "getList") {
						// .. and handle the data and meta data
						
						if(data.rows){
							extractedData = data.rows;
							extractedData.total = data.total;
						}else{
							extractedData = data;
						}
						
					} else {
						extractedData = data;
					}
					return extractedData;
				});

			} ]);
	
	var result = angularAMD.bootstrap(app);

	return result;
});

