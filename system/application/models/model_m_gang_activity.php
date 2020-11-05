<?

class Model_m_gang_activity extends Model 
{

    function Model_m_gang_activity()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_gang_activity ( $id )
	{

		$this->db->select( 'GANG_CODE,MANDORE1_CODE,MANDORE_CODE,KERANI_CODE,ITEM_CODE1,ITEM_CODE2,ITEM_CODE3,INPUT_BY,INPUT_DATE' );
		$this->db->where( 'GANG_CODE', $id );
		$this->db->from('m_gang_activity');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_gang_activity ( $data )
	{
		$this->db->insert( 'm_gang_activity', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_gang_activity ( $id, $data )
	{
		$this->db->where( 'GANG_CODE', $id );  
		$this->db->update( 'm_gang_activity', $data );   
	}
	
	function enroll_m_gang_activity ( )
	{
		$this->db->select( 'GANG_CODE,MANDORE1_CODE,MANDORE_CODE,KERANI_CODE,ITEM_CODE1,ITEM_CODE2,ITEM_CODE3,INPUT_BY,INPUT_DATE');

		$this->db->from( 'm_gang_activity' );

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
