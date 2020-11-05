<?php
class model_s_catat_sounding_kernel extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();  
    }
    
    function LoadData($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company=trim($this->db->escape_str($company)); 
        $queries = "SELECT ID_SOUNDING_KERNEL, ID_STORAGE, DATE, TIME, (CASE HEIGHT WHEN 0 THEN '' ELSE HEIGHT END) AS HEIGHT,(CASE HEIGHT2 WHEN 0 THEN '' ELSE HEIGHT2 END) AS HEIGHT2, WEIGHT, EXTRA_WEIGHT, COMPANY_CODE  FROM s_sounding_kernel WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' ORDER BY DATE DESC";

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
            array_push($cell, htmlentities($obj->ID_SOUNDING_KERNEL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->HEIGHT2,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities(number_format($obj->VOLUME,4),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->EXTRA_WEIGHT,ENT_QUOTES,'UTF-8'));
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
        
        $query="SELECT ID_STORAGE, PRODUCT_CODE, DESCRIPTION FROM m_storage WHERE ID_STORAGE LIKE '%".$prod_code."%' AND COMPANY_CODE ='".$company."' AND PRODUCT_CODE = 'KERNEL'";
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
        
        $cek_data_exist = $this->cek_data_exist('s_sounding_kernel',array('ID_SOUNDING_KERNEL'=>$data_post['ID_SOUNDING_KERNEL']),'ID_SOUNDING_KERNEL');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database.. !!';    
        }
        
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_sounding_kernel',array('ACTIVE'=>1, 'ID_STORAGE'=>$data_post['ID_STORAGE'],'DATE'=>$data_post['DATE']),'ID_SOUNDING_KERNEL');
        if ($cek_data_exist > 0){
            $status="Sounding Storage '".$data_post['ID_STORAGE']."' , untuk periode ".$data_post['DATE'].
                            " telah dilakukan";
        }
        
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status="ID STORAGE : ".$data_post['ID_STORAGE']." Tidak terdapat di database";
        }
             
        if(empty($status) || $status===FALSE){
            $this->db->insert( 's_sounding_kernel', $data_post );
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil";
                
                //$this->calc_volume($data_post['HEIGHT'],$data_post['ID_SOUNDING'],$data_post['ID_STORAGE'],$company);// hitung volume tanki berdasarkan table yg telah di tetapkan 
                //$this->calc_weight($data_post['TEMPERATURE'],$data_post['ID_SOUNDING'],$company);//hitung berat tangki  
            }
        }
        return $status;
    }
    
    function update_sounding($id_sounding,$data_post,$company){
        $id_sounding = trim($this->db->escape_str($id_sounding));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_sounding)){
            $status = "id_sounding CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_sounding_kernel',array('ID_SOUNDING_KERNEL'=>$id_sounding),'ID_SOUNDING_KERNEL');
        if ($cek_data_exist <= 0){
            $status ="DATA SOUNDING NOT EXIST !!";
        }
        $cek_data_exist = $this->cek_data_exist('m_storage',array('ID_STORAGE'=>$data_post['ID_STORAGE']),'ID_STORAGE');
        if ($cek_data_exist <= 0){
            $status ="DATA STORAGE NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_SOUNDING_KERNEL',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_sounding_kernel',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                //$this->calc_volume($data_post['HEIGHT'],$id_sounding,$data_post['ID_STORAGE'],$company);
                //$this->calc_weight($data_post['TEMPERATURE'],$id_sounding,$company);
                
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
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
        
        $cek_data_exist = $this->cek_data_exist('s_sounding_kernel',array('ID_SOUNDING_KERNEL'=>$id_sounding),'ID_SOUNDING_KERNEL');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_SOUNDING_KERNEL',$id_sounding);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_sounding_kernel');
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
        //$sound_type = $this->snd_type;
        
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
        $where .= " AND ACTIVE =1 AND COMPANY_CODE = '".$company."'";
        
        $queries = "SELECT * FROM s_sounding_kernel ". $where;
                    
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
            array_push($cell, htmlentities($obj->ID_SOUNDING_KERNEL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
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
        
        $queries = "SELECT * FROM s_sounding_kernel ". $where;
                    
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
            array_push($cell, htmlentities($obj->ID_SOUNDING_KERNEL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TIME,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->HEIGHT,ENT_QUOTES,'UTF-8'));
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
        $query="SELECT VOLUME FROM storage_volume_converter WHERE ID_STORAGE='".$id_storage."' AND HEIGHT='".$height."' AND COMPANY_CODE='".$company."'"; 
        $sQuery = $this->db->query($query);

        if ($sQuery->num_rows() > 0){
           $row = $sQuery->row();
           $volume=$row->VOLUME;
           
           $this->db->where('ID_SOUNDING',$id_sounding);
           $this->db->where('COMPANY_CODE',$company);
           $this->db->set('VOLUME',$volume);
           $this->db->update('s_sounding');
        }           
    }
    
    function calc_weight($temp,$id_sounding,$company){
        $temp = trim($this->db->escape_str($temp));
        $company = trim($this->db->escape_str($company));
        $id_sounding = trim($this->db->escape_str($id_sounding));
        
        $bj=0;
        $volume=0; 
        $query="SELECT VOLUME FROM s_sounding WHERE ID_SOUNDING='".$id_sounding."' AND COMPANY_CODE='".$company."'";
        $sQuery = $this->db->query($query);

        if ($sQuery->num_rows() > 0)
        {
           $row = $sQuery->row();
           $volume=$row->VOLUME;
           
           $sQuery->free_result(); 
           $query="SELECT BJ FROM storage_temperature_converter WHERE TEMPERATURE='".$temp."' AND COMPANY_CODE='".$company."'";
           $sQuery = $this->db->query($query);
           if ($sQuery->num_rows() > 0){
                $row = $sQuery->row();    
                $bj=$row->BJ;
           } 
           
           $weight = $volume*$bj; 
           
           $this->db->where('ID_SOUNDING',$id_sounding);
           $this->db->where('COMPANY_CODE',$company);
           $this->db->set('WEIGHT',$weight);
           $this->db->update('s_sounding');
        }           
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


