<?
class sync_c_block extends Controller 
{
	function sync_c_block ()
	{
		parent::Controller();	
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		//$this->load->model('model_c_user_auth');
		$this->load->model( 'sync_m_block' );
		//$this->load->database();
	}
	
	function testconnpg(){
		//$dsn = 'dbdriver://username:password@hostname/database?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=/path/to/cache';
		$company = $this->uri->segment(3);
		$pgdata = $this->sync_m_block->getblockadem($company);
        
        foreach($pgdata as $row)
        {
			echo $row['block'] . "|" . $row['blocktanam'] . "|" . $row['tahuntanam'] . "|" . $row['plantedarea'] . "|" . $row['inisial'] . "|" . "<br/>";
		}
	}
}

?>