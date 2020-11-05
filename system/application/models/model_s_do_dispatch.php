<?php
class model_s_do_dispatch extends Model{
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
          
        $queries = "SELECT * FROM s_dispatch_do dotbs WHERE dotbs.ACTIVE=1 AND dotbs.COMPANY_CODE='".$company."'";
        $sql2 = $queries;
       	
		if ($company=='GKM' || $company=='SML' || $company=='SSS'){
			$db_gkm = $this->load->database('GKM_SITE', TRUE); 
		}else if($company=='LIH'){
			$db_gkm = $this->load->database('LIH_SITE', TRUE); 
		}else if($company=='MAG'){
			$db_gkm = $this->load->database('MAG_SITE', TRUE); 
		}else if($company=='SMI'){
			$db_gkm = $this->load->database('SSS_SITE', TRUE); 
		}else if($company=='NRP'){
			$db_gkm = $this->load->database('default', TRUE); 
		}
        if(!$sidx) $sidx =1;
        $query = $db_gkm->query($sql2);
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
        
        //$sql = $queries." ORDER BY ID_DO DESC LIMIT ".$start.",".$limit." "; 
		$sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
			
		$objects = $db_gkm->query($sql,FALSE)->result(); 
        //var_dump($db_gkm);
        $rows =  array();
        $act = "";
        $no = 1; 
        foreach($objects as $obj){
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->C_BPARTNER_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CUSTOMER_NAME,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->CUSTOMER_ADDRESS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->QTY_CONTRACT),ENT_QUOTES,'UTF-8'));	
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));			
			array_push($cell, htmlentities($obj->ID_JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SO_NUMBER,ENT_QUOTES,'UTF-8'));
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
        
        $config['hostname'] = "10.88.1.74";
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
        
        $sql = $pgquery." ORDER BY so.documentno DESC LIMIT ".$limit." OFFSET ".$start; 

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
	
	function search_so($data_search,$company){
		$company = strtoupper(trim($this->db->escape_str($company)));
        
        $config['hostname'] = "10.88.1.74";
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
        }else if($company=='NRP'){
            $ad_org_id="1000060";  
        }
		//
		$limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=trim($this->db->escape_str($company));
        $where = "WHERE so.issotrx='Y' AND soLine.m_product_id in (1001461,1001460) and so.ad_org_id=".$ad_org_id.""; 
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
          
        $queries = "Select so.ad_org_id, org.name, so.documentno, so.c_bpartner_id, bp.name as customer, loc.address1,
                soLine.line, soLine.m_product_id, pr.name as namaProduct, prCat.name as productCategory, soLine.qtyordered
                FROM C_Order so
                LEFT JOIN C_OrderLine soLine ON so.c_order_id = soLine.c_order_id
                LEFT JOIN c_bpartner bp ON so.c_bpartner_id = bp.c_bpartner_id
                LEFT JOIN c_bpartner_location bpLoc ON bp.c_bpartner_id = bpLoc.c_bpartner_id
                LEFT JOIN c_location loc ON bpLoc.c_location_id = loc.c_location_id
                LEFT JOIN ad_org org ON so.ad_org_id = org.ad_org_id
                LEFT JOIN m_product pr ON soLine.m_product_id = pr.m_product_id
                LEFT JOIN m_product_category prCat ON pr.m_product_category_id = prCat.m_product_category_id ".$where;

        $sql2 = $queries;

        if(!$sidx) $sidx =1;
        $query = $pgsql->query($sql2);
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
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord; 
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

        return $jsonObject;
    }
    
    function data_search($data_search,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=trim($this->db->escape_str($company));
        $where = "WHERE dotbs.ACTIVE=1 AND dotbs.COMPANY_CODE='".$company."' "; 
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
          
        $queries = "SELECT * FROM s_dispatch_do dotbs ".$where;

        $sql2 = $queries;
       	
		if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
			$db_gkm = $this->load->database('GKM_SITE', TRUE);
		}else if($company == 'LIH'){
			$db_gkm = $this->load->database('LIH_SITE', TRUE);	
		}else if ($company == 'MAG'){
			$db_gkm = $this->load->database('MAG_SITE', TRUE);	
		}else if ($company == 'SMI' || $company=='NRP'){
			$db_gkm = $this->load->database('SSS_SITE', TRUE);	
		}

        if(!$sidx) $sidx =1;
        $query = $db_gkm->query($sql2);
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
        $objects = $db_gkm->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();			
			array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ID_DO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->C_BPARTNER_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CUSTOMER_NAME,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->CUSTOMER_ADDRESS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(number_format($obj->QTY_CONTRACT),ENT_QUOTES,'UTF-8'));	
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));			
			array_push($cell, htmlentities($obj->ID_JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SO_NUMBER,ENT_QUOTES,'UTF-8'));
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
    
    function update_data($so_number, $data_post, $company){
        $so_number = trim($this->db->escape_str($so_number));
        $status=FALSE;
        
        if(empty($so_number)) {
            $status = "Nomor SO CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',array('SO_NUMBER'=>$so_number),'SO_NUMBER', $company);
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==FALSE){            
			if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
				$db_gkm = $this->load->database('GKM_SITE', TRUE);
			}else if($company == 'LIH'){
				$db_gkm = $this->load->database('LIH_SITE', TRUE);	
			}else if ($company == 'MAG'){
				$db_gkm = $this->load->database('MAG_SITE', TRUE);	
			}else if ($company == 'SMI' || $company=='NRP'){
				$db_gkm = $this->load->database('SSS_SITE', TRUE);	
			}
            $db_gkm->where('SO_NUMBER',$so_number);
            $db_gkm->update('s_dispatch_do',$data_post);
            if($db_gkm->trans_status() == FALSE){
                $status = $db_gkm->_error_message();//"Error in Transactions!!";				
            }else{
				//update data di HO
				$this->db->where('SO_NUMBER',$so_number);
            	$this->db->update('s_dispatch_do',$data_post);	
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete_data($id_do, $company){
        $id_do = ($this->db->escape_str($id_do));
        $status=FALSE;
		       	
		if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
			$db_gkm = $this->load->database('GKM_SITE', TRUE);
		}else if($company == 'LIH'){
			$db_gkm = $this->load->database('LIH_SITE', TRUE);	
		}else if ($company == 'MAG'){
			$db_gkm = $this->load->database('MAG_SITE', TRUE);	
		}else if ($company == 'SMI' || $company=='NRP'){
			$db_gkm = $this->load->database('SSS_SITE', TRUE);	
		}
        $db_gkm->where('ID_DO',$id_do);
		$set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
		$db_gkm->set($set);
        $db_gkm->update('s_dispatch_do');
		
        if($db_gkm->trans_status() == FALSE){
        	$status = $db_gkm->_error_message();//"Error in Transactions!!";				
       	}else{
			//delete data di HO
			$this->db->where('ID_DO',$id_do);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update('s_dispatch_do');
			
			if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data DO : ". $id_do ." Berhasil"."\n";     
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
    
	function cek_data_exist_site($tableName, $where_condition, $select_condition, $company){
		if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
			$db_gkm = $this->load->database('GKM_SITE', TRUE);
		}else if($company == 'LIH'){
			$db_gkm = $this->load->database('LIH_SITE', TRUE);	
		}else if ($company == 'MAG'){
			$db_gkm = $this->load->database('MAG_SITE', TRUE);	
		}else if ($company == 'SMI' || $company=='NRP'){
			$db_gkm = $this->load->database('SSS_SITE', TRUE);	
		}
        $db_gkm->select($select_condition);
        $db_gkm->from($tableName);
        $db_gkm->where($where_condition);
        
        $sQuery = $db_gkm->get();
        $count = $sQuery->num_rows();
           
        return $count;
    }
		
	function add_new($company, $so_number, $do_number, $data_post){
        $status=FALSE;
		
        $cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                    array('SO_NUMBER'=>$so_number,'ACTIVE'=>1),'SO_NUMBER', $company);
        if ($cek_data_exist > 0){
            $status='Data Input Nomor SO telah ada di database = '.$so_number;
        }
		
		$cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                    array('ID_DO'=>$do_number,'ACTIVE'=>1),'ID_DO', $company);
        if ($cek_data_exist > 0){
            $status='Data Input Nomor DO telah ada di database = '.$do_number;
        }
        
        if(empty($status) || $status==FALSE){
			
			if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
				$db_gkm = $this->load->database('GKM_SITE', TRUE);
			}else if($company == 'LIH'){
				$db_gkm = $this->load->database('LIH_SITE', TRUE);	
			}else if ($company == 'MAG'){				
				$db_gkm = $this->load->database('MAG_SITE', TRUE);	
			}else if ($company == 'SMI' || $company=='NRP'){				
				$db_gkm = $this->load->database('SSS_SITE', TRUE);	
			}
			
            $db_gkm->insert('s_dispatch_do', $data_post);
                        
            if($db_gkm->trans_status() == FALSE){
                $status = $db_gkm->_error_message();//"Error in Transactions!!";
            }else{
				$cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                array('ID_DO'=>$do_number),'ID_DO', $company);
				if ($cek_data_exist > 0){
					$status="Insert Data Berhasil ".$do_number;
				}else{
                	$status="Insert Data Gagal ".$do_number; 
				}
            }
        }
        return $status;   
    }
	
	function create($company, $so_number, $do_number, $data_post){
        $status=FALSE;
		
        $cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                    array('SO_NUMBER'=>$so_number,'ACTIVE'=>1),'SO_NUMBER', $company);
        if ($cek_data_exist > 0){
            $status='Data Input Nomor SO telah ada di database = '.$so_number;
        }
		
		$cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                    array('ID_DO'=>$do_number, 'ACTIVE'=>1),'ID_DO', $company);
        if ($cek_data_exist > 0){
            $status='Data Input Nomor DO telah ada di database = '.$do_number;
        }
        
        if(empty($status) || $status==FALSE){
			
			if ($company == 'GKM' || $company == 'SML' || $company =='SSS'){
				$db_gkm = $this->load->database('GKM_SITE', TRUE);
			}else if($company == 'LIH'){
				$db_gkm = $this->load->database('LIH_SITE', TRUE);	
			}else if ($company == 'MAG'){				
				$db_gkm = $this->load->database('MAG_SITE', TRUE);	
			}else if ($company == 'SMI' || $company=='NRP'){				
				$db_gkm = $this->load->database('SSS_SITE', TRUE);	
			}
			
            $db_gkm->insert('s_dispatch_do', $data_post);
                        
            if($db_gkm->trans_status() == FALSE){
                $status = $db_gkm->_error_message();//"Error in Transactions!!";
            }else{
				$cek_data_exist = $this->cek_data_exist_site('s_dispatch_do',
                array('ID_DO'=>$do_number),'ID_DO', $company);
				if ($cek_data_exist > 0){
					$status="Insert Data Berhasil ".$do_number;   
				}else{
                	$status="Insert Data Gagal ".$do_number; 
				}
                
            }
        }
        return $status;   
    }
	
	function get_cbpartner($q,$company){
        $company=trim($this->db->escape_str($company));
        $cbpartner=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
		
		$config['hostname'] = "10.88.1.74";
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
		
		$query="SELECT c_bpartner.c_bpartner_id, c_bpartner.value, c_bpartner.name, address1 ||' ' || city AS address1, address2 ||' ' || city AS address2 
FROM c_bpartner
LEFT JOIN c_bpartner_location ON c_bpartner.c_bpartner_id = c_bpartner_location.c_bpartner_id
LEFT JOIN c_location ON c_bpartner_location.c_location_id = c_location.c_location_id
WHERE LOWER(c_bpartner.name) LIKE LOWER('%".$cbpartner."%') AND c_bpartner.ad_org_id IN (0,'".$company."')"; 
        $sQuery = $pgsql->query($query);		
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
