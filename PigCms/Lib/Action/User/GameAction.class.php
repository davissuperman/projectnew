<?php

/**
 * Description of GameAction
 *
 * @author ring
 */
class GameAction extends UserAction {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {


        $db = M('game');
        $where = array('token' => $this->token);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);

        $this->assign('page', $page->show());

        $this->assign('token', $this->token);
        $this->display();
    }

    public function add() {

        if (IS_POST) {
            $data['token'] = $_POST['token'];
            $data['title'] = $_POST['title'];
            $data['defaultbg'] = $_POST['default'];
            $data['bg'] = $_POST['bg'];
            $data['loading'] = $_POST['loading'];
            $data['imageone'] = $_POST['images1'];
            $data['imagetwo'] = $_POST['images2'];
            $data['imagethree'] = $_POST['images3'];
            $data['imagefour'] = $_POST['images4'];
            $data['imagefive'] = $_POST['images5'];
            $data['imagesex'] = $_POST['images6'];
            $data['prize'] = $_POST['prize'];
            $data['progressbg'] = $_POST['progressbg'];
            $data['progress'] = $_POST['progress'];
            $data['down'] = $_POST['down'];
            $data['info'] = $_POST['info'];
            $data['rule'] = $_POST['rule'];
            $data['integral'] = $_POST['integral'];
            $data['button'] = $_POST['button'];
            $data['top'] = $_POST['top'];
            $data['start'] = strtotime($_POST['start']);
            $data['end'] = strtotime($_POST['end']);
            $data['createtime'] = time();
            if ($_POST['gid']) {
                $qry = M("game")->where(array('gid' => $_POST['gid']))->save($data);
            } else {
                $qry = M("game")->add($data);
            }
            if ($qry) {
                $this->success('操作成功', U('Game/index', array('token' => $token)));
                exit;
            } else {
                $this->error('操作失败');
                exit;
            }
        } else {
            $where = array('gid' => $_GET['gid']);
            $info = M("game")->where($where)->find();
            $this->assign('info', $info);
            $this->assign('token', $this->token);
            $this->display();
        }
    }

    public function slist() {
        $db = M('game_info');
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
        $db = M('game_list');
        $where = array('gid' => $_GET['gid']);
        $count = $db->where($where)->count();
        $page = new Page($count, 25);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display('ulist');
    }

    public function export() {
        $starta = $_POST['start'];
        $enda = $_POST['end'];
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $db = M('game_info');
        $sql = "select tel,name,createtime,sharetime,share,views,vote,joins,share*10+number as n from tp_game_info  where createtime>" . $start . " and createtime<" . $end . " order by createtime desc";
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
