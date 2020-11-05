<?php
class pms_m_monitoring_rm extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	function loadMonitoringRM($company, $periode)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $where = "";
		
		if($company != "PAG"){
			$where .= " AND rm.COMPANY_CODE = '".$company."' ";
		} 	
		
		$sQuery ="SELECT RM_PENGAJUAN_ID, PERIODE, RM_TGL_PENGAJUAN, rm.IFCODE, iif.IFNAME, RM_VALID_FROM, RM_VALID_TO, 
					RM_BUDGET, DESCRIPTION, 
					CASE WHEN rm.PENGAJUAN_STATUS = 0 THEN 'draft' 
					WHEN rm.PENGAJUAN_STATUS = 2 THEN 'approval kebun'
					WHEN rm.PENGAJUAN_STATUS = 1 THEN 'approved' END AS 
					PENGAJUAN_STATUS, ISAPPR1, ISAPPR1_DATE, ISAPPR2, ISAPPR2_DATE, rm.COMPANY_CODE 
					FROM pms_rm_pengajuan rm
					LEFT JOIN m_infrastructure iif on iif.IFCODE = rm.IFCODE AND iif.COMPANY_CODE = rm.COMPANY_CODE
					WHERE rm.PENGAJUAN_STATUS <> 9 AND PERIODE = '".$periode."'" .$where ;  
		
		if(!$sidx) $sidx =1;
		$query = $this->db->query($sQuery);
		$count = $query->num_rows(); 
		
        if( $count >0 ) 
		{
            $total_pages = @(ceil($count/$limit));
        } 
		else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
		
		if($count >0) { 
			$sQuery .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
						
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no = 1;	
		$det = "";		
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, htmlentities($obj->RM_PENGAJUAN_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_TGL_PENGAJUAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_VALID_FROM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_VALID_TO,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->RM_BUDGET,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PENGAJUAN_STATUS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR1,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ISAPPR1_DATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR2,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR2_DATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($det,ENT_QUOTES,'UTF-8'));
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
	
	function loadPengajuanNotes($noPengajuan)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "";
		
		$sQuery ="SELECT RM_PENGAJUAN_ID, DESCRIPTION, CREATED, CREATEDDATE FROM pms_rm_log
					WHERE LOG_TYPE = 'notes' AND RM_PENGAJUAN_ID = '".$noPengajuan."' " .$where ;  
		
		if(!$sidx) $sidx =1;
		$query = $this->db->query($sQuery);
		$count = $query->num_rows(); 
		
        if( $count >0 ) 
		{
            $total_pages = @(ceil($count/$limit));
        } 
		else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
		
		if($count >0) { 
			$sQuery .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
						
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no = 1;	
		$det = "";		
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_PENGAJUAN_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CREATED,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CREATEDDATE,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[1];
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
	
	function getRoleUser($user)
	{
		$query = $this->db->query("SELECT PMSUSERGROUP_ID FROM pms_user_group_map WHERE LOGINID = '".$user."'");
		$data = array_shift($query->result_array());
		$temp = $data['PMSUSERGROUP_ID'];
		$this->db->close();
		return $temp; 
	}
	
	function approve1 ( $pengajuanID ){
		$this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->set( 'ISAPPR1', 1 );
		$this->db->set( 'PENGAJUAN_STATUS', 2 );
		$this->db->set( 'ISAPPR1_DATE', date ("Y-m-d H:i:s") );
        $this->db->update( 'pms_rm_pengajuan' );
        return $this->db->affected_rows();   
    }
	
	function approve2 ( $pengajuanID ){
		$this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->set( 'ISAPPR2', 1 );
		$this->db->set( 'PENGAJUAN_STATUS', 1 );
		$this->db->set( 'ISAPPR2_DATE', date ("Y-m-d H:i:s") );
        $this->db->update( 'pms_rm_pengajuan' );
        return $this->db->affected_rows();  
    }
	
	/* crud pengajuan */
	function insert_pengajuan_notes ( $data ){
        $this->db->insert( 'pms_rm_log', $data );
        return $this->db->affected_rows();   
    }
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function retPengajuanRMXls($company){
		$where = "";
		if($company != "PAG"){
			$where .= " AND rm.COMPANY_CODE = '".$company."' ";
		} 	
		
		$Query ="SELECT RM_PENGAJUAN_ID, PERIODE, DATE_FORMAT(RM_TGL_PENGAJUAN,'%d-%m-%Y') AS RM_TGL_PENGAJUAN, 
					rm.IFCODE, iif.IFNAME, RM_VALID_FROM, RM_VALID_TO, 
					RM_BUDGET, DESCRIPTION, 
					CASE WHEN rm.PENGAJUAN_STATUS = 0 THEN 'draft' 
					WHEN rm.PENGAJUAN_STATUS = 2 THEN 'approval kebun'
					WHEN rm.PENGAJUAN_STATUS = 1 THEN 'approved' END AS 
					PENGAJUAN_STATUS, ISAPPR1, DATE_FORMAT(ISAPPR1_DATE,'%d-%m-%Y') AS ISAPPR1_DATE, ISAPPR2, 
					DATE_FORMAT(ISAPPR2_DATE,'%d-%m-%Y') AS ISAPPR2_DATE, rm.COMPANY_CODE 
					FROM pms_rm_pengajuan rm
					LEFT JOIN m_infrastructure iif on iif.IFCODE = rm.IFCODE AND iif.COMPANY_CODE = rm.COMPANY_CODE
					WHERE rm.PENGAJUAN_STATUS <> 9" .$where ; 
		$sQuery=$this->db->query($Query);
         $temp_result = array();
         foreach($sQuery->result_array() as $row) {
            	$temp_result [] = $row;     
         }
         return $temp_result;	
	}
}

?>