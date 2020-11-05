<? 
    $template_path = base_url().$this->config->item('template_path');  
	$session = $this->session->userdata('LOGINID');
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';
var company = "<?=$company_code ?>";
$(function() {  $( "#tabs" ).tabs(); });
$('#tabs').tabs('select',0);

jQuery(document).ready(function(){
	
	
	/* auto reload list periode */
	var svTahun = document.getElementById("sTahun").value;
	var svCompany = document.getElementById("i_company").value;
	jQuery("#list_period").setGridParam({url:url+"m_closing_control/search_period/"+svTahun+"/"+ svCompany }).trigger("reloadGrid");  
	/* end auto reload list periode */
	
	/* auto reload list periode control */
	/* var _ids = jQuery("#list_period").getGridParam('selrow');
	var _data = $("#list_period").getRowData(_ids);
	
	var _idgroup = _data.PERIODE_ID; 		
	var _idcompany = _data.COMPANY_CODE;
	var _module = document.getElementById("sModule").value;
	$("#menulist_id").val(_data.PERIODE_ID);
	jQuery("#list_pControl").setGridParam({url:"m_closing_control/search_pControl/"+_idgroup+"/"+_idcompany+"/"+_module}).trigger("reloadGrid"); */
	/* end auto reload list periode control */
	
		  
	/* onChange dropdown year */
	$("#i_company").change(function() { 
		  var vurls = "";
		  var svTahun = document.getElementById("sTahun").value;
		  var svCompany = document.getElementById("i_company").value;
		 // vurls = "m_closing_control/search_period/"+svTahun+"/"+ svCompany;
		  /* if(svCompany != ""){
			  vurls += "/"+ svCompany;
		  } */
		  jQuery("#list_period").setGridParam({url:url+"m_closing_control/search_period/"+svTahun+"/"+ svCompany }).trigger("reloadGrid");	
	});	
	
	$("#sTahun").change(function() { 
		  var vurls = "";
		  var svTahun = document.getElementById("sTahun").value;
		  var svCompany = document.getElementById("i_company").value;
		 // vurls = "m_closing_control/search_period/"+svTahun+"/"+ svCompany;
		  /* if(svCompany != ""){
			  vurls += "/"+ svCompany;
		  } */
		  jQuery("#list_period").setGridParam({url:url+"m_closing_control/search_period/"+svTahun+"/"+ svCompany }).trigger("reloadGrid");	
	});	
	
	$("#sModule").change(function() { 
		var id = jQuery("#list_period").getGridParam('selrow');
		var data = $("#list_period").getRowData(id) ;
		var idgroup = data.PERIODE_ID; 
		
			
		var idcompany = data.COMPANY_CODE;
		var module = $("#sModule").val();
		$("#menulist_id").val(data.PERIODE_ID);
		if( idgroup == "" || idgroup == undefined) {
			alert("mohon pilih periode terlebih dahulu");
		} else {
		jQuery("#list_pControl").setGridParam({url:url+"m_closing_control/search_pControl/"+idgroup+"/"+idcompany+"/"+module}).trigger("reloadGrid");
		  /* if(svCompany != ""){
			  vurls += "/"+svCompany;
		  } */
		}
	});	
	/* end drop year */

	$("#inputPeriod").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 280, width: 350, modal: true,buttons: { 
			'Tutup	': function(){
				$("#inputPeriod").dialog("close");
				initFormPeriod();
			}, 'Ubah Periode': function(){
				submitPeriod("ubah");
			}
		}
	}); 
	
	$("#inputPControl").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 300, width: 350, modal: true,buttons: { 
			'Tutup	': function(){
				$("#inputPControl").dialog("close");
				//initFormRole();
			}, 'Ubah Period Control': function(){
				submitPControl("", "ubah")
			}
		}
	});
	
	$("#inputPControlDetail").dialog({
			bgiframe: true, autoOpen: false, resizable: true, draggable: true,
			closeOnEscape:false, height: 300, width: 350, modal: true,buttons: { 
			'Tutup	Detail': function(){
				$("#inputPControlDetail").dialog("close");
				//initFormRole();
			}, 'Ubah Detail Period': function(){
				submitPControlDetail("", "ubah")
			}
		}
	});
	 
});

var jGrid_Period = null;
var colNamesT_Period = new Array();
var colModelT_Period = new Array();

colNamesT_Period.push('Period ID');
colModelT_Period.push({name:'PERIODE_ID',index:'PERIODE_ID', hidden:true, width: 60, align:'center'});

