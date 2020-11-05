<?php
class s_production extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_production');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_production";
        $this->data = array();
    }
    
    function index(){
        $view="info_s_production";
        
        $this->data['judul_header'] = "Pencatatan hasil produksi";
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
        
        echo json_encode($this->model_s_production->LoadData($company));   
    }
    
    function search_data(){
        //$kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        //$tanggal = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        //$jenis = trim(htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8'));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_production->data_search($data['rules'], $company));
        
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
        
        $data_post['ID_PRODUCTION'] = $this->global_func->createMy_ID('s_production','ID_PRODUCTION',$company."PROD","PRODUCTION_DATE",$company);
        $data_post['PRODUCTION_DATE'] = strtoupper(trim(htmlentities($data_id['PRODUCTION_DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['ID_COMMODITY'] = strtoupper(trim(htmlentities($data_id['ID_COMMODITY'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['WEIGHT']=strtoupper(trim(htmlentities($data_id['WEIGHT'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
                
        $validate_numeric=$this->validate_numeric($data_post['WEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Berat harus angka";
            $return['error']=true;       
        }
        
        if (empty($data_post['ID_PRODUCTION']) || trim($data_post['ID_PRODUCTION'])==''){
            $return['status']="Harap isi ID ID_PRODUCTION";
            $return['error']=true;  
        }elseif(strlen($data_post['ID_PRODUCTION']) > 50){
            $return['status']="Panjang karakter ID_DISPATCH melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['ID_PRODUCTION']) || trim($data_post['ID_PRODUCTION'])==''){
            $return['status']="Harap isi ID PRODUCTION";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_PRODUCTION']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['ID_COMMODITY']) || trim($data_post['ID_COMMODITY'])==''){
            $return['status']="Harap isi JENIS BARANG";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_COMMODITY']) > 20){
            $return['status']="Panjang karakter JENIS BARANG melebihi batas maksimal";
            $return['error']=true;
        }        
        
        if(empty($return['status']) && $return['error']===false){                
            $insert_id = $this->model_s_production->add_new($company,$data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);  
        }
    }
    
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_PRODUCTION'] = strtoupper(trim(htmlentities($data_id['ID_PRODUCTION'],ENT_QUOTES,'UTF-8')));        
        $data_post['PRODUCTION_DATE'] = strtoupper(trim(htmlentities($data_id['PRODUCTION_DATE'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['ID_COMMODITY'] = strtoupper(trim(htmlentities($data_id['ID_COMMODITY'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['WEIGHT']=strtoupper(trim(htmlentities($data_id['WEIGHT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
		
		if(intval($data_post['PRODUCTION_DATE'])==0){					
			$return['status']="Tanggal produksi tidak boleh null \r\n";
			$return['error']=true;   
        }
        
        $validate_numeric=$this->validate_numeric($data_post['WEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Berat harus angka";
            $return['error']=true;       
        }
        
        if (empty($data_post['ID_PRODUCTION']) || trim($data_post['ID_PRODUCTION'])==''){
            $return['status']="Harap isi ID_PRODUCTION";
            $return['error']=true;  
        }elseif(strlen($data_post['ID_PRODUCTION']) > 50){
            $return['status']="Panjang karakter ID_PRODUCTION melebihi batas maksimal";
            $return['error']=true;
        }        
        
        if (empty($data_post['ID_COMMODITY']) || trim($data_post['ID_COMMODITY'])==''){
            $return['status']="Harap isi JENIS BARANG";
            $return['error']=true;  
        }elseif(strlen($data_id['ID_COMMODITY']) > 20){
            $return['status']="Panjang karakter JENIS BARANG melebihi batas maksimal";
            $return['error']=true;
        }
		
        if(empty($return['status']) && $return['error']==false){               
            $insert_id = $this->model_s_production->update_production($data_post['ID_PRODUCTION'],$data_post,$company);
            $return['status']=  $insert_id;
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
        $id_production = strtoupper(trim(htmlentities($data_id['ID_PRODUCTION'],ENT_QUOTES,'UTF-8'))) ;
        
        if (empty($id_production)){
            $status = "ID_PRODUCTION KOSONG !!"; 
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;  
        }elseif(strlen($id_production) > 50){
            $status  ="Panjang karakter ID melebihi batas maksimal";
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_production->delete_production($id_production,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function get_commodity(){
        $q = trim(htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8')); //no kendaraan
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        $data_storage = $this->model_s_production->get_commodity($q,$company);
  
        $storage = array();
        foreach($data_storage as $row)
        {			
			$komoditas[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8')).
         	'",res_name:"'.str_replace('"','\\"',htmlentities($row['DESKRIPSI'],ENT_QUOTES,'UTF-8')).
          	'",res_dl:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8').
			"&nbsp;&nbsp; - &nbsp;&nbsp;".htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8').
          	"&nbsp;&nbsp; - &nbsp;&nbsp;".htmlentities($row['DESKRIPSI'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$komoditas).']'; exit;         
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
