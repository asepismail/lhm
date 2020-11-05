<?
if (!defined('BASEPATH')) exit('No direct script access allowed');

class p_machine extends Controller 
{
   	function p_machine ()
	{
		parent::Controller();	
		/*modul yang di load halaman vehicle activity*/
		$this->load->model( 'model_p_machine' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="p_machine";
        
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
		$data = array();
		
		$view = "info_p_machine";
		$data = array();
		$data['judul_header'] = "Buku Catat Meter Mesin";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
	
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}

    }   
	
	function grid_p_machine()
    {
				
		$mc = $this->uri->segment(3);
		$bln = $this->uri->segment(4);
		$thn = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		echo json_encode($this->model_p_machine->grid_ma($mc, $bln, $thn, $company));
		//echo json_encode($this->model_p_machine->grid_ma());
    }
	
	
	function create_ma( )
    {
						$kode = $this->input->post( 'KODE_MESIN' );
						$data_post['ID'] = $this->global_func->id_BK('p_machine_meter','ID', $kode );
						$data_post['KODE_MESIN'] = $kode;
						$data_post['SATUAN_PRESTASI'] = $this->input->post( 'SATUAN_PRESTASI' );
						$data_post['BULAN'] = $this->input->post( 'BULAN' );
						$data_post['TAHUN'] = $this->input->post( 'TAHUN' );
						$data_post['TGL_AKTIVITAS'] = $this->input->post( 'TGL_AKTIVITAS' );
						$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
						$data_post['LOCATION_CODE'] = $this->input->post( 'LOCATION_CODE' );											$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
						$data_post['METER_PEMAKAIAN'] = $this->input->post( 'METER_PEMAKAIAN' );	
						$data_post['JAM_KERJA'] = $this->input->post( 'JAM_KERJA' );	
						$data_post['KETERANGAN'] = $this->input->post( 'KETERANGAN' );	
						$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
						$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");	
						$data_post['COMPANY_CODE'] =  $this->session->userdata('DCOMPANY');
       					
						$ltc = $this->input->post( 'LOCATION_TYPE_CODE' );
       					$lc = $this->input->post( 'LOCATION_CODE' );
						$ac = $this->input->post( 'ACTIVITY_CODE' );
						$data_lokasi = $this->model_p_machine->lokasi_validate($lc, $ltc);	
						$data_aktivitas = $this->model_p_machine->aktivitas_validate($ac, $ltc);	
					
						if(strlen($data_post['TGL_AKTIVITAS']) > 20){
							$status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
							$status = "mohon tutup kotak  kode tipe lokasi yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_CODE']) > 20){
							$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
							$status = "mohon tutup kotak  kode aktivitas yang terbuka  \r\n"; 
							echo $status;
						}  else if(strlen($data_post['METER_PEMAKAIAN']) > 20){
							$status = "mohon tutup kotak meter pemakaian yang terbuka  \r\n"; 
							echo $status;
						}  else if(strlen($data_post['JAM_KERJA']) > 20){
							$status = "mohon tutup kotak Jam kerja yang terbuka  \r\n"; 
							echo $status;
						}
						
						
						if ($ac != '' || $ltc != ''){
								if(count($data_aktivitas) == 0)
								{ 
									$status = "kode aktivitas : ".$ac.", kode salah!!\r\n"; 
									echo $status;
								}
						} else if ($lc != '' || $ltc != ''){
								if(count($data_lokasi) == 0)
								{ 
									$status = "kode lokasi : ".$lc.", kode salah!!\r\n"; 
									echo $status;
								}
						} else if (($ac != "STAND BY" || $ac != "BREAK DOWN") && ord(trim($ltc)) !='45') {
								$data_post['JAM_KERJA']= 0 ;
								$data_post['METER_PEMAKAIAN'] = 0 ;
								$data_post['LOCATION_CODE'] = "-";
						}
						
					if(empty($status)){
						$insert_id = $this->model_p_machine->insert_machine_activity( $data_post );	  
					}
    }
	
	/* update */
	
