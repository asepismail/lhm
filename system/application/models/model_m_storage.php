<?php
class model_m_storage extends Model{
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    function LoadData($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));  
        $queries = "SELECT * FROM m_storage WHERE COMPANY_CODE='".$company."' AND ACTIVE=1";

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
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MAXCAPACITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DIAMETER,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS_ATAP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ZERO_CAPACITY,ENT_QUOTES,'UTF-8'));
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
        
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 'm_storage', $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";   
            }
        }
        
        return $status;
    }
    
    function update_storage($id_storage,$data_post,$company){
        $id_storage = trim($this->db->escape_str($id_storage));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_storage)){
            $status = "ID_STORAGE CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$id_storage),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_STORAGE',$id_storage);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_storage',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_storage($id_storage,$company){
        $id_storage = trim($this->db->escape_str($id_storage));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_storage)){
            $status = "ID_STORAGE CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$id_storage),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_STORAGE',$id_storage);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 'm_storage');
            //$this->db->delete('m_storage');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
    }
    
    function search_data($kode_storage, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $kode_storage = trim($this->db->escape_str($kode_storage));
        $company = trim($this->db->escape_str($company));
        
        $where = "WHERE 1=1"; 
        if(!empty($kode_storage)){
            if($kode_storage!='-'){
                $where.= " AND ID_STORAGE LIKE '%$kode_storage%'";    
            }
        }
          
        $where .= " AND COMPANY_CODE = '".$company."'";
        
        $queries = "SELECT * FROM m_storage ". $where;
                    
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
        //$sql = "select * FROM m_employee ".$where." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        
        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MAXCAPACITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DIAMETER,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS_ATAP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ZERO_CAPACITY,ENT_QUOTES,'UTF-8'));
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
        
        $queries = "SELECT * FROM m_storage ". $where;
                    
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
        //$sql = "select * FROM m_employee ".$where." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        
        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MAXCAPACITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DIAMETER,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS_ATAP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ZERO_CAPACITY,ENT_QUOTES,'UTF-8'));
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
