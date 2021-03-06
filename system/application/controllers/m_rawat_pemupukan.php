<?php
class m_rawat_pemupukan extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_rawat_pemupukan');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_rawat_pemupukan";
		$this->load->library('csvReader');
        $this->data = array();    
    }
    
    function index(){
        $view="info_m_rawat_pemupukan";
        
        //$data = array();
        $this->data['judul_header'] = "Monitoring Rawat Pemupukan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['periode_rawat_pemupukan'] = $this->global_func->drop_date2('bulan_rawat_pemupukan','tahun_rawat_pemupukan','select');
        
        $this->data['menu']=$this->model_c_user_auth->get_menu($this->data['login_id'],$this->data['user_level'],$this->data['company_code'],$this->lastmenu); 
        
        if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
        } else {
            redirect('login');
        }
    }
    
    function LoadData(){
        
        $bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_m_rawat_pemupukan->LoadData($bulan,$tahun,$company));   
    }
    /*########################## PENETAPAN PERIODE rawat_pemupukan ###############################
    ##################################################################################*/
    function set_rawat_pemupukan_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'));
        
        $set_rawat_pemupukan_periode = $this->model_m_rawat_pemupukan->set_rawat_pemupukan_periode($periode,$company);
        echo $set_rawat_pemupukan_periode;
    }
    function get_rawat_pemupukan_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $rawat_pemupukan_periode = $this->model_m_rawat_pemupukan->get_rawat_pemupukan_periode($company);
        
        return $rawat_pemupukan_periode; 
    }
	
	function do_import(){
        $error = "";
		$msg = "";
		$fileElementName = 'myfile';
		$baris = 1;
		$ret = 0; 
		$reterror = 0;
		$boolean_error = false;
	
		$today = date('Ymd');
		$bulan = date("m",strtotime($today));
        $tahun = date("Y",strtotime($today));
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		
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
		}elseif(empty($_FILES['myfile']['tmp_name']) || $_FILES['myfile']['tmp_name'] == 'none'){
			$error = 'No file was uploaded..';
		}else{		
			$csvfile = $_FILES['myfile']['tmp_name'];
			$result =   $this->csvreader->parse_file($csvfile);		
			$ret = 0;
			foreach($result as $field){
				$boolean_error = false;
				$items = array();
				$data_lokasi = $this->model_m_rawat_pemupukan->lokasi_validate(substr($field['BLOK_TANAH'],0,2),$field['BLOK_TANAH'],$company); 					
				if(empty($data_lokasi)){						
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  lokasi ". $field['BLOK_TANAH']. " tidak ditemukan pada data master blok <br/>";
					$boolean_error = true;
				}
				
				$data_rawat_pemupukan = $this->model_m_rawat_pemupukan->get_rawat_pemupukan_blok($company, $field['BLOK_TANAH'], $today); 
				if($data_rawat_pemupukan>0){	
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  rawat pemupukan pada lokasi ". $field['BLOK_TANAH']. " Periode " .$bulan."-".$tahun." sudah pernah diinput <br/>";
					$boolean_error = true;
				}
				
				if ($boolean_error == false){
					$items = array(
							'BULAN' => $bulan,
							'TAHUN' => $tahun,
							'AFD' => substr($field['BLOK_TANAH'],0,2),
							'BLOK_TANAH' => $field['BLOK_TANAH'],
							'SISTEM_GANG' => $field['SISTEM_GANG'],
							'TEPAT_JENIS' => $field['TEPAT_JENIS'],
							'TEPAT_DOSIS' => $field['TEPAT_DOSIS'],
							'TEPAT_WAKTU' => $field['TEPAT_WAKTU'],
							'TEPAT_METODE' => $field['TEPAT_METODE'],
							'INPUT_BY' => trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),								
							'COMPANY_CODE' => $company
							);
					$query = $this->db->insert('s_rawat_pemupukan',$items);
					$ret += (int)$this->db->affected_rows($query);
				}
					
					$baris++;
			} 
			$msg = $ret . " data monitoring rawat pemupukan blok BERHASIL diupload, dan ". $reterror. " data rawat pemupukan blok GAGAL diupload";	
				//for security reason, we force to remove all uploaded file
			@unlink($_FILES['myfile']);		
		}				
		echo "{";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "'\n";
		echo "}";		
	}
	
    function search_data(){
        $bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'); 
        $afd = htmlentities($this->uri->segment('5'),ENT_QUOTES,'UTF-8');
        $block = htmlentities($this->uri->segment('6'),ENT_QUOTES,'UTF-8');
        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        echo json_encode($this->model_m_rawat_pemupukan->LoadData($bulan,$tahun,$company,$afd,$block));       
    }
    
    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_m_rawat_pemupukan->get_afdeling($company,$q);
         
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
        $data_afd = $this->model_m_rawat_pemupukan->get_block($company,$location_left,$q);
         
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
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DELL_ALL"){
			//var_dump($data_id);
			$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_all_data($data_id);    
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
        
        $data_post['BULAN'] = strtoupper(trim(htmlentities($data_id['BULAN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TAHUN'] = strtoupper(trim(htmlentities($data_id['TAHUN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['AFD']=strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BLOK_TANAH'] =strtoupper(trim(htmlentities($data_id['BLOK_TANAH'],ENT_QUOTES,'UTF-8')));
        $data_post['SISTEM_GANG']  =strtoupper(trim(htmlentities($data_id['SISTEM_GANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_JENIS']  =strtoupper(trim(htmlentities($data_id['TEPAT_JENIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_DOSIS']  =strtoupper(trim(htmlentities($data_id['TEPAT_DOSIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_WAKTU']  =strtoupper(trim(htmlentities($data_id['TEPAT_WAKTU'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_METODE']  =strtoupper(trim(htmlentities($data_id['TEPAT_METODE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;

        $validate_numeric=$this->validate_numeric($data_post['SISTEM_GANG']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai SISTEM_GANG harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_JENIS']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai TEPAT_JENIS harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_DOSIS']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai TEPAT_DOSIS harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_WAKTU']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai TEPAT_WAKTU harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_METODE']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai TEPAT_METODE harus angka";
            $return['error']=true;        
        }
		        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $status = "Harap isi AFD"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($data_id['AFD']) > 4){
            $status  ="Panjang karakter Afd melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_post['BLOK_TANAH']) || trim($data_post['BLOK_TANAH'])==''){
            $status = "Harap isi BLOK_TANAH";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['BLOK_TANAH']) > 25){
            $status  ="Panjang karakter Block melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_m_rawat_pemupukan->lokasi_validate($data_post['AFD'],$data_post['BLOK_TANAH'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOK_TANAH']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_rawat_pemupukan->add_new($company,$data_post);
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
        
        $data_post['BULAN'] = strtoupper(trim(htmlentities($data_id['BULAN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TAHUN'] = strtoupper(trim(htmlentities($data_id['TAHUN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['AFD']=strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['BLOK_TANAH'] =strtoupper(trim(htmlentities($data_id['BLOK_TANAH'],ENT_QUOTES,'UTF-8')));
        $data_post['SISTEM_GANG']  =strtoupper(trim(htmlentities($data_id['SISTEM_GANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_JENIS']  =strtoupper(trim(htmlentities($data_id['TEPAT_JENIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_DOSIS']  =strtoupper(trim(htmlentities($data_id['TEPAT_DOSIS'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_WAKTU']  =strtoupper(trim(htmlentities($data_id['TEPAT_WAKTU'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TEPAT_METODE']  =strtoupper(trim(htmlentities($data_id['TEPAT_METODE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['SISTEM_GANG']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai SISTEM_GANG harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_JENIS']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai TEPAT_JENIS harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_DOSIS']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai TEPAT_DOSIS harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_WAKTU']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai TEPAT_WAKTU harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TEPAT_METODE']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai TEPAT_METODE harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $status = "Harap isi AFD";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['AFD']) > 4){
            $status  ="Panjang karakter Afd melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_post['BLOK_TANAH']) || trim($data_post['BLOK_TANAH'])==''){
            $status = "Harap isi BLOK_TANAH";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['BLOK_TANAH']) > 25){
            $status  ="Panjang karakter Block melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_m_rawat_pemupukan->lokasi_validate($data_post['AFD'],$data_post['BLOK_TANAH'],$company);  
		
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOK_TANAH']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_rawat_pemupukan->update_rawat_pemupukan($data_post, $company);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }   
		
    }
    
    function delete_data($data_id){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $afd = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $block = strtoupper(trim(htmlentities($data_id['BLOK_TANAH'],ENT_QUOTES,'UTF-8'))) ;
        $bulan = strtoupper(trim(htmlentities($data_id['BULAN'],ENT_QUOTES,'UTF-8'))) ;
        $tahun = strtoupper(trim(htmlentities($data_id['TAHUN'],ENT_QUOTES,'UTF-8'))) ;
		
        $delete_rawat_pemupukan = $this->model_m_rawat_pemupukan->delete_rawat_pemupukan($afd,$block,$bulan,$tahun,$company);
        $return['status']=  $delete_rawat_pemupukan;
        $return['error']=false;
        echo json_encode($return);
    }
	
	function delete_all_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_post = array();
		$data = json_decode($this->input->post('myJson'), true);
		$data_detail = $data["detail"]; 
        $data_id = $data["id"];
		//var_dump($data_id);
		
		$int=0;
		$tmp_identifier='';	
		$bulan = '';
		
		foreach($data_detail as $key => $val){
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT); 
            $tmp_identifier=$int; 
			

			if (preg_match('/BULAN/',$key)){
				$data_post[$tmp_identifier]=array('BULAN'=>$val);
			}elseif (preg_match('/TAHUN/',$key)){				
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TAHUN'=>$val)); 
			}elseif (preg_match('/ID_RAWAT_PEMUPUKAN/',$key)){
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('ID_RAWAT_PEMUPUKAN'=>$val));             
			}elseif (preg_match('/AFD/',$key)){
                $tmp_afd=trim($val);
                $data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('AFD'=>$val));			
			}elseif (preg_match('/BLOK_TANAH/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('BLOK_TANAH'=>$val));   
			}elseif (preg_match('/SISTEM_GANG/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('SISTEM_GANG'=>$val)); 
			}elseif (preg_match('/TEPAT_JENIS/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TEPAT_JENIS'=>$val)); 
			}elseif (preg_match('/TEPAT_DOSIS/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TEPAT_DOSIS'=>$val)); 
			}elseif (preg_match('/TEPAT_WAKTU/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TEPAT_WAKTU'=>$val)); 
			}elseif (preg_match('/TEPAT_METODE/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TEPAT_METODE'=>$val)); 
			}
			
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('ACTIVE'=>0));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('COMPANY_CODE'=>$company));
		} //for   
		
		//var_dump($data_post);
		if ($data_post == null){
			$return['status']="Data kosong, tidak dapat di delete \r\n";
			$return['error']=true;		
		}
		
        if(empty($return['status']) && $return['error']==false){    
            $insert_id = $this->model_m_rawat_pemupukan->delete_all_data($data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }   
		
    }
    
    function search_data_2(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8') ;
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        $ar=(empty($ar) || $ar===false)?'-':$ar;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_m_rawat_pemupukan->data_search($data['rules'], $company));
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
	function xls_month(){
		$company_code = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');	
		$bulan = substr($periode,4,2);
		$tahun = substr($periode,0,4);
		$judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
		$headers .= "PT. ".$company."\n";
		$headers .= "MONITORING RAWAT PEMUPUKAN \n";
		$headers .= "PERIODE: ". $periode ."\n";
		$headers .= "\n";
		$headers .= "NO. \t";
		$headers .= "BLOK TANAH \t";
		$headers .= "PLANTED (HA) \t";
		$headers .= "POKOK \t";	
		$headers .= "SPH \t";	
		$headers .= "SISTEM GANG \t";
		$headers .= "TEPAT JENIS \t";
		$headers .= "TEPAT DOSIS \t";
		$headers .= "TEPAT WAKTU \t";		
		$headers .= "TEPAT METODE \t";
		
		$no = 1;
		$data=$this->model_m_rawat_pemupukan->get_xls($company_code, $bulan, $tahun);
		if($data!=NULL){
			foreach ($data as $row){
				$line = '';
				$line .= str_replace('"', '""',$no)."\t"; 
				$line .= str_replace('"', '""',$row['BLOCKID'])."\t";
				$line .= str_replace('"', '""',$row['HECTPLANTED'])."\t";
				$line .= str_replace('"', '""',$row['NUMPLANTATION'])."\t";
				$line .= str_replace('"', '""',$row['SPH'])."\t";
				$line .= str_replace('"', '""',$row['SISTEM_GANG'])."\t";
				$line .= str_replace('"', '""',$row['TEPAT_JENIS'])."\t";
				$line .= str_replace('"', '""',$row['TEPAT_DOSIS'])."\t";
				$line .= str_replace('"', '""',$row['TEPAT_WAKTU'])."\t";
				$line .= str_replace('"', '""',$row['TEPAT_METODE'])."\t";
				$no++;
				$data .= trim($line)."\n"; 
			
			}
			$data = str_replace("\r","",$data);
			$data = str_replace("Array","",$data);
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=HPT_rawat_pemupukan".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
		}
		
	}
}
?>
