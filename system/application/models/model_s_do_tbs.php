<?php
class model_s_do_tbs extends Model{
    function __Construct(){
        parent::__Construct();
        $this->load->database();
    }
    
    function LoadData($periode,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $periode=trim($this->db->escape_str($periode));
        $company=trim($this->db->escape_str($company));
          
        $queries = "SELECT ID_ANON, SO_NUMBER ,ID_DO ,C_BPARTNER_ID ,CUSTOMER_NAME ,
                    CUSTOMER_ADDRESS ,QTY_CONTRACT ,JENIS ,ID_JENIS ,COMPANY_CODE ,SINKRON_STATUS
                    FROM m_do_tbs dotbs
                WHERE dotbs.ACTIVE=1 AND dotbs.COMPANY_CODE='".$company."'";

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
            array_push($cell, htmlentities($obj->SO_NUMBER,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CUSTOMER_NAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTY_CONTRACT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SINKRON_STATUS,ENT_QUOTES,'UTF-8'));
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
    
    function get_adem_dotbs($company){
        $company = strtoupper(trim($this->db->escape_str($company)));
        
        $config['hostname'] = "10.88.1.64";
        $config['username'] = "adempiere";
        $config['password'] = "adem5224878";
        $config['database'] = "adempiere";
        $config['dbdriver'] = "postgre";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
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
        }elseif($company=='GKM'){
            $ad_org_id="1000028";
        }elseif($company='MIA'){
            $ad_org_id="1000024";  
        }
          
        $pgquery="Select so.ad_org_id, org.name, so.documentno, so.c_bpartner_id, bp.name as customer, loc.address1,
                soLine.line, soLine.m_product_id, pr.name as namaProduct, prCat.name as productCategory, soLine.qtyordered
                FROM C_Order so
                LEFT JOIN C_OrderLine soLine ON so.c_order_id = soLine.c_order_id
                LEFT JOIN c_bpartner bp ON so.c_bpartner_id = bp.c_bpartner_id
                LEFT JOIN c_bpartner_location bpLoc ON bp.c_bpartner_id = bpLoc.c_bpartner_id
                LEFT JOIN c_location loc ON bpLoc.c_location_id = loc.c_location_id
                LEFT JOIN ad_org org ON so.ad_org_id = org.ad_org_id
                LEFT JOIN m_product pr ON soLine.m_product_id = pr.m_product_id
                LEFT JOIN m_product_category prCat ON pr.m_product_category_id = prCat.m_product_category_id
                WHERE so.issotrx='Y' AND soLine.m_product_id in (1001461,1001460) and so.ad_org_id=".$ad_org_id."";
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
            array_push($cell, htmlentities($obj->ad_org_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->name,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->documentno,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->c_bpartner_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->customer,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->address1,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->line,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->m_product_id,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->namaproduct,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->productcategory,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->qtyordered,ENT_QUOTES,'UTF-8'));
            
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
    
    function update_data($so_number, $data_post){
        $so_number = trim($this->db->escape_str($so_number));
        $status=FALSE;
        
        if(empty($so_number)) {
            $status = "Nomor SO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_do_tbs',array('SO_NUMBER'=>$so_number),'SO_NUMBER');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('SO_NUMBER',$so_number);
            $this->db->update('m_do_tbs',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_data($so_number){
        $so_number = trim($this->db->escape_str($so_number));
        $status=FALSE;
       
        if(empty($so_number)) {
            $status = "Nomor SO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('m_do_tbs',array('SO_NUMBER'=>$so_number),'SO_NUMBER');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status===FALSE){
            
            $this->db->where('SO_NUMBER',$so_number);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 'm_do_tbs');
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
    
    
    function add_new_doadem($id,$data_post){
        $status=FALSE;

        $cek_data_exist = $this->cek_data_exist('m_do_tbs',
                    array('SO_NUMBER'=>$id),'SO_NUMBER');
        if ($cek_data_exist > 0){
            $status='Data Input Nomor DO telah ada di database = '.$id;
        }
        
        if(empty($status) || $status===FALSE){
            $this->db->insert( 'm_do_tbs', $data_post );
                        
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
        }elseif(strtoupper($company)=='MIA'){
            $hostname="10.88.41.101";
            $password='timbanganpksmia';
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
