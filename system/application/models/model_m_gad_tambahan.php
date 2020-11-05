<?

class Model_m_gad_tambahan extends Model 
{

    function Model_m_gad_tambahan()
    {
        parent::Model(); 

		$this->load->database();
    }

	function insert_m_gad_tambahan ( $data )
	{
		$this->db->insert( 'm_gad_tambahan', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_gad_tambahan ( $id, $data )
	{
		$this->db->where( 'NIK', $id );  
		$this->db->update( 'm_gad_tambahan', $data );   
	}
	
	function enroll_m_gad_tambahan ( )
	{
		$this->db->select( 'NIK,PERIODE,TUNJANGAN_JABATAN,POTONGAN_LAIN,NATURA,POTONGAN_NATURA,RAPEL,THR,BONUS,TUNJANGAN_CUTI,PENSIUN,PPH_21,PAJAK_BLN_LALU,COMPANY_CODE');

		$this->db->from( 'm_gad_tambahan' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	/* grid pinjaman */
	function read_employee($periode, $type_karyawan, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
		
        $sidx = 'TYPE_KARYAWAN';
        $sord = 'DESC';
		
		if ($type_karyawan == "all") {
			$karyawan = "AND 1 = 1";
		} else {
			$karyawan = "AND TYPE_KARYAWAN = '".$type_karyawan."'";
		}
		
		$sql2 = "SELECT DATE_FORMAT(gad.LHM_DATE, '%Y%m') AS PERIODE, gad.EMPLOYEE_CODE AS NIK, emp.NAMA as NAMA, emp.TYPE_KARYAWAN as TYPE_KARYAWAN, emp.FAMILY_STATUS as STATUS, emp.COMPANY_CODE FROM m_gang_activity_detail gad
LEFT JOIN m_employee emp ON (emp.NIK = gad.EMPLOYEE_CODE AND emp.COMPANY_CODE = gad.COMPANY_CODE)
WHERE gad.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad.LHM_DATE, '%Y%m') = '".$periode."' AND gad.EMPLOYEE_CODE <> '' ".$karyawan." GROUP BY EMPLOYEE_CODE";

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

		
$sql = "SELECT DATE_FORMAT(gad.LHM_DATE, '%Y%m') AS PERIODE, gad.EMPLOYEE_CODE AS NIK, emp.NAMA as NAMA, emp.TYPE_KARYAWAN as TYPE_KARYAWAN, emp.FAMILY_STATUS as STATUS, emp.COMPANY_CODE FROM m_gang_activity_detail gad
LEFT JOIN m_employee emp ON (emp.NIK = gad.EMPLOYEE_CODE AND emp.COMPANY_CODE = gad.COMPANY_CODE)
WHERE gad.COMPANY_CODE = '".$company."' AND DATE_FORMAT(gad.LHM_DATE, '%Y%m') = '".$periode."' AND gad.EMPLOYEE_CODE <> '' ".$karyawan." GROUP BY EMPLOYEE_CODE ORDER BY ".$sidx." ".$sord."";

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
						
		$no = 1;			
		$action = "";		
		$tunjab = "";
		$potongan_lain = "";
		$natura = "";
		$rapel = "";
		$thr = "";
		$bonus = "";
		$pensiun = "";
		$pph21 = "";
		$pajak_lalu = "";
				
							
		foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, $no);
					array_push($cell, $obj->NIK);
                    array_push($cell, $obj->NAMA);
					array_push($cell, $obj->TYPE_KARYAWAN);
					array_push($cell, $obj->STATUS);
                	array_push($cell, $tunjab);
					array_push($cell, $potongan_lain);
					array_push($cell, $natura);
					array_push($cell, $rapel);
					array_push($cell, $thr);
					array_push($cell, $bonus);
					array_push($cell, $pensiun);
					array_push($cell, $pph21);
					array_push($cell, $pajak_lalu);
					//array_push($cell, $action);
			
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
	    


}   

?>
