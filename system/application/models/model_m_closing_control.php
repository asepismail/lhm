<?php
class model_m_closing_control extends Model{
    function __construct(){
        parent::__construct();
    }
	
	function read_Periode($searchField ="", $searchString ="", $searchOper = "", $sTahun = "", $sCompany = "")
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = 'desc';
		$company_code=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		if($sCompany == ""){
			$where = " WHERE COMPANY_CODE = '".$company_code ."'";	
		} else {
			$where = " WHERE COMPANY_CODE = '".$sCompany ."'";	
		}
		
		if($searchString!= ""){
			$where .= $this->getWhereClause($searchField, $searchOper, $searchString, $company_code);
		}
		
		if($sTahun!=""){
			$where .= " AND PERIODE_NAME LIKE '".$sTahun."%'";
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
		if( $count > 0 ) {
            $sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
        } 
		//var_dump($sql);
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';
		$status='';
        foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->PERIODE_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_NAME,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_START,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_END,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($status,ENT_QUOTES,'UTF-8'));
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
	
	
	function update_period($id, $ic, $rb, $rd, $cb, $cd){
		$this->db->where( 'PERIODE_ID', $id );
		$this->db->set( 'ISCLOSE', $ic);
		$this->db->set( 'REOPEN_BY', $rb);
		$this->db->set( 'REOPEN_DATE', $rd);
		$this->db->set( 'CLOSE_BY', $cb);
		$this->db->set( 'CLOSE_DATE', $cd);
        $this->db->update( 'm_periode' );
		//return $this->db->insert_id();
		return $this->db->affected_rows(); 
	}
	
	/* ketika periode diupdate */
	function getPeriodeControlId($periodeId){
		$query = $this->db->query("SELECT PERIODE_CONTROL_ID FROM m_periode_control WHERE PERIODE_ID = '". $periodeId ."'");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		
		return $temp_result; 
	}
	
	function update_pControlRev($periodeCoId, $periodeId, $data){
		$this->db->where( 'PERIODE_ID',$periodeId );
		$this->db->where( 'PERIODE_CONTROL_ID',$periodeCoId );
        $this->db->update( 'm_periode_control', $data );
		return $this->db->affected_rows(); 
	}
	
	function update_pControlDetRev($periodeCoDetId, $data){
		$this->db->where( 'PERIODE_CONTROL_ID',$periodeCoDetId );
        $this->db->update( 'm_periode_control_detail', $data );
	}
	
	function update_pControl($id, $data){
		$this->db->where( 'PERIODE_CONTROL_ID',$id );
        $this->db->update( 'm_periode_control', $data );
		//return $this->db->insert_id();
		return $this->db->affected_rows(); 
	}
	
	/* ------------------------------------------------------------------------------------ */
	/* role per group */
	function read_pControl($period_id, $company_id, $module)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = 'desc';
		
		//$where = " WHERE PERIODE_CONTROL_ID = 'xx' ";
		$company_code=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$where = " WHERE COMPANY_CODE = '".$company_code ."'";		
		if($period_id != ""){
			$where = " WHERE PERIODE_ID = '".$period_id."' AND COMPANY_CODE = '".$company_id ."'";
		}
		
		if($module != "" ){
			$where .= " AND MODULE = '".$module."' ";
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
		if( $count > 0 ) {
            $sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
        } 
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';
		$status='';
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
					array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($status,ENT_QUOTES,'UTF-8'));
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
	
	function read_pControlDetail($PeriodeControlId, $company_id)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = 'desc';
		
		//$where = " WHERE PERIODE_CONTROL_ID = 'xx' ";
		$company_code=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$where = " WHERE 1=1";		
		if($PeriodeControlId != ""){
			$where = " WHERE PERIODE_CONTROL_ID = '".$PeriodeControlId."'";
		}
		
