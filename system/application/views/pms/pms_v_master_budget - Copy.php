<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>

</style>

<script type="text/javascript">
$(document).ready(function() {
	$("#progress").hide(); 
	$("#message").hide(); 
	/* ###### dialog ##### */
	$(function() {
		 $("#form_coa").dialog({
			bgiframe: true, autoOpen: false, height: 550, width: 600,
			modal: true, title: "Aktivitas", resizable: false, moveable: true,
			buttons: { 'Tutup': function() {
									   $("#form_coa").dialog("close");     
				   }
			   } 
		 }); 
	}); 
	
	$(function() {
		 $("#myForm").dialog({
			bgiframe: true, autoOpen: false, height: 270, width: 500,
			modal: true, title: "Upload data master budget", resizable: false, 
			moveable: true, closeOnEscape: false,
			open: function() { $(".ui-dialog-titlebar-close").hide(); },
			buttons: { 'Tutup': function() {
						  $("#myForm").dialog("close");     
				   }
			   } 
		 }); 
	}); 
	
	$(function() {
		 $("#form_coa").dialog({
			bgiframe: true, autoOpen: false, height: 550, width: 600,
			modal: true, title: "Aktivitas", resizable: false, moveable: true,
			buttons: { 'Tutup': function() {
									   $("#form_coa").dialog("close");     
				   }
			   } 
		 }); 
	}); 
	
	$(function() {
		 $("#input_mb").dialog({
			bgiframe: true, autoOpen: false, height: 375, width: 475,
			modal: false, title: "Input Data Master Budget", resizable: false, moveable: true,
			buttons: {
						'Tutup': function() {
										init();        
						},
						'Simpan': function() {
										init();        
						}
					} 
		 }); 
	}); 
	
});	


