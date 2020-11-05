<?

class M_empgang_history extends Controller 
{
    
	function M_empgang_history ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_empgang_history' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'M_empgang_history/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_m_empgang_history->info_m_empgang_history( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_empgang_history.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_empgang_history->enroll_m_empgang_history( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_empgang_history/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['EMPLOYEE_CODE'] = '';
$data['values']['BASIC_SALARY'] = '';
$data['values']['FAMILY_STATUS_RICE'] = '';
$data['values']['FAMILY_STATUS_STAX'] = '';
$data['values']['GRADE_ID'] = '';
$data['values']['PAYROLL'] = '';
$data['values']['MONTH'] = '';
$data['values']['YEAR'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_m_empgang_history.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'EMPLOYEE_CODE', lang('EMPLOYEE_CODE'), 'required' );
$this->form_validation->set_rules( 'BASIC_SALARY', lang('BASIC_SALARY'), 'required' );
$this->form_validation->set_rules( 'FAMILY_STATUS_RICE', lang('FAMILY_STATUS_RICE'), 'required' );
$this->form_validation->set_rules( 'FAMILY_STATUS_STAX', lang('FAMILY_STATUS_STAX'), 'required' );
$this->form_validation->set_rules( 'GRADE_ID', lang('GRADE_ID'), 'required' );
$this->form_validation->set_rules( 'PAYROLL', lang('PAYROLL'), 'required' );
$this->form_validation->set_rules( 'MONTH', lang('MONTH'), 'required' );
$this->form_validation->set_rules( 'YEAR', lang('YEAR'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['EMPLOYEE_CODE'] = set_value( 'EMPLOYEE_CODE' );
$data['values']['BASIC_SALARY'] = set_value( 'BASIC_SALARY' );
$data['values']['FAMILY_STATUS_RICE'] = set_value( 'FAMILY_STATUS_RICE' );
$data['values']['FAMILY_STATUS_STAX'] = set_value( 'FAMILY_STATUS_STAX' );
$data['values']['GRADE_ID'] = set_value( 'GRADE_ID' );
$data['values']['PAYROLL'] = set_value( 'PAYROLL' );
$data['values']['MONTH'] = set_value( 'MONTH' );
$data['values']['YEAR'] = set_value( 'YEAR' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_m_empgang_history.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['EMPLOYEE_CODE'] = $this->input->post( 'EMPLOYEE_CODE' );
$data_post['BASIC_SALARY'] = $this->input->post( 'BASIC_SALARY' );
$data_post['FAMILY_STATUS_RICE'] = $this->input->post( 'FAMILY_STATUS_RICE' );
$data_post['FAMILY_STATUS_STAX'] = $this->input->post( 'FAMILY_STATUS_STAX' );
$data_post['GRADE_ID'] = $this->input->post( 'GRADE_ID' );
$data_post['PAYROLL'] = $this->input->post( 'PAYROLL' );
$data_post['MONTH'] = $this->input->post( 'MONTH' );
$data_post['YEAR'] = $this->input->post( 'YEAR' );

    
                    $insert_id = $this->model_m_empgang_history->insert_m_empgang_history( $data_post );
                    
					redirect( 'M_empgang_history/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_m_empgang_history->Info_m_empgang_history( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_m_empgang_history.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'EMPLOYEE_CODE', lang('EMPLOYEE_CODE'), 'required' );
$this->form_validation->set_rules( 'BASIC_SALARY', lang('BASIC_SALARY'), 'required' );
$this->form_validation->set_rules( 'FAMILY_STATUS_RICE', lang('FAMILY_STATUS_RICE'), 'required' );
$this->form_validation->set_rules( 'FAMILY_STATUS_STAX', lang('FAMILY_STATUS_STAX'), 'required' );
$this->form_validation->set_rules( 'GRADE_ID', lang('GRADE_ID'), 'required' );
$this->form_validation->set_rules( 'PAYROLL', lang('PAYROLL'), 'required' );
$this->form_validation->set_rules( 'MONTH', lang('MONTH'), 'required' );
$this->form_validation->set_rules( 'YEAR', lang('YEAR'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['EMPLOYEE_CODE'] = set_value( 'EMPLOYEE_CODE' );
$data['values']['BASIC_SALARY'] = set_value( 'BASIC_SALARY' );
$data['values']['FAMILY_STATUS_RICE'] = set_value( 'FAMILY_STATUS_RICE' );
$data['values']['FAMILY_STATUS_STAX'] = set_value( 'FAMILY_STATUS_STAX' );
$data['values']['GRADE_ID'] = set_value( 'GRADE_ID' );
$data['values']['PAYROLL'] = set_value( 'PAYROLL' );
$data['values']['MONTH'] = set_value( 'MONTH' );
$data['values']['YEAR'] = set_value( 'YEAR' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_empgang_history.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['EMPLOYEE_CODE'] = $this->input->post( 'EMPLOYEE_CODE' );
$data_post['BASIC_SALARY'] = $this->input->post( 'BASIC_SALARY' );
$data_post['FAMILY_STATUS_RICE'] = $this->input->post( 'FAMILY_STATUS_RICE' );
$data_post['FAMILY_STATUS_STAX'] = $this->input->post( 'FAMILY_STATUS_STAX' );
$data_post['GRADE_ID'] = $this->input->post( 'GRADE_ID' );
$data_post['PAYROLL'] = $this->input->post( 'PAYROLL' );
$data_post['MONTH'] = $this->input->post( 'MONTH' );
$data_post['YEAR'] = $this->input->post( 'YEAR' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_empgang_history->update_m_empgang_history( $id, $data_post );
    
					redirect( 'M_empgang_history/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>