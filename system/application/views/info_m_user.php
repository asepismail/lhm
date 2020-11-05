<? $template_path = base_url().$this->config->item('template_path'); ?> 
<script type="text/javascript">
    $(function() {  $( "#tabs" ).tabs();  });
	jQuery(document).ready(function(){
		$(".positive").format({precision: 2,allow_negative:false,autofix:true});
		/* dialog progress */	
		$("#progressbar").dialog({
					bgiframe: true, autoOpen: false,
					resizable: true, draggable: true,
					closeOnEscape:false, height: 160,
					width: 220, modal: true
		}); 
		
		$("#gangcode").dialog({
					bgiframe: true, autoOpen: false,
					resizable: true, draggable: true,
					closeOnEscape:false, height: 500,
					width: 700, modal: true
		}); 
	});
	
	//--------------------------------------------------------- url global js
	var url = "<?= base_url().'index.php/' ?>";
	var level = "<?= $user_level; ?>";
	var gridimgpath = "<?= $template_path ?>themes/base/images"; 
	$('#company_access').hide();
		
	// ----------------------------------------------------------------grid
	var jGrid = null;
    var colNamesT = new Array();
    var colModelT = new Array();
    colNamesT.push('No');
    colModelT.push({name:'no',index:'no', editable: false, width: 100, hidden : true, align:'center'});
    colNamesT.push('ID User');
    colModelT.push({name:'LOGINID',index:'LOGINID', editable: false, width: 100, align:'center'});
    colNamesT.push('Nama User');
    colModelT.push({name:'USER_FULLNAME',index:'USER_FULLNAME', editable: false, width: 150, align:'left'});
    colNamesT.push('Password');
    colModelT.push({name:'USER_PASS',index:'USER_PASS', editable: false, hidden : true, width: 180, align:'center'});
	colNamesT.push('Email');
    colModelT.push({name:'USER_MAIL',index:'USER_MAIL', editable: false, width: 150, align:'left'});
    colNamesT.push('Group Pengguna');
    colModelT.push({name:'USER_LEVEL',index:'USER_LEVEL', editable: false, hidden:true, width: 80, align:'center'});
    colNamesT.push('Group');
    colModelT.push({name:'USER_GROUP_NAME',index:'USER_GROUP_NAME', editable: false, width: 200, align:'center'});
    colNamesT.push('Departemen');
    colModelT.push({name:'USER_DEPT',index:'USER_DEPT', editable: false, hidden: true, width: 80, align:'center'});
	colNamesT.push('Departemen');
    colModelT.push({name:'DEPT_DESCRIPTION',index:'DEPT_DESCRIPTION', editable: false, width: 150, align:'center'});
    colNamesT.push('Inaktif');
    colModelT.push({name:'INACTIVE',index:'INACTIVE', editable: false, width: 80, edittype:'checkbox', 
				editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : true}, align:'center'});
	colNamesT.push('Company Code');
    colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, editable: false, width: 100, align:'left'});
      	
    var loadView = function(){
       jGrid = jQuery("#list_user").jqGrid({
           url:url+'m_user/read_grid_user',
           mtype : "POST", datatype: "json", colNames: colNamesT, colModel: colModelT ,
           rowNum:20, height: 350, rowList:[10,20,30], imgpath: gridimgpath, rownumbers:true,
           pager: jQuery('#pager_user'), sortname: colModelT[2].name, viewrecords: true,
           caption:"LHM User Management",
		   onSelectRow: function(){
				var id = jQuery("#list_user").getGridParam('selrow');
				var data = $("#list_user").getRowData(id) ;
				$("#tmpnik").val(''); 
				$("#company").val('');
				$("#duser").val(''); 
				$("#duser").val(data.LOGINID);   
				$("#tmpnik").val(data.LOGINID);  				
				$("#company").val(data.COMPANY_CODE);  
				
				var tmpNik =data.LOGINID; 
				var co = data.COMPANY_CODE;
				jQuery("#company_role_list").setGridParam({url:url+"m_user/read_grid_co_role/"+tmpNik}).trigger("reloadGrid");	
				jQuery("#company_role_gc_list").setGridParam({url:url+"m_user/read_grid_gc_role/"+tmpNik+"/"+co}).trigger("reloadGrid");	
			},
			loadComplete: function(){ }
        });
        jGrid.navGrid('#pager_user',{edit:false,del:false,add:false, search: true, refresh: true});
		
		//----------------------------------------------------------------button add di grid		
		jGrid.navButtonAdd('#pager_user',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		if(level != 'SAD'){
						alert("Akses ini hanya dibatasi kepada sistem administrator")
					} else {
			   			$("#form_mode").val('POST');
	    				$('#dialog_user').dialog('open');
					}
			   }, position:"last"
		});
		//---------------------------------------------------------------- button edit di grid
		jGrid.navButtonAdd('#pager_user',{
			   caption:"Edit", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   	  if(level != 'SAD'){
						alert("Akses ini hanya dibatasi kepada sistem administrator")
				  } else {
			   		var id = jQuery("#list_user").getGridParam('selrow');
					$("#form_mode").val('GET');
					if (id){ 
						var ret = jQuery("#list_user").getRowData(id); 
						$('#dialog_user').dialog('open');
						$("#LOGINID").val(ret.LOGINID); 
						$("#USER_FULLNAME").val(ret.USER_FULLNAME);
		                $("#USER_PASS").val(ret.USER_PASS);
						$("#USER_MAIL").val(ret.USER_MAIL);
						$("#USER_LEVEL").val(ret.USER_LEVEL);
						$("#USER_DEPT").val(ret.USER_DEPT);
						$("#COMPANY_CODE").val(ret.COMPANY_CODE);
					} else {
						alert("Silakan pilih baris yang akan diubah");
					} 				
				  }
			   }, position:"last"
		});
		//----------------------------------------------------------------button delete di grid
		jGrid.navButtonAdd('#pager_user',{
			   caption:"Delete", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){
				   if(level != 'SAD'){
						alert("Akses ini hanya dibatasi kepada sistem administrator")
				   } else {
						var id = jQuery("#list_user").getGridParam('selrow');
						if (id)	{
							var ret = jQuery("#list_user").getRowData(id);
							var id_login = ret.LOGINID;
							hapus(id_login);		 
						}
						gridReload();
				   }
			   }, position:"last"
		});						
        $("#alertmod").remove();//FIXME
    }
    jQuery("#list_user").ready(loadView);

	//---------------------------------------------------------------- fungsi refresh grid
	function gridReload(){
		//var nm_mask = jQuery("#item_nm").val();
		//var cd_mask = jQuery("#search_cd").val();
		jQuery("#list_user").setGridParam({url:url+"m_user/read_json_format"}).trigger("reloadGrid");
	}
	
	//---------------------------------------------------------------- button submit data
	jQuery("#submitdata").click(function (){
		var mode = $("#form_mode").val();
		if (mode == "GET"){
			var login_id = $("#LOGINID").val()
			update(login_id);
		} else if (mode == "POST"){
			Create();
		}
	});
	
	//---------------------------------------------------------------- init form
	function Init1() {  //nilai awal form
        $("#list_user").trigger("reloadGrid"); 
        $("#LOGINID").val("");
        $("#USER_FULLNAME").val("");
		$("#USER_PASS").val("");
		$("#USER_MAIL").val("");
		$("#USER_LEVEL").val("");
		$("#USER_DEPT").val("");
		$("#COMPANY_CODE").val("");
		i = 0;
	}
		
		//---------------------------------------------------------------- fungsi Add
			
		function Create() {
			var postdata = {}; 
					  
		  	// Data dari form
			postdata['LOGINID'] = $("#LOGINID").val() ;
			postdata['USER_FULLNAME'] = $("#USER_FULLNAME").val(); 
			postdata['USER_PASS'] = $("#USER_PASS").val() ; 
			postdata['USER_MAIL'] = $("#USER_MAIL").val();
			postdata['USER_LEVEL'] = $("#USER_LEVEL").val(); 
			postdata['USER_DEPT'] = $("#USER_DEPT").val();
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val();
				 
		   	// Post it all 
			$.post( url+'M_user/create', postdata,function(message,status) { 
				if(status !== 'success') { 
					alert(message); 
				  } else { 
				Init1(); //nilai awal form
				var i=1;
					}; 
			  }); 
		};
		  
		  
		//---------------------------------------------------------------- fungsi delete
		function hapus(id) {
			var postdata = {}; 
			
			$.post( url+'M_user/delete/'+id, postdata,function(message,status) { 
			if(status !== 'success') { 
				alert(message); 
			  } else { 
					Init1(); //nilai awal form
					var i=1;
			   }; 
		  	}); 
		}
		
		//---------------------------------------------------------------- fungsi Update
		function update(id){
			var postdata = {}; 					  
		  	// Data dari form
			//postdata['LOGINID'] = $("#LOGINID").val() ;
			postdata['USER_FULLNAME'] = $("#USER_FULLNAME").val(); 
			postdata['USER_PASS'] = $("#USER_PASS").val() ; 
			postdata['USER_MAIL'] = $("#USER_MAIL").val();
			postdata['USER_LEVEL'] = $("#USER_LEVEL").val(); 
			postdata['USER_DEPT'] = $("#USER_DEPT").val();
			postdata['COMPANY_CODE'] = $("#COMPANY_CODE").val();
			
		   // Post it all 
			$.post( url+'M_user/edit/'+id, postdata,function(message,status) { 
				if(status !== 'success') { 
					alert(message); 
				  } else { 
				Init1(); //nilai awal form
				var i=1;
					}; 
			  } ); 
		}
		
		//---------------------------------------------------------------- Modal Dialog
		$(function() {
			$("#dialog_user").dialog({
				bgiframe: true, autoOpen: false,
				height: 300, width: 450, modal: true,
				buttons: {
					'Tutup': function() {
									$(this).dialog('close');
									//gridReload();
									Init1();							
								},
					'Simpan' : function(){
							
						}
				} 
			}); 
		});
		//-- end dialog modal
		
	var jGrid_role = null;
    var colNamesT_role = new Array();
    var colModelT_role = new Array();
		
	colNamesT_role.push('Kode Perusahaan');
    colModelT_role.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, width: 120, align:'center'});
	colNamesT_role.push('Nama Perusahaan');
    colModelT_role.push({name:'COMPANY_NAME',index:'COMPANY_NAME', editable: false, width:250, align:'left'});
	colNamesT_role.push('Akses');
    colModelT_role.push({name:'com_access',index:'com_access', editable: true, width:50, 
				align:'center', edittype:"checkbox",formatter:"checkbox"});
		
	var loadView_role = function(){
    jGrid_role = jQuery("#company_role_list").jqGrid({
         url:url+'m_user/read_grid_co_role', mtype : "POST", datatype: "json", pager: jQuery('#pager_company_role'),
         colNames: colNamesT_role,colModel: colModelT_role,rowNum:20, height: 165,
         rowList:[10,20,30], imgpath: gridimgpath,forceFit : true, cellEdit: true,
         sortname: colModelT_role[0].name, viewrecords: true, caption:"Akses Perusahaan",
				cellurl: url+'m_user/create_company_access',
				beforeSubmitCell : function(rowid,celname,value,iRow,iCol) {
					var id = jQuery("#company_role_list").getGridParam('selrow');
					 if (id)	{
						var ret = jQuery("#company_role_list").getRowData(id);
						return {COMPANY_CODE:ret.COMPANY_CODE};
					}
			}, onSelectRow: function(rowid){
					jQuery('#company').resetSelection();
       	    }
		});
        jGrid_role.navGrid('#pager_company_role',{edit:false,del:false,add:false, search: false, refresh: true});			
						
        $("#alertmod").remove();//FIXME
    }
    jQuery("#company_role_list").ready(loadView_role);
		
	/* AKSES KEMANDORAN */
	var jGrid_role_gc = null;
    var colNamesT_role_gc = new Array();
    var colModelT_role_gc = new Array();
	
	colNamesT_role_gc.push('no');
    colModelT_role_gc.push({name:'no',index:'no', editable: false, hidden:true, width: 100, align:'center'});	
	colNamesT_role_gc.push('Loginid');
    colModelT_role_gc.push({name:'LOGINID',index:'LOGINID', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_role_gc.push('Kode Kemandoran');
    colModelT_role_gc.push({name:'DETAIL_CODE',index:'DETAIL_CODE', editable: false, width:120, align:'center'});
	colNamesT_role_gc.push('Deskripsi Kemandoran');
    colModelT_role_gc.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, width:250, align:'left'});
	colNamesT_role_gc.push('Perusahaan');
    colModelT_role_gc.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, width:150, align:'center'});
		
	var loadView_role_gc = function(){
    jGrid_role_gc = jQuery("#company_role_gc_list").jqGrid({
         url:url+'m_user/read_grid_gc_role', mtype : "POST", datatype: "json", pager: jQuery('#pager_company_role_gc'),
         colNames: colNamesT_role_gc,colModel: colModelT_role_gc,rowNum:20, height: 165,
         rowList:[10,20,30], imgpath: gridimgpath,forceFit : true, cellEdit: true,
         sortname: colModelT_role_gc[2].name, viewrecords: true, caption:"Akses Kemandoran",
				cellurl: url+'m_user/create_company_access',
				beforeSubmitCell : function(rowid,celname,value,iRow,iCol) {
					var id = jQuery("#company_role_gc_list").getGridParam('selrow');
					 if (id)	{
						var ret = jQuery("#company_role_gc_list").getRowData(id);
						return {COMPANY_CODE:ret.COMPANY_CODE};
					}
			}, onSelectRow: function(rowid){
					jQuery('#company').resetSelection();
       	    }
		});
		jGrid_role_gc.navGrid('#pager_company_role_gc',{edit:false,del:false,add:false, search: false, refresh: true});			
		//----------------------------------------------------------------button add di grid		
		jGrid_role_gc.navButtonAdd('#pager_company_role_gc',{
			   caption:"tambah", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		$('#gangcode').dialog('open');
			   }, position:"last"
		});
		jGrid_role_gc.navButtonAdd('#pager_company_role_gc',{
			   caption:"hapus", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		var postdata = {}; 
					var id = jQuery("#company_role_gc_list").getGridParam('selrow');
						if (id)	{
							var ret = jQuery("#company_role_gc_list").getRowData(id);
							postdata['LOGINID'] = ret.LOGINID;
							postdata['DETAIL_CODE'] = ret.DETAIL_CODE;
							postdata['COMPANY_CODE'] = ret.COMPANY_CODE;
							$.post( url+'m_user/delete_gc_role/', postdata,function(message) { 
							}); 		 
						}
						var tmpNik = $("#tmpnik").val(); 
						var co = $("#company").val(); 
		jQuery("#company_role_gc_list").setGridParam({url:url+"m_user/read_grid_gc_role/"+tmpNik+"/"+co}).trigger("reloadGrid");
			   }, position:"last"
		});
        $("#alertmod").remove();//FIXME
    }
    jQuery("#company_role_gc_list").ready(loadView_role_gc);
	
	
	/* grid gangcode */
	
	/*search*/
    var timeoutHnd; 
    var flAuto = false; 
    
    function doSearch(ev){ 
        if(timeoutHnd) 
            clearTimeout(timeoutHnd) 
            timeoutHnd = setTimeout(gridgReload,500) 
    } 
    
    function gridgReload(){ 
        var gc = jQuery("#search_gc").val(); 
        if (gc == ""){ gc = "-"; } 
        jQuery("#list_gang").setGridParam({url:url+"m_gang/search_gang/"+gc}).trigger("reloadGrid");        
    } 
	
	var jGrid_gang = null;
    var colNamesT_gang = new Array();
    var colModelT_gang = new Array();    
                                                                         
	colNamesT_gang.push('Kemandoran');
	colModelT_gang.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 90, align:'center'});
	colNamesT_gang.push('Nama Kemandoran');
	colModelT_gang.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:false, width: 340, align:'left'});
	colNamesT_gang.push('GANG_TYPE');
	colModelT_gang.push({name:'GANG_TYPE',index:'GANG_TYPE', hidden:true});
	colNamesT_gang.push('MANDORE1_CODE');
	colModelT_gang.push({name:'MANDORE1_CODE',index:'MANDORE1_CODE', hidden:true});
	colNamesT_gang.push('Mandor');
	colModelT_gang.push({name:'MANDORE_CODE',index:'MANDORE_CODE', editable: false, hidden:false, width: 70, align:'center'});
	colNamesT_gang.push('Nama Mandor');
	colModelT_gang.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 140, align:'left'});
 	colNamesT_gang.push('Kerani');
	colModelT_gang.push({name:'KERANI_CODE',index:'KERANI_CODE', hidden:true});    
	colNamesT_gang.push('Departemen');
	colModelT_gang.push({name:'DEPARTEMEN_CODE',index:'DEPARTEMEN_CODE', hidden:true});    
	colNamesT_gang.push('Divisi');

	colModelT_gang.push({name:'DIVISION_CODE',index:'DIVISION_CODE', hidden:true});
	colNamesT_gang.push('FUNCTION_CODE');
	colModelT_gang.push({name:'FUNCTION_CODE',index:'FUNCTION_CODE', hidden:true});    
	colNamesT_gang.push('GA_CODE');
	colModelT_gang.push({name:'GA_CODE',index:'GA_CODE', hidden:true});
	colNamesT_gang.push('COMPANY_CODE');
	colModelT_gang.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true});
	colNamesT_gang.push('');
	colModelT_gang.push({name:'action',index:'action', hidden:true});    

    var loadView_gang = function() {
    var gc = jQuery("#search_gc").val(); 
        
    if (gc == ""){ gc = "-"; }
    jGrid_gang = jQuery("#list_gang").jqGrid({
         url:url+'m_gang/search_gang/'+gc, mtype : "POST", datatype: "json",
         colNames: colNamesT_gang, colModel: colModelT_gang, rownumbers:true,
         viewrecords: true, multiselect: false, 
         caption: "Data Kemandoran <?php echo $company_dest;?>", 
         rowNum:20, rowList:[10,20,30], multiple:true, height: 300,
         cellEdit: false, pager: jQuery('#pager_gang'), sortname: 'GANG_CODE',
		 ondblClickRow: function(){
			 	var user = $("#duser").val(); 
				var id = jQuery("#list_gang").getGridParam('selrow');
				var postdata = {}; 
				var message = "";
				if (id)	{
						var ret = jQuery("#list_gang").getRowData(id);
							postdata['LOGINID'] = user;
							postdata['GANG_CODE'] = ret.GANG_CODE;
							postdata['COMPANY_CODE'] = ret.COMPANY_CODE;
							$.post( url+'m_user/insert_gc_role/', postdata,function(message) { 
								if(message!=""){
									alert(message);
								}
							}); 		 
						}
						var tmpNik = $("#duser").val(); 
						var co = $("#company").val(); 
		jQuery("#company_role_gc_list").setGridParam({url:url+"m_user/read_grid_gc_role/"+tmpNik+"/"+co}).trigger("reloadGrid");
			}
     });
     jGrid_gang.navGrid('#pager_gang',{edit:false,add:false,del:false, search: true, refresh: true});
	}
        
    jQuery("#list_gang").ready(loadView_gang);
	/* end gangcode*/
    </script>

