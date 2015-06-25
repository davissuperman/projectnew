<?php

class CountmaskAction  extends BonusAction {

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
                $this->success('操作成功', U('Countmask/index'));
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
        $db = M('countmask');
        $where = array('gid' => $gid,'phone'=>array('neq',''));
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('number desc')->select();
//根据GID 得到渠道
        $gidInfo = M('countmask')->where(array('gid' => $gid))->find();
        $infoList = array();
        foreach($info as $each ){
            $tmp = array();
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
        $db = M('countmask');
        $sql = "select  openid ,phone,name,phonetime,sharetime,share,views,uniqueviews,vote,joins,number
        from tp_countmask  where phonetime>" . $start . " and phonetime<" . $end . " and phone != '' order by number desc";
        $list = M()->query($sql);
        $listArr = array();
        foreach($list as $key => $each){
            $awardInfo = array();
            $tmp = $each;
            if($each['sharetime']){
                $tmp['sharetime'] = date('Y-m-d H:i:s', $tmp['createtime']);
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
                ->setCellValue('E' . ($n + 2), $data[$n]['gid'])
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
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function datareport(){
        set_time_limit(0);
        //每日数据汇总（记录每天活动所有模板所产生的数据总数）

        //记录从6.20 到 7.20号每天产生的模板总数
        $fromDate = strtotime("2015-06-20 00:00:00");
        $endDate = strtotime("2015-07-20 00:00:00");
        $i = 0;
        $datereport = array();
        while($i<35){
            $eachData = array();
            $add = 24*3600;
            $eachFrom = $i*$add + $fromDate;
            $eachEnd = $eachFrom + $add;

            $queryGidCount = "SELECT * from tp_countmask where phone != '' and phonetime >= $eachFrom and phonetime<$eachEnd";
            $list = M('countmask')->query($queryGidCount);

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
}
