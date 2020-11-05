<?php
class s_do_dispatch extends Controller{
    private $lastmenu;
    function __Construct(){
        parent::__Construct();
        $this->load->model('model_s_do_dispatch');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
    }
    
    function index(){
        $this->output->cache(3);
        $view="info_s_do_dispatch";
        
        $data = array();
        $data['judul_header'] = "Delivery Order";
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
        
        echo json_encode($this->model_s_do_dispatch->LoadData($periode,$company));   
    }
    
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_do_dispatch->data_search($data['rules'], $company));
    }
    
	function search_so(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_do_dispatch->search_so($data['rules'], $company));
    }
	
    function LoadData_DOAdem(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_do_dispatch->get_adem_dotbs($company));   
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
               
        }else if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "EDIT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"EDIT",$loginid);
            if($is_auth_user_command['0']['ROLE_EDIT']=='1'){
                $this->update_data($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
                    
        }else if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_data($data_id);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }
               
        }else if(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "CREATE"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_ADD']=='1'){
                $this->create($data_id);   
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
	
	function create($data_id){
        $return['status']='';
        $return['error']=false;
         try{
            $so_number=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8')));
			$do_number=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['ID_DO'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))); 
			$data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8')));
			$data_post['CUSTOMER_NAME'] = strtoupper(trim(htmlentities($data_id['CUSTOMER_NAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['CUSTOMER_ADDRESS'] = strtoupper(trim(htmlentities($data_id['CUSTOMER_ADDRESS'],ENT_QUOTES,'UTF-8')));
			$data_post['QTY_CONTRACT'] = strtoupper(trim(htmlentities($data_id['QTY_CONTRACT'],ENT_QUOTES,'UTF-8')));
			//$data_post['ID_JENIS'] = strtoupper(trim(htmlentities($data_id['ID_JENIS'],ENT_QUOTES,'UTF-8')));
			$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8')));
			$data_post['SO_NUMBER'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8')));
            $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
			$data_post['COMPANY_CODE'] = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
            /*
			if ($data_post['ID_JENIS']=="1001461"){
                $data_post['JENIS']="KERNEL";
            }else if($data_post['ID_JENIS']=="1001460"){
                $data_post['JENIS']="CPO";
            }else{
                throw new Exception("Produk ". $data_post['ID_JENIS'] ." tidak diketahui"); 
            } 
			
			if ($data_post['COMPANY_CODE']=='1000028'){
                $data_post['COMPANY_CODE']="GKM";
            }else if($data_post['COMPANY_CODE']=='1000018'){
                $data_post['COMPANY_CODE']="SSS";
            }else if($data_post['COMPANY_CODE']=='1000031'){
                $data_post['COMPANY_CODE']="SML";
			}else if($data_post['COMPANY_CODE']=='1000024'){
                $data_post['COMPANY_CODE']="MAG";
            }else if($data_post['COMPANY_CODE']=='1000001'){
                $data_post['COMPANY_CODE']="LIH";
            }else if($data_post['COMPANY_CODE']=='1000070'){
                $data_post['COMPANY_CODE']="SMI";
            }else if($data_post['COMPANY_CODE']=='1000060'){
                $data_post['COMPANY_CODE']="NRP";
            }else{
                //Throw Error
                throw new Exception("company code ". $data_post['COMPANY_CODE'] ." tidak diketahui"); 
            } 
			*/
			
            if (empty($do_number) || trim($do_number)==''){
                throw new Exception("Nomor DO tidak boleh null!");              
            }else if(strlen($do_number) > 50){
                throw new Exception("panjang karakter Nomor DO melebihi batas!!");;
            }
			
			if (empty($so_number) || trim($so_number)==''){
                throw new Exception("Nomor SO tidak boleh null!");              
            }else if(strlen($so_number) > 50){
                throw new Exception("panjang karakter Nomor SO melebihi batas!!");;
            }
            
            if(empty($return['status']) && $return['error']==false){     
                $update_id = $this->model_s_do_dispatch->create($data_post['COMPANY_CODE'], $so_number, $do_number, $data_post);
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
    	
	function add_new($data_id){
        $return['status']='';
        $return['error']=false;
         try{
            $so_number=strtoupper(trim(htmlentities($data_id['SO_NUMBER'],ENT_QUOTES,'UTF-8')));
			$do_number=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['ID_DO'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))); 
			$data_post['C_BPARTNER_ID'] = strtoupper(trim(htmlentities($data_id['C_BPARTNER_ID'],ENT_QUOTES,'UTF-8')));
			$data_post['CUSTOMER_NAME'] = strtoupper(trim(htmlentities($data_id['CUSTOMER_NAME'],ENT_QUOTES,'UTF-8'))); 
			$data_post['CUSTOMER_ADDRESS'] = strtoupper(trim(htmlentities($data_id['CUSTOMER_ADDRESS'],ENT_QUOTES,'UTF-8')));
			$data_post['QTY_CONTRACT'] = strtoupper(trim(htmlentities($data_id['QTY_CONTRACT'],ENT_QUOTES,'UTF-8')));
			$data_post['ID_JENIS'] = strtoupper(trim(htmlentities($data_id['ID_JENIS'],ENT_QUOTES,'UTF-8')));
			$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8')));
			$data_post['SO_NUMBER'] = strtoupper(trim(htmlentities($data_id['SO_NUMBER'],ENT_QUOTES,'UTF-8')));
            $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
			$data_post['COMPANY_CODE'] = strtoupper(trim(htmlentities($data_id['COMPANY_CODE'],ENT_QUOTES,'UTF-8'))); 		
            
			if ($data_post['ID_JENIS']=="1001461"){
                //KERNEL
                $data_post['JENIS']="KERNEL";
            }else if($data_post['ID_JENIS']=="1001460"){
                $data_post['JENIS']="CPO";
            }else{
                //Throw Error
                throw new Exception("Produk ". $data_post['ID_JENIS'] ." tidak diketahui"); 
            } 
			
			if ($data_post['COMPANY_CODE']=='1000028'){
                $data_post['COMPANY_CODE']="GKM";
            }else if($data_post['COMPANY_CODE']=='1000018'){
                $data_post['COMPANY_CODE']="SSS";
            }else if($data_post['COMPANY_CODE']=='1000031'){
                $data_post['COMPANY_CODE']="SML";
			}else if($data_post['COMPANY_CODE']=='1000024'){
                $data_post['COMPANY_CODE']="MAG";
            }else if($data_post['COMPANY_CODE']=='1000001'){
                $data_post['COMPANY_CODE']="LIH";
            }else if($data_post['COMPANY_CODE']=='1000070'){
                $data_post['COMPANY_CODE']="SMI";
            }else if($data_post['COMPANY_CODE']=='1000060'){
                $data_post['COMPANY_CODE']="NRP";
            }else{
                //Throw Error
                throw new Exception("company code ". $data_post['COMPANY_CODE'] ." tidak diketahui"); 
            } 
			
			
            if (empty($do_number) || trim($do_number)==''){
                throw new Exception("Nomor DO tidak boleh null!");              
            }else if(strlen($do_number) > 50){
                throw new Exception("panjang karakter Nomor DO melebihi batas!!");;
            }
			
			if (empty($so_number) || trim($so_number)==''){
                throw new Exception("Nomor SO tidak boleh null!");              
            }else if(strlen($so_number) > 50){
                throw new Exception("panjang karakter Nomor SO melebihi batas!!");;
            }
            
            if(empty($return['status']) && $return['error']==false){     
                $update_id = $this->model_s_do_dispatch->add_new($data_post['COMPANY_CODE'], $so_number, $do_number, $data_post);
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
	
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;

         try{
            $so_number=strtoupper(trim(htmlentities($data_id['SO_NUMBER'],ENT_QUOTES,'UTF-8')));
			$do_number=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;
            $data_post['ID_DO'] = strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))); 
            $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
            $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
			$company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
             
            if (empty($do_number) || trim($do_number)==''){
                throw new Exception("Nomor DO tidak boleh null!!");              
            }else if(strlen($do_number) > 50){
                throw new Exception("panjang karakter Nomor DO melebihi batas!!");;
            }
            
            if(empty($return['status']) && $return['error']==false){     
                $update_id = $this->model_s_do_dispatch->update_data($so_number, $data_post, $company);
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
		$id_do = strtoupper((htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;  
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if (empty($id_do) || trim($id_do)=='' || $id_do==false){
            $return['status']="Nomor ID !!";
            $return['error']=true;   
        }

        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_do_dispatch->delete_data($id_do, $company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);                      
        }else{
            echo json_encode($return);
        }      
    }
	function get_cbpartner(){
		try{
			$q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); 
			$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
						
			if ($company=="GKM"){
				$company = '1000028';
            }else if ($company=="SSS"){
                $company = '1000018';
            }else if ($company=="SML"){
                $company = '1000031';
			}else if ($company=="MAG"){
                $company = '1000024';
            }else if($company=="LIH"){
                $company = '1000001';
            }else if($company=="SMI"){
                $company = '1000070';
            }else if($company=="NRP"){
                $company = '1000060';
            }else{
                //Throw Error
                throw new Exception("company code ". $company." tidak diketahui"); 
            }

			$data = $this->model_s_do_dispatch->get_cbpartner($q,$company);
			
			//var_dump($data);			
			
			$cbpartner = array();
			foreach($data as $row){
				$cbpartner[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['name'],ENT_QUOTES,'UTF-8')).
					'",res_name:"'.str_replace('"','\\"',htmlentities($row['c_bpartner_id'],ENT_QUOTES,'UTF-8')).
					'",res_dName:"'.str_replace('"','\\"',htmlentities($row['address1'],ENT_QUOTES,'UTF-8')).
					'",res_dNetto:"'.str_replace('"','\\"',htmlentities($row['value'],ENT_QUOTES,'UTF-8')).
					'",res_dFlag:"'.str_replace('"','\\"',htmlentities($row['address2'],ENT_QUOTES,'UTF-8')).
					'",res_dl:"'.str_replace('"','\\"',htmlentities($row['c_bpartner_id'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
					.htmlentities($row['name'],ENT_QUOTES,'UTF-8')).'"}';
			}
			echo '['.implode(',',$cbpartner).']'; exit;    
			
		}catch(Exception $e){
            $return['status'] = $e->getMessage();
            $return['error']=true;
            echo json_encode($return);   
        } 
    }
}
?>
