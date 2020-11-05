<?php
class s_close_bjr extends Controller{
    private $lastmenu;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_close_bjr');
        $this->load->model('model_c_user_auth');
    }
    
    function index(){
        $view="info_s_close_bjr";
        
        $data = array();
        $data['judul_header'] = "Closing data BJR";
        $data['js'] = "";
    
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');

        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function close_bjr(){
        $return['status']='';
        $return['error']=false;
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $periode = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode_to = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $jns_transaksi = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8')); 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar); 
        
        $ar2 = preg_split('/[- :]/',trim($periode_to));
        $ar2 = implode('',$ar2);
        
        if(empty($return['status']) && $return['error']===false){ 
            $close_data = $this->model_s_close_bjr->close_bjr($periode, $periode_to,$jns_transaksi ,$company);
            $return['status']=  $close_data;
            $return['error']=false;
            echo json_encode($return);          
        }else{
            echo json_encode($return);
        }
           
    }
}
?>