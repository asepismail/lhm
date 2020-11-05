<?

class M_location extends Controller 
{
    
	function M_location ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_location' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_location/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_location->info_m_location( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_location.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_location->enroll_m_location( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_location/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['LOCATION_TYPE_CODE'] = '';
$data['values']['DESCRIPTION'] = '';
$data['values']['INACTIVE'] = '';
$data['values']['INACTIVE_DATE'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_location.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'LOCATION_TYPE_CODE', lang('LOCATION_TYPE_CODE'), 'required' );
$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
$this->form_validation->set_rules( 'INACTIVE_DATE', lang('INACTIVE_DATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['LOCATION_TYPE_CODE'] = set_value( 'LOCATION_TYPE_CODE' );
$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
$data['values']['INACTIVE_DATE'] = set_value( 'INACTIVE_DATE' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_location.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
$data_post['INACTIVE_DATE'] = $this->input->post( 'INACTIVE_DATE' );

    
                    $insert_id = $this->model_m_location->insert_m_location( $data_post );
                    
					redirect( 'M_location/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_location->Info_m_location( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_location.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'LOCATION_TYPE_CODE', lang('LOCATION_TYPE_CODE'), 'required' );
$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
$this->form_validation->set_rules( 'INACTIVE_DATE', lang('INACTIVE_DATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['LOCATION_TYPE_CODE'] = set_value( 'LOCATION_TYPE_CODE' );
$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
$data['values']['INACTIVE_DATE'] = set_value( 'INACTIVE_DATE' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_location.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
$data_post['INACTIVE_DATE'] = $this->input->post( 'INACTIVE_DATE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_location->update_m_location( $id, $data_post );
    
					redirect( 'M_location/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>