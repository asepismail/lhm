<?

class p_progress_infrastruktur extends Controller 
{
    
	function p_progress_infrastruktur ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_infrastruktur' ); 
		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
	}

    function index()
    {
		$view = "info_p_progress_infrastruktur";
		$data = array();
		$data['judul_header'] = "Progress Project Infrastruktur";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    }    

   
    function create( )
    {
          
$data_post['TGL_PROGRESS'] = $this->input->post( 'TGL_PROGRESS' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['ACTIVITY_LOCATION'] = $this->input->post( 'ACTIVITY_LOCATION' );
$data_post['SATUAN'] = $this->input->post( 'SATUAN' );
$data_post['HASIL_KERJA'] = $this->input->post( 'HASIL_KERJA' );
$data_post['REALISASI'] = $this->input->post( 'REALISASI' );
$data_post['HK_PER_SATUAN'] = $this->input->post( 'HK_PER_SATUAN' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

    
                    $insert_id = $this->model_p_progress_infrastruktur->insert_p_progress_infrastruktur( $data_post );
                 
    }

    function edit( $id = '' )
    {
        
	
$data_post['TGL_PROGRESS'] = $this->input->post( 'TGL_PROGRESS' );
$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC' );
$data_post['ACTIVITY_LOCATION'] = $this->input->post( 'ACTIVITY_LOCATION' );
$data_post['SATUAN'] = $this->input->post( 'SATUAN' );
$data_post['HASIL_KERJA'] = $this->input->post( 'HASIL_KERJA' );
$data_post['REALISASI'] = $this->input->post( 'REALISASI' );
$data_post['HK_PER_SATUAN'] = $this->input->post( 'HK_PER_SATUAN' );
$data_post['INPUT_BY'] = $this->input->post( 'INPUT_BY' );
$data_post['INPUT_DATE'] = $this->input->post( 'INPUT_DATE' );
$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );

                    $id = $this->session->flashdata('id');
				    $insert_id = $this->model_p_progress_infrastruktur->update_p_progress_infrastruktur( $id, $data_post );
    
	}
	
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' onchange='' >";
		$string .= "<option value=''> -- choose -- </option>";
		
		$data_afd = $this->model_p_progress_infrastruktur->get_afdeling($this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row)
		{
			if( (isset($default)) && ($default==$row[$nama_isi]) )
			{
				$string = $string." <option value=\"".$row['AFD']."\"  selected>".$row['AFD']." </option>";
			}
			else
			{
				$string = $string." <option value=\"".$row['AFD']."\">".$row['AFD']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	function dropdownlist_block()
	{
		$afd = $_REQUEST['_value'];
		$array = array();
		
		$data_afd = $this->model_p_progress_infrastruktur->get_block($afd,$this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row)
		{
			$array[] = array($row['LOCATION_CODE'] => $row['LOCATION_CODE']);
		}
		
		echo json_encode( $array );
	}
	
	function read_act()
    {
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_lc->read_act($tgl,$lc, $company));
    }

}

?>