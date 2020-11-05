<?php
class model_s_production extends Model{
    function __construct(){
        parent::__construct();
        
    }
    
    function LoadData($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
		$queries = "SELECT p.*,k.DESKRIPSI FROM s_production p 
					INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS 
					WHERE p.COMPANY_CODE='".$company."' AND p.ACTIVE=1";

        //$queries = "SELECT * FROM s_production WHERE COMPANY_CODE='".$company."' AND ACTIVE=1";
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
            array_push($cell, htmlentities($obj->ID_PRODUCTION,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->PRODUCTION_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESKRIPSI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
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
    
	function LoadData_Commodity($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
		//$queries = "SELECT p.*,k.DESKRIPSI FROM s_production p 
					//INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS 
					//WHERE p.COMPANY_CODE='".$company."' AND p.ACTIVE=1";
		/*$queries = "SELECT c.ID_KOMODITAS, c.JENIS, prod.WEIGHT  FROM
					(
						SELECT k.ID_KOMODITAS , k.JENIS FROM s_komoditas k
						WHERE k.ACTIVE=1
						AND k.COMPANY_CODE ='".$company."'  
						AND k.KODE_JENIS IN ('CPO', 'KRN', 'SLD', 'TNK', 'CKG', 'ABJ')
					) c
					LEFT JOIN(
						SELECT p.ID_COMMODITY, p.WEIGHT FROM s_production p 
						WHERE p.PRODUCTION_DATE = '".$date."' 
						AND p.COMPANY_CODE = '".$company."'
						AND p.ACTIVE =1
						) prod on c.ID_KOMODITAS = prod.ID_COMMODITY
					ORDER BY c.ID_KOMODITAS";*/
		$queries="SELECT k.ID_KOMODITAS AS ID_COMMODITY , k.JENIS AS COMMODITY FROM s_komoditas k
					WHERE k.ACTIVE=1
					AND k.COMPANY_CODE ='".$company."' 
					AND k.KODE_JENIS IN ('CPO', 'KRN', 'SLD', 'TNK', 'CKG', 'ABJ')";
		
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
		var_dump($sql);
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMMODITY,ENT_QUOTES,'UTF-8'));
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
        
        $where = "WHERE p.ACTIVE=1 AND p.COMPANY_CODE = '".$company."'"; 
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

        $queries = "SELECT p.*,k.DESKRIPSI FROM s_production p INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS ". $where;
                    
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
            array_push($cell, htmlentities($obj->ID_PRODUCTION,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->PRODUCTION_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESKRIPSI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));			
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
    
    function add_new($company, $data_post){
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_production',array('ID_PRODUCTION'=>$data_post['ID_PRODUCTION']),'ID_PRODUCTION');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database';
        }
		
		unset($cek_data_exist);
		$cek_data_exist = $this->cek_data_exist('s_production',array('PRODUCTION_DATE'=>$data_post['PRODUCTION_DATE'],'ID_COMMODITY'=>$data_post['ID_COMMODITY'],'COMPANY_CODE'=>$company, 'ACTIVE'=>1),'ID_PRODUCTION');
        if ($cek_data_exist > 0){
            $status="Jenis barang ". $data_post['ID_COMMODITY'] ." telah diinput hari ini!";
        }
		
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_komoditas',array('ID_KOMODITAS'=>$data_post['ID_COMMODITY']),'ID_KOMODITAS');
        if ($cek_data_exist <= 0){
            $status='Jenis barang tidak ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_production', $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();
            }else{
                $status="Insert Data Berhasil";   
            }
        }
        
        return $status;
    }
    
    function update_production($id_production,$data_post,$company){		
        $id_production = trim($this->db->escape_str($id_production));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_production)){
            $status = "ID_PRODUCTION CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_production',array('ID_PRODUCTION'=>$id_production,'COMPANY_CODE'=>$company),'ID_PRODUCTION');
        if ($cek_data_exist <= 0){
            $status='Data Transaksi tidak ada di database';
        } 
		/*
		unset($cek_data_exist);
		$cek_data_exist = $this->cek_data_exist('s_production',array('PRODUCTION_DATE'=>$data_post['PRODUCTION_DATE'],'ID_COMMODITY'=>$data_post['ID_COMMODITY'],'COMPANY_CODE'=>$company, 'ACTIVE'=>1),'ID_PRODUCTION');
        if ($cek_data_exist > 0){
            $status="Tanggal ". $data_post['PRODUCTION_DATE'] .", produksi ". $data_post['ID_COMMODITY'] ." telah diinput!";
        }
		*/
		
		unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_komoditas',array('ID_KOMODITAS'=>$data_post['ID_COMMODITY']),'ID_KOMODITAS');
        if ($cek_data_exist <= 0){
            $status='Jenis barang tidak ada di database';
        }

        if(empty($status) || $status===FALSE){            
            $this->db->where('ID_PRODUCTION',$id_production);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_production',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update data production Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_production($id_production,$company){
        $id_production = trim($this->db->escape_str($id_production));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_production)){
            $status = "ID_PRODUCTION CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_production',array('ID_PRODUCTION'=>$id_production),'ID_PRODUCTION');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_PRODUCTION',$id_production);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update('s_production');
            //$this->db->delete('m_storage');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;        
    }
    /*
	function get_ba_commodity($company){             
        $company=$this->db->escape_str($company);
		
        $query="SELECT ID_KOMODITAS AS ID_COMMODITY, JENIS AS COMMODITY FROM s_komoditas
                WHERE KODE_JENIS IN ('CPO', 'KRN', 'SLD', 'TNK', 'CKG', 'ABJ') AND COMPANY_CODE ='".$company."' AND ACTIVE=1 ORDER BY ID_KOMODITAS";
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
	*/
    function get_commodity($q,$company){             
        $company=$this->db->escape_str($company);
        $prod_code=$this->db->escape_str($q);
        
        $query="SELECT k.ID_KOMODITAS, k.JENIS, k.DESKRIPSI FROM s_komoditas k
                WHERE k.DESKRIPSI LIKE '%".$prod_code."%' AND COMPANY_CODE ='".$company."' ";
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
    
    function get_komoditi($q,$company){             
        $company=$this->db->escape_str($company);
        $jenis=$this->db->escape_str($q);
        
        $query="SELECT ID_KOMODITAS, JENIS FROM s_komoditas
                WHERE JENIS LIKE '%".$jenis."%' AND COMPANY_CODE ='".$company."' AND ACTIVE=1";
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
    /*
	function get_id_komoditi($q,$company){             
        $company=$this->db->escape_str($company);
        $jenis=$this->db->escape_str($q);
        
        $query="SELECT ID_KOMODITAS FROM s_komoditas
                WHERE JENIS LIKE '%".$jenis."%' AND COMPANY_CODE ='".$company."' AND ACTIVE=1";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
            $value = $row->ID_KOMODITAS;    
        }else{
            $value = 0;   
        } 
        return $value;          
    }
	*/
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
