<?php
class model_m_workshop extends Model
{
	function model_m_workshop()
	{
		parent::Model();
		$this->load->database();
	}
	
	
	function LoadData($company)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$sQuery = "SELECT WORKSHOPCODE, DESCRIPTION,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By, Approved_Date FROM m_workshop WHERE COMPANY_CODE='".$company."'";
		$query = $this->db->query($sQuery);
		
		if(!$sidx) $sidx =1;
		$query = $this->db->query($sQuery);
		$count = $query->num_rows();
		
        if( $count >0 ) 
		{
            $total_pages = @(ceil($count/$limit));
        } 
		else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;
        $this->db->limit($limit, $start);
		
		$sQuery = "SELECT WORKSHOPCODE, DESCRIPTION,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By, Approved_Date FROM m_workshop WHERE COMPANY_CODE='".$company."'";
		$query = $this->db->query($sQuery);
		
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, $obj->WORKSHOPCODE);
			array_push($cell, $obj->DESCRIPTION);
            array_push($cell, $obj->Approved);
            array_push($cell, $obj->Approved_By);
            array_push($cell, $obj->Approved_Date);
            $row = new stdClass();
            $row->id = $cell[1];
            $row->cell = $cell;
            array_push($rows, $row);
			$no_va++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
	}
	
	function src_data($code,$desc,$company)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		if (isset($code)){
			$code = $code;
		} else {
			$code = "";
		}
			
		if (isset($desc)){
			$desc = $desc;
		} else {
			$desc = "";
		}
		
		$where = "WHERE 1=1"; 
		if($code!='' && $code!='-') $where.= " AND WORKSHOPCODE LIKE '%$code%'"; 
		if($desc!='') $where.= " AND DESCRIPTION LIKE '%$desc%'"; 
		$where .= " AND COMPANY_CODE = '".$company."'";
		
		$sQuery ="SELECT WORKSHOPCODE,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By,Approved_Date DESCRIPTION FROM m_workshop ". $where;
		
		
		if(!$sidx) $sidx =1;
		$query = $this->db->query($sQuery);
		$count = $query->num_rows();
		
        if( $count >0 ) 
		{
            $total_pages = @(ceil($count/$limit));
        } 
		else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
		
	    $sQuery ="SELECT WORKSHOPCODE, DESCRIPTION, 
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved, Approved_By, Approved_Date FROM m_workshop ". $where;
		
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, $obj->WORKSHOPCODE);
			array_push($cell, $obj->DESCRIPTION);
            array_push($cell, $obj->Approved);
            array_push($cell, $obj->Approved_By);
            array_push($cell, $obj->Approved_Date);
            $row = new stdClass();
            $row->id = $cell[1];
            $row->cell = $cell;
            array_push($rows, $row);
			$no_va++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
	}
	
	
	function cek_exist_data($wCode, $company)
	{
		$sQuery="SELECT * FROM m_workshop WHERE WORKSHOPCODE='".$wCode."' AND COMPANY_CODE='".$company."'";
		$query=$this->db->query($sQuery);
		$count=$query->num_rows();
		
		return $count;
	}
	
	function AddNew($data)
	{
		$this->db->insert('m_workshop',$data);
		return $this->db->insert_id();
	}
	
	function EditData($id,$company,$data)
	{
		$id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
		$this->db->where('WORKSHOPCODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('m_workshop',$data);
	}
	
	function DeleteData($id,$company)
	{
		$id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
		$insert_history = $this->insert_history($id,$company,"m_workshop","WORKSHOPCODE",'WS');
		
		$this->db->where('WORKSHOPCODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->delete('m_workshop');
	}
	//##################### 17 jan 2011 ######################
    //insert setiap daya yg di hapus ke table : master_history
    function insert_history($master_code,$company,$master_table,$master_key,$loc_type_code=null)
    {
        $master_code =$this->db->escape_str($master_code);
        $company=$this->db->escape_str($company);
        $master_table =$this->db->escape_str($master_table);
        $master_key=$this->db->escape_str($master_key);
        $user = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        
        $query="SELECT * FROM ".$master_table." WHERE ".$master_key."='".$master_code."' AND COMPANY_CODE='".$company."'";
        $sQuery=$this->db->query($query);
        
        $result='';
        if($sQuery->num_rows() > 0)
        {
            $history_data ='';
            foreach ($sQuery->list_fields() as $field)
            {
                foreach ($sQuery -> result_array() as $row)
                {
                    if(trim($row[$field])=='' || trim($row[$field])==null || empty($row[$field]))
                    {
                        $row_field = 'NULL';    
                    }else{
                       $row_field = $row[$field]; 
                    }
                    
                    $history_data .= "-".$field.":".$row_field;  
                }
            }
            $this->db->set('LOCATION_TYPE_CODE',$loc_type_code);
            $this->db->set('MASTER_CODE',$master_code);
            $this->db->set('HISTORY_DATA',$history_data);
            $this->db->set('INPUT_BY',$user);
            $this->db->insert('master_history') ;
            $result = $this->db->insert_id();
        }else{
            $result='none';    
        }
        //return $result;
    }
    //########################################################
	
	function AddToOther($id,$desc,$company)
	{
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='WS' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "WS";
            $data['DESCRIPTION'] = $desc;
            
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('LOCATION_TYPE_CODE',"WS");
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        }
        elseif($count <= 0) //jika data belum terdapat dalam database
        {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,COMPANY_CODE) 
                    VALUES('".$id."', 'WS', '".$desc."' , '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             return $this->db->insert_id();
        }
	}
    function DelToOther($id,$company)
    {

        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='WS' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->delete('m_location');
        }
        
    }
    function UpdateApprMloc($id,$company,$locTypeCode,$command)
    {
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='".$locTypeCode."' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            if ($command==0) // 0 =inactive
            {
                 $data['INACTIVE'] =0;  
            }
            elseif($command==1)
            {
                 $data['INACTIVE'] =1;   
            }
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('LOCATION_TYPE_CODE',$locTypeCode);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        }   
    }
}
?>