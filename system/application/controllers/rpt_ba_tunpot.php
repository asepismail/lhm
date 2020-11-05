<?
class rpt_ba_tunpot extends Controller 
{
    function rpt_ba_tunpot()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_ba' ); 
        
        $this->load->model('model_c_user_auth'); 
        $this->lastmenu="rpt_ba_tunpot";
        
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
        $view = "rpt_ba_tunpot";
        $data = array();
        $data['judul_header'] = "Berita Acara Tunjangan Dan Potongan";
        $data['js'] = $this->js_ba_tunpot();    
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['periode'] = $this->global_func->drop_date2('bulan','tahun','select');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
                
        if ($data['login_id'] == TRUE){
            //if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
            show($view, $data);
            //} 
        } else {
            redirect('login');
        }
        
    } 
        
    // ------------------------------ umum punya ------------------------------------------------- //
    function js_ba_tunpot(){
        
        $js = "$(function() {
                    $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
                    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
                });
        jQuery('#submitdata').click(function (){
            var periode = $('#tahun').val() + $('#bulan').val();
            var jns_laporan = $('#jns_laporan').val();                
            if ( jns_laporan == 'html'){
                urls = url + 'rpt_ba_tunpot/ba_tunpot/' + $('#FROM').val() + '/' + $('#TO').val();; 
                $('#frame').attr('src',urls); 
            } else if ( jns_laporan == 'excell'){
                urls = url + 'rpt_ba_tunpot/ba_xlstunpot/' + $('#FROM').val() + '/' + $('#TO').val();; 
                $.download(urls,'');
            }
        });";
        return $js;
    }
    
    function ba_xlstunpot($from,$to){
       $from = str_replace("-","",$this->uri->segment(3));
        $to = str_replace("-","",$this->uri->segment(4));
        $periode = substr(str_replace("-","",$to),0,6);
        $company = $this->session->userdata('DCOMPANY');
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $bulan = substr($periode,-2);
        $tahun = substr($periode,0,4);
        if($bulan=='01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan=='02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan=='03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan=='04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan=='05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan=='06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan=='07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan=='08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan=='09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan=='10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan=='11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan=='12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $company = $this->session->userdata('DCOMPANY');
        $data_umum = $this->model_rpt_ba->ba_tunpot($from, $to, $periode, $company);
        
        $total_hk = 0;
        
        $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
        $judul .= "BERITA ACARA TUNJANGAN DAN POTONGAN \n";
        $judul .= "NO:      / TUNPOT /".$company." / ".$bulanr." / ".$tahun."\n";
        $judul .= "PERIODE : ".strtoupper($bulan)." - " .$tahun."\n";
        $judul .= "PT : ".$this->session->userdata('DCOMPANY_NAME')."\n";
        $judul .= " \n";
        
        
        $headers .= "AKTIVITAS \t";
        $headers .= "\t";
        $headers .= "REALISASI BIAYA \t";    
        $headers .= "\t";
        
        $headers .= "\n";
        $headers .= "KODE \t";
        $headers .= "NAMA \t";
        $headers .= "BLN INI \t";
        $headers .= "S.D BLN INI \t";  
        
        $total = 0;
        foreach ( $data_umum as $row){
            $line = '';
                        
            $line .= str_replace('"', '""',$row['ACTIVITY_CODE'])."\t";
            $line .= str_replace('"', '""',$row['COA_DESCRIPTION'])."\t";
            $line .= str_replace('"', '""',$row['BIAYA'])."\t";
            $line .= str_replace('"', '""',$row['BIAYA'])."\t";
            
            $data .= trim($line)."\n";
             
            if (substr($row['ACTIVITY_CODE'],0,1) == '6') {
                $total = $total + $row['BIAYA'];
            } else {
                $total = $total - $row['BIAYA'];
            }       
        }
        $footer .= " TOTAL BIAYA \t";
        $footer .= "\t";
        $footer .= number_format($total)."\t";
        $footer .= number_format($total)."\t";
        
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);  
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=BA_TUNPOT_".$company."_".$periode.".xls");
        echo "$judul\n$headers\n$data";
    }
    
    function ba_tunpot($from,$to){
        $from = str_replace("-","",$this->uri->segment(3));
        $to = str_replace("-","",$this->uri->segment(4));
        $periode = substr(str_replace("-","",$to),0,6);
        $company = $this->session->userdata('DCOMPANY');
        
        $data = array();
        
        $bulan = substr($periode,-2);
        $tahun = substr($periode,0,4);
        if($bulan=='01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan=='02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan=='03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan=='04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan=='05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan=='06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan=='07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan=='08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan=='09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan=='10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan=='11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan=='12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $company = $this->session->userdata('DCOMPANY');
        $data_umum = $this->model_rpt_ba->ba_tunpot($from, $to, $periode, $company);
        
        $total_hk = 0;
        
        $tabel = "";
        $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; }
            .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
            .tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid }
            .tbl_2 { font-size: 12px;color:#678197;}
            .content { font-size: 12px;color:#678197; }
            </style>";
        $tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>BERITA ACARA TUNJANGAN DAN POTONGAN</strong></td>
</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / TUNPOT / ".$company." / ".$bulanr." / ".$tahun." </strong></td>
</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper($bulan)." &nbsp;" .$tahun. "</strong></td>
</tr><tr><td colspan='3'>&nbsp;</td>
</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
        $tabel .= "<table width='80%' class='tbl_header' cellpadding='0' cellspacing='0'>";
        $tabel .= "<tr><th class='tbl_th' colspan='2'>AKTIVITAS</td><th class='tbl_th' colspan='2'>REALISASI BIAYA</th></tr><tr><th class='tbl_th'>KODE</th><th class='tbl_th'>NAMA</th><th class='tbl_th'>BLN INI</th><th class='tbl_th'>s.d BLN INI</th></tr>";
    
        $total = 0;
        
        foreach ( $data_umum as $row){
            $tabel .= "<tr><td class='tbl_td' align = 'center'>&nbsp;".$row['ACTIVITY_CODE']."</td>
            <td class='tbl_td' align = 'left'> &nbsp;&nbsp;".$row['COA_DESCRIPTION']."</td>";
     $tabel .= "<td class='tbl_td' align = 'right'>".number_format($row['BIAYA'])."&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'>".number_format($row['BIAYA'])."&nbsp;&nbsp;</td></tr>";
            if (substr($row['ACTIVITY_CODE'],0,1) == '6') {
                $total = $total + $row['BIAYA'];
            } else {
                $total = $total - $row['BIAYA'];
            }
        }

        $tabel .= "<tr>
    <td colspan='2' class='tbl_td' align='center'><strong>&nbsp;&nbsp;TOTAL BIAYA</strong></td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
    <td class='tbl_td' align = 'right'><strong>".number_format($total)."</strong>&nbsp;&nbsp;</td>
      </tr>";
        $tabel .= "</table>"; 
        
        echo $tabel;
    }
    
}

?>
