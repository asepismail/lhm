<?

class model_p_progress_tanam extends Model 
{

    function model_p_progress_tanam()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_p_progress_tanam ( $data )
	{
		$this->db->insert( 'p_progress_tanam', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_tanam ( $afd,$tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where('AFD',$afd);
		$this->db->where('TGL_PROGRESS',$tgl_progress);
		$this->db->where('ACTIVITY_CODE',$activity);
		$this->db->where('LOCATION_CODE',$location);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update( 'p_progress_tanam', $data );   
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

	//GRID
	
	function read_act($tgl,$lc, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        //$sidx = $this->input->post('sidx');
        //$sord = $this->input->post('sord');
		$sidx = 'LHM_DATE';
		$sord = 'ASC';
		
		if($lc != ''){
			$where = "AFD = '".$lc."' ";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2, pj.AFD FROM m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
LEFT JOIN m_project pj ON (lhm.LOCATION_CODE = pj.PROJECT_ID AND lhm.COMPANY_CODE = pj.COMPANY_CODE)
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."' 
AND ACTIVITY_CODE IN ( SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN IN ('TN','LC') AND PARENT = 0 )
AND ". $where ." GROUP BY LHM_DATE, LOCATION_CODE, ACTIVITY_CODE";
	   
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
  
  
		$sql = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2, pj.AFD FROM 			m_gang_activity_detail lhm
LEFT JOIN m_progress_map pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE)
LEFT JOIN m_project pj ON (lhm.LOCATION_CODE = pj.PROJECT_ID AND lhm.COMPANY_CODE = pj.COMPANY_CODE)
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."' 
AND ACTIVITY_CODE IN ( SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN IN ('TN','LC') AND PARENT = 0 )
AND ". $where ." GROUP BY LHM_DATE, LOCATION_CODE, ACTIVITY_CODE ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$ID_PROGRESS_TANAM = 1;
		$REALISASI_UNIT = "";
		$HASIL_KERJA = "";	
		foreach($objects as $obj)
        {
            $cell = array();
				$TGL_PROGRESS = $obj->LHM_DATE;
				$SATUAN = $obj->UNIT1;
				$ACTIVITY_DESC = $obj->DESCR;
				$AFD = $obj->AFD;
				
				array_push($cell, $ID_PROGRESS_TANAM);
				array_push($cell, $TGL_PROGRESS);
				array_push($cell, $obj->ACTIVITY_CODE);
				array_push($cell, $ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);  
				array_push($cell, $HASIL_KERJA);                       
                array_push($cell, $SATUAN);
				array_push($cell, $AFD);
				
					
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
			$ID_PROGRESS_TANAM++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
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
			$where = "AFD = '".$lc."'";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT ID_PROGRESS_TANAM, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, AFD, COMPANY_CODE FROM p_progress_tanam
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
  
  
		$sql = "SELECT ID_PROGRESS_TANAM, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, HASIL_KERJA, AFD, COMPANY_CODE FROM p_progress_tanam
WHERE ".$where." AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' 
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $obj->ID_PROGRESS_TANAM);
				array_push($cell, $obj->TGL_PROGRESS); 
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $obj->ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);
				array_push($cell, $obj->HASIL_KERJA);                       
                array_push($cell, $obj->SATUAN);
				array_push($cell, $obj->AFD);
	      	
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
	
	function cek_pp($tgl, $lc, $company){
		
		$query = $this->db->query("select count(*) as jumlah FROM p_progress_tanam where DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND AFD LIKE '".$lc."%' AND LEFT(ACTIVITY_CODE,2) IN ('84','82') AND COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}

		return $temp_result;
	}
	
	//add data
	function blok($afd, $q, $tgl, $company){
	
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR, m_project.AFD AS AFD FROM m_gang_activity_detail 
		LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
		LEFT JOIN m_project ON m_project.PROJECT_ID = m_gang_activity_detail.LOCATION_CODE
		WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' 
		AND m_gang_activity_detail.ACTIVITY_CODE like '".$q."%'
		AND m_project.AFD LIKE '".$afd."%'		
		AND LOCATION_TYPE_CODE = 'PJ' 
		AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
		AND ACTIVITY_CODE IN ( SELECT ACCOUNT_CODE FROM m_project_activity_map WHERE STATUS_PENGGUNAAN = 'LHM'  ) GROUP BY ACTIVITY_CODE");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	//add data
	function act($afd, $q, $act, $tgl, $company){
	
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR, m_project.AFD AS AFD FROM m_gang_activity_detail 
		LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
		LEFT JOIN m_project ON m_project.PROJECT_ID = m_gang_activity_detail.LOCATION_CODE
		WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' 
		AND m_gang_activity_detail.ACTIVITY_CODE = '".$act."'
		AND m_project.AFD LIKE '".$afd."%'		
		AND LOCATION_TYPE_CODE LIKE '".$q."%' 
		AND m_gang_activity_detail.COMPANY_CODE = '".$company."'
		AND ACTIVITY_CODE IN ( SELECT ACCOUNT_CODE FROM m_project_activity_map WHERE STATUS_PENGGUNAAN = 'LHM'  ) GROUP BY ACTIVITY_CODE,LOCATION_CODE");
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
		$this->db->where( 'ID_PROGRESS_TANAM', $id ); 
		$this->db->where( 'AFD', $afd ); 
		$this->db->where( 'TGL_PROGRESS', $tgl ); 
		$this->db->where( 'COMPANY_CODE', $company ); 	
		$this->db->delete('p_progress_tanam');   
	}
}   

?>
