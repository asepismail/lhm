<?

class model_rpt_progress extends Model 
{
    function model_rpt_progress()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function get_afdeling($company)
	{
		$query = $this->db->query("SELECT DISTINCT LEFT(LOCATION_CODE,2) as AFD FROM m_location WHERE company_code = '".$company."' AND LOCATION_TYPE_CODE = 'OP' GROUP BY LOCATION_CODE");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
    function gen_prog_rawat($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_rawat
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_rawat.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'RWT' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_rawat p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE  
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_rawat p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
    }
    
	function gen_prog_rawat_detail($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
		{
			$where ="AND LEFT(p2.location_code,2) = '".$afd."'";
			$where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
			$where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
		}
		else
		{
			$where='';
			$where2='';
			$where3 ='';
           
		}
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
		if ($to=='')
		{
			$to=$tgl;
		}
	
		$query = $this->db->query("SELECT p.LOCATION, 
		p.ACCOUNTCODE,  
		COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
		COALESCE(map.UNIT1,'-') AS UNIT1,
		map.UNIT2,
		COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
		COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
		COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
		COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
		COALESCE(a.HK,0.00) AS HK_HI,
		COALESCE(b.HK_SHI,0.00) AS HK_SHI,		
		COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
		COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
		COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,		
		COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
		FROM
		(
			SELECT DISTINCT LOCATION_CODE AS LOCATION , 
			CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
			,coa.COA_DESCRIPTION	
			FROM p_progress_rawat
			INNER JOIN m_coa coa 
			ON coa.ACCOUNTCODE = p_progress_rawat.ACTIVITY_CODE
		)p
		
		INNER JOIN 
		(
		 SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
			(
			 SELECT ACCOUNTCODE FROM m_progress_map 
			 WHERE PENGGUNAAN = 'RWT' 
			 AND PARENT <> 1
			) 
		) map 
		ON map.accountcode = p.accountcode
		
		INNER JOIN 
		( 
          SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
            SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
            SUM(p2.REALISASI) AS REALISASI,
            CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                THEN '0'
                ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
            END AS REALISASI_HK,
            
            CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                THEN '0'
                ELSE SUM(p2.REALISASI_UNIT) 
            END AS REALISASI_UNIT,
            SUM(p2.HK) AS HK, 
            p2.COMPANY_CODE 
            FROM p_progress_rawat p2
            LEFT JOIN
            (
                SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
            )emp
            ON emp.COMPANY_CODE = p2.COMPANY_CODE
            WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
            GROUP BY p2.location_code, p2.ACTIVITY_CODE
		) a
		ON a.location_code=p.location AND a.activity_code = p.accountcode
		
		INNER JOIN 
		( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
            SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
            SUM(p.HK) AS HK_SHI, 
            SUM(p.REALISASI) AS REALISASI_SHI,
            CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                THEN '0'
                ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
            END AS REALISASI_HK_SHI,
            CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                THEN '0'
                ELSE SUM(p.REALISASI_UNIT) 
            END AS REALISASI_UNIT_SHI
            FROM p_progress_rawat p 
            LEFT JOIN
            (
                SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
            )emp
            ON emp.COMPANY_CODE = p.COMPANY_CODE
            WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
            DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
            DATE_FORMAT('".$tgl."','%Y%m%01')
                AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
            GROUP BY p.location_code,p.ACTIVITY_CODE  
		) b 
		ON b.location_code=p.location AND b.activity_code = p.accountcode
		 ".$where2."
		GROUP BY p.LOCATION, p.ACCOUNTCODE 
		ORDER BY p.ACCOUNTCODE ASC
		");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;
	}

	function gen_prog_panen($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_panen
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_panen.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'PNN' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_panen p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_panen p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
    function gen_prog_panen_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_panen
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_panen.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN = 'PNN' 
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_panen p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_panen p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    

	function gen_prog_tp($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_tp
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_tp.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'TP' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_tp p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_tp p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_tp_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_tp
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_tp.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN = 'TP' 
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                 
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_tp p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_tp p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
	function gen_prog_bibitan($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_bibitan
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_bibitan.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'NS' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_bibitan p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_bibitan p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_bibitan_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_bibitan
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_bibitan.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN = 'NS' 
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_bibitan p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_bibitan p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
	function gen_prog_sisip($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_sisip
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_sisip.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'SSP' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_sisip p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_sisip p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_sisip_detail($tgl, $afd, $company, $to,$acCode='')
    {
         if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_sisip
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_sisip.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN = 'SSP' 
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_sisip p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_sisip p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
    
	function gen_prog_rwtinf($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_rawat_if
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_rawat_if.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'RWTIF' 
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_rawat_if p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE
            )SHI
            LEFT JOIN
            (
                SELECT p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_rawat_if p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_rwtinf_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_rawat_if
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_rawat_if.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN = 'RWTIF' 
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_rawat_if p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_rawat_if p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
    
	function gen_prog_tanam($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_tanam
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_tanam.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN IN ('TN','LC')
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_tanam p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_tanam p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_tanam_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_tanam
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_tanam.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN IN ('TN','LC')
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_tanam p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_tanam p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
    
	
	function gen_prog_pjinf($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_infrastruktur
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_infrastruktur.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'IF'
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_infrastruktur p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_infrastruktur p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
	function gen_prog_pjinf_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_infrastruktur
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_infrastruktur.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN ='IF'
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_infrastruktur p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_infrastruktur p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }    
        return $temp_result;
    }
    
	function gen_prog_pjbbt($tgl, $afd, $company, $to,$acCode='')
	{
		if ($afd!='all')
        {
            $where =" AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2="WHERE LEFT(pMap.LOCATION,2) = '".$afd."'";
            $where3 =" AND LEFT(p.location_code,2) = '".$afd."'";
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
        }
        
        if ($acCode!='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."'";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."'";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT pMap.LOCATION, pMap.ACCOUNTCODE,  
            COALESCE(pMap.ACCOUNTDESC,'') AS ACCOUNTDESC,
            COALESCE(pMap.UNIT1,'-') AS UNIT1, pMap.UNIT2,
            COALESCE(b.HASIL_KERJA_HI,0.00)AS HSL_KERJA_HI, 
            COALESCE(b.HASIL_KERJA_SHI,0.00)AS HSL_KERJA_SHI,
            COALESCE(b.REALISASI,0.00)AS REALISASI_HI, 
            COALESCE(b.REALISASI_SHI,0.00)AS REALISASI_SHI,
            COALESCE(b.HK,0.00)AS HK_HI, 
            COALESCE(b.HK_SHI,0.00)AS HK_SHI,
            COALESCE(b.REALISASI_HK,0.00)AS REALISASI_PERHK_HI, 
            COALESCE(b.REALISASI_HK_SHI,0.00)AS REALISASI_PERHK_SHI, 
            COALESCE(b.REALISASI_UNIT,0.00)AS REALISASI_UNIT_HI, 
            COALESCE(b.REALISASI_UNIT_SHI,0.00)AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT p.LOCATION, p.ACCOUNTCODE,  
                COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
                COALESCE(map.UNIT1,'-') AS UNIT1, map.UNIT2
            FROM
            (
                SELECT DISTINCT LOCATION_CODE AS LOCATION , 
                CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
                ,coa.COA_DESCRIPTION    
                FROM p_progress_pjbibitan
                INNER JOIN m_coa coa 
                ON coa.ACCOUNTCODE = p_progress_pjbibitan.ACTIVITY_CODE
            )p
                    
            INNER JOIN 
            (
                SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
                (
                    SELECT ACCOUNTCODE FROM m_progress_map 
                    WHERE PENGGUNAAN = 'PJNS'
                    AND PARENT <> 1
                ) 
            ) map 
            ON map.accountcode = p.accountcode
        )pMap
        RIGHT JOIN
        (
        SELECT HISHI.TGL_PROGRESS,HISHI.LOCATION_CODE, HISHI.ACTIVITY_CODE, HISHI.SATUAN,
          SUM(HASIL_KERJA_HI) AS HASIL_KERJA_HI, 
          SUM(HASIL_KERJA_SHI) AS HASIL_KERJA_SHI, 
          SUM(REALISASI) AS REALISASI,
          SUM(REALISASI_SHI) AS REALISASI_SHI,
          SUM(HK) AS HK, 
          SUM(HK_SHI) AS HK_SHI, 
          SUM(REALISASI_HK) AS REALISASI_HK , 
          SUM(REALISASI_HK_SHI) AS REALISASI_HK_SHI ,
          SUM(REALISASI_UNIT) AS REALISASI_UNIT,
          SUM(REALISASI_UNIT_SHI) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT SHI.TGL_PROGRESS,SHI.LOCATION_CODE, SHI.ACTIVITY_CODE, COALESCE(HI.SATUAN,'Ha') AS SATUAN,
              COALESCE(HI.HASIL_KERJA_HI,0.00) AS HASIL_KERJA_HI, 
              COALESCE(SHI.HSL_KERJA_SHI,0.00) AS HASIL_KERJA_SHI, 
              COALESCE(HI.REALISASI,0.00) AS REALISASI,
              COALESCE(SHI.REALISASI_SHI,0.00) AS REALISASI_SHI,
              COALESCE(HI.HK,0.00) AS HK, 
              COALESCE(SHI.HK_SHI,0.00) AS HK_SHI, 
              COALESCE(HI.REALISASI_HK,0.00) AS REALISASI_HK, 
              COALESCE(SHI.REALISASI_HK_SHI,0.00) AS REALISASI_HK_SHI, 
              COALESCE(HI.REALISASI_UNIT,0.00) AS REALISASI_UNIT, HI.COMPANY_CODE,
              COALESCE(SHI.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
            FROM
            (
                SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_pjbibitan p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
            )SHI
            LEFT JOIN
            (
                SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_pjbibitan p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
            )HI
            ON HI.ACTIVITY_CODE=SHI.ACTIVITY_CODE AND HI.LOCATION_CODE=SHI.LOCATION_CODE
            GROUP BY SHI.ACTIVITY_CODE,SHI.LOCATION_CODE
            ORDER BY SHI.ACTIVITY_CODE ASC 
        )HISHI
        GROUP BY HISHI.ACTIVITY_CODE
        ORDER BY HISHI.ACTIVITY_CODE ASC 
        )b
        ON b.ACTIVITY_CODE=pMap.ACCOUNTCODE AND b.LOCATION_CODE=pMap.LOCATION
        ".$where2."
        GROUP BY  pMap.LOCATION, pMap.ACCOUNTCODE
        ORDER BY  pMap.ACCOUNTCODE ASC
        ");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }  
        $query->free_result();  
        return $temp_result;
	}
    function gen_prog_pjbbt_detail($tgl, $afd, $company, $to,$acCode='')
    {
        if ($afd!='all')
        {
            $where ="AND LEFT(p2.location_code,2) = '".$afd."'";
            $where2 ="WHERE LEFT(p.LOCATION,2) = '".$afd."'";
            $where3 ="AND LEFT(p.location_code,2) = '".$afd."'";
            
        }
        else
        {
            $where='';
            $where2='';
            $where3 ='';
           
        }
        
        if ($acCode !='')
        {
            $where4 =" AND p2.ACTIVITY_CODE = '".$acCode."' ";
            $where5 =" AND p.ACTIVITY_CODE = '".$acCode."' ";   
        }
        else
        {   $where4='';
            $where5='';
        }
        
        if ($to=='')
        {
            $to=$tgl;
        }
    
        $query = $this->db->query("SELECT p.LOCATION, 
        p.ACCOUNTCODE,  
        COALESCE(p.COA_DESCRIPTION,'') AS ACCOUNTDESC,
        COALESCE(map.UNIT1,'-') AS UNIT1,
        map.UNIT2,
        COALESCE(a.HASIL_KERJA_HI,0.00) AS HSL_KERJA_HI, 
        COALESCE(b.HSL_KERJA_SHI,0.00) AS HSL_KERJA_SHI,
        COALESCE(a.REALISASI,0.00) AS REALISASI_HI,
        COALESCE(b.REALISASI_SHI,0.00) AS REALISASI_SHI,
        COALESCE(a.HK,0.00) AS HK_HI,
        COALESCE(b.HK_SHI,0.00) AS HK_SHI,        
        COALESCE(a.REALISASI_HK,00) AS REALISASI_PERHK_HI,
        COALESCE(b.REALISASI_HK_SHI,0.00) AS REALISASI_PERHK_SHI,
        COALESCE(a.REALISASI_UNIT,0.00) AS REALISASI_UNIT_HI,        
        COALESCE(b.REALISASI_UNIT_SHI,0.00) AS REALISASI_UNIT_SHI
        FROM
        (
            SELECT DISTINCT LOCATION_CODE AS LOCATION , 
            CASE WHEN ACTIVITY_CODE = '' THEN '-' ELSE ACTIVITY_CODE END AS ACCOUNTCODE
            ,coa.COA_DESCRIPTION    
            FROM p_progress_pjbibitan
            INNER JOIN m_coa coa 
            ON coa.ACCOUNTCODE = p_progress_pjbibitan.ACTIVITY_CODE
        )p
        
        INNER JOIN 
        (
         SELECT * FROM m_progress_map WHERE m_progress_map.ACCOUNTCODE IN 
            (
             SELECT ACCOUNTCODE FROM m_progress_map 
             WHERE PENGGUNAAN ='PJNS'
             AND PARENT <> 1
            ) 
        ) map 
        ON map.accountcode = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT p2.AFD,p2.TGL_PROGRESS,p2.LOCATION_CODE, p2.ACTIVITY_CODE, p2.SATUAN,
                SUM(p2.HASIL_KERJA) AS HASIL_KERJA_HI, 
                SUM(p2.REALISASI) AS REALISASI,
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p2.REALISASI)/emp.UMR)/SUM(p2.HK))
                END AS REALISASI_HK,
                
                CASE WHEN SUM(p2.HASIL_KERJA) IS NULL OR SUM(p2.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p2.REALISASI_UNIT) 
                END AS REALISASI_UNIT,
                SUM(p2.HK) AS HK, 
                p2.COMPANY_CODE 
                FROM p_progress_pjbibitan p2
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p2.COMPANY_CODE
                WHERE p2.COMPANY_CODE = '".$company."' ".$where4."  AND p2.TGL_PROGRESS = '".$tgl."' ".$where."
                GROUP BY p2.location_code, p2.ACTIVITY_CODE 
        ) a
        ON a.location_code=p.location AND a.activity_code = p.accountcode
        
        INNER JOIN 
        ( 
         SELECT TGL_PROGRESS,LOCATION_CODE, ACTIVITY_CODE, 
                SUM(p.HASIL_KERJA) AS HSL_KERJA_SHI, 
                SUM(p.HK) AS HK_SHI, 
                SUM(p.REALISASI) AS REALISASI_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE ((SUM(p.REALISASI)/emp.UMR)/SUM(p.HK))
                END AS REALISASI_HK_SHI,
                CASE WHEN SUM(p.HASIL_KERJA) IS NULL OR SUM(p.HASIL_KERJA)='0.00'
                    THEN '0'
                    ELSE SUM(p.REALISASI_UNIT) 
                END AS REALISASI_UNIT_SHI
                FROM p_progress_pjbibitan p 
                LEFT JOIN
                (
                    SELECT GP/25 AS UMR, COMPANY_CODE FROM m_employee 
                    WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN = 'BHL' LIMIT 1
                )emp
                ON emp.COMPANY_CODE = p.COMPANY_CODE
                WHERE p.COMPANY_CODE = '".$company."' ".$where5." AND     
                DATE_FORMAT(p.TGL_PROGRESS,'%Y%m%d') BETWEEN 
                DATE_FORMAT('".$tgl."','%Y%m%01')
                    AND DATE_FORMAT('".$to."','%Y%m%d') ".$where3."
                GROUP BY p.location_code,p.ACTIVITY_CODE 
        ) b 
        ON b.location_code=p.location AND b.activity_code = p.accountcode
         ".$where2."
        GROUP BY p.LOCATION, p.ACCOUNTCODE 
        ORDER BY p.ACCOUNTCODE ASC
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