colNamesT_Period.push('Perusahaan');
colModelT_Period.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, width: 80, align:'center'});
	
colNamesT_Period.push('Period Name');
colModelT_Period.push({name:'PERIODE_NAME',index:'PERIODE_NAME', editable: false, hidden:false, 
					  width: 100, align:'center'});
	
colNamesT_Period.push('Start');
colModelT_Period.push({name:'PERIODE_START',index:'PERIODE_START', hidden:false, width: 100, align:'center'});

colNamesT_Period.push('End');
colModelT_Period.push({name:'PERIODE_END',index:'PERIODE_END', editable: false, hidden:false, 
					  width: 100, align:'center'});
	
colNamesT_Period.push('Is Close');
colModelT_Period.push({name:'ISCLOSE',index:'ISCLOSE', hidden:false, editable: true, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, width: 100, align:'center'});

colNamesT_Period.push('Close By');
colModelT_Period.push({name:'CLOSE_BY',index:'CLOSE_BY', editable: false, hidden:false, 
					  width: 100, align:'center'});

colNamesT_Period.push('Close Date');
colModelT_Period.push({name:'CLOSE_DATE',index:'CLOSE_DATE', editable: false, hidden:false, 
					  width: 100, align:'center'});

colNamesT_Period.push('Reopen By');
colModelT_Period.push({name:'REOPEN_BY',index:'REOPEN_BY', editable: false, hidden:false, 
					  width: 100, align:'center'});

colNamesT_Period.push('Reopen Date');
colModelT_Period.push({Reopen:'REOPEN_DATE',index:'REOPEN_DATE', editable: false, hidden:false, 
					  width: 100, align:'center'});

colNamesT_Period.push('Status');
colModelT_Period.push({name:'status',index:'status', editable:false, hidden:false, width:80, align:'center'});
					  
