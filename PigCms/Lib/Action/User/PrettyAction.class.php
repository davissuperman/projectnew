<?php

class PrettyAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        //2 - pretty
        $db = M('bonus');
        $where = array('token' => $this->token,'type'=>2);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display();
    }

    public function award() {
        $db = M('pretty');
        $where = array('phone'=>array('neq',''));
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('number desc,share desc,phonetime asc')->select();
//根据GID 得到渠道
        $infoList = array();
        $n = 1;
        $p = 0;
        if( $_GET['p'] ){
            $p = $_GET['p'] - 1;
        }

        foreach($info as $each ){
            $tmp = array();
            $tmp['id'] = $n+ $p*25;
            $n++;
            $tmp['name'] = $each['name'];
            $tmp['views'] = $each['views'];
            $tmp['uniqueviews'] = $each['uniqueviews'];
            $cheat = '否';
            if( $each['views'] < $each['vote'] || $each['illegal']){
                $cheat = "<font style='color:red'>是</font>";
            }
            $tmp['cheat'] = $cheat;
            $tmp['telephone'] = $each['phone'];
            $tmp['share'] = $each['share'];
            $tmp['vote'] = $each['vote'];
            $tmp['joins'] = $each['joins'];
            $tmp['openid'] = $each['openid'];
            $tmp['number'] = $each['number'];
            $tmp['phonetime'] = $each['phonetime'];
            $tmp['sharetime'] = $each['sharetime'];
            $fansInfo = M('customer_service_fans')->where(array('openid' => $each['openid'],'token'=>'rggfsk1394161441'))->find();
            $whetherSbuscribe = $fansInfo['subscribe'];
            if($whetherSbuscribe == 1){
                $tmp['subscribe'] = "是";
            }else{
                $tmp['subscribe'] = "否";
            }
            $sex = $fansInfo['sex'];
            if($sex == 1){
                $tmp['sex'] = "男";
            }else if($sex == 2){
                $tmp['sex'] = "女";
            }else{
                $tmp['sex'] = "未知";
            }
            $tmp['province'] = $fansInfo['province'];

            $gid = $each['gid'];
            $gidInfo = M('bonus')->where(array('gid' => $gid))->find();
            $tmp['comefrom'] = $gidInfo['title'];
            $infoList[] = $tmp;
        }
//        var_dump($infoList);
        $this->assign('info', $infoList);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);

        $this->display('award');
    }

    public function statistics() {
        $db = M('doing_info_date');
        $count = $db->count();
        $page = new Page($count, 25);
        $info = $db->limit($page->firstRow . ',' . $page->listRows)->order('dateinfo desc')->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display();
    }

    public function myexport() {
        $starta = $_POST['start'];
        $enda = $_POST['end'];
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $sql = "select * from tp_doing_info_date  where dateinfo>" . $start . " and dateinfo<" . $end . " order by createtime desc";

        // $filename = $starta . "~" . $enda . "统计";
        //$this->exportexcel($list, $title, $filename);
    }

    public function add() {

        if (IS_POST) {
            $data['token'] = $_POST['token'];
            $data['title'] = $_POST['title'];
            $data['desc'] = $_POST['desc'];
            $data['type'] = 2;
            $data['img'] = $_POST['img'];
            $data['start'] = strtotime($_POST['start']);
            $data['end'] = strtotime($_POST['end']);
            $data['createtime'] = time();
            if ($_POST['gid']) {
                $qry = M("bonus")->where(array('gid' => $_POST['gid']))->save($data);
            } else {
                $qry = M("bonus")->add($data);
            }
            if ($qry) {
                $this->success('操作成功', U('Pretty/index'));
                exit;
            } else {
                $this->error('操作失败');
                exit;
            }
        } else {
            $where = array('gid' => $_GET['gid']);
            $info = M("bonus")->where($where)->find();
            $this->assign('info', $info);
            $this->assign('token', $this->token);
            $this->display();
        }
    }

    public function slist() {
        $gid = $_GET['gid'];
        $db = M('pretty');
        $where = array('gid' => $gid);//,'phone'=>array('neq','')
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('  phonetime asc')->select();
//根据GID 得到渠道
        $gidInfo = M('pretty')->where(array('gid' => $gid))->find();
        $infoList = array();
        foreach($info as $each ){
            $tmp = array();
            $tmp['id'] = $each['id'];
            $tmp['name'] = $each['name'];
            $tmp['views'] = $each['views'];
            $tmp['uniqueviews'] = $each['uniqueviews'];
            $cheat = '否';
            if( $each['views'] < $each['vote'] || $each['illegal']){
                $cheat = "<font style='color:red'>是</font>";
            }
            $tmp['cheat'] = $cheat;
            $tmp['telephone'] = $each['phone'];
            $tmp['share'] = $each['share'];
            $tmp['vote'] = $each['vote'];
            $tmp['joins'] = $each['joins'];
            $tmp['openid'] = $each['openid'];
            $tmp['number'] = $each['number'];
            $tmp['createtime'] = $each['createtime'];
            $tmp['phonetime'] = $each['phonetime'];
            $tmp['sharetime'] = $each['sharetime'];
            $fansInfo = M('customer_service_fans')->where(array('openid' => $each['openid'],'token'=>'rggfsk1394161441'))->find();
            $whetherSbuscribe = $fansInfo['subscribe'];
            if($whetherSbuscribe == 1){
                $tmp['subscribe'] = "是";
            }else{
                $tmp['subscribe'] = "否";
            }
            $sex = $fansInfo['sex'];
            if($sex == 1){
                $tmp['sex'] = "男";
            }else if($sex == 2){
                $tmp['sex'] = "女";
            }else{
                $tmp['sex'] = "未知";
            }
            $tmp['province'] = $fansInfo['province'];
            $savePath = './PUBLIC/imagess/';
            $openid = $each['openid'];
            $t = $each['uploadimagetime'];
            $uploadImageSrc= $savePath."$openid"."_$t".".jpeg";
            if(file_exists($uploadImageSrc)){
                $tmp['imgsrc'] = $uploadImageSrc;
            }else{
                $tmp['imgsrc'] = 1;
            }

//            //根据openid获取收获地址
//            $awardInfo = M("countmask_award")->where(array("openid" => $each['openid']))->find();
//            if($info){
//                $tmp['awardusername'] = $awardInfo['name'];//收货人姓名
//                $tmp['awarduserphone'] = $awardInfo['phone'];//收货人手机号
//                $tmp['awarduserprovince'] = $awardInfo['phone'];//收货人省份
//                $tmp['awardusercity'] = $awardInfo['phone'];//收货人城市
//                $tmp['awarduseraddress'] = $awardInfo['phone'];//收货人地址
//            }

            $infoList[] = $tmp;
        }
//        var_dump($infoList);
        $this->assign('info', $infoList);
        $this->assign('gid', $gid);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->assign('comefrom', $gidInfo['title']);

        $this->display('list');
    }

    public function ulist() {
        $db = M('doing_list');
        $where = array('gid' => $_GET['gid']);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display('ulist');
    }

    public function exportstore() {
        $start = 1;
        $end = 100;
        if(isset($_POST['start']) && $_POST['start']){
            $start = $_POST['start'];
        }
        if(isset($_POST['end']) && $_POST['end']){
            $end = $_POST['end'];
        }
//        $start = $_POST['start'];
//        $end = $_POST['end'];
        $end=$end-$start;
        $start=$start-1;
        $list = M('countmask')->query(
            "SELECT info.*, info.number n,award.telephone tels,bonus.title tel,award.province as city, award.address as addres,award.type as type,award.telephone as tels,award.province as city,
award.address as addres,award.orderid as orderid,award.username as username from tp_bonus_info as info
             left join tp_bonus_award as award on (award.openid=info.openid)
             left join tp_bonus as bonus on (bonus.gid=info.gid)
             order by info.number desc,share desc,phonetime asc limit $start,$end"); //第二名和你最近的
        $i = $start+1;
        foreach ($list as $k => $v) {
            $list[$k]['sort'] = $i;
            $i = $i + 1;
        }
        $listArr = array();
        foreach($list as $key => $each){
            $awardInfo = '';
            $tmp = $each;
            if($each['sharetime']){
                $tmp['sharetime'] = date('Y-m-d H:i:s', $tmp['createtime']);
            }else{
                $tmp['sharetime'] = "无";
            }
            if($each['createtime']){
                $tmp['createtime'] = date('Y-m-d H:i:s', $tmp['createtime']);
            }else{
                $tmp['createtime'] = "无";
            }
            if($each['views']< $each['vote'] || $each['illegal']){
                $tmp['illegal'] = "是";
            }else{
                $tmp['illegal'] = "否";
            }
            $condition['openid'] = $each['openid'];
            $condition['type'] = $each['type'];
            $condition['orderid'] =array('neq','');
            if($each['type'] == 1){
                $awardInfo = '一等奖';
            }else if($each['type'] == 2){
                $awardInfo = '二等奖';
            }else if($each['type'] == 3 ){
                $awardInfo = '三等奖';
            }else if($each['type'] == 4 ){
                $awardInfo = '四等奖';
            }

            $tmp['awardlist'] = $awardInfo;

            $listArr[] = $tmp;
        }
        $filename = $start . "~" . $end . "统计";
        $this->exportexcelx($listArr, $filename);
    }
    public function exporthistory() {
        $start = 1;
        $end = 100;
        $openId = $_POST['openid'];
        if(!$openId){
            echo '请输入OPENID';
            return;
        }
        $list = M('bonus_history')->query(
            "SELECT h.*,info.nickname as name from tp_bonus_history as h
              left join tp_customer_service_fans info on (info.openid=h.from_open_id)
              where h.openid='".$openId."'
             order by h.createtime desc"); //第二名和你最近的




        $filename = $start . "~" . $end . "统计";
        $this->exportexcelhistory($list, $filename);
    }
    public function exportcheckbox() {
        $sorts = $_REQUEST['item'];
        $in = implode(' , ', $sorts);
        $list = M('doing_info')->query("SELECT b.addres,b.city,b.tel as tels,b.name as names,a.*,a.share*10+a.number as n FROM `tp_doing_info` as a left join `tp_doing_list` as b on a.tel=b.temptel where a.id in ($in) order by n desc "); //第二名和你最近
        $i = 0;
        foreach ($sorts as $k => $v) {
            $list[$i]['sort'] = $k;
            $i = $i + 1;
        }
        $filename = "统计";
        $this->exportexcelx($list, $filename);
    }
    public function exportexcelhistory($data = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '描述')
            ->setCellValue('D1', '分数')
            ->setCellValue('E1', '加分时间');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['name'])
                ->setCellValue('C' . ($n + 2), $data[$n]['description'])
                ->setCellValue('D' . ($n + 2), $data[$n]['number'])
                ->setCellValue('E' . ($n + 2), $data[$n]['createtime'])
            ;
        }
        $objPHPExcel->getActiveSheet()->setTitle('加分历史');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    public function exportexcelx($data = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '排名')
            ->setCellValue('B1', '分数')
            ->setCellValue('C1', '模版号')
            ->setCellValue('D1', '姓名')
            ->setCellValue('E1', '手机号')
            ->setCellValue('F1', '收货人姓名')
            ->setCellValue('G1', '城市')
            ->setCellValue('H1', '地址')
            ->setCellValue('I1', '浏览量')
            ->setCellValue('K1', '投票量')
            ->setCellValue('L1', '已领奖项')
            ->setCellValue('L1', '天锚订单号')
            ->setCellValue('M1', '非法数据');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $data[$n]['sort'])
                ->setCellValue('B' . ($n + 2), $data[$n]['n'])
                ->setCellValue('C' . ($n + 2), $data[$n]['tel'])
                ->setCellValue('D' . ($n + 2), $name)
                ->setCellValue('E' . ($n + 2), $data[$n]['tels'])
                ->setCellValue('F' . ($n + 2), $data[$n]['username'])
                ->setCellValue('G' . ($n + 2), $data[$n]['city'])
                ->setCellValue('H' . ($n + 2), $data[$n]['addres'])
                ->setCellValue('I' . ($n + 2), $data[$n]['views'])
                ->setCellValue('J' . ($n + 2), $data[$n]['vote'])
                ->setCellValue('K' . ($n + 2), $data[$n]['awardlist'])
                ->setCellValue('L' . ($n + 2), $data[$n]['orderid'])
                ->setCellValue('M' . ($n + 2), $data[$n]['illegal'])
            ;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function getUserInfo($id){
        $r = M('pretty')->where("id=$id")->find();
        if(!$r){
            return null;
        }
        return $r;
    }
    public function export() {
        $starta = $_POST['start'];
        $enda = $_POST['end'];
        $gid=$_POST['gid'];
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $awardFromPost = null;
        $awardFromPost = $_POST['award'];
        if(!$start){
            $start = strtotime(date('Y-m-d').'00:00:00');
        }
        if(!$end){
            $end = strtotime(date('Y-m-d').'24:00:00');
        }



        $listArr = array();
        $pL =array();
        if($awardFromPost){
            $phoneList = M('pretty_phonelist')->select();
            $arr = array();
            foreach($phoneList as $key =>$eachValue){
//                if($key >= 5){
//                    break;
//                }
                $id = $eachValue['id'];
                $uid = $eachValue['uid'];
                $level = null;
                if(strrchr((string)"$id","18") == "18"){
                    $level = 1;
                }elseif(strrchr((string)"$id","1") == '1'){
                    $level = 2;
                }elseif(strrchr((string)"$id","8") == '8'){
                    $level = 2;
                }
                $userInfo = $this->getUserInfo($uid);
                $tmp = null;
                $each = $userInfo;
                $tmp = $each;
                $gid = $each['gid'];
                $gInfo = M("bonus")->where(array('gid'=>$gid))->select();
                $awardInfo = array();
                $tmp['gidname'] = $gInfo[0]['title'];
                if($each['createtime']){
                    $tmp['createtime'] = date('Y-m-d H:i:s', $tmp['createtime']);
                }else{
                    $tmp['createtime'] = "无";
                }
                if($each['sharetime']){
                    $tmp['sharetime'] = date('Y-m-d H:i:s', $tmp['sharetime']);
                }else{
                    $tmp['sharetime'] = "无";
                }
                if($each['phonetime']){
                    $tmp['phonetime'] = date('Y-m-d H:i:s', $tmp['phonetime']);
                }else{
                    $tmp['phonetime'] = "无";
                }
                if($each['views']< $each['vote'] || $each['illegal']){
                    $tmp['illegal'] = "是";
                }else{
                    $tmp['illegal'] = "否";
                }
                $condition['openid'] = $each['openid'];
                $resAwardList = M('pretty_award')->where($condition)->select();
                $tmp['username'] = null;
                $tmp['userphone'] = null;
                $tmp['userprovince'] = null;
                $tmp['city'] = null;
                $tmp['address'] = null;
                if($resAwardList){
                    foreach($resAwardList as $award){
                        $tmp['username'] = $award['name'];
                        $tmp['userphone'] = $award['phone'];
                        $tmp['userprovince'] = $award['province'];
                        $tmp['city'] = $award['city'];
                        $tmp['address'] = $award['address'];
                    }
                }
                $tmp['orderid'] = $id;
                $tmp['level'] = $level;
                $listArr[] = $tmp;
            }
        }else{
            //全部的数据
            $db = M('pretty');
            $sql = "select *  from tp_pretty where createtime>=$start and createtime<$end order by  createtime asc ";//where gid=$gid limit 100
            $list = M()->query($sql);
            foreach($list as $key => $each){
                $tmp = null;
                $tmp = $each;
                $gid = $each['gid'];
                $gInfo = M("bonus")->where(array('gid'=>$gid))->select();
                $awardInfo = array();
                $tmp['gidname'] = $gInfo[0]['title'];
                if($each['createtime']){
                    $tmp['createtime'] = date('Y-m-d H:i:s', $tmp['createtime']);
                }else{
                    $tmp['createtime'] = "无";
                }
                if($each['sharetime']){
                    $tmp['sharetime'] = date('Y-m-d H:i:s', $tmp['sharetime']);
                }else{
                    $tmp['sharetime'] = "无";
                }
                if($each['phonetime']){
                    $tmp['phonetime'] = date('Y-m-d H:i:s', $tmp['phonetime']);
                }else{
                    $tmp['phonetime'] = "无";
                }
                if($each['views']< $each['vote'] || $each['illegal']){
                    $tmp['illegal'] = "是";
                }else{
                    $tmp['illegal'] = "否";
                }
                $condition['openid'] = $each['openid'];
                $resAwardList = M('pretty_award')->where($condition)->select();
                $tmp['username'] = null;
                $tmp['userphone'] = null;
                $tmp['userprovince'] = null;
                $tmp['city'] = null;
                $tmp['address'] = null;
                if($resAwardList){
                    foreach($resAwardList as $award){
                        $tmp['username'] = $award['name'];
                        $tmp['userphone'] = $award['phone'];
                        $tmp['userprovince'] = $award['province'];
                        $tmp['city'] = $award['city'];
                        $tmp['address'] = $award['address'];
                    }
                }
                $phone = $each['phone'];
                $orderId = null;
                $orderId = M('pretty_phonelist')->where(array('phone' => $phone) )->getField('id');
                $tmp['orderid'] = $orderId;

                $level = null;
                if(strrchr((string)"$orderId","18") == "18"){
                    $level = 1;
                }elseif(strrchr((string)"$orderId","1") == '1'){
                    $level = 2;
                }elseif(strrchr((string)"$orderId","8") == '8'){
                    $level = 2;
                }
                $tmp['level'] = $level;
                $listArr[] = $tmp;
            }
        }

        $title = array();
        $filename = $starta . "~" . $enda . "统计";
        $this->exportexcel($listArr, $title, $filename);
    }
    function ReplaceSpecialChar($C_char){//过滤特殊字符
        $C_char=HTMLSpecialChars($C_char); //将特殊字元转成 HTML 格式
        $C_char=str_replace(",","",$C_char); //替换英文逗号,
        $C_char=str_replace("<","",$C_char); //替换英文小破折号<
        $C_char=str_replace(">","",$C_char);//替换英文小破折号>
        $C_char=str_replace("'","",$C_char);//替换英文单引号 '
        $C_char=str_replace("{","",$C_char);//替换英文大括号{
        $C_char=str_replace("}","",$C_char);//替换英文大括号}
        $C_char=str_replace("(","",$C_char);//替换英文小括号(
        $C_char=str_replace("）","",$C_char);//替换英文小括号）
        $C_char=str_replace("=","",$C_char);//替换英文小括号）
        return $C_char;//返回处理结果
    }
    public function exportexcel($data = array(), $title = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'OPENID')
            ->setCellValue('C1', '微信昵称')
            ->setCellValue('D1', '联系电话')
            ->setCellValue('E1', '来源分组')
            ->setCellValue('F1', '参加游戏时间')
            ->setCellValue('G1', '首次分享时间')
            ->setCellValue('H1', '手机号')
            ->setCellValue('I1', '提交手机号时间')
            ->setCellValue('J1', 'PV')
            ->setCellValue('K1', 'UV')
            ->setCellValue('L1', '分享数')
            ->setCellValue('M1', '得票数')
            ->setCellValue('N1', '扩散数')
            ->setCellValue('O1', '收货姓名')
            ->setCellValue('P1', '收货手机')
            ->setCellValue('Q1', '省份')
            ->setCellValue('R1', '城市')
            ->setCellValue('S1', '地址')
            ->setCellValue('T1', '排名')
            ->setCellValue('U1', '奖项')
            ->setCellValue('V1', '头像')
            ;
        //写出内容 UTF-8
        //log :: write( print_r($data,true)  );
        $imageUrl = "http://wx.drjou.cc/PUBLIC/imagess/";
        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $name = str_replace('=','',$name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['openid'])
                ->setCellValue('C' . ($n + 2),  $name)
                ->setCellValue('D' . ($n + 2), $data[$n]['phone'])
                ->setCellValue('E' . ($n + 2), $data[$n]['gidname'])
                ->setCellValue('F' . ($n + 2), $data[$n]['createtime'])
                ->setCellValue('G' . ($n + 2), $data[$n]['sharetime'])
                ->setCellValue('H' . ($n + 2), $data[$n]['phone'])
                ->setCellValue('I' . ($n + 2), $data[$n]['phonetime'])
                ->setCellValue('J' . ($n + 2), $data[$n]['views'])
                ->setCellValue('K' . ($n + 2), $data[$n]['uniqueviews'])
                ->setCellValue('L' . ($n + 2), $data[$n]['share'])
                ->setCellValue('M' . ($n + 2), $data[$n]['vote'])
                ->setCellValue('N' . ($n + 2), $data[$n]['joins'])
                ->setCellValue('O' . ($n + 2), $data[$n]['username'])
                ->setCellValue('P' . ($n + 2), $data[$n]['userphone'])
                ->setCellValue('Q' . ($n + 2), $data[$n]['userprovince'])
                ->setCellValue('R' . ($n + 2), $data[$n]['city'])
                ->setCellValue('S' . ($n + 2), $data[$n]['address'])
                ->setCellValue('T' . ($n + 2), $data[$n]['orderid'])
                ->setCellValue('U' . ($n + 2), $data[$n]['level'] )
                ->setCellValue('V' . ($n + 2), $imageUrl.$data[$n]['openid']."_".$data[$n]['uploadimagetime'].".jpeg")
                ;

        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    public function exportDataReport(){
        set_time_limit(0);
        //每日数据汇总（记录每天活动所有模板所产生的数据总数）

        //记录从6.20 到 7.20号每天产生的模板总数
        $fromDate = strtotime("2015-10-01 00:00:00");
        $endDate = strtotime("2015-12-10 00:00:00");
        $i = 0;
        $datereport = array();
        while($i<35){
            $eachData = array();
            $add = 24*3600;
            $eachFrom = $i*$add + $fromDate;
            $eachEnd = $eachFrom + $add;

            $queryGidCount = "SELECT * from tp_pretty where   createtime >= $eachFrom and createtime<$eachEnd";
            $list = M('pretty')->query($queryGidCount);

            if($list){
                $eachData['date'] = date('Y-m-d',$eachFrom);
                $eachData['sumtemplate'] = count($list);
                $pvSum = 0;
                $uvSum = 0;
                $shareSum = 0;
                $voteSum = 0;
                $joinSum = 0;
                foreach($list as $each){
                    $pvSum += $each['views'];
                    $uvSum += $each['uniqueviews'];
                    $shareSum += $each['share'];
                    $voteSum += $each['vote'];
                    $joinSum += $each['joins'];
                }
                $eachData['pvsum'] = $pvSum;
                $eachData['uvsum'] = $uvSum;
                $eachData['sharesum'] = $shareSum;
                $eachData['votesum'] = $voteSum;
                $eachData['joinsum'] = $joinSum;
                $datereport[] = $eachData;
            }
            $i++;
        }

        $filename = "每日数据汇总" . "统计";
        $this->exportexcelx1($datereport, $filename);
    }
    public function exportexcelx1($data = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '日期')
            ->setCellValue('C1', '模板数')
            ->setCellValue('D1', 'PV')
            ->setCellValue('E1', 'UV')
            ->setCellValue('F1', '转发数')
            ->setCellValue('G1', '投票数')
            ->setCellValue('H1', '扩散数')
            ;

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['date'])
                ->setCellValue('C' . ($n + 2), $data[$n]['sumtemplate'])
                ->setCellValue('D' . ($n + 2), $data[$n]['pvsum'])
                ->setCellValue('E' . ($n + 2), $data[$n]['uvsum'])
                ->setCellValue('F' . ($n + 2), $data[$n]['sharesum'])
                ->setCellValue('G' . ($n + 2), $data[$n]['votesum'])
                ->setCellValue('H' . ($n + 2), $data[$n]['joinsum'])
            ;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" ."每日数据汇总". date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    public function datareport(){
        set_time_limit(0);
        //每日数据汇总（记录每天活动所有模板所产生的数据总数）

        //记录从6.20 到 7.20号每天产生的模板总数
        $fromDate = strtotime("2015-10-01 00:00:00");
        $endDate = strtotime("2015-11-10 00:00:00");
        $i = 0;
        $datereport = array();
        while($i<35){
            $eachData = array();
            $add = 24*3600;
            $eachFrom = $i*$add + $fromDate;
            $eachEnd = $eachFrom + $add;

            $queryGidCount = "SELECT * from tp_pretty where   createtime >= $eachFrom and createtime<$eachEnd";
            $list = M('pretty')->query($queryGidCount);

            if($list){
                $eachData['date'] = date('Y-m-d',$eachFrom);
                $eachData['sumtemplate'] = count($list);
                $pvSum = 0;
                $uvSum = 0;
                $shareSum = 0;
                $voteSum = 0;
                $joinSum = 0;
                foreach($list as $each){
                    $pvSum += $each['views'];
                    $uvSum += $each['uniqueviews'];
                    $shareSum += $each['share'];
                    $voteSum += $each['vote'];
                    $joinSum += $each['joins'];
                }
                $eachData['pvsum'] = $pvSum;
                $eachData['uvsum'] = $uvSum;
                $eachData['sharesum'] = $shareSum;
                $eachData['votesum'] = $voteSum;
                $eachData['joinsum'] = $joinSum;
                $datereport[] = $eachData;
            }
            $i++;
        }

        $this->assign('datareport', $datereport);
        $this->display();
    }

    public function datareport2(){
        set_time_limit(0);

        //获取所有模板
        $query = "select gid,title from tp_bonus where type=2";
        $glist = M('bonus')->query($query);
        $this->assign('glist', $glist);

        //每日渠道汇总表
        $m = 0;
        $datereport2 = array();
        $fromDate2 = strtotime("2015-10-01 00:00:00");
        $endDate2 = strtotime("2015-11-20 00:00:00");
        while($m<35){
            $gidArr = array();
            $add = 24*3600;
            $eachFrom2 = $m*$add + $fromDate2;
            $eachEnd2 = $eachFrom2 + $add;

            $queryGidCount = "SELECT gid, count(id)  as countnumber from tp_pretty where  createtime >= $eachFrom2 and createtime<$eachEnd2  group by gid ";
            $list2 = M('pretty')->query($queryGidCount);

            if($list2){
                foreach($list2 as $each2){
                    $gid = $each2['gid'];
                    $gidArr[$gid] = $each2['countnumber'];

                }
                $gidArr['date'] =  date('Y-m-d',$eachFrom2);
                $datereport2[] = $gidArr;
            }


            $m++;
        }
        $this->assign('datareport', $datereport2);
        $this->display();
    }

    //渠道汇总
    public function exportDataReport2(){
        set_time_limit(0);

        //每日渠道汇总表
        $m = 0;
        $datereport2 = array();
        $fromDate2 = strtotime("2015-10-01 00:00:00");
        $endDate2 = strtotime("2015-12-10 00:00:00");
        while($m<35){
            $gidArr = array();
            $add = 24*3600;
            $eachFrom2 = $m*$add + $fromDate2;
            $eachEnd2 = $eachFrom2 + $add;

            $queryGidCount = "SELECT gid, count(id)  as countnumber from tp_pretty where createtime >= $eachFrom2 and createtime<$eachEnd2  group by gid ";
            $list2 = M('pretty')->query($queryGidCount);

            if($list2){
                foreach($list2 as $each2){
                    $gid = $each2['gid'];
                    $gidArr[$gid] = $each2['countnumber'];

                }
                $gidArr['date'] =  date('Y-m-d',$eachFrom2);
                $datereport2[] = $gidArr;
            }


            $m++;
        }
        $filename = "每日渠道汇总" . "统计";
        $this->exportexcelx2($datereport2, $filename);
    }
    public function exportexcelx2($data = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $query = "select gid,title from tp_bonus where type=2";
        $glist = M('bonus')->query($query);
        $arrayKeyList = array(
            'A1','B1','C1','D1','E1','F1','G1','H1','I1','J1','K1','L1','M1',
            'N1','O1','P1','Q1','R1','S1','T1','U1','V1','W1','X1','Y1','Z1',
            'AA1','AB1','AC1','AD1','AE1','AF1','AG1','AH1','AI1','AJ1','AK1','AL1','AM1',
            'AN1','AO1','AP1','AQ1','AR1','AS1','AT1','AU1','AV1','AW1','AX1','AY1','AZ1',
            'BA1','BB1','BC1','BD1','BE1','BF1','BG1','BH1'
        );
        $arrayKeyList2 = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
            'BA','BB','BC','BD','BE','BF','BG','BH'
        );
        $obj = $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '日期');
        foreach($glist as $key => $each){
            $obj = $obj->setCellValue($arrayKeyList[$key+2], $each['title']);
        }

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $obj2 = $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['date']);
            $query = "select gid,title from tp_bonus where type=2";
            $glist = M('bonus')->query($query);
            foreach($glist as $key => $each){
                $obj2 = $obj2->setCellValue($arrayKeyList2[$key+2] . ($n + 2), $data[$n][$each['gid']]);
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" ."每日渠道汇总表". date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function exportalldata() {
        $starta = $_POST['start'];
        $enda = $_POST['end'];
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        if(!$start){
            $start = strtotime(date('Y-m-d').'00:00:00');
        }
        if(!$end){
            $end = strtotime(date('Y-m-d').'24:00:00');
        }
        $db = M('countmask');
        $sql = "select gid,  openid ,phone,name,phonetime,sharetime,share,views,uniqueviews,vote,joins,number
        from tp_countmask  where phonetime>" . $start . " and phonetime<" . $end . " and phone != '' order by number desc,share desc,phonetime asc";

        $list = M()->query($sql);
        $listArr = array();
        foreach($list as $key => $each){
            $tmp = $each;
            $gid = $each['gid'];
            $gInfo = M("bonus")->where(array('gid'=>$gid))->select();
            $awardInfo = array();
            $tmp['gidname'] = $gInfo[0]['title'];
            if($each['sharetime']){
                $tmp['sharetime'] = date('Y-m-d H:i:s', $tmp['sharetime']);
            }else{
                $tmp['sharetime'] = "无";
            }
            if($each['phonetime']){
                $tmp['phonetime'] = date('Y-m-d H:i:s', $tmp['phonetime']);
            }else{
                $tmp['phonetime'] = "无";
            }
            if($each['views']< $each['vote'] || $each['illegal']){
                $tmp['illegal'] = "是";
            }else{
                $tmp['illegal'] = "否";
            }
            $condition['openid'] = $each['openid'];
            $resAwardList = M('countmask_award')->where($condition)->select();
            if($resAwardList){
                foreach($resAwardList as $award){
                    $tmp['username'] = $award['name'];
                    $tmp['userphone'] = $award['phone'];
                    $tmp['userprovince'] = $award['province'];
                    $tmp['city'] = $award['city'];
                    $tmp['address'] = $award['address'];
                }
            }

            //根据openid 获取三次得分情况
            $numberList = M('countmask_list')->where($condition)->select();
            foreach($numberList as $numberEach){
                switch($numberEach['sequence']){
                    case 1:
                        $tmp['numberadd1'] = $numberEach['number'];
                        break;
                    case 2:
                        $tmp['numberadd2'] = $numberEach['number'];
                        break;
                    case 3:
                        $tmp['numberadd3'] = $numberEach['number'];
                        break;
                }
            }

            $listArr[] = $tmp;
        }
        $title = array();
        $filename = $starta . "~" . $enda . "统计";
        $this->exportexcelalldata($listArr, $title, $filename);
    }

    public function exportexcelalldata($data = array(), $title = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'OPENID')
            ->setCellValue('C1', '微信昵称')
            ->setCellValue('D1', '联系电话')
            ->setCellValue('E1', '来源分组')
            ->setCellValue('F1', '参加游戏时间')
            ->setCellValue('G1', '首次分享时间')
            ->setCellValue('H1', '总分数')
            ->setCellValue('I1', '加分一')
            ->setCellValue('J1', '加分二')
            ->setCellValue('K1', '加分三')
            ->setCellValue('L1', 'PV')
            ->setCellValue('M1', 'UV')
            ->setCellValue('N1', '分享数')
            ->setCellValue('O1', '得票数')
            ->setCellValue('P1', '扩散数')
            ->setCellValue('Q1', '收货姓名')
            ->setCellValue('R1', '收货手机')
            ->setCellValue('S1', '省份')
            ->setCellValue('T1', '城市')
            ->setCellValue('U1', '地址');
        //写出内容 UTF-8
        //log :: write( print_r($data,true)  );
        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $name = str_replace('=','',$name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['openid'])
                ->setCellValue('C' . ($n + 2),  $name)
                ->setCellValue('D' . ($n + 2), $data[$n]['phone'])
                ->setCellValue('E' . ($n + 2), $data[$n]['gidname'])
                ->setCellValue('F' . ($n + 2), $data[$n]['phonetime'])
                ->setCellValue('G' . ($n + 2), $data[$n]['sharetime'])
                ->setCellValue('H' . ($n + 2), $data[$n]['number'])
                ->setCellValue('I' . ($n + 2), $data[$n]['numberadd1'])
                ->setCellValue('J' . ($n + 2), $data[$n]['numberadd2'])
                ->setCellValue('K' . ($n + 2), $data[$n]['numberadd3'])
                ->setCellValue('L' . ($n + 2), $data[$n]['uniqueviews'])
                ->setCellValue('M' . ($n + 2), $data[$n]['views'])
                ->setCellValue('N' . ($n + 2), $data[$n]['share'])
                ->setCellValue('O' . ($n + 2), $data[$n]['vote'])
                ->setCellValue('P' . ($n + 2), $data[$n]['joins'])
                ->setCellValue('Q' . ($n + 2), $data[$n]['username'])
                ->setCellValue('R' . ($n + 2), $data[$n]['userphone'])
                ->setCellValue('S' . ($n + 2), $data[$n]['userprovince'])
                ->setCellValue('T' . ($n + 2), $data[$n]['city'])
                ->setCellValue('U' . ($n + 2), $data[$n]['address']);

        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . "时时排名".date("Y-m-d h:i") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function test(){
        $var = 1018;
        echo strrchr("1018","18");
        echo "<br/>";
        echo strrchr("1018","8");
//        echo strrchr("1019","8");


    }

    public function getAwardList(){
        //参加者的活动排名末位数字为“18”、“1”、“8”，则为幸运者，获得森田药妆官方提供的面膜礼盒大奖。
        $phoneList = M('pretty_phonelist')->select();
        $arr = array();
        foreach($phoneList as $each){
            $id = $each['id'];
            $uid = $each['uid'];
            if(strrchr((string)"$id","18") == "18"){
                $arr[] = $uid;
            }elseif(strrchr((string)"$id","1") == '1'){
                $arr[] = $uid;
            }elseif(strrchr((string)"$id","8") == '8'){
                $arr[] = $uid;
            }

        }
        return $arr;
    }

    public function generate(){
        for($i=0;$i<=5000;$i++){
            $phone = rand(13000000000,18999999999);
            $uid = rand(2,10000);
            $arr1['id'] = array('eq',$uid);
            $arr2['phone'] = array('eq',$phone);
            $arr = array($arr1,$arr2,'and');
//            $phoneList = M('pretty_phonelist')->where( $arr)->find();
            $phoneList = M('pretty_phonelist')->query( "select * from tp_pretty_phonelist where phone = '$phone' or uid = $uid") ;
            if(!$phoneList){
                $n = array();
                $t = time();
                $n['uid'] = $uid;
                $n['phone'] = $phone;
                $n['createtime'] = $t;

                echo "$uid  $phone<br/>";
                M('pretty_phonelist')->add($n);
            }
        }

    }
}
