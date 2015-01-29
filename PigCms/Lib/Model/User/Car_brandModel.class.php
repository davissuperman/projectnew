<?php
class Car_brandModel extends Model{
	protected $_validate = array(
			array('brand','require','区域名称不能为空'),
			array('url','require','品牌官网不能为空')
	 );
	protected $_auto = array (		
		array('token','getToken',Model:: MODEL_BOTH,'callback')
	);
	function getToken(){	
		return $_SESSION['token'];
	}
}

?>
