<?php
class s_stock_cpo extends Controller{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_stock_cpo');
        $this->load->model('model_c_user_auth');  
				
		$this->load->helper('url');
        $this->load->helper('object2array');
		
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
		
		require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
        $this->lastmenu="s_stock_cpo";
        $this->data = array();    
    }
    
    function index(){
        $view="info_s_stock_cpo";
		
        $this->data['judul_header'] = "Berita Acara Produksi Harian";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
	
    function cek_tgl($tgl1){
		$tgl2=$today = date('Y-m-d');
		
		$pecah1 = explode("-", $tgl1);
		$date1 = $pecah1[2];
		$month1 = $pecah1[1];
		$year1 = $pecah1[0];
		
		$pecah2 = explode("-", $tgl2);
		$date2 = $pecah2[2];
		$month2 = $pecah2[1];
		$year2 =  $pecah2[0];
		
		$jd1 = GregorianToJD($month1, $date1, $year1);
		$jd2 = GregorianToJD($month2, $date2, $year2);		

		$selisih = $jd2 - $jd1;	
		return $selisih;
	}
	
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
		$data_prod=array();
		$data_dispatch=array();
		$data_stock=array();
		$data_storage=array();
		$bolean_sta = false;
        $data_id = $data["id"];
		
		$status = '';
		$error=false;
		if(empty($status) && $error==false){ 
			if (strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL" || strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "APPROVE"|| strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "REOPEN"){
				$bolean_sta = true;	
			}else{
				$bolean_sta = false;		
			}
			
			if($bolean_sta==false){
				$data_prod = $data["prod"]; 
				$data_dispatch = $data["dispatch"];
				$data_stock = $data["stock"];
				$data_storage = $data["storage"];
			}

			if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
				$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
				
				if($is_auth_user_command['0']['ROLE_ADD']=='1'){
					$this->add_new($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage);  
				}else{
					$return['status'] ="User tidak berwenang !!";
					$return['error']=true;
					echo json_encode($return);    
				}
				   
			}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
				$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
				if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
					$this->update_data($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage);    
				}else{
					$return['status'] ="User tidak berwenang!";
					$return['error']=true;
					echo json_encode($return);    
				}                    
			}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
				$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
				if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
					$this->delete_data($data_id);    
				}else{
					$return['status'] ="User tidak berwenang !!";
					$return['error']=true;
					echo json_encode($return);    
				}               
			}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "APPROVE"){
					$this->approve_data($data_id);                       
			}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "REOPEN"){
					$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"REOPEN",$loginid);
					if($is_auth_user_command['0']['ROLE_REOPEN']=='1'){
						$this->reopen_data($data_id);    
					}else{
						$return['status'] ="User tidak berwenang !!";
						$return['error']=true;
						echo json_encode($return);    
					}  					                      
			}else{
				$return['status'] ="Operation Unknown!";
				$return['error']=true;
				echo json_encode($return);
			}    
		}else{ //else telat
			$return['status'] =$status;
			$return['error']=$error;
			echo json_encode($return);   	
		}//end telat
    }
	function pdf_daily($dates,$id){
		require_once(APPPATH . '/libraries/html2pdf/html2pdf.class.php');	
		ob_start();
		$content='';
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company_name = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');

        $ar = preg_split('/[- :]/',trim($dates));
        $ar = implode('',$ar); //01062013
        
		$m='';
		$y='';
		$m=date("m",strtotime($ar));
		$y=date("Y",strtotime($ar));
		$first_month= $y.$m."01";
		$first_year= $y."01"."01";
		$yesterday=$dates-1;
		
		//start: Get data
		//$total_ffb = 0;
		$stock_awal = 0;
		$stock_akhir = 0;
		$actual=$this->model_s_stock_cpo->get_ffb_actual($company, $dates);		
		$actual_cpo_prod=$this->model_s_stock_cpo->get_production($id, $company, 'CPO');
		//$actual_cpo_prod_yesterday=$this->model_s_stock_cpo->get_production_yesterday(($dates-1), $company, 'CPO');				
		$actual_cpo_despatch=$this->model_s_stock_cpo->get_despatch($id, $company, 'CPO');
		$actual_cpo_recycledespatch=$this->model_s_stock_cpo->get_recycle_despatch($dates, $company, 'CPO');		
		$actual_cpo_stock=$this->model_s_stock_cpo->get_stock($id, $company, 'CPO');
				
		$actual_cpo_stock1=$this->model_s_stock_cpo->get_storage_stock($id, $company, 'CPO', 1);
		if ($actual_cpo_stock1==NULL){
			$weight_cpo1=0;
			$ffa_cpo1=0;
			$dirt_cpo1=0;
			$moisture_cpo1=0;
			$write_off1=0;
			$oil_recovery1=0;
		}else{
			$weight_cpo1=$actual_cpo_stock1->WEIGHT;
			$ffa_cpo1=$actual_cpo_stock1->FFA;
			$dirt_cpo1=$actual_cpo_stock1->DIRT;
			$moisture_cpo1=$actual_cpo_stock1->MOISTURE;
			$write_off1=$actual_cpo_stock1->WRITE_OFF;
			$oil_recovery1=$actual_cpo_stock1->OIL_RECOVERY;
		}
		/*
		$row_ffa_month=$this->model_s_stock_cpo->get_ffa_period($first_month, $dates, $company, 'CPO'); //month
		$ffa_month = 0;
		$weight_month = 0;
		
		$ffa_cpo = 0;
		$weight_cpo = 0;
		foreach($row_ffa_month as $row){
			if ($row['FLAG']=='1'){
				$ffa_month = $row['FFA']; 
				$weight_month = $row['WEIGHT'];				
			}else{
				if ($weight_month==0){
					$ffa_cpo =0;
				}else{
					$ffa_cpo = (($row['FFA']*$row['WEIGHT'])+($ffa_month*$weight_month))/($weight_month+$row['WEIGHT']);
				}
				$weight_cpo = ($weight_month+$row['WEIGHT']);
				$ffa_month = $ffa_cpo;
				$weight_month = $weight_cpo; 
			}			
		}
		*/
		$actual_cpo_stock2=$this->model_s_stock_cpo->get_storage_stock($id, $company, 'CPO', 2);	
		if ($actual_cpo_stock2==NULL){
			$weight_cpo2=0;
			$ffa_cpo2=0;
			$dirt_cpo2=0;
			$moisture_cpo2=0;
			$write_off2=0;
			$oil_recovery2=0;
		}else{
			$weight_cpo2=$actual_cpo_stock2->WEIGHT;
			$ffa_cpo2=$actual_cpo_stock2->FFA;
			$dirt_cpo2=$actual_cpo_stock2->DIRT;
			$moisture_cpo2=$actual_cpo_stock2->MOISTURE;
			$write_off2=$actual_cpo_stock2->WRITE_OFF;
			$oil_recovery2=$actual_cpo_stock2->OIL_RECOVERY;
		}

		$actual_cpo_stock3=$this->model_s_stock_cpo->get_storage_stock($id, $company, 'CPO', 3);
		if ($actual_cpo_stock3==NULL){
			$weight_cpo3=0;
			$ffa_cpo3=0;
			$dirt_cpo3=0;
			$moisture_cpo3=0;
			$write_off3=0;
			$oil_recovery3=0;
		}else{
			$weight_cpo3=$actual_cpo_stock3->WEIGHT;
			$ffa_cpo3=$actual_cpo_stock3->FFA;
			$dirt_cpo3=$actual_cpo_stock3->DIRT;
			$moisture_cpo3=$actual_cpo_stock3->MOISTURE;
			$write_off3=$actual_cpo_stock3->WRITE_OFF;
			$oil_recovery3=$actual_cpo_stock3->OIL_RECOVERY;
		}

		$cpo_prod_toyesterday =$this->model_s_stock_cpo->get_prod_period('2000-01-01', $dates-1, $company, 'CPO%');
		$cpo_dispatch_toyesterday =$this->model_s_stock_cpo->get_dispatch_period('2000-01-01', $dates-1, $company, 'CPO');
		$stock_awal = $cpo_prod_toyesterday - $cpo_dispatch_toyesterday;

		$sum_weight_cpo=$weight_cpo1+$weight_cpo2+$weight_cpo3;
		if($sum_weight_cpo==0){
			$ffa_stock_cpo=0;
		}else{
			$ffa_stock_cpo=(($weight_cpo1*$ffa_cpo1)+($weight_cpo2*$ffa_cpo2)+($weight_cpo3*$ffa_cpo3))/($sum_weight_cpo);	
		}
		$actual_sounding_cpo1=$this->model_s_stock_cpo->get_sounding_cpo($dates, $company, 'CPO', 1);
		if ($actual_sounding_cpo1==NULL){
			$sounding_cpo1=0;
			$sounding_temp_cpo1=0;
		}else{			
			$sounding_cpo1=$actual_sounding_cpo1->HEIGHT;
			$sounding_temp_cpo1=$actual_sounding_cpo1->TEMPERATURE;
		}
		$actual_sounding_cpo2=$this->model_s_stock_cpo->get_sounding_cpo($dates, $company, 'CPO', 2);
		if ($actual_sounding_cpo2==NULL){
			$sounding_cpo2=0;
			$sounding_temp_cpo2=0;
		}else{			
			$sounding_cpo2=$actual_sounding_cpo2->HEIGHT;
			$sounding_temp_cpo2=$actual_sounding_cpo2->TEMPERATURE;
		}
		
		$actual_sounding_cpo3=$this->model_s_stock_cpo->get_sounding_cpo($dates, $company, 'CPO', 3);
		if ($actual_sounding_cpo3==NULL){
			$sounding_cpo3=0;
			$sounding_temp_cpo3=0;
		}else{			
			$sounding_cpo3=$actual_sounding_cpo3->HEIGHT;
			$sounding_temp_cpo3=$actual_sounding_cpo3->TEMPERATURE;
		}
				
		$month_cpo_dispatch =$this->model_s_stock_cpo->get_dispatch_period($first_month, $dates, $company, 'CPO');
		$year_cpo_dispatch =$this->model_s_stock_cpo->get_dispatch_period($first_year, $dates, $company, 'CPO');
				
		$qc=$actual->QC;
		$mill_manager=$actual->MILL_MANAGER;
		$ktu=$actual->KTU;
		$administratur=$actual->ADMINISTRATUR;
		$labor=$actual->LABOR;
		
		$dispatch_doc = $this->model_s_stock_cpo->get_dispatch_doc($dates,$company);
		//end: Get data
		
		$content = "
		<style> .tbl_header { font-size: 12px; border-top:1px solid;border-left:1px solid} 
.tbl_th { font-size: 12px; border-bottom:1px solid; border-right:1px solid} 
.tbl_th8 { font-size: 9px; border-bottom:1px solid; border-right:1px solid} 
.tbl_th7 { font-size: 12px; border-bottom:1px solid; border-right:1px solid; padding: 0 15px} 
.tbl_th2 { font-size: 12px; font-weight:bold; border-bottom:1px solid; border-right:1px solid; background-color:#CCC} 
.tbl_th3 { font-size: 12px; font-weight:bold; border-bottom:1px solid} 
.tbl_th4 { font-size: 12px; font-weight:bold; border-right:1px solid} 
.tbl_th5 { font-size: 12px; border-right:1px solid} 
.tbl_th6 { font-size: 10px; border-right:1px solid} 
.tbl_header2 { font-size: 18px; border-bottom:2px solid; border-right:1px solid} 
.tbl_header3 { font-size: 10px; border-bottom:2px solid} 
.tbl_header4 { font-size: 12px; border-bottom:2px solid; border-right:1px solid} 
.tbl_td { font-size: 12px; border-bottom:1px solid; border-right:1px solid} 
.tbl_td3 {font-size: 12px; text-decoration:underline} 
.tbl_td2 {border-right:1px solid} 
.tbl_2 { font-size: 12px;color:#678197;} 
.content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>
<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>
<tr>    
    <td colspan='2' align='center' class='tbl_header4' height='20'>PT. ".$company_name." </td>
    <td colspan='4' align='center' class='tbl_header2' height='20'>DAILY PRODUCTION REPORT</td>
    <td colspan='2' align='center' class='tbl_header4' height='20'>TANGGAL: " .date("d-m-Y",strtotime($dates))." </td>
  </tr>
  <tr>
    <td class='tbl_th2'>&nbsp;</td>
    <td class='tbl_th2' colspan='2' align='center'>For Day</td>
    <td class='tbl_th2' colspan='3' align='center'>Month Todate</td>
    <td class='tbl_th2' colspan='2' align='center'>Year Todate</td>
  </tr>
  <tr>
    <td class='tbl_th2' align='center' width='80'></td>
    <td class='tbl_th2' align='center' width='80'>Actual Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Actual Received (Kg)</td>
    <td class='tbl_th2' align='center' width='85'>To date Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Month Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Actual YTD Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Budget YTD Received (Kg)</td>
  </tr>";
  $content .= "<tr><td class='tbl_th2'> &nbsp;"." RECEIPT"."</td><td class='tbl_th2' align='center'>FFA</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th2' colspan='4'></td></tr>";
  $total_receipt = 0;
  foreach ($actual_cpo_prod as $row){
	$total_receipt = $total_receipt + $row['WEIGHT']; 
	$month_cpo_prod =$this->model_s_stock_cpo->get_prod_period($first_month, $dates, $company, $row['KODE_JENIS']);
	$year_cpo_prod =$this->model_s_stock_cpo->get_prod_period($first_year, $dates, $company, $row['KODE_JENIS']);
		
  	$content .= "<tr><td align='left' class='tbl_th'> &nbsp; ".$row['KODE_JENIS']."</td><td align='right' class='tbl_th'>".number_format($row['WEIGHT'],2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  }
  $content .= "<tr><td align='right' class='tbl_th'> "." TOTAL RECEIPT"." &nbsp;</td><td align='right' class='tbl_th'>".number_format($total_receipt,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  
  	$content .= "<tr><td colspan='5' class='tbl_th3'>&nbsp;</td><td colspan='3' class='tbl_th4'>&nbsp;</td></tr>";
	$content .= "<tr><td class='tbl_th2' colspan='2'> &nbsp;"." RECEIPT QUALITY"."</td><td class='tbl_th2' align='center'>FFA/BROKEN</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th4' colspan='3' rowspan='3'></td></tr>";
  foreach ($actual_cpo_prod as $row){
  	$content .= "<tr><td class='tbl_th' colspan='2'> &nbsp; ".$row['KODE_JENIS']."</td><td class='tbl_th' align='right'>".number_format($row['FFA'],2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($row['DIRT'],2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($row['MOISTURE'],2)."&nbsp;</td></tr>";
  }
  $content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2'> &nbsp;"." DESPATCH"."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th2' align='center'>MONTH TODATE</td><td class='tbl_th2' align='center'>YEAR TODATE</td><td class='tbl_th4' rowspan='6'></td></tr>";	
  $content .= "<tr><td class='tbl_th'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->FFA,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->DIRT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->MOISTURE,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($month_cpo_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_cpo_dispatch,2)."&nbsp;</td></tr>";
  if ($actual_cpo_recycledespatch != NULL){
	  $content .= "<tr><td class='tbl_th2'> &nbsp;"." DESPATCH (RECYCLE) "."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th4' colspan='3' rowspan='3'></td></tr>";
  	  $content .= "<tr><td class='tbl_th'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->BERAT_BERSIH,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->BROKEN,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->DIRTY,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->MOIST,2)."&nbsp;</td></tr>";
  }
  //asep
  $content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2' colspan='7'> &nbsp;"." STOCK CPO"."</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." BALANCE YESTERDAY"."</td><td class='tbl_th' align='right'>".number_format($stock_awal,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." BALANCE TODAY"."</td><td class='tbl_th' align='right'>".number_format($stock_awal+$total_receipt-$actual_cpo_despatch->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
  
  $content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2'> &nbsp;"." STOCK SOUNDING"."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th2' align='center'>SOUNDING (mm)</td><td class='tbl_th2' align='center'>TEMPERATURE</td><td class='tbl_th4' rowspan='10'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($weight_cpo1+$weight_cpo2+$weight_cpo3,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_stock_cpo,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." STORAGE TANK 1"."</td><td class='tbl_th' align='right'>".number_format($weight_cpo1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_cpo1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($dirt_cpo1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($moisture_cpo1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($sounding_cpo1*1000)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($sounding_temp_cpo1)."&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." STORAGE TANK 2"."</td><td class='tbl_th' align='right'>".number_format($weight_cpo2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_cpo2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($dirt_cpo2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($moisture_cpo2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format(
$sounding_cpo2*1000)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($sounding_temp_cpo2)."&nbsp;</td></tr>";
  //asep
  $content .= "<tr><td class='tbl_th'> &nbsp;"." STORAGE TANK 3"."</td><td class='tbl_th' align='right'>".number_format($weight_cpo3,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_cpo3,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($dirt_cpo3,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($moisture_cpo3,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format(
$sounding_cpo3*1000)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($sounding_temp_cpo3)."&nbsp;</td></tr>";
  //asep
  $content .= "<tr><td class='tbl_th7'> &nbsp;"." WRITE OFF/ SLUDGE"."</td><td class='tbl_th' align='right'>".number_format($write_off2+$write_off1,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th7'> &nbsp;"." OIL RECOVERY"."</td><td class='tbl_th' align='right'>".number_format($oil_recovery2+$oil_recovery1,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
	$content .= "<tr><td colspan='6' class='tbl_th3'>&nbsp;</td><td class='tbl_th4' colspan='2' >&nbsp;</td></tr>";
	$content .= "<tr><td class='tbl_th2' align='center' colspan='2'>NO. DOC</td><td class='tbl_th2' align='center'>CONTRACT</td><td class='tbl_th2' align='center'>PARTY</td><td class='tbl_th2' align='center'>DISPATCH</td><td class='tbl_th2' align='center'>BALANCE</td><td class='tbl_th4' align='left' colspan='2'></td></tr>";
	foreach($dispatch_doc as $row){	
		$content.="<tr>";
        $content.="<td class='tbl_td' align='left' colspan='2'>"."&nbsp; ". $row['ID_DO']."</td>";		 
        $content.="<td class='tbl_td' align='right'>".$row['JENIS']."&nbsp;</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['QTY_CONTRACT'],2)."&nbsp;</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['QTY_DELIVERED_RUN'],2)."&nbsp;</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['BALANCE'],2)."&nbsp;</td>";
		$content.="<td class='tbl_td2' colspan='2'></td>";
        $content.='</tr>';
    }
	if ($dispatch_doc==null){
		$content.="<tr>";
        $content.="<td class='tbl_td' align='center' colspan='2'>-</td>";		 
        $content.="<td class='tbl_td' align='center'>-</td>";
        $content.="<td class='tbl_td' align='center'>-</td>";
        $content.="<td class='tbl_td' align='center'>-</td>";
        $content.="<td class='tbl_td' align='center'>-</td>";
		$content.="<td class='tbl_td2' colspan='2'></td>";
        $content.='</tr>';	
	}

	$content .= "<tr><td class='tbl_th6' colspan='8'>&nbsp;"." NOTE : ".$actual->DESCRIPTION."</td></tr>";
	$content .= "<tr><td colspan='8' class='tbl_th6'>&nbsp;</td></tr>";
			

	$content.="<tr align='center'><td colspan='8' class='tbl_th5' ><table>
  <tr>
    <td colspan='2'>Prepared By,</td>
    <td colspan='2'>Checked By,</td>
    <td>Approved By,</td>
  </tr>
  <tr>
    <td height='35'>&nbsp;</td>
    <td height='35'>&nbsp;</td>
    <td height='35'>&nbsp;</td>
    <td height='35'>&nbsp;</td>
    <td height='35'>&nbsp;</td>
  </tr>
  <tr>
    <td class='tbl_td3'>".$qc."</td>
    <td class='tbl_td3'>".$labor."</td>
    <td class='tbl_td3'>".$mill_manager."</td>
    <td class='tbl_td3'>".$ktu."</td>
    <td class='tbl_td3'>".$administratur."</td>
  </tr>
  <tr>
    <td width='147'>Quality Control</td>
    <td width='147'>Ast. Labor</td>
    <td width='147'>Mill Manager</td>
    <td width='147'>KTU</td>
    <td width='147'>Administratur</td>
  </tr>
</table></td></tr>";

	$content .= "<tr><td colspan='8' class='tbl_th'>&nbsp;</td></tr>";
	$content.="</table>";
		try{
			$html2pdf = new HTML2PDF('P', 'Folio', 'en', true, 'UTF-8', array(4, 4, 4, 4));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content);
			$html2pdf->Output("BA_STOCK_CPO_HARIAN_".$company."_".$dates.".pdf");
		}catch(HTML2PDF_exception $e) {
			echo 'header("Content-type: application/pdf");'.$e;
			exit;
		}
	}
	function xls_month(){
		$company_code = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');	
		$f_day = $periode."01";
				
		$judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
		$headers .= "PT. ".$company."\n";
		$headers .= "DAILY PRODUCTION REPORT \n";
		$headers .= "PERIODE: ". $periode ."\n";
		$headers .= "\n";
		$headers .= "NO. \t";
		$headers .= "DATE \t";
		$headers .= "CPO GKM (For Day)\t";
		$headers .= "CPO GKM (Todate)\t";	
		$headers .= "FFA CPO GKM \t";	
		$headers .= "CPO SMI (For Day)\t";
		$headers .= "CPO SMI (Todate)\t";		
		$headers .= "FFA CPO SMI \t";
		$headers .= "CPO SUPPLIER (For Day)\t";
		$headers .= "CPO SUPPLIER (Todate)\t";
		$headers .= "FFA CPO SUPPLIER \t";
		$headers .= "TOTAL CPO (For Day)\t";
		$headers .= "TOTAL CPO (Todate)\t";
		$headers .= "DISPATCH CPO (For Day)\t";
		$headers .= "DISPATCH CPO (Todate)\t";
		$headers .= "DISPATCH RETURN \t";
		$headers .= "STOCK CPO YESTERDAY \t";	
		$headers .= "STOCK CPO TODAY \t";		
		$headers .= "SOUNDING CPO 1\t";
		$headers .= "SOUNDING CPO 2\t";
		$headers .= "SOUNDING CPO 3\t";
		$headers .= "TOTAL SOUNDING \t";
		$headers .= "FFA STOCK CPO1\t";
		$headers .= "FFA STOCK CPO2\t";
		$headers .= "FFA STOCK CPO3\t";
		$headers .= "FFA CPO STOCK\t";
		$headers .= "OIL_RECOVERY \t";
		$headers .= "WRITE_OFF \t";
		$headers .= "COMPANY CODE\t";
		$headers .= "INPUT DATE\t";
		$headers .= "APPROVE DATE\t";

		$no = 1;
		$oil_er_shi=0;
		$kernel_er_shi=0;
		$shell_er_shi=0;
		$empty_bunch_er_shi=0;
		$abu_er_shi=0;
		$ffa = 0;
		$ffa_shi = 0;
		$count_ffa = 0;		
		$oil_er=0;	
		$kernel_er=0;
		$shell_er=0;
		$empty_bunch_er=0;
		$abu_er=0;
		$throughput_shi=0;
		$data=$this->model_s_stock_cpo->get_ba_xls($company_code, $periode, $f_day);
		if($data!=NULL){
			foreach ($data as $row){
				
				$line = '';
				$line .= str_replace('"', '""',$no)."\t"; 
				$line .= str_replace('"', '""',$row['BA_DATE'])."\t";
				
				$line .= str_replace('"', '""',$row['CPO_GKM'])."\t";
				$line .= str_replace('"', '""',$row['CPO_GKM_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFA_GKM'])."\t";
				$line .= str_replace('"', '""',$row['CPO_SMI'])."\t";
				$line .= str_replace('"', '""',$row['CPO_SMI_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFA_SMI'])."\t";
				$line .= str_replace('"', '""',$row['CPO_SUPPLIER'])."\t";
				$line .= str_replace('"', '""',$row['CPO_SUPPLIER_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFA_SUPPLIER'])."\t";
				$line .= str_replace('"', '""',$row['TOTAL_RECEIPT'])."\t";
				$line .= str_replace('"', '""',$row['TOTAL_RECEIPT_SHI'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_CPO'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_CPO_SHI'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_RETURN'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO_YESTERDAY'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO_TODAY'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO1'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO2'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO3'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO'])."\t";
				
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO1'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO2'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO3'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK'])."\t";				
				$line .= str_replace('"', '""',$row['OIL_RECOVERY'])."\t";
				$line .= str_replace('"', '""',$row['WRITE_OFF'])."\t";

				$line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['INPUT_DATE'])."\t";
				$line .= str_replace('"', '""',$row['APPROVED_DATE'])."\t";
				$no++;
				$data .= trim($line)."\n"; 			
			}
			$data = str_replace("\r","",$data);
			$data = str_replace("Array","",$data);

        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=Rekap_BA_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
		}
	}
	
	function get_approval(){
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_s_stock_cpo->get_approval($company);
		foreach($data_enroll as $row){
			$data = '~'.$row['MILL'].'~'.$row['KTU'].'~'.$row['ADM'].'~'.$row['ID_APPROVAL'].'~'.$row['QC'].'~'.$row['LABOR'].'~'.$row['COMPANY_CODE'].'~';
        }
		$storeData = json_encode($data);
        echo $storeData;
	}
		
    function LoadData(){
        $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_s_stock_cpo->LoadData($periode,$company));   
    }
    	
	function LoadData_Commodity(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadData_Commodity($company));   
    }
	
	function LoadData_Commodities(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadData_Commodities($company));   
    }
	
	
	function LoadData_Storage(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadData_Storage($company));   
    }
	
	function LoadDetail_Production($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadDetail_Production($company, $id_ba));   
    }
	
	function LoadProductionByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadProductionByDate($company, $date));   
    }
	/*
	function LoadNoProductionByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadNoProductionByDate($company, $date));   
    }
	*/
	function LoadDispatchByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadDispatchByDate($company, $date));   
    }
	
	function LoadStorageByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadStorageByDate($company, $date));   
    }
	
	function LoadOtherStockByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadOtherStockByDate($company, $date));   
    }
	
	
	function LoadDetail_Dispatch($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadDetail_Dispatch($company, $id_ba));   
    }

	function LoadDetail_Stock($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadDetail_Stock($company, $id_ba));   
    }

	function LoadDetail_StorageStock($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_stock_cpo->LoadDetail_StorageStock($company, $id_ba));   
    }
	
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_stock_cpo->data_search($data['rules'], $company));    
    } 
	
    function add_new($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage){
		//Start for Master BA
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        
        $data_post['ID_BA'] = trim($this->global_func->createMy_ID('s_ba','ID_BA',$company."BA","BA_DATE",$company));
        $data_post['BA_DATE'] = strtoupper(trim(htmlentities($data_id['BA_DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['QC']=strtoupper(trim(htmlentities($data_id['QC'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['MILL_MANAGER']=strtoupper(trim(htmlentities($data_id['MILL_MANAGER'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['KTU']=strtoupper(trim(htmlentities($data_id['KTU'],ENT_QUOTES,'UTF-8')));
		$data_post['ADMINISTRATUR']=strtoupper(trim(htmlentities($data_id['ADMINISTRATUR'],ENT_QUOTES,'UTF-8')));
		$data_post['LABOR']=strtoupper(trim(htmlentities($data_id['LABOR'],ENT_QUOTES,'UTF-8')));
		$data_post['DESCRIPTION']=strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8')));				
		$data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;
		
		$field = $data_post['BA_DATE'];		
		$validate_approve=$this->validate_approve($field);
        if( strtolower($validate_approve)=='false'){
            $return['status'] ="BA tanggal ".$field. " tidak dapat disimpan karena BA tanggal sebelumnya belum di APPROVE atau di INPUT";
            $return['error']=true;        
        }
		
		//end for Master BA
		//start: production
		$data_post_d = array();		
		$int=0;
		$tmp_identifier=0;
		foreach($data_prod as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;		
			if (preg_match('/PRODUCTION_DATE/',$key)){
				$a = array('PRODUCTION_DATE'=>$val);
				$data_post_d[$tmp_identifier]=$a;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="PRODUCTION commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom production pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('WEIGHT'=>$val));	
				}
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_BA'=>$data_post['ID_BA']));			
		}
		//end: production
				
		//start: dispatch
		$data_post_dispatch = array();
		$int=0;
		$tmp_identifier=0;
		foreach($data_dispatch as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/DISPATCH_DATE/',$key)){
				$b = array('DISPATCH_DATE'=>$val);
				$data_post_dispatch[$tmp_identifier]=$b;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="DISPATCH commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DISPATCH (Kg) pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('WEIGHT'=>$val));
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA - dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE-dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY - dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('ID_BA'=>$data_post['ID_BA']));				
		}	
		//end: dispatch
		//start: stock
		$data_post_stock = array();
		$int=0;
		$tmp_identifier=0;
		foreach($data_stock as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/STOCK_DATE/',$key)){
				$c = array('STOCK_DATE'=>$val);
				$data_post_stock[$tmp_identifier]=$c;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="STOCK commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom STOCK (kg) - OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('WEIGHT'=>$val));	
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA - OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE-OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY - OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('ID_BA'=>$data_post['ID_BA']));				
		}	
		//end: stock
		//start: storage
		$data_post_storage = array();
		$int=0;
		$tmp_identifier=0;
		foreach($data_storage as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/STRG_STOCK_DATE/',$key)){
				$c = array('STRG_STOCK_DATE'=>$val);
				$data_post_storage[$tmp_identifier]=$c;
			}elseif (preg_match('/ID_STORAGE/',$key)){
				if($val==''){
                    $return['status']="Storage code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('ID_STORAGE'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom STOCK (kg) - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('WEIGHT'=>$val));	
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('ID_BA'=>$data_post['ID_BA']));				
		}
		//end: storage		
		if(empty($return['status']) && $return['error']==false){     
            $insert_id = $this->model_s_stock_cpo->add_new($company,$data_post);
			
			if($insert_id['error'] == false){ 
            	$insert_detail = $this->model_s_stock_cpo->add_new_production($data_post['ID_BA'], $company, $data_post_d);
				$insert_detail = $this->model_s_stock_cpo->add_new_dispatch($data_post['ID_BA'], $company, $data_post_dispatch);
				$insert_detail = $this->model_s_stock_cpo->add_new_stock($data_post['ID_BA'], $company, $data_post_stock);
				$insert_detail = $this->model_s_stock_cpo->add_new_storage_stock($data_post['ID_BA'], $company, $data_post_storage);				
				
				$insert_detail = $this->model_s_stock_cpo->update_approval($data_post['QC'], $data_post['MILL_MANAGER'], $data_post['KTU'], $data_post['ADMINISTRATUR'], $data_post['LABOR'], $company);
				
				$message = $insert_detail;
			}else{
				$message = $insert_id;	
			}		

            echo json_encode($message);          
        }else{
            echo json_encode($return);
        }
		
    }
				
    function update_data($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage){
        //Start for Master BA
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        
		$id_ba = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8')));
        $data_post['BA_DATE'] = strtoupper(trim(htmlentities($data_id['BA_DATE'],ENT_QUOTES,'UTF-8')));
        $data_post['QC']=strtoupper(trim(htmlentities($data_id['QC'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['MILL_MANAGER']=strtoupper(trim(htmlentities($data_id['MILL_MANAGER'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['KTU']=strtoupper(trim(htmlentities($data_id['KTU'],ENT_QUOTES,'UTF-8')));
		$data_post['ADMINISTRATUR']=strtoupper(trim(htmlentities($data_id['ADMINISTRATUR'],ENT_QUOTES,'UTF-8')));
		$data_post['LABOR']=strtoupper(trim(htmlentities($data_id['LABOR'],ENT_QUOTES,'UTF-8')));
		$data_post['DESCRIPTION']=strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8')));
				
		$data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
		$data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
        $data_post['COMPANY_CODE'] = $company;
		
		$ba_date=strval($data_post['BA_DATE']);
        if(empty($ba_date) || $ba_date==null || $ba_date==false){
            $return['status']="Tanggal berita acara tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($ba_date)) == '19700101'){
                $return['status']="format tanggal salah";
                $return['error']=true;
            }
        }
				
		//end for Master BA
		//start: production
		$data_post_d = array();		
		$int=0;
		$tmp_identifier=0;
		foreach($data_prod as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;		
			if (preg_match('/PRODUCTION_DATE/',$key)){
				$a = array('PRODUCTION_DATE'=>$val);
				$data_post_d[$tmp_identifier]=$a;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="PRODUCTION commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom production pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('WEIGHT'=>$val));	
				}
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY PRODUCTION pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('ID_BA'=>$id_ba));
			$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))
																																						 ));
			$data_post_d[$tmp_identifier]=array_merge((array)$data_post_d[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()
																														   ));
		}
		//end: production

		//start: dispatch
		$data_post_dispatch = array();
		$int=0;
		$tmp_identifier=0;
		foreach($data_dispatch as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/DISPATCH_DATE/',$key)){
				$b = array('DISPATCH_DATE'=>$val);
				$data_post_dispatch[$tmp_identifier]=$b;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="DISPATCH commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom dispatch (kg) pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('WEIGHT'=>$val));
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA-dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE-dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY-dispatch pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('ID_BA'=>$id_ba));	
			$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))
																																						 ));
			$data_post_dispatch[$tmp_identifier]=array_merge((array)$data_post_dispatch[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()
																														   ));
		}	
		//end: dispatch
		//start: stock
		$data_post_stock = array();
		$int=0;
		$tmp_identifier=0;
		foreach($data_stock as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/STOCK_DATE/',$key)){
				$c = array('STOCK_DATE'=>$val);
				$data_post_stock[$tmp_identifier]=$c;
			}elseif (preg_match('/ID_COMMODITY/',$key)){
				if($val==''){
                    $return['status']="STOCK commodity code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('ID_COMMODITY'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom STOCK (kg)- OTHER STOCK  pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('WEIGHT'=>$val));	
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA-OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE-OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY-OTHER STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('ID_BA'=>$id_ba));
			$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))
																																						 ));
			$data_post_stock[$tmp_identifier]=array_merge((array)$data_post_stock[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()
																														   ));
		}	
		//end: stock
		//start: storage
		$data_post_storage = array();
		$int=0;
		$tmp_identifier=0;

		foreach($data_storage as $key => $val){			
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);			
            $tmp_identifier=$int;	
			if (preg_match('/STRG_STOCK_DATE/',$key)){
				$c = array('STRG_STOCK_DATE'=>$val);
				$data_post_storage[$tmp_identifier]=$c;
			}elseif (preg_match('/ID_STORAGE/',$key)){
				if($val==''){
                    $return['status']="Storage code pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['error']=true;   
                }else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('ID_STORAGE'=>$val));
				}
				
			}elseif (preg_match('/COMPANY_CODE/',$key)){
				$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('COMPANY_CODE'=>$val));
			}elseif (preg_match('/WEIGHT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom STOCK (kg) - STORAGE STOCK  pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('WEIGHT'=>$val));	
				}				
			}elseif (preg_match('/FFA/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom FFA - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('FFA'=>$val));	
				}				
			}elseif (preg_match('/MOISTURE/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom MOISTURE - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('MOISTURE'=>$val));	
				}					
			}elseif (preg_match('/DIRT/',$key)){
				$field = $val;
				$validate_numeric=$this->validate_numeric($field);
        		if( strtolower($validate_numeric)=='false'){
            		$return['status'] ="Nilai kolom DIRTY - STORAGE STOCK pada baris ".$int." harus angka \r\n ";
            		$return['error']=true;        
				}else{
					$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('DIRT'=>$val));	
				}				
			}
			$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('ID_BA'=>$id_ba));		
			$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))
																																						 ));
			$data_post_storage[$tmp_identifier]=array_merge((array)$data_post_storage[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()
																														   ));
		}
		//end: storage
				
		if(empty($return['status']) && $return['error']==false){  
			$update_id = $this->model_s_stock_cpo->update_data($id_ba, $company, $data_post);			
			if($update_id['error'] == false){ 
            	$insert_detail = $this->model_s_stock_cpo->update_production($id_ba, $company, $data_post_d);
				$insert_detail = $this->model_s_stock_cpo->update_dispatch($id_ba, $company, $data_post_dispatch);
				$insert_detail = $this->model_s_stock_cpo->update_stock($id_ba, $company, $data_post_stock);
				$insert_detail = $this->model_s_stock_cpo->update_storage_stock($id_ba, $company, $data_post_storage);				
				$insert_detail = $this->model_s_stock_cpo->update_approval($data_post['QC'], $data_post['MILL_MANAGER'], $data_post['KTU'], $data_post['ADMINISTRATUR'], $data_post['LABOR'], $company);
				
				$message = $insert_detail;
			}else{
				$message = $insert_id;	
			}		
            echo json_encode($message);          
        }else{
            echo json_encode($return);
        }
    }
	
    function delete_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_ba = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_ba) || trim($id_ba)=='' || $id_ba==false){
            $return['status']="ID BA KOSONG";
            $return['error']=true;   
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_stock_cpo->delete_ba($id_ba,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
    }
	
	function approve_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_ba = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ; 
		$ba_date = strtoupper(trim(htmlentities($data_id['BA_DATE'],ENT_QUOTES,'UTF-8'))) ;
        if (empty($id_ba) || trim($id_ba)=='' || $id_ba==false){
            $return['status']="ID BA KOSONG";
            $return['error']=true;   
        }
        if (empty($ba_date) || trim($ba_date)=='' || $ba_date==false){
            $return['status']="BA DATE KOSONG";
            $return['error']=true;   
        }
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_stock_cpo->approve_ba($id_ba,$company,$ba_date);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
    }
	function reopen_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_ba = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ; 
		$ba_date = strtoupper(trim(htmlentities($data_id['BA_DATE'],ENT_QUOTES,'UTF-8'))) ;

		$date =  date('Y-m-d', strtotime($ba_date));		
		$next_date = strtotime('+1 day',strtotime($date));
		$next_date = date('Y-m-d', $next_date); 

		$validate_approve=$this->validate_reopen($ba_date);
        if( strtolower($validate_approve)=='true'){
            $return['status'] ="BA tanggal ".$date. " tidak dapat direopen. BA tanggal ".$next_date. " harus direopen terlebuh dahulu";
            $return['error']=true;        
        }
		
        if (empty($id_ba) || trim($id_ba)=='' || $id_ba==false){
            $return['status']="ID BA KOSONG";
            $return['error']=true;   
        }
        if (empty($ba_date) || trim($ba_date)=='' || $ba_date==false){
            $return['status']="BA DATE KOSONG";
            $return['error']=true;   
        }
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_input_ba->reopen_ba($id_ba,$company,$ba_date);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
    }
	
	function validate_approve($dates){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$status_ba=$this->model_s_stock_cpo->get_ba($company, $dates);
		return $status_ba;
	}
	
	function validate_reopen($dates){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$status_ba=$this->model_s_input_ba->get_ba_next($company, $dates);
		return $status_ba;
    }
	
    function validate_numeric($data){
        $numeric=$data;
        $result='';
        if(is_array($data)){
            while(list($key,$val)=each($data)){
                if(trim($val)=="" || $val==null){
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val))){
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        }else {
            if(trim($numeric)=="" || $numeric == null){
                $numeric=0;
            }
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric)){
                $result='false';   
            }else{
                $result='true';
            }    
        }

        return $result;   
    }
		
}
?>
