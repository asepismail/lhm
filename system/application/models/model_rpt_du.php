<?

class model_rpt_du extends Model 
{

    function model_rpt_du()
    {
        parent::Model(); 
        $this->load->database();
		$this->load->library('global_func');
		$this->load->library('session');
    }
    
    function header_du($gc, $company){
        $sql = "SELECT g.GANG_CODE, g.DESCRIPTION, g.MANDORE_CODE, m_employee.NAMA FROM m_gang g
LEFT JOIN m_employee ON (m_employee.NIK = g.MANDORE_CODE AND m_employee.COMPANY_CODE = g.COMPANY_CODE)
WHERE g.GANG_CODE = '".$gc."' AND g.COMPANY_CODE = '".$company."'";
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
    
    function get_gc($company)
    {
        $query = $this->db->query("SELECT GANG_CODE FROM m_gang WHERE COMPANY_CODE = '".$company."' order by GANG_CODE ASC");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;  
    }
    
    function gen_tanda_terima ($periode, $gc, $company){
        $sql = "SELECT m.NIK_KARYAWAN, 
    m.NAMA_KARYAWAN, 
    FORMAT(((m.HKE_BYR + HKNE_BYR + m.TUNJANGAN_JABATAN + m.PREMI + m.NATURA + m.RTB + m.ASTEK ) - (m.POT_ASTEK + m.POTONGAN_NATURA + m.POTONGAN_LAIN + m.PPH_21 )),2) AS UPAH_DITERIMA        
    FROM rpt_main m WHERE GANG_CODE = '".$gc."' AND COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."'";
    $query = $this->db->query($sql);
        
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function get_division ($company){
        $sql = "SELECT UPPER(DIVISION_CODE) AS DIVISION_CODE 
                FROM m_employee WHERE COMPANY_CODE = '".$company."' GROUP BY DIVISION_CODE";
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
    
    function get_du_perafd($company, $from, $to, $div){
        if ($div != 'ALL') {
            $sql = "CALL sp_select_du_detail('".$company."', '".$from."', '".$to."', '".$div."' )";
        } else {
            $sql = "CALL sp_select_du_detail_all('".$company."', '".$from."', '".$to."' )";
        }
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
    
    function generate_du2_breakdown($nik,$gc,$periode)
    {
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8'); 
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $periode =htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($this->session->userdata('DCOMPANY')),ENT_QUOTES,'UTF-8');
        
		$month = substr($periode,4,2);//date('m');
		$year = substr($periode,0,4);//date('Y');
	    $result = strtotime("{$year}-{$month}-01");
   		$result = strtotime('-1 second', strtotime('+1 month', $result));
   		$result =date('d', $result);
		
        $from = $periode."01";
        $to = $periode.$result;
		
        $query="SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA, lhm.LHM_DATE, emp.DIVISION_CODE, emp.FAMILY_STATUS,
	emp.TYPE_KARYAWAN,  
	lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
	lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS AKTIVITAS,
	emp.GP, 
	lhm.HK_JUMLAH AS HKE_JUMLAH,
	CASE WHEN ACTIVITY_CODE IN ('9650001','9600001') THEN 
		cekvh.KODE_KENDARAAN
	ELSE 0	
	END AS VH,
	CASE WHEN ACTIVITY_CODE IN ('9650001','9600001') THEN 
		CASE WHEN cekvh.KODE_KENDARAAN IS NULL THEN
			-- kalo kosng pengalinya 0 --			
			CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP')  AND TYPE_ABSENSI IN('KJ','KJI') THEN  0
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  0
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  ROUND( (emp.GP/cnt_hk.HK) ) * lhm.HK_JUMLAH
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ cnt_hk.HK) )  * 1
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ 25))  * 1		
			END				
			-- end kalo kosng pengalinya 0 --	
			ELSE 
			-- normal --	
			CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  ROUND( (emp.GP/cnt_hk.HK) ) * lhm.HK_JUMLAH
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ cnt_hk.HK) )  * 1
			  WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ 25))  * 1
			END				
			-- end normal -- 
	   END
	END AS VHBYR, 
	CASE 
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
	 END AS HKNE_JUMLAH,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' THEN  ROUND( (emp.GP/cnt_hk.HK) ) * lhm.HK_JUMLAH
	 WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ cnt_hk.HK) )  * 1
	WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ 25))  * 1
	 END AS HKE_BYR,
	CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
	 END AS HKNE_BYR,
	lhm.LEMBUR_JAM, 
	CASE WHEN ACTIVITY_CODE IN ('9650001','9600001') THEN 
		 CASE WHEN cekvh.KODE_KENDARAAN IS NULL THEN
				0
			ELSE
				COALESCE(lhm.LEMBUR_RUPIAH,0)
			END
	ELSE COALESCE(lhm.LEMBUR_RUPIAH,0)
	END AS LEMBUR_RUPIAH, 
	CASE WHEN ACTIVITY_CODE IN ('9650001','9600001') THEN 
		 CASE WHEN cekvh.KODE_KENDARAAN IS NULL THEN
			0
			ELSE
			lhm.PREMI
		 END
	ELSE
		lhm.PREMI
	END AS PREMI, 
	lhm.PENALTI, 
	lhm.COMPANY_CODE, 
	DATE_FORMAT(lhm.INPUT_DATE,'%d-%m-%Y') as INPUT_DATE,
	lhm.INPUT_BY,
	emp.GANG_CODE_CURRENT 
