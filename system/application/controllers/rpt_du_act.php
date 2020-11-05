<?
class rpt_du_act extends Controller 
{
    function rpt_du_act ()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_du' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_du_act";
        
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
    
        function js_du_act(){
        $js = " $(function() {
                    $('#FROM').datepicker({dateFormat:'yy-mm-dd'});
                    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
                });
                jQuery('#submitdata').click(function (){
                var periode = $('#tahun').val() + $('#bulan').val();
                var gc = $('#activity').val();
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
                            urls = url+'rpt_du_act/preview_act/' + gc  + '/' + from  + '/' + to, 
                                $('#frame').attr('src',urls); 
                        } else if ( jns_laporan == 'pdf'){
                            urls = url+'rpt_du_act/gen_rpt_du_act/' + gc  + '/' + from  + '/' + to, 
                            $('#frame').attr('src',urls);                 
                        } else if ( jns_laporan == 'excell'){
                                urls = url + 'rpt_du_act/du_act_xls/' + gc + '/' + from + '/' + to + '/div';
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
        $view = "rpt_du_act";
        $data = array();
        $data['judul_header'] = "Daftar Upah Per Aktivitas";
        $data['js'] = $this->js_du_act();
            
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu); 
        
        if ($data['login_id'] == TRUE){
            if ($data['user_level'] == 'SAD' || $data['user_level'] == 'SAS'){
                show($view, $data);
            } 
        } else {
            redirect('login');
        }    
    }      
    
    function preview_act(){
        //$periode = $this->uri->segment(3);
        $act = $this->uri->segment(3);
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $data_row = $this->model_rpt_du->generate_du_activity($company,$from,$to,$act);
        
        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        
        $i = 1;
        $DU = "";
        
         $DU .= "<span style='font-size: 11px;color:#678197'>".strtoupper($this->session->userdata('DCOMPANY_NAME')) ."</span><br />";
        $DU .= "<span style='font-size: 11px;color:#678197'>DAFTAR UPAH</span><br />";
        $DU .= "<span style='font-size: 11px;color:#678197'>AKTIVITAS     :  ".strtoupper($act) ."</span><br />";
        
        $DU .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $DU .= ".tbl_th { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_td { font-size: 10px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $DU .= ".tbl_2 { font-size: 12px;color:#678197;} ";
        $DU .= ".content { font-size: 12px;color:#678197; }</style>";
        
        $DU .= "<table class='tbl_header' cellpadding='0' cellspacing='0'>";    
        $DU .= "<tr><td  align='center' rowspan='2' class='tbl_th'> No. </td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>NIK</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Nama</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Status</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Type Karyawan</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Kode Aktivitas</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Nama Aktivitas</td>";
        $DU .= "<td colspan='3' align='center' class='tbl_th'>HK dibayar</td>";
        $DU .= "<td align='center' colspan='3' class='tbl_th'>HK (Rp) dibayar</td>";
        //$DU .= "<td rowspan='2' align='center' class='tbl_th'>GP</td>";
        //$DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Jabatan / Tunj. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Premi / Lembur</td>";
        /* $DU .= "<td rowspan='2' align='center' class='tbl_th'>Natura</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Rapel / Thr / Bonus</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Tunj. Lebih Hari</td>"; */
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Gaji Bruto</td>";
        /*$DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Astek</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Lain</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Pot. Kurang Hari</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>PPh 21</td>";
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Total Potongan</td>"; */
        $DU .= "<td rowspan='2' align='center' class='tbl_th'>Upah Diterima</td></tr>";
        $DU .= "<tr><td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td>";
        $DU .= "<td align='center' class='tbl_th'>HKE</td>";
        $DU .= "<td align='center' class='tbl_th'>HKNE</td>";
        $DU .= "<td align='center' class='tbl_th'>Total</td></tr>";

        foreach ($data_row as $row)
        {
            $DU .= '<tr id="tr_1">';
              $DU .= '<td class="tbl_td" ><center>'.$i.'</center></td>';
            $DU .= '<td width="50" class="tbl_td" ><center>'.$row['EMPLOYEE_CODE'].'</center></td>';
            $DU .= '<td width="150" class="tbl_td" align="left">&nbsp;'.$row['NAMA'].'</td>';
            $DU .= '<td width="75" class="tbl_td"><center>'.$row['FAMILY_STATUS'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td"><center>'.$row['TYPE_KARYAWAN'].'</center></td>';
            $DU .= '<td width="78" class="tbl_td" align="center">&nbsp;'.$row['ACTIVITY_CODE'].'</td>';
            $DU .= '<td width="250" class="tbl_td" align="left">&nbsp;'.$row['COA_DESCRIPTION'].'</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.$row['HK'].' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.$row['HKNE'].'  &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.$row['TTL'].'</strong> &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['HKE_BYR']) .' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['HKNE_BYR']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($row['TTL_BYR']).'</strong>&nbsp;</td>';
            
            /*
            if ($row['TYPE_KARYAWAN'] == "BHL"){
                $gp = $row['TTL_BYR'];
            } else {
                $gp = $row['GP'];
            } */
            
            //$DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gp).'</strong>&nbsp;</td>';
            //$DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['TUNJANGAN_JABATAN']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['PREMI_LEMBUR']).' &nbsp;</td>';
            /* $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['NATURA']).' &nbsp;</td>';
            $rtb = $row['RAPEL'] + $row['THR'] + $row['BONUS'];
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($rtb).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['ASTEK']).' &nbsp;</td>';
            if ($row['TYPE_KARYAWAN'] == "SKU" && $gp < $row['TTL_BYR']) {
                    $tunj_lhari = $row['TTL_BYR'] - $gp;
                    if($tunj_lhari < 100) { $tunj_lhari = 0; } 
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            } else {
                $tunj_lhari = 0;
                $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($tunj_lhari).' &nbsp;</td>';
            }
            
        $gaji_bruto = $gp + $row['TUNJANGAN_JABATAN'] + $row['PREMI_LEMBUR'] + $row['NATURA'] + $rtb + $row['ASTEK'] + $tunj_lhari;
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gaji_bruto).'</strong>&nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['POT_ASTEK']).' &nbsp;</td>';
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['POTONGAN_LAIN']).' &nbsp;</td>';
            if($row['TYPE_KARYAWAN'] == "SKU" && $gp > $row['TTL_BYR']){
                    $pot_khari = -($row['TTL_BYR'] - $gp);
                    if($pot_khari < 100) { $pot_khari = 0; }
                    $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($pot_khari).' &nbsp;</td>';
            } else {
                $pot_khari = 0;
                $DU .= '<td width="78" class="tbl_td" align="right"> 0 &nbsp;</td>';
            }    
             
            $DU .= '<td width="78" class="tbl_td" align="right">'.number_format($row['PPH_21']).' &nbsp;</td>';
            $total_potongan = $row['POT_ASTEK'] + $row['POTONGAN_LAIN'] + $pot_khari + $row['PPH_21'];
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($total_potongan).' &nbsp;</strong></td>'; */
            $gp = $row['TTL_BYR'];
            $gaji_bruto = $gp + $row['PREMI_LEMBUR'];
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gaji_bruto).' &nbsp;</strong></td>';
            $DU .= '<td width="78" class="tbl_td" align="right"><strong>'.number_format($gaji_bruto).' &nbsp;</strong></td>';
          $DU .= '</tr>';    
        
        $ttl_hke = $ttl_hke + $row['HK'];
        $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
        $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
        $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
        $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
        $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
        
        //$ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
        $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
        //$ttl_natura = $ttl_natura + $row['NATURA'];
        //$ttl_rtb = $ttl_rtb + $rtb;
        $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
        //$pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
        //$ttl_potongan = $ttl_potongan + $total_potongan;
        $ttl_upah = $ttl_upah + ( $gaji_bruto );
        
            $i++;
        }
        
        $DU .= "<tr><td class='tbl_td' align='center' colspan='7'><strong>Total</strong></td>";

        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hkne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_hke_ne)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hkne)."&nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttlbyr_hke_ne)."&nbsp;</strong></td>";
        
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        //$DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_tunjab)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_premi)." &nbsp;</strong></td>";
        //$DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_natura)." &nbsp;</strong></td>";
        //$DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_rtb)." &nbsp;</strong></td>";
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_gaji_bruto)."&nbsp;</strong></td>";
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        //$DU .= "<td class='tbl_td' align='right'><strong>".number_format($pot_lain)." &nbsp;</strong></td>";
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        //$DU .= "<td class='tbl_td' align='right'>- &nbsp;</td>";
        //$DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_potongan)." &nbsp;</strong></td>";
        $DU .= "<td class='tbl_td' align='right'><strong>".number_format($ttl_upah)." &nbsp;</strong></td></tr>";
        $DU .= "</table>"; 
        
        echo $DU;
    
    }
    function du_act_xls()
    {
        $act = $this->uri->segment(3);
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $company_name = $this->session->userdata('DCOMPANY_NAME');

        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        //$i = 1;
        
        $data_row = $this->model_rpt_du->generate_du_activity($company,$from,$to,$act);
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        $obj =& get_instance();
        
        $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
        $judul .= "Daftar Upah per Aktifitas \n";

        $headers .= "No \t";
        $headers .= "NIK \t";
        $headers .= "Nama \t";
        $headers .= "Status \t";
        $headers .= "Type Karyawan \t";
        $headers .= "KODE AKTIVITAS \t";    
        $headers .= "NAMA AKTIVITAS \t";
        $headers .= "HKE \t";
        $headers .= "HKNE \t";
        $headers .= "TOTAL (Rp.) \t";
        $headers .= "HKE \t";
        $headers .= "HKNE \t";
        $headers .= "TOTAL \t";
        $headers .= "Premi /Lembur \t";
        $headers .= "Gaji Bruto \t";
        $headers .= "Upah Diterima \t";
        $x=0;
            
        foreach ( $data_row as $row)
        {
            $x=$x+1;
            $gp = $row['TTL_BYR'];
            $gaji_bruto = $gp + $row['PREMI_LEMBUR'];
            
            $ttl_hke = $ttl_hke + $row['HK'];
            $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
            $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
            $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
            $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
            $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
            $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
            $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
            $ttl_upah = $ttl_upah + ( $gaji_bruto );
            
            $line = '';
            
            $line .= str_replace('"', '""',$x)."\t";    
            $line .= str_replace('"', '""',trim($row['EMPLOYEE_CODE']))."\t";
            $line .= str_replace('"', '""',trim($row['NAMA']))."\t";
            $line .= str_replace('"', '""',trim($row['FAMILY_STATUS']))."\t";
            $line .= str_replace('"', '""',trim($row['TYPE_KARYAWAN']))."\t";
            $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t";
            $line .= str_replace('"', '""',trim($row['COA_DESCRIPTION']))."\t";
            $line .= str_replace('"', '""',trim($row['HK']))."\t";
            $line .= str_replace('"', '""',trim($row['HKNE']))."\t";
            $line .= str_replace('"', '""',trim($row['TTL']))."\t";
            $line .= str_replace('"', '""',number_format(trim($row['HKE_BYR'])))."\t";
            $line .= str_replace('"', '""',number_format(trim($row['HKNE_BYR'])))."\t";
            $line .= str_replace('"', '""',number_format(trim($row['TTL_BYR'])))."\t";
            $line .= str_replace('"', '""',number_format(trim($row['PREMI_LEMBUR'])))."\t";
            $line .= str_replace('"', '""',number_format(trim($gaji_bruto)))."\t";
            $line .= str_replace('"', '""',number_format(trim($gaji_bruto)))."\t";
                    
            $data .= trim($line)."\n";        
        }
            
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " TOTAL \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        
        $footer .= str_replace('"', '""',number_format(trim($ttl_hke)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttl_hkne)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttl_hke_ne)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttlbyr_hke)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttlbyr_hkne)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttlbyr_hke_ne)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttl_premi)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttl_gaji_bruto)))."\t";
        $footer .= str_replace('"', '""',number_format(trim($ttl_upah)))."\t";
                
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=DU_PERAKTIFITAS_".$company.".xls");
        echo "$judul\n$headers\n$data";  
    }
    
    function gen_rpt_du_act()
    {
        if ($this->session->userdata('logged_in') != TRUE)
        {
           redirect('login');
        }
        
        $act = $this->uri->segment(3);
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $company = $this->session->userdata('DCOMPANY');
        $company_name = $this->session->userdata('DCOMPANY_NAME');

        $ttl_hke = 0; $ttl_hkne = 0; $ttl_hke_ne = 0; $ttlbyr_hke = 0;
        $ttlbyr_hkne = 0; $ttlbyr_hke_ne = 0; $ttl_tunjab = 0; $ttl_premi = 0;
        $ttl_natura = 0; $ttl_rtb = 0; $ttl_gaji_bruto = 0; $pot_lain = 0;
        $ttl_potongan = 0; $ttl_upah = 0; $gp = 0; $tunj_lhari = 0; $pot_khari = 0;
        //$i = 1;
        
        $data_row = $this->model_rpt_du->generate_du_activity($company,$from,$to,$act);
        $pdf = new pdf_usage();        
        $pdf->Open();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(0, 20,10);
        $pdf->AddPage("L","A4");
        $pdf->AliasNbPages(); 
        
        require_once(APPPATH . 'libraries/ba/header_du_act.inc');
    
        require_once(APPPATH . 'libraries/ba/table_border.inc');
        
        $columns = 16; //number of Columns
        $pdf->tbInitialize($columns, true, true);
        $pdf->tbSetTableType($table_default_table_type);
        $aSimpleHeader = array(); 
        for($i=0; $i<=$columns; $i++) {
            $aSimpleHeader[$i] = $table_default_header_type;
            if($i == 0) {
                $aSimpleHeader[$i]['TEXT'] = "No";
                $aSimpleHeader[$i]['WIDTH'] = 8;
                //$aSimpleHeader[$i]['COLSPAN'] = 2;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 1) {
                $aSimpleHeader[$i]['TEXT'] = "NIK";
                $aSimpleHeader[$i]['WIDTH'] =15;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 2) {
                $aSimpleHeader[$i]['TEXT'] = "NAMA";
                $aSimpleHeader[$i]['WIDTH'] = 28;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 3) {
                $aSimpleHeader[$i]['TEXT'] = "STATUS";
                $aSimpleHeader[$i]['WIDTH'] = 15;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 4) {
                $aSimpleHeader[$i]['TEXT'] = "Type Karyawan";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 5) {
                $aSimpleHeader[$i]['TEXT'] = "Kode Aktifitas";
                $aSimpleHeader[$i]['WIDTH'] = 24;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 6) {
                $aSimpleHeader[$i]['TEXT'] = "Nama Aktifitas";
                $aSimpleHeader[$i]['WIDTH'] = 35;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 7) {
                $aSimpleHeader[$i]['TEXT'] = "HKE Dibayar";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 3;
            }
            if($i == 8) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 9) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            
            if($i ==10) {
                $aSimpleHeader[$i]['TEXT'] = "HKE (Rp) Dibayar";
                $aSimpleHeader[$i]['WIDTH'] = 28;
                $aSimpleHeader[$i]['COLSPAN'] = 3;
            }
            if($i == 11) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 12) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 13) {
                $aSimpleHeader[$i]['TEXT'] = "Premi /Lembur";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 14) {
                $aSimpleHeader[$i]['TEXT'] = "Gaji Bruto";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 15) {
                $aSimpleHeader[$i]['TEXT'] = "Upah Diterima";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
        }
        
        $aSimpleHeader2 = array(); 
        for($i=0; $i<=$columns; $i++) {
            $aSimpleHeader2[$i] = $table_default_header_type;
            if($i == 0) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                //$aSimpleHeader2[$i]['COLSPAN'] = 2;                
            }
            if($i == 1) {
                $aSimpleHeader2[$i]['TEXT'] = "";
            }
            if($i == 2) {
                $aSimpleHeader2[$i]['TEXT'] = "";    
                $aSimpleHeader2[$i]['WIDTH'] = 10;            
            }
            if($i == 3) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 4) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 5) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 6) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 7) {
                $aSimpleHeader2[$i]['TEXT'] = "HKE";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 8) {
                $aSimpleHeader2[$i]['TEXT'] = "HKNE";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 9) {
                $aSimpleHeader2[$i]['TEXT'] = "TOTAL";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 10) {
                $aSimpleHeader2[$i]['TEXT'] = "HKE";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 11) {
                $aSimpleHeader2[$i]['TEXT'] = "HKNE";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 12) {
                $aSimpleHeader2[$i]['TEXT'] = "TOTAL";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 13) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 14) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 15) {
                $aSimpleHeader2[$i]['TEXT'] = "";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
        }
        
        $aHeader = array( $aSimpleHeader, $aSimpleHeader2);
        
        $pdf->tbSetHeaderType($aHeader, TRUE);
        
        $pdf->tbDrawHeader();
        
        $aDataType = Array();
        for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
        $pdf->tbSetDataType($aDataType);
        //require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
        $x=1;
        foreach ($data_row as $row)
        {
            $x=$x+1;
            $gp = $row['TTL_BYR'];
            $gaji_bruto = $gp + $row['PREMI_LEMBUR'];
            
            $ttl_hke = $ttl_hke + $row['HK'];
            $ttl_hkne = $ttl_hkne + $row['HKNE'] ;
            $ttl_hke_ne = $ttl_hke_ne + $row['TTL'];
        
            $ttlbyr_hke = $ttlbyr_hke + $row['HKE_BYR'];
            $ttlbyr_hkne = $ttlbyr_hkne + $row['HKNE_BYR'];
            $ttlbyr_hke_ne = $ttlbyr_hke_ne + $row['TTL_BYR'];
        
            //$ttl_tunjab = $ttl_tunjab + $row['TUNJANGAN_JABATAN'];
            $ttl_premi = $ttl_premi + $row['PREMI_LEMBUR'];
            //$ttl_natura = $ttl_natura + $row['NATURA'];
            //$ttl_rtb = $ttl_rtb + $rtb;
            $ttl_gaji_bruto = $ttl_gaji_bruto + $gaji_bruto;
            //$pot_lain = $pot_lain + $row['POTONGAN_LAIN'];
            //$ttl_potongan = $ttl_potongan + $total_potongan;
            $ttl_upah = $ttl_upah + ( $gaji_bruto );
            
            $data = Array();
            $data[0]['TEXT'] = $x;
            $data[1]['TEXT'] = $row['EMPLOYEE_CODE'];
            $data[2]['TEXT'] = $row['NAMA'];
            $data[3]['TEXT'] = $row['FAMILY_STATUS'];
            $data[4]['TEXT'] = $row['TYPE_KARYAWAN'];
            $data[5]['TEXT'] = $row['ACTIVITY_CODE'];
            $data[6]['TEXT'] = $row['COA_DESCRIPTION'];
            $data[7]['TEXT'] = $row['HK'];
            $data[8]['TEXT'] = $row['HKNE'];
            $data[9]['TEXT'] = $row['TTL'];
            $data[10]['TEXT'] =number_format($row['HKE_BYR']); 
            $data[11]['TEXT'] =number_format($row['HKNE_BYR']);
            $data[12]['TEXT'] =number_format($row['TTL_BYR']);
            $data[13]['TEXT'] =number_format($row['PREMI_LEMBUR']);
            $data[14]['TEXT'] =number_format($gaji_bruto);
            $data[15]['TEXT'] =number_format($gaji_bruto);
            
            $data[1]['T_ALIGN'] = "C";
            $data[2]['T_ALIGN'] = "L";
            $data[3]['T_ALIGN'] = "C";
            $data[4]['T_ALIGN'] = "L";
            $data[5]['T_ALIGN'] = "C";
            $data[6]['T_ALIGN'] = "L";
            
            $data[7]['T_ALIGN'] = "R";
            $data[8]['T_ALIGN'] = "R";
            $data[9]['T_ALIGN'] = "R";
            $data[10]['T_ALIGN'] = "R";
            $data[11]['T_ALIGN'] = "R";
            $data[12]['T_ALIGN'] = "R";
            $data[13]['T_ALIGN'] = "R";
            $data[14]['T_ALIGN'] = "R";
            $data[15]['T_ALIGN'] = "R";
            
            $pdf->tbDrawData($data);
            
        }
        
        $data_test=array();
        $data_test[0]['TEXT'] = "TOTAL BIAYA";
        $data_test[0]['COLSPAN'] = 7;
        $data_test[7]['TEXT'] = number_format($ttl_hke);
        $data_test[8]['TEXT'] = number_format($ttl_hkne);
        $data_test[9]['TEXT'] = number_format($ttl_hke_ne);
        $data_test[10]['TEXT'] = number_format($ttlbyr_hke);
        $data_test[11]['TEXT'] = number_format($ttlbyr_hkne);
        $data_test[12]['TEXT'] = number_format($ttlbyr_hke_ne);
        $data_test[13]['TEXT'] = number_format($ttl_premi);
        $data_test[14]['TEXT'] = number_format($ttl_gaji_bruto);
        $data_test[15]['TEXT'] = number_format($ttl_upah);
        
        $data_test[0]['T_ALIGN'] = "C";
        $data_test[7]['T_ALIGN'] = "R";
        $data_test[8]['T_ALIGN'] = "R";
        $data_test[9]['T_ALIGN'] = "R";
        $data_test[10]['T_ALIGN'] = "R";
        $data_test[11]['T_ALIGN'] = "R";
        $data_test[12]['T_ALIGN'] = "R";
        $data_test[13]['T_ALIGN'] = "R";
        $data_test[14]['T_ALIGN'] = "R";
        
        $data_test[1]['T_TYPE'] = "B";
        $data_test[7]['T_TYPE'] = "B";
        $data_test[8]['T_TYPE'] = "B";
        $data_test[9]['T_TYPE'] = "B";
        $data_test[10]['T_TYPE'] = "B";
        $data_test[11]['T_TYPE'] = "B";
        $data_test[12]['T_TYPE'] = "B";
        $data_test[13]['T_TYPE'] = "B";
        $data_test[14]['T_TYPE'] = "B";
    
        $pdf->tbDrawData($data_test);
        
        $pdf->tbOuputData();
        $pdf->tbDrawBorder();
        
        $pdf->Ln(7.5);
        //$pdf->AddPage("L","LETTER");
        require_once(APPPATH . 'libraries/daftar_upah/authorize.inc');
        $pdf->Output();
    }
}

?>