<?
class rpt_tandaterima extends Controller 
{
	function rpt_tandaterima ()
	{
		parent::Controller();	

		$this->load->model( 'model_rpt_du' ); 
		
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
		$data['GANG_CODE'] = $this->global_func->dropdownlist2("GANG_CODE","m_gang","GANG_CODE","GANG_CODE","COMPANY_CODE = '".$this->session->userdata('DCOMPANY')."'",NULL, NULL,'CheckAfdeling()',"select");
		
		if ($data['login_id'] == TRUE){
			if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
				$this->load->view('rpt_tandaterima', $data);
			} 
		} else {
			redirect('login');
		}
		
    }  
		
	function gen_tt(){
			
	if ($this->session->userdata('logged_in') != TRUE)
	{
	   redirect('login');
	}
	$type = $this->uri->segment(5);
	$company = $this->session->userdata('DCOMPANY');
	$company_name = $this->session->userdata('DCOMPANY_NAME');
	
	$pdf = new pdf_usage('P','mm','A4C');		
	$pdf->Open();
	$pdf->SetAutoPageBreak(true, 10);
    $pdf->SetMargins(5, 13,20);
	$pdf->AddPage();
	$pdf->AliasNbPages(); 
		
	$pdf->SetStyle("s1","arial","",11,"118,0,3");
	$pdf->SetStyle("s2","arial","",10,"0,49,159");
	$pdf->SetStyle("s3","arial","",10,"118,0,3");
	
	if($type == "afd"){
		$div = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(6);
		$bulan = substr($from,4,2);
		$tahun =  substr($to,0,4);
		$data_row = $this->model_rpt_du->get_du_perafd($company,$from,$to,$div);
		
		if($bulan==01){ $bulan = "Januari ".$tahun; } 
		else if($bulan==02){ $bulan = "Februari ".$tahun; } 
		else if($bulan==03){ $bulan = "Maret ".$tahun; } 
		else if($bulan==04){ $bulan = "April ".$tahun; } 
		else if($bulan==05){ $bulan = "Mei ".$tahun; } 
		else if($bulan==06){ $bulan = "Juni ".$tahun; } 
		else if($bulan==07){ $bulan = "Juli ".$tahun; } 
		else if($bulan==08){ $bulan = "Agustus ".$tahun; } 
		else if($bulan==09){ $bulan = "September ".$tahun; } 
		else if($bulan==10){ $bulan = "Oktober ".$tahun; } 
		else if($bulan==11){ $bulan = "Nopember ".$tahun; } 
		else if($bulan==12){ $bulan = "Desember ".$tahun; }	
				
		$gangcode1 = "DIVISI     :  ".strtoupper($div);
		$gangcode2 = "PERIODE    :  ".strtoupper($bulan);
	
		
		$pdf->MultiCellTag(200, 2, "<s1>FORM TANDA TERIMA GAJI </s1>", 0);
		$pdf->Ln(2);
		$pdf->MultiCellTag(200, 2, "<s3>".$gangcode1." </s3>", 0);
		$pdf->Ln(1);
		$pdf->MultiCellTag(200, 2, "<s3>".$gangcode2." </s3>", 0);
		$pdf->Ln(1);
		
	} else {
		$gc = $this->uri->segment(3);
		$from = $this->uri->segment(4);
		$to = $this->uri->segment(6);
		$bulan = substr($to,4,2);
		$tahun =  substr($to,0,4);
		if( $type == "bln" ) {
			$data_row = $this->model_rpt_du->generate_du_bulanan($gc, $from, $to, $company);
		} else {
			$data_row = $this->model_rpt_du->generate_du2($gc, $from, $to, $company);
		}
		$data_gc = $this->model_rpt_du->header_du($gc,$company);
		
		if($bulan==01){ $bulan = "Januari ".$tahun; } 
		else if($bulan==02){ $bulan = "Februari ".$tahun; } 
		else if($bulan==03){ $bulan = "Maret ".$tahun; } 
		else if($bulan==04){ $bulan = "April ".$tahun; } 
		else if($bulan==05){ $bulan = "Mei ".$tahun; } 
		else if($bulan==06){ $bulan = "Juni ".$tahun; } 
		else if($bulan==07){ $bulan = "Juli ".$tahun; } 
		else if($bulan==08){ $bulan = "Agustus ".$tahun; } 
		else if($bulan==09){ $bulan = "September ".$tahun; } 
		else if($bulan==10){ $bulan = "Oktober ".$tahun; } 
		else if($bulan==11){ $bulan = "Nopember ".$tahun; } 
		else if($bulan==12){ $bulan = "Desember ".$tahun; }	
		
		$gc1 = "";
		$gc2 = "";
		$gc3 = "";
			
		foreach ($data_gc as $row)
		{
			$gc1 .= $row['DESCRIPTION'];
			$gc2 .= $row['MANDORE_CODE'];
			$gc3 .= $row['NAMA'];
		}
		
		require_once(APPPATH . 'libraries/table_border.inc');
		$columns = 3; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[0]['WIDTH'] = 30;
			$aSimpleHeader[1]['WIDTH'] = 4;
			$aSimpleHeader[2]['WIDTH'] = 60;
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);	
							
		for ($j=0; $j<=5; $j++)
		{
				$data = Array();
				$data[0]['BRD_TYPE'] = 0; $data[1]['BRD_TYPE'] = 0; $data[2]['BRD_TYPE'] = 0;
				$data[3]['BRD_TYPE'] = 0; $data[4]['BRD_TYPE'] = 0; $data[5]['BRD_TYPE'] = 0;
				$data[0]['LN_SIZE'] = 4; $data[1]['LN_SIZE'] = 2; $data[2]['LN_SIZE'] = 2;
				$data[3]['LN_SIZE'] = 2; $data[4]['LN_SIZE'] = 2; $data[5]['LN_SIZE'] = 2;
				$data[0]['T_ALIGN'] = 'L'; $data[1]['T_ALIGN'] = 'L'; $data[2]['T_ALIGN'] = 'L';
				$data[3]['T_ALIGN'] = 'L'; $data[4]['T_ALIGN'] = 'L'; $data[5]['T_ALIGN'] = 'L';
				if($j == 0) {
					$data[0]['T_SIZE'] = 9;
					$data[0]['TEXT'] = "FORM TANDA TERIMA GAJI";
					$data[0]['COLSPAN'] = 3;
					$data[0]['T_ALIGN'] = 'L';
					$data[0]['BRD_TYPE'] = 0;				
				}
				if($j == 1) {
					$data[0]['T_SIZE'] = 8;
					$data[0]['TEXT'] = "Kode Kemandoran";
					$data[1]['T_SIZE'] = 8;
					$data[1]['TEXT'] = ":";
					$data[2]['T_SIZE'] = 8;
					$data[2]['TEXT'] = $gc;
					$data[2]['T_ALIGN'] = 'L';
				}
				if($j == 2) {
					$data[0]['T_SIZE'] = 8;
					$data[0]['TEXT'] = "Nama Kemandoran";
					$data[1]['T_SIZE'] = 8;
					$data[1]['TEXT'] = ":";
					$data[2]['T_SIZE'] = 8;
					$data[2]['TEXT'] = $gc1;
					$data[2]['T_ALIGN'] = 'L';
				}
				if($j == 3) {
					$data[0]['T_SIZE'] = 8;
					$data[0]['TEXT'] = "NIK Mandor";
					$data[1]['T_SIZE'] = 8;
					$data[1]['TEXT'] = ":";
					$data[2]['T_SIZE'] = 8;
					$data[2]['TEXT'] = $gc2;
					$data[2]['T_ALIGN'] = 'L';
				}
				if($j == 4) {
					$data[0]['T_SIZE'] = 8;
					$data[0]['TEXT'] = "Nama Mandor";
					$data[1]['T_SIZE'] = 8;
					$data[1]['TEXT'] = ":";
					$data[2]['T_SIZE'] = 8;
					$data[2]['TEXT'] = $gc3;
					$data[2]['T_ALIGN'] = 'L';
				}
				if($j == 5) {
					$data[0]['T_SIZE'] = 8;
					$data[0]['TEXT'] = "Periode";
					$data[1]['T_SIZE'] = 8;
					$data[1]['TEXT'] = ":";
					$data[2]['T_SIZE'] = 8;
					$data[2]['TEXT'] = strtoupper($bulan);
					$data[2]['T_ALIGN'] = 'L';
				}
			
			$pdf->tbDrawData($data);
			
		}

		$pdf->tbOuputData();	
		
	}
	
	$pdf->Ln(3);	
	//load the table default definitions DEFAULT!!!
	require_once(APPPATH . 'libraries/table_border.inc');
	$columns = 5; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[0]['WIDTH'] = 15;
			$aSimpleHeader[0]['TEXT'] = 'NO';
			$aSimpleHeader[0]['T_SIZE'] = 8;
			$aSimpleHeader[1]['WIDTH'] = 30;
			$aSimpleHeader[1]['TEXT'] = 'NIK';
			$aSimpleHeader[1]['T_SIZE'] = 8;
			$aSimpleHeader[2]['WIDTH'] = 50;
			$aSimpleHeader[2]['TEXT'] = 'NAMA';
			$aSimpleHeader[2]['T_SIZE'] = 8;
			$aSimpleHeader[3]['WIDTH'] = 30;
			$aSimpleHeader[3]['TEXT'] = 'TANDA TANGAN';
			$aSimpleHeader[3]['T_SIZE'] = 8;
			$aSimpleHeader[3]['COLSPAN'] = 2;
			$aSimpleHeader[4]['TEXT'] = '';
			$aSimpleHeader[4]['WIDTH'] = 30;
				
		}
		$pdf->tbSetHeaderType($aSimpleHeader);
		
	$pdf->tbDrawHeader();
	
	//Table Data Settings
	$aDataType = Array();
	
	for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	$pdf->tbSetDataType($aDataType);
			
	$j = 0;
	foreach ($data_row as $row)
	{
		$j = $j + 1;
		$data = Array();
			$data[0]['T_SIZE'] = 8;
			$data[0]['TEXT'] = $j;
			$data[1]['T_SIZE'] = 8;
			$data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
			$data[2]['T_SIZE'] = 8;
			$data[2]['TEXT'] = $row['NAMA'];
			$data[2]['T_ALIGN'] = 'L'; 
						
			if ( ($j % 2) != 0) {
				$data[3]['T_SIZE'] = 8;
				$data[3]['TEXT'] = $j.".  ..................";
			} else {
				$data[4]['T_SIZE'] = 8;
				$data[4]['TEXT'] = $j.".  ..................";
			}
		

		$pdf->tbDrawData($data);
		
	}

	$pdf->tbOuputData();
	$pdf->tbDrawBorder();
	
	//$pdf->Ln(10.5);
	
	// require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
	
	$pdf->Output();

	}
	
	function generate () {
		$periode = $this->uri->segment(3);
		$gc = $this->uri->segment(4);
		
		$data= array();
		
		if(isset($periode) && isset($gc)){
			$this->gen_tt($periode, $gc);
		}
					
	}
}

?>