<?php
class pms_m_pengajuan extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	/* afdeling */
	function get_dept()
	{
		$query = $this->db->query("SELECT DEPT_CODE,DEPT_DESCRIPTION FROM m_employee_dept WHERE INACTIVE = 0 AND DEPT_CODE IN ('TNM','PAB','TEK')");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_fixedasset()
	{
		$query = $this->db->query("SELECT DISTINCT IFTYPE_NAME, IFTYPE FROM m_infrastructure_type");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_ifcode($iftype)
	{
		$query = $this->db->query("SELECT IFSUBTYPE,IFSUBTYPE_NAME FROM m_infrastructure_subtype WHERE IFTYPE LIKE '%".$iftype."%'");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	/* function untuk PPJ */
	function read_ppj($ppj)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
        $sql2 = "SELECT PROJECT_PROP_ID, ppjh.PROJECT_PROPNUM_NUMID, ISCOMPLETE, COMPANY_CODE,PROJECT_PROP_START, ";
		$sql2 .= " PROJECT_PROP_END, ppjd.PROJECT_PROP_TYPE, ppjd.PROJECT_PROP_SUBTYPE, ppjd.PROJECT_ID,PROJECT_PROP_IFTYPE,";
		$sql2 .= " ppjd.PROJECT_PROP_AFD, ppjd.PROJECT_PROP_DESC, ppjd.PROJECT_PROP_LOCATION, ppjd.PROJECT_PROP_ACTIVITY,";
		$sql2 .= " ppjd.PROJECT_PROP_SUBACTIVITY, ppjd.PROJECT_PROP_QTY, ppjd.PROJECT_PROP_UOM, ppjd.PROJECT_PROP_VALUE, ";
		$sql2 .= " ppjd.PROJECT_PROP_TVALUE, ppjd.ISDETAIL FROM pms_project_propnum ppjh ";
		$sql2 .= " LEFT JOIN pms_project_proposal ppjd ON ppjd.PROJECT_PROPNUM_NUMID = ppjh.PROJECT_PROPNUM_NUMID ";
		$sql2 .= " where ppjh.PROJECT_PROPNUM_NUMID = '".$ppj."' AND ppjd.ISCANCEL = 0";
		
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
		
        $objects = $this->db->query($sql)->result(); 
        $rows =  array();
        $act='';                           
        foreach($objects as $obj)
        {
            $cell = array();
			 		array_push($cell, htmlentities($obj->PROJECT_PROP_ID,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROPNUM_NUMID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCOMPLETE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_TYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_SUBTYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_IFTYPE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_AFD,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_DESC,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_LOCATION,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_ACTIVITY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_SUBACTIVITY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_QTY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_UOM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_VALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_TVALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_START,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_END,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISDETAIL,ENT_QUOTES,'UTF-8'));
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
	
	/* function untuk detail PPJ */
	function read_detail_ppj($pjs)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

	 	$sql2 = " SELECT PROJECT_PROPDET_ID, DPROJECT_ID, DPROJECT_PROP_ACTIVITY, c.COA_DESCRIPTION, DPROJECT_PROP_QTY, ";
		$sql2 .= " DPROJECT_PROP_UOM,DPROJECT_PROP_VALUE,DPROJECT_PROP_TVALUE FROM pms_project_proposaldetail ppd";
		$sql2 .= " LEFT JOIN m_coa c ON c.ACCOUNTCODE = ppd.DPROJECT_PROP_ACTIVITY";
		$sql2 .= " WHERE DPROJECT_ID = '".$pjs."' ";
		
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
                    array_push($cell, htmlentities($obj->PROJECT_PROPDET_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DPROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DPROJECT_PROP_ACTIVITY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COA_DESCRIPTION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DPROJECT_PROP_QTY,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->DPROJECT_PROP_UOM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->DPROJECT_PROP_VALUE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->DPROJECT_PROP_TVALUE,ENT_QUOTES,'UTF-8'));
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
	
	function cekNotComplete($company){
        $query = $this->db->query("SELECT COUNT(PROJECT_PROPNUM_NUMID) AS jumlah, PROJECT_PROPNUM_NUMID, 
									PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA,PROJECT_DEPT,PROJECT_FINISH_TARGET			
									FROM pms_project_propnum ppjh 
									WHERE COMPANY_CODE = '".$company."' AND ISCOMPLETE = 0 AND ISCANCEL = 0");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function get_afd($company)
	{
		if($company == "PAG"){
			$query = $this->db->query("SELECT AFD_CODE,AFD_DESC FROM m_afdeling GROUP BY AFD_CODE");
		} else {
			$query = $this->db->query("SELECT AFD_CODE,AFD_DESC FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
		}
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_company_id($company)
	{
		$query = $this->db->query("SELECT COMPANY_NUMBER FROM m_company WHERE COMPANY_CODE = '".$company."'");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_activity_pj($type, $subtype)
	{
		$sql = "SELECT map.ACTIVITY_CODE, CONCAT(map.ACTIVITY_CODE,'-',c.COA_DESCRIPTION) AS COA_DESCRIPTION FROM";
		$sql .= " pms_project_activity_map map LEFT JOIN m_coa c ON c.ACCOUNTCODE = map.ACTIVITY_CODE";
		$sql .= " WHERE PROJECT_TYPE = '".$type."'  AND PROJECT_SUBTYPE = '".$subtype."'";
		$query = $this->db->query($sql);
		
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	/* CRUD Header Project */
	function cek_header($company, $ppj){
		 $query = $this->db->query("SELECT PROJECT_PROPNUM_NUMID			
									FROM pms_project_propnum ppjh 
									WHERE COMPANY_CODE = '".$company."' AND PROJECT_PROPNUM_NUMID = '".$ppj."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function insert_header($data){
		$this->db->insert( 'pms_project_propnum', $data );
		return $this->db->insert_id();
	}
	
	function update_header($ppj, $data){
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum', $data );  
		return $this->db->insert_id();
	}
	
	function cancel_header($ppj, $user){
		$this->db->set('ISCANCEL', 1);
		$this->db->set('CANCELBY', $user);
		$this->db->set('CANCELDATE', date ("Y-m-d"));
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	/* CRUD detail line Project */
	function cek_detail($company, $ppj, $pjs){
		 $query = $this->db->query("SELECT PROJECT_PROPNUM_NUMID			
									FROM pms_project_proposal ppjh 
									WHERE PROJECT_PROPNUM_NUMID = '".$ppj."' AND PROJECT_ID = '".$pjs."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function insert_detail($data){
		$this->db->insert( 'pms_project_proposal', $data );
		return $this->db->insert_id();
	}
	
	function update_detail($ppj, $pjs, $data){
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj ); 
		$this->db->where( 'PROJECT_ID', $pjs ); 
		$this->db->update( 'pms_project_proposal', $data );  
		return $this->db->insert_id();
	}
	
	function delete_detail($id, $pjs){
		$this->db->where( 'PROJECT_PROPDET_ID', $id ); 
		$this->db->delete( 'pms_project_proposaldetail' );  
		//return $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
			$query = $this->db->query("SELECT SUM(DPROJECT_PROP_TVALUE) AS NILAI FROM pms_project_proposaldetail WHERE DPROJECT_ID = '".$pjs."' AND ISCANCEL = 0 GROUP BY DPROJECT_ID");
			$temp_result = array();
			foreach ( $query->result_array() as $row ){
				$temp_result [] = $row['NILAI'];
			}
			return $temp_result [0];
        return FALSE;
	}
	
	function cancel_detail($ppj,$pjs,$user){
		$this->db->set('ISCANCEL', 1);
		$this->db->set('CANCELBY', $user);
		$this->db->set('CANCELDATE', date ("Y-m-d"));
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );
		$this->db->where( 'PROJECT_ID', $pjs );
		$this->db->update( 'pms_project_proposal');  
		return $this->db->insert_id();
	}
	
	/* CRUD detail line Project */
	function cek_detail_act($pjs, $act){
		 $query = $this->db->query("SELECT DPROJECT_ID, DPROJECT_PROP_ACTIVITY FROM pms_project_proposaldetail 
									WHERE DPROJECT_ID = '".$pjs."' AND DPROJECT_PROP_ACTIVITY = '".$act."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function insert_detail_act($pjs, $data){
		$this->db->insert( 'pms_project_proposaldetail', $data );
		//return $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
			$query = $this->db->query("SELECT SUM(DPROJECT_PROP_TVALUE) AS NILAI FROM pms_project_proposaldetail WHERE DPROJECT_ID = '".$pjs."' AND ISCANCEL = 0 GROUP BY DPROJECT_ID");
			$temp_result = array();
			foreach ( $query->result_array() as $row ){
				$temp_result [] = $row['NILAI'];
			}
			return $temp_result [0];
        return FALSE;
	}
	
	function update_detail_act($pjs, $act, $data){
		$this->db->where( 'DPROJECT_ID', $pjs ); 
		$this->db->where( 'DPROJECT_PROP_ACTIVITY', $act ); 
		$this->db->update( 'pms_project_proposaldetail', $data );  
		//return $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
			$query = $this->db->query("SELECT SUM(DPROJECT_PROP_TVALUE) AS NILAI FROM pms_project_proposaldetail WHERE DPROJECT_ID = '".$pjs."' AND ISCANCEL = 0 GROUP BY DPROJECT_ID");
			$temp_result = array();
			foreach ( $query->result_array() as $row ){
				$temp_result [] = $row['NILAI'];
			}
			return $temp_result [0];
        return FALSE;
	}
	
	function cancel_detail_act($pjs,$act,$user){
		$this->db->set('ISCANCEL', 1);
		$this->db->set('CANCELBY', $user);
		$this->db->set('CANCELDATE', date ("Y-m-d"));
		$this->db->where( 'DPROJECT_ID', $pjs );
		$this->db->where( 'DPROJECT_PROP_ACTIVITY', $act );
		$this->db->update( 'pms_project_proposaldetail');  
		return $this->db->insert_id();
	}
	
	function selesai( $ppj ){
		$this->db->set('ISCOMPLETE', 1);
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	/* lokasi blok */
	function getblok($company, $afd, $q)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = ""; 
		if($q != "-" ){
			$where = " AND BLOCKID LIKE '%".$q."%' OR COMPANY_CODE = '".$company."' AND DESCRIPTION LIKE '%".$q."%'" ;
		}
		
        $sql2 = "SELECT BID, BLOCKID, ESTATECODE, DESCRIPTION, COMPANY_CODE FROM m_blockmaster ";
		$sql2 .= " WHERE COMPANY_CODE = '".$company."' AND ESTATECODE = '".$afd."' ".$where."";
		
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
		
        $objects = $this->db->query($sql)->result(); 
        $rows =  array();
        foreach($objects as $obj)
        {
            $cell = array();
			 		array_push($cell, htmlentities($obj->BID,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->BLOCKID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ESTATECODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
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
	
	function insert_log_pengajuan($data){
		$this->db->insert( 'pms_project_prop_log', $data );
		return $this->db->insert_id();
	}
	
	function update_log_pengajuan($id, $data){
		$this->db->set('LOG_AFTER', $data);
		$this->db->where( 'HIST_ID', $id );  
		$this->db->update( 'pms_project_prop_log');  
		return $this->db->insert_id();
	}
	
	function delete_log_pengajuan ($id){
        $this->db->where( 'HIST_ID', $id );      
        $this->db->delete('pms_project_prop_log');   
    }
}

?>