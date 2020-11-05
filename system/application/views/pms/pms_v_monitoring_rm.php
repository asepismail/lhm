<? 
    $template_path = base_url().$this->config->item('template_path'); 
	$session = $this->session->userdata('GROUP_USER');
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 

$(document).ready(function() {
    $("#loader").attr('style','display:none');
    //$("#form_natura").hide();
	
	jQuery.post( url+'pms_c_monitoring_rm/getUserRole/',"",function(status) { 
	   if(status.replace(/\s/g,"") != "") { 
			jQuery("#iRole").val(status);	
	   }
    });
});

$("#frmMonitoringRM").dialog({
	dialogClass : 'dialog1', id:'frmMonitoringRM', bgiframe: true, autoOpen: false, height: 570, width: 650,
	modal: true, title: "Monitoring Pengajuan RM Baru",
	resizable: false, moveable: true,
	buttons: {
		'Tutup Detail': function() {
					initFormMonitoringInput();
					$( "#frmMonitoringRM" ).dialog( "close" );    
				},
		'Setujui Pengajuan': function() {
					approvePengajuan();        
				}     
	} 
});

$("#RMNotes").dialog({
	dialogClass : 'dialog1', id:'RMNotes', bgiframe: true, autoOpen: false, height: 300, width: 500,
	modal: true, title: "Catatan Pengajuan RM",
	resizable: false, moveable: true,
	buttons: {
		'Tutup Catatan': function() {
					jQuery( "#RMNotes" ).dialog( "close" );    
				},
		'Simpan Catatan': function() {  
		     		addNotes();
				}     
	} 
});

	
/* detail aktivitas PJ */
var jGrid_sMonitoringRM = null;
var colNamesT_sMonitoringRM = new Array();
var colModelT_sMonitoringRM = new Array();

colNamesT_sMonitoringRM.push('ID');
colModelT_sMonitoringRM.push({name:'RM_PENGAJUAN_ID',index:'RM_PENGAJUAN_ID', hidden:false, width: 80, align:'center'});

colNamesT_sMonitoringRM.push('Periode');
colModelT_sMonitoringRM.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_sMonitoringRM.push('Tgl Pengajuan');
colModelT_sMonitoringRM.push({name:'RM_TGL_PENGAJUAN',index:'RM_TGL_PENGAJUAN', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_sMonitoringRM.push('Kode Infras');
colModelT_sMonitoringRM.push({name:'IFCODE',index:'IFCODE', hidden:false, editable: false, edittype: "text", width: 90, align:'center'});

colNamesT_sMonitoringRM.push('Desk. Infras');
colModelT_sMonitoringRM.push({name:'IFNAME',index:'IFNAME', hidden:false, editable: false, edittype: "text", width: 200, align:'center'});

colNamesT_sMonitoringRM.push('Valid Dari');
colModelT_sMonitoringRM.push({name:'RM_VALID_FROM',index:'RM_VALID_FROM', hidden:true, width: 10, align:'center'});

colNamesT_sMonitoringRM.push('Valid Sampai');
colModelT_sMonitoringRM.push({name:'RM_VALID_TO',index:'RM_VALID_TO', hidden:true, width: 10, align:'center'});

colNamesT_sMonitoringRM.push('Budget');
colModelT_sMonitoringRM.push({name:'RM_BUDGET',index:'RM_BUDGET', hidden:true, width: 10, align:'center'});

colNamesT_sMonitoringRM.push('Keterangan');
colModelT_sMonitoringRM.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, width: 180, align:'center'});

colNamesT_sMonitoringRM.push('Status');
colModelT_sMonitoringRM.push({name:'PENGAJUAN_STATUS',index:'PENGAJUAN_STATUS', hidden:false, width: 80, align:'center'});

