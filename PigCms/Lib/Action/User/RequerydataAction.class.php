<?php

class RequerydataAction extends UserAction {

    public $token; //为什么一改在private或者protected就不行了呢
    public $mysql;

    public function _initialize() {
        parent::_initialize();
        $this->token = session('token');
        $this->mysql = M('Requestdata');
    }

    /**
     * CRM 统计
     */
    public function crm() {
        $start = $_POST['start'] ? $_POST['start'] : date('Y-m-d', strtotime('-7 days'));
        $end = $_POST['end'] ? $_POST['end'] : date('Y-m-d');
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");
        $listc = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and starttime<$end group by a");
        $listw = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and  starttime<$end and u_member>=1 group by a");
        $listb = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and  starttime<$end and u_form='活动' group by a");
        $listy = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and  starttime<$end and u_form='年终奖品' group by a");
        $listg = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and  starttime<$end and u_form='官网' group by a");
        $lists = M('member_user')->query("select DATE_FORMAT(FROM_UNIXTIME(starttime),'%Y-%m-%d') as a,COUNT(*) as c from tp_member_user  where starttime>$start  and  starttime<$end and u_form='商城' group by a");


        foreach ($listc as $k => $v) {
            $listc[$v['a']] = $v;
        }
        foreach ($listw as $k => $v) {
            $listw[$v['a']] = $v;
        }
        foreach ($listb as $k => $v) {
            $listb[$v['a']] = $v;
        }
        foreach ($listy as $k => $v) {
            $listy[$v['a']] = $v;
        }
        foreach ($listg as $k => $v) {
            $listg[$v['a']] = $v;
        }
        foreach ($lists as $k => $v) {
            $lists[$v['a']] = $v;
        }
        $list = array();
        $i=0;
        for ($s = $start; $s <= $end; $s += 86400) {  //我这里是按每小时遍历，所以每次增加3600秒
            $ymd = date('Y-m-d', $s);
            $list[$i]['time'] = $s;
            if (isset($listc[$ymd])) {
                $list[$i]['c'] = $listc[$ymd]['c'];
            } else {
                $list[$i]['c'] = 0;
            }
            if (isset($listw[$ymd])) {
                $list[$i]['w'] = $listw[$ymd]['c'];
            } else {
                $list[$i]['w'] = 0;
            }
            if (isset($listb[$ymd])) {
                $list[$i]['b'] = $listb[$ymd]['c'];
            } else {
                $list[$i]['b'] = 0;
            }
            if (isset($listy[$ymd])) {
                $list[$i]['y'] = $listw[$ymd]['c'];
            } else {
                $list[$i]['y'] = 0;
            }
            if (isset($listg[$ymd])) {
                $list[$i]['g'] = $listg[$ymd]['c'];
            } else {
                $list[$i]['g'] = 0;
            }
            if (isset($lists[$ymd])) {
                $list[$i]['s'] = $lists[$ymd]['c'];
            } else {
                $list[$i]['s'] = 0;
            }
            $i=$i+1;
        }
       

        $xml = '<chart caption="统计图" xAxisName="日期" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';
        $c = '';
        $w = '';
        $b = '';
        $y = '';
        $g = '';
        $s = '';
        foreach ($list as $li) {
            $day = date('d', $li['time']);
            $xml.='<category label="' . $day . '"/>';
            $c.='<set value="' . $li['c'] . '"/>';
            $w.='<set value="' . $li['w'] . '"/>';
            $b.='<set value="' . $li['b'] . '"/>';
            $y.='<set value="' . $li['y'] . '"/>';
            $s.='<set value="' . $li['s'] . '"/>';
            $g.='<set value="' . $li['g'] . '"/>';
        }
        $xml.='</categories>';
        $xml.='<dataset seriesName="CRM会员数" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $c . '</dataset>';
        $xml.='<dataset seriesName="微信会员" color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A">' . $w . '</dataset>';
        $xml.='<dataset seriesName="流程B" color="663333" anchorBorderColor="663333" anchorBgColor="663333">' . $b . '</dataset>';
        $xml.='<dataset seriesName="年终奖品" color="9900FF" anchorBorderColor="9900FF" anchorBgColor="9900FF">' . $y . '</dataset>';
        $xml.='<dataset seriesName="官网" color="CCCC00" anchorBorderColor="CCCC00" anchorBgColor="CCCC00">' . $s . '</dataset>';
        $xml.='<dataset seriesName="商城" color="FF33FF" anchorBorderColor="FF33FF" anchorBgColor="FF33FF">' . $g . '</dataset>';
        $xml.='</chart>';
        $this->assign('xml', $xml);
        $this->display();
    }

