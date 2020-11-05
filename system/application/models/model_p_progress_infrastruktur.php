<?

class Model_p_progress_infrastruktur extends Model 
{

    function Model_p_progress_infrastruktur()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_p_progress_infrastruktur ( $data )
	{
		$this->db->insert( 'p_progress_infrastruktur', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_infrastruktur ( $id, $data )
	{
		$this->db->where( 'JENIS_PROGRESS', $id );  
		$this->db->update( 'p_progress_infrastruktur', $data );   
	}
	
	function get_afdeling($company)
	{
		$query = $this->db->query("SELECT DISTINCT LEFT(LOCATION_CODE,2) as AFD FROM m_location WHERE company_code = '".$company."' AND LOCATION_TYPE_CODE = 'OP' GROUP BY LOCATION_CODE");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	function get_block($afd,$company)
	{
		$query = $this->db->query("SELECT DISTINCT LOCATION_CODE FROM m_gang_activity_detail WHERE LOCATION_TYPE_CODE = 'OP' AND LEFT(LOCATION_CODE,2) = '".$afd."' AND COMPANY_CODE = '".$company."'");
		//AND LOCATION_CODE LIKE '".$afd."%' AND AND DATE_FORMAT(LHM_DATE,'%Y%m') = '".$periode."'
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	//GRID
	
	function read_act($tgl,$lc, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		$sidx = 'LHM_DATE';
		$sord = 'ASC';
	
	//JENIS_PROGRESS,TGL_PROGRESS,ACTIVITY_CODE,ACTIVITY_DESC,ACTIVITY_LOCATION,SATUAN,HASIL_KERJA,REALISASI,HK_PER_SATUAN,INPUT_BY,INPUT_DATE,COMPANY_CODE	
		
		$sql2 = "SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, SUM(HK_JUMLAH) AS HK FROM m_gang_activity_detail 
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = m_gang_activity_detail.ACTIVITY_CODE)
WHERE COMPANY_CODE = '".$company."' AND LOCATION_CODE = '".$lc."' and DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
AND ACTIVITY_CODE LIKE '810%' 
GROUP BY LHM_DATE, LOCATION_CODE, ACTIVITY_CODE";
	   
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
  
  
		$sql = "SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, SUM(HK_JUMLAH) AS HK FROM m_gang_activity_detail 
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = m_gang_activity_detail.ACTIVITY_CODE)
WHERE COMPANY_CODE = '".$company."' AND LOCATION_CODE = '".$lc."' and DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
AND ACTIVITY_CODE LIKE '810%' 
GROUP BY LHM_DATE, LOCATION_CODE, ACTIVITY_CODE ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $no);
				array_push($cell, $obj->LHM_DATE);
				array_push($cell, $obj->LOCATION_CODE);  
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $obj->DESCR);
				array_push($cell, "");                       
                array_push($cell, "");
				array_push($cell, $obj->HK);							
					
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

	
	
	    


}   

?>