FROM m_gang_activity_detail lhm
LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) AS HK FROM m_gang_activity_detail lhm2
		LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
		WHERE lhm2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."' 
		AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%' 
		GROUP BY lhm2.EMPLOYEE_CODE
	) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
LEFT JOIN ( SELECT KODE_KENDARAAN, TGL_AKTIVITAS FROM p_vehicle_activity WHERE TGL_AKTIVITAS BETWEEN '".$from."' AND '".$to."' 
		AND COMPANY_CODE = '".$company."' ) cekvh ON cekvh.KODE_KENDARAAN = lhm.LOCATION_CODE AND cekvh.TGL_AKTIVITAS = lhm.LHM_DATE
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = lhm.ACTIVITY_CODE
WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND '".$to."'
AND lhm.TYPE_ABSENSI <> ''  AND lhm.EMPLOYEE_CODE ='".$nik."'
GROUP BY lhm.EMPLOYEE_CODE, lhm.LHM_DATE, lhm.GANG_CODE, lhm.LOCATION_CODE, lhm.ACTIVITY_CODE, lhm.TYPE_ABSENSI";
            $sQuery=$this->db->query($query);
            $temp_result = array();
                
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }    
            return $temp_result;
    }
    
    function generate_du2($gc, $from, $to, $company){
        $periode = substr($from, 0,6);
		$cek_closing = $this->global_func->cekClosing($periode, $company);
		$user = $this->session->userdata('USER_LEVEL');
		$wheregc = "";
        if ($cek_closing > 0) {
			 if (strtoupper($gc) == 'ALL') {
				$wheregc = "AND 1 = 1"; 
			 } else {
			 	$wheregc = "AND rpt_du.GANG_CODE = '".$gc."'";	 	
			 }
			
			if($company == "SSS" || $company == "ASL"){
				if($user == "SAS" || $user == "SAD"){
					$wheregc .= " ";
				} else {
					$wheregc .= " AND TYPE_KARYAWAN <> 'BULANAN'";
				}
			} /* else if ( $company == "ASL"){
				if($user == "SAD"){
					$wheregc .= " ";
				} else {
					$wheregc .= " AND TYPE_KARYAWAN NOT IN ('BULANAN','SKU')";
				}
			} */
			
            $sql=" SELECT GANG_CODE,EMPLOYEE_CODE,NAMA,STATUS AS FAMILY_STATUS,TYPE_KARYAWAN,DIVISION_CODE,GP,
			HK,HKNE,TTL_HK AS TTL, HKE_BYR,HKNE_BYR,TTL_BYR, PREMI_LEMBUR, NATURA, ASTEK, BPJS_KES, POT_ASTEK, POT_BPJS_KES AS POTONGAN_BPJS_KES, POT_PPH21, 
			TUNJANGAN AS TUNJANGAN_JABATAN, 0 AS SUBSIDI_KENDARAN, 0 AS TUNJAB, RAPEL_THR_BONUS AS RAPEL, 0 AS THR, 
			0 AS BONUS, TUNJ_LEBIH_HARI, BRUTO_GAJI, POT_LAIN AS POTONGAN_LAIN, POT_KRG_HARI, POT_PPH21 AS PPH_21, 
			TTL_POTONGAN, UPAH_TERIMA, COMPANY_CODE 
			FROM rpt_du WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."' ". $wheregc ."";   
        } else {
            if (strtoupper($gc) != 'ALL') {
				if($company == "SSS" || $company == "ASL"){
					if($user == "SAS" || $user == "SAD"){
						$sql = "CALL sp_select_du_kemandoran('".$company."', '".$from."', '".$to."', '".$gc."' )";
					} else {
						$sql = "CALL sp_select_du_kemandoranfilt('".$company."', '".$from."', '".$to."', '".$gc."' )";
					}
				} else {
                	$sql = "CALL sp_select_du_kemandoran('".$company."', '".$from."', '".$to."', '".$gc."' )";
				}
			} else {
				if($company == "SSS" || $company == "ASL"){
					if($user == "SAS" || $user == "SAD"){
						$sql = "CALL sp_select_du_kemandoran_all('".$company."', '".$from."', '".$to."' )";
					} else {
						$sql = "CALL sp_select_du_kemandoranfilt_all('".$company."', '".$from."', '".$to."' )";
					}
				} else {
                	$sql = "CALL sp_select_du_kemandoran_all('".$company."', '".$from."', '".$to."' )";
				}
            }    
        }
        
        $query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function generate_du_activity($company, $from, $to,$act)
    {
        //$sql = "CALL sp_select_du_aktivitas_all('".$company."', '".$from."', '".$to."' )";
        if (isset($act)){
            $act = $act;
        } else {
            $act = "";
        }
        
        if ($act=='all')
        {
            $nAct = "1=1";
        }
        elseif ($act=='rwt')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8501%' OR rptdu.ACTIVITY_CODE LIKE '8502%' 
                        OR rptdu.ACTIVITY_CODE LIKE '8503%' OR rptdu.ACTIVITY_CODE LIKE '8504%'
                        OR rptdu.ACTIVITY_CODE LIKE '8505%' OR rptdu.ACTIVITY_CODE LIKE '8506%'
                        OR rptdu.ACTIVITY_CODE LIKE '8507%' OR rptdu.ACTIVITY_CODE LIKE '8508%'
                        OR rptdu.ACTIVITY_CODE LIKE '8509%' OR rptdu.ACTIVITY_CODE LIKE '8510%'
                        OR rptdu.ACTIVITY_CODE LIKE '8598%' OR rptdu.ACTIVITY_CODE LIKE '8599%' ";
        }
        elseif ($act=='rwtinf')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8112%' OR rptdu.ACTIVITY_CODE LIKE '8122%' 
                        OR rptdu.ACTIVITY_CODE LIKE '8132%' OR rptdu.ACTIVITY_CODE LIKE '8142%' 
                        OR rptdu.ACTIVITY_CODE LIKE '8152%' OR rptdu.ACTIVITY_CODE LIKE '8162%'
                        OR rptdu.ACTIVITY_CODE LIKE '8170%' OR rptdu.ACTIVITY_CODE LIKE '8190%' ";
        }
        elseif ($act=='pnn')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8601%' ";
        }
        elseif ($act=='tpnn')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8602%' ";
        }
        elseif ($act=='bbt')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8300%' OR rptdu.ACTIVITY_CODE LIKE '8302%' 
                        OR rptdu.ACTIVITY_CODE LIKE '8303%' OR rptdu.ACTIVITY_CODE LIKE '8309%' ";
        }
        elseif ($act=='ssp')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8402%' ";
        }
        elseif ($act=='pjtnm')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8200%' OR rptdu.ACTIVITY_CODE LIKE '8201%' 
                        OR rptdu.ACTIVITY_CODE LIKE '8202%' OR rptdu.ACTIVITY_CODE LIKE '8203%'
                        OR rptdu.ACTIVITY_CODE LIKE '8204%' OR rptdu.ACTIVITY_CODE LIKE '8205%'
                        OR rptdu.ACTIVITY_CODE LIKE '8206%' OR rptdu.ACTIVITY_CODE LIKE '8207%'
                        OR rptdu.ACTIVITY_CODE LIKE '8209%' OR rptdu.ACTIVITY_CODE LIKE '8299%'
                        OR rptdu.ACTIVITY_CODE LIKE '8401%' ";
        }
        elseif ($act=='pjbbt')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8301%' ";
        }
        elseif ($act=='pjif')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '8111%' OR rptdu.ACTIVITY_CODE LIKE '8121%'
                        OR rptdu.ACTIVITY_CODE LIKE '8131%' OR rptdu.ACTIVITY_CODE LIKE '8141%'
                        OR rptdu.ACTIVITY_CODE LIKE '8151%' OR rptdu.ACTIVITY_CODE LIKE '8161%'
                        OR rptdu.ACTIVITY_CODE LIKE '8199%' ";
        }
        elseif ($act=='umum')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '6201%' ";
        }
        elseif ($act=='vmw')
        {
            $nAct = "rptdu.ACTIVITY_CODE LIKE '9600%' OR rptdu.ACTIVITY_CODE LIKE '9650%'
                        OR rptdu.ACTIVITY_CODE LIKE '9700%' OR rptdu.ACTIVITY_CODE LIKE '9800%'";
        }
        
                
        $sql=
       "SELECT LEFT('".$from."',6) AS PERIODE, rptdu.GANG_CODE, rptdu.EMPLOYEE_CODE, rptdu.NAMA, rptdu.DIVISION_CODE, 
        rptdu.FAMILY_STATUS,  
        rptdu.TYPE_KARYAWAN, rptdu.ACTIVITY_CODE, coa.COA_DESCRIPTION, rptdu.GP, 
        SUM(COALESCE(rptdu.HKE_JUMLAH,0)) AS HK, 
        SUM(COALESCE(rptdu.HKNE_JUMLAH,0)) AS HKNE,
        SUM(COALESCE(rptdu.HKE_JUMLAH,0)) + SUM(COALESCE(rptdu.HKNE_JUMLAH,0)) AS TTL, 
        SUM(COALESCE(rptdu.HKE_BYR,0)) AS HKE_BYR, 
        SUM(COALESCE(rptdu.HKNE_BYR,0)) AS HKNE_BYR,
        SUM(COALESCE(rptdu.HKE_BYR,0)) + SUM(COALESCE(rptdu.HKNE_BYR,0)) AS TTL_BYR,
        SUM(COALESCE(rptdu.LEMBUR_RUPIAH,0)) AS LEMBUR_RUPIAH, 
        SUM(COALESCE(rptdu.PREMI,0)) AS PREMI, 
        SUM(COALESCE(rptdu.PENALTI,0)) AS PENALTI, 
        ( SUM(COALESCE(rptdu.LEMBUR_RUPIAH,0)) + SUM(COALESCE(rptdu.PREMI,0)) ) - SUM(COALESCE(rptdu.PENALTI,0)) 
            AS PREMI_LEMBUR
        FROM (
        SELECT lhm.GANG_CODE, lhm.EMPLOYEE_CODE, emp.NAMA, lhm.LHM_DATE, emp.DIVISION_CODE, emp.FAMILY_STATUS,
        emp.TYPE_KARYAWAN,  
        lhm.TYPE_ABSENSI, lhm.LOCATION_TYPE_CODE, 
        lhm.LOCATION_CODE, lhm.ACTIVITY_CODE,
        emp.GP, 
        lhm.HK_JUMLAH AS HKE_JUMLAH,
        CASE 
             WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI 
            NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  1
         END AS HKNE_JUMLAH,
        CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','')  IN ('BHL', 'KDMP') AND TYPE_ABSENSI IN('KJ','KJI') 
            THEN  ROUND( (emp.GP/ 25)) * lhm.HK_JUMLAH
             WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI IN('KJ','KJI') 
            THEN  ROUND( (emp.GP/ 25))  * lhm.HK_JUMLAH
             WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' 
            THEN  ROUND( (emp.GP/cnt_hk.HK) ) * lhm.HK_JUMLAH
             WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH) <> 0 
            AND TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ cnt_hk.HK) )  * 1
            WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE '%BULANAN%' AND COALESCE(lhm.HK_JUMLAH,0) = 0 AND 
            TYPE_ABSENSI NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  ROUND( (emp.GP/ 25))  * 1
         END AS HKE_BYR,
        CASE WHEN REPLACE(UPPER(TRIM(emp.TYPE_KARYAWAN)),' ','') LIKE 'SKU' AND TYPE_ABSENSI 
            NOT IN ('KJ','KJI','KJO','M','P1','TP1','NA','') THEN  (emp.GP/ 25)  * 1
         END AS HKNE_BYR,
        lhm.LEMBUR_JAM, COALESCE(lhm.LEMBUR_RUPIAH,0) AS LEMBUR_RUPIAH, lhm.PREMI, lhm.PENALTI, lhm.COMPANY_CODE 
        FROM m_gang_activity_detail lhm
        LEFT JOIN m_employee emp ON emp.NIK = lhm.EMPLOYEE_CODE AND emp.COMPANY_CODE = lhm.COMPANY_CODE
        LEFT JOIN ( SELECT lhm2.EMPLOYEE_CODE, emp2.TYPE_KARYAWAN, SUM(lhm2.HK_JUMLAH) 
        AS HK FROM m_gang_activity_detail lhm2
        LEFT JOIN m_employee emp2 ON emp2.NIK = lhm2.EMPLOYEE_CODE
        WHERE lhm2.COMPANY_CODE ='".$company."' AND DATE_FORMAT(lhm2.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' 
                                                                AND '".$to."' 
        AND emp2.TYPE_KARYAWAN LIKE '%BULANAN%' 
        GROUP BY lhm2.EMPLOYEE_CODE
        ) cnt_hk ON cnt_hk.EMPLOYEE_CODE = emp.NIK
        WHERE lhm.COMPANY_CODE = '".$company."' AND DATE_FORMAT(lhm.LHM_DATE, '%Y%m%d') BETWEEN '".$from."' AND 
                                                                    '".$to."' 
        AND lhm.TYPE_ABSENSI <> '' ) rptdu 
    LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = rptdu.ACTIVITY_CODE
    WHERE rptdu.COMPANY_CODE = '".$company."' AND ".$nAct."
    GROUP BY rptdu.ACTIVITY_CODE, rptdu.EMPLOYEE_CODE ORDER BY rptdu.EMPLOYEE_CODE";
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
    
	
	function regenerateKemandoran($company, $periode){
		$sql = "CALL sp_update_gangcode('".$company."', '".$periode."' )";
		$query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
		
	}
	
	/* 	report untuk du bulanan
		author 		: ridhu
		modified 	: 2012-01-16 */
	function get_gangcode($company)
	{
		$query = $this->db->query("SELECT GANG_CODE, EMPLOYEE_CODE, m_empgang.COMPANY_CODE FROM m_empgang
LEFT JOIN ( SELECT NIK, TYPE_KARYAWAN, COMPANY_CODE FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp
	ON emp.NIK = m_empgang.EMPLOYEE_CODE AND emp.COMPANY_CODE = m_empgang.COMPANY_CODE
WHERE m_empgang.COMPANY_CODE = '".$company."' AND emp.TYPE_KARYAWAN = 'BULANAN' 
GROUP BY GANG_CODE");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function generate_du_bulanan($dept, $from, $to,  $company){
        if (strtoupper($dept) != 'ALL') {
		if($company == 'ASL' ){
                	$sql = "CALL sp_select_du_kary_skubulanan('".$company."', '".$from."', '".$to."', '".$dept."' )";
		} else {
			$sql = "CALL sp_select_du_kary_bulanan('".$company."', '".$from."', '".$to."', '".$dept."' )";
		}
        } else {
		if($company == 'ASL' ){
                	$sql = "CALL sp_select_du_kary_skubulanan_all('".$company."', '".$from."', '".$to."')";
		} else {
                $sql = "CALL sp_select_du_kary_bulanan_all('".$company."', '".$from."', '".$to."' )";
		}
        }    
        
	 $query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
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
	
	function regenerateGP($company, $tahun){
		$umr = $this->get_umr($company, $tahun);
		$vumr = 0;
		foreach ( $umr as $row ){
            $vumr = $row['UMR'];
        }
		$sql = "CALL sp_generate_gp_bhl('".$company."', ". $vumr .")";
		$query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
		
	}
	
	function cekGeneratePPh($periode, $company){
		
		$pgquery = "SELECT DISTINCT `PPH_GENERATE_DATE` as RET FROM `m_gad_tambahan` WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."'";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['RET'];
		$this->db->close();
		return $temp;
	}
}

?>