    public function index() {
        if ($this->_get('month') == false) {
            $month = date('m');
        } else {
            $month = $this->_get('month');
        }
        $thisYear = date('Y');
        if ($this->_get('year') == false) {
            $year = $thisYear;
        } else {
            $year = $this->_get('year');
        }
        $this->assign('month', $month);
        $this->assign('year', $year);
        $lastyear = $thisYear - 1;
        if ($year == $lastyear) {
            $yearOption = '<option value="' . $lastyear . '" selected>' . $lastyear . '</option><option value="' . $thisYear . '">' . $thisYear . '</option>';
        } else {
            $yearOption = '<option value="' . $lastyear . '">' . $lastyear . '</option><option value="' . $thisYear . '" selected>' . $thisYear . '</option>';
        }
        $this->assign('yearOption', $yearOption);
        $tp_requestdata = M('requestdata');
        $tokenn = $this->token;
        $start = 1;
        $thisMonth = date('n'); //取当前日期中的月
        $thisYear = date('Y'); //取当前日期中的月
        if (($month >= $thisMonth) && ( $year >= $thisYear )) {
            $end = date('j'); //取当前日期中的天
        } else {
            $end = date('t', strtotime($year . '-' . $month . '-01 00:00:00'));
        }
        $list = array();
        for ($i = 1; $i <= $end; $i++) {
            $sql = "SELECT time,sum(textnum) AS textnum,sum(imgnum) as imgnum,sum(videonum)as videonum, sum(other)as other,sum(follownum)as follownum,sum(unfollownum)as unfollownum,sum(3g) as 3g 
	        FROM tp_requestdata 
	        WHERE token = '$tokenn' 
	        AND month = '$month' 
	        AND year = '$year'  
	        AND day = '$i' 
	         ";
            $data = $tp_requestdata->query($sql);
            $data = $data[0];
            if (empty($data['time'])) {
                $data['time'] = strtotime($year . '-' . $month . '-' . $i);
            }
            if (empty($data['textnum'])) {
                $data['textnum'] = 0;
            }
            if (empty($data['imgnum'])) {
                $data['imgnum'] = 0;
            }
            if (empty($data['videonum'])) {
                $data['videonum'] = 0;
            }
            if (empty($data['other'])) {
                $data['other'] = 0;
            }
            if (empty($data['follownum'])) {
                $data['follownum'] = 0;
            }
            if (empty($data['unfollownum'])) {
                $data['unfollownum'] = 0;
            }
            if (empty($data['3g'])) {
                $data['3g'] = 0;
            }
            $list[] = $data;
        }
        //var_dump($list);
        $this->assign('list', $list);
        $this->assign('page', $page);

        $xml = '<chart caption="' . $month . '月统计图" xAxisName="日期" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';
        $fansCountSet = '';
        $imgRequryCountSet = '';
        foreach ($list as $li) {
            $day = date('d', $li['time']);
            $xml.='<category label="' . $day . '"/>';
            $fansCountSet.='<set value="' . $li['follownum'] . '"/>';
            $imgRequryCountSet.='<set value="' . $li['textnum'] . '"/>';
        }
        $xml.='</categories><dataset seriesName="关注数" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $fansCountSet . '</dataset><dataset seriesName="文本请求数" color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A">' . $imgRequryCountSet . '</dataset><styles><definition><style name="CaptionFont" type="font" size="12"/></definition><application><apply toObject="CAPTION" styles="CaptionFont"/><apply toObject="SUBCAPTION" styles="CaptionFont"/></application></styles></chart>';
        $this->assign('xml', $xml);
        //rint_r($xml);
        // exit;
        $this->display();
    }

    /**
     * menuClick() 自定义菜单点击统计
     * add by wuhaiyan 2014/3/21
     *
     */
    public function menuClick() {
        $where[] = array();
        $where['token'] = $this->token;
        $where['pid'] = 0;
        $where['is_show'] = 1;
        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = date('Y-m-d');
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");
        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        }
        $time_range = array('BETWEEN', "{$start},{$end}");

        $tsite = C('site_url'); //调用此站url，用于下面的匹配
        $thisSite = parse_url($tsite);
        $thisHost = str_replace('.', '\.', $thisSite['host']);

        $pMenus = M('Diymen_class')->where($where)->order('sort asc')->select();
        $Menus = array(); //这是要输出到页的点击量数据
        $click = array(); //这是为导出所准备的数据 这是一个二纬数组，把子菜单和一级菜单同级
        foreach ($pMenus as $k => $v) {
            $mid = $v['id']; //查出一级菜单的id
            $Menus[$k]['id'] = $mid;
            $Menus[$k]['title'] = $v['title'];
            $Menus[$k]['keyword'] = $v['keyword'];
            $Menus[$k]['url'] = $v['url'];
            $mWhere['mid'] = $mid;
            $mWhere['token'] = $this->token;
            $mWhere['click_time'] = $time_range;
            if (!empty($v['url'])) {//如果菜单是链接则执行下面的程序
                $Menus[$k]['clicknum'] = M('User_menu_click')->where($mWhere)->count();
                if (!preg_match('/' . $thisHost . '/', $v['url'])) {//如果是外站链接不统计到达数和到达率
                    $Menus[$k]['reachednum'] = '--';
                    $Menus[$k]['reachedPer'] = '--';
                    $click[] = $Menus[$k];
                } else {
                    $Menus[$k]['reachednum'] = M('User_menu_click')->where(array('mid' => $mid, 'token' => $this->token, 'click_time' => $time_range, 'is_reached' => 1))->count();
                    $Menus[$k]['reachedPer'] = sprintf('%.2f%%', $Menus[$k]['reachednum'] / $Menus[$k]['clicknum'] * 100); //无子菜单的一级菜单到达率统计
                    $click[] = $Menus[$k];
                }
            } else {
                $cmWhere['pid'] = $mid;
                $cmWhere['token'] = $this->token;
                $cmWhere['is_show'] = 1;
                $childMenus = M('Diymen_class')->where($cmWhere)->select();
                if (!$childMenus) {//如果没有子菜单
                    $Menus[$k]['clicknum'] = M('User_menu_click')->where($mWhere)->count();
                    $Menus[$k]['reachednum'] = M('User_menu_click')->where(array('mid' => $mid, 'token' => $this->token, 'click_time' => $time_range, 'is_reached' => 1))->count();
                    $Menus[$k]['reachedPer'] = sprintf('%.2f%%', $Menus[$k]['reachednum'] / $Menus[$k]['clicknum'] * 100); //无子菜单的一级菜单到达率统计
                    $click[] = $Menus[$k];
                } else {
                    $Menus[$k]['clicknum'] = 0;
                    //如果有子菜单，一级菜单点击量就是子菜单点击量之和
                    $Menus[$k]['child'] = array();
                    $chlidData = array();
                    for ($i = 0; $i < count($childMenus); $i++) {
                        $ccWhere['mid'] = $childMenus[$i]['id'];
                        $ccWhere['token'] = $this->token;
                        $ccWhere['click_time'] = $time_range;
                        $chlidData[$i]['id'] = $childMenus[$i]['id'];
                        $chlidData[$i]['title'] = $childMenus[$i]['title'];
                        $chlidData[$i]['keyword'] = $childMenus[$i]['keyword'];
                        $chlidData[$i]['url'] = $childMenus[$i]['url'];
                        $chlidData[$i]['clicknum'] = M('User_menu_click')->where($ccWhere)->count();
                        $Menus[$k]['clicknum'] += $chlidData[$i]['clicknum'];

                        //子菜单到达数、到达率统计	
                        if (!empty($childMenus[$i]['url']) && (!preg_match('/' . $thisHost . '/', $childMenus[$i]['url']) )) {//如果是外站链接不统计
                            $chlidData[$i]['reachednum'] = '--';
                            $chlidData[$i]['reachedPer'] = '--';
                        } else {
                            $chlidData[$i]['reachednum'] = M('User_menu_click')->where(array('mid' => $childMenus[$i]['id'], 'token' => $this->token, 'click_time' => $time_range, 'is_reached' => 1))->count();
                            $chlidData[$i]['reachedPer'] = sprintf('%.2f%%', $chlidData[$i]['reachednum'] / $chlidData[$i]['clicknum'] * 100);
                        }
                        $click[] = $chlidData[$i];
                        $Menus[$k]['reachednum'] += intval($chlidData[$i]['reachednum']);
                    }
                    $Menus[$k]['reachedPer'] = sprintf('%.2f%%', $Menus[$k]['reachednum'] / $Menus[$k]['clicknum'] * 100); //有子菜单的一级菜单到达率统计
                    $Menus[$k]['child'] = $chlidData;
                    $ddd = $Menus[$k];
                    unset($ddd['child']);
                    $click[] = $ddd;
                }
            }
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        $this->assign('Menus', $Menus);
        $_SESSION['menuClick'] = $click;
        $_SESSION['start'] = date('Y-m-d', $start);
        $_SESSION['end'] = date('Y-m-d', $end);
        $this->display();
    }