/* ###### end dialog ##### */
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
var url = "<?= base_url().'index.php/pms/' ?>";   

  /*grid*/
  
  var jGrid_mb_header = null;
  var colNamesT_mb_header = new Array();
  var colModelT_mb_header = new Array();
																		 
  colNamesT_mb_header.push('ID');
  colModelT_mb_header.push({name:'MASTER_BUDGET_ID',index:'MASTER_BUDGET_ID', hidden:true, 
			  width: 80, align:'center'});
  
  colNamesT_mb_header.push('Type');
  colModelT_mb_header.push({name:'MASTER_BUDGET_TYPE',index:'MASTER_BUDGET_TYPE', 
			  hidden:false, width: 80, align:'center'});
  
  colNamesT_mb_header.push('Periode');
  colModelT_mb_header.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:true, 
			  width: 70, align:'left'});
  
  colNamesT_mb_header.push('Kode');
  colModelT_mb_header.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, hidden:false, 
			  width: 70, align:'center'});
  
  colNamesT_mb_header.push('Keterangan');
  colModelT_mb_header.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false, hidden:false, width: 130, align:'left'});
  
  colNamesT_mb_header.push('Sub Aktivitas');
  colModelT_mb_header.push({name:'SUB_ACTIVITY_CODE',index:'SUB_ACTIVITY_CODE', editable: false, hidden:false, width: 90, align:'center'});
  
  colNamesT_mb_header.push('Sub Aktivitas');
  colModelT_mb_header.push({name:'SUB_ACTIVITY_DESC',index:'SUB_ACTIVITY_DESC', editable: false, hidden:true, width: 90, align:'center'});
  
  colNamesT_mb_header.push('Type Infras');
  colModelT_mb_header.push({name:'IS_IF_TYPE',index:'IS_IF_TYPE', editable: false, hidden:false, width: 80, align:'center'});
  
  colNamesT_mb_header.push('Sub Type Infras');
  colModelT_mb_header.push({name:'IS_IF_SUBTYPE',index:'IS_IF_SUBTYPE', editable: false, hidden:false, width: 110, align:'center'});
			  
  colNamesT_mb_header.push('Satuan');
  colModelT_mb_header.push({name:'SATUAN1',index:'SATUAN1', editable: false, hidden:false, width: 70, align:'center'});
  
  colNamesT_mb_header.push('Satuan 2');
  colModelT_mb_header.push({name:'SATUAN2',index:'SATUAN2', editable: false, hidden:false, width: 70, align:'center'});
  
  colNamesT_mb_header.push('Qty');
  colModelT_mb_header.push({name:'QTY',index:'QTY', editable: false, editrules:{number:true}, 
	  formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
	  decimalPlaces: 0}, hidden:false, width: 80, align:'right'});
  
  colNamesT_mb_header.push('Rotasi');
  colModelT_mb_header.push({name:'ROTASI',index:'ROTASI', editable: false, editrules:{number:true}, 
	  formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
	  decimalPlaces: 0}, hidden:false, width: 70, align:'right'});
  
  colNamesT_mb_header.push('Rupiah');
  colModelT_mb_header.push({name:'RUPIAH',index:'RUPIAH', editable: false, editrules:{number:true}, 
	  formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
	  decimalPlaces: 0}, hidden:false, width: 100, align:'right'});
  
  colNamesT_mb_header.push('Rp / Sat');
  colModelT_mb_header.push({name:'RUPIAH_PER_SATUAN',index:'RUPIAH_PER_SATUAN', editable: false, 
	  editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", 
	  defaultValue: '', decimalPlaces: 0}, hidden:false, width: 80, align:'right'});
  
  
  colNamesT_mb_header.push('Perusahaan');
  colModelT_mb_header.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 60, align:'center'});
	   
  var loadView_mb_header = function(){  
	var company = jQuery("#company1").val();
	jGrid_mb_header = jQuery("#list_mb_header").jqGrid({
		url:url+'pms_c_master_budget/grid_mb_header/'+jQuery("#company1").val()+'/'+jQuery("#tahun1").val(),
		mtype : "POST", datatype: "json",
		colNames: colNamesT_mb_header , colModel: colModelT_mb_header ,
		rownumbers:true, viewrecords: true, multiselect: false, 
		caption: "Data Master Budget <?php echo $company_dest;?>", 
		rowNum:20, rowList:[10,20,30], multiple:true,
		height: 320, cellEdit: false,
		loadComplete: function(){ }, imgpath: gridimgpath, 
		pager: jQuery('#pager_mb_header'), sortname: colModelT_mb_header[0].name
	});
	jGrid_mb_header.navGrid('#pager_mb_header',{edit:false,add:false,del:false, search: false, refresh: true});
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
		caption:"Ubah", buttonicon:"ui-icon-add", 
		onClickButton: function(){ dialog_inputmb_open(); }, position:"left" });
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
		caption:"Hapus", buttonicon:'ui-icon-newwin',
		onClickButton: function(){ edit_tunpot(); }, position:"left" });  
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
	   caption:"PTA", buttonicon:'ui-icon-newwin',
	   onClickButton: function(){  OpenUploadForm(); }, position:"left" });              
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
	  caption:"Export", buttonicon:'ui-icon-newwin',
	  onClickButton: function(){ edit_tunpot(); }, position:"left" });                
  }
  jQuery("#list_mb_header").ready(loadView_mb_header);
  
  function gridReload(){ 
	  var company = jQuery("#company1").val(); 
	  var periode = jQuery("#tahun1").val(); 
	  jQuery("#list_mb_header").setGridParam({url:url+"pms_c_master_budget/grid_mb_header/"+company+"/"+periode}).trigger("reloadGrid"); 
  }
  /* ######## start coa ######## */
  
  var jGrid_mcoa = null;
  var colNamesT_mcoa = new Array();
  var colModelT_mcoa = new Array();
															 
  colNamesT_mcoa.push('KODE AKTIVITAS');
  colModelT_mcoa.push({name:'ACCOUNTCODE',index:'ACCOUNTCODE', hidden:false, width: 120, align:'center'});
  
  colNamesT_mcoa.push('KETERANGAN AKTIVITAS');
  colModelT_mcoa.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false, hidden:false, width: 420, align:'left'});
  
  var loadView_mcoa = function(){
	  jGrid_mcoa = jQuery("#list_mcoa").jqGrid({
				  url:url+'pms_c_master_budget/grid_mcoa/',
				  mtype : "POST", datatype: "json", colNames: colNamesT_mcoa , colModel: colModelT_mcoa,
				  rownumbers:true, viewrecords: true, multiselect: false, caption: "Data Master Aktivitas", 
				  rowNum:20, rowList:[10,20,30], multiple:true, height: 320, cellEdit: false,
				  imgpath: gridimgpath, pager: jQuery('#pager_mcoa'), 
				  sortname: colModelT_mcoa[0].name,  
				  ondblClickRow: function(){
				  var id = jQuery("#list_mcoa").getGridParam('selrow');
				  if (id)	{
					  var data = $("#list_mcoa").getRowData(id) ;
					  $("#i_activity_code").val(id);
					  $("#i_activity_desc").val(data.COA_DESCRIPTION);
					  $("#form_coa").dialog("close");				}
			  }
		  });
	  jGrid_mcoa.navGrid('#pager_mcoa',{edit:false,add:false,del:false, search: false, refresh: true});            
  }
  jQuery("#list_mcoa").ready(loadView_mcoa);
  /* ######## end coa ####### */
  
  var timeoutHnd; 
  var flAuto = false; 
  function doSearchCoa(ev){ 
	  if(timeoutHnd) 
  
	  clearTimeout(timeoutHnd) 
	  timeoutHnd = setTimeout(gridCoaReload,500) 
  } 
		  
  function gridCoaReload(){ 
	  var scoa = jQuery("#search_coa").val(); 
	  if (scoa != ""){
		  jQuery("#list_mcoa").setGridParam({url:url+"pms_c_master_budget/grid_mcoa/"+scoa}).trigger("reloadGrid");  
	  } 
  }
  
  function dialog_coa_open(){
	  $("#search_coa").val("");
	  $("#form_coa").dialog('open');
  }
  
  function dialog_inputmb_open(){
	  $("#input_mb").dialog('open');
  }
  
  function init(){
	  $("#i_activity_code").val("");
	  $("#i_activity_desc").val("");
	  $("#i_qty").val("");
	  $("#i_satuan1").val("");
	  $("#i_rotasi").val(""); 
	  $("#i_rupiah").val("");
	  $("#i_rp_sat").val("");
	  $("#isclose").attr('checked',false); 
	  $("#isapprove").attr('checked',true); 
	  $("#form_mode").val('');
	  $("#input_mb").dialog('close');
  }
	  
  function submit() {
	  var postdata = {}; 
	  var ids = jQuery("#list_gang").getGridParam('selrow'); 
	  var data = $("#list_gang").getRowData(ids) ; 
	  var mode = $("#form_mode").val();       
			  
	  postdata['GANG_CODE'] = $("#i_gangcode").val() ; 
	  postdata['DESCRIPTION'] = $("#i_gangname").val() ; 
	  postdata['MANDORE_CODE'] = $("#i_nikmandor").val() ; 
	  postdata['MANDORE1_CODE'] = $("#i_nikmandor1").val();
	  postdata['KERANI_CODE'] = $("#i_nikkerani").val(); 
	  postdata['DEPARTEMEN'] = $("#i_departemen").val(); 
	  postdata['DIVISION'] = $("#i_divisi").val(); 
	  
	  if (mode == "GET"){
		  $.post( url+'m_gang/update/'+$("#i_gangcode").val(), postdata,function(message,status) { 
			  if(status !== 'success') { 
				  alert('data untuk kemandoran ini sudah terisi.'); 
			  } else { 
				  gridReload();
				  alert('data berhasil tersimpan.')
				  
			  };
		  });
	  } else if (mode == "POST") {
		  $.post( url+'m_gang/create', postdata,function(message,status) { 
			  if(status !== 'success') { 
				  alert('data untuk karyawan ini sudah terisi.'); 
			  } else { 
				  gridReload();
				  alert('data berhasil tersimpan.')
				  
			  };
		  });
	  }
  }
		  
  function tambah(){
	  init_gang();
	  $("#gang_form").dialog('open');
	  $("#form_mode").val("POST");
  }
  
  function ubah(){
	  /* initiate data */        
	  var ids = jQuery("#list_gang").getGridParam('selrow'); 
	  var data = $("#list_gang").getRowData(ids) ; 
	  $("#i_gangcode").val(data.GANG_CODE);
	  $("#i_gangname").val(data.DESCRIPTION);
	  $("#i_nikmandor").val(data.MANDORE_CODE);
	  $("#i_namamandor").val(data.NAMA);
	  $("#i_nikkerani").val(data.KERANI_CODE);
	  $("#i_nikmandor1").val(data.MANDORE1_CODE);
	  $("#i_departemen").val(data.DEPARTEMEN_CODE); 
	  $("#i_divisi").val(data.DIVISION_CODE);
	  $("#gang_form").dialog('open');
	  $("#form_mode").val("GET");            
  }
  
  function init_gang(){
	  $("#i_gangcode").val("");
	  $("#i_gangname").val("");
	  $("#i_nikmandor").val("");
	  $("#i_namamandor").val("");
	  $("#i_nikkerani").val("");
	  $("#i_nikmandor1").val("");
	  $("#i_departemen").val(""); 
	  $("#i_divisi").val("");
	  $("#gang_form").dialog('close');
	  $("#form_mode").val('');
  }
		  
  function hapus() {
	  
	 var postdata = {};
	 var ids = jQuery("#list_gang").getGridParam('selrow'); 
	 var data = $("#list_gang").getRowData(ids) ;
	 postdata['GANG_CODE'] = data.GANG_CODE;  
	 if(data.GANG_CODE == undefined){
		  alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
	  } else {
	  
	  var answer = confirm ("Hapus Data Dari Kemandoran : " + data.GANG_CODE + ":" + data.DESCRIPTION + "?" )
	  if (answer)
	  {
		 $.post( url+'m_gang/delete_gang/', postdata,function(message,status) { 
			 if(status !== 'success') { 
					  alert('data untuk tanggal ini sudah terisi.'); 
				} else { 
					  
					  alert('data berhasil terhapus.')
					  gridReload();
				 };  
			});
		  }
	   }
  }  
  /* fungsi upload file */
  
  function OpenUploadForm(){
	  if( $("#company1").val() == "" || $("#tahun1").val() == "" ){
		  alert("Perusahaan dan periode tidak boleh kosong");
	  } else {
		  $("#myfile").val("");
		  $("#message").hide();
		  $("#progress").hide();
		  $("#company2").val( $("#company1").val() );
		  $("#tahun2").val( $("#tahun1").val() );
		  $("#company2").attr("disabled", true);
		  $("#tahun2").attr("disabled", true);
		  $("#myForm").dialog("open");
	  }
  }
  
  function startUpload(){
	  var status = $("#upStatus").val();
	  var theFile = $("#myfile").val();
	  
	  if(theFile == ''){
			  
		  $("#message").html("Mohon pilih file terlebih dahulu");
		  $("#message").show();
		  
	  } else {
		  if (status == 0){
			  $("#message").html("Mohon menunggu proses upload data sedang berlangsung");
			  //$("#progress").show();
			  $("#message").show();
			  return ajaxFileUpload();
			  //$("#bUpload").attr("disabled", true);
		  } else {
			  $("#bUpload").removeAttr("disabled");
			  $("#progress").hide();
			  $("#message").hide();
		  }
	  }		
  }
  
  function ajaxFileUpload(){
	  $("#progress").ajaxStart(function(){
		  $(this).show();
	  }).ajaxComplete(function(){
		  $(this).hide();
	  });
	  
	  $.ajaxFileUpload({
			  url:url+'pms_c_master_budget/do_import/',
			  secureuri:false, fileElementId:'myfile',
			  dataType: 'json',
			  success: function (data, status)
			  {
				  if(typeof(data.error) != 'undefined')
				  {
					  if(data.error != '')
					  {
						  //alert(data.error);
						  $("#message").html(data.error);
						  $("#message").show();
						  $("#progress").hide();
						 
					  } else {
						  $("#message").html(data.msg);
						  $("#message").show();
						  $("#progress").hide();
						  //$("#frm_load").dialog('close');
					  }
				  }
			  }, error: function (data, status, e){
				   	$("#message").html(data.msg);
					$("#message").show();
				 // $("#message").html("<font color='red'> " + data.msg + " </font>");
				  //("<font color='red'> " + data.msg + " </font>");
			  }
		  }
	  )     
  return false;
  }	
  
  function cek(){
		var options = { 
		beforeSend: function() {
				$("#progress").show();
				$("#bar").width('0%');
				$("#message").html("");
				$("#percent").html("0%");
			},
			uploadProgress: function(event, position, total, percentComplete) {
				$("#bar").width(percentComplete+'%');
				$("#percent").html(percentComplete+'%');
			},
			success: function() {
				$("#bar").width('100%');
				$("#percent").html('100%');
		
			},
			complete: function(response) {
				$("#message").html("<font color='green'>"+response.responseText+"</font>");
			},
			error: function(){
				$("#message").html("<font color='red'> ERROR: unable to upload files</font>");
			}   
		}; 
	
		$("#myForm").ajaxForm(options);
	}
	
	jQuery( "#bUpload" ).click(function() {
		startUpload();
	}); 
	
	jQuery( "#company1" ).change(function() {
		gridReload();
	});
	
	jQuery( "#tahun1" ).change(function() {
		gridReload();
	});
	
	jQuery( "#doOpenDialogCoa" ).click(function() {
		dialog_coa_open();
	});

