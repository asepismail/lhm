<?php
class model_s_po_tbs extends Model{
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
          
        $queries = "SELECT ID_ANON, PO_NUMBER, msup.SUPPLIERCODE, msup.SUPPLIERNAME, QTYORDERED, PRICELIST,TANGGALM, 
                    TANGGALK, DESCRIPTION, potbs.COMPANY_CODE,potbs.SINKRON_STATUS,potbs.C_BPARTNER_ID
                    FROM m_po_tbs potbs
                    LEFT JOIN m_supplier msup ON msup.SUPPLIERCODE = potbs.SUPPLIERCODE and msup.COMPANY_CODE = potbs.COMPANY_CODE
                WHERE potbs.ACTIVE=1 AND potbs.COMPANY_CODE='".$company."'";

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
    
    function get_adem_potbs($company){
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

        //$c_bpartner_id='';
        //if($company=='LIH'){
            $c_bpartner_id = $this->get_supplier_cbpartner($company); //"1012727,1012728,1000423,1012730,1012731,1003922,1012732,1012744,1012743,1012807,1018029,1018267,1018372";    
        //}elseif($company=='GKM'){
            //$c_bpartner_id="1016779,1016781,1016780,1012568,1016874,1016471,1000595,1000149,1012159,1016918,1018373,1018419,1018436,1018435";
        //}
          
        $pgquery="select cord.c_bpartner_id, bp.value, bp.name, cord.documentno, 
                        cordl.qtyordered,cordl.pricelist ,cord.description
                    from c_order cord
                    INNER JOIN c_orderline cordl on cord.c_order_id = cordl.c_order_id
                    INNER JOIN c_bpartner bp on bp.c_bpartner_id = cord.c_bpartner_id
                    INNER JOIN m_product prod on prod.m_product_id = cordl.m_product_id
                    where cord.c_bpartner_id in (".$c_bpartner_id.") 
                    and cord.docstatus='CO' and cordl.qtyordered != cordl.qtydelivered ";
                    //-- and cord.ad_org_id=1000001
       var_dump($pgquery);
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
            array_push($cell, htmlentities($obj->value,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->name,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->documentno,ENT_QUOTES,'UTF-8'));
            array_push($cell, number_format(htmlentities($obj->qtyordered,ENT_QUOTES,'UTF-8')));
            array_push($cell, number_format(htmlentities($obj->pricelist,ENT_QUOTES,'UTF-8')));
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
    
    function update_data($id_anon, $po_number, $data_post){
        $id_anon = trim($this->db->escape_str($id_anon));
        $po_number = trim($this->db->escape_str($po_number));
        $status=FALSE;
        if(empty($id_anon)){
            $status = "id_anon CANNOT BE NULL !!";
        }
        
        if(empty($po_number)) {
            $status = "Nomor PO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_po_tbs',array('PO_NUMBER'=>$po_number),'PO_NUMBER');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_ANON',$id_anon);
            $this->db->where('PO_NUMBER',$po_number);
            $this->db->update('m_po_tbs',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_data($id_anon,$po_number){
        $id_anon = trim($this->db->escape_str($id_anon));
        $po_number = trim($this->db->escape_str($po_number));
        $status=FALSE;
        if(empty($id_anon)){
            $status = "ID ANON CANNOT BE NULL !!";
        }
        
        if(empty($po_number)) {
            $status = "Nomor PO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_po_tbs',array('PO_NUMBER'=>$po_number),'PO_NUMBER');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('ID_ANON',$id_anon);
            $this->db->where('PO_NUMBER',$po_number);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 'm_po_tbs');
            if($this->db->trans_status() == FALSE){
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
    
    
    function add_new_poadem($id,$data_post){
        $status=FALSE;

        $cek_data_exist = $this->cek_data_exist('m_po_tbs',
                    array('PO_NUMBER'=>$id),'PO_NUMBER');
        if ($cek_data_exist > 0){
            $status='Data Input Nomor PO telah ada di database = '.$id;
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 'm_po_tbs', $data_post );
                        
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Insert Data Berhasil ".$id;   
            }
        }
        return $status; 
       
    }
    
    function sync_data($id_anon,$po_number,$company,$data_post){
        $status=FALSE;
        $id_anon = trim($this->db->escape_str($id_anon));
        $po_number = trim($this->db->escape_str($po_number));
        $company = trim($this->db->escape_str($company));
        $hostname='';
        $password=''; 
        if (strtoupper($company)=='LIH'){
            $hostname="10.88.43.54";
            $password='pkslih';
        }elseif(strtoupper($company)=="GKM"){
            $hostname="10.88.22.20";
            $password='pksgkm';
        }elseif(strtoupper($company)=='MAG'){
            $hostname="10.88.41.101";
            $password='pksM4G5224878';
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

            $mysql_wb->select('PO_NUMBER');
            $mysql_wb->from('m_po_tbs');
            $mysql_wb->where(array('SUPPLIERCODE'=>$data_post['SUPPLIERCODE'],'PO_NUMBER'=>$po_number));
            
            $sQuery = $mysql_wb->get();
            $count = $sQuery->num_rows();
            
            if ($count > 0){
                $mysql_wb->where(array('SUPPLIERCODE'=>$data_post['SUPPLIERCODE'],'PO_NUMBER'=>$po_number));
                $mysql_wb->update('m_po_tbs',$data_post);
                
                if($mysql_wb->trans_status() === FALSE){
                    throw new Exception($mysql_wb->_error_message()) ;//"Error in Transactions!!";
                }else{ 
                    $this->db->where(array('ID_ANON'=>$id_anon,'PO_NUMBER'=>$po_number));
                    $this->db->set('SINKRON_STATUS',1);
                    $this->db->update('m_po_tbs');
                      
                    $status="Update data berhasil ";   
                }
                
            }else{
                //query untuk mengatasi bug ketika adanya perubahan harga di tengah2
                //periode po sebelumnya
                $query="SELECT PO_NUMBER FROM m_po_tbs WHERE ACTIVE=1 AND 
                                 SUPPLIERCODE ='". $data_post['SUPPLIERCODE'] ."' AND
                                TANGGALM <= '".date($data_post['TANGGALM'])."' AND
                                 TANGGALK >= '".date($data_post['TANGGALK'])."'";
                $sQuery=$mysql_wb->query($query);
                $rowcount=$sQuery->num_rows();
                if ($rowcount > 0){
                      $mysql_wb->where(array('SUPPLIERCODE'=>$data_post['SUPPLIERCODE'],'TANGGALM <= '=>date($data_post['TANGGALM']),
                                'TANGGALK >= '=>date($data_post['TANGGALK'])));
                      $mysql_wb->set('ACTIVE','0');
                      $mysql_wb->update('m_po_tbs');
                }
                
                
                $temp_result = array();
                if(!empty($rowcount)){
                    foreach ( $sQuery->result_array() as $row )
                    {
                        $temp_result[] = $row;
                    }
                }
        
                $mysql_wb->insert( 'm_po_tbs', $data_post );
                if($mysql_wb->trans_status() === FALSE){
                    throw new Exception($mysql_wb->_error_message()) ;//"Error in Transactions!!";
                }else{
                    $this->db->where(array('ID_ANON'=>$id_anon,'PO_NUMBER'=>$po_number));
                    $this->db->set('SINKRON_STATUS',1);
                    $this->db->update('m_po_tbs');
                    
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

