<?
class rpt_ba_pjinfrastruktur extends Controller 
{
	function rpt_ba_pjinfrastruktur ()
	{
		parent::Controller();	

		$this->load->model( 'model_rpt_ba' ); 
        
        $this->load->model('model_c_user_auth'); 
        $this->lastmenu="rpt_ba_pjinfrastruktur";
		
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
		$view = "rpt_ba_pjinfras";
		$data = array();
		$data['judul_header'] = "Berita Acara Hasil Kerja Project Infrastruktur";
		$data['js'] = $this->js_ba_pjinfras();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}	
	} 
	
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' >";
		$string .= "<option value=''> -- choose -- </option><option value='all'>rekap semua</option>";
		$data_afd = $this->model_rpt_ba->get_afdeling($this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row){
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string." <option value=\"".$row['AFD']."\"  selected>".$row['AFD']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD']."\">".$row['AFD']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	// ------------------------------ PJ Infras ------------------------------------------------- //
	function js_ba_pjinfras(){
		
		$js = "$(function() {
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
					$('#TO').datepicker({dateFormat:'yy-mm-dd'});
				});
				
		jQuery('#submitdata').click(function (){
			var tfrom = document.getElementById('FROM').value;
			var elem = tfrom.split('-');
			from = elem[0]+elem[1]+elem[2];
							
			var tto = document.getElementById('TO').value;
			var elem2 = tto.split('-');
			to = elem2[0]+elem2[1]+elem2[2];
						
			var period = to - from;
														 	
			var afd = $('#afd').val();
			var rkp = $('#rkp').val();
			var nw = $('#newwindow').is(':checked');
			var urlafd = url + 'rpt_ba_pjinfrastruktur/ba_pjinfras_rekap_afd/' + from  + '/' + to + '/' + rkp; 
			var urlbl = url + 'rpt_ba_pjinfrastruktur/ba_pjinfras_rekap_block/' + from  + '/' + to + '/' + rkp;
			if(rkp == 'rekap'){
				urls = urlafd;
			} else {
				urls = urlbl;
			}
			
			var jns_laporan = $('#jns_laporan').val();	
					if ( jns_laporan == 'html'){
						if(nw != false) {	
						$('#frame').attr('src','');
						$('.button').popupWindow({ 
						windowURL:urls,
						windowName:'Rekap Biaya Gaji Project Infrastruktur',
						width:800 
					}); 
				} else {
					$('#frame').attr('src',urls);
				}
			} else if ( jns_laporan == 'excell'){
				if(rkp == 'rekap'){
					urls = url + 'rpt_ba_pjinfrastruktur/ba_xlspjinfras_rekap_afd/'  + from  + '/' + to + '/' + rkp; 
				} else {
					urls = url + 'rpt_ba_pjinfrastruktur/ba_xlspjinfras_rekap_block/'  + from  + '/' + to + '/' + rkp; 
				}
				$.download(urls,'');
			} else if (jns_laporan =='pdf') {
				if(rkp == 'rekap'){
					urls = url + 'rpt_ba_pjinfrastruktur/ba_pdfpjinfras_rekap/'  + from + '/' + to + '/' + rkp; 
				} else {
					urls = url + 'rpt_ba_pjinfrastruktur/ba_pdfpjinfras_detail/'  + from + '/' + to + '/' + rkp; 
				}
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_pjinfras_rekap_afd(){
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
			
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$data = array();
		$data_infras = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$umr = $this->getUmr($company, substr($from,0,4));
		
		$total = 0;
		$total_hk = 0;
		
		 $tabel = "";
		 $tabel .= $this->getStyle();
		 $tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'>
					<strong>BERITA ACARA HASIL KERJA RAWAT INFRASTRUKTUR</strong></td></tr>
					<tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp; / PJ-INF / ".$company." / ".$bulanr." / ".$tahun." </strong></td>
					</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." 
						&nbsp;" .$tahun. "</strong></td>
					</tr><tr><td colspan='3'>&nbsp;</td></tr>
					<tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
		 $tabel .= "<span class='content' style='float:right;margin-right:15%;'>Rp/HK = ".number_format($umr)."</span><br/>";
		 $tabel .= "<table width='85%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		 $tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th>
					<th class='tbl_th' rowspan='2'>SAT</th><th class='tbl_th' colspan='2'>Hasil Kerja</th>
					<th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    				<th class='tbl_th' colspan='2'>Rp / Sat</th>
					<th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
		 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th>
					<th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th>
					<th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th>
					<th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_infras as $row){
			$realisasi = $row['REAL_BIAYA_BI'];
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
				$hk_satuan = $realisasi/$umr/$hasil_kerja;
			} else {
				$rp_satuan = 0;
				$hk_satuan = 0;
			}
			
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    				<td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
					<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
		$tabel .= "<td class='tbl_td' align = 'right'>".number_format($row['HASIL_KERJA'],2,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($row['HASIL_KERJA'],2,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($realisasi,0,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($realisasi,0,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'>".number_format($hk_satuan,1,',','.')."&nbsp;&nbsp;</td>
					<td class='tbl_td' align = 'right'>".number_format($hk_satuan,1,',','.')."&nbsp;&nbsp;</td></tr>";
	} else {
		 $tabel .= "<td class='tbl_td' align = 'center'><strong>".$row['ACCOUNTCODE']."</strong></td>
					<td class='tbl_td'><strong>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</strong></td>
					<td class='tbl_td'> &nbsp; </td>";
		 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>
    				<td class='tbl_td' align = 'right'>&nbsp;</td>
    				<td class='tbl_td' align = 'right'>&nbsp;</td>
					<td class='tbl_td' align = 'right'>&nbsp;</td>
					<td class='tbl_td' align = 'right'>&nbsp;</td>
					<td class='tbl_td' align = 'right'>&nbsp;</td>
					<td class='tbl_td' align = 'right'>&nbsp;</td>
					<td class='tbl_td' align = 'right'>&nbsp;</td></tr>";
	}
   
		}
		$tabel .= "<tr><td class='tbl_td' align = 'center'><strong></strong></td>
    				<td class='tbl_td'><strong>&nbsp;&nbsp;</strong></td>
    				<td class='tbl_td' align = 'center'>&nbsp;</td>
    				<td class='tbl_td'>&nbsp;</td><td class='tbl_td'>&nbsp;</td>
   					<td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    				<td class='tbl_td'>&nbsp;</td><td class='tbl_td'>&nbsp;</td>
    				<td class='tbl_td' align = 'right'><strong>&nbsp;</strong>&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'><strong>&nbsp;</strong>&nbsp;&nbsp;</td></tr>";
		$tabel .= "</table>"; 
		echo $tabel;
	}
	
	function ba_pjinfras_rekap_block(){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_infras = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0;
		$total_hk = 0;
		$total_hasilkerja = 0;
		$total_hk_sat = 0;
		$total_hk_unit = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
		$tabel .= " .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$tabel .= " .tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$tabel .= " .tbl_2 { font-size: 12px;color:#678197;} ";
		$tabel .= " .content { font-size: 12px;color:#678197; } </style>";
		$tabel .= "<table class='tbl_2' border='0'><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td>";
		$tabel .= "</tr><tr><td colspan='3'>DETAIL REALISASI KERJA PROJECT INFRASTRUKTUR</td></tr></table>";
		$tabel .= "<span class='content' style='float:right;margin-right:15%;'>Rp/HK = ".number_format($umr)."</span><br/>";
		$tabel .= "<table width='85%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th rowspan='2' class='tbl_th'>KODE PROJECT</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>LOKASI</th>";
		$tabel .= "<th colspan='2' class='tbl_th'>AKTIVITAS</th><th rowspan='2' class='tbl_th'>SAT</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>QTY</th><th rowspan='2' class='tbl_th'>REALISASI (Rp)</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>HK / SAT</th><th rowspan='2' class='tbl_th'>Rp / SAT</th>";
		$tabel .= "</tr><tr><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th></tr>";
	
	foreach ( $data_infras as $row){
			$realisasi = $row['REAL_BIAYA_BI'];
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];		
			$hasil_kerja = $row['HASIL_KERJA']; 
	
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
				$hk_sat = $realisasi/$umr/$hasil_kerja;
				$total_hk_sat = $total_hk_sat + $hk_sat;
			} else {
				$rp_satuan = 0;
				$total_hk_sat = 0;
				$hk_sat = 0;
			}
			$total = $total + $realisasi;
			$total_hasilkerja = $total_hasilkerja + $hasil_kerja;
			$total_hk_unit = $total_hk_unit + $rp_satuan;
			$tabel .= "<tr>";
			
   	$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;".$row['LOCATION_CODE']."</td>
					<td class='tbl_td' width='12%' align = 'center'>".$row['PJ_LOCATION']."</td>";
		$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;".$row['ACCOUNTCODE']."</td>
				<td class='tbl_td' align = 'left'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
				<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>
				<td class='tbl_td' align = 'right'>".number_format($hasil_kerja,2,',','.')."&nbsp;&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>".number_format($hk_sat,2,',','.')."&nbsp;&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;&nbsp;</td></tr>";	
	
  	}
	
		$tabel .= "<tr>
				<td class='tbl_td' colspan='4' align='center'><strong>&nbsp;&nbsp;TOTAL</strong></td>
				<td class='tbl_td' align = 'right'><strong></strong>&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>&nbsp;</td>
			<td class='tbl_td' align = 'right'><strong>".number_format($total,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td>
			<td class='tbl_td' align = 'right'><strong>&nbsp;</td>
			<td class='tbl_td' align = 'right'><strong>&nbsp;</td></tr>";
		$tabel .= "</table>"; 
		echo $tabel;
	}
	
	
	function ba_xlspjinfras_rekap_afd() {
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
			
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_pj_if = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0; $total_hk = 0; $total_qty = 0;
		
		$judul = ''; $headers = '';  $data = ''; $footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REKAP REALISASI KERJA PROJECT INFRASTRUKTUR \n";
		$judul .= "NO :     / PJ-INF / ".$company." / ".$bulanr." / ".$tahun."\n";
		$judul .= "PERIODE : ".strtoupper($bulan)." " .$tahun."\n";
		$judul .= " \n";
		$judul .= "HK / HARI : " .$umr. " \n";
		
		$headers .= "KODE AKTIVITAS \t";
		$headers .= "NAMA AKTIVITAS \t";
		$headers .= "SAT \t";
		$headers .= "HASIL KERJA BLN INI \t";	
		$headers .= "HASIL KERJA S.D BLN INI \t";
		$headers .= "REALISASI BLN INI \t";
		$headers .= "REALISASI S.D BLN INI \t";
		$headers .= "Rp / SAT BLN INI \t";
		$headers .= "Rp / SAT S.D BLN INI \t";
		$headers .= "HK / SAT BLN INI \t";
		$headers .= "HK / SAT S.D BLN INI \t";
		
			
		foreach ( $data_pj_if as $row){
			
			$realisasi = $row['REAL_BIAYA_BI'];
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
					
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
				$hk_sat = ($realisasi/$umr) / $hasil_kerja;
			} else {
				$rp_satuan = 0;
				$hk_sat = 0;
			}
				
			$line = '';
						
			$line .= str_replace('"', '""',$row['ACCOUNTCODE'])."\t";
			$line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
			$line .= str_replace('"', '""',$row['UNIT1'])."\t";
			$line .= str_replace('"', '""',$row['HASIL_KERJA'])."\t";
			$line .= str_replace('"', '""',$row['HASIL_KERJA'])."\t";
			$line .= str_replace('"', '""',$realisasi)."\t";
			$line .= str_replace('"', '""',$realisasi)."\t";
			$line .= str_replace('"', '""',$rp_satuan)."\t";
			$line .= str_replace('"', '""',$rp_satuan)."\t";
			$line .= str_replace('"', '""',$hk_sat)."\t";
			$line .= str_replace('"', '""',$hk_sat)."\t";
								
			$data .= trim($line)."\n";		
		}
			
		$footer .= " - \t";
		$footer .= " TOTAL \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " - \t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_PJINFRAS_PERAFD_".$company."_".substr($from,0,6).".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	//xls blok 
	function ba_xlspjinfras_rekap_block( $periode){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
			
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_pj_if = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0;  $total_hasilkerja = 0; $total_hk = 0;
		$total_hk_sat = 0; $total_hk_unit = 0; $total_qty = 0;
		$judul = ''; $headers = ''; $data = ''; $footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "DETAIL REALISASI KERJA PROJECT INFRASTRUKTUR \n";
		$judul .= "PERIODE : ".strtoupper($bulan)."  " .$tahun."\n";
		$judul .= " \n";
		$judul .= "HK / HARI : " .$umr. " \n";
		
		$headers .= "KODE PROJECT \t";
		$headers .= "LOKASI \t";
-		$headers .= "KODE AKTIVITAS \t";	
		$headers .= "NAMA AKTIVITAS \t";
		$headers .= "SAT \t";
		$headers .= "QTY \t";
		$headers .= "REALISASI (Rp.) \t";
		$headers .= "Rp. / SAT \t";
		$headers .= "HK / SAT \t";
			
		foreach ( $data_pj_if as $row){
			$realisasi = $row['REAL_BIAYA_BI'];
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
						
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;
					$hk_sat = ($realisasi/$umr) / $hasil_kerja;
				} else {
					$rp_satuan = 0;
					$hk_sat = 0;
				}
				
			$total_hasilkerja = $total_hk + $hasil_kerja;
			$total_hk_unit = $total_hk_unit + $rp_satuan;
				
			$line = '';
						
			$line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
			$line .= str_replace('"', '""',$row['PJ_LOCATION'])."\t";
			$line .= str_replace('"', '""',$row['ACCOUNTCODE'])."\t";
			$line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
			$line .= str_replace('"', '""',$row['UNIT1'])."\t";
			$line .= str_replace('"', '""',$hasil_kerja)."\t";
			$line .= str_replace('"', '""',$realisasi)."\t";
			$line .= str_replace('"', '""',$rp_satuan)."\t";
			$line .= str_replace('"', '""',$hk_sat)."\t";
						
			$data .= trim($line)."\n";		
		}
			
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " TOTAL \t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total_hk_unit)."\t";
		$footer .= " - \t";
		
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_PJINFRAS_PERLOKASI_".$company."_".substr($from,0,6).".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	############################### PDF PRINT #########################
	function ba_pdfpjinfras_rekap()
	{
		if ($this->session->userdata('logged_in')!=TRUE){
			redirect('login');
		}

		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		$company_name = $this->session->userdata('DCOMPANY_NAME');
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_pjinfras = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$total = 0; $total_hk = 0; $total_rp=0; $total_qty=0;
		
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->SetMargins(5, 10);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		
		require_once(APPPATH . 'libraries/ba/header_ba_pjinfrastruktur.inc');
	
		require_once(APPPATH . 'libraries/ba/table_border.inc');
		
		$columns = 11; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader[$i]['TEXT'] = "ACTIVITY";
				$aSimpleHeader[$i]['WIDTH'] = 20;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 1) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 80;

			}
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "SAT";
				$aSimpleHeader[$i]['WIDTH'] = 15;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "HASIL KERJA";
				$aSimpleHeader[$i]['WIDTH'] = 28;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 28;
			}
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "REALISASI BIAYA ( Rp. )";
				$aSimpleHeader[$i]['WIDTH'] = 28;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 28;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "RP / SAT";
				$aSimpleHeader[$i]['WIDTH'] = 28;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 28;
			}
			if($i == 9) {
				$aSimpleHeader[$i]['TEXT'] = "HK / SAT";
				$aSimpleHeader[$i]['WIDTH'] = 28;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 10) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 28;
			}
			
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['COLSPAN'] = 2;				
			}
			if($i == 1) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 2) {
				$aSimpleHeader2[$i]['TEXT'] = "";	
				$aSimpleHeader2[$i]['WIDTH'] = 10;			
			}
			if($i == 3) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 9) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 10) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['WIDTH'] = 28;
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
		
		foreach ($data_pjinfras as $row)
		{
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
						
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
				$hk_satuan = $realisasi/$umr/$hasil_kerja;
			} else {
				$rp_satuan = 0;
				$hk_satuan = 0;
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
			$data[9]['TEXT'] = number_format($hk_satuan,2,'.',',');
			$data[10]['TEXT'] = number_format($hk_satuan,2,'.',',');
			
			$data[3]['T_ALIGN'] = "R";
			$data[4]['T_ALIGN'] = "R";
			$data[5]['T_ALIGN'] = "R";
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
			$data[9]['LN_SIZE'] = 5;
			$data[10]['LN_SIZE'] = 5;
			
			$pdf->tbDrawData($data);
		}
		$data_test=array();
		$data_test[1]['TEXT'] = "PROJECT INFRASTRUKTUR";
		$data_test[1]['T_ALIGN'] = "L";
		$data_test[2]['TEXT'] = "";
		$data_test[5]['TEXT'] = number_format($total,2,'.',',');
		$data_test[6]['TEXT'] = number_format($total,2,'.',',');
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
		$data_test[9]['T_TYPE'] = "B";
		$data_test[10]['T_TYPE'] = "B";
		
		$data_test[1]['T_SIZE'] = 9;
		$data_test[2]['T_SIZE'] = 9;
		$data_test[5]['T_SIZE'] = 9;
		$data_test[6]['T_SIZE'] = 9;
		$data_test[7]['T_SIZE'] = 9;
		$data_test[8]['T_SIZE'] = 9;
		$data_test[9]['T_SIZE'] = 9;
		$data_test[10]['T_SIZE'] = 9;
		
		$data_test[0]['LN_SIZE'] = 5;
		$data_test[1]['LN_SIZE'] = 5;
		$data_test[2]['LN_SIZE'] = 5;
		$data_test[3]['LN_SIZE'] = 5;
		$data_test[4]['LN_SIZE'] = 5;
		$data_test[5]['LN_SIZE'] = 5;
		$data_test[6]['LN_SIZE'] = 5;
		$data_test[7]['LN_SIZE'] = 5;
		$data_test[8]['LN_SIZE'] = 5;
		$data_test[9]['LN_SIZE'] = 5;
		$data_test[10]['LN_SIZE'] = 5;		
		$pdf->tbDrawData($data_test);
		
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize_ba.inc');
		$pdf->Output();	
	}
	
	function ba_pdfpjinfras_detail(){
		if ($this->session->userdata('logged_in')!=TRUE)
		{
			redirect('login');
		}
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		$company_name = $this->session->userdata('DCOMPANY_NAME');
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_pjinfras = $this->model_rpt_ba->ba_pjinfras_afd($rkp, $from, $to, $company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0; $total_hasilkerja = 0; $total_hk = 0;
		$total_hk_sat = 0; $total_hk_unit = 0; $total_qty = 0;
		
		$pdf = new pdf_usage();
		$pdf->Open();
		$pdf->SetAutoPageBreak(TRUE,10);
		$pdf->SetMargins(5,10);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		
		require_once(APPPATH . 'libraries/ba/header_ba_pjinfrastruktur.inc');
		require_once(APPPATH . 'libraries/ba/table_border.inc');
		
		$columns = 9; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader[$i]['TEXT'] = "KODE PROJECT";
				$aSimpleHeader[$i]['WIDTH'] = 30;
-				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 1) {
				$aSimpleHeader[$i]['TEXT'] = "LOKASI";
				$aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "AKTIVITAS";
				$aSimpleHeader[$i]['WIDTH'] = 25;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 90;
			}
			if($i == 4) {
				$aSimpleHeader[$i]['TEXT'] = "SAT";
				$aSimpleHeader[$i]['WIDTH'] = 20;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "QTY";
				$aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = "REALISASI (Rp)";
				$aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "Rp / SAT";
				$aSimpleHeader[$i]['WIDTH'] =35;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = "HK / SAT";
				$aSimpleHeader[$i]['WIDTH'] =35;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['COLSPAN'] = 2;				
			}
			if($i == 1) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 2) {
				$aSimpleHeader2[$i]['TEXT'] = "KODE";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader2[$i]['TEXT'] = "NAMA";
				$aSimpleHeader2[$i]['WIDTH'] = 45;
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['WIDTH'] = 35;
			}
		}
		
		$aHeader = array( $aSimpleHeader, $aSimpleHeader2);
		$pdf->tbSetHeaderType($aHeader, TRUE);
		$pdf->tbDrawHeader();
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		
		foreach ($data_pjinfras as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
				$total_hk = $total_hk + $row['HK'];
				$total = $total + $realisasi;
				$hasil_kerja = $row['HASIL_KERJA']; 
					
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;
					$hk_satuan = $realisasi/$umr/$hasil_kerja;
				} else {
					$rp_satuan = 0;
					$hk_satuan = 0;
				}
				
				$total_hasilkerja = $total_hasilkerja + $hasil_kerja;
				$total_hk_unit = $total_hk_unit + $rp_satuan;
			
			$data = Array();
			$data[0]['TEXT'] = $row['LOCATION_CODE'];
			$data[1]['TEXT'] = $row['PJ_LOCATION'];
			$data[2]['TEXT'] = $row['ACCOUNTCODE'];
			$data[3]['TEXT'] = $row['COA_DESCRIPTION'];
			
			$data[4]['TEXT'] = $row['UNIT1'];
			$data[5]['TEXT'] = $row['HASIL_KERJA'];
			$data[6]['TEXT'] = number_format($realisasi,2,'.',',');
			$data[7]['TEXT'] = number_format($rp_satuan,2,'.',',');
			$data[8]['TEXT'] = number_format($hk_satuan,2,'.',',');
			
			$data[1]['T_ALIGN'] = "C";
			$data[2]['T_ALIGN'] = "C";
			$data[3]['T_ALIGN'] = "L";
			$data[4]['T_ALIGN'] = "C";
			$data[5]['T_ALIGN'] = "R";
			$data[6]['T_ALIGN'] = "R";
			$data[7]['T_ALIGN'] = "R";
			$data[8]['T_ALIGN'] = "R";
			
			$data[0]['T_SIZE'] = 10;
			$data[1]['T_SIZE'] = 10;
			$data[2]['T_SIZE'] = 10;
			$data[3]['T_SIZE'] = 10;
			$data[4]['T_SIZE'] = 10;
			$data[5]['T_SIZE'] = 10;
			$data[6]['T_SIZE'] = 10;
			$data[7]['T_SIZE'] = 10;
			$data[8]['T_SIZE'] = 10;
			
			$data[0]['LN_SIZE'] = 5;
			$data[1]['LN_SIZE'] = 5;
			$data[3]['LN_SIZE'] = 5;
			$data[4]['LN_SIZE'] = 5;
			$data[5]['LN_SIZE'] = 5;
			$data[6]['LN_SIZE'] = 5;
			$data[7]['LN_SIZE'] = 5;
			$data[8]['LN_SIZE'] = 5;
			
			$pdf->tbDrawData($data);
		}
		
		$data_test=array();
		$data_test[0]['TEXT'] = "TOTAL";
		$data_test[0]['COLSPAN'] = "5";
		$data_test[5]['TEXT'] = number_format($total_hasilkerja,2,',','.');
		$data_test[6]['TEXT'] = number_format($total,2,',','.');
		$data_test[7]['TEXT'] = number_format($total/$total_hasilkerja,2,',','.');
		$data_test[8]['TEXT'] = number_format($hk_satuan,2,',','.');

		$data_test[5]['T_ALIGN'] = "R";
		$data_test[6]['T_ALIGN'] = "R";
		$data_test[7]['T_ALIGN'] = "R";
		$data_test[8]['T_ALIGN'] = "R";
	
		$data_test[3]['T_TYPE'] = "B";
		$data_test[5]['T_TYPE'] = "B";
		$data_test[6]['T_TYPE'] = "B";
		$data_test[7]['T_TYPE'] = "B";
		$data_test[8]['T_TYPE'] = "B";
		
		$data_test[0]['T_SIZE'] = 10;
		$data_test[3]['T_SIZE'] = 10;
		$data_test[5]['T_SIZE'] = 10;
		$data_test[6]['T_SIZE'] = 10;
		$data_test[7]['T_SIZE'] =10;
		$data_test[8]['T_SIZE'] = 10;
		
		$data_test[0]['LN_SIZE'] = 5;
		$data_test[3]['LN_SIZE'] = 5;
		$data_test[5]['LN_SIZE'] = 5;
		$data_test[6]['LN_SIZE'] = 5;
		$data_test[7]['LN_SIZE'] = 5;
		$data_test[8]['LN_SIZE'] = 5;
		
		$pdf->tbDrawData($data_test);
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize_ba.inc');
		$pdf->Output();	
	}
	
	function getStyle(){
		 	$style = "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
					.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
					.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
					.tbl_2 { font-size: 12px;color:#678197;}
					.content { font-size: 12px;color:#678197; }
					</style>";
			return $style;
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
	
	function getUmr($company, $tahun){
		$data_umr = $this->model_rpt_ba->get_umr($company,$tahun);
		$umr = 0;
		foreach ( $data_umr as $row_umr){
			$umr = $row_umr['UMR'];
		}
		return $umr;
	}
}

?>