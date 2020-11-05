<?php
class model_project_pengajuan extends Model
{
    function model_project_pengajuan()
    {
        parent::Model();
        $this->load->database();
        
    }
	
   	/* function loadData */
	function LoadData($company)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$sql2 = "SELECT ID, PROJECT_ID, AFD, PROJECT_TYPE, PROJECT_ACTIVITY AS PROJECT_SUBTYPE, PROJECT_SUB_ACTIVITY, PROJECT_DESC, PROJECT_LOCATION, ";
		$sql2 .= " KODE_PELAKSANA, PROJECT_ACTIVITY, SPK, ";
		$sql2 .= " PROJECT_START, PROJECT_END, PROJECT_QTY, UPPER(PROJECT_UOM) AS PROJECT_UOM, PROJECT_VALUE, PROJECT_PPN, PROJECT_NETTVAL, ";
		$sql2 .= " PROJECT_STATUS, TGL_TERBIT, COMPANY_CODE FROM m_project WHERE COMPANY_CODE ='".$company."'";
		
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
		
		$sql = "SELECT ID, PROJECT_ID, AFD, PROJECT_TYPE, PROJECT_ACTIVITY AS PROJECT_SUBTYPE, PROJECT_SUB_ACTIVITY, PROJECT_DESC, PROJECT_LOCATION, ";
		$sql .= " KODE_PELAKSANA, PROJECT_ACTIVITY, SPK, ";
		$sql .= " PROJECT_START, PROJECT_END, PROJECT_QTY, UPPER(PROJECT_UOM) AS PROJECT_UOM, PROJECT_VALUE, PROJECT_PPN, PROJECT_NETTVAL, ";
		$sql .= " PROJECT_STATUS, TGL_TERBIT, COMPANY_CODE FROM m_project WHERE COMPANY_CODE ='".$company."'";
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
	    
