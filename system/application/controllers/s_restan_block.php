<?php
class s_restan_block extends Controller{
    function __construct(){
        parent::__construct();
        
        $this->load->model('model_s_restan_block');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_restan_block";
        $this->load->plugin('to_excel');
        
    }
    
    function index()
    {
        $view="info_s_restan_block";
        
        $data = array();
        $data['judul_header'] = "Pencatatan Janjang Afkir";
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
        echo json_encode($this->model_s_restan_block->LoadData($periode,$company));   
    }
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');

        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_restan_block->data_search($data['rules'], $company));    
    } 
    
    function add_new($data_id){
        $return['status']='';
        $return['error']=FALSE;
        $company = trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
        
        $data_post['ID_AFKIR'] = trim($this->global_func->createMy_ID('s_jjg_afkir','ID_AFKIR',$company."RST","TANGGAL",$company));
        $data_post['TANGGAL'] = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BLOCK'] = strtoupper(trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['JANJANG']=strtoupper(trim(htmlentities($data_id['JANJANG'],ENT_QUOTES,'UTF-8'))) ;

        $data_post['INPUT_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['JANJANG']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai JANJANG harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_AFKIR']) || trim($data_post['ID_AFKIR'])==''){
            $return['status'] = "Harap isi ID_RESTAN";
            $return['error']=true; 

        }elseif(strlen($data_post['ID_AFKIR']) > 50){
            $return['status']  ="Panjang karakter ID_RESTAN melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['JANJANG']) || trim($data_post['JANJANG'])==''){
            $return['status'] = "Harap isi JANJANG";
            $return['error']=true;   
        }elseif(strlen($data_id['JANJANG']) > 50){
            $return['status']  ="Panjang karakter JANJANG melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $return['status'] = "Harap isi AFD";
            $return['error']=true;   
        }elseif(strlen($data_id['AFD']) > 5){
            $return['status']  ="Panjang karakter AFD melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['BLOCK']) || trim($data_post['BLOCK'])==''){
            $return['status'] = "Harap isi BLOCK";
            $return['error']=true;   
        }elseif(strlen($data_id['BLOCK']) > 10){
            $return['status']  ="Panjang karakter BLOCK melebihi batas maksimal";
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_s_restan_block->lokasi_validate($data_post['AFD'],$data_post['BLOCK'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){  
            $return['status']="Kode lokasi : ".$data_post['BLOCK']." - SALAH!! \r\n";
            $return['error']=true;
        }
                    
        $validate_date=$this->validate_date($data_post['TANGGAL']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_s_restan_block->add_new($company,$data_post);
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
        $data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BLOCK'] = strtoupper(trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['JANJANG']=strtoupper(trim(htmlentities($data_id['JANJANG'],ENT_QUOTES,'UTF-8'))) ;

        $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data_post['UPDATE_TIME'] = $this->global_func->gen_datetime(); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['JANJANG']);
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai JANJANG harus angka";
            $return['error']=true;        
        }
        
        $id_restan = strtoupper(trim(htmlentities($data_id['ID_AFKIR'],ENT_QUOTES,'UTF-8'))) ;
        if (empty($id_restan) || trim($id_restan)==''){
            $return['status'] = "Harap isi ID_AFKIR";
            $return['error']=true; 
              
        }elseif(strlen($id_restan) > 50){
            $return['status']  ="Panjang karakter ID_AFKIR melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['JANJANG']) || trim($data_post['JANJANG'])==''){
            $return['status'] = "Harap isi JANJANG";
            $return['error']=true;   
        }elseif(strlen($data_id['JANJANG']) > 50){
            $return['status']  ="Panjang karakter JANJANG melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $return['status'] = "Harap isi AFD";
            $return['error']=true;   
        }elseif(strlen($data_id['AFD']) > 5){
            $return['status']  ="Panjang karakter AFD melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['BLOCK']) || trim($data_post['BLOCK'])==''){
            $return['status'] = "Harap isi BLOCK";
            $return['error']=true;   
        }elseif(strlen($data_id['BLOCK']) > 10){
            $return['status']  ="Panjang karakter BLOCK melebihi batas maksimal";
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_s_restan_block->lokasi_validate($data_post['AFD'],$data_post['BLOCK'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){  
            $return['status']="Kode lokasi : ".$data_post['BLOCK']." - SALAH!! \r\n";
            $return['error']=true;
        }
        
        $validate_date=$this->validate_date($data_post['TANGGAL']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $update_id = $this->model_s_restan_block->update_restan($id_restan,$data_post,$company);
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
        $id_restan = strtoupper(trim(htmlentities($data_id['ID_AFKIR'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_restan) || trim($id_restan)==='' || $id_restan===false){
            $return['status']="ID_AFKIR KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_restan) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_restan_block->delete_restan($id_restan,$company);
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
        $data_afd = $this->model_s_restan_block->get_afdeling($company,$q);
         
        //echo $q;
        $afdeling = array();
        foreach($data_afd as $row)
        {
            $afdeling[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_name:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).
            '",res_dl:"'.str_replace('"','\\"',htmlentities($row['AFD'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$afdeling).']'; exit;     
    }
    
    function get_block(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); 
        $location_left = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_s_restan_block->get_block($company,$location_left,$q);
         
        //echo $q;
        $block = array();
        foreach($data_afd as $row)
        {
            $block[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$block).']'; exit;     
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
