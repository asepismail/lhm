<? 
    $template_path = base_url().$this->config->item('template_path');  
	$session = $this->session->userdata('LOGINID');
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/system/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';
var company = "<?=$company_code ?>";
$("#i_menuid").val("");
/*####### dialog ###### */
$(function() {
	$("#i_isparent").change(function() {
		var cek = $(this).is(":checked");
		if(cek == true){
			showRow();
		} else {
			hideRow();
		}
	});
	 
	$("#i_parent").change(function() {
		inisialisasi();
	});
	
	$("#form_input").dialog({
		bgiframe: false, autoOpen: false, height: 300, width: 450,
		modal: true, title: "Tambah Menu Baru", resizable: false,
		moveable: true, buttons: { 
			'Tutup	': function(){
				$("#form_input").dialog("close");
				initForm();
			},
			'Simpan': function(){
				submit_addMenu("simpan");
			},
			'Ubah': function(){
				submit_addMenu("ubah");
			},
			'Hapus': function(){
				submit_addMenu("hapus");
			}
		} 
	});
});

function hideRow(){
	var row2 = document.getElementById("prnt");
	row2.style.display = 'none';
}

function showRow(){
	var row2 = document.getElementById("prnt");
	row2.style.display = '';
}

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
colModelT_Menu.push({name:'MENU_URL',index:'MENU_URL', hidden:false, width: 180, align:'center'});
	
colNamesT_Menu.push('Left');
colModelT_Menu.push({name:'LFT',index:'LFT', editable: false, hidden:false, width: 60, align:'center'});
	
colNamesT_Menu.push('Right');
colModelT_Menu.push({name:'RGT',index:'RGT', editable: false, hidden:false, width: 60, align:'center'});
	
colNamesT_Menu.push('Action');
colModelT_Menu.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
	
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
     height: 300, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
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
	  jGrid_Menu.navButtonAdd('#pager_Menu',{
	   caption:"Tambah Menu",  buttonicon:"ui-icon-add", 
	   onClickButton: function(){ 
	   		$("#form_input").dialog("open");
			$(":button:contains('Ubah')").hide();
			$(":button:contains('Hapus')").hide();
			$(":button:contains('Simpan')").show();
	   },
	   position:"left",
	});
    }
jQuery("#list_Menu").ready(loadView_Menu);


/* #### fungsi generate */
function inisialisasi(){
	var postdata = {};
	$.post(url+'syst_c_menu/ext_genID/'+$("#i_parent").val(), postdata, function(data){
		$("#i_menuid").val(data);
		$.post(url+'syst_c_menu/getLeft/'+$("#i_parent").val(), postdata, function(data){
			$("#i_menuleft").val(data);
		});
		$.post(url+'syst_c_menu/getRight/'+$("#i_parent").val(), postdata, function(data){
			$("#i_menuright").val(data);
		});
	});
}

function submit_addMenu(method){
		var postdata={};
		postdata['MENU_ID'] = $("#i_menuid").val() ;
		postdata['MENU_NAME'] = $("#i_menudesc").val() ; 
		var isparent = $("#i_isparent").is(':checked');
		if(isparent==true) {
			isparent=1;
		} else {
			isparent=0;
		}
		postdata['MENU_PARENT'] = isparent ; 
		postdata['MENU_URL'] = $("#i_menuurl").val() ; 
		postdata['LFT'] = $("#i_menuleft").val() ;
		postdata['RGT'] = $("#i_menuright").val() ; 
		var urls = ""
		if(method == "simpan"){
			urls = "syst_c_menu/insertData/";
		} else if(method == "ubah"){
			urls = "syst_c_menu/updateData/"+$("#i_menuid").val();
		} else if(method == "hapus"){
			urls = "syst_c_menu/deleteData/"+$("#i_menuid").val();
		}
		
		$.post( url+urls, postdata, function(status) {
				var status = new String(status);
				if(status.replace(/\s/g,"") != "") { 
					alert(status); 
				} else { 
					jQuery("#list_Menu").setGridParam({url:url+'syst_c_menu/search_menu/'}).trigger("reloadGrid");
					if(method == "simpan"){
						alert('data berhasil tersimpan.');
					} else if(method == "ubah"){
						alert('data berhasil terupdate.');
					} else if(method == "hapus"){
						alert('data berhasil terhapus.');
					}
					initForm();        
					$("#form_input").dialog("close");
				};  
		});
}
	
function view(ids){
	var ids = jQuery("#list_Menu").getGridParam('selrow'); 
	var data = $("#list_Menu").getRowData(ids) ; 
	if (ids!=null ){
		initForm();
		$("#form_input").dialog("open");
		$(":button:contains('Ubah')").show();
		$(":button:contains('Hapus')").show();
		$(":button:contains('Simpan')").hide();
		$("#i_menuid").attr('disabled','true');
		$("#i_menuid").val(data.MENU_ID);
		$("#i_menudesc").val(data.MENU_NAME);
		$("#i_isparent").val(data.MENU_PARENT);
		$("#i_menuurl").val(data.MENU_URL);
		$("#i_menuleft").val(data.LFT);
		$("#i_menuright").val(data.RGT);
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function initForm(){
	 $("#i_menuid").val("") ; $("#i_menudesc").val("") ; $("#i_isparent").attr('checked',false); 
	 $("#i_menuurl").val("") ; $("#i_menuleft").val("") ;  $("#i_menuright").val("") ;
}
</script>

<div style="padding-top:35px">
<table id="list_Menu" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_Menu" class="scroll"></div>
</div>

<div id="form_input">
<table border="0" class="teks_">
  <tr>
    <td>Is Parent</td> 
    <td>:</td>
    <td><input tabindex="1" type="checkbox" id="i_isparent" class="input"/></td>
  </tr>
  <tr id="prnt" >
    <td width="167">Parent</td>
    <td width="4">:</td>
    <td width="415"><?php if(isset($parent)){ echo $parent; }?></td>
  </tr>
  <tr>
    <td width="167">Id Menu </td>
    <td width="4">:</td>
    <td width="415"><input tabindex="3" type="text" style="width:120px;" id="i_menuid" class="input"/>
    	<div id="search" style=" position:relative; margin-left:140px; margin-top:-16px;">
        	<img id="loadbutton" src="<?= $template_path ?>themes/base/images/reloader.png" style="cursor:pointer;" onclick="inisialisasi()" />
        </div>
       </td>
  </tr>
 <tr>
    <td>Deskripsi Menu</td>
    <td>:</td>
    <td><input tabindex="4" type="text" style="width:120px;" id="i_menudesc" class="input"/></td>
  </tr>
  <tr>
    <td>Menu URL</td>
    <td>:</td> 
    <td><input tabindex="5" type="text" style="width:120px;" id="i_menuurl" class="input"/></td>
  </tr>
  <tr>
    <td>Left</td>
    <td>:</td> 
    <td><input tabindex="6" type="text" style="width:120px;" id="i_menuleft" class="input" />
    </td>
  </tr>
  <tr>
    <td>Right</td> 
    <td>:</td>
    <td><input tabindex="7" type="text" style="width:120px;" id="i_menuright" class="input" />
        </div>
    </td>
  </tr>  
</table>
</div>