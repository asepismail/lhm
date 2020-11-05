<?
if (!defined('BASEPATH')) exit('No direct script access allowed');

class m_gang_activity_detail extends Controller 
{
    
    function m_gang_activity_detail ()
    {
        parent::Controller();    
        $this->load->model( 'model_m_gang_activity_detail' ); 
        $this->load->model( 'model_m_gang_activity' );
        $this->load->model('model_c_user_auth');
        $this->lastmenu="m_gang_activity_detail";
        $this->load->helper('form');
        $this->load->helper('language');
        $this->load->database(); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->plugin('to_excel');
		require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
    }

    function index()
    {
        $view = "info_m_gang_activity_detail";
        $data = array();
        $data['judul_header'] = "Laporan Harian Mandor";
        $data['js'] = "";
		
		$company=htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$user = htmlentities($this->session->userdata('LOGINID'));
		
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'));
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'));
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['GANG_CODE'] = $this->global_func->dropdownlist_gangcode('GANG_CODE','CheckAfdeling()',$company,$user); 
$data['GC_TO'] = $this->global_func->dropdownlist2("GC_TO","m_gang","GANG_CODE","GANG_CODE",NULL,NULL, NULL,NULL,"select");
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            show($view, $data);
        } else {
            redirect('login');
        }
    }    

    /* grid utama */
    function read_grid()
    {
        echo json_encode($this->model_m_gang_activity_detail->grid());
    }
    
    /* baca data yang sudah ada */
    function read_exist_gad()
    {
        $tdate = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gang_activity_detail->read_exist_gad($tdate,$gc, $company));
    }

    /*look up*/
    function cek_mandor(){
        
        $gc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        $data_enroll = $this->model_m_gang_activity_detail->cek_exist_mandor($gc, $tgl, $company);
		//$data_enroll = $this->model_m_gang_activity_detail->cek_mandor($gc, $company);
        $data = array();
		
		if(count($data_enroll) == 0)
        { 
        	$data_enroll = $this->model_m_gang_activity_detail->cek_mandor($gc, $company);
        	foreach($data_enroll as $row)
            {
                $data[] = array("name"=>($row['DIVISION_CODE']),"mandore"=>($row['MANDORE_CODE']),"nm_kemandoran"=>($row['DESCRIPTION']),"kerani"=>($row['KERANI_CODE']),"nm_m"=>($row['NM_MANDOR']),"nm_k"=>($row['NM_KERANI']),"nm_m1"=>($row['NM_MANDOR1']),"mandore1"=>($row['MANDORE1_CODE']) );
            }
		} else {
			foreach($data_enroll as $row)
            {
                $data[] = array("name"=>($row['DIVISION_CODE']),"mandore"=>($row['MANDORE_CODE']),"nm_kemandoran"=>($row['DESCRIPTION']),"kerani"=>($row['KERANI_CODE']),"nm_m"=>($row['NM_MANDOR']),"nm_k"=>($row['NM_KERANI']),"nm_m1"=>($row['NM_MANDOR1']),"mandore1"=>($row['MANDORE1_CODE']) );
            }
		}
        $storeData = json_encode($data);
        echo $storeData;
        



    }
        
    function cek_anggota(){
        $tgl = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gang_activity_detail->cek_anggota($tgl, $gc, $company));
    }
    
    function cek_anggota_pinjam(){
        $tgl = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gang_activity_detail->cek_anggota_pinjam($tgl, $gc, $company));
    }
    
    
    /*lookup combo di grid*/
    function kode_absen(){
        $data_enroll = $this->model_m_gang_activity_detail->kode_absen();
        $data = "";
        foreach($data_enroll as $row)
            {
                $data .= $row['TYPE_ABSENSI'].":".$row['TYPE_ABSENSI'].";";
            }
        $len = strlen($data);
        $datas = substr($data,0, $len-1);
        echo json_encode($datas);
    }
    
    function type_absensi(){
    
    $q = $_REQUEST["q"]; 
    
    $data_absen = $this->model_m_gang_activity_detail->type_absensi($q);
        
        $absensi = array();
        foreach($data_absen as $row)
            {
                $absensi[] = '{res_id:"'.str_replace('"','\\"',$row['TYPE_ABSENSI']).'",res_name:"'.str_replace('"','\\"',$row['DESCRIPTION']).'",res_dl:"'.str_replace('"','\\"',$row['TYPE_ABSENSI']. " - " .$row['DESCRIPTION']).'"}';
            }
              echo '['.implode(',',$absensi).']'; exit; 
    }
    
    function location_type(){
        $ltc = $_REQUEST['q'];
        $data_loctype = $this->model_m_gang_activity_detail->location_type($ltc);
        
        $loctype = array();
        foreach($data_loctype as $row)
            {
                $loctype[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_TYPE_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_TYPE_CODE']).'"}';
            }
              echo '['.implode(',',$loctype).']'; exit; 
    }
    
    function satuan(){
        $q = $_REQUEST['q'];
        $data_satuan = $this->model_m_gang_activity_detail->satuan($q);
        
        $satuan = array();
        foreach($data_satuan as $row)
            {
                $satuan[] = '{res_id:"'.str_replace('"','\\"',$row['UNIT_CODE']).'",res_name:"'.str_replace('"','\\"',$row['UNIT_DESC']).'",res_dl:"'.str_replace('"','\\"',$row['UNIT_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['UNIT_DESC']).'"}';
            }
              echo '['.implode(',',$satuan).']'; exit; 
    }
    
    function gangc(){
        $company = $this->session->userdata('DCOMPANY');
        $data_gang = $this->model_m_gang_activity_detail->gangc($company);
        
        $gangc = array();
        foreach($data_gang as $row)
            {
                $gangc[] = '{res_id:"'.str_replace('"','\\"',$row['GANG_CODE']).'",res_name:"'.str_replace('"','\\"',$row['DESCRIPTION']).'",res_dl:"'.str_replace('"','\\"',$row['GANG_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
            }
              echo '['.implode(',',$gangc).']'; exit; 
    }
    
    function search_nik(){
        $company = $this->session->userdata('DCOMPANY');
        $nik_s = $_REQUEST['q'];
        $data_gang = $this->model_m_gang_activity_detail->search_nik($nik_s,$company);
        
        
        $gangc = array();
        foreach($data_gang as $row)
            {
                $gangc[] = '{res_id:"'.str_replace('"','\\"',$row['NIK']).'",res_name:"'.str_replace('"','\\"',$row['NAMA']).'",res_dl:"'.str_replace('"','\\"',$row['NIK']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['NAMA']).'"}';
            }
              echo '['.implode(',',$gangc).']'; exit; 
    }
    
    function location(){
		
        $q = $_REQUEST['q'];
		$qs = str_replace(array("\r", "\r\n", "\n", " ", '&nbsp;', '&amp;'), '', $q);
        $loc = $this->uri->segment(3);
        
        $company = $this->session->userdata('DCOMPANY');
        
        $data = array();
        $location = array();
        
        if($loc == 'PJ'){
            $data_location = $this->model_m_gang_activity_detail->location_pj($qs,$company);
            foreach($data_location as $row)
            {
                $location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
            }
        } else {
            $data_location = $this->model_m_gang_activity_detail->location($q, $loc, $company);
            foreach($data_location as $row)
                {
                    $location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
                }
        }
        
        echo '['.implode(',',$location).']'; exit;
    }
    
    function activity_pn()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');   
        $data_enroll = $this->model_m_gang_activity_detail->activity_pn($company);
        $activity = array(); 
        
        foreach($data_enroll as $row)
        {
            $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
            '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
            '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
        }
        echo '['.implode(',',$activity).']'; exit;
    }
	
    function activity(){
        $lt = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $loc_code = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $ac = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');        
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $activity = array();    
                    
        if($lt == 'PJ'){ 
        $lc = substr($loc_code,0,strlen($loc_code)-2);
        $project_subtype = substr($loc_code,-2);
        
        $data_enroll = '';
        
        if($project_subtype == "TN" || $project_subtype == "LC") {
            $data_enroll = $this->model_m_gang_activity_detail->activity_pj_lctn($ac,$project_subtype);    
        }
         else {
            $data_enroll = $this->model_m_gang_activity_detail->activity_pj($ac,$loc_code,$company);
        }
        
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            } 
          
        }
	 elseif(strtoupper($lt)=='OP')  /* validasi untuk aktivitas yang OP tanam */
		{ 
			$data_enroll = '';
			$cek = $this->model_m_gang_activity_detail->cek_blokTanam($loc_code, $company);
			if($cek > 0){
				 $data_enroll = $this->model_m_gang_activity_detail->aktivitasBlokTanam($ac, $lt);
			} else {
				 $data_enroll = $this->model_m_gang_activity_detail->activity($ac, $lt);
			}
			
			if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
                }
                echo '['.implode(',',$activity).']'; exit;    
            } 
	 }	
        elseif(strtoupper($lt)=='VH') 
        { 
            if(strtoupper(substr($loc_code,-1))=='R')
            {
                $data_enroll = $this->model_m_gang_activity_detail->activity_vh($ac, $lt);    
            }
            else
			{
                $data_enroll = $this->model_m_gang_activity_detail->activity($ac, $lt);    
            }
            
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
                }
                echo '['.implode(',',$activity).']'; exit;    
            }      
        }
		
		elseif(strtoupper($lt)=='SA') 
        { 
           
            $data_enroll = $this->model_m_gang_activity_detail->activity_sa();    
           
            
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
                }
                echo '['.implode(',',$activity).']'; exit;    
            }      
        }
        else {
			
            $data_enroll = $this->model_m_gang_activity_detail->activity($ac, $lt);
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                    $activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']."&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
                }
                echo '['.implode(',',$activity).']'; exit;    
            }  
        }
        
    }
    /*fungsi insert data ke 3 tabel : M_gang_activity_detail, M_gang_activity, dan m_kehadiran*/
    
    function create_gad( )
    {
        
        $data ="";
        foreach ($_POST as $k => $v) {
        $data .= "$k:   $v";
        }
		
        $company = $this->session->userdata('DCOMPANY');
        $gangcode = ltrim(rtrim(strtoupper($this->input->post( 'GANG_CODE' ) ) ) ); 
		$mandor1 = ltrim(rtrim(strtoupper($this->input->post( 'MANDORE1_CODE' ) ) ) ); 
		$mandor = ltrim(rtrim(strtoupper($this->input->post( 'MANDORE_CODE' ) ) ) ); 
		$kerani = ltrim(rtrim(strtoupper($this->input->post( 'KERANI_CODE' ) ) ) ); 
		
        $lhm_date = $this->input->post( 'LHM_DATE' ); 
        $status_entri =  $this->input->post( 'STATUS_POST' );
     	$periode = substr(str_replace("-","",$lhm_date),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			$status = "Periode transaksi bulan ini sudah diclose..";
			echo $status;
		} else {
			/* start cek closing weekly */
			$numWeek = ceil( date( 'j', strtotime( $lhm_date ) ) / 7 ); 
			$cekClosingWeekly = $this->global_func->cekClosingTransaksi('LHM', $lhm_date, $company);
			if($cekClosingWeekly == '1'){
				$status = "Transaksi tanggal " . $lhm_date ." ini sudah ditutup..";
				echo $status;
			} else { 

				$jumlah = $this->input->post( 'jumlah' );
				for ($i=1; $i<=$jumlah;$i++) {
						$type_absen = ltrim(rtrim(strtoupper($this->input->post( 'TYPE_ABSENSI'.strval($i) ))));
						$ltc = ltrim(rtrim(strtoupper($this->input->post( 'LOCATION_TYPE_CODE'.strval($i) ))));
						$lc = ltrim(rtrim(strtoupper($this->input->post( 'LOCATION_CODE'.strval($i)))));
						$ac = ltrim(rtrim(strtoupper($this->input->post( 'ACTIVITY_CODE'.strval($i)))));
						$hk = ltrim(rtrim($this->input->post( 'HK_JUMLAH'.strval($i) )));
						$hks = str_replace(array("\r", "\r\n", "\n", " ", '&nbsp;', '&amp;'), '', $hk);
						$loc_code = substr($lc,0,strlen($lc)-2);
						$project_subtype = substr($lc,-2);
				
						$data_post['GANG_CODE'] = $this->input->post( 'GANG_CODE'.strval($i) );
						$data_post['LHM_DATE'] = $this->input->post( 'LHM_DATE'.strval($i) );
						$data_post['EMPLOYEE_CODE'] = $this->input->post( 'NIK'.strval($i) );
						$data_post['MANDORE1_CODE'] = $mandor1;
						$data_post['MANDORE_CODE'] = $mandor;
						$data_post['KERANI_CODE'] = $kerani;
						$data_post['TYPE_ABSENSI'] = $type_absen;
						$data_post['LOCATION_TYPE_CODE'] = $ltc;
						$data_post['LOCATION_CODE'] = $lc;
						$data_post['ACTIVITY_CODE'] = $ac;
						$data_post['HK_JUMLAH'] = $hks;
						$data_post['HSL_KERJA_UNIT'] = $this->input->post( 'HSL_KERJA_UNIT'.strval($i) );
						$data_post['HSL_KERJA_VOLUME'] = $this->input->post( 'HSL_KERJA_VOLUME'.strval($i) );
						$data_post['TARIF_SATUAN'] = $this->input->post( 'TARIF_SATUAN'.strval($i) );
						$data_post['PREMI'] = $this->input->post( 'PREMI'.strval($i) );
						$data_post['NOTE'] = $this->input->post( 'NOTE'.strval($i) );
						
						/* start lembur */				
						if ( $this->input->post( 'LEMBUR_JAM'.strval($i) ) != ""){
							if ( $this->input->post( 'LEMBUR_JAM'.strval($i) ) > 0){
								$data_post['LEMBUR_JAM'] = $this->input->post( 'LEMBUR_JAM'.strval($i) );
								$data_post['LEMBUR_RUPIAH'] = $this->model_m_gang_activity_detail->getlembur(
								$this->input->post( 'NIK'.strval($i) ),$this->input->post( 'LHM_DATE'.strval($i) ),$this->input->post( 'LEMBUR_JAM'.strval($i) ),$company);
							} else {
								$data_post['LEMBUR_JAM'] = 0;
								$data_post['LEMBUR_RUPIAH'] = 0;
							}
						} else {
							$data_post['LEMBUR_JAM'] = 0;
							$data_post['LEMBUR_RUPIAH'] = 0;
						}
						/* end lembur */   
						   
						$data_post['PENALTI'] = $this->input->post( 'PENALTI'.strval($i) );
						$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE'.strval($i) );
						/* start kontanan */
						if ( $this->input->post( 'KONTANAN'.strval($i) ) != ""){
							if ( $this->input->post( 'KONTANAN'.strval($i) ) != 0){
								$data_post['KONTANAN'] = $this->input->post( 'KONTANAN'.strval($i) );
								$gp = $this->model_m_gang_activity_detail->getgp($this->input->post( 'NIK'.strval($i) ),$company);
								$data_post['POTONGAN_KONTANAN'] = ($gp*$hks) + $this->input->post( 'PREMI'.strval($i) ) - $this->input->post( 'PENALTI'.strval($i) );
							} else {
								$data_post['KONTANAN'] = 0;
								$data_post['POTONGAN_KONTANAN'] = 0;
							}
						} else {
							$data_post['KONTANAN'] = 0;
							$data_post['POTONGAN_KONTANAN'] = 0;
						}
						
						/* end kontanan */         
						$data_absen = $this->model_m_gang_activity_detail->absen_validate($type_absen);
						
						if($ltc == "PJ"){
							if($project_subtype == "TN" || $project_subtype == "LC") {
				$data_aktivitas = $this->model_m_gang_activity_detail->projectlctn_activity_validate($project_subtype,$ac);
				$data_lokasi = $this->model_m_gang_activity_detail->lokasi_project_validate($lc, $company);    
									} else {
					$data_aktivitas = $this->model_m_gang_activity_detail->project_activity_validate($lc,$ac, $company);
					$data_lokasi = $this->model_m_gang_activity_detail->lokasi_project_validate($lc, $company);
							} 
						} else {
							$data_lokasi = $this->model_m_gang_activity_detail->lokasi_validate($lc, $ltc, $company);    
							$data_aktivitas = $this->model_m_gang_activity_detail->aktivitas_validate($ac, $ltc);
						}
											
						if(strlen($data_post['EMPLOYEE_CODE']) > 20){
							$status = "mohon tutup kotak NIK yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['TYPE_ABSENSI']) > 20){
							$status = "mohon tutup kotak Absensi yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
							$status = "mohon tutup kotak Kode Tipe yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['LOCATION_CODE']) > 20){
							$status = "mohon tutup kotak Kode Lokasi yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['ACTIVITY_CODE']) > 20){
							$status = "mohon tutup kotak Kode Aktivitas yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['HK_JUMLAH']) > 20){
							$status = "mohon tutup kotak HK yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['HSL_KERJA_UNIT']) > 20){
							$status = "mohon tutup kotak Satuan yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['HSL_KERJA_VOLUME']) > 20){
							$status = "mohon tutup kotak Volume yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['TARIF_SATUAN']) > 20){
							$status = "mohon tutup kotak Tarif / Satuan yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						}  else if(strlen($data_post['PREMI']) > 20){
							$status = "mohon tutup kotak Premi yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['LEMBUR_JAM']) > 20){
							$status = "mohon tutup kotak Jam Lembur yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						} else if(strlen($data_post['PENALTI']) > 20){
							$status = "mohon tutup kotak Penalti yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						}  else if(strlen($data_post['NOTE']) > 20){
							$status = "mohon tutup kotak Catatan yang terbuka pada baris ".$i." \r\n"; 
							echo $status;
						}
								
						if ($ac != '' || $ltc != ''){
							if ($type_absen == "") {
								 $status = "Tipe absensi baris no ".$i." tidak boleh kosong jika terdapat aktivitas!!\r\n"; 
								 echo $status;
							} else if(count($data_aktivitas) == 0)
								 { 
									 $status = "kode aktivitas baris no ".$i." : ".$ac.", kode salah!!\r\n"; 
									 echo $status;
								 }
							}
													
							if ($lc != '' || $ltc != ''){
								 if(count($data_lokasi) == 0)
								 { 
									 $status = "kode lokasi baris no ".$i." : ".$lc.", kode salah!!\r\n"; 
									 echo $status;
								 }
							}
								
							if (str_replace(" ", "",$type_absen) != ''){
								 if(count($data_absen) == 0)
								 { 
									  $status = "kode absen baris no ".$i.":".$type_absen.", kode salah!!<br/>"; 
									  echo $status;
								  }
							}
								
							if($type_absen == "KJ" || $type_absen == "KJI"){
								if($ltc == ""){
									$status = "kode tipe lokasi baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n"; 
									echo $status;
								} else if ($lc == ""){
									$status = "kode lokasi baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n";
									echo $status;    
								} else if ($ac == ""){
									$status = "kode aktivitas baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n";
									echo $status;        
								} else if ($hk == "" || $hk > "1"){
									$status = "HK baris no ".$i.": tidak boleh kosong / lebih dari 1 jika KJ atau KJI !!\r\n";
									echo $status;        
								}   
							} else if( $type_absen != "KJ" || $type_absen != "KJI" ){
								if(trim($hk) != ""){
									$status = "HK baris no ".$i.": tidak boleh berisi jika tidak KJ atau KJI !!\r\n";
									echo $status;
								 }
							 } 
							 
							if($type_absen == "NA"){
								if($ltc == ""){
									$status = "kode tipe lokasi baris no ".$i.": tidak boleh kosong  !!\r\n"; 
									echo $status;
								} else if ($lc == ""){
									$status = "kode lokasi baris no ".$i.": tidak boleh kosong !!\r\n";
									echo $status;    
								} else if ($ac == ""){
									$status = "kode aktivitas baris no ".$i.": tidak boleh kosong !!\r\n";
									echo $status;        
								} else if ($hk > "0"){
									$status = "HK baris no ".$i.": tidak boleh berisi jika NA !!\r\n";
									echo $status;        
								}   
							} 	
							
							/* cek satuan panen */
							if( $company != "SCK"){
							  if($ac == "8601003"){
								  if($data_post['HSL_KERJA_UNIT'] != "JANJANG"){ 
									   $status = "kode unit / satuan baris no ".$i." : ".$ac.", harus menggunakan JANJANG!!\r <br/>"; 
									   echo $status;
								  }
								  
								  if( $data_post['HSL_KERJA_VOLUME'] == ""){
									   $status = "Hasil kerja baris no ".$i." : ".$ac.", tidak boleh kosong atau nol!!\r <br/>"; 
									   echo $status;
								  } else {
									  if( $data_post['HSL_KERJA_VOLUME'] > 1500){
										  $status = "Hasil kerja baris no ".$i." : ".$ac.", tidak boleh lebih besar dari 1500 JANJANG!!\r <br/>"; 
										  echo $status;
									  }
								  }							
							  }
							}
							/* end cek satuan panen */
							
							$this->db->start_cache();
							$this->db->from('m_gang_activity_detail');
							$this->db->stop_cache();
							 
							$id = $this->input->post( 'ID'.strval($i) ) ;
							if($id == ""){
								 $id = $this->input->post( 'ID'.strval($i) ) ;
								 $this->db->where('ID',$id);
							} 
								
							if(empty($status)){
									$emp = $this->input->post( 'NIK'.strval($i) );                
									
									if ($status_entri == "update"){
										$this->db->where('ID',$id);    
									}
									
									$this->db->where('EMPLOYEE_CODE',$emp);
									$this->db->where('GANG_CODE',$gangcode);
									$this->db->where('LHM_DATE',$lhm_date);
									$this->db->where('COMPANY_CODE',$company);
									
									if ($this->db->count_all_results() == 0) {
										if ($this->input->post( 'TYPE_ABSENSI'.strval($i) ) == "KJO"){ } 
										else if ($this->input->post( 'NIK'.strval($i) ) == "" 
												&& $this->input->post( 'TYPE_ABSENSI'.strval($i) ) == ""){        						
										} else {
												$tglID = str_replace("-", "", $this->input->post( 'LHM_DATE'.strval($i) ));
												$begID = $company.$this->input->post( 'GANG_CODE'.strval($i) ).substr($tglID,2);
												$data_post['ID'] = $this->global_func->id_GAD('m_gang_activity_detail','ID',$begID);
												$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
												$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");
												$data_post['INPUT_IP'] = $this->input->ip_address(); 
												$insert_id = $this->model_m_gang_activity_detail->insert_m_gang_activity_detail( $data_post );                                        
										}
									} else if ($this->db->count_all_results() != 0) { 
										$input_id = $this->input->post( 'ID'.strval($i) );
										
										if ($this->input->post( 'TYPE_ABSENSI'.strval($i) ) == "KJO"){
										} else if ($this->input->post( 'NIK'.strval($i) ) == "" 
												&& $this->input->post( 'TYPE_ABSENSI'.strval($i) ) == ""){        						
										} else {
										  $data_post['UPDATE_BY'] = $this->session->userdata('LOGINID');
										  $data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");
										  $data_post['UPDATE_IP'] = $this->input->ip_address();
										  $insert_id = $this->model_m_gang_activity_detail->update_m_gang_activity_detail($id,$gangcode,$company,$lhm_date, $data_post );
										}    
									}
								}
								$this->db->flush_cache();    
							}
			}  /* end cek closing weekly */
		}
    }
  
    function edit_gad()
    {
        $tanggal_hapus = $this->uri->segment(3); 
        $gc_hapus = $this->uri->segment(4); 
        $company = $this->session->userdata('DCOMPANY');
        $gangcode = $this->input->post( 'GANG_CODE' ); 
        $lhm_date = $this->input->post( 'LHM_DATE' ); 
		$mandor1 = ltrim(rtrim(strtoupper($this->input->post( 'MANDORE1_CODE' ) ) ) ); 
		$mandor = ltrim(rtrim(strtoupper($this->input->post( 'MANDORE_CODE' ) ) ) ); 
		$kerani = ltrim(rtrim(strtoupper($this->input->post( 'KERANI_CODE' ) ) ) ); 
        
        $jumlah = $this->input->post( 'jumlah' );
		$periode = substr(str_replace("-","",$lhm_date),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			$status = "Periode transaksi bulan ini sudah diclose..";
			echo $status;
		} else {
			/* start closing weekly */
			$cekClosingWeekly = $this->global_func->cekClosingTransaksi('LHM', $lhm_date, $company);
			if($cekClosingWeekly == '1'){
				$status = "Transaksi tanggal " . $lhm_date ." ini sudah ditutup..";
				echo $status;
			} else {		
				
				for ($i=1; $i<=$jumlah;$i++) {
							
				$type_absen = ltrim(rtrim(strtoupper($this->input->post( 'TYPE_ABSENSI'.strval($i) ))));
				$ltc = ltrim(rtrim(strtoupper($this->input->post( 'LOCATION_TYPE_CODE'.strval($i) ))));
				$lc = ltrim(rtrim(strtoupper($this->input->post( 'LOCATION_CODE'.strval($i) ))));
				$ac = ltrim(rtrim(strtoupper($this->input->post( 'ACTIVITY_CODE'.strval($i) ))));
				$hks = ltrim(rtrim($this->input->post( 'HK_JUMLAH'.strval($i) )));
				$hk = str_replace(array("\r", "\r\n", "\n", " ", '&nbsp;', '&amp;'), '', $hks);                        
				$loc_code = substr($lc,0,strlen($lc)-2);
				$project_subtype = substr($lc,-2);
								
				$data_post['ID'] = $this->input->post( 'ID'.strval($i) );
				$data_post['GANG_CODE'] = $this->input->post( 'GANG_CODE'.strval($i) );
				$data_post['MANDORE1_CODE'] = $mandor1;
				$data_post['MANDORE_CODE'] = $mandor;
				$data_post['KERANI_CODE'] = $kerani;
				$data_post['LHM_DATE'] = $this->input->post( 'LHM_DATE'.strval($i) );
				$data_post['EMPLOYEE_CODE'] = $this->input->post( 'NIK'.strval($i) );
				$data_post['TYPE_ABSENSI'] = $type_absen;
				$data_post['LOCATION_TYPE_CODE'] = $ltc;            
				$data_post['LOCATION_CODE'] = $lc;
				$data_post['ACTIVITY_CODE'] = $ac;
				$data_post['HK_JUMLAH'] = $hk;
				$data_post['HSL_KERJA_UNIT'] = $this->input->post( 'HSL_KERJA_UNIT'.strval($i) );
				$data_post['HSL_KERJA_VOLUME'] = $this->input->post( 'HSL_KERJA_VOLUME'.strval($i) );
				$data_post['TARIF_SATUAN'] = $this->input->post( 'TARIF_SATUAN'.strval($i) );
				$data_post['PREMI'] = $this->input->post( 'PREMI'.strval($i) );
			   
				/* start lembur */ 
				if ( $this->input->post( 'LEMBUR_JAM'.strval($i) ) != ""){
					if ( $this->input->post( 'LEMBUR_JAM'.strval($i) ) > 0){
						$data_post['LEMBUR_JAM'] = $this->input->post( 'LEMBUR_JAM'.strval($i) );
						$data_post['LEMBUR_RUPIAH'] = $this->model_m_gang_activity_detail->getlembur(
						$this->input->post( 'NIK'.strval($i) ),$this->input->post( 'LHM_DATE'.strval($i) ),$this->input->post( 'LEMBUR_JAM'.strval($i) ),$company);
					} else {
						$data_post['LEMBUR_JAM'] = 0;
						$data_post['LEMBUR_RUPIAH'] = 0;
					}
				} else {
					$data_post['LEMBUR_JAM'] = 0;
					$data_post['LEMBUR_RUPIAH'] = 0;
				}
				/* end lembur */		         
				$data_post['PENALTI'] = $this->input->post( 'PENALTI'.strval($i) );
				$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
				$data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE'.strval($i) );
				$data_post['KONTANAN'] = $this->input->post( 'KONTANAN'.strval($i) );
				$data_post['NOTE'] = $this->input->post( 'NOTE'.strval($i) );
				/* start kontanan */
				if ( $this->input->post( 'KONTANAN'.strval($i) ) != ""){
					if ( $this->input->post( 'KONTANAN'.strval($i) ) != 0){
						$data_post['KONTANAN'] = $this->input->post( 'KONTANAN'.strval($i) );
						$gp = $this->model_m_gang_activity_detail->getgp($this->input->post( 'NIK'.strval($i) ),$company);
						$data_post['POTONGAN_KONTANAN'] = ($gp*$hks) + $this->input->post( 'PREMI'.strval($i) ) - $this->input->post( 'PENALTI'.strval($i) );
					} else {
						$data_post['KONTANAN'] = 0;
						$data_post['POTONGAN_KONTANAN'] = 0;
					}
				} else {
					$data_post['KONTANAN'] = 0;
					$data_post['POTONGAN_KONTANAN'] = 0;
				}
				/* end kontanan */                
				$gc = $this->input->post( 'GANG_CODE'.strval($i) );
				$id = $this->input->post( 'ID'.strval($i) );
				$company = $this->input->post( 'COMPANY_CODE'.strval($i) );
				$tgl = $this->input->post( 'LHM_DATE'.strval($i) );
				$nik = $this->input->post( 'NIK'.strval($i) );
				$type_absen = $this->input->post( 'TYPE_ABSENSI'.strval($i) );
				$ltc = $this->input->post( 'LOCATION_TYPE_CODE'.strval($i) );
				$lc = $this->input->post( 'LOCATION_CODE'.strval($i) );
				$ac = $this->input->post( 'ACTIVITY_CODE'.strval($i) );
				$hk = $this->input->post( 'HK_JUMLAH'.strval($i) );
				
				$data_absen = $this->model_m_gang_activity_detail->absen_validate($type_absen);
						
				if($ltc == "PJ"){
					if($project_subtype == "TN" || $project_subtype == "LC") {
						$data_aktivitas = $this->model_m_gang_activity_detail->projectlctn_activity_validate($project_subtype,$ac);
						$data_lokasi = $this->model_m_gang_activity_detail->lokasi_project_validate($lc, $company);    
					} else {
						$data_aktivitas = $this->model_m_gang_activity_detail->project_activity_validate($lc,$ac, $company);
						$data_lokasi = $this->model_m_gang_activity_detail->lokasi_project_validate($lc, $company);
					} 
				 } else {
					 $data_lokasi = $this->model_m_gang_activity_detail->lokasi_validate($lc, $ltc, $company);    
					 $data_aktivitas = $this->model_m_gang_activity_detail->aktivitas_validate($ac, $ltc);
				 }							
				 if(strlen($data_post['EMPLOYEE_CODE']) > 20){
					 $status = "mohon tutup kotak NIK yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				 } else if(strlen($data_post['TYPE_ABSENSI']) > 20){
					 $status = "mohon tutup kotak Absensi yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				 } else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
					 $status = "mohon tutup kotak Kode Tipe yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['LOCATION_CODE']) > 20){
					 $status = "mohon tutup kotak Kode Lokasi yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['ACTIVITY_CODE']) > 20){
					 $status = "mohon tutup kotak Kode Aktivitas yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['HK_JUMLAH']) > 20){
					 $status = "mohon tutup kotak HK yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['HSL_KERJA_UNIT']) > 20){
					 $status = "mohon tutup kotak Satuan yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['HSL_KERJA_VOLUME']) > 20){
					 $status = "mohon tutup kotak Volume yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['TARIF_SATUAN']) > 20){
					 $status = "mohon tutup kotak Tarif / Satuan yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  }  else if(strlen($data_post['PREMI']) > 20){
					 $status = "mohon tutup kotak Premi yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['LEMBUR_JAM']) > 20){
					 $status = "mohon tutup kotak Jam Lembur yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['PENALTI']) > 20){
					 $status = "mohon tutup kotak Penalti yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  } else if(strlen($data_post['NOTE']) > 20){
					 $status = "mohon tutup kotak Catatan yang terbuka pada baris ".$i." \r\n"; 
					 echo $status;
				  }
								
				  if ($ac != '' || $ltc != ''){
					 if ($type_absen == "") {
						 $status = "Tipe absensi baris no ".$i." tidak boleh kosong jika terdapat aktivitas!!\r\n"; 
						 echo $status;
					 } else if(count($data_aktivitas) == 0){ 
						 $status = "kode aktivitas baris no ".$i." : ".$ac.", kode salah!!\r\n"; 
						 echo $status;
					  }
					  
				   }
								
				   if ($lc != '' || $ltc != ''){
					  if(count($data_lokasi) == 0){ 
						   $status = "kode lokasi baris no ".$i." : ".$lc.", kode salah!!\r\n"; 
						   echo $status;
					  }
					}
								
					if (str_replace(" ", "",$type_absen) != ''){
					   if(count($data_absen) == 0){ 
							$status = "kode absen baris no ".$i.":".$type_absen.", kode salah!!<br/>"; 
							echo $status;
					   }
					 }
								
					 if($type_absen == "KJ" || $type_absen == "KJI"){
						 if($ltc == ""){
							 $status = "kode tipe lokasi baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n"; 
							 echo $status;
						 } else if ($lc == ""){
							 $status = "kode lokasi baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n";
							 echo $status;    
						 } else if ($ac == ""){
							 $status = "kode aktivitas baris no ".$i.": tidak boleh kosong jika KJ atau KJI !!\r\n";
							 echo $status;        
						 } else if (trim($hk) == "" || $hk > "1"){
							 $status = "HK baris no ".$i.": tidak boleh kosong / lebih dari 1 jika KJ atau KJI !!\r\n";
							 echo $status;        
						 }
					  } else if( $type_absen != "KJ" || $type_absen != "KJI" ){
						  if(trim($hk) != ""){
								$status = "HK baris no ".$i.": tidak boleh berisi jika tidak KJ atau KJI !!\r\n";
								echo $status;
						  }
					   } 
					   
					  if($type_absen == "NA"){
								if($ltc == ""){
									$status = "kode tipe lokasi baris no ".$i.": tidak boleh kosong  !!\r\n"; 
									echo $status;
								} else if ($lc == ""){
									$status = "kode lokasi baris no ".$i.": tidak boleh kosong !!\r\n";
									echo $status;    
								} else if ($ac == ""){
									$status = "kode aktivitas baris no ".$i.": tidak boleh kosong !!\r\n";
									echo $status;        
								} else if ($hk > "0"){
									$status = "HK baris no ".$i.": tidak boleh berisi jika NA !!\r\n";
									echo $status;        
								}   
					  }
					  
					  /* cek satuan panen */ 
					  if( $company != "SCK"){	
						if($ac == "8601003"){
							  if($data_post['HSL_KERJA_UNIT'] != "JANJANG"){ 
								   $status = "kode unit / satuan baris no ".$i." : ".$ac.", harus menggunakan JANJANG!!\r <br/>"; 
								   echo $status;
							  }
							  
							  if( $data_post['HSL_KERJA_VOLUME'] == ""){
								   $status = "Hasil kerja baris no ".$i." : ".$ac.", tidak boleh kosong atau nol!!\r <br/>"; 
								   echo $status;
							  } else {
								  if( $data_post['HSL_KERJA_VOLUME'] > 1500){
									  $status = "Hasil kerja baris no ".$i." : ".$ac.", tidak boleh lebih besar dari 1500 JANJANG!!\r <br/>"; 
									  echo $status;
								  }
							  }							
						}
					  }
					  /* end cek satuan panen */
										 
					   if(empty($status)){
						   $emp = $this->input->post( 'NIK'.strval($i) );                
						   $ids = $this->input->post( 'ID'.strval($i) );
															
						   if(strlen($ids) < 1 ){
								if ($this->input->post( 'NIK'.strval($i) ) == "" && $this->input->post( 'TYPE_ABSENSI'.strval($i) ) == ""){
								} else {          
									$tglID = str_replace("-", "", $this->input->post( 'LHM_DATE'.strval($i) ));
									$begID = $company.$this->input->post( 'GANG_CODE'.strval($i) ).substr($tglID,2);
									$data_post['ID'] = $this->global_func->id_GAD('m_gang_activity_detail','ID',$begID);
									$data_post['INPUT_BY'] = $this->session->userdata('LOGINID');
									$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");
									$data_post['INPUT_IP'] = $this->input->ip_address();
									
									$str = $this->model_m_gang_activity_detail->insert_m_gang_activity_detail( $data_post );
									$res = $this->db->query($str);
									if (!$res) {
										  // if query returns null
										$msg = $this->db->_error_message();
										$num = $this->db->_error_number();
										
									$cekduplikat = $this->model_m_gang_activity_detail->cek_duplikat($this->input->post( 'NIK'.strval($i) ),$this->input->post( 'LHM_DATE'.strval($i) ), $type_absen, $ac, $loc_code, $company);
										foreach( $cekduplikat as $r ) {
											echo  "data baris no ".$i.", NIK :" . $r['EMPLOYEE_CODE'] . " sudah ada transaksi sebelumnya
												di kemandoran :".$r['GANG_CODE'].", ".$r['TYPE_ABSENSI']." 
												lokasi: ". $r['LOCATION_CODE'].", dan aktivitas :".$r['ACTIVITY_CODE']."<br/>"; 
										}
									} 
								}
							} else { 
								$data_post['UPDATE_BY'] = $this->session->userdata('LOGINID');
								$data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");	
								$data_post['UPDATE_IP'] = $this->input->ip_address();		
								$this->model_m_gang_activity_detail->update_m_gang_activity_detail($id,$gc,$company,$tgl, $data_post );    
					   	}
					 }    
			} /* end closing weekly */      
		}
		}
	 }
    
    /* cek jumlah baris pada grid setiap memilih kode kemandoran */
    function cek_gad(){
        $tdate = $this->uri->segment(3);
        $gangc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
		$periode = substr($tdate,0,6);
		$close = $this->global_func->cekClosing($periode, $company);
        $data_enroll = $this->model_m_gang_activity_detail->cek_gad($tdate, $gangc, $company);
        $cp = "";
        $data = "";
        foreach($data_enroll as $row){
			if($close = 0){
				$cp = $close;
			} else {
				$cp = $row['closing'];
			}
            $data .= $row['jumlah'] . "~" . $cp;
		}
        echo $data;
    }
   
    /* delete */
    function delete_currlhm()
    {
        $company = $this->session->userdata('DCOMPANY');
            
        $gc = $this->input->post( 'GANG_CODE' );
        $nik = $this->input->post( 'NIK' );
        $tgl = $this->input->post( 'TGL' );
        $id = $this->input->post( 'ID' );
		$periode = substr(str_replace("-","",$tgl),0,6);
        $close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			$status = "Periode transaksi bulan ini sudah diclose..";
			echo $status;
		} else {
			$tglFull = substr($tgl,0,4)."-".substr($tgl,4,2)."-".substr($tgl,6,2);
			$cekClosingWeekly = $this->global_func->cekClosingTransaksi('LHM', $tglFull, $company);
			
			if($cekClosingWeekly == '1'){
				$status = "Data tidak dapat dihapus, transaksi tanggal " . $tglFull ." ini sudah ditutup..";
				echo $status;
			} else { 		
        		$this->model_m_gang_activity_detail->delete_currlhm($id,$gc,$nik,$tgl,$company);
			}
		}
    }
    
    /* fungsi hapus */
    /* function delete()
    {
        $tdate = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        $jumlah = $this->input->post( 'jumlah' );
        
        $return = 0;
		$jumlah = $this->input->post( 'jumlah' );
		$periode = substr(str_replace("-","",$tdate),0,6);
		$close = $this->global_func->cekClosing($periode, $company);
		if($close == '1'){
			$status = "Periode transaksi bulan ini sudah diclose..";
			echo $status;
		} else {
			$numWeek = ceil( date( 'j', strtotime( $lhm_date ) ) / 7 ); 
			$cekClosingWeekly = $this->model_m_gang_activity_detail->cekClosingMingguan($numWeek, $periode, $company);
			if($cekClosingWeekly == '1'){
				$status = "Transaksi minggu ke " . $numWeek ." ini sudah ditutup..";
				echo $status;
			} else { 			
				for ($i=1; $i<=$jumlah;$i++) {        
					$id = $this->input->post( 'ID'.strval($i) );
							
							$this->db->where('ID', $id);
							$this->db->where('DATE_FORMAT(LHM_DATE, "%Y%m%d") = ', $tdate);
							$this->db->where('GANG_CODE', $gc);
							$this->db->where('COMPANY_CODE', $company);
							$this->db->delete('m_gang_activity_detail'); 
						 
					$return = $return + $this->db->affected_rows();
				}
				echo $return;
			}
		}
    } */
    
    function update($gc)
    {
        $tdate = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        $this->model_m_gang_activity_detail->delete_ga($tdate, $gc, $company);
        $this->create_gad();
    }

	/* form pinjam meminjam */
    function insert_pinjaman( )
    {
        $data_post['GANG_CODE'] = $this->input->post( 'GC_PINDAH' );
        $data_post['NIK'] = $this->input->post( 'NIK_PINDAH' );
        $data_post['BDATE'] = $this->input->post( 'TGL_PINDAH' );
        $data_post['FROM'] = trim($this->input->post( 'GC_FROM' ));
        $data_post['TO'] = $this->input->post( 'GC_TO' );
        $data_post['REMARK'] = $this->input->post( 'REMARK' );
        $data_post['COMPANY_CODE'] = $this->session->userdata('DCOMPANY');
        $data_post['STATUS'] = $this->input->post('STATUS_PINJAM');
        $insert_id = $this->model_m_gang_activity_detail->insert_pjm_karyawan( $data_post );
     }
    
    /* cek pinjaman from / to */
    function cek_pinjaman()
    {
        $tdate = $this->uri->segment(3);
        $gangc = $this->uri->segment(4);
        $data_enroll = $this->model_m_gang_activity_detail->cek_pinjam($tdate, $gangc);
        $data = "";
        foreach($data_enroll as $row)
        {
            $data .= $row['jumlah'];
        }
        print_r($data);
    }
    
    function read_pinjaman(){
        $tgl = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        echo json_encode($this->model_m_gang_activity_detail->read_pinjaman($tgl, $gc, $company));
    }
    
    function update_pjm( )
    {
        $id = $this->uri->segment(3);
        $company = $this->session->userdata('DCOMPANY');
        $data_post['GANG_CODE'] = $this->input->post( 'GANG_CODE' );
        $data_post['NIK'] = $this->input->post( 'NIK' );
        $data_post['BDATE'] = $this->input->post( 'BDATE' );
        $data_post['FROM'] = $this->input->post( 'FROM' );
        $data_post['TO'] = $this->input->post( 'TO' );
        $data_post['COMPANY_CODE'] = $this->input->post( 'COMPANY_CODE' );
        $gc = $this->input->post( 'GANG_CODE' );
        $nik = $this->input->post( 'NIK' );
        $tgl = $this->input->post( 'BDATE' );
        $insert_id = $this->model_m_gang_activity_detail->update_pjm( $gc,$nik,$tgl,$company, $data_post );      
    }
    
    /* delete */
    function delete_pjm()
    {
        $id = $this->uri->segment(3);
        $company = $this->session->userdata('DCOMPANY');
        
        $data_post['GANG_CODE'] = $this->input->post( 'GANG_CODE' );
        $data_post['NIK'] = $this->input->post( 'NIK' );
        $data_post['BDATE'] = $this->input->post( 'BDATE' );
        $gc = $this->input->post( 'GANG_CODE' );
        $nik = $this->input->post( 'NIK' );
        $tgl = $this->input->post( 'BDATE' );
        $this->model_m_gang_activity_detail->delete_pjm($gc,$nik,$tgl,$company);
    }
    
    function cek_kmandoran_asal()
    {
        $tgl = $this->uri->segment(3);
        $nik = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
        
        $data_enroll = $this->model_m_gang_activity_detail->cek_kmandoran_asal($nik, $tgl, $company);
        
        $data = "";
        foreach($data_enroll as $row)
        {
            $data .= $row['GANG_CODE'];
        }
        echo $data; 
    }
    
    /*  create excel  */
    function create_excel(){
        $tgl = $this->uri->segment(3);
        $gc = $this->uri->segment(4);
        $company = $this->session->userdata('DCOMPANY');
              
        $this->db->select('GANG_CODE,LHM_DATE,EMPLOYEE_CODE,m_employee.NAMA,TYPE_ABSENSI,m_gang_activity_detail.LOCATION_TYPE_CODE,m_gang_activity_detail.LOCATION_CODE,m_location.DESCRIPTION,ACTIVITY_CODE,m_coa.COA_DESCRIPTION,HSL_KERJA_UNIT,HSL_KERJA_VOLUME,HK_JUMLAH,LEMBUR_JAM,TARIF_SATUAN,PREMI,PENALTI,m_gang_activity_detail.COMPANY_CODE');
        $this->db->join('m_employee','m_employee.NIK=m_gang_activity_detail.EMPLOYEE_CODE', 'left');
        $this->db->join('m_coa','m_coa.ACCOUNTCODE=m_gang_activity_detail.ACTIVITY_CODE', 'left');
        $this->db->join('m_location','m_location.LOCATION_CODE=m_gang_activity_detail.LOCATION_CODE AND m_location.COMPANY_CODE = m_gang_activity_detail.COMPANY_CODE', 'left');
        $this->db->where('GANG_CODE', $gc);
        $this->db->where('DATE_FORMAT(LHM_DATE,"%Y%m%d")', $tgl);
        $this->db->where('m_gang_activity_detail.COMPANY_CODE', $company);  
       
          $query = $this->db->get('m_gang_activity_detail');              
            
        to_excel($query,'LHM_'.$company.'_'.$gc.'_'.$tgl.'');
        //redirect( 'm_gang_activity_detail/' );
         if ($query->num_rows() == 0) {
            redirect( 'm_gang_activity_detail/' );
        } 
    }
    
    /* ################ progress ########################### */
    /* grid pertama kali baca ke tabel m_gang_activity_detail */
    function read_progress_curr()
    {
        $tdate =htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $gc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_gang_activity_detail->get_progress($tdate, $gc, $company));
    }
    
    function submit_progress()
    {
        $data ="";
        $err_status='';
        
        foreach ($_POST as $k => $v) {
        $data .= "$k:   $v";
        }
           
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $prog_date = htmlentities($this->input->post( 'TGL_PROGRESS' ),ENT_QUOTES,'UTF-8'); 
        $gc = htmlentities($this->input->post( 'GANG_CODE' ),ENT_QUOTES,'UTF-8'); 
		$periode = substr( str_replace("-","",$prog_date), 0, 6);
		$close = $this->global_func->cekClosing($periode, $company);
        if($close == '1'){
			$status = "Periode transaksi bulan ini sudah diclose..";
			echo $status;
		} else {
			/* start cek closing */
			$cekClosingWeekly = $this->global_func->cekClosingTransaksi('PRG', $prog_date, $company);
			
			if($cekClosingWeekly == '1'){
				$status = "Data tidak dapat disimpan, transaksi tanggal " . $prog_date ." ini sudah ditutup..";
				echo $status;
			} else { 		
				$jumlah = $this->input->post( 'jumlahdt' );
				for ($i=1; $i<=$jumlah;$i++) {
					$hasil_kerja = ltrim(rtrim($this->input->post( 'NILAI'.strval($i))));
					$tglID = str_replace('-','',$prog_date);
					$begID = $company.$tglID;
					
					/* $data_post['ID_PROGRESS'] = $this->global_func->id_GAD('p_progress','ID_PROGRESS',$begID); */
					
					$tgl_progress = htmlentities($this->input->post( 'TGL_PROGRESS'.strval($i) ),ENT_QUOTES,'UTF-8');
					$activity =  htmlentities($this->input->post( 'PACTIVITY_CODE'.strval($i) ),ENT_QUOTES,'UTF-8');
					$location = htmlentities($this->input->post( 'PLOCATION_CODE'.strval($i) ),ENT_QUOTES,'UTF-8');
					$hk=htmlentities($this->input->post( 'PHASIL_KERJA'.strval($i) ),ENT_QUOTES,'UTF-8');
					$hk2=htmlentities($this->input->post( 'PHASIL_KERJA2'.strval($i) ),ENT_QUOTES,'UTF-8');
					$unit=htmlentities($this->input->post( 'PSATUAN2'.strval($i) ),ENT_QUOTES,'UTF-8');
					$unit2 = htmlentities($this->input->post( 'PSATUAN'.strval($i) ),ENT_QUOTES,'UTF-8');
					$cek_activity=$this->model_m_gang_activity_detail->cek_aktifitas($activity);
					
					if($activity=='' || $activity==null)
					{
						$err_status ="activity code tidak boleh kosong pada kolom - ".$i;
						echo $err_status;
						break;     
					}
					if ($cek_activity <= 0 )
					{
						 $err_status= "activity code pada kolom - ".$i." tidak ada dalam database";
						 echo $err_status ;
						 break;       
					}
					if(strlen($location)>20)
					{
						$err_status= "harap tutup kotak lokasi";
						echo $err_status ;
						break;    
					}
					
					
					/*if( strlen($unit2) > 0 && $activity!= "8601003"){
						 if($unit2 !== '-') { 
							if($hk == "" || $hk == "0"){
								$err_status= "baris ".$i." satuan tidak boleh kosong atau 0";
								echo $err_status ;
								break;
							}
						}
					}
					   
					if( strlen($unit) > 0 && $activity!= "8601003") {
						 if($unit !== '-') { 						
							if($hk2 == "" || $hk2 == "0"){
								$err_status= "baris ".$i." satuan 2 tidak boleh kosong atau 0";
								echo $err_status ;
								break;
							}
						}
					} */
					
					if(strlen($hk)>20){
						$err_status= "harap tutup kotak Nilai";
						echo $err_status ;
						break;    
					}    
					
					/* update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */
					if(strlen($hk2)>20)
					{
						$err_status= "harap tutup kotak Nilai 2";
						echo $err_status ;
						break;    
					} 
					
					if(strlen($unit)>20)
					{
						$err_status= "harap tutup kotak Satuan 2";
						echo $err_status ;
						break;    
					}
					/* end update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */
	
					if (empty($err_status) || $err_status=='')
					{
						$data_post['GANG_CODE'] = $gc;
						$data_post['TGL_PROGRESS'] = $prog_date;
						$data_post['LOCATION_CODE'] = $location;
						$data_post['ACTIVITY_CODE'] = $activity;
						$data_post['HASIL_KERJA'] = htmlentities($this->input->post( 'PHASIL_KERJA'.strval($i) ),ENT_QUOTES,'UTF-8');
						$data_post['SATUAN'] = htmlentities($this->input->post( 'PSATUAN'.strval($i) ),ENT_QUOTES,'UTF-8');
						$data_post['HK'] = htmlentities($this->input->post( 'HK'.strval($i) ),ENT_QUOTES,'UTF-8');
						
						$data_post['HASIL_KERJA2'] = htmlentities($this->input->post( 'PHASIL_KERJA2'.strval($i) ),ENT_QUOTES,'UTF-8');
						$data_post['SATUAN2'] = htmlentities($this->input->post( 'PSATUAN2'.strval($i) ),ENT_QUOTES,'UTF-8');
	
						$data_post['REALISASI'] = htmlentities($this->input->post( 'REALISASI'.strval($i) ),ENT_QUOTES,'UTF-8');
						$data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
						$data_post['COMPANY_CODE'] = $company;
						
						$this->db->from('p_progress');
						$this->db->where('GANG_CODE',$gc);
						$this->db->where('TGL_PROGRESS',$prog_date);
						$this->db->where('ACTIVITY_CODE',$activity);
						$this->db->where('LOCATION_CODE',$location);
						$this->db->where('COMPANY_CODE',$company);
						
						if ($this->db->count_all_results() == 0) {
							$insert_id = $this->model_m_gang_activity_detail->insert_p_progress( $data_post );                        
						} else if ($this->db->count_all_results() != 0) {
							$insert_id = $this->model_m_gang_activity_detail->update_p_progress( $gc,$prog_date,$activity,$location,$company, $data_post );    
							
						}       
					}
				}
			} /* end cek closing */
        }            
    }
    
    function delete_progress(){ 
		$data ="";
        $err_status="";
        $company = $this->session->userdata('DCOMPANY');
                
        $gc = $this->input->post( 'GANG_CODE' );
        $idp = $this->input->post( 'IDP' );
        $tgl = $this->input->post( 'TGL' );
        $act = $this->input->post( 'ACT' );
        $lc = $this->input->post( 'LC' );
		$periode = substr( str_replace("-","",$tgl), 0, 6);
        $close = $this->global_func->cekClosing($periode, $company);
       
	    if($close == '1'){
			$err_status = "Periode transaksi bulan ini sudah diclose..";
			echo $err_status;
		} else {
			/* start cek closing */
			$cekClosingWeekly = $this->global_func->cekClosingTransaksi('PRG', $tgl, $company);
			
			if($cekClosingWeekly == '1'){
				$err_status = "Data tidak dapat dihapus, transaksi tanggal " . $tgl ." ini sudah ditutup..";
				echo $err_status;
			} else { 	
				if (empty($err_status) || $err_status==''){
       				$del = $this->model_m_gang_activity_detail->delete_p_progress($gc, $idp, $tgl, $act, $lc, $company);
				}
			}
		}
    }
	
	function cetak_pdf(){
		$periode = htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
		$gc = htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$company_name = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		
		$gc_header = false;
		$afd = false;
		$nik_mandor = false;
		$nama_mandor = false;
		$lhm_pdf = $this->model_m_gang_activity_detail->cetak_pdf_header($gc,$company);
		foreach ($lhm_pdf as $row){
			$gc_header = $row['GANG_CODE'];
			$nik_mandor = $row['MANDORE_CODE'];
			$nama_mandor = $row['NAMA'];	
		}
		
		$pdf = new pdf_usage(); 
               
        $pdf->Open();
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetMargins(3, 12,0);
        $pdf->AddPage('L', 'LEGAL');
        $pdf->AliasNbPages(); 
            
        $pdf->SetStyle("s1","arial","",9,"");
        $pdf->SetStyle("s2","arial","",8,"");
        $pdf->SetStyle("s3","arial","",10,"");
        
        $pdf->SetTextColor(118, 0, 3);
        $pdf->SetX(60);
        
        $pdf->Ln(1);
        
        require_once(APPPATH . 'libraries/rptPDF_def.inc'); 
        $columns = 15; //number of Columns
        
        //Initialize the table class
        $pdf->tbInitialize($columns, true, true);
        
        //set the Table Type
        $pdf->tbSetTableType($table_default_table_type);
        $aSimpleHeader = array();
		$aSimpleHeader2 = array();
		$aSimpleHeader3 = array();
		$aSimpleHeader4 = array();
		$aSimpleHeader5 = array();
		$aSimpleHeader6 = array();
		$aSimpleHeader7 = array();
		$aSimpleHeader8 = array();
		$aSimpleHeader9 = array();
        
        $header = array('PT. '.$company_name,'','','L H M','', '','','','', '','','Pemakaian Material','','','','');
        $header2 = array('','','','','', '','','','', '','','','','','','','');
		$header3 = array('Kode Kemandoran',$gc_header,'','(LAPORAN HARIAN MANDOR)','','','','', '','','','No','Nama Material','Sat','Jumlah','');
		$header4 = array('Afdeling/Bagian','','','Tanggal: '.$periode,'','', '','','','', '','1','','','','','','');
		$header5 = array('NIK Mandor',$nik_mandor,'','','', '','','','', '','','2','','','','','');
		$header6 = array('Nama Mandor',$nama_mandor,'','','', '','','','', '','','3','','','','','');
		$header7 = array('','','','','', '','','','', '','','','','','','','');
		$header8 = array('No','NIK','Nama Karyawan','Absensi','Lokasi Kerja', '','','Aktifitas','HK', 'Fisik/Hasil','Sat','Tarif/Unit','Premi','Jam Lembur','Penalty','Ttd Kehadiran','Keterangan');
		$header9 = array('','','','','Tipe', 'Kode','Account','','', '','','','','','','','');
        //Table Header
        for($i=0; $i < $columns+1; $i++) {
            $aSimpleHeader[$i] = $table_default_header_type;
            $aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[$i]['WIDTH'] = 22.5;
			$aSimpleHeader[$i]['LN_SIZE'] = 12;
			$aSimpleHeader[$i]['T_SIZE'] = 9;
			$aSimpleHeader[0]['COLSPAN'] = 3;
			$aSimpleHeader[0]['ROWSPAN'] = 2;
            $aSimpleHeader[3]['COLSPAN'] = 8;
			$aSimpleHeader[3]['ROWSPAN'] = 2;
			$aSimpleHeader[3]['T_SIZE'] = 12;
			$aSimpleHeader[11]['COLSPAN'] = 4;
			$aSimpleHeader[11]['ROWSPAN'] = 2;
			
			$aSimpleHeader2[$i] = $table_default_header_type;
            $aSimpleHeader2[$i]['TEXT'] = $header2[$i];
			$aSimpleHeader2[$i]['WIDTH'] = 22.5;
			$aSimpleHeader2[0]['WIDTH'] = 30;
            $aSimpleHeader2[$i]['LN_SIZE'] = 5;
			$aSimpleHeader3[$i] = $table_default_header_type;
            $aSimpleHeader3[$i]['TEXT'] = $header3[$i];
			$aSimpleHeader3[$i]['WIDTH'] = 22.5;
			$aSimpleHeader3[0]['WIDTH'] = 30;
			$aSimpleHeader3[0]['T_ALIGN'] = 'L';
            $aSimpleHeader3[1]['COLSPAN'] = 2;
			$aSimpleHeader3[1]['WIDTH'] = 15;
			$aSimpleHeader3[3]['COLSPAN'] = 8;
			$aSimpleHeader3[11]['WIDTH'] = 9;
			$aSimpleHeader3[12]['WIDTH'] = 45;
			$aSimpleHeader3[13]['WIDTH'] = 16;
			$aSimpleHeader3[14]['WIDTH'] = 20;
			//$aSimpleHeader3[3]['ROWSPAN'] = 1;
			$aSimpleHeader3[$i]['LN_SIZE'] = 5;
			
			$aSimpleHeader4[$i] = $table_default_header_type;
            $aSimpleHeader4[$i]['TEXT'] = $header4[$i];
			$aSimpleHeader4[$i]['WIDTH'] = 22.5;
			$aSimpleHeader4[0]['T_ALIGN'] = 'L';
			$aSimpleHeader4[0]['WIDTH'] = 30;
			$aSimpleHeader4[1]['COLSPAN'] = 2;
			$aSimpleHeader4[1]['WIDTH'] = 15;
			$aSimpleHeader4[3]['COLSPAN'] = 8;
			$aSimpleHeader4[11]['WIDTH'] = 9;
			$aSimpleHeader4[12]['WIDTH'] = 45;
			$aSimpleHeader4[13]['WIDTH'] = 16;
			$aSimpleHeader4[14]['WIDTH'] = 20;
            $aSimpleHeader4[$i]['LN_SIZE'] = 5;
			$aSimpleHeader4[3]['ROWSPAN'] = 3;
			
			$aSimpleHeader5[$i] = $table_default_header_type;
            $aSimpleHeader5[$i]['TEXT'] = $header5[$i];
			$aSimpleHeader5[$i]['WIDTH'] = 22.5;
           	$aSimpleHeader5[$i]['LN_SIZE'] = 5;
			$aSimpleHeader5[0]['T_ALIGN'] = 'L';
			$aSimpleHeader5[0]['WIDTH'] = 30;
			$aSimpleHeader5[1]['COLSPAN'] = 2;
			$aSimpleHeader5[1]['WIDTH'] = 15;
			$aSimpleHeader5[3]['COLSPAN'] = 8;
			$aSimpleHeader5[11]['WIDTH'] = 9;
			$aSimpleHeader5[12]['WIDTH'] = 45;
			$aSimpleHeader5[13]['WIDTH'] = 16;
			$aSimpleHeader5[14]['WIDTH'] = 20;
			
			$aSimpleHeader6[$i] = $table_default_header_type;
            $aSimpleHeader6[$i]['TEXT'] = $header6[$i];
            $aSimpleHeader6[$i]['WIDTH'] = 22.5;
			$aSimpleHeader6[$i]['LN_SIZE'] = 5;
			$aSimpleHeader6[0]['T_ALIGN'] = 'L';
			$aSimpleHeader6[0]['WIDTH'] = 30;
			$aSimpleHeader6[1]['COLSPAN'] = 2;
			$aSimpleHeader6[1]['WIDTH'] = 15;
			$aSimpleHeader6[3]['COLSPAN'] = 8;
			$aSimpleHeader6[11]['WIDTH'] = 9;
			$aSimpleHeader6[12]['WIDTH'] = 45;
			$aSimpleHeader6[13]['WIDTH'] = 16;
			$aSimpleHeader6[14]['WIDTH'] = 20;
			
			$aSimpleHeader7[$i] = $table_default_header_type;
            $aSimpleHeader7[$i]['TEXT'] = $header7[$i];
            $aSimpleHeader7[$i]['WIDTH'] = 22.5;
			$aSimpleHeader7[0]['COLSPAN'] = 17;
            $aSimpleHeader7[$i]['LN_SIZE'] = 5;
			
			$aSimpleHeader8[$i] = $table_default_header_type;
            $aSimpleHeader8[$i]['TEXT'] = $header8[$i];
			$aSimpleHeader8[$i]['T_SIZE'] = 8;
            $aSimpleHeader8[$i]['WIDTH'] = 22.5;
			$aSimpleHeader8[0]['ROWSPAN'] = 2;
			$aSimpleHeader8[0]['WIDTH'] = 10;
            $aSimpleHeader8[1]['ROWSPAN'] = 2;
			$aSimpleHeader8[1]['WIDTH'] = 20;
            $aSimpleHeader8[2]['ROWSPAN'] = 2;
			$aSimpleHeader8[2]['WIDTH'] = 45;
			$aSimpleHeader8[3]['WIDTH'] = 15;
			$aSimpleHeader8[3]['ROWSPAN'] = 2;
            $aSimpleHeader8[4]['COLSPAN'] = 3;
			$aSimpleHeader8[4]['WIDTH'] = 12;
			$aSimpleHeader8[5]['WIDTH'] = 30;
			$aSimpleHeader8[6]['WIDTH'] = 15;
			$aSimpleHeader8[7]['ROWSPAN'] = 2; 
			$aSimpleHeader8[7]['WIDTH'] = 69.5;
            $aSimpleHeader8[8]['ROWSPAN'] = 2;
			$aSimpleHeader8[8]['WIDTH'] = 10;
			$aSimpleHeader8[9]['ROWSPAN'] = 2;
			$aSimpleHeader8[9]['WIDTH'] = 16;
			$aSimpleHeader8[10]['WIDTH'] = 15;
            $aSimpleHeader8[10]['ROWSPAN'] = 2;
            $aSimpleHeader8[11]['ROWSPAN'] = 2;
			$aSimpleHeader8[11]['WIDTH'] = 20;
            $aSimpleHeader8[12]['ROWSPAN'] = 2;
			$aSimpleHeader8[13]['ROWSPAN'] = 2;
			$aSimpleHeader8[13]['WIDTH'] = 15;
			$aSimpleHeader8[14]['ROWSPAN'] = 2;
            $aSimpleHeader8[15]['ROWSPAN'] = 2;
            $aSimpleHeader8[16]['ROWSPAN'] = 2;
            $aSimpleHeader8[$i]['LN_SIZE'] = 5;
			
			$aSimpleHeader9[$i] = $table_default_header_type;
            $aSimpleHeader9[$i]['TEXT'] = $header9[$i];
            $aSimpleHeader9[$i]['WIDTH'] = 22.5;
			$aSimpleHeader9[$i]['LN_SIZE'] = 5;
			$aSimpleHeader9[$i]['T_SIZE'] = 8;
			$aSimpleHeader9[0]['WIDTH'] = 10;
			$aSimpleHeader9[1]['WIDTH'] = 20;
			$aSimpleHeader9[2]['WIDTH'] = 45;
			$aSimpleHeader9[3]['WIDTH'] = 15;
			$aSimpleHeader9[4]['WIDTH'] = 12;
			$aSimpleHeader9[5]['WIDTH'] = 30;
			$aSimpleHeader9[6]['WIDTH'] = 15;
			$aSimpleHeader9[7]['WIDTH'] = 69.5;
			$aSimpleHeader9[8]['WIDTH'] = 10;
			$aSimpleHeader9[9]['WIDTH'] = 16;
			$aSimpleHeader9[10]['WIDTH'] = 15;
			$aSimpleHeader9[11]['WIDTH'] = 20;
			$aSimpleHeader9[13]['WIDTH'] = 15;
          
        }
        
        $pdf->tbSetHeaderType($aSimpleHeader);
        $pdf->tbSetHeaderType($aSimpleHeader2);
		$pdf->tbSetHeaderType($aSimpleHeader3);
		$pdf->tbSetHeaderType($aSimpleHeader4);
		$pdf->tbSetHeaderType($aSimpleHeader5);
		$pdf->tbSetHeaderType($aSimpleHeader6);
		$pdf->tbSetHeaderType($aSimpleHeader7);
		$pdf->tbSetHeaderType($aSimpleHeader8);
		$pdf->tbSetHeaderType($aSimpleHeader9);
        //Draw the Header
        $pdf->tbDrawHeader();
		
		 //Table Data Settings
        $aDataType = Array();
        for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
        $pdf->tbSetDataType($aDataType);
                
        $lhm_body = $this->model_m_gang_activity_detail->cetak_pdf_body($gc,$periode,$company);
        $i = 1;
		$data[0]['WIDTH'] = 10;
        foreach ($lhm_body as $row)
        {
            $data = Array();
            $data[0]['TEXT'] = $i;
			$data[0]['WIDTH'] = 10;
            $data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
            $data[2]['TEXT'] = $row['NAMA'];
            $data[2]['WIDTH'] = 30;
            $data[2]['T_ALIGN'] = 'L';
            $data[3]['TEXT'] = $row['TYPE_ABSENSI'];
            $data[4]['TEXT'] = $row['LOCATION_TYPE_CODE'];        
            $data[5]['TEXT'] = $row['LOCATION_CODE'];
            $data[6]['TEXT'] = $row['ACTIVITY_CODE'];
			$data[7]['TEXT'] = $row['COA_DESCRIPTION'];
            $data[7]['WIDTH'] = 100;
            $data[7]['T_ALIGN'] = 'L';
			$data[8]['TEXT'] = $row['HK_JUMLAH'];
			$data[9]['TEXT'] = $row['TARIF_SATUAN'];
			$data[10]['TEXT'] = '';
			$data[11]['TEXT'] = '';
			$data[12]['TEXT'] = $row['PREMI'];
			$data[13]['TEXT'] = $row['LEMBUR_JAM'];
			$data[14]['TEXT'] = $row['PENALTI'];
			$data[15]['TEXT'] = '';
			$data[16]['TEXT'] = '';
         
            $i++;   
            $pdf->tbDrawData($data);
        }
		
		$pdf->tbOuputData();
        $pdf->tbDrawBorder();
        $pdf->Ln(15.5);
    
        require_once(APPPATH . 'libraries/daftar_upah/authorize_lhm.inc');
		$pdf->Output();
	}
	
	/* ### Start Pengisian Material ### */
    function read_material()
    {
        $tdate =htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $gc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_m_gang_activity_detail->get_material($tdate, $gc, $company));
    }
    
    function submit_material()
    {
        $company = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
        $mode = htmlentities(mysql_escape_string($this->input->post('MODE')));
		$activity = htmlentities(mysql_escape_string($this->input->post('ACTIVITY_CODE')));
		$location = htmlentities(mysql_escape_string($this->input->post('LOCATION_CODE')));
		$date = htmlentities(mysql_escape_string($this->input->post('LHM_DATE')));
		$gangcode = htmlentities(mysql_escape_string($this->input->post('GANG_CODE')));
		$mc = htmlentities(mysql_escape_string($this->input->post('MATERIAL_CODE')));
		$data_post['GANG_CODE']= $gangcode;
        $data_post['LHM_DATE']= $date;
        $data_post['ACTIVITY_CODE']=$activity;
        $data_post['LOCATION_CODE']=$location;
		$data_post['MATERIAL_QTY']=htmlentities(mysql_escape_string($this->input->post('MATERIAL_QUANTITY')));
        $data_post['MATERIAL_CODE']=$mc;
		$data_post['MATERIAL_BPB_NO']=htmlentities(mysql_escape_string($this->input->post('MATERIAL_BPB_NO')));
        $data_post['COMPANY_CODE']=$company;
		
		if($mode == "POST"){
			$data_post['INPUT_BY']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        	$data_post['INPUT_DATE']=date ("Y-m-d H:i:s");
			$data_exist = $this->model_m_gang_activity_detail->cek_exist_data($gangcode,$date,$mc,$activity,$location,$company); 
                if($data_exist > 0) {
					echo("data material yang ke lokasi & aktivitas tersebut sudah ada di dalam database, \n
						  untuk mengubah silakan gunakan tombol ubah.."); 
				} else {
					$update_data=$this->model_m_gang_activity_detail->insert_material($data_post);
					echo "0"; 					
				}
		} else if ( $mode == "GET"){
			$matid = htmlentities(mysql_escape_string($this->input->post('LHM_MATERIAL_ID')));
			$data_post['UPDATE_BY']=htmlentities(mysql_escape_string($this->session->userdata('LOGINID')));
        	$data_post['UPDATE_DATE']=date ("Y-m-d H:i:s");
			$update_data=$this->model_m_gang_activity_detail->update_material($matid, $gangcode,$date,$activity, $location, $company,$data_post);
            echo "0";  
		}
    }
    
    function delete_material(){ 
        $company = htmlentities(mysql_escape_string($this->session->userdata('DCOMPANY')));
       	$idp = htmlentities(mysql_escape_string($this->input->post('LHM_MATERIAL_ID')));
		$act = htmlentities(mysql_escape_string($this->input->post('ACTIVITY_CODE')));
		$lc = htmlentities(mysql_escape_string($this->input->post('LOCATION_CODE')));
		$tgl = htmlentities(mysql_escape_string($this->input->post('LHM_DATE')));
		$gc = htmlentities(mysql_escape_string($this->input->post('GANG_CODE')));
		$mc = htmlentities(mysql_escape_string($this->input->post('MATERIAL_CODE')));
        
        $this->model_m_gang_activity_detail->delete_material($gc, $idp, $tgl, $act, $mc, $lc, $company);
    }
	
	function getActMaterial(){
		$q = $_REQUEST["q"]; 
		$gc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
		$data_act = $this->model_m_gang_activity_detail->mgetActMaterial($gc,$tgl,$q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['ACTIVITY_CODE']).'","res_name":"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'","res_dl":"'.str_replace('"','\\"',$row['ACTIVITY_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	function getLocMaterial(){
		$q = $_REQUEST["q"]; 
		$gc = $this->uri->segment(3);
		$tgl = $this->uri->segment(4);
		$act = $this->uri->segment(5);
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data_act = $this->model_m_gang_activity_detail->mgetLocMaterial($gc,$tgl,$act,$q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['LOCATION_CODE']).'","res_name":"'.str_replace('"','\\"',$row['LOCATION_CODE']).'","res_dl":"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['LOCATION_CODE']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	function getMaterial(){
		$q = $_REQUEST["q"]; 
		$data_act = $this->model_m_gang_activity_detail->mgetMaterial($q);
		$aktivitas = array();
		foreach($data_act as $row) {
			$aktivitas[] = '{"res_id":"'.str_replace('"','\\"',$row['MATERIAL_CODE']).'","res_name":"'.str_replace('"','\\"',$row['MATERIAL_NAME']).'","res_dl":"'.str_replace('"','\\"',$row['MATERIAL_CODE']. "&nbsp; - &nbsp;" .$row['MATERIAL_NAME']).'","uom":"'.str_replace('"','\\"',$row['MATERIAL_UOM']).'"}';
		}
		echo '['.implode(',',$aktivitas).']'; exit; 
	}
	
	/* ### End Pengisian Material ### */

}

?>