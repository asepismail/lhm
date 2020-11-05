<?php
class s_catat_sounding extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        
        $param=1; 
        $this->load->model('model_s_catat_sounding', '', FALSE, $param);        
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_catat_sounding";
        $this->data = array(); 
    }
    
    function index(){
        $view="info_s_catat_sounding";
        
        //$data = array();
        $this->data['judul_header'] = "Product Storage Reading (Sounding) - CPO";
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
        
        echo json_encode($this->model_s_catat_sounding->LoadData($company));   
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
        
        $data_post['ID_SOUNDING'] = $this->global_func->createMy_ID('s_sounding','ID_SOUNDING',$company."SND","DATE",$company);
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = strtoupper(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
        //if($company=='LIH' || $company=='GKM'){
            $data_post['HEIGHT']=trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8')) ;     
        //}elseif($company=='MAG'){
            //$data_post['HEIGHT']=trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))/1000 ; 
            //$data_post['HEIGHT']=trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'));    
       //}
        
        $data_post['TEMPERATURE'] =trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_S'] =1;
        //$data_post['VOLUME'] =strtoupper(trim(htmlentities($data_id['VOLUME'],ENT_QUOTES,'UTF-8')));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        
        $validate_numeric=$this->validate_numeric($data_post['HEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai HEIGHT/Tinggi harus angka";
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
        /*
        if (empty($data_post['HEIGHT']) || trim($data_post['HEIGHT'])==''){
            $return['status']="Harap isi HEIGHT";
            $return['error']=true;  
        }elseif(strlen($data_id['HEIGHT']) > 50){
            $return['status']="Panjang karakter HEIGHT melebihi batas maksimal";
            $return['error']=true;
        }
	 */
        
        $validate_date=$this->validate_date($data_post['DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_s_catat_sounding->add_new($company,$data_post);
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
        
        //$data_post['ID_FFA'] = $this->global_func->createMy_ID('s_ffa','ID_FFA',$company."FFA");
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = date('Y-m-d',strtotime(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['HEIGHT']=strtoupper(trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TEMPERATURE'] =strtoupper(trim(htmlentities($data_id['TEMPERATURE'],ENT_QUOTES,'UTF-8')));
        $data_post['TYPE_S'] =1;
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
        
        if(empty($return['status']) && $return['error']===false){     
            $update_id = $this->model_s_catat_sounding->update_sounding($id_sounding,$data_post,$company);
            $return['status']=  $update_id;
            $return['error']=false;
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
            $delete_id = $this->model_s_catat_sounding->delete_sounding($id_sounding,$company);
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
        $data_storage = $this->model_s_catat_sounding->get_storage($q,$company);
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
    
    function search_data(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8') ;
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        $ar=(empty($ar) || $ar===false)?'-':$ar;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        //echo json_encode($this->model_s_catat_sounding->search_data($kode_storage,$ar, $company));
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_catat_sounding->data_search($data['rules'], $company));
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

