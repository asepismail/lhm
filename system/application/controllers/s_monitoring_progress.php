<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class s_monitoring_progress extends Controller{
   function s_monitoring_progress (){
		parent::Controller();	
		/*modul yang di load halaman gad*/
		$this->load->model( 'm_monitoring_progress' ); 
		        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="s_monitoring_progress";
        
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

    function index(){		
		$view = "info_s_monitoring_progress";
		$data = array();
		$data['judul_header'] = "Monitoring Progress Activity";
		$data['js'] = $this->js_absensi();	
		
		$data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 

		if ($data['login_id'] == TRUE){
			show($view, $data);
		} else {
			redirect('login');
		}
    } 
	
	function js_absensi(){		
		$js = "jQuery('#submitdata').click(function (){
			var periode = $('#tahun').val() + $('#bulan').val();
			var jns_laporan = $('#jns_laporan').val();				
			if ( jns_laporan == 'html'){
				urls = url + 's_monitoring_progress/prev_absensi/' + periode; 
				$('#frame').attr('src',urls); 
			} else if ( jns_laporan == 'excell'){
				urls = url + 's_monitoring_progress/prev_absensi/' + periode;
                $.download(urls,'');
			} else if ( jns_laporan == 'pdf'){
				urls = url + 's_monitoring_progress/absen_pdf/' + periode;
				$('#frame').attr('src',urls); 
			}
		});";
		return $js; 
	}
		
	function absen_pdf(){
		require_once(APPPATH . '/libraries/html2pdf/html2pdf.class.php');
		ob_start();
		$periode = $this->uri->segment(3);
		
		$data = array();
		$company = $this->session->userdata('DCOMPANY');
		$company_name = $this->session->userdata('DCOMPANY_NAME');
			
		
		$bulan = substr($periode,-2);
		$month = substr($periode,-2);
		$tahun = substr($periode,0,4);
		$thn_lalu = $tahun;
		if($bulan == '01'){ $bulan = "Januari"; $hari = 31; $bln_lalu = "12"; $thn_lalu = ((int)$tahun-1);} 
		else if($bulan == '02'){ $bulan = "Februari"; $hari = 29;  $bln_lalu = "01";} 
		else if($bulan == '03'){ $bulan = "Maret";  $hari = 31; $bln_lalu = "02";} 
		else if($bulan == '04'){ $bulan = "April";  $hari = 30; $bln_lalu = "03";} 
		else if($bulan == '05'){ $bulan = "Mei";  $hari = 31; $bln_lalu = "04";} 
		else if($bulan == '06'){ $bulan = "Juni";  $hari = 30; $bln_lalu = "05";} 
		else if($bulan == '07'){ $bulan = "Juli";  $hari = 31; $bln_lalu = "06";} 
		else if($bulan == '08'){ $bulan = "Agustus";  $hari = 31; $bln_lalu = "07";} 
		else if($bulan == '09'){ $bulan = "September";  $hari = 30; $bln_lalu = "08";} 
		else if($bulan == '10'){ $bulan = "Oktober";  $hari = 31; $bln_lalu = "09";} 
		else if($bulan == '11'){ $bulan = "Nopember";  $hari = 30; $bln_lalu = "10";} 
		else if($bulan == '12'){ $bulan = "Desember";  $hari = 31; $bln_lalu = "11";}
		
		$bln = strtotime($periode.$hari);
		$last_period = $thn_lalu.$bln_lalu;
		$data_absen = $this->m_monitoring_progress->create_absensi($company, $periode, $last_period);	
		
		$content='';
		$content = "MONITORING ROTASI PANEN<br>";
		$content.= "PERIODE : " .strtoupper($bulan)  . " " . $tahun;
		$content.="<br>PT. ".$company_name."<br>";
		$content.="<br><table cellpadding='0' cellspacing='0' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' width='95%'>
  <tr>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>NO.</td>
    <td align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>LOKASI</td>
    <td align='center' style='padding:1px; font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>STATUS</td>
    <td align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>TGL PANEN </td>";
	
		for($i=1; $i<=$hari; $i++){
			$cal_flag = "";	
			$tgllengkap = $periode.$i;
			$data_calendar = $this->m_monitoring_progress->cek_hari($tgllengkap, $company);
			foreach($data_calendar as $row_cal){
				$cal_flag .= $row_cal['CAL_FLAG'];
			}
			
			if($cal_flag != 'KJ'){
				$content .= "<td  align='center' style='background-color: #ff0000; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
			}else{
				$content .= "<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
			}
		}
   
    	$content .= "<td align='center' style='padding:1px; font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>ROTASI</td>
  </tr>";
		
		$no = 1;		
		$x = 1;
		foreach($data_absen as $row){
			$content .= " <tr>
			<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$no." </td> ";
			$content .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['LOCATION_CODE']." </td> ";
			$total_tahun=((int)$tahun-(int)$row['YEARREPLANT']);	
			if($row['YEARREPLANT']== NULL){
				$status="";
			}else{
				if ($total_tahun==0){
					$status = "TBM"."0";
				}elseif ($total_tahun==1){
					$status = "TBM"."1";	
				}elseif ($total_tahun==2){
					$status = "TBM"."2";
				}elseif ($total_tahun==3){
					$status = "TBM"."3";	
				}else{
					$status = "TM".($total_tahun-3);					
				}
			}
			$content .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$status." </td> ";
				
			$array = explode(",",$row['ABSEN']);
			$location = $row['LOCATION_CODE'];
			$max_date = $row['MAX_TGL_PROGRESS'];
			$max_date = $this->m_monitoring_progress->check_max_date($company, $max_date, $location);
			$content .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$max_date." </td> ";				
			$rotasi = 0;
			$count_rotasi = 0;
			$tgl_lengkap = $tahun."-".$month."-".sprintf("%02d", $x);
			$diff = abs(strtotime($tgl_lengkap) - strtotime($max_date));
			$days=floor($diff/(60*60*24));			
			$rotasi = $days;
			if ($max_date==""){
				$rotasi = 1;
			}
	
			for($i=1; $i<=$hari; $i++){	
				$a = false;
				for($j=0; $j<count($array); $j++){										
					$absennya = explode(":", $array[$j] );					
					$tgl = $absennya[0];
					if($tgl==$i){							
						$a = true;	
					}
				}
				if($a==true){		
					if ($rotasi>3) {
						$rotasi = 0;
						$content .= "<td width='2%' align='center' style='background-color: #0F0; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}else{
						$content .= "<td width='2%' align='center' style='background-color: #FF0; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}
				} elseif ($a==false) {
					if ($rotasi>10){
						$content .= "<td width='2%' align='center' style='font-size: 12px;color:#F00;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}else{
						$content .= "<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}
				}	
				$content .= "</td>";	
				if ($rotasi==0){
					$count_rotasi = $count_rotasi + 1;	
				}
				$rotasi = $rotasi + 1;										
			}//end for
			$content .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$count_rotasi." </td> ";
			$content .= "</tr>";
			$no++;
		}	

		$content .= "</table>";
		try{
			$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(5, 15, 5, 5));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content);
			$html2pdf->Output("ROTASI_PANEN_".$company."_".$periode.".pdf");
		}catch(HTML2PDF_exception $e) {
			echo 'header("Content-type: application/pdf");'.$e;
			exit;
		}
	 	
	}
	
	function prev_absensi(){
		$periode = $this->uri->segment(3);
		
		$data = array();
		
		$data['login_id'] = $this->session->userdata('LOGINID');
		$data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
		$data['company_code'] = $this->session->userdata('DCOMPANY');
		$data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
		$data['user_level'] = $this->session->userdata('USER_LEVEL');
		$data['absen'] = $this->absensi($periode);
		if ($data['login_id'] == TRUE){
			$this->load->view('info_s_monitoring_progressdetail', $data);
		} else {
			redirect('login');
		} 
	}
	
	function absensi(){
		$periode = $this->uri->segment(3);
		$company = $this->session->userdata('DCOMPANY');
				
		$absen = "";
		$array = array();
		
		$libur = "";				
				
		$bulan = substr($periode,-2);
		$month = substr($periode,-2);
		$tahun = substr($periode,0,4);
		$thn_lalu = $tahun;
		if($bulan == '01'){ $bulan = "Januari"; $hari = 31; $bln_lalu = "12"; $thn_lalu = ((int)$tahun-1);} 
		else if($bulan == '02'){ $bulan = "Februari"; $hari = 29;  $bln_lalu = "01";} 
		else if($bulan == '03'){ $bulan = "Maret";  $hari = 31; $bln_lalu = "02";} 
		else if($bulan == '04'){ $bulan = "April";  $hari = 30; $bln_lalu = "03";} 
		else if($bulan == '05'){ $bulan = "Mei";  $hari = 31; $bln_lalu = "04";} 
		else if($bulan == '06'){ $bulan = "Juni";  $hari = 30; $bln_lalu = "05";} 
		else if($bulan == '07'){ $bulan = "Juli";  $hari = 31; $bln_lalu = "06";} 
		else if($bulan == '08'){ $bulan = "Agustus";  $hari = 31; $bln_lalu = "07";} 
		else if($bulan == '09'){ $bulan = "September";  $hari = 30; $bln_lalu = "08";} 
		else if($bulan == '10'){ $bulan = "Oktober";  $hari = 31; $bln_lalu = "09";} 
		else if($bulan == '11'){ $bulan = "Nopember";  $hari = 30; $bln_lalu = "10";} 
		else if($bulan == '12'){ $bulan = "Desember";  $hari = 31; $bln_lalu = "11";}
		
		$bln = strtotime($periode.$hari);
		$last_period = $thn_lalu.$bln_lalu;
		$data_absen = $this->m_monitoring_progress->create_absensi($company, $periode, $last_period);	
		$table = "<table cellpadding='0' cellspacing='0' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' width='95%'>";
		
		$table .= "<tr><td rowspan='2' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> NO. </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> KODE LOKASI </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> STATUS </td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> TGL PANEN TERAKHIR </td>";			
		$table .= "<td colspan = '".$hari."' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'><center>" . strtoupper($bulan)  . " " . $tahun . "</center></td>
		<td rowspan='2' align='center' style='padding:1px; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> JUMLAH ROTASI </td>	
		</tr>";		
		$table .= "<tr>";
		for($i=1; $i<=$hari; $i++){
			$cal_flag = "";	
			$tgllengkap = $periode.$i;
			$data_calendar = $this->m_monitoring_progress->cek_hari($tgllengkap, $company);
			foreach($data_calendar as $row_cal){
				$cal_flag .= $row_cal['CAL_FLAG'];
			}
			
			if($cal_flag != 'KJ'){
				$table .= "<td  align='center' style='background-color: #ff0000; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
			}else{
				$table .= "<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$i." </td>";
			}
		}
		
		$table .= "</tr>";		
			
		$no = 1;		
		$x = 1;
		foreach($data_absen as $row)
		{
			$table .= " <tr>
			<td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$no." </td> ";
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$row['LOCATION_CODE']." </td> ";
			$total_tahun=((int)$tahun-(int)$row['YEARREPLANT']);
			if($row['YEARREPLANT']== NULL){
				$status="";
			}else{
				if ($total_tahun==0){
					$status = "TBM"."0";
				}elseif ($total_tahun==1){
					$status = "TBM"."1";	
				}elseif ($total_tahun==2){
					$status = "TBM"."2";
				}elseif ($total_tahun==3){
					$status = "TBM"."3";	
				}else{
					$status = "TM".($total_tahun-3);					
				}
			}
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$status." </td> ";
			
			$array = explode(",",$row['ABSEN']);
			$location = $row['LOCATION_CODE'];
			$max_date = $row['MAX_TGL_PROGRESS'];
			$max_date = $this->m_monitoring_progress->check_max_date($company, $max_date, $location);
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$max_date." </td> ";				
			$rotasi = 0;
			$count_rotasi = 0;
			$tgl_lengkap = $tahun."-".$month."-".sprintf("%02d", $x);

$max_date= strtotime('-1 day',strtotime($max_date)); 
		$max_date= date('Y-m-d', $max_date);

			$diff = abs(strtotime($tgl_lengkap) - strtotime($max_date));
			$days=floor($diff/(60*60*24));			
			$rotasi = $days;
			if ($max_date==""){
				$rotasi = 1;
			}

			for($i=1; $i<=$hari; $i++){				
				$a = false;
				for($j=0; $j<count($array); $j++){										
					$absennya = explode(":", $array[$j] );					
					$tgl = $absennya[0];										
					if($tgl==$i){							
						$a = true;	
					}
				}
				if($a==true){
					
					if ($rotasi>3) {
						$rotasi = 0;
						$table .= "<td width='2%' align='center' style='background-color: #0F0; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;						
					}else{
						$table .= "<td width='2%' align='center' style='background-color: #FF0; font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}
				} elseif ($a==false) {
					if ($rotasi>10){
						$table .= "<td width='2%' align='center' style='font-size: 12px;color:#F00;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}else{
						$table .= "<td width='2%' align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$rotasi;
					}
				}	
				$table .= "</td>";	
				if ($rotasi==0){
					$count_rotasi = $count_rotasi + 1;	
				}
				$rotasi = $rotasi + 1;										
			}//end for
			$table .= " <td  align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ".$count_rotasi." </td> ";
			$table .= "</tr>";
			$no++;
		}
		
		$table .= "</table><br />";
		$table .= $libur;		
		return $table;
	} 	
}
?>
