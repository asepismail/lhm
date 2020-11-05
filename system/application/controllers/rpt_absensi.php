<?
if (!defined('BASEPATH')) exit('No direct script access allowed');

class rpt_absensi extends Controller 
{
 	function rpt_absensi ()
	{
		parent::Controller();	
		/*modul yang di load halaman gad*/
		$this->load->model( 'model_rpt_absensi' ); 
		$this->load->model( 'model_m_gang_activity_detail' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_absensi";
        
		$this->load->helper('form');
		$this->load->helper('language');
		$this->load->database(); 
		$this->load->helper('url');
		$this->load->helper('object2array');
        $this->load->library('form_validation');
		$this->load->library('global_func');
		$this->load->library('session');
		$this->load->plugin('to_excel');
		require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
        require_once(APPPATH . 'libraries/table_def.inc');
	}

    function index()
    {
		
		$view = "info_rpt_absensi";
		$data = array();
		$data['judul_header'] = "Laporan Absensi Karyawan";
		$data['js'] = $this->js_absensi();	
		
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['GANG_CODE'] = $this->global_func->dropdownlist2("GANG_CODE","m_gang","GANG_CODE","GANG_CODE","COMPANY_CODE = '".$this->session->userdata('DCOMPANY')."'",NULL, NULL,'',"select", TRUE);
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 

		if ($data['login_id'] == TRUE){
			//$this->load->view('info_rpt_absensi', $data);
			show($view, $data);
		} else {
			redirect('login');
		}
		

    } 
	
	function delete_elhm()
	{
		$company = $this->session->userdata('DCOMPANY');
			
		$gc = $this->input->post( 'GANG_CODE' );
		$nik = $this->input->post( 'NIK' );
		$tgl = $this->input->post( 'TGL' );
		$id = $this->input->post( 'ID' );
		
		$this->model_rpt_absensi->delete_elhm($id,$gc,$nik,$tgl,$company);
	}
	
	function js_absensi(){
		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var gc = $('#GANG_CODE').val();
			var jns_laporan = $('#jns_laporan').val();
			var typeAbsensi = $('#jns_absensi').val();
			if ( jns_laporan == 'html'){
				urls = url + 'rpt_absensi/prev_absensi/' + gc + '/' + periode+ '/' + typeAbsensi; 
				$('#frame').attr('src',urls); 
			} else if ( jns_laporan == 'excell'){
				urls = url + 'rpt_absensi/xls_absensi/' + gc + '/' + periode+ '/' + typeAbsensi;
                $.download(urls,'');
			} else if ( jns_laporan == 'pdf'){
				urls = url + 'rpt_absensi/pdf_absensi/' + gc + '/' + periode+ '/' + typeAbsensi;
				$('#frame').attr('src',urls); 
			}
		});";
		return $js; 
	}
	
	function prev_absensi(){
		$gc = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$type = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		if($type == "hk"){
			echo $this->absensi($gc, $periode, $company);
		} else if ($type == "tp"){
			echo $this->absensiType($gc, $periode, $company);
		}
	}
	
	function xls_absensi(){
		$gc = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$type = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		if($type == "hk"){
			echo $this->absensi_xls($gc, $periode, $company);
		} else if ($type == "tp"){
			echo $this->absensiType_xls($gc, $periode, $company);
		}
	}
	
	function pdf_absensi(){
		$gc = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$type = $this->uri->segment(5);
		$company = $this->session->userdata('DCOMPANY');
		
		if($type == "hk"){
			echo $this->absen_pdf($gc, $periode, $company);
		} else if ($type == "tp"){
			echo $this->absensiType_pdf($gc, $periode, $company);
		}
	}
	
	function cek_employee_lhm(){
		$nik = $this->uri->segment(3);
		$periode = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
	   	echo json_encode($this->model_rpt_absensi->employee_lhm($nik, $periode, $company));	
	}
		
	function absensi($gc, $periode, $company){
				
		$data_absen = $this->model_rpt_absensi->create_absensi($company, $gc, $periode);
		
		$absen = "";
		$array = array();
		
		$libur = "";				
				
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);
		if($bulan == '01'){ $bulan = "Januari"; $hari = 31; } 
		else if($bulan == '02'){ $bulan = "Februari"; $hari = 29;  } 
		else if($bulan == '03'){ $bulan = "Maret";  $hari = 31; } 
		else if($bulan == '04'){ $bulan = "April";  $hari = 30; } 
		else if($bulan == '05'){ $bulan = "Mei";  $hari = 31; } 
		else if($bulan == '06'){ $bulan = "Juni";  $hari = 30; } 
		else if($bulan == '07'){ $bulan = "Juli";  $hari = 31; } 
		else if($bulan == '08'){ $bulan = "Agustus";  $hari = 31; } 
		else if($bulan == '09'){ $bulan = "September";  $hari = 30; } 
		else if($bulan == '10'){ $bulan = "Oktober";  $hari = 31; } 
		else if($bulan == '11'){ $bulan = "Nopember";  $hari = 30; } 
		else if($bulan == '12'){ $bulan = "Desember";  $hari = 31; }
		
		$bln = strtotime($periode.$hari);
		//$bulan = date("F Y", $bln);
			
		$table = "<table cellpadding='0' cellspacing='0' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' width='95%'>";
		
		$table .= "<tr><td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> No. </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> Kemandoran </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> NIK </td>
		<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> Nama </td>
		<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> Status </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> KJ </td>";
				
		$table .= "<td colspan = '".$hari."' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'><center>" . strtoupper($bulan)  . " " . $tahun . "</center></td></tr>";		
		$table .= "<tr>";	
		for($i=1; $i<=$hari; $i++){
			$table .= "<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
		}
			
		$table .= "</tr>";
			
		$no = 1;
		foreach($data_absen as $row)
		{
			$table .= " <tr>
			<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$no." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['GANG_CODE']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['EMPLOYEE_CODE']." </td> ";
			$tes = "elhm(".$row['EMPLOYEE_CODE'].")";
			$table .= '<td  align="left" style="font-size: 12px;cursor:pointer;color:#678197;border-bottom:1px solid; border-right:1px solid"> &nbsp;&nbsp;<a onclick="elhm(\''.$row['EMPLOYEE_CODE'] .'\',\''. $periode .'\')">'.$row['NAMA'].'</a></td> ';
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['TYPE_KARYAWAN']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".round(number_format($row['KJ']))." </td> ";
			
			$array = explode(",",$row['ABSEN']);
			//echo count($tipe_absen);
			
			for($i=1; $i<=$hari; $i++){
			$cal_flag = "";
			$tgl_lengkap = $periode.$i;
			$data_calendar = $this->model_rpt_absensi->cek_hari($tgl_lengkap, $company);
			foreach($data_calendar as $row_cal){
				$cal_flag .= $row_cal['CAL_FLAG'];
				if($row_cal['CAL_FLAG'] == 'LN'){
					$libur = "<strong><span style='background-color: #ff0000; font-size: 12px;color:#ffffff;'>".$row_cal['CAL_TGL']."</span></strong> : <span style='font-size: 12px;color:#678197;'>".$row_cal['CAL_KETERANGAN']."</span><br/>";
				}
			}
			
			if($cal_flag != 'KJ'){
				if ($cal_flag == 'LN') {
				$table .= "<td width='2%'  align='center' style='background-color: #ff0000; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
				} else {
				$table .= "<td width='2%'  align='center' style='background-color: #00ffff; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
				}

			} else {
				$table .= "<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
			}
			
			$a = "";
	
			for($j=0; $j<count($array); $j++){
									
					$absennya = explode(":", $array[$j] );				
					
					$tgl = $absennya[0];
					
					if(isset($absennya[1])){
						$ta = round($absennya[1]);
					} else {
						$ta = "-";	
					}
										
					if($tgl == $i){
						$a .= $ta;				
					} else {
						$a .= "";
					}
					
			}
			
			if($a != ""){
						if($a > 1){
							$table .= "<span style='background-color: #FFFF00'>".$a."</span>";
						} else {
							$table .= $a;
						}
					} else {
						$table .= "-";
					}	
			$table .= "</td>";	
										
			}
				$table .= "</tr>";
			$no++;
			//echo "<br/>";	
				
		}
		
		$table .= "</table><br />";
		$table .= $libur;
		return $table;
	} 
	
	
	function absensi_xls($gc, $periode, $company){
		
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		
		$obj =& get_instance();
		if ($gc == 'all'){
			$data_absen = $this->model_rpt_absensi->create_absensi_all($company, $periode);
		} else {
			$data_absen = $this->model_rpt_absensi->create_absensi($company, $gc, $periode);
		}
		$absen = "";
		$array = array();
				
		$hari = 31;
		$bln = strtotime($periode.$hari);
		$bulan = date("F Y", $bln);
		
		$headers .= "No \t";
		$headers .= "Kemandoran \t";
		$headers .= "NIK \t";
		$headers .= "Nama \t";
		$headers .= "Type Karyawan \t";
		$headers .= "KJ \t";
		
		for($i=1; $i<=$hari; $i++){
			$headers .= $i."\t";
		}

		$no = 1;

		foreach($data_absen as $row){
			$line = '';			
			$line .= str_replace('"', '""',$no)."\t";
			$line .= str_replace('"', '""',$row['GANG_CODE'])."\t";
			$line .= str_replace('"', '""',$row['EMPLOYEE_CODE'])."\t";
			$line .= str_replace('"', '""',$row['NAMA'])."\t";
			$line .= str_replace('"', '""',$row['TYPE_KARYAWAN'])."\t";
			$line .= str_replace('"', '""',$row['KJ'])."\t";
					
			$array = explode(",",$row['ABSEN']);
			
			
			for($i=1; $i<=31; $i++){
				$a = "";
				for($j=0; $j<count($array); $j++){
					$absennya = explode(":", $array[$j] );				
					$tgl = $absennya[0];
					if(isset($absennya[1])){
						$ta = $absennya[1];
					} else {
						$ta = "";	
					}
								
					if($tgl == $i){
						$a .= $ta;						
					} else {
						$a .= "";
					}
				 }
					
				 if($a != ""){
						$line .= $a."\t";
				} else {
						$line .= "- \t";
				}
			}
			$no++;
			$data .= trim($line)."\n";
		}
		
		$data = str_replace("\r","",$data);
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=absensi_".$gc."_".$periode.".xls");
        echo "$headers\n$data";  
	}
	
	function absen_pdf($gc, $periode, $company){
		
		$pdf = new pdf_usage();        
    	$pdf->Open();
    	$pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(5,13,20);
    	$pdf->AddPage('L', 'LEGAL');
    	$pdf->AliasNbPages(); 
        
    	$pdf->SetStyle("s1","arial","",9,"");
    	$pdf->SetStyle("s2","arial","",8,"");
    	$pdf->SetStyle("s3","arial","",10,"");
    
    	if ($gc == 'all'){
			$data_absen = $this->model_rpt_absensi->create_absensi_all($company, $periode);
		} else {
			$data_absen = $this->model_rpt_absensi->create_absensi($company, $gc, $periode);
		}
		
		$absen = "";
		$array = array();
		
		$tahap = "TAHAP I";
		$hari = 31;
		$bln = strtotime($periode.$hari);
		$bulanr = strtoupper( $this->bln_to_periode(substr($periode,4,2) ) ). " " . substr($periode,0,4) . " " . $tahap ;
		$bulan = date("F Y", $bln);
    	
		//default text color
		$pdf->SetTextColor(118, 0, 3);
		$pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>ABSENSI KARYAWAN UPAH</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>KEMANDORAN ". strtoupper($gc). "</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>PERIODE ". $bulanr. "</s3>", 0);
		
		//load the table default definitions DEFAULT!!!
    	require_once(APPPATH . 'libraries/rptPDF_def.inc');
    	$columns = 37; //number of Columns
		$pdf->tbInitialize($columns, true, true);
    
		$pdf->tbSetTableType($table_default_table_type);
		$aSimpleHeader = array();
    
    $header = array('NO','MANDOR','NIK','NAMA','TYPE KARYAWAN','HK',$bulanr,'','','','','', '','','','','','','','','','','','','','','','','','','','','','','','','');
    $header2 = array('','','','','','','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','','');
		for($i=0; $i < $columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[$i]['WIDTH'] = 7.5;
			$aSimpleHeader[1]['WIDTH'] = 15;
			$aSimpleHeader[2]['WIDTH'] = 17;
			$aSimpleHeader[3]['WIDTH'] = 35;
			$aSimpleHeader[4]['WIDTH'] = 18;
			$aSimpleHeader[5]['WIDTH'] = 10;
			$aSimpleHeader2[$i]['LN_SIZE'] = 2;
			
			if ($i >= 0 && $i < 6){ $aSimpleHeader[$i]['ROWSPAN'] = 2;  } 
			else if ($i >= 6){ $aSimpleHeader[$i]['COLSPAN'] = 31; } 
			
			$aSimpleHeader2[$i] = $table_default_header_type;
			$aSimpleHeader2[$i]['TEXT'] = $header2[$i];
			
			$aSimpleHeader2[$i]['WIDTH'] = 7.5;
			$aSimpleHeader2[1]['WIDTH'] = 15;
			$aSimpleHeader2[2]['WIDTH'] = 17;
			$aSimpleHeader2[3]['WIDTH'] = 35;
			$aSimpleHeader2[4]['WIDTH'] = 18;
			$aSimpleHeader2[5]['WIDTH'] = 10;			
			$aSimpleHeader2[$i]['LN_SIZE'] = 5;
		}
    
		$pdf->tbSetHeaderType($aSimpleHeader);
		$pdf->tbSetHeaderType($aSimpleHeader2);
		//Draw the Header
		$pdf->tbDrawHeader();

    //Table Data Settings
    	$aDataType = Array();
    	for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
    	$pdf->tbSetDataType($aDataType);
            
			$no = 1;    
			foreach($data_absen as $row){				
				$data = Array();
				$data[0]['TEXT'] = $no;
				$data[1]['TEXT'] = $row['GANG_CODE'];
				$data[2]['TEXT'] = $row['EMPLOYEE_CODE'];
				$data[2]['T_ALIGN'] = 'C';
				$data[3]['TEXT'] = $row['NAMA'];
				$data[3]['T_ALIGN'] = 'L';
				$data[4]['TEXT'] = $row['TYPE_KARYAWAN'];        
				$data[5]['TEXT'] = $row['KJ'];
				$data[5]['T_ALIGN'] = 'R';
				$array = explode(",",$row['ABSEN']);
				
				$col = 6;
					for($i=1; $i<=$hari; $i++){
						$tgl_lengkap = $periode.$i;
						$a = "";
								for($j=0; $j<count($array); $j++){
										$absennya = explode(":", $array[$j] );				
										$tgl = $absennya[0];
										if(isset($absennya[1])){
											$ta = round($absennya[1]);
										} else {
											$ta = "-";	
										}
	
										if($tgl == $i){
											$a .= $ta;				
										} else {
											$a .= "";
										}
								}
								
								if($a != ""){
									$data[$col]['TEXT'] = $a;
								} else {
								 	$data[$col]['TEXT'] = '-';
								}	
								$col++;
					  }
				$no++;
				
				$pdf->tbDrawData($data);
			}
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->Ln(15.5);
		//require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
    	$pdf->Output();
    }
	
	/* modified : Asep #2013-12-12
	   add report attendance by type of attendance */
	function absensiType($gc, $periode, $company){
		
		$data_absen = $this->model_rpt_absensi->create_absensi_bytype($company, $gc, $periode);
		
		$absen = "";
		$array = array();
		
		$libur = "";				
				
		$bulan = substr($periode,-2);
		$tahun = substr($periode,0,4);

		$blnToPeriode= $this->bln_to_periode($bulan);
		
		$hari = $blnToPeriode[1];
		$bulan = $blnToPeriode[0];
		
		$table = "<table cellpadding='0' cellspacing='0' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' width='100%'>";
		
		$table .= "<tr><td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> No. </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> Kemandoran </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> NIK </td>
		<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> Nama </td>
		<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' width='2%'> Status </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> KJ </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> S1 </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> CT </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> PH </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> P1 </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> M </td>";
		$table .= "<td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> H2 </td>";
	
		
		$table .= "<td colspan = '".$hari."' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'><center>" . strtoupper($bulan)  . " " . $tahun . "</center></td></tr>";		
		$table .= "<tr>";	
		for($i=1; $i<=$hari; $i++){
			$table .= "<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
		}
			
		$table .= "</tr>";
			
		$no = 1;
		foreach($data_absen as $row)
		{
			$table .= " <tr>
			<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$no." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['GANG_CODE']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['EMPLOYEE_CODE']." </td> ";
			$tes = "elhm(".$row['EMPLOYEE_CODE'].")";
			$table .= '<td  align="left" style="font-size: 12px;cursor:pointer;color:#678197;border-bottom:1px solid; border-right:1px solid"> &nbsp;&nbsp;<a onclick="elhm(\''.$row['EMPLOYEE_CODE'] .'\',\''. $periode .'\')">'.$row['NAMA'].'</a></td> ';
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['TYPE_KARYAWAN']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".round(number_format($row['KJ']))." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['S1']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['CT']." </td> ";			
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['PH']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['P1']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['M']." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['H2']." </td> ";
			
			
			$array = explode(",",$row['ABSEN']);
			//echo count($tipe_absen);
			//var_dump($row['ABSEN']);
			for($i=1; $i<=$hari; $i++){
				$cal_flag = "";
				$tgl_lengkap = $periode.$i;
				$data_calendar = $this->model_rpt_absensi->cek_hari($tgl_lengkap, $company);
				foreach($data_calendar as $row_cal){
					$cal_flag .= $row_cal['CAL_FLAG'];
					if($row_cal['CAL_FLAG'] == 'LN'){
						$libur = "<strong><span style='background-color: #ff0000; font-size: 12px;color:#ffffff;'>".$row_cal['CAL_TGL']."</span></strong> : <span style='font-size: 12px;color:#678197;'>".$row_cal['CAL_KETERANGAN']."</span><br/>";
					}
				}
			
				if($cal_flag != 'KJ'){
					if ($cal_flag == 'LN') {
					$table .= "<td width='2%'  align='center' style='background-color: #ff0000; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
					} else {
					$table .= "<td width='2%'  align='center' style='background-color: #00ffff; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
					}	
				} else {
					$table .= "<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
				}
			
				$a = "";
	
				for($j=0; $j<count($array); $j++){
									
					$absennya = explode(":", $array[$j] );				
					
					$tgl = $absennya[0];
					
					if(isset($absennya[1])){
						$ta = ($absennya[1]);
					} else {
						$ta = "-";	
					}
										
					if($tgl == $i){
						$a .= $ta;				
					} else {
						$a .= "";
					}
					
				}
			
				if($a != ""){
						if($a > 1){
							$table .= "<span style='background-color: #FFFF00'>".$a."</span>";
						} else {
							$table .= $a;
						}
				} else {
						$table .= "-";
				}	
				$table .= "</td>";	
							
			}//end for			
				
				$table .= "</tr>";
			if ($row['ABSEN_NA']<>NULL){
				//start: asep nyoba
				$array = explode(",",$row['ABSEN_NA']);
				$table .="
		<td colspan='5' width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>";
				$table .= "<td  width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".round(number_format($row['NA']))." </td> ";
			$table .="<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>
		<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>
		<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>
		<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>
		<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>
		<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;</td>";
				for($i=1; $i<=$hari; $i++){
					$cal_flag = "";
					$tgl_lengkap = $periode.$i;
					$data_calendar = $this->model_rpt_absensi->cek_hari($tgl_lengkap, $company);
					foreach($data_calendar as $row_cal){
						$cal_flag .= $row_cal['CAL_FLAG'];
						if($row_cal['CAL_FLAG'] == 'LN'){
							$libur = "<strong><span style='background-color: #ff0000; font-size: 12px;color:#ffffff;'>".$row_cal['CAL_TGL']."</span></strong> : <span style='font-size: 12px;color:#678197;'>".$row_cal['CAL_KETERANGAN']."</span><br/>";
						}
					}
				
					if($cal_flag != 'KJ'){
						if ($cal_flag == 'LN') {
						$table .= "<td width='2%'  align='center' style='background-color: #ff0000; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
						} else {
						$table .= "<td width='2%'  align='center' style='background-color: #00ffff; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
						}
		
					} else {
						$table .= "<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>";
					}
				
					$a = "";
		
					for($j=0; $j<count($array); $j++){
										
						$absennya = explode(":", $array[$j] );				
						
						$tgl = $absennya[0];
						
						if(isset($absennya[1])){
							$ta = ($absennya[1]);
						} else {
							$ta = "-";	
						}
											
						if($tgl == $i){
							$a .= $ta;				
						} else {
							$a .= "";
						}
						
					}
				
					if($a != ""){
							if($a > 1){
								$table .= "<span style='background-color: #FFFF00'>".$a."</span>";
							} else {
								$table .= $a;
							}
					} else {
							$table .= "-";
					}	
					$table .= "</td>";	
								
				}//end for	
				$table .= "</tr>";
				//end: asep nyoba
			}
			$no++;
			//echo "<br/>";					
		}
		
		$table .= "</table><br />";
		$table .= $libur;
		return $table;
	} 
	
	function absensiType_xls($gc, $periode, $company){
		
		$headers = ''; // just creating the var for field headers to append to below
    	$data = ''; // just creating the var for field data to append to below
		
		$obj =& get_instance();
		if ($gc == 'all'){
			$data_absen = $this->model_rpt_absensi->create_absensi_bytype($company, $periode);
		} else {
			$data_absen = $this->model_rpt_absensi->create_absensi_bytype($company, $gc, $periode);
		}
		$absen = "";
		$array = array();
				
		$hari = 31;
		$bln = strtotime($periode.$hari);
		$bulan = date("F Y", $bln);
		
		$headers .= "No \t";
		$headers .= "Kemandoran \t";
		$headers .= "NIK \t";
		$headers .= "Nama \t";
		$headers .= "Type Karyawan \t";
		$headers .= "KJ \t";
		$headers .= "S1 \t";
		$headers .= "CT \t";
		$headers .= "PH \t";
		$headers .= "P1 \t";
		$headers .= "M \t";
		$headers .= "H2 \t";
		
		for($i=1; $i<=$hari; $i++){
			$headers .= $i."\t";
		}

		$no = 1;

		foreach($data_absen as $row){
			$line = '';			
			$line .= str_replace('"', '""',$no)."\t";
			$line .= str_replace('"', '""',$row['GANG_CODE'])."\t";
			$line .= str_replace('"', '""',$row['EMPLOYEE_CODE'])."\t";
			$line .= str_replace('"', '""',$row['NAMA'])."\t";
			$line .= str_replace('"', '""',$row['TYPE_KARYAWAN'])."\t";
			$line .= str_replace('"', '""',$row['KJ'])."\t";
			$line .= str_replace('"', '""',$row['S1'])."\t";
			$line .= str_replace('"', '""',$row['CT'])."\t";
			$line .= str_replace('"', '""',$row['PH'])."\t";
			$line .= str_replace('"', '""',$row['P1'])."\t";
			$line .= str_replace('"', '""',$row['M'])."\t";
			$line .= str_replace('"', '""',$row['H2'])."\t";
					
			$array = explode(",",$row['ABSEN']);
			
			
			for($i=1; $i<=31; $i++){
				$a = "";
				for($j=0; $j<count($array); $j++){
					$absennya = explode(":", $array[$j] );				
					$tgl = $absennya[0];
					if(isset($absennya[1])){
						$ta = $absennya[1];
					} else {
						$ta = "";	
					}
								
					if($tgl == $i){
						$a .= $ta;						
					} else {
						$a .= "";
					}
				 }
					
				 if($a != ""){
						$line .= $a."\t";
				} else {
						$line .= "- \t";
				}
			}
			
			if ($row['ABSEN_NA']<>NULL){
				$array = explode(",",$row['ABSEN_NA']);
				$line .= "\n";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= str_replace('"', '""',$row['NA'])."\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				$line .= "\t";
				for($i=1; $i<=31; $i++){
					
					$a = "";
					for($j=0; $j<count($array); $j++){
						$absennya = explode(":", $array[$j] );				
						$tgl = $absennya[0];
						if(isset($absennya[1])){
							$ta = $absennya[1];
						} else {
							$ta = "";	
						}
									
						if($tgl == $i){
							$a .= $ta;						
						} else {
							$a .= "";
						}
					 }
						
					 if($a != ""){
							$line .= $a."\t";
					 }else {
							$line .= "- \t";
					 }					
				} //end for NA
			}//end if
			$no++;
			$data .= trim($line)."\n";
		}
		
		$data = str_replace("\r","",$data);
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=absensi_".$gc."_".$periode.".xls");
        echo "$headers\n$data";  
	}
	
	function absensiType_pdf($gc, $periode, $company){
		
		$pdf = new pdf_usage();        
    	$pdf->Open();
    	$pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(5,13,20);
    	$pdf->AddPage('L', 'LEGAL');
    	$pdf->AliasNbPages(); 
        
    	$pdf->SetStyle("s1","arial","",9,"");
    	$pdf->SetStyle("s2","arial","",8,"");
    	$pdf->SetStyle("s3","arial","",10,"");
    
    	if ($gc == 'all'){
			$data_absen = $this->model_rpt_absensi->create_absensi_bytype_all($company, $periode);
		} else {
			$data_absen = $this->model_rpt_absensi->create_absensi_bytype($company, $gc, $periode);
		}
		
		$absen = "";
		$array = array();
		
		$tahap = "TAHAP I";
		$hari = 31;
		$bln = strtotime($periode.$hari);
		$bulanr = strtoupper( $this->bln_to_periode(substr($periode,4,2) ) ). " " . substr($periode,0,4) . " " . $tahap ;
		$bulan = date("F Y", $bln);
    	
		//default text color
		$pdf->SetTextColor(118, 0, 3);
		$pdf->MultiCellTag(200, 5, "<s3>PT. ". strtoupper( $this->session->userdata('DCOMPANY_NAME') ) ."</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>ABSENSI KARYAWAN UPAH</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>KEMANDORAN ". strtoupper($gc). "</s3>", 0);
		$pdf->MultiCellTag(200, 5, "<s3>PERIODE ". $bulanr. "</s3>", 0);
		
		//load the table default definitions DEFAULT!!!
    	require_once(APPPATH . 'libraries/rptPDF_def.inc');
    	$columns = 37; //number of Columns
		$pdf->tbInitialize($columns, true, true);
    
		$pdf->tbSetTableType($table_default_table_type);
		$aSimpleHeader = array();
    
    $header = array('NO','MANDOR','NIK','NAMA','TYPE KARYAWAN','HK',$bulanr,'','','','','', '','','','','','','','','','','','','','','','','','','','','','','','','');
    $header2 = array('','','','','','','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','','');
		for($i=0; $i < $columns; $i++) {
			$aSimpleHeader[$i] = $table_default_header_type;
			$aSimpleHeader[$i]['TEXT'] = $header[$i];
			$aSimpleHeader[$i]['WIDTH'] = 7.5;
			$aSimpleHeader[1]['WIDTH'] = 15;
			$aSimpleHeader[2]['WIDTH'] = 17;
			$aSimpleHeader[3]['WIDTH'] = 35;
			$aSimpleHeader[4]['WIDTH'] = 18;
			$aSimpleHeader[5]['WIDTH'] = 10;
			$aSimpleHeader2[$i]['LN_SIZE'] = 2;
			
			if ($i >= 0 && $i < 6){ $aSimpleHeader[$i]['ROWSPAN'] = 2;  } 
			else if ($i >= 6){ $aSimpleHeader[$i]['COLSPAN'] = 31; } 
			
			$aSimpleHeader2[$i] = $table_default_header_type;
			$aSimpleHeader2[$i]['TEXT'] = $header2[$i];
			
			$aSimpleHeader2[$i]['WIDTH'] = 7.5;
			$aSimpleHeader2[1]['WIDTH'] = 15;
			$aSimpleHeader2[2]['WIDTH'] = 17;
			$aSimpleHeader2[3]['WIDTH'] = 35;
			$aSimpleHeader2[4]['WIDTH'] = 18;
			$aSimpleHeader2[5]['WIDTH'] = 10;			
			$aSimpleHeader2[$i]['LN_SIZE'] = 5;
		}
    
		$pdf->tbSetHeaderType($aSimpleHeader);
		$pdf->tbSetHeaderType($aSimpleHeader2);
		//Draw the Header
		$pdf->tbDrawHeader();

    //Table Data Settings
    	$aDataType = Array();
    	for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
    	$pdf->tbSetDataType($aDataType);
            
			$no = 1;    
			foreach($data_absen as $row){				
				$data = Array();
				$data[0]['TEXT'] = $no;
				$data[1]['TEXT'] = $row['GANG_CODE'];
				$data[2]['TEXT'] = $row['EMPLOYEE_CODE'];
				$data[2]['T_ALIGN'] = 'C';
				$data[3]['TEXT'] = $row['NAMA'];
				$data[3]['T_ALIGN'] = 'L';
				$data[4]['TEXT'] = $row['TYPE_KARYAWAN'];        
				$data[5]['TEXT'] = $row['KJ'];
				$data[5]['T_ALIGN'] = 'R';
				$array = explode(",",$row['ABSEN']);
				
				$col = 6;
					for($i=1; $i<=$hari; $i++){
						$tgl_lengkap = $periode.$i;
						$a = "";
								for($j=0; $j<count($array); $j++){
										$absennya = explode(":", $array[$j] );				
										$tgl = $absennya[0];
										if(isset($absennya[1])){
											$ta = round($absennya[1]);
										} else {
											$ta = "-";	
										}
	
										if($tgl == $i){
											$a .= $ta;				
										} else {
											$a .= "";
										}
								}
								
								if($a != ""){
									$data[$col]['TEXT'] = $a;
								} else {
								 	$data[$col]['TEXT'] = '-';
								}	
								$col++;
					  }
				$no++;
				
				$pdf->tbDrawData($data);
			}
		$pdf->tbOuputData();
		$pdf->tbDrawBorder();
		
		$pdf->Ln(15.5);
		//require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
    	$pdf->Output();
    }
	/* end att by type */
	
	
	function bln_to_periode($bulan){
		if($bulan == '01'){ $bulan = "Januari"; $hari = 31; } 
		else if($bulan == '02'){ $bulan = "Februari"; $hari = 29;  } 
		else if($bulan == '03'){ $bulan = "Maret";  $hari = 31; } 
		else if($bulan == '04'){ $bulan = "April";  $hari = 30; } 
		else if($bulan == '05'){ $bulan = "Mei";  $hari = 31; } 
		else if($bulan == '06'){ $bulan = "Juni";  $hari = 30; } 
		else if($bulan == '07'){ $bulan = "Juli";  $hari = 31; } 
		else if($bulan == '08'){ $bulan = "Agustus";  $hari = 31; } 
		else if($bulan == '09'){ $bulan = "September";  $hari = 30; } 
		else if($bulan == '10'){ $bulan = "Oktober";  $hari = 31; } 
		else if($bulan == '11'){ $bulan = "Nopember";  $hari = 30; } 
		else if($bulan == '12'){ $bulan = "Desember";  $hari = 31; }
		
		$bln = array($bulan,$hari);
		return $bln;
	}
	
	function type_absensi(){
		$q = $_REQUEST["q"]; 
		$data_absen = $this->model_m_gang_activity_detail->type_absensi($q);
		$absensi = array();
		foreach($data_absen as $row){
				$absensi[] = '{res_id:"'.str_replace('"','\\"',$row['TYPE_ABSENSI']).'",res_name:"'.str_replace('"','\\"',$row['DESCRIPTION']).'",res_dl:"'.str_replace('"','\\"',$row['TYPE_ABSENSI']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
			}
			  echo '['.implode(',',$absensi).']'; exit; 
	}
	
	function location_type(){
		$ltc = $_REQUEST['q'];
		$data_loctype = $this->model_m_gang_activity_detail->location_type($ltc);
		
		$loctype = array();
		foreach($data_loctype as $row)
			{
				$loctype[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_TYPE_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_TYPE_CODE']).'"}';
			}
			  echo '['.implode(',',$loctype).']'; exit; 
	}
	
	function satuan(){
		$q = $_REQUEST['q'];
		$data_satuan = $this->model_m_gang_activity_detail->satuan($q);
		
		$satuan = array();
		foreach($data_satuan as $row)
			{
				$satuan[] = '{res_id:"'.str_replace('"','\\"',$row['UNIT_CODE']).'",res_name:"'.str_replace('"','\\"',$row['UNIT_DESC']).'",res_dl:"'.str_replace('"','\\"',$row['UNIT_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['UNIT_DESC']).'"}';
			}
			  echo '['.implode(',',$satuan).']'; exit; 
	}
	
	function location(){
		$q = $_REQUEST['q'];
		$loc = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
		if($loc == 'PJ'){
			$data_location = $this->model_m_gang_activity_detail->location_pj($q,$company);
		} else {
			$data_location = $this->model_m_gang_activity_detail->location($q, $loc, $company);
		}
		
		$data = array();
		$location = array();
		foreach($data_location as $row)
			{
				$location[] = '{res_id:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_name:"'.str_replace('"','\\"',$row['LOCATION_CODE']).'",res_dl:"'.str_replace('"','\\"',$row['LOCATION_CODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['DESCRIPTION']).'"}';
			}
		  echo '['.implode(',',$location).']'; exit;
	}
	
	function activity(){
		$lt = $this->uri->segment(3);
		$lc = $this->uri->segment(4);
		$ac = $_REQUEST['q'];
		$limit = $_REQUEST['limit'];
		$company = $this->session->userdata('DCOMPANY');
		
		$activity = array();				
		if($lt == 'PJ'){
		$data_enroll = $this->model_m_gang_activity_detail->activity_pj($lc,$company);
		foreach($data_enroll as $row)
			{
				$activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
			}
		 echo '['.implode(',',$activity).']'; exit;
		 
		} else {
		$data_enroll = $this->model_m_gang_activity_detail->activity($ac, $lt);
		
		foreach($data_enroll as $row)
			{
				$activity[] = '{res_id:"'.str_replace('"','\\"',$row['ACCOUNTCODE']).'",res_name:"'.str_replace('"','\\"',$row['COA_DESCRIPTION']).'",res_d:"'.str_replace('"','\\"',$row['ACCOUNTCODE']. "&nbsp;&nbsp; - &nbsp;&nbsp;" .$row['COA_DESCRIPTION']).'",}';
			}
		 echo '['.implode(',',$activity).']'; exit;
		 
		}
	}	
}

?>