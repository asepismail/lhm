<?php
class m_bjr extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_bjr');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_bjr";
		$this->load->library('csvReader');
        $this->data = array();    
    }
    
    function index(){
        $view="info_m_bjr";
        
        //$data = array();
        $this->data['judul_header'] = "Data BJR yang Ditetapkan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['periode_bjr'] = $this->global_func->drop_date2('bulan_bjr','tahun_bjr','select');
        
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
        
        echo json_encode($this->model_m_bjr->LoadData($bulan,$tahun,$company));   
    }
    /*########################## PENETAPAN PERIODE BJR ###############################
    ##################################################################################*/
    function set_bjr_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'));
        
        $set_bjr_periode = $this->model_m_bjr->set_bjr_periode($periode,$company);
        echo $set_bjr_periode;
    }
    function get_bjr_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $bjr_periode = $this->model_m_bjr->get_bjr_periode($company);
        
        return $bjr_periode; 
    }
	
	function do_import(){
        $error = "";
		$msg = "";
		$fileElementName = 'myfile';
		$baris = 1;
		$ret = 0; 
		$reterror = 0;
		$boolean_error = false;
	
		//$today = date('Ymd');
		//$bulan = date("m",strtotime($today));
        //$tahun = date("Y",strtotime($today));
		$bulan = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tahun = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'); 
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
				$data_lokasi = $this->model_m_bjr->lokasi_validate(substr($field['BLOCK'],0,2),$field['BLOCK'],$company); 					
				if(empty($data_lokasi)){						
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  lokasi ". $field['BLOCK']. " tidak ditemukan pada data master blok <br/>";
					$boolean_error = true;
				}
				
				$data_bjr = $this->model_m_bjr->get_bjr_blok($company, $field['BLOCK'], $today); 
				if($data_bjr>0){	
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  BJR pada lokasi ". $field['BLOCK']. " Periode " .$bulan."-".$tahun." sudah pernah diinput <br/>";
					$boolean_error = true;
				}
				
				if ($boolean_error == false){
					$items = array(
							'BULAN' => $bulan,
							'TAHUN' => $tahun,
							'AFD' => substr($field['BLOCK'],0,2),
							'BLOCK' => $field['BLOCK'],
							'VALUE' => $field['VALUE'],
							'INPUT_BY' => trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),								
							'COMPANY_CODE' => $company
							);
					$query = $this->db->insert('s_data_bjr',$items);
					$ret += (int)$this->db->affected_rows($query);
				}
					
					$baris++;
			} 
			$msg = $ret . " data BJR blok BERHASIL diupload, dan ". $reterror. " data BJR blok GAGAL diupload";	
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
        
        echo json_encode($this->model_m_bjr->LoadData($bulan,$tahun,$company,$afd,$block));       
    }
    
    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_m_bjr->get_afdeling($company,$q);
         
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
        $data_afd = $this->model_m_bjr->get_block($company,$location_left,$q);
         
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
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "APP"){
			//var_dump($data_id);
			$is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_APPROVE']=='1'){
                $this->approve_data($data_id);    
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
        $data_post['BLOCK'] =strtoupper(trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8')));
        $data_post['VALUE']  =strtoupper(trim(htmlentities($data_id['VALUE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;

        $validate_numeric=$this->validate_numeric($data_post['VALUE']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai BJR harus angka";
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
        
        if (empty($data_post['BLOCK']) || trim($data_post['BLOCK'])==''){
            $status = "Harap isi BLOCK";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['BLOCK']) > 25){
            $status  ="Panjang karakter Block melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_m_bjr->lokasi_validate($data_post['AFD'],$data_post['BLOCK'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOCK']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_bjr->add_new($company,$data_post);
            $return['status']=  $insert_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
	function approve_data($data_id){
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
			}elseif (preg_match('/ID_BJR/',$key)){
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('ID_BJR'=>$val));               
			}elseif (preg_match('/AFD/',$key)){
            	if(strlen(trim($val))>2){
                	$status = "input AFD pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n"; 
                    $return['status']=$status;
                    $return['error']=true;    
               	}elseif(strlen(trim($val))<=0 || empty($val)){
                	$status="AFD pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['status']=$status;
                    $return['error']=true;   
                }else{
                    $tmp_afd=trim($val);
                    $data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('AFD'=>$val));
               	}				
			}elseif (preg_match('/BLOCK/',$key)){
            	if(strlen(trim($val))>7){
                	$status = "input BLOCK pada baris ".$int." melebihi batas karakter yang di tetapkan  \r\n"; 
                    $return['status']=$status;
                    $return['error']=true;    
           		}elseif(strlen(trim($val))<=0 || empty($val)){
               		$status="BLOCK pada baris ".$int." tidak boleh kosong   \r\n";
                    $return['status']=$status;
                    $return['error']=true;   
               	}else{ // no error, then validate location
                	$lc = trim($val);  
                    $afd = $tmp_afd;
                    $data_lokasi = $this->model_m_bjr->lokasi_validate($afd,$lc,$company);  
                    if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
	                    $status = "kode lokasi : ".$lc.", pada baris ".$int." SALAH!! \r\n"; 
                        $return['status']=$status;
                        $return['error']=true;
                    }else{
       		        	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('BLOCK'=>$val));
                    }   						
              	}    
			}elseif (preg_match('/VALUE/',$key)){
				$validate_numeric=$this->validate_numeric($val);
            	if(strtolower($validate_numeric)=='false'){
                	$status ="Nilai BJR harus angka";
					$return['status']=$status;
					$return['error']=true;     
              	}elseif(strlen(trim($val))<=0 || intval($val)==0){
                	$return['status']="input JANJANG pada baris ".$int." tidak boleh nol atau kosong  \r\n";
                    $return['error']=true;   
               	}else{
                    $data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('VALUE'=>$val));
                }
			}
			
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('APPROVED_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('APPROVED'=>1));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('APPROVE_DATE'=>$this->global_func->gen_datetime()));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('COMPANY_CODE'=>$company));
		} //for   
		
		//var_dump($data_post);
		if ($data_post == null){
			$return['status']="Data kosong, tidak dapat di approve \r\n";
			$return['error']=true;		
		}
		
        if(empty($return['status']) && $return['error']==false){    
            $insert_id = $this->model_m_bjr->approve_bjr($data_post);
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
        $data_post['BLOCK'] =strtoupper(trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8')));
        $data_post['VALUE']  =strtoupper(trim(htmlentities($data_id['VALUE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['VALUE']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai BJR harus angka";
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
        
        if (empty($data_post['BLOCK']) || trim($data_post['BLOCK'])==''){
            $status = "Harap isi BLOCK";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['BLOCK']) > 25){
            $status  ="Panjang karakter Block melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        $data_lokasi = $this->model_m_bjr->lokasi_validate($data_post['AFD'],$data_post['BLOCK'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode lokasi : '".$data_post['BLOCK']."' SALAH!! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $insert_id = $this->model_m_bjr->update_bjr($data_post, $company);
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
        $block = strtoupper(trim(htmlentities($data_id['BLOCK'],ENT_QUOTES,'UTF-8'))) ;
        $bulan = strtoupper(trim(htmlentities($data_id['BULAN'],ENT_QUOTES,'UTF-8'))) ;
        $tahun = strtoupper(trim(htmlentities($data_id['TAHUN'],ENT_QUOTES,'UTF-8'))) ;
		
        $delete_bjr = $this->model_m_bjr->delete_bjr($afd,$block,$bulan,$tahun,$company);
        $return['status']=  $delete_bjr;
        $return['error']=false;
        echo json_encode($return);
        
   
    }
    
    function search_data_2(){
        $kode_storage = trim(htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8'));
        $periode = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8') ;
        $ar = preg_split('/[- :]/',trim($periode));
        $ar = implode('',$ar);
        $ar=(empty($ar) || $ar===false)?'-':$ar;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_m_bjr->data_search($data['rules'], $company));
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
