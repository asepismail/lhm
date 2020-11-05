<?

class bbt_m_trx_kks extends Model 
{
    //private $table_list;
    //private $table_name;
    
    function __construct()
    {
        parent::Model(); 

        $this->load->database();
        //$this->set_table_used();
    }
	
	function LoadData($company)
	{
		$limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');

        $company=$this->db->escape_str($company);
         
        $queries = "SELECT TRANSACTION_ID,trx.MOVEMENTTYPE_ID, mt.MOVEMENTNAME, MOVEMENTDATE, trx.BATCH_ID, mn.NURSERYCODE, mn.DESCRIPTION,";
		$queries .= " trx.SOURCE_TYPE,SOURCE,NO_DOCUMENT,LOCATION_TYPE_CODE,LOCATION_CODE,OPENING_QTY_SSTOCK,SQTY_MOVEMENT,ENDING_QTY_SSTOCK";
		$queries .= " ,OPENING_QTY_DSTOCK,DQTY_MOVEMENT,ENDING_QTY_DSTOCK FROM bbt_p_transaction trx ";
		$queries .= " LEFT JOIN bbt_m_movemementtype mt ON mt.MOVEMENTTYPE_ID = trx.MOVEMENTTYPE_ID";
		$queries .= " LEFT JOIN m_nursery mn ON mn.BATCH_ID = trx.BATCH_ID WHERE trx.COMPANY_CODE = '".$company."'";

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
            array_push($cell, htmlentities($obj->MOVEMENTTYPE_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->MOVEMENTNAME,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->MOVEMENTDATE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BATCH_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NURSERYCODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->DESCRIPTION,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SOURCE_TYPE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->SOURCE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NO_DOCUMENT,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LOCATION_TYPE_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->LOCATION_CODE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->SQTY_MOVEMENT,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->OPENING_QTY_SSTOCK,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->ENDING_QTY_SSTOCK,ENT_QUOTES,'UTF-8'));  
			array_push($cell, htmlentities($obj->DQTY_MOVEMENT,ENT_QUOTES,'UTF-8'));
            //array_push($cell, htmlentities($obj->OPENING_QTY_DSTOCK,ENT_QUOTES,'UTF-8'));
			//array_push($cell, htmlentities($obj->ENDING_QTY_DSTOCK,ENT_QUOTES,'UTF-8'));  
			//array_push($cell, htmlentities($obj->COMPANY_CODE,ENT_QUOTES,'UTF-8'));             
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
	
	function get_mvtype()
	{
		$query = $this->db->query("SELECT MOVEMENTTYPE_ID, MOVEMENTNAME FROM bbt_m_movemementtype");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
	
	function get_batch($company)
	{
		$query = $this->db->query("SELECT stok.BATCH_ID, CONCAT(mn.NURSERYCODE, ' - ', mn.DESCRIPTION) AS DESCRIPTION 
									FROM bbt_p_stok_kks stok
									LEFT JOIN m_nursery mn ON mn.BATCH_ID = stok.BATCH_ID WHERE stok.COMPANY_CODE = '".$company."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}

}

?>
