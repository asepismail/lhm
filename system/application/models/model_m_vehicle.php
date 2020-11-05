<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 


class model_m_vehicle extends Model
{
	function model_m_vehicle()
	{
		parent::model();
		$this->load->database();
	}
	
	function LoadView($company)
	{
        $company=$this->db->escape_str($company);
		$limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        $limit=($limit==0 | $limit==null)?1:$limit;
		
		$sQuery ="SELECT VEHICLECODE,REGISTRATIONNO,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,CONTACTNAME,
                    CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,
                    Approved_BY ,Approved_DATE
                     FROM m_vehicle WHERE COMPANY_CODE='".$company."' AND INACTIVE = 0";
		
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
        
		$sQuery ="SELECT VEHICLECODE,REGISTRATIONNO,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,CONTACTNAME,
                    CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,
                    Approved_BY ,Approved_DATE
		   	      FROM m_vehicle WHERE COMPANY_CODE='".$company."' AND INACTIVE = 0 ORDER BY 1 LIMIT ".$start.",".$limit."";
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
        
		$act = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, htmlentities($no_va,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->VEHICLECODE,ENT_QUOTES,'UTF-8'));
	        array_push($cell, htmlentities($obj->REGISTRATIONNO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->OWNERSHIP,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CONTACTNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SATUAN_PRESTASI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved_BY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved_DATE,ENT_QUOTES,'UTF-8'));
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
	
	function src_data($code,$desc,$appr,$company)
	{
		$limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
		
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
		
		if (isset($appr)){
			$appr = $appr;
		} else {
			$appr = "";
		}
		
		$where = "WHERE 1=1"; 
		if($code!='' && $code!='-') $where.= " AND VEHICLECODE LIKE '%$code%'"; 
		if($desc!='' && $desc!='-') $where.= " AND DESCRIPTION LIKE '%$desc%'"; 
		if($appr!='') $where.= " AND Approved = '$appr'";
		$where .= " AND COMPANY_CODE = '".$company."' AND INACTIVE = 0";
		
		$sQuery ="SELECT VEHICLECODE,REGISTRATIONNO,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,CONTACTNAME,
					CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,
                    Approved_BY,Approved_DATE
				  FROM m_vehicle ". $where;

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
		
	    $sQuery ="SELECT VEHICLECODE,REGISTRATIONNO,DESCRIPTION,OWNERSHIP,SATUAN_PRESTASI,CONTACTNAME,
					CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved
                    ,Approved_BY,Approved_DATE 
				  FROM m_vehicle ". $where." ORDER BY 1 LIMIT ".$start.",".$limit."";
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
        
		$act = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, htmlentities($no_va,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->VEHICLECODE,ENT_QUOTES,'UTF-8'));
	        array_push($cell, htmlentities($obj->REGISTRATIONNO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->OWNERSHIP,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->CONTACTNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SATUAN_PRESTASI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved_BY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->Approved_DATE,ENT_QUOTES,'UTF-8'));
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
	
	function cek_exist_vehicle($vhccode,$company)
	{
        $vhccode=htmlentities($this->db->escape_str($vhccode),ENT_QUOTES,'UTF-8');
        $company=$this->db->escape_str($company);
        
		$sQuery = "SELECT * FROM m_vehicle WHERE VEHICLECODE='".$vhccode."' AND COMPANY_CODE='".$company."'";
		$query = $this->db->query($sQuery);
		$count = $query->num_rows();
		return $count;
	}
	
	function insert_new_vehicle($data)
	{
        $insert_id='';
        if(isset($data))
        {
            $this->db->insert('m_vehicle',$data);
            $insert_id=$this->db->insert_id();    
        }else{
            $insert_id="error";    
        }
		
		return $insert_id;
	}
	
	function update_vehicle($id,$company,$data)
	{
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        
		$this->db->where('VEHICLECODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('m_vehicle',$data);
	}
	
	function delete_vehicle($id,$company)
	{
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
		$insert_history = $this->insert_history($id,$company,"m_vehicle","VEHICLECODE",'VH');
		$this->db->set('INACTIVE', 1);
		$this->db->set('INACTIVEDATE', date ("Y-m-d"));
		$this->db->where('VEHICLECODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('m_vehicle');
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
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $desc=htmlentities($desc,ENT_QUOTES,'UTF-8');
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='VH' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "VH";
            $data['DESCRIPTION'] = $desc;
            
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
            
        }
        elseif($count <= 0) //jika data belum terdapat dalam database
        {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,COMPANY_CODE) 
                    VALUES('".$id."', 'VH', '".$desc."' , '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             return $this->db->insert_id();
        }
	}
	function DelToOther($id,$company)
    {
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='VH' AND COMPANY_CODE='".$company."' "; //cek data exist
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
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        $locTypeCode=htmlentities($this->db->escape_str($locTypeCode),ENT_QUOTES,'UTF-8');
         
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
	/*function cek_approve($uName,$uPass)
	{
		
		if (!(isset($uName)))
		{
			$uName="";
		}
		else
		{
			$uName=mysql_escape_string($uName) ;
		}
		
		if (!(isset($uPass)))
		{
			$uPass="";
		}else
		{
			$uPass=mysql_escape_string($uPass);
		}

		$sQuery="SELECT m_user_co.USERID,m_user_co.COMPANY_CODE,m_user.LOGINID,m_user.USER_FULLNAME,m_user.USER_PASS,
                m_user.USER_MAIL,m_user.USER_LEVEL,m_user.USER_DEPT,m_user.ROLE_APPROVE 
                FROM m_user_co
                INNER JOIN m_user ON m_user_co.USERID = m_user.LOGINID
                WHERE  m_user_co.USERID='".$uName."' AND m_user.USER_PASS='".$uPass."'
                        AND m_user.ROLE_APPROVE='1'
                GROUP BY m_user_co.USERID, m_user_co.COMPANY_CODE";
		$query=$this->db->query($sQuery);
		$count=$query->num_rows();
		if ($count>0)
		{
			return $count;
		}
		else{
			return $count;
		}
	}
	
	function update_approve($id,$company,$data)
	{
		$this->db->where('VEHICLECODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('m_vehicle',$data);
	}*/
}
?>