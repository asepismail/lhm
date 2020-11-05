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
	$("#dialog_user").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 330, title: "Input Data User", width: 420, modal: true,buttons: { 
			'Tutup	': function(){
				$("#dialog_user").dialog("close");
				init_inputuser();
			}, 'Simpan User': function(){
				submitUser("simpan");
			}, 'Ubah User': function(){
				submitUser("ubah");
			}, 'Hapus User	': function(){
				submitUser("hapus");
			}
		}
	}); 
	
	$("#userco").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 180, title: "Akses Perusahaan", width: 420, modal: true,buttons: { 
			'Tutup	': function(){
				$("#userco").dialog("close");
				init_userco();
			}, 'Simpan Akses Perusahaan': function(){
				submit_userco("","simpan");
			}
		}
	}); 
	
	$("#usermodule").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 180, title: "Akses Module", width: 420, modal: true,buttons: { 
			'Tutup	': function(){
				$("#usermodule").dialog("close");
				init_usermodule();
			}, 'Simpan Akses Module': function(){
				submit_usermodule("","simpan");
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
});


$(function(){
		$("#s_company").change(function() {
			var company = $("#s_company").val(); 
			jQuery("#list_User").setGridParam({url:url+'syst_c_user/search_user/'+company}).trigger("reloadGrid"); 
		});
	});
/* Grid User Group*/
var jGrid_User = null;
var colNamesT_User = new Array();
var colModelT_User = new Array();
					
colNamesT_User.push('ID');
colModelT_User.push({name:'no',index:'no', hidden:true, width: 80, align:'center'});
	
colNamesT_User.push('Nama Login');
colModelT_User.push({name:'LOGINID',index:'LOGINID', editable: false, hidden:false, width: 100, align:'left'});
	
colNamesT_User.push('Nama Lengkap');
colModelT_User.push({name:'USER_FULLNAME',index:'USER_FULLNAME', hidden:false, width: 180, align:'left'});

colNamesT_User.push('Password');
colModelT_User.push({name:'USER_PASS',index:'USER_PASS', editable: false, hidden:true, width: 150, align:'left'});
	
colNamesT_User.push('Email');
colModelT_User.push({name:'USER_MAIL',index:'USER_MAIL', hidden:false, width: 150, align:'center'});

colNamesT_User.push('Kode Dept');
colModelT_User.push({name:'USER_DEPT',index:'USER_DEPT', editable: false, hidden:true, width: 150, align:'left'});
	
colNamesT_User.push('Departemen');
colModelT_User.push({name:'DEPT_DESCRIPTION',index:'DEPT_DESCRIPTION', hidden:false, width: 140, align:'center'});

colNamesT_User.push('Kode Level');
colModelT_User.push({name:'USER_LEVEL',index:'USER_LEVEL', hidden:true, width: 120, align:'center'});

colNamesT_User.push('Level');
colModelT_User.push({name:'USER_GROUP_NAME',index:'USER_GROUP_NAME', editable: false, hidden:false, width: 140, align:'center'});

colNamesT_User.push('Inaktif');
colModelT_User.push({name:'INAKTIF',index:'INAKTIF', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 60, align:'center'});

colNamesT_User.push('Perusahaan');
colModelT_User.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, width: 80, align:'center'});