<br/>

<!-- START TABS -->
<input type="hidden" id="tmpnik" />
<input type="hidden" id="company" />
<div id="tabs" style="width:90%;margin-top:25px">
	<ul>
		<li><a href="#tabs-1">User</a></li>
		<li><a href="#tabs-2">Akses User</a></li>
	</ul>
	<div id="tabs-1">
		<p> 
        	<div id="user_grid">
            <table id="list_user" class="scroll" cellpadding="0" cellspacing="0"></table>
            <div id="pager_user" class="scroll" style="text-align:center;"></div>
            </div>
    	</p>
	</div>
	<div id="tabs-2">
		<p>           
            <div id="usrname"></div>	
                
                <table id="company_role_gc_list" class="scroll" cellpadding="0" cellspacing="0">
                </table>
                <div id="pager_company_role_gc" class="scroll" style="text-align:center;"></div>
                
                <br/>
                <table id="company_role_list" class="scroll" cellpadding="0" cellspacing="0">
                </table>
                <div id="pager_company_role" class="scroll" style="text-align:center;"></div>
                
            </div>
        </p>
	</div>
</div>

<div id="gangcode">
<table id="cari_kemandoran" border="0" class="teks_" cellpadding="2" cellspacing="4">
<tr><td colspan="8">Cari Kemandoran :</td></tr>
<tr><td>User</td><td>:</td><td><input type="text" class="input" id="duser" disabled="disabled"/></td><td style="padding-left:15px;"></td></tr>
<tr><td>Kode Kemandoran</td><td>:</td><td><input type="text" class="input" id="search_gc" onkeydown="doSearch(arguments[0]||event)" /></td><td style="padding-left:15px;"></td></tr>
</table>
<table id="list_gang" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_gang" class="scroll"></div>
</div>
<!-- END TABS -->

        


