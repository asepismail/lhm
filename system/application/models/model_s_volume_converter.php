<?php
class model_s_volume_converter extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    function LoadData($company,$periode){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $periode=trim($this->db->escape_str($periode));
        $company=trim($this->db->escape_str($company));  
        $queries = "SELECT ID_STORAGE, HEIGHT, VOLUME, COMPANY_CODE, ID_ANON 
                        FROM storage_volume_converter WHERE COMPANY_CODE='".$company."'";

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
        foreach($objects as $obj){
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VOLUME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));

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
        
        $company = trim($this->db->escape_str($company));
        $where = "WHERE COMPANY_CODE = '".$company."' "; 
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
        $queries ="SELECT ID_STORAGE, HEIGHT, VOLUME, COMPANY_CODE, ID_ANON 
                        FROM storage_volume_converter ".$where;
            
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
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VOLUME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));
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
    
    function get_storage($company,$q){
        $company=trim($this->db->escape_str($company));
        $q=trim($this->db->escape_str($q));
        $query="SELECT ID_STORAGE, PRODUCT_CODE
                FROM m_storage 
                WHERE COMPANY_CODE='".$company."' and ID_STORAGE LIKE '%".$q."%'";
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
    
    function add_new($tmp, $idstrg, $company, $data_post){
        $status=FALSE;
        $company = $this->db->escape_str($company);
        $tmp = $this->db->escape_str($tmp);
        $idstrg = $this->db->escape_str($idstrg);
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        if(empty($idstrg)) {
            $status = "ID_STORAGE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('storage_volume_converter',
                    array('ID_STORAGE'=>$idstrg,'HEIGHT'=>$tmp),'ID_ANON');
        if ($cek_data_exist > 0){
            $status='Data Input Volume telah ada di database';
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 'storage_volume_converter', $data_post );
                        
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil ";   
            }
        }
        return $status; 
       
    }
    
    function update_volume($id_anon,$company,$data_post){
        $id_anon = trim($this->db->escape_str($id_anon));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_anon)){
            $status = "ID VOLUME BUAH CANNOT BE NULL !!";
        }
 
        if(empty($status) || $status==false){
            
            $this->db->where('ID_ANON',$id_anon);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('storage_volume_converter',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_volume($id_anon,$company){
        $id_anon = trim($this->db->escape_str($id_anon));
        $company = trim($this->db->escape_str($company));
        
        $status=FALSE;
        if((!empty($id_anon) && $id_anon==false)){
            $status = "ID_VOLUME CANNOT BE NULL !!";
        }
        
        if((!empty($company) && $company==false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }

        if(empty($status) || $status==false){
            $this->db->where('ID_ANON',$id_anon);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->delete('storage_volume_converter');
            
            
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
