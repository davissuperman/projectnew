<?php
class Upload2Action extends UserAction{



   public function add(){
     
			header('Content-Type: text/html; charset=UTF-8');
			$tooken=$_SESSION['token'];
			$tpwxuser=M('wxuser'); // 实例化wxUser对象
             $wxuser=$tpwxuser->where("token='$tooken'")->find();
			 if($wxuser['usednum']<=0){
				 echo '空间不足请联系管理员';exit;
			 }else{
			//echo $tooken;exit;
			$inputName='filedata';//表单文件域name
			$attachDir='./PUBLIC/imagess/'.$tooken;//上传文件保存路径，结尾不要带/
			$dsizee=$this->dirSize($attachDir);
		
		    $numberr=$dsizee/1024/1024;
		    $numbb=round($numberr,6);//没有上传图片的时候计算整个文件夹的大小
			
			//$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
			$maxAttachSize=2097152;//最大上传大小，默认是2M
			$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
			$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
			$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用
			ini_set('date.timezone','Asia/Shanghai');//时区

			$err = "";
			$msg = "''";
			$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
			
			$localName='';

			if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
				file_put_contents($tempPath,file_get_contents("php://input"));
				$localName=urldecode($info[2]);
			}
			else{//标准表单式上传
				$upfile=@$_FILES[$inputName];
				if(!isset($upfile))$err='文件域的name错误';
				elseif(!empty($upfile['error'])){
					switch($upfile['error'])
					{
						case '1':
							$err = '文件大小超过了php.ini定义的upload_max_filesize值';
							break;
						case '2':
							$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
							break;
						case '3':
							$err = '文件上传不完全';
							break;
						case '4':
							$err = '无文件上传';
							break;
						case '6':
							$err = '缺少临时文件夹';
							break;
						case '7':
							$err = '写文件失败';
							break;
						case '8':
							$err = '上传被其它扩展中断';
							break;
						case '999':
						default:
							$err = '无有效错误代码';
					}
				}
				elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
				else{
					move_uploaded_file($upfile['tmp_name'],$tempPath);
					$localName=$upfile['name'];
				}
			}
                $dsize=$this->dirSize($attachDir);
		
		        $number=$dsize/1024/1024;
		        $numb=round($number,6);//上传图片以后的文件夹文件大小
				$numb1=$numb-$numbb;//第二次计算出来的文件夹大小减去第一次没有上传的文件夹大小计算出一张图片的大小
				$numb2=round($numb1,6);
		        $tpwxuser=M('wxuser'); // 实例化wxUser对象
                $wxuser=$tpwxuser->where("token='$tooken'")->find();
		        $udnum=$wxuser['usednum'];
		       
		       
				$numdd1=$udnum-$numb2;
		        //echo $numdd1.'<br>';
		        //$numdd2=round($numdd1,2);
		        $numdd2=((int)($numdd1*100))/100;
				$data['usednum']=$numdd2;
				$qry=$tpwxuser->where("token='$tooken'")->data($data)->save(); 
		
				//var_dump($qry);exit;
				/*if(!$qry){
				    
                      $this->error('提交失败',U('Upload2/add')); 
				 }*/
            
			if($err==''){
				$fileInfo=pathinfo($localName);
				$extension=$fileInfo['extension'];
				if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
				{
					$bytes=filesize($tempPath);
					if($bytes > $maxAttachSize)$err='请不要上传大小超过'.$this->formatBytes($maxAttachSize).'的文件';
					else
					{
						/*switch($dirType)
						{
							case 1: $attachSubDir = 'day_'.date('ymd'); break;
							case 2: $attachSubDir = 'month_'.date('ym'); break;
							case 3: $attachSubDir = 'ext_'.$extension; break;
						}*/
						$attachDir = $attachDir;
						if(!is_dir($attachDir))
						{
							@mkdir($attachDir, 0777);
							@fclose(fopen($attachDir.'/index.htm', 'w'));
						}
						PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
						$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
						$targetPath = $attachDir.'/'.$newFilename;
						
						rename($tempPath,$targetPath);
						@chmod($targetPath,0755);
						$targetPath=$this->jsonString($targetPath);
						if($immediate=='1')$targetPath='!'.$targetPath;
						if($msgType==1)$msg="'$targetPath'";
						else $msg="{'url':'".$targetPath."','localname':'".$this->jsonString($localName)."','id':'1','num':'".$numbb."','numb':'".$numb."','numdd2':'".$numdd2."'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
					}
				}
				else $err='上传文件扩展名必需为：'.$upExt;
             
				@unlink($tempPath);
				echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
				
			}	

	}

}

  
			
 
       
			public function jsonString($str)
			{
				return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
			}
			public function formatBytes($bytes) {
				if($bytes >= 1073741824) {
					$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
				} elseif($bytes >= 1048576) {
					$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
				} elseif($bytes >= 1024) {
					$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
				} else {
					$bytes = $bytes . 'Bytes';
				}
				return $bytes;
			}


			 #利用递归的方式统计目录的大小
			function dirSize($dirName){
			  $dirsize=0;
			  $dir=opendir($dirName);
			  while($fileName=readdir($dir)){
			   $file=$dirName."/".$fileName;
			   if($fileName!="." && $fileName!=".."){      //一定要进行判断，否则会出现错误的
					if(is_dir($file)){
					 $dirsize+=dirSize($file);
					}
					else{
					 $dirsize+=filesize($file);
					}
			   }
		  }
			  closedir($dir);
			  return $dirsize;
		 }
  
}
?>