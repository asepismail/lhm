<? $template_path = base_url().$this->config->item('template_path'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript">
//###################### OTHER FORM FUNCTION #######################
var timeoutHnd;  

function stringFunction(str)
{
    this.str=str;
    this.strToLower = function (){
         return (str+'').toLowerCase();
    }
    this.trim = function(){
        return str.replace(/^\s+|\s+$/g,'');
    }
    this.regExpIs_valid = function(){
        var pattern= new RegExp(/^[a-z0-9A-Z-\s]+$/);
        return pattern.test(str);
    }
}
           
function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement;      
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

</script>

<script type="text/javascript">
//###################### GRID FORM FUNCTION ####################### 
var url="<?= base_url().'index.php/pms/main_c_pms/' ?>";
var urls="loadData/";
var gridNames = "jsGrid";
//var gridimgpath = '<?= $template_path ?>themes/basic/images'; //definisi imagepath pada jgrid

jQuery('document').ready(function()
{
	/* dialog progress */	
	$("#progressbar").dialog({
			bgiframe: true, autoOpen: false,
			resizable: true, draggable: true,
			closeOnEscape:false, height: 160,
			width: 220, modal: true
	}); 
	/* end dialog */
	
	/* dialog detail aktivitas */	
	$("#fdetailproject").dialog({
			bgiframe: true, autoOpen: false,
			resizable: true, draggable: true, title: 'Detail Aktivitas Project',
			closeOnEscape:false, height: 'auto',
			width: 'auto', modal: true, position: 'center'
	}); 
	/* end dialog */
	
	/* dialog form detail aktivitas */	
	$("#frmaktivitas").dialog({
			bgiframe: true, autoOpen: false,
			resizable: true, draggable: true, title: 'Tambah Detail Aktivitas Project',
			closeOnEscape:false, height: 'auto',
			width: 'auto', modal: true, position: 'center',
			buttons: {         
			Tutup: function()  {	init_prj_dtl(); $("#frmaktivitas").dialog('close'); },
			Simpan: function() {	submit_detail();	}  
        } 
	}); 
	/* end dialog */
	
    //------------- definisi colModel dan colNames -------------    
    var grid_pts = null;
    var colNames = new Array(); //definisi colNames untuk jGrid
    var colModel = new Array(); //definisi colModel untuk jGrid
    
    colNames.push('id');
    colModel.push({name:'ID',index:'ID', 
    editable: true,hidden:true, width: 30, align:'center'});

    colNames.push('Kode Project');
    colModel.push({name:'PROJECT_ID',index:'PROJECT_ID', 
    editable: true,hidden:false, width: 40, align:'center'});
            
    colNames.push('Afd');
    colModel.push({name:'AFD',index:'AFD',
    editable: true,hidden:false, width: 20, align:'center'});
            
    colNames.push('Tipe');
    colModel.push({name:'PROJECT_TYPE',index:'PROJECT_TYPE', 
    editable: true,hidden:false, width: 20, align:'center'});

    colNames.push('Subtipe');
    colModel.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE', 
    editable: true,hidden:true, width:30, align:'left'});
	
	colNames.push('Subtipe');
    colModel.push({name:'PROJECT_SUB_ACTIVITY',index:'PROJECT_SUB_ACTIVITY', 
    editable: true,hidden:true, width:30, align:'left'});

    colNames.push('Deskripsi');
    colModel.push({name:'PROJECT_DESC',index:'PROJECT_DESC', 
    editable: true,hidden:false, width:150, align:'left'});
	
	colNames.push('Lokasi');
    colModel.push({name:'PROJECT_LOCATION',index:'PROJECT_LOCATION', 
    editable: true,hidden:false, width:60, align:'center'});

    colNames.push('Pelaksana');
    colModel.push({name:'KODE_PELAKSANA',index:'KODE_PELAKSANA', 
    editable: true,hidden:true, width: 35, align:'center'});
	
	colNames.push('Aktivitas');
    colModel.push({name:'PROJECT_ACTIVITY',index:'PROJECT_ACTIVITY', 
    editable: true,hidden:true, width: 35, align:'center'});
	
    colNames.push('PK');
    colModel.push({name:'SPK',index:'SPK', 
    editable: true,hidden:false, width: 50, align:'center'});

    colNames.push('Tgl Mulai');
    colModel.push({name:'PROJECT_START',index:'PROJECT_START', 
    editable: true,hidden:true, width: 50, align:'center'});
	
	colNames.push('Tgl Selesai');
    colModel.push({name:'PROJECT_END',index:'PROJECT_END', 
    editable: true,hidden:true, width: 50, align:'center'});
    
    colNames.push('Qty');
    colModel.push({name:'PROJECT_QTY',index:'PROJECT_QTY', 
    editable: true,hidden:true, width: 30, align:'center'});

    colNames.push('Satuan');
    colModel.push({name:'PROJECT_UOM',index:'PROJECT_UOM', 
    editable: true,hidden:true, width: 50, align:'center'});

    colNames.push('Nilai/Satuan');
    colModel.push({name:'PROJECT_VALUE',index:'PROJECT_VALUE', 
    editable: true,hidden:true, width: 50, align:'center'});
	
	colNames.push('PPN');
    colModel.push({name:'PROJECT_PPN',index:'PROJECT_PPN', 
    editable: true,hidden:true, width: 50, align:'center'});

	colNames.push('Nett');
    colModel.push({name:'PROJECT_NETTVAL',index:'PROJECT_NETTVAL', 
    editable: true,hidden:true, width: 50, align:'center'});
	
	colNames.push('Aktif');
    colModel.push({name:'PROJECT_STATUS',index:'PROJECT_STATUS', 
    			hidden:false, editable: false, edittype:'checkbox', editoptions: { value:"1:0"},
  				formatter: "checkbox", formatoptions: {disabled : true}, width: 20, align:'center'});
	
	colNames.push('Tgl Terbit');
    colModel.push({name:'TGL_TERBIT',index:'TGL_TERBIT', 
    editable: true,hidden:false, width: 40, align:'center'});
	
	colNames.push('COMPANY_CODE');
    colModel.push({name:'COMPANY_CODE',index:'COMPANY_CODE', 
    editable: true,hidden:true, width: 50, align:'center'});
	
	colNames.push('Action');
	colModel.push({name:'action', index:'action', align:'center', 
			resizable:false, sortable:false, editable: false,hidden:false, width: 30})
        
    var loadView_pb = function()
    {
        jgrid_pb = jQuery("#list").jqGrid({
            url:url+urls,  //loaddata untuk jGrid ->dari controller ->ke model
            datatype: 'json',  mtype: 'POST', colNames:colNames, colModel:colModel,
            pager: jQuery('#GridPager'),  rownumbers: true, rowNum: 20, width:900, 
            height: 300, sortorder: "asc", forceFit : true, rowList:[10,20,30], 
            multiple:false, 
            caption: 'Daftar Project', editurl:url+urls,
			loadComplete: function(){ 
                  var ids = jQuery("#list").getDataIDs(); 
                  for(var i=0;i<ids.length;i++){ 
                      var cl = ids[i];
                      be = "<a href='#' onclick=\"DetailProject('"+cl+"');\"/ style='cursor:pointer'>Aktivitas</a>";
				 	  jQuery("#list").setRowData(ids[i],{action:be}) 
                     }
                  }, sortname: colModel[1].name,  sortorder: "desc",  viewrecords: true    
        });
        jgrid_pb.navGrid('#GridPager',{edit:false,del:false,add:false, search: false, refresh: true});
        
        jgrid_pb.navButtonAdd('#GridPager',{
           caption:"Export ke Excell", 
           buttonicon:"ui-icon-add", 
           onClickButton: function(){ window.location = url+"pjtoexcell/";},
           position:"left",
        });
        $("#alertmod").remove();//FIXME         
    }
    jQuery("#list").ready(loadView_pb);
	
	 //------------- detail project -------------    
   var grid_dp = null;
   var colNamesDp = new Array(); //definisi colNames untuk jGrid
   var colModelDp = new Array(); //definisi colModel untuk jGrid
   
   colNamesDp.push('no');
   colModelDp.push({name:'no',index:'no', editable: false,hidden:true, width: 35, align:'center'});

   colNamesDp.push('Kode Project');
   colModelDp.push({name:'MASTER_PROJECT_ID',index:'MASTER_PROJECT_ID', editable: false,hidden:false, width: 35, align:'center'});

   colNamesDp.push('Kode Aktivitas');
   colModelDp.push({name:'PROJECT_ACTIVITY',index:'PROJECT_ACTIVITY', editable: true,hidden:false, width: 40, align:'center'});
            
   colNamesDp.push('Keterangan Aktivitas');
   colModelDp.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true,hidden:false, width: 100, align:'left'});
            
   colNamesDp.push('Company Code');
   colModelDp.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true,hidden:true, width: 20, align:'center'});

   var loadView_Dp = function()
    {
        jgrid_Dp = jQuery("#listDp").jqGrid({
            url:url+"LoadDetail/",  //loaddata untuk jGrid ->dari controller ->ke model
            datatype: 'json',  mtype: 'POST', colNames:colNamesDp, colModel:colModelDp,
            pager: jQuery('#GridPagerDp'),  rownumbers: true, rowNum: 20, width:600, 
            height: 160, sortorder: "asc", forceFit : true, rowList:[10,20,30], 
            multiple:false, editurl:url+urls,
			sortname: colModelDp[1].name,  sortorder: "desc",  viewrecords: true    
        });
        jgrid_Dp.navGrid('#GridPagerDp',{edit:false,del:false,add:false, search: false, refresh: true});
        jgrid_pb.navButtonAdd('#GridPagerDp',{
           caption:"Tambah", 
           buttonicon:"ui-icon-add", 
           onClickButton: function(){ TambahDataDetail(); },
           position:"left",
        });
		jgrid_pb.navButtonAdd('#GridPagerDp',{
           caption:"Hapus", 
           buttonicon:"ui-icon-add", 
           onClickButton: function(){ delDataDetail();},
           position:"left",
        });
        $("#alertmod").remove();//FIXME         
    }
    jQuery("#listDp").ready(loadView_Dp);
	
    $("#i_start").datepicker({dateFormat:"yy-mm-dd"});
    $("#i_end").datepicker({dateFormat:"yy-mm-dd"}); 
	$("#i_terbit").datepicker({dateFormat:"yy-mm-dd"});
});

