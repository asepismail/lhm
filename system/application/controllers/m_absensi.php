<?

class M_absensi extends Controller 
{
    
	function M_absensi ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_absensi' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_absensi/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_absensi->info_m_absensi( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_absensi.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_absensi->enroll_m_absensi( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_absensi/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['DESKRIPSI'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_absensi.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'DESKRIPSI', lang('DESKRIPSI'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['DESKRIPSI'] = set_value( 'DESKRIPSI' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_absensi.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['DESKRIPSI'] = $this->input->post( 'DESKRIPSI' );

    
                    $insert_id = $this->model_m_absensi->insert_m_absensi( $data_post );
                    
					redirect( 'M_absensi/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_absensi->Info_m_absensi( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_absensi.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'DESKRIPSI', lang('DESKRIPSI'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['DESKRIPSI'] = set_value( 'DESKRIPSI' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_absensi.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['DESKRIPSI'] = $this->input->post( 'DESKRIPSI' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_absensi->update_m_absensi( $id, $data_post );
    
					redirect( 'M_absensi/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>