<?php
class model_importdata extends Model
{
    function __Construct()
    {
        parent::__Construct();
        $this->load->database();
    }
	
    function do_import($query){
        $this->db->query($query);
    }
	
	function LoadData($company)
    {
        $limit = htmlentities($this->input->post('rows'),ENT_QUOTES,'UTF-8');
        $page = htmlentities($this->input->post('page'),ENT_QUOTES,'UTF-8');
        $sidx = htmlentities($this->input->post('sidx'),ENT_QUOTES,'UTF-8');
        $sord = htmlentities($this->input->post('sord'),ENT_QUOTES,'UTF-8');
        //echo $sidx;
		if($sidx == "Kode Asset"){ $sidx == "kode"; }
		else if($sidx == "Perusahaan") { $sidx == "company_code"; }
		else { $sidx = $sidx; }
		
		
        $company=$this->db->escape_str($company);
        
        $queries = "select transactid, kode,cost,periode,company_code FROM dummy_table WHERE company_code='".$company."'";
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

        $sql = "select transactid, kode,cost,periode,company_code FROM dummy_table WHERE company_code='".$company."' ORDER BY ".$sidx." ".$sord." LIMIT ".$start.",".$limit." ";

        $objects = $this->db->query($sql,FALSE)->result(); 
        $rows =  array();

        $act = "";
        $no = 1;
        foreach($objects as $obj)
        {
            $cell = array();
            array_push($cell, $no);
            array_push($cell, htmlentities($obj->transactid,ENT_QUOTES,'UTF-8'));
            array_push($cell, htmlentities($obj->kode,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->cost,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->periode,ENT_QUOTES,'UTF-8'));
			array_push($cell, htmlentities($obj->company_code,ENT_QUOTES,'UTF-8'));              
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
	
	function get_inisial($company)
	{
		$query = $this->db->query("SELECT COMPANY_INISIAL as INISIAL, COMPANY_NAME FROM m_company WHERE company_code = '".$company."'");
		
		$temp_result = array();
				
		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
			
		}	
		return $temp_result;  
	}
}  
?>
