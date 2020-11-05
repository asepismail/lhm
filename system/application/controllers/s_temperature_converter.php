<?php
class s_temperature_converter extends Controller{
    private $lastmenu;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_temperature_converter');
        $this->load->model('model_c_user_auth');
          
        $this->load->library('form_validation');
        $this->load->library('form_validation');
        $this->lastmenu="info_s_temperature_converter";
    }
    
    function index(){
        $this->output->cache(3);
        $view="info_s_temperature_converter";
        
        $data = array();
        $data['judul_header'] = "Temperature Converter";
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
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_temperature_converter->data_search($data['rules'], $company));    
    }
    
    function LoadData(){
        //$opt= htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        //$no_tiket = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_temperature_converter->LoadData($company,$periode));   
    }
    
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
        
        if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADD"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->insert_temperature($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_temperature($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_temperature($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "PRINT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $print_type = $this->uri->segment('3');
                $this->print_nota($data_id,$print_type);    
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
    
    function insert_temperature($data_id){
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $data_post['TEMPERATURE'] = strtoupper(trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['BJ'] = strtoupper(trim(htmlentities($data_id['BJ'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['BJ_CORRECTION'] = strtoupper(trim(htmlentities($data_id['BJ_CORRECTION'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8')));
            $data_post['COMPANY_CODE'] = $company;
            $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            
            if ($this->form_validation->is_numeric($data_post['TEMPERATURE'])==FALSE){
                throw new Exception("TEMPERATURE harus angka!!");    
            }
            
            if (empty($data_post['TEMPERATURE']) || trim($data_post['TEMPERATURE'])==''){
                throw new Exception("Harap isi TEMPERATURE");
            }elseif(strlen($data_post['TEMPERATURE']) > 25){
                throw new Exception("Panjang karakter TEMPERATURE melebihi batas maksimal");
            }
            
            if (empty($data_post['BJ']) || trim($data_post['BJ'])==''){
                throw new Exception("Harap isi BJ");
            }elseif(strlen($data_post['BJ']) > 25){
                throw new Exception("Panjang karakter BJ melebihi batas maksimal");
            }
            
            if (empty($data_post['BJ_CORRECTION']) || trim($data_post['BJ_CORRECTION'])==''){
                throw new Exception("Harap isi BJ_CORRECTION");
            }elseif(strlen($data_post['BJ_CORRECTION']) > 25){
                throw new Exception("Panjang karakter BJ_CORRECTION melebihi batas maksimal");
            }
            
            if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
                throw new Exception("Harap isi ID_STORAGE");
            }elseif(strlen($data_post['ID_STORAGE']) > 25){
                throw new Exception("Panjang karakter ID_STORAGE melebihi batas maksimal");
            }
            
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_temperature_converter->add_new($data_post['TEMPERATURE'], $data_post['ID_STORAGE'], $company, $data_post);
                $return['status']=  $insert_id;
                $return['error']=false;
                echo json_encode($return);          
            }else{
                echo json_encode($return);
            } 
            
               
        }catch(Exception $e){
            $return['status'] = $e->getMessage();
            $return['error']=true;
            echo json_encode($return);    
        }    
    }
    
    function update_temperature($data_id){
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $now   = new DateTime;

            $id_anon= strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['TEMPERATURE'] = strtoupper(trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['BJ'] = strtoupper(trim(htmlentities($data_id['BJ'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['BJ_CORRECTION'] = strtoupper(trim(htmlentities($data_id['BJ_CORRECTION'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_TIME'] = $now->format( 'Y-m-d h:m:s' );
            
            if ($this->form_validation->is_numeric($data_post['TEMPERATURE'])==FALSE){
                throw new Exception("TEMPERATURE harus angka!!");    
            }
            
            if (empty($data_post['TEMPERATURE']) || trim($data_post['TEMPERATURE'])==''){
                throw new Exception("Harap isi TEMPERATURE");
            }elseif(strlen($data_post['TEMPERATURE']) > 25){
                throw new Exception("Panjang karakter TEMPERATURE melebihi batas maksimal");
            }
            
            if (empty($data_post['BJ']) || trim($data_post['BJ'])==''){
                throw new Exception("Harap isi BJ");
            }elseif(strlen($data_post['BJ']) > 25){
                throw new Exception("Panjang karakter BJ melebihi batas maksimal");
            }
            
            if (empty($data_post['BJ_CORRECTION']) || trim($data_post['BJ_CORRECTION'])==''){
                throw new Exception("Harap isi BJ_CORRECTION");
            }elseif(strlen($data_post['BJ_CORRECTION']) > 25){
                throw new Exception("Panjang karakter BJ_CORRECTION melebihi batas maksimal");
            }
            
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_temperature_converter->update_temperature($id_anon,$company,$data_post);
                $return['status']=  $insert_id;
                $return['error']=false;
                echo json_encode($return);          
            }else{
                echo json_encode($return);
            } 
          
        }catch(Exception $e){
            $return['status'] = $e->getMessage();
            $return['error']=true;
            echo json_encode($return);    
        }    
    }
    
    function delete_temperature($data_id){
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $id_anon= strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;
            
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_temperature_converter->delete_temperature($id_anon,$company);
                $return['status']=  $insert_id;
                $return['error']=false;
                echo json_encode($return);          
            }else{
                echo json_encode($return);
            } 
          
        }catch(Exception $e){
            $return['status'] = $e->getMessage();
            $return['error']=true;
            echo json_encode($return);    
        }    
    }
     
    function get_storage(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); 
        $data_storage = $this->model_s_temperature_converter->get_storage($company,$q);
         
        //echo $q;
        $storage = array();
        foreach($data_storage as $row)
        {
            $storage[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['PRODUCT_CODE'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$storage).']'; exit;     
    }
}
?>