<?php
class m_closing_control extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('model_m_closing_control');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->lastmenu="syst_c_menu";
		$this->load->helper('file');
    }
	
	function index(){
      $view="info_closing_control";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Closing Period Control";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['speriode'] = $this->global_func->drop_year('sTahun','select');
	  if( $this->data['user_level'] == "SAD" ){
	  		$this->data['company'] = $this->dropdownlist("i_company","style='width:260px; height:25px;'","tabindex='1'","COMPANY_CODE","COMPANY_NAME","");
	  } 
	  $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
	  
	  if ($this->data['login_id'] == TRUE){
		  if( $this->data['user_level'] == "SAD" ){
            	show($view, $this->data);
		  }
      } else {
            redirect('login');
      }
   }
   
   function search_period(){
	   $sTahun = $this->uri->segment(3);
	   $sCompany = $this->uri->segment(4);
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->model_m_closing_control->read_Periode($searchField, $searchString, $searchOper, $sTahun, $sCompany);
	   } else {	
		   	$get = $this->model_m_closing_control->read_Periode("","","",$sTahun, $sCompany);
	   }
	   echo json_encode($get);
   }  
 
	function updateDataPeriod(){
		$periodeId = str_replace(" ","",$this->input->post( 'PERIODE_ID' ));
        $data_post['PERIODE_NAME'] = $this->input->post('PERIODE_NAME');
		$data_post['PERIODE_START'] = $this->input->post('PERIODE_START');
		$data_post['PERIODE_END'] = $this->input->post('PERIODE_END');
		//$data_post['COMPANY_CODE'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_post['ISCLOSE'] = $this->input->post('ISCLOSE');
		
		$data_post['REOPEN_BY'] = $this->input->post('REOPEN_BY');
		$data_post['REOPEN_DATE'] = $this->input->post('REOPEN_DATE');
		$data_post['CLOSE_BY'] = $this->input->post('CLOSE_BY');
		$data_post['CLOSE_DATE'] =  $this->input->post('CLOSE_DATE');
		
		if ($data_post['ISCLOSE']==1){
			$data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['CLOSE_DATE'] =  date ("Y-m-d H:i:s");
		} if ($data_post['ISCLOSE']==0){
			$data_post['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['REOPEN_DATE'] =  date ("Y-m-d H:i:s");
		}
		
		$insert_id = $this->model_m_closing_control->update_period( $periodeId, $data_post['ISCLOSE'], $data_post['CLOSE_BY'], $data_post['CLOSE_DATE'], $data_post['REOPEN_BY'], $data_post['REOPEN_DATE']);
		//echo $insert_id;
		if($insert_id > 0 ){
			$data = $this->model_m_closing_control->getPeriodeControlId($periodeId);
		
			foreach ( $data as $row){
				$periodCoId = $row['PERIODE_CONTROL_ID'];
				
				$data_postCo['ISCLOSE'] = $data_post['ISCLOSE'];
				$data_postCo['CLOSE_BY'] = $data_post['CLOSE_BY'];
				$data_postCo['CLOSE_DATE'] = $data_post['CLOSE_DATE'];
				$data_postCo['REOPEN_BY'] = $data_post['REOPEN_BY'];
				$data_postCo['REOPEN_DATE'] = $data_post['REOPEN_DATE'];
				
				$setCloseControl = $this->model_m_closing_control->update_pControlRev( $periodCoId, $periodeId, $data_postCo );
				
				if($setCloseControl > 0 ){
					$data_postCoId['ISCLOSE'] = $data_post['ISCLOSE'];
					$data_postCoId['CLOSE_BY'] = $data_post['CLOSE_BY'];
					$data_postCoId['CLOSE_DATE'] = $data_post['CLOSE_DATE'];
					$data_postCoId['REOPEN_BY'] = $data_post['REOPEN_BY'];
					$data_postCoId['REOPEN_DATE'] = $data_post['REOPEN_DATE'];
					$setCloseControlDet = $this->model_m_closing_control->update_pControlDetRev( $periodCoId, $data_postCoId );
					
				}
			}
		}
	}
	
	function dropdownlist($name, $style, $tab, $val, $desc, $onChange){ 
		
		$string = "<select  name='".$name."' ".$tab." onchange='".$onChange."' class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih perusahaan -- </option>";
		$data = $this->model_m_closing_control->getCompany();
		
		foreach ( $data as $row){
			if( (isset($default))){
				$string = $string." <option value=\"".$row[$val]."\"  selected>".$row[$desc]." </option>";
			} else {
				$string = $string." <option value=\"".$row[$val]."\">".$row[$desc]." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
	function updatePeriodControl(){
		
		$data_post['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
		$module = str_replace(" ","",$this->input->post( 'MODULE' ));
		$start = str_replace(" ","",$this->input->post( 'PERIODE_START' ));
		$end = str_replace(" ","",$this->input->post( 'PERIODE_END' )); 
		$data_post['ISCLOSE'] = $this->input->post('ISCLOSE');
		
		$data_post['MODULE'] = $module;
		$data_post['PERIODE_START'] = $start;
		$data_post['PERIODE_END'] = $end;
		$data_post['CLOSE_BY'] = str_replace(" ","",$this->input->post( 'CLOSE_BY' ));
	  	$data_post['CLOSE_DATE'] = $this->input->post( 'CLOSE_DATE' );
	  	$data_post['REOPEN_BY'] = str_replace(" ","",$this->input->post( 'REOPEN_BY' ));
	  	$data_post['REOPEN_DATE'] = $this->input->post( 'REOPEN_DATE' );
			
		if ($data_post['ISCLOSE']==1){
			$data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['CLOSE_DATE'] =  date ("Y-m-d H:i:s");
		} if ($data_post['ISCLOSE']==0){
			$data_post['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['REOPEN_DATE'] =  date ("Y-m-d H:i:s");
		}
		$diff = round(abs(strtotime($start)-strtotime($end))/86400);
		$cek = $this->model_m_closing_control->cekControlDetail($data_post['PERIODE_CONTROL_ID']);
		$insertDetail = false;
		/* if ($data_post['ISCLOSE']==1){
			if($cek < $diff){
				$delControlDetailFirst = $this->model_m_closing_control->delControlDetail($data_post['PERIODE_CONTROL_ID']);
				if($delControlDetailFirst > 0){
					for($i=strtotime($start); $i<=strtotime($end); $i = strtotime('+1 Day', $i)){
						// addControlDetail	
						$data_postDetail['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
						$data_postDetail['PERIODE_DATE'] = strtotime('+1 Day', $start);
						$data_postDetail['MODULE'] = $this->input->post('MODULE');
						$data_postDetail['ISCLOSE'] = $this->input->post('ISCLOSE');
						$data_postDetail['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
						$data_postDetail['CLOSE_DATE'] = $this->global_func->gen_datetime();
						$insertDetail = $this->model_m_closing_control->addControlDetail( $data_postDetail );
					}
				}
			}	
			$data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['CLOSE_DATE'] =  $this->global_func->gen_datetime();
		}else if ($data_post['ISCLOSE']==0){
			
			if($cek < $diff){
				$delControlDetailFirst = $this->model_m_closing_control->delControlDetail($data_post['PERIODE_CONTROL_ID']);
				if($delControlDetailFirst > 0){
					for($i=strtotime($start); $i<=strtotime($end); $i = strtotime('+1 Day', $i)){
							// addControlDetail	
							$data_postDetail['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
							$data_postDetail['PERIODE_DATE'] = strtotime('+1 Day', $start);
							$data_postDetail['MODULE'] = $this->input->post('MODULE');
							$data_postDetail['ISCLOSE'] = $this->input->post('ISCLOSE');
							$data_postDetail['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
							$data_postDetail['REOPEN_DATE'] = $this->global_func->gen_datetime();
							$insertDetail = $this->model_m_closing_control->addControlDetail( $data_postDetail );
					}
				}
			}	
			$data_post['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['REOPEN_DATE'] =  $this->global_func->gen_datetime();	
		} */
		
		$cekPerubahanTanggal = $this->model_m_closing_control->cekControlDate($data_post['PERIODE_CONTROL_ID']);
		$cekPerubahanTanggal = explode("~",$cekPerubahanTanggal);
		if( $cekPerubahanTanggal[0] !== $start || $cekPerubahanTanggal[1] !== $end){
			$delControlDetailFirst = $this->model_m_closing_control->delControlDetail($data_post['PERIODE_CONTROL_ID']);
		}
		
		
		for($i=strtotime($start); $i<=strtotime($end); $i = strtotime('+1 Day', $i)){
			// addControlDetail	
			$data_postDetail['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
			$data_postDetail['PERIODE_DATE'] = date('Y-m-d',$i);
			$data_postDetail['MODULE'] = $module;
			$data_postDetail['ISCLOSE'] = $data_post['ISCLOSE'];
			
			$data_postDetail['CLOSE_BY'] = str_replace(" ","",$this->input->post( 'CLOSE_BY' ));
	  		$data_postDetail['CLOSE_DATE'] = $this->input->post( 'CLOSE_DATE' );
	  		$data_postDetail['REOPEN_BY'] = str_replace(" ","",$this->input->post( 'REOPEN_BY' ));
	  		$data_postDetail['REOPEN_DATE'] = $this->input->post( 'REOPEN_DATE' );
			//echo $data_postDetail['ISCLOSE'];
			if ($data_postDetail['ISCLOSE']==1){
				$data_postDetail['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
				$data_postDetail['CLOSE_DATE'] = date ("Y-m-d H:i:s");
			} if ($data_postDetail['ISCLOSE']==0){
				//echo $data_postDetail['ISCLOSE'];
				$data_postDetail['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
				$data_postDetail['REOPEN_DATE'] = date ("Y-m-d H:i:s");
			}
			
			if($cek > 0){
				$insertDetail = $this->model_m_closing_control->updateControlDetail($data_post['PERIODE_CONTROL_ID'],$data_postDetail['PERIODE_DATE'],$data_postDetail['MODULE'], $data_post['ISCLOSE'], $data_postDetail['CLOSE_BY'], $data_postDetail['CLOSE_DATE'], $data_postDetail['REOPEN_BY'], $data_postDetail['REOPEN_DATE']);
				//var_dump($insertDetail);
			} else {
				$insertDetail = $this->model_m_closing_control->addControlDetail( $data_postDetail );
			}
			//echo $insertDetail;
		}
		
		if($insertDetail == true) {
			$insert_id = $this->model_m_closing_control->update_pControl( $data_post['PERIODE_CONTROL_ID'], $data_post );
		} else {
			echo "failed";
		}
	}
	/* -------------------------------------------------------------------------------------------------- */
   
   /* insert & update pcontrol detail */
  function updatePeriodControlDetail(){
	  $data_post['PERIODE_CONTROL_DETAIL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_DETAIL_ID' ));
	  $data_post['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
	  $pModule = str_replace(" ","",$this->input->post( 'MODULE' ));
	  $pDate = str_replace(" ","",$this->input->post( 'PERIODE_DATE' ));
	  $isClose = $this->input->post('ISCLOSE');
	  
	  $data_post['CLOSE_BY'] = str_replace(" ","",$this->input->post( 'CLOSE_BY' ));
	  $data_post['CLOSE_DATE'] = $this->input->post( 'CLOSE_DATE' );
	  $data_post['REOPEN_BY'] = str_replace(" ","",$this->input->post( 'REOPEN_BY' ));
	  $data_post['REOPEN_DATE'] = $this->input->post( 'REOPEN_DATE' );
	  	  
	  if ($isClose==1){
		  $data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
		  $data_post['CLOSE_DATE'] =  date ("Y-m-d H:i:s");
	  } if ($isClose==0){
		  $data_post['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
		  $data_post['REOPEN_DATE'] =  date ("Y-m-d H:i:s");
	  }
	  
	  $insert_id = $this->model_m_closing_control->updateControlDetail( $data_post['PERIODE_CONTROL_ID'], $pDate, $pModule, $isClose, $data_post['CLOSE_BY'], $data_post['CLOSE_DATE'], $data_post['REOPEN_BY'], $data_post['REOPEN_DATE']);
	  if($insert_id < 1) {
		  echo "failed";
	  }
  }
   
   function search_pControl(){
	  $period_id = $this->uri->segment(3);
	  $company_id = $this->uri->segment(4);
	  $module = $this->uri->segment(5);
	  $get = $this->model_m_closing_control->read_pControl($period_id, $company_id, $module);	  
	  echo json_encode($get);
   }
   
   function search_pControlDetail(){
	  $PeriodeControlId = $this->uri->segment(3);
	  $company_id = $this->uri->segment(4);
	  $get = $this->model_m_closing_control->read_pControlDetail($PeriodeControlId, $company_id);	  
	  echo json_encode($get);
   }
	
	function delAll(){
		$year = $this->uri->segment(3);
		echo $this->model_m_closing_control->deleteAll();
	}
/* development auto insert */
   function last_friday_of_month($year, $month) {
	  $day = 0;
	  while(True) {
		$last_day = mktime(0, 0, 0, $month+1, $day, $year); 
		if (date("w", $last_day) == 5) {
		  return date("Y-m-d", $last_day);
		}
		$day -= 1;
	  }
	}
	 
	function print_last_fridays_of_month($year) {
	  foreach(range(1, 12) as $month) {
		echo $this->last_friday_of_month($year, $month), "<br>";
	  }
	}
	
	function print_every_fridays_of_month($year) {
	  foreach(range(1, 12) as $month) {
		echo $this->last_friday_of_month($year, $month), "<br>";
	  }
	}
	
	   
    function weeks_in_month($month, $year) {
		// Start of month
		$start = mktime(0, 0, 0, $month, 1, $year);
		// End of month
		$end = mktime(0, 0, 0, $month, date('t', $start), $year);
		// Start week
		$start_week = date('W', $start);
		// End week
		$end_week = date('W', $end);
		 
		if ($end_week < $start_week) { // Month wraps
		return ((52 + $end_week) - $start_week) + 1;
		}
		 
		return ($end_week - $start_week) + 1;
    }
	
	function doit(){
		$year = $this->uri->segment(3);
		$given_year = strtotime("1 January ".$year);
		$for_start = strtotime('Friday', $given_year);
		$for_end = strtotime('+1 year', $given_year);
		$for_start2 = strtotime('Wednesday', $given_year);
		$no = 1;
		$no2 = 1;
		/* for ($i = $for_start; $i <= $for_end; $i = strtotime('+1 week', $i)) {
			echo $no . " | " . date('l Y-m-d', $i) . '<br />';
			$no++;
		} */
		$StartOfMonth = "";
		$EndOfMonth = "";
		$idPeriode = $this->model_m_closing_control->cekLastPeriodeId();
		if($idPeriode == 0){
			$idPeriode = 1;
		} else {
			$idPeriode = $idPeriode+1;
		}
		
		$modul = array('LHM','BK','BKT','NAB','PRG');
		$company = array('LIH','MAG','TPAI','MSS','SSS','SCK','NAK','IGL','BTL','ASL','SML','GKM');
		
		//$modul = array('LHM');
		//$company = array('LIH');
		/* periode */
		for ($i = $given_year; $i < $for_end; $i = strtotime('+1 Month', $i)) {
			$StartOfMonth = strtotime(date('d F Y', strtotime('FIRST DAY OF '.date('M Y', $i))));
			//$StartOfMonth = strtotime(date('d F Y', strtotime('FIRST SATURDAY OF '.date('M Y', $i))));
			$EndOfMonth = strtotime(date('d F Y', strtotime('last day of '.date('M Y', $i))));
			//echo $idPeriode . " | " . date('Ym', $i) . " | " . date('Y-m-d', strtotime('FIRST DAY OF '.date('M Y', $i))) . " | " . date('Y-m-d', strtotime('last day of '.date('M Y', $i))) . "<br/>";
			$idPeriodeControl = 1;
			/* periode control */
			for($y=0;$y<count($company); $y++){
				//echo $idPeriode . " | " . date('Ym', $i) . " | " . date('Y-m-d', strtotime('FIRST DAY OF '.date('M Y', $i))) . " | " . date('Y-m-d', strtotime('last day of '.date('M Y', $i))) . " | " . $company[$y] . " | 0 <br/>";
				
				
				$data_post['PERIODE_ID'] = $idPeriode;
				$data_post['PERIODE_NAME'] = date('Ym', $i);
				$data_post['PERIODE_START'] = date('Y-m-d', strtotime('FIRST DAY OF '.date('M Y', $i)));
				$data_post['PERIODE_END'] = date('Y-m-d', strtotime('last day of '.date('M Y', $i)));
				$data_post['COMPANY_CODE'] = $company[$y];
				$data_post['ISCLOSE'] = 0;
				
				$do = $this->model_m_closing_control->insertPeriode($data_post);
				
				
				for($x=0;$x<count($modul);$x++){
					for ($j = $StartOfMonth; $j <= $EndOfMonth; $j = strtotime('+1 week', $j)) {
						$startDate = "";
						$endDate = "";
						
						if(date('l', $j)!= 'Wednesday'){
							//echo date('l', $j) . "<br/>";
							//echo date('d', $j) . "<br/>";
							if(date('d', $j) < 2){
								
								$startDate = strtotime('FIRST DAY OF '.date('M Y', $i));
								$endDate = date('Y-m-d', strtotime('next Tuesday', $startDate) );
								if( date('m', strtotime('+6 day', $startDate) ) > date( 'm', strtotime('FIRST DAY OF '.date('M Y', $i)) )){
									$endDate = date( 'Y-m-d', strtotime('last day of '.date('M Y', $i)) ) ;
								}
							} else {
								
								$crit = strtotime('END DAY OF '.date('M Y', $i));
								
								
								$startDate = strtotime(date('Y-m-d', $j));
								//$startDate = strtotime(date('Y-m-d', strtotime('next Wednesday', $j)));
								$endDate = date('Y-m-d', strtotime('+6 day', $startDate) );
								if( date('m', strtotime('+6 day', $startDate) ) > date( 'm', strtotime('FIRST DAY OF '.date('M Y', $i)) )){
									
									$endDate = date( 'Y-m-d', strtotime('last day of '.date('M Y', $i)) ) ;
								}
							}
						} else {
							
							if(date('d', $j) < 2){
								$startDate = strtotime('FIRST DAY OF '.date('M Y', $i));
								$endDate = date('Y-m-d', strtotime('next Tuesday', $startDate) );
								if( date('m', strtotime('+6 day', $startDate) ) > date( 'm', strtotime('FIRST DAY OF '.date('M Y', $i)) )){
									
									$endDate = date( 'Y-m-d', strtotime('last day of '.date('M Y', $i)) ) ;
								}
							} else {
								$startDate = strtotime(date('Y-m-d', $j));
								$endDate = date('Y-m-d', strtotime('+6 day', $startDate) );
								if( date('m', strtotime('+6 day', $startDate) ) > date( 'm', strtotime('FIRST DAY OF '.date('M Y', $i)) )){
									
									$endDate = date( 'Y-m-d', strtotime('last day of '.date('M Y', $i)) ) ;
								}
							}
							
						}
						
						$closedDate = date('Y-m-d', strtotime('next saturday', $j));
						//echo $idPeriodeControl . ", Minggu " . $idPeriodeControl . " ".date('Ym',$startDate)."," . date('l Y-m-d',$startDate) . "," .  $endDate . "," .  $closedDate . "," . date("W",$startDate ) . "," . $modul[$x] . "," . $company[$y] . "<br />";				
						$id = $this->model_m_closing_control->cekLastControlId();
						
						if($id == ""){
							$id = 1000001;
						} else {
							$id = $id + 1;
						} 
						//$idPeriodeControl = $this->global_func->createMy_ID('m_periode_control','PERIODE_CONTROL_','','');
						
					
						/* echo "<div style='padding-left: 50px'>";
						echo $id . ",".$idPeriode.", Minggu " . date("W",$startDate ) . " ".date('Ym',$startDate)."," . date('Y-m-d',$startDate) . "," .  $endDate . "," .  $closedDate . "," . date("W",$startDate ) . "," . $modul[$x] . "," . $company[$y] . ",0,0</div><br />"; */
						
						
						
						$data_postC['PERIODE_CONTROL_ID'] = $id;
						$data_postC['PERIODE_ID'] = $idPeriode;
						$data_postC['PERIODE_NAME'] =  "Minggu " . date('W',$startDate ) . " ".date('Ym',$startDate);
						$data_postC['PERIODE_START'] = date('Y-m-d',$startDate);
						if(strtotime($endDate) > strtotime($data_post['PERIODE_END']) ){
							$data_postC['PERIODE_END'] = $data_post['PERIODE_END'];
						} else {
							$data_postC['PERIODE_END'] = $endDate;
						}
						//$data_postC['PERIODE_END'] = $endDate;
						$data_postC['PERIODE_CLOSED'] = $closedDate;
						$data_postC['WEEK_NUMBER'] = date("W",$startDate );
						$data_postC['MODULE'] =  $modul[$x];
						$data_postC['COMPANY_CODE'] =  $company[$y];
						$data_postC['ISCLOSE'] = 0;
						$doC = $this->model_m_closing_control->insertPeriodeControl($data_postC);
						
						for($s=$startDate; $s<=strtotime($endDate); $s = strtotime('+1 Day', $s)){
							/* echo "<div style='padding-left: 150px'>";
							echo 1000000+$idPeriodeControl. ",". date('Y-m-d',$s) . "," . $modul[$x] .",0";
							echo "</div>"; */
							$data_postCD['PERIODE_CONTROL_ID'] = $id;
							$data_postCD['PERIODE_DATE'] = date('Y-m-d',$s);
							$data_postCD['MODULE'] =  $modul[$x];
							$data_postCD['ISCLOSE'] = 0;
							$doCD = $this->model_m_closing_control->insertPeriodeControlDetail($data_postCD);
						}
						
						
						
						$idPeriodeControl++;
						/* periode control detail*/
						/* $idPeriodeControlDetail = 1;
						echo "minggu : " . date("W",$j ) . "<br/>";
						echo "eksekusi close ".date('l Y-m-d', strtotime('first Saturday of',$j))."<br/>";
						echo "yang di close ".date('l Y-m-d', strtotime('-3 day', strtotime('first Saturday of',$j)))."<br/>"; */
						//echo "yang di close ".date('l Y-m-d', strtotime('-3 day', strtotime('first Saturday of',$j)))."<br/>";
					}
			   }
			   $idPeriode++;
			}
			
			
			//echo " | " . . " | " . . "<br/>";
			/* $startmonth = "";
			for ($i = $for_start2; $i <= $for_end; $i = strtotime('+1 week', $i)) {
				echo $no2 . " | " . date('l Y-m-d', $i) . " | " .  date('l Y-m-d', strtotime('+6 day', $i) ) . " | " .  date('l Y-m-d', strtotime('+2 day', $i) ) . '|' . date("W",$i ) . '<br />';
				$no2++;
			} */
		}
				
	} 
	
	function generatePeriode(){
		$year = $this->uri->segment(3);
		$deleteFirst = $this->model_m_closing_control->deleteAll($year);
	}	
   
}

?>