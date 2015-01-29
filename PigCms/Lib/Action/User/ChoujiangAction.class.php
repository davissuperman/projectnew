<?php
class ChoujiangAction extends UserAction{
	public function index(){
		$count = M('Choujiang')->count();
		//echo $count;
		$page = new Page( $count,15 );
		$show = $page->show();
		$list=M('Choujiang')->limit( $page->firstRow.','.$page->listRows )->select();
		//var_dump($list);
		
		$this->assign('list',$list);
		$this->Assign( 'page',$show );
		$this->display();	
	}
	
}


?>