function init_prj()
{
    $("#i_prjid").val(""); $("#i_afd").val("");  $("#i_prjtype").val(""); 
    $("#i_prjsubtype").val(""); $("#i_prjsubact").val(""); $("#i_prjdesc").val("");
	$("#i_loc").val(""); $("#i_act").val(""); $("#i_prjtypepelaksana").val("");
	$("#i_pk").val(""); $("#i_start").val(""); $("#i_end").val(""); $("#i_qty").val(""); 
	$("#i_uom").val("");  $("#i_val").val(""); $("#i_ppn").val("");  $("#i_nett").val(""); 
	$("#i_terbit").val(""); $("#i_active").val(""); $("#form_mode").val(""); $("#prj_form").dialog('close');  
}

function init_prj_dtl()
{
	 //$("#projectnum").val("");
	 $("#form_mode_dtl").val("");
	 $("#i_actdet").val("");
	 //$("#i_prjiddet").val("");
	 $("#i_actdesc").val("");
	 //$("#frmaktivitas").dialog('close');  
}
</script>

<script type="text/javascript">
//############################ START BUTTON FUNCTION ################################# 
jQuery(document).ready(function(){
   $("#prj_form").dialog({
        bgiframe: true, autoOpen: false, height: 545, width: 500, modal: true, 
		title: "Project", resizable: false, moveable: true,
        buttons: {         
			Tutup: function()  {	init_prj();  },
			Simpan: function() {	submit();	}  
        } 
    });  
});

