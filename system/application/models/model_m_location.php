<?

class Model_m_location extends Model 
{

    function Model_m_location()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_location ( $id )
	{

		$this->db->select( 'LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,INACTIVE,INACTIVE_DATE' );
		$this->db->where( 'LOCATION_CODE', $id );
		$this->db->from('m_location');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_location ( $data )
	{
		$this->db->insert( 'm_location', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_location ( $id, $data )
	{
		$this->db->where( 'LOCATION_CODE', $id );  
		$this->db->update( 'm_location', $data );   
	}
	
	function enroll_m_location ( )
	{
		$this->db->select( 'LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,INACTIVE,INACTIVE_DATE');

		$this->db->from( 'm_location' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	
	    


}   

?>
