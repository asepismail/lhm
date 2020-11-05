<?php
class model_s_dispatch_sounding extends Model{
    public $snd_type;
    function __construct($snd_type){
        parent::__construct();
        $this->snd_type = $snd_type;
        
        $this->load->database();  
    }
    
    function LoadData($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company=trim($this->db->escape_str($company)); 
        $queries = "SELECT * FROM s_movement_sounding WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' ORDER BY DATE DESC";
    

        $sql2 = $queries;
       
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        //$sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
        $sql = $queries." LIMIT ".$start.",".$limit." "; 

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_SOUNDING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TEMPERATURE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->WEIGHT,2),ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_STORAGE2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HEIGHT2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TEMPERATURE2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->WEIGHT2,2),ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DOC_NO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SUPPLIER,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOV_TYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));                
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    
    function get_storage($q,$company){             
        $company=$this->db->escape_str($company);
        $prod_code=$this->db->escape_str($q);
        
        $query="SELECT ID_STORAGE, PRODUCT_CODE, DESCRIPTION FROM m_storage
                WHERE ID_STORAGE LIKE '%".$prod_code."%' AND COMPANY_CODE ='".$company."' AND PRODUCT_CODE = 'CPO'";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
	
	function get_doc($q,$company){             
        $company=$this->db->escape_str($company);
        $prod_code=$this->db->escape_str($q);
        
        $query="SELECT ID_DO, CUSTOMER_NAME 
FROM s_dispatch_do
WHERE company_code = '".$company."' AND ACTIVE = 1 AND JENIS = 'CPO' AND QTY_CONTRACT <> QTY_DELIVERED";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
    function get_supplier($q,$company){             
        $company=$this->db->escape_str($company);
        $prod_code=$this->db->escape_str($q);
        
        $query="SELECT company_code, company_name FROM m_company";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
        
    }
	
    function add_new($company, $data_post){
        $return['status']='';
        $return['error']=false;
        $company = trim($this->db->escape_str($company));
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_movement_sounding',array('ID_SOUNDING'=>$data_post['ID_SOUNDING']),'ID_SOUNDING');
	 //$cek_data_exist = $this->cek_data_exist('s_movement_sounding',array('ID_SOUNDING'=>$data_post['ID_SOUNDING'],'MOV_TYPE'=>$data_post['MOV_TYPE']),'ID_SOUNDING');
	//var_dump($data_post['ID_SOUNDING']);
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database.. !!'; 
			$return['status']=$status;
        	$return['error']=true;   
        }
        
		/*
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_sounding',array('ACTIVE'=>1,'ID_STORAGE'=>$data_post['ID_STORAGE'],'DATE'=>$data_post['DATE']),'ID_SOUNDING');
        if ($cek_data_exist > 0){
            $status="Sounding Storage '".$data_post['ID_STORAGE']."' , untuk periode ".$data_post['DATE'].
                            " telah dilakukan";
        }
        */
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status="ID STORAGE : ".$data_post['ID_STORAGE']." Tidak terdapat di database";
			$return['status']=$status;
        	$return['error']=true;
        }
        
		unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE2']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status="ID STORAGE 2 : ".$data_post['ID_STORAGE2']." Tidak terdapat di database";
			$return['status']=$status;
        	$return['error']=true;
        }
		/*
		if ($data_post['MOV_TYPE']=='D'){
			unset($cek_data_exist);
			$cek_data_exist = $this->cek_data_exist('s_dispatch_do',array('ID_DO'=>$data_post['DOC_NO']),'ID_DO');
			if ($cek_data_exist <= 0){
				$status="ID DO : ".$data_post['DOC_NO']." Tidak terdapat di database";
				$return['status']=$status;
				$return['error']=true;
			}	
		}    
		*/ 
        if(empty($return['status']) && $return['error'] == false){ 
            $this->db->insert( 's_movement_sounding', $data_post );
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";                
            }
        }
        return $return;
    }
    
    function update_sounding($id_sounding,$data_post,$company){
        $id_sounding = trim($this->db->escape_str($id_sounding));
        $company = trim($this->db->escape_str($company));
        $return['status']='';
        $return['error']=false;
        
        if(empty($id_sounding)){
            $status = "id_sounding CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_movement_sounding',array('ID_SOUNDING'=>$id_sounding),'ID_SOUNDING');
        if ($cek_data_exist <= 0){
            $status ="DATA SOUNDING NOT EXIST !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status ="DATA STORAGE NOT EXIST !!";
			$return['status']=$status;
        	$return['error']=true;
        }
		
		unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE2']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status="ID STORAGE 2 : ".$data_post['ID_STORAGE2']." Tidak terdapat di database";
			$return['status']=$status;
        	$return['error']=true;
        }
        
		if ($data_post['MOV_TYPE']=='D'){
			unset($cek_data_exist);
			$cek_data_exist = $this->cek_data_exist('s_dispatch_do',array('ID_DO'=>$data_post['DOC_NO']),'ID_DO');
			if ($cek_data_exist <= 0){
				$status="ID DO : ".$data_post['DOC_NO']." Tidak terdapat di database";
				$return['status']=$status;
				$return['error']=true;
			}	
		}
        if(empty($return['status']) && $return['error'] == false){  
            
            $this->db->where('ID_SOUNDING',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_movement_sounding',$data_post);
            if($this->db->trans_status() == false){
                $status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status'] = $status;//"Error in Transactions!!";
        		$return['error']=true;
            }else{                
				$return['status'] = "Update Data Berhasil ID ".$id_sounding;
        		$return['error']=false;  
            }
        }
        
        return $return;
    }
    
	function update_sounding2($id_sounding,$data_post,$company){
        $id_sounding = trim($this->db->escape_str($id_sounding));
        $company = trim($this->db->escape_str($company));
        $return['status']='';
        $return['error']=false;
        
        if(empty($id_sounding)){
            $status = "id_sounding CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_movement_sounding',array('DOC_NO'=>$id_sounding),'DOC_NO');
        if ($cek_data_exist <= 0){
            $status ="DATA SOUNDING NOT EXIST !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status ="DATA STORAGE NOT EXIST !!";
			$return['status']=$status;
        	$return['error']=true;
        }
		
		unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE2']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status="ID STORAGE 2 : ".$data_post['ID_STORAGE2']." Tidak terdapat di database";
			$return['status']=$status;
        	$return['error']=true;
        }
        
		if ($data_post['MOV_TYPE']=='D'){
			unset($cek_data_exist);
			$cek_data_exist = $this->cek_data_exist('s_dispatch_do',array('ID_DO'=>$data_post['DOC_NO']),'ID_DO');
			if ($cek_data_exist <= 0){
				$status="ID DO : ".$data_post['DOC_NO']." Tidak terdapat di database";
				$return['status']=$status;
				$return['error']=true;
			}	
		}
        if(empty($return['status']) && $return['error'] == false){  
            
            $this->db->where('DOC_NO',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_movement_sounding',$data_post);
            if($this->db->trans_status() == false){
                $status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status'] = $status;//"Error in Transactions!!";
        		$return['error']=true;
            }else{                
				$return['status'] = "Update Data Berhasil ID ".$id_sounding;
        		$return['error']=false;  
            }
        }
        
        return $return;
    }
	
    function delete_sounding($id_sounding,$company){
        $id_sounding = trim($this->db->escape_str($id_sounding));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_sounding)){
            $status = "ID_SOUNDING CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_sounding',array('ID_SOUNDING'=>$id_sounding),'ID_SOUNDING');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_SOUNDING',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_sounding');
            //$this->db->delete('s_sounding');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
    }
    
    function search_data($kode_storage,$periode, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $kode_storage = trim($this->db->escape_str($kode_storage));
        $periode = trim($this->db->escape_str($periode));
        $company = trim($this->db->escape_str($company));
        $sound_type = $this->snd_type;
        
        $where = "WHERE 1=1"; 
        if(!empty($kode_storage)){
            if($kode_storage!='-'){
                $where.= " AND ID_STORAGE LIKE '%$kode_storage%'";    
            }
        }  
        if(!empty($periode)){
            if($periode!='-'){
                $where.= " AND DATE_FORMAT(DATE,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d')";    
            }
        }       
        $where .= " AND ACTIVE =1 AND COMPANY_CODE = '".$company."' AND TYPE_S='".$sound_type."'";
        
        $queries = "SELECT * FROM s_sounding ". $where;
                    
        $sql2 = $queries;
        
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        
        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_SOUNDING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TEMPERATURE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->VOLUME),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->WEIGHT),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));                
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    
    function data_search($data_search, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
        $sound_type = $this->snd_type;
        
        $where = "WHERE ACTIVE=1 AND COMPANY_CODE = '".$company."'  AND TYPE_S='".$sound_type."' "; 
        $where_cnt = sizeof($data_search);
        $i=0;
        for($i==0; $i<=$where_cnt-1; $i++){
            switch(strtolower(trim($data_search[$i]['op']))){
                case "bw":
                    $operator = "LIKE";
                    break;
                case "eq":
                    $operator = "=";
                    break;
                case "ne":
                    $operator = "!=";
                    break;
                case "lt":
                    $operator = "<";
                    break;
                case "le":
                    $operator = "<=";
                    break;
                case "gt":
                    $operator = ">";
                    break;
                case "ge":
                    $operator = ">=";
                    break;
                case "ew":
                    $operator ="LIKE";
                    break;
                case "cn":
                    $operator ="LIKE";
                    break;
                default:
                    $operator ="LIKE";    
            }
            
            if(trim(strtoupper($operator))== "LIKE" && !empty($operator)){
                $where .=" AND ".trim($this->db->escape_str($data_search[$i]['field']))." $operator '%".trim($this->db->escape_like_str($data_search[$i]['data']))."%'";   
            }else{
               $where .=" AND ".trim($this->db->escape_str($data_search[$i]['field']))." $operator '".trim($this->db->escape_str($data_search[$i]['data']))."'"; 
            }           
        }       
        
        $queries = "SELECT * FROM s_sounding ". $where;
                    
        $sql2 = $queries;
        
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        
        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_SOUNDING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TEMPERATURE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->VOLUME),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->WEIGHT),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));                
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    } 
    
    function calc_volume($height,$id_sounding,$id_storage,$company){
        $height = trim($this->db->escape_str($height));
        $company = trim($this->db->escape_str($company));
        $id_storage = trim($this->db->escape_str($id_storage));
        $id_sounding = trim($this->db->escape_str($id_sounding));
        
        $volume=0;
        if($company == 'LIH' || $company == 'GKM'){
            $height=(float)substr($height,0,1).".".substr($height,strpos($height,".")+1,2);
            $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$height."' AND COMPANY_CODE='".$company."'"; 
            $sQuery = $this->db->query($query);

            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
               $volume=$row->VOLUME;
            }
            $this->db->where('ID_SOUNDING',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->set('VOLUME',$volume);
            $this->db->update('s_sounding');                    
        }elseif($company == 'MIA' || $company == 'MAG'){
            //$height=(intval((($height*1000)/10))+1)/100;
        }
        
    }
    
    function calc_weight($temp,$height,$id_storage,$company){ //TONASE
        $temp = trim($this->db->escape_str($temp));
        $height = trim($this->db->escape_str($height));
        $company = trim($this->db->escape_str($company));
        $id_storage = trim($this->db->escape_str($id_storage));
        
        $bj=0;
        $bj_koreksi=0;
        $volume=0;
        $volume_1=0; //Volume +1
        $zeroVol=0; //tinggi mejaukur
        
        $query="SELECT ZERO_CAPACITY FROM m_storage 
                WHERE COMPANY_CODE='".$company."' AND ID_STORAGE='".$id_storage."'"; //cari tinggi meja ukur
        $sQuery = $this->db->query($query);
        if ($sQuery->num_rows() > 0){
            $row = $sQuery->row();
            $zeroVol=$row->ZERO_CAPACITY;
        }
        $sQuery->free_result();
         
        $height_gross = (($height*1000)+$zeroVol)/1000;
        $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".
                (float)substr($height_gross,0,1).".".substr($height_gross,strpos($height_gross,".")+1,2).
                "' AND COMPANY_CODE='".$company."'";
        $sQuery = $this->db->query($query);

        if ($sQuery->num_rows() > 0){
           $row = $sQuery->row();
           $volume=$row->VOLUME;
        }
        $sQuery->free_result();
         
        $query="SELECT BJ,BJ_CORRECTION FROM storage_temperature_converter WHERE TEMPERATURE='".$temp.
                "' AND COMPANY_CODE='".$company."' AND ID_STORAGE='".$id_storage."'";
        $sQuery = $this->db->query($query);

        if ($sQuery->num_rows() > 0){
            $row = $sQuery->row();    
            $bj=$row->BJ;
            $bj_koreksi=$row->BJ_CORRECTION;
        } 
        $sQuery->free_result();
        
        if($company=='LIH'){
            $koef_a=(intval((($height_gross*1000)/10))+1)/100;	
            $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$koef_a."' AND COMPANY_CODE='".$company."'";
            $sQuery = $this->db->query($query);
	
            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
               $volume_1=$row->VOLUME;
            }
            $height_gross_m=$height_gross*1000;
            $koef_c=$height_gross_m-((($koef_a*100)-1)*10);
            $koef_b = abs($volume_1-$volume)*(($koef_c)/10);
            $volume_2=$volume+$koef_b;
            $volume_3=$volume_2*$bj_koreksi;
            $tonase=$volume_3*$bj; 			 
        }elseif($company=='GKM'|| $company=='SMI' || $company=='NRP'){
			$hi=$height*1000;
			if ($hi>24 && $hi<100){
				$koef_a = substr($hi, 0, 1);				
			}elseif ($hi>99 && $hi<1000){
				$koef_a = substr($hi, 0, 2);
				
			}elseif ($hi>999 && $hi<10000){
				$koef_a = substr($hi, 0, 3);
			}else{
				$koef_a = substr($hi, 0, 4);				
			}
						
			$koef_a=$koef_a/100;
            $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$koef_a."' AND COMPANY_CODE='".$company."'";
            $sQuery = $this->db->query($query);

            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
          	   $volume_1=$row->VOLUME;
            }
			$sQuery->free_result();
			
			$const_a = $hi - ($koef_a*1000);
			$const_b = (1 + ($koef_a*100))/100;				
			
			$query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$const_b."' AND COMPANY_CODE='".$company."'";
            $sQuery = $this->db->query($query);

            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
          	   $volume_2=$row->VOLUME;
            }
			
			$const_c = ($volume_2-$volume_1)*($const_a/10);
			$const_d = $volume_1 + $const_c;
			$const_e=$const_d*$bj_koreksi;
			$tonase=$const_e*$bj;			
		}elseif($company=='MIA' || $company=='MAG'){
            $halaman=0;
            $cincin=0;
            $total_koreksi=0;
            $koef_a=0;
            $koef_b=0;
			$height_nett2 = 0;
			$height_nett3 = 0;
			$volume_0=0;
            
            $koef_a=(intval((($height_gross*1000)/10)))/100;
            $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$koef_a."' AND COMPANY_CODE='".$company."'";
            $sQuery = $this->db->query($query);
			

            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
               $volume_1=$row->VOLUME;
            }
            $sQuery->free_result();
            
            $halaman = substr($height_gross,-1);
			//added By Asep
			$height_nett = $height_gross*1000;
			if ($height_nett>24 && $height_nett<100){
				$height_nett2 = substr($height_nett, 0, 1);	
			}else if ($height_nett>99 && $height_nett<1000){
				$height_nett2 = substr($height_nett, 0, 2);		
			}else if ($height_nett>999 && $height_nett<10000){
				$height_nett2 = substr($height_nett, 0, 3);		
			}else{
				$height_nett2 = substr($height_nett, 0, 4);		
			}
			$height_nett3 = $height_nett2+1;
			
			$query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".($height_nett3/100)."' AND COMPANY_CODE='".$company."'";
            $sQuery = $this->db->query($query);

            if ($sQuery->num_rows() > 0){
               $row = $sQuery->row();
               $volume_0=$row->VOLUME;
            }
            $sQuery->free_result();
			$koef_b=($volume_0-$volume_1)*($halaman/10); 
			//end added By Asep
						
            if ($id_storage=='TCMIA-01'){
                $total_koreksi=(1+(0.0000348*($temp-50)));
                
                if($koef_a <= 1.49){
                    $cincin=1;
                }elseif($koef_a > 1.49 && $koef_a <= 2.99){
                    $cincin=2;
                }elseif($koef_a >2.99 && $koef_a <= 4.49){
                    $cincin=3;
                }elseif($koef_a > 4.49 && $koef_a <= 5.99){
                    $cincin=4;
                }elseif($koef_a > 5.99 && $koef_a <= 7.49){
                    $cincin=5;
                }elseif($koef_a > 7.49 && $koef_a <= 9.29){
                    $cincin=6;
                }elseif($koef_a >9.29 && $koef_a <= 11.10){
                    $cincin=7;
                }else{
                    $cincin=0;
                }    
            }elseif($id_storage=='TCMIA-02'){
                $total_koreksi=(1+(0.0000348*($temp-40)));
                
                if($koef_a >= 0.21 && $koef_a <=1.51 ){
                    $cincin=1;
                }elseif($koef_a > 1.51 && $koef_a <= 3.02){
                    $cincin=2;
                }elseif($koef_a > 3.02 && $koef_a <= 4.53){
                    $cincin=3;
                }elseif($koef_a > 4.53 && $koef_a <= 6.04){
                    $cincin=4;
                }elseif($koef_a > 6.04 && $koef_a <= 7.55){
                    $cincin=5;
                }elseif($koef_a > 7.55 && $koef_a <= 8.84){
                    $cincin=6;
                }else{
                    $cincin=0;
                }
            }
            
            if(strlen($height_gross)>4){
                $volume_2 = $volume+$koef_b;    
            }elseif(strlen($height_gross)<5){
                $volume_2 = $volume;   
            }else{
                $volume_2 = $volume;   
            }

            $volume_3 = $volume_2 * $total_koreksi;
            $tonase = $volume_3*$bj;
			/*
            $this->db->where('ID_SOUNDING',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->set('VOLUME',$volume_2);
            $this->db->update('s_sounding') ; 
			*/                   
        }
		
		//Start: Added By Asep, 20140217
		if($company<>'NRP'){
			$tonase = floor($tonase );
			$tonase = $this->rounds($tonase);
		}
        //end: Added By Asep, 20140217
		/*
        $this->db->where('ID_SOUNDING',$id_sounding);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->set('WEIGHT',$tonase);
        $this->db->update('s_sounding');  
		*/  
		return $tonase;       
    }
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
    function cek_data_exist($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
           
        return $count;
    }
}
?>

