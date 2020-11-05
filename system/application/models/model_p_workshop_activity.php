<?

class Model_p_workshop_activity extends Model 
{

    function Model_p_workshop_activity()
    {
        parent::Model(); 

		$this->load->database();
    }

	function insert_p_workshop_activity ( $data )
	{
		$this->db->insert( 'p_workshop_activity', $data );
		return $this->db->insert_id();   
	}
	
	function update_p_workshop_activity ( $id,$company, $data )
	{
		$this->db->where( 'ID', $id );  
		$this->db->where( 'COMPANY_CODE', $company );  
		$this->db->update( 'p_workshop_activity', $data );   
	}
	
	function delete_p_workshop_activity ( $id, $company)
	{
		$this->db->where( 'ID', $id );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('p_workshop_activity');   
	}
	
	function grid_wa($wc, $bln, $thn, $company)
    	{
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$sql2 = "SELECT * from p_workshop_activity WHERE KODE_WORKSHOP = '".$wc."' and BULAN = '".$bln."' AND TAHUN = '".$thn."' AND COMPANY_CODE='".$company."'";
       
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
      
	    $sql = "SELECT * from p_workshop_activity WHERE KODE_WORKSHOP = '".$wc."' and BULAN = '".$bln."' AND TAHUN = '".$thn."' AND COMPANY_CODE='".$company."' ORDER BY ".$sidx." ".$sord." ";

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
       		
		$no_ma = 1;
		$action = "";
        foreach($objects as $obj)
        {
            $cell = array();
			
							array_push($cell, $no_ma);
							array_push($cell, $obj->ID);
	                        array_push($cell, $obj->KODE_WORKSHOP);
                        	//array_push($cell, $obj->BULAN);
                            //array_push($cell, $obj->TAHUN);
                            array_push($cell, $obj->TGL_AKTIVITAS);
							array_push($cell, $obj->LOCATION_TYPE_CODE);
							array_push($cell, $obj->LOCATION_CODE);
							array_push($cell, $obj->ACTIVITY_CODE);
                           	array_push($cell, $obj->JAM_KERJA);
                            array_push($cell, $obj->COMPANY_CODE);
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
	function kode_ws($company){
		$q = $this->input->post('q');
		$limit = $this->input->post('limit');
		
		$query = $this->db->query("SELECT WORKSHOPCODE, CONCAT(WORKSHOPCODE,'-',DESCRIPTION) AS DESCRIPTION FROM m_workshop WHERE COMPANY_CODE = '".$company."'");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
		
	function location($loc, $q, $company){
		$limit = $this->input->post('limit');
		$qryor = "AND LOCATION_TYPE_CODE = '".$loc."' AND COMPANY_CODE = '".$company."' AND INACTIVE=0";
		$qry = "SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_CODE LIKE '".$q."%' " . $qryor;
		$qry .= " OR DESCRIPTION LIKE '%".$q."%' " . $qryor;
		$query = $this->db->query($qry);
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}
		return $temp_result;
	} 
	
	function activity($act, $q){
		$qryor = " AND m.LOCATION_TYPE = '".$act."' AND m.STATUS_PENGGUNAAN = 'BW' ";
		$qry = "SELECT m.ACCOUNT_CODE as ACCOUNTCODE, m_coa.COA_DESCRIPTION as COA_DESCRIPTION from m_activity_map m ";
		$qry .= "LEFT JOIN `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) ";
		$qry .= " WHERE m.ACCOUNT_CODE like '".$q."%'" . $qryor . " OR m_coa.`COA_DESCRIPTION` LIKE '%".$q."%'". $qryor ;
		$query = $this->db->query($qry);
		
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;	
		}

		return $temp_result;
	}
	
	function lokasi_validate($lc, $ltc, $company){
		$query = $this->db->query("SELECT LOCATION_CODE FROM m_location where TRIM(LOCATION_TYPE_CODE) = TRIM('".$ltc."') AND TRIM(LOCATION_CODE) = TRIM('".$lc."') AND COMPANY_CODE = '".$company."' AND INACTIVE=0");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function aktivitas_validate($ac, $ltc){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_activity_map where LOCATION_TYPE = '".$this->db->escape_str($ltc).
        "' AND ACCOUNT_CODE = '".$this->db->escape_str($ac)."' AND STATUS_PENGGUNAAN = 'BW'");;
        $temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
    }
}   

?>
