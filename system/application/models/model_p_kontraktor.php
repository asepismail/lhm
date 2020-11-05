<?php
class model_p_kontraktor extends Model
{
    private $table_list;
    private $table_name;
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->database(); 
        $this->set_table_used();  
    }
	
    function set_table_used()
    {   
        $get_table_pkontraktor=array('ID_KONTRAK'=>'varchar-25',
                                        'TGL_KONTRAK'=>'date-',
                                        'ID_KONTRAKTOR'=>'varchar-25',
                                        'LOCATION_TYPE_CODE'=>'varchar-5',
                                        'LOCATION_CODE'=>'varchar-30',
                                        'LOCATION_DESC'=>'varchar-200',
                                        'ACTIVITY_CODE'=>'varchar-30',
                                        'ACTIVITY_DESC'=>'varchar-200',
										'MUATAN'=>'varchar-255',
										'JARAK'=>'varchar-100',
                                        'HSL_SATUAN'=>'varchar-25',
                                        'HSL_VOLUME'=>'decimal-10',
                                        'TARIF_SATUAN'=>'decimal-10',
                                        'NILAI'=>'decimal-10',
                                        'COMPANY_CODE'=>'varchar,4');
                                        
        $insert_table_kontraktor=array(	'ID_KONTRAK'=>'',
										'TGL_KONTRAK'=>'',
										'ID_KONTRAKTOR'=>'',
										'LOCATION_TYPE_CODE'=>'',
										'LOCATION_CODE'=>'',
                                        'LOCATION_DESC'=>'', 
										'ACTIVITY_CODE'=>'',
										'ACTIVITY_DESC'=>'',
										'MUATAN'=>'',
										'JARAK'=>'',
										'HSL_SATUAN'=>'',
                                        'HSL_VOLUME'=>'',
										'TARIF_SATUAN'=>'',
										'NILAI'=>'','COMPANY_CODE'=>'');
        $update_table_kontrak =array('TGL_KONTRAK'=>'',
									'LOCATION_TYPE_CODE'=>'', 
									'LOCATION_CODE'=>'',
									'LOCATION_DESC'=>'',
                                    'ACTIVITY_CODE'=>'',
									'ACTIVITY_DESC'=>'',
									'MUATAN'=>'',
									'JARAK'=>'',
									'HSL_SATUAN'=>'',
									'HSL_VOLUME'=>'',
									'TARIF_SATUAN'=>'',
									'NILAI'=>'');
        
        $this->table_list=array('get_table'=>$get_table_pkontraktor,'insert_table'=>$insert_table_kontraktor,'update_table'=>$update_table_kontrak);
        $this->table_name='p_kontraktor';   
    }
    
    function load_kode_kontraktor($company, $q)
    {
        $query="SELECT KODE_INISIAL AS KODE_KONTRAKTOR, NAMA_KONTRAKTOR, IS_KONTRAKTOR_TBS FROM m_kontraktor WHERE ACTIVE=1 AND COMPANY_CODE ='".$company."' AND NAMA_KONTRAKTOR LIKE '%".$q."%' OR ACTIVE=1 AND COMPANY_CODE ='".$company."' AND KODE_INISIAL LIKE '%".$q."%'  OR ACTIVE=1 AND COMPANY_CODE ='".$company."' AND KODE_KONTRAKTOR LIKE '%".$q."%'";
        $sQuery =$this->db->query($query);
        $num_rows=$sQuery->num_rows();
        $temp_result=array();
		
        if($num_rows>0)
        {
            
            foreach($sQuery->result_array() as $row)
            {
                $temp_result[]=$row;
            }    
        }
        return $temp_result;    
    }
	
	function reverse_kode_kontraktor($company, $q)
    {
        $query="SELECT KODE_KONTRAKTOR FROM m_kontraktor WHERE ACTIVE=1 AND COMPANY_CODE ='".$company."' AND KODE_INISIAL LIKE '%".$q."%'";
        $sQuery =$this->db->query($query);
        $num_rows=$sQuery->num_rows();
        $temp_result=array();
		
        if($num_rows>0)
        {
            foreach($sQuery->result_array() as $row)
            {
                $temp_result[]=$row;
            }    
        }
        return $temp_result;    
    }
    
    function load_data($kode_kontraktor,$periode,$company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        
        $sidx="ID_KONTRAK";
      	$queries ="SELECT ID_KONTRAK,TGL_KONTRAK,NO_KENDARAAN,ID_KONTRAKTOR,LOCATION_TYPE_CODE,LOCATION_CODE,LOCATION_DESC,
        ACTIVITY_CODE,ACTIVITY_DESC,HSL_SATUAN,HSL_VOLUME,HSL_SATUAN2,HSL_VOLUME2,TARIF_SATUAN,NILAI,pk.COMPANY_CODE AS COMPANY_CODE,
        pk.MUATAN AS MUATAN,pk.JARAK AS JARAK
        FROM p_kontraktor pk
        LEFT JOIN m_kontraktor mk ON mk.KODE_KONTRAKTOR = pk.ID_KONTRAKTOR 
        WHERE mk.KODE_INISIAL = '".$kode_kontraktor."' AND pk.COMPANY_CODE='".$company."' 
        AND DATE_FORMAT(TGL_KONTRAK, '%Y%m') = '". $periode ."'";
            
        $sql2 = $queries;
       
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
        if ($start > 0 ){
            $start = $start;
        } else {
            $start = 0;
        }
        
        $sql = $queries." ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";
        //return $sql;
        $objects = $this->db->query($sql,FALSE)->result(); 

        $rows =  array();
               
        $no_ma = 1;
        $action = "";
        foreach($objects as $obj)
        {
            $cell = array();         
            array_push($cell, htmlentities($no_ma,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KONTRAK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TGL_KONTRAK,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NO_KENDARAAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ID_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_DESC,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_DESC,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MUATAN,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->JARAK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->HSL_SATUAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HSL_VOLUME,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->HSL_SATUAN2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->HSL_VOLUME2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TARIF_SATUAN,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->NILAI,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($action,ENT_QUOTES,'UTF-8'));
            $row = new stdClass();
            $row->id = $cell[1];
            $row->cell = $cell;
            array_push($rows, $row);
            
            $no_ma++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject; 
    }
    
		
    function activity($act, $q)
    {
        $act =htmlentities($act,ENT_QUOTES,'UTF-8');
        $q =htmlentities($q,ENT_QUOTES,'UTF-8');
        $query = $this->db->query("select m.`ACCOUNT_CODE` as ACCOUNTCODE, m_coa.`COA_DESCRIPTION` as `COA_DESCRIPTION`,
					mc.UNIT1 AS UNIT1, mc.UNIT2 AS UNIT2 from m_activity_map m 
					LEFT JOIN `m_coa` on (m_coa.`ACCOUNTCODE` = m.`ACCOUNT_CODE`) 
					LEFT JOIN m_progress_map mc ON mc.ACCOUNTCODE = m.ACCOUNT_CODE
					WHERE m.LOCATION_TYPE = '".$act."' AND ACCOUNT_CODE like '".$q."%' AND m.STATUS_PENGGUNAAN = 'BKT' 
					OR m.LOCATION_TYPE = '".$act."' AND m_coa.COA_DESCRIPTION like '%".$q."%' AND m.STATUS_PENGGUNAAN = 'BKT'");
        
        $temp_result = array();
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;    
        }

        return $temp_result;
    }
	
    function location($loc, $q, $company){
        
        $limit = $this->input->post('limit');
      
        $query = $this->db->query("SELECT LOCATION_CODE, DESCRIPTION FROM m_location WHERE LOCATION_TYPE_CODE='".$loc."' AND LOCATION_CODE LIKE '".$q."%' AND COMPANY_CODE = '".$company."' OR LOCATION_TYPE_CODE='".$loc."' AND DESCRIPTION LIKE '%".$q."%' AND COMPANY_CODE = '".$company."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
    
	function location_pj($q, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
        $query = $this->db->query("SELECT PROJECT_ID AS LOCATION_CODE, CONCAT(PROJECT_DESC,' : ',PROJECT_LOCATION) AS DESCRIPTION FROM m_project WHERE PROJECT_ID LIKE '".$this->db->escape_str($q)."%' AND COMPANY_CODE = '".$this->db->escape_str($company)."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
	
	 function activity_pj($lc, $company){
    
        $limit = htmlentities($this->input->post('limit'),ENT_QUOTES,'UTF-8');
      
        $query = $this->db->query("SELECT PROJECT_ACTIVITY AS ACCOUNTCODE, m_coa.COA_DESCRIPTION AS COA_DESCRIPTION             FROM m_project_detail 
        LEFT JOIN m_coa ON (m_coa.ACCOUNTCODE = m_project_detail.PROJECT_ACTIVITY) WHERE 
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
 LOCATION_SUBTYPE ='".$this->db->escape_str($pj_subtype)."' AND STATUS_PENGGUNAAN = 'BKT' AND ACCOUNTCODE like '%".$this->db->escape_str($ac)."%' ORDER BY ACCOUNTCODE asc");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    } 
	
    function insert_new_kontraktor($datapost)
    {
        $result='';
        $clear='';
        if (is_array($datapost))
        {
			$this->db->insert( 'p_kontraktor', $datapost );
			$result= $this->db->insert_id();
        } else{
            $result="input salah";
        }  
        return  $result;
    }
    
    function update_data($datapost,$id_kontrak,$kode_kontrak,$company)
    {
        $result='';
        $clear='false'; //default
        if (is_array($datapost))
        {
            $difKey='update_table';
            $arrDefault =$this->table_list;
            $arrDefault =$arrDefault[$difKey];
            $table='p_kontraktor';
            $this->db->where("ID_KONTRAK", htmlentities($id_kontrak,ENT_QUOTES,'UTF-8')); 
            $this->db->where("ID_KONTRAKTOR", htmlentities($kode_kontrak,ENT_QUOTES,'UTF-8')); 
            $this->db->where("COMPANY_CODE", htmlentities($company,ENT_QUOTES,'UTF-8'));
            $this->db->update($table,$datapost);
        } else{
            $result="input salah";
        }  
        return  $result;       
    }
    
    function delete_data($id,$kode,$company)
    {
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        if ($data['login_id'] == TRUE)
        {
            $table='p_kontraktor';
            $this->db->where( 'ID_KONTRAK', htmlentities($id,ENT_QUOTES,'UTF-8') );
            $this->db->where( 'ID_KONTRAKTOR', htmlentities($kode,ENT_QUOTES,'UTF-8') );      
            $this->db->where( 'COMPANY_CODE', htmlentities($company,ENT_QUOTES,'UTF-8') );      
            $this->db->delete(htmlentities($table,ENT_QUOTES,'UTF-8'));
        } else {
            redirect('login');
        }
        
    }
    
    function get_vehicle($q,$inisial_kontraktor,$company){
        $company=$this->db->escape_str($company);
        $vehicle=$this->db->escape_str($q);
        $inisial_kontraktor = $this->db->escape_str($inisial_kontraktor);
        
        $query="select mk.KODE_KONTRAKTOR, mk.KODE_INISIAL, mkk.NO_KENDARAAN
                from m_kontraktor mk
                left JOIN m_kontraktor_kendaraan mkk
                    on mkk.KODE_KONTRAKTOR = mk.KODE_KONTRAKTOR
                    where mk.KODE_INISIAL = '".$inisial_kontraktor."' AND mkk.NO_KENDARAAN LIKE '%".$vehicle."%' ";
        $sQuery=$this->db->query($query);
        $rowcount=$sQuery->num_rows();
        
        $temp_result = array();
        if(!empty($rowcount)){
            foreach ( $sQuery->result_array() as $row )
            {
                $temp_result[] = $row;
            }
        }
        return $temp_result;   
    }
    
	//validasi
	function aktivitas_validate($ac, $ltc){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_activity_map where LOCATION_TYPE = '".$ltc."' AND ACCOUNT_CODE = '".$ac."' AND STATUS_PENGGUNAAN = 'BKT'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
	
	function projectlctn_activity_validate($pj_subtype, $ac){
        $query = $this->db->query("SELECT ACCOUNT_CODE FROM m_project_activity_map WHERE LOCATION_SUBTYPE = '".$pj_subtype."' AND ACCOUNT_CODE = '".$ac."' AND STATUS_PENGGUNAAN = 'BKT'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
	
	 function lokasi_project_validate($lc, $company){
        $query = $this->db->query("SELECT PROJECT_ID FROM m_project where TRIM(PROJECT_ID) = TRIM('".$lc."') AND COMPANY_CODE = '".$company."' AND PROJECT_STATUS = 1");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
	
	 function project_activity_validate($pj, $ac, $company){
        $query = $this->db->query("SELECT PROJECT_ACTIVITY FROM m_project_detail WHERE MASTER_PROJECT_ID = '".$pj."' AND PROJECT_ACTIVITY = '".$ac."' AND COMPANY_CODE = '".$company."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }
        return $temp_result;
    }
	
	 function lokasi_validate($lc, $ltc, $company){
        $query = $this->db->query("SELECT LOCATION_CODE FROM m_location where TRIM(LOCATION_TYPE_CODE) = TRIM('".$ltc."') AND TRIM(LOCATION_CODE) = TRIM('".$lc."') AND COMPANY_CODE = '".$company."' AND INACTIVE = 0");
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
	
	/* ### material ### */
	
    function get_material ($tgl, $gc, $company) {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        
        $sidx = 'ACTIVITY_CODE';
        $sord = 'ASC';    
        $sql2 = "SELECT gm.BKT_MATERIAL_ID, gm.KODE_KONTRAKTOR, gm.TGL_AKTIVITAS, gm.MATERIAL_SKB_NO, gm.MATERIAL_BPB_NO,
					gm.MATERIAL_CODE, mat.MATERIAL_NAME, mat.MATERIAL_UOM, gm.MATERIAL_QTY, gm.ACTIVITY_CODE, gm.LOCATION_TYPE_CODE,
					gm.LOCATION_CODE, gm.COMPANY_CODE 
					FROM p_kontraktor_material gm
					LEFT JOIN m_material mat ON mat.MATERIAL_CODE = gm.MATERIAL_CODE
					WHERE gm.COMPANY_CODE = '".$company."' 
						AND DATE_FORMAT(gm.TGL_AKTIVITAS,'%Y%m') = '".$tgl."' 
						AND gm.KODE_KONTRAKTOR = '".$gc."'";   
       
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
			$sql .= " ORDER BY ".$sidx." ".$sord."";
		}
        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
                        
        $no = 1;                                      
                                    
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, htmlentities($obj->BKT_MATERIAL_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->KODE_KONTRAKTOR,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TGL_AKTIVITAS,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MATERIAL_SKB_NO,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MATERIAL_BPB_NO,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_NAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MATERIAL_QTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MATERIAL_UOM,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));
                    
            $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
            $no++;
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }
    
    function insert_material ( $data )
    {
        $this->db->insert( 'p_kontraktor_material', $data );
        $this->db->insert_id();
		return $this->db->affected_rows();    
    }
    
    function update_material ( $matid, $gc,$tgl_material,$activity,$location,$company, $data )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8'); 
        $tgl_material=htmlentities($this->db->escape_str($tgl_material),ENT_QUOTES,'UTF-8'); 
        $activity =htmlentities($this->db->escape_str($activity),ENT_QUOTES,'UTF-8'); 
		//$locationtype=htmlentities($this->db->escape_str($locationtype),ENT_QUOTES,'UTF-8'); 
        $location=htmlentities($this->db->escape_str($location),ENT_QUOTES,'UTF-8'); 
        $company =htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
		$this->db->where('BKT_MATERIAL_ID',$matid);
        $this->db->where('KODE_KONTRAKTOR',$gc);
        $this->db->where('TGL_AKTIVITAS',$tgl_material);
        $this->db->where('ACTIVITY_CODE',$activity);
		//$this->db->where('LOCATION_TYPE_CODE',$activity);
        $this->db->where('LOCATION_CODE',$location);
        $this->db->where('COMPANY_CODE',$company);
        $this->db->update( 'p_kontraktor_material', $data ); 
		return $this->db->affected_rows();  
    }
	
    function delete_material( $gc, $idp, $tgl, $act, $mat, $lc, $company )
    {
        $gc =htmlentities($this->db->escape_str($gc),ENT_QUOTES,'UTF-8');
        $idp=htmlentities($this->db->escape_str($idp),ENT_QUOTES,'UTF-8'); 
        $tgl=htmlentities($this->db->escape_str($tgl),ENT_QUOTES,'UTF-8'); 
        $act =htmlentities($this->db->escape_str($act),ENT_QUOTES,'UTF-8'); 
        $lc=htmlentities($this->db->escape_str($lc),ENT_QUOTES,'UTF-8'); 
        $company=htmlentities($this->db->escape_str($company),ENT_QUOTES,'UTF-8'); 
        
        $this->db->where( 'KODE_KONTRAKTOR', $gc ); 
        $this->db->where( 'BKT_MATERIAL_ID', $idp );
		$this->db->where( 'MATERIAL_CODE', $mat ); 
        $this->db->where( 'TGL_AKTIVITAS', $tgl ); 
        $this->db->where( 'ACTIVITY_CODE', $act ); 
        $this->db->where( 'LOCATION_CODE', $lc ); 
        $this->db->where( 'COMPANY_CODE', $company );     
        $this->db->delete('p_kontraktor_material');
		return $this->db->affected_rows();   
    }
	
	function cek_exist_data($gangcode,$date,$mc,$activity,$location,$company)
    {
        $sQuery = "SELECT * FROM p_kontraktor_material 
		WHERE KODE_KONTRAKTOR='".$gangcode."' AND TGL_AKTIVITAS = '".$date."' AND MATERIAL_CODE = '".$mc."' 
		AND ACTIVITY_CODE = '".$activity."' AND LOCATION_CODE = '".$location."' AND COMPANY_CODE ='".$company."'";
        $query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
    }
	
	function mgetActMaterial($gc, $tgl, $act){
		$query = $this->db->query("SELECT DISTINCT ACTIVITY_CODE, m_coa.COA_DESCRIPTION FROM p_kontraktor gad
							LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = gad.ACTIVITY_CODE
							LEFT JOIN m_kontraktor k ON k.KODE_KONTRAKTOR = gad.ID_KONTRAKTOR
WHERE k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y%m%d') = '".$tgl."' AND ACTIVITY_CODE LIKE '".$act."%'
OR k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y%m%d') = '".$tgl."' AND m_coa.COA_DESCRIPTION LIKE '%".$act."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function mgetLocMaterial($gc, $tgl, $act, $loc){
		$query = $this->db->query("SELECT DISTINCT LOCATION_CODE FROM p_kontraktor gad
							LEFT JOIN m_kontraktor k ON k.KODE_KONTRAKTOR = gad.ID_KONTRAKTOR
			WHERE k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y%m%d') = '".$tgl."' 
			AND ACTIVITY_CODE LIKE '".$act."%' 
			AND LOCATION_CODE LIKE '".$loc."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function mgetMaterial($q){
		$query = $this->db->query("SELECT MATERIAL_CODE, MATERIAL_NAME, MATERIAL_UOM FROM m_material WHERE MATERIAL_FLAG = 'LHM'
			AND MATERIAL_CODE LIKE '%".$q."%' OR MATERIAL_FLAG = 'LHM' AND MATERIAL_NAME LIKE '%".$q."%'");
		$temp_result = array();
				
		foreach ( $query->result_array() as $row ) {
			$temp_result [] = $row;
		}	
		return $temp_result;
	}
	
	function valActMaterial($gc, $tgl, $act){
		 $sQuery = "SELECT DISTINCT ACTIVITY_CODE, m_coa.COA_DESCRIPTION FROM p_kontraktor gad
							LEFT JOIN m_coa ON m_coa.ACCOUNTCODE = gad.ACTIVITY_CODE
							LEFT JOIN m_kontraktor k ON k.KODE_KONTRAKTOR = gad.ID_KONTRAKTOR
WHERE k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y-%m-%d') = '".$tgl."' AND ACTIVITY_CODE LIKE '".$act."%'
OR k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y-%m-%d') = '".$tgl."' AND m_coa.COA_DESCRIPTION LIKE '%".$act."%'";
		$query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
	}
	
	function valLocMaterial($gc, $tgl, $act, $loc){
		 $sQuery = "SELECT DISTINCT LOCATION_CODE FROM p_kontraktor gad
							LEFT JOIN m_kontraktor k ON k.KODE_KONTRAKTOR = gad.ID_KONTRAKTOR
							WHERE k.KODE_INISIAL = '".$gc."' AND DATE_FORMAT(TGL_KONTRAK,'%Y-%m-%d') = '".$tgl."' 
							AND ACTIVITY_CODE LIKE '".$act."%' 
							AND LOCATION_CODE LIKE '".$loc."%'";
		$query = $this->db->query($sQuery);
        $count = $query->num_rows();
        return $count;
	}
	/*  ### end material ###  */
}  
?>
