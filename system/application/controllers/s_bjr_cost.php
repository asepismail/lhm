<?php
class s_bjr_cost extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        
        $this->load->model('model_s_bjr_cost');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        
        $this->lastmenu='s_bjr_cost'; 
    }
    
    function index(){
        $view="info_s_bjr_cost";
        
        $this->data['judul_header'] = "Daftar Harga Angkut Buah Per-Afdeling per-Kg";
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
        
        echo json_encode($this->model_s_bjr_cost->LoadData($company));   
    }
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_pcr_input->data_search($data['rules'], $company));  
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
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['COST'] = strtoupper(trim(htmlentities($data_id['COST'],ENT_QUOTES,'UTF-8'))) ;

            $data_post['COMPANY_CODE'] = $company;
            $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
            
            if ($this->form_validation->is_numeric($data_post['COST'])==FALSE){
                throw new Exception("COST harus angka!!");    
            }
            
            if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
                throw new Exception("Harap isi AFD");
            }elseif(strlen($data_post['AFD']) > 25){
                throw new Exception("Panjang karakter AFD melebihi batas maksimal");
            }
            
            if (empty($data_post['COST']) || trim($data_post['COST'])==''){
                throw new Exception("Harap isi COST");
            }elseif(strlen($data_post['COST']) > 25){
                throw new Exception("Panjang karakter COST melebihi batas maksimal");
            }
            
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_bjr_cost->add_new($data_post['COMPANY_CODE'],$data_post);
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
    
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
         try{
            $id_anon=strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;
         
            $data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['COST'] = strtoupper(trim(htmlentities($data_id['COST'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['ACTIVE'] = strtoupper(trim(htmlentities($data_id['ACTIVE'],ENT_QUOTES,'UTF-8'))) ;

            $data_post['COMPANY_CODE'] = $company;
            $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
            $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
            
            if (empty($id_anon) || trim($id_anon)==''){
                throw new Exception("Harap isi id");
            }elseif(strlen($id_anon) > 25){
                throw new Exception("Panjang karakter id melebihi batas maksimal");
            }
        
            if(empty($return['status']) && $return['error']===false){     
                $update_id = $this->model_s_bjr_cost->update_costbjr($id_anon,$data_post['COMPANY_CODE'],$data_post);
                $return['status']=  $update_id;
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
    
    function delete_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_anon = strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;  
          
        if (empty($id_anon) || trim($id_anon)==='' || $id_anon===false){
            $return['status']="id KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_anon) > 50){
            $return['status']="Panjang karakter id melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_bjr_cost->delete_costbjr($id_anon,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }      
    }

    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_s_bjr_cost->get_afdeling($company,$q);
         
        $afdeling = array();
        foreach($data_afd as $row)
        {
            $afdeling[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_name:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_dl:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$afdeling).']'; exit;     
    }
}
?>