<?php

class DinghaiAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('Dinghai_index')->count();
        $page = new Page($count, 25);
        $list = M('Dinghai_index')->query(
            "SELECT * from tp_dinghai_index  order by createtime desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $listArr = array();
        foreach($list as $each){
            $awardInfo = '';
            $tmp = $each;
            $condition['openid'] = $each['openid'];
            $tmp['awardlist'] = $awardInfo;
            $name = M('customer_service_fans')->where(array('openid' => $condition['openid'],'token'=>'rggfsk1394161441'))->getField('nickname');
            $tmp['name'] = $name;
            $listArr[] = $tmp;
        }
        $this->assign('info', $listArr);

        $this->display();
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
        $list = M('Dinghai_index')->query(
            "SELECT h.*,f.nickname as name from tp_dinghai_index as h
              left join tp_customer_service_fans f on (f.openid=h.openid)
               order by h.createtime desc limit $start,$end");
        $i = $start+1;
        $listArr = array();
        foreach($list as $key => $each){
            $awardInfo = '';
            $tmp = $each;

            $listArr[] = $tmp;
        }
        $filename = $start . "~" . $end . "统计";
        $this->exportexcelx($listArr, $filename);
    }
    public function exportstorebydate() {
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
        if(isset($_POST['startdate']) && $_POST['startdate']){
            $start = $_POST['startdate'];
        }
        if(isset($_POST['enddate']) && $_POST['enddate']){
            $end = $_POST['enddate'];
        }
//        $start = $_POST['start'];
//        $end = $_POST['end'];
        $list = M('Dinghai_index')->query(
            "SELECT h.*,f.nickname as name from tp_dinghai_index as h
              left join tp_customer_service_fans f on (f.openid=h.openid)
              where h.createtime >= '$start' and h.createtime<='$end'
               order by h.createtime desc");
        $i = $start+1;
        $listArr = array();
        foreach($list as $key => $each){
            $awardInfo = '';
            $tmp = $each;

            $listArr[] = $tmp;
        }
        $filename = $start . "~" . $end . "统计";
        $this->exportexcelx($listArr, $filename);
    }
    public function exportexcelx($data = array(), $filename = 'report') {
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //写出表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '排名')
            ->setCellValue('B1', 'OPENID')
            ->setCellValue('C1', '昵称')
            ->setCellValue('D1', '手机号')
            ->setCellValue('E1', '首次参与时间')
            ->setCellValue('F1', '发奖按钮点击次数')
            ->setCellValue('G1', '是否领奖');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $award =  $data[$n]['award'];
            $awardDes = 'NO';
            if($award == 1){
                $awardDes = "YES";
            }
            $name = $this->ReplaceSpecialChar($name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $data[$n]['openid'])
                ->setCellValue('C' . ($n + 2), $name)
                ->setCellValue('D' . ($n + 2), $data[$n]['phone'])
                ->setCellValue('E' . ($n + 2), $data[$n]['createtime'])
                ->setCellValue('F' . ($n + 2), $data[$n]['lingjiangsum'])
                ->setCellValue('G' . ($n + 2), $awardDes)
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

}
