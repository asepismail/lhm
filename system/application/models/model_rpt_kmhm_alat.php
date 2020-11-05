<?

class model_rpt_kmhm_alat extends Model 
{
    function model_rpt_kmhm_alat()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function generate($company, $periode){
		$sql = "SELECT pa.KODE_KENDARAAN, vh.DESCRIPTION, 
	CASE WHEN vh.OWNERSHIP = 'I' THEN 'INVENTARIS'
		WHEN vh.OWNERSHIP = 'R' THEN 'RENTAL'
	END AS OWNERSHIP, pa.SATUAN_PRESTASI, 
CASE WHEN pa.SATUAN_PRESTASI = 'KM' THEN 
	CASE WHEN COALESCE(SUM(pa.KMHM_JUMLAH),0) = 0 THEN
			COALESCE(SUM(pa.JAM_KERJA),0)
	     WHEN COALESCE(SUM(pa.KMHM_JUMLAH),0) > 0 THEN
			CASE WHEN COALESCE(SUM(pa.JAM_KERJA),0) > 0 THEN
				COALESCE(SUM(pa.KMHM_JUMLAH),0)	
			END
		ELSE 
			COALESCE(SUM(pa.KMHM_JUMLAH),0)
		END
	ELSE 0
	END AS JUMLAH_KM,
CASE WHEN pa.SATUAN_PRESTASI = 'HM' THEN
	CASE WHEN COALESCE(SUM(pa.KMHM_JUMLAH),0) = 0 THEN
			COALESCE(SUM(pa.JAM_KERJA),0)
	     WHEN COALESCE(SUM(pa.KMHM_JUMLAH),0) > 0 THEN
			CASE WHEN COALESCE(SUM(pa.JAM_KERJA),0) > 0 THEN
				COALESCE(SUM(pa.KMHM_JUMLAH),0)	
			END
		ELSE 
			COALESCE(SUM(pa.KMHM_JUMLAH),0)
		END
	ELSE 0
END AS JUMLAH_HM,
COALESCE(SUM(pa.JAM_KERJA),0), COALESCE(SUM(pa.KMHM_JUMLAH),0) 
FROM p_vehicle_activity pa
LEFT JOIN m_vehicle vh ON vh.VEHICLECODE = pa.KODE_KENDARAAN AND vh.COMPANY_CODE = pa.COMPANY_CODE  
WHERE DATE_FORMAT(TGL_AKTIVITAS,'%Y%m') = '".$periode."'
AND pa.COMPANY_CODE = '".$company."' AND ACTIVITY_CODE NOT IN ('8999997','8999995','8999996')
GROUP BY KODE_KENDARAAN ORDER BY KODE_KENDARAAN, OWNERSHIP ";
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