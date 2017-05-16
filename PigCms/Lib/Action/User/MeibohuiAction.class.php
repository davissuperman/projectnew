<?php

class MeibohuiAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('Meibohui_index')->count();
        $page = new Page($count, 25);
        $list = M('Meibohui_index')->query(
            "SELECT * from tp_meibohui_index  order by createtime desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $listArr = array();
        foreach($list as $each){
            $awardInfo = '';
            $tmp = $each;
            $userOpenId = $each['oepnid'];
//            $fansInfo = M('customer_service_fans')->field('openid,nickname,headimgurl')->where(array('openid' => $userOpenId,'token'=>'rggfsk1394161441'))->find();
//            $tmp['nickname'] = $fansInfo['nickname'];
            if($each['offline'] == 1){
               $tmp['offline'] = "是";
           }else{
                $tmp['offline'] = "否";
            }
            if($each['online'] == 1){
                $tmp['online'] = "是";
            }else{
                $tmp['online'] = "否";
            }
            $listArr[] = $tmp;
        }
        $this->assign('info', $listArr);

        $this->display();
    }

    public function exportstore() {
        $linetype = $_POST['linetype'];

        if($linetype == 1){
            $list = M('Meibohui_index')->query(
                "SELECT * from tp_meibohui_index as h where h.offline=1
               order by h.createtime asc ");
        }else if($linetype == 2){
            $list = M('Meibohui_index')->query(
                "SELECT * from tp_meibohui_index as h where h.online=1
               order by h.createtime asc ");
        }else{
            $list = M('Meibohui_index')->query(
                "SELECT * from tp_meibohui_index as h
               order by h.createtime asc ");
        }
        $listArr = array();
        foreach($list as $key => $each){
            $awardInfo = '';
            $tmp = $each;
            if($each['offline'] == 1){
                $tmp['offline'] = "是";
            }else{
                $tmp['offline'] = "否";
            }
            if($each['online'] == 1){
                $tmp['online'] = "是";
            }else{
                $tmp['online'] = "否";
            }

            $listArr[] = $tmp;
        }
        $filename = "统计";
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
        $list = M('Meibohui_index')->query(
            "SELECT h.*,f.nickname as name from tp_meibohui_index as h
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
            ->setCellValue('A1', 'OPENID')
            ->setCellValue('B1', '线上')
            ->setCellValue('C1', '线下')
            ->setCellValue('D1', '姓名')
            ->setCellValue('E1', '电话')
            ->setCellValue('F1', '店铺名称')
            ->setCellValue('G1', '店铺年度营业额')
            ->setCellValue('H1', '店铺主营产品类型')
            ->setCellValue('I1', '提交时间');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($n + 2),  $data[$n]['openid'])
                ->setCellValue('B' . ($n + 2), $data[$n]['online'])
                ->setCellValue('C' . ($n + 2), $data[$n]['offline'])
                ->setCellValue('D' . ($n + 2), $data[$n]['username'])
                ->setCellValue('E' . ($n + 2), $data[$n]['telephone'])
                ->setCellValue('F' . ($n + 2), $data[$n]['storename'])
                ->setCellValue('G' . ($n + 2), $data[$n]['salary'])
                ->setCellValue('H' . ($n + 2), $data[$n]['companytype'])
                ->setCellValue('I' . ($n + 2), $data[$n]['createtime'])
            ;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") .".xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
