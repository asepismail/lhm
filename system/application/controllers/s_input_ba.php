<?php
class s_input_ba extends Controller{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_input_ba');
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
        $this->lastmenu="s_input_ba";
        $this->data = array();    
    }
    
    function index(){
        $view="info_s_input_ba";
		
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
			if ($this->data['company_code']=='NRP'){
				redirect('s_stock_cpo');
			}else{
            	show($view, $this->data);
			}
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
		$data_performance=array();
		$data_dispatch=array();
		$data_stock=array();
		$data_storage=array();
		$bolean_sta = false;
        $data_id = $data["id"];
		
		//asep
		
		$selisih = 0;
		$status = '';
		$error=false;
		/*
		$selisih=$this->cek_tgl(strtoupper(trim(htmlentities($data_id['BA_DATE'],ENT_QUOTES,'UTF-8'))));
		if($selisih >=3){
            $status  ="Maaf data BA tidak dapat disimpan, dikarenakan data terlambat diinput ".$selisih." Hari. Batas keterlambatan penginputan data adalah 2 (dua) hari. Silakan hubungi Administrator";
            $error=true;
        }
*/

		if(empty($status) && $error==false){ 
			if (strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL" || strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "APPROVE"|| strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "REOPEN"){
				$bolean_sta = true;	
			}else{
				$bolean_sta = false;		
			}
			
			if($bolean_sta==false){
				$data_prod = $data["prod"]; 
				$data_performance = $data["performance"];
				$data_dispatch = $data["dispatch"];
				$data_stock = $data["stock"];
				$data_storage = $data["storage"];
			}
			
			if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
				$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
				
				if($is_auth_user_command['0']['ROLE_ADD']=='1'){
					$this->add_new($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage, $data_performance);  
				}else{
					$return['status'] ="User tidak berwenang !!";
					$return['error']=true;
					echo json_encode($return);    
				}
				   
			}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
				$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
				if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
					$this->update_data($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage, $data_performance);    
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
		$total_ffb = 0;
		$actual=$this->model_s_input_ba->get_ffb_actual($company, $dates);
		//$actual_yesterday=$this->model_s_input_ba->get_ffb_actual($company, ($dates-1));
		$actual_first=$this->model_s_input_ba->get_ffb_min($company, $dates);
        $total_ffb =$actual->FFB_INTI+$actual->FFB_PLASMA+$actual->FFB_SUPPLIER+$actual->FFB_GROUP;
		
		$actual_cpo_prod=$this->model_s_input_ba->get_production($id, $company, 'CPO');
		$actual_cpo_prod_yesterday=$this->model_s_input_ba->get_production_yesterday(($dates-1), $company, 'CPO');
		$actual_kernel_prod=$this->model_s_input_ba->get_production($id, $company, 'KRN');
		$actual_shell_prod=$this->model_s_input_ba->get_production($id, $company, 'CKG');
		$actual_empty_bunch_prod=$this->model_s_input_ba->get_production($id, $company, 'TNK');
		$actual_abu_prod=$this->model_s_input_ba->get_production($id, $company, 'ABJ');
		$abu_prod_weight=0;
		if ($actual_abu_prod<>NULL){
			$abu_prod_weight=$actual_abu_prod->WEIGHT;
		}
		//$actual_solid_prod=$this->model_s_input_ba->get_production($id, $company, 'SLD');
		$empty_bunch_weight=0;
		if($actual->FFB_PROCESSED==0){
			$oil_er=0;
			$kernel_er=0;
			$shell_er=0;
			$empty_bunch_er=0;
			$abu_er=0;
			//$solid_er=0;
		}else{
			if ($actual_cpo_prod==NULL){
				$oil_er=0;
			}else{
				$oil_er=$actual_cpo_prod->WEIGHT/$actual->FFB_PROCESSED;
			}
			if ($actual_kernel_prod==NULL){
				$kernel_er=0;	
			}else{
				$kernel_er=$actual_kernel_prod->WEIGHT/$actual->FFB_PROCESSED;
			}
			if ($actual_shell_prod==NULL){
				$shell_er=0;
			}else{
				$shell_er=$actual_shell_prod->WEIGHT/$actual->FFB_PROCESSED;
			}
			if ($actual_empty_bunch_prod==NULL){
				$empty_bunch_er=0;				
			}else{
				$empty_bunch_er=$actual_empty_bunch_prod->WEIGHT/$actual->FFB_PROCESSED;
				$empty_bunch_weight=$actual_empty_bunch_prod->WEIGHT;
			}
			if ($actual_abu_prod==NULL){
				$abu_er=0;
			}else{
				$abu_er=$actual_abu_prod->WEIGHT/$actual->FFB_PROCESSED;
			}
		}
				
		$actual_cpo_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'CPO');
		$actual_cpo_recycledespatch=$this->model_s_input_ba->get_recycle_despatch($dates, $company, 'CPO');
		$actual_kernel_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'KRN');
		$actual_shell_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'CKG');
		$actual_empty_bunch_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'TNK');
		$actual_abu_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'ABJ');
		
		$empty_bunch_despatch_weight=0;
		if ($actual_empty_bunch_despatch<>NULL){
			$empty_bunch_despatch_weight=$actual_empty_bunch_despatch->WEIGHT;	
		}
		$abu_despatch_weight=0;
		if ($actual_abu_despatch<>NULL){
			$abu_despatch_weight=$actual_abu_despatch->WEIGHT;
		}
		
		$actual_cpo_stock=$this->model_s_input_ba->get_stock($id, $company, 'CPO');
		$actual_kernel_stock=$this->model_s_input_ba->get_stock($id, $company, 'KRN');
		$actual_shell_stock=$this->model_s_input_ba->get_stock($id, $company, 'CKG');
		$actual_empty_bunch_stock=$this->model_s_input_ba->get_stock($id, $company, 'TNK');
		$actual_abu_stock=$this->model_s_input_ba->get_stock($id, $company, 'ABJ');
		//$actual_solid_stock=$this->model_s_input_ba->get_stock($id, $company, 'SLD');	
		$empty_bunch_stock_weight=0;
		if ($actual_empty_bunch_stock<>NULL){
			$empty_bunch_stock_weight=$actual_empty_bunch_stock->WEIGHT;
		}
		$abu_stock_weight=0;
		if ($actual_abu_stock<>NULL){
			$abu_stock_weight=$actual_abu_stock->WEIGHT;
		}
				
		$actual_cpo_stock1=$this->model_s_input_ba->get_storage_stock($id, $company, 'CPO', 1);
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
		$check_ffa_month=$this->model_s_input_ba->check_ffa($first_month, $dates, $company, 'CPO'); 
		if ($check_ffa_month==true){
			$ffa_month = $actual_cpo_prod->FFA;	
		}else{
			$ffa_month = (($actual_cpo_prod->WEIGHT*$actual_cpo_prod->FFA)+($actual_cpo_prod_yesterday->WEIGHT*$actual_cpo_prod_yesterday->FFA))/($actual_cpo_prod->WEIGHT+$actual_cpo_prod_yesterday->WEIGHT);
		}
		$check_ffa_month_yesterday=$this->model_s_input_ba->check_ffa($first_month, $dates-1, $company, 'CPO'); 
		if ($check_ffa_month_yesterday==true){
			$ffa_month_yesterday = $actual_cpo_prod_yesterday->FFA;	
		}else{
			$ffa_month_yesterday = (($actual_cpo_prod_yesterday->WEIGHT*$actual_cpo_prod_yesterday->FFA)+($actual_cpo_prod_yesterday->WEIGHT*$actual_cpo_prod_yesterday->FFA))/($actual_cpo_prod->WEIGHT+$actual_cpo_prod_yesterday->WEIGHT);
		}
		*/

		$row_ffa_month=$this->model_s_input_ba->get_ffa_period($first_month, $dates, $company, 'CPO'); //month
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
		//$ffa_cpo_month  = 0;
		//$i_ffa = 0;
		/*
		//masih error
		if ($row_ffa_month!=NULL){
			foreach($row_ffa_month as $row){				
				$ffa_month = $ffa_month + $row['FFA'];
				
				if ($row['FFA']=='0' || $row['FFA']=='0.00'){
					$i_ffa = $i_ffa + 1;
				}	
						
			}
			if ($i_ffa==0){
				$ffa_cpo_month = 0;
			}else{
				$ffa_cpo_month = $ffa_month /$i_ffa;
			}
		}
		//masih error
		*/
		$actual_cpo_stock2=$this->model_s_input_ba->get_storage_stock($id, $company, 'CPO', 2);	
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
		//asep
		$actual_cpo_stock3=$this->model_s_input_ba->get_storage_stock($id, $company, 'CPO', 3);
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
		//asep
		$sum_weight_cpo=$weight_cpo1+$weight_cpo2+$weight_cpo3;
		if($sum_weight_cpo==0){
			$ffa_stock_cpo=0;
		}else{
			$ffa_stock_cpo=(($weight_cpo1*$ffa_cpo1)+($weight_cpo2*$ffa_cpo2)+($weight_cpo3*$ffa_cpo3))/($sum_weight_cpo);	
		}
		$actual_sounding_cpo1=$this->model_s_input_ba->get_sounding_cpo($dates, $company, 'CPO', 1);
		if ($actual_sounding_cpo1==NULL){
			$sounding_cpo1=0;
			$sounding_temp_cpo1=0;
		}else{			
			$sounding_cpo1=$actual_sounding_cpo1->HEIGHT;
			$sounding_temp_cpo1=$actual_sounding_cpo1->TEMPERATURE;
		}
		$actual_sounding_cpo2=$this->model_s_input_ba->get_sounding_cpo($dates, $company, 'CPO', 2);
		if ($actual_sounding_cpo2==NULL){
			$sounding_cpo2=0;
			$sounding_temp_cpo2=0;
		}else{			
			$sounding_cpo2=$actual_sounding_cpo2->HEIGHT;
			$sounding_temp_cpo2=$actual_sounding_cpo2->TEMPERATURE;
		}
		//asep
		$actual_sounding_cpo3=$this->model_s_input_ba->get_sounding_cpo($dates, $company, 'CPO', 3);
		if ($actual_sounding_cpo3==NULL){
			$sounding_cpo3=0;
			$sounding_temp_cpo3=0;
		}else{			
			$sounding_cpo3=$actual_sounding_cpo3->HEIGHT;
			$sounding_temp_cpo3=$actual_sounding_cpo3->TEMPERATURE;
		}
		//asep
		$actual_kernel_stock1=$this->model_s_input_ba->get_storage_stock($id, $company, 'KERNEL', 1);
		if ($actual_kernel_stock1==NULL){
			$weight_kernel1= 0;
			$ffa_kernel1=0;
			$dirt_kernel1=0;
			$moisture_kernel1=0;
		}else{
			$weight_kernel1=$actual_kernel_stock1->WEIGHT;
			$ffa_kernel1=$actual_kernel_stock1->FFA;
			$dirt_kernel1=$actual_kernel_stock1->DIRT;
			$moisture_kernel1=$actual_kernel_stock1->MOISTURE;
		}
		$actual_kernel_stock2=$this->model_s_input_ba->get_storage_stock($id, $company, 'KERNEL', 2);
		if ($actual_kernel_stock2==NULL){
			$weight_kernel2= 0;
			$ffa_kernel2=0;
			$dirt_kernel2=0;
			$moisture_kernel2=0;
		}else{
			$weight_kernel2= $actual_kernel_stock2->WEIGHT;
			$ffa_kernel2=$actual_kernel_stock2->FFA;
			$dirt_kernel2=$actual_kernel_stock2->DIRT;
			$moisture_kernel2=$actual_kernel_stock2->MOISTURE;
		}
		$actual_sounding_kernel1=$this->model_s_input_ba->get_sounding_kernel($dates, $company, 'KERNEL', 1);
		if ($actual_sounding_kernel1==NULL){
			$sounding_kernel1=0;
		}else{
			$sounding_kernel1=$actual_sounding_kernel1->HEIGHT;
			if ($sounding_kernel1==0){
				$sounding_kernel1=$actual_sounding_kernel1->HEIGHT2;	
			}
		}
		$actual_sounding_kernel2=$this->model_s_input_ba->get_sounding_kernel($dates, $company, 'KERNEL', 2);
		if ($actual_sounding_kernel2==NULL){
			$sounding_kernel2=0;
		}else{
			$sounding_kernel2=$actual_sounding_kernel2->HEIGHT;
			if ($sounding_kernel2==0){
				$sounding_kernel2=$actual_sounding_kernel2->HEIGHT2;	
			}
		}
		$ffb_month_todate=$this->model_s_input_ba->get_ffb_period($first_month, $dates, $company);
		$ffb_month_todate_yesterday=$this->model_s_input_ba->get_ffb_period($first_month, $dates-1, $company); //yesterday
		$ffb_year_todate=$this->model_s_input_ba->get_ffb_period($first_year, $dates, $company);
		if($actual->PROCESSED_HOUR==0){
			$actual_troughput=0;			
		}else{
			$actual_troughput=$actual->FFB_PROCESSED/$actual->PROCESSED_HOUR/1000;	
		}
		if($ffb_month_todate->PROCESSED_HOUR==0){
			$month_troughput=0;			
		}else{
			$month_troughput=$ffb_month_todate->FFB_PROCESSED/$ffb_month_todate->PROCESSED_HOUR/1000;	
		}
		if($ffb_year_todate->PROCESSED_HOUR==0){
			$year_troughput=0;			
		}else{
			$year_troughput=$ffb_year_todate->FFB_PROCESSED/$ffb_year_todate->PROCESSED_HOUR/1000;
		}
		
		$total_month_ffb =$ffb_month_todate->FFB_INTI+$ffb_month_todate->FFB_PLASMA+$ffb_month_todate->FFB_SUPPLIER+$ffb_month_todate->FFB_GROUP;
		$total_month_ffb_yesterday =$ffb_month_todate_yesterday->FFB_INTI+$ffb_month_todate_yesterday->FFB_PLASMA+$ffb_month_todate_yesterday->FFB_SUPPLIER+$ffb_month_todate_yesterday->FFB_GROUP; //yesterday
		
		$total_year_ffb =$ffb_year_todate->FFB_INTI+$ffb_year_todate->FFB_PLASMA+$ffb_year_todate->FFB_SUPPLIER+$ffb_year_todate->FFB_GROUP;
		$month_cpo_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'CPO');
		$month_cpo_prod_yesterday =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'CPO');
		$year_cpo_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'CPO');
		$month_kernel_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'KRN');
		$year_kernel_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'KRN');
		$month_shell_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'CKG');
		$year_shell_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'CKG');
		$month_empty_bunch_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'TNK');
		$year_empty_bunch_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'TNK');
		$month_abu_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'ABJ');
		$year_abu_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'ABJ');
		//$month_solid_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'SLD');
		//$year_solid_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'SLD');
		
		$month_cpo_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'CPO');
		$year_cpo_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'CPO');
		$month_kernel_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'KRN');
		$year_kernel_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'KRN');
		$month_shell_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'CKG');
		$year_shell_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'CKG');
		$month_empty_bunch_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'TNK');
		$year_empty_bunch_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'TNK');
		$month_abu_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'ABJ');
		$year_abu_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'ABJ');
				
		$qc=$actual->QC;
		$mill_manager=$actual->MILL_MANAGER;
		$ktu=$actual->KTU;
		$administratur=$actual->ADMINISTRATUR;
		$labor=$actual->LABOR;
		
		$dispatch_doc = $this->model_s_input_ba->get_dispatch_doc($dates,$company);
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
    <td class='tbl_th2' align='center' width='80'>FFB</td>
    <td class='tbl_th2' align='center' width='80'>Actual Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Actual Received (Kg)</td>
    <td class='tbl_th2' align='center' width='85'>To date Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Month Budget Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Actual YTD Received (Kg)</td>
    <td class='tbl_th2' align='center' width='80'>Budget YTD Received (Kg)</td>
  </tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Inti"."</td><td class='tbl_th' align='right'>".number_format($actual->FFB_INTI,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->FFB_INTI,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->FFB_INTI,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Plasma"."</td><td class='tbl_th' align='right'>".number_format($actual->FFB_PLASMA,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->FFB_PLASMA,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->FFB_PLASMA,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Outside supplier"."</td><td class='tbl_th' align='right'>".number_format($actual->FFB_SUPPLIER,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->FFB_SUPPLIER,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->FFB_SUPPLIER,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Group"."</td><td class='tbl_th' align='right'>".number_format($actual->FFB_GROUP,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->FFB_GROUP,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->FFB_GROUP,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th7'>&nbsp;"."Total FFB Received"."</td><td class='tbl_th' align='right'>".number_format($total_ffb,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($total_month_ffb,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($total_year_ffb,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Balance Yesterday"."</td><td class='tbl_th' align='right'>".number_format($actual->BALANCE_YESTERDAY,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";

  if ($actual_first==NULL){
  	$actual_first->BALANCE_YESTERDAY = 0;
  }
  $content .= "<tr><td class='tbl_th7'>&nbsp;"." Total FFB"."</td><td class='tbl_th' align='right'>".number_format($total_ffb+$actual->BALANCE_YESTERDAY,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($total_month_ffb+$actual_first->BALANCE_YESTERDAY,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th7'>&nbsp;"." Total FFB Processed"."</td><td class='tbl_th' align='right'>".number_format($actual->FFB_PROCESSED,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->FFB_PROCESSED,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->FFB_PROCESSED,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Balance Today"."</td><td class='tbl_th' align='right'>".number_format($actual->BALANCE,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format(($total_month_ffb+$actual_first->BALANCE_YESTERDAY)-($ffb_month_todate->FFB_PROCESSED),2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Average cage weight"."</td><td class='tbl_th' align='right'>".number_format($actual->CAGE_WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'></td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'></td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td colspan='8' class='tbl_th2' align='center'>&nbsp;"." PERFORMANCE"."</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Processed hour"."</td><td class='tbl_th' align='right'>".number_format($actual->PROCESSED_HOUR,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_month_todate->PROCESSED_HOUR,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffb_year_todate->PROCESSED_HOUR,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Throughput"."</td><td class='tbl_th' align='right'>".number_format($actual_troughput,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($month_troughput,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_troughput,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $kj_month=$this->model_s_input_ba->get_kj_month($dates,$company); 
  if ($kj_month==0){
	$mill_month=0;  
  }else{
  	$mill_month=$ffb_month_todate->FFB_PROCESSED/(30000*20*$kj_month)*100;
  }
  $kj_year=$this->model_s_input_ba->get_kj_year($dates,$company); 
  if ($kj_year==0){
	$mill_year=0;  
  }else{
  	$mill_year=$ffb_year_todate->FFB_PROCESSED/(30000*20*$kj_year)*100;
  }
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Mill Utilization"."</td><td class='tbl_th' align='right'>".number_format($actual->MILL_UTILIZATION,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($mill_month,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td><td class='tbl_th' align='right'>".number_format($mill_year,2)."&nbsp;</td><td class='tbl_th' align='right'>&nbsp;</td></tr>";
  $content .= "<tr><td colspan='8' class='tbl_th2' align='center'>&nbsp;"." PRODUCTION"."</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'> &nbsp;"." CPO"."</td><td align='right' class='tbl_th'>".number_format($actual_cpo_prod->WEIGHT,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_cpo_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>&nbsp;"." FFA"."</td><td align='right' class='tbl_th'>".number_format($actual_cpo_prod->FFA,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($ffa_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'></td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  if ($ffb_month_todate->FFB_PROCESSED==0){
  	$oil_er_month=0;
  }else{
	$oil_er_month=($month_cpo_prod/$ffb_month_todate->FFB_PROCESSED)*100;	  
  }
  if ($ffb_year_todate->FFB_PROCESSED==0){
  	$oil_er_year=0;
	$kernel_er_year=0;
	$shell_er_year=0;
	$abu_er_year=0;
	$empty_bunch_er_year=0;
  }else{
	$oil_er_year=($year_cpo_prod/$ffb_year_todate->FFB_PROCESSED)*100;	 
	$kernel_er_year=($year_kernel_prod/$ffb_year_todate->FFB_PROCESSED)*100;
	$shell_er_year=($year_shell_prod/$ffb_year_todate->FFB_PROCESSED)*100;
	$abu_er_year=($year_abu_prod/$ffb_year_todate->FFB_PROCESSED)*100;
	$empty_bunch_er_year=($year_empty_bunch_prod/$ffb_year_todate->FFB_PROCESSED)*100;
  }
  $content .= "<tr><td class='tbl_th'>&nbsp;"." Oil Extraction Rates"."</td><td align='right' class='tbl_th'>".number_format(($oil_er)*100,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($oil_er_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($oil_er_year,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'> &nbsp;"." KERNEL"."</td><td align='right' class='tbl_th'>".number_format($actual_kernel_prod->WEIGHT,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_kernel_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_kernel_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";

  if ($ffb_month_todate->FFB_PROCESSED==0.00){
	  $kernel_er_month=0;
	  $shell_er_month=0;
	  $abu_er_month=0;
	  $empty_bunch_er_month=0;
  }else{
	  $kernel_er_month=($month_kernel_prod/$ffb_month_todate->FFB_PROCESSED)*100;
	  $shell_er_month=($month_shell_prod/$ffb_month_todate->FFB_PROCESSED)*100;
	  $abu_er_month=($month_abu_prod/$ffb_month_todate->FFB_PROCESSED)*100;
	  $empty_bunch_er_month=($month_empty_bunch_prod/$ffb_month_todate->FFB_PROCESSED)*100;
  }
	  
  $content .= "<tr><td class='tbl_th'> &nbsp;"." Kernel Extraction Rates"."</td><td align='right' class='tbl_th'>".number_format(($kernel_er)*100,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($kernel_er_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($kernel_er_year,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'> &nbsp;"." SHELL"."</td><td align='right' class='tbl_th'>".number_format($actual_shell_prod->WEIGHT,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_shell_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_shell_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";	
  $content .= "<tr><td class='tbl_th'>%</td><td align='right' class='tbl_th'>".number_format(($shell_er)*100,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($shell_er_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($shell_er_year,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'> &nbsp;"." EMPTY BUNCH"."</td><td align='right' class='tbl_th'>".number_format($empty_bunch_weight,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_empty_bunch_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_empty_bunch_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'>%</td><td align='right' class='tbl_th'>".number_format(($empty_bunch_er)*100,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($empty_bunch_er_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($empty_bunch_er_year,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'> &nbsp;"." ABU"."</td><td align='right' class='tbl_th'>".number_format($abu_prod_weight,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($month_abu_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($year_abu_prod,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";	
  $content .= "<tr><td class='tbl_th'>%</td><td align='right' class='tbl_th'>".number_format(($abu_er)*100,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($abu_er_month,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td><td align='right' class='tbl_th'>".number_format($abu_er_year,2)."&nbsp;</td><td align='right' class='tbl_th'>&nbsp;</td></tr>";
  $content .= "<tr><td colspan='5' class='tbl_th3'>&nbsp;</td><td colspan='3' class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2' colspan='2'> &nbsp;"." PRODUCTION QUALITY"."</td><td class='tbl_th2' align='center'>FFA/BROKEN</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th4' colspan='3' rowspan='3'></td></tr>";
  $content .= "<tr><td class='tbl_th' colspan='2'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($actual_cpo_prod->FFA,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_prod->DIRT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_prod->MOISTURE,2)."&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th' colspan='2'> &nbsp;"." KERNEL"."</td><td class='tbl_th' align='right'>".number_format($actual_kernel_prod->FFA,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_kernel_prod->DIRT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_kernel_prod->MOISTURE,2)."&nbsp;</td></tr>";
  $content .= "<tr><td colspan='8' class='tbl_td'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2' colspan='5' align='center'> &nbsp;"." KRITERIA GRADING"."</td><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th2' align='center'>FROM</td><td class='tbl_th2' align='center'>TO</td></tr>";
  $content .= "<tr><td align='center' class='tbl_th2'> &nbsp;"." BUAH MENTAH"."</td><td align='center' class='tbl_th2'>BUAH BUSUK</td><td align='center' class='tbl_th2'>TANGKAI PJG</td><td align='center' class='tbl_th2'>JANJANG KSG</td><td align='center' class='tbl_th2'>BRONDOLAN</td><td align='right' class='tbl_th2'>PROCESS </td><td align='right' class='tbl_th8'>".$actual->HOUR_FROM."&nbsp;</td><td align='right' class='tbl_th8'>".$actual->HOUR_TO."&nbsp;</td></tr>";
  $content .= "<tr><td align='right' class='tbl_th'>".number_format($actual->BUAH_MENTAH,2)."&nbsp;</td><td align='right' class='tbl_th'>".number_format($actual->BUAH_BUSUK,2)."&nbsp;</td><td align='right' class='tbl_th'>".number_format($actual->TANGKAI,2)."&nbsp;</td><td align='right' class='tbl_th'>".number_format($actual->JJK,2)."&nbsp;</td><td align='right' class='tbl_th'>".number_format($actual->BRONDOLAN,2)."&nbsp;</td><td align='right' class='tbl_th2'>CBC HOUR </td><td align='right' class='tbl_th'>".number_format($actual->CBC_FROM,2)."&nbsp;</td><td align='right' class='tbl_th'>".number_format($actual->CBC_TO,2)."&nbsp;</td></tr>";
  $content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2'> &nbsp;"." DESPATCH"."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA/BROKEN</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th2' align='center'>MONTH TODATE</td><td class='tbl_th2' align='center'>YEAR TODATE</td><td class='tbl_th4' rowspan='6'></td></tr>";	
  $content .= "<tr><td class='tbl_th'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->FFA,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->DIRT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_despatch->MOISTURE,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($month_cpo_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_cpo_dispatch,2)."&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." KERNEL"."</td><td class='tbl_th' align='right'>".number_format($actual_kernel_despatch->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_kernel_despatch->FFA,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_kernel_despatch->DIRT,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_kernel_despatch->MOISTURE,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($month_kernel_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_kernel_dispatch,2)."&nbsp;</td></tr>"; 
  $content .= "<tr><td class='tbl_th'> &nbsp;"." SHELL"."</td><td class='tbl_th' align='right'>".number_format($actual_shell_despatch->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='right'></td><td class='tbl_th' align='right'></td><td class='tbl_th' align='right'></td><td class='tbl_th' align='right'>".number_format($month_shell_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_shell_dispatch,2)."&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." EMPTY BUNCH"."</td><td class='tbl_th' align='right'>".number_format($empty_bunch_despatch_weight,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='right'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='right'>".number_format($month_empty_bunch_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_empty_bunch_dispatch,2)."&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." ABU"."</td><td class='tbl_th' align='right'>".number_format($abu_despatch_weight,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='right'>".number_format($month_abu_dispatch,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($year_abu_dispatch,2)."&nbsp;</td></tr>";
  //asep
  if ($actual_cpo_recycledespatch != NULL){
	  //$content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
	  $content .= "<tr><td class='tbl_th2'> &nbsp;"." DESPATCH (RECYCLE) "."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA/BROKEN</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th4' colspan='3' rowspan='3'></td></tr>";
  	  $content .= "<tr><td class='tbl_th'> &nbsp;"." CPO"."</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->BERAT_BERSIH,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->BROKEN,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->DIRTY,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($actual_cpo_recycledespatch->MOIST,2)."&nbsp;</td></tr>";
  }
  //asep
  $content .= "<tr><td colspan='7' class='tbl_th3'>&nbsp;</td><td class='tbl_th4'>&nbsp;</td></tr>";
  $content .= "<tr><td class='tbl_th2'> &nbsp;"." STOCK"."</td><td class='tbl_th2' align='center'>FOR DAY</td><td class='tbl_th2' align='center'>FFA/BROKEN</td><td class='tbl_th2' align='center'>DIRTY</td><td class='tbl_th2' align='center'>MOISTURE</td><td class='tbl_th2' align='center'>SOUNDING (mm)</td><td class='tbl_th2' align='center'>TEMPERATURE</td><td class='tbl_th4' rowspan='10'>&nbsp;</td></tr>";
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
  $content .= "<tr><td class='tbl_th'> &nbsp;"." KERNEL"."</td><td class='tbl_th' align='right'>".number_format($weight_kernel1+$weight_kernel2,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
  $content .= "<tr><td class='tbl_th'> &nbsp;"." BUNKER 1"."</td><td class='tbl_th' align='right'>".number_format($weight_kernel1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_kernel1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($dirt_kernel1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($moisture_kernel1,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($sounding_kernel1*1000)."&nbsp;</td><td class='tbl_th' align='right'></td></tr>";
	$content .= "<tr><td class='tbl_th'> &nbsp;"." BUNKER 2"."</td><td class='tbl_th' align='right'>".number_format($weight_kernel2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($ffa_kernel2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($dirt_kernel2,2)."&nbsp;</td><td class='tbl_th' align='right'>".number_format($moisture_kernel2,2)."&nbsp;</td>&nbsp;<td class='tbl_th' align='right'>".number_format($sounding_kernel2*1000)."&nbsp;</td><td class='tbl_th' align='center'></td></tr>";
	$content .= "<tr><td class='tbl_th'> &nbsp;"." SHELL"."</td><td class='tbl_th' align='right'>".number_format($actual_shell_stock->WEIGHT,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
	$content .= "<tr><td class='tbl_th'> &nbsp;"." EMPTY BUNCH"."</td><td class='tbl_th' align='right'>".number_format($empty_bunch_stock_weight,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
	$content .= "<tr><td class='tbl_th'> &nbsp;"." ABU"."</td><td class='tbl_th' align='right'>".number_format($abu_stock_weight,2)."&nbsp;</td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td><td class='tbl_th' align='center'></td></tr>";
	$content .= "<tr><td colspan='6' class='tbl_th3'>&nbsp;</td><td class='tbl_th4' colspan='2' >&nbsp;</td></tr>";
	$content .= "<tr><td class='tbl_th2' align='center' colspan='2'>NO. DOC</td><td class='tbl_th2' align='center'>CONTRACT</td><td class='tbl_th2' align='center'>PARTY</td><td class='tbl_th2' align='center'>DISPATCH</td><td class='tbl_th2' align='center'>BALANCE</td><td class='tbl_th4' align='left' colspan='2'></td></tr>";
	//$content .= "<tr><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th2' align='center'>&nbsp;</td><td class='tbl_th4' align='left' colspan='3'>&nbsp;</td></tr>";
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
	//$content .= "<tr><td colspan='8' class='tbl_th4'>&nbsp;</td></tr>";

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
	//require_once(APPPATH . 'libraries/daftar_upah/authorize_ba2.inc');
		try{
			//ob_end_clean();
			$html2pdf = new HTML2PDF('P', 'Folio', 'en', true, 'UTF-8', array(4, 4, 4, 4));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content);
			$html2pdf->Output("BA_PRODUKSI_HARIAN_".$company."_".$dates.".pdf");
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
		$headers .= "FFB INTI (For Day)\t";
		$headers .= "FFB INTI (Todate)\t";
		$headers .= "FFB PLASMA (For Day)\t";
		$headers .= "FFB PLASMA (Todate)\t";		
		$headers .= "FFB SUPPLIER (For Day)\t";
		$headers .= "FFB SUPPLIER (Todate)\t";
		$headers .= "FFB GROUP (For Day)\t";
		$headers .= "FFB GROUP (Todate)\t";
		$headers .= "TOTAL FFB (For Day)\t";
		$headers .= "TOTAL FFB (Todate)\t";
		$headers .= "BALANCE YESTERDAY \t";		
		$headers .= "FFB PROCESSED (For Day)\t";
		$headers .= "FFB PROCESSED (Todate)\t";
		$headers .= "BALANCE TODAY \t";
		$headers .= "PROCESSED HOUR (For Day)\t";
		$headers .= "PROCESSED HOUR (Todate)\t"; // todo
		$headers .= "THROUGHPUT (For Day)\t";
		$headers .= "THROUGHPUT (Todate)\t"; //todo
		$headers .= "MILL UTILIZATION\t";
		$headers .= "AVERAGE CAGE WEIGHT\t";
		$headers .= "CPO PRODUCTION (For Day)\t";
		$headers .= "CPO PRODUCTION (Todate)\t";
		$headers .= "OER (For Day)\t";
		$headers .= "OER SHI\t";
		$headers .= "FFA CPO PRODUCTION (For Day)\t"; 
		$headers .= "FFA CPO PRODUCTION (Todate)\t";
		$headers .= "FFA STOCK CPO1\t";
		$headers .= "FFA STOCK CPO2\t";
		$headers .= "FFA STOCK CPO3\t";
		$headers .= "FFA CPO STOCK\t";
		$headers .= "KERNEL PRODUCTION (For Day)\t";
		$headers .= "KERNEL PRODUCTION (Todate)\t";
		$headers .= "KER (For Day)\t";
		$headers .= "KER (Todate)\t"; //30
		$headers .= "SHELL (For Day)\t";
		$headers .= "SHELL (Todate)\t";
		$headers .= "% SHELL\t";
		$headers .= "EMPTY BUNCH (For Day)\t";
		$headers .= "EMPTY BUNCH (Todate)\t";
		$headers .= "% EMPTY BUNCH\t";
		$headers .= "ABU (For Day)\t";
		$headers .= "ABU (Todate)\t";
		$headers .= "% ABU\t";
		$headers .= "DISPATCH CPO (For Day)\t";
		$headers .= "DISPATCH RETURN \t";
		$headers .= "DISPATCH CPO (Todate)\t";
		$headers .= "DISPATCH KERNEL (For Day)\t";
		$headers .= "DISPATCH KERNEL (Todate)\t";
		$headers .= "DISPATCH SHELL (For Day)\t";
		$headers .= "DISPATCH SHELL (Todate)\t";
		$headers .= "DISPATCH EMPTY BUNCH (For Day)\t";
		$headers .= "DISPATCH EMPTY BUNCH (Todate)\t";
		$headers .= "DISPATCH ABU (For Day)\t";
		$headers .= "DISPATCH ABU (Todate)\t";
		$headers .= "STOCK CPO 1\t";
		$headers .= "STOCK CPO 2\t";
		$headers .= "STOCK CPO 3\t";
		$headers .= "STOCK CPO\t";
		$headers .= "STOCK CPO (Todate)\t";
		$headers .= "STOCK KERNEL 1\t";
		$headers .= "STOCK KERNEL 2\t";
		$headers .= "STOCK SHELL\t";
		$headers .= "STOCK EMPTY BUNCH\t";
		$headers .= "STOCK ABU\t";
		$headers .= "BUAH MENTAH\t";
		$headers .= "BUAH BUSUK\t";
		$headers .= "TANGKAI PANJANG\t";
		$headers .= "JANJANG KOSONG\t";
		$headers .= "BRONDOLAN\t";
		$headers .= "COMPANY CODE\t";
		$headers .= "INPUT DATE\t";
		$headers .= "APPROVE DATE\t";

		/*
		$headers .= "% FFB INTI \t";
		$headers .= "% FFB PLASMA \t";
		$headers .= "% FFB SUPPLIER \t";
		$headers .= "% FFB GROUP \t";
		$headers .= "% INTI PROCESSED\t";
		$headers .= "% PLASMA PROCESSED\t";
		$headers .= "% SUPPLIER PROCESSED\t";
		$headers .= "% GROUP PROCESSED\t";
		*/
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
		$data=$this->model_s_input_ba->get_ba_xls($company_code, $periode, $f_day);
		if($data!=NULL){
			foreach ($data as $row){
				if ($row['FFB_PROCESSED']== 0.00||$row['FFB_PROCESSED']==0){
					$oil_er=0;	
					$kernel_er=0;
					$shell_er=0;
					$empty_bunch_er=0;
					$abu_er=0;
				}else{
					$oil_er=$row['CPO_PROD']/$row['FFB_PROCESSED']*100;	
					$kernel_er=$row['KERNEL_PROD']/$row['FFB_PROCESSED']*100;
					$shell_er=$row['SHELL_PROD']/$row['FFB_PROCESSED']*100;
					$empty_bunch_er=$row['EMPTY_BUNCH_PROD']/$row['FFB_PROCESSED']*100;
					$abu_er=$row['ABU_PROD']/$row['FFB_PROCESSED']*100;
				}
				if ($row['FFB_PROCESSED_SHI']== 0.00||$row['FFB_PROCESSED_SHI']==0){
					$oil_er_shi=0;	
					$kernel_er_shi =0;
					//$shell_er_shi=0;
					//$empty_bunch_er_shi=0;
					//$abu_er_shi=0;
				}else{
					$oil_er_shi=($row['CPO_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;	
					$kernel_er_shi =($row['KERNEL_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;
					//$shell_er_shi=($row['SHELL_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;
					//$empty_bunch_er_shi=($row['EMPTY_BUNCH_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;
					//$abu_er_shi=($row['ABU_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;
				}
				
				if ($row['PROCESSED_HOUR_SHI']== 0.00||$row['PROCESSED_HOUR_SHI']==0){
					$throughput_shi=0;	
				}else{
					$throughput_shi=$row['FFB_PROCESSED_SHI']/$row['PROCESSED_HOUR_SHI']/1000;
				}
				
				
				$ffa=$ffa+$row['FFA_PROD'];
				if ($row['FFA_PROD']<> 0.00||$row['FFA_PROD']<>0){
					$count_ffa++;
				}
				if ($count_ffa==0){
					$ffa_shi =$row['FFA_PROD'];
				}else{
					$ffa_shi = $ffa/$count_ffa;
				}
				$line = '';
				//$line .= str_replace('"', '""',$no)."\t";
				$line .= str_replace('"', '""',$no)."\t"; 
				$line .= str_replace('"', '""',$row['BA_DATE'])."\t";
				$line .= str_replace('"', '""',$row['FFB_INTI'])."\t";
				$line .= str_replace('"', '""',$row['FFB_INTI_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFB_PLASMA'])."\t";
				$line .= str_replace('"', '""',$row['FFB_PLASMA_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFB_SUPPLIER'])."\t";
				$line .= str_replace('"', '""',$row['FFB_SUPPLIER_SHI'])."\t";
				$line .= str_replace('"', '""',$row['FFB_GROUP'])."\t";
				$line .= str_replace('"', '""',$row['FFB_GROUP_SHI'])."\t";
				$line .= str_replace('"', '""',$row['TOTAL_FFB'])."\t";
				$line .= str_replace('"', '""',$row['TOTAL_FFB_SHI'])."\t";
				$line .= str_replace('"', '""',$row['BALANCE_YESTERDAY'])."\t";
				$line .= str_replace('"', '""',$row['FFB_PROCESSED'])."\t";
				$line .= str_replace('"', '""',$row['FFB_PROCESSED_SHI'])."\t";
				$line .= str_replace('"', '""',$row['BALANCE_TODAY'])."\t";			
				$line .= str_replace('"', '""',$row['PROCESSED_HOUR'])."\t";
				$line .= str_replace('"', '""',$row['PROCESSED_HOUR_SHI'])."\t";				
				$line .= str_replace('"', '""',$row['THROUGHPUT'])."\t";
				$line .= str_replace('"', '""',$throughput_shi)."\t";
				$line .= str_replace('"', '""',$row['MILL_UTILIZATION'])."\t";
				$line .= str_replace('"', '""',$row['CAGE_WEIGHT'])."\t";
				$line .= str_replace('"', '""',$row['CPO_PROD'])."\t";	
				$line .= str_replace('"', '""',$row['CPO_PROD_SHI'])."\t";	
				$line .= str_replace('"', '""',$oil_er)."\t";	
				$line .= str_replace('"', '""',$oil_er_shi)."\t";			
				$line .= str_replace('"', '""',$row['FFA_PROD'])."\t";
				$line .= str_replace('"', '""',$ffa_shi)."\t";				
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO1'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO2'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK_CPO3'])."\t";
				$line .= str_replace('"', '""',$row['FFA_STOCK'])."\t";			
				$line .= str_replace('"', '""',$row['KERNEL_PROD'])."\t";
				$line .= str_replace('"', '""',$row['KERNEL_PROD_SHI'])."\t";
				$line .= str_replace('"', '""',$kernel_er)."\t";//30
				$line .= str_replace('"', '""',$kernel_er_shi)."\t";
				$line .= str_replace('"', '""',$row['SHELL_PROD'])."\t";
				$line .= str_replace('"', '""',$row['SHELL_PROD_SHI'])."\t";
				$line .= str_replace('"', '""',$shell_er)."\t";
				$line .= str_replace('"', '""',$row['EMPTY_BUNCH_PROD'])."\t";
				$line .= str_replace('"', '""',$row['EMPTY_BUNCH_PROD_SHI'])."\t";
				$line .= str_replace('"', '""',$empty_bunch_er)."\t";
				$line .= str_replace('"', '""',$row['ABU_PROD'])."\t";
				$line .= str_replace('"', '""',$row['ABU_PROD_SHI'])."\t";
				$line .= str_replace('"', '""',$abu_er)."\t";		
				$line .= str_replace('"', '""',$row['DISPATCH_CPO'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_RETURN'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_CPO_SHI'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_KERNEL'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_KERNEL_SHI'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_SHELL'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_SHELL_SHI'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_EMPTY_BUNCH'])."\t";
				$line .= str_replace('"', '""',$row['DISPATCH_EMPTY_BUNCH_SHI'])."\t";	
				$line .= str_replace('"', '""',$row['DISPATCH_ABU'])."\t";	
				$line .= str_replace('"', '""',$row['DISPATCH_ABU_SHI'])."\t";	
				$line .= str_replace('"', '""',$row['STOCK_CPO1'])."\t";	
				$line .= str_replace('"', '""',$row['STOCK_CPO2'])."\t";	
				$line .= str_replace('"', '""',$row['STOCK_CPO3'])."\t";	
				$line .= str_replace('"', '""',$row['STOCK_CPO'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_CPO_SHI'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_KERNEL1'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_KERNEL2'])."\t";
				//$line .= str_replace('"', '""',)."\t";
				$line .= str_replace('"', '""',$row['STOCK_SHELL'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_EMPTY_BUNCH'])."\t";
				$line .= str_replace('"', '""',$row['STOCK_ABU'])."\t";
				$line .= str_replace('"', '""',$row['BUAH_MENTAH'])."\t";
				$line .= str_replace('"', '""',$row['BUAH_BUSUK'])."\t";
				$line .= str_replace('"', '""',$row['TANGKAI'])."\t";
				$line .= str_replace('"', '""',$row['JJK'])."\t";
				$line .= str_replace('"', '""',$row['BRONDOLAN'])."\t";
				$line .= str_replace('"', '""',$row['COMPANY_CODE'])."\t";
				$line .= str_replace('"', '""',$row['INPUT_DATE'])."\t";
				$line .= str_replace('"', '""',$row['APPROVED_DATE'])."\t";
				$no++;
				$data .= trim($line)."\n"; 
	
		/*
			$headers .= "% FFB INTI \t";
			$headers .= "% FFB PLASMA \t";
			$headers .= "% FFB SUPPLIER \t";
			$headers .= "% FFB GROUP \t";
			$headers .= "% INTI PROCESSED\t";
			$headers .= "% PLASMA PROCESSED\t";
			$headers .= "% SUPPLIER PROCESSED\t";
			$headers .= "% GROUP PROCESSED\t";
			*/
			
			}
			$data = str_replace("\r","",$data);
			$data = str_replace("Array","",$data);
		
		 //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=Rekap_BA_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
		}
		

	}
	function pdf(){
require_once(APPPATH . '/libraries/html2pdf/html2pdf.class.php');

		ob_start();
		// Tampilkan HTML untuk di jadikan PDF //
		//$content = ob_get_clean();
		$content='';
		$company_code = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
		$f_day = $periode."01";
		$data=$this->model_s_input_ba->get_data_ba($company_code, $periode, $f_day);
		$content = "
		<style> .tbl_header { font-size: 12px; border-top:1px solid;border-left:1px solid} 
.tbl_th { font-size: 10px; border-bottom:1px solid; border-right:1px solid} 
.tbl_header2 { font-size: 18px; border-bottom:2px solid; border-right:1px solid} 
.tbl_header3 { font-size: 10px; border-bottom:2px solid} 
.tbl_header4 { font-size: 12px; border-bottom:2px solid; border-right:1px solid} 
.tbl_td { font-size: 10px; border-bottom:1px solid; border-right:1px solid} 
.tbl_td2 {border-right:1px solid} 
.tbl_2 { font-size: 12px;color:#678197;} 
.content { font-size: 12px;color:#678197; } .content2 { font-size: 11px;color:#678197; } </style>
<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>
<tr>    
    <td colspan='6' align='center' class='tbl_header4' height='40'>PT. ".$company." </td>
    <td colspan='13' align='center' class='tbl_header2' height='40'>BERITA ACARA PRODUKSI HARIAN</td>
    <td colspan='5' align='center' class='tbl_header4' height='40'>PERIODE ".$periode." </td>
  </tr>
  <tr>
    <td rowspan='3' align='center' class='tbl_th' width='40'>TGL</td>   
    <td colspan='5' class='tbl_th' align='center'>TBS</td>
    <td colspan='11' class='tbl_th' align='center'>CPO</td>
    <td colspan='7' class='tbl_th' align='center'>KERNEL</td>
  </tr>
  <tr>
    <td colspan='2' class='tbl_th' align='center' width='85'>TERIMA</td>
    <td colspan='2' class='tbl_th' align='center' width='85'>OLAH</td>
    <td rowspan='2' class='tbl_th' align='center' width='40'>RESTAN PABRIK</td>
    <td colspan='2' class='tbl_th' align='center' width='85'> PRODUKSI</td>
    <td colspan='2' class='tbl_th' align='center' width='60'>ER </td>
    <td colspan='2' class='tbl_th' align='center' width='60'>FFA PROD</td>
    <td rowspan='2' class='tbl_th' align='center' width='35'>FFA STOCK</td>
    <td colspan='2' class='tbl_th' align='center' width='85'>DISPATCH </td>
    <td colspan='2' class='tbl_th' align='center' width='85'>STOCK</td>
    <td colspan='2' class='tbl_th' align='center' width='85'>PRODUKSI</td>
    <td colspan='2' class='tbl_th' align='center' width='60'>ER </td>
    <td colspan='2' class='tbl_th' align='center' width='85'>DISPATCH </td>
    <td rowspan='2' class='tbl_th' align='center' width='50'>STOCK </td>
  </tr>
  <tr>
    
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>TANK 1</td>
    <td class='tbl_th' align='center'>TANK 2</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
    <td class='tbl_th' align='center'>HI</td>
    <td class='tbl_th' align='center'>SHI</td>
  </tr>
";

//var_dump($content);
$oil_er_shi=0;
$kernel_er_shi=0;
$ffa = 0;
$count_ffa = 0;
$ffa_shi = 0;
$oil_er=0;	
$kernel_er=0;
if ($data<>NULL){
 foreach($data as $row){
        $content.="<tr>";
		if ($row['FFB_PROCESSED']== 0.00||$row['FFB_PROCESSED']==0){
			$oil_er=0;	
			$kernel_er=0;
		}else{
			$oil_er=$row['CPO_PROD']/$row['FFB_PROCESSED']*100;	
			$kernel_er=$row['KERNEL_PROD']/$row['FFB_PROCESSED']*100;
		}
		if ($row['FFB_PROCESSED_SHI']== 0.00||$row['FFB_PROCESSED_SHI']==0){
			$oil_er_shi=0;	
			$kernel_er_shi =0;
		}else{
			$oil_er_shi=($row['CPO_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;	
			$kernel_er_shi =($row['KERNEL_PROD_SHI']/$row['FFB_PROCESSED_SHI'])*100;
		}
		
		$ffa=$ffa+$row['FFA_PROD'];
		if ($row['FFA_PROD']<> 0.00||$row['FFA_PROD']<>0){
			$count_ffa++;
		}
		if ($count_ffa==0){
			$ffa_shi =$row['FFA_PROD'];
		}else{
			$ffa_shi = $ffa/$count_ffa;
		}
		
		$qc=$row['QC'];
		$labor=$row['LABOR'];
		$mill=$row['MILL_MANAGER'];
		$adm=$row['ADMINISTRATUR'];
		$ktu=$row['KTU'];
		
        $content.="<td class='tbl_td' align='center'>".$row['BA_DATE']."</td>";		 
        $content.="<td class='tbl_td' align='right'>".number_format($row['FFB'],2)."</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['FFB_SHI'],2)."</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['FFB_PROCESSED'],2)."</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['FFB_PROCESSED_SHI'],2)."</td>";
        $content.="<td class='tbl_td' align='right'>".number_format($row['BALANCE_YESTERDAY'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['CPO_PROD'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['CPO_PROD_SHI'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($oil_er,2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($oil_er_shi,2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['FFA_PROD'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($ffa_shi,2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['FFA_STOCK'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['DISPATCH_CPO'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['DISPATCH_CPO_SHI'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['STOCK_CPO1'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['STOCK_CPO2'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['KERNEL_PROD'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['KERNEL_PROD_SHI'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($kernel_er,2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($kernel_er_shi,2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['DISPATCH_KERNEL'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['DISPATCH_KERNEL_SHI'],2)."</td>";
		$content.="<td class='tbl_td' align='right'>".number_format($row['STOCK_KERNEL1']+$row['STOCK_KERNEL2'],2)."</td>";
		$content.="<td class='tbl_td' align='right'></td>";
		
        $content.='</tr>';
		
        
    }

	
        $content.="
			<tr>
				<td colspan='24' height='20'>&nbsp;</td>
				<td class='tbl_td2'>&nbsp;</td>
			</tr>
			<tr>
				<td colspan='2'>&nbsp;</td>
				<td colspan='8' align='center'>Prepared By</td>
				<td colspan='8' align='center'>Checked By</td>
				<td colspan='5' align='center'>Approved By</td>
				<td class='tbl_td2' colspan='2'>&nbsp;</td>
			 </tr>
			 <tr>
				<td colspan='24' height='30'>&nbsp;</td>
				<td class='tbl_td2'>&nbsp;</td>
			</tr>";
			$content.="<tr>";
			$content.="<td colspan='2'>&nbsp;</td>";
			$content.="<td colspan='4' align='center'>".$qc."</td>";
			$content.="<td colspan='4' align='center'>".$labor."</td>";
			$content.="<td colspan='4' align='center'>".$mill."</td>";
			$content.="<td colspan='4' align='center'>".$ktu."</td>";
			$content.="<td colspan='5' align='center'>".$adm."</td>";
			$content.="<td class='tbl_td2' colspan='2'>&nbsp;</td>";
			$content.="</tr>";
			
			$content.="<tr>";
			$content.="<td colspan='2'>&nbsp;</td>";
			$content.="<td colspan='4' align='center'>Quality Control</td>";
			$content.="<td colspan='4' align='center'>Ast. Lab</td>";
			$content.="<td colspan='4' align='center'>Mill Manager</td>";
			$content.="<td colspan='4' align='center'>KTU</td>";
			$content.="<td colspan='5' align='center'>Administratur</td>";
			$content.="<td class='tbl_td2' colspan='2'>&nbsp;</td>";
			$content.="</tr>";
}//end if
			$content.="<tr>
				<td class='tbl_header3' colspan='24' height='5'>&nbsp;</td>
				<td class='tbl_header4'>&nbsp;</td>
			</tr>";
		$content.="</table>";

		try{
			//ob_end_clean();
			$html2pdf = new HTML2PDF('L', 'Folio', 'en', true, 'UTF-8', array(5, 15, 5, 5));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content);
			$html2pdf->Output("Rekap_BA_".$company_code."_".$periode.".pdf");
		}catch(HTML2PDF_exception $e) {
			echo 'header("Content-type: application/pdf");'.$e;
			exit;
		}
		
	}
	/*
	function print_pdf($dates,$id){
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
		$yesterday =$ar-1;
		//test
		$pdf = new pdf_usage(); 
               
        $pdf->Open();
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetMargins(3, 12,0);
        $pdf->AddPage('P', 'A4');
        $pdf->AliasNbPages(); 
            
        $pdf->SetStyle("s1","arial","",9,"");
        $pdf->SetStyle("s2","arial","",8,"");
        $pdf->SetStyle("s3","arial","",10,"");
        
        $pdf->SetTextColor(118, 0, 3);
        $pdf->SetX(60);
        
        $pdf->Ln(1);
        
        require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
        $columns = 8; //number of Columns
        
        //Initialize the table class
        $pdf->tbInitialize($columns, true, true);
		
		//Get data
		$total_ffb = 0;
		$actual=$this->model_s_input_ba->get_ffb_actual($id);
        $total_ffb =$actual->FFB_INTI+$actual->FFB_PLASMA+$actual->FFB_SUPPLIER+$actual->FFB_GROUP;
		
		$actual_cpo_prod=$this->model_s_input_ba->get_production($id, $company, 'CPO');
		$actual_kernel_prod=$this->model_s_input_ba->get_production($id, $company, 'KRN');
		$actual_shell_prod=$this->model_s_input_ba->get_production($id, $company, 'CKG');
		$actual_empty_bunch_prod=$this->model_s_input_ba->get_production($id, $company, 'TNK');
		$actual_abu_prod=$this->model_s_input_ba->get_production($id, $company, 'ABJ');
		//$actual_solid_prod=$this->model_s_input_ba->get_production($id, $company, 'SLD');
		if($actual->FFB_PROCESSED==0){
			$oil_er=0;
			$kernel_er=0;
			$shell_er=0;
			$empty_bunch_er=0;
			$abu_er=0;
			//$solid_er=0;
		}else{
			$oil_er=$actual_cpo_prod->WEIGHT/$actual->FFB_PROCESSED;
			$kernel_er=$actual_kernel_prod->WEIGHT/$actual->FFB_PROCESSED;
			$shell_er=$actual_shell_prod->WEIGHT/$actual->FFB_PROCESSED;
			$empty_bunch_er=$actual_empty_bunch_prod->WEIGHT/$actual->FFB_PROCESSED;
			$abu_er=$actual_abu_prod->WEIGHT/$actual->FFB_PROCESSED;
			//$solid_er=$actual_solid_prod->WEIGHT/$actual->FFB_PROCESSED;
		}
				
		$actual_cpo_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'CPO');
		$actual_kernel_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'KRN');
		$actual_shell_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'CKG');
		$actual_empty_bunch_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'TNK');
		$actual_abu_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'ABJ');
		//$actual_solid_despatch=$this->model_s_input_ba->get_despatch($id, $company, 'SLD');
		
		$actual_cpo_stock=$this->model_s_input_ba->get_stock($id, $company, 'CPO');
		$actual_kernel_stock=$this->model_s_input_ba->get_stock($id, $company, 'KRN');
		$actual_shell_stock=$this->model_s_input_ba->get_stock($id, $company, 'CKG');
		$actual_empty_bunch_stock=$this->model_s_input_ba->get_stock($id, $company, 'TNK');
		$actual_abu_stock=$this->model_s_input_ba->get_stock($id, $company, 'ABJ');
		//$actual_solid_stock=$this->model_s_input_ba->get_stock($id, $company, 'SLD');
		
		$actual_cpo_stock1=$this->model_s_input_ba->get_storage_stock($id, $company, 'CPO', 1);
		if ($actual_cpo_stock1==NULL){
			$weight_cpo1=0;
			$ffa_cpo1=0;
			$dirt_cpo1=0;
			$moisture_cpo1=0;
		}else{
			$weight_cpo1=$actual_cpo_stock1->WEIGHT;
			$ffa_cpo1=$actual_cpo_stock1->FFA;
			$dirt_cpo1=$actual_cpo_stock1->DIRT;
			$moisture_cpo1=$actual_cpo_stock1->MOISTURE;
		}
		$actual_cpo_stock2=$this->model_s_input_ba->get_storage_stock($id, $company, 'CPO', 2);	
		if ($actual_cpo_stock2==NULL){
			$weight_cpo2=0;
			$ffa_cpo2=0;
			$dirt_cpo2=0;
			$moisture_cpo2=0;
		}else{
			$weight_cpo2=$actual_cpo_stock2->WEIGHT;
			$ffa_cpo2=$actual_cpo_stock2->FFA;
			$dirt_cpo2=$actual_cpo_stock2->DIRT;
			$moisture_cpo2=$actual_cpo_stock2->MOISTURE;
		}
		$actual_sounding_cpo1=$this->model_s_input_ba->get_sounding_cpo($dates, $company, 'CPO', 1);
		if ($actual_sounding_cpo1==NULL){
			$sounding_cpo1=0;
			$sounding_temp_cpo1=0;
		}else{			
			$sounding_cpo1=$actual_sounding_cpo1->HEIGHT;
			$sounding_temp_cpo1=$actual_sounding_cpo1->TEMPERATURE;
		}
		$actual_sounding_cpo2=$this->model_s_input_ba->get_sounding_cpo($dates, $company, 'CPO', 2);
		if ($actual_sounding_cpo2==NULL){
			$sounding_cpo2=0;
			$sounding_temp_cpo2=0;
		}else{			
			$sounding_cpo2=$actual_sounding_cpo2->HEIGHT;
			$sounding_temp_cpo2=$actual_sounding_cpo2->TEMPERATURE;
		}
		$actual_kernel_stock1=$this->model_s_input_ba->get_storage_stock($id, $company, 'KERNEL', 1);
		if ($actual_kernel_stock1==NULL){
			$weight_kernel1= 0;
			$ffa_kernel1=0;
			$dirt_kernel1=0;
			$moisture_kernel1=0;
		}else{
			$weight_kernel1=$actual_kernel_stock1->WEIGHT;
			$ffa_kernel1=$actual_kernel_stock1->FFA;
			$dirt_kernel1=$actual_kernel_stock1->DIRT;
			$moisture_kernel1=$actual_kernel_stock1->MOISTURE;
		}
		$actual_kernel_stock2=$this->model_s_input_ba->get_storage_stock($id, $company, 'KERNEL', 2);
		if ($actual_kernel_stock2==NULL){
			$weight_kernel2= 0;
			$ffa_kernel2=0;
			$dirt_kernel2=0;
			$moisture_kernel2=0;
		}else{
			$weight_kernel2= $actual_kernel_stock2->WEIGHT;
			$ffa_kernel2=$actual_kernel_stock2->FFA;
			$dirt_kernel2=$actual_kernel_stock2->DIRT;
			$moisture_kernel2=$actual_kernel_stock2->MOISTURE;
		}
		$actual_sounding_kernel1=$this->model_s_input_ba->get_sounding_kernel($dates, $company, 'KERNEL', 1);
		if ($actual_sounding_kernel1==NULL){
			$sounding_kernel1=0;
		}else{
			$sounding_kernel1=$actual_sounding_kernel1->HEIGHT;
		}
		$actual_sounding_kernel2=$this->model_s_input_ba->get_sounding_kernel($dates, $company, 'KERNEL', 2);		
		if ($actual_kernel_stock2==NULL){
			$sounding_kernel2=0;
		}else{
			$sounding_kernel2=$actual_sounding_kernel2->HEIGHT;
		}
		$ffb_month_todate=$this->model_s_input_ba->get_ffb_period($first_month, $dates, $company);
		if($ffb_month_todate->PROCESSED_HOUR==0){
			$month_troughput=0;			
		}else{
			$month_troughput=$ffb_month_todate->FFB_PROCESSED/$ffb_month_todate->PROCESSED_HOUR/1000;	
		}
		if($ffb_year_todate->PROCESSED_HOUR==0){
			$year_troughput=0;			
		}else{
			$year_troughput=$ffb_year_todate->FFB_PROCESSED/$ffb_year_todate->PROCESSED_HOUR/1000;
		}
		
		$total_month_ffb =$ffb_month_todate->FFB_INTI+$ffb_month_todate->FFB_PLASMA+$ffb_month_todate->FFB_SUPPLIER+$ffb_month_todate->FFB_GROUP;
		$ffb_year_todate=$this->model_s_input_ba->get_ffb_period($first_year, $dates, $company);
		$total_year_ffb =$ffb_year_todate->FFB_INTI+$ffb_year_todate->FFB_PLASMA+$ffb_year_todate->FFB_SUPPLIER+$ffb_year_todate->FFB_GROUP;
		$month_cpo_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'CPO');
		$year_cpo_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'CPO');
		$month_kernel_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'KRN');
		$year_kernel_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'KRN');
		$month_shell_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'CKG');
		$year_shell_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'CKG');
		$month_empty_bunch_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'TNK');
		$year_empty_bunch_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'TNK');
		$month_abu_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'ABJ');
		$year_abu_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'ABJ');
		//$month_solid_prod =$this->model_s_input_ba->get_prod_period($first_month, $dates, $company, 'SLD');
		//$year_solid_prod =$this->model_s_input_ba->get_prod_period($first_year, $dates, $company, 'SLD');
		
		$month_cpo_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'CPO');
		$year_cpo_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'CPO');
		$month_kernel_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'KRN');
		$year_kernel_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'KRN');
		$month_shell_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'CKG');
		$year_shell_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'CKG');
		$month_empty_bunch_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'TNK');
		$year_empty_bunch_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'TNK');
		$month_abu_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'ABJ');
		$year_abu_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'ABJ');
		//$month_solid_dispatch =$this->model_s_input_ba->get_dispatch_period($first_month, $dates, $company, 'SLD');
		//$year_solid_dispatch =$this->model_s_input_ba->get_dispatch_period($first_year, $dates, $company, 'SLD');
		
		$qc=$actual->QC;
		$mill_manager=$actual->MILL_MANAGER;
		$ktu=$actual->KTU;
		$administratur=$actual->ADMINISTRATUR;
		$labor=$actual->LABOR;
        //set the Table Type
        $pdf->tbSetTableType($table_default_table_type);
        $aSimpleHeader = array();
		$aSimpleHeader1 = array();
		$aSimpleHeader2 = array();
		$aSimpleHeader3 = array();
		$aSimpleHeader4 = array();
		$aSimpleHeader5 = array();
		$aSimpleHeader6 = array();
		$aSimpleHeader7 = array();
		$aSimpleHeader71 = array();
		$aSimpleHeader8 = array();
		$aSimpleHeader9 = array();
		$aSimpleHeader10 = array();
        $aSimpleHeader11 = array();
		$aSimpleHeader12 = array();
		$aSimpleHeader13 = array();
		$aSimpleHeader14 = array();
		$aSimpleHeader15 = array();
		$aSimpleHeader16 = array();
		$aSimpleHeader17 = array();
		$aSimpleHeader18 = array();
		$aSimpleHeader19 = array();
		$aSimpleHeader20 = array();
		$aSimpleHeader21 = array();
		$aSimpleHeader22 = array();
		$aSimpleHeader23 = array();
		$aSimpleHeader24 = array();
		$aSimpleHeader25 = array();
		$aSimpleHeader26 = array();
		$aSimpleHeader27 = array();
		$aSimpleHeader28 = array();
		$aSimpleHeader29 = array();
		//$aSimpleHeader30 = array();
		//$aSimpleHeader31 = array();
		$aSimpleHeader32 = array();		
		$aSimpleHeader33 = array();
		$aSimpleHeader331 = array();
		$aSimpleHeader332 = array();
		$aSimpleHeader333 = array();
		$aSimpleHeader334 = array();
		$aSimpleHeader335 = array();
		$aSimpleHeader336 = array();
		$aSimpleHeader34 = array();
		$aSimpleHeader35 = array();
		$aSimpleHeader351 = array();
		$aSimpleHeader352 = array();
		$aSimpleHeader353 = array();
		$aSimpleHeader354 = array();
		$aSimpleHeader355 = array();
		//$aSimpleHeader356 = array();
		$aSimpleHeader36 = array();
		$aSimpleHeader37 = array();
		$aSimpleHeader371 = array();
		$aSimpleHeader3711 = array();
		$aSimpleHeader3712 = array();
		$aSimpleHeader372 = array();
		$aSimpleHeader3721 = array();
		$aSimpleHeader3722 = array();
		$aSimpleHeader38 = array();
		$aSimpleHeader39 = array();
		$aSimpleHeader40 = array();
		$aSimpleHeader41 = array();
		//$aSimpleHeader42 = array();
		//$aSimpleHeader43 = array();
		//$aSimpleHeader44 = array();
		//$aSimpleHeader45 = array();
		
        $header = array('PT. '.$company_name,'', 'DAILY PRODUCTION REPORT','','','','TANGGAL: ' .date("d-m-Y",strtotime($dates)),'');
		$header1 = array('','','','','', '','','');
		$header2 = array('','For Day','','Month Todate','', '','Year Todate','');
		$header3 = array('FFB','Actual Received (Kg)','Budget Received (Kg)','Actual Received (Kg)','Todate Budget Received (Kg)', 'Month Budget Received (Kg)','Actual YTD Received (Kg)','Budget YTD Received (Kg)');
		$header4 = array('Inti',number_format($actual->FFB_INTI,2),'',number_format($ffb_month_todate->FFB_INTI,2),'', '',number_format($ffb_year_todate->FFB_INTI,2),'');
		$header5 = array('Plasma',number_format($actual->FFB_PLASMA,2),'',number_format($ffb_month_todate->FFB_PLASMA,2),'', '',number_format($ffb_year_todate->FFB_PLASMA,2),'');
		$header6 = array('Outside supplier',number_format($actual->FFB_SUPPLIER,2),'',number_format($ffb_month_todate->FFB_SUPPLIER,2),'', '',number_format($ffb_year_todate->FFB_SUPPLIER,2),'');
		$header7 = array('Group',number_format($actual->FFB_GROUP,2),'',number_format($ffb_month_todate->FFB_GROUP,2),'', '',number_format($ffb_year_todate->FFB_GROUP,2),'');
		$header71 = array('','','','','', '','','');
		$header8 = array('   Total FFB Received',number_format($total_ffb,2),'',number_format($total_month_ffb,2),'', '',number_format($total_year_ffb,2),'');
		$header81 = array('Balance Yesterday',number_format($actual->BALANCE_YESTERDAY,2),'','','', '','','');
		$header82 = array('   Total FFB',number_format($total_ffb+$actual->BALANCE_YESTERDAY,2),'','','', '','','');
		$header9 = array('   Total FFB Processed',number_format($actual->FFB_PROCESSED,2),'',number_format($ffb_month_todate->FFB_PROCESSED,2),'', '',number_format($ffb_year_todate->FFB_PROCESSED,2),'');
		$header10 = array('Balance Today',number_format(($total_ffb+$actual->BALANCE_YESTERDAY)-$actual->FFB_PROCESSED,2),'',number_format($total_ffb-$actual->FFB_PROCESSED,2),'', '',number_format($total_ffb-$actual->FFB_PROCESSED,2),'');
		$header11 = array('Average cage weight',number_format($actual->CAGE_WEIGHT,2),'','','', '','','');
		$header12 = array('','','','','', '','','');
		$header13 = array('PERFORMANCE','','','','', '','','');
		$header14 = array('Processed hour',number_format($actual->PROCESSED_HOUR,2),'',number_format($ffb_month_todate->PROCESSED_HOUR,2),'', '',number_format($ffb_year_todate->PROCESSED_HOUR,2),'');
		$header15 = array('Throughput',number_format($actual->THROUGHPUT,2),'',number_format($month_troughput,2),'', '',number_format($year_troughput,2),'');
		$header16 = array('Mill Utilization',number_format($actual->MILL_UTILIZATION,2),'',number_format($ffb_month_todate->FFB_PROCESSED/(30000*20*1)*100,2),'', '',number_format($ffb_year_todate->FFB_PROCESSED/(30000*20*26*12)*100,2),''); //		
		$header17 = array('','','','','', '','','');
		$header18 = array('PRODUCTION','','','','', '','','');
		$header19 = array('CPO',number_format($actual_cpo_prod->WEIGHT,2),'',number_format($month_cpo_prod,2),'', '',number_format($year_cpo_prod,2),'');
		$header20 = array('FFA',number_format($actual_cpo_prod->FFA,2),'','','', '','','');
		$header21 = array('Oil Extraction Rates',number_format(($oil_er)*100,2),'','','', '','','');
		$header22 = array('KERNEL',number_format($actual_kernel_prod->WEIGHT,2),'',number_format($month_kernel_prod,2),'', '',number_format($year_kernel_prod,2),'');
		$header23 = array('Kernel Extraction Rates',number_format(($kernel_er)*100,2),'','','', '','','');
		$header24 = array('SHELL',number_format($actual_shell_prod->WEIGHT,2),'',number_format($month_shell_prod,2),'', '',number_format($year_shell_prod,2),'');
		$header25 = array('% Shell',number_format(($shell_er)*100,2),'','','', '','','');
		
		$header26 = array('EMPTY BUNCH',number_format($actual_empty_bunch_prod->WEIGHT,2),'',number_format($month_empty_bunch_prod,2),'', '',number_format($year_empty_bunch_prod,2),'');
		$header27 = array('% Empty Bunch',number_format(($empty_bunch_er)*100,2),'','','', '','','');		
		$header28 = array('ABU',number_format($actual_abu_prod->WEIGHT,2),'',number_format($month_abu_prod,2),'', '',number_format($year_abu_prod,2),'');
		$header29 = array('% Abu',number_format(($abu_er)*100,2),'','','', '','','');
		//$header30 = array('SOLID',number_format($actual_solid_prod->WEIGHT,2),'',number_format($month_solid_prod,2),'', '',number_format($year_solid_prod,2),'');
		//$header31 = array('% Solid',number_format(($solid_er)*100,2),'','','', '','','');			
		$header32 = array('','','','','', '','','');
		$header33 = array('PRODUCTION QUALITY','','FFA/BROKEN','DIRTY','MOISTURE', '','','');
		$header331 = array('CPO','',number_format($actual_cpo_prod->FFA,2),number_format($actual_cpo_prod->DIRT,2),number_format($actual_cpo_prod->MOISTURE,2),'','','');
		$header332 = array('KERNEL','',number_format($actual_kernel_prod->FFA,2),number_format($actual_kernel_prod->DIRT,2),number_format($actual_kernel_prod->MOISTURE,2), '','','');	
		$header333 = array('','','','','', '','','');
		$header334 = array('KRITERIA GRADING','','','','', '','FROM','TO');
		$header335 = array('BUAH MENTAH','BUAH BUSUK','JANJANG KSG','TANGKAI PJG','BRONDOLAN', 'PROCESS',number_format($actual->HOUR_FROM,2),number_format($actual->HOUR_TO,2));
		$header336 = array(number_format($actual->BUAH_MENTAH,2),number_format($actual->BUAH_BUSUK,2),number_format($actual->JJK,2),number_format($actual->TANGKAI,2),number_format($actual->BRONDOLAN,2),'CBC HOUR',number_format($actual->CBC_FROM,2),number_format($actual->CBC_TO,2));		
		$header34 = array('','','','','', '','','');
		$header35 = array('DESPATCH','FOR DAY','FFA/BROKEN','DIRTY','MOISTURE', 'M TODATE','Y TODATE','');		
		$header351 = array('CPO',number_format($actual_cpo_despatch->WEIGHT,2),number_format($actual_cpo_despatch->FFA,2),number_format($actual_cpo_despatch->DIRT,2),number_format($actual_cpo_despatch->MOISTURE,2), number_format($month_cpo_dispatch,2),number_format($year_cpo_dispatch,2),'');
		$header352 = array('KERNEL',number_format($actual_kernel_despatch->WEIGHT,2),number_format($actual_kernel_despatch->FFA,2),number_format($actual_kernel_despatch->DIRT,2),number_format($actual_kernel_despatch->MOISTURE,2), number_format($month_kernel_dispatch,2), number_format($year_kernel_dispatch,2),'');
		$header353 = array('SHELL',number_format($actual_shell_despatch->WEIGHT,2),'','','', number_format($month_shell_dispatch,2),number_format($year_shell_dispatch,2),'');
		$header354 = array('EMPTY BUNCH',number_format($actual_empty_bunch_despatch->WEIGHT,2),'','','', number_format($month_empty_bunch_dispatch,2),number_format($year_empty_bunch_dispatch,2),'');
		$header355 = array('ABU',number_format($actual_abu_despatch->WEIGHT,2),'','','', number_format($month_abu_dispatch,2),number_format($year_abu_dispatch,2),'');
		//$header356 = array('SOLID',number_format($actual_solid_despatch->WEIGHT,2),'','','', '',number_format($month_solid_dispatch,2),number_format($year_solid_dispatch,2));
		
		$header36 = array('','','','','', '','','');		
		$header37 = array('STOCK','FOR DAY','FFA/BROKEN','DIRTY','MOISTURE', 'SOUNDING (mm)','TEMP','');		
		$header371 = array('CPO',number_format($weight_cpo1+$weight_cpo2,2),number_format(($ffa_cpo1+$ffa_cpo2)/2,2), '', '', '', '','');	
		
		$header3711 = array('  STORAGE TANK 1',number_format($weight_cpo1,2),number_format($ffa_cpo1,2),number_format($dirt_cpo1,2),number_format($moisture_cpo1,2), number_format($sounding_cpo1*1000),number_format($sounding_temp_cpo1),'');
		
		$header3712 = array('  STORAGE TANK 2',number_format($weight_cpo2,2),number_format($ffa_cpo2,2),number_format($dirt_cpo2,2),number_format($moisture_cpo2,2), number_format(
$sounding_cpo2*1000),number_format($sounding_temp_cpo2),'');	
		
		$header372 = array('KERNEL',number_format($weight_kernel1+$weight_kernel2,2),'','','', '','','');
		
		$header3721 = array('  BUNKER 1',number_format($weight_kernel1,2),number_format($ffa_kernel1,2),number_format($dirt_kernel1,2),number_format($moisture_kernel1,2), number_format($sounding_kernel1*1000),'','');
		
		//$header3722 = array('  BUNKER 2',($actual_kernel_stock2->WEIGHT==NULL) ? 0:number_format($actual_kernel_stock2->WEIGHT,2),($actual_kernel_stock2->FFA==NULL) ? 0:number_format($actual_kernel_stock2->FFA,2),($actual_kernel_stock2->DIRT==NULL) ? 0:number_format($actual_kernel_stock2->DIRT,2),($actual_kernel_stock2->MOISTURE==NULL) ? 0:number_format($actual_kernel_stock2->MOISTURE,2), ($actual_kernel_stock2->WATER_CONTENT==NULL) ? 0:number_format($actual_kernel_stock2->WATER_CONTENT,2),'','');	
		
		$header3722 = array('  BUNKER 2',number_format($weight_kernel2,2),number_format($ffa_kernel2,2),number_format($dirt_kernel2,2),number_format($moisture_kernel2,2),number_format($sounding_kernel2*1000),'','');			
		
		$header373 = array('SHELL',number_format($actual_shell_stock->WEIGHT,2),'','','', '','','');
		$header374 = array('EMPTY BUNCH',number_format($actual_empty_bunch_stock->WEIGHT,2),'','','', '','','');
		$header375 = array('ABU',number_format($actual_abu_stock->WEIGHT,2),'','','', '','','');
		//$header376 = array('SOLID',number_format($actual_solid_stock->WEIGHT,2),'','','', '','','');
		$header38 = array('','','','','', '','','');
		$header39 = array('NOTE',$actual->DESCRIPTION,'','','','','','');
		$header40 = array('','','','','', '','','');
		$header41 = array('NO. DOC','CONTRACT','PARTY','DISPATCH','BALANCE','NOTE','','');
		//$header42 = array('','','','','', '','','');
		//$header43 = array('Prepared By','','',' Checked By','', '','','Approved By');
		//$header44 = array('','','','','', '','','');
		//$header45 = array($actual_abu_stock->QC,$actual_abu_stock->LABOR,'',$actual_abu_stock->MILL_MANAGER,'', $actual_abu_stock->KTU,'',$actual_abu_stock->ADMINISTRATUR);
		
        //Table Header
        for($i=0; $i < $columns; $i++) {
		
			$aSimpleHeader[$i] = $table_default_header_type;
            $aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[$i]['WIDTH'] = 24.5;
			$aSimpleHeader[$i]['LN_SIZE'] = 8;
			$aSimpleHeader[$i]['T_SIZE'] = 9;
			$aSimpleHeader[0]['COLSPAN'] = 2;
			$aSimpleHeader[0]['T_SIZE'] = 8;
            $aSimpleHeader[2]['COLSPAN'] = 4;
			$aSimpleHeader[2]['T_SIZE'] = 10;
			$aSimpleHeader[6]['COLSPAN'] = 2;
			$aSimpleHeader[6]['T_SIZE'] = 8;
		
			$aSimpleHeader1[$i] = $table_default_header_type;
            $aSimpleHeader1[$i]['TEXT'] = $header1[$i];
			$aSimpleHeader1[$i]['WIDTH'] = 24.5;
			$aSimpleHeader1[0]['COLSPAN'] = 8;
            $aSimpleHeader1[$i]['LN_SIZE'] = 1;	
	
			$aSimpleHeader2[$i] = $table_default_header_type;
            $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
			$aSimpleHeader2[$i]['T_SIZE'] = 8;
			$aSimpleHeader2[$i]['LN_SIZE'] = 4;
			$aSimpleHeader2[0]['WIDTH'] = 35;
			$aSimpleHeader2[1]['COLSPAN'] = 2;
			$aSimpleHeader2[1]['WIDTH'] = 40;
			$aSimpleHeader2[3]['COLSPAN'] = 3;
			$aSimpleHeader2[3]['WIDTH'] = 57;
			$aSimpleHeader2[6]['COLSPAN'] = 2;
			$aSimpleHeader2[6]['WIDTH'] = 40;
			
			$aSimpleHeader3[$i] = $table_default_header_type;
            $aSimpleHeader3[$i]['TEXT'] = $header3[$i];
			$aSimpleHeader3[$i]['T_SIZE'] = 8;
			$aSimpleHeader3[$i]['LN_SIZE'] = 4;
			$aSimpleHeader3[0]['WIDTH'] = 35;
			$aSimpleHeader3[1]['WIDTH'] = 23;
			$aSimpleHeader3[2]['WIDTH'] = 23;
			$aSimpleHeader3[3]['WIDTH'] = 23;
			$aSimpleHeader3[4]['WIDTH'] = 23;
			$aSimpleHeader3[5]['WIDTH'] = 23;
			$aSimpleHeader3[6]['WIDTH'] = 23;
			$aSimpleHeader3[7]['WIDTH'] = 23;
			
			$aSimpleHeader4[$i] = $table_default_data_type;
            $aSimpleHeader4[$i]['TEXT'] = $header4[$i];
			$aSimpleHeader4[$i]['T_SIZE'] = 8;
			$aSimpleHeader4[$i]['LN_SIZE'] = 4;
			$aSimpleHeader4[0]['WIDTH'] = 35;
			$aSimpleHeader4[0]['T_ALIGN'] = 'L';
			$aSimpleHeader4[1]['WIDTH'] = 23;
			$aSimpleHeader4[1]['T_ALIGN'] = 'R';
			$aSimpleHeader4[2]['WIDTH'] = 23;
			$aSimpleHeader4[2]['T_ALIGN'] = 'R';
			$aSimpleHeader4[3]['WIDTH'] = 23;
			$aSimpleHeader4[3]['T_ALIGN'] = 'R';
			$aSimpleHeader4[4]['WIDTH'] = 23;
			$aSimpleHeader4[4]['T_ALIGN'] = 'R';
			$aSimpleHeader4[5]['WIDTH'] = 23;
			$aSimpleHeader4[5]['T_ALIGN'] = 'R';
			$aSimpleHeader4[6]['WIDTH'] = 23;
			$aSimpleHeader4[6]['T_ALIGN'] = 'R';
			$aSimpleHeader4[7]['WIDTH'] = 23;
			$aSimpleHeader4[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader4[$i] = $table_default_data_type;
            $aSimpleHeader4[$i]['TEXT'] = $header4[$i];
			$aSimpleHeader4[$i]['T_SIZE'] = 8;
			$aSimpleHeader4[$i]['LN_SIZE'] = 4;
			$aSimpleHeader4[0]['WIDTH'] = 35;
			$aSimpleHeader4[0]['T_ALIGN'] = 'L';
			$aSimpleHeader4[1]['WIDTH'] = 23;
			$aSimpleHeader4[1]['T_ALIGN'] = 'R';
			$aSimpleHeader4[2]['WIDTH'] = 23;
			$aSimpleHeader4[2]['T_ALIGN'] = 'R';
			$aSimpleHeader4[3]['WIDTH'] = 23;
			$aSimpleHeader4[3]['T_ALIGN'] = 'R';
			$aSimpleHeader4[4]['WIDTH'] = 23;
			$aSimpleHeader4[4]['T_ALIGN'] = 'R';
			$aSimpleHeader4[5]['WIDTH'] = 23;
			$aSimpleHeader4[5]['T_ALIGN'] = 'R';
			$aSimpleHeader4[6]['WIDTH'] = 23;
			$aSimpleHeader4[6]['T_ALIGN'] = 'R';
			$aSimpleHeader4[7]['WIDTH'] = 23;
			$aSimpleHeader4[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader5[$i] = $table_default_data_type;
            $aSimpleHeader5[$i]['TEXT'] = $header5[$i];
			$aSimpleHeader5[$i]['T_SIZE'] = 8;
			$aSimpleHeader5[$i]['LN_SIZE'] = 4;
			$aSimpleHeader5[0]['WIDTH'] = 35;
			$aSimpleHeader5[0]['T_ALIGN'] = 'L';
			$aSimpleHeader5[1]['WIDTH'] = 23;
			$aSimpleHeader5[1]['T_ALIGN'] = 'R';
			$aSimpleHeader5[2]['WIDTH'] = 23;
			$aSimpleHeader5[2]['T_ALIGN'] = 'R';
			$aSimpleHeader5[3]['WIDTH'] = 23;
			$aSimpleHeader5[3]['T_ALIGN'] = 'R';
			$aSimpleHeader5[4]['WIDTH'] = 23;
			$aSimpleHeader5[4]['T_ALIGN'] = 'R';
			$aSimpleHeader5[5]['WIDTH'] = 23;
			$aSimpleHeader5[5]['T_ALIGN'] = 'R';
			$aSimpleHeader5[6]['WIDTH'] = 23;
			$aSimpleHeader5[6]['T_ALIGN'] = 'R';
			$aSimpleHeader5[7]['WIDTH'] = 23;
			$aSimpleHeader5[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader6[$i] = $table_default_data_type;
            $aSimpleHeader6[$i]['TEXT'] = $header6[$i];
			$aSimpleHeader6[$i]['T_SIZE'] = 8;
			$aSimpleHeader6[$i]['LN_SIZE'] = 4;
			$aSimpleHeader6[0]['WIDTH'] = 35;
			$aSimpleHeader6[0]['T_ALIGN'] = 'L';
			$aSimpleHeader6[1]['WIDTH'] = 23;
			$aSimpleHeader6[1]['T_ALIGN'] = 'R';
			$aSimpleHeader6[2]['WIDTH'] = 23;
			$aSimpleHeader6[2]['T_ALIGN'] = 'R';
			$aSimpleHeader6[3]['WIDTH'] = 23;
			$aSimpleHeader6[3]['T_ALIGN'] = 'R';
			$aSimpleHeader6[4]['WIDTH'] = 23;
			$aSimpleHeader6[4]['T_ALIGN'] = 'R';
			$aSimpleHeader6[5]['WIDTH'] = 23;
			$aSimpleHeader6[5]['T_ALIGN'] = 'R';
			$aSimpleHeader6[6]['WIDTH'] = 23;
			$aSimpleHeader6[6]['T_ALIGN'] = 'R';
			$aSimpleHeader6[7]['WIDTH'] = 23;
			$aSimpleHeader6[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader7[$i] = $table_default_data_type;
            $aSimpleHeader7[$i]['TEXT'] = $header7[$i];
			$aSimpleHeader7[$i]['T_SIZE'] = 8;
			$aSimpleHeader7[$i]['LN_SIZE'] = 4;
			$aSimpleHeader7[0]['WIDTH'] = 35;
			$aSimpleHeader7[0]['T_ALIGN'] = 'L';
			$aSimpleHeader7[1]['WIDTH'] = 23;
			$aSimpleHeader7[1]['T_ALIGN'] = 'R';
			$aSimpleHeader7[2]['WIDTH'] = 23;
			$aSimpleHeader7[2]['T_ALIGN'] = 'R';
			$aSimpleHeader7[3]['WIDTH'] = 23;
			$aSimpleHeader7[3]['T_ALIGN'] = 'R';
			$aSimpleHeader7[4]['WIDTH'] = 23;
			$aSimpleHeader7[4]['T_ALIGN'] = 'R';
			$aSimpleHeader7[5]['WIDTH'] = 23;
			$aSimpleHeader7[5]['T_ALIGN'] = 'R';
			$aSimpleHeader7[6]['WIDTH'] = 23;
			$aSimpleHeader7[6]['T_ALIGN'] = 'R';
			$aSimpleHeader7[7]['WIDTH'] = 23;
			$aSimpleHeader7[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader71[$i] = $table_default_header_type;
            $aSimpleHeader71[$i]['TEXT'] = $header71[$i];
			$aSimpleHeader71[$i]['WIDTH'] = 24.5;
			$aSimpleHeader71[0]['COLSPAN'] = 8;
            $aSimpleHeader71[$i]['LN_SIZE'] = 0.5;	
			
			$aSimpleHeader8[$i] = $table_default_data_type;
            $aSimpleHeader8[$i]['TEXT'] = $header8[$i];
			$aSimpleHeader8[$i]['T_SIZE'] = 8;
			$aSimpleHeader8[$i]['LN_SIZE'] = 4;
			$aSimpleHeader8[0]['WIDTH'] = 35;
			$aSimpleHeader8[0]['T_ALIGN'] = 'L';
			$aSimpleHeader8[1]['WIDTH'] = 23;
			$aSimpleHeader8[1]['T_ALIGN'] = 'R';
			$aSimpleHeader8[2]['WIDTH'] = 23;
			$aSimpleHeader8[2]['T_ALIGN'] = 'R';
			$aSimpleHeader8[3]['WIDTH'] = 23;
			$aSimpleHeader8[3]['T_ALIGN'] = 'R';
			$aSimpleHeader8[4]['WIDTH'] = 23;
			$aSimpleHeader8[4]['T_ALIGN'] = 'R';
			$aSimpleHeader8[5]['WIDTH'] = 23;
			$aSimpleHeader8[5]['T_ALIGN'] = 'R';
			$aSimpleHeader8[6]['WIDTH'] = 23;
			$aSimpleHeader8[6]['T_ALIGN'] = 'R';
			$aSimpleHeader8[7]['WIDTH'] = 23;
			$aSimpleHeader8[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader81[$i] = $table_default_data_type;
            $aSimpleHeader81[$i]['TEXT'] = $header81[$i];
			$aSimpleHeader81[$i]['T_SIZE'] = 8;
			$aSimpleHeader81[$i]['LN_SIZE'] = 4;
			$aSimpleHeader81[0]['WIDTH'] = 35;
			$aSimpleHeader81[0]['T_ALIGN'] = 'L';
			$aSimpleHeader81[1]['WIDTH'] = 23;
			$aSimpleHeader81[1]['T_ALIGN'] = 'R';
			$aSimpleHeader81[2]['WIDTH'] = 23;
			$aSimpleHeader81[3]['WIDTH'] = 23;
			$aSimpleHeader81[4]['WIDTH'] = 23;
			$aSimpleHeader81[5]['WIDTH'] = 23;
			$aSimpleHeader81[6]['WIDTH'] = 23;
			$aSimpleHeader81[7]['WIDTH'] = 23;
			
			$aSimpleHeader82[$i] = $table_default_data_type;
            $aSimpleHeader82[$i]['TEXT'] = $header82[$i];
			$aSimpleHeader82[$i]['T_SIZE'] = 8;
			$aSimpleHeader82[$i]['LN_SIZE'] = 4;
			$aSimpleHeader82[0]['WIDTH'] = 35;
			$aSimpleHeader82[0]['T_ALIGN'] = 'L';
			$aSimpleHeader82[1]['WIDTH'] = 23;
			$aSimpleHeader82[1]['T_ALIGN'] = 'R';
			$aSimpleHeader82[2]['WIDTH'] = 23;
			$aSimpleHeader82[2]['T_ALIGN'] = 'R';
			$aSimpleHeader82[3]['WIDTH'] = 23;
			$aSimpleHeader82[3]['T_ALIGN'] = 'R';
			$aSimpleHeader82[4]['WIDTH'] = 23;
			$aSimpleHeader82[4]['T_ALIGN'] = 'R';
			$aSimpleHeader82[5]['WIDTH'] = 23;
			$aSimpleHeader82[5]['T_ALIGN'] = 'R';
			$aSimpleHeader82[6]['WIDTH'] = 23;
			$aSimpleHeader82[6]['T_ALIGN'] = 'R';
			$aSimpleHeader82[7]['WIDTH'] = 23;
			$aSimpleHeader82[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader9[$i] = $table_default_data_type;
            $aSimpleHeader9[$i]['TEXT'] = $header9[$i];
			$aSimpleHeader9[$i]['T_SIZE'] = 8;
			$aSimpleHeader9[$i]['LN_SIZE'] = 4;
			$aSimpleHeader9[0]['WIDTH'] = 35;
			$aSimpleHeader9[0]['T_ALIGN'] = 'L';
			$aSimpleHeader9[1]['WIDTH'] = 23;
			$aSimpleHeader9[1]['T_ALIGN'] = 'R';
			$aSimpleHeader9[2]['WIDTH'] = 23;
			$aSimpleHeader9[2]['T_ALIGN'] = 'R';
			$aSimpleHeader9[3]['WIDTH'] = 23;
			$aSimpleHeader9[3]['T_ALIGN'] = 'R';
			$aSimpleHeader9[4]['WIDTH'] = 23;
			$aSimpleHeader9[4]['T_ALIGN'] = 'R';
			$aSimpleHeader9[5]['WIDTH'] = 23;
			$aSimpleHeader9[5]['T_ALIGN'] = 'R';
			$aSimpleHeader9[6]['WIDTH'] = 23;
			$aSimpleHeader9[6]['T_ALIGN'] = 'R';
			$aSimpleHeader9[7]['WIDTH'] = 23;
			$aSimpleHeader9[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader10[$i] = $table_default_data_type;
            $aSimpleHeader10[$i]['TEXT'] = $header10[$i];
			$aSimpleHeader10[$i]['T_SIZE'] = 8;
			$aSimpleHeader10[$i]['LN_SIZE'] = 4;
			$aSimpleHeader10[0]['WIDTH'] = 35;
			$aSimpleHeader10[0]['T_ALIGN'] = 'L';
			$aSimpleHeader10[1]['WIDTH'] = 23;
			$aSimpleHeader10[1]['T_ALIGN'] = 'R';
			$aSimpleHeader10[2]['WIDTH'] = 23;
			$aSimpleHeader10[2]['T_ALIGN'] = 'R';
			$aSimpleHeader10[3]['WIDTH'] = 23;
			$aSimpleHeader10[3]['T_ALIGN'] = 'R';
			$aSimpleHeader10[4]['WIDTH'] = 23;
			$aSimpleHeader10[4]['T_ALIGN'] = 'R';
			$aSimpleHeader10[5]['WIDTH'] = 23;
			$aSimpleHeader10[5]['T_ALIGN'] = 'R';
			$aSimpleHeader10[6]['WIDTH'] = 23;
			$aSimpleHeader10[6]['T_ALIGN'] = 'R';
			$aSimpleHeader10[7]['WIDTH'] = 23;
			$aSimpleHeader10[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader11[$i] = $table_default_data_type;
            $aSimpleHeader11[$i]['TEXT'] = $header11[$i];
			$aSimpleHeader11[$i]['T_SIZE'] = 8;
			$aSimpleHeader11[$i]['LN_SIZE'] = 4;
			$aSimpleHeader11[0]['WIDTH'] = 35;
			$aSimpleHeader11[0]['T_ALIGN'] = 'L';
			$aSimpleHeader11[1]['WIDTH'] = 23;
			$aSimpleHeader11[1]['T_ALIGN'] = 'R';
			$aSimpleHeader11[2]['WIDTH'] = 23;
			$aSimpleHeader11[2]['T_ALIGN'] = 'R';
			$aSimpleHeader11[3]['WIDTH'] = 23;
			$aSimpleHeader11[3]['T_ALIGN'] = 'R';
			$aSimpleHeader11[4]['WIDTH'] = 23;
			$aSimpleHeader11[4]['T_ALIGN'] = 'R';
			$aSimpleHeader11[5]['WIDTH'] = 23;
			$aSimpleHeader11[5]['T_ALIGN'] = 'R';
			$aSimpleHeader11[6]['WIDTH'] = 23;
			$aSimpleHeader11[6]['T_ALIGN'] = 'R';
			$aSimpleHeader11[7]['WIDTH'] = 23;
			$aSimpleHeader11[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader12[$i] = $table_default_header_type;
            $aSimpleHeader12[$i]['TEXT'] = $header12[$i];
			$aSimpleHeader12[$i]['WIDTH'] = 24.5;
			$aSimpleHeader12[0]['COLSPAN'] = 8;
            $aSimpleHeader12[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader13[$i] = $table_default_header_type;
            $aSimpleHeader13[$i]['TEXT'] = $header13[$i];
			$aSimpleHeader13[$i]['WIDTH'] = 24.5;
			$aSimpleHeader13[0]['COLSPAN'] = 8;
            $aSimpleHeader13[$i]['LN_SIZE'] = 4;
			$aSimpleHeader13[$i]['T_SIZE'] = 8;
			
			$aSimpleHeader14[$i] = $table_default_data_type;
            $aSimpleHeader14[$i]['TEXT'] = $header14[$i];
			$aSimpleHeader14[$i]['T_SIZE'] = 8;
			$aSimpleHeader14[$i]['LN_SIZE'] = 4;
			$aSimpleHeader14[0]['WIDTH'] = 35;
			$aSimpleHeader14[0]['T_ALIGN'] = 'L';
			$aSimpleHeader14[1]['WIDTH'] = 23;
			$aSimpleHeader14[1]['T_ALIGN'] = 'R';
			$aSimpleHeader14[2]['WIDTH'] = 23;
			$aSimpleHeader14[2]['T_ALIGN'] = 'R';
			$aSimpleHeader14[3]['WIDTH'] = 23;
			$aSimpleHeader14[3]['T_ALIGN'] = 'R';
			$aSimpleHeader14[4]['WIDTH'] = 23;
			$aSimpleHeader14[4]['T_ALIGN'] = 'R';
			$aSimpleHeader14[5]['WIDTH'] = 23;
			$aSimpleHeader14[5]['T_ALIGN'] = 'R';
			$aSimpleHeader14[6]['WIDTH'] = 23;
			$aSimpleHeader14[6]['T_ALIGN'] = 'R';
			$aSimpleHeader14[7]['WIDTH'] = 23;
			$aSimpleHeader14[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader15[$i] = $table_default_data_type;
            $aSimpleHeader15[$i]['TEXT'] = $header15[$i];
			$aSimpleHeader15[$i]['T_SIZE'] = 8;
			$aSimpleHeader15[$i]['LN_SIZE'] = 4;
			$aSimpleHeader15[0]['WIDTH'] = 35;
			$aSimpleHeader15[0]['T_ALIGN'] = 'L';
			$aSimpleHeader15[1]['WIDTH'] = 23;
			$aSimpleHeader15[1]['T_ALIGN'] = 'R';
			$aSimpleHeader15[2]['WIDTH'] = 23;
			$aSimpleHeader15[2]['T_ALIGN'] = 'R';
			$aSimpleHeader15[3]['WIDTH'] = 23;
			$aSimpleHeader15[3]['T_ALIGN'] = 'R';
			$aSimpleHeader15[4]['WIDTH'] = 23;
			$aSimpleHeader15[4]['T_ALIGN'] = 'R';
			$aSimpleHeader15[5]['WIDTH'] = 23;
			$aSimpleHeader15[5]['T_ALIGN'] = 'R';
			$aSimpleHeader15[6]['WIDTH'] = 23;
			$aSimpleHeader15[6]['T_ALIGN'] = 'R';
			$aSimpleHeader15[7]['WIDTH'] = 23;
			$aSimpleHeader15[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader16[$i] = $table_default_data_type;
            $aSimpleHeader16[$i]['TEXT'] = $header16[$i];
			$aSimpleHeader16[$i]['T_SIZE'] = 8;
			$aSimpleHeader16[$i]['LN_SIZE'] = 4;
			$aSimpleHeader16[0]['WIDTH'] = 35;
			$aSimpleHeader16[0]['T_ALIGN'] = 'L';
			$aSimpleHeader16[1]['WIDTH'] = 23;
			$aSimpleHeader16[1]['T_ALIGN'] = 'R';
			$aSimpleHeader16[2]['WIDTH'] = 23;
			$aSimpleHeader16[2]['T_ALIGN'] = 'R';
			$aSimpleHeader16[3]['WIDTH'] = 23;
			$aSimpleHeader16[3]['T_ALIGN'] = 'R';
			$aSimpleHeader16[4]['WIDTH'] = 23;
			$aSimpleHeader16[4]['T_ALIGN'] = 'R';
			$aSimpleHeader16[5]['WIDTH'] = 23;
			$aSimpleHeader16[5]['T_ALIGN'] = 'R';
			$aSimpleHeader16[6]['WIDTH'] = 23;
			$aSimpleHeader16[6]['T_ALIGN'] = 'R';
			$aSimpleHeader16[7]['WIDTH'] = 23;
			$aSimpleHeader16[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader17[$i] = $table_default_header_type;
            $aSimpleHeader17[$i]['TEXT'] = $header17[$i];
			$aSimpleHeader17[$i]['WIDTH'] = 24.5;
			$aSimpleHeader17[0]['COLSPAN'] = 9;
            $aSimpleHeader17[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader18[$i] = $table_default_header_type;
            $aSimpleHeader18[$i]['TEXT'] = $header18[$i];
			$aSimpleHeader18[$i]['WIDTH'] = 24.5;
			$aSimpleHeader18[0]['COLSPAN'] = 8;
            $aSimpleHeader18[$i]['LN_SIZE'] = 4;	
			$aSimpleHeader18[$i]['T_SIZE'] = 8;
			
			$aSimpleHeader19[$i] = $table_default_data_type;
            $aSimpleHeader19[$i]['TEXT'] = $header19[$i];
			$aSimpleHeader19[$i]['T_SIZE'] = 8;
			$aSimpleHeader19[$i]['LN_SIZE'] = 4;
			$aSimpleHeader19[0]['WIDTH'] = 35;
			$aSimpleHeader19[0]['T_ALIGN'] = 'L';
			$aSimpleHeader19[1]['WIDTH'] = 23;
			$aSimpleHeader19[1]['T_ALIGN'] = 'R';
			$aSimpleHeader19[2]['WIDTH'] = 23;
			$aSimpleHeader19[2]['T_ALIGN'] = 'R';
			$aSimpleHeader19[3]['WIDTH'] = 23;
			$aSimpleHeader19[3]['T_ALIGN'] = 'R';
			$aSimpleHeader19[4]['WIDTH'] = 23;
			$aSimpleHeader19[4]['T_ALIGN'] = 'R';
			$aSimpleHeader19[5]['WIDTH'] = 23;
			$aSimpleHeader19[5]['T_ALIGN'] = 'R';
			$aSimpleHeader19[6]['WIDTH'] = 23;
			$aSimpleHeader19[6]['T_ALIGN'] = 'R';
			$aSimpleHeader19[7]['WIDTH'] = 23;
			$aSimpleHeader19[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader20[$i] = $table_default_data_type;
            $aSimpleHeader20[$i]['TEXT'] = $header20[$i];
			$aSimpleHeader20[$i]['T_SIZE'] = 8;
			$aSimpleHeader20[$i]['LN_SIZE'] = 4;
			$aSimpleHeader20[0]['WIDTH'] = 35;
			$aSimpleHeader20[0]['T_ALIGN'] = 'L';
			$aSimpleHeader20[1]['WIDTH'] = 23;
			$aSimpleHeader20[1]['T_ALIGN'] = 'R';
			$aSimpleHeader20[2]['WIDTH'] = 23;
			$aSimpleHeader20[2]['T_ALIGN'] = 'R';
			$aSimpleHeader20[3]['WIDTH'] = 23;
			$aSimpleHeader20[3]['T_ALIGN'] = 'R';
			$aSimpleHeader20[4]['WIDTH'] = 23;
			$aSimpleHeader20[4]['T_ALIGN'] = 'R';
			$aSimpleHeader20[5]['WIDTH'] = 23;
			$aSimpleHeader20[5]['T_ALIGN'] = 'R';
			$aSimpleHeader20[6]['WIDTH'] = 23;
			$aSimpleHeader20[6]['T_ALIGN'] = 'R';
			$aSimpleHeader20[7]['WIDTH'] = 23;
			$aSimpleHeader20[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader21[$i] = $table_default_data_type;
            $aSimpleHeader21[$i]['TEXT'] = $header21[$i];
			$aSimpleHeader21[$i]['T_SIZE'] = 8;
			$aSimpleHeader21[$i]['LN_SIZE'] = 4;
			$aSimpleHeader21[0]['WIDTH'] = 35;
			$aSimpleHeader21[0]['T_ALIGN'] = 'L';
			$aSimpleHeader21[1]['WIDTH'] = 23;
			$aSimpleHeader21[1]['T_ALIGN'] = 'R';
			$aSimpleHeader21[2]['WIDTH'] = 23;
			$aSimpleHeader21[2]['T_ALIGN'] = 'R';
			$aSimpleHeader21[3]['WIDTH'] = 23;
			$aSimpleHeader21[3]['T_ALIGN'] = 'R';
			$aSimpleHeader21[4]['WIDTH'] = 23;
			$aSimpleHeader21[4]['T_ALIGN'] = 'R';
			$aSimpleHeader21[5]['WIDTH'] = 23;
			$aSimpleHeader21[5]['T_ALIGN'] = 'R';
			$aSimpleHeader21[6]['WIDTH'] = 23;
			$aSimpleHeader21[6]['T_ALIGN'] = 'R';
			$aSimpleHeader21[7]['WIDTH'] = 23;
			$aSimpleHeader21[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader22[$i] = $table_default_data_type;
            $aSimpleHeader22[$i]['TEXT'] = $header22[$i];
			$aSimpleHeader22[$i]['T_SIZE'] = 8;
			$aSimpleHeader22[$i]['LN_SIZE'] = 4;
			$aSimpleHeader22[0]['WIDTH'] = 35;
			$aSimpleHeader22[0]['T_ALIGN'] = 'L';
			$aSimpleHeader22[1]['WIDTH'] = 23;
			$aSimpleHeader22[1]['T_ALIGN'] = 'R';
			$aSimpleHeader22[2]['WIDTH'] = 23;
			$aSimpleHeader22[2]['T_ALIGN'] = 'R';
			$aSimpleHeader22[3]['WIDTH'] = 23;
			$aSimpleHeader22[3]['T_ALIGN'] = 'R';
			$aSimpleHeader22[4]['WIDTH'] = 23;
			$aSimpleHeader22[4]['T_ALIGN'] = 'R';
			$aSimpleHeader22[5]['WIDTH'] = 23;
			$aSimpleHeader22[5]['T_ALIGN'] = 'R';
			$aSimpleHeader22[6]['WIDTH'] = 23;
			$aSimpleHeader22[6]['T_ALIGN'] = 'R';
			$aSimpleHeader22[7]['WIDTH'] = 23;
			$aSimpleHeader22[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader23[$i] = $table_default_data_type;
            $aSimpleHeader23[$i]['TEXT'] = $header23[$i];
			$aSimpleHeader23[$i]['T_SIZE'] = 8;
			$aSimpleHeader23[$i]['LN_SIZE'] = 4;
			$aSimpleHeader23[0]['WIDTH'] = 35;
			$aSimpleHeader23[0]['T_ALIGN'] = 'L';
			$aSimpleHeader23[1]['WIDTH'] = 23;
			$aSimpleHeader23[1]['T_ALIGN'] = 'R';
			$aSimpleHeader23[2]['WIDTH'] = 23;
			$aSimpleHeader23[2]['T_ALIGN'] = 'R';
			$aSimpleHeader23[3]['WIDTH'] = 23;
			$aSimpleHeader23[3]['T_ALIGN'] = 'R';
			$aSimpleHeader23[4]['WIDTH'] = 23;
			$aSimpleHeader23[4]['T_ALIGN'] = 'R';
			$aSimpleHeader23[5]['WIDTH'] = 23;
			$aSimpleHeader23[5]['T_ALIGN'] = 'R';
			$aSimpleHeader23[6]['WIDTH'] = 23;
			$aSimpleHeader23[6]['T_ALIGN'] = 'R';
			$aSimpleHeader23[7]['WIDTH'] = 23;
			$aSimpleHeader23[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader24[$i] = $table_default_data_type;
            $aSimpleHeader24[$i]['TEXT'] = $header24[$i];
			$aSimpleHeader24[$i]['T_SIZE'] = 8;
			$aSimpleHeader24[$i]['LN_SIZE'] = 4;
			$aSimpleHeader24[0]['WIDTH'] = 35;
			$aSimpleHeader24[0]['T_ALIGN'] = 'L';
			$aSimpleHeader24[1]['WIDTH'] = 23;
			$aSimpleHeader24[1]['T_ALIGN'] = 'R';
			$aSimpleHeader24[2]['WIDTH'] = 23;
			$aSimpleHeader24[2]['T_ALIGN'] = 'R';
			$aSimpleHeader24[3]['WIDTH'] = 23;
			$aSimpleHeader24[3]['T_ALIGN'] = 'R';
			$aSimpleHeader24[4]['WIDTH'] = 23;
			$aSimpleHeader24[4]['T_ALIGN'] = 'R';
			$aSimpleHeader24[5]['WIDTH'] = 23;
			$aSimpleHeader24[5]['T_ALIGN'] = 'R';
			$aSimpleHeader24[6]['WIDTH'] = 23;
			$aSimpleHeader24[6]['T_ALIGN'] = 'R';
			$aSimpleHeader24[7]['WIDTH'] = 23;
			$aSimpleHeader24[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader25[$i] = $table_default_data_type;
            $aSimpleHeader25[$i]['TEXT'] = $header25[$i];
			$aSimpleHeader25[$i]['T_SIZE'] = 8;
			$aSimpleHeader25[$i]['LN_SIZE'] = 4;
			$aSimpleHeader25[0]['WIDTH'] = 35;
			$aSimpleHeader25[0]['T_ALIGN'] = 'L';
			$aSimpleHeader25[1]['WIDTH'] = 23;
			$aSimpleHeader25[1]['T_ALIGN'] = 'R';
			$aSimpleHeader25[2]['WIDTH'] = 23;
			$aSimpleHeader25[2]['T_ALIGN'] = 'R';
			$aSimpleHeader25[3]['WIDTH'] = 23;
			$aSimpleHeader25[3]['T_ALIGN'] = 'R';
			$aSimpleHeader25[4]['WIDTH'] = 23;
			$aSimpleHeader25[4]['T_ALIGN'] = 'R';
			$aSimpleHeader25[5]['WIDTH'] = 23;
			$aSimpleHeader25[5]['T_ALIGN'] = 'R';
			$aSimpleHeader25[6]['WIDTH'] = 23;
			$aSimpleHeader25[6]['T_ALIGN'] = 'R';
			$aSimpleHeader25[7]['WIDTH'] = 23;
			$aSimpleHeader25[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader26[$i] = $table_default_data_type;
            $aSimpleHeader26[$i]['TEXT'] = $header26[$i];
			$aSimpleHeader26[$i]['T_SIZE'] = 8;
			$aSimpleHeader26[$i]['LN_SIZE'] = 4;
			$aSimpleHeader26[0]['WIDTH'] = 35;
			$aSimpleHeader26[0]['T_ALIGN'] = 'L';
			$aSimpleHeader26[1]['WIDTH'] = 23;
			$aSimpleHeader26[1]['T_ALIGN'] = 'R';
			$aSimpleHeader26[2]['WIDTH'] = 23;
			$aSimpleHeader26[2]['T_ALIGN'] = 'R';
			$aSimpleHeader26[3]['WIDTH'] = 23;
			$aSimpleHeader26[3]['T_ALIGN'] = 'R';
			$aSimpleHeader26[4]['WIDTH'] = 23;
			$aSimpleHeader26[4]['T_ALIGN'] = 'R';
			$aSimpleHeader26[5]['WIDTH'] = 23;
			$aSimpleHeader26[5]['T_ALIGN'] = 'R';
			$aSimpleHeader26[6]['WIDTH'] = 23;
			$aSimpleHeader26[6]['T_ALIGN'] = 'R';
			$aSimpleHeader26[7]['WIDTH'] = 23;
			$aSimpleHeader26[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader27[$i] = $table_default_data_type;
            $aSimpleHeader27[$i]['TEXT'] = $header27[$i];
			$aSimpleHeader27[$i]['T_SIZE'] = 8;
			$aSimpleHeader27[$i]['LN_SIZE'] = 4;
			$aSimpleHeader27[0]['WIDTH'] = 35;
			$aSimpleHeader27[0]['T_ALIGN'] = 'L';
			$aSimpleHeader27[1]['WIDTH'] = 23;
			$aSimpleHeader27[1]['T_ALIGN'] = 'R';
			$aSimpleHeader27[2]['WIDTH'] = 23;
			$aSimpleHeader27[2]['T_ALIGN'] = 'R';
			$aSimpleHeader27[3]['WIDTH'] = 23;
			$aSimpleHeader27[3]['T_ALIGN'] = 'R';
			$aSimpleHeader27[4]['WIDTH'] = 23;
			$aSimpleHeader27[4]['T_ALIGN'] = 'R';
			$aSimpleHeader27[5]['WIDTH'] = 23;
			$aSimpleHeader27[5]['T_ALIGN'] = 'R';
			$aSimpleHeader27[6]['WIDTH'] = 23;
			$aSimpleHeader27[6]['T_ALIGN'] = 'R';
			$aSimpleHeader27[7]['WIDTH'] = 23;
			$aSimpleHeader27[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader28[$i] = $table_default_data_type;
            $aSimpleHeader28[$i]['TEXT'] = $header28[$i];
			$aSimpleHeader28[$i]['T_SIZE'] = 8;
			$aSimpleHeader28[$i]['LN_SIZE'] = 4;
			$aSimpleHeader28[0]['WIDTH'] = 35;
			$aSimpleHeader28[0]['T_ALIGN'] = 'L';
			$aSimpleHeader28[1]['WIDTH'] = 23;
			$aSimpleHeader28[1]['T_ALIGN'] = 'R';
			$aSimpleHeader28[2]['WIDTH'] = 23;
			$aSimpleHeader28[2]['T_ALIGN'] = 'R';
			$aSimpleHeader28[3]['WIDTH'] = 23;
			$aSimpleHeader28[3]['T_ALIGN'] = 'R';
			$aSimpleHeader28[4]['WIDTH'] = 23;
			$aSimpleHeader28[4]['T_ALIGN'] = 'R';
			$aSimpleHeader28[5]['WIDTH'] = 23;
			$aSimpleHeader28[5]['T_ALIGN'] = 'R';
			$aSimpleHeader28[6]['WIDTH'] = 23;
			$aSimpleHeader28[6]['T_ALIGN'] = 'R';
			$aSimpleHeader28[7]['WIDTH'] = 23;
			$aSimpleHeader28[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader29[$i] = $table_default_data_type;
            $aSimpleHeader29[$i]['TEXT'] = $header29[$i];
			$aSimpleHeader29[$i]['T_SIZE'] = 8;
			$aSimpleHeader29[$i]['LN_SIZE'] = 4;
			$aSimpleHeader29[0]['WIDTH'] = 35;
			$aSimpleHeader29[0]['T_ALIGN'] = 'L';
			$aSimpleHeader29[1]['WIDTH'] = 23;
			$aSimpleHeader29[1]['T_ALIGN'] = 'R';
			$aSimpleHeader29[2]['WIDTH'] = 23;
			$aSimpleHeader29[2]['T_ALIGN'] = 'R';
			$aSimpleHeader29[3]['WIDTH'] = 23;
			$aSimpleHeader29[3]['T_ALIGN'] = 'R';
			$aSimpleHeader29[4]['WIDTH'] = 23;
			$aSimpleHeader29[4]['T_ALIGN'] = 'R';
			$aSimpleHeader29[5]['WIDTH'] = 23;
			$aSimpleHeader29[5]['T_ALIGN'] = 'R';
			$aSimpleHeader29[6]['WIDTH'] = 23;
			$aSimpleHeader29[6]['T_ALIGN'] = 'R';
			$aSimpleHeader29[7]['WIDTH'] = 23;
			$aSimpleHeader29[7]['T_ALIGN'] = 'R';
			/*
			$aSimpleHeader30[$i] = $table_default_data_type;
            $aSimpleHeader30[$i]['TEXT'] = $header30[$i];
			$aSimpleHeader30[$i]['T_SIZE'] = 8;
			$aSimpleHeader30[$i]['LN_SIZE'] = 4;
			$aSimpleHeader30[0]['WIDTH'] = 35;
			$aSimpleHeader30[0]['T_ALIGN'] = 'L';
			$aSimpleHeader30[1]['WIDTH'] = 23;
			$aSimpleHeader30[1]['T_ALIGN'] = 'R';
			$aSimpleHeader30[2]['WIDTH'] = 23;
			$aSimpleHeader30[2]['T_ALIGN'] = 'R';
			$aSimpleHeader30[3]['WIDTH'] = 23;
			$aSimpleHeader30[3]['T_ALIGN'] = 'R';
			$aSimpleHeader30[4]['WIDTH'] = 23;
			$aSimpleHeader30[4]['T_ALIGN'] = 'R';
			$aSimpleHeader30[5]['WIDTH'] = 23;
			$aSimpleHeader30[5]['T_ALIGN'] = 'R';
			$aSimpleHeader30[6]['WIDTH'] = 23;
			$aSimpleHeader30[6]['T_ALIGN'] = 'R';
			$aSimpleHeader30[7]['WIDTH'] = 23;
			$aSimpleHeader30[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader31[$i] = $table_default_data_type;
            $aSimpleHeader31[$i]['TEXT'] = $header31[$i];
			$aSimpleHeader31[$i]['T_SIZE'] = 8;
			$aSimpleHeader31[$i]['LN_SIZE'] = 4;
			$aSimpleHeader31[0]['WIDTH'] = 35;
			$aSimpleHeader31[0]['T_ALIGN'] = 'L';
			$aSimpleHeader31[1]['WIDTH'] = 23;
			$aSimpleHeader31[1]['T_ALIGN'] = 'R';
			$aSimpleHeader31[2]['WIDTH'] = 23;
			$aSimpleHeader31[2]['T_ALIGN'] = 'R';
			$aSimpleHeader31[3]['WIDTH'] = 23;
			$aSimpleHeader31[3]['T_ALIGN'] = 'R';
			$aSimpleHeader31[4]['WIDTH'] = 23;
			$aSimpleHeader31[4]['T_ALIGN'] = 'R';
			$aSimpleHeader31[5]['WIDTH'] = 23;
			$aSimpleHeader31[5]['T_ALIGN'] = 'R';
			$aSimpleHeader31[6]['WIDTH'] = 23;
			$aSimpleHeader31[6]['T_ALIGN'] = 'R';
			$aSimpleHeader31[7]['WIDTH'] = 23;
			$aSimpleHeader31[7]['T_ALIGN'] = 'R';
			*/
			/*
			$aSimpleHeader32[$i] = $table_default_header_type;
            $aSimpleHeader32[$i]['TEXT'] = $header32[$i];
			$aSimpleHeader32[$i]['WIDTH'] = 24.5;
			$aSimpleHeader32[0]['COLSPAN'] = 8;
            $aSimpleHeader32[$i]['LN_SIZE'] = 1;	
			
			$aSimpleHeader33[$i] = $table_default_header_type;
            $aSimpleHeader33[$i]['TEXT'] = $header33[$i];
			$aSimpleHeader33[$i]['T_SIZE'] = 8;
			$aSimpleHeader33[$i]['LN_SIZE'] = 4;
			$aSimpleHeader33[0]['WIDTH'] = 52;
			$aSimpleHeader33[0]['COLSPAN'] = 2;
			$aSimpleHeader33[2]['WIDTH'] = 23;
			$aSimpleHeader33[3]['WIDTH'] = 23;
			$aSimpleHeader33[4]['WIDTH'] = 23;
			//$aSimpleHeader33[5]['WIDTH'] = 23;
			$aSimpleHeader33[5]['WIDTH'] = 57;
			$aSimpleHeader33[5]['COLSPAN'] = 3;	
			$aSimpleHeader33[5]['ROWSPAN'] = 3;
			
            $aSimpleHeader331[$i] = $table_default_header_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader331[$i]['TEXT'] = $header331[$i];
			$aSimpleHeader331[$i]['T_SIZE'] = 8;
			$aSimpleHeader331[$i]['LN_SIZE'] = 4;
			$aSimpleHeader331[0]['WIDTH'] = 52; // change to 52
			$aSimpleHeader331[0]['T_ALIGN'] = 'L';
			$aSimpleHeader331[0]['COLSPAN'] = 2;
			$aSimpleHeader331[2]['WIDTH'] = 23;
			$aSimpleHeader331[2]['T_ALIGN'] = 'R';
			$aSimpleHeader331[3]['WIDTH'] = 23;
			$aSimpleHeader331[3]['T_ALIGN'] = 'R';
			$aSimpleHeader331[4]['WIDTH'] = 23;
			$aSimpleHeader331[4]['T_ALIGN'] = 'R';
			$aSimpleHeader331[5]['WIDTH'] = 23;
			$aSimpleHeader331[5]['T_ALIGN'] = 'R';
			//$aSimpleHeader331[6]['WIDTH'] = 40;
			//$aSimpleHeader331[6]['COLSPAN'] = 2;	
			
			$aSimpleHeader332[$i] = $table_default_header_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader332[$i]['TEXT'] = $header332[$i];
			$aSimpleHeader332[$i]['T_SIZE'] = 8;
			$aSimpleHeader332[$i]['LN_SIZE'] = 4;
			$aSimpleHeader332[0]['WIDTH'] = 52; //change to 58
			$aSimpleHeader332[0]['COLSPAN'] = 2;
			$aSimpleHeader332[0]['T_ALIGN'] = 'L';
			$aSimpleHeader332[2]['WIDTH'] = 23;
			$aSimpleHeader332[2]['T_ALIGN'] = 'R';
			$aSimpleHeader332[3]['WIDTH'] = 23;
			$aSimpleHeader332[3]['T_ALIGN'] = 'R';
			$aSimpleHeader332[4]['WIDTH'] = 23;
			$aSimpleHeader332[4]['T_ALIGN'] = 'R';
			$aSimpleHeader332[5]['WIDTH'] = 23;
			$aSimpleHeader332[5]['T_ALIGN'] = 'R';
			//$aSimpleHeader332[6]['WIDTH'] = 40;
			//$aSimpleHeader332[6]['COLSPAN'] = 2;
			
			$aSimpleHeader333[$i] = $table_default_header_type;
            $aSimpleHeader333[$i]['TEXT'] = $header333[$i];
			$aSimpleHeader333[$i]['WIDTH'] = 24.5;
			$aSimpleHeader333[0]['COLSPAN'] = 8;
            $aSimpleHeader333[$i]['LN_SIZE'] = 1;	
			$aSimpleHeader333[$i]['T_SIZE'] = 8;
			
			$aSimpleHeader334[$i] = $table_default_header_type;
            $aSimpleHeader334[$i]['TEXT'] = $header334[$i];
			//$aSimpleHeader334[$i]['WIDTH'] = 24.5;
			$aSimpleHeader334[0]['COLSPAN'] = 5;
			$aSimpleHeader334[0]['WIDTH'] = 103;
			$aSimpleHeader334[5]['WIDTH'] = 23;
			$aSimpleHeader334[6]['WIDTH'] = 23;
			$aSimpleHeader334[7]['WIDTH'] = 23;
            $aSimpleHeader334[$i]['LN_SIZE'] = 4;	
			$aSimpleHeader334[$i]['T_SIZE'] = 8;
						
			$aSimpleHeader335[$i] = $table_default_data_type;
            $aSimpleHeader335[$i]['TEXT'] = $header335[$i];
			$aSimpleHeader335[$i]['T_SIZE'] = 8;
			$aSimpleHeader335[$i]['LN_SIZE'] = 4;
			$aSimpleHeader335[0]['WIDTH'] = 35;
			$aSimpleHeader335[0]['T_ALIGN'] = 'C';
			$aSimpleHeader335[1]['WIDTH'] = 23;
			$aSimpleHeader335[1]['T_ALIGN'] = 'C';
			$aSimpleHeader335[2]['WIDTH'] = 23;
			$aSimpleHeader335[2]['T_ALIGN'] = 'C';
			$aSimpleHeader335[3]['WIDTH'] = 23;
			$aSimpleHeader335[3]['T_ALIGN'] = 'C';
			$aSimpleHeader335[4]['WIDTH'] = 23;
			$aSimpleHeader335[4]['T_ALIGN'] = 'C';
			$aSimpleHeader335[5]['WIDTH'] = 23;
			$aSimpleHeader335[5]['T_ALIGN'] = 'R';
			$aSimpleHeader335[6]['WIDTH'] = 23;
			$aSimpleHeader335[6]['T_ALIGN'] = 'R';
			$aSimpleHeader335[7]['WIDTH'] = 23;
			$aSimpleHeader335[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader336[$i] = $table_default_data_type;
            $aSimpleHeader336[$i]['TEXT'] = $header336[$i];
			$aSimpleHeader336[$i]['T_SIZE'] = 8;
			$aSimpleHeader336[$i]['LN_SIZE'] = 4;
			$aSimpleHeader336[0]['WIDTH'] = 35;
			$aSimpleHeader336[0]['T_ALIGN'] = 'R';
			$aSimpleHeader336[1]['WIDTH'] = 23;
			$aSimpleHeader336[1]['T_ALIGN'] = 'R';
			$aSimpleHeader336[2]['WIDTH'] = 23;
			$aSimpleHeader336[2]['T_ALIGN'] = 'R';
			$aSimpleHeader336[3]['WIDTH'] = 23;
			$aSimpleHeader336[3]['T_ALIGN'] = 'R';
			$aSimpleHeader336[4]['WIDTH'] = 23;
			$aSimpleHeader336[4]['T_ALIGN'] = 'R';
			$aSimpleHeader336[5]['WIDTH'] = 23;
			$aSimpleHeader336[5]['T_ALIGN'] = 'R';
			$aSimpleHeader336[6]['WIDTH'] = 23;
			$aSimpleHeader336[6]['T_ALIGN'] = 'R';
			$aSimpleHeader336[7]['WIDTH'] = 23;
			$aSimpleHeader336[7]['T_ALIGN'] = 'R';
					
			$aSimpleHeader34[$i] = $table_default_header_type;
            $aSimpleHeader34[$i]['TEXT'] = $header34[$i];
			$aSimpleHeader34[$i]['WIDTH'] = 24.5;
			$aSimpleHeader34[0]['COLSPAN'] = 8;
            $aSimpleHeader34[$i]['LN_SIZE'] = 1;	
			
			$aSimpleHeader35[$i] = $table_default_header_type;
            $aSimpleHeader35[$i]['TEXT'] = $header35[$i];
			$aSimpleHeader35[$i]['T_SIZE'] = 8;
			$aSimpleHeader35[$i]['LN_SIZE'] = 4;
			$aSimpleHeader35[0]['WIDTH'] = 35;
			$aSimpleHeader35[1]['WIDTH'] = 23;
			$aSimpleHeader35[2]['WIDTH'] = 23;
			$aSimpleHeader35[3]['WIDTH'] = 23;
			$aSimpleHeader35[4]['WIDTH'] = 23;
			$aSimpleHeader35[5]['WIDTH'] = 23;
			$aSimpleHeader35[6]['WIDTH'] = 23;
			$aSimpleHeader35[7]['WIDTH'] = 23;
			
			$aSimpleHeader351[$i] = $table_default_data_type;
            $aSimpleHeader351[$i]['TEXT'] = $header351[$i];
			$aSimpleHeader351[$i]['T_SIZE'] = 8;
			$aSimpleHeader351[$i]['LN_SIZE'] = 4;
			$aSimpleHeader351[0]['WIDTH'] = 35;
			$aSimpleHeader351[0]['T_ALIGN'] = 'L';
			$aSimpleHeader351[1]['WIDTH'] = 23;
			$aSimpleHeader351[1]['T_ALIGN'] = 'R';
			$aSimpleHeader351[2]['WIDTH'] = 23;
			$aSimpleHeader351[2]['T_ALIGN'] = 'R';
			$aSimpleHeader351[3]['WIDTH'] = 23;
			$aSimpleHeader351[3]['T_ALIGN'] = 'R';
			$aSimpleHeader351[4]['WIDTH'] = 23;
			$aSimpleHeader351[4]['T_ALIGN'] = 'R';
			$aSimpleHeader351[5]['WIDTH'] = 23;
			$aSimpleHeader351[5]['T_ALIGN'] = 'R';
			$aSimpleHeader351[6]['WIDTH'] = 23;
			$aSimpleHeader351[6]['T_ALIGN'] = 'R';
			$aSimpleHeader351[7]['WIDTH'] = 23;
			$aSimpleHeader351[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader352[$i] = $table_default_data_type;
            $aSimpleHeader352[$i]['TEXT'] = $header352[$i];
			$aSimpleHeader352[$i]['T_SIZE'] = 8;
			$aSimpleHeader352[$i]['LN_SIZE'] = 4;
			$aSimpleHeader352[0]['WIDTH'] = 35;
			$aSimpleHeader352[0]['T_ALIGN'] = 'L';
			$aSimpleHeader352[1]['WIDTH'] = 23;
			$aSimpleHeader352[1]['T_ALIGN'] = 'R';
			$aSimpleHeader352[2]['WIDTH'] = 23;
			$aSimpleHeader352[2]['T_ALIGN'] = 'R';
			$aSimpleHeader352[3]['WIDTH'] = 23;
			$aSimpleHeader352[3]['T_ALIGN'] = 'R';
			$aSimpleHeader352[4]['WIDTH'] = 23;
			$aSimpleHeader352[4]['T_ALIGN'] = 'R';
			$aSimpleHeader352[5]['WIDTH'] = 23;
			$aSimpleHeader352[5]['T_ALIGN'] = 'R';
			$aSimpleHeader352[6]['WIDTH'] = 23;
			$aSimpleHeader352[6]['T_ALIGN'] = 'R';
			$aSimpleHeader352[7]['WIDTH'] = 23;
			$aSimpleHeader352[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader353[$i] = $table_default_data_type;
            $aSimpleHeader353[$i]['TEXT'] = $header353[$i];
			$aSimpleHeader353[$i]['T_SIZE'] = 8;
			$aSimpleHeader353[$i]['LN_SIZE'] = 4;
			$aSimpleHeader353[0]['WIDTH'] = 35;
			$aSimpleHeader353[0]['T_ALIGN'] = 'L';
			$aSimpleHeader353[1]['WIDTH'] = 23;
			$aSimpleHeader353[1]['T_ALIGN'] = 'R';
			$aSimpleHeader353[2]['WIDTH'] = 23;
			$aSimpleHeader353[2]['T_ALIGN'] = 'R';
			$aSimpleHeader353[3]['WIDTH'] = 23;
			$aSimpleHeader353[3]['T_ALIGN'] = 'R';
			$aSimpleHeader353[4]['WIDTH'] = 23;
			$aSimpleHeader353[4]['T_ALIGN'] = 'R';
			$aSimpleHeader353[5]['WIDTH'] = 23;
			$aSimpleHeader353[5]['T_ALIGN'] = 'R';
			$aSimpleHeader353[6]['WIDTH'] = 23;
			$aSimpleHeader353[6]['T_ALIGN'] = 'R';
			$aSimpleHeader353[7]['WIDTH'] = 23;
			$aSimpleHeader353[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader354[$i] = $table_default_data_type;
            $aSimpleHeader354[$i]['TEXT'] = $header354[$i];
			$aSimpleHeader354[$i]['T_SIZE'] = 8;
			$aSimpleHeader354[$i]['LN_SIZE'] = 4;
			$aSimpleHeader354[0]['WIDTH'] = 35;
			$aSimpleHeader354[0]['T_ALIGN'] = 'L';
			$aSimpleHeader354[1]['WIDTH'] = 23;
			$aSimpleHeader354[1]['T_ALIGN'] = 'R';
			$aSimpleHeader354[2]['WIDTH'] = 23;
			$aSimpleHeader354[2]['T_ALIGN'] = 'R';
			$aSimpleHeader354[3]['WIDTH'] = 23;
			$aSimpleHeader354[3]['T_ALIGN'] = 'R';
			$aSimpleHeader354[4]['WIDTH'] = 23;
			$aSimpleHeader354[4]['T_ALIGN'] = 'R';
			$aSimpleHeader354[5]['WIDTH'] = 23;
			$aSimpleHeader354[5]['T_ALIGN'] = 'R';
			$aSimpleHeader354[6]['WIDTH'] = 23;
			$aSimpleHeader354[6]['T_ALIGN'] = 'R';
			$aSimpleHeader354[7]['WIDTH'] = 23;
			$aSimpleHeader354[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader355[$i] = $table_default_data_type;
            $aSimpleHeader355[$i]['TEXT'] = $header355[$i];
			$aSimpleHeader355[$i]['T_SIZE'] = 8;
			$aSimpleHeader355[$i]['LN_SIZE'] = 4;
			$aSimpleHeader355[0]['WIDTH'] = 35;
			$aSimpleHeader355[0]['T_ALIGN'] = 'L';
			$aSimpleHeader355[1]['WIDTH'] = 23;
			$aSimpleHeader355[1]['T_ALIGN'] = 'R';
			$aSimpleHeader355[2]['WIDTH'] = 23;
			$aSimpleHeader355[2]['T_ALIGN'] = 'R';
			$aSimpleHeader355[3]['WIDTH'] = 23;
			$aSimpleHeader355[3]['T_ALIGN'] = 'R';
			$aSimpleHeader355[4]['WIDTH'] = 23;
			$aSimpleHeader355[4]['T_ALIGN'] = 'R';
			$aSimpleHeader355[5]['WIDTH'] = 23;
			$aSimpleHeader355[5]['T_ALIGN'] = 'R';
			$aSimpleHeader355[6]['WIDTH'] = 23;
			$aSimpleHeader355[6]['T_ALIGN'] = 'R';
			$aSimpleHeader355[7]['WIDTH'] = 23;
			$aSimpleHeader354[7]['T_ALIGN'] = 'R';
			/*
			$aSimpleHeader356[$i] = $table_default_data_type;
            $aSimpleHeader356[$i]['TEXT'] = $header356[$i];
			$aSimpleHeader356[$i]['T_SIZE'] = 8;
			$aSimpleHeader356[$i]['LN_SIZE'] = 4;
			$aSimpleHeader356[0]['WIDTH'] = 35;
			$aSimpleHeader356[0]['T_ALIGN'] = 'L';
			$aSimpleHeader356[1]['WIDTH'] = 23;
			$aSimpleHeader356[1]['T_ALIGN'] = 'R';
			$aSimpleHeader356[2]['WIDTH'] = 23;
			$aSimpleHeader356[2]['T_ALIGN'] = 'R';
			$aSimpleHeader356[3]['WIDTH'] = 23;
			$aSimpleHeader356[3]['T_ALIGN'] = 'R';
			$aSimpleHeader356[4]['WIDTH'] = 23;
			$aSimpleHeader356[4]['T_ALIGN'] = 'R';
			$aSimpleHeader356[5]['WIDTH'] = 23;
			$aSimpleHeader356[5]['T_ALIGN'] = 'R';
			$aSimpleHeader356[6]['WIDTH'] = 23;
			$aSimpleHeader356[6]['T_ALIGN'] = 'R';
			$aSimpleHeader356[7]['WIDTH'] = 23;
			$aSimpleHeader356[7]['T_ALIGN'] = 'R';
			*/
			/*
			$aSimpleHeader36[$i] = $table_default_header_type;
            $aSimpleHeader36[$i]['TEXT'] = $header36[$i];
			$aSimpleHeader36[$i]['WIDTH'] = 24.5;
			$aSimpleHeader36[0]['COLSPAN'] = 8;
            $aSimpleHeader36[$i]['LN_SIZE'] = 1;	
			
			$aSimpleHeader37[$i] = $table_default_header_type;
            $aSimpleHeader37[$i]['TEXT'] = $header37[$i];
			$aSimpleHeader37[$i]['T_SIZE'] = 8;
			$aSimpleHeader37[$i]['LN_SIZE'] = 4;
			$aSimpleHeader37[0]['WIDTH'] = 35;
			$aSimpleHeader37[1]['WIDTH'] = 23;
			$aSimpleHeader37[2]['WIDTH'] = 23;
			$aSimpleHeader37[3]['WIDTH'] = 23;
			$aSimpleHeader37[4]['WIDTH'] = 23;
			$aSimpleHeader37[5]['WIDTH'] = 23;
			$aSimpleHeader37[6]['WIDTH'] = 23;
			$aSimpleHeader37[7]['WIDTH'] = 23;
			
			$aSimpleHeader371[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader371[$i]['TEXT'] = $header371[$i];
			$aSimpleHeader371[$i]['T_SIZE'] = 8;
			$aSimpleHeader371[$i]['LN_SIZE'] = 4;
			$aSimpleHeader371[0]['WIDTH'] = 35;
			$aSimpleHeader371[0]['T_ALIGN'] = 'L';
			$aSimpleHeader371[1]['WIDTH'] = 23;
			$aSimpleHeader371[1]['T_ALIGN'] = 'R';
			$aSimpleHeader371[2]['WIDTH'] = 23;
			$aSimpleHeader371[2]['T_ALIGN'] = 'R';
			$aSimpleHeader371[3]['WIDTH'] = 23;
			$aSimpleHeader371[3]['T_ALIGN'] = 'R';
			$aSimpleHeader371[4]['WIDTH'] = 23;
			$aSimpleHeader371[4]['T_ALIGN'] = 'R';
			$aSimpleHeader371[5]['WIDTH'] = 23;
			$aSimpleHeader371[5]['T_ALIGN'] = 'R';
			$aSimpleHeader371[6]['WIDTH'] = 23;	
			$aSimpleHeader371[6]['T_ALIGN'] = 'R';
			$aSimpleHeader371[7]['WIDTH'] = 23;	
			$aSimpleHeader371[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader3711[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader3711[$i]['TEXT'] = $header3711[$i];
			$aSimpleHeader3711[$i]['T_SIZE'] = 8;
			$aSimpleHeader3711[$i]['LN_SIZE'] = 4;
			$aSimpleHeader3711[0]['WIDTH'] = 35;
			$aSimpleHeader3711[0]['T_ALIGN'] = 'L';
			$aSimpleHeader3711[1]['WIDTH'] = 23;
			$aSimpleHeader3711[1]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[2]['WIDTH'] = 23;
			$aSimpleHeader3711[2]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[3]['WIDTH'] = 23;
			$aSimpleHeader3711[3]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[4]['WIDTH'] = 23;
			$aSimpleHeader3711[4]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[5]['WIDTH'] = 23;
			$aSimpleHeader3711[5]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[6]['WIDTH'] = 23;	
			$aSimpleHeader3711[6]['T_ALIGN'] = 'R';
			$aSimpleHeader3711[7]['WIDTH'] = 23;	
			$aSimpleHeader3711[7]['T_ALIGN'] = 'R';
			
			
			$aSimpleHeader3712[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader3712[$i]['TEXT'] = $header3712[$i];
			$aSimpleHeader3712[$i]['T_SIZE'] = 8;
			$aSimpleHeader3712[$i]['LN_SIZE'] = 4;
			$aSimpleHeader3712[0]['WIDTH'] = 35;
			$aSimpleHeader3712[0]['T_ALIGN'] = 'L';
			$aSimpleHeader3712[1]['WIDTH'] = 23;
			$aSimpleHeader3712[1]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[2]['WIDTH'] = 23;
			$aSimpleHeader3712[2]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[3]['WIDTH'] = 23;
			$aSimpleHeader3712[3]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[4]['WIDTH'] = 23;
			$aSimpleHeader3712[4]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[5]['WIDTH'] = 23;
			$aSimpleHeader3712[5]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[6]['WIDTH'] = 23;	
			$aSimpleHeader3712[6]['T_ALIGN'] = 'R';
			$aSimpleHeader3712[7]['WIDTH'] = 23;	
			$aSimpleHeader3712[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader372[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader372[$i]['TEXT'] = $header372[$i];
			$aSimpleHeader372[$i]['T_SIZE'] = 8;
			$aSimpleHeader372[$i]['LN_SIZE'] = 4;
			$aSimpleHeader372[0]['WIDTH'] = 35;
			$aSimpleHeader372[0]['T_ALIGN'] = 'L';
			$aSimpleHeader372[1]['WIDTH'] = 23;
			$aSimpleHeader372[1]['T_ALIGN'] = 'R';
			$aSimpleHeader372[2]['WIDTH'] = 23;
			$aSimpleHeader372[2]['T_ALIGN'] = 'R';
			$aSimpleHeader372[3]['WIDTH'] = 23;
			$aSimpleHeader372[3]['T_ALIGN'] = 'R';
			$aSimpleHeader372[4]['WIDTH'] = 23;
			$aSimpleHeader372[4]['T_ALIGN'] = 'R';
			$aSimpleHeader372[5]['WIDTH'] = 23;
			$aSimpleHeader372[5]['T_ALIGN'] = 'R';
			$aSimpleHeader372[6]['WIDTH'] = 23;	
			$aSimpleHeader372[6]['T_ALIGN'] = 'R';
			$aSimpleHeader372[7]['WIDTH'] = 23;	
			$aSimpleHeader372[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader3721[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader3721[$i]['TEXT'] = $header3721[$i];
			$aSimpleHeader3721[$i]['T_SIZE'] = 8;
			$aSimpleHeader3721[$i]['LN_SIZE'] = 4;
			$aSimpleHeader3721[0]['WIDTH'] = 35;
			$aSimpleHeader3721[0]['T_ALIGN'] = 'L';
			$aSimpleHeader3721[1]['WIDTH'] = 23;
			$aSimpleHeader3721[1]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[2]['WIDTH'] = 23;
			$aSimpleHeader3721[2]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[3]['WIDTH'] = 23;
			$aSimpleHeader3721[3]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[4]['WIDTH'] = 23;
			$aSimpleHeader3721[4]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[5]['WIDTH'] = 23;
			$aSimpleHeader3721[5]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[6]['WIDTH'] = 23;	
			$aSimpleHeader3721[6]['T_ALIGN'] = 'R';
			$aSimpleHeader3721[7]['WIDTH'] = 23;	
			$aSimpleHeader3721[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader3722[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader3722[$i]['TEXT'] = $header3722[$i];
			$aSimpleHeader3722[$i]['T_SIZE'] = 8;
			$aSimpleHeader3722[$i]['LN_SIZE'] = 4;
			$aSimpleHeader3722[0]['WIDTH'] = 35;
			$aSimpleHeader3722[0]['T_ALIGN'] = 'L';
			$aSimpleHeader3722[1]['WIDTH'] = 23;
			$aSimpleHeader3722[1]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[2]['WIDTH'] = 23;
			$aSimpleHeader3722[2]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[3]['WIDTH'] = 23;
			$aSimpleHeader3722[3]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[4]['WIDTH'] = 23;
			$aSimpleHeader3722[4]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[5]['WIDTH'] = 23;
			$aSimpleHeader3722[5]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[6]['WIDTH'] = 23;	
			$aSimpleHeader3722[6]['T_ALIGN'] = 'R';
			$aSimpleHeader3722[7]['WIDTH'] = 23;	
			$aSimpleHeader3722[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader373[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader373[$i]['TEXT'] = $header373[$i];
			$aSimpleHeader373[$i]['T_SIZE'] = 8;
			$aSimpleHeader373[$i]['LN_SIZE'] = 4;
			$aSimpleHeader373[0]['WIDTH'] = 35;
			$aSimpleHeader373[0]['T_ALIGN'] = 'L';
			$aSimpleHeader373[1]['WIDTH'] = 23;
			$aSimpleHeader373[1]['T_ALIGN'] = 'R';
			$aSimpleHeader373[2]['WIDTH'] = 23;
			$aSimpleHeader373[2]['T_ALIGN'] = 'R';
			$aSimpleHeader373[3]['WIDTH'] = 23;
			$aSimpleHeader373[3]['T_ALIGN'] = 'R';
			$aSimpleHeader373[4]['WIDTH'] = 23;
			$aSimpleHeader373[4]['T_ALIGN'] = 'R';
			$aSimpleHeader373[5]['WIDTH'] = 23;
			$aSimpleHeader373[5]['T_ALIGN'] = 'R';
			$aSimpleHeader373[6]['WIDTH'] = 23;
			$aSimpleHeader374[6]['T_ALIGN'] = 'R';
			$aSimpleHeader373[7]['WIDTH'] = 23;	
			$aSimpleHeader373[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader374[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader374[$i]['TEXT'] = $header374[$i];
			$aSimpleHeader374[$i]['T_SIZE'] = 8;
			$aSimpleHeader374[$i]['LN_SIZE'] = 4;
			$aSimpleHeader374[0]['WIDTH'] = 35;
			$aSimpleHeader374[0]['T_ALIGN'] = 'L';
			$aSimpleHeader374[1]['WIDTH'] = 23;
			$aSimpleHeader374[1]['T_ALIGN'] = 'R';
			$aSimpleHeader374[2]['WIDTH'] = 23;
			$aSimpleHeader374[2]['T_ALIGN'] = 'R';
			$aSimpleHeader374[3]['WIDTH'] = 23;
			$aSimpleHeader374[3]['T_ALIGN'] = 'R';
			$aSimpleHeader374[4]['WIDTH'] = 23;
			$aSimpleHeader374[4]['T_ALIGN'] = 'R';
			$aSimpleHeader374[5]['WIDTH'] = 23;
			$aSimpleHeader374[5]['T_ALIGN'] = 'R';
			$aSimpleHeader374[6]['WIDTH'] = 23;
			$aSimpleHeader374[6]['T_ALIGN'] = 'R';
			$aSimpleHeader374[7]['WIDTH'] = 23;	
			$aSimpleHeader374[7]['T_ALIGN'] = 'R';
			
			$aSimpleHeader375[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader375[$i]['TEXT'] = $header375[$i];
			$aSimpleHeader375[$i]['T_SIZE'] = 8;
			$aSimpleHeader375[$i]['LN_SIZE'] = 4;
			$aSimpleHeader375[0]['WIDTH'] = 35;
			$aSimpleHeader375[0]['T_ALIGN'] = 'L';
			$aSimpleHeader375[1]['WIDTH'] = 23;
			$aSimpleHeader375[1]['T_ALIGN'] = 'R';
			$aSimpleHeader375[2]['WIDTH'] = 23;
			$aSimpleHeader375[2]['T_ALIGN'] = 'R';
			$aSimpleHeader375[3]['WIDTH'] = 23;
			$aSimpleHeader375[3]['T_ALIGN'] = 'R';
			$aSimpleHeader375[4]['WIDTH'] = 23;
			$aSimpleHeader375[4]['T_ALIGN'] = 'R';
			$aSimpleHeader375[5]['WIDTH'] = 23;
			$aSimpleHeader375[5]['T_ALIGN'] = 'R';
			$aSimpleHeader375[6]['WIDTH'] = 23;
			$aSimpleHeader375[6]['T_ALIGN'] = 'R';
			$aSimpleHeader375[7]['WIDTH'] = 23;	
			$aSimpleHeader375[7]['T_ALIGN'] = 'R';
			/*
			$aSimpleHeader376[$i] = $table_default_data_type; // ini kenapa g bs dirubah $table_default_data_type
            $aSimpleHeader376[$i]['TEXT'] = $header376[$i];
			$aSimpleHeader376[$i]['T_SIZE'] = 8;
			$aSimpleHeader376[$i]['LN_SIZE'] = 4;
			$aSimpleHeader376[0]['WIDTH'] = 35;
			$aSimpleHeader376[0]['T_ALIGN'] = 'L';
			$aSimpleHeader376[1]['WIDTH'] = 23;
			$aSimpleHeader376[1]['T_ALIGN'] = 'R';
			$aSimpleHeader376[2]['WIDTH'] = 23;
			$aSimpleHeader376[2]['T_ALIGN'] = 'R';
			$aSimpleHeader376[3]['WIDTH'] = 23;
			$aSimpleHeader376[3]['T_ALIGN'] = 'R';
			$aSimpleHeader376[4]['WIDTH'] = 23;
			$aSimpleHeader376[4]['T_ALIGN'] = 'R';
			$aSimpleHeader376[5]['WIDTH'] = 23;
			$aSimpleHeader376[5]['T_ALIGN'] = 'R';
			$aSimpleHeader376[6]['WIDTH'] = 23;	
			$aSimpleHeader376[6]['T_ALIGN'] = 'R';
			$aSimpleHeader376[7]['WIDTH'] = 23;	
			$aSimpleHeader376[7]['T_ALIGN'] = 'R';
			*/
			/*
			$aSimpleHeader38[$i] = $table_default_header_type;
            $aSimpleHeader38[$i]['TEXT'] = $header38[$i];
			$aSimpleHeader38[$i]['WIDTH'] = 24.5;
			$aSimpleHeader38[0]['COLSPAN'] = 8;
            $aSimpleHeader38[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader39[$i] = $table_default_header_type;
            $aSimpleHeader39[$i]['TEXT'] = $header39[$i];
			$aSimpleHeader39[$i]['T_SIZE'] = 8;
			$aSimpleHeader39[$i]['LN_SIZE'] = 4;
			$aSimpleHeader39[0]['WIDTH'] = 35;
			$aSimpleHeader39[0]['T_ALIGN'] = 'L';
			$aSimpleHeader39[1]['WIDTH'] = 125;
			$aSimpleHeader39[1]['T_ALIGN'] = 'R';
			$aSimpleHeader39[1]['COLSPAN'] = 7;
			
			
			$aSimpleHeader40[$i] = $table_default_header_type;
            $aSimpleHeader40[$i]['TEXT'] = $header40[$i];
			$aSimpleHeader40[$i]['WIDTH'] = 24.5;
			$aSimpleHeader40[0]['COLSPAN'] = 8;
            $aSimpleHeader40[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader41[$i] = $table_default_header_type;
            $aSimpleHeader41[$i]['TEXT'] = $header41[$i];
			$aSimpleHeader41[$i]['T_SIZE'] = 8;
			$aSimpleHeader41[$i]['LN_SIZE'] = 4;
			$aSimpleHeader41[0]['WIDTH'] = 35;
			$aSimpleHeader41[1]['WIDTH'] = 23;
			$aSimpleHeader41[2]['WIDTH'] = 23;
			$aSimpleHeader41[3]['WIDTH'] = 23;
			$aSimpleHeader41[4]['WIDTH'] = 23;
			$aSimpleHeader41[5]['COLSPAN'] = 3;
			$aSimpleHeader41[5]['WIDTH'] = 57;
			/*
			$aSimpleHeader42[$i] = $table_default_header_type;
            $aSimpleHeader42[$i]['TEXT'] = $header42[$i];
			$aSimpleHeader42[$i]['WIDTH'] = 24.5;
			$aSimpleHeader42[0]['COLSPAN'] = 8;
            $aSimpleHeader42[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader43[$i] = $table_default_header_type;
            $aSimpleHeader43[$i]['TEXT'] = $header43[$i];
			$aSimpleHeader43[$i]['WIDTH'] = 24.5;
			$aSimpleHeader43[0]['COLSPAN'] = 8;
            $aSimpleHeader43[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader44[$i] = $table_default_header_type;
            $aSimpleHeader44[$i]['TEXT'] = $header44[$i];
			$aSimpleHeader44[$i]['WIDTH'] = 24.5;
			$aSimpleHeader44[0]['COLSPAN'] = 8;
            $aSimpleHeader44[$i]['LN_SIZE'] = 1;
			
			$aSimpleHeader45[$i] = $table_default_header_type;
            $aSimpleHeader45[$i]['TEXT'] = $header45[$i];
			$aSimpleHeader45[$i]['WIDTH'] = 24.5;
			$aSimpleHeader45[0]['COLSPAN'] = 8;
            $aSimpleHeader45[$i]['LN_SIZE'] = 1;
			*/
			/*
		}
		        			
        $pdf->tbSetHeaderType($aSimpleHeader);
		$pdf->tbSetHeaderType($aSimpleHeader1);
		$pdf->tbSetHeaderType($aSimpleHeader2);
		$pdf->tbSetHeaderType($aSimpleHeader3);
		$pdf->tbSetHeaderType($aSimpleHeader4);
		$pdf->tbSetHeaderType($aSimpleHeader5);
		$pdf->tbSetHeaderType($aSimpleHeader6);
		$pdf->tbSetHeaderType($aSimpleHeader7);
		$pdf->tbSetHeaderType($aSimpleHeader71);
		$pdf->tbSetHeaderType($aSimpleHeader8);
		$pdf->tbSetHeaderType($aSimpleHeader81);
		$pdf->tbSetHeaderType($aSimpleHeader82);
		$pdf->tbSetHeaderType($aSimpleHeader9);
		$pdf->tbSetHeaderType($aSimpleHeader10);
		$pdf->tbSetHeaderType($aSimpleHeader11);
		$pdf->tbSetHeaderType($aSimpleHeader12);
		$pdf->tbSetHeaderType($aSimpleHeader13);
		$pdf->tbSetHeaderType($aSimpleHeader14);
		$pdf->tbSetHeaderType($aSimpleHeader15);
		$pdf->tbSetHeaderType($aSimpleHeader16);
		$pdf->tbSetHeaderType($aSimpleHeader17);
		$pdf->tbSetHeaderType($aSimpleHeader18);
		$pdf->tbSetHeaderType($aSimpleHeader19);
		$pdf->tbSetHeaderType($aSimpleHeader20);
		$pdf->tbSetHeaderType($aSimpleHeader21);
		$pdf->tbSetHeaderType($aSimpleHeader22);
		$pdf->tbSetHeaderType($aSimpleHeader23);
		$pdf->tbSetHeaderType($aSimpleHeader24);
		$pdf->tbSetHeaderType($aSimpleHeader25);
		$pdf->tbSetHeaderType($aSimpleHeader26);
		$pdf->tbSetHeaderType($aSimpleHeader27);
		$pdf->tbSetHeaderType($aSimpleHeader28);
		$pdf->tbSetHeaderType($aSimpleHeader29);
		//$pdf->tbSetHeaderType($aSimpleHeader30);
		//$pdf->tbSetHeaderType($aSimpleHeader31);		
		$pdf->tbSetHeaderType($aSimpleHeader32);
		$pdf->tbSetHeaderType($aSimpleHeader33);
		$pdf->tbSetHeaderType($aSimpleHeader331);		
		$pdf->tbSetHeaderType($aSimpleHeader332);
		$pdf->tbSetHeaderType($aSimpleHeader333);
		$pdf->tbSetHeaderType($aSimpleHeader334);
		$pdf->tbSetHeaderType($aSimpleHeader335);
		$pdf->tbSetHeaderType($aSimpleHeader336);
		$pdf->tbSetHeaderType($aSimpleHeader34);		
		$pdf->tbSetHeaderType($aSimpleHeader35);		
		$pdf->tbSetHeaderType($aSimpleHeader351);
		$pdf->tbSetHeaderType($aSimpleHeader352);		
		$pdf->tbSetHeaderType($aSimpleHeader353);
		$pdf->tbSetHeaderType($aSimpleHeader354);
		$pdf->tbSetHeaderType($aSimpleHeader355);
		//$pdf->tbSetHeaderType($aSimpleHeader356);		
		$pdf->tbSetHeaderType($aSimpleHeader36);		
		$pdf->tbSetHeaderType($aSimpleHeader37);		
		$pdf->tbSetHeaderType($aSimpleHeader371);		
		$pdf->tbSetHeaderType($aSimpleHeader3711);
		$pdf->tbSetHeaderType($aSimpleHeader3712);		
		$pdf->tbSetHeaderType($aSimpleHeader372);
		$pdf->tbSetHeaderType($aSimpleHeader3721);

		$pdf->tbSetHeaderType($aSimpleHeader3722);	

		$pdf->tbSetHeaderType($aSimpleHeader373);
		$pdf->tbSetHeaderType($aSimpleHeader374);
		$pdf->tbSetHeaderType($aSimpleHeader375);
		//$pdf->tbSetHeaderType($aSimpleHeader376);	
		$pdf->tbSetHeaderType($aSimpleHeader38);
		$pdf->tbSetHeaderType($aSimpleHeader39);
		$pdf->tbSetHeaderType($aSimpleHeader40);
		$pdf->tbSetHeaderType($aSimpleHeader41);	
		//$pdf->tbSetHeaderType($aSimpleHeader42);
		//$pdf->tbSetHeaderType($aSimpleHeader43);
		//$pdf->tbSetHeaderType($aSimpleHeader44);
		//$pdf->tbSetHeaderType($aSimpleHeader45);
		 //Draw the Header
        $pdf->tbDrawHeader();
		
		$aDataType = Array();
        for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
        $pdf->tbSetDataType($aDataType);
                
        $dispatch_doc = $this->model_s_input_ba->get_dispatch_doc($dates,$company);
        $i = 1;
		$data[0]['WIDTH'] = 10;
        foreach ($dispatch_doc as $row){
			$data = Array();
			$data[0]['WIDTH'] = 23;
			$data[0]['T_ALIGN'] = 'L';
			$data[0]['TEXT'] = $row['ID_DO'];	
			
			$data[1]['TEXT'] = $row['JENIS'];
			$data[1]['WIDTH'] = 23;
			$data[1]['T_ALIGN'] = 'L';
			
			$data[2]['WIDTH'] = 23;
			$data[2]['T_ALIGN'] = 'R';
			$data[2]['TEXT'] = $row['QTY_CONTRACT'];	
			
			$data[3]['WIDTH'] = 23;
			$data[3]['T_ALIGN'] = 'R';
			$data[3]['TEXT'] = $row['QTY_DELIVERED_RUN'];
			
			$data[4]['WIDTH'] = 23;
			$data[4]['T_ALIGN'] = 'R';
			$data[4]['TEXT'] = $row['BALANCE'];
			
			$data[5]['COLSPAN'] = 3;
			$data[5]['ROWSPAN'] = 4;
                 
            $i++;   
            $pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
        $pdf->tbDrawBorder();
        $pdf->Ln(15.5);
    
        require_once(APPPATH . 'libraries/daftar_upah/authorize_ba2.inc');
		$pdf->Output();
	}*/
	/*
		Algoritma get_approval() sebaiknya d perbiki
	*/
	function get_approval(){
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_s_input_ba->get_approval($company);
		foreach($data_enroll as $row){
			//$data[] = array("QC"=>($row['QC']),"LABOR"=>($row['LABOR']),"MILL"=>($row['MILL']),"KTU"=>($row['KTU']),"ADM"=>($row['ADM']),"ID_APPROVAL"=>($row['ID_APPROVAL']));	
			$data = '~'.$row['MILL'].'~'.$row['KTU'].'~'.$row['ADM'].'~'.$row['ID_APPROVAL'].'~'.$row['QC'].'~'.$row['LABOR'].'~'.$row['COMPANY_CODE'].'~';
        }
		$storeData = json_encode($data);
        echo $storeData;
	}
	
	function get_tbs(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_tbs = $this->model_s_input_ba->get_tbs($company,$tanggalm);
		if ($data_tbs!=NULL){
			foreach($data_tbs as $row)
			{
				$tbs = str_replace('"','\\"',htmlentities($row['TBS_INTI'],ENT_QUOTES,'UTF-8')). "~" . str_replace('"','\\"',htmlentities($row['TBS_SUPPLIER'],ENT_QUOTES,'UTF-8')). "~" . str_replace('"','\\"',htmlentities($row['TBS_GROUP'],ENT_QUOTES,'UTF-8')). "~" . str_replace('"','\\"',htmlentities($row['TBS_PLASMA'],ENT_QUOTES,'UTF-8'));
				
			}
			echo $tbs;
		}
		
    }
	
	function get_restan(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $balance_yesterday = $this->model_s_input_ba->get_restan($company,$tanggalm);
		
		$balance = str_replace('"','\\"',htmlentities($balance_yesterday,ENT_QUOTES,'UTF-8'));
		echo $balance;
    }
	
	function get_buahmentah(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
		$type = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');
        $grading= $this->model_s_input_ba->get_grading($company,$tanggalm,$type);
		
		$grading = str_replace('"','\\"',htmlentities($grading,ENT_QUOTES,'UTF-8'));
		echo $grading;
    }
	
	/*
	function get_ffb_processed(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
		$day=preg_split('/[- :]/',trim($tanggalm));
		$day=implode('',$day);
		$yesterday = strtotime('-1 day',strtotime($day)); 
		$yesterday = date('Ymd', $yesterday);
		
        $balance_yesterday = $this->model_s_input_ba->get_restan($company,$yesterday);		
		$balance_today = $this->model_s_input_ba->get_restan($company,$tanggalm);
		$tbs = $this->model_s_input_ba->get_tbs($company,$tanggalm);	
    }
	*/
	
    function LoadData(){
        $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_s_input_ba->LoadData($periode,$company));   
    }
    
	function LoadData_Commodity(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadData_Commodity($company));   
    }
	
	function LoadData_OtherStock(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadData_OtherStock($company));   
    }
	
	function LoadData_Storage(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadData_Storage($company));   
    }
	
	function LoadDetail_Production($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadDetail_Production($company, $id_ba));   
    }
	
	function LoadProductionByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadProductionByDate($company, $date));   
    }
	
	function LoadNoProductionByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadNoProductionByDate($company, $date));   
    }
	
	function LoadDispatchByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadDispatchByDate($company, $date));   
    }
	
	function LoadStorageByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadStorageByDate($company, $date));   
    }
	
	function LoadOtherStockByDate($date){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadOtherStockByDate($company, $date));   
    }
	
	function LoadDetail_Dispatch($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadDetail_Dispatch($company, $id_ba));   
    }
	
	function LoadDetail_Stock($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadDetail_Stock($company, $id_ba));   
    }
	
	function LoadDetail_StorageStock($id_ba){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');        
        echo json_encode($this->model_s_input_ba->LoadDetail_StorageStock($company, $id_ba));   
    }
	
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_input_ba->data_search($data['rules'], $company));    
    } 
	
    function add_new($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage, $data_performance){
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
        
		$data_post['FFB_INTI']=strtoupper(trim(htmlentities($data_id['FFB_INTI'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_PLASMA']=strtoupper(trim(htmlentities($data_id['FFB_PLASMA'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_SUPPLIER']=strtoupper(trim(htmlentities($data_id['FFB_SUPPLIER'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_GROUP']=strtoupper(trim(htmlentities($data_id['FFB_GROUP'],ENT_QUOTES,'UTF-8')));		
		$data_post['FFB_PROCESSED']=strtoupper(trim(htmlentities($data_id['FFB_PROCESSED'],ENT_QUOTES,'UTF-8')));
		$data_post['BALANCE_YESTERDAY']=strtoupper(trim(htmlentities($data_id['BALANCE_YESTERDAY'],ENT_QUOTES,'UTF-8')));
		$data_post['LORI_OLAH']=strtoupper(trim(htmlentities($data_id['LORI_OLAH'],ENT_QUOTES,'UTF-8')));
		$data_post['LORI_RESTAN']=strtoupper(trim(htmlentities($data_id['LORI_RESTAN'],ENT_QUOTES,'UTF-8')));
		if ($data_id['LORI_RESTAN']==0 && $data_id['LORI_OLAH']>0){
			$data_post['BALANCE']=0;
		}else{
			$data_post['BALANCE']=(($data_post['FFB_INTI']+$data_post['FFB_PLASMA']+$data_post['FFB_SUPPLIER']+$data_post['FFB_GROUP']+$data_post['BALANCE_YESTERDAY'])-$data_post['FFB_PROCESSED']);
		}
		$data_post['CAGE_WEIGHT']=strtoupper(trim(htmlentities($data_id['CAGE_WEIGHT'],ENT_QUOTES,'UTF-8')));
		
		$data_post['BUAH_MENTAH']=strtoupper(trim(htmlentities($data_id['BUAH_MENTAH'],ENT_QUOTES,'UTF-8')));
		$data_post['BUAH_BUSUK']=strtoupper(trim(htmlentities($data_id['BUAH_BUSUK'],ENT_QUOTES,'UTF-8')));
		$data_post['JJK']=strtoupper(trim(htmlentities($data_id['JJK'],ENT_QUOTES,'UTF-8')));
		$data_post['TANGKAI']=strtoupper(trim(htmlentities($data_id['TANGKAI'],ENT_QUOTES,'UTF-8')));
		$data_post['BRONDOLAN']=strtoupper(trim(htmlentities($data_id['BRONDOLAN'],ENT_QUOTES,'UTF-8')));
		$data_post['HOUR_FROM']=strtoupper(trim(htmlentities($data_id['HOUR_FROM'],ENT_QUOTES,'UTF-8')));
		$data_post['HOUR_TO']=strtoupper(trim(htmlentities($data_id['HOUR_TO'],ENT_QUOTES,'UTF-8')));
		$data_post['CBC_FROM']=strtoupper(trim(htmlentities($data_id['CBC_FROM'],ENT_QUOTES,'UTF-8')));
		$data_post['CBC_TO']=strtoupper(trim(htmlentities($data_id['CBC_TO'],ENT_QUOTES,'UTF-8')));
				
		$data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;
		
		
		$field = $data_post['BA_DATE'];		
		$validate_approve=$this->validate_approve($field);
        if( strtolower($validate_approve)=='false'){
            $return['status'] ="BA tanggal ".$field. " tidak dapat disimpan karena BA tanggal sebelumnya belum di APPROVE atau di INPUT";
            $return['error']=true;        
        }
		
        //add BA validation code here!!!
		$field = $data_post['BUAH_MENTAH'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai Buah mentah harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['BUAH_BUSUK'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai Buah busuk harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['JJK'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai JJK harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['BRONDOLAN'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai brondolan harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['CBC_FROM'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CBC harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['CBC_TO'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CBC harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['TANGKAI'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai panjang tangkai harus angka";
            $return['error']=true;        
        }
		
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
		
		$field = $data_post['FFB_INTI'];
		$validate_numeric=$this->validate_numeric($field);
		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB Inti harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['FFB_INTI']) > 50){
            $return['status']  ="Panjang karakter FFB Inti melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_PLASMA'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB Plasma harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_PLASMA']) > 50){
            $return['status']  ="Panjang karakter FFB plasma melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_SUPPLIER'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB supplier harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_SUPPLIER']) > 50){
            $return['status']  ="Panjang karakter FFB supplier melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_GROUP'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB group harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_GROUP']) > 50){
            $return['status']  ="Panjang karakter FFB group melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_PROCESSED'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB processed harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_PROCESSED']) > 50){
            $return['status']  ="Panjang karakter FFB processed melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['CAGE_WEIGHT'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai cage weight harus angka";
            $return['error']=true;        
        }
		
		if(strlen($data_post['CAGE_WEIGHT']) > 50){
            $return['status']  ="Panjang karakter cage weight melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['BALANCE_YESTERDAY'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai balance yesterday harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['BALANCE_YESTERDAY']) > 50){
            $return['status']  ="Panjang karakter balance yesterday melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['LORI_OLAH'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai lori olah harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['LORI_OLAH']) > 50){
            $return['status']  ="Panjang karakter lori olah melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['LORI_RESTAN'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai lori restan harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['LORI_RESTAN']) > 50){
            $return['status']  ="Panjang karakter lori restan melebihi batas maksimal";
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
		//start: performance
		$data_post['PROCESSED_HOUR']=strtoupper(trim(htmlentities($data_performance['PROCESSED_HOUR'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['THROUGHPUT']=strtoupper(trim(htmlentities($data_performance['THROUGHPUT'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['MILL_UTILIZATION']=strtoupper(trim(htmlentities($data_performance['MILL_UTILIZATION'],ENT_QUOTES,'UTF-8'))) ;
		
		$validate_numeric=$this->validate_numeric($data_post['PROCESSED_HOUR']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai PROCESSED HOUR harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['THROUGHPUT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai THROUGHPUT harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['MILL_UTILIZATION']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai MILL UTILIZATION harus angka";
            $return['error']=true;        
        }
		
		if(strlen($data_post['PROCESSED_HOUR']) > 50){
            $return['status']  ="Panjang karakter PROCESSED_HOUR melebihi batas maksimal";
            $return['error']=true;
        }
		
		if(strlen($data_post['THROUGHPUT']) > 50){
            $return['status']  ="Panjang karakter THROUGHPUT melebihi batas maksimal";
            $return['error']=true;
        }
		
		if(strlen($data_post['MILL_UTILIZATION']) > 50){
            $return['status']  ="Panjang karakter MILL_UTILIZATION melebihi batas maksimal";
            $return['error']=true;
        }
		//end: performance
				
		if(empty($return['status']) && $return['error']==false){     
            $insert_id = $this->model_s_input_ba->add_new($company,$data_post);
			
			if($insert_id['error'] == false){ 
            	$insert_detail = $this->model_s_input_ba->add_new_production($data_post['ID_BA'], $company, $data_post_d);
				$insert_detail = $this->model_s_input_ba->add_new_dispatch($data_post['ID_BA'], $company, $data_post_dispatch);
				$insert_detail = $this->model_s_input_ba->add_new_stock($data_post['ID_BA'], $company, $data_post_stock);
				$insert_detail = $this->model_s_input_ba->add_new_storage_stock($data_post['ID_BA'], $company, $data_post_storage);				
				$insert_detail = $this->model_s_input_ba->update_approval($data_post['QC'], $data_post['MILL_MANAGER'], $data_post['KTU'], $data_post['ADMINISTRATUR'], $data_post['LABOR'], $company);
				
				$message = $insert_detail;
			}else{
				$message = $insert_id;	
			}		

            echo json_encode($message);          
        }else{
            echo json_encode($return);
        }
		
    }
				
    function update_data($data_id, $data_prod, $data_dispatch, $data_stock, $data_storage, $data_performance){
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
        
		$data_post['FFB_INTI']=strtoupper(trim(htmlentities($data_id['FFB_INTI'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_PLASMA']=strtoupper(trim(htmlentities($data_id['FFB_PLASMA'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_SUPPLIER']=strtoupper(trim(htmlentities($data_id['FFB_SUPPLIER'],ENT_QUOTES,'UTF-8')));
		$data_post['FFB_GROUP']=strtoupper(trim(htmlentities($data_id['FFB_GROUP'],ENT_QUOTES,'UTF-8')));		
		$data_post['FFB_PROCESSED']=strtoupper(trim(htmlentities($data_id['FFB_PROCESSED'],ENT_QUOTES,'UTF-8')));
		$data_post['BALANCE_YESTERDAY']=strtoupper(trim(htmlentities($data_id['BALANCE_YESTERDAY'],ENT_QUOTES,'UTF-8')));
		$data_post['LORI_OLAH']=strtoupper(trim(htmlentities($data_id['LORI_OLAH'],ENT_QUOTES,'UTF-8')));
		$data_post['LORI_RESTAN']=strtoupper(trim(htmlentities($data_id['LORI_RESTAN'],ENT_QUOTES,'UTF-8')));
		if ($data_id['LORI_RESTAN']==0 && $data_id['LORI_OLAH']>0){
			$data_post['BALANCE']=0;
		}else{
			$data_post['BALANCE']=(($data_post['FFB_INTI']+$data_post['FFB_PLASMA']+$data_post['FFB_SUPPLIER']+$data_post['FFB_GROUP']+$data_post['BALANCE_YESTERDAY'])-$data_post['FFB_PROCESSED']);
		}
		$data_post['CAGE_WEIGHT']=strtoupper(trim(htmlentities($data_id['CAGE_WEIGHT'],ENT_QUOTES,'UTF-8')));
		
		$data_post['BUAH_MENTAH']=strtoupper(trim(htmlentities($data_id['BUAH_MENTAH'],ENT_QUOTES,'UTF-8')));
		$data_post['BUAH_BUSUK']=strtoupper(trim(htmlentities($data_id['BUAH_BUSUK'],ENT_QUOTES,'UTF-8')));
		$data_post['JJK']=strtoupper(trim(htmlentities($data_id['JJK'],ENT_QUOTES,'UTF-8')));
		$data_post['TANGKAI']=strtoupper(trim(htmlentities($data_id['TANGKAI'],ENT_QUOTES,'UTF-8')));
		$data_post['BRONDOLAN']=strtoupper(trim(htmlentities($data_id['BRONDOLAN'],ENT_QUOTES,'UTF-8')));
		$data_post['HOUR_FROM']=strtoupper(trim(htmlentities($data_id['HOUR_FROM'],ENT_QUOTES,'UTF-8')));
		$data_post['HOUR_TO']=strtoupper(trim(htmlentities($data_id['HOUR_TO'],ENT_QUOTES,'UTF-8')));
		$data_post['CBC_FROM']=strtoupper(trim(htmlentities($data_id['CBC_FROM'],ENT_QUOTES,'UTF-8')));
		$data_post['CBC_TO']=strtoupper(trim(htmlentities($data_id['CBC_TO'],ENT_QUOTES,'UTF-8')));
				
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
		
		$field = $data_post['BUAH_MENTAH'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai Buah mentah harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['BUAH_BUSUK'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai Buah busuk harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['JJK'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai JJK harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['BRONDOLAN'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai brondolan harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['CBC_FROM'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CBC harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['CBC_TO'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CBC harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['TANGKAI'];
		$validate_numeric=$this->validate_numeric($field);		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai panjang tangkai harus angka";
            $return['error']=true;        
        }
		
		$field = $data_post['FFB_INTI'];
		$validate_numeric=$this->validate_numeric($field);
		
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB Inti harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['FFB_INTI']) > 50){
            $return['status']  ="Panjang karakter FFB Inti melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_PLASMA'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB Plasma harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_PLASMA']) > 50){
            $return['status']  ="Panjang karakter FFB plasma melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_SUPPLIER'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB supplier harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_SUPPLIER']) > 50){
            $return['status']  ="Panjang karakter FFB supplier melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_GROUP'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB group harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_GROUP']) > 50){
            $return['status']  ="Panjang karakter FFB group melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['FFB_PROCESSED'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai FFB processed harus angka";
            $return['error']=true;        
        }
		if(strlen($data_post['FFB_GROUP']) > 50){
            $return['status']  ="Panjang karakter FFB processed melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['CAGE_WEIGHT'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai cage weight harus angka";
            $return['error']=true;        
        }
		
		if(strlen($data_post['CAGE_WEIGHT']) > 50){
            $return['status']  ="Panjang karakter cage weight melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['BALANCE_YESTERDAY'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai balance yesterday harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['BALANCE_YESTERDAY']) > 50){
            $return['status']  ="Panjang karakter balance yesterday melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['LORI_OLAH'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai lori olah harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['LORI_OLAH']) > 50){
            $return['status']  ="Panjang karakter lori olah melebihi batas maksimal";
            $return['error']=true;
        }
		
		$field = $data_post['LORI_RESTAN'];
		$validate_numeric=$this->validate_numeric($field);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai lori restan harus angka";
            $return['error']=true;        
        }		
		if(strlen($data_post['LORI_RESTAN']) > 50){
            $return['status']  ="Panjang karakter lori restan melebihi batas maksimal";
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
		//start: performance
		$data_post['PROCESSED_HOUR']=strtoupper(trim(htmlentities($data_performance['PROCESSED_HOUR'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['THROUGHPUT']=strtoupper(trim(htmlentities($data_performance['THROUGHPUT'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['MILL_UTILIZATION']=strtoupper(trim(htmlentities($data_performance['MILL_UTILIZATION'],ENT_QUOTES,'UTF-8'))) ;
		
		$validate_numeric=$this->validate_numeric($data_post['PROCESSED_HOUR']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai PROCESSED HOUR harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['THROUGHPUT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai THROUGHPUT harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['MILL_UTILIZATION']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai MILL UTILIZATION harus angka";
            $return['error']=true;        
        }
		
		if(strlen($data_post['PROCESSED_HOUR']) > 50){
            $return['status']  ="Panjang karakter PROCESSED_HOUR melebihi batas maksimal";
            $return['error']=true;
        }
		
		if(strlen($data_post['THROUGHPUT']) > 50){
            $return['status']  ="Panjang karakter THROUGHPUT melebihi batas maksimal";
            $return['error']=true;
        }
		
		if(strlen($data_post['MILL_UTILIZATION']) > 50){
            $return['status']  ="Panjang karakter MILL_UTILIZATION melebihi batas maksimal";
            $return['error']=true;
        }
		//end: performance
		
		if(empty($return['status']) && $return['error']==false){  
			$update_id = $this->model_s_input_ba->update_data($id_ba, $company, $data_post);			
			if($update_id['error'] == false){ 
            	$insert_detail = $this->model_s_input_ba->update_production($id_ba, $company, $data_post_d);
				$insert_detail = $this->model_s_input_ba->update_dispatch($id_ba, $company, $data_post_dispatch);
				$insert_detail = $this->model_s_input_ba->update_stock($id_ba, $company, $data_post_stock);
				$insert_detail = $this->model_s_input_ba->update_storage_stock($id_ba, $company, $data_post_storage);				
				$insert_detail = $this->model_s_input_ba->update_approval($data_post['QC'], $data_post['MILL_MANAGER'], $data_post['KTU'], $data_post['ADMINISTRATUR'], $data_post['LABOR'], $company);
				
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
            $delete_id = $this->model_s_input_ba->delete_ba($id_ba,$company);
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
            $delete_id = $this->model_s_input_ba->approve_ba($id_ba,$company,$ba_date);
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
		$status_ba=$this->model_s_input_ba->get_ba($company, $dates);
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
