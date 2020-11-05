<?
if (!defined('BASEPATH')) exit('No direct script access allowed');

class P_contract extends Controller 
{
    
	function P_contract ()
	{
		parent::Controller();	
		/*modul yang di load halaman vehicle activity*/
		$this->load->model( 'model_contract' ); 
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
	}
	
	function index()
    {
		$data = array();
		
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		
		if ($data['login_id'] == TRUE){
			$this->load->view('info_p_contract', $data);
		} else {
			redirect('login');
		}
		

    }    
	
	function grid_contract()
    {
				
		$vc = $this->uri->segment(3);
		$bln = $this->uri->segment(4);
		$thn = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		echo json_encode($this->model_contract->grid_vc($vc, $bln, $thn, $company));
		//echo json_encode($this->model_contract->grid_contract());
    }
	
	
	
	
	
}

?>