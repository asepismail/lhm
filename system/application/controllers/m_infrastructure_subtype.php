<?

class M_infrastructure_subtype extends Controller 
{
    
	function M_infrastructure_subtype ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_infrastructure_subtype' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
	}

    function index()
    {
		$data['IFTYPE'] =$this->global_func->dropdownlist("IFTYPE","m_infrastructure_type","IFTYPE_NAME","IFTYPE");
		$this->load->view('info_m_infrastructure_subtype', $data);
    }    

    function info( $id )
    {

		$data_info = $this->model_m_infrastructure_subtype->info_m_infrastructure_subtype( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_infrastructure_subtype.php', $data ); 
    }

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		   
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'IFSUBTYPE', lang('IFSUBTYPE'), 'required' );
				//$this->form_validation->set_rules( 'IFSUBTYPE_NAME', lang('IFSUBTYPE_NAME'), 'required' );
				//$this->form_validation->set_rules( 'IFTYPE', lang('IFTYPE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    				$data['values']['IFSUBTYPE'] = set_value( 'IFSUBTYPE' );
					$data['values']['IFSUBTYPE_NAME'] = set_value( 'IFSUBTYPE_NAME' );
					$data['values']['IFTYPE'] = set_value( 'IFTYPE' );

                    $data['form_mode'] = 'create'; 
                    
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['IFSUBTYPE'] = $this->input->post ( 'IFSUBTYPE' );
					$data_post['IFSUBTYPE_NAME'] = $this->input->post( 'IFSUBTYPE_NAME' );
					$data_post['IFTYPE'] = $this->input->post( 'IFTYPE' );

    
                    $insert_id = $this->model_m_infrastructure_subtype->insert_m_infrastructure_subtype( $data_post );
                    
					    
                }
         
    }

    function edit( $id )
    {
        
		$this->load->library('form_validation'); 
      
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'IFSUBTYPE_NAME', lang('IFSUBTYPE_NAME'), 'required' );
				//$this->form_validation->set_rules( 'IFTYPE', lang('IFTYPE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $data['values']['IFSUBTYPE_NAME'] = set_value( 'IFSUBTYPE_NAME' );
					$data['values']['IFTYPE'] = set_value( 'IFTYPE' );

                    $data['form_mode'] = 'edit'; 
                    //$this->load->view( 'edit_m_infrastructure_subtype.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
					$data_post['IFSUBTYPE_NAME'] = $this->input->post( 'IFSUBTYPE_NAME' );
					$data_post['IFTYPE'] = $this->input->post( 'IFTYPE' );

                    $insert_id = $this->model_m_infrastructure_subtype->update_m_infrastructure_subtype( $id, $data_post );
    
					    
                }
    
    }
	
	
	function delete($id)
	{
		$this->model_m_infrastructure_subtype->delete($id);
	}
	
	// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_infrastructure_subtype->readByPagination());
    }

}

?>