colNamesT_User.push('Action');
colModelT_User.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_User = function(){
jGrid_User = jQuery("#list_User").jqGrid({
     url:url+'syst_c_user/search_user/*',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_User , colModel: colModelT_User ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     rowNum:20, height: 350, rowList:[10,20,30], imgpath: gridimgpath, rownumbers:true,
     pager: jQuery('#pager_User'), sortname: colModelT_User[2].name, viewrecords: true,
     caption:"LHM User Management",
	 onSelectRow: function(){
		var id = jQuery("#list_User").getGridParam('selrow');
		var data = $("#list_User").getRowData(id) ;
		var iduser = data.LOGINID; 
		$("#u_login").val(data.LOGINID);
		$("#m_login").val(data.LOGINID);
		jQuery("#list_Company").setGridParam({url:url+"syst_c_user/search_user_co/"+iduser}).trigger("reloadGrid");	
		jQuery("#list_uMenu").setGridParam({url:url+"syst_c_user/search_user_menu/"+iduser}).trigger("reloadGrid");	
		jQuery("#list_Module").setGridParam({url:url+"syst_c_user/search_user_module/"+iduser}).trigger("reloadGrid");	
		
	 }, loadComplete: function(){ 
        	var ids = jQuery("#list_User").getDataIDs(); 
        	for(var i=0;i<ids.length;i++) { 
             	var cl = ids[i];
             	ce = "<a href='#' onclick=\"viewUser('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
             	jQuery("#list_User").setRowData(ids[i],{act:ce}) 
         	}
     }, imgpath: gridimgpath, pager: jQuery('#pager_User'), sortname: colModelT_User[1].name
  });
  jGrid_User.navGrid('#pager_User',{edit:false,add:false,del:false, search: true, refresh: true});
  jGrid_User.navButtonAdd('#pager_User',{
	caption:"Tambah User",  buttonicon:"ui-icon-add", 
	onClickButton: function(){ 
		init_inputuser();
		$("#dialog_user").dialog("open");
		$(":button:contains('Ubah User')").hide();
		$(":button:contains('Hapus User')").hide();
		$(":button:contains('Simpan User')").show();
	}, position:"left",
  });
}
jQuery("#list_User").ready(loadView_User);

function submitUser(method){
	var postdata={};
		postdata['LOGINID'] = $("#i_login").val() ;
		postdata['USER_FULLNAME'] = $("#i_user").val() ; 
		postdata['USER_PASS'] = $("#i_pass").val() ;
		postdata['USER_MAIL'] = $("#i_email").val() ; 
		postdata['USER_DEPT'] = $("#i_dept").val() ;
		postdata['USER_LEVEL'] = $("#i_group").val() ; 
		var inActive = $("#i_inaktif").is(':checked');
		if(inActive==true) {
			inActive=1;
		} else {
			inActive=0;
		}
		postdata['INACTIVE'] = inActive;
		postdata['COMPANY_CODE'] = $("#i_company").val() ; 
				
		var urls = ""
		if(method == "simpan"){
			urls = "syst_c_user/insertUser/";
		} else if(method == "ubah"){
			urls = "syst_c_user/updateUser/"+$("#i_login").val();
		} else if(method == "hapus"){
			urls = "syst_c_user/deleteUser/"+$("#i_login").val();
		}
		
		$.post( url+urls, postdata, function(status) {
			var status = new String(status);
			if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
			} else { 
			   var company = $("#s_company").val(); 
			   jQuery("#list_User").setGridParam({url:url+'syst_c_user/search_user/'+company}).trigger("reloadGrid");
				   if(method == "simpan"){
						alert('data berhasil tersimpan.');
				   } else if(method == "ubah"){
						alert('data berhasil terupdate.');
				   } else if(method == "hapus"){
						alert('data berhasil terhapus.');
				   }
				   init_inputuser();        
				    $("#dialog_user").dialog("close");
			   };  
		});
}

function viewUser(ids){
	var ids = jQuery("#list_User").getGridParam('selrow'); 
	var data = $("#list_User").getRowData(ids) ; 
	if (ids!=null ){
		init_inputuser();
		$("#dialog_user").dialog("open");
		$(":button:contains('Ubah User')").show();
		$(":button:contains('Hapus User')").show();
		$(":button:contains('Simpan User')").hide();
		$("#i_login").attr('disabled','true');
		$("#i_login").val(data.LOGINID);
		$("#i_user").val(data.USER_FULLNAME);
		$("#i_group").val(data.USER_LEVEL);
		$("#i_pass").val(data.USER_PASS);
		$("#i_email").val(data.USER_MAIL);
		$("#i_dept").val(data.USER_DEPT);
		$("#i_company").val(data.COMPANY_CODE);
		var inAktif = data.INAKTIF;
			if (inAktif==1) {
				$("#i_inaktif").attr('checked',true);
			} else {
				$("#i_inaktif").attr('checked',false);
			}
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function init_inputuser(){

	$("#i_login").val(""); $("#i_user").val("");
	$("#i_pass").val(""); 
	$("#i_email").val(""); $("#i_inaktif").attr('checked',false);  $("#i_dept").val("");
	$("#i_group").val(""); $("#i_company").val(""); 
}

/* Role untuk PT */
var jGrid_Company = null;
var colNamesT_Company = new Array();
var colModelT_Company = new Array();
	
colNamesT_Company.push('ID');
colModelT_Company.push({name:'ID',index:'ID', hidden:true, width: 80, align:'center'});
	
colNamesT_Company.push('Pengguna');
colModelT_Company.push({name:'USERID',index:'USERID', editable: false, hidden:false, width: 130, align:'left'});
	
colNamesT_Company.push('kode Perusahaan');
colModelT_Company.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, 
			editable: false, edittype: "text", width: 120, align:'center'});
	
