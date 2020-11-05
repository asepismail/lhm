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
	
	function getUom(){
		$query = $this->db->query("SELECT UNIT_CODE, UNIT_DESC FROM m_satuan");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
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
	
	/* detail pengajuan */
	function loadPengajuanDetail($noPengajuan)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "";
		
		$sQuery ="SELECT RM_PENGAJUAN_DETAIL_ID, rm.RM_PENGAJUAN_ID, rm.ACTIVITY_CODE, c.COA_DESCRIPTION, 
					QTY, UOM, RPSAT, RPTTL, VOIDEDBY,
					DATE_FORMAT(VOIDED_DATE,'%d-%m-%Y') AS VOIDED_DATE,CREATED, 
					DATE_FORMAT(CREATED_DATE,'%d-%m-%Y') AS CREATED_DATE, UPDATED, 
					DATE_FORMAT(UPDATED_DATE,'%d-%m-%Y') AS UPDATED_DATE
					FROM pms_rm_pengajuan_detail rm
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = rm.ACTIVITY_CODE
					WHERE RM_PENGAJUAN_ID = '".$noPengajuan."' AND VOIDED <> 1 " .$where ;  
		
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
			array_push($cell, htmlentities($obj->RM_PENGAJUAN_DETAIL_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_PENGAJUAN_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COA_DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UOM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RPSAT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RPTTL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CREATED,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CREATED_DATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UPDATED,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UPDATED_DATE,ENT_QUOTES,'UTF-8'));
			
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
	
	function getActivity($lc, $q){
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
        /* $qryor = " AND COMPANY_CODE='".$this->db->escape_str($company)."' AND INACTIVE = 0 AND ISAPPR_RM = 0";
		
		$qry = "SELECT ACCOUNTCODE, COA_DESCRIPTION FROM m_coa
					WHERE ACCOUNTCODE like '".$this->db->escape_str($cv)."%'
					OR COA_DESCRIPTION LIKE '%".$this->db->escape_str($cv)."%'" . $qryor;
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result; */
		
		$filtact = "";
		if($lc != "" ) {
		  switch (substr($lc,0,2)) {
			  case "BP": case "BS": case "BN":
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8152%'"; break;
			  case "JA": case "JS": case "TB":
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8162%'"; break;
			  case "WD": case "WG": case "TG": case "ON": case "OP": case "GV": case "DM":
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8132%'"; break;
			  case "JC": case "JU": case "JT": case "JH": case "JL": 
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8112%'"; break;
			  case "D0": case "GB": case "LG": case "BT": case "BB": case "C0": case "D1":
			  case "D2": case "JP": case "GP": case "LP": case "GK": case "GV": 
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8142%'"; break;
			  case "PU": case "PT": case "PC": case "OL": case "PS": case "PB": case "PU": case "PE": 
			  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8122%'"; break;
			  default:
			  $filtact = " WHERE 1 = 1";
			  break;
		  }
		  
		  $qryor = " m.LOCATION_TYPE = 'IF'";
		  $qry = "SELECT m.ACCOUNT_CODE as ACCOUNTCODE, m_coa.COA_DESCRIPTION as COA_DESCRIPTION 
				  from m_activity_map m LEFT JOIN m_coa on (m_coa.ACCOUNTCODE = m.ACCOUNT_CODE) "; 
		  $qry .= $filtact . " AND " . $qryor;
		  $qry .= " OR m_coa.COA_DESCRIPTION LIKE '%".$q."%' AND ". $qryor;
		  $qry .= " GROUP BY m.ACCOUNT_CODE";
		  
		  $query = $this->db->query($qry);
		  
		  $temp_result = array();
		  
		  foreach ( $query->result_array() as $row ){
			  $temp_result [] = $row;    
		  }
		  
		  return $temp_result;
		} else {
			return false;	
		}
    }
	
	function validateActivity ($activity){
		$q = $this->db->query("SELECT ACCOUNT_CODE AS ret FROM m_activity_map WHERE LOCATION_TYPE = 'IF' 
								AND ACCOUNT_CODE = '".$activity."' GROUP BY ACCOUNT_CODE", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function validateDetailRM($ppj, $act){
		$q = $this->db->query("SELECT COUNT(ACTIVITY_CODE) AS ret FROM pms_rm_pengajuan_detail WHERE RM_PENGAJUAN_ID = '".$ppj."' 
								AND ACTIVITY_CODE = '".$act."' AND VOIDED = 0", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	/* crud detail pengajuan */
	function insert_dpengajuan_rm ( $data ){
        $this->db->insert( 'pms_rm_pengajuan_detail', $data );
        return $this->db->affected_rows();   
    }
	
	function update_dpengajuan_rm ( $pengajuanID, $activity, $data ){
		$this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->where( 'ACTIVITY_CODE', $activity );
        $this->db->update( 'pms_rm_pengajuan_detail', $data );
        return $this->db->affected_rows();   
    }
	
	function void_dpengajuan_rm ( $pengajuanID, $activity )
    {
        $this->db->where( 'RM_PENGAJUAN_ID', $pengajuanID );
		$this->db->where( 'ACTIVITY_CODE', $activity ); 
		$this->db->set('VOIDED',1); 
		$this->db->set('VOIDEDBY',$this->session->userdata('LOGINID')); 
		$this->db->set('VOIDED_DATE',date("Y-m-d H:i:s")); 
        $this->db->update('pms_rm_pengajuan_detail'); 
		return $this->db->affected_rows();       
    }
	/* end detail pengajuan */
	
}

?>