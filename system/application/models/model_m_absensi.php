<?

class Model_m_absensi extends Model 
{

    function Model_m_absensi()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_absensi ( $id )
	{

		$this->db->select( 'TYPE_ABSENSI,DESKRIPSI' );
		$this->db->where( 'TYPE_ABSENSI', $id );
		$this->db->from('m_absensi');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_absensi ( $data )
	{
		$this->db->insert( 'm_absensi', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_absensi ( $id, $data )
	{
		$this->db->where( 'TYPE_ABSENSI', $id );  
		$this->db->update( 'm_absensi', $data );   
	}
	
	function enroll_m_absensi ( )
	{
		$this->db->select( 'TYPE_ABSENSI,DESKRIPSI');

		$this->db->from( 'm_absensi' );

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
