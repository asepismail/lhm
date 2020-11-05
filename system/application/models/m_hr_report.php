<?php

class m_hr_report extends Model{
    function model_rpt_du()
    {
        parent::Model(); 
        $this->load->database();
		$this->load->library('global_func');
		$this->load->library('session');
    }
	/* menampilkan data per type karyawan */
	function rpt_hr_type($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_typekaryawan('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_typekaryawan_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function rpt_hr_jk($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_jeniskelamin('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_jeniskelamin_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function rpt_hr_status($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_status('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_status_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function rpt_hr_agama($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_religion('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_religion_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function rpt_hr_umur($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_umur('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_umur_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function rpt_hr_pangkat($company, $periode){
		if($company !== 'PAG'){
			$query = $this->db->query("CALL sp_hr_report_pangkat('".$company."','".$periode."')");
		} else {
			$query = $this->db->query("CALL sp_hr_report_pangkat_all('".$periode."')");
		}
		
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
	
	function getCompanyDesc($company){
		$query = $this->db->query("SELECT COMPANY_NAME FROM m_company WHERE COMPANY_CODE = '".$company."'");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
}

?>