colNamesT_sMonitoringRM.push('Persetujuan Kebun');
colModelT_sMonitoringRM.push({name:'ISAPPR1',index:'ISAPPR1', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", <?php if($session == '1004' || $session = '1000') { ?> formatoptions: {disabled : false}, <?php } else {?> formatoptions: {disabled : true}, <?php } ?> hidden:false, width: 60, align:'center'});

colNamesT_sMonitoringRM.push('Tgl Persetujuan');
colModelT_sMonitoringRM.push({name:'ISAPPR1_DATE',index:'ISAPPR1_DATE', hidden:false, width: 80, align:'center'});

colNamesT_sMonitoringRM.push('Persetujuan HO');
colModelT_sMonitoringRM.push({name:'ISAPPR2',index:'ISAPPR2', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", <?php if($session == '1002' || $session = '1000') { ?> formatoptions: { disabled : false }, <?php } else {?> formatoptions: {disabled : true}, <?php } ?> hidden:false, width: 60, align:'center'});

colNamesT_sMonitoringRM.push('Tgl Persetujuan HO');
colModelT_sMonitoringRM.push({name:'ISAPPR2_DATE',index:'ISAPPR2_DATE', hidden:false, width: 80, align:'center'});

colNamesT_sMonitoringRM.push('Perusahaan');
colModelT_sMonitoringRM.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, width: 90, align:'center'});

colNamesT_sMonitoringRM.push('Action');
colModelT_sMonitoringRM.push({name:'det',index:'det', editable: false, hidden:false, width: 40, align:'center'}); 


var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_sMonitoringRM = function(){
var fPeriode = jQuery("#ftahun").val() + jQuery("#fbulan").val();
jGrid_sMonitoringRM = jQuery("#list_monitoring_rm").jqGrid({
	url:url+'pms_c_monitoring_rm/read_mgrid_rm/'+fPeriode+'/-',
	mtype : "POST", datatype: "json",
	colNames: colNamesT_sMonitoringRM , colModel: colModelT_sMonitoringRM ,
	rownumbers:true, viewrecords: true, multiselect: false, 
	caption: "Monitoring Pengajuan Rawat Infrastrukturs", 
	rowNum:20, rowList:[10,20,30], multiple:true,
	height: 300, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	 ondblClickRow: function(){
			/* var id = jQuery("#list_monitoring_rm").getGridParam('selrow');
			if (id)	{
					var ret = jQuery("#list_monitoring_rm").getRowData(id);
					jQuery("#i_lokasi").val(ret.BLOCKID)
					$("#sblok").dialog("close");
					//$('#sblok').dialog('destroy');
			} */
	},
	loadComplete: function(){ 
			var ids = jQuery("#list_monitoring_rm").getDataIDs(); 
			for(var i=0;i<ids.length;i++) { 
				var cl = ids[i];
				ce = "<a href='#' onclick=\"OpenFormMonitoringRM();\"/ style='cursor:pointer'>detail</a>";
				jQuery("#list_monitoring_rm").setRowData(ids[i],{det:ce}) 
			}
		}, imgpath: gridimgpath, pager: jQuery('#pager_monitoring_rm'), sortname: colModelT_sMonitoringRM[0].name
	});
	jGrid_sMonitoringRM.navGrid('#pager_monitoring_rm',{edit:false,add:false,del:false, search: false, refresh: true});
	jGrid_sMonitoringRM.navButtonAdd('#pager_monitoring_rm',{
               caption:"Export xls", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
			var compret = jQuery("#i_rm_company").val(); 
	 		var fPeriode = jQuery("#ftahun").val() + jQuery("#fbulan").val();
 
                	window.location =url+'pms_c_monitoring_rm/exportToExcel/'+fPeriode+'/'+compret;
               }, position:"left",
            });

            $("#alertmod").remove();//FIXME	  
	}
jQuery("#list_monitoring_rm").ready(loadView_sMonitoringRM);

function initFormMonitoringInput(){
	jQuery("#i_no_Monitoringrm").val(""); 
	jQuery("#i_tgl_Monitoringrm").val("");
	jQuery("#i_infras_Monitoringrm").val("");
	jQuery("#i_ket_infras_Monitoringrm").val("");
	jQuery("#i_ket_rm_Monitoringrm").val("");
	jQuery("#i_status_rm_Monitoringrm").val("");
	jQuery("#i_appr1").val("");
	jQuery("#i_appr1_date").val("");
	jQuery("#i_appr2").val("");
	jQuery("#i_appr2_date").val("");
	//jQuery("#form_mode").val("");	
}

function initFormNotes(){
	jQuery("#notes_no_Monitoringrm").val(""); 
	jQuery("#notes_Monitoringrm").val("");
	//jQuery("#form_mode").val("");	
}

function OpenFormMonitoringRM(){
	var id = jQuery("#list_monitoring_rm").getGridParam('selrow');
	if (id){ 
		initFormMonitoringInput();
		var data = jQuery("#list_monitoring_rm").getRowData(id);  
		jQuery("#i_no_Monitoringrm").val(data.RM_PENGAJUAN_ID); 
		jQuery("#i_tgl_Monitoringrm").val(data.RM_TGL_PENGAJUAN);
		jQuery("#i_infras_Monitoringrm").val(data.IFCODE);
		jQuery("#i_ket_infras_Monitoringrm").val(data.IFNAME);
		jQuery("#i_ket_rm_Monitoringrm").val(data.DESCRIPTION);
		jQuery("#i_status_rm_Monitoringrm").val(data.PENGAJUAN_STATUS);
		var app1 = data.ISAPPR1;
		if (app1==1) {
			$("#i_appr1").attr('checked',true);
		} else {
			$("#i_appr1").attr('checked',false);
		}
		//jQuery("#i_appr1").val(data.APPR1);
		jQuery("#i_appr1_date").val(data.ISAPPR1_DATE);
		//jQuery("#i_appr2").val(data.APPR2);
		var app2 = data.ISAPPR2;
		if (app2==1) {
			$("#i_appr2").attr('checked',true);
		} else {
			$("#i_appr2").attr('checked',false);
		}
		jQuery("#i_appr2_date").val(data.ISAPPR2_DATE);
	
		gridNotesRMReload();
		jQuery("#frmMonitoringRM").dialog('open');
	} else {
		alert("Silakan pilih baris yang akan diubah");
	} 	
}

function openRMNotes(){
	initFormNotes();
	jQuery("#notes_no_Monitoringrm").val(jQuery("#i_no_Monitoringrm").val()); 
	jQuery("#RMNotes").dialog('open');
}
/* Notes */
var jGrid_sMonitoringNotesRM = null;
var colNamesT_sMonitoringNotesRM = new Array();
var colModelT_sMonitoringNotesRM = new Array();

colNamesT_sMonitoringNotesRM.push('No');
colModelT_sMonitoringNotesRM.push({name:'no',index:'no', hidden:true, width: 80, align:'center'});

colNamesT_sMonitoringNotesRM.push('No Pengajuan');
colModelT_sMonitoringNotesRM.push({name:'RM_PENGAJUAN_ID',index:'RM_PENGAJUAN_ID', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_sMonitoringNotesRM.push('Keterangan');
colModelT_sMonitoringNotesRM.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, editable: false, edittype: "text", width: 280, align:'center'});

colNamesT_sMonitoringNotesRM.push('Dibuat Oleh');
colModelT_sMonitoringNotesRM.push({name:'CREATED',index:'CREATED', hidden:false, editable: false, edittype: "text", width: 100, align:'center'});

colNamesT_sMonitoringNotesRM.push('Tanggal Dibuat');
colModelT_sMonitoringNotesRM.push({name:'CREATEDDATE',index:'CREATEDDATE', hidden:false, width: 110, align:'center'});


var loadView_sMonitoringNotesRM = function(){
jGrid_sMonitoringNotesRM = jQuery("#list_monitoring_rm_notes").jqGrid({
	url:url+'pms_c_monitoring_rm/read_mgrid_rm_notes/xxx',
	mtype : "POST", datatype: "json",
	colNames: colNamesT_sMonitoringNotesRM , colModel: colModelT_sMonitoringNotesRM ,
	rownumbers:true, viewrecords: true, multiselect: false, 
	caption: "Catatan Pengajuan Rawat Infrastruktur", 
	rowNum:20, rowList:[10,20,30], multiple:true,
	height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	imgpath: gridimgpath, pager: jQuery('#pager_monitoring_rm_notes'), sortname: colModelT_sMonitoringNotesRM[1].name
	});
	jGrid_sMonitoringNotesRM.navGrid('#pager_monitoring_rm_notes',{edit:false,add:false,del:false,search:false,refresh: true});
	jGrid_sMonitoringNotesRM.navButtonAdd('#pager_monitoring_rm_notes',{
          caption:"Tambah Catatan", buttonicon:"ui-icon-add", 
          onClickButton: function(){ 
		  		openRMNotes();
		  }, 
          position:"left"
    });
	$("#alertmod").remove();//FIXME	
}
jQuery("#list_monitoring_rm_notes").ready(loadView_sMonitoringNotesRM);


function gridNotesRMReload(){	
	 var id = jQuery("#i_no_Monitoringrm").val(); 
	 jQuery("#list_monitoring_rm_notes").setGridParam({url:url+'pms_c_monitoring_rm/read_mgrid_rm_notes/'+id}).trigger("reloadGrid");
}

$("#fbulan").change(function() {
	gridRMReload();
});
	
$("#ftahun").change(function() {
	gridRMReload();
});
	
	
function gridRMReload(){
	 var compret = jQuery("#i_rm_company").val(); 
	 var fPeriode = jQuery("#ftahun").val() + jQuery("#fbulan").val();
	 jQuery("#list_monitoring_rm").setGridParam({url:url+'pms_c_monitoring_rm/read_mgrid_rm/'+fPeriode+'/'+compret}).trigger("reloadGrid");
}

function addNotes(){
   var postdata = {};
   postdata['RM_PENGAJUAN_ID'] = jQuery("#notes_no_Monitoringrm").val(); 
   postdata['DESCRIPTION'] = jQuery("#notes_Monitoringrm").val();
   var a2 = $("#i_appr2").is(':checked');
   var urls = "";
   //if(mode == "post"){
   urls = 'pms_c_monitoring_rm/addNotes';
   // } else if ( mode == "get") {
   //  urls = 'pms_c_pengajuan_rm/updatePengajuanRM';
   // }		 
   /* submit data */
   if(a2 == true){
	  alert("data sudah disetujui HO, tidak bisa menambahkan catatan");
   } else {
	   $.post( url+urls, postdata, function(message) {
		  var status = new String(message);
			if(status.replace(/\s/g,"") != "") { 
				if(status>0){
					//jQuery.jGrowl("data tersimpan", { life: 1000 });
					gridNotesRMReload(jQuery("#notes_no_Monitoringrm").val());
					initFormNotes();
					jQuery( "#RMNotes" ).dialog( "close" );
				} 
			} 
		});
   }
	/* end submit data */
}


function approvePengajuan(){
   var usergroup = jQuery("#iRole").val();
   if( usergroup == 1005 ){
		alert("user ini tidak mempunyai akses untuk persetujuan pengajuan!!!")
   } else {
	    //$("#isdetail").attr('checked',true);
		var a1 = $("#i_appr1").is(':checked');
		if(a1==true) { 
			a1=1;
		} else { 
			a1=0; 
		}
		
		var a2 = $("#i_appr2").is(':checked');
		if(a2==true) { 
			a2=1;
		} else { 
			a2=0; 
		}
		
		if( usergroup == 1004 ){
			if(a1==1){
				alert("data sudah disetujui sebelumnya");
			} else {
				var answer = confirm ("Setujui pengajuan untuk rawat " + $("#i_infras_Monitoringrm").val()  + " ?" )
                if (answer == true) {
 				   var postdata = {};
				   postdata['RM_PENGAJUAN_ID'] = jQuery("#i_no_Monitoringrm").val(); 
				   urls = 'pms_c_monitoring_rm/approve1';
				   $.post( url+urls, postdata, function(message) {
					  var status = new String(message);
						if(status.replace(/\s/g,"") != "") { 
							if(status>0){
								gridRMReload();
								initFormMonitoringInput();
								jQuery( "#frmMonitoringRM" ).dialog( "close" );
							} 
						} 
					}); 
				}
			}
		}
		
		if( usergroup == 1002 ){
			if(a1==0){
				alert("Data pengajuan rawat untuk " + $("#i_infras_Monitoringrm").val()  + " belum disetujui kebun");
			} else {
				var answer = confirm ("Setujui pengajuan untuk rawat " + $("#i_infras_Monitoringrm").val()  + " ?" )
                if (answer == true) {
					if(a2==1){
						alert("Data sudah disetujui sebelumnya");
					} else {
						 var postdata = {};
						 postdata['RM_PENGAJUAN_ID'] = jQuery("#i_no_Monitoringrm").val(); 
						 urls = 'pms_c_monitoring_rm/approve2';
						 $.post( url+urls, postdata, function(message) {
							var status = new String(message);
							  if(status.replace(/\s/g,"") != "") { 
								  if(status>0){
									  gridRMReload();
									  initFormMonitoringInput();
									  jQuery( "#frmMonitoringRM" ).dialog( "close" );
								  } 
							  } 
						  });
						  /* end submit data */
					}
				}
			}
		}
   }		
}
</script>
<input type="hidden" id="iRole" />
<!-- ###################### isi konten ###################### --> 
<?php  echo "<span style='padding-right:20px;'>Perusahaan : </span>" . $company; 
if($company != ""){
echo "<br/><br/>";
}?> 
<? if(isset($fperiode)){ echo "<span style='padding-right:42px;'>Periode : </span>" .$fperiode; } ?>
<br/> <br/>      
<div id="tabs1" style="margin-left:5px;">
  <table id="list_monitoring_rm" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
  </table>
  <div id="pager_monitoring_rm" class="scroll"></div>
</div>
<!-- ###################### end konten ###################### -->

<div id="frmMonitoringRM">              
<table width="95%" border=1 cellpadding="5" cellspacing="2">
<tr>
    <td width="234">No. Pengajuan Rawat</td>
    <td width="3">:</td>
    <td width="267">
    	<input type="text" style="width:100px;" class="input" disabled="disabled" id="i_no_Monitoringrm" name="i_no_Monitoringrm" />
    </td>
</tr>

<tr>
  <td>Tanggal Pengajuan</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:100px;" disabled="disabled" class="input" name="i_tgl_Monitoringrm" id="i_tgl_Monitoringrm" />
    </td>
</tr> 

<tr>
  <td>Kode Infrastruktur</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:120px;" disabled="disabled" class="input" name="i_infras_Monitoringrm" id="i_infras_Monitoringrm" />
   </td>
</tr>

<tr>
  <td>Deskripsi Infrastruktur</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:300px;" disabled="disabled" class="input" name="i_ket_infras_Monitoringrm" id="i_ket_infras_Monitoringrm" /></td> 
</tr> 

<tr>
  <td>Keterangan</td>
  <td>:</td>
  <td><textarea tabindex="3" class="input" disabled="disabled" style="height:60px; width:300px;" name="i_ket_rm_Monitoringrm" id="i_ket_rm_Monitoringrm" /></td>
</tr>

<tr>
  <td>Status Pengajuan</td> 
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:120px;" disabled="disabled" class="input_disable" id="i_status_rm_Monitoringrm" name="i_status_rm_Monitoringrm" /></td>
</tr>

<tr>
  <td>Persetujuan Kebun</td>
  <td>:</td>
  <td><input disabled="disabled" type="checkbox" value="1" id="i_appr1" name="i_appr1" class="input"/></td>
</tr>

<tr>
  <td>Tanggal Persetujuan Kebun</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:90px;" disabled="disabled" class="input_disable" id="i_appr1_date" /></td>
</tr>

<tr>
  <td>Persetujuan Dept. Teknik</td>
  <td>:</td>
  <td><input disabled="disabled" type="checkbox" value="1" id="i_appr2" name="i_appr2" class="input"/></td>
</tr>

<tr>
  <td height="29">Tanggal Persetujuan Dept. Teknik</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:90px;" disabled="disabled" class="input_disable" id="i_appr2_date" />
    <input type="hidden" id="form_mode" name="form_mode"></td>
</tr>

<tr>
  	<td colspan="3"></td>
</tr>
<tr>
  	<td colspan="3">
   
</tr>
<tr>
	<td colspan="3">
    	<table id="list_monitoring_rm_notes" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
          </table>
          <div id="pager_monitoring_rm_notes" class="scroll"></div>	
    </td>
</tr>
</table>
</div>

<div id="RMNotes">
<table width="95%" border=1 cellpadding="5" cellspacing="2">
<tr>
    <td width="234">No. Pengajuan Rawat</td>
    <td width="3">:</td>
    <td width="267">
    	<input type="text" style="width:100px;" class="input" disabled="disabled" id="notes_no_Monitoringrm" name="notes_no_Monitoringrm" />
    </td>
</tr>

<tr>
  <td>Catatan</td> 
  <td>:</td>
  <td><textarea tabindex="3" class="input" style="height:60px; width:300px;" name="notes_Monitoringrm" id="notes_Monitoringrm" /></td>
</tr>


</table>
</div>