<?php

class VoteAction extends BaseAction {//WAP前段页面显示

    public function __construct() {
        parent::__construct(); //初始化函数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
        $this->assign('staticFilePath', str_replace('./', '/', THEME_PATH . 'common/css/product'));
        $this->Assign('url', $url);
    }

    /*
     * $theme 投票主题
     * $Time  投票时间
     * $Aaddress 投票地址
     * $Details 活动详情
     * $derma 主题皮肤
     * $Option 投票选项
     */

    public function index() {//投票首页方法
        $token = $this->_get("token", trim);
        $this->Assign("token", $token);
        $this->Display();
    }

    /*
     * $Token 对于那个用户主题评论
     * $Id 标题ID
     * $weachId 点评用户ID;
     */

    public function commentAjax() {//投票评论AJAX
        $id = urldecode($_POST['id']);
        $title = urldecode($_POST['title']);
        $option = urldecode($_POST['options']);
        $UP = M("wedding_vote_option");
        $sql = "UPDATE tp_wedding_vote_option SET votes=votes+1 where oid='$option' and vid='$id' ";
        $a = $UP->query($sql);
        if (empty($a)) {
            $info = array('state' => 100);
            echo json_encode($info);
            exit;
        } else {
            $info = array('state' => 0);
            echo json_encode($info);
            exit;
        }
    }

    public function Ajax() {
        $VI['vid'] = urldecode($_POST['vid']);
        $VI['message'] = urldecode($_POST['message']);
        $VI['cdate'] = time();
        $c = M("wedding_vote_comment");
        $I = $c->add($VI);
        if ($I) {
            $info = array('state' => 100);
            echo json_encode($info);
            exit;
        } else {
            $info = array('state' => 0);
            echo json_encode($info);
            exit;
        }
    }

    public function voteA() {
        $VI =$_REQUEST['vid'];
        $token =$_REQUEST['token'];	
		$UP=M('wedding_vote');
        $sql = "UPDATE tp_wedding_vote SET shares=shares+1 WHERE vid='$VI' and token='$token'";	
        $a = $UP->query($sql);		
		exit;
	
    }

    public function commentList() {//投票评论列表显示方法
        /*         * ******************** */
        $id = $this->_get('id', intval);
        $token = $this->_get('token', trim);
		$UP = M( 'wedding_vote' );
		$sq = "UPDATE tp_wedding_vote SET reviews=reviews+1 WHERE vid=$id and token='$token'";
		$UP->query( $sq );
        $vote_title = M('wedding_vote')->where(array('vid' => $id, 'token' => $token))->select();
        $this->Assign('title', $vote_title[0]['subject']);
        $this->Assign('message', $vote_title[0]['message']);
        $this->Assign('is', $vote_title[0]['multiple']);
        $this->Assign('time', $this->tranTime($vote_title[0]['adate']));
        $this->Assign('style', $vote_title[0]['style']);
		$this->Assign('shares', $vote_title[0]['shares'] );
		$this->Assign('reviews', $vote_title[0]['reviews'] );		
        $this->Assign('vid', $id);
        /*         * ******************** */
        $vote_image = M('wedding_vote_attachment')->where(array('vid' => $id))->select();
        $this->Assign('img', $vote_image);
		$this->Assign( 'img_l',$vote_image[ 0 ][ 'attachment' ] );
        /*         * ******************** */
        $vote_option = M('wedding_vote_option')->where(array('vid' => $id))->select();	
        $sumSql = "select sum(votes) as c from tp_wedding_vote_option WHERE vid=$id ";
        $sum = M('wedding_vote_option')->query($sumSql);
        $this->Assign('sum', $sum);
        $this->Assign('optio', $vote_option);
		/*****************************/
		$G = M( "wedding_vote_yuyuexx" )->where( array( 'token'=>$token ) )->select();
		$this->Assign( "gid",$G[ 0 ][ 'keyid' ] );
        /*     * ******************** */
        $vote_comment = M("wedding_vote_comment")->where(array('vid' => $id))->select();
        $count = count($vote_comment);
        $p = new Page($count, 10);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = "SELECT * FROM tp_wedding_vote_comment WHERE vid=$id ORDER BY  cdate DESC  LIMIT $firstRow,$listRows";
        $list1 = M("wedding_vote_comment")->query($sql1);
		foreach( $list1 as $key=>$val ){
							$list1[ $key ][ 'cdate' ] = $this->tranTime( $val[ 'cdate' ] );
		}
        $page = $p->show();
        $this->assign('page', $page);
        $this->Assign('comment', $list1);
        $this->Assign("pages", @ceil($count / 10));
        $this->Assign("count",$count);
        $this->Assign("token", $token);
        $this->Display();
    }

    /*
     * $Token 对于那个用户主题投票
     * $Id 标题ID
     * $weachId 投票用户ID;
     */

    public function VoteAjax() {//用户投票AJAX
        $vote['subject'] = urldecode($_POST['title']);
        $vote['message'] = urldecode($_POST['content']);
        $vote['multiple'] = $_POST['kind'];
        $votea['options'] = $_POST['options'];
        $vote['style'] = $_POST['style'];
        $vote['time'] = $_POST['time'];
        $vote['adress'] = $_POST['address'];
        $vote["token"] = urldecode($_POST['token']);
        $vote['adate'] = time();
        if (!empty($_FILES["file"])) {
            import("@.ORG.upload");
            $upload = new upload();
            $filedir = $upload->mkdir_by_uid(1, './tpl/Wap/default/common/css/vote/static/');
            $upload->set_dir('./tpl/Wap/default/common/css/vote/static/' . $filedir);
            $votes['file'] = $upload->execute('./tpl/Wap/default/common/css/vote/static/' . $filedir);
        }
        if ($votea['options']) {
            $arrays = explode("^", $votea['options']);
            array_pop($arrays);
            $votea['options'] = $arrays;
            if (count($votea['options']) < 2) {
                $info = array('state' => 0);
                echo json_encode($info);
                exit;
            }
        } else {
            $info = array('state' => 0);
            echo json_encode($info);
            exit;
        }
		
        $c = M("wedding_vote");
        $a = $c->add($vote);
        if (!empty($a)) {
            foreach ($votea['options'] as $key => $val) {
                $infosA['vid'] = $a;
                $infosA['displayorder'] = $key + 1;
                $infosA['voteoption'] = $val;
                $infosA['token'] = $vote["token"];
                $b = M("wedding_vote_option");
                $b->add($infosA);
            }
            foreach ($votes['file'] as $key => $val) {
                $fileI['vid'] = $a;
                $fileI['filename'] = $val['name'];
                $fileI['attachment'] = $val['file'];
                $fileI['filesize'] = $val['size'];
                $fileI['token'] = $vote["token"];
                $fileI['dateline'] = time();
                $f = M('wedding_vote_attachment');
                $vi = $f->add($fileI);
            }
            if ($vi) {
                $info = array('state' => 200, 'vid' => $a, 'token' => $vote["token"]);
                echo json_encode($info);
                exit;
            } else {
                $info = array('state' => 0);
                echo json_encode($info);
                exit;
            }
        }
    }
	/**
 * 时间转换为 刚刚 几分钟前
 * @param type $time
 * @return type
 */
function tranTime($time) {
    $rtime = date("m-d H:i", $time);
    $htime = date("H:i", $time);
    $time = time() - $time;
    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h = floor($time / (60 * 60));
        $str = $h . '小时前 ';
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time / (60 * 60 * 24));
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