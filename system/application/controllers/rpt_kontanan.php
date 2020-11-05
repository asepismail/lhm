<?
class rpt_kontanan extends Controller 
{
    function rpt_kontanan ()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_tunjanganpotongan' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_kontanan";
        
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
    
    function js_kontanan(){
        $js = " jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var jns_laporan = $('#jns_laporan').val();
				if ( periode > 0 ){
					if ( jns_laporan == 'html'){
							urls = url + 'rpt_kontanan/kontanan_preview/' + periode, 
							$('#frame').attr('src',urls); 
						} else if ( jns_laporan == 'pdf'){
							urls = url+'rpt_kontanan/generate/' + periode,  
							$('#frame').attr('src',urls);                  
						} else if ( jns_laporan == 'excell'){
							urls = url + 'rpt_kontanan/kontanan_xls/' + periode,
							$.download(urls,'');
					}
				} else {
                    alert('rentang periode salah!!');
                    return false;
                }
            });";
        return $js;
    }
	
	function index()
    {
        $view = "rpt_kontanan";
        $data = array();
        $data['judul_header'] = "Daftar Kontanan Karyawan";
        $data['js'] = $this->js_kontanan();
            
        $data['login_id'] = htmlentities($this->session->userdata('LOGINID'),ENT_QUOTES,'UTF-8');
        $data['company_name'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['company_code'] = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $data['company_dest'] = htmlentities($this->session->userdata('DCOMPANY_NAME'),ENT_QUOTES,'UTF-8');
        $data['user_level'] = htmlentities($this->session->userdata('USER_LEVEL'),ENT_QUOTES,'UTF-8');
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
	
	function kontanan_preview(){
		$periode = $this->uri->segment(3);
        $company = $this->session->userdata('DCOMPANY');
		$data_row = $this->model_rpt_tunjanganpotongan->model_rpt_kontanan($company, $periode);
		$i = 1;
        $rpt = "";
		$bulan = $this->periodename(substr($periode,-2));
    	$tahun = substr($periode,0,4);
		$this->style();
		
		$rpt .= "<span class='content2'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
        $rpt .= "<span class='content2'>DAFTAR KONTANAN KARYAWAN</span><br />";
        $rpt .= "<span class='content2'>PERIODE  :  ". strtoupper($bulan) . " ". $tahun ."</span><br />";
		
		$rpt .= "<table width='400px' style='' class='tbl_header' cellpadding='0' cellspacing='0'>";
		$rpt .= "<tr><td align='center' class='tbl_th'> No. </td>";
        $rpt .= "<td align='center' class='tbl_th'>NIK</td>";
        $rpt .= "<td align='center' class='tbl_th'>NAMA</td>";
        $rpt .= "<td align='center' class='tbl_th'>RUPIAH KONTANAN</td></tr>";
		
		$total = 0;
		foreach ($data_row as $row)
        { 
			$rpt .= "<tr><td  align='center' class='tbl_td'> " . $i . " </td>";
			$rpt .= "<td align='center' class='tbl_td'> " . $row['EMPLOYEE_CODE'] . " </td>";
			$rpt .= "<td align='left' class='tbl_td'> " . $row['NAMA'] . " </td>";
			$rpt .= "<td align='right' class='tbl_td'> " . number_format($row['RUPIAH']) . " </td></tr>";
			$total = $total + $row['RUPIAH'];
			$i++;
		}
		$rpt .= "<tr><td colspan='3' align='center' class='tbl_td'> TOTAL </td>";
		$rpt .= "<td align='right' class='tbl_td'> " . number_format($total) . " </td></tr>";
		$rpt .= "</table>";
		
		echo $rpt;
	}
	
	function kontanan_xls(){
		$periode = $this->uri->segment(3);
        $company = $this->session->userdata('DCOMPANY');
		$data_row = $this->model_rpt_tunjanganpotongan->model_rpt_kontanan($company, $periode);
		$i = 1;
        $rpt = "";
		$bulan = $this->periodename(substr($periode,-2));
    	$tahun = substr($periode,0,4);
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';

		$obj =& get_instance();
		$judul .= strtoupper($this->session->userdata('DCOMPANY_NAME'))."\t";
		$judul .= " \n";
        $judul .= "DAFTAR KONTANAN KARYAWAN \t";
        $judul .= " \n";
        $judul .= "PERIODE :  ". strtoupper($bulan) . " ". $tahun ." \t" ;
        $judul .= " \n";
		
		$array = array();
		$headers .= "NO \t";
        $headers .= "NIK \t";
        $headers .= "NAMA \t";
        $headers .= "RUPIAH KONTANAN \t";    
		$no = 1;		
		
		$total = 0;
		foreach ($data_row as $row)
        { 
			$line = '';
            $line .= str_replace('"', '""',$no)."\t";
			$line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
            $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
            $line .= str_replace('"', '""',trim($row['RUPIAH']))."\t";
			$total = $total + $row['RUPIAH'];
			$no++;
			$data .= trim($line)."\n";
		}
		$footer .= " - \t";
		$footer .= " TOTAL \t";
		$footer .= " - \t";
		$footer .= str_replace('"', '""',trim($total))."\t";
		$data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=KONTANAN_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";  
	}
	
	function kontanan_pdf(){
	}
	
	function periodename($bulan){
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
		else if($bulan=='11'){ $bulan = "November"; } 
		else if($bulan=='12'){ $bulan = "Desember"; }
		return $bulan;
	}
	
	function style() {
		echo "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
                    .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
                    .tbl_td { font-size: 12px;color:#678197; padding:1px; padding-left:5px; padding-right:5px; border-bottom:1px solid; border-right:1px solid }
                    .tbl_2 { font-size: 12px;color:#678197;}
                    .content2 { font-size: 12px;color:#678197; }
                    </style>";
	}
}
?>