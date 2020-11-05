<?php
class model_s_ba_afkir extends Model{
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
        $queries = "SELECT ID_BA, NO_BA,  BA_DATE, COMPANY_CODE, DESCRIPTION, 
CASE WHEN `STATUS` = 1 THEN 'APPROVED' ELSE 'WAITING APPROVAL' END AS `STATUS` FROM s_ba_afkir WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' AND DATE_FORMAT(BA_DATE,'%Y%m')='".$periode."'";
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
			array_push($cell, htmlentities($obj->ID_BA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_BA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BA_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->STATUS,ENT_QUOTES,'UTF-8'));
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
    
    function LoadData_Detail($company,$id_nota){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $id_nota=trim($this->db->escape_str($id_nota));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT ID, ID_BA, LEFT(BLOCK,2) AS AFD, TANGGAL_PANEN, BLOCK, JANJANG, DESCRIPTION FROM s_ba_afkir_detail WHERE ID_BA = '".$id_nota."'";

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
			array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_BA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL_PANEN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JANJANG,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));            
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
        $where = "WHERE nab.ACTIVE=1 AND nab.COMPANY_CODE = '".$company."' "; 
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
                $where .=" AND nab.".trim($this->db->escape_str($data_search[$i]['field']))." $operator '%".trim($this->db->escape_like_str($data_search[$i]['data']))."%'";   
            }else{
               $where .=" AND nab.".trim($this->db->escape_str($data_search[$i]['field']))." $operator '".trim($this->db->escape_str($data_search[$i]['data']))."'"; 
            }           
        }
        $queries ="SELECT nab.ID_NT_AB, TANGGAL, nab.NO_KENDARAAN,
                                NAMA_SUPIR, nab.NO_TIKET, nab.NO_SPB, nab.COMPANY_CODE, t.BERAT_BERSIH, t.FLAG_TIMBANGAN from s_nota_angkutbuah nab
                    LEFT JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB
                    LEFT JOIN s_data_timbangan t ON nab.NO_SPB = t.NO_SPB ".$where;
            
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
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_SUPIR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FLAG_TIMBANGAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
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
    
    function get_afdeling($company,$q){
        $company=trim($this->db->escape_str($company));
        $q=trim($this->db->escape_str($q));
        $query="SELECT LEFT(LOCATION_CODE,2) AS AFD FROM m_location WHERE COMPANY_CODE='".$company."' AND LOCATION_TYPE_CODE='OP' AND LOCATION_CODE LIKE '%".$q."%' GROUP BY AFD,COMPANY_CODE";

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
    
    function get_block($company,$location_left,$date,$location){
        $company=trim($this->db->escape_str($company));
        $location_left=trim($this->db->escape_str($location_left)); 
        $location=trim($this->db->escape_str($location)); 
        /*
		Remarked by Asep, 20130822
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$location_left."' 
                AND LOCATION_CODE LIKE '%".$location."%' AND LOCATION_TYPE_CODE='OP'";
				*/
		if ($company=='GKM' || $company=='SML'){
			$query="SELECT DISTINCT(mag.LOCATION_CODE) AS LOCATION_CODE, loc.DESCRIPTION FROM dummy_mgangactivitydetail_gkm mag
LEFT JOIN 
	(
		SELECT LOCATION_CODE, DESCRIPTION 
	 	FROM m_location WHERE COMPANY_CODE = '".$company."' AND LOCATION_TYPE_CODE='OP'
	 ) loc ON mag.LOCATION_CODE=loc.LOCATION_CODE 
WHERE mag.COMPANY_CODE = '".$company."' 
AND mag.ACTIVITY_CODE ='8601003' 
AND LHM_DATE = DATE_FORMAT('".$date."','%Y%m%d')
AND LEFT(mag.LOCATION_CODE,2) = '".$location_left."'
AND mag.LOCATION_CODE LIKE '%".$location."%'";
		}else{
		$query="SELECT DISTINCT(mag.LOCATION_CODE) AS LOCATION_CODE, loc.DESCRIPTION FROM m_gang_activity_detail mag
LEFT JOIN 
	(
		SELECT LOCATION_CODE, DESCRIPTION 
	 	FROM m_location WHERE COMPANY_CODE = '".$company."' AND LOCATION_TYPE_CODE='OP'
	 ) loc ON mag.LOCATION_CODE=loc.LOCATION_CODE 
WHERE mag.COMPANY_CODE = '".$company."' 
AND mag.ACTIVITY_CODE ='8601003' 
AND LHM_DATE = DATE_FORMAT('".$date."','%Y%m%d')
AND LEFT(mag.LOCATION_CODE,2) = '".$location_left."'
AND mag.LOCATION_CODE LIKE '%".$location."%'";
		}

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
    
    function get_no_kendaraan($q,$company){
        $company=trim($this->db->escape_str($company));
        $no_kend=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
        
        $query="SELECT NAMA_KONTRAKTOR, NO_KENDARAAN FROM m_kontraktor kont
                LEFT JOIN m_kontraktor_kendaraan kend ON kend.KODE_KONTRAKTOR = kont.KODE_KONTRAKTOR
                    AND kend.COMPANY_CODE = kont.COMPANY_CODE
                WHERE REPLACE(kend.NO_KENDARAAN,' ','') LIKE '%".$no_kend."%' AND kont.COMPANY_CODE ='".$company."' 
                ORDER BY kont.KODE_KONTRAKTOR ASC ";
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
	
	function get_supplier($supplier,$company){
        $company=trim($this->db->escape_str($company));
        $supplier=strtoupper(str_replace(" ","",trim($this->db->escape_str($supplier))));
        $query="SELECT s.SUPPLIERCODE, s.SUPPLIERNAME FROM m_supplier s WHERE COMPANY_CODE='".$company."' AND SUPPLIERCODE LIKE '%".$supplier."%'";
		
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
    
    function get_no_spb($q,$company,$tanggalm){
        $company=trim($this->db->escape_str($company));
        $tanggalm=trim($this->db->escape_str($tanggalm));
        $no_spb=strtoupper(str_replace(" ","",trim($this->db->escape_str($q))));
        
	/*## Hanya ambil data SPB yang belum ter-input di table s_nota_angkutbuah
		untuk membantu validasi duplicate data input
		##*/
        //$query="SELECT NO_SPB, NO_KENDARAAN, DRIVER_NAME FROM s_data_timbangan WHERE REPLACE(NO_SPB,' ','') LIKE '%".$no_spb."%' AND COMPANY_CODE ='".$company."' AND TANGGALM='".$tanggalm."'	AND NO_SPB NOT IN (SELECT NO_SPB FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND TANGGAL='".$tanggalm."') ORDER BY NO_TIKET ASC "; //remarked by Asep, 20130506
		$query="SELECT NO_SPB, NO_KENDARAAN, DRIVER_NAME, BERAT_BERSIH, FLAG_TIMBANGAN FROM s_data_timbangan WHERE REPLACE(NO_SPB,' ','') LIKE '%".$no_spb."%' AND COMPANY_CODE ='".$company."' AND TANGGALM='".$tanggalm."'	AND NO_SPB NOT IN (SELECT NO_SPB FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND TANGGAL='".$tanggalm."') AND s_data_timbangan.ACTIVE= 1 ORDER BY NO_TIKET ASC "; //modified by Asep, 20130506, added BERAT_BERSIH
		
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
    
    function lokasi_validate($location,$company){
        $company = trim($this->db->escape_str($company));
        $location = trim($this->db->escape_str($location));
        
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' 
                AND LOCATION_CODE = '".$location."' AND LOCATION_TYPE_CODE='OP'"; 
        $sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        return $rowcount;    
    }
	
	function lokasitgl_validate($date,$location,$company){
        $company = trim($this->db->escape_str($company));
        $location = trim($this->db->escape_str($location));
		
		
		if($company=='GKM' || $company =='SML'){
			$query ="SELECT DISTINCT(dummy_mgangactivitydetail_gkm.LOCATION_CODE) AS LOCATION_CODE, m_location.DESCRIPTION
FROM dummy_mgangactivitydetail_gkm
LEFT JOIN ( SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_TYPE_CODE = 'OP'
		AND COMPANY_CODE = '".$company."' GROUP BY LOCATION_CODE ) m_location ON m_location.LOCATION_CODE = dummy_mgangactivitydetail_gkm.LOCATION_CODE 
WHERE dummy_mgangactivitydetail_gkm.COMPANY_CODE = '".$company."'
AND LHM_DATE = DATE_FORMAT('".$date."','%Y%m%d')
AND dummy_mgangactivitydetail_gkm.LOCATION_CODE = '".$location."'
AND ACTIVITY_CODE IN ('8601003')
ORDER BY dummy_mgangactivitydetail_gkm.LOCATION_CODE";			
		}else{
			$query ="SELECT DISTINCT(m_gang_activity_detail.LOCATION_CODE) AS LOCATION_CODE, m_location.DESCRIPTION
FROM m_gang_activity_detail
LEFT JOIN ( SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_TYPE_CODE = 'OP'
		AND COMPANY_CODE = '".$company."' GROUP BY LOCATION_CODE ) m_location ON m_location.LOCATION_CODE = m_gang_activity_detail.LOCATION_CODE 
WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."'
AND LHM_DATE = DATE_FORMAT('".$date."','%Y%m%d')
AND m_gang_activity_detail.LOCATION_CODE = '".$location."'
AND ACTIVITY_CODE IN ('8601003')
ORDER BY m_gang_activity_detail.LOCATION_CODE";			
		}

        $sQuery = $this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        return $rowcount;    
    }
    
    function add_new($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        $company = $this->db->escape_str($company);
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ba_afkir',
                    array('NO_BA'=>$id),'NO_BA');
	//var_dump($cek_data_exist);	
        if ($cek_data_exist > 0){
			$return['status']='Data Input ID telah ada di database = '.$id;
        	$return['error']=true;
        }
        
		if(empty($return['status']) && $return['error'] == false){  
            $this->db->insert('s_ba_afkir', $data_post );
                        
            if($this->db->trans_status() == FALSE){
                $return['status'] = $this->db->_error_message();
        		$return['error']=true;
            }else{
				$return['status'] = "Insert Data Berhasil ".$id;
        		$return['error']=false;
            }
        }
		return $return; 
    }
    
    function add_new_detail($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert('s_ba_afkir_detail', $data_post[$keys] );                                 
            }
            if($this->db->trans_status() == FALSE){
				$return['status']=$this->db->_error_message();
        		$return['error']=true;
           	}else{
				$return['status']="Insert Data Berhasil"."\n";
        		$return['error']=false;
           	} 
        }else{
            $return['status']="data tidak lengkap"."\n";
        	$return['error']=true;
        }
        return $return;
    }
    
    function update_data($id,$company,$data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));
        
        $notastatus='0';
        $closingstatus='';
        $qCekStatus= "SELECT STATUS FROM s_ba_afkir WHERE ID_BA='".$id."'";
        $cek_nota_status=$this->db->query($qCekStatus);
        if($cek_nota_status->num_rows() > 0){
            $row_data = $cek_nota_status->row();
            $notastatus=$row_data->STATUS; 
            $closingstatus=$row_data->STATUS;  
        }
        
        if($notastatus!=1){
            if($closingstatus=1){
                $this->db->where('ID_BA',$id);
                $this->db->where('COMPANY_CODE',$company);
                $this->db->update('s_ba_afkir', $data );
                if($this->db->trans_status() == FALSE){
                    $status = $this->db->_error_message();
					$return['status']=$status;
        			$return['error']=true;
                }else{
                    $status="Update Data ID Berhasil"."\n";
					$return['status']=$status;
        			$return['error']=false;
                }        
            }else{
                $status="Data Closing Tidak dapat di Update..!!"."\n"; 
				$return['status']=$status;
        		$return['error']=true;
            }
            
        }else{
            $status="Data Tidak dapat di Update..!!"."\n";  
			$return['status']=$status;
        	$return['error']=true;
        }         
		return $return;
    }
    
    function update_detail($id,$company,$data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));
        
        if(empty($id)){
            $status = "ID NOTA CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
                
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
		if(empty($return['status']) && $return['error'] == false){  		
            $notastatus='0';
            $qCekStatus= "SELECT STATUS FROM s_ba_afkir WHERE ID_BA='".$id."'";
            $cek_nota_status=$this->db->query($qCekStatus);
            if($cek_nota_status->num_rows() > 0){
                $row_data = $cek_nota_status->row();
                $notastatus=$row_data->STATUS;   
            }
        
            if($notastatus!=1){
                $this->db->where('ID_BA', $id );      
                $this->db->delete('s_ba_afkir_detail'); 
                    foreach($data as $keys => $vals){
                        $this->db->insert('s_ba_afkir_detail', $data[$keys] );                                 
                    }
                    if($this->db->trans_status() == FALSE){
                        $status = $this->db->_error_message();
                    }else{
                        $status="Update Data detail Berhasil"."\n";  
						$return['status']=$status;
        				$return['error']=false;
                    }    
            }else{
                $status="Data Tidak dapat di Update..!!"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }
		return $return; 
    }
    
    function update_nota_detail($id_anon,$id_nt_ab,$data_post){
        $id_anon = trim($this->db->escape_str($id_anon));
        $id_nt_ab = trim($this->db->escape_str($id_nt_ab));
        $status=FALSE;
        if(empty($id_nt_ab)){
            $status = "ID NOTA BUAH CANNOT BE NULL !!";
        }
 
        if(empty($status) || $status==false){
            
            $this->db->where('ID_ANON',$id_anon);
            $this->db->where('ID_NT_AB',$id_nt_ab);
            $this->db->update('s_nota_angkutbuah_detail',$data_post);
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Update Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    }
    
    function delete($id_ba,$no_ba,$company){
        $id_ba = trim($this->db->escape_str($id_ba));
        $no_ba = trim($this->db->escape_str($no_ba));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if((!empty($id_ba) && $id_ba==false)){
            $status = "ID_BA CANNOT BE NULL !!";
        }
        
        if((!empty($company) && $company==false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ba_afkir',array('ID_BA'=>$id_ba,'NO_BA'=>$no_ba,'COMPANY_CODE'=>$company),'ID_BA');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            //$this->db->where('ID_BA',$id_ba);
            //$this->db->delete('s_ba_afkir');
			
            //$delQ="delete from s_ba_afkir_detail where ID_BA = '".$id_ba."'";
            //$queryD = $this->db->query($delQ);
			
			$delQ="UPDATE s_ba_afkir SET ACTIVE = 0 where ID_BA = '".$id_ba."'";
            $this->db->query($delQ);
			
			$delQ2="UPDATE s_ba_afkir_detail SET ACTIVE = 0 where ID_BA = '".$id_ba."'";
            $this->db->query($delQ2);
            
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        return $status;
    }
    
    function delete_detail($id,$id_ba){
        $id = trim($this->db->escape_str($id));
        $id_ba = trim($this->db->escape_str($id_ba));
        
        $status=FALSE;
        if((!empty($id) && $id==false)){
            $status = "ID_NOTA CANNOT BE NULL !!";
        }
 
        if(empty($status) || $status==false){
            
            $notastatus = $this->cek_nota_status('',$id_ba);
            if($notastatus!=1){
                $this->db->where('ID',$id);
                $this->db->delete('s_ba_afkir_detail');
                
                if($this->db->trans_status()== FALSE){
                    $status = $this->db->_error_message();;
                }else{
					$status=true;
                    $status="Delete Data Berhasil"."\n"; 				
                }   
            }else{
				$status="Delete baris berhasil"."\n";
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
    
    function cek_nota_status($no_tiket='',$id_ba){
        $no_tiket = $this->db->escape_str($no_tiket);
        $id_ba = $this->db->escape_str($id_ba);
        $status ='';
        
        $query="SELECT STATUS FROM s_ba_afkir WHERE ID_BA='".$id_ba."'";
        $sQuery = $this->db->query($query);
        
        $rowcount = $sQuery->num_rows();
        if($rowcount > 0){
            $row = $sQuery->row_array();
            $status = $row['STATUS'];    
        }elseif($rowcount<=0){
            $status = 1;
        }
        
        return $status;
    }
    
    function search_spb($spb, $no_kend,$periode, $company){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
        if (isset($spb)){
            $spb = htmlentities($spb,ENT_QUOTES,'UTF-8');
        } else {
            $spb = "";
        }
        
        if (isset($no_kend)){
            $no_kend = htmlentities($no_kend,ENT_QUOTES,'UTF-8');
        } else {
            $no_kend = "";
        }
        
        $where = "WHERE 1=1"; 
        if($spb!='' && $spb!='-') $where.= " AND NO_SPB LIKE '%$spb%'"; 
        if($no_kend!='' && $no_kend!='-') $where.= " AND NO_KENDARAAN LIKE '%$no_kend%'";       
        $where .= " AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(TANGGAL,'%Y%m')='".$periode."' AND ACTIVE=1";
        
        $queries = "SELECT * FROM s_nota_angkutbuah ". $where;
                    
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
        
        if( $count >0 ) {
            $sql =  $queries." ORDER BY ID_NT_AB ASC LIMIT ".$start.",".$limit."";
        } else {
            $sql =  $queries;     
        }
        //$sql = "select * FROM m_employee ".$where." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act = "";
        $no = 1;                           
        foreach($objects as $obj){
            $cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_SUPIR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			//array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8')); //Added by Asep, 20130507
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
	
	function approve_ba($id_ba,$company, $ba_date){
        $id_ba = trim($this->db->escape_str($id_ba));
        $company = trim($this->db->escape_str($company));
		$ba_date = trim($this->db->escape_str($ba_date));
        $status=FALSE;
        
        if(empty($id_ba)){
            $status = "id_ba CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ba_afkir',array('ID_BA'=>$id_ba,'BA_DATE'=>$ba_date),'ID_BA');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==FALSE){            
            $this->db->where('ID_BA',$id_ba);
			$this->db->where('BA_DATE',$ba_date);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('APPROVED_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'APPROVED_TIME' =>  $this->global_func->gen_datetime(),
                    'STATUS'=>1
                    );
            $this->db->set($set);
            $this->db->update('s_ba_afkir');   
			
			$qApprove="UPDATE s_ba_afkir_detail SET STATUS = 1 where ID_BA = '".$id_ba."'";
            $this->db->query($qApprove);
			
			$status="Approve berita acara tanggal " . $ba_date .  " berhasil"."\n";  
        }
        
        return $status;
    }
}
?>
