<?

class bbt_m_stock_kks extends Model 
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
                 
        $queries = "SELECT SKKS_ID, kks.BATCH_ID, mn.NURSERYCODE,BATCH_TYPE,STOCK_TYPE,NUM_TUNGGAL,";
		$queries .= " NUM_DOUBLE,NUM_TOTAL,kks.COMPANY_CODE FROM bbt_p_stok_kks kks";
		$queries .= " LEFT JOIN m_nursery mn ON mn.BATCH_ID = kks.BATCH_ID AND mn.COMPANY_CODE = kks.COMPANY_CODE";
		$queries .= " WHERE kks.COMPANY_CODE = '".$company."'";

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
            array_push($cell, htmlentities($obj->SKKS_ID,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BATCH_ID,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NURSERYCODE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->BATCH_TYPE,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->STOCK_TYPE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NUM_TUNGGAL,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NUM_DOUBLE,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->NUM_TOTAL,ENT_QUOTES,'UTF-8'));
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

}

?>
