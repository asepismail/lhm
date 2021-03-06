<?php
class model_s_analisa_panen extends Model{
    function __construct(){
        parent::__construct();  
		$this->load->database();
    }
    
    function get_supplier($company){
        $query = $this->db->query("SELECT SUPPLIERCODE, SUPPLIERNAME FROM m_supplier WHERE COMPANY_CODE = '".$company."' AND ACTIVE=1");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
            
        }    
        return $temp_result;  
    }
    
    function get_afd($company){
        $query = $this->db->query("SELECT AFD_CODE,COMPANY_CODE FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
            
        }    
        return $temp_result;  
    }
    
    function get_kontraktor($company){
        $query = $this->db->query("SELECT KODE_KONTRAKTOR, NAMA_KONTRAKTOR FROM m_kontraktor WHERE COMPANY_CODE = '".$company."' AND ACTIVE=1");
        
        $temp_result = array();
                
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
            
        }    
        return $temp_result;  
    }
    
    function get_kontraktor_detail($kodekontraktor,$company){
        $query = $this->db->query("SELECT KODE_KONTRAKTOR, NAMA_KONTRAKTOR FROM m_kontraktor WHERE COMPANY_CODE = '".$company."' 
                    AND KODE_KONTRAKTOR='".$kodekontraktor."' AND ACTIVE=1");
        $temp_result = array();       
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
            
        }    
        return $temp_result;  
    }
    
    function generate_tbglampbatpanen($kodekontraktor, $periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $kodekontraktor = $this->db->escape_str($kodekontraktor);
        $company = $this->db->escape_str($company);    
        
        $query="SELECT a.ID_TIMBANGAN, a.NO_KENDARAAN, a.DRIVER_NAME, b.AFD, 
                SUM(b.BERAT_REAL) AS BERAT_REAL, c.COST, 
                (c.COST * SUM(b.BERAT_REAL)) AS C_TERIMA,
                (0.02 * (c.COST * SUM(b.BERAT_REAL))) AS PPH23,
                (4 * SUM(b.BERAT_REAL)) AS SPSI,
                (c.COST * SUM(b.BERAT_REAL))-((0.02 * (c.COST * SUM(b.BERAT_REAL)))+(4 * SUM(b.BERAT_REAL))) AS C_TOTAL_TERIMA,
                d.NAMA_KONTRAKTOR
                FROM s_data_timbangan a
                LEFT JOIN s_data_timbangan_detail b
                    ON b.ID_TIMBANGAN = a.ID_TIMBANGAN
                LEFT JOIN s_data_bjr_cost c
                    ON c.AFD = b.AFD
                LEFT JOIN m_kontraktor d
                    on d.KODE_KONTRAKTOR = a.KODE_KONTRAKTOR
                WHERE a.COMPANY_CODE='".$company."' and a.TYPE_BUAH=1 and a.TANGGALM BETWEEN '".$periode."' and '".$periode_to."'
                    AND a.KODE_KONTRAKTOR='".$kodekontraktor."'
                GROUP BY b.AFD, a.DRIVER_NAME
                ORDER BY a.DRIVER_NAME,a.NO_KENDARAAN, b.AFD , a.TANGGALM ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function generate_tbgbatpanen($kodekontraktor, $periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $kodekontraktor = $this->db->escape_str($kodekontraktor);
        $company = $this->db->escape_str($company);    
        
        $query="SELECT a.ID_TIMBANGAN, a.NO_KENDARAAN, a.DRIVER_NAME, b.AFD, 
                SUM(b.BERAT_REAL) AS BERAT_REAL, c.COST, 
                (c.COST * SUM(b.BERAT_REAL)) AS C_TERIMA,
                (0.02 * (c.COST * SUM(b.BERAT_REAL))) AS PPH23,
                (4 * SUM(b.BERAT_REAL)) AS SPSI,
                (c.COST * SUM(b.BERAT_REAL))-((0.02 * (c.COST * SUM(b.BERAT_REAL)))+(4 * SUM(b.BERAT_REAL))) AS C_TOTAL_TERIMA,
                d.NAMA_KONTRAKTOR
                FROM s_data_timbangan a
                LEFT JOIN s_data_timbangan_detail b
                    ON b.ID_TIMBANGAN = a.ID_TIMBANGAN
                LEFT JOIN s_data_bjr_cost c
                    ON c.AFD = b.AFD
                LEFT JOIN m_kontraktor d
                    on d.KODE_KONTRAKTOR = a.KODE_KONTRAKTOR
                WHERE a.COMPANY_CODE='".$company."' and a.TYPE_BUAH=1 and a.TANGGALM BETWEEN '".$periode."' and '".$periode_to."'
                    AND a.KODE_KONTRAKTOR='".$kodekontraktor."'
                GROUP BY b.AFD
                ORDER BY b.AFD , a.TANGGALM ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function generate_dttbgbatpanen($afd, $periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $afd = $this->db->escape_str($afd);
        $company = $this->db->escape_str($company);    
        
        if (strtolower($afd)!='all'){
            $wAfd=" AND a.AFD = '".$afd."'";    
        }else{
            $wAfd="";
        }
        $query="select b.TANGGALM, a.NO_KENDARAAN,a.NO_TIKET
                            ,a.BLOCK, a.BERAT_REAL, c.COST,
                            (a.BERAT_REAL * c.COST) as JUMLAH,
                            (0.02*(a.BERAT_REAL * c.COST)) AS POTONGAN,
                            ((a.BERAT_REAL * c.COST)-(0.02*(a.BERAT_REAL * c.COST))) AS JUMLAH_AKHIR
                from s_data_timbangan_detail a
                INNER JOIN s_data_timbangan b
                    on a.ID_TIMBANGAN = b.ID_TIMBANGAN
                LEFT JOIN s_data_bjr_cost c
                    on c.COMPANY_CODE = b.COMPANY_CODE AND
                        c.AFD = b.AFD
                where b.COMPANY_CODE='".$company."' and b.TYPE_BUAH=1 and b.TANGGALM BETWEEN '".$periode."' and '".$periode_to."'
                    $wAfd  
                ORDER BY b.TANGGALM, a.BLOCK ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function generate_adem_dispatch($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
         $query="SELECT CONCAT(dpc.COMPANY_CODE,'-','Site') AS organisasi,dpc.ID_DISPATCH AS DOCUMENT_NO,'MM Shipment' as DocTypeName,
                     dpc.TANGGALM,dpcdo.SO_NUMBER AS ORDER_NO,
                     CASE WHEN TRIM(dpc.JENIS) IN ('KERNEL','KRN') THEN 'PK' ELSE TRIM(dpc.JENIS) END AS JENIS, dpc.BERAT_BERSIH, dpc.ID_DISPATCH, dpc.NO_KENDARAAN,
                     CASE WHEN TRIM(dpc.JENIS) IN ('KERNEL','KRN') THEN 'Gd PK $company' ELSE 'Gd CPO $company' END AS warehouse, '' AS KETERANGAN
                     FROM s_dispatch dpc
                     LEFT JOIN s_dispatch_do dpcdo ON dpcdo.ID_DO = dpc.ID_DO 
                     WHERE dpc.COMPANY_CODE ='".$company."'  
                     AND DATE_FORMAT(dpc.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
		UNION
		SELECT CONCAT(dpc.COMPANY_CODE,'-','Site') AS organisasi,dpc.ID_DISPATCH AS DOCUMENT_NO,'MM Shipment' as DocTypeName,
                     dpc.TANGGALM,dpcdo.SO_NUMBER AS ORDER_NO,
                     CASE WHEN TRIM(dpc.JENIS) IN ('KERNEL','KRN') THEN 'PK' ELSE TRIM(dpc.JENIS) END AS JENIS, dpc.BERAT_BERSIH*-1, dpc.ID_DISPATCH, dpc.NO_KENDARAAN,
                     CASE WHEN TRIM(dpc.JENIS) IN ('KERNEL','KRN') THEN 'Gd PK $company' ELSE 'Gd CPO $company' END AS warehouse, '' AS KETERANGAN
                     FROM s_dispatch_return dpc
                     LEFT JOIN s_dispatch_do dpcdo ON dpcdo.ID_DO = dpc.ID_DO 
                     WHERE dpc.COMPANY_CODE ='".$company."'  
                     AND DATE_FORMAT(dpc.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
		UNION                
		SELECT CONCAT(s_purchase.COMPANY_CODE,'-','Site'), ID_DISPATCH AS DOCUMENT_NO, 'MM Shipment' as DocTypeName,
		TANGGALM, PO_NUMBER AS ORDER_NO,
		CASE WHEN TRIM(s_komoditas.JENIS) IN ('KERNEL','KRN') THEN 'PK' ELSE TRIM(s_komoditas.JENIS) END AS JENIS, 
		BERAT_BERSIH AS BERAT_BERSIH, ID_DISPATCH, '' AS NO_KENDARAAN,  
		CASE WHEN TRIM(s_komoditas.JENIS) IN ('KERNEL','KRN') THEN 'Gd PK $company' ELSE 'Gd CPO $company' END AS warehouse, DESCRIPTION AS KETERANGAN
		FROM s_purchase
		LEFT JOIN s_komoditas ON s_purchase.COMPANY_CODE = s_komoditas.COMPANY_CODE
		AND s_purchase.ID_KOMODITAS = s_komoditas.ID_KOMODITAS  
		WHERE s_purchase.COMPANY_CODE = '".$company."' AND s_purchase.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
		UNION
		SELECT CONCAT(s_sales.COMPANY_CODE,'-','Site'), ID_DISPATCH AS DOCUMENT_NO, 'MM Shipment' as DocTypeName,
		TANGGALM, SO_NUMBER AS ORDER_NO,
		CASE WHEN TRIM(s_komoditas.JENIS) IN ('KERNEL','KRN') THEN 'PK' ELSE TRIM(s_komoditas.JENIS) END AS JENIS, 
		BERAT_BERSIH AS BERAT_BERSIH, ID_DISPATCH, '' AS NO_KENDARAAN,  
		CASE WHEN TRIM(s_komoditas.JENIS) IN ('KERNEL','KRN') THEN 'Gd PK $company' ELSE 'Gd CPO $company' END AS warehouse, DESCRIPTION AS KETERANGAN
		FROM s_sales
		LEFT JOIN s_komoditas ON s_sales.COMPANY_CODE = s_komoditas.COMPANY_CODE
		AND s_sales.ID_KOMODITAS = s_komoditas.ID_KOMODITAS  
		WHERE s_sales.COMPANY_CODE = '".$company."' AND s_sales.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
				SELECT 
CASE WHEN m.COMPANY_CODE = 'SMI' THEN 'SSS-Mills' ELSE CONCAT(m.COMPANY_CODE,'-','Site') END AS organisasi , ID_SOUNDING AS DOCUMENT_NO,  'MM Shipment' as DocTypeName, DATE2 AS TANGGALM, DOC_NO AS ORDER_NO,
PRODUCT_CODE AS JENIS, 
BERAT_BERSIH AS BERAT_BERSIH, ID_SOUNDING AS ID_DISPATCH, ID_STORAGE2 AS NO_KENDARAAN,  
		'Gd CPO SMI' AS warehouse, '' AS KETERANGAN

FROM s_movement_sounding m
INNER JOIn m_storage s ON s.ID_STORAGE = m.ID_STORAGE2
WHERE m.COMPANY_CODE ='".$company."' AND MOV_TYPE = 'D'
AND DATE2 BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
";

        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
        /*
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result; */   
    }
    
	function generate_restan($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
         $query="SELECT * FROM (
	SELECT COMPANY_CODE, LOCATION_CODe, max(nab.DATE_TRANSACT) AS MAX_DATE, (SELECT janjang_restan FROM rpt_nab WHERE LOCATION_CODE = nab.LOCATION_CODE AND DATE_TRANSACT = max(nab.DATE_TRANSACT) AND COMPANY_CODE= nab.COMPANY_CODE) AS RESTAN
	FROM rpt_nab nab
	WHERE nab.COMPANY_CODE = '".$company."' AND nab.DATE_TRANSACT BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
	GROUP BY LOCATION_CODe 
) data_restan WHERE data_restan.RESTAN <> 0";
		 

        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
    }
	
	//generate_lhm_nab Modified by Asep, 20130819
	function generate_afkir_xls($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
		$query ="SELECT ba.NO_BA, bafd.TANGGAL_PANEN, bafd.BLOCK, bafd.JANJANG, bafd.DESCRIPTION AS KETERANGAN FROM s_ba_afkir ba
INNER JOIN s_ba_afkir_detail bafd ON ba.ID_BA = bafd.ID_BA
WHERE ba.COMPANY_CODE = '".$company."' AND bafd.TANGGAL_PANEN BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
AND bafd.ACTIVE=1 AND bafd.`STATUS`=1
ORDER BY bafd.TANGGAL_PANEN, bafd.BLOCK";
		
        $sQuery = $this->db->query($query);
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }

        $this->db->close();
        return $temp_result;
    }
	
	function generate_afkir($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
         $query="SELECT ba.NO_BA, bafd.TANGGAL_PANEN, bafd.BLOCK, bafd.JANJANG, CASE WHEN ba.STATUS=0 THEN 'WAITING APPROVAL' ELSE 'APPROVED' END AS APPROVED, bafd.DESCRIPTION AS KETERANGAN FROM s_ba_afkir ba
INNER JOIN s_ba_afkir_detail bafd ON ba.ID_BA = bafd.ID_BA
WHERE ba.COMPANY_CODE = '".$company."' AND bafd.TANGGAL_PANEN BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
ORDER BY bafd.TANGGAL_PANEN, bafd.BLOCK";

        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
    }
	
    function generate_adem_tbsin($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
         $query="SELECT TANGGALM,'Gd TBS $company' AS Locator , JENIS_MUATAN AS Product, 0-BERAT_BERSIH AS Quantity_Use, 
                     NO_TIKET , NO_KENDARAAN, NO_SPB, '$company-Site' AS OrgKey 
                     From s_data_timbangan 
                     WHERE COMPANY_CODE='".$company."' AND JENIS_MUATAN='TBS' AND TYPE_BUAH=1 
                     AND DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
                                ORDER BY TANGGALM ASC";
	*/
	 $query="SELECT * FROM (SELECT CASE WHEN TIME(WAKTUK)> '06:59:59' THEN DATE(TANGGALK) ELSE  (DATE(TANGGALK) - INTERVAL 1 DAY) END AS TANGGALM,'Gd TBS $company' AS Locator , JENIS_MUATAN AS Product, 0-BERAT_BERSIH AS Quantity_Use, 
                     NO_TIKET , NO_KENDARAAN, NO_SPB, '$company-Site' AS OrgKey 
                     From s_data_timbangan 
                     WHERE COMPANY_CODE='".$company."' AND JENIS_MUATAN='TBS' AND TYPE_BUAH=1 
                     ORDER BY TANGGALM ASC) tbs_in
		WHERE  DATE_FORMAT(tbs_in.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
    
    function generate_adem_tbsout($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
         $query="SELECT TANGGALM,'Gd TBS $company' AS Locator , JENIS_MUATAN AS Product, BERAT_BERSIH AS Quantity_Use, 
                     CONCAT(NO_TIKET,'_o') AS NO_TIKET , NO_KENDARAAN, NO_SPB, '$company-Site' AS OrgKey  
                     From s_data_timbangan 
                     WHERE COMPANY_CODE='".$company."' AND JENIS_MUATAN='TBS' AND TYPE_BUAH=1 
                     AND DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
                                ORDER BY TANGGALM ASC";
	 */
	$query="SELECT * FROM (SELECT CASE WHEN TIME(WAKTUK)> '06:59:59' THEN DATE(TANGGALK) ELSE  (DATE(TANGGALK) - INTERVAL 1 DAY) END AS TANGGALM,'Gd TBS $company' AS Locator , JENIS_MUATAN AS Product, BERAT_BERSIH AS Quantity_Use, 
                     CONCAT(NO_TIKET,'_o') AS NO_TIKET , NO_KENDARAAN, NO_SPB, '$company-Site' AS OrgKey  
                     From s_data_timbangan 
                     WHERE COMPANY_CODE='".$company."' AND JENIS_MUATAN='TBS' AND TYPE_BUAH=1 
                     ORDER BY TANGGALM ASC) tbs_out
		WHERE  DATE_FORMAT(tbs_out.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
    
    function generate_adem_tbsplasma($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
         $query="SELECT '$company-Site' AS locator,a.NO_TIKET,'MM Receipt' AS ship,a.TANGGALM,'PO-NUM-PLACE-HERE' as SO_NUMBER,
                    'TBS-Plasma' as JENIS_MUATAN, a.BERAT_BERSIH, a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=3 and DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
                                ORDER BY TANGGALM ASC";
	*/
	$query="SELECT * FROM (SELECT '$company-Site' AS locator,a.NO_TIKET,'MM Receipt' AS ship,CASE WHEN TIME(a.WAKTUK)> '06:59:59' THEN DATE(a.TANGGALK) ELSE  (DATE(a.TANGGALK) - INTERVAL 1 DAY) END AS TANGGALM,'PO-NUM-PLACE-HERE' as SO_NUMBER,
                    'TBS-Plasma' as JENIS_MUATAN, a.BERAT_BERSIH, a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=3
                                ORDER BY TANGGALM ASC) tbs_plasma
		WHERE  DATE_FORMAT(tbs_plasma.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
    
    function generate_adem_tbsluar($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
/*
         $query="SELECT '$company-Site' AS locator,a.NO_TIKET,'MM Receipt' AS ship,a.TANGGALM,a.PO_NUMBER as SO_NUMBER,
                    'TBS-Luar' as JENIS_MUATAN, a.BERAT_BERSIH as BERAT_BERSIH, 
                    a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse, a.SUPPLIERCODE
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=2 and DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
                                ORDER BY a.TANGGALM,a.SUPPLIERCODE ASC";
*/
	$query=" SELECT * FROM (SELECT '$company-Site' AS locator,a.NO_TIKET,'MM Receipt' AS ship, CASE WHEN TIME(a.WAKTUK)> '06:59:59' THEN DATE(a.TANGGALK) ELSE  (DATE(a.TANGGALK) - INTERVAL 1 DAY) END AS TANGGALM,a.PO_NUMBER as SO_NUMBER,
                    'TBS-Luar' as JENIS_MUATAN, a.BERAT_BERSIH as BERAT_BERSIH, 
                    a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse, a.SUPPLIERCODE
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=2 
                                ORDER BY a.TANGGALM,a.SUPPLIERCODE ASC) tbs_luar
WHERE  DATE_FORMAT(tbs_luar.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }

    function generate_adem_tbsafiliasi($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
         $query="SELECT '$company-Site' AS locator,a.ID_TIMBANGAN,'MM Receipt' AS ship,a.TANGGALM,a.PO_NUMBER as SO_NUMBER,
                    'TBS-Luar' as JENIS_MUATAN, ROUND(a.BERAT_ISI-a.BERAT_KOSONG) as BERAT_BERSIH, 
                    a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=4 and DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')
                                ORDER BY a.TANGGALM,a.SUPPLIERCODE ASC";
	*/
	$query="SELECT * FROM (SELECT '$company-Site' AS locator,a.ID_TIMBANGAN,'MM Receipt' AS ship,CASE WHEN TIME(a.WAKTUK)> '06:59:59' THEN DATE(a.TANGGALK) ELSE  (DATE(a.TANGGALK) - INTERVAL 1 DAY) END AS TANGGALM,a.PO_NUMBER as SO_NUMBER,
                    'TBS-Luar' as JENIS_MUATAN, ROUND(a.BERAT_ISI-a.BERAT_KOSONG) as BERAT_BERSIH, 
                    a.NO_TIKET as NO_TIKET1,a.NO_KENDARAAN, 'Gd TBS $company' AS warehouse
                    FROM s_data_timbangan a
                    where a.COMPANY_CODE='".$company."' and a.JENIS_MUATAN='TBS' 
                        and a.TYPE_BUAH=4 
                                ORDER BY a.TANGGALM,a.SUPPLIERCODE ASC) tbs_afiliasi
WHERE  DATE_FORMAT(tbs_afiliasi.TANGGALM,'%Y%m%d') BETWEEN 
                     DATE_FORMAT('".$periode."','%Y%m%d') AND 
                     DATE_FORMAT('".$periode_to."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
	
	function generate_produksi_gkm($periode, $periode_to, $user, $company){
		$periode= date("Y-m-d", strtotime($periode));
		$periode_to= date("Y-m-d", strtotime($periode_to));
		$p1=$periode;
		$p2=$periode_to;
		
		$query ="DELETE FROM tmp_prod_gkm WHERE BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "'";
		$this->db->reconnect();
		$this->db->query($query);
		/*	
		while (strtotime($periode) <= strtotime($periode_to)) {
			$qSP ="CALL sp_prod_gkm_group(?, ?)";		
			$this->db->reconnect();		
			$periode2 = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
			//var_dump($qSP.' '.$periode.' '.$periode2.' '.$user.' '.$company);
			$this->db->query($qSP,array($periode, $periode2));
			//var_dump($sukses);
			$periode = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
		}
		*/
		
		$qRendemen ="SELECT TAHUN, BULAN, SUM(SSS_CPO)/100 AS SSS_CPO, SUM(SSS_KERNEL)/100  AS SSS_KERNEL, SUM(SML_CPO)/100  AS SML_CPO, SUM(SML_KERNEL)/100  AS SML_KERNEL FROM
(	
	SELECT TAHUN, BULAN, RENDEMEN AS SSS_CPO, '' AS SSS_KERNEL, '' AS SML_CPO, '' AS SML_KERNEL
	FROM s_rendemen 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SSS' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'CPO'
	UNION
	SELECT TAHUN, BULAN, '' AS SSS_CPO, RENDEMEN AS SSS_KERNEL, '' AS SML_CPO, '' AS SML_KERNEL
	FROM s_rendemen 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SSS' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'KERNEL'
	UNION	
	SELECT TAHUN, BULAN, '' AS SSS_CPO, '' AS SSS_KERNEL, RENDEMEN AS SML_CPO, '' AS SML_KERNEL
	FROM s_rendemen 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SML' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'CPO'
	UNION
	SELECT TAHUN, BULAN, '' AS SSS_CPO, '' AS SSS_KERNEL, '' AS SML_CPO, RENDEMEN AS SML_KERNEL
	FROM s_rendemen 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SML' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'KERNEL'
) data_rendemen
	GROUP BY data_rendemen.TAHUN, data_rendemen.BULAN";
		$this->db->reconnect();
		$sQuery=$this->db->query($qRendemen);
		
		$sss_cpo = 0;   
		$sml_cpo = 0; 
		$sss_kernel = 0;
		$sml_kernel = 0; 
		if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
            $sss_cpo = $row->SSS_CPO;   
			$sml_cpo = $row->SML_CPO; 
			$sss_kernel = $row->SSS_KERNEL;
			$sml_kernel = $row->SML_KERNEL; 
        }
			
		while (strtotime($periode) <= strtotime($periode_to)) {
			$qSP ="CALL sp_prod_gkm_group(?, ?, ?, ?, ?, ?)";		
			$this->db->reconnect();		
			$periode2 = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
			//var_dump($qSP.' '.$periode.' '.$periode2.' '.$sss_cpo.' '.$sml_cpo.' '.$sss_kernel.' '.$sml_kernel);
			$this->db->query($qSP,array($periode, $periode2, $sss_cpo, $sml_cpo, $sss_kernel, $sml_kernel));
			//var_dump($sukses);
			$periode = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
		}

		$query2 ="DELETE FROM tmp_prod_gkm_fixed WHERE BA_DATE BETWEEN '". $p1. "' AND '". $p2. "'";
		$this->db->reconnect();
		$this->db->query($query2);
		$qSP2 ="CALL sp_prod_gkm_group_fixed(?, ?)";
		//var_dump($qSP2 .' '.$p1.' '.$p2);	
		$this->db->reconnect();	
		$this->db->query($qSP2,array($p1, $p2));
	}
	
function generate_produksi_gkm_smi($periode, $periode_to, $user, $company){
		$periode= date("Y-m-d", strtotime($periode));
		$periode_to= date("Y-m-d", strtotime($periode_to));
		$p1=$periode;
		$p2=$periode_to;
		
		$query ="DELETE FROM tmp_prod_gkm_smi WHERE BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "'";
		$this->db->reconnect();
		$this->db->query($query);
				
		$qRendemen ="SELECT TAHUN, BULAN, SUM(SML_CPO)/100  AS SML_CPO, SUM(SML_KERNEL)/100  AS SML_KERNEL FROM
(	
	
	SELECT TAHUN, BULAN, RENDEMEN AS SML_CPO, '' AS SML_KERNEL
	FROM s_rendemen_smi 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SML' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'CPO'
	UNION
	SELECT TAHUN, BULAN, '' AS SML_CPO, RENDEMEN AS SML_KERNEL
	FROM s_rendemen_smi 
	WHERE ACTIVE =1 AND COMPANY_CODE = 'SML' 
	AND TAHUN= DATE_FORMAT('". $periode. "','%Y') AND BULAN= DATE_FORMAT('". $periode. "','%m') 
	AND ID_KOMODITAS = 'KERNEL'
) data_rendemen
	GROUP BY data_rendemen.TAHUN, data_rendemen.BULAN";
		$this->db->reconnect();
		$sQuery=$this->db->query($qRendemen);
		
		$sss_cpo = 0;   
		$sml_cpo = 0; 
		$sss_kernel = 0;
		$sml_kernel = 0; 
		if($sQuery->num_rows() > 0){
            $row = $sQuery->row();            
 
			$sml_cpo = $row->SML_CPO; 
			$sml_kernel = $row->SML_KERNEL; 
        }
			
		while (strtotime($periode) <= strtotime($periode_to)) {
			$qSP ="CALL sp_prod_gkm_group_smi(?, ?, ?, ?)";		
			$this->db->reconnect();		
			$periode2 = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
			//var_dump($qSP.' '.$periode.' '.$periode2.' '.$sml_cpo.' '.$sml_kernel);
			$this->db->query($qSP,array($periode, $periode2, $sml_cpo, $sml_kernel));
			//var_dump($sukses);
			$periode = date ("Y-m-d", strtotime("+1 day", strtotime($periode)));
		}

		$query2 ="DELETE FROM tmp_prod_gkm_fixed_smi WHERE BA_DATE BETWEEN '". $p1. "' AND '". $p2. "'";
		$this->db->reconnect();
		$this->db->query($query2);
		$qSP2 ="CALL sp_prod_gkm_group_fixed_smi(?, ?)";
		//var_dump($qSP2 .' '.$p1.' '.$p2);	
		$this->db->reconnect();	
		$this->db->query($qSP2,array($p1, $p2));
	}

    function generate_nab_null($periode,$periode_to, $user, $company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
	  $user = $this->db->escape_str($user);
        $company = $this->db->escape_str($company);
        
	  $query="SELECT  distinct(nab.NO_SPB), nab.TANGGAL, nab.COMPANY_CODE FROM s_nota_angkutbuah nab
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		where DATE_FORMAT(nab.TANGGAL,'%Y%m') = DATE_FORMAT('".$periode."','%Y%m') AND nabd.TONASE =0";
			
		$this->db->reconnect();
		$sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }


    function generate_adem_produksi($periode,$periode_to, $user, $company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
		$user = $this->db->escape_str($user);
        $company = $this->db->escape_str($company);
        
		if($company=='GKM' || $company=='SML' || $company=='SSS'){
			$this->generate_produksi_gkm($periode, $periode_to, $user, $company);
		}
		
		if($company=='GKM'){
			$query="SELECT BA_DATE, 'Gd CPO $company' AS Locator, 'CPO' AS Product,  
CASE WHEN COALESCE(CPO_GKM,0) = 0 THEN 0
ELSE 
(CPO_GKM)*-1
END  AS CPO_GKM
,'$company-Site'  
FROM tmp_prod_gkm_fixed
WHERE CPO_GKM<>0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
UNION 
SELECT ADJUST_DATE AS BA_DATE,  'Gd CPO $company' AS Locator, s_komoditas.JENIS AS Product, WEIGHT*-1 AS CPO_GKM, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'CPO' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."' 
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d');
";
			/*
			$query="SELECT prod_cpo.STRG_STOCK_DATE, 'Gd CPO $company' AS Locator, 'CPO' AS Product, 
CASE WHEN COALESCE(((prod_cpo.WEIGHT_GKM+prod_cpo.BERAT_BERSIH_GKM)-WEIGHT_GKM_0),0) = 0 THEN 0
ELSE 
((prod_cpo.WEIGHT_GKM+prod_cpo.BERAT_BERSIH_GKM)-WEIGHT_GKM_0)*-1
END  AS CPO_GKM,'GKM-Site'    
FROM(
	SELECT ba.STRG_STOCK_DATE, 
	round(sum(ba.WEIGHT)*0.24) AS WEIGHT_SML, 
	ba_0.WEIGHT_SML_0,
	COALESCE(BERAT_BERSIH_SML,0) AS BERAT_BERSIH_SML,
	round(sum(ba.WEIGHT)*0.24) AS WEIGHT_SSS, 
	ba_0.WEIGHT_SSS_0,
	COALESCE(BERAT_BERSIH_SSS,0) AS BERAT_BERSIH_SSS, 
	sum(ba.WEIGHT)-(round(sum(ba.WEIGHT)*0.24)+round(sum(ba.WEIGHT)*0.24)) AS WEIGHT_GKM, 
	ba_0.WEIGHT_GKM_0,
	COALESCE(BERAT_BERSIH_GKM,0) AS BERAT_BERSIH_GKM  
	FROM s_ba_storage_stock ba
	INNER JOIN (SELECT * FROM m_storage WHERE COMPANY_CODE = 'GKM') m ON m.ID_STORAGE = ba.ID_STORAGE
	LEFT JOIN (
		SELECT DATE(DATE_FORMAT(ba.STRG_STOCK_DATE + INTERVAL 1 DAY,'%Y%m%d')) AS STRG_STOCK_DATE_0, round(sum(ba.WEIGHT)*0.24) AS WEIGHT_SML_0,
		round(sum(ba.WEIGHT)*0.24) AS WEIGHT_SSS_0, 
		sum(ba.WEIGHT)-(round(sum(ba.WEIGHT)*0.24)+round(sum(ba.WEIGHT)*0.24)) AS WEIGHT_GKM_0
		FROM s_ba_storage_stock ba
		INNER JOIN (SELECT * FROM m_storage WHERE COMPANY_CODE = 'GKM') m ON m.ID_STORAGE = ba.ID_STORAGE
		WHERE ba.COMPANY_CODE = 'GKM' AND ba.STRG_STOCK_DATE BETWEEN DATE_FORMAT('".$periode."' - INTERVAL 1 DAY,'%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
		AND m.PRODUCT_CODE='CPO'
		GROUP BY ba.STRG_STOCK_DATE
	) ba_0 ON ba.STRG_STOCK_DATE = ba_0.STRG_STOCK_DATE_0
	LEFT JOIN(
		SELECT dispatch_all.TANGGALM, COALESCE(dispatch_sml.BERAT_BERSIH,0) AS BERAT_BERSIH_SML, 
		COALESCE(dispatch_sss.BERAT_BERSIH,0) AS BERAT_BERSIH_SSS, COALESCE(dispatch_gkm.BERAT_BERSIH,0) AS BERAT_BERSIH_GKM   
		FROM(
			SELECT DISTINCT(d.TANGGALM) AS TANGGALM
			FROM s_dispatch d
			WHERE d.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			AND d.COMPANY_CODE IN ('SML', 'SSS', 'GKM') AND d.ID_KOMODITAS ='KOGKM0002'
			GROUP BY d.TANGGALM
		) dispatch_all
		LEFT JOIN (
			SELECT d.TANGGALM, SUM(d.BERAT_BERSIH) AS BERAT_BERSIH
			FROM s_dispatch d
			WHERE d.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			AND d.COMPANY_CODE IN ('SML') AND d.ID_KOMODITAS ='KOGKM0002'
			GROUP BY d.TANGGALM
		) dispatch_sml ON dispatch_all.TANGGALM = dispatch_sml.TANGGALM
		LEFT JOIN (
			SELECT d.TANGGALM, SUM(d.BERAT_BERSIH) AS BERAT_BERSIH
			FROM s_dispatch d
			WHERE d.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			AND d.COMPANY_CODE IN ('SSS') AND d.ID_KOMODITAS ='KOGKM0002'
			GROUP BY d.TANGGALM
		) dispatch_sss ON dispatch_all.TANGGALM = dispatch_sss.TANGGALM
		LEFT JOIN(
			SELECT d.TANGGALM, SUM(d.BERAT_BERSIH) AS BERAT_BERSIH
			FROM s_dispatch d
			WHERE d.TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			AND d.COMPANY_CODE IN ('GKM') AND d.ID_KOMODITAS ='KOGKM0002'
			GROUP BY d.TANGGALM
		) dispatch_gkm ON dispatch_all.TANGGALM = dispatch_gkm.TANGGALM
	) dispatch ON ba.STRG_STOCK_DATE = dispatch.TANGGALM
	WHERE ba.COMPANY_CODE = 'GKM' AND ba.STRG_STOCK_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
	AND m.PRODUCT_CODE='CPO'
	GROUP BY ba.STRG_STOCK_DATE
) prod_cpo
;";
*/
		}elseif ($company=='SML'){
			$query="SELECT BA_DATE, 'Gd CPO $company' AS Locator, 'CPO' AS Product,  
CASE WHEN COALESCE(CPO_SML,0) = 0 THEN 0
ELSE 
(CPO_SML)*-1
END  AS CPO_SML
,'$company-Site'  
FROM tmp_prod_gkm_fixed
WHERE CPO_SML<>0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
UNION 
SELECT ADJUST_DATE AS BA_DATE,  'Gd CPO $company' AS Locator, s_komoditas.JENIS AS Product, WEIGHT*-1 AS CPO_SML, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'CPO' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."' 
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d');
";			
		}elseif ($company=='SSS'){
			$query="SELECT BA_DATE, 'Gd CPO $company' AS Locator, 'CPO' AS Product,  
CASE WHEN COALESCE(CPO_SSS,0) = 0 THEN 0
ELSE 
(CPO_SSS)*-1
END  AS CPO_SSS
,'$company-Site'  
FROM tmp_prod_gkm_fixed
WHERE CPO_SSS<>0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
UNION 
SELECT ADJUST_DATE AS BA_DATE,  'Gd CPO $company' AS Locator, s_komoditas.JENIS AS Product, WEIGHT*-1 AS CPO_SSS, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'CPO' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."' 
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
;";	
		}else{
			$query="SELECT ba.BA_DATE, 'Gd CPO $company' AS Locator, 'CPO' AS Product, 
ROUND(0-(WEIGHT),-1) AS PROD_CPO,
ba.ID_BA,'','','','$company-Site'
FROM s_ba ba
LEFT JOIN (
	SELECT ID_BA, JENIS, WEIGHT FROM s_ba_production bap
	LEFT JOIN  s_komoditas k ON bap.ID_COMMODITY = k.ID_KOMODITAS 
	WHERE k.COMPANY_CODE='".$company."' AND bap.COMPANY_CODE ='".$company."' AND bap.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
	AND k.JENIS = 'CPO'
) bap ON ba.ID_BA = bap.ID_BA
WHERE ba.COMPANY_CODE = '".$company."' AND ba.BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
AND ba.ACTIVE = 1 AND ba.`STATUS`=1;";
			/*
			$query="SELECT s_data_timbangan.TANGGALM AS TANGGAL, 'Gd CPO $company' AS Locator,'CPO' AS Product,        
								ROUND(CASE WHEN COALESCE(VOL_H,0) > 0 THEN    
									CASE WHEN COALESCE(VOL_MIN,0) = 0 THEN
									(
										0-COALESCE((COALESCE(VOL_H,0)+COALESCE(VOL_DISPATCH,0))-
										COALESCE((SELECT SUM(snd.WEIGHT) as WEIGHT
											FROM s_sounding snd
											WHERE snd.COMPANY_CODE='".$company."' and DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)-- and snd.ACTIVE =1
												FROM s_sounding snd
												WHERE snd.DATE < s_data_timbangan.TANGGALM and snd.COMPANY_CODE='".$company."' ),'%Y%m%d')GROUP BY snd.DATE),0),0)
									)
									WHEN COALESCE(VOL_MIN,0) > 0 THEN  
										0-COALESCE((COALESCE(VOL_H,0)+COALESCE(VOL_DISPATCH,0))-COALESCE(VOL_MIN,0),0)
									END
								WHEN COALESCE(VOL_H,0) = 0 THEN '0'
								END,-1) AS  PROD_CPO ,
								ID_SOUNDING,'','','','$company-Site'
					FROM s_data_timbangan
					LEFT JOIN(
									SELECT SUM(snd.WEIGHT)AS VOL_MIN,snd.COMPANY_CODE,`DATE` 
									FROM s_sounding snd
									WHERE snd.TYPE_S='1' AND snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' 
									   GROUP BY snd.DATE
								)sounding_min ON sounding_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
									AND DATE(DATE_FORMAT(sounding_min.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
					LEFT JOIN(
									SELECT SUM(snd.WEIGHT)AS VOL_H,snd.COMPANY_CODE,`DATE`,ID_SOUNDING
									FROM s_sounding snd  
									WHERE snd.TYPE_S='1' AND snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' 
									   GROUP BY snd.DATE
								)sounding_h ON sounding_h.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
									AND DATE(DATE_FORMAT(sounding_h.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
					LEFT JOIN(
									SELECT coalesce(sum(BERAT_BERSIH),0) AS VOL_DISPATCH,COMPANY_CODE,TANGGALM
									FROM s_dispatch
									WHERE s_dispatch.ACTIVE='1' AND s_dispatch.COMPANY_CODE='".$company."' and 
										s_dispatch.ID_KOMODITAS = (
											select ID_KOMODITAS from s_komoditas
											where s_komoditas.COMPANY_CODE = '".$company."'
												and s_komoditas.JENIS like 'CP%'
										)
									group by s_dispatch.TANGGALM
								)dispatch ON dispatch.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
									AND DATE(DATE_FORMAT(dispatch.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))    
					WHERE s_data_timbangan.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d') BETWEEN 
											DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
								AND s_data_timbangan.TYPE_BUAH=1 AND s_data_timbangan.TYPE_TIMBANG=1
								AND s_data_timbangan.JENIS_MUATAN='TBS'
					GROUP BY s_data_timbangan.TANGGALM, s_data_timbangan.COMPANY_CODE";
			*/
		}
		//var_dump($query);
		$this->db->reconnect();
		$sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
    
    function generate_adem_produksi_kernel($periode,$periode_to, $user, $company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		
		if($company=='GKM' || $company=='SML' || $company=='SSS'){
			$this->generate_produksi_gkm($periode, $periode_to, $user, $company);
		}
        
		if($company=='GKM'){
			$query="SELECT BA_DATE, 'Gd PK $company' AS Locator, 'PK' AS Product,  
			CASE WHEN COALESCE(KERNEL_GKM,0) = 0 THEN 0
			ELSE 
			(KERNEL_GKM)*-1
			END  AS KERNEL_GKM
			,'$company-Site'  
			FROM tmp_prod_gkm_fixed
			WHERE KERNEL_GKM <> 0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			UNION
			SELECT ADJUST_DATE AS BA_DATE,  'Gd PK $company' AS Locator, 'PK' AS Product, WEIGHT*-1 AS KERNEL_GKM, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'KERNEL' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."'
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d');";
		}elseif ($company=='SML'){
			$query="SELECT BA_DATE, 'Gd PK $company' AS Locator, 'PK' AS Product,  
			CASE WHEN COALESCE(KERNEL_SML,0) = 0 THEN 0
			ELSE 
			(KERNEL_SML)*-1
			END  AS KERNEL_SML
			,'$company-Site'  
			FROM tmp_prod_gkm_fixed
			WHERE KERNEL_SML <> 0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			UNION
			SELECT ADJUST_DATE AS BA_DATE,  'Gd PK $company' AS Locator, 'PK' AS Product, WEIGHT*-1 AS KERNEL_SML, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'KERNEL' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."'
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d');";			
		}elseif ($company=='SSS'){
			$query="SELECT BA_DATE, 'Gd PK $company' AS Locator, 'PK' AS Product,  
			CASE WHEN COALESCE(KERNEL_SSS,0) = 0 THEN 0
			ELSE 
			(KERNEL_SSS)*-1
			END  AS KERNEL_SSS
			,'$company-Site'  
			FROM tmp_prod_gkm_fixed
			WHERE KERNEL_SSS <> 0 AND BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
			UNION
			SELECT ADJUST_DATE AS BA_DATE,  'Gd PK $company' AS Locator, 'PK' AS Product, WEIGHT*-1 AS KERNEL_SSS, '$company-Site'
FROM s_adjustment_titip_olah
INNER JOIN s_komoditas ON s_adjustment_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS 
AND s_adjustment_titip_olah.COMPANY_CODE = s_komoditas.COMPANY_CODE 
WHERE s_komoditas.JENIS = 'KERNEL' AND s_adjustment_titip_olah.COMPANY_CODE ='".$company."'
AND s_adjustment_titip_olah.ADJUST_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
;";	
		}else{
			$query="SELECT ba.BA_DATE, 'Gd KERNEL $company' AS Locator, 'KERNEL' AS Product, 
ROUND(0-(WEIGHT),-1) AS PROD_KERNEL,
ba.ID_BA,'','','','$company-Site'
FROM s_ba ba
LEFT JOIN (
	SELECT ID_BA, JENIS, WEIGHT FROM s_ba_production bap
	LEFT JOIN  s_komoditas k ON bap.ID_COMMODITY = k.ID_KOMODITAS 
	WHERE k.COMPANY_CODE='".$company."' AND bap.COMPANY_CODE ='".$company."' AND bap.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
	AND k.JENIS = 'KERNEL'
) bap ON ba.ID_BA = bap.ID_BA
WHERE ba.COMPANY_CODE = '".$company."' AND ba.BA_DATE BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
AND ba.ACTIVE = 1 AND ba.`STATUS`=1;";
			/*
			$query="SELECT s_data_timbangan.TANGGALM AS TANGGAL, 'Gd PK $company' AS Locator,'PK' AS Product,        
								ROUND(0-((COALESCE(VOL_KERNEL,0) - COALESCE(VOL_KERNEL_MIN,0))+COALESCE(VOL_DISPATCH_KERNEL,0)),-1) AS PROD_KERNEL,
								ID_SOUNDING_KERNEL,'','','','$company-Site'
					FROM s_data_timbangan
					LEFT JOIN(
									SELECT coalesce(sum(BERAT_BERSIH),0) AS VOL_DISPATCH,COMPANY_CODE,TANGGALM
									FROM s_dispatch
									WHERE s_dispatch.ACTIVE='1' AND s_dispatch.COMPANY_CODE='".$company."' and 
										s_dispatch.ID_KOMODITAS = (
											select ID_KOMODITAS from s_komoditas
											where s_komoditas.COMPANY_CODE = '".$company."'
												and s_komoditas.JENIS like 'CP%'
										)
									group by s_dispatch.TANGGALM
								)dispatch ON dispatch.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
									AND DATE(DATE_FORMAT(dispatch.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))    
					LEFT JOIN(
							 SELECT SUM(sndk.WEIGHT)AS VOL_KERNEL, sndk.COMPANY_CODE, sndk.DATE, ID_SOUNDING_KERNEL,
								case when coalesce(VOL_KERNEL_MIN,0) = 0 then
									(
										SELECT SUM(sndk2.WEIGHT)AS VOL_KERNEL_MIN 
										 FROM s_sounding_kernel sndk2  
										 WHERE sndk2.ACTIVE=1 and sndk2.COMPANY_CODE='".$company."'
											and sndk2.DATE = (
												select max(DATE) from s_sounding_kernel
												where ACTIVE=1 AND s_sounding_kernel.COMPANY_CODE='".$company."' 
													and DATE_FORMAT(date,'%Y%m%d') < DATE_FORMAT(sndk.DATE,'%Y%m%d')
											) and sndk2.COMPANY_CODE = sndk.COMPANY_CODE
											GROUP BY sndk2.DATE
									)
								when COALESCE(VOL_KERNEL_MIN,0) > 0 then VOL_KERNEL_MIN    
								end as VOL_KERNEL_MIN,VOL_DISPATCH_KERNEL
							 FROM s_sounding_kernel sndk   
							 left join(
								SELECT SUM(sndk1.WEIGHT)AS VOL_KERNEL_MIN, sndk1.COMPANY_CODE, sndk1.DATE 
								 FROM s_sounding_kernel sndk1  
								 WHERE sndk1.ACTIVE=1 AND sndk1.COMPANY_CODE='".$company."' 
									GROUP BY sndk1.DATE
							 )sndk_min on sndk_min.COMPANY_CODE = sndk.COMPANY_CODE
								AND DATE(DATE_FORMAT(sndk_min.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(sndk.DATE,'%Y%m%d'))-1
							 LEFT JOIN(
									SELECT COALESCE(sum(BERAT_BERSIH),0) AS VOL_DISPATCH_KERNEL,COMPANY_CODE,TANGGALM
									FROM s_dispatch
									WHERE s_dispatch.ACTIVE='1' AND s_dispatch.COMPANY_CODE='".$company."' AND 
										s_dispatch.ID_KOMODITAS = (
											SELECT ID_KOMODITAS FROM s_komoditas
											WHERE s_komoditas.COMPANY_CODE = '".$company."'
												AND s_komoditas.JENIS LIKE 'KER%'
										)
									GROUP BY s_dispatch.TANGGALM
							 )dispatch_kernel ON dispatch_kernel.COMPANY_CODE = sndk.COMPANY_CODE
									AND DATE(DATE_FORMAT(dispatch_kernel.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(sndk.DATE,'%Y%m%d'))
							 WHERE sndk.ACTIVE=1 AND sndk.COMPANY_CODE='".$company."' 
								GROUP BY sndk.DATE
						)produksi_kernel ON produksi_kernel.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
						AND DATE(DATE_FORMAT(produksi_kernel.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
					WHERE s_data_timbangan.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d') BETWEEN 
											DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
								 AND s_data_timbangan.TYPE_BUAH=1 AND s_data_timbangan.TYPE_TIMBANG=1
								 AND s_data_timbangan.JENIS_MUATAN='TBS'
					GROUP BY s_data_timbangan.TANGGALM, s_data_timbangan.COMPANY_CODE";
			*/
		}
		//var_dump($query);
		$this->db->reconnect();
        $sQuery = $this->db->query($query);
        
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = "";
        return $this->dbutil->csv_from_result($sQuery, $delimiter, $newline,$enclosure); 
          
    }
    
    function cek_data_exist($tableName,$where_condition,$select_condition){
        $this->db->select($select_condition);
        $this->db->from($tableName);
        $this->db->where($where_condition);
        
        $sQuery = $this->db->get();
        $count = $sQuery->num_rows();
           
        return $count;
    }

	//## Create report: Laporan - Produksi Kebun(panen) ##	
	/*
    function generate_lhm_nab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $month_start = date("Ymd", strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'));
        $month_end = date("Ymd", strtotime('-1 second',strtotime('+1 month',strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'))));
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
        //$cek_data_exist = $this->cek_data_exist('m_gang_activity_detail',array('COMPANY_CODE'=>$company,'LHM_DATE'=>$periode),'ID');
        //if ($cek_data_exist <= 0){
            //$qSP ="CALL sp_tbg_gen_produksi_tbs_hist(?,?,?,?)";
        //}else
        //{
            if($company=='GKM' || $company=='SML'){
                    $qSP ="CALL sp_tbg_gen_produksi_tbs_gkmgroup(?,?,?,?)";
            }else{
                    $qSP ="CALL sp_tbg_gen_produksi_tbs(?,?,?,?)";    
            }  
        //}

        $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }else{
            $sQuery->free_result();
            $this->db->close(); 
            
            $qSP ="CALL sp_tbg_gen_produksi_tbs_hist(?,?,?,?)";
            $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
            
            $numrows = $sQuery->num_rows();
            if ($numrows > 0){
                $temp = $sQuery->row_array();
                $temp_result = array(); 
                foreach ( $sQuery->result_array() as $row )
                {
                    $temp_result [] = $row;
                    
                }
            }
                
        }

        $this->db->close();
        return $temp_result;
    }
	*/
	
    function runjob_nab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $month_start = date("Ymd", strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'));
        $month_end = date("Ymd", strtotime('-1 second',strtotime('+1 month',strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'))));
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
       	if($company=='GKM' || $company=='SML'){
        	$qSP ="CALL sp_tbg_gen_produksi_tbs_gkmgroup_new(?,?,?,?)";
			
        }else{
        	$qSP ="CALL sp_tbg_gen_produksi_tbs_new(?,?,?,?)";    
       }  	
	 $this->db->reconnect();	
        $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
		        
        $numrows = $sQuery->num_rows();

        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }else{
            $sQuery->free_result();
            $this->db->close(); 
            
            $qSP ="CALL sp_tbg_gen_produksi_tbs_hist(?,?,?,?)";
	     $this->db->reconnect();
            $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
            
            $numrows = $sQuery->num_rows();
            if ($numrows > 0){
                $temp = $sQuery->row_array();
                $temp_result = array(); 
                foreach ( $sQuery->result_array() as $row )
                {
                    $temp_result [] = $row;
                    
                }
            }
                
        }

        $this->db->close();
        return $temp_result;
    }
	
	function generate_closing($periode_from, $periode_to){
        $periode_from = $this->db->escape_str($periode_from);
		$periode_to = $this->db->escape_str($periode_to);
		$status = FALSE;
		/*
		$query ="UPDATE m_periode_control SET ISCLOSE=1, CLOSE_BY = 'JOB_SCHEDULLER', CLOSE_DATE =now()
				WHERE PERIODE_CONTROL_ID IN
				(
				SELECT PERIODE_CONTROL_ID FROM(
					SELECT 
					DATE(DATE_FORMAT(m_periode_control.PERIODE_END + INTERVAL 3 DAY,'%Y%m%d'))  AS TANGGAL,
					ISCLOSE, m_periode_control.PERIODE_CONTROL_ID
					FROM m_periode_control
					WHERE MODULE ='NAB'
				) periode_kontrol
				WHERE TANGGAL <= '". $periode_to. "'
				)";
		*/
		$query ="UPDATE m_periode_control SET ISCLOSE=1, CLOSE_BY = 'JOB_SCHEDULLER', CLOSE_DATE =now()
				WHERE PERIODE_CONTROL_ID IN
				(
				SELECT PERIODE_CONTROL_ID FROM(
					SELECT 
					DATE(DATE_FORMAT(m_periode_control.PERIODE_END + INTERVAL 3 DAY,'%Y%m%d'))  AS TANGGAL,
					ISCLOSE, m_periode_control.PERIODE_CONTROL_ID,
					COALESCE((to_days('". $periode_to. "') - to_days(m_periode_control.REOPEN_DATE)),2) AS DEF
					FROM m_periode_control
					WHERE MODULE ='NAB'
				) periode_kontrol
				WHERE TANGGAL <= '". $periode_to. "' AND DEF >=2
				)";

		$sQuery = $this->db->query($query);
		
		if($sQuery == FALSE){
			$status = $this->db->_error_message();
			$status=FALSE;
		}else{
			$status=TRUE;   
		}
        return $status;
    }
	function delete_rpt_nab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$status = FALSE;
		
		$sCount = $this->db->query("SELECT COUNT(rpt_nab.TRANSACT_ID) AS JML FROM rpt_nab WHERE DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "' AND COMPANY_CODE ='". $company ."'");	
		$row = $sCount->row();
		$jml = $row->JML;
		if ($jml>0){
			$query ="DELETE FROM rpt_nab 
					WHERE DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
					AND COMPANY_CODE ='". $company ."'";
			$this->db->reconnect();
			$this->db->query($query);	
			
			//asep
			$sCheckCount = $this->db->query("SELECT COUNT(rpt_nab.TRANSACT_ID) AS JML FROM rpt_nab WHERE DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "' AND COMPANY_CODE ='". $company ."'");		
			$checkRow = $sCheckCount->row();
			$checkJml = $checkRow->JML;
			if ($checkJml>0){
				$status=FALSE;
			}else{
				$status=TRUE;   
			}
		}else{
			$status=TRUE;	
		}
    	
        return $status;
    }
	
	function delete_progress($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$status = FALSE;
		
		$sCount = $this->db->query("SELECT COUNT(p_progress.TGL_PROGRESS) AS JML FROM p_progress 
	where company_code = '". $company ."'
	and tgl_progress BETWEEN '". $periode. "' AND '". $periode_to. "'
	and activity_code = '8601003'");	
		$row = $sCount->row();
		$jml = $row->JML;
		if ($jml>0){			
			$query ="DELETE FROM p_progress 
	where company_code = '". $company ."'
	and tgl_progress BETWEEN '". $periode. "' AND '". $periode_to. "'
	and activity_code = '8601003'";
			$this->db->reconnect();
			$this->db->query($query);	
			
			//asep
			$sCheckCount = $this->db->query("SELECT COUNT(p_progress.TGL_PROGRESS) AS JML FROM p_progress 
			where company_code = '". $company ."'
			and tgl_progress BETWEEN '". $periode. "' AND '". $periode_to. "'
			and activity_code = '8601003'");	
			$checkRow = $sCheckCount->row();
			$checkJml = $checkRow->JML;
			if ($checkJml>0){
				$status=FALSE;	
			}else{
				$status=TRUE;   
			}
		}else{
			$status=TRUE;	
		}
        return $status;
    }

function delete_progress_gkm($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$status = FALSE;
		$db_other = $this->load->database('lhm_gkm', TRUE); 
		
		$db_other->reconnect();
		$sCount = $db_other->query("SELECT COUNT(p_progress.TGL_PROGRESS) AS JML FROM p_progress 
	where company_code = '". $company ."'
	and tgl_progress BETWEEN '". $periode. "' AND '". $periode_to. "'
	and activity_code = '8601003'");	
		$row = $sCount->row();
		$jml = $row->JML;
		if ($jml>0){			
			$query ="DELETE FROM p_progress 
	where company_code = '". $company ."'
	and tgl_progress BETWEEN '". $periode. "' AND '". $periode_to. "'
	and activity_code = '8601003'";
			$db_other->query($query);
			if($db_other->trans_status() == FALSE){
				$status = $this->db->_error_message();
				$status=FALSE;
			}else{
				$status=TRUE;   
			}
		}else{
			$status=TRUE;	
		}

        return $status;
    }
	
	function generate_monitor_tonase($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		
		$date = date('Y-m-d', strtotime($periode)); 
		$ar = preg_split('/[- :]/',trim(date('Y-m-d', strtotime($periode_to))));
        $ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;
		
		$temp_result [] = null;
        if($company=='SML'){
	$query="SELECT * 
	FROM(
			SELECT T.TANGGAL, SUM(T.BERAT_BERSIH) AS TONASE_TIMBANG, COMPANY_CODE FROM(	
				SELECT 
					CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
					CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
						DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
					ELSE
						TANGGALK
					END AS TANGGAL, 
					BERAT_BERSIH,
					COMPANY_CODE				
				FROM s_data_timbangan 			
				WHERE COMPANY_CODE = '". $company ."'			
				AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
				AND JENIS_MUATAN= 'TBS'
				AND TYPE_BUAH IN (4)
			) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
			GROUP BY T.TANGGAL
			ORDER BY T.TANGGAL
		) data_timbangan 
	LEFT JOIN (
		SELECT r.DATE_TRANSACT AS TANGGAL_NAB, sum(r.BERAT_ANGKUT) AS TONASE_NAB FROM rpt_nab r
		WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
		GROUP BY r.DATE_TRANSACT
		ORDER BY r.DATE_TRANSACT
	) data_nab ON data_timbangan.TANGGAL = data_nab.TANGGAL_NAB
	LEFT JOIN (
		SELECT p.TGL_PROGRESS AS TANGGAL_PROGRESS, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS FROM dummy_pprogress_gkm p
		WHERE  p.TGL_PROGRESS between '". $periode. "' AND '". $periode_to. "' AND p.COMPANY_CODE = '". $company ."'
		AND p.ACTIVITY_CODE='8601003'
		GROUP BY p.TGL_PROGRESS
		ORDER BY p.TGL_PROGRESS
	) data_progress ON data_timbangan.TANGGAL = data_progress.TANGGAL_PROGRESS 
	LEFT JOIN (
		SELECT ba.BA_DATE AS TANGGAL_BA, SUM(ba.FFB_INTI+ ba.FFB_PLASMA ) AS TONASE_BA FROM s_ba ba
		WHERE  ba.BA_DATE between '". $periode. "' AND '". $periode_to. "' AND ba.COMPANY_CODE = '". $company ."'
		AND ba.ACTIVE = 1
		GROUP BY ba.BA_DATE
		ORDER BY ba.BA_DATE
	) data_ba ON data_timbangan.TANGGAL = data_ba.TANGGAL_BA 
	";		
		}else if($company=='GKM'){
	$query="SELECT * 
	FROM(
			SELECT T.TANGGAL, SUM(T.BERAT_BERSIH) AS TONASE_TIMBANG, COMPANY_CODE FROM(	
				SELECT 
					CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
					CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
						DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
					ELSE
						TANGGALK
					END AS TANGGAL, 
					BERAT_BERSIH,
					COMPANY_CODE				
				FROM s_data_timbangan 			
				WHERE COMPANY_CODE = '". $company ."'			
				AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
				AND JENIS_MUATAN= 'TBS'
				AND TYPE_BUAH IN (1,3)
			) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
			GROUP BY T.TANGGAL
			ORDER BY T.TANGGAL
		) data_timbangan 
	LEFT JOIN (
		SELECT r.DATE_TRANSACT AS TANGGAL_NAB, sum(r.BERAT_ANGKUT) AS TONASE_NAB FROM rpt_nab r
		WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
		GROUP BY r.DATE_TRANSACT
		ORDER BY r.DATE_TRANSACT
	) data_nab ON data_timbangan.TANGGAL = data_nab.TANGGAL_NAB
	LEFT JOIN (
		SELECT p.TGL_PROGRESS AS TANGGAL_PROGRESS, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS FROM dummy_pprogress_gkm p
		WHERE  p.TGL_PROGRESS between '". $periode. "' AND '". $periode_to. "' AND p.COMPANY_CODE = '". $company ."'
		AND p.ACTIVITY_CODE='8601003'
		GROUP BY p.TGL_PROGRESS
		ORDER BY p.TGL_PROGRESS
	) data_progress ON data_timbangan.TANGGAL = data_progress.TANGGAL_PROGRESS 
	LEFT JOIN (
		SELECT ba.BA_DATE AS TANGGAL_BA, SUM(ba.FFB_INTI+ ba.FFB_PLASMA ) AS TONASE_BA FROM s_ba ba
		WHERE  ba.BA_DATE between '". $periode. "' AND '". $periode_to. "' AND ba.COMPANY_CODE = '". $company ."'
		AND ba.ACTIVE = 1
		GROUP BY ba.BA_DATE
		ORDER BY ba.BA_DATE
	) data_ba ON data_timbangan.TANGGAL = data_ba.TANGGAL_BA 
	";		
		}else if($company=='SSS'){
			$query ="SELECT tonase.TANGGAL, '' AS TANGGAL_BA, '' AS TANGGAL_PROGRESS, '' AS TANGGAL_NAB, SUM(tonase.TONASE_TIMBANG) AS TONASE_TIMBANG, SUM(tonase.TONASE_NAB) AS TONASE_NAB, 
SUM(TONASE_PROGRESS) AS TONASE_PROGRESS, SUM(tonase.TONASE_BA) AS TONASE_BA, tonase.COMPANY_CODE
FROM( 
	SELECT T.TANGGAL, SUM(T.BERAT_ISI-T.BERAT_KOSONG) AS TONASE_TIMBANG, '' AS TONASE_NAB, '' AS TONASE_PROGRESS, '' AS TONASE_BA, COMPANY_CODE 
	FROM
		( SELECT CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU, 
			CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') 
			THEN DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d')) ELSE TANGGALK END AS TANGGAL, 
			BERAT_BERSIH, BERAT_ISI, BERAT_KOSONG, COMPANY_CODE 
			FROM s_data_timbangan 
			WHERE COMPANY_CODE = '". $company ."' AND TANGGALK BETWEEN '".$date."' AND '".$next_day."' 
			AND JENIS_MUATAN= 'TBS' AND TYPE_BUAH IN (4) 
		) T 
	WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59' 
	GROUP BY T.TANGGAL 
	UNION
	SELECT r.DATE_TRANSACT AS TANGGAL, '' AS TONASE_TIMBANG, sum(r.BERAT_ANGKUT) AS TONASE_NAB, '' AS TONASE_PROGRESS, '' AS TONASE_BA, COMPANY_CODE
	FROM rpt_nab r WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '".$date."' AND '".$next_day."'
	GROUP BY r.DATE_TRANSACT 
	UNION
	SELECT p.TGL_PROGRESS AS TANGGAL, '' AS TONASE_TIMBANG, '' AS TONASE_NAB, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS, '' AS TONASE_BA, COMPANY_CODE
	FROM p_progress p WHERE p.TGL_PROGRESS between '".$date."' AND '".$next_day."' AND p.COMPANY_CODE = '". $company ."' 
	AND p.ACTIVITY_CODE='8601003' GROUP BY p.TGL_PROGRESS
	UNION
	
		SELECT BA.TANGGAL, '' AS TONASE_TIMBANG, '' AS TONASE_NAB, '' AS TONASE_PROGRESS, SUM(BA.TONASE_BA) AS TONASE_BA, COMPANY_CODE FROM (
			SELECT LHM_DATE AS TANGGAL, SUM(COALESCE(HSL_KERJA_VOLUME,0)) * bjr_panen.VALUE AS TONASE_BA, SUM(COALESCE(HSL_KERJA_VOLUME,0)),bjr_panen.VALUE, mgad.COMPANY_CODE  				
								FROM m_gang_activity_detail mgad
								LEFT JOIN (
										SELECT DISTINCT(bj.BLOCK) AS BLOCK, bj.VALUE
									FROM(
											SELECT AFD,BLOCK,VALUE,
														CONCAT(TAHUN,BULAN) AS PERIODE,
														COMPANY_CODE 
											FROM s_data_bjr 
											WHERE COMPANY_CODE='". $company ."' 
									)bj
									JOIN (
											SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
											FROM s_data_bjr
											WHERE COMPANY_CODE='". $company ."' AND CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$date."','%Y%m') AND ACTIVE=1
											GROUP BY BLOCK 
									) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK 
													AND bjr.MAX_PERIODE = bj.PERIODE
					ORDER BY bj.AFD ASC, bj.BLOCK ASC
								) bjr_panen ON bjr_panen.BLOCK = mgad.LOCATION_CODE
								WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$date."' ,'%Y%m%d') AND DATE_FORMAT('".$next_day."' ,'%Y%m%d')
								AND mgad.COMPANY_CODE = '". $company ."'
								AND ACTIVITY_CODE ='8601003'
								GROUP BY LOCATION_CODE, LHM_DATE 
			) BA
			GROUP BY BA.TANGGAL
)tonase
GROUP BY tonase.TANGGAL";
			/*
			$query="SELECT * 
FROM(
		SELECT T.TANGGAL, SUM(T.BERAT_BERSIH) AS TONASE_TIMBANG, COMPANY_CODE FROM(	
			SELECT 
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				COMPANY_CODE				
			FROM s_data_timbangan 			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			AND TYPE_BUAH IN (4)
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.TANGGAL
		ORDER BY T.TANGGAL
	) data_timbangan 
LEFT JOIN (
	SELECT r.DATE_TRANSACT AS TANGGAL_NAB, sum(r.BERAT_ANGKUT) AS TONASE_NAB FROM rpt_nab r
	WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
	GROUP BY r.DATE_TRANSACT
	ORDER BY r.DATE_TRANSACT
) data_nab ON data_timbangan.TANGGAL = data_nab.TANGGAL_NAB
LEFT JOIN (
	SELECT p.TGL_PROGRESS AS TANGGAL_PROGRESS, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS FROM p_progress p
	WHERE  p.TGL_PROGRESS between '". $periode. "' AND '". $periode_to. "' AND p.COMPANY_CODE = '". $company ."'
	AND p.ACTIVITY_CODE='8601003'
	GROUP BY p.TGL_PROGRESS
	ORDER BY p.TGL_PROGRESS
) data_progress ON data_timbangan.TANGGAL = data_progress.TANGGAL_PROGRESS 
LEFT JOIN (
	SELECT ba.BA_DATE AS TANGGAL_BA, SUM(ba.FFB_INTI+ ba.FFB_PLASMA ) AS TONASE_BA FROM s_ba ba
	WHERE  ba.BA_DATE between '". $periode. "' AND '". $periode_to. "' AND ba.COMPANY_CODE = '". $company ."'
	AND ba.ACTIVE = 1
	GROUP BY ba.BA_DATE
	ORDER BY ba.BA_DATE
) data_ba ON data_timbangan.TANGGAL = data_ba.TANGGAL_BA";
*/
		// asep
}else if($company=='MSS' || $company=='ASL'){
			
$query="SELECT * 
FROM(
		SELECT T.TANGGAL, SUM(T.BERAT_BERSIH) AS TONASE_TIMBANG, COMPANY_CODE FROM(	
			SELECT 
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				COMPANY_CODE				
			FROM s_data_timbangan_kebun 			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			AND TYPE_BUAH IN (1,3)
			AND NO_SPB NOT LIKE '%PKS%' AND NO_SPB NOT LIKE '%PABRIK%'	
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.TANGGAL
		ORDER BY T.TANGGAL
	) data_timbangan 
LEFT JOIN (
	SELECT r.DATE_TRANSACT AS TANGGAL_NAB, sum(r.BERAT_ANGKUT) AS TONASE_NAB FROM rpt_nab r
	WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
	GROUP BY r.DATE_TRANSACT
	ORDER BY r.DATE_TRANSACT
) data_nab ON data_timbangan.TANGGAL = data_nab.TANGGAL_NAB
LEFT JOIN (
	SELECT p.TGL_PROGRESS AS TANGGAL_PROGRESS, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS FROM p_progress p
	WHERE  p.TGL_PROGRESS between '". $periode. "' AND '". $periode_to. "' AND p.COMPANY_CODE = '". $company ."'
	AND p.ACTIVITY_CODE='8601003'
	GROUP BY p.TGL_PROGRESS
	ORDER BY p.TGL_PROGRESS
) data_progress ON data_timbangan.TANGGAL = data_progress.TANGGAL_PROGRESS 
LEFT JOIN (
	SELECT ba.BA_DATE AS TANGGAL_BA, SUM(ba.FFB_INTI+ ba.FFB_PLASMA ) AS TONASE_BA FROM s_ba ba
	WHERE  ba.BA_DATE between '". $periode. "' AND '". $periode_to. "' AND ba.COMPANY_CODE = '". $company ."'
	AND ba.ACTIVE = 1
	GROUP BY ba.BA_DATE
	ORDER BY ba.BA_DATE
) data_ba ON data_timbangan.TANGGAL = data_ba.TANGGAL_BA";
//var_dump($query);
		// asep
		}else{
		$query="SELECT * 
FROM(
		SELECT T.TANGGAL, SUM(T.BERAT_BERSIH) AS TONASE_TIMBANG, COMPANY_CODE FROM(	
			SELECT 
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				COMPANY_CODE				
			FROM s_data_timbangan 			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			AND TYPE_BUAH IN (1,3)
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.TANGGAL
		ORDER BY T.TANGGAL
	) data_timbangan 
LEFT JOIN (
	SELECT r.DATE_TRANSACT AS TANGGAL_NAB, sum(r.BERAT_ANGKUT) AS TONASE_NAB FROM rpt_nab r
	WHERE r.COMPANY_CODE = '". $company ."' AND r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
	GROUP BY r.DATE_TRANSACT
	ORDER BY r.DATE_TRANSACT
) data_nab ON data_timbangan.TANGGAL = data_nab.TANGGAL_NAB
LEFT JOIN (
	SELECT p.TGL_PROGRESS AS TANGGAL_PROGRESS, SUM(p.HASIL_KERJA) AS TONASE_PROGRESS FROM p_progress p
	WHERE  p.TGL_PROGRESS between '". $periode. "' AND '". $periode_to. "' AND p.COMPANY_CODE = '". $company ."'
	AND p.ACTIVITY_CODE='8601003'
	GROUP BY p.TGL_PROGRESS
	ORDER BY p.TGL_PROGRESS
) data_progress ON data_timbangan.TANGGAL = data_progress.TANGGAL_PROGRESS 
LEFT JOIN (
	SELECT ba.BA_DATE AS TANGGAL_BA, SUM(ba.FFB_INTI+ ba.FFB_PLASMA ) AS TONASE_BA FROM s_ba ba
	WHERE  ba.BA_DATE between '". $periode. "' AND '". $periode_to. "' AND ba.COMPANY_CODE = '". $company ."'
	AND ba.ACTIVE = 1
	GROUP BY ba.BA_DATE
	ORDER BY ba.BA_DATE
) data_ba ON data_timbangan.TANGGAL = data_ba.TANGGAL_BA 
";
//var_dump($query);
}
	
        $sQuery = $this->db->query($query);
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }

        $this->db->close();
        return $temp_result;
    }
	
	function generate_tonase_pernab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
		
		$date = date('Y-m-d', strtotime($periode)); 
		$ar = preg_split('/[- :]/',trim(date('Y-m-d', strtotime($periode_to))));
        $ar = implode('',$ar);
		$next_day = strtotime('1 day',strtotime($ar)); 
		$next_day= date('Ymd', $next_day);

		$m='';
		$y='';
		$d='';
		$d=date("d",strtotime($next_day));
		$m=date("m",strtotime($next_day));
		$y=date("Y",strtotime($next_day));
		$next_day= $y."-".$m."-".$d;
		
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
	if($company=='SML' || $company=='ASL' || $company=='SSS'){
			$query="SELECT NO_SPB, TANGGAL, SUM(TONASE_TIMBANG) AS TONASE_TIMBANG, SUM(TONASE_NAB) AS TONASE_NAB, COMPANY_CODE
FROM(
		SELECT T.NO_SPB, T.TANGGAL, SUM(T.BERAT_ISI-T.BERAT_KOSONG) AS TONASE_TIMBANG, 0 AS TONASE_NAB, COMPANY_CODE 
		FROM(	
			SELECT NO_SPB,
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				BERAT_ISI,
				BERAT_KOSONG,
				COMPANY_CODE				
			FROM s_data_timbangan 			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			-- AND TYPE_BUAH IN (4)
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.NO_SPB
		UNION
		SELECT  nab.NO_SPB,
		nab.TANGGAL AS TANGGAL, 0 AS TONASE_TIMBANG, ROUND(SUM(nabd.TONASE)) AS TONASE_NAB, nab.COMPANY_CODE
		FROM s_nota_angkutbuah nab
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		WHERE nab.COMPANY_CODE='". $company ."'
		AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('". $periode. "' ,'%Y%m%d') AND DATE_FORMAT('". $periode_to. "' ,'%Y%m%d')
		GROUP BY nab.NO_SPB
		ORDER BY NO_SPB,TANGGAL ASC
) data_tonase 
GROUP BY NO_SPB, TANGGAL ORDER BY NO_SPB ASC;
";	
		}
		else if($company=='MSS'){
			$query="SELECT NO_SPB, TANGGAL, SUM(TONASE_TIMBANG) AS TONASE_TIMBANG, SUM(TONASE_NAB) AS TONASE_NAB, COMPANY_CODE
FROM(
		SELECT T.NO_SPB, T.TANGGAL, SUM(T.BERAT_ISI-T.BERAT_KOSONG) AS TONASE_TIMBANG, 0 AS TONASE_NAB, COMPANY_CODE 
		FROM(	
			SELECT NO_SPB,
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				BERAT_ISI,
				BERAT_KOSONG,
				COMPANY_CODE				
			FROM s_data_timbangan_kebun			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			AND NO_SPB NOT LIKE '%PKS%' AND NO_SPB NOT LIKE '%PABRIK%'
			-- AND TYPE_BUAH IN (4)
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.NO_SPB
		UNION
		SELECT  nab.NO_SPB,
		nab.TANGGAL AS TANGGAL, 0 AS TONASE_TIMBANG, ROUND(SUM(nabd.TONASE)) AS TONASE_NAB, nab.COMPANY_CODE
		FROM s_nota_angkutbuah nab
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		WHERE nab.COMPANY_CODE='". $company ."'
		AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('". $periode. "' ,'%Y%m%d') AND DATE_FORMAT('". $periode_to. "' ,'%Y%m%d')
		AND NO_SPB NOT LIKE '%PKS%' AND NO_SPB NOT LIKE '%PABRIK%'
		GROUP BY nab.NO_SPB
		ORDER BY NO_SPB,TANGGAL ASC
) data_tonase 
GROUP BY NO_SPB, TANGGAL ORDER BY NO_SPB ASC;
";
}
else{
		$query="SELECT NO_SPB, TANGGAL, SUM(TONASE_TIMBANG) AS TONASE_TIMBANG, SUM(TONASE_NAB) AS TONASE_NAB, COMPANY_CODE
FROM(
		SELECT T.NO_SPB, T.TANGGAL, SUM(T.BERAT_ISI-T.BERAT_KOSONG) AS TONASE_TIMBANG, 0 AS TONASE_NAB, COMPANY_CODE 
		FROM(	
			SELECT NO_SPB,
				CAST(STR_TO_DATE(CONCAT(TANGGALK, WAKTUK), '%Y-%m-%d %H:%i:%s') AS DATETIME) AS TANGGALWAKTU,
				CASE WHEN (WAKTUK >= '00:00:00' AND WAKTUK <= '06:59:59') THEN
					DATE(DATE_FORMAT(TANGGALK - INTERVAL 1 DAY,'%Y%m%d'))
				ELSE
					TANGGALK
				END AS TANGGAL, 
				BERAT_BERSIH,
				BERAT_ISI,
				BERAT_KOSONG,
				COMPANY_CODE				
			FROM s_data_timbangan 			
			WHERE COMPANY_CODE = '". $company ."'			
			AND TANGGALK BETWEEN '".$date."' AND '".$next_day."'
			AND JENIS_MUATAN= 'TBS'
			AND TYPE_BUAH IN (1,3)
		) T WHERE T.TANGGALWAKTU BETWEEN '".$date." 07:00' AND '".$next_day." 06:59'
		GROUP BY T.NO_SPB
		UNION
		SELECT  nab.NO_SPB,
		nab.TANGGAL AS TANGGAL, 0 AS TONASE_TIMBANG, ROUND(SUM(nabd.TONASE)) AS TONASE_NAB, nab.COMPANY_CODE
		FROM s_nota_angkutbuah nab
		INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
		WHERE nab.COMPANY_CODE='". $company ."'
		AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('". $periode. "' ,'%Y%m%d') AND DATE_FORMAT('". $periode_to. "' ,'%Y%m%d')
		GROUP BY nab.NO_SPB
		ORDER BY NO_SPB,TANGGAL ASC
) data_tonase 
GROUP BY NO_SPB, TANGGAL ORDER BY NO_SPB ASC;
";
}
        $sQuery = $this->db->query($query);
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }

        $this->db->close();
        return $temp_result;
    }
	//generate_lhm_nab Modified by Asep, 20130819
	function generate_lhm_nab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
		$query ="SELECT * FROM rpt_nab r 
				WHERE r.DATE_TRANSACT BETWEEN '". $periode. "' AND '". $periode_to. "'
				AND r.COMPANY_CODE ='". $company ."'
				ORDER BY r.DATE_TRANSACT, r.LOCATION_CODE";
		
        $sQuery = $this->db->query($query);
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }

        $this->db->close();
        return $temp_result;
    }
	
	function saldo_awal($periode,$company){
        $periode = $this->db->escape_str($periode);
		$periode=date("Ym",strtotime($periode));
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        
		$query ="SELECT DATE(DATE_FORMAT(TANGGAL - INTERVAL 1 DAY,'%Y%m%d')) AS TANGGAL, BLOCK, RESTAN  FROM s_restan_block
WHERE COMPANY_CODE = '". $company ."' AND DATE_FORMAT(TANGGAL, '%Y%m')='". $periode. "'";
		
        $sQuery = $this->db->query($query);
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row ){
                $temp_result [] = $row;                
            }
        }else{
			$temp_result = NULL ;	
		}

        $this->db->close();
        return $temp_result;
    }
	//## generate_titip_olah
	function generate_titip_olah($periode,$periode_to,$company, $user){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$user = $this->db->escape_str($user);
		$temp_result [] = null;
		
		if($company=='GKM' || $company=='SML' || $company=='SSS'){
			$this->generate_produksi_gkm($periode, $periode_to, $user, $company);
		
        		/*
			$query ="SELECT * FROM tmp_prod_gkm_fixed r 
					WHERE r.BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "' ORDER BY r.BA_DATE ";
			*/

			$query ="SELECT tmp_prod_gkm_fixed.*,  COALESCE(WRITE_OFF.SLUDGE, 0) AS SLUDGE FROM tmp_prod_gkm_fixed 
LEFT JOIN (SELECT ADJUST_DATE, SLUDGE FROM s_adjustment WHERE COMPANY_CODE = 'GKM' AND  ADJUST_DATE BETWEEN '". $periode. "' AND '20141231' AND STATUS=1) WRITE_OFF  
		ON tmp_prod_gkm_fixed.BA_DATE = WRITE_OFF.ADJUST_DATE
WHERE BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "' ORDER BY BA_DATE ";
			
			$sQuery = $this->db->query($query);
			
			$numrows = $sQuery->num_rows();
			if ($numrows > 0){
				$temp = $sQuery->row_array();
				$temp_result = array(); 
				foreach ( $sQuery->result_array() as $row )
				{
					$temp_result [] = $row;
					
				}
			}
	
			$this->db->close();
		}
        return $temp_result;
    }
	
//## generate_titip_olah
	function generate_titip_olah_smi($periode,$periode_to,$company, $user){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$user = $this->db->escape_str($user);
		$temp_result [] = null;
		
		if($company=='SML' || $company=='SMI'){
			$this->generate_produksi_gkm_smi($periode, $periode_to, $user, $company);
		
        		/*
			$query ="SELECT * FROM tmp_prod_gkm_fixed r 
					WHERE r.BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "' ORDER BY r.BA_DATE ";
			*/

			$query ="SELECT tmp_prod_gkm_fixed_smi.*,  COALESCE(WRITE_OFF.SLUDGE, 0) AS SLUDGE FROM tmp_prod_gkm_fixed_smi 
LEFT JOIN (SELECT ADJUST_DATE, SLUDGE FROM s_adjustment WHERE COMPANY_CODE = 'GKM' AND  ADJUST_DATE BETWEEN '". $periode. "' AND '20141231' AND STATUS=1) WRITE_OFF  
		ON tmp_prod_gkm_fixed_smi.BA_DATE = WRITE_OFF.ADJUST_DATE
WHERE BA_DATE BETWEEN '". $periode. "' AND '". $periode_to. "' ORDER BY BA_DATE ";
			
			$sQuery = $this->db->query($query);
			
			$numrows = $sQuery->num_rows();
			if ($numrows > 0){
				$temp = $sQuery->row_array();
				$temp_result = array(); 
				foreach ( $sQuery->result_array() as $row )
				{
					$temp_result [] = $row;
					
				}
			}
	
			$this->db->close();
		}
        return $temp_result;
    }

	//## Create report: Summary Produksi Kebun(panen) ##
    function generate_sum_lhm_nab($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $month_start = date("Ymd", strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'));
        $month_end = date("Ymd", strtotime('-1 second',strtotime('+1 month',strtotime(date('m',strtotime("$periode")).'/01/'.date('Y').' 00:00:00'))));
        $company = $this->db->escape_str($company);
		$temp_result [] = null;
        

            if($company == 'GKM' || $company == 'SML'){
                    $qSP ="CALL sp_tbg_gen_sum_produksi_tbs_gkmgroup(?,?,?,?)";
            }else{
                    $qSP ="CALL sp_tbg_gen_sum_produksi_tbs(?,?,?,?)";    
            }  

        $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
        
        $numrows = $sQuery->num_rows();
        if ($numrows > 0){
            $temp = $sQuery->row_array();
            $temp_result = array(); 
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result [] = $row;
                
            }
        }else{
            $sQuery->free_result();
            $this->db->close(); 
            
            $qSP ="CALL sp_tbg_gen_produksi_tbs_hist(?,?,?,?)";
            $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$month_start));
            
            $numrows = $sQuery->num_rows();
            if ($numrows > 0){
                $temp = $sQuery->row_array();
                $temp_result = array(); 
                foreach ( $sQuery->result_array() as $row )
                {
                    $temp_result [] = $row;
                    
                }
            }
                
        }

        $this->db->close();
        return $temp_result;
    }
    
    function generate_lhm_bjr($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		
		$query ="SELECT nab.DATE_TRANSACT, nab.LOCATION_CODE, nab.JANJANG_PANEN, nab.BERAT_PANEN, nab.JANJANG_ANGKUT, nab.BERAT_ANGKUT, ROUND(nab.BJR_REAL,2) AS BJR_REAL
FROM rpt_nab nab
WHERE nab.COMPANY_CODE = '".$company."' AND nab.DATE_TRANSACT  BETWEEN  DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
ORDER BY nab.DATE_TRANSACT, nab.LOCATION_CODE";
        
		/*
        $query="SELECT data_panen_lhm.TANGGAL_PANEN, data_nab_tbg.TANGGAL_TIMBANG, 
                                data_nab_tbg.TANGGAL_PANEN as TANGGAL_PANEN_NAB,
                                data_panen_lhm.LOCATION_CODE, data_panen_lhm.JANJANG_PANEN,
                                COALESCE(data_nab_tbg.JJG_ANGKUT_NAB,0) AS JJG_ANGKUT_NAB, COALESCE(data_nab_tbg.JJG_ANGKUT_TBG,0) AS JJG_ANGKUT_TBG,
                              CASE WHEN data_panen_lhm.JANJANG_PANEN = data_nab_tbg.JJG_ANGKUT_NAB
                                     THEN BJR_REAL
                              WHEN data_panen_lhm.JANJANG_PANEN != data_nab_tbg.JJG_ANGKUT_NAB
                                     THEN 0
                                END AS BJRREAL,
                                COALESCE(data_nab_tbg.BJR_REAL,0) AS BJR_REAL,
                                COALESCE(data_nab_tbg.BERAT_EMPIRIS,0) AS BERAT_EMPIRIS, COALESCE(data_nab_tbg.BERAT_REAL,0) AS BERAT_REAL,
                                data_nab_tbg.NO_SPB as SPB_NAB, data_nab_tbg.NO_SPB as SPB_TBG
                FROM(
                      SELECT LHM_DATE AS TANGGAL_PANEN,LOCATION_CODE,ACTIVITY_CODE,SUM(HSL_KERJA_VOLUME)AS JANJANG_PANEN
                      FROM m_gang_activity_detail mgad
                      WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
                              AND mgad.COMPANY_CODE='".$company."' 
                              AND ACTIVITY_CODE ='8601003'
                      GROUP BY LOCATION_CODE, LHM_DATE -- ORDER BY LHM_DATE ASC, LOCATION_CODE asc
                )data_panen_lhm 
                LEFT JOIN(
                            SELECT data_tbg.BLOCK, data_tbg.TANGGALM as TANGGAL_TIMBANG ,data_tbg.TANGGAL_PANEN,
                                    COALESCE(sum(data_tbg.BERAT_EMPIRIS),0) as BERAT_EMPIRIS,
                                    COALESCE(sum(data_tbg.BERAT_REAL),0) as BERAT_REAL,sum(data_tbg.JANJANG) as JJG_ANGKUT_TBG,
                                    COALESCE((sum(data_tbg.BERAT_REAL)/sum(data_tbg.JANJANG)),0) AS BJR_REAL ,
                                    data_tbg.NO_SPB, data_tbg.NO_TIKET, sum(data_tbg.JANJANG) as JJG_ANGKUT_NAB
                            FROM(
                                        SELECT tbgd.BLOCK,tbg.TANGGALM,
                                                    tbgd.BERAT_EMPIRIS,
                                                    tbgd.BERAT_REAL,tbgd.JANJANG,
                                                    tbg.NO_SPB,tbg.NO_TIKET, tbgd.TANGGAL_PANEN
                                        FROM s_data_timbangan tbg 
                                        LEFT JOIN s_data_timbangan_detail tbgd ON tbgd.ID_TIMBANGAN = tbg.ID_TIMBANGAN
                                        WHERE DATE_FORMAT(tbg.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
                                                        AND tbg.COMPANY_CODE='".$company."'
                                                        AND tbg.TYPE_BUAH =1 AND tbg.TYPE_TIMBANG =1
                                        ORDER BY NO_SPB, TANGGALM asc, BLOCK ASC
                                                                ) data_tbg
                            GROUP BY data_tbg.BLOCK, data_tbg.TANGGAL_PANEN -- data_tbg.TANGGALM 
                            -- ORDER BY data_tbg.TANGGALM ASC, data_tbg.BLOCK ASC
        )data_nab_tbg ON TRIM(UPPER(data_nab_tbg.BLOCK)) = TRIM(UPPER(data_panen_lhm.LOCATION_CODE)) 
                and DATE_FORMAT(data_nab_tbg.TANGGAL_PANEN,'%Y%m%d') = DATE_FORMAT(data_panen_lhm.TANGGAL_PANEN,'%Y%m%d') 
                ORDER BY data_panen_lhm.TANGGAL_PANEN ASC,data_nab_tbg.BLOCK ASC";
	*/
                /*"SELECT data_panen_lhm.TANGGAL_PANEN, data_nab_tbg.TANGGAL_TIMBANG, 
                                data_nab_tbg.TANGGAL_PANEN as TANGGAL_PANEN_NAB,
                                data_panen_lhm.LOCATION_CODE, data_panen_lhm.JANJANG_PANEN,
                                COALESCE(data_nab_tbg.JJG_ANGKUT_NAB,0) AS JJG_ANGKUT_NAB, COALESCE(data_nab_tbg.JJG_ANGKUT_TBG,0) AS JJG_ANGKUT_TBG,
                              CASE WHEN data_panen_lhm.JANJANG_PANEN = data_nab_tbg.JJG_ANGKUT_NAB
                                     THEN BJR_REAL
                              WHEN data_panen_lhm.JANJANG_PANEN != data_nab_tbg.JJG_ANGKUT_NAB
                                     THEN 0
                                END AS BJRREAL,
                                COALESCE(data_nab_tbg.BJR_REAL,0) AS BJR_REAL,
                                COALESCE(data_nab_tbg.BERAT_EMPIRIS,0) AS BERAT_EMPIRIS, COALESCE(data_nab_tbg.BERAT_REAL,0) AS BERAT_REAL,
                                data_nab_tbg.NO_SPB as SPB_NAB, data_nab_tbg.NO_SPB as SPB_TBG
                FROM(
                      SELECT LHM_DATE AS TANGGAL_PANEN,LOCATION_CODE,ACTIVITY_CODE,SUM(HSL_KERJA_VOLUME)AS JANJANG_PANEN
                      FROM m_gang_activity_detail mgad
                      WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d') 
                              AND mgad.COMPANY_CODE='".$company."' 
                              AND ACTIVITY_CODE ='8601003'
                      GROUP BY LOCATION_CODE, LHM_DATE ORDER BY LHM_DATE ASC, LOCATION_CODE asc
                )data_panen_lhm 
                LEFT JOIN(
                            SELECT data_tbg.BLOCK, data_tbg.TANGGALM as TANGGAL_TIMBANG ,data_nab.TANGGAL_PANEN,
                                    COALESCE(sum(data_tbg.BERAT_EMPIRIS),0) as BERAT_EMPIRIS,
                                    COALESCE(sum(data_tbg.BERAT_REAL),0) as BERAT_REAL,sum(data_tbg.JANJANG) as JJG_ANGKUT_TBG,
                                    COALESCE((sum(data_tbg.BERAT_REAL)/sum(data_tbg.JANJANG)),0) AS BJR_REAL ,
                                    data_tbg.NO_SPB, data_tbg.NO_TIKET, sum(data_nab.JANJANG) as JJG_ANGKUT_NAB
                            FROM(
                                        SELECT tbgd.BLOCK,tbg.TANGGALM,
                                                    tbgd.BERAT_EMPIRIS,
                                                    tbgd.BERAT_REAL,tbgd.JANJANG,
                                                    tbg.NO_SPB,tbg.NO_TIKET
                                        FROM s_data_timbangan tbg
                                        LEFT JOIN s_data_timbangan_detail tbgd ON tbgd.ID_TIMBANGAN = tbg.ID_TIMBANGAN
                                        WHERE DATE_FORMAT(tbg.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
                                                        AND tbg.COMPANY_CODE='".$company."'
                                                        AND tbg.TYPE_BUAH =1 AND tbg.TYPE_TIMBANG =1
                                        ORDER BY NO_SPB, TANGGALM asc, BLOCK ASC) data_tbg
                            INNER JOIN (
                                                SELECT nab.NO_TIKET,nab.NO_SPB,nabd.TANGGAL_PANEN,nabd.BLOCK,nabd.JANJANG
                                                FROM s_nota_angkutbuah nab
                                                INNER JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB 
                                                WHERE nab.COMPANY_CODE = '".$company."' and nab.ACTIVE = 1 -- and nab.ID_NT_AB='MIANAB1107060009'
                                                         AND DATE_FORMAT(nabd.TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d')  
                                                                                AND DATE_FORMAT('".$periode_to."','%Y%m%d')
                                                ORDER BY nab.NO_TIKET ASC
                            )data_nab on data_nab.NO_TIKET = data_tbg.NO_TIKET and data_nab.NO_SPB=data_tbg.NO_SPB 
                                                and data_nab.TANGGAL_PANEN = data_tbg.TANGGALM
                                                and data_nab.BLOCK = data_tbg.BLOCK
                                            GROUP BY data_tbg.BLOCK, data_tbg.TANGGALM ORDER BY data_tbg.TANGGALM ASC, data_tbg.NO_TIKET ASC, data_tbg.BLOCK ASC
        )data_nab_tbg ON data_nab_tbg.BLOCK = data_panen_lhm.LOCATION_CODE and data_nab_tbg.TANGGAL_PANEN = data_panen_lhm.TANGGAL_PANEN";*/
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
	
    function generate_lhm_tbg($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
        $query="select a.ID_TIMBANGAN, a.TANGGALM, a.NO_TIKET, a.NO_SPB, a.NO_KENDARAAN, a.BERAT_BERSIH, b.AFD, b.BLOCK ,  b.JANJANG, b.BERAT_EMPIRIS, b.BERAT_REAL
                from s_data_timbangan a
                    inner join s_data_timbangan_detail b on b.ID_TIMBANGAN = a.ID_TIMBANGAN
                where DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode_to."','%Y%m%d')
                               AND a.COMPANY_CODE='".$company."'
                               AND a.TYPE_TIMBANG =1 -- AND a.TYPE_BUAH =1 
                order by ID_TIMBANGAN asc ";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function get_jjg_angkut($periode,$company){
        $periode = $this->db->escape_str($periode);
        $company = $this->db->escape_str($company);
        
        $query = "SELECT nab.NO_TIKET,nab.NO_SPB,nabd.TANGGAL_PANEN,nabd.BLOCK , sum(nabd.JANJANG) as JJG_ANGKUT
                    FROM s_nota_angkutbuah nab
                         INNER JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB 
                          WHERE nab.COMPANY_CODE = '".$company."' and nab.ACTIVE = 1 and 
                          DATE_FORMAT(nab.TANGGAL,'%Y%m%d') = DATE_FORMAT('".$periode."','%Y%m%d') and
                          DATE_FORMAT(nabd.TANGGAL_PANEN,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d')
                    group by nabd.BLOCK
                    order by nabd.BLOCK asc";
                    
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function get_panen_breakdown($periode,$company,$location){
        $periode = $this->db->escape_str($periode);
        $company = $this->db->escape_str($company);
        $location = $this->db->escape_str($location);
        $table='';
        if ($company=='GKM' || $company=='SML'){
            $table='dummy_mgangactivitydetail_gkm';    
        }else{
            $table='m_gang_activity_detail';
        }
        $query = "SELECT LOCATION_CODE,ACTIVITY_CODE,HSL_KERJA_VOLUME,LHM_DATE,EMPLOYEE_CODE, emp.NAMA
                FROM ".$table." mgad 
                LEFT JOIN m_employee emp on emp.NIK = mgad.EMPLOYEE_CODE 
                WHERE 
                    DATE_FORMAT(LHM_DATE,'%Y%m%d')='".$periode."'
                    AND mgad.ACTIVITY_CODE= '8601003'
                    AND mgad.COMPANY_CODE='".$company."'
                    AND mgad.LOCATION_CODE ='".$location."'
                    ORDER BY mgad.LOCATION_CODE ASC";
                    
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function get_tbg_breakdown($periode,$company,$location){
        $periode = $this->db->escape_str($periode);
        $company = $this->db->escape_str($company);
        $location = $this->db->escape_str($location);
        $type_buah='';
        if ($company=='GKM' || $company=='SML'){
            $type_buah='4';    
        }else{
            $type_buah='1';
        }
        
       // $query = "SELECT tbg.TANGGALM,tbgd.TANGGAL_PANEN,tbg.NO_SPB,tbgd.AFD,tbgd.BLOCK, BERAT_ISI,BERAT_KOSONG,BERAT_BERSIH, tbgd.JANJANG,tbgd.BERAT_EMPIRIS,tbgd.BERAT_REAL,(tbgd.BERAT_REAL/tbgd.JANJANG) AS BJR_REAL FROM s_data_timbangan tbg LEFT JOIN s_data_timbangan_detail tbgd ON tbgd.ID_TIMBANGAN = tbg.ID_TIMBANGAN WHERE DATE_FORMAT(TANGGALM,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d') AND tbgd.BLOCK='".$location."' AND tbg.COMPANY_CODE ='".$company."' AND tbg.TYPE_BUAH =".$type_buah." AND tbg.TYPE_TIMBANG =1 ORDER BY tbgd.BLOCK ASC, tbg.NO_SPB ASC "; //Remarked By Asep, 20130508
	   	$query = "SELECT nab.TANGGAL AS TANGGALM, nabd.TANGGAL_PANEN, nab.NO_SPB, nabd.JANJANG AS JANJANG, '' AS BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL, '' AS BJR_REAL FROM s_nota_angkutbuah nab INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB WHERE nab.COMPANY_CODE='".$company."' AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d')=DATE_FORMAT('".$periode."','%Y%m%d') AND nabd.BLOCK='".$location."'"; //added by Asep, 20130508
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
    }
    
    function get_nab_data($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
        $query = "select a.ID_NT_AB,a.NO_TIKET,a.NO_SPB,a.NO_KENDARAAN, b.AFD,b.BLOCK,b.JANJANG,b.TANGGAL_PANEN, a.TANGGAL AS TANGGAL_ANGKUT, b.ROUND_TONASE
                    from s_nota_angkutbuah a
                    LEFT JOIN s_nota_angkutbuah_detail b
                        on b.ID_NT_AB = a.ID_NT_AB
                    where a.COMPANY_CODE='".$company."' and DATE_FORMAT(a.TANGGAL,'%Y%m%d') 
                            BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        and a.ACTIVE=1 ";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
	
    //## Create Report: Export TBG
    function get_tbg_data_kebun($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
        $query = "select a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
                    a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
			CASE WHEN a.TYPE_BUAH = 1 THEN 'INTI'
			 WHEN a.TYPE_BUAH = 2 THEN 'LUAR'
			 WHEN a.TYPE_BUAH = 3 THEN 'PLASMA'
			 WHEN a.TYPE_BUAH = 4 THEN 'GROUP'
			END AS TYPE_BUAH
                from s_data_timbangan a
                left join s_data_timbangan_detail b
                    on b.ID_TIMBANGAN = a.ID_TIMBANGAN
                where a.COMPANY_CODE = '".$company."' 
                        and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        AND a.JENIS_MUATAN='TBS' -- and a.TYPE_BUAH=1";
		*/
		if ($company=='MSS'){
$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN LEFT(b.AFD,1) = 'O' THEN 'INTI'
	WHEN LEFT(b.AFD,1) = 'P' THEN 'PLASMA'
END AS TYPE_BUAH
FROM s_data_timbangan_kebun a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' 
AND a.NO_SPB NOT LIKE '%PKS%'
ORDER BY a.TANGGALM, b.NO_SPB";
		}else{
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN a.TYPE_BUAH = 1 THEN 'INTI'
	WHEN a.TYPE_BUAH = 2 THEN 'LUAR'
	WHEN a.TYPE_BUAH = 3 THEN 'PLASMA'
	WHEN a.TYPE_BUAH = 4 THEN 'GROUP'
END AS TYPE_BUAH
FROM s_data_timbangan_kebun a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE_KEBUN AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' 
ORDER BY a.TANGGALM, b.NO_SPB";
		}
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    //## Create Report: Export TBG
    function get_tbg_data($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
        $query = "select a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
                    a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
			CASE WHEN a.TYPE_BUAH = 1 THEN 'INTI'
			 WHEN a.TYPE_BUAH = 2 THEN 'LUAR'
			 WHEN a.TYPE_BUAH = 3 THEN 'PLASMA'
			 WHEN a.TYPE_BUAH = 4 THEN 'GROUP'
			END AS TYPE_BUAH
                from s_data_timbangan a
                left join s_data_timbangan_detail b
                    on b.ID_TIMBANGAN = a.ID_TIMBANGAN
                where a.COMPANY_CODE = '".$company."' 
                        and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        AND a.JENIS_MUATAN='TBS' -- and a.TYPE_BUAH=1";
		*/
	if ($company=='ASL'){
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN LEFT(b.AFD,1) = 'O' THEN 'INTI'
	WHEN LEFT(b.AFD,1) = 'P' THEN 'PLASMA'
END AS TYPE_BUAH
FROM s_data_timbangan_kebun a
LEFT JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	-- AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' 
ORDER BY a.TANGGALM, b.NO_SPB";

	}else if ($company=='MSS'){
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN LEFT(b.AFD,1) = 'O' THEN 'INTI'
	WHEN LEFT(b.AFD,1) = 'P' THEN 'PLASMA'
END AS TYPE_BUAH
FROM s_data_timbangan_kebun a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE_KEBUN AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALK,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' AND a.NO_SPB LIKE '%PKS%'
ORDER BY a.TANGGALM, b.NO_SPB";

	}else{
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN a.TYPE_BUAH = 1 THEN 'INTI'
	WHEN a.TYPE_BUAH = 2 THEN 'LUAR'
	WHEN a.TYPE_BUAH = 3 THEN 'PLASMA'
	WHEN a.TYPE_BUAH = 4 THEN 'GROUP'
END AS TYPE_BUAH
FROM s_data_timbangan a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' 
ORDER BY a.TANGGALM, b.NO_SPB";
	}
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
//## Create Report: Export TBG
    function get_tbg_data_pks_luar($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        /*
        $query = "select a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
                    a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
			CASE WHEN a.TYPE_BUAH = 1 THEN 'INTI'
			 WHEN a.TYPE_BUAH = 2 THEN 'LUAR'
			 WHEN a.TYPE_BUAH = 3 THEN 'PLASMA'
			 WHEN a.TYPE_BUAH = 4 THEN 'GROUP'
			END AS TYPE_BUAH
                from s_data_timbangan a
                left join s_data_timbangan_detail b
                    on b.ID_TIMBANGAN = a.ID_TIMBANGAN
                where a.COMPANY_CODE = '".$company."' 
                        and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        AND a.JENIS_MUATAN='TBS' -- and a.TYPE_BUAH=1";
		*/
	if ($company=='ASL'){
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN LEFT(b.AFD,1) = 'O' THEN 'INTI'
	WHEN LEFT(b.AFD,1) = 'P' THEN 'PLASMA'
END AS TYPE_BUAH
FROM s_data_timbangan a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' 
ORDER BY a.TANGGALM, b.NO_SPB";

	}else if ($company=='MSS'){
		$query = "SELECT a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH, b.AFD,b.BLOCK,b.JANJANG,b.BERAT_EMPIRIS,b.BERAT_REAL,
CASE WHEN LEFT(b.AFD,1) = 'O' THEN 'INTI'
	WHEN LEFT(b.AFD,1) = 'P' THEN 'PLASMA'
END AS TYPE_BUAH
FROM s_data_timbangan a
INNER JOIN (
	SELECT  nab.NO_SPB ,nab.COMPANY_CODE ,nabd.AFD, nabd.BLOCK, nabd.JANJANG, nabd.BERAT_EMPIRIS, nabd.ROUND_TONASE AS BERAT_REAL
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' 
	AND nab.TANGGAL between DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
) b ON a.NO_SPB = b.NO_SPB
WHERE a.COMPANY_CODE = '".$company."'  
and DATE_FORMAT(a.TANGGALK,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
AND a.JENIS_MUATAN='TBS' AND a.NO_SPB LIKE '%PKS%'
ORDER BY a.TANGGALM, b.NO_SPB";

	}
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }

    function get_tbgluar_data($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
/*
        $query = "select a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
                    a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH AS BERAT_BERSIH, 
                    a.SUPPLIERCODE , (a.GRD_LAINNYA/100)*(a.BERAT_ISI-a.BERAT_KOSONG) AS POTONGAN_KG , a.GRD_LAINNYA 
                from s_data_timbangan a
                where a.COMPANY_CODE = '".$company."' 
                        and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        AND a.JENIS_MUATAN='TBS' and a.TYPE_BUAH=2 ORDER BY a.SUPPLIERCODE, a.TANGGALM ASC ";
*/
	$query = "select a.ID_TIMBANGAN,a.NO_TIKET,a.NO_SPB, a.NO_KENDARAAN,a.TANGGALM,a.TANGGALK,
                    a.BERAT_ISI,a.BERAT_KOSONG,a.BERAT_BERSIH AS BERAT_BERSIH, 
                    a.SUPPLIERCODE , BERAT_GRADING AS POTONGAN_KG , a.GRD_LAINNYA 
                from s_data_timbangan a
                where a.COMPANY_CODE = '".$company."' 
                        and DATE_FORMAT(a.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                        AND a.JENIS_MUATAN='TBS' and a.TYPE_BUAH=2 ORDER BY a.SUPPLIERCODE, a.TANGGALM ASC ";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
	
	function get_tbgkebun_data($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
		$query = "SELECT NO_TIKET, NO_SPB, TANGGALM, TANGGALK, WAKTUM, WAKTUK, NO_KENDARAAN, DRIVER_NAME, JENIS_MUATAN, BERAT_ISI, BERAT_KOSONG, BERAT_BERSIH,
TYPE_BUAH, JJG,  COMPANY_CODE
FROM s_data_timbangan_kebun WHERE COMPANY_CODE = '".$company."'  AND TANGGALM BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')";

        
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function get_nabdist($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
        $query = "SELECT notabuah.TANGGAL,notabuah.NO_TIKET, notabuah.NO_SPB, notabuah.NO_KENDARAAN,
                        notabuah.AFD, notabuah.BLOCK, notabuah.JANJANG,
                        timbangan.BERAT_REAL,timbangan.BERAT_BERSIH
                FROM(SELECT nab.NO_TIKET,nab.NO_SPB,nab.NO_KENDARAAN,nab.TANGGAL,
                        nabd.AFD,nabd.BLOCK,nabd.JANJANG
                    FROM s_nota_angkutbuah nab
                    INNER JOIN s_nota_angkutbuah_detail nabd on nabd.ID_NT_AB = nab.ID_NT_AB 
                    WHERE nab.COMPANY_CODE = '".$company."' and nab.ACTIVE = 1 and 
                                    DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                    -- GROUP BY nabd.BLOCK
                )notabuah
                LEFT JOIN (
                    SELECT tbg.NO_TIKET, tbg.NO_SPB,tbgd.AFD,tbgd.BLOCK, tbg.BERAT_ISI, tbg.BERAT_KOSONG, tbg.BERAT_BERSIH,
                         tbgd.JANJANG,tbgd.BERAT_EMPIRIS,tbgd.BERAT_REAL
                  FROM s_data_timbangan tbg
                  LEFT JOIN s_data_timbangan_detail tbgd
                  ON tbgd.ID_TIMBANGAN = tbg.ID_TIMBANGAN
                  WHERE DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d')
                    AND tbg.COMPANY_CODE ='".$company."' AND tbg.TYPE_BUAH =1 and tbg.JENIS_MUATAN='TBS'
                    -- GROUP BY tbgd.BLOCK
                )timbangan ON timbangan.NO_SPB = notabuah.NO_SPB and timbangan.NO_TIKET = notabuah.NO_TIKET
                        and timbangan.BLOCK=notabuah.BLOCK AND timbangan.JANJANG=notabuah.JANJANG
                -- GROUP BY notabuah.BLOCK,timbangan.BLOCK";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
	function get_scrap($periode,$periode_to,$company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
		$query = "SELECT s_dispatch.TANGGALM AS TANGGAL_KIRIM, s_dispatch.NO_KENDARAAN, s_dispatch.DRIVER_NAME, s_dispatch.ID_DO, s_dispatch_do.SO_NUMBER, s_dispatch_do.CUSTOMER_NAME, 
s_dispatch.ID_DISPATCH AS NO_TIKET_KIRIM, s_dispatch.WAKTUM AS JAM_MASUK_KIRIM, s_dispatch.WAKTUK AS JAM_KELUAR_KIRIM, s_dispatch.BERAT_KOSONG AS TARA_KIRIM, s_dispatch.BERAT_ISI AS BRUTO_KIRIM, s_dispatch.BERAT_BERSIH AS NETTO_KIRIM,
s_dispatch_franco.ID_DISPATCH AS NO_TIKET_TERIMA, s_dispatch_franco.TANGGALM AS TANGGAL_TERIMA, s_dispatch_franco.WAKTUM AS JAM_MASUK_TERIMA, s_dispatch_franco.WAKTUK AS JAM_KELUAR_TERIMA, s_dispatch_franco.BERAT_KOSONG AS TARA_TERIMA, s_dispatch_franco.BERAT_ISI AS BRUTO_TERIMA,
s_dispatch_franco.BERAT_BERSIH AS NETTO_TERIMA, (s_dispatch_franco.BERAT_BERSIH - s_dispatch.BERAT_BERSIH) AS SCRAP  
FROM s_dispatch
INNER JOIN s_dispatch_do ON s_dispatch.ID_DO = s_dispatch_do.ID_DO
LEFT JOIN s_dispatch_franco ON s_dispatch.ID_DISPATCH = s_dispatch_franco.ID_DISPATCH_KIRIM
WHERE DATE_FORMAT(s_dispatch.TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') and DATE_FORMAT('".$periode_to."','%Y%m%d') AND s_dispatch.COMPANY_CODE = '".$company."'
ORDER BY s_dispatch_do.SO_NUMBER, s_dispatch.TANGGALM, s_dispatch.ID_DISPATCH ";
        $sQuery = $this->db->query($query);
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        $this->db->close();
        return $temp_result;    
    }
    function export_bjrttp($periode,$company){
        $periode = $this->db->escape_str($periode);
        $company = $this->db->escape_str($company);
        
        //$query = "SELECT BULAN, TAHUN, AFD, BLOCK, VALUE FROM s_data_bjr WHERE COMPANY_CODE='".$company."'";
		$query = "SELECT bj.AFD,bj.BLOCK,bj.VALUE,
							LEFT(PERIODE, 4) AS TAHUN, RIGHT (PERIODE,2) AS BULAN, bj.COMPANY_CODE 
				FROM(
						SELECT AFD,BLOCK,VALUE,
									CONCAT(TAHUN,BULAN) AS PERIODE,
									COMPANY_CODE 
						FROM s_data_bjr 
						WHERE COMPANY_CODE='".$company."' 
				)bj
				JOIN (
						SELECT AFD,BLOCK,MAX(CONCAT(TAHUN,BULAN)) AS MAX_PERIODE
						FROM s_data_bjr
						WHERE COMPANY_CODE='".$company."' AND CONCAT(TAHUN,BULAN) <= DATE_FORMAT('".$periode."','%Y%m') AND ACTIVE=1
						GROUP BY BLOCK 
				) bjr ON bjr.AFD = bj.AFD AND bjr.BLOCK = bj.BLOCK 
								AND bjr.MAX_PERIODE = bj.PERIODE
ORDER BY bj.AFD ASC, bj.BLOCK ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function generate_lhm_produksi_tbs($periode,$periode_to,$company,$jns_barang){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        $jns_barang = $this->db->escape_str($jns_barang);
        /*$query="SELECT s_data_timbangan.TANGGALM AS TANGGAL, s_data_timbangan.COMPANY_CODE,
                              SUM(s_data_timbangan.BERAT_BERSIH) AS TBS_TERIMA,
                              ((SUM(s_data_timbangan.BERAT_BERSIH)+RESTAN_MIN) - RESTAN_H) AS TBS_OLAH,
                              (VOL_H-VOL_MIN)+VOL_DISPATCH AS PROD_CPO,
                              ROUND(((VOL_H-VOL_MIN)+VOL_DISPATCH)/((SUM(BERAT_BERSIH)+RESTAN_MIN) - RESTAN_H),3) AS RENDEMEN,
                              RESTAN_H AS RESTAN,FFA
                     
                FROM s_data_timbangan
                LEFT JOIN(
                           SELECT TANGGAL,COMPANY_CODE,RESTAN AS RESTAN_MIN
                           FROM s_restan
                            WHERE ACTIVE =1
                )rest_min ON rest_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(rest_min.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
                LEFT JOIN (
                           SELECT TANGGAL,COMPANY_CODE,RESTAN AS RESTAN_H
                           FROM s_restan
                           WHERE ACTIVE =1
                )rest_h ON rest_h.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(rest_h.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
                LEFT JOIN (
                       SELECT FFA,COMPANY_CODE,`DATE` 
                       FROM s_ffa_prod 
                        WHERE ACTIVE =1
                )ffa ON ffa.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(ffa.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
                LEFT JOIN(
                    SELECT SUM(snd.VOLUME)AS VOL_MIN,snd.COMPANY_CODE,`DATE` 
                        FROM s_sounding snd
                        WHERE snd.TYPE_S='1' AND snd.ACTIVE=1
                           GROUP BY snd.DATE
                )sounding_min ON sounding_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(sounding_min.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
                LEFT JOIN(
                    SELECT SUM(snd.VOLUME)AS VOL_H,snd.COMPANY_CODE,`DATE` 
                        FROM s_sounding snd
                        WHERE snd.TYPE_S='1' AND snd.ACTIVE=1
                           GROUP BY snd.DATE
                )sounding_h ON sounding_h.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(sounding_h.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
                LEFT JOIN(
                    SELECT BERAT_BERSIH AS VOL_DISPATCH,COMPANY_CODE,TANGGAL
                    FROM s_dispatch
                    WHERE s_dispatch.ACTIVE='1'
                )dispatch ON dispatch.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                    AND DATE(DATE_FORMAT(dispatch.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
                    
                WHERE s_data_timbangan.COMPANY_CODE='".$company."' 
                    AND DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT('".$periode."','%Y%m%d') AND DATE_FORMAT('".$periode."','%Y%m%d')
                        GROUP BY s_data_timbangan.TANGGALM, s_data_timbangan.COMPANY_CODE";*/
        if (empty($periode_to)){
            $periode_to = $periode;
        }
        $qSP ="CALL sp_tbg_gen_produksi_cpo(?, ?, ?, ?)";

        $sQuery = $this->db->query($qSP,array($company,$periode,$periode_to,$jns_barang));
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
        
    }
    
    function get_adem_sales(){
        $config['hostname'] = "192.168.1.4";
        $config['username'] = "adempiere";
        $config['password'] = "adem5224878";
        $config['database'] = "adempiere";
        $config['dbdriver'] = "postgre";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        $config['port'] = "5432";

        $pgsql = $this->load->database($config, TRUE);
        
        /*
            select i.documentno,i.movementdate,p.name Product,bp.name Customer,lo.value Gudang,
            il.movementqty Tonase
            from m_inoutline il
            left join m_inout i on il.m_inout_id = i.m_inout_id
            left join m_product p on il.m_product_id = p.m_product_id
            left join c_bpartner bp on i.c_bpartner_id = bp.c_bpartner_id
            left join m_locator lo on il.m_locator_id = lo.m_locator_id
            where il.ad_org_id = 1000001
            and i.issotrx = 'Y'
            --and lo.value = 'Gd CPO1'
        */
        
        $pgquery="select i.documentno,i.movementdate,p.name Product,bp.name Customer,lo.value Gudang,
            il.movementqty Tonase
            from m_inoutline il
            left join m_inout i on il.m_inout_id = i.m_inout_id
            left join m_product p on il.m_product_id = p.m_product_id
            left join c_bpartner bp on i.c_bpartner_id = bp.c_bpartner_id
            left join m_locator lo on il.m_locator_id = lo.m_locator_id
            where il.ad_org_id = 1000001
            and i.issotrx = 'Y'
            --and lo.value = 'Gd CPO1";
        
        $pgquery2="select org.value PT, 
                    oh.documentno,
                    bp.value kode_customer,bp.name Customer,bpl.name Alamat,
                     o.dateordered, p.name Produk, 
                    o.qtyordered, o.qtydelivered
                    from c_orderline o
                    left join ad_org org on o.ad_org_id=org.ad_org_id
                    left join c_bpartner bp on o.c_bpartner_id=bp.c_bpartner_id
                    left join m_product p on o.m_product_id=p.m_product_id
                    left join c_order oh on o.c_order_id = oh.c_order_id
                    left join c_bpartner_location bpl on bp.c_bpartner_id = bpl.c_bpartner_id
                    where oh.issotrx = 'Y'
                    and (p.value = 'CPO' or p.value ='PK')";
        $sQuery=$pgsql->query($pgquery);
        $row = $sQuery->row();
        
        $value =$row->totaldr;
        $pgsql->close();
       // return $value;
    }
    
    function generate_sounding($periode, $periode_to, $company){
        $periode = $this->db->escape_str($periode);
        $periode_to = $this->db->escape_str($periode_to);
        $company = $this->db->escape_str($company);
        
        $query = "select DATE, TIME, ID_STORAGE, HEIGHT, TEMPERATURE, 0 AS HEIGHT2, VOLUME, WEIGHT
                     from s_sounding a 
                    where a.COMPANY_CODE='".$company."' and a.DATE between date_format('".$periode."','%Y%m%d') and date_format('".$periode_to."','%Y%m%d')
		    union all                   
		    select DATE, TIME, ID_STORAGE, HEIGHT, 0 AS TEMPERATURE, HEIGHT2, 0 AS VOLUME, WEIGHT
                     from s_sounding_kernel a 
                    where a.COMPANY_CODE='".$company."' and a.DATE between date_format('".$periode."','%Y%m%d') and date_format('".$periode_to."','%Y%m%d');";

        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function get_max_stg_volume($company,$strg_code){
        $company = $this->db->escape_str($company);
        $strg_code = $this->db->escape_str($strg_code);
        
        $query = "select a.MAXCAPACITY from m_storage a where a.COMPANY_CODE='".$company."' and a.ID_STORAGE='".$strg_code."'";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function get_vol_tanki($company,$strg_code,$periode){
        $company = $this->db->escape_str($company);
        $strg_code = $this->db->escape_str($strg_code);
        $periode = $this->db->escape_str($periode);
        
        $query = "select a.DATE, a.VOLUME, a.TEMPERATURE, b.FFA 
                from s_sounding a
                LEFT JOIN s_ffa b on b.ID_STORAGE = a.ID_STORAGE AND
                    DATE_FORMAT(b.DATE,'%Y%m%d') = DATE_FORMAT(a.DATE,'%Y%m%d')
                    and b.COMPANY_CODE= a.COMPANY_CODE
                where a.COMPANY_CODE='".$company."' 
                and a.ID_STORAGE='".$strg_code."' and DATE_FORMAT(a.DATE,'%Y%m')='".$periode."' 
                ORDER BY a.DATE ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        $i=0;
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [$i] = $row;
            $i++;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function get_produksi($company,$periode_awal,$periode_akhir){
        $company = $this->db->escape_str($company);
        //$periode_awal = $this->db->escape_str($periode_awal);
        //$periode_akhir = $this->db->escape_str($periode_akhir);
        /*
        $query = "SELECT TANGGAL,TBS_TERIMA,
            TBS_OLAH, PROD_CPO, PROD_KERNEL,
            (PROD_CPO/TBS_OLAH)*100 AS RENDEMEN_CPO,
            coalesce(RESTAN,0) as RESTAN
            FROM(
            SELECT s_data_timbangan.TANGGALM AS TANGGAL,        
                        ROUND(CASE WHEN COALESCE(VOL_H,0) > 0 THEN    
                            CASE WHEN COALESCE(VOL_MIN,0) = 0 THEN
                            (
                                COALESCE((COALESCE(VOL_H,0)+COALESCE(VOL_DISPATCH,0))-
                                COALESCE((SELECT SUM(snd.WEIGHT) as WEIGHT
                                    FROM s_sounding snd
                                    WHERE snd.COMPANY_CODE='".$company."' and DATE_FORMAT(snd.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)-- and snd.ACTIVE =1
                                        FROM s_sounding snd
                                        WHERE snd.DATE < s_data_timbangan.TANGGALM and snd.COMPANY_CODE='".$company."' ),'%Y%m%d')GROUP BY snd.DATE),0),0)
                            )
                            WHEN COALESCE(VOL_MIN,0) > 0 THEN  
                                COALESCE((COALESCE(VOL_H,0)+COALESCE(VOL_DISPATCH,0))-COALESCE(VOL_MIN,0),0)
                            END
                        WHEN COALESCE(VOL_H,0) = 0 THEN '0'
                        END,-1) AS  PROD_CPO ,

                        ROUND(((COALESCE(VOL_KERNEL,0) - COALESCE(VOL_KERNEL_MIN,0))+COALESCE(VOL_DISPATCH_KERNEL,0)),-1) AS PROD_KERNEL,
                        COALESCE(SUM(s_data_timbangan.BERAT_BERSIH),0) as TBS_TERIMA,
                        CASE WHEN COALESCE(VOL_H,0) > 0 THEN   
                            CASE WHEN coalesce(RESTAN_MIN,0) = 0 THEN
                                COALESCE(((
                                    (CASE WHEN coalesce(VOL_MIN,0) = 0 THEN
                                        coalesce(SUM(s_data_timbangan.BERAT_BERSIH),0)+COALESCE(TBS_PLASMA,0)+coalesce(NET_MIN,0)
                                     WHEN COALESCE(VOL_MIN,0) > 0 THEN coalesce(SUM(s_data_timbangan.BERAT_BERSIH),0)+COALESCE(TBS_PLASMA,0)
                                     END )+
                                COALESCE((SELECT RESTAN AS RESTAN_MIN_COND 
                                    FROM s_restan
                                    WHERE ACTIVE=1 AND COMPANY_CODE='".$company."'
                                    AND TANGGAL=DATE(DATE_FORMAT((SELECT MAX(TANGGAL) FROM s_restan
                                                WHERE s_restan.TANGGAL < s_data_timbangan.TANGGALM AND s_restan.COMPANY_CODE='".$company."' 
                                                GROUP BY s_restan.COMPANY_CODE),'%Y%m%d'))),0)) 
                                - COALESCE(RESTAN_H,0)),0)    
                            WHEN RESTAN_MIN > 0 THEN
                                COALESCE(((
                                (CASE WHEN COALESCE(VOL_MIN,0) = 0 THEN
                                    -- coalesce(SUM(s_data_timbangan.BERAT_BERSIH),0)+coalesce(NET_MIN,0)
                                    coalesce(SUM(s_data_timbangan.BERAT_BERSIH),0)+COALESCE(TBS_PLASMA,0)
                                    WHEN COALESCE(VOL_MIN,0) > 0 THEN coalesce(SUM(s_data_timbangan.BERAT_BERSIH),0)+COALESCE(TBS_PLASMA,0)
                                    END)+COALESCE(RESTAN_MIN,0)) - COALESCE(RESTAN_H,0)),0)
                            end
                        WHEN COALESCE(VOL_H,0) = 0 THEN '0'
                        END AS TBS_OLAH,
                        COALESCE(RESTAN_H,0) AS RESTAN
                        
            FROM s_data_timbangan
            LEFT JOIN(
                               SELECT TANGGAL,COMPANY_CODE,RESTAN AS RESTAN_MIN
                               FROM s_restan
                                WHERE s_restan.ACTIVE =1 AND s_restan.COMPANY_CODE='".$company."' 
                        )rest_min ON rest_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(rest_min.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
            LEFT JOIN (
                               SELECT TANGGAL,COMPANY_CODE,RESTAN AS RESTAN_H
                               FROM s_restan
                               WHERE s_restan.ACTIVE =1 AND s_restan.COMPANY_CODE='".$company."' 
                        )rest_h ON rest_h.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(rest_h.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
            LEFT JOIN(
                            SELECT SUM(snd.WEIGHT)AS VOL_MIN,snd.COMPANY_CODE,`DATE` 
                            FROM s_sounding snd
                            WHERE snd.TYPE_S='1' AND snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' 
                               GROUP BY snd.DATE
                        )sounding_min ON sounding_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(sounding_min.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))-1
            LEFT JOIN(
                            SELECT SUM(snd.WEIGHT)AS VOL_H,snd.COMPANY_CODE,`DATE`,ID_SOUNDING
                            FROM s_sounding snd  
                            WHERE snd.TYPE_S='1' AND snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' 
                               GROUP BY snd.DATE
                        )sounding_h ON sounding_h.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(sounding_h.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
            LEFT JOIN(
                            SELECT coalesce(sum(BERAT_BERSIH),0) AS VOL_DISPATCH,COMPANY_CODE,TANGGALM
                            FROM s_dispatch
                            WHERE s_dispatch.ACTIVE='1' AND s_dispatch.COMPANY_CODE='".$company."' and 
                                s_dispatch.ID_KOMODITAS = (
                                    select ID_KOMODITAS from s_komoditas
                                    where s_komoditas.COMPANY_CODE = '".$company."'
                                        and s_komoditas.JENIS like 'CP%'
                                )
                            group by s_dispatch.TANGGALM
                        )dispatch ON dispatch.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(dispatch.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))    
            LEFT JOIN (
                        SELECT tbg1.TANGGALM, tbg1.COMPANY_CODE, sum(tbg1.BERAT_BERSIH) AS NET_MIN
                        FROM s_data_timbangan tbg1
                        WHERE tbg1.TYPE_BUAH=1 AND tbg1.TYPE_TIMBANG=1 AND tbg1.COMPANY_CODE='".$company."' 
                            GROUP BY tbg1.TANGGALM, tbg1.COMPANY_CODE
                    )tbg_min ON tbg_min.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                            AND DATE(DATE_FORMAT(tbg_min.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d')) -1
            LEFT JOIN (
                            SELECT TANGGALM AS TANGGAL,COMPANY_CODE, 
                                COALESCE(SUM(s_data_timbangan.BERAT_BERSIH),0) AS TBS_PLASMA
                            FROM s_data_timbangan
                            WHERE s_data_timbangan.COMPANY_CODE='".$company."' 
                                        -- AND DATE_FORMAT(TANGGALM,'%Y%m%d') BETWEEN DATE_FORMAT(from_periode,'%Y%m%d') AND DATE_FORMAT(to_periode,'%Y%m%d')
                                        AND s_data_timbangan.TYPE_BUAH!=1 AND s_data_timbangan.TYPE_TIMBANG=1
                                        and s_data_timbangan.JENIS_MUATAN='TBS'
                                    GROUP BY s_data_timbangan.TANGGALM-- , s_data_timbangan.COMPANY_CODE 
                    )produksi_plasma ON DATE_FORMAT(produksi_plasma.TANGGAL,'%Y%m%d') = DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d')
                            AND produksi_plasma.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
            LEFT JOIN(
                     SELECT SUM(sndk.WEIGHT)AS VOL_KERNEL, sndk.COMPANY_CODE, sndk.DATE, ID_SOUNDING_KERNEL,
                        case when coalesce(VOL_KERNEL_MIN,0) = 0 then
                            (
                                SELECT SUM(sndk2.WEIGHT)AS VOL_KERNEL_MIN 
                                 FROM s_sounding_kernel sndk2  
                                 WHERE sndk2.ACTIVE=1 and sndk2.COMPANY_CODE='".$company."'
                                    and sndk2.DATE = (
                                        select max(DATE) from s_sounding_kernel
                                        where ACTIVE=1 AND s_sounding_kernel.COMPANY_CODE='".$company."' 
                                            and DATE_FORMAT(date,'%Y%m%d') < DATE_FORMAT(sndk.DATE,'%Y%m%d')
                                    ) and sndk2.COMPANY_CODE = sndk.COMPANY_CODE
                                    GROUP BY sndk2.DATE
                            )
                        when COALESCE(VOL_KERNEL_MIN,0) > 0 then VOL_KERNEL_MIN    
                        end as VOL_KERNEL_MIN,VOL_DISPATCH_KERNEL
                     FROM s_sounding_kernel sndk   
                     left join(
                        SELECT SUM(sndk1.WEIGHT)AS VOL_KERNEL_MIN, sndk1.COMPANY_CODE, sndk1.DATE 
                         FROM s_sounding_kernel sndk1  
                         WHERE sndk1.ACTIVE=1 AND sndk1.COMPANY_CODE='".$company."' 
                            GROUP BY sndk1.DATE
                     )sndk_min on sndk_min.COMPANY_CODE = sndk.COMPANY_CODE
                        AND DATE(DATE_FORMAT(sndk_min.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(sndk.DATE,'%Y%m%d'))-1
                     LEFT JOIN(
                            SELECT COALESCE(sum(BERAT_BERSIH),0) AS VOL_DISPATCH_KERNEL,COMPANY_CODE,TANGGALM
                            FROM s_dispatch
                            WHERE s_dispatch.ACTIVE='1' AND s_dispatch.COMPANY_CODE='".$company."' AND 
                                s_dispatch.ID_KOMODITAS = (
                                    SELECT ID_KOMODITAS FROM s_komoditas
                                    WHERE s_komoditas.COMPANY_CODE = '".$company."'
                                        AND s_komoditas.JENIS LIKE 'KER%'
                                )
                            GROUP BY s_dispatch.TANGGALM
                     )dispatch_kernel ON dispatch_kernel.COMPANY_CODE = sndk.COMPANY_CODE
                            AND DATE(DATE_FORMAT(dispatch_kernel.TANGGALM,'%Y%m%d')) = DATE(DATE_FORMAT(sndk.DATE,'%Y%m%d'))
                     WHERE sndk.ACTIVE=1 AND sndk.COMPANY_CODE='".$company."' 
                        GROUP BY sndk.DATE
                )produksi_kernel ON produksi_kernel.COMPANY_CODE = s_data_timbangan.COMPANY_CODE
                AND DATE(DATE_FORMAT(produksi_kernel.DATE,'%Y%m%d')) = DATE(DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d'))
            WHERE s_data_timbangan.COMPANY_CODE='".$company."' AND DATE_FORMAT(s_data_timbangan.TANGGALM,'%Y%m%d') BETWEEN 
                                    DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
                        AND s_data_timbangan.TYPE_TIMBANG=1 AND s_data_timbangan.TYPE_BUAH=1
                        AND s_data_timbangan.JENIS_MUATAN='TBS'
            GROUP BY s_data_timbangan.TANGGALM, s_data_timbangan.COMPANY_CODE
            )produksi_graph";
			
			$query ="SELECT s_ba.BA_DATE AS TANGGAL, (s_ba.FFB_INTI + s_ba.FFB_PLASMA + s_ba.FFB_SUPPLIER + s_ba.FFB_GROUP) AS TBS_TERIMA,
s_ba.FFB_PROCESSED AS TBS_OLAH,
prod_cpo.WEIGHT AS PROD_CPO,
prod_kernel.WEIGHT AS PROD_KERNEL,
COALESCE((prod_cpo.WEIGHT/s_ba.FFB_PROCESSED)*100,0) AS RENDEMEN_CPO, 
s_ba.BALANCE AS RESTAN
FROM s_ba
LEFT JOIN (
	SELECT prod.PRODUCTION_DATE, prod.WEIGHT FROM s_ba_production prod
	INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS
	WHERE prod.COMPANY_CODE = '".$company."'  AND prod.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
	AND kom.JENIS = 'CPO' AND prod.ACTIVE=1
) prod_cpo ON s_ba.BA_DATE = prod_cpo.PRODUCTION_DATE 
LEFT JOIN(
	SELECT prod.PRODUCTION_DATE, prod.WEIGHT FROM s_ba_production prod
	INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS
	WHERE prod.COMPANY_CODE = '".$company."'  AND prod.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
	AND kom.JENIS = 'KERNEL' AND prod.ACTIVE=1
) prod_kernel ON s_ba.BA_DATE = prod_kernel.PRODUCTION_DATE 
WHERE s_ba.BA_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
AND s_ba.COMPANY_CODE = '".$company."' 
ORDER BY s_ba.BA_DATE";
*/
$query ="SELECT s_ba.BA_DATE AS TANGGAL, (s_ba.FFB_INTI + s_ba.FFB_PLASMA + s_ba.FFB_SUPPLIER + s_ba.FFB_GROUP) AS TBS_TERIMA,
s_ba.FFB_PROCESSED AS TBS_OLAH,
prod_cpo.WEIGHT AS PROD_CPO,
prod_kernel.WEIGHT AS PROD_KERNEL,
COALESCE((prod_cpo.WEIGHT/s_ba.FFB_PROCESSED)*100,0) AS RENDEMEN_CPO, 
COALESCE((prod_kernel.WEIGHT/s_ba.FFB_PROCESSED)*100,0) AS RENDEMEN_KERNEL,
s_ba.BALANCE_YESTERDAY AS RESTAN
FROM s_ba
LEFT JOIN (
	SELECT prod.PRODUCTION_DATE, prod.WEIGHT FROM s_ba_production prod
	INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS
	WHERE prod.COMPANY_CODE = '".$company."'  AND prod.PRODUCTION_DATE BETWEEN                                		
	DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
	AND kom.JENIS = 'CPO' AND prod.ACTIVE=1
) prod_cpo ON s_ba.BA_DATE = prod_cpo.PRODUCTION_DATE 
LEFT JOIN(
	SELECT prod.PRODUCTION_DATE, prod.WEIGHT FROM s_ba_production prod
	INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS
	WHERE prod.COMPANY_CODE = '".$company."'  AND prod.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
	AND kom.JENIS = 'KERNEL' AND prod.ACTIVE=1
) prod_kernel ON s_ba.BA_DATE = prod_kernel.PRODUCTION_DATE 
WHERE s_ba.BA_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d')
AND s_ba.COMPANY_CODE = '".$company."' AND s_ba.ACTIVE=1 
ORDER BY s_ba.BA_DATE";
			//var_dump($query);
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        $i=0;
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [$i] = $row;
            $i++;
        }

        $this->db->close();
        return $temp_result;    
    }
    
	function get_produksi_all($periode_awal,$periode_akhir){
        //$company = $this->db->escape_str($company);
        //$periode_awal = $this->db->escape_str($periode_awal);
        //$periode_akhir = $this->db->escape_str($periode_akhir);
       
		$query ="SELECT COALESCE(daily_production.TBS_TERIMA,0) AS TBS_TERIMA, 
COALESCE(daily_production.TBS_OLAH,0) AS TBS_OLAH,
COALESCE(daily_production.PROD_CPO,0) AS PROD_CPO,
COALESCE(daily_production.PROD_KERNEL,0) AS PROD_KERNEL,
COALESCE(daily_production.RENDEMEN_CPO,0) AS RENDEMEN_CPO,
COALESCE(daily_production.RENDEMEN_KERNEL,0) AS RENDEMEN_KERNEL,
c.COMPANY_CODE, c.COMPANY_NAME FROM m_company c 
LEFT JOIN (
	SELECT SUM((s_ba.FFB_INTI + s_ba.FFB_PLASMA + s_ba.FFB_SUPPLIER + s_ba.FFB_GROUP)) AS TBS_TERIMA, 
	SUM(s_ba.FFB_PROCESSED) AS TBS_OLAH, SUM(prod_cpo.WEIGHT) AS PROD_CPO, SUM(prod_kernel.WEIGHT) AS PROD_KERNEL, 
	COALESCE((SUM(prod_cpo.WEIGHT)/SUM(s_ba.FFB_PROCESSED))*100,0) AS RENDEMEN_CPO, 
	COALESCE((SUM(prod_kernel.WEIGHT)/SUM(s_ba.FFB_PROCESSED))*100,0) AS RENDEMEN_KERNEL,
	s_ba.COMPANY_CODE
	FROM s_ba 
	LEFT JOIN ( SELECT prod.PRODUCTION_DATE, prod.WEIGHT, prod.COMPANY_CODE FROM s_ba_production prod INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS WHERE prod.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d') AND kom.JENIS = 'CPO' AND prod.ACTIVE=1
	) prod_cpo ON s_ba.BA_DATE = prod_cpo.PRODUCTION_DATE AND s_ba.COMPANY_CODE = prod_cpo.COMPANY_CODE 
	LEFT JOIN( SELECT prod.PRODUCTION_DATE, prod.WEIGHT, prod.COMPANY_CODE FROM s_ba_production prod INNER JOIN s_komoditas kom ON prod.ID_COMMODITY = kom.ID_KOMODITAS WHERE prod.PRODUCTION_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d') AND kom.JENIS = 'KERNEL' AND prod.ACTIVE=1 
	) prod_kernel ON s_ba.BA_DATE = prod_kernel.PRODUCTION_DATE AND s_ba.COMPANY_CODE = prod_kernel.COMPANY_CODE 
	WHERE s_ba.BA_DATE BETWEEN DATE_FORMAT('".$periode_awal."','%Y%m%d') AND DATE_FORMAT('".$periode_akhir."','%Y%m%d') AND s_ba.ACTIVE=1
	GROUP BY s_ba.COMPANY_CODE
) daily_production ON c.COMPANY_CODE = daily_production.COMPANY_CODE 
WHERE c.COMPANY_CODE IN ('GKM','LIH','MAG','SMI')";

        $sQuery = $this->db->query($query);
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        $i=0;
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [$i] = $row;
            $i++;
        }

        $this->db->close();
        return $temp_result;    
    }
	
    function get_vol_dispatch($company,$year,$id_komoditas){
        $company = $this->db->escape_str($company);
        $id_komoditas = $this->db->escape_str($id_komoditas);
        
        $query = "select a.ID_KOMODITAS,a.JENIS, a.TANGGALM ,SUM(a.BERAT_BERSIH) AS VOL_DESPATCH  
                    from s_dispatch a 
                    where a.COMPANY_CODE='".$company."' and DATE_FORMAT(a.TANGGALM,'%Y')='".$year."' and a.ID_KOMODITAS='".$id_komoditas."'
                    GROUP BY DATE_FORMAT(a.TANGGALM,'%Y%m')
                    ORDER BY a.TANGGALM ASC";
        $sQuery = $this->db->query($query);
        
        $temp = $sQuery->row_array();
        $temp_result = array(); 
        $i=0;
        foreach ( $sQuery->result_array() as $row ){
            $temp_result [$i] = $row;
            $i++;
        }

        $this->db->close();
        return $temp_result;    
    }
    
    function LoadData_UnmatchNAB($periode,$company){
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company = trim($this->db->escape_str($company));
        
        $queries ="select a.ID_NT_AB, a.NO_SPB,a.TANGGAL from s_nota_angkutbuah a 
                    where a.COMPANY_CODE='".$company."' and DATE_FORMAT(a.TANGGAL,'%Y%m')='".$periode."'
                                and a.NO_TIKET='-'";
            
        $sql2 = $queries;
       
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sql2);
        $count = $query->num_rows(); 

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
            
        $start = $limit * $page - $limit;
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";                                                      

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->ID_NT_AB,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_SPB,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->TANGGAL,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            
            $row = new stdClass();

            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
	/*
		get_restan added by Asep, 20130507		
	*/
	function get_restan($awal_bulan, $tanggal, $company, $block, $tabel){
        $company = $this->db->escape_str($company);
	 if ($company == 'NAK'){
		$query="SELECT LOCATION_CODE, COALESCE(data_restan.STOK_AWAL,0) AS STOK_AWAL, 
COALESCE(SUM(JANJANG_PANEN),0) AS JANJANG_PANEN, 
COALESCE(SUM(JANJANG_ANGKUT),0) AS JANJANG_ANGKUT,
(COALESCE(data_restan.STOK_AWAL,0)+COALESCE(SUM(JANJANG_PANEN),0)-COALESCE(SUM(JANJANG_ANGKUT),0)-COALESCE(data_afkir.AFKIR,0)) AS RESTAN 
FROM
(	
	SELECT nabd.BLOCK AS LOCATION_CODE, 
	0 AS JANJANG_PANEN, 
	SUM(COALESCE(nabd.JANJANG,0)) AS JANJANG_ANGKUT
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')  
	AND nabd.BLOCK = '".$block."'
	GROUP BY nabd.BLOCK
	UNION ALL					
	SELECT LOCATION_CODE, SUM(COALESCE(HSL_VOLUME2,0)) AS JANJANG_PANEN, 
0 AS JANJANG_ANGKUT	
	FROM p_kontraktor pk
	WHERE pk.COMPANY_CODE = '".$company."' AND pk.ACTIVITY_CODE ='8601003' 
	AND pk.TGL_KONTRAK BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d') 
	AND LOCATION_CODE = '".$block."'
	GROUP BY LOCATION_CODE 
) data_produksi 
LEFT JOIN (
	SELECT BLOCK, RESTAN AS STOK_AWAL
	FROM s_restan_block
	WHERE COMPANY_CODE = '".$company."'
	AND ACTIVE =1
	AND TANGGAL = '".$awal_bulan."'
	AND BLOCK ='".$block."'
) data_restan ON data_produksi.LOCATION_CODE= data_restan.BLOCK 
LEFT JOIN (
	SELECT BLOCK, SUM(JANJANG) AS AFKIR FROM s_ba_afkir
	INNER JOIN s_ba_afkir_detail ON s_ba_afkir.ID_BA = s_ba_afkir_detail.ID_BA
	WHERE s_ba_afkir.STATUS =1 AND s_ba_afkir.ACTIVE = 1
	AND BLOCK ='".$block."'
	AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
	GROUP BY BLOCK
) data_afkir ON data_produksi.LOCATION_CODE= data_afkir.BLOCK    
GROUP BY LOCATION_CODE";

	 }else{
        $query="SELECT LOCATION_CODE, COALESCE(data_restan.STOK_AWAL,0) AS STOK_AWAL, 
COALESCE(SUM(JANJANG_PANEN),0) AS JANJANG_PANEN, 
COALESCE(SUM(JANJANG_ANGKUT),0) AS JANJANG_ANGKUT,
COALESCE(data_afkir.AFKIR,0) AS AFKIR,
(COALESCE(data_restan.STOK_AWAL,0)+COALESCE(SUM(JANJANG_PANEN),0)-COALESCE(SUM(JANJANG_ANGKUT),0)-COALESCE(data_afkir.AFKIR,0)) AS RESTAN 
FROM
(	
	SELECT nabd.BLOCK AS LOCATION_CODE, 
	0 AS JANJANG_PANEN, 
	SUM(COALESCE(nabd.JANJANG,0)) AS JANJANG_ANGKUT
	FROM s_nota_angkutbuah nab
	INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
	WHERE nab.COMPANY_CODE='".$company."' AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')  
	AND nabd.BLOCK = '".$block."'
	GROUP BY nabd.BLOCK
	UNION ALL					
	SELECT LOCATION_CODE, 
	SUM(COALESCE(HSL_KERJA_VOLUME,0)) AS JANJANG_PANEN,
	0 AS JANJANG_ANGKUT				
	FROM ".$tabel." mgad
	WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
	AND mgad.COMPANY_CODE = '".$company."'
	AND ACTIVITY_CODE ='8601003'
	AND LOCATION_CODE = '".$block."'
	GROUP BY LOCATION_CODE
) data_produksi 
LEFT JOIN (
	SELECT BLOCK, SUM(RESTAN) AS STOK_AWAL
	FROM s_restan_block
	WHERE COMPANY_CODE = '".$company."'
	AND ACTIVE =1
	AND TANGGAL = '".$awal_bulan."'
	AND BLOCK ='".$block."'
) data_restan ON data_produksi.LOCATION_CODE= data_restan.BLOCK  
LEFT JOIN (
	SELECT BLOCK, SUM(JANJANG) AS AFKIR FROM s_ba_afkir
	INNER JOIN s_ba_afkir_detail ON s_ba_afkir.ID_BA = s_ba_afkir_detail.ID_BA
	WHERE s_ba_afkir.STATUS =1 AND s_ba_afkir.ACTIVE = 1
	AND BLOCK ='".$block."'
	AND COMPANY_CODE = '".$company."' AND DATE_FORMAT(TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
	GROUP BY BLOCK
) data_afkir ON data_produksi.LOCATION_CODE= data_afkir.BLOCK    
GROUP BY LOCATION_CODE";
	}
	//var_dump('*************insert query restan***************');
						//var_dump($query);	
		/*$query="SELECT SUM(COALESCE(HSL_KERJA_VOLUME,0)) AS JANJANG_PANEN
			FROM ".$tabel." mgad
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
			AND mgad.COMPANY_CODE = '".$company."'
			AND mgad.LOCATION_CODE='".$block."'
			AND ACTIVITY_CODE ='8601003'";
			*/
	$this->db->reconnect();
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->RESTAN;    
        }else{
            $value = 0;   
        } 
        return $value;  
    }
	
	/*
		get_janjang_shi added by Asep, 20130507		
	*/
	function get_janjang_shi($awal_bulan, $tanggal, $company, $block, $tabel){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
	 if ($company == 'NAK'){
		$query="SELECT SUM(COALESCE(HSL_VOLUME2,0)) AS JANJANG_PANEN	
					FROM p_kontraktor pk
					WHERE pk.COMPANY_CODE = '".$company."' AND pk.ACTIVITY_CODE ='8601003' 
					AND pk.TGL_KONTRAK BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
					AND LOCATION_CODE = '".$block."'
					GROUP BY LOCATION_CODE ";
	 }else{
		$query="SELECT SUM(COALESCE(HSL_KERJA_VOLUME,0)) AS JANJANG_PANEN
			FROM ".$tabel." mgad
			WHERE DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
			AND mgad.COMPANY_CODE = '".$company."'
			AND mgad.LOCATION_CODE='".$block."'
			AND ACTIVITY_CODE ='8601003'";
	}
	$this->db->reconnect();
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->JANJANG_PANEN;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	
	/*
		get_stock_awal added by Asep, 20130507		
	*/
	function get_stock_awal($tanggal, $company, $jenis){

        $company = $this->db->escape_str($company);
		$query="SELECT WEIGHT FROM s_stock_titip_olah
INNER JOIN s_komoditas ON s_stock_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS
WHERE s_stock_titip_olah.COMPANY_CODE = '".$company."' AND jenis = '".$jenis."' AND s_stock_titip_olah.ACTIVE = 1
AND DATE_FORMAT(STOCK_DATE,'%Y%m') = DATE_FORMAT('".$tanggal."' ,'%Y%m')";
	
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->WEIGHT;    
        }else{
            $value = 0;   
        } 
        return $value;  

	/*
		$company = $this->db->escape_str($company);
		$qTgl = $this->db->query("SELECT MAX(BA_DATE) AS TGL FROM tmp_prod_gkm_fixed WHERE DATE_FORMAT(BA_DATE,'%Y%m') = DATE_FORMAT(DATE('".$tanggal."' - INTERVAL 1 MONTH),'%Y%m') AND CPO_GKM>0");
		$tgl='';
		$value='';
        if($qTgl->num_rows() > 0){
            $row = $qTgl->row(); 
            $tgl = $row->TGL;
			
			$query="SELECT WEIGHT FROM s_stock_titip_olah
			INNER JOIN s_komoditas ON s_stock_titip_olah.ID_COMODITY = s_komoditas.ID_KOMODITAS
			WHERE s_stock_titip_olah.COMPANY_CODE = '".$company."' AND jenis = '".$jenis."' AND s_stock_titip_olah.ACTIVE = 1
			AND DATE_FORMAT(STOCK_DATE,'%Y%m') = DATE_FORMAT('".$tgl."' ,'%Y%m')";
		
			$sQuery = $this->db->query($query);        
			if($sQuery->num_rows() > 0){
				$row = $sQuery->row(); 
				$value = $row->WEIGHT;    
			}else{
				$value = 0;   
			} 
		
        }else{
            $value = 0;   
        } 
		
        return $value;
	*/
    	}
	
	/*
		get_berat_angkut_shi added by Asep, 20130507		
	*/
	function get_berat_angkut_shi($awal_bulan, $tanggal, $company, $block){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT  SUM(COALESCE(nabd.ROUND_TONASE,0)) AS TONASE
				FROM s_nota_angkutbuah nab
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				WHERE nab.COMPANY_CODE='".$company."'
				AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
				AND nabd.BLOCK='".$block."'";
	$this->db->reconnect();
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->TONASE;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	
	/*
		get_yield_angkut_shi added by Asep, 20130507		
	*/
	function get_yield_angkut_shi($awal_bulan, $tanggal, $company, $block){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT  (SUM(COALESCE(nabd.ROUND_TONASE,0))/ filed_crop.HECTPLANTED) AS YIELD_ANGKUT
				FROM s_nota_angkutbuah nab
				INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
				INNER JOIN (
					SELECT f.FIELDCODE, f.HECTPLANTED, f.HECTPLANTABLE, f.TOTALHECTARAGE FROM m_fieldcrop f 
					WHERE f.FIELDCODE='".$block."' AND f.INACTIVE=0 AND f.COMPANY_CODE='".$company."'
				) filed_crop ON nabd.BLOCK = filed_crop.FIELDCODE
				WHERE nab.COMPANY_CODE='".$company."'
				AND DATE_FORMAT(nabd.TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
				AND nabd.BLOCK='".$block."'";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->YIELD_ANGKUT;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	
	/*
		get_janjang_angkut_shi added by Asep, 20130507		
	*/
	function get_janjang_angkut_shi($awal_bulan, $tanggal, $company, $block){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT SUM(COALESCE(nabd.JANJANG,0)) AS JANJANG
			FROM s_nota_angkutbuah nab
			INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
			WHERE nab.COMPANY_CODE='".$company."' 
			AND DATE_FORMAT(nab.TANGGAL,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')			
			AND nabd.BLOCK='".$block."'";
	$this->db->reconnect();
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->JANJANG;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	/*
		get_berat_panen_shi added by Asep, 20130507		
	*/
	function get_berat_panen_shi($awal_bulan, $tanggal, $company, $block, $tabel){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
	if ($company=='NAK'){
		$query="SELECT SUM(COALESCE(HSL_VOLUME,0)) AS BERAT_PANEN 	
					FROM p_kontraktor pk
					WHERE pk.COMPANY_CODE = '".$company."' AND pk.ACTIVITY_CODE ='8601003' 
					AND pk.TGL_KONTRAK BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
					AND LOCATION_CODE = '".$block."'
					GROUP BY LOCATION_CODE";
	}else{
		$query="SELECT SUM(data_panen_lhm.JANJANG_PANEN * COALESCE(data_nota_angkut.BJR_REAL,0)) AS BERAT_PANEN 
FROM 		
(
			SELECT LHM_DATE AS TANGGAL_PANEN,LOCATION_CODE, SUM(COALESCE(HSL_KERJA_VOLUME,0)) AS JANJANG_PANEN 
			FROM ".$tabel." mgad
			WHERE 
			DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')			
				AND mgad.COMPANY_CODE = '".$company."'
				AND ACTIVITY_CODE ='8601003'
				AND mgad.LOCATION_CODE = '".$block."'
			GROUP BY LOCATION_CODE, LHM_DATE
			) data_panen_lhm
LEFT JOIN (
			SELECT nabd.TANGGAL_PANEN, nabd.BLOCK AS LOCATION_CODE,
			(SUM(COALESCE(nabd.TONASE,0))/SUM(COALESCE(nabd.JANJANG,0))) as BJR_REAL
			FROM s_nota_angkutbuah nab
			INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
			WHERE nab.COMPANY_CODE='".$company."' 
			AND DATE_FORMAT(TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
			AND nabd.BLOCK='".$block."'
			
			GROUP BY nabd.BLOCK, nabd.TANGGAL_PANEN
) data_nota_angkut ON data_panen_lhm.LOCATION_CODE = data_nota_angkut.LOCATION_CODE 
						AND DATE_FORMAT(data_panen_lhm.TANGGAL_PANEN,'%Y%m%d')= DATE_FORMAT(data_nota_angkut.TANGGAL_PANEN,'%Y%m%d')
";
	}
	$this->db->reconnect();
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->BERAT_PANEN;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	
	/*
		get_berat_panen_shi added by Asep, 20130507		
	*/
	function get_yield_panen_shi($awal_bulan, $tanggal, $company, $block, $tabel){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT (SUM(data_panen_lhm.JANJANG_PANEN * COALESCE(data_nota_angkut.BJR_REAL,0)) /field_crop.HECTPLANTED)  AS YIELD_PANEN 
FROM(
	SELECT f.FIELDCODE, f.HECTPLANTED, f.HECTPLANTABLE, f.TOTALHECTARAGE FROM m_fieldcrop f 
	WHERE f.FIELDCODE='".$block."' AND f.INACTIVE=0 AND f.COMPANY_CODE='".$company."'
) field_crop 
INNER JOIN( 		
			SELECT LHM_DATE AS TANGGAL_PANEN,LOCATION_CODE, SUM(COALESCE(HSL_KERJA_VOLUME,0)) AS JANJANG_PANEN 
			FROM m_gang_activity_detail mgad
			WHERE 
				DATE_FORMAT(LHM_DATE,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')			
				AND mgad.COMPANY_CODE = '".$company."'
				AND ACTIVITY_CODE ='8601003'
				AND mgad.LOCATION_CODE = '".$block."'
			GROUP BY LOCATION_CODE, LHM_DATE
) data_panen_lhm ON field_crop.FIELDCODE = data_panen_lhm.LOCATION_CODE  
LEFT JOIN (
			SELECT nabd.TANGGAL_PANEN, nabd.BLOCK AS LOCATION_CODE, 
			(SUM(COALESCE(nabd.ROUND_TONASE,0))/SUM(COALESCE(nabd.JANJANG,0))) as BJR_REAL
			FROM s_nota_angkutbuah nab
			INNER JOIN s_nota_angkutbuah_detail nabd ON nab.ID_NT_AB = nabd.ID_NT_AB
			WHERE nab.COMPANY_CODE='".$company."' 
			AND DATE_FORMAT(TANGGAL_PANEN,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."' ,'%Y%m%d') AND DATE_FORMAT('".$tanggal."' ,'%Y%m%d')
			AND nabd.BLOCK='".$block."'			
			GROUP BY nabd.BLOCK, nabd.TANGGAL_PANEN
) data_nota_angkut ON data_panen_lhm.LOCATION_CODE = data_nota_angkut.LOCATION_CODE 
						 AND DATE_FORMAT(data_panen_lhm.TANGGAL_PANEN,'%Y%m%d')= DATE_FORMAT(data_nota_angkut.TANGGAL_PANEN,'%Y%m%d')
";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->YIELD_PANEN;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	/*
		get_tbs_terima_shi added by Asep, 20130507		
	*/
	function get_tbs_terima_shi($awal_bulan, $tanggal, $company, $jenis_muatan){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT SUM(T.BERAT_BERSIH) AS TBS_TERIMA 
				FROM s_data_timbangan T			
				WHERE T.COMPANY_CODE='".$company."' 
				AND DATE_FORMAT(T.TANGGALM ,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."','%Y%m%d') AND DATE_FORMAT('".$tanggal."','%Y%m%d')
				AND T.JENIS_MUATAN= '".$jenis_muatan."'";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->TBS_TERIMA;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	/*
		get_tbs_olah_shi added by Asep, 20130507		
	*/
	function get_tbs_olah_shi($awal_bulan, $tanggal, $company, $jenis_muatan){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT SUM((COALESCE(data_timbangan.TBS_TERIMA,0)+ COALESCE(data_restan1.RESTAN1,0)) - COALESCE(data_restan.RESTAN,0)) AS TBS_OLAH			
		FROM 			
		(		
			SELECT T.TANGGALM AS TANGGAL,
			SUM(T.BERAT_BERSIH) AS TBS_TERIMA					    
			FROM s_data_timbangan T			
			WHERE T.COMPANY_CODE='".$company."' 
			AND DATE_FORMAT(T.TANGGALM ,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."','%Y%m%d') AND DATE_FORMAT('".$tanggal."','%Y%m%d') 
			AND T.JENIS_MUATAN= '".$jenis_muatan."'
			GROUP BY T.TANGGALM, T.COMPANY_CODE
		) data_timbangan
		LEFT JOIN(
			SELECT R.TANGGAL, R.RESTAN AS RESTAN
			FROM s_restan R
			WHERE R.ACTIVE =1 AND R.COMPANY_CODE='".$company."' 
		)data_restan ON DATE(DATE_FORMAT(data_timbangan.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(data_restan.TANGGAL,'%Y%m%d'))	
		LEFT JOIN(
			SELECT DATE(DATE_FORMAT(R.TANGGAL + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL1, R.COMPANY_CODE, R.RESTAN AS RESTAN1
			FROM s_restan R
			WHERE R.ACTIVE =1 AND R.COMPANY_CODE='".$company."' 
		)data_restan1 ON DATE(DATE_FORMAT(data_timbangan.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(data_restan1.TANGGAL1,'%Y%m%d'))";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->TBS_OLAH;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	/*
		get_prod_cpo_shi added by Asep, 20130507		
	*/
	function get_prod_cpo_shi($awal_bulan, $tanggal, $company){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT  SUM(coalesce((WEIGHT0 - WEIGHT1) + coalesce(VOL_DISPATCH,0),0)) AS PROD 			
					FROM
					(	
						SELECT DATE(DATE_FORMAT(TANGGAL0 - INTERVAL 1 DAY,'%Y%m%d')) AS TANGGAL_PRODUKSI, TANGGAL0, SUM(WEIGHT0) AS WEIGHT0, 
								SUM(CASE WHEN coalesce(SOUNDING1.WEIGHT1,0) = 0  THEN (
									SELECT snd2.WEIGHT as WEIGHT
									FROM s_sounding snd2
									WHERE snd2.COMPANY_CODE='".$company."' 
									AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																						FROM s_sounding snd1
																						WHERE snd1.DATE < DATE_FORMAT(TANGGAL0,'%Y%m%d') and snd1.COMPANY_CODE='".$company."'
																						AND snd1.ACTIVE=1 AND snd1.ID_STORAGE=SOUNDING0.ID_STORAGE),'%Y%m%d') 
								   AND snd2.ID_STORAGE = SOUNDING0.ID_STORAGE						
								)ELSE SOUNDING1.WEIGHT1 END) AS WEIGHT1
						FROM
						(
							SELECT snd.WEIGHT AS WEIGHT0, `DATE` AS TANGGAL0, strg.STORAGE_NUM AS STORAGE_NUM0, snd.ID_STORAGE 
							FROM s_sounding snd 
							LEFT JOIN m_storage strg ON snd.ID_STORAGE = strg.ID_STORAGE 
							WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' AND strg.STORAGE_NUM =1 
						) SOUNDING0 
						LEFT JOIN (
							SELECT snd.WEIGHT AS WEIGHT1, strg.STORAGE_NUM AS STORAGE_NUM1,
							DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL1
							FROM s_sounding snd 
							LEFT JOIN m_storage strg ON snd.ID_STORAGE = strg.ID_STORAGE  
							WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' AND strg.STORAGE_NUM =1  
						) SOUNDING1 ON SOUNDING0.TANGGAL0 = SOUNDING1.TANGGAL1 AND SOUNDING0.STORAGE_NUM0 = SOUNDING1.STORAGE_NUM1 
						GROUP BY TANGGAL_PRODUKSI
					) AS data_sounding
					LEFT JOIN (
						SELECT SUM(BERAT_BERSIH) AS VOL_DISPATCH, DATE(DATE_FORMAT(TANGGALM + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL_S1 
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE='".$company."' 
						AND s_dispatch.ID_KOMODITAS = (
												SELECT ID_KOMODITAS FROM s_komoditas
												WHERE s_komoditas.COMPANY_CODE = '".$company."'
												AND s_komoditas.JENIS LIKE 'CP%'
											)
						GROUP BY s_dispatch.TANGGALM
					)DISPATCH ON data_sounding.TANGGAL0 = DISPATCH.TANGGAL_S1
					WHERE 
					DATE_FORMAT(TANGGAL_PRODUKSI ,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."','%Y%m%d') AND DATE_FORMAT('".$tanggal."','%Y%m%d')";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->PROD;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
	/*
		get_rendemen_shi added by Asep, 20130507		
	*/
	function get_rendemen_shi($awal_bulan, $tanggal, $company, $jenis_muatan){
        $company = $this->db->escape_str($company);
        //$query="SELECT PERIODE_USED FROM s_data_bjr WHERE COMPANY_CODE ='".$company."'";
		$query="SELECT SUM(COALESCE(((PROD/TBS_OLAH) * 100),0)) AS RENDEMEN
		FROM(
			SELECT data_timbangan.TANGGAL, 
					((COALESCE(data_timbangan.TBS_TERIMA,0)+ COALESCE(data_restan1.RESTAN1,0)) - COALESCE(data_restan.RESTAN,0)) AS TBS_OLAH			
					FROM 			
					(		
							SELECT T.TANGGALM AS TANGGAL,
							SUM(T.BERAT_BERSIH) AS TBS_TERIMA					    
							FROM s_data_timbangan T			
							WHERE T.COMPANY_CODE='".$company."' 
							AND DATE_FORMAT(T.TANGGALM ,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."','%Y%m%d') AND DATE_FORMAT('".$tanggal."','%Y%m%d') 									
							AND T.JENIS_MUATAN= '".$jenis_muatan."'
							GROUP BY T.TANGGALM, T.COMPANY_CODE
					) data_timbangan
					LEFT JOIN(
							SELECT R.TANGGAL, R.RESTAN AS RESTAN
							FROM s_restan R
							WHERE R.ACTIVE =1 AND R.COMPANY_CODE='".$company."' 
					)data_restan ON DATE(DATE_FORMAT(data_timbangan.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(data_restan.TANGGAL,'%Y%m%d'))						
					LEFT JOIN(
						SELECT DATE(DATE_FORMAT(R.TANGGAL + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL1, R.RESTAN AS RESTAN1
						FROM s_restan R
						WHERE R.ACTIVE =1 AND R.COMPANY_CODE='".$company."' 
					)data_restan1 ON DATE(DATE_FORMAT(data_timbangan.TANGGAL,'%Y%m%d')) = DATE(DATE_FORMAT(data_restan1.TANGGAL1,'%Y%m%d')) 
		) data_kebun 
		LEFT JOIN(
			SELECT TANGGAL_PRODUKSI AS TANGGAL,
			coalesce(WEIGHT0,0) AS WEIGHT0, coalesce(WEIGHT1,0) AS WEIGHT1, coalesce(VOL_DISPATCH,0) AS VOL_DISPATCH1  
					, coalesce((WEIGHT0 - WEIGHT1) + coalesce(VOL_DISPATCH,0),0) AS PROD 			
					FROM
					(	
						SELECT DATE(DATE_FORMAT(TANGGAL0 - INTERVAL 1 DAY,'%Y%m%d')) AS TANGGAL_PRODUKSI, TANGGAL0, SUM(WEIGHT0) AS WEIGHT0, 
								SUM(CASE WHEN coalesce(SOUNDING1.WEIGHT1,0) = 0  THEN (
									SELECT snd2.WEIGHT as WEIGHT
									FROM s_sounding snd2
									WHERE snd2.COMPANY_CODE='".$company."' 
									AND DATE_FORMAT(snd2.DATE,'%Y%m%d') = DATE_FORMAT((SELECT MAX(DATE)
																						FROM s_sounding snd1
																						WHERE snd1.DATE < DATE_FORMAT(TANGGAL0,'%Y%m%d') and snd1.COMPANY_CODE='".$company."'
																						AND snd1.ACTIVE=1 AND snd1.ID_STORAGE=SOUNDING0.ID_STORAGE),'%Y%m%d') 
								   AND snd2.ID_STORAGE = SOUNDING0.ID_STORAGE						
								)ELSE SOUNDING1.WEIGHT1 END) AS WEIGHT1
						FROM
						(
							SELECT snd.WEIGHT AS WEIGHT0 ,`DATE` AS TANGGAL0, strg.STORAGE_NUM AS STORAGE_NUM0, snd.ID_STORAGE 
							FROM s_sounding snd 
							LEFT JOIN m_storage strg ON snd.ID_STORAGE = strg.ID_STORAGE 
							WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' AND strg.STORAGE_NUM =1 
						) SOUNDING0 
						LEFT JOIN (
							SELECT snd.WEIGHT AS WEIGHT1, strg.STORAGE_NUM AS STORAGE_NUM1,
							DATE(DATE_FORMAT(snd.DATE + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL1
							FROM s_sounding snd 
							LEFT JOIN m_storage strg ON snd.ID_STORAGE = strg.ID_STORAGE  
							WHERE snd.ACTIVE=1 AND snd.COMPANY_CODE='".$company."' AND strg.STORAGE_NUM =1  
						) SOUNDING1 ON SOUNDING0.TANGGAL0 = SOUNDING1.TANGGAL1 AND SOUNDING0.STORAGE_NUM0 = SOUNDING1.STORAGE_NUM1 
						GROUP BY TANGGAL_PRODUKSI
					) AS data_sounding
					LEFT JOIN (
						SELECT SUM(BERAT_BERSIH) AS VOL_DISPATCH
						,DATE(DATE_FORMAT(TANGGALM + INTERVAL 1 DAY,'%Y%m%d'))  AS TANGGAL_S1 
						FROM s_dispatch
						WHERE s_dispatch.ACTIVE='1' 
						AND s_dispatch.COMPANY_CODE='".$company."' 
						AND s_dispatch.ID_KOMODITAS = (
												SELECT ID_KOMODITAS FROM s_komoditas
												WHERE s_komoditas.COMPANY_CODE = '".$company."'
												AND s_komoditas.JENIS LIKE 'CP%'
											)
						GROUP BY s_dispatch.TANGGALM
					)DISPATCH ON data_sounding.TANGGAL0 = DISPATCH.TANGGAL_S1
					WHERE 
					DATE_FORMAT(TANGGAL_PRODUKSI ,'%Y%m%d') BETWEEN DATE_FORMAT('".$awal_bulan."','%Y%m%d') AND DATE_FORMAT('".$tanggal."','%Y%m%d')					
		) data_produksi ON data_kebun.TANGGAL = data_produksi.TANGGAL";
        $sQuery = $this->db->query($query);
        $value='';
        if($sQuery->num_rows() > 0){
            $row = $sQuery->row(); 
            $value = $row->RENDEMEN;    
        }else{
            $value = '<span style="color: #FF0000; font-weight: bold;"><em>BJR NOT SET</em></span>';   
        } 
        return $value;  
    }
}
?>
