<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 

$(document).ready(function() {
    $("#loader").attr('style','display:none');
    //$("#form_natura").hide();
	getNoPengajuan();
});

$("#i_tgl_rm").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2014:2015' });
$("#fbulan").change(function() {
	gridPengajuanRMReload();
});
	
$("#ftahun").change(function() {
	gridPengajuanRMReload();
});
	
$("#frmInputRM").dialog({
        dialogClass : 'dialog1', id:'frmInputRM', bgiframe: true, autoOpen: false, height: 350, width: 640,
        modal: true, title: "Tambah Pengajuan RM Baru",
        resizable: false, moveable: true,
        buttons: {
            'Tutup Pengajuan': 
			function() {
						initFormInput();
                        jQuery( "#frmInputRM" ).dialog( "close" );    
                    },
            'Simpan Pengajuan': function() {
						submitRM(jQuery("#form_mode").val());        
                    }     
        } 
    });
/* detail aktivitas PJ */
var jGrid_sPengajuanRM = null;
var colNamesT_sPengajuanRM = new Array();
var colModelT_sPengajuanRM = new Array();

colNamesT_sPengajuanRM.push('ID');
colModelT_sPengajuanRM.push({name:'RM_PENGAJUAN_ID',index:'RM_PENGAJUAN_ID', hidden:false, width: 80, align:'center'});

colNamesT_sPengajuanRM.push('Periode');
colModelT_sPengajuanRM.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:false, width: 90, align:'center'});

colNamesT_sPengajuanRM.push('Tgl Pengajuan');
colModelT_sPengajuanRM.push({name:'RM_TGL_PENGAJUAN',index:'RM_TGL_PENGAJUAN', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_sPengajuanRM.push('Kode Infras');
colModelT_sPengajuanRM.push({name:'IFCODE',index:'IFCODE', hidden:false, editable: false, edittype: "text", width: 100, align:'center'});

colNamesT_sPengajuanRM.push('Desk. Infras');
colModelT_sPengajuanRM.push({name:'IFNAME',index:'IFNAME', hidden:false, editable: false, edittype: "text", width: 220, align:'center'});

colNamesT_sPengajuanRM.push('Valid Dari');
colModelT_sPengajuanRM.push({name:'RM_VALID_FROM',index:'RM_VALID_FROM', hidden:true, width: 10, align:'center'});

colNamesT_sPengajuanRM.push('Valid Sampai');
colModelT_sPengajuanRM.push({name:'RM_VALID_TO',index:'RM_VALID_TO', hidden:true, width: 10, align:'center'});

colNamesT_sPengajuanRM.push('Budget');
colModelT_sPengajuanRM.push({name:'RM_BUDGET',index:'RM_BUDGET', hidden:true, width: 10, align:'center'});

colNamesT_sPengajuanRM.push('Keterangan');
colModelT_sPengajuanRM.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, width: 200, align:'center'});

colNamesT_sPengajuanRM.push('Status');
colModelT_sPengajuanRM.push({name:'PENGAJUAN_STATUS',index:'PENGAJUAN_STATUS', hidden:false, width: 80, align:'center'});

colNamesT_sPengajuanRM.push('Perusahaan');
colModelT_sPengajuanRM.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, width: 90, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var fPeriode = jQuery("#tahun").val() + jQuery("#bulan").val()
var loadView_sPengajuanRM = function(){
jGrid_sPengajuanRM = jQuery("#list_pengajuan_rm").jqGrid({
	url:url+'pms_c_pengajuan_rm/read_grid_rm/'+fPeriode,
	mtype : "POST", datatype: "json",
	colNames: colNamesT_sPengajuanRM , colModel: colModelT_sPengajuanRM ,
	rownumbers:true, viewrecords: true, multiselect: false, 
	caption: "Pengajuan Rawat Infrastrukturs", 
	rowNum:20, rowList:[10,20,30], multiple:true,
	height: 200, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	 ondblClickRow: function(){
			var id = jQuery("#list_pengajuan_rm").getGridParam('selrow');
			if (id)	{
					var ret = jQuery("#list_pengajuan_rm").getRowData(id);
					jQuery("#i_lokasi").val(ret.BLOCKID)
					$("#sblok").dialog("close");
					//$('#sblok').dialog('destroy');
			}
	},
	loadComplete: function(){ 
			var ids = jQuery("#list_pengajuan_rm").getDataIDs(); 
			for(var i=0;i<ids.length;i++) { 
				var cl = ids[i];
				ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
				jQuery("#list_pengajuan_rm").setRowData(ids[i],{act:ce}) 
			}
		}, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_rm'), sortname: colModelT_sPengajuanRM[0].name
	});
	jGrid_sPengajuanRM.navGrid('#pager_pengajuan_rm',{edit:false,add:false,del:false, search: false, refresh: true});
		  
	}