</script>
</head>

<body>
<span>
	Perusahaan : </span><?php if(isset($dropcompany)){ echo $dropcompany; }?> <p />
<span>Master Budget Tahun : </span><?php if(isset($periode)){ echo $periode; }?> <p />
<table id="list_mb_header" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_mb_header" class="scroll"></div>


<!-- ######## TARIK DATA COA ####### -->
<div id="form_coa">
	<table id="cari_kemandoran" border="0" class="teks_" style="margin-bottom:4px;" cellpadding="2" cellspacing="2">
        <tr><td colspan="3">Cari Aktivitas :</td></tr>
        <tr><td>Kode / Deskripsi</td><td>:</td><td>
        <input type="text" value="" class="input" id="search_coa" onkeydown="doSearchCoa(arguments[0]||event)" /></td></tr>
    </table>
    <table id="list_mcoa" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    <div id="pager_mcoa" class="scroll"></div>
</div>
<!-- ######## FORM ######### -->

<div id="input_mb">
  <table width="100%" border="0" class="teks_" >
      <tr>
          <td width="150">Periode</td><td>:</td><td>
          <? if(isset($i_periode)){ echo $i_periode; } ?></td>
      </tr>
     <tr>
     	<td width="150">Kode Aktivitas</td><td>:</td><td>
          <input tabindex="4" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_activity_code" maxlength="25"/><img src="<?= $template_path ?>themes/base/images/Search.png" style="margin-bottom:-5px; margin-left:5px;" id="doOpenDialogCoa" /></td>
      </tr>
       <tr>
          <td width="150">Deskripsi</td> <td>:</td><td> 
          <input tabindex="5" type="text" style="text-transform: uppercase; width:250px ;" class="positive" id="i_activity_desc" maxlength="25"/></td>
      </tr>
      <tr>
          <td width="150">Qty</td> <td>:</td><td>
          <input tabindex="8" type="text" style="text-transform: uppercase; width:80px ;" class="positive" id="i_qty" maxlength="25"/></td>
      </tr>
      <tr>
          <td width="150">Satuan</td> <td>:</td><td>
          <? if(isset($satuan1)){ echo $satuan1; }?></td>
      </tr>
      <!-- <tr>
          <td width="150">Satuan 2</td> <td>:</td><td>
         <? if(isset($satuan2)){ echo $satuan2; }?></td>
      </tr> -->
      
       <tr>
          <td width="150">Rotasi</td><td>:</td><td>
          <input tabindex="9" type="text" style="text-transform: uppercase; width:80px ;" class="positive" id="i_rotasi" maxlength="25"/></td>
      </tr>
