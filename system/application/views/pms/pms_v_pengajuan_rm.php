<? 
    $template_path = base_url().$this->config->item('template_path');  
?>


<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
var screenWidth = $(window).width();
var screenHeight = $(window).height();

$(document).ready(function() {
    $("#loaderRM").attr('style','display:none');
    //$("#form_natura").hide();
	getNoPengajuan();
});

$("#i_tgl_RM").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2014:2015' });

$("#fbulan").change(function() {
	gridPengajuanRMReload();
});
	
$("#ftahun").change(function() {
	gridPengajuanRMReload();
});

$("#frmInputRM").dialog({
        dialogClass : 'dialog1', id:'frmInputRM', bgiframe: true, autoOpen: false, width: 800, height:530,
		modal: true, title: "Tambah Pengajuan RM Baru", 
        resizable: false, moveable: true
		/* ,buttons: {
            'Tutup Pengajuan': 
			function() {
						initFormInput();
                        jQuery( "#frmInputRM" ).dialog( "close" );    
                    },
            'Simpan Pengajuan': function() {
						submitRM(jQuery("#form_mode").val());        
                    }     
        } */
});

$("#inputRMDetail").dialog({
        dialogClass : 'dialog1', id:'FormInputRMDetail', bgiframe: true, autoOpen: false, width: 450, height:260,
		modal: true, title: "Tambah Pengajuan Detail RM", 
        resizable: false, moveable: true
		
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
	height: 300, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	 ondblClickRow: function(){
			var id = jQuery("#list_pengajuan_rm").getGridParam('selrow');
			if (id)	{
					var ret = jQuery("#list_pengajuan_rm").getRowData(id);
					jQuery("#i_lokasi").val(ret.BLOCKID)
					$("#sblok").dialog("close");
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
	jQuery("#i_no_RM").val(""); 
	jQuery("#i_tgl_RM").val("");
	jQuery("#i_infras_rm").val("");
	jQuery("#i_ket_infras").val("");
	jQuery("#i_ket_rm").val("");
	jQuery("#i_status_rm").val("");
	jQuery("#form_mode").val("");	
}


function getNoPengajuan(){
   var sstatus = "";
   jQuery.post( url+'pms_c_pengajuan_rm/returnNoPengajuanRM/', "",function(status) { 
    	var sstatus = new String(status.replace(/\s/g,""));
		if(status != "") { 
			//jQuery("#i_no_RM").val(sstatus);
			document.getElementById("i_no_RM").value = sstatus;
	   }
   });
}

/* fungsi detail pengajuan */
jQuery( "#pAddPengajuanRMDetail" ).click(function() {
	submitRMDetail();
	
});


jQuery( "#pClosePengajuanRMDetail" ).click(function() {
	jQuery("#inputRMDetail").dialog('close');
	initFormRMDetail();
	
});

function openFormRMDetail(method){
	var noPengajuanDetail = document.getElementById("i_no_RM").value;
	jQuery.post( url+'pms_c_pengajuan_rm/cekDataDetail/'+noPengajuanDetail, "",function(status) { 
    	var sstatus = new String(status.replace(/\s/g,""));
		if(status > 0 ) { 
			if(method == "post"){
				initFormRMDetail();
				
				jQuery("#RMdetail_i_no_rm").val(noPengajuanDetail);
				jQuery("#inputRMDetail").dialog('open');
				jQuery("#form_detail_mode").val(method);
			} else {
				initFormRMDetail();
				var id = jQuery("#list_pengajuan_rm_detail").getGridParam('selrow');
				var data = jQuery("#list_pengajuan_rm_detail").getRowData(id);  
				if (id){ 
				  	jQuery("#RMdetail_i_no_rm").val(data.RM_PENGAJUAN_ID);
					jQuery("#RMdetail_i_no_rm").attr('disabled','disabled');
					jQuery("#RMdetail_i_aktivitas").val(data.ACTIVITY_CODE);
					jQuery("#RMdetail_ket_aktivitas").val(data.COA_DESCRIPTION);
					jQuery("#RMdetail_i_qty").val(data.QTY);
					jQuery("#RMdetail_i_uom").val(data.UOM);
					jQuery("#RMdetail_i_qsat").val(data.RPSAT);
					jQuery("#RMdetail_i_ttl").val(data.RPTTL);
					jQuery("#inputRMDetail").dialog('open');
					jQuery("#form_detail_mode").val(method);	
				} else {
					alert("Silakan pilih baris yang akan diubah");
				} 	
			}					
	    } else {
			alert("mohon simpan data pengajuan terlebih dahulu");
		}
   });
}

function initFormRMDetail(){
	jQuery("#RMdetail_i_no_rm").val("");
	jQuery("#RMdetail_i_aktivitas").val("");
	jQuery("#RMdetail_ket_aktivitas").val("");
	jQuery("#RMdetail_i_qty").val("");
	jQuery("#RMdetail_i_uom").val("");
	jQuery("#RMdetail_i_qsat").val("");
	jQuery("#RMdetail_i_ttl").val("");
}

function getActRM(){
	var ifCode = $("#i_infras_rm").val();
	$("#RMdetail_i_aktivitas").autocomplete(  url+"pms_c_pengajuan_rm/getActivity/" + ifCode, {  dataType: 'ajax',
		width:350, multiple: false,  limit:20,
		parse: function(data) { // parsing json input
			  return $.map(eval(data), function(row) {
			  return (typeof(row) == 'object')
				  ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
				  : { data: row, value: '',result: ''};
			  });
			}, formatItem: function(item) {
			   return (typeof(item) == 'object')?item.res_dl :'';
			}
		 }).result(function(e, item) {
			$("#RMdetail_i_aktivitas").val(item.res_id );
			$("#RMdetail_ket_aktivitas").val(item.res_name );
	 });
};

$("#RMdetail_i_qty").keyup(function() {
	if($("#RMdetail_i_qty").val() != "" ){
		if( $("#RMdetail_i_qsat").val() != "" ){
			var total = $("#RMdetail_i_qty").val() * $("#RMdetail_i_qsat").val();
			$("#RMdetail_i_ttl").val(total)
		}
	}
});  
	
$("#RMdetail_i_qsat").keyup(function() {
	if($("#RMdetail_i_qty").val() != "" ){
		if( $("#RMdetail_i_qsat").val() != "" ){
			var total = $("#RMdetail_i_qty").val() * $("#RMdetail_i_qsat").val();
			$("#RMdetail_i_ttl").val(total)
		}
	}
});

function submitRMDetail(){
   var _headerPengajuanID = document.getElementById("i_no_RM").value;
   if( _headerPengajuanID == "" ){
	   alert("Mohon pilih nomor pengajuan terlebih dahulu");
   } else if( jQuery("#RMdetail_i_aktivitas").val() == "" ) {
	   alert("Mohon isi detail aktivitas terlebih dahulu");
   } else if( jQuery("#RMdetail_i_qty").val() == "" ) {
	   alert("Mohon isi detail Qty terlebih dahulu");
   } else if( jQuery("#RMdetail_i_uom").val() == "" ) {
	   alert("Mohon isi detail satuan terlebih dahulu");
   } else if( jQuery("#RMdetail_i_qsat").val() == "" ) {
	    alert("Mohon isi rupiah qty / satuan terlebih dahulu");
   } else {
	   var postdata = {};
	   
	   var mode = jQuery("#form_mode_detail").val(); 
	   postdata['mode'] = mode; 
	   postdata['RM_PENGAJUAN_ID'] = _headerPengajuanID;
	   postdata['ACTIVITY_CODE'] = jQuery("#RMdetail_i_aktivitas").val(); 
	   postdata['QTY'] = jQuery("#RMdetail_i_qty").val();  
	   postdata['UOM'] = jQuery("#RMdetail_i_uom").val();  
	   postdata['RPSAT'] = jQuery("#RMdetail_i_qsat").val();
	   postdata['RPTTL'] = jQuery("#RMdetail_i_ttl").val();
	  
	  var urls = "";
	  urls = 'pms_c_pengajuan_rm/submitDataDetail';
	  $.post( url+urls, postdata, function(message) {
		  var status = new String(message);
			if(status.replace(/\s/g,"") != "") { 
				if(status>0){
					gridDetailRMReload();
					initFormRMDetail();
					jQuery( "#inputRMDetail" ).dialog( "close" );
					jQuery( "#frmInputRM" ).dialog( "close" );
					jQuery( "#frmInputRM" ).dialog( "open" );
				} else {
					alert(status);
				}
			} 
	   });
		/* end submit data */
   }
}	

function voidRMDetail(){
	var id = jQuery("#list_pengajuan_rm_detail").getGridParam('selrow');
	var postdata = {};
	urls = 'pms_c_pengajuan_rm/voidDataDetail';
	var id = jQuery("#list_pengajuan_rm_detail").getGridParam('selrow');
	if (id){ 
		var data = jQuery("#list_pengajuan_rm_detail").getRowData(id);
		var answer = confirm ("Hapus pengajuan detail untuk rawat " + data.RM_PENGAJUAN_ID + " : " + data.ACTIVITY_CODE + " ?" )
        if (answer == true) {
			 postdata['RM_PENGAJUAN_ID'] = data.RM_PENGAJUAN_ID;
			 postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE;  
			 $.post( url+urls, postdata, function(message) {
				var status = new String(message);
				  if(status.replace(/\s/g,"") != "") { 
					  if(status>0){
						 gridDetailRMReload();
					  } 
				  } 
			 });
		}
	} else {
		alert("Silakan pilih baris yang akan diubah");
	} 
}

/* end fungsi detail pengajuan */

/* fungsi window header pengajuan */

jQuery( "#pAddPengajuanRM" ).click(function() {
	submitRM(jQuery("#form_mode").val()); 
});

jQuery( "#pClosePengajuanRM" ).click(function() {
	initFormInput();
	jQuery( "#frmInputRM" ).dialog( "close" );
});

function OpenFormRM(method){
	if(method=="post"){
		initFormInput()
		getNoPengajuan();
		jQuery("#loadbuttonRM").show();
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
			  
			  jQuery("#loadbuttonRM").hide();
			  jQuery("#i_no_RM").val(data.RM_PENGAJUAN_ID);
			  jQuery("#i_no_RM").attr('disabled','disabled');
			  jQuery("#i_tgl_RM").val(data.RM_TGL_PENGAJUAN);
			  jQuery("#i_infras_rm").val(data.IFCODE);
			  jQuery("#i_ket_infras").val(data.IFNAME);
			  jQuery("#i_ket_rm").val(data.DESCRIPTION);
			  jQuery("#i_status_rm").val(data.PENGAJUAN_STATUS);
			  jQuery("#form_mode").val("get");
			  gridDetailRMReload();
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
   postdata['RM_PENGAJUAN_ID'] = jQuery("#i_no_RM").val();
   postdata['PERIODE'] = periode; 
   var dateArr = jQuery("#i_tgl_RM").val().split("/");
   dateStr = dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0];
   postdata['RM_TGL_PENGAJUAN'] = jQuery("#i_tgl_RM").val();  
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
 /* end form header pengajuan */
 
 
 /* detail pengajuan */
 
 /* Notes */
var jGrid_pengajuanDetail = null;
var colNamesT_pengajuanDetailRM = new Array();
var colModelT_pengajuanDetailRM = new Array();
			
colNamesT_pengajuanDetailRM.push('RM_PENGAJUAN_DETAIL_ID');
colModelT_pengajuanDetailRM.push({name:'RM_PENGAJUAN_DETAIL_ID',index:'RM_PENGAJUAN_DETAIL_ID', hidden:true, width: 80, align:'center'});

colNamesT_pengajuanDetailRM.push('No Pengajuan');
colModelT_pengajuanDetailRM.push({name:'RM_PENGAJUAN_ID',index:'RM_PENGAJUAN_ID', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_pengajuanDetailRM.push('Kode');
colModelT_pengajuanDetailRM.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', hidden:false, editable: false, edittype: "text", width: 80, align:'center'});

colNamesT_pengajuanDetailRM.push('Deskripsi');
colModelT_pengajuanDetailRM.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', hidden:false, editable: false, edittype: "text", width: 160, align:'center'});

colNamesT_pengajuanDetailRM.push('Qty');
colModelT_pengajuanDetailRM.push({name:'QTY',index:'QTY', hidden:false, editable: false, edittype: "text", width: 80, align:'center'});

colNamesT_pengajuanDetailRM.push('Unit Satuan');
colModelT_pengajuanDetailRM.push({name:'UOM',index:'UOM', hidden:false, editable: false, edittype: "text", width: 100, align:'center'});

colNamesT_pengajuanDetailRM.push('Rp/Sat');
colModelT_pengajuanDetailRM.push({name:'RPSAT',index:'RPSAT', hidden:false, editable: false, edittype: "text", width: 100, align:'center'});

colNamesT_pengajuanDetailRM.push('Total (Rp)');
colModelT_pengajuanDetailRM.push({name:'RPTTL',index:'RPTTL', hidden:false, editable: false, edittype: "text", width: 110, align:'center'});

colNamesT_pengajuanDetailRM.push('Tanggal Dibuat');
colModelT_pengajuanDetailRM.push({name:'CREATED',index:'CREATED', hidden:true, width: 110, align:'center'});

colNamesT_pengajuanDetailRM.push('Dibuat Oleh');
colModelT_pengajuanDetailRM.push({name:'CREATED_DATE',index:'CREATED_DATE', hidden:true, width: 110, align:'center'});

colNamesT_pengajuanDetailRM.push('Tanggal Diubah');
colModelT_pengajuanDetailRM.push({name:'UPDATED',index:'UPDATED', hidden:true, width: 110, align:'center'});

colNamesT_pengajuanDetailRM.push('Diubah Oleh');
colModelT_pengajuanDetailRM.push({name:'UPDATED_DATE',index:'UPDATED_DATE', hidden:true, width: 110, align:'center'});

var idDetail = document.getElementById("i_no_RM").value; 
var loadView_sPengajuanDetail = function(){
jGrid_pengajuanDetail = jQuery("#list_pengajuan_rm_detail").jqGrid({
	url:url+'pms_c_pengajuan_rm/read_grid_detail_rm/'+idDetail,
	mtype : "POST", datatype: "json",
	colNames: colNamesT_pengajuanDetailRM , colModel: colModelT_pengajuanDetailRM ,
	rownumbers:true, viewrecords: true, multiselect: false, 
	caption: "Detail pengajuan rawat infrastruktur", 
	rowNum:20, rowList:[10,20,30], multiple:true,
	height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_rm_detail'), sortname: colModelT_pengajuanDetailRM[1].name
	});
	jGrid_pengajuanDetail.navGrid('#pager_pengajuan_rm_detail',{edit:false,add:false,del:false,search:false,refresh: true});
	
	jGrid_pengajuanDetail.navButtonAdd('#pager_pengajuan_rm_detail',{
          caption:"Tambah", buttonicon:"ui-icon-add", 
          onClickButton: function(){ 
		  		openFormRMDetail('post');
		  }, 
          position:"left"
    });
	jGrid_pengajuanDetail.navButtonAdd('#pager_pengajuan_rm_detail',{
          caption:"Ubah", buttonicon:"ui-icon-edit", 
          onClickButton: function(){ 
		  		openFormRMDetail('get');
		  }, 
          position:"left"
    });
	jGrid_pengajuanDetail.navButtonAdd('#pager_pengajuan_rm_detail',{
          caption:"Hapus", buttonicon:"ui-icon-delete", 
          onClickButton: function(){ 
		  		voidRMDetail();
		  }, 
          position:"left"
    });
	$("#alertmod").remove();//FIXME	
}
jQuery("#list_pengajuan_rm_detail").ready(loadView_sPengajuanDetail);

function gridDetailRMReload(){	
	 var id = document.getElementById("i_no_RM").value; 
	 jQuery("#list_pengajuan_rm_detail").setGridParam({url:url+'pms_c_pengajuan_rm/read_grid_detail_rm/'+id}).trigger("reloadGrid");
}

 /* end detail pengajuan */
</script>
<!-- ###################### isi konten ###################### -->           
<div id="tabs1" style="margin-left:5px; min-height:450px;">
  <? if(isset($fperiode)){ echo $fperiode; } ?>
  <br/><br/>
  <table id="list_pengajuan_rm" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
  </table>
  <div id="pager_pengajuan_rm" class="scroll"></div>

  <div id="btnGroupInputRM" style="padding-top:10px;">  
  <input type="button" class="ui-state-default ui-corner-all" style="height:28px; padding:2px;" id="pRMtambah" value="Tambah Data" onclick="OpenFormRM('post')" />
  <input type="button" class="ui-state-default ui-corner-all" style="height:28px; padding:2px;" id="pRMcomplete" value="Ubah Data" onclick="OpenFormRM('get')" />
  <input type="button" class="ui-state-default ui-corner-all" style="height:28px; padding:2px;" id="pRMbatal" value="Batalkan Pengajuan" onclick="deleteRM()" />
  </div>

</div>
<!-- ###################### end konten ###################### -->

<div id="frmInputRM">  
<table width="95%" border=1>
<tr>
    <td width="150">No. Pengajuan Rawat</td>
    <td width="20">:</td>
    <td>
    	<input type="text" style="width:100px;" class="input" disabled="disabled" id="i_no_RM" name="i_no_RM" />
        <div id="reloader" style="padding-left:-25px; padding-top:1px;">
            <img id="loadbuttonRM" src="<?= $template_path ?>themes/base/images/Reloader.png" onclick="getNoPengajuan()" />
            <img id="loaderRM" src="<?= $template_path ?>themes/base/images/loading.gif" height="15" width="15"/>
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
  <td><input tabindex="3" type="text" style="width:100px;" class="input" id="i_tgl_RM" />
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
  <td><input tabindex="3" type="text" style="width:340px;" class="input" id="i_ket_infras" /></td>
</tr>

<tr>
	<td colspan="3">
    	<div style="padding:10px">
        	<table id="list_pengajuan_rm_detail" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
          	</table>
          	<div id="pager_pengajuan_rm_detail" class="scroll"></div>	
        </div>
    </td>
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
    <input type="hidden" id="form_mode" name="form_detail_mode"></td>
</tr>

<tr>
  	<td colspan="3"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
</tr>
<tr>
  	<td colspan="3">
   		
    </td>
</tr>
<tr>
  <td colspan="3">
  		
  </td>
</tr>
<tr>
   	<td colspan="3" style="padding-right:10px; padding-top:10px;" align="right">
      <input type="button" class="ui-state-default ui-corner-all" id="pAddPengajuanRM" value="Simpan Pengajuan" style="height:28px;padding:2px;cursor:pointer;"/>
      <input type="button" class="ui-state-default ui-corner-all" id="pClosePengajuanRM" value="Batal" style="height:28px;padding:2px;cursor:pointer;"/>
    </td>
</tr>
</table>
</div>


<div id="inputRMDetail">
<table width="95%" border=1>
<tr>
    <td width="150">No. Pengajuan Rawat</td>
    <td width="20">:</td>
    <td>
    	<input type="text" style="width:100px;" class="input" disabled="disabled" id="RMdetail_i_no_rm" name="detail_i_no_rm" />
        
        &nbsp; <span style="font-size:9px; color:#F00">* </span>
    </td>
</tr>

<tr>
  <td>Kode Aktivitas</td>
  <td>:</td>
  <td>
  <input tabindex="3" type="text" style="width:120px;" class="input" id="RMdetail_i_aktivitas" onkeypress="getActRM()" />
    </td>
</tr>

<tr>
  <td>Deskripsi Aktivitas</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:220px;" class="input" id="RMdetail_ket_aktivitas" /></td>
</tr>

<tr>
  <td>Qty</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:140px;" class="input" id="RMdetail_i_qty" /></td>
</tr>

<tr>
  <td>Unit Satuan</td>
  <td>:</td>
  <td><? if(isset($RMSatuan)){ echo $RMSatuan; } ?></td>
</tr>

<tr>
  <td>Rupiah / Satuan</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:120px;" class="input" id="RMdetail_i_qsat" /></td>
</tr>

<tr>
  <td>Total ( Rp. )</td>
  <td>:</td>
  <td><input tabindex="3" type="text" style="width:120px;" class="input" id="RMdetail_i_ttl" disabled="disabled" /></td>
</tr>

<tr>
  	<td colspan="3"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
</tr>
<tr>
  	<td colspan="3" align="right">
    		<button class="ui-state-default ui-corner-all" id="pAddPengajuanRMDetail" type="button" 
                          style="height:28px;padding:2px;cursor:pointer;">
                    Simpan
                </button>
                <button class="ui-state-default ui-corner-all" id="pClosePengajuanRMDetail" type="button" 
                          style="height:28px;padding:2px;cursor:pointer;">
                    Batal
                </button>
   	</td>
</tr>
</table>

</div>