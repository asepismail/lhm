<?

class M_gang_activity extends Controller 
{
    
	function M_gang_activity ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_gang_activity' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_gang_activity/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_gang_activity->info_m_gang_activity( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_gang_activity.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_gang_activity->enroll_m_gang_activity( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_gang_activity/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['MANDORE1_CODE'] = '';
$data['values']['MANDORE_CODE'] = '';
$data['values']['KERANI_CODE'] = '';
$data['values']['ITEM_CODE1'] = '';
$data['values']['ITEM_CODE2'] = '';
$data['values']['ITEM_CODE3'] = '';
$data['values']['INPUT_BY'] = '';
$data['values']['INPUT_DATE'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_gang_activity.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'MANDORE1_CODE', lang('MANDORE1_CODE'), 'required' );
$this->form_validation->set_rules( 'MANDORE_CODE', lang('MANDORE_CODE'), 'required' );
$this->form_validation->set_rules( 'KERANI_CODE', lang('KERANI_CODE'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE1', lang('ITEM_CODE1'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE2', lang('ITEM_CODE2'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE3', lang('ITEM_CODE3'), 'required' );
$this->form_validation->set_rules( 'INPUT_BY', lang('INPUT_BY'), 'required' );
$this->form_validation->set_rules( 'INPUT_DATE', lang('INPUT_DATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['MANDORE1_CODE'] = set_value( 'MANDORE1_CODE' );
$data['values']['MANDORE_CODE'] = set_value( 'MANDORE_CODE' );
$data['values']['KERANI_CODE'] = set_value( 'KERANI_CODE' );
$data['values']['ITEM_CODE1'] = set_value( 'ITEM_CODE1' );
$data['values']['ITEM_CODE2'] = set_value( 'ITEM_CODE2' );
$data['values']['ITEM_CODE3'] = set_value( 'ITEM_CODE3' );
$data['values']['INPUT_BY'] = set_value( 'INPUT_BY' );
$data['values']['INPUT_DATE'] = set_value( 'INPUT_DATE' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_gang_activity.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['MANDORE1_CODE'] = $this->input->post( 'MANDORE1_CODE' );
$data_post['MANDORE_CODE'] = $this->input->post( 'MANDORE_CODE' );
$data_post['KERANI_CODE'] = $this->input->post( 'KERANI_CODE' );
$data_post['ITEM_CODE1'] = $this->input->post( 'ITEM_CODE1' );
$data_post['ITEM_CODE2'] = $this->input->post( 'ITEM_CODE2' );
$data_post['ITEM_CODE3'] = $this->input->post( 'ITEM_CODE3' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );

    
                    $insert_id = $this->model_m_gang_activity->insert_m_gang_activity( $data_post );
                    
					redirect( 'M_gang_activity/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_gang_activity->Info_m_gang_activity( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_gang_activity.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'MANDORE1_CODE', lang('MANDORE1_CODE'), 'required' );
$this->form_validation->set_rules( 'MANDORE_CODE', lang('MANDORE_CODE'), 'required' );
$this->form_validation->set_rules( 'KERANI_CODE', lang('KERANI_CODE'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE1', lang('ITEM_CODE1'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE2', lang('ITEM_CODE2'), 'required' );
$this->form_validation->set_rules( 'ITEM_CODE3', lang('ITEM_CODE3'), 'required' );
$this->form_validation->set_rules( 'INPUT_BY', lang('INPUT_BY'), 'required' );
$this->form_validation->set_rules( 'INPUT_DATE', lang('INPUT_DATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['MANDORE1_CODE'] = set_value( 'MANDORE1_CODE' );
$data['values']['MANDORE_CODE'] = set_value( 'MANDORE_CODE' );
$data['values']['KERANI_CODE'] = set_value( 'KERANI_CODE' );
$data['values']['ITEM_CODE1'] = set_value( 'ITEM_CODE1' );
$data['values']['ITEM_CODE2'] = set_value( 'ITEM_CODE2' );
$data['values']['ITEM_CODE3'] = set_value( 'ITEM_CODE3' );
$data['values']['INPUT_BY'] = set_value( 'INPUT_BY' );
$data['values']['INPUT_DATE'] = set_value( 'INPUT_DATE' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_gang_activity.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['MANDORE1_CODE'] = $this->input->post( 'MANDORE1_CODE' );
$data_post['MANDORE_CODE'] = $this->input->post( 'MANDORE_CODE' );
$data_post['KERANI_CODE'] = $this->input->post( 'KERANI_CODE' );
$data_post['ITEM_CODE1'] = $this->input->post( 'ITEM_CODE1' );
$data_post['ITEM_CODE2'] = $this->input->post( 'ITEM_CODE2' );
$data_post['ITEM_CODE3'] = $this->input->post( 'ITEM_CODE3' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_gang_activity->update_m_gang_activity( $id, $data_post );
    
					redirect( 'M_gang_activity/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>