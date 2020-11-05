<?

class Model_m_user_group extends Model 
{

    function Model_m_user_group()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_user_group ( $id )
	{

		$this->db->select( 'USER_GROUP_ID,USER_GROUP_NAME' );
		$this->db->where( 'USER_GROUP_ID', $id );
		$this->db->from('m_user_group');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_user_group ( $data )
	{
		$this->db->insert( 'm_user_group', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_user_group ( $id, $data )
	{
		$this->db->where( 'USER_GROUP_ID', $id );  
		$this->db->update( 'm_user_group', $data );   
	}
	
	function enroll_m_user_group ( )
	{
		$this->db->select( 'USER_GROUP_ID,USER_GROUP_NAME');

		$this->db->from( 'm_user_group' );

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
