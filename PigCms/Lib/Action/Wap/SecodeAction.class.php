<?php
require realpath (__ROOT__)."/Lib/Core/Ftp.class.php";
class SecodeAction extends Action {
    public $fileDate;
    public $tab;
    public $codeList;
    public $usrList;
    public function _initialize() {
        $this->fileDate = date("Ymd",strtotime("-1 day"));
        $this->tab = '	  ';
    }
    public function generateHyFile(){
        //获取当天的数据
        $start = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $end = mktime(23,59,59,date("m"),date("d")-1,date("Y"));
        $list = M('customer_service_fans')->query("SELECT * from tp_customer_service_fans WHERE subscribe=1   and  subscribe_time >= $start and subscribe_time<=$end order by subscribe_time limit 300000");
        $listArr = array();
        $fileData = $this->fileDate;
        $localfile = DATA_PATH.'HY_'.$fileData.'.txt';
//        $firstLine = "微信ID,昵称,性别,国家,省份,城市\n";
//        file_put_contents($localfile,$firstLine);
        foreach($list as $key => $each){
            $filePrefix = floor($key/5000);
            $localfile = DATA_PATH.'HY_'.$fileData."_$filePrefix".'.txt';
            $sex = $each['sex'];
            if($sex == 1){
                $sex='男';
            }else if($sex == 2){
                $sex='女';
            }else{
                $sex='未知';
            }
            $nickname = $each['nickname'];
            $nickname=str_replace("\n","",$nickname);
            $nickname=str_replace("\r","",$nickname);
            $nickname=str_replace("\r\n","",$nickname);
            $eachLine = $each['openid'].$this->tab. $nickname.$this->tab.$sex.$this->tab. $each['country'].$this->tab. $each['province'].$this->tab. $each['city'].$this->tab.$this->tab."\r\n";
//            $eachLine = $each['openid'].$this->tab. $each['nickname'].$this->tab.$sex.$this->tab. $each['province'].$this->tab.$this->tab."\r\n";
            $eachLine = iconv("UTF-8", "GBK", $eachLine);
            if(!$eachLine){
                $eachLine = $each['openid'].$this->tab. " ".$this->tab.$sex.$this->tab. $each['country'].$this->tab. $each['province'].$this->tab. $each['city'].$this->tab.$this->tab."\r\n";
            }
//            mb_convert_encoding($eachLine,"windows-1252","UTF-8") ;
            file_put_contents($localfile,$eachLine,FILE_APPEND);
            $this->usrList = $filePrefix;
        }

    }

    public function generateFile(){
        //获取当天的数据
        $start = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
        $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-1,date("Y")));
        $list = M('Secode')->query(
            "SELECT secode.*,fans.nickname,fans.province from tp_secode as secode
              left join tp_customer_service_fans as fans on (fans.openid=secode.openid)
              where secode.createtime >= '$start' and secode.createtime<='$end' and secode.status=1
               order by createtime desc");
        $listArr = array();
        $fileData = $this->fileDate;
        $localfile = DATA_PATH.'FWCX_'.$fileData.'.txt';
//        $firstLine = "防伪码,微信ID,查询日期,查询IP\n";
//        file_put_contents($localfile,$firstLine);
        foreach($list as $key => $each){
            $filePrefix = floor($key/5000);
            $localfile = DATA_PATH.'FWCX_'.$fileData."_$filePrefix".'.txt';
            $tmp = array();
            $tmp['code'] = $each['code'];
            $tmp['openid'] = $each['openid'];
            $tmp['ip'] = $each['ip'];
            $tmp['createtime'] = $each['createtime'];
            $tmp['nickname'] = $each['nickname'];
            $listArr[] = $tmp;
            $ip = $each['ip'];
            if(!$ip){
                $ip = 'unknown';
            }
            $eachLine = $each['code'].$this->tab. $each['openid'].$this->tab. $each['createtime'].$this->tab.$each['province'].$this->tab.$ip."\r\n";
            $eachLine = iconv("UTF-8", "GBK", $eachLine);
            file_put_contents($localfile,$eachLine,FILE_APPEND);
            $this->codeList = $filePrefix;
        }

    }
        public function index() {
            $fileData = $this->fileDate;
            //delete old file
            $fList = scandir(DATA_PATH);
            foreach($fList as $eachFileNeedDelete){
                if($eachFileNeedDelete != '.' && $eachFileNeedDelete != '..'){
                    $needDeleteReal = DATA_PATH.$eachFileNeedDelete;
                    echo $needDeleteReal." <br/>";
                    unlink($needDeleteReal);
                }
            }
            //end delete
            $this->generateFile();
            $this->generateHyFile();
            $ftp = new Ftp();//实例化对象
            $data['server'] = '114.215.188.89';//服务器地址(IP or domain)
            $data['username'] = 'crm';//ftp帐户
            $data['password'] = 'Drjou.1934-2015';//ftp密码
            $data['port'] = 9090;//ftp端口,默认为21
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


                for($i=0;$i<=$this->codeList;$i++){
                    $localfile = DATA_PATH.'FWCX_'.$fileData."_$i".'.txt';
                    $remotefile =  "/crm/UAT/FWCX_$fileData"."_$i".'.txt';
                    if( $ftp->put($remotefile,$localfile))
                    {
                        echo " $i FWCX_ success ";
                    }
                }

                for($n=0;$n<=$this->usrList;$n++){
                    $localfile2 = DATA_PATH.'HY_'.$fileData."_$n".'.txt';
                    $remotefile2 =  "/crm/UAT/HY_$fileData"."_$n".'.txt';
                    if( $ftp->put($remotefile2,$localfile2))
                    {
                        echo " $n HY_ success ";
                    }
                }
//                $localfile2 = DATA_PATH.'HY_'.$fileData.'.txt';
//                $remotefile2 =  "/crm/UAT/HY_$fileData.txt";
//                if( $ftp->put($remotefile2,$localfile2))
//                {
//                    echo "HY_ success ";
//                }
//                //其它功能
//                $ftp->rmdir($dirname);//删除目录
//                $ftp->delete($filename);//删除文件
//                $ftp->nlist($dirname);//返回目录列表
//                $ftp->get_error();//错误调试信息

            }
            $ftp->close();

    }



}
