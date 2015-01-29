<?php
class IndexAction extends BaseAction{
	//关注回复
	public function index(){
		
		$this->display();
	}
	
	public function test () {
		/*$like ['keyword'] = array('like', "%會%");
        $like ['token'] = 'rlyqwj1381634152';
		$datas = M('keyword')->where($like)->order('id desc')->select();
		
		//檢查完全或模糊匹配
		foreach ($datas as $data)
		{
			$info = M($data ['module'])->order('id desc')->find($data ['pid']);
			if ($info['type'] == "1") //完全
			{
				if (!strcmp("會員", $info ['keyword'])) 
				{
					echo $info['text'];
				}
			}
			else //模糊
			{
			
			}
		}*/
		
		$like ['keyword'] = array('like', "%輝哥%");
        $like ['token'] = 'rlyqwj1381634152';
		$img_db = M('img');
		$back = $img_db->field('id,keyword,type,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
		$idsWhere = 'id in (';
		$comma = '';
		//var_dump($back);
		foreach ($back as $keya => $infot) {
			$isMatch = true;
			$keywords = explode(' ', $infot ['keyword']);
			
			foreach ($keywords as $keyword) {

				if ($infot['type'] == "2") //完全
				{
					if (!strcmp("輝哥", $keyword)) 
					{
						$isMatch = true;
						break;
					}
					else
					{
						$isMatch = false;
					}
				}
				else
				{
					$isMatch = true;
				}
			}
			if ($isMatch) {
                        $idsWhere .= $comma . $infot ['id'];
                        $comma = ',';
                        if ($infot ['url'] != false) {
                            if (!(strpos($infot ['url'], 'http') === FALSE)) {
                                $url = html_entity_decode($infot ['url']);
                            } else {
                                $urlInfos = explode(' ', $infot ['url']);
                                switch ($urlInfos [0]) {
                                    case '刮刮卡' :
                                        $url = C('site_url') . U('Wap/Guajiang/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '大转盘' :
                                        $url = C('site_url') . U('Wap/Lottery/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '商家订单' :
                                        $url = C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'] . '&hid=' . $urlInfos [1];
                                        break;
                                    case '优惠券' :
                                        $url = C('site_url') . U('Wap/Coupon/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName'], 'id' => $urlInfos [1]));
                                        break;
                                    case '会员卡' :
                                        $url = C('site_url') . U('Wap/Card/index', array('token' => $this->token, 'wecha_id' => $this->data ['FromUserName']));
                                        break;
                                    case '首页' :
                                        $url = rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->data ['FromUserName'];
                                        break;
                                }
                            }
                        } else {
                            $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot ['id']));
                        }
                        $return [] = array($infot ['title'], $infot ['text'], $infot ['pic'], $url);
            }
		}
		var_dump($return);
		return false;
	}
	
	
	public function resetpwd(){
		$uid=$this->_get('uid','intval');
		$code=$this->_get('code','trim');
		$rtime=$this->_get('resettime','intval');
		$info=M('Users')->find($uid);
		if( (md5($info['uid'].$info['password'].$info['email'])!==$code) || ($rtime<time()) ){
			$this->error('非法操作',U('Index/index'));
		}
		$this->assign('uid',$uid);
		$this->display();
	}
	
}