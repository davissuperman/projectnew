<?php

class WomensdayAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('greeting')->count();
        $page = new Page($count, 25);
        $list = M('greeting')->query("SELECT* from tp_greeting order by view desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
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
        $list = M('greeting')->query(
            "SELECT g.*, f.nickname as name from tp_greeting as g
            left join tp_customer_service_fans f on (f.openid=g.openid)
             order by g.view desc limit $start,$end");
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
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '浏览量')
            ->setCellValue('D1', '分享量')
            ->setCellValue('E1', '送祝福点击数')
            ->setCellValue('F1', '接收祝福点击数')
            ->setCellValue('G1', '我也要送贺卡点击数')
            ->setCellValue('H1', '关注森天药妆点击数');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $name = $data[$n]['name'];
            $name = $this->ReplaceSpecialChar($name);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2), $n+1)
                ->setCellValue('B' . ($n + 2), $name)
                ->setCellValue('C' . ($n + 2), $data[$n]['view'])
                ->setCellValue('D' . ($n + 2), $data[$n]['share'])
                ->setCellValue('E' . ($n + 2), $data[$n]['joins'])
                ->setCellValue('F' . ($n + 2), $data[$n]['accept'])
                ->setCellValue('G' . ($n + 2), $data[$n]['wantcard'])
                ->setCellValue('H' . ($n + 2), $data[$n]['subscribe'])
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
