<?

class model_rpt_ba extends Model 
{

    function model_rpt_ba()
    {
        parent::Model(); 
		$this->load->library('global_func');
		$this->load->database();
    }
	
	function get_afdeling($company)
	{
		$query = $this->db->query("SELECT AFD_CODE as AFD FROM m_afdeling WHERE company_code = '".$company."' ORDER BY AFD_CODE");
		
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_umr($company, $tahun){
		$query = $this->db->query("SELECT UMR_DAY AS UMR FROM m_umr WHERE UMR_YEAR = '".$tahun."' AND COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	##cara baru ambil umr, baru ba rawat yang pakai 2014-01-09 : ridhu
	function getUMRnew($company, $tahun){
		// var_dump( $company ."-". $tahun );
		$q = $this->db->query("SELECT UMR_DAY AS UMR FROM m_umr WHERE UMR_YEAR = '".$tahun."' AND COMPANY_CODE = '".$company."' GROUP BY UMR_YEAR ", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['UMR'];
		$this->db->close();
		return $temp;
	}

	/* ### BERITA ACARA UNTUK AKTIVITAS RAWAT ### */
	function ba_rawat_afd_baru($afd, $rkp, $bl, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			if ($afd == 'all'){
				$where = '';
			} else {
				$where = 'AND bi.LOCATION_CODE LIKE "'.$afd.'%"';
			}
			
			if($rkp == 'rekap') {
				$group = 'GROUP BY bi.ACTIVITY_CODE';
			} else {
				$group = 'GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN';
			}
			$activity = 'AND bi.ACTIVITY_CODE LIKE "85%"';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','".$where."','".$group."','".$activity."')";
		} else { /* belum closing */
			if ($afd == 'all'){
					$where = '';
					$where2 = ' AND 1 = 1 ';
			} else if ($afd != 'all'){
					$where = 'AND lhm.LOCATION_CODE LIKE "'.$afd.'%"';
					$where2 = 'AND pr.LOCATION_CODE LIKE "'.$afd.'%"';
			}
			
			if($rkp == 'rekap') {
				$group = 'GROUP BY lhm.ACTIVITY_CODE';
				$group2 = 'GROUP BY pr.ACTIVITY_CODE';
				$on = 'ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE';
			} else {
				$group = 'GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN';
				$group2 = 'GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE';
				$on = 'ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE' ;
			}
			//$where3 = 'AND ACTIVITY_CODE LIKE "85%"';
			//$where4 = 'AND pr.ACTIVITY_CODE LIKE "85%"';
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE as ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION, fc.HECTPLANTED AS HECTPLANTED, fc.NUMPLANTATION,
	map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
	lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
	COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
	FROM 
( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN, GP, INACTIVE, COMPANY_CODE FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0) 
				emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '85%' ".$where." ) lhm 
LEFT JOIN m_fieldcrop fc ON fc.FIELDCODE = lhm.LOCATION_CODE AND fc.COMPANY_CODE = '".$company."'
LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
LEFT JOIN ( SELECT LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,
		SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE COMPANY_CODE = '".$company."' AND pr.ACTIVITY_CODE LIKE '85%' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$where2." ".$group2.") prog 
			".$on." ".$group."";
		} /* end belum closing */
		
		$query = $this->db->query($qry);
		$temp = $query->row_array();
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		$this->db->close();
		return $temp_result;  
	}
	
	function ba_rawat_breakdown($location, $activity, $from,$to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){
			$qry = "";
		} else {
			$qry = "SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA, lhm.LHM_DATE,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, m_coa.COA_DESCRIPTION,
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
LEFT JOIN m_coa on m_coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '".$activity."%' AND LOCATION_CODE = '".$location."' ORDER BY LHM_DATE,EMPLOYEE_CODE";
		}
		$query = $this->db->query($qry);
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	/* ### BERITA ACARA UNTUK AKTIVITAS PANEN ### */
	function ba_panen_afd($afd, $rkp, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){
			/* ### BERITA ACARA UNTUK AKTIVITAS PANEN SETELAH CLOSING ### */
			if ($afd == 'all'){
				$where = '';
			} else {
				$where = 'AND bi.LOCATION_CODE LIKE "'.$afd.'%"';
			}
			
			if($rkp == 'rekap') {
				$group = 'GROUP BY bi.ACTIVITY_CODE';
			} else {
				$group = 'GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN';
			}
			
			$activity = 'AND bi.ACTIVITY_CODE LIKE "8601%"';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','".$where."','".$group."','".$activity."')";
			
		} else { 
			/* ### BERITA ACARA UNTUK AKTIVITAS PANEN SEBELUM CLOSING ### */
			if ($afd == 'all'){
				  $where = 'AND 1 = 1';
				  $where2 = 'AND 1 = 1';
			} else if ($afd != 'all'){
				  $where = 'AND lhm.LOCATION_CODE LIKE "'.$afd.'%"';
				  $where2 = 'AND pr.LOCATION_CODE LIKE "'.$afd.'%"';
			}
			
			if($rkp == 'rekap') {
				$group = 'GROUP BY lhm.ACTIVITY_CODE';
				$group2 = 'GROUP BY pr.ACTIVITY_CODE';
				$on = 'ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE';
			} else {
				$group = 'GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN';
				$group2 = 'GROUP BY pr.ACTIVITY_CODE, pr.LOCATION_CODE';
				$on = 'ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE' ;
			}
			
			$where3 = 'AND ACTIVITY_CODE LIKE "8601%"';
			$where4 = 'AND ACTIVITY_CODE LIKE "8601%"';
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION, fc.HECTPLANTED AS HECTPLANTED, fc.NUMPLANTATION,
	map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
	lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
	COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
	FROM 
( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN, GP, INACTIVE, COMPANY_CODE FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0)
		emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '8601%' ".$where.") lhm 
LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
LEFT JOIN m_fieldcrop fc ON fc.FIELDCODE = lhm.LOCATION_CODE AND fc.COMPANY_CODE = '".$company."'
LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
LEFT JOIN ( SELECT LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE ACTIVITY_CODE LIKE '8601%' AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$where2." ".$group2.") prog ".$on." ".$group."";
			
		}
		$query = $this->db->query($qry);
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result; 
	}
	
	//TRANSPORT PANEN
	function ba_tpanen_afd($afd, $rkp, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){
			/* ### BERITA ACARA UNTUK AKTIVITAS TRANSPORT PANEN SETELAH CLOSING ### */
			if ($afd == 'all'){
				$where = "";
			} else {
				$where = "AND bi.LOCATION_CODE LIKE '".$afd."%'";
			}
			
			if($rkp == 'rekap') {
				$group = "GROUP BY bi.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			}
			$activity = 'AND bi.ACTIVITY_CODE LIKE "8602%"';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','".$where."','".$group."','".$activity."')";
		} else {
			if ($afd == 'all'){
				  $where = "AND 1 = 1";
				  $where2 = "AND 1 = 1";
			} else if ($afd != 'all'){
				  $where = "AND lhm.LOCATION_CODE LIKE '".$afd."%'";
				  $where2 = "AND pr.LOCATION_CODE LIKE '".$afd."%'";		  
			}
			
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY pr.ACTIVITY_CODE";
				$on = "ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN";
				$group2 = "GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE";
				$on = "ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
			}
			$where3 = 'AND ACTIVITY_CODE LIKE "8602%"';
			$where4 = 'AND ACTIVITY_CODE LIKE "8602%"';
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION, fc.HECTPLANTED AS HECTPLANTED, fc.NUMPLANTATION,
	map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
	lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
	COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
	FROM 
( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN, GP, INACTIVE, COMPANY_CODE FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0)
		 emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '8602%' ".$where.") lhm 
LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
LEFT JOIN m_fieldcrop fc ON fc.FIELDCODE = lhm.LOCATION_CODE AND fc.COMPANY_CODE = '".$company."'
LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
LEFT JOIN ( SELECT LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE ACTIVITY_CODE LIKE '8602%' AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$where2." ".$group2.") prog ".$on." ".$group."";
		}
		$query = $this->db->query($qry);
		
		$temp = $query->row_array();
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		$this->db->close();	
		return $temp_result; 
	}
	
	//Bibitan
	function ba_bibitan_afd($from, $to, $rkp, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK AKTIVITAS BIBITAN SETELAH CLOSING ### */
			if($rkp == 'rekap') {
				$group = "GROUP BY bi.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			}
			$activity = 'AND bi.ACTIVITY_CODE LIKE "83%"';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','','".$group."','".$activity."')";
		} else {
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY pr.ACTIVITY_CODE";
				$on = "ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN";
				$group2 = "GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE";
				$on = "ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
			}
			$where3 = 'AND ACTIVITY_CODE LIKE "83%"';
			$where4 = 'AND ACTIVITY_CODE LIKE "83%"';
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION,
	map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
	lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
	COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
	FROM 
( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '83%' AND ACTIVITY_CODE NOT LIKE '8301%') lhm 
LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
LEFT JOIN ( SELECT  LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE ACTIVITY_CODE LIKE '83%' AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$group2.") prog ".$on." ".$group."";
		}
		$query = $this->db->query($qry);
		$temp = $query->row_array();
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		$this->db->close();		
		return $temp_result; 
	}
	
	/* ### BERITA ACARA SISIP ### */
	function ba_sisip($afd, $rkp, $bl, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK AKTIVITAS SISIP SETELAH CLOSING ### */
			if ($afd == 'all'){
				$where = "";
			} else {
				$where = "AND bi.LOCATION_CODE LIKE '".$afd."%'";
			}
			
			if($rkp == 'rekap') {
				$group = "GROUP BY bi.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			}
			$activity = 'AND bi.ACTIVITY_CODE LIKE "8402%"';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','".$where."','".$group."','".$activity."')";
		} else {
			/* ### BERITA ACARA BEFORE CLOSING ### */
			if ($afd == 'all'){				
				$where = "";
				$where2 = " AND 1 = 1 ";			
			} else if ($afd != 'all'){
				$where = "AND lhm.LOCATION_CODE LIKE '".$afd."%'";
				$where2 = "AND pr.LOCATION_CODE LIKE '".$afd."%'";
			}
			
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY pr.ACTIVITY_CODE";
				$on = "ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN";
				$group2 = "GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE";
				$on = "ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
			}
			$where3 = 'AND ACTIVITY_CODE LIKE "8402%"';
			$where4 = 'AND ACTIVITY_CODE LIKE "8402%"';
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE as ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION,  fc.HECTPLANTED AS HECTPLANTED, fc.NUMPLANTATION,
	map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
	lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
	COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
	FROM 
( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP, 
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN, GP, INACTIVE, COMPANY_CODE FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0)
		 emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
AND ACTIVITY_CODE LIKE '8402%' ".$where." ) lhm 
LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
LEFT JOIN m_fieldcrop fc ON fc.FIELDCODE = lhm.LOCATION_CODE AND fc.COMPANY_CODE = '".$company."'
LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
LEFT JOIN ( SELECT LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,
		SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE COMPANY_CODE = '".$company."' AND pr.ACTIVITY_CODE LIKE '8402%' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$where2." ".$group2.") prog 
			".$on." ".$group."";
		}
		$query = $this->db->query($qry);
		$temp = $query->row_array();
		$temp_result = array();				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		$this->db->close();		
		return $temp_result; 
	}
	
