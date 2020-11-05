<?

class M_employee_type extends Controller 
{
    
	function M_employee_type ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_employee_type' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_employee_type/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_employee_type->info_m_employee_type( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_employee_type.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_employee_type->enroll_m_employee_type( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_employee_type/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['DESCRIPTION'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_employee_type.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_employee_type.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );

    
                    $insert_id = $this->model_m_employee_type->insert_m_employee_type( $data_post );
                    
					redirect( 'M_employee_type/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_employee_type->Info_m_employee_type( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_employee_type.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_employee_type.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_employee_type->update_m_employee_type( $id, $data_post );
    
					redirect( 'M_employee_type/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>