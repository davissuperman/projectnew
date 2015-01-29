<?php

class CodeAction extends BaseAction {

    public function index() {
        $npic_twocode_tongji = M('npic_twocode_tongji');
        $npic_twocode = M('npic_twocode');
        $token = $this->_get('token');
        $cid = $this->_get('id');
        $wecha_id = $this->_get('wecha_id');
        $sql = "SELECT * FROM `tp_npic_twocode_tongji` WHERE `token`='$token' and `c_fid`='$cid' and `wecha_id`='$wecha_id'";
        $tongjilist = $npic_twocode_tongji->query($sql);
        $list = $npic_twocode->where("token='$token' and cid='$cid'")->find();
        $edate = $list['edate'];
        $sdate = $list['sdate'];
        if (time()>$sdate&&time() < $edate) {
          
        




            if ($list) {
                if(cookie('user_openid')){
                //二维码添加扫描次数
                $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('code_num');
                ini_set("date.timezone", "Asia/Chongqing");
                $h = date('H', time());
                $h = $h + 1;
                $h = $h - 1;

                if ($h < 6) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('one');
                } elseif ($h >= 6 && $h < 8) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('two');
                } elseif ($h >= 8 && $h < 10) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('three');
                } elseif ($h >= 10 && $h < 12) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('four');
                } elseif ($h >= 12 && $h < 14) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('five');
                } elseif ($h >= 14 && $h < 16) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('six');
                } elseif ($h >= 16 && $h < 18) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('seven');
                } elseif ($h >= 18 && $h < 20) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('eight');
                } elseif ($h >= 20 && $h < 22) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('nine');
                } elseif ($h >= 22 && $h < 24) {
                    $z = $npic_twocode->where(array('token' => $token, 'cid' => $cid))->setInc('ten');
                }
                //给用户添加扫描次数
                $memberinfo = M('member_user')->where(array('token' => $token, 'openid' => $wecha_id))->find();
                if ($memberinfo) {
                    $ud['code_num'] = $memberinfo['code_num'] + 1;
                    $ud['u_number'] = $memberinfo['u_number'] + 1;
                    $y = M('member_user')->where(array('token' => $token, 'openid' => $wecha_id))->save($ud);
                }
                }
            }
            if ($tongjilist) {
                $d['cnum'] = $tongjilist[0]['cnum'] + 1;
                $d['cjtime'] = time();
                $x = $npic_twocode_tongji->where(array('token' => $token, 'c_fid' => $cid, 'wecha_id' => $wecha_id))->save($d);
            } else {
                $data['token'] = $token;
                $data['wecha_id'] = $wecha_id;
                $data['cname'] = $list['cname'];
                $data['cnum'] = 1;
                $data['cjtime'] = time();
                $data['type'] = $list['type'];
                $data['c_fid'] = $cid;
                if ($list['qudao']) {
                    $data['qudao'] = $list['qudao'];
                }
                if ($list['city']) {
                    $data['city'] = $list['city'];
                }
                if ($list['huodong']) {
                    $data['huodong'] = $list['huodong'];
                }
                if ($list['huodongleixing']) {
                    $data['huodongleixing'] = $list['huodongleixing'];
                }
                $npic_twocode_tongji->add($data);
            }
            $url = str_replace("&amp;", "&", $list['curl']);
            header("Location:" . $url);
            exit;
        }  else {
           echo "<h1 align='center' style='margin-top: 50%'><font size='7'>时间不在活动范围以内</font><h1>";exit;    
        }
    }

}

?>
