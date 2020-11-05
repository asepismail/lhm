 <?php
class model_m_infras extends Model
{
	function model_m_infras()
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
        	
		$company = $this->db->escape_str($company);
		$sQuery ="SELECT DISTINCT IFCODE, IFNAME, mi.IFTYPE, mit.IFTYPE_NAME, mi.IFSUBTYPE, mis.IFSUBTYPE_NAME, ESTATE,IF_LOCATION, 
					IFLENGTH, IFWIDTH, UOM, INSTALLDATE, VOLUME, DEVELOPMENT_COST, mi.COMPANY_CODE, 
					CASE WHEN mi.INACTIVE = 0 THEN 1 ELSE 0 END AS ACTIVE, 
					CASE WHEN mloc.INACTIVE = 1 THEN 0 ELSE 1 END AS ISAPPR_RM 
					FROM m_infrastructure mi 
					LEFT JOIN m_infrastructure_type mit ON mit.IFTYPE = mi.IFTYPE 
					LEFT JOIN m_infrastructure_subtype mis ON mis.IFSUBTYPE = mi.IFSUBTYPE 
					LEFT JOIN ( SELECT LOCATION_CODE, INACTIVE, COMPANY_CODE FROM m_location WHERE COMPANY_CODE = '".$company."'
							AND LOCATION_TYPE_CODE = 'IF' ) mloc ON mloc.LOCATION_CODE = mi.IFCODE
					WHERE mi.COMPANY_CODE='".$company."'"; 
		
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
		