colNamesT_Period.push('Action');
colModelT_Period.push({name:'act',index:'act', editable:false, hidden:false, width:70, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_period = function(){
jGrid_Period = jQuery("#list_period").jqGrid({
     url:url+'m_closing_control/search_period/'+document.getElementById("sTahun").value+'/'+document.getElementById("i_company").value,
     mtype : "POST", datatype: "json",
     colNames: colNamesT_Period , colModel: colModelT_Period ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     rowNum:20, height: 300, rowList:[10,20,30], imgpath: gridimgpath, rownumbers:true,
     pager: jQuery('#pager_period'), sortname: colModelT_Period[2].name, sortorder: "asc", viewrecords: true,
     caption:"Daftar Periode Tutup Buku",
	 onSelectRow: function(){
			var id = jQuery("#list_period").getGridParam('selrow');
			var data = $("#list_period").getRowData(id) ;
			var idgroup = data.PERIODE_ID; 		
			var idcompany = data.COMPANY_CODE;
			var module = document.getElementById("sModule").value;
			$("#menulist_id").val(data.PERIODE_ID);
			jQuery("#list_pControl").setGridParam({url:url+"m_closing_control/search_pControl/"+idgroup+"/"+idcompany+"/"+module}).trigger("reloadGrid");
			$('#tabs').tabs('select',1);
	 }, loadComplete: function(){ 
        	var ids = jQuery("#list_period").getDataIDs(); 
        	for(var i=0;i<ids.length;i++) { 
             	var cl = ids[i];
				var data = $("#list_period").getRowData(cl);
				if(data.ISCLOSE == '0'){
					ce = "<a href='#' onclick=\"viewPeriod('"+cl+"');\"/ style='cursor:pointer'>Close</a>";
					de = "<span style='font:#00F'>Open</span>";
					jQuery("#list_period").setCell(ids[i], 'status', '', {'background-color':'#3FF' });
					jQuery("#list_period").setRowData(ids[i],{act:ce});
					jQuery("#list_period").setRowData(ids[i],{status:de}); 
				} else {
					ce = "<a href='#' onclick=\"viewPeriod('"+cl+"');\"/ style='cursor:pointer'>Open</a>";
					de = "<span style='font:#F00'>Close</span>";
					jQuery("#list_period").setCell(ids[i], 'status', '', {'background-color':'#F60' });
					jQuery("#list_period").setRowData(ids[i],{act:ce});
					jQuery("#list_period").setRowData(ids[i],{status:de}); 
				}
         	}
			/* set pilih ke baris pertama saat load */
			//var firstid = $("tr:first","#list_period").attr("id");
			//$("#list_period").setSelection(firstid);
			/* end set pilih ke baris pertama saat load */
			
			/* load data di periode control */
			var data = $("#list_period").getRowData(ids) ;
			var idgroup = data.PERIODE_ID; 		
			var idcompany = data.COMPANY_CODE;
			$("#menulist_id").val(data.PERIODE_ID);
			jQuery("#list_pControl").setGridParam({url:url+"m_closing_control/search_pControl/"+idgroup+"/"+idcompany}).trigger("reloadGrid");
			/* end load data di periode control */
			
     }, imgpath: gridimgpath, pager: jQuery('#pager_period'), sortname: colModelT_Period[0].name
  });
  jGrid_Period.navGrid('#pager_period',{edit:false,add:false,del:false, search: true, refresh: true});
  
}
jQuery("#list_period").ready(loadView_period);

function reloadGridPeriode(){
	jQuery("#list_pControl").setGridParam({url:url+"m_closing_control/search_pControl/"+idgroup}).trigger("reloadGrid");	
}


function submitPControl(id, method){
	var postdata={};
	postdata['PERIODE_CONTROL_ID'] = $("#i_pControlid").val() ;
	postdata['PERIODE_NAME'] = $("#i_pControlname").val();
	var isActive = document.getElementById('i_pControlIsClose').checked;
	if(isActive==true) {
		isActive=1;
	} else {
		isActive=0;
	}
   	postdata['ISCLOSE'] = isActive;
	postdata['PERIODE_START'] = $("#i_pControlStart").val();
	postdata['PERIODE_END'] = $("#i_pControlEnd").val(); 
	postdata['MODULE'] = $("#i_pControlModule").val();	
	postdata['CLOSE_BY'] = $("#i_pControlCloseBy").val();
	postdata['CLOSE_DATE'] = $("#i_pControlCloseDate").val();
	postdata['REOPEN_BY'] = $("#i_pControlReopenBy").val();
	postdata['REOPEN_DATE'] = $("#i_pControlReopenDate").val();
	
	urls = url+"m_closing_control/updatePeriodControl/"+id;
	$.post(urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			alert(status); 
		} else { 
			var id = jQuery("#list_period").getGridParam('selrow');
			var data = $("#list_period").getRowData(id) ;
			var idgroup = data.PERIODE_ID; 	
			var idcompany = data.COMPANY_CODE;
			
			var module = $("#sModule").val();
			$("#menulist_id").val(data.PERIODE_ID);
			
			var idDetail = jQuery("#list_pControl").getGridParam('selrow');
			var dataDetail = $("#list_pControl").getRowData(idDetail) ;
			var idgroupDetail = dataDetail.PERIODE_CONTROL_ID; 		
			var idcompanyDetail = dataDetail.COMPANY_CODE;
			
			
			var _module = document.getElementById("sModule").value;
			jQuery("#list_pControl").setGridParam({url:url+'m_closing_control/search_pControl/'+$("#i_pControl").val()+'/'+ idcompany +'/'+ _module }).trigger("reloadGrid");
			
			
			jQuery("#list_pControlDetail").setGridParam({url:url+"m_closing_control/search_pControlDetail/"+idgroupDetail+"/"+idcompanyDetail}).trigger("reloadGrid");
			
			$("#list_pControl").setSelection(dataDetail);
		
			$("#inputPControl").dialog("close");
		}  
	});
}

