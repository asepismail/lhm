<? 
   // if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
	header('expire-header');

	$template_path = base_url().$this->config->item('template_path');
	//$template_path = "http://localhost/lhm/public/";
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

<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){
    //$('#switcher').themeswitcher();
	
	/*menu*/
document.getElementById("search_gc").value = "";

$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu
											
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
			
});


var url = "<?= base_url().'index.php/' ?>";
	
	
	function gridReload(){ 
		var gc = jQuery("#search_gc").val(); 
			
		if (gc == ""){
			gc = "-";
		} 
		
		jQuery("#list_empgang").setGridParam({url:url+"m_empgang/search_empgang/"+nik+"/"+dept}).trigger("reloadGrid");		} 


/*grid*/

		var jGrid_empgang = null;
        var colNamesT_empgang = new Array();
        var colModelT_empgang = new Array();	

colNamesT_empgang.push('no');
colModelT_empgang.push({name:'no',index:'no', editable: false, hidden:true, width: 90, align:'center'});
              									                       
colNamesT_empgang.push('Kemandoran');
colModelT_empgang.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 90, align:'center'});
 
colNamesT_empgang.push('Nama Kemandoran');
colModelT_empgang.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:false, width: 220, align:'left'});
 
colNamesT_empgang.push('NIK Anggota');
colModelT_empgang.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_empgang.push('Nama');
colModelT_empgang.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_empgang.push('');
colModelT_empgang.push({name:'',index:'', editable: false, hidden:false, width: 50, align:'center'});


	
	var loadView_empgang = function()
        {
			var gc = jQuery("#search_gc").val(); 
			var periode = jQuery("#tahun").val() + jQuery("#bulan").val(); 
		
			if (gc == ""){
				gc = "-";
			}
		
            jGrid_empgang = jQuery("#list_empgang").jqGrid(
            {
                url:url+'m_empgang/search_empgang/'+gc+'/'+periode,
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_empgang ,
                colModel: colModelT_empgang ,
				rownumbers:true,
                viewrecords: true, 
				multiselect: false, 
				caption: "Data Kemandoran <?php echo $company_dest;?>", 
				//multikey: "ctrlKey", 
				rowNum:20,
				rowList:[10,20,30], 
				multiple:true,
                height: 350,
				cellEdit: false,
				//cellsubmit: 'clientArray',
                imgpath: gridimgpath,
                pager: jQuery('#pager_empgang'),
                sortname: 'GANG_CODE'
                
				
            });
            jGrid_empgang.navGrid('#pager_empgang',{edit:false,add:false,del:false, search: false, refresh: true});
						
        }
		
        jQuery("#list_empgang").ready(loadView_empgang);
		
		$(function() {
			$("#empgang_form").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 320,
				width: 350,
				modal: true,
				title: "form Kemandoran",
				resizable: false,
				moveable: true,
				buttons: {
					'Tutup	': function() {
							
									//init_emp();		
								}
					
				} 
			}); 
		});	
		
		function tambah(){
			
			$("#gang_form").dialog('open');
			$("#form_mode").val("POST");
		}

</script>

<div class="teks_headline"><strong><?php echo $company_dest;?><br>Master Data Karyawan<br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
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
						<li><a href="<?= base_url()?>index.php/p_progress_rawat_if">Entry Progress Rawat Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_infrastruktur">Entry Progress Project Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_bibitan">Entry Progress Project Bibitan</a></li>
					</ul>
				</li>
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
				
				<hr>
					<li><a href="<?= base_url()?>index.php/rpt_lhm">Export Data</a>
					</li>	
			</ul>
			
		</li>
		<?php } ?>
		<li style="float:right;">&nbsp;&nbsp;&nbsp;Logged as, <?php echo $login_id; ?> &nbsp; | &nbsp; <a href="<?= base_url()?>index.php/login/Dologout">Logout</a> </li>
	</ul>

</div>

<table border="0" class="teks_" cellpadding="2" cellspacing="4">
<tr><td colspan="8">Cari Kemandoran :</td></tr>
<tr><td>Kode Kemandoran</td><td>:</td><td><input type="text" class="input" id="search_gc" onkeydown="doSearch(arguments[0]||event)" /></td><td style="padding-left:15px;"></td></tr>
<tr><td>periode</td><td>:</td><td><? echo $periode; ?></td><td style="padding-left:15px;"></td></tr>
</table>
<table id="list_empgang" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_empgang" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="add_data" value="Tambah" onclick="tambah()">&nbsp; </div> <div id="update" class="scroll" style="float:left;"><input type="button"  id="update_data" value="Ubah" onclick="ubah()">&nbsp;</div><div id="delete" class="scroll" style="float:left;"><input type="button"  id="delete_data" value="Hapus" onclick="hapus()"></div> 

<div id="empgang_form">
<table>
<tr><td>Kode Kemandoran</td><td>:</td><td><input tabindex="1" type="text" style="text-transform: uppercase;" class="input" id="i_gangcode"/></td></tr>
<tr><td>Nama Kemandoran</td><td>:</td><td><input tabindex="2" type="text" style="text-transform: uppercase;" class="input" id="i_gangname"/></td></tr>
<tr><td>NIK Mandor</td><td>:</td><td><input tabindex="3" type="text" style="text-transform: uppercase;" class="input" id="i_nikmandor"/></td></tr>
<tr><td>Nama Mandor</td><td>:</td><td><input tabindex="4" type="text" style="text-transform: uppercase;" class="input" id="i_namamandor"/></td></tr>
<tr><td>NIK Kerani</td><td>:</td><td><input tabindex="5" type="text" style="text-transform: uppercase;" class="input" id="i_nikkerani"/></td></tr>
<tr><td>Nama Kerani</td><td>:</td><td><input tabindex="6" type="text" style="text-transform: uppercase;" class="input" id="i_nmkerani"/></td></tr>
<tr><td>NIK Mandor 1</td><td>:</td><td><input tabindex="7" type="text" style="text-transform: uppercase;" class="input" id="i_nikmandor1"/></td></tr>
<tr><td>Departemen</td><td>:</td><td><input tabindex="8" type="text" style="text-transform: uppercase;" class="input" id="i_departemen"/></td></tr>
<tr><td>Divisi</td><td>:</td><td><input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_divisi"/></td></tr>
<tr><td></td><td></td><td colspan="3"><input type="hidden" id="form_mode">
<input tabindex="17" type="button" id="submitdata" value="Simpan" onclick="submit()"></td></tr>

</table>

</div>

</body>
