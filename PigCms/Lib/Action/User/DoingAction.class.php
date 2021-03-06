<?php

class DoingAction extends UserAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $db = M('doing');
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
        $count = M('doing_info')->count();
        $page = new Page($count, 25);
        $list = M('doing_info')->query("SELECT b.addres,b.city,b.tel as tels,b.name as names,a.*,a.share*10+a.number as n FROM `tp_doing_info` as a left join `tp_doing_list` as b on a.tel=b.temptel  order by n desc limit $page->firstRow,$page->listRows"); //第二名和你最近的
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->assign('info', $list);
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
                $qry = M("doing")->where(array('gid' => $_POST['gid']))->save($data);
            } else {
                $qry = M("doing")->add($data);
            }
            if ($qry) {
                $this->success('操作成功', U('Doing/index', array('token' => $token)));
                exit;
            } else {
                $this->error('操作失败');
                exit;
            }
        } else {
            $where = array('gid' => $_GET['gid']);
            $info = M("doing")->where($where)->find();
            $this->assign('info', $info);
            $this->assign('token', $this->token);
            $this->display();
        }
    }

    public function slist() {
        $db = M('doing_info');
        $where = array('gid' => $_GET['gid']);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc')->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
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
        $start = $_POST['start'];
        $end = $_POST['end'];
        $g= $_POST['g'];
        $end=$end-$start;
        $start=$start-1;
        $where='';
        if($g){
            $where= " where a.views>=a.vote  and a.tel!='13083984221' and a.tel!='13376612948' ";
                  
        }
        $list = M('doing_info')->query("SELECT b.addres,b.city,b.tel as tels,b.name as names,a.*,a.share*10+a.number as n FROM `tp_doing_info` as a left join `tp_doing_list` as b on a.tel=b.temptel $where order by n desc limit $start,$end"); //第二名和你最近的
        $i = $start+1;
        foreach ($list as $k => $v) {
            $list[$k]['sort'] = $i;
            $i = $i + 1;
        }

        $filename = $start . "~" . $end . "统计";
        $this->exportexcelx($list, $filename);
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
                ->setCellValue('F1', '城市')
                ->setCellValue('G1', '地址');

        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($n + 2), $data[$n]['sort'])
                    ->setCellValue('B' . ($n + 2), $data[$n]['n'])
                    ->setCellValue('C' . ($n + 2), $data[$n]['tel'])
                    ->setCellValue('D' . ($n + 2), $data[$n]['name'])
                    ->setCellValue('E' . ($n + 2), $data[$n]['tels'])
                    ->setCellValue('F' . ($n + 2), $data[$n]['city'])
                    ->setCellValue('G' . ($n + 2), $data[$n]['addres'])
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
        $db = M('doing_info');
        $sql = "select tel,name,createtime,sharetime,share,views,vote,joins,share*10+number as n from tp_doing_info  where createtime>" . $start . " and createtime<" . $end . " order by createtime desc";
        $list = M()->query($sql);
        foreach ($list as $key => $value) {
            $list[$key]['ID'] = $key;
            $list[$key]['createtime'] = date('Y-m-d H:i:s', $list[$key]['createtime']);
            if ($list[$key]['sharetime'] == 0) {
                $list[$key]['sharetime'] = '无';
            } else {
                $list[$key]['sharetime'] = date('Y-m-d H:i:s', $list[$key]['sharetime']);
            }
        }
        //   $title = array('ID', '电话', '姓名', '参与时间', '首次分享时间', '总分数', '转发数', '浏览数', '点赞数', '扩散数');
        $filename = $starta . "~" . $enda . "统计";
        $this->exportexcel($list, $title, $filename);
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
                ->setCellValue('I1', '点赞数')->setCellValue('j1', '扩散数');
        //写出内容 UTF-8

        for ($n = 0; $n < count($data); $n++) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($n + 2), $data[$n]['ID'])
                    ->setCellValue('B' . ($n + 2), $data[$n]['tel'])
                    ->setCellValue('C' . ($n + 2), $data[$n]['name'])
                    ->setCellValue('D' . ($n + 2), $data[$n]['createtime'])
                    ->setCellValue('E' . ($n + 2), $data[$n]['sharetime'])
                    ->setCellValue('F' . ($n + 2), $data[$n]['n'])
                    ->setCellValue('G' . ($n + 2), $data[$n]['share'])
                    ->setCellValue('H' . ($n + 2), $data[$n]['views'])
                    ->setCellValue('I' . ($n + 2), $data[$n]['vote'])
                    ->setCellValue('j' . ($n + 2), $data[$n]['joins']);
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
