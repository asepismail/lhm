<?php
class pms_m_closing extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	/* cari project yang ada */
	function LoadDataProject($company, $qwhere)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $where = "WHERE 1 = 1 ";
		if($company != "PAG"){
			$where .= "AND COMPANY_CODE ='".$company."'";
		} 
		
		if($qwhere != "" || $qwhere != "-"){
			$where .= "AND PROJECT_ID LIKE '%".$qwhere."%' ";
			$where .= "OR COMPANY_CODE ='".$company."' AND AFD LIKE '%".$qwhere."%' ";
			$where .= "OR COMPANY_CODE ='".$company."' AND PROJECT_TYPE LIKE '%".$qwhere."%' ";
			$where .= "OR COMPANY_CODE ='".$company."' AND PROJECT_ACTIVITY LIKE '%".$qwhere."%' ";
			$where .= "OR COMPANY_CODE ='".$company."' AND PROJECT_DESC LIKE '%".$qwhere."%' ";
			$where .= "OR COMPANY_CODE ='".$company."' AND PROJECT_LOCATION LIKE '%".$qwhere."%' ";
		} 
		
		$sql2 = "SELECT ID,PROJECT_ID,AFD,PROJECT_TYPE,PROJECT_ACTIVITY AS PROJECT_SUBTYPE,";
		$sql2 .= " PROJECT_DESC,PROJECT_LOCATION,KODE_PELAKSANA,PROJECT_ACTIVITY,SPK, ";
		$sql2 .= " PROJECT_NETTVAL,PROJECT_STATUS,TGL_TERBIT,COMPANY_CODE FROM m_project ".$where." ";
		
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
		if($count > 0) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        foreach($objects as $obj){
            $cell = array();
			array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_TYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_SUBTYPE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PROJECT_DESC,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PROJECT_LOCATION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_ACTIVITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_STATUS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->TGL_TERBIT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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
	/* end cari project */
	
}

?>