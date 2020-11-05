<?php
class model_rpt_tanam_sisip extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function cekDataHistory($periode, $type, $company){
		$where = "";
		if($type == "bulanan"){
			$where = " AND DATE_FORMAT(TGL_PROGRESS,'%Y%m') = '".$periode."'";
		} else {
			$periode = split("\|",$periode);
			$from = $periode[0];
			$to = $periode[1];
			$where = " AND TGL_PROGRESS BETWEEN '".$from."' AND '".$to."'";
		}
		$query = $this->db->query("SELECT ID_PROGRESS FROM hist_p_progress WHERE COMPANY_CODE = '".$company."' ".$where."");
        $count = $query->num_rows();
        return $count;
	}
	
	function tanam_sisip($periode, $type, $ishist, $afd="", $company) {
		$where = ""; $table = "";
		if($afd != ""){
			$where = " AND fc.FIELDCODE LIKE '".$afd."%'";
		}
			
		if($ishist > 1){
			$table = "vw_tanam_sisip_hist_bi";
		} else {
			$table = "vw_tanam_sisip_bi";
		}
		
		$qry = "";
		
		$qry = "SELECT s.PERIODE, fc.FIELDCODE, fc.ESTATECODE, fc.BLOCKID, fc.YEARREPLANT, 
		COALESCE(s.QTY_TNM_BI,0) AS QTY_TNM_BI, COALESCE(sbi.QTY_TNM_SBI,0) AS QTY_TNM_SBI, 
		COALESCE(s.QTY_SISIP_BI,0) AS QTY_SISIP_BI, COALESCE(sbi.QTY_SISIP_SBI,0) AS QTY_SISIP_SBI, 		
		COALESCE(s.QTY_PMATI_BI,0) AS QTY_PMATI_BI, COALESCE(sbi.QTY_PMATI_SBI,0) AS QTY_PMATI_SBI
	 	FROM m_fieldcrop fc
		LEFT JOIN (
			SELECT PERIODE, LOCATION_CODE, AFD, BLOK, YEARREPLANT, SUM(QTY_TNM_BI) AS QTY_TNM_BI, 
			SUM(QTY_SISIP_BI) AS QTY_SISIP_BI, SUM(QTY_PMATI_BI) AS QTY_PMATI_BI, COMPANY_CODE 
			FROM ".$table."
			WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."'
			GROUP BY LOCATION_CODE
		) s ON s.LOCATION_CODE = fc.FIELDCODE AND s.COMPANY_CODE = fc.COMPANY_CODE
		LEFT JOIN ( 
			SELECT PERIODE, LOCATION_CODE, AFD, BLOK, YEARREPLANT, SUM(QTY_TNM_BI) AS QTY_TNM_SBI, 
			SUM(QTY_SISIP_BI) AS QTY_SISIP_SBI, SUM(QTY_PMATI_BI) AS QTY_PMATI_SBI, COMPANY_CODE 
			FROM ".$table."
			WHERE COMPANY_CODE = '".$company."' 
			AND PERIODE BETWEEN CONCAT(LEFT('".$periode."',4),'01') AND '".$periode."'
			GROUP BY LOCATION_CODE
  		) sbi ON sbi.LOCATION_CODE = fc.FIELDCODE AND sbi.COMPANY_CODE = fc.COMPANY_CODE
		WHERE fc.COMPANY_CODE = '".$company."' ".$where." AND QTY_SISIP_SBI > 0 OR
		fc.COMPANY_CODE = '".$company."' ".$where." AND QTY_TNM_SBI > 0 OR
		fc.COMPANY_CODE = '".$company."' ".$where." AND QTY_PMATI_SBI > 0
		
		ORDER BY fc.FIELDCODE";
		
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;   
	}
	
	function tanam_sisip_harian($periode, $type, $ishist, $afd="", $company) {
		$where = ""; $where2 = ""; $table = "";
		$periode = split("\|",$periode);
		$from = $periode[0];
		$to = $periode[1];
		
		if($afd != ""){
			$where = " AND fc.FIELDCODE LIKE '".$afd."%'";
		}
		
		if($ishist > 1){
			$table = "vw_tanam_sisip_hist_hi";
		} else {
			$table = "vw_tanam_sisip_hi";
		}
		
		$qry = "";
		
		$qry = "SELECT s.TGL_PROGRESS, fc.FIELDCODE, fc.ESTATECODE, fc.BLOCKID, fc.YEARREPLANT, 
			COALESCE(s.QTY_TNM_HI,0) AS QTY_TNM_BI, COALESCE(sbi.QTY_TNM_SHI,0) AS QTY_TNM_SBI, 
			COALESCE(s.QTY_SISIP_HI,0) AS QTY_SISIP_BI, COALESCE(sbi.QTY_SISIP_SHI,0) AS QTY_SISIP_SBI, 		
			COALESCE(s.QTY_PMATI_HI,0) AS QTY_PMATI_BI, COALESCE(sbi.QTY_PMATI_SHI,0) AS QTY_PMATI_SBI
			FROM m_fieldcrop fc
			LEFT JOIN (
				SELECT TGL_PROGRESS, LOCATION_CODE, AFD, BLOK, YEARREPLANT, SUM(QTY_TNM_HI) AS QTY_TNM_HI, 
				SUM(QTY_SISIP_HI) AS QTY_SISIP_HI, SUM(QTY_PMATI_HI) AS QTY_PMATI_HI, COMPANY_CODE 
				FROM ".$table."
				WHERE COMPANY_CODE = '".$company."' AND TGL_PROGRESS = '".$to."'
				GROUP BY LOCATION_CODE
			) s ON s.LOCATION_CODE = fc.FIELDCODE AND s.COMPANY_CODE = fc.COMPANY_CODE
			LEFT JOIN ( 
				SELECT TGL_PROGRESS, LOCATION_CODE, AFD, BLOK, YEARREPLANT, SUM(QTY_TNM_HI) AS QTY_TNM_SHI, 
				SUM(QTY_SISIP_HI) AS QTY_SISIP_SHI, SUM(QTY_PMATI_HI) AS QTY_PMATI_SHI, COMPANY_CODE 
				FROM ".$table."
				WHERE COMPANY_CODE = '".$company."' 
				AND TGL_PROGRESS BETWEEN '".$from."' AND '".$to."'
				GROUP BY LOCATION_CODE
			) sbi ON sbi.LOCATION_CODE = fc.FIELDCODE AND sbi.COMPANY_CODE = fc.COMPANY_CODE
			WHERE fc.COMPANY_CODE = '".$company."' ".$where." AND sbi.QTY_TNM_SHI > 0 OR
			fc.COMPANY_CODE = '".$company."' ".$where." AND sbi.QTY_SISIP_SHI > 0 OR
			fc.COMPANY_CODE = '".$company."' ".$where." AND sbi.QTY_PMATI_SHI > 0
			ORDER BY fc.FIELDCODE";
		
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;   
	}
	
	function get_afdeling($company){
		$query = $this->db->query("SELECT AFD_CODE, AFD_DESC FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
}

?>