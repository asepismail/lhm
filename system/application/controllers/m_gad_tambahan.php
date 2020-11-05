<?

class m_gad_tambahan extends Controller 
{
    
	function m_gad_tambahan ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_gad_tambahan' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->plugin('to_excel');
	}

    function index()
    {
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		if ($data['login_id'] == TRUE){
			$this->load->view('info_m_gad_tambahan', $data);
		} else {
			redirect('login');
		}
    }    
	
    function create( )
    {
        $data_post['PERIODE'] = $this->input->post( 'PERIODE' );
		$data_post['TUNJANGAN_JABATAN'] = $this->input->post( 'TUNJANGAN_JABATAN' );
		$data_post['POTONGAN_LAIN'] = $this->input->post( 'POTONGAN_LAIN' );
		$data_post['NATURA'] = $this->input->post( 'NATURA' );
		$data_post['POTONGAN_NATURA'] = $this->input->post( 'POTONGAN_NATURA' );
		$data_post['RAPEL'] = $this->input->post( 'RAPEL' );
		$data_post['THR'] = $this->input->post( 'THR' );
		$data_post['BONUS'] = $this->input->post( 'BONUS' );
		$data_post['TUNJANGAN_CUTI'] = $this->input->post( 'TUNJANGAN_CUTI' );
		$data_post['PENSIUN'] = $this->input->post( 'PENSIUN' );
		$data_post['PPH_21'] = $this->input->post( 'PPH_21' );
		$data_post['PAJAK_BLN_LALU'] = $this->input->post( 'PAJAK_BLN_LALU' );
		$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

    	$insert_id = $this->model_m_gad_tambahan->insert_m_gad_tambahan( $data_post );
         
    }

    function edit( $id = '' )
    {
            
		$data_post['PERIODE'] = $this->input->post( 'PERIODE' );
		$data_post['TUNJANGAN_JABATAN'] = $this->input->post( 'TUNJANGAN_JABATAN' );
		$data_post['POTONGAN_LAIN'] = $this->input->post( 'POTONGAN_LAIN' );
		$data_post['NATURA'] = $this->input->post( 'NATURA' );
		$data_post['POTONGAN_NATURA'] = $this->input->post( 'POTONGAN_NATURA' );
		$data_post['RAPEL'] = $this->input->post( 'RAPEL' );
		$data_post['THR'] = $this->input->post( 'THR' );
		$data_post['BONUS'] = $this->input->post( 'BONUS' );
		$data_post['TUNJANGAN_CUTI'] = $this->input->post( 'TUNJANGAN_CUTI' );
		$data_post['PENSIUN'] = $this->input->post( 'PENSIUN' );
		$data_post['PPH_21'] = $this->input->post( 'PPH_21' );
		$data_post['PAJAK_BLN_LALU'] = $this->input->post( 'PAJAK_BLN_LALU' );
		$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

        $id = $this->session->flashdata('id');
	    $insert_id = $this->model_m_gad_tambahan->update_m_gad_tambahan( $id, $data_post );
                     
    }
	
	function read_employee()
    {
		$periode = $this->uri->segment(3);
		$type_karyawan = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gad_tambahan->read_employee($periode, $type_karyawan, $company));
    }
	

}

?>