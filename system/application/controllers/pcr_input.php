<?php
class pcr_input extends Controller{
    private $lastmenu;
    function __Construct(){
        parent::__Construct();
        
        $this->load->model('model_pcr_input');
        $this->load->model('model_c_user_auth');
        
        $this->load->library('form_validation');
        $this->lastmenu='pcr_input';    
    }
    
    function index(){
        $this->output->cache(3);
        $view="info_pcr_input";
        
        $data = array();
        $data['judul_header'] = "Procurement Module";
        $data['js'] = "";
    
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        //$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');

        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
}
?>