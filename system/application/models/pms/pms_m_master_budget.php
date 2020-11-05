<?php
class pms_m_master_budget extends Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }
	
	function loadDataHeader($company, $tahun)
	{
		$limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "";
		
		$sQuery ="SELECT MASTER_BUDGET_ID, MASTER_BUDGET_TYPE, PERIODE, ACTIVITY_CODE,
					c.COA_DESCRIPTION, SUB_ACTIVITY_CODE,
					CASE SUB_ACTIVITY_CODE 
						WHEN 1 THEN 'PEMBUATAN JALAN' 
						WHEN 2 THEN 'PENINGGIAN JALAN'
						WHEN 3 THEN 'PELAPISAN JALAN'
						WHEN 4 THEN 'PERKERASAN JALAN'
						WHEN 5 THEN 'TAHAP I' 
						WHEN 6 THEN 'TAHAP II'
						WHEN 7 THEN 'TAHAP III'
						WHEN 8 THEN 'TAHAP IV'	
					END AS SUB_ACTIVITY_DESC, IS_IF_TYPE, tp.IFTYPE_NAME,
					IS_IF_SUBTYPE, stp.IFSUBTYPE_NAME, SATUAN1, SATUAN2, 
					QTY, ROTASI, RUPIAH, RUPIAH_PER_SATUAN,
					COMPANY_CODE FROM pms_master_budget pm
				LEFT JOIN m_coa c ON c.ACCOUNTCODE = pm.ACTIVITY_CODE
				LEFT JOIN m_infrastructure_type tp ON tp.IFTYPE = pm.IS_IF_TYPE
				LEFT JOIN m_infrastructure_subtype stp ON stp.IFSUBTYPE = pm.IS_IF_SUBTYPE 
						AND stp.IFTYPE = pm.IS_IF_TYPE
				WHERE COMPANY_CODE = '".$company."' AND PERIODE = '".$tahun."' " .$where;  
		
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
			$sQuery .= " ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit."";
		}
						
		$query = $this->db->query($sQuery,FALSE)->result();
		$temp_result=array();
		$rows=array();
		$no = 1;			
		$action = "";
        foreach($query as $obj)
        {

            $cell = array();
			array_push($cell, htmlentities($obj->MASTER_BUDGET_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MASTER_BUDGET_TYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->PERIODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->COA_DESCRIPTION,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SUB_ACTIVITY_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SUB_ACTIVITY_DESC,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IS_IF_TYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities(strtoupper($obj->IFTYPE_NAME),ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->IS_IF_SUBTYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities(strtoupper($obj->IFSUBTYPE_NAME),ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SATUAN1,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SATUAN2,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->QTY,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ROTASI,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RUPIAH,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->RUPIAH_PER_SATUAN,ENT_QUOTES,'UTF-8'));
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
	
	/* end populate grid */
	function read_mcoa($searchp)
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');
        
		$where = "WHERE 1=1"; 
		
		if($searchp!='' && $searchp!='-') { 
			$where.= " AND ACCOUNTCODE LIKE '%".$searchp."%' OR COA_DESCRIPTION LIKE '%".$searchp."%'"; 
		}
		
		$sql2 = "SELECT ACCOUNTCODE, COA_DESCRIPTION FROM m_coa ". $where;
	
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
                    array_push($cell, htmlentities($obj->ACCOUNTCODE,ENT_QUOTES,'UTF-8'));
					array_push($cell, htmlentities($obj->COA_DESCRIPTION,ENT_QUOTES,'UTF-8'));
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
	
	function getCompany(){
		$query = $this->db->query("SELECT COMPANY_CODE, COMPANY_NAME FROM m_company WHERE COMPANY_FLAG = 1");
        $temp = $query->row_array();
        $temp_result = array(); 
        
        foreach ( $query->result_array() as $row ){
            $temp_result [] = $row;
        }

        $this->db->close();
        return $temp_result;	
	}
		
	/* satuan */
	function get_satuan()
	{
		$query = $this->db->query("SELECT UNIT_CODE, UNIT_DESC FROM m_satuan");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	/* afdeling */
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
	/* import */
	function do_import($items){
        //$this->db->query($query);
		$this->db->insert('pms_master_budget',$items);
    }
	
	function getIFCode($iftype){
		$q = $this->db->query("SELECT IFTYPE as ret FROM m_infrastructure_type WHERE IFTYPE_NAME LIKE '%".$iftype."%'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function getSubIFCode($iftype, $ifsubtype){
		$q = $this->db->query("SELECT IFSUBTYPE as ret FROM m_infrastructure_subtype 
					WHERE IFTYPE LIKE '%".$iftype."%' AND IFSUBTYPE_NAME LIKE '%".$ifsubtype."%'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cekExist($company, $periode, $type, $activity, $subact, $iftype, $ifsubtype){
		$q = $this->db->query("SELECT count(*) AS ret FROM pms_master_budget 
					WHERE COMPANY_CODE = '".$company."' 
					AND PERIODE = '".$periode."'
					AND ACTIVITY_CODE = '".$activity."'
					AND SUB_ACTIVITY_CODE = '".$subact."'
					AND IS_IF_TYPE = '".$iftype."'
					AND IS_IF_SUBTYPE = '".$ifsubtype."' ", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function cekDetailBudget($bDetailId){
		$q = $this->db->query("SELECT count(*) AS ret FROM pms_master_budget_detail 
					WHERE MASTER_BUDGET_ID = '".$bDetailId."'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function deleteBudget($bDetailId ){
		$this->db->where('MASTER_BUDGET_ID',$bDetailId );
		$this->db->delete('pms_master_budget');
		return $this->db->affected_rows();
	}
	
	function cekDetailPTA($bDetailId){
		$q = $this->db->query("SELECT count(*) AS ret FROM pms_master_budget_detail 
					WHERE MASTER_BUDGET_ID = '".$bDetailId."' AND DETAIL_TYPE = 'PTA'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function getBalance($BudgetId){
		$q = $this->db->query("SELECT RUPIAH AS ret FROM pms_master_budget 
					WHERE MASTER_BUDGET_ID = '".$BudgetId."'", FALSE);
		$data = array_shift($q->result_array());
		$temp = $data['ret'];
		$this->db->close();
		return $temp;
	}
	
	function insertPTA($data){
		$this->db->insert('pms_master_budget_detail', $data);
		return $this->db->affected_rows();
	}
	
	function updateBudgetHeader($BudgetId, $val){
		$this->db->where( 'MASTER_BUDGET_ID', $BudgetId );   
		$this->db->set( 'RUPIAH', $val );   
        $this->db->update( 'pms_master_budget' );
        return $this->db->affected_rows();   
	}
	
}
?>