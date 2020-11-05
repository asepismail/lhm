<?

class model_m_employee_tp extends Model 
{
    private $table_list;
    private $table_name;
    
    function __construct()
    {
        parent::Model(); 
        $this->load->database();
    }
	
	/* function untuk lembur */
	function read_employee_ot($periode, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
        $sql2 = "SELECT ID, EMPLOYEE_CODE, emp.NAMA AS NAMA, GANG_CODE, LHM_DATE, TYPE_ABSENSI, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE,";
		$sql2 .= " LEMBUR_JAM, LEMBUR_RUPIAH, m_gang_activity_detail.COMPANY_CODE AS COMPANY_CODE FROM m_gang_activity_detail  ";
		$sql2 .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ";
		$sql2 .= " ON emp.NIK = m_gang_activity_detail.EMPLOYEE_CODE ";
		$sql2 .= " WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."'";
		$sql2 .= " AND LEMBUR_JAM > 0";

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

		$sql = $sql2;
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
                    array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->EMPLOYEE_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LHM_DATE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->TYPE_ABSENSI,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LEMBUR_JAM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LEMBUR_RUPIAH,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      
        return $jsonObject;
    } 	
	
	function glembursingle ($id, $nik, $gc, $date, $absen, $loc, $act, $co) {
			
		$sql = "CALL sp_generate_lembur_single('".$id."', '".$nik."', '".$gc."', '".$date."', '".$absen."', '".$loc."', '".$act."', '".$co."' )";
		$query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        foreach ( $query->result_array() as $row ) {  $temp_result [] = $row; }
        $this->db->close();
        return $temp_result;
	}
	
