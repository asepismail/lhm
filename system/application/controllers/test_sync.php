<?
class test_sync extends Controller 
{
	function test_sync ()
	{
		parent::Controller();	
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		//$this->load->database();
	}
	
	function testconnpg(){
		//$dsn = 'dbdriver://username:password@hostname/database?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=/path/to/cache';
		
		$dsn = 'postgre://adempiere:adem5224878@10.88.1.64:5432/adempiere?port=5432&db_debug=TRUE';
		$this->load->database($dsn);
		
		$sql = "select * from rv_allocation";
		$query  = $this->db->query($sql);
		$isValidCredential = FALSE;
		if($query->num_rows()>0){
			//$isValidCredential = TRUE;
			foreach($query->result() as $data)
			{
				echo $data->c_allocationhdr_id." | " .$data->datetrx."<br/>";
			}
		}
		
		$this->db->close();
		//echo $sql;
		echo $isValidCredential;
		//return $isValidCredential == TRUE;

		//echo $this->load->database($dsn);
		//echo "aaa";
	}
}

?>