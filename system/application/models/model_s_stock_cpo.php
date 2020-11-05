<?php
class model_s_stock_cpo extends Model{
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
        
		$queries ="SELECT s.ID_BA, s.BA_DATE, s.DESCRIPTION, s.QC, s.MILL_MANAGER, s.KTU, s.ADMINISTRATUR, s.LABOR, 	
				   s.COMPANY_CODE, s.FFB_INTI, s.FFB_PLASMA, s.FFB_SUPPLIER, s.FFB_GROUP, s.FFB_PROCESSED, s.BALANCE, 
				   s.BALANCE_YESTERDAY, s.LORI_OLAH, s.LORI_RESTAN, s.CAGE_WEIGHT, s.PROCESSED_HOUR, s.THROUGHPUT, s.MILL_UTILIZATION, 
				   s.BUAH_MENTAH, s.BUAH_BUSUK, s.JJK, s.TANGKAI, s.BRONDOLAN, s.HOUR_FROM, s.HOUR_TO, s.CBC_FROM, s.CBC_TO, CASE (s.STATUS) WHEN 0 THEN 'WAITING APPROVAL' ELSE 'APPROVED' END AS STATUS
				   FROM s_ba s
				   WHERE s.ACTIVE= 1 AND s.COMPANY_CODE = '".$company."' 
				   AND DATE_FORMAT(s.BA_DATE,'%Y%m')='".$periode."'";
         
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
            array_push($cell, htmlentities($obj->ID_BA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BA_DATE,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->QC,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MILL_MANAGER,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KTU,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ADMINISTRATUR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LABOR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_INTI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_PLASMA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_SUPPLIER,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_GROUP,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_PROCESSED,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BALANCE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BALANCE_YESTERDAY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LORI_OLAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LORI_RESTAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CAGE_WEIGHT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROCESSED_HOUR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->THROUGHPUT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MILL_UTILIZATION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BUAH_MENTAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BUAH_BUSUK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JJK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TANGKAI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BRONDOLAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->HOUR_FROM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->HOUR_TO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CBC_FROM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CBC_TO,ENT_QUOTES,'UTF-8'));
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

