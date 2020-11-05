<?
class rpt_progress_n extends Controller 
{
    function rpt_progress_n ()
    {
        parent::Controller();    

        $this->load->model( 'model_rpt_progress_n' ); 
        
        $this->load->model('model_c_user_auth');
        $this->lastmenu="rpt_progress_n";
        
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
        $view = "rpt_progress_n";
        $data = array();
        $data['judul_header'] = "Laporan Perincian Pekerjaan Harian";
        
        $data['js'] = $this->js_progress();    
        $data['login_id'] = $this->session->userdata('LOGINID');
        $data['company_name'] = $this->session->userdata('NCOMPANY_NAME');
        $data['company_code'] = $this->session->userdata('DCOMPANY');
        $data['company_dest'] = $this->session->userdata('DCOMPANY_NAME');
        $data['user_level'] = $this->session->userdata('USER_LEVEL');
        $data['AFD'] = $this->dropdownlist_afd();
        
        $data['menu']=$this->model_c_user_auth->get_menu($data['login_id'],$data['user_level'],$data['company_code'],$this->lastmenu);
        
        if ($data['login_id'] == TRUE){
                show($view, $data);
            } else {
                redirect('login');
            }
        
    } 
    
    function dropdownlist_afd()
    {
    
        $string = "<select  name='afd' class='select'  id='afd' >";
        $string .= "<option value='all'> -- semua -- </option>";
        
        $data_afd = $this->model_rpt_progress_n->get_afdeling($this->session->userdata('DCOMPANY'));
        
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
            
            var tgl = tdate.replace(/-/gi, '');
            var afd = $('#afd').val();
            var jns_laporan = $('#jns_laporan').val();        
            var tipe_laporan =$('#tipe_laporan').val();
            
            if (tipe_laporan=='html')
                {
                     urls = url + 'rpt_progress_n/lpph/' + afd + '/' + tgl + '/' + jns_laporan; 
                     $('#frame').attr('src',urls);   
                }  
                else if (tipe_laporan=='pdf')
                {
                    if( jns_laporan == 'tehnik' ) {
                        urls = url + 'rpt_progress_n/lpphtekniktopdf/' + afd + '/' + tgl + '/' + jns_laporan;             
                    } else {
                         urls = url + 'rpt_progress_n/lpphtopdf/' + afd + '/' + tgl + '/' + jns_laporan; 
                    }
                     $('#frame').attr('src',urls);   
                } 
                else if (tipe_laporan=='excell')
                {
                    if( jns_laporan == 'tehnik' ) {
                        urls = url + 'rpt_progress_n/lpphtekniktoxls/' + afd + '/' + tgl + '/' + jns_laporan; 
                    } else {
                         urls = url + 'rpt_progress_n/lpphtoxls/' + afd + '/' + tgl + '/' + jns_laporan; 
                    }
                    $('#frame').attr('src',urls);   
                }
            });";
        return $js;
    }
    
    function lpph()
    {
        $company = $this->session->userdata('DCOMPANY');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $hari = substr($tgl,6,2);
        $bulan = substr($tgl,4,2);
        $tahun = substr($tgl,0,4);

        if($bulan== '01'){ $bulan = "Januari"; $bulanr = "I";} else if($bulan== '02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan== '03'){ $bulan = "Maret"; $bulanr = "III"; } else if($bulan== '04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan== '05'){ $bulan = "Mei"; $bulanr = "V"; } else if($bulan== '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan== '07'){ $bulan = "Juli"; $bulanr = "VII"; } else if($bulan== '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan== '09'){ $bulan = "September"; $bulanr = "IX"; } else if($bulan== '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan== '11'){ $bulan = "Nopember"; $bulanr = "XI"; } else if($bulan== '12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $judul = "";
        if ($jns == 'rawat'){ $judul .= "RAWAT"; } else if ($jns == 'panen') { $judul .= "PANEN"; }
        else if ($jns == 'trans_panen'){ $judul .= "TRANSPORT PANEN"; } else if ($jns == 'bibitan') {  $judul .= "BIBITAN"; }
        else if ($jns == 'sisip'){ $judul .= "SISIP";  } else if ($jns == 'tanam') {  $judul .= "PROJECT TANAM"; }
        else if ($jns == 'rwtif'){ $judul .= "RAWAT INFRASTRUKTUR"; }
        else if ($jns == 'pj_inf'){ $judul .= "PROJECT INFRASTRUKTUR"; }
        else if ($jns == 'pj_bibitan'){ $judul .= "PROJECT PERSIAPAN BIBITAN"; }
        else if ($jns == 'umum'){ $judul .= "UMUM"; }
        else if ($jns == 'lc'){ $judul .= "PROJECT LAND PREPARATION"; }
        else if (strtolower($jns) == 'tehnik') {  $judul .= "TEKNIK";    }
        //########## UPDATE 16 Desember #############
      if (strtolower($jns)=='tehnik')
        {
            $this->lpph_tehnik($company,$afd,$tgl,$hari,$bulan,$tahun,$judul,$bulanr);
        }
      else
        {
            $data_prog = $this->model_rpt_progress_n->gen_prog_detail($tgl, $afd, $jns, $company);
        
            $tabel = "";
            $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
            $tabel .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
            $tabel .= ".tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid; padding-right:6px; padding-left:6px; }";
            $tabel .= ".tbl_2 { font-size: 12px;color:#678197; } ";
            $tabel .= ".content { font-size: 12px;color:#678197; } </style>";
            $tabel .= "<table class='tbl_2' border='0' width='100%'>";
            $tabel .= "<tr><td colspan='3' align='center'><strong>LAPORAN PERINCIAN PEKERJAAN HARIAN - ".$judul."</strong></td></tr>";
            $tabel .= "<tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp; / PROGRESS / ".$company." / ". strtoupper($bulanr) ." / ".$tahun."</strong></td></tr>";
            $tabel .= "<tr><td colspan='3' align='center'><strong>".$hari." ".strtoupper($bulan)."&nbsp;".$tahun."</strong></td></tr><tr><td colspan='3'>&nbsp;</td></tr>";
            $tabel .= "<tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
            
            $tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
            $tabel .= "<tr><td align='center' width='2%' class='tbl_td' rowspan='2'>No</td>";
            $tabel .= "<td align='center' width='7%' class='tbl_td' rowspan='2'>KODE ITEM KERJA</td>";
            $tabel .= "<td align='center' width='18%' class='tbl_td' rowspan='2'>NAMA ITEM KERJA</td>";
            $tabel .= "<td align='center' width='6%' class='tbl_td' rowspan='2'>LOKASI</td>";
            $tabel .= "<td align='center' width='4%' class='tbl_td' rowspan='2'>SAT</td>";
            $tabel .= "<td align='center' width='11%' class='tbl_td' colspan='2'>HASIL KERJA</td>";
            $tabel .= "<td align='center' width='12%' class='tbl_td' colspan='2'>TENAGA KERJA (HK)</td>";
            $tabel .= "<td align='center' width='15%' class='tbl_td' colspan='2'>REALISASI BIAYA (Rp)</td>";
            $tabel .= "<td align='center' width='11%' class='tbl_td' colspan='2'>RP / SATUAN</td>";
            $tabel .= "<td align='center' width='11%' class='tbl_td' colspan='2'>HK / SATUAN</td></tr>";
            
            $tabel .= "<tr><td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td>";
            $tabel .= "<td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td>";
            $tabel .= "<td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td>";
            $tabel .= "<td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td>";
            $tabel .= "<td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td></tr>";
    
            $i=1;
            $tmpAccode='';
            $curAccode='';
            $totalHkHi=0;
            $totalHkShi=0;
            $totalRbHi=0;
            $totalRbShi=0;
            $url = base_url().'index.php/rpt_progress_n/';
            
            foreach ( $data_prog as $row)
            {
                if ($tmpAccode!='')
                {
                    if ($row['ACCOUNTCODE'] != $tmpAccode)
                    {
                        
                       $datatotal = $this->model_rpt_progress_n->gen_prog($tgl, $afd, $company, $jns, $tmpAccode);
                       
                        foreach($datatotal as $rows)
                        {
                            $tabel .= "<tr style='background-color:#E0FFFF'><td width='30px' align='left' class='tbl_td' colspan='5'>&nbsp;&nbsp;<strong>TOTAL - ".$rows['ACCOUNTDESC']."</strong></td>";
               
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_HI'],2)."</strong></td>"; 
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_SHI'],2)."</strong></td>"; 
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HK_HI'],2)."</strong></td>";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HK_SHI'],2)."</strong></td>";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_HI'],2)."</strong></td> ";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_SHI'],2)."</strong></td> ";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_UNIT_HI'],2)."</strong></td>";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_UNIT_SHI'],2)."</strong></td>";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_PERHK_HI'],2)."</strong></td>";
         $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_PERHK_SHI'],2)."</strong></td></tr>";
                        }
                        $datatotal=''; 
                    }  
                }
                
                $tabel .= "<tr><td width='30px' align='center' class='tbl_td'>".$i."</td>";
                $tabel .= "<td width='80px' align='center' class='tbl_td'>".$row['ACCOUNTCODE']."</td>";
                $tabel .= "<td align='left' class='tbl_td' width='400px'>".$row['ACCOUNTDESC']."</td>";
                $tabel .= "<td width='80px' align='center' class='tbl_td'>".$row['LOCATION']."</td>";
                $tabel .= "<td width='80px' align='center' class='tbl_td'>".$row['UNIT1']."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['HSL_KERJA_HI'],2)."</td> ";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['HSL_KERJA_SHI'],2)."</td> ";
                $tabel .= "<td width='160px' align='right' class='tbl_td'> ". number_format($row['HK_HI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['HK_SHI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_HI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_SHI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_UNIT_HI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_UNIT_SHI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_PERHK_HI'],2)."</td>";
                $tabel .= "<td width='160px' align='right' class='tbl_td'>". number_format($row['REALISASI_PERHK_SHI'],2)."</td></tr>";
                        
                $tmpAccode = $row['ACCOUNTCODE'];
                $totalHkHi=$totalHkHi + $row['HK_HI'];
                $totalHkShi=$totalHkShi + $row['HK_SHI'];
                $totalRbHi=$totalRbHi + $row['REALISASI_HI'];
                $totalRbShi=$totalRbShi + $row['REALISASI_SHI'];
                $i=$i+1;
            }
            
            if ($tmpAccode!='')
            {
                 $datatotal = $this->model_rpt_progress_n->gen_prog($tgl, $afd, $company, $jns, $tmpAccode);
            
            foreach($datatotal as $rows)
            {
            $tabel .= "<tr style='background-color:#E0FFFF'><td width='30px' align='left' class='tbl_td' colspan='5'>&nbsp;&nbsp;<strong>TOTAL - ".$rows['ACCOUNTDESC']."</strong></td>";
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_HI'],2)."</strong></td>"; 
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_SHI'],2)."</strong></td>";             $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HK_HI'],2)."</strong></td>"; 
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HK_SHI'],2)."</strong></td>"; 
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_HI'],2)."</strong></td>"; 
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_SHI'],2)."</strong></td>";                 
            $tabel .= "<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_UNIT_HI'],2)."</strong></td>";            $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_UNIT_SHI'],2)."</strong></td>";
            $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_PERHK_HI'],2)."</strong></td>";            $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['REALISASI_PERHK_SHI'],2)."</strong></td></tr>";
                }
            }
            
            $datatotal=''; 
            $tabel .= "<tr style='background-color:#E0FFFF'><td class='tbl_td' align = 'left' colspan='5'>&nbsp;&nbsp;<strong>TOTAL</strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($totalHkHi,2,',','.')."</strong></td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($totalHkShi,2,',','.')."</strong></td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($totalRbHi,2,',','.')."</strong></td>";
            $tabel .= "<td class='tbl_td' align = 'right'><strong>".number_format($totalRbShi,2,',','.')."</strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td>";
            $tabel .= "<td class='tbl_td' align = 'center'><strong> - </strong></td></tr>";    
            $tabel .= "</table>";
            echo $tabel;
        }
        //########## END UPDATE 16 Desember #############
    
    }
    
    function lpph_tehnik($company,$afd,$tgl,$hari,$bulan,$tahun,$judul,$bulanr)
    {
        $tmpAccode='';
        $data_prog = $this->model_rpt_progress_n->gen_prog_detail_tehnik($tgl, $afd, $company);
        
        
        $tabel = "";
        $tabel .= "<style> .tbl_header { font-size: 12px; color:#678197;border-top:1px solid;border-left:1px solid; } ";
        $tabel .= ".tbl_th { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid } ";
        $tabel .= ".tbl_td { font-size: 12px;color:#678197;border-bottom:1px solid; border-right:1px solid; padding-right:6px; padding-left:6px;   } ";
        $tabel .= ".tbl_2 { font-size: 12px;color:#678197; } ";
        $tabel .= ".content { font-size: 12px;color:#678197; } </style>";
        $tabel .= "<table class='tbl_2' border='0' width='100%'>";
        $tabel .= "<tr><td colspan='3' align='center'><strong>LAPORAN PERINCIAN PEKERJAAN HARIAN - ".$judul."</strong></td></tr>";
        $tabel .= "<tr><td colspan='3' align='center'><strong>NO : &nbsp;&nbsp;&nbsp;&nbsp; / PROGRESS / ".$company." / ". strtoupper($bulanr) ." / ".$tahun."</strong></td></tr>";
        $tabel .= "<tr><td colspan='3' align='center'><strong>".$hari." ".strtoupper($bulan)."&nbsp;".$tahun."</strong></td></tr><tr><td colspan='3'>&nbsp;</td></tr>";
        $tabel .= "<tr><td colspan='3'>PT. ".$this->session->userdata('DCOMPANY_NAME')."</td></tr></table>";
        $tabel .= "<table width='100%' class='tbl_header' cellpadding='0' cellspacing='0'>";
        $tabel .= "<tr><td align='center' width='2%' class='tbl_td' rowspan='2'>NO</td>";
        $tabel .= "<td align='center' width='6%' class='tbl_td' rowspan='2'>KODE ITEM KERJA</td>";
        $tabel .= "<td align='center' width='22%' class='tbl_td' rowspan='2'>NAMA ITEM KERJA</td>";
        $tabel .= "<td align='center' width='5%' class='tbl_td' rowspan='2'>AFD</td>";
        $tabel .= "<td align='center' width='6%' class='tbl_td' rowspan='2'>LOKASI</td>";
        $tabel .= "<td align='center' width='6%' class='tbl_td' rowspan='2'>SUB LOKASI</td>";
        $tabel .= "<td align='center' width='3%' class='tbl_td' rowspan='2'>SAT</td>";
        $tabel .= "<td align='center' width='10%' class='tbl_td' colspan='2'>HASIL KERJA</td>";
       // $tabel .= "<td align='center' width='12%' class='tbl_td' colspan='2'>TENAGA KERJA (HK)</td>";
        $tabel .= "<tr><td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td></tr>";
        //$tabel .= "<td align='center' class='tbl_td'>Hari Ini</td><td align='center' class='tbl_td'>S.d Hari Ini</td>";
        
        $i=1;
         
        foreach ( $data_prog as $row){
            if ($tmpAccode!=''){
                if ($row['ACTIVITY_CODE'] != $tmpAccode){     
                   $datatotal = $this->model_rpt_progress_n->gen_prog_tehnik($tgl,$afd,$company,$tmpAccode);
                   foreach($datatotal as $rows){
                     $tabel .= "<tr style='background-color:#E0FFFF; padding:2px;'><td width='30px' align='left' class='tbl_td' colspan='6'>&nbsp;&nbsp;
                                <strong>TOTAL - ".$rows['ACTIVITY_DESC']."</strong></td>";
                     $tabel .="<td width='160px' align='center' class='tbl_td'><strong>".$rows['SATUAN']."</strong></td>";      
                     $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_HI'],2)."</strong></td>"; 
                     $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_SHI'],2)."</strong></td>"; 
     
                    }
                    $datatotal=''; 
               }  
            }  
            $tabel .= "<tr><td align='center' class='tbl_td'>".$i."</td>";
            $tabel .= "<td  align='center' class='tbl_td'>&nbsp;".$row['ACTIVITY_CODE']."</td>";
            $tabel .= "<td align='left' class='tbl_td' >&nbsp;".$row['ACTIVITY_DESC']."</td>";
            $tabel .= "<td align='center' class='tbl_td' >&nbsp;".$row['AFD']."</td>";
            $tabel .= "<td align='center' class='tbl_td'>".$row['LOCATION_CODE']."</td>";
            $tabel .= "<td align='left' class='tbl_td' >&nbsp;".$row['PJ_LOCATION']."</td>";
            $satuan=(empty($row['SATUAN']) || trim($row['SATUAN'])=='')?'-':$row['SATUAN'];
            $tabel .= "<td  align='center' class='tbl_td'>".$satuan."</td>";
            $tabel .= "<td  align='right' class='tbl_td'>". number_format($row['HSL_KERJA_HI'],2)."</td> ";
            $tabel .= "<td align='right' class='tbl_td'>". number_format($row['HSL_KERJA_SHI'],2)."</td> ";
           
            
            $tmpAccode = $row['ACTIVITY_CODE'];         
            $i=$i+1;
       }
       
       if($tmpAccode===false || !empty($tmpAccode)){
           $datatotal = $this->model_rpt_progress_n->gen_prog_tehnik($tgl,$afd,$company,$tmpAccode);
           foreach($datatotal as $rows){
                 $tabel .= "<tr style='background-color:#E0FFFF; padding:2px;'><td width='30px' align='left' class='tbl_td' colspan='6'>&nbsp;&nbsp;
                            <strong>TOTAL - ".$rows['ACTIVITY_DESC']."</strong></td>";
                 $tabel .="<td width='160px' align='center' class='tbl_td'><strong>".$rows['SATUAN']."</strong></td>";      
                 $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_HI'],2)."</strong></td>"; 
                 $tabel .="<td width='160px' align='right' class='tbl_td'><strong>".number_format($rows['HSL_KERJA_SHI'],2)."</strong></td>"; 
            }
            $datatotal='';    
       }
       
            
        $tabel .= "</table>";
        
        echo $tabel;    
    }
    
    
    function lpphtoxls()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $company_name = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $afd = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $tgl = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $jns = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $hari = substr($tgl,6,2);
        $bulan = substr($tgl,4,2);
        $tahun = substr($tgl,0,4);
              
        $totalHkHi=0;
        $totalHkShi=0;
        $totalRbHi=0;
        $totalRbShi=0;        
        
        if($bulan== '01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan== '02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan== '03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan== '04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan== '05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan== '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan== '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan== '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan== '09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan== '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan== '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan== '12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $judul = "";
        if ($jns == 'rawat') { $judul .= "RAWAT";    }
        else if ($jns == 'panen') {  $judul .= "PANEN";    }
        else if ($jns == 'trans_panen') {  $judul .= "TRANSPORT PANEN";    }
        else if ($jns == 'bibitan') {  $judul .= "BIBITAN";    }
        else if ($jns == 'sisip') {  $judul .= "SISIP";    }
        else if ($jns == 'tanam') {  $judul .= "PROJECT TANAM";    }
        else if ($jns == 'rwtif') {  $judul .= "RAWAT INFRASTRUKTUR";    }
        else if ($jns == 'pj_inf') {  $judul .= "PROJECT INFRASTRUKTUR";    }
        else if ($jns == 'pj_bibitan') {  $judul .= "PROJECT PERSIAPAN BIBITAN";    }
        else if ($jns == 'umum') {  $judul .= "UMUM";    }
        else if ($jns == 'tehnik') {  $judul .= "TEKNIK";    }
        else if ($jns == 'lc') {  $judul .= "PROJECT LAND PREPARATION";    }
        
        if (strtoupper(trim($jns))=='PALL')
        {
            $periode= substr($tgl,0,4).substr($tgl,4,2);
            $data_prog = $this->model_rpt_progress_n->gen_prog_pall($periode, $company);
            $judul = '';
            $headers = ''; // just creating the var for field headers to append to below
            $data = ''; // just creating the var for field data to append to below
            $footer = '';
            
            $obj =& get_instance();
            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "LAPORAN PERINCIAN PEKERJAAN HARIAN ".strtoupper($jns)."\n";
            $judul .= "PERIODE : ".date("Y / m / d",strtotime($tgl))."\n";
            
            $headers .= "LOKASI \t";
            $headers .= "KETERANGAN \t";
            $headers .= "TIPE \t";
            $headers .= "SUBTIPE LOKASI\t";
            $headers .= "KODE AKTIVITAS \t";
            $headers .= "AKTIVITAS \t";
            $headers .= "QTY \t";
            $headers .= "SATUAN \t";
            $headers .= "Flag \t";
            
            foreach ( $data_prog as $row)
            {
                $line = '';
                        
                $line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['KETERANGAN']))."\t";
                $line .= str_replace('"', '""',trim($row['TIPE']))."\t";
                $line .= str_replace('"', '""',trim($row['SUBTIPE_LOKASI']))."\t";
                $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['COA_DESCRIPTION']))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['QTY']) ,2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['SATUAN']) ,2) )."\t";
                $line .= str_replace('"', '""',trim($row['FLAG']))."\t";

                $data .= trim($line)."\n";        
            }
            
            $footer .= " - \t";
            $footer .= " - \t";
            $footer .= " -\t";
            $footer .= " - \t";
            $footer .= " - \t";   
            
            $data .= trim($footer)."\n";
            $data = str_replace("\r","",$data);    
             } elseif(strtoupper(trim($jns))=='TEKNIK') {
            $data_prog = $this->model_rpt_progress_n->gen_prog_detail_tehnik($tgl, $afd, $company);
        
            $judul = '';
            $headers = ''; // just creating the var for field headers to append to below
            $data = ''; // just creating the var for field data to append to below
            $footer = '';
            
            $obj =& get_instance();
            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "LAPORAN PERINCIAN PEKERJAAN HARIAN ".strtoupper($jns)."\n";
            $judul .= "PERIODE : ".date("Y / m / d",strtotime($tgl))."\n";
            $judul .= "AFDELING : ". strtoupper($afd)."\n";
            
            
            $headers .= "KODE ITEM \t";
            $headers .= "NAMA ITEM KERJA \t";
            $headers .= "LOKASI \t";
            $headers .= "SAT \t";
            $headers .= "HASIL KERJA HARI INI \t";    
            $headers .= "HASIL KERJA S.D HARI INI \t";
            $headers .= "TENAGA KERJA HARI INI \t";    
            $headers .= "TENAGA KERJA S.D HARI INI \t";
                
            foreach ( $data_prog as $row)
            {
                $line = '';
                        
                $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['ACTIVITY_DESC']))."\t";
                $line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['SATUAN']))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_HI']) ,2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_SHI']) ,2))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HK_HI']) ,2))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HK_SHI']) ,2))."\t";
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
            
            
            $data .= trim($footer)."\n";
            $data = str_replace("\r","",$data);
            
        }
        else{
            $data_prog = $this->model_rpt_progress_n->gen_prog_detail($tgl, $afd, $jns, $company);
        
            $judul = '';
            $headers = ''; // just creating the var for field headers to append to below
            $data = ''; // just creating the var for field data to append to below
            $footer = '';
            
            $obj =& get_instance();
            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "LAPORAN PERINCIAN PEKERJAAN HARIAN ".strtoupper($jns)."\n";
            $judul .= "PERIODE : ".date("Y / m / d",strtotime($tgl))."\n";
            $judul .= "AFDELING : ". strtoupper($afd)."\n";
            
            
            $headers .= "KODE ITEM \t";
            $headers .= "NAMA ITEM KERJA \t";
            $headers .= "LOKASI \t";
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
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_HI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_SHI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HK_HI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HK_SHI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_HI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_SHI']),2))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_UNIT_HI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_UNIT_SHI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_PERHK_HI']),2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['REALISASI_PERHK_SHI']),2) )."\t";
                
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
        }
        
        
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=PRG_PEKERJAANHARIAN_".$company."_".strtoupper($afd)."_".$tgl.
                            ".xls");
        echo "$judul\n$headers\n$data";  
    }
    
    function lpphtekniktoxls()
    {
        $company = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $company_name = htmlentities($this->session->userdata('DCOMPANY'),ENT_QUOTES,'UTF-8');
        $afd = htmlentities($this->uri->segment(3),ENT_QUOTES,'UTF-8');
        $tgl = htmlentities($this->uri->segment(4),ENT_QUOTES,'UTF-8');
        $jns = htmlentities($this->uri->segment(5),ENT_QUOTES,'UTF-8');
        $hari = substr($tgl,6,2);
        $bulan = substr($tgl,4,2);
        $tahun = substr($tgl,0,4);
              
        $totalHkHi=0;
        $totalHkShi=0;
        $totalRbHi=0;
        $totalRbShi=0;        
        
        if($bulan== '01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan== '02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan== '03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan== '04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan== '05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan== '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan== '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan== '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan== '09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan== '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan== '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan== '12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $judul = "";
        if ($jns == 'tehnik') {  $judul .= "TEKNIK";    }
        
            $data_prog = $this->model_rpt_progress_n->gen_prog_detail_tehnik($tgl, $afd, $company);
        
            $judul = '';
            $headers = ''; // just creating the var for field headers to append to below
            $data = ''; // just creating the var for field data to append to below
            $footer = '';
            
            $obj =& get_instance();
            
            $judul .= $this->session->userdata('DCOMPANY_NAME'). "\n";
            $judul .= "LAPORAN PERINCIAN PEKERJAAN HARIAN ".strtoupper($jns)."\n";
            $judul .= "PERIODE : ".date("Y / m / d",strtotime($tgl))."\n";
            $judul .= "AFDELING : ". strtoupper($afd)."\n";
            
            
            $headers .= "KODE ITEM \t";
            $headers .= "NAMA ITEM KERJA \t";
            $headers .= "AFD \t";
            $headers .= "LOKASI \t";
            $headers .= "SUB LOKASI \t";
            $headers .= "SAT \t";
            $headers .= "HASIL KERJA HARI INI \t";    
            $headers .= "HASIL KERJA S.D HARI INI \t";
                            
            foreach ( $data_prog as $row)
            {
                $line = '';
                        
                $line .= str_replace('"', '""',trim($row['ACTIVITY_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['ACTIVITY_DESC']))."\t";
                $line .= str_replace('"', '""',trim($row['AFD']))."\t";
                $line .= str_replace('"', '""',trim($row['LOCATION_CODE']))."\t";
                $line .= str_replace('"', '""',trim($row['PJ_LOCATION']))."\t";
                $line .= str_replace('"', '""',number_format(trim($row['SATUAN']) ,2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_HI']) ,2) )."\t";
                $line .= str_replace('"', '""',number_format(trim($row['HSL_KERJA_SHI']) ,2) )."\t";
                $data .= trim($line)."\n";        
            }
                
            $footer .= " - \t";
            $footer .= " - \t";
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
        header("Content-Disposition: attachment; filename=PRG_PEKERJAANHARIANTEKNIK_".$company."_".strtoupper($afd)."_".$tgl.
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
        $company_name = $this->session->userdata('DCOMPANY_NAME');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $hari = substr($tgl,6,2);
        $bulan = substr($tgl,4,2);
        $tahun = substr($tgl,0,4);
                
        $totalHkHi=0;
        $totalHkShi=0;
        $totalRbHi=0;
        $totalRbShi=0;        
        
        
        if($bulan== '01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan== '02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan== '03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan== '04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan== '05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan== '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan== '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan== '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan== '09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan== '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan== '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan== '12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $judul = "";
        if ($jns == 'rawat') { $judul .= "RAWAT";    }
        else if ($jns == 'panen') {  $judul .= "PANEN";    }
        else if ($jns == 'trans_panen') {  $judul .= "TRANSPORT PANEN";    }
        else if ($jns == 'bibitan') {  $judul .= "BIBITAN";    }
        else if ($jns == 'sisip') {  $judul .= "SISIP";    }
        else if ($jns == 'tanam') {  $judul .= "PROJECT TANAM";    }
        else if ($jns == 'rwtif') {  $judul .= "RAWAT INFRASTRUKTUR";    }
        else if ($jns == 'pj_inf') {  $judul .= "PROJECT INFRASTRUKTUR";    }
        else if ($jns == 'pj_bibitan') {  $judul .= "PROJECT PERSIAPAN BIBITAN";    }
        else if ($jns == 'umum') {  $judul .= "UMUM";    }
        else if ($jns == 'lc') {  $judul .= "PROJECT LAND PREPARATION";    }
        
        $data_prog = $this->model_rpt_progress_n->gen_prog_detail($tgl, $afd, $jns, $company);

        $pdf = new pdf_usage();
        $pdf->Open();
        $pdf->SetAutoPageBreak(TRUE,10);
        $pdf->SetMargins(5,15);
        $pdf->AddPage("L","A4");
        $pdf->AliasNbPages(); 
                
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
                $aSimpleHeader[$i]['TEXT'] = "Lokasi";
                $aSimpleHeader[$i]['WIDTH'] = 22;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 4) {
                $aSimpleHeader[$i]['TEXT'] = "Sat";
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
                    $datatotal = $this->model_rpt_progress_n->gen_prog($tgl, $afd, $company, $jns, $tmpAccode);
                        
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
            $data[6]['TEXT'] = number_format($row['HSL_KERJA_SHI'],2,',','.');
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
        
           $datatotal = $this->model_rpt_progress_n->gen_prog($tgl, $afd, $company, $jns, $tmpAccode);
       
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
        require_once(APPPATH . 'libraries/daftar_upah/authorize_prg.inc');
        $pdf->Output();
    }
    
    function lpphtekniktopdf()
    {
        if ($this->session->userdata('logged_in')!= TRUE)
        {
            redirect('login');
        }
        
        $company = $this->session->userdata('DCOMPANY');
        $company_name = $this->session->userdata('DCOMPANY_NAME');
        $afd = $this->uri->segment(3);
        $tgl = $this->uri->segment(4);
        $jns = $this->uri->segment(5);
        $hari = substr($tgl,6,2);
        $bulan = substr($tgl,4,2);
        $tahun = substr($tgl,0,4);
                
        $totalRbHi=0;
        $totalRbShi=0;        
        
        
        if($bulan== '01'){ $bulan = "Januari"; $bulanr = "I";} 
        else if($bulan== '02'){ $bulan = "Februari"; $bulanr = "II"; } 
        else if($bulan== '03'){ $bulan = "Maret"; $bulanr = "III"; } 
        else if($bulan== '04'){ $bulan = "April"; $bulanr = "IV"; } 
        else if($bulan== '05'){ $bulan = "Mei"; $bulanr = "V"; } 
        else if($bulan== '06'){ $bulan = "Juni"; $bulanr = "VI"; } 
        else if($bulan== '07'){ $bulan = "Juli"; $bulanr = "VII"; } 
        else if($bulan== '08'){ $bulan = "Agustus"; $bulanr = "VIII"; } 
        else if($bulan== '09'){ $bulan = "September"; $bulanr = "IX"; } 
        else if($bulan== '10'){ $bulan = "Oktober"; $bulanr = "X"; } 
        else if($bulan== '11'){ $bulan = "Nopember"; $bulanr = "XI"; } 
        else if($bulan== '12'){ $bulan = "Desember"; $bulanr = "XII"; }
        
        $judul = "";
        if ($jns == 'tehnik') { $judul .= "TEKNIK";    }
        
       $data_prog = $this->model_rpt_progress_n->gen_prog_detail_tehnik($tgl, $afd, $company);

        $pdf = new pdf_usage();
        $pdf->Open();
        $pdf->SetAutoPageBreak(TRUE,10);
        $pdf->SetMargins(5,15);
        $pdf->AddPage("P","A4");
        $pdf->AliasNbPages(); 
                
        require_once(APPPATH . 'libraries/ba/header_progress_tek.inc');
        
        require_once(APPPATH . 'libraries/ba/table_border.inc');
        
        $columns = 9; //number of Columns
        $pdf->tbInitialize($columns, true, true);
        $pdf->tbSetTableType($table_default_table_type);
        
        $aSimpleHeader = array(); 
        for($i=0; $i<=$columns+1; $i++) {
            $aSimpleHeader[$i] = $table_default_header_type;
            if($i == 0) {
                $aSimpleHeader[$i]['TEXT'] = "NO";
                $aSimpleHeader[$i]['WIDTH'] = 7;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            
            if($i == 1) {
                $aSimpleHeader[$i]['TEXT'] = "KODE ITEM KERJA";
                $aSimpleHeader[$i]['WIDTH'] = 15;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 2) {
                $aSimpleHeader[$i]['TEXT'] = "NAMA ITEM KERJA";
                $aSimpleHeader[$i]['WIDTH'] = 55;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 3) {
                $aSimpleHeader[$i]['TEXT'] = "AFD";
                $aSimpleHeader[$i]['WIDTH'] = 12;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 4) {
                $aSimpleHeader[$i]['TEXT'] = "LOKASI";
                $aSimpleHeader[$i]['WIDTH'] = 28;
                $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 5) {
                $aSimpleHeader[$i]['TEXT'] = "SUB LOKASI";
                $aSimpleHeader[$i]['WIDTH'] = 30;
                 $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 6) {
                $aSimpleHeader[$i]['TEXT'] = "SAT";
                $aSimpleHeader[$i]['WIDTH'] = 15;
                 $aSimpleHeader[$i]['ROWSPAN'] = 2;
            }
            if($i == 7) {
                $aSimpleHeader[$i]['TEXT'] = "HASIL KERJA";
                $aSimpleHeader[$i]['WIDTH'] = 20;
                $aSimpleHeader[$i]['COLSPAN'] = 2;
            }
            if($i == 8) {
                $aSimpleHeader[$i]['TEXT'] = "";
                $aSimpleHeader[$i]['WIDTH'] = 20;
            }
            
        }
        
        $aSimpleHeader2 = array(); 
        for($i=0; $i<=$columns; $i++) {
            $aSimpleHeader2[$i] = $table_default_header_type;
            if($i == 0) {
                $aSimpleHeader2[$i]['TEXT'] = "";
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
                $aSimpleHeader2[$i]['WIDTH'] = 24;
            }
            if($i == 8) {
                $aSimpleHeader2[$i]['TEXT'] = "S.D Hari ini";
                $aSimpleHeader2[$i]['WIDTH'] = 24;
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
                if ($row['ACTIVITY_CODE'] != $tmpAccode)
                {
           $datatotal = $this->model_rpt_progress_n->gen_prog_tehnik($tgl, $afd, $company, $tmpAccode);
                    foreach($datatotal as $rows)
                    {
                        $data2 = array();
						$hiTotal +=$rows['HSL_KERJA_HI'];
						$shiTotal += $rows['HSL_KERJA_SHI'];
						
                        $data2[0]['TEXT'] = "TOTAL - " .$rows['ACTIVITY_DESC'];
                        $data2[0]['COLSPAN']=5;
                        $data2[5]['TEXT'] = "";
                        $data2[6]['TEXT'] = "";
                        $data2[7]['TEXT'] = number_format($rows['HSL_KERJA_HI'],2,',','.');
                        $data2[8]['TEXT'] = number_format($rows['HSL_KERJA_SHI'],2,',','.');
                                                                      
                        $data2[5]['T_ALIGN'] = "C";
                        $data2[6]['T_ALIGN'] = "C";
                        $data2[7]['T_ALIGN'] = "R";
                        $data2[8]['T_ALIGN'] = "R";
                                                 
                        $data2[0]['T_TYPE'] = "B";
                        $data2[5]['T_TYPE'] = "B";
                        $data2[6]['T_TYPE'] = "B";
                        $data2[7]['T_TYPE'] = "B";
                        $data2[8]['T_TYPE'] = "B";
                       
                    }
                    $datatotal=''; 
                    
                    $pdf->tbDrawData($data2);
                }  
            }

            $totalRbHi=$totalRbHi + $row['HSL_KERJA_HI'];
            $totalRbShi=$totalRbShi + $row['HSL_KERJA_SHI'];
            
            $currAccode= $row['ACTIVITY_CODE'];
            $data[0]['TEXT'] = $i;    
            $data[1]['TEXT'] = $row['ACTIVITY_CODE'];
            $data[2]['TEXT'] = $row['ACTIVITY_DESC'];
            $data[3]['TEXT'] = $row['AFD'];
            $data[4]['TEXT'] = $row['LOCATION_CODE'];
            $data[5]['TEXT'] = $row['PJ_LOCATION'];
            $data[6]['TEXT'] = $row['SATUAN'];
            $data[7]['TEXT'] = number_format($row['HSL_KERJA_HI'],2,',','.');
            $data[8]['TEXT'] = number_format($row['HSL_KERJA_SHI'],2,',','.');
                                  
            $data[0]['T_ALIGN'] = "C";
            $data[1]['T_ALIGN'] = "C";
            $data[2]['T_ALIGN'] = "L";
            $data[3]['T_ALIGN'] = "C";
            $data[4]['T_ALIGN'] = "C";
            $data[5]['T_ALIGN'] = "C";
            $data[6]['T_ALIGN'] = "C";
            $data[7]['T_ALIGN'] = "R";
            $data[8]['T_ALIGN'] = "R";
           
            $tmpAccode = $row['ACTIVITY_CODE'];

            $i=$i+1;
            $pdf->tbDrawData($data);
        }
        
        $datatotal = $this->model_rpt_progress_n->gen_prog_tehnik($tgl, $afd, $company, $jns, $tmpAccode);
       
        foreach($datatotal as $rows)
        {
                $data3 = array();
                $data3[0]['TEXT'] = "TOTAL - " .$rows['ACTIVITY_DESC'];
                $data3[0]['COLSPAN']=5;
                $data3[5]['TEXT'] = "";
                $data3[6]['TEXT'] = "";
                $data3[7]['TEXT'] = number_format($rows['HSL_KERJA_HI'],2,',','.');
                $data3[8]['TEXT'] = number_format($rows['HSL_KERJA_SHI'],2,',','.');
                               
                $data3[5]['T_ALIGN'] = "R";
                $data3[6]['T_ALIGN'] = "R";
                $data3[7]['T_ALIGN'] = "R";
                $data3[8]['T_ALIGN'] = "R";
                
                $data3[0]['T_TYPE'] = "B";
                $data3[5]['T_TYPE'] = "B";
                $data3[6]['T_TYPE'] = "B";
                $data3[7]['T_TYPE'] = "B";
                $data3[8]['T_TYPE'] = "B";
               
                $pdf->tbDrawData($data3);
        }
        $datatotal=''; 
        $data4 = Array();
        $data4[0]['TEXT'] = "TOTAL";
        $data4[0]['COLSPAN']=5;
        $data4[7]['TEXT'] = number_format($totalRbHi,2,',','.');
        $data4[8]['TEXT'] = number_format($totalRbShi,2,',','.');
        
        $data4[5]['T_ALIGN'] = "C";
        $data4[6]['T_ALIGN'] = "C";
        $data4[7]['T_ALIGN'] = "R";
        $data4[8]['T_ALIGN'] = "R";
               
        $data4[0]['T_TYPE'] = "B";
        $data4[5]['T_TYPE'] = "B";
        $data4[6]['T_TYPE'] = "B";
        $data4[7]['T_TYPE'] = "B";
        $data4[8]['T_TYPE'] = "B";
        $pdf->tbDrawData($data4);
        
        $pdf->tbOuputData();
        $pdf->tbDrawBorder();
        
        $pdf->Ln(4);
        require_once(APPPATH . 'libraries/daftar_upah/authorize_prg.inc');
        $pdf->Output();
    }
}

?>