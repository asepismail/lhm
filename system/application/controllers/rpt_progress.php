<?
class rpt_progress extends Controller 
{
    function rpt_progress ()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_progress' ); 
        $this->load->helper('form');
        $this->load->helper('language'); 
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('form_validation');
        $this->load->library('global_func');
        $this->load->library('session');
        $this->load->database();
        $this->load->plugin('to_excel');
        require_once(APPPATH . 'libraries/fpdf_table.php');
        require_once(APPPATH . 'libraries/header_footer.inc');
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
        
        if ($data['login_id'] == TRUE){
            
                $this->load->view('rpt_progress', $data);
             
        } else {
            redirect('login');
        }
        
    } 
    
    function dropdownlist_afd()
    {
    
        $string = "<select  name='afd' class='select'  id='afd' >";
        $string .= "<option value='all'> -- semua -- </option>";
        
        $data_afd = $this->model_rpt_progress->get_afdeling($this->session->userdata('DCOMPANY'));
        
        foreach ( $data_afd as $row)
        {
            if( (isset($default)) && ($default==$row[$nama_isi]) )
            {
                $string = $string." <option value=\"".$row['AFD']."\"  selected>".$row['AFD']." </option>";
            }
            else
            {
                $string = $string." <option value=\"".$row['AFD']."\">".$row['AFD']." </option>";
            }
        }
        
        $string =$string. "</select>";
        return $string;
    }
    
    // ------------------------------ rawat punya ------------------------------------------------- //
    function js_progress(){
        
        $js = "jQuery('#submitdata').click(function (){
            var tdate = document.getElementById('TGL').value;
            var tdateto = document.getElementById('TO').value;

            var tgl = tdate.replace(/-/gi, '');
            var to = tdateto.replace(/-/gi, '');
            
            var afd = $('#afd').val();
            var jns_laporan = $('#jns_laporan').val();        
            var tipe_laporan =$('#tipe_laporan').val();
            
            if (tgl =='' || to =='')
            {
                alert('rentang periode salah!!!');
            }
            else
            {
                
                if (tipe_laporan=='html')
                {
                    if ( jns_laporan == 'panen'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/PNN' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'rawat'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/RWT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'trans_panen'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/TP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'bibitan'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/BBT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'sisip'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/SSP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'tanam'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/TNM' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'rwtif'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/RWTIF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_inf'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/PJINF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_bibitan'){
                        urls = url + 'rpt_progress/lpph/' + afd + '/' + tgl +'/PJBBT' + '/' + to; 
                        $('#frame').attr('src',urls);
                    } 
                }
                else if (tipe_laporan=='excell')
                {
                    if ( jns_laporan == 'panen'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/PNN' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'rawat'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/RWT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'trans_panen'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/TP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'bibitan'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/BBT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'sisip'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/SSP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'tanam'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/TNM' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'rwtif'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/RWTIF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_inf'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/PJINF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_bibitan'){
                        urls = url + 'rpt_progress/lpphtoxls/' + afd + '/' + tgl +'/PJBBT' + '/' + to; 
                        $('#frame').attr('src',urls);
                    }
                }
                else if (tipe_laporan=='pdf')
                {
                    if ( jns_laporan == 'panen'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/PNN' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'rawat'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/RWT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'trans_panen'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/TP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'bibitan'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/BBT' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    } else if ( jns_laporan == 'sisip'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/SSP' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'tanam'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/TNM' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'rwtif'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/RWTIF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_inf'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/PJINF' + '/' + to; 
                        $('#frame').attr('src',urls); 
                    }else if ( jns_laporan == 'pj_bibitan'){
                        urls = url + 'rpt_progress/lpphtopdf/' + afd + '/' + tgl +'/PJBBT' + '/' + to; 
                        $('#frame').attr('src',urls);
                    }
                }
            }
            
                    
            
        });
        
        ";
        return $js;
    }
    
    function progress(){
        $view = "rpt_progress";
        $data = array();
        $data['judul_header'] = "LAPORAN PERINCIAN PEKERJAAN HARIAN";
        $data['js'] = $this->js_progress();    
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['AFD'] = $this->dropdownlist_afd();
        
        if ($data['login_id'] == TRUE){
            //if ($data['user_level'] == 'SAD'){
                //$this->load->view('rpt_ba_rawat', $data);
                show($view, $data);
            //} 
        } else {
            redirect('login');
        }
        
    }
    
    
    function lpph()
    {
        $company = $this->session->userdata('DCOMPANY');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $to = $this->uri->segment(6);
        //$data_prog = [];
        
        if ($jns == 'PNN') {
            $data_prog = $this->model_rpt_progress->gen_prog_panen_detail($tgl,$afd, $company,$to);
        } else if ($jns == 'RWT') {
            $data_prog = $this->model_rpt_progress->gen_prog_rawat_detail($tgl,$afd, $company,$to);
        } else if ($jns == 'TP') {
            $data_prog = $this->model_rpt_progress->gen_prog_tp_detail($tgl,$afd, $company,$to);
        } else if ($jns == 'BBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_bibitan_detail($tgl,$afd, $company,$to);
        } else if ($jns == 'SSP') {
            $data_prog = $this->model_rpt_progress->gen_prog_sisip_detail($tgl,$afd, $company,$to);
        }else if ($jns == 'TNM') {
            $data_prog = $this->model_rpt_progress->gen_prog_tanam_detail($tgl,$afd, $company,$to);
        }else if ($jns == 'RWTIF') {
            $data_prog = $this->model_rpt_progress->gen_prog_rwtinf_detail($tgl, $company,$to,'');
        }else if ($jns == 'PJINF') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjinf_detail($tgl,$afd, $company,$to);
        }else if ($jns == 'PJBBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjbbt_detail($tgl,$afd, $company,$to);
        }
        
        $tabel = "";
        $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $tabel .= "    .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $tabel .= "    .tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
         $tabel .= "    .tbl_2 { font-size: 12px;color:#678197; } ";
        $tabel .= "    .content { font-size: 12px;color:#678197; } </style>";
        $tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>LAPORAN PERINCIAN PEKERJAAN HARIAN
                    </strong></td>";
        $tabel .= "</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / PROGRESS / ".$company." / ".substr($tgl,4,2)." / ".        substr($tgl,0,4)."</strong></td> ";
        $tabel .= "</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper(substr($tgl,4,2))." &nbsp;" .substr($tgl,0,4). "</strong></td>
