<?

class model_p_machine extends Model 
{
	function model_p_machine()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_machine_activity ( $data )
	{
		$this->db->insert( 'p_machine_meter', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_machine_activity ( $id,$company, $data )
	{
		$this->db->where( 'ID', $id ); 
		$this->db->where( 'COMPANY_CODE', $company );   	
		$this->db->update( 'p_machine_meter', $data );   
	}
	
	function delete_machine_activity ( $id, $company)
	{
		$this->db->where( 'ID', $id );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('p_machine_meter');   
	}
	
	function grid_ma($mc, $bln, $thn, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$sql2 = "SELECT * from p_machine_meter WHERE KODE_MESIN = '".$mc."' and BULAN = '".$bln."' AND TAHUN = '".$thn."' AND COMPANY_CODE='".$company."'";
       
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
        //$this->db->order_by("$sidx", "$sord");
        $sql = "SELECT * from p_machine_meter WHERE KODE_MESIN = '".$mc."' and BULAN = '".$bln."' AND TAHUN = '".$thn."' AND COMPANY_CODE='".$company."' ORDER BY ".$sidx." ".$sord." ";

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
       		
		$no_ma = 1;
		$action = "";
        foreach($objects as $obj)
        {
            $cell = array();
							array_push($cell, $no_ma);
							array_push($cell, $obj->ID);
	                        array_push($cell, $obj->KODE_MESIN);
                            array_push($cell, $obj->SATUAN_PRESTASI);
							array_push($cell, $obj->BULAN);
                            array_push($cell, $obj->TAHUN);
                            array_push($cell, $obj->TGL_AKTIVITAS);
							array_push($cell, $obj->LOCATION_TYPE_CODE);
                            array_push($cell, $obj->LOCATION_CODE);
                            array_push($cell, $obj->ACTIVITY_CODE);
                            array_push($cell, $obj->METER_PEMAKAIAN);
                            array_push($cell, $obj->JAM_KERJA);
                            array_push($cell, $obj->KETERANGAN);
							array_push($cell, $action);
                        $row = new stdClass();
            $row->id = $cell[1];
            $row->cell = $cell;
            array_push($rows, $row);
			
			$no_ma++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
		
	//autocomplete
	function kode_mesin($company, $q) {
		
		$limit = $this->input->post('limit');
		
		$query = $this->db->query("SELECT MACHINECODE, DESCRIPTION, SATUAN_PRESTASI FROM m_machine WHERE COMPANY_CODE = '".$company."' AND MACHINECODE LIKE '".$q."%'");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function location_type(){
		$q = $this->input->post('q');
		$limit = $this->input->post('limit');
		
		$query = $this->db->query("SELECT LOCATION_TYPE_CODE, LOCATION_TYPE_NAME FROM m_location_type WHERE ACTIVE=1");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	
	function location($loc, $q, $company){
		
		$limit = $this->input->post('limit');
	  
		$query = $this->db->query("SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_TYPE_CODE='".$loc."' AND LOCATION_CODE LIKE '".$q."%' AND COMPANY_CODE = '".$company."' AND INACTIVE=0");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	function activity($act, $q){
	
		$query = $this->db->query("SELECT DISTINCT m.ACCOUNT_CODE as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as COA_DESCRIPTION from m_activity_map m 
						LEFT JOIN `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) 
						WHERE m.LOCATION_TYPE = '".$act."' AND ACCOUNT_CODE like '%".$q."%' AND m.STATUS_PENGGUNAAN = 'BM'");
		
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;	
		}

		return $temp_result;
	}  
	
	// validasi
	
	function lokasi_validate($lc, $ltc){
		$query = $this->db->query("SELECT LOCATION_CODE FROM m_location where TRIM(LOCATION_TYPE_CODE) = TRIM('".$ltc."') AND TRIM(LOCATION_CODE) = TRIM('".$lc."') AND INACTIVE=0");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function aktivitas_validate($ac, $ltc){
		$query = $this->db->query("SELECT ACCOUNT_CODE FROM m_activity_map where LOCATION_TYPE = '".$ltc."' AND ACCOUNT_CODE = '".$ac."' AND STATUS_PENGGUNAAN = 'BM'");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function location_pj($q, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
        $query = $this->db->query("SELECT PROJECT_ID AS LOCATION_CODE, CONCAT(PROJECT_DESC,' : ',PROJECT_LOCATION) AS DESCRIPTION FROM m_project 
			WHERE PROJECT_ID LIKE '".$this->db->escape_str($q)."%' AND COMPANY_CODE = '".$this->db->escape_str($company)."' AND PROJECT_STATUS = 1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
	
    function activity_pj($lc, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
        $query = $this->db->query("SELECT PROJECT_ACTIVITY AS ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION FROM m_project_detail 
        LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_detail.PROJECT_ACTIVITY) WHERE 
        MASTER_PROJECT_ID ='".$this->db->escape_str($lc)."' AND COMPANY_CODE = '".$this->db->escape_str($company)."' order by PROJECT_ACTIVITY asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function activity_pj_lctn($ac,$pj_subtype){
    
        $query = $this->db->query("SELECT ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION 
FROM m_project_activity_map LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_activity_map.ACCOUNT_CODE) WHERE 
 LOCATION_SUBTYPE ='".$this->db->escape_str($pj_subtype)."' AND STATUS_PENGGUNAAN = 'BM' AND ACCOUNTCODE like '%".$this->db->escape_str($ac)."%' ORDER BY ACCOUNTCODE asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
	
}

?>