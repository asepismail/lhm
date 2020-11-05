<?

class Model_m_employee_type extends Model 
{

    function Model_m_employee_type()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_employee_type ( $id )
	{

		$this->db->select( 'EMPLOYEE_TYPE,DESCRIPTION' );
		$this->db->where( 'EMPLOYEE_TYPE', $id );
		$this->db->from('m_employee_type');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_employee_type ( $data )
	{
		$this->db->insert( 'm_employee_type', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_employee_type ( $id, $data )
	{
		$this->db->where( 'EMPLOYEE_TYPE', $id );  
		$this->db->update( 'm_employee_type', $data );   
	}
	
	function enroll_m_employee_type ( )
	{
		$this->db->select( 'EMPLOYEE_TYPE,DESCRIPTION');

		$this->db->from( 'm_employee_type' );

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
