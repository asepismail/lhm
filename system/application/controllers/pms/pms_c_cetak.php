<?php
class pms_c_cetak extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
		$this->load->model('pms/pms_m_daftpengajuan');
		$this->load->model('pms/pms_m_pengajuan');
		$this->load->model('pms/pms_m_monitoring');
		$this->load->model('pms/pms_m_master_budget');
		
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->lastmenu="main_c_pms";
		$this->load->helper('file');
		require_once(APPPATH . 'libraries/fpdf_table.php');
		require_once(APPPATH . 'libraries/header_footer.inc');
    }
	
	function index(){
      $this->data['js'] = "";
      $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
      $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
      $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
      $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
	  $this->data['user_dept'] = htmlentities($this->session->userdata('USER_DEPT'),ENT_QUOTES,'UTF-8');
      $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
	  $this->data['company'] = $this->dropdownlist("i_company","style='width:260px;'","tabindex='1'","comp","COMPANY_CODE","COMPANY_NAME");
	  $this->data['dept'] = $this->dropdownlist("i_dept","style='width:220px;'","tabindex='1'","dept","DEPT_CODE","DEPT_DESCRIPTION");
	  $this->data['afd'] = $this->dropdownlist("i_afd","style='width:140px;'","tabindex='4'","afd","AFD_CODE","AFD_DESC");
	  $this->data['satuan'] = $this->dropdownlist("i_satuan","style='width:120px;'","tabindex='16'","sat","UNIT_CODE","UNIT_DESC");
	  if ($this->data['login_id'] == TRUE){
            show($view, $this->data);
      } else {
            redirect('login');
      }
   }
   
   function cetak_pengajuan (){
	   $noproject = $this->uri->segment(4);
	   $company = $this->uri->segment(6);
	   $cek = $this->pms_m_daftpengajuan->headerformp_project($noproject, $company);
	   $status = "";
	   foreach ($cek as $hrow)
	   {	
	   		if($hrow['ISAPPR_LVL1'] == "0" && $hrow['ISAPPR_LVL2'] == "0"  ){
				$this->cetakformKebun($noproject, $company);
			} else {
				$this->cetakform($noproject, $company);
			}
	   }
   }
   
   function cetakform() {
		$noproject = $this->uri->segment(4);
		$stats = $this->uri->segment(5);
		$company = $this->uri->segment(6);
		$companyname = "";
		
		if($company == "LIH"){
			$companyname = "LANGGAM INTI HIBRINDO";
		} else if($company == "MIA"){
			$companyname = "MINANG AGRO";
		} else if($company == "MSS"){
			$companyname = "MUTIARA SAWIT SELUMA";
		} else if($company == "SSS"){
			$companyname = "SABAN SAWIT SUBUR";
		} else if($company == "SAP"){
			$companyname = "SURYA AGRO PERSADA";
		} else if($company == "TPAI"){
			$companyname = "TRANS PACIFIC AGRO INDUSTRI";
		} else if($company == "SML"){
			$companyname = "SEMAI LESTARI";
		} else if($company == "GKM"){
			$companyname = "GLOBAL KALIMANTAN MAKMUR";
		} else if($company == "ASL"){
			$companyname = "AGRA SENTRA LESTARI";
		}
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->FPDF('P','mm','letter');
		$pdf->SetAutoPageBreak(false, 10);
		$pdf->SetMargins(5, 7);
		$pdf->AddPage('P', 'A4D');
		$pdf->AliasNbPages(); 
		$pdf->SetStyle("s1","arial","",6,"");
		$pdf->SetStyle("s2","arial","",7,"");
		$pdf->SetStyle("s3","arial","",8,""); 
		
		require_once(APPPATH . 'libraries/table_no_border.inc');
		$headerrow = $this->pms_m_daftpengajuan->headerformp_project($noproject, $company);
		foreach ($headerrow as $hrow)
		{	
			/* header */
			$columns = 4; //number of Columns
			$pdf->tbInitialize($columns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			
			//$pdf->Ln(10);
			$aSimpleHeader = array(); 
			for($i=0; $i<=$columns; $i++) {
				$aSimpleHeader[$i] = $table_default_header_type;
				$aSimpleHeader[$i]['WIDTH'] = 44;
			}
			
			$pdf->tbSetHeaderType($aSimpleHeader);
			$aDataType = Array();
			for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($aDataType);
		
			for ($j=0; $j<=9; $j++)
			{
				$data = Array();
					
				if ($j == 0){
					$data[0]['TEXT'] = "PROVIDENT AGRO GROUP";
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 7;
					$data[0]['LN_SIZE'] = 2;
				
				}
				if ($j == 1){
					$data[0]['TEXT'] = "PT. ". $companyname;
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 3;
					$data[0]['T_SIZE'] = 7;	
					
					$data[3]['TEXT'] = "";
					$data[3]['T_ALIGN'] = "C";
					$data[3]['LN_SIZE'] = 3;
					$data[3]['T_SIZE'] = 7;	
				}
				
				if ($j == 3){
					$judul = "";
					if($stats == "baru") {
						$judul = "PERSETUJUAN PENGAJUAN PROJECT";
					} 
					$data[0]['TEXT'] = $judul;
					$data[0]['T_ALIGN'] = "C";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 8;
					$data[0]['T_TYPE'] = "B";
				}
				
				if ($j == 5){
					$data[0]['TEXT'] = "No. Pengajuan :  " . $hrow['PJ_PNUM'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 6){
					$data[0]['TEXT'] = "No. Project  :  " . $hrow['PROJECT_ID'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
		
				if ($j == 7){
					$data[0]['TEXT'] = "Departemen :  " . strtoupper($hrow['DEPT']);
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 8){
					$data[0]['TEXT'] = "Tanggal Pengajuan  :  " . $hrow['PDATE'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 9){
					$data[0]['TEXT'] = "Pelaksana :  " . $hrow['PELAKSANA'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				$pdf->tbDrawData($data);
			}
		}
		$pdf->tbOuputData();
		$pdf->Ln(1.5);
		$total = 0;
		/* middle table */	
		require_once(APPPATH . 'libraries/table_border_pms.inc');
		$middlerow = $this->pms_m_daftpengajuan->formp_project($noproject,$company);
		foreach ($middlerow as $mrow)
		{	
			/* header */
			$mcolumns = 8; //number of Columns
			$pdf->tbInitialize($mcolumns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			$mSimpleHeader = array();
			$mheader = array('No.','Kode Project','Pekerjaan','Lokasi / Afdeling','Qty','Sat','Biaya Satuan (Rp.)','Total Biaya (Rp)',''); 
			for($i=0; $i<=count($mheader)-1; $i++) {
				$mSimpleHeader[$i] = $table_default_header_type;
				$mSimpleHeader[$i]['TEXT'] = $mheader[$i];
				$mSimpleHeader[$i]['LN_SIZE'] = 5;
				$mSimpleHeader[$i]['T_SIZE'] = 7;
				$mSimpleHeader[0]['WIDTH'] = 8;
				$mSimpleHeader[1]['WIDTH'] = 20;
				$mSimpleHeader[2]['WIDTH'] = 52;
				$mSimpleHeader[3]['WIDTH'] = 25;
				$mSimpleHeader[4]['WIDTH'] = 15;
				$mSimpleHeader[5]['WIDTH'] = 16;
				$mSimpleHeader[6]['WIDTH'] = 21;
				$mSimpleHeader[7]['WIDTH'] = 23;
			}
			
			$pdf->tbSetHeaderType($mSimpleHeader);
			$pdf->tbDrawHeader();
			
			$mDataType = Array();
			for ($i=0; $i<$mcolumns; $i++) $mDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($mDataType);
			$desc_row = $this->pms_m_daftpengajuan->formp_project($noproject);
    		$i = 1;    
			foreach ($desc_row as $drow)
			{
				$data_desc = Array();
					
					$data_desc[0]['TEXT'] = $i;
					$data_desc[0]['T_ALIGN'] = "C";
					$data_desc[0]['LN_SIZE'] = 4;
					$data_desc[0]['T_SIZE'] = 7;	
					
					$data_desc[1]['TEXT'] = $drow['PJ_ID'];
					$data_desc[1]['T_ALIGN'] = "C";
					$data_desc[1]['LN_SIZE'] = 4;
					$data_desc[1]['T_SIZE'] = 7;
					
					$data_desc[2]['TEXT'] = $drow['AKTIVITAS'] . " - " . $drow['DESCR'] ;
					$data_desc[2]['T_ALIGN'] = "L";
					$data_desc[2]['LN_SIZE'] = 4;
					$data_desc[2]['T_SIZE'] = 7;
					
					$data_desc[3]['TEXT'] = $drow['LOKASI'] ;
					$data_desc[3]['T_ALIGN'] = "C";
					$data_desc[3]['LN_SIZE'] = 4;
					$data_desc[3]['T_SIZE'] = 7;
					
					$data_desc[4]['TEXT'] = $drow['QTY'] ;
					$data_desc[4]['T_ALIGN'] = "R";
					$data_desc[4]['LN_SIZE'] = 4;
					$data_desc[4]['T_SIZE'] = 7;
					
					$data_desc[5]['TEXT'] = $drow['SAT'] ;
					$data_desc[5]['T_ALIGN'] = "C";
					$data_desc[5]['LN_SIZE'] = 4;
					$data_desc[5]['T_SIZE'] = 7;
					
					$data_desc[6]['TEXT'] = number_format($drow['VALUE'],2,',','.') ;
					$data_desc[6]['T_ALIGN'] = "R";
					$data_desc[6]['LN_SIZE'] = 4;
					$data_desc[6]['T_SIZE'] = 7;
					
					$data_desc[7]['TEXT'] = number_format( ($drow['VALUE'] * $drow['QTY'] ),2,',','.') ;
					$data_desc[7]['T_ALIGN'] = "R";
					$data_desc[7]['LN_SIZE'] = 4;
					$data_desc[7]['T_SIZE'] = 7;
				$i++;
				$pdf->tbDrawData($data_desc);
			}
			
			$desc_row2 = $this->pms_m_daftpengajuan->formp_sumproject($noproject);
    		foreach ($desc_row2 as $drow)
			{
				$data_desc2 = Array();
					
					$data_desc2[0]['TEXT'] = "TOTAL ";
					$data_desc2[0]['T_ALIGN'] = "C";
					$data_desc2[0]['LN_SIZE'] = 4;
					$data_desc2[0]['T_SIZE'] = 7;	
					$data_desc2[0]['COLSPAN'] = 7;
					
					$data_desc2[7]['TEXT'] = number_format( ($drow['TOTAL'] ),2,',','.') ;
					$data_desc2[7]['T_ALIGN'] = "R";
					$data_desc2[7]['LN_SIZE'] = 4;
					$data_desc2[7]['T_SIZE'] = 7;
					$total = $drow['TOTAL'];
				$pdf->tbDrawData($data_desc2);
			}
		}
	   
		$pdf->tbOuputData();
		$pdf->MultiCellTag(100, 5, "<s1>* Biaya diatas sebelum dipotong PPh dan sesudah dikenakan PPN (Jika Ada)</s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s2>catatan</s2>", 0);	
		$pdf->MultiCellTag(180, 3, "<s1>.......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................... </s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s1>Lampiran</s1>", 0);	
		$attchment = $this->pms_m_daftpengajuan->formp_getattachment($noproject);
		$noatch = 1;
		if(count($attchment) > 0){
			foreach ($attchment as $attcrow){
				if($attcrow['JNS_DATA'] != ""){
					$pdf->MultiCellTag(170, 3, "<s1> ".$noatch." ". $attcrow['JNS_DATA']." - ".$attcrow['DESKRIPSI']." </s1>", 0);
				}
				$noatch++;
			}
		} else {
			$pdf->MultiCellTag(170, 3, "<s1>1. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>2. .....................................................................................................................................................</s1>", 0);
		}
		
		$pdf->Ln(1.5);
		
		$budget = 0;
		$aktivitas = '';
		$desc_budget = $this->pms_m_daftpengajuan->formp_projectact($noproject);
    	foreach ($desc_budget as $drow){
			$aktivitas = $drow['ACTIVITY'];
			$budget = $drow['RUPIAH_PER_SATUAN'];
		}
		require_once(APPPATH . 'libraries/pms/budget_kebun.inc');
		require_once(APPPATH . 'libraries/pms/authorized_ho_appr.inc');
		
		$pdf->Output();
	}
	
	 function cetakformKebun($noproject, $company) {
		//$noproject = $this->uri->segment(4);
		$stats = "baru";
		//$company = $this->uri->segment(6);
		$companyname = "";
		
		if($company == "LIH"){
			$companyname = "LANGGAM INTI HIBRINDO";
		} else if($company == "MIA"){
			$companyname = "MINANG AGRO";
		} else if($company == "MSS"){
			$companyname = "MUTIARA SAWIT SELUMA";
		} else if($company == "SSS"){
			$companyname = "SABAN SAWIT SUBUR";
		} else if($company == "SAP"){
			$companyname = "SURYA AGRO PERSADA";
		} else if($company == "TPAI"){
			$companyname = "TRANS PACIFIC AGRO INDUSTRI";
		} else if($company == "SML"){
			$companyname = "SEMAI LESTARI";
		} else if($company == "GKM"){
			$companyname = "GLOBAL KALIMANTAN MAKMUR";
		} else if($company == "ASL"){
			$companyname = "AGRA SENTRA LESTARI";
		}
		
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->FPDF('P','mm','letter');
		$pdf->SetAutoPageBreak(false, 10);
		$pdf->SetMargins(5, 7);
		$pdf->AddPage('P', 'A4D');
		$pdf->AliasNbPages(); 
		$pdf->SetStyle("s1","arial","",6,"");
		$pdf->SetStyle("s2","arial","",7,"");
		$pdf->SetStyle("s3","arial","",8,""); 
		
		require_once(APPPATH . 'libraries/table_no_border.inc');
		$headerrow = $this->pms_m_daftpengajuan->headerformp_project($noproject, $company);
		$appr0 = "";
		$appr1 = "";
		$appr2= "";
		foreach ($headerrow as $hrow)
		{	
			/* header */
			$columns = 4; //number of Columns
			$pdf->tbInitialize($columns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			
			//$pdf->Ln(10);
			$aSimpleHeader = array(); 
			for($i=0; $i<=$columns; $i++) {
				$aSimpleHeader[$i] = $table_default_header_type;
				$aSimpleHeader[$i]['WIDTH'] = 44;
			}
			
			$pdf->tbSetHeaderType($aSimpleHeader);
			$aDataType = Array();
			for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($aDataType);
		
			for ($j=0; $j<=9; $j++)
			{
				$data = Array();
					
				if ($j == 0){
					$data[0]['TEXT'] = "PROVIDENT AGRO GROUP";
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 7;
					$data[0]['LN_SIZE'] = 2;
				
				}
				if ($j == 1){
					$data[0]['TEXT'] = "PT. ". $companyname;
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 3;
					$data[0]['T_SIZE'] = 7;	
					
					$data[3]['TEXT'] = "";
					$data[3]['T_ALIGN'] = "C";
					$data[3]['LN_SIZE'] = 3;
					$data[3]['T_SIZE'] = 7;	
				}
				
				if ($j == 3){
					$judul = "";
					if($stats == "baru") {
						$judul = "PERMOHONAN PROJECT";
					} else if($stats == "revisi"){
						$judul = "PENGAJUAN REVISI PROJECT";
					}
					$data[0]['TEXT'] = $judul;
					$data[0]['T_ALIGN'] = "C";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 8;
					$data[0]['T_TYPE'] = "B";
				}
				
				if ($j == 5){
					$data[0]['TEXT'] = "No. Pengajuan :  " . $hrow['PJ_PNUM'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 6){
					$data[0]['TEXT'] = "No. Project  :  " . $hrow['PROJECT_ID'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
		
				if ($j == 7){
					$data[0]['TEXT'] = "Departemen :  " . strtoupper($hrow['DEPT']);
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 8){
					$data[0]['TEXT'] = "Tanggal Pengajuan  :  " . $hrow['PDATE'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 9){
					$data[0]['TEXT'] = "Pelaksana :  " . $hrow['PELAKSANA'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				$appr0 = $hrow['ISAPPR_LVL0'];
				$appr1 = $hrow['ISAPPR_LVL1'];
				$appr2= $hrow['ISAPPR_LVL2'];
				$pdf->tbDrawData($data);
			}
		}
		$pdf->tbOuputData();
		$pdf->Ln(1.5);
		$total = 0;
		/* middle table */	
		require_once(APPPATH . 'libraries/table_border_pms.inc');
		$middlerow = $this->pms_m_daftpengajuan->formp_project($noproject,$company);
		foreach ($middlerow as $mrow)
		{	
			/* header */
			$mcolumns = 8; //number of Columns
			$pdf->tbInitialize($mcolumns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			$mSimpleHeader = array();
			$mheader = array('No.','Kode Project','Pekerjaan','Lokasi / Afdeling','Qty','Sat','Biaya Yang Disetujui (Rp.)','Total Realisasi Biaya (Rp)',''); 
			for($i=0; $i<=count($mheader)-1; $i++) {
				$mSimpleHeader[$i] = $table_default_header_type;
				$mSimpleHeader[$i]['TEXT'] = $mheader[$i];
				$mSimpleHeader[$i]['LN_SIZE'] = 5;
				$mSimpleHeader[$i]['T_SIZE'] = 7;
				$mSimpleHeader[0]['WIDTH'] = 8;
				$mSimpleHeader[1]['WIDTH'] = 20;
				$mSimpleHeader[2]['WIDTH'] = 52;
				$mSimpleHeader[3]['WIDTH'] = 25;
				$mSimpleHeader[4]['WIDTH'] = 15;
				$mSimpleHeader[5]['WIDTH'] = 16;
				$mSimpleHeader[6]['WIDTH'] = 21;
				$mSimpleHeader[7]['WIDTH'] = 23;
			}
			
			$pdf->tbSetHeaderType($mSimpleHeader);
			$pdf->tbDrawHeader();
			
			$mDataType = Array();
			for ($i=0; $i<$mcolumns; $i++) $mDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($mDataType);
			$desc_row = $this->pms_m_daftpengajuan->formp_project($noproject);
    		$i = 1;    
			foreach ($desc_row as $drow)
			{
				$data_desc = Array();
					
					$data_desc[0]['TEXT'] = $i;
					$data_desc[0]['T_ALIGN'] = "C";
					$data_desc[0]['LN_SIZE'] = 4;
					$data_desc[0]['T_SIZE'] = 7;	
					
					$data_desc[1]['TEXT'] = $drow['PJ_ID'];
					$data_desc[1]['T_ALIGN'] = "C";
					$data_desc[1]['LN_SIZE'] = 4;
					$data_desc[1]['T_SIZE'] = 7;
					
					$data_desc[2]['TEXT'] = $drow['AKTIVITAS'] . " - " . $drow['DESCR'] ;
					$data_desc[2]['T_ALIGN'] = "L";
					$data_desc[2]['LN_SIZE'] = 4;
					$data_desc[2]['T_SIZE'] = 7;
					
					$data_desc[3]['TEXT'] = $drow['LOKASI'] ;
					$data_desc[3]['T_ALIGN'] = "C";
					$data_desc[3]['LN_SIZE'] = 4;
					$data_desc[3]['T_SIZE'] = 7;
					
					$data_desc[4]['TEXT'] = $drow['QTY'] ;
					$data_desc[4]['T_ALIGN'] = "R";
					$data_desc[4]['LN_SIZE'] = 4;
					$data_desc[4]['T_SIZE'] = 7;
					
					$data_desc[5]['TEXT'] = $drow['SAT'] ;
					$data_desc[5]['T_ALIGN'] = "C";
					$data_desc[5]['LN_SIZE'] = 4;
					$data_desc[5]['T_SIZE'] = 7;
					
					$data_desc[6]['TEXT'] = number_format($drow['VALUE'],2,',','.') ;
					$data_desc[6]['T_ALIGN'] = "R";
					$data_desc[6]['LN_SIZE'] = 4;
					$data_desc[6]['T_SIZE'] = 7;
					
					$data_desc[7]['TEXT'] = number_format( ($drow['VALUE'] * $drow['QTY'] ),2,',','.') ;
					$data_desc[7]['T_ALIGN'] = "R";
					$data_desc[7]['LN_SIZE'] = 4;
					$data_desc[7]['T_SIZE'] = 7;
				$i++;
				$pdf->tbDrawData($data_desc);
			}
			
			$desc_row2 = $this->pms_m_daftpengajuan->formp_sumproject($noproject);
    		foreach ($desc_row2 as $drow)
			{
				$data_desc2 = Array();
					
					$data_desc2[0]['TEXT'] = "TOTAL ";
					$data_desc2[0]['T_ALIGN'] = "C";
					$data_desc2[0]['LN_SIZE'] = 4;
					$data_desc2[0]['T_SIZE'] = 7;	
					$data_desc2[0]['COLSPAN'] = 7;
					
					$data_desc2[7]['TEXT'] = number_format( ($drow['TOTAL'] ),2,',','.') ;
					$data_desc2[7]['T_ALIGN'] = "R";
					$data_desc2[7]['LN_SIZE'] = 4;
					$data_desc2[7]['T_SIZE'] = 7;
					$total = $drow['TOTAL'];
				$pdf->tbDrawData($data_desc2);
			}
		}
	   
		$pdf->tbOuputData();
		$pdf->MultiCellTag(100, 5, "<s1>* Biaya diatas sebelum dipotong PPh dan sesudah dikenakan PPN (Jika Ada)</s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s2>catatan</s2>", 0);	
		$pdf->MultiCellTag(180, 3, "<s1>.......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................... </s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s1>Lampiran</s1>", 0);	
		$attchment = $this->pms_m_daftpengajuan->formp_getattachment($noproject);
		$noatch = 1;
		if(count($attchment) > 0){
			foreach ($attchment as $attcrow){
				$pdf->MultiCellTag(170, 3, "<s1> ".$noatch." ". $attcrow['JNS_DATA']." - ".$attcrow['DESKRIPSI']." </s1>", 0);
				$noatch++;
			}
		} else {
			$pdf->MultiCellTag(170, 3, "<s1>1. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>2. .....................................................................................................................................................</s1>", 0);
		}	
		$pdf->Ln(1.5);
		
		$budget = 0;
		$aktivitas = '';
		$desc_budget = $this->pms_m_daftpengajuan->formp_projectact($noproject);
    	foreach ($desc_budget as $drow){
			$aktivitas = $drow['ACTIVITY'];
			$budget = $drow['RUPIAH_PER_SATUAN'];
		}
		require_once(APPPATH . 'libraries/pms/budget_kebun.inc');
		require_once(APPPATH . 'libraries/pms/authorized_kebun.inc');
		
		$pdf->Output();
	}
	
	function cetakformKoreksi() {
		$noproject = $this->uri->segment(4);
		$stats = $this->uri->segment(5);
		$company = $this->uri->segment(6);
		$companyname = "";
		
		if($company == "LIH"){
			$companyname = "LANGGAM INTI HIBRINDO";
		} else if($company == "MIA"){
			$companyname = "MINANG AGRO";
		} else if($company == "MSS"){
			$companyname = "MUTIARA SAWIT SELUMA";
		} else if($company == "SSS"){
			$companyname = "SABAN SAWIT SUBUR";
		} else if($company == "SAP"){
			$companyname = "SURYA AGRO PERSADA";
		} else if($company == "TPAI"){
			$companyname = "TRANS PACIFIC AGRO INDUSTRI";
		} else if($company == "SML"){
			$companyname = "SEMAI LESTARI";
		} else if($company == "GKM"){
			$companyname = "GLOBAL KALIMANTAN MAKMUR";
		} else if($company == "ASL"){
			$companyname = "AGRA SENTRA LESTARI";
		}
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->FPDF('P','mm','letter');
		$pdf->SetAutoPageBreak(false, 10);
		$pdf->SetMargins(5, 7);
		$pdf->AddPage('P', 'A4D');
		$pdf->AliasNbPages(); 
		$pdf->SetStyle("s1","arial","",6,"");
		$pdf->SetStyle("s2","arial","",7,"");
		$pdf->SetStyle("s3","arial","",8,""); 
		
		require_once(APPPATH . 'libraries/table_no_border.inc');
		$headerrow = $this->pms_m_daftpengajuan->headerformp_project($noproject, $company);
		foreach ($headerrow as $hrow)
		{	
			/* header */
			$columns = 4; //number of Columns
			$pdf->tbInitialize($columns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			
			//$pdf->Ln(10);
			$aSimpleHeader = array(); 
			for($i=0; $i<=$columns; $i++) {
				$aSimpleHeader[$i] = $table_default_header_type;
				$aSimpleHeader[$i]['WIDTH'] = 44;
			}
			
			$pdf->tbSetHeaderType($aSimpleHeader);
			$aDataType = Array();
			for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($aDataType);
		
			for ($j=0; $j<=9; $j++)
			{
				$data = Array();
					
				if ($j == 0){
					$data[0]['TEXT'] = "PROVIDENT AGRO GROUP";
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 7;
					$data[0]['LN_SIZE'] = 2;
				
				}
				if ($j == 1){
					$data[0]['TEXT'] = "PT. ". $companyname;
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 3;
					$data[0]['T_SIZE'] = 7;	
					
					$data[3]['TEXT'] = "";
					$data[3]['T_ALIGN'] = "C";
					$data[3]['LN_SIZE'] = 3;
					$data[3]['T_SIZE'] = 7;	
				}
				
				if ($j == 3){
					$judul = "";
					if($stats == "baru") {
						$judul = "PENGAJUAN REVISI PROJECT";
					} 
					$data[0]['TEXT'] = $judul;
					$data[0]['T_ALIGN'] = "C";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 8;
					$data[0]['T_TYPE'] = "B";
				}
				
				if ($j == 5){
					$data[0]['TEXT'] = "No. Pengajuan :  " . $hrow['PJ_PNUM'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 6){
					$data[0]['TEXT'] = "No. Project  :  " . $hrow['PROJECT_ID'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
		
				if ($j == 7){
					$data[0]['TEXT'] = "Departemen :  " . $hrow['DEPT'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 8){
					$data[0]['TEXT'] = "Tanggal Pengajuan  :  " . $hrow['PDATE'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 9){
					$data[0]['TEXT'] = "Pelaksana :  " . $hrow['PELAKSANA'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				$pdf->tbDrawData($data);
			}
		}
		$pdf->tbOuputData();
		$pdf->Ln(1.5);
		$total = 0;
		/* middle table */	
		require_once(APPPATH . 'libraries/table_border_pms.inc');
		$middlerow = $this->pms_m_daftpengajuan->formp_project($noproject,$company);
		foreach ($middlerow as $mrow)
		{	
			/* header */
			$mcolumns = 8; //number of Columns
			$pdf->tbInitialize($mcolumns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			$mSimpleHeader = array();
			$mheader = array('No.','Kode Project','Pekerjaan - Lokasi / Afdeling','Total Biaya - Pengajuan Awal','Qty','Sat','Biaya Satuan Revisi(Rp.)','Total Biaya Revisi (Rp)',''); 
			for($i=0; $i<=count($mheader)-1; $i++) {
				$mSimpleHeader[$i] = $table_default_header_type;
				$mSimpleHeader[$i]['TEXT'] = $mheader[$i];
				$mSimpleHeader[$i]['LN_SIZE'] = 5;
				$mSimpleHeader[$i]['T_SIZE'] = 7;
				$mSimpleHeader[0]['WIDTH'] = 8;
				$mSimpleHeader[1]['WIDTH'] = 20;
				$mSimpleHeader[2]['WIDTH'] = 52;
				$mSimpleHeader[3]['WIDTH'] = 25;
				$mSimpleHeader[4]['WIDTH'] = 15;
				$mSimpleHeader[5]['WIDTH'] = 16;
				$mSimpleHeader[6]['WIDTH'] = 21;
				$mSimpleHeader[7]['WIDTH'] = 23;
			}
			
			$pdf->tbSetHeaderType($mSimpleHeader);
			$pdf->tbDrawHeader();
			
			$mDataType = Array();
			for ($i=0; $i<$mcolumns; $i++) $mDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($mDataType);
			$desc_row = $this->pms_m_daftpengajuan->formp_project($noproject);
    		$i = 1;    
			foreach ($desc_row as $drow)
			{
				$data_desc = Array();
					
					$data_desc[0]['TEXT'] = $i;
					$data_desc[0]['T_ALIGN'] = "C";
					$data_desc[0]['LN_SIZE'] = 4;
					$data_desc[0]['T_SIZE'] = 7;	
					
					$data_desc[1]['TEXT'] = $drow['PJ_ID'];
					$data_desc[1]['T_ALIGN'] = "C";
					$data_desc[1]['LN_SIZE'] = 4;
					$data_desc[1]['T_SIZE'] = 7;
					
					$data_desc[2]['TEXT'] = $drow['AKTIVITAS'] . " - " . $drow['DESCR'] . " | " . $drow['LOKASI'] ;
					$data_desc[2]['T_ALIGN'] = "L";
					$data_desc[2]['LN_SIZE'] = 4;
					$data_desc[2]['T_SIZE'] = 7;
					
					$data_desc[3]['TEXT'] = 0 ;
					$data_desc[3]['T_ALIGN'] = "C";
					$data_desc[3]['LN_SIZE'] = 4;
					$data_desc[3]['T_SIZE'] = 7;
					
					$data_desc[4]['TEXT'] = $drow['QTY'] ;
					$data_desc[4]['T_ALIGN'] = "R";
					$data_desc[4]['LN_SIZE'] = 4;
					$data_desc[4]['T_SIZE'] = 7;
					
					$data_desc[5]['TEXT'] = $drow['SAT'] ;
					$data_desc[5]['T_ALIGN'] = "C";
					$data_desc[5]['LN_SIZE'] = 4;
					$data_desc[5]['T_SIZE'] = 7;
					
					$data_desc[6]['TEXT'] = number_format($drow['VALUE'],2,',','.') ;
					$data_desc[6]['T_ALIGN'] = "R";
					$data_desc[6]['LN_SIZE'] = 4;
					$data_desc[6]['T_SIZE'] = 7;
					
					$data_desc[7]['TEXT'] = number_format( ($drow['VALUE'] * $drow['QTY'] ),2,',','.') ;
					$data_desc[7]['T_ALIGN'] = "R";
					$data_desc[7]['LN_SIZE'] = 4;
					$data_desc[7]['T_SIZE'] = 7;
				$i++;
				$pdf->tbDrawData($data_desc);
			}
			
			$desc_row2 = $this->pms_m_daftpengajuan->formp_sumproject($noproject);
    		foreach ($desc_row2 as $drow)
			{
				$data_desc2 = Array();
					
					$data_desc2[0]['TEXT'] = "TOTAL ";
					$data_desc2[0]['T_ALIGN'] = "C";
					$data_desc2[0]['LN_SIZE'] = 4;
					$data_desc2[0]['T_SIZE'] = 7;	
					$data_desc2[0]['COLSPAN'] = 7;
					
					$data_desc2[7]['TEXT'] = number_format( ($drow['TOTAL'] ),2,',','.') ;
					$data_desc2[7]['T_ALIGN'] = "R";
					$data_desc2[7]['LN_SIZE'] = 4;
					$data_desc2[7]['T_SIZE'] = 7;
					$total = $drow['TOTAL'];
				$pdf->tbDrawData($data_desc2);
			}
		}
	   
		$pdf->tbOuputData();
		$pdf->MultiCellTag(100, 5, "<s1>* Biaya diatas sebelum dipotong PPh dan sesudah dikenakan PPN (Jika Ada)</s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s2>catatan</s2>", 0);	
		$pdf->MultiCellTag(180, 3, "<s1>.......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................... </s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s1>Lampiran</s1>", 0);	
		$attchment = $this->pms_m_daftpengajuan->formp_getattachment($noproject);
		$noatch = 1;
		if(count($attchment) > 0){
			foreach ($attchment as $attcrow){
				if($attcrow['JNS_DATA'] != ""){
					$pdf->MultiCellTag(170, 3, "<s1> ".$noatch." ". $attcrow['JNS_DATA']." - ".$attcrow['DESKRIPSI']." </s1>", 0);
				}
				$noatch++;
			}
		} else {
			$pdf->MultiCellTag(170, 3, "<s1>1. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>2. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>3. .....................................................................................................................................................</s1>", 0);
		}
		
		$pdf->Ln(1.5);
		
		$budget = 0;
		$aktivitas = '';
		$desc_budget = $this->pms_m_daftpengajuan->formp_projectact($noproject);
    	foreach ($desc_budget as $drow){
			$aktivitas = $drow['ACTIVITY'];
			$budget = $drow['RUPIAH_PER_SATUAN'];
		}
		require_once(APPPATH . 'libraries/pms/budget_kebun.inc');
		require_once(APPPATH . 'libraries/pms/authorized_ho_rev.inc');
		
		$pdf->Output();
	}
	
	function cetakformClosing() {
		$noproject = $this->uri->segment(4);
		$stats = $this->uri->segment(5);
		$company = $this->uri->segment(6);
		$companyname = "";
		
		if($company == "LIH"){
			$companyname = "LANGGAM INTI HIBRINDO";
		} else if($company == "MIA"){
			$companyname = "MINANG AGRO";
		} else if($company == "MSS"){
			$companyname = "MUTIARA SAWIT SELUMA";
		} else if($company == "SSS"){
			$companyname = "SABAN SAWIT SUBUR";
		} else if($company == "SAP"){
			$companyname = "SURYA AGRO PERSADA";
		} else if($company == "TPAI"){
			$companyname = "TRANS PACIFIC AGRO INDUSTRI";
		} else if($company == "SML"){
			$companyname = "SEMAI LESTARI";
		} else if($company == "GKM"){
			$companyname = "GLOBAL KALIMANTAN MAKMUR";
		} else if($company == "ASL"){
			$companyname = "AGRA SENTRA LESTARI";
		}
		$pdf = new pdf_usage();		
		$pdf->Open();
		$pdf->FPDF('P','mm','letter');
		$pdf->SetAutoPageBreak(false, 10);
		$pdf->SetMargins(5, 7);
		$pdf->AddPage('P', 'A4D');
		$pdf->AliasNbPages(); 
		$pdf->SetStyle("s1","arial","",6,"");
		$pdf->SetStyle("s2","arial","",7,"");
		$pdf->SetStyle("s3","arial","",8,""); 
		
		require_once(APPPATH . 'libraries/table_no_border.inc');
		$headerrow = $this->pms_m_daftpengajuan->headerformp_project($noproject, $company);
		foreach ($headerrow as $hrow)
		{	
			/* header */
			$columns = 4; //number of Columns
			$pdf->tbInitialize($columns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			
			//$pdf->Ln(10);
			$aSimpleHeader = array(); 
			for($i=0; $i<=$columns; $i++) {
				$aSimpleHeader[$i] = $table_default_header_type;
				$aSimpleHeader[$i]['WIDTH'] = 44;
			}
			
			$pdf->tbSetHeaderType($aSimpleHeader);
			$aDataType = Array();
			for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($aDataType);
		
			for ($j=0; $j<=9; $j++)
			{
				$data = Array();
					
				if ($j == 0){
					$data[0]['TEXT'] = "PROVIDENT AGRO GROUP";
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 7;
					$data[0]['LN_SIZE'] = 2;
				
				}
				if ($j == 1){
					$data[0]['TEXT'] = "PT. ". $companyname;
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 3;
					$data[0]['T_SIZE'] = 7;	
					
					$data[3]['TEXT'] = "";
					$data[3]['T_ALIGN'] = "C";
					$data[3]['LN_SIZE'] = 3;
					$data[3]['T_SIZE'] = 7;	
				}
				
				if ($j == 3){
					$judul = "";
					if($stats == "baru") {
						$judul = "BERITA ACARA PENYELESAIAN PROJECT";
					} 
					$data[0]['TEXT'] = $judul;
					$data[0]['T_ALIGN'] = "C";
					$data[0]['COLSPAN'] = 4;
					$data[0]['T_SIZE'] = 8;
					$data[0]['T_TYPE'] = "B";
				}
				
				if ($j == 5){
					$data[0]['TEXT'] = "No. Pengajuan :  " . $hrow['PJ_PNUM'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 6){
					$data[0]['TEXT'] = "No. Project  :  " . $hrow['PROJECT_ID'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
		
				if ($j == 7){
					$data[0]['TEXT'] = "Departemen :  " . strtoupper($hrow['DEPT']);
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 8){
					$data[0]['TEXT'] = "Tanggal Pengajuan  :  " . $hrow['PDATE'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				if ($j == 9){
					$data[0]['TEXT'] = "Pelaksana :  " . $hrow['PELAKSANA'];
					$data[0]['T_ALIGN'] = "L";
					$data[0]['COLSPAN'] = 4;
					$data[0]['COLSPAN'] = 2;
					$data[0]['LN_SIZE'] = 4;
					$data[0]['T_SIZE'] = 7;		
				}
				
				$pdf->tbDrawData($data);
			}
		}
		$pdf->tbOuputData();
		$pdf->Ln(1.5);
		$total = 0;
		/* middle table */	
		require_once(APPPATH . 'libraries/table_border_pms.inc');
		$middlerow = $this->pms_m_daftpengajuan->formp_project($noproject,$company);
		foreach ($middlerow as $mrow)
		{	
			/* header */
			$mcolumns = 8; //number of Columns
			$pdf->tbInitialize($mcolumns, true, true);
			$pdf->tbSetTableType($table_default_table_type);
			
			$mSimpleHeader = array();
			$mheader = array('No.','Kode Project','Pekerjaan','Lokasi / Afdeling','Qty','Sat','Biaya Satuan (Rp.)','Total Biaya (Rp)',''); 
			for($i=0; $i<=count($mheader)-1; $i++) {
				$mSimpleHeader[$i] = $table_default_header_type;
				$mSimpleHeader[$i]['TEXT'] = $mheader[$i];
				$mSimpleHeader[$i]['LN_SIZE'] = 5;
				$mSimpleHeader[$i]['T_SIZE'] = 7;
				$mSimpleHeader[0]['WIDTH'] = 8;
				$mSimpleHeader[1]['WIDTH'] = 20;
				$mSimpleHeader[2]['WIDTH'] = 52;
				$mSimpleHeader[3]['WIDTH'] = 25;
				$mSimpleHeader[4]['WIDTH'] = 15;
				$mSimpleHeader[5]['WIDTH'] = 16;
				$mSimpleHeader[6]['WIDTH'] = 21;
				$mSimpleHeader[7]['WIDTH'] = 23;
			}
			
			$pdf->tbSetHeaderType($mSimpleHeader);
			$pdf->tbDrawHeader();
			
			$mDataType = Array();
			for ($i=0; $i<$mcolumns; $i++) $mDataType[$i] = $table_default_data_type;
		
			$pdf->tbSetDataType($mDataType);
			$desc_row = $this->pms_m_daftpengajuan->formp_project($noproject);
    		$i = 1;    
			foreach ($desc_row as $drow)
			{
				$data_desc = Array();
					
					$data_desc[0]['TEXT'] = $i;
					$data_desc[0]['T_ALIGN'] = "C";
					$data_desc[0]['LN_SIZE'] = 4;
					$data_desc[0]['T_SIZE'] = 7;	
					
					$data_desc[1]['TEXT'] = $drow['PJ_ID'];
					$data_desc[1]['T_ALIGN'] = "C";
					$data_desc[1]['LN_SIZE'] = 4;
					$data_desc[1]['T_SIZE'] = 7;
					
					$data_desc[2]['TEXT'] = $drow['AKTIVITAS'] . " - " . $drow['DESCR'] ;
					$data_desc[2]['T_ALIGN'] = "L";
					$data_desc[2]['LN_SIZE'] = 4;
					$data_desc[2]['T_SIZE'] = 7;
					
					$data_desc[3]['TEXT'] = $drow['LOKASI'] ;
					$data_desc[3]['T_ALIGN'] = "C";
					$data_desc[3]['LN_SIZE'] = 4;
					$data_desc[3]['T_SIZE'] = 7;
					
					$data_desc[4]['TEXT'] = $drow['QTY'] ;
					$data_desc[4]['T_ALIGN'] = "R";
					$data_desc[4]['LN_SIZE'] = 4;
					$data_desc[4]['T_SIZE'] = 7;
					
					$data_desc[5]['TEXT'] = $drow['SAT'] ;
					$data_desc[5]['T_ALIGN'] = "C";
					$data_desc[5]['LN_SIZE'] = 4;
					$data_desc[5]['T_SIZE'] = 7;
					
					$data_desc[6]['TEXT'] = number_format($drow['VALUE'],2,',','.') ;
					$data_desc[6]['T_ALIGN'] = "R";
					$data_desc[6]['LN_SIZE'] = 4;
					$data_desc[6]['T_SIZE'] = 7;
					
					$data_desc[7]['TEXT'] = number_format( ($drow['VALUE'] * $drow['QTY'] ),2,',','.') ;
					$data_desc[7]['T_ALIGN'] = "R";
					$data_desc[7]['LN_SIZE'] = 4;
					$data_desc[7]['T_SIZE'] = 7;
				$i++;
				$pdf->tbDrawData($data_desc);
			}
			
			$desc_row2 = $this->pms_m_daftpengajuan->formp_sumproject($noproject);
    		foreach ($desc_row2 as $drow)
			{
				$data_desc2 = Array();
					
					$data_desc2[0]['TEXT'] = "TOTAL ";
					$data_desc2[0]['T_ALIGN'] = "C";
					$data_desc2[0]['LN_SIZE'] = 4;
					$data_desc2[0]['T_SIZE'] = 7;	
					$data_desc2[0]['COLSPAN'] = 7;
					
					$data_desc2[7]['TEXT'] = number_format( ($drow['TOTAL'] ),2,',','.') ;
					$data_desc2[7]['T_ALIGN'] = "R";
					$data_desc2[7]['LN_SIZE'] = 4;
					$data_desc2[7]['T_SIZE'] = 7;
					$total = $drow['TOTAL'];
				$pdf->tbDrawData($data_desc2);
			}
		}
	   
		$pdf->tbOuputData();
		$pdf->MultiCellTag(100, 5, "<s1>* Biaya diatas sebelum dipotong PPh dan sesudah dikenakan PPN (Jika Ada)</s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s2>catatan</s2>", 0);	
		$pdf->MultiCellTag(180, 3, "<s1>.......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................... </s1>", 0);	
		$pdf->MultiCellTag(100, 5, "<s1>Lampiran</s1>", 0);	
		$attchment = $this->pms_m_daftpengajuan->formp_getattachment($noproject);
		$noatch = 1;
		if(count($attchment) > 0){
			foreach ($attchment as $attcrow){
				if($attcrow['JNS_DATA'] != ""){
					$pdf->MultiCellTag(170, 3, "<s1> ".$noatch." ". $attcrow['JNS_DATA']." - ".$attcrow['DESKRIPSI']." </s1>", 0);
				}
				$noatch++;
			}
		} else {
			$pdf->MultiCellTag(170, 3, "<s1>1. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>2. .....................................................................................................................................................</s1>", 0);
			$pdf->MultiCellTag(170, 3, "<s1>3. .....................................................................................................................................................</s1>", 0);
		}
		
		$pdf->Ln(1.5);
		
		$budget = 0;
		$aktivitas = '';
		$desc_budget = $this->pms_m_daftpengajuan->formp_projectact($noproject);
    	foreach ($desc_budget as $drow){
			$aktivitas = $drow['ACTIVITY'];
			$budget = $drow['RUPIAH_PER_SATUAN'];
		}
		//require_once(APPPATH . 'libraries/pms/budget_kebun.inc');
		require_once(APPPATH . 'libraries/pms/authorized_ba_close.inc');
		
		$pdf->Output();
	}
}

?>