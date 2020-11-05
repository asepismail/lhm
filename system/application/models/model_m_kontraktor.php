<?php
class model_m_kontraktor extends Model
{
    function __Construct()
    {
        parent::__Construct();
        
        $this->load->database();
    }
    
    function LoadData($company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company=$this->db->escape_str($company);
        
        $queries = "SELECT * FROM m_kontraktor WHERE COMPANY_CODE='".$company."' AND ACTIVE=1";
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
        
        $sql = $queries;
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		}
				
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->KODE_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_INISIAL,ENT_QUOTES,'UTF-8'));
	     array_push($cell, htmlentities($obj->IS_KONTRAKTOR_TBS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ALAMAT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROPINSI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KOTA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KECAMATAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_POS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TELEPON,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->EMAIL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BANK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_REKENING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NPWP,ENT_QUOTES,'UTF-8'));            
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVE,ENT_QUOTES,'UTF-8'));
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
    
    function LoadData_Kendaraan($kode_kontraktor,$company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $kode_kontraktor=$this->db->escape_str($kode_kontraktor);
        $company=$this->db->escape_str($company);
        
        $queries ="SELECT ID_KENDARAAN_KONTRAKTOR,NO_KENDARAAN AS TMP_NO,NO_KENDARAAN,DESKRIPSI,NOTE FROM m_kontraktor_kendaraan
                    WHERE ACTIVE=1 AND KODE_KONTRAKTOR ='".$kode_kontraktor."' AND COMPANY_CODE='".$company."'";
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
		$sql = $queries;
		
        if( $count >0 ) {
        	$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            //array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_KENDARAAN_KONTRAKTOR,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->TMP_NO,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESKRIPSI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));

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
    
    function add_new($kode_kontraktor,$company,$data_post){
        $kode_kontraktor =$this->db->escape_str($kode_kontraktor);
        $company = $this->db->escape_str($company);
        
        $status='';
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($kode_kontraktor) && $kode_kontraktor===false)) {
            $status = "KODE_KONTRAKTOR CANNOT BE NULL !!";
        }      
        
        if(empty($status) || $status==false){
            $this->db->insert( 'm_kontraktor', $data_post );  
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message(); //"Error in Transactions!!";
            }else{
                $status = $this->db->affected_rows();    
            }      
        }
        return $status;
    }
    
    function update_data($id,$company,$data_post){  
        $id =$this->db->escape_str($id);
        $company =$this->db->escape_str($company);
        $tableName = 'm_kontraktor';
        $status ='';
        
        $status='';
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($id) && $id===false)) {
            $status = "KODE_KONTRAKTOR CANNOT BE NULL !!";
        }
                
        if(empty($status) || $status==false){
            $this->db->where( 'KODE_KONTRAKTOR',$id );
            $this->db->where( 'COMPANY_CODE',$company);
            $this->db->update( $tableName, $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();
            }else{
                $status = $this->db->affected_rows();    
            }       
        }
        
        return $status;
    }
    
    function add_new_kendaraan($id_kontraktor,$no_kend,$company,$data_post){
        $tableName = 'm_kontraktor_kendaraan';
        $no_kend =$this->db->escape_str($no_kend);
        $company =$this->db->escape_str($company);
        $id_kontraktor =$this->db->escape_str($id_kontraktor);
        
        $status='';
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($no_kend) && $no_kend===false)) {
            $status = "NO_KENDARAAN CANNOT BE NULL !!";
        }
        
        $cek_data_kontraktor = $this->cek_data_exist('m_kontraktor',
                        array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company),'KODE_KONTRAKTOR');
        if ($cek_data_kontraktor <= 0){
            $status='Harap buat data kontraktor terlebih dahulu';
        }
        
        $cek_data = $this->cek_data_exist($tableName,array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company,'NO_KENDARAAN'=>$no_kend),'NO_KENDARAAN');
        if ($cek_data > 0){
            $status='Data Kendaraan sudah ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->insert( 'm_kontraktor_kendaraan', $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Kendaraan Berhasil";   
            }      
        }
        
        return $status;
    }
    
    function update_kendaraan($id_kontraktor,$id_kendaraan,$no_kend,$company,$data_post){
        $id_kontraktor =$this->db->escape_str($id_kontraktor);
        $id_kendaraan =$this->db->escape_str($id_kendaraan);
        $no_kend =$this->db->escape_str($no_kend);
        $company =$this->db->escape_str($company);
        $status='';
        
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($no_kend) && $no_kend===false)) {
            $status = "NO_KENDARAAN CANNOT BE NULL !!";
        }
        
        if((empty($id_kontraktor) && $id_kontraktor===false)) {
            $status = "ID_KONTRAKTOR CANNOT BE NULL !!";
        }
        
        $cek_data_kontraktor = $this->cek_data_exist('m_kontraktor',
                        array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company),'KODE_KONTRAKTOR');
        if ($cek_data_kontraktor <= 0){
            $status='Harap buat data kontraktor terlebih dahulu';
        }
        
        $cek_data = $this->cek_data_exist('m_kontraktor_kendaraan',
            array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company,'ID_KENDARAAN_KONTRAKTOR'=>$id_kendaraan),'ID_KENDARAAN_KONTRAKTOR');
        if ($cek_data <= 0){
            $status='Data Kendaraan tidak ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->where( 'ID_KENDARAAN_KONTRAKTOR',$id_kendaraan); 
            //$this->db->where( 'NO_KENDARAAN',$no_kend);
            $this->db->where( 'KODE_KONTRAKTOR',$id_kontraktor ); 
            $this->db->where( 'COMPANY_CODE',$company);
            $this->db->update( 'm_kontraktor_kendaraan', $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data Berhasil";   
            }      
        }
        
        return $status;
        
        /*$id_kontraktor = $this->db->escape_str($id_kontraktor);
        $no_kend = $this->db->escape_str($no_kend);
        $company = $this->db->escape_str($company);
        
        $this->$delete_kendaraan($id_kontraktor,$no_kend,$company);
        $this->$add_new_kendaraan($id_kontraktor,$no_kend,$company,$data_post);*/    
    }
    
    function delete_kendaraan($no_kend,$id_kontraktor,$company){
        $id_kontraktor = $this->db->escape_str($id_kontraktor);
        $no_kend = $this->db->escape_str($no_kend);
        $company = $this->db->escape_str($company);
        
        $status='';
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($no_kend) && $no_kend===false)) {
            $status = "NO_KENDARAAN CANNOT BE NULL !!";
        }
        
        if((empty($id_kontraktor) && $id_kontraktor===false)) {
            $status = "ID_KONTRAKTOR CANNOT BE NULL !!";
        }
        
        $cek_data_kontraktor = $this->cek_data_exist('m_kontraktor',
                        array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company),'KODE_KONTRAKTOR');
        if ($cek_data_kontraktor <= 0){
            $status='Harap buat data kontraktor terlebih dahulu';
        }
        
        $cek_data = $this->cek_data_exist('m_kontraktor_kendaraan',
            array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company,'NO_KENDARAAN'=>$no_kend),'NO_KENDARAAN');
        if ($cek_data <= 0){
            $status='Data Kendaraan tidak ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->where( 'KODE_KONTRAKTOR',$id_kontraktor );
            $this->db->where( 'NO_KENDARAAN',$no_kend);
            $this->db->where( 'COMPANY_CODE',$company);
            
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 'm_kontraktor_kendaraan');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data Berhasil";   
            }      
        }
        
        return $status;
    }
    
    function delete_kontraktor($id_kontraktor,$company){
        $id_kontraktor = $this->db->escape_str($id_kontraktor);
        $company = $this->db->escape_str($company);
        
        $status='';
        if((empty($company) && $company===false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        if((empty($id_kontraktor) && $id_kontraktor===false)) {
            $status = "ID_KONTRAKTOR CANNOT BE NULL !!";
        }
    
        $cek_data = $this->cek_data_exist('m_kontraktor',
            array('KODE_KONTRAKTOR'=>$id_kontraktor,'COMPANY_CODE'=>$company),'KODE_KONTRAKTOR');
        if ($cek_data <= 0){
            $status='Data Kontraktor tidak ada di database';
        }
        
        if(empty($status) || $status==false){
            $this->db->where( 'KODE_KONTRAKTOR',$id_kontraktor );
            $this->db->where( 'COMPANY_CODE',$company);
            
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 'm_kontraktor');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Data berhasil dinonaktifkan";   
            }      
        }
        
        return $status;  
    }
    
    function search_data($nama,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
		$where = " WHERE "; 
        $nama=$this->db->escape_str($nama);
        $company=$this->db->escape_str($company);
		
        if (isset($nama)){
			if($nama != "-"){
				$where .= " NAMA_KONTRAKTOR LIKE '$nama%' AND COMPANY_CODE = '".$company."' 
					AND ACTIVE=1 OR KODE_INISIAL LIKE '$nama%' 
					AND COMPANY_CODE = '".$company."' AND ACTIVE=1";
			} else {
				$where .= " COMPANY_CODE = '".$company."' AND ACTIVE=1";	
			}
        } else {
           $where .= " COMPANY_CODE = '".$company."' AND ACTIVE=1";
        }
        
        $queries = "SELECT * FROM m_kontraktor". $where;
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
        
		if( $count >0 ) {
            $sql2 = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
        }
        

        $objects = $this->db->query($sql2,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->KODE_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_INISIAL,ENT_QUOTES,'UTF-8'));
	     array_push($cell, htmlentities($obj->IS_KONTRAKTOR_TBS,ENT_QUOTES,'UTF-8'));
	     array_push($cell, htmlentities($obj->NAMA_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ALAMAT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROPINSI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KOTA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KECAMATAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_POS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TELEPON,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->EMAIL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BANK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_REKENING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NPWP,ENT_QUOTES,'UTF-8'));            
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVE,ENT_QUOTES,'UTF-8')); 
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
    
    function data_search($data_search,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=$this->db->escape_str($company);
 
        $where = "WHERE ACTIVE=1 AND COMPANY_CODE = '".$company."'"; 
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
        
        $queries = "SELECT * FROM m_kontraktor ". $where;
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
            array_push($cell, htmlentities($obj->KODE_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_INISIAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_CONTACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ALAMAT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROPINSI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KOTA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KECAMATAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_POS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TELEPON,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->EMAIL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BANK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_REKENING,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NPWP,ENT_QUOTES,'UTF-8'));            
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVE,ENT_QUOTES,'UTF-8'));
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
    
    function cek_data_exist($tableName,$where_condition,$select_condition){
        
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
   
        return $count;
    }
	
	/* option tambahan */
	
	function get_propinsi()
	{
		$query = $this->db->query("SELECT id_prov,nama_prov FROM m_wil_propinsi");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_kabkot($propinsi)
	{
		$query = $this->db->query("SELECT id_kabkot,nama_kabkot FROM m_wil_kabkot WHERE id_prov = '".$propinsi."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_kecamatan($propinsi, $kabkot)
	{
		$query = $this->db->query("SELECT id_kec,nama_kec FROM m_wil_kecamatan WHERE id_prov = '".$propinsi."' AND id_kabkot = '".$kabkot."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function getCompanyIDAdem($company){
				
		$pgsql = $this->load->database('adem', TRUE);
		$pgquery = "SELECT ad_org_id as ret FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $pgsql->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
		/* $temp_result = array();
			foreach ( $query->result_array() as $row )
			{
				$temp_result [] = $row;
			}
			
			$this->db->close();
			return $temp_result; */
	}
	
	/* get data adempiere */
	function LoadDataAdem($company, $nama="")
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		$pgsql = $this->load->database('adem', TRUE);
		
		$wherenama = "";
		if($nama!=""){
			$wherenama = " AND bp.value LIKE '".$nama."%'";
		}
		
        $queries = "SELECT bp.c_bpartner_id, bp.isactive, bp.value as inisal, bp.name as nama, bp.description as ket, 
					bp.taxid as npwp, loc2.address1, loc2.address2, loc2.city, loc2.postal, reg.name as propinsi, 
					loc.phone, loc.fax, bp.ad_org_id, org.name 
					FROM c_bpartner bp  
					LEFT JOIN ad_org org ON org.ad_org_id = bp.ad_org_id
					LEFT JOIN ( SELECT DISTINCT ON (c_bpartner_id) * FROM C_BPartner_Location) loc 
								ON loc.c_bpartner_id = bp.c_bpartner_id
					LEFT JOIN c_location loc2 ON loc2.c_location_id = loc.c_location_id
					LEFT JOIN c_region reg ON reg.c_region_id = loc2.c_region_id
					WHERE c_bp_group_id = 1000008 AND bp.isactive = 'Y' AND bp.ad_org_id = 0 ".strtoupper($wherenama)." 
    				OR c_bp_group_id = 1000008 AND bp.isactive = 'Y' AND bp.ad_org_id = ". $company ." ".$wherenama."";
        $sql2 = $queries;
       
        if(!$sidx) $sidx =1;
		//$pgsql->query($queries)
        $query = $pgsql->query($queries);
        $count = $query->num_rows(); 
		
        if( $count > 0 ) {
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
        
        $sql = $queries;
		
		if( $count >0 ) {
			//echo $count;	
			$sql .= " ORDER BY ad_org_id, ".$sidx." ".$sord." LIMIT ".$limit." OFFSET ".$start." ";
		}
		// $query = $pgsql->query($queries);		
        $objects = $pgsql->query($sql)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
			$alamat = $obj->address1.' '.$obj->address2.' '.$obj->city.' '.$obj->propinsi;
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->c_bpartner_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->inisal,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->nama,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ket,ENT_QUOTES,'UTF-8'));
            array_push($cell, $alamat);
			array_push($cell, htmlentities($obj->postal,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->phone,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->fax,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ad_org_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->npwp,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->name,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->isactive,ENT_QUOTES,'UTF-8'));
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
	/* end get data adempiere */
}
?>