</tr><tr><td colspan='3'>&nbsp;</td> ";
        $tabel .= "</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
        
        $tabel .= "<table width='100%' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' cellpadding='0' cellspacing='0'>";
        $tabel .= "
  <tr>
  
        
       <td align='center' width='2%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>No</td>
      <td align='center' width='7%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>KODE ITEM KERJA</td>
      <td align='center' width='15%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>NAMA ITEM KERJA</td>
       <td align='center' width='7%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>BLOK</td>
    <td align='center' width='4%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>SAT</td>
    <td align='center' width='12%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>HASIL KERJA</td>
    <td align='center' width='12%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>TENAGA KERJA (HK)</td>
    <td align='center' width='15%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>REALISASI BIAYA (Rp)</td>
    <td align='center' width='13%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>RP / SATUAN</td>
    <td align='center' width='13%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>HK / SATUAN</td>
  </tr>
  <tr>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
  </tr>
";
        $i=1;
        $tmpAccode='';
        $curAccode='';
        $totalHkHi=0;
        $totalHkShi=0;
        $totalRbHi=0;
        $totalRbShi=0;
        $url = base_url().'index.php/rpt_progress/';
        foreach ( $data_prog as $row)
        {
        
            /*$tabel .= "<tr><td width='30px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;&nbsp;".$i."&nbsp;&nbsp;</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; 
            border-right:1px solid'>&nbsp;&nbsp;<a href='".$url."lpph_breakdown/".$afd."/".$row['ACCOUNTCODE']."/".$tgl."/".$jns."/".$to."' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><strong>"
            .$row['ACCOUNTCODE']."</strong></a>
            </td>*/
            if ($tmpAccode!='')
            {
                if ($row['ACCOUNTCODE'] != $tmpAccode)
                {
                    if ($jns == 'PNN') {
                         $datatotal = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'RWT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_rawat($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'TP') {
                         $datatotal = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'BBT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'SSP') {
                         $datatotal = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'TNM') {
                         $datatotal = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'RWTIF') {
                         $datatotal = $this->model_rpt_progress->gen_prog_rwtinf($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'PJINF') {
                         $datatotal = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'PJBBT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to,$tmpAccode);
                    }
                   
                    foreach($datatotal as $rows)
                    {
                        $tabel .= "<tr><td width='30px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='5'>
                        &nbsp;&nbsp;<strong>TOTAL - ".$rows['ACCOUNTDESC']."</strong>&nbsp;&nbsp;</td>
           
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HSL_KERJA_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HSL_KERJA_SHI'],2)."</strong> &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HK_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HK_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_UNIT_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_UNIT_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_PERHK_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_PERHK_SHI'],2)."</strong>&nbsp;</td> </tr>";
                    $totalHkHi=$totalHkHi + $rows['HK_HI'];
                    $totalHkShi=$totalHkShi + $rows['HK_SHI'];
                    $totalRbHi=$totalRbHi + $rows['REALISASI_HI'];
                    $totalRbShi=$totalRbShi + $rows['REALISASI_SHI'];
                    }
                    $datatotal=''; 
                }  
            }
            
            $tabel .= "<tr><td width='30px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;&nbsp;
            ".$i."&nbsp;&nbsp;</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; 
            border-right:1px solid'>&nbsp;&nbsp;"
            .$row['ACCOUNTCODE']."
            </td>
            <td align='left' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid' width='400px'>&nbsp;&nbsp;"
            .$row['ACCOUNTDESC']."</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;&nbsp;"
            .$row['LOCATION']."&nbsp;&nbsp;</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$row['UNIT1']."</td>
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HSL_KERJA_HI'],2)." 
            &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HSL_KERJA_SHI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'> ". number_format($row['HK_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HK_SHI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_SHI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_UNIT_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_UNIT_SHI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_PERHK_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_PERHK_SHI'],2)." &nbsp;</td> </tr>";
            $tmpAccode = $row['ACCOUNTCODE'];
            $i=$i+1;
        }
        
        if ($tmpAccode!='')
        {
             if ($jns == 'PNN') {
             $datatotal = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to,$tmpAccode);
        } else if ($jns == 'RWT') {
             $datatotal = $this->model_rpt_progress->gen_prog_rawat($tgl,$afd, $company,$to,$tmpAccode);
        } else if ($jns == 'TP') {
             $datatotal = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to,$tmpAccode);
        } else if ($jns == 'BBT') {
             $datatotal = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to,$tmpAccode);
        } else if ($jns == 'SSP') {
             $datatotal = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to,$tmpAccode);
        }else if ($jns == 'TNM') {
             $datatotal = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to,$tmpAccode);
        }else if ($jns == 'RWTIF') {
             $datatotal = $this->model_rpt_progress->gen_prog_rwtinf($tgl,$afd, $company,$to,$tmpAccode);
        }else if ($jns == 'PJINF') {
             $datatotal = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to,$tmpAccode);
        }else if ($jns == 'PJBBT') {
             $datatotal = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to,$tmpAccode);
        }
        foreach($datatotal as $rows)
        {
            $tabel .= "<tr><td width='30px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='5'>
            &nbsp;&nbsp;<strong>TOTAL - ".$rows['ACCOUNTDESC']."</strong>&nbsp;&nbsp;</td>
           
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HSL_KERJA_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HSL_KERJA_SHI'],2)."</strong> &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HK_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['HK_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_UNIT_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_UNIT_SHI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_PERHK_HI'],2)."</strong>&nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'><strong>"
            . number_format($rows['REALISASI_PERHK_SHI'],2)."</strong>&nbsp;</td> </tr>";
        }
        }
        $datatotal=''; 
        $tabel .= "<tr>
                    <td class='tbl_td' align = 'center' colspan='5'><strong>TOTAL</strong></td>
                    <td class='tbl_td' align = 'center'><strong> - </strong></td>
                    <td class='tbl_td' align = 'center'><strong> - </strong></td>
                    <td class='tbl_td' align = 'right'><strong>".number_format($totalHkHi,2,',','.')."</strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'right'><strong>".number_format($totalHkShi,2,',','.')."</strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'right'><strong>".number_format($totalRbHi,2,',','.')."</strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'right'><strong>".number_format($totalRbShi,2,',','.')."</strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'center'><strong> - </strong></td>
                    <td class='tbl_td' align = 'center'><strong> - </strong></td>
                    <td class='tbl_td' align = 'center'><strong> - </strong>&nbsp;&nbsp;</td>
                    <td class='tbl_td' align = 'center'><strong> - </strong>&nbsp;&nbsp;</td>
                  </tr>";    
        $tabel .= "</table>";
         echo $tabel;
        
        
    }
    function lpph_breakdown()
    {
        $company = $this->session->userdata('DCOMPANY');
        $afd = $this->uri->segment(3);
        $acCode = $this->uri->segment(4);
        $tgl = $this->uri->segment(5);
        $jns = $this->uri->segment(6);
        $to = $this->uri->segment(7);
        //$data_prog = [];
        
        if ($jns == 'PNN') {
            $data_prog = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to);
        } else if ($jns == 'RWT') {
            $data_prog = $this->model_rpt_progress->gen_prog_rawat_detail($tgl,$afd, $company,$to,$acCode);
        } else if ($jns == 'TP') {
            $data_prog = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to);
        } else if ($jns == 'BBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to);
        } else if ($jns == 'SSP') {
            $data_prog = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to);
        }else if ($jns == 'TNM') {
            $data_prog = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to);
        }else if ($jns == 'RWTIF') {
            $data_prog = $this->model_rpt_progress->gen_prog_rwtinf($tgl, $company,$to);
        }else if ($jns == 'PJINF') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to);
        }else if ($jns == 'PJBBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to);
        }
        
        $tabel = "";
        $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $tabel .= "    .tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $tabel .= "    .tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
         $tabel .= "    .tbl_2 { font-size: 12px;color:#678197; } ";
        $tabel .= "    .content { font-size: 12px;color:#678197; } </style>";
        $tabel .= "<table class='tbl_2' border='0' width='85%'><tr><td colspan='3' align='center'><strong>LAPORAN PERINCIAN PEKERJAAN HARIAN
                    </strong></td>";
        $tabel .= "</tr><tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / PROGRESS / ".$company." / ".substr($tgl,4,2)." / ".        substr($tgl,0,4)."</strong></td> ";
        $tabel .= "</tr><tr><td colspan='3' align='center'><strong>PERIODE : ".strtoupper(substr($tgl,4,2))." &nbsp;" .substr($tgl,0,4). "</strong></td>
