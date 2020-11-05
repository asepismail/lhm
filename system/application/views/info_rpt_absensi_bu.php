<?php 
	header('expire-header');
	$template_path = base_url().$this->config->item('template_path');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
	<link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
</head>

<body>

<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
<script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>

<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>";
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){

$(function(){
				$('ul.jd_menu').jdMenu({ onShow: loadMenu });
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
		
			jQuery('#submitdata').click(function (){
				var periode = $('#tahun').val() + $('#bulan').val();
				var gc = $('#GANG_CODE').val();
				var jns_laporan = $('#jns_laporan').val();
							
				if ( jns_laporan == 'html'){
					urls = url + 'rpt_absensi/absensi/' + gc + '/' + periode; 
					//$('#frame').attr('src',urls); 
					alert(urls);
					  var postdata = {}; 
					 $.post( urls,postdata, function(message,status) { 
					 	
					 });
				} else if ( jns_laporan == 'excell'){
					urls = url + 'rpt_absensi/absensi_xls/' + gc + '/' + periode;
				$.download(urls,'');}
			});	
			
		});
	
		var jGrid_elhm = null;
        var colNamesT_elhm = new Array();
        var colModelT_elhm = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';
                       
        colNamesT_elhm.push('no');
        colModelT_elhm.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});
       							
		colNamesT_elhm.push('GANG_CODE');
		colModelT_elhm.push({name:'GANG_CODE',index:'GANG_CODE', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('LHM_DATE');
		colModelT_elhm.push({name:'LHM_DATE',index:'LHM_DATE', editable: false,hidden:false, width: 30, align:'center'});
		colNamesT_elhm.push('NIK');
		colModelT_elhm.push({name:'NIK',index:'NIK', editable: false,hidden:true, width: 30, align:'center'});
		
		colNamesT_elhm.push('TYPE_ABSENSI');
		colModelT_elhm.push({name:'TYPE_ABSENSI',index:'TYPE_ABSENSI', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('LOCATION_TYPE_CODE');
		colModelT_elhm.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false,hidden:true, width: 30, align:'center'});
		
		colNamesT_elhm.push('LOCATION_CODE');
		colModelT_elhm.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('ACTIVITY_CODE');
		colModelT_elhm.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 30, align:'center'});

		colNamesT_elhm.push('HK_JUMLAH');
		colModelT_elhm.push({name:'HK_JUMLAH',index:'HK_JUMLAH', editable: false,hidden:false, width: 30, align:'center'});

		colNamesT_elhm.push('HSL_KERJA_UNIT');
		colModelT_elhm.push({name:'HSL_KERJA_UNIT',index:'HSL_KERJA_UNIT', editable: false,hidden:false, width: 30, align:'center'});

		colNamesT_elhm.push('HSL_KERJA_VOLUME');
		colModelT_elhm.push({name:'HSL_KERJA_VOLUME',index:'HSL_KERJA_VOLUME', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('TARIF_SATUAN');
		colModelT_elhm.push({name:'TARIF_SATUAN',index:'TARIF_SATUAN', editable: false,hidden:false, width: 30, align:'center'});

		colNamesT_elhm.push('PREMI');
		colModelT_elhm.push({name:'PREMI',index:'PREMI', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('LEMBUR_JAM');
		colModelT_elhm.push({name:'LEMBUR_JAM',index:'LEMBUR_JAM', editable: false,hidden:false, width: 30, align:'center'});
		
		colNamesT_elhm.push('PENALTI');
		colModelT_elhm.push({name:'PENALTI',index:'PENALTI', editable: false,hidden:false, width: 30, align:'center'});

		colNamesT_elhm.push('COMPANY_CODE');
		colModelT_elhm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,hidden:false, width: 30, align:'center'});
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
        
		var loadView_elhm = function()
        {
            jGrid_elhm = jQuery("#list_elhm").jqGrid(
            {
				url:url+'rpt_absensi/cek_employee_lhm/13080045/201004',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_elhm ,
                colModel: colModelT_elhm ,
               	sortname: colNamesT_elhm[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_elhm"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
			});
           

            $("#alertmod").remove();//FIXME
			
        }
        jQuery("#list_elhm").ready(loadView_elhm);	
		
		$(function() {
			$("#elhm").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 360,
				width: 690,
				position: ['top','left'],
				modal: true,
				resizable:true,
				title:"daftar lhm",				
			}); 
		});
			
</script>

<div class="teks_headline"><strong><?php echo $company_dest;?><br>Laporan Harian Mandor<br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
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
						<li><a href="<?= base_url()?>index.php/p_progress_rawat_if">Entry Progress Rawat Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_infrastruktur">Entry Progress Project Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_bibitan">Entry Progress Project Bibitan</a></li>

					</ul>
				</li>
				<li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li>
				<li><a href="<?= base_url()?>index.php/rpt_absensi">Absensi Karyawan</a></li>
				<!-- <li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
				<li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li> -->
			</ul>
		</li>
		<?php if( $user_level == 'SAD' || $user_level == 'ADM') { ?>
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
        <?php } ?>
		<?php if( $user_level == 'SAD' ) { ?>
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
					<li><a href="<?= base_url()?>index.php/rpt_progress/progress">Progress</a>
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

<form id="gen_x" action="" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Afdeling</td><td>:</td><td style="font-size: 11px;"><? echo $GANG_CODE; ?></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode ?></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_laporan" class="select" id="jns_laporan">
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" id="submitdata" value="Generate"></td></tr>
</table>

<!-- <iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe> -->

      <!--   <div id="popupContact">
                <a id="popupContactClose">[ X ]</a>
<table id="list_elhm" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_elhm" class="scroll"></div><br/>
        </div>
        <div id="backgroundPopup"></div>
<div id="elhm"> 

</div>-->

</form>
</body>