jQuery("#list_pengajuan_rm").ready(loadView_sPengajuanRM);

/* look up kode infrastruktur */
$(function () {
	$("#i_infras_rm").autocomplete( url+"pms_c_pengajuan_rm/getInfrasC/", {
		dataType: 'ajax', width:350, multiple: false, limit:20, parse: function(data) { // parsing json input
					  return $.map(eval(data), function(row) {
					  return (typeof(row) == 'object')
						? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
						: { data: row, value: '',result: ''};
			});
		}, formatItem: function(item) {
			 return (typeof(item) == 'object')?item.res_dl :'';
		}
	 }).result(function(e, item) {
		$("#i_infras_rm").val(item.res_id );
		$("#i_ket_infras").val(item.res_name );
	});
});
/* end look up kode infrastruktur */

function initFormInput(){
	jQuery("#i_no_rm").val(""); 
	jQuery("#i_tgl_rm").val("");
	jQuery("#i_infras_rm").val("");
	jQuery("#i_ket_infras").val("");
	jQuery("#i_ket_rm").val("");
	jQuery("#i_status_rm").val("");
	jQuery("#form_mode").val("");	
}


function getNoPengajuan(){
   jQuery.post( url+'pms_c_pengajuan_rm/returnNoPengajuanRM/', "",function(status) { 
    	var status = new String(status);
	   if(status.replace(/\s/g,"") != "") { 
			$("#i_no_rm").val(status);
	   }
   });
}

function OpenFormRM(method){
	
	if(method=="post"){
		initFormInput()
		getNoPengajuan();
		jQuery("#loadbutton").show();
		jQuery("#i_status_rm").val("draft");
		jQuery("#form_mode").val("post");	
		jQuery("#frmInputRM").dialog('open');
	} else {
		initFormInput();
		var id = jQuery("#list_pengajuan_rm").getGridParam('selrow');
		var data = jQuery("#list_pengajuan_rm").getRowData(id);  
		if (id){ 
		  if(data.PENGAJUAN_STATUS != 'draft'){
			  alert("data tidak bisa diubah, data sudah proses" );
		  } else {
			  
			  jQuery("#loadbutton").hide();
			  jQuery("#i_no_rm").val(data.RM_PENGAJUAN_ID);
			  jQuery("#i_no_rm").attr('disabled','disabled');
			  jQuery("#i_tgl_rm").val(data.RM_TGL_PENGAJUAN);
			  jQuery("#i_infras_rm").val(data.IFCODE);
			  jQuery("#i_ket_infras").val(data.IFNAME);
			  jQuery("#i_ket_rm").val(data.DESCRIPTION);
			  jQuery("#i_status_rm").val(data.PENGAJUAN_STATUS);
			  jQuery("#form_mode").val("get");
			  jQuery("#frmInputRM").dialog('open');
			  //jQuery("#entriPersediaan").dialog('open');
		  }
		} else {
			alert("Silakan pilih baris yang akan diubah");
		} 	
	}
	
}
	
/* simpan data */
function submitRM(method){
   var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
   var postdata = {};
   var mode = jQuery("#form_mode").val();  
   postdata['RM_PENGAJUAN_ID'] = jQuery("#i_no_rm").val();
   postdata['PERIODE'] = periode; 
   var dateArr = jQuery("#i_tgl_rm").val().split("/");
   dateStr = dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0];
   postdata['RM_TGL_PENGAJUAN'] = jQuery("#i_tgl_rm").val();  
   postdata['IFCODE'] = jQuery("#i_infras_rm").val();  
   postdata['DESCRIPTION'] = jQuery("#i_ket_rm").val();
   postdata['PENGAJUAN_STATUS'] = jQuery("#i_status_rm").val();
  
   var urls = "";
   //if(mode == "post"){
   urls = 'pms_c_pengajuan_rm/submitData';
   // } else if ( mode == "get") {
   //  urls = 'pms_c_pengajuan_rm/updatePengajuanRM';
   // }		 
   /* submit data */
  $.post( url+urls, postdata, function(message) {
	  var status = new String(message);
		if(status.replace(/\s/g,"") != "") { 
			if(status>0){
				//jQuery.jGrowl("data tersimpan", { life: 1000 });
				gridPengajuanRMReload();
				initFormInput();
				jQuery( "#frmInputRM" ).dialog( "close" );
			} else {
				alert(status);
			}
		} 
	});
	/* end submit data */
}