function submitPControlDetail(id, method){
	var postdata={};
	postdata['PERIODE_CONTROL_DETAIL_ID'] = $("#i_pDetailControlDid").val() ;
	postdata['PERIODE_CONTROL_ID'] = $("#i_pDetailControlid").val() ;
	postdata['PERIODE_DATE'] = $("#i_pDetailDate").val();
	var isActive = document.getElementById('i_pDetailControlIsClose').checked;
	if(isActive==true) {
		isActive=1;
	} else {
		isActive=0;
	}
   	postdata['ISCLOSE'] = isActive;
	postdata['PERIODE_START'] = $("#i_pDetailControlStart").val();
	postdata['PERIODE_END'] = $("#i_pDetailControlEnd").val(); 
	postdata['MODULE'] = $("#i_pDetailControlModule").val();	
	
	postdata['CLOSE_BY'] = $("#i_pDetailControlCloseBy").val();
	postdata['CLOSE_DATE'] = $("#i_pDetailControlCloseDate").val();
	postdata['REOPEN_BY'] = $("#i_pDetailControlReopenBy").val();
	postdata['REOPEN_DATE'] = $("#i_pDetailControlReopenDate").val();
		
	urls = url+"m_closing_control/updatePeriodControlDetail/"+id;
	$.post(urls, postdata, function(status) {
		var status = new String(status);
		if(status.replace(/\s/g,"") != "") { 
			alert(status); 
		} else { 
			var id = jQuery("#list_period").getGridParam('selrow');
			var data = $("#list_period").getRowData(id) ;
			var idcompany = data.COMPANY_CODE;
			
			var idDetail = jQuery("#list_pControlDetail").getGridParam('selrow');
			var dataDetail = $("#list_pControlDetail").getRowData(idDetail) ;
			var idgroupDetail = dataDetail.PERIODE_CONTROL_ID; 		
			var idcompanyDetail = dataDetail.COMPANY_CODE;
						
			jQuery("#list_pControlDetail").setGridParam({url:url+"m_closing_control/search_pControlDetail/"+idgroupDetail+"/"+idcompanyDetail}).trigger("reloadGrid");
			
			$("#list_pControlDetail").setSelection(dataDetail);
		
			$("#inputPControlDetail").dialog("close");
		}  
	});
}

function submitPeriod(method){
	var postdata={};
		postdata['PERIODE_ID'] = $("#i_periodid").val() ;
		postdata['PERIODE_NAME'] = $("#i_periodname").val(); 
		postdata['PERIODE_START'] = $("#i_start").val(); 
		postdata['PERIODE_END'] = $("#i_end").val();
		//postdata['MODULE'] = $("#i_pControlModule").val();	
		postdata['CLOSE_BY'] = $("#i_closeBy").val(); 
		postdata['CLOSE_DATE'] = $("#i_closeDate").val(); 
		postdata['REOPEN_BY'] = $("#i_reopenBy").val();
		postdata['REOPEN_DATE'] = $("#i_reopenDate").val();		
		
			
		var isActive = document.getElementById('i_isClose').checked;
		if(isActive==true) {
			isActive=1;
		} else {
			isActive=0;
		}
    	postdata['ISCLOSE'] = isActive;
		
		urls = url+"m_closing_control/updateDataPeriod/"+$("#i_periodid").val();
		
		$.post(urls, postdata, function(status) {
			var status = new String(status);
			if(status.replace(/\s/g,"") != "") { 
			   alert(status); 
			} else { 
			   var svTahun = document.getElementById("sTahun").value;
			   var svCompany = document.getElementById("i_company").value;
			   jQuery("#list_period").setGridParam({url:url+"m_closing_control/search_period/"+svTahun+"/"+ svCompany }).trigger("reloadGrid"); 								
			   alert('data berhasil terupdate.');
			   initFormPeriod();        
			   $("#inputPeriod").dialog("close");
			};  
		});
}