	function update_ma( )
    {
		$id = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
						$kode = $this->input->post( 'KODE_MESIN' );
						$data_post['ID'] = $this->global_func->id_BK('p_machine_meter','ID', $kode );
						$data_post['KODE_MESIN'] = $kode;
						$data_post['SATUAN_PRESTASI'] = $this->input->post( 'SATUAN_PRESTASI' );
						$data_post['BULAN'] = $this->input->post( 'BULAN' );
						$data_post['TAHUN'] = $this->input->post( 'TAHUN' );
						$data_post['TGL_AKTIVITAS'] = $this->input->post( 'TGL_AKTIVITAS' );
						$data_post['LOCATION_TYPE_CODE'] = $this->input->post( 'LOCATION_TYPE_CODE' );
						$data_post['LOCATION_CODE'] = $this->input->post( 'LOCATION_CODE' );											$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );					
						$data_post['METER_PEMAKAIAN'] = $this->input->post( 'METER_PEMAKAIAN' );	
						$data_post['JAM_KERJA'] = $this->input->post( 'JAM_KERJA' );	
						$data_post['KETERANGAN'] = $this->input->post( 'KETERANGAN' );	
						$data_post['UPDATE_BY'] = $this->session->userdata('LOGINID');
						$data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");	
						$data_post['COMPANY_CODE'] =  $this->session->userdata('DCOMPANY');
       					
						$ltc = $this->input->post( 'LOCATION_TYPE_CODE' );
       					$lc = $this->input->post( 'LOCATION_CODE' );
						$ac = $this->input->post( 'ACTIVITY_CODE' );
						$data_lokasi = $this->model_p_machine->lokasi_validate($lc, $ltc);	
						$data_aktivitas = $this->model_p_machine->aktivitas_validate($ac, $ltc);	
						
						if(strlen($data_post['TGL_AKTIVITAS']) > 20){
							$status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
							$status = "mohon tutup kotak  kode tipe lokasi yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_CODE']) > 20){
							$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; 
							echo $status;
						} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
							$status = "mohon tutup kotak  kode aktivitas yang terbuka  \r\n"; 
							echo $status;
						}  else if(strlen($data_post['METER_PEMAKAIAN']) > 20){
							$status = "mohon tutup kotak meter pemakaian yang terbuka  \r\n"; 
							echo $status;
						}  else if(strlen($data_post['JAM_KERJA']) > 20){
							$status = "mohon tutup kotak Jam kerja yang terbuka  \r\n"; 
							echo $status;
						}
						
						if ($ac != '' || $ltc != ''){
								if(count($data_aktivitas) == 0)
								{ 
									$status = "kode aktivitas : ".$ac.", kode salah!!\r\n"; 
									echo $status;
								}
						} else if ($lc != '' || $ltc != ''){
								if(count($data_lokasi) == 0)
								{ 
									$status = "kode lokasi : ".$lc.", kode salah!!\r\n"; 
									echo $status;
								}
						}
						
					if(empty($status)){
					$insert_id = $this->model_p_machine->update_machine_activity($id,$company, $data_post);
           		 		         
					}
    }
	
	/* delete */
	function delete()
	{
		$id = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$this->model_p_machine->delete_machine_activity($id, $company);
	}
	
	//autocomplete
	function kode_mesin(){
		$company = $this->session->userdata('DCOMPANY');
		$q = $_REQUEST['q'];
		$data_mesin = $this->model_p_machine->kode_mesin($company, $q);
		
		$mesin = array();
		foreach($data_mesin as $row)
			{
				$mesin[] = '{res_id:"'.str_replace('"','\\"',$row['MACHINECODE']).'",res_name:"'.str_replace('"','\\"',$row['DESCRIPTION']).'",res_dl:"'.str_replace('"','\\"',$row['MACHINECODE']. " - " .$row['DESCRIPTION']).'", sat_pres:"'.str_replace('"','\\"',$row['SATUAN_PRESTASI']).'"}';
			}
			  echo '['.implode(',',$mesin).']'; exit; 
	}
	
	//location type
	function location_type(){
		$data_loctype = $this->model_p_machine->location_type();
		
		$loctype = array();
		foreach($data_loctype as $row)
			{
				$loctype[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_TYPE_CODE']).'",res_name:"'.str_replace('"','\\"',				$row['LOCATION_TYPE_CODE']).'"}';
			}
			  echo '['.implode(',',$loctype).']'; exit; 
	}
	
	//location code
	function location(){
		$loc = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$q = $_REQUEST['q'];
		
		
		if($loc == 'PJ'){
       		$data_location = $this->model_p_machine->location_pj($q,$company);
       	} else {
			$data_location = $this->model_p_machine->location($loc, $q, $company);
        	}
		
		$data = array();
		$location = array();
		foreach($data_location as $row)
			{
				$location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
			}
		  echo '['.implode(',',$location).']'; exit;
	}
	
	//activity
	function activity(){
		$ac = $this->uri->segment(3);
		$q = $_REQUEST['q'];
		$data_enroll = $this->model_p_machine->activity($ac, $q);
		$activityMesin = array();
		foreach($data_enroll as $row)
			{
				$activityMesin [] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
			}
		 echo '['.implode(',',$activityMesin ).']'; exit;
	}
}

?>