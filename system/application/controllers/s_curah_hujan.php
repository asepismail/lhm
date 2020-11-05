<?php
class s_curah_hujan extends Controller{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_curah_hujan');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_curah_hujan";
        $this->data = array();    
    }
    
    function index(){
        $view="info_s_curah_hujan";
        
        //$data = array();
        $this->data['judul_header'] = "Data Curah Hujan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        //$this->data['bjr_periode'] = $this->get_bjr_periode();
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
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

    function LoadData(){
        //$periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'); 
        $periode=FALSE; 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_s_curah_hujan->LoadData($periode,$company));   
    }
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_curah_hujan->data_search($data['rules'], $company));    
    } 
    
    function add_new($data_id){
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        
        $data_post['ID_CH'] = trim($this->global_func->createMy_ID('s_curah_hujan','ID_CH',$company."CRH","TANGGAL",$company));
        $data_post['TANGGAL'] = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['CURAH_HUJAN']=strtoupper(trim(htmlentities($data_id['CURAH_HUJAN'],ENT_QUOTES,'UTF-8'))) ;

        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['CURAH_HUJAN']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CURAH_HUJAN harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_CH']) || trim($data_post['ID_CH'])==''){
            $return['status'] = "Harap isi ID_CH";
            $return['error']=true; 
              
        }elseif(strlen($data_post['ID_CH']) > 50){
            $return['status']  ="Panjang karakter ID_CH melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['CURAH_HUJAN']) || trim($data_post['CURAH_HUJAN'])==''){
            $return['status'] = "Harap isi CURAH_HUJAN";
            $return['error']=true;   
        }elseif(strlen($data_id['CURAH_HUJAN']) > 50){
            $return['status']  ="Panjang karakter CURAH_HUJAN melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['TANGGAL']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_s_curah_hujan->add_new($company,$data_post);
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

        $data_post['TANGGAL'] = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['CURAH_HUJAN']=strtoupper(trim(htmlentities($data_id['CURAH_HUJAN'],ENT_QUOTES,'UTF-8'))) ;

        $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data_post['UPDATE_TIME'] = $this->global_func->gen_datetime(); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['CURAH_HUJAN']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai CURAH_HUJAN harus angka";
            $return['error']=true;        
        }
        
        $id_restan = strtoupper(trim(htmlentities($data_id['ID_CH'],ENT_QUOTES,'UTF-8'))) ;
        if (empty($id_restan) || trim($id_restan)==''){
            $return['status'] = "Harap isi ID_CH";
            $return['error']=true; 
              
        }elseif(strlen($id_restan) > 50){
            $return['status']  ="Panjang karakter ID_CH melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['CURAH_HUJAN']) || trim($data_post['CURAH_HUJAN'])==''){
            $return['status'] = "Harap isi CURAH_HUJAN";
            $return['error']=true;   
        }elseif(strlen($data_id['CURAH_HUJAN']) > 50){
            $return['status']  ="Panjang karakter CURAH_HUJAN melebihi batas maksimal";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['TANGGAL']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $update_id = $this->model_s_curah_hujan->update_restan($id_restan,$data_post,$company);
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
        $id_restan = strtoupper(trim(htmlentities($data_id['ID_CH'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_restan) || trim($id_restan)==='' || $id_restan===false){
            $return['status']="ID_CH KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_restan) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_curah_hujan->delete_restan($id_restan,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
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
        if(is_array($data)){
            while(list($key,$val)=each($data)){
                if(trim($val)=="" || $val==null){
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val))){
                    $result='false';
                    break;
                }else{
                    $result='true';   
                }
            }
        }else {
            if(trim($numeric)=="" || $numeric==null){
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric)){
                $result='false';   
            }else{
                $result='true';
            }    
        }
        return $result;   
    }   
}
?>
