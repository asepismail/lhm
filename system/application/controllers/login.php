<?php

class Login extends Controller 
{
	function Login ()
	{
		parent::Controller();	
		$this->load->model( 'model_m_user' ); 
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
	}
	
	function index(){
		$data = array();
		$data['company'] = $this->dropdowncompany();
		$data['module'] = $this->dropdownmodule();
		$data['session'] = $this->session->userdata('LOGINID');
		
		if($this->session->userdata('LOGINID') == ""){
			$this->load->view('login',$data);
		} else { 
			if($this->session->userdata('MODULE_ACCESS') == "PMS") {
				redirect('pms/main_c');
			} elseif ( $this->session->userdata('MODULE_ACCESS') == "LHM" ){
				redirect('m_gang_activity_detail');
			} elseif ( $this->session->userdata('MODULE_ACCESS') == "PRD" ){ 
				redirect('c_dashboard');
			}
		}
	}
	
	function doLogin(){
		$data = array();
		$data['company'] = $this->dropdowncompany();
		$data['module'] = $this->dropdownmodule();
		$data['session'] = $this->session->userdata('LOGINID');
		
		if(empty($_GET)){
			$this->load->view('login',$data);
		} else { 
			$data_info = $this->model_m_user->login($_GET);
			if(count($data_info) == 0){ 
					$this->session->sess_destroy();
					$this->load->view('login');
			} else {
				$row = $data_info; 
				$data_user = $this->model_m_user->login($row['LOGINID']); 
				$this->session->sess_destroy();
				$this->session->sess_create();
				$this->session->set_userdata($row);
				$data_login = $this->session->userdata('LOGINID');
				$data_pass = $this->session->userdata('USER_PASS');
				$company_code = $this->session->userdata('NCOMPANY');
				$company_name = $this->session->userdata('NCOMPANY_NAME');
				$company_dest = $this->session->userdata('DCOMPANY');
				$user_level = $this->session->userdata('GROUP_USER');
				$module = $_GET['modul'];
				unset($row['USER_PASS']);
				
				$this->session->set_userdata(array('logged_in' => true,'username' => $data_login,'company_nm'=> $company_name, 'company'=> $company_code,'co_dest'=> $company_dest, 'level'=> $user_level  ));
					
                if($this->session->userdata('LOGINID') == ""){
                    $this->load->view('login',$data);
                } else { 
                    if($this->session->userdata('MODULE_ACCESS') == "PMS") {
                        echo 1;
                    } elseif ( $this->session->userdata('MODULE_ACCESS') == "LHM" ){
                        echo 1;
                    } elseif ( $this->session->userdata('MODULE_ACCESS') == "PRD" ){ 
                        echo 1;
                    }
                }		
				/*if(trim(strtoupper($row['USER_LEVEL']))=='ADMPNN'){
					redirect('c_dashboard');
				} elseif (trim(strtoupper($row['USER_LEVEL']))=='USRPNN'){
					redirect('c_dashboard');
				} else {  
					echo 1;
				} */                   
			}
		}
    }
	
	function doLogout(){
		$this->session->sess_destroy();
		redirect(base_url());
		return TRUE;
	}
	
	function valUser(){
		$validateUser = $this->model_m_user->validateUser($_GET['fieldValue']);
		if(count($validateUser) == '0') {
			$arrayToJs[0] = 'uname';
			$arrayToJs[1] = false;
			$arrayToJs[2] = "Nama user " . $_GET['fieldValue'] . " tidak terdaftar atau tidak aktif!!!";
		} else{
			$arrayToJs[0] = 'uname';
			$arrayToJs[1] = true;
		} 
		echo json_encode($arrayToJs);
	}
	
	function valPass(){
		$arrayToJs = array();
		$validatePass = $this->model_m_user->validatePassword($_GET['uname'], $_GET['fieldValue']);
		if(count($validatePass) == '0') {
			$arrayToJs[0] = 'upass';
			$arrayToJs[1] = false;
			$arrayToJs[2] = "Password anda salah!!!";
		} else{
			$arrayToJs[0] = 'upass';
			$arrayToJs[1] = true;
		}
		echo json_encode($arrayToJs);
	}
	
	function valCompany(){
		$arrayToJs = array();
		$validateCompany = $this->model_m_user->validateCompany($_GET['uname'], $_GET['fieldValue']);
		if(count($validateCompany) == '0') {
			$arrayToJs[0] = 'usite';
			$arrayToJs[1] = false;
			$arrayToJs[2] = "Anda tidak mempunyai akses untuk perusahaan ini!!!";		
		} else{
			$arrayToJs[0] = 'usite';
			$arrayToJs[1] = true;
		}
		print json_encode($arrayToJs);
	}
	
	function valModule(){
		$arrayToJs = array();
		$validateModule = $this->model_m_user->validateModule($_GET['uname'], $_GET['fieldValue']);
		if(count($validateModule) == '0') {
			$arrayToJs[0] = 'modul';
			$arrayToJs[1] = false;
			$arrayToJs[2] = "Anda tidak mempunyai akses untuk modul ini!!!";
		} else{
			$arrayToJs[0] = 'modul';
			$arrayToJs[1] = true;
		}
		echo json_encode($arrayToJs);
	}
	
	function dropdowncompany(){
		$string = "<select class='validate[required,ajax[ajaxCompanyCallPhp]] text-input' name='usite' id='usite' >";
		$string .= "<option value=''> -- Pilih Site -- </option>";
		$data_afd = $this->model_m_user->getCompany();
		foreach ( $data_afd as $row){
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\"  selected>".$row['COMPANY_NAME']." </option>";
			} else {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownmodule(){
		$string="<select class='validate[required,ajax[ajaxModulCallPhp]] text-input' name='modul'  id='modul'>";
		$string .= "<option value=''> -- Pilih Modul -- </option>";
		$data_afd = $this->model_m_user->getModule();
		foreach ( $data_afd as $row){
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string."<option value=\"".$row['MODULE_ID']."\" selected>".$row['MODULE_NAME']." </option>";
			} else {
				$string = $string."<option value=\"".$row['MODULE_ID']."\">".$row['MODULE_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
}
?>