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
		 $("#fPTA").dialog({
			bgiframe: true, autoOpen: false, height: 340, width: 500,
			modal: true, title: "Entri Data PTA", resizable: false, 
			moveable: true, closeOnEscape: false,
			open: function() { $(".ui-dialog-titlebar-close").hide(); },
			buttons: { 'Tutup': function() {
						  $("#fPTA").dialog("close");     
				   }, 'Simpan': function() {
						  submit();
				   }
			   } 
		 }); 
	}); 
	
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
  
  colNamesT_mb_header.push('Aktivitas');
  colModelT_mb_header.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false, hidden:false, width: 130, align:'left'});
  
  colNamesT_mb_header.push('Sub Aktivitas');
  colModelT_mb_header.push({name:'SUB_ACTIVITY_CODE',index:'SUB_ACTIVITY_CODE', editable: false, hidden:true, width: 90, align:'center'});
  
  colNamesT_mb_header.push('Sub Aktivitas');
  colModelT_mb_header.push({name:'SUB_ACTIVITY_DESC',index:'SUB_ACTIVITY_DESC', editable: false, hidden:false, width: 90, align:'center'});
  
  colNamesT_mb_header.push('Type Infras');
  colModelT_mb_header.push({name:'IS_IF_TYPE',index:'IS_IF_TYPE', editable: false, hidden:true, width: 80, align:'center'});
  
  colNamesT_mb_header.push('Type Infras');
  colModelT_mb_header.push({name:'IFTYPE_NAME',index:'IFTYPE_NAME', editable: false, hidden:false, width: 80, align:'center'});
  
  colNamesT_mb_header.push('Sub Type Infras');
  colModelT_mb_header.push({name:'IS_IF_SUBTYPE',index:'IS_IF_SUBTYPE', editable: false, hidden:true, width: 110, align:'center'});
  
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
	   caption:"Upload Data", buttonicon:'ui-icon-newwin',
	   onClickButton: function(){  OpenUploadForm(); }, position:"left" });
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
		caption:"Hapus", buttonicon:'ui-icon-newwin',
		onClickButton: function(){ hapus(); }, position:"left" }); 
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
		caption:"Hapus Semua Data", buttonicon:'ui-icon-newwin',
		onClickButton: function(){ hapusAll(); }, position:"left" }); 
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
	   caption:"PTA", buttonicon:'ui-icon-newwin',
	   onClickButton: function(){  OpenPTAForm(); }, position:"left" });              
	jGrid_mb_header.navButtonAdd('#pager_mb_header',{
	  caption:"Export", buttonicon:'ui-icon-newwin',
	  onClickButton: function(){ hapus(); }, position:"left" });                
  }
  jQuery("#list_mb_header").ready(loadView_mb_header);
  
  function gridReload(){ 
	  var company = jQuery("#company1").val(); 
	  var periode = jQuery("#tahun1").val(); 
	  jQuery("#list_mb_header").setGridParam({url:url+"pms_c_master_budget/grid_mb_header/"+company+"/"+periode}).trigger("reloadGrid"); 
  }
  /* ######## start coa ######## */
   	  	  		  
  function hapus() {
	 var postdata = {};
	 var ids = jQuery("#list_mb_header").getGridParam('selrow'); 
	 var data = $("#list_mb_header").getRowData(ids) ;
	 postdata['MASTER_BUDGET_ID'] = data.MASTER_BUDGET_ID;  
	 if(data.MASTER_BUDGET_ID == undefined || data.MASTER_BUDGET_ID == "" ){
		  alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
	  } else {
	  
		  var answer = confirm ("Hapus data budget untuk " + data.COA_DESCRIPTION  + ", " + data.IS_IF_TYPE + ", " + data.IS_IF_SUBTYPE + "?" )
		   if (answer){
			  $.post( url+'pms_c_master_budget/deleteMbudget/', postdata,function(status) { 
				  if(status.replace(/\s/g,"") != "") { 
					  if(status == 99999){
							alert("data master budget ini tidak dapat dihapus karena sudah digunakan di dalam transaksi");
					  } else {
							if(status > 0 )	{ 					  
								alert('data berhasil terhapus.')
								gridReload();
							} else {
								alert('tidak ada data yang terhapus')
							}
					  }
				  }  
			  });
		   }
	  }
  }  
  
  function hapusAll() {
	if( $("#company1").val() != "" && $("#tahun1").val() != "" ){
	  
		  var answer = confirm ("Hapus data budget untuk PT "+$("#company1").text()+" Tahun " + $("#tahun1").val() + "?" )
		   if (answer){
			  $.post( url+'pms_c_master_budget/deleteMbudgetAll/', postdata,function(status) { 
				  if(status.replace(/\s/g,"") != "") { 				  
					  if(status > 0 )	{ 					  
						  alert('data berhasil terhapus.')
						  gridReload();
					  } else {
						  alert('tidak ada data yang terhapus')
					  }	  
				  }  
			  });
		   }
	  } else {
	  		alert("Data perusahaan dan periode tidak boleh kosong..")
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
  	
 
  
  function initFPTA(){
	  $("#ptaCompany").val("");
	  $("#ptaPeriode").val("");
	  $("#ptaDocument").val("");
	  $("#ptaAct").val("");
	  $("#ptaSubAct").val("");
	  $("#ptaTypeInfras").val("");
	  $("#ptaSubtypeInfras").val("");
	  $("#ptaVal").val("");
	  $("#ptaNotes").val("");
	  $("#PTAmessage").hide();
	  $("#PTAprogress").hide();
	  $("#ptaBudgetId").val("");
	  
  }
  /* PTA */
  function OpenPTAForm(){
	  if( $("#company1").val() == "" || $("#tahun1").val() == "" ){
		  alert("Perusahaan dan periode tidak boleh kosong");
	  } else {
		  initFPTA();
		  var ids = jQuery("#list_mb_header").getGridParam('selrow'); 
		  var data = $("#list_mb_header").getRowData(ids) ;
		  if(ids){
			  
			  $("#ptaBudgetId").val(data.MASTER_BUDGET_ID);
			  $("#ptaCompany").val(data.COMPANY_CODE);
			  $("#ptaPeriode").val(data.PERIODE);
			  $("#ptaAct").val(data.ACTIVITY_CODE);
			  $("#ptaSubAct").val(data.SUB_ACTIVITY_CODE);
			  $("#ptaTypeInfras").val(data.IS_IF_TYPE);
			  $("#ptaSubtypeInfras").val(data.IS_IF_SUBTYPE);
			  $("#fPTA").dialog("open");
		  } else {
		  	  alert("mohon pilih data yang akan diajukan PTA nya terlebih dahulu!!");
		  }
	  }
  }
  
  function submit() {
	var postdata = {}; 
	var ids = jQuery("#list_mb_header").getGridParam('selrow'); 
	var data = $("#list_mb_header").getRowData(ids) ; 
	
	postdata['MASTER_BUDGET_ID'] = $("#ptaBudgetId").val() ; 
	postdata['DOCUMENT_NUMBER'] = $("#ptaDocument").val() ; 
	postdata['ACTIVITY_CODE'] = $("#ptaAct").val() ; 
	postdata['SUB_ACTIVITY_CODE'] = $("#ptaSubAct").val();
	postdata['IS_IF_TYPE'] = $("#ptaTypeInfras").val(); 
	postdata['IS_IF_SUBTYPE'] = $("#ptaSubtypeInfras").val(); 
	postdata['PROG_RUPIAH'] = $("#ptaVal").val(); 
	postdata['COMPANY_CODE'] = $("#ptaCompany").val(); 
	postdata['PERIODE'] = $("#ptaPeriode").val(); 
	postdata['NOTES'] = $("#ptaNotes").val(); 
	
	if( $("#ptaBudgetId").val() == ""){
		alert("Mohon pilih kembali data yang akan diajukan PTA");
	} else if ( $("#ptaVal").val() == "" || $("#ptaVal").val() < 10000 ) {
		alert("Mohon masukkan nilai PTA yang benar!!");  
	} else {
		$.post( url+'pms_c_master_budget/cekPTA/', postdata,function(status) { 
		  if(status.replace(/\s/g,"") != "") { 
			  if(status > 0 )	{ 					  
				 var answer = confirm ("Data untuk PTA sudah pernah diajukan sebelumnya, apakah anda yakin akan melakukan perbaharuan data?" )
		   		 if (answer){
					 $.post( url+'pms_c_master_budget/addPTA/', postdata,function(statusd) { 
						  if(status.replace(/\s/g,"") != "") { 
							  if(statusd > 0 )	{ 					  
								  alert('data berhasil tersimpan.');
								  gridReload();
							  }	else {
								  alert('data gagal tersimpan.')
							  }				
						  }
					  });
				 }
			  }	else {
				  $.post( url+'pms_c_master_budget/addPTA/', postdata,function(statusd) { 
					  if(status.replace(/\s/g,"") != "") { 
						  if(statusd > 0 )	{ 					  
							  alert('data berhasil tersimpan.');
							  gridReload();
						  }	else {
							  alert('data gagal tersimpan.')
						  }				
					  }
				  });
			  }				
		  }
		});
	}
  }
  //ptaCompany, ptaPeriode, ptaDocument, ptaAct, ptaSubAct, ptaTypeInfras, ptaSubtypeInfras, ptaVal, ptaNotes,  PTAprogress, PTAmessage, ptaBudgetId
</script>
</head>

<body>
<span>
	Perusahaan : </span><?php if(isset($dropcompany)){ echo $dropcompany; }?> <p />
<span>Master Budget Tahun : </span><?php if(isset($periode)){ echo $periode; }?> <p />
<table id="list_mb_header" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_mb_header" class="scroll"></div>

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

<div id="fPTA">
<div>
    <table cellpadding="0" cellspacing="0">
    <tr>
          <td> Perusahaan </td>
          <td>:</td>
          <td> <input type="text" id="ptaCompany" name="ptaCompany" style="width:60px;" class="input" disabled="disabled" /></td>
    </tr>
    <tr>
          <td> Periode </td>  
          <td>:</td>
          <td> <input type="text" id="ptaPeriode" name="ptaPeriode" style="width:60px;" class="input" disabled="disabled"/></td>
    </tr>
    <tr>
          <td> No Dokumen PTA </td>
          <td>:</td>
          <td> <input type="text" id="ptaDocument" name="ptaDocument" class="input" /></td>
    </tr>
    <tr>
          <td> Aktivitas </td>
          <td>:</td>
          <td> <input type="text" id="ptaAct" name="ptaAct" class="input" style="width:70px;" disabled="disabled"/></td>
    </tr>
    <tr>
          <td> Sub Aktivitas </td> 
          <td>:</td>
          <td> <input type="text" id="ptaSubAct" name="ptaSubAct" style="width:70px;" class="input" disabled="disabled"/></td>
    </tr>
    <tr>
          <td> Tipe Infrastruktur </td>
          <td>:</td>
          <td> <input type="text" id="ptaTypeInfras" name="ptaTypeInfras" style="width:70px;" class="input" disabled="disabled"/></td>
    </tr> 
    <tr>
          <td> Subtipe Infrastruktur </td>
          <td>:</td>
          <td> <input type="text" id="ptaSubtypeInfras" name="ptaSubtypeInfras" style="width:70px;" class="input" disabled="disabled"/></td>
    </tr>
    <tr>  
          <td> Nilai PTA </td>
          <td>:</td>
          <td> <input type="text" id="ptaVal" name="ptaVal" class="input" /></td>
    </tr>
    <tr>
          <td> Catatan </td>
          <td>:</td>
          <td> <input type="hidden" id="ptaBudgetId" name="ptaBudgetId" /> <textarea id="ptaNotes" name="ptaNotes" style="height:40px; width:200px" class="input" />
          	   
          </td>
    </tr>
    </table>
    
    <div id="PTAprogress">
        <img class='loading' src='<?= base_url() ?>public/themes_pms/img/loader14.gif' alt='loading...' />
    </div>
    
    <div id="PTAmessage">
    </div>
   
</div>


  </body>
  </html> 	