<form id="form_user" name="form_user">
<div id="dialog_user">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
<tr><td>ID Pengguna</td><td>:</td><td><input class="input" type="text" size=25 id="LOGINID" /></td></tr>
<tr><td>Nama Pengguna</td><td>:</td><td><input class="input"  type="text" size=25 id="USER_FULLNAME" /></td></tr>
<tr><td>Password</td><td>:</td><td><input class="input"  type="password" size=25 id="USER_PASS" /></td></tr>
<tr><td>Password Lama</td><td>:</td><td><input class="input"  type="opassword" size=25 id="USER_PASS" /></td></tr>
<tr><td>Password Baru</td><td>:</td><td><input class="input"  type="npassword" size=25 id="USER_PASS" /></td></tr>
<tr><td>Konfirmasi Password Baru</td><td>:</td><td><input class="input"  type="knpassword" size=25 id="USER_PASS" /></td></tr>
<tr><td>Email</td><td>:</td><td><input class="input"  type="text" size=25 id="USER_MAIL" /></td></tr>
<tr><td>Level</td><td>:</td><td><?php echo $LEVEL;?></td></tr>
<tr><td>Departemen</td><td>:</td><td><?php echo $USER_DEPT; ?></td></tr>
<tr><td>Company</td><td>:</td><td><?php echo $COMPANY_CODE; ?></td></tr>
</table>

<input type="hidden" id="form_mode">

</div>
</form>

</body>