<?php
class model_s_bpartner extends Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
        
    }

    function LoadData($periode, $company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $periode=trim($this->db->escape_str($periode));
        $company=trim($this->db->escape_str($company));
          
        $queries = "SELECT SUPPLIERCODE, C_BPARTNER_ID, SUPPLIERNAME, CONTACTNAME, ADDRESS, SUPPLIERTYPE, NPWP, COMPANY_CODE, KODE_PENGIRIM FROM m_supplier WHERE SUPPLIERTYPE = 'LUAR' AND ACTIVE = 1 AND COMPANY_CODE = '".$company."'";

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
            array_push($cell, htmlentities($obj->SUPPLIERCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->C_BPARTNER_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERNAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CONTACTNAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ADDRESS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERTYPE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NPWP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_PENGIRIM,ENT_QUOTES,'UTF-8'));
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
    
    function get_adem_bptbs($company){
        $company = strtoupper(trim($this->db->escape_str($company)));
        
        $config['hostname'] = "10.88.1.74";
        $config['username'] = "adempiere";
        $config['password'] = "adem5224878";
        $config['database'] = "adempiere";
        $config['dbdriver'] = "postgre";
        $config['dbprefix'] = "";
        $config['pconnect'] = TRUE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        $config['port'] = "5432";

        $pgsql = $this->load->database($config, TRUE);
        
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $ad_org_id='';
        if($company=='LIH'){
            $ad_org_id="1000001";    
        }else if($company=='GKM'){
            $ad_org_id="1000028";
        }else if($company=='MAG'){
            $ad_org_id="1000024";  
        }else if($company=='SSS'){
            $ad_org_id="1000018";  
        }else if($company=='SML'){
            $ad_org_id="1000031";  
        }else if($company=='SMI'){
            $ad_org_id="1000070";  
        }else if($company=='NRP'){
            $ad_org_id="1000060";  
        }else if($company=='TPAI'){
            $ad_org_id="1000015";  
        }
          
        $pgquery="SELECT c_bpartner.c_bpartner_id, c_bpartner.ad_org_id, ad_org.value as company_code, c_bpartner.c_bp_group_id, c_bp_group.value as group, c_bpartner.value as kode, c_bpartner.name, taxid,
 c_location.address1||' '||c_location.city||' '||c_location.regionname as address, c_bpartner.description, ad_user.name as cp 
FROM c_bpartner 
inner join c_bp_group on c_bp_group.c_bp_group_id = c_bpartner.c_bp_group_id 
inner join ad_org on ad_org.ad_org_id = c_bpartner.ad_org_id
inner join c_bpartner_location on c_bpartner_location.c_bpartner_id = c_bpartner.c_bpartner_id
inner join c_location on c_location.c_location_id = c_bpartner_location.c_location_id
inner join ad_user on ad_user.c_bpartner_id = c_bpartner.c_bpartner_id
WHERE c_bp_group.value = 'Vendor Supplier' AND c_bpartner.ad_org_id = '".$ad_org_id."'";
                    //-- and cord.ad_org_id=1000001
       
        if(!$sidx) $sidx =1;
        $query = $pgsql->query($pgquery);
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
        
        $sql = $pgquery." ORDER BY ".$sidx." ".$sord." LIMIT ".$limit." OFFSET ".$start; 

        $objects = $pgsql->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->c_bpartner_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->company_code,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->group,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->kode,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->name,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->taxid,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->address,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->cp,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->description,ENT_QUOTES,'UTF-8'));
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

        $pgsql->close();
        return $jsonObject;
    }
    
    function data_search($data_search,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=trim($this->db->escape_str($company));
        $where = "WHERE potbs.ACTIVE=1 AND potbs.COMPANY_CODE = '".$company."' "; 
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
          
        $queries = "SELECT ID_ANON, PO_NUMBER, msup.SUPPLIERCODE, msup.SUPPLIERNAME, QTYORDERED, PRICELIST,TANGGALM, 
                    TANGGALK, DESCRIPTION, potbs.COMPANY_CODE,potbs.SINKRON_STATUS,potbs.C_BPARTNER_ID
                    FROM m_po_tbs potbs
                    LEFT JOIN m_supplier msup ON msup.SUPPLIERCODE = potbs.SUPPLIERCODE and msup.COMPANY_CODE = potbs.COMPANY_CODE ".$where;

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
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PO_NUMBER,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUPPLIERNAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYORDERED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PRICELIST,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGALM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGALK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SINKRON_STATUS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->C_BPARTNER_ID,ENT_QUOTES,'UTF-8'));
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
    
    function update_data($kode, $data_post){
        $kode = trim($this->db->escape_str($kode));
        $status=FALSE;
        
        if(empty($kode)) {
            $status = "Nomor PO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_supplier',array('SUPPLIERCODE'=>$kode),'SUPPLIERCODE');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('SUPPLIERCODE',$kode);
            $this->db->update('m_supplier',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_data($kode, $company){
        $kode = trim($this->db->escape_str($kode));
        $status=FALSE;
        
        if(empty($kode)) {
            $status = "Kode supplier CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_supplier',array('SUPPLIERCODE'=>$kode),'SUPPLIERCODE');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==FALSE){
            $this->db->where('SUPPLIERCODE',$kode);
	     	$this->db->delete('m_supplier');
			
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
		  	
			if ($company=='GKM' || $company=='SML' || $company=='SSS'){
				$db_weight = $this->load->database('GKM_SITE', TRUE); 
		  	}else if($company=='LIH'){
				$db_weight = $this->load->database('LIH_SITE', TRUE); 
		  	}else if($company=='MAG'){
				$db_weight = $this->load->database('MAG_SITE', TRUE); 
		  	}else if($company=='SMI'){
				$db_weight = $this->load->database('SSS_SITE', TRUE); 
		  	}else if($company=='TPAI'){
				$db_weight = $this->load->database('PAI_SITE', TRUE); 
		  	}

                $db_weight->where('SUPPLIERCODE',$kode);
                $db_weight->delete('m_supplier');
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
    
    function get_supplier($company,$q){
        $company=trim($this->db->escape_str($company));
        $q=trim($this->db->escape_str($q));
        $query="SELECT SUPPLIERCODE, SUPPLIERNAME 
                FROM m_supplier 
                WHERE COMPANY_CODE='".$company."' AND ACTIVE=1
                    AND SUPPLIERCODE LIKE '%".$q."%'
                GROUP BY SUPPLIERCODE,COMPANY_CODE";
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

    function get_supplier_cbpartner($company){
        $company=trim($this->db->escape_str($company));
        $query="SELECT C_BPARTNER_ID
                FROM m_supplier 
                WHERE COMPANY_CODE='".$company."' AND ACTIVE=1";

        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = "";
        if(!empty($rowcount)){
            $rows_array = $sQuery->result_array(); //array of arrays
            reset($rows_array);
            foreach ( $rows_array as $row => $val)
            {
                //if($temp_result==""){
                //    $temp_result=$row['C_BPARTNER_ID'];
                //}else{

                  if ( end( array_keys( $rows_array ) ) == $row ) {
                    $temp_result = $temp_result.$val['C_BPARTNER_ID'] ;
                  } else {
                    $temp_result = $val['C_BPARTNER_ID'].','.$temp_result;
                  }
                //}
            }
        }
        return $temp_result;
    }
    
    
    function add_new_bpadem($id,$data_post){
        $status=FALSE;

        $cek_data_exist = $this->cek_data_exist('m_supplier',
                    array('SUPPLIERCODE'=>$id),'SUPPLIERCODE');
        if ($cek_data_exist > 0){
            $status='Data Input kode telah ada di database = '.$id;
        }
        
        if(empty($status) || $status==FALSE){
            $this->db->insert( 'm_supplier', $data_post );
                        
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil ".$id;   
            }
        }
        return $status; 
       
    }
    
    function sync_data($kode,$company,$data_post){
        $status=FALSE;
        $kode = trim($this->db->escape_str($kode));
        $company = trim($this->db->escape_str($company));
        $hostname='';
        $password=''; 
        if (strtoupper($company)=='LIH'){
            $hostname="10.88.1.63";
            $password='pr0v1d3ntmysql';
        }elseif(strtoupper($company)=="GKM"){
            $hostname="10.88.1.63";
            $password='pr0v1d3ntmysql';
        }elseif(strtoupper($company)=='MAG'){
            $hostname="10.88.1.63";
            $password='pr0v1d3ntmysql';
        }elseif(strtoupper($company)=='SMI' || strtoupper($company)=='NRP'){
            $hostname="10.88.1.63";
            $password='pr0v1d3ntmysql';
        }elseif(strtoupper($company)=='TPAI'){
            $hostname="10.88.1.63";
            $password='pr0v1d3ntmysql';
        }
        
        $config['hostname'] = $hostname;
        $config['username'] = "root";
        $config['password'] = $password;
        $config['database'] = "timbangan";
        $config['dbdriver'] = "mysqli";
        $config['dbprefix'] = "";
        $config['pconnect'] = TRUE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        
        try{
            $mysql_wb=$this->load->database($config, TRUE); 

            $mysql_wb->select('SUPPLIERCODE');
            $mysql_wb->from('m_supplier');
            $mysql_wb->where(array('SUPPLIERCODE'=>$kode));
            
            $sQuery = $mysql_wb->get();
            $count = $sQuery->num_rows();
            
            if ($count > 0){
                $mysql_wb->where(array('SUPPLIERCODE'=>$kode));
                $mysql_wb->update('m_supplier',$data_post);
                
                if($mysql_wb->trans_status() == FALSE){
                    throw new Exception($mysql_wb->_error_message()) ;//"Error in Transactions!!";
                }                
            }else{        
                $mysql_wb->insert( 'm_supplier', $data_post );
                if($mysql_wb->trans_status() == FALSE){
                    throw new Exception($mysql_wb->_error_message()) ;//"Error in Transactions!!";
                }else{
                    $status="Insert Data Berhasil ";   
                }   
            }  
             
        }catch(Exception $e){
            $status = $e->getMessage();  
        }
        $mysql_wb->close();
        return $status; 
       
    }
}
?>

