<?
class rpt_tanam_sisip extends Controller {
    function rpt_tanam_sisip (){
        parent::Controller();    
        $this->load->model( 'model_rpt_tanam_sisip' ); 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_tanam_sisip";
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->database();
        $this->load->plugin('to_excel');
        $this->load->library('cezpdf');
        $this->load->helper('file');
        require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
    }
	
	function js_tnm_sisip(){
	
	}
	
	function index(){
        $view = "rpt_tanam_sisip";
        $data = array();
        $data['judul_header'] = "laporan Tanam & Sisip";
        $data['js'] = $this->js_tnm_sisip();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['afdeling'] = $this->dropdownlist_afd();
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS' ){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }  
	
	function tnm_sisip_preview($periode){
		$type = $this->uri->segment(3);
		$ishist = 0;
		$company = $this->session->userdata('DCOMPANY');
		$data = array();
		
		if($type == "bulanan"){
			$periode = $this->uri->segment(4);
			$afd = $this->uri->segment(5);
			$bulan = substr($periode,-2);
			$tahun = substr($periode,0,4);
		} else {
			$periode = $this->uri->segment(4)."|".$this->uri->segment(5);
			$afd = $this->uri->segment(6);
			$bulan = substr($this->uri->segment(4),5,2);
			$tahun = substr($this->uri->segment(4),0,4);
		}
				
		$bulanr = $this->bln_to_rperiode($bulan);
		$bulanh = $this->bln_to_periode($bulan);
		$cek = $this->model_rpt_tanam_sisip->cekDataHistory($periode, $type, $company);
		if($cek > 0) {
			$ishist = 1;
		} else {
			$ishist = 0;
		}
		if($type == "bulanan"){
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip($periode, $type, $ishist, $afd, $company);
		} else {
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip_harian($periode, $type, $ishist, $afd, $company);
		}
		$totaltnmbi = 0;
		$totaltnmsbi = 0;
		$totalsspbi = 0;
		$totalsspsbi = 0;
		$totalmatibi = 0;
		$totalmatisbi = 0;
		
		$tabel = $this->getStyle();
		$tabel .= "<table class='tbl_2' border='0' width='80%'><tr><td colspan='3' align='center'>";
		$tabel .= "<strong>LAPORAN TANAM DAN SISIP KELAPA SAWIT</strong></td></tr><tr>";
		$tabel .= "<td colspan='3' align='center'><strong>NO :  / TNM / ".$company." / ".$bulanr." / ".$tahun;
 		$tabel .= "</strong></td></tr><tr><td colspan='3' align='center'>";
		$tabel .= "<strong>PERIODE : ".strtoupper($bulanh)." &nbsp;" .$tahun. "</strong></td>";
		$tabel .= "</tr><tr><td colspan='3'>&nbsp;</td></tr><tr><td colspan='3'>";
		$tabel .= "PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
		$tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$tabel .= "<tr><th width='3%' class='tbl_th' rowspan='2'>No.</th>";
		$tabel .= "<th width='15%' width='' class='tbl_th' rowspan='2'>LOKASI</th>";
		$tabel .= "<th width='4%' class='tbl_th' rowspan='2'>AFD</th>";
		$tabel .= "<th width='8%' class='tbl_th' rowspan='2'>BLOK</th>";
		$tabel .= "<th width='10%' class='tbl_th' width='10%' rowspan='2'>TAHUN TANAM</th>";
   		$tabel .= "<th width='20%' class='tbl_th' colspan='2'>TANAM ( Pokok )</th>";
		$tabel .= "<th width='20%' class='tbl_th' colspan='2'>SISIP ( Pokok )</th>";
		$tabel .= "<th width='20%' class='tbl_th' colspan='2'>MATI ( Pokok )</th></tr>";
 		$tabel .= "<tr><th class='tbl_th' width='10%'>BLN INI</th><th width='10%' class='tbl_th'>s.d BLN INI</th>";
		$tabel .= "<th width='10%' class='tbl_th'>BLN INI</th><th width='10%' class='tbl_th'>s.d BLN INI</th>";
		$tabel .= "<th width='10%' class='tbl_th'>BLN INI</th><th width='10%' class='tbl_th'>s.d BLN INI</th></tr>";
		
		$no = 1;
		foreach ( $data_rpt as $row){
			$tabel .= "<tr><td width='3%' class='tbl_td' align = 'center'> ".$no."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['FIELDCODE']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['ESTATECODE']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['BLOCKID']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['YEARREPLANT']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_TNM_BI']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_TNM_SBI']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_SISIP_BI']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_SISIP_SBI']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_PMATI_BI']."</td>";
			$tabel .= "<td class='tbl_td' align = 'center'> ".$row['QTY_PMATI_SBI']."</td></tr>";
			
			$totaltnmbi = $totaltnmbi + $row['QTY_TNM_BI'];
			$totaltnmsbi = $totaltnmsbi + $row['QTY_TNM_SBI'];
			$totalsspbi = $totalsspbi + $row['QTY_SISIP_BI'];
			$totalsspsbi = $totalsspsbi + $row['QTY_SISIP_SBI'];
			$totalmatibi = $totalmatibi + $row['QTY_PMATI_BI'];
			$totalmatisbi = $totalmatisbi + $row['QTY_PMATI_SBI'];
			$no++;
		}
		$tabel .= "<tr><td class='tbl_td' align='center' colspan='5'><strong>TOTAL</strong></td>";
    	$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totaltnmbi,2)."</strong>&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totaltnmsbi,2)."</strong>&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totalsspbi,2)."</strong>&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totalsspsbi,2)."</strong>&nbsp;</td>";
		$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totalmatibi,2)."</strong>&nbsp;</td>";
    	$tabel .= "<td class='tbl_td' align = 'center'><strong>".number_format($totalmatisbi,2)."</strong>&nbsp;</td>";
  		$tabel .= "</tr></table>"; 
		echo $tabel;
	}
	
	function tnm_sisip_xls($periode){
		$type = $this->uri->segment(3);
		$ishist = 0;
		$company = $this->session->userdata('DCOMPANY');
		$data = array();
		$afd = $this->uri->segment(5);
		if($type == "bulanan"){
			$periode = $this->uri->segment(4);
			$bulan = substr($periode,-2);
			$tahun = substr($periode,0,4);
		} else {
			$periode = $this->uri->segment(4)."|".$this->uri->segment(5);
			$bulan = substr($this->uri->segment(4),5,2);
			$tahun = substr($this->uri->segment(4),0,4);
		}
				
		$bulanr = $this->bln_to_rperiode($bulan);
		$bulanh = $this->bln_to_periode($bulan);
		$cek = $this->model_rpt_tanam_sisip->cekDataHistory($periode, $type, $company);
		if($cek > 0) {
			$ishist = 1;
		} else {
			$ishist = 0;
		}
		if($type == "bulanan"){
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip($periode, $type, $ishist, $afd, $company);
		} else {
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip_harian($periode, $type, $ishist, $afd, $company);
		}
		$totaltnmbi = 0;
		$totaltnmsbi = 0;
		$totalsspbi = 0;
		$totalsspsbi = 0;
		$totalmatibi = 0;
		$totalmatisbi = 0;
		
		$judul = '';
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		$footer = '';
		
		$obj =& get_instance();
		
		$judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
		$judul .= "LAPORAN TANAM DAN SISIP KELAPA SAWIT \n";
		$judul .= "  / TNM / ".$company." / ".$bulanr." / ".$tahun."\n";
		$judul .= "PERIODE : ".strtoupper($bulanh)." &nbsp;" .$tahun."\n";
		$judul .= " \n";
		
		$headers .= "NO \t";
		$headers .= "LOKASI \t";
		$headers .= "AFD \t";
		$headers .= "BLOK \t";	
		$headers .= "TAHUN TANAM \t";
		$headers .= "TANAM BLN INI (POKOK) \t";
		$headers .= "TANAM S.D BLN INI (POKOK) \t";
		$headers .= "SISIP BLN INI (POKOK) \t";
		$headers .= "SISIP S.D BLN INI (POKOK) \t";
		$headers .= "POKOK MATI BLN INI (POKOK) \t";
		$headers .= "POKOK MATI S.D BLN INI (POKOK) \t";
				
		$no = 1;
		foreach ( $data_rpt as $row){
			$line = '';
			$line .= str_replace('"', '""',$no)."\t";
			$line .= str_replace('"', '""',$row['FIELDCODE'])."\t";
			$line .= str_replace('"', '""',$row['ESTATECODE'])."\t";
			$line .= str_replace('"', '""',$row['BLOCKID'])."\t";
			$line .= str_replace('"', '""',$row['YEARREPLANT'])."\t";
			$line .= str_replace('"', '""',$row['QTY_TNM_BI'])."\t";
			$line .= str_replace('"', '""',$row['QTY_TNM_SBI'])."\t";
			$line .= str_replace('"', '""',$row['QTY_SISIP_BI'])."\t";
			$line .= str_replace('"', '""',$row['QTY_SISIP_SBI'])."\t";
			$line .= str_replace('"', '""',$row['QTY_PMATI_BI'])."\t";
			$line .= str_replace('"', '""',$row['QTY_PMATI_SBI'])."\t";
			$totaltnmbi = $totaltnmbi + $row['QTY_TNM_BI'];
			$totaltnmsbi = $totaltnmsbi + $row['QTY_TNM_SBI'];
			$totalsspbi = $totalsspbi + $row['QTY_SISIP_BI'];
			$totalsspsbi = $totalsspsbi + $row['QTY_SISIP_SBI'];
			$totalmatibi = $totalmatibi + $row['QTY_PMATI_BI'];
			$totalmatisbi = $totalmatisbi + $row['QTY_PMATI_SBI'];
			$no++;
			$data .= trim($line)."\n";
		}
		$footer .= " \t";
		$footer .= " \t";
		$footer .= " TOTAL \t";
		$footer .= " \t";
		$footer .= " \t";
		$footer .= str_replace('"', '""',number_format($totaltnmbi,2) )."\t";
		$footer .= str_replace('"', '""',number_format($totaltnmsbi,2) )."\t";
		$footer .= str_replace('"', '""',number_format($totalsspbi,2) )."\t";
		$footer .= str_replace('"', '""',number_format($totalsspsbi,2) )."\t";
		$footer .= str_replace('"', '""',number_format($totalmatibi,2) )."\t";
		$footer .= str_replace('"', '""',number_format($totalmatisbi,2) )."\t";
				
		$data .= trim($footer)."\n";
		$data = str_replace("\r","",$data);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=RPT_TNM".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	function tnm_sisip_pdf(){
		$type = $this->uri->segment(3);
		$ishist = 0;
		$company = $this->session->userdata('DCOMPANY');
		$data = array();
		
		if($type == "bulanan"){
			$periode = $this->uri->segment(4);
			$afd = $this->uri->segment(5);
			$bulan = substr($periode,-2);
			$tahun = substr($periode,0,4);
		} else {
			$periode = $this->uri->segment(4)."|".$this->uri->segment(5);
			$afd = $this->uri->segment(6);
			$bulan = substr($this->uri->segment(4),5,2);
			$tahun = substr($this->uri->segment(4),0,4);
		}
				
		$bulanr = $this->bln_to_rperiode($bulan);
		$bulanh = $this->bln_to_periode($bulan);
		$cek = $this->model_rpt_tanam_sisip->cekDataHistory($periode, $type, $company);
		if($cek > 0) {
			$ishist = 1;
		} else {
			$ishist = 0;
		}
		
		if($type == "bulanan"){
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip($periode, $type, $ishist, $afd, $company);
		} else {
			$data_rpt = $this->model_rpt_tanam_sisip->tanam_sisip_harian($periode, $type, $ishist, $afd, $company);
		}
		
		$totaltnmbi = 0;
		$totaltnmsbi = 0;
		$totalsspbi = 0;
		$totalsspsbi = 0;
		$totalmatibi = 0;
		$totalmatisbi = 0;
			
		$pdf = new pdf_usage();
		$pdf->Open();
		$pdf->SetAutoPageBreak(TRUE,10);
		$pdf->SetMargins(5,10);
		$pdf->AddPage("L","A4");
		$pdf->AliasNbPages(); 
		$company_name = $this->session->userdata('DCOMPANY_NAME');
		require_once(APPPATH . 'libraries/ba/header_rpt_tanam_sisip.inc');
		require_once(APPPATH . 'libraries/ba/table_border_tnm_ssp.inc');
		
		$columns = 11; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['T_SIZE'] = 10;
			$aSimpleHeader[$i]['LN_SIZE'] = 5;
			if($i == 0) {
				$aSimpleHeader[$i]['TEXT'] = "NO"; $aSimpleHeader[$i]['WIDTH'] = 13;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
			}
			if($i == 1) {
				$aSimpleHeader[$i]['TEXT'] = "LOKASI"; $aSimpleHeader[$i]['WIDTH'] = 40;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
			}
			if($i == 2) {
				$aSimpleHeader[$i]['TEXT'] = "AFD"; $aSimpleHeader[$i]['WIDTH'] = 22;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
			}
			if($i == 3) {
				$aSimpleHeader[$i]['TEXT'] = "BLOK"; $aSimpleHeader[$i]['WIDTH'] = 30;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
			}
			if($i == 4) {
				$aSimpleHeader[$i]['TEXT'] = "TAHUN TANAM"; $aSimpleHeader[$i]['WIDTH'] = 25;
				$aSimpleHeader[$i]['ROWSPAN'] = 2;
			}
			if($i == 5) {
				$aSimpleHeader[$i]['TEXT'] = "TANAM (POKOK)"; $aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
			}
			if($i == 6) {
				$aSimpleHeader[$i]['TEXT'] = ""; $aSimpleHeader[$i]['WIDTH'] = 35;
			}
			if($i == 7) {
				$aSimpleHeader[$i]['TEXT'] = "SISIP (POKOK)"; $aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
			}
			if($i == 8) {
				$aSimpleHeader[$i]['TEXT'] = ""; $aSimpleHeader[$i]['WIDTH'] = 35;
			}
			if($i == 9) {
				$aSimpleHeader[$i]['TEXT'] = "MATI (POKOK)"; $aSimpleHeader[$i]['WIDTH'] = 35;
				$aSimpleHeader[$i]['COLSPAN'] = 2;
			}
			if($i == 10) {
				$aSimpleHeader[$i]['TEXT'] = ""; $aSimpleHeader[$i]['WIDTH'] = 35;
			}
		}
		
		$aSimpleHeader2 = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader2[$i] = $table_default_header_type;
			$aSimpleHeader2[$i]['T_SIZE'] = 10;
			$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			if($i == 0) {
				$aSimpleHeader2[$i]['TEXT'] = ""; $aSimpleHeader2[$i]['COLSPAN'] = 2;				
			}
			if($i == 1) {
				$aSimpleHeader2[$i]['TEXT'] = "";
			}
			if($i == 2) {
				$aSimpleHeader2[$i]['TEXT'] = ""; $aSimpleHeader2[$i]['WIDTH'] = 10;			
			}
			if($i == 3) {
				$aSimpleHeader2[$i]['TEXT'] = ""; $aSimpleHeader2[$i]['WIDTH'] = 10;			
			}
			if($i == 4) {
				$aSimpleHeader2[$i]['TEXT'] = ""; $aSimpleHeader2[$i]['WIDTH'] = 10;			
			}
			if($i == 5) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 6) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 7) {
				$aSimpleHeader2[$i]['TEXT'] = "BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 8) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 9) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
			if($i == 10) {
				$aSimpleHeader2[$i]['TEXT'] = "SD. BLN INI"; $aSimpleHeader2[$i]['WIDTH'] = 35;
			}
		}
		
		$aHeader = array( $aSimpleHeader, $aSimpleHeader2);
		$pdf->tbSetHeaderType($aHeader, TRUE);
		
		$pdf->tbDrawHeader();
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		$pdf->tbSetDataType($aDataType);
		
		
		$no = 1;
		foreach ($data_rpt as $row){
			for($i=0; $i<$columns; $i++){
				$data[$i]['T_TYPE'] = "B";
				$data[$i]['T_SIZE'] = 11;
				$data[$i]['LN_SIZE'] = 5;
			}
			$data = Array();
			$data[0]['TEXT'] = $no;
			$data[1]['TEXT'] = $row['FIELDCODE'];
			$data[1]['T_ALIGN'] = "L";
			$data[2]['TEXT'] = $row['ESTATECODE'];
			$data[3]['TEXT'] = $row['BLOCKID'];
			$data[4]['TEXT'] = $row['YEARREPLANT'];
			$data[5]['TEXT'] = number_format($row['QTY_TNM_BI'],2,'.',',');
			$data[6]['TEXT'] = number_format($row['QTY_TNM_SBI'],2,'.',',');
			$data[7]['TEXT'] = number_format($row['QTY_SISIP_BI'],2,'.',',');
			$data[8]['TEXT'] = number_format($row['QTY_SISIP_SBI'],2,'.',',');
			$data[9]['TEXT'] = number_format($row['QTY_PMATI_BI'],2,'.',',');
			$data[10]['TEXT'] = number_format($row['QTY_PMATI_SBI'],2,'.',',');
			
			for($i=5; $i<$columns; $i++){
				$data[$i]['T_ALIGN'] = "R";
			}
			$totaltnmbi = $totaltnmbi + $row['QTY_TNM_BI'];
			$totaltnmsbi = $totaltnmsbi + $row['QTY_TNM_SBI'];
			$totalsspbi = $totalsspbi + $row['QTY_SISIP_BI'];
			$totalsspsbi = $totalsspsbi + $row['QTY_SISIP_SBI'];
			$totalmatibi = $totalmatibi + $row['QTY_PMATI_BI'];
			$totalmatisbi = $totalmatisbi + $row['QTY_PMATI_SBI'];
			$index++; $no++;
			$pdf->tbDrawData($data);
		}
		$data_test=array();
		$data_test[0]['TEXT'] = " TOTAL POKOK";
		$data_test[0]['COLSPAN'] = 5;
		$data_test[5]['TEXT'] = number_format($totaltnmbi,2,'.',',');
		$data_test[6]['TEXT'] = number_format($totaltnmsbi,2,'.',',');
		$data_test[7]['TEXT'] = number_format($totalsspbi,2,'.',',');
		$data_test[8]['TEXT'] = number_format($totalsspsbi,2,'.',',');
		$data_test[9]['TEXT'] = number_format($totalmatibi,2,'.',',');
		$data_test[10]['TEXT'] = number_format($totalmatisbi,2,'.',',');
		for($i=5; $i<$columns; $i++){
			$data_test[$i]['T_ALIGN'] = "R";
		}
		for($i=0; $i<$columns; $i++){
			$data_test[$i]['T_TYPE'] = "B";
			$data_test[$i]['T_SIZE'] = 10;
			$data_test[$i]['LN_SIZE'] = 5;
		}
		
		$pdf->tbDrawData($data_test);
		
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->Ln(7.5);
		require_once(APPPATH . 'libraries/daftar_upah/authorize_rpt_tanam_sisip.inc');
		$pdf->Output();	
	}
	
	function bln_to_periode($bulan){
		if($bulan=='01'){ $bulan = "Januari"; } 
		else if($bulan=='02'){ $bulan = "Februari"; } 
		else if($bulan=='03'){ $bulan = "Maret"; } 
		else if($bulan=='04'){ $bulan = "April"; } 
		else if($bulan=='05'){ $bulan = "Mei"; } 
		else if($bulan=='06'){ $bulan = "Juni"; } 
		else if($bulan=='07'){ $bulan = "Juli"; } 
		else if($bulan=='08'){ $bulan = "Agustus"; } 
		else if($bulan=='09'){ $bulan = "September"; } 
		else if($bulan=='10'){ $bulan = "Oktober"; } 
		else if($bulan=='11'){ $bulan = "Nopember"; } 
		else if($bulan=='12'){ $bulan = "Desember";}
		return $bulan; 
	}
	
	function bln_to_rperiode($bulan){
		if($bulan=='01'){ $bulanr = "I";} 
		else if($bulan=='02'){ $bulanr = "II"; } 
		else if($bulan=='03'){ $bulanr = "III"; } 
		else if($bulan=='04'){ $bulanr = "IV"; } 
		else if($bulan=='05'){ $bulanr = "V"; } 
		else if($bulan=='06'){ $bulanr = "VI"; } 
		else if($bulan=='07'){ $bulanr = "VII"; } 
		else if($bulan=='08'){ $bulanr = "VIII"; } 
		else if($bulan=='09'){ $bulanr = "IX"; } 
		else if($bulan=='10'){ $bulanr = "X"; } 
		else if($bulan=='11'){ $bulanr = "XI"; } 
		else if($bulan=='12'){ $bulanr = "XII"; }
		return $bulanr;
	}
	
	function getStyle(){
		$tabel = "<style> 
			#header1{ position: fixed; top: 100px; background: #fff; }
			#header{ position: fixed; top: 0px; background: #fff; }
			#data{ margin-top: 134px; }
			.tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
			.tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
			.tbl_2 { font-size: 12px;color:#678197;} .content { font-size: 12px;color:#678197; } </style>";
		return $tabel;
	}
	
	function dropdownlist_afd(){
		$string = "<select  name='s_afd' class='select' id='s_afd' >";
		$string .= "<option value=''> -- pilih -- </option>";
		$data_afd = $this->model_rpt_tanam_sisip->get_afdeling($this->session->userdata('DCOMPANY'));
		
		foreach ( $data_afd as $row){
			if( (isset($default)) && ($default==$row[$nama_isi]) ){
				$string = $string." <option value=\"".$row['AFD_CODE']."\"  selected>".$row['AFD_DESC']." </option>";
			} else {
				$string = $string." <option value=\"".$row['AFD_CODE']."\">".$row['AFD_DESC']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
}

?>