<?

class Model_m_user_grole extends Model 
{

    function Model_m_user_grole()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_user_grole ( $id )
	{

		$this->db->select( 'GROUP_ID,MENU_ID,ROLE_ADD,ROLE_EDIT,ROLE_DELETE,ROLE_REPORT' );
		$this->db->where( 'GROUP_ID', $id );
		$this->db->from('m_user_grole');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_user_grole ( $data )
	{
		$this->db->insert( 'm_user_grole', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_user_grole ( $id, $data )
	{
		$this->db->where( 'GROUP_ID', $id );  
		$this->db->update( 'm_user_grole', $data );   
	}
	
	function enroll_m_user_grole ( )
	{
		$this->db->select( 'GROUP_ID,MENU_ID,ROLE_ADD,ROLE_EDIT,ROLE_DELETE,ROLE_REPORT');

		$this->db->from( 'm_user_grole' );

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
