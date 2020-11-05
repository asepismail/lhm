<? 
    $template_path = base_url().$this->config->item('template_path');  
	$session = $this->session->userdata('LOGINID');
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/system/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';
var company = "<?=$company_code ?>";
 $(function() {  $( "#tabs" ).tabs();  });
jQuery(document).ready(function(){
	$("#inputGroup").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 200, width: 320, modal: true,buttons: { 
			'Tutup	': function(){
				$("#inputGroup").dialog("close");
				initFormGroup();
			}, 'Simpan Group': function(){
				submitGroup("simpan");
			}, 'Ubah Group': function(){
				submitGroup("ubah");
			}, 'Hapus Group': function(){
				submitGroup("hapus");
			}
		}
	}); 
	
	$("#inputGroupRole").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 200, width: 400, modal: true,buttons: { 
			'Tutup	': function(){
				$("#inputGroupRole").dialog("close");
				initFormRole();
			}, 'Simpan Role': function(){
				submitRole("", "simpan")
			}
		}
	}); 
	
	$("#inputGroupExport").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 200, width: 400, modal: true,buttons: { 
			'Tutup	': function(){
				$("#inputGroupExport").dialog("close");
				initFormExport();
			}, 'Simpan Export': function(){
				submitExport("", "simpan")
			}
		}
	}); 
	
	$("#gridMenu").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 550, width: 450, modal: true,buttons: { 
			'Tutup	': function(){
				$("#gridMenu").dialog("close");
				jQuery("#list_Menu").setGridParam({url:url+'syst_c_menu/search_menu/'}).trigger("reloadGrid");
			}
		}
	}); 
	
	$("#gridExport").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 550, width: 450, modal: true,buttons: { 
			'Tutup	': function(){
				$("#gridExport").dialog("close");
				jQuery("#list_Export").setGridParam({url:url+'syst_c_usergroup/search_export_group/'}).trigger("reloadGrid");
			}
		}
	});
});
/* Grid User Group*/
var jGrid_uGroup = null;
var colNamesT_uGroup = new Array();
var colModelT_uGroup = new Array();
	
colNamesT_uGroup.push('ID');
colModelT_uGroup.push({name:'USER_GROUP_TID',index:'USER_GROUP_TID', hidden:false, width: 80, align:'center'});
	
colNamesT_uGroup.push('Kode Group User');
colModelT_uGroup.push({name:'USER_GROUP_ID',index:'USER_GROUP_ID', editable: false, hidden:false, 
					  width: 200, align:'left'});
	
colNamesT_uGroup.push('Deskripsi Group');
colModelT_uGroup.push({name:'USER_GROUP_NAME',index:'USER_GROUP_NAME', hidden:false, width: 180, align:'center'});

colNamesT_uGroup.push('Action');
colModelT_uGroup.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_uGroup = function(){
jGrid_uGroup = jQuery("#list_uGroup").jqGrid({
     url:url+'syst_c_usergroup/search_usergroup/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_uGroup , colModel: colModelT_uGroup ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     rowNum:20, height: 350, rowList:[10,20,30], imgpath: gridimgpath, rownumbers:true,
     pager: jQuery('#pager_uGroup'), sortname: colModelT_uGroup[2].name, viewrecords: true,
     caption:"LHM User Management",
	 onSelectRow: function(){
		var id = jQuery("#list_uGroup").getGridParam('selrow');
		var data = $("#list_uGroup").getRowData(id) ;
		var idgroup = data.USER_GROUP_ID; 
		$("#menulist_id").val(data.USER_GROUP_ID);
		jQuery("#list_uGRole").setGridParam({url:url+"syst_c_usergroup/search_ugRole/"+idgroup}).trigger("reloadGrid");	
		jQuery("#list_uGExport").setGridParam({url:url+"syst_c_usergroup/search_ugExport/"+idgroup}).trigger("reloadGrid");
	 }, loadComplete: function(){ 
        	var ids = jQuery("#list_uGroup").getDataIDs(); 
        	for(var i=0;i<ids.length;i++) { 
             	var cl = ids[i];
             	ce = "<a href='#' onclick=\"viewGroup('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
             	jQuery("#list_uGroup").setRowData(ids[i],{act:ce}) 
         	}
     }, imgpath: gridimgpath, pager: jQuery('#pager_uGroup'), sortname: colModelT_uGroup[0].name
  });
  jGrid_uGroup.navGrid('#pager_uGroup',{edit:false,add:false,del:false, search: true, refresh: true});
  jGrid_uGroup.navButtonAdd('#pager_uGroup',{
	caption:"Tambah Group",  buttonicon:"ui-icon-add", 
	onClickButton: function(){ 
		initFormGroup();
		$("#inputGroup").dialog("open");
		$("#i_groupid").attr('disabled','');
		$(":button:contains('Ubah Group')").hide();
		$(":button:contains('Hapus Group')").hide();
		$(":button:contains('Simpan Group')").show();
	}, position:"left",
  });
}
jQuery("#list_uGroup").ready(loadView_uGroup);

