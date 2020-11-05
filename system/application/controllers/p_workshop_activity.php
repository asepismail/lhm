<?
if (!defined('BASEPATH')) exit('No direct script access allowed');

class p_workshop_activity extends Controller 
{
    
	function p_workshop_activity ()
	{
		parent::Controller();	
		$this->load->model( 'model_p_workshop_activity' );         
        $this->load->model('model_c_user_auth');
        $this->lastmenu="p_workshop_activity";
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
		
		$view = "info_p_workshop_activity";
		$data = array();
		$data['judul_header'] = "Buku Catat Workshop";
		$data['js'] = "";
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['WS'] = $this->dropdownlist_ws();
		
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
	
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    }    
	
	function dropdownlist_ws()
    {
    
        $string = "<select  name='kd_ws' class='select'  id='kd_ws' style='width:260px;' onchange='reloadGridWs()' >";
        $string .= "<option value=''> -- Pilih Workshop -- </option>";
        
        $data_afd = $this->model_p_workshop_activity->kode_ws($this->session->userdata('DCOMPANY'));
        
        foreach ( $data_afd as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['WORKSHOPCODE']."\"  selected>".$row['DESCRIPTION']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['WORKSHOPCODE']."\">".$row['DESCRIPTION']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
	
	function grid_p_workshop()
    {
				
		$wc = $this->uri->segment(3);
		$bln = $this->uri->segment(4);
		$thn = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		echo json_encode($this->model_p_workshop_activity->grid_wa($wc, $bln, $thn, $company));
    }
	
    function create_wa ( )
    {
        $kode = $this->input->post( 'KODE_WORKSHOP' );
		$ltc = $this->input->post( 'LOCATION_TYPE_CODE' );
       	$lc = $this->input->post( 'LOCATION_CODE' );
		$ac =  $this->input->post( 'ACTIVITY_CODE' );
		$company = $this->session->userdata('DCOMPANY');					
		$data_post['ID'] = $this->global_func->id_BK('p_workshop_activity','ID', $kode );
		$data_post['KODE_WORKSHOP'] = $kode;
		$data_post['BULAN'] = $this->input->post( 'BULAN' );
		$data_post['TAHUN'] = $this->input->post( 'TAHUN' );
		$data_post['TGL_AKTIVITAS'] = $this->input->post( 'TGL_AKTIVITAS' );
		$data_post['LOCATION_TYPE_CODE'] = $ltc;
		$data_post['ACTIVITY_CODE'] = $this->input->post( 'ACTIVITY_CODE' );
		$data_post['LOCATION_CODE'] = $lc;
		$data_post['JAM_KERJA'] = $this->input->post( 'JAM_KERJA' );
		$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");
		$data_post['COMPANY_CODE'] = $company;
 						
		$data_lokasi = $this->model_p_workshop_activity->lokasi_validate($lc, $ltc, $company);	
		$data_aktivitas = $this->model_p_workshop_activity->aktivitas_validate($ac, $ltc); 
					
		if(strlen($data_post['TGL_AKTIVITAS']) > 20){
			$status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak aktivitas yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
			$status = "mohon tutup kotak kode tipe lokasi yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['LOCATION_CODE']) > 20){
			$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['JAM_KERJA']) > 20){
			$status = "mohon tutup kotak jam kerja yang terbuka  \r\n";  echo $status;
		} 
    	
		$TGL_AKTIVITAS=strval($data_post['TGL_AKTIVITAS']);
		if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
			$status="Tanggal Aktifitas tidak boleh kosong \r\n"; echo $status;
		} else { 
			if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
				$status= "format tanggal tidak sesuai \r\n"; echo $status;
			} else {
				$year_now= $data_post['TAHUN'];//date("Y",time());
				$month_now= $data_post['BULAN'];//date("m",time());
				if(date("Y",strtotime($TGL_AKTIVITAS)) != $year_now || date("m",strtotime($TGL_AKTIVITAS)) != $month_now){
					$status="Tanggal transaksi tidak sama dengan periode berjalan \r\n"; echo $status;
				} 
			}	   
		}
		
