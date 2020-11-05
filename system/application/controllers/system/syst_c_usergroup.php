<?php
class syst_c_usergroup extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('system/syst_m_usergroup');
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
      $view="system/syst_v_usergroup";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Master Data Group Pengguna";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
	  
	  if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   function search_usergroup(){
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_usergroup->read_ugroup($searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_usergroup->read_ugroup();
	   }
	   echo json_encode($get);
   }
   
   function insertDataGroup(){
		$data_post['USER_GROUP_ID'] = str_replace(" ","",$this->input->post( 'USER_GROUP_ID' ));
        $data_post['USER_GROUP_NAME'] = $this->input->post( 'USER_GROUP_NAME' );
		$data_Group = $this->syst_m_usergroup->cek_existGroup(str_replace(" ","",$this->input->post( 'USER_GROUP_ID' )));
		if($data_Group > 0) { 
           $status = "Group dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
        } else  {
		   $insert_id = $this->syst_m_usergroup->insert_Group( $data_post );
		}
	}
	
	function updateDataGroup(){
		$data_post['USER_GROUP_ID'] = str_replace(" ","",$this->input->post( 'USER_GROUP_ID' ));
        $data_post['USER_GROUP_NAME'] = $this->input->post( 'USER_GROUP_NAME' );
		$insert_id = $this->syst_m_usergroup->update_Group( $data_post['USER_GROUP_ID'], $data_post );
	}
	
	function deleteDataGroup(){
		$data_post['USER_GROUP_ID'] = str_replace(" ","",$this->input->post( 'USER_GROUP_ID' ));
		$insert_id = $this->syst_m_usergroup->delete_Group( $data_post['USER_GROUP_ID'] );
		if ($insert_id > 0){
			$insert_id_role = $this->syst_m_usergroup->delete_GroupRole( $data_post['USER_GROUP_ID'] );
			if ($insert_id < 1){
				echo "data gagal terhapus";
			} 
		}
	}
	
	/* -------------------------------------------------------------------------------------------------- */
   
   function search_ugRole(){
	  $grole = $this->uri->segment(4);
	  $get = $this->syst_m_usergroup->read_ugRole($grole);
	  echo json_encode($get);
   }
   
   function search_ugExport(){
	  $grole = $this->uri->segment(4);
	  $get = $this->syst_m_usergroup->read_ugExport($grole);
	  echo json_encode($get);
   }
   
   function search_export_group(){
	   $company = $this->session->userdata('DCOMPANY');
	   $get = "";
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->syst_m_usergroup->read_export($searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->syst_m_usergroup->read_export();
	   }

	   echo json_encode($get);
   }
    function insertDataRole(){
		$data_post['USER_GROUP_ID'] = str_replace(" ","",$this->input->post( 'USER_GROUP_ID' ));
        $data_post['MENU_ID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
		$data_Group = $this->syst_m_usergroup->cek_existRole($data_post['USER_GROUP_ID'], $data_post['MENU_ID']);
		if($data_Group > 0) { 
           $status = "Group dengan kode ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
        } else  {
		   $insert_id = $this->syst_m_usergroup->insert_Role( $data_post );
		}
	}
	
	function insertDataExport(){
		$data_post['GROLE'] = str_replace(" ","",$this->input->post( 'USER_GROUP_ID' ));
        $data_post['EXPORT_MEID'] = str_replace(" ","",$this->input->post( 'MENU_ID' ));
		$data_post['INACTIVE'] =0;
		$data_Group = $this->syst_m_usergroup->cek_existExport($data_post['GROLE'], $data_post['EXPORT_MEID']);
		if($data_Group > 0) { 
           $status = "Group ". $data_post['GROLE']. " dengan kode " .$data_post['EXPORT_MEID']." ini sudah ada mohon periksa kembali!!!"; 
		   echo $status;
        } else  {	
		   $insert_id = $this->syst_m_usergroup->insert_Export( $data_post );
		}
	}	
	function deleteDataRole(){
		$id = $this->uri->segment(4);
		$insert_id = $this->syst_m_usergroup->delete_Role( $id );
	}
	function deleteDataExport(){
		$id = $this->uri->segment(4);
		$insert_id = $this->syst_m_usergroup->delete_Export( $id );
	}
}

?>