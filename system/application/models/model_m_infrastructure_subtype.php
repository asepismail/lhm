<?

class Model_m_infrastructure_subtype extends Model 
{

    function Model_m_infrastructure_subtype()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_infrastructure_subtype ( $id )
	{

		$this->db->select( 'IFSUBTYPE,IFSUBTYPE_NAME,IFTYPE' );
		$this->db->where( 'IFSUBTYPE', $id );
		$this->db->from('m_infrastructure_subtype');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_infrastructure_subtype ( $data )
	{
		$this->db->insert( 'm_infrastructure_subtype', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_infrastructure_subtype ( $id, $data )
	{
		$this->db->where( 'IFSUBTYPE', $id );  
		$this->db->update( 'm_infrastructure_subtype', $data );   
	}
	
	function enroll_m_infrastructure_subtype ( )
	{
		$this->db->select( 'IFSUBTYPE,IFSUBTYPE_NAME,IFTYPE');

		$this->db->from( 'm_infrastructure_subtype' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	
		  //----------------------------jqgrid parsing data
			
	function delete($id)
	{
		$this->db->where('IFSUBTYPE', $id);
		$this->db->delete('m_infrastructure_subtype'); 
	}
	
	
	//TODO: check XSS and SQL injection here
    function readByPagination()
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_infrastructure_subtype');

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
        $objects = $this->db->get("m_infrastructure_subtype")->result();
        $rows =  array();
		
		foreach($objects as $obj)
        {
            $cell = array();
							array_push($cell, $obj->IFSUBTYPE);
                            array_push($cell, $obj->IFSUBTYPE_NAME);
							array_push($cell, $obj->IFTYPE);
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