		$sQuery = $sQuery . " ORDER BY 1 LIMIT ".$start.",".$limit.""; 
					
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, htmlentities($obj->IFCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFTYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFTYPE_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFSUBTYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFSUBTYPE_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ESTATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IF_LOCATION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFLENGTH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFWIDTH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UOM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->INSTALLDATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->VOLUME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DEVELOPMENT_COST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR_RM,ENT_QUOTES,'UTF-8'));
			
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
	
	function src_data($code,$desc,$company)
	{
	    $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
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
		
		$where = " WHERE 1=1"; 
		if($code!='' && $code!='-') $where.= " AND mi.IFCODE LIKE '%$code%' "; 
		if($desc!='') $where.= " AND mi.IFNAME LIKE '%$desc%' "; 
		$where .= " AND mi.COMPANY_CODE='".$company."' ";
		
		$sQuery ="SELECT DISTINCT IFCODE, IFNAME, mi.IFTYPE, mit.IFTYPE_NAME, mi.IFSUBTYPE, mis.IFSUBTYPE_NAME, ESTATE,IF_LOCATION, 
					IFLENGTH, IFWIDTH, UOM, INSTALLDATE, VOLUME, DEVELOPMENT_COST, mi.COMPANY_CODE, 
					CASE WHEN mi.INACTIVE = 0 THEN 1 ELSE 0 END AS ACTIVE, 
					CASE WHEN mloc.INACTIVE = 0 THEN 1 ELSE 0 END AS ISAPPR_RM 
					FROM m_infrastructure mi 
					LEFT JOIN m_infrastructure_type mit ON mit.IFTYPE = mi.IFTYPE 
					LEFT JOIN m_infrastructure_subtype mis ON mis.IFSUBTYPE = mi.IFSUBTYPE 
					LEFT JOIN ( SELECT LOCATION_CODE, INACTIVE, COMPANY_CODE FROM m_location WHERE COMPANY_CODE = '".$company."'
							AND LOCATION_TYPE_CODE = 'IF' ) mloc ON mloc.LOCATION_CODE = mi.IFCODE". $where ." GROUP BY IFCODE"; 

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
		
	    $sQuery = $sQuery . " ORDER BY 1 LIMIT ".$start.",".$limit.""; 
			
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, htmlentities($obj->IFCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFNAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFTYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFTYPE_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFSUBTYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFSUBTYPE_NAME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ESTATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IF_LOCATION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFLENGTH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFWIDTH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->UOM,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->INSTALLDATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->VOLUME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DEVELOPMENT_COST,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR_RM,ENT_QUOTES,'UTF-8'));
			
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
        $param = $this->db->escape_str($param);
        $company = $this->db->escape_str($company);
        
		if ($param=="1")
		{
			$sQuery = "SELECT * FROM m_infrastructure WHERE IFCODE='".$id."' AND COMPANY_CODE='".$company."'";
		}
		elseif ($param=="2")
		{
			$sQuery = "SELECT * FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='IF' 
						AND COMPANY_CODE='".$company."'";
		}
		$query= $this->db->query($sQuery);
		$count= $query->num_rows();
		
		return $count;
	}
	
	function AddNew($data)
	{
        $status='';
        if(isset($data))
        {
            $this->db->insert('m_infrastructure',$data);
            $status=$this->db->insert_id();    
        } else{
            $status="data salah";
        }
		
		return $status;
	}
    
	function AddToOther($id,$desc,$company)
	{
        $id = $this->db->escape_str($id);
        $desc = $this->db->escape_str($desc);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='IF' AND COMPANY_CODE='".$company."' "; //cek data exist
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        if ($count > 0)    //jika data sudah terdapat pada database
        {
            $data['LOCATION_TYPE_CODE'] = "IF";
            $data['DESCRIPTION'] = $desc;
            
            $this->db->where('LOCATION_CODE',$id);
            $this->db->where('LOCATION_TYPE_CODE',"IF");
            $this->db->where('COMPANY_CODE',$company);
            $this->db->update('m_location',$data);   //maka update data
        }
        elseif($count <= 0) //jika data belum terdapat dalam database
        {
             $sQuery = "INSERT INTO m_location (LOCATION_CODE,LOCATION_TYPE_CODE,DESCRIPTION,COMPANY_CODE) 
                    VALUES('".$id."', 'IF', '".$desc."' , '".$company."')";
             $query = $this->db->query($sQuery);       // maka insert baru
             return $this->db->insert_id();
        }
	} 
	
	function EditData($id,$company,$data)
	{
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
         
		$this->db->where('IFCODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->update('m_infrastructure',$data);
		$cek = $this->db->affected_rows();
		if($cek > 0){
			$act = 0;
			if($data['ISAPPR_RM'] == 1){
				$act = 0;
			} else{
				$act = 1;
			}
			$this->db->where('LOCATION_CODE',$id);
			$this->db->where('COMPANY_CODE',$company);
			$this->db->set('INACTIVE',$act );
			$this->db->update('m_location');
		}
	}
    
	function DeleteData($id,$company)
	{
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
		$this->db->where('IFCODE',$id);
		$this->db->where('COMPANY_CODE',$company);
		$this->db->delete('m_infrastructure');
	}
    
    function DelToOther($id,$company)
    { 
        $id = $this->db->escape_str($id);
        $company = $this->db->escape_str($company);
        
        $sQuery ="SELECT LOCATION_CODE FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='IF' AND COMPANY_CODE='".$company."' "; //cek data exist
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
        $command = $this->db->escape_str($command);
        
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
	
	function get_fixedasset()
	{
		$query = $this->db->query("SELECT DISTINCT IFTYPE_NAME, IFTYPE FROM m_infrastructure_type");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	function get_ifcode($iftype)
	{
		$query = $this->db->query("SELECT IFSUBTYPE,IFSUBTYPE_NAME FROM m_infrastructure_subtype WHERE IFTYPE LIKE '%".$iftype."%'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
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
	
	/* detail pengajuan rm */
	
	function LoadDataRM($company, $periode)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        	
		$company = $this->db->escape_str($company);
		$sQuery ="SELECT RM_PENGAJUAN_ID,RM_TGL_PENGAJUAN,PERIODE,
					IFCODE,DESCRIPTION,
					CASE WHEN PENGAJUAN_STATUS = 1 THEN 'disetujui' ELSE 'proses' END AS PENGAJUAN_STATUS,
					ISAPPR1,ISAPPR2,COMPANY_CODE 
				FROM pms_rm_pengajuan WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$periode."'
				AND PENGAJUAN_STATUS = 1 AND ISAPPR2 = 1"; 
		
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
		if($count >0) { 
			$sQuery = $sQuery . " ORDER BY 1 LIMIT ".$start.",".$limit.""; 
		}		
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no_va = 1;
		$action = "";
        foreach($query as $obj)
        {
        	
            $cell = array();
			array_push($cell, $no_va);
			array_push($cell, htmlentities($obj->RM_PENGAJUAN_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RM_TGL_PENGAJUAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IFCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PENGAJUAN_STATUS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR1,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ISAPPR2,ENT_QUOTES,'UTF-8'));
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
	
	/* end pengajuan rm */
	
	function DelSyncDataInfrasLocation($company)
    {
        $company = $this->db->escape_str($company);
        $this->db->where('LOCATION_TYPE_CODE','IF');
        $this->db->where('COMPANY_CODE',$company);
        $this->db->delete('m_location');
        return $this->db->affected_rows();
    }
	
	function DelSyncDataInfras($company)
    {
        $company = $this->db->escape_str($company);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->delete('m_infrastructure');
        return $this->db->affected_rows();
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
	
	function SyncLocation($company_adem, $company){
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
	
	function SyncInrastructure($company, $user){
		$company_adem = $this->getCompanyIDAdem($company);	
		$status[] = null;
		$count = 0;
		$status = 0;
		$boolean_insert = 0;
    	$pgsql = $this->load->database('adem', TRUE);  
		$pgquery = "select ce.isactive, ce.value, SUBSTRING(ce.value, 5, LENGTH(ce.value)-4) AS ifcode, ce.name, 
					    infrastructuretype, infrastructuresubtype, infrastructureafd, c_uom.name as uomname, COALESCE(ce.dimension,0) AS dimensi
					    , condition, disetujui, 
					    CASE WHEN substring(ce.value,1,3) = 'PAI' THEN 'TPAI' ELSE substring(ce.value,1,3) END AS company_code
					from c_elementvalue ce 
					left join c_uom on c_uom.c_uom_id = ce.c_uom_id
					where ce.value LIKE '".$company."%' AND ce.isactive = 'Y'";
		
		$query = $pgsql->query($pgquery);
		$count = $query->num_rows();
		
		$this->benchmark->mark('code_start');
		if ($count > 0){
						
            foreach($query->result() as $key => $val){
            	if($val->disetujui == 'Y'){
					$val->disetujui = 1;
				} else {
					$val->disetujui = 0;
				}
				
				$iftype = $val->infrastructuretype;
				$ifsubtype = substr($val->ifcode,0,2);;
				/* cek subtipe */
				
				/* cek bangunan */
				$bng = array('BN','BS','BP');
				for($i=0; $i < count($bng); $i++){
					if(substr($val->ifcode,0,2) == $bng[$i]){
						$iftype = $val->infrastructuretype;
						$ifsubtype = substr($val->ifcode,2,3);
					}
				}
				/* end bangunan */
				
				/* cek jalan */
				$jalan = array('JC','JT','JU','JL','JH','JS');
				for($i=0; $i < count($jalan); $i++){
					if(substr($val->ifcode,0,2) == $jalan[$i]){
						$iftype = 'JA';
						$ifsubtype = substr($val->ifcode,0,2);
					}
				}
				/* end jalan */
				/* bangunan air */
				
				$bangunanair = array('WD','WG','TG','ON','OP','GV','DM','TB');
				for($i=0; $i < count($bangunanair); $i++){
					if(substr($val->ifcode,0,2) == $bangunanair[$i]){
						$iftype = 'BA';
						$ifsubtype = substr($val->ifcode,0,2);
					}
				}
				
				/* end bangunan air */
				
				/* bangunan utilitas */
				
				$bangunanutil = array('JAR','JAL','JSP','TBG','TTA','JAB');
				for($i=0; $i < count($bangunanutil); $i++){
					if(substr($val->ifcode,0,2) == $bangunanutil[$i]){
						$iftype = 'BU';
						$ifsubtype = substr($val->ifcode,0,2);
					}
				}
				
				/* end bangunan utilitas */
				
				/* bangunan parit */
				
				$parit = array('PU','PT','PC','OL','PS','PB','PL','PE');
				for($i=0; $i < count($parit); $i++){
					if(substr($val->ifcode,0,2) == $parit[$i]){
						$iftype = 'PR';
						$ifsubtype = substr($val->ifcode,0,2);
					}
				}
				
				/* end bangunan parit */
				
				/* bangunan jembatan */
				
				$jembatan = array('D0','GB','LG','BT','C0','D1','D2','JP','GP','TP','BB','LP','GK','GV','D0S','GBS','LGS','BTS','BBS','C0S','D1S','D2S','JPS','GPS','LPS','GKS','GVS');
				for($i=0; $i < count($jembatan); $i++){
					if(substr($val->ifcode,0,2) == $jembatan[$i]){
						$iftype = 'JB';
						$ifsubtype = substr($val->ifcode,0,2);
					}
				}
				
				/* end bangunan jembatan */

				/* end cek subtype */
            	$sInsert = "INSERT INTO m_infrastructure 
								(	
									IFCODE, IFNAME, IFTYPE, IFSUBTYPE, IFLENGTH, UOM,
									ESTATE, INPUT_BY, INPUT_DATE, INACTIVE, ISAPPR_RM,COMPANY_CODE
								 ) 
							VALUES('".$val->ifcode."', '".$val->name."', '".$iftype."','".$ifsubtype."',
								   ".$val->dimensi.", '".$val->uomname."','".$val->infrastructureafd."',
							   		'".$user."','". date ("Y-m-d H:i:s") ."', 0, ".$val->disetujui.",
							   		'".$company."'
							  	)";	
				
				$query = $this->db->query($sInsert);
				
				if( $query > 0 ) {
					$sInsert2 = "INSERT INTO m_location 
								(	
									LOCATION_CODE, LOCATION_TYPE_CODE, DESCRIPTION, INACTIVE, COMPANY_CODE
								 ) 
							VALUES('".$val->ifcode."','IF','".$val->name."', 1,'".$company."'
							  	)";	
					$this->db->query($sInsert2);
				}			  	
				
							  	
				$boolean_insert = $boolean_insert + $this->db->affected_rows();
					
					/* if ($boolean_insert > 0 ){
						$status[] ="Sinkron data blok infrastructure ".$val->ifcode." sukses";
					} */
			} 
			
		}
		$this->benchmark->mark('code_end');

		// echo $this->benchmark->elapsed_time('code_start', 'code_end');
		
		
		$this->db->close();
		return $boolean_insert;
    }
    
    //##################### 17 jan 2011 ######################
    //insert setiap daya yg di hapus ke table : master_history
    function insert_history($company, $user)
    {
        $query="SELECT IFCODE, IFTYPE,IFSUBTYPE,IFNAME,IFLENGTH,IFWIDTH,UOM,ESTATE,INACTIVE,INACTIVEDATE,COMPANY_CODE,
        		INPUT_BY,INPUT_DATE,UPDATE_BY,UPDATE_DATE,NOTES,ISAPPR_RM FROM m_infrastructure WHERE COMPANY_CODE='".$company."'";
        $sQuery=$this->db->query($query);
     
     	$insertHistory = '';
        $history_data = array();
            foreach ($sQuery -> result_array() as $row){                   
                    $history_data[] = $row;  
            }
       	
       	$insertHistory = json_encode($history_data);
		//return $temp_result; 
        $this->db->set('LOCATION_TYPE_CODE','IF');
        $this->db->set('HISTORY_DATA',	$insertHistory);
        $this->db->set('SYNC_DATE', date ("Y-m-d H:i:s") );
        $this->db->set('SYNC_BY',$user);
        $this->db->insert('master_sync_history') ;
        $result = $this->db->insert_id();
        return $this->db->affected_rows();
        //return $result;
    }
    //########################################################
    
    function doSyncInfras($company, $user){
    	$status = 0;
		/* insert data terdahulu ke history dalam format json */
		$insertToHistory = $this->insert_history($company,$user);
		
		/* Delete master data setelah masuk ke history */
		/* delete data infras */
		$syncLocation = $this->DelSyncDataInfrasLocation($company);
		
		/* delete data location infras */
		$syncInfras = $this->DelSyncDataInfras($company);
		 	
		/* tarik data adem dan insert ke M_infrastructure */
		$status = $this->SyncInrastructure($company,$user);
		
		return $status;
	}
}
?>