<?

class P_progress_sisip extends Controller 
{
    
	function P_progress_sisip ()
	{
		parent::Controller();	

		$this->load->model( 'model_p_progress_sisip' ); 
		
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
		$view = "info_p_progress_sisip";
		$data = array();
		$data['judul_header'] = "Progress Sisip";
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
        echo json_encode($this->model_p_progress_sisip->read_act($tgl,$lc, $company));
    }
    
	/* baca data yang sudah ada */
	function read_exist_act()
    {
		$tdate = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_p_progress_sisip->read_exist_act($tdate,$lc, $company));
    }
	
	function cek_pts()
	{
		$tgl = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		$data_enroll = $this->model_p_progress_sisip->cek_pts($tgl, $lc, $company);
		$data = "";
		foreach($data_enroll as $row)
			{
				$data .= $row['jumlah'];
			}
		echo $data;
	}
		
	function create_pts( )
    {
	    $data = "";
        foreach ($_POST as $k => $v) {
		$data .= "$k:   $v";
		}
		
		$company = $this->session->userdata('DCOMPANY');
		$prog_date = $this->input->post( 'PROGRESS_DATE' ); 
		
			$jumlah = $this->input->post( 'jumlahdt' );
	   			for ($i=1; $i<=$jumlah;$i++) {
					
					
						$hasil_kerja = ltrim(rtrim($this->input->post( 'HASIL_KERJA'.strval($i))));
						
						$tglID = str_replace('-','',$prog_date);
						$begID = $company.$tglID;
						
						$afd = substr($this->input->post( 'LOCATION_CODE'.strval($i)),0,2);
						$tgl_progress = $this->input->post( 'TGL_PROGRESS_SISIP'.strval($i) );
						$activity =  $this->input->post( 'ACTIVITY_CODE'.strval($i) );
						$location = $this->input->post( 'LOCATION_CODE'.strval($i) );
						
						$data_post['ID_PROGRESS_SISIP'] = $this->global_func->id_GAD('p_progress_sisip','ID_PROGRESS_SISIP',$begID);
						$data_post['AFD'] = $afd;
						$data_post['TGL_PROGRESS'] = $this->input->post( 'TGL_PROGRESS_SISIP'.strval($i) );
						$data_post['ACTIVITY_CODE'] = $activity;
						$data_post['ACTIVITY_DESC'] = $this->input->post( 'ACTIVITY_DESC'.strval($i) );
						$data_post['LOCATION_CODE'] = $location;
						$data_post['SATUAN'] = $this->input->post( 'SATUAN'.strval($i) );
						$data_post['HASIL_KERJA'] = $this->input->post( 'HASIL_KERJA'.strval($i) );
						$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
						$data_post['COMPANY_CODE'] = $company;

    					$this->db->from('p_progress_sisip');
						$this->db->where('AFD',$afd);
						$this->db->where('TGL_PROGRESS',$tgl_progress);
						$this->db->where('ACTIVITY_CODE',$activity);
						$this->db->where('LOCATION_CODE',$location);
						$this->db->where('COMPANY_CODE',$company);
						
						if ($this->db->count_all_results() == 0) {
                    		$insert_id = $this->model_p_progress_sisip->insert_p_progress_sisip( $data_post );
						} else if ($this->db->count_all_results() != 0) {
							$insert_id = $this->model_p_progress_sisip->update_p_progress_sisip( $afd,$tgl_progress,$activity,$location,$company, $data_post );	
						}
				}			
		                
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
				    $insert_id = $this->model_p_progress_sisip->update_p_progress_sisip( $id, $data_post );
                     
    }
	
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' onchange='cek_act_block()' >";
		$string .= "<option value=''> -- choose -- </option>";
		
		$data_afd = $this->model_p_progress_sisip->get_afdeling($this->session->userdata('DCOMPANY'));
		
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
		
		$data_afd = $this->model_p_progress_sisip->get_block($afd,$this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row)
		{
			$array[] = array($row['LOCATION_CODE'] => $row['LOCATION_CODE']);
		}
		
		echo json_encode( $array );
	}
	
	//add data
	function get_active_act()
	{
		$company = $this->session->userdata('DCOMPANY');
		$afd = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
	
		$q = $_REQUEST['q'];
		
		$data_rawat = $this->model_p_progress_sisip->act($afd, $q, $tgl, $company);
				
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
		$afd = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
		$act = $this->uri->segment(5);
	
		$q = $_REQUEST['q'];
		
		$data_rawat = $this->model_p_progress_sisip->blok($afd, $q, $act, $tgl, $company);
				
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
		
		$this->model_p_progress_sisip->delete_data($id,$afd,$tgl,$company);
	}
	
}

?>