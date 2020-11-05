<?

class M_fieldcrop extends Controller 
{
    
	function M_fieldcrop ()
	{
		parent::Controller();	

		$this->load->model( 'model_m_fieldcrop' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
	}

    function index()
    {
		$this->load->view('info_m_fieldcrop');
    }    

    function info( $id )
    {

		$data_info = $this->model_m_fieldcrop->info_m_fieldcrop( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_m_fieldcrop.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_m_fieldcrop->enroll_m_fieldcrop( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'M_fieldcrop/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
			//$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
			//$this->form_validation->set_rules( 'NOHGU', lang('NOHGU'), 'required' );
			//$this->form_validation->set_rules( 'USAGEID', lang('USAGEID'), 'required' );
			$this->form_validation->set_rules( 'FIELDCODE', lang('FIELDCODE'), 'required' );
			/* $this->form_validation->set_rules( 'ESTATECODE', lang('ESTATECODE'), 'required' );
			$this->form_validation->set_rules( 'DIVISIONCODE', lang('DIVISIONCODE'), 'required' );
			$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
			$this->form_validation->set_rules( 'CROPTYPE', lang('CROPTYPE'), 'required' );
			$this->form_validation->set_rules( 'HECTPLANTED', lang('HECTPLANTED'), 'required' );
			$this->form_validation->set_rules( 'PLANTINGDATE', lang('PLANTINGDATE'), 'required' );
			$this->form_validation->set_rules( 'PLANTINGDISTANCE', lang('PLANTINGDISTANCE'), 'required' );
			$this->form_validation->set_rules( 'LASTSUPPHECT', lang('LASTSUPPHECT'), 'required' );
			$this->form_validation->set_rules( 'LASTSUPPDATE', lang('LASTSUPPDATE'), 'required' );
			$this->form_validation->set_rules( 'TOTSTANDOFFIELD', lang('TOTSTANDOFFIELD'), 'required' );
			$this->form_validation->set_rules( 'STANDPERHECT', lang('STANDPERHECT'), 'required' );
			$this->form_validation->set_rules( 'CHECKROLLPRACTICE', lang('CHECKROLLPRACTICE'), 'required' );
			$this->form_validation->set_rules( 'PAYMENTMETHOD', lang('PAYMENTMETHOD'), 'required' );
			$this->form_validation->set_rules( 'HEIGHTCLASS', lang('HEIGHTCLASS'), 'required' );
			$this->form_validation->set_rules( 'CROPPOLICY', lang('CROPPOLICY'), 'required' );
			$this->form_validation->set_rules( 'YEARREPLANT', lang('YEARREPLANT'), 'required' );
			$this->form_validation->set_rules( 'LONGCARRYPERC', lang('LONGCARRYPERC'), 'required' );
			$this->form_validation->set_rules( 'SPECIES', lang('SPECIES'), 'required' );
			$this->form_validation->set_rules( 'HARVCOMMDATE', lang('HARVCOMMDATE'), 'required' );
			$this->form_validation->set_rules( 'PALMSHARV', lang('PALMSHARV'), 'required' );
			$this->form_validation->set_rules( 'HECTHARV', lang('HECTHARV'), 'required' );
			$this->form_validation->set_rules( 'HECTRESTED', lang('HECTRESTED'), 'required' );
			$this->form_validation->set_rules( 'CLONES', lang('CLONES'), 'required' );
			$this->form_validation->set_rules( 'FIELDAGE', lang('FIELDAGE'), 'required' );
			$this->form_validation->set_rules( 'COSTCENTERID', lang('COSTCENTERID'), 'required' );
			$this->form_validation->set_rules( 'INTIPLASMA', lang('INTIPLASMA'), 'required' );
			$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
			$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
			$this->form_validation->set_rules( 'TERAINTYPE', lang('TERAINTYPE'), 'required' );
			$this->form_validation->set_rules( 'TOTALHECTARAGE', lang('TOTALHECTARAGE'), 'required' );
			$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
			$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
			$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
			$this->form_validation->set_rules( 'BLOCKID', lang('BLOCKID'), 'required' );*/

    
                if ( $this->form_validation->run() == FALSE )
                {
    			$data['values']['CONCESSIONID'] = set_value( 'CONCESSIONID' );
				$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
				$data['values']['NOHGU'] = set_value( 'NOHGU' );
				$data['values']['USAGEID'] = set_value( 'USAGEID' );
				$data['values']['FIELDCODE'] = set_value( 'FIELDCODE' );
				$data['values']['ESTATECODE'] = set_value( 'ESTATECODE' );
				$data['values']['DIVISIONCODE'] = set_value( 'DIVISIONCODE' );
				$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
				$data['values']['CROPTYPE'] = set_value( 'CROPTYPE' );
				$data['values']['HECTPLANTED'] = set_value( 'HECTPLANTED' );
				$data['values']['PLANTINGDATE'] = set_value( 'PLANTINGDATE' );
				$data['values']['PLANTINGDISTANCE'] = set_value( 'PLANTINGDISTANCE' );
				$data['values']['LASTSUPPHECT'] = set_value( 'LASTSUPPHECT' );
				$data['values']['LASTSUPPDATE'] = set_value( 'LASTSUPPDATE' );
				$data['values']['TOTSTANDOFFIELD'] = set_value( 'TOTSTANDOFFIELD' );
				$data['values']['STANDPERHECT'] = set_value( 'STANDPERHECT' );
				$data['values']['CHECKROLLPRACTICE'] = set_value( 'CHECKROLLPRACTICE' );
				$data['values']['PAYMENTMETHOD'] = set_value( 'PAYMENTMETHOD' );
				$data['values']['HEIGHTCLASS'] = set_value( 'HEIGHTCLASS' );
				$data['values']['CROPPOLICY'] = set_value( 'CROPPOLICY' );
				$data['values']['YEARREPLANT'] = set_value( 'YEARREPLANT' );
				$data['values']['LONGCARRYPERC'] = set_value( 'LONGCARRYPERC' );
				$data['values']['SPECIES'] = set_value( 'SPECIES' );
				$data['values']['HARVCOMMDATE'] = set_value( 'HARVCOMMDATE' );
				$data['values']['PALMSHARV'] = set_value( 'PALMSHARV' );
				$data['values']['HECTHARV'] = set_value( 'HECTHARV' );
				$data['values']['HECTRESTED'] = set_value( 'HECTRESTED' );
				$data['values']['CLONES'] = set_value( 'CLONES' );
				$data['values']['FIELDAGE'] = set_value( 'FIELDAGE' );
				$data['values']['COSTCENTERID'] = set_value( 'COSTCENTERID' );
				$data['values']['INTIPLASMA'] = set_value( 'INTIPLASMA' );
				$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
				$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
				$data['values']['TERAINTYPE'] = set_value( 'TERAINTYPE' );
				$data['values']['TOTALHECTARAGE'] = set_value( 'TOTALHECTARAGE' );
				$data['values']['ROLLING'] = set_value( 'ROLLING' );
				$data['values']['FLAT'] = set_value( 'FLAT' );
				$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
				$data['values']['BLOCKID'] = set_value( 'BLOCKID' );

                    $data['form_mode'] = 'create'; 
                   // $this->load->view( 'create_m_fieldcrop.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
				$data_post['CONCESSIONID'] = $this->input->post( 'CONCESSIONID' );
				$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
				$data_post['NOHGU'] = $this->input->post( 'NOHGU' );
				$data_post['USAGEID'] = $this->input->post( 'USAGEID' );
				$data_post['FIELDCODE'] = $this->input->post( 'FIELDCODE' );
				$data_post['ESTATECODE'] = $this->input->post( 'ESTATECODE' );
				$data_post['DIVISIONCODE'] = $this->input->post( 'DIVISIONCODE' );
				$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
				$data_post['CROPTYPE'] = $this->input->post( 'CROPTYPE' );
				$data_post['HECTPLANTED'] = $this->input->post( 'HECTPLANTED' );
				$data_post['PLANTINGDATE'] = $this->input->post( 'PLANTINGDATE' );
				$data_post['PLANTINGDISTANCE'] = $this->input->post( 'PLANTINGDISTANCE' );
				$data_post['LASTSUPPHECT'] = $this->input->post( 'LASTSUPPHECT' );
				$data_post['LASTSUPPDATE'] = $this->input->post( 'LASTSUPPDATE' );
				$data_post['TOTSTANDOFFIELD'] = $this->input->post( 'TOTSTANDOFFIELD' );
				$data_post['STANDPERHECT'] = $this->input->post( 'STANDPERHECT' );
				$data_post['CHECKROLLPRACTICE'] = $this->input->post( 'CHECKROLLPRACTICE' );
				$data_post['PAYMENTMETHOD'] = $this->input->post( 'PAYMENTMETHOD' );
				$data_post['HEIGHTCLASS'] = $this->input->post( 'HEIGHTCLASS' );
				$data_post['CROPPOLICY'] = $this->input->post( 'CROPPOLICY' );
				$data_post['YEARREPLANT'] = $this->input->post( 'YEARREPLANT' );
				$data_post['LONGCARRYPERC'] = $this->input->post( 'LONGCARRYPERC' );
				$data_post['SPECIES'] = $this->input->post( 'SPECIES' );
				$data_post['HARVCOMMDATE'] = $this->input->post( 'HARVCOMMDATE' );
				$data_post['PALMSHARV'] = $this->input->post( 'PALMSHARV' );
				$data_post['HECTHARV'] = $this->input->post( 'HECTHARV' );
				$data_post['HECTRESTED'] = $this->input->post( 'HECTRESTED' );
				$data_post['CLONES'] = $this->input->post( 'CLONES' );
				$data_post['FIELDAGE'] = $this->input->post( 'FIELDAGE' );
				$data_post['COSTCENTERID'] = $this->input->post( 'COSTCENTERID' );
				$data_post['INTIPLASMA'] = $this->input->post( 'INTIPLASMA' );
				$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
				$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
				$data_post['TERAINTYPE'] = $this->input->post( 'TERAINTYPE' );
				$data_post['TOTALHECTARAGE'] = $this->input->post( 'TOTALHECTARAGE' );
				$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
				$data_post['FLAT'] = $this->input->post( 'FLAT' );
				$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
				$data_post['BLOCKID'] = $this->input->post( 'BLOCKID' );

    
                    $insert_id = $this->model_m_fieldcrop->insert_m_fieldcrop( $data_post );
                     
                }               
    }

    function edit( $id )
    {
        
		//$this->load->library('session');
        $this->load->library('form_validation'); 

				//$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );
				//$this->form_validation->set_rules( 'NOHGU', lang('NOHGU'), 'required' );
				//$this->form_validation->set_rules( 'USAGEID', lang('USAGEID'), 'required' );
				$this->form_validation->set_rules( 'FIELDCODE', lang('FIELDCODE'), 'required' );
				/* $this->form_validation->set_rules( 'ESTATECODE', lang('ESTATECODE'), 'required' );
				$this->form_validation->set_rules( 'DIVISIONCODE', lang('DIVISIONCODE'), 'required' );
				$this->form_validation->set_rules( 'DESCRIPTION', lang('DESCRIPTION'), 'required' );
				$this->form_validation->set_rules( 'CROPTYPE', lang('CROPTYPE'), 'required' );
				$this->form_validation->set_rules( 'HECTPLANTED', lang('HECTPLANTED'), 'required' );
				$this->form_validation->set_rules( 'PLANTINGDATE', lang('PLANTINGDATE'), 'required' );
				$this->form_validation->set_rules( 'PLANTINGDISTANCE', lang('PLANTINGDISTANCE'), 'required' );
				$this->form_validation->set_rules( 'LASTSUPPHECT', lang('LASTSUPPHECT'), 'required' );
				$this->form_validation->set_rules( 'LASTSUPPDATE', lang('LASTSUPPDATE'), 'required' );
				$this->form_validation->set_rules( 'TOTSTANDOFFIELD', lang('TOTSTANDOFFIELD'), 'required' );
				$this->form_validation->set_rules( 'STANDPERHECT', lang('STANDPERHECT'), 'required' );
				$this->form_validation->set_rules( 'CHECKROLLPRACTICE', lang('CHECKROLLPRACTICE'), 'required' );
				$this->form_validation->set_rules( 'PAYMENTMETHOD', lang('PAYMENTMETHOD'), 'required' );
				$this->form_validation->set_rules( 'HEIGHTCLASS', lang('HEIGHTCLASS'), 'required' );
				$this->form_validation->set_rules( 'CROPPOLICY', lang('CROPPOLICY'), 'required' );
				$this->form_validation->set_rules( 'YEARREPLANT', lang('YEARREPLANT'), 'required' );
				$this->form_validation->set_rules( 'LONGCARRYPERC', lang('LONGCARRYPERC'), 'required' );
				$this->form_validation->set_rules( 'SPECIES', lang('SPECIES'), 'required' );
				$this->form_validation->set_rules( 'HARVCOMMDATE', lang('HARVCOMMDATE'), 'required' );
				$this->form_validation->set_rules( 'PALMSHARV', lang('PALMSHARV'), 'required' );
				$this->form_validation->set_rules( 'HECTHARV', lang('HECTHARV'), 'required' );
				$this->form_validation->set_rules( 'HECTRESTED', lang('HECTRESTED'), 'required' );
				$this->form_validation->set_rules( 'CLONES', lang('CLONES'), 'required' );
				$this->form_validation->set_rules( 'FIELDAGE', lang('FIELDAGE'), 'required' );
				$this->form_validation->set_rules( 'COSTCENTERID', lang('COSTCENTERID'), 'required' );
				$this->form_validation->set_rules( 'INTIPLASMA', lang('INTIPLASMA'), 'required' );
				$this->form_validation->set_rules( 'INACTIVE', lang('INACTIVE'), 'required' );
				$this->form_validation->set_rules( 'INACTIVEDATE', lang('INACTIVEDATE'), 'required' );
				$this->form_validation->set_rules( 'TERAINTYPE', lang('TERAINTYPE'), 'required' );
				$this->form_validation->set_rules( 'TOTALHECTARAGE', lang('TOTALHECTARAGE'), 'required' );
				$this->form_validation->set_rules( 'ROLLING', lang('ROLLING'), 'required' );
				$this->form_validation->set_rules( 'FLAT', lang('FLAT'), 'required' );
				$this->form_validation->set_rules( 'LOWLAND', lang('LOWLAND'), 'required' );
				$this->form_validation->set_rules( 'BLOCKID', lang('BLOCKID'), 'required' );*/

    
                if ( $this->form_validation->run() == FALSE )
                {
                    //$this->session->keep_flashdata('id');
					$data['values']['CONCESSIONID'] = set_value( 'CONCESSIONID' );
					$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );
					$data['values']['NOHGU'] = set_value( 'NOHGU' );
					$data['values']['USAGEID'] = set_value( 'USAGEID' );
					//$data['values']['FIELDCODE'] = set_value( 'FIELDCODE' );
					$data['values']['ESTATECODE'] = set_value( 'ESTATECODE' );
					$data['values']['DIVISIONCODE'] = set_value( 'DIVISIONCODE' );
					$data['values']['DESCRIPTION'] = set_value( 'DESCRIPTION' );
					$data['values']['CROPTYPE'] = set_value( 'CROPTYPE' );
					$data['values']['HECTPLANTED'] = set_value( 'HECTPLANTED' );
					$data['values']['PLANTINGDATE'] = set_value( 'PLANTINGDATE' );
					$data['values']['PLANTINGDISTANCE'] = set_value( 'PLANTINGDISTANCE' );
					$data['values']['LASTSUPPHECT'] = set_value( 'LASTSUPPHECT' );
					$data['values']['LASTSUPPDATE'] = set_value( 'LASTSUPPDATE' );
					$data['values']['TOTSTANDOFFIELD'] = set_value( 'TOTSTANDOFFIELD' );
					$data['values']['STANDPERHECT'] = set_value( 'STANDPERHECT' );
					$data['values']['CHECKROLLPRACTICE'] = set_value( 'CHECKROLLPRACTICE' );
					$data['values']['PAYMENTMETHOD'] = set_value( 'PAYMENTMETHOD' );
					$data['values']['HEIGHTCLASS'] = set_value( 'HEIGHTCLASS' );
					$data['values']['CROPPOLICY'] = set_value( 'CROPPOLICY' );
					$data['values']['YEARREPLANT'] = set_value( 'YEARREPLANT' );
					$data['values']['LONGCARRYPERC'] = set_value( 'LONGCARRYPERC' );
					$data['values']['SPECIES'] = set_value( 'SPECIES' );
					$data['values']['HARVCOMMDATE'] = set_value( 'HARVCOMMDATE' );
					$data['values']['PALMSHARV'] = set_value( 'PALMSHARV' );
					$data['values']['HECTHARV'] = set_value( 'HECTHARV' );
					$data['values']['HECTRESTED'] = set_value( 'HECTRESTED' );
					$data['values']['CLONES'] = set_value( 'CLONES' );
					$data['values']['FIELDAGE'] = set_value( 'FIELDAGE' );
					$data['values']['COSTCENTERID'] = set_value( 'COSTCENTERID' );
					$data['values']['INTIPLASMA'] = set_value( 'INTIPLASMA' );
					$data['values']['INACTIVE'] = set_value( 'INACTIVE' );
					$data['values']['INACTIVEDATE'] = set_value( 'INACTIVEDATE' );
					$data['values']['TERAINTYPE'] = set_value( 'TERAINTYPE' );
					$data['values']['TOTALHECTARAGE'] = set_value( 'TOTALHECTARAGE' );
					$data['values']['ROLLING'] = set_value( 'ROLLING' );
					$data['values']['FLAT'] = set_value( 'FLAT' );
					$data['values']['LOWLAND'] = set_value( 'LOWLAND' );
					$data['values']['BLOCKID'] = set_value( 'BLOCKID' );

                    $data['form_mode'] = 'edit'; 
                    //$this->load->view( 'edit_m_fieldcrop.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
						$data_post['CONCESSIONID'] = $this->input->post( 'CONCESSIONID' );    
						$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
						$data_post['NOHGU'] = $this->input->post( 'NOHGU' );
						$data_post['USAGEID'] = $this->input->post( 'USAGEID' );
						//$data_post['FIELDCODE'] = $this->input->post( 'FIELDCODE' );
						$data_post['ESTATECODE'] = $this->input->post( 'ESTATECODE' );
						$data_post['DIVISIONCODE'] = $this->input->post( 'DIVISIONCODE' );
						$data_post['DESCRIPTION'] = $this->input->post( 'DESCRIPTION' );
						$data_post['CROPTYPE'] = $this->input->post( 'CROPTYPE' );
						$data_post['HECTPLANTED'] = $this->input->post( 'HECTPLANTED' );
						$data_post['PLANTINGDATE'] = $this->input->post( 'PLANTINGDATE' );
						$data_post['PLANTINGDISTANCE'] = $this->input->post( 'PLANTINGDISTANCE' );
						$data_post['LASTSUPPHECT'] = $this->input->post( 'LASTSUPPHECT' );
						$data_post['LASTSUPPDATE'] = $this->input->post( 'LASTSUPPDATE' );
						$data_post['TOTSTANDOFFIELD'] = $this->input->post( 'TOTSTANDOFFIELD' );
						$data_post['STANDPERHECT'] = $this->input->post( 'STANDPERHECT' );
						$data_post['CHECKROLLPRACTICE'] = $this->input->post( 'CHECKROLLPRACTICE' );
						$data_post['PAYMENTMETHOD'] = $this->input->post( 'PAYMENTMETHOD' );
						$data_post['HEIGHTCLASS'] = $this->input->post( 'HEIGHTCLASS' );
						$data_post['CROPPOLICY'] = $this->input->post( 'CROPPOLICY' );
						$data_post['YEARREPLANT'] = $this->input->post( 'YEARREPLANT' );
						$data_post['LONGCARRYPERC'] = $this->input->post( 'LONGCARRYPERC' );
						$data_post['SPECIES'] = $this->input->post( 'SPECIES' );
						$data_post['HARVCOMMDATE'] = $this->input->post( 'HARVCOMMDATE' );
						$data_post['PALMSHARV'] = $this->input->post( 'PALMSHARV' );
						$data_post['HECTHARV'] = $this->input->post( 'HECTHARV' );
						$data_post['HECTRESTED'] = $this->input->post( 'HECTRESTED' );
						$data_post['CLONES'] = $this->input->post( 'CLONES' );
						$data_post['FIELDAGE'] = $this->input->post( 'FIELDAGE' );
						$data_post['COSTCENTERID'] = $this->input->post( 'COSTCENTERID' );
						$data_post['INTIPLASMA'] = $this->input->post( 'INTIPLASMA' );
						$data_post['INACTIVE'] = $this->input->post( 'INACTIVE' );
						$data_post['INACTIVEDATE'] = $this->input->post( 'INACTIVEDATE' );
						$data_post['TERAINTYPE'] = $this->input->post( 'TERAINTYPE' );
						$data_post['TOTALHECTARAGE'] = $this->input->post( 'TOTALHECTARAGE' );
						$data_post['ROLLING'] = $this->input->post( 'ROLLING' );
						$data_post['FLAT'] = $this->input->post( 'FLAT' );
						$data_post['LOWLAND'] = $this->input->post( 'LOWLAND' );
						$data_post['BLOCKID'] = $this->input->post( 'BLOCKID' );

                    //$id = $this->session->flashdata('id');
				    $insert_id = $this->model_m_fieldcrop->update_m_fieldcrop( $id, $data_post );
    
					//redirect( 'M_fieldcrop/info/' . $id );   
    
                }
        
    }
	
	function delete($id)
	{
		$this->model_m_fieldcrop->delete($id);
	}	
	// --------------- script jqquerynya --------------
	
	function read_json_format()
    {
        echo json_encode($this->model_m_fieldcrop->readByPagination());
    }

}

?>