    /**
     * 菜单点击量导出
     *
     */
    public function menuClickImport() {
        if ($_GET['token'] != $this->token) {
            echo "非法操作";
            exit();
        }
        $data = $_SESSION['menuClick'];
        $title = array('编号', '菜单名称', '关键词', '网址', '到达数', '点击次数', '到达率');
        $filename = $_SESSION['start'] . "~" . $_SESSION['end'] . "自定义菜单统计";
        $this->exportexcel($data, $title, $filename);
        unset($_SESSION['menuClick']);
        unset($_SESSION['start']);
        unset($_SESSION['end']);
    }

    /**
     * keywordsClick() 自定义菜单点击统计
     * add by wuhaiyan 2014/3/21
     *
     */
    public function keywordsClick() {
        //查询条件
        $where = " where token = '" . $this->token . "' ";
        unset($_SESSION['matched']);
        if ($_GET['matched'] == 1) {
            $matched = intval($_GET['matched']);
            $where .= 'and matched = ' . $matched;
            $this->assign('matched', $matched);
            $_SESSION['matched'] = $matched;
        }

        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = date('Y-m-d');
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }

        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        } elseif ($end == $start) {
            $ctime = ' click_hour as ctime';
            $group = ' group by click_hour';
            $chartBottomTime = 'h';
            $_SESSION['ctime'] = 'click_hour';
            $time_range = " and click_date = '{$start}'";
        } else {
            $ctime = ' click_date as ctime';
            $group = ' group by click_date';
            $chartBottomTime = 'y/m/d';
            $_SESSION['ctime'] = 'click_date';
            $time_range = " and click_date >= '" . $start . "' and click_date <= '" . $end . "' ";
        }
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;

        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);
        $sql = "select keyword from tp_user_keyword_click " . $where . " group by keyword";
        $data = M()->query($sql); //查出关键词统计类
        $count = count($data);
        $p = new Page($count, 10);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = "select keyword,count(id) as clicknum,matched from tp_user_keyword_click " . $where . " group by keyword order by clicknum desc limit {$firstRow},{$listRows}";
        $list = M()->query($sql1); //查出关键词统计类
        $this->assign('click', $list);
        $page = $p->show();
        $this->assign('page', $page);

        $sql2 = 'select ' . $ctime . ',count(id) as clicknum from tp_user_keyword_click ' . $where . $group;
        $xdata = M()->query($sql2);
        $this->assign('xdata', $xdata);
        //生成统计图
        if ($matched == 1) {
            $title = '关键词命中次数变化图';
        } else {
            $title = '关键词触发次数变化图';
        }
        $xml = '<chart caption="' . $title . '" xAxisName="时间" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';
        $fansCountSet = '';
        $imgRequryCountSet = '';
        foreach ($xdata as $li) {
            $day = $li['ctime'];
            $xml .='<category label="' . $day . '"/>';
            $fansCountSet .='<set value="' . $li['clicknum'] . '"/>';
        }
        $xml.='</categories><dataset seriesName="关键词触发总次数" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $fansCountSet . '</dataset><styles><definition><style name="CaptionFont" type="font" size="12"/></definition><application><apply toObject="CAPTION" styles="CaptionFont"/><apply toObject="SUBCAPTION" styles="CaptionFont"/></application></styles></chart>';
        $this->assign('xml', $xml);
        unset($_POST['hash']);
        $match = array(
            0 => '否',
            1 => '是'
        );
        $this->assign('match', $match);
        $this->display();
    }

    /**
     * 关键词导出
     *
     */
    public function keywordsImport() {
        if ($_GET['token'] != $this->token) {
            echo "非法操作";
            exit();
        }
        $match = array(
            0 => '否',
            1 => '是'
        );
        $start = $_SESSION['start'];
        $end = $_SESSION['end'];
        $where = "where token = '" . $this->token . "' ";
        if ($_SESSION['matched'] === 1) {
            $where .= " and matched = " . $_SESSION['matched'];
            $title = array('日期', '关键词', '命中次数');
            $filename = $start . "~" . $end . "命中关键词";
        } else {
            $m = ',matched';
            $title = array('日期', '关键词', '触发次数', '是否命中');
            $filename = $start . "~" . $end . "触发关键词";
        }
        if ($_SESSION['ctime'] == 'click_hour') {
            $where .= " and click_date ='" . $start . "'";
        } else {
            $where .= " and click_date >= '{$start}' and click_date <= '{$end}' ";
        }
        $sql = "select FROM_UNIXTIME(click_time,'%Y-%m-%d') as d,keyword,count(id) as clicknum" . $m . " from tp_user_keyword_click " . $where . " group by d,keyword order by d desc,clicknum desc";
        $data = M()->query($sql);
        if ($_SESSION['matched'] != 1) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['matched'] = $match[$data[$i]['matched']];
            }
        }
        $this->exportexcel($data, $title, $filename);
    }

    /**
     * LBS统计
     *
     */
    public function companyNav() {
        //查询条件
        $where = " where token = '" . $this->token . "' ";
        if (!empty($_GET['type'])) {
            $type = intval($_GET['type']);
            $where .= 'and type = ' . $type;
            $this->assign('type', $type);
            $_SESSION['type'] = $type;
        }

        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = date('Y-m-d');
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }

        //$start = strtotime( $start." 00:00:00" );
        //$end = strtotime( $end." 23:59:59" );
        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        }
        $time_range = " and date >= '{$start}' and date <= '{$end}' ";
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;

        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        $sql = "select shop_id from tp_company_nav " . $where . " group by shop_id";
        $data = M()->query($sql);
        $count = count($data);
        $p = new Page($count, 20);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = "select  shop_id,count(id) as navnum from tp_company_nav " . $where . " group by shop_id order by navnum desc limit {$firstRow},{$listRows}";
        $list = M()->query($sql1);
        if (!empty($list)) {
            $nav = array();
            foreach ($list as $k => $v) {
                $thisShop = M('Company')->field('name')->where(array('token' => $this->token, 'id' => $v['shop_id']))->find();
                $nav[$k]['shop_name'] = $thisShop['name'];
                $nav[$k]['navnum'] = $v['navnum'];
            }
            $this->assign('navs', $nav);
            $page = $p->show();
            $this->assign('page', $page);
            $this->assign('navtop', array_slice($nav, 0, 10)); //取出前10条店铺数据
            $_SESSION['nav'] = $nav;
        }
        unset($_POST['hash']);
        $this->display();
    }

    /**
     * lbs相关数据导出
     *
     */
    public function companyNavImport() {
        if ($_GET['token'] != $this->token) {
            echo "非法操作";
            exit();
        }
        $data = $_SESSION['nav'];
        $type = $_SESSION['type'];
        $title = array('店铺名称', '导航次数');
        if ($_SESSION['type'] == 2) {
            $t = '活动店铺导航次数';
        } else {
            $t = '店铺导航次数';
        }
        $filename = $_SESSION['start'] . "~" . $_SESSION['end'] . $t;
        $this->exportexcel($data, $title, $filename);
        unset($_SESSION['nav']);
        unset($_SESSION['start']);
        unset($_SESSION['end']);
        unset($_SESSION['type']);
    }

    /**
     * 用户分析，列出一个时间段内的用户
     *
     */
    public function users() {
        $where = "where token = '" . $this->token . "' ";

        //时间范围处理
        //开始时间
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = '2014-01-01';
        }
        //结束时间
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");
        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        }
        $time_range = " and subscribe_time >= '{$start}' and subscribe_time <= '{$end}' ";
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;
        $_SESSION['time_range'] = $time_range;

        //给出三个筛选条件的默认值，不然页面显示选中状态有误
        $tp = -1;
        $ts = -1;
        $tatt = -1;
        //省的筛选
        if (isset($_GET['pro']) && $_GET['pro'] != -1) {
            $tp = trim($_GET['pro']);
            $where .= " and province = '" . $tp . "'";
            $_SESSION['province'] = $tp;
        }
        $this->assign('tp', $tp);

        //性别筛选
        if (isset($_GET['sex'])) {
            $ts = intval($_GET['sex']);
            if ($ts == 0 || $ts == 1 || $ts == 2) {
                $where .= " and sex = " . $ts;
                $_SESSION['sex'] = $ts;
            }
        }
        $this->assign('ts', $ts);

        //关注状态筛选
        if (isset($_GET['att'])) {
            $tatt = intval($_GET['att']);
            if ($tatt == 0 || $tatt == 1) {
                $where .= " and subscribe = " . $tatt;
                $_SESSION['subscribe'] = $tatt;
            }
        }
        $this->assign('tatt', $tatt);

        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        //调出目前有用户的所有省
        $province = M('Customer_service_fans')->field('province')->where(array('token' => $this->token))->group('province')->select();
        $this->assign('province', $province);

        $sql = "select count(openid) as c from tp_customer_service_fans " . $where;
        $data = M()->query($sql);
        $count = $data[0]['c'];
        $p = new Page($count, 10);
        $firstRow = $p->firstRow;
        $listRows = $p->listRows;
        $sql1 = "select * from tp_customer_service_fans " . $where . " order by subscribe_time desc  limit {$firstRow},{$listRows}";
        $list = M()->query($sql1); //查出符合条件的用户
        $this->assign('users', $list);
        $page = $p->show();
        $this->assign('page', $page);
        $g = M('Customer_service_fans_group')->field('gid,groupname')->where(array('token' => $this->token))->select();
        $groups = array();
        foreach ($g as $k => $v) {
            $groups[$v['gid']] = $v['groupname'];
        }
        $_SESSION['groups'] = $groups;
        $this->assign('groups', $groups);
        $sex = array(
            0 => '未知',
            1 => '男',
            2 => '女'
        );
        $this->assign('sex', $sex);
        $attention = array(
            0 => '已取消关注',
            1 => '关注中'
        );
        $this->assign('att', $attention);
        $this->display();
    }

    /**
     * 用户分析导出
     *
     */
    public function usersImport() {
        if ($_GET['token'] != $this->token) {
            echo "非法操作";
            exit();
        }
        $where = ' where 1 = 1' . $_SESSION['time_range'];
        $filename = date('Y-m-d', $_SESSION['start']) . "~" . date('Y-m-d', $_SESSION['end']);
        if ($_SESSION['province']) {
            $where .= " and  province = '" . $_SESSION['province'] . "'";
        }

        $attention = array(
            0 => '已取消关注',
            1 => '关注中'
        );
        if ($_SESSION['subscribe']) {
            $where .= " and  subscribe = '" . $_SESSION['subscribe'] . "'";
        }

        $sex = array(
            0 => '未知',
            1 => '男',
            2 => '女'
        );
        if ($_SESSION['sex']) {
            $where .= " and  sex = '" . $_SESSION['sex'] . "'";
        }

        $sql = "select openid,nickname,remark,sex,subscribe,province,gid from tp_customer_service_fans {$where} order by subscribe_time desc";
        $data = M()->query($sql);
        for ($i = 0; $i < count($data); $i++) {
            array_unshift($data[$i], $i + 1);
            $data[$i]['sex'] = $sex[$data[$i]['sex']];
            $data[$i]['subscribe'] = $attention[$data[$i]['subscribe']];
            $data[$i]['gid'] = $_SESSION['groups'][$data[$i]['gid']];
        }
        $filename .= '用户';
        $title = array('编号', '微信ID', '昵称', '客户备注', '性别', '关注', '地域', '所属分组');
        $this->exportexcel($data, $title, $filename);

        unset($_SESSION['start']);
        unset($_SESSION['end']);
        unset($_SESSION['time_range']);
        unset($_SESSION['province']);
        unset($_SESSION['subscribe']);
        unset($_SESSION['sex']);
    }

    /**
     * 用户统计
     *
     */
    public function usersChange() {
        $where = " where token = '" . $this->token . "' ";
        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = '2014-01-01';
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }
        //结束时间不能小于开始时间
        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        } elseif ($end == $start) {
            $group = '  group by hour ';
            $ctime = 'hour';
            $chartTimeFormat = 'h';
        } else {
            $group = " group by date ";
            $ctime = 'date';
            $chartTimeFormat = 'y/m/d';
        }

        $time_range = " and date >= '{$start}' and date <= '{$end}' ";
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;

        //查出总用户数
        $sql_all_user = "select count(openid) as c from tp_customer_service_fans where token='" . $this->token . "' and subscribe_time <=" . strtotime($end . ' 23:59:59');
        $all_users = M()->query($sql_all_user);
        $all_users = $all_users ? $all_users[0]['c'] : 0;
        if ($all_users) {
            //总活跃用户数
            $sql_all_active = "select count(openid) as c from tp_user_action where token='" . $this->token . "' and date <= '" . $end . "' and type = 'active_user' group by openid ";
            $all_actives = M()->query($sql_all_active);
            $all_actives = $all_actives ? $all_actives[0]['c'] : 0;
            $active_per = sprintf('%.2f%%', $all_actives / $all_users * 100); //总活跃用户占比
            //总绑定用户数
            $sql_all_binding = "select count(openid) as c from tp_user_action where token='" . $this->token . "' and date <= '" . $end . "' and type = 'binding'  group by openid ";
            $all_bindings = M()->query($sql_all_binding);
            $all_bindings = $all_bindings ? $all_bindings[0]['c'] : 0;
            $binding_per = sprintf('%.2f%%', $all_bindings / $all_users * 100); //总绑定用户占比
        } else {
            $all_actives = 0;
            $active_per = 0;
            $all_bindings = 0;
            $binding_per = 0;
        }
        $this->assign('all_users', $all_users);
        $this->assign('all_actives', $all_actives);
        $this->assign('active_per', $active_per);
        $this->assign('all_bindings', $all_bindings);
        $this->assign('binding_per', $binding_per);

        //这是要准备的文本数据
        //之前的活跃用户数量
        $asql = "select COUNT(DISTINCT openid) AS num from tp_user_action where token = '" . $this->token . "' and time < " . strtotime($start . ' 00:00:00') . "  and type= 'active_user' ";
        $as = M()->query($asql);
        $mytext['activeNum'] = $as ? $as[0]['num'] : 0;
        //之前的绑定用户
        $asql = "select COUNT(DISTINCT openid) AS num from tp_user_action where token = '" . $this->token . "' and time < " . strtotime($start . ' 00:00:00') . "  and type= 'binding' ";
        $bs = M()->query($asql);
        $mytext['bindingNum'] = $bs ? $bs[0]['num'] : 0;

        $sql = 'select ' . $ctime . ' as ctime from tp_user_action ' . $where . $group;
        $data = M()->query($sql);
        $udata = array();
        foreach ($data as $key => $value) {
            $udata[$key] = $value;
            //关注数据调用
            $sql1 = "select COUNT(DISTINCT openid) AS num from tp_user_action where  token = '" . $this->token . "' and type = 'subscribe' and " . $ctime . " = '" . $value['ctime'] . "' ";
            $sub = M()->query($sql1);
            $udata[$key]['subscribe'] = $sub ? $sub[0]['num'] : 0;
            //取消关注数据调用
            $sql2 = "select COUNT(DISTINCT openid) AS num from tp_user_action where  token = '" . $this->token . "' and type = 'unsubscribe' and " . $ctime . " = '" . $value['ctime'] . "' ";
            $unsub = M()->query($sql2);
            $udata[$key]['unsubscribe'] = $unsub ? $unsub[0]['num'] : 0;
            //活跃度数据调用
            $sql3 = "select COUNT(DISTINCT openid) AS num from tp_user_action where  token = '" . $this->token . "' and  type = 'active_user' and " . $ctime . " = '" . $value['ctime'] . "' ";
            $active = M()->query($sql3);
            $udata[$key]['active_user'] = $active ? $active[0]['num'] : 0;
            $mytext['activeNum'] += $udata[$key]['active_user']; //累加出活跃用户数
            //绑定数据调用
            $sql4 = "select COUNT(DISTINCT openid) AS num from tp_user_action where  token = '" . $this->token . "' and  type = 'binding' and " . $ctime . " = '" . $value['ctime'] . "' ";
            $binding = M()->query($sql4);
            $udata[$key]['binding'] = $binding ? $binding[0]['num'] : 0;
            $mytext['bindingNum'] += $udata[$key]['binding']; //累加出绑定用户
        }
        $this->assign('udata', $udata);
        $_SESSION['udata'] = $udata;
        $xml = '<chart caption="每日变化趋势" xAxisName="时间" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';
        $fansCountSet = '';
        $imgRequryCountSet = '';
        foreach ($udata as $li) {
            $day = $li['ctime'];
            $xml .= '<category label="' . $day . '"/>';
            $c1 .= '<set value="' . $li['subscribe'] . '"/>';
            $c2 .= '<set value="' . $li['unsubscribe'] . '"/>';
            $c3 .= '<set value="' . $li['active_user'] . '"/>';
            $c4 .= '<set value="' . $li['binding'] . '"/>';
        }
        $xml .= '</categories><dataset seriesName="新增关注" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $c1 . '</dataset><dataset seriesName="取消关注" color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A">' . $c2 . '</dataset><dataset seriesName="活跃用户" color="ff0000" anchorBorderColor="ff0000" anchorBgColor="ff0000">' . $c3 . '</dataset><dataset seriesName="绑定用户" color="000000" anchorBorderColor="000000" anchorBgColor="000000">' . $c4 . '</dataset><styles><definition><style name="CaptionFont" type="font" size="12"/></definition><application><apply toObject="CAPTION" styles="CaptionFont"/><apply toObject="SUBCAPTION" styles="CaptionFont"/></application></styles></chart>';
        $this->assign('xml', $xml);


        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        //用户累计数量表数据调用及统计图生成
        //查出天
        $sql5 = "select FROM_UNIXTIME(time,'%Y-%m-%d') as d from tp_user_action 
			where token = '" . $this->token . "' 
			and time >" . (strtotime($start . " 00:00:00") - 1) . " 
			and time < " . (strtotime($end . " 23:59:59") + 2) . " 
			group by d order by d ";
        $sd = M()->query($sql5);
        $s_sql = "select COUNT(DISTINCT openid) AS num from tp_customer_service_fans 
			where token = '" . $this->token . "' 
			and subscribe_time <" . strtotime($start . " 00:00:00");
        $ss = M()->query($s_sql);
        $startnum = $ss[0]['num']; //初始用户数
        $users = array();
        for ($n = 0; $n < count($sd); $n++) {
            $users[$n]['date'] = $sd[$n]['d'];
            $sql_n = "select COUNT(DISTINCT openid) AS num from tp_user_action 
				where token = '" . $this->token . "' 
				and date = '" . $sd[$n]['d'] . "'  
				and type = 'subscribe' 
				";
            $sn = M()->query($sql_n);
            $thisnum = $sn[0]['num'] ? $sn[0]['num'] : 0;
            if ($n == 0) {
                $users[$n]['num'] = $startnum + $thisnum;
            } else {
                $users[$n]['num'] = $users[$n - 1]['num'] + $thisnum;
            }
        }
        $endData = end($users);
        $mytext['userNum'] = $endData['num'];
        $mytext['activePer'] = sprintf('%.2f%%', $mytext['activeNum'] / $mytext['userNum'] * 100);
        $mytext['bindingPer'] = sprintf('%.2f%%', $mytext['bindingNum'] / $mytext['userNum'] * 100);
        $this->assign('mytext', $mytext); //这是要输出到页面的文本数据

        $xml2 .= '<chart caption="每日累计趋势" xAxisName="累计关注数" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';

        $fansCountSet2 = '';
        $imgRequryCountSet2 = '';
        foreach ($users as $li2) {
            $day2 = $li2['date'];
            $xml2 .='<category label="' . $day2 . '"/>';
            $fansCountSet2 .='<set value="' . $li2['num'] . '"/>';
        }
        $xml2 .='</categories><dataset seriesName="累计关注数" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $fansCountSet2 . '</dataset><styles><definition><style name="CaptionFont" type="font" size="12"/></definition><application><apply toObject="CAPTION" styles="CaptionFont"/><apply toObject="SUBCAPTION" styles="CaptionFont"/></application></styles></chart>';
        $this->assign('xml2', $xml2);
        $this->display();
    }

    /**
     * 用户统计导出
     *
     */
    public function usersChangeImport() {
        if (empty($_SESSION['udata'])) {
            $this->error('无数据');
        }
        $udata = $_SESSION['udata'];
        $filename = $_SESSION['start'] . "~" . $_SESSION['end'] . "日用户分析";
        $title = array('日期', '新增关注', '取消关注', '活跃用户', '绑定用户');
        $this->exportexcel($udata, $title, $filename);
        unset($_SESSION['start']);
        unset($_SESSION['end']);
        unset($_SESSION['udata']);
    }

    /**
     * visitors()客服统计－－浏量统计
     *
     */
    public function visitors() {
        $where = " where token = '" . $this->token . "' ";
        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = date('Y-m-d');
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");

        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        }
        $time_range = " and time >= '{$start}' and time <= '{$end}' ";
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;
        $sql = "select FROM_UNIXTIME(time,'%Y-%m-%d') as d,count(vid) as vnum from tp_customer_service_fans_visitors " . $where . " group by d  order by d asc ";
        $data = M()->query($sql);
        for ($i = 0; $i < count($data); $i++) {
            $st = strtotime($data[$i]['d'] . ' 00:00:00') - 1;
            $et = strtotime($data[$i]['d'] . ' 23:59:59') + 1;
            $c = M()->query("select count(vid) as chatnum,sum(lasttime - starttime) as t from tp_customer_service_fans_visitors where token = '" . $this->token . "' and time > " . $st . " and time < " . $et . " and (lasttime - starttime) > 0 ");
            $data[$i]['chatnum'] = $c[0]['chatnum'] ? $c[0]['chatnum'] : 0;
            $data[$i]['per'] = sprintf('%.2f%%', $data[$i]['chatnum'] / $data[$i]['vnum'] * 100);
            if ($c[0]['t']) {
                $tTime = $this->getTime(ceil($c[0]['t'] / $data[$i]['chatnum']));
            } else {
                $tTime = '00:00:00';
            }
            $data[$i]['perTime'] = $tTime;
        }
        $this->assign('vlist', $data);
        $_SESSION['visitors'] = $data;
        $xml = '<chart caption="访客和沟通统计" xAxisName="日期" yAxisName="" labelStep="" showNames="" showValues="" rotateNames="" showColumnShadow="1" animation="1" showAlternateHGridColor="0" AlternateHGridColor="ff5904" divLineColor="D0DCE4" divLineAlpha="100" alternateHGridAlpha="5"   formatNumberScale="0"  baseFontColor="666666" baseFont="宋体" baseFontSize="12" outCnvBaseFontSize="12"  canvasBorderThickness="1" canvasBorderColor="D0DCE4"  bgColor="FFFFFF" bgAlpha="0"  showBorder="0"  lineColor="0F6FCF" lineThickness="3"  anchorBorderColor="FFFFFF" anchorBorderThickness="2" anchorBgColor="0F6FCF"   numDivLines="2" numVDivLines="3"><categories>';

        $fansCountSet = '';
        $imgRequryCountSet = '';
        foreach ($data as $li) {
            $day = $li['d'];
            $xml .= '<category label="' . $day . '"/>';
            $c1 .= '<set value="' . $li['vnum'] . '"/>';
            $c2 .= '<set value="' . $li['chatnum'] . '"/>';
            //$c3 .= '<set value="'.$li['perTime'].'"/>';
        }
        $xml .= '</categories><dataset seriesName="访客数" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">' . $c1 . '</dataset><dataset seriesName="沟通次数" color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A">' . $c2 . '</dataset><styles><definition><style name="CaptionFont" type="font" size="12"/></definition><application><apply toObject="CAPTION" styles="CaptionFont"/><apply toObject="SUBCAPTION" styles="CaptionFont"/></application></styles></chart>';
        $this->assign('xml', $xml);

        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        $this->display();
    }

    /**
     * 访客记录导出
     *
     */
    public function visitorsImport() {
        if (empty($_SESSION['visitors'])) {
            $this->error('无数据');
        }
        $data = $_SESSION['visitors'];
        $filename = date('Y-m-d', $_SESSION['start']) . "~" . date('Y-m-d', $_SESSION['end']) . "日客服流量统计";
        $title = array('地区', '访客数', '沟通次数', '沟通比例', '平均沟通时长');
        $this->exportexcel($data, $title, $filename);
        unset($_SESSION['start']);
        unset($_SESSION['end']);
        unset($_SESSION['visitors']);
    }

    /**
     * visitorsArea()客服统计－－地域统计
     *
     */
    public function visitorsArea() {
        $where = " where v.token = '" . $this->token . "' and v.openid = f.openid ";
        //时间范围处理
        if (!empty($_POST['start'])) {
            $start = trim($_POST['start']);
        } elseif ($_GET['start']) {
            $start = trim($_GET['start']);
        } else {
            $start = date('Y-m-d');
        }
        if (!empty($_POST['end'])) {
            $end = trim($_POST['end']);
        } elseif ($_GET['end']) {
            $end = trim($_GET['end']);
        } else {
            $end = date('Y-m-d');
        }
        $start = strtotime($start . " 00:00:00");
        $end = strtotime($end . " 23:59:59");

        if ($end < $start) {
            $this->error('结束时间不能小于开始时间');
        }
        $time_range = " and v.time >= '{$start}' and v.time <= '{$end}' ";
        $where .= $time_range;
        $this->assign('start', $start);
        $this->assign('end', $end);
        $_SESSION['start'] = $start;
        $_SESSION['end'] = $end;
        $sql = "select f.province as province,count(v.vid) as vnum from tp_customer_service_fans_visitors v,tp_customer_service_fans f " . $where . " group by province ";
        $data = M()->query($sql);
        $ChartData1 = ''; //访客数图表数据
        $ChartData2 = ''; //沟通次数图表数据
        $c2 = 0;
        for ($ii = 0; $ii < count($data); $ii++) {
            $cc = M()->query("select count(v.vid) as chatnum,sum(v.lasttime - v.starttime) as t from tp_customer_service_fans_visitors v,tp_customer_service_fans f " . $where . " and (v.lasttime - v.starttime) > 0  and f.province = '" . $data[$ii]['province'] . "'");
            $data[$ii]['chatnum'] = $cc[0]['chatnum'] ? $cc[0]['chatnum'] : 0;
            $data[$ii]['per'] = sprintf('%.2f%%', $data[$ii]['chatnum'] / $data[$ii]['vnum'] * 100);
            if ($cc[0]['t']) {
                $tTime = $this->getTime(ceil($cc[0]['t'] / $data[$ii]['chatnum']));
            } else {
                $tTime = '00:00:00';
            }
            $data[$ii]['perTime'] = $tTime;

            //准备统计图数据
            $ChartData1 .= "['" . $data[$ii]['province'];
            $ChartData1 .= "'," . $data[$ii]['vnum'] . "],";
            if ($data[$ii]['chatnum']) {
                $ChartData2 .= "['" . $data[$ii]['province'];
                $ChartData2 .= "'," . $data[$ii]['chatnum'] . "],";
                $c2++;
            }
        }
        $ChartData1 = rtrim($ChartData1, ',');
        $ChartData2 = rtrim($ChartData2, ',');
        $colors1 = array('#1E90FF', '#FF6600', '#339933', '#99CC00', '#000099', '#CC0099', '#FF6633', '#9999CC', '#996633', '#DCDCDC', '#C76114', '#5E2612', '#D2B48C', '#B0171F', '#9C661F', '#FF0000', '#872657', '#FF4500', '#D2691E', '#F0E68C', '#C0C0C0', '#0673B8', '#0091F1', '#F85900', '#1CC2E6', '#C32121', '#CCCC00', '#003333', '#996633', '#99CC00', '#FF6600', '#339933', '#99CC00', '#D2B48C');
        $colors2 = array('#1E90FF', '#CC0099', '#FF6633', '#CCCC00', '#003333', '#996633', '#99CC00', '#FF6600', '#339933', '#99CC00', '#000099', '#C76114', '#5E2612', '#D2B48C', '#B0171F', '#9C661F', '#FF0000', '#872657', '#FF4500', '#D2691E', '#F0E68C', '#C0C0C0', '#0673B8', '#0091F1', '#F85900', '#1CC2E6', '#C32121', '#CCCC00', '#003333', '#996633', '#99CC00', '#FF6600', '#339933', '#99CC00');
        $this->assign('ChartData1', $ChartData1);
        $this->assign('ChartData2', $ChartData2);
        $Colors1 = "'" . implode("','", array_slice($colors1, 0, count($data))) . "'";
        $Colors2 = "'" . implode("','", array_slice($colors2, 0, $c2)) . "'";
        $this->assign('colors1', $Colors1);
        $this->assign('colors2', $Colors2);
        $this->assign('vlist', $data);
        $_SESSION['visitorsArea'] = $data;

        //今天
        $nowtime = date('Y-m-d');
        $this->assign('nowtime', $nowtime);
        //昨天
        $yestoday = date('Y-m-d', strtotime('-1 days'));
        $this->assign('yestoday', $yestoday);

        //7天以内，即6天前到今天
        $sevenDay = date('Y-m-d', strtotime('-6 days'));
        $this->assign('sevenDay', $sevenDay);

        //30天以内，即29天前到今天
        $thirtyday = date('Y-m-d', strtotime('-29 days'));
        $this->assign('thirtyday', $thirtyday);

        $this->display();
    }

    /**
     * 地域统计导出
     */
    public function visitorsAreaImport() {
        if (empty($_SESSION['visitorsArea'])) {
            $this->error('无数据');
        }
        $data = $_SESSION['visitorsArea'];
        $filename = date('Y-m-d', $_SESSION['start']) . "~" . date('Y-m-d', $_SESSION['end']) . "日访客地域统计";
        $title = array('地域', '访客数', '沟通次数', '沟通比例', '平均沟通时长');
        $this->exportexcel($data, $title, $filename);
        unset($_SESSION['start']);
        unset($_SESSION['end']);
        unset($_SESSION['visitorsArea']);
    }

    /**
     * changeGroup() 修改用户分组
     *
     */
    public function changeGroup() {
        if (strlen($_POST['openid']) < 16 || ( intval($_POST['gid']) < 0 )) {
            echo 'error';
            exit;
        }
        $where['openid'] = $_POST['openid'];
        $data['gid'] = intval($_POST['gid']);
        $res = M('customer_service_fans')->where($where)->save($data);
        if ($res) {
            $newGroup = M('customer_service_fans_group')->field('groupname')->where(array('token' => $this->token, 'gid' => intval($_POST['gid'])))->find();
            echo $newGroup['groupname'];
        } else {
            echo 'error';
        }
    }

    /**
     * 把秒转换成 N小时:N分:N秒
     */
    public function getTime($s = 0) {
        if ($s < 60) {
            $sec = str_pad($s, 2, '0', STR_PAD_LEFT);
            $time = '00:00:' . $sec;
        } elseif ($s >= 60 && $s < 3600) {
            $m = str_pad(floor($s / 60), 2, '0', STR_PAD_LEFT);
            $sec = str_pad($s - $m * 60, 2, '0', STR_PAD_LEFT);
            $time = '00:' . $m . ':' . $sec;
        } else {
            $h = str_pad(floor($s / 3600), 2, '0', STR_PAD_LEFT);
            $m = str_pad(floor(( $s - $h * 3600 ) / 60), 2, '0', STR_PAD_LEFT);
            $sec = str_pad(( $s - $h * 3600 - $m * 60), 2, '0', STR_PAD_LEFT);
            $time = $h . ':' . $m . ':' . $sec;
        }
        return $time;
    }

    /**
     *  导出数据为excel表格
     * @ param $data    一个二维数组,结构如同从数据库查出来的数组
     * @ param $title   excel的第一行标题,一个数组,如果为空则没有标题
     * @ param $filename 下载的文件名
     */
    public function exportexcel($data = array(), $title = array(), $filename = 'report') {
        /** Include PHPExcel */
        $base_url = THINK_PATH . "PigCms/Lib/Action/User/Classes/PHPExcel.php";
        require_once $base_url;
        //require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //写出表头
        for ($m = 0; $m < count($title); $m++) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(substr($str, $m, 1) . '1', $title[$m]);
        }

        //写出内容 UTF-8
        for ($n = 0; $n < count($data); $n++) {
            $num = 0;
            foreach ($data[$n] as $arr) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue(substr($str, $num, 1) . ( $n + 2 ), $arr);
                $num++;
            }
        }
        //表格名称,excel每个表格右下角的表名称
        $objPHPExcel->getActiveSheet()->setTitle($filename);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //设置excel文档名称
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}

?>