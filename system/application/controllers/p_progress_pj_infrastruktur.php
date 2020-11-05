<?

class p_progress_pj_infrastruktur extends Controller 
{
    
	function p_progress_pj_infrastruktur ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_pj_infrastruktur' ); 
		
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
		$view = "info_p_progress_pj_infrastruktur";
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
	
	function read_act()
    {
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_pj_infrastruktur->read_act($tgl,$lc, $company));
    }
	
	/* baca data yang sudah ada */
	function read_exist_act()
    {
		$tdate = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_pj_infrastruktur->read_exist_act($tdate,$lc, $company));
    }
	
	 function create_prif( )
    {
        $data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
		
		$company = $this->session->userdata('DCOMPANY');
		$prog_date = $this->input->post( 'PROGRESS_DATE' ); 
		
  			//$this->db->get('m_gang_activity_detail');
				$jumlah = $this->input->post( 'jumlahdt' );
	   				for ($i=1; $i<=$jumlah;$i++) {
					
					$hasil_kerja = ltrim(rtrim($this->input->post( 'HASIL_KERJA'.strval($i))));
					$tglID = str_replace('-','',$prog_date);
					$begID = $company.$tglID;
					
					$tgl_progress = $this->input->post( 'TGL_PROGRESS'.strval($i) );
					$activity =  $this->input->post( 'ACTIVITY_CODE'.strval($i) );
					$location = $this->input->post( 'LOCATION_CODE'.strval($i) );
					
					$data_post['ID_PROGRESS_INFRASTRUKTUR'] = $this->global_func->id_GAD('p_progress_infrastruktur','ID_PROGRESS_INFRASTRUKTUR',$begID);
					$data_post['TGL_PROGRESS'] = $tgl_progress;
					$data_post['ACTIVITY_CODE'] = $activity;
					$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC'.strval($i) );
					$data_post['PJ_LOCATION'] = $this->input->post( 'PJ_LOCATION'.strval($i) );
					$data_post['AFD'] = $this->input->post( 'AFD'.strval($i) );
					$data_post['LOCATION_CODE'] = $location;
					$data_post['SATUAN'] = $this->input->post( 'SATUAN'.strval($i) );
					$data_post['HASIL_KERJA'] = $hasil_kerja;
					
					$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
					$data_post['COMPANY_CODE'] = $company;
    				
					$this->db->from('p_progress_infrastruktur');
					$this->db->where('TGL_PROGRESS',$tgl_progress);
					$this->db->where('ACTIVITY_CODE',$activity);
					$this->db->where('LOCATION_CODE',$location);
					$this->db->where('COMPANY_CODE',$company);
					
					if ($this->db->count_all_results() == 0) {
						$insert_id = $this->model_p_progress_pj_infrastruktur->insert_p_progress_pj_infrastruktur( $data_post );				
					} else if ($this->db->count_all_results() != 0) {
						$insert_id = $this->model_p_progress_pj_infrastruktur->update_p_progress_pj_infrastruktur($tgl_progress,$activity,$location,$company, $data_post );
					}
					
                    
			}
                                    
    }
	
	function cek_prif()
	{
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_p_progress_pj_infrastruktur->cek_if($tgl, $lc, $company);
		$data = "";
		foreach($data_enroll as $row)
			{
				$data .= $row['jumlah'];
			}
		echo $data;
	}
	
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' onchange='cek_act_block()' >";
		$string .= "<option value=''> -- choose -- </option>";
		
		$data_afd = $this->model_p_progress_pj_infrastruktur->get_afdeling($this->session->userdata('DCOMPANY'));
		
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
	
		//add data
	function get_active_act()
	{
		$company = $this->session->userdata('DCOMPANY');
		$loc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
	
		$q = $_REQUEST['q'];
		
		$data_rawat = $this->model_p_progress_pj_infrastruktur->act($loc, $q, $tgl, $company);
				
		$rawat = array();
		foreach($data_rawat as $row)
		{
			$rawat[] = '{res_id:"'.str_replace('"','\\"',$row['ACTIVITY_CODE']).'",res_name:"'.str_replace('"','\\"',$row['DESCR']).'",
			res_d:"'.str_replace('"','\\"',$row['ACTIVITY_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCR']).'",res_unit:"'.str_replace('"','\\"',$row['UNIT1']).'"}';
		}
		echo '['.implode(',',$rawat).']'; exit; 
	}
	
	function get_active_loc()
	{
		$company = $this->session->userdata('DCOMPANY');
		$tgl = $this->uri->segment(3);
		$act = $this->uri->segment(4);
	
		$q = $_REQUEST['q'];
		
		$data_rawat = $this->model_p_progress_pj_infrastruktur->blok($q, $act, $tgl, $company);
				
		$rawat = array();
		foreach($data_rawat as $row)
		{
			$rawat[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_pjl:"'.str_replace('"','\\"',$row['PJ_LOCATION']).'",
			res_afd:"'.str_replace('"','\\"',$row['AFD']).'"}';
		}
		echo '['.implode(',',$rawat).']'; exit; 
	}
	
	//hapus progress
	function delete()
	{
		$company = $this->session->userdata('DCOMPANY');
			
		$afd = $this->input->post( 'AFD' );
		$id = $this->input->post( 'ID' );
		$tgl = $this->input->post( 'TGL' );
		
		$this->model_p_progress_pj_infrastruktur->delete_data($id,$afd,$tgl,$company);
	}
}

?>