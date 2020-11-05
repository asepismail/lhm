<?

class Model_m_gang extends Model 
{

    function Model_m_gang()
    {
        parent::Model(); 

		$this->load->database();
    }

	
	function insert_m_gang ( $data )
	{
		$this->db->insert( 'm_gang', $data );
		return $this->db->insert_id();   
	}
	
	function update_m_gang ( $gc, $company, $data )
	{
		$this->db->where( 'GANG_CODE', $gc );  
		$this->db->where( 'COMPANY_CODE', $company );  
		$this->db->update( 'm_gang', $data );   
	}
	
	function delete_m_gang ( $gc, $company)
	{
		$this->db->where( 'GANG_CODE', $gc );  	
		$this->db->where( 'COMPANY_CODE', $company );  	
		$this->db->delete('m_gang');   
	}

	function grid_gang($gc, $company)
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
		
		if($gc!='' && $gc!='-') $where.= " AND GANG_CODE LIKE '%$gc%'"; 
		
		$sql2 = "select g.GANG_CODE,g.DESCRIPTION,g.GANG_TYPE,g.MANDORE1_CODE,g.MANDORE_CODE,emp.NAMA,g.KERANI_CODE,g.DEPARTEMEN_CODE,g.DIVISION_CODE,g.FUNCTION_CODE,g.GA_CODE,g.COMPANY_CODE FROM m_gang g left JOIN m_employee emp ON (emp.NIK = g.MANDORE_CODE AND emp.INACTIVE = 0) ".$where." AND g.COMPANY_CODE = '".$company."'";
		
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
		
		$sql = "select g.GANG_CODE,g.DESCRIPTION,g.GANG_TYPE,g.MANDORE1_CODE,g.MANDORE_CODE,emp.NAMA,g.KERANI_CODE,g.DEPARTEMEN_CODE,g.DIVISION_CODE,g.FUNCTION_CODE,g.GA_CODE,g.COMPANY_CODE FROM m_gang g left JOIN m_employee emp ON (emp.NIK = g.MANDORE_CODE AND emp.INACTIVE = 0) ".$where." AND g.COMPANY_CODE = '".$company."' ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";

		$objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
				
		$action = '';									
		foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, $obj->GANG_CODE);
					array_push($cell, $obj->DESCRIPTION);
                    array_push($cell, $obj->GANG_TYPE);
					array_push($cell, $obj->MANDORE1_CODE);
					array_push($cell, $obj->MANDORE_CODE);
					array_push($cell, $obj->NAMA);
                	array_push($cell, $obj->KERANI_CODE);
					array_push($cell, $obj->DEPARTEMEN_CODE);
					array_push($cell, $obj->DIVISION_CODE);
					array_push($cell, $obj->FUNCTION_CODE);
					array_push($cell, $obj->GA_CODE);
					array_push($cell, $obj->COMPANY_CODE);
					array_push($cell, $action);
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
	
	//lookup value 
	function cek_mandor($ec,$company){
		
		$query = $this->db->query("SELECT NIK, NAMA FROM `m_employee` WHERE JABATAN LIKE '%MANDOR%' AND JABATAN NOT LIKE 'MANDOR I%' AND NIK LIKE '".$ec."%' AND COMPANY_CODE = '".$company."'");
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function cek_kerani($ec,$company){
		
		$query = $this->db->query("SELECT NIK, NAMA FROM `m_employee` WHERE JABATAN LIKE '%KERANI%' AND NIK LIKE '".$ec."%' AND COMPANY_CODE = '".$company."'");
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}      
	
	function cek_mandori($ec,$company){
		
		$query = $this->db->query("SELECT NIK, NAMA FROM `m_employee` WHERE JABATAN LIKE 'MANDOR I%' AND NIK LIKE '".$ec."%' AND COMPANY_CODE = '".$company."'");
		$temp_result = array();
		
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}  
	
	function cek_nik_kosong($nik,$periode,$company){
		
		$query = $this->db->query("SELECT emp_kosong.NIK, emp_kosong.NAMA,  emp_kosong.GANG_CODE FROM 
( SELECT NIK, NAMA, emp.GANG_CODE FROM m_employee 
LEFT JOIN ( SELECT EMPLOYEE_CODE, GANG_CODE FROM m_empgang WHERE COMPANY_CODE = '".$company."' AND CONCAT(m_empgang.YEAR,m_empgang.MONTH) = '".$periode."' AND GANG_CODE <> '' ) emp
ON emp.EMPLOYEE_CODE = m_employee.NIK WHERE m_employee.COMPANY_CODE = '".$company."' ) emp_kosong
WHERE emp_kosong.NIK like '".$nik."%' AND SUBSTR(emp_kosong.NIK,1,4) NOT IN ('1307','1308','1309') AND emp_kosong.GANG_CODE IS NULL ");
		$temp_result = array();
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}
		return $temp_result;
	}      

}   

?>
