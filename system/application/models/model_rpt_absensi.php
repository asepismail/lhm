<?

class model_rpt_absensi extends Model 
{

    function model_rpt_absensi()
    {
        parent::Model(); 

        $this->load->database();
    }
    
    function create_absensi($company, $gc, $periode){
        $query = $this->db->query("SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
  GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',ROUND(c.HK,2) ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN,
  SUM(c.HK) AS KJ FROM m_empgang emp 
  LEFT JOIN (SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, SUM(IF(gad2.HK_JUMLAH <> '0.00' AND gad2.TYPE_ABSENSI IN ('KJ','KJI'),gad2.HK_JUMLAH,0)) AS HK, gad2.COMPANY_CODE FROM m_gang_activity_detail gad2
WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."'
GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE ) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE LEFT JOIN (SELECT
               emp1.NIK,
               emp1.NAMA,
               emp1.TYPE_KARYAWAN
             FROM m_employee emp1
             WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK) b
    ON emp.EMPLOYEE_CODE = b.NIK
WHERE emp.GANG_CODE = '".$gc."'
    AND CONCAT(emp.YEAR,emp.MONTH) = '".$periode."'
    AND emp.COMPANY_CODE = '".$company."' 
GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.HK) <> 0 
ORDER BY c.LHM_DATE, emp.EMPLOYEE_CODE ASC
  ");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }

        return $temp_result;
    
    }
    
    function create_absensi_all($company, $periode){
        $query = $this->db->query("SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
  GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',ROUND(c.HK) ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN,
  SUM(c.HK) AS KJ FROM m_empgang emp 
  LEFT JOIN ( SELECT gad2.EMPLOYEE_CODE, 
  					gad2.LHM_DATE, 
					ROUND(SUM(IF(gad2.HK_JUMLAH <> '0.00' 
					AND gad2.TYPE_ABSENSI IN ('KJ','KJI'),gad2.HK_JUMLAH,0))) AS HK, 
					gad2.COMPANY_CODE FROM m_gang_activity_detail gad2
WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."'
GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE ) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
LEFT JOIN (SELECT
               emp1.NIK,
               emp1.NAMA,
               emp1.TYPE_KARYAWAN
             FROM m_employee emp1
             WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK ) b
    ON emp.EMPLOYEE_CODE = b.NIK
WHERE CONCAT(emp.YEAR,emp.MONTH) = '".$periode."'
    AND emp.COMPANY_CODE = '".$company."' 
GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.HK) <>0 
ORDER BY c.LHM_DATE, emp.EMPLOYEE_CODE ASC
  ");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }

        return $temp_result;
    
    }
    
