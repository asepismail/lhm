<?

class M_infrastructure extends Controller 
{
    
	function M_infrastructure ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_infrastructure' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
	}

    function index()
    {
	$data['IFTYPE'] = $this->global_func->dropdownlist("IFTYPE","m_infrastructure_type","IFTYPE_NAME","IFTYPE");
	$data['IFSUBTYPE'] = $this->global_func->dropdownlist("IFSUBTYPE","m_infrastructure_subtype","IFSUBTYPE_NAME","IFSUBTYPE");
	$data['COMPANY_CODE'] = $this->global_func->dropdownlist("COMPANY_CODE","m_company","COMPANY_NAME","COMPANY_CODE");
	
		$this->load->view('info_m_infrastructure', $data);
    }    

    function info( $id )
    {

		$data_info = $this->model_m_infrastructure->info_m_infrastructure( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_infrastructure.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_infrastructure->enroll_m_infrastructure( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_infrastructure/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
	
    
                /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'IFCODE', lang('IFCODE'), 'required' );
				$this->form_validation->set_rules( 'FIXEDASSETCODE', lang('FIXEDASSETCODE'), 'required' );
				/*$this->form_validation->set_rules( 'IFTYPE', lang('IFTYPE'), 'required' );
				$this->form_validation->set_rules( 'IFSUBTYPE', lang('IFSUBTYPE'), 'required' );
				$this->form_validation->set_rules( 'IFNAME', lang('IFNAME'), 'required' );
				$this->form_validation->set_rules( 'IFLENGTH', lang('IFLENGTH'), 'required' );
				$this->form_validation->set_rules( 'IFWIDTH', lang('IFWIDTH'), 'required' );
				$this->form_validation->set_rules( 'UOM', lang('UOM'), 'required' );
				$this->form_validation->set_rules( 'INSTALLDATE', lang('INSTALLDATE'), 'required' );
				$this->form_validation->set_rules( 'DEVELOPMENT_COST', lang('DEVELOPMENT_COST'), 'required' );
				$this->form_validation->set_rules( 'VOLUME', lang('VOLUME'), 'required' );
				$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
				$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
				$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
				$this->form_validation->set_rules( 'ESTATE', lang('ESTATE'), 'required' );
				$this->form_validation->set_rules( 'DIVISION', lang('DIVISION'), 'required' );
				$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );*/

    
                if ( $this->form_validation->run() == FALSE )
                {
					$data['values']['IFCODE'] = set_value( 'IFCODE' );    
					$data['values']['FIXEDASSETCODE'] = set_value( 'FIXEDASSETCODE' );
					$data['values']['IFTYPE'] = set_value( 'IFTYPE' );
					$data['values']['IFSUBTYPE'] = set_value( 'IFSUBTYPE' );
					$data['values']['IFNAME'] = set_value( 'IFNAME' );
					$data['values']['IFLENGTH'] = set_value( 'IFLENGTH' );
					$data['values']['IFWIDTH'] = set_value( 'IFWIDTH' );
					$data['values']['UOM'] = set_value( 'UOM' );
					$data['values']['INSTALLDATE'] = set_value( 'INSTALLDATE' );
					$data['values']['DEVELOPMENT_COST'] = set_value( 'DEVELOPMENT_COST' );
					$data['values']['VOLUME'] = set_value( 'VOLUME' );
					$data['values']['ROLLING'] = set_value( 'ROLLING' );
					$data['values']['FLAT'] = set_value( 'FLAT' );
					$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
					$data['values']['ESTATE'] = set_value( 'ESTATE' );
					$data['values']['DIVISION'] = set_value( 'DIVISION' );
					$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'create'; 
                    
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {

					$data_post['IFCODE'] = $this->input->post( 'IFCODE' );    
					$data_post['FIXEDASSETCODE'] = $this->input->post( 'FIXEDASSETCODE' );
					$data_post['IFTYPE'] = $this->input->post( 'IFTYPE' );
					$data_post['IFSUBTYPE'] = $this->input->post( 'IFSUBTYPE' );
					$data_post['IFNAME'] = $this->input->post( 'IFNAME' );
					$data_post['IFLENGTH'] = $this->input->post( 'IFLENGTH' );
					$data_post['IFWIDTH'] = $this->input->post( 'IFWIDTH' );
					$data_post['UOM'] = $this->input->post( 'UOM' );
					$data_post['INSTALLDATE'] = $this->input->post( 'INSTALLDATE' );
					$data_post['DEVELOPMENT_COST'] = $this->input->post( 'DEVELOPMENT_COST' );
					$data_post['VOLUME'] = $this->input->post( 'VOLUME' );
					$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
					$data_post['FLAT'] = $this->input->post( 'FLAT' );
					$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
					$data_post['ESTATE'] = $this->input->post( 'ESTATE' );
					$data_post['DIVISION'] = $this->input->post( 'DIVISION' );
					$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

    
                    $insert_id = $this->model_m_infrastructure->insert_m_infrastructure( $data_post );
                    
                }
    
    
    
                 
    }

    function edit( $id )
    {
        
		
        $this->load->library('form_validation'); 

                       /* we set the rules */
                /* dont forget to edit these */
				$this->form_validation->set_rules( 'FIXEDASSETCODE', lang('FIXEDASSETCODE'), 'required' );
				$this->form_validation->set_rules( 'IFTYPE', lang('IFTYPE'), 'required' );
				$this->form_validation->set_rules( 'IFSUBTYPE', lang('IFSUBTYPE'), 'required' );
				/*$this->form_validation->set_rules( 'IFNAME', lang('IFNAME'), 'required' );
				$this->form_validation->set_rules( 'IFLENGTH', lang('IFLENGTH'), 'required' );
				$this->form_validation->set_rules( 'IFWIDTH', lang('IFWIDTH'), 'required' );
				$this->form_validation->set_rules( 'UOM', lang('UOM'), 'required' );
				$this->form_validation->set_rules( 'INSTALLDATE', lang('INSTALLDATE'), 'required' );
				$this->form_validation->set_rules( 'DEVELOPMENT_COST', lang('DEVELOPMENT_COST'), 'required' );
				$this->form_validation->set_rules( 'VOLUME', lang('VOLUME'), 'required' );
				$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
				$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
				$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
				$this->form_validation->set_rules( 'ESTATE', lang('ESTATE'), 'required' );
				$this->form_validation->set_rules( 'DIVISION', lang('DIVISION'), 'required' );
				$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
				$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );*/

    
                if ( $this->form_validation->run() == FALSE )
                {

					$data['values']['FIXEDASSETCODE'] = set_value( 'FIXEDASSETCODE' );
					$data['values']['IFTYPE'] = set_value( 'IFTYPE' );
					$data['values']['IFSUBTYPE'] = set_value( 'IFSUBTYPE' );
					$data['values']['IFNAME'] = set_value( 'IFNAME' );
					$data['values']['IFLENGTH'] = set_value( 'IFLENGTH' );
					$data['values']['IFWIDTH'] = set_value( 'IFWIDTH' );
					$data['values']['UOM'] = set_value( 'UOM' );
					$data['values']['INSTALLDATE'] = set_value( 'INSTALLDATE' );
					$data['values']['DEVELOPMENT_COST'] = set_value( 'DEVELOPMENT_COST' );
					$data['values']['VOLUME'] = set_value( 'VOLUME' );
					$data['values']['ROLLING'] = set_value( 'ROLLING' );
					$data['values']['FLAT'] = set_value( 'FLAT' );
					$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
					$data['values']['ESTATE'] = set_value( 'ESTATE' );
					$data['values']['DIVISION'] = set_value( 'DIVISION' );
					$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'edit'; 
                    
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
					$data_post['FIXEDASSETCODE'] = $this->input->post( 'FIXEDASSETCODE' );
					$data_post['IFTYPE'] = $this->input->post( 'IFTYPE' );
					$data_post['IFSUBTYPE'] = $this->input->post( 'IFSUBTYPE' );
					$data_post['IFNAME'] = $this->input->post( 'IFNAME' );
					$data_post['IFLENGTH'] = $this->input->post( 'IFLENGTH' );
					$data_post['IFWIDTH'] = $this->input->post( 'IFWIDTH' );
					$data_post['UOM'] = $this->input->post( 'UOM' );
					$data_post['INSTALLDATE'] = $this->input->post( 'INSTALLDATE' );
					$data_post['DEVELOPMENT_COST'] = $this->input->post( 'DEVELOPMENT_COST' );
					$data_post['VOLUME'] = $this->input->post( 'VOLUME' );
					$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
					$data_post['FLAT'] = $this->input->post( 'FLAT' );
					$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
					$data_post['ESTATE'] = $this->input->post( 'ESTATE' );
					$data_post['DIVISION'] = $this->input->post( 'DIVISION' );
					$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
					$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

   				    $insert_id = $this->model_m_infrastructure->update_m_infrastructure( $id, $data_post );
     
    
                }
                    
    }
	
	function delete($id)
	{
		$this->model_m_infrastructure->delete($id);
	}
	
	// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_infrastructure->readByPagination());
    }

}

?>