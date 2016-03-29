<?php

/*
  二维码
 */

class CodeAction extends UserAction {

    public function index() {





        //$infolist='北京,上海,石家庄';
        //$a=explode(',',$infolist);
        //foreach($a as $k=> $v){
        //  echo $v;
        //}
        //print_r($a);
        $this->display();
    }

    //活动二维码编辑
    public function hd() {
        $token = $_GET['token'];
        $npic_twocode_qudao = M('npic_twocode_qudao');
        $npic_twocode_huodong = M('npic_twocode_huodong');
        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_qdleix = M('npic_twocode_qdleix');
        $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


        $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

        $this->assign('hdlist', $hdlist);
        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign("qdlxlist", $qdlxlist);
        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


        $this->assign('qudao_list', $qudao_list);



        $npic_twocode = M('npic_twocode');

        $list1 = $npic_twocode->where("token='$token' and type='1'")->select();
        $num = count($list1);
        $Page = new Page($num, 25);
        $show = $Page->show();
        $list = $npic_twocode->where("token='$token' and type='1'")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('tab', 'hd');

        $this->display();
    }

    //活动添加
    public function hdtj() {
        $npic_twocode = M('npic_twocode');
        if ($_POST) {
            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {

                $token = $_POST['token'];
                $data['token'] = $_POST['token'];
                $data['cname'] = $_POST['cname'];
                $data['curl'] = $_POST['curl'];
                //$data['hdcpname'] = $_POST['hdcpname'];
                //$data['cguige'] = $_POST['cpguige'];
                //$data['cnum'] = $_POST['num'];
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
                if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }
                if ($_POST['fid']) {
                    $data['qudao'] = $_POST['fid'];
                }
                if ($_POST['qdlx']) {
                    $data['huodongleixing'] = $_POST['qdlx'];
                }
                if ($_POST['f_city']) {
                    $data['city'] = $_POST['f_city'];
                }
                if ($_POST['hid']) {
                    $data['huodong'] = $_POST['hid'];
                }

                // print_r($data);exit;
                //$data['cfather']=$_POST['qd'];
                //if($_POST['zl']){
                //$data['cfather_children']=$_POST['zl'];
                //}
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];
                //echo "<pre>";
                //print_r($data);exit;
                $qry1 = $npic_twocode->add($data);
                if ($qry1) {
                    $this->success('添加成功', U('Code/hd', array('token' => $token)));
                } else {

                    $this->error('添加失败', U('Code/hd', array('token' => $token)));
                }
            }
        }
    }

    //活动二维码修改
    public function hdeditlist() {
        $npic_twocode = M('npic_twocode');
        $npic_twocode_father = M('npic_twocode_father');
        if ($_POST) {
            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {
                // print_r($_POST);exit;
                $token = $_POST['token'];
                $cid = $_POST['cid'];
                $data['token'] = $_POST['token'];
                $data['cname'] = $_POST['cpname'];

                $data['curl'] = $_POST['codeurl'];
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
                if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }
                if ($_POST['fid']) {
                    $data['qudao'] = $_POST['fid'];
                }
                if ($_POST['qdlx']) {
                    $data['huodongleixing'] = $_POST['qdlx'];
                }
                if ($_POST['f_city']) {
                    $data['city'] = $_POST['f_city'];
                }
                if ($_POST['hid']) {
                    $data['huodong'] = $_POST['hid'];
                }
                //$data['cfather']=$_POST['qd'];
                //if($_POST['zl']){
                //$data['cfather_children']=$_POST['zl'];
                //}
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];
                $qry2 = $npic_twocode->where("token='$token' and cid='$cid'")->save($data);
                if ($qry2) {
                    $this->success('修改成功', U('Code/hd', array('token' => $token)));
                } else {

                    $this->error('修改失败', U('Code/hd', array('token' => $token)));
                }
            }
        } else {
            $cid = $_GET['id'];
            $token = $_GET['token'];
            $npic_twocode_list = $npic_twocode->where("token='$token' and cid='$cid'")->find();
            //print_r($npic_twocode_list);
            $npic_twocode_qudao = M('npic_twocode_qudao');
            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


            $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

            $this->assign('hdlist', $hdlist);
            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign("qdlxlist", $qdlxlist);

            $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


            $this->assign('qudao_list', $qudao_list);

            $this->assign('npic_twocode_list', $npic_twocode_list);
            $this->display();
        }
    }

    //产品二维码编辑
    public function cp() {
        $npic_twocode = M('npic_twocode');

        $token = $_GET['token'];



        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
        $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();



        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();


        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign('cpxxcitylist', $cpxxcitylist);

        $twocode_list = $npic_twocode->where("token='$token' and type='2'")->select();


        $this->assign('twocode_list', $twocode_list);
        $this->assign('tab', 'cp');
        $this->display();
    }

    //产品二维码修改
    public function cpeditlist() {
        $npic_twocode = M('npic_twocode');
        $npic_twocode_father = M('npic_twocode_father');
        if ($_POST) {
            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {
                // print_r($_POST);exit;
                $token = $_POST['token'];
                $cid = $_POST['cid'];
                $data['token'] = $_POST['token'];
                $data['cname'] = $_POST['cpname'];
               if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }
                $data['curl'] = $_POST['codeurl'];
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
               if ($_POST['cpxxid']) {
                    $data['qudao'] = $_POST['cpxxid'];
                }
                if ($_POST['cpcity']) {
                    $data['huodongleixing'] = $_POST['cpcity'];
                }
                if ($_POST['chanpct']) {
                    $data['city'] = $_POST['chanpct'];
                }
                //$data['cfather']=$_POST['qd'];
                //if($_POST['zl']){
                //$data['cfather_children']=$_POST['zl'];
                //}
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];
                //print_R($data);exit;
                $twocode = $npic_twocode->where("token='$token' and cid='$cid'")->save($data);
                if ($twocode) {
                    $this->success('修改成功', U('Code/cp', array('token' => $token)));
                } else {

                    $this->error('修改失败', U('Code/cp', array('token' => $token)));
                }
            }
        } else {

            $cid = $_GET['id'];
            $token = $_GET['token'];
            $npic_twocode_list = $npic_twocode->where("token='$token' and cid='$cid'")->find();
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
            $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();



            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();


            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign('cpxxcitylist', $cpxxcitylist);



            $this->assign('npic_twocode_list', $npic_twocode_list);
            $this->display();
        }
    }

    //产品二维码提交地方
    public function cptj() {
        $npic_twocode = M('npic_twocode');
        if ($_POST) {
            //print_r($_POST);exit;
            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {
                $token = $_POST['token'];
                $data['token'] = $_POST['token'];
                $data['cname'] = $_POST['cpname'];
               if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }




                $data['curl'] = $_POST['codeurl'];
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
                if ($_POST['cpxxid']) {
                    $data['qudao'] = $_POST['cpxxid'];
                }
                if ($_POST['cpcity']) {
                    $data['huodongleixing'] = $_POST['cpcity'];
                }
                if ($_POST['chanpct']) {
                    $data['city'] = $_POST['chanpct'];
                }
                //$data['cfather']=$_POST['qd'];
                //if($_POST['zl']){
                //$data['cfather_children']=$_POST['zl'];
                //}
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];

                $twocode = $npic_twocode->add($data);
                if ($twocode) {
                    $this->success('添加成功', U('Code/cp', array('token' => $token)));
                } else {

                    $this->error('添加失败', U('Code/cp', array('token' => $token)));
                }
            }
        }
    }

    //自定义选项
    public function zdy() {
        $npic_twocode_father = M('npic_twocode_father');
        $token = $_GET['token'];
        $father_list = $npic_twocode_father->where("token='$token'")->select();
        //print_r($father_list);
        $this->assign('father_list', $father_list);
        $this->assign('tab', 'zdy');
        $this->display();
    }

    //自定义活动二维码信息
    public function zdyhd() {
        $token = $_GET['token'];
        $npic_twocode_qudao = M('npic_twocode_qudao');
        $npic_twocode_huodong = M('npic_twocode_huodong');
        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();

        $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();
        $this->assign('qudao_list', $qudao_list);
        $this->assign('hdlist', $hdlist);
        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);

        $this->display();
    }

    //自定义产品二维码信息
    public function zdycp() {
        $token = $_GET['token'];
        $npic_twocode_qudao = M('npic_twocode_qudao');
        $npic_twocode_active = M('npic_twocode_active'); //活动：
        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();
        $active_list = $npic_twocode_active->where("token='$token'")->select();

        $this->assign('qudao_list', $qudao_list);
        $this->assign('active_list', $active_list);
        $this->display();
    }

    public function qudao() {
        $npic_twocode_children = M('npic_twocode_children');
        if ($_POST) {
            $token = $_POST['token'];
            $data['token'] = $_POST['token'];
            $data['f_fid'] = $_POST['fid'];
            $data['zchildren_name'] = $_POST['qudao'];
            $data['etime'] = time();
            $qry = $npic_twocode_children->add($data);
            if ($qry) {
                $this->success('添加成功', U('Code/zdy', array('token' => $token)));
            } else {

                $this->error('添加失败', U('Code/zdy', array('token' => $token)));
            }
        }
    }

    //ajax点击添加线上
    public function ajaxxs() {
        $token = $_POST['token'];
        $fid = $_POST['fid'];
        //echo $fid.'---'.$token;exit;
        $npic_twocode_qdleix = M('npic_twocode_qdleix');
        $flist = $npic_twocode_qdleix->where("token='$token' and q_fid='$fid'")->select();
        //print_r($flist);exit;
        if ($flist) {
            foreach ($flist as $key => $v) {







                //$info.="<option value=''>" . "请选择" . "</option>";

                $info.="<option value=" . $v['lid'] . ">" . $v['l_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            //if(stripos(',',$v['zchildren_name'])){
            //}

            echo $info;
        } else {

            echo 2;
        }
    }

    //ajax 点击ok添加自定义添加子类
    public function ajaxzileiok() {
        $token = $_POST['token'];
        $fid = $_POST['fid'];
        $qdleix = $_POST['qdleix'];

        $npic_twocode_qdleix = M('npic_twocode_qdleix');
        $leix=$npic_twocode_qdleix->where("token='$token' and l_name='$qdleix'")->find();
        if($leix){
            echo '3';
        }else{
        $data['l_name'] = $qdleix;
        $data['q_fid'] = $fid;
        $data['token'] = $token;
        $data['etime'] = time();
        $qry = $npic_twocode_qdleix->add($data);

       

        if ($qry) {

            $newlist = $npic_twocode_qdleix->where("token='$token' and q_fid='$fid'")->select();

            foreach ($newlist as $key => $v) {





                $info.="<option value=" . $v['lid'] . ">" . $v['l_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            echo $info;
        } else {

            echo 2;
        }
        }
    }
    
    //删除自定义子类
    public function ajaxzileidel(){
           $qdlx=$_REQUEST['qdlx'];
           $token=$_REQUEST['token'];
           //echo $qdlx.'--'.$token;
           $npic_twocode_qdleix = M('npic_twocode_qdleix');
           $delleix=$npic_twocode_qdleix->where("token='$token' and lid='$qdlx'")->delete();
           if($delleix){
               echo '1';
           }else{
               echo '2'; 
           }
    }
    //删除城市
    public function ajaxcitydel(){
           $f_city=$_REQUEST['f_city'];
           $token=$_REQUEST['token'];
           //echo $f_city.'--'.$token;exit;
           $npic_twocode_xxcity = M('npic_twocode_xxcity');
           $delleix=$npic_twocode_xxcity->where("token='$token' and cid='$f_city'")->delete();
           if($delleix){
               echo '1';
           }else{
               echo '2'; 
           }
    }
    
     //删除活动
    public function ajaxhuodongdel(){
           $hid=$_REQUEST['hid'];
           $token=$_REQUEST['token'];
           //echo $f_city.'--'.$token;exit;
           $npic_twocode_huodong = M('npic_twocode_huodong');
           $delleix=$npic_twocode_huodong->where("token='$token' and hid='$hid'")->delete();
           if($delleix){
               echo '1';
           }else{
               echo '2'; 
           }
    }
    
     //删除产品的线上线下类型
    public function ajaxcpdel(){
           $cpcity=$_REQUEST['cpcity'];
           $token=$_REQUEST['token'];
           //echo $f_city.'--'.$token;exit;
           $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
           $delleix=$npic_twocode_cpxxcity->where("token='$token' and cid='$cpcity'")->delete();
           if($delleix){
               echo '1';
           }else{
               echo '2'; 
           }
    }

    //自定义添加活动
    public function ajaxxsok() {

        $token = $_POST['token'];

        $hdleix = $_POST['hdleix'];

        $npic_twocode_huodong = M('npic_twocode_huodong');

        $leix=$npic_twocode_huodong->where("token='$token' and h_name='$hdleix'")->find();
        if($leix){
            echo '3';
            
        }else{
        $data['h_name'] = $hdleix;
        $data['token'] = $token;
        $data['etime'] = time();
        $qry = $npic_twocode_huodong->add($data);
        if ($qry) {
            $newlist = $npic_twocode_huodong->where("token='$token'")->select();
            foreach ($newlist as $key => $v) {







                //$info.="<option value=''>" . "请选择" . "</option>";

                $info.="<option value=" . $v['hid'] . ">" . $v['h_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            echo $info;
        } else {

            echo 2;
        }
        }
    }

    //ajax 点击ok添加自定义城市
    public function ajaxcityok() {
        $token = $_POST['token'];

        $cityname = $_POST['cityname'];

        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $leix=$npic_twocode_xxcity->where("token='$token' and c_name='$cityname'")->find();
        if($leix){
            echo '3';
        }else{
        $data['c_name'] = $cityname;
        $data['token'] = $token;
        $data['etime'] = time();

        $qry = $npic_twocode_xxcity->add($data);
        if ($qry) {
            $newlist = $npic_twocode_xxcity->where("token='$token'")->select();
            foreach ($newlist as $key => $v) {





                $info.="<option value=" . $v['cid'] . ">" . $v['c_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            //if(stripos(',',$v['zchildren_name'])){
            //}

            echo $info;
        } else {

            echo 2;
        }
        }
    }

    //产品二维码属性添加
    //ajax点击添加线上
    public function ajacpxxs() {
        $token = $_POST['token'];
        $cpxxid = $_POST['cpxxid'];
        //echo $cpxxid.'---'.$token;exit;
        $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
        $flist = $npic_twocode_cpxxcity->where("token='$token' and x_fid='$cpxxid'")->select();
        //print_r($flist);exit;
        if ($flist) {
            foreach ($flist as $key => $v) {







                //$info.="<option value=''>" . "请选择" . "</option>";

                $info.="<option value=" . $v['cid'] . ">" . $v['c_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            //if(stripos(',',$v['zchildren_name'])){
            //}

            echo $info;
        } else {

            echo 2;
        }
    }

    //ajax 点击ok添加自定义城市
    public function ajaxcpcityok() {
        $token = $_POST['token'];

        $cpxxid = $_POST['cpxxid'];
        $citycp = $_POST['citycp'];
        //echo $citycp.'--'.$token.'--'.$cpxxid;exit;
        $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
        $leix=$npic_twocode_cpxxcity->where("token='$token' and c_name='$citycp'")->find();
        if($leix){
            echo '3';
            
        }else{
        $data['c_name'] = $citycp;
        $data['token'] = $token;
        $data['x_fid'] = $cpxxid;
        $data['etime'] = time();



        $qry = $npic_twocode_cpxxcity->add($data);
        if ($qry) {
            $newlist = $npic_twocode_cpxxcity->where("token='$token' and x_fid='$cpxxid'")->select();
            foreach ($newlist as $key => $v) {





                $info.="<option value=" . $v['cid'] . ">" . $v['c_name'] . "</option>";
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            //if(stripos(',',$v['zchildren_name'])){
            //}

            echo $info;
        } else {

            echo 2;
        }
        }
    }

    //路演活动
   /* public function ajaxluyan() {
        $token = $_POST['token'];
        $hid = $_POST['hid'];

        $npic_twocode_active_zilei = M('npic_twocode_active_zilei');
        $flist = $npic_twocode_active_zilei->where("token='$token' and h_fid='$hid'")->select();

        if ($flist) {
            foreach ($flist as $key => $v) {

                $infolist = stripos($v['z_name'], ',');

                if ($infolist) {

                    $infolist1 = explode(',', $v['z_name']);
                    // print_r($infolist1);exit;


                    foreach ($infolist1 as $k => $vvv) {
                        $info.="<option value=" . $v['zid'] . ">" . $vvv . "</option>";
                    }
                } else {
                    $info.="<option value=" . $v['zid'] . ">" . $v['z_name'] . "</option>";
                }

                //if(stripos(',',$v['zchildren_name'])){
                //}
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            echo $info;
        } else {

            echo 2;
        }
    }

    //点击OKajax 添加路演活动或者门店活动
    public function ajaxhuodongok() {
        $token = $_POST['token'];
        $hid = $_POST['hid'];
        $huodong = $_POST['huodong'];
        $aa = stripos($huodong, ',');
        $npic_twocode_active_zilei = M('npic_twocode_active_zilei');
        if ($aa) {
            $infolist1 = explode(',', $huodong);
            foreach ($infolist1 as $kk => $vv) {

                $data['token'] = $token;
                $data['h_fid'] = $hid;
                $data['z_name'] = $vv;
                $qry = $npic_twocode_active_zilei->add($data);
            }
            if ($qry) {
                $newlist = $npic_twocode_active_zilei->where("token='$token' and h_fid='$hid'")->select();

                foreach ($newlist as $key => $v) {

                    $infolist = stripos($v['z_name'], ',');

                    if ($infolist) {

                        $infolist1 = explode(',', $v['z_name']);
                        // print_r($infolist1);exit;


                        foreach ($infolist1 as $k => $vvv) {
                            $info.="<option value=" . $v['zid'] . ">" . $vvv . "</option>";
                        }
                    } else {
                        $info.="<option value=" . $v['zid'] . ">" . $v['z_name'] . "</option>";
                    }

                    //if(stripos(',',$v['zchildren_name'])){
                    //}
                }
                $info.="<option value='自定义'>" . "自定义" . "</option>";
                echo $info;
            }
        } else {
            $data['token'] = $token;
            $data['h_fid'] = $hid;
            $data['z_name'] = $huodong;
            $qry = $npic_twocode_active_zilei->add($data);
            if ($qry) {
                $newlist = $npic_twocode_active_zilei->where("token='$token' and h_fid='$hid'")->select();
                //print_r($newlist);exit;
                foreach ($newlist as $key => $v) {

                    $infolist = stripos($v['z_name'], ',');

                    if ($infolist) {

                        $infolist1 = explode(',', $v['z_name']);
                        // print_r($infolist1);exit;


                        foreach ($infolist1 as $k => $vvv) {
                            $info.="<option value=" . $v['zid'] . ">" . $vvv . "</option>";
                        }
                    } else {
                        $info.="<option value=" . $v['zid'] . ">" . $v['z_name'] . "</option>";
                    }

                    //if(stripos(',',$v['zchildren_name'])){
                    //}
                }
                $info.="<option value='自定义'>" . "自定义" . "</option>";
                echo $info;
            }
        }
    }

    //ajax 选择门店
    public function ajaxmendian() {

        $token = $_POST['token'];
        $zid = $_POST['zid'];

        $npic_twocode_active_zilei_mendian = M('npic_twocode_active_zilei_mendian');
        $flist = $npic_twocode_active_zilei_mendian->where("token='$token' and z_fid='$zid'")->select();
        //print_r($flist);exit;
        if ($flist) {
            foreach ($flist as $key => $v) {

                $infolist = stripos($v['m_name'], ',');

                if ($infolist) {

                    $infolist1 = explode(',', $v['m_name']);
                    // print_r($infolist1);exit;


                    foreach ($infolist1 as $k => $vvv) {
                        $info.="<option value=" . $v['mid'] . ">" . $vvv . "</option>";
                    }
                } else {
                    $info.="<option value=" . $v['mid'] . ">" . $v['m_name'] . "</option>";
                }
            }
            $info.="<option value='自定义'>" . "自定义" . "</option>";
            echo $info;
        } else {

            echo 2;
        }
    }

    //ajax 点击OK添加门店子类信息
    public function ajaxmendianok() {
        $token = $_POST['token'];
        $zid = $_POST['zid'];
        $mendian = $_POST['mendian'];
        //echo $token.'--'.$zid.'--'.$mendian;exit;
        $aa = stripos($mendian, ',');
        $npic_twocode_active_zilei_mendian = M('npic_twocode_active_zilei_mendian');
        if ($aa) {
            $infolist1 = explode(',', $mendian);
            foreach ($infolist1 as $kk => $vv) {

                $data['token'] = $token;
                $data['z_fid'] = $zid;
                $data['m_name'] = $vv;
                $qry = $npic_twocode_active_zilei_mendian->add($data);
            }
            if ($qry) {
                $newlist = $npic_twocode_active_zilei_mendian->where("token='$token' and z_fid='$zid'")->select();

                foreach ($newlist as $key => $v) {

                    $infolist = stripos($v['m_name'], ',');

                    if ($infolist) {

                        $infolist1 = explode(',', $v['m_name']);
                        // print_r($infolist1);exit;


                        foreach ($infolist1 as $k => $vvv) {
                            $info.="<option value=" . $v['mid'] . ">" . $vvv . "</option>";
                        }
                    } else {
                        $info.="<option value=" . $v['mid'] . ">" . $v['m_name'] . "</option>";
                    }

                    //if(stripos(',',$v['zchildren_name'])){
                    //}
                }
                $info.="<option value='自定义'>" . "自定义" . "</option>";
                echo $info;
            }
        } else {
            $data['token'] = $token;
            $data['z_fid'] = $zid;
            $data['m_name'] = $mendian;
            $qry = $npic_twocode_active_zilei_mendian->add($data);
            if ($qry) {
                $newlist = $npic_twocode_active_zilei_mendian->where("token='$token' and z_fid='$zid'")->select();
                //print_r($newlist);exit;
                foreach ($newlist as $key => $v) {

                    $infolist = stripos($v['m_name'], ',');

                    if ($infolist) {

                        $infolist1 = explode(',', $v['m_name']);
                        // print_r($infolist1);exit;


                        foreach ($infolist1 as $k => $vvv) {
                            $info.="<option value=" . $v['mid'] . ">" . $vvv . "</option>";
                        }
                    } else {
                        $info.="<option value=" . $v['mid'] . ">" . $v['m_name'] . "</option>";
                    }

                    //if(stripos(',',$v['zchildren_name'])){
                    //}
                }
                $info.="<option value='自定义'>" . "自定义" . "</option>";
                echo $info;
            }
        }
    }
*/
    public function ajaxchange() {
        $token = $_POST['token'];
        $fid = $_POST['fid'];
        $npic_twocode_children = M('npic_twocode_children');
        $flist = $npic_twocode_children->where("token='$token' and f_fid='$fid'")->select();
        if ($flist) {
            foreach ($flist as $key => $v) {

                $infolist = stripos($v['zchildren_name'], ',');

                if ($infolist) {

                    $infolist1 = explode(',', $v['zchildren_name']);

                    foreach ($infolist1 as $k => $vvv) {
                        $info.="<option value=" . $vvv . ">" . $vvv . "</option>";
                    }
                } else {
                    $info.="<option value=" . $v['zchildren_name'] . ">" . $v['zchildren_name'] . "</option>";
                }
                //if(stripos(',',$v['zchildren_name'])){
                //}
            }
            echo $info;
        } else {

            echo 2;
        }
    }

    //活动二维码生成
    public function hdcode() {
        $token = $_GET['token'];
        $npic_twocode = M('npic_twocode');
        $npic_twocode_qudao = M('npic_twocode_qudao');
        
        // $father_list=$npic_twocode_father->where("token='$token'")->select();

        $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


        $this->assign('qudao_list', $qudao_list);
       
  
        $npic_twocode_huodong = M('npic_twocode_huodong');
        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_qdleix = M('npic_twocode_qdleix');
        $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


        $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

        $this->assign('hdlist', $hdlist);
        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign("qdlxlist", $qdlxlist);
      

   
        $list1 = $npic_twocode->where("token='$token' and type='1' and ccode=''")->select();

        $num = count($list1);
        $Page = new Page($num, 25);
        $show = $Page->show();
        $list = $npic_twocode->where("token='$token' and type='1' and ccode=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);



        $this->assign('tab', 'hdcode');
        $this->display();
    }

    //活动二维批量生成
    public function codedc() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/phpqrcode.php';


        if ($_REQUEST['item'] == '') {
            $this->error("你没有选中任何条件");
        } else {
            $npic_twocode = M('npic_twocode');
            $token = $_REQUEST['token'];
            $cid = $_REQUEST['item'];
            $ccid = implode(',', $cid);
            //echo $ccid;

            $rr = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->select();

            if ($rr) {
                foreach ($rr as $kkkk => $vvvv) {
                    //print_r($vvvv);exit;
                    $cccid = $vvvv['cid'];
//                    $value = C('site_url') . '/index.php?g=Wap&m=O&a=index&t=code&id=' . $cccid;
                    $cUrl = str_replace('&amp;','&',$vvvv['curl']);
                    $value = $cUrl  . '&gid=' . $cccid;
                    $filename = './PUBLIC/imagess/' . $_SESSION['token'] . '/code/' . $vvvv['cid'] . '.jpg';
                    $errorCorrectionLevel = "L";
                    $matrixPointSize = "4";
                    $margin = 2;
                    QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);

                    // $imgSrc = generateQRfromGoogle(C('site_url') . '/index.php?g=Wap&m=Yuangong&a=grlist&token=' . $token . '&id=' . $cccid);
                    $supda['ccode'] = C('site_url') . (ltrim($filename, '.'));
                    $rrr = $npic_twocode->where("token='$token' and cid='$cccid'")->save($supda);
                }

                $this->success('操作成功');
            } else {
                $this->error('操作成功');
            }
        }
    }

    //产品二维码生成
    public function cpcode() {
        $token = $_GET['token'];

        $npic_twocode = M('npic_twocode');
        //$father_list=$npic_twocode_father->where("token='$token'")->select();

        $npic_twocode_xians = M('npic_twocode_xians');
        $npic_twocode_xxcity = M('npic_twocode_xxcity');
        $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
        $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();



        $xxlist = $npic_twocode_xians->where("token='$token'")->select();
        $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();


        $this->assign('xxlist', $xxlist);
        $this->assign('xxcity', $xxcity);
        $this->assign('cpxxcitylist', $cpxxcitylist);
        $list1 = $npic_twocode->where("token='$token' and type='2' and ccode=''")->select();

        $num = count($list1);
        $Page = new Page($num, 25);
        $show = $Page->show();
        $list = $npic_twocode->where("token='$token' and type='2' and ccode=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        $this->assign('page', $show);
        $this->assign('list', $list);


        $this->assign('tab', 'cpcode');
        $this->display();
    }

    //产品二维码批量生产
    public function cpcodesc() {
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/phpqrcode.php';
        if ($_REQUEST['item'] == '') {
            $this->error('您没有做出任何选择');
        } else {
            $npic_twocode = M('npic_twocode');
            $token1 = $_REQUEST['token'];
            $cid1 = $_REQUEST['item'];
            $ccid1 = implode(',', $cid1);
            //echo $ccid;

            $rr1 = $npic_twocode->where("token='$token1' and cid in(" . $ccid1 . ")")->select();

            if ($rr1) {
                foreach ($rr1 as $kkkk1 => $vvvv1) {

                    $cccid1 = $vvvv1['cid'];
                    $value = C('site_url') . '/index.php?g=Wap&m=O&a=index&t=code&id=' . $cccid1;

                    $filename = './PUBLIC/imagess/' . $_SESSION['token'] . '/code/' . $vvvv1['cid'] . '.jpg';
                    $errorCorrectionLevel = "L";
                    $matrixPointSize = "4";
                    $margin = 2;
                    QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);
                    //$imgSrc1 = generateQRfromGoogle(C('site_url') . '/index.php?g=Wap&m=Yuangong&a=grlist&token=' . $token1 . '&id=' . $cccid1);
                    $supda1['ccode'] = C('site_url') . (ltrim($filename, '.'));
                    $rrr1 = $npic_twocode->where("token='$token1' and cid='$cccid1'")->save($supda1);
                }

                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
    }

    //活动二维码删除
    public function hdcode_del() {
        $npic_twocode = M('npic_twocode');
        $token = $_GET['token'];
        $cid = $_GET['id'];
        $ll = $npic_twocode->where("token='$token' and cid='$cid'")->delete();
        if ($ll) {
            $this->success('删除成功');
        } else {

            $this->error('删除失败');
        }
    }

    //活动二维码批量删除
    public function hdcodedel() {
        $npic_twocode = M('npic_twocode');
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何东西');
        } else {
            $token = $_REQUEST['token'];
            $cid = $_REQUEST['item'];
            $ccid = implode(',', $cid);
            $dl = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->delete();
            if ($dl) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    //活动二维码修改
    public function hdcode_set() {

        $npic_twocode = M('npic_twocode');

        if ($_POST) {

            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {

                $token = $_POST['token'];
                $cid = $_POST['cid'];


                $data['cname'] = $_POST['cpname'];
                
                $data['curl'] = $_POST['codeurl'];
                if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
                if ($_POST['fid']) {
                    $data['qudao'] = $_POST['fid'];
                }
                if ($_POST['qdlx']) {
                    $data['huodongleixing'] = $_POST['qdlx'];
                }
                if ($_POST['f_city']) {
                    $data['city'] = $_POST['f_city'];
                }
                if ($_POST['hid']) {
                    $data['huodong'] = $_POST['hid'];
                }
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];
                $qry2 = $npic_twocode->where("token='$token' and cid='$cid'")->save($data);
                if ($qry2) {
                    $this->success('修改成功', U('Code/codewh', array('token' => $token)));
                } else {

                    $this->error('修改失败', U('Code/codewh', array('token' => $token)));
                }
            }
        } else {
            $cid = $_GET['id'];
            $token = $_GET['token'];
            $npic_twocode_list = $npic_twocode->where("token='$token' and cid='$cid'")->find();
            $npic_twocode_qudao = M('npic_twocode_qudao');
           
            $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();
            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


            $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

            $this->assign('hdlist', $hdlist);
            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign("qdlxlist", $qdlxlist);
           

            $this->assign('qudao_list', $qudao_list);
            $this->assign('npic_twocode_list', $npic_twocode_list);
            $this->display();
        }
    }

    //活动二维码维护
    public function codewh() {
        
        $npic_twocode = M('npic_twocode');
        if ($_POST) {

            $ctoken = $_POST['ctoken'];
            $sous = $_POST['sous'];
            

            //$list1=$npic_twocode->where("token='$token' and type='1' and cname='%$%'")->select();
            $sql = "select * from tp_npic_twocode where token='$ctoken' and type='1' and ccode !='' and cname like '%$sous%'";
            $list1 = $npic_twocode->query($sql);
            $num = count($list1);
            $p = new Page($num, 20);
            $firstRow = $p->firstRow;
            $listRows = $p->listRows;
            $sql1 = "select * from tp_npic_twocode where token='$ctoken' and type='1' and ccode !='' and cname like '%$sous%' limit {$firstRow},{$listRows}";
            $list = $npic_twocode->query($sql1);
            $page = $p->show();
            $npic_twocode_qudao = M('npic_twocode_qudao');
            //print_r($list);exit;
            // $father_list=$npic_twocode_father->where("token='$token'")->select();
            $this->assign('page', $page);
            $this->assign('list', $list);
            $qudao_list = $npic_twocode_qudao->where("token='$ctoken'")->select();
            //print_r($qudao_list);
            //$qudao_lists=array();
            //foreach ($qudao_list as $key => $v) {
              //  $qudao_lists[$v['qid']]=$v;
                
            //}
            //echo '<hr>';
            //print_r($qudao_lists);exit;


            


            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $qdlxlist = $npic_twocode_qdleix->where("token='$ctoken'")->select();

          
            $hdlist = $npic_twocode_huodong->where("token='$ctoken'")->select();
            $xxlist = $npic_twocode_xians->where("token='$ctoken'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$ctoken'")->select();
            $this->assign('qudao_list', $qudao_list);
            //$this->assign('myqudao_list', $qudao_lists);
            $this->assign('hdlist', $hdlist);
            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign("qdlxlist", $qdlxlist);
            
        } else {


            $token = $_GET['token'];

            
            $cname=$_GET['cname'];
            $list1 = $npic_twocode->where("token='$token' and type='1' and cname='$cname' and ccode !=''")->select();

            $num = count($list1);
            $Page = new Page($num, 25);
            $show = $Page->show();
            $list = $npic_twocode->where("token='$token' and type='1' and cname='$cname' and ccode !=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $show);
            $this->assign('list', $list);
           
            $npic_twocode_qudao = M('npic_twocode_qudao');
        
            // $father_list=$npic_twocode_father->where("token='$token'")->select();

            $qudao_list = $npic_twocode_qudao->where("token='$token'")->select();


            $this->assign('qudao_list', $qudao_list);


            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


            $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

            $this->assign('hdlist', $hdlist);
            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign("qdlxlist", $qdlxlist);
        }

        $this->assign('tab', 'hdwhcode');
        $this->display();
    }

    //活动二维码导出
    public function hdcodedc() {
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何内容');
        } else {

            $npic_twocode = M('npic_twocode');
            $npic_twocode_qudao = M('npic_twocode_qudao');
            $npic_twocode_huodong = M('npic_twocode_huodong');
            $npic_twocode_tongji=M('npic_twocode_tongji');
            //$npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_qdleix = M('npic_twocode_qdleix');
            $cid = $_REQUEST['item'];
            $token = $_REQUEST['token'];
            $ccid = implode(',', $cid);
            $list = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->select();
            //$num=$npic_twocode_tongji->where("token='$token' and c_fid in(" . $ccid . ")")->select();
            //$sql="select sum(cnum) as num from tp_npic_twocode_tongji where token='$token' and c_fid in(" . $ccid . ")";
            //$num=$npic_twocode_tongji->query($sql);
            //print_R($num[0]['num']);exit;
            $qdao_list = $npic_twocode_qudao->where("token='$token'")->select();
             
            $qdlxlist = $npic_twocode_qdleix->where("token='$token'")->select();


            $hdlist = $npic_twocode_huodong->where("token='$token'")->select();
            //$xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();
            /* foreach($list as $k=>$v){
              foreach($qdao_list as  $kk=>$vv){
              if($v['qudao']==$vv['qid']){
              print_r($vv['q_name']);
              }
              }
              }
             */
            $str1 = substr(THINK_PATH, 0, -1);
            require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '活动名称')
                   
                    ->setCellValue('C1', '渠道')
                    ->setCellValue('D1', '类型')
                    ->setCellValue('E1', '城市')
                    ->setCellValue('F1', '活动')
                    ->setCellValue('G1', '扫描次数')
                    ->setCellValue('H1', '开始时间')
                    ->setCellValue('I1', '结束时间')
                    ->setCellValue('J1', '二维码')
                    ->setCellValue('K1', '备注1')
                    ->setCellValue('L1', '备注2')
                    ->setCellValue('M1', '0-6点')
                    ->setCellValue('N1', '6-8点')
                    ->setCellValue('O1', '8-10点')
                    ->setCellValue('P1', '10-12点')
                    ->setCellValue('Q1', '12-14点')
                    ->setCellValue('R1', '14-16点')
                    ->setCellValue('S1', '16-18点')
                    ->setCellValue('T1', '18-20点')
                    ->setCellValue('U1', '20-22点')
                    ->setCellValue('V1', '22-24点');
                    


            // Miscellaneous glyphs, UTF-8
            for ($i = 0; $i < count($list); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $list[$i]['cid'])
                        ->setCellValue('B' . ($i + 2), $list[$i]['cname']);
                       
                foreach ($qdao_list as $kk => $vv) {
                    if ($list[$i]['qudao'] == $vv['qid']) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('C' . ($i + 2), $vv['q_name']);
                    }
                }
                foreach ($qdlxlist as $qdlxlistk =>$qdlxlistv){
                    if($list[$i]['huodongleixing'] == $qdlxlistv['lid']){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('D' . ($i + 2), $qdlxlistv['l_name']);
                    }
                }
                foreach ($xxcity as $xxcityk =>$xxcityv){
                    if($list[$i]['city'] == $xxcityv['cid']){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('E' . ($i + 2), $xxcityv['c_name']);
                    }
                }
                
                foreach ($hdlist as $hdlistk => $hdlistv) {
                    if ($list[$i]['huodong'] == $hdlistv['hid']) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('F' . ($i + 2), $hdlistv['h_name']);
                    }
                }
                
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('G' . ($i + 2), $list[$i]['code_num'])
                        ->setCellValue('H' . ($i + 2), date("Y-m-d:H:i:s", $list[$i]['sdate']))
                        ->setCellValue('I' . ($i + 2), date("Y-m-d:H:i:s", $list[$i]['edate']))
                        ->setCellValue('J' . ($i + 2), $list[$i]['ccode'])
                        ->setCellValue('K' . ($i + 2), $list[$i]['beizhu1'])
                        ->setCellValue('L' . ($i + 2), $list[$i]['beizhu2'])
                        ->setCellValue('M' . ($i + 2), $list[$i]['one'])
                        ->setCellValue('N' . ($i + 2), $list[$i]['two'])
                        ->setCellValue('O' . ($i + 2), $list[$i]['three'])
                        ->setCellValue('P' . ($i + 2), $list[$i]['four'])
                        ->setCellValue('Q' . ($i + 2), $list[$i]['five'])
                        ->setCellValue('R' . ($i + 2), $list[$i]['six'])
                        ->setCellValue('S' . ($i + 2), $list[$i]['seven'])
                        ->setCellValue('T' . ($i + 2), $list[$i]['eight'])
                        ->setCellValue('U' . ($i + 2), $list[$i]['nine'])
                        ->setCellValue('V' . ($i + 2), $list[$i]['ten']);
                        
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=test.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    //产品类二维码维护
    public function cpcodewh() {
        $npic_twocode_father = M('npic_twocode_father');
        $npic_twocode = M('npic_twocode');
        if ($_POST) {

            $ctoken = $_POST['ctoken'];
            $sous = $_POST['sous'];
          

            //$list1=$npic_twocode->where("token='$token' and type='1' and cname='%$%'")->select();
            $sql = "select * from tp_npic_twocode where token='$ctoken' and type='2' and ccode!='' and cname like '%$sous%'";
            $list1 = $npic_twocode->query($sql);
            $num = count($list1);
            $p = new Page($num, 20);
            $firstRow = $p->firstRow;
            $listRows = $p->listRows;
            $sql1 = "select * from tp_npic_twocode where token='$ctoken' and type='2' and ccode!='' and cname like '%$sous%' limit {$firstRow},{$listRows}";
            $list = $npic_twocode->query($sql1);
            $page = $p->show();
            $this->assign('page', $page);
            $this->assign('list', $list);

            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
            $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$ctoken'")->select();



            $xxlist = $npic_twocode_xians->where("token='$ctoken'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$ctoken'")->select();


            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign('cpxxcitylist', $cpxxcitylist);
        } else {


            $token = $_GET['token'];
            $cname=$_GET['cname'];


            $list1 = $npic_twocode->where("token='$token' and type='2' and cname='$cname' and ccode!=''")->select();

            $num = count($list1);
            $Page = new Page($num, 25);
            $show = $Page->show();
            $list = $npic_twocode->where("token='$token' and type='2' and cname='$cname' and ccode!=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $show);
            $this->assign('list', $list);
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
            $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();



            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();


            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign('cpxxcitylist', $cpxxcitylist);

           
        }
        $this->assign('tab', 'cpwhcode');
        $this->display();
    }

    //产品二维码删除
    public function cpcode_del() {
        $npic_twocode = M('npic_twocode');
        $token = $_GET['token'];
        $cid = $_GET['id'];
        $ll = $npic_twocode->where("token='$token' and cid='$cid'")->delete();
        if ($ll) {
            $this->success('删除成功');
        } else {

            $this->error('删除失败');
        }
    }

    //产品二维码批量删除
    public function cpcodedel() {
        $npic_twocode = M('npic_twocode');
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何东西');
        } else {
            $token = $_REQUEST['token'];
            $cid = $_REQUEST['item'];
            $ccid = implode(',', $cid);
            $dl = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->delete();
            if ($dl) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    //产品二维码修改
    public function cpcode_set() {
        $npic_twocode = M('npic_twocode');
        $npic_twocode_father = M('npic_twocode_father');
        if ($_POST) {
            // print_r($_POST);exit;
            if (strtotime($_POST['sdate']) > strtotime($_POST['edate'])) {
                $this->error("开始时间不能大于结束时间");
            } else {
                $token = $_POST['token'];
                $cid = $_POST['cid'];
                $data['token'] = $_POST['token'];
                $data['cname'] = $_POST['cpname'];
                
                $data['curl'] = $_POST['codeurl'];
                if($_POST['beizhu1']){
                    $data['beizhu1']=$_POST['beizhu1'];
                }
                if($_POST['beizhu2']){
                    $data['beizhu2']=$_POST['beizhu2'];
                }
                $data['sdate'] = strtotime($_POST['sdate']);
                $data['edate'] = strtotime($_POST['edate']);
               if ($_POST['cpxxid']) {
                    $data['qudao'] = $_POST['cpxxid'];
                }
                if ($_POST['cpcity']) {
                    $data['huodongleixing'] = $_POST['cpcity'];
                }
                if ($_POST['chanpct']) {
                    $data['city'] = $_POST['chanpct'];
                }
                $data['cjtime'] = time();
                $data['type'] = $_POST['cptype'];
                //print_R($data);exit;
                $twocode = $npic_twocode->where("token='$token' and cid='$cid'")->save($data);
                if ($twocode) {
                    $this->success('修改成功', U('Code/cpcodewh', array('token' => $token)));
                } else {

                    $this->error('修改失败', U('Code/cpcodewh', array('token' => $token)));
                }
            }
        } else {

            $cid = $_GET['id'];
            $token = $_GET['token'];
            $npic_twocode_list = $npic_twocode->where("token='$token' and cid='$cid'")->find();
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
            $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();



            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();


            $this->assign('xxlist', $xxlist);
            $this->assign('xxcity', $xxcity);
            $this->assign('cpxxcitylist', $cpxxcitylist);
            
            $this->assign('npic_twocode_list', $npic_twocode_list);
            $this->display();
        }
    }
    //文件夹层级活动二维码
    public function wjjhdcode(){
            $token = $_GET['token'];
            $npic_twocode = M('npic_twocode');
            $list1 = $npic_twocode->group("cname")->where("token='$token' and type='1' and ccode !=''")->select();

            $num = count($list1);
            $Page = new Page($num, 25);
            $show = $Page->show();
            $list = $npic_twocode->group("cname")->where("token='$token' and type='1' and ccode !=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $show);
            $this->assign('list', $list);
           
            
            $this->assign('tab','wjjhdwhcode');
            $this->display();
    }
    //文件夹层级产品二维码
    public function wjjcpcode(){
            $token = $_GET['token'];
            $npic_twocode = M('npic_twocode');
            $list1 = $npic_twocode->group("cname")->where("token='$token' and type='2' and ccode !=''")->select();

            $num = count($list1);
            $Page = new Page($num, 25);
            $show = $Page->show();
            $list = $npic_twocode->group("cname")->where("token='$token' and type='2' and ccode !=''")->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $show);
            $this->assign('list', $list);
            $this->assign('tab','wjjcpwhcode');
            $this->display();
    }

    //产品二维码批量导出
    public function cpcodedc() {
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何内容');
        } else {

            $npic_twocode = M('npic_twocode');
            $cid = $_REQUEST['item'];
            $token = $_REQUEST['token'];
            $npic_twocode_xians = M('npic_twocode_xians');
            $npic_twocode_xxcity = M('npic_twocode_xxcity');
            $npic_twocode_cpxxcity = M('npic_twocode_cpxxcity');
            $npic_twocode_tongji=M('npic_twocode_tongji');
            $cpxxcitylist = $npic_twocode_cpxxcity->where("token='$token'")->select();
       


            $xxlist = $npic_twocode_xians->where("token='$token'")->select();
            $xxcity = $npic_twocode_xxcity->where("token='$token'")->select();

            $ccid = implode(',', $cid);
            $list = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->select();
            //$sql="select sum(cnum) as num from tp_npic_twocode_tongji where token='$token' and c_fid in(" . $ccid . ")";
            //$num=$npic_twocode_tongji->query($sql);

            $str1 = substr(THINK_PATH, 0, -1);
            require_once $str1 . '/PigCms/Lib/Action/User/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                    ->setLastModifiedBy("Maarten Balliauw")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '产品名称')
                    ->setCellValue('C1', '活动URL')
                   
                    ->setCellValue('D1', '渠道')
                    ->setCellValue('E1', '类型')
                    ->setCellValue('F1', '城市')
                    ->setCellValue('G1', '扫描次数')
                    ->setCellValue('H1', '开始时间')
                    ->setCellValue('I1', '结束时间')
                    ->setCellValue('J1', '二维码')
                    ->setCellValue('K1', '备注1')
                    ->setCellValue('L1', '备注2')
                    ->setCellValue('M1', '0-6点')
                    ->setCellValue('N1', '6-8点')
                    ->setCellValue('O1', '8-10点')
                    ->setCellValue('P1', '10-12点')
                    ->setCellValue('Q1', '12-14点')
                    ->setCellValue('R1', '14-16点')
                    ->setCellValue('S1', '16-18点')
                    ->setCellValue('T1', '18-20点')
                    ->setCellValue('U1', '20-22点')
                    ->setCellValue('V1', '22-24点');


            // Miscellaneous glyphs, UTF-8
            for ($i = 0; $i < count($list); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $list[$i]['cid'])
                        ->setCellValue('B' . ($i + 2), $list[$i]['cname'])
                        ->setCellValue('C' . ($i + 2), $list[$i]['curl']);
                      
                foreach ($xxlist as $xxlistkk => $xxlistvv) {
                    if ($list[$i]['qudao'] == $xxlistvv['xid']) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('D' . ($i + 2), $xxlistvv['x_name']);
                    }
                }
                foreach($cpxxcitylist as $cpxxcitylistkk =>$cpxxcitylistvv){
                    if ($list[$i]['huodongleixing'] == $cpxxcitylistvv['cid']) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('E' . ($i + 2), $cpxxcitylistvv['c_name']);
                    }
                }
                foreach($xxcity as $xxcitykk =>$xxcityvv){
                    if($list[$i]['city']==''){
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('F' . ($i + 2), '无');
                    }else{
                         if ($list[$i]['city'] == $xxcityvv['cid']) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('F' . ($i + 2), $xxcityvv['c_name']);
                       }
                    }
                   
                }
                
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('G' . ($i + 2), $list[$i]['code_num'])
                        ->setCellValue('H' . ($i + 2), date("Y-m-d:H:i:s", $list[$i]['sdate']))
                        ->setCellValue('I' . ($i + 2), date("Y-m-d:H:i:s", $list[$i]['edate']))
                        ->setCellValue('J' . ($i + 2), $list[$i]['ccode'])
                        ->setCellValue('k' . ($i + 2), $list[$i]['beizhu1'])
                        ->setCellValue('L' . ($i + 2), $list[$i]['beizhu2'])
                         ->setCellValue('M' . ($i + 2), $list[$i]['one'])
                        ->setCellValue('N' . ($i + 2), $list[$i]['two'])
                        ->setCellValue('O' . ($i + 2), $list[$i]['three'])
                        ->setCellValue('P' . ($i + 2), $list[$i]['four'])
                        ->setCellValue('Q' . ($i + 2), $list[$i]['five'])
                        ->setCellValue('R' . ($i + 2), $list[$i]['six'])
                        ->setCellValue('S' . ($i + 2), $list[$i]['seven'])
                        ->setCellValue('T' . ($i + 2), $list[$i]['eight'])
                        ->setCellValue('U' . ($i + 2), $list[$i]['nine'])
                        ->setCellValue('V' . ($i + 2), $list[$i]['ten']);
            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=test.xls");
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    public function plbcimg() {
        set_time_limit(0);
        $str1 = substr(THINK_PATH, 0, -1);
        require_once $str1 . '/PigCms/Lib/Action/User/Classes/zip.php';
        $npic_twocode = M('npic_twocode');
        if ($_REQUEST['item'] == '') {
            $this->error('您没有选中任何内容');
        } else {
            $cid = $_REQUEST['item'];
            $token = $_REQUEST['token'];
            $ccid = implode(',', $cid);
            $list = $npic_twocode->where("token='$token' and cid in(" . $ccid . ")")->select();
            //print_r($list);exit;
            foreach ($list as $k => $v) {
                //echo basename($v);
                //echo "<br/>";

                file_put_contents('./PUBLIC/imagess/' . $_SESSION['token'] . '/codezip/' . basename($v['ccode']), file_get_contents($v['ccode']));
            }
            $zip = new PclZip("code.zip"); //压缩文件
            $zip->create('./PUBLIC/imagess/' . $_SESSION['token'] . '/codezip/');
            $d = './PUBLIC/imagess/' . $_SESSION['token'] . '/codezip/';
            if ($od = opendir($d)) {   //$d是目录名
                while (($file = readdir($od)) !== false) {  //读取目录内文件   //echo file_exists($file);exit;
                    //echo $file; echo '<br>';
                    if ($file != '.' && $file != '..') {


                        $file_path = $d . '/' . $file;
                        unlink($file_path);
                        //file_put_contents('./xxxxx.txt','OK');
                    }

                    //unlink($file);  //$file是文件名
                }
            }
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=code.zip"); //$downname是下载后的文件名
            readfile('code.zip');
        }
    }

}

//下载图片函数
function forceDownload($filename = '', $data = '') {
    if ($filename == '' OR $data == '') {
        return FALSE;
    }
    # Try to determine if the filename includes a file extension.
    # We need it in order to set the MIME type
    if (FALSE === strpos($filename, '.')) {
        return FALSE;
    }
    # Grab the file extension
    $x = explode('.', $filename);
    $extension = end($x);
    # Load the mime types
    $mimes = array('hqx' => 'application/mac-binhex40', 'cpt' => 'application/mac-compactpro', 'csv' => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'), 'bin' => 'application/macbinary', 'dms' => 'application/octet-stream', 'lha' => 'application/octet-stream', 'lzh' => 'application/octet-stream', 'exe' => array('application/octet-stream', 'application/x-msdownload'), 'class' => 'application/octet-stream', 'psd' => 'application/x-photoshop', 'so' => 'application/octet-stream', 'sea' => 'application/octet-stream', 'dll' => 'application/octet-stream', 'oda' => 'application/oda', 'pdf' => array('application/pdf', 'application/x-download'), 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'smi' => 'application/smil', 'smil' => 'application/smil', 'mif' => 'application/vnd.mif', 'xls' => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'), 'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'), 'wbxml' => 'application/wbxml', 'wmlc' => 'application/wmlc', 'dcr' => 'application/x-director', 'dir' => 'application/x-director', 'dxr' => 'application/x-director', 'dvi' => 'application/x-dvi', 'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip', 'php' => 'application/x-httpd-php', 'php4' => 'application/x-httpd-php', 'php3' => 'application/x-httpd-php', 'phtml' => 'application/x-httpd-php', 'phps' => 'application/x-httpd-php-source', 'js' => 'application/x-javascript', 'swf' => 'application/x-shockwave-flash', 'sit' => 'application/x-stuffit', 'tar' => 'application/x-tar', 'tgz' => array('application/x-tar', 'application/x-gzip-compressed'), 'xhtml' => 'application/xhtml+xml', 'xht' => 'application/xhtml+xml', 'zip' => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'), 'mid' => 'audio/midi', 'midi' => 'audio/midi', 'mpga' => 'audio/mpeg', 'mp2' => 'audio/mpeg', 'mp3' => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'), 'aif' => 'audio/x-aiff', 'aiff' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio', 'rpm' => 'audio/x-pn-realaudio-plugin', 'ra' => 'audio/x-realaudio', 'rv' => 'video/vnd.rn-realvideo', 'wav' => 'audio/x-wav', 'bmp' => 'image/bmp', 'gif' => 'image/gif', 'jpeg' => array('image/jpeg', 'image/pjpeg'), 'jpg' => array('image/jpeg', 'image/pjpeg'), 'jpe' => array('image/jpeg', 'image/pjpeg'), 'png' => array('image/png', 'image/x-png'), 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'css' => 'text/css', 'html' => 'text/html', 'htm' => 'text/html', 'shtml' => 'text/html', 'txt' => 'text/plain', 'text' => 'text/plain', 'log' => array('text/plain', 'text/x-log'), 'rtx' => 'text/richtext', 'rtf' => 'text/rtf', 'xml' => 'text/xml', 'xsl' => 'text/xml', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mpe' => 'video/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'avi' => 'video/x-msvideo', 'movie' => 'video/x-sgi-movie', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'word' => array('application/msword', 'application/octet-stream'), 'xl' => 'application/excel', 'eml' => 'message/rfc822', 'json' => array('application/json', 'text/json'));
    # Set a default mime if we can't find it
    if (!isset($mimes[$extension])) {
        $mime = 'application/octet-stream';
    } else {
        $mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
    }
    header('Content-Type: "' . $mime . '"');
    $tmpName = $filename;
    $filename = '"' . urlencode($tmpName) . '"'; #ie中文文件名支持
    if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'firefox') != false) {
        $filename = '"' . $tmpName . '"';
    }#firefox中文文件名支持
    if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'chrome') != false) {
        $filename = urlencode($tmpName);
    }#Chrome中文文件名支持
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header("Content-Transfer-Encoding: binary");
    header('Pragma: no-cache');
    header("Content-Length: " . strlen($data));
    exit($data);
}

/**
 * 二维码操作
 * @param type $chl
 * @param type $widhtHeight
 * @param type $EC_level
 * @param type $margin
 * @return stringer
 */
function generateQRfromGoogle($chl, $widhtHeight = '150', $EC_level = 'L', $margin = '0') {
    $chl = urlencode($chl);
    $src = 'http://chart.apis.google.com/chart?chs=' . $widhtHeight . 'x' . $widhtHeight . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $chl;
    return $src;
}
