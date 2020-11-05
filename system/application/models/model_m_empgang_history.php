<?

class Model_m_empgang_history extends Model 
{

    function Model_m_empgang_history()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_empgang_history ( $id )
	{

		$this->db->select( 'GANG_CODE,EMPLOYEE_CODE,BASIC_SALARY,FAMILY_STATUS_RICE,FAMILY_STATUS_STAX,GRADE_ID,PAYROLL,MONTH,YEAR' );
		$this->db->where( 'GANG_CODE', $id );
		$this->db->from('m_empgang_history');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_empgang_history ( $data )
	{
		$this->db->insert( 'm_empgang_history', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_empgang_history ( $id, $data )
	{
		$this->db->where( 'GANG_CODE', $id );  
		$this->db->update( 'm_empgang_history', $data );   
	}
	
	function enroll_m_empgang_history ( )
	{
		$this->db->select( 'GANG_CODE,EMPLOYEE_CODE,BASIC_SALARY,FAMILY_STATUS_RICE,FAMILY_STATUS_STAX,GRADE_ID,PAYROLL,MONTH,YEAR');

		$this->db->from( 'm_empgang_history' );

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
