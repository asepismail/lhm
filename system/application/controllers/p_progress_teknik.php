<?

class p_progress_teknik extends Controller 
{
    
	function p_progress_teknik ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_teknik' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="p_progress_teknik";
        
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
		$view = "info_p_progress_teknik";
		$data = array();
		$data['judul_header'] = "Progress teknik";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
	
		if ($data['login_id'] == TRUE){
				show($view, $data);
		} else {
			redirect('login');
		}
    }    
	
	function read_act()
    {
		$tgl = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_teknik->read_act($tgl,$company));
    }
	
	function create_pb()
    {
		$data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
	   	
		$company = $this->session->userdata('DCOMPANY');
		$prog_date = $this->input->post( 'TGLPROGRESS' ); 
		
  			
				$jumlah = $this->input->post( 'jumlahdt' );
	   				for ($i=1; $i<=$jumlah;$i++) {
						
						$tgl_progress = $prog_date;
						$activity =  $this->input->post( 'ACT'.strval($i) );
						$location = $this->input->post( 'LC'.strval($i) );
						
						$hasil_kerja = ltrim(rtrim($this->input->post( 'NILAI'.strval($i))));
						$tglID = str_replace('-','',$prog_date);
						$begID = $company.$tglID;
						$data_post['ID_PROGRESS'] = $this->global_func->id_GAD('p_progress_teknik','ID_PROGRESS',$begID);
						$idp = $this->input->post( 'IDP'.strval($i) );
						$data_post['TGL_PROGRESS'] = $tgl_progress;
 						$data_post['LOCATION_CODE'] = $location;
						$data_post['ACTIVITY_CODE'] = $activity;
						$data_post['HASIL_KERJA'] = $hasil_kerja;
						$data_post['SATUAN'] = $this->input->post( 'UNIT'.strval($i) );
						
						$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
						$data_post['COMPANY_CODE'] = $company;
						
						$this->db->from('p_progress_teknik');
						$this->db->where('TGL_PROGRESS',$tgl_progress);
						$this->db->where('ACTIVITY_CODE',$activity);
						$this->db->where('LOCATION_CODE',$location);
						$this->db->where('COMPANY_CODE',$company);
						
						if ($this->db->count_all_results() == 0) {
							$insert_id = $this->model_p_progress_teknik->insert_p_progress_teknik( $data_post );	
						}  else if ($this->db->count_all_results() != 0) {
							$insert_id = $this->model_p_progress_teknik->update_p_progress_teknik( $idp, $tgl_progress,$activity,$location,$company, $data_post );
						}
				}			
    	}
		
	//hapus progress
	function delete()
	{
		$company = $this->session->userdata('DCOMPANY');
		$act = $this->input->post( 'ACT' );
		$lc = $this->input->post( 'LC' );
		$idp = $this->input->post( 'IDP' );
		$tgl = $this->input->post( 'TGL' );
		
		$this->model_p_progress_teknik->delete_progress_teknik($idp,$tgl,$act,$lc,$company);
	}
}

?>