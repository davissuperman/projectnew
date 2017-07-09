require.config({
  baseUrl: 'js/',  
  waitSeconds: 0,
  // alias libraries paths.  Must set 'angular'
  paths: {
	'jquery': 'libs/jquery/jquery-1.12.3.min',
	//'swiper': 'libs/swiper/swiper.jquery.min',
    'angular': 'libs/angular/angular.min',  
	//'angular-locale_zh': 'libs/angular-locale/angular-locale_zh',  
	'angular-ui-router': 'libs/angular-ui/angular-ui-router/angular-ui-router.min', 
    'angularAMD': 'libs/angularAMD/angularAMD',
    'ngload': 'libs/angularAMD/ngload',
    'restangular': 'libs/restangular/restangular',
    'underscore': "libs/restangular/underscore",
    'lodash': "libs/restangular/lodash.min",
    'ng-table': "libs/ng-table/ng-table",
	'angular-strap': 'libs/angular-strap/angular-strap.min',
    'customUtilJS': 'customUtilJS'
  },
  
  map: {
      '*': {
          'css': 'libs/require-css/css.min'
      }
  },

  // Add angular modules that does not support AMD out of the box, put it in a shim
  shim: {
	'angular' : {
		exports: 'angular'
	},
	//'angular-locale_zh' : [ 'angular' ],
	'angular-ui-router' : [ 'angular' ],
	'jquery' : ['angular'],
	//'swiper':['jquery'],
    'angularAMD': [ 'angular' ],
    'ngload': [ 'angularAMD' ],
    'restangular': ['angular', 'underscore'],
    'ng-table':{
    	deps: [
    	    'angular'
   		]
    },
	'angular-strap':['angular'],
    'customUtilJS': [ 'jquery' ]
  },

  // kick start application
  deps: ['app']
});

