<?

class Model_m_company extends Model 
{

    function Model_m_company()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_company ( $id )
	{

		$this->db->select( 'COMPANY_CODE,COMPANY_NAME,COMPANY_ADDRESS,COMPANY_PHONE,COMPANY_EMAIL,COMPANY_NPWP,COMPANY_FLAG' );
		$this->db->where( 'COMPANY_CODE', $id );
		$this->db->from('m_company');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_company ( $data )
	{
		$this->db->insert( 'm_company', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_company ( $id, $data )
	{
		$this->db->where( 'COMPANY_CODE', $id );  
		$this->db->update( 'm_company', $data );   
	}
	
	function enroll_m_company ( )
	{
		$this->db->select( 'COMPANY_CODE,COMPANY_NAME,COMPANY_ADDRESS,COMPANY_PHONE,COMPANY_EMAIL,COMPANY_NPWP,COMPANY_FLAG');

		$this->db->from( 'm_company' );

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
		$this->db->where('COMPANY_CODE', $id);
		$this->db->delete('m_company'); 
	}
	
	// ---------------------------------- script untuk jqgrid
	
	
	//TODO: check XSS and SQL injection here
    function readByPagination()
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_company');

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
        $objects = $this->db->get("m_company")->result();
        $rows =  array();

        foreach($objects as $obj)
        {
            $cell = array();
                            array_push($cell, $obj->COMPANY_CODE);
                            array_push($cell, $obj->COMPANY_NAME);
							array_push($cell, $obj->COMPANY_ADDRESS);
                            array_push($cell, $obj->COMPANY_PHONE);
                            array_push($cell, $obj->COMPANY_EMAIL);
							array_push($cell, $obj->COMPANY_NPWP);
                            array_push($cell, $obj->COMPANY_FLAG);
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
	
	/*
		info_weighbridge_sta added by Asep, 20130611		
	*/
	function info_weighbridge_sta ( $id )
	{

		$this->db->select('STA_WEIGHBRIDGE' );
		$this->db->where( 'COMPANY_CODE', $id );
		$this->db->from('m_company');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row();
			$value = $row->STA_WEIGHBRIDGE; 
			return $value;
		}

	}

}   

?>