function submitGroup(method){
	var postdata={};
		postdata['USER_GROUP_ID'] = $("#i_groupid").val() ;
		postdata['USER_GROUP_NAME'] = $("#i_groupname").val() ; 
		
		var urls = ""
		if(method == "simpan"){
			urls = "syst_c_usergroup/insertDataGroup/";
		} else if(method == "ubah"){
			urls = "syst_c_usergroup/updateDataGroup/"+$("#i_groupid").val();
		} else if(method == "hapus"){
			urls = "syst_c_usergroup/deleteDataGroup/"+$("#i_groupid").val();
		}
		
		$.post( url+urls, postdata, function(status) {
			var status = new String(status);
			if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
			} else { 
			   jQuery("#list_uGroup").setGridParam({url:url+'syst_c_usergroup/search_usergroup/'}).trigger("reloadGrid");
				   if(method == "simpan"){
						alert('data berhasil tersimpan.');
				   } else if(method == "ubah"){
						alert('data berhasil terupdate.');
				   } else if(method == "hapus"){
						alert('data berhasil terhapus.');
				   }
				   initFormGroup();        
				    $("#inputGroup").dialog("close");
			   };  
		});
}

function viewGroup(ids){
	var ids = jQuery("#list_uGroup").getGridParam('selrow'); 
	var data = $("#list_uGroup").getRowData(ids) ; 
	if (ids!=null ){
		initFormGroup();
		$("#inputGroup").dialog("open");
		$(":button:contains('Ubah Group')").show();
		$(":button:contains('Hapus Group')").show();
		$(":button:contains('Simpan Group')").hide();
		$("#i_groupid").attr('disabled','true');
		$("#i_groupid").val(data.USER_GROUP_ID);
		$("#i_groupname").val(data.USER_GROUP_NAME);
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function initFormGroup(){
	 $("#i_groupid").val("") ; $("#i_groupname").val("") ;
}

/* Grid User Group Export*/
var jGrid_uGExport = null;
var colNamesT_uGExport = new Array();
var colModelT_uGExport = new Array();
	
colNamesT_uGExport.push('ID');
colModelT_uGExport.push({name:'AEID',index:'AEID', hidden:false, width: 60, align:'center'});
	
colNamesT_uGExport.push('Kode Group');
colModelT_uGExport.push({name:'GROLE',index:'GROLE', editable: false, hidden:false, 
					  width: 80, align:'center'});
	
colNamesT_uGExport.push('Deskripsi Group');
colModelT_uGExport.push({name:'USER_GROUP_NAME',index:'USER_GROUP_NAME', hidden:false, width: 160, align:'center'});
	
colNamesT_uGExport.push('Akses Menu');
colModelT_uGExport.push({name:'EXPORT_MENU',index:'EXPORT_MENU', hidden:false, width: 180, align:'left'});

colNamesT_uGExport.push('Action');
colModelT_uGExport.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_uGExport = function(){
jGrid_uGExport = jQuery("#list_uGExport").jqGrid({
     url:url+'syst_c_usergroup/search_ugExport/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_uGExport , colModel: colModelT_uGExport ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "User Group List", rowNum:20, rowList:[10,20,30], multiple:true,
     height: 300, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 
                var ids = jQuery("#list_uGExport").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"submitExport('"+cl+"', 'hapus');\"/ style='cursor:pointer'>hapus</a>";
                    jQuery("#list_uGExport").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_uGExport'), sortname: colModelT_uGExport[0].name
	  });
	  jGrid_uGExport.navGrid('#pager_uGExport',{edit:false,add:false,del:false, search: false, refresh: true});
	  jGrid_uGExport.navButtonAdd('#pager_uGExport',{
	   caption:"Tambah Akses Export",  buttonicon:"ui-icon-add", 
	   onClickButton: function(){ 
	   		AddExportGroup();
	   },
	   position:"left",
	});
}
jQuery("#list_uGExport").ready(loadView_uGExport);

/* Grid User Group Role*/
var jGrid_uGRole = null;
var colNamesT_uGRole = new Array();
var colModelT_uGRole = new Array();
	
colNamesT_uGRole.push('ID');
colModelT_uGRole.push({name:'MENU_LIST',index:'MENU_LIST', hidden:false, width: 60, align:'center'});
	
colNamesT_uGRole.push('Kode Group');
colModelT_uGRole.push({name:'USER_GROUP_ID',index:'USER_GROUP_ID', editable: false, hidden:false, 
					  width: 80, align:'center'});
	
colNamesT_uGRole.push('Deskripsi Group');
colModelT_uGRole.push({name:'USER_GROUP_NAME',index:'USER_GROUP_NAME', hidden:false, width: 160, align:'center'});

colNamesT_uGRole.push('Kode Menu');
colModelT_uGRole.push({name:'MENU_ID',index:'MENU_ID', editable: false, hidden:true, 
					  width: 200, align:'left'});
	
colNamesT_uGRole.push('Akses Menu');
colModelT_uGRole.push({name:'MENU_NAME',index:'MENU_NAME', hidden:false, width: 180, align:'left'});

colNamesT_uGRole.push('Action');
colModelT_uGRole.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_uGRole = function(){
jGrid_uGRole = jQuery("#list_uGRole").jqGrid({
     url:url+'syst_c_usergroup/search_ugRole/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_uGRole , colModel: colModelT_uGRole ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "User Group List", rowNum:20, rowList:[10,20,30], multiple:true,
     height: 300, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 
                var ids = jQuery("#list_uGRole").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"submitRole('"+cl+"', 'hapus');\"/ style='cursor:pointer'>hapus</a>";
                    jQuery("#list_uGRole").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_uGRole'), sortname: colModelT_uGRole[0].name
	  });
	  jGrid_uGRole.navGrid('#pager_uGRole',{edit:false,add:false,del:false, search: false, refresh: true});
	  jGrid_uGRole.navButtonAdd('#pager_uGRole',{
	   caption:"Tambah Akses Menu",  buttonicon:"ui-icon-add", 
	   onClickButton: function(){ 
	   		AddRoleGroup();
	   },
	   position:"left",
	});
}
jQuery("#list_uGRole").ready(loadView_uGRole);

function AddRoleGroup(){
	var ids = jQuery("#list_uGroup").getGridParam('selrow'); 
	var data = $("#list_uGroup").getRowData(ids) ; 
	if (ids!=null ){
		initFormRole();
		$("#inputGroupRole").dialog("open");
		$("#i_rolegroupid").val(data.USER_GROUP_ID);
		$("#i_rolegroupname").val(data.USER_GROUP_NAME);
	} else {
		alert("harap pilih data untuk diberikan akses");
	}  
}

function AddExportGroup(){
	var ids = jQuery("#list_uGroup").getGridParam('selrow'); 
	var data = $("#list_uGroup").getRowData(ids) ; 
	if (ids!=null ){
		initFormExport();
		$("#inputGroupExport").dialog("open");
		$("#i_exportgroupid").val(data.USER_GROUP_ID);
		$("#i_exportgroupname").val(data.USER_GROUP_NAME);
	} else {
		alert("harap pilih data untuk diberikan akses");
	}  
}

function submitExport(id, method){
	var postdata={};
	postdata['USER_GROUP_ID'] = $("#i_exportgroupid").val() ;
	postdata['MENU_ID'] = $("#i_exportgroupmenuid").val() ; 
		
	var urls = ""
	if(method == "simpan"){
		urls = "syst_c_usergroup/insertDataExport/";
	} else if(method == "hapus"){
		urls = "syst_c_usergroup/deleteDataExport/"+id;
	}
	$.post( url+urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			alert(status); 
		} else { 
			jQuery("#list_uGExport").setGridParam({url:url+'syst_c_usergroup/search_ugExport/'+$("#menulist_id").val()}).trigger("reloadGrid");
			//if(method == "simpan"){
			//	alert('data berhasil tersimpan.');
			//} 
			//initFormRole();        
			//$("#inputGroupRole").dialog("close");
		}  
	});
}

