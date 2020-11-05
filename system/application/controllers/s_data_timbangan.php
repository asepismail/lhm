<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class s_data_timbangan extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_s_data_timbangan');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->load->plugin('to_excel');
        $this->load->helper('file');
        
        $this->lastmenu="s_data_timbangan";
        $this->data = array();    
    }
    
    function index(){
        $view="info_s_data_timbangan";
        
        //$data = array();
        $this->data['judul_header'] = "Data Timbangan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['type_buah'] = $this->dropdownlist_typebuah();
        $this->data['type_timbang'] = $this->dropdownlist_typetimbang();
        //$this->data['bjr_periode'] = $this->get_bjr_periode();
        
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
        echo json_encode($this->model_s_data_timbangan->LoadData($periode,$company));   
    }
    
    function dropdownlist_typebuah(){
        $string = "<select  name='tbg_input' class='select'  id='TYPE_BUAH' style='width:200px'>";
        $string .= "<option value='1' >INTI</option>
            <option value='2'>LUAR</option>
			<option value='3'>PLASMA</option>
			<option value='4'>AFILIASI</option>";  
        /*$data_afd = $this->model_rpt_du->get_gc($this->session->userdata('DCOMPANY'));       
        foreach ( $data_afd as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['GANG_CODE']."\"  selected>".$row['GANG_CODE']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['GANG_CODE']."\">".$row['GANG_CODE']." </option>";
            }
        }
        */
        $string =$string. "</select>";
        return $string;
    }
    function dropdownlist_typetimbang(){
        $string = "<select  name='tbg_input' class='select'  id='TYPE_TIMBANG' style='width:200px'>";
        $string .= "<option value='2'>TIMBANGAN LUAR</option>
			<option value='1'>TIMBANGAN DALAM</option>";  
        $string =$string. "</select>";
        return $string;
    }
    
    function search_data(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data = json_decode($this->input->post('filters'), true); 
        echo json_encode($this->model_s_data_timbangan->data_search($data['rules'], $company));  
    }
    
    function search_spb(){
        $spb = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $no_kend = trim(htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8') ;
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_data_timbangan->search_spb($spb, $no_kend,$ar, $company));
    }
    
    function get_no_mesin(){
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $no_kendaraan = $this->model_s_data_timbangan->get_no_mesin($q,$company,$periode);
         
        //echo $q;
        $kendaraan = array();
        foreach($no_kendaraan as $row)
        {
            $kendaraan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).
            '",res_name:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).
            '",res_dl:"'.str_replace('"','\\"',htmlentities($row['NO_KENDARAAN'],ENT_QUOTES,'UTF-8')).'"}';
        }
        echo '['.implode(',',$kendaraan).']'; exit;     
        
    }
    
    function grid_data_timbangan(){    
        $no_tiket = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $vc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8'); 
        $periode = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_s_data_timbangan->load_grid_timbangan($vc, $no_tiket, $company));
        //echo json_encode($this->model_vehicle_activity->grid_vehicle_activity());
    }
    
    function load_nota_info(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $periode = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8') ;
        $no_kendaraan = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8') ; 
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        echo json_encode($this->model_s_data_timbangan->load_nota_info($company,$ar,$no_kendaraan));
        
    }
    
    function do_upload(){
        /*
        $error = "NO ERROR OCCUR !!";
        $msg = "";
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'DBF';
        $config['max_size'] = '1000'; 
        
        $this->load->library('upload', $config);
        $this->upload->display_errors('','');
        if ( ! $this->upload->do_upload("fileToUpload")){
              $error = $this->upload->display_errors();
              $error .= 'is an invalid file type!';
        }
        else{
            $file = $_FILES['fileToUpload'];
            $filename = $file['name'];
            $file_basename = substr($filename, 0, strripos($filename, '.')); // strip extention
            $file_ext      = substr($filename, strripos($filename, '.'));

            $dbf = $this->model_s_data_timbangan->LoadData_WB($filename);
            $msg ='Import data berhasil !!';
            //@unlink($_FILES['fileToUpload']['name']);
            /*$msg= 'Import data berhasil !!<br/>'.
                '<a href="javascript:history.go(-1);">'.
                '&lt;&lt Kembali</a>';
            //redirect('s_data_timbangan');
            

        }
        /*echo "{";
        echo    "error: '" . $error . "'"."<br>";
        echo    "msg: '" . $msg . "'n";
        echo "}";*/ 
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        if(!empty($_FILES[$fileElementName]['error'])){
            
            switch($_FILES[$fileElementName]['error']){
                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        } elseif(empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none'){
            $error = 'No file was uploaded..';
        } else {   
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'DBF';
            $config['max_size'] = '1000'; 
            
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload("fileToUpload")){
                  $error = $this->upload->display_errors();
                  $error .= 'is an invalid file type!';
            }else{
                $periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
                $periode_to = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');
                $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
                
                /*$ar = preg_split('/[- :]/',trim($periode));
                $ar = implode('',$ar);
                $ar=(empty($ar) || $ar===false)?'-':$ar;
                
                $ar2 = preg_split('/[- :]/',trim($periode_to));
                $ar2 = implode('',$ar2);
                $ar2=(empty($ar2) || $ar2===false)?'-':$ar2;
                
                if(empty($ar2)){
                   //$ar2=$ar; 
                }*/
                $ar = date('Ymd',strtotime($periode));
                $ar2 = date('Ymd',strtotime($periode_to));
                $ar=(empty($ar) || $ar===false)?'-':$ar;
                $ar2=(empty($ar2) || $ar2===false)?'-':$ar2;
                
                $dbf = $this->model_s_data_timbangan->LoadData_WB($_FILES['fileToUpload']['name'],$ar,$ar2,$company);
                
                $msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
                $msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name'])." -- Telah di Capture dengan Sukses";
                //for security reason, we force to remove all uploaded file
                $delfile= @unlink('uploads/'.$_FILES['fileToUpload']['name']) or die("Unlink File Gagal !!");   
            }
                        
        }        
        echo "{";
        echo                "error: '" . $error . "',\n";
        echo                "msg: '" . $msg . "'\n";
        echo "}"; 
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
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "PRINT"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $print_type = $this->uri->segment('3');
                $this->print_report($data_id,$print_type);    
            }else{
                $return['status'] ="User tidak berwenang !!";
                $return['error']=true;
                echo json_encode($return);    
            }               
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "XLS"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"PRINT",$loginid);
            if($is_auth_user_command['0']['ROLE_REPORT']=='1'){
                $this->generate_xls_tbg();    
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
	
	function delete_data($data_id){
        $return['status']='';
        $return['error']=false;
       
        $id_timbang = trim(htmlentities($data_id['ID_TIMBANGAN'],ENT_QUOTES,'UTF-8'));
        $company =trim(htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8'));
 
        if (empty($id_timbang) || trim($id_timbang)=='' || $id_timbang==false){
            $status = "ID_TIMBANGAN KOSONG !!"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($id_timbang) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']==false){     
            $delete_id = $this->model_s_data_timbangan->delete_data($id_timbang,$company);
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
         
        $id_timbang= trim(htmlentities($data_id['ID_TIMBANGAN'],ENT_QUOTES,'UTF-8')); 
        $data_post['NO_TIKET']=strtoupper(trim(htmlentities($data_id['NO_TIKET'],ENT_QUOTES,'UTF-8'))) ; 
        $data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGALK']=strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['WAKTUM'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['WAKTUK']=strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'))) ;				
        $data_post['JENIS_MUATAN']=strtoupper(trim(htmlentities($data_id['JENIS_MUATAN'],ENT_QUOTES,'UTF-8'))) ;  
        $data_post['BERAT_ISI'] = strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;		
        $data_post['NO_SPB']=strtoupper(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['COMPANY_CODE'] = $company ;
        $data_post['TYPE_BUAH'] = trim(htmlentities($data_id['TYPE_BUAH'],ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_TIMBANG'] = trim(htmlentities($data_id['TYPE_TIMBANG'],ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KENDARAAN'] = trim(htmlentities($data_id['TYPE_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $data_post['NOTE'] = trim(htmlentities($data_id['NOTE'],ENT_QUOTES,'UTF-8'));
        //$data_post['UPDATE_BY'] = trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'));
		//$data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime();
		
		//Added by Asep, 20130827
		$data_post['JJG'] = strtoupper(trim(htmlentities($data_id['JJG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ; 
		$data_post['BERAT_BERSIH']=($data_id['BERAT_ISI']-$data_id['BERAT_KOSONG']-$data_id['BERAT_GRADING']);
		$data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))) ;
		//$data_post['SINKRON_STATUS'] =  "1";
		//$data_post['FLAG_TIMBANGAN'] =  "1";
		$data_post['GRD_BUAHMENTAH'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHMENTAH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BUAHBUSUK'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHBUSUK'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BUAHKECIL'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHKECIL'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_TANGKAIPANJANG'] = strtoupper(trim(htmlentities($data_id['GRD_TANGKAIPANJANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BRONDOLAN'] = strtoupper(trim(htmlentities($data_id['GRD_BRONDOLAN'],ENT_QUOTES,'UTF-8'))) ;		
		$data_post['GRD_LAINNYA'] = strtoupper(trim(htmlentities($data_id['GRD_LAINNYA'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['BERAT_GRADING'] = strtoupper(trim(htmlentities($data_id['BERAT_GRADING'],ENT_QUOTES,'UTF-8'))) ;
		//end: Added by Asep, 20130827
		
        $validate_numeric=$this->validate_numeric(array($data_post['BERAT_ISI'],$data_post['BERAT_KOSONG']));
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai BERAT_ISI dan BERAT_KOSONG harus angka";
            $return['error']=true;        
        }
                        
        if (empty($data_post['NO_TIKET']) || trim($data_post['NO_TIKET'])==''){
            $return['status'] = "Harap isi NO_TIKET";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_TIKET']) > 50){
            $return['status']  ="Panjang karakter NO_TIKET melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status'] = "Harap isi NO_KENDARAAN";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $return['status']  ="Panjang karakter NO_KENDARAAN melebihi batas maksimal";
            $return['error']=true;
        }
        
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status'] = "Harap isi DRIVER_NAME";
            $return['error']=true;   
        }elseif(strlen($data_id['DRIVER_NAME']) > 50){
            $return['status']  ="Panjang karakter DRIVER_NAME melebihi batas maksimal";
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
		
		if (empty($data_post['BERAT_GRADING']) || trim($data_post['BERAT_GRADING'])==''){
            $return['status']="Harap isi Total Grading";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_GRADING']) > 50){
            $return['status']="Panjang karakter Total Grading melebihi batas maksimal";
            $return['error']=true;
        }
	   	//Added by Asep, 20130826
		if (empty($data_post['NO_SPB']) || trim($data_post['NO_SPB'])==''){
            $return['status']="Harap isi NO NAB";
            $return['error']=true;  
       	}
		if (empty($data_post['JJG']) || trim($data_post['JJG'])==''){
            $return['status']="Harap isi JJG";
            $return['error']=true;  
       	}elseif(strlen($data_id['JJG']) > 50){
            $return['status']="Panjang karakter JJG melebihi batas maksimal";
            $return['error']=true;
        }
		//Added by Asep, 20130826
		if (empty($data_post['JENIS_MUATAN']) || trim($data_post['JENIS_MUATAN'])==''){
            $return['status']="Harap isi JENIS_MUATAN";
            $return['error']=true;  
       	}
	   
		if(strlen($data_id['BERAT_KOSONG']) > 50){
            $return['status']="Panjang karakter BERAT_KOSONG melebihi batas maksimal";
            $return['error']=true;
        }
		
        if(empty($return['status']) && $return['error']==false){     
            $insert_id = $this->model_s_data_timbangan->update_data($id_timbang,$company,$data_post);
            //$return['status']=  $insert_id;
            //$return['error']=false;
            echo json_encode($insert_id);          
        }else{
            echo json_encode($return);
        }	
	}
    function print_report($data_id,$print_type){
        $return['status']='';
        $return['error']=false;
 
        if (empty($print_type) || trim($print_type)==='' || $print_type===false){
            $status = "Harap pilih tipe pelaporan..."; 
            $return['status']=$status;
            $return['error']=true;  
        }
        
        if(empty($return['status']) && $return['error']===false){     
            if (trim(strtoupper($print_type))==="PDF"){
                $prints=$this->print_pdf($data_id);
                $return['status'] =$prints;
                $return['error']=false;
                        
            }elseif(trim(strtoupper($print_type))==="XLS"){
                $return['error']=false; 
                $this->generate_tbg_xls($data_id);
                    
            }else{
                $return['status'] ="Operation Unknown !!";
                $return['error']=true;    
            }
            echo json_encode($return);              
        }else{
            echo json_encode($return);
        }    
    }
    
    function add_new($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
         
        $data_post['ID_TIMBANGAN'] =  $this->global_func->createMy_ID('s_data_timbangan','ID_TIMBANGAN',$company."TB","TANGGALM",$company); ;
        $data_post['NO_TIKET']=strtoupper(trim(htmlentities($data_id['NO_TIKET'],ENT_QUOTES,'UTF-8'))) ; 
        $data_post['TANGGALM'] = strtoupper(trim(htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGALK']=strtoupper(trim(htmlentities($data_id['TANGGALK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['WAKTUM'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUM'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['WAKTUK']=strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['WAKTUK'],ENT_QUOTES,'UTF-8')))) ;
        $data_post['NO_KENDARAAN'] = strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'))) ;				
        $data_post['JENIS_MUATAN']=strtoupper(trim(htmlentities($data_id['JENIS_MUATAN'],ENT_QUOTES,'UTF-8'))) ;  
        $data_post['BERAT_ISI'] = strtoupper(trim(htmlentities($data_id['BERAT_ISI'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BERAT_KOSONG']=strtoupper(trim(htmlentities($data_id['BERAT_KOSONG'],ENT_QUOTES,'UTF-8'))) ;		
        $data_post['NO_SPB']=strtoupper(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['COMPANY_CODE'] = $company ;
        $data_post['TYPE_BUAH'] = trim(htmlentities($data_id['TYPE_BUAH'],ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_TIMBANG'] = trim(htmlentities($data_id['TYPE_TIMBANG'],ENT_QUOTES,'UTF-8'));
        $data_post['TYPE_KENDARAAN'] = trim(htmlentities($data_id['TYPE_KENDARAAN'],ENT_QUOTES,'UTF-8'));
        $data_post['NOTE'] = trim(htmlentities($data_id['NOTE'],ENT_QUOTES,'UTF-8'));
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
		
		//Added by Asep, 20130827
		$data_post['JJG'] = strtoupper(trim(htmlentities($data_id['JJG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['DRIVER_NAME'] = strtoupper(trim(htmlentities($data_id['DRIVER_NAME'],ENT_QUOTES,'UTF-8'))) ; 
		$data_post['AFD'] = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['SUPPLIERCODE'] = strtoupper(trim(htmlentities($data_id['SUPPLIERCODE'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['BERAT_BERSIH']=($data_id['BERAT_ISI']-$data_id['BERAT_KOSONG']-$data_id['BERAT_GRADING']);
		$data_post['INPUT_DATE'] =  $this->global_func->gen_datetime();
		$data_post['SINKRON_STATUS'] =  "1";
		$data_post['FLAG_TIMBANGAN'] =  "1";
		$data_post['GRD_BUAHMENTAH'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHMENTAH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BUAHBUSUK'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHBUSUK'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BUAHKECIL'] = strtoupper(trim(htmlentities($data_id['GRD_BUAHKECIL'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_TANGKAIPANJANG'] = strtoupper(trim(htmlentities($data_id['GRD_TANGKAIPANJANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GRD_BRONDOLAN'] = strtoupper(trim(htmlentities($data_id['GRD_BRONDOLAN'],ENT_QUOTES,'UTF-8'))) ;		
		$data_post['GRD_LAINNYA'] = strtoupper(trim(htmlentities($data_id['GRD_LAINNYA'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['BERAT_GRADING'] = strtoupper(trim(htmlentities($data_id['BERAT_GRADING'],ENT_QUOTES,'UTF-8'))) ;
		//end: Added by Asep, 20130827
		
        $validate_numeric=$this->validate_numeric(array($data_post['BERAT_ISI'],$data_post['BERAT_KOSONG']));
        if( strtolower($validate_numeric)=='false'){
            $return['status'] ="Nilai BERAT_ISI dan BERAT_KOSONG harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_TIMBANGAN']) || trim($data_post['ID_TIMBANGAN'])==''){
            $return['status'] = "Harap isi ID_TIMBANGAN";
            $return['error']=true;          
        }elseif(strlen($data_post['ID_TIMBANGAN']) > 50){
            $return['status']  ="Panjang karakter ID_TIMBANGAN melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['NO_TIKET']) || trim($data_post['NO_TIKET'])==''){
            $return['status'] = "Harap isi NO_TIKET";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_TIKET']) > 50){
            $return['status']  ="Panjang karakter NO_TIKET melebihi batas maksimal";
            $return['error']=true;
        }
        
        if (empty($data_post['NO_KENDARAAN']) || trim($data_post['NO_KENDARAAN'])==''){
            $return['status'] = "Harap isi NO_KENDARAAN";
            $return['error']=true;   
        }elseif(strlen($data_id['NO_KENDARAAN']) > 50){
            $return['status']  ="Panjang karakter NO_KENDARAAN melebihi batas maksimal";
            $return['error']=true;
        }
        
		if (empty($data_post['DRIVER_NAME']) || trim($data_post['DRIVER_NAME'])==''){
            $return['status'] = "Harap isi DRIVER_NAME";
            $return['error']=true;   
        }elseif(strlen($data_id['DRIVER_NAME']) > 50){
            $return['status']  ="Panjang karakter DRIVER_NAME melebihi batas maksimal";
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
		
		if (empty($data_post['BERAT_GRADING']) || trim($data_post['BERAT_GRADING'])==''){
            $return['status']="Harap isi total grading";
            $return['error']=true;  
       	}elseif(strlen($data_id['BERAT_GRADING']) > 50){
            $return['status']="Panjang karakter total grading melebihi batas maksimal";
            $return['error']=true;
        }
	   	//Added by Asep, 20130826
		if (empty($data_post['NO_SPB']) || trim($data_post['NO_SPB'])==''){
            $return['status']="Harap isi NO NAB";
            $return['error']=true;  
       	}
		if (empty($data_post['JJG']) || trim($data_post['JJG'])==''){
            $return['status']="Harap isi JJG";
            $return['error']=true;  
       	}elseif(strlen($data_id['JJG']) > 50){
            $return['status']="Panjang karakter JJG melebihi batas maksimal";
            $return['error']=true;
        }
		//Added by Asep, 20130826
		if (empty($data_post['JENIS_MUATAN']) || trim($data_post['JENIS_MUATAN'])==''){
            $return['status']="Harap isi JENIS_MUATAN";
            $return['error']=true;  
       	}
	   
		if(strlen($data_id['BERAT_KOSONG']) > 50){
            $return['status']="Panjang karakter BERAT_KOSONG melebihi batas maksimal";
            $return['error']=true;
        }

        if(empty($return['status']) && $return['error']===false){     
            $insert_id = $this->model_s_data_timbangan->add_new($company,$data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($insert_id);          
        }else{
            echo json_encode($return);
        }
        
    }
    
    function update_spb_timbangan(){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('myJson'), true);
        $data_id=array();
        $data_id = $data["id"];
         
        $data_post['NO_SPB'] = strtoupper(trim(htmlentities($data_id['NO_SPB'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['NO_KENDARAAN']=strtoupper(trim(htmlentities($data_id['NO_KENDARAAN'],ENT_QUOTES,'UTF-8'))) ;
    
        //$tmp_spb= strtoupper(trim(htmlentities($data_id['TMP_SPB'],ENT_QUOTES,'UTF-8'))) ;
        $id_timbangan=strtoupper(trim(htmlentities($data_id['ID_TIMBANGAN'],ENT_QUOTES,'UTF-8'))) ;
        $id_nt_ab=strtoupper(trim(htmlentities($data_id['ID_NT_AB'],ENT_QUOTES,'UTF-8')));
        //$no_tiket= strtoupper(trim(htmlentities($data_id['NO_TIKET'],ENT_QUOTES,'UTF-8')));
        if (empty($id_timbangan) || trim($id_timbangan)==''){
            $return['status']="Harap isi id_timbangan";
            $return['error']=true;
            unset ($id_timbangan);   
        }elseif(strlen($id_timbangan) > 50){
            $return['status']="Panjang karakter id_timbangan melebihi batas maksimal";
            $return['error']=true;
            unset ($id_timbangan);
        }
        
        if (empty($id_nt_ab) || trim($id_nt_ab)==''){
            $return['status']="Harap isi id_notabuah";
            $return['error']=true;
            unset ($id_nt_ab);   
        }elseif(strlen($id_nt_ab) > 50){
            $return['status']="Panjang karakter id_notabuah melebihi batas maksimal";
            $return['error']=true;
            unset ($id_nt_ab);
        }
        
        if (empty($data_post['NO_SPB']) || trim($data_post['NO_SPB'])==''){
            $return['status']="Harap isi NO_SPB";
            $return['error']=true; 
        }elseif(strlen($data_id['NO_SPB']) > 50){
            $return['status']="Panjang karakter NO_SPB melebihi batas maksimal";
            $return['error']=true;
        }
        
        /*if (empty($no_tiket) || trim($no_tiket)==''){
            $return['status']="Harap isi NO_TIKET";
            $return['error']=true;  
        }elseif(strlen($no_tiket) > 50){
            $return['status']="Panjang karakter NO_TIKET melebihi batas maksimal";
            $return['error']=true;
        }   
        if(!empty($tmp_spb)){
            $status='Operation Not Allowed !!';
        }*/
        if(empty($return['status']) && $return['error']===false){     
            $update_id = $this->model_s_data_timbangan->update_spb_timbangan($id_timbangan,$id_nt_ab,$company,$data_post);
            $return['status']=  $update_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }    
    }
    
    function generate_tiket(){
        $no_tiket = $this->global_func->id_GAD('s_data_timbangan','NO_TIKET',str_replace(" ","","TBG"));
        echo $no_tiket;   
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
    
    function generate_tbg_xls($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($data_id['TANGGALM'],ENT_QUOTES,'UTF-8');
        $jns_muatan = 'TBS';
        
        if (empty($periode) || trim($periode)==''){
            $return['status']="Harap isi Periode";
            $return['error']=true; 
        }elseif(strlen($periode) > 50){
            $return['status']="Panjang karakter Periode melebihi batas maksimal";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($periode)) == '19700101'){
                $return['status']= "format datetime Periode tidak benar";
                $return['error']=true;
            }   
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $judul = '';
            $headers = ''; // just creating the var for field headers to append to below
            $data = ''; // just creating the var for field data to append to below
            $footer = '';
            
            $obj =& get_instance();

            $data_tbg = $this->model_s_data_timbangan->generate_tbg_xls($jns_muatan,$periode,$company);      

            //baris 1
            $headers .= "No \t";

            $headers .= "No Tiket \t";
            $headers .= "No SPB \t";
            $headers .= "Tanggal Masuk \t";
            $headers .= "Tanggal Keluar \t";    
            $headers .= "No Kendaraan \t";
            $headers .= "Berat Isi\t";
            $headers .= "Berat Kosong \t";
            $headers .= "Berat Bersih \t";
     
            $no = 1;
            $total_netto=0;
            foreach ($data_tbg as $row){
                $line = '';
                        
                $line .= str_replace('"', '""',$row['NO_TIKET'])."\t";
                $line .= str_replace('"', '""',$row['NO_SPB'])."\t";
                $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
                $line .= str_replace('"', '""',$row['TANGGALK'])."\t";
                $line .= str_replace('"', '""',$row['NO_KENDARAAN'])."\t";
                $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
                $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
                $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";

                $total_netto = $total_netto + $row['BERAT_BERSIH'];            
                $no++;
            
                $data .= trim($line)."\n";
                
            }        

            $footer .= " Total \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= str_replace('"', '""',$total_netto)."\t";

          
            $data .= trim($footer)."\n";
            
            $data = str_replace("\r","",$data);
                             
                             
            //header("Content-type: application/vnd.ms-excel");
            //header("Content-Disposition: attachment; filename=TBG_".$company."_".$periode.".xls");       
            //echo "$judul\n$headers\n$data";
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/x-msdownload;");
            echo "$judul\n$headers\n$data";
            //$return['status']=  "$judul\n$headers\n$data";
            //$return['error']=false;
            //echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
        
    }
    
    function generate_xls_tbg(){ 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();

        $data_produksi=$this->model_s_data_timbangan->generate_xls_tbg();

        //baris 1
        $headers .= "Tanggal \t";
        $headers .= "No Tiket \t";
        $headers .= "No Kendaraan \t";
        $headers .= "Berat isi(Kg) \t";
        $headers .= "Berat Kosong(Kg) \t";
        $headers .= "Berat Bersih(Kg) \t";
        $headers .= "Supplier \t";
        
        $no = 1;
        $total_netto=0;
        foreach ($data_produksi as $row){
            $line = '';
                   
            $line .= str_replace('"', '""',$row['TANGGALM'])."\t";
            $line .= str_replace('"', '""',$row['NO_TIKET'])."\t"; 
            $line .= str_replace('"', '""',$row['NO_KEDARAAN'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_ISI'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_KOSONG'])."\t";
            $line .= str_replace('"', '""',$row['BERAT_BERSIH'])."\t";
            $line .= str_replace('"', '""',$row['SUPPLIERCODE'])."\t";
            
            $no++;
            $data .= trim($line)."\n";  
        }        
        
        $data = str_replace("\r","",$data);
                 
        //header("Content-type: application/vnd.ms-excel");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=DATA_".$company.".xls");
        echo "$judul\n$headers\n$data";
    }   
}
?>
