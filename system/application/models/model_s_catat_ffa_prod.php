<?php
class model_s_catat_ffa_prod extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    function LoadData($periode,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $periode=trim($this->db->escape_str($periode));
        $company=trim($this->db->escape_str($company));
        $wherep=FALSE;
        if(!empty($periode)){
            if($periode!='-'){
                $wherep =  " AND DATE_FORMAT(DATE,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d')";    
            }    
        }
          
        $queries = "SELECT * FROM s_ffa_prod WHERE ACTIVE=1 AND COMPANY_CODE='".$company."'".$wherep;

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
            array_push($cell, htmlentities($obj->ID_FFA_PROD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WATER_CONTENT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->REMARKS,ENT_QUOTES,'UTF-8'));
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
    
    function data_search($data_search,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=trim($this->db->escape_str($company));
        $where = "WHERE ACTIVE=1 AND COMPANY_CODE = '".$company."' "; 
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
          
        $queries = "SELECT * FROM s_ffa_prod ".$where;

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
            array_push($cell, htmlentities($obj->ID_FFA_PROD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WATER_CONTENT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->REMARKS,ENT_QUOTES,'UTF-8'));
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
        $company=trim($this->db->escape_str($company));
        $prod_code=trim($this->db->escape_str($q));
        
        $query="SELECT ID_STORAGE, PRODUCT_CODE, DESCRIPTION FROM m_storage
                WHERE ID_STORAGE LIKE '%".$prod_code."%' AND COMPANY_CODE ='".$company."' ";
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
        $status=FALSE;
        $company = trim($this->db->escape_str($company));
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ffa_prod',array('ID_FFA_PROD'=>$data_post['ID_FFA_PROD']),'ID_FFA_PROD');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database';
        }
        
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_ffa_prod',
                                array('DATE_FORMAT(DATE,"%Y%m%d")'=>date("Ymd",strtotime($data_post['DATE'])),
                                        'COMPANY_CODE'=>$company,'ACTIVE'=>1),
                                        'ID_FFA_PROD');
        if ($cek_data_exist > 0){
            $status='Data FFA untuk Pabrik hari ini telah ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_ffa_prod', $data_post );
                    
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";   
            }
        }
        return $status;    
    }
    
    function update_ffa($id_ffa,$data_post,$company){
        $id_ffa = trim($this->db->escape_str($id_ffa));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_ffa)){
            $status = "id_ffa CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ffa_prod',array('ID_FFA_PROD'=>$id_ffa),'ID_FFA_PROD');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_FFA_PROD',$id_ffa);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_ffa_prod',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_ffa($id_ffa,$company){
        $id_ffa = trim($this->db->escape_str($id_ffa));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_ffa)){
            $status = "ID_FFA CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ffa_prod',array('ID_FFA_PROD'=>$id_ffa),'ID_FFA_PROD');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_FFA_PROD',$id_ffa);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_ffa_prod');
            //$this->db->delete('s_ffa');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
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
