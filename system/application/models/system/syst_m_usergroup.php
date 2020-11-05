<?php
class syst_m_usergroup extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_ugroup($searchField ="", $searchString ="", $searchOper = "")
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT USER_GROUP_TID,USER_GROUP_ID,USER_GROUP_NAME FROM m_user_group" . $where;
		
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
					array_push($cell, htmlentities($obj->USER_GROUP_TID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->USER_GROUP_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_GROUP_NAME,ENT_QUOTES,'UTF-8'));
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
	
	function cek_existGroup($groupid) {
        $query = $this->db->query("SELECT USER_GROUP_ID FROM m_user_group WHERE USER_GROUP_ID = '".$groupid."' ");
        $count = $query->num_rows();
        return $count;
	}
	
	function insert_Group($data){
		$this->db->insert( 'm_user_group', $data );
		return $this->db->insert_id();
	}
	
	function update_Group($id, $data){
		$this->db->where( 'USER_GROUP_ID',$id );
        $this->db->update( 'm_user_group', $data );
		return $this->db->insert_id();
	}
	
	function delete_Group($id){
		$this->db->where( 'USER_GROUP_ID',$id );
        $this->db->delete( 'm_user_group');
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function delete_GroupRole($id){
		$this->db->where( 'USER_GROUP_ID',$id );
		$this->db->delete( 'm_user_menu_grole');
		
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	/* ------------------------------------------------------------------------------------ */
	/* role per export group  */
	function read_ugExport($uGroup)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE ex.GROLE = 'xx' ";
		if($uGroup != ""){
			$where = " WHERE ex.GROLE = '".$uGroup."'";
		}
		
		$sql2 = "SELECT ex.AEID, ex.GROLE, g.USER_GROUP_NAME, e.EXPORT_MENU
FROM m_user_exportdata_access_group ex
INNER JOIN m_user_exportdata e  ON ex.EXPORT_MEID = e.MUEXD_ID
INNER JOIN m_user_group g ON ex.GROLE = g.USER_GROUP_ID" . $where;
		
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
					array_push($cell, htmlentities($obj->AEID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->GROLE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_GROUP_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->EXPORT_MENU,ENT_QUOTES,'UTF-8'));
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
	/* ------------------------------------------------------------------------------------ */
	/* role per group */
	function read_ugRole($uGroup)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE grole.USER_GROUP_ID = 'xx' ";
		if($uGroup != ""){
			$where = " WHERE grole.USER_GROUP_ID = '".$uGroup."'";
		}
		
		$sql2 = "SELECT MENU_LIST, grole.USER_GROUP_ID, gr.USER_GROUP_NAME, grole.MENU_ID, mnu.MENU_NAME
					FROM m_user_menu_grole grole
					LEFT JOIN m_user_menu mnu ON mnu.MENU_ID = grole.MENU_ID 
					LEFT JOIN m_user_group gr ON gr.USER_GROUP_ID = grole.USER_GROUP_ID" . $where;
		
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
					array_push($cell, htmlentities($obj->MENU_LIST,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->USER_GROUP_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->USER_GROUP_NAME,ENT_QUOTES,'UTF-8'));
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
	
	function cek_existRole($groupid, $menuid) {
        $query = $this->db->query("SELECT USER_GROUP_ID FROM m_user_menu_grole WHERE USER_GROUP_ID = '".$groupid."' 
								   AND MENU_ID = '".$menuid."'");
        $count = $query->num_rows();
        return $count;
	}
	
	function cek_existExport($groupid, $menuid) {
        $query = $this->db->query("SELECT AEID FROM m_user_exportdata_access_group WHERE GROLE = '".$groupid."' 
								   AND EXPORT_MEID = '".$menuid."'");
        $count = $query->num_rows();
        return $count;
	}
	
	function insert_Role($data){
		$this->db->insert( 'm_user_menu_grole', $data );
		return $this->db->insert_id();
	}
	
	function insert_Export($data){
		$this->db->insert( 'm_user_exportdata_access_group', $data );
		return $this->db->insert_id();
	}
	
	function delete_Role($gid){
		$this->db->where( 'MENU_LIST',$gid );
		$this->db->delete( 'm_user_menu_grole');
		return $this->db->insert_id();
	}
	
	function delete_Export($eid){
		$this->db->where( 'AEID',$eid );
		$this->db->delete( 'm_user_exportdata_access_group');
		return $this->db->insert_id();
	}
	
	/* fungsi konversi jqgrid ke query */ 
	function getWhereClause($col, $oper, $val){
		$ops = array(
						'eq'=>'=', //equal
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
	
	function read_export($searchField ="", $searchString ="", $searchOper = "")
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        //$sidx = $this->input->post('sidx');
		$sidx='MUEXD_ID';
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT ex.MUEXD_ID, ex.EXPORT_MENU FROM m_user_exportdata ex" . $where;
		
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
					array_push($cell, htmlentities($obj->MUEXD_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->EXPORT_MENU,ENT_QUOTES,'UTF-8'));					
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
	
}

?>