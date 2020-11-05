<?php

class model_rpt_agronomi extends Model 
{
    function model_rpt_agronomi()
    {
        parent::Model(); 
		$this->load->library('global_func');
        $this->load->database();
    }
	
	function getData($company, $vstart, $vend, $block){
		$where = "";
				
		$query = "SELECT ACTIVITYDATE, rpt.ACTIVITY_CODE, c.COA_DESCRIPTION, rpt.LOCATION_CODE, 
					SUBSTR(rpt.LOCATION_CODE,1,5) AS BLOCK, 
					CASE WHEN SUBSTR(rpt.LOCATION_CODE,6,1) < 3 THEN 
						CONCAT('20',SUBSTR(rpt.LOCATION_CODE,6,2))
					WHEN SUBSTR(rpt.LOCATION_CODE,6,1) > 3 THEN 
						CONCAT('19',SUBSTR(rpt.LOCATION_CODE,6,2))
					WHEN SUBSTR(rpt.LOCATION_CODE,6,2) = ' T' THEN 
						'-'
					ELSE 
						'-'
					END AS TAHUNTANAM,
					SUM(COALESCE(HKE_JUMLAH,0)) AS HK, 
					COALESCE(prg.HASIL_KERJA,0) AS HASIL_KERJA, COALESCE(prg.SATUAN,'-') AS SATUAN, 
					COALESCE(prg.HASIL_KERJA2,0) AS HASIL_KERJA2, prg.SATUAN2,
					SUM( ( COALESCE(HKE_BYR,0) + COALESCE(LEMBUR_RUPIAH,0) + COALESCE(PREMI,0) - COALESCE(PENALTI,0) ) ) AS BIAYA, rpt.COMPANY_CODE 
					FROM rpt_du_detail rpt
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = rpt.ACTIVITY_CODE
					LEFT JOIN ( 
								SELECT TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE, SATUAN, 
								SUM(COALESCE(HASIL_KERJA,0)) AS HASIL_KERJA, SATUAN2, 
								SUM(COALESCE(HASIL_KERJA2,0)) AS HASIL_KERJA2, COMPANY_CODE FROM hist_p_progress
								WHERE ACTIVITY_CODE LIKE '85%' AND TGL_PROGRESS BETWEEN '".$vstart."' AND '".$vend."'
								AND COMPANY_CODE = '".$company."' AND LOCATION_CODE LIKE '".$block."%'
								GROUP BY TGL_PROGRESS, LOCATION_CODE, ACTIVITY_CODE
					) prg ON prg.TGL_PROGRESS = rpt.ACTIVITYDATE 
						AND prg.LOCATION_CODE = rpt.`LOCATION_CODE`
						AND prg.ACTIVITY_CODE = rpt.`ACTIVITY_CODE`
						AND prg.COMPANY_CODE = rpt.`COMPANY_CODE`
					WHERE rpt.ACTIVITY_CODE LIKE '85%' 
					AND ACTIVITYDATE BETWEEN '".$vstart."' AND '".$vend."'
					AND rpt.COMPANY_CODE = '".$company."' AND rpt.LOCATION_CODE LIKE '".$block."%' GROUP BY ACTIVITYDATE, rpt.ACTIVITY_CODE, rpt.LOCATION_CODE 
					ORDER BY ACTIVITYDATE, rpt.LOCATION_CODE, rpt.ACTIVITY_CODE";
		 $sQuery=$this->db->query($query);
         $temp_result = array();
         foreach($sQuery->result_array() as $row) {
            	$temp_result [] = $row;     
         }
         return $temp_result;
	}
	
	 function kodeBlok($cv, $company){
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
       
		$qry = "SELECT BLOCKID, CONCAT('Blok ', BLOCKID) AS BLOKDESC FROM m_fieldcrop WHERE COMPANY_CODE = '".$company."' AND BLOCKID LIKE '".$cv."%' GROUP BY BLOCKID, COMPANY_CODE";
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
    }
}

?>