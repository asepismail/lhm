<?

class P_kontrak_progress extends Controller 
{
    
	function P_kontrak_progress ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_kontrak_progress' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	}

    function index()
    {
		echo anchor( 'P_kontrak_progress/enroll/', 'Enroll (list)' );
		echo '<br>';
    }    

    function info( $id )
    {

		$data_info = $this->model_p_kontrak_progress->info_p_kontrak_progress( $id );
		
		$data['data_info'] = $data_info;
		$data['id'] = $id;
		
		$this->load->view( 'info_p_kontrak_progress.php', $data ); 
    }

	function enroll( )
	{
		$data_enroll = $this->model_p_kontrak_progress->enroll_p_kontrak_progress( );
		//$data['data_enroll'] = $data_enroll;
		
		echo '<pre>';
		print_r( $data_enroll );
		echo '</pre>';
		
		echo anchor( 'P_kontrak_progress/create', 'Create New' );		
	}

    function create( )
    {
        
		$this->load->library('form_validation');   
        
		switch ( $_SERVER ['REQUEST_METHOD'] ) 
        {
    
            case 'GET':
    
$data['values']['TGL_KONTRAK'] = '';
$data['values']['ID_KONTRAKTOR'] = '';
$data['values']['LOCATION_TYPE_CODE'] = '';
$data['values']['LOCATION_CODE'] = '';
$data['values']['LOCATION_DESC'] = '';
$data['values']['ACTIVITY_CODE'] = '';
$data['values']['ACTIVITY_DESC'] = '';
$data['values']['HSL_SATUAN'] = '';
$data['values']['HSL_VOLUME'] = '';
$data['values']['TARIF_SATUAN'] = '';
$data['values']['NILAI'] = '';
$data['values']['COMPANY_CODE'] = '';

    
				$data['form_mode'] = 'create';
                $this->load->view( 'create_p_kontrak_progress.php', $data );  
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'TGL_KONTRAK', lang('TGL_KONTRAK'), 'required' );
$this->form_validation->set_rules( 'ID_KONTRAKTOR', lang('ID_KONTRAKTOR'), 'required' );
$this->form_validation->set_rules( 'LOCATION_TYPE_CODE', lang('LOCATION_TYPE_CODE'), 'required' );
$this->form_validation->set_rules( 'LOCATION_CODE', lang('LOCATION_CODE'), 'required' );
$this->form_validation->set_rules( 'LOCATION_DESC', lang('LOCATION_DESC'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_CODE', lang('ACTIVITY_CODE'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_DESC', lang('ACTIVITY_DESC'), 'required' );
$this->form_validation->set_rules( 'HSL_SATUAN', lang('HSL_SATUAN'), 'required' );
$this->form_validation->set_rules( 'HSL_VOLUME', lang('HSL_VOLUME'), 'required' );
$this->form_validation->set_rules( 'TARIF_SATUAN', lang('TARIF_SATUAN'), 'required' );
$this->form_validation->set_rules( 'NILAI', lang('NILAI'), 'required' );
$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
    
$data['values']['TGL_KONTRAK'] = set_value( 'TGL_KONTRAK' );
$data['values']['ID_KONTRAKTOR'] = set_value( 'ID_KONTRAKTOR' );
$data['values']['LOCATION_TYPE_CODE'] = set_value( 'LOCATION_TYPE_CODE' );
$data['values']['LOCATION_CODE'] = set_value( 'LOCATION_CODE' );
$data['values']['LOCATION_DESC'] = set_value( 'LOCATION_DESC' );
$data['values']['ACTIVITY_CODE'] = set_value( 'ACTIVITY_CODE' );
$data['values']['ACTIVITY_DESC'] = set_value( 'ACTIVITY_DESC' );
$data['values']['HSL_SATUAN'] = set_value( 'HSL_SATUAN' );
$data['values']['HSL_VOLUME'] = set_value( 'HSL_VOLUME' );
$data['values']['TARIF_SATUAN'] = set_value( 'TARIF_SATUAN' );
$data['values']['NILAI'] = set_value( 'NILAI' );
$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'create'; 
                    $this->load->view( 'create_p_kontrak_progress.php', $data );
                }
                elseif ( $this->form_validation->run() == TRUE ) 
                {
    
$data_post['TGL_KONTRAK'] = $this->input->post( 'TGL_KONTRAK' );
$data_post['ID_KONTRAKTOR'] = $this->input->post( 'ID_KONTRAKTOR' );
$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
$data_post['LOCATION_CODE'] = $this->input->post( 'LOCATION_CODE' );
$data_post['LOCATION_DESC'] = $this->input->post( 'LOCATION_DESC' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['HSL_SATUAN'] = $this->input->post( 'HSL_SATUAN' );
$data_post['HSL_VOLUME'] = $this->input->post( 'HSL_VOLUME' );
$data_post['TARIF_SATUAN'] = $this->input->post( 'TARIF_SATUAN' );
$data_post['NILAI'] = $this->input->post( 'NILAI' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

    
                    $insert_id = $this->model_p_kontrak_progress->insert_p_kontrak_progress( $data_post );
                    
					redirect( 'P_kontrak_progress/info/' . $insert_id );
    
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
    
$data['values'] = $this->model_p_kontrak_progress->Info_p_kontrak_progress( $id );

                
				$this->session->set_flashdata( 'id', $id );

				$data['form_mode'] = 'edit';
                $this->load->view( 'edit_p_kontrak_progress.php', $data );   
    
            break;
    
            case 'POST':
    
                /* we set the rules */
                /* dont forget to edit these */
$this->form_validation->set_rules( 'TGL_KONTRAK', lang('TGL_KONTRAK'), 'required' );
$this->form_validation->set_rules( 'ID_KONTRAKTOR', lang('ID_KONTRAKTOR'), 'required' );
$this->form_validation->set_rules( 'LOCATION_TYPE_CODE', lang('LOCATION_TYPE_CODE'), 'required' );
$this->form_validation->set_rules( 'LOCATION_CODE', lang('LOCATION_CODE'), 'required' );
$this->form_validation->set_rules( 'LOCATION_DESC', lang('LOCATION_DESC'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_CODE', lang('ACTIVITY_CODE'), 'required' );
$this->form_validation->set_rules( 'ACTIVITY_DESC', lang('ACTIVITY_DESC'), 'required' );
$this->form_validation->set_rules( 'HSL_SATUAN', lang('HSL_SATUAN'), 'required' );
$this->form_validation->set_rules( 'HSL_VOLUME', lang('HSL_VOLUME'), 'required' );
$this->form_validation->set_rules( 'TARIF_SATUAN', lang('TARIF_SATUAN'), 'required' );
$this->form_validation->set_rules( 'NILAI', lang('NILAI'), 'required' );
$this->form_validation->set_rules( 'COMPANY_CODE', lang('COMPANY_CODE'), 'required' );

    
                if ( $this->form_validation->run() == FALSE )
                {
                    $this->session->keep_flashdata('id');
$data['values']['TGL_KONTRAK'] = set_value( 'TGL_KONTRAK' );
$data['values']['ID_KONTRAKTOR'] = set_value( 'ID_KONTRAKTOR' );
$data['values']['LOCATION_TYPE_CODE'] = set_value( 'LOCATION_TYPE_CODE' );
$data['values']['LOCATION_CODE'] = set_value( 'LOCATION_CODE' );
$data['values']['LOCATION_DESC'] = set_value( 'LOCATION_DESC' );
$data['values']['ACTIVITY_CODE'] = set_value( 'ACTIVITY_CODE' );
$data['values']['ACTIVITY_DESC'] = set_value( 'ACTIVITY_DESC' );
$data['values']['HSL_SATUAN'] = set_value( 'HSL_SATUAN' );
$data['values']['HSL_VOLUME'] = set_value( 'HSL_VOLUME' );
$data['values']['TARIF_SATUAN'] = set_value( 'TARIF_SATUAN' );
$data['values']['NILAI'] = set_value( 'NILAI' );
$data['values']['COMPANY_CODE'] = set_value( 'COMPANY_CODE' );

                    $data['form_mode'] = 'edit'; 
                    $this->load->view( 'edit_p_kontrak_progress.php', $data ); 
                }
                elseif ( $this->form_validation->run() == TRUE )  
                {
    
$data_post['TGL_KONTRAK'] = $this->input->post( 'TGL_KONTRAK' );
$data_post['ID_KONTRAKTOR'] = $this->input->post( 'ID_KONTRAKTOR' );
$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
$data_post['LOCATION_CODE'] = $this->input->post( 'LOCATION_CODE' );
$data_post['LOCATION_DESC'] = $this->input->post( 'LOCATION_DESC' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['HSL_SATUAN'] = $this->input->post( 'HSL_SATUAN' );
$data_post['HSL_VOLUME'] = $this->input->post( 'HSL_VOLUME' );
$data_post['TARIF_SATUAN'] = $this->input->post( 'TARIF_SATUAN' );
$data_post['NILAI'] = $this->input->post( 'NILAI' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_p_kontrak_progress->update_p_kontrak_progress( $id, $data_post );
    
					redirect( 'P_kontrak_progress/info/' . $id );   
    
                }
    
    
            break;
    		
    
            default:
            break;
        }

                 
    }

}

?>