    function delete_elhm ( $gc,$nik,$tgl,$company)
    {
        $this->db->where( 'GANG_CODE', $gc ); 
        $this->db->where( 'NIK', $nik ); 
        $this->db->where( 'BDATE', $tgl ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('p_pjm_karyawan');   
    }
    
    function cek_hari($tgl,$company){
        $query = $this->db->query("SELECT * FROM m_calendar WHERE COMPANY_CODE = '".$company."' AND DATE_FORMAT(CAL_TGL,'%Y%m%e') = '".$tgl."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
            
        }

        return $temp_result;
    }
    
    function employee_lhm($nik, $periode, $company){
    
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $sidx = 'LHM_DATE';
        $sord = 'ASC';
        
        $sql2 = "SELECT ID,GANG_CODE,LHM_DATE,EMPLOYEE_CODE as NIK, e.NAMA,TYPE_ABSENSI,LOCATION_TYPE_CODE,
LOCATION_CODE,ACTIVITY_CODE,HSL_KERJA_UNIT,HSL_KERJA_VOLUME,TARIF_SATUAN,HK_JUMLAH,LEMBUR_JAM,TARIF_SATUAN, 
PREMI,PENALTI,m_gang_activity_detail.COMPANY_CODE FROM m_gang_activity_detail 
LEFT JOIN m_employee e ON (e.`NIK` = m_gang_activity_detail.`EMPLOYEE_CODE` AND e.INACTIVE = 0)
WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND EMPLOYEE_CODE = '".$nik."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."'";
            
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

        $this->db->limit($limit, $start);
  
        $sql = "SELECT ID,GANG_CODE,LHM_DATE,EMPLOYEE_CODE as NIK, e.NAMA as NM_K,TYPE_ABSENSI,LOCATION_TYPE_CODE,
LOCATION_CODE,ACTIVITY_CODE,HSL_KERJA_UNIT,HSL_KERJA_VOLUME,TARIF_SATUAN,HK_JUMLAH,LEMBUR_JAM,TARIF_SATUAN, 
PREMI,PENALTI,m_gang_activity_detail.COMPANY_CODE FROM m_gang_activity_detail 
LEFT JOIN m_employee e ON (e.`NIK` = m_gang_activity_detail.`EMPLOYEE_CODE` AND e.INACTIVE = 0) 
WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND EMPLOYEE_CODE = '".$nik."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."' ORDER BY ".$sidx." ".$sord."";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $action = "";
        $no = 1;        
        foreach($objects as $obj)
        {
            $cell = array();
            
                array_push($cell, $obj->ID);
                array_push($cell, $obj->GANG_CODE);
                array_push($cell, $obj->LHM_DATE);    
                array_push($cell, $obj->NIK);                 
                array_push($cell, $obj->NM_K);
                array_push($cell, $obj->TYPE_ABSENSI);
                array_push($cell, $obj->LOCATION_TYPE_CODE);
                array_push($cell, $obj->LOCATION_CODE);
                array_push($cell, $obj->ACTIVITY_CODE);
    			array_push($cell, $obj->HK_JUMLAH);    
   				array_push($cell, $obj->HSL_KERJA_UNIT); 
     			array_push($cell, $obj->HSL_KERJA_VOLUME); 
    			array_push($cell, $obj->TARIF_SATUAN); 
   				array_push($cell, $obj->PREMI); 
    			array_push($cell, $obj->LEMBUR_JAM); 
    			array_push($cell, $obj->PENALTI); 
                array_push($cell, $obj->COMPANY_CODE);
                array_push($cell, $action);    
                    
                    
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
	
	/* modified : Asep #2013-12-12 */
	/* buat laporan absensi berdasarkan type absensi */
	function create_absensi_bytype($company, $gc, $periode){
		$query = $this->db->query("SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
				GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',
				c.TYPE_ABSENSI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN, 
				d.ABSEN_NA AS ABSEN_NA,
				d.NA,
				SUM(c.HK) AS KJ,
				SUM(c.CT) AS CT,
				SUM(c.S1) AS S1,
				SUM(c.PH) AS PH,
				SUM(c.P1) AS P1,
				SUM(c.M) AS M,
				SUM(c.H2) AS H2,
				SUM(c.P25) AS P25,
				SUM(c.P23) AS P23,
				SUM(c.P28) AS P28,
				SUM(c.P21) AS P21,
				SUM(c.H1) AS H1,
				SUM(c.S2) AS S2 
				FROM m_empgang emp 
				LEFT JOIN (
						SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 
						SUM(IF(gad2.HK_JUMLAH <> '0.00' AND gad2.TYPE_ABSENSI IN ('KJ','KJI'),gad2.HK_JUMLAH,0)) AS HK, 
						IF(gad2.TYPE_ABSENSI IN ('CT'),1.00,'') AS CT, 
						IF(gad2.TYPE_ABSENSI IN ('S1'),1.00,'') AS S1,
						IF(gad2.TYPE_ABSENSI IN ('PH'),1.00,'') AS PH,
						IF(gad2.TYPE_ABSENSI IN ('P1'),1.00,'') AS P1,
						IF(gad2.TYPE_ABSENSI IN ('M'),1.00,'') AS M,
						IF(gad2.TYPE_ABSENSI IN ('H2'),1.00,'') AS H2,
						IF(gad2.TYPE_ABSENSI IN ('P25'),1.00,'') AS P25,
						IF(gad2.TYPE_ABSENSI IN ('P23'),1.00,'') AS P23,
						IF(gad2.TYPE_ABSENSI IN ('P28'),1.00,'') AS P28,
						IF(gad2.TYPE_ABSENSI IN ('P21'),1.00,'') AS P21,
						IF(gad2.TYPE_ABSENSI IN ('H1'),1.00,'') AS H1,
						IF(gad2.TYPE_ABSENSI IN ('S2'),1.00,'') AS S2,
						gad2.COMPANY_CODE,
						gad2.TYPE_ABSENSI  
						FROM m_gang_activity_detail gad2 
						WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."' 
						AND gad2.TYPE_ABSENSI NOT IN ('NA')
						GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE 
					) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
				LEFT JOIN (
					SELECT emp1.NIK, emp1.NAMA, emp1.TYPE_KARYAWAN FROM m_employee emp1 
					WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK) b ON emp.EMPLOYEE_CODE = b.NIK 
				LEFT JOIN(
					SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
					GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',
					c.TYPE_ABSENSI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN_NA, 
					SUM(c.NA) NA 
				FROM m_empgang emp 
				LEFT JOIN (
						SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 		
						IF(gad2.TYPE_ABSENSI IN ('NA'),1.00,'') AS NA,
						gad2.COMPANY_CODE,
						gad2.TYPE_ABSENSI  
						FROM m_gang_activity_detail gad2 
						WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."' 
						AND gad2.TYPE_ABSENSI IN ('NA')
						GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE 
					) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
				LEFT JOIN 
					(SELECT emp1.NIK, emp1.NAMA, emp1.TYPE_KARYAWAN FROM m_employee emp1 
					WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK) b ON emp.EMPLOYEE_CODE = b.NIK 
				WHERE emp.GANG_CODE = '".$gc."' AND CONCAT(emp.YEAR,emp.MONTH) = '".$periode."' 
				AND emp.COMPANY_CODE = '".$company."' 
				GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.NA) <> 0 		
						
					) d ON d.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
				WHERE emp.GANG_CODE = '".$gc."' AND CONCAT(emp.YEAR,emp.MONTH) = '".$periode."' 
				AND emp.COMPANY_CODE = '".$company."' 
				GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.HK) <> 0 
				ORDER BY emp.EMPLOYEE_CODE ASC");
        
		$temp_result = array();
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
            
        }

        return $temp_result;
    }
	
	function create_absensi_bytype_all($company, $periode){        
		$query = $this->db->query("SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
						GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',
						c.TYPE_ABSENSI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN, 
						d.ABSEN_NA AS ABSEN_NA,
						d.NA,
						SUM(c.HK) AS KJ,
						SUM(c.CT) AS CT,
						SUM(c.S1) AS S1,
						SUM(c.PH) AS PH,
						SUM(c.P1) AS P1,
						SUM(c.M) AS M,
						SUM(c.H2) AS H2,
						SUM(c.P25) AS P25,
						SUM(c.P23) AS P23,
						SUM(c.P28) AS P28,
						SUM(c.P21) AS P21,
						SUM(c.H1) AS H1,
						SUM(c.S2) AS S2 
						FROM m_empgang emp 
				LEFT JOIN (
						SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 
						SUM(IF(gad2.HK_JUMLAH <> '0.00' AND gad2.TYPE_ABSENSI IN ('KJ','KJI'),gad2.HK_JUMLAH,0)) AS HK, 
						IF(gad2.TYPE_ABSENSI IN ('CT'),1.00,'') AS CT, 
						IF(gad2.TYPE_ABSENSI IN ('S1'),1.00,'') AS S1,
						IF(gad2.TYPE_ABSENSI IN ('PH'),1.00,'') AS PH,
						IF(gad2.TYPE_ABSENSI IN ('P1'),1.00,'') AS P1,
						IF(gad2.TYPE_ABSENSI IN ('M'),1.00,'') AS M,
						IF(gad2.TYPE_ABSENSI IN ('H2'),1.00,'') AS H2,
						IF(gad2.TYPE_ABSENSI IN ('P25'),1.00,'') AS P25,
						IF(gad2.TYPE_ABSENSI IN ('P23'),1.00,'') AS P23,
						IF(gad2.TYPE_ABSENSI IN ('P28'),1.00,'') AS P28,
						IF(gad2.TYPE_ABSENSI IN ('P21'),1.00,'') AS P21,
						IF(gad2.TYPE_ABSENSI IN ('H1'),1.00,'') AS H1,
						IF(gad2.TYPE_ABSENSI IN ('S2'),1.00,'') AS S2,
						gad2.COMPANY_CODE,
						gad2.TYPE_ABSENSI  
						FROM m_gang_activity_detail gad2 
						WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."' 
						AND gad2.TYPE_ABSENSI NOT IN ('NA')
						GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE 
					) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
				LEFT JOIN (
					SELECT emp1.NIK, emp1.NAMA, emp1.TYPE_KARYAWAN FROM m_employee emp1 
					WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK) b ON emp.EMPLOYEE_CODE = b.NIK 
				LEFT JOIN (	
					SELECT emp.GANG_CODE,emp.EMPLOYEE_CODE, b.NAMA, b.TYPE_KARYAWAN, 
					GROUP_CONCAT(DATE_FORMAT(c.LHM_DATE, '%e'),':',
					c.TYPE_ABSENSI ORDER BY c.LHM_DATE SEPARATOR ',') AS ABSEN_NA, 
					SUM(c.NA) NA 
					FROM m_empgang emp 
					LEFT JOIN (
							SELECT gad2.EMPLOYEE_CODE, gad2.LHM_DATE, 		
							IF(gad2.TYPE_ABSENSI IN ('NA'),1.00,'') AS NA,
							gad2.COMPANY_CODE,
							gad2.TYPE_ABSENSI  
							FROM m_gang_activity_detail gad2 
							WHERE gad2.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad2.LHM_DATE,'%Y%m') = '".$periode."' 
							AND gad2.TYPE_ABSENSI IN ('NA')
							GROUP BY gad2.LHM_DATE, gad2.EMPLOYEE_CODE 
						) c ON c.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
					LEFT JOIN (
							SELECT emp1.NIK, emp1.NAMA, emp1.TYPE_KARYAWAN FROM m_employee emp1 
							WHERE emp1.COMPANY_CODE = '".$company."' GROUP BY emp1.NIK) b ON emp.EMPLOYEE_CODE = b.NIK 
							WHERE CONCAT(emp.YEAR,emp.MONTH) = '".$periode."' 
							AND emp.COMPANY_CODE = '".$company."' 
							GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.NA) <> 0 		
						) d ON d.EMPLOYEE_CODE = emp.EMPLOYEE_CODE 
					WHERE CONCAT(emp.YEAR,emp.MONTH) = '".$periode."' 
					AND emp.COMPANY_CODE = '".$company."' 
					GROUP BY emp.EMPLOYEE_CODE HAVING SUM(c.HK) <> 0 
					ORDER BY emp.EMPLOYEE_CODE ASC");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;   
        }
        return $temp_result;
    }
	/* end type absensi */
}

?>