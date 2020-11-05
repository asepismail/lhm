<?php
class pms_m_revisi extends Model{
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
		$sql2 .= " PROJECT_NETTVAL,PROJECT_STATUS,TGL_TERBIT,COMPANY_CODE, PROJECT_QTY, 
					PROJECT_UOM, PROJECT_VALUE FROM m_project ".$where." ";
		
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
			array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->KODE_PELAKSANA,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_QTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_UOM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PROJECT_VALUE,ENT_QUOTES,'UTF-8'));
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
	
	/* project yang akan direvisi */
	/* function read_ppj_rev($pj, $company){
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        $where = "WHERE 1 = 1";
		if($company != "PAG"){
			$where = "WHERE COMPANY_CODE ='".$company."'";
		} 
		
		$sql2 = "SELECT ID,PROJECT_ID,AFD,PROJECT_TYPE,PROJECT_ACTIVITY AS PROJECT_SUBTYPE,PROJECT_SUB_ACTIVITY, ";
		$sql2 .= " PROJECT_DESC,PROJECT_LOCATION,KODE_PELAKSANA,PROJECT_ACTIVITY,SPK, ";
		$sql2 .= " PROJECT_START,PROJECT_END,PROJECT_QTY,UPPER(PROJECT_UOM) AS PROJECT_UOM,PROJECT_VALUE,PROJECT_PPN, ";
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
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->AFD,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_TYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_SUBTYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_SUB_ACTIVITY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_DESC,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_LOCATION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->KODE_PELAKSANA,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_ACTIVITY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->SPK,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_START,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_END,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_QTY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_UOM,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_VALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PPN,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_NETTVAL,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_STATUS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TGL_TERBIT,ENT_QUOTES,'UTF-8'));
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
		*/
	/* end project revisi */
	/* cek complete transaksi */
	function cekNotComplete($company){
        $query = $this->db->query("SELECT COUNT(PROJECT_PROPNUM_NUMID) AS jumlah, PROJECT_PROPNUM_NUMID, 
									PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA,PROJECT_DEPT,PROJECT_FINISH_TARGET			
									FROM pms_project_propnum ppjh 
									WHERE COMPANY_CODE = '".$company."' AND ISCOMPLETE = 0 AND ISCANCEL = 0 AND ISREVISED = 1
									AND PROJECT_PROPNUM_NUMID LIKE 'PPJR%'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	/* end cek */
	/* function untuk lembur */
	function read_ppj_rev($company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
        $sql2 = "SELECT PPID,PROJECT_PROPNUM_NUMID,PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA,PROJECT_PROPNUM_DESC, ";
		$sql2 .= " PROJECT_DEPT,PROJECT_FINISH_TARGET,PROP_STATUS,COMPANY_CODE,ISCOMPLETE,ISAPPR_LVL1,ISAPPR_LVL2,";
		$sql2 .= " ISREVISED,ISCLOSED FROM pms_project_propnum WHERE ISCOMPLETE = 1 AND COMPANY_CODE = '".$company."'";
		
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
                    array_push($cell, htmlentities($obj->PPID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_NUMID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_PELAKSANA,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_DEPT,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_FINISH_TARGET,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ISCOMPLETE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ISAPPR_LVL1,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ISAPPR_LVL2,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->ISREVISED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSED,ENT_QUOTES,'UTF-8'));
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