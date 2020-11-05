<? $template_path = base_url().$this->config->item('template_path'); ?>

<script type="text/javascript">
var grid_pts = null;
var colNamesT_pts = new Array(); //definisi colNames untuk jGrid
var colModelT_pts = new Array(); //definisi colModel untuk jGrid
var url="<?= base_url().'index.php/m_nursery/' ?>";
var urls="loadData/";

//------------- definisi colModel dan colNames -------------	
colNamesT_pts.push('id');
colModelT_pts.push({name:'BATCH_ID',index:'BATCH_ID', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('Kode Bibitan');
colModelT_pts.push({name:'NURSERYCODE',index:'NURSERYCODE', 
editable: true,hidden:false, width: 50, align:'center'});

colNamesT_pts.push('Deskripsi');
colModelT_pts.push({name:'DESCRIPTION',index:'DESCRIPTION', 
editable: true,hidden:false, width: 200, align:'left'});

colNamesT_pts.push('Periode Batch');
colModelT_pts.push({name:'DATEPLANTED',index:'DATEPLANTED', 
editable: true,hidden:false, width: 50, align:'center'});

colNamesT_pts.push('VARIETAS');
colModelT_pts.push({name:'VARIETAS',index:'VARIETAS', 
editable: true,hidden:false, width: 70, align:'left'});
		
colNamesT_pts.push('Qty Pesan');
colModelT_pts.push({name:'QTYORDERED',index:'QTYORDERED', 
editable: true,hidden:true, width: 40, align:'right'});

colNamesT_pts.push('Qty Persediaan');
colModelT_pts.push({name:'QTYONHAND',index:'QTYONHAND', 
editable: true,hidden:false, width: 60, align:'right'});

colNamesT_pts.push('Qty Onhold');
colModelT_pts.push({name:'QTYONHOLD',index:'QTYONHOLD', 
editable: true,hidden:true, width: 60, align:'right'});

colNamesT_pts.push('Inaktif');
colModelT_pts.push({name:'INACTIVE',index:'INACTIVE', 
				editable: false, edittype:'checkbox', editoptions: { value:"1:0"},
  				formatter: "checkbox", formatoptions: {disabled : true}, width: 30, align:'center'});

colNamesT_pts.push('Tgl Inaktif');
colModelT_pts.push({name:'INACTIVE_DATE',index:'INACTIVE_DATE', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('company');
colModelT_pts.push({name:'COMPANY_CODE',index:'COMPANY_CODE', 
editable: true,hidden:true, width: 50, align:'center'});
//------------- end definisi colModel dan colNames -------------	
	
	var loadView_pb = function()
        {
            jgrid_pb = jQuery("#list").jqGrid(
            {
				url:url+urls,  //loaddata untuk jGrid ->dari controller ->ke model
				datatype: 'json',  mtype: 'POST', colNames:colNamesT_pts,
				colModel:colModelT_pts, pager: jQuery('#pager'), 
				rownumbers: true, rowNum: 20, width:800, height:300,
				sortorder: "asc", forceFit : true, rowList:[10,20,30], 
				sortname: colModelT_pts[1].name, 
				sortorder: "desc", 
				viewrecords: true,  caption: 'Daftar Kode Bibitan',
				editurl: url + "SearchData"	
            });
            jgrid_pb.navGrid('#pageri',{edit:false,del:false,add:false, search: false, refresh: true});
			jgrid_pb.navButtonAdd('#pager',{
			   caption:"Export ke Excell", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
			   		window.location = url + "create_excel/";
			   }, position:"left",
			});
            $("#alertmod").remove();//FIXME		 
        }
        jQuery("#list").ready(loadView_pb);
		
		function init_nurs()
		{
			$("#i_nurs_code").val(""); $("#i_nurs_desc").val("");
			$("#i_date").val("");  $("#i_nurs_varietas").val("");
			$("#i_nurs_qtyonhand").val(""); 
			$("#nurs_form").dialog('close');
			$("#form_mode").val('');	
		}
		
		$(function() {
			$("#nurs_form").dialog({
				bgiframe: false, autoOpen: false, height: 270,
				width: 350, modal: true, title: "Tambah Batch Bibitan",
				resizable: false, moveable: true,
				buttons: {
					'Tutup	': function() {
						init_nurs();		
					},
					'Simpan  ':function(){
						submit();
					}
				} 
			}); 
			
			/* dialog progress */	
			$("#progressbar").dialog({
					bgiframe: true, autoOpen: false,
					resizable: true, draggable: true,
					closeOnEscape:false, height: 160,
					width: 220, modal: true
			}); 
			/* end dialog */
			
            $("#i_date").datepicker({dateFormat:"yy-mm-dd"}); 
		});
		
		/* close progress bar */
		function closewin(){
			$("#progressbar").dialog('close');
		}
		/* end close progress bar */
		
		function gridReload()
		{ 
			jQuery("#list").setGridParam
			({url:url+urls}).trigger("reloadGrid");		
		} 
		  
		var timeoutHnd; 
		function doSearch(ev)
		{ 
			if(timeoutHnd) 
				clearTimeout(timeoutHnd) 
				timeoutHnd = setTimeout(srcReload,500) 
		} 
		  
		function srcReload()
		{ 
		  	var code = jQuery("#search_nik").val(); 
			var desc = jQuery("#search_desc").val(); 
			if (code == ""){
				code = "-";
			} 

			jQuery("#list").setGridParam
			({url:url+"SearchData/"+code+"/"+desc}).trigger("reloadGrid");		
		} 
		   
	function delData()
	{
		var postdata={};
		var ids = jQuery("#list").getGridParam('selrow'); 
		var data = $("#list").getRowData(ids) ;
		if( ids != null ){
			var answer = confirm ("Hapus Data Batch Bibitan : " + data.NURSERYCODE + ":" + data.DESCRIPTION + "?" )
			if (answer) {
				$.post(url+'DeleteData/'+data.NURSERYCODE, 
				postdata,
				function(message,status) { 
				  if(status !== 'success') { 
						alert('data untuk tanggal ini sudah terisi.'); 
				  } else {             
						alert('data berhasil terhapus.')
						gridReload();
				  };  
			   });
			}
		}
		else { alert("Please Select Row to delete!"); }
	}

	function TambahData()
	{
		init_nurs();
		$("#i_nurs_code").removeAttr('disabled');
		$("#nurs_form").dialog('open');
		$("#form_mode").val("POST");
	}

	function submit()
	{
		var postdata={};
		var ids = jQuery("#list").getGridParam('selrow'); 
		var data = $("#list").getRowData(ids) ; 
		var mode = $("#form_mode").val();
		postdata['NURSERYCODE'] = $("#i_nurs_code").val() ; 
		postdata['DESCRIPTION']=$("#i_nurs_desc").val();
		postdata['DATEPLANTED']=$("#i_date").val();
		postdata['VARIETAS']=$("#i_nurs_varietas").val();
		postdata['QTYONHAND']=$("#i_nurs_qtyonhand").val();
		var isActive = $("#i_active").is(':checked');
		if(isActive==true) {
			isActive=1;
		} else {
			isActive=0;
		}
		postdata['INACTIVE']= isActive;
		postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
		
		if (mode == "GET"){
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
			$("#progressbar").dialog('open');
			$.post( url+'EditData/'+$("#i_nurs_code").val(), postdata, function(message) {
				 if(message.replace(/\s/g,"") != 0 ) { 
					   $("#load").hide();
					   document.getElementById('msg').innerHTML= message;
					   $("#cok").attr("disabled", false);
				 } else { 
					  $("#load").hide();
					  $("#cok").attr("disabled", false);
					  document.getElementById('msg').innerHTML= 'data tersimpan';
					  gridReload();
					  $("#nurs_form").dialog('close');  
				  };  
			  }); 
		}  else if (mode == "POST")  {
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
			$("#progressbar").dialog('open');
			$.post( url+'AddNew/'+$("#i_nurs_code").val(), postdata, function(message) {
				 if(message.replace(/\s/g,"") != 0 ) { 
					   $("#load").hide();
					   document.getElementById('msg').innerHTML= message;
					   $("#cok").attr("disabled", false);
				 } else { 
					  $("#load").hide();
					  $("#cok").attr("disabled", false);
					  document.getElementById('msg').innerHTML= 'data tersimpan';
					  gridReload();
					  $("#nurs_form").dialog('close');  
				  };  
			  });   
		}
	 }
  
function Edit()
{                     
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (ids!=null ) {
        init_nurs();
        $("#i_nurs_code").val(data.NURSERYCODE);
        $("#i_nurs_desc").val(data.DESCRIPTION);
        $("#i_date").val(data.DATEPLANTED);
        $("#i_nurs_varietas").val(data.VARIETAS);
        $("#i_nurs_qtyonhand").val(data.QTYONHAND);
       
        $("#i_nurs_code").attr('disabled','disabled');
		var isActive = data.INACTIVE;
        if (isActive==1) {
            $("#i_active").attr('checked',true);
        } else {
            $("#i_active").attr('checked',false);    
        }
               
        $("#nurs_form").dialog('open');
        $("#form_mode").val("GET");
    } else {
        alert("harap pilih data untuk di edit");
    }                
}
//###################### END BUTTON FUNCTION ###########################
</script>

<div id"gridSearch">  
    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
    <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
    <tr>
        <td>Kode</td>
        <td>:</td>
        <td>
        <input type="text" class="input" id="search_nik" onkeydown="doSearch(arguments[0]||event)" />
        </td>
        <td>Deskripsi</td>
        <td>:</td>
        <td>
        <input type="text" class="input" id="search_desc" onkeydown="doSearch(arguments[0]||event)" />
        </td>
    </tr>
    </table> 
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table> 
    <div id="pager" class="scroll" style="text-align:center;"></div>
</div>
<br>

<div id="save" class="scroll" style="float:left;">
<input type="button"  id="add" value="Tambah" onclick="TambahData()">
<input type="button"  id="edit" value="Ubah" onclick="Edit()">
<input type="button"  id="delete" value="Hapus" onclick="delData()">
</div>

<div id="nurs_form">
<table border="0" width="100%" class="teks_">
	<tr>
		<td>
			<tr>
				<td width="130">Kode Batch Bibitan</td>
				<td>:</td>
				<td><input type="text" id="i_nurs_code" name="i_nurs_code" class="input" tabindex="1"/></td>
			</tr>
			<tr>
				<td width="20">Deskripsi</td>
				<td>:</td>
				<td><textarea rows="5" cols="25" id="i_nurs_desc" name="i_nurs_desc" style="height:50px; width:200px;" class="input" tabindex="2">
					</textarea> 
				</td>
			</tr>
			<tr>
				<td >Periode Batch</td>
				<td>:</td>
				<td><input type="text" id="i_date" name="i_date" class="input" tabindex="3"/></td>
			</tr>
			<tr>
				<td >varietas</td>
				<td>:</td>
				<td><input type="text" id="i_nurs_varietas" name="i_nurs_varietas" class="input" tabindex="4"/></td>
			</tr>
			<tr>
				<td >Jumlah Kuantitas</td>
				<td>:</td>
				<td><input type="text" id="i_nurs_qtyonhand" name="i_nurs_qtyonhand" class="input" tabindex="6"/></td>
			</tr>
			<tr>			
			<tr>
				<td>Inaktif</td>
				<td>:</td>
				<td><input type="checkbox" id="i_active" name="i_active" class="input" tabindex="6" maxlength="50"/></td>
			</tr>

	
		<td colspan="5"><input type="hidden" id="form_mode"></td>
	</tr>
</table>
</div>

<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 
<!-- end progress bar -->

