<?php

class model_m_nursery extends Model 
{

    function model_m_nursery()
    {
        parent::Model(); 
        $this->load->database();
    }

    function AddNew ( $data )
    {
        $this->db->insert( 'm_nursery', $data );
        return $this->db->insert_id();   
    }
    
    function EditData ( $id,$company, $data )
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $this->db->where( 'NURSERYCODE', $id ); 
        $this->db->where('COMPANY_CODE',$company) ;
        $this->db->update( 'm_nursery', $data );   
    }
    
    function DeleteData($id,$company)
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        $insert_history = $this->insert_history($id,$company,"m_nursery","NURSERYCODE",'NS');
		
        $this->db->where('COMPANY_CODE',$company);
        $this->db->where('NURSERYCODE', $id);
        $this->db->delete('m_nursery'); 
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
    
    function LoadData($company, $limit, $page, $sidx, $sord)
    {
        $company = $this->db->escape_str($company);
        
        $sQuery = "SELECT BATCH_ID, NURSERYCODE,DESCRIPTION,DATEPLANTED,VARIETAS,QTYORDERED,QTYONHAND,QTYONHOLD, ";
		$sQuery .= " INACTIVE,INACTIVE_DATE,COMPANY_CODE FROM m_nursery WHERE COMPANY_CODE='".$company."'"; 
       
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
        
        $sQuery2  = "SELECT BATCH_ID, NURSERYCODE,DESCRIPTION,DATEPLANTED,VARIETAS,QTYORDERED,QTYONHAND,QTYONHOLD, ";
        $sQuery2 .= "INACTIVE,INACTIVE_DATE,COMPANY_CODE FROM m_nursery WHERE COMPANY_CODE='".$company."' ";
		if($count >0) {
		 	$sQuery2 .= " ORDER BY 1 LIMIT ".$start.",".$limit.""; 
		}
		
		$q1 = $this->db->query($sQuery2,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        $action = "";
        foreach($q1 as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->BATCH_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NURSERYCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATEPLANTED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VARIETAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYORDERED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYONHAND,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYONHOLD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->INACTIVE_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));    
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
    
    function src_data($code,$desc,$company, $limit, $page, $sidx, $sord)
    {       
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
        if($code!='' && $code!='-') $where.= " AND NURSERYCODE LIKE '%$code%'"; 
        if($desc!='') $where.= " AND DESCRIPTION LIKE '%$desc%'"; 
        $where .= " AND COMPANY_CODE = '".$company."'";
        
		$sQuery = "SELECT BATCH_ID, NURSERYCODE,DESCRIPTION,DATEPLANTED,VARIETAS,QTYORDERED,QTYONHAND,QTYONHOLD, ";
		$sQuery .= " INACTIVE,INACTIVE_DATE,COMPANY_CODE FROM m_nursery " . $where ;
		
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        
        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
        
        $sQuery2 = "SELECT BATCH_ID, NURSERYCODE,DESCRIPTION,DATEPLANTED,VARIETAS,QTYORDERED,QTYONHAND,QTYONHOLD, ";
		$sQuery2 .= " INACTIVE,INACTIVE_DATE,COMPANY_CODE FROM m_nursery " . $where ;
        
		if( $count >0 ) {
			 $sQuery2 . " ORDER BY 1 LIMIT ".$start.",".$limit."";
		}
		
        $q1 = $this->db->query($sQuery2,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        $action = "";
        foreach($q1 as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->BATCH_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NURSERYCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DATEPLANTED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->VARIETAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYORDERED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYONHAND,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTYONHOLD,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->INACTIVE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->INACTIVE_DATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8')); 
            
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
    
    
    function cek_exist_data($id,$company,$param)
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        if ($param =="1")
        {
            $sQuery = "SELECT * FROM m_nursery WHERE NURSERYCODE='".$id."' AND COMPANY_CODE='".$company."'";
        }
        elseif ($param=="2")
        {
            $sQuery = "SELECT * FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='NS'
                        AND COMPANY_CODE='".$company."'";
        }
        
        $query=$this->db->query($sQuery);
        $count=$query->num_rows();
        
        return $count;
    }
    
    function AddToOther($id,$desc,$active,$company)
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='NS' AND COMPANY_CODE='".$company."'"; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "NS";
            $data['DESCRIPTION'] = $desc;
			$data['INACTIVE'] = $active;
            
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('LOCATION_TYPE_CODE',"NS");
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        } elseif($count <= 0) {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION, INACTIVE, COMPANY_CODE) 
                    VALUES('".$id."', 'NS', '".$desc."' , '".$active."', '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             return $this->db->insert_id();
        }
    } 
	
    function DelToOther($id,$company)
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE = '".$id."' AND LOCATION_TYPE_CODE='NS' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
		echo $count;
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('COMPANY_CODE',$company);
			$this->db->where('LOCATION_TYPE_CODE','NS');
            $this->db->delete('m_location');
        }
        
    }
    
}   

?>
