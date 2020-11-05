<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class s_recycle extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_recycle');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        //$this->load->plugin('to_excel');
        $this->load->helper('file');
        
        $this->lastmenu="s_recycle";
        $this->data = array();    
    }
    
    function index(){
        $view="info_s_recycle";
        
        //$data = array();
        $this->data['judul_header'] = "Data Recycle CPO KERNEL";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');  
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_s_recycle->LoadData($periode,$company));   
    }
	
    function get_no_tiket(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_recycle = $this->model_s_recycle->get_no_tiket($q,$company,$tanggalm);
        //echo $q;
        $recycle = array();
        foreach($data_recycle as $row)
        {
            $recycle[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_TIMBANGAN'],ENT_QUOTES,'UTF-8')).
				'",res_dKendaraan:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).				
				'",res_dTara:"'.str_replace('"','\\"',htmlentities($row['BERAT_KOSONG'],ENT_QUOTES,'UTF-8')).
				'",res_dBruto:"'.str_replace('"','\\"',htmlentities($row['BERAT_ISI'],ENT_QUOTES,'UTF-8')).
				'",res_dNetto:"'.str_replace('"','\\"',htmlentities($row['BERAT_BERSIH'],ENT_QUOTES,'UTF-8')).
				'",res_dTglM:"'.str_replace('"','\\"',htmlentities($row['TANGGALM'],ENT_QUOTES,'UTF-8')).
				'",res_dTglK:"'.str_replace('"','\\"',htmlentities($row['TANGGALK'],ENT_QUOTES,'UTF-8')).
				'",res_dWaktuM:"'.str_replace('"','\\"',htmlentities($row['WAKTUM'],ENT_QUOTES,'UTF-8')).
				'",res_dWaktuK:"'.str_replace('"','\\"',htmlentities($row['WAKTUK'],ENT_QUOTES,'UTF-8')).
				'",res_dName:"'.str_replace('"','\\"',htmlentities($row['DRIVER_NAME'],ENT_QUOTES,'UTF-8')).				
                '",res_dl:"'.str_replace('"','\\"',
				 htmlentities($row['ID_TIMBANGAN'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
				.htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;"
                .htmlentities($row['BERAT_BERSIH'],ENT_QUOTES,'UTF-8'))." Kg".'"}';
        }
        echo '['.implode(',',$recycle).']'; exit;         
    }
	
	function get_jenis(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_recycle = $this->model_s_recycle->get_jenis($company);
        //echo $q;
        $recycle = array();
        foreach($data_recycle as $row)
        {
            $recycle[] = '{res_Jenis:"'.str_replace('"','\\"',htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8')).
				'",res_id:"'.str_replace('"','\\"',htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8')).						
                '",res_dl:"'.str_replace('"','\\"',
				 htmlentities($row['JENIS'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['ID_KOMODITAS'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$recycle).']'; exit;         
    }
	
	function get_doc(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); //no kendaraan
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $tanggalm = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $data_recycle = $this->model_s_recycle->get_doc($q,$company,$tanggalm);
        //echo $q;
        $recycle = array();
        foreach($data_recycle as $row)
        {
            $recycle[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ID_DISPATCH'],ENT_QUOTES,'UTF-8')).
				'",res_do:"'.str_replace('"','\\"',htmlentities($row['ID_DO'],ENT_QUOTES,'UTF-8')).						
                '",res_dl:"'.str_replace('"','\\"',
				 htmlentities($row['ID_DISPATCH'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['ID_DO'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$recycle).']'; exit;         
    }
	        
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_recycle->data_search($data['rules'], $company));  
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
            $this->update_data($data_id);        
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL"){
            $this->delete_data($data_id);  
        }else{
            $return['status'] ="Operation Unknown !!";
            $return['error']=true;
            echo json_encode($return);
        }      
    }
	
	function delete_data($data_id){
        $return['status']='';
        $return['error']=false;
       
        $id_timbang = trim(htmlentities($data_id['ID_RECYCLE'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($id_timbang) || trim($id_timbang)=='' || $id_timbang==false){
            $status = "ID_RECYCLE KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($id_timbang) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_recycle->delete_data($id_timbang,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }    
    }
	
    function update_data(){
		$data = json_decode($this->input->post('myJson'), true);
		$data_id=array();
		$data_id = $data["id"];		
		
		$return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
              
	    $id_timbang= trim(htmlentities($data_id['ID_RECYCLE'],ENT_QUOTES,'UTF-8')); 
		$data_post['ID_RECYCLE']=strtoupper(trim(htmlentities($data_id['ID_RECYCLE'],ENT_QUOTES,'UTF-8'))) ; 
        $data_post['ID_DISPATCH'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_KENDARAAN']=strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'))) ;       
        $data_post['TANGGAL'] = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;				
        $data_post['ID_KOMODITAS']=strtoupper(trim(htmlentities($data_id['ID_KOMODITAS'],ENT_QUOTES,'UTF-8'))) ;  
		$data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;	
        $data_post['BERAT_ISI'] = strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_BERSIH']=strtoupper(trim(htmlentities($data_id['BERAT_BERSIH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['COMPANY_CODE'] = $company ;	
		$data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
		$data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();		
        $data_post['ID_DO']=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;     
        $data_post['TANGGALM'] = trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8'));
        $data_post['TANGGALK'] = trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8'));
		$data_post['WAKTUM'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['WAKTUK']=strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')))) ;						
        $data_post['BROKEN'] = trim(htmlentities($data_id['BROKEN'],ENT_QUOTES,'UTF-8'));
        $data_post['DIRTY'] = trim(htmlentities($data_id['DIRTY'],ENT_QUOTES,'UTF-8'));  
		$data_post['MOIST'] = strtoupper(trim(htmlentities($data_id['MOIST'],ENT_QUOTES,'UTF-8'))) ;	
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ; 
		$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8'))) ;		
		$data_post['NO_SIM'] = strtoupper(trim(htmlentities($data_id['NO_SIM'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['NO_BA'] = strtoupper(trim(htmlentities($data_id['NO_BA'],ENT_QUOTES,'UTF-8'))) ;
		
        $validate_numeric=$this->validate_numeric(array($data_post['BERAT_ISI'],$data_post['BERAT_KOSONG'],$data_post['BERAT_BERSIH'],$data_post['BROKEN'],$data_post['DIRTY'],$data_post['MOIST']));
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai berat, dirty, broken dan moist harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_RECYCLE']) || trim($data_post['ID_RECYCLE'])==''){
            $return['status'] = "Harap isi ID_RECYCLE";
            $return['error']=true;          
        }elseif(strlen($data_post['ID_RECYCLE']) > 50){
            $return['status']  ="Panjang karakter ID_RECYCLE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['ID_DISPATCH']) || trim($data_post['ID_DISPATCH'])==''){
            $return['status'] = "Harap isi ID_DISPATCH";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_DISPATCH']) > 50){
            $return['status']  ="Panjang karakter ID_DISPATCH melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status'] = "Harap isi NO_KENDARAAN";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $return['status']  ="Panjang karakter NO_KENDARAAN melebihi batas maksimal";
            $return['error']=true;
        }
        
		if(empty($data_post['TANGGAL']) || $data_post['TANGGAL']==null || $data_post['TANGGAL']===false){
            $return['status']="Tanggal tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGAL'])) == '19700101'){
                $return['status']= "format datetime TANGGAL tidak benar";
                $return['error']=true;
            }   
        }
		
		if (empty($data_post['ID_KOMODITAS']) || trim($data_post['ID_KOMODITAS'])==''){
            $return['status'] = "Harap isi ID_KOMODITAS";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_KOMODITAS']) > 50){
            $return['status']  ="Panjang karakter ID_KOMODITAS melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['BERAT_ISI']) || trim($data_post['BERAT_ISI'])==''){
            $return['status']="Harap isi BERAT_ISI";
            $return['error']=true;  
        }elseif(strlen($data_id['BERAT_ISI']) > 50){
            $return['status']="Panjang karakter BERAT_ISI melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['BERAT_KOSONG']) || trim($data_post['BERAT_KOSONG'])==''){
            $return['status']="Harap isi BERAT_KOSONG";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_KOSONG']) > 50){
            $return['status']="Panjang karakter BERAT_KOSONG melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['BERAT_BERSIH']) || trim($data_post['BERAT_BERSIH'])==''){
            $return['status']="Harap isi BERAT_BERSIH";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_BERSIH']) > 50){
            $return['status']="Panjang karakter BERAT_BERSIH melebihi batas maksimal";
            $return['error']=true;
        }
			
		if (empty($data_post['ID_DO']) || trim($data_post['ID_DO'])==''){
            $return['status'] = "Harap isi ID_DO";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_DO']) > 50){
            $return['status']  ="Panjang karakter ID_DO melebihi batas maksimal";
            $return['error']=true;
        }
						
        if(empty($data_post['TANGGALM']) || $data_post['TANGGALM']==null || $data_post['TANGGALM']===false){
            $return['status']="Tanggal Masuk tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGALM'])) == '19700101'){
                $return['status']= "format datetime TANGGALM tidak benar";
                $return['error']=true;
            }   
        }
        
        if(empty($data_post['TANGGALK']) || $data_post['TANGGALK']==null || $data_post['TANGGALK']===false){
            $return['status']="Tanggal Keluar tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGALK'])) == '19700101'){
                $return['status']= "format datetime TANGGALK tidak benar";
                $return['error']=true;
            }   
        }
		
		if (empty($data_post['WAKTUM']) || trim($data_post['WAKTUM'])==''){
            $return['status'] = "Harap isi WAKTUM";
            $return['error']=true;   
        }elseif(strlen($data_id['WAKTUM']) > 50){
            $return['status']  ="Panjang karakter WAKTUM melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['WAKTUK']) || trim($data_post['WAKTUK'])==''){
            $return['status'] = "Harap isi WAKTUK";
            $return['error']=true;   
        }elseif(strlen($data_id['WAKTUK']) > 50){
            $return['status']  ="Panjang karakter WAKTUK melebihi batas maksimal";
            $return['error']=true;
        }
						
		if (empty($data_post['BROKEN']) || trim($data_post['BROKEN'])=='' || ($data_post['BROKEN'])== 0){
            $return['status']="BROKEN/FFA tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['BROKEN']) > 50){
            $return['status']="Panjang karakter BROKEN melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DIRTY']) || trim($data_post['DIRTY'])=='' || ($data_post['DIRTY'])== 0){
            $return['status']="DIRTY tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['DIRTY']) > 50){
            $return['status']="Panjang karakter DIRTY melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['MOIST']) || trim($data_post['MOIST'])==''  || ($data_post['MOIST'])== 0){
            $return['status']="MOIST tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['MOIST']) > 50){
            $return['status']="Panjang karakter MOIST melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status'] = "Harap isi DRIVER_NAME";
            $return['error']=true;   
        }elseif(strlen($data_id['DRIVER_NAME']) > 50){
            $return['status']  ="Panjang karakter DRIVER_NAME melebihi batas maksimal";
            $return['error']=true;
        }
       
	   	if (empty($data_post['JENIS']) || trim($data_post['JENIS'])==''){
            $return['status']="Harap isi JENIS";
            $return['error']=true;  
       	}elseif(strlen($data_id['JENIS']) > 50){
            $return['status']="Panjang karakter JENIS melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DESCRIPTION']) || trim($data_post['DESCRIPTION'])==''){
            $return['status']="Harap isi DESCRIPTION";
            $return['error']=true;  
       	}
		
		if (empty($data_post['NO_BA']) || trim($data_post['NO_BA'])==''){
            $return['status']="Harap isi NO_BA";
            $return['error']=true;  
       	}elseif(strlen($data_id['NO_BA']) > 50){
            $return['status']="Panjang karakter NO_BA melebihi batas maksimal";
            $return['error']=true;
        }
		
        if(empty($return['status']) && $return['error']==false){     
            $insert_id = $this->model_s_recycle->update_data($id_timbang,$company,$data_post);
            $return['status']=  $insert_id;
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

        $data_post['ID_RECYCLE']=strtoupper(trim(htmlentities($data_id['ID_RECYCLE'],ENT_QUOTES,'UTF-8'))) ; 
        $data_post['ID_DISPATCH'] = strtoupper(trim(htmlentities($data_id['ID_DISPATCH'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_KENDARAAN']=strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'))) ;       
        $data_post['TANGGAL'] = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;				
        $data_post['ID_KOMODITAS']=strtoupper(trim(htmlentities($data_id['ID_KOMODITAS'],ENT_QUOTES,'UTF-8'))) ;  
		$data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;	
        $data_post['BERAT_ISI'] = strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_BERSIH']=strtoupper(trim(htmlentities($data_id['BERAT_BERSIH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['COMPANY_CODE'] = $company ;	
		$data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
		$data_post['INPUT_DATE'] =  $this->global_func->gen_datetime();		
        $data_post['ID_DO']=strtoupper(trim(htmlentities($data_id['ID_DO'],ENT_QUOTES,'UTF-8'))) ;     
        $data_post['TANGGALM'] = trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8'));
        $data_post['TANGGALK'] = trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8'));
		$data_post['WAKTUM'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['WAKTUK']=strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')))) ;						
        $data_post['BROKEN'] = trim(htmlentities($data_id['BROKEN'],ENT_QUOTES,'UTF-8'));
        $data_post['DIRTY'] = trim(htmlentities($data_id['DIRTY'],ENT_QUOTES,'UTF-8'));  
		$data_post['MOIST'] = strtoupper(trim(htmlentities($data_id['MOIST'],ENT_QUOTES,'UTF-8'))) ;	
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ; 
		$data_post['JENIS'] = strtoupper(trim(htmlentities($data_id['JENIS'],ENT_QUOTES,'UTF-8'))) ;		
		$data_post['NO_SIM'] = strtoupper(trim(htmlentities($data_id['NO_SIM'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DESCRIPTION'] = strtoupper(trim(htmlentities($data_id['DESCRIPTION'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['NO_BA'] = strtoupper(trim(htmlentities($data_id['NO_BA'],ENT_QUOTES,'UTF-8'))) ;
			
        $validate_numeric=$this->validate_numeric(array($data_post['BERAT_ISI'],$data_post['BERAT_KOSONG'],$data_post['BERAT_BERSIH'],$data_post['BROKEN'],$data_post['DIRTY'],$data_post['MOIST']));
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai berat, dirty, broken dan moist harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_RECYCLE']) || trim($data_post['ID_RECYCLE'])==''){
            $return['status'] = "Harap isi ID_RECYCLE";
            $return['error']=true;          
        }elseif(strlen($data_post['ID_RECYCLE']) > 50){
            $return['status']  ="Panjang karakter ID_RECYCLE melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['ID_DISPATCH']) || trim($data_post['ID_DISPATCH'])==''){
            $return['status'] = "Harap isi ID_DISPATCH";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_DISPATCH']) > 50){
            $return['status']  ="Panjang karakter ID_DISPATCH melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status'] = "Harap isi NO_KENDARAAN";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $return['status']  ="Panjang karakter NO_KENDARAAN melebihi batas maksimal";
            $return['error']=true;
        }
        
		if(empty($data_post['TANGGAL']) || $data_post['TANGGAL']==null || $data_post['TANGGAL']===false){
            $return['status']="Tanggal tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGAL'])) == '19700101'){
                $return['status']= "format datetime TANGGAL tidak benar";
                $return['error']=true;
            }   
        }
		
		if (empty($data_post['ID_KOMODITAS']) || trim($data_post['ID_KOMODITAS'])==''){
            $return['status'] = "Harap isi ID_KOMODITAS";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_KOMODITAS']) > 50){
            $return['status']  ="Panjang karakter ID_KOMODITAS melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['BERAT_ISI']) || trim($data_post['BERAT_ISI'])==''){
            $return['status']="Harap isi BERAT_ISI";
            $return['error']=true;  
        }elseif(strlen($data_id['BERAT_ISI']) > 50){
            $return['status']="Panjang karakter BERAT_ISI melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['BERAT_KOSONG']) || trim($data_post['BERAT_KOSONG'])==''){
            $return['status']="Harap isi BERAT_KOSONG";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_KOSONG']) > 50){
            $return['status']="Panjang karakter BERAT_KOSONG melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['BERAT_BERSIH']) || trim($data_post['BERAT_BERSIH'])==''){
            $return['status']="Harap isi BERAT_BERSIH";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_BERSIH']) > 50){
            $return['status']="Panjang karakter BERAT_BERSIH melebihi batas maksimal";
            $return['error']=true;
        }
			
		if (empty($data_post['ID_DO']) || trim($data_post['ID_DO'])==''){
            $return['status'] = "Harap isi ID_DO";
            $return['error']=true;   
        }elseif(strlen($data_id['ID_DO']) > 50){
            $return['status']  ="Panjang karakter ID_DO melebihi batas maksimal";
            $return['error']=true;
        }
						
        if(empty($data_post['TANGGALM']) || $data_post['TANGGALM']==null || $data_post['TANGGALM']===false){
            $return['status']="Tanggal Masuk tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGALM'])) == '19700101'){
                $return['status']= "format datetime TANGGALM tidak benar";
                $return['error']=true;
            }   
        }
        
        if(empty($data_post['TANGGALK']) || $data_post['TANGGALK']==null || $data_post['TANGGALK']===false){
            $return['status']="Tanggal Keluar tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($data_post['TANGGALK'])) == '19700101'){
                $return['status']= "format datetime TANGGALK tidak benar";
                $return['error']=true;
            }   
        }
		
		if (empty($data_post['WAKTUM']) || trim($data_post['WAKTUM'])==''){
            $return['status'] = "Harap isi WAKTUM";
            $return['error']=true;   
        }elseif(strlen($data_id['WAKTUM']) > 50){
            $return['status']  ="Panjang karakter WAKTUM melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['WAKTUK']) || trim($data_post['WAKTUK'])==''){
            $return['status'] = "Harap isi WAKTUK";
            $return['error']=true;   
        }elseif(strlen($data_id['WAKTUK']) > 50){
            $return['status']  ="Panjang karakter WAKTUK melebihi batas maksimal";
            $return['error']=true;
        }
						
		if (empty($data_post['BROKEN']) || trim($data_post['BROKEN'])=='' || ($data_post['BROKEN'])== 0){
            $return['status']="BROKEN/FFA tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['BROKEN']) > 50){
            $return['status']="Panjang karakter BROKEN melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DIRTY']) || trim($data_post['DIRTY'])=='' || ($data_post['DIRTY'])== 0){
            $return['status']="DIRTY tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['DIRTY']) > 50){
            $return['status']="Panjang karakter DIRTY melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['MOIST']) || trim($data_post['MOIST'])==''  || ($data_post['MOIST'])== 0){
            $return['status']="MOIST tidak boleh 0 atau kosong";
            $return['error']=true;  
       	}elseif(strlen($data_id['MOIST']) > 50){
            $return['status']="Panjang karakter MOIST melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status'] = "Harap isi DRIVER_NAME";
            $return['error']=true;   
        }elseif(strlen($data_id['DRIVER_NAME']) > 50){
            $return['status']  ="Panjang karakter DRIVER_NAME melebihi batas maksimal";
            $return['error']=true;
        }
       
	   	if (empty($data_post['JENIS']) || trim($data_post['JENIS'])==''){
            $return['status']="Harap isi JENIS";
            $return['error']=true;  
       	}elseif(strlen($data_id['JENIS']) > 50){
            $return['status']="Panjang karakter JENIS melebihi batas maksimal";
            $return['error']=true;
        }
		
		if (empty($data_post['DESCRIPTION']) || trim($data_post['DESCRIPTION'])==''){
            $return['status']="Harap isi DESCRIPTION";
            $return['error']=true;  
       	}
		
		if (empty($data_post['NO_BA']) || trim($data_post['NO_BA'])==''){
            $return['status']="Harap isi NO_BA";
            $return['error']=true;  
       	}elseif(strlen($data_id['NO_BA']) > 50){
            $return['status']="Panjang karakter NO_BA melebihi batas maksimal";
            $return['error']=true;
        }

        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_s_recycle->add_new($company,$data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);          
        }else{
            echo json_encode($return);
        }
        
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
