<?php
class m_rawat_rutin extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_rawat_rutin');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_rawat_rutin";
		$this->load->library('csvReader');
        $this->data = array();    
    }
    
    function index(){
        $view="info_m_rawat_rutin";
        
        //$data = array();
        $this->data['judul_header'] = "Monitoring Rawat Rutin";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['periode_rawat_rutin'] = $this->global_func->drop_date2('bulan_rawat_rutin','tahun_rawat_rutin','select');
        
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
        
        echo json_encode($this->model_m_rawat_rutin->LoadData($bulan,$tahun,$company));   
    }
    /*########################## PENETAPAN PERIODE rawat_rutin ###############################
    ##################################################################################*/
    function set_rawat_rutin_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'));
        
        $set_rawat_rutin_periode = $this->model_m_rawat_rutin->set_rawat_rutin_periode($periode,$company);
        echo $set_rawat_rutin_periode;
    }
    function get_rawat_rutin_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $rawat_rutin_periode = $this->model_m_rawat_rutin->get_rawat_rutin_periode($company);
        
        return $rawat_rutin_periode; 
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
				$data_lokasi = $this->model_m_rawat_rutin->lokasi_validate(substr($field['BLOK_TANAH'],0,2),$field['BLOK_TANAH'],$company); 					
				if(empty($data_lokasi)){						
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  lokasi ". $field['BLOK_TANAH']. " tidak ditemukan pada data master blok <br/>";
					$boolean_error = true;
				}
				
				$data_rawat_rutin = $this->model_m_rawat_rutin->get_rawat_rutin_blok($company, $field['BLOK_TANAH'], $today); 
				if($data_rawat_rutin>0){	
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  rawat rutin pada lokasi ". $field['BLOK_TANAH']. " Periode " .$bulan."-".$tahun." sudah pernah diinput <br/>";
					$boolean_error = true;
				}
				
				if ($boolean_error == false){
					$items = array(
							'BULAN' => $bulan,
							'TAHUN' => $tahun,
							'AFD' => substr($field['BLOK_TANAH'],0,2),
							'BLOK_TANAH' => $field['BLOK_TANAH'],
							'CIRCLE' => $field['CIRCLE'],
							'GAWANGAN' => $field['GAWANGAN'],
							'PATH' => $field['PATH'],
							'TPH' => $field['TPH'],
							'PRUNING' => $field['PRUNING'],
							'LALANG' => $field['LALANG'],
							'INITIAL_BLOK' => $field['INITIAL_BLOK'],
							'INPUT_BY' => trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),								
							'COMPANY_CODE' => $company
							);
					$query = $this->db->insert('s_rawat_rutin',$items);
					$ret += (int)$this->db->affected_rows($query);
				}
					
					$baris++;
			} 
			$msg = $ret . " data monitoring rawat rutin blok BERHASIL diupload, dan ". $reterror. " data rawat rutin blok GAGAL diupload";	
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
        
        echo json_encode($this->model_m_rawat_rutin->LoadData($bulan,$tahun,$company,$afd,$block));       
    }
    
    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_m_rawat_rutin->get_afdeling($company,$q);
         
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
        $data_afd = $this->model_m_rawat_rutin->get_block($company,$location_left,$q);
         
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
        $data_post['CIRCLE']  =strtoupper(trim(htmlentities($data_id['CIRCLE'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GAWANGAN']  =strtoupper(trim(htmlentities($data_id['GAWANGAN'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['PATH']  =strtoupper(trim(htmlentities($data_id['PATH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TPH']  =strtoupper(trim(htmlentities($data_id['TPH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['PRUNING']  =strtoupper(trim(htmlentities($data_id['PRUNING'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['LALANG']  =strtoupper(trim(htmlentities($data_id['LALANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['INITIAL_BLOK']  =strtoupper(trim(htmlentities($data_id['INITIAL_BLOK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;

        $validate_numeric=$this->validate_numeric($data_post['CIRCLE']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai CIRCLE harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['GAWANGAN']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai GAWANGAN harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['PATH']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai PATH harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TPH']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai TPH harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['PRUNING']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai PRUNING harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['LALANG']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai LALANG harus angka";
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['INITIAL_BLOK']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai INITIAL_BLOK harus angka";
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
        
        $data_lokasi = $this->model_m_rawat_rutin->lokasi_validate($data_post['AFD'],$data_post['BLOK_TANAH'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOK_TANAH']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_rawat_rutin->add_new($company,$data_post);
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
        $data_post['CIRCLE']  =strtoupper(trim(htmlentities($data_id['CIRCLE'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['GAWANGAN']  =strtoupper(trim(htmlentities($data_id['GAWANGAN'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['PATH']  =strtoupper(trim(htmlentities($data_id['PATH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['TPH']  =strtoupper(trim(htmlentities($data_id['TPH'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['PRUNING']  =strtoupper(trim(htmlentities($data_id['PRUNING'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['LALANG']  =strtoupper(trim(htmlentities($data_id['LALANG'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['INITIAL_BLOK']  =strtoupper(trim(htmlentities($data_id['INITIAL_BLOK'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['CIRCLE']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai CIRCLE harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['GAWANGAN']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai GAWANGAN harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['PATH']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai PATH harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['TPH']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai TPH harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['PRUNING']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai PRUNING harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['LALANG']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai LALANG harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
		
		$validate_numeric=$this->validate_numeric($data_post['INITIAL_BLOK']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai INITIAL_BLOK harus angka";
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
        
        $data_lokasi = $this->model_m_rawat_rutin->lokasi_validate($data_post['AFD'],$data_post['BLOK_TANAH'],$company);  
		
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOK_TANAH']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_rawat_rutin->update_rawat_rutin($data_post, $company);
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
		
        $delete_rawat_rutin = $this->model_m_rawat_rutin->delete_rawat_rutin($afd,$block,$bulan,$tahun,$company);
        $return['status']=  $delete_rawat_rutin;
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
			}elseif (preg_match('/ID_RAWAT_RUTIN/',$key)){
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('ID_RAWAT_RUTIN'=>$val));             
			}elseif (preg_match('/AFD/',$key)){
                $tmp_afd=trim($val);
                $data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('AFD'=>$val));			
			}elseif (preg_match('/BLOK_TANAH/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('BLOK_TANAH'=>$val));   
			}elseif (preg_match('/CIRCLE/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('CIRCLE'=>$val)); 
			}elseif (preg_match('/GAWANGAN/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('GAWANGAN'=>$val)); 
			}elseif (preg_match('/PATH/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('PATH'=>$val)); 
			}elseif (preg_match('/TPH/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TPH'=>$val)); 
			}elseif (preg_match('/PRUNING/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('PRUNING'=>$val)); 
			}elseif (preg_match('/LALANG/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('LALANG'=>$val)); 
			}elseif (preg_match('/INITIAL_BLOK/',$key)){
	        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('INITIAL_BLOK'=>$val)); 
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
            $insert_id = $this->model_m_rawat_rutin->delete_all_data($data_post);
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
        echo json_encode($this->model_m_rawat_rutin->data_search($data['rules'], $company));
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
		$headers .= "MONITORING RAWAT RUTIN \n";
		$headers .= "PERIODE: ". $periode ."\n";
		$headers .= "\n";
		$headers .= "NO. \t";
		$headers .= "BLOK TANAH \t";
		$headers .= "PLANTED (HA) \t";
		$headers .= "POKOK \t";	
		$headers .= "SPH \t";	
		$headers .= "CIRCLE \t";
		$headers .= "GAWANGAN \t";
		$headers .= "PATH \t";
		$headers .= "TPH/KASTRASI \t";		
		$headers .= "PRUNING/SANITASI \t";
		$headers .= "LALANG \t";
		$headers .= "INITIAL BLOK \t";
		
		$no = 1;
		$data=$this->model_m_rawat_rutin->get_xls($company_code, $bulan, $tahun);
		if($data!=NULL){
			foreach ($data as $row){
				$line = '';
				$line .= str_replace('"', '""',$no)."\t"; 
				$line .= str_replace('"', '""',$row['BLOCKID'])."\t";
				$line .= str_replace('"', '""',$row['HECTPLANTED'])."\t";
				$line .= str_replace('"', '""',$row['NUMPLANTATION'])."\t";
				$line .= str_replace('"', '""',$row['SPH'])."\t";
				$line .= str_replace('"', '""',$row['CIRCLE'])."\t";
				$line .= str_replace('"', '""',$row['GAWANGAN'])."\t";
				$line .= str_replace('"', '""',$row['PATH'])."\t";
				$line .= str_replace('"', '""',$row['TPH'])."\t";
				$line .= str_replace('"', '""',$row['PRUNING'])."\t";
				$line .= str_replace('"', '""',$row['LALANG'])."\t";
				$line .= str_replace('"', '""',$row['INITIAL_BLOK'])."\t";
				$no++;
				$data .= trim($line)."\n"; 
			
			}
			$data = str_replace("\r","",$data);
			$data = str_replace("Array","",$data);
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload;");
        header("Content-Disposition: attachment; filename=HPT_rawat_rutin".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
		}
		
	}
}
?>
