<?

class model_contract extends Model 
{
	function model_contract()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_contract ( $data )
	{
		$this->db->insert( 'p_contract', $data );
		
		return $this->db->insert_id();   
	}
	
}
?>