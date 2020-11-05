<?

class M_user_grole extends Controller 
{
    
	function M_user_grole ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_user_grole' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_user_grole/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_user_grole->info_m_user_grole( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_user_grole.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_user_grole->enroll_m_user_grole( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_user_grole/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['MENU_ID'] = '';
$data['values']['ROLE_ADD'] = '';
$data['values']['ROLE_EDIT'] = '';
$data['values']['ROLE_DELETE'] = '';
$data['values']['ROLE_REPORT'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_user_grole.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'MENU_ID', lang('MENU_ID'), 'required' );
$this->form_validation->set_rules( 'ROLE_ADD', lang('ROLE_ADD'), 'required' );
$this->form_validation->set_rules( 'ROLE_EDIT', lang('ROLE_EDIT'), 'required' );
$this->form_validation->set_rules( 'ROLE_DELETE', lang('ROLE_DELETE'), 'required' );
$this->form_validation->set_rules( 'ROLE_REPORT', lang('ROLE_REPORT'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['MENU_ID'] = set_value( 'MENU_ID' );
$data['values']['ROLE_ADD'] = set_value( 'ROLE_ADD' );
$data['values']['ROLE_EDIT'] = set_value( 'ROLE_EDIT' );
$data['values']['ROLE_DELETE'] = set_value( 'ROLE_DELETE' );
$data['values']['ROLE_REPORT'] = set_value( 'ROLE_REPORT' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_user_grole.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['MENU_ID'] = $this->input->post( 'MENU_ID' );
$data_post['ROLE_ADD'] = $this->input->post( 'ROLE_ADD' );
$data_post['ROLE_EDIT'] = $this->input->post( 'ROLE_EDIT' );
$data_post['ROLE_DELETE'] = $this->input->post( 'ROLE_DELETE' );
$data_post['ROLE_REPORT'] = $this->input->post( 'ROLE_REPORT' );

    
                    $insert_id = $this->model_m_user_grole->insert_m_user_grole( $data_post );
                    
					redirect( 'M_user_grole/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_user_grole->Info_m_user_grole( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_user_grole.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'MENU_ID', lang('MENU_ID'), 'required' );
$this->form_validation->set_rules( 'ROLE_ADD', lang('ROLE_ADD'), 'required' );
$this->form_validation->set_rules( 'ROLE_EDIT', lang('ROLE_EDIT'), 'required' );
$this->form_validation->set_rules( 'ROLE_DELETE', lang('ROLE_DELETE'), 'required' );
$this->form_validation->set_rules( 'ROLE_REPORT', lang('ROLE_REPORT'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['MENU_ID'] = set_value( 'MENU_ID' );
$data['values']['ROLE_ADD'] = set_value( 'ROLE_ADD' );
$data['values']['ROLE_EDIT'] = set_value( 'ROLE_EDIT' );
$data['values']['ROLE_DELETE'] = set_value( 'ROLE_DELETE' );
$data['values']['ROLE_REPORT'] = set_value( 'ROLE_REPORT' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_user_grole.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['MENU_ID'] = $this->input->post( 'MENU_ID' );
$data_post['ROLE_ADD'] = $this->input->post( 'ROLE_ADD' );
$data_post['ROLE_EDIT'] = $this->input->post( 'ROLE_EDIT' );
$data_post['ROLE_DELETE'] = $this->input->post( 'ROLE_DELETE' );
$data_post['ROLE_REPORT'] = $this->input->post( 'ROLE_REPORT' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_user_grole->update_m_user_grole( $id, $data_post );
    
					redirect( 'M_user_grole/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>