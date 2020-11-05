<? 
   // if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
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
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){

	$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu		});
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

});
</script>

<div class="teks_headline"><strong class="accessible"><?php echo $company_dest;?><br>Laporan Buku Kendaraan<br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
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
						<li><a href="<?= base_url()?>index.php/p_progress_bibitan">Entry Progress Bibitan</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_sisip">Entry Progress Sisip</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_lc">Entry Progress LC</a></li>
						<li><a href="<?= base_url()?>index.php/p_progress_infrastruktur">Entry Progress Infrastruktur</a></li>
					</ul>
				</li>
				<li><a href="<?= base_url()?>index.php/rpt_absensi">Absensi Karyawan</a></li>
				
			</ul>
		</li>
		<li><a href="#" class="accessible">Master Data</a>
			<ul>
				<li><a href="<?= base_url()?>index.php/m_employee">Karyawan</a></li>
				<li><a href="<?= base_url()?>index.php/m_gang">Kemandoran</a></li>
				<li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li>
				<li><a href="#">Kendaraan</a></li>
				<li><a href="#">Mesin</a></li>
				<li><a href="#">Blok Tanam</a></li>
				<li><a href="#">Workshop</a></li>
			</ul>
		</li>
		<?php if( $user_level == 'SAD' ) { ?>
		<li><a href="#" class="accessible" style="height:20px;">Reporting</a>
			<ul style="width: 200px;">
								
					<li><a href="<?= base_url()?>index.php/rpt_du">Daftar Upah</a></li>
					<li><a href="#">Berita Acara</a>
						<ul>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_rawat">Berita Acara Gaji Rawat</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_panen">Berita Acara Gaji Panen</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_bibitan">Berita Acara Gaji Bibitan</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_tanamsisip">Berita Acara Gaji Tanam</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_lc">Berita Acara Gaji LC</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba/ba_infrastruktur">Berita Acara Gaji Infrastruktur</a></li>
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
 </body>