$(function () { 
	$("#i_actdet").autocomplete( url+"getactivity/", {  dataType: 'ajax',
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
			$("#i_actdesc").val(item.res_name );
	 });
});

/* close progress bar */
function closewin(){
	$("#progressbar").dialog('close');
}
/* end close progress bar */

function DetailProject(project){
	$("#projectnum").val('');
	jQuery("#listDp").setGridParam({url:url+"LoadDetail/"+project}).trigger("reloadGrid");  
	$("#fdetailproject").dialog('open');	
	$("#projectnum").val(project);
}

//############################ END BUTTON FUNCTION ################################   
</script>

<script type="text/javascript">
//########################### START SEARCH FUNCTION #############################    
var timeoutHnd;
function gridReload(){ 
    jQuery("#list").setGridParam({url:url+urls}).trigger("reloadGrid");        
} 

function gridReloadDetail(){ 
	jQuery("#listDp").setGridParam({url:url+"LoadDetail/"+$("#projectnum").val()}).trigger("reloadGrid");
}

function doSearch(ev) { 
    if(timeoutHnd) 
    clearTimeout(timeoutHnd) 
    timeoutHnd = setTimeout(srcReload,500) 
}
 
function srcReload() { 
    var id = $("#search_id").val();
    var afd = $("#search_afd").val(); 
    var type = $("#search_prjtype").val(); 
    var desc = $("#search_prjdesc").val(); 

    if (id == ""){  id = "-"; }
    if (afd == ""){ afd = "-"; } 
    if (type == ""){ type = "-"; } 
    if (desc == ""){ desc = "-"; }

    jQuery("#list").setGridParam
    ({url:url+"SearchData/"+id+"/"+afd+"/"+type+"/"+desc}).trigger("reloadGrid");        
}
//########################### END SEARCH FUNCTION #############################    
</script>
<div style="margin-top:-15px;"><strong>Daftar Project</strong></div>

