<?php
class adem_c_monitoring_kendaraan extends Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('adem_m_monitoring'); 
        $this->load->model('model_c_user_auth');
        
        $this->lastmenu="adem_c_monitoring_kendaraan";
        
    }
    function index(){
        $view="adem_v_monitoring_kendaraan";
        
        //$data = array();
        $this->data['judul_header'] = "Monitoring Kendaraan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
		$this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$periode = $this->uri->segment(3);
        echo json_encode($this->adem_m_monitoring->LoadData($company, $periode));   
    }    
}
?>