		if($ltc == 'GC' || $ltc == 'VH' || $ltc == 'MA' || $ltc == 'SA') {
			if(count($data_lokasi) == 0){ 
					$status = "kode lokasi : ".$lc.", kode kosong atau kode salah!!\r\n"; echo $status;
			} else {
				if(count($data_aktivitas) == 0){ 
					$status = "kode aktivitas : ".$ac.", kode kosong atau kode tidak sesuai!!\r\n";  echo $status;
				} else {
					if(str_replace(" ","", $this->input->post( 'JAM_KERJA' )) == ''){
						$status = "Jam kerja tidak boleh kosong!!\r\n";  echo $status;
					} else if ( str_replace(" ","", $this->input->post( 'JAM_KERJA' ) ) > '24' ){
						$status = "Jam kerja tidak lebih besar dari 24 jam!!\r\n";  echo $status;
					}
				}
			}
		} else {
			$status = "kode tipe lokasi : ".$ltc.", kode kosong atau kode tidak terdapat pada buku workshop!!\r\n";  echo $status;
		}
	
		if(empty($status)){		
			$insert_id = $this->model_p_workshop_activity->insert_p_workshop_activity( $data_post );
		}
   }

    function update_wa( )
    {
        $id = $this->uri->segment(3);
		$kode = $this->input->post( 'KODE_WORKSHOP' );
		$ltc = $this->input->post( 'LOCATION_TYPE_CODE' );
       	$lc = $this->input->post( 'LOCATION_CODE' );
		$ac =  $this->input->post( 'ACTIVITY_CODE' );
		$company = $this->session->userdata('DCOMPANY');
					
		$data_post['KODE_WORKSHOP'] = $this->input->post( 'KODE_WORKSHOP' );
		$data_post['BULAN'] = $this->input->post( 'BULAN' );
		$data_post['TAHUN'] = $this->input->post( 'TAHUN' );
		$data_post['TGL_AKTIVITAS'] = $this->input->post( 'TGL_AKTIVITAS' );
		$data_post['LOCATION_TYPE_CODE'] = str_replace(" ","", $this->input->post( 'LOCATION_TYPE_CODE' ) );
		$data_post['ACTIVITY_CODE'] = str_replace(" ","", $this->input->post( 'ACTIVITY_CODE' ) );
		$data_post['LOCATION_CODE'] = str_replace(" ","", $this->input->post( 'LOCATION_CODE' ) );
		$data_post['JAM_KERJA'] = str_replace(" ","", $this->input->post( 'JAM_KERJA' ));
		$data_post['UPDATE_BY'] = $this->session->userdata('LOGINID');
		$data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");
		$data_post['COMPANY_CODE'] =  $this->session->userdata('DCOMPANY');
		$data_lokasi = $this->model_p_workshop_activity->lokasi_validate($lc, $ltc, $company);	
		$data_aktivitas = $this->model_p_workshop_activity->aktivitas_validate($ac, $ltc); 
					
		if(strlen($data_post['TGL_AKTIVITAS']) > 20){
			$status = "mohon tutup kotak tanggal aktivitas yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak aktivitas yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
			$status = "mohon tutup kotak kode tipe lokasi yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['LOCATION_CODE']) > 20){
			$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['JAM_KERJA']) > 20){
			$status = "mohon tutup kotak jam kerja yang terbuka  \r\n"; echo $status;
		} 
    	
		$TGL_AKTIVITAS=strval($data_post['TGL_AKTIVITAS']);
		if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
			$status="Tanggal Aktifitas tidak boleh kosong \r\n"; echo $status;
		} else { 
			if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
				$status= "format tanggal tidak sesuai \r\n"; echo $status;
			} else {
				$year_now= $data_post['TAHUN'];//date("Y",time());
				$month_now= $data_post['BULAN'];//date("m",time());
				if(date("Y",strtotime($TGL_AKTIVITAS)) != $year_now || date("m",strtotime($TGL_AKTIVITAS)) != $month_now){
					$status="Tanggal transaksi tidak sama dengan periode berjalan \r\n"; echo $status;
				} 
			}	   
		}
		
		if($ltc == 'GC' || $ltc == 'VH' || $ltc == 'MA' || $ltc == 'SA') {
			if(count($data_lokasi) == 0){ 
					$status = "kode lokasi : ".$lc.", kode kosong atau kode salah!!\r\n"; echo $status;
			} else {
				if(count($data_aktivitas) == 0){ 
					$status = "kode aktivitas : ".$ac.", kode kosong atau kode tidak sesuai!!\r\n";  echo $status;
				} else {
					if(str_replace(" ","", $this->input->post( 'JAM_KERJA' )) == ''){
						$status = "Jam kerja tidak boleh kosong!!\r\n";  echo $status;
					} else if ( str_replace(" ","", $this->input->post( 'JAM_KERJA' ) ) > '24' ){
						$status = "Jam kerja tidak lebih besar dari 24 jam!!\r\n";  echo $status;
					}
				}
			}
		} else {
			$status = "kode tipe lokasi : ".$ltc.",kode kosong atau kode tidak terdapat pada buku workshop!!\r\n";  echo $status;
		}
				
		/* if ($lc != '' || $ltc != '' ){
			if ($ac != ''){
			} else {
				$status = "Data tidak boleh kosong !!\r\n";  echo $status;
			}
		} else {
			$status = "Data tidak boleh kosong !!\r\n";  echo $status;
		} */
		
		if(empty($status)){		
			$insert_id = $this->model_p_workshop_activity->update_p_workshop_activity( $id, $company, $data_post );
		}					                 
    }
	
	/* delete */
	function delete()
	{
		$id = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		$this->model_p_workshop_activity->delete_p_workshop_activity($id, $company);
	}
	
	//activity
	function activity(){
		$ac = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$q = $_REQUEST['q'];
		$activity = array();
				
		$data_enroll = $this->model_p_workshop_activity->activity($ac, $q);
		foreach($data_enroll as $row) {
					$activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
		}
		echo '['.implode(',',$activity).']'; exit;
	}
	
	//location code
	function location(){
		$loc = $this->uri->segment(3);
		$q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
		$company = $this->session->userdata('DCOMPANY');
		$data_location = $this->model_p_workshop_activity->location($loc, $q, $company);
		
		$data = array();
		$location = array();
		foreach($data_location as $row){
				$location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
		}
		echo '['.implode(',',$location).']'; exit;
	}
	
	 //Export data
    function create_excel()
    {
        $vc = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $bln = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $thn = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
              
        $data_kendaraan = $this->model_vehicle_activity->gen_kendaraan($vc,$bln,$thn ,$company);
        $judul = '';   $headers = ''; 
		$data = '';  $footer = '';
        
        $obj =& get_instance();
        
        $judul .= htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8'). "\n";
        $judul .= "LAPORAN BUKU KENDARAAN"."\n";
        $judul .= "KODE KENDARAAN : \t".strtoupper($vc)."\n";
        $judul .= "PERIODE : \t".$bln."-".$thn."\n";
        
        $headers .= "TGL_AKTIVITAS \t";
        $headers .= "LOCATION_TYPE_CODE \t";
        $headers .= "LOCATION_CODE \t";
        $headers .= "LOKASI \t";
        $headers .= "ACTIVITY_CODE \t";
        $headers .= "COA DESC \t";

        $headers .= "KMHM_BERANGKAT \t";
        $headers .= "KMHM_KEMBALI \t";
        $headers .= "KMHM_JUMLAH \t";
        $headers .= "JAM_KERJA \t";
           
        $headers .= "MUATAN_JENIS \t";
        $headers .= "MUATAN_VOL \t";
        $headers .= "MUATAN_SAT \t";
        $headers .= "PRESTASI_VOL \t";
        $headers .= "PRESTASI_SAT \t";
        
        foreach ( $data_kendaraan as $row){
            $line = '';
            $line .= str_replace('"', '""',$row['TGL_AKTIVITAS'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_TYPE_CODE'])."\t";
            $line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
            $line .= str_replace('"', '""',$row['LOKASI'])."\t";
            $line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
            $line .= str_replace('"', '""',$row['KMHM_BERANGKAT'])."\t";
            $line .= str_replace('"', '""',$row['KMHM_KEMBALI'])."\t";  
            $line .= str_replace('"', '""',$row['KMHM_JUMLAH'])."\t";
            $line .= str_replace('"', '""',$row['JAM_KERJA'])."\t";
            $line .= str_replace('"', '""',$row['MUATAN_JENIS'])."\t";
            $line .= str_replace('"', '""',$row['MUATAN_VOL'])."\t";
            $line .= str_replace('"', '""',$row['MUATAN_SAT'])."\t";
            $line .= str_replace('"', '""',$row['PRESTASI_VOL'])."\t";
            $line .= str_replace('"', '""',$row['PRESTASI_SAT'])."\t";
            $data .= trim($line)."\n";        
        }
        
        $footer .= " - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t";        
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BK_KENDARAAN_".$company."_".$bln."_".$thn.".xls");
        echo "$judul\n$headers\n$data";  
        
    }
}

?>