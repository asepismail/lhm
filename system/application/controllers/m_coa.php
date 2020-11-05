<?

class M_coa extends Controller 
{
    
	function M_coa ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_coa' ); 
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');

	}

    function index()
    {
		$this->load->view('info_m_coa');
    }    
	
    function read_json_format()
    {
        echo json_encode($this->model_m_coa->readByPagination());
    }
	
    function info( $id )
    {

		$data_info = $this->model_m_coa->info_m_coa( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_coa.php', $data ); 
    }

	function delete($id)
	{
		$this->model_m_coa->delete($id);
		
	}

	function enroll( )
	{
		$data_enroll = $this->model_m_coa->enroll_m_coa( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'm_coa/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');       
                /* we set the rules */
                /* dont forget to edit these */
		$this->form_validation->set_rules( 'ACCOUNTCODE', lang('ACCOUNTCODE'), 'required' );
		$this->form_validation->set_rules( 'ACCOUNTTYPE', lang('ACCOUNTTYPE'), 'required' );
		$this->form_validation->set_rules( 'COA_GROUPTYPE', lang('COA_GROUPTYPE'), 'required' );
		//$this->form_validation->set_rules( 'COA_OPERATIONAL', lang('COA_OPERATIONAL'), 'required' );
		$this->form_validation->set_rules( 'COA_DESCRIPTION', lang('COA_DESCRIPTION'), 'required' );
		$this->form_validation->set_rules( 'COA_STATUS', lang('COA_STATUS'), 'required' );
		//$this->form_validation->set_rules( 'COA_INPUTBY', lang('COA_INPUTBY'), 'required' );
		$this->form_validation->set_rules( 'COA_INPUTDATE', lang('COA_INPUTDATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    				$data['values']['ACCOUNTCODE'] = set_value( 'ACCOUNTCODE' );
					$data['values']['ACCOUNTTYPE'] = set_value( 'ACCOUNTTYPE' );
					$data['values']['COA_GROUPTYPE'] = set_value( 'COA_GROUPTYPE' );
					$data['values']['COA_OPERATIONAL'] = set_value( 'COA_OPERATIONAL' );
					$data['values']['COA_DESCRIPTION'] = set_value( 'COA_DESCRIPTION' );
					$data['values']['COA_STATUS'] = set_value( 'COA_STATUS' );
					$data['values']['COA_INPUTBY'] = set_value( 'COA_INPUTBY' );
					$data['values']['COA_INPUTDATE'] = set_value( 'COA_INPUTDATE' );

                    $data['form_mode'] = 'create'; 
                    //$this->load->view( 'create_m_coa.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['ACCOUNTCODE'] = $this->input->post( 'ACCOUNTCODE' );
					$data_post['ACCOUNTTYPE'] = $this->input->post( 'ACCOUNTTYPE' );
					$data_post['COA_GROUPTYPE'] = $this->input->post( 'COA_GROUPTYPE' );
					$data_post['COA_OPERATIONAL'] = $this->input->post( 'COA_OPERATIONAL' );
					$data_post['COA_DESCRIPTION'] = $this->input->post( 'COA_DESCRIPTION' );
					$data_post['COA_STATUS'] = $this->input->post( 'COA_STATUS' );
					$data_post['COA_INPUTBY'] = $this->input->post( 'COA_INPUTBY' );
					$data_post['COA_INPUTDATE'] = $this->input->post( 'COA_INPUTDATE' );

    
                    $insert_id = $this->model_m_coa->insert_m_coa( $data_post );
                    
					//redirect( 'M_coa/info/' . $insert_id );
    
                }          
    }

    function edit( $id  )
    {
        
		
        $this->load->library('form_validation'); 
           
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'ACCOUNTCODE', lang('ACCOUNTCODE'), 'required' );
				$this->form_validation->set_rules( 'ACCOUNTTYPE', lang('ACCOUNTTYPE'), 'required' );
				$this->form_validation->set_rules( 'COA_GROUPTYPE', lang('COA_GROUPTYPE'), 'required' );
				//$this->form_validation->set_rules( 'COA_OPERATIONAL', lang('COA_OPERATIONAL'), 'required' );
				$this->form_validation->set_rules( 'COA_DESCRIPTION', lang('COA_DESCRIPTION'), 'required' );
				$this->form_validation->set_rules( 'COA_STATUS', lang('COA_STATUS'), 'required' );
				//$this->form_validation->set_rules( 'COA_INPUTBY', lang('COA_INPUTBY'), 'required' );
				$this->form_validation->set_rules( 'COA_INPUTDATE', lang('COA_INPUTDATE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    //$this->session->keep_flashdata('id');
					$data['values']['ACCOUNTCODE'] = set_value( 'ACCOUNTCODE' );
					$data['values']['ACCOUNTTYPE'] = set_value( 'ACCOUNTTYPE' );
					$data['values']['COA_GROUPTYPE'] = set_value( 'COA_GROUPTYPE' );
					$data['values']['COA_OPERATIONAL'] = set_value( 'COA_OPERATIONAL' );
					$data['values']['COA_DESCRIPTION'] = set_value( 'COA_DESCRIPTION' );
					$data['values']['COA_STATUS'] = set_value( 'COA_STATUS' );
					$data['values']['COA_INPUTBY'] = set_value( 'COA_INPUTBY' );
					$data['values']['COA_INPUTDATE'] = set_value( 'COA_INPUTDATE' );

                    $data['form_mode'] = 'edit'; 
                    //$this->load->view( 'edit_m_coa.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    				$data_post['ACCOUNTCODE'] = $this->input->post( 'ACCOUNTCODE' );
					$data_post['ACCOUNTTYPE'] = $this->input->post( 'ACCOUNTTYPE' );
					$data_post['COA_GROUPTYPE'] = $this->input->post( 'COA_GROUPTYPE' );
					$data_post['COA_OPERATIONAL'] = $this->input->post( 'COA_OPERATIONAL' );
					$data_post['COA_DESCRIPTION'] = $this->input->post( 'COA_DESCRIPTION' );
					$data_post['COA_STATUS'] = $this->input->post( 'COA_STATUS' );
					$data_post['COA_INPUTBY'] = $this->input->post( 'COA_INPUTBY' );
					$data_post['COA_INPUTDATE'] = $this->input->post( 'COA_INPUTDATE' );

                   // $id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_coa->update_m_coa( $id, $data_post );
    
					//redirect( 'M_coa/info/' . $id );   
    
                }

                 
    }

}

?>