<?php
class m_storage extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_storage');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_storage";
        $this->data = array(); 
    }
    
    function index(){
        $view="info_m_storage";
        
        //$data = array();
        $this->data['judul_header'] = "Master Storage";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        //$this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        //$this->data['bjr_periode'] = $this->get_bjr_periode();
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_m_storage->LoadData($company));   
    }
    
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
        //echo $data_id['CRUD'];
        if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->add_new($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_data($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_data($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }else{
            $return['status'] ="Operation Unknown !!";
            $return['error']=true;
            echo json_encode($return);
        }         
    }
    
    function add_new($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['PRODUCT_CODE'] = strtoupper(trim(htmlentities($data_id['PRODUCT_CODE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESCRIPTION']=strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['MAXCAPACITY'] =strtoupper(trim(htmlentities($data_id['MAXCAPACITY'],ENT_QUOTES,'UTF-8')));
        $data_post['DIAMETER']=strtoupper(trim(htmlentities($data_id['DIAMETER'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['JENIS_ATAP'] =strtoupper(trim(htmlentities($data_id['JENIS_ATAP'],ENT_QUOTES,'UTF-8')));
        $data_post['ZERO_CAPACITY'] =strtoupper(trim(htmlentities($data_id['ZERO_CAPACITY'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        
        $validate_numeric=$this->validate_numeric($data_post['MAXCAPACITY']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai MAXCAPACITY harus angka";
            $return['error']=true;       
        }
        
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['PRODUCT_CODE']) || trim($data_post['PRODUCT_CODE'])==''){
            $return['status']="Harap isi PRODUCT_CODE";
            $return['error']=true;  
        }elseif(strlen($data_id['PRODUCT_CODE']) > 50){
            $return['status']="Panjang karakter PRODUCT_CODE melebihi batas maksimal";
            $return['error']=true;
        }
        
        
        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_m_storage->add_new($company,$data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);  
        }
    }
    
    function update_data($data_id){
        $return['status']='';
        $return['error']=FALSE;
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $id_storage = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        //$data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['PRODUCT_CODE'] = strtoupper(trim(htmlentities($data_id['PRODUCT_CODE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESCRIPTION']=strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['MAXCAPACITY'] =strtoupper(trim(htmlentities($data_id['MAXCAPACITY'],ENT_QUOTES,'UTF-8')));
        $data_post['DIAMETER']=strtoupper(trim(htmlentities($data_id['DIAMETER'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['JENIS_ATAP'] =strtoupper(trim(htmlentities($data_id['JENIS_ATAP'],ENT_QUOTES,'UTF-8')));
        $data_post['ZERO_CAPACITY'] =strtoupper(trim(htmlentities($data_id['ZERO_CAPACITY'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
        
        $validate_numeric=$this->validate_numeric($data_post['MAXCAPACITY']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai MAXCAPACITY harus angka";
            $return['error']=true;        
        }
        
        if (empty($id_storage)){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;   
        }elseif(strlen($id_storage) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['PRODUCT_CODE'])){
            $return['status']="Harap isi PRODUCT_CODE";
            $return['error']=true;  
        }elseif(strlen($data_id['PRODUCT_CODE']) > 50){
            $return['status']="Panjang karakter PRODUCT_CODE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $update_id = $this->model_m_storage->update_storage($id_storage,$data_post,$company);
            $return['status']=  $update_id;
            $return['error']=false;
            echo json_encode($return);            
        }else{
            echo json_encode($return);
        }   
    }
    
    function delete_data($data_id){
        $return['status']='';
        $return['error']=false;
        
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $id_storage = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        
        if (empty($id_storage)){
            $status = "ID_STORAGE KOSONG !!"; 
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;  
        }elseif(strlen($id_storage) > 50){
            $status  ="Panjang karakter ID melebihi batas maksimal";
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_m_storage->delete_storage($id_storage,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function validate_numeric($data){
        $numeric=$data;
        $result='';
        if(is_array($data))
        {
            while(list($key,$val)=each($data))
            {
                if(trim($val)=="" || $val==null)
                {
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val)))
                {
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        }else {
            if(trim($numeric)=="" || $numeric==null)
            {
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric))
            {
                $result='false';   
            }else{
                $result='true';
            }    
        }
        return $result;   
    }
    
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_m_storage->search_data($kode_storage, $company));
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_m_storage->data_search($data['rules'], $company));
    }
}
?>