<tr>
          <td width="150">Rupiah Per Satuan</td><td>:</td><td>
          <input tabindex="10" type="text" id="i_rp_sat" class="input" style="width:120px" maxlength="25"/>
          </td>
      </tr>
       <tr>
          <td width="150">Rupiah</td><td>:</td><td>
          <input tabindex="10" type="text" id="i_rupiah" class="input" style="width:120px" maxlength="25"/>
          </td>
      </tr>
     
      <tr>
          <td width="150">Close</td><td>:</td><td>
          <input type="checkbox" tabindex="18" id="isclose" disabled="disabled" style="margin-left: 5px;" name="emp_input"/>
          </td>
      </tr>
      <tr>
          <td width="150">Approve</td><td>:</td><td>
          <input type="checkbox" tabindex="18" id="isapprove" disabled="disabled" style="margin-left: 5px;" name="emp_input"/>
          </td>
      </tr>
      <tr>
          <td colspan="3"><input type="hidden" id="form_mode_tunpot"></td>
      </tr>    
  </table>
              </div>
          <!-- end form tunjangan & potongan -->
      </p>
  </div>
 	
    <!-- ######## TARIK DATA COA ####### -->
<div id="form_coa">
	<table id="cari_kemandoran" border="0" class="teks_" style="margin-bottom:4px;" cellpadding="2" cellspacing="2">
        <tr><td colspan="3">Cari Aktivitas :</td></tr>
        <tr><td>Kode / Deskripsi</td><td>:</td><td>
        <input type="text" value="" class="input" id="search_coa" onkeydown="doSearchCoa(arguments[0]||event)" /></td></tr>
    </table>
    <table id="list_mcoa" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    <div id="pager_mcoa" class="scroll"></div>
</div>
<!-- ######## FORM ######### -->
 
  	<div id="myForm" class="form">
    	<div>
    	Perusahaan : </span> <?php if(isset($dropcompany2)){ echo $dropcompany2; }?> <p />
<span>Master Budget Tahun : </span><?php if(isset($periode2)){ echo $periode2; }?> <p />
        <input type="file" size="60" name="myfile" id="myfile">
        <input id="bUpload" type="submit" value="Mulai Upload Data">
        </div>
        <div id="progress">
            <img class='loading' src='<?= base_url() ?>public/themes_pms/img/loader14.gif' alt='loading...' />
        </div>
        
        <div id="message">
        </div>
       
        
        <input id="upStatus" type="hidden" value="0">
 	</div>  
  
  </body>
  </html> 	