function deleteRM(){
	var postdata = {};
	urls = 'pms_c_pengajuan_rm/deleteData';
	var id = jQuery("#list_pengajuan_rm").getGridParam('selrow');
	if (id){ 
		var data = jQuery("#list_pengajuan_rm").getRowData(id);
		var answer = confirm ("Hapus pengajuan untuk rawat " + data.RM_PENGAJUAN_ID + " : " + data.IFCODE + " ?" )
        if (answer == true) {
			if(data.PENGAJUAN_STATUS == 'disetujui'){
				alert("data sudah disetujui, tidak bisa dibatalkan!!")
			} else {
				 postdata['RM_PENGAJUAN_ID'] = data.RM_PENGAJUAN_ID;  
				 $.post( url+urls, postdata, function(message) {
					var status = new String(message);
					  if(status.replace(/\s/g,"") != "") { 
						  if(status>0){
							  //jQuery.jGrowl("data tersimpan", { life: 1000 });
							  gridPengajuanRMReload();
						  } 
					  } 
				 });
			}
		}
	} else {
		alert("Silakan pilih baris yang akan diubah");
	}
}
/* end simpan data */

 function gridPengajuanRMReload(){
	 var fperiode = jQuery("#ftahun").val() + jQuery("#fbulan").val();		
	 jQuery("#list_pengajuan_rm").setGridParam({url:url+'pms_c_pengajuan_rm/read_grid_rm/'+fperiode}).trigger("reloadGrid");
 }
  		
</script>
<!-- ###################### isi konten ###################### -->           
<div id="tabs1" style="margin-left:5px;">
  <? if(isset($fperiode)){ echo $fperiode; } ?>
  <br/><br/>
  <table id="list_pengajuan_rm" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
  </table>
  <div id="pager_pengajuan_rm" class="scroll"></div>

  <div id="btnGroupInputRM" style="padding-top:10px;">  
  <input type="button"  id="ptambah" value="Tambah Data" onclick="OpenFormRM('post')" />
  <input type="button"  id="pcomplete" value="Ubah Data" onclick="OpenFormRM('get')" />
  <input type="button"  id="pbatal" value="Batalkan Pengajuan" onclick="deleteRM()" />
  </div>

</div>
<!-- ###################### end konten ###################### -->

<div id="frmInputRM">              
<table width="95%" border=1>
<tr>
    <td width="150">No. Pengajuan Rawat</td>
    <td width="20">:</td>
    <td>
    	<input type="text" style="width:100px;" class="input" disabled="disabled" id="i_no_rm" name="i_no_rm" />
        <div id="reloader" style="padding-left:-25px; padding-top:1px;">
            <img id="loadbutton" src="<?= $template_path ?>themes/base/images/Reloader.png" onclick="getNoPengajuan()" />
            <img id="loader" src="<?= $template_path ?>themes/base/images/loading.gif" height="15" width="15"/>
        </div>  &nbsp; <span style="font-size:9px; color:#F00">* </span>
    </td>
</tr>

<tr>
  <td>Periode</td>
  <td>:</td>
  <td><? if(isset($periode)){ echo $periode; }?>
    &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
</tr>

<tr>
  <td>Tanggal Pengajuan</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:100px;" class="input" id="i_tgl_rm" />
    &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
</tr>

<tr>
  <td>Kode Infrastruktur</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_infras_rm" />
    &nbsp; <span style="font-size:9px; color:#F00"> * </span></td>
</tr>

<tr>
  <td>Deskripsi Infrastruktur</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:220px;" class="input" id="i_ket_infras" /></td>
</tr>

<tr>
  <td>Keterangan</td>
  <td>:</td>
  <td><textarea tabindex="3" class="input" style="height:60px; width:260px;"id="i_ket_rm" /></td>
</tr>

<tr>
  <td>Status Pengajuan</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:90px;" disabled="disabled" class="input_disable" id="i_status_rm" />
    &nbsp; <span style="font-size:9px; color:#F00">* </span>
    <input type="hidden" id="form_mode" name="form_mode"></td>
</tr>

<tr>
  	<td colspan="3"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
</tr>
<tr>
  	<td colspan="3">
   
</tr>
<!-- <tr>
   	<td colspan="3" style="padding-right:10px; padding-top:10px;" align="right">
      <input type="button"  id="ptambah" value="Tambah" onclick="submit_headerppj()" />
      <input type="button"  id="pcomplete" value="Selesai" onclick="selesai()" />
      <input type="button"  id="pbatal" value="Batal" onclick="cancel_headerppj()" />
    </td>
</tr> -->
</table>
</div>
