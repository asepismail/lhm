<?

class model_p_progress_teknik extends Model 
{

    function model_p_progress_teknik()
    {
        parent::Model(); 

		$this->load->database();
    }

	function insert_p_progress_teknik ( $data )
	{
		$this->db->insert( 'p_progress_teknik', $data );
		return $this->db->insert_id();   
	}
	
	function update_p_progress_teknik ( $id, $tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where( 'ID_PROGRESS', $id ); 
		$this->db->where( 'TGL_PROGRESS',$tgl_progress);
		$this->db->where( 'ACTIVITY_CODE',$activity);
		$this->db->where( 'LOCATION_CODE',$location);
		$this->db->where( 'COMPANY_CODE',$company);  
		$this->db->update( 'p_progress_teknik', $data );   
	}
	
	//delete data
	function delete_progress_teknik (  $id, $tgl_progress,$activity,$location,$company)
	{
		$this->db->where( 'ID_PROGRESS', $id ); 
		$this->db->where( 'TGL_PROGRESS', $tgl_progress ); 
		$this->db->where( 'ACTIVITY_CODE',$activity);
		$this->db->where( 'LOCATION_CODE', $location ); 
		$this->db->where( 'COMPANY_CODE', $company ); 	
		$this->db->delete('p_progress_teknik');   
	}	
	
	//GRID
	
	function read_act($tgl, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		$sidx = 'ACTIVITY_CODE ASC, LOCATION_CODE';
		$sord = 'ASC';
		
		
		$sql2 = "SELECT ID_PROGRESS AS IDP, TGL_PROGRESS AS TGL_AKTIVITAS, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, SATUAN AS UNIT1  FROM p_progress_teknik
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
UNION
SELECT ID AS IDP,  TGL_AKTIVITAS, ACTIVITY_CODE, m_coa.COA_DESCRIPTION,  LOCATION_CODE, '' AS NILAI, pm.UNIT1 FROM p_vehicle_activity 
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_vehicle_activity.ACTIVITY_CODE
LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE = p_vehicle_activity.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d') = '".$tgl."' AND LOCATION_CODE <> '' AND COMPANY_CODE = '".$company."'
AND CONCAT(ACTIVITY_CODE, LOCATION_CODE) NOT IN (SELECT CONCAT(ACTIVITY_CODE,LOCATION_CODE) FROM p_progress_teknik
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' )
GROUP BY ACTIVITY_CODE, LOCATION_CODE";
	   
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
  
  
		$sql = "SELECT ID_PROGRESS AS IDP, TGL_PROGRESS AS TGL_AKTIVITAS, ACTIVITY_CODE, m_coa.COA_DESCRIPTION, LOCATION_CODE, HASIL_KERJA AS NILAI, SATUAN AS UNIT1  FROM p_progress_teknik
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'
UNION
SELECT ID AS IDP,  TGL_AKTIVITAS, ACTIVITY_CODE, m_coa.COA_DESCRIPTION,  LOCATION_CODE, '' AS NILAI, pm.UNIT1 FROM p_vehicle_activity 
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_vehicle_activity.ACTIVITY_CODE
LEFT JOIN m_progress_map pm ON pm.ACCOUNTCODE = p_vehicle_activity.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d') = '".$tgl."' AND LOCATION_CODE <> '' AND COMPANY_CODE = '".$company."'
AND CONCAT(ACTIVITY_CODE, LOCATION_CODE) NOT IN (SELECT CONCAT(ACTIVITY_CODE,LOCATION_CODE) FROM p_progress_teknik
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
WHERE DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' )
GROUP BY ACTIVITY_CODE, LOCATION_CODE ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		
		foreach($objects as $obj)
        {
            $cell = array();
				
				array_push($cell, $obj->IDP);
				array_push($cell, $obj->TGL_AKTIVITAS);
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $obj->COA_DESCRIPTION);
				array_push($cell, $obj->LOCATION_CODE);
				array_push($cell, $obj->NILAI);                       
                array_push($cell, $obj->UNIT1);
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
		
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
			
}   

?>