function viewPeriod(ids){
	var ids = jQuery("#list_period").getGridParam('selrow'); 	
	var data = $("#list_period").getRowData(ids) ; 
	if (ids!=null ){
		initFormPeriod();
		$("#inputPeriod").dialog("open");
		$(":button:contains('Ubah Periode')").show();
		//$(":button:contains('Hapus Periode')").show();
		//$(":button:contains('Simpan Group')").hide();
		$("#i_periodid").val(data.PERIODE_ID);
		$("#i_periodname").val(data.PERIODE_NAME);
		$("#i_start").val(data.PERIODE_START);
		$("#i_end").val(data.PERIODE_END);
		var isActive = data.ISCLOSE;
		if (isActive==1) {
            $("#i_isClose").attr('checked',true);
        } else {
            $("#i_isClose").attr('checked',false);    
        }
		$("#i_closeBy").val(data.CLOSE_BY);
		$("#i_closeDate").val(data.CLOSE_DATE);
		$("#i_reopenBy").val(data.REOPEN_BY);
		$("#i_reopenDate").val(data.REOPEN_DATE);
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function viewPControl(ids){
	var ids = jQuery("#list_pControl").getGridParam('selrow'); 	
	var data = $("#list_pControl").getRowData(ids) ; 
	if (ids!=null ){
		initFormPeriod();
		$("#inputPControl").dialog("open");
		$(":button:contains('Ubah Periode')").show();		
		$("#i_pControlid").val(data.PERIODE_CONTROL_ID);
		$("#i_pControl").val(data.PERIODE_ID);
		$("#i_pControlname").val(data.PERIODE_NAME);
		$("#i_pControlStart").val(data.PERIODE_START);
		$("#i_pControlEnd").val(data.PERIODE_END);
		$("#i_pControlModule").val(data.MODULE);
		var isActive = data.ISCLOSE;
		if (isActive==1) {
            $("#i_pControlIsClose").attr('checked',true);
        } else {
            $("#i_pControlIsClose").attr('checked',false);    
        }
		$("#i_pControlCloseBy").val(data.CLOSE_BY);
		$("#i_pControlCloseDate").val(data.CLOSE_DATE);
		$("#i_pControlReopenBy").val(data.REOPEN_BY);
		$("#i_pControlReopenDate").val(data.REOPEN_DATE);
		
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function viewPControlDetail(ids){
	var ids = jQuery("#list_pControlDetail").getGridParam('selrow'); 	
	var data = $("#list_pControlDetail").getRowData(ids) ; 
	if (ids!=null ){
		initFormPeriod();
		$("#inputPControlDetail").dialog("open");	
		$("#i_pDetailControlDid").val(data.PERIODE_CONTROL_DETAIL_ID);
		$("#i_pDetailControlid").val(data.PERIODE_CONTROL_ID);
		$("#i_pDetailDate").val(data.PERIODE_DATE);
		$("#i_pDetailControlModule").val(data.MODULE);
		var isActive = data.ISCLOSE;
		if (isActive==1) {
            $("#i_pDetailControlIsClose").attr('checked',true);
        } else {
            $("#i_pDetailControlIsClose").attr('checked',false);    
        }
		$("#i_pDetailControlCloseBy").val(data.CLOSE_BY);
		$("#i_pDetailControlCloseDate").val(data.CLOSE_DATE);
		$("#i_pDetailControlReopenBy").val(data.REOPEN_BY);
		$("#i_pDetailControlReopenDate").val(data.REOPEN_DATE);
	} else {
		alert("harap pilih data untuk di edit");
	}                
}

function initFormPeriod(){
	 $("#i_groupid").val("") ; $("#i_groupname").val("") ;
}

/* Grid User Group Role*/
var jGrid_pControl = null;
var colNamesT_pControl = new Array();
var colModelT_pControl= new Array();
	
colNamesT_pControl.push('ID');
colModelT_pControl.push({name:'PERIODE_CONTROL_ID',index:'PERIODE_CONTROL_ID', hidden:true, width: 60, align:'center'});

colNamesT_pControl.push('PERIODE_ID');
colModelT_pControl.push({name:'PERIODE_ID',index:'PERIODE_ID', hidden:true, width: 60, align:'center'});

	
colNamesT_pControl.push('Period Name');
colModelT_pControl.push({name:'PERIODE_NAME',index:'PERIODE_NAME', editable: false, hidden:false, 
					  width: 100, align:'center'});
	
colNamesT_pControl.push('Start');
colModelT_pControl.push({name:'PERIODE_START',index:'PERIODE_START', hidden:false, width: 80, align:'center'});

colNamesT_pControl.push('End');
colModelT_pControl.push({name:'PERIODE_END',index:'PERIODE_END', editable: false, hidden:false, 
					  width: 80, align:'left'});
	
colNamesT_pControl.push('Module');
colModelT_pControl.push({name:'MODULE',index:'MODULE', hidden:false, width: 80, align:'left'});

colNamesT_pControl.push('Is Close');
colModelT_pControl.push({name:'ISCLOSE',index:'ISCLOSE', hidden:false, editable: true, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, width: 100, align:'center'});

colNamesT_pControl.push('Close By');
colModelT_pControl.push({name:'CLOSE_BY',index:'CLOSE_BY', editable: false, hidden:false, 
					  width: 80, align:'left'});

colNamesT_pControl.push('Close Date');
colModelT_pControl.push({name:'CLOSE_DATE',index:'CLOSE_DATE', editable: false, hidden:false, 
					  width: 80, align:'left'});

colNamesT_pControl.push('Reopen By');
colModelT_pControl.push({name:'REOPEN_BY',index:'REOPEN_BY', editable: false, hidden:false, 
					  width: 80, align:'left'});

colNamesT_pControl.push('Reopen Date');
colModelT_pControl.push({name:'REOPEN_DATE',index:'REOPEN_DATE', editable: false, hidden:false, 
					  width: 80, align:'left'});

colNamesT_pControl.push('Perusahaan');
colModelT_pControl.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, 
					  width: 80, align:'left'});

colNamesT_pControl.push('Status');
colModelT_pControl.push({name:'status',index:'status', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_pControl.push('Action');
colModelT_pControl.push({name:'act',index:'act', editable: false, hidden:false, width: 70, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_pControl = function(){
jGrid_pControl = jQuery("#list_pControl").jqGrid({
     url:url+'m_closing_control/search_pControl/xx/xx',
     mtype : "POST", datatype: "json",
     colNames: colNamesT_pControl , colModel: colModelT_pControl ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     caption: "Pengaturan periode tutup buku", rowNum:20, rowList:[10,20,30], multiple:true,
     height: 120, width: 950, cellEdit: false, cellsubmit: 'clientArray', forceFit : true,
     onSelectRow: function(){
		var id = jQuery("#list_pControl").getGridParam('selrow');
		var data = $("#list_pControl").getRowData(id) ;
		var idgroup = data.PERIODE_CONTROL_ID; 		
		var idcompany = data.COMPANY_CODE;
		$("#menulist_id").val(data.PERIODE_ID);
		jQuery("#list_pControlDetail").setGridParam({url:url+"m_closing_control/search_pControlDetail/"+idgroup+"/"+idcompany}).trigger("reloadGrid");	
	 }, loadComplete: function(){ 	 
                var ids = jQuery("#list_pControl").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    //ce = "<a href='#' onclick=\"viewPControl('"+cl+"');\"/ style='cursor:pointer'>Open/Close</a>";
                    jQuery("#list_pControl").setRowData(ids[i],{act:ce}) 
					var data = $("#list_pControl").getRowData(cl);
					if(data.ISCLOSE == '0'){
						ce = "<a href='#' onclick=\"viewPControl('"+cl+"');\"/ style='cursor:pointer'>Close</a>";
						de = "<span style='font:#00F'>Open</span>";
						jQuery("#list_pControl").setCell(ids[i], 'status', '', {'background-color':'#3FF' });
						jQuery("#list_pControl").setRowData(ids[i],{act:ce});
						jQuery("#list_pControl").setRowData(ids[i],{status:de}); 
					} else {
						ce = "<a href='#' onclick=\"viewPControl('"+cl+"');\"/ style='cursor:pointer'>Open</a>";
						de = "<span style='font:#F00'>Close</span>";
						jQuery("#list_pControl").setCell(ids[i], 'status', '', {'background-color':'#F60' });
						jQuery("#list_pControl").setRowData(ids[i],{act:ce});
						jQuery("#list_pControl").setRowData(ids[i],{status:de}); 
					}
                }
				$("#list_pControl").setSelection(ids);
            }, imgpath: gridimgpath, pager: jQuery('#pager_pControl'), sortname: colModelT_pControl[3].name
	  });
	  jGrid_pControl.navGrid('#pager_pControl',{edit:false,add:false,del:false, search: false, refresh: true});
	  
}
jQuery("#list_pControl").ready(loadView_pControl);

/* periode control detail */

var jGrid_pControlDetail = null;
var cNT_pControlDetail = new Array();
var cMT_pControlDetail = new Array();
	
cNT_pControlDetail.push('PERIODE_CONTROL_DETAIL_ID');
cMT_pControlDetail.push({name:'PERIODE_CONTROL_DETAIL_ID',index:'PERIODE_CONTROL_DETAIL_ID', hidden:true, width: 60, align:'center'});

cNT_pControlDetail.push('PERIODE_CONTROL_ID');
cMT_pControlDetail.push({name:'PERIODE_CONTROL_ID',index:'PERIODE_CONTROL_ID', hidden:true, width: 60, align:'center'});

	
cNT_pControlDetail.push('Tanggal');
cMT_pControlDetail.push({name:'PERIODE_DATE',index:'PERIODE_DATE', editable: false, hidden:false, 
					  width: 100, align:'center'});
	
cNT_pControlDetail.push('Modul');
cMT_pControlDetail.push({name:'MODULE',index:'MODULE', hidden:false, width: 80, align:'center'});

cNT_pControlDetail.push('Is Close');
cMT_pControlDetail.push({name:'ISCLOSE',index:'ISCLOSE', hidden:false, editable: true, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, width: 100, align:'center'});

cNT_pControlDetail.push('Close By');
cMT_pControlDetail.push({name:'CLOSE_BY',index:'CLOSE_BY', editable: false, hidden:false, 
					  width: 80, align:'left'});

cNT_pControlDetail.push('Close Date');
cMT_pControlDetail.push({name:'CLOSE_DATE',index:'CLOSE_DATE', editable: false, hidden:false, 
					  width: 80, align:'left'});

cNT_pControlDetail.push('Reopen By');
cMT_pControlDetail.push({name:'REOPEN_BY',index:'REOPEN_BY', editable: false, hidden:false, 
					  width: 80, align:'left'});

cNT_pControlDetail.push('Reopen Date');
cMT_pControlDetail.push({name:'REOPEN_DATE',index:'REOPEN_DATE', editable: false, hidden:false, 
					  width: 80, align:'left'});

cNT_pControlDetail.push('Status');
cMT_pControlDetail.push({name:'status',index:'status', editable: false, hidden:false, width: 80, align:'center'});

cNT_pControlDetail.push('Action');
cMT_pControlDetail.push({name:'act',index:'act', editable: false, hidden:false, width: 70, align:'center'});

var lastselDetail; var jdesc1Detail;
var lRowDetail; var lColDetail; var i = 0;
var loadView_pControlDetail = function(){
jGrid_pControlDetail = jQuery("#list_pControlDetail").jqGrid({
     url:url+'m_closing_control/search_pControlDetail/xx/xx',
     mtype : "POST", datatype: "json",
     colNames: cNT_pControlDetail , colModel: cMT_pControlDetail ,
     rownumbers:true, viewrecords: true, multiselect: false, 
     rowNum:20, rowList:[10,20,30], multiple:true,
     height: 120, width: 950, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
     loadComplete: function(){ 	 
                var ids = jQuery("#list_pControlDetail").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    //ce = "<a href='#' onclick=\"viewPControlDetail('"+cl+"');\"/ style='cursor:pointer'>Open/Close</a>";
                    jQuery("#list_pControlDetail").setRowData(ids[i],{act:ce}) 
					var data = $("#list_pControlDetail").getRowData(cl);
					if(data.ISCLOSE == '0'){
						ce = "<a href='#' onclick=\"viewPControlDetail('"+cl+"');\"/ style='cursor:pointer'>Close</a>";
						de = "<span style='font:#00F'>Open</span>";
						jQuery("#list_pControlDetail").setCell(ids[i], 'status', '', {'background-color':'#3FF' });
						jQuery("#list_pControlDetail").setRowData(ids[i],{act:ce});
						jQuery("#list_pControlDetail").setRowData(ids[i],{status:de}); 
					} else {
						ce = "<a href='#' onclick=\"viewPControlDetail('"+cl+"');\"/ style='cursor:pointer'>Open</a>";
						de = "<span style='font:#F00'>Close</span>";
						jQuery("#list_pControlDetail").setCell(ids[i], 'status', '', {'background-color':'#F60' });
						jQuery("#list_pControlDetail").setRowData(ids[i],{act:ce});
						jQuery("#list_pControlDetail").setRowData(ids[i],{status:de}); 
					}
                }
				
            }, imgpath: gridimgpath, pager: jQuery('#pager_pControlDetail'), sortname: cMT_pControlDetail[2].name
	  });
	  jGrid_pControlDetail.navGrid('#pager_pControlDetail',{edit:false,add:false,del:false, search: false, refresh: true});
	  
}
jQuery("#list_pControlDetail").ready(loadView_pControlDetail);

/* end periode control detial */
</script>

<div style="min-height:520px;">

    <div id="tabs" style="width:95%; height:500px;">
        <ul>
            <li><a href="#tabs-1">Periode Tutup Buku</a></li>
            <li><a href="#tabs-2">Pengaturan Tutup Buku</a></li>
        </ul>
        <div id="tabs-1">
        	<p><?php if(isset($company)){ echo "Perusahaan : " . $company; }?> <span style="padding-left:15px;">&nbsp;</span> 
            Periode : <?php if(isset($speriode)){ echo $speriode; }?></p>
            <p> 
                <div id="period">
                   <table id="list_period" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_period" class="scroll"></div>
                </div>
            </p>
        </div>
        <div id="tabs-2">
        	Modul : &nbsp; <select id="sModule" class="select" style="width:200px; height:25px;">
            <option value=""> -- pilih -- </option>
                <option value="LHM"> Laporan Harian Mandor </option>
                <option value="PRG"> Progress Kerja </option>
                <option value="BK"> Buku Kendaraan </option>
                <option value="BKT"> Buku Kontraktor </option>
                <option value="BM"> Buku Mesin </option>
                <option value="NAB"> Nota Angkut Buah </option>
            </select>
            <p>           
                <div id="pControl"></div>	
                   <table id="list_pControl" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_pControl" class="scroll"></div> 
                   
                   <br/>
                	<table id="list_pControlDetail" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                   <div id="pager_pControlDetail" class="scroll"></div> 
                </div>
            	
            </p>
             
        </div>
    </div>

</div>
<div id="inputPeriod" style="padding-top:15px; margin-left:-10px;">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
    <tr><td>ID Period</td><td>:</td><td><input class="input" type="text" size=25 id="i_periodid" disabled="disabled" /></td></tr>
    <tr><td>Period Name</td><td>:</td><td><input class="input"  type="text" size=25 id="i_periodname" disabled="disabled" /></td></tr>
    <tr><td>Start</td><td>:</td><td><input class="input"  type="text" size=25 id="i_start" disabled="disabled" /></td></tr>
    <tr><td>End</td><td>:</td><td><input class="input"  type="text" size=25 id="i_end" disabled="disabled"/></td></tr>
    <tr><td>Is Closed</td><td>:</td><td><input type="checkbox" id="i_isClose" name="i_isClose" class="input" tabindex="3" maxlength="50"/></td></tr>
    <tr><td>Closed By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_closeBy" disabled="disabled"/></td></tr>
    <tr><td>Closed Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_closeDate" disabled="disabled"/></td></tr>
    <tr><td>Reopen By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_reopenBy" disabled="disabled" /></td></tr>
    <tr><td>Reopen Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_reopenDate" disabled="disabled"/></td></tr>
</table>
</div>

<div id="inputPControl">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
	<tr><td>ID Period Control</td><td>:</td><td><input class="input" type="text" size=25 id="i_pControlid" disabled="disabled" /></td></tr>
    <tr><td>ID Period</td><td>:</td><td><input class="input" type="text" size=25 id="i_pControl" disabled="disabled" /></td></tr>
    <tr><td>Period Name</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlname" disabled="disabled"/></td></tr>
    <tr><td>Start</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlStart" /></td></tr>
    <tr><td>End</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlEnd" /></td></tr>
    <tr><td>Module</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlModule" disabled="disabled"/></td></tr>
    <tr><td>Is Closed</td><td>:</td><td><input type="checkbox" id="i_pControlIsClose" name="i_pControlIsClose" class="input" tabindex="3" maxlength="50"/></td></tr>
    <tr><td>Closed By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlCloseBy" disabled="disabled"/></td></tr>
    <tr><td>Closed Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlCloseDate" disabled="disabled"/></td></tr>
    <tr><td>Reopen By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlReopenBy" disabled="disabled"/></td></tr>
    <tr><td>Reopen Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pControlReopenDate" disabled="disabled"/></td></tr>
</table>
</div>

<div id="inputPControlDetail">
<table cellpadding="0" cellspacing="1" border="0" width="100%" class="teks_">
	<tr><td>ID Period Control Detail</td><td>:</td><td><input class="input" type="text" size=25 id="i_pDetailControlDid" disabled="disabled" /></td></tr>
    <tr><td>ID Period Control</td><td>:</td><td><input class="input" type="text" size=25 id="i_pDetailControlid" disabled="disabled" /></td></tr>
   <tr><td>Tanggal Transaksi</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailDate" disabled="disabled" /></td></tr>
    <tr><td>Module</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailControlModule" disabled="disabled" /></td></tr>
    <tr><td>Is Closed</td><td>:</td><td><input type="checkbox" id="i_pDetailControlIsClose" name="i_pDetailControlIsClose" class="input" tabindex="3" maxlength="50"/></td></tr>
    <tr><td>Closed By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailControlCloseBy"disabled="disabled" /></td></tr>
    <tr><td>Closed Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailControlCloseDate" disabled="disabled" /></td></tr>
    <tr><td>Reopen By</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailControlReopenBy" disabled="disabled" /></td></tr>
    <tr><td>Reopen Date</td><td>:</td><td><input class="input"  type="text" size=25 id="i_pDetailControlReopenDate" disabled="disabled" /></td></tr>
</table>
</div>