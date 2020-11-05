<? 
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
var url = "<?= base_url().'index.php/' ?>";

jQuery(document).ready(function(){
$(function () {
//document.getElementById("kode_ma").value = "";
//document.getElementById("sat_unit_ma").value = "";
});

$(function() {
	$("#LHM_DATE").datepicker({dateFormat:"yy-mm-dd"});
});

	
/*menu*/

$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu	});
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
});

function cek_employee(){
	var postdata = {}; 
	//var lc = document.getElementById("block").value;
	var bln = document.getElementById("bulan").value;
	var thn = document.getElementById("tahun").value;
	var type_karyawan = document.getElementById("type_karyawan").value;
	var periode = thn+bln;
	
	//var tgl = tdate.replace(/-/gi, "");
	jQuery("#list_gad_tambahan").setGridParam({url:url+"m_gad_tambahan/read_employee/"+periode+"/"+type_karyawan}).trigger("reloadGrid")

}

//-----------------------------------------------------grig-------------------------------------------
		var grid_gad_tambahan = null;
        var colNamesT_gad_tambahan = new Array();
        var colModelT_gad_tambahan = new Array();
		
		colNamesT_gad_tambahan.push('no');
        colModelT_gad_tambahan.push({name:'no',index:'no', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_gad_tambahan.push('nik');
        colModelT_gad_tambahan.push({name:'NIK',index:'NIK', editable: false,hidden:false, width: 70, align:'center'});
		
		colNamesT_gad_tambahan.push('nama');
        colModelT_gad_tambahan.push({name:'NAMA',index:'NAMA', editable: false,hidden:false, width: 120, align:'left'});
		
		colNamesT_gad_tambahan.push('type karyawan');
        colModelT_gad_tambahan.push({name:'TYPE_KARYAWAN',index:'TYPE_KARYAWAN', editable: false,hidden:false, width: 120, align:'center'});

		colNamesT_gad_tambahan.push('status');
        colModelT_gad_tambahan.push({name:'STATUS',index:'STATUS', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_gad_tambahan.push('tunj. jab.');
        colModelT_gad_tambahan.push({name:'tunjab',index:'tunjab', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_gad_tambahan.push('pot. lain');
        colModelT_gad_tambahan.push({name:'potongan_lain',index:'potongan_lain', editable: false,hidden:false, width:80, align:'center'});

		colNamesT_gad_tambahan.push('natura');
        colModelT_gad_tambahan.push({name:'natura',index:'natura', editable: false,hidden:false, width:70, align:'center'});

		colNamesT_gad_tambahan.push('rapel');
        colModelT_gad_tambahan.push({name:'rapel',index:'rapel', editable: false,hidden:false, width:70, align:'center'});

		colNamesT_gad_tambahan.push('thr');
        colModelT_gad_tambahan.push({name:'thr',index:'thr', editable: false,hidden:false, width:70, align:'center'});

		colNamesT_gad_tambahan.push('bonus');
        colModelT_gad_tambahan.push({name:'bonus',index:'bonus', editable: false,hidden:false, width:70, align:'center'});
	
		colNamesT_gad_tambahan.push('pensiun');
        colModelT_gad_tambahan.push({name:'pensiun',index:'pensiun', editable: false,hidden:false, width:70, align:'center'});
		
		colNamesT_gad_tambahan.push('pph 21');
        colModelT_gad_tambahan.push({name:'pph21',index:'pph21', editable: false,hidden:false, width:70, align:'center'});
		
		colNamesT_gad_tambahan.push('pajak bln lalu');
        colModelT_gad_tambahan.push({name:'pajak_lalu',index:'pajak_lalu', editable: false,hidden:false, width:90, align:'center'});
		
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_gad_tambahan = function()
        {
            jGrid_gad_tambahan = jQuery("#list_gad_tambahan").jqGrid(
            {
				url:url+'m_gad_tambahan/read_employee/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_gad_tambahan ,
                colModel: colModelT_gad_tambahan ,
               	sortname: colNamesT_gad_tambahan[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_gad_tambahan"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_gad_tambahan.navGrid('#pager_gad_tambahan',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_gad_tambahan").ready(loadView_gad_tambahan);


	
</script>

<div class="teks_headline"><strong><?php echo $company_dest;?><br>Entri Potongan dan Tunjangan<br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
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

<form id="gad_tambahan">
<table class="teks_">
<tr><td>periode</td><td>:</td><td><? echo $periode; ?></td></tr>
<tr><td>Type Karyawan</td><td>:</td><td><select name='type_karyawan' id="type_karyawan" class="select">
<option value="all"> -- Semua -- </option>
<option value="sku">SKU</option>
<option value="bulanan">Bulanan</option>
<option value="bhl">BHL</option>

</select> &nbsp; <a href="#" onclick="cek_employee()" >go</a></td></tr>
<!-- <tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?></td></tr>
<tr><td>Blok</td><td>:</td><td><? echo $BLOCK; ?></td></tr>  -->
</table>

<br/>

 <table id="list_gad_tambahan" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_gad_tambahan" class="scroll"></div><br/>
</form>
</body>