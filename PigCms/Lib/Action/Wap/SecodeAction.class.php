<?php
require"/Lib/Core/Ftp.class.php";
class SecodeAction extends Action {

    public function _initialize() {

    }


    public function generateFile(){
        //获取当天的数据
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-1,date("Y")));
        $list = M('Secode')->query(
            "SELECT * from tp_secode where createtime >= '$start' and createtime<='$end' and status=1
               order by createtime desc");
        $listArr = array();
        $fileData = date("Ymd");
        $localfile = DATA_PATH.'FWCX_'.$fileData.'.txt';
        $firstLine = "防伪码,微信ID,查询日期,查询IP\n";
        file_put_contents($localfile,$firstLine);
        foreach($list as $key => $each){
            $tmp = array();
            $tmp['code'] = $each['code'];
            $tmp['openid'] = $each['openid'];
            $tmp['ip'] = $each['ip'];
            $tmp['createtime'] = $each['createtime'];
            $listArr[] = $tmp;
            $eachLine = $each['code'].",". $each['openid'].",". $each['createtime'].",".$each['ip']."\n";
            file_put_contents($localfile,$eachLine,FILE_APPEND);
        }

    }
        public function index() {
            $this->generateFile();

            $ftp = new Ftp();//实例化对象
            $data['server'] = '116.236.205.203';//服务器地址(IP or domain)
            $data['username'] = 'crm';//ftp帐户
            $data['password'] = 'Drjou.1934-2015';//ftp密码
            $data['port'] = 21;//ftp端口,默认为21
            $data['pasv'] = false;//是否开启被动模式,true开启,默认不开启
            $data['ssl'] = false;//ssl连接,默认不开启
            $data['timeout'] = 60;//超时时间,默认60,单位 s
            if($ftp->start($data))
            {
                // 远程连接成功;
                //检测目录&创建目录
//                $remotedir = '/crm/UAT/test';
//                if (!$ftp->chdir($remotedir))
//                {
//                    $ftp->mkdir($remotedir);
//                }

                $fileData = date("Ymd");
                $localfile = DATA_PATH.'FWCX_'.$fileData.'.txt';
                $remotefile =  "/crm/UAT/$fileData.txt";
                if( $ftp->put($remotefile,$localfile))
                {
                    echo " good ";
                    //上传文件成功!
                }
//                //其它功能
//                $ftp->rmdir($dirname);//删除目录
//                $ftp->delete($filename);//删除文件
//                $ftp->nlist($dirname);//返回目录列表
//                $ftp->get_error();//错误调试信息

            }
            $ftp->close();

    }



}
