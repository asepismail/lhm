<?php
class m_curah_hujan extends Controller
{
    private $data;
    function __construct(){
        parent::__construct();
        $this->load->model('model_m_curah_hujan');
        $this->load->model('model_c_user_auth');  
        
        $this->load->library('form_validation');
        
        $this->lastmenu="m_curah_hujan";
		$this->load->library('csvReader');
        $this->data = array();    
    }
    
    function index(){
        $view="info_m_curah_hujan";
        
        //$data = array();
        $this->data['judul_header'] = "Data Curah Hujan yang Ditetapkan";
        $this->data['js'] = "";
    
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $this->data['periode_curah_hujan'] = $this->global_func->drop_date2('bulan_curah_hujan','tahun_curah_hujan','select');
        
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
        
        echo json_encode($this->model_m_curah_hujan->LoadData($bulan,$tahun,$company));   
    }
    /*########################## PENETAPAN PERIODE BJR ###############################
    ##################################################################################*/
    function set_curah_hujan_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $periode = htmlentities($this->uri->segment('3'));
        
        $set_curah_hujan_periode = $this->model_m_curah_hujan->set_curah_hujan_periode($periode,$company);
        echo $set_curah_hujan_periode;
    }
    function get_curah_hujan_periode(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $curah_hujan_periode = $this->model_m_curah_hujan->get_curah_hujan_periode($company);
        
        return $curah_hujan_periode; 
    }
	
	function do_import(){
        $error = "";
		$msg = "";
		$fileElementName = 'myfile';
		$baris = 1;
		$ret = 0; 
		$reterror = 0;
		$boolean_error = false;
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
				$data_lokasi = $this->model_m_curah_hujan->lokasi_validate($field['AFD'],$field['COMPANY_CODE']); 					
				if(empty($data_lokasi)){						
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  kode AFD ". $field['AFD']. " salah <br/>";
					$boolean_error = true;
				}
				
				$data_curah_hujan = $this->model_m_curah_hujan->get_curah_hujan($field['COMPANY_CODE'], $field['AFD'], $field['TANGGAL']); 
				if($data_curah_hujan>0){	
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  curah hujan pada AFD ". $field['AFD']. " Tanggal " .$field['TANGGAL']." sudah pernah diinput <br/>";
					$boolean_error = true;
				}
				
				if(!(trim($field['COMPANY_CODE'])==trim($company))){						
					$reterror = $reterror +1;
					$error .= "Baris " . $baris . "  PT ". $field['COMPANY_CODE']. " tidak sama dengan PT LOGIN <br/>";
					$boolean_error = true;
				}
				
				if ($boolean_error == false){
					$items = array(
							'AFD' => $field['AFD'],
							'TANGGAL' => $field['TANGGAL'],
							'CURAH_HUJAN' => $field['CURAH_HUJAN'],
							'INPUT_BY' => trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),								
							'COMPANY_CODE' => $field['COMPANY_CODE']
							);
					$query = $this->db->insert('s_data_curah_hujan',$items);
					$ret += (int)$this->db->affected_rows($query);
				}
					
					$baris++;
			} 
			$msg = $ret . " data curah hujan BERHASIL diupload, dan ". $reterror. " data curah hujan blok GAGAL diupload";	
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
        
        echo json_encode($this->model_m_curah_hujan->LoadData($bulan,$tahun,$company,$afd,$block));       
    }
    
    function get_afdeling(){
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $data_afd = $this->model_m_curah_hujan->get_afdeling($company,$q);
         
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
        $data_afd = $this->model_m_curah_hujan->get_block($company,$location_left,$q);
         
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
        }elseif(strtoupper(trim(htmlentities($data_id['CRUD'],ENT_QUOTES,'UTF-8'))) == "DEL_ALL"){
            $is_auth_user_command = $this->global_func->cek_user_authentification(htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8'),"DELETE",$loginid);
            if($is_auth_user_command['0']['ROLE_DELETE']=='1'){
                $this->delete_all($data_id);    
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
        $data_post['AFD']=strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGAL'] =strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8')));
        $data_post['CURAH_HUJAN']  =strtoupper(trim(htmlentities($data_id['CURAH_HUJAN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;

        $validate_numeric=$this->validate_numeric($data_post['CURAH_HUJAN']);
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai curah hujan harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $status = "Harap isi AFD"; 
            $return['status']=$status;
            $return['error']=true;  
        }elseif(strlen($data_id['AFD']) > 3){
            $status  ="Panjang karakter Afd melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_post['TANGGAL']) || trim($data_post['TANGGAL'])==''){
            $status = "Harap isi BLOCK";
            $return['status']=$status;
            $return['error']=true;   
        }
        
        $data_lokasi = $this->model_m_curah_hujan->lokasi_validate($data_post['AFD'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode AFD : '".$data_post['AFD']."' salah! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }
        
        if(empty($status)){     
            $return = $this->model_m_curah_hujan->add_new($company,$data_post);
            echo json_encode($return);                      
        }else{
            echo json_encode($return);
        }
    }
    
	function delete_all($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_post = array();
		$data = json_decode($this->input->post('myJson'), true);
		$data_detail = $data["detail"]; 
        $data_id = $data["id"];
		
		$int=0;
		$tmp_identifier='';	
		$bulan = '';

		foreach($data_detail as $key => $val){
			$int = filter_var($key, FILTER_SANITIZE_NUMBER_INT); 
            $tmp_identifier=$int; 
			

			if (preg_match('/ID_CURAH_HUJAN/',$key)){
				$data_post[$tmp_identifier]=array('ID_CURAH_HUJAN'=>$val);
			}elseif (preg_match('/AFD/',$key)){				
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('AFD'=>$val)); 
			}elseif (preg_match('/TANGGAL/',$key)){				
            	$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('TANGGAL'=>$val)); 
			}
			
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('ACTIVE'=>0));
			$data_post[$tmp_identifier]=array_merge((array)$data_post[$tmp_identifier],array('UPDATE_TIME'=>$this->global_func->gen_datetime()));
			
		} //for   
		
		if ($data_post == null){
			$return['status']="Data kosong, tidak dapat di hapus \r\n";
			$return['error']=true;		
		}
		
        if(empty($return['status']) && $return['error']==false){    
            $return = $this->model_m_curah_hujan->delete_all($data_post);
            echo json_encode($return);                      
        }else{
            echo json_encode($return);
        }   
		
    }
	
    function update_data($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['AFD']=strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TANGGAL'] =strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8')));
        $data_post['CURAH_HUJAN']  =strtoupper(trim(htmlentities($data_id['CURAH_HUJAN'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['COMPANY_CODE'] = $company;
        
        $validate_numeric=$this->validate_numeric($data_post['CURAH_HUJAN']);
        if( strtolower($validate_numeric)=='false'){
            $status ="Nilai Curah Hujan harus angka";
            $return['status']=$status;
            $return['error']=true;        
        }
        
        if (empty($data_post['AFD']) || trim($data_post['AFD'])==''){
            $status = "Harap isi AFD";
            $return['status']=$status;
            $return['error']=true;   
        }elseif(strlen($data_id['AFD']) > 3){
            $status  ="Panjang karakter Afd melebihi batas maksimal";
            $return['status']=$status;
            $return['error']=true;
        }
        
        if (empty($data_post['TANGGAL']) || trim($data_post['TANGGAL'])==''){
            $status = "Harap isi TANGGAL";
            $return['status']=$status;
            $return['error']=true;   
        }

        $data_lokasi = $this->model_m_curah_hujan->lokasi_validate($data_post['AFD'],$company);   
        if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
            $status = "kode AFD : '".$data_post['AFD']."' salah! \r\n"; 
            $return['status']=$status;
            $return['error']=true;
        }

        if(empty($status)){     
            $return = $this->model_m_curah_hujan->update_curah_hujan($data_post, $company);
            echo json_encode($return);          
        }else{
            echo json_encode($return);
        }   
    }
    
    function delete_data($data_id){
		$return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $afd = strtoupper(trim(htmlentities($data_id['AFD'],ENT_QUOTES,'UTF-8'))) ;
        $tanggal = strtoupper(trim(htmlentities($data_id['TANGGAL'],ENT_QUOTES,'UTF-8'))) ;

        $return = $this->model_m_curah_hujan->delete_curah_hujan($afd,$tanggal,$company);
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
        echo json_encode($this->model_m_curah_hujan->data_search($data['rules'], $company));
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
