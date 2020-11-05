<?php
class s_po_tbs extends Controller{
    private $lastmenu;
    function __Construct(){
        parent::__Construct();
        $this->load->model('model_s_po_tbs');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation'); 
    }
    
    function index(){
        $this->output->cache(3);
        $view="info_s_po_tbs";
        
        $data = array();
        $data['judul_header'] = "PO TBS";
        $data['js'] = "";
    
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');

        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_po_tbs->LoadData($periode,$company));   
    }
    
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_m_storage->search_data($kode_storage, $company));
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_po_tbs->data_search($data['rules'], $company));
    }
    
    function LoadData_POAdem(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_po_tbs->get_adem_potbs($company));   
    }
    
    function get_supplier(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_supplier = $this->model_s_po_tbs->get_supplier($company,$q);
         
        //echo $q;
        $supplier = array();
        foreach($data_supplier as $row)
        {
            $supplier[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERCODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERNAME'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['SUPPLIERNAME'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$supplier).']'; exit;     
    }
    
    function CRUD_METHOD(){
        $loginid=trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
        
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
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "PRINT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $print_type = $this->uri->segment('3');
                $this->print_po($data_id,$print_type);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADDPOADEM"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->add_poadem($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "SYNC"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->sync_data($data_id);    
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
    
    function add_poadem($data_id){
        $return['status']='';
        $return['error']=FALSE;
        //$company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
            $data_post['PO_NUMBER'] = strtoupper(trim(htmlentities($data_id['PO_NUMBER'],ENT_QUOTES,'UTF-8')));
            $data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))); 
            $data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8'))); 
            $data_post['QTYORDERED'] = strtoupper((float) str_replace(',', '', trim(htmlentities($data_id['QTYORDERED'],ENT_QUOTES,'UTF-8')))); 
            $data_post['PRICELIST'] = strtoupper((float) str_replace(',', '', trim(htmlentities($data_id['PRICELIST'],ENT_QUOTES,'UTF-8')))); 
            $data_post['DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8')));
            $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['COMPANY_CODE'] = $company;
             
            if (empty($data_post['PO_NUMBER']) || trim($data_post['PO_NUMBER'])==''){
                throw new Exception("Harap isi Nomor PO");
            }elseif(strlen($data_post['PO_NUMBER']) > 50){
                throw new Exception("Panjang karakter Nomor PO melebihi batas maksimal");
            }
            
            if ($this->form_validation->is_numeric($data_post['QTYORDERED'])==FALSE){
                throw new Exception("PRICELIST harus angka!!");    
            }
            if ($this->form_validation->is_numeric($data_post['PRICELIST'])==FALSE){
                throw new Exception("PRICELIST harus angka!!");    
            }       
            
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_po_tbs->add_new_poadem($data_post['PO_NUMBER'],$data_post);
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

         try{
            $id_anon=strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;
            $po_number=strtoupper(trim(htmlentities($data_id['PO_NUMBER'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['PRICELIST'] = strtoupper(trim(htmlentities($data_id['PRICELIST'],ENT_QUOTES,'UTF-8')));
            $data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8')));
            $data_post['TANGGALK'] = strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8')));
            $data_post['DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8'))); 
            $data_post['SINKRON_STATUS'] = 0; 
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
             
            if (empty($po_number) || trim($po_number)==''){
                throw new Exception("Nomor PO tidak boleh null!!");              
            }elseif(strlen($po_number) > 50){
                throw new Exception("panjang karakter Nomor PO melebihi batas!!");;
            }
            
            if (empty($id_anon) || trim($id_anon)==''){
                throw new Exception("id_anon tidak boleh null!!");              
            }elseif(strlen($id_anon) > 10){
                throw new Exception("panjang karakter id_anon melebihi batas!!");;
            }
            
            if(empty($return['status']) && $return['error']===false){     
                $update_id = $this->model_s_po_tbs->update_data($id_anon, $po_number, $data_post);
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
        
        $id_anon = strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;  
        $po_number = strtoupper(trim(htmlentities($data_id['PO_NUMBER'],ENT_QUOTES,'UTF-8'))) ;  
        if (empty($id_anon) || trim($id_anon)==='' || $id_anon===false){
            $return['status']="id_anon KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_anon) > 10){
            $return['status']="Panjang karakter id_anon melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($po_number) || trim($po_number)==='' || $po_number===false){
            $return['status']="Nomor PO KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($po_number) > 50){
            $return['status']="Panjang karakter Nomor PO melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_po_tbs->delete_data($id_anon,$po_number);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }      
    }
    
    function sync_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $id_anon = strtoupper(trim(htmlentities($data_id['ID_ANON'],ENT_QUOTES,'UTF-8'))) ;  
        $po_number = strtoupper(trim(htmlentities($data_id['PO_NUMBER'],ENT_QUOTES,'UTF-8'))) ; 
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));  
        $data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))); 
        $data_post['PO_NUMBER'] = strtoupper(trim(htmlentities($data_id['PO_NUMBER'],ENT_QUOTES,'UTF-8')));
        $data_post['QTYORDERED'] = strtoupper(trim(htmlentities($data_id['QTYORDERED'],ENT_QUOTES,'UTF-8')));
        $data_post['PRICELIST'] = strtoupper(trim(htmlentities($data_id['PRICELIST'],ENT_QUOTES,'UTF-8')));
        $data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8')));
        $data_post['TANGGALK'] = strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8')));
        $data_post['DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8')));
        $data_post['SINKRON_STATUS'] = 1;
        $data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8')));
        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
        $data_post['COMPANY_CODE'] = $company;
         
        if (empty($id_anon) || trim($id_anon)==='' || $id_anon===false){
            $return['status']="id_anon KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_anon) > 10){
            $return['status']="Panjang karakter id_anon melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($po_number) || trim($po_number)==='' || $po_number===false){
            $return['status']="Nomor PO KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($po_number) > 50){
            $return['status']="Panjang karakter Nomor PO melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $sync_id = $this->model_s_po_tbs->sync_data($id_anon,$po_number,$company,$data_post);
            $return['status']=  $sync_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }      
    }
}
?>