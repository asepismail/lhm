<?

class php extends Model 
{

    function php()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function drop_temp() {
	
		$query = $this->db->query("DROP TEMPORARY TABLE IF EXISTS rptdu;");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
	function rpttbdu($company, $from, $to) {
	
		$query = $this->db->query("CALL sp_tb_rptdu('".$company."','".$from."','".$to."')");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
	function gen_pph($company, $from, $to) {
	
		$query = $this->db->query("CALL sp_update_pph21('".$company."','".$from."','".$to."')");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
}
?>