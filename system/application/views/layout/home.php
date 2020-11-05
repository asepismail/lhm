<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		header('expire-header');
		$template_path = base_url().$this->config->item('template_path');

        global $template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
    <head>
    <title>Plantation System</title>
  
<!-- dialog -->	
	<link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
	
<!-- end dialog --->

    </head>

    <body>

<!-- <script type="text/javascript" src="<?= $template_path ?>NEWUI/jquery-1.4.4.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/jquery.ui.all.js"></script>
    -->	
<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script> 
<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
<script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>


<? if(isset($script)) { echo $script; }?>

<script type="text/javascript">
jQuery(document).ready(function(){
    //$('#switcher').themeswitcher();
	
	/*menu*/
var url = '<?= base_url().'index.php/' ?>';
$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu
											//onHideCheck: onHideCheckMenu,
											//onHide: onHideMenu, 
											//onClick: onClickMenu, 
											//onAnimate: onAnimate
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
			
			$(function() {
				$("#TGL").datepicker({dateFormat:"yy-mm-dd"});
				$("#TO").datepicker({dateFormat:"yy-mm-dd"});
				$('#RPTFROM').datepicker({dateFormat:'dd-mm-yy'});
				$('#RPTTO').datepicker({dateFormat:'dd-mm-yy'});
			});

			<? if(isset($js)) { echo $js; }?>
			
});


	/*end menu*/
	</script>
