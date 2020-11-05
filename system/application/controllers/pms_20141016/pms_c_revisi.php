<?php
class pms_c_revisi extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_revisi');
		$this->load->model('pms/pms_m_pengajuan');
		$this->load->model('pms/pms_m_master_budget');
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
        $view="pms/pms_v_revisi";
        $this->data['js'] = "";
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		$this->data['dept'] = $this->dropdownlist("i_dept","style='width:220px;'","tabindex='1'","dept","DEPT_CODE","DEPT_DESCRIPTION");
        if ($this->data['login_id'] == TRUE){
            $this->load->view($view, $this->data);
        } else {
            redirect('login');
        }
    }
	
	function dropdownlist($name, $style, $tab, $type, $val, $desc)
	{
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$string = "<select  name='".$name."' ".$tab." class='select' id='".$name."' ".$style." >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = "";
		if($type == "dept"){
			$data = $this->pms_m_pengajuan->get_dept();
		} else if($type == "afd"){
			$data = $this->pms_m_pengajuan->get_afd($company);
		} else if($type == "sat"){
			$data = $this->pms_m_master_budget->get_satuan();
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
	
	/* grid utama */
    function listProject()
    {
		$qwhere = $this->uri->segment(4);
		$company= htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->pms_m_revisi->LoadDataProject($company, $qwhere));
    }
	/* grid utama */
    function read_ppj_revisi()
    {
		$company= htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->pms_m_revisi->read_ppj_rev($company));
    }
	
	/* project yang akan direvisi */
	function read_revproject(){
		$pj = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->pms_m_revisi->read_ppj_rev($pj, $company));
	}
	/* ########### generate no pengajuan & project sementara*/
	function ext_genNoPengajuan(){
		$company = $this->session->userdata('DCOMPANY');
		$noperusahaan = '';
	    $coid = $this->pms_m_pengajuan->get_company_id($company);
        foreach ($coid as $drow){
			$noperusahaan = $drow['COMPANY_NUMBER'];
		}
		$begID = "PPJR-".$noperusahaan.substr(date('Y'),2,2);
		$nopengajuan = $this->global_func->id_GAD('pms_project_propnum','PROJECT_PROPNUM_NUMID',$begID);
		echo trim($nopengajuan);   
    }
	
	function cekNotComplete(){
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$dataPj = $this->pms_m_revisi->cekNotComplete($company);
		$data = "";
		foreach($dataPj as $row){
			$data .= $row['jumlah'] . "~" . $row['PROJECT_PROPNUM_NUMID'] . "~" . 
						$row['PROJECT_PROPNUM_DATE'] . "~" . $row['PROJECT_PROPNUM_PELAKSANA'] . "~" . $row['PROJECT_DEPT']
						 . "~" . $row['PROJECT_FINISH_TARGET'];
		}
		echo $data;
	}
}
?>