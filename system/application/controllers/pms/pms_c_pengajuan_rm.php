<?php
class pms_c_pengajuan_rm extends Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_pengajuan_rm');
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
		$this->lastmenu="main_c_pms";
    }
	
	function index(){
		$view="pms/pms_v_pengajuan_rm";
		$this->data['js'] = "";
		$this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
		$this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		$this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$this->data['fperiode'] = $this->global_func->drop_date2('fbulan','ftahun','select');
		$this->data['RMSatuan'] = $this->dropdownlist_satuan('RMdetail_i_uom');
		
		if ($this->data['login_id'] == TRUE){
			$this->load->view($view, $this->data);
		} else {
			redirect('login');
		}
    }
	
	/* grid utama */
    function read_grid_rm(){
		$company = $this->session->userdata('DCOMPANY');
		$periode = $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan_rm->loadDataRM($company, $periode));
    }
	
	function getInfrasC(){
        $cv = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $dataInfras = $this->pms_m_pengajuan_rm->getInfrasModel($cv, $company);
        
        $infras = array();
        foreach($dataInfras as $row)
		{
			$infras[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['IFCODE'],ENT_QUOTES,'UTF-8')). '",res_name:"'.str_replace('"','\\"',htmlentities($row['IFNAME'],ENT_QUOTES,'UTF-8')). '",res_dl:"'.str_replace('"','\\"',htmlentities($row['IFCODE'],ENT_QUOTES,'UTF-8'). " - " . htmlentities($row['IFNAME'],ENT_QUOTES,'UTF-8')).'"}';
		}
        echo '['.implode(',',$infras).']'; exit; 
    }
	
	function returnNoPengajuanRM(){
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$companyID = $this->pms_m_pengajuan_rm->get_company_id($company);
		$ret = $this->pms_m_pengajuan_rm->getNoPengajuanRM($company);
				
		if($ret == "" || $ret == "0"){
			$ret = $companyID."00001";
		} else {
			$ret = $ret+1;
		}
		echo $ret;
	}
	
	/* fungsi controller CRUD Header*/
	function submitData(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
        $data_post['RM_PENGAJUAN_ID'] = $ppj;
		$data_post['PERIODE'] = trim($this->input->post( 'PERIODE' ) ); 
		$data_post['RM_TGL_PENGAJUAN'] = trim($this->input->post( 'RM_TGL_PENGAJUAN' ) ); 
		$data_post['IFCODE'] = trim($this->input->post( 'IFCODE' ) ) ; 
		$data_post['DESCRIPTION'] = trim($this->input->post( 'DESCRIPTION' ) ); 
		$status = trim($this->input->post( 'PENGAJUAN_STATUS' ) );
		if($status == "draft"){
			$status = 0;
		} else {
			$status = 1;
		}
		$data_post['PENGAJUAN_STATUS'] = $status; 
		$data_post['COMPANY_CODE'] = trim($company); 
		
		$dataHeader = $this->pms_m_pengajuan_rm->cek_pengajuan($company, $ppj);
		$cekExistPengajuan = $this->pms_m_pengajuan_rm->cekExist($company, $data_post['IFCODE'], $data_post['PERIODE']);
		$cekExistIfcode = $this->pms_m_pengajuan_rm->cekExistIfcode($company, $data_post['IFCODE']);
		$cekdata = count($dataHeader);
		$result = "";
		
		if( $data_post['PERIODE'] == date("Ym") ) {
			if( $company == 'MSS' ||  $company == 'SCK' ){
				if($cekdata > 0){
					if( $cekExistIfcode > 0 ) {
						$data_post['UPDATED'] = $this->session->userdata('LOGINID');
						$data_post['UPDATED_DATE'] = date ("Y-m-d H:i:s");
						$result = $this->pms_m_pengajuan_rm->update_pengajuan_rm( $ppj, $company, $data_post );
					} else {
						$result = "Infrastruktur tidak terdaftar di dalam database!!!";
					}
				} else {
			
					if( $cekExistPengajuan != "" || $cekExistPengajuan > 0 ) {
						$result = "Data Pengajuan sudah pernah diajukan di periode yang sama sebelumnya!!!";
					} else {
						if( $cekExistIfcode > 0 ) {
							$data_post['CREATED'] = $this->session->userdata('LOGINID');
							$data_post['CREATED_DATE'] = date ("Y-m-d H:i:s");
							$result = $this->pms_m_pengajuan_rm->insert_pengajuan_rm( $data_post );
						} else {
							$result = "Infrastruktur tidak terdaftar di dalam database!!!";
						}
					}
				}		
				
			} else {
				$result = "Data pengajuan untuk bulan ini sudah ditutup!!!";
			}
		} else {	
			if($cekdata > 0){
				if( $cekExistIfcode > 0 ) {
					$data_post['UPDATED'] = $this->session->userdata('LOGINID');
					$data_post['UPDATED_DATE'] = date ("Y-m-d H:i:s");
					$result = $this->pms_m_pengajuan_rm->update_pengajuan_rm( $ppj, $company, $data_post );
				} else {
					$result = "Infrastruktur tidak terdaftar di dalam database!!!";
				}
			} else {
			
				if( $cekExistPengajuan != "" || $cekExistPengajuan > 0 ) {
					$result = "Data Pengajuan sudah pernah diajukan di periode yang sama sebelumnya!!!";
				} else {
					if( $cekExistIfcode > 0 ) {
						$data_post['CREATED'] = $this->session->userdata('LOGINID');
						$data_post['CREATED_DATE'] = date ("Y-m-d H:i:s");
						$result = $this->pms_m_pengajuan_rm->insert_pengajuan_rm( $data_post );
					} else {
						$result = "Infrastruktur tidak terdaftar di dalam database!!!";
					}
				}
			}
		}	
		echo $result;
	}
	
	function deleteData(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
        $data_post['RM_PENGAJUAN_ID'] = $ppj;
		
		$dataHeader = $this->pms_m_pengajuan_rm->cek_pengajuan($company, $ppj);
		$cekdata = count($dataHeader);
		if($cekdata > 0){
			$insert_id = $this->pms_m_pengajuan_rm->delete_pengajuan_rm( $ppj, $company );
		}
		
		echo $insert_id;
	}
	
	function read_grid_detail_rm(){
		$nopengajuan = $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan_rm->loadPengajuanDetail($nopengajuan));
    }
	
	function getActivity(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
		$lc = $this->uri->segment(4);
        $dataInfras = $this->pms_m_pengajuan_rm->getActivity($lc, $q);
        
        $infras = array();
		if($dataInfras != ""){
		  foreach($dataInfras as $row)
		  {
			  $infras[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')). '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')). '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8'). " - " . htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'"}';
		  }
		  echo '['.implode(',',$infras).']'; exit; 
		}
    }
	
	function dropdownlist_satuan($name){ 
		$string = "<select  name='".$name."' class='select' id='".$name."' style='width:120px;'>";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = $this->pms_m_pengajuan_rm->getUom();
		
		foreach ( $data as $row){
		   if( (isset($default))){
			 $string = $string." <option value=\"".$row['UNIT_CODE']."\"  selected>".$row['UNIT_DESC']." </option>";
			} else {
			 $string = $string." <option value=\"".$row['UNIT_CODE']."\">".$row['UNIT_DESC']." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
	/* fungsi crud detail pengajuan */
	function cekDataDetail(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = $this->uri->segment(4);
        		
		$dataHeader = $this->pms_m_pengajuan_rm->cek_pengajuan($company, $ppj);
		
		$cekdata = count($dataHeader);
		echo $cekdata;
	}
	
		
	function submitDataDetail(){
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) );
		$mode = trim($this->input->post( 'mode' ) );  
        $data_post['RM_PENGAJUAN_ID'] = $ppj;
		$data_post['ACTIVITY_CODE'] = trim($this->input->post( 'ACTIVITY_CODE' ) ); 
		$data_post['QTY'] = trim($this->input->post( 'QTY' ) ); 
		$data_post['UOM'] = trim($this->input->post( 'UOM' ) ) ; 
		$data_post['RPSAT'] = trim($this->input->post( 'RPSAT' ) ); 
		$data_post['RPTTL'] = trim($this->input->post( 'RPTTL' ) ); 
				
		//$cekExistPengajuan = $this->pms_m_pengajuan_rm->cekExist($company, $data_post['IFCODE'], $data_post['PERIODE']);
		
		$result = "";
		$validateActivity = $this->pms_m_pengajuan_rm->validateActivity($data_post['ACTIVITY_CODE']);
		
		if(count($validateActivity) > 0 ){
			//echo $cekExistIfcode;
			$cekDetailRM = $this->pms_m_pengajuan_rm->validateDetailRM($ppj, $data_post['ACTIVITY_CODE']);
			if( $cekDetailRM > 0 ) {
				$data_post['UPDATED'] = $this->session->userdata('LOGINID');
				$data_post['UPDATED_DATE'] = date ("Y-m-d H:i:s");
				$result = $this->pms_m_pengajuan_rm->update_dpengajuan_rm( $ppj, $data_post['ACTIVITY_CODE'], $data_post );
			} else {
				$data_post['CREATED'] = $this->session->userdata('LOGINID');
				$data_post['CREATED_DATE'] = date ("Y-m-d H:i:s");
				$result = $this->pms_m_pengajuan_rm->insert_dpengajuan_rm( $data_post );
			}
			
		} else {
			$result = "data aktivitas salah atau tidak terdapat di dalam database !!!";
		}
			
		echo $result;
	}
	
	function voidDataDetail(){
		$activity = trim($this->input->post( 'ACTIVITY_CODE' ) );
		$ppj = trim($this->input->post( 'RM_PENGAJUAN_ID' ) ); 
		$insert_id = $this->pms_m_pengajuan_rm->void_dpengajuan_rm( $ppj, $activity );
		
		echo $insert_id;
	}
	/* end fungsi detail pengajuan */
}

?>