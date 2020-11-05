<?php
class syst_c_user extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
		$this->load->model('system/syst_m_user');
		$this->load->model( 'model_m_user' );
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
		$this->lastmenu="syst_m_user";
		$this->load->helper('file');
    }
	
	function index(){
      $view="system/syst_v_user";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Master Data Pengguna Sistem";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
	  
	  $this->data['dept'] = $this->dropdownlist_dept();
	  $this->data['company'] = $this->dropdownlist_company("i_company");
	  $this->data['scompany'] = $this->dropdownlist_company("s_company");
	  $this->data['ucompany'] = $this->dropdownlist_company("u_company");
	  $this->data['level'] = $this->dropdownlist_usergroup();
	  
	  if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   function search_user(){
	   $company = $this->uri->segment(4);
	   
	   if($company == "" || $company == "PAG"){
	   		$company = "*";
	   }
	   
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_user->read_user($company,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_user->read_user($company);
	   }
	   echo json_encode($get);
   }
   
   function dropdownlist_dept(){
		$string = "<select  name='i_dept' class='select' id='i_dept' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->model_m_user->get_dept();
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\"  selected>".$row['DEPT_DESCRIPTION']." </option>";
			} else {
				$string = $string." <option value=\"".$row['DEPT_CODE']."\">".$row['DEPT_DESCRIPTION']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_company($id){
		$string = "<select  name='".$id."' class='select' id='".$id."' style='width:220px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->syst_m_user->get_company();
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\"  selected>".$row['COMPANY_NAME']." </option>";
			} else {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_usergroup(){
		$string = "<select  name='i_group' class='select' id='i_group' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_dept = $this->syst_m_user->get_usergroup();
		
		foreach ( $data_dept as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['USER_GROUP_ID']."\"  selected>".$row['USER_GROUP_NAME']." </option>";
			} else {
				$string = $string." <option value=\"".$row['USER_GROUP_ID']."\">".$row['USER_GROUP_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	/* CRUD */
	function insertUser(){
		$data_post['LOGINID'] = str_replace(" ","",$this->input->post( 'LOGINID' ));
        $data_post['USER_FULLNAME'] = $this->input->post( 'USER_FULLNAME' );
		$data_post['USER_PASS'] = str_replace(" ","",md5($this->input->post( 'USER_PASS' )));
        $data_post['USER_MAIL'] = str_replace(" ","",$this->input->post( 'USER_MAIL' ));
		$data_post['USER_DEPT'] = str_replace(" ","",$this->input->post( 'USER_DEPT' ));
        $data_post['USER_LEVEL'] = str_replace(" ","",$this->input->post( 'USER_LEVEL' ));
		$data_post['INACTIVE'] = str_replace(" ","",$this->input->post( 'INACTIVE' ));
        $data_post['COMPANY_CODE'] = str_replace(" ","",$this->input->post( 'COMPANY_CODE' ));
				
		$data_User = $this->syst_m_user->cek_exist($data_post['LOGINID']);
		if($data_User > 0) { 
           $status = "user dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
        } else  {
		   $insert_id = $this->syst_m_user->insertUser( $data_post );
		}
	}
	
	function updateUser(){
		$data_post['LOGINID'] = str_replace(" ","",$this->uri->segment(4));
        $data_post['USER_FULLNAME'] = $this->input->post( 'USER_FULLNAME' );
		$data_post['USER_PASS'] = str_replace(" ","",md5($this->input->post( 'USER_PASS' )));
        $data_post['USER_MAIL'] = str_replace(" ","",$this->input->post( 'USER_MAIL' ));
		$data_post['USER_DEPT'] = str_replace(" ","",$this->input->post( 'USER_DEPT' ));
        $data_post['USER_LEVEL'] = str_replace(" ","",$this->input->post( 'USER_LEVEL' ));
		$data_post['INACTIVE'] = str_replace(" ","",$this->input->post( 'INACTIVE' ));
        $data_post['COMPANY_CODE'] = str_replace(" ","",$this->input->post( 'COMPANY_CODE' ));
		$insert_id = $this->syst_m_user->updateUser( $data_post['LOGINID'], $data_post );
	}
	
	function deleteUser(){
		$data_post['LOGINID'] = str_replace(" ","",$this->uri->segment(4));
		$insert_id = $this->syst_m_user->deleteUser( $data_post['LOGINID'] );
	}
	
	/* company access */
	function search_user_co(){
	   $uid = $this->uri->segment(4);
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_user->read_userco_access($uid,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_user->read_userco_access($uid);
	   }
	   echo json_encode($get);
   }
   
   /* module access */
	function search_user_module(){
	   $uid = $this->uri->segment(4);
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_user->read_usermodule_access($uid,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_user->read_usermodule_access($uid);
	   }
	   echo json_encode($get);
   }
   
   function insertUserCo(){
	   $data_post['USERID'] = str_replace(" ","",$this->input->post( 'USERID' ));
       $data_post['COMPANY_CODE'] = str_replace(" ","",$this->input->post( 'COMPANY_CODE' ));
				
	   $data_UserCo = $this->syst_m_user->cek_exist_userco($data_post['USERID'], $data_post['COMPANY_CODE']);
	   if($data_UserCo > 0) { 
           $status = "user dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
       } else  {
		   $insert_id = $this->syst_m_user->insertUserCo( $data_post );
	   }
	}
   
   function deleteUserCo(){
	   $id = str_replace(" ","",$this->uri->segment(4));
       $insert_id = $this->syst_m_user->deleteUserCo( $id );
   }
   /* end company access */
   
   /* start module access */
   function insertUserModule(){
	   $data_post['LOGINID'] = str_replace(" ","",$this->input->post( 'LOGINID' ));
       $data_post['MODULE_ACCESS'] = str_replace(" ","",$this->input->post( 'MODULE_ACCESS' ));
	   $data_post['INACTIVE'] = 0;
				
	   //$data_UserModule = $this->syst_m_user->cek_exist_usermodule($data_post['LOGINID'], $data_post['MODULE_ACCESS']);
	   $data_UserModule = $this->syst_m_user->cek_exist_usermodule($data_post['LOGINID'], $data_post['MODULE_ACCESS']);
	   if($data_UserModule > 0) { 
           $status = "user dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
       } else  {
		   $insert_id = $this->syst_m_user->insertUserModule( $data_post );
	   }
	}
   
   function deleteUserModule(){
	   $id = str_replace(" ","",$this->uri->segment(4));
       $insert_id = $this->syst_m_user->deleteUserModule($id);
   }
   /* end module access */
	
   /* menu access */
   function search_user_menu(){
	   $uid = $this->uri->segment(4);
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_user->read_userco_menu($uid,$searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_user->read_userco_menu($uid);
	   }
	   echo json_encode($get);
   }
   
   function insertUserMenu(){
	   $data_post['LOGINID'] = str_replace(" ","",$this->input->post( 'LOGINID' ));
       $data_post['MENU_ID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
				
	   $data_menu = $this->syst_m_user->cek_exist_menu($data_post['LOGINID'], $data_post['MENU_ID']);
	   if($data_menu > 0) { 
           $status = "user dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
       } else  {
		   $insert_id = $this->syst_m_user->insertUserMenu( $data_post );
	   }
	}
   
   function deleteUserMenu(){
	   $id = str_replace(" ","",$this->uri->segment(4));
       $insert_id = $this->syst_m_user->deleteUserMenu( $id );
   }
	/* end menu access */
}

?>