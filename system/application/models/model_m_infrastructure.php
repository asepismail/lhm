<?

class Model_m_infrastructure extends Model 
{

    function Model_m_infrastructure()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_infrastructure ( $id )
	{

		$this->db->select( 'IFCODE,FIXEDASSETCODE,IFTYPE,IFSUBTYPE,IFNAME,IFLENGTH,IFWIDTH,UOM,INSTALLDATE,DEVELOPMENT_COST,VOLUME,ROLLING,FLAT,LOWLAND,ESTATE,DIVISION,INACTIVEDATE,COMPANY_CODE' );
		$this->db->where( 'IFCODE', $id );
		$this->db->from('m_infrastructure');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_infrastructure ( $data )
	{
		$this->db->insert( 'm_infrastructure', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_infrastructure ( $id, $data )
	{
		$this->db->where( 'IFCODE', $id );  
		$this->db->update( 'm_infrastructure', $data );   
	}
	
	function enroll_m_infrastructure ( )
	{
		$this->db->select( 'IFCODE,FIXEDASSETCODE,IFTYPE,IFSUBTYPE,IFNAME,IFLENGTH,IFWIDTH,UOM,INSTALLDATE,DEVELOPMENT_COST,VOLUME,ROLLING,FLAT,LOWLAND,ESTATE,DIVISION,INACTIVEDATE,COMPANY_CODE');

		$this->db->from( 'm_infrastructure' );

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
		$this->db->where('IFCODE', $id);
		$this->db->delete('m_infrastructure'); 
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
        $count = $this->db->count_all('m_infrastructure');

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
        $objects = $this->db->get("m_infrastructure")->result();
        $rows =  array();
		
		
		foreach($objects as $obj)
        {
            $cell = array();
							array_push($cell, $obj->IFCODE);
                            array_push($cell, $obj->FIXEDASSETCODE);
							array_push($cell, $obj->IFTYPE);
                            array_push($cell, $obj->IFSUBTYPE);
							array_push($cell, $obj->IFNAME);
							array_push($cell, $obj->INSTALLDATE);
							array_push($cell, $obj->DEVELOPMENT_COST);
							array_push($cell, $obj->VOLUME);
							array_push($cell, $obj->IFLENGTH);
							array_push($cell, $obj->IFWIDTH);
							array_push($cell, $obj->UOM);
							array_push($cell, $obj->ROLLING);
							array_push($cell, $obj->FLAT);
							array_push($cell, $obj->LOWLAND);
							array_push($cell, $obj->ESTATE);
							array_push($cell, $obj->DIVISION);
							array_push($cell, $obj->INACTIVEDATE);
							array_push($cell, $obj->COMPANY_CODE);					
							
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
