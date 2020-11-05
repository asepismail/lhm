<?php
class rpt_monitoring_vh extends Controller{
    function __construct(){
        parent::__construct();
		
		$this->load->model( 'model_rpt_monitoring_vh' );
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
		$this->lastmenu="syst_m_user";
		$this->load->helper('file');
    }
	
	function js_monvh(){
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
							urls = url + 'rpt_monitoring_vh/preview/' + from  + '/' + to, 
							$('#frame').attr('src',urls); 
						} else if ( jns_laporan == 'excell'){
							urls = url + 'rpt_monitoring_vh/table_xls/' + from  + '/' + to,
							$.download(urls,'');
					}
				} else {
                    alert('rentang periode salah!!');
                    return false;
                }
            });";
        return $js;
    }
	
	function index(){
        $view = "rpt_monitoring_vh";
        $data = array();
        $data['judul_header'] = "Monitoring Buku Kendaraan Belum Terisi";
        $data['js'] = $this->js_monvh();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
                show($view, $data);
		} else {
            redirect('login');
        }
	}
	
	function preview(){
        $from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        
        $no = 1;
        $table = ""; 
        $bulan = $this->bln_to_periode(substr($from,4,2));
		$periode = strtoupper($bulan) . " " . substr($from,0,4); 
		
        $table .= "<style>.tbl_header { font-size:12px;color:#678197;border-top:1px solid;border-left:1px solid; }";
		$table .= ".tbl_th{ font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$table .= ".tbl_td{ font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
		$table .= ".tbl_2{ font-size: 12px;color:#678197;} ";
		$table .= ".content{ font-size: 12px;color:#678197; } .content2{ font-size: 11px;color:#678197; } </style>";
		
		$table .= "<span style='font-size: 12px;color:#678197'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
        $table .= "<span style='font-size: 12px;color:#678197'>MONITORING BUKU KENDARAAN</span><br />";
        $table .= "<span style='font-size: 12px;color:#678197'>PERIODE     :  ".$periode ."</span><br /><br />";

		$table .= "<table width='850px' class='tbl_header' cellpadding='0' cellspacing='0'><tr>";    
		$table .= "<td align='center' class='tbl_th'>No. </td>";
		$table .= "<td align='center' class='tbl_th'>Tanggal</td>";
		$table .= "<td align='center' class='tbl_th'>Tipe Lokasi</td>";
		$table .= "<td align='center' class='tbl_th'>Kode Kendaraan Belum Terisi</td>";
		$table .= "<td align='center' class='tbl_th'>Jumlah HK Pada Kendaraan</td>";
		$table .= "<td align='center' class='tbl_th'>Jumlah Lembur Pada Kendaraan</td>";
		$table .= "<td align='center' class='tbl_th'>Jumlah Premi Pada Kendaraan</td></tr>";
		
		$data_vh = $this->model_rpt_monitoring_vh->generate_monitoring_vh($company, $from, $to);
		
		foreach ($data_vh as $row){
			$table .= "<tr><td align='center' class='tbl_th'>".$no."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['LHM_DATE']."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['LOCATION_TYPE_CODE']."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['LOCATION_CODE']."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['HKE_JUMLAH']."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['LEMBUR_RUPIAH']."</td>";
			$table .= "<td align='center' class='tbl_th'>".$row['PREMI']."</td></tr>";
			$no++;
		}
		$table .= "</table>";    
        echo $table;    
    }
	
	function table_xls(){
		$from = $this->uri->segment(3);
		$to = $this->uri->segment(4);
		$company = $this->session->userdata('DCOMPANY');
        
        $no = 1;
        $table = ""; 
        $bulan = $this->bln_to_periode(substr($from,4,2));
		$periode = strtoupper($bulan) . " " . substr($from,0,4);
		
		$judul = '';
        $headers = ''; 
        $data = '';
		
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t";
        $judul .= " \n";
        $judul .= "MONITORING BUKU KENDARAAN \t";
        $judul .= " \n";
        $judul .= "PERIODE     :  ".$periode." \t";
        $judul .= " \n";
		
		$headers .= "No \t";
        $headers .= "Tanggal \t";
        $headers .= "Tipe Lokasi \t";
        $headers .= "Kode Kendaraan Belum Terisi \t";
        $headers .= "Jumlah HK Pada Kendaraan \t";    
        $headers .= "Jumlah Lembur Pada Kendaraan \t";
        $headers .= "Jumlah Premi Pada Kendaraan \t";
		
		$data_vh = $this->model_rpt_monitoring_vh->generate_monitoring_vh($company, $from, $to);
		
		foreach ($data_vh as $row){
			$line = '';
			$line .= str_replace('"', '""',trim($no))."\t";
			$line .= str_replace('"', '""',trim($row['LHM_DATE']))."\t";
			$line .= str_replace('"', '""',trim($row['LOCATION_TYPE_CODE']))."\t";
			$line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t";
			$line .= str_replace('"', '""',trim($row['HKE_JUMLAH']))."\t";
			$line .= str_replace('"', '""',trim($row['LEMBUR_RUPIAH']))."\t";
			$line .= str_replace('"', '""',trim($row['PREMI']))."\t";
			$no++;
			$data .= trim($line)."\n";
		}
		
		$data = str_replace("\r","",$data);
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=Monitoring_vh_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
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
}

?>