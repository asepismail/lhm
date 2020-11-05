<?

class Model_m_user extends Model 
{

    function Model_m_user()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_user ( $id ){
		$this->db->select( 'LOGINID,USER_FULLNAME,USER_PASS,USER_MAIL,USER_LEVEL,USER_DEPT,COMPANY_CODE' );
		$this->db->where( 'LOGINID', $id );
        $this->db->where( 'ACTIVE', '1' );
		$this->db->from('m_user');
		$query = $this->db->get();
		if ( $query->num_rows() > 0 ){
			$row = $query->row_array();
			return $row;
		}
	}
	
	function insert_m_user ( $data ){
		$this->db->insert( 'm_user', $data );
		return $this->db->insert_id();   
	}
	
	function update_m_user ( $id, $data ){
		$this->db->where( 'LOGINID', $id );  
		$this->db->update( 'm_user', $data );   
	}
	
	function enroll_m_user (){
		$this->db->select( 'LOGINID,USER_FULLNAME,USER_PASS,USER_MAIL,USER_LEVEL,USER_DEPT,COMPANY_CODE');
		$this->db->from( 'm_user' );
		$query = $this->db->get();
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}
		return $temp_result;
	}
	
	function delete($id){
		$this->db->where('LOGINID', $id);
		$this->db->delete('m_user'); 
	}

	//TODO: check XSS and SQL injection here
    function readByPagination(){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_user');

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
        //$this->db->order_by("$sidx", "$sord");
        $objects = $this->db->get("m_user")->result();
        $rows =  array();

        foreach($objects as $obj){
			//$kosong = "";
            $cell = array();
                            array_push($cell, $obj->LOGINID);
                            array_push($cell, $obj->USER_FULLNAME);
							array_push($cell, $obj->USER_PASS);
							array_push($cell, $obj->USER_MAIL);
                            array_push($cell, $obj->USER_LEVEL);
                            array_push($cell, $obj->USER_DEPT);
							array_push($cell, $obj->COMPANY_CODE);
							//array_push($cell, $kosong);
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
	
	
	function cek_user ($id, $co, $pass){
		$act=1;
		$sql = "SELECT * FROM m_user WHERE LOGINID = ? AND USER_PASS = ? AND COMPANY_CODE = ? AND ACTIVE=?";

		$this->db->query($sql, array($id, $pass, $co, $act)); 
		$query = $this->db->get();
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}
		return $temp_result;	
	}
	
	function login($data){
		if($data == '') {
            return false;
        }
		
		$act=0;
		$this->db->select('m_user.LOGINID,USER_FULLNAME,USER_PASS,USER_MAIL,pma.MODULE_ACCESS AS MODULE_ACCESS, USER_LEVEL,USER_DEPT, m_user.COMPANY_CODE as NCOMPANY,d.`COMPANY_NAME` as NCOMPANY_NAME, m_user_co.`COMPANY_CODE` as DCOMPANY, `m_company`.`COMPANY_NAME` as DCOMPANY_NAME, CASE WHEN m_user_group.USER_GROUP_NAME IS NULL 
	THEN pugm.PMSUSERGROUP_ID
ELSE  m_user_group.USER_GROUP_NAME END AS GROUP_USER', FALSE);
		
		$pass = md5($data["upass"]);
		$this->db->from( 'm_user_co' );
		$this->db->join('m_user','m_user.LOGINID = m_user_co.USERID', 'left');
		$this->db->join('m_user_group','m_user_group.USER_GROUP_ID = m_user.USER_LEVEL', 'left');
		$this->db->join('pms_user_group_map pugm','pugm.LOGINID = m_user.LOGINID', 'left');
		$this->db->join('pms_user_group pug','pug.PMSUSERGROUP_ID = pugm.PMSUSERGROUP_ID', 'left');
		$this->db->join('m_user_module_access pma','pma.LOGINID = m_user.LOGINID', 'left');
		$this->db->join('m_company','m_company.COMPANY_CODE = m_user_co.COMPANY_CODE', 'left');
		$this->db->join('m_company d','d.COMPANY_CODE = m_user.COMPANY_CODE', 'left');
		
		$this->db->where('m_user.LOGINID',$data['uname']);
		$this->db->where('m_user.USER_PASS', $pass);
        $this->db->where('m_user.INACTIVE', $act); 
		$this->db->where('m_user_co.COMPANY_CODE', $data['usite']);
		$this->db->where('pma.MODULE_ACCESS', $data['modul']);
		
		
		$query = $this->db->get();
		$temp = $query->row_array();
		$this->db->close();
		return $temp;
	}
	
	function insert_m_user_co ( $data ){
		$this->db->insert( 'm_user_co', $data );
		return $this->db->insert_id();   
	}
	
	function update_m_user_co ( $id, $data ){
		$this->db->where( 'USERID', $id );  
		$this->db->update( 'm_user_co', $data );   
	}
	
	//TODO: check XSS and SQL injection here
    function setRole_list($userid){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

       $sql_count = "SELECT c.COMPANY_CODE,c.COMPANY_NAME,  
			(CASE d.com_access WHEN IFNULL(d.com_access,'1') THEN '1' ELSE '0' END) AS com_access
			FROM m_company c  LEFT JOIN 
			(SELECT a.COMPANY_CODE, a.COMPANY_NAME, b.COMPANY_CODE AS com_access FROM m_company a
			LEFT JOIN m_user_co b ON a.COMPANY_CODE=b.COMPANY_CODE WHERE b.USERID= '".$userid."') AS d
			ON c.COMPANY_CODE=d.COMPANY_CODE";

        if(!$sidx) $sidx =1;
		$query = $this->db->query($sql_count);
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
        $sql = "SELECT c.COMPANY_CODE,c.COMPANY_NAME,  
			(CASE d.com_access WHEN IFNULL(d.com_access,'1') THEN '1' ELSE '0' END) AS com_access
			FROM m_company c  LEFT JOIN 
			(SELECT a.COMPANY_CODE, a.COMPANY_NAME, b.COMPANY_CODE AS com_access FROM m_company a
			LEFT JOIN m_user_co b ON a.COMPANY_CODE=b.COMPANY_CODE WHERE b.USERID= '".$userid."') AS d
			ON c.COMPANY_CODE=d.COMPANY_CODE ";
 		
		$objects = $this->db->query($sql,FALSE)->result(); 
		 $rows =  array();

        foreach($objects as $obj)
        {
            $cell = array();
                            array_push($cell, $obj->COMPANY_CODE);
                            array_push($cell, $obj->COMPANY_NAME);
                            array_push($cell, $obj->com_access);
                           							
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
	
		
	function delete_m_user_menu($id)
	{
		$this->db->where('ID', $id);
		$this->db->delete('m_user_co'); 
	}
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function getModule(){
		$query = $this->db->query("SELECT MODULE_ID, MODULE_NAME FROM m_system_module WHERE INACTIVE = 0");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function validateUser($user){
		$this->db->select('LOGINID');
		$this->db->from( 'm_user' );
		$this->db->where('LOGINID', $user);
		$query = $this->db->get();
		$temp = $query->row_array();
		$this->db->close();
		return $temp;
	}
	
	function validatePassword($user, $pass){
		$this->db->select('LOGINID, USER_PASS');
		$this->db->from( 'm_user' );
		$this->db->where('LOGINID', $user);
		$this->db->where('USER_PASS', md5($pass));
		$query = $this->db->get();
		$temp = $query->row_array();
		$this->db->close();
		return $temp;
	}
	
	function validateCompany($user, $company){
		$this->db->select('USERID, COMPANY_CODE');
		$this->db->from( 'm_user_co' );
		$this->db->where('USERID', $user);
		$this->db->where('COMPANY_CODE',$company);
		$query = $this->db->get();
		$temp = $query->row_array();
		$this->db->close();
		return $temp;
	}
	
	function validateModule($user, $module){
		$this->db->select('LOGINID, MODULE_ACCESS');
		$this->db->from( 'm_user_module_access' );
		$this->db->where('LOGINID', $user);
		$this->db->where('MODULE_ACCESS',$module);
		$query = $this->db->get();
		$temp = $query->row_array();
		$this->db->close();
		return $temp;
	}
	
	function get_dept(){
		$query = $this->db->query("SELECT DEPT_CODE,DEPT_DESCRIPTION FROM m_employee_dept WHERE INACTIVE = 0");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function read_user($company,$level, $searchField ="", $searchString ="", $searchOper = ""){
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = " AND 1=1";
		if($company != 'PAG' && $level != 'SAD'){
			$where = " AND COMPANY_CODE = '".$company."'";
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
        $sql2 = "SELECT LOGINID,USER_FULLNAME,USER_MAIL,USER_PASS,USER_LEVEL,ug.USER_GROUP_NAME,USER_DEPT,dp.DEPT_DESCRIPTION, ";
		$sql2 .= "COMPANY_CODE, us.INACTIVE FROM m_user us LEFT JOIN m_user_group ug ON ug.USER_GROUP_ID = us.USER_LEVEL ";
		$sql2 .= "LEFT JOIN m_employee_dept dp ON dp.DEPT_CODE = us.USER_DEPT WHERE us.INACTIVE = 0	".$where."";
		
		
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
		$no = 0;                    
        foreach($objects as $obj){
            $cell = array();               
            array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LOGINID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_FULLNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_PASS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_MAIL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_LEVEL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_GROUP_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->USER_DEPT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DEPT_DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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
	
	function getRole_gangcode($userid, $company) {
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

    	$sql_count = "SELECT u.LOGINID, ud.DETAIL_CODE, g.DESCRIPTION,ud.COMPANY_CODE  FROM m_user u ";
		$sql_count .= "LEFT JOIN m_user_per_detail ud ON ud.LOGINID = u.LOGINID AND ud.COMPANY_CODE = u.COMPANY_CODE ";
		$sql_count .= "LEFT JOIN m_gang g ON g.GANG_CODE = ud.DETAIL_CODE AND g.COMPANY_CODE = ud.COMPANY_CODE ";
		$sql_count .= "WHERE DETAIL_TYPE = 'GANG_CODE' AND u.LOGINID = '".$userid."' AND u.COMPANY_CODE = '".$company."'";

        if(!$sidx) $sidx =1;
		$query = $this->db->query($sql_count);
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
        $sql = "SELECT u.LOGINID, ud.DETAIL_CODE, g.DESCRIPTION, ud.COMPANY_CODE  FROM m_user u ";
		$sql .= "LEFT JOIN m_user_per_detail ud ON ud.LOGINID = u.LOGINID AND ud.COMPANY_CODE = u.COMPANY_CODE ";
		$sql .= "LEFT JOIN m_gang g ON g.GANG_CODE = ud.DETAIL_CODE AND g.COMPANY_CODE = ud.COMPANY_CODE ";
		$sql .= "WHERE DETAIL_TYPE = 'GANG_CODE' AND u.LOGINID = '".$userid."' AND u.COMPANY_CODE = '".$company."'";
		
		if ($count > 0 ){
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		}
 		
		$objects = $this->db->query($sql,FALSE)->result(); 
		$rows =  array();

		$no = 0;
        foreach($objects as $obj){
            $cell = array();
			array_push($cell, $no);
            array_push($cell, $obj->LOGINID);
			array_push($cell, $obj->DETAIL_CODE);
			array_push($cell, $obj->DESCRIPTION);
            array_push($cell, $obj->COMPANY_CODE);                         							
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
	
	/* delete gc role */
	function delete_gc_role($id,$dc,$company){
		$this->db->where('LOGINID', $id);
		$this->db->where('DETAIL_CODE', $dc);
		$this->db->where('COMPANY_CODE', $company);
		$this->db->where('DETAIL_TYPE', 'GANG_CODE');
		$this->db->delete('m_user_per_detail'); 
	}
	
	/* */
	function insert_gc_role($data){
		$this->db->insert( 'm_user_per_detail', $data );
	}
	
	function cek_exist_gc_role($id, $gc, $company){
        $company = trim($this->db->escape_str($company));
        $id = trim($this->db->escape_str($id));
		$gc = trim($this->db->escape_str($gc));
        $query = $this->db->query("SELECT * FROM m_user_per_detail WHERE LOGINID = '".$id."' AND DETAIL_CODE='".$gc."' AND DETAIL_TYPE = 'GANG_CODE' AND COMPANY_CODE = '".$company."'");
        $count=$query->num_rows();
        return $count;
    }
	
	function cek_role($id, $company){
		$company = trim($this->db->escape_str($company));
        $id = trim($this->db->escape_str($id));
        $query = $this->db->query("SELECT USER_LEVEL FROM m_user WHERE LOGINID = '".$id."' AND COMPANY_CODE = '".$company."'");
        $temp_result = array();
        
        foreach ( $query->result_array() as $row ){
            $temp_result[] = $row;
        }
        return $temp_result;
	}
	
	/* akses menu user */ 
	function getRole_Menu($userid, $company) {
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

    	$sql_count = "SELECT MENU_LIST_ID, mn.LOGINID, mn.MENU_ID, u.MENU_NAME FROM m_user_menu_list mn
						LEFT JOIN m_user_menu u ON u.MENU_ID = mn.MENU_ID 
						LEFT JOIN m_user us ON us.LOGINID = mn.LOGINID 
						WHERE mn.LOGINID = '".$userid."'
						AND us.COMPANY_CODE = '".$company."'";

        if(!$sidx) $sidx =1;
		$query = $this->db->query($sql_count);
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
        $sql = $sql_count;
		
		if ($count > 0 ){
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
		}
 		
		$objects = $this->db->query($sql,FALSE)->result(); 
		$rows =  array();
		$act = '';
		$no = 0;
        foreach($objects as $obj){
            $cell = array();
			array_push($cell, $obj->MENU_LIST_ID);
            array_push($cell, $obj->LOGINID);
			array_push($cell, $obj->MENU_ID);
			array_push($cell, $obj->MENU_NAME);
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
	
	/* fungsi konversi jqgrid ke query */ 
	function getWhereClause($col, $oper, $val){
		$ops = array( 'eq'=>'=', //equal
					  'ne'=>'<>',//not equal
					  'lt'=>'<', //less than
					  'le'=>'<=',//less than or equal
					  'gt'=>'>', //greater than
					  'ge'=>'>=',//greater than or equal
					  'bw'=>'LIKE', //begins with
					  'bn'=>'NOT LIKE', //doesn't begin with
					  'in'=>'LIKE', //is in
					  'ni'=>'NOT LIKE', //is not in
					  'ew'=>'LIKE', //ends with
					  'en'=>'NOT LIKE', //doesn't end with
					  'cn'=>'LIKE', // contains
					  'nc'=>'NOT LIKE'  //doesn't contain
		);   
		if($oper == 'bw' || $oper == 'bn') $val .= '%';
		if($oper == 'ew' || $oper == 'en' ) $val = '%'.$val;
		if($oper == 'cn' || $oper == 'nc' || $oper == 'in' || $oper == 'ni') $val = '%'.$val.'%';
		return " AND $col {$ops[$oper]} '$val' ";
	}
}   

?>