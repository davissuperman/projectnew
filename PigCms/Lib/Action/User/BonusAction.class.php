<?php

class BonusAction extends UserAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $db = M('bonus');
        $where = array('token' => $this->token);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display();
    }

    public function award() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('bonus_info')->count();
        $page = new Page($count, 25);
        $list = M('bonus_info')->query("SELECT distinct info.id,info.*, bonus.title  from tp_bonus_info as info
          inner join tp_bonus as bonus on (bonus.gid=info.gid)
         order by info.number desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $listArr = array();
        foreach($list as $each){
            $awardInfo = '';
            $tmp = $each;
            if($each['views']< $each['vote'] || $each['illegal']){
                $tmp['illegal'] = "<font style='color:red'>是</font>";
            }else{
                $tmp['illegal'] = "否";
            }
            $condition['openid'] = $each['openid'];
            $resAwardList = M('bonus_award')->where($condition)->field('type,telephone')->select();
                if($resAwardList){
                    $phone = '';
                    $address = '';
                    $city = '';
                    foreach($resAwardList as $award){
                        if($award['telephone']){
                            $phone = $award['telephone'];
                        }
                        if($award['type'] == 1){
                            $awardInfo .= '一等奖； ';
                            $address = $award['address'];
                            echo $address;
                            $city = $award['province'];
                        }else if($award['type'] == 2){
                            $awardInfo .= '二等奖； ';
                            $address = $award['address'];
                            $city = $award['province'];
                        }else if($award['type'] == 3 && $award['orderid'] != ''){
                            $awardInfo .= '三等奖； ';
                        }else if($award['type'] == 4 & $award['orderid'] != ''){
                            $awardInfo .= '四等奖；';
                        }
                    }
                }
            $tmp['awardlist'] = $awardInfo;
            $tmp['phone'] = $phone;
            $tmp['addres'] = $address;
            $tmp['city'] = $city;

            $listArr[] = $tmp;
        }
        $this->assign('info', $listArr);
        $this->display();
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
                $this->success('操作成功', U('Bonus/index', array('token' => $token)));
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
        $db = M('bonus_info');
        $where = array('gid' => $gid);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('number desc')->select();
//根据GID 得到渠道
        $gidInfo = M('bonus')->where(array('gid' => $gid))->find();
        $infoList = array();
        foreach($info as $each ){
            $tmp = array();
            $tmp['name'] = $each['name'];
            $tmp['views'] = $each['views'];
            $cheat = '否';
            if( $each['views'] < $each['vote'] || $each['illegal']){
                $cheat = "<font style='color:red'>是</font>";
            }
            $tmp['cheat'] = $cheat;
            $tmp['share'] = $each['share'];
            $tmp['vote'] = $each['vote'];
            $tmp['joins'] = $each['joins'];
            $tmp['openid'] = $each['openid'];
            $tmp['number'] = $each['number'];
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
            $map['openid']  = $each['openid'];
            $map['telephone']  = array('gt',0);
            $res = M('bonus_award')->where($map)->field('type,telephone')->find();
            $tmp['tel'] = $res['telephone'];

            $infoList[] = $tmp;
        }
        $this->assign('info', $infoList);
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
        $list = M('bonus_info')->query(
            "SELECT info.*, info.number n,award.telephone tels,bonus.title tel,award.province as city, award.address as addres,award.type as type,award.telephone as tels,award.province as city,
award.address as addres,award.orderid as orderid,award.username as username from tp_bonus_info as info
             left join tp_bonus_award as award on (award.openid=info.openid)
             left join tp_bonus as bonus on (bonus.gid=info.gid)
             order by info.number desc limit $start,$end"); //第二名和你最近的
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

    public function export() {
        $starta = $_POST['start'];
        $enda = $_POST['end'];
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $db = M('bonus_info');
        $sql = "select  openid as ID,tel,name,createtime,sharetime,share,views,vote,joins,number as n
        from tp_bonus_info  where createtime>" . $start . " and createtime<" . $end . " order by number desc";
        $list = M()->query($sql);
//        foreach ($list as $key => $value) {
//            $list[$key]['ID'] = $key;
//            $list[$key]['createtime'] = date('Y-m-d H:i:s', $list[$key]['createtime']);
//            if ($list[$key]['sharetime'] == 0) {
//                $list[$key]['sharetime'] = '无';
//            } else {
//                $list[$key]['sharetime'] = date('Y-m-d H:i:s', $list[$key]['sharetime']);
//            }
//        }
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
            $resAwardList = M('bonus_award')->where($condition)->field('type,telephone')->select();
            if($resAwardList){
                $phone = '';
                foreach($resAwardList as $award){
                    if($award['telephone']){
                        $phone = $award['telephone'];
                    }
                    if($award['type'] == 1){
                        $awardInfo .= '一等奖； ';
                    }else if($award['type'] == 2){
                        $awardInfo .= '二等奖； ';
                    }else if($award['type'] == 3 && $award['orderid'] != ''){
                        $awardInfo .= '三等奖； ';
                    }else if($award['type'] == 4 & $award['orderid'] != ''){
                        $awardInfo .= '四等奖；';
                    }
                }
            }
            $tmp['awardlist'] = $awardInfo;
            $tmp['phone'] = $phone;

            $listArr[] = $tmp;
        }
        //   $title = array('ID', '电话', '姓名', '参与时间', '首次分享时间', '总分数', '转发数', '浏览数', '点赞数', '扩散数');
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
                ->setCellValue('B1', '电话')
                ->setCellValue('C1', '姓名')
                ->setCellValue('D1', '参与时间')
                ->setCellValue('E1', '首次分享时间')
                ->setCellValue('F1', '总分数')
                ->setCellValue('G1', '转发数')
                ->setCellValue('H1', '浏览数')
                ->setCellValue('I1', '投票数')
                ->setCellValue('J1', '我也要参加数')
                ->setCellValue('K1', '非法数据');
        //写出内容 UTF-8
        //log :: write( print_r($data,true)  );
        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $name = str_replace('=','',$name);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($n + 2), $data[$n]['ID'])
                    ->setCellValue('B' . ($n + 2), $data[$n]['tel'])
                    ->setCellValue('C' . ($n + 2),  $name)
                    ->setCellValue('D' . ($n + 2), $data[$n]['createtime'])
                    ->setCellValue('E' . ($n + 2), $data[$n]['sharetime'])
                    ->setCellValue('F' . ($n + 2), $data[$n]['n'])
                    ->setCellValue('G' . ($n + 2), $data[$n]['share'])
                    ->setCellValue('H' . ($n + 2), $data[$n]['views'])
                    ->setCellValue('I' . ($n + 2), $data[$n]['vote'])
                    ->setCellValue('j' . ($n + 2), $data[$n]['joins'])
                    ->setCellValue('k' . ($n + 2), $data[$n]['illegal']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
