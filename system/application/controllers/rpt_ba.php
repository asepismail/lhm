<?
class rpt_ba extends Controller 
{
	function rpt_ba ()
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
	    //fpdf table defintion file
	    require_once(APPPATH . 'libraries/table_def.inc');
	}
	
		
	function index()
    {
		$data = array();
			
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		
		if ($data['login_id'] == TRUE){
			$this->load->view('rpt_ba', $data);
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
	
	// ------------------------------ rawat punya ------------------------------------------------- //
	function js_ba_rawat(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			
			
						
			if ( jns_laporan = 'html'){
				if(afd == ''){ 
					alert('pilih afd terlebih dahulu!!') 
				} else {
					var jns_laporan = $('#jns_laporan').val();	
					var urls = url + 'rpt_ba/ba_rawat_rekap_afd/' + afd + '/' + periode; 
					$('#frame').attr('src',urls);
				}
			}
		});";
		return $js;
	}
	
	function ba_rawat(){
		$view = "rpt_ba_rawat";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Rawat";
		$data['js'] = $this->js_ba_rawat();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
			
	}
		
	function ba_rawat_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_rawat_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong>8500000</strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;RAWAT TANAMAN (UPKEEP)</strong></td>
    <td class='tbl_td' align = 'center'>Ha</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ panen punya ------------------------------------------------- //
	function js_ba_panen(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_panen_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_panen(){
		$view = "rpt_ba_panen";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Panen";
		$data['js'] = $this->js_ba_panen();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_panen_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_panen_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong>8601000</strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;PANEN</strong></td>
    <td class='tbl_td' align = 'center'>Kg</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ transport panen punya ------------------------------------------------- //
	function js_ba_tpanen(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_tpanen_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_tpanen(){
		$view = "rpt_ba_tpanen";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Transport Panen";
		$data['js'] = $this->js_ba_tpanen();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_tpanen_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_tpanen_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong>8602000</strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TRANSPORT PANEN</strong></td>
    <td class='tbl_td' align = 'center'>Kg</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ LC punya ------------------------------------------------- //
	function js_ba_lc(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_lc_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_lc(){
		$view = "rpt_ba_lc";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Land Preparation";
		$data['js'] = $this->js_ba_lc();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_lc_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_lc_afd($afd, $periode, $company);
		$total = 0;
		$tabel = "";
		$tabel .= "<table style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' cellpadding='0' cellspacing='0'>";
		$tabel .= "<th align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>ACTIVITY</th><th align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>UNIT</th><th align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>REALISASI</th>";
		foreach ( $data_rawat as $row){
			$total = $total + $row['REALISASI']; 
			$tabel .= "<tr><td width='80px' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$row['ACTIVITY_CODE']."</td><td align='left' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' width='400px'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td><td width='80px' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$row['UNIT1']."</td><td width='160px' align='right' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Rp. ". number_format($row['REALISASI'],2)." &nbsp;</td></tr>";
			
		}
		$tabel .= "<tr><td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='3' align = 'center'><strong>Total</strong></td><td style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' align = 'right'><strong>Rp. ".number_format($total,2)." &nbsp;</strong></td></tr>";
		$tabel .= "</table>";
		
		echo $tabel;
	}
	
	// ------------------------------ Bibitan punya ------------------------------------------------- //
	function js_ba_bibitan(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_bibitan_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_bibitan(){
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
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_bibitan_rekap_afd($periode){
		
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_bibitan_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong>8300000</strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;BIBITAN / NURSERY</strong></td>
    <td class='tbl_td' align = 'center'>Pkk</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ sisip punya ------------------------------------------------- //
	function js_ba_sisip(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_sisip_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_sisip(){
		$view = "rpt_ba_sisip";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Sisip Kelapa Sawit";
		$data['js'] = $this->js_ba_sisip();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_sisip_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_sisip_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong>8402000</strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA SISIP KELAPA SAWIT</strong></td>
    <td class='tbl_td' align = 'center'>Pkk</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ rawat infras punya ------------------------------------------------- //
	function js_ba_rinfrastruktur(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_rinfrastruktur_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_rinfrastruktur(){
		$view = "rpt_ba_rinfrastruktur";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Rawat Infrastruktur";
		$data['js'] = $this->js_ba_rinfrastruktur();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_rinfrastruktur_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_rinfrastruktur_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA RAWAT INFRASTRUKTUR</strong></td>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ umum punya ------------------------------------------------- //
	function js_ba_umum(){
		
		$js = "$(function() {
                    $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
                    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
                });
        jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_umum_rekap_afd/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_umum(){
		$view = "rpt_ba_umum";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Umum";
		$data['js'] = $this->js_ba_umum();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_umum_rekap_afd($periode){
		
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_umum_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2'>AKTIVITAS</td><th class='tbl_th' colspan='2'>COST CENTER</th><th class='tbl_th' colspan='2'>REALISASI BIAYA</th></tr><tr><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
	
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr><td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td' align = 'center'>".$row['LOCATION_CODE']."</td>
	<td class='tbl_td'>&nbsp;&nbsp;".$row['DESCRIPTION']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td></tr>";
		
		}

		$tabel .= "<tr>
    <td colspan='3' class='tbl_td' align='center'><strong>&nbsp;&nbsp;TOTAL BIAYA</strong></td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
 	 </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	
	// ------------------------------ vehicle mesin workshop punya ------------------------------------------------- //
	function js_ba_vmw(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_vmw_rekap_afd/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_vmw(){
		$view = "rpt_ba_vmw";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Kendaraan, Mesin, Dan Workshop";
		$data['js'] = $this->js_ba_vmw();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
				
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_vmw_rekap_afd($periode){
		
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_vmw_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th>
   <th class='tbl_th'  colspan='2'>REALISASI BIAYA ( Rp )</th>
   <th class='tbl_th' colspan='2'>HK</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr><td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
	
	}
   
	$tabel .= "<tr>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA GAJI KENDARAAN, MESIN, WORKSHOP</strong></td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ sisip punya ------------------------------------------------- //
	function js_ba_pjbibitan(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_pjbibitan_rekap_afd/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_pjbibitan(){
		$view = "rpt_ba_pjbibitan";
		$data = array();
		$data['judul_header'] = "Berita Acara Hasil Kerja Project Persiapan Pembibitan";
		$data['js'] = $this->js_ba_pjbibitan();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_pjbibitan_rekap_afd($periode){
		
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_pjbibitan_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong></strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA PEMBUKAAN LAHAN BIBITAN</strong></td>
    <td class='tbl_td' align = 'center'>Pkk</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	// ------------------------------ pj tanam punya ------------------------------------------------- //
	function js_ba_pjtanam(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_pjtanam_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_pjtanam(){
		$view = "rpt_ba_pjtanam";
		$data = array();
		$data['judul_header'] = "Berita Acara Hasil Kerja Project Tanam";
		$data['js'] = $this->js_ba_pjtanam();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['AFD'] = $this->dropdownlist_afd();
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_pjtanam_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_pjtanam_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;BIAYA PROJECT TANAMAN</strong></td>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	
	// ------------------------------ PJ Infras ------------------------------------------------- //
	function js_ba_pjinfras(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var afd = $('#afd').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan = 'html'){
				urls = url + 'rpt_ba/ba_pjinfras_rekap_afd/' + afd + '/' + periode; 
				$('#frame').attr('src',urls); 
			}
		});";
		return $js;
	}
	
	function ba_pjinfras(){
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
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				//$this->load->view('rpt_ba_rawat', $data);
				show($view, $data);
			} 
		} else {
			redirect('login');
		}
		
	}
	
	function ba_pjinfras_rekap_afd($afd, $periode){
		
		$afd = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_rawat = $this->model_rpt_ba->ba_pjinfras_afd($afd, $periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			</style>";
		$tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th><th class='tbl_th' rowspan='2'>SAT</th>
    <th class='tbl_th' colspan='2'>Hasil Kerja</th><th class='tbl_th'  colspan='2'>Realisasi Biaya ( Rp )</th>
    <th class='tbl_th' colspan='2'>Rp / Sat</th><th class='tbl_th' colspan='2'>HK/Sat</th></tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th>
    <th class='tbl_th'>s.d BLN INI</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_rawat as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr>";
    if($row['PARENT'] != "1"){
		$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	<td class='tbl_td' align = 'center'>".$row['UNIT1']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>&nbsp; N/A &nbsp;</td>
    <td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td>
	<td class='tbl_td' align = 'right'>".$row['HK']."&nbsp;&nbsp;</td></tr>";
		
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
		$tabel .= "<tr>
    <td class='tbl_td' align = 'center'><strong></strong></td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA PROJECT INFRASTRUKTUR</strong></td>
    <td class='tbl_td' align = 'center'></td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td'>&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total_hk)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
}

?>