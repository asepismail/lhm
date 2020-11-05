<?
class rpt_ba_panen extends Controller 
{
	function rpt_ba_panen ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_ba' );
        $this->load->model('model_c_user_auth'); 
        $this->lastmenu="rpt_ba_panen"; 
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
		$view = "rpt_ba_panen";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Panen";
		$data['js'] = $this->js_ba_panen();	
		$data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
		$data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
		$data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
		$data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
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
		
		foreach ( $data_afd as $row)
		{
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string." <option value=\"".$row['AFD']."\"  selected>".$row['AFD']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD']."\">".$row['AFD']." </option>";
			}
		}
		
		$string =$string. "</select>";
		return $string;
	}
	
	// ------------------------------ panen punya ------------------------------------------------- //
	function js_ba_panen(){
		
		$js = "
			$(function() {
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
					$('#TO').datepicker({dateFormat:'yy-mm-dd'});
				});
			jQuery('#submitdata').click(function (){
			var afd = $('#afd').val();
			
			var tfrom = document.getElementById('FROM').value;
			var elem = tfrom.split('-');
			from = elem[0]+elem[1]+elem[2];
							
			var tto = document.getElementById('TO').value;
			var elem2 = tto.split('-');
			to = elem2[0]+elem2[1]+elem2[2];
						
			var period = to - from;
			var blok = $('#blok').val();
			var rkp = $('#rkp').val();
			var nw = $('#newwindow').is(':checked');
			var urlafd = url + 'rpt_ba_panen/ba_panen_rekap_afd/' + afd + '/' + from  + '/' + to + '/' + rkp; 
			var urlbl = url + 'rpt_ba_panen/ba_panen_rekap_block/' + afd + '/' + from  + '/' + to + '/' + rkp;
			
			if(rkp == 'rekap'){
				urls = urlafd;
			} else {
				urls = urlbl;
			}
			
			if(afd == ''){ 
					alert('pilih afd terlebih dahulu!!') 
			} else {
					var jns_laporan = $('#jns_laporan').val();	
					if ( jns_laporan == 'html'){
						if(nw != false) {	
							$('#frame').attr('src','');
							$('.button').popupWindow({ 
									windowURL:urls,
									windowName:'Rekap Biaya Gaji Panen AFD : '+ afd,
									width:800 
							}); 
						} else {
							$('#frame').attr('src',urls);
						}
					} else if ( jns_laporan == 'excell'){
						if(rkp == 'rekap'){
							urls = url + 'rpt_ba_panen/ba_xlspanen_rekap_afd/' + afd + '/'+ from +'/'+ to +'/'+rkp; 
						} else {
							urls = url + 'rpt_ba_panen/ba_xlspanen_rekap_block/' + afd + '/'+ from +'/'+ to +'/'+rkp; 
						}
						$.download(urls,'');
					} else if (jns_laporan =='pdf') {	
						if(rkp == 'rekap'){ 
							urls = url + 'rpt_ba_panen/ba_pdfpanen_rekap_afd/' + afd + '/' + from  + '/' + to + '/' +rkp; 
						} else {	
							urls = url + 'rpt_ba_panen/ba_pdfpanen_rekap_block/' + afd + '/'+ from  + '/' + to + '/' + rkp;
						}	
						$('#frame').attr('src',urls);
					}
			 }
		});";
		return $js;
	}
	
	function ba_panen_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
				
		$data = array();
		
		$bulan = substr($from,4,2); $tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan); $bulanr = $this->bln_to_rperiode($bulan);

		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
		
		$total = 0;
		$total_hk = 0;
		$total_qty = 0;
		$rp_satuan_ttl = 0;
		
		/* variabel untuk tabel */	
		$tdstart_right = "<td class='tbl_td' align = 'right'>";
		$tdstart_left = "<td class='tbl_td' align = 'left'>";
		$tdstart_center = "<td class='tbl_td' align = 'center'>";
		$tdstart = "<td class='tbl_td'>";
		$tdclose = "&nbsp;&nbsp;</td>";
		$tdclose_padding = "&nbsp;&nbsp;</td>";
		/* variabel untuk tabel */
		
		$tabel = "";
		$tabel .= $this->getStyle();
$tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>BERITA ACARA HASIL KERJA PANEN</strong></td>";
$tabel .= "</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ ". strtoupper($afd) ." / PNN / ".$company." / ".$bulanr." / ".$tahun." </strong></td>";
		$tabel .= "</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." &nbsp;" .$tahun. "</strong></td>";
		$tabel .= "</tr><tr><td colspan='3'>&nbsp;</td>";
		$tabel .= "</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td>";
		$tabel .= "</tr><tr><td>AFD &nbsp;  :  ". strtoupper($afd) ."</td><td></td><td></td></tr></table>";
		$tabel .= "<table width='85%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>";
		$tabel .= "<th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>";
		$tabel .= "<th class='tbl_th' colspan='2'>Rp / Sat</th></tr>";
		$tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th>
					<th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI";		
		$tabel .= "</th><th class='tbl_th'>BLN INI</th>";
		$tabel .= "<th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_panen as $row){
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			if ($row['ACCOUNTCODE'] == '8601003') {
				$total_qty = $total_qty + $row['HASIL_KERJA'] ;
			} 
				
			if ( $total_qty != 0 ){ $rp_satuan_ttl = $total / $total_qty; } else { $rp_satuan_ttl = 0; }
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;	
				} else {
					$rp_satuan = 0;
				}
			
				$tabel .= "<tr>";
				$tabel .= $tdstart_center.$row['ACCOUNTCODE'].$tdclose;
				$tabel .= $tdstart."&nbsp;&nbsp;".$row['COA_DESCRIPTION'].$tdclose;
				$tabel .= $tdstart_center."&nbsp;".$row['UNIT1'].$tdclose;
				$tabel .= $tdstart_right.number_format($row['HASIL_KERJA'],2,',','.').$tdclose_padding;
				$tabel .= $tdstart_right.number_format($row['HASIL_KERJA'],2,',','.').$tdclose_padding;
				$tabel .= $tdstart_right.number_format($realisasi,2,',','.').$tdclose_padding;
				$tabel .= $tdstart_right.number_format($realisasi,2,',','.').$tdclose_padding;
				$tabel .= $tdstart_right.number_format($rp_satuan,2,',','.').$tdclose_padding;
				$tabel .= $tdstart_right.number_format($rp_satuan,2,',','.').$tdclose_padding;"</tr>";
			}
	
		$tabel .= "<tr>".$tdstart_center."<strong>8601000</strong>".$tdclose;
		$tabel .= $tdstart."<strong>&nbsp;&nbsp;PANEN</strong>".$tdclose;
		$tabel .= $tdstart_center."&nbsp;Kg".$tdclose;
		$tabel .= $tdstart_right."<strong>".number_format($total_qty,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= $tdstart_right."<strong>".number_format($total_qty,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= $tdstart_right."<strong>".number_format($total,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= $tdstart_right."<strong>".number_format($total,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= $tdstart_right."<strong>".number_format($rp_satuan_ttl,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= $tdstart_right."<strong>".number_format($rp_satuan_ttl,2,',','.')."</strong>".$tdclose_padding;
		$tabel .= "</tr></table>"; 
		
		echo $tabel;
	}
	
	function ba_panen_rekap_block($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
			
		$total = 0;
		$total_hk = 0;
		$total_hk_unit = 0;
		$total_qty = 0;
		
		/* variabel untuk tabel */	
		$tdstart_right = "<td class='tbl_td' align = 'right'>";
		$tdstart_left = "<td class='tbl_td' align = 'left'>";
		$tdstart_center = "<td class='tbl_td' align = 'center'>";
		$tdstart = "<td class='tbl_td'>";
		$tdclose = "&nbsp;&nbsp;</td>";
		$tdclose_padding = "&nbsp;&nbsp;</td>";
		/* variabel untuk tabel */
		
		$tabel = "";
		$tabel .= $this->getStyle();
		$tabel .= "<table class='tbl_2' border='0'><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td>
					</tr><tr><td colspan='3'>REALISASI KERJA PANEN</td>
					</tr><tr><td>AFD</td><td>:</td><td>".strtoupper($afd)."</td></tr></table>";
		$tabel .= "<table width='85%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th rowspan='2' class='tbl_th'>KODE BLOK</th><th rowspan='2' class='tbl_th'>LUAS BLOK</th>
					<th rowspan='2' class='tbl_th'>JML POKOK</th>
					<th colspan='2' class='tbl_th'>AKTIVITAS</th><th rowspan='2' class='tbl_th'>SAT</th>
					<th rowspan='2' class='tbl_th'>QTY</th><th rowspan='2' class='tbl_th'>REALISASI (Rp)</th>
					<th rowspan='2' class='tbl_th'>Rp / SAT</th>
					</tr><tr><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th></tr>";
	$rp_satuan_ttl = 0;
	foreach ( $data_panen as $row){
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];		
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			if ($row['ACCOUNTCODE'] == '8601003') {
				$total_qty = $total_qty + $hasil_kerja ;
			} 
			
			if ( $total_qty != 0 ){ 
				$rp_satuan_ttl = $total / $total_qty; 
			} else { 
				$rp_satuan_ttl = 0; 
			}
			
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;	
			} else {
				$rp_satuan = 0;
			}
			
			$total = $total + $realisasi;
			$total_hk_unit = $total_hk_unit + $rp_satuan_ttl;
			$tabel .= "<tr>";
			
   	$tabel .= $tdstart_center."&nbsp;&nbsp;".$row['LOCATION_CODE']."</td>
				<td class='tbl_td' width='8%' align = 'right'>".number_format($row['HECTPLANTED'],2,',','.')."&nbsp;&nbsp;</td>
			<td class='tbl_td' width='8%' align = 'right'>".number_format($row['NUMPLANTATION'],2,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;".$row['ACCOUNTCODE']."</td>
				<td class='tbl_td' align = 'left'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
				<td class='tbl_td' align = 'center'>&nbsp;".$row['UNIT1']."</td>
				<td class='tbl_td' align = 'right'>".number_format($hasil_kerja,2,',','.')."&nbsp;&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;&nbsp;</td>
				<td class='tbl_td' align = 'right'>".number_format($rp_satuan,2,',','.')."&nbsp;&nbsp;&nbsp;</td></tr>";	
  	}
	
		$tabel .= "<tr><td class='tbl_td' colspan='3'> &nbsp;</td><td class='tbl_td' align='center'>
				<strong>&nbsp;&nbsp;8601000 </strong></td><td  class='tbl_td'><strong>&nbsp;&nbsp;PANEN</strong></td>
				<td class='tbl_td' align = 'center'><strong>Kg</strong></td>
				<td class='tbl_td' align = 'right'><strong>".number_format($total_qty,2,',','.')."</strong>&nbsp;
				</strong>&nbsp;&nbsp;</td>
			<td class='tbl_td' align = 'right'><strong>".number_format($total,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td>
			<td class='tbl_td' align = 'right'><strong>".number_format($rp_satuan_ttl,2,',','.')."</strong>&nbsp;&nbsp;&nbsp;</td></tr>";
		$tabel .= "</table>"; 
		echo $tabel;
	}
	
	//xls afd 
	function ba_xlspanen_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2); $tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan); $bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
		
		//$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0;
		$total_hk = 0;
		$total_qty = 0;
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REKAP REALISASI KERJA PANEN \n";
		$judul .= "NO :      / PT / RWT / ".$company." / ".$bulanr." / ".$tahun."\n";
		$judul .= "PERIODE : ".strtoupper($bulan)." " .$tahun."\n";
		$judul .= "AFD : ".strtoupper($afd)."\n";
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
		
			
		foreach ( $data_panen as $row){
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$hasil_kerja = $row['HASIL_KERJA']; 
			
			if ($row['ACCOUNTCODE'] == '8601003') {
				$total_qty = $total_qty + $row['HASIL_KERJA'] ;
			} 
			
			if ( $total_qty != 0 ){ $rp_satuan_ttl = $total / $total_qty; } else { $rp_satuan_ttl = 0; }
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;	
			} else {
				$rp_satuan = 0;
			}
				
			$total = $total + $realisasi;
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
			
		$footer .= " 8601003 \t";
		$footer .= " PANEN \t";
		$footer .= " Ha \t";
		$footer .= str_replace('"', '""',$total_qty)."\t";
		$footer .= str_replace('"', '""',$total_qty)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= " - \t";
		$footer .= " - \t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_PANEN_PERAFD_".$company."_".strtoupper($afd)."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	//xls blok 
	function ba_xlspanen_rekap_block($afd, $periode){
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($from,4,2); $tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan); $bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
		//$data_umr = $this->model_rpt_ba->get_umr($company);
		//$data_umr = $this->model_rpt_ba->get_umr($company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total = 0; $total_hasilkerja = 0; $total_hk = 0; $total_hk_sat = 0;
		$total_hk_unit = 0; $total_qty = 0; $judul = '';
		$headers = ''; $data = ''; $footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REALISASI KERJA PANEN \n";
		$judul .= "PERIODE : ".strtoupper($bulan)."  " .$tahun."\n";
		$judul .= "AFD : ".strtoupper($afd)." \n";
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
			
		foreach ( $data_panen as $row){
			
				$realisasi = $row['REAL_BIAYA_BI']; 
				/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
				$total_hk = $total_hk + $row['HK'];
				$total = $total + $realisasi;
				$hasil_kerja = $row['HASIL_KERJA']; 
				
				if ($row['ACCOUNTCODE'] == '8601003') {
					$total_qty = $total_qty + $row['HASIL_KERJA'] ;
				} 
			
				if ( $total_qty != 0 ){ $rp_satuan_ttl = $total / $total_qty; } else { $rp_satuan_ttl = 0; }
				if ($hasil_kerja != 0){
					$rp_satuan = $realisasi / $hasil_kerja;	
				} else {
					$rp_satuan = 0;
				}
				
				$total_hasilkerja = $total_hk + $hasil_kerja;
				$total_hk_unit = $total_hk_unit + $rp_satuan;
				
				$line = '';
						
				$line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
				$line .= str_replace('"', '""',$row['HECTPLANTED'])."\t";
				$line .= str_replace('"', '""',$row['NUMPLANTATION'])."\t";
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
		$footer .= str_replace('"', '""',$total_qty)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total_hk_unit)."\t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
       header("Content-Disposition: attachment; filename=BA_PANEN_PERBLOK_".$company."_".strtoupper($afd)."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	/* ############## printing BA to PDF ############################# */
	function ba_pdfpanen_rekap_afd($afd, $periode){
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
		$company_name =  $this->session->userdata('DCOMPANY_NAME');
				
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
		$umr = $this->getUmr($company,  substr($from,0,4));
		
		$total_qty = 0;
		$total = 0;
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->SetMargins(5, 12);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		
		require_once(APPPATH . 'libraries/ba/header_ba_panen.inc');
		
		require_once(APPPATH . 'libraries/ba/table_border.inc');
		
		$columns = 9; //number of Columns
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
				$aSimpleHeader[$i]['WIDTH'] = 90;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "SAT";
				$aSimpleHeader[$i]['WIDTH'] = 20;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "HASIL KERJA";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "REALISASI BIAYA ( Rp. )";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 30;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "RP / SAT";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 30;
			}
			
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = "";
				$aSimpleHeader2[$i]['COLSPAN'] = 2;
				$aSimpleHeader2[$i]['WIDTH'] = 10;				
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
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 30;
			}
		}
		
		$aHeader = array( $aSimpleHeader, $aSimpleHeader2);
		
		$pdf->tbSetHeaderType($aHeader, TRUE);
		
		$pdf->tbDrawHeader();
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		//require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
		$ttl_realisasi = 0;
		foreach ($data_panen as $row)
		{
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$hasil_kerja = $row['HASIL_KERJA']; 
			$ttl_realisasi = $ttl_realisasi + $realisasi;
						
			if ($row['ACCOUNTCODE'] == '8601003') {
				$total_qty = $total_qty + $row['HASIL_KERJA'] ;
			} 
				
			if ( $total_qty != 0 ){ $rp_satuan_ttl = $total / $total_qty; } else { $rp_satuan_ttl = 0; }
			
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;	
			} else {
				$rp_satuan = 0;
			}
			
			$data = Array();
			$data[0]['TEXT'] = $row['ACCOUNTCODE'];
			$data[0]['T_SIZE'] = 9;
			$data[0]['LN_SIZE'] = 5;
			$data[1]['TEXT'] = $row['COA_DESCRIPTION'];
			$data[1]['T_SIZE'] = 9;
			$data[1]['LN_SIZE'] = 5;
			$data[1]['T_ALIGN'] = "L";
			$data[2]['TEXT'] = $row['UNIT1'];
			$data[2]['T_SIZE'] = 9;
			$data[2]['LN_SIZE'] = 5;
			$data[3]['TEXT'] = number_format($hasil_kerja,2,'.',',')." " ;
			$data[3]['T_SIZE'] = 9;
			$data[3]['LN_SIZE'] = 5;
			$data[3]['T_ALIGN'] = "R";
			$data[4]['TEXT'] = number_format($hasil_kerja,2,'.',',')." ";
			$data[4]['T_SIZE'] = 9;
			$data[4]['LN_SIZE'] = 5;
			$data[4]['T_ALIGN'] = "R";
			$data[5]['TEXT'] = number_format($realisasi,2,'.',',')." ";
			$data[5]['T_SIZE'] = 9;
			$data[5]['LN_SIZE'] = 5;
			$data[5]['T_ALIGN'] = "R";
			$data[6]['TEXT'] = number_format($realisasi,2,'.',',')." ";
			$data[6]['T_SIZE'] = 9;
			$data[6]['LN_SIZE'] = 5;
			$data[6]['T_ALIGN'] = "R";
			$data[7]['TEXT'] = number_format($rp_satuan,2,'.',',')." ";
			$data[7]['T_SIZE'] = 9;
			$data[7]['LN_SIZE'] = 5;
			$data[7]['T_ALIGN'] = "R";
			$data[8]['TEXT'] = number_format($rp_satuan,2,'.',',')." ";
			$data[8]['T_SIZE'] = 9;
			$data[8]['LN_SIZE'] = 5;
			$data[8]['T_ALIGN'] = "R";
			$pdf->tbDrawData($data);
		}
		
		$data2 = Array();
		$data2[0]['TEXT'] = "8601000";
		$data2[0]['T_SIZE'] = 9;
		$data2[0]['LN_SIZE'] = 5;
		$data2[1]['TEXT'] = "PANEN";
		$data2[1]['T_SIZE'] = 9;
		$data2[1]['LN_SIZE'] = 5;
		$data2[1]['T_ALIGN'] = "L";
		$data2[2]['TEXT'] = "Kg";
		$data2[2]['T_SIZE'] = 9;
		$data2[2]['LN_SIZE'] = 5;
		$data2[3]['TEXT'] = " - " ;
		$data2[3]['T_SIZE'] = 9;
		$data2[4]['TEXT'] = " - ";
		$data2[4]['T_SIZE'] = 9;
		$data2[5]['TEXT'] = number_format($ttl_realisasi,2,'.',',')." ";
		$data2[5]['T_SIZE'] = 9;
		$data2[5]['LN_SIZE'] = 5;
		$data2[5]['T_ALIGN'] = "R";
		$data2[6]['TEXT'] = number_format($ttl_realisasi,2,'.',',')." ";
		$data2[6]['T_SIZE'] = 9;
		$data2[6]['LN_SIZE'] = 5;
		$data2[6]['T_ALIGN'] = "R";
		$data2[7]['TEXT'] = " - ";
		$data2[7]['T_SIZE'] = 9;
		$data2[8]['TEXT'] = " - ";
		$data2[8]['T_SIZE'] = 9;
			
		$pdf->tbDrawData($data2);
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->Ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
		$pdf->Output();
		
	}
	
	function ba_pdfpanen_rekap_block($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(5);
		$rkp = $this->uri->segment(6);
		$company = $this->session->userdata('DCOMPANY');
		$company_name = $this->session->userdata('DCOMPANY_NAME');
		
		$data = array();
		
		$bulan = substr($from,4,2);
		$tahun = substr($from,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$company = $this->session->userdata('DCOMPANY');
		$data_panen = $this->model_rpt_ba->ba_panen_afd($afd, $rkp, $from, $to, $company);
			
		$total = 0;
		$total_hk = 0;
		$total_hk_unit = 0;
		$total_qty = 0;
		
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->SetMargins(5, 12);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		$pdf->Ln(2);
		$jdl = "PANEN";
		require_once(APPPATH . 'libraries/ba/header_ba_rawat_blok.inc');
		
		require_once(APPPATH . 'libraries/ba/table_border.inc');
		$pdf->Ln(2);
		$columns = 9; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader[$i]['TEXT'] = "KODE BLOK";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 1) {
				$aSimpleHeader[$i]['TEXT'] = "LUAS BLOK";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "JML POKOK";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "AKTIVITAS";
				$aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
				$aSimpleHeader[$i]['LN_SIZE'] = 5;
			}
			if($i == 4) {
				$aSimpleHeader[$i]['TEXT'] = "";
				$aSimpleHeader[$i]['WIDTH'] = 90;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "SAT";
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['WIDTH'] = 31;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = "QTY";
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['WIDTH'] = 31;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "REALISASI (Rp)";
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['WIDTH'] = 31;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = "RP / SAT";
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
				$aSimpleHeader[$i]['WIDTH'] = 31;
				$aSimpleHeader[$i]['T_SIZE'] = 10;
			}
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 1) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 2) {
				$aSimpleHeader2[$i]['TEXT'] = "";	
			}
			if($i == 3) {
				$aSimpleHeader2[$i]['TEXT'] = "KODE";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 31;
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = "NAMA";
				$aSimpleHeader2[$i]['T_SIZE'] = 10;
				$aSimpleHeader2[$i]['LN_SIZE'] = 5;
				$aSimpleHeader2[$i]['WIDTH'] = 31;
			}
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
		}
		
		$aHeader = array( $aSimpleHeader, $aSimpleHeader2);
		
		$pdf->tbSetHeaderType($aHeader, TRUE);
		
		$pdf->tbDrawHeader();
		
		$aDataType = Array();
		
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		$ttl_realisasi = 0;
		
		$total = 0;
		$total_hasilkerja = 0;
		$total_hk = 0;
		$total_hk_sat = 0;
		
		foreach ($data_panen as $row)
		{
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* replace $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$hasil_kerja = $row['HASIL_KERJA']; 
					
			if ($hasil_kerja != 0){
				$rp_satuan = $realisasi / $hasil_kerja;
			} else {
				$rp_satuan = "";
			}
			
			$total_hasilkerja = $total_hasilkerja + $hasil_kerja;
			//$total_hk_sat = $total_hk_sat + $hk_sat;
						
			$data = Array();
			$data[0]['TEXT'] = $row['LOCATION_CODE'];
			$data[0]['T_SIZE'] = 9;
			$data[0]['LN_SIZE'] = 5;
			$data[1]['TEXT'] = number_format($row['HECTPLANTED'],2,',','.');
			$data[1]['T_SIZE'] = 9;
			$data[1]['LN_SIZE'] = 5;
			$data[1]['T_ALIGN'] = "R";
			$data[2]['TEXT'] = number_format($row['NUMPLANTATION'],2,',','.');
			$data[2]['T_SIZE'] = 9;
			$data[2]['LN_SIZE'] = 5;
			$data[2]['T_ALIGN'] = "R";
			$data[3]['TEXT'] = $row['ACCOUNTCODE'];
			$data[3]['T_SIZE'] = 9;
			$data[3]['LN_SIZE'] = 5;
			$data[3]['T_ALIGN'] = "C";
			$data[4]['TEXT'] = $row['COA_DESCRIPTION'];
			$data[4]['T_SIZE'] = 9;
			$data[4]['LN_SIZE'] = 5;
			$data[4]['T_ALIGN'] = "L";
			$data[5]['TEXT'] = $row['UNIT1'];
			$data[5]['T_SIZE'] = 9;
			$data[5]['LN_SIZE'] = 5;
			$data[5]['T_ALIGN'] = "C";
			$data[6]['TEXT'] = number_format($hasil_kerja,2,'.',',')." ";
			$data[6]['T_SIZE'] = 9;
			$data[6]['LN_SIZE'] = 5;
			$data[6]['T_ALIGN'] = "R";
			$data[7]['TEXT'] = number_format($realisasi,2,'.',',')." ";
			$data[7]['T_SIZE'] = 9;
			$data[7]['LN_SIZE'] = 5;
			$data[7]['T_ALIGN'] = "R";
			$data[8]['TEXT'] = number_format(floatval($rp_satuan),2,'.',',')." ";
			$data[8]['T_SIZE'] = 9;
			$data[8]['LN_SIZE'] = 5;
			$data[8]['T_ALIGN'] = "R";
			$pdf->tbDrawData($data);
		}
		
		$data2 = Array();
		$data2[0]['TEXT'] = "";
		$data2[0]['T_SIZE'] = 9;
		$data2[0]['COLSPAN'] = 3;
		$data2[0]['LN_SIZE'] = 5;
		$data2[1]['TEXT'] = "";
		$data2[1]['T_SIZE'] = 9;
		$data2[1]['LN_SIZE'] = 5;
		$data2[1]['T_ALIGN'] = "L";
		$data2[2]['TEXT'] = "";
		$data2[2]['T_SIZE'] = 9;
		$data2[2]['LN_SIZE'] = 5;
		$data2[3]['TEXT'] = "8601003" ;
		$data2[3]['T_SIZE'] = 9;
		$data2[3]['T_ALIGN'] = "C";
		$data2[4]['TEXT'] = "PANEN";
		$data2[4]['T_SIZE'] = 9;
		$data2[4]['T_ALIGN'] = "L";
		$data2[5]['TEXT'] = "Kg";
		$data2[5]['T_SIZE'] = 9;
		$data2[5]['LN_SIZE'] = 5;
		$data2[5]['T_ALIGN'] = "C";
		$data2[6]['TEXT'] = " - ";
		$data2[6]['T_SIZE'] = 9;
		$data2[6]['LN_SIZE'] = 5;
		$data2[6]['T_ALIGN'] = "C";
		$data2[7]['TEXT'] = number_format($total,2,'.',',')." ";
		$data2[7]['T_ALIGN'] = "R";
		$data2[7]['T_SIZE'] = 9;
		$data2[8]['TEXT'] = " - ";
		$data2[8]['T_SIZE'] = 9;
			
		$pdf->tbDrawData($data2); 
		
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->Ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
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
	
	function getStyle(){
		 	$style = "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
					.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
					.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
					.tbl_2 { font-size: 12px;color:#678197;}
					.content { font-size: 12px;color:#678197; }
					</style>";
			return $style;
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