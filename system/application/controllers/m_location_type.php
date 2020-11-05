<?

class M_location_type extends Controller 
{
    
	function M_location_type ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_location_type' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_location_type/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_location_type->info_m_location_type( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_location_type.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_location_type->enroll_m_location_type( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_location_type/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['LOCATION_TYPE_NAME'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_location_type.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'LOCATION_TYPE_NAME', lang('LOCATION_TYPE_NAME'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['LOCATION_TYPE_NAME'] = set_value( 'LOCATION_TYPE_NAME' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_location_type.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['LOCATION_TYPE_NAME'] = $this->input->post( 'LOCATION_TYPE_NAME' );

    
                    $insert_id = $this->model_m_location_type->insert_m_location_type( $data_post );
                    
					redirect( 'M_location_type/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_location_type->Info_m_location_type( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_location_type.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'LOCATION_TYPE_NAME', lang('LOCATION_TYPE_NAME'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['LOCATION_TYPE_NAME'] = set_value( 'LOCATION_TYPE_NAME' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_location_type.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['LOCATION_TYPE_NAME'] = $this->input->post( 'LOCATION_TYPE_NAME' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_location_type->update_m_location_type( $id, $data_post );
    
					redirect( 'M_location_type/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>