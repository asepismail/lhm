<?php
class s_closing_control extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('model_s_closing_control');
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
      $view="info_s_closing_control";
      $this->data['js'] = "";
	  $this->data['judul_header'] = "Closing Period Control";
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
   
   function search_period(){
	   if( $this->input->post( '_search' ) == "true" ) {
	   		$searchField = $this->input->post( 'searchField' );
			$searchString = $this->input->post( 'searchString' );
			$searchOper = $this->input->post( 'searchOper' );
			$get = $this->model_s_closing_control->read_ugroup($searchField, $searchString, $searchOper);
	   } else {
		   	$get = $this->model_s_closing_control->read_ugroup();
	   }
	   echo json_encode($get);
   }  
 
	function updateDataPeriod(){
		$data_post['PERIODE_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_ID' ));
        $data_post['PERIODE_NAME'] = $this->input->post('PERIODE_NAME');
		$data_post['PERIODE_START'] = $this->input->post('PERIODE_START');
		$data_post['PERIODE_END'] = $this->input->post('PERIODE_END');
		$data_post['COMPANY_CODE'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_post['ISCLOSE'] = $this->input->post('ISCLOSE');
		$data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
		$data_post['CLOSE_DATE'] =  $this->global_func->gen_datetime();
		$insert_id = $this->model_s_closing_control->update_period( $data_post['PERIODE_ID'], $data_post );
	}
	
	function updatePeriodControl(){
		
		$data_post['PERIODE_CONTROL_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_CONTROL_ID' ));
		/*
		$data_post['PERIODE_ID'] = str_replace(" ","",$this->input->post( 'PERIODE_ID' ));
        $data_post['PERIODE_NAME'] = $this->input->post('PERIODE_NAME');
		$data_post['PERIODE_START'] = $this->input->post('PERIODE_START');
		$data_post['PERIODE_END'] = $this->input->post('PERIODE_END');
		$data_post['COMPANY_CODE'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		*/
		$data_post['ISCLOSE'] = $this->input->post('ISCLOSE');
		if ($data_post['ISCLOSE']==1){
			$data_post['CLOSE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['CLOSE_DATE'] =  $this->global_func->gen_datetime();
		}else if ($data_post['ISCLOSE']==0){		
			$data_post['REOPEN_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));;
			$data_post['REOPEN_DATE'] =  $this->global_func->gen_datetime();	
		}
		$insert_id = $this->model_s_closing_control->update_pControl( $data_post['PERIODE_CONTROL_ID'], $data_post );
	}
	/* -------------------------------------------------------------------------------------------------- */
   
   function search_pControl(){
	  $period_id = $this->uri->segment(3);
	  $get = $this->model_s_closing_control->read_pControl($period_id);	  
	  echo json_encode($get);
   }
   
}

?>