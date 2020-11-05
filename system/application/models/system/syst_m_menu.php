<?php
class syst_m_menu extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_menu($searchField ="", $searchString ="", $searchOper = "")
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT MENU_ID,MENU_NAME,MENU_PARENT,MENU_URL,LFT,RGT FROM m_user_menu" . $where;
		
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
					array_push($cell, htmlentities($obj->MENU_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->MENU_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MENU_PARENT,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MENU_URL,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->LFT,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->RGT,ENT_QUOTES,'UTF-8'));
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
	
	function get_parent()
	{
		$query = $this->db->query("SELECT MENU_ID,MENU_NAME FROM m_user_menu WHERE MENU_PARENT = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function cek_exist($menuid) {
        $query = $this->db->query("SELECT MENU_ID FROM m_user_menu WHERE MENU_ID = '".$menuid."' ");
        $count = $query->num_rows();
        return $count;
	}
	
	function insert_menu($data){
		$this->db->insert( 'm_user_menu', $data );
		return $this->db->insert_id();
	}
	
	function update_menu($id, $data){
		$this->db->where( 'MENU_ID',$id );
        $this->db->update( 'm_user_menu', $data );
		return $this->db->insert_id();
	}
	
	function delete_menu($id, $data){
		$this->db->where( 'MENU_ID',$id );
        $this->db->delete( 'm_user_menu');
		return $this->db->insert_id();
	}
	
	function updatelevel_right($lft){
		$this->db->query("UPDATE m_user_menu SET RGT = RGT+2 WHERE RGT > ".$lft.""); 
	}
	
	function updatelevel_left($lft){
		$this->db->query("UPDATE m_user_menu SET LFT = LFT+2 WHERE LFT > ".$lft.""); 
	}
	
	function deletelevel_right($lft){
		$this->db->query("UPDATE m_user_menu SET RGT = RGT-2 WHERE RGT > ".$lft.""); 
	}
	
	function deletelevel_left($lft){
		$this->db->query("UPDATE m_user_menu SET LFT = LFT-2 WHERE LFT > ".$lft.""); 
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
}
?>