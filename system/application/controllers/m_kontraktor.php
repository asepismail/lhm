<?php
class m_kontraktor extends Controller
{
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_kontraktor');
        $this->load->model('model_c_user_auth');  

        $this->load->library('form_validation');
        
        $this->lastmenu="m_kontraktor";
    }
    
    function index(){
        $view="info_m_kontraktor";
        
        $data = array();
        $data['judul_header'] = "Data Kontraktor";
        $data['js'] = "";
    
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['grid_name'] = "list_kontraktor";
        $data['grid_pager'] ="pager_kontraktor";

        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_kontraktor->LoadData($company));
    }
    
    function LoadData_Kendaraan(){
        
        $kode_kontraktor = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_m_kontraktor->LoadData_Kendaraan($kode_kontraktor,$company));
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
                $this->create_kontraktor($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_kontraktor($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADDVH"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->create_kendaraan($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDITVH"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_kendaraan($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DELKTR"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_kontraktor($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DELVH"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_kendaraan($data_id);    
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
    
    function create_kontraktor($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        //$this->global_func->createMy_ID('m_kontraktor','KODE_KONTRAKTOR',$company."TBG");
        $data_post['KODE_KONTRAKTOR'] = $this->global_func->createMy_ID('m_kontraktor','KODE_KONTRAKTOR',$company."KTK","INPUT_DATE",$company);
        $data_post['KODE_INISIAL'] = strtoupper(trim(htmlentities($data_id['KODE_INISIAL'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_KONTRAKTOR'] = strtoupper(trim(htmlentities($data_id['NAMA_KONTRAKTOR'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_CONTACT']=strtoupper(trim(htmlentities($data_id['NAMA_CONTACT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_CONTACT'] =trim(htmlentities($data_id['NO_CONTACT'],ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT']  =strtoupper(trim(htmlentities($data_id['ALAMAT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['KOTA'] = strtoupper(trim(htmlentities($data_id['KOTA'],ENT_QUOTES,'UTF-8')));
        $data_post['KODE_POS'] = trim(htmlentities($data_id['KODE_POS'],ENT_QUOTES,'UTF-8')); 
        $data_post['PROPINSI'] = strtoupper(trim(htmlentities($data_id['PROPINSI'],ENT_QUOTES,'UTF-8'))); 
        $data_post['TELEPON'] = trim(htmlentities($data_id['TELEPON'],ENT_QUOTES,'UTF-8')); 
        $data_post['EMAIL'] = trim(htmlentities($data_id['EMAIL'],ENT_QUOTES,'UTF-8')); 
        $data_post['BANK'] = strtoupper(trim(htmlentities($data_id['BANK'],ENT_QUOTES,'UTF-8'))); 
        $data_post['NO_REKENING'] = trim(htmlentities($data_id['NO_REKENING'],ENT_QUOTES,'UTF-8')); 
        $data_post['NPWP'] = trim(htmlentities($data_id['NPWP'],ENT_QUOTES,'UTF-8'));
        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;  
            
        $status='';
        if (empty($data_post['KODE_KONTRAKTOR']) || trim($data_post['KODE_KONTRAKTOR'])==''){
            $status = "Harap isi Kode Kontraktor";
            $return['status']=$status;
            $return['error']=true;   
        }
        
        if (empty($data_post['KODE_INISIAL']) || trim($data_post['KODE_INISIAL'])==''){
            $status = "Harap isi Kode Inisial Kontraktor";
            $return['status']=$status;
            $return['error']=true;   
        }
        
        if (empty($data_post['NAMA_KONTRAKTOR']) || trim($data_post['NAMA_KONTRAKTOR'])==''){
            $status = "Harap isi Nama Kontraktor";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['NAMA_KONTRAKTOR']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
         
        if(empty($status)){     
            $insert_id = $this->model_m_kontraktor->add_new($data_post['KODE_KONTRAKTOR'],$company,$data_post);
            $return['status']=  "detail: ".$insert_id;
            $return['error']=false;
            echo json_encode($return);         
        }else{
            echo json_encode($return);
        }  
    }
    
    function update_kontraktor($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        //$this->global_func->createMy_ID('m_kontraktor','KODE_KONTRAKTOR',$company."TBG");
        //$data_post['KODE_KONTRAKTOR'] = $this->global_func->createMy_ID('m_kontraktor','KODE_KONTRAKTOR',$company."TBG");
        $data_post['KODE_INISIAL'] = strtoupper(trim(htmlentities($data_id['KODE_INISIAL'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_KONTRAKTOR'] = strtoupper(trim(htmlentities($data_id['NAMA_KONTRAKTOR'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NAMA_CONTACT']=strtoupper(trim(htmlentities($data_id['NAMA_CONTACT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_CONTACT'] =trim(htmlentities($data_id['NO_CONTACT'],ENT_QUOTES,'UTF-8'));
        $data_post['ALAMAT']  =strtoupper(trim(htmlentities($data_id['ALAMAT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['KOTA'] = strtoupper(trim(htmlentities($data_id['KOTA'],ENT_QUOTES,'UTF-8')));
        $data_post['KODE_POS'] = trim(htmlentities($data_id['KODE_POS'],ENT_QUOTES,'UTF-8')); 
        $data_post['PROPINSI'] = strtoupper(trim(htmlentities($data_id['PROPINSI'],ENT_QUOTES,'UTF-8'))); 
        $data_post['TELEPON'] = trim(htmlentities($data_id['TELEPON'],ENT_QUOTES,'UTF-8')); 
        $data_post['EMAIL'] = trim(htmlentities($data_id['EMAIL'],ENT_QUOTES,'UTF-8')); 
        $data_post['BANK'] = strtoupper(trim(htmlentities($data_id['BANK'],ENT_QUOTES,'UTF-8'))); 
        $data_post['NO_REKENING'] = trim(htmlentities($data_id['NO_REKENING'],ENT_QUOTES,'UTF-8')); 
        $data_post['NPWP'] = trim(htmlentities($data_id['NPWP'],ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
        //$data_post['COMPANY_CODE'] = $company;  
            
        $status='';
        $id=strtoupper(trim(htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8')));
        if (empty($id) || $id==''){
            $status = "Harap isi Kode Kontraktor";   
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_post['KODE_INISIAL']) || trim($data_post['KODE_INISIAL'])==''){
            $status = "Harap isi Kode Inisial Kontraktor";
            $return['status']=$status;
            $return['error']=true;   
        }
        
        if (empty($data_post['NAMA_KONTRAKTOR']) || trim($data_post['NAMA_KONTRAKTOR'])==''){
            $status = "Harap isi Nama Kontraktor";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['NAMA_KONTRAKTOR']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }

        if(empty($status)){
                    
            $update_id = $this->model_m_kontraktor->update_data($id,$company,$data_post);
            $return['status']=  "detail: ".$update_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }     
    }
    
    function create_kendaraan($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')));
        $data_post['KODE_KONTRAKTOR']=strtoupper(trim(htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESKRIPSI']=strtoupper(trim(htmlentities($data_id['DESKRIPSI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NOTE'] =trim(htmlentities($data_id['NOTE'],ENT_QUOTES,'UTF-8'));
        $data_post['INPUT_BY']  =trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company; 
        
        $status='';
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $status = "Harap isi No Kendaraan";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){
                    
            $create_id = $this->model_m_kontraktor->add_new_kendaraan($data_post['KODE_KONTRAKTOR'],$data_post['NO_KENDARAAN'],$company,$data_post);
            $return['status']=  "detail: ".$create_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }    
    }
    
    function update_kendaraan($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //$data_post['ID_KENDARAAN_KONTRAKTOR'] = strtoupper(trim(htmlentities($data_id['ID_KENDARAAN_KONTRAKTOR'],ENT_QUOTES,'UTF-8')));
        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')));
        $data_post['KODE_KONTRAKTOR']=strtoupper(trim(htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DESKRIPSI']=strtoupper(trim(htmlentities($data_id['DESKRIPSI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NOTE'] =trim(htmlentities($data_id['NOTE'],ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_BY']  =trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
        $data_post['COMPANY_CODE'] = $company; 
        
        $status='';
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $status = "Harap isi No Kendaraan";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_id['ID_KENDARAAN_KONTRAKTOR']) || trim($data_id['ID_KENDARAAN_KONTRAKTOR'])==''){
            $status = "Harap isi ID Kendaraan";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['ID_KENDARAAN_KONTRAKTOR']) > 50){
            $status  ="Panjang karakter melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){
                    
            $create_id = $this->model_m_kontraktor->update_kendaraan($data_post['KODE_KONTRAKTOR'],$data_id['ID_KENDARAAN_KONTRAKTOR']
                                                                        ,$data_post['NO_KENDARAAN'],$company,$data_post);
            $return['status']=  "Detail: ".$create_id." - Update Berhasil ";
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function delete_kendaraan($data_id){
        $return['status']='';
        $return['error']=false;
        
        $no_kend = trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $kode_kontraktor = trim(htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($no_kend) || trim($no_kend)==='' || $no_kend===false){
            $status = "NO_KENDARAAN KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($no_kend) > 50){
            $return['status']="Panjang karakter NOPOL melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($kode_kontraktor) || trim($kode_kontraktor)==='' || $kode_kontraktor===false){
            $status = "KODE_KONTRAKTOR KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($kode_kontraktor) > 50){
            $return['status']="Panjang karakter KODE_KONTRAKTOR melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_m_kontraktor->delete_kendaraan($no_kend,$kode_kontraktor,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function delete_kontraktor($data_id){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_kontraktor = htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8');//strtoupper(trim(htmlentities($data_id['KODE_KONTRAKTOR'],ENT_QUOTES,'UTF-8'))) ;
        
        $return['status']='';
        $return['error']=false;

        if (empty($id_kontraktor) || trim($id_kontraktor)==='' || $id_kontraktor===false){
            $status = "KODE_KONTRAKTOR KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($id_kontraktor) > 50){
            $return['status']="Panjang karakter KODE_KONTRAKTOR melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_kontraktor = $this->model_m_kontraktor->delete_kontraktor($id_kontraktor,$company);
            $return['status']=  $delete_kontraktor;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }   
    }
    
    function search_data(){
        $nama = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_m_kontraktor->search_data($nama,$company)); 
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_m_kontraktor->data_search($data['rules'], $company));      
    }
}
?>
