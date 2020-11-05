<?
class rpt_ba_bibitan extends Controller 
{
	function rpt_ba_bibitan ()
	{
		parent::Controller();	

		$this->load->model( 'model_rpt_ba' ); 
		
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
		$view = "rpt_ba_bibitan";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Bibitan / Nursery";
		$data['js'] = $this->js_ba_bibitan();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
				
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD'){
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
    } 
	
	function dropdownlist_afd()
	{
	
		$string = "<select  name='afd' class='select'  id='afd' >";
		$string .= "<option value=''> -- choose -- </option><option value='all'>rekap semua</option>";
		
		$data_afd = $this->model_rpt_ba->get_afdeling($this->session->userdata('DCOMPANY'));
		
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
	
	// ------------------------------ Bibitan punya ------------------------------------------------- //
	function js_ba_bibitan(){
		
		$js = "$(function() {
				 $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
				 $('#TO').datepicker({dateFormat:'yy-mm-dd'});
			});
			
			jQuery('#submitdata').click(function (){
			var rkp = $('#rkp').val();
			var tfrom = document.getElementById('FROM').value;
			var elem = tfrom.split('-');
			from = elem[0]+elem[1]+elem[2];
							
			var tto = document.getElementById('TO').value;
			var elem2 = tto.split('-');
			to = elem2[0]+elem2[1]+elem2[2];
			
			var nw = $('#newwindow').is(':checked');
			var urlafd = url + 'rpt_ba_bibitan/ba_bibitan_rekap_afd/' + from + '/' + to + '/' + rkp; 
			var urlbl = url + 'rpt_ba_bibitan/ba_bibitan_rekap_block/' + from + '/' + to + '/' + rkp;
			
			if(rkp == 'rekap')
			{
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
					windowName:'Rekap Biaya Gaji Bibitan',
					width:800 
					}); 
				} else {
					$('#frame').attr('src',urls);
				}
		
			} else if ( jns_laporan == 'excell'){
				if(rkp == 'rekap')
				{
					urls = url + 'rpt_ba_bibitan/ba_xlsbibitan_rekap/' + from + '/' + to + '/' + rkp;
				} else {
					urls = url + 'rpt_ba_bibitan/ba_xlsbibitan_petak/' + from + '/' + to + '/' + rkp;
				}
				$.download(urls,'');
			}
		 
		});";
		return $js;
	}
	
	function ba_bibitan_rekap_afd($periode){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		if($bulan == '01'){ $bulan = "Januari"; $bulanr = "I";} 
		else if($bulan == '02'){ $bulan = "Februari"; $bulanr = "II"; } 
		else if($bulan == '03'){ $bulan = "Maret"; $bulanr = "III"; } 
		else if($bulan == '04'){ $bulan = "April"; $bulanr = "IV"; } 
		else if($bulan == '05'){ $bulan = "Mei"; $bulanr = "V"; } 
		else if($bulan == '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
		else if($bulan == '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
		else if($bulan == '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
		else if($bulan == '09'){ $bulan = "September"; $bulanr = "IX"; } 
		else if($bulan == '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
		else if($bulan == '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
		else if($bulan == '12'){ $bulan = "Desember"; $bulanr = "XII"; }
		
		$company = $this->session->userdata('DCOMPANY');
		$data_bibitan = $this->model_rpt_ba->ba_bibitan_afd($from, $to, $rkp, $company);
		$total = 0;
		$total_hk = 0;
		
		$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = "";	
		foreach ( $data_umr as $row_umr){
			$umr .= $row_umr['UMR'];
		}
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
		$tabel .= "	.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$tabel .= "	.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
 		$tabel .= "	.tbl_2 { font-size: 12px;color:#678197; } ";
		$tabel .= "	.content { font-size: 12px;color:#678197; } </style>";
		$tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>BERITA ACARA HASIL KERJA PEMBIBITAN
					</strong></td>";
		$tabel .= "</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / BBT / ".$company." / ".$bulanr." / ".		$tahun."</strong></td> ";
		$tabel .= "</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." &nbsp;" .$tahun. "</strong></td>
</tr><tr><td colspan='3'>&nbsp;</td> ";
		$tabel .= "</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
		$tabel .= "<span class='content' style='float:right;margin-right:15%;'>Rp/HK = ".number_format($umr)."</span><br/>";
		$tabel .= "<table width='85%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>";
   		$tabel .= "<th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>";
    	$tabel .= "<th class='tbl_th' colspan='2'>Rp / Sat</th></tr>";
 		$tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>";
    $tabel .= " <th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_bibitan as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			//if ( $total_qty != 0 ){ $rp_satuan_ttl = $total / $total_qty; } else { $rp_satuan_ttl = 0; }
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;	
				} else {
					$rp_satuan = 0;
				}
				
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>";
   	 	$tabel .= "<td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>";
		$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;".$row['UNIT1']."</td>";
	 	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($row['HASIL_KERJA'],2,',','.')."&nbsp;&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($row['HASIL_KERJA'],2,',','.')."&nbsp;&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;</td></tr>";
		
	} else {
		$tabel .= "<td class='tbl_td' align = 'center'><strong>".$row['ACCOUNTCODE']."</strong></td>";
		$tabel .= "<td class='tbl_td'><strong>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</strong></td>";
		$tabel .= "<td class='tbl_td'> &nbsp; </td>";
		$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>";
     	$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>";
     	$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>";
     	$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td>";
     	$tabel .= "<td class='tbl_td' align = 'right'>&nbsp;</td></tr>";
	}
   
			
		}
		$tabel .= "<tr>";
		$tabel .= "<td class='tbl_td' align = 'center'><strong>8300000</strong></td>";
		$tabel .= "<td class='tbl_td'><strong>&nbsp;&nbsp;BIBITAN</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'center'>Pokok</td>";
		$tabel .= "<td class='tbl_td'>&nbsp;</td>";
		$tabel .= "<td class='tbl_td'>&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total,2,',','.')."</strong>&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total,2,',','.')."</strong>&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td'>&nbsp;</td>";
		$tabel .= "<td class='tbl_td'>&nbsp;</td></tr>";
 
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	function ba_bibitan_rekap_block($periode){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_bibitan = $this->model_rpt_ba->ba_bibitan_afd($from, $to, $rkp, $company);
		$total = 0;
		$total_hasilkerja = 0;
		$total_hk = 0;
		$total_hk_sat = 0;
		$total_hk_unit = 0;
		
		$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = "";	
		foreach ( $data_umr as $row_umr){
			$umr .= $row_umr['UMR'];
		}
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
		$tabel .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$tabel .= ".tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$tabel .= ".tbl_2 { font-size: 12px;color:#678197;} ";
		$tabel .= ".content { font-size: 12px;color:#678197; } </style>";
		
		$tabel .= "<table class='tbl_2' border='0'><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td>";
		$tabel .= "</tr><tr><td colspan='3'>REALISASI KERJA PEMBIBITAN</td>";
		$tabel .= "<span class='content' style='float:right;margin-right:15%;margin-top:40px;'>Rp/HK = ".number_format($umr)."</span><br/>";
		$tabel .= "<table width='85%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th rowspan='2' class='tbl_th'>KODE BLOK</th><th rowspan='2' class='tbl_th'>LUAS BLOK</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>JML POKOK</th>";
		$tabel .= "<th colspan='2' class='tbl_th'>AKTIVITAS</th><th rowspan='2' class='tbl_th'>SAT</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>QTY</th><th rowspan='2' class='tbl_th'>REALISASI (Rp)</th>";
		$tabel .= "<th rowspan='2' class='tbl_th'>Rp / SAT</th>";
		$tabel .= "</tr><tr><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th></tr>";
	
	foreach ( $data_bibitan as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];		
			$hasil_kerja = $row['HASIL_KERJA']; 
			$hk_sat = $realisasi/$umr;
		
			if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;	
				} else {
					$rp_satuan = 0;
				}
				
			$total = $total + $realisasi;
			$total_hasilkerja = $total_hasilkerja + $hasil_kerja;
			$total_hk_sat = $total_hk_sat + $hk_sat;
			$total_hk_unit = $total_hk_unit + $rp_satuan;
			$tabel .= "<tr>";
			
			$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;".$row['LOCATION_CODE']."</td>";
			$tabel .= "<td class='tbl_td' width='8%' align = 'center'>&nbsp;&nbsp;</td>";
			$tabel .= "<td class='tbl_td' width='8%' align = 'center'>&nbsp;</td>";
			$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;".$row['ACCOUNTCODE']."</td>";
			$tabel .= "<td class='tbl_td' align = 'left'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;".$row['UNIT1']."</td>";
			$tabel .= "<td class='tbl_td' align = 'right'>".number_format($hasil_kerja,2,',','.')."&nbsp;&nbsp;&nbsp;</td>";
			$tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;&nbsp;</td>";
			$tabel .= "<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;&nbsp;</td></tr>";	
	
  	}
	
		$tabel .= "<tr><td class='tbl_td' colspan='5' align='center'><strong>&nbsp;&nbsp;TOTAL</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong></strong>&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_hasilkerja,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($total_hk_unit,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td></tr>";
		$tabel .= "</table>"; 
		echo $tabel;
	}
	
	//xls afd 
	function ba_xlsbibitan_rekap($periode){
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		if($bulan == '01'){ $bulan = "Januari"; $bulanr = "I";} 
		else if($bulan == '02'){ $bulan = "Februari"; $bulanr = "II"; } 
		else if($bulan == '03'){ $bulan = "Maret"; $bulanr = "III"; } 
		else if($bulan == '04'){ $bulan = "April"; $bulanr = "IV"; } 
		else if($bulan == '05'){ $bulan = "Mei"; $bulanr = "V"; } 
		else if($bulan == '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
		else if($bulan == '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
		else if($bulan == '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
		else if($bulan == '09'){ $bulan = "September"; $bulanr = "IX"; } 
		else if($bulan == '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
		else if($bulan == '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
		else if($bulan == '12'){ $bulan = "Desember"; $bulanr = "XII"; }
		
		$company = $this->session->userdata('DCOMPANY');
		$data_bibitan = $this->model_rpt_ba->ba_bibitan_afd($from, $to, $rkp, $company);
		$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = "";	
		foreach ( $data_umr as $row_umr){
			$umr .= $row_umr['UMR'];
		}
		
		$total = 0;
		$total_hk = 0;
		$total_qty = 0;
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REKAP REALISASI KERJA PEMBIBITAN \n";
		$judul .= "NO :     / BBT / ".$company." / ".$bulanr." / ".$tahun."\n";
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
		
			
		foreach ( $data_bibitan as $row){
			
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			//if ($row['ACCOUNTCODE'] == '8602001') {
			//	$total_qty = $total_qty + $row['HASIL_KERJA'] ;
			//} 
			
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
			} else {
				$rp_satuan = $realisasi;
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
								
				$data .= trim($line)."\n";		
		}
			
		$footer .= " 8300000 \t";
		$footer .= " BIBITAN / NURSERY \t";
		$footer .= " Pkk \t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= " - \t";
		$footer .= " - \t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_BIBITAN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	//xls blok 
	function ba_xlsbibitan_petak( $periode){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$rkp = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		if($bulan == '01'){ $bulan = "Januari"; $bulanr = "I";} 
		else if($bulan == '02'){ $bulan = "Februari"; $bulanr = "II"; } 
		else if($bulan == '03'){ $bulan = "Maret"; $bulanr = "III"; } 
		else if($bulan == '04'){ $bulan = "April"; $bulanr = "IV"; } 
		else if($bulan == '05'){ $bulan = "Mei"; $bulanr = "V"; } 
		else if($bulan == '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
		else if($bulan == '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
		else if($bulan == '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
		else if($bulan == '09'){ $bulan = "September"; $bulanr = "IX"; } 
		else if($bulan == '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
		else if($bulan == '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
		else if($bulan == '12'){ $bulan = "Desember"; $bulanr = "XII"; }
		
		$company = $this->session->userdata('DCOMPANY');
		$data_bibitan = $this->model_rpt_ba->ba_bibitan_afd($from, $to, $rkp, $company);
		$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = "";	
		foreach ( $data_umr as $row_umr){
			$umr .= $row_umr['UMR'];
		}
		
		$total = 0;
		$total_hasilkerja = 0;
		$total_hk = 0;
		$total_hk_sat = 0;
		$total_hk_unit = 0;
		$total_qty = 0;
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REALISASI KERJA PEMBIBITAN \n";
		$judul .= "PERIODE : ".strtoupper($bulan)."  " .$tahun."\n";
		$judul .= " \n";
		$judul .= "HK / HARI : " .$umr. " \n";
		
		$headers .= "KODE BLOK \t";
		$headers .= "LUAS BLOK \t";
		$headers .= "JML POKOK \t";
		$headers .= "KODE AKTIVITAS \t";	
		$headers .= "NAMA AKTIVITAS \t";
		$headers .= "SAT \t";
		$headers .= "QTY \t";
		$headers .= "REALISASI (Rp.) \t";
		$headers .= "Rp. / SAT \t";
			
		foreach ( $data_bibitan as $row){
			
				$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI'];
				$total_hk = $total_hk + $row['HK'];
				$total = $total + $realisasi;
				$hasil_kerja = $row['HASIL_KERJA']; 
				
				//if ($row['ACCOUNTCODE'] == '8602001') {
				//	$total_qty = $total_qty + $row['HASIL_KERJA'] ;
				//} 
			
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;
				} else {
					$rp_satuan = $realisasi;
				}
				
				$total_hasilkerja = $total_hk + $hasil_kerja;
				$total_hk_unit = $total_hk_unit + $rp_satuan;
				
				$line = '';
						
				$line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
				$line .= str_replace('"', '""',"-")."\t";
				$line .= str_replace('"', '""',"-")."\t";
				$line .= str_replace('"', '""',$row['ACCOUNTCODE'])."\t";
				$line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
				$line .= str_replace('"', '""',$row['UNIT1'])."\t";
				$line .= str_replace('"', '""',$hasil_kerja)."\t";
				$line .= str_replace('"', '""',$realisasi)."\t";
				$line .= str_replace('"', '""',$rp_satuan)."\t";
						
				$data .= trim($line)."\n";		
		}
			
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " TOTAL \t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""','-')."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total_hk_unit)."\t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_BIBITAN_PERPETAK_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
}

?>