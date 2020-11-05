<?php
class main_c_pms extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/main_m_pms');
        $this->load->model('model_c_user_auth');
		$this->load->model('pms/pms_m_monitoring');
		$this->load->model('model_project_pengajuan');
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
        $view="pms/main_v_pms";
        //$this->data['judul_header'] = "DASHBOARD";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
        $this->data['safd'] = $this->dropdownlist_safd();
		$this->data['company'] = $this->dropdownlist("i_company","style='width:260px;'","tabindex='1'","comp","COMPANY_CODE","COMPANY_NAME");
        $this->data['menupms']=$this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
        if ($this->data['login_id'] == TRUE){
            $this->load->view($view, $this->data);
        } else {
            redirect('login');
        }
    }
	
	function LoadData() 
	{
        $company=$this->session->userdata('DCOMPANY');
        echo json_encode($this->model_project_pengajuan->LoadData($company));
    }
	
	/* detail project */
	function LoadDetail() 
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$id = $this->uri->segment(3);
        $company=$this->session->userdata('DCOMPANY');
		echo json_encode($this->model_project_pengajuan->getdetailact($id, $company, $limit, $page, $sidx, $sord));
	}
	
	function SearchData() 
	{
        $getID =htmlentities(mysql_escape_string($this->uri->segment(4))); 
        $getAfd=htmlentities(mysql_escape_string($this->uri->segment(5)));
        $getType=htmlentities(mysql_escape_string($this->uri->segment(6)));
        $getDesc=htmlentities(mysql_escape_string($this->uri->segment(7)));
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $company=$this->session->userdata('DCOMPANY');
        if ($getID =="" && $getAfd=="" && $getDesc=="" && $getType=="") {
            echo json_encode($this->model_project_pengajuan->LoadData($company));
        } else {
            echo json_encode($this->model_project_pengajuan->search_prj($getID,$getAfd,$getType,$getDesc, $limit, $page, $sidx, $sord));
        }
    }
	
	function dropdownlist_safd()
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='search_afd' class='select' id='search_afd' onchange='doSearch(arguments[0]||event)' style='width:120px;' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_project_pengajuan->get_afd($company);
		
		foreach ( $data_afd as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	/* dropdown company */
	function dropdownlist($name, $style, $tab, $type, $val, $desc)
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='".$name."' ".$tab." class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = "";
		if($type == "comp"){
				$data = $this->pms_m_monitoring->get_company();
		} 
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
}
?>
