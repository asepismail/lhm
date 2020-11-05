<?

class Model_p_kontrak_progress extends Model 
{

    function Model_p_kontrak_progress()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_p_kontrak_progress ( $id )
	{

		$this->db->select( 'ID_KONTRAK,TGL_KONTRAK,ID_KONTRAKTOR,LOCATION_TYPE_CODE,LOCATION_CODE,LOCATION_DESC,ACTIVITY_CODE,ACTIVITY_DESC,HSL_SATUAN,HSL_VOLUME,TARIF_SATUAN,NILAI,COMPANY_CODE' );
		$this->db->where( 'ID_KONTRAK', $id );
		$this->db->from('p_kontrak_progress');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_p_kontrak_progress ( $data )
	{
		$this->db->insert( 'p_kontrak_progress', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_kontrak_progress ( $id, $data )
	{
		$this->db->where( 'ID_KONTRAK', $id );  
		$this->db->update( 'p_kontrak_progress', $data );   
	}
	
	function enroll_p_kontrak_progress ( )
	{
		$this->db->select( 'ID_KONTRAK,TGL_KONTRAK,ID_KONTRAKTOR,LOCATION_TYPE_CODE,LOCATION_CODE,LOCATION_DESC,ACTIVITY_CODE,ACTIVITY_DESC,HSL_SATUAN,HSL_VOLUME,TARIF_SATUAN,NILAI,COMPANY_CODE');

		$this->db->from( 'p_kontrak_progress' );

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
