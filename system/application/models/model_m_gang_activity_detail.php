<?

class model_m_gang_activity_detail extends Model 
{

    function model_m_gang_activity_detail()
    {
        parent::Model(); 
		$this->load->library('global_func');
        $this->load->database();
    }
    
    function insert_m_gang_activity_detail ( $data )
    {
        $this->db->insert( 'm_gang_activity_detail', $data );
        
        return $this->db->insert_id();   
    } 
    
    function update_m_gang_activity_detail ( $id,$gc,$company,$tgl, $data )
    {
        $this->db->where( 'ID', $id );  
        $this->db->where( 'GANG_CODE', $gc );  
        $this->db->where( 'COMPANY_CODE', $company );  
        $this->db->where( 'DATE_FORMAT(LHM_DATE,"%Y-%m-%d")', $tgl );  
        $this->db->update( 'm_gang_activity_detail', $data );   
    }
            
    function delete_currlhm ( $id,$gc,$nik,$tgl,$company)
    {
        $this->db->where( 'ID', $id ); 
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'EMPLOYEE_CODE', $nik ); 
        $this->db->where( 'LHM_DATE', $tgl ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('m_gang_activity_detail');   
    }

    
    function read_exist_gad($tdate,$gc, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
       
        $periode = substr(str_replace("-","",$tdate),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			$sql2 = "SELECT ID, `GANG_CODE`, `LHM_DATE`, `EMPLOYEE_CODE` AS NIK, m_employee.NAMA AS NM_K, `TYPE_ABSENSI`, `LOCATION_TYPE_CODE`, `LOCATION_CODE`, `ACTIVITY_CODE`, HSL_KERJA_UNIT, HSL_KERJA_VOLUME, `HK_JUMLAH`, `LEMBUR_JAM`, TARIF_SATUAN, `PREMI`, PENALTI, `INPUT_BY`, `INPUT_DATE`, m_gang_activity_detail.`COMPANY_CODE`, `KONTANAN`, m_gang_activity_detail.NOTE FROM hist_m_gang_activity_detail m_gang_activity_detail LEFT JOIN `m_employee` ON (m_employee.`NIK` = `m_gang_activity_detail`.`EMPLOYEE_CODE` AND m_employee.COMPANY_CODE = m_gang_activity_detail.COMPANY_CODE AND m_employee.INACTIVE = 0) WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tdate."' AND m_gang_activity_detail.COMPANY_CODE = '".$company."'";
			
			$sql = $sql2;
			
		} else {
			$sql2 = "SELECT ID, `GANG_CODE`, `LHM_DATE`, `EMPLOYEE_CODE` AS NIK, m_employee.NAMA AS NM_K, `TYPE_ABSENSI`, `LOCATION_TYPE_CODE`, `LOCATION_CODE`, `ACTIVITY_CODE`, HSL_KERJA_UNIT, HSL_KERJA_VOLUME, `HK_JUMLAH`, `LEMBUR_JAM`, TARIF_SATUAN, `PREMI`, PENALTI, `INPUT_BY`, `INPUT_DATE`, m_gang_activity_detail.`COMPANY_CODE`, `KONTANAN`, m_gang_activity_detail.NOTE FROM m_gang_activity_detail LEFT JOIN `m_employee` ON (m_employee.`NIK` = `m_gang_activity_detail`.`EMPLOYEE_CODE` AND m_employee.COMPANY_CODE = m_gang_activity_detail.COMPANY_CODE AND m_employee.INACTIVE = 0) WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tdate."' AND EMPLOYEE_CODE NOT IN(SELECT pjm.`NIK` FROM p_pjm_karyawan pjm WHERE pjm.FROM = '".$gc."' AND DATE_FORMAT(pjm.`BDATE`,'%Y%m%d') = '".$tdate."') AND m_gang_activity_detail.COMPANY_CODE = '".$company."'";
			
			$sql = $sql2;
			
		}
       
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

        $this->db->limit($limit, $start); 
		
		if($count >0) { 
			$sql .= "ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $no = 1;
		$kontanan = "";
		foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LHM_DATE,ENT_QUOTES,'UTF-8'));  
            array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));                       
            array_push($cell, htmlentities($obj->NM_K,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TYPE_ABSENSI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
    if($obj->HK_JUMLAH == '0') { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->HK_JUMLAH,ENT_QUOTES,'UTF-8'));    }
    if($obj->HSL_KERJA_UNIT == '0') { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->HSL_KERJA_UNIT,ENT_QUOTES,'UTF-8')); } 
    //if($obj->HSL_KERJA_VOLUME == '0' ) { 
	//	array_push($cell, ""); 
	//} else { 
		array_push($cell, htmlentities($obj->HSL_KERJA_VOLUME,ENT_QUOTES,'UTF-8')); 
	//}
    if($obj->TARIF_SATUAN == '0' ) { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->TARIF_SATUAN,ENT_QUOTES,'UTF-8')); }
    if($obj->PREMI == '0' ) { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->PREMI,ENT_QUOTES,'UTF-8')); }
    if($obj->LEMBUR_JAM == '0' ) { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->LEMBUR_JAM,ENT_QUOTES,'UTF-8')); }
    if($obj->PENALTI == '0' ) { array_push($cell, ""); } else { array_push($cell, htmlentities($obj->PENALTI,ENT_QUOTES,'UTF-8')); }
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KONTANAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
		            
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
    
    /*look up*/
    function cek_mandor($gc, $company)
    {
        $gc=htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
         
        $query = $this->db->query("SELECT DISTINCT g.`GANG_CODE`,
	   g.`MANDORE1_CODE`, e3.NAMA NM_MANDOR1,
       g.`MANDORE_CODE`, e.`NAMA` NM_MANDOR,
       g.KERANI_CODE, e2.`NAMA` NM_KERANI,
       g.`DIVISION_CODE`, g.DESCRIPTION FROM
       `m_gang` g
left join m_employee e on (e.`NIK` = g.`MANDORE_CODE` AND e.`COMPANY_CODE` = g.`COMPANY_CODE`)
left join m_employee e2 on (e2.`NIK` = g.`KERANI_CODE` AND e2.`COMPANY_CODE` = g.`COMPANY_CODE`)
LEFT JOIN m_employee e3 ON (e3.`NIK` = g.`MANDORE1_CODE` AND e3.`COMPANY_CODE` = g.`COMPANY_CODE`)
WHERE g.GANG_CODE = '".$gc."' AND g.COMPANY_CODE = '".$company."' group by g.`GANG_CODE`");
		$temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
    	return $temp_result;
    }  
    
	function cek_exist_mandor($gc, $tgl, $company)
    {
        $gc=htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
         
        $query = $this->db->query("SELECT g.`GANG_CODE`,
	   g.`MANDORE1_CODE`,
       e3.NAMA NM_MANDOR1,
       g.`MANDORE_CODE`,
       e.`NAMA` NM_MANDOR,
       g.KERANI_CODE,
       e2.`NAMA` NM_KERANI,
       gg.`DIVISION_CODE`,
       gg.DESCRIPTION FROM
       `m_gang_activity_detail` g
left join m_gang gg on (gg.GANG_CODE = g.GANG_CODE AND gg.COMPANY_CODE = g.COMPANY_CODE)
left join m_employee e on (e.`NIK` = g.`MANDORE_CODE` AND e.`COMPANY_CODE` = g.`COMPANY_CODE`)
left join m_employee e2 on (e2.`NIK` = g.`KERANI_CODE` AND e2.`COMPANY_CODE` = g.`COMPANY_CODE`)
LEFT JOIN m_employee e3 ON (e3.`NIK` = g.`MANDORE1_CODE` AND e3.`COMPANY_CODE` = g.`COMPANY_CODE`)
WHERE g.GANG_CODE = '".$gc."' AND g.COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' 
group by g.`GANG_CODE`");

        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
		return $temp_result;
    }  
	   
    /*look up grid*/
    function cek_anggota($tgl, $gc, $company){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'EMPLOYEE_CODE';
        $sord = 'ASC';
        
        $date = strtotime($tgl);
        $month = date('m', $date );
        $year = date('Y', $date );
        
        $sql2 = "SELECT eg.`EMPLOYEE_CODE`,g.GANG_CODE FROM `m_gang` g
left join m_empgang eg on (eg.`GANG_CODE` = g.`GANG_CODE`)
left join ( SELECT NIK, NAMA, COMPANY_CODE, INACTIVE from m_employee where COMPANY_CODE = '".$company."' AND m_employee.INACTIVE = 0 ) e on (e.`NIK` = eg.`EMPLOYEE_CODE` )
WHERE g.GANG_CODE = '".$gc."' AND eg.COMPANY_CODE = '".$company."' AND CONCAT(eg.YEAR, eg.MONTH) = '".$year.$month."'
group by eg.`EMPLOYEE_CODE`";
       
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
 
		$sql = $sql2 ." ORDER BY ".$sidx." ".$sord."";
        
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();        
        
        $no = 1;        
        $LHM_DATE = "";
        $TYPE_ABSENSI = "";
        $LOCATION_TYPE_CODE = "";
        $LOCATION_CODE = "";
        $ACTIVITY_CODE = "";
        $HK_JUMLAH = "";
        $HSL_KERJA_UNIT = "";
        $HSL_KERJA_VOLUME = "";
        $LEMBUR_JAM = "";
        $TARIF_SATUAN = "";
        $PREMI = "";
        $PENALTI = "";
        $COMPANY_CODE = "";
		$KONTANAN = 0;
		$NOTE ="";
                            
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($LHM_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NM_K,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($TYPE_ABSENSI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($HK_JUMLAH,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($HSL_KERJA_UNIT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($HSL_KERJA_VOLUME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($TARIF_SATUAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($PREMI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($LEMBUR_JAM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($PENALTI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($KONTANAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($NOTE,ENT_QUOTES,'UTF-8'));
			
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
        
    function location_type($ltc)
    {
        $ltc=htmlentities($this->db->escape_str($ltc),ENT_QUOTES,'UTF-8');
         
        $query = $this->db->query("SELECT LOCATION_TYPE_CODE, LOCATION_TYPE_NAME FROM m_location_type WHERE LOCATION_TYPE_CODE like '".$ltc."%' AND ACTIVE=1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function satuan($q)
    {
        $q=htmlentities($this->db->escape_str($q),ENT_QUOTES,'UTF-8');
        
        $query = $this->db->query("SELECT UNIT_CODE, UNIT_DESC FROM m_satuan where UNIT_CODE like '".$q."%'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function search_nik($nik, $company)
    {
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $query = $this->db->query("SELECT NIK, NAMA FROM m_employee WHERE NIK like '".$nik."%' AND COMPANY_CODE='".$company."' AND INACTIVE = 0 OR NAMA like '".$nik."%' AND COMPANY_CODE='".$company."' AND INACTIVE = 0");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function gangc($company)
    {
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        //$q = $this->input->post('q');
        //$limit = $this->input->post('limit');
        
        $query = $this->db->query("SELECT GANG_CODE, DESCRIPTION FROM m_gang WHERE COMPANY_CODE = '".$company."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function type_absensi($q)
    {
        $q=htmlentities($this->db->escape_str($q),ENT_QUOTES,'UTF-8');
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8'); 
        
        $query = $this->db->query("SELECT TYPE_ABSENSI, TRIM(DESCRIPTION) AS DESCRIPTION FROM m_absensi where TYPE_ABSENSI like '".$q."%'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function location($q, $loc, $company)
    {
        $q=htmlentities($this->db->escape_str($q),ENT_QUOTES,'UTF-8');
        $loc=htmlentities($this->db->escape_str($loc),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
        $query = $this->db->query("SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_TYPE_CODE='".$loc."' AND LOCATION_CODE like '".$q."%' AND COMPANY_CODE = '".$company."' AND INACTIVE = 0 OR  LOCATION_TYPE_CODE='".$loc."' AND DESCRIPTION like '%".$q."%' AND COMPANY_CODE = '".$company."' AND INACTIVE = 0
			ORDER BY LOCATION_CODE");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function location_pj($q, $company)
    {
        $q=htmlentities($this->db->escape_str($q),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        
        $query = $this->db->query("SELECT PROJECT_ID AS LOCATION_CODE, CONCAT(PROJECT_DESC,' : ',PROJECT_LOCATION) AS DESCRIPTION FROM m_project 
            WHERE PROJECT_ID LIKE '".$q."%' AND COMPANY_CODE = '".$company."' AND PROJECT_STATUS = 1
			OR CONCAT(PROJECT_DESC,':',PROJECT_LOCATION) LIKE '%".$q."%' AND COMPANY_CODE = '".$company."' AND PROJECT_STATUS = 1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function activity_pj($ac, $lc, $company)
    {
        $ac=htmlentities($this->db->escape_str($ac),ENT_QUOTES,'UTF-8');
        $lc=htmlentities($this->db->escape_str($lc),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');  
        
        $query = $this->db->query("SELECT PROJECT_ACTIVITY AS ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION FROM m_project_detail 
        LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_detail.PROJECT_ACTIVITY) WHERE 
        MASTER_PROJECT_ID ='".$lc."' AND PROJECT_ACTIVITY like '".$ac."%' AND COMPANY_CODE = '".$company."' 
		OR MASTER_PROJECT_ID ='".$lc."' AND m_coa.COA_DESCRIPTION like '%".$ac."%' AND COMPANY_CODE = '".$company."' ORDER BY PROJECT_ACTIVITY asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function activity_pj_lctn($ac,$pj_subtype)
    {
        $ac=htmlentities($this->db->escape_str($ac),ENT_QUOTES,'UTF-8');
        $pj_subtype=htmlentities($this->db->escape_str($pj_subtype),ENT_QUOTES,'UTF-8');
        
        $query = $this->db->query("SELECT ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION 
FROM m_project_activity_map LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_activity_map.ACCOUNT_CODE) WHERE 
 LOCATION_SUBTYPE ='".$pj_subtype."' AND STATUS_PENGGUNAAN = 'LHM' AND ACCOUNTCODE like '".$ac."%' OR LOCATION_SUBTYPE ='".$pj_subtype."' AND STATUS_PENGGUNAAN = 'LHM' AND m_coa.COA_DESCRIPTION like '%".$ac."%' ORDER BY ACCOUNTCODE asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }  
    
    function activity($act, $lt)
    { 
        $act=htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8');
        $lt=htmlentities($this->db->escape_str($lt),ENT_QUOTES,'UTF-8');
        $query = $this->db->query("select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` from m_activity_map m left join `m_coa` on 
                (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) WHERE m.`ACCOUNT_CODE` like '%".$act."%' AND m.`ACCOUNT_CODE` NOT LIKE'965%' 
                    and m.STATUS_PENGGUNAAN = 'LHM' and m.LOCATION_TYPE = '".$lt."' OR m_coa.`COA_DESCRIPTION` like '%".$act."%' AND m.`ACCOUNT_CODE` NOT LIKE'965%' 
                    and m.STATUS_PENGGUNAAN = 'LHM' and m.LOCATION_TYPE = '".$lt."'");
        
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
    	return $temp_result;
    }
    
	function activity_sa(){
		$query="select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` 
				from m_activity_map m left join `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) 
				WHERE LOCATION_TYPE = 'SA' AND m.STATUS_PENGGUNAAN = 'LHM'";
                    
        $sQuery=$this->db->query($query);
        $temp_result = array();
        foreach($sQuery->result_array() as $row)
        {
            $temp_result [] = $row;     
        }
        return $temp_result;   
	}
	
    //############################### PANEN ##############################
    function activity_pn(){
        $query="select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` from m_activity_map m left join `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) WHERE m.`ACCOUNT_CODE` like '%860%' and m.STATUS_PENGGUNAAN = 'LHM'";
                    
        $sQuery=$this->db->query($query);
        $temp_result = array();
        foreach($sQuery->result_array() as $row)
        {
            $temp_result [] = $row;     
        }
        return $temp_result;   
    }
	
    function cek_aktifitas($akt)
    {
        $akt=htmlentities($this->db->escape_str($akt),ENT_QUOTES,'UTF-8');
        $query="select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` from m_activity_map m left join `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) WHERE m.`ACCOUNT_CODE`='".$akt."' and m.STATUS_PENGGUNAAN = 'LHM'";
                    
        $sQuery=$this->db->query($query);
        $num_rows=$sQuery->num_rows();
        
        return $num_rows;   
    }
    //############################### END PANEN ##############################
     
    function activity_vh($act, $lt)//15 nov 2010
    { 
        $act=htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8');
        $lt=htmlentities($this->db->escape_str($lt),ENT_QUOTES,'UTF-8');
        $query = $this->db->query("select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` from m_activity_map m left join `m_coa` on 
                (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) WHERE m.`ACCOUNT_CODE` like '%".$act."%' AND m.`ACCOUNT_CODE` REGEXP'^965'
                and m.STATUS_PENGGUNAAN = 'LHM' and m.LOCATION_TYPE = '".$lt."'");
        
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }

        return $temp_result;
    } 
    
    function cek_gad($tdate, $gangc, $company){
		$periode = substr(str_replace("-","",$tdate),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
        	$query = $this->db->query("select count(gang_code) as jumlah from hist_m_gang_activity_detail where GANG_CODE = '".$gangc."' and DATE_FORMAT(LHM_DATE, '%Y%m%d') = '".$tdate."' and COMPANY_CODE = '".$company."'");
		} else {
			$query = $this->db->query("select count(gang_code) as jumlah, SUM(CASE WHEN CLOSING_STATUS = 0 THEN 0 WHEN CLOSING_STATUS = '1' THEN 1 END) AS closing from m_gang_activity_detail where GANG_CODE = '".$gangc."' and DATE_FORMAT(LHM_DATE, '%Y%m%d') = '".$tdate."' and COMPANY_CODE = '".$company."'");
		}
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function delete_gad($id, $tdate, $gc, $company)
    {
        //$this->db->where('EMPLOYEE_CODE', $employee);
        $this->db->where('DATE_FORMAT(LHM_DATE, "%Y%m%d") = ', $tdate);
        $this->db->where('GANG_CODE', $gc);
        $this->db->where('COMPANY_CODE', $company);
        $this->db->delete('m_gang_activity_detail'); 
    }
    
    /* form pinjam meminjam */
    function insert_pjm_karyawan ( $data )
    {
        $this->db->insert( 'p_pjm_karyawan', $data );
        return $this->db->insert_id();   
    }
    
    function cek_kmandoran_asal ($nik,$tgl, $company )
    {
        $date = strtotime($tgl);
        $month = date('m', $date );
        $year = date('Y', $date );
        
        $query = $this->db->query("select DISTINCT eg.`GANG_CODE`, m_gang.`DESCRIPTION` from `m_empgang` eg
            left join m_gang on (m_gang.`GANG_CODE` = eg.`GANG_CODE`) where eg.`EMPLOYEE_CODE` = '".$nik."'
            AND CONCAT(eg.YEAR, eg.MONTH) = '".$year.$month."' AND eg.COMPANY_CODE = '".$company."' GROUP BY eg.GANG_CODE ");
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
		}    
        return $temp_result;  
    }
    
    /* cek from / to pinjaman*/
    function cek_pinjam ( $tdate, $gangc )
    {
    
        $query = $this->db->query("SELECT COUNT(NIK) as jumlah FROM p_pjm_karyawan WHERE p_pjm_karyawan.TO = '".$gangc."' AND DATE_FORMAT(BDATE, '%Y%m%d') = '".$tdate."' ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
    	}    
        return $temp_result;
    }
    
    function cek_anggota_pinjam($tgl, $gc, $company){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'NIK';
        $sord = 'ASC';
        
        $date = strtotime($tgl);
        $month = date('m', $date );
        $year = date('Y', $date );
        
        $sql2 = "SELECT eg.`EMPLOYEE_CODE` as NIK,g.GANG_CODE, e.NAMA as NM_K,
 (SELECT pjm.STATUS FROM p_pjm_karyawan pjm where
      pjm.GANG_CODE = '".$gc."' AND pjm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(pjm.`BDATE`, '%Y%m%d') =
      '".$tgl."') AS STATUS FROM `m_gang` g
LEFT JOIN m_empgang eg ON (eg.`GANG_CODE` = g.`GANG_CODE`)
LEFT JOIN ( SELECT NIK, NAMA, COMPANY_CODE, INACTIVE from m_employee where COMPANY_CODE = '".$company."' AND m_employee.INACTIVE = 0 ) e ON (e.`NIK` = eg.`EMPLOYEE_CODE`)
WHERE g.GANG_CODE = '".$gc."' AND eg.COMPANY_CODE = '".$company."'
AND CONCAT(eg.YEAR, eg.MONTH) = '".$year.$month."'
AND eg.`EMPLOYEE_CODE` NOT IN (SELECT pjm.`NIK` FROM p_pjm_karyawan pjm where pjm.FROM = '".$gc."' 
AND DATE_FORMAT(pjm.`BDATE`,'%Y%m%d') = '".$tgl."')
 UNION
SELECT pjm.NIK,pjm.TO,e.NAMA, pjm.STATUS FROM p_pjm_karyawan pjm
LEFT JOIN ( SELECT NIK, NAMA, COMPANY_CODE, INACTIVE from m_employee where COMPANY_CODE = '".$company."' AND m_employee.INACTIVE = 0 ) e ON (e.`NIK` = pjm.`NIK`)
WHERE pjm.TO = '".$gc."' AND DATE_FORMAT(pjm.`BDATE`,'%Y%m%d') = '".$tgl."' AND pjm.COMPANY_CODE = '".$company."'";
       
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

        
		$sql = $sql2 . " ORDER BY ".$sidx." ".$sord."";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
            $no = 1;            
                $LHM_DATE = "";
                $LOCATION_TYPE_CODE = "";
                $LOCATION_CODE = "";
                $ACTIVITY_CODE = "";
                $HSL_KERJA_UNIT = "";
                $HSL_KERJA_VOLUME = "";
                $HK_JUMLAH = "";
                $LEMBUR_JAM = "";
                $TARIF_SATUAN = "";
                $PREMI = "";
                $PENALTI = "";
                $COMPANY_CODE = "";
				$KONTANAN = 0;
				$NOTE = "";
                                    
        foreach($objects as $obj)
        {
            $cell = array();
                    array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($LHM_DATE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NM_K,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->STATUS,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($LOCATION_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($HK_JUMLAH,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($HSL_KERJA_UNIT,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($HSL_KERJA_VOLUME,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($TARIF_SATUAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($PREMI,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($LEMBUR_JAM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($PENALTI,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($COMPANY_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($KONTANAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($NOTE,ENT_QUOTES,'UTF-8'));
					
					

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
    
    /* grid pinjaman */
    function read_pinjaman($tgl , $gc, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'NIK';
        $sord = 'ASC';
        
        $sql2 = "select pjm.`GANG_CODE`,pjm.`BDATE`, pjm.`NIK`, m.`NAMA`,pjm.`FROM`, pjm.`TO`, pjm.`COMPANY_CODE`,pjm.`STATUS` from `p_pjm_karyawan` pjm left join m_employee m on (m.`NIK` = pjm.NIK) where DATE_FORMAT(pjm.BDATE,'%Y%m%d') = '".$tgl."' AND pjm.GANG_CODE = '".$gc."' AND pjm.COMPANY_CODE = '".$company."'";

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
    
$sql = "select pjm.`GANG_CODE`,pjm.`BDATE`, pjm.`NIK`, m.`NAMA`,pjm.`FROM`, pjm.`TO`, pjm.`COMPANY_CODE`,pjm.`STATUS` from `p_pjm_karyawan` pjm left join m_employee m on (m.`NIK` = pjm.NIK) where DATE_FORMAT(pjm.BDATE,'%Y%m%d') = '".$tgl."' AND pjm.GANG_CODE = '".$gc."' AND pjm.COMPANY_CODE = '".$company."' ORDER BY ".$sidx." ".$sord."";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
        $no = 1;            
        $action = "";                        
        foreach($objects as $obj)
        {
            $cell = array();
                    array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->BDATE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->FROM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->TO,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->STATUS,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($action,ENT_QUOTES,'UTF-8'));
            
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
    
    function update_pjm( $gc,$nik,$tgl,$company, $data )
    {
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'NIK', $nik ); 
        $this->db->where( 'BDATE', $tgl ); 
        $this->db->where( 'COMPANY_CODE', $company );       
        $this->db->update( 'p_pjm_karyawan', $data );   
    }
    
    function delete_pjm ( $gc,$nik,$tgl,$company)
    {
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'NIK', $nik ); 
        $this->db->where( 'BDATE', $tgl ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('p_pjm_karyawan');   
    }
    
    // validasi    
    function absen_validate($absen){
        $query = $this->db->query("SELECT TYPE_ABSENSI FROM m_absensi where TYPE_ABSENSI = '".$absen."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function lokasi_validate($lc, $ltc, $company){
        $query = $this->db->query("SELECT LOCATION_CODE FROM m_location where TRIM(LOCATION_TYPE_CODE) = TRIM('".$ltc."') AND TRIM(LOCATION_CODE) = TRIM('".$lc."') AND COMPANY_CODE = '".$company."' AND INACTIVE = 0");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function lokasi_project_validate($lc, $company){
        $query = $this->db->query("SELECT PROJECT_ID FROM m_project where TRIM(PROJECT_ID) = TRIM('".$lc."') AND COMPANY_CODE = '".$company."' AND PROJECT_STATUS = 1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
        
    function aktivitas_validate($ac, $ltc){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_activity_map where LOCATION_TYPE = '".$ltc."' AND ACCOUNT_CODE = '".$ac."' AND STATUS_PENGGUNAAN = 'LHM'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
        
    function project_activity_validate($pj, $ac, $company){
        $query = $this->db->query("SELECT PROJECT_ACTIVITY FROM m_project_detail WHERE MASTER_PROJECT_ID = '".$pj."' AND PROJECT_ACTIVITY = '".$ac."' AND COMPANY_CODE = '".$company."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function projectlctn_activity_validate($pj_subtype, $ac){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_project_activity_map WHERE LOCATION_SUBTYPE = '".$pj_subtype."' AND ACCOUNT_CODE = '".$ac."' AND STATUS_PENGGUNAAN = 'LHM'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    /* #################### progress ###################### */
    
    function get_progress ($tgl, $gc, $company) {
       $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'ACTIVITY_CODE';
        $sord = 'ASC';  
		$isPanen = FALSE;
		/*
		$sql2 = "SELECT ID_PROGRESS AS IDP, GANG_CODE, TGL_PROGRESS AS LHM_DATE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, 
SATUAN AS UNIT1, HASIL_KERJA2 AS NILAI2, SATUAN2 AS UNIT2, HK,REALISASI  FROM p_progress
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress.ACTIVITY_CODE
WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
UNION
SELECT ID AS IDP, GANG_CODE,  gad.LHM_DATE,  gad.ACTIVITY_CODE, m_coa.COA_DESCRIPTION, gad.LOCATION_CODE, '' AS NILAI, pm.UNIT1, '' AS NILAI2, pm.UNIT2 AS UNIT2, b.HKE_JUMLAH, BIAYA
FROM m_gang_activity_detail gad
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  gad.ACTIVITY_CODE
LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE =  gad.ACTIVITY_CODE
LEFT JOIN ( SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, SUM(HKE_JUMLAH) AS HKE_JUMLAH, SUM(HKE_bYR) + SUM(PREMI_LEMBUR) AS BIAYA  FROM ( SELECT lhm.LHM_DATE, 
    lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
    emp.GP, 
    lhm.HK_JUMLAH AS HKE_JUMLAH,
    CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL','KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
     WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
     WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
    ELSE 0     
     END AS HKE_BYR,
    ( COALESCE(lhm.LEMBUR_RUPIAH,0) + lhm.PREMI) - lhm.PENALTI AS PREMI_LEMBUR, lhm.COMPANY_CODE
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE AND emp.INACTIVE = 0
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') = '".$tgl."' 
AND lhm.TYPE_ABSENSI <> '' AND lhm.GANG_CODE = '".$gc."' ) biaya 
GROUP BY lhm_date, LOCATION_CODE, ACTIVITY_CODE ) b ON b.LHM_DATE =  gad.LHM_DATE AND b.LOCATION_CODE = gad.LOCATION_CODE AND b.ACTIVITY_CODE = gad .ACTIVITY_CODE
WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT( gad.LHM_DATE,'%Y%m%d') = '".$tgl."' AND  gad.LOCATION_CODE <> '' AND  gad.COMPANY_CODE = '".$company."'
AND CONCAT(gad.ACTIVITY_CODE, gad.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
WHERE p_progress.GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' )
GROUP BY ACTIVITY_CODE, LOCATION_CODE";
*/
        $isPanen = $this->cek_kemandoran_panen($gc, $company);
		if ($isPanen==TRUE){//start: isPanen==TRUE
			if ($company=='MAG' || $company=='LIH' || $company=='GKM' || $company=='SML' || $company=='NAK' || $company=='TPAI'){
			$sql2 = "SELECT ID_PROGRESS AS IDP, GANG_CODE, TGL_PROGRESS AS LHM_DATE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, 
	SATUAN AS UNIT1, HASIL_KERJA2 AS NILAI2, SATUAN2 AS UNIT2, HK,REALISASI  FROM p_progress
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress.ACTIVITY_CODE
	WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
	UNION
	SELECT ID AS IDP, GANG_CODE,  gad.LHM_DATE,  gad.ACTIVITY_CODE, m_coa.COA_DESCRIPTION, gad.LOCATION_CODE, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN NAB.BERAT_ANGKUT ELSE '' END AS NILAI, pm.UNIT1, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN b.HSL_KERJA_VOLUME ELSE '' END AS NILAI2, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN b.HSL_KERJA_UNIT ELSE pm.UNIT2 END AS UNIT2, b.HKE_JUMLAH, BIAYA
	FROM m_gang_activity_detail gad
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  gad.ACTIVITY_CODE
	LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE =  gad.ACTIVITY_CODE
	LEFT JOIN ( SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, SUM(HKE_JUMLAH) AS HKE_JUMLAH, SUM(HKE_bYR) + SUM(PREMI_LEMBUR) AS BIAYA, SUM(HSL_KERJA_VOLUME) AS HSL_KERJA_VOLUME, HSL_KERJA_UNIT   FROM ( SELECT lhm.LHM_DATE, 
		lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
		emp.GP, 
		lhm.HK_JUMLAH AS HKE_JUMLAH,
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL','KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
		ELSE 0     
		 END AS HKE_BYR,
		( COALESCE(lhm.LEMBUR_RUPIAH,0) + lhm.PREMI) - lhm.PENALTI AS PREMI_LEMBUR, lhm.COMPANY_CODE, lhm.HSL_KERJA_VOLUME, lhm.HSL_KERJA_UNIT
	FROM m_gang_activity_detail lhm
	LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE AND emp.INACTIVE = 0
	WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') = '".$tgl."' 
	AND lhm.TYPE_ABSENSI <> '' AND lhm.GANG_CODE = '".$gc."' ) biaya 
	GROUP BY lhm_date, LOCATION_CODE, ACTIVITY_CODE ) b ON b.LHM_DATE =  gad.LHM_DATE AND b.LOCATION_CODE = gad.LOCATION_CODE AND b.ACTIVITY_CODE = gad .ACTIVITY_CODE
	LEFT JOIN (
	SELECT nab.DATE_TRANSACT,nab.LOCATION_CODE,nab.BERAT_ANGKUT FROM rpt_nab nab
	WHERE nab.COMPANY_CODE='".$company."' AND DATE_FORMAT(nab.DATE_TRANSACT, '%Y%m%d') = '".$tgl."'
	) NAB ON NAB.DATE_TRANSACT =  gad.LHM_DATE AND NAB.LOCATION_CODE = gad.LOCATION_CODE
	WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT( gad.LHM_DATE,'%Y%m%d') = '".$tgl."' AND  gad.LOCATION_CODE <> '' AND  gad.COMPANY_CODE = '".$company."'
	AND CONCAT(gad.ACTIVITY_CODE, gad.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
	WHERE p_progress.GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' )
	GROUP BY ACTIVITY_CODE, LOCATION_CODE
	UNION
	SELECT IDP, GANG_CODE, LHM_DATE, ACTIVITY_CODE, COA_DESCRIPTION, LOCATION_CODE, NILAI, UNIT, NILAI2, UNIT2, HK, REALISASI
	FROM(
		SELECT restan.TRANSACT_ID AS IDP, '".$gc."' AS GANG_CODE,  restan.DATE_TRANSACT AS LHM_DATE, '8601003' AS ACTIVITY_CODE, 'PANEN' AS COA_DESCRIPTION, 
		restan.LOCATION_CODE, restan.BERAT_ANGKUT AS NILAI, 'Kg' AS UNIT, restan.JANJANG_ANGKUT AS NILAI2, 'Jjg' AS UNIT2, 0 AS HK, 0 AS REALISASI, LEFT(restan.LOCATION_CODE,2) AS AFD   
		FROM rpt_nab restan 
		WHERE DATE_FORMAT(restan.DATE_TRANSACT,'%Y%m%d') = '".$tgl."' AND restan.COMPANY_CODE = '".$company."' 
		AND CONCAT('8601003', restan.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
		WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' 
		AND p_progress.`ACTIVITY_CODE`='8601003') 
	)RESTAN INNER JOIN (
		SELECT AFD FROM m_gang WHERE COMPANY_CODE='".$company."' AND GANG_CODE = '".$gc."'
	) m_gang ON RESTAN.AFD = m_gang.AFD";  
			}else if ($company=='SAP' || $company=='MSS' || $company=='SSS' || $company=='ASL'){
				$sql2 = "SELECT ID_PROGRESS AS IDP, GANG_CODE, TGL_PROGRESS AS LHM_DATE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, 
	SATUAN AS UNIT1, HASIL_KERJA2 AS NILAI2, SATUAN2 AS UNIT2, HK,REALISASI  FROM p_progress
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress.ACTIVITY_CODE
	WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
	UNION
	SELECT ID AS IDP, GANG_CODE,  gad.LHM_DATE,  gad.ACTIVITY_CODE, m_coa.COA_DESCRIPTION, gad.LOCATION_CODE, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN NAB.BERAT_PANEN ELSE '' END AS NILAI, pm.UNIT1, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN b.HSL_KERJA_VOLUME ELSE '' END AS NILAI2, CASE WHEN gad.ACTIVITY_CODE = '8601003' THEN b.HSL_KERJA_UNIT ELSE pm.UNIT2 END AS UNIT2, b.HKE_JUMLAH, BIAYA
	FROM m_gang_activity_detail gad
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  gad.ACTIVITY_CODE
	LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE =  gad.ACTIVITY_CODE
	LEFT JOIN ( SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, SUM(HKE_JUMLAH) AS HKE_JUMLAH, SUM(HKE_bYR) + SUM(PREMI_LEMBUR) AS BIAYA, SUM(HSL_KERJA_VOLUME) AS HSL_KERJA_VOLUME, HSL_KERJA_UNIT   FROM ( SELECT lhm.LHM_DATE, 
		lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
		emp.GP, 
		lhm.HK_JUMLAH AS HKE_JUMLAH,
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL','KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
		ELSE 0     
		 END AS HKE_BYR,
		( COALESCE(lhm.LEMBUR_RUPIAH,0) + lhm.PREMI) - lhm.PENALTI AS PREMI_LEMBUR, lhm.COMPANY_CODE, lhm.HSL_KERJA_VOLUME, lhm.HSL_KERJA_UNIT
	FROM m_gang_activity_detail lhm
	LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE AND emp.INACTIVE = 0
	WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') = '".$tgl."' 
	AND lhm.TYPE_ABSENSI <> '' AND lhm.GANG_CODE = '".$gc."' ) biaya 
	GROUP BY lhm_date, LOCATION_CODE, ACTIVITY_CODE ) b ON b.LHM_DATE =  gad.LHM_DATE AND b.LOCATION_CODE = gad.LOCATION_CODE AND b.ACTIVITY_CODE = gad .ACTIVITY_CODE
	LEFT JOIN (
	SELECT nab.DATE_TRANSACT,nab.LOCATION_CODE,nab.BERAT_PANEN FROM rpt_nab nab
	WHERE nab.COMPANY_CODE='".$company."' AND DATE_FORMAT(nab.DATE_TRANSACT, '%Y%m%d') = '".$tgl."'
	) NAB ON NAB.DATE_TRANSACT =  gad.LHM_DATE AND NAB.LOCATION_CODE = gad.LOCATION_CODE
	WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT( gad.LHM_DATE,'%Y%m%d') = '".$tgl."' AND  gad.LOCATION_CODE <> '' AND  gad.COMPANY_CODE = '".$company."'
	AND CONCAT(gad.ACTIVITY_CODE, gad.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
	WHERE p_progress.GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' )
	GROUP BY ACTIVITY_CODE, LOCATION_CODE
	UNION
	SELECT IDP, GANG_CODE, LHM_DATE, ACTIVITY_CODE, COA_DESCRIPTION, LOCATION_CODE, NILAI, UNIT, NILAI2, UNIT2, HK, REALISASI
	FROM(
		SELECT restan.TRANSACT_ID AS IDP, '".$gc."' AS GANG_CODE,  restan.DATE_TRANSACT AS LHM_DATE, '8601003' AS ACTIVITY_CODE, 'PANEN' AS COA_DESCRIPTION, 
		restan.LOCATION_CODE, restan.BERAT_ANGKUT AS NILAI, 'Kg' AS UNIT, restan.JANJANG_ANGKUT AS NILAI2, 'Jjg' AS UNIT2, 0 AS HK, 0 AS REALISASI, LEFT(restan.LOCATION_CODE,2) AS AFD   
		FROM rpt_nab restan 
		WHERE DATE_FORMAT(restan.DATE_TRANSACT,'%Y%m%d') = '".$tgl."' AND restan.COMPANY_CODE = '".$company."' 
		AND CONCAT('8601003', restan.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
		WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' 
		AND p_progress.`ACTIVITY_CODE`='8601003') 
	)RESTAN INNER JOIN (
		SELECT AFD FROM m_gang WHERE COMPANY_CODE='".$company."' AND GANG_CODE = '".$gc."'
	) m_gang ON RESTAN.AFD = m_gang.AFD"; 	
			}
		} // end: isPanen==TRUE
		else{ // start: isPanen==FLASE
			$sql2 = "SELECT ID_PROGRESS AS IDP, GANG_CODE, TGL_PROGRESS AS LHM_DATE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, 
SATUAN AS UNIT1, HASIL_KERJA2 AS NILAI2, SATUAN2 AS UNIT2, HK,REALISASI  FROM p_progress
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress.ACTIVITY_CODE
WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
UNION
SELECT ID AS IDP, GANG_CODE,  gad.LHM_DATE,  gad.ACTIVITY_CODE, m_coa.COA_DESCRIPTION, gad.LOCATION_CODE, '' AS NILAI, pm.UNIT1, '' AS NILAI2, pm.UNIT2 AS UNIT2, b.HKE_JUMLAH, BIAYA
FROM m_gang_activity_detail gad
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  gad.ACTIVITY_CODE
LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE =  gad.ACTIVITY_CODE
LEFT JOIN ( SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, SUM(HKE_JUMLAH) AS HKE_JUMLAH, SUM(HKE_bYR) + SUM(PREMI_LEMBUR) AS BIAYA  FROM ( SELECT lhm.LHM_DATE, 
    lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
    emp.GP, 
    lhm.HK_JUMLAH AS HKE_JUMLAH,
    CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL','KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
     WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
     WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
    ELSE 0     
     END AS HKE_BYR,
    ( COALESCE(lhm.LEMBUR_RUPIAH,0) + lhm.PREMI) - lhm.PENALTI AS PREMI_LEMBUR, lhm.COMPANY_CODE
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE AND emp.INACTIVE = 0
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') = '".$tgl."' 
AND lhm.TYPE_ABSENSI <> '' AND lhm.GANG_CODE = '".$gc."' ) biaya 
GROUP BY lhm_date, LOCATION_CODE, ACTIVITY_CODE ) b ON b.LHM_DATE =  gad.LHM_DATE AND b.LOCATION_CODE = gad.LOCATION_CODE AND b.ACTIVITY_CODE = gad .ACTIVITY_CODE
WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT( gad.LHM_DATE,'%Y%m%d') = '".$tgl."' AND  gad.LOCATION_CODE <> '' AND  gad.COMPANY_CODE = '".$company."'
AND CONCAT(gad.ACTIVITY_CODE, gad.LOCATION_CODE) NOT IN (SELECT CONCAT(p_progress.ACTIVITY_CODE,p_progress.LOCATION_CODE) FROM p_progress
WHERE p_progress.GANG_CODE = '".$gc."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND p_progress.COMPANY_CODE = '".$company."' )
GROUP BY ACTIVITY_CODE, LOCATION_CODE";	
		}//end: isPanen==FLASE

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

        
		$sql = $sql2;
		
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord."";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
        $no = 1;            
        $LHM_DATE = "";
        $LOCATION_CODE = "";
        $ACTIVITY_CODE = "";
        $COA_DESCRIPTION = "";
        $NILAI = "";
        $UNIT1 = "";
                            
                                    
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->IDP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LHM_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COA_DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NILAI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->UNIT1,ENT_QUOTES,'UTF-8'));
			/* update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */
			array_push($cell, htmlentities($obj->NILAI2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->UNIT2,ENT_QUOTES,'UTF-8'));
			/* end update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */
            array_push($cell, htmlentities($obj->HK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->REALISASI,ENT_QUOTES,'UTF-8')); 
                    
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
	
    function cek_kemandoran_panen ($gc, $company){
		$company = $this->db->escape_str($company);
        $query="SELECT ISPANEN FROM m_gang g WHERE GANG_CODE='".$gc."' AND g.COMPANY_CODE='".$company."'";
        $sQuery = $this->db->query($query);
        $value='';
		$boolean_panen = FALSE;
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row();       
            $value = $row->ISPANEN; 
			if ($value==1 || $value=='1'){
				$boolean_panen = TRUE;
			}
        }
        return $boolean_panen;  
    }
	
    function insert_p_progress ( $data )
    {
        $this->db->insert( 'p_progress', $data );
        return $this->db->insert_id();   
    }
    
    function update_p_progress ( $gc,$tgl_progress,$activity,$location,$company, $data )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8'); 
        $tgl_progress=htmlentities($this->db->escape_str($tgl_progress),ENT_QUOTES,'UTF-8'); 
        $activity =htmlentities($this->db->escape_str($activity),ENT_QUOTES,'UTF-8'); 
        $location=htmlentities($this->db->escape_str($location),ENT_QUOTES,'UTF-8'); 
        $company =htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where('GANG_CODE',$gc);
        $this->db->where('TGL_PROGRESS',$tgl_progress);
        $this->db->where('ACTIVITY_CODE',$activity);
        $this->db->where('LOCATION_CODE',$location);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update( 'p_progress', $data );   
    }
    
    function delete_p_progress ( $gc, $idp, $tgl, $act, $lc, $company )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $idp=htmlentities($this->db->escape_str($idp),ENT_QUOTES,'UTF-8'); 
        $tgl=htmlentities($this->db->escape_str($tgl),ENT_QUOTES,'UTF-8'); 
        $act =htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8'); 
        $lc=htmlentities($this->db->escape_str($lc),ENT_QUOTES,'UTF-8'); 
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'ID_PROGRESS', $idp ); 
        $this->db->where( 'TGL_PROGRESS', $tgl ); 
        $this->db->where( 'ACTIVITY_CODE', $act ); 
        $this->db->where( 'LOCATION_CODE', $lc ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('p_progress');   
    }
	
	function cetak_pdf_header($gc,$company){
		$gc = trim($this->db->escape_str($gc));
		$company = trim($this->db->escape_str($company));
		
		$header="SELECT m_gang.GANG_CODE, m_gang.MANDORE_CODE,emp.NAMA
				FROM m_gang
				LEFT JOIN m_employee emp ON emp.NIK = m_gang.MANDORE_CODE
					AND emp.COMPANY_CODE = m_gang.COMPANY_CODE
				WHERE m_gang.COMPANY_CODE ='".$company."'
					AND m_gang.GANG_CODE='".$gc."'";
		$sQuery = $this->db->query($header);
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
	
	function cetak_pdf_body($gc,$periode,$company){
		$gc = trim($this->db->escape_str($gc));
		$periode = trim($this->db->escape_str($periode));
		$company = trim($this->db->escape_str($company));
		
		$body="SELECT mgad.EMPLOYEE_CODE, emp.NAMA, mgad.TYPE_ABSENSI, mgad.LOCATION_TYPE_CODE, mgad.LOCATION_CODE, 	mgad.ACTIVITY_CODE,
				coa.COA_DESCRIPTION, mgad.HK_JUMLAH, mgad.TARIF_SATUAN, mgad.PREMI, mgad.LEMBUR_JAM, mgad.PENALTI 
			FROM m_gang_activity_detail mgad
			LEFT JOIN m_employee emp ON emp.NIK = mgad.EMPLOYEE_CODE
				AND emp.COMPANY_CODE = mgad.COMPANY_CODE
			LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = mgad.ACTIVITY_CODE
			WHERE mgad.COMPANY_CODE = '".$company."' AND mgad.GANG_COdE = '".$gc."'
				AND DATE_FORMAT(mgad.LHM_DATE,'%Y%m%d') = DATE_FORMAT('".$periode."','%Y%m%d') ";
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
	
	function cek_duplikat($nik, $tgl, $typeabs, $act, $loc, $company){
		$body="SELECT mgad.EMPLOYEE_CODE, mgad.TYPE_ABSENSI, mgad.GANG_CODE, mgad.LHM_DATE, mgad.LOCATION_TYPE_CODE, mgad.LOCATION_CODE, 			mgad.ACTIVITY_CODE FROM m_gang_activity_detail mgad
			WHERE mgad.COMPANY_CODE = '".$company."' AND mgad.EMPLOYEE_CODE = '".$nik."'
				AND mgad.LHM_DATE = '".$tgl."' AND mgad.TYPE_ABSENSI = '".$typeabs."'
				AND mgad.LOCATION_CODE = '".$loc."' AND mgad.ACTIVITY_CODE = '".$act."'";
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
	
	function getlembur($nik,$tgl,$jamlembur,$company){
		$query="SELECT ret_lembur('".$nik."','".$tgl."','".$jamlembur."','".$company."') AS LEMBUR";	
		$sQuery = $this->db->query($query);
		$rowcount = $sQuery->num_rows();
		$temp_result = array();
		$ret = "";
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                //$temp_result[] = $row;
            	 $ret = $row['LEMBUR'];
			}
        }
        return $ret;
	}
	
	function getgp($nik,$company){
		$query="SELECT ret_gp('".$nik."','".$company."') AS GP";	
		$sQuery = $this->db->query($query);
		$rowcount = $sQuery->num_rows();
		$temp_result = array();
		$ret = "";
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                //$temp_result[] = $row;
            	 $ret = $row['GP'];
			}
        }
        return $ret;
	}
	
	/* ### material ### */
	
    function get_material ($tgl, $gc, $company) {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'ACTIVITY_CODE';
        $sord = 'ASC';    
        $sql2 = "SELECT gm.LHM_MATERIAL_ID, gm.GANG_CODE, gm.LHM_DATE, gm.MATERIAL_CODE, 
	gm.MATERIAL_QTY, mat.MATERIAL_NAME, mat.MATERIAL_UOM, gm.ACTIVITY_CODE,gm.LOCATION_CODE, gm.MATERIAL_BPB_NO, gm.COMPANY_CODE 
FROM m_gang_activity_detail_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
WHERE gm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gm.LHM_DATE,'%Y%m%d') = '".$tgl."' AND gm.GANG_CODE = '".$gc."'";   
       
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

        
$sql = "SELECT gm.LHM_MATERIAL_ID, gm.GANG_CODE, gm.LHM_DATE, gm.MATERIAL_CODE, 
	gm.MATERIAL_QTY, mat.MATERIAL_NAME, mat.MATERIAL_UOM, gm.ACTIVITY_CODE,gm.LOCATION_CODE, gm.MATERIAL_BPB_NO, gm.COMPANY_CODE 
FROM m_gang_activity_detail_material gm
LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
WHERE gm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gm.LHM_DATE,'%Y%m%d') = '".$tgl."' AND gm.GANG_CODE = '".$gc."'";
		
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord."";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
        $no = 1;                                      
                                    
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->LHM_MATERIAL_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LHM_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_NAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_QTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MATERIAL_UOM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MATERIAL_BPB_NO,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    
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
    
    function insert_material ( $data )
    {
        $this->db->insert( 'm_gang_activity_detail_material', $data );
        return $this->db->insert_id();   
    }
    
    function update_material ( $matid, $gc,$tgl_material,$activity,$location,$company, $data )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8'); 
        $tgl_material=htmlentities($this->db->escape_str($tgl_material),ENT_QUOTES,'UTF-8'); 
        $activity =htmlentities($this->db->escape_str($activity),ENT_QUOTES,'UTF-8'); 
        $location=htmlentities($this->db->escape_str($location),ENT_QUOTES,'UTF-8'); 
        $company =htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
		$this->db->where('LHM_MATERIAL_ID',$matid);
        $this->db->where('GANG_CODE',$gc);
        $this->db->where('LHM_DATE',$tgl_material);
        $this->db->where('ACTIVITY_CODE',$activity);
        $this->db->where('LOCATION_CODE',$location);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update( 'm_gang_activity_detail_material', $data );   
    }
    
    function delete_material ( $gc, $idp, $tgl, $act, $mat, $lc, $company )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $idp=htmlentities($this->db->escape_str($idp),ENT_QUOTES,'UTF-8'); 
        $tgl=htmlentities($this->db->escape_str($tgl),ENT_QUOTES,'UTF-8'); 
        $act =htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8'); 
        $lc=htmlentities($this->db->escape_str($lc),ENT_QUOTES,'UTF-8'); 
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'LHM_MATERIAL_ID', $idp );
		$this->db->where( 'MATERIAL_CODE', $mat ); 
        $this->db->where( 'LHM_DATE', $tgl ); 
        $this->db->where( 'ACTIVITY_CODE', $act ); 
        $this->db->where( 'LOCATION_CODE', $lc ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('m_gang_activity_detail_material');   
    }
	
	function cek_exist_data($gangcode,$date,$mc,$activity,$location,$company)
    {
        $sQuery = "SELECT * FROM m_gang_activity_detail_material 
		WHERE GANG_CODE='".$gangcode."' AND LHM_DATE = '".$date."' AND MATERIAL_CODE = '".$mc."' 
		AND ACTIVITY_CODE = '".$activity."' AND LOCATION_CODE = '".$location."' AND COMPANY_CODE ='".$company."'";
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
    }
	
		function mgetActMaterial($gc, $tgl, $act){
		$query = $this->db->query("SELECT DISTINCT ACTIVITY_CODE, m_coa.COA_DESCRIPTION FROM m_gang_activity_detail gad
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = gad.ACTIVITY_CODE
WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' AND ACTIVITY_CODE LIKE '".$act."%'
OR GANG_CODE = '".$gc."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' AND m_coa.COA_DESCRIPTION LIKE '%".$act."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function mgetLocMaterial($gc, $tgl, $act, $loc){
		$query = $this->db->query("SELECT DISTINCT LOCATION_CODE FROM m_gang_activity_detail gad
			WHERE GANG_CODE = '".$gc."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' AND ACTIVITY_CODE LIKE '".$act."%' 
			AND LOCATION_CODE LIKE '".$loc."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function mgetMaterial($q){
		$query = $this->db->query("SELECT MATERIAL_CODE, MATERIAL_NAME, MATERIAL_UOM FROM m_material WHERE MATERIAL_FLAG = 'LHM'
			AND MATERIAL_CODE LIKE '%".$q."%' OR MATERIAL_FLAG = 'LHM' AND MATERIAL_NAME LIKE '%".$q."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	/*  ### end material ###  */

	/*  ### update untuk cek aktivitas per blok yang di mapping di blok tanam,
		author ridhu : 16-07-2013 ###  */
	
	function cek_blokTanam($loc_code,$company)
    {
        $query="SELECT BLOCK_ID, NO_PK, COMPANY_CODE FROM m_block_pjtanam WHERE BLOCK_ID = '".$loc_code."' AND COMPANY_CODE = '".$company."'";
                    
        $sQuery=$this->db->query($query);
        $num=$sQuery->num_rows();
        
        return $num;   
    }
	
	function aktivitasBlokTanam($act, $lt)
    {
       $act=htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8');
        $lt=htmlentities($this->db->escape_str($lt),ENT_QUOTES,'UTF-8');
        $query = $this->db->query("SELECT m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION` 
									FROM m_activity_map m 
									LEFT JOIN `m_coa` ON m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`
									WHERE m.`ACCOUNT_CODE` like '%".$act."%' 
									AND m.`ACCOUNT_CODE` NOT IN ('8401000','8401998','8401999','8401997')
                    				AND m.STATUS_PENGGUNAAN = 'LHM' 
									AND m.LOCATION_TYPE = '".$lt."' 
								OR m_coa.`COA_DESCRIPTION` like '%".$act."%' 
									AND m.`ACCOUNT_CODE` NOT IN ('8401000','8401998','8401999','8401997')
                    				AND m.STATUS_PENGGUNAAN = 'LHM' 
									AND m.LOCATION_TYPE = '".$lt."'");
        
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
    	return $temp_result; 
    }
	/*  ### end update blok tanam ### */
	
	/* cek closing mingguan */
	/* edited : ridhu 2014-05-26 */
	function cekClosingMingguan($WeekNum, $periode, $company){
		$query = $this->db->query("SELECT pc.ISCLOSE FROM m_periode_control pc 
						LEFT JOIN m_periode m ON m.PERIODE_ID = pc.PERIODE_ID
						WHERE WEEK_NUMBER = '".$WeekNum."' AND pc.COMPANY_CODE = '".$company."' 
							AND MODULE = 'LHM' AND m.PERIODE_NAME = '".$periode."'");
		$data = array_shift($query->result_array());
		$temp = $data['ISCLOSE'];
		$this->db->close();
		return $temp; 
	}
	/* end cek closing mingguan */
}   

?>
