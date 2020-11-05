<?

class M_infrastructure_type extends Controller 
{
    
	function M_infrastructure_type ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_infrastructure_type' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
	}

    function index()
    {
		$this->load->view('info_m_infrastructure_type');
    }    

    function info( $id )
    {

		$data_info = $this->model_m_infrastructure_type->info_m_infrastructure_type( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_infrastructure_type.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_infrastructure_type->enroll_m_infrastructure_type( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_infrastructure_type/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
                /* we set the rules */
				                /* dont forget to edit these */
				$this->form_validation->set_rules( 'IFTYPE_NAME', lang('IFTYPE_NAME'), 'required' );
				//$this->form_validation->set_rules( 'IFTYPE_NAME', lang('IFTYPE_NAME'), 'required' );
				//$this->form_validation->set_rules( 'CONTROL_JOB', lang('CONTROL_JOB'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
					$data['values']['IFTYPE'] = set_value( 'IFTYPE' );    
					$data['values']['IFTYPE_NAME'] = set_value( 'IFTYPE_NAME' );
					$data['values']['CONTROL_JOB'] = set_value( 'CONTROL_JOB' );

                    $data['form_mode'] = 'create'; 
                   
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['IFTYPE'] = $this->input->post( 'IFTYPE' );
					$data_post['IFTYPE_NAME'] = $this->input->post( 'IFTYPE_NAME' );
					$data_post['CONTROL_JOB'] = $this->input->post( 'CONTROL_JOB' );

    
                    $insert_id = $this->model_m_infrastructure_type->insert_m_infrastructure_type( $data_post ); 
					    
                }
                 
    }

    function edit( $id )
    {
        
        $this->load->library('form_validation'); 

          
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'IFTYPE_NAME', lang('IFTYPE_NAME'), 'required' );
				//$this->form_validation->set_rules( 'CONTROL_JOB', lang('CONTROL_JOB'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                   // $this->session->keep_flashdata('id');
$data['values']['IFTYPE_NAME'] = set_value( 'IFTYPE_NAME' );
$data['values']['CONTROL_JOB'] = set_value( 'CONTROL_JOB' );

                    $data['form_mode'] = 'edit'; 
                    
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
					$data_post['IFTYPE_NAME'] = $this->input->post( 'IFTYPE_NAME' );
					$data_post['CONTROL_JOB'] = $this->input->post( 'CONTROL_JOB' );

			    $insert_id = $this->model_m_infrastructure_type->update_m_infrastructure_type( $id, $data_post );
				}
    
					
    }

	function delete($id)
	{
		$this->model_m_infrastructure_type->delete($id);
	}
	 
// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_infrastructure_type->readByPagination());
    }
}

?>