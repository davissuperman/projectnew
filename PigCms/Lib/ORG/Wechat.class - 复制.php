<?php
/**
 * 微信收发入口
 *
 */
class Wechat {
	private $data = array ();
	/**
	 * 程云添加注释 本方法是验证token 然后扥到输入数据流 解析xml
	 *
	 * @param unknown_type $token
	 */
	public function __construct($token) {		
		//$this->auth ( $token ) || exit ();// 正式部署后要开启
		if (IS_GET) {			
			echo ($_GET ['echostr']);		
			exit ();
		} else {
		
			$xml = file_get_contents ( "php://input" );
			/**
			 * 程云添加对输入数据写入到本地文件
			 */
			//$fo=fopen(dirname(__FILE__).'/b.txt','a');
			//fwrite($fo,$xml);
			//fclose($fo);
			//end
			$xml = new SimpleXMLElement ( $xml );
			$xml || exit ();
			foreach ( $xml as $key => $value ) {
				$this->data [$key] = strval ( $value );
			}
		}
	}
	/**
	 * 接收发来的数据
	 *
	 * @return unknown
	 */
	public function request() {
		return $this->data;
	}
	/**
	 * 发送给微信
	 *
	 * @param unknown_type $content
	 * @param unknown_type $type
	 * @param unknown_type $flag
	 */
	public function response($content, $type = 'text', $flag = 0) {
		$this->data = array ('ToUserName' => $this->data ['FromUserName'], 'FromUserName' => $this->data ['ToUserName'], 'CreateTime' => NOW_TIME, 'MsgType' => $type );
		$this->$type ( $content );
		$this->data ['FuncFlag'] = $flag;
		$xml = new SimpleXMLElement ( '<xml></xml>' );
		$this->data2xml ( $xml, $this->data );
		///
		//$fo=fopen(dirname(__FILE__).'/'.$this->data ['ToUserName'].'.txt','a');
		//fwrite($fo,$xml->asXML ());
		//fclose($fo);
		///end
		exit ( $xml->asXML () );
	}
	/*
	 * 回复文本消息MsgType	 text
	 */
	private function text($content) {
		$this->data ['Content'] = $content;
	}
	/**
	 * 回复音乐消息
	 * MsgType	= music
	 *
	 * @param unknown_type $music
	 */
	private function music($music) {
		list ( $music ['Title'], $music ['Description'], $music ['MusicUrl'], $music ['HQMusicUrl'] ) = $music;
		$this->data ['Music'] = $music;
	}
	/**
	 * 回复图文消息 MsgType=	 news
	 *
	 * @param unknown_type $news
	 */
	private function news($news) {
		$articles = array ();
		foreach ( $news as $key => $value ) {
			list ( $articles [$key] ['Title'], $articles [$key] ['Description'], $articles [$key] ['PicUrl'], $articles [$key] ['Url'] ) = $value;
			if ($key >= 9) {
				break;
			}
		}
		$this->data ['ArticleCount'] = count ( $articles );
		$this->data ['Articles'] = $articles;
	}
	/**
	 * 
	 *封装微信xml
	 * @param unknown_type $xml
	 * @param unknown_type $data
	 * @param unknown_type $item
	 */
	private function data2xml($xml, $data, $item = 'item') {
		foreach ( $data as $key => $value ) {
			is_numeric ( $key ) && $key = $item;
			if (is_array ( $value ) || is_object ( $value )) {
				$child = $xml->addChild ( $key );
				$this->data2xml ( $child, $value, $item );
			} else {
				if (is_numeric ( $value )) {
					$child = $xml->addChild ( $key, $value );
				} else {
					$child = $xml->addChild ( $key );
					$node = dom_import_simplexml ( $child );
					$node->appendChild ( $node->ownerDocument->createCDATASection ( $value ) );
				}
			}
		}
	}
	/**
	 * 程云添加注释 这里是接入验证
	 *
	 * @param unknown_type $token
	 * @return unknown
	 */
	private function auth($token) {
		$data = array ($_GET ['timestamp'], $_GET ['nonce'], $token );
		$sign = $_GET ['signature'];
		sort ( $data );
		$signature = sha1 ( implode ( $data ) );
			//
		//$fo = fopen ( dirname ( __FILE__ ) . '/token.txt', 'a' );
		//fwrite ( $fo, $sign );
		//fwrite ( $fo, '==' );
		//fwrite ( $fo, $signature );
		//fclose ( $fo );
		//
		if ($signature == $sign) {
			return true;
		} else {
			return false;
		}
		//return $signature === $sign;
	}
}
?>