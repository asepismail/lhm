<?php
class pms_m_pengajuan_rm extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	function loadDataRM($company, $periode)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "";
		$sQuery ="SELECT RM_PENGAJUAN_ID, PERIODE, RM_TGL_PENGAJUAN, rm.IFCODE, iif.IFNAME, RM_VALID_FROM, RM_VALID_TO, 
					RM_BUDGET, DESCRIPTION, CASE WHEN rm.PENGAJUAN_STATUS = 0 THEN 'draft' ELSE 'disetujui' END AS 
					PENGAJUAN_STATUS, rm.COMPANY_CODE 
					FROM pms_rm_pengajuan rm
					LEFT JOIN m_infrastructure iif on iif.IFCODE = rm.IFCODE AND iif.COMPANY_CODE = rm.COMPANY_CODE
					WHERE rm.COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."' AND rm.PENGAJUAN_STATUS <> 9" .$where ;  
		
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
	
	function getInfrasModel($cv, $company){
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
        $qryor = " AND COMPANY_CODE='".$this->db->escape_str($company)."' AND INACTIVE = 0 AND ISAPPR_RM = 0";
		
		$qry = "SELECT IFCODE, IFNAME FROM m_infrastructure
					WHERE IFCODE like '".$this->db->escape_str($cv)."%'" . $qryor .
					" OR IFNAME LIKE '%".$this->db->escape_str($cv)."%'" . $qryor;
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
    }
	
	function get_company_id($company)
	{
		$query = $this->db->query("SELECT COMPANY_NUMBER FROM m_company WHERE COMPANY_CODE = '".$company."'");
		$data = array_shift($query->result_array());
		$temp = $data['COMPANY_NUMBER'];
		$this->db->close();
		return $temp; 
	}
	
	function getNoPengajuanRM($company){
		$q = $this->db->query("SELECT COALESCE(MAX(RM_PENGAJUAN_ID),0) as ret FROM pms_rm_pengajuan 
								WHERE COMPANY_CODE = '".$company."'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cekExist($company, $ifcode, $periode){
		$q = $this->db->query("SELECT COALESCE(RM_PENGAJUAN_ID,0) as ret FROM pms_rm_pengajuan rm 
								WHERE COMPANY_CODE = '".$company."' AND IFCODE = '".$ifcode."' 
								AND PERIODE = '".$periode."' AND rm.PENGAJUAN_STATUS <> 9", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cekExistIfcode($company, $ifcode){
		$q = $this->db->query("SELECT COUNT(COALESCE(IFCODE,0)) as ret FROM m_infrastructure 
								WHERE COMPANY_CODE = '".$company."' AND IFCODE = '".$ifcode."' 
								AND INACTIVE <> 1", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cek_pengajuan($company, $ppj){
		 $query = $this->db->query("SELECT RM_PENGAJUAN_ID			
									FROM pms_rm_pengajuan 
									WHERE COMPANY_CODE = '".$company."' AND RM_PENGAJUAN_ID = '".$ppj."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	/* crud pengajuan */
	function insert_pengajuan_rm ( $data ){
        $this->db->insert( 'pms_rm_pengajuan', $data );
        return $this->db->affected_rows();   
    }
	
	function delete_pengajuan_rm ( $pengajuanID, $company )
    {
        $this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->where( 'COMPANY_CODE', $company ); 
		$this->db->set('PENGAJUAN_STATUS',9); 
        $this->db->update('pms_rm_pengajuan'); 
		return $this->db->affected_rows();       
    }
	
	function update_pengajuan_rm ( $pengajuanID, $company, $data ){
		$this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->where( 'COMPANY_CODE', $company );
        $this->db->update( 'pms_rm_pengajuan', $data );
        return $this->db->affected_rows();   
    }
	
}

?>