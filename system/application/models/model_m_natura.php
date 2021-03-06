<?php
class model_m_natura extends Model
{
    function __construct()
    {
        parent::Model();
        
        $this->load->database();
    }
    
    function insert_natura($data)
    {
        if(isset($data))
        {
            $this->db->insert( 'm_gad_tambahan', $data );
            $result= $this->db->insert_id();    
        }
        return $result;
    }
	
    function update_natura($id,$periode,$company,$data)
    {
        $result='';
        if(isset($data))
        {
            $this->db->where('NIK', $id );  
            $this->db->where('PERIODE', $periode );  
            $this->db->where('COMPANY_CODE', $company );   
            $this->db->update('m_gad_tambahan', $data );
            $result=0;
        }
        return $result;
    }
    
    function load_data_natura($periode,$dosearch="")
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        $limit=($limit==0 | $limit==null)?1:$limit;
        
		$periode=htmlentities($periode,ENT_QUOTES,'UTF-8'); 
        $company = htmlentities($this->db->escape_str($this->session->userdata('DCOMPANY')),ENT_QUOTES,'UTF-8');  
		
		$where = "";
		if($dosearch == "-"){
			$dosearch ="";
		}
		if( $dosearch != "" ){
			$where = " WHERE gadt.COMPANY_CODE = '".$company."' AND PERIODE ='".$periode."' AND emp.NIK LIKE '%".$dosearch."%' 
					OR gadt.COMPANY_CODE = '".$company."' AND PERIODE ='".$periode."' AND emp.NAMA LIKE '%".$dosearch."%' ";
		} else {
			$where = " WHERE gadt.COMPANY_CODE = '".$company."' AND PERIODE ='".$periode."' ";
		}
		
		/* if($nik != '' && $nik != '-') { 
			$nik = " AND m_gad_tambahan.NIK LIKE '%".$nik."%'";
		} else {
			$nik = "";
		}
		
		if($nama != '' && $nama != '-') { 
			$nama = " AND emp.NAMA LIKE '%".$nama."%'";
		} else {
			$nama = "";
		} */ 
         
        $sql2 = "SELECT gadt.NIK, emp.NAMA, emp.TYPE_KARYAWAN, emp.FAMILY_STATUS, gadt.PERIODE, gadt.NATURA, gadt.COMPANY_CODE
					FROM m_gad_tambahan gadt
				LEFT JOIN
        		( 
						SELECT NIK, NAMA, TYPE_KARYAWAN, FAMILY_STATUS, 
						COMPANY_CODE from m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 
				) emp ON gadt.NIK = emp.NIK " . $where ;

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
        foreach($objects as $obj)
        {
            $cell = array();     
            array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->TYPE_KARYAWAN,ENT_QUOTES,'UTF-8')); 
			array_push($cell, htmlentities($obj->FAMILY_STATUS,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NATURA,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
          
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
    
	function get_natura($company, $periode)
    {
		$sql = "SELECT m_gad_tambahan.NIK, emp.NAMA, emp.TYPE_KARYAWAN, emp.FAMILY_STATUS, ";
		$sql .= " m_gad_tambahan.PERIODE,m_gad_tambahan.NATURA,m_gad_tambahan.COMPANY_CODE";
		$sql .= " FROM m_gad_tambahan LEFT JOIN";
        $sql .= " ( SELECT NIK, NAMA, TYPE_KARYAWAN, FAMILY_STATUS, COMPANY_CODE from m_employee WHERE COMPANY_CODE = '".$company."' ";
		$sql .= " AND INACTIVE = 0 ) emp ON m_gad_tambahan.NIK = emp.NIK";
        $sql .= " WHERE m_gad_tambahan.COMPANY_CODE = '".$company."' AND PERIODE ='".$periode."'";
        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }    
        return $temp_result;  
    }
	
    function search_natura($nik,$periode,$company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company = htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $periode=htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8'); 
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8');  
        
        $where = "WHERE 1=1"; 
        if($nik!='' && $nik!='-') $where.= " AND m_gad_tambahan.NIK LIKE '%$nik%' "; 
        if($periode!='' && $periode!='-') $where.= " AND m_gad_tambahan.PERIODE ='$periode' "; 
        if($company!='' && $company!='-') $where.= " AND m_gad_tambahan.COMPANY_CODE ='$company' ";
        
        $sql2 = "SELECT m_gad_tambahan.NIK,m_employee.NAMA,m_gad_tambahan.PERIODE,m_gad_tambahan.NATURA,m_gad_tambahan.COMPANY_CODE 
                    FROM m_gad_tambahan LEFT JOIN
                    m_employee ON m_gad_tambahan.NIK=m_employee.NIK ".$where."";

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

        
        $sql = "SELECT m_gad_tambahan.NIK,m_employee.NAMA,m_gad_tambahan.PERIODE,m_gad_tambahan.NATURA,m_gad_tambahan.COMPANY_CODE 
                    FROM m_gad_tambahan LEFT JOIN
                    m_employee ON m_gad_tambahan.NIK=m_employee.NIK ".$where." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
                    
            array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NATURA,ENT_QUOTES,'UTF-8')); 
            //array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
          
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
    
    function cek_natura_employee($nik,$periode,$company)
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
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NIK LIKE '%".$nik."%' AND INACTIVE = 0");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
    
	/* new cek employee #20111017 - ridhu */
	function lookup_employee($company,$nik)
    {
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $nik=htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8'); 
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NIK LIKE '%".$nik."%' AND INACTIVE = 0 AND TYPE_KARYAWAN NOT IN ('BHL','KDMP')");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
	/* end cek employee*/
	
    function cek_employee_2($company,$nama)
    {
        $company=htmlentities($company,ENT_QUOTES,'UTF-8');
        $nama=htmlentities($nama,ENT_QUOTES,'UTF-8');
         
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NAMA LIKE '%".$nama."%' AND INACTIVE = 0 ");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
    
    function cek_employee_3($company,$nik)
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
    
    function get_natura_koefisien($company,$status)
    {
        $company=$this->db->escape_str($company);
        $status=$this->db->escape_str($status);
        
        $query="SELECT * FROM m_koefisien_natura WHERE COMPANY_CODE='".$company."' AND STATUS_KARYAWAN LIKE '%".$status."%' ";
        $sQuery=$this->db->query($query);
        
        $temp_result = array();
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result[] = $row;
        }
        return $temp_result;
    }
	
	function generate_natura($periode, $company){
		$company=$this->db->escape_str($company);
        $periode=$this->db->escape_str($periode);
        
        $query="CALL sp_generate_natura('".$company."','".$periode."')";
        $sQuery=$this->db->query($query);
        $ret = "";
        $temp_result = array();
        foreach ( $sQuery->result_array() as $row )
        {
            //$temp_result[] = $row;
			$ret = $row['TOTAL'];
        }
        return $ret;	
	}
}
?>
