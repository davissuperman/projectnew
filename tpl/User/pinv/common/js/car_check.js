function form_check(){
	var thisForm = document.getElementById('check-form');
	//必填表单验证
	var requireInputs = thisForm.getElementsByClassName('required');
	for( var i = 0;i < requireInputs.length;i++ ){
		if( requireInputs[i].value == ''){
			alert( requireInputs[i].getAttribute('title')+'必填！' );
			requireInputs[i].focus();
			return false;
		}
	}

	//车系必填验证
	var brand = document.getElementById('brand');
	if( brand != null && brand != undefined  ){
		var series = document.getElementById('series');
		if( series == null || series == undefined){
			alert('必须选择车系！');
			return false;
		}
	}

	var mobile = document.getElementById('mobile').value;
	var preg_m = /^0{0,1}1[3,4,5,8][0-9]{9}$/;
	if( mobile ){
		if ( !preg_m.test(mobile) ){
			alert('电话号码格式不正确！');
			return false;
		}
	}
}