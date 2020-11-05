<?

class Model_p_progress_panen extends Model 
{

    function Model_p_progress_panen()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_p_progress_panen ( $data )
	{
		$this->db->insert( 'p_progress_panen', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_panen ( $afd,$tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where('AFD',$afd);
		$this->db->where('TGL_PROGRESS',$tgl_progress);
		$this->db->where('ACTIVITY_CODE',$activity);
		$this->db->where('LOCATION_CODE',$location);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update( 'p_progress_panen', $data );   
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
		$sidx = 'ACTIVITY_CODE,LOCATION_CODE';
		$sord = 'ASC';
		
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail 
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = m_gang_activity_detail.ACTIVITY_CODE)
WHERE ".$where." AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
AND ACTIVITY_CODE IN ('8601001','8601002','8601003','8601004','8601005') 
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
  
  
		$sql = "SELECT LHM_DATE, LOCATION_CODE, ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = m_gang_activity_detail.ACTIVITY_CODE)
WHERE ".$where." AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
AND ACTIVITY_CODE IN ('8601001','8601002','8601003','8601004','8601005') 
GROUP BY LHM_DATE, LOCATION_CODE, ACTIVITY_CODE ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$ID_PROGRESS_PANEN = 1;
		$REALISASI_UNIT = "";
		$HASIL_KERJA = "";
		foreach($objects as $obj)
        {
            $cell = array();
				$TGL_PROGRESS = $obj->LHM_DATE;
				$SATUAN = $obj->UNIT1;
				$ACTIVITY_DESC = $obj->DESCR;
				
				array_push($cell, $ID_PROGRESS_PANEN);
				array_push($cell, $TGL_PROGRESS);
				array_push($cell, $obj->ACTIVITY_CODE);
				array_push($cell, $ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);  
				array_push($cell, $HASIL_KERJA);                       
                array_push($cell, $SATUAN);                       
                					
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
			$ID_PROGRESS_PANEN++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
	
	/* baca data yang ada di p_progress_bibitan */
	function cek_pp($tgl, $lc, $company){
		$query = $this->db->query("SELECT COUNT(*) AS jumlah FROM p_progress_panen WHERE DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND LOCATION_CODE LIKE '".$lc."%' AND COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}

		return $temp_result;
	}
	
	function read_exist_act($tgl,$lc, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		$sidx = 'ACTIVITY_CODE,LOCATION_CODE';
		$sord = 'ASC';
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		$sql2 = "SELECT ID_PROGRESS_PANEN, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, COMPANY_CODE FROM p_progress_panen
WHERE " . $where . " AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'";

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
  
  
		$sql = "SELECT ID_PROGRESS_PANEN, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, 
	HASIL_KERJA, COMPANY_CODE  FROM p_progress_panen
WHERE " . $where . " AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' 
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $obj->ID_PROGRESS_PANEN);
				array_push($cell, $obj->TGL_PROGRESS); 
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $obj->ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);
				array_push($cell, $obj->HASIL_KERJA);                       
                array_push($cell, $obj->SATUAN);
					
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
	
	function active_block($periode,$q,$company){
	
		$query = $this->db->query("SELECT DISTINCT LOCATION_CODE FROM m_gang_activity_detail 
WHERE ACTIVITY_CODE IN ('8601001','8601002','8601003','8601004','8601005') and LOCATION_CODE like '".$q."%' 
AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE,'%Y%m') = '".$periode."' order by LOCATION_CODE asc");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}  

}   

?>
