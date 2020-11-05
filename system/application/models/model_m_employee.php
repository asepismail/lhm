<?

class model_m_employee extends Model 
{
    private $table_list;
    private $table_name;
    
    function __construct()
    {
        parent::Model(); 

        $this->load->database();
        //$this->set_table_used();
    }
	    
    function read_employee($company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $user = $this->session->userdata('USER_LEVEL');
		
		$where = "";
		if($company == 'ASL'){
			if($user != "SAS" || $user != "SAD"){
				$where = " AND TYPE_KARYAWAN <> 'BULANAN'";
			}
		}

        $sql2 = "select * FROM m_employee WHERE COMPANY_CODE = '".$company."' AND INACTIVE='false'" . $where ;

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

        
        $sql = "select * FROM m_employee WHERE  INACTIVE='false' AND COMPANY_CODE = '".$company."'" . $where ;
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
					array_push($cell, htmlentities(strtoupper($obj->NAMA),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->TYPE_KARYAWAN),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->GP,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->HK,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->PANGKAT),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->JABATAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->COST_CENTER),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->DEPT_CODE),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->ESTATE_CODE),ENT_QUOTES,'UTF-8'));
					$dateJoin = new DateTime($obj->DATE_JOIN);
					$datePromote = new DateTime($obj->DATE_PROMOTION);
					array_push($cell, htmlentities($dateJoin->format('Y-m-d')));
					array_push($cell, htmlentities($datePromote->format('Y-m-d')));
					array_push($cell, htmlentities(strtoupper($obj->FAMILY_STATUS),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->LAST_EDUCATION),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ALAMAT,ENT_QUOTES,'UTF-8')); 
					array_push($cell, htmlentities($obj->PHONE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->TAX_STATUS),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_JAMSOSTEK,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_NPWP,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DIVISION_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_IDENTITAS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->RELIGION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities(strtoupper($obj->SEX),ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TANGGAL_LAHIR,ENT_QUOTES,'UTF-8'));          
					array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NAMA_SEKOLAH,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->JURUSAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ALAMAT_SEKOLAH,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISBPJS_KETENAGAKERJAAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_REG_BPJS_TNG,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISBPJS_KESEHATAN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NO_REG_BPJS_KES,ENT_QUOTES,'UTF-8'));
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
    
    function cek_exist_employee($company,$nik,$type)
    {
        $company = trim($this->db->escape_str($company));
        $nik = trim($this->db->escape_str($nik));
        $type = trim($this->db->escape_str($type));
        $query = $this->db->query("SELECT * FROM m_employee WHERE company_code = '".$company."' AND NIK='".$nik."' AND TYPE_KARYAWAN='".$type."' AND INACTIVE = 0");
        $count=$query->num_rows();
      
        return $count;
    }
    
    function search_employee($nik,$name, $dept,$inactive, $company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $limit=($limit==0 | $limit==null)?1:$limit;
        
		
		
        if (isset($nik)){
            $nik = htmlentities($nik,ENT_QUOTES,'UTF-8');
        } else {
            $nik = "";
        }
        
        if (isset($name)){
            $name = htmlentities($name,ENT_QUOTES,'UTF-8');
        } else {
            $name = "";
        }
            
        if (isset($dept)){
            $dept =htmlentities($dept,ENT_QUOTES,'UTF-8');
        } else {
            $dept = "";
        }
        
        $inactive=(isset($inactive))?$inactive:"";
        
        $where = "WHERE 1=1"; 
        if($nik!='' && $nik!='-') $where.= " AND NIK LIKE '%$nik%'"; 
        if($name!='' && $name!='-') $where.= " AND NAMA LIKE '%$name%'"; 
        if($dept!='' && $dept!='-') $where.= " AND DEPT_CODE LIKE '%$dept%'";
        if($inactive!='') 
        {
            $where .=" AND INACTIVE='$inactive' ";    
        }
	
	
       
        $where .= " AND COMPANY_CODE = '".$company."'";
        
        $sql2 = "select * FROM m_employee ". $where;
        
        
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
        
        
        $sql = "select * FROM m_employee ".$where." ";
		if ($count > 0 ){
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
    
            array_push($cell, htmlentities($obj->NIK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->NAMA),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->TYPE_KARYAWAN),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->GP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities(strtoupper($obj->PANGKAT),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->JABATAN),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->COST_CENTER),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->DEPT_CODE),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->ESTATE_CODE),ENT_QUOTES,'UTF-8'));
            $dateJoin = new DateTime($obj->DATE_JOIN);
			$datePromote = new DateTime($obj->DATE_PROMOTION);
            array_push($cell, htmlentities($dateJoin->format('Y-m-d')));
			array_push($cell, htmlentities($datePromote->format('Y-m-d')));
            array_push($cell, htmlentities(strtoupper($obj->FAMILY_STATUS),ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities(strtoupper($obj->LAST_EDUCATION),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ALAMAT,ENT_QUOTES,'UTF-8')); 
            array_push($cell, htmlentities($obj->PHONE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->TAX_STATUS),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_JAMSOSTEK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_NPWP,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DIVISION_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_IDENTITAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->RELIGION),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities(strtoupper($obj->SEX),ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TANGGAL_LAHIR,ENT_QUOTES,'UTF-8'));          
            array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NAMA_SEKOLAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JURUSAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ALAMAT_SEKOLAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISBPJS_KETENAGAKERJAAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_REG_BPJS_TNG,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISBPJS_KESEHATAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_REG_BPJS_KES,ENT_QUOTES,'UTF-8'));
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
    
    function insert_m_employee ( $data )
    {
        $status='';
        if(isset($data)) {
            $this->db->insert('m_employee',$data); 
            $status= $this->db->insert_id();    
        }
        
        return $status;
    }
    
    function update_m_employee ( $id, $company, $data )
    {
        $id =$this->db->escape_str($id);
        $company =$this->db->escape_str($company);
        
        $query="SElECT * FROM m_employee WHERE NIK='".$id."' AND company_code='".$company."' AND INACTIVE = 0";
        $sQuery = $this->db->query($query);
        $num_rows = $sQuery->num_rows();
        
        if($num_rows>0) {
            $this->db->where( 'NIK',$id );
            $this->db->where( 'COMPANY_CODE',$company);
			$this->db->where( 'INACTIVE',0);
            $this->db->update( 'm_employee', $data ); 
            $status=0;
        } else {
            //$status = "data tidak ada";
        	$this->db->where( 'NIK',$id );
            $this->db->where( 'COMPANY_CODE',$company);
			$this->db->where( 'INACTIVE',1);
            $this->db->update( 'm_employee', $data ); 
            $status=0;
		} 
        return $status;  
    }   
    
    function delete_m_employee ( $id, $company)
    { 
        $id=$this->db->escape_str($id);
        $company=$this->db->escape_str($company);
        
        $query="SElECT * FROM m_employee WHERE NIK='".$id."' AND company_code='".$company."'";
        $sQuery = $this->db->query($query);
        $num_rows = $sQuery->num_rows();
        if($num_rows>0) {
            $temp_result = array() ;
            foreach ($sQuery->result_array() as $row ) {
                $temp_result[] = $row;
            }
            
            $data_array=array();
            foreach($temp_result as $val) {
                foreach($val as $key => $value) {
                    if ($key=='E_INPUT_DATE') {
                        $data_array[$key]=date ("Y-m-d H:i:s");     
                    } else if ($key=='E_INPUT_BY'){
                        $loginid=htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
                        $data_array[$key]= $loginid;  
                    } else {
                         $data_array[$key]=$value;    
                    } 
                }
            }
            $this->insert_history($data_array,$id,$company);
            
            $data_array['Action']="delete";
            $this->insert_record($data_array);
            unset($data_array);
            
			$this->db->set('INACTIVE', 1);
			$this->db->set('INACTIVE_DATE', date ("Y-m-d"));
			
            $this->db->where( 'NIK', $id );      
            $this->db->where( 'COMPANY_CODE', $company );      
            $this->db->update('m_employee');
            $status ="success";    
        } else {
            $status="data tidak ada.";
        } 
        return $status;   
    }
    /* cek id employee */
    
    function cek_employee($type, $company)
    {
        $type=$this->db->escape_str($type);
        $company=$this->db->escape_str($company);
        
        $query = $this->db->query("SELECT MAX(NIK) as NIK FROM `m_employee` WHERE COMPANY_CODE = '".$company."' AND TYPE_KARYAWAN LIKE '%".$type."%'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
	
	function getcompanynumber($company){
		$query = $this->db->query("SELECT COMPANY_NUMBER FROM `m_company` WHERE COMPANY_CODE = '".$company."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
	}

    function insert_record($data)//insert history record untuk setiap perubahan data karyawan
    {
        $status='';
        if(isset($data))
        {
            $status= $this->db->insert('m_employee_record', $data);    
        }
        return $status;
    }
    
    function insert_history($data,$id,$company)//insert history untuk setiap mutasi data karyawan
    {    
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $query = "SELECT * FROM m_employee_history WHERE NIK='".$id."' AND COMPANY_CODE ='".$company."'";
        $sQuery = $this->db->query($query);
        $row_count = $sQuery->num_rows();
        $status='';
        if ($row_count > 0)
        {
            $this->db->where( 'NIK',$id );
            $this->db->where( 'COMPANY_CODE',$company);
            $this->db->update( 'm_employee_history', $data );    
        }else{
            
            if(isset($data))
            {
                $status=$this->db->insert('m_employee_history',$data);     
            }   
        } 
        
        return $status; 
    }
    
    function get_field_name($tblName)
    {
        $query = "SELECT * FROM ".$tblName;
        $sQuery =$this->db->query($query);
        
        $field_result=array(); 
        foreach ($sQuery->list_fields() as $field)
        {
           $field_result[]= $field;
        }
        return $field_result; 
    }
    
    function get_employee($nik, $company)
    {
        $nik=$this->db->escape_str($nik);
        $company=$this->db->escape_str($company);
        
        $query = $this->db->query("SELECT *  FROM `m_employee` WHERE COMPANY_CODE = '".$company."' AND NIK = '".$nik."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result[] = $row;
        }

        return $temp_result;
    }
    
     //################### DB VALIDATION #####################
    function cek_array_table_exist($arrDefault,$arrData)
    {
       $cekstatus=''; 
        if(count($arrData)==count($arrDefault))
        {
            $arrDif=array_diff_key($arrDefault, $arrData);
            $countdif=count($arrDif);
            if($countdif==0){
                $cekstatus="true";      
            }else{
                 //$cekstatus=$arrData;
                $cekstatus="data table tidak sama";
            }       
        }elseif(count($arrData)>count($arrDefault))
        {
            $cekstatus="data input lebih besar";    
        }elseif(count($arrData)<count($arrDefault))
        {
            $cekstatus="data input lebih kecil";    
        }else{
            $cekstatus="data tidak sama";
        }
        return $cekstatus; 
    }
	
	/*######### jabatan karyawan ############# */
	function get_dept()
	{
		$query = $this->db->query("SELECT DEPT_CODE,DEPT_DESCRIPTION FROM m_employee_dept WHERE INACTIVE = 0");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_level()
	{
		$query = $this->db->query("SELECT EMP_LEVEL_ID,EMP_LEVEL_DESC FROM m_employee_level WHERE INACTIVE = 0");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_position($dept, $level)
	{
		$query = $this->db->query("SELECT EMP_POSITION_ID,POSITION_DESCRIPTION FROM m_employee_position WHERE DEPT_CODE = '".$dept."' AND LEVEL_CODE = '".$level."' AND INACTIVE = 0");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_education()
	{
		$query = $this->db->query("SELECT ED_ID,CONCAT(EDUCATION_CODE, ' - ', EDUCATION_NAME) AS DESCRIPTION FROM m_employee_education WHERE INACTIVE = 0");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_afd($company)
	{
		$query = $this->db->query("SELECT AFD_CODE,AFD_DESC FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_famstat()
	{
		$query = $this->db->query("SELECT EMPLOYEE_FAMSTAT_CODE,EMPLOYEE_FAMSTAT_DESC FROM m_employee_famstat");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_costcenter($company)
	{
		$query = $this->db->query("SELECT COSTCENTERCODE, CONCAT(COSTCENTERCODE,'-',DESCRIPTION) AS DESCR FROM m_costcenter WHERE COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
}   

?>
