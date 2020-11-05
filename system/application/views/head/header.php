<?PHP 
     header('expire-header');
    $template_path = base_url().$this->config->item('template_path');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
<!-- dialog -->    
    <link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
    
<!-- end dialog --->
</head>

<body>

<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
<script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images'; //definisi imagepath pada jgrid
var url = "<?= base_url().'index.php/' ?>"; //definisi url untuk jGrid

jQuery(document).ready(function(){

$(function(){
                $('ul.jd_menu').jdMenu({    onShow: loadMenu
                                            });
                $('ul.jd_menu_vertical').jdMenu({onShow: loadMenu, onHide: unloadMenu, offset: 1, onAnimate: onAnimate});
            });

            function onAnimate(show) {
                //$(this).fadeIn('slow').show();
                if (show) {
                    $(this)
                        .css('visibility', 'hidden').show()
                            .css('width', $(this).innerWidth())
                        .hide().css('visibility', 'visible')
                    .fadeIn('normal');
                } else {
                    $(this).fadeOut('fast');
                }
            }

            var MENU_COUNTER = 1;
            function loadMenu() {
                if (this.id == 'dynamicMenu') {
                    $('> ul > li', this).remove();
            
                    var ul = $('<ul></ul>');
                    var t = MENU_COUNTER + 10;
                    for (; MENU_COUNTER < t; MENU_COUNTER++) {
                        $('> ul', this).append('<li>Item ' + MENU_COUNTER + '</li>');
                    }
                }
            }

            function unloadMenu() {
                if (MENU_COUNTER >= 30) {
                    MENU_COUNTER = 1;
                }
            }

            // We're passed a UL
            function onHideCheckMenu() {
                return !$(this).parent().is('.LOCKED');
            }

            // We're passed a LI
            function onClickMenu() {
                $(this).toggleClass('LOCKED');
                return true;
            }

    /*end menu*/
    
    
    $.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
    var maintab =jQuery('#tabs','#RightPane').tabs({
        add: function(e, ui) {
            // append close thingy
            $(ui.tab).parents('li:first')
                .append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
                .find('span.ui-tabs-close')
                .click(function() {
                    maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
                });
            // select just added tab
            maintab.tabs('select', '#' + ui.panel.id);
        }
    });
    

});

$(function(){
    $("#ajax_display").ajaxStart(function(){
            $('#htmlExampleTarget').hide();             
            $(this).html("<img alt='' src='themes/wait.gif' ><br >Waiting ...");
        });
        
    $("#ajax_display").ajaxSuccess(function(){
           $(this).html('');
     });
    $("#ajax_display").ajaxError(function(url){
           alert('jQuery ajax is error ');
     });
});
</script>
<style>
#loading
{
    position:absolute;
    top:0px;
    right:0px;
    background:#ff0000;
    color:#fff;
    font-size:14px;
    font-family:Verdana,Arial,sans-serif;
    padding:2px;
    display:none;
}
</style>
<div class="teks_headline"><strong><?php echo $company_dest;?><br>Master Data<br/></strong></div>
<hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>

<div style="float:right">
<ul class="jd_menu jd_menu_slate">
        <li><a href="#" class="accessible">Transaksi</a>
            <ul>
                <li><a href="<?= base_url()?>index.php/m_gang_activity_detail">Laporan harian mandor</a></li>
                <li><a href="<?= base_url()?>index.php/p_machine">Buku mesin</a></li>
                <li><a href="<?= base_url()?>index.php/p_vehicle_activity">Buku Kendaraan</a></li>
                <li><a href="<?= base_url()?>index.php/p_workshop_activity">Buku Workshop</a></li>
                <hr>    
                <li><a href="#">Entry Progress</a>
                    <ul>
                        <li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_panen">Entry Progress Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_tp">Entry Progress Transport Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_sisip">Entry Progress Sisip</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_bibitan">Entry Progress Bibitan</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_tanam">Entry Progress Tanam</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_lc">Entry Progress LC</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_infrastruktur">Entry Progress Infrastruktur</a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url()?>index.php/rpt_absensi">Absensi Karyawan</a></li>
                <!-- <li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
                <li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li> -->
            </ul>
        </li>
        <?php if( $user_level == 'SAD' || $user_level == 'ADM' || $user_level =='RVW') { ?>
        <li><a href="#" class="accessible">Master Data</a>
            <ul>
                <li><a href="<?= base_url()?>index.php/m_employee">Karyawan</a></li>
                <li><a href="<?= base_url()?>index.php/m_gang">Kemandoran</a></li>
                <li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li>
                <li><a href="<?= base_url()?>index.php/m_vehicle">Kendaraan</a></li>
                <li><a href="<?= base_url()?>index.php/m_machine">Mesin</a></li>
                <li><a href="<?= base_url()?>index.php/m_bloktanam">Blok Tanam</a></li>
                <li><a href="<?= base_url()?>index.php/m_workshop">Workshop</a></li>
                <li><a href="<?= base_url()?>index.php/m_nursery">Nursery</a></li>
                <li><a href="<?= base_url()?>index.php/m_infras">Infrastruktur</a></li>
                <li><a href="<?= base_url()?>index.php/m_user">User</a></li>
            </ul>
        </li>
        <li ><a href='#' class='accessible'>Project</a>
            <ul>
                <li><a href='<?= base_url()?>index.php/Project/Prj_pengajuan'>Pengajuan Project</a></li>
                <li><a href='#'>Pengajuan Revisi Project</a></li>
        	</ul>
		</li>
        <?php } ?>
        <?php if( $user_level == 'SAD'  || $user_level =='RVW') { ?>
        <li><a href="#" class="accessible" style="height:20px;">Reporting</a>
            <ul style="width: 200px;">
                                
                    <li><a href="#">Daftar Upah </a>
                        <ul>
                            <li><a href="<?= base_url()?>index.php/rpt_du">Daftar Upah Per Kemandoran</a></li>
                             <li><a href="<?= base_url()?>index.php/rpt_du/du_afd">Daftar Upah Per Divisi / Bagian</a></li>
                              <li><a href="<?= base_url()?>index.php/rpt_du_act">Daftar Upah Per Aktivitas</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Berita Acara</a>
                        <ul>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat">Berita Acara Gaji Rawat</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_panen">Berita Acara Gaji Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_transportpanen">Berita Acara Gaji Transport Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_bibitan">Berita Acara Gaji Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_sisip">Berita Acara Gaji Sisip</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjtanam">Berita Acara Hasil Kerja Project Tanam</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjbibitan">Berita Acara Hasil Kerja Project Persiapan Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjinfrastruktur">Berita Acara Gaji Project Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat_infrastruktur">Berita Acara Rawat Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_umum">Berita Acara Gaji Umum</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_vmw">Berita Acara Gaji Kendaraan, Workshop, Mesin</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_tunpot">Berita Acara Tunjangan dan Potongan</a></li>
                    <hr />
                    <li><a href="#">Komparasi DU dan BA</a>
                     <ul>
                        <li><a href="<?= base_url()?>index.php/rpt_rekonbadu">Rekonsiliasi BA & DU</a></li>
                     </ul>
                    </li>
                        </ul>
                    </li>
                
                <hr>
                    <li><a href="<?= base_url()?>index.php/rpt_lhm">Export Data</a>
                    </li>    
            </ul>
            
        </li>
        <?php } ?>
        <li style="float:right;">&nbsp;&nbsp;&nbsp;Logged as, <?php echo $login_id; ?> &nbsp; | &nbsp; <a href="<?= base_url()?>index.php/login/Dologout">Logout</a> </li>
    </ul>
</div>
<br></<br>
</body>