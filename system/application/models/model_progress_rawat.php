<?

class model_progress_rawat extends Model 
{
	function model_progress_rawat()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_progress_rawat ( $data )
	{
		$this->db->insert( 'p_progress_rawat', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_progress_rawat ( $afd,$tgl_progress,$activity,$location,$company, $data )
	{
		$this->db->where('AFD',$afd);
		$this->db->where('TGL_PROGRESS',$tgl_progress);
		$this->db->where('ACTIVITY_CODE',$activity);
		$this->db->where('LOCATION_CODE',$location);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update( 'p_progress_rawat', $data );   
	}
	
	function delete_progress_rawat ( $id, $company)
	{
		$this->db->where( 'ID_PROGRESS_RAWAT', $id );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('p_progress_rawat');   
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
        //$sidx = $this->input->post('sidx');
        //$sord = $this->input->post('sord');
		$sidx = 'lhm.ACTIVITY_CODE ASC, lhm.LOCATION_CODE';
		$sord = 'ASC';
		
		if($lc != '' || $lc != 'xx'){
			$where = "lhm.LOCATION_CODE LIKE '".$lc."%'";
		} else {
			$where = "1 = 1";
		}
		
		$sql2 = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail lhm LEFT JOIN ( SELECT ACCOUNTCODE,ACCOUNTDESC, UNIT1,UNIT2 FROM m_progress_map WHERE PENGGUNAAN = 'RWT' AND PARENT <> 1) pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE) WHERE ".$where." AND lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."' AND lhm.ACTIVITY_CODE LIKE '850%' GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE";
   
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
  
		$sql = "SELECT lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, pm.ACCOUNTDESC AS DESCR, pm.UNIT1, pm.UNIT2 FROM m_gang_activity_detail lhm LEFT JOIN ( SELECT ACCOUNTCODE,ACCOUNTDESC, UNIT1,UNIT2 FROM m_progress_map WHERE PENGGUNAAN = 'RWT' AND PARENT <> 1) pm ON (pm.ACCOUNTCODE = lhm.ACTIVITY_CODE) WHERE ".$where." AND lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE,'%Y%m%d') = '".$tgl."' AND lhm.ACTIVITY_CODE LIKE '850%' GROUP BY lhm.LHM_DATE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$ID_PROGRESS_RAWAT = 1;
		$HASIL_KERJA = "";
		foreach($objects as $obj)
        {
            $cell = array();
				$TGL_PROGRESS = $obj->LHM_DATE;
				$SATUAN = $obj->UNIT1;
				$ACTIVITY_DESC = $obj->DESCR;
				
				array_push($cell, $ID_PROGRESS_RAWAT);
				array_push($cell, $TGL_PROGRESS);
				array_push($cell, $obj->ACTIVITY_CODE);                       
                array_push($cell, $ACTIVITY_DESC);
				array_push($cell, $obj->LOCATION_CODE);
				array_push($cell, $HASIL_KERJA);                       
                array_push($cell, $SATUAN);
				//array_push($cell, $obj->HK);
				//array_push($cell, $obj->REALISASI);							
				//array_push($cell, $REALISASI_UNIT);	
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
			$ID_PROGRESS_RAWAT++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
	
	/* baca data yang ada di p_progress_rawat */
	function cek_pr($tgl, $lc, $company){
		$query = $this->db->query("select count(*) as jumlah FROM p_progress_rawat where LOCATION_CODE LIKE '".$lc."%' and DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' and COMPANY_CODE = '".$company."'");
		
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
        //$sidx = $this->input->post('sidx');
        //$sord = $this->input->post('sord');
		$sidx = 'ACTIVITY_CODE ASC, LOCATION_CODE';
		$sord = 'ASC';
		
		if($lc != ''){
			$where = "LOCATION_CODE like '".$lc."%'";
		} else {
			$where = "1 = 1";
		}		
		
		$sql2 = "SELECT ID_PROGRESS_RAWAT, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, 
	HASIL_KERJA, COMPANY_CODE FROM p_progress_rawat
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
  
  
		$sql = "SELECT ID_PROGRESS_RAWAT, AFD, TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, ACTIVITY_DESC, SATUAN, 
	HASIL_KERJA, COMPANY_CODE  FROM p_progress_rawat
WHERE ".$where." AND DATE_FORMAT(TGL_PROGRESS, '%Y%m%d') = '".$tgl."' AND COMPANY_CODE = '".$company."' 
ORDER BY ".$sidx." ".$sord.""; 

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;		
		
		foreach($objects as $obj)
        {
            $cell = array();
			
				array_push($cell, $obj->ID_PROGRESS_RAWAT);
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
	function blok($afd, $q, $tgl, $company){
	
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESCR FROM m_gang_activity_detail 
		LEFT JOIN m_coa ON m_coa.ACCOUNTCODE =  m_gang_activity_detail.ACTIVITY_CODE
		WHERE LEFT(LOCATION_CODE,2) = '".$afd."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' AND LOCATION_TYPE_CODE = 'OP' AND COMPANY_CODE = '".$company."'
		AND ACTIVITY_CODE LIKE '".$q."%' AND ACTIVITY_CODE LIKE '85%'  group by ACTIVITY_CODE");
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
	  
		$query = $this->db->query("SELECT LOCATION_CODE, ACTIVITY_CODE FROM m_gang_activity_detail 
		WHERE LEFT(LOCATION_CODE,2) = '".$afd."' AND DATE_FORMAT(LHM_DATE,'%Y%m%d') = '".$tgl."' AND LOCATION_TYPE_CODE = 'OP' AND COMPANY_CODE = '".$company."'
		AND ACTIVITY_CODE = '".$act."' AND ACTIVITY_CODE LIKE '85%' and LOCATION_CODE LIKE '".$q."%'  group by ACTIVITY_CODE");
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
		$this->db->where( 'ID_PROGRESS_RAWAT', $id ); 
		$this->db->where( 'AFD', $afd ); 
		$this->db->where( 'TGL_PROGRESS', $tgl ); 
		$this->db->where( 'COMPANY_CODE', $company ); 	
		$this->db->delete('p_progress_rawat');   
	}
		
}

?>