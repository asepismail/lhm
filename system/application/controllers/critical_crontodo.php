<?php
class critical_crontodo extends Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('mcrontodo');  
    }
 
	function index(){
		$today = date('Ymd');
		//$yesterday =$today-3;
		$yesterday =$today-3; //for request only
		$f_day_of_month = date("Y").date("m")."01";
		
		$array_company = array('MAG','GKM', 'LIH', 'NAK', 'TPAI');
		//$array_company = array('MAG','LIH'); //for request only
		foreach($array_company as $i => $value){
			$this->mcrontodo->synchronize($value, $f_day_of_month, $yesterday); //synchronize timbangan
			if ($value=='GKM'){
				$this->mcrontodo->synchronize_group($value, $f_day_of_month, $yesterday); //synchronize timbangan for GKM Group
			}
			$this->mcrontodo->synchronize_dispatch($value, $f_day_of_month, $yesterday); //synchronize dispatch
		}		
	}
}
?>