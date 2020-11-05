<?

class Model_m_location_type extends Model 
{

    function Model_m_location_type()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_location_type ( $id )
	{

		$this->db->select( 'LOCATION_TYPE_CODE,LOCATION_TYPE_NAME' );
		$this->db->where( 'LOCATION_TYPE_CODE', $id );
		$this->db->from('m_location_type');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_location_type ( $data )
	{
		$this->db->insert( 'm_location_type', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_location_type ( $id, $data )
	{
		$this->db->where( 'LOCATION_TYPE_CODE', $id );  
		$this->db->update( 'm_location_type', $data );   
	}
	
	function enroll_m_location_type ( )
	{
		$this->db->select( 'LOCATION_TYPE_CODE,LOCATION_TYPE_NAME');

		$this->db->from( 'm_location_type' );

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
