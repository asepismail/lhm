<?php
class model_s_nota_angkut extends Model{
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
        //$queries = "SELECT * FROM s_nota_angkutbuah WHERE ACTIVE=1 AND COMPANY_CODE='".$company."' AND DATE_FORMAT(TANGGAL,'%Y%m')='".$periode."'"; //remarked by Asep, 20130507
		
		$flag_wp = $this->Model_m_company->info_weighbridge_sta($company);
		//if ($flag_wp == "0"){
			//$queries = "SELECT s_nota_angkutbuah.*, '' AS BERAT_BERSIH FROM s_nota_angkutbuah WHERE s_nota_angkutbuah.ACTIVE=1 AND s_nota_angkutbuah.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_nota_angkutbuah.TANGGAL,'%Y%m')='".$periode."'"; 
		//}else{
			if ($company<>"ASL"){
				if ($company=="MSS"){
					$queries = "SELECT s_nota_angkutbuah.*, (s_data_timbangan_kebun.BERAT_ISI - s_data_timbangan_kebun.BERAT_KOSONG) AS BERAT_BERSIH, s_data_timbangan_kebun.FLAG_TIMBANGAN FROM s_nota_angkutbuah INNER JOIN s_data_timbangan_kebun ON s_nota_angkutbuah.NO_SPB = s_data_timbangan_kebun.NO_SPB WHERE s_nota_angkutbuah.ACTIVE=1 AND s_nota_angkutbuah.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_nota_angkutbuah.TANGGAL,'%Y%m')='".$periode."' AND s_data_timbangan_kebun.NO_SPB NOT LIKE '%PKS%' 
AND s_data_timbangan_kebun.NO_SPB NOT LIKE '%PABRIK%'"; 
				}else{
					$queries = "SELECT s_nota_angkutbuah.*, (s_data_timbangan.BERAT_ISI - s_data_timbangan.BERAT_KOSONG) AS BERAT_BERSIH, s_data_timbangan.FLAG_TIMBANGAN FROM s_nota_angkutbuah INNER JOIN s_data_timbangan ON s_nota_angkutbuah.NO_SPB = s_data_timbangan.NO_SPB WHERE s_nota_angkutbuah.ACTIVE=1 AND s_nota_angkutbuah.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_nota_angkutbuah.TANGGAL,'%Y%m')='".$periode."'"; 
				}
			}
		//}

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
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA_SUPIR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_TIKET,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BERAT_BERSIH,ENT_QUOTES,'UTF-8')); //Added by Asep, 20130507
			array_push($cell, htmlentities($obj->FLAG_TIMBANGAN,ENT_QUOTES,'UTF-8')); //Added by Asep, 20130828
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
    
    function LoadData_Detail($company,$id_nota){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        //$no_tiket=trim($this->db->escape_str($no_tiket));
        $id_nota=trim($this->db->escape_str($id_nota));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT ID_ANON,ID_NT_AB,AFD,BLOCK,JANJANG,TANGGAL_PANEN, OVERRIPE, AFKIR, BRONDOLAN, DESCRIPTION FROM s_nota_angkutbuah_detail WHERE ID_NT_AB ='".$id_nota."'";
		
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
            array_push($cell, htmlentities($obj->ID_ANON,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGGAL_PANEN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JANJANG,ENT_QUOTES,'UTF-8'));            
			array_push($cell, htmlentities($obj->OVERRIPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->AFKIR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BRONDOLAN,ENT_QUOTES,'UTF-8'));
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
		if ($company<>"ASL"){
			if ($company =="MSS"){
				$queries ="SELECT DISTINCT nab.ID_NT_AB, TANGGAL, nab.NO_KENDARAAN,
									NAMA_SUPIR, nab.NO_TIKET, nab.NO_SPB, nab.COMPANY_CODE, (t.BERAT_ISI - t.BERAT_KOSONG) AS BERAT_BERSIH, t.FLAG_TIMBANGAN from s_nota_angkutbuah nab
						LEFT JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB
						LEFT JOIN s_data_timbangan_kebun t ON nab.NO_SPB = t.NO_SPB ".$where;	
			}else{
				$queries ="SELECT DISTINCT nab.ID_NT_AB, TANGGAL, nab.NO_KENDARAAN,
									NAMA_SUPIR, nab.NO_TIKET, nab.NO_SPB, nab.COMPANY_CODE, (t.BERAT_ISI - t.BERAT_KOSONG) AS BERAT_BERSIH, t.FLAG_TIMBANGAN from s_nota_angkutbuah nab
						LEFT JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB
						LEFT JOIN s_data_timbangan t ON nab.NO_SPB = t.NO_SPB ".$where;
			}
		}
            
        $sql2 = $queries;
        //var_dump($sql2);
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
    
    function get_block($company,$location_left,$location){
        $company=trim($this->db->escape_str($company));
        //$location_left=trim($this->db->escape_str($location_left)); Remarked by Asep, 20130822
        $location=trim($this->db->escape_str($location)); 
        /*
		Remarked by Asep, 20130822
        $query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."' AND LEFT(LOCATION_CODE,2)='".$location_left."' 
                AND LOCATION_CODE LIKE '%".$location."%' AND LOCATION_TYPE_CODE='OP'";
				*/
		$query="SELECT LOCATION_CODE,DESCRIPTION
                FROM m_location 
                WHERE COMPANY_CODE='".$company."'  
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
		if ($company<>"ASL"){
			if ($company == "MSS"){
				$query="SELECT NO_SPB, NO_KENDARAAN, DRIVER_NAME, (BERAT_ISI-BERAT_KOSONG) AS BERAT_BERSIH, FLAG_TIMBANGAN FROM s_data_timbangan_kebun WHERE REPLACE(NO_SPB,' ','') LIKE '%".$no_spb."%' AND COMPANY_CODE ='".$company."' AND TANGGALM='".$tanggalm."'	AND NO_SPB NOT IN (SELECT NO_SPB FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND TANGGAL='".$tanggalm."') AND s_data_timbangan_kebun.ACTIVE= 1 ORDER BY NO_TIKET ASC "; //modified by Asep, 20130506, added BERAT_BERSIH			
			}else{
				$query="SELECT NO_SPB, NO_KENDARAAN, DRIVER_NAME, (BERAT_ISI-BERAT_KOSONG) AS BERAT_BERSIH, FLAG_TIMBANGAN FROM s_data_timbangan WHERE REPLACE(NO_SPB,' ','') LIKE '%".$no_spb."%' AND COMPANY_CODE ='".$company."' AND TANGGALM='".$tanggalm."'	AND NO_SPB NOT IN (SELECT NO_SPB FROM s_nota_angkutbuah WHERE COMPANY_CODE ='".$company."' AND TANGGAL='".$tanggalm."') AND s_data_timbangan.ACTIVE= 1 ORDER BY NO_TIKET ASC "; //modified by Asep, 20130506, added BERAT_BERSIH
			}
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
    
    function add_new($id, $company, $data_post, $flag_wb){
        //$status=FALSE; 
		$return['status']='';
        $return['error']=false;
        $company = $this->db->escape_str($company);
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        /*## Cek jika data timbang telah tersinkron apa belum
	   ## if (!sinkron){tidak boleh insert}
	   ## else {do_insert}
		*/
		//Modified by Asep, validasi ini khusus untuk perusahaan yang punya timbangan
		//$flag_wb == "1" => perusahaan ada timbangan 
		//$flag_wb == "0" => perusahaan tidak ada timbangan
	 	//if ($flag_wb == "1"){ 
			if ($company<>"ASL"){
				if ($company=="MSS"){
					$cek_data_exist = $this->cek_data_exist('s_data_timbangan_kebun',
											array('NO_SPB'=>$data_post['NO_SPB']),'NO_SPB');
				}else{
					$cek_data_exist = $this->cek_data_exist('s_data_timbangan',
											array('NO_SPB'=>$data_post['NO_SPB']),'NO_SPB');
				}
			}
			if ($cek_data_exist <= 0){
				$return['status']='Data SPB belum ada di database = '.$data_post['NO_SPB'];
        		$return['error']=true;
			} 
			$cek_data_exist = $this->cek_data_exist('s_nota_angkutbuah',
						array('NO_SPB'=>$data_post['NO_SPB']),'NO_SPB');

			if ($cek_data_exist > 0){
				//$status='Data Input NO SPB telah ada di database';
				$return['status']='Data Input NO SPB telah ada di database';
        		$return['error']=true;
			}
       	//}	

        /*## Meyakinkan proses validasi pada 'function get_no_spb'
		##*/
        $cek_data_exist = $this->cek_data_exist('s_nota_angkutbuah',
                    array('ID_NT_AB'=>$id),'ID_NT_AB');
        if ($cek_data_exist > 0){
            //$status='Data Input ID telah ada di database = '.$id;
			$return['status']='Data Input ID telah ada di database = '.$id;
        	$return['error']=true;
        }
        
		
        /*## End Duplicate Input validation */

        //if(empty($status) || $status===FALSE){
		if(empty($return['status']) && $return['error'] === false){  
            $this->db->insert( 's_nota_angkutbuah', $data_post );
                        
            if($this->db->trans_status() === FALSE){
                $return['status'] = $this->db->_error_message();//"Error in Transactions!!";
        		$return['error']=true;
            }else{
                //$status="Insert Data Berhasil ".$id;   
				$return['status'] = "Insert Data Berhasil ".$id;
        		$return['error']=false;
            }
        }
        //return $status;
		return $return; 
       
    }
    
    function add_new_detail($id, $no_spb, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_nota_angkutbuah_detail', $data_post[$keys] );                                 
            }
            if($this->db->trans_status() === FALSE){
				$return['status']=$this->db->_error_message();
        		$return['error']=true;
           	}else{
				$return['status']="Insert Data Berhasil"."\n";
        		$return['error']=false;
           	} 
		$this->round_tonase($no_spb, $company);
        }else{
            $return['status']="data tidak lengkap"."\n";
        	$return['error']=true;
        }
        return $return;
    }
    
    function update_data($id,$no_tiket,$company,$data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $no_tiket = trim($this->db->escape_str($no_tiket));
        $company = trim($this->db->escape_str($company));
        //$status=FALSE;
        
        $notastatus='0';
        $closingstatus='';
        $qCekStatus= "SELECT STATUS,CLOSING_STATUS FROM s_nota_angkutbuah WHERE ID_NT_AB='".$id."'";
        $cek_nota_status=$this->db->query($qCekStatus);
        if($cek_nota_status->num_rows() > 0){
            $row_data = $cek_nota_status->row();
            $notastatus=$row_data->STATUS; 
            $closingstatus=$row_data->CLOSING_STATUS;  
        }
        
        if($notastatus!=1){
            if($closingstatus=1){
                $this->db->where('ID_NT_AB',$id);
                $this->db->where('COMPANY_CODE',$company);
                $this->db->update('s_nota_angkutbuah', $data );
                if($this->db->trans_status() === FALSE){
                    $status = $this->db->_error_message();//"Error in Transactions!!";
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

        //return $status;  
		return $return;
    }
    
    function update_detail($id,$no_tiket,$company,$data){
        //$status=FALSE;
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $no_tiket = trim($this->db->escape_str($no_tiket));
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
        
        //if($status==FALSE){
		if(empty($return['status']) && $return['error'] == false){  
		
            $notastatus='0';
            $qCekStatus= "SELECT STATUS FROM s_nota_angkutbuah WHERE ID_NT_AB='".$id."'";
            $cek_nota_status=$this->db->query($qCekStatus);
            if($cek_nota_status->num_rows() > 0){
                $row_data = $cek_nota_status->row();
                $notastatus=$row_data->STATUS;   
            }
        
            if($notastatus!=1){
                $this->db->where('ID_NT_AB', $id );      
                $this->db->delete('s_nota_angkutbuah_detail');  
                //if(count($data) > 0){
                    foreach($data as $keys => $vals)
                    {
                        $this->db->insert( 's_nota_angkutbuah_detail', $data[$keys] );                                 
                    }
                    if($this->db->trans_status() === FALSE){
                        $status = $this->db->_error_message();//"Error in Transactions!!";
                    }else{
                        $status="Update Data detail Berhasil"."\n";  
						$return['status']=$status;
        				$return['error']=false;
                    }    
                //}
		  $this->round_tonase($no_tiket, $company);  
            }else{
                $status="Data Tidak dapat di Update..!!"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }
        //return $status;    
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
    
    function delete_nota($id_nota,$plat_no,$no_spb,$company){
        $id_nota = trim($this->db->escape_str($id_nota));
        $plat_no = str_replace(" ","",strtoupper(trim($this->db->escape_str($plat_no)))) ;
        $no_spb = trim($this->db->escape_str($no_spb));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        if((!empty($id_nota) && $id_nota==false)){
            $status = "ID_NOTA CANNOT BE NULL !!";
        }
        
        if((!empty($company) && $company==false)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_nota_angkutbuah',array('ID_NT_AB'=>$id_nota,'COMPANY_CODE'=>$company),'ID_NT_AB');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==false){
            
            $this->db->where('ID_NT_AB',$id_nota);
            $this->db->delete('s_nota_angkutbuah');
            /*$this->db->where('REPLACE(NO_KENDARAAN," ","")',$plat_no);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->set('ACTIVE',0);
            $this->db->update('s_nota_angkutbuah' );*/
            
            $delQ="delete from s_data_timbangan_detail where ID_TIMBANGAN IN(SELECT ID_TIMBANGAN FROM s_data_timbangan where s_data_timbangan.NO_SPB='".$no_spb."')";
            $queryD = $this->db->query($delQ);
            
            if($this->db->trans_status() === FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
        
    }
    
    function delete_notadetail($id_anon,$id_nt_ab){
        $id_anon = trim($this->db->escape_str($id_anon));
        $id_nt_ab = trim($this->db->escape_str($id_nt_ab));
        
        $status=FALSE;
        if((!empty($id_anon) && $id_anon==false)){
            $status = "ID_NOTA CANNOT BE NULL !!";
        }
 
        if(empty($status) || $status==false){
            
            $notastatus = $this->cek_nota_status('',$id_nt_ab);
            if($notastatus!=1){
                /*$this->db->where('ID_NT_AB',$id_nt_ab);*/
                $this->db->where('ID_ANON',$id_anon);
                $this->db->delete('s_nota_angkutbuah_detail');
                
                if($this->db->trans_status() === FALSE){
                    $status = $this->db->_error_message();//"Error in Transactions!!";
                }else{
                    $status="Delete Data Berhasil"."\n"; 
					$status=true;
                }   
            }else{
                //$status="Data Tidak dapat di Hapus..!!"."\n";  
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
    
    function cek_nota_status($no_tiket='',$id_nota){
        $no_tiket = $this->db->escape_str($no_tiket);
        $id_nota = $this->db->escape_str($id_nota);
        $status ='';
        
        $query="SELECT STATUS FROM s_nota_angkutbuah WHERE ID_NT_AB='".$id_nota."' -- AND NO_SPB = '".$no_tiket."'";
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
        if($spb!='' && $spb!='-') $where.= " AND nab.NO_SPB LIKE '%$spb%'"; 
        if($no_kend!='' && $no_kend!='-') $where.= " AND NO_KENDARAAN LIKE '%$no_kend%'";       
        $where .= " AND nab.COMPANY_CODE = '".$company."' AND DATE_FORMAT(TANGGAL,'%Y%m')='".$periode."' AND nab.ACTIVE=1";
        
        //$queries = "SELECT * FROM s_nota_angkutbuah ". $where;
		if ($company<>"ASL"){
			if ($company =="MSS"){
				$queries ="SELECT DISTINCT nab.ID_NT_AB, TANGGAL, nab.NO_KENDARAAN,
									NAMA_SUPIR, nab.NO_TIKET, nab.NO_SPB, nab.COMPANY_CODE, (t.BERAT_ISI - t.BERAT_KOSONG) AS BERAT_BERSIH, t.FLAG_TIMBANGAN from s_nota_angkutbuah nab
						LEFT JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB
						LEFT JOIN s_data_timbangan_kebun t ON nab.NO_SPB = t.NO_SPB ".$where;	
			}else{
				$queries ="SELECT DISTINCT nab.ID_NT_AB, TANGGAL, nab.NO_KENDARAAN,
									NAMA_SUPIR, nab.NO_TIKET, nab.NO_SPB, nab.COMPANY_CODE, (t.BERAT_ISI - t.BERAT_KOSONG) AS BERAT_BERSIH, t.FLAG_TIMBANGAN from s_nota_angkutbuah nab
						LEFT JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB
						LEFT JOIN s_data_timbangan t ON nab.NO_SPB = t.NO_SPB ".$where;
			}
		}
                    
        $sql2 = $queries;
        //var_dump($sql2);
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
		//var_dump($sql);
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
	
	/*
		get_status_close added by Asep, 20130507		
	*/
	function get_status_close($company, $tgl){
        $company = $this->db->escape_str($company);
		
        $query="SELECT ISCLOSE FROM m_periode_control WHERE COMPANY_CODE = '".$company."' AND MODULE ='NAB' AND DATE_FORMAT('".$tgl."','%Y%m%d') BETWEEN PERIODE_START AND PERIODE_END";
					
        $sQuery = $this->db->query($query);
        $value = 0;
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
            $value = $row->ISCLOSE;    
        } 
        return $value;  
    }

	function round_tonase($id_nab, $company){
		$i=0;		
		$sisa=0;
		$round_tonase=0;
		if  ($company=='MSS'){
			$qNab= "SELECT nabd.ID_ANON, nabd.BLOCK, nabd.TONASE, ROUND(nabd.TONASE) AS ROUND_TONASE, (nabd.TONASE - ROUND(nabd.TONASE)) AS SISA, timbang.BERAT_BERSIH FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan_kebun timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.NO_SPB = '". $id_nab ."' AND nab.ACTIVE=1";
		}else{
			$qNab= "SELECT nabd.ID_ANON, nabd.BLOCK, nabd.TONASE, ROUND(nabd.TONASE) AS ROUND_TONASE, (nabd.TONASE - ROUND(nabd.TONASE)) AS SISA, timbang.BERAT_BERSIH FROM s_nota_angkutbuah nab 
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN s_data_timbangan timbang ON nab.NO_SPB = timbang.NO_SPB 
				WHERE nab.NO_SPB = '". $id_nab ."' AND nab.ACTIVE=1";
		}
		$this->db->reconnect();
		$dataNab =$this->db->query($qNab);
		$i=$dataNab->num_rows();
		if($i > 0){
			foreach ($dataNab->result_array() as $row_nab){				
				$sisa=$sisa+$row_nab['SISA'];
				if ($i == 1){				
					$round_tonase=round(($row_nab['ROUND_TONASE']+$sisa),0);
				}else{
					$round_tonase=$row_nab['ROUND_TONASE'];					
				}
				$i=$i-1;								
				$sUpdateDetail="UPDATE s_nota_angkutbuah_detail SET ROUND_TONASE = ". $round_tonase ." WHERE ID_ANON ='". $row_nab['ID_ANON'] ."'";
				$this->db->reconnect();
				$this->db->query($sUpdateDetail);
				//var_dump('*************update sUpdateDetail***************');
				//var_dump($sUpdateDetail);
			}
		}
		
	}
	
}
?>