<div class="teks_headline"><strong><?php echo strtoupper($company_dest);?><br><? echo $judul_header; ?><br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
<div style="float:right">
<ul class="jd_menu jd_menu_slate">
		
		<li><a href="#" class="accessible">Transaksi</a>
			<ul>
				<li><a href="<?= base_url()?>index.php/m_gang_activity_detail">Laporan harian mandor</a></li>
				<li><a href="<?= base_url()?>index.php/p_machine">Buku mesin</a></li>
				<li><a href="<?= base_url()?>index.php/p_vehicle_activity">Buku Kendaraan</a></li>
				<li><a href="<?= base_url()?>index.php/p_workshop_activity">Buku Workshop</a></li>
                <li><a href="<?= base_url()?>index.php/p_kontraktor">Buku Kontraktor</a></li>
               <!--  <li><a href="<?= base_url()?>index.php/p_kontraktor">Buku Kontraktor</a></li> -->
				<hr>
                <li><a href="<?= base_url()?>index.php/p_progress_teknik">Progress Teknik</a></li>	
                 <li><a href="<?= base_url()?>index.php/kpivar">Entry Data KPI</a></li>
         		<li><a href="<?= base_url()?>index.php/rpt_progress_n/">Laporan Progress</a></li>

                   		
                
				<!-- <li><a href="#">Entry Progress</a>
					<ul>
						<li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_panen">Entry Progress Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_tp">Entry Progress Transport Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_sisip">Entry Progress Sisip</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_bibitan">Entry Progress Bibitan</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_tanam">Entry Progress Tanam</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_rawat_if">Entry Progress Rawat Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_infrastruktur">Entry Progress Project Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_bibitan">Entry Progress Project Bibitan</a></li>

					</ul>
				</li> -->
				<li><a href="<?= base_url()?>index.php/rpt_absensi">Absensi Karyawan</a></li>
				
			</ul>
		</li>
        
		<?php if( $user_level == 'SAD' || $user_level == 'ADMHO' || $user_level == 'SAS') { ?>
		<li><a href="#" class="accessible">Master Data</a>
			<ul>              
                <li><a href='<?= base_url()?>index.php/m_employee'>Karyawan</a></li>
                <li><a href='<?= base_url()?>index.php/m_gang'>Kemandoran</a></li>
                <!-- <li><a href='<?= base_url()?>index.php/p_empcopy'>Mutasi Karyawan</a></li> -->
                <? if ( $login_id == 'endang' ||  $login_id == 'nizal' || $login_id == 'pietro' || $login_id=='norman') { ?>
                <li><a href='<?= base_url()?>index.php/m_vehicle'>Kendaraan</a></li>
                <li><a href='<?= base_url()?>index.php/m_machine'>Mesin</a></li>
                <li><a href='<?= base_url()?>index.php/m_bloktanam'>Blok Tanam</a></li>
                <li><a href='<?= base_url()?>index.php/m_workshop'>Workshop</a></li>
                <li><a href='<?= base_url()?>index.php/m_nursery'>Nursery</a></li>
                <li><a href='<?= base_url()?>index.php/m_infras'>Infrastruktur</a></li>
                <? if ( $login_id == 'endang' ||  $login_id == 'nizal' || $login_id == 'pietro' || $login_id=='norman' ) { ?>
                <li><a href='<?= base_url()?>index.php/m_kontraktor'>Kontraktor</a></li>
                <? } ?>
                <? } ?>
               <!--  <li><a href='<?= base_url()?>index.php/m_user'>User</a></li> -->
			</ul>
		</li>
       <!--   <li ><a href='#' class='accessible'>Project</a>
                        <ul>
                            <li><a href='<?= base_url()?>index.php/project/prj_pengajuan'>Daftar Project</a></li>
                            <li><a href='#'>Pengajuan Revisi Project</a></li>
                        </ul>
                        </li> -->
        <?php } ?>
		<?php if( $user_level == 'SAD' || $user_level == 'RVW' || $user_level == 'SAS' || $user_level == 'ADMHO') { ?>
		<li><a href="#" class="accessible" style="height:20px;">Reporting</a>
			<ul style="width: 200px;">
								
					<li><a href="#">Daftar Upah </a>
                    	<ul>
                        	<li><a href="<?= base_url()?>index.php/rpt_du/">Daftar Upah Per Kemandoran</a></li>
                         	<li><a href="<?= base_url()?>index.php/rpt_du/du_afd">Daftar Upah Per Divisi / Bagian</a></li>
                          	<li><a href="<?= base_url()?>index.php/rpt_du_act">Daftar Upah Per Aktivitas</a></li>
                        </ul>
                    </li>
					<li><a href="#">Berita Acara Gaji</a>
						<ul>
					<li><a href="<?= base_url()?>index.php/rpt_ba_rawat">Rawat</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_panen">Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_transportpanen">Transport Panen</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_bibitan">Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_sisip">Sisip</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_pjtanam">Project Tanam & Land Preparation</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjbibitan">Project Persiapan Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjinfrastruktur">Project Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat_infrastruktur">Rawat Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_umum">Gaji Umum</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_vmw">Kendaraan, Workshop, Mesin</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pks">Pabrik</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_tunpot">Tunjangan dan Potongan</a></li>
                    <hr />
                    <li><a href="#">Komparasi DU dan BA</a>
                     <ul>
						<li><a href="<?= base_url()?>index.php/rpt_rekonbadu">Rekonsiliasi BA & DU</a></li>
					 </ul>
                    </li>
						</ul>
					</li>
                   	<!-- 	<li><a href="<?= base_url()?>index.php/rpt_progress/progress">Progress</a></li> -->
					
                    <hr>
                    <li><a href='<?= base_url()?>index.php/rpt_premi'>Premi</a></li>
					<hr>
					<li><a href="<?= base_url()?>index.php/rpt_lhm">Export Data</a>
					</li>	
			</ul>
			
		</li>
		<?php } ?>
		<li style="float:right;">&nbsp;&nbsp;&nbsp;Logged as, <?php echo $login_id; ?> &nbsp; | &nbsp; <a href="<?= base_url()?>index.php/login/Dologout">Logout</a> </li>
	</ul>

</div>

<div id="MyContent">
 <!-- load CONTENT nya -->
                <?php $this->load->view($view);    ?></div>

</body>
</html>
