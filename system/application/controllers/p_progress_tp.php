<?

class p_progress_tp extends Controller 
{
    
	function p_progress_tp ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_tp' ); 
		
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
		$view = "info_p_progress_tp";
		$data = array();
		$data['judul_header'] = "Progress Transport Panen";
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
        echo json_encode($this->model_p_progress_tp->read_act($tgl,$lc, $company));
    }
	
	/* baca data yang sudah ada */
	function read_exist_act()
    {
		$tdate = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_tp->read_exist_act($tdate,$lc, $company));
    }
	
	function cek_tp()
	{
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_p_progress_tp->cek_tp($tgl, $lc, $company);
		$data = "";
		foreach($data_enroll as $row)
			{
				$data .= $row['jumlah'];
			}
		echo $data;
	}
	
    function create_tp( )
    {
        $data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
		
		$company = $this->session->userdata('DCOMPANY');
		$prog_date = $this->input->post( 'TGL_PROGRESS_TP' ); 
		
  			//$this->db->get('m_gang_activity_detail');
				$jumlah = $this->input->post( 'jumlahdt' );
	   				for ($i=1; $i<=$jumlah;$i++) {
					
					$hasil_kerja = ltrim(rtrim($this->input->post( 'HASIL_KERJA'.strval($i))));
					$tglID = str_replace('-','',$prog_date);
					$begID = $company.$tglID;
					
					$afd = substr($this->input->post( 'LOCATION_CODE'.strval($i)),0,2);
					$tgl_progress = $this->input->post( 'TGL_PROGRESS'.strval($i) );
					$activity =  $this->input->post( 'ACTIVITY_CODE'.strval($i) );
					$location = $this->input->post( 'LOCATION_CODE'.strval($i) );
					
					$data_post['ID_PROGRESS_TP'] = $this->global_func->id_GAD('p_progress_tp','ID_PROGRESS_TP',$begID);
					$data_post['AFD'] = $afd;
					$data_post['TGL_PROGRESS'] = $tgl_progress;
					$data_post['ACTIVITY_CODE'] = $activity;
					$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC'.strval($i) );
					$data_post['LOCATION_CODE'] = $location;
					$data_post['SATUAN'] = $this->input->post( 'SATUAN'.strval($i) );
					$data_post['HASIL_KERJA'] = $hasil_kerja;
					
					$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
					$data_post['COMPANY_CODE'] = $company;
    				
					$this->db->from('p_progress_tp');
					$this->db->where('AFD',$afd);
					$this->db->where('TGL_PROGRESS',$tgl_progress);
					$this->db->where('ACTIVITY_CODE',$activity);
					$this->db->where('LOCATION_CODE',$location);
					$this->db->where('COMPANY_CODE',$company);
						
					if ($this->db->count_all_results() == 0) {
						$insert_id = $this->model_p_progress_tp->insert_p_progress_tp( $data_post );
					}  else if ($this->db->count_all_results() != 0) {
						$insert_id = $this->model_p_progress_tp->update_p_progress_tp( $afd,$tgl_progress,$activity,$location,$company, $data_post );
					}
			}
                                    
    }

  
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' onchange='cek_act_block()' >";
		$string .= "<option value=''> -- choose -- </option>";
		
		$data_afd = $this->model_p_progress_tp->get_afdeling($this->session->userdata('DCOMPANY'));
		
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
		
		$data_afd = $this->model_p_progress_tp->get_block($afd,$this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row)
		{
			$array[] = array($row['LOCATION_CODE'] => $row['LOCATION_CODE']);
		}
		
		echo json_encode( $array );
	}
	
	function get_active_block()
	{
		$company = $this->session->userdata('DCOMPANY');
		$periode = substr($this->uri->segment(3),0,6);
		$q = $_REQUEST['q'];
		$data_gang = $this->model_p_progress_tp->active_block($periode,$q,$company);
				
		$gangc = array();
		foreach($data_gang as $row)
			{
				$gangc[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'"}';
			}
		echo '['.implode(',',$gangc).']'; exit; 
	}
			
}

?>