<?

class model_p_progress_pj_bibitan extends Model 
{

    function model_p_progress_pj_bibitan()
    {
        parent::Model(); 

		$this->load->database();
    }

	function insert_p_progress_pj_bibitan ( $data )
	{
		$this->db->insert( 'p_progress_pjbibitan', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_pj_bibitan ( $tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where('TGL_PROGRESS',$tgl_progress);
		$this->db->where('ACTIVITY_CODE',$activity);
		$this->db->where('LOCATION_CODE',$location);
		$this->db->where('COMPANY_CODE',$company);  
		$this->db->update( 'p_progress_pjbibitan', $data );   
	}
	
	function delete_p_progress_pj_bibitan ( $id, $company)
	{
		$this->db->where( 'ID_PROGRESS_PJBIBITAN', $id );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('p_progress_pjbibitan');   
	}
	
	//GRID
	
	function read_act($tgl, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		$sidx = 'lhm.ACTIVITY_CODE ASC, lhm.LOCATION_CODE';
		$sord = 'ASC';
		
		
		$sql2 = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2
FROM m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."'
AND lhm.ACTIVITY_CODE IN (SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'PJNS' AND PARENT = 0) 
GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE
";
	   
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
  
  
		$sql = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2
FROM m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."'
AND ACTIVITY_CODE IN (SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'PJNS' AND PARENT = 0) 
GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$ID_PROGRESS_PJBIBITAN = 1;
		$REALISASI_UNIT = "";
		$HASIL_KERJA = "";
		foreach($objects as $obj)
        {
            $cell = array();
				$TGL_PROGRESS = $obj->LHM_DATE;
				$SATUAN = $obj->UNIT1;
				$ACTIVITY_DESC = $obj->DESCR;
				array_push($cell, $ID_PROGRESS_PJBIBITAN);
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
			$ID_PROGRESS_PJBIBITAN++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
	
	/* baca data yang ada di p_progress_pjbibitan */
	function cek_pb($tgl, $lc, $company){
		$query = $this->db->query("select count(*) as jumlah FROM p_progress_pjbibitan where DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' and COMPANY_CODE = '".$company."'");
		
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
		
		
		$sql2 = "SELECT ID_PROGRESS_PJBIBITAN, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, COMPANY_CODE FROM p_progress_pjbibitan
WHERE DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'";

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
  
  
		$sql = "SELECT ID_PROGRESS_PJBIBITAN, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, 
	HASIL_KERJA, COMPANY_CODE  FROM p_progress_pjbibitan
WHERE DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' 
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $obj->ID_PROGRESS_PJBIBITAN);
				array_push($cell, $obj->TGL_PROGRESS); 
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $obj->ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);
				array_push($cell, $obj->HASIL_KERJA);                       
                array_push($cell, $obj->SATUAN);
				//array_push($cell, $obj->HK);
				//array_push($cell, $obj->REALISASI);	
				//array_push($cell, $obj->REALISASI_UNIT);						
					
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
	
	//add data
	function act($q, $tgl, $company){
	
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR, m_progress_map.UNIT1 AS UNIT1  FROM m_gang_activity_detail 
			LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
			LEFT JOIN m_progress_map ON m_progress_map.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
			AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
			AND LEFT(m_gang_activity_detail.ACTIVITY_CODE,4) = '8301' 
			AND m_gang_activity_detail.ACTIVITY_CODE LIKE '".$q."%'
			GROUP BY ACTIVITY_CODE");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	//add data
	function blok($q, $act, $tgl, $company){
	
		$query = $this->db->query("SELECT LOCATION_CODE FROM m_gang_activity_detail 
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
			AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
			AND LEFT(m_gang_activity_detail.ACTIVITY_CODE,4) = '8301' 
			AND m_gang_activity_detail.ACTIVITY_CODE = '".$act."'
			AND m_gang_activity_detail.LOCATION_CODE LIKE '".$q."%'
			GROUP BY ACTIVITY_CODE, LOCATION_CODE");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	//delete data
	function delete_data ( $id,$afd,$tgl,$company)
	{
		$this->db->where( 'ID_PROGRESS_PJBIBITAN', $id ); 
		$this->db->where( 'LOCATION_CODE', $afd ); 
		$this->db->where( 'TGL_PROGRESS', $tgl ); 
		$this->db->where( 'COMPANY_CODE', $company ); 	
		$this->db->delete('p_progress_pjbibitan');   
	}	
	
}   

?>
