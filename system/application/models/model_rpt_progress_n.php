<?

class model_rpt_progress_n extends Model 
{
    function model_rpt_progress_n()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function get_afdeling($company)//untuk detail
	{
		$query = $this->db->query("SELECT DISTINCT LEFT(LOCATION_CODE,2) as AFD FROM m_location WHERE company_code = '".$company."' AND LOCATION_TYPE_CODE = 'OP' GROUP BY LOCATION_CODE");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	function cek_progress($tgl, $company){
		$query = $this->db->query("SELECT COUNT(*) AS RECORD FROM p_progress WHERE TGL_PROGRESS = '".$tgl."' AND COMPANY_CODE = '".$company."'");
		
        	foreach ( $query->result_array() as $row){
			$ret = $row['RECORD'];
		}
		return $ret;

	}

    function gen_prog_detail($tgl, $afd = '', $jns,  $company)
    {
	 $where_act = "";
	 $table = "";
	$cek = $this->cek_progress($tgl,$company);
if($cek > 0){
	$table = "p_progress";
} else {
	$table = "hist_p_progress";
}
	
        if ( $afd != 'all' ) { $where =" AND pp.LOCATION_CODE LIKE '%".$afd."%'"; } else { $where=''; }
		if ($jns == 'rawat') { $where_act = "AND ACTIVITY_CODE LIKE '85%'"; }
		else if ($jns == 'panen') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8601'"; }
		else if ($jns == 'trans_panen') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8602'"; }
		else if ($jns == 'sisip') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8402'"; }
		else if ($jns == 'tanam') { 
			$where_act = "AND LEFT(ACTIVITY_CODE,4) = '8401'"; 
			/* custom filter afd di project tanam */
			if ( $afd != 'all' ) { $where =" AND pj.PROJECT_LOCATION LIKE '%".$afd."%'"; }
		}
		else if ($jns == 'lc') { $where_act = "AND ACTIVITY_CODE LIKE '82%'"; }
		else if ($jns == 'bibitan') { $where_act = "AND LEFT(ACTIVITY_CODE,4) IN ('8302','8303','8304')"; }
		else if ($jns == 'pj_bibitan') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8301'"; }
		else if ($jns == 'rwtif') { $where_act = "AND LEFT(ACTIVITY_CODE,2) = '81' AND LEFT(ACTIVITY_CODE,4) NOT IN ('8111','8121','8131','8141','8151','8161')"; }
		else if ($jns == 'pj_inf') { $where_act = "AND LEFT(ACTIVITY_CODE,4) IN ('8111','8121','8131','8141','8151','8161')"; }
		else if ($jns == 'all') { $where_act = ""; }
		else if ($jns == 'umum') { $where_act = "AND LEFT(ACTIVITY_CODE,2) IN ('62','17')"; }
		
        $query = $this->db->query("SELECT pp.TGL_PROGRESS, 
	CASE WHEN LEFT(ACTIVITY_CODE,4) = '8401' THEN 
		CONCAT(pp.LOCATION_CODE,' - ',pj.PROJECT_LOCATION)
	ELSE 
		pp.LOCATION_CODE 
	END AS LOCATION, pp.ACTIVITY_CODE AS ACCOUNTCODE, 
	c.COA_DESCRIPTION AS ACCOUNTDESC,  pp.SATUAN AS UNIT1,
	COALESCE(hi.HASIL_KERJA_HI,0) AS HSL_KERJA_HI, SUM(pp.HASIL_KERJA) AS HSL_KERJA_SHI, 
	COALESCE(hi.REALISASI_HI,0) AS REALISASI_HI, SUM(pp.REALISASI) AS REALISASI_SHI, 
	COALESCE(hi.HK_HI,0) AS HK_HI, SUM(pp.HK) AS HK_SHI, um.UMR, 
	COALESCE(hi.RP_PER_SAT_HI,0) AS REALISASI_UNIT_HI, ROUND( COALESCE( (SUM(pp.REALISASI) / SUM(pp.HASIL_KERJA)),0),2) AS REALISASI_UNIT_SHI, 
	COALESCE(hi.HK_PER_SAT_HI,0) AS REALISASI_PERHK_HI, ROUND( COALESCE((SUM(pp.HK) / SUM(pp.HASIL_KERJA)),0),4) AS REALISASI_PERHK_SHI 
FROM ".$table." pp
LEFT JOIN m_coa c ON c.ACCOUNTCODE = pp.ACTIVITY_CODE
LEFT JOIN m_project pj ON pj.PROJECT_ID = pp.LOCATION_CODE
LEFT JOIN ( SELECT GP/25 AS UMR, company_code FROM m_employee WHERE TYPE_KARYAWAN = 'BHL' GROUP BY COMPANY_CODE ) um 
	ON um.COMPANY_CODE = pp.COMPANY_CODE
LEFT JOIN (
	SELECT pp.TGL_PROGRESS AS TGL, pp.LOCATION_CODE, pp.ACTIVITY_CODE AS ACTIVITY, c.COA_DESCRIPTION,  pp.SATUAN, SUM(pp.HASIL_KERJA) AS HASIL_KERJA_HI, SUM(pp.REALISASI) AS REALISASI_HI, 
	SUM(pp.HK) AS HK_HI, um.UMR, ROUND( COALESCE( (SUM(pp.REALISASI) / SUM(pp.HASIL_KERJA)),0),2) AS RP_PER_SAT_HI, 
	ROUND( COALESCE((SUM(pp.HK) / SUM(pp.HASIL_KERJA)),0),4) AS HK_PER_SAT_HI, pp.COMPANY_CODE AS COMPANY
	FROM ".$table." pp
	LEFT JOIN m_coa c ON c.ACCOUNTCODE = pp.ACTIVITY_CODE
	LEFT JOIN m_project pj ON pj.PROJECT_ID = pp.LOCATION_CODE
	LEFT JOIN ( SELECT GP/25 AS UMR, company_code FROM m_employee WHERE TYPE_KARYAWAN = 'BHL' GROUP BY COMPANY_CODE ) um 
		ON um.COMPANY_CODE = pp.COMPANY_CODE
	WHERE pp.COMPANY_CODE = '".$company."' ".$where_act." AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' 
	GROUP BY pp.LOCATION_CODE, pp.ACTIVITY_CODE
) hi ON hi.LOCATION_CODE = pp.LOCATION_CODE AND hi.ACTIVITY = pp.ACTIVITY_CODE AND hi.COMPANY = pp.COMPANY_CODE
WHERE pp.COMPANY_CODE = '".$company."' ".$where_act." ".$where." AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."' GROUP BY pp.LOCATION_CODE, pp.ACTIVITY_CODE ORDER BY pp.ACTIVITY_CODE ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
    }
    
	function gen_prog($tgl, $afd = '', $company, $jns, $acCode='')//untuk total
	{

 $table = "";
	$cek = $this->cek_progress($tgl,$company);
if($cek > 0){
	$table = "p_progress";
} else {
	$table = "hist_p_progress";
}
		if ($afd!='all') {  $where =" AND pp.LOCATION_CODE LIKE '%".$afd."%'";  } else { $where=''; }
        
        if ($acCode !='') { $where4 =" AND pp.ACTIVITY_CODE = '".$acCode."' ";  } else  { $where4=''; }
        
		if ($jns == 'rawat') { $where_act = "AND ACTIVITY_CODE LIKE '85%'"; } 
		else if ($jns == 'panen') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8601'"; }
		else if ($jns == 'trans_panen') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8602'"; }
		else if ($jns == 'sisip') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8402'"; }
		else if ($jns == 'tanam') { 
			$where_act = "AND LEFT(ACTIVITY_CODE,4) = '8401'"; 
			if ( $afd != 'all' ) { $where =" AND pj.PROJECT_LOCATION LIKE '%".$afd."%'"; }
		}
		else if ($jns == 'lc') { $where_act = "AND ACTIVITY_CODE LIKE '82%'"; }
		else if ($jns == 'bibitan') { $where_act = "AND LEFT(ACTIVITY_CODE,4) IN ('8302','8303')"; }
		else if ($jns == 'pj_bibitan') { $where_act = "AND LEFT(ACTIVITY_CODE,4) = '8301'"; }
		else if ($jns == 'rwtif') { $where_act = "AND LEFT(ACTIVITY_CODE,2) = '81' AND LEFT(ACTIVITY_CODE,4) NOT IN ('8111','8121','8131','8141','8151','8161')"; }
		else if ($jns == 'pj_inf') { $where_act = "AND LEFT(ACTIVITY_CODE,4) IN ('8111','8121','8131','8141','8151','8161')"; }
		else if ($jns == 'all') { $where_act = ""; }
		else if ($jns == 'umum') { $where_act = "AND LEFT(ACTIVITY_CODE,2) IN ('62','17')"; }		
     $query = $this->db->query("
	 SELECT BHI.TGL_PROGRESS,
	  BHI.LOCATION,
	  BHI.ACCOUNTCODE,
	  BHI.ACCOUNTDESC,
	  BHI.UNIT1,
	  SUM(BHI.HSL_KERJA_HI) AS HSL_KERJA_HI,
	  SUM(BHI.HSL_KERJA_SHI) AS HSL_KERJA_SHI,
	  SUM(BHI.REALISASI_HI) AS REALISASI_HI,
	  SUM(BHI.REALISASI_SHI) AS REALISASI_SHI,
	  SUM(BHI.HK_HI) AS HK_HI,
	  SUM(BHI.HK_SHI)        AS HK_SHI,
	  BHI.UMR,
	  SUM(BHI.REALISASI_UNIT_HI) AS REALISASI_UNIT_HI,
	  (SUM(BHI.REALISASI_SHI) / SUM(BHI.HSL_KERJA_SHI)) as REALISASI_UNIT_SHI,
	  SUM(BHI.REALISASI_PERHK_HI) AS REALISASI_PERHK_HI,
	  (SUM(BHI.HK_SHI)/SUM(BHI.HSL_KERJA_SHI)) AS REALISASI_PERHK_SHI

FROM(
	 SELECT pp.TGL_PROGRESS, pp.LOCATION_CODE as LOCATION, pp.ACTIVITY_CODE AS ACCOUNTCODE, 
	c.COA_DESCRIPTION AS ACCOUNTDESC,  pp.SATUAN AS UNIT1,
	COALESCE(hi.HASIL_KERJA_HI,0) AS HSL_KERJA_HI, SUM(pp.HASIL_KERJA) AS HSL_KERJA_SHI, 
	COALESCE(hi.REALISASI_HI,0) AS REALISASI_HI, SUM(pp.REALISASI) AS REALISASI_SHI, 
	COALESCE(hi.HK_HI,0) AS HK_HI, SUM(pp.HK) AS HK_SHI, um.UMR, 
	COALESCE(hi.RP_PER_SAT_HI,0) AS REALISASI_UNIT_HI,  COALESCE( (SUM(pp.REALISASI) / SUM(pp.HASIL_KERJA)),0) AS REALISASI_UNIT_SHI, 
	COALESCE(hi.HK_PER_SAT_HI,0) AS REALISASI_PERHK_HI, ROUND( COALESCE((SUM(pp.HK) / SUM(pp.HASIL_KERJA)),0),4) AS REALISASI_PERHK_SHI 
FROM ".$table." pp
LEFT JOIN m_coa c ON c.ACCOUNTCODE = pp.ACTIVITY_CODE
LEFT JOIN m_project pj ON pj.PROJECT_ID = pp.LOCATION_CODE
LEFT JOIN ( SELECT GP/25 AS UMR, company_code FROM m_employee WHERE TYPE_KARYAWAN = 'BHL' GROUP BY COMPANY_CODE ) um 
	ON um.COMPANY_CODE = pp.COMPANY_CODE
LEFT JOIN (
	SELECT pp.TGL_PROGRESS AS TGL, pp.LOCATION_CODE, pp.ACTIVITY_CODE AS ACTIVITY, c.COA_DESCRIPTION,  pp.SATUAN, SUM(pp.HASIL_KERJA) AS HASIL_KERJA_HI, SUM(pp.REALISASI) AS REALISASI_HI, 
	SUM(pp.HK) AS HK_HI, um.UMR, ROUND( COALESCE( (SUM(pp.REALISASI) / SUM(pp.HASIL_KERJA)),0),2) AS RP_PER_SAT_HI, 
	ROUND( COALESCE((SUM(pp.HK) / SUM(pp.HASIL_KERJA)),0),4) AS HK_PER_SAT_HI, pp.COMPANY_CODE AS COMPANY
	FROM ".$table." pp
	LEFT JOIN m_coa c ON c.ACCOUNTCODE = pp.ACTIVITY_CODE
	LEFT JOIN m_project pj ON pj.PROJECT_ID = pp.LOCATION_CODE
	LEFT JOIN ( SELECT GP/25 AS UMR, company_code FROM m_employee WHERE TYPE_KARYAWAN = 'BHL' GROUP BY COMPANY_CODE ) um 
		ON um.COMPANY_CODE = pp.COMPANY_CODE
	WHERE pp.COMPANY_CODE = '".$company."' ".$where_act." ".$where." ".$where4." AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') = '".$tgl."' 
	GROUP BY pp.ACTIVITY_CODE
) hi ON hi.LOCATION_CODE = pp.LOCATION_CODE AND hi.ACTIVITY = pp.ACTIVITY_CODE AND hi.COMPANY = pp.COMPANY_CODE
WHERE pp.COMPANY_CODE = '".$company."' ".$where_act." ".$where." ".$where4."  AND DATE_FORMAT(TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."' GROUP BY pp.LOCATION_CODE)BHI");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;
	}

	//############################### UPDATE 16 Desember ################################
	function gen_prog_detail_tehnik($tgl, $afd,  $company)
	{
		$tgl=$this->db->escape_str($tgl);
		$afd=$this->db->escape_str($afd);
		$company=$this->db->escape_str($company);
		
		//if ($afd!='all') {  $where =" AND m_project.AFD = '".$afd."' ";  }
		//elseif ($afd=='' || $afd=='all') { $where=''; }
		
		$query="SELECT p_progress_teknik.ID_PROGRESS,p_progress_teknik.GANG_CODE, p_progress_teknik.AFD, p_progress_teknik.TGL_PROGRESS,
	p_progress_teknik.LOCATION_CODE,p_progress_teknik.ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS ACTIVITY_DESC, m_project.AFD AS AFD, 	
	m_project.PROJECT_LOCATION AS PJ_LOCATION, SATUAN
	,COALESCE(HI.HASIL_KERJA,0) AS HSL_KERJA_HI
	,COALESCE(SHI.HASIL_KERJA_SHI,0) AS HSL_KERJA_SHI
	,COALESCE(HI.HK,0) AS HK_HI
	,COALESCE(SHI.HK_SHI,0) AS HK_SHI
	,REALISASI,REALISASI_HK,REALISASI_UNIT
FROM p_progress_teknik 
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN (	
		SELECT ID_PROGRESS,GANG_CODE, AFD,LOCATION_CODE,ACTIVITY_CODE,HASIL_KERJA,HK
		FROM p_progress_teknik 
		WHERE COMPANY_CODE = '".$company."' AND TGL_PROGRESS = '".$tgl."'
		ORDER BY ACTIVITY_CODE ASC 
	  )HI
ON HI.ID_PROGRESS = p_progress_teknik.ID_PROGRESS AND HI.LOCATION_CODE=p_progress_teknik.LOCATION_CODE
	AND HI.ACTIVITY_CODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN (
		SELECT ID_PROGRESS,GANG_CODE, AFD,LOCATION_CODE,ACTIVITY_CODE,SUM(HASIL_KERJA) AS HASIL_KERJA_SHI
		,SUM(HK) AS HK_SHI
		FROM p_progress_teknik 
		LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
		WHERE p_progress_teknik.COMPANY_CODE = '".$company."' AND DATE_FORMAT(p_progress_teknik.TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."'
		GROUP BY p_progress_teknik.ACTIVITY_CODE , p_progress_teknik.LOCATION_CODE
	  )SHI
ON SHI.ID_PROGRESS = p_progress_teknik.ID_PROGRESS AND SHI.LOCATION_CODE=p_progress_teknik.LOCATION_CODE
	AND SHI.ACTIVITY_CODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN m_project ON ( m_project.PROJECT_ID = p_progress_teknik.LOCATION_CODE )	
WHERE p_progress_teknik.COMPANY_CODE = '".$company."' AND p_progress_teknik.ACTIVITY_CODE NOT IN ('BREAK DOWN','STAND BY') AND
DATE_FORMAT(p_progress_teknik.TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."'
GROUP BY ACTIVITY_CODE, LOCATION_CODE
ORDER BY ACTIVITY_CODE, LOCATION_CODE, TGL_PROGRESS ASC ";
		$sQuery=$this->db->query($query);
		$rowcount=$sQuery->num_rows();
		
		$temp_result = array();	
		if($rowcount > 0)
		{
			foreach ( $sQuery->result_array() as $row )
			{
				$temp_result [] = $row;
			}
		}
			
		return $temp_result;	
	}
	
	function gen_prog_tehnik($tgl, $afd,  $company, $acCode)
	{
		$tgl=$this->db->escape_str($tgl);
		$afd=$this->db->escape_str($afd);
		$company=$this->db->escape_str($company);
		
		//if ($afd!='all') {  $where =" AND m_project.AFD = '".$afd."' ";  }
		//if ($acCode !='') { $where4 =" AND p_progress_teknik.ACTIVITY_CODE = '".$acCode."'";  } else  { $where4=''; }
		//elseif ($afd=='' || $afd=='all') { $where=''; }
		
		$query="SELECT p_progress_teknik.ID_PROGRESS,p_progress_teknik.GANG_CODE, p_progress_teknik.AFD, p_progress_teknik.TGL_PROGRESS,
	p_progress_teknik.LOCATION_CODE,p_progress_teknik.ACTIVITY_CODE, m_coa.COA_DESCRIPTION AS ACTIVITY_DESC, m_project.AFD AS AFD, 	
	m_project.PROJECT_LOCATION AS PJ_LOCATION, SATUAN
	,COALESCE(SUM(HI.HASIL_KERJA),0) AS HSL_KERJA_HI
	,COALESCE(SUM(SHI.HASIL_KERJA_SHI),0) AS HSL_KERJA_SHI
	,COALESCE(SUM(HI.HK),0) AS HK_HI
	,COALESCE(SUM(SHI.HK_SHI),0) AS HK_SHI
	,REALISASI,REALISASI_HK,REALISASI_UNIT
FROM p_progress_teknik 
LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN (	
		SELECT ID_PROGRESS,GANG_CODE, AFD,LOCATION_CODE,ACTIVITY_CODE,HASIL_KERJA,HK
		FROM p_progress_teknik 
		WHERE COMPANY_CODE = '".$company."' AND TGL_PROGRESS = '".$tgl."'
		ORDER BY ACTIVITY_CODE ASC 
	  )HI
ON HI.ID_PROGRESS = p_progress_teknik.ID_PROGRESS AND HI.LOCATION_CODE=p_progress_teknik.LOCATION_CODE
	AND HI.ACTIVITY_CODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN (
		SELECT ID_PROGRESS,GANG_CODE, AFD,LOCATION_CODE,ACTIVITY_CODE,SUM(HASIL_KERJA) AS HASIL_KERJA_SHI
		,SUM(HK) AS HK_SHI
		FROM p_progress_teknik 
		LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = p_progress_teknik.ACTIVITY_CODE
		WHERE p_progress_teknik.COMPANY_CODE = '".$company."' AND DATE_FORMAT(p_progress_teknik.TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."'
		GROUP BY p_progress_teknik.ACTIVITY_CODE , p_progress_teknik.LOCATION_CODE
	  )SHI
ON SHI.ID_PROGRESS = p_progress_teknik.ID_PROGRESS AND SHI.LOCATION_CODE=p_progress_teknik.LOCATION_CODE
	AND SHI.ACTIVITY_CODE = p_progress_teknik.ACTIVITY_CODE
LEFT JOIN m_project ON ( m_project.PROJECT_ID = p_progress_teknik.LOCATION_CODE )	
WHERE p_progress_teknik.COMPANY_CODE = '".$company."' AND p_progress_teknik.ACTIVITY_CODE NOT IN ('BREAK DOWN','STAND BY') AND
DATE_FORMAT(p_progress_teknik.TGL_PROGRESS,'%Y%m%d') BETWEEN DATE_FORMAT('".$tgl."','%Y%m01') AND '".$tgl."' 
AND p_progress_teknik.ACTIVITY_CODE='".$acCode."'
-- GROUP BY ACTIVITY_CODE
ORDER BY ACTIVITY_CODE ASC ";
		$sQuery=$this->db->query($query);
		$rowcount=$sQuery->num_rows();
		
		$temp_result = array();	
		if($rowcount > 0)
		{
			foreach ( $sQuery->result_array() as $row )
			{
				$temp_result [] = $row;
			}
		}
			
		return $temp_result;	
	}
	
	//################# PESANAN PAK NIZAL ###################
	//18 January 2011
	function gen_prog_pall($periode,$company)
	{
		$periode = $this->db->escape_str($periode);
		$company = $this->db->escape_str($company);
		echo $periode ."-".$company; 
		$query="SELECT TRIM(prggb.location_code) AS LOCATION_CODE, 
		CASE
			 WHEN prggb.location_code LIKE 'PJ%' THEN
			 CONCAT(m_project.PROJECT_DESC, ' - ', m_project.project_location)
			 ELSE m_location.DESCRIPTION
        END AS KETERANGAN,
       CASE
        WHEN activity_code = '8111000' THEN 
				CASE 
					WHEN m_project.PROJECT_DESC LIKE '%PENINGGIAN%' THEN 'PENINGGIAN'
					WHEN m_project.PROJECT_DESC LIKE '%PEMBUATAN%' THEN 'PEMBUATAN'
					WHEN m_project.PROJECT_DESC LIKE '%PERKERASAN%' THEN 'PERKERASAN'
                    WHEN m_project.PROJECT_DESC LIKE '%PELAPISAN%' THEN 'PELAPISAN'
          			WHEN m_project.PROJECT_DESC LIKE '%CUT & FILL%' THEN 'CUT & FILL'
                END
		WHEN activity_code = '8131000' THEN 
				CASE
					WHEN m_project.PROJECT_DESC LIKE '%TAHAP I' THEN 'TAHAP I'
					WHEN m_project.PROJECT_DESC LIKE '%TAHAP II' THEN 'TAHAP II'
					WHEN m_project.PROJECT_DESC LIKE '%TAHAP III' THEN 'TAHAP III'
					WHEN m_project.PROJECT_DESC LIKE '%TAHAP IV' THEN 'TAHAP IV'
				END
         ELSE NULL
       END AS TIPE,
       CASE
         WHEN activity_code = '8111000' THEN 
		 	CASE
		 		WHEN m_project.project_location LIKE 'JT%' THEN 'JALAN TRANSPORT'
                WHEN m_project.project_location LIKE 'JC%' THEN 'JALAN COLLECTION'
                WHEN m_project.project_location LIKE 'JU%' THEN 'JALAN UTAMA'
         	END
         ELSE NULL
       END AS SUBTIPE_LOKASI,
       TRIM(activity_code) AS ACTIVITY_CODE,
       m_coa.coa_description AS COA_DESCRIPTION,
       COALESCE(SUM(qty), 0) AS QTY,
       COALESCE(satuan, '') AS SATUAN,
       FLAG
FROM   ( SELECT TRIM(pvc.location_code) AS location_code,  TRIM(pvc.activity_code) AS activity_code,
               pvc.tgl_aktivitas AS tanggal, prg_tk.qty, prg_tk.satuan, 'BK' AS flag
        FROM   p_vehicle_activity pvc
               LEFT JOIN ( SELECT location_code, activity_code, qty, satuan, company_code
		FROM v_rpt_progress_union WHERE COMPANY_CODE = '".$company."'
		AND DATE_FORMAT(tgl_progress, '%Y%m') = '".$periode."' )  prg_tk ON prg_tk.location_code = pvc.location_code
                    AND prg_tk.activity_code = pvc.activity_code AND prg_tk.COMPANY_CODE = pvc.COMPANY_CODE
        WHERE  pvc.company_code = '".$company."' AND DATE_FORMAT(tgl_aktivitas, '%Y%m') = '".$periode."'
        GROUP  BY location_code, activity_code
        										UNION
        SELECT TRIM(pmm.location_code) AS location_code, TRIM(pmm.activity_code) AS activity_code,
               pmm.tgl_aktivitas AS tanggal,  prg_tk.qty, prg_tk.satuan, 'BM' AS flag
        FROM   p_machine_meter pmm
               LEFT JOIN ( SELECT location_code, activity_code, qty, satuan, company_code
		FROM v_rpt_progress_union WHERE COMPANY_CODE = '".$company."'
		AND DATE_FORMAT(tgl_progress, '%Y%m') = '".$periode."' )prg_tk
                 ON prg_tk.location_code = pmm.location_code AND prg_tk.activity_code = pmm.activity_code
                 AND prg_tk.COMPANY_CODE = pmm.COMPANY_CODE
        WHERE  pmm.company_code = '".$company."' AND DATE_FORMAT(tgl_aktivitas, '%Y%m') = '".$periode."'
        GROUP  BY location_code, activity_code
        UNION
        SELECT TRIM(mgad.location_code) AS location_code, TRIM(mgad.activity_code) AS activity_code,
               mgad.lhm_date AS tanggal, prg_tk.qty, prg_tk.satuan, 'LHM' AS flag
        FROM   m_gang_activity_detail mgad
               LEFT JOIN ( SELECT location_code, activity_code, qty, satuan, company_code
		FROM v_rpt_progress_union WHERE COMPANY_CODE = '".$company."'
		AND DATE_FORMAT(tgl_progress, '%Y%m') = '".$periode."' )prg_tk
                ON prg_tk.location_code = mgad.location_code AND prg_tk.activity_code = mgad.activity_code 
                AND prg_tk.COMPANY_CODE = mgad.COMPANY_CODE 
        WHERE  mgad.company_code = '".$company."' AND DATE_FORMAT(lhm_date, '%Y%m') = '".$periode."'
        GROUP  BY location_code, activity_code
        										UNION
        SELECT TRIM(pwa.location_code) AS location_code, TRIM(pwa.activity_code) AS activity_code,
               pwa.tgl_aktivitas AS tanggal, prg_tk.qty, prg_tk.satuan, 'WS' AS flag
        FROM   p_workshop_activity pwa
               LEFT JOIN (  SELECT location_code, activity_code, qty, satuan, company_code
		FROM v_rpt_progress_union WHERE COMPANY_CODE = '".$company."'
		AND DATE_FORMAT(tgl_progress, '%Y%m') = '".$periode."' )prg_tk
                 ON prg_tk.location_code = pwa.location_code AND prg_tk.activity_code = pwa.activity_code
                 AND prg_tk.COMPANY_CODE = pwa.COMPANY_CODE 
        WHERE  pwa.company_code = '".$company."' AND DATE_FORMAT(tgl_aktivitas, '%Y%m') = '".$periode."'
        GROUP  BY location_code, activity_code) prggb 
	LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = prggb.activity_code 
	LEFT JOIN m_project ON m_project.PROJECT_ID = prggb.location_code
	LEFT JOIN m_location ON m_location.LOCATION_CODE = prggb.location_code
	GROUP BY LOCATION_CODE,ACTIVITY_CODE ORDER BY LOCATION_CODE ASC";
		$sQuery=$this->db->query($query);
		$rowcount=$sQuery->num_rows();
		
		$temp_result = array();	
		if($rowcount > 0)
		{
			foreach ( $sQuery->result_array() as $row )
			{
				$temp_result [] = $row;
				
			}
		}
			
		return $temp_result;
	}
	//##################################################
}
?>