<?php
class model_s_restan_block extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    } 
    
    function LoadData($periode,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $periode = trim($this->db->escape_str($periode));
        $company = trim($this->db->escape_str($company));
        
        $queries ="SELECT ID_AFKIR, TANGGAL, AFD, BLOCK, JANJANG,COMPANY_CODE FROM s_jjg_afkir 
                    WHERE COMPANY_CODE='".$company."' AND ACTIVE =1 ORDER BY TANGGAL DESC -- AND TANGGAL='".$periode."'";
            
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
        
        $sql = $queries." ORDER B ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_AFKIR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JANJANG,ENT_QUOTES,'UTF-8'));
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
        
        $company = trim($this->db->escape_str($company));
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
        $queries ="SELECT ID_AFKIR, TANGGAL, AFD, BLOCK, JANJANG,COMPANY_CODE FROM s_jjg_afkir ".$where;
            
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
            array_push($cell, htmlentities($obj->ID_AFKIR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JANJANG,ENT_QUOTES,'UTF-8')); 
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
        $status=FALSE;
        $company = trim($this->db->escape_str($company));
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_jjg_afkir',
                    array('AFD'=>$data_post['AFD'],'BLOCK'=>$data_post['BLOCK'],'TANGGAL'=>$data_post['TANGGAL'],'COMPANY_CODE'=>$company),'ID_AFKIR');
        if ($cek_data_exist > 0){
            $status='Data Jjg Afkir untuk Afd dan Block telah ada di database';
        }
        
        unset($cek_data_exist);
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_jjg_afkir', $data_post );
                    
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";   
            }
        }
        return $status;
    }
    
    function update_restan($id_restan,$data_post,$company){
        $id_restan = trim($this->db->escape_str($id_restan));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if(empty($id_restan)){
            $status = "id_restan CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_jjg_afkir',array('ID_AFKIR'=>$id_restan),'ID_AFKIR');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('ID_AFKIR',$id_restan);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_jjg_afkir',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_restan($id_restan,$company){
        $id_restan = trim($this->db->escape_str($id_restan));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_restan)){
            $status = "id_restan CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_jjg_afkir',array('ID_AFKIR'=>$id_restan),'ID_AFKIR');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_AFKIR',$id_restan);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_jjg_afkir');
            //$this->db->delete('s_restan');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    } 
    
    function get_afdeling($company,$q){
        $company=trim($this->db->escape_str($company));
        $q=trim($this->db->escape_str($q));
        $query="SELECT LEFT(LOCATION_CODE,2) AS AFD 
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LOCATION_TYPE_CODE='OP'
                    AND LOCATION_CODE LIKE '%".$q."%'
                GROUP BY AFD,COMPANY_CODE";
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
    
    function get_block($company,$location_left,$location){
        $company=trim($this->db->escape_str($company));
        $location_left=trim($this->db->escape_str($location_left));
        $location=trim($this->db->escape_str($location)); 
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$location_left."' 
                AND LOCATION_CODE LIKE '%".$location."%' AND LOCATION_TYPE_CODE='OP'";
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
    
    function lokasi_validate($afd,$location,$company){
        $company = trim($this->db->escape_str($company));
        $afd = trim($this->db->escape_str($afd));
        $location = trim($this->db->escape_str($location));
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$afd."' 
                AND LOCATION_CODE = '".$location."' AND LOCATION_TYPE_CODE='OP'"; 
        $sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        return $rowcount;    
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
