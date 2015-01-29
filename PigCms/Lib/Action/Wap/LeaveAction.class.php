<?php
/*
***微信留言 功能 
***开发：李建伟
*/
class LeaveAction extends BaseAction{
		public $token;
		public $wecha_id;
		public function Index(){
				$this->token = $this->_get('token','trim');
				$this->wecha_id = $this->_get( 'wecha_id','intval' );
				$W = M( 'wxuser' );
				$filed = $W->where( array( 'token'=>$this->token) )->getfield( 'token' );
				if( empty( $filed ) )die( 'Hacker attacks!' );
				$Token = trim( $this->token );
				$m = M( 'message_leave' );	
				$info = $m->where( array( 'token'=>$this->token ) )->order( 'time desc' )->select();		
				$d = M( 'message_leaver' );
				for( $i = 0;$i<count( $info );$i++ ){
				$in[ $info[ $i ][ 'lid' ] ] = $d->order( 'time desc' )->where( array( 'fid' =>$info[ $i ][ 'lid' ] ),array( 'token'=>$Token ) )->select();		
				}
				$um= M( 'message_uleave' );
				$uinfo = $um->where(array( 'token'=>$this->token ) )->find();
				
				if($uinfo){
					$this->Assign( 'uurl',$uinfo['uurl'] );
				}else{
					$this->Assign( 'uurl','/tpl/Wap/default/common/images/leave/mpic.jpg' );
				}
				
				$this->Assign( 'token',$Token );
				$this->Assign( 'in',$in );
				$this->Assign( 'list',$info );
				$this->Display();
	}
	public function oT(){
						$nickname = $this->Gl( $_POST[ 'nickname' ] );
						$info = $this->Gl( $_POST[ 'info' ] );
						$token = $this->Gl( $_POST[ 'action' ] );
						$m = M( 'message_leave' );
						$data[ 'Otitle' ] = $nickname;
						$data[ 'Ttitle' ] = $info;
						$data[ 'token' ] = $token;
						$data[ 'time' ] = time();
						$id = $m->add( $data );
						if( $id ){
							echo 1;
						}else{
							echo 0;
						}		
		}
	public function wT(){
					$fid = !empty( $_POST[ 'fid' ] ) ? intval( $_POST[ 'fid' ] ) : exit;
					$inf = $this->Gl( $_POST[ 'info' ] );
					$token = $this->Gl( $_POST[ 'action' ] );
					$m = M( 'message_leaver' );
					$data[ 'fid' ] = $fid;
					$data[ 'title' ] = $inf;
					$data[ 'token' ] = $token;
					$data[ 'time' ] = time();
 					$id = $m->add( $data );
					if( $id ){
							echo 1;
					}else{
							echo 0;
					}		
		}
	Private function Gl( $g ){
			$G = !empty( $g ) ? trim( $g ) : exit; 
			$G = htmlspecialchars($G, ENT_QUOTES);
			if( !get_magic_quotes_gpc() ){
				$G = addslashes( $G );
			}
				return $G;	
	}
	
}
?>