<?php
/**
 * 程云活动创建方法
 *
 */
class ActivityAction extends BaseAction {
	public $token;
	public $wecha_id;
	public function __construct() {
		parent::__construct ();
		$this->token = $this->_get ( 'token' );
		$this->assign ( 'token', $this->token );
		$this->wecha_id = $this->_get ( 'wecha_id' );
		if (! $this->wecha_id) {
			$this->wecha_id = '';
		}
		$this->assign ( 'wecha_id', $this->wecha_id );
		$this->assign ( 'staticJSPath', str_replace ( './', '/', THEME_PATH . 'common/js/activity' ) );
		$this->assign ( 'staticImagesPath', str_replace ( './', '/', THEME_PATH . 'common/images/activity' ) );
		$this->assign ( 'staticCssPath', str_replace ( './', '/', THEME_PATH . 'common/css/activity' ) );
	}
	/**
	 * 提交信息入口
	 *
	 */
	public function index() {
		$this->assign ( 'URL', $_SERVER ['REQUEST_URI'] );
		$this->display ();
	
	}
	public function alist() {
		$this->display ();
	}
	//ajax提交活动信息
	public function ajax() {
		if (IS_POST) { //如果ajax提交了
			$subject = $_POST ['subject'];
			$message = $_POST ['message'];
			$username = $_POST ['username'];
			$password = $_POST ['password'];
		$this->token = $_POST ['token'];
		$this->wecha_id = $_POST ['wecha_id'];
			/**
			$fo = fopen ( 'd:/t.txt', 'a' );
			fwrite ( $fo, $subject );
			fwrite ( $fo, $message );
			fwrite ( $fo, $username );
			fwrite ( $fo, $password );
			fwrite ( $fo, $this->token );
			fwrite ( $fo, $this->wecha_id );
			fclose ( $fo );
			 */
			$row ['subject'] = $subject;
			$row ['message'] = $message;
			$row ['username'] = $username;
			$row ['password'] = $password;
			$row ['open_id'] = $this->wecha_id;
			$row ['token'] = $this->token;
			$row ['regdate'] = time ();
			$id = M ( 'activity' )->add ( $row );
			echo U ( 'Activity/view', array ('token' => $this->token, 'wecha_id' => $this->wecha_id, 'aid' => $id ) );
			exit ();
		}
	
	}
	/*
	 * view 页面
	 */
	public function view() {
		$aid = intval ( $_GET ['aid'] );
		M ( 'activity' )->where ( array ('aid' => $aid ) )->setInc ( 'views' );
		$thisForm = M ( 'activity' )->where ( array ('aid' => $aid ) )->find ();
		$thisUserList = M ( 'activity_users' )->where ( array ('aid' => $aid ) )->select ();
		$thisForm ['usersize'] = count ( $thisUserList );
		$thisForm ['regdate'] = $this->tranTime ( $thisForm ['regdate'] );
		foreach ( $thisUserList as $key => $value ) {
			$thisUserList [$key] ['regdate'] = $this->tranTime ( $thisUserList [$key] ['regdate'] );
		}
		$this->assign ( 'thisForm', $thisForm ); //活动信息
		$this->assign ( 'thisUserList', $thisUserList ); //用户列表	
		$this->display ();
	
	}
	//验证密码
	public function viewajax() {
		if (IS_POST) { //如果ajax提交了
			$aid = $_POST ['aid'];
			$password = $_POST ['password'];
			$thisForm = M ( 'activity' )->field ( 'password' )->where ( array ('aid' => $aid ) )->find ();
			/*
			$fo = fopen ( 'd:/a.txt', 'a' );
			fwrite ( $fo, $aid );
			fwrite ( $fo, 'in=' . $password . '---thisfrom' . $thisForm ['password'] );
			
			fclose ( $fo );
			//
			*/
			
			if ($thisForm && ($thisForm ['password'] == $password)) {
				exit ( '1' );
			} else {
				exit ( '0' );
			}
		}
	}
	//报名
	public function submitajax() {
		$aid = $_POST ['aid'];
		$username = $_POST ['username'];
		$tel = $_POST ['tel'];
		$open_id = $_POST ['open_id'];
		$row ['aid'] = $aid;
		$row ['username'] = $username;
		$row ['tel'] = $tel;
		$row ['open_id'] = $open_id;
		$row ['regdate'] = time ();
		$id = M ( 'activity_users' )->add ( $row );
		if ($id) {
			exit ( '1' );
		
		} else {
			exit ( '0' );
		}
	}
	
	public function tranTime($time) {
		$rtime = date ( "m-d H:i", $time );
		$htime = date ( "H:i", $time );
		$time = time () - $time;
		if ($time < 60) {
			$str = '刚刚';
		} elseif ($time < 60 * 60) {
			$min = floor ( $time / 60 );
			$str = $min . '分钟前';
		} elseif ($time < 60 * 60 * 24) {
			$h = floor ( $time / (60 * 60) );
			$str = $h . '小时前 ';
		} elseif ($time < 60 * 60 * 24 * 3) {
			$d = floor ( $time / (60 * 60 * 24) );
			if ($d == 1)
				$str = '昨天 ';
			else
				$str = '前天 ';
		} else {
			$str = $rtime;
		}
		return $str;
	}

}

?>