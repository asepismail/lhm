<?
class rpt_rekonbadu extends Controller 
{
	function rpt_rekonbadu ()
	{
		parent::Controller();	

		$this->load->model( 'model_rpt_rekon_badu' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_rekonbadu";
		
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
		$view = "rpt_rekonbadu";
		$data = array();
		$data['judul_header'] = "Rekonsiliasi BA & DU";
		$data['js'] = $this->js_badu();	
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				show($view, $data);
			} 
		} else {
			redirect('login');
		}		
    } 
	
	function js_badu(){
		
		$js = "$(function() {
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
					$('#TO').datepicker({dateFormat:'yy-mm-dd'});
				});
			jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
				
				var tfrom = document.getElementById('FROM').value;
				var elem = tfrom.split('-');
				from = elem[0]+elem[1]+elem[2];
							
				var tto = document.getElementById('TO').value;
				var elem2 = tto.split('-');
				to = elem2[0]+elem2[1]+elem2[2];
				
			var nw = $('#newwindow').is(':checked');
			urls = url + 'rpt_rekonbadu/gen_du/'+from+'/'+to;
				if(nw != false) {	
						$('#frame').attr('src','');
							
						$('.button').popupWindow({ 
							windowURL:urls,
							windowName:'Rekap Biaya Gaji Rawat AFD : '+ afd,
							width:800 
						}); 
				} else {
						$('#frame').attr('src',urls);
				}	
		});";
		return $js;
	}
	
	function gen_du($periode){
		
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
		
		$data = array();
		
		$company = $this->session->userdata('DCOMPANY');
		$data_du = $this->model_rpt_rekon_badu->gen_du($from,$to,$company);
		
		$hke = 0;
		$hkne = 0;
		$hkebyr = 0;
		$hknebyr = 0;
		$tunj_ttl = 0;
		$astek = 0;
		$premi = 0;
		$natura = 0;
		$rtb = 0;
		$bruto = 0;
		$pot_astek = 0;
		$pph21 = 0;
		$potlain = 0;
		$ttlpot = 0;
		$netto = 0;
		
		$tabel = "";
		$tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.content { font-size: 12px;color:#678197; }
			</style>";
		$tabel .= "<span class='content'>DAFTAR UPAH</span>";
		$tabel .= "<table width='100%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th'>AFDELING / DIV</th>";
		$tabel .= "<th class='tbl_th'>HKE</th>";
		$tabel .= "<th class='tbl_th'>HKNE</th>";
		$tabel .= "<th class='tbl_th'>HKE BYR</th>";
		$tabel .= "<th class='tbl_th'>HKNE BYR</th>";
		$tabel .= "<th class='tbl_th'>TUNJ. JABATAN <br/>/ TUNJ. LAIN</th>";
		$tabel .= "<th class='tbl_th'>ASTEK</th>";
		$tabel .= "<th class='tbl_th'>PREMI / LEMBUR</th>";
		$tabel .= "<th class='tbl_th'>NATURA</th>";
		$tabel .= "<th class='tbl_th'>RAPEL / THR <br/>/BONUS</th>";
		$tabel .= "<th class='tbl_th'>GAJI BRUTO</th>";
		$tabel .= "<th class='tbl_th'>POT. ASTEK</th>";
		$tabel .= "<th class='tbl_th'>PPH21</th>";
		$tabel .= "<th class='tbl_th'>POT. LAIN</th>";
		$tabel .= "<th class='tbl_th'>TTL. POTONGAN</th>";
		$tabel .= "<th class='tbl_th'>TOTAL</th></tr>";
	
		foreach ( $data_du as $row){
			
		
			$hke = $hke + $row['HKE'];
			$hkne = $hkne + $row['HKNE'];
			$hkebyr = $hkebyr + $row['GP'];
			$hknebyr = $hknebyr + $row['HKNE_BYR'];
			$tunj_ttl = $tunj_ttl + $row['TUNJANGAN_LAIN'] ;
			$astek = $astek + $row['ASTEK'];
			$premi = $premi + $row['PREMI_LEMBUR'];
			$natura = $natura + $row['NATURA'];
			$rtb = $rtb + $row['RTB'];
			$bruto = $bruto + $row['GAJI_BRUTO'];
			$pot_astek = $pot_astek + $row['POT_ASTEK'];
			$pph21 = $pph21 + $row['PPH21'];
			$potlain = $potlain + $row['POT_LAIN'];
			$ttlpot = $ttlpot + $row['TTL_POTONGAN'];
			$netto = $netto + $row['TTL'];

		$tabel .= "<tr>";
   		$tabel .= "<td class='tbl_td' align = 'left'> &nbsp;&nbsp; ".$row['DIVISION_CODE']."</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['HKE'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['HKNE'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['GP'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['HKNE_BYR'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['TUNJANGAN_LAIN'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['ASTEK'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['PREMI_LEMBUR'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['NATURA'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['RTB'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['GAJI_BRUTO'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['POT_ASTEK'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['PPH21'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['POT_LAIN'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['TTL_POTONGAN'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['TTL'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "</tr>";
		}
		
		$tabel .= "<tr>";
   		$tabel .= "<td class='tbl_td' align = 'left'> &nbsp;&nbsp; TOTAL </td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($hke,0,',','.')."&nbsp;&nbsp;</strong> </td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($hkne,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($hkebyr,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($hknebyr,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($tunj_ttl,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($astek,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($premi,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($natura,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($rtb,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($bruto,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($pot_astek,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($pph21,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($potlain,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($ttlpot,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($netto,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "</tr>";
		$tabel .= "</table>"; 
		
		$data_ba = $this->model_rpt_rekon_badu->gen_ba_global($from,$to,$company);
		
		$tabel .= "<p/>";
		$totalbiaya = 0;
		$totalhke = 0;
		$totalpremi = 0;
		
		$tabel .= "<span class='content'>BERITA ACARA</span>";
		$tabel .= "<table width='100%' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th class='tbl_th'>KODE AKTIVITAS</th>";
		$tabel .= "<th class='tbl_th'>NAMA AKTIVITAS</th>";
		$tabel .= "<th class='tbl_th'>HKE BYR</th>";
		$tabel .= "<th class='tbl_th'>PREMI / LEMBUR</th>";
		$tabel .= "<th class='tbl_th'>BIAYA (Rp.)</th></tr>";
	
		foreach ( $data_ba as $row){
			//if ($row['COA_DESCRIPTION'] == "POTONGAN") { 
			//	$totalbiaya = $totalbiaya - $row['BIAYA']; 
			//} else {
				$totalbiaya = $totalbiaya + $row['BIAYA'];
			//}
			$totalhke = $totalhke + $row['HKE'];
			$totalpremi = $totalpremi + $row['PREMI_LEMBUR'];
		
		$tabel .= "<tr>";
   		$tabel .= "<td class='tbl_td' align = 'left'> &nbsp;&nbsp; ".$row['ACTIVITY_CODE']."</td>";
		$tabel .= "<td class='tbl_td' align = 'left'> &nbsp;&nbsp; ".$row['COA_DESCRIPTION']."</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> &nbsp;&nbsp; ".number_format($row['HKE'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> &nbsp;&nbsp; ".number_format($row['PREMI_LEMBUR'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'right'> ".number_format($row['BIAYA'],0,',','.')."&nbsp;&nbsp;</td>";
		$tabel .= "</tr>";
		}
		
		$tabel .= "<tr>";
   		$tabel .= "<td class='tbl_td' align = 'center' colspan='2'> &nbsp;&nbsp; TOTAL </td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($totalhke,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($totalpremi,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "<td class='tbl_td' align = 'right'><strong> ".number_format($totalbiaya,0,',','.')."&nbsp;&nbsp;</strong></td>";
		$tabel .= "</tr>";
		
		
		$tabel .= "</table><p>";		
		$selisih = $totalbiaya - $netto;
		$tabel .= "<div style='float:right'><span class='content'>SELISIH : " . number_format($totalbiaya - $netto,0,',','.') ." </span></div>";
		echo $tabel;
	
	}
	
}

?>