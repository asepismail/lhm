<?

class P_progress_transport_panen extends Controller 
{
    
	function P_progress_transport_panen ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_transport_panen' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'P_progress_transport_panen/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_p_progress_transport_panen->info_p_progress_transport_panen( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_p_progress_transport_panen.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_p_progress_transport_panen->enroll_p_progress_transport_panen( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'P_progress_transport_panen/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['TGL_PROGRESS'] = '';
$data['values']['ACTIVITY_CODE'] = '';
$data['values']['ACTIVITY_DESC'] = '';
$data['values']['ACTIVITY_LOCATION'] = '';
$data['values']['SATUAN'] = '';
$data['values']['HASIL_KERJA'] = '';
$data['values']['REALISASI'] = '';
$data['values']['HK_PER_SATUAN'] = '';
$data['values']['INPUT_BY'] = '';
$data['values']['INPUT_DATE'] = '';
$data['values']['COMPANY_CODE'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_p_progress_transport_panen.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'TGL_PROGRESS', lang('TGL_PROGRESS'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_CODE', lang('ACTIVITY_CODE'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_DESC', lang('ACTIVITY_DESC'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_LOCATION', lang('ACTIVITY_LOCATION'), 'required' );
$this->form_validation->set_rules( 'SATUAN', lang('SATUAN'), 'required' );
$this->form_validation->set_rules( 'HASIL_KERJA', lang('HASIL_KERJA'), 'required' );
$this->form_validation->set_rules( 'REALISASI', lang('REALISASI'), 'required' );
$this->form_validation->set_rules( 'HK_PER_SATUAN', lang('HK_PER_SATUAN'), 'required' );
$this->form_validation->set_rules( 'INPUT_BY', lang('INPUT_BY'), 'required' );
$this->form_validation->set_rules( 'INPUT_DATE', lang('INPUT_DATE'), 'required' );
$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['TGL_PROGRESS'] = set_value( 'TGL_PROGRESS' );
$data['values']['ACTIVITY_CODE'] = set_value( 'ACTIVITY_CODE' );
$data['values']['ACTIVITY_DESC'] = set_value( 'ACTIVITY_DESC' );
$data['values']['ACTIVITY_LOCATION'] = set_value( 'ACTIVITY_LOCATION' );
$data['values']['SATUAN'] = set_value( 'SATUAN' );
$data['values']['HASIL_KERJA'] = set_value( 'HASIL_KERJA' );
$data['values']['REALISASI'] = set_value( 'REALISASI' );
$data['values']['HK_PER_SATUAN'] = set_value( 'HK_PER_SATUAN' );
$data['values']['INPUT_BY'] = set_value( 'INPUT_BY' );
$data['values']['INPUT_DATE'] = set_value( 'INPUT_DATE' );
$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_p_progress_transport_panen.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['TGL_PROGRESS'] = $this->input->post( 'TGL_PROGRESS' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['ACTIVITY_LOCATION'] = $this->input->post( 'ACTIVITY_LOCATION' );
$data_post['SATUAN'] = $this->input->post( 'SATUAN' );
$data_post['HASIL_KERJA'] = $this->input->post( 'HASIL_KERJA' );
$data_post['REALISASI'] = $this->input->post( 'REALISASI' );
$data_post['HK_PER_SATUAN'] = $this->input->post( 'HK_PER_SATUAN' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

    
                    $insert_id = $this->model_p_progress_transport_panen->insert_p_progress_transport_panen( $data_post );
                    
					redirect( 'P_progress_transport_panen/info/' . $insert_id );
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

    function edit( $id = '' )
    {
        
		$this->load->library('session');
        $this->load->library('form_validation'); 

        switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values'] = $this->model_p_progress_transport_panen->Info_p_progress_transport_panen( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_p_progress_transport_panen.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'TGL_PROGRESS', lang('TGL_PROGRESS'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_CODE', lang('ACTIVITY_CODE'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_DESC', lang('ACTIVITY_DESC'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_LOCATION', lang('ACTIVITY_LOCATION'), 'required' );
$this->form_validation->set_rules( 'SATUAN', lang('SATUAN'), 'required' );
$this->form_validation->set_rules( 'HASIL_KERJA', lang('HASIL_KERJA'), 'required' );
$this->form_validation->set_rules( 'REALISASI', lang('REALISASI'), 'required' );
$this->form_validation->set_rules( 'HK_PER_SATUAN', lang('HK_PER_SATUAN'), 'required' );
$this->form_validation->set_rules( 'INPUT_BY', lang('INPUT_BY'), 'required' );
$this->form_validation->set_rules( 'INPUT_DATE', lang('INPUT_DATE'), 'required' );
$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['TGL_PROGRESS'] = set_value( 'TGL_PROGRESS' );
$data['values']['ACTIVITY_CODE'] = set_value( 'ACTIVITY_CODE' );
$data['values']['ACTIVITY_DESC'] = set_value( 'ACTIVITY_DESC' );
$data['values']['ACTIVITY_LOCATION'] = set_value( 'ACTIVITY_LOCATION' );
$data['values']['SATUAN'] = set_value( 'SATUAN' );
$data['values']['HASIL_KERJA'] = set_value( 'HASIL_KERJA' );
$data['values']['REALISASI'] = set_value( 'REALISASI' );
$data['values']['HK_PER_SATUAN'] = set_value( 'HK_PER_SATUAN' );
$data['values']['INPUT_BY'] = set_value( 'INPUT_BY' );
$data['values']['INPUT_DATE'] = set_value( 'INPUT_DATE' );
$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_p_progress_transport_panen.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['TGL_PROGRESS'] = $this->input->post( 'TGL_PROGRESS' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['ACTIVITY_LOCATION'] = $this->input->post( 'ACTIVITY_LOCATION' );
$data_post['SATUAN'] = $this->input->post( 'SATUAN' );
$data_post['HASIL_KERJA'] = $this->input->post( 'HASIL_KERJA' );
$data_post['REALISASI'] = $this->input->post( 'REALISASI' );
$data_post['HK_PER_SATUAN'] = $this->input->post( 'HK_PER_SATUAN' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_p_progress_transport_panen->update_p_progress_transport_panen( $id, $data_post );
    
					redirect( 'P_progress_transport_panen/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>