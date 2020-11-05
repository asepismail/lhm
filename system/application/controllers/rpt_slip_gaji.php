<?
class rpt_slip_gaji extends Controller 
{
	function rpt_slip_gaji ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_du' ); 	
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
	    $this->load->library('form_validation');
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('file');
		require_once(APPPATH . 'libraries/fpdf_table.php');
		require_once(APPPATH . 'libraries/header_footer.inc');	
	}
	
	function create_slipgaji(){
	if ($this->session->userdata('logged_in') != TRUE)
	{
	   redirect('login');
	}
	$type = $this->uri->segment(5);
	$bulan = ""; $tahun = ""; $data_row=array();
	$company = $this->session->userdata('DCOMPANY');
	$company_name = $this->session->userdata('DCOMPANY_NAME');
	echo $type;
	if($type == "-") {
		$gc = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(6);
		$bulan = substr($from,4,2);
		$tahun =  substr($to,0,4);
		$data_row = $this->model_rpt_du->generate_du2($gc, $from, $to, $company);
	} else if($type == "bln"){
		$gc = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(6);
		$bulan = substr($from,4,2);
		$tahun =  substr($to,0,4);
		$data_row = $this->model_rpt_du->generate_du_bulanan($gc, $from, $to, $company);
	} else if($type == "afd"){
		$div = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(6);
		$bulan = substr($from,4,2);
		$tahun =  substr($to,0,4);
		$data_row = $this->model_rpt_du->get_du_perafd($company,$from,$to,$div);
	}
		
	
	if($bulan=='01'){ $bulan = "Januari ".$tahun; } 
	else if($bulan=='02'){ $bulan = "Februari ".$tahun; } 
	else if($bulan=='03'){ $bulan = "Maret ".$tahun; } 
	else if($bulan=='04'){ $bulan = "April ".$tahun; } 
	else if($bulan=='05'){ $bulan = "Mei ".$tahun; } 
	else if($bulan=='06'){ $bulan = "Juni ".$tahun; } 
	else if($bulan=='07'){ $bulan = "Juli ".$tahun; } 
	else if($bulan=='08'){ $bulan = "Agustus ".$tahun; } 
	else if($bulan=='09'){ $bulan = "September ".$tahun; } 
	else if($bulan=='10'){ $bulan = "Oktober ".$tahun; } 
	else if($bulan=='11'){ $bulan = "Nopember ".$tahun; } 
	else if($bulan=='12'){ $bulan = "Desember ".$tahun; }	
	
	$pdf = new pdf_usage();		
	$pdf->Open();
	$pdf->FPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false, 10);
    $pdf->SetMargins(5, 12);
	$pdf->AddPage('P', 'LEGAL');
	$pdf->AliasNbPages(); 
	$pdf->SetStyle("s1","arial","",8,"118,0,3");
	$pdf->SetStyle("s2","arial","",6,"0,49,159");
	//$this->head();	
		
	foreach ($data_row as $row)
	{	
		$pdf->SetTextColor(118, 0, 3);
		require_once(APPPATH . 'libraries/table_border.inc');
	
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		$pdf->Ln(3);
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 44.5;
			$aSimpleHeader[$i]['BRD_TYPE'] = 0;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=1; $j<=6; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
			$data[3]['TEXT'] = "";
			$data[0]['BRD_TYPE'] = 0;
			$data[1]['BRD_TYPE'] = 0;
			$data[2]['BRD_TYPE'] = 0;
			$data[3]['BRD_TYPE'] = 0;
		
			if ($j == 1){
				$data[0]['TEXT'] = "PT.". strtoupper($company_name);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 9;
				$data[0]['BRD_TYPE'] = 0;
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "SLIP GAJI";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 11;
				$data[0]['T_TYPE'] = "B";
				$data[0]['BRD_TYPE'] = 0;
			}
			
			if ($j == 3){
				$data[0]['TEXT'] = "Bulan : " .$bulan;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 4;
				$data[0]['BRD_TYPE'] = 0;	
			}
			
			if ($j == 4){
				$data[0]['TEXT'] = "" ;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 2;
				$data[0]['BRD_TYPE'] = 0;
			}
	
			if ($j == 5){
				$data[3]['TEXT'] = "Status : " . $row['TYPE_KARYAWAN'];
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 10;
				$data[3]['LN_SIZE'] = 4;
			}
			
			if ($j == 6){
				$data[0]['TEXT'] = "Nama : ". strtoupper($row['NAMA']);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = "2";
				$data[0]['T_SIZE'] = 10;
				$data[0]['LN_SIZE'] = 4;		
				$data[3]['TEXT'] = "NIK : ". strtoupper($row['EMPLOYEE_CODE']);
				$data[3]['T_ALIGN'] = "L";
				$data[3]['T_SIZE'] = 10;
				$data[3]['LN_SIZE'] = 4;
			}
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		// end tabel judul	
		$pdf->Ln(2);
		//table isi
		require_once(APPPATH . 'libraries/table_border.inc');
		$columns = 6; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array();
		$header = array('No.','Pendapatan','Jumlah','No.','Potongan','Jumlah');
		for($i=0; $i<$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[$i]['T_SIZE'] = 10;
		}
		
		$aSimpleHeader[0]['WIDTH'] = 9;
		$aSimpleHeader[1]['WIDTH'] = 47;
		$aSimpleHeader[2]['WIDTH'] = 33;
		$aSimpleHeader[3]['WIDTH'] = 9;
		$aSimpleHeader[4]['WIDTH'] = 47;
		$aSimpleHeader[5]['WIDTH'] = 33;
	
		$pdf->tbSetHeaderType($aSimpleHeader);
		$pdf->tbDrawHeader();
		
		$aDataType = Array();
		
		if ($company == "SSS") {	
			$jumlah_hk = $row['HK'] + $row['HKNE'];
		$data_gaji1 = array('Gaji Pokok (' . $jumlah_hk .' HK )','Tunjangan Transport','Tunjangan Jabatan','Tunjangan Cuti','Tunjangan Lain','Natura','ASTEK 4,54 %','THR/Rapel/Bonus','Lembur/Premi','Tunjangan Lebih Hari','..............................','Total','');
		$data_gaji2 = array('Potongan Koperasi','Iuran SP','ASTEK 6,54%','Pajak (PPH - 21)','Potongan Kurang Hari','Potongan THR / HHR','Pengambilan Gaji Tahap 1','Potongan Lain','','','..............................','Total','');
			$gp = $row['TTL_BYR'];
 $thr = 0;
	    // if($this->session->userdata('LOGINID') == 'ridhu' || $this->session->userdata('LOGINID') == 'asep'){
	     	$thr = $row['THR'];
	    // }

			$rtb = $row['RAPEL'] + $thr + $row['BONUS'];	
			$tunj_lhari = 0;
			$pot_khari = 0;
			
			$totalpendapatan = 	$gp + $row['TUNJ_TRANSPORT'] + $row['TUNJAB'] + $row['SUBSIDI_KENDARAAN'] + $row['NATURA'] + $row['ASTEK'] + $rtb + $row['PREMI_LEMBUR'] + $tunj_lhari;
			$totalpotongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
			$nilai_gaji1 = array($gp,$row['TUNJ_TRANSPORT'],$row['TUNJAB'],0,$row['SUBSIDI_KENDARAAN'],$row['NATURA'],$row['ASTEK'],$rtb,$row['PREMI_LEMBUR'],$tunj_lhari,'',$totalpendapatan,'');
			$nilai_gaji2 = array(0,0,$row['POT_ASTEK'],$row['PPH_21'],$pot_khari, $row['POTONGAN_LAIN'],'','','','','',$totalpotongan,'');
			
			$gaji_bruto = $gp + $row['TUNJ_TRANSPORT'] + $row['TUNJAB'] + $row['SUBSIDI_KENDARAAN'] + $row['NATURA'] + $row['ASTEK'] + $rtb + $row['PREMI_LEMBUR'] + $tunj_lhari;
			$total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
	/* kalau bukan SSS */
	} else if ( $company != "SSS") {
		$data_gaji1 = array('Gaji Pokok','Tunjangan Transport','Tunjangan Jabatan','Tunjangan Cuti','Tunjangan Lain','Natura','ASTEK 4,54 %','THR/Rapel/Bonus','Lembur/Premi','Tunjangan Lebih Hari','Kompensasi Cuti 5 Tahunan','Total','');
		$data_gaji2 = array('Potongan Koperasi','Iuran SP','ASTEK 6,54%','Pajak (PPH - 21)','Potongan Kurang Hari','Potongan THR / HHR','Pengambilan Gaji Tahap 1','Potongan Lain','','','..............................','Total','');	
			if ($row['TYPE_KARYAWAN'] == "BHL"){
				$gp = $row['TTL_BYR'];
			} else if ($row['TYPE_KARYAWAN'] == "KDMP"){
				$gp = $row['TTL_BYR'];
			} else {
				$gp = $row['GP'];
			}
			 $thr = 0;
	     // if($this->session->userdata('LOGINID') == 'ridhu' || $this->session->userdata('LOGINID') == 'asep'){
	     	$thr = $row['THR'];
	    // }

			$rtb = $row['RAPEL'] + $thr + $row['BONUS'];	
			if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
					$tunj_lhari = $row['TTL_BYR'] - $gp;
					if($tunj_lhari < 100) { $tunj_lhari = 0; } 
			} else if ($row['TYPE_KARYAWAN'] == "KDMP" && $gp < $row['TTL_BYR']) {
					$tunj_lhari = $row['TTL_BYR'] - $gp;
					if($tunj_lhari < 100) { $tunj_lhari = 0; } 
			} else {
					$tunj_lhari = 0;
			}
			
			
			
			if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
				$pot_khari = -($row['TTL_BYR'] - $gp);
				if($pot_khari < 100) { $pot_khari = 0; }
			} else if($row['TYPE_KARYAWAN'] == "KDMP" && $gp > $row['TTL_BYR']){
				$pot_khari = -($row['TTL_BYR'] - $gp);
				if($pot_khari < 100) { $pot_khari = 0; }
			} else {
				$pot_khari = 0;
			}	
			
			$totalpendapatan = 	$gp + $row['TUNJ_TRANSPORT'] + $row['TUNJAB'] + $row['SUBSIDI_KENDARAAN'] + $row['NATURA'] + $row['ASTEK'] + $rtb + $row['PREMI_LEMBUR'] + $tunj_lhari + $row['KOMPENSASI_CUTI'];
			$totalpotongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
			$nilai_gaji1 = array($gp,$row['TUNJ_TRANSPORT'],$row['TUNJAB'],0,$row['SUBSIDI_KENDARAAN'],$row['NATURA'],$row['ASTEK'],$rtb,$row['PREMI_LEMBUR'],$tunj_lhari,$row['KOMPENSASI_CUTI'],$totalpendapatan,'');
			$nilai_gaji2 = array(0,0,$row['POT_ASTEK'],$row['PPH_21'],$pot_khari, $row['POTONGAN_THR'],$row['POTONGAN_GAJI'],$row['POTONGAN_LAIN1'],'','','',$totalpotongan,'');
			
			$gaji_bruto = $gp + $row['TUNJ_TRANSPORT'] + $row['TUNJAB'] + $row['SUBSIDI_KENDARAAN'] + $row['NATURA'] + $row['ASTEK'] + $rtb + $row['PREMI_LEMBUR'] + $tunj_lhari + $row['KOMPENSASI_CUTI'];
			$total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];

		}
				
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		
		for ($j=0; $j <count($data_gaji1)-1; $j++)
			{
				$data = Array();
				$data[$j]['LN_SIZE'] = 4;
				$data[$j]['T_SIZE'] = 10;
				$data[0]['T_SIZE'] = 10;
				$data[1]['T_SIZE'] = 10;
				$data[2]['T_SIZE'] = 10;
				$data[3]['T_SIZE'] = 10;
				$data[4]['T_SIZE'] = 10;
				$data[5]['T_SIZE'] = 10;
				$data[6]['T_SIZE'] = 10;
				$data[7]['T_SIZE'] = 10;
				$data[8]['T_SIZE'] = 10;
				
				$data[0]['TEXT'] = $j + 1;
				$data[0]['T_ALIGN'] = "C"; //default in the example is C
				$data[1]['TEXT'] = $data_gaji1[$j];
				$data[1]['T_ALIGN'] = "L";
				if($nilai_gaji1[$j] == '0' || $nilai_gaji1[$j] == ''){
					$data[2]['TEXT'] = number_format(floatval($nilai_gaji1[$j]),0,'.',',');	
				} else {
					$data[2]['TEXT'] = "Rp. " . number_format(floatval($nilai_gaji1[$j]),0,'.',',');
				}
				$data[2]['T_ALIGN'] = "R";
				$data[3]['TEXT'] = $j + 1;
				$data[3]['T_ALIGN'] = "C";
				$data[4]['TEXT'] = $data_gaji2[$j];
				$data[4]['T_ALIGN'] = "L";
				if($nilai_gaji2[$j] == '0' || $nilai_gaji2[$j] == ''){
					$data[5]['TEXT'] = number_format(floatval($nilai_gaji2[$j]),0,'.',',');
				} else {
					$data[5]['TEXT'] = "Rp. " . number_format($nilai_gaji2[$j],0,'.',','); 
				}
				$data[5]['T_ALIGN'] = "R";	 	
				
				$pdf->tbDrawData($data);
			}
		
			$pdf->tbOuputData();
			$pdf->tbDrawBorder();
			
			$pdf->Ln(2);	
			
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 44.5;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=1; $j<=6; $j++)
		{
			$data = Array();
			$data[0]['TEXT'] = "";
			$data[1]['TEXT'] = "";
			$data[2]['TEXT'] = "";
			$data[3]['TEXT'] = "";
			$data[0]['BRD_TYPE'] = 0;
			$data[1]['BRD_TYPE'] = 0;
			$data[2]['BRD_TYPE'] = 0;
			$data[3]['BRD_TYPE'] = 0;
			$total_gaji = round($gaji_bruto - $total_potongan);
			//echo round($total_gaji);
			if ($j == 1){
				$data[0]['TEXT'] = "Gaji Diterima : Rp. "  . number_format($total_gaji,0,'.',',');
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
			}
			
			if ($j == 2){
				$data[0]['TEXT'] = "Terbilang : ". $this->terbilang($total_gaji);
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[0]['LN_SIZE'] = 5;	
			}
			
			if ($j == 3){
				if($company == "LIH") {
					$data[2]['TEXT'] = " Kemang, 30 ".$bulan;
				} else if($company == "SAP"){
					$data[2]['TEXT'] = " Musi Rawas, 30 ".$bulan;
				} else if($company == "TPAI") {
					$data[2]['TEXT'] = " Banyuasin, 30 ".$bulan;
				} else if($company == "SSS") {
					$data[2]['TEXT'] = " Ngabang, 30 ".$bulan;
				} else {
					$data[2]['TEXT'] = " ..........., 30 ".$bulan;;
				}
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 4;
			}
			
			if ($j == 4){
				if($company == "ASL") {
	
					$data[2]['TEXT'] = "( " . $row['NAMA'] . " )";
				} 
				
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 6;
			}
	
			if ($j == 5){
				$data[0]['TEXT'] = "";
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['BRD_TYPE'] = 0;
				$data[0]['LN_SIZE'] = 12;	
			}
			
			if ($j == 6){
				if($company == "ASL") {

					$data[2]['TEXT'] = "";
				} else {
					$data[2]['TEXT'] = "( " . $row['NAMA'] . " )";
				}
				$data[2]['T_ALIGN'] = "C";
				$data[2]['COLSPAN'] = 2;
				$data[2]['T_SIZE'] = 10;
				$data[0]['BRD_TYPE'] = 0;
				$data[1]['BRD_TYPE'] = 0;
				$data[2]['BRD_TYPE'] = 0;
				$data[2]['LN_SIZE'] = 4;
			}	
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
			$pdf->Ln(14);
		}		
		
		$pdf->Output();
	}
	
	function terbilang($bilangan) {

		  $angka = array('0','0','0','0','0','0','0','0','0','0',
						 '0','0','0','0','0','0');
		  $kata = array('','satu','dua','tiga','empat','lima',
						'enam','tujuh','delapan','sembilan');
		  $tingkat = array('','ribu','juta','milyar','triliun');
		
		  $panjang_bilangan = strlen($bilangan);
		
		  /* pengujian panjang bilangan */
		  if ($panjang_bilangan > 15) {
			$kalimat = "Diluar Batas";
			return $kalimat;
		  }
		
		  /* mengambil angka-angka yang ada dalam bilangan,
			 dimasukkan ke dalam array */
		  for ($i = 1; $i <= $panjang_bilangan; $i++) {
			$angka[$i] = substr($bilangan,-($i),1);
		  }
		
		  $i = 1;
		  $j = 0;
		  $kalimat = "";
		
		
		  /* mulai proses iterasi terhadap array angka */
		  while ($i <= $panjang_bilangan) {
		
			$subkalimat = "";
			$kata1 = "";
			$kata2 = "";
			$kata3 = "";
		
			/* untuk ratusan */
			if ($angka[$i+2] != "0") {
			  if ($angka[$i+2] == "1") {
				$kata1 = "seratus";
			  } else {
				$kata1 = $kata[$angka[$i+2]] . " ratus";
			  }
			}
		
			/* untuk puluhan atau belasan */
			if ($angka[$i+1] != "0") {
			  if ($angka[$i+1] == "1") {
				if ($angka[$i] == "0") {
				  $kata2 = "sepuluh";
				} elseif ($angka[$i] == "1") {
				  $kata2 = "sebelas";
				} else {
				  $kata2 = $kata[$angka[$i]] . " belas";
				}
			  } else {
				$kata2 = $kata[$angka[$i+1]] . " puluh";
			  }
			}
		
			/* untuk satuan */
			if ($angka[$i] != "0") {
			  if ($angka[$i+1] != "1") {
				$kata3 = $kata[$angka[$i]];
			  }
			}
		
			/* pengujian angka apakah tidak nol semua,
			   lalu ditambahkan tingkat */
			if (($angka[$i] != "0") OR ($angka[$i+1] != "0") OR
				($angka[$i+2] != "0")) {
			  $subkalimat = "$kata1 $kata2 $kata3 " . $tingkat[$j] . " ";
			}
		
			/* gabungkan variabe sub kalimat (untuk satu blok 3 angka)
			   ke variabel kalimat */
			$kalimat = $subkalimat . $kalimat;
			$i = $i + 3;
			$j = $j + 1;
		
		  }
		
		  /* mengganti satu ribu jadi seribu jika diperlukan */
		  if (($angka[5] == "0") AND ($angka[6] == "0")) {
			$kalimat = str_replace("satu ribu","seribu",$kalimat);
		  }
		
		  return trim($kalimat) . "  rupiah";
		
	}

}
?>
