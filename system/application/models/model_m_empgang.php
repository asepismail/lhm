<?

class model_m_empgang extends Model 
{

    function model_m_empgang()
    {
        parent::Model(); 

		$this->load->database();
    }
	
	function insert_m_empgang ( $data )
	{
		$this->db->insert( 'm_empgang', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_empgang ( $id,$nik,$bulan,$tahun, $data )
	{
		$this->db->where( 'GANG_CODE', $id );  
		$this->db->where( 'EMPLOYEE_CODE', $nik );
		$this->db->where( 'm_empgang.MONTH', $bulan );
		$this->db->where( 'm_empgang.TAHUN', $tahun );
		  
		$this->db->update( 'm_empgang', $data );   
	}
	
		
	function delete_m_empgang ( $id,$nik,$bulan,$tahun,$company)
	{
		$this->db->where( 'GANG_CODE', $id );  
		$this->db->where( 'EMPLOYEE_CODE', $nik );
		$this->db->where( 'MONTH', $bulan );
		$this->db->where( 'YEAR', $tahun );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('m_empgang');   
	} 
	
	function cek_exist_empgang($company,$nik,$periode)
    {
        $company = trim($this->db->escape_str($company));
        $nik = trim($this->db->escape_str($nik));
        $periode = trim($this->db->escape_str($periode));
        $query = $this->db->query("SELECT GANG_CODE, EMPLOYEE_CODE, emp.NAMA AS NAMA FROM m_empgang g
		LEFT JOIN ( SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE = 0 ) emp
			ON emp.NIK = g.EMPLOYEE_CODE
		WHERE g.COMPANY_CODE = '".$company."' AND EMPLOYEE_CODE='".$nik."' 
			AND CONCAT(YEAR,MONTH) = '".$periode."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
	
	function cek_exist_employee($company,$nik)
    {
        $company = trim($this->db->escape_str($company));
        $nik = trim($this->db->escape_str($nik));
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NIK='".$nik."' AND INACTIVE = 0");
        $count=$query->num_rows();
      
        return $count;
    }
	
	function search_empgang($gc,$periode,$company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		if (isset($gc)){
			$gc = $gc;
		} else {
			$gc = "";
		}
				
		$where = "WHERE 1=1"; 
		if($gc!='' && $gc!='-') $where.= " AND eg.GANG_CODE LIKE '%$gc%'"; 
				
		$sql2 = "SELECT eg.GANG_CODE AS GANG_CODE, DESCRIPTION, eg.EMPLOYEE_CODE AS NIK, emp.NAMA FROM m_empgang eg
JOIN (SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."') emp ON emp.NIK =  eg.EMPLOYEE_CODE
LEFT JOIN m_gang g ON g.GANG_CODE = eg.GANG_CODE 
".$where." AND eg.COMPANY_CODE = '".$company."' AND eg.GANG_CODE <> '' AND CONCAT(eg.YEAR, eg.MONTH) = '".$periode."' GROUP BY eg.EMPLOYEE_CODE,eg.YEAR, eg.MONTH";
		
		
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
				
		$sql = "SELECT eg.GANG_CODE AS GANG_CODE, DESCRIPTION as DESCRIPTION, eg.EMPLOYEE_CODE AS NIK, emp.NAMA as NAMA FROM m_empgang eg 
JOIN (SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."') emp ON emp.NIK =  eg.EMPLOYEE_CODE
LEFT JOIN m_gang g ON g.GANG_CODE = eg.GANG_CODE
".$where." AND eg.COMPANY_CODE = '".$company."' AND eg.GANG_CODE <> '' AND CONCAT(eg.YEAR, eg.MONTH) = '".$periode."' GROUP BY eg.EMPLOYEE_CODE,eg.YEAR, eg.MONTH ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		$no = 1;											
		foreach($objects as $obj)
        {
            $cell = array();
					
					array_push($cell, $no);
					array_push($cell, $obj->GANG_CODE);
                    array_push($cell, $obj->DESCRIPTION);
					array_push($cell, $obj->NIK);
					array_push($cell, $obj->NAMA);
					array_push($cell, "");
					
			$row = new stdClass();
            $row->id = $no;
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
	
    function search_empgang_detail($name,$nik,$gc,$periode,$company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        if (isset($gc)){
            $gc = htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        } else {
            $gc = "";
        }
        if (isset($nik)){
            $nik = htmlentities($this->db->escape_str($nik),ENT_QUOTES,'UTF-8');
        } else {
            $nik = "";
        }
        if (isset($name)){
            $name = htmlentities($this->db->escape_str($name),ENT_QUOTES,'UTF-8');
        } else {
            $name = "";
        }
        $periode =htmlentities($this->db->escape_str($periode),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
              
        $where = "WHERE 1=1"; 
        if($gc!='' && $gc!='-') $where.= " AND eg.GANG_CODE LIKE '%$gc%'";
        if($nik!='' && $nik!='-') $where.= " AND eg.EMPLOYEE_CODE LIKE '%$nik%'";
        if($name!='' && $name!='-') $where.= " AND emp.NAMA LIKE '%$name%'"; 
                
        $sql2 = "SELECT eg.GANG_CODE AS GANG_CODE, DESCRIPTION, eg.EMPLOYEE_CODE AS NIK, emp.NAMA FROM m_empgang eg
JOIN (SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."') emp ON emp.NIK =  eg.EMPLOYEE_CODE
LEFT JOIN m_gang g ON g.GANG_CODE = eg.GANG_CODE 
".$where." AND eg.COMPANY_CODE = '".$company."' AND eg.GANG_CODE <> '' AND CONCAT(eg.YEAR, eg.MONTH) = '".$periode."' GROUP BY eg.EMPLOYEE_CODE,eg.YEAR, eg.MONTH";
        
        
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
                
        $sql = "SELECT eg.GANG_CODE AS GANG_CODE, DESCRIPTION as DESCRIPTION, eg.EMPLOYEE_CODE AS NIK, emp.NAMA as NAMA FROM m_empgang eg 
JOIN (SELECT NIK, NAMA FROM m_employee WHERE COMPANY_CODE = '".$company."') emp ON emp.NIK =  eg.EMPLOYEE_CODE
LEFT JOIN m_gang g ON g.GANG_CODE = eg.GANG_CODE
".$where." AND eg.COMPANY_CODE = '".$company."' AND eg.GANG_CODE <> '' AND CONCAT(eg.YEAR, eg.MONTH) = '".$periode."' GROUP BY eg.EMPLOYEE_CODE,eg.YEAR, eg.MONTH ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $no = 1;                                            
        foreach($objects as $obj)
        {
            $cell = array();
                    
                    array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->GANG_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->NAMA,ENT_QUOTES,'UTF-8'));
                    array_push($cell, "");
                    
            $row = new stdClass();
            $row->id = $no;
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