	function update_approval($qc, $mill, $ktu, $adm, $lab, $company){
		$return['status']='';
        $return['error']=false;
		$status=false;
		$isExist=false;
		
		$this->db->select('ID_APPROVAL');
        $this->db->from('s_ba_approval');
        $this->db->where(array('COMPANY_CODE'=>$company));
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
		
		if ($count==0){
			$isExist == false;
		}else{
			$isExist == true;
		}
		if ($isExist == true){
			$update_sql= "UPDATE s_ba_approval SET QC = '".$qc."', LABOR = '".$lab."', MILL = '".$mill."', KTU = '".$ktu."',
							ADM = '".$adm."' WHERE COMPANY_CODE='".$company."'";
			$status=$this->db->query($update_sql);
		}else{
			$insert_sql= "INSERT INTO s_ba_approval (QC, LABOR, MILL, KTU, ADM, COMPANY_CODE) VALUES ('".$qc."', '".$lab."', '".$mill."', '".$ktu."', '".$adm."', '".$company."')";
			$status=$this->db->query($insert_sql);	
		}
		
		if($status == false){
			$return['status']=$this->db->_error_message();
			$return['error']=true;
		}else{
			$return['status']="Update Data Berhasil"."\n";
			$return['error']=false;
		} 
		return $return;
	}
	function get_approval($company){
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
         
        $query = $this->db->query("SELECT * FROM s_ba_approval a
WHERE a.COMPANY_CODE = '".$company."'");
		
		return $query->result_array();
    }
	
	function get_ba($company, $dates){         
        $sql= "SELECT a.status AS STATUS FROM s_ba a
WHERE a.COMPANY_CODE = '".$company."' AND a.BA_DATE = ('".$dates."' - INTERVAL 1 DAY) AND a.ACTIVE=1";

		$query = $this->db->query($sql);
		
        $status='';
        if($query->num_rows() > 0){
            $row = $query->row();            
            $value = $row->STATUS; 
			if ($value==1){
				$status = 'true';		
			}else if ($status==0){
				$status = 'false';					  
			}
        }else{
            $status = 'false';   
        } 
        return $status; 
	
    }
	
	function get_ba_next($company, $dates){         
        $sql= "SELECT a.status AS STATUS FROM s_ba a
WHERE a.COMPANY_CODE = '".$company."' AND a.BA_DATE = ('".$dates."' + INTERVAL 1 DAY) AND a.ACTIVE=1";

		$query = $this->db->query($sql);
		
        $status='';
        if($query->num_rows() > 0){
            $row = $query->row();            
            $value = $row->STATUS; 
			if ($value==1){
				$status = 'true';		
			}else if ($status==0){
				$status = 'false';					  
			}
        }else{
            $status = 'false';   
        } 
        return $status; 
	
    }
	
	function LoadProductionByDate($company,$date){
	 $ar = preg_split('/[- :]/',trim($date));
        $ar = implode('',$ar);
	 $next_day = strtotime('1 day',strtotime($ar)); 
	 $next_day= date('Ymd', $next_day);

	 $m='';
	 $y='';
	 $d='';
	 $d=date("d",strtotime($next_day));
	 $m=date("m",strtotime($next_day));
	 $y=date("Y",strtotime($next_day));
	 $next_day= $y."-".$m."-".$d;

        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
        $company=trim($this->db->escape_str($company)); 
		$queries = "SELECT k.ID_KOMODITAS AS ID_COMMODITY, k.JENIS , k.COMPANY_CODE, '' AS ID_PRODUCTION , COALESCE(s.BERAT_BERSIH,0) AS WEIGHT, '' AS FFA, '' AS MOISTURE, '' AS DIRT
FROM s_komoditas k
LEFT JOIN (
	/*
	SELECT JENIS_MUATAN, s.COMPANY_CODE, '' AS ID_PRODUCTION , SUM(s.BERAT_BERSIH) AS BERAT_BERSIH
	FROM s_data_timbangan s
	WHERE s.JENIS_MUATAN like '%cpo-%' AND s.TANGGALM = '".$date."'
	AND s.COMPANY_CODE = '".$company."'
	GROUP BY JENIS_MUATAN
	*/
	SELECT JENIS_MUATAN, COMPANY_CODE, ID_PRODUCTION, COALESCE(SUM(RECEIPT.BERAT_BERSIH),0) AS BERAT_BERSIH
	FROM(
		SELECT CASE WHEN JENIS_MUATAN = 'CPO-GKM' THEN 'CPO-GKMNRP' ELSE JENIS_MUATAN END AS JENIS_MUATAN, COMPANY_CODE, '' AS ID_PRODUCTION, CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH		    
		FROM s_data_timbangan 			
		WHERE JENIS_MUATAN like '%cpo-%' AND COMPANY_CODE = '".$company."'			
		AND TANGGALK between '".$date."' AND '".$next_day."'
	)RECEIPT
	WHERE RECEIPT.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'
	GROUP BY JENIS_MUATAN
	UNION ALL
	SELECT 'CPO-SMINRP' AS JENIS_MUATAN , comodity.COMPANY_CODE, '' AS ID_PRODUCTION, SUM(BERAT_BERSIH) AS BERAT_BERSIH
	FROM s_komoditas comodity
	LEFT JOIN (
		SELECT PRODUCT_CODE, BERAT_BERSIH
		FROM s_movement_sounding dispatch
		LEFT JOIN (
			SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
			WHERE COMPANY_CODE = '".$company."'
		) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
		WHERE COMPANY_CODE = '".$company."' AND DATE = '".$date."' AND MOV_TYPE = 'P'
	) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
	WHERE COMPANY_CODE = '".$company."' AND JENIS = 'CPO'
) s ON s.JENIS_MUATAN = k.JENIS 
WHERE k.COMPANY_CODE ='".$company."' AND k.JENIS like '%cpo-%'";
		
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
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_PRODUCTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
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
	function LoadNoProductionByDate($company,$date){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
        $company=trim($this->db->escape_str($company)); 
		$queries = "SELECT k.ID_KOMODITAS AS ID_COMMODITY , k.JENIS AS JENIS, k.COMPANY_CODE,'' AS ID_PRODUCTION, '' AS FFA, '' AS MOISTURE, '' AS DIRT
FROM s_komoditas k
WHERE k.ACTIVE=1
AND k.COMPANY_CODE ='".$company."'  
AND k.KODE_JENIS IN ('CPO', 'KRN', 'TNK', 'CKG', 'ABJ')";

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
			$weight = 0;
			$dispatch = 0;
			$weight_yesterday = 0;
			$weight_today =0;
			if ($obj->JENIS == 'CPO'){
				$weight=0;
			}else if ($obj->JENIS == 'KERNEL'){
				$weight=0;
			}else if ($obj->JENIS == 'TANKOS'){
				$weight_yesterday=$this->get_prod($obj->COMPANY_CODE, $obj->ID_COMMODITY, $date);
				$weight_today=$this->get_produksi($obj->COMPANY_CODE, $obj->ID_COMMODITY, $date);
				$dispatch=$this->get_dispatch_prod($obj->COMPANY_CODE, $obj->JENIS, $date);
				$weight = $weight_today+$dispatch-$weight_yesterday;
			}
			
			if ($obj->JENIS == 'CANGKANG' || $obj->JENIS == 'ABU JANJANG' || $obj->JENIS == 'CKG'){
				$weight_yesterday=$this->get_prod($obj->COMPANY_CODE, $obj->ID_COMMODITY, $date);
				$weight_today=$this->get_produksi($obj->COMPANY_CODE, $obj->ID_COMMODITY, $date);
				$dispatch=$this->get_dispatch($obj->COMPANY_CODE, $obj->JENIS, $date);
				$weight = $weight_today+$dispatch-$weight_yesterday;
			}
			$weight = floor($weight);
			$weight = $this->rounds($weight);
			
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_PRODUCTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($weight,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
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
	*/
	function rounds($round){
		$right = substr($round, -1);
		$x=0;
		$result=0;
		if ($right>=5){
			$x=10-$right;
			$result = $round + $x;
		}else{
			$result = $round - $right;
		}
		return $result;		
	}
	
	function LoadStorageByDate($company,$date){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
        $company=trim($this->db->escape_str($company)); 
		$queries = "SELECT k.ID_STORAGE AS ID_STORAGE , k.PRODUCT_CODE AS PRODUCT_CODE, k.COMPANY_CODE,'' AS ID_PRODUCTION, '' AS FFA, '' AS MOISTURE, '' AS DIRT
FROM m_storage k
WHERE k.ACTIVE=1
AND k.COMPANY_CODE ='".$company."' 
AND k.PRODUCT_CODE IN ('CPO')";

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
			$weight = 0;
			$product_code =$obj->PRODUCT_CODE;
			if ($product_code=='CPO'){
				$table = 's_sounding';	
			}else if ($product_code=='KERNEL'){
				$table = 's_sounding_kernel';	
			}
			$weight=$this->get_idstorage_stock($obj->COMPANY_CODE, $obj->ID_STORAGE, $date, $table);
			$weight = floor($weight);
			$weight = $this->rounds($weight);
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($weight,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
                  
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
	
	function LoadDispatchByDate($company,$date){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
        $company=trim($this->db->escape_str($company)); 
		$queries = "SELECT ID_KOMODITAS AS ID_COMMODITY, JENIS, comodity.COMPANY_CODE, '' AS ID_DISPATCH , COALESCE(BERAT_BERSIH,0) AS WEIGHT, '' AS FFA, '' AS MOISTURE, '' AS DIRT
FROM s_komoditas comodity
LEFT JOIN (
	SELECT PRODUCT_CODE, BERAT_BERSIH
	FROM s_movement_sounding dispatch
	LEFT JOIN (
		SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
		WHERE COMPANY_CODE = '".$company."'
	) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
	WHERE COMPANY_CODE = '".$company."' AND DATE = '".$date."' AND MOV_TYPE = 'D'
) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
WHERE COMPANY_CODE = '".$company."' AND JENIS = 'CPO'";
		
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
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
                  
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
	
	function LoadOtherStockByDate($company,$date){
	  	$ar = preg_split('/[- :]/',trim($date));
    $ar = implode('',$ar);
	$next_day = strtotime('1 day',strtotime($ar)); 
	$next_day= date('Ymd', $next_day);

	$m='';
	$y='';
	$d='';
	$d=date("d",strtotime($next_day));
	$m=date("m",strtotime($next_day));
	$y=date("Y",strtotime($next_day));
	$next_day= $y."-".$m."-".$d;

        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));

		$queries="SELECT ID_KOMODITAS, JENIS, COMPANY_CODE, ID_STOCK, SUM(STOCK) AS STOCK, SUM(DISPATCH) AS DISPATCH, SUM(RECEIPT) AS RECEIPT 
FROM(
	SELECT ID_KOMODITAS, JENIS, COMPANY_CODE, '' AS ID_STOCK, SUM(RECEIPT) - SUM(DISPATCH) AS STOCK, 0 AS DISPATCH, 0 AS RECEIPT
	FROM(
		SELECT comodity.ID_KOMODITAS, comodity.JENIS, comodity.COMPANY_CODE,  COALESCE(BERAT_BERSIH,0) AS DISPATCH, 0 AS RECEIPT
		FROM s_komoditas comodity
		LEFT JOIN (
			SELECT PRODUCT_CODE, BERAT_BERSIH
			FROM s_movement_sounding dispatch
			LEFT JOIN (
				SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
				WHERE COMPANY_CODE = '".$company."'
			) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
			WHERE COMPANY_CODE = '".$company."' AND DATE < '".$date."'  AND MOV_TYPE = 'D'
		) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
		WHERE COMPANY_CODE = '".$company."' AND JENIS = 'CPO'
		UNION ALL
		SELECT k.ID_KOMODITAS, k.JENIS, k.COMPANY_CODE, 0 AS DISPATCH, COALESCE(SUM(s.BERAT_BERSIH),0) AS RECEIPT
		FROM s_komoditas k
		LEFT JOIN (
/*
			SELECT JENIS_MUATAN, s.COMPANY_CODE, '' AS ID_PRODUCTION , SUM(s.BERAT_BERSIH) AS BERAT_BERSIH
			FROM s_data_timbangan s
			WHERE s.JENIS_MUATAN like '%cpo-%' AND s.TANGGALM < '".$date."'
			AND s.COMPANY_CODE = '".$company."'
*/
			SELECT JENIS_MUATAN, COMPANY_CODE, ID_PRODUCTION, COALESCE(SUM(RECEIPT.BERAT_BERSIH),0) AS BERAT_BERSIH
			FROM(
				SELECT JENIS_MUATAN, COMPANY_CODE, '' AS ID_PRODUCTION, CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH		    
				FROM s_data_timbangan 			
				WHERE (JENIS_MUATAN like '%cpo-%' OR JENIS_MUATAN like '%titip%') AND COMPANY_CODE = 'NRP'			
			)RECEIPT
			WHERE RECEIPT.TANGGAL < '".$date." 06:59:59'
			UNION ALL
			SELECT 'CPO-SMINRP' AS JENIS_MUATAN , comodity.COMPANY_CODE, '' AS ID_PRODUCTION, SUM(BERAT_BERSIH) AS BERAT_BERSIH
			FROM s_komoditas comodity
			LEFT JOIN (
				SELECT PRODUCT_CODE, BERAT_BERSIH
				FROM s_movement_sounding dispatch
				LEFT JOIN (
					SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
					WHERE COMPANY_CODE = '".$company."'
				) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
				WHERE COMPANY_CODE = '".$company."' AND DATE < '".$date."' AND MOV_TYPE = 'P'
			) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
			WHERE COMPANY_CODE = 'NRP' AND JENIS = 'CPO'
			
		) s ON s.JENIS_MUATAN = k.JENIS 
		WHERE k.COMPANY_CODE ='".$company."' AND k.JENIS like '%cpo-%'
	) stock_awal
	UNION ALL
	SELECT comodity.ID_KOMODITAS, comodity.JENIS, comodity.COMPANY_CODE, '' AS ID_STOCK , 0 AS STOCK, COALESCE(BERAT_BERSIH,0) AS DISPATCH, 0 AS RECEIPT
	FROM s_komoditas comodity
	LEFT JOIN (
		SELECT PRODUCT_CODE, BERAT_BERSIH
		FROM s_movement_sounding dispatch
		LEFT JOIN (
			SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
			WHERE COMPANY_CODE = '".$company."'
		) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
		WHERE COMPANY_CODE = '".$company."' AND DATE = '".$date."' AND MOV_TYPE = 'D'
	) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
	WHERE COMPANY_CODE = '".$company."' AND JENIS = 'CPO'
	UNION ALL
	SELECT k.ID_KOMODITAS, k.JENIS, k.COMPANY_CODE, 0 AS STOCK, '' AS ID_STOCK , 0 AS DISPATCH, COALESCE(SUM(s.BERAT_BERSIH),0) AS RECEIPT
	FROM s_komoditas k
	LEFT JOIN (
/*
		SELECT JENIS_MUATAN, s.COMPANY_CODE, '' AS ID_PRODUCTION , SUM(s.BERAT_BERSIH) AS BERAT_BERSIH
		FROM s_data_timbangan s
		WHERE s.JENIS_MUATAN like '%cpo-%' AND s.TANGGALM = '".$date."'
		AND s.COMPANY_CODE = '".$company."'
*/
		SELECT JENIS_MUATAN, COMPANY_CODE, ID_PRODUCTION, COALESCE(SUM(RECEIPT.BERAT_BERSIH),0) AS BERAT_BERSIH
		FROM(
			SELECT JENIS_MUATAN, COMPANY_CODE, '' AS ID_PRODUCTION, CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH		    
			FROM s_data_timbangan 			
			WHERE JENIS_MUATAN like '%cpo-%' AND COMPANY_CODE = '".$company."'			
			AND TANGGALK between '".$date."' AND '".$next_day."'
		)RECEIPT
		WHERE RECEIPT.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'
		UNION ALL
		SELECT 'CPO-SMINRP' AS JENIS_MUATAN , comodity.COMPANY_CODE, '' AS ID_PRODUCTION, SUM(BERAT_BERSIH) AS BERAT_BERSIH
		FROM s_komoditas comodity
		LEFT JOIN (
			SELECT PRODUCT_CODE, BERAT_BERSIH
			FROM s_movement_sounding dispatch
			LEFT JOIN (
				SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
				WHERE COMPANY_CODE = '".$company."'
			) storage ON storage.ID_STORAGE = dispatch.ID_STORAGE
			WHERE COMPANY_CODE = '".$company."' AND DATE = '".$date."' AND MOV_TYPE = 'P'
		) dispatch ON dispatch.PRODUCT_CODE = comodity.JENIS
		WHERE COMPANY_CODE = 'NRP' AND JENIS = 'CPO'		
	) s ON s.JENIS_MUATAN = k.JENIS 
	WHERE k.COMPANY_CODE ='".$company."' AND k.JENIS like '%cpo-%'
) stock
";

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
        
        $sql = $queries." ORDER BY "."ID_KOMODITAS"." ".$sord." LIMIT ".$start.",".$limit." ";
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj){			
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_KOMODITAS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->ID_STOCK,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->STOCK+$obj->RECEIPT-$obj->DISPATCH,ENT_QUOTES,'UTF-8')); 
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
	function get_dispatch($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		$ar = preg_split('/[- :]/',trim($date));
        	$ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;
		
		//todo: Asep, tambahkan parameter tanggal (yyyymmdd)
		if (($company=='GKM' || $company=='LIH' || $company=='SMI') && ($jenis=='CANGKANG' || $jenis=='CKG')){

			$query="SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
					FROM s_dispatch
					WHERE s_dispatch.ACTIVE='1' 
					AND s_dispatch.COMPANY_CODE ='".$company."'
					AND s_dispatch.JENIS IN ('CANGKANG','CKG')
					AND TANGGALK between '".$date."' AND '".$next_day."'
					ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59' ";
		}else if (($company=='GKM' || $company=='LIH' || $company=='SMI') && ($jenis=='KERNEL' || $jenis=='KRN')){

			$query="SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
					FROM s_dispatch
					WHERE s_dispatch.ACTIVE='1' 
					AND s_dispatch.COMPANY_CODE ='".$company."'
					AND s_dispatch.JENIS IN ('KERNEL','KRN')
					AND TANGGALK between '".$date."' AND '".$next_day."'
					ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59' ";
		}else if ($company=='GKM' && ($jenis=='CPO')){

			$query="SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
					FROM s_dispatch
					WHERE s_dispatch.ACTIVE='1' 
					AND s_dispatch.COMPANY_CODE IN ('GKM', 'SML', 'SSS')
					AND s_dispatch.JENIS IN ('CPO') 
					AND TANGGALK between '".$date."' AND '".$next_day."'
					AND s_dispatch.ID_DISPATCH LIKE 'GKM%'
					ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59' ";
		}else if ($company=='SMI' && ($jenis=='CPO')){
			$query="SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT d.TANGGAL, SUM(d.BERAT_BERSIH) AS BERAT_BERSIH
					FROM(
						SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE ='".$company."' 
						AND s_dispatch.JENIS IN ('".$jenis."')  
						AND TANGGALK between '".$date."' AND '".$next_day."'
						UNION ALL
						SELECT CAST(STR_TO_DATE(CONCAT(DATE, TIME), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_movement_sounding
						WHERE s_movement_sounding.ACTIVE='1' 
						AND s_movement_sounding.COMPANY_CODE ='".$company."' 
						AND DATE = '".$date."' 
					)d
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59' ";

		}else{

			$query="SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
					FROM s_dispatch
					WHERE s_dispatch.ACTIVE='1' 
					AND s_dispatch.COMPANY_CODE ='".$company."' 
					AND s_dispatch.JENIS IN ('".$jenis."')  
					AND TANGGALK between '".$date."' AND '".$next_day."'
					ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59' ";
		}
	//var_dump($query);
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = $row->VOL_DISPATCH;
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	
	function get_dispatch_prod($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		$ar = preg_split('/[- :]/',trim($date));
        $ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;

			$query="SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH FROM (
					SELECT BERAT_BERSIH, CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS MASUK
							FROM s_data_timbangan
							WHERE s_data_timbangan.ACTIVE='1' 
							AND s_data_timbangan.COMPANY_CODE='".$company."' 
							AND s_data_timbangan.JENIS_MUATAN='".$jenis."' 
							-- AND s_data_timbangan.NO_TIKET LIKE 'GKM%'
							
				) DATA_TIMBANGAN
				WHERE MASUK BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'";
//var_dump($query);

        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = $row->VOL_DISPATCH;
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	*/
	function LoadDetail_Production($company,$id_ba){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $id_ba=trim($this->db->escape_str($id_ba));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT p.ID_COMMODITY, k.JENIS, p.COMPANY_CODE, p.ID_PRODUCTION, p.WEIGHT, p.FFA, p.MOISTURE, p.DIRT
					FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
					WHERE p.ID_BA ='".$id_ba."' AND p.COMPANY_CODE ='".$company."' AND p.ACTIVE = 1";

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
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_PRODUCTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
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
	function get_prod($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		$query="SELECT WEIGHT, ADJUST.SLUDGE AS ADJUST FROM s_ba_stock
LEFT JOIN (
	SELECT s_adjustment.SLUDGE, s_adjustment.ADJUST_DATE, s_adjustment.ID_STORAGE FROM s_adjustment
	WHERE s_adjustment.COMPANY_CODE='".$company."' AND s_adjustment.ADJUST_DATE= ('".$date."' - INTERVAL 1 DAY)
	AND s_adjustment.ACTIVE = 1 AND s_adjustment.ID_STORAGE = '".$jenis."'	
) ADJUST ON s_ba_stock.STOCK_DATE = ADJUST.ADJUST_DATE AND s_ba_stock.ID_COMMODITY = ADJUST.ID_STORAGE 
WHERE s_ba_stock.COMPANY_CODE='".$company."' AND s_ba_stock.STOCK_DATE= ('".$date."' - INTERVAL 1 DAY)
AND s_ba_stock.ACTIVE = 1 AND s_ba_stock.ID_COMMODITY = '".$jenis."'";
		
		
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = ($row->WEIGHT);
			$adjust = ($row->ADJUST);
            $value = ($weight+$adjust);    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	*/
	function get_produksi($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		
        $query="SELECT WEIGHT FROM s_production
WHERE COMPANY_CODE='".$company."' AND ACTIVE = 1 
AND PRODUCTION_DATE = '".$date."' AND ID_COMMODITY ='".$jenis."'";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = ($row->WEIGHT);
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	/*
	function get_prod_cpo($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		$ar = preg_split('/[- :]/',trim($date));
        	$ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;

		if ($company=='GKM'){
			$query="SELECT  TANGGALH AS TANGGAL_PRODUKSI, COALESCE(DISPATCH.VOL_DISPATCH,0) AS DISPATCH, COALESCE(SOUNDINGH.WEIGHTH,0) AS WEIGHTH,
					 SUM(CASE WHEN COALESCE(SOUNDINGK.WEIGHTK,0) = 0  THEN (
						SELECT SUM(snd2.WEIGHT) AS WEIGHT
						FROM s_sounding snd2
						WHERE snd2.COMPANY_CODE='".$company."'
						AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																			FROM s_sounding snd1
																			WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																			AND snd1.ACTIVE=1),'%Y%m%d') 					
					)ELSE SOUNDINGK.WEIGHTK END) AS WEIGHTK, coalesce(SOUNDINGH.ADJUSTH,0) AS ADJUSTH,  coalesce(DISPATCH_RETURN.VOL_DISPATCH,0) AS DISPATCH_RETURN, COMPANY_CODE
			FROM
			(
				SELECT SUM(snd.WEIGHT) AS WEIGHTH, SUM(ADJUST.SLUDGE) AS ADJUSTH, snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
				FROM s_sounding snd 			
				LEFT JOIN(
					SELECT ID_STORAGE,ADJUST_DATE, SLUDGE
					FROM s_adjustment 		
					WHERE s_adjustment.COMPANY_CODE='".$company."'  AND s_adjustment.ACTIVE=1 AND s_adjustment.STATUS=1 AND ADJUST_DATE = '".$date."'		
				) ADJUST ON snd.`DATE` = ADJUST.ADJUST_DATE AND snd.ID_STORAGE=ADJUST.ID_STORAGE
				WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
				AND snd.DATE = '".$date."'	 
			) SOUNDINGH 
			LEFT JOIN (
				SELECT SUM(snd1.WEIGHTK) AS WEIGHTK, snd1.COMPANY_CODEK, snd1.TANGGALK
				FROM(
					SELECT snd.ID_STORAGE, snd.WEIGHT AS WEIGHTK ,snd.COMPANY_CODE AS COMPANY_CODEK,
					DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGALK
					FROM s_sounding snd 
					WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
					AND DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT('".$date."' - INTERVAL 1 DAY,'%Y%m%d')    
				) snd1	
		 )SOUNDINGK ON SOUNDINGH.TANGGALH = SOUNDINGK.TANGGALK 
			LEFT JOIN (
	
				SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL_H, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
				SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE IN ('GKM', 'SML', 'SSS')
						AND s_dispatch.JENIS='".$jenis."'
						AND TANGGALK between '".$date."' AND '".$next_day."'
						AND s_dispatch.ID_DISPATCH LIKE 'GKM%'
						ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'
				
			)DISPATCH ON SOUNDINGH.TANGGALH = DISPATCH.TANGGAL_H
LEFT JOIN (
				SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH,TANGGALM AS TANGGAL_H
				FROM s_dispatch_return
				WHERE ACTIVE='1' 
				AND COMPANY_CODE IN ('GKM','SML','SSS')
				AND JENIS='".$jenis."' 
				AND TANGGALM = '".$date."' 
			)DISPATCH_RETURN ON SOUNDINGH.TANGGALH = DISPATCH_RETURN.TANGGAL_H";	
		}else if ($company=='SMI'){
			$query="SELECT  TANGGALH AS TANGGAL_PRODUKSI, COALESCE(DISPATCH.VOL_DISPATCH,0) AS DISPATCH, COALESCE(SOUNDINGH.WEIGHTH,0) AS WEIGHTH,
					 SUM(CASE WHEN COALESCE(SOUNDINGK.WEIGHTK,0) = 0  THEN (
						SELECT SUM(snd2.WEIGHT) AS WEIGHT
						FROM s_sounding snd2
						WHERE snd2.COMPANY_CODE='".$company."'
						AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																			FROM s_sounding snd1
																			WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																			AND snd1.ACTIVE=1),'%Y%m%d') 					
					)ELSE SOUNDINGK.WEIGHTK END) AS WEIGHTK, coalesce(SOUNDINGH.ADJUSTH,0) AS ADJUSTH, coalesce(DISPATCH_RETURN.VOL_DISPATCH,0) AS DISPATCH_RETURN, COMPANY_CODE
			FROM
			(
				SELECT SUM(snd.WEIGHT) AS WEIGHTH, (ADJUST.SLUDGE) AS ADJUSTH, snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
				FROM s_sounding snd 			
				LEFT JOIN(
					SELECT ID_STORAGE,ADJUST_DATE, SLUDGE
					FROM s_adjustment 		
					WHERE s_adjustment.COMPANY_CODE='".$company."'  AND s_adjustment.ACTIVE=1 AND s_adjustment.STATUS=1 AND ADJUST_DATE = '".$date."'		
				) ADJUST ON snd.`DATE` = ADJUST.ADJUST_DATE -- AND snd.ID_STORAGE=ADJUST.ID_STORAGE
				WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
				AND snd.DATE = '".$date."'	 
			) SOUNDINGH 
			LEFT JOIN (
				SELECT SUM(snd1.WEIGHTK) AS WEIGHTK, snd1.COMPANY_CODEK, snd1.TANGGALK
				FROM(
					SELECT snd.ID_STORAGE, snd.WEIGHT AS WEIGHTK ,snd.COMPANY_CODE AS COMPANY_CODEK,
					DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGALK
					FROM s_sounding snd 
					WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
					AND DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT('".$date."' - INTERVAL 1 DAY,'%Y%m%d')    
				) snd1	
		 )SOUNDINGK ON SOUNDINGH.TANGGALH = SOUNDINGK.TANGGALK 
			LEFT JOIN (
				
				SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL_H, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
					SELECT d.TANGGAL, SUM(d.BERAT_BERSIH) AS BERAT_BERSIH
					FROM(	
						SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE = '".$company."' 
						AND s_dispatch.JENIS='".$jenis."'
						AND TANGGALK between '".$date."' AND '".$next_day."'
						UNION ALL
						SELECT CAST(STR_TO_DATE(CONCAT(DATE, TIME), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_movement_sounding
						WHERE s_movement_sounding.ACTIVE='1' 
						AND s_movement_sounding.COMPANY_CODE ='".$company."' 
						AND DATE = '".$date."'
					) d
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'

			)DISPATCH ON SOUNDINGH.TANGGALH = DISPATCH.TANGGAL_H
		LEFT JOIN (
				SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH,TANGGALM AS TANGGAL_H
				FROM s_dispatch_return
				WHERE ACTIVE='1' 
				AND COMPANY_CODE ='".$company."'  
				AND JENIS='".$jenis."'
				AND TANGGALM = '".$date."' 
			)DISPATCH_RETURN ON SOUNDINGH.TANGGALH = DISPATCH_RETURN.TANGGAL_H";
		}else{
			$query="SELECT  TANGGALH AS TANGGAL_PRODUKSI, COALESCE(DISPATCH.VOL_DISPATCH,0) AS DISPATCH, COALESCE(SOUNDINGH.WEIGHTH,0) AS WEIGHTH,
					 SUM(CASE WHEN COALESCE(SOUNDINGK.WEIGHTK,0) = 0  THEN (
						SELECT SUM(snd2.WEIGHT) AS WEIGHT
						FROM s_sounding snd2
						WHERE snd2.COMPANY_CODE='".$company."'
						AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																			FROM s_sounding snd1
																			WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																			AND snd1.ACTIVE=1),'%Y%m%d') 					
					)ELSE SOUNDINGK.WEIGHTK END) AS WEIGHTK, coalesce(SOUNDINGH.ADJUSTH,0) AS ADJUSTH, coalesce(DISPATCH_RETURN.VOL_DISPATCH,0) AS DISPATCH_RETURN, COMPANY_CODE
			FROM
			(
				SELECT SUM(snd.WEIGHT) AS WEIGHTH, (ADJUST.SLUDGE) AS ADJUSTH, snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
				FROM s_sounding snd 			
				LEFT JOIN(
					SELECT ID_STORAGE,ADJUST_DATE, SLUDGE
					FROM s_adjustment 		
					WHERE s_adjustment.COMPANY_CODE='".$company."'  AND s_adjustment.ACTIVE=1 AND s_adjustment.STATUS=1 AND ADJUST_DATE = '".$date."'		
				) ADJUST ON snd.`DATE` = ADJUST.ADJUST_DATE -- AND snd.ID_STORAGE=ADJUST.ID_STORAGE
				WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
				AND snd.DATE = '".$date."'	 
			) SOUNDINGH 
			LEFT JOIN (
				SELECT SUM(snd1.WEIGHTK) AS WEIGHTK, snd1.COMPANY_CODEK, snd1.TANGGALK
				FROM(
					SELECT snd.ID_STORAGE, snd.WEIGHT AS WEIGHTK ,snd.COMPANY_CODE AS COMPANY_CODEK,
					DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGALK
					FROM s_sounding snd 
					WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
					AND DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT('".$date."' - INTERVAL 1 DAY,'%Y%m%d')    
				) snd1	
		 )SOUNDINGK ON SOUNDINGH.TANGGALH = SOUNDINGK.TANGGALK 
			LEFT JOIN (

				SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL_H, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
				FROM(
				SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE = '".$company."' 
						AND s_dispatch.JENIS='".$jenis."'
						AND TANGGALK between '".$date."' AND '".$next_day."'
						ORDER BY TANGGALK
				)DISPATCH
				WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'

			)DISPATCH ON SOUNDINGH.TANGGALH = DISPATCH.TANGGAL_H
		LEFT JOIN (
				SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH,TANGGALM AS TANGGAL_H
				FROM s_dispatch_return
				WHERE ACTIVE='1' 
				AND COMPANY_CODE ='".$company."'  
				AND JENIS='".$jenis."'
				AND TANGGALM = '".$date."' 
			)DISPATCH_RETURN ON SOUNDINGH.TANGGALH = DISPATCH_RETURN.TANGGAL_H";
		}

		
	//var_dump($query);
        $sQuery = $this->db->query($query);
        $value='';
					
        if($sQuery->num_rows() > 0){
			$row = $sQuery->row(); 
			
			$weight_dispatch = floor($row->DISPATCH);
			$weight_dispatch = $this->rounds($weight_dispatch);
			
			$weighth = floor($row->WEIGHTH);
			$weighth = $this->rounds($weighth);
			
			$weightk = floor($row->WEIGHTK);
			$weightk = $this->rounds($weightk);
						
			$adjusth=$row->ADJUSTH;
			$return=$row->DISPATCH_RETURN;
			//var_dump($return);
			$weight = ($weight_dispatch + ($weighth+$adjusth) - ($weightk)-$return);
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	
	function get_prod_emptybunch($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		
        $query="SELECT  TANGGALH AS TANGGAL_PRODUKSI, COALESCE(DISPATCH.VOL_DISPATCH,0) AS DISPATCH, COALESCE(SOUNDINGH.WEIGHTH,0) AS WEIGHTH,
			 SUM(CASE WHEN COALESCE(SOUNDINGK.WEIGHTK,0) = 0  THEN (
				SELECT SUM(snd2.WEIGHT) AS WEIGHT
				FROM s_sounding snd2
				WHERE snd2.COMPANY_CODE='".$company."'
				AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																	FROM s_sounding snd1
																	WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																	AND snd1.ACTIVE=1),'%Y%m%d') 					
			)ELSE SOUNDINGK.WEIGHTK END) AS WEIGHTK, COMPANY_CODE
	FROM
	(
		SELECT snd.ID_STORAGE, SUM(snd.WEIGHT) AS WEIGHTH ,snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
		FROM s_sounding snd 
		WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
		AND snd.DATE = '".$date."' 
	) SOUNDINGH 
	LEFT JOIN (
		SELECT SUM(snd1.WEIGHTK) AS WEIGHTK, snd1.COMPANY_CODEK, snd1.TANGGALK  
		FROM(
			SELECT snd.WEIGHT AS WEIGHTK ,snd.COMPANY_CODE AS COMPANY_CODEK,
			DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGALK
			FROM s_sounding snd 
			WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
			AND DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT('".$date."' - INTERVAL 1 DAY,'%Y%m%d')    
			) snd1
	) SOUNDINGK ON SOUNDINGH.TANGGALH = SOUNDINGK.TANGGALK 
	LEFT JOIN (
		SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH,TANGGALM AS TANGGAL_H
		FROM s_dispatch
		WHERE s_dispatch.ACTIVE='1' 
		AND s_dispatch.COMPANY_CODE='".$company."' 
		AND s_dispatch.JENIS='".$jenis."' 
		AND TANGGALM = '".$date."' 
	)DISPATCH ON SOUNDINGH.TANGGALH = DISPATCH.TANGGAL_H";

        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = ($row->DISPATCH + $row->WEIGHTH - $row->WEIGHTK);
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	
	function get_prod_kernel($company, $jenis, $date){
        $company = $this->db->escape_str($company);
		$ar = preg_split('/[- :]/',trim($date));
        	$ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;
		
$query="SELECT  TANGGALH AS TANGGAL_PRODUKSI, COALESCE(DISPATCHKERNEL.VOL_DISPATCH,0) AS DISPATCH, COALESCE(SOUNDINGKERNELH.WEIGHTH,0) AS WEIGHTH,
COALESCE(SOUNDINGKERNELH.EXTRA_WEIGHTH,0) AS EXTRA_WEIGHTH,
			 SUM(CASE WHEN COALESCE(SOUNDINGKERNELK.WEIGHTK,0) = 0  THEN (
				SELECT SUM(snd2.WEIGHT) AS WEIGHT
				FROM s_sounding_kernel snd2
				WHERE snd2.COMPANY_CODE='".$company."'
				AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																	FROM s_sounding_kernel snd1
																	WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																	AND snd1.ACTIVE=1),'%Y%m%d') 					
			)ELSE SOUNDINGKERNELK.WEIGHTK END) AS WEIGHTK, 
			SUM(CASE WHEN COALESCE(SOUNDINGKERNELK.EXTRA_WEIGHTK,0) = 0  THEN (
				SELECT SUM(snd2.EXTRA_WEIGHT) AS EXTRA_WEIGHTK
				FROM s_sounding_kernel snd2
				WHERE snd2.COMPANY_CODE='".$company."'
				AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																	FROM s_sounding_kernel snd1
																	WHERE snd1.DATE < DATE_FORMAT(TANGGALH,'%Y%m%d') AND snd1.COMPANY_CODE='".$company."'
																	AND snd1.ACTIVE=1),'%Y%m%d') 					
			)ELSE SOUNDINGKERNELK.EXTRA_WEIGHTK END) AS EXTRA_WEIGHTK,
			coalesce(DISPATCH_RETURN.VOL_DISPATCH,0) AS DISPATCH_RETURN,
			COMPANY_CODE
	FROM
	(
		SELECT snd.ID_STORAGE, SUM(snd.WEIGHT) AS WEIGHTH , SUM(snd.EXTRA_WEIGHT) AS EXTRA_WEIGHTH, snd.COMPANY_CODE, snd.DATE AS TANGGALH
		FROM s_sounding_kernel snd 
		WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
		AND snd.DATE = '".$date."'
	) SOUNDINGKERNELH 
	LEFT JOIN (
		SELECT SUM(snd1.WEIGHTK) AS WEIGHTK, SUM(snd1.EXTRA_WEIGHTK) AS EXTRA_WEIGHTK, snd1.COMPANY_CODEK, snd1.TANGGALK  
		FROM(
			SELECT snd.WEIGHT AS WEIGHTK ,snd.EXTRA_WEIGHT AS EXTRA_WEIGHTK, snd.COMPANY_CODE AS COMPANY_CODEK,
			DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGALK
			FROM s_sounding_kernel snd 
			WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
			AND snd.DATE = DATE_FORMAT('".$date."' - INTERVAL 1 DAY,'%Y%m%d')   
			) snd1
	) SOUNDINGKERNELK ON SOUNDINGKERNELH.TANGGALH = SOUNDINGKERNELK.TANGGALK 
	LEFT JOIN (
		SELECT DATE_FORMAT(DISPATCH.TANGGAL, '%Y-%m-%d') AS TANGGAL_H, COALESCE(SUM(DISPATCH.BERAT_BERSIH),0) AS VOL_DISPATCH
		FROM(
			SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGAL, BERAT_BERSIH
			FROM s_dispatch
			WHERE s_dispatch.ACTIVE='1' 
			AND s_dispatch.COMPANY_CODE ='".$company."'
			AND s_dispatch.JENIS IN ('KERNEL','KRN') 
			AND TANGGALK between '".$date."' AND '".$next_day."'
			ORDER BY TANGGALK
		)DISPATCH
		WHERE DISPATCH.TANGGAL BETWEEN '".$date." 07:00:00' AND '".$next_day." 06:59:59'

	)DISPATCHKERNEL ON SOUNDINGKERNELH.TANGGALH = DISPATCHKERNEL.TANGGAL_H
	LEFT JOIN (
				SELECT COALESCE(SUM(BERAT_BERSIH),0) AS VOL_DISPATCH,TANGGALM AS TANGGAL_H
				FROM s_dispatch_return
				WHERE ACTIVE='1' 
				-- AND COMPANY_CODE IN ('GKM','SML','SSS')
				AND COMPANY_CODE IN ('".$company."')
				AND JENIS='KERNEL' 
				AND TANGGALM = '".$date."'
			)DISPATCH_RETURN ON SOUNDINGKERNELH.TANGGALH = DISPATCH_RETURN.TANGGAL_H";
//var_dump($query);
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			
			$weight_dispatch = floor($row->DISPATCH);
			$weight_dispatch = $this->rounds($weight_dispatch);
			
			$weighth = floor($row->WEIGHTH);
			$weighth = $this->rounds($weighth);
			
			$weightk = floor($row->WEIGHTK);
			$weightk = $this->rounds($weightk);

			$extra_weighth = floor($row->EXTRA_WEIGHTH);
			$extra_weighth = $this->rounds($extra_weighth);
			$extra_weightk = floor($row->EXTRA_WEIGHTK);
			$extra_weightk = $this->rounds($extra_weightk);		
            
			//$weight = ($weight_dispatch + $weighth - $weightk);
			$return=$row->DISPATCH_RETURN;
			$weight = ($weight_dispatch + ($weighth+$extra_weighth) - ($weightk+$extra_weightk)-$return);
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	*/
	function LoadDetail_Dispatch($company,$id_ba){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $id_ba=trim($this->db->escape_str($id_ba));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT d.ID_COMMODITY, k.JENIS, d.COMPANY_CODE, d.ID_DISPATCH, 
					d.WEIGHT, d.FFA, d.MOISTURE, d.DIRT
					FROM s_ba_dispatch d
					LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
					WHERE d.ID_BA ='".$id_ba."' AND d.COMPANY_CODE ='".$company."' AND d.ACTIVE = 1";

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
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_DISPATCH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
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
	
	function LoadDetail_Stock($company,$id_ba){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $id_ba=trim($this->db->escape_str($id_ba));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT s.ID_COMMODITY, k.JENIS, s.COMPANY_CODE, s.ID_STOCK, 
					s.WEIGHT, s.FFA, s.MOISTURE, s.DIRT
					FROM s_ba_stock s
					LEFT JOIN s_komoditas k ON s.ID_COMMODITY = k.ID_KOMODITAS
					WHERE s.ID_BA ='".$id_ba."' AND s.COMPANY_CODE ='".$company."' AND s.ACTIVE = 1";

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
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->JENIS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
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
	
	function LoadDetail_StorageStock($company, $id_ba){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $id_ba=trim($this->db->escape_str($id_ba));
        $company=trim($this->db->escape_str($company)); 
         
        $queries = "SELECT ss.ID_STORAGE, s.PRODUCT_CODE, ss.COMPANY_CODE, ss.ID_STRG_STOCK, 
					ss.WEIGHT, ss.FFA, ss.MOISTURE, ss.DIRT
					FROM s_ba_storage_stock ss
					LEFT JOIN m_storage s ON ss.ID_STORAGE = s.ID_STORAGE
					WHERE ss.ID_BA ='".$id_ba."' AND ss.COMPANY_CODE ='".$company."' AND ss.ACTIVE = 1";

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
            array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_STRG_STOCK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->WEIGHT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FFA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOISTURE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIRT,ENT_QUOTES,'UTF-8'));
                  
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
	
	function get_idstorage_stock($company, $id_storage, $date, $tabel){
        $company = $this->db->escape_str($company);
		
		if ($tabel=='s_sounding_kernel'){
			$query="SELECT ID_STORAGE, (WEIGHTH+EXTRA_WEIGHTH) AS WEIGHTH, COMPANY_CODE, TANGGALH 
	FROM
	(		
			SELECT snd.ID_STORAGE, SUM(snd.WEIGHT) AS WEIGHTH , SUM(snd.EXTRA_WEIGHT) AS EXTRA_WEIGHTH, snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
			FROM ".$tabel." snd 
			WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
			AND snd.ID_STORAGE='".$id_storage."'
			AND snd.DATE = '".$date."'
	) SOUNDINGH
	";
		}else{
			$query="SELECT ID_STORAGE, WEIGHTH, COMPANY_CODE, TANGGALH 
	FROM
	(		
			SELECT snd.ID_STORAGE, SUM(snd.WEIGHT) AS WEIGHTH ,snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
			FROM ".$tabel." snd 
			WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
			AND snd.ID_STORAGE='".$id_storage."'
			AND snd.DATE = '".$date."'
	) SOUNDINGH
	";
		}

        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = $row->WEIGHTH;       
			$value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	/*
	function get_storage_kernel($company, $id_storage, $date){
        $company = $this->db->escape_str($company);
        $query="SELECT snd.ID_STORAGE, SUM(snd.WEIGHT) AS WEIGHTH ,snd.COMPANY_CODE, snd.`DATE` AS TANGGALH
		FROM s_sounding_kernel snd 
		WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."'
		AND snd.ID_STORAGE='".$id_storage."'
		AND snd.DATE = '".$date."'";

        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
			$weight = $row->WEIGHTH;
            $value = $weight;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	*/
	function LoadData_Storage($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
		$queries="SELECT s.ID_STORAGE, s.PRODUCT_CODE, s.COMPANY_CODE FROM m_storage s
WHERE s.ACTIVE= 1 AND s.COMPANY_CODE = '".$company."'";
		
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
        
        $sql = $queries." ORDER BY "."ID_STORAGE"." ".$sord." LIMIT ".$start.",".$limit." ";
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_STORAGE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PRODUCT_CODE,ENT_QUOTES,'UTF-8'));
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
	
    function LoadData_Commodity($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
		
		$queries="SELECT k.ID_KOMODITAS AS ID_COMMODITY , k.JENIS AS COMMODITY, k.COMPANY_CODE FROM s_komoditas k
					WHERE k.ACTIVE=1
					AND k.COMPANY_CODE ='".$company."' 
					AND k.KODE_JENIS = 'CPO'";
					
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
        
        $sql = $queries." ORDER BY "."ID_KOMODITAS"." ".$sord." LIMIT ".$start.",".$limit." ";
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMMODITY,ENT_QUOTES,'UTF-8'));
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
	
	function LoadData_Commodities($company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company = trim($this->db->escape_str($company));
		
		$queries="SELECT k.ID_KOMODITAS AS ID_COMMODITY , k.JENIS AS COMMODITY, k.COMPANY_CODE FROM s_komoditas k
					WHERE k.ACTIVE=1
					AND k.COMPANY_CODE ='".$company."' 
					AND k.KODE_JENIS LIKE 'CPO-%'";
					
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
        
        $sql = $queries." ORDER BY "."ID_KOMODITAS"." ".$sord." LIMIT ".$start.",".$limit." ";
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1; 
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_COMMODITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMMODITY,ENT_QUOTES,'UTF-8'));
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
        $queries ="SELECT * FROM s_ba ".$where;
            
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
            array_push($cell, htmlentities($obj->ID_BA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BA_DATE,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));			
            array_push($cell, htmlentities($obj->QC,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MILL_MANAGER,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KTU,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ADMINISTRATUR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LABOR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_INTI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_PLASMA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_SUPPLIER,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_GROUP,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FFB_PROCESSED,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BALANCE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BALANCE_YESTERDAY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CAGE_WEIGHT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROCESSED_HOUR,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->THROUGHPUT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MILL_UTILIZATION,ENT_QUOTES,'UTF-8'));
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
		$return['status']='';
        $return['error']=false;
        $company = trim($this->db->escape_str($company));
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ba',
                    array('ID_BA'=>$data_post['ID_BA'],'BA_DATE'=>$data_post['BA_DATE'],'COMPANY_CODE'=>$company),'ID_BA');
        if ($cek_data_exist > 0){
            $status='Data Input ID telah ada di database';
			$return['status']=$status;
        	$return['error']=true;
        }
        
        unset($cek_data_exist);
        $cek_data_exist = $this->cek_data_exist('s_ba',array('BA_DATE'=>$data_post['BA_DATE'],'COMPANY_CODE'=>$company, 'ACTIVE'=>1),'ID_BA');
        if ($cek_data_exist > 0){
            $status="Berita acara tanggal " .$data_post['BA_DATE']." telah diinput";
			$return['status']=$status;
        	$return['error']=true;
        }

        if(empty($status) || $status==FALSE){			
            $this->db->insert( 's_ba', $data_post );
                    
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status']=$status;
        		$return['error']=true;
            }else{
                $status="Insert berita acara berhasil";   
				$return['status']=$status;
	        	$return['error']=false;
            }
        }
        return $return;
    }
	
	function add_new_production($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_ba_production', $data_post[$keys] );                                 
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
	
	function add_new_quality($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_ba_quality', $data_post[$keys] );                                 
            }
            if($this->db->trans_status() === FALSE){
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
    
	function add_new_stock($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_ba_stock', $data_post[$keys] );                                 
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
	
	function add_new_storage_stock($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_ba_storage_stock', $data_post[$keys] );                                 
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
	
	function add_new_dispatch($id, $company, $data_post){
		$return['status']='';
        $return['error']=false;
        if(isset($id) && isset($company)){
        	foreach($data_post as $keys => $vals){
            	$this->db->insert( 's_ba_dispatch', $data_post[$keys] );                                 
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
	
	function update_data($id, $company, $data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));
		
        $notastatus='0';
        $qCekStatus= "SELECT STATUS FROM s_ba WHERE ID_BA='".$id."'";
        $cek_ba_status=$this->db->query($qCekStatus);
        if($cek_ba_status->num_rows() > 0){
            $row_data = $cek_ba_status->row();
            $ba_status=$row_data->STATUS; 
        }        

		if($notastatus==0){
       		$this->db->where('ID_BA',$id);
          	$this->db->where('COMPANY_CODE',$company);
           	$this->db->update('s_ba', $data );
         	if($this->db->trans_status() == FALSE){
            	$status = $this->db->_error_message();//"Error in Transactions!!";
				$return['status']=$status;
        		$return['error']=true;
          	}else{
            	$status="Update Data ID Berhasil"."\n";
				$return['status']=$status;
        		$return['error']=false;
          	}        
      	}else{
       		$status="Data sudah di approve tidak dapat di update"."\n"; 
			$return['status']=$status;
        	$return['error']=true;
       	}      

		return $return;
    }
	
	function update_production($id,$company,$data){
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
        	$qCekStatus= "SELECT STATUS FROM s_ba WHERE ID_BA='".$id."'";
        	$cek_ba_status=$this->db->query($qCekStatus);
        	if($cek_ba_status->num_rows() > 0){
            	$row_data = $cek_ba_status->row();
            	$ba_status=$row_data->STATUS; 
        	}
        
            if($ba_status!=1){
                $this->db->where('ID_BA', $id );      
                $this->db->delete('s_ba_production');  
                foreach($data as $keys => $vals){
               		$this->db->insert( 's_ba_production', $data[$keys] );                                 
             	}
              	if($this->db->trans_status() == FALSE){
                	$status = $this->db->_error_message();//"Error in Transactions!!";
               	}else{
                   	$status="Update data berhasil"."\n";  
					$return['status']=$status;
        			$return['error']=false;
                }    
            }else{
                $status="Data tidak dapat di Update"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }   
		return $return; 
    }
	
	function update_dispatch($id,$company,$data){
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
        	$qCekStatus= "SELECT STATUS FROM s_ba WHERE ID_BA='".$id."'";
        	$cek_ba_status=$this->db->query($qCekStatus);
        	if($cek_ba_status->num_rows() > 0){
            	$row_data = $cek_ba_status->row();
            	$ba_status=$row_data->STATUS; 
        	}
        
            if($ba_status!=1){
                $this->db->where('ID_BA', $id );      
                $this->db->delete('s_ba_dispatch');  
                foreach($data as $keys => $vals){
               		$this->db->insert('s_ba_dispatch', $data[$keys] );                                 
             	}
              	if($this->db->trans_status() == FALSE){
                	$status = $this->db->_error_message();//"Error in Transactions!!";
               	}else{
                   	$status="Update data berhasil"."\n";  
					$return['status']=$status;
        			$return['error']=false;
                }    
            }else{
                $status="Data tidak dapat di Update"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }   
		return $return; 
    }
	
	function update_stock($id,$company,$data){
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
        	$qCekStatus= "SELECT STATUS FROM s_ba WHERE ID_BA='".$id."'";
        	$cek_ba_status=$this->db->query($qCekStatus);
        	if($cek_ba_status->num_rows() > 0){
            	$row_data = $cek_ba_status->row();
            	$ba_status=$row_data->STATUS; 
        	}
        
            if($ba_status!=1){
                $this->db->where('ID_BA', $id );      
                $this->db->delete('s_ba_stock');  
                foreach($data as $keys => $vals){
               		$this->db->insert('s_ba_stock', $data[$keys] );                                 
             	}
              	if($this->db->trans_status() == FALSE){
                	$status = $this->db->_error_message();//"Error in Transactions!!";
               	}else{
                   	$status="Update data berhasil"."\n";  
					$return['status']=$status;
        			$return['error']=false;
                }    
            }else{
                $status="Data tidak dapat di Update"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }   
		return $return; 
    }
	
	function update_storage_stock($id,$company,$data){
		$return['status']='';
        $return['error']=false;
        $id = trim($this->db->escape_str($id));
        $company = trim($this->db->escape_str($company));
        
        if(empty($id)){
            $status = "ID NOTA CANNOT BE NULL!";
			$return['status']=$status;
        	$return['error']=true;
        }
                
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL!";
			$return['status']=$status;
        	$return['error']=true;
        }
        
		if(empty($return['status']) && $return['error'] == false){  
			$notastatus='0';
        	$qCekStatus= "SELECT STATUS FROM s_ba WHERE ID_BA='".$id."'";
        	$cek_ba_status=$this->db->query($qCekStatus);
        	if($cek_ba_status->num_rows() > 0){
            	$row_data = $cek_ba_status->row();
            	$ba_status=$row_data->STATUS; 
        	}
        
            if($ba_status!=1){
                $this->db->where('ID_BA', $id );      
                $this->db->delete('s_ba_storage_stock');  
                foreach($data as $keys => $vals){
               		$this->db->insert('s_ba_storage_stock', $data[$keys] );                                 
             	}
              	if($this->db->trans_status() == FALSE){
                	$status = $this->db->_error_message();//"Error in Transactions!!";
               	}else{
                   	$status="Update data berhasil"."\n";  
					$return['status']=$status;
        			$return['error']=false;
                }    
            }else{
                $status="Data tidak dapat di Update"."\n";  
				$return['status']=$status;
        		$return['error']=true;
            }   
        }   
		return $return; 
    }
		
    function cek_data_exist($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
           
        return $count;
    } 
	
	function cek_status($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
            $value = $row->STATUS;    
        }else{
            $value = 0;   
        } 
        return $value;
    } 
	
	//start: reporting code
	function get_dispatch_doc($date_dispatch,$company){
		$date_dispatch = trim($this->db->escape_str($date_dispatch));

		$body = "SELECT doo.ID_DO, dispatch.ID_KOMODITAS, doo.QTY_CONTRACT, dispatch.QTY_DELIVERED_RUN, (doo.QTY_CONTRACT -dispatch.QTY_DELIVERED_RUN) AS BALANCE
			FROM s_dispatch_do doo
			LEFT JOIN (				
				SELECT d.DOC_NO AS ID_DO, SUM(BERAT_BERSIH) AS QTY_DELIVERED_RUN, PRODUCT_CODE AS ID_KOMODITAS, d.DATE AS TANGGALM
				FROM s_movement_sounding d
				LEFT JOIN (
					SELECT ID_STORAGE, PRODUCT_CODE FROM m_storage
					WHERE COMPANY_CODE = '".$company."'
				) storage ON storage.ID_STORAGE = d.ID_STORAGE
				WHERE COMPANY_CODE = '".$company."' AND DATE = '".$date_dispatch."'  AND MOV_TYPE = 'D'
				GROUP BY d.DOC_NO, PRODUCT_CODE, d.DATE 
			) dispatch ON doo.ID_DO = dispatch.ID_DO
			WHERE DATE_FORMAT(dispatch.TANGGALM ,'%Y%m%d') = DATE_FORMAT('".$date_dispatch."','%Y%m%d')";		
		/*
		$body ="SELECT doo.ID_DO, k.JENIS, doo.QTY_CONTRACT, dispatch.QTY_DELIVERED_RUN, (doo.QTY_CONTRACT -dispatch.QTY_DELIVERED_RUN) AS BALANCE
			FROM s_dispatch_do doo
			LEFT JOIN (
				SELECT  d.ID_DO, MAX(d.QTY_DELIVERED_RUN) AS QTY_DELIVERED_RUN, d.ID_KOMODITAS, d.TANGGALM
				FROM s_dispatch d
				WHERE DATE_FORMAT(d.TANGGALK ,'%Y%m%d') = DATE_FORMAT('".$date_dispatch."','%Y%m%d') 
				AND d.COMPANY_CODE='".$company."'
				AND d.ACTIVE=1
				GROUP BY d.ID_DO
			) dispatch ON doo.ID_DO = dispatch.ID_DO
			LEFT JOIN s_komoditas k ON dispatch.ID_KOMODITAS = k.ID_KOMODITAS
			WHERE DATE_FORMAT(dispatch.TANGGALM ,'%Y%m%d') = DATE_FORMAT('".$date_dispatch."','%Y%m%d') ";
		*/

		$sQuery = $this->db->query($body);
		$rowcount = $sQuery->num_rows();
		
		$temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
	}
	
	function get_ffb_actual($company_code, $dates){
		$query="SELECT * FROM s_ba ba WHERE ba.BA_DATE ='".$dates."' AND ba.ACTIVE =1 AND ba.COMPANY_CODE = '".$company_code."'";

        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();    
        }else{
			$row=NULL;	
		}
        return $row;  
    }
	
	function get_ffa_period($date, $date_to, $company, $commodity_type){
		$query="SELECT PROD.PRODUCTION_DATE, PROD.WEIGHT, PROD.FFA, MIN_DATE.FLAG   
FROM
(
	SELECT p.PRODUCTION_DATE, p.WEIGHT, p.FFA FROM s_ba_production p
	INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
	where p.COMPANY_CODE = '".$company."' AND p.ACTIVE=1
	AND p.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$date."','%Y%m%d') AND DATE_FORMAT('".$date_to."','%Y%m%d') 
	AND k.KODE_JENIS = '".$commodity_type."'
	ORDER BY p.PRODUCTION_DATE
) PROD LEFT JOIN(
	SELECT MIN(p.PRODUCTION_DATE) PRODUCTION_DATE, 1 FLAG  FROM s_ba_production p
	INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
	WHERE p.COMPANY_CODE = '".$company."' AND p.ACTIVE=1 
	AND p.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$date."','%Y%m%d') AND DATE_FORMAT('".$date_to."','%Y%m%d') 
	AND k.KODE_JENIS = '".$commodity_type."'
) MIN_DATE ON PROD.PRODUCTION_DATE = MIN_DATE.PRODUCTION_DATE";

		$sQuery = $this->db->query($query);
		$rowcount = $sQuery->num_rows();
		
		$temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;
	}
	/*
	function check_ffa($date, $date_to, $company, $commodity_type){
		$query="SELECT FFA FROM s_ba_production p
INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
where p.COMPANY_CODE = '".$company."' AND p.ACTIVE=1
AND p.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$date."','%Y%m%d') AND DATE_FORMAT('".$date_to."','%Y%m%d') AND k.KODE_JENIS = '".$commodity_type."'";	
		$boolean_ffa = false;
		$sQuery = $this->db->query($query);
		if($sQuery->num_rows() == 1){
			$boolean_ffa = true; 
		}
		return $boolean_ffa;
	}
	*/
	function get_prod_period($date, $date_to, $company, $commodity_type){
		$query="SELECT SUM(p.WEIGHT) AS WEIGHT FROM s_ba_production p
INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
WHERE p.COMPANY_CODE='".$company."' AND p.ACTIVE=1 AND k.KODE_JENIS LIKE ('".$commodity_type."') 
AND DATE_FORMAT(p.PRODUCTION_DATE ,'%Y%m%d') BETWEEN DATE_FORMAT('".$date."','%Y%m%d') AND DATE_FORMAT('".$date_to."','%Y%m%d')";
	//var_dump($query);		
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();  
			$value = $row->WEIGHT; 
        }
        return $value;  
    }
		
	function get_production($id, $company, $commodity_type){
		$query="SELECT p.ID_BA, p.PRODUCTION_DATE, p.ID_COMMODITY, p.WEIGHT, p.FFA, p.MOISTURE, p.DIRT, 
k.KODE_JENIS FROM s_ba_production p
INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
WHERE p.ID_BA = '".$id."'  AND p.COMPANY_CODE='".$company."' AND p.ACTIVE=1 AND k.KODE_JENIS LIKE ('%".$commodity_type."%')";
		/*
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row=NULL;
		}
        return $row;  
		*/
		$sQuery = $this->db->query($query);
		$numrows = $sQuery->num_rows();
        if ($numrows > 0){
        	$temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
		}else{
			$temp_result = NULL;	
		}
		return $temp_result;
    }
	
	function get_production_yesterday($date, $company, $commodity_type){
		$query="SELECT p.ID_BA, p.PRODUCTION_DATE, p.ID_COMMODITY, p.WEIGHT, p.FFA, p.MOISTURE, p.DIRT, 
k.KODE_JENIS FROM s_ba_production p
INNER JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
WHERE p.PRODUCTION_DATE = DATE_FORMAT('".$date."','%Y%m%d')  AND p.COMPANY_CODE='".$company."' AND p.ACTIVE=1 AND k.KODE_JENIS LIKE ('%".$commodity_type."%')";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row=NULL;
		}
        return $row;  
    }
	
	function get_despatch($id, $company, $commodity_type){
		$query="SELECT d.ID_BA, d.DISPATCH_DATE, d.ID_COMMODITY, d.WEIGHT, d.FFA, d.MOISTURE, d.DIRT, 
 k.KODE_JENIS
FROM s_ba_dispatch d
INNER JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
WHERE d.ID_BA = '".$id."' AND d.ACTIVE=1 AND d.COMPANY_CODE='".$company."' AND k.KODE_JENIS IN ('".$commodity_type."')";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row=NULL;
		}
        return $row;  
    }
	
	function get_recycle_despatch($tanggal, $company, $commodity_type){
		$query="SELECT d.TANGGAL, d.ID_KOMODITAS, d.BERAT_BERSIH, d.BROKEN, d.DIRTY, d.MOIST,  k.KODE_JENIS 
FROM s_dispatch_return d
INNER JOIN s_komoditas k ON d.ID_KOMODITAS = k.ID_KOMODITAS
WHERE d.ACTIVE=1 AND d.COMPANY_CODE = '".$company."' AND k.KODE_JENIS IN ('".$commodity_type."') AND d.TANGGAL = '".$tanggal."'";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row=NULL;
		}
        return $row;  
    }
	
	function get_dispatch_period($date, $date_to, $company, $commodity_type){
		$query="SELECT SUM(d.WEIGHT) AS WEIGHT FROM s_ba_dispatch d
INNER JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
WHERE d.COMPANY_CODE='".$company."' AND d.ACTIVE=1 AND k.KODE_JENIS IN ('".$commodity_type."') 
AND DATE_FORMAT(d.DISPATCH_DATE ,'%Y%m%d') BETWEEN DATE_FORMAT('".$date."','%Y%m%d') AND DATE_FORMAT('".$date_to."','%Y%m%d')";
		
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery==true){
            $row = $sQuery->row();  
			$value = $row->WEIGHT; 
        }
        return $value;  
    }
	/*
	function get_kj_month($date, $company){
		$query="SELECT COUNT(c.CAL_FLAG) KJ FROM m_calendar c
WHERE c.COMPANY_CODE='".$company."' AND DATE_FORMAT(c.CAL_TGL,'%Y%m') = DATE_FORMAT('".$date."','%Y%m')
AND c.CAL_FLAG = 'KJ'";
		
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery==true){
            $row = $sQuery->row();  
			$value = $row->KJ; 
        }
        return $value;  
    }
	
	function get_kj_year($date, $company){
		$query="SELECT COUNT(c.CAL_FLAG) KJ FROM m_calendar c
WHERE c.COMPANY_CODE='".$company."' AND DATE_FORMAT(c.CAL_TGL,'%Y') = DATE_FORMAT('".$date."','%Y')
AND c.CAL_FLAG = 'KJ'";
		
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery==true){
            $row = $sQuery->row();  
			$value = $row->KJ; 
        }
        return $value;  
    }
	*/
	function get_stock($id, $company, $commodity_type){
		$query="SELECT d.ID_BA, d.STOCK_DATE, d.ID_COMMODITY, d.WEIGHT, d.FFA, d.MOISTURE, d.DIRT, 
 k.KODE_JENIS
FROM s_ba_stock d
INNER JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
WHERE d.ID_BA = '".$id."'  AND d.ACTIVE=1 AND d.COMPANY_CODE='".$company."' AND k.KODE_JENIS IN ('".$commodity_type."')";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row=NULL;
		}
        return $row;  
    }
	
	function get_storage_stock($id, $company, $commodity_type, $storage_num){
		$query="SELECT d.ID_BA, d.STRG_STOCK_DATE, d.ID_STORAGE, d.WEIGHT, d.FFA, d.MOISTURE, d.DIRT, 
 k.PRODUCT_CODE, COALESCE(a.OIL_RECOVERY,0) AS OIL_RECOVERY, COALESCE(a.SLUDGE,0) AS WRITE_OFF
FROM s_ba_storage_stock d
INNER JOIN m_storage k ON d.ID_STORAGE = k.ID_STORAGE
LEFT JOIN (SELECT ADJUST_DATE, ID_STORAGE, OIL_RECOVERY, SLUDGE FROM s_adjustment
WHERE COMPANY_CODE = '".$company."' AND ACTIVE =1 AND STATUS =1) a ON d.ID_STORAGE = a.ID_STORAGE AND d.STRG_STOCK_DATE = a.ADJUST_DATE
WHERE d.ID_BA = '".$id."'  AND d.ACTIVE=1 AND d.COMPANY_CODE='".$company."' AND k.PRODUCT_CODE IN ('".$commodity_type."') AND SUBSTRING(d.ID_STORAGE,-1)=".$storage_num."";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row = NULL;
		}
        return $row;  
    }
	
	function get_sounding_cpo($periode, $company, $commodity_type, $storage_num){
		$query="SELECT s.DATE, s.HEIGHT, s.TEMPERATURE, s.VOLUME, s.WEIGHT FROM s_sounding s
INNER JOIN m_storage k ON s.ID_STORAGE = k.ID_STORAGE
WHERE s.ACTIVE=1 AND s.COMPANY_CODE='".$company."' 
AND k.PRODUCT_CODE IN ('".$commodity_type."') AND SUBSTRING(s.ID_STORAGE,-1)=".$storage_num."
AND s.DATE = '".$periode."'";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }
        return $row;  
    }
	/*
	function get_sounding_kernel($periode, $company, $commodity_type, $storage_num){
		$query="SELECT s.DATE, s.HEIGHT, s.WEIGHT, s.HEIGHT2 FROM s_sounding_kernel s
INNER JOIN m_storage k ON s.ID_STORAGE = k.ID_STORAGE
WHERE s.ACTIVE=1 AND s.COMPANY_CODE='".$company."' 
AND k.PRODUCT_CODE IN ('".$commodity_type."') AND SUBSTRING(s.ID_STORAGE,-1)=".$storage_num."
AND s.DATE = '".$periode."'";
		
        $sQuery = $this->db->query($query);
        $row=array();
        if($sQuery==true){
            $row = $sQuery->row();   
        }else{
			$row = NULL;
		}
        return $row;  
    }
	*/
	function delete_ba($id_ba,$company){
        $id_ba = trim($this->db->escape_str($id_ba));
        $company = trim($this->db->escape_str($company));
        $status=FALSE;
        
        if(empty($id_ba)){
            $status = "id_ba CANNOT BE NULL !!";
        }
        
        if(empty($company)) {
            $status = "COMPANY_CODE CANNOT BE NULL !!";
        }
        
        $cek_data_exist = $this->cek_data_exist('s_ba',array('ID_BA'=>$id_ba),'ID_BA');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
        if(empty($status) || $status==FALSE){
            
            $this->db->where('ID_BA',$id_ba);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('UPDATE_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'UPDATE_TIME' =>  $this->global_func->gen_datetime(),
                    'ACTIVE'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_ba');
            if($this->db->trans_status() == FALSE){
                $status = $this->db->_error_message();//"Error in Transactions!!";
            }else{
				//start: delete production
				$this->db->where('ID_BA',$id_ba);
            	$this->db->where('COMPANY_CODE',$company);
             	$this->db->set($set);
            	$this->db->update('s_ba_production');
				//end: delete production
				//start: delete dispatch
				$this->db->where('ID_BA',$id_ba);
            	$this->db->where('COMPANY_CODE',$company);
            	$this->db->set($set);
            	$this->db->update('s_ba_dispatch');
				//end: delete dispatch
				//start: delete dispatch
				$this->db->where('ID_BA',$id_ba);
            	$this->db->where('COMPANY_CODE',$company);
            	$this->db->set($set);
            	$this->db->update('s_ba_storage_stock');
				//end: delete dispatch
				//start: delete dispatch
				$this->db->where('ID_BA',$id_ba);
            	$this->db->where('COMPANY_CODE',$company);
            	$this->db->set($set);
            	$this->db->update('s_ba_stock');
				//end: delete dispatch
                $status="Delete Data ID Berhasil"."\n";   
            }
        }
        
        return $status;
    } 
	/*
	function get_data_ba($company, $period, $f_day){
		$query="SELECT ba.BA_DATE, (ba.FFB_INTI+ ba.FFB_PLASMA + ba.FFB_SUPPLIER + ba.FFB_GROUP ) AS FFB, 
		(SELECT SUM(b.FFB_INTI) + SUM(b.FFB_PLASMA) + SUM(b.FFB_SUPPLIER) + SUM(b.FFB_GROUP) AS FFB_SHI FROM s_ba b WHERE b.BA_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND b.ACTIVE=1 AND b.COMPANY_CODE='".$company."') AS FFB_SHI,
ba.FFB_PROCESSED, 
(SELECT SUM(b.FFB_PROCESSED) AS FFB_SHI FROM s_ba b WHERE b.BA_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND b.ACTIVE=1 AND b.COMPANY_CODE='".$company."') AS FFB_PROCESSED_SHI, ((ba.BALANCE_YESTERDAY + ba.FFB_INTI+ ba.FFB_PLASMA + ba.FFB_SUPPLIER + ba.FFB_GROUP ) - ba.FFB_PROCESSED) AS BALANCE_YESTERDAY, PROD_CPO.WEIGHT AS CPO_PROD, 
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS CPO_PROD_SHI, 0 AS ER_CPO,
0 AS ER_CPO_SHI, PROD_CPO.FFA AS FFA_PROD, (STOCK_CPO1.WEIGHT*STOCK_CPO1.FFA+STOCK_CPO2.WEIGHT*STOCK_CPO2.FFA)/(STOCK_CPO1.WEIGHT+STOCK_CPO2.WEIGHT) AS FFA_STOCK, DISPATCH_CPO.WEIGHT AS DISPATCH_CPO, 
(SELECT SUM(d.WEIGHT) FROM s_ba_dispatch d LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND d.DISPATCH_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND d.COMPANY_CODE='".$company."') AS DISPATCH_CPO_SHI,
STOCK_CPO1.WEIGHT AS STOCK_CPO1, STOCK_CPO2.WEIGHT AS STOCK_CPO2, 
(SELECT SUM(str.WEIGHT) FROM s_ba_storage_stock str LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND str.STRG_STOCK_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND str.COMPANY_CODE='".$company."') AS STOCK_CPO_SHI, PROD_KERNEL.WEIGHT AS KERNEL_PROD, 
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'KRN' AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS KERNEL_PROD_SHI, 0 AS ER_KERNEL, 0 AS ER_KERNEL_SHI,
DISPATCH_KERNEL.WEIGHT AS DISPATCH_KERNEL, 
(SELECT SUM(d.WEIGHT) FROM s_ba_dispatch d LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'KRN' AND d.DISPATCH_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND d.COMPANY_CODE='".$company."') AS DISPATCH_KERNEL_SHI, STOCK_KERNEL1.WEIGHT AS STOCK_KERNEL1, STOCK_KERNEL2.WEIGHT AS STOCK_KERNEL2, ba.QC, ba.LABOR, ba.MILL_MANAGER, ba.KTU, ba.ADMINISTRATUR 
FROM s_ba ba
LEFT JOIN (
	SELECT d.ID_BA, d.WEIGHT FROM s_ba_dispatch d
	LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
	WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND DATE_FORMAT(d.DISPATCH_DATE,'%Y%m')='".$period."' AND d.COMPANY_CODE='".$company."'
) DISPATCH_CPO ON ba.ID_BA = DISPATCH_CPO.ID_BA
LEFT JOIN (
	SELECT d.ID_BA, d.WEIGHT FROM s_ba_dispatch d
	LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS
	WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'KRN' AND DATE_FORMAT(d.DISPATCH_DATE,'%Y%m')='".$period."' AND d.COMPANY_CODE='".$company."' 
	) DISPATCH_KERNEL ON ba.ID_BA = DISPATCH_KERNEL.ID_BA
LEFT JOIN(
	SELECT p.ID_BA, p.WEIGHT, p.FFA 
	FROM s_ba_production p
	LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
	WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND DATE_FORMAT(p.PRODUCTION_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."' 
	) PROD_CPO ON ba.ID_BA = PROD_CPO.ID_BA
LEFT JOIN(
	SELECT p.ID_BA, p.WEIGHT 
	FROM s_ba_production p
	LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS
	WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'KRN' AND DATE_FORMAT(p.PRODUCTION_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."' 
	) PROD_KERNEL ON ba.ID_BA = PROD_KERNEL.ID_BA
LEFT JOIN(
	SELECT str.ID_BA, str.WEIGHT, str.FFA 
	FROM s_ba_storage_stock str
	LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE
	WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND SUBSTRING(s.ID_STORAGE,-1) = 1 AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."'
) STOCK_CPO1 ON ba.ID_BA = STOCK_CPO1.ID_BA
LEFT JOIN(
	SELECT str.ID_BA, str.WEIGHT, str.FFA 
	FROM s_ba_storage_stock str
	LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE
	WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND SUBSTRING(s.ID_STORAGE,-1) = 2
	AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."'
) STOCK_CPO2 ON ba.ID_BA = STOCK_CPO2.ID_BA
LEFT JOIN(
	SELECT str.ID_BA, str.WEIGHT, str.FFA 
	FROM s_ba_storage_stock str
	LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE
	WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='KERNEL' AND SUBSTRING(s.ID_STORAGE,-1) = 1
	AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."'
) STOCK_KERNEL1 ON ba.ID_BA = STOCK_KERNEL1.ID_BA
LEFT JOIN(
	SELECT str.ID_BA, str.WEIGHT, str.FFA 
	FROM s_ba_storage_stock str
	LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE
	WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='KERNEL' AND SUBSTRING(s.ID_STORAGE,-1) = 2
	AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."'
) STOCK_KERNEL2 ON ba.ID_BA = STOCK_KERNEL2.ID_BA
WHERE ba.ACTIVE = 1 AND ba.COMPANY_CODE='".$company."' AND DATE_FORMAT(ba.BA_DATE,'%Y%m')='".$period."'
ORDER BY ba.BA_DATE";
		
        $sQuery = $this->db->query($query);
		$numrows = $sQuery->num_rows();
        if ($numrows > 0){
        	$temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
		}else{
			$temp_result = NULL;	
		}
        
		return $temp_result;
    }
	*/
	function get_ba_xls($company, $period, $f_day){
		$query = "SELECT ba.BA_DATE, 
CPO_GKM.WEIGHT AS CPO_GKM, 
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-GKMNRP' AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS CPO_GKM_SHI, 
CPO_GKM.FFA AS FFA_GKM, 
CPO_SMI.WEIGHT AS CPO_SMI, 
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-SMINRP' AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS CPO_SMI_SHI, 
CPO_SMI.FFA AS FFA_SMI, 
CPO_SUPPLIER.WEIGHT AS CPO_SUPPLIER, 
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-LUAR' AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS CPO_SUPPLIER_SHI, 
CPO_SUPPLIER.FFA AS FFA_SUPPLIER, 
CPO_GKM.WEIGHT + CPO_SMI.WEIGHT + CPO_SUPPLIER.WEIGHT AS TOTAL_RECEIPT,
(SELECT SUM(p.WEIGHT) FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS IN ('CPO-LUAR','CPO-SMINRP','CPO-GKMNRP') AND p.PRODUCTION_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND p.COMPANY_CODE='".$company."') AS TOTAL_RECEIPT_SHI,
DISPATCH_CPO.WEIGHT AS DISPATCH_CPO, 
COALESCE((SELECT SUM(d.WEIGHT) FROM s_ba_dispatch d LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND d.DISPATCH_DATE BETWEEN '".$f_day."' AND ba.BA_DATE AND d.COMPANY_CODE='".$company."'),0) - COALESCE((SELECT SUM(BERAT_BERSIH) AS BERAT_BERSIH FROM s_dispatch_return INNER JOIN s_komoditas ON s_komoditas.ID_KOMODITAS = s_dispatch_return.ID_KOMODITAS WHERE s_komoditas.JENIS ='CPO' AND s_dispatch_return.COMPANY_CODE='".$company."' AND s_dispatch_return.ACTIVE =1 AND DATE_FORMAT(s_dispatch_return.TANGGALM,'%Y%m%d') BETWEEN  '".$f_day."' AND ba.BA_DATE),0 ) AS DISPATCH_CPO_SHI, 																																					
COALESCE(DISPATCH_RETURN.BERAT_BERSIH,0) AS  DISPATCH_RETURN,	
STOCK_CPO.WEIGHT AS STOCK_CPO_TODAY,
(SELECT p.WEIGHT FROM s_ba_stock p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND p.STOCK_DATE = ba.BA_DATE - INTERVAL 1 DAY AND p.COMPANY_CODE='".$company."') AS STOCK_CPO_YESTERDAY,
COALESCE(STOCK_CPO1.WEIGHT,0) AS STOCK_CPO1, 																																																																
COALESCE(STOCK_CPO2.WEIGHT,0) AS STOCK_CPO2, 																																																																
COALESCE(STOCK_CPO3.WEIGHT,0) AS STOCK_CPO3, 																																																																
(COALESCE(STOCK_CPO1.WEIGHT,0) + COALESCE(STOCK_CPO2.WEIGHT,0) + COALESCE(STOCK_CPO3.WEIGHT,0) ) AS STOCK_CPO,
COALESCE(STOCK_CPO1.FFA,0) AS FFA_STOCK_CPO1, 	
COALESCE(STOCK_CPO2.FFA,0) AS FFA_STOCK_CPO2,
COALESCE(STOCK_CPO3.FFA,0) AS FFA_STOCK_CPO3,
((COALESCE(STOCK_CPO1.WEIGHT,0)*COALESCE(STOCK_CPO1.FFA,0))+(COALESCE(STOCK_CPO2.WEIGHT,0)*COALESCE(STOCK_CPO2.FFA,0))+(COALESCE(STOCK_CPO3.WEIGHT,0)*COALESCE(STOCK_CPO3.FFA,0)))/(COALESCE(STOCK_CPO1.WEIGHT,0)+COALESCE(STOCK_CPO2.WEIGHT,0)+COALESCE(STOCK_CPO3.WEIGHT,0)) AS FFA_STOCK,
ba.QC, ba.LABOR, 
ba.MILL_MANAGER, 
ba.KTU, 
ba.ADMINISTRATUR,
ba.COMPANY_CODE,
DATE_FORMAT(ba.INPUT_DATE, '%Y-%m-%d') AS INPUT_DATE,
DATE_FORMAT(ba.APPROVED_TIME, '%Y-%m-%d') AS APPROVED_DATE,
COALESCE((SELECT SUM(OIL_RECOVERY) FROM s_adjustment WHERE COMPANY_CODE = '".$company."' AND ADJUST_DATE = ba.BA_DATE AND ACTIVE = 1 AND STATUS = 1),0) AS OIL_RECOVERY,
COALESCE((SELECT SUM(SLUDGE)+ SUM(AIR) + SUM(EMULSI) FROM s_adjustment WHERE COMPANY_CODE = '".$company."' AND ADJUST_DATE = ba.BA_DATE AND ACTIVE = 1 AND STATUS = 1),0) AS WRITE_OFF
FROM s_ba ba 
	LEFT JOIN ( SELECT p.ID_BA, p.WEIGHT, p.FFA FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-GKMNRP' AND DATE_FORMAT(p.PRODUCTION_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."') CPO_GKM ON ba.ID_BA = CPO_GKM.ID_BA 
	LEFT JOIN ( SELECT p.ID_BA, p.WEIGHT, p.FFA FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-SMINRP' AND DATE_FORMAT(p.PRODUCTION_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."') CPO_SMI ON ba.ID_BA = CPO_SMI.ID_BA 
	LEFT JOIN ( SELECT p.ID_BA, p.WEIGHT, p.FFA FROM s_ba_production p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS LIKE 'CPO-LUAR' AND DATE_FORMAT(p.PRODUCTION_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."') CPO_SUPPLIER ON ba.ID_BA = CPO_SUPPLIER.ID_BA 
	LEFT JOIN ( SELECT d.ID_BA, d.WEIGHT FROM s_ba_dispatch d LEFT JOIN s_komoditas k ON d.ID_COMMODITY = k.ID_KOMODITAS WHERE d.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND DATE_FORMAT(d.DISPATCH_DATE,'%Y%m')='".$period."' AND d.COMPANY_CODE='".$company."') DISPATCH_CPO ON ba.ID_BA = DISPATCH_CPO.ID_BA 
	LEFT JOIN ( SELECT TANGGALM, SUM(BERAT_BERSIH) AS BERAT_BERSIH FROM s_dispatch_return INNER JOIN s_komoditas ON s_komoditas.ID_KOMODITAS = s_dispatch_return.ID_KOMODITAS WHERE s_komoditas.JENIS ='CPO' AND s_dispatch_return.COMPANY_CODE='".$company."' AND s_dispatch_return.ACTIVE =1 GROUP BY TANGGALM )	DISPATCH_RETURN ON ba.BA_DATE = DISPATCH_RETURN.TANGGALM
	LEFT JOIN( SELECT p.ID_BA, p.WEIGHT FROM s_ba_stock p LEFT JOIN s_komoditas k ON p.ID_COMMODITY = k.ID_KOMODITAS WHERE p.ACTIVE= 1 AND k.KODE_JENIS = 'CPO' AND DATE_FORMAT(p.STOCK_DATE,'%Y%m')='".$period."' AND p.COMPANY_CODE='".$company."') STOCK_CPO ON ba.ID_BA = STOCK_CPO.ID_BA 
	LEFT JOIN ( SELECT str.ID_BA, str.WEIGHT, str.FFA FROM s_ba_storage_stock str LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND SUBSTRING(s.ID_STORAGE,-1) = 1 AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."') STOCK_CPO1 ON ba.ID_BA = STOCK_CPO1.ID_BA 
	LEFT JOIN ( SELECT str.ID_BA, str.WEIGHT, str.FFA FROM s_ba_storage_stock str LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND SUBSTRING(s.ID_STORAGE,-1) = 2 AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."') STOCK_CPO2 ON ba.ID_BA = STOCK_CPO2.ID_BA 
	LEFT JOIN ( SELECT str.ID_BA, str.WEIGHT, str.FFA FROM s_ba_storage_stock str LEFT JOIN m_storage s ON str.ID_STORAGE = s.ID_STORAGE WHERE str.ACTIVE = 1 AND s.PRODUCT_CODE ='CPO' AND SUBSTRING(s.ID_STORAGE,-1) = 3 AND DATE_FORMAT(str.STRG_STOCK_DATE,'%Y%m')='".$period."' AND str.COMPANY_CODE='".$company."') STOCK_CPO3 ON ba.ID_BA = STOCK_CPO3.ID_BA 
WHERE ba.ACTIVE = 1 AND ba.COMPANY_CODE='".$company."' AND DATE_FORMAT(ba.BA_DATE,'%Y%m')='".$period."'
ORDER BY ba.BA_DATE";
		//var_dump($query);
		$sQuery = $this->db->query($query);
		$numrows = $sQuery->num_rows();
        if ($numrows > 0){
        	$temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
		}else{
			$temp_result = NULL;	
		}
		return $temp_result;
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
        
        $cek_data_exist = $this->cek_data_exist('s_ba',array('ID_BA'=>$id_ba,'BA_DATE'=>$ba_date),'ID_BA');
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
            $this->db->update( 's_ba');   
			$status="Approve berita acara tanggal " . $ba_date .  " berhasil"."\n";  
        }
        
        return $status;
    } 
	function reopen_ba($id_ba,$company, $ba_date){
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
        
        $cek_data_exist = $this->cek_data_exist('s_ba',array('ID_BA'=>$id_ba,'BA_DATE'=>$ba_date),'ID_BA');
        if ($cek_data_exist <= 0){
            $status ="DATA NOT EXIST !!";
        }
        
		$cek_status = $this->cek_status('s_ba',array('ID_BA'=>$id_ba,'BA_DATE'=>$ba_date),'STATUS');

        if ($cek_status == 0){
            $status ="BA  Tgl ".$ba_date." sudah direopen";
        }
		
        if(empty($status) || $status==FALSE){            
            $this->db->where('ID_BA',$id_ba);
			$this->db->where('BA_DATE',$ba_date);
            $this->db->where('COMPANY_CODE',$company);
            $set = array('REOPENED_BY'=>trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8')),
                    'REOPENED_TIME' =>  $this->global_func->gen_datetime(),
                    'STATUS'=>0
                    );
            $this->db->set($set);
            $this->db->update( 's_ba');   
			$status="Reopen berita acara tanggal " . $ba_date .  " berhasil"."\n";  
        }
        
        return $status;
    }
}
?>
