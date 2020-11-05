<?php
class main_c extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/main_m_pms');
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
		$this->lastmenu="main_c";
    }
	
	 function index(){
        $view="layout/pms_index";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
		//$this->data['company'] = $this->dropdownlist("i_company","style='width:260px;'","tabindex='1'","comp","COMPANY_CODE","COMPANY_NAME");
        $this->data['menupms']=$this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
        if ($this->data['login_id'] == TRUE){
            $this->load->view($view, $this->data);
        } else {
            redirect('login');
        }
    }
}

?>