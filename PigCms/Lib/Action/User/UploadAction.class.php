<?php
class UploadAction extends UserAction{

	
	public function index(){
	    //var_dump($_SESSION);
	   
       //$this->display();
	}
    
	public function add(){
		//echo $_POST['policy1'];exit;
		
		
		import("@.ORG.UploadFile");
		$tooken=$_SESSION['token'];
		$idd=$_POST['idd'];
		$tpwxuser=M('wxuser'); // 实例化wxUser对象
        $wxuser=$tpwxuser->where("token='$tooken'")->find();
       
        $upload = new UploadFile(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->savePath = './PUBLIC/imagess/'.$tooken.'/'; // 设置附件上传目录
		//根据当前的文件路径计算出没有上传图片时候的文件夹大小
		$dirr=$upload->savePath = './PUBLIC/imagess/'.$tooken.'/';
	
		
		
		if (!$upload->upload()) { // 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
			$this->error('提交失败',U('Upload/add')); 
        } else { // 上传成功获取上传文件信息
            $info = $upload->getUploadFileInfo();
			$lit = $info[0]['savepath'];
			//根据当前文件夹路径计算出上传以后的文件夹大小
		
			
			$path = ltrim($lit,'.');
			//echo $path.$info[0]['savename'];exit;//返回的文件路径
			
			 
			$this->assign('url','http://wx.drjou.cc'.$path.$info[0]['savename']);
			$this->assign('idd',$idd);
			$this->display();
        }
      
		
       
		
		 
		
	    
	
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
  
  public function upload(){
		$idd=$_REQUEST['idd'];
	    if (!isset($_SESSION['username'])&&!isset($_SESSION['uid'])){
			exit('非法操作');
		}
		$bucket = $this->bucket; /// 空间名
		$form_api_secret = $this->form_api_secret; /// 表单 API 功能的密匙（请访问又拍云管理后台的空间管理页面获取）

		$options = array();
		$options['bucket'] = $bucket; /// 空间名
		$options['expiration'] = time()+600; /// 授权过期时间
		$options['save-key'] = '/'.$this->token.'/{year}/{mon}/{day}/'.time().'_{random}{.suffix}'; /// 文件名生成格式，请参阅 API 文档
		$options['allow-file-type'] = C('up_exts'); /// 控制文件上传的类型，可选
		$options['content-length-range'] = '0,'.intval(C('up_size'))*1000; /// 限制文件大小，可选
		if (intval($_GET['width'])){
			$options['x-gmkerl-type'] = 'fix_width';
			$options['fix_width '] = $_GET['width'];
		}
		$options['return-url'] = C('site_url').'/index.php?g=User&m=Upload&a=uploadReturn'; /// 页面跳转型回调地址
		$policy = base64_encode(json_encode($options));
		$sign = md5($policy.'&'.$form_api_secret); /// 表单 API 功能的密匙（请访问又拍云管理后台的空间管理页面获取）
		$this->assign('bucket',$bucket);
		$this->assign('sign',$sign);
		$this->assign('policy',$policy);
		$this->assign('idd',$idd);
		$this->display();
	}


   //最新开通的模块列表
   public function xinzeng(){
   
     $this->display();
   }
	

}


?>