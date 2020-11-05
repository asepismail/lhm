<? 
class model_rpt_rekon_badu extends Model 
{

    function model_rpt_rekon_badu()
    {
        parent::Model(); 

		$this->load->database();
    }

	function gen_du ($from,$to,$company){
		$sql = "CALL sp_rekon_badu_du('".$company."','".$from."','".$to."')";
		$query = $this->db->query($sql);
		
		$temp = $query->row_array();
		$temp_result = array(); 
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		$this->db->close();
		return $temp_result;
	}
	
	function gen_ba_global ($from, $to, $company){
		
		$sql = "CALL sp_rekon_badu_ba('".$company."','".$from."','".$to."')";
		$query = $this->db->query($sql);
		
		$temp = $query->row_array();
		$temp_result = array(); 
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		$this->db->close();
		return $temp_result;
	}
}

?>