<?

class Model_vehicle_activity extends Model 
{
    function Model_vehicle_activity(){
        parent::Model(); 
        $this->load->database();
    }
    
    function insert_vehicle_activity ( $data )
    {
        if(isset($data))
        {
             $this->db->insert( 'p_vehicle_activity', $data );
             return $this->db->insert_id();   
        }    
    }
    
    function update_vehicle_activity ( $id,$company, $data )
    {
        $update='';
        $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
        if(isset($id) && isset($company) && isset($data))
        {
            $this->db->where( 'ID',$id ); 
            $this->db->where( 'COMPANY_CODE',$company);       
            $update=$this->db->update( 'p_vehicle_activity', $data );  
               
        }else{
            $update='update failure';   
        }
        return $update; 
    }
    
    function delete_vehicle_activity ( $id, $company)
    {
        if(isset($id) && isset($company))
        {
            $id=htmlentities($this->db->escape_str($id),ENT_QUOTES,'UTF-8');
            $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8');
            $this->db->where( 'ID', $id );      
            $this->db->where( 'COMPANY_CODE', $company);      
            $this->db->delete('p_vehicle_activity');
            return $this->db->affected_rows(); 
        }    
    }
    
    function grid_vc($vc, $bln, $thn, $company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $sql2 = "SELECT * from p_vehicle_activity WHERE KODE_KENDARAAN = '".$this->db->escape_str($vc)."' 
						AND BULAN = '".$this->db->escape_str($bln)."' AND TAHUN = '".$this->db->escape_str($thn)."' 
						AND COMPANY_CODE='".$this->db->escape_str($company)."'";
       
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

        $this->db->limit($limit, $start);
        if($count >0) { 
			$sql2 .= "ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
		

        $objects = $this->db->query($sql2,FALSE)->result(); 
        $rows =  array();
               
        $no_va = 1;
        $action = "";
        foreach($objects as $obj)
        {
            $cell = array();
                            array_push($cell, htmlentities($no_va,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->ID,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->KODE_KENDARAAN,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->BULAN,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->TAHUN,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->TGL_AKTIVITAS,ENT_QUOTES,'UTF-8'));
                           // array_push($cell, htmlentities($obj->JAM_BERANGKAT,ENT_QUOTES,'UTF-8'));
                           // array_push($cell, htmlentities($obj->JAM_KEMBALI,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->JAM_KERJA,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->KMHM_BERANGKAT,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->KMHM_KEMBALI,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->KMHM_JUMLAH,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
							array_push($cell, htmlentities($obj->SUB_ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->PRESTASI_VOL,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->PRESTASI_SAT,ENT_QUOTES,'UTF-8'));
							array_push($cell, htmlentities($obj->PRESTASI_VOL2,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->PRESTASI_SAT2,ENT_QUOTES,'UTF-8'));
							array_push($cell, htmlentities($obj->MUATAN_JENIS,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->MUATAN_VOL,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($obj->MUATAN_SAT,ENT_QUOTES,'UTF-8'));
                            array_push($cell, htmlentities($action,ENT_QUOTES,'UTF-8'));
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
            
    //autocomplete
    function kode_kend($cv, $company){
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
        $qryor = " AND COMPANY_CODE='".$this->db->escape_str($company)."' AND INACTIVE = 0";
		
		$qry = "SELECT VEHICLECODE, DESCRIPTION, SATUAN_PRESTASI FROM m_vehicle 
					WHERE VEHICLECODE like '".$this->db->escape_str($cv)."%'" . $qryor .
					" OR DESCRIPTION LIKE '%".$this->db->escape_str($cv)."%'" . $qryor;
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function location_type(){
        $q = htmlentities($this->input->post('q'),ENT_QUOTES,'UTF-8');
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
        
        $query = $this->db->query("SELECT LOCATION_TYPE_CODE, LOCATION_TYPE_NAME FROM m_location_type WHERE ACTIVE=1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    
    function location($loc, $q, $company){
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      	
		$qryor = "AND LOCATION_TYPE_CODE = '".$this->db->escape_str($loc)."' AND COMPANY_CODE = '".$this->db->escape_str($company)."' AND INACTIVE=0";
		$qry = "SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_CODE LIKE '".$q."%' ".$qryor."";
		$qry .= " OR DESCRIPTION LIKE '%".$q."%' ".$qryor."";
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function location_pj($q, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
	  	$qryor = " AND COMPANY_CODE = '".$this->db->escape_str($company)."' AND PROJECT_STATUS = 1";
		$qry = "SELECT PROJECT_ID AS LOCATION_CODE, CONCAT(PROJECT_DESC,' : ',PROJECT_LOCATION) AS DESCRIPTION FROM m_project";
		$qry .= " WHERE PROJECT_ID LIKE '".$this->db->escape_str($q)."%'" . $qryor . " ";
		$qry .= " OR CONCAT(PROJECT_DESC,' : ',PROJECT_LOCATION) LIKE '%". $this->db->escape_str($q)."%'" . $qryor . " ";
        $query = $this->db->query($qry);
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function activity($act, $lc, $q){
    	$limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$filtact = "";
		if($act == "IF"){
			/* switch (substr($lc,0,2)) {
				case "BP": case "BS": case "BN":
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8152%'"; break;
				case "JA": case "JS": case "TB":
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8162%'"; break;
				case "WD": case "WG": case "TG": case "ON": case "OP": case "GV": case "DM":
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8132%'"; break;
				case "JC": case "JU": case "JT": case "JH": case "JL": 
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8112%'"; break;
				case "D0": case "GB": case "LG": case "BT": case "BB": case "C0": case "D1":
				case "D2": case "JP": case "GP": case "LP": case "GK": case "GV": 
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8142%'"; break;
				case "PU": case "PT": case "PC": case "OL": case "PS": case "PB": case "PU": case "PE": 
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '8122%'"; break;
				default:
				$filtact = " WHERE m.ACCOUNT_CODE LIKE '%".$q."%'";
				break;
			} */
			/* NOT USED SUDAH MAP KE TABLE */
			$qry = "SELECT mp.INFRAS_ACTIVITY_CODE AS ACCOUNTCODE, c.COA_DESCRIPTION, mc.UNIT1 AS UNIT1, mc.UNIT2 AS UNIT2
						FROM m_infrastructure ifs
					LEFT JOIN m_infrastructure_type tp ON tp.IFTYPE = ifs.IFTYPE
					LEFT JOIN m_activity_infras_map mp ON mp.INFRAS_PJ_SUBTYPE = ifs.IFTYPE
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = mp.ACCOUNTCODE
					LEFT JOIN m_progress_map mc ON mc.ACCOUNTCODE = mp.INFRAS_ACTIVITY_CODE
					WHERE IFCODE = '".$lc."' AND COMPANY_CODE = '".$company."' AND ISSUBTYPE = 0";
		} else { 
			  if ( $act == "NS" ) {
				  switch (substr($lc,0,2)) {
					  case "PN": 
					  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8302%'"; break;
					  case "MN": case "JS": case "TB":
					  $filtact = " WHERE m.ACCOUNT_CODE LIKE '8303%'"; break;
					  default:
					  $filtact = " WHERE m.ACCOUNT_CODE LIKE '%".$q."%'";
					  break;
				  }
			  } else {
				  $filtact = " WHERE m.ACCOUNT_CODE LIKE '%".$q."%'";
			  }
			  
			  $qryor = " m.LOCATION_TYPE = '".$this->db->escape_str($act)."' AND m.STATUS_PENGGUNAAN IN ('BK','BKP')";
			  
			  $qry = "SELECT DISTINCT m.ACCOUNT_CODE as ACCOUNTCODE, m_coa.COA_DESCRIPTION as COA_DESCRIPTION, mp.UNIT1 AS UNIT1, mp.UNIT2 AS UNIT2 from m_activity_map m ";
			  $qry .= " LEFT JOIN m_coa on (m_coa.ACCOUNTCODE = m.ACCOUNT_CODE) ";
			  $qry .= " LEFT JOIN m_progress_map mp ON mp.ACCOUNTCODE = m.ACCOUNT_CODE";
			  $qry .= $filtact . " AND " . $qryor;
			  $qry .= " OR m_coa.COA_DESCRIPTION LIKE '%".$q."%' AND ". $qryor;
		}
		
		$query = $this->db->query($qry);
        	$temp_result = array();
        
        	foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;    
		}
        return $temp_result;
    } 
    
    function activity_pj($lc, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      	
        $query = $this->db->query("SELECT PROJECT_ACTIVITY AS ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION, mc.UNIT1 AS UNIT1, mc.UNIT2 AS UNIT2 
									FROM m_project_detail 
        							LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_detail.PROJECT_ACTIVITY) 
									LEFT JOIN m_progress_map mc ON mc.ACCOUNTCODE = PROJECT_ACTIVITY
									WHERE 
        MASTER_PROJECT_ID ='".$this->db->escape_str($lc)."' AND COMPANY_CODE = '".$this->db->escape_str($company)."' order by PROJECT_ACTIVITY asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function activity_pj_lctn($ac,$pj_subtype){
    
        $query = $this->db->query("SELECT ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION 
FROM m_project_activity_map LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_activity_map.ACCOUNT_CODE) WHERE 
 LOCATION_SUBTYPE ='".$this->db->escape_str($pj_subtype)."' AND STATUS_PENGGUNAAN = 'BK' AND ACCOUNTCODE like '%".$this->db->escape_str($ac)."%' ORDER BY ACCOUNTCODE asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function satuan(){
        $query = $this->db->query("SELECT UNIT_CODE, UNIT_DESC FROM m_satuan");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    function muatan($q){
        $query = $this->db->query("SELECT KODE_MUATAN, NAMA_MUATAN, SATUAN FROM m_jenis_muatan WHERE KODE_MUATAN LIKE '%".$q."%' OR NAMA_MUATAN LIKE '%".$q."%'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
    
    // validasi
    
    function lokasi_validate($lc, $ltc,$company){
        $query = "SELECT LOCATION_CODE FROM m_location where TRIM(LOCATION_TYPE_CODE) = TRIM('".$this->db->escape_str($ltc)."') 
                    AND TRIM(LOCATION_CODE) = TRIM('".$this->db->escape_str($lc)."') AND COMPANY_CODE='".$company."' AND INACTIVE=0";
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        return $numrows;
    }
    
    /* function aktivitas_validate($ac, $ltc){
        $query = "SELECT ACCOUNT_CODE FROM m_activity_map where LOCATION_TYPE = '".$this->db->escape_str($ltc).
        "' AND ACCOUNT_CODE = '".$this->db->escape_str($ac)."' AND STATUS_PENGGUNAAN = 'BK'";
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        return $numrows;
    } */
	
	/* ######### UPDATE 22 okct 2012 */
	function aktivitas_validate($ac, $lc, $ltc, $company){
		
		if($ltc == "IF"){
			//echo $lc;
			$query = "SELECT mp.INFRAS_ACTIVITY_CODE 
						FROM m_infrastructure ifs
					LEFT JOIN m_infrastructure_type tp ON tp.IFTYPE = ifs.IFTYPE
					LEFT JOIN m_activity_infras_map mp ON mp.INFRAS_PJ_SUBTYPE = ifs.IFTYPE
					LEFT JOIN m_coa c ON c.ACCOUNTCODE = mp.ACCOUNTCODE
					WHERE IFCODE = '".$lc."' AND COMPANY_CODE = '".$company."' 
					AND mp.ACCOUNTCODE = '".$ac."' AND ISSUBTYPE = 0";	
		} else { 
			if($lc != "" && $company != ""){
				$cek = $this->getBlockPathMekanis($lc, $company);
				if($cek=0 || $cek='0' || $cek==null){
					$where = " AND STATUS_PENGGUNAAN = 'BK'";
				} else {
					$where = " AND STATUS_PENGGUNAAN IN ('BK','BKP')";
				}
			} else {
				$where = " AND STATUS_PENGGUNAAN = 'BK'";
			}
			$query = "SELECT ACCOUNT_CODE FROM m_activity_map 
						WHERE LOCATION_TYPE = '".$this->db->escape_str($ltc)."' 
						AND ACCOUNT_CODE = '".$this->db->escape_str($ac)."'".$where."";
		}
		/* SELECT mp.INFRAS_ACTIVITY_CODE, c.`COA_DESCRIPTION` FROM m_infrastructure ifs
LEFT JOIN m_infrastructure_type tp ON tp.IFTYPE = ifs.IFTYPE
LEFT JOIN m_activity_infras_map mp ON mp.INFRAS_PJ_SUBTYPE = ifs.`IFTYPE`
LEFT JOIN m_coa c ON c.`ACCOUNTCODE` = mp.`ACCOUNTCODE`
WHERE IFCODE = 'JCOC027-OC026' */
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        return $numrows;
    }
	
	function getBlockPathMekanis($block, $company){
		$query = "SELECT BLOCK_PATH FROM m_block_pathmekanis where BLOCK_PATH LIKE '".substr($this->db->escape_str($block),0,5).
        "%' AND COMPANY_CODE = '".$company."'";
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        return $numrows;
	}
    
	function vehicle_validate($vh,$company){
        $query = "SELECT VEHICLECODE FROM m_vehicle where VEHICLECODE = '".$vh."' AND COMPANY_CODE='".$company."' AND INACTIVE=0";
        $sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
        return $numrows;
    }
	
    // validasi project
    function lokasi_project_validate($lc, $company){
        $query = $this->db->query("SELECT PROJECT_ID FROM m_project where TRIM(PROJECT_ID) = TRIM('".$this->db->escape_str($lc)."') 
            AND COMPANY_CODE = '".$this->db->escape_str($company)."' AND PROJECT_STATUS = 1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    function project_activity_validate($pj, $ac, $company){
        $query = $this->db->query("SELECT PROJECT_ACTIVITY FROM m_project_detail WHERE MASTER_PROJECT_ID = '".$this->db->escape_str($pj).
            "' AND PROJECT_ACTIVITY = '".$this->db->escape_str($ac)."' AND COMPANY_CODE = '".$this->db->escape_str($company)."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    // validasi project tanam / land preparation
     
    function projectlctn_activity_validate($pj_subtype, $ac){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_project_activity_map WHERE LOCATION_SUBTYPE = '".$this->db->escape_str($pj_subtype).
        "' AND ACCOUNT_CODE = '".$this->db->escape_str($ac)."' AND STATUS_PENGGUNAAN = 'BK'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
    // get km/hm latest
    function get_latest_kmhm($company,$kode,$tgl,$type,$location,$act,$opt)
    {
        $company=$this->db->escape_str($company);
        $kode=$this->db->escape_str($kode);
        $tgl=$this->db->escape_str($tgl);
        $type=$this->db->escape_str($type);
        //$location=$this->db->escape_str($location);
        $act=$this->db->escape_str($act);
        $opt=$this->db->escape_str($opt);
        
        $query='';
        if(!empty($opt) && $opt==1)
        {
            $tahun=substr($tgl,0,4);
            $bulan=substr($tgl,4,2);
            $query="SELECT MAX(KMHM_KEMBALI) AS KMHM_KEMBALI FROM p_vehicle_activity 
                WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                AND BULAN = '".$bulan."' AND TAHUN='".$tahun."' AND DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d')='".$tgl."'
                AND LOCATION_TYPE_CODE='".$type."'  AND LOCATION_CODE='".$location."'  AND ACTIVITY_CODE='".$act."' " ; 
				 
        }
        else if(!empty($opt) && $opt==2)
        {
            $tgl=date("Ym",strtotime('-1 month'));
            $tahun=substr($tgl,0,4);
            $bulan=substr($tgl,4,2);
            
            $query="SELECT * FROM p_vehicle_activity 
                    WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                    AND BULAN = '".$bulan."' AND TAHUN='".$tahun."' 
                    AND TGL_AKTIVITAS = 
                        (
                            SELECT MAX(TGL_AKTIVITAS) FROM p_vehicle_activity p WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                            AND BULAN = '".$bulan."' AND TAHUN='".$tahun."'
                        )";                  
        }else{
			$tahun=substr($tgl,0,4);
            $bulan=substr($tgl,4,2);
            $query="SELECT MAX(KMHM_KEMBALI) AS KMHM_KEMBALI FROM p_vehicle_activity 
                WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                AND BULAN = '".$bulan."' AND TAHUN='".$tahun."' AND DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d')='".$tgl."'
                AND LOCATION_TYPE_CODE='".$type."'  AND LOCATION_CODE='".$location."'  AND ACTIVITY_CODE='".$act."' " ;
		}
		//echo $query."---"; 
        $sQuery=$this->db->query($query); 
        
        $temp_result = array();
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result; 
    }
    
    function get_total_jamkerja($company,$kode,$tgl,$type,$location,$act,$id)
    {
        $company=$this->db->escape_str($company);
        $kode=$this->db->escape_str($kode);
        $tgl=$this->db->escape_str($tgl);
        $type=$this->db->escape_str($type);
        $location=$this->db->escape_str($location);
        $act=$this->db->escape_str($act);
        $id=$this->db->escape_str($id);

        $tahun=substr($tgl,0,4);
        $bulan=substr($tgl,4,2);
        $query='';
        if(empty($id) || $id==''){
            $query="SELECT SUM(JAM_KERJA) AS JAM_KERJA FROM p_vehicle_activity 
                WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                AND BULAN = '".$bulan."' AND TAHUN='".$tahun."' AND DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d')='".$tgl."'
                AND LOCATION_TYPE_CODE='".$type."'  AND LOCATION_CODE='".$location."'  AND ACTIVITY_CODE='".$act."' " ;    
        }else{
            $query="SELECT SUM(JAM_KERJA) AS JAM_KERJA FROM p_vehicle_activity 
                WHERE COMPANY_CODE='".$company."' AND KODE_KENDARAAN='".$kode."'
                AND BULAN = '".$bulan."' AND TAHUN='".$tahun."' AND DATE_FORMAT(TGL_AKTIVITAS,'%Y%m%d')='".$tgl."'
                AND LOCATION_TYPE_CODE='".$type."'  AND LOCATION_CODE='".$location."'  AND ACTIVITY_CODE='".$act."' AND ID !='".$id."'" ;    
        }
           

        $sQuery=$this->db->query($query); 
        
        $temp_result = array();
        foreach ( $sQuery->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;    
    }
    
    //########## UPDATE 15 Des 2010
    function gen_kendaraan($vc,$bln,$thn ,$company)
    {
        $company=$this->db->escape_str($company);
        $vc=$this->db->escape_str($vc);
        $bln=$this->db->escape_str($bln);
        $thn=$this->db->escape_str($thn);
        
       $query = "SELECT pv.KODE_KENDARAAN,vh.DESCRIPTION AS NAMA_KENDARAAN, 
					TGL_AKTIVITAS, JAM_KERJA, KMHM_BERANGKAT, KMHM_KEMBALI, KMHM_JUMLAH, 
					pv.LOCATION_TYPE_CODE, pv.LOCATION_CODE,
					CASE WHEN pv.LOCATION_TYPE_CODE = 'PJ' THEN 
						CONCAT( pj.PROJECT_DESC,'-',pj.PROJECT_LOCATION )
					ELSE
						loc.DESCRIPTION
					END AS LOKASI,ACTIVITY_CODE,m_coa.COA_DESCRIPTION,
                    MUATAN_JENIS,MUATAN_VOL,MUATAN_SAT,PRESTASI_VOL,PRESTASI_SAT,PRESTASI_VOL2,PRESTASI_SAT2
                    FROM p_vehicle_activity pv
                    LEFT JOIN m_vehicle vh ON vh.VEHICLECODE= pv.KODE_KENDARAAN 
										 AND vh.COMPANY_CODE=pv.COMPANY_CODE
                    LEFT JOIN m_location loc ON loc.LOCATION_CODE = pv.LOCATION_CODE 
                        				 AND loc.LOCATION_TYPE_CODE = pv.LOCATION_TYPE_CODE
                        				 AND loc.COMPANY_CODE = pv.COMPANY_CODE
                    LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = pv.ACTIVITY_CODE
                    LEFT JOIN m_project pj ON pj.PROJECT_ID = pv.LOCATION_CODE and pj.COMPANY_CODE = pv.COMPANY_CODE
					WHERE KODE_KENDARAAN='".$vc."' AND BULAN='".$bln."' AND TAHUN='".$thn."' 
					AND pv.COMPANY_CODE='".$company."'";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if($rowcount > 0){
            foreach ( $sQuery->result_array() as $row ){
                $temp_result [] = $row;
            }
        }
        return $temp_result; 
    }
	
	/* fungsi penambahan sub aktifitas */
	function getProjectDetail($projectNo){
		$q = $this->db->query("SELECT TRIM(PROJECT_SUB_ACTIVITY) AS ret FROM m_project 
								WHERE PROJECT_ID = '".$projectNo."'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function getSubActivty($pjsubtype="",$accountcode){
		$query = $this->db->query("SELECT im.INFRAS_ACTIVITY_CODE, UPPER(mi.INFRAS_ACTIVITY_NAME) INFRAS_ACTIVITY_NAME 
					FROM m_activity_infras_map im
					LEFT JOIN m_activity_infras mi ON mi.INFRAS_ACTIVITY_CODE = im.INFRAS_ACTIVITY_CODE 
					WHERE INFRAS_PJ_SUBTYPE = '".$pjsubtype."' OR ACCOUNTCODE = '".$accountcode."' 
					AND ISSUBTYPE = 1", FALSE);
		$temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function cekIsSubAct($ac){
		$sQuery = $this->db->query("SELECT ACCOUNTCODE FROM m_activity_infras_map WHERE ISSUBTYPE = 1
								AND ACCOUNTCODE = ".$ac."", FALSE);
		//$sQuery=$this->db->query($query);
        $numrows=$sQuery->num_rows();
		return $numrows;
	}
	
	function subactivity_validate($pjsubtype="",$accountcode, $subact=""){
		$sQuery = $this->db->query("SELECT im.INFRAS_ACTIVITY_CODE, UPPER(mi.INFRAS_ACTIVITY_NAME) INFRAS_ACTIVITY_NAME 
					FROM m_activity_infras_map im
					LEFT JOIN m_activity_infras mi ON mi.INFRAS_ACTIVITY_CODE = im.INFRAS_ACTIVITY_CODE 
					WHERE INFRAS_PJ_SUBTYPE = '".$pjsubtype."' AND im.INFRAS_ACTIVITY_CODE = '".$subact."'
					AND ACCOUNTCODE = '".$accountcode."' 
					AND ISSUBTYPE = 1", FALSE);
		$numrows=$sQuery->num_rows();
		return $numrows;
	} 
	/* end fungsi sub aktivitas */
	
	/* start cek closing mingguan */
	/* edited : ridhu 2014-05-28 */
	function cekClosingMingguan($WeekNum,$company){
		$query = $this->db->query("SELECT ISCLOSE FROM m_periode_control WHERE WEEK_NUMBER = '".$WeekNum."' AND COMPANY_CODE = '".$company."' AND MODULE = 'BK'");
		$data = array_shift($query->result_array());
		$temp = $data['ISCLOSE'];
		$this->db->close();
		return $temp; 
	}
	/* end cek closing mingguan */
}
?>