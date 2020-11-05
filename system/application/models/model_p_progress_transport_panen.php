<?

class Model_p_progress_transport_panen extends Model 
{

    function Model_p_progress_transport_panen()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_p_progress_transport_panen ( $id )
	{

		$this->db->select( 'JENIS_PROGRESS,TGL_PROGRESS,ACTIVITY_CODE,ACTIVITY_DESC,ACTIVITY_LOCATION,SATUAN,HASIL_KERJA,REALISASI,HK_PER_SATUAN,INPUT_BY,INPUT_DATE,COMPANY_CODE' );
		$this->db->where( 'JENIS_PROGRESS', $id );
		$this->db->from('p_progress_transport_panen');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_p_progress_transport_panen ( $data )
	{
		$this->db->insert( 'p_progress_transport_panen', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_p_progress_transport_panen ( $id, $data )
	{
		$this->db->where( 'JENIS_PROGRESS', $id );  
		$this->db->update( 'p_progress_transport_panen', $data );   
	}
	
	function enroll_p_progress_transport_panen ( )
	{
		$this->db->select( 'JENIS_PROGRESS,TGL_PROGRESS,ACTIVITY_CODE,ACTIVITY_DESC,ACTIVITY_LOCATION,SATUAN,HASIL_KERJA,REALISASI,HK_PER_SATUAN,INPUT_BY,INPUT_DATE,COMPANY_CODE');

		$this->db->from( 'p_progress_transport_panen' );

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
