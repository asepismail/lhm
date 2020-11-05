<?
class m_monitoring_progress extends Model{

    function m_monitoring_progress(){
        parent::Model(); 
        $this->load->database();
    }
    
    function create_absensi($company, $periode, $last_period){ 
		$db_gkm = $this->load->database('lhm_gkm', TRUE); 
		$sql_check="SELECT DISTINCT(pr.TGL_PROGRESS), pr.LOCATION_CODE, pr.COMPANY_CODE FROM p_progress pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$periode."'
	ORDER BY pr.TGL_PROGRESS";
		if ($company=='GKM' || $company=='SML'){
			$query_check=$db_gkm->query($sql_check);
		}else{
			$query_check= $this->db->query($sql_check);
		}
		if ($query_check->num_rows()==0){
			$table_periode='hist_p_progress';
		}else{
			$table_periode='p_progress';
		}
		
		$sql_check2="SELECT MAX(pr.TGL_PROGRESS) MAX_TGL_PROGRESS, pr.LOCATION_CODE, pr.COMPANY_CODE FROM p_progress pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$last_period."'
	GROUP BY pr.LOCATION_CODE";
		if ($company=='GKM' || $company=='SML'){
			$query_check2=$db_gkm->query($sql_check2);
		}else{
			$query_check2=$this->db->query($sql_check2);
		}
		
		if ($query_check2->num_rows()==0){
			$table_last='hist_p_progress';
		}else{
			$table_last='p_progress';
		}
	
		$sql=("SELECT p.LOCATION_CODE, p.COMPANY_CODE,
GROUP_CONCAT(DATE_FORMAT(p.TGL_PROGRESS, '%e'),':',1 ORDER BY p.TGL_PROGRESS SEPARATOR ',') AS ABSEN,
max_p.MAX_TGL_PROGRESS, fieldcrop.YEARREPLANT
FROM
(
	SELECT DISTINCT(pr.TGL_PROGRESS), pr.LOCATION_CODE, pr.COMPANY_CODE FROM ".$table_periode." pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$periode."'
		-- AND pr.LOCATION_CODE='OA05911'
	ORDER BY pr.TGL_PROGRESS
) p
LEFT JOIN (
	SELECT MAX(pr.TGL_PROGRESS) MAX_TGL_PROGRESS, pr.LOCATION_CODE, pr.COMPANY_CODE FROM ".$table_last." pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$last_period."'
	GROUP BY pr.LOCATION_CODE
) max_p ON p.LOCATION_CODE = max_p.LOCATION_CODE
LEFT JOIN (
	SELECT f.FIELDCODE, f.YEARREPLANT  
	FROM m_fieldcrop f
	WHERE f.COMPANY_CODE ='".$company."'
	AND f.INACTIVE = 0
) fieldcrop ON p.LOCATION_CODE=fieldcrop.FIELDCODE
GROUP BY p.LOCATION_CODE");
		if ($company=='GKM' || $company=='SML'){
			$query=$db_gkm->query($sql);
		}else{
			$query=$this->db->query($sql);
		}		
		/*
		if($query->num_rows()==0){
			$query = $this->db->query("SELECT p.LOCATION_CODE, p.COMPANY_CODE,
GROUP_CONCAT(DATE_FORMAT(p.TGL_PROGRESS, '%e'),':',1 ORDER BY p.TGL_PROGRESS SEPARATOR ',') AS ABSEN,
max_p.MAX_TGL_PROGRESS, fieldcrop.YEARREPLANT
FROM
(
	SELECT DISTINCT(pr.TGL_PROGRESS), pr.LOCATION_CODE, pr.COMPANY_CODE FROM p_progress pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$periode."'
	ORDER BY pr.TGL_PROGRESS
) p
LEFT JOIN (
	SELECT MAX(pr.TGL_PROGRESS) MAX_TGL_PROGRESS, pr.LOCATION_CODE, pr.COMPANY_CODE FROM hist_p_progress pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND DATE_FORMAT(pr.TGL_PROGRESS,'%Y%m') = '".$last_period."'
	GROUP BY pr.LOCATION_CODE
) max_p ON p.LOCATION_CODE = max_p.LOCATION_CODE
LEFT JOIN (
	SELECT f.FIELDCODE, f.YEARREPLANT  
	FROM m_fieldcrop f
	WHERE f.COMPANY_CODE ='".$company."'
	AND f.INACTIVE = 0
) fieldcrop ON p.LOCATION_CODE=fieldcrop.FIELDCODE
GROUP BY p.LOCATION_CODE");				
		}
		*/
        $temp_result = array();        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;            
        }
        return $temp_result;    
    }
    	
	function check_max_date($company, $tgl, $location){
		$tgl = preg_split('/[- :]/',trim($tgl));
        $tgl = implode('',$tgl);
		$value=$tgl;
		for ($i=0; $i<4; $i++){
			$query = $this->db->query("SELECT pr.TGL_PROGRESS FROM hist_p_progress pr
	WHERE pr.COMPANY_CODE='".$company."'
	AND pr.ACTIVITY_CODE = '8601003'
	AND pr.TGL_PROGRESS= '".$tgl."'
	AND pr.LOCATION_CODE = '".$location."'");
			$tgl = $tgl-1;			
			if($query->num_rows() > 0){
				$row = $query->row();            
				$value = $row->TGL_PROGRESS;  
			}
		}
        return $value;    
    }
    
    function cek_hari($tgl,$company){
        $query = $this->db->query("SELECT * FROM m_calendar WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(CAL_TGL,'%Y%m%e') = '".$tgl."'");
		
        $temp_result = array();        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;            
        }
        return $temp_result;
    }
    
}
?>