	//Rawat Infrastruktur
	function ba_rinfrastruktur_afd($from, $to, $rkp, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK RAWAT INFRAS SETELAH CLOSING ### */
			if($rkp == 'rekap') {
				$group = "GROUP BY bi.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			}
			$activity = 'AND LEFT(bi.ACTIVITY_CODE,4) IN ("8112","8122","8132","8142","8152","8162","8170","8190" )';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','','".$group."','".$activity."')";
		} else {
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY ACTIVITY_CODE";
				$on = "ON fisik.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, fisik.SATUAN";
				$group2 = "GROUP BY LOCATION_CODE, ACTIVITY_CODE";
				$on = "ON fisik.LOCATION_CODE = lhm.LOCATION_CODE AND fisik.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
			}
			
				$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION,
			map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
			lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
			COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
			COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
			COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
			COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
			COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
			COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
				+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
				- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
			COALESCE(fisik.HSL,0) AS HASIL_KERJA 
			FROM 
		( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
			emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
			lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
			ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
			CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
			 END AS HKNE_JUMLAH,
			emp.GP, 
			CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
			 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
			 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
			 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
			WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
			 END AS HKE_BYR,
			CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
			 END AS HKNE_BYR,
			lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
		FROM m_gang_activity_detail lhm
		LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
		LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
		LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
				LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
				WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
				AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
				GROUP BY lhm2.EMPLOYEE_CODE
				ORDER BY lhm2.EMPLOYEE_CODE
		) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
		WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
		AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
		AND LEFT(ACTIVITY_CODE,4) IN ('8112','8122','8132','8142','8152','8162','8170','8190' )) lhm 
		LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
		LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
		LEFT JOIN ( 
				SELECT LOCATION_CODE, ACTIVITY_CODE,
				CASE 
					WHEN COALESCE(HSLBK,0) > 0 THEN 
						COALESCE(SUM(HSLBK),0)
					ELSE 
						COALESCE(SUM(HSLLHM),0)
					END
				AS HSL ,
				SATUAN,
				COMPANY_CODE FROM ( 
				SELECT LOCATION_CODE, ACTIVITY_CODE, 
				CASE WHEN FLAG = 'BK' THEN
					COALESCE(HSL,0)
				END AS HSLBK,
				CASE WHEN FLAG = 'LHM' THEN
					COALESCE(HSL,0)
				END AS HSLLHM, SATUAN, FLAG, COMPANY_CODE FROM 
		
				( SELECT LOCATION_CODE, ACTIVITY_CODE, COALESCE(SUM(HASIL_KERJA),0) AS HSL, SATUAN, 'BK' AS FLAG, COMPANY_CODE FROM p_progress_teknik 
				WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
				AND LOCATION_CODE <> '-'
				".$group2."
				UNION 
				SELECT LOCATION_CODE, ACTIVITY_CODE, COALESCE(SUM(HASIL_KERJA),0) AS HSL, SATUAN, 'LHM' AS FLAG, COMPANY_CODE FROM p_progress 
				WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
				AND LOCATION_CODE <> '-'
				".$group2."
				) progress_teknik
				ORDER BY LOCATION_CODE, ACTIVITY_CODE
				) a ".$group2."
			) fisik ".$on." ".$group."";
		}
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 	
	}
	
	//Umum
	function ba_umum_afd($from, $to, $periode,$company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK RAWAT INFRAS SETELAH CLOSING ### */
		
			$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			$activity = 'AND LEFT(bi.ACTIVITY_CODE,2) IN ("61","62")';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','','".$group."','".$activity."')";
		} else {
			$qry = "SELECT m_coa.ACCOUNTCODE, loc.LOCATION_CODE, loc.DESCRIPTION, COA_DESCRIPTION, lhm.LOCATION_CODE,
		COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
		COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
		COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
		COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
		COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
		COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI
	FROM m_coa	
	LEFT JOIN ( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
		emp.TYPE_KARYAWAN,  lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
		lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
		ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
		CASE 
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
		 END AS HKNE_JUMLAH,
		emp.GP,
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
		 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
		WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
		 END AS HKE_BYR,
		
		CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
		 END AS HKNE_BYR,
		lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
	FROM m_gang_activity_detail lhm
	LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
	LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
	LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
			LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
			WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
			AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
			GROUP BY lhm2.EMPLOYEE_CODE
			ORDER BY lhm2.EMPLOYEE_CODE
	) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK	
	WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
	AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
	AND LEFT(ACTIVITY_CODE,2) IN ('61','62','65') AND LOCATION_TYPE_CODE = 'GC' ) lhm ON lhm.ACTIVITY_CODE = m_coa.ACCOUNTCODE
	LEFT JOIN (SELECT loc.LOCATION_CODE, loc.DESCRIPTION FROM m_location loc WHERE COMPANY_CODE = '".$company."' AND LOCATION_TYPE_CODE = 'GC') loc ON loc.LOCATION_CODE = lhm.LOCATION_CODE
	WHERE LEFT(m_coa.ACCOUNTCODE,2) IN ('61','62','65') AND lhm.LOCATION_CODE IS NOT NULL
	GROUP BY ACCOUNTCODE, loc.LOCATION_CODE";
		}
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	//Vehicle, machine, workshop
	function ba_vmw_afd($from, $to, $periode, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK RAWAT INFRAS SETELAH CLOSING ### */
			$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			$activity = 'AND bi.ACTIVITY_CODE IN ("9600001","9650001","9700001","9800001","9600004","9600005","9650004")';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','','".$group."','".$activity."')";
		} else {
			$qry = "SELECT m_coa.ACCOUNTCODE, COA_DESCRIPTION, lhm.LOCATION_CODE, loc.DESCRIPTION,
	COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
	COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
	COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
	COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
	COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI
FROM m_coa	
LEFT JOIN ( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
	emp.TYPE_KARYAWAN,  lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
	ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
	CASE 
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	emp.GP,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKE_BYR,
	
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE and emp.INACTIVE = 0
LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' AND emp2.INACTIVE = 0
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
		GROUP BY lhm2.EMPLOYEE_CODE
		ORDER BY lhm2.EMPLOYEE_CODE
) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK	
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
AND lhm.TYPE_ABSENSI <> ''
AND ACTIVITY_CODE IN ('9600001','9650001','9700001','9800001','9600004','9600005','9650004') ) lhm ON lhm.ACTIVITY_CODE = m_coa.ACCOUNTCODE
LEFT JOIN ( select LOCATION_CODE, DESCRIPTION FROM m_location where company_code = '".$company."' ) loc
		ON loc.LOCATION_CODE = lhm.LOCATION_CODE
