<?

class M_blockmaster extends Controller 
{
    
	function M_blockmaster ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_blockmaster' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
	}

    function index()
    {
		$data['COMPANY_CODE'] =$this->global_func->dropdownlist("COMPANY_CODE","m_company","COMPANY_NAME","COMPANY_CODE");
		$this->load->view('info_m_blockmaster', $data);
    }    

    function info( $id )
    {

		$data_info = $this->model_m_blockmaster->info_m_blockmaster( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_blockmaster.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_blockmaster->enroll_m_blockmaster( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_blockmaster/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
  			
				$this->form_validation->set_rules( 'CONCESSIONID', lang('CONCESSIONID'), 'required' );    
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
				$this->form_validation->set_rules( 'NOHGU', lang('NOHGU'), 'required' );
				$this->form_validation->set_rules( 'BLOCKID', lang('BLOCKID'), 'required' );
				/*$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
				$this->form_validation->set_rules( 'SOILTYPE', lang('SOILTYPE'), 'required' );
				$this->form_validation->set_rules( 'TOPOGRAPH', lang('TOPOGRAPH'), 'required' );
				$this->form_validation->set_rules( 'HECTARAGE', lang('HECTARAGE'), 'required' );
				$this->form_validation->set_rules( 'PLANTABLE', lang('PLANTABLE'), 'required' );
				$this->form_validation->set_rules( 'UNPLANTABLE', lang('UNPLANTABLE'), 'required' );
				$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
				$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
				$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
				$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
				$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
				$this->form_validation->set_rules( 'PLANTED', lang('PLANTED'), 'required' );
				$this->form_validation->set_rules( 'UNPLANTED', lang('UNPLANTED'), 'required' );
				$this->form_validation->set_rules( 'NONEFFECTIVE', lang('NONEFFECTIVE'), 'required' );
				$this->form_validation->set_rules( 'VEGETATION', lang('VEGETATION'), 'required' );
				$this->form_validation->set_rules( 'INTIPLASMA', lang('INTIPLASMA'), 'required' );
				*/
    
                if ( $this->form_validation->run() == FALSE )
                {
					$data['values']['CONCESSIONID'] = set_value( 'CONCESSIONID' );    
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
					$data['values']['NOHGU'] = set_value( 'NOHGU' );
					$data['values']['BLOCKID'] = set_value( 'BLOCKID' );
					$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
					$data['values']['SOILTYPE'] = set_value( 'SOILTYPE' );
					$data['values']['TOPOGRAPH'] = set_value( 'TOPOGRAPH' );
					$data['values']['HECTARAGE'] = set_value( 'HECTARAGE' );
					$data['values']['PLANTABLE'] = set_value( 'PLANTABLE' );
					$data['values']['UNPLANTABLE'] = set_value( 'UNPLANTABLE' );
					$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
					$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
					$data['values']['ROLLING'] = set_value( 'ROLLING' );
					$data['values']['FLAT'] = set_value( 'FLAT' );
					$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
					$data['values']['PLANTED'] = set_value( 'PLANTED' );
					$data['values']['UNPLANTED'] = set_value( 'UNPLANTED' );
					$data['values']['NONEFFECTIVE'] = set_value( 'NONEFFECTIVE' );
					$data['values']['VEGETATION'] = set_value( 'VEGETATION' );
					$data['values']['INTIPLASMA'] = set_value( 'INTIPLASMA' );
					
					//$data['COMPANY_CODE'] =$this->global_func->dropdownlist("COMPANY_CODE","m_company","COMPANY_NAME","COMPANY_CODE");
                    $data['form_mode'] = 'create'; 
					//$this->load->view( 'create_m_blockmaster.php', $data );
	            }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    				$data_post['CONCESSIONID'] = $this->input->post( 'CONCESSIONID' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
					$data_post['NOHGU'] = $this->input->post( 'NOHGU' );
					$data_post['BLOCKID'] = $this->input->post( 'BLOCKID' );
					$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
					$data_post['SOILTYPE'] = $this->input->post( 'SOILTYPE' );
					$data_post['TOPOGRAPH'] = $this->input->post( 'TOPOGRAPH' );
					$data_post['HECTARAGE'] = $this->input->post( 'HECTARAGE' );
					$data_post['PLANTABLE'] = $this->input->post( 'PLANTABLE' );
					$data_post['UNPLANTABLE'] = $this->input->post( 'UNPLANTABLE' );
					$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
					$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
					$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
					$data_post['FLAT'] = $this->input->post( 'FLAT' );
					$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
					$data_post['PLANTED'] = $this->input->post( 'PLANTED' );
					$data_post['UNPLANTED'] = $this->input->post( 'UNPLANTED' );
					$data_post['NONEFFECTIVE'] = $this->input->post( 'NONEFFECTIVE' );
					$data_post['VEGETATION'] = $this->input->post( 'VEGETATION' );
					$data_post['INTIPLASMA'] = $this->input->post( 'INTIPLASMA' );

    
                    $insert_id = $this->model_m_blockmaster->insert_m_blockmaster( $data_post );
                    
    
                }
    
    }

    function edit( $id )
    {
   
        $this->load->library('form_validation'); 
		
				$this->form_validation->set_rules( 'CONCESSIONID', lang('CONCESSIONID'), 'required' );          
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
				$this->form_validation->set_rules( 'NOHGU', lang('NOHGU'), 'required' );
				$this->form_validation->set_rules( 'BLOCKID', lang('BLOCKID'), 'required' );
				/* $this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
				$this->form_validation->set_rules( 'SOILTYPE', lang('SOILTYPE'), 'required' );
				$this->form_validation->set_rules( 'TOPOGRAPH', lang('TOPOGRAPH'), 'required' );
				$this->form_validation->set_rules( 'HECTARAGE', lang('HECTARAGE'), 'required' );
				$this->form_validation->set_rules( 'PLANTABLE', lang('PLANTABLE'), 'required' );
				$this->form_validation->set_rules( 'UNPLANTABLE', lang('UNPLANTABLE'), 'required' );
				$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
				$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
				$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
				$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
				$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
				$this->form_validation->set_rules( 'PLANTED', lang('PLANTED'), 'required' );
				$this->form_validation->set_rules( 'UNPLANTED', lang('UNPLANTED'), 'required' );
				$this->form_validation->set_rules( 'NONEFFECTIVE', lang('NONEFFECTIVE'), 'required' );
				$this->form_validation->set_rules( 'VEGETATION', lang('VEGETATION'), 'required' );
				$this->form_validation->set_rules( 'INTIPLASMA', lang('INTIPLASMA'), 'required' ); */

    
                if ( $this->form_validation->run() == FALSE )
                {
                   
					$data['values']['CONCESSIONID'] = set_value( 'CONCESSIONID' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
					$data['values']['NOHGU'] = set_value( 'NOHGU' );
					$data['values']['BLOCKID'] = set_value( 'BLOCKID' );
					$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
					$data['values']['SOILTYPE'] = set_value( 'SOILTYPE' );
					$data['values']['TOPOGRAPH'] = set_value( 'TOPOGRAPH' );
					$data['values']['HECTARAGE'] = set_value( 'HECTARAGE' );
					$data['values']['PLANTABLE'] = set_value( 'PLANTABLE' );
					$data['values']['UNPLANTABLE'] = set_value( 'UNPLANTABLE' );
					$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
					$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
					$data['values']['ROLLING'] = set_value( 'ROLLING' );
					$data['values']['FLAT'] = set_value( 'FLAT' );
					$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
					$data['values']['PLANTED'] = set_value( 'PLANTED' );
					$data['values']['UNPLANTED'] = set_value( 'UNPLANTED' );
					$data['values']['NONEFFECTIVE'] = set_value( 'NONEFFECTIVE' );
					$data['values']['VEGETATION'] = set_value( 'VEGETATION' );
					$data['values']['INTIPLASMA'] = set_value( 'INTIPLASMA' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_m_blockmaster.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    				$data_post['CONCESSIONID'] = $this->input->post( 'CONCESSIONID' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
					$data_post['NOHGU'] = $this->input->post( 'NOHGU' );
					$data_post['BLOCKID'] = $this->input->post( 'BLOCKID' );
					$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
					$data_post['SOILTYPE'] = $this->input->post( 'SOILTYPE' );
					$data_post['TOPOGRAPH'] = $this->input->post( 'TOPOGRAPH' );
					$data_post['HECTARAGE'] = $this->input->post( 'HECTARAGE' );
					$data_post['PLANTABLE'] = $this->input->post( 'PLANTABLE' );
					$data_post['UNPLANTABLE'] = $this->input->post( 'UNPLANTABLE' );
					$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
					$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
					$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
					$data_post['FLAT'] = $this->input->post( 'FLAT' );
					$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
					$data_post['PLANTED'] = $this->input->post( 'PLANTED' );
					$data_post['UNPLANTED'] = $this->input->post( 'UNPLANTED' );
					$data_post['NONEFFECTIVE'] = $this->input->post( 'NONEFFECTIVE' );
					$data_post['VEGETATION'] = $this->input->post( 'VEGETATION' );
					$data_post['INTIPLASMA'] = $this->input->post( 'INTIPLASMA' );

				    $insert_id = $this->model_m_blockmaster->update_m_blockmaster( $id, $data_post );
    

                }

                 
    }
	
	
	//---------------- delete -------------
	
	function delete($id)
	{
		$this->model_m_blockmaster->delete($id);
	}
	
	// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_blockmaster->readByPagination());
    }


}

?>