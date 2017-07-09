define([
	'app/states/task/TaskState',
	'app/states/order/OrderState',
	'app/states/my/MyState'
        ], function () {
  'use strict';
  var state = [];
  for(var i=0;i<arguments.length;i++) {
	  state = state.concat(arguments[i]);
  }
  return state;
});

