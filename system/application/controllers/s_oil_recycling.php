<?php
class s_oil_recycling extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        
        $param=1; 
        $this->load->model('model_s_oil_recycling', '', FALSE, $param);        
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="s_oil_recycling";
        $this->data = array(); 
    }
    
    function index(){
        $view="info_s_oil_recycling";
		
        $this->data['judul_header'] = "Product Storage Reading (Sounding) - CPO";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_oil_recycling->LoadData($company));   
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
               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "APPROVE"){
					$this->approve_data($data_id);                       
		}else{
            $return['status'] ="Operation Unknown !!";
            $return['error']=true;
            echo json_encode($return);
        }         
    }
    
	function approve_data($data_id){
        $return['status']="";
        $return['error']=false;
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $id_adjust = strtoupper(trim(htmlentities($data_id['ID_ADJUST'],ENT_QUOTES,'UTF-8'))) ; 
		$id_ba = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ;
        if (empty($id_ba) || trim($id_ba)=='' || $id_ba==false){
            $return['status']="ID BA KOSONG";
            $return['error']=true;   
        }
        if (empty($id_adjust) || trim($id_adjust)=='' || $id_adjust==false){
            $return['status']="ID ADJUST KOSONG";
            $return['error']=true;   
        }
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_oil_recycling->approve_ba($id_ba,$company,$id_adjust);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
    }
	
    function add_new($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_BA'] = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['ADJUST_DATE'] = strtoupper(trim(htmlentities($data_id['ADJUST_DATE'],ENT_QUOTES,'UTF-8')));
		$data_post['ADJUST_DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['ADJUST_DESCRIPTION'],ENT_QUOTES,'UTF-8')));
        $data_post['FROM_DATE']=trim(htmlentities($data_id['FROM_DATE'],ENT_QUOTES,'UTF-8')); 
		$data_post['TO_DATE']=trim(htmlentities($data_id['TO_DATE'],ENT_QUOTES,'UTF-8')); 
        $data_post['ID_STORAGE'] =trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'));
		$data_post['WEIGHT'] =trim(htmlentities($data_id['WEIGHT'],ENT_QUOTES,'UTF-8'));
		$data_post['OIL_RECOVERY'] =trim(htmlentities($data_id['OIL_RECOVERY'],ENT_QUOTES,'UTF-8'));
		$data_post['SLUDGE'] =trim(htmlentities($data_id['SLUDGE'],ENT_QUOTES,'UTF-8'));
		$data_post['AIR'] =trim(htmlentities($data_id['AIR'],ENT_QUOTES,'UTF-8'));
		$data_post['EMULSI'] =trim(htmlentities($data_id['EMULSI'],ENT_QUOTES,'UTF-8'));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        
        $validate_numeric=$this->validate_numeric($data_post['WEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Berat material harus angka";
            $return['error']=true;        
        }
        
		$validate_numeric=$this->validate_numeric($data_post['OIL_RECOVERY']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Oil recovery harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['AIR']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Air harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['EMULSI']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Emulsi harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['SLUDGE']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Sludge harus angka";
            $return['error']=true;        
        }
		
        if (empty($data_post['ID_BA']) || trim($data_post['ID_BA'])==''){
            $return['status']="Harap isi ID_BA";
            $return['error']=true;   
        }elseif(strlen($data_post['ID_BA']) > 50){
            $return['status']="Panjang karakter ID_SOUNDING melebihi batas maksimal";
            $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['ADJUST_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['FROM_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['TO_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
				        
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
                
        if (empty($data_post['WEIGHT']) || trim($data_post['WEIGHT'])==''){
            $return['status']="Harap isi berat material";
            $return['error']=true;   
        }elseif(strlen($data_id['WEIGHT']) > 9){
            $return['status']="Panjang karakter berat material melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['OIL_RECOVERY']) || trim($data_post['OIL_RECOVERY'])==''){
            $return['status']="Harap isi OIL_RECOVERY";
            $return['error']=true;   
        }elseif(strlen($data_id['OIL_RECOVERY']) > 9){
            $return['status']="Panjang karakter OIL_RECOVERY melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['SLUDGE']) || trim($data_post['SLUDGE'])==''){
            $return['status']="Harap isi SLUDGE";
            $return['error']=true;   
        }elseif(strlen($data_id['SLUDGE']) > 9){
            $return['status']="Panjang karakter SLUDGE melebihi batas maksimal";
            $return['error']=true;
        }
		
        if(empty($return['status']) && $return['error']==false){     
            $insert_id = $this->model_s_oil_recycling->add_new($company,$data_post);
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
        	
		$data_post['ID_BA'] = strtoupper(trim(htmlentities($data_id['ID_BA'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['ADJUST_DATE'] = strtoupper(trim(htmlentities($data_id['ADJUST_DATE'],ENT_QUOTES,'UTF-8')));
		$data_post['ADJUST_DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['ADJUST_DESCRIPTION'],ENT_QUOTES,'UTF-8')));
        $data_post['FROM_DATE']=trim(htmlentities($data_id['FROM_DATE'],ENT_QUOTES,'UTF-8')); 
		$data_post['TO_DATE']=trim(htmlentities($data_id['TO_DATE'],ENT_QUOTES,'UTF-8')); 
        $data_post['ID_STORAGE'] =trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'));
		$data_post['WEIGHT'] =trim(htmlentities($data_id['WEIGHT'],ENT_QUOTES,'UTF-8'));
		$data_post['OIL_RECOVERY'] =trim(htmlentities($data_id['OIL_RECOVERY'],ENT_QUOTES,'UTF-8'));
		$data_post['SLUDGE'] =trim(htmlentities($data_id['SLUDGE'],ENT_QUOTES,'UTF-8'));
		$data_post['AIR'] =trim(htmlentities($data_id['AIR'],ENT_QUOTES,'UTF-8'));
		$data_post['EMULSI'] =trim(htmlentities($data_id['EMULSI'],ENT_QUOTES,'UTF-8'));
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
        
        $id_adjust=strtoupper(trim(htmlentities($data_id['ID_ADJUST'],ENT_QUOTES,'UTF-8'))) ;
        
        $validate_numeric=$this->validate_numeric($data_post['WEIGHT']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Berat material harus angka";
            $return['error']=true;        
        }
        
		$validate_numeric=$this->validate_numeric($data_post['OIL_RECOVERY']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Oil recovery harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['AIR']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Air harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['EMULSI']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Emulsi harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['SLUDGE']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Sludge harus angka";
            $return['error']=true;        
        }
		
        if (empty($data_post['ID_BA']) || trim($data_post['ID_BA'])==''){
            $return['status']="Harap isi ID_BA";
            $return['error']=true;   
        }elseif(strlen($data_post['ID_BA']) > 50){
            $return['status']="Panjang karakter ID_SOUNDING melebihi batas maksimal";
            $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['ADJUST_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['FROM_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
		
		$validate_date=$this->validate_date($data_post['TO_DATE']);
        if(!empty($validate_date)){
           $status=$validate_date; 
           $return['status']=$status;
           $return['error']=true;
        }
				        
        if (empty($data_post['ID_STORAGE']) || trim($data_post['ID_STORAGE'])==''){
            $return['status']="Harap isi ID STORAGE";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_STORAGE']) > 50){
            $return['status']="Panjang karakter ID_STORAGE melebihi batas maksimal";
            $return['error']=true;
        }
                
        if (empty($data_post['WEIGHT']) || trim($data_post['WEIGHT'])==''){
            $return['status']="Harap isi berat material";
            $return['error']=true;   
        }elseif(strlen($data_id['WEIGHT']) > 9){
            $return['status']="Panjang karakter berat material melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['OIL_RECOVERY']) || trim($data_post['OIL_RECOVERY'])==''){
            $return['status']="Harap isi OIL_RECOVERY";
            $return['error']=true;   
        }elseif(strlen($data_id['OIL_RECOVERY']) > 9){
            $return['status']="Panjang karakter OIL_RECOVERY melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['SLUDGE']) || trim($data_post['SLUDGE'])==''){
            $return['status']="Harap isi SLUDGE";
            $return['error']=true;   
        }elseif(strlen($data_id['SLUDGE']) > 9){
            $return['status']="Panjang karakter SLUDGE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $update_id = $this->model_s_oil_recycling->update_sounding($id_adjust,$data_post,$company);
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
        $id_sounding = strtoupper(trim(htmlentities($data_id['ID_ADJUST'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_sounding) || trim($id_sounding)=='' || $id_sounding==false){
            $return['status']="ID_ADJUST KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_sounding) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_oil_recycling->delete_sounding($id_sounding,$company);
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
        $data_storage = $this->model_s_oil_recycling->get_storage($q,$company);
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
        
        //echo json_encode($this->model_s_oil_recycling->search_data($kode_storage,$ar, $company));
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_oil_recycling->data_search($data['rules'], $company));
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

