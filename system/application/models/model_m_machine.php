<?php
class model_m_machine extends Model
{
    function model_m_machine()
    {
        parent::model();
        $this->load->database();
    }
    
    function LoadData($company)
    {
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        $limit=($limit==0 | $limit==null)?1:$limit;
        
        $sQuery ="SELECT MACHINECODE,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE '' END AS Approved,Approved_By,Approved_Date
                  FROM m_machine WHERE COMPANY_CODE='".$company."' AND INACTIVE = 0";
        
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
        
        $sQuery ="SELECT MACHINECODE,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE '' END AS Approved,Approved_By,Approved_Date  
                  FROM m_machine WHERE COMPANY_CODE='".$company."' AND INACTIVE = 0 ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
        $query = $this->db->query($sQuery,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        
        $act = "";
        foreach($query as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($no_va,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MACHINECODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->OWNERSHIP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SATUAN_PRESTASI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_By,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_Date,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            
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
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx',ENT_QUOTES,'UTF-8'));
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $code = htmlentities($this->db->escape_str($code),ENT_QUOTES,'UTF-8') ;
        $desc = htmlentities($this->db->escape_str($desc),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        //$limit=($limit==0 | $limit==null)?1:$limit;  
        
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
        if($code!='' && $code!='-') $where.= " AND MACHINECODE LIKE '%$code%'"; 
        if($desc!='') $where.= " AND DESCRIPTION LIKE '%$desc%'"; 
        $where .= " AND COMPANY_CODE = '".$company."'";
        
        $sQuery ="SELECT MACHINECODE,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By,Approved_Date
                  FROM m_machine ". $where;
        
        
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
        
        $sQuery ="SELECT MACHINECODE,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By,Approved_Date  
                  FROM m_machine ". $where ." WHERE INACTIVE = 0 ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
        $query = $this->db->query($sQuery,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        
        $act = "";
        foreach($query as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($no_va,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MACHINECODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->OWNERSHIP,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SATUAN_PRESTASI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_By,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_Date,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($act,ENT_QUOTES,'UTF-8'));
            
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
    
    function cek_exist_machine($id,$company)
    {
        $id =$this->db->escape_str($id);
        $company=$this->db->escape_str($company);
        
        $sQuery = "SELECT * FROM m_machine WHERE MACHINECODE ='".$id."' AND COMPANY_CODE='" .$company. "'";
        $query=$this->db->query($sQuery);
        $count=$query->num_rows();
        return $count;
    }
    
    function insert_new_machine($data)
    {
        if(isset($data))
        {
            $this->db->insert('m_machine',$data);           
        }
        return $this->db->insert_id();  
    }
    
    function update_machine($id,$company,$data)
    {
        if (isset($data) && isset($id) && isset($company))
        {
            $id =$this->db->escape_str($id);
            $company=$this->db->escape_str($company);
            $this->db->where('MACHINECODE',$id);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_machine',$data);   
        }   
    }
    
    function DelData($id,$company)
    {
        if (isset($id) && isset($company))
        {
            $id =$this->db->escape_str($id);
            $company=$this->db->escape_str($company);
			$insert_history = $this->insert_history($id,$company,"m_machine","MACHINECODE",'MA');
			$this->db->set('INACTIVE', 1);
			$this->db->set('INACTIVEDATE', date ("Y-m-d"));
            $this->db->where('MACHINECODE',$id);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_machine');
        }
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
        $id =$this->db->escape_str($id);
        $company=$this->db->escape_str($company);
        $desc=htmlentities($desc,ENT_QUOTES,'UTF-8');
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='MA' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "MA";
            $data['DESCRIPTION'] = $desc;
            
			$this->db->set('INACTIVE', 1);
			$this->db->set('INACTIVEDATE', date ("Y-m-d"));
			
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('LOCATION_TYPE_CODE',"MA");
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        }
        elseif($count <= 0) //jika data belum terdapat dalam database
        {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,COMPANY_CODE) 
                    VALUES('".$id."', 'MA', '".$desc."' , '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             //return $this->db->insert_id();
        } 
    }
	
    function DelToOther($id,$company)
    {
        $id =$this->db->escape_str($id);
        $company=$this->db->escape_str($company);
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='MA' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
			$this->db->set('INACTIVE', 1);
			$this->db->set('INACTIVE_DATE', date ("Y-m-d"));
			
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location');
        }
        
    }
    function UpdateApprMloc($id,$company,$locTypeCode,$command)
    {
        $id =$this->db->escape_str($id);
        $company=$this->db->escape_str($company);
        $locTypeCode =$this->db->escape_str($locTypeCode);

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