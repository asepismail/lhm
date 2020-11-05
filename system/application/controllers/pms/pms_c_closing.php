<?php
class pms_c_closing extends Controller{
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
        $view="pms/pms_v_closing";
        $this->data['js'] = "";
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		
		$this->data['RevAfd'] = $this->dropdownlist("i_RevAfd","style='width:140px;'","tabindex='4'","afd","AFD_CODE","AFD_DESC");
        $this->data['RevSatuan'] = $this->dropdownlist("i_RevSatuan","style='width:120px;'","tabindex='16'","sat","UNIT_CODE","UNIT_DESC");
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
    function read_ppj_close()
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
		$begID = "PPJC-".$noperusahaan.substr(date('Y'),2,2);
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
	
	function read_detail_ppj()
    {
		$pjs= $this->uri->segment(4);
        echo json_encode($this->pms_m_pengajuan->read_detail_ppj($pjs));
    }
	
	function insert_header(){
		$company = $this->session->userdata('DCOMPANY');
		$ppj = trim($this->input->post( 'PROJECT_PROPNUM_NUMID' ) ); 
        $data_post['PROJECT_PROPNUM_NUMID'] = $ppj;
		//$data_post['PROJECT_DEPT'] = trim($this->input->post( 'PROJECT_DEPT' ) ); 
		$data_post['PROJECT_PROPNUM_DATE'] = trim($this->input->post( 'PROJECT_PROPNUM_DATE' ) ) ; 
		//$data_post['PROJECT_PROPNUM_PELAKSANA'] = trim($this->input->post( 'PROJECT_PROPNUM_PELAKSANA' ) ); 
		$data_post['COMPANY_CODE'] = trim($company); 
		
		$dataHeader = $this->pms_m_pengajuan->cek_header($company, $ppj);
		$cekdata = count($dataHeader);
		if($cekdata > 0){
			$data_post['UPDATEBY'] = $this->session->userdata('LOGINID');
			$data_post['UPDATEDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->update_header( $ppj, $data_post );
		} else {
			$data_post['INPUTBY'] = $this->session->userdata('LOGINID');
			$data_post['INPUTDATE'] = date ("Y-m-d H:i:s");
			$insert_id = $this->pms_m_pengajuan->insert_header( $data_post );
		}
	}
}
?>