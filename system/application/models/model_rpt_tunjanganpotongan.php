<?

class model_rpt_tunjanganpotongan extends Model 
{
    function model_rpt_tunjanganpotongan()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function model_rpt_kontanan($company, $periode){
		$sql = "SELECT EMPLOYEE_CODE, emp.NAMA, SUM(POTONGAN_KONTANAN) AS RUPIAH FROM m_gang_activity_detail "; 
		$sql .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ";
		$sql .= " ON emp.NIK = m_gang_activity_detail.EMPLOYEE_CODE WHERE KONTANAN = 1 AND COMPANY_CODE = '".$company."'";
		$sql .= " AND DATE_FORMAT(LHM_DATE,'%Y%m') = '".$periode."' GROUP BY EMPLOYEE_CODE ";
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