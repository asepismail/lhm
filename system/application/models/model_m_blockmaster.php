<?

class Model_m_blockmaster extends Model 
{

    function Model_m_blockmaster()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_blockmaster ( $id )
	{

		$this->db->select( 'CONCESSIONID,COMPANY_CODE,NOHGU,BLOCKID,DESCRIPTION,SOILTYPE,TOPOGRAPH,HECTARAGE,PLANTABLE,UNPLANTABLE,INACTIVE,INACTIVEDATE,ROLLING,FLAT,LOWLAND,PLANTED,UNPLANTED,NONEFFECTIVE,VEGETATION,INTIPLASMA' );
		$this->db->where( 'CONCESSIONID', $id );
		$this->db->from('m_blockmaster');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_blockmaster ( $data )
	{
		$this->db->insert( 'm_blockmaster', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_blockmaster ( $id, $data )
	{
		$this->db->where( 'CONCESSIONID', $id );  
		$this->db->update( 'm_blockmaster', $data );   
	}
	
	function enroll_m_blockmaster ( )
	{
		$this->db->select( 'CONCESSIONID,COMPANY_CODE,NOHGU,BLOCKID,DESCRIPTION,SOILTYPE,TOPOGRAPH,HECTARAGE,PLANTABLE,UNPLANTABLE,INACTIVE,INACTIVEDATE,ROLLING,FLAT,LOWLAND,PLANTED,UNPLANTED,NONEFFECTIVE,VEGETATION,INTIPLASMA');

		$this->db->from( 'm_blockmaster' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	function delete($id)
	{
		$this->db->where('BLOCKID', $id);
		$this->db->delete('m_blockmaster'); 
	}
	
	
	//----------------------------jqgrid parsing data
	
	//TODO: check XSS and SQL injection here
    function readByPagination()
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_blockmaster');

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
        $objects = $this->db->get("m_blockmaster")->result();
        $rows =  array();

        foreach($objects as $obj)
        {
            $cell = array();
							array_push($cell, $obj->CONCESSIONID);
                            array_push($cell, $obj->COMPANY_CODE);
							array_push($cell, $obj->NOHGU);
                            array_push($cell, $obj->BLOCKID);
							array_push($cell, $obj->DESCRIPTION);
							array_push($cell, $obj->SOILTYPE);
                            array_push($cell, $obj->TOPOGRAPH);
                            array_push($cell, $obj->HECTARAGE);
							array_push($cell, $obj->PLANTABLE);
							array_push($cell, $obj->UNPLANTABLE);
							array_push($cell, $obj->INACTIVE);
							array_push($cell, $obj->INACTIVEDATE);
							array_push($cell, $obj->ROLLING);
							array_push($cell, $obj->FLAT);
							array_push($cell, $obj->LOWLAND);
							array_push($cell, $obj->PLANTED);
							array_push($cell, $obj->UNPLANTED);
							array_push($cell, $obj->NONEFFECTIVE);
							array_push($cell, $obj->VEGETATION);
							array_push($cell, $obj->INTIPLASMA);
							
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