    function search_prj($id,$afd,$type,$name, $limit, $page, $sidx, $sord)
    {
        if (isset($id)) { $id=$id; } else {  $id=""; }
        if (isset($afd)) { $afd=$afd; } else { $afd=""; }
        if (isset($name)) { $name=$name; } else { $name=""; }
        
        if (isset($type)) { $type=$type; } else { $type=""; }
        $company=$this->session->userdata('DCOMPANY');
        
        $where = "WHERE 1=1";
        if ($id !='' && $id !='-')$where.= " AND PROJECT_ID LIKE '%$id%'";
        if ($afd !='' && $afd !='-')$where.= " AND AFD LIKE '%$afd%'";
        if ($name !='' && $name !='-')$where.= " AND PROJECT_DESC LIKE '%$name%'";
        if ($type !='' && $type !='-')$where.= " AND PROJECT_TYPE LIKE '%$type%'";
        $where .= " AND COMPANY_CODE = '".$company."'";
        
        $sql2 = "SELECT ID, PROJECT_ID, AFD, PROJECT_TYPE, PROJECT_ACTIVITY AS PROJECT_SUBTYPE, PROJECT_SUB_ACTIVITY, PROJECT_DESC, PROJECT_LOCATION, ";
		$sql2 .= " KODE_PELAKSANA, PROJECT_ACTIVITY, SPK, ";
		$sql2 .= " PROJECT_START, PROJECT_END, PROJECT_QTY, UPPER(PROJECT_UOM) AS PROJECT_UOM, PROJECT_VALUE, PROJECT_PPN, PROJECT_NETTVAL, ";
		$sql2 .= " PROJECT_STATUS, TGL_TERBIT, COMPANY_CODE FROM m_project ".$where."";
		
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
		
		$sql = "SELECT ID, PROJECT_ID, AFD, PROJECT_TYPE, PROJECT_ACTIVITY AS PROJECT_SUBTYPE, PROJECT_SUB_ACTIVITY, PROJECT_DESC, PROJECT_LOCATION, ";
		$sql .= " KODE_PELAKSANA, PROJECT_ACTIVITY, SPK, ";
		$sql .= " PROJECT_START, PROJECT_END, PROJECT_QTY, UPPER(PROJECT_UOM) AS PROJECT_UOM, PROJECT_VALUE, PROJECT_PPN, PROJECT_NETTVAL, ";
		$sql .= " PROJECT_STATUS, TGL_TERBIT, COMPANY_CODE FROM m_project ".$where."";
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
    
    function cek_exist_data($code,$tableName,$company)
    {
        $sQuery = "SELECT * FROM ".$tableName." WHERE PROJECT_ID='".$code."' AND COMPANY_CODE ='".$company."'";
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
    }
	
    function insert_new_data($data,$tableName)
    {
        $this->db->insert($tableName,$data);
        return $this->db->insert_id();
    }
	
	function cek_exist_data_detail($code,$act,$company)
    {
        $sQuery = "SELECT * FROM m_project_detail WHERE MASTER_PROJECT_ID='".$code."' AND COMPANY_CODE ='".$company."' AND PROJECT_ACTIVITY = '".$act."'";
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
    }
	
    function insert_new_data_detail($data,$tableName)
    {
        $this->db->insert($tableName,$data);
        return $this->db->insert_id();
    }
	
    function update_data($id,$company,$data,$tableName)
    {
        $this->db->where('PROJECT_ID',$id);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update($tableName,$data);
    }
	
    function delete_data($id,$company,$tableName)
    {
        $this->db->where('PROJECT_ID',$id);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->delete($tableName);
    }
	
	function update_data_detail($id,$company,$data,$act)
    {
        $this->db->where('MASTER_PROJECT_ID',$id);
        $this->db->where('COMPANY_CODE',$company);
		$this->db->where('PROJECT_ACTIVITY',$act);
        $this->db->update('m_project_detail',$data);
    }
	
    function delete_data_detail($id, $act = '', $company,$tableName)
    {
        $this->db->where('MASTER_PROJECT_ID',$id);
        $this->db->where('COMPANY_CODE',$company);
		if($act != "-"){
			$this->db->where('PROJECT_ACTIVITY',$act);
		}
        $this->db->delete($tableName);
    }
	
	function get_afd($company)
	{
		$query = $this->db->query("SELECT AFD_CODE,AFD_DESC FROM m_afdeling WHERE COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	/* detail aktivitas */	
	function getdetailact($id, $company, $limit, $page, $sidx, $sord){
		
        $sql2 = "SELECT pj.ID, mpj.MASTER_PROJECT_ID, mpj.PROJECT_ACTIVITY, m_coa.COA_DESCRIPTION AS DESCRIPTION, pj.COMPANY_CODE ";
		$sql2 .= " FROM m_project pj RIGHT JOIN m_project_detail mpj ON mpj.MASTER_PROJECT_ID = pj.PROJECT_ID ";
		$sql2 .= " LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = mpj.PROJECT_ACTIVITY  ";
		$sql2 .= " WHERE ID = '".$id."' AND pj.COMPANY_CODE = '".$company."'";
		
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
		
		$sql = "SELECT pj.ID, mpj.MASTER_PROJECT_ID, mpj.PROJECT_ACTIVITY, m_coa.COA_DESCRIPTION AS DESCRIPTION, pj.COMPANY_CODE ";
		$sql .= " FROM m_project pj RIGHT JOIN m_project_detail mpj ON mpj.MASTER_PROJECT_ID = pj.PROJECT_ID ";
		$sql .= " LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = mpj.PROJECT_ACTIVITY  ";
		$sql .= " WHERE ID = '".$id."' AND pj.COMPANY_CODE = '".$company."'";
		if($count > 0) {
			$sql .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
		
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
        $act='';   
		$no = 0;                        
        foreach($objects as $obj)
        {
            $cell = array();
			array_push($cell, $no);
			array_push($cell, htmlentities($obj->MASTER_PROJECT_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->PROJECT_ACTIVITY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
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
	
	function get_project_num($company, $id)
	{
		$query = $this->db->query("SELECT DISTINCT PROJECT_ID, PROJECT_TYPE FROM m_project WHERE COMPANY_CODE = '".$company."' AND ID = ".$id."");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function get_project_act($q)
	{
		$query = $this->db->query("SELECT ACCOUNTCODE, ACCOUNTTYPE, COA_DESCRIPTION FROM m_coa WHERE ACCOUNTCODE LIKE '".$q."%'
			OR COA_DESCRIPTION LIKE '%".$q."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
}
?>
