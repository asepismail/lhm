<?

class M_company extends Controller 
{
    
	function M_company ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_company' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
	}

    function index()
    {
		$this->load->view('info_m_company');
    }    

    function info( $id )
    {

		$data_info = $this->model_m_company->info_m_company( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_company.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_company->enroll_m_company( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_company/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
                 
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
				$this->form_validation->set_rules( 'COMPANY_NAME', lang('COMPANY_NAME'), 'required' );
			

    
                if ( $this->form_validation->run() == FALSE )
                {
    				$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
					$data['values']['COMPANY_NAME'] = set_value( 'COMPANY_NAME' );
					$data['values']['COMPANY_ADDRESS'] = set_value( 'COMPANY_ADDRESS' );
					$data['values']['COMPANY_PHONE'] = set_value( 'COMPANY_PHONE' );
					$data['values']['COMPANY_EMAIL'] = set_value( 'COMPANY_EMAIL' );
					$data['values']['COMPANY_NPWP'] = set_value( 'COMPANY_NPWP' );
					$data['values']['COMPANY_FLAG'] = set_value( 'COMPANY_FLAG' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'info_m_company.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
					$data_post['COMPANY_NAME'] = $this->input->post( 'COMPANY_NAME' );
					$data_post['COMPANY_ADDRESS'] = $this->input->post( 'COMPANY_ADDRESS' );
					$data_post['COMPANY_PHONE'] = $this->input->post( 'COMPANY_PHONE' );
					$data_post['COMPANY_EMAIL'] = $this->input->post( 'COMPANY_EMAIL' );
					$data_post['COMPANY_NPWP'] = $this->input->post( 'COMPANY_NPWP' );
					$data_post['COMPANY_FLAG'] = $this->input->post( 'COMPANY_FLAG' );

    
                    $insert_id = $this->model_m_company->insert_m_company( $data_post );
                    
					//redirect( 'M_company/info/' . $insert_id );
    
                }
      
                 
    }

    function edit( $id )
    {
        	
        $this->load->library('form_validation'); 

                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
				$this->form_validation->set_rules( 'COMPANY_NAME', lang('COMPANY_NAME'), 'required' );
				
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
					$data['values']['COMPANY_NAME'] = set_value( 'COMPANY_NAME' );
					$data['values']['COMPANY_ADDRESS'] = set_value( 'COMPANY_ADDRESS' );
					$data['values']['COMPANY_PHONE'] = set_value( 'COMPANY_PHONE' );
					$data['values']['COMPANY_EMAIL'] = set_value( 'COMPANY_EMAIL' );
					$data['values']['COMPANY_NPWP'] = set_value( 'COMPANY_NPWP' );
					$data['values']['COMPANY_FLAG'] = set_value( 'COMPANY_FLAG' );

                    $data['form_mode'] = 'edit'; 
                    //$this->load->view( 'info_m_company.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
					$data_post['COMPANY_NAME'] = $this->input->post( 'COMPANY_NAME' );
					$data_post['COMPANY_ADDRESS'] = $this->input->post( 'COMPANY_ADDRESS' );
					$data_post['COMPANY_PHONE'] = $this->input->post( 'COMPANY_PHONE' );
					$data_post['COMPANY_EMAIL'] = $this->input->post( 'COMPANY_EMAIL' );
					$data_post['COMPANY_NPWP'] = $this->input->post( 'COMPANY_NPWP' );
					$data_post['COMPANY_FLAG'] = $this->input->post( 'COMPANY_FLAG' );

                    //$id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_company->update_m_company( $id, $data_post );
    
					//redirect( 'M_company/info/' . $id );   
    
                }
                 
    }
	
	//---------------------- fungsi delete
	
	function delete($id)
	{
		$this->model_m_company->delete($id);
	}
	 
	// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_company->readByPagination());
    }

}

?>