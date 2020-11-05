<?php
class model_p_block_transaction extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_pokok_mati($company, $periode, $searchField ="", $searchString ="", $searchOper = ""){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$where = " WHERE 1 = 1 ";
		if($company != "*"){
			$where .= " AND pt.COMPANY_CODE = '".$company."'";
		}
		
		$where .= " AND DATE_FORMAT(TRANS_DATE,'%Y%m') LIKE '%". $periode ."%' AND VOID = 0";
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString);
		}
		
		$sql2 = "SELECT TRANS_ID,TRANS_DATE,AFD,BLOCK, BLOCK_TANAM,QTY,DOCUMENT_NUMBER,NOTE, pt.COMPANY_CODE FROM
				 p_block_transaction pt LEFT JOIN m_fieldcrop fc ON fc.FIELDCODE = pt.BLOCK_TANAM AND
				 fc.COMPANY_CODE = pt.COMPANY_CODE" . $where;
		
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
					array_push($cell, htmlentities($obj->TRANS_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TRANS_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->BLOCK,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->BLOCK_TANAM,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->QTY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DOCUMENT_NUMBER,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->NOTE,ENT_QUOTES,'UTF-8'));
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
	
	function get_afd($company){
		$query = $this->db->query("SELECT AFD_CODE,AFD_DESC FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function get_block($afd, $company, $block = ""){
		if($block == ""){ 
			$sql = "SELECT BLOCKID, BLOCKID AS DESCRIPTION FROM m_fieldcrop WHERE COMPANY_CODE = '".$company."' AND ESTATECODE = '".$afd."' AND YEARREPLANT <> 'N/A' GROUP BY BLOCKID";
		} else if($block != ""){
			$sql = "SELECT FIELDCODE, CONCAT(FIELDCODE,' - ', DESCRIPTION) AS DESCRIPTION FROM m_fieldcrop WHERE COMPANY_CODE = '".$company."' AND ESTATECODE = '".$afd."' AND BLOCKID = '".$block."' AND YEARREPLANT <> 'N/A'";
		}
		$query = $this->db->query($sql);
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function cek_exist_trans($tgl, $block) {
        $query = $this->db->query("SELECT TRANS_DATE, BLOCK_TANAM FROM p_block_transaction WHERE TRANS_DATE = '".$tgl."'  AND BLOCK_TANAM = '".$block."'");
        $count = $query->num_rows();
        return $count;
	}
	
	function insert_trans($data){
		$this->db->insert( 'p_block_transaction', $data );
		//return $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function update_trans($id, $data){
		$this->db->where( 'TRANS_ID',$id );
        $this->db->update( 'p_block_transaction', $data );
		//return $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function data_for_log($transid){
		$sql = "SELECT TRANS_DATE, CONCAT(TRANS_ID,';',TRANS_DATE,';',TRANS_TYPE,';',AFD,';',BLOCK,';',BLOCK_TANAM,';',QTY,';',DOCUMENT_NUMBER,';',NOTE,';',COMPANY_CODE,';',VOID,';',INPUTBY,';',INPUTDATE,';',UPDATEBY,';',UPDATEDATE) AS LOG_BEFORE 
FROM p_block_transaction WHERE TRANS_ID = '".$transid."'";
		$query = $this->db->query($sql);
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function insert_log($data){
		$this->db->insert( 'log_transaction', $data );
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
}

?>