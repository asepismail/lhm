<?php
class pms_m_monitoring extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	/* company */
	function get_company()
	{
		$query = $this->db->query("SELECT COMPANY_CODE,COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function read_ppj($company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$wherecomp = "";
		if($company != "PAG"){
			$wherecomp = " AND ppj.COMPANY_CODE = '". $company . "'";
		}
		$sql2 = "SELECT ppjd.PROJECT_PROP_ID, ppj.PROJECT_PROPNUM_NUMID,PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA,";
		$sql2 .= " ppjd.PROJECT_ID, PROJECT_PROP_SUBTYPE,PROJECT_PROP_LOCATION,PROJECT_PROP_ACTIVITY,PROJECT_PROP_QTY,";
		$sql2 .= " PROJECT_PROP_UOM, PROJECT_PROP_VALUE,PROJECT_PROP_TVALUE, ppj.ISAPPR_LVL0, ppj.ISAPPR_LVL1, ";
		$sql2 .= " ppj.ISAPPR_LVL2,ppj.ISREVISED,ppj.ISCLOSED,ppj.PROP_STATUS, ppj.COMPANY_CODE";
		$sql2 .= " FROM pms_project_propnum ppj";
		$sql2 .= " LEFT JOIN pms_project_proposal ppjd ON ppjd.PROJECT_PROPNUM_NUMID = ppj.PROJECT_PROPNUM_NUMID ";
		$sql2 .= " AND ppjd.ISCANCEL = 0 WHERE ppj.ISCOMPLETE = 1".$wherecomp."";
		
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
					array_push($cell, htmlentities($obj->PROJECT_PROP_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PROJECT_PROPNUM_NUMID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_PELAKSANA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_SUBTYPE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_LOCATION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_ACTIVITY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_QTY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_UOM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_VALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_TVALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL0,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL1,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL2,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ISREVISED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROP_STATUS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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