function submitRole(id, method){
	var postdata={};
	postdata['USER_GROUP_ID'] = $("#i_rolegroupid").val() ;
	postdata['MENU_ID'] = $("#i_rolegroupmenuid").val() ; 
		
	var urls = ""
	if(method == "simpan"){
		urls = "syst_c_usergroup/insertDataRole/";
	} else if(method == "hapus"){
		urls = "syst_c_usergroup/deleteDataRole/"+id;
	}
	$.post( url+urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			alert(status); 
		} else { 
			jQuery("#list_uGRole").setGridParam({url:url+'syst_c_usergroup/search_ugRole/'+$("#menulist_id").val()}).trigger("reloadGrid");
			//if(method == "simpan"){
			//	alert('data berhasil tersimpan.');
			//} 
			//initFormRole();        
			//$("#inputGroupRole").dialog("close");
		}  
	});
}

function initFormRole(){
	 $("#i_rolegroupid").val("") ; $("#i_rolegroupname").val("") ;
	 $("#i_rolegroupmenuid").val("") ; $("#i_rolegroupmenu").val("") ;
}

function initFormExport(){
	 $("#i_exportgroupid").val("") ; $("#i_exportgroupname").val("") ;
	 $("#i_exportgroupmenuid").val("") ; $("#i_exportgroupmenu").val("") ;
}

