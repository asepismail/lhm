<?

class Model_p_progress_sisip extends Model 
{

    function Model_p_progress_sisip()
    {
        parent::Model(); 

		$this->load->database();
    }
		
	function insert_p_progress_sisip ( $data )
	{
		$this->db->insert( 'p_progress_sisip', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_sisip ( $afd,$tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where('AFD',$afd);
		$this->db->where('TGL_PROGRESS',$tgl_progress);
		$this->db->where('ACTIVITY_CODE',$activity);
		$this->db->where('LOCATION_CODE',$location);
		$this->db->where('COMPANY_CODE',$company); 
		$this->db->update( 'p_progress_sisip', $data );   
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
		
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
WHERE " . $where . " AND lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."'
AND lhm.ACTIVITY_CODE IN ( SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'SSP' AND PARENT = 0 )
GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE";
	   
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
  		
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$sql = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC as DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
WHERE " . $where . " AND lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."'
AND lhm.ACTIVITY_CODE IN ( SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'SSP' AND PARENT = 0 ) 
GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE ORDER BY ".$sidx." ".$sord.""; 
		
		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$ID_PROGRESS_SISIP = 1;
		$HASIL_KERJA = "";		
		foreach($objects as $obj)
        {
            $cell = array();
				$TGL_PROGRESS = $obj->LHM_DATE;
				$SATUAN = $obj->UNIT1;
				$ACTIVITY_DESC = $obj->DESCR;
				
				array_push($cell, $ID_PROGRESS_SISIP);
				array_push($cell, $TGL_PROGRESS);
				array_push($cell, $obj->ACTIVITY_CODE);
				array_push($cell, $ACTIVITY_DESC);  
				array_push($cell, $obj->LOCATION_CODE);  
				array_push($cell, $HASIL_KERJA);                       
                array_push($cell, $SATUAN);                     
				//array_push($cell, $obj->HK);							
					
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
			$ID_PROGRESS_SISIP++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
	
	/* baca data yang ada di p_progress_sisip */
	function cek_pts($tgl, $lc, $company){
		
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$query = $this->db->query("select count(*) as jumlah FROM p_progress_sisip where ".$where." AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' and COMPANY_CODE = '".$company."'");
		
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
		$sidx = 'LOCATION_CODE, ACTIVITY_CODE';
		//$sord = 'ASC';
		
		if($lc != ''){
			$where = "LEFT(LOCATION_CODE,2) = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT ID_PROGRESS_SISIP, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, COMPANY_CODE FROM p_progress_sisip
WHERE ".$where." AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."'";

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
  
  
		$sql = "SELECT ID_PROGRESS_SISIP, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, COMPANY_CODE FROM p_progress_sisip WHERE ".$where." AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' 
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $obj->ID_PROGRESS_SISIP);
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
	
	//add data
	function act($afd, $q, $tgl, $company){
	
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR,  m_progress_map.UNIT1 AS UNIT1
			FROM m_gang_activity_detail 
			LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
			LEFT JOIN m_progress_map ON m_progress_map.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
			AND LEFT(m_gang_activity_detail.LOCATION_CODE,2) = '".$afd."' 
			AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
			AND LEFT(m_gang_activity_detail.ACTIVITY_CODE,4) = '8402' 
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
	function blok($afd, $q, $act, $tgl, $company){
	
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR FROM m_gang_activity_detail 
			LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."'
			AND LEFT(m_gang_activity_detail.LOCATION_CODE,2) = '".$afd."' 
			AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
			AND m_gang_activity_detail.ACTIVITY_CODE = '".$act."'
			AND LEFT(m_gang_activity_detail.ACTIVITY_CODE,4) = '8402' 
			AND m_gang_activity_detail.LOCATION_CODE LIKE '".$q."%'
			GROUP BY ACTIVITY_CODE,LOCATION_CODE");
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
		$this->db->where( 'ID_PROGRESS_SISIP', $id ); 
		$this->db->where( 'AFD', $afd ); 
		$this->db->where( 'TGL_PROGRESS', $tgl ); 
		$this->db->where( 'COMPANY_CODE', $company ); 	
		$this->db->delete('p_progress_sisip');   
	}	
}   

?>
