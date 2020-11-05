<?

class Model_m_coa extends Model 
{

    function Model_m_coa()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_coa ( $id )
	{

		$this->db->select( 'ACCOUNTCODE,ACCOUNTTYPE,COA_GROUPTYPE,COA_OPERATIONAL,COA_DESCRIPTION,COA_STATUS,COA_INPUTBY,COA_INPUTDATE' );
		$this->db->where( 'ACCOUNTCODE', $id );
		$this->db->from('m_coa');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_coa ( $data )
	{
		$this->db->insert( 'm_coa', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_coa ( $id, $data )
	{
		$this->db->where( 'ACCOUNTCODE', $id );  
		$this->db->update( 'm_coa', $data );   
	}
	
	function enroll_m_coa ( )
	{
		$this->db->select( 'ACCOUNTCODE,ACCOUNTTYPE,COA_GROUPTYPE,COA_OPERATIONAL,COA_DESCRIPTION,COA_STATUS,COA_INPUTBY,COA_INPUTDATE');

		$this->db->from( 'm_coa' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}

	//function delete	
	function delete($id)
	{
		$this->db->where('ACCOUNTCODE', $id);
		$this->db->delete('m_coa'); 
	}
	
	//---------------------------------------untuk jqquery------------------------

	//TODO: check XSS and SQL injection here
    function readByPagination()
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_coa');

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
        $objects = $this->db->get("m_coa")->result();
        $rows =  array();

        foreach($objects as $obj)
        {
            $cell = array();
                            array_push($cell, $obj->ACCOUNTCODE);
                            array_push($cell, $obj->ACCOUNTTYPE);
							array_push($cell, $obj->COA_GROUPTYPE);
							array_push($cell, $obj->COA_OPERATIONAL);
                            array_push($cell, $obj->COA_DESCRIPTION);
                            array_push($cell, $obj->COA_STATUS);
							array_push($cell, $obj->COA_INPUTBY);
							array_push($cell, $obj->COA_INPUTDATE);
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
