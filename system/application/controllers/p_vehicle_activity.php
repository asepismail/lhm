<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class P_vehicle_activity extends Controller 
{
    private $lastmenu;
    function P_vehicle_activity ()
    {
        parent::Controller();    
        /*modul yang di load halaman vehicle activity*/
        $this->load->model( 'model_vehicle_activity' );
        $this->load->model('model_c_user_auth');
        $this->lastmenu="p_vehicle_activity";
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        
        $this->load->plugin('to_excel');
        
        $this->lastmenu="p_vehicle_activity";
    }
    
    function index()
    {
        $data = array();
        
        $view = "info_vehicle_activity";
        $data = array();
        $data['judul_header'] = "Laporan Buku Kendaraan";
        $data['js'] = "";
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');          
    
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE){    
                show($view, $data);
        } else {
            redirect('login');
        }
    }    
    
    function grid_vehicle_activity()
    {      
        $vc = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $bln = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $thn = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        echo json_encode($this->model_vehicle_activity->grid_vc($vc, $bln, $thn, $company));
        //echo json_encode($this->model_vehicle_activity->grid_vehicle_activity());
    }
    /* create */
    
function create_va()
{
	$periode = $this->input->post('TAHUN').$this->input->post('BULAN');
	$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
	$close = $this->global_func->cekClosing($periode, $company);
		
	if($close == '1'){
		echo "Periode transaksi bulan ini sudah diclose..";
	} else {
		$KMHM_BERANGKAT =htmlentities($this->input->post('KMHM_BERANGKAT'),ENT_QUOTES,'UTF-8');
		$KMHM_KEMBALI=htmlentities($this->input->post('KMHM_KEMBALI'),ENT_QUOTES,'UTF-8');
		$KMHM_JUMLAH=$KMHM_KEMBALI-$KMHM_BERANGKAT;
		$numeric_data=array('KMHM_BERANGKAT'=>htmlentities($this->input->post('KMHM_BERANGKAT'),ENT_QUOTES,'UTF-8'),
							'KMHM_KEMBALI'=>htmlentities($this->input->post('KMHM_KEMBALI'),ENT_QUOTES,'UTF-8')
						);
			
		$validate_numeric=$this->validate_numeric($numeric_data);
		$kode = htmlentities(trim($this->input->post('KODE_KENDARAAN')),ENT_QUOTES,'UTF-8');
		$data_post['ID'] = $this->global_func->id_BK('p_vehicle_activity','ID', $kode );	
		$data_post['KODE_KENDARAAN'] = htmlentities(trim($this->input->post( 'KODE_KENDARAAN' )),ENT_QUOTES,'UTF-8');
		$data_post['SATUAN_PRESTASI'] = htmlentities($this->input->post( 'SATUAN_PRESTASI' ),ENT_QUOTES,'UTF-8');
		$data_post['BULAN'] = htmlentities($this->input->post( 'BULAN' ),ENT_QUOTES,'UTF-8');
		$data_post['TAHUN'] = htmlentities($this->input->post( 'TAHUN' ),ENT_QUOTES,'UTF-8');
		$data_post['TGL_AKTIVITAS'] = htmlentities($this->input->post( 'TGL_AKTIVITAS' ),ENT_QUOTES,'UTF-8');       
		$data_post['JAM_KERJA'] = floatval(htmlentities($this->input->post( 'JAM_KERJA' ),ENT_QUOTES,'UTF-8'));
		$data_post['KMHM_BERANGKAT'] = htmlentities($this->input->post( 'KMHM_BERANGKAT' ),ENT_QUOTES,'UTF-8');
		$data_post['KMHM_KEMBALI'] = htmlentities($this->input->post( 'KMHM_KEMBALI' ),ENT_QUOTES,'UTF-8');
		$data_post['KMHM_JUMLAH'] = htmlentities($KMHM_JUMLAH,ENT_QUOTES,'UTF-8');
		$data_post['LOCATION_TYPE_CODE'] = htmlentities(trim($this->input->post( 'LOCATION_TYPE_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['LOCATION_CODE'] = htmlentities(trim($this->input->post( 'LOCATION_CODE' )),ENT_QUOTES,'UTF-8');            		
		$data_post['ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['SUB_ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'SUB_ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['MUATAN_JENIS'] = htmlentities($this->input->post( 'MUATAN_JENIS' ),ENT_QUOTES,'UTF-8');
		$data_post['MUATAN_SAT'] = htmlentities($this->input->post( 'MUATAN_SAT' ),ENT_QUOTES,'UTF-8');    
		$data_post['MUATAN_VOL'] = htmlentities($this->input->post( 'MUATAN_VOL' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_VOL'] = htmlentities($this->input->post( 'PRESTASI_VOL' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_SAT'] = htmlentities($this->input->post( 'PRESTASI_SAT' ),ENT_QUOTES,'UTF-8');
		$data_post['PRESTASI_VOL2'] = htmlentities($this->input->post( 'PRESTASI_VOL2' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_SAT2'] = htmlentities($this->input->post( 'PRESTASI_SAT2' ),ENT_QUOTES,'UTF-8');    
		$data_post['INPUT_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['INPUT_DATE'] = date ("Y-m-d H:i:s");     
		$data_post['COMPANY_CODE'] =  htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$ltc = htmlentities(trim($this->input->post( 'LOCATION_TYPE_CODE' )),ENT_QUOTES,'UTF-8');
		$lc = htmlentities(trim($this->input->post( 'LOCATION_CODE' )),ENT_QUOTES,'UTF-8');
		$ac = htmlentities(trim($this->input->post( 'ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
			
		if(strlen($data_post['JAM_KERJA']) > 20){
			$status = "mohon tutup kotak Jam kerja yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['KMHM_BERANGKAT']) > 20){
			$status = "mohon tutup kotak KMHM Berangkat yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['KMHM_KEMBALI']) > 20){
			$status = "mohon tutup kotak KMHM kembali yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
			$status = "mohon tutup kotak kode tipe lokasi yang terbuka  \r\n"; echo $status;
		}  else if(strlen($data_post['LOCATION_CODE']) > 20){
			$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; echo $status;
		}  else if(strlen($data_post['ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak kode aktivitas yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['MUATAN_JENIS']) > 20){
			$status = "mohon tutup kotak muatan jenis yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['MUATAN_SAT']) > 20){
			$status = "mohon tutup kotak satuan muatan yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['MUATAN_VOL']) > 20){
			$status = "mohon tutup kotak volume yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_VOL']) > 20){
			$status = "mohon tutup kotak volume prestasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_VOL2']) > 20){
			$status = "mohon tutup kotak volume prestasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_SAT']) > 20){
			$status = "mohon tutup kotak satuan prestasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['SUB_ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak sub aktivitas yang terbuka  \r\n"; echo $status;
		} 
		
		/* validasi kode kendaraan #updated 2012-10-23 : ridhu */
		$vh = htmlentities(trim($this->input->post( 'KODE_KENDARAAN' )),ENT_QUOTES,'UTF-8');
		$data_vehicle = $this->model_vehicle_activity->vehicle_validate($vh, $company);  
		if($data_vehicle=0 || $data_vehicle='0' || $data_vehicle==null){ 
			$status = "kode kendaraan : ".$vh." tidak terdapat dalam sistem atau tidak aktif!!\r\n"; echo $status;
		}
		
		$id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$loc_code = substr($lc,0,strlen($lc)-2);
		$project_subtype = substr($lc,-2);
					
		/* ### validasi tanggal ### */
		$TGL_AKTIVITAS=strval($data_post['TGL_AKTIVITAS']);
		if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
			$status="Tanggal Aktifitas tidak boleh kosong"; echo $status;
		} else { 
			if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
				$status= "format tanggal tidak sesuai"; echo $status;
			} else {
				$year_now= $data_post['TAHUN'];//date("Y",time());
				$month_now= $data_post['BULAN'];//date("m",time());
				if(date("Y",strtotime($TGL_AKTIVITAS)) != $year_now || date("m",strtotime($TGL_AKTIVITAS)) != $month_now){
					$status="Tanggal transaksi tidak sama dengan periode berjalan"; echo $status;
				} 
			}	   
		}
		/* ### end validasi tanggal ### */
			
		/* ### Mulai validasi location type code ### */
		if(ord(trim($ltc)) !='45'){
			if(strlen(trim($data_post['LOCATION_TYPE_CODE']))==0 ){
				$status="Type Lokasi tidak boleh kosong"; echo $status;
			} else {
			/* ### cek untuk aktivitas break down atau stand by ### */
			   if($this->input->post( 'ACTIVITY_CODE' ) == '8999995' || $this->input->post( 'ACTIVITY_CODE' ) == '8999996' || $this->input->post( 'ACTIVITY_CODE' ) == '8999997' ){
					if($ac == "") {
						$status="Aktivitas tidak bisa kosong"; echo $status;
					} 
					
					if($this->input->post( 'ACTIVITY_CODE' ) != '8999997'){
					if($KMHM_JUMLAH!="" || $KMHM_JUMLAH > 0){
					 $status="KM / HM pada kendaraan BREAK DOWN dan STAND BY tidak boleh diisi \r\n"; echo $status;
					} 
					
					if($this->input->post( 'JAM_KERJA' ) > 0){
						$status="Jam kerja pada kendaraan BREAK DOWN dan STAND BY tidak boleh diisi \r\n"; echo $status;
					}
					}
					if(empty($status)){ /* start empty */
                                            /* cek closing mingguan */
                                            $periode = substr(str_replace("-","",$data_post['TGL_AKTIVITAS']),0,6);
                                            $close = $this->global_func->cekClosing($periode, $company);
                                            if($close == '1'){
                                                $status = "Periode transaksi bulan ini sudah diclose..";
                                                echo $status;
                                            } else {
                                                /* start cek closing weekly */
                                                $cekClosingWeekly = $this->global_func->cekClosingTransaksi('BK', $data_post['TGL_AKTIVITAS'], $data_post['COMPANY_CODE']);
                                                if($cekClosingWeekly == '1'){
                                                    $status = "Transaksi tanggal " . $data_post['TGL_AKTIVITAS'] ." ini sudah ditutup..";
                                                    echo $status;
                                                } else {
                                                    $insert_id = $this->model_vehicle_activity->insert_vehicle_activity($data_post );
                                                    //echo $insert_id;
                                                }
                                            }
					} /* end empty */
			/* ### end cek break down ### */
						
			/* ### cek untuk aktivitas selain break down atau stand by ### */
				} else {
					 /* ###cek kmhm berangkat harus lebih besar dari kembali ### */
					 if($KMHM_JUMLAH<0){
							echo("Kmhm kembali harus lebih besar atau sama dengan kmhm berangkat.") ;
					 } else { 
							if(strtoupper($ltc) == "PJ"){
								 if(strtoupper($project_subtype) == "TN" || strtoupper($project_subtype) == "LC") {
									$data_lokasi = $this->model_vehicle_activity->lokasi_project_validate($lc, $company);    
					$data_aktivitas = $this->model_vehicle_activity->projectlctn_activity_validate($project_subtype, $ac);
								} else {
									$data_lokasi = $this->model_vehicle_activity->lokasi_project_validate($lc, $company);    
								$data_aktivitas = $this->model_vehicle_activity->project_activity_validate($lc,$ac, $company);
								}
							} else {
								$data_lokasi = $this->model_vehicle_activity->lokasi_validate($lc, $ltc, $company);  
								//if($ac == '8507009'){
								$data_aktivitas = $this->model_vehicle_activity->aktivitas_validate($ac,$lc,$ltc,$company);
								//} else {
								//	$data_aktivitas = $this->model_vehicle_activity->aktivitas_validate($ac,'', $ltc,''); 
								//}
							}
									
							if($data_aktivitas=0 || $data_aktivitas='0' || $data_aktivitas==null){ 
								if($ac == '8507009' || $ac == '8401014' ){
				$status = "kode lokasi : ".$lc.", belum diajukan untuk menggunakan aktivitas pembuatan path mekanis!!\r\n";  
								} else {
									$status = "kode aktivitas : ".$ac.", kosong atau tidak sesuai dengan kode lokasi!!\r\n"; 
								}
								echo $status;
							}
									
							if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
								$status = "kode lokasi : ".$lc.", kosong atau tidak sesuai dengan tipe lokasi!!\r\n"; echo $status;
							}
		
							$tgl=str_replace('-','', $data_post['TGL_AKTIVITAS']);
							$total_jamkerja=$this->model_vehicle_activity->get_total_jamkerja($company,trim($data_post['KODE_KENDARAAN']),$tgl,$data_post['LOCATION_TYPE_CODE'],$data_post['LOCATION_CODE'],$data_post['ACTIVITY_CODE'],$id); 
							$total_jam=0;                                                                     
							if(is_array($total_jamkerja)){
								foreach($total_jamkerja as $row){
									$total_jam=$row['JAM_KERJA'];   
								}
							}                                                                   
		
							if($data_post['JAM_KERJA']== '' ){
								$status = "jam kerja tidak boleh kosong  \r\n"; echo $status;
							}
								
							if(strtoupper(trim($data_post['SATUAN_PRESTASI']))=="HM") {
								if($KMHM_JUMLAH>24){
									$status = "jam kerja dalam 1 tanggal tidak boleh lebih dari 24 Jam  \r\n"; echo $status;    
								}
								if((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )>24){
									$status = "jam kerja dalam 1 tanggal tidak boleh lebih dari 24 Jam  \r\n"; echo $status;    
								} elseif ((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )<0){
									$status = "jam kerja tidak boleh kurang dari 0  \r\n"; echo $status;    
								}    
							} else if (strtoupper(trim($data_post['SATUAN_PRESTASI']))=="KM") {
								if($KMHM_JUMLAH>500){
									$status = "jumlah KM dalam 1 transaksi tidak boleh lebih dari 500 KM!!  \r\n"; echo $status;    
								}
								if((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )>24){
									$status = "jam kerja dalam 1 tanggal tidak boleh lebih dari 24 Jam  \r\n"; echo $status;    
								} elseif ((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )<0){
									$status = "jam kerja tidak boleh kurang dari 0  \r\n"; echo $status;    
								} 
							}   
							
							
							$cekSubType = $this->model_vehicle_activity->cekIsSubAct($ac);
								
							if( $cekSubType > 0 ){
								if( $data_post['SUB_ACTIVITY_CODE'] == ""){
									$status = "Sub aktivitas tidak boleh kosong untuk aktivitas ini \r\n"; echo $status; 
								} else {
									$cekDetail = $this->model_vehicle_activity->getProjectDetail($lc);
									/* $validateSubact = $this->model_vehicle_activity->subactivity_validate($cekDetail, $ac, $data_post['SUB_ACTIVITY_CODE']);	
									
									if( $validateSubact < 1/ ){
										$status = "Sub aktivitas salah atau tidak terdaftar di dalam sistem \r\n"; echo $status;
									}	*/					
								}
							}
							
							/* cek closing mingguan */
							/* edited : ridhu 2014-05-28 */
							
							/* $numWeek = ceil( date( 'j', strtotime( $TGL_AKTIVITAS ) ) / 7 ); 
							$cekClosingWeekly = $this->model_vehicle_activity->cekClosingMingguan($numWeek, $company);
							//if($cekClosingWeekly == '1'){

							
							if ( $TGL_AKTIVITAS < '2014-10-01' ){
								$status = "Transaksi minggu ke " . $numWeek ." ini sudah ditutup..";
								echo $status;
							}
														
							/* end cek closing mingguan */
							
							if(empty($status)){
                                                                 $periode = substr(str_replace("-","",$data_post['TGL_AKTIVITAS']),0,6);
                                                                    $close = $this->global_func->cekClosing($periode, $company);
                                                                    if($close == '1'){
                                                                        $status = "Periode transaksi bulan ini sudah diclose..";
                                                                        echo $status;
                                                                    } else {
                                                                        /* start cek closing weekly */
                                                                        $cekClosingWeekly = $this->global_func->cekClosingTransaksi('BK', $data_post['TGL_AKTIVITAS'], $data_post['COMPANY_CODE']);
                                                                        if($cekClosingWeekly == '1'){
                                                                            $status = "Transaksi tanggal " . $data_post['TGL_AKTIVITAS'] ." ini sudah ditutup..";
                                                                            echo $status;
                                                                        } else {
                                                                            $insert_id = $this->model_vehicle_activity->insert_vehicle_activity( $data_post );
                                                                            //echo $insert_id;
                                                                        }
                                                                    }
                                                            }
							} /* end cek kmhm */
						}
					}  
			} /* ### end validasi location type code ### */
		}
    }
/* update */    
function update_va()
{
	$periode = $this->input->post('TAHUN').$this->input->post('BULAN');
	$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
	$close = $this->global_func->cekClosing($periode, $company);
		
	if($close == '1'){
		echo "Periode transaksi bulan ini sudah diclose..";
	} else {
		$KMHM_BERANGKAT =htmlentities($this->input->post('KMHM_BERANGKAT'),ENT_QUOTES,'UTF-8');
		$KMHM_KEMBALI=htmlentities($this->input->post('KMHM_KEMBALI'),ENT_QUOTES,'UTF-8');
		$KMHM_JUMLAH=$KMHM_KEMBALI-$KMHM_BERANGKAT;
		$numeric_data=array('KMHM_BERANGKAT'=>htmlentities($this->input->post('KMHM_BERANGKAT'),ENT_QUOTES,'UTF-8'),
							'KMHM_KEMBALI'=>htmlentities($this->input->post('KMHM_KEMBALI'),ENT_QUOTES,'UTF-8')
						);
			
		$validate_numeric=$this->validate_numeric($numeric_data);
			
		$data_post['KODE_KENDARAAN'] = htmlentities(trim($this->input->post( 'KODE_KENDARAAN' )),ENT_QUOTES,'UTF-8');
		$data_post['SATUAN_PRESTASI'] = htmlentities($this->input->post( 'SATUAN_PRESTASI' ),ENT_QUOTES,'UTF-8');
		$data_post['BULAN'] = htmlentities($this->input->post( 'BULAN' ),ENT_QUOTES,'UTF-8');
		$data_post['TAHUN'] = htmlentities($this->input->post( 'TAHUN' ),ENT_QUOTES,'UTF-8');
		$data_post['TGL_AKTIVITAS'] = htmlentities($this->input->post( 'TGL_AKTIVITAS' ),ENT_QUOTES,'UTF-8');       
		$data_post['JAM_KERJA'] = floatval(htmlentities($this->input->post( 'JAM_KERJA' ),ENT_QUOTES,'UTF-8'));
		$data_post['KMHM_BERANGKAT'] = htmlentities($this->input->post( 'KMHM_BERANGKAT' ),ENT_QUOTES,'UTF-8');
		$data_post['KMHM_KEMBALI'] = htmlentities($this->input->post( 'KMHM_KEMBALI' ),ENT_QUOTES,'UTF-8');
		$data_post['KMHM_JUMLAH'] = htmlentities($KMHM_JUMLAH,ENT_QUOTES,'UTF-8');
		$data_post['LOCATION_TYPE_CODE'] = htmlentities(trim($this->input->post( 'LOCATION_TYPE_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['LOCATION_CODE'] = htmlentities(trim($this->input->post( 'LOCATION_CODE' )),ENT_QUOTES,'UTF-8');            		
		$data_post['ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['SUB_ACTIVITY_CODE'] = htmlentities(trim($this->input->post( 'SUB_ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
		$data_post['MUATAN_JENIS'] = htmlentities($this->input->post( 'MUATAN_JENIS' ),ENT_QUOTES,'UTF-8');
		$data_post['MUATAN_SAT'] = htmlentities($this->input->post( 'MUATAN_SAT' ),ENT_QUOTES,'UTF-8');    
		$data_post['MUATAN_VOL'] = htmlentities($this->input->post( 'MUATAN_VOL' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_VOL'] = htmlentities($this->input->post( 'PRESTASI_VOL' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_SAT'] = htmlentities($this->input->post( 'PRESTASI_SAT' ),ENT_QUOTES,'UTF-8'); 
		$data_post['PRESTASI_VOL2'] = htmlentities($this->input->post( 'PRESTASI_VOL2' ),ENT_QUOTES,'UTF-8');    
		$data_post['PRESTASI_SAT2'] = htmlentities($this->input->post( 'PRESTASI_SAT2' ),ENT_QUOTES,'UTF-8');    
		$data_post['UPDATE_BY'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data_post['UPDATE_DATE'] = date ("Y-m-d H:i:s");     
		$data_post['COMPANY_CODE'] =  htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$ltc = htmlentities(trim($this->input->post( 'LOCATION_TYPE_CODE' )),ENT_QUOTES,'UTF-8');
		$lc = htmlentities(trim($this->input->post( 'LOCATION_CODE' )),ENT_QUOTES,'UTF-8');
		$ac = htmlentities(trim($this->input->post( 'ACTIVITY_CODE' )),ENT_QUOTES,'UTF-8');
			
		if(strlen($data_post['JAM_KERJA']) > 20){
			$status = "mohon tutup kotak Jam kerja yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['KMHM_BERANGKAT']) > 20){
			$status = "mohon tutup kotak KMHM Berangkat yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['KMHM_KEMBALI']) > 20){
			$status = "mohon tutup kotak KMHM kembali yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['LOCATION_TYPE_CODE']) > 20){
			$status = "mohon tutup kotak kode tipe lokasi yang terbuka  \r\n"; echo $status;
		}  else if(strlen($data_post['LOCATION_CODE']) > 20){
			$status = "mohon tutup kotak kode lokasi yang terbuka  \r\n"; echo $status;
		}  else if(strlen($data_post['ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak kode aktivitas yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['MUATAN_JENIS']) > 20){
			$status = "mohon tutup kotak muatan jenis yang terbuka  \r\n";  echo $status;
		} else if(strlen($data_post['MUATAN_SAT']) > 20){
			$status = "mohon tutup kotak satuan muatan yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['MUATAN_VOL']) > 20){
			$status = "mohon tutup kotak volume yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_VOL']) > 20){
			$status = "mohon tutup kotak volume prestasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_VOL2']) > 20){
			$status = "mohon tutup kotak volume prestasi 2 yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['PRESTASI_SAT']) > 20){
			$status = "mohon tutup kotak satuan prestasi yang terbuka  \r\n"; echo $status;
		} else if(strlen($data_post['SUB_ACTIVITY_CODE']) > 20){
			$status = "mohon tutup kotak sub aktivitas yang terbuka  \r\n"; echo $status;
		} 
		
		$id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$loc_code = substr($lc,0,strlen($lc)-2);
		$project_subtype = substr($lc,-2);
		
		/* validasi kode kendaraan #updated 2012-10-23 : ridhu */
		$vh = htmlentities(trim($this->input->post( 'KODE_KENDARAAN' )),ENT_QUOTES,'UTF-8');
		$data_vehicle = $this->model_vehicle_activity->vehicle_validate($vh, $company);  
		if($data_vehicle=0 || $data_vehicle='0' || $data_vehicle==null){ 
			$status = "kode kendaraan : ".$vh." tidak terdapat dalam sistem atau tidak aktif!!\r\n"; echo $status;
		}
		
		/* ### validasi tanggal ### */
		$TGL_AKTIVITAS=strval($data_post['TGL_AKTIVITAS']);
		if(empty($TGL_AKTIVITAS) || $TGL_AKTIVITAS==null || $TGL_AKTIVITAS==''){
			$status="Tanggal Aktifitas tidak boleh kosong"; echo $status;
		} else { 
			if(date("Ymd",strtotime($TGL_AKTIVITAS)) == '19700101'){
				$status= "format tanggal tidak sesuai"; echo $status;
			} else {
				$year_now= $data_post['TAHUN'];//date("Y",time());
				$month_now= $data_post['BULAN'];//date("m",time());
				if(date("Y",strtotime($TGL_AKTIVITAS)) != $year_now || date("m",strtotime($TGL_AKTIVITAS)) != $month_now){
					$status="Tanggal transaksi tidak sama dengan periode berjalan"; echo $status;
				} 
			}	   
		}
		/* ### end validasi tanggal ### */
			
		/* ### Mulai validasi location type code ### */
		if(ord(trim($ltc)) !='45'){
			if(strlen(trim($data_post['LOCATION_TYPE_CODE']))==0 ){
				$status="Type Lokasi tidak boleh kosong"; echo $status;
			} else {
			/* ### cek untuk aktivitas break down atau stand by ### */
			   if($this->input->post( 'ACTIVITY_CODE' ) == '8999995' || $this->input->post( 'ACTIVITY_CODE' ) == '8999996' || $this->input->post( 'ACTIVITY_CODE' ) == '8999997' ){
					if($ac == "") {
						$status="Aktivitas tidak bisa kosong"; echo $status;
					} /* else {
						if($ac != "STAND BY"){
							if( $ac != "BREAK DOWN"){
								$status="Tipe Lokasi '-' Hanya bisa digunakan untuk BREAK DOWN dan STAND BY... \r\n"; echo $status;
							}
						}
					} */
					if( $this->input->post( 'ACTIVITY_CODE' ) != '8999997') {
					if($KMHM_JUMLAH!="" || $KMHM_JUMLAH > 0){
						$status="KM / HM pada kendaraan BREAK DOWN dan STAND BY tidak boleh diisi \r\n"; echo $status;
					} 
					
					if($this->input->post( 'JAM_KERJA' ) > 0){
						$status="Jam kerja pada kendaraan BREAK DOWN dan STAND BY tidak boleh diisi \r\n"; echo $status;
					}
					
					/* if(str_replace(" ","",$this->input->post( 'LOCATION_CODE' )) != ""){
						$status="Lokasi pada kendaraan BREAK DOWN dan STAND BY tidak boleh diisi \r\n"; echo $status;
					} */
					}
					if(empty($status)){
						//$data_post['LOCATION_TYPE_CODE'] = "-";
                                             $periode = substr(str_replace("-","",$data_post['TGL_AKTIVITAS']),0,6);
                                            $close = $this->global_func->cekClosing($periode, $company);
                                            if($close == '1'){
                                                $status = "Periode transaksi bulan ini sudah diclose..";
                                                echo $status;
                                            } else {
                                                /* start cek closing weekly */
                                                $cekClosingWeekly = $this->global_func->cekClosingTransaksi('BK', $data_post['TGL_AKTIVITAS'], $data_post['COMPANY_CODE']);
                                                if($cekClosingWeekly == '1'){
                                                    $status = "Transaksi tanggal " . $data_post['TGL_AKTIVITAS'] ." ini sudah ditutup..";
                                                    echo $status;
                                                } else {
                                                    $insert_id = $this->model_vehicle_activity->update_vehicle_activity($id, $company, $data_post );
                                                    //echo $insert_id;
                                                }
                                            }
					}
			/* ### end cek break down ### */
						
			/* ### cek untuk aktivitas selain break down atau stand by ### */
				} else {
					 /* ###cek kmhm berangkat harus lebih besar dari kembali ### */
					 if($KMHM_JUMLAH<0){
							echo("Kmhm kembali harus lebih besar atau sama dengan kmhm berangkat.") ;
					 } else { 
							if(strtoupper($ltc) == "PJ"){
								 if(strtoupper($project_subtype) == "TN" || strtoupper($project_subtype) == "LC") {
										$data_lokasi = $this->model_vehicle_activity->lokasi_project_validate($lc, $company);    
								$data_aktivitas = $this->model_vehicle_activity->projectlctn_activity_validate($project_subtype, $ac);
										} else {
											$data_lokasi = $this->model_vehicle_activity->lokasi_project_validate($lc, $company);    
											$data_aktivitas = $this->model_vehicle_activity->project_activity_validate($lc,$ac, $company);
										}
									} else {
										$data_lokasi = $this->model_vehicle_activity->lokasi_validate($lc, $ltc, $company);  
										$data_aktivitas = $this->model_vehicle_activity->aktivitas_validate($ac,$lc,$ltc,$company);
										
									}
									
									if($data_aktivitas=0 || $data_aktivitas='0' || $data_aktivitas==null){ 
										if($ac == '8507009' || $ac == '8401014'){
											$status = "kode lokasi : ".$lc.", belum diajukan untuk menggunakan aktivitas pembuatan path mekanis!!\r\n";  
										} else {
											$status = "kode aktivitas : ".$ac.", kosong atau tidak sesuai dengan kode lokasi!!\r\n"; 
										}
										echo $status;
									}
									
									if($data_lokasi=0 || $data_lokasi='0' || $data_lokasi==null){ 
										$status = "kode lokasi : ".$lc.", kosong atau tidak sesuai dengan tipe lokasi!!\r\n"; 
										echo $status;
									}
									
									/* if($data_path_aktivitas=0 || $data_path_aktivitas='0' || $data_path_aktivitas==null){ 
										$status = "kode lokasi : ".$lc.", belum diajukan untuk menggunakan aktivitas pembuatan path mekanis!!\r\n"; 
										echo $status;
									} */
																		
									$tgl=str_replace('-','', $data_post['TGL_AKTIVITAS']);
									$total_jamkerja=$this->model_vehicle_activity->get_total_jamkerja($company,trim($data_post['KODE_KENDARAAN']),$tgl,$data_post['LOCATION_TYPE_CODE'],$data_post['LOCATION_CODE'],$data_post['ACTIVITY_CODE'],$id); 
									$total_jam=0;                                                                     
									if(is_array($total_jamkerja)){
										foreach($total_jamkerja as $row){
											$total_jam=$row['JAM_KERJA'];   
										}
									}                                                                   
				
									if($data_post['JAM_KERJA']== '' ){
										$status = "jam kerja tidak boleh kosong  \r\n"; echo $status;
									}
										
									if(strtoupper(trim($data_post['SATUAN_PRESTASI']))=="HM") {
										if($KMHM_JUMLAH>24){
											$status = "jam kerja dalam 1 tanggal tidak boleh lebih dari 24 Jam  \r\n"; echo $status;    
										}
										if((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )>24){
											$status = "jam kerja dalam 1 tanggal tidak boleh lebih dari 24 Jam  \r\n"; echo $status;    
										} elseif ((floatval($data_post['JAM_KERJA']) + floatval($total_jam) )<0){
											$status = "jam kerja tidak boleh kurang dari 0  \r\n"; echo $status;    
										}    
									} else if (strtoupper(trim($data_post['SATUAN_PRESTASI']))=="KM") {
										if($KMHM_JUMLAH>500){
											$status = "jumlah KM dalam 1 transaksi tidak boleh lebih dari 500 KM!!  \r\n"; echo $status;    
										}
									}   
									
							$cekSubType = $this->model_vehicle_activity->cekIsSubAct($ac);
								
							if( $cekSubType > 0 ){
								if( $data_post['SUB_ACTIVITY_CODE'] == ""){
									$status = "Sub aktivitas tidak boleh kosong untuk aktivitas ini \r\n"; echo $status; 
								} else {
									$cekDetail = $this->model_vehicle_activity->getProjectDetail($lc);
									/* $validateSubact = $this->model_vehicle_activity->subactivity_validate($cekDetail, $ac, $data_post['SUB_ACTIVITY_CODE']);	
									if( $validateSubact < 1 ){
										$status = "Sub aktivitas salah atau tidak terdaftar di dalam sistem \r\n"; echo $status;
									}			*/			
								}
							}
							
							/* cek closing mingguan */
							/* edited : ridhu 2014-05-28 */
							
							/* $numWeek = ceil( date( 'j', strtotime( $TGL_AKTIVITAS ) ) / 7 );
							$cekClosingWeekly = $this->model_vehicle_activity->cekClosingMingguan($numWeek, $company);
							//if($cekClosingWeekly == '1'){
							
							if ( $TGL_AKTIVITAS < '2014-10-01' ){
								$status = "Transaksi minggu ke " . $numWeek ." ini sudah ditutup..";
								echo $status;
							} */
							
							/* end cek closing mingguan */
							
                                                            if(empty($status)){
                                                                 $periode = substr(str_replace("-","",$data_post['TGL_AKTIVITAS']),0,6);
                                                                $close = $this->global_func->cekClosing($periode, $company);
                                                                if($close == '1'){
                                                                    $status = "Periode transaksi bulan ini sudah diclose..";
                                                                    echo $status;
                                                                } else {
                                                                    /* start cek closing weekly */
                                                                    $cekClosingWeekly = $this->global_func->cekClosingTransaksi('BK', $data_post['TGL_AKTIVITAS'], $data_post['COMPANY_CODE']);
                                                                    if($cekClosingWeekly == '1'){
                                                                        $status = "Transaksi tanggal " . $data_post['TGL_AKTIVITAS'] ." ini sudah ditutup..";
                                                                        echo $status;
                                                                    } else {
                                                                        $insert_id = $this->model_vehicle_activity->update_vehicle_activity($id, $company, $data_post );
                                                                        //echo $insert_id;
                                                                    }
                                                                }
                                                            }
							} /* end cek kmhm */
						}
					}  
			} /* ### end validasi location type code ### */
		}
    }
    
    /* delete */
    function delete()
    {
                $id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
		$periode = substr(str_replace("-","",$this->input->post('TGL_AKTIVITAS')),0,6);
		$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		//$periode = substr(str_replace("-","",$data_post['COMPANY_CODE']),0,6);
                                            $close = $this->global_func->cekClosing($periode, $company);
                                            if($close == '1'){
                                                $status = "Periode transaksi bulan ini sudah diclose..";
                                                echo $status;
                                            } else {
                                                /* start cek closing weekly */
                                                $cekClosingWeekly = $this->global_func->cekClosingTransaksi('BK', $this->input->post('TGL_AKTIVITAS'), $company);
                                                if($cekClosingWeekly == '1'){
                                                    $status = "Transaksi tanggal " . $this->input->post('TGL_AKTIVITAS') ." ini sudah ditutup..";
                                                    echo $status;
                                                } else {
                                                    $this->model_vehicle_activity->delete_vehicle_activity($id, $company);  
                                                }
			/* $id = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
			$company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
			$tdate = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
			
			$numWeek = ceil( date( 'j', strtotime( $tdate ) ) / 7 ); 
			$cekClosingWeekly = $this->model_vehicle_activity->cekClosingMingguan($numWeek, $company);
			
			if($cekClosingWeekly == '1'){
if ( $TGL_AKTIVITAS < '2014-10-01' ){
								$status = "Transaksi minggu ke " . $numWeek ." ini sudah ditutup..";
								echo $status;
							} else { */
					 
			// }

		}
    }
         
    //autocomplete
    function kode_kend(){
        $cv = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data_kend = $this->model_vehicle_activity->kode_kend($cv, $company);
        
        $kendaraan = array();
        foreach($data_kend as $row)
            {
                $kendaraan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['VEHICLECODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['VEHICLECODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" .
                    htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8')).
                    '", sat_pres:"'.str_replace('"','\\"',htmlentities($row['SATUAN_PRESTASI'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$kendaraan).']'; exit; 
    }
    
    //location type
    function location_type(){
        $data_loctype = $this->model_vehicle_activity->location_type();
        
        $loctype = array();
        foreach($data_loctype as $row)
            {
                $loctype[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['LOCATION_TYPE_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['LOCATION_TYPE_CODE'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$loctype).']'; exit; 
    }
    
    //location code
    function location(){
        $q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $loc = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        if($loc == 'PJ'){
        $data_location = $this->model_vehicle_activity->location_pj($q,$company);
        } else {
        $data_location = $this->model_vehicle_activity->location($loc, $q, $company);
        }
        
        $data = array();
        $location = array();
        foreach($data_location as $row)
            {
                $location[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).'",res_name:"'.
                str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8')).'",res_dl:"'.
                str_replace('"','\\"',htmlentities($row['LOCATION_CODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['DESCRIPTION'],ENT_QUOTES,'UTF-8')).'"}';
            }
          echo '['.implode(',',$location).']'; exit;
    }
    
    //activity
    function activity(){
        $ac = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $lc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
		$q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
        $activity = array();
                
        if($ac == 'PJ'){
            $loc_ac = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');        
            $project_subtype = substr($lc,-2);
            
            $data_enroll = '';
            if($project_subtype == "TN" || $project_subtype == "LC") {
                $data_enroll = $this->model_vehicle_activity->activity_pj_lctn($loc_ac,$project_subtype);
            } else {
                $data_enroll = $this->model_vehicle_activity->activity_pj($lc, $company);
            }
            
            if(is_array($data_enroll)){
                foreach($data_enroll as $row){
                    $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
                    '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
					'",res_sat1:"'.str_replace('"','\\"',htmlentities($row['UNIT1'],ENT_QUOTES,'UTF-8')).
					'",res_sat2:"'.str_replace('"','\\"',htmlentities($row['UNIT2'],ENT_QUOTES,'UTF-8')).
                    '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8'). 
                    " - " .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            }
        } 
        else 
        {
            $data_enroll = $this->model_vehicle_activity->activity($ac, $lc, $q);
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                        $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
                        '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
						'",res_sat1:"'.str_replace('"','\\"',htmlentities($row['UNIT1'],ENT_QUOTES,'UTF-8')).
						'",res_sat2:"'.str_replace('"','\\"',htmlentities($row['UNIT2'],ENT_QUOTES,'UTF-8')).
                        '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8').
                         "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            }
        }
    }
    
	function subactivity(){
        $ac = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $lc = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
		$account = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
		$q = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        
		$subActivity = array();
		if($ac == 'PJ'){
			$cekDetail = $this->model_vehicle_activity->getProjectDetail($lc); 
			if($cekDetail!= ""){
				$c = $this->model_vehicle_activity->getSubActivty($cekDetail,"");
				foreach($c as $row){
                    $subActivity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_CODE'],ENT_QUOTES,'UTF-8')).
                    '",res_name:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_NAME'],ENT_QUOTES,'UTF-8')).
                    '",res_d:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_CODE'],ENT_QUOTES,'UTF-8'). 
                    "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['INFRAS_ACTIVITY_NAME'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$subActivity).']'; exit; 
			}
		} else {
				$c = $this->model_vehicle_activity->getSubActivty("",$account);
				foreach($c as $row){
                    $subActivity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_CODE'],ENT_QUOTES,'UTF-8')).
                    '",res_name:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_NAME'],ENT_QUOTES,'UTF-8')).
                    '",res_d:"'.str_replace('"','\\"',htmlentities($row['INFRAS_ACTIVITY_CODE'],ENT_QUOTES,'UTF-8'). 
                    "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['INFRAS_ACTIVITY_NAME'],ENT_QUOTES,'UTF-8')).'",}';
                }
                echo '['.implode(',',$subActivity).']'; exit; 
		}
		     
        /* if($ac == 'PJ'){
            $loc_ac = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');        
            $project_subtype = substr($lc,-2);
            
            $data_enroll = '';
            if($project_subtype == "TN" || $project_subtype == "LC") {
                $data_enroll = $this->model_vehicle_activity->activity_pj_lctn($loc_ac,$project_subtype);
            } else {
                $data_enroll = $this->model_vehicle_activity->activity_pj($lc, $company);
            }
            
            if(is_array($data_enroll)){
                foreach($data_enroll as $row){
                    $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
                    '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
                    '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8'). 
                    "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            }
        } 
        else 
        {
            $data_enroll = $this->model_vehicle_activity->activity($ac, $lc, $q);
            if(is_array($data_enroll))
            {
                foreach($data_enroll as $row)
                {
                        $activity[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8')).
                        '",res_name:"'.str_replace('"','\\"',htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).
                        '",res_d:"'.str_replace('"','\\"',htmlentities($row['ACCOUNTCODE'],ENT_QUOTES,'UTF-8').
                         "&nbsp;&nbsp; - &nbsp;&nbsp;" .htmlentities($row['COA_DESCRIPTION'],ENT_QUOTES,'UTF-8')).'",}';
                }
                 echo '['.implode(',',$activity).']'; exit;    
            }
        } */
    }
    //satuan
    function satuan(){
        $data_satuan = $this->model_vehicle_activity->satuan();

        if(is_array($data_satuan))
        {
            $satuan = array();
            foreach($data_satuan as $row)
            {
                $satuan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['UNIT_CODE'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['UNIT_DESC'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['UNIT_CODE'],ENT_QUOTES,'UTF-8'). "&nbsp;&nbsp; - &nbsp;&nbsp;" 
                .htmlentities($row['UNIT_DESC'],ENT_QUOTES,'UTF-8')).'"}';
            }
            echo '['.implode(',',$satuan).']'; exit;   
        }  
    }
    
    //jenis muatan
    function muatan(){
		$q = $_REQUEST['q'];
        $data_muatan = $this->model_vehicle_activity->muatan($q);
       
        if(is_array($data_muatan)){
            $muatan = array();
            foreach($data_muatan as $row){
                $muatan[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['KODE_MUATAN'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['NAMA_MUATAN'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['KODE_MUATAN'],ENT_QUOTES,'UTF-8'). 
                "&nbsp; - &nbsp;" .htmlentities($row['NAMA_MUATAN'],ENT_QUOTES,'UTF-8')).
                '", res_sat:"'.htmlentities($row['SATUAN']).'"}';
            }
            echo '['.implode(',',$muatan).']'; exit;    
        }
         
    }
    
    //################# validation ########################
    function get_latest_kmhm()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');  
        $kode=htmlentities($this->uri->segment('3'),ENT_QUOTES,'UTF-8');
        $tgl=str_replace('-','', htmlentities($this->uri->segment('4'),ENT_QUOTES,'UTF-8'));
        $type=str_replace('-','', htmlentities($this->uri->segment('5'),ENT_QUOTES,'UTF-8'));
        $location=htmlentities($this->uri->segment('6'),ENT_QUOTES,'UTF-8');
        $act=$this->uri->segment('7');
        $opt=htmlentities($this->uri->segment('8'),ENT_QUOTES,'UTF-8');
        
        //echo $act;
        $kmhm=$this->model_vehicle_activity->get_latest_kmhm($company,$kode,$tgl,$type,$location,$act,$opt);
        $kmhm_val=null;
        if(is_array($kmhm)){
            foreach($kmhm as $row) {
                $kmhm_val=$row['KMHM_KEMBALI'];
            }    
        } else {
          $kmhm_val=$kmhm;  
        }
         
        echo trim(floatval($kmhm_val));   
    }
    
    function validate_numeric($data)
    {
        $numeric=$data;
        $result='';
        if(is_array($data))
        {
            while(list($key,$val)=each($data))
            {
                if(trim($val)=="" || $val==null){
                    $val=0;
                }
                if((! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$val))){
                    $result='false';
                    break;
                } else{
                    $result='true';   
                }
            }
        } else {
            if(trim($numeric)=="" || $numeric==null) {
                $val=0;
            }
            
            if (! preg_match('/(^-*\d+$)|(^-*\d+\.\d+$)/',$numeric)){
                $result='false';   
            } else {
                $result='true';
            }    
        }
        return $result;   
    }
    
    function validate_jam($data)
    {
        $hour=$data;
        $result='';
        if(is_array($data)) {
            while(list($key,$val)=each($data)){
                if(trim($val)=="" || $val==null){
                    $val=0;
                }
                if((! preg_match('/^\d\d(:[0-9]\d){1,2}$/',$val))) {
                    $result='false';
                    break;
                } else {
                    $result='true';   
                }
            }  
        } else {
            if(preg_match('/^\d\d(:[0-9]\d){1,2}$/', trim($hour))){
                $result='true' ;
            } else {
                 $result='false';
            }    
        }
        return $result;    
    }
    
    //######## Update 15 Des 2010 ##########
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
		
		$headers .= "PRESTASI_VOL 2 \t";
        $headers .= "PRESTASI_SAT 2 \t";
        
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
			$line .= str_replace('"', '""',$row['PRESTASI_VOL2'])."\t";
            $line .= str_replace('"', '""',$row['PRESTASI_SAT2'])."\t";
            $data .= trim($line)."\n";        
        }
        
        $footer .= " - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t - \t";        
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BK_KENDARAAN_".$company."_".$bln."_".$thn.".xls");
        echo "$judul\n$headers\n$data";  
    }
}

?>