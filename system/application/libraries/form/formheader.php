<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once(APPPATH . 'libraries/path/apppath.php');

class formheader extends apppath
{
    public $CI;
    
	function __construct()
    {
        $this->CI = &get_instance();
    }

    function show_head($headName,$headType)
    {
        $companyName=htmlentities($this->CI->session->userdata('DCOMPANY_NAME'));//$obj->session->userdata('DCOMPANY');
        $html="<div class='teks_headline'><strong>$companyName<br>$headName $headType<br/></strong></div>
               <hr style='border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;'/> ";
        
        return $html;
    }
    
	function show_menu()
    {
        $baseUrl = $this->getBaseUrl();
        $userLevel = strtoupper(htmlentities($this->CI->session->userdata('USER_LEVEL')));
        $login_id= strtoupper(htmlentities($this->CI->session->userdata('LOGINID')));
        
        
        $menu ="<ul class='jd_menu jd_menu_slate'>";
        $menu .="<li ><a href='#' class='accessible'>Transaksi</a>
                 <ul>
                    <li><a href='$baseUrl"."index.php/m_gang_activity_detail'>Laporan harian mandor</a></li>
                    <li><a href='$baseUrl"."index.php/p_machine'>Buku mesin</a></li>
                    <li><a href='$baseUrl"."index.php/p_vehicle_activity'>Buku Kendaraan</a></li>
                    <li><a href='$baseUrl"."index.php/p_workshop_activity'>Buku Workshop</a></li>
                    <hr>    
                    <li><a href='#'>Entry Progress</a>
                        <ul>
                            <li><a href='$baseUrl"."index.php/p_progress_rawat'>Entry Progress Rawat</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_panen'>Entry Progress Panen</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_tp'>Entry Progress Transport Panen</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_sisip'>Entry Progress Sisip</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_bibitan'>Entry Progress Bibitan</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_tanam'>Entry Progress Tanam</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_rawat_if'>Entry Progress Rawat Infrastruktur</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_pj_infrastruktur'>Entry Progress Project Infrastruktur</a></li>
                            <li><a href='$baseUrl"."index.php/p_progress_pj_bibitan'>Entry Progress Project Bibitan</a></li>
                        </ul>
                    </li>
                    <li><a href='$baseUrl"."index.php/rpt_absensi'>Absensi Karyawan</a></li>
                    <!-- <li><a href='$baseUrl"."index.php/p_progress_rawat'>Entry Progress Rawat</a></li>
                    <li><a href='$baseUrl"."index.php/p_empcopy'>Mutasi Karyawan</a></li> -->
                 </ul>
                 </li>";
        //$menu .="</ul>";
        if ('SAD'==$userLevel || 'ADM'==$userLevel)
        {
            $menu .= " <li ><a href='#' class='accessible'>Master Data</a>
                        <ul>
                            <li><a href='$baseUrl"."index.php/m_employee'>Karyawan</a></li>
                            <li><a href='$baseUrl"."index.php/m_gang'>Kemandoran</a></li>
                            <li><a href='$baseUrl"."index.php/p_empcopy'>Mutasi Karyawan</a></li>
                            <li><a href='$baseUrl"."index.php/m_vehicle'>Kendaraan</a></li>
                            <li><a href='$baseUrl"."index.php/m_machine'>Mesin</a></li>
                            <li><a href='$baseUrl"."index.php/m_bloktanam'>Blok Tanam</a></li>
                            <li><a href='$baseUrl"."index.php/m_workshop'>Workshop</a></li>
                            <li><a href='$baseUrl"."index.php/m_nursery'>Nursery</a></li>
                            <li><a href='$baseUrl"."index.php/m_infras'>Infrastruktur</a></li>
                            <li><a href='$baseUrl"."index.php/m_user'>User</a></li>
                        </ul>
                        </li> 
                        <li ><a href='#' class='accessible'>Project</a>
                        <ul>
                            <li><a href='$baseUrl"."index.php/project/prj_pengajuan'>Pengajuan Project</a></li>
                            <li><a href='#'>Pengajuan Revisi Project</a></li>
                        </ul>
                        </li>";
        }
        if ('SAD'==$userLevel)
        {
            $menu .= "  <li ><a href='#' class='accessible' style='height:20px;'>Reporting</a>
                        <ul style='width: 200px;'>
                                            
                                <li><a href='#'>Daftar Upah </a>
                                    <ul>
                                        <li><a href='$baseUrl"."index.php/rpt_du'>Daftar Upah Per Kemandoran</a></li>
                                        <li><a href='$baseUrl"."index.php/rpt_du/du_afd'>Daftar Upah Per Divisi / Bagian</a></li>
                                        <li><a href='$baseUrl"."index.php/rpt_du_act'>Daftar Upah Per Aktivitas</a></li>
                                    </ul>
                                </li>
                                <li><a href='#'>Berita Acara</a>
                                    <ul>
                                <li><a href='$baseUrl"."index.php/rpt_ba_rawat'>Berita Acara Gaji Rawat</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_panen'>Berita Acara Gaji Panen</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_transportpanen'>Berita Acara Gaji Transport Panen</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_bibitan'>Berita Acara Gaji Bibitan</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_sisip'>Berita Acara Gaji Sisip</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_pjtanam'>Berita Acara Hasil Kerja Project Tanam</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_pjbibitan'>Berita Acara Hasil Kerja Project Persiapan Bibitan</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_pjinfrastruktur'>Berita Acara Gaji Project Infrastruktur</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_rawat_infrastruktur'>Berita Acara Rawat Infrastruktur</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_umum'>Berita Acara Gaji Umum</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_vmw'>Berita Acara Gaji Kendaraan, Workshop, Mesin</a></li>
                                <li><a href='$baseUrl"."index.php/rpt_ba_tunpot'>Berita Acara Tunjangan dan Potongan</a></li>   
                                <hr />
                                <li><a href='#'>Komparasi DU dan BA</a>
                                 <ul>
                                    <li><a href='$baseUrl"."index.php/rpt_rekonbadu'>Rekonsiliasi BA & DU</a></li>
                                 </ul>
                                </li>
                                    </ul>
                                </li>
                                <li><a href='$baseUrl"."index.php/rpt_progress/progress'>Progress</a></li>
                                <hr>
                                <!--<li><a href='$baseUrl"."index.php/m_closing'>Closing</a></li>-->
                    			<li><a href='<?= base_url()?>index.php/rpt_premi'>Premi</a></li>    
                            <hr>
                                <li><a href='$baseUrl"."index.php/rpt_lhm'>Export Data</a>
                                </li>        
                            </ul>   
                            </li> ";
        }
        $menu .= "<li style='float:right;'>&nbsp;&nbsp;&nbsp;Logged as, "." $login_id &nbsp; | &nbsp; 
                    <a href='$baseUrl"."index.php/login/Dologout'>Logout</a> </li>
                    </ul>";
         
        return $menu;
    }
    
    function LoadJSPath()
    {
        $template_path=$this->getTemplatePath();
        
        $JSPath ="  <script src='$template_path"."js/jquery.js' type='text/javascript'></script>
                    <script src='$template_path"."js/jquery.ui.all.js' type='text/javascript'></script>
                    <script language='javascript' src='$template_path"."js/js_compile/prov_grid.js'></script>
                    <script language='javascript' src='$template_path"."js/js_compile/prov_jqAll.js'></script>
                    <script type='text/javascript' src='$template_path"."js/thickbox-compressed.js'></script>
                    <script type='text/javascript' src='$template_path"."js/js_compile/prov_autocomp.js'></script> \n";
                    
        return $JSPath;
    }
    function LoadCSSPath()
    {
        $template_path = $this->getTemplatePath();
        
        $CSSPath =" <link type='text/css' href='$template_path"."themes/base/ui.css' rel='stylesheet' /> ";
        
        return $CSSPath;
    }
}

?>