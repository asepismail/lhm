<?php
class s_bpartner extends Controller{
    private $lastmenu;
    function __Construct(){
        parent::__Construct();
        $this->load->model('model_s_bpartner');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation'); 
    }
    
    function index(){
        $this->output->cache(3);
        $view="info_s_bpartner";
        
        $data = array();
        $data['judul_header'] = "Business Partner TBS";
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
        
        echo json_encode($this->model_s_bpartner->LoadData($periode,$company));   
    }
    
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_m_storage->search_data($kode_storage, $company));
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_bpartner->data_search($data['rules'], $company));
    }
    
    function LoadData_BPAdem(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_bpartner->get_adem_bptbs($company));   
    }
    
    function get_supplier(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_supplier = $this->model_s_bpartner->get_supplier($company,$q);
         
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
				//var_dump($data_id);  
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
               
        /*}elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "PRINT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $print_type = $this->uri->segment('3');
                $this->print_po($data_id,$print_type);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
         */      
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "ADDBPADEM"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"ADD",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->add_bpadem($data_id);    
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
    
    function add_bpadem($data_id){
        $return['status']='';
        $return['error']=FALSE;
        //$company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        try{
            $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
            $data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8')));
            $data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))); 
            $data_post['SUPPLIERNAME'] = strtoupper(trim(htmlentities($data_id['SUPPLIERNAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['CONTACTNAME'] = strtoupper(trim(htmlentities($data_id['CONTACTNAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['ADDRESS'] = strtoupper(trim(htmlentities($data_id['ADDRESS'],ENT_QUOTES,'UTF-8')));
			$data_post['SUPPLIERTYPE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERTYPE'],ENT_QUOTES,'UTF-8')));
			$data_post['NPWP'] = strtoupper(trim(htmlentities($data_id['NPWP'],ENT_QUOTES,'UTF-8')));
            $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
			$data_post['INPUT_DATE'] =  $this->global_func->gen_datetime(); 
            $data_post['COMPANY_CODE'] = $company;
             
            if (empty($data_post['C_BPARTNER_ID']) || trim($data_post['C_BPARTNER_ID'])==''){
                throw new Exception("C_BPARTNER_ID kosong");
            }
            
			if (empty($data_post['SUPPLIERCODE']) || trim($data_post['SUPPLIERCODE'])==''){
                throw new Exception("SUPPLIERCODE kosong");
            }
           	
			if (empty($data_post['SUPPLIERNAME']) || trim($data_post['SUPPLIERNAME'])==''){
                throw new Exception("SUPPLIERNAME kosong");
            }
			
			if (empty($data_post['SUPPLIERTYPE']) || trim($data_post['SUPPLIERTYPE'])==''){
                throw new Exception("SUPPLIERTYPE kosong");
            }
			if (empty($data_post['NPWP']) || trim($data_post['NPWP'])==''){
                throw new Exception("NPWP kosong");
            }
			
            if(empty($return['status']) && $return['error']===false){     
                $insert_id = $this->model_s_bpartner->add_new_bpadem($data_post['SUPPLIERCODE'],$data_post);
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
			$company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
			$kode=strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8')));
            $data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))); 
            $data_post['SUPPLIERNAME'] = strtoupper(trim(htmlentities($data_id['SUPPLIERNAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['CONTACTNAME'] = strtoupper(trim(htmlentities($data_id['CONTACTNAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['ADDRESS'] = strtoupper(trim(htmlentities($data_id['ADDRESS'],ENT_QUOTES,'UTF-8')));
			$data_post['SUPPLIERTYPE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERTYPE'],ENT_QUOTES,'UTF-8')));
			$data_post['NPWP'] = strtoupper(trim(htmlentities($data_id['NPWP'],ENT_QUOTES,'UTF-8')));
			$data_post['KODE_PENGIRIM'] = strtoupper(trim(htmlentities($data_id['KODE_PENGIRIM'],ENT_QUOTES,'UTF-8')));
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_DATE'] =  $this->global_func->gen_datetime(); 
            $data_post['COMPANY_CODE'] = $company;
                        
            if (empty($data_post['C_BPARTNER_ID']) || trim($data_post['C_BPARTNER_ID'])==''){
                throw new Exception("C_BPARTNER_ID kosong");
            }
            
			if (empty($data_post['SUPPLIERCODE']) || trim($data_post['SUPPLIERCODE'])==''){
                throw new Exception("SUPPLIERCODE kosong");
            }
           	
			if (empty($data_post['SUPPLIERNAME']) || trim($data_post['SUPPLIERNAME'])==''){
                throw new Exception("SUPPLIERNAME kosong");
            }
			
			if (empty($data_post['SUPPLIERTYPE']) || trim($data_post['SUPPLIERTYPE'])==''){
                throw new Exception("SUPPLIERTYPE kosong");
            }
			
            if (empty($data_post['NPWP']) || trim($data_post['NPWP'])==''){
                throw new Exception("NPWP kosong");
            }
			
			if (empty($data_post['KODE_PENGIRIM']) || trim($data_post['KODE_PENGIRIM'])==''){
                throw new Exception("KODE_PENGIRIM kosong");
            }
			
            if(empty($return['status']) && $return['error']===false){     
                $update_id = $this->model_s_bpartner->update_data($kode, $data_post);
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
	 	$company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
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
            $delete_id = $this->s_bpartner->delete_data($id_anon,$po_number, $company);
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
              
        $kode = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))) ; 
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));  
        $data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))); 
        $data_post['SUPPLIERNAME'] = strtoupper(trim(htmlentities($data_id['SUPPLIERNAME'],ENT_QUOTES,'UTF-8'))); 
		$data_post['CONTACTNAME'] = strtoupper(trim(htmlentities($data_id['CONTACTNAME'],ENT_QUOTES,'UTF-8'))); 
		$data_post['ADDRESS'] = strtoupper(trim(htmlentities($data_id['ADDRESS'],ENT_QUOTES,'UTF-8')));
		$data_post['SUPPLIERTYPE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERTYPE'],ENT_QUOTES,'UTF-8')));
		$data_post['NPWP'] = strtoupper(trim(htmlentities($data_id['NPWP'],ENT_QUOTES,'UTF-8')));
		$data_post['INPUT_DATE'] =  $this->global_func->gen_datetime(); 
		$data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
		if ($company == 'TPAI') {
			$data_post['COMPANY_CODE'] = 'PAI';
		}else{
        	$data_post['COMPANY_CODE'] = $company;
		}
                     
		if ($company == 'GKM') {
			$data_post['KODE_PENGIRIM'] = strtoupper(trim(htmlentities($data_id['KODE_PENGIRIM'],ENT_QUOTES,'UTF-8')));
		}
       
		if (empty($data_post['SUPPLIERCODE']) || trim($data_post['SUPPLIERCODE'])==''){
			$return['status']="Gagal: SUPPLIERCODE kosong";
            $return['error']=true;
       	}
           	
		if (empty($data_post['SUPPLIERNAME']) || trim($data_post['SUPPLIERNAME'])==''){
			$return['status']="Gagal: SUPPLIERNAME kosong";
            $return['error']=true;
       	}
			
		if (empty($data_post['SUPPLIERTYPE']) || trim($data_post['SUPPLIERTYPE'])==''){
			$return['status']="Gagal: BP Group kosong";
            $return['error']=true;
     	}
			
    	if (empty($data_post['NPWP']) || trim($data_post['NPWP'])==''){
			$return['status']="Gagal: NPWP kosong";
            $return['error']=true;
		}
		if ($company == 'GKM') {	
			if (empty($data_post['KODE_PENGIRIM']) || trim($data_post['KODE_PENGIRIM'])==''){
				$return['status']="Gagal: BP Type kosong";
				$return['error']=true;
			}
		}
        if(empty($return['status']) && $return['error']===false){     
            $sync_id = $this->model_s_bpartner->sync_data($kode,$company,$data_post);
            $return['status']=  $sync_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }      
    }
}
?>