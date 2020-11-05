<?php
class s_dispatch_sounding extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        
        $param=1; 
        $this->load->model('model_s_dispatch_sounding', '', FALSE, $param);        
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_dispatch_sounding";
        $this->data = array(); 
    }
    
    function index(){
        $view="info_s_dispatch_sounding";
        
        //$data = array();
        $this->data['judul_header'] = "MOVEMENT STOCK CPO - SOUNDING";
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
        
        echo json_encode($this->model_s_dispatch_sounding->LoadData($company));   
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
				//var_dump($data_id);
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
		//var_dump($data_id);
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_SOUNDING'] = $this->global_func->createMy_ID('s_movement_sounding','ID_SOUNDING',$company."MOV","DATE",$company);
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = strtoupper(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
		$data_post['HEIGHT']=trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'));   
        $data_post['TEMPERATURE'] =trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8'));
		$data_post['WEIGHT'] =$this->model_s_dispatch_sounding->calc_weight($data_id['TEMPERATURE'],$data_id['HEIGHT'],$data_id['ID_STORAGE'],$company);
		
		
		$data_post['ID_STORAGE2'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE2'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE2'] = strtoupper(trim(htmlentities($data_id['DATE2'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME2'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME2'],ENT_QUOTES,'UTF-8')))) ;
		$data_post['HEIGHT2']=trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8'));   
        $data_post['TEMPERATURE2'] =trim(htmlentities($data_id['TEMPERATURE2'],ENT_QUOTES,'UTF-8'));
		$data_post['WEIGHT2'] =$this->model_s_dispatch_sounding->calc_weight($data_id['TEMPERATURE2'],$data_id['HEIGHT2'],$data_id['ID_STORAGE2'],$company);
		
		$data_post['DOC_NO'] =trim(htmlentities($data_id['DOC_NO'],ENT_QUOTES,'UTF-8'));
		$data_post['SUPPLIER'] =trim(htmlentities($data_id['SUPPLIER'],ENT_QUOTES,'UTF-8'));
		if ($data_id['MOV_TYPE']=='M'){
			$data_post['BERAT_BERSIH'] = round($data_post['WEIGHT']-$data_post['WEIGHT2'],0);
		}else if ($data_id['MOV_TYPE']=='D'){
			$data_post['BERAT_BERSIH'] = abs(round($data_post['WEIGHT2'] - $data_post['WEIGHT'],0));
		}
        $data_post['MOV_TYPE'] =trim(htmlentities($data_id['MOV_TYPE'],ENT_QUOTES,'UTF-8'));
        //$data_post['VOLUME'] =strtoupper(trim(htmlentities($data_id['VOLUME'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        
        $validate_numeric=$this->validate_numeric($data_post['HEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai HEIGHT harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_SOUNDING']) || trim($data_post['ID_SOUNDING'])==''){
            $return['status']="Harap isi ID_SOUNDING";
            $return['error']=true;   
        }elseif(strlen($data_post['ID_SOUNDING']) > 50){
            $return['status']="Panjang karakter ID_SOUNDING melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
		
		$validate_numeric=$this->validate_numeric($data_post['HEIGHT2']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai HEIGHT 2 harus angka";
            $return['error']=true;        
        }
                
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE2'])==''){
            $return['status']="Harap isi ID STORAGE 2";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['DATE2']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		if (empty($data_post['DOC_NO']) || trim($data_post['DOC_NO'])==''){
            $return['status']="Harap isi DOC NO: SO No. atau BA No";
            $return['error']=true;   
        }
		
		if (empty($data_post['MOV_TYPE']) || trim($data_post['MOV_TYPE'])==''){
            $return['status']="Harap Pilih tipe movement";
            $return['error']=true;   
        }
		
		if ($data_post['MOV_TYPE']=='M'){
            if (empty($data_post['SUPPLIER']) || trim($data_post['SUPPLIER'])==''){
				$return['status']="Harap isi SUPPLIER";
				$return['error']=true;   
        	}
        }
		/*
		begin create copy data_post
		*/
		if ($company == "SMI" && $data_id['MOV_TYPE']=='D'){
			$company2 = "NRP";
			if ($data_id['ID_STORAGE'] == "TCSMI-02") {
				$storage1 = "TCNRP-01";
			}else if ($data_id['ID_STORAGE'] == "TCSMI-03"){
				$storage1 = "TCNRP-02";
			}else if ($data_id['ID_STORAGE'] == "TCSMI-04"){
				$storage1 = "TCNRP-02";
			}
			
			if ($data_id['ID_STORAGE2'] == "TCSMI-02") {
				$storage2 = "TCNRP-01";
			}else if ($data_id['ID_STORAGE2'] == "TCSMI-03"){
				$storage2 = "TCNRP-02";
			}else if ($data_id['ID_STORAGE2'] == "TCSMI-04"){
				$storage2 = "TCNRP-02";
			}
			
			$data_post2['ID_SOUNDING'] = $this->global_func->createMy_ID('s_movement_sounding','ID_SOUNDING',$company2."MOV","DATE",$company2);
			$data_post2['ID_STORAGE'] = $storage1 ;
			$data_post2['DATE'] = strtoupper(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
			$data_post2['HEIGHT']=trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'));   
			$data_post2['TEMPERATURE'] =trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8'));
			$data_post2['WEIGHT'] =$data_post['WEIGHT'];
					
			$data_post2['ID_STORAGE2'] = $storage2 ;
			$data_post2['DATE2'] = strtoupper(trim(htmlentities($data_id['DATE2'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['TIME2'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME2'],ENT_QUOTES,'UTF-8')))) ;
			$data_post2['HEIGHT2']=trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8'));   
			$data_post2['TEMPERATURE2'] =trim(htmlentities($data_id['TEMPERATURE2'],ENT_QUOTES,'UTF-8'));
			$data_post2['WEIGHT2'] =$data_post['WEIGHT2'];
			
			$data_post2['DOC_NO'] =$data_post['ID_SOUNDING'];
			$data_post2['SUPPLIER'] =trim(htmlentities($data_id['SUPPLIER'],ENT_QUOTES,'UTF-8'));
			$data_post2['BERAT_BERSIH'] = abs(round($data_post['WEIGHT2'] - $data_post['WEIGHT'],0));
			
			$data_post2['MOV_TYPE'] = "P";
			$data_post2['COMPANY_CODE'] = $company2;
			$data_post2['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
		}
		// end
        if(empty($return['status']) && $return['error']==false){    
            $insert_id = $this->model_s_dispatch_sounding->add_new($company,$data_post);
			if ($insert_id['error'] == false && $company == "SMI"){
				$insert_id = $this->model_s_dispatch_sounding->add_new($company2,$data_post2);
			}
            $return =  $insert_id;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = date('Y-m-d',strtotime(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['HEIGHT']=strtoupper(trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TEMPERATURE'] =strtoupper(trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8')));
        $data_post['WEIGHT'] =$this->model_s_dispatch_sounding->calc_weight($data_id['TEMPERATURE'],$data_id['HEIGHT'],$data_id['ID_STORAGE'],$company);
		
		$data_post['ID_STORAGE2'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE2'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE2'] = strtoupper(trim(htmlentities($data_id['DATE2'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME2'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME2'],ENT_QUOTES,'UTF-8')))) ;
		$data_post['HEIGHT2']=trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8'));   
        $data_post['TEMPERATURE2'] =trim(htmlentities($data_id['TEMPERATURE2'],ENT_QUOTES,'UTF-8'));
		$data_post['WEIGHT2'] =$this->model_s_dispatch_sounding->calc_weight($data_id['TEMPERATURE2'],$data_id['HEIGHT2'],$data_id['ID_STORAGE2'],$company);
		
		$data_post['DOC_NO'] =trim(htmlentities($data_id['DOC_NO'],ENT_QUOTES,'UTF-8'));
		$data_post['SUPPLIER'] =trim(htmlentities($data_id['SUPPLIER'],ENT_QUOTES,'UTF-8'));
		if ($data_id['MOV_TYPE']=='M'){
			$data_post['BERAT_BERSIH'] = round($data_post['WEIGHT']-$data_post['WEIGHT2'],0);
		}else if ($data_id['MOV_TYPE']=='D'){
			$data_post['BERAT_BERSIH'] = abs(round($data_post['WEIGHT2'] - $data_post['WEIGHT'],0));
		}
        $data_post['MOV_TYPE'] =trim(htmlentities($data_id['MOV_TYPE'],ENT_QUOTES,'UTF-8'));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
		        
        $validate_numeric=$this->validate_numeric($data_post['HEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai HEIGHT harus angka";
            $return['error']=true;       
        }
        
        $id_sounding=strtoupper(trim(htmlentities($data_id['ID_SOUNDING'],ENT_QUOTES,'UTF-8'))) ;
        if (empty($id_sounding) || trim($id_sounding)==''){
            $return['status']="Harap isi ID_SOUNDING";
            $return['error']=true;
            unset ($id_sounding);   
        }elseif(strlen($id_sounding) > 50){
            $return['status']="Panjang karakter ID_SOUNDING melebihi batas maksimal";
            $return['error']=true;
            unset ($id_sounding);
        }
        
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['HEIGHT']) || trim($data_post['HEIGHT'])==''){
            $return['status']="Harap isi HEIGHT";
            $return['error']=true;   
        }elseif(strlen($data_id['HEIGHT']) > 50){
            $return['status']="Panjang karakter HEIGHT melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
		$validate_numeric=$this->validate_numeric($data_post['HEIGHT2']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai HEIGHT 2 harus angka";
            $return['error']=true;        
        }
                
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE2'])==''){
            $return['status']="Harap isi ID STORAGE 2";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['DATE2']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		if (empty($data_post['DOC_NO']) || trim($data_post['DOC_NO'])==''){
            $return['status']="Harap isi DOC NO: SO No. atau BA No";
            $return['error']=true;   
        }
		
		if (empty($data_post['MOV_TYPE']) || trim($data_post['MOV_TYPE'])==''){
            $return['status']="Harap Pilih tipe movement";
            $return['error']=true;   
        }
		
		if ($data_post['MOV_TYPE']=='M'){
            if (empty($data_post['SUPPLIER']) || trim($data_post['SUPPLIER'])==''){
				$return['status']="Harap isi SUPPLIER";
				$return['error']=true;   
        	}
        }
		
		if ($company == "SMI" && $data_id['MOV_TYPE']=='D'){
			$company2 = "NRP";
			if ($data_id['ID_STORAGE'] == "TCSMI-02") {
				$storage1 = "TCNRP-01";
			}else if ($data_id['ID_STORAGE'] == "TCSMI-03"){
				$storage1 = "TCNRP-02";
			}else if ($data_id['ID_STORAGE'] == "TCSMI-04"){
				$storage1 = "TCNRP-02";
			}
			
			if ($data_id['ID_STORAGE2'] == "TCSMI-02") {
				$storage2 = "TCNRP-01";
			}else if ($data_id['ID_STORAGE2'] == "TCSMI-03"){
				$storage2 = "TCNRP-02";
			}else if ($data_id['ID_STORAGE2'] == "TCSMI-04"){
				$storage2 = "TCNRP-02";
			}
			
			$data_post2['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['DATE'] = date('Y-m-d',strtotime(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8')))) ;
			$data_post2['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
			$data_post2['HEIGHT']=strtoupper(trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['TEMPERATURE'] =strtoupper(trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8')));
			$data_post2['WEIGHT'] =$data_post['WEIGHT'];
			
			$data_post2['ID_STORAGE2'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE2'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['DATE2'] = strtoupper(trim(htmlentities($data_id['DATE2'],ENT_QUOTES,'UTF-8'))) ;
			$data_post2['TIME2'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME2'],ENT_QUOTES,'UTF-8')))) ;
			$data_post2['HEIGHT2']=trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8'));   
			$data_post2['TEMPERATURE2'] =trim(htmlentities($data_id['TEMPERATURE2'],ENT_QUOTES,'UTF-8'));
			$data_post2['WEIGHT2'] =$data_post['WEIGHT2'];
			
			$data_post2['DOC_NO'] =$id_sounding;
			$data_post2['SUPPLIER'] =trim(htmlentities($data_id['SUPPLIER'],ENT_QUOTES,'UTF-8'));
			$data_post2['BERAT_BERSIH'] = abs(round($data_post['WEIGHT2'] - $data_post['WEIGHT'],0));

			$data_post2['MOV_TYPE'] ="P";
			$data_post2['COMPANY_CODE'] = $company;
			$data_post2['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
			$data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
		}
		
        if(empty($return['status']) && $return['error']==false){     
            $update_id = $this->model_s_dispatch_sounding->update_sounding($id_sounding,$data_post,$company);
			if ($update_id['error'] == false && $company == "SMI"){
				$update_id = $this->model_s_dispatch_sounding->update_sounding2($id_sounding,$data_post2,$company);
			}
            $return =  $update_id;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function delete_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_sounding = strtoupper(trim(htmlentities($data_id['ID_SOUNDING'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_sounding) || trim($id_sounding)==='' || $id_sounding===false){
            $return['status']="ID_SOUNDING KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_sounding) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_dispatch_sounding->delete_sounding($id_sounding,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function get_storage(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //id_storage
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_storage = $this->model_s_dispatch_sounding->get_storage($q,$company);
        //echo $q;
        $storage = array();
        foreach($data_storage as $row){
            $storage[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['PRODUCT_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_STORAGE'],ENT_QUOTES,'UTF-8'). 
                "&nbsp;&nbsp; - &nbsp;&nbsp;".htmlentities($row['PRODUCT_CODE'],ENT_QUOTES,'UTF-8').
                "&nbsp;&nbsp; - &nbsp;&nbsp;".str_replace(chr(10),'',htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8'))).'"}';
        }
        echo '['.implode(',',$storage).']'; exit;         
    }
    
	function get_doc(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //id_storage
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_storage = $this->model_s_dispatch_sounding->get_doc($q,$company);
        //echo $q;
        $storage = array();
        foreach($data_storage as $row){
            $storage[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_DO'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['CUSTOMER_NAME'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_DO'],ENT_QUOTES,'UTF-8'). 
                "&nbsp;&nbsp; - &nbsp;&nbsp;".str_replace(chr(10),'',htmlentities($row['CUSTOMER_NAME'],ENT_QUOTES,'UTF-8'))).'"}';
        }
        echo '['.implode(',',$storage).']'; exit;         
    }
	function get_supplier(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //id_storage
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_storage = $this->model_s_dispatch_sounding->get_supplier($q,$company);
        //echo $q;
        $storage = array();
        foreach($data_storage as $row){
            $storage[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['company_code'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['company_name'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['company_code'],ENT_QUOTES,'UTF-8'). 
                "&nbsp;&nbsp; - &nbsp;&nbsp;".str_replace(chr(10),'',htmlentities($row['company_name'],ENT_QUOTES,'UTF-8'))).'"}';
        }
        echo '['.implode(',',$storage).']'; exit;         
    }
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8') ;
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        $ar=(empty($ar) || $ar===false)?'-':$ar;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_s_catat_sounding->search_data($kode_storage,$ar, $company));
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_dispatch_sounding->data_search($data['rules'], $company));
    }
    
    
    function validate_date($date_input){
        $TGL_AKTIVITAS=trim(strval($date_input));
        $status=FALSE;
        if(empty($TGL_AKTIVITAS)){
            $status="Tanggal Aktifitas tidak boleh kosong";
        } 
        
        if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
            $status= "format datetime tidak benar";
        }
      
        return $status;
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
}
?>

