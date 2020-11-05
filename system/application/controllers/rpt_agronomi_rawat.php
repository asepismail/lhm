<?
class rpt_agronomi_rawat extends Controller 
{
	function rpt_agronomi_rawat ()
	{
		parent::Controller();	
		$this->load->model( 'model_rpt_agronomi' );
		 
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_agronomi_rawat";
         	 		
		$this->load->helper('form');
		$this->load->helper('language'); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->database();
		$this->load->plugin('to_excel');
		$this->load->helper('date');
	}
	
	function index()
    {
		$view = "rpt_agronomi_rawat";
        $data = array();
        $data['judul_header'] = "Laporan Historikal Rawat Tanaman";
		$data['js'] = $this->js_agro_rawat();    
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['dropcompany'] = $this->dropdownlist_company();
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
		
		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
            redirect('login');
        }
    }  
	
	function dropdownlist_company(){ 
		$string = "<select  name='company' class='select' id='company'";
		$string .= "style='width:190px;' ><option value=''> -- Pilih -- </option>";
		
		$sQuery = "	SELECT COMPANY_CODE, COMPANY_NAME from m_company WHERE COMPANY_FLAG = 1";
		             		
		$temp=$this->db->query($sQuery);
        $temp = $temp->result_array();
        $this->db->close();		
						
		foreach ( $temp as $row) {
			if( (isset($default))) {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\" selected>".$row['COMPANY_NAME']." </option>";
			} else {
				$string = $string." <option value=\"".$row['COMPANY_CODE']."\">".$row['COMPANY_NAME']." </option>";
			}
		}
		$string =$string. "</select>";
		return $string;
	}
	
	function js_agro_rawat(){
        $js = 'jQuery(document).ready(function(){
					$(function() { $("#vStartRawat").datepicker({dateFormat:"yy-mm-dd", 	
																		changeMonth: true,
																		changeYear: true}); });
					$(function() { $("#vEndRawat").datepicker({dateFormat:"yy-mm-dd", 	
																		changeMonth: true,
																		changeYear: true}); });
			}); 
			
			$("#progressbar").dialog({
							bgiframe: true, autoOpen: false,
							resizable: true, draggable: true,
							closeOnEscape:false, height: 160,
							width: 220, modal: true
				});
				
			function genRptRawat(){
				
				var postdata = {};
				var urls;
				var company = jQuery("#company").val();
				var jenisRpt = jQuery("#jns_kriteria").val();
				var KodeBlok = jQuery("#sKodeBlok").val();
				
				var datetglStart = jQuery("#vStartRawat").val();
				// var datetglStart = tglStart[2] + tglStart[1] + tglStart[0];
				
				var datetglEnd = jQuery("#vEndRawat").val();
				// var datetglEnd = tglEnd[2] + tglEnd[1] + tglEnd[0];
					
				urls = url+"rpt_agronomi_rawat/submitRpt/"+company+"/"+datetglStart+"/"+datetglEnd+"/"+KodeBlok+"/"+jenisRpt;
				
				
				if( company == "" ){
					alert("mohon pilih perusahaan");
				} else if( jenisRpt == "" ){
					alert("mohon pilih jenis file laporan");
				} else if( datetglStart == "" || datetglEnd == ""){
					alert("mohon isi rentang tanggal laporan");
				} else if( KodeBlok == "" ){
					alert("mohon kode blok");
				} else {
					if ( jenisRpt == "1" || jenisRpt == "3" ){
						//jQuery( "#reportformRealPP" ).dialog( "open" );
						jQuery("#rptRawatFrame").attr("src",urls); 
					} else {
						jQuery("#rptRawatFrame").attr("src",urls); 
					} 
				}
			}
			
			$(function () {
				  $("#sKodeBlok").autocomplete( url+"rpt_agronomi_rawat/getBlock/"+$("#company").val(), {
					  dataType: "ajax", width:150, multiple: false, limit:20, parse: function(data) {
									return $.map(eval(data), function(row) {
									return (typeof(row) == "object")
									  ? { data: row, value: row.res_id, result: row.res_id } 
									  : { data: row, value: "",result: ""};
						  });
					  }, formatItem: function(item) {
						   return (typeof(item) == "object")?item.res_dl :"";
					  }
					}).result(function(e, item) {					
							$("#sKodeBlok").val(item.res_id);
				});
		    });

			jQuery( "#subRptRawat" ).click(function() {
				genRptRawat();
			});
			';
        return $js;
    }
	
	function styling(){
		$DU = "";
        $DU .= "<style> .tbl_header { font-size: 12px; color:#000; border-top:1px solid; border-left:1px solid; } ";
        $DU .= ".tbl_th { font-size: 12px;color:#000; font-weight:bold; border-top:1px solid; border-bottom:1px solid; padding:3px; 
						border-right:1px solid } ";
        $DU .= ".tbl_td { font-size: 12px;color:#000;border-bottom:1px solid; padding:3px; border-right:1px solid } ";
        $DU .= ".tbl_2 { font-size: 13px;color:#000;} ";
        $DU .= ".content { font-size: 13px;color:#000; } 
				.content2 { font-size: 13px; font-weight:bold; color:#000; text-align:center; } </style>";
		return $DU;
	}
	
	function getBlock(){
        $cv = htmlentities($_REQUEST['q'],ENT_QUOTES,'UTF-8');
        $company = $this->uri->segment(3);
        $dataBlok = $this->model_rpt_agronomi->kodeBlok($cv, $company);
        
        $blok = array();
        foreach($dataBlok as $row)
            {
                $blok[] = '{res_id:"'.str_replace('"','\\"',htmlentities($row['BLOCKID'],ENT_QUOTES,'UTF-8')).
                '",res_name:"'.str_replace('"','\\"',htmlentities($row['BLOKDESC'],ENT_QUOTES,'UTF-8')).
                '",res_dl:"'.str_replace('"','\\"',htmlentities($row['BLOCKID'],ENT_QUOTES,'UTF-8'). " - " .
                    htmlentities($row['BLOKDESC'],ENT_QUOTES,'UTF-8')).'"}';
            }
              echo '['.implode(',',$blok).']'; exit; 
    }
	
	function submitRpt() {
		$company = $this->uri->segment(3);
		$vfrom = $this->uri->segment(4);
		$vto = $this->uri->segment(5);
		$block = $this->uri->segment(6);
		$jenis = $this->uri->segment(7);
		if($jenis == "1"){
			echo $this->prevRptRawat($company,$vfrom,$vto,$block);
		} else if ($jenis == "2"){
			$this->xlsRptRawat();
		} else if ($jenis == "3"){
			require_once(APPPATH . 'helpers/dompdf/dompdf_config.inc.php');
			ob_start();
			$dompdf = new DOMPDF();
			$dompdf->load_html($this->prevRptRawat($company,$vfrom,$vto,$block));
			$dompdf->render();
			$dompdf->output();
			$canvas = $dompdf->get_canvas();
			$font = Font_Metrics::get_font("helvetica", "bold");
			$canvas->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
			$dompdf->stream("historikal_rawat".$company."_".$block.".pdf", array("Attachment" => 0));
		}
	}
	
	function prevRptRawat($company,$vfrom,$vto,$block){
		$rpt = $this->styling();
		$dataRpt = $this->model_rpt_agronomi->getData($company, $vfrom, $vto, $block);
		
		$rpt .= '<table width="100%" border="0">
  					<tr><td colspan="2">Laporan Data Historikal Rawat</td></tr>
  					<tr><td width="96">Perusahaan</td> <td width="405">'.$company.'</td></tr>
  					<tr><td>Blok</td><td>'.$block.'</td></tr><tr>
					<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_header">
					  <tr>
						<td class="tbl_th" style="border-left:1px solid;" align="center">No.</td>
						<td class="tbl_th" align="center">Tanggal Transaksi</td>
						<td class="tbl_th" align="center">Kode Aktivitas</td>
						<td class="tbl_th" align="center">Deskripsi Aktivitas</td>
						<td class="tbl_th" align="center">Blok</td>
						<td class="tbl_th" align="center">Tahun Tanam</td>
						<td class="tbl_th" align="center">Jumlah HK</td>
						<td class="tbl_th" align="center">Hasil Kerja</td>
						<td class="tbl_th" align="center">Satuan</td>
						<td class="tbl_th" align="center">Realisasi Biaya</td>
					  </tr>';
	   $no = 1;
	  foreach ( $dataRpt as $row ){
		  $rpt .= '<tr>
				<td class="tbl_td" style="border-left:1px solid;" align="center">'.$no.'</td>
				<td class="tbl_td" align="center">'.$row['ACTIVITYDATE'].'</td>
				<td class="tbl_td" align="center">'.$row['ACTIVITY_CODE'].'</td>
				<td class="tbl_td" align="center">'.$row['COA_DESCRIPTION'].'</td>
				<td class="tbl_td" align="center">'.$row['BLOCK'].'</td>
				<td class="tbl_td" align="center">'.$row['TAHUNTANAM'].'</td>
				<td class="tbl_td" align="right">'.number_format($row['HK'],2).'</td>
				<td class="tbl_td" align="right">'.number_format($row['HASIL_KERJA'],2).'</td>
				<td class="tbl_td" align="center">'.$row['SATUAN'].'</td>
				<td class="tbl_td" align="right">'.number_format($row['BIAYA'],2).'</td>
			  </tr>';
	  $no++;
	  }
      
      
   	 	$rpt .= '</table></td>
			  </tr>
			</table>';
		
		return $rpt;
	}
	
	function xlsRptRawat(){
		$this->load->library('excel');
		//$this->load->library('PHPExcel/IOFactory');

			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('rptRetribusiUmumPerMinggu');
			
			$this->excel->getActiveSheet()->setCellValue('A1', 'DINAS PENDAPATAN');
			$this->excel->getActiveSheet()->setCellValue('A2', 'TANGERANG SELATAN');
			$this->excel->getActiveSheet()->setCellValue('A3', 'REKAPITULASI PENGEMBALIAN KARCIS RETRIBUSI TANGERANG SELATAN');
			$this->excel->getActiveSheet()->setCellValue('A4', " PERIODE ");
			$this->excel->getActiveSheet()->getStyle('A1:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->mergeCells('A1:R1');
			$this->excel->getActiveSheet()->mergeCells('A2:R2');
			$this->excel->getActiveSheet()->mergeCells('A3:R3');
			$this->excel->getActiveSheet()->mergeCells('A4:R4');	
					
			$this->excel->getActiveSheet()->setCellValue('A6', 'BULAN');
			$this->excel->getActiveSheet()->setCellValue('B6', 'SALDO BLN INI ');
			$this->excel->getActiveSheet()->setCellValue('C6', 'TERIMA');
			$this->excel->getActiveSheet()->setCellValue('C7', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('D7', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('E6', 'PENYETORAN');
			$this->excel->getActiveSheet()->setCellValue('E7', 'MINGGU 1');
			$this->excel->getActiveSheet()->setCellValue('E8', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('F8', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('G7', 'MINGGU 2');
			$this->excel->getActiveSheet()->setCellValue('G8', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('H8', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('I7', 'MINGGU 3');
			$this->excel->getActiveSheet()->setCellValue('I8', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('J8', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('K7', 'MINGGU 4');
			$this->excel->getActiveSheet()->setCellValue('K8', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('L8', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('M7', 'MINGGU 5');
			$this->excel->getActiveSheet()->setCellValue('M8', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('N8', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('O6', 'TOTAL SETORAN');
			$this->excel->getActiveSheet()->setCellValue('O7', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('P7', 'NILAI');
			$this->excel->getActiveSheet()->setCellValue('Q6', 'SISA');
			$this->excel->getActiveSheet()->setCellValue('Q7', 'LBR');
			$this->excel->getActiveSheet()->setCellValue('R7', 'NILAI');
			
			
			
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			
			$rowXls = 9;
			/* 
			
			
			$endCol = "R";
			$data_row = $this->m_report_realisasi->rptRetUmumPerMinggu($UnitKd,$tahun);	
			foreach ($data_row as $row){
				$this->excel->getActiveSheet()->setCellValue('A'.$rowXls, $row['kdBulan']);
				$this->excel->getActiveSheet()->setCellValue('B'.$rowXls, $row['NmBulan']);
				$this->excel->getActiveSheet()->setCellValue('C'.$rowXls, number_format($row['Qty']));
				$this->excel->getActiveSheet()->setCellValue('D'.$rowXls, number_format($row['Amount']));
				$this->excel->getActiveSheet()->setCellValue('E'.$rowXls, number_format($row['M1Qty']));
				$this->excel->getActiveSheet()->setCellValue('F'.$rowXls, number_format($row['M1Amt']));
				$this->excel->getActiveSheet()->setCellValue('G'.$rowXls, number_format($row['M2Qty']));
				$this->excel->getActiveSheet()->setCellValue('H'.$rowXls, number_format($row['M2Amt']));
				$this->excel->getActiveSheet()->setCellValue('I'.$rowXls, number_format($row['M3Qty']));
				$this->excel->getActiveSheet()->setCellValue('J'.$rowXls, number_format($row['M3Amt']));
				$this->excel->getActiveSheet()->setCellValue('K'.$rowXls, number_format($row['M4Qty']));
				$this->excel->getActiveSheet()->setCellValue('L'.$rowXls, number_format($row['M4Amt']));
				$this->excel->getActiveSheet()->setCellValue('M'.$rowXls, number_format($row['M5Qty']));
				$this->excel->getActiveSheet()->setCellValue('N'.$rowXls, number_format($row['M5Amt']));
				$ttlLbr = $row['M1Qty'] + $row['M2Qty'] + $row['M3Qty'] + $row['M4Qty'] + $row['M5Qty'];
				$ttlAmount = $row['M1Amt'] + $row['M2Amt'] + $row['M3Amt'] + $row['M4Amt'] + $row['M5Amt'];
				$this->excel->getActiveSheet()->setCellValue('O'.$rowXls, number_format($ttlLbr));
				$this->excel->getActiveSheet()->setCellValue('P'.$rowXls, number_format($ttlAmount));
				$this->excel->getActiveSheet()->setCellValue('Q'.$rowXls, number_format($row['Sld']));
				$this->excel->getActiveSheet()->setCellValue('R'.$rowXls, number_format($row['SldAmt']));
				$rowXls++;
			}
			for($j=6; $j<$rowXls; $j++){
				for($i="A"; $i<="R"; $i++){
					//echo $i.$j;
					$this->excel->getActiveSheet()->getStyle($i.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					//$this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
				}
				//echo "<br/>";
			}
			*/
			//$endCol = $rowXls;
			$this->excel->getActiveSheet()->getStyle('A6:R8')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1:R8')->getFont()->setBold(true);
					
			$filename='REKAPITULASI PENGEMBALIAN KARCIS RETRIBUSI (UMUM) PUSKESMAS.xls'; //save our workbook as this file name
			
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5'); 
			ob_end_clean();
			$objWriter->save('php://output');	
			//$this->excel->disconnectWorksheets();
    		//unset($excel);
	
	}
	
}

?>