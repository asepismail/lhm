<?

class bbt_m_purchase_kks extends Model 
{
    
    function __construct()
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

        $company=$this->db->escape_str($company);
                 
        $queries = "SELECT TRANSACTION_ID,MOVEMENTDATE,bt.BATCH_ID, mn.DESCRIPTION,TRANSACTION_DESC,SOURCE,";
		$queries .= " mn.NURSERYCODE, NO_DOCUMENT,OPENING_QTY_SSTOCK,SQTY_MOVEMENT,ENDING_QTY_SSTOCK,";
		$queries .= " mn.VARIETAS, bt.COMPANY_CODE FROM bbt_p_transaction bt ";
		$queries .= " LEFT JOIN m_nursery mn ON mn.BATCH_ID = bt.BATCH_ID";
		$queries .= " WHERE bt.COMPANY_CODE = '".$company."' AND MOVEMENTTYPE_ID = '11001'";

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
        

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();
		
        $no = 1; 
        foreach($objects as $obj)
        {
			$cell = array();
            array_push($cell, $no); 
            array_push($cell, htmlentities($obj->TRANSACTION_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MOVEMENTDATE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->BATCH_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NURSERYCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->VARIETAS,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->TRANSACTION_DESC,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SOURCE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_DOCUMENT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->OPENING_QTY_SSTOCK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SQTY_MOVEMENT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->ENDING_QTY_SSTOCK,ENT_QUOTES,'UTF-8'));
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
	
	function cek_exist_data($id,$company,$param)
	{
        $id = $this->db->escape_str($id);
        $param = $this->db->escape_str($param);
        $company = $this->db->escape_str($company);
        
		if ($param=="1")
		{
			$sQuery = "SELECT * FROM m_nursery WHERE NURSERYCODE='".$id."' AND COMPANY_CODE='".$company."'";
		}
		elseif ($param=="2")
		{
			$sQuery = "SELECT * FROM m_location WHERE LOCATION_CODE='".$id."' AND LOCATION_TYPE_CODE='NS' 
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
            $this->db->insert('bbt_p_transaction',$data);
            $status=$this->db->insert_id();
			if(isset($status)){
				$status = 1;
			}
        } else{
            $status= 0;
        }
		return $status;
	}
	
	function AddNewNs($data)
	{
        $status='';
        if(isset($data))
        {
            $this->db->insert('m_nursery',$data);
            $status=$this->db->insert_id();
			if(isset($status)){
				$status = 1;
			}
        } else{
            $status= 0;
        }
		return $status;
	}
	
	function AddNewLoc($data)
	{
        $status='';
        if(isset($data))
        {
            $this->db->insert('m_location',$data);
            $status=$this->db->insert_id();
			if(isset($status)){
				$status = 1;
			}
        } else{
            $status= 0;
        }
		return $status;
	}
	
	function AddNewSaldo($data)
	{
        $status='';
        if(isset($data))
        {
            $this->db->insert('bbt_p_stok_kks',$data);
            $status=$this->db->insert_id();
			if(isset($status)){
				$status = 1;
			}
        } else{
            $status= 0;
        }
		return $status;
	}
	
	
	
}

?>
