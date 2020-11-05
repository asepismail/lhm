<?

class Model_m_infrastructure_type extends Model 
{

    function Model_m_infrastructure_type()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_infrastructure_type ( $id )
	{

		$this->db->select( 'IFTYPE,IFTYPE_NAME,CONTROL_JOB' );
		$this->db->where( 'IFTYPE', $id );
		$this->db->from('m_infrastructure_type');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_infrastructure_type ( $data )
	{
		$this->db->insert( 'm_infrastructure_type', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_infrastructure_type ( $id, $data )
	{
		$this->db->where( 'IFTYPE', $id );  
		$this->db->update( 'm_infrastructure_type', $data );   
	}
	
	function enroll_m_infrastructure_type ( )
	{
		$this->db->select( 'IFTYPE,IFTYPE_NAME,CONTROL_JOB');

		$this->db->from( 'm_infrastructure_type' );

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
		$this->db->where('IFTYPE', $id);
		$this->db->delete('m_infrastructure_type'); 
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
        $count = $this->db->count_all('m_infrastructure_type');

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
        $objects = $this->db->get("m_infrastructure_type")->result();
        $rows =  array();
		
        foreach($objects as $obj)
        {
            $cell = array();
							array_push($cell, $obj->IFTYPE);
                            array_push($cell, $obj->IFTYPE_NAME);
							array_push($cell, $obj->CONTROL_JOB);
                            
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
