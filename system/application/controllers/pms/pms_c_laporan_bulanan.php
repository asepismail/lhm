<?php
class pms_c_laporan_bulanan extends Controller{
    private $lastmenu;
    private $data;
    
    function __construct(){
        parent::__construct();
        $this->load->model('pms/pms_m_laporan_bulanan');
        $this->load->model('model_c_user_auth');
        $this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->library('csvReader');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->lastmenu="main_c_pms";
		require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
    }
    
    function index(){
        $view="pms/pms_v_laporan_bulanan";
        $this->data['js'] = $this->js_ba_pj();
		$this->data['judul_header'] = "Berita Acara Bulanan Project";
        $this->data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $this->data['company_name'] = htmlentities($this->session->userdata('NCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $this->data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $this->data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $this->data['menupms'] = $this->model_c_user_auth->get_menu_pms($this->session->userdata('LOGINID'));
		$this->data['periode'] = $this->data['periode'] = $this->global_func->drop_date2('fbulan','ftahun','select');
		//$this->data['periode2'] = $this->global_func->drop_year('tahun2','select');
		$this->data['dropcompany'] = $this->dropdownlist_company('company1');
		//$this->data['dropcompany2'] = $this->dropdownlist_company('company2');
		
				
		if ($this->data['login_id'] == TRUE){
		  	$this->load->view($view, $this->data);
		} else {
			  redirect('login');
		}
    }
	
	function js_ba_pj(){
        $js = "jQuery(document).ready(function(){
					$('#form_progress').hide();		
					
					$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
               		$('#TO').datepicker({dateFormat:'yy-mm-dd'});				
				});
				
				$('#progressbar').dialog({
							bgiframe: true, autoOpen: false,
							resizable: true, draggable: true,
							closeOnEscape:false, height: 160,
							width: 220, modal: true
				});
				
        		jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var gc = $('#GANG_CODE').val();
                var jns_laporan = $('#jns_laporan').val();
				
				var tfrom = document.getElementById('FROM').value;
                var elem = tfrom.split('-');
                from = elem[0]+elem[1]+elem[2];
                            
                var tto = document.getElementById('TO').value;
                var elem2 = tto.split('-');
                to = elem2[0]+elem2[1]+elem2[2];
                        
                var period = to - from;
				
				if ( period > 0 ){
					if ( jns_laporan == 'html'){
							urls = url + 'rpt_du/preview/' + gc + '/' + from  + '/' + to, 
							$('#frame').attr('src',urls); 
						} else if ( jns_laporan == 'pdf'){
							urls = url+'rpt_du/generate/' + gc + '/' + from  + '/' + to,  
							$('#frame').attr('src',urls);                  
						} else if ( jns_laporan == 'excell'){
							urls = url + 'rpt_du/du_xls/' + gc + '/' + from  + '/' + to,
							$.download(urls,'');
					}
				} else {
                    alert('rentang periode salah!!');
                    return false;
                }
            }); 
				
				jQuery('#regenerategc').click(function (){
					var answer = confirm ('regenerate ulang data kemandoran?');
					if (answer){
				      var postdata = {}; 
					  var tfrom = document.getElementById('FROM').value;
					  var elem = tfrom.split('-');
					  var periode = elem[0]+elem[1];
					
					  $('#load').show();
					  document.getElementById('msg').innerHTML= 'Mohon menunggu...regenerate sedang diproses...';
					  $('#progressbar').dialog('open');
					  $.post( url+'rpt_du/generateKemandoran/'+periode, postdata,function(status) 		  { 
						var status = new String(status);
						   if(status.replace(/\s/g,'') != '') { 
								$('#load').hide();
								document.getElementById('msg').innerHTML= status; 
						  };  
					  }); 
				    }
				});
				
				jQuery('#regeneratehk').click(function (){
					var answer = confirm ('regenerate ulang GP Karyawan BHL & SKU?');
					if (answer){
				      var postdata = {}; 
					  var tfrom = document.getElementById('FROM').value;
					  var elem = tfrom.split('-');
					  var tahun = elem[0];
					
					  $('#load').show();
					  document.getElementById('msg').innerHTML= 'Mohon menunggu...regenerate sedang diproses...';
					  $('#progressbar').dialog('open');
					  $.post( url+'rpt_du/generateGP/'+tahun, postdata,function(status) 		  { 
						var status = new String(status);
						   if(status.replace(/\s/g,'') != '') { 
								$('#load').hide();
								document.getElementById('msg').innerHTML= status; 
						  };  
					  }); 
				    }
				});";
        return $js;
    }
	
	/* dropdown company */
   function dropdownlist_company($name){ 
		$string = "<select  name='".$name."' class='select' id='".$name."' style='width:230px;'>";
		$string .= "<option value=''> -- pilih -- </option>";
		$data = $this->pms_m_laporan_bulanan->getCompany();
		
		foreach ( $data as $row){
		   if( (isset($default))){
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\"  selected>".$row['COMPANY_NAME']." </option>";
			} else {
			 $string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		} 
		$string =$string. "</select>";
		return $string;
	}
	
	function styling(){
		$style = "<style>
					body { margin: 0 auto; font-size: 12px; width: 695px; }
					.divMain {
						margin: 0 auto;
						font-size: 12px;
						width:695px;
						height:842px;
					}
					.tbl_header { 
						font-size: 12px; 
						color:#000; 
						border-top:.5px solid; 
						border-left:.5px solid; 
					}
					.tbl_th { 
						font-size: 12px; 
						color:#000; 
						font-weight:bold; 
						border-top:1px solid; 
						border-bottom:1px solid; 
						padding:3px; 
						border-right:1px solid 
					}
					hr {
						border:.5px solid; 
					}
					.tbl_td { 
						font-size: 12px; color:#000;
						border-bottom:1px solid; 
						padding:3px; 
						border-right:1px solid 
					} 
					#column1-wrap{
						float: left;
						width: 100px;
						margin-right: 150px;
					}
					#column1{
						margin-right: 90px;
						width: 100px;
					}
					#column2{
						float: left;
						min-width: 580px;
						margin-left: -150px;
					}
					#clear{
						clear: both;
					}
					</style>";
		return $style;
	}
	
	function newCetakBulanan() {
		require_once(APPPATH . 'helpers/dompdf/dompdf_config.inc.php');
		$template_path = base_url().$this->config->item('template_path');
		
		$periode = $this->uri->segment(5);
		$company = $this->uri->segment(4);
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
		
		$rpt = $this->styling();
		$rpt .= '<body>';
		
		$rpt .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td colspan="13" align="left"><b>PROVIDENT AGRO GROUP</b></td>
  </tr>
   <tr>
    <td colspan="13" align="left"><b>'.$companyname.'</b></td>
  </tr>
  <tr>
    <td colspan="13" align="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="13" align="center"><b>BERITA ACARA BULANAN PROJECT</b></td>
  </tr>
   <tr>
    <td colspan="13" align="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="13" align="left">&nbsp;</td>
  </tr>
   <tr>
    <td colspan="13" align="left"><b>Tanggal : '. date ("d-m-Y") .' </b></td>
  </tr>
  <tr style="padding:10px;">
    <td style="border-left:1px solid;"  align="center" class="tbl_th" rowspan="2">No</td>
    <td class="tbl_th" colspan="6" align="center">Deskripsi Project</td>
    <td class="tbl_th" colspan="3" align="center">Bulan Ini</td>
    <td class="tbl_th" colspan="3" align="center">Sampai Dengan Bulan Ini</td>
  </tr>
  <tr style="padding:10px;">
    <td class="tbl_th" align="center">No. Project</td>
    <td class="tbl_th" align="center">Pekerjaan</td>
    <td class="tbl_th" align="center">Lokasi / Afdeling</td>
    <td class="tbl_th" align="center">Qty</td>
    <td class="tbl_th" align="center">Sat</td>
    <td class="tbl_th" align="center">Total Biaya ( Rp )</td>
    <td class="tbl_th" align="center">Qty</td>
    <td class="tbl_th" align="center">Sat</td>
    <td class="tbl_th" align="center">Total Biaya (Rp)</td>
    <td class="tbl_th" align="center">Qty</td>
    <td class="tbl_th" align="center">Sat</td>
    <td class="tbl_th" align="center">Total Biaya (Rp)</td>
  </tr>';
  $desc_row = $this->pms_m_laporan_bulanan->getReport($company, $periode);
  $i = 1;    
  foreach ($desc_row as $drow)
  {
	$rpt .= '<tr>
		<td style="border-left:1px solid;" class="tbl_td" align="center">'.$i.'</td>
		<td class="tbl_td" align="center">'.$drow['kode_project'].'</td>
		<td class="tbl_td" align="center">'.$drow['kode_act'] . ' - ' . $drow['nama_act'].'</td>
		<td class="tbl_td" align="center">'.$drow['lokasi'].'</td>
		<td class="tbl_td" align="right">'.number_format($drow['plannedqty'],2,',','.').'</td>
		<td class="tbl_td" align="center">'.$drow['uom'].'</td>
		<td class="tbl_td" align="right">'.number_format($drow['plannedqty'],2,',','.').'</td>
		<td class="tbl_td" align="center">'.$drow['uom'].'</td>
		<td class="tbl_td" align="right">'. number_format($drow['plannedamt'],2,',','.').'</td>
		<td class="tbl_td" >&nbsp;</td>
		<td class="tbl_td" >&nbsp;</td>
		<td class="tbl_td" >&nbsp;</td>
		<td class="tbl_td" >&nbsp;</td>
	  </tr>';
	  $i++;
   }
  
		$rpt .= '</table>';
		
		ob_start();
			$dompdf = new DOMPDF();
			$dompdf->load_html($rpt);
			$dompdf->render();
			$dompdf->output();
			$canvas = $dompdf->get_canvas();
			$font = Font_Metrics::get_font("helvetica", "bold");
			$dompdf->set_paper("letter", "landscape");
			$dompdf->stream("BA Bulanan Project ".$company.".pdf", array("Attachment" => 0));
			$canvas->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
			
			
	
	}
	
	function cetakBaBulanan() {
		
		$periode = $this->uri->segment(5);
		$company = $this->uri->segment(4);
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
		$pdf->AddPage('L', 'A4D');
		$pdf->AliasNbPages(); 
		$pdf->SetStyle("s1","arial","",6,"");
		$pdf->SetStyle("s2","arial","",7,"");
		$pdf->SetStyle("s3","arial","",8,""); 
		
		require_once(APPPATH . 'libraries/table_no_border.inc');
		
		/* header */
		$columns = 4; //number of Columns
		$pdf->tbInitialize($columns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		
		//$pdf->Ln(10);
		$aSimpleHeader = array(); 
		for($i=0; $i<=$columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['WIDTH'] = 60;
		}
		
		$pdf->tbSetHeaderType($aSimpleHeader);
		$aDataType = Array();
		for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($aDataType);
	
		for ($j=0; $j<=5; $j++)
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
				$judul = "BERITA ACARA BULANAN PROJECT";
				$data[0]['TEXT'] = $judul;
				$data[0]['T_ALIGN'] = "C";
				$data[0]['COLSPAN'] = 4;
				$data[0]['T_SIZE'] = 8;
				$data[0]['T_TYPE'] = "B";
			}
			
			if ($j == 5){
				$data[0]['TEXT'] = "Tanggal :  " . date ("d-m-Y");
				$data[0]['T_ALIGN'] = "L";
				$data[0]['COLSPAN'] = 4;
				$data[0]['COLSPAN'] = 2;
				$data[0]['LN_SIZE'] = 4;
				$data[0]['T_SIZE'] = 7;		
			}
			
			$pdf->tbDrawData($data);
		}
		
		$pdf->tbOuputData();
		$pdf->Ln(1.5);
		$total = 0;
		/* middle table */	
		require_once(APPPATH . 'libraries/table_border_pms.inc');
		
		/* header */
		$mcolumns = 13; //number of Columns
		$pdf->tbInitialize($mcolumns, true, true);
		$pdf->tbSetTableType($table_default_table_type);
		
		$aSimpleHeader = array();
		$mheader1 = array('No.','Deskripsi Project','','','','','','Bulan Ini','','','Sampai Dengan Bulan Ini','',''); 
		$mheader = array('','No Project','Pekerjaan','Lokasi / Afdeling','Qty','Sat','Total Biaya (Rp)','Qty','Sat','Total Biaya (Rp)','Qty','Sat','Total Biaya (Rp)'); 
		
		for($i=0; $i<$mcolumns; $i++) {
			// if ($i == '1') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
			//if ($i == '5') { $aSimpleHeader[$i]['COLSPAN'] = 3; } 
			
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['TEXT'] = $mheader1[$i];
			$aSimpleHeader[$i]['LN_SIZE'] = 5;
			$aSimpleHeader[$i]['T_SIZE'] = 7;
			$aSimpleHeader[$i]['WIDTH'] = 15.5;
			$aSimpleHeader[2]['WIDTH'] = 50;
			$aSimpleHeader[0]['WIDTH'] = 10;
			$aSimpleHeader[1]['WIDTH'] = 16;
			$aSimpleHeader[3]['WIDTH'] = 18;
			$aSimpleHeader[0]['ROWSPAN'] = 2;
			$aSimpleHeader[1]['COLSPAN'] = 6;
			$aSimpleHeader[7]['COLSPAN'] = 3;
			$aSimpleHeader[10]['COLSPAN'] = 3;
		
			
			$aSimpleHeader2[$i] = $table_default_header_type;
			$aSimpleHeader2[$i]['TEXT'] = $mheader[$i];
			$aSimpleHeader2[$i]['LN_SIZE'] = 5;
			$aSimpleHeader2[$i]['T_SIZE'] = 7;
			$aSimpleHeader2[$i]['WIDTH'] = 15.5;
			$aSimpleHeader2[1]['WIDTH'] = 16;
			$aSimpleHeader2[2]['WIDTH'] = 50;
			$aSimpleHeader2[3]['WIDTH'] = 18;
		
		}
		
		$pdf->tbSetHeaderType($aSimpleHeader);
		$pdf->tbSetHeaderType($aSimpleHeader2);
		$pdf->tbDrawHeader();
		
		$mDataType = array();
		for ($i=0; $i<$mcolumns; $i++) $mDataType[$i] = $table_default_data_type;
	
		$pdf->tbSetDataType($mDataType);
		$desc_row = $this->pms_m_laporan_bulanan->getReport($company, $periode);
		$i = 1;    
		foreach ($desc_row as $drow)
		{
			$datas = Array();
			
			$datas[0]['TEXT'] = $i;
			$datas[0]['T_ALIGN'] = "C";
			$datas[0]['LN_SIZE'] = 4;
			$datas[0]['T_SIZE'] = 7;	
			$datas[0]['WIDTH'] = 10;
			
			$datas[1]['TEXT'] = $drow['kode_project'];
			$datas[1]['T_ALIGN'] = "C";
			$datas[1]['LN_SIZE'] = 4;
			$datas[1]['T_SIZE'] = 7;
			$datas[1]['WIDTH'] = 16;
			
			$datas[2]['TEXT'] = $drow['kode_act'] . " - " . $drow['nama_act'] ;
			$datas[2]['T_ALIGN'] = "L";
			$datas[2]['LN_SIZE'] = 4;
			$datas[2]['T_SIZE'] = 7;
			$datas[2]['WIDTH'] = 50;
					
			$datas[3]['TEXT'] = $drow['lokasi'] ;
			$datas[3]['T_ALIGN'] = "C";
			$datas[3]['LN_SIZE'] = 4;
			$datas[3]['T_SIZE'] = 7;
			$datas[3]['WIDTH'] = 18;
			
			$datas[4]['TEXT'] = $drow['plannedqty'] ;
			$datas[4]['T_ALIGN'] = "R";
			$datas[4]['LN_SIZE'] = 4;
			$datas[4]['T_SIZE'] = 7;
			$datas[4]['WIDTH'] = 15.5;
			
			$datas[5]['TEXT'] = $drow['uom'] ;
			$datas[5]['T_ALIGN'] = "C";
			$datas[5]['LN_SIZE'] = 4;
			$datas[5]['T_SIZE'] = 7;
			$datas[5]['WIDTH'] = 15.5;
			
			$datas[6]['TEXT'] = number_format($drow['plannedamt'],2,',','.') ;
			$datas[6]['T_ALIGN'] = "R";
			$datas[6]['LN_SIZE'] = 4;
			$datas[6]['T_SIZE'] = 7;
			$datas[6]['WIDTH'] = 15.5;
			
			$datas[7]['TEXT'] = $drow['plannedqty'] ;
			$datas[7]['T_ALIGN'] = "R";
			$datas[7]['LN_SIZE'] = 4;
			$datas[7]['T_SIZE'] = 7;
			$datas[7]['WIDTH'] = 15.5;
			
			$datas[8]['TEXT'] = $drow['uom'] ;
			$datas[8]['T_ALIGN'] = "C";
			$datas[8]['LN_SIZE'] = 4;
			$datas[8]['T_SIZE'] = 7;
			$datas[8]['WIDTH'] = 15.5;
			
			$datas[9]['TEXT'] = number_format($drow['plannedamt'],2,',','.') ;
			$datas[9]['T_ALIGN'] = "R";
			$datas[9]['LN_SIZE'] = 4;
			$datas[9]['T_SIZE'] = 7;
			$datas[9]['WIDTH'] = 15.5;
				
			$i++;
			$pdf->tbDrawData($datas); 
		}
		$pdf->tbDrawData($datas); 
		//require_once(APPPATH . 'libraries/pms/budget_kebun.inc');
		//require_once(APPPATH . 'libraries/pms/authorized_ho.inc');

		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		$pdf->Output();
	}
	
}