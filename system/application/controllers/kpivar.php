<?

class kpivar extends Controller 
{
    
	function kpivar ()
	{
		parent::Controller();	

		$this->load->model( 'model_kpivar' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="kpivar";
		
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
		$view = "info_kpivar";
		$data = array();
		$data['judul_header'] = "ENTRY DATA REALISASI 2011 - KEY PERFORMANCE INDICATOR";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['KPIP_DESC'] = $this->global_func->dropdownlist2("KPIP_DESC","kpi_parameter","KPIP_DESC","KPIP_ID","KPIP_PARENT =''",NULL, NULL,'cek_kpi()',"select",NULL,'KPIP_ID');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    } 
	
	function read_grid_kpi()
    {
		$periode = $this->uri->segment(4);
		$tipe = $this->uri->segment(3);
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_kpivar->get_kpi($company, $periode, $tipe));
    }
	
	function create_kpi()
    {
		$data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
	   	
		$company = $this->session->userdata('DCOMPANY');
		$periode = $this->input->post( 'PERIODE' ); 
		
  			
				$jumlah = $this->input->post( 'jumlahdt' );
	   				for ($i=1; $i<=$jumlah;$i++) {
						
						
						$begID = $company.$periode;
						$data_post['KPIT_ID'] = $this->global_func->id_GAD('kpi_variable','KPIT_ID',$begID);
						$data_post['PERIODE'] = $periode;
						$data_post['KPIV_ID'] = $this->input->post( 'KPIV_ID'.strval($i) );
						$data_post['KPIV_VALUE'] = $this->input->post( 'KPIV_VALUE'.strval($i) );
						$data_post['KETERANGAN'] = $this->input->post( 'KETERANGAN'.strval($i) );
						$data_post['COMPANY_CODE'] = $company;
						
						$this->db->from('kpi_variable');
						$this->db->where('PERIODE',$periode);
						$this->db->where('KPIV_ID',$this->input->post( 'KPIV_ID'.strval($i) ));
						$this->db->where('COMPANY_CODE',$company);
						
						if ($this->db->count_all_results() == 0) {
							$insert_id = $this->model_kpivar->insert_kpi( $data_post );	
						}  else if ($this->db->count_all_results() != 0) {
							
							$insert_id = $this->model_kpivar->update_kpi( $company, $this->input->post( 'KPIV_ID'.strval($i)),$periode, $data_post );
						}
				}			
    	}
		
	//hapus progress
	function delete()
	{
		$company = $this->session->userdata('DCOMPANY');
		$tipe = $this->input->post( 'KPIV_ID');
		$periode = $this->input->post( 'PERIODE' );
				
		$this->model_kpivar->delete_kpi($company,$tipe,$periode);
	}
}

?>
