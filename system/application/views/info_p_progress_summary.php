<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';

var url = "<?= base_url().'index.php/' ?>";
jQuery(document).ready(function(){
var periode = $("#tahun").val() + $("#bulan").val();
					var act = $("#actype").val();			
					var afd = $("#proafd").val();

var jGrid_sprg = null;
var colNamesT_sprg = new Array();
var colModelT_sprg = new Array();

colNamesT_sprg.push('no');
colModelT_sprg.push({name:'no_sprg',index:'no_sprg', sortable:false, resizable:true, editable: false,
				  hidden:true, width: 30, align:'center'});

colNamesT_sprg.push('id');
colModelT_sprg.push({name:'PROGSUM_ID',index:'PROGSUM_ID', editable: false,
						hidden:true, width: 30, align:'center'});
       
colNamesT_sprg.push('Perusahaan');
colModelT_sprg.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,
						hidden:true, width: 70, align:'center'});

colNamesT_sprg.push('Periode');
colModelT_sprg.push({name:'PERIODE',index:'PERIODE', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_sprg.push('Kode');
colModelT_sprg.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,
						hidden:false, width: 75, align:'center'});

colNamesT_sprg.push('Deskripsi Aktivitas');
colModelT_sprg.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false, 
						hidden:false, width: 180, align:'center'});

colNamesT_sprg.push('Lokasi');
colModelT_sprg.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false,
						hidden:false, width: 120, align:'center'});       

colNamesT_sprg.push('Desk. Lokasi');
colModelT_sprg.push({name:'LOCATION_DESC',index:'LOCATION_DESC', editable: false,
						hidden:true, width: 70, align:'center'});       

colNamesT_sprg.push('LHM 1');
colModelT_sprg.push({name:'QTY1_LHM',index:'QTY1_LHM', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00", decimalPlaces: 2}, 	
		editable: false,hidden:false, width: 60, align:'right'});       

colNamesT_sprg.push('VH 1');
colModelT_sprg.push({name:'QTY1_BKE',index:'QTY1_BKE', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00", decimalPlaces: 2}, 
		editrules:{number:true}, editable: false,hidden:false, width: 60, align:'right'}); 

colNamesT_sprg.push('KT 1');
colModelT_sprg.push({name:'QTY1_BKT',index:'QTY1_BKT', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00", decimalPlaces: 2}, 	
		editrules:{number:true}, editable: false,hidden:false, width: 50, align:'right'}); 

colNamesT_sprg.push('Sat 1');
colModelT_sprg.push({name:'UNIT1',index:'UNIT1', editable: false,hidden:false, width: 65, align:'center'}); 

colNamesT_sprg.push('Total 1');
colModelT_sprg.push({name:'TOTAL1',index:'TOTAL1', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: true,hidden:false, width: 70, align:'right'}); 
		
colNamesT_sprg.push('Penyesuaian 1');
colModelT_sprg.push({name:'QTY1_PENYESUAIAN',index:'QTY1_PENYESUAIAN', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: false,hidden:false, width: 90, align:'right'}); 

colNamesT_sprg.push('Final 1');
colModelT_sprg.push({name:'FINAL1',index:'FINAL1', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: true,hidden:false, width: 90, align:'right'});
				
colNamesT_sprg.push('LHM 2');
colModelT_sprg.push({name:'QTY2_LHM',index:'QTY2_LHM', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00", decimalPlaces: 2}, 	
		editable: false,hidden:false, width: 60, align:'right'});       

colNamesT_sprg.push('VH 2');
colModelT_sprg.push({name:'QTY2_BKE',index:'QTY2_BKE', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, 
		editrules:{number:true}, editable: false,hidden:true, width: 60, align:'center'}); 

colNamesT_sprg.push('KT 2');
colModelT_sprg.push({name:'QTY2_BKT',index:'QTY2_BKT', editrules:{number:true}, formatter:'number', 
		formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, 	
		editrules:{number:true}, editable: false,hidden:true, width: 60, align:'center'}); 

colNamesT_sprg.push('Sat 2');
colModelT_sprg.push({name:'UNIT2',index:'UNIT2', editable: false,hidden:false, width: 65, align:'center'}); 

colNamesT_sprg.push('Total 2');
colModelT_sprg.push({name:'TOTAL2',index:'TOTAL2', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: true,hidden:false, width: 70, align:'right'});
		
colNamesT_sprg.push('Penyesuaian 2');
colModelT_sprg.push({name:'QTY2_PENYESUAIAN',index:'QTY2_PENYESUAIAN', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: false,hidden:false, width: 90, align:'right'});

