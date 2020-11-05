<?php
class model_s_closing_control extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_ugroup($searchField ="", $searchString ="", $searchOper = "")
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = 'desc';
		$company_code=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$where = " WHERE COMPANY_CODE = '".$company_code ."'";	
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString, $company_code);
		}
		
		$sql2 = "SELECT * FROM m_periode" . $where;
		
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
		//var_dump($sql);
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';
        foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->PERIODE_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PERIODE_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_START,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_END,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_DATE,ENT_QUOTES,'UTF-8'));
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
	
	
	function update_period($id, $data){
		$this->db->where( 'PERIODE_ID',$id );
        $this->db->update( 'm_periode', $data );
		return $this->db->insert_id();
	}
	
	function update_pControl($id, $data){
		$this->db->where( 'PERIODE_CONTROL_ID',$id );
        $this->db->update( 'm_periode_control', $data );
		return $this->db->insert_id();
	}
	
	/* ------------------------------------------------------------------------------------ */
	/* role per group */
	function read_pControl($period_id)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = 'desc';
		
		//$where = " WHERE PERIODE_CONTROL_ID = 'xx' ";
		$company_code=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$where = " WHERE COMPANY_CODE = '".$company_code ."'";		
		if($period_id != ""){
			$where = " WHERE PERIODE_ID = '".$period_id."' AND COMPANY_CODE = '".$company_code ."'";
		}
		
		$sql2 = "SELECT * FROM m_periode_control" . $where;		
		
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
					array_push($cell, htmlentities($obj->PERIODE_CONTROL_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PERIODE_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_START,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_END,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MODULE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_DATE,ENT_QUOTES,'UTF-8'));
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
	function getWhereClause($col, $oper, $val, $company_code){
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
		return " AND $col {$ops[$oper]} '$val' COMPANY_CODE = '".$company_code ."'";
		
	}
	
}

?>