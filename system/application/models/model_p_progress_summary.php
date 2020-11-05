<?

class model_p_progress_summary extends Model 
{
    function model_p_progress_summary()
    {
        parent::Model();
		$this->load->database();
    }
	
	function LoadData($company, $periode, $act, $afd)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "";
		
		if($act !== 'all'){
			$where .= " AND ACTIVITY_CODE LIKE '".$act."%'";	
		} 
		
		if($afd !== 'all'){
			$where .= " AND LOCATION_CODE LIKE '%".$afd."%'";
		}
		$company = $this->db->escape_str($company);
		$sQuery ="SELECT PROGSUM_ID, COMPANY_CODE, PERIODE, ACTIVITY_CODE, ACTIVITY_DESC,LOCATION_CODE,
					LOCATION_DESC,
					COALESCE(QTY1_LHM,0) AS QTY1_LHM,
					COALESCE(QTY1_BKE,0) AS QTY1_BKE,
					COALESCE(QTY1_BKT,0) AS QTY1_BKT, UNIT1,
					COALESCE(QTY1_LHM,0) + COALESCE(QTY1_BKE,0) + COALESCE(QTY1_BKT,0) AS TOTAL1,
					COALESCE(QTY2_LHM,0) AS QTY2_LHM,
					COALESCE(QTY2_BKE,0) AS QTY2_BKE,
					COALESCE(QTY2_BKT,0) AS QTY2_BKT,UNIT2, 
					COALESCE(QTY2_LHM,0) + COALESCE(QTY2_BKE,0) + COALESCE(QTY2_BKT,0) AS TOTAL2,
					QTY1_PENYESUAIAN,QTY2_PENYESUAIAN, FINAL1, FINAL2 FROM p_progress_summary
					WHERE COMPANY_CODE='".$company."' AND PERIODE = '".$periode."'" . $where; 
		
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
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, htmlentities($obj->PROGSUM_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVITY_DESC,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LOCATION_DESC,ENT_QUOTES,'UTF-8'));
	        array_push($cell, htmlentities($obj->QTY1_LHM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY1_BKE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY1_BKT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UNIT1,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TOTAL1,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY1_PENYESUAIAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FINAL1,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY2_LHM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY2_BKE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY2_BKT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UNIT2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TOTAL2,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->QTY2_PENYESUAIAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->FINAL2,ENT_QUOTES,'UTF-8'));

            $row = new stdClass();
            $row->id = $cell[1];
            $row->cell = $cell;
            array_push($rows, $row);
			$no_va++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
	}
	
	function getcoatype(){
		$query="SELECT COA_PARENT, COA_ACCOUNTTYPE FROM m_coa_accountype";
                    
        $sQuery=$this->db->query($query);
        $temp_result = array();
        foreach($sQuery->result_array() as $row)
        {
            $temp_result [] = $row;     
        }
        return $temp_result;   
	}
	
	function generateData($company, $periode, $user){
		$company=$this->db->escape_str($company);
        $periode=$this->db->escape_str($periode);
        
        $query=$this->db->query("CALL sp_generate_summary_progress('".$company."', '".$periode."', '".$user."')");
        $temp_result = array();
       	foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result; 
	}
	
	function update_progress ( $id, $data )
    {
        $this->db->where( 'PROGSUM_ID', $id );  
        $this->db->update( 'p_progress_summary', $data );   
    }
	
	function cekProgressValue($id, $field){
		$query=$this->db->query("SELECT ".$field." from p_progress_summary WHERE PROGSUM_ID = ".$id);
        $temp_result = array();
       	foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	 function gen_progress($company, $periode, $act, $afd)
     {
        $where = "";
		
		if($act !== 'all'){
			$where .= " AND ACTIVITY_CODE LIKE '".$act."%'";	
		} 
		
		if($afd !== 'all'){
			$where .= " AND LOCATION_CODE LIKE '%".$afd."%'";
		}
		$company = $this->db->escape_str($company);
		$sQuery ="SELECT PROGSUM_ID, PERIODE, ACTIVITY_CODE, ACTIVITY_DESC,LOCATION_CODE,
					LOCATION_DESC,
					COALESCE(HK,0) AS JMLHK,
					COALESCE(QTY1_LHM,0) AS QTY1_LHM,
					COALESCE(QTY1_BKE,0) AS QTY1_BKE,
					COALESCE(QTY1_BKT,0) AS QTY1_BKT, UNIT1,
					COALESCE(TOTAL1,0) AS TOTAL1,
					COALESCE(QTY1_PENYESUAIAN,0) AS QTY1_PENYESUAIAN,
					COALESCE(FINAL1,0) AS FINAL1,
					COALESCE(QTY2_LHM,0) AS QTY2_LHM,
					COALESCE(QTY2_BKE,0) AS QTY2_BKE,
					COALESCE(QTY2_BKT,0) AS QTY2_BKT,UNIT2, 
					COALESCE(TOTAL2,0) AS TOTAL2,
					COALESCE(QTY2_PENYESUAIAN,0) AS QTY2_PENYESUAIAN, 
					COALESCE(FINAL2,0) AS FINAL2, p_progress_summary.COMPANY_CODE, 
					CASE WHEN SUBSTRING(LOCATION_CODE,1,2) = 'PJ' THEN 
						CASE WHEN SUBSTRING(m_project.`PROJECT_LOCATION`,1,1) LIKE 'O%' THEN 'OP'
							WHEN SUBSTRING(m_project.`PROJECT_LOCATION`,1,1) LIKE 'P%' THEN 'PL'
						END
					WHEN  iif.IFCODE IS NOT NULL THEN 
						CASE WHEN iif.ESTATE LIKE 'O%' THEN 'OP'
						WHEN iif.ESTATE LIKE 'p%' THEN 'PL'
						END
					ELSE CASE WHEN SUBSTRING(LOCATION_CODE,1,1) LIKE 'O%' THEN 'OP'
						WHEN SUBSTRING(LOCATION_CODE,1,1) LIKE 'P%' THEN 'PL'
						END
					END AS BLOCKTYPE
					FROM p_progress_summary
					LEFT JOIN m_project ON PROJECT_ID = LOCATION_CODE  AND p_progress_summary.COMPANY_CODE = m_project.COMPANY_CODE
					LEFT JOIN m_infrastructure iif ON iif.IFCODE = LOCATION_CODE AND iif.COMPANY_CODE = p_progress_summary.COMPANY_CODE
					WHERE p_progress_summary.COMPANY_CODE='".$company."' AND PERIODE = '".$periode."'" . $where; 
        $sQuery=$this->db->query($sQuery);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if($rowcount > 0){
            foreach ( $sQuery->result_array() as $row ){
                $temp_result [] = $row;
            }
        }
        return $temp_result; 
    }
}

?>