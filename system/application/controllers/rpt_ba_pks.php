<?
class rpt_ba_pks extends Controller 
{
	function rpt_ba_pks ()
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
		$view = "rpt_ba_pks";
		$data = array();
		$data['judul_header'] = "Berita Acara Gaji Pabrik Kelapa Sawit";
		$data['js'] = $this->js_ba_pks();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
				
		if ($data['login_id'] == TRUE){
			//if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
			show($view, $data);
			//} 
		} else {
			redirect('login');
		}
	} 
		
	// ------------------------------ vehicle mesin workshop punya ------------------------------------------------- //
	function js_ba_pks(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan == 'html'){
				urls = url + 'rpt_ba_pks/ba_pks_rekap_afd/' + periode; 
				$('#frame').attr('src',urls); 
			} else if ( jns_laporan == 'excell'){
				urls = url + 'rpt_ba_pks/ba_xlspks_rekap/' + periode; 
				$.download(urls,'');
			}
		});";
		return $js;
	}
	
	function ba_pks_rekap_afd($periode){
		
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);
		if($bulan=='01'){ $bulan = "Januari"; $bulanr = "I";} 
		else if($bulan=='02'){ $bulan = "Februari"; $bulanr = "II"; } 
		else if($bulan=='03'){ $bulan = "Maret"; $bulanr = "III"; } 
		else if($bulan=='04'){ $bulan = "April"; $bulanr = "IV"; } 
		else if($bulan=='05'){ $bulan = "Mei"; $bulanr = "V"; } 
		else if($bulan=='06'){ $bulan = "Juni"; $bulanr = "VI"; } 
		else if($bulan=='07'){ $bulan = "Juli"; $bulanr = "VII"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
		else if($bulan=='09'){ $bulan = "September"; $bulanr = "IX"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; $bulanr = "X"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
		else if($bulan=='12'){ $bulan = "Desember"; $bulanr = "XII"; }
		
		$data_pks = $this->model_rpt_ba->ba_pks_afd($periode, $company);
		$total = 0;
		$total_hk = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_2 { font-size: 12px;color:#678197;}
			.content { font-size: 12px;color:#678197; }
			</style>";
		$tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>BERITA ACARA GAJI KENDARAAN, WORKSHOP, DAN MESIN</strong></td>
</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / PKS / ".$company." / ".$bulanr." / ".$tahun." </strong></td>
</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." &nbsp;" .$tahun. "</strong></td>
</tr><tr><td colspan='3'>&nbsp;</td>
</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
	$tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th' rowspan='2' colspan='2'>ACTIVITY</th>
		<th class='tbl_th' rowspan='2'>KODE ASSET</th>
		<th class='tbl_th' rowspan='2'>NAMA ASSET</th>
   <th class='tbl_th'  colspan='2'>REALISASI BIAYA ( Rp )</th>
  </tr>";
 $tabel .= "<tr><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
		foreach ( $data_pks as $row){
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total_hk = $total_hk + $row['HK'];
			$total = $total + $realisasi;
			$tabel .= "<tr><td class='tbl_td' align = 'center'> ".$row['ACCOUNTCODE']."</td>
    <td class='tbl_td'>&nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>
	 <td class='tbl_td'>&nbsp;&nbsp;".strtoupper($row['LOCATION_CODE'])."</td>
	  <td class='tbl_td'>&nbsp;&nbsp;".strtoupper($row['DESCRIPTION'])."</td>";
	 $tabel .= "<td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($realisasi,2,',','.')."&nbsp;&nbsp;</td></tr>";
		
	
	}
   
	$tabel .= "<tr>
    <td class='tbl_td' align = 'center'>&nbsp;</td>
    <td class='tbl_td'><strong>&nbsp;&nbsp;TOTAL BIAYA GAJI KENDARAAN, MESIN, WORKSHOP</strong></td>
    <td class='tbl_td' align = 'right'><strong>&nbsp;</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>&nbsp;</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
  </tr>";
		$tabel .= "</table>"; 
		
		echo $tabel;
	}
	
	function ba_xlspks_rekap($periode){
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);
		if($bulan=='01'){ $bulan = "Januari"; $bulanr = "I";} 
		else if($bulan=='02'){ $bulan = "Februari"; $bulanr = "II"; } 
		else if($bulan=='03'){ $bulan = "Maret"; $bulanr = "III"; } 
		else if($bulan=='04'){ $bulan = "April"; $bulanr = "IV"; } 
		else if($bulan=='05'){ $bulan = "Mei"; $bulanr = "V"; } 
		else if($bulan=='06'){ $bulan = "Juni"; $bulanr = "VI"; } 
		else if($bulan=='07'){ $bulan = "Juli"; $bulanr = "VII"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
		else if($bulan=='09'){ $bulan = "September"; $bulanr = "IX"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; $bulanr = "X"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
		else if($bulan=='12'){ $bulan = "Desember"; $bulanr = "XII"; }
		
		$company = $this->session->userdata('DCOMPANY');
		$data_pks = $this->model_rpt_ba->ba_pks_afd($periode, $company);
				
		$total = 0;
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "BERITA ACARA GAJI KENDARAAN, WORKSHOP, DAN MESIN \n";
		$judul .= "NO :     / PKS / ".$company." / ".$bulanr." / ".$tahun."\n";
		$judul .= "PERIODE : ".strtoupper($bulan)." " .$tahun."\n";
		$judul .= " \n";
		
		$headers .= "KODE AKTIVITAS \t";
		$headers .= "NAMA AKTIVITAS \t";	
		$headers .= "KODE ASSET \t";
		$headers .= "NAMA ASSET \t";
		$headers .= "REALISASI BLN INI \t";
		$headers .= "REALISASI S.D BLN INI \t";
					
		foreach ( $data_pks as $row){
			
			$realisasi = $row['HKE_BYR'] + $row['PREMI'] + $row['LEMBUR'] + $row['PENALTI'];
			$total = $total + $realisasi;
				
				$line = '';
						
				$line .= str_replace('"', '""',$row['ACCOUNTCODE'])."\t";
				$line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
				$line .= str_replace('"', '""',$row['LOCATION_CODE'])."\t";
				$line .= str_replace('"', '""',strtoupper($row['DESCRIPTION']))."\t";
				$line .= str_replace('"', '""',$realisasi)."\t";
				$line .= str_replace('"', '""',$realisasi)."\t";
								
				$data .= trim($line)."\n";		
		}
			
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= " TOTAL BIAYA \t";
		$footer .= " - \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""',$total)."\t";
		$footer .= str_replace('"', '""',$total)."\t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_PABRIK_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
}

?>