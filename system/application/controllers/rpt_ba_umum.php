<?
class rpt_ba_umum extends Controller 
{
	function rpt_ba_umum ()
	{
		parent::Controller();	

		$this->load->model( 'model_rpt_ba' ); 
        
        $this->load->model('model_c_user_auth'); 
        $this->lastmenu="rpt_ba_umum";
		
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
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		
		if ($data['login_id'] == TRUE){
			//if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
			show($view, $data);
			//} 
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
			if ( jns_laporan == 'html'){
				urls = url + 'rpt_ba_umum/ba_umum_rekap_afd/' + $('#FROM').val() + '/' + $('#TO').val(); 
				$('#frame').attr('src',urls);                                       
			} else if ( jns_laporan == 'excell'){
				urls = url + 'rpt_ba_umum/ba_xlsumum_rekap/' + $('#FROM').val() + '/' + $('#TO').val(); 
				$.download(urls,'');
			}
		});";
		return $js;
	}
	
	function ba_umum_rekap_afd(){	
		$from = str_replace("-","",$this->uri->segment(3));
        $to = str_replace("-","",$this->uri->segment(4));
        $periode = substr(str_replace("-","",$to),0,6);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$data_umum = $this->model_rpt_ba->ba_umum_afd($from, $to, $periode,$company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= $this->getStyle();
		$tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'>
					<strong>BERITA ACARA GAJI UMUM</strong></td></tr>
					<tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp; / UMM / ".$company." / ".$bulanr." / ".$tahun." </strong></td></tr>
					<tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." 
					&nbsp;" .$tahun. "</strong></td></tr><tr><td colspan='3'>&nbsp;</td></tr>
					<tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
		$tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' colspan='2'>AKTIVITAS</td><th class='tbl_th' colspan='2'>COST CENTER</th>
					<th class='tbl_th' colspan='2'>REALISASI BIAYA</th></tr><tr><th class='tbl_th'>KODE</th>
					<th class='tbl_th'>NAMA</th><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th>
					<th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
	
		foreach ( $data_umum as $row){
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr><td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
						<td class='tbl_td' align = 'left'> &nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
						<td class='tbl_td' align = 'center'>".$row['LOCATION_CODE']."</td>
						<td class='tbl_td'>&nbsp;&nbsp;".$row['DESCRIPTION']."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td>
   					 <td class='tbl_td' align = 'right'>".number_format($realisasi)."&nbsp;&nbsp;</td></tr>";
		
		}

		$tabel .= "<tr><td colspan='4' class='tbl_td' align='center'><strong>&nbsp;&nbsp;TOTAL BIAYA</strong></td>
    				<td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    				<td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td></tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	function ba_xlsumum_rekap($from,$to){
		$from = str_replace("-","",$this->uri->segment(3));
        $to = str_replace("-","",$this->uri->segment(4));
        $periode = substr(str_replace("-","",$to),0,6);
		$company = $this->session->userdata('DCOMPANY');
		$data = array();
		
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);
		$bulan = $this->bln_to_periode($bulan);
		$bulanr = $this->bln_to_rperiode($bulan);
		
		$data_umum = $this->model_rpt_ba->ba_umum_afd($from, $to, $periode,$company);
		
		$total = 0;
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "REKAP REALISASI GAJI UMUM \n";
		$judul .= "NO :     / UMM / ".$company." / ".$bulanr." / ".$tahun."\n";
		$judul .= "PERIODE : ".strtoupper($bulan)." " .$tahun."\n";
		$judul .= " \n";
		
		$headers .= "KODE AKTIVITAS \t";
		$headers .= "DESKRIPSI AKTIVITAS \t";
		$headers .= "KODE COST CENTER \t";	
		$headers .= "NAMA COST CENTER \t";
		$headers .= "REALISASI BLN INI \t";
		$headers .= "REALISASI S.D BLN INI \t";
					
		foreach ( $data_umum as $row){
			
			$realisasi = $row['REAL_BIAYA_BI']; 
			/* $realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] - $row['PENALTI']; */
			$total = $total + $realisasi;
				
				$line = '';
						
				$line .= str_replace('"', '""',$row['ACCOUNTCODE'])."\t";
				$line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
				$line .= str_replace('"', '""',$row['DESCRIPTION'])."\t";
				$line .= str_replace('"', '""',$realisasi)."\t";
				$line .= str_replace('"', '""',$realisasi)."\t";
								
				$data .= trim($line)."\n";		
		}
			
		$footer .= " - \t";
		$footer .= " TOTAL BIAYA \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_UMUM_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
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
}

?>