colNamesT_Company.push('Nama Perusahaan');
colModelT_Company.push({name:'COMPANY_NAME',index:'COMPANY_NAME', hidden:false, width: 200, align:'center'});
	
colNamesT_Company.push('Action');
colModelT_Company.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
	
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_Company = function(){
jGrid_Company = jQuery("#list_Company").jqGrid({
     url:url+'syst_c_user/search_user_co/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_Company , colModel: colModelT_Company ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Daftar Akses Perusahaan", 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 150, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 
                var ids = jQuery("#list_Company").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"submit_userco('"+cl+"','hapus');\"/ style='cursor:pointer'>hapus</a>";
                    jQuery("#list_Company").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_Company'), sortname: colModelT_Company[1].name
	  });
	  jGrid_Company.navGrid('#pager_Company',{edit:false,add:false,del:false, search: true, refresh: true});
	  jGrid_Company.navButtonAdd('#pager_Company',{
		caption:"Tambah",  buttonicon:"ui-icon-add", 
		onClickButton: function(){ 
			$("#userco").dialog("open");
		}, position:"left",
	  });
    }
jQuery("#list_Company").ready(loadView_Company);

/* Role untuk Modul */
var jGrid_Module = null;
var colNamesT_Module = new Array();
var colModelT_Module = new Array();
	
colNamesT_Module.push('ID');
colModelT_Module.push({name:'ID',index:'ID', hidden:true, width: 80, align:'center'});
	
colNamesT_Module.push('PENGGUNA');
colModelT_Module.push({name:'USERID',index:'USERID', editable: false, hidden:false, width: 140, align:'left'});
	
colNamesT_Module.push('KODE MODUL');
colModelT_Module.push({name:'MODULE_ACCESS',index:'MODULE_ACCESS', hidden:false, 
			editable: false, edittype: "text", width: 130, align:'center'});
	
colNamesT_Module.push('NAMA MODUL');
colModelT_Module.push({name:'MODULE_NAME',index:'MODULE_NAME', hidden:false, width: 220, align:'center'});
	
colNamesT_Module.push('Action');
colModelT_Module.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
	
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_Module = function(){
jGrid_Module = jQuery("#list_Module").jqGrid({
     url:url+'syst_c_user/search_user_module/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_Module , colModel: colModelT_Module,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Daftar Akses Modul", 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 150, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 
                var ids = jQuery("#list_Module").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"submit_usermodule('"+cl+"','hapus');\"/ style='cursor:pointer'>hapus</a>";
                    jQuery("#list_Module").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_Module'), sortname: colModelT_Module[1].name
	  });
	  jGrid_Module.navGrid('#pager_Module',{edit:false,add:false,del:false, search: true, refresh: true});
	  jGrid_Module.navButtonAdd('#pager_Module',{
		caption:"Tambah Modul",  buttonicon:"ui-icon-add", 
		onClickButton: function(){
			$("#module_login").val( $("#u_login").val() );  
			$("#usermodule").dialog("open");
		}, position:"left",
	  });
    }
jQuery("#list_Module").ready(loadView_Module);

function submit_userco(uid, method){
	var postdata={};
	postdata['USERID'] = $("#u_login").val() ;
	postdata['COMPANY_CODE'] = $("#u_company").val() ; 	
	
	var urls = ""
	if(method == "simpan"){
		urls = "syst_c_user/insertUserCo/";
	} else if(method == "hapus"){
		urls = "syst_c_user/deleteUserCo/"+uid;
	}
		
	$.post( url+urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
		} else { 
			   var id = $("#u_login").val(); 
			   jQuery("#list_Company").setGridParam({url:url+'syst_c_user/search_user_co/'+id}).trigger("reloadGrid");
			   init_userco();        
			   $("#userco").dialog("close");
		};  
	});
}