colNamesT_sprg.push('Final 2');
colModelT_sprg.push({name:'FINAL2',index:'FINAL2', editrules:{number:true}, 
		formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: "0.00",
		decimalPlaces: 2}, editrules:{number:true}, editable: true,hidden:false, width: 90, align:'right'});
		
        var lastsel; var jdesc1; var lRow; var lCol; var i = 0;
		var lasteditedcell;
		var lastvalue = null;
		var loadView_sprg = function(){
        jGrid_sprg = jQuery("#list_sprg").jqGrid({
                url:url+'p_progress_summary/read_grid/'+periode+'/'+act+'/'+afd,
				editurl: url+'p_progress_summary/submit',
                mtype : "POST", datatype: "json", colNames: colNamesT_sprg , colModel: colModelT_sprg ,
                sortname: colModelT_sprg.name, pager:jQuery("#pager_sprg"), rowNum: 400, rownumbers: true,
                height: 370, imgpath: gridimgpath, sortorder: "asc", 
				cellEdit: true,
				cellsubmit: 'remote',
				cellurl : url+'p_progress_summary/submit',
				beforeEditCell :  function(rowid, cellname, value, iRow, iCol) {
					lasteditedcell = null;
					lasteditedcell = value;	
				},
				
				afterSaveCell : function(rowid, cellname, value, iRow, iCol) {
					$.post( url+'p_progress_summary/cek/'+cellname+'/'+rowid, '',function(status) { 
                     	var status = new String(status);
                        if(status.replace(/\s/g,"") != "") { 
                         	lastvalue = status
						};
                    });
					if(lastvalue == lasteditedcell){
						if(cellname == 'QTY1_PENYESUAIAN') { 
							  jQuery("#list_sprg").setRowData(rowid,{QTY1_PENYESUAIAN:lastvalue});
						}
						if(cellname == 'QTY2_PENYESUAIAN') { 
							  jQuery("#list_sprg").setRowData(rowid,{QTY2_PENYESUAIAN:lastvalue});
						}
						notify("data gagal tersimpan");
					} else {
						if(lasteditedcell!=value){
							notify("data tersimpan");
						}
					}	
				}
            });
        jGrid_sprg.navGrid('#pager_sprg',{edit:false,del:false,add:false, search: false, refresh: true}); 
		jGrid_sprg.navButtonAdd('#pager_sprg',{
               caption:"Export ke Excell", buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
			   		var periode = $("#tahun").val() + $("#bulan").val();
					var act = $("#actype").val();			
					var afd = $("#proafd").val();
			   		//alert(url+'p_progress_summary/create_excel/'+periode+'/'+act+'/'+afd);
                    window.location = url+'p_progress_summary/create_excel/'+periode+'/'+act+'/'+afd;
               }, position:"left",
            });
		}
		jQuery("#list_sprg").ready(loadView_sprg);
		
		function notify(message){
			var title, opts, container;
			opts = {};
				opts.classes = ["gray", ""];
				opts.autoHideDelay = 1000;
					opts.classes.push("slide");
					opts.hideStyle = {
						opacity: 0,
						left: "400px"
					};
					opts.showStyle = {
						opacity: 1,
						left: 0
					};
			
				container = "#freeow-br";
				$(container).freeow("", message, opts);
		} 
		
		
		
});	

function gridReload(){
	var periode = $("#tahun").val() + $("#bulan").val();
	var act = $("#actype").val();			
	var afd = $("#proafd").val();
	jQuery("#list_sprg").setGridParam({url:url+'p_progress_summary/read_grid/'+periode+'/'+act+'/'+afd}).trigger("reloadGrid");
}

function generateData(){
	var periode = $("#tahun").val() + $("#bulan").val();
	var act = $("#actype").val();			
	var afd = $("#proafd").val();
			var confirmsg = confirm("Semua data yang sudah ada sebelumnya akan terhapus dan tidak dapat diulang kembali, lanjutkan generate data progress?");
            if(confirmsg){
				$.post( url+'p_progress_summary/generateData/'+periode, '',function(status) { 
                     	var status = new String(status);
                       	if(status.replace(/\s/g,"") != "") { 
                            alert(status);
							gridReload();
                     };
				});	
			}
		}
</script>
<div id="freeow-br" class="freeow freeow-bottom-right"></div>
<form id="form_lhm" name="form_lhm">
<table width="550" class="teks_">
<tr>
  <td width="135">Periode</td><td width="9">:</td><td width="374"><? echo $periode; ?>&nbsp;</td></tr>
<tr>
  <td>Aktivitas</td><td>:</td><td><? echo $acttype; ?>&nbsp;</td></tr>
<tr>
  <td>Afdeling</td><td>:</td><td><? echo $afdeling; ?>&nbsp;</td></tr>

</table>

<!-- <div id="status_entri" style="color:#F00;">Periode ini sudah ditutup!!</div> -->
<div id="forms_progress">
 <table id="list_sprg" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_sprg" class="scroll"></div><br/>
                <div id="save" class="scroll" style="float:left; padding-right:4px;"><input type="button" id="submitdata" value="Generate Data" onClick="generateData()" class="basicBtn"></div>
                
                
                
</form>