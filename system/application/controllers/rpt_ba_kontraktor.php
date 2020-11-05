<?
class rpt_ba_kontraktor extends Controller 
{
	function rpt_ba_kontraktor ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_ba' ); 
        $this->load->model('model_c_user_auth'); 
        $this->lastmenu="rpt_ba_pks";
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->load->helper('file');
		require_once(APPPATH . 'libraries/fpdf_table.php');
		require_once(APPPATH . 'libraries/header_footer.inc');
	    require_once(APPPATH . 'libraries/table_def.inc');
	}
	
	function index()
    {
		$view = "rpt_ba_kontraktor";
		$data = array();
		$data['judul_header'] = "Berita Acara Pembayaran Progres Kontraktor";
		$data['js'] = $this->js_ba_kontraktor();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
       	$data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		$data['KONTRAKTOR'] = $this->global_func->dropdownlist2("KONTRAKTOR","m_kontraktor","NAMA_KONTRAKTOR","KODE_KONTRAKTOR","COMPANY_CODE = '".$this->session->userdata('DCOMPANY')."'",NULL, NULL,NULL,"select");
		if ($data['login_id'] == TRUE){
			//if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				show($view, $data);
			//} 
		} else {
			redirect('login');
		}
	}
	
	function js_ba_kontraktor(){
		
		$js = "$(function() {
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
					$('#TO').datepicker({dateFormat:'yy-mm-dd'});
				});
			
			jQuery('#submitdata').click(function (){
			var kontraktor = $('#KONTRAKTOR').val(); //$('#afd').val()
			var tfrom = document.getElementById('FROM').value;
			var elem = tfrom.split('-');
			from = elem[0]+elem[1]+elem[2];
							
			var tto = document.getElementById('TO').value;
			var elem2 = tto.split('-');
			to = elem2[0]+elem2[1]+elem2[2];
						
			var period = to - from;
		
			if ( period > 0 ){
					
					if(kontraktor == ''){ 
							alert('pilih kontraktor terlebih dahulu!!') 
					} else {
							var jns_laporan = $('#jns_laporan').val();	
							if ( jns_laporan == 'html'){
								var urls = url + 'rpt_ba_kontraktor/ba_kontraktor_prev/' + kontraktor + '/' + from  + '/' + to ; 
								$('#frame').attr('src',urls);
							} else if ( jns_laporan == 'excell'){
								var urls = url + 'rpt_ba_kontraktor/ba_kontraktor_xls/' + kontraktor + '/' + from  + '/' + to ; 
								$('#frame').attr('src',urls);
							} else if ( jns_laporan == 'pdf'){
								var urls = url + 'rpt_ba_kontraktor/ba_kontraktor_pdf/' + kontraktor + '/' + from  + '/' + to ; 
								$('#frame').attr('src',urls);
							}
					}
			} else {
				alert('rentang periode salah!!');
				return false;
			}	
		});";
		return $js;
	}
	
	function ba_kontraktor_prev() {
		$kontraktor = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
				 	
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$tabel = "";
		$output = "";
		$nmkontraktor = "";
		
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
		$tabel .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }";
		$tabel .= ".tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }";
		$tabel .= ".tbl_2 { font-size: 12px;color:#678197;}	.content { font-size: 12px;color:#678197; }	</style>";
		$tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'>";
		
		
		$data_kontraktor = $this->model_rpt_ba->ba_kontraktor($kontraktor, $from, $to, '', '', $company);
		$tmpAccode='';
		$ttlVol = 0;
		$ttlNilai = 0;
		$totalPPH = 0;
		$totalBersih = 0;
		foreach ( $data_kontraktor as $row){
			$nmkontraktor = $row['NAMA_KONTRAKTOR'];
			if ($tmpAccode!=''){
               	if ($row['ACTIVITY_CODE'] != $tmpAccode){
					$datatotal = $this->model_rpt_ba->ba_kontraktor($kontraktor, $from, $to,  $tmpAccode, "total", $company);
                    foreach($datatotal as $rows){
                       $output .= "<tr><td class='tbl_th' align='left' style='padding-left:20px' colspan='4'><strong>TOTAL 
							 ".$rows['ACTIVITY_CODE']." - ".$rows['DESKRIPSI']."</strong></td>";
					   $output .= "<td class='tbl_th' align='center' style='padding-left:5px'>".$row['MUATAN']."</td>";
					   $output .= "<td class='tbl_th' align='center' style='padding-left:5px'>".$row['HSL_SATUAN']."</td>";
					   $output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($rows['VOL'],2,',','.')."</td>";
					   $output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($rows['TARIF_SATUAN'],2,',','.')."</td>";
					   $output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($rows['NILAI'],2,',','.')."</td>";
					   $output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($rows['PPH23'],2,',','.')."</td>";
					   $output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($rows['BERSIH_TERIMA'],2,',','.')."</td></tr>";
                     }
                     $datatotal=''; 
                 }  
           }
			
		$output .= "<tr><td class='tbl_th' align='center' style='padding-left:5px'>".$row['ACTIVITY_CODE']."</td>";
		$output .= "<td class='tbl_th' align='left' style='padding-left:5px'>".$row['DESKRIPSI']."</td>";
		$output .= "<td class='tbl_th' align='left' style='padding-left:5px'>".$row['LOCATION_CODE']."</td>";
		$output .= "<td class='tbl_th' align='left' style='padding-left:5px'>".strtoupper($row['DESKRIPSI_LOKASI'])."</td>";
		$output .= "<td class='tbl_th' align='center' style='padding-left:5px'>".$row['MUATAN']."</td>";
		$output .= "<td class='tbl_th' align='center' style='padding-left:5px'>".$row['HSL_SATUAN']."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($row['VOL'],2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($row['TARIF_SATUAN'],2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($row['NILAI'],2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($row['PPH23'],2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($row['BERSIH_TERIMA'],2,',','.')."</td></tr>";
		$tmpAccode = $row['ACTIVITY_CODE'];
			
		$ttlVol = $ttlVol + $row['VOL'];
		$ttlNilai = $ttlNilai + $row['NILAI'];
		$totalPPH = $totalPPH + $row['PPH23'];
		$totalBersih = $totalBersih + $row['BERSIH_TERIMA'] ;
	}
	
	$tabel .= "<strong>BERITA ACARA PEMBAYARAN PROGRESS KONTRAKTOR</strong></td></tr>";
		$tabel .= "<tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ ".$company." / KONTRAKTOR / ".strtoupper($bulanr)." / ".$tahun." </strong></td>";
		$tabel .= "</tr><tr><td colspan='3' align='center'><strong>PERIODE : ". substr($from,6,2)."-". substr($from,4,2)."-". substr($from,0,4) . " s/d " . substr($to,6,2)."-". substr($to,4,2)."-". substr($to,0,4) . "</strong></td>";
		$tabel .= "</tr><tr><td colspan='3'>&nbsp;</td></tr>";
		$tabel .= "<tr><td width='120px;'>PERUSAHAAN</td><td> : </td><td> PT. ".$this->session->userdata('DCOMPANY_NAME')."</td>";
		$tabel .= "<tr><td width='120px;'>KONTRAKTOR</td><td> : </td><td>".strtoupper($nmkontraktor)."</td>";
		$tabel .= "<tr><td width='120px;'>No SPK</td><td> : </td><td></td>";
		$tabel .= "</tr></table>";
		
		$tabel .= "<table width='95%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' colspan='2'>ACTIVITY</th><th class='tbl_th' colspan='2' >LOKASI</th>";
    	$tabel .= "<th class='tbl_th' rowspan='2'>JENIS MUATAN</th><th class='tbl_th' rowspan='2'>SAT</th><th class='tbl_th' rowspan='2'>VOL</th>";
    	$tabel .= "<th class='tbl_th' rowspan='2'>TARIF / SATUAN</th><th class='tbl_th' rowspan='2'>NILAI</th>";
 		$tabel .= "<th class='tbl_th' rowspan='2'>PPH23 (2-4%)</th><th class='tbl_th' rowspan='2'>BERSIH TERIMA</th></tr>";
		$tabel .= "<tr><th class='tbl_th'>KODE</th><th class='tbl_th'>DESKRIPSI</th><th class='tbl_th'>KODE</th><th class='tbl_th'>DESKRIPSI</th></tr>";
		
		$output .= "<tr><td class='tbl_th' align='left' style='padding-left:20px' colspan='6'><strong>TOTAL</strong></td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($ttlVol,2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'> - </td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($ttlNilai,2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($totalPPH,2,',','.')."</td>";
		$output .= "<td class='tbl_th' align='right' style='padding-right:5px'>".number_format($totalBersih,2,',','.')."</td></tr>";
		$output .= "</table>";
		
		$tabel = $tabel . $output;
		echo $tabel;
	}
	
	function ba_kontraktor_xls() {
		$kontraktor = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
				 	
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';	
		$nmkontraktor = '';
		$obj =& get_instance(); 
		
		$data_kontraktor = $this->model_rpt_ba->ba_kontraktor($kontraktor, $from, $to, '', '', $company);
		foreach ( $data_kontraktor as $rows){
			$nmkontraktor = $rows['NAMA_KONTRAKTOR'];
		}
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
        $judul .= "BERITA ACARA PEMBAYARAN PROGRESS KONTRAKTOR\n";
        $judul .= "PERIODE : ". substr($from,6,2)."-". substr($from,4,2)."-". substr($from,0,4) . " s/d " . substr($to,6,2)."/". substr($to,4,2)."-". substr($to,0,4) . "\n";
        $judul .= "\n";
		$judul .= "PERUSAHAAN \t". $this->session->userdata('DCOMPANY_NAME') . "\n" ;
		$judul .= "KONTRAKTOR \t". strtoupper($nmkontraktor) . "\n" ;
		
		$headers .= "KODE AKTIVITAS \t";
        $headers .= "DESKRIPSI AKTIVITAS \t";
        $headers .= "KODE LOKASI \t";
        $headers .= "DESKRIPSI LOKASI \t";
        $headers .= "JENIS MUATAN \t";
        $headers .= "SATUAN \t";
        $headers .= "VOLUME \t";
        $headers .= "TARIF / SATUAN \t";
        $headers .= "NILAI \t";
		$headers .= "POTONGAN PPH23 \t";
		$headers .= "BERSIH TERIMA \t";
			
		$ttlVol = 0;
		$ttlNilai = 0;
		$totalPPH = 0;
		$totalBersih = 0;
		foreach ( $data_kontraktor as $row){
			$line = '';
			$line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t";	
			$line .= str_replace('"', '""',trim($row['DESKRIPSI']))."\t";
			$line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t";
			$line .= str_replace('"', '""',trim($row['DESKRIPSI_LOKASI']))."\t";
			$line .= str_replace('"', '""',trim($row['MUATAN']))."\t";
			$line .= str_replace('"', '""',trim($row['HSL_SATUAN']))."\t";
			$line .= str_replace('"', '""',trim($row['VOL']))."\t";
			$line .= str_replace('"', '""',trim($row['TARIF_SATUAN']))."\t";
			$line .= str_replace('"', '""',trim($row['NILAI']))."\t";
			$line .= str_replace('"', '""',trim($row['PPH23']))."\t";
			$line .= str_replace('"', '""',trim($row['BERSIH_TERIMA']))."\t";
			$data .= trim($line)."\n";
			
			$ttlVol = $ttlVol + $row['VOL'];
			$ttlNilai = $ttlNilai + $row['NILAI'];
			$totalPPH = $totalPPH + $row['PPH23'];
			$totalBersih = $totalBersih + $row['BERSIH_TERIMA'] ;
		}
		
		$footer .= "-\t";	
		$footer .= "-\t";
		$footer .= "-\t";
		$footer .= "\t";
		$footer .= "-\t";
		$footer .= "-\t";
		$footer .= str_replace('"', '""',$ttlVol)."\t";
		$footer .= "-\t";
		$footer .= str_replace('"', '""',$ttlNilai)."\t";
		$footer .= str_replace('"', '""',$totalPPH)."\t";
		$footer .= str_replace('"', '""',$totalBersih)."\t";
		
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=PRG_KONTRAKTOR_".$company."_".$tahun.$bulan.".xls");
        echo "$judul\n$headers\n$data";
	}
	
	function ba_kontraktor_pdf()
	{
		if ($this->session->userdata('logged_in')!=TRUE){
			redirect('login');
		}

		$kontraktor = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
				 	
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		//$data_bibitan = $this->model_rpt_ba->ba_bibitan_afd($from, $to, $rkp, $company);
		
		$nmkontraktor = '';
		$data_kontraktor = $this->model_rpt_ba->ba_kontraktor($kontraktor, $from, $to, '', '', $company);
		foreach ( $data_kontraktor as $rows){
			$nmkontraktor = $rows['NAMA_KONTRAKTOR'];
		}
		
		$total = 0; $total_hk = 0; $total_rp=0; $total_qty=0;
		
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->SetMargins(5, 10);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		
		require_once(APPPATH . 'libraries/ba/header_ba_kontraktor.inc');
		require_once(APPPATH . 'libraries/ba/table_border.inc');
		
		$columns = 11; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader[$i]['TEXT'] = "ACTIVITY";
				$aSimpleHeader[$i]['WIDTH'] = 25;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 1) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] =60;

			}
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "LOKASI";
				if($company == "TPAI"){
					$aSimpleHeader[$i]['WIDTH'] = 25;
				} else {
					$aSimpleHeader[$i]['WIDTH'] = 35;
				}
				
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "";
				if($company == "TPAI"){
					$aSimpleHeader[$i]['WIDTH'] = 70;
				} else {
					$aSimpleHeader[$i]['WIDTH'] = 60;
				}
			}
			if($i == 4) {
				if($company == "TPAI"){
					$aSimpleHeader[$i]['TEXT'] = "KET";
				} else {
					$aSimpleHeader[$i]['TEXT'] = "JENIS MUATAN";
				}
				$aSimpleHeader[$i]['WIDTH'] = 20;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "SAT";
				$aSimpleHeader[$i]['WIDTH'] = 15;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = "VOL";
				$aSimpleHeader[$i]['WIDTH'] = 20;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "TARIF / SATUAN";
				$aSimpleHeader[$i]['WIDTH'] = 27;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = "NILAI";
				$aSimpleHeader[$i]['WIDTH'] = 27;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 9) {
				$aSimpleHeader[$i]['TEXT'] = "PPH23 (2-4%)";
				$aSimpleHeader[$i]['WIDTH'] = 27;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 10) {
				$aSimpleHeader[$i]['TEXT'] = "BERSIH TERIMA";
				$aSimpleHeader[$i]['WIDTH'] = 27;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = "KODE";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 1) {
				$aSimpleHeader2[$i]['TEXT'] = "DESKRIPSI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 2) {
				$aSimpleHeader2[$i]['TEXT'] = "KODE";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader2[$i]['TEXT'] = "DESKRIPSI";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = "";	
				$aSimpleHeader2[$i]['WIDTH'] = 10;			
			}
			
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			
		}
		
		$aHeader = array( $aSimpleHeader, $aSimpleHeader2);
		
		$pdf->tbSetHeaderType($aHeader, TRUE);
		
		$pdf->tbDrawHeader();
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		$tmpAccode='';
		$ttlVol = 0;
		$ttlNilai = 0;
		$totalPPH = 0;
		$totalBersih = 0;
		
		foreach ( $data_kontraktor as $row){
			
			$nmkontraktor = $row['NAMA_KONTRAKTOR'];
			if ($tmpAccode!=''){
               if ($row['ACTIVITY_CODE'] != $tmpAccode){
				   
					$datatotal = $this->model_rpt_ba->ba_kontraktor($kontraktor, $from, $to,  $tmpAccode, "total", $company);
			        foreach($datatotal as $rows){
						$data2 = array();
						$data2[0]['TEXT'] = "TOTAL ".$rows['ACTIVITY_CODE']." - ".$rows['DESKRIPSI'];
						$data2[0]['COLSPAN'] = 4;
						$data2[4]['TEXT'] = $rows['MUATAN'];
						$data2[5]['TEXT'] = $rows['HSL_SATUAN'];
						$data2[6]['TEXT'] = number_format($rows['VOL'],2,',','.')."  ";
						$data2[7]['TEXT'] = number_format($rows['TARIF_SATUAN'],2,',','.')."  ";
						$data2[8]['TEXT'] = number_format($rows['NILAI'],2,',','.')."  ";
						$data2[9]['TEXT'] = number_format($rows['PPH23'],2,',','.')."  ";
						$data2[10]['TEXT'] = number_format($rows['BERSIH_TERIMA'],2,',','.')."  ";
						
						$data2[3]['T_ALIGN'] = "L"; $data2[4]['T_ALIGN'] = "C"; $data2[5]['T_ALIGN'] = "C";
						$data2[6]['T_ALIGN'] = "R"; $data2[7]['T_ALIGN'] = "R"; $data2[8]['T_ALIGN'] = "R";
						$data2[9]['T_ALIGN'] = "R"; $data2[10]['T_ALIGN'] = "R";
						
						$data2[0]['T_SIZE'] = 9; $data2[1]['T_SIZE'] = 9; $data2[2]['T_SIZE'] = 9;
						$data2[3]['T_SIZE'] = 9; $data2[4]['T_SIZE'] = 9; $data2[5]['T_SIZE'] = 9;
						$data2[6]['T_SIZE'] = 9; $data2[7]['T_SIZE'] = 9; $data2[8]['T_SIZE'] = 9;
						$data2[9]['T_SIZE'] = 9; $data2[10]['T_SIZE'] = 9;
						
						$data2[0]['LN_SIZE'] = 5; $data2[1]['LN_SIZE'] = 5; $data2[2]['LN_SIZE'] = 5;
						$data2[3]['LN_SIZE'] = 5; $data2[4]['LN_SIZE'] = 5; $data2[5]['LN_SIZE'] = 5;
						$data2[6]['LN_SIZE'] = 5; $data2[7]['LN_SIZE'] = 5; $data2[8]['LN_SIZE'] = 5;
                     }
                     $datatotal=''; 
					 $pdf->tbDrawData($data2);
                }  
            }
			$data = Array();
			$data[0]['TEXT'] = $row['ACTIVITY_CODE'];
			$data[1]['TEXT'] = "  ". $row['DESKRIPSI'];
			$data[1]['T_ALIGN'] = "L";
			$data[2]['TEXT'] = $row['LOCATION_CODE'];
			$data[3]['TEXT'] = "  ". $row['DESKRIPSI_LOKASI'];
			$data[4]['TEXT'] = $row['MUATAN'];
			$data[5]['TEXT'] = $row['HSL_SATUAN'];
			$data[6]['TEXT'] = number_format($row['VOL'],2,',','.')."  ";
			$data[7]['TEXT'] = number_format($row['TARIF_SATUAN'],2,',','.')."  ";
			$data[8]['TEXT'] = number_format($row['NILAI'],2,',','.')."  ";
			$data[9]['TEXT'] = number_format($row['PPH23'],2,',','.')."  ";
			$data[10]['TEXT'] = number_format($row['BERSIH_TERIMA'],2,',','.')."  ";
			
			$data[3]['T_ALIGN'] = "L";
			$data[4]['T_ALIGN'] = "C";
			$data[5]['T_ALIGN'] = "C";
			$data[6]['T_ALIGN'] = "R";
			$data[7]['T_ALIGN'] = "R";
			$data[8]['T_ALIGN'] = "R";
			$data[9]['T_ALIGN'] = "R";
			$data[10]['T_ALIGN'] = "R";
			
			$data[0]['T_SIZE'] = 9;
			$data[1]['T_SIZE'] = 9;
			$data[2]['T_SIZE'] = 9;
			$data[3]['T_SIZE'] = 9;
			$data[4]['T_SIZE'] = 9;
			$data[5]['T_SIZE'] = 9;
			$data[6]['T_SIZE'] = 9;
			$data[7]['T_SIZE'] = 9;
			$data[8]['T_SIZE'] = 9;
			$data[9]['T_SIZE'] = 9;
			$data[10]['T_SIZE'] = 9;
			
			$data[0]['LN_SIZE'] = 5;
			$data[1]['LN_SIZE'] = 5;
			$data[2]['LN_SIZE'] = 5;
			$data[3]['LN_SIZE'] = 5;
			$data[4]['LN_SIZE'] = 5;
			$data[5]['LN_SIZE'] = 5;
			$data[6]['LN_SIZE'] = 5;
			$data[7]['LN_SIZE'] = 5;
			$data[8]['LN_SIZE'] = 5;
		
			$pdf->tbDrawData($data);
				
			$tmpAccode = $row['ACTIVITY_CODE'];
			
			$ttlVol = $ttlVol + $row['VOL'];
			$ttlNilai = $ttlNilai + $row['NILAI'];
			$totalPPH = $totalPPH + $row['PPH23'];
			$totalBersih = $totalBersih + $row['BERSIH_TERIMA'] ;
	}
	
		/* foreach ($data_bibitan as $row)
		{
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
			} else {
				$rp_satuan = $realisasi;
			}
			
			$data = Array();
			$data[0]['TEXT'] = $row['ACCOUNTCODE'];
			$data[1]['TEXT'] = $row['COA_DESCRIPTION'];
			$data[1]['T_ALIGN'] = "L";
			$data[2]['TEXT'] = $row['UNIT1'];
			$data[3]['TEXT'] = $row['HASIL_KERJA'];
			$data[4]['TEXT'] = $row['HASIL_KERJA'];
			$data[5]['TEXT'] = number_format($realisasi,2,'.',',');
			$data[6]['TEXT'] = number_format($realisasi,2,'.',',');
			$data[7]['TEXT'] = number_format($rp_satuan,2,'.',',');
			$data[8]['TEXT'] = number_format($rp_satuan,2,'.',',');
			
			$data[3]['T_ALIGN'] = "R";
			$data[4]['T_ALIGN'] = "R";
			$data[5]['T_ALIGN'] = "R";
			$data[6]['T_ALIGN'] = "R";
			$data[7]['T_ALIGN'] = "R";
			$data[8]['T_ALIGN'] = "R";
			
			$data[0]['T_SIZE'] = 9;
			$data[1]['T_SIZE'] = 9;
			$data[2]['T_SIZE'] = 9;
			$data[3]['T_SIZE'] = 9;
			$data[4]['T_SIZE'] = 9;
			$data[5]['T_SIZE'] = 9;
			$data[6]['T_SIZE'] = 9;
			$data[7]['T_SIZE'] = 9;
			$data[8]['T_SIZE'] = 9;
			
			$data[0]['LN_SIZE'] = 5;
			$data[1]['LN_SIZE'] = 5;
			$data[2]['LN_SIZE'] = 5;
			$data[3]['LN_SIZE'] = 5;
			$data[4]['LN_SIZE'] = 5;
			$data[5]['LN_SIZE'] = 5;
			$data[6]['LN_SIZE'] = 5;
			$data[7]['LN_SIZE'] = 5;
			$data[8]['LN_SIZE'] = 5;
		
			$pdf->tbDrawData($data);
		} */
		$data_test=array();
		$data_test[0]['TEXT'] = " TOTAL PEMBAYARAN";
		$data_test[0]['COLSPAN'] = 10;
		$data_test[5]['TEXT'] = number_format($total,2,'.',',');
		$data_test[10]['TEXT'] = number_format($totalBersih,2,'.',',') . "  ";
		$data_test[10]['T_ALIGN'] = "R";
		$data_test[10]['T_SIZE'] = 9;
		$data_test[3]['T_ALIGN'] = "R";
		$data_test[4]['T_ALIGN'] = "R";
		$data_test[5]['T_ALIGN'] = "R";
		$data_test[6]['T_ALIGN'] = "R";
		$data_test[7]['T_ALIGN'] = "R";
		$data_test[8]['T_ALIGN'] = "R";
		
		$data_test[1]['T_TYPE'] = "B";
		$data_test[2]['T_TYPE'] = "B";
		$data_test[5]['T_TYPE'] = "B";
		$data_test[6]['T_TYPE'] = "B";
		$data_test[7]['T_TYPE'] = "B";
		$data_test[8]['T_TYPE'] = "B";
		
		$data_test[1]['T_SIZE'] = 9;
		$data_test[2]['T_SIZE'] = 9;
		$data_test[5]['T_SIZE'] = 9;
		$data_test[6]['T_SIZE'] = 9;
		$data_test[7]['T_SIZE'] = 9;
		$data_test[8]['T_SIZE'] = 9;
		
		$data_test[0]['LN_SIZE'] = 5;
		$data_test[1]['LN_SIZE'] = 5;
		$data_test[2]['LN_SIZE'] = 5;
		$data_test[3]['LN_SIZE'] = 5;
		$data_test[4]['LN_SIZE'] = 5;
		$data_test[5]['LN_SIZE'] = 5;
		$data_test[6]['LN_SIZE'] = 5;
		$data_test[7]['LN_SIZE'] = 5;
		$data_test[8]['LN_SIZE'] = 5;	
		$pdf->tbDrawData($data_test); 
		
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize_kontraktor.inc');
		$pdf->Output();	
	}
	
	function bln_to_periode($bulan){
		if($bulan=="01"){ $bulan = "Januari"; } 
		else if($bulan=="02"){ $bulan = "Februari"; } 
		else if($bulan=="03"){ $bulan = "Maret"; } 
		else if($bulan=="04"){ $bulan = "April"; } 
		else if($bulan=="05"){ $bulan = "Mei"; } 
		else if($bulan=="06"){ $bulan = "Juni"; } 
		else if($bulan=="07"){ $bulan = "Juli"; } 
		else if($bulan=="08"){ $bulan = "Agustus"; } 
		else if($bulan=="09"){ $bulan = "September"; } 
		else if($bulan=="10"){ $bulan = "Oktober"; } 
		else if($bulan=="11"){ $bulan = "Nopember"; } 
		else if($bulan=="12"){ $bulan = "Desember"; }
		return $bulan;
	}
	
	function bln_to_rperiode($bulan){
		if($bulan=="01"){ $bulan = "I"; } 
		else if($bulan=="02"){ $bulan = "II"; } 
		else if($bulan=="03"){ $bulan = "III"; } 
		else if($bulan=="04"){ $bulan = "IV"; } 
		else if($bulan=="05"){ $bulan = "V"; } 
		else if($bulan=="06"){ $bulan = "VI"; } 
		else if($bulan=="07"){ $bulan = "VII"; } 
		else if($bulan=="08"){ $bulan = "VIII"; } 
		else if($bulan=="09"){ $bulan = "IX"; } 
		else if($bulan=="10"){ $bulan = "X"; } 
		else if($bulan=="11"){ $bulan = "XI"; } 
		else if($bulan=="12"){ $bulan = "XII"; }
		return $bulan;
	}
}
?>