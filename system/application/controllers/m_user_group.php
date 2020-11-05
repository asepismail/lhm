<?

class M_user_group extends Controller 
{
    
	function M_user_group ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_user_group' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_user_group/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_user_group->info_m_user_group( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_user_group.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_user_group->enroll_m_user_group( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_user_group/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['USER_GROUP_NAME'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_user_group.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'USER_GROUP_NAME', lang('USER_GROUP_NAME'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['USER_GROUP_NAME'] = set_value( 'USER_GROUP_NAME' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_user_group.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['USER_GROUP_NAME'] = $this->input->post( 'USER_GROUP_NAME' );

    
                    $insert_id = $this->model_m_user_group->insert_m_user_group( $data_post );
                    
					redirect( 'M_user_group/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_user_group->Info_m_user_group( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_user_group.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'USER_GROUP_NAME', lang('USER_GROUP_NAME'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['USER_GROUP_NAME'] = set_value( 'USER_GROUP_NAME' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_user_group.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['USER_GROUP_NAME'] = $this->input->post( 'USER_GROUP_NAME' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_user_group->update_m_user_group( $id, $data_post );
    
					redirect( 'M_user_group/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>