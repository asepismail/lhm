<?php
class model_s_catat_dispatch extends Model{
    function __construct(){
        parent::__construct();
        
    }
    
    function LoadData($company, $periode){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
		$periode=trim($this->db->escape_str($periode));
        $company = trim($this->db->escape_str($company));  
        $queries = "SELECT * FROM s_dispatch_franco WHERE COMPANY_CODE='".$company."' AND ACTIVE=1 AND DATE_FORMAT(TANGGALM,'%Y%m')='".$periode."'";

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
			array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DISPATCH_KIRIM,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->TANGGAL_KIRIM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KOMODITAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BROKEN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOIST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
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
    
    function search_data($no_tiket,$periode,$jenis, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $no_tiket = trim($this->db->escape_str($no_tiket));
        $periode = trim($this->db->escape_str($periode));
        $jenis = trim($this->db->escape_str($jenis));
        $company = trim($this->db->escape_str($company));
        
        $where = "WHERE 1=1"; 
        if(!empty($no_tiket)){
            if($no_tiket!='-'){
                $where.= " AND ID_DISPATCH LIKE '%".$no_tiket."%'";    
            }
        }
        if(!empty($periode)){
            if($periode!='-'){
                $where.= " AND DATE_FORMAT(TANGGALK,'%Y%m')='".$periode."' "; 
				 
            }
        }
        if(!empty($jenis)){
            if($jenis!='-'){
                $where.= " AND ID_KOMODITAS = '".$jenis."'";    
            }
        }
          
        $where .= " AND ACTIVE=1 AND COMPANY_CODE = '".$company."'";
		        
        $queries = "SELECT * FROM s_dispatch_franco ". $where;
                    
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
		//var_dump($sql);
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        
        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
			array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DISPATCH_KIRIM,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->TANGGAL_KIRIM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KOMODITAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->WAKTUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BROKEN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOIST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DRIVER_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
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

        $queries = "SELECT * FROM s_dispatch ". $where;
                    
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
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_KOSONG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BERAT_ISI,ENT_QUOTES,'UTF-8'));
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
    
    function add_new($company, $data_post){
        $company = trim($this->db->escape_str($company));
        //$status=FALSE;
		$return['status']='';
        $return['error']=false;
		
        if(empty($company)) {
			$return['status']='COMPANY_CODE CANNOT BE NULL !';
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_dispatch_franco',array('ID_DISPATCH'=>$data_post['ID_DISPATCH']),'ID_DISPATCH');
        if ($cek_data_exist > 0){
			$return['status']='Data Input ID Dispatch telah ada di database = '.$data_post['ID_DISPATCH'];
        	$return['error']=true;
        }
        
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_komoditas',array('ID_KOMODITAS'=>$data_post['ID_KOMODITAS']),'ID_KOMODITAS');
        if ($cek_data_exist <= 0){
			$return['status']='Data Jenis Barang tidak ada di database = '.$data_post['ID_KOMODITAS'];
        	$return['error']=true;
        }
        
        if(empty($return['status']) && $return['error'] == false){  
            $this->db->insert( 's_dispatch_franco', $data_post );
            if($this->db->trans_status() == FALSE){
                $return['status'] = $this->db->_error_message();//"Error in Transactions!!";
        		$return['error']=true;
            }else{
                $return['status'] = "Insert Data Berhasil ".$data_post['ID_DISPATCH'];
        		$return['error']=false; 
            }
        }
        
        return $return;
    }
    
    function update_dispatch($id_dispatch,$data_post,$company){
        $id_dispatch = trim($this->db->escape_str($id_dispatch));
        $company = trim($this->db->escape_str($company));
        $return['status']='';
        $return['error']=false;
		
        if(empty($id_dispatch)){
			$return['status']='ID_DISPATCH CANNOT BE NULL !!';
        	$return['error']=true;
        }
        
        if(empty($company)) {
			$return['status']='COMPANY_CODE CANNOT BE NULL !!';
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_dispatch_franco',array('ID'=>$id_dispatch,'COMPANY_CODE'=>$company),'ID_DISPATCH');
        if ($cek_data_exist <= 0){
			$return['status']='Data Transaksi tidak ada di database';
        	$return['error']=true;
        }
                
         if(empty($return['status']) && $return['error'] == false){              
            $this->db->where('ID',$id_dispatch);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('s_dispatch_franco',$data_post);
			 if($this->db->trans_status() === FALSE){
                $return['status'] = $this->db->_error_message();//"Error in Transactions!!";
        		$return['error']=true;
            }else{
                $return['status'] = "Update Data Berhasil, ".$data_post['ID_DISPATCH'];
        		$return['error']=false; 
            }
        }
        
        return $return;
    }
    
    function delete_dispatch($id_dispatch,$company){
        $id_dispatch = trim($this->db->escape_str($id_dispatch));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_dispatch)){
            $status = "ID_DISPATCH CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_dispatch',array('ID_DISPATCH'=>$id_dispatch),'ID_DISPATCH');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_DISPATCH',$id_dispatch);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_dispatch');
            //$this->db->delete('m_storage');
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
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
    
    function cek_data_exist($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
           
        return $count;
    }
	
	function get_no_tiket($q,$company,$tanggalm){
        $company=trim($this->db->escape_str($company));
        $tanggalm=trim($this->db->escape_str($tanggalm));
        $no_tiket=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
		
		$query="SELECT TANGGALK, ID_DISPATCH, ID_KOMODITAS, ID_DO, JENIS, DRIVER_NAME, NO_KENDARAAN, (BERAT_ISI-BERAT_KOSONG) AS BERAT_BERSIH 
FROM s_dispatch 
WHERE REPLACE(ID_DISPATCH,' ','') LIKE '%".$no_tiket."%' AND COMPANY_CODE ='".$company."' 
AND TANGGALK='".$tanggalm."'	
AND ID_DISPATCH NOT IN (SELECT ID_DISPATCH_KIRIM FROM s_dispatch_franco 
WHERE COMPANY_CODE ='".$company."' AND TANGGAL_KIRIM ='".$tanggalm."') 
AND s_dispatch.ACTIVE= 1 ORDER BY ID_DISPATCH ASC ";

        /*
		$query="SELECT NO_SPB, NO_KENDARAAN, DRIVER_NAME, (BERAT_ISI-BERAT_KOSONG) AS BERAT_BERSIH, FLAG_TIMBANGAN FROM s_data_timbangan WHERE REPLACE(NO_SPB,' ','') LIKE '%".$no_spb."%' AND COMPANY_CODE ='".$company."' AND TANGGALM='".$tanggalm."'	AND NO_SPB NOT IN (SELECT NO_SPB FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND TANGGAL='".$tanggalm."') AND s_data_timbangan.ACTIVE= 1 ORDER BY NO_TIKET ASC "; //modified by Asep, 20130506, added BERAT_BERSIH
		*/
		
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
}
?>