		$sql2 = "SELECT * FROM m_periode_control_detail" . $where;		
		
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
		if( $count > 0 ) {
            $sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
        } 
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';
		$status='';
        foreach($objects as $obj){
            $cell = array();
					array_push($cell, htmlentities($obj->PERIODE_CONTROL_DETAIL_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PERIODE_CONTROL_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PERIODE_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->MODULE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CLOSE_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_BY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->REOPEN_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($status,ENT_QUOTES,'UTF-8'));
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
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		
		return $temp_result;  
	}
	
	function cekControlDate($pControlId){
		
		$pgquery = "SELECT CONCAT(PERIODE_START, '~', PERIODE_END ) as ret FROM m_periode_control WHERE PERIODE_CONTROL_ID = '".$pControlId."'";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cekControlDetail($pControlId){
		$pgquery = "SELECT COUNT(*) as ret FROM m_periode_control_detail WHERE PERIODE_CONTROL_ID = '".$pControlId."'";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
   	}
  	
	function cekLastPeriodeId(){
		$pgquery = "SELECT MAX(PERIODE_ID) as ret FROM m_periode";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
   	}
	
	function cekLastControlId(){
		$pgquery = "SELECT MAX(PERIODE_CONTROL_ID) as ret FROM m_periode_control";
		//$pgquery = "SELECT * FROM ad_org WHERE value = CONCAT('".$company."','-Site') LIMIT 1";
		$query = $this->db->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
   	}

	function delControlDetail($pControlId){
		$this->db->where( 'PERIODE_CONTROL_ID',$pControlId );
        $this->db->delete( 'm_periode_control_detail' );
		return $this->db->insert_id();
	} 
	
	function updateControlDetail($pControlId, $pDate, $pModule, $isClose, $cb, $cd, $rb, $rd){
		$this->db->where( 'PERIODE_CONTROL_ID',$pControlId );
		$this->db->where( 'PERIODE_DATE',$pDate );
		$this->db->where( 'MODULE',$pModule );
		$this->db->set( 'ISCLOSE', $isClose );
		$this->db->set( 'CLOSE_BY',$cb );
		$this->db->set( 'CLOSE_DATE',$cd );
		$this->db->set( 'REOPEN_BY',$rb );
		$this->db->set( 'REOPEN_DATE',$rd );
        $this->db->update( 'm_periode_control_detail' );
		//return $this->db->affected_rows(); //remarked by Asep, 20141028
		return $this->db->trans_status(); //remarked by Asep, 20141028
	} 
	
	function addControlDetail($data){
		if(isset($data)){
        	$this->db->insert( 'm_periode_control_detail', $data );
            return $this->db->affected_rows(); 
        }
	}
	
	/* fungsi generate closing control */
	function getPeriodeId(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		
		return $temp_result;  
	}
	
	function deleteAll($year){
		//$this->db->where( 'PERIODE_CONTROL_ID',$year );
		$this->db->like('PERIODE_DATE', $year, 'after'); 
        $this->db->delete( 'm_periode_control_detail' );
		$pcDetail = $this->db->affected_rows(); 
		
		$this->db->like('PERIODE_START', $year, 'after'); 
        $this->db->delete( 'm_periode_control' );
		$pc = $this->db->affected_rows();
		
		$this->db->like('PERIODE_NAME', $year, 'after'); 
        $this->db->delete( 'm_periode' );
		$p = $this->db->affected_rows();
		
		return $p;
	}
	
	function insertPeriode($data){
		if(isset($data)){
        	$this->db->insert( 'm_periode', $data );
            return $this->db->affected_rows(); 
        }
	}
	
	function insertPeriodeControl($data){
		if(isset($data)){
        	$this->db->insert( 'm_periode_control', $data );
            return $this->db->affected_rows(); 
        }
	}
	
	function insertPeriodeControlDetail($data){
		if(isset($data)){
        	$this->db->insert( 'm_periode_control_detail', $data );
            return $this->db->affected_rows(); 
        }
	}
	/* end generate closing control */
}

?>