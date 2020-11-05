<?php
class pms_m_daftpengajuan extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	/* company */
	function get_company()
	{
		$query = $this->db->query("SELECT COMPANY_CODE,COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
		$temp_result = array();
		foreach ( $query->result_array() as $row ){
			$temp_result [] = $row;
		}	
		return $temp_result;  
	}
	
	function read_ppj($company, $dept)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$wherecomp = "";
		$wheredept = "";
		if($company != "PAG"){
			$wherecomp = " AND ppj.COMPANY_CODE = '". $company . "' ";
		}
		
		if($dept == 'TEK' || $dept == 'TNM' || $dept == 'PKS'){
			$wheredept = " AND ppj.PROJECT_DEPT = '". $dept . "' ";
		}
		
		$sql2 = "SELECT ppjd.PROJECT_PROP_ID, PROJECT_PROP_AFD,PROJECT_PROP_TYPE, ppj.PROJECT_PROPNUM_NUMID,PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA, PROP_TYPE, ";
		$sql2 .= " ppjd.PROJECT_ID, PROJECT_PROP_SUBTYPE,PROJECT_PROP_START,PROJECT_PROP_END,PROJECT_PROP_LOCATION,PROJECT_PROP_DESC,PROJECT_PROP_ACTIVITY,PROJECT_PROP_SUBACTIVITY,PROJECT_PROP_QTY,PROJECT_DEPT,PROJECT_PROP_IFTYPE,";
		$sql2 .= " PROJECT_PROP_UOM, PROJECT_PROP_VALUE,PROJECT_PROP_TVALUE, ppj.COMPANY_CODE, ISDETAIL, ppj.ISAPPR_ADM, ppj.ISAPPR_ADM_DATE,
					 ppj.ISAPPR_LVL1, ISAPPR_LVL1_DATE,";
		$sql2 .= " ppj.ISAPPR_LVL0, ISAPPR_LVL0_DATE, ppj.ISAPPR_LVL2, ISAPPR_LVL2_DATE, ppj.ISREVISED,ppj.ISCLOSED,ppj.PROP_STATUS FROM pms_project_propnum ppj";
		$sql2 .= " LEFT JOIN pms_project_proposal ppjd ON ppjd.PROJECT_PROPNUM_NUMID = ppj.PROJECT_PROPNUM_NUMID ";
		//$sql2 .= " LEFT JOIN m_infrastructure_type tp ON tp.IFTYPE = ppjd.PROJECT_PROP_SUBTYPE ";
		$sql2 .= " AND ppjd.ISCANCEL = 0 WHERE ppj.ISCOMPLETE = 1".$wherecomp. $wheredept." ";
		
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
        foreach($objects as $obj)
        {
            $cell = array();
					array_push($cell, htmlentities($obj->PROJECT_PROP_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PROJECT_PROPNUM_NUMID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_DATE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_PELAKSANA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_AFD,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_TYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_SUBTYPE,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_LOCATION,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_ACTIVITY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_SUBACTIVITY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_DESC,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_START,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_END,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_QTY,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_UOM,ENT_QUOTES,'UTF-8'));
                    array_push($cell, htmlentities($obj->PROJECT_PROP_VALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_TVALUE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISDETAIL,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_ADM,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_ADM_DATE,ENT_QUOTES,'UTF-8'));
					
					array_push($cell, htmlentities($obj->ISAPPR_LVL0,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL0_DATE,ENT_QUOTES,'UTF-8'));
					
					array_push($cell, htmlentities($obj->ISAPPR_LVL1,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL1_DATE,ENT_QUOTES,'UTF-8'));
					
					array_push($cell, htmlentities($obj->ISAPPR_LVL2,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISAPPR_LVL2_DATE,ENT_QUOTES,'UTF-8'));
					
                    array_push($cell, htmlentities($obj->ISREVISED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISCLOSED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROP_STATUS,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_DEPT,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROP_IFTYPE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROP_TYPE,ENT_QUOTES,'UTF-8'));
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
	
	/* get dokumen pendukung */
	function read_pendukung($project_id){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$sql2 = "SELECT pcd.ID_KONFIRMASI_DATA, pcd.ID_KONFIRMASI, pc.PROJECT_ID, pc.TGL_KONFIRMASI,";
		$sql2 .= " pcd.DESKRIPSI, JNS_DATA, pcd.ISVALID FROM pms_ppj_confirmation_data pcd";
		$sql2 .= " LEFT JOIN pms_ppj_confirmation pc ON pc.ID_KONFIRMASI = pcd.ID_KONFIRMASI";
		$sql2 .= " WHERE pc.PROJECT_ID = '".$project_id."'";
		
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
					array_push($cell, htmlentities($obj->ID_KONFIRMASI_DATA,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ID_KONFIRMASI,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->TGL_KONFIRMASI,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->JNS_DATA,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->DESKRIPSI,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->ISVALID,ENT_QUOTES,'UTF-8'));
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
	
	/* fungsi flow approval */
	function read_flow_appr($project_id){
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
		
		$sql2 = "SELECT PNOTES_ID,PROJECT_ID,PROJECT_PROPNUM_NUMID,NOTES,CREATED,CREATED_DATE FROM pms_project_propnotes WHERE NOTESTYPE = 1
					AND PROJECT_ID = '".$project_id."'";
		
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
					array_push($cell, htmlentities($obj->PNOTES_ID,ENT_QUOTES,'UTF-8'));
			 		array_push($cell, htmlentities($obj->PROJECT_ID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->PROJECT_PROPNUM_NUMID,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->NOTES,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CREATED,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->CREATED_DATE,ENT_QUOTES,'UTF-8'));
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
	/* end flow approval */
		
	/* model untuk fungsi cetak */
	function headerformp_project ($noproject, $company) {//($nopeng, $company) {
		
		$q = "SELECT ppm.PROJECT_PROPNUM_NUMID AS PJ_PNUM ,ppm.PROJECT_PROPNUM_DATE as PDATE,ppm.PROJECT_PROPNUM_PELAKSANA AS PELAKSANA,
				CASE WHEN PROJECT_DEPT = 'TNM' THEN 'Agronomi' WHEN PROJECT_DEPT = 'TEK' THEN 'TEKNIK' WHEN PROJECT_DEPT = 'PKS' THEN 'Pabrik' END 
				AS DEPT";
		$q .= " ,pps.PROJECT_ID AS PROJECT_ID, CONCAT(pps.PROJECT_PROP_DESC,'(',pps.PROJECT_PROP_LOCATION,')') AS PROJECT";
		$q .= " ,ISAPPR_LVL0,ISAPPR_LVL1,ISAPPR_LVL2 FROM pms_project_propnum ppm";
		$q .= " LEFT JOIN pms_project_proposal pps ON pps.PROJECT_PROPNUM_NUMID = ppm.PROJECT_PROPNUM_NUMID";
		$q .= " WHERE pps.PROJECT_ID = '".$noproject."' AND ppm.COMPANY_CODE = '".$company."'";
		$query = $this->db->query($q);
        
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
	}
	
	function formp_project ($noproject) {
	   $q = "SELECT pps.PROJECT_ID AS PJ_ID, CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS') THEN pps.PROJECT_PROP_ACTIVITY  ELSE ppd.DPROJECT_PROP_ACTIVITY END AS AKTIVITAS";
	   $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS')  THEN coa2.COA_DESCRIPTION ELSE coa.COA_DESCRIPTION END AS DESCR";
	   $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS')  THEN pps.PROJECT_PROP_QTY  ELSE ppd.DPROJECT_PROP_QTY END AS QTY";
	   $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS') THEN pps.PROJECT_PROP_UOM  ELSE ppd.DPROJECT_PROP_UOM END AS SAT";
	   $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS') THEN pps.PROJECT_PROP_VALUE ELSE ppd.DPROJECT_PROP_VALUE END AS VALUE";
	   $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS') THEN pps.PROJECT_PROP_TVALUE ELSE ppd.DPROJECT_PROP_TVALUE END AS TOTAL";
	  $q .= " ,CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS') THEN pps.PROJECT_PROP_LOCATION ELSE pps.PROJECT_PROP_LOCATION END AS LOKASI";
	   $q .= " FROM pms_project_proposal pps";
	   $q .= " LEFT JOIN pms_project_proposaldetail ppd ON ppd.DPROJECT_ID = pps.PROJECT_ID";
	   $q .= " LEFT JOIN m_coa coa ON coa.ACCOUNTCODE = ppd.DPROJECT_PROP_ACTIVITY";
	   $q .= " LEFT JOIN m_coa coa2 ON coa2.ACCOUNTCODE = pps.PROJECT_PROP_ACTIVITY";
	   $q .= " WHERE pps.PROJECT_ID = '".$noproject."' ORDER BY ppd.DPROJECT_PROP_ACTIVITY";
		
		$query = $this->db->query($q);
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
	}
	
	function formp_sumproject ($noproject) {
		/* $q = "SELECT pps.PROJECT_ID AS PJ_ID, CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS')  THEN 
					SUM(pps.PROJECT_PROP_TVALUE)
				ELSE SUM(ppd.DPROJECT_PROP_TVALUE) END AS TOTAL FROM pms_project_proposal pps";
		$q .= " LEFT JOIN pms_project_proposaldetail ppd ON ppd.DPROJECT_ID = pps.PROJECT_ID";
		$q .= " WHERE pps.PROJECT_ID = '".$noproject."' GROUP BY pps.PROJECT_ID ORDER BY ppd.DPROJECT_PROP_ACTIVITY"; */
		
		$q = "SELECT PJ_ID, CASE WHEN FLAG = 'F' THEN TVAL ELSE PROPVAL END AS TOTAL FROM (
SELECT pps.PROJECT_ID AS PJ_ID, SUM(pps.PROJECT_PROP_TVALUE) AS TVAL, SUM(COALESCE(ppd.DPROJECT_PROP_TVALUE,0)) AS PROPVAL, 
	CASE WHEN PROJECT_PROP_TYPE IN ('IF','PKS')  THEN  
					'F'
				ELSE 'T' END AS FLAG FROM pms_project_proposal pps
		LEFT JOIN pms_project_proposaldetail ppd ON ppd.DPROJECT_ID = pps.PROJECT_ID
		WHERE pps.PROJECT_ID = '".$noproject."' GROUP BY pps.PROJECT_ID ORDER BY ppd.DPROJECT_PROP_ACTIVITY
) c ";
		
		$query = $this->db->query($q);
        $temp = $query->row_array();
        $temp_result = array(); 
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        $this->db->close();
        return $temp_result;
	}
	
	function formp_getattachment ($noproject) {

		$q = "SELECT pc.PROJECT_ID, pcd.JNS_DATA, pcd.DESKRIPSI FROM pms_ppj_confirmation pc";
		$q .= " LEFT JOIN pms_ppj_confirmation_data pcd ON pcd.ID_KONFIRMASI = pc.ID_KONFIRMASI";
		$q .= " WHERE pc.PROJECT_ID = '".$noproject."' GROUP BY pc.PROJECT_ID,pcd.JNS_DATA ";
		
		$query = $this->db->query($q);
        $temp = $query->row_array();
        $temp_result = array(); 
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        $this->db->close();
        return $temp_result;
	}
	
	
	function formp_projectact ($noproject) {
		$q = "SELECT PJ_ID, ACTIVITY, ph.RUPIAH_PER_SATUAN, s.COMPANY_CODE FROM (SELECT pps.PROJECT_ID AS PJ_ID, 
			CASE WHEN PROJECT_PROP_TYPE = 'OP' THEN 
				CASE WHEN PROJECT_PROP_SUBTYPE LIKE '%TANAM KELAPA SAWIT%' THEN 
				'8401000' 
				WHEN PROJECT_PROP_SUBTYPE LIKE '%LAND PREPARATION%' THEN
				'8200000'
				WHEN PROJECT_PROP_SUBTYPE LIKE '%BIBITAN%' THEN
				'8301000' END 
			WHEN PROJECT_PROP_TYPE = 'IF' THEN
				CASE WHEN PROJECT_PROP_SUBTYPE LIKE '%JA%' THEN 
				'8111000' 
				WHEN PROJECT_PROP_SUBTYPE LIKE '%PR%' THEN
				'8121000'
				WHEN PROJECT_PROP_SUBTYPE LIKE '%BA%' THEN
				'8131000'
				WHEN PROJECT_PROP_SUBTYPE LIKE '%JB%' THEN
				'8141000'
				WHEN PROJECT_PROP_SUBTYPE IN ('BP','BS','BN') THEN
				'8151000'
				WHEN PROJECT_PROP_SUBTYPE LIKE '%BU%' THEN
				'8161000' END 
			END AS ACTIVITY, COMPANY_CODE FROM pms_project_proposal pps
			LEFT JOIN pms_project_proposaldetail ppd ON ppd.DPROJECT_ID = pps.PROJECT_ID
			LEFT JOIN pms_project_propnum ppn ON ppn.PROJECT_PROPNUM_NUMID = pps.PROJECT_PROPNUM_NUMID 
			WHERE pps.PROJECT_ID = '".$noproject."' GROUP BY pps.PROJECT_ID ORDER BY ppd.DPROJECT_PROP_ACTIVITY ) s
		LEFT JOIN pms_master_budget_header ph ON ph.ACTIVITY_CODE = s.ACTIVITY AND ph.COMPANY_CODE = s.COMPANY_CODE";
		
		$query = $this->db->query($q);
        
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row )
        {
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;
	}
	
	function cekPPJ($company, $ppj){
        $query = $this->db->query("SELECT COUNT(PROJECT_PROPNUM_NUMID) AS jumlah, PROJECT_PROPNUM_NUMID, 
									PROJECT_PROPNUM_DATE,PROJECT_PROPNUM_PELAKSANA,PROJECT_DEPT,PROJECT_FINISH_TARGET			
									FROM pms_project_propnum ppjh 
									WHERE COMPANY_CODE = '".$company."' AND PPID = '".$ppj."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function cek_konfirmasi($projectid, $tanggal){
		$query = $this->db->query("SELECT COUNT(PROJECT_ID) AS jumlah FROM pms_ppj_confirmation 
												WHERE PROJECT_ID='".$projectid."' AND TGL_KONFIRMASI='".$tanggal."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function insert_konfimasi($data){
		$this->db->insert( 'pms_ppj_confirmation', $data );
		$id = $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
            return $id;
        return FALSE;
	}
	
	function update_konfimasi($projectid, $tanggal, $data){
		$this->db->where( 'PROJECT_ID', $projectid );
		$this->db->where( 'TGL_KONFIRMASI', $tanggal );
		$id = $this->db->update( 'pms_ppj_confirmation', $data);  
		if ($this->db->affected_rows() > 0)
			$query = $this->db->query("SELECT ID_KONFIRMASI FROM pms_ppj_confirmation 
								WHERE PROJECT_ID='".$projectid."' AND TGL_KONFIRMASI='".$tanggal."'");
			$temp_result = array();
			foreach ( $query->result_array() as $row ){
				$temp_result [] = $row['ID_KONFIRMASI'];
			}
            return $temp_result [0];
        return FALSE;
	}
	
	function cek_detail_konfirmasi($idkonf, $jnsdata){
		$query = $this->db->query("SELECT COUNT(ID_KONFIRMASI) AS jumlah FROM pms_ppj_confirmation_data 
									WHERE ID_KONFIRMASI='".$idkonf."' AND JNS_DATA='".$jnsdata."'");
        $temp_result = array();
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }
        return $temp_result;
	}
	
	function insert_detail_konfimasi($data){
		$this->db->insert( 'pms_ppj_confirmation_data', $data );
		$id = $this->db->insert_id();
		if ($this->db->affected_rows() > 0)
            return $id;
        return FALSE;
	}
	
	function update_detail_konfimasi($idkonf, $jnsdata, $data){
		$this->db->where( 'ID_KONFIRMASI', $idkonf );
		$this->db->where( 'JNS_DATA', $jnsdata );
		$id = $this->db->update( 'pms_ppj_confirmation_data', $data);  
		if ($this->db->affected_rows() > 0)
			$query = $this->db->query("SELECT ID_KONFIRMASI FROM pms_ppj_confirmation_data 
									WHERE ID_KONFIRMASI='".$idkonf."' AND JNS_DATA='".$jnsdata."'
									GROUP BY ID_KONFIRMASI");
			$temp_result = array();
			foreach ( $query->result_array() as $row ){
				$temp_result [] = $row['ID_KONFIRMASI'];
			}
            return $temp_result [0];
        return FALSE;
	}
	
	function delete_detail_konfimasi($id){
		$this->db->where( 'ID_KONFIRMASI_DATA', $id );  	
		$this->db->delete('pms_ppj_confirmation_data');  
		if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
	}
	
	function update_after_konfirmasi($ppj){
		$this->db->set('ISAPPR_LVL0', 1); 
		$this->db->set('ISAPPR_LVL0_DATE', date ("Y-m-d H:i:s"));
		$this->db->set('PROP_STATUS', 'konfirmasi kirim data');
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	function insert_approval_kebun($data){
		$this->db->insert( 'pms_ppj_approval', $data );
		return $this->db->insert_id();
	}
	
	function update_after_approval_kebun($ppj){
		$this->db->set('ISAPPR_ADM', 1);
		$this->db->set('ISAPPR_ADM_DATE', date ("Y-m-d H:i:s"));
		$this->db->set('PROP_STATUS', 'persetujuan Administratur');
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	function insert_approval0($data){
		$this->db->insert( 'pms_ppj_approval', $data );
		return $this->db->insert_id();
	}
	
	function update_after_approval0($ppj){
		$this->db->set('ISAPPR_LVL0', 1);
		$this->db->set('ISAPPR_LVL0_DATE', date ("Y-m-d H:i:s"));
		$this->db->set('PROP_STATUS', 'persetujuan Direktur Area');
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	function insert_approval1($data){
		$this->db->insert( 'pms_ppj_approval', $data );
		return $this->db->insert_id();
	}
	
	function update_after_approval1($ppj){
		$this->db->set('ISAPPR_LVL1', 1);
		$this->db->set('ISAPPR_LVL1_DATE', date ("Y-m-d H:i:s"));
		$this->db->set('PROP_STATUS', 'persetujuan dept head');
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
	
	function insert_approval2($data){
		$this->db->insert( 'pms_ppj_approval', $data );
		return $this->db->insert_id();
	}
	
	function update_after_approval2($ppj){
		$this->db->set('ISAPPR_LVL2', 1);
		$this->db->set('ISAPPR_LVL2_DATE', date ("Y-m-d H:i:s"));
		$this->db->set('PROP_STATUS', 'disetujui');
		$this->db->set('ISCLOSED', 1);
		$this->db->where( 'PROJECT_PROPNUM_NUMID', $ppj );  
		$this->db->update( 'pms_project_propnum');  
		return $this->db->insert_id();
	}
}
?>