/* get grid menu */
/* Grid Export*/
var jGrid_Export = null;
var colNamesT_Export = new Array();
var colModelT_Export = new Array();
	
colNamesT_Export.push('ID');
colModelT_Export.push({name:'MENU_ID',index:'MENU_ID', hidden:false, width: 80, align:'center'});
	
colNamesT_Export.push('Deskripsi Menu');
colModelT_Export.push({name:'MENU_NAME',index:'MENU_NAME', editable: false, hidden:false, width: 300, align:'left'});
	
colNamesT_Export.push('Action');
colModelT_Export.push({name:'act',index:'act', editable: false, hidden:true, width: 50, align:'center'});
	
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_Export = function(){
jGrid_Export = jQuery("#list_Export").jqGrid({
//     url:url+'syst_c_usergroup/search_export_group/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_Export , colModel: colModelT_Export ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Menu List", 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 350, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	 ondblClickRow: function(){
			 	var id = jQuery("#list_Export").getGridParam('selrow');
				if (id)	{
						var ret = jQuery("#list_Export").getRowData(id);
						jQuery("#i_exportgroupmenuid").val(ret.MENU_ID)
						jQuery("#i_exportgroupmenu").val(ret.MENU_NAME)
						$("#gridExport").dialog("close");
				}
	 },
     loadComplete: function(){ 
                var ids = jQuery("#list_Export").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"view('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_Export").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_Export'), sortname: colModelT_Export[0].name
	  });
	  jGrid_Export.navGrid('#pager_Export',{edit:false,add:false,del:false, search: true, refresh: true});
    }
jQuery("#list_Export").ready(loadView_Export);
/* Grid Menu*/
var jGrid_Menu = null;
var colNamesT_Menu = new Array();
var colModelT_Menu = new Array();
	
colNamesT_Menu.push('ID');
colModelT_Menu.push({name:'MENU_ID',index:'MENU_ID', hidden:false, width: 80, align:'center'});
	
colNamesT_Menu.push('Deskripsi Menu');
colModelT_Menu.push({name:'MENU_NAME',index:'MENU_NAME', editable: false, hidden:false, width: 200, align:'left'});
	
colNamesT_Menu.push('Is Parent');
colModelT_Menu.push({name:'MENU_PARENT',index:'MENU_PARENT', hidden:false, 
			editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", 
			formatoptions: {disabled : true}, edittype: "text", width: 70, align:'center'});
	
colNamesT_Menu.push('Menu URL');
colModelT_Menu.push({name:'MENU_URL',index:'MENU_URL', hidden:true, width: 180, align:'center'});
	
colNamesT_Menu.push('Left');
colModelT_Menu.push({name:'LFT',index:'LFT', editable: false, hidden:true, width: 60, align:'center'});
	
colNamesT_Menu.push('Right');
colModelT_Menu.push({name:'RGT',index:'RGT', editable: false, hidden:true, width: 60, align:'center'});
	
