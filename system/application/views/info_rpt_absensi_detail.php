<?php 
	header('expire-header');

	$template_path = base_url().$this->config->item('template_path');
	//$template_path = "http://localhost/lhm/public/";
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

		function giveLocType(){		
			var ids = jQuery("#list_elhm").getGridParam('selrow'); 
			var rets = jQuery("#list_elhm").getRowData(ids); 
			var type = rets.LOCATION_TYPE_CODE;
			return type;
		} 

		var jGrid_elhm = null;
        var colNamesT_elhm = new Array();
        var colModelT_elhm = new Array();
       
        colNamesT_elhm.push('no');
        colModelT_elhm.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});
       							
		colNamesT_elhm.push('Kemandoran');
		colModelT_elhm.push({name:'GANG_CODE',index:'GANG_CODE', editable: false,hidden:false, width: 85, align:'center'});
		
		colNamesT_elhm.push('Tanggal');
		colModelT_elhm.push({name:'LHM_DATE',index:'LHM_DATE', editable: false,hidden:false, width: 80, align:'center'});
		colNamesT_elhm.push('NIK');
		colModelT_elhm.push({name:'NIK',index:'NIK', editable: false,hidden:true, width: 80, align:'center'});
		
		colNamesT_elhm.push('Nama');
		colModelT_elhm.push({name:'NM_K',index:'NM_K', editable: false,hidden:false, width: 120, align:'center'});
		
		colNamesT_elhm.push('Absensi');
		colModelT_elhm.push({name:'TYPE_ABSENSI',index:'TYPE_ABSENSI', editable: false,hidden:false, width: 60, align:'center'});
		
		colNamesT_elhm.push('Tipe Lokasi');
		colModelT_elhm.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false,hidden:true, width: 60, align:'center'});
		
		colNamesT_elhm.push('Lokasi');
		colModelT_elhm.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_elhm.push('Aktivitas');
		colModelT_elhm.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 70, align:'center'});

		colNamesT_elhm.push('HK');
		colModelT_elhm.push({name:'HK_JUMLAH',index:'HK_JUMLAH', editable: false,hidden:false, width: 50, align:'center'});

		colNamesT_elhm.push('Satuan');
		colModelT_elhm.push({name:'HSL_KERJA_UNIT',index:'HSL_KERJA_UNIT', editable: false,hidden:false, width: 60, align:'center'});

		colNamesT_elhm.push('Hasil');
		colModelT_elhm.push({name:'HSL_KERJA_VOLUME',index:'HSL_KERJA_VOLUME', editrules:{number:true}, 
			formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
			decimalPlaces: 2}, editable: false,hidden:false, width: 60, align:'right'});
		
		colNamesT_elhm.push('Tarif / Satuan');
		colModelT_elhm.push({name:'TARIF_SATUAN',index:'TARIF_SATUAN', editrules:{number:true}, 
			formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
			decimalPlaces: 2}, editable: false,hidden:true, width: 30, align:'right'});

		colNamesT_elhm.push('Premi');
		colModelT_elhm.push({name:'PREMI',index:'PREMI', editable: false,editrules:{number:true}, 
			formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
			decimalPlaces: 2}, hidden:false, width: 80, align:'right'});
		
		colNamesT_elhm.push('Lembur');
		colModelT_elhm.push({name:'LEMBUR_JAM',index:'LEMBUR_JAM', editable: false, editrules:{number:true}, 
			formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
			decimalPlaces: 2},hidden:false, width: 50, align:'right'});
		
		colNamesT_elhm.push('Penalti');
		colModelT_elhm.push({name:'PENALTI',index:'PENALTI', editable: false, editrules:{number:true}, 
			formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
			decimalPlaces: 2}, hidden:false, width: 60, align:'right'});

		colNamesT_elhm.push('COMPANY_CODE');
		colModelT_elhm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,hidden:true, width: 30, align:'center'});

		colNamesT_elhm.push('Action');
		colModelT_elhm.push({name:'action',index:'action', editable: false,hidden:false, width: 30, align:'center'});

		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
        
		var loadView_elhm = function(){
            jGrid_elhm = jQuery("#list_elhm").jqGrid({
				url:url+'rpt_absensi/cek_employee_lhm/xx/xx', mtype : "POST", datatype: "json",
                colNames: colNamesT_elhm , colModel: colModelT_elhm , sortname: colNamesT_elhm[2],
				rownumbers: true, rowNum: 400, height: 250,imgpath: gridimgpath,
				loadComplete: function(){ 
					var ids = jQuery("#list_elhm").getDataIDs(); 
					var id = jQuery("#list_elhm").getGridParam('selrow'); 
					var rets = jQuery("#list_elhm").getRowData(id); 
					//var reportSum = jQuery(GridName).jqGrid('getCol', 'Reporting', false, 'sum');
					
					for(var i=0;i<ids.length;i++){ 
						var cl = ids[i]; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick='hapus_elhm()' />"; 
						jQuery("#list_elhm").setRowData(ids[i],{action:ce}) 
					}
					
					/* jQuery(GridName).jqGrid('footerData', 'set', 
					{ 
						BillID: 'Total:',
						Reporting: reportSum 
					}); */

				},
				pager:jQuery("#pager_elhm"), sortorder: "asc", cellEdit: true,
				cellsubmit: 'clientArray', forceFit : true,
			});
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_elhm").ready(loadView_elhm);	
       	$("#list_elhm .ui-jqgrid-titlebar").removeClass('ui-widget-header');
		$("#list_elhm .ui-jqgrid-titlebar").addClass('jqgrid-header');

		$(function() {
			$("#elhm").dialog({
				bgiframe: true, autoOpen: false, height: 350,
				width: 940, position: 'left', modal: true,
				resizable:true, title:"daftar Absensi lhm",				
			}); 
		}); 
	
	function hapus_elhm(){
		var postdata = {}; 
		var ids = jQuery("#list_elhm").getGridParam('selrow'); 
		var data = $("#list_elhm").getRowData(ids) ; 
		postdata['GANG_CODE'] = data.GANG_CODE;
		postdata['ID'] = data.ID; 
		postdata['NIK'] = data.NIK;
		postdata['TGL'] = data.LHM_DATE;
			
		if(data.NIK == undefined){
			alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
		} else {
			var answer = confirm ("Hapus Data ?" )
			if (answer){
				$.post( url+'rpt_absensi/delete_elhm', postdata,function(message,status) { 
			    if(status !== 'success') { 
			      	alert('data gagal terhapus.'); 
			    } else { 
					alert('data berhasil terhapus.')
					jQuery("#list_lhm").setGridParam({url:url+'rpt_absensi/cek_employee_lhm/'+nik+'/'+periode}).trigger("reloadGrid"); 
			    };  
			 });
		}	
	}	
}
	
	
function elhm(nik, periode) {

	jQuery("#list_elhm").setGridParam({url:url+'rpt_absensi/cek_employee_lhm/'+nik+'/'+periode}).trigger("reloadGrid"); 
	$('#elhm').dialog('open');
	 
}

</script>
<? if(isset($absen)){ echo $absen; }?>
<div id="elhm">
<table id="list_elhm" style="font-size: 10px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_elhm" class="scroll"></div>
</div>



</body>
