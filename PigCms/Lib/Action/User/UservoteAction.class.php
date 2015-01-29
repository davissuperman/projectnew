<?php
class UservoteAction extends UserAction{
		public function _initialize() {	
		parent::_initialize();
		 $token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'fenxiang')){
            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
	}
	public function Index(){
			if( IS_POST ){//判断是否为提交时间
				$vote['subject'] = $_POST['title'];//标题
				$vote['message'] = $_POST['content'];//内容
				$vote['multiple'] = $_POST['failtip'];//是否多选
				$vote['style'] = $_POST['style'];//样式
				$vote['time']=	$_POST['enddate'];//时间
				$vote['adress']=$_POST['address'];//地址
				$vote[ "token" ] = $_POST['token'];
				$votea['options'] = $_POST['hidden'];
				$votelogo[ 'logos' ] = $_POST['loghidden'];
				$vote['keyword'] = $_POST['keyword'];
				$vote['adate']=time();
				$c = M("wedding_vote");//初始化投票表
				$a = $c->add( $vote );//将值插入到表中
				if ($votea['options']) {//判断选项值是否存在
					$arrays = explode("^", $votea['options']);//拆分选项
					array_pop($arrays);
					$votea['options'] = $arrays;//重新赋值
				}
				if ($votelogo[ 'logos' ]) {//判断图片值是否为空
					$log = explode("^", $votelogo[ 'logos' ]);//拆分图片表
					array_pop($log);
					$votelogo[ 'logos' ] = $log;//将拆分的图片重新赋值
				}
			if( !empty($a) ){//判断投票是否插入成功插入成功就把选项插入表中
					foreach( $votea['options'] as $key=>$val ){
					$infosA[ 'vid' ] = $a;
					$infosA[ 'displayorder' ] = $key+1;
					$infosA[ 'voteoption' ] = $val;
					$b = M("wedding_vote_option");
					$o =$b->add( $infosA );
					}
				} 
				if( !empty($a) ){//判断投票是否插入成功插入成功就把图片插入表中
					foreach( $votelogo[ 'logos' ] as $key=>$val ){
					$fileI[ 'vid' ] = $a;
					$fileI[ 'filename' ] = 'staff'+$key;
					$fileI[ 'attachment' ] = $val;
					//$fileI[ 'filesize' ] = $val[ 'size' ];
					$fileI[ 'dateline' ] = time();
					$f = M( 'wedding_vote_attachment' );
					$vi = $f->add( $fileI );
				}
			}
			if( !empty( $a ) ){//判断投票是否插入成功插入成功就把关键字插入表中
					$yy['pid']     = $a;
                    $yy['module']  = "Uservote";
                    $yy['token']   = $vote[ "token" ];
                    $yy['keyword'] = $vote['keyword'];
                    M('Keyword')->add($yy);
			}					
			if( $o && $vi ){//如果都执行成功就返回到页面	
					$this->success('操作成功',U('User/Vote/tplist',array('token'=>$vote[ "token" ])));exit;
						}else{
					$this->error("操作失败请检查");
				}
			}
			$token = $this->_get( 'token',trim );
			$this->Assign( 'token',$token );
			$this->assign( 'endtime',time() );
			$this->Display();
		}	
	}
?>