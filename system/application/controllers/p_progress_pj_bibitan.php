<?

class p_progress_pj_bibitan extends Controller 
{
    
	function p_progress_pj_bibitan ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_pj_bibitan' ); 
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
		$view = "info_p_progress_pj_bibitan";
		$data = array();
		$data['judul_header'] = "Progress Project Bibitan";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    }    
	
	function read_act()
    {
		$tgl = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_pj_bibitan->read_act($tgl,$company));
    }
	
	/* baca data yang sudah ada */
	function read_exist_act()
    {
		$tdate = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_pj_bibitan->read_exist_act($tdate,$lc, $company));
    }
	
	function cek_pb()
	{
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_p_progress_pj_bibitan->cek_pb($tgl, $lc, $company);
		$data = "";
		foreach($data_enroll as $row)
			{
				$data .= $row['jumlah'];
			}
		echo $data;
	}
	
	function create_pb()
    {
		$data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
	   	
		$company = $this->session->userdata('DCOMPANY');
		$prog_date = $this->input->post( 'PROGRESS_TGL_BIBITAN' ); 
		
  			
				$jumlah = $this->input->post( 'jumlahdt' );
	   				for ($i=1; $i<=$jumlah;$i++) {
						
						$tgl_progress = $this->input->post( 'TGL_PROGRESS'.strval($i) );
						$activity =  $this->input->post( 'ACTIVITY_CODE'.strval($i) );
						$location = $this->input->post( 'LOCATION_CODE'.strval($i) );
						
						$hasil_kerja = ltrim(rtrim($this->input->post( 'HASIL_KERJA'.strval($i))));
						$tglID = str_replace('-','',$prog_date);
						$begID = $company.$tglID;
					$data_post['ID_PROGRESS_PJBIBITAN'] = $this->global_func->id_GAD('p_progress_pjbibitan','ID_PROGRESS_PJBIBITAN',$begID);
						
						$data_post['TGL_PROGRESS'] = $tgl_progress;
 						$data_post['LOCATION_CODE'] = $location;
						$data_post['ACTIVITY_CODE'] = $activity;
						$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC'.strval($i) );
						$data_post['HASIL_KERJA'] = $hasil_kerja;
						$data_post['SATUAN'] = $this->input->post( 'SATUAN'.strval($i) );
						
						$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
						$data_post['COMPANY_CODE'] = $company;
						
						$this->db->from('p_progress_pjbibitan');
						$this->db->where('TGL_PROGRESS',$tgl_progress);
						$this->db->where('ACTIVITY_CODE',$activity);
						$this->db->where('LOCATION_CODE',$location);
						$this->db->where('COMPANY_CODE',$company);
						
						if ($this->db->count_all_results() == 0) {
					$insert_id = $this->model_p_progress_pj_bibitan->insert_p_progress_pj_bibitan( $data_post );	
						}  else if ($this->db->count_all_results() != 0) {
							$insert_id = $this->model_p_progress_pj_bibitan->update_p_progress_pj_bibitan( $tgl_progress,$activity,$location,$company, $data_post );
						}
				}			
    	}
		
			//add data
	function get_active_act()
	{
		$company = $this->session->userdata('DCOMPANY');
		$tgl = $this->uri->segment(3);
	
		$q = $_REQUEST['q'];
		
		$data_rawat = $this->model_p_progress_pj_bibitan->act($q, $tgl, $company);
				
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
		
		$data_rawat = $this->model_p_progress_pj_bibitan->blok($q, $act, $tgl, $company);
				
		$rawat = array();
		foreach($data_rawat as $row)
		{
			$rawat[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'"}';
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
		
		$this->model_p_progress_pj_bibitan->delete_data($id,$afd,$tgl,$company);
	}
}

?>