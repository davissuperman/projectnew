<?php

class WomensdayAction  extends BonusAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        // join tp_bonus_award as award on (info.openid=award.openid ) ,award.province as city, award.address as addres
        $count = M('womensday')->count();
        $page = new Page($count, 25);
        $list = M('womensday')->query(
            "SELECT w.openid as openid, w.shares as shares,w.views as views , a.telephone as telephone,
              a.province as province, a.address as address, w.createtime, a.award as award, w.getsucaiclicknum as getsucaiclicknum from tp_womensday as w
              left join tp_womensday_award as a on (a.openid=w.openid)
        order by views desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
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

    public function importstore(){
        if (! empty ( $_FILES ['file_stu'] ['name'] ))
        {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls")
            {
                $this->error ( '不是Excel文件，重新上传' );
            }
            /*设置上传路径*/
            $savePath =  "./PUBLIC/Excel/";
            /*以时间来命名上传的文件*/
            $str = date ( 'Ymdhis' );
            $file_name = $str . "." . $file_type;
            /*是否上传成功*/
            if (! copy ( $tmp_file, $savePath . $file_name ))
            {
                $this->error ( '上传失败' );
            }
            /*
               *对上传的Excel数据进行处理生成编程数据,这个函数会在下面第三步的ExcelToArray类中
              注意：这里调用执行了第三步类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
            */
            $res = $this->read ( $savePath . $file_name );
            /*
                 重要代码 解决Thinkphp M、D方法不能调用的问题
                 如果在thinkphp中遇到M 、D方法失效时就加入下面一句代码
             */
            //spl_autoload_register ( array ('Think', 'autoload' ) );
            if(count($res)>=1){
                /*对生成的数组进行数据库的写入*/

                foreach ( $res as $k => $v )
                {
                    $d = array();
                    if ($k*1 > 1)
                    {
                        $openId = $v[1];
                        $award = $v[9];
                        if(strtolower($award) == 'yes' || strtolower($award) == 'y'){
                            //此openid 为中奖号码

                            $info = M('womensday_award')->where(array('openid' => $openId))->find();
                            $d['id'] = $info['id'];
                            $d['award'] = 1;
                            M("womensday_award")->save($d);
                            echo "OPENID ".$openId ." 姓名  ".$v[2] ." 手机号为 ".$v[6]."  设置为中奖<br/>";
                        }else{
                            $info = M('womensday_award')->where(array('openid' => $openId))->find();
                            $d['id'] = $info['id'];
                            $d['award'] = 0;
                            M("womensday_award")->save($d);
                            echo "OPENID ".$openId ." 姓名  ".$v[2] ." 手机号为 ".$v[6]."  设置为<font color='red'>未中奖</font><br/>";
                        }
                    }
                }
            }

        }
    }
    public function read($filename,$encode='utf-8'){
        $str = substr(THINK_PATH, 0, -1);
        require_once $str . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
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
        $list = M('womensday')->query(
            "SELECT g.*,award.*, f.nickname as name from tp_womensday_award as award
              left join tp_womensday as g on (g.openid = award.openid)
              left join tp_customer_service_fans f on (f.openid=g.openid)
               order by g.views desc limit $start,$end");
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
        $list = M('womensday')->query(
            "SELECT g.*,award.*, f.nickname as name from tp_womensday_award as award
              left join tp_womensday as g on (g.openid = award.openid)
              left join tp_customer_service_fans f on (f.openid=g.openid)
              where award.createtime >= '$start' and award.createtime<='$end'
               order by g.views desc");
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
            ->setCellValue('D1', '浏览量')
            ->setCellValue('E1', '分享量')
            ->setCellValue('F1', '首次参与时间')
            ->setCellValue('G1', '手机号')
            ->setCellValue('H1', '省')
            ->setCellValue('I1', '地址')
            ->setCellValue('J1', '中奖')
            ->setCellValue('K1', '获取素材点击数')
            ->setCellValue('L1', '姓名');

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
                ->setCellValue('D' . ($n + 2), $data[$n]['views'])
                ->setCellValue('E' . ($n + 2), $data[$n]['shares'])
                ->setCellValue('F' . ($n + 2), $data[$n]['createtime'])
                ->setCellValue('G' . ($n + 2), $data[$n]['telephone'])
                ->setCellValue('H' . ($n + 2), $data[$n]['province'])
                ->setCellValue('I' . ($n + 2), $data[$n]['address'])
                ->setCellValue('J' . ($n + 2), $awardDes)
                ->setCellValue('K' . ($n + 2), $data[$n]['getsucaiclicknum'])
                ->setCellValue('L' . ($n + 2), $data[$n]['username'])
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