</tr><tr><td colspan='3'>&nbsp;</td> ";
        $tabel .= "</tr><tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
        
        $tabel .= "<table width='100%' style='font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid;' cellpadding='0' cellspacing='0'>";
        $tabel .= "
  <tr>
  
        
       <td align='center' width='2%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>No</td>
      <td align='center' width='7%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>KODE ITEM KERJA</td>
      <td align='center' width='15%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>NAMA ITEM KERJA</td>
       <td align='center' width='7%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>BLOK</td>
    <td align='center' width='4%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' rowspan='2'>SAT</td>
    <td align='center' width='12%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>HASIL KERJA</td>
    <td align='center' width='12%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>TENAGA KERJA (HK)</td>
    <td align='center' width='15%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>REALISASI BIAYA (Rp)</td>
    <td align='center' width='13%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>RP / SATUAN</td>
    <td align='center' width='13%' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid' colspan='2'>HK / SATUAN</td>
  </tr>
  <tr>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
    <td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>Hari Ini</td>
<td align='center' style='font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid'>S.d Hari Ini</td>
  </tr>
";
        $i=1;
        foreach ( $data_prog as $row)
        {
        /*$tabel .= "<td class='tbl_td' align = 'center'>&nbsp;&nbsp;<a href='".$url."ba_lokasi_breakdown/".$row['LOCATION_CODE']."/".$row['ACCOUNTCODE']."/".$from."/".$to."' style='cursor:pointer;color:#678197; text-decoration: none;' target='_BLANK'><strong>".$row['ACCOUNTCODE']."</strong></a><
        /td>*/
            $tabel .= "<tr><td width='30px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;&nbsp;".$i."&nbsp;&nbsp;</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; 
            border-right:1px solid'>"
            .$row['ACCOUNTCODE']."
            </td>
            <td align='left' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid' width='400px'>&nbsp;&nbsp;"
            .$row['ACCOUNTDESC']."</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>&nbsp;&nbsp;"
            .$row['LOCATION']."&nbsp;&nbsp;</td>
            <td width='80px' align='center' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>".$row['UNIT1']."</td>
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HSL_KERJA_HI'],2)." 
                &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HSL_KERJA_SHI'],2)."                &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HK_HI'],2)." 
                &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['HK_SHI'],2)." 
                &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_HI'],2)."                 &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_SHI'],2)."                &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_UNIT_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_UNIT_SHI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_PERHK_HI'],2)." &nbsp;</td> 
            <td width='160px' align='right' style='font-size: 11px;color:#678197;border-bottom:1px solid; border-right:1px solid'>". number_format($row['REALISASI_PERHK_SHI'],2)." &nbsp;</td> </tr>";
            
            $i=$i+1;
        }
       
        $tabel .= "</table>";
         echo $tabel;
        
        
    }
    
    
    function lpphtoxls()
    {
        $company = $this->session->userdata('DCOMPANY');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $to = $this->uri->segment(6);
        //$data_prog = [];
        
        if ($jns == 'PNN') {
            $data_prog = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to);
        } else if ($jns == 'RWT') {
            $data_prog = $this->model_rpt_progress->gen_prog_rawat($tgl,$afd, $company,$to);
        } else if ($jns == 'TP') {
            $data_prog = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to);
        } else if ($jns == 'BBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to);
        } else if ($jns == 'SSP') {
            $data_prog = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to);
        } else if ($jns == 'TNM') {
            $data_prog = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to);
        }else if ($jns == 'RWTIF') {
            $data_prog = $this->model_rpt_progress->gen_prog_rwtinf($tgl, $company,$to);
        }else if ($jns == 'PJINF') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to);
        }else if ($jns == 'PJBBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to);
        }
        
        $bulan = substr($tgl,-2);
        $tahun = substr($tgl,0,4);
        
        $judul = '';
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below
        $footer = '';
        
        $obj =& get_instance();
        
        $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
        $judul .= "LAPORAN PERINCIAN PEKERJAAN HARIAN .".$jns."\n";
        $judul .= "PERIODE : ".date("Y / m / d",strtotime($tgl))."  SAMPAI DENGAN :  ".date("Y / m / d",strtotime($to))."\n";
        $judul .= "AFDELING : ". strtoupper($afd)."\n";
        
        
        $headers .= "KODE ITEM \t";
        $headers .= "NAMA ITEM KERJA \t";
        $headers .= "BLOK \t";
        $headers .= "SAT \t";
        $headers .= "HASIL KERJA HARI INI \t";    
        $headers .= "HASIL KERJA S.D HARI INI \t";
        $headers .= "TENAGA KERJA HARI INI \t";    
        $headers .= "TENAGA KERJA S.D HARI INI \t";
        $headers .= "REALISASI BIAYA HARI INI \t";
        $headers .= "REALISASI BIAYA S.D HARI INI \t";
        $headers .= "Rp / SAT HARI INI \t";
        $headers .= "Rp / SAT S.D HARI INI \t";
        $headers .= "HK / SAT HARI INI\t";
        $headers .= "HK / SAT S.D HARI INI \t";
            
        foreach ( $data_prog as $row)
        {
            $line = '';
                    
            $line .= str_replace('"', '""',trim($row['ACCOUNTCODE']))."\t";
            $line .= str_replace('"', '""',trim($row['ACCOUNTDESC']))."\t";
            $line .= str_replace('"', '""',trim($row['LOCATION']))."\t";
            $line .= str_replace('"', '""',trim($row['UNIT1']))."\t";
            $line .= str_replace('"', '""',trim($row['HSL_KERJA_HI']))."\t";
            $line .= str_replace('"', '""',trim($row['HSL_KERJA_SHI']))."\t";
            $line .= str_replace('"', '""',trim($row['HK_HI']))."\t";
            $line .= str_replace('"', '""',trim($row['HK_SHI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_HI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_SHI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_UNIT_HI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_UNIT_SHI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_PERHK_HI']))."\t";
            $line .= str_replace('"', '""',trim($row['REALISASI_PERHK_SHI']))."\t";
            
            $data .= trim($line)."\n";        
        }
            
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " -\t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";;
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";    
        $footer .= " - \t";
        $footer .= " - \t";
        $footer .= " - \t";
        
        $data .= trim($footer)."\n";
        $data = str_replace("\r","",$data);
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=PRG_PEKERJAANHARIAN_".$company."_".strtoupper($afd)."_".$tgl.
                            ".xls");
        echo "$judul\n$headers\n$data";  
    }
    
    function lpphtopdf()
    {
        if ($this->session->userdata('logged_in')!= TRUE)
        {
            redirect('login');
        }
        
        $company = $this->session->userdata('DCOMPANY');
        $company_name= $this->session->userdata('DCOMPANY_NAME');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $to = $this->uri->segment(6);
        
        $totalHkHi=0;
        $totalHkShi=0;
        $totalRbHi=0;
        $totalRbShi=0;
        
        if ($jns == 'PNN') {
            $data_prog = $this->model_rpt_progress->gen_prog_panen_detail($tgl,$afd, $company,$to,'');
        } else if ($jns == 'RWT') {
            $data_prog = $this->model_rpt_progress->gen_prog_rawat_detail($tgl,$afd, $company,$to,'');
        } else if ($jns == 'TP') {
            $data_prog = $this->model_rpt_progress->gen_prog_tp_detail($tgl,$afd, $company,$to,'');
        } else if ($jns == 'BBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_bibitan_detail($tgl,$afd, $company,$to,'');
        } else if ($jns == 'SSP') {
            $data_prog = $this->model_rpt_progress->gen_prog_sisip_detail($tgl,$afd, $company,$to,'');
        }else if ($jns == 'TNM') {
            $data_prog = $this->model_rpt_progress->gen_prog_tanam_detail($tgl,$afd, $company,$to,'');
        }else if ($jns == 'RWTIF') {
            $data_prog = $this->model_rpt_progress->gen_prog_rwtinf_detail($tgl, $company,$to,'');
        }else if ($jns == 'PJINF') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjinf_detail($tgl,$afd, $company,$to,'');
        }else if ($jns == 'PJBBT') {
            $data_prog = $this->model_rpt_progress->gen_prog_pjbbt_detail($tgl,$afd, $company,$to,'');
        }
        
        $pdf = new pdf_usage();
        $pdf->Open();
        $pdf->SetAutoPageBreak(TRUE,10);
        $pdf->SetMargins(5,15);
        $pdf->AddPage("L","A4");
        $pdf->AliasNbPages(); 
        if ($to=='')
        {
            $to2=$tgl;
        }
        else{
             $to2=$to;
        }
        
        require_once(APPPATH . 'libraries/ba/header_progress.inc');
        
        require_once(APPPATH . 'libraries/ba/table_border.inc');
        
        $columns = 15; //number of Columns
        $pdf->tbInitialize($columns, true, true);
        $pdf->tbSetTableType($table_default_table_type);
        
        $aSimpleHeader = array(); 
        for($i=0; $i<=$columns; $i++) {
            $aSimpleHeader[$i] = $table_default_header_type;
            if($i == 0) {
                $aSimpleHeader[$i]['TEXT'] = "No";
                $aSimpleHeader[$i]['WIDTH'] = 7;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            
            if($i == 1) {
                $aSimpleHeader[$i]['TEXT'] = "Kode Item Kerja";
                $aSimpleHeader[$i]['WIDTH'] = 15;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 2) {
                $aSimpleHeader[$i]['TEXT'] = "Nama Item Kerja";
                $aSimpleHeader[$i]['WIDTH'] = 65;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 3) {
                $aSimpleHeader[$i]['TEXT'] = "Blok";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 4) {
                $aSimpleHeader[$i]['TEXT'] = "SAT";
                $aSimpleHeader[$i]['WIDTH'] = 17;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 5) {
                $aSimpleHeader[$i]['TEXT'] = "Hasil Kerja";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 6) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 7) {
                $aSimpleHeader[$i]['TEXT'] = "Tenaga Kerja (HK)";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 8) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 9) {
                $aSimpleHeader[$i]['TEXT'] = "Realisasi Biaya";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 10) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 11) {
                $aSimpleHeader[$i]['TEXT'] = "Rp / Satuan";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 12) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
            }
            if($i == 13) {
                $aSimpleHeader[$i]['TEXT'] = "HK / Satuan";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 14) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 22;
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
                $aSimpleHeader2[$i]['TEXT'] = "Hari Ini";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 6) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 7) {
                $aSimpleHeader2[$i]['TEXT'] = "Hari Ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 8) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 9) {
                $aSimpleHeader2[$i]['TEXT'] = "Hari Ini";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 10) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 11) {
                $aSimpleHeader2[$i]['TEXT'] = "Hari Ini";
                $aSimpleHeader2[$i]['WIDTH'] = 22;
            }
            if($i == 12) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 13) {
                $aSimpleHeader2[$i]['TEXT'] = "Hari Ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            if($i == 14) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 28;
            }
            
        }
        
        $aHeader = array( $aSimpleHeader, $aSimpleHeader2);
        
        $pdf->tbSetHeaderType($aHeader, TRUE);
        
        $pdf->tbDrawHeader();
        $aDataType = Array();
        for ($i=0; $i<$columns; $i++) $aDataType[$i] = $table_default_data_type;
        $pdf->tbSetDataType($aDataType);

        $i=1;
        
        $tmpAccode='';
        $currAccode='';
        foreach ($data_prog as $row)
        {
            $data = Array();
            
            if ($tmpAccode!='')
            {
                if ($row['ACCOUNTCODE'] != $tmpAccode)
                {
                     if ($jns == 'PNN') {
                         $datatotal = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'RWT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_rawat($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'TP') {
                         $datatotal = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'BBT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to,$tmpAccode);
                    } else if ($jns == 'SSP') {
                         $datatotal = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'TNM') {
                         $datatotal = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'RWTIF') {
                         $datatotal = $this->model_rpt_progress->gen_prog_rwtinf($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'PJINF') {
                         $datatotal = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to,$tmpAccode);
                    }else if ($jns == 'PJBBT') {
                         $datatotal = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to,$tmpAccode);
                    }
                    
                    foreach($datatotal as $rows)
                    {
                        $data2 = array();
                        $data2[0]['TEXT'] = "TOTAL - " .$rows['ACCOUNTDESC'];
                        $data2[0]['COLSPAN']=5;
                        $data2[5]['TEXT'] = number_format($rows['HSL_KERJA_HI'],2,',','.');
                        $data2[6]['TEXT'] = number_format($rows['HSL_KERJA_SHI'],2,',','.');
                        $data2[7]['TEXT'] = number_format($rows['HK_HI'],2,',','.');
                        $data2[8]['TEXT'] = number_format($rows['HK_SHI'],2,',','.');
                        $data2[9]['TEXT'] = number_format($rows['REALISASI_HI'],2,',','.');
                        $data2[10]['TEXT'] = number_format($rows['REALISASI_SHI'],2,',','.');
                        $data2[11]['TEXT'] = number_format($rows['REALISASI_UNIT_HI'],2,',','.');
                        $data2[12]['TEXT'] = number_format($rows['REALISASI_UNIT_SHI'],2,',','.');
                        $data2[13]['TEXT'] = number_format($rows['REALISASI_PERHK_HI'],2,',','.');
                        $data2[14]['TEXT'] = number_format($rows['REALISASI_PERHK_SHI'],2,',','.');
                        
                        $data2[5]['T_ALIGN'] = "R";
                        $data2[6]['T_ALIGN'] = "R";
                        $data2[7]['T_ALIGN'] = "R";
                        $data2[8]['T_ALIGN'] = "R";
                        $data2[9]['T_ALIGN'] = "R";
                        $data2[10]['T_ALIGN'] = "R";
                        $data2[11]['T_ALIGN'] = "R";
                        $data2[12]['T_ALIGN'] = "R";
                        $data2[13]['T_ALIGN'] = "R";
                        $data2[14]['T_ALIGN'] = "R";
                        
                        $data2[0]['T_TYPE'] = "B";
                        $data2[5]['T_TYPE'] = "B";
                        $data2[6]['T_TYPE'] = "B";
                        $data2[7]['T_TYPE'] = "B";
                        $data2[8]['T_TYPE'] = "B";
                        $data2[9]['T_TYPE'] = "B";
                        $data2[10]['T_TYPE'] = "B";
                        $data2[11]['T_TYPE'] = "B";
                        $data2[12]['T_TYPE'] = "B";
                        $data2[13]['T_TYPE'] = "B";
                        $data2[14]['T_TYPE'] = "B"; 
                    }
                    $datatotal=''; 
                    
                    $pdf->tbDrawData($data2);
                }  
            }

            $totalHkHi=$totalHkHi + $row['HK_HI'];
            $totalHkShi=$totalHkShi + $row['HK_SHI'];
            $totalRbHi=$totalRbHi + $row['REALISASI_HI'];
            $totalRbShi=$totalRbShi + $row['REALISASI_SHI'];
            
            $currAccode= $row['ACCOUNTCODE'];
            $data[0]['TEXT'] = $i;    
            $data[1]['TEXT'] = $row['ACCOUNTCODE'];
            $data[2]['TEXT'] = $row['ACCOUNTDESC'];
            $data[3]['TEXT'] = $row['LOCATION'];
            $data[4]['TEXT'] = $row['UNIT1'];
            $data[5]['TEXT'] = number_format($row['HSL_KERJA_HI'],2,',','.');
            $data[6]['TEXT'] = number_format($row['HSL_KERJA_HI'],2,',','.');
            $data[7]['TEXT'] = number_format($row['HK_HI'],2,',','.');
            $data[8]['TEXT'] = number_format($row['HK_SHI'],2,',','.');
            $data[9]['TEXT'] = number_format($row['REALISASI_HI'],2,',','.');
            $data[10]['TEXT'] = number_format($row['REALISASI_SHI'],2,',','.');
            $data[11]['TEXT'] = number_format($row['REALISASI_UNIT_HI'],2,',','.');
            $data[12]['TEXT'] = number_format($row['REALISASI_UNIT_SHI'],2,',','.');
            $data[13]['TEXT'] = number_format($row['REALISASI_PERHK_HI'],2,',','.');
            $data[14]['TEXT'] = number_format($row['REALISASI_PERHK_SHI'],2,',','.');
            
            $data[0]['T_ALIGN'] = "C";
            $data[1]['T_ALIGN'] = "C";
            $data[2]['T_ALIGN'] = "L";
            $data[3]['T_ALIGN'] = "C";
            $data[4]['T_ALIGN'] = "C";
            $data[5]['T_ALIGN'] = "R";
            $data[6]['T_ALIGN'] = "R";
            $data[7]['T_ALIGN'] = "R";
            $data[8]['T_ALIGN'] = "R";
            $data[9]['T_ALIGN'] = "R";
            $data[10]['T_ALIGN'] = "R";
            $data[11]['T_ALIGN'] = "R";
            $data[12]['T_ALIGN'] = "R";
            $data[13]['T_ALIGN'] = "R";
            $data[14]['T_ALIGN'] = "R";
            $tmpAccode = $row['ACCOUNTCODE'];

            $i=$i+1;
            $pdf->tbDrawData($data);
        }
        
         if ($jns == 'PNN') {
                 $datatotal = $this->model_rpt_progress->gen_prog_panen($tgl,$afd, $company,$to,$currAccode);
            } else if ($jns == 'RWT') {
                 $datatotal = $this->model_rpt_progress->gen_prog_rawat($tgl,$afd, $company,$to,$currAccode);
            } else if ($jns == 'TP') {
                 $datatotal = $this->model_rpt_progress->gen_prog_tp($tgl,$afd, $company,$to,$currAccode);
            } else if ($jns == 'BBT') {
                 $datatotal = $this->model_rpt_progress->gen_prog_bibitan($tgl,$afd, $company,$to,$currAccode);
            } else if ($jns == 'SSP') {
                 $datatotal = $this->model_rpt_progress->gen_prog_sisip($tgl,$afd, $company,$to,$currAccode);
            }else if ($jns == 'TNM') {
                 $datatotal = $this->model_rpt_progress->gen_prog_tanam($tgl,$afd, $company,$to,$currAccode);
            }else if ($jns == 'RWTIF') {
                 $datatotal = $this->model_rpt_progress->gen_prog_rwtinf($tgl,$afd, $company,$to,$currAccode);
            }else if ($jns == 'PJINF') {
                 $datatotal = $this->model_rpt_progress->gen_prog_pjinf($tgl,$afd, $company,$to,$currAccode);
            }else if ($jns == 'PJBBT') {
                 $datatotal = $this->model_rpt_progress->gen_prog_pjbbt($tgl,$afd, $company,$to,$currAccode);
            }
        foreach($datatotal as $rows)
        {
                $data3 = array();
                $data3[0]['TEXT'] = "TOTAL - " .$rows['ACCOUNTDESC'];
                $data3[0]['COLSPAN']=5;
                $data3[5]['TEXT'] = number_format($rows['HSL_KERJA_HI'],2,',','.');
                $data3[6]['TEXT'] = number_format($rows['HSL_KERJA_SHI'],2,',','.');
                $data3[7]['TEXT'] = number_format($rows['HK_HI'],2,',','.');
                $data3[8]['TEXT'] = number_format($rows['HK_SHI'],2,',','.');
                $data3[9]['TEXT'] = number_format($rows['REALISASI_HI'],2,',','.');
                $data3[10]['TEXT'] = number_format($rows['REALISASI_SHI'],2,',','.');
                $data3[11]['TEXT'] = number_format($rows['REALISASI_UNIT_HI'],2,',','.');
                $data3[12]['TEXT'] = number_format($rows['REALISASI_UNIT_SHI'],2,',','.');
                $data3[13]['TEXT'] = number_format($rows['REALISASI_PERHK_HI'],2,',','.');
                $data3[14]['TEXT'] = number_format($rows['REALISASI_PERHK_SHI'],2,',','.');
                
                $data3[5]['T_ALIGN'] = "R";
                $data3[6]['T_ALIGN'] = "R";
                $data3[7]['T_ALIGN'] = "R";
                $data3[8]['T_ALIGN'] = "R";
                $data3[9]['T_ALIGN'] = "R";
                $data3[10]['T_ALIGN'] = "R";
                $data3[11]['T_ALIGN'] = "R";
                $data3[12]['T_ALIGN'] = "R";
                $data3[13]['T_ALIGN'] = "R";
                $data3[14]['T_ALIGN'] = "R";
                
                $data3[0]['T_TYPE'] = "B";
                $data3[5]['T_TYPE'] = "B";
                $data3[6]['T_TYPE'] = "B";
                $data3[7]['T_TYPE'] = "B";
                $data3[8]['T_TYPE'] = "B";
                $data3[9]['T_TYPE'] = "B";
                $data3[10]['T_TYPE'] = "B";
                $data3[11]['T_TYPE'] = "B";
                $data3[12]['T_TYPE'] = "B";
                $data3[13]['T_TYPE'] = "B";
                $data3[14]['T_TYPE'] = "B"; 
                $pdf->tbDrawData($data3);
        }
       // $datatotal=''; 
        $data4 = Array();
        $data4[0]['TEXT'] = "TOTAL";
        $data4[0]['COLSPAN']=5;
        $data4[7]['TEXT'] = number_format($totalHkHi,2,',','.');
        $data4[8]['TEXT'] = number_format($totalHkShi,2,',','.');
        $data4[9]['TEXT'] = number_format($totalRbHi,2,',','.');
        $data4[10]['TEXT'] = number_format($totalRbShi,2,',','.');
       
        $data4[5]['T_ALIGN'] = "R";
        $data4[6]['T_ALIGN'] = "R";
        $data4[7]['T_ALIGN'] = "R";
        $data4[8]['T_ALIGN'] = "R";
        $data4[9]['T_ALIGN'] = "R";
        $data4[10]['T_ALIGN'] = "R";
        $data4[11]['T_ALIGN'] = "R";
        $data4[12]['T_ALIGN'] = "R";
        $data4[13]['T_ALIGN'] = "R";
        $data4[14]['T_ALIGN'] = "R";
        
        $data4[0]['T_TYPE'] = "B";
        $data4[5]['T_TYPE'] = "B";
        $data4[6]['T_TYPE'] = "B";
        $data4[7]['T_TYPE'] = "B";
        $data4[8]['T_TYPE'] = "B";
        $data4[9]['T_TYPE'] = "B";
        $data4[10]['T_TYPE'] = "B";
        $data4[11]['T_TYPE'] = "B";
        $data4[12]['T_TYPE'] = "B";
        $data4[13]['T_TYPE'] = "B";
        $data4[14]['T_TYPE'] = "B"; 
        $pdf->tbDrawData($data4);
        
        $pdf->tbOuputData();
        $pdf->tbDrawBorder();
        
        $pdf->Ln(4);
        //$pdf->AddPage("L","LETTER");
        require_once(APPPATH . 'libraries/daftar_upah/authorize_prg.inc');
        $pdf->Output();
    }
}

?>