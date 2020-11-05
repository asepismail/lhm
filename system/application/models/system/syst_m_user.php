<?php
class syst_m_user extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_user($company, $searchField ="", $searchString ="", $searchOper = ""){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($company != "*"){
			$where .= " AND COMPANY_CODE = '".$company."'";
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT LOGINID, USER_FULLNAME, USER_PASS, USER_MAIL, USER_DEPT, dept.DEPT_DESCRIPTION, USER_LEVEL, 
				gr.USER_GROUP_NAME, usr.INACTIVE, COMPANY_CODE FROM m_user usr
				LEFT JOIN m_employee_dept dept ON dept.DEPT_CODE = usr.USER_DEPT
				LEFT JOIN m_user_group gr ON gr.USER_GROUP_ID = usr.USER_LEVEL" . $where;
		
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
		$no = 1;
        foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($no,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->LOGINID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->USER_FULLNAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_PASS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_MAIL,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->USER_DEPT,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DEPT_DESCRIPTION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_LEVEL,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->USER_GROUP_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
			$no++;
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      
        return $jsonObject;
    } 
	
	function get_company(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function get_usergroup(){
		$query = $this->db->query("SELECT USER_GROUP_ID,USER_GROUP_NAME FROM m_user_group");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	/* CRUD */
	function cek_exist($id) {
        $query = $this->db->query("SELECT LOGINID FROM m_user WHERE LOGINID = '".$id."' ");
        $count = $query->num_rows();
        return $count;
	}
	
	function insertUser($data){
		$this->db->insert( 'm_user', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function updateUser($uid, $data){
		$this->db->where( 'LOGINID', $uid );
		$this->db->update( 'm_user', $data );
		
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function deleteUser( $uid ){
		$this->db->set('INACTIVE', 1);
		$this->db->where( 'LOGINID', $uid );
        $this->db->update( 'm_user');   
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	/* end CRUD */
	
	/* module access */
	function read_usermodule_access($uid, $searchField ="", $searchString ="", $searchOper = ""){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        //$sidx = $this->input->post('sidx');
		$sidx = 'MOD_ID';
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($uid != ""){
			$where .= " AND mo.LOGINID = '".$uid."'";
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT mo.MOD_ID, mo.LOGINID, mo.MODULE_ACCESS, 
					CASE WHEN mo.MODULE_ACCESS = 'PRD' THEN 'PRODUKSI' 
	 					WHEN mo.MODULE_ACCESS = 'PMS' THEN 'PROJECT' 
						ELSE 'LHM' END AS MODULE_NAME
				FROM m_user_module_access mo
				LEFT JOIN m_user us ON us.LOGINID = mo.LOGINID" . $where;
		
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
		foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->MOD_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->LOGINID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->MODULE_ACCESS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MODULE_NAME,ENT_QUOTES,'UTF-8'));
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
	
	function read_userco_access($uid, $searchField ="", $searchString ="", $searchOper = ""){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($uid != ""){
			$where .= " AND USERID = '".$uid."'";
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT co.ID, co.USERID, co.COMPANY_CODE, cp.COMPANY_NAME FROM m_user_co co
				LEFT JOIN m_user us ON us.LOGINID = co.USERID
				LEFT JOIN m_company cp ON cp.COMPANY_CODE = co.COMPANY_CODE" . $where;
		
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
		foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USERID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COMPANY_NAME,ENT_QUOTES,'UTF-8'));
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
	/* CRUD User Company */ 
	
	function cek_exist_userco($uid, $company) {
        $query = $this->db->query("SELECT USERID, COMPANY_CODE FROM m_user_co WHERE USERID = '".$uid."' 
								   AND COMPANY_CODE = '".$company."'");
        $count = $query->num_rows();
        return $count;
	}
	
	function cek_exist_usermodule($uid, $mod) {
        $query = $this->db->query("SELECT LOGINID, MODULE_ACCESS FROM m_user_module_access WHERE LOGINID = '".$uid."'
					AND MODULE_ACCESS = '".$mod."' ");
        $count = $query->num_rows();
        return $count;
	}
	
	function insertUserCo($data){
		$this->db->insert( 'm_user_co', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function insertUserModule($data){
		$this->db->insert( 'm_user_module_access', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function deleteUserCo($uid){
		$this->db->where( 'ID',$uid );
		$this->db->delete( 'm_user_co');
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	function deleteUserModule($uid){
		$this->db->where( 'MOD_ID',$uid );
		$this->db->delete('m_user_module_access');
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	/* menu khusus user */
	function read_userco_menu($uid, $searchField ="", $searchString ="", $searchOper = ""){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($uid != ""){
			$where .= " AND LOGINID = '".$uid."'";
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT lst.MENU_LIST_ID, lst.LOGINID, lst.MENU_ID, mn.MENU_NAME FROM m_user_menu_list lst 
					LEFT JOIN m_user_menu mn ON mn.MENU_ID = lst.MENU_ID" . $where;
		
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
		foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->MENU_LIST_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->LOGINID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->MENU_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MENU_NAME,ENT_QUOTES,'UTF-8'));
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
	
	/* CRUD Menu*/
	function cek_exist_menu($id, $menu) {
        $query = $this->db->query("SELECT LOGINID, MENU_ID FROM m_user_menu_list WHERE LOGINID = '".$id."' 
								  AND MENU_ID = '".$menu."'");
        $count = $query->num_rows();
        return $count;
	}
	
	function insertUserMenu($data){
		$this->db->insert( 'm_user_menu_list', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function deleteUserMenu($uid){
		$this->db->where( 'MENU_LIST_ID',$uid );
		$this->db->delete( 'm_user_menu_list');
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	/* end CRUD Menu*/
	/* end menu khusus */
	
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