colNamesT_Menu.push('Action');
colModelT_Menu.push({name:'act',index:'act', editable: false, hidden:true, width: 50, align:'center'});
	
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_Menu = function(){
jGrid_Menu = jQuery("#list_Menu").jqGrid({
     url:url+'syst_c_menu/search_menu/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_Menu , colModel: colModelT_Menu ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Menu List", 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 350, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
	 ondblClickRow: function(){
			 	var id = jQuery("#list_Menu").getGridParam('selrow');
				if (id)	{
						var ret = jQuery("#list_Menu").getRowData(id);
						jQuery("#i_rolegroupmenuid").val(ret.MENU_ID)
						jQuery("#i_rolegroupmenu").val(ret.MENU_NAME)
						$("#gridMenu").dialog("close");
				}
	 },
     loadComplete: function(){ 
                var ids = jQuery("#list_Menu").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"view('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_Menu").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_Menu'), sortname: colModelT_Menu[0].name
	  });
	  jGrid_Menu.navGrid('#pager_Menu',{edit:false,add:false,del:false, search: true, refresh: true});
    }
jQuery("#list_Menu").ready(loadView_Menu);

function getMenu(){
	$("#gridMenu").dialog("open");
	jQuery("#list_Menu").setGridParam({url:url+'syst_c_menu/search_menu/'}).trigger("reloadGrid");
}
function getExport(){
	$("#gridExport").dialog("open");
	jQuery("#list_Export").setGridParam({url:url+'syst_c_usergroup/search_export_group/'}).trigger("reloadGrid");
}
/* end get grid menu */

</script>

<div style="padding-top:35px">

    <div id="tabs" style="width:90%;margin-top:25px">
        <ul>
            <li><a href="#tabs-1">User Group</a></li>
            <li><a href="#tabs-2">User Group Access Menu</a></li>
            <li><a href="#tabs-3">User Group Export Menu</a></li>
        </ul>
        <div id="tabs-1">
            <p> 
                <div id="uGroup">
                   <table id="list_uGroup" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_uGroup" class="scroll"></div>
                </div>
            </p>
        </div>
        <div id="tabs-2">
            <p>           
                <div id="ugRole">
                   <table id="list_uGRole" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_uGRole" class="scroll"></div> 
                </div>
            </p>
        </div>
        <div id="tabs-3">
            <p>           
                <div id="ugExport">
                   <table id="list_uGExport" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_uGExport" class="scroll"></div> 
                </div>
            </p>
        </div>
    </div>

</div>

<div id="inputGroup">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Kode Group Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="i_groupid" /></td></tr>
<tr><td>Nama Group</td><td>:</td><td><input class="input"  type="text" size=25 id="i_groupname" /></td></tr>
</table>
</div>

<div id="inputGroupExport"> 
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Kode Group Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="i_exportgroupid" 
		disabled="disabled" /></td></tr>
<tr><td>Deskripsi Group Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="i_exportgroupname" 
		disabled="disabled"  /></td></tr>
<tr><td>Kode Export Menu</td><td>:</td><td><input class="input"  type="text" size=25 id="i_exportgroupmenuid" />
<img id="loadbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer; margin-bottom:-6px;" onclick="getExport()" />
</td></tr>        
<tr><td>Deskripsi Export</td><td>:</td><td><input class="input"  type="text" size=25 id="i_exportgroupmenu" />
<input type="hidden" id="menulist_id"></td></tr>
</table>
</div>

<div id="inputGroupRole"> 
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Kode Group Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="i_rolegroupid" 
		disabled="disabled" /></td></tr>
<tr><td>Deskripsi Group Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="i_rolegroupname" 
		disabled="disabled"  /></td></tr>
<tr><td>Kode Akses Menu</td><td>:</td><td><input class="input"  type="text" size=25 id="i_rolegroupmenuid" />
<img id="loadbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer; margin-bottom:-6px;" onclick="getMenu()" />
</td></tr>        
<tr><td>Deskripsi Menu</td><td>:</td><td><input class="input"  type="text" size=25 id="i_rolegroupmenu" />
<input type="hidden" id="menulist_id"></td></tr>
</table>
</div>

<div id="gridMenu">
	<table id="list_Menu" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
	<div id="pager_Menu" class="scroll"></div>
</div>

<div id="gridExport">
	<table id="list_Export" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
	<div id="pager_Export" class="scroll"></div>
</div>