<?php
class s_catat_sounding_kernel extends Controller{
    private $lastmenu;
    private $data;
    function __construct(){
        parent::__construct();
        
        $this->load->model('model_s_catat_sounding_kernel');        
        $this->load->model('model_c_user_auth');  
        
        $this->lastmenu="s_catat_sounding_kernel";
        $this->data = array(); 
    }
    
    function index(){
        $view="info_s_catat_sounding_kernel";
        
        $this->data['judul_header'] = "Product Storage Reading (Sounding) - KERNEL";
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
        
        echo json_encode($this->model_s_catat_sounding_kernel->LoadData($company));   
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
	
	function calc_weight($height_tabung, $height_kerucut,$id_storage,$company){
		$weight = 0;
		$weight_tabung =0;
		$weight_selisih=0;
		$query = "SELECT s.ID_STORAGE, s.TAN_ANGLE, s.VOLUME_KERUCUT, s.VOLUME_TABUNG, s.CONE  
				FROM m_storage s
				WHERE s.COMPANY_CODE = '".$company."' 
				AND s.ACTIVE=1
				AND s.ID_STORAGE = '".$id_storage."'";
				//var_dump($query);
		$sQuery = $this->db->query($query);
		if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
               
			if ($height_kerucut>0){
				$query="";
				$height_kerucut = $height_kerucut*100;				
				$height_kerucut = (string)$height_kerucut;
				if ($row->TAN_ANGLE==0){
					$sudut_kerucut = $row->CONE;
					if ($sudut_kerucut!=0){ 
						$weight = (($height_kerucut/3)*($sudut_kerucut));
						$weight = ($weight/1000000*600);
					}else{
						$weight = 0;
					}
					
				}else{
					$sudut_kerucut = $row->TAN_ANGLE;
					
					if ($sudut_kerucut!=0){ 
						$r = ($height_kerucut/$sudut_kerucut);
						//var_dump("sudut :".$sudut_kerucut);
					}else{
						$r = 0;	
					}
					
					$weight = (3.14*($r*$r)*$height_kerucut/3/1000*0.62*0.9);
					//var_dump("r : ".$r);
					//var_dump("height_kerucut : ".$height_kerucut);
					//var_dump("weight : ".$weight);
				}
				//$sudut_kerucut = 0.838496694171491;				
			}
			
			if ($height_tabung>0){
				$height_tabung = ($height_tabung*100);
				$floor_height_tabung = floor($height_tabung);
				$selisih_round_height = ($height_tabung - $floor_height_tabung);				
				//$height_tabung = (string)$height_tabung;
				$weight_tabung = $row->VOLUME_TABUNG;
				$selisih = $row->VOLUME_KERUCUT;
				//$weight_tabung =  40394.15;
				//$selisih =   331.5;
				for ($i=1;$i<=$floor_height_tabung;$i++){	
					$weight = ($weight_tabung + $selisih);					
					$weight_tabung = $weight;
				}
										
				if ($selisih_round_height!=0){
					$round_height_tabung = round($height_tabung);
					$weight_round_tabung = $row->VOLUME_TABUNG;
					$selisih_round = $row->VOLUME_KERUCUT;
					for ($i=1;$i<=($floor_height_tabung+1);$i++){	
						$weight_round = ($weight_round_tabung + $selisih_round);
						$weight_round_tabung = $weight_round;
					}
					$weight_selisih_round=$weight_round_tabung-$weight_tabung;
					$weight_selisih = $weight_selisih_round * $selisih_round_height;									
				}
				$weight = $weight_tabung + $weight_selisih;							
			}
        }
		var_dump($weight);
		$weight = round($weight,-1);
		var_dump($weight);
		$weight = $this->rounds($weight);
		var_dump($weight);
		return $weight;
	}
	/*
	function calc_weight($height_tabung, $height_kerucut,$id_storage,$company){
		$weight = 0;
		$query = "SELECT s.ID_STORAGE, s.TAN_ANGLE, s.VOLUME_KERUCUT, s.VOLUME_TABUNG, s.CONE  
				FROM m_storage s
				WHERE s.COMPANY_CODE = '".$company."' 
				AND s.ACTIVE=1
				AND s.ID_STORAGE = '".$id_storage."'";
		$sQuery = $this->db->query($query);
		if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
               
			if ($height_kerucut>0){
				$query="";
				$height_kerucut = $height_kerucut*100;
				$height_kerucut = (string)$height_kerucut;
				if ($row->TAN_ANGLE==0){
					$sudut_kerucut = $row->CONE;
					if ($sudut_kerucut!=0){ 
						$weight = (($height_kerucut/3)*($sudut_kerucut));
						$weight = ($weight/1000000*600);
					}else{
						$weight = 0;
					}
					
				}else{
					$sudut_kerucut = $row->TAN_ANGLE;
					if ($sudut_kerucut!=0){ 
						$r = ($height_kerucut/$sudut_kerucut);
					}else{
						$r = 0;	
					}
					$weight = (3.14*($r*$r)*$height_kerucut/3/1000*0.62*0.9);
				}
				//$sudut_kerucut = 0.838496694171491;				
			}
			
			if ($height_tabung>0){
				$height_tabung = ($height_tabung*100);	
				$height_tabung = (string)$height_tabung;
				$weight_tabung = $row->VOLUME_TABUNG;
				$selisih = $row->VOLUME_KERUCUT;
				//$weight_tabung =  40394.15;
				//$selisih =   331.5;
				for ($i=1;$i<=$height_tabung;$i++){				
					$weight = ($weight_tabung + $selisih);
					$weight_tabung = $weight;
				}
			}
        }
		 $weight = floor($weight);
		$weight = $this->rounds($weight);		
		return $weight;
	}
	*/
    function rounds($round){
		$right = substr($round, -1);
		$x=0;
		$result=0;
		if ($right>=5){
			$x=10-$right;
			$result = $round + $x;
		}else{
			$result = $round - $right;
		}
		return $result;		
	}
    function add_new($data_id){
        $return['status']='';
        $return['error']=false;
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $data_post['ID_SOUNDING_KERNEL'] = $this->global_func->createMy_ID('s_sounding_kernel','ID_SOUNDING_KERNEL',$company."SNDK","DATE",$company);
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = strtoupper(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ; 
        $data_post['HEIGHT']=strtoupper(trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['HEIGHT2']=strtoupper(trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['EXTRA_WEIGHT']=strtoupper(trim(htmlentities($data_id['EXTRA_WEIGHT'],ENT_QUOTES,'UTF-8'))) ;
		
        $data_post['COMPANY_CODE'] = $company;
        $data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        
        $validate_numeric=$this->validate_numeric(array($data_id['HEIGHT'],$data_id['HEIGHT2'],$data_id['WEIGHT'],$data_id['EXTRA_WEIGHT']));
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai Tinggi dan Berat harus angka";
            $return['error']=true;        
        }
        
        if (empty($data_post['ID_SOUNDING_KERNEL']) || trim($data_post['ID_SOUNDING_KERNEL'])==''){
            $return['status']="Harap isi ID_SOUNDING";
            $return['error']=true;   
        }elseif(strlen($data_post['ID_SOUNDING_KERNEL']) > 50){
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
        
        /*if (empty($data_post['HEIGHT']) || trim($data_post['HEIGHT'])==''){
            $return['status']="Harap isi HEIGHT";
            $return['error']=true;  
        }elseif(strlen($data_id['HEIGHT']) > 50){
            $return['status']="Panjang karakter HEIGHT melebihi batas maksimal";
            $return['error']=true;
        }*/
		
		if (empty($data_post['HEIGHT']) && empty($data_post['HEIGHT2'])){
            $return['status']="Harap isi Tinggi";
            $return['error']=true;  
        }elseif(!empty($data_post['HEIGHT']) && !empty($data_post['HEIGHT2'])){
            $return['status']="Harap isi salah satu: Tinggi Tabung atau Tinggi Kerucut";
            $return['error']=true;
        }
        
        $TGL_AKTIVITAS=strval($data_post['DATE']);
        if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
            $return['status']="Tanggal Aktifitas tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
                $return['status']="format datetime tidak benar";
                $return['error']=true;
            }
        }
        
        		
        if(empty($return['status']) && $return['error']==false){ 
			$weight=$this->calc_weight($data_id['HEIGHT'], $data_id['HEIGHT2'],$data_id['ID_STORAGE'],$company);
        	$data_post['WEIGHT']=$weight;
		
            $insert_id = $this->model_s_catat_sounding_kernel->add_new($company,$data_post);
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
        
        $data_post['ID_STORAGE'] = strtoupper(trim(htmlentities($data_id['ID_STORAGE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['DATE'] = strtoupper(trim(htmlentities($data_id['DATE'],ENT_QUOTES,'UTF-8'))) ;
        $data_post['TIME'] = strftime("%H:%M:%S",strtotime(trim(htmlentities($data_id['TIME'],ENT_QUOTES,'UTF-8')))) ; 
        $data_post['HEIGHT']=strtoupper(trim(htmlentities($data_id['HEIGHT'],ENT_QUOTES,'UTF-8'))) ;
		$data_post['HEIGHT2']=strtoupper(trim(htmlentities($data_id['HEIGHT2'],ENT_QUOTES,'UTF-8')));
		$data_post['EXTRA_WEIGHT']=strtoupper(trim(htmlentities($data_id['EXTRA_WEIGHT'],ENT_QUOTES,'UTF-8')));
        //$data_post['WEIGHT']=strtoupper(trim(htmlentities($data_id['WEIGHT'],ENT_QUOTES,'UTF-8'))) ;
        //$data_post['TYPE_S'] =2;
        $data_post['COMPANY_CODE'] = $company;
        $data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'); 
        $data_post['UPDATE_TIME'] =  $this->global_func->gen_datetime(); 
        
        $validate_numeric=$this->validate_numeric(array($data_id['HEIGHT'],$data_id['HEIGHT2'],$data_id['WEIGHT'],$data_id['EXTRA_WEIGHT']));
        if( strtolower($validate_numeric)=='false'){
            $return['status']="Nilai Tinggi dan Berat harus angka";
            $return['error']=true;       
        }
        
        $id_sounding=strtoupper(trim(htmlentities($data_id['ID_SOUNDING_KERNEL'],ENT_QUOTES,'UTF-8'))) ;
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
        
        /*if (empty($data_post['HEIGHT']) || trim($data_post['HEIGHT'])==''){
            $return['status']="Harap isi HEIGHT";
            $return['error']=true;   
        }elseif(strlen($data_id['HEIGHT']) > 50){
            $return['status']="Panjang karakter HEIGHT melebihi batas maksimal";
            $return['error']=true;
        }*/
		
		if (empty($data_post['HEIGHT']) && empty($data_post['HEIGHT2'])){
			if (empty($data_post['EXTRA_WEIGHT'])){
				$return['status']="Harap isi Tinggi";
				$return['error']=true;  
			}
        }elseif(!empty($data_post['HEIGHT']) && !empty($data_post['HEIGHT2'])){
            $return['status']="Harap isi salah satu: Tinggi Tabung atau Tinggi Kerucut";
            $return['error']=true;
        }
        
        $TGL_AKTIVITAS=strval($data_post['DATE']);
        if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
            $return['status']="Tanggal Aktifitas tidak boleh kosong";
            $return['error']=true;
        }else{ 
            if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
                $return['status']="format datetime tidak benar";
                $return['error']=true;
            }
        }
        
        if(empty($return['status']) && $return['error']==false){  
			$weight=$this->calc_weight($data_id['HEIGHT'], $data_id['HEIGHT2'],$data_id['ID_STORAGE'],$company);
        	$data_post['WEIGHT']=$weight;
            $update_id = $this->model_s_catat_sounding_kernel->update_sounding($id_sounding,$data_post,$company);
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
        $id_sounding = strtoupper(trim(htmlentities($data_id['ID_SOUNDING_KERNEL'],ENT_QUOTES,'UTF-8'))) ;    
        if (empty($id_sounding) || trim($id_sounding)==='' || $id_sounding===false){
            $return['status']="ID_SOUNDING KOSONG !!";
            $return['error']=true;   
        }elseif(strlen($id_sounding) > 50){
            $return['status']="Panjang karakter ID melebihi batas maksimal";
            $return['error']=true;
        }
        
        if(empty($return['status']) && $return['error']===false){     
            $delete_id = $this->model_s_catat_sounding_kernel->delete_sounding($id_sounding,$company);
            $return['status']=  $delete_id;
            $return['error']=false;
            echo json_encode($return);
                      
        }else{
            echo json_encode($return);
        }
    }
    
    function get_storage(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_storage = $this->model_s_catat_sounding_kernel->get_storage($q,$company);

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
                
        $data = json_decode($this->input->post('filters'), true);
        echo json_encode($this->model_s_catat_sounding_kernel->data_search($data['rules'], $company));
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


