<?php
class crontodo extends Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('mcrontodo');  
    }
 
	function index(){
		$today = date('Ymd');
		//start: today-2
		//$yesterday = strtotime('-2 day',strtotime($today)); 
		$yesterday = strtotime('-2 day',strtotime($today)); 
		$yesterday = date('Ymd', $yesterday);
		$array_company = array('MAG','GKM', 'LIH', 'NAK', 'TPAI');
		//$array_company = array('MAG'); //for request only
		foreach($array_company as $i => $value){
			$this->mcrontodo->synchronize($value, $yesterday); //synchronize timbangan
			$this->mcrontodo->synchronize_bunchEmpty($value, $yesterday); //synchronize bunchEmpty
			if ($value=='GKM'){
				$this->mcrontodo->synchronize_group($value, $yesterday); //synchronize timbangan for GKM Group
			}
			$this->mcrontodo->synchronize_dispatch($value, $yesterday); //synchronize dispatch
		}
		//end: today-2
		
		//start: today-1
		//$yesterday = strtotime('-1 day',strtotime($today)); 
		$yesterday = strtotime('-1 day',strtotime($today)); 
		$yesterday = date('Ymd', $yesterday);

		$array_company = array('MAG','GKM', 'LIH', 'NAK', 'TPAI');
		//$array_company = array('MAG','GKM', 'LIH', 'NAK'); //for request only
		foreach($array_company as $i => $value){
			$this->mcrontodo->synchronize($value, $yesterday); //synchronize timbangan
			$this->mcrontodo->synchronize_bunchEmpty($value, $yesterday); //synchronize bunchEmpty
			if ($value=='GKM'){
				$this->mcrontodo->synchronize_group($value, $yesterday); //synchronize timbangan for GKM Group
			}
			$this->mcrontodo->synchronize_dispatch($value, $yesterday); //synchronize dispatch
		}
		//end: today-1
	}
}
?>