	function get_lembur($company, $periode)
    {
		$sql = "SELECT ID, EMPLOYEE_CODE, emp.NAMA AS NAMA, GANG_CODE, LHM_DATE, TYPE_ABSENSI, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE,";
		$sql .= " LEMBUR_JAM, LEMBUR_RUPIAH, m_gang_activity_detail.COMPANY_CODE AS COMPANY_CODE FROM m_gang_activity_detail  ";
		$sql .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ";
		$sql .= " ON emp.NIK = m_gang_activity_detail.EMPLOYEE_CODE ";
		$sql .= " WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."'";
		$sql .= " AND LEMBUR_JAM > 0";
        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }    
        return $temp_result;  
    }
	/* end lembur */
	
	/* kontanan */
	function read_employee_kontanan($periode, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
        $sql2 = "SELECT ID, EMPLOYEE_CODE, emp.NAMA AS NAMA, GANG_CODE, LHM_DATE, TYPE_ABSENSI, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE,";
		$sql2 .= " KONTANAN, POTONGAN_KONTANAN, m_gang_activity_detail.COMPANY_CODE AS COMPANY_CODE FROM m_gang_activity_detail  ";
		$sql2 .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ";
		$sql2 .= " ON emp.NIK = m_gang_activity_detail.EMPLOYEE_CODE ";
		$sql2 .= " WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."'";
		$sql2 .= " AND KONTANAN <> 0";

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

		$sql = $sql2;
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
		 
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
                    array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->EMPLOYEE_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LHM_DATE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->TYPE_ABSENSI,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KONTANAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->POTONGAN_KONTANAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      
        return $jsonObject;
    }
	
	function get_kontanan($company, $periode)
    {
		$sql = "SELECT ID, EMPLOYEE_CODE, emp.NAMA AS NAMA, GANG_CODE, LHM_DATE, TYPE_ABSENSI, LOCATION_TYPE_CODE, LOCATION_CODE, ACTIVITY_CODE,";
		$sql .= " KONTANAN, POTONGAN_KONTANAN, m_gang_activity_detail.COMPANY_CODE AS COMPANY_CODE FROM m_gang_activity_detail  ";
		$sql .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp ";
		$sql .= " ON emp.NIK = m_gang_activity_detail.EMPLOYEE_CODE ";
		$sql .= " WHERE m_gang_activity_detail.COMPANY_CODE = '".$company."' AND DATE_FORMAT(LHM_DATE, '%Y%m') = '".$periode."'";
		$sql .= " AND KONTANAN <> 0";
        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }    
        return $temp_result;  
    }
	/* end kontanan */
	
	function read_employee_tunpot($periode, $company, $nik, $nama)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		if($nik != '' && $nik != '-') { 
			$nik = " AND gt.NIK LIKE '%". $nik."%'";
		} else {
			$nik = "";
		}
		
		if($nama != '' && $nama != '-') { 
			$nama = " AND emp.NAMA LIKE '%".$nama."%'";
		} else {
			$nama = "";
		}
		
        $sql2 = "SELECT gt.NIK, PERIODE, TUNJANGAN_CUTI, KOMPENSASI_CUTI, emp.NAMA, TUNJANGAN_JABATAN, POTONGAN_LAIN, SUBSIDI_KENDARAAN,";
		$sql2 .= " TUNJ_TRANSPORT, RAPEL, THR, PPH_21, KETERANGAN, COMPANY_CODE FROM m_gad_tambahan gt";
		$sql2 .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 )	";
		$sql2 .= " emp ON emp.NIK = gt.NIK WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."' ".$nik." ".$nama." ";

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
		
		$sql = $sql2;
		
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TUNJANGAN_JABATAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TUNJANGAN_CUTI,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->KOMPENSASI_CUTI,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->POTONGAN_LAIN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->SUBSIDI_KENDARAAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->TUNJ_TRANSPORT,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->RAPEL,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->THR,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PPH_21,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->KETERANGAN,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      
        return $jsonObject;
	}
	
	function get_tunpot($company, $periode)
    {
		$sql = "SELECT gt.NIK, emp.NAMA, COALESCE(TUNJANGAN_JABATAN,0) AS TUNJANGAN_JABATAN, TUNJANGAN_CUTI, COALESCE(KOMPENSASI_CUTI,0) AS KOMPENSASI_CUTI, COALESCE(POTONGAN_LAIN,0) AS POTONGAN_LAIN,";
		$sql .= " COALESCE(SUBSIDI_KENDARAAN,0) AS SUBSIDI_KENDARAAN, COALESCE(TUNJ_TRANSPORT,0) AS TUNJ_TRANSPORT, COALESCE(RAPEL,0) AS RAPEL,";
		$sql .= " COALESCE(POTONGAN_BPJS_KES,0) AS POT_BPJS_KES, COALESCE(TUNJANGAN_BPJS_KES,0) AS TUNJ_BPJS_KES, COALESCE(THR,0) AS THR, COALESCE(PPH_21,0) AS PPH_21, KETERANGAN FROM m_gad_tambahan gt ";
		$sql .= " LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 )";
		$sql .= " emp ON emp.NIK = gt.NIK WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."'";
        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }    
        return $temp_result;  
    }
	
	/* new cek employee #20111017 - ridhu */
	function lookup_employee($company,$nik)
    {
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8'); 
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NIK LIKE '%".$nik."%' AND INACTIVE = 0 ");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
	/* end cek employee*/
	
	function cek_tp_employee($nik,$periode,$company)
    {
        $count='';
        if(isset($nik) && isset($periode) && isset($company))
        {
            $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8') ;
            $periode=htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8') ;
            $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8') ;
            
            $query="SELECT NIK FROM m_gad_tambahan WHERE NIK ='".$nik."' AND PERIODE='".$periode."' AND COMPANY_CODE='".$company."'";
            $sQuery=$this->db->query($query);
            $count=$sQuery->num_rows();
        }

        return $count ;
    }
	
	function cek_employee($company,$nik)
    {
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8'); 
        $query = $this->db->query("SELECT NIK,NAMA FROM m_employee WHERE company_code = '".$company."' AND NIK = '".$nik."' AND INACTIVE = 0 ");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
	/* tunjangan & potongan */
	/* function untuk hitung BPJS */
	/* get data bpjs */
	function read_employee_bpjs($periode, $company, $filt)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		if($filt != '' && $filt != '-') { 
			$filt = " AND gt.NIK LIKE '%". $filt."%' OR COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."' 
					 emp.NAMA LIKE '%".$filt."%' ";
		} else {
			$filt = "";
		}
        $sql2 = "SELECT gt.NIK AS NIK, PERIODE, emp.NAMA AS NAMA, emp.TYPE_KARYAWAN, emp.NO_REG_BPJS_TNG, emp.NO_REG_BPJS_KES, TUNJANGAN_BPJS_KES, TUNJANGAN_BPJS_TNG,";
		$sql2 .= " POTONGAN_BPJS_KES, POTONGAN_BPJS_TNG, COMPANY_CODE FROM m_gad_tambahan gt";
		$sql2 .= " LEFT JOIN ( SELECT NIK, NAMA, TYPE_KARYAWAN,NO_REG_BPJS_TNG,NO_REG_BPJS_KES FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 )	";
		$sql2 .= " emp ON emp.NIK = gt.NIK WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."' ".$filt." ";
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
		$sql = $sql2;
		if( $count >0 ) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TYPE_KARYAWAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_REG_BPJS_KES,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TUNJANGAN_BPJS_KES,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->POTONGAN_BPJS_KES,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_REG_BPJS_TNG,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TUNJANGAN_BPJS_TNG,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->POTONGAN_BPJS_TNG,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    //array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
        }
        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      
        return $jsonObject;
	}
	function getKoefBPJS($periode){
		$sql = "SELECT PERSENTASE_POTONGAN, PERSENTASE_TUNJANGAN FROM m_koefisien_bpjs WHERE DATE_FORMAT(VALID_FROM,'%Y%m') <= '".$periode."'
	AND DATE_FORMAT(VALID_UNTILL,'%Y%m') >= '".$periode."'";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($sql);
		$data = array_shift($query->result_array());
		$temp = $data['PERSENTASE_POTONGAN']."|".$data['PERSENTASE_TUNJANGAN'];
		$this->db->close();
		return $temp;
	}
	function getKaryawanBPJSKes($company){
		$query = $this->db->query("SELECT * FROM m_employee WHERE COMPANY_CODE = '".$company."' AND ISBPJS_KESEHATAN = 1 AND INACTIVE = 0 ");
        //$query = $this->db->query($sql);
        $temp = $query->row_array();
        $temp_result = array(); 
        foreach ( $query->result_array() as $row ) {  $temp_result [] = $row; }
        $this->db->close();
        return $temp_result;
	}
	function cek_existBPJS($nik, $periode, $company) {
        $query = $this->db->query("SELECT NIK FROM m_gad_tambahan WHERE NIK = '".$nik."' AND PERIODE = '".$periode."'");
        $count = $query->num_rows();
        return $count;
	}
	function insertBPJS($data){
		$this->db->insert( 'm_gad_tambahan', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	function updateBPJS($nik, $periode, $data){
		$this->db->where( 'NIK',$nik );
        $this->db->where( 'PERIODE',$periode);
        $this->db->update( 'm_gad_tambahan', $data ); 
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	/* end hitung BPJS */
}

?>