WHERE m_coa.ACCOUNTCODE IN ('9600001','9650001','9700001','9800001','9600004','9600005','9650004')
GROUP BY ACCOUNTCODE, lhm.LOCATION_CODE";
		}
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	//Vehicle, machine, workshop
	function ba_pks_afd($periode, $company) {
		
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK RAWAT INFRAS SETELAH CLOSING ### */
			$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			$activity = 'AND bi.ACTIVITY_CODE IN ("5112500","5112650")';
			$qry = "CALL sp_select_ba_closing('".$company."','".$periode."','','".$group."','".$activity."')";
		} else {
			$qry = "SELECT m_coa.ACCOUNTCODE, COA_DESCRIPTION, lhm.LOCATION_CODE, loc.DESCRIPTION,
					COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
					COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
					COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
					COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
					COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
					COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI
				FROM m_coa	
				LEFT JOIN ( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
					emp.TYPE_KARYAWAN,  lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
					lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
					ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
					CASE 
					 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
					 END AS HKNE_JUMLAH,
					emp.GP,
					CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
					 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
					 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
					 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
					WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
					 END AS HKE_BYR,
					
					CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
					 END AS HKNE_BYR,
					lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
				FROM m_gang_activity_detail lhm
				LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
				LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".$periode."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
				LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
						LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
						WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m') = '".$periode."'
						AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
						GROUP BY lhm2.EMPLOYEE_CODE
						ORDER BY lhm2.EMPLOYEE_CODE
				) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK	
				WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m') = '".$periode."'
				AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
				AND ACTIVITY_CODE IN ('5112500','5112650') ) lhm ON lhm.ACTIVITY_CODE = m_coa.ACCOUNTCODE
				LEFT JOIN ( select LOCATION_CODE, DESCRIPTION FROM m_location where company_code = '".$company."' ) loc
						ON loc.LOCATION_CODE = lhm.LOCATION_CODE
				WHERE m_coa.ACCOUNTCODE IN ('5112500','5112650')
				GROUP BY ACCOUNTCODE, lhm.LOCATION_CODE";
		}
		$query = $this->db->query($qry);
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;   
	}
	
	//PJ bibitan
	function ba_pjbibitan_afd($from, $to, $rkp, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){	
			/* ### BERITA ACARA UNTUK BIBITAN SETELAH CLOSING ### */
			$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			$activity = "AND bi.ACTIVITY_CODE LIKE '8301%'";
			$qry = "SELECT bi.RPT_BA_ID, bi.RPT_BA_TYPE,  pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION, bi.DOCUMENTNO, bi.PERIODE, bi.UMR, bi.AFD, bi.LOCATION_CODE, bi.LOCATION_DESC AS DESCRIPTION, HK_BI AS HK, map.PARENT, bi.LUAS_BLOK AS HECTPLANTED, bi.JUMLAH_POKOK, bi.TAHUN_TANAM AS NUMPLANTATION,
       bi.ACTIVITY_CODE AS ACCOUNTCODE, bi.ACTIVITY_DESC AS COA_DESCRIPTION, HK_BI AS HK_JUMLAH,
       bi.SATUAN AS UNIT1, SUM(HSL_KERJA_BI) AS HASIL_KERJA, sbi.HSL_KERJA_SBI, 
       SUM(REAL_BIAYA_BI) AS REAL_BIAYA_BI, sbi.REAL_BIAYA_SBI, SUM(RP_SAT_BI), sbi.RP_SAT_SBI, 
       SUM(HK_SAT_BI), sbi.HK_SAT_SBI, bi.COMPANY_CODE FROM rpt_ba_detail bi
LEFT JOIN ( 
		SELECT RPT_BA_ID, RPT_BA_TYPE, DOCUMENTNO, PERIODE, UMR, AFD, 
        ACTIVITY_CODE, ACTIVITY_DESC, LOCATION_CODE,
        SATUAN, SUM(HSL_KERJA_BI) AS HSL_KERJA_SBI, SUM(REAL_BIAYA_BI) AS REAL_BIAYA_SBI, 
        SUM(RP_SAT_BI) AS RP_SAT_SBI, SUM(HK_SAT_BI) AS HK_SAT_SBI, COMPANY_CODE FROM rpt_ba_detail 
	WHERE COMPANY_CODE = '".$company."' AND PERIODE BETWEEN CONCAT(LEFT('".$periode."',4),'01') AND '".$periode."'
	GROUP BY AFD, LOCATION_CODE, ACTIVITY_CODE, SATUAN
	) sbi ON sbi.ACTIVITY_CODE = bi.ACTIVITY_CODE AND sbi.LOCATION_CODE = bi.LOCATION_CODE AND sbi.COMPANY_CODE = bi.COMPANY_CODE
LEFT JOIN m_progress_map map ON map.ACCOUNTCODE = bi.ACTIVITY_CODE
LEFT JOIN m_project pj ON (pj.PROJECT_ID = bi.LOCATION_CODE)
WHERE bi.COMPANY_CODE = '".$company."' AND bi.PERIODE = '".$periode."' ".$activity."
".$group."";
		} else {
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY pr.ACTIVITY_CODE";
				$on = "ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN";
				$group2 = "GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE";
				$on = "ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
			}
					
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION,
				map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
				lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
				COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
				COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
				COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
				COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
				COALESCE( prog.HASIL_KERJA,0) AS HASIL_KERJA 
				FROM 
			( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
				emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
				lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
				ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
				 END AS HKNE_JUMLAH,
				emp.GP, 
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
				WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKE_BYR,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKNE_BYR,
				lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
			FROM m_gang_activity_detail lhm
			LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
			LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
			LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
					LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
					WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
					AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
					GROUP BY lhm2.EMPLOYEE_CODE
					ORDER BY lhm2.EMPLOYEE_CODE
			) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
			WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
			AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
			AND ACTIVITY_CODE LIKE '8301%') lhm 
			LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
			LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
			LEFT JOIN m_project pj ON (pj.PROJECT_ID = lhm.LOCATION_CODE)
			LEFT JOIN ( SELECT LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,SUM(pr.HASIL_KERJA) AS HASIL_KERJA FROM p_progress pr WHERE  ACTIVITY_CODE LIKE '8301%' AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' GROUP BY pr.ACTIVITY_CODE) prog 
			ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE 
			GROUP BY lhm.ACTIVITY_CODE";
		}
		
		$query = $this->db->query($qry);
		$temp = $query->row_array();
		$temp_result = array();				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		$this->db->close();		
		return $temp_result; 
	}
	
	//PJ Tanam
	function ba_pjtanam_afd($afd, $rkp, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){
			/* ### BERITA ACARA UNTUK PROJECT TANAM SETELAH CLOSING ### */
			if ($afd == 'all'){
				$where = "WHERE 1 = 1";
				$where2 = "AND 1 = 1";
			} else if ($afd != 'all'){ 
				$where = "WHERE pj.AFD = '".$afd."'";
				$where2 = "AND m_project.AFD = '".$afd."'";		  
			}
			$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			$activity = "AND bi.ACTIVITY_CODE LIKE '8301%'";
			$qry = "SELECT bi.RPT_BA_ID, bi.RPT_BA_TYPE,  pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION, bi.DOCUMENTNO, bi.PERIODE, bi.UMR, bi.AFD, bi.LOCATION_CODE, bi.LOCATION_DESC AS DESCRIPTION, HK_BI AS HK, map.PARENT, bi.LUAS_BLOK AS HECTPLANTED, bi.JUMLAH_POKOK, bi.TAHUN_TANAM AS NUMPLANTATION,
       bi.ACTIVITY_CODE AS ACCOUNTCODE, bi.ACTIVITY_DESC AS COA_DESCRIPTION, HK_BI AS HK_JUMLAH,
       bi.SATUAN AS UNIT1, SUM(HSL_KERJA_BI) AS HASIL_KERJA, sbi.HSL_KERJA_SBI, 
       SUM(REAL_BIAYA_BI) AS REAL_BIAYA_BI, sbi.REAL_BIAYA_SBI, SUM(RP_SAT_BI), sbi.RP_SAT_SBI, 
       SUM(HK_SAT_BI), sbi.HK_SAT_SBI, bi.COMPANY_CODE FROM rpt_ba_detail bi
LEFT JOIN ( 
		SELECT RPT_BA_ID, RPT_BA_TYPE, DOCUMENTNO, PERIODE, UMR, AFD, 
        ACTIVITY_CODE, ACTIVITY_DESC, LOCATION_CODE,
        SATUAN, SUM(HSL_KERJA_BI) AS HSL_KERJA_SBI, SUM(REAL_BIAYA_BI) AS REAL_BIAYA_SBI, 
        SUM(RP_SAT_BI) AS RP_SAT_SBI, SUM(HK_SAT_BI) AS HK_SAT_SBI, COMPANY_CODE FROM rpt_ba_detail 
	WHERE COMPANY_CODE = '".$company."' AND PERIODE BETWEEN CONCAT(LEFT('".$periode."',4),'01') AND '".$periode."'
	GROUP BY AFD, LOCATION_CODE, ACTIVITY_CODE, SATUAN
	) sbi ON sbi.ACTIVITY_CODE = bi.ACTIVITY_CODE AND sbi.LOCATION_CODE = bi.LOCATION_CODE AND sbi.COMPANY_CODE = bi.COMPANY_CODE
LEFT JOIN m_progress_map map ON map.ACCOUNTCODE = bi.ACTIVITY_CODE
LEFT JOIN m_project pj ON (pj.PROJECT_ID = bi.LOCATION_CODE)
WHERE bi.COMPANY_CODE = '".$company."' AND bi.PERIODE = '".$periode."' ".$activity."
".$group."";
		} else {
				if ($afd == 'all'){
					$where = "WHERE 1 = 1";
					$where2 = "AND 1 = 1";
				} else if ($afd != 'all'){ 
					$where = "WHERE pj.AFD = '".$afd."'";
					$where2 = "AND m_project.AFD = '".$afd."'";		  
				}
					
					if($rkp == 'rekap') {
						$group = "GROUP BY lhm.ACTIVITY_CODE";
						$group2 = "GROUP BY pr.ACTIVITY_CODE";
						$on = "ON prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE";
					} else {
						$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, prog.SATUAN";
						$group2 = "GROUP BY pr.LOCATION_CODE, pr.ACTIVITY_CODE";
						$on = "ON prog.LOCATION_CODE = lhm.LOCATION_CODE AND prog.ACTIVITY_CODE = lhm.ACTIVITY_CODE" ;
					}
					
					$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION,
					lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION,
				map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
				lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
				COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
				COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
				COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
				COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
				COALESCE(prog.HASIL_KERJA,0) AS HASIL_KERJA 
				FROM 
			( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA,
				emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
				lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, 
				ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
				 END AS HKNE_JUMLAH,
				emp.GP, 
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
				WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKE_BYR,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKNE_BYR,
				lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
			FROM m_gang_activity_detail lhm
			LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
			LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
			LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
					LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
					WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d')  BETWEEN '".$from."' AND '".$to."' 
					AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
					GROUP BY lhm2.EMPLOYEE_CODE
					ORDER BY lhm2.EMPLOYEE_CODE
			) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
			WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
			AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
			AND LEFT(ACTIVITY_CODE,4) IN ('8201','8202','8203','8204','8205','8206','8207','8209','8401','8208','8402')) lhm 
			LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
			LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
			LEFT JOIN m_project pj ON ( pj.PROJECT_ID = lhm.LOCATION_CODE )
			LEFT JOIN ( SELECT  LEFT(pr.LOCATION_CODE,2) AS  AFD, pr.LOCATION_CODE,pr.ACTIVITY_CODE,pr.SATUAN,SUM(pr.HASIL_KERJA) AS HASIL_KERJA 
			FROM p_progress pr
			LEFT JOIN m_project ON pr.LOCATION_CODE = m_project.PROJECT_ID 
			WHERE LEFT(ACTIVITY_CODE,4) IN ('8201','8202','8203','8204','8205','8206','8207','8209','8401','8208','8402') 
			AND pr.COMPANY_CODE = '".$company."' ".$where2." AND DATE_FORMAT(pr.TGL_PROGRESS, '%Y%m%d')  
			BETWEEN '".$from."' AND '".$to."' ".$group2.") prog ".$on." ".$where." ".$group."";
		}
		
		$query = $this->db->query($qry);
		$temp = $query->row_array();
		$temp_result = array();				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		$this->db->close();		
		return $temp_result; 
	}
	
	//pj infrastruktur
	function ba_pjinfras_afd($rkp, $from, $to, $company) {
		$periode = substr(str_replace("-","",$to),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		$qry = "";
		if($close == '1'){
			/* ### BERITA ACARA UNTUK PROJECT INFRAS SETELAH CLOSING ### */
			if($rkp == 'rekap') {
				$group = "GROUP BY bi.ACTIVITY_CODE";
			} else {
				$group = "GROUP BY bi.LOCATION_CODE, bi.ACTIVITY_CODE, bi.SATUAN";
			}
			$activity = "AND bi.ACTIVITY_CODE IN (SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'IF')";
			$qry = "SELECT bi.RPT_BA_ID, bi.RPT_BA_TYPE,  pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION, bi.DOCUMENTNO, bi.PERIODE, bi.UMR, bi.AFD, bi.LOCATION_CODE, bi.LOCATION_DESC AS DESCRIPTION, HK_BI AS HK, map.PARENT, bi.LUAS_BLOK AS HECTPLANTED, bi.JUMLAH_POKOK, bi.TAHUN_TANAM AS NUMPLANTATION,
       bi.ACTIVITY_CODE AS ACCOUNTCODE, bi.ACTIVITY_DESC AS COA_DESCRIPTION, HK_BI AS HK_JUMLAH,
       bi.SATUAN AS UNIT1, SUM(HSL_KERJA_BI) AS HASIL_KERJA, sbi.HSL_KERJA_SBI, 
       SUM(REAL_BIAYA_BI) AS REAL_BIAYA_BI, sbi.REAL_BIAYA_SBI, SUM(RP_SAT_BI), sbi.RP_SAT_SBI, 
       SUM(HK_SAT_BI), sbi.HK_SAT_SBI, bi.COMPANY_CODE FROM rpt_ba_detail bi
LEFT JOIN ( 
		SELECT RPT_BA_ID, RPT_BA_TYPE, DOCUMENTNO, PERIODE, UMR, AFD, 
        ACTIVITY_CODE, ACTIVITY_DESC, LOCATION_CODE,
        SATUAN, SUM(HSL_KERJA_BI) AS HSL_KERJA_SBI, SUM(REAL_BIAYA_BI) AS REAL_BIAYA_SBI, 
        SUM(RP_SAT_BI) AS RP_SAT_SBI, SUM(HK_SAT_BI) AS HK_SAT_SBI, COMPANY_CODE FROM rpt_ba_detail 
	WHERE COMPANY_CODE = '".$company."' AND PERIODE BETWEEN CONCAT(LEFT('".$periode."',4),'01') AND '".$periode."'
	GROUP BY AFD, LOCATION_CODE, ACTIVITY_CODE, SATUAN
	) sbi ON sbi.ACTIVITY_CODE = bi.ACTIVITY_CODE AND sbi.LOCATION_CODE = bi.LOCATION_CODE AND sbi.COMPANY_CODE = bi.COMPANY_CODE
LEFT JOIN m_progress_map map ON map.ACCOUNTCODE = bi.ACTIVITY_CODE
LEFT JOIN m_project pj ON (pj.PROJECT_ID = bi.LOCATION_CODE)
WHERE bi.COMPANY_CODE = '".$company."' AND bi.PERIODE = '".$periode."' ".$activity."
".$group."";
		} else {	
			if($rkp == 'rekap') {
				$group = "GROUP BY lhm.ACTIVITY_CODE";
				$group2 = "GROUP BY ACTIVITY_CODE";
				$on = "ON fisik.ACTIVITY_CODE = lhm.activity_code";
			} else {
				$group = "GROUP BY lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, fisik.SATUAN";
				$group2 = "GROUP BY LOCATION_CODE, ACTIVITY_CODE";
				$on = "ON fisik.LOCATION_CODE = lhm.location_code AND fisik.ACTIVITY_CODE = lhm.activity_code" ;
			}
					
			$qry = "SELECT lhm.LOCATION_CODE AS LOCATION_CODE, lhm.ACTIVITY_CODE AS ACCOUNTCODE, coa.COA_DESCRIPTION AS COA_DESCRIPTION, pj.AFD AS AFD_PJ, pj.PROJECT_LOCATION AS PJ_LOCATION,
				map.UNIT1 AS UNIT1,map.UNIT2 AS UNIT2, 
				lhm.GP/25 AS UMR, map.PARENT AS PARENT, 
				COALESCE(SUM(COALESCE(lhm.HK_JUMLAH,0)),0) AS HK, 
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0) AS HKE_BYR,
				COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) AS PREMI,
				COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0) AS LEMBUR,
				COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS PENALTI,
				COALESCE( SUM( COALESCE(lhm.HKE_BYR,0)),0)
			+ COALESCE( SUM( COALESCE(lhm.PREMI,0)),0) + COALESCE( SUM( COALESCE(lhm.LEMBUR_RUPIAH,0)),0)
			- COALESCE( SUM( COALESCE(lhm.PENALTI,0)),0) AS REAL_BIAYA_BI,
				COALESCE(fisik.HSL,0) AS HASIL_KERJA 
				FROM 
			( SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA, 
				emp.TYPE_KARYAWAN, lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
				lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
				ROUND(lhm.HK_JUMLAH) AS HK_JUMLAH,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
				 END AS HKNE_JUMLAH,
				emp.GP, 
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  (emp.GP/ 25)  * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  (emp.GP/cnt_hk.HK) * lhm.HK_JUMLAH
				 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ cnt_hk.HK)  * 1
				WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKE_BYR,
				CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
				 END AS HKNE_BYR,
				lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI 
			FROM m_gang_activity_detail lhm
			LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
			LEFT JOIN ( SELECT * FROM m_gad_tambahan WHERE PERIODE = '".substr($from,0,6)."' AND COMPANY_CODE = '".$company."' ) gadt ON gadt.NIK = lhm.EMPLOYEE_CODE
			LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
					LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
					WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN ".$from." AND ".$to."
					AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%'
					GROUP BY lhm2.EMPLOYEE_CODE
					ORDER BY lhm2.EMPLOYEE_CODE
			) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
			WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN ".$from." AND ".$to."
			AND lhm.TYPE_ABSENSI <> '' AND emp.INACTIVE = 0
			AND lhm.ACTIVITY_CODE IN (SELECT ACCOUNTCODE FROM m_progress_map WHERE PENGGUNAAN = 'IF') ) lhm 
			LEFT JOIN ( SELECT pj.PROJECT_ID, pj.AFD, pj.PROJECT_TYPE, pj.PROJECT_SUBTYPE, pj.PROJECT_DESC, pj.PROJECT_LOCATION,det.PROJECT_ACTIVITY FROM m_project pj
			LEFT JOIN m_project_detail det ON det.MASTER_PROJECT_ID = pj.PROJECT_ID AND det.COMPANY_CODE = pj.COMPANY_CODE
			WHERE pj.COMPANY_CODE = '".$company."' AND pj.PROJECT_TYPE = 'IF' ) pj ON pj.PROJECT_ID = lhm.LOCATION_CODE
			LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
			LEFT JOIN m_progress_map map ON coa.ACCOUNTCODE = map.ACCOUNTCODE
			LEFT JOIN ( 
					SELECT LOCATION_CODE, ACTIVITY_CODE,
					CASE 
						WHEN COALESCE(HSLBK,0) > 0 THEN 
							COALESCE(SUM(HSLBK),0)
						ELSE 
							COALESCE(SUM(HSLLHM),0)
						END
					AS HSL ,
					SATUAN,
					COMPANY_CODE FROM ( 
					SELECT LOCATION_CODE, ACTIVITY_CODE, 
					CASE WHEN FLAG = 'BK' THEN
						COALESCE(HSL,0)
					END AS HSLBK,
					CASE WHEN FLAG = 'LHM' THEN
						COALESCE(HSL,0)
					END AS HSLLHM, SATUAN, FLAG, COMPANY_CODE FROM 
			
					( SELECT LOCATION_CODE, ACTIVITY_CODE, COALESCE(SUM(HASIL_KERJA),0) AS HSL, SATUAN, 'BK' AS FLAG, COMPANY_CODE FROM p_progress_teknik 
					WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
					AND LOCATION_CODE <> '-'
					".$group2."
					UNION 
					SELECT LOCATION_CODE, ACTIVITY_CODE, COALESCE(SUM(HASIL_KERJA),0) AS HSL, SATUAN, 'LHM' AS FLAG, COMPANY_CODE FROM p_progress 
					WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN '".$from."' AND '".$to."'
					AND LOCATION_CODE <> '-'
					".$group2."
					) progress_teknik
					ORDER BY LOCATION_CODE, ACTIVITY_CODE
					) a ".$group2."
				) fisik ".$on." ".$group."";
		}
		$query = $this->db->query($qry);
		$temp_result = array();	
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result; 
	}
	
	//pj infrastruktur
	function ba_tunpot($from, $to, $periode, $company) {
		$from = $from;
		$to = $to;
		$query = $this->db->query("CALL sp_ba_tunpot('".$company."','".$from."','".$to."')");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
	
	/* untuk ba kontraktor */
	
	function ba_kontraktor($kontraktor, $from, $to, $tmpAccode, $total, $company) {
		$group = "";
		$where = "";
		if($total!= "total"){
			$group = "GROUP BY ID_KONTRAKTOR, ACTIVITY_CODE, LOCATION_CODE, MUATAN, HSL_SATUAN";
		} else {
			$group = "GROUP BY ID_KONTRAKTOR";
		}
		
		if($tmpAccode!= ""){
			$where = "AND ACTIVITY_CODE = '".$tmpAccode."'";
		} else {
			$where = "AND 1=1";
		}
		
		$query = $this->db->query("SELECT ID_KONTRAKTOR, m_kontraktor.NAMA_KONTRAKTOR, p_kontraktor.LOCATION_TYPE_CODE,
	p_kontraktor.LOCATION_CODE,
	CASE WHEN p_kontraktor.LOCATION_TYPE_CODE = 'PJ' THEN 
		CONCAT(m_project.PROJECT_DESC,' (',m_project.PROJECT_LOCATION, ')')
	ELSE 
		lokasi.DESCRIPTION	
	END AS DESKRIPSI_LOKASI
	,ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS DESKRIPSI,
	MUATAN,
	HSL_SATUAN,SUM(COALESCE(HSL_VOLUME,0)) AS VOL,TARIF_SATUAN, SUM(COALESCE(NILAI,0)) AS NILAI,
	CASE WHEN m_kontraktor.NPWP IS NULL OR m_kontraktor.NPWP = '' THEN 
		ROUND(SUM(COALESCE(NILAI,0)) * 4/100,2)
	ELSE 	
		ROUND(SUM(COALESCE(NILAI,0)) * 2/100,2)
	END AS PPH23,
	CASE WHEN m_kontraktor.NPWP IS NULL OR m_kontraktor.NPWP = '' THEN 
		ROUND(SUM(COALESCE(NILAI,0)) - SUM(COALESCE(NILAI,0)) * 4/100,2)
	ELSE 	
		ROUND(SUM(COALESCE(NILAI,0)) - SUM(COALESCE(NILAI,0)) * 2/100,2)
	END AS BERSIH_TERIMA	
	FROM p_kontraktor
LEFT JOIN m_kontraktor ON p_kontraktor.ID_KONTRAKTOR = m_kontraktor.KODE_KONTRAKTOR
LEFT JOIN ( SELECT LOCATION_CODE, LOCATION_TYPE_CODE, DESCRIPTION FROM m_location WHERE COMPANY_CODE = '".$company."')
	lokasi ON lokasi.LOCATION_CODE = p_kontraktor.LOCATION_CODE AND lokasi.LOCATION_TYPE_CODE = p_kontraktor.LOCATION_TYPE_CODE
LEFT JOIN m_project ON m_project.PROJECT_ID = p_kontraktor.LOCATION_CODE
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_kontraktor.ACTIVITY_CODE
WHERE ID_KONTRAKTOR = '".$kontraktor."' ".$where." AND DATE_FORMAT(TGL_KONTRAK,'%Y%m%d') BETWEEN '".$from."' AND '".$to."' ".$group."
");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result; 
	}
	
}

?>