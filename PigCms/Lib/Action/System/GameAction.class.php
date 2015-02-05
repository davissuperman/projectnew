<?php

class GameAction extends BackAction {

    public function index() {
        $db = M('bonus_info');
        $count = $db->count();
        $page = new Page($count, 25);
        $info = $db->limit($page->firstRow . ',' . $page->listRows)->order('number desc')->select();
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('token', $this->token);
        $this->display();
    }

    public function rsum() {
        $db = M('bonus_info');
        $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d,COUNT(id) as n, sum(share) as s, sum(views) as v, sum(vote) as vo , sum(joins) as js FROM `tp_bonus_info` group by d";
        $list1 = $db->query($sql);
        $num = count($list1);
        $p = new Page($num, 25);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = $sql . " limit {$firstRow},{$listRows}";
        $list = $db->query($sql1);
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->assign('token', $this->token);
        $this->display();
    }

    public function exportrsum() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $start = $_POST['tstart'] ? $_POST['tstart'] : date('Y-m-d', strtotime('-7 days'));
        $end = $_POST['tend'] ? $_POST['tend'] : date('Y-m-d');
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");
        $sql = "SELECT createtime, DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d,COUNT(id) as n, sum(share) as s, sum(views) as v, sum(vote) as vo , sum(joins) as js FROM `tp_doing_info` where createtime>=$start  and createtime<$end  group by d";
        $info = M('doing_info')->query($sql);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '日期')
                ->setCellValue('B1', '模版数')
                ->setCellValue('C1', '浏览数')
                ->setCellValue('D1', '转发数')
                ->setCellValue('E1', '点赞数')
                ->setCellValue('F1', '扩散数');
        foreach ($info as $k => $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($k + 2), $info[$k]['d'])
                    ->setCellValue('B' . ($k + 2), $info[$k]['n'])
                    ->setCellValue('C' . ($k + 2), $info[$k]['v'])
                    ->setCellValue('D' . ($k + 2), $info[$k]['s'])
                    ->setCellValue('E' . ($k + 2), $info[$k]['vo'])
                    ->setCellValue('F' . ($k + 2), $info[$k]['js']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('每日数据汇总');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function qsum() {
        $this->display();
    }

    public function exportqsum() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $start = $_POST['tstart'] ? $_POST['tstart'] : date('Y-m-d', strtotime('-7 days'));
        $end = $_POST['tend'] ? $_POST['tend'] : date('Y-m-d');
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");

        //微信菜单
        $sql4 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=4 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i4 = M('doing_info')->query($sql4);
        foreach ($i4 as $k => $v) {
            $info4[$v['d']] = $v;
        }
        //学生组
        $sql1 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=1 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i1 = M('doing_info')->query($sql1);
        foreach ($i1 as $k => $v) {
            $info1[$v['d']] = $v;
        }
        //30岁以下素人租
        $sql2 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=2 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i2 = M('doing_info')->query($sql2);
        foreach ($i2 as $k => $v) {
            $info2[$v['d']] = $v;
        }
        //30岁以上素人租
        $sql3 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=3 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i3 = M('doing_info')->query($sql3);
        foreach ($i3 as $k => $v) {
            $info3[$v['d']] = $v;
        }
        //内部用
        $sql5 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=5 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i5 = M('doing_info')->query($sql5);
        foreach ($i5 as $k => $v) {
            $info5[$v['d']] = $v;
        }
        //Jessic 订阅号
        $sql6 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=8 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i6 = M('doing_info')->query($sql6);
        foreach ($i6 as $k => $v) {
            $info6[$v['d']] = $v;
        }
        // PCLADY论坛
        $sql7 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=9 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i7 = M('doing_info')->query($sql7);
        foreach ($i7 as $k => $v) {
            $info7[$v['d']] = $v;
        }
        //网易女人
        $sql8 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=10 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i8 = M('doing_info')->query($sql8);
        foreach ($i8 as $k => $v) {
            $info8[$v['d']] = $v;
        }
        //新浪女性
        $sql9 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=11 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i9 = M('doing_info')->query($sql9);
        foreach ($i9 as $k => $v) {
            $info9[$v['d']] = $v;
        }
        //YOKA
        $sql10 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=12 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i10 = M('doing_info')->query($sql10);
        foreach ($i10 as $k => $v) {
            $info10[$v['d']] = $v;
        }
        //ONLYLADY
        $sql11 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=13 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i11 = M('doing_info')->query($sql11);
        foreach ($i11 as $k => $v) {
            $info11[$v['d']] = $v;
        }
        //森田官方微博
        $sql12 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=14 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i12 = M('doing_info')->query($sql12);
        foreach ($i12 as $k => $v) {
            $info12[$v['d']] = $v;
        }
        //EPR-公众号
        $sqlx = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=15 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i13 = M('doing_info')->query($sqlx);
        foreach ($i13 as $k => $v) {
            $info13[$v['d']] = $v;
        }
        //二次提醒图文
        $sql14 = "SELECT createtime, count(*) as n ,DATE_FORMAT(FROM_UNIXTIME(createtime),'%Y-%m-%d') as d FROM `tp_doing_info` where gid=16 and createtime>=$start  and createtime<$end   group by d "; //微信菜单
        $i14 = M('doing_info')->query($sql14);
        foreach ($i14 as $k => $v) {
            $info14[$v['d']] = $v;
        }


        foreach ($info4 as $key => $value) {
            if (!isset($info1[$value['d']])) {
                $info1[$value['d']]['n'] = 0;
            }
            if (!isset($info2[$value['d']])) {
                $info2[$value['d']]['n'] = 0;
            }
            if (!isset($info3[$value['d']])) {
                $info3[$value['d']]['n'] = 0;
            }
            if (!isset($info4[$value['d']])) {
                $info4[$value['d']]['n'] = 0;
            }
            if (!isset($info5[$value['d']])) {
                $info5[$value['d']]['n'] = 0;
            }
            if (!isset($info6[$value['d']])) {
                $info6[$value['d']]['n'] = 0;
                $info6[$value['d']]['d'] = 0;
            }
            if (!isset($info7[$value['d']])) {
                $info7[$value['d']]['n'] = 0;
            }
            if (!isset($info8[$value['d']])) {
                $info8[$value['d']]['n'] = 0;
            }
            if (!isset($info9[$value['d']])) {
                $info9[$value['d']]['n'] = 0;
            }
            if (!isset($info10[$value['d']])) {
                $info10[$value['d']]['n'] = 0;
            }
            if (!isset($info11[$value['d']])) {
                $info11[$value['d']]['n'] = 0;
            }
            if (!isset($info12[$value['d']])) {
                $info12[$value['d']]['n'] = 0;
            }
            if (!isset($info13[$value['d']])) {
                $info13[$value['d']]['n'] = 0;
            }
            if (!isset($info14[$value['d']])) {
                $info14[$value['d']]['n'] = 0;
            }
        }


        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '日期')
                ->setCellValue('B1', '学生组')
                ->setCellValue('C1', '30岁以下素人租')
                ->setCellValue('D1', '30岁以上素人租')
                ->setCellValue('E1', '微信菜单')
                ->setCellValue('F1', '内部用')
                ->setCellValue('G1', 'Jessic 订阅号')
                ->setCellValue('H1', 'PCLADY论坛')
                ->setCellValue('I1', '网易女人')
                ->setCellValue('J1', '新浪女性')
                ->setCellValue('K1', 'YOKA')
                ->setCellValue('L1', 'ONLYLADY')
                ->setCellValue('M1', '森田官方微博')
                ->setCellValue('N1', 'EPR-公众号')
                ->setCellValue('O1', '二次提醒图文');
        $i = 0;


        foreach ($info4 as $k => $value) {

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($i + 2), $value['d'])
                    ->setCellValue('B' . ($i + 2), $info1[$value['d']]['n'])
                    ->setCellValue('C' . ($i + 2), $info2[$value['d']]['n'])
                    ->setCellValue('D' . ($i + 2), $info3[$value['d']]['n'])
                    ->setCellValue('E' . ($i + 2), $info4[$value['d']]['n'])
                    ->setCellValue('F' . ($i + 2), $info5[$value['d']]['n'])
                    ->setCellValue('G' . ($i + 2), $info6[$value['d']]['n'])
                    ->setCellValue('H' . ($i + 2), $info7[$value['d']]['n'])
                    ->setCellValue('I' . ($i + 2), $info8[$value['d']]['n'])
                    ->setCellValue('J' . ($i + 2), $info9[$value['d']]['n'])
                    ->setCellValue('K' . ($i + 2), $info10[$value['d']]['n'])
                    ->setCellValue('L' . ($i + 2), $info11[$value['d']]['n'])
                    ->setCellValue('M' . ($i + 2), $info12[$value['d']]['n'])
                    ->setCellValue('N' . ($i + 2), $info13[$value['d']]['n'])
                    ->setCellValue('O' . ($i + 2), $info14[$value['d']]['n']);

            $i = $i + 1;
        }

        $objPHPExcel->getActiveSheet()->setTitle('每日渠道汇总');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d h:i") . "xsl");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function export() {
        $tstart = strtotime($_REQUEST['tstart']);
        $tend = strtotime($_POST['tend']);
        $start = $_POST['start'];
        $end = $_POST['end'];
        $gs = $_POST['g'];
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $where = "createtime>='$tstart' AND createtime<'$tend'  ";
     
        if ($gs) {
    
            $where = $where .' AND views>=vote';
        }
      //$where
        $info = M('bonus_info')->where($where)->order('createtime desc')->select();
        $tels = array('浏览数', '转发数', '点赞数', '扩散数');
        $str = 'CDEFGHIJKLMNOPQRSTUVWXYZAAABACADAEAFAGAHAIAJAKALAMANAOAPAQARASATAUAVAWAXAYAZBABBBCBDBEBFBGBHBIBJBKBLBMBN';
        $cj = (strtotime($end) - strtotime($start)) / (24 * 3600);
        //处理数据
        foreach ($info as $key => $value) {
            $tel = $value['tel'];
            $ls = M('doing_date')->where("createdate>'$start' AND createdate<'$end' AND tel='$tel'")->order('createdate asc')->select();
            $list = array();
            $lists = array();
            foreach ($ls as $k => $v) {
                $list[$v['createdate']] = $v;
            }
            for ($i = 0; $i < $cj; $i++) {
                $s = date('Y-m-d', strtotime($start) + 24 * 3600 * $i);
                if (!isset($list[$s])) {
                    $lists[$i] = array('shares' => 0, 'views' => 0, 'votes' => 0, 'jons' => 0);
                } else {
                    $lists[$i] = $list[$s];
                }
            }
            $info[$key]['views'] = $lists;
        }
        //输出excel
        foreach ($tels as $key => $value) {
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($key)
                    ->setCellValue('A1', '模版创建日期')
                    ->setCellValue('B1', '模版名称');
            $j = 0;
            for ($i = 0; $i < $cj; $i++) {
                $s = date('Y-m-d', strtotime($start) + 24 * 3600 * $i);
                if ($i <= 23) {
                    $objPHPExcel->setActiveSheetIndex($key)
                            ->setCellValue(substr($str, $i, 1) . '1', $s);
                } else {
                    $objPHPExcel->setActiveSheetIndex($key)
                            ->setCellValue(substr($str, 24 + $j * 2, 2) . '1', $s);
                    $j = $j + 1;
                }
            }
            foreach ($info as $k => $v) {
                $objPHPExcel->setActiveSheetIndex($key)
                        ->setCellValue('A' . ( $k + 2 ), date("Y-m-d", $v['createtime']))
                        ->setCellValue('B' . ( $k + 2 ), $v['tel']);
                $j = 0;
                foreach ($info[$k]['views'] as $ke => $va) {

                    if ($key == 0) {
                        if ($ke <= 23) {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, $ke, 1) . ( $k + 2 ), $va['views']);
                        } else {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, 24 + $j * 2, 2) . ( $k + 2 ), $va['views']);
                            $j = $j + 1;
                        }
                    }
                    if ($key == 1) {

                        if ($ke <= 23) {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, $ke, 1) . ( $k + 2 ), $va['shares']);
                        } else {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, 24 + $j * 2, 2) . ( $k + 2 ), $va['shares']);
                            $j = $j + 1;
                        }
                    }
                    if ($key == 2) {
                        if ($ke <= 23) {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, $ke, 1) . ( $k + 2 ), $va['votes']);
                        } else {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, 24 + $j * 2, 2) . ( $k + 2 ), $va['votes']);
                            $j = $j + 1;
                        }
                    }
                    if ($key == 3) {
                        if ($ke <= 23) {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, $ke, 1) . ( $k + 2 ), $va['jons']);
                        } else {
                            $objPHPExcel->setActiveSheetIndex($key)
                                    ->setCellValue(substr($str, 24 + $j * 2, 2) . ( $k + 2 ), $va['jons']);
                            $j = $j + 1;
                        }
                    }
                }
            }

            $objPHPExcel->getActiveSheet()->setTitle($value);
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . time() . ".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