<body>
<?
	if($company_code=="PAG"){
?>
<div id="fcompany">
<span>Filter berdasarkan Perusahaan : <? if(isset($company)) { echo $company; } ?></span>
</div>
<br/>
<?
	}
?>
<div id"gridSearch">
    <div class="teks_"></div>  
    <div>
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr>
                <tr><td colspan="8" style="padding-bottom:8px;">Pencarian Berdasarkan :</td></tr>
                <td>No Project </td>
                <td>:</td>
                <td width="18%">
                <input type="text" class="input" id="search_id" maxlength="25" style="width:100px" onkeydown="doSearch(arguments[0]||event)" />
                </td>
                <td>Afd</td>
                <td>:</td>
                <td width="20%">
               <!--  <input type="text" class="input" id="search_afd" maxlength="5" onkeydown="doSearch(arguments[0]||event)" /> -->
               			<? if(isset($safd)){ echo $safd; } ?>
                </td>
                <td>Type</td>
                <td>:</td>
                <td width="20%">
                	 <select name='search_prjtype' class='select' id="search_prjtype" onchange="doSearch(arguments[0]||event)" tabindex="6" style="width:130px">
                        <option value=""> -- pilih -- </option>
                          <option value="IF">Infrastuktur</option>
                          <option value="OP">Oil Palm (OP)</option>
                          <option value="NS">Persiapan Bibitan</option>
                          <option value="PB">Pabrik Kelapa Sawit</option>
                    </select>
                </td>
                <td>Deskripsi</td>
                <td>:</td>
                <td>
                <input type="text" class="input" id="search_prjdesc" maxlength="25" onkeydown="doSearch(arguments[0]||event)" />
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table>
    <div id="GridPager" class="scroll" style="text-align:center;" align="center"></div>
</div>

<div id="fdetailproject" style=" margin-right: auto; width: 100%;">
    <table id="listDp" class="scroll"></table>
    <div id="GridPagerDp" class="scroll" style="text-align:center;" align="center"></div>
</div>
<br>
<div id="save" class="scroll" style="float:left;">
</div>

<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 

</body>
</html>