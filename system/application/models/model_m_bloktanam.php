<?php
class model_m_bloktanam extends Model
{
    function model_m_bloktanam()
    {
        parent::Model();
        $this->load->database();
    }
    function LoadData($company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT FIELDCODE,BLOCKID,ESTATECODE,DESCRIPTION,HECTPLANTED,HECTPLANTABLE
                    ,CROPSSTATUS,NUMPLANTATION,YEARREPLANT
                    FROM m_fieldcrop WHERE
                    COMPANY_CODE='".$company."'"; 
        
        if(!$sidx) $sidx =1;
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        
        if( $count >0 ) 
        {
            $total_pages = @(ceil($count/$limit));
		} else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
        
        $sQuery = $sQuery . " ORDER BY 1 LIMIT ".$start.",".$limit." "; 
        $query = $this->db->query($sQuery,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        $action = "";
        foreach($query as $obj)
        {
            $cell = array();
            array_push($cell, $no_va);
            array_push($cell, htmlentities($obj->FIELDCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCKID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ESTATECODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HECTPLANTED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HECTPLANTABLE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CROPSSTATUS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NUMPLANTATION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->YEARREPLANT,ENT_QUOTES,'UTF-8'));
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
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $code = $this->db->escape_str($code);
        $desc = $this->db->escape_str($desc);
        $company = $this->db->escape_str($company);
        
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
        if($code!='' && $code!='-') $where.= " AND FIELDCODE LIKE '%$code%'"; 
        if($desc!='') $where.= " AND DESCRIPTION LIKE '%$desc%'"; 
        $where .= " AND COMPANY_CODE = '".$company."'";
        
        $sQuery ="SELECT FIELDCODE,FIELDCODECONV,BLOCKID,ESTATECODE,DESCRIPTION,HECTPLANTED,HECTPLANTABLE,
                    CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By,Approved_Date
                    ,CROPSSTATUS,NUMPLANTATION,YEARREPLANT
                    FROM m_fieldcrop ". $where." ORDER BY 1 LIMIT ".$start.",".$limit." ";
        
        
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
        
        $sQuery ="SELECT FIELDCODE,FIELDCODECONV,BLOCKID,ESTATECODE,DESCRIPTION,HECTPLANTED,HECTPLANTABLE
                    ,CROPSSTATUS,NUMPLANTATION,YEARREPLANT,
                        CASE WHEN Approved =1
                            THEN 'APPROVED' 
                            ELSE ''END AS Approved,Approved_By,Approved_Date
                    FROM m_fieldcrop ". $where;
        $query = $this->db->query($sQuery,FALSE)->result();
        $temp_result=array();
        $rows=array();
        $no_va = 1;
        $action = "";
        foreach($query as $obj)
        {
            $cell = array();
            array_push($cell, $no_va);
            array_push($cell, htmlentities($obj->FIELDCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->FIELDCODECONV,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BLOCKID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ESTATECODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HECTPLANTED,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HECTPLANTABLE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->CROPSSTATUS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NUMPLANTATION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->YEARREPLANT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_By,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->Approved_Date,ENT_QUOTES,'UTF-8'));
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
    
    
    function AddNew($data)
    {
        $this->db->insert('m_fieldcrop',$data);
        return $this->db->insert_id();
    }
    
    function cek_exist_data($fCode,$company)
    {
        $fCode = $this->db->escape_str($fCode);
        
        $sQuery = "SELECT * FROM m_fieldcrop WHERE FIELDCODE='".$fCode."' AND COMPANY_CODE='".$company."'";
        $query=$this->db->query($sQuery);
        $count=$query->num_rows();
        
        return $count;
    }
    
    function EditData($wCode,$company,$data)
    {
        $wCode = $this->db->escape_str($wCode);
        $company = $this->db->escape_str($company);
        
        $this->db->where('FIELDCODE',$wCode);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update('m_fieldcrop',$data);
    }
    
    function DelData($wCode,$company)
    {
        $wCode = $this->db->escape_str($wCode);
        $company = $this->db->escape_str($company);
        $insert_history = $this->insert_history($id,$company,"m_fieldcrop","FIELDCODE",'OP');
		
        $this->db->where('FIELDCODE',$wCode);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->delete('m_fieldcrop');
    }
	
	function getCompanyIDAdem($company){
		
		if ($company=='TPAI'){
			$company = 'PAI' ;	
		}else{
			$company = 	$company;
		}
		$pgsql = $this->load->database('adem', TRUE);
		$pgquery = "SELECT ad_org_id as ret FROM ad_org WHERE value = '".$company."' LIMIT 1";
//var_dump($pgquery);
		$query = $pgsql->query($pgquery);
		
		$data = array_shift($query->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function SyncLocation($company_adem, $company, $afd, $block, $all){
		$status[] = null;
		$count = 0;
    	$pgsql = $this->load->database('adem', TRUE);  
		$pgquery = "SELECT CONCAT(substring(blok,5,5),SUBSTRING(tahun,3,2)) AS LOCATION_CODE, 'OP' AS LOCATION_TYPE_CODE, 
					CONCAT('Blok ', substring(blok,5,5) ,' Tahun Tanam ', tahun) AS DESCRIPTION, substring(blok,1,3) AS COMPANY_CODE  
					FROM rv_zblokthntanam
					WHERE ad_org_id = '".$company_adem."'";
		//var_dump($afd);			
		if ($block<>"null"){
			$pgquery = $pgquery . " AND CONCAT(substring(blok,5,5),SUBSTRING(tahun,3,2)) = '".$block."'";	
		}else if($afd<>"null"){
			$pgquery = $pgquery . " AND substring(blok,5,2) = '".$afd."'";		
		}
		
		$query = $pgsql->query($pgquery);
		$count = $query->num_rows();
		//var_dump($pgquery);
		if ($count > 0){
			if ($block<>"null"){				
				$this->db->where('LOCATION_CODE',$block);
				$this->db->where('LOCATION_TYPE_CODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_location');
			}else if($afd<>"null"){	
				$this->db->like('LOCATION_CODE',$afd);
				$this->db->where('LOCATION_TYPE_CODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_location');
			}else if($all==true){
				$this->db->where('LOCATION_TYPE_CODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_location');	
			}			

            foreach($query->result() as $key => $val){
				$sInsert = "INSERT INTO m_location 
								(LOCATION_CODE, LOCATION_TYPE_CODE, DESCRIPTION, COMPANY_CODE) 
							VALUES('".$val->location_code."', '".$val->location_type_code."', '".$val->description."','".$val->company_code."')";		
					$boolean_insert = $this->db->query($sInsert);
					if ($boolean_insert==true){
						$status ="Sinkron lokasi blok ".$val->location_code." sukses";
					}else{
						$status ="Sinkron lokasi blok ".$val->location_code." gagal";	
					}
			}
			
		}
		$this->db->close();
		return $status;
    }
	
	function SyncFieldCrop($company_adem, $company, $afd, $block, $all){
		$status[] = null;
		$count = 0;
    	$pgsql = $this->load->database('adem', TRUE);  
		$pgquery = "SELECT CONCAT(substring(blok,5,5),SUBSTRING(tahun,3,2)) AS FIELDCODE, 
					substring(blok,5,5) AS BLOCKID, 'OP' AS ESTATECODE, 
					CONCAT('Blok ', substring(blok,5,5), ' Tahun Tanam ', tahun) AS DESCRIPTION, 
					PLANTEDAREA AS HECTPLANTED, PLANTABLEAREA AS HECTPLANTABLE, PLANTABLEAREA AS TOTALHECTARAGE, 
					COALESCE(jumlahpokok,0) AS NUMPLANTATION, tahun AS YEARREPLANT,  substring(blok,1,3) AS COMPANY_CODE
					FROM rv_zblokthntanam
					WHERE ad_org_id = '".$company_adem."'";
		
		if ($block<>"null"){
			$pgquery = $pgquery . " AND CONCAT(substring(blok,5,5),SUBSTRING(tahun,3,2)) = '".$block."'";	
		}else if($afd<>"null"){
			$pgquery = $pgquery . " AND substring(blok,5,2) = '".$afd."'";		
		}

		$query = $pgsql->query($pgquery);
		$count = $query->num_rows();
		if ($count > 0){
			if ($block<>"null"){			
				//var_dump($block);
				$this->db->where('FIELDCODE',$block);
				$this->db->where('ESTATECODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_fieldcrop');
			}else if($afd<>"null"){	
				$this->db->like('FIELDCODE',$afd);
				$this->db->where('ESTATECODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_fieldcrop');
			}else if($all==true){
				$this->db->where('ESTATECODE','OP');
				$this->db->where('COMPANY_CODE',$company);
				$this->db->delete('m_fieldcrop');	
			}
			
            foreach($query->result() as $key => $val){
				$sInsert = "INSERT INTO m_fieldcrop 
								(FIELDCODE, BLOCKID, ESTATECODE, DESCRIPTION, 
								 HECTPLANTED, HECTPLANTABLE, TOTALHECTARAGE, CROPSSTATUS, 
								 NUMPLANTATION, YEARREPLANT, AGE, COMPANY_CODE, 
								 INPUT_BY, INPUT_DATE, LAST_SYNCHRONIZE_DATE
								 ) 
							VALUES('".$val->fieldcode."', '".$val->blockid."', '".$val->estatecode."','".$val->description."',
								   ".$val->hectplanted.", ".$val->hectplantable.",".$val->totalhectarage.",'NO STATUS',
							   		".$val->numplantation.",'".$val->yearreplant."', 1, '".$val->company_code."',
							   		'".trim(htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8'))."', now(), now()
							  	)";		
					$boolean_insert = $this->db->query($sInsert);
					
					if ($boolean_insert==true){
						$status ="Sinkron data blok tanah ".$val->fieldcode." sukses";
					}	
			}
			
		}
		$this->db->close();
		return $status;
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
	
    function AddToOther($fieldcode,$desc,$company)
    {
        $fieldcode = $this->db->escape_str($fieldcode);
        $desc = $this->db->escape_str($desc);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$fieldcode."' AND LOCATION_TYPE_CODE='OP' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "OP";
            $data['DESCRIPTION'] = $desc;
            
            $this->db->where('LOCATION_CODE',$fieldcode);
            $this->db->where('LOCATION_TYPE_CODE',"OP");
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        }
        elseif($count <= 0) //jika data belum terdapat dalam database
        {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,COMPANY_CODE) 
                    VALUES('".$fieldcode."', 'OP', '".$desc."' , '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             return $this->db->insert_id();
        }
    }
    
    function DelToOther($id,$company)
    {
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='OP' AND COMPANY_CODE='".$company."' "; //cek data exist
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
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        $locTypeCode = $this->db->escape_str($locTypeCode);
        
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