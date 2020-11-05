<?php
class model_rpt_monitoring_vh extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function generate_monitoring_vh($company,$from,$to){					
		
		$query="SELECT lhm.LHM_DATE, lhm.LOCATION_TYPE_CODE, lhm.LOCATION_CODE, SUM(lhm.HK_JUMLAH) AS HKE_JUMLAH,
				CASE WHEN ACTIVITY_CODE IN ('9650001','9600001') THEN 
					cekvh.KODE_KENDARAAN
				ELSE 0	
				END AS VH, COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0) ),0) AS LEMBUR_RUPIAH, 
				SUM( COALESCE(lhm.PREMI,0) ) AS PREMI, lhm.COMPANY_CODE
				FROM m_gang_activity_detail lhm
				LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
				LEFT JOIN ( 
						   SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM 
						   m_gang_activity_detail lhm2
						   LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
						   WHERE lhm2.COMPANY_CODE = '". $company ."' 
						   AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '". $from ."' AND '". $to ."' 
						   AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%' 
						   GROUP BY lhm2.EMPLOYEE_CODE
						) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
				LEFT JOIN ( 
						   SELECT KODE_KENDARAAN, TGL_AKTIVITAS FROM p_vehicle_activity 
						   WHERE DATE_FORMAT(TGL_AKTIVITAS, '%Y%m%d') BETWEEN '". $from ."' AND '". $to ."' 
						   AND COMPANY_CODE = '". $company ."' ) cekvh 
						   ON cekvh.KODE_KENDARAAN = lhm.LOCATION_CODE AND cekvh.TGL_AKTIVITAS = lhm.LHM_DATE
				LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
			WHERE lhm.COMPANY_CODE = '". $company ."' 
			AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '". $from ."' AND '". $to ."'
			AND lhm.TYPE_ABSENSI <> ''  AND lhm.LOCATION_TYPE_CODE = 'VH' AND cekvh.KODE_KENDARAAN IS NULL
			GROUP BY lhm.EMPLOYEE_CODE, lhm.LHM_DATE, lhm.GANG_CODE, lhm.LOCATION_CODE, 
					lhm.ACTIVITY_CODE, lhm.TYPE_ABSENSI";
        
		$sQuery=$this->db->query($query);
        $temp_result = array();
                
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
        }    
        return $temp_result;
	}
}