function submit_usermodule(uid, method){
	var postdata={};
	postdata['LOGINID'] = $("#module_login").val() ;
	postdata['MODULE_ACCESS'] = $("#u_module").val() ; 	
	
	var urls = ""
	if(method == "simpan"){
		urls = "syst_c_user/insertUserModule/";
	} else if(method == "hapus"){
		urls = "syst_c_user/deleteUserModule/"+uid;
	}
		
	$.post( url+urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
		} else { 
			   var id = $("#u_login").val(); 
			   jQuery("#list_Module").setGridParam({url:url+'syst_c_user/search_user_Module/'+id}).trigger("reloadGrid");
			   init_usermodule();        
			   $("#userModule").dialog("close");
		};  
	});
}

function init_userco(){
	$("#u_company").val("");
}

function init_usermodule(){
	$("#u_module").val("");
}
/* end Role PT */



/* get grid menu */
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
		 var postdata={};
		 var id = jQuery("#list_Menu").getGridParam('selrow');
		 if (id){
			var ret = jQuery("#list_Menu").getRowData(id);
			postdata['LOGINID'] = $("#m_login").val() ;
			postdata['MENU_ID'] = ret.MENU_ID	
			$.post( url+'syst_c_user/insertUserMenu/', postdata, function(status) {
				var status = new String(status);
				if(status.replace(/\s/g,"") != "") { 
					alert(status); 
				} else { 
					var id = $("#m_login").val(); 
					jQuery("#list_uMenu").setGridParam({url:url+'syst_c_user/search_user_menu/'+id}).trigger("reloadGrid");
					$("#gridMenu").dialog("close");
				};  
			});
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

/* end get grid menu */

/* akses menu user */
/* Role untuk PT */
var jGrid_uMenu = null;
var colNamesT_uMenu = new Array();
var colModelT_uMenu = new Array();
	
colNamesT_uMenu.push('ID');
colModelT_uMenu.push({name:'MENU_LIST_ID',index:'MENU_LIST_ID', hidden:true, width: 80, align:'center'});
	
colNamesT_uMenu.push('Pengguna');
colModelT_uMenu.push({name:'LOGINID',index:'LOGINID', editable: false, hidden:false, width: 130, align:'left'});
	
colNamesT_uMenu.push('Kode Menu');
colModelT_uMenu.push({name:'MENU_ID',index:'MENU_ID', hidden:false, editable: false, edittype: "text", width: 120, align:'center'});
	
colNamesT_uMenu.push('Nama Menu');
colModelT_uMenu.push({name:'MENU_NAME',index:'MENU_NAME', hidden:false, width: 200, align:'center'});
	
colNamesT_uMenu.push('Action');
colModelT_uMenu.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
	
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_uMenu = function(){
jGrid_uMenu = jQuery("#list_uMenu").jqGrid({
     url:url+'syst_c_user/search_user_menu/',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_uMenu , colModel: colModelT_uMenu ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Daftar Akses Menu Khusus", 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 150, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 
                var ids = jQuery("#list_uMenu").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"delete_user_menu('"+cl+"');\"/ style='cursor:pointer'>hapus</a>";
                    jQuery("#list_uMenu").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_uMenu'), sortname: colModelT_uMenu[1].name
	  });
	  jGrid_uMenu.navGrid('#pager_uMenu',{edit:false,add:false,del:false, search: false, refresh: true});
	  jGrid_uMenu.navButtonAdd('#pager_uMenu',{
		caption:"Tambah Menu",  buttonicon:"ui-icon-add", 
		onClickButton: function(){ 
			$("#gridMenu").dialog("open");
		}, position:"left",
	  });
    }
jQuery("#list_uMenu").ready(loadView_uMenu);

function delete_user_menu(uid){
	$.post( url+"syst_c_user/deleteUserMenu/"+uid, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
		} else { 
			   var id = $("#m_login").val(); 
			   jQuery("#list_uMenu").setGridParam({url:url+'syst_c_user/search_user_menu/'+id}).trigger("reloadGrid");
		};  
	});
}
/* end akses menu user */
</script>

<div id="tabs" style="width:90%;margin-top:25px">
	<ul>
		<li><a href="#tabs-1">Pengguna</a></li>
		<li><a href="#tabs-2">Akses Pengguna</a></li>
        <li><a href="#tabs-3">Akses Modul</a></li>
	</ul>
	<div id="tabs-1">
		<p> 
        	<span> Filter berdasarkan Perusahaan : <?php echo $scompany; ?> <br/><br/> </span>
        	<div id="user_grid">
            <table id="list_User" class="scroll" cellpadding="0" cellspacing="0"></table>
            <div id="pager_User" class="scroll" style="text-align:center;"></div>
            </div>
    	</p>
	</div>
	<div id="tabs-2">
		<p>           
            <span> Akses Perusahaan </span> <br/> <br/>
            <div id="user_company">
            <table id="list_Company" class="scroll" cellpadding="0" cellspacing="0"></table>
            <div id="pager_Company" class="scroll" style="text-align:center;"></div>
            </div>
            <br/>
            <span> Akses Menu Khusus </span> <br/> <br/>
            <div id="user_uMenu">
            <table id="list_uMenu" class="scroll" cellpadding="0" cellspacing="0"></table>
            <div id="pager_uMenu" class="scroll" style="text-align:center;"></div>
            </div>
            
        </p>
	</div>
    <div id="tabs-3">
		<p> 
        	<span> Akses Modul </span> <br/> <br/>
        	<div id="modul_grid">
            <table id="list_Module" class="scroll" cellpadding="0" cellspacing="0"></table>
            <div id="pager_Module" class="scroll" style="text-align:center;"></div>
            </div>
    	</p>
	</div>
</div>

<div id="usermodule">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Nama Login</td><td>:</td><td><input class="input" type="text" id="module_login" disabled="disabled" style="width:100px" /> 
	<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Module</td><td>:</td><td><select  name='u_module' class='select' id='u_module' style='width:120px;' >
<option value='LHM'>LHM </option>
<option value='PRD'>PRODUKSI </option>
<option value='PMS'>PROJECT</option>
</select>
	<span style="font-size:9px; color:#F00">* </span></td></tr>
</table>
</div>

<div id="userco">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Nama Login</td><td>:</td><td><input class="input" type="text" id="u_login" disabled="disabled" style="width:100px" /> 
	<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Perusahaan</td><td>:</td><td><?php if(isset($ucompany)){ echo $ucompany; } ?>
	<span style="font-size:9px; color:#F00">* </span></td></tr>
</table>
</div>

<div id="dialog_user">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>Nama Login</td><td>:</td><td><input class="input" type="text" id="i_login" style="width:100px" /> 
	<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Nama Pengguna</td><td>:</td><td><input class="input"  type="text" id="i_user" style="width:180px" />
	<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Password</td><td>:</td><td><input class="input" type="password" id="i_pass" style="width:120px"  />
	<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Email</td><td>:</td><td><input class="input"  type="text" id="i_email" style="width:160px" /></td></tr>
<tr><td>Level Pengguna</td><td>:</td><td><?php echo $level;?> 
		<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Departemen</td><td>:</td><td><?php echo $dept; ?>
		<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Inaktif</td><td>:</td><td><input class="input" type="checkbox" id="i_inaktif" />
		<span style="font-size:9px; color:#F00">* </span></td></tr>
<tr><td>Perusahaan</td><td>:</td><td><?php echo $company; ?>
		<span style="font-size:9px; color:#F00">* </span></td></tr>
 <tr><td colspan="3"> <span style="font-size:9px; color:#F00">* tidak boleh kosong</span> </td></tr>         
</table>
<input type="hidden" id="form_mode">
</div>

<div id="gridMenu">
	<table cellpadding="0" border="0">
    <tr><td>Nama User</td><td>:</td><td><input type="text" disabled="disabled" id="m_login" /></td></tr>
    <tr><td colspan="3">
    	<table id="list_Menu" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
		<div id="pager_Menu" class="scroll"></div>
    </td></tr>
    </table>
	
</div>