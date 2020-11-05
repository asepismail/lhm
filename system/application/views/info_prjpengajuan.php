<? $template_path = base_url().$this->config->item('template_path'); ?>
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
var url="<?= base_url().'index.php/prj_pengajuan/' ?>";
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
	
	$("#i_prjtype").change(function() {
        var product = $(this).val();
        if (product != 0) {
			  $("#i_prjsubtype").empty();
			  var cType = $("#i_prjsubtype").val();
			  if (cType==null) {  cType="-"; }
			  $.post(url+'/LoadChain/'+$("#i_prjtype").val()+'/'+cType+'/', 
			  $("#i_prjtype").val(),
				function(datapost) 
				{ 
					$("#i_prjsubtype").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
					for (var i=0; i<datapost.length; i++)
					{
						$("#i_prjsubtype").get(0).add(new Option(
						datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
					$("#i_prjsubtype").attr('style','display:inline; width:250px;');
						
				},"json");
				document.getElementById("i_act").value = '';
        } else {
            $("#i_prjsubtype").attr('style','display:none;');
        }
    });    
	
	$("#i_prjsubtype").change(function() {
        var product = $(this).val();
        if (product != 0) {
          $("#i_prjsubact").empty();
          var cType = $("#i_prjsubact").val();
          if (cType==null) { cType="-"; }
          $.post(url+'/LoadChain2/'+$("#i_prjsubtype").val()+'/'+cType+'/', 
          $("#i_prjsubtype").val(),
            function(datapost) { 
                $("#i_prjsubact").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++){
                    $("#i_prjsubact").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#i_prjsubact").attr('style','display:inline; width:250px;');
            },"json");
		  document.getElementById("i_act").value = document.getElementById("i_prjsubtype").value;
        } else {
            $("#i_prjsubtype").attr('style','display:none;');
        }
    });
	
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
                      be = "<input class='basicBtn' style='height:20px;width:60px; font-size:8px;' type='button' value='Aktivitas' onclick=\"DetailProject('"+cl+"')\" />"; 
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
	
function delData()
{
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Data Project : " + data.PROJECT_ID + ":" + data.PROJECT_DESC + "?" )
        if (answer)
        {
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses menghapus data...";
			$("#progressbar").dialog('open');
			
			$.post(url+'DelData/'+data.PROJECT_ID, '',
			function(message) {
					if(message.replace(/\s/g,"") != 0 ) { 
						$("#load").hide();
						document.getElementById('msg').innerHTML= message;
						$("#cok").attr("disabled", false);
				   } else { 
						$("#load").hide();
						$("#cok").attr("disabled", false);
						document.getElementById('msg').innerHTML= 'data berhasil terhapus';
						gridReload();
						$("#prj_form").dialog('close'); 
				   };  
			  } );
        }
    }
    else { alert("Pilih data project yang akan dihapus!"); }
}  

function delDataDetail()
{
    var ids = jQuery("#listDp").getGridParam('selrow'); 
    var dataDp = $("#listDp").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Aktivitas Project : " + dataDp.MASTER_PROJECT_ID + ":" + dataDp.PROJECT_ACTIVITY + "?" )
        if (answer)
        {
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses menghapus data...";
			$("#progressbar").dialog('open');
			
			$.post(url+'DelDataDetail/'+dataDp.MASTER_PROJECT_ID+'/'+dataDp.PROJECT_ACTIVITY, '',
			function(message) {
					if(message.replace(/\s/g,"") != 0 ) { 
						$("#load").hide();
						document.getElementById('msg').innerHTML= message;
						$("#cok").attr("disabled", false);
				   } else { 
						$("#load").hide();
						$("#cok").attr("disabled", false);
						document.getElementById('msg').innerHTML= 'data berhasil terhapus';
						gridReloadDetail();
			  			init_prj_dtl();
						$("#frmaktivitas").dialog('close');
				   };  
			  } );
        }
    }
    else { alert("Pilih data aktivitas yang akan dihapus!"); }
}  
 
function TambahData()
{
    init_prj();
    $("#i_prjid").removeAttr('disabled'); 
    $("#prj_form").dialog('open');
    $("#form_mode").val("POST"); 
}

function TambahDataDetail()
{
	$.post( url+"getProjectNum/"+$("#projectnum").val(), '', function(data) {
		var jdat = JSON.parse(data);
	 	for (var i = 0; i < jdat.length ; i++ ) {
			document.getElementById("i_prjiddet").value = jdat[i].code;
        };  
    });
    //init_prj_dtl();
    $("#i_prjiddet").attr('disabled',false); 
    $("#frmaktivitas").dialog('open');
    $("#form_mode_dtl").val("POST"); 
	
	
}

function DetailProject(project){
	$("#projectnum").val('');
	jQuery("#listDp").setGridParam({url:url+"LoadDetail/"+project}).trigger("reloadGrid");  
	$("#fdetailproject").dialog('open');	
	$("#projectnum").val(project);
}

function submit_detail(){
	var postdata={};
	var action = '';
    var mode = $("#form_mode").val();
	postdata['MASTER_PROJECT_ID'] = $("#i_prjiddet").val() ; 
    postdata['PROJECT_ACTIVITY'] = $("#i_actdet").val() ; 
  	
	$("#cok").attr("disabled", true);
	$("#load").show();
	document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
	$("#progressbar").dialog('open');
	$.post( url+"AddNewDetail/"+$("#i_prjid").val(), postdata, function(message) {
		 if(message.replace(/\s/g,"") != 0 ) { 
               $("#load").hide();
			   document.getElementById('msg').innerHTML= message;
			   $("#cok").attr("disabled", false);
         } else { 
			  $("#load").hide();
			  $("#cok").attr("disabled", false);
			  document.getElementById('msg').innerHTML= 'data tersimpan';
			  closewin();
			  gridReloadDetail();
			  init_prj_dtl(); 
			  $("#frmaktivitas").dialog('close');
			  TambahDataDetail();
          };  
      });
}

function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();

    postdata['PROJECT_ID'] = $("#i_prjid").val() ; 
    postdata['AFD'] = $("#i_afd").val() ; 
    postdata['PROJECT_TYPE'] = $("#i_prjtype").val() ; 
    postdata['PROJECT_SUBTYPE']= $("#i_prjsubtype").val();
	postdata['PROJECT_SUB_ACTIVITY']= $("#i_prjsubact").val();
    postdata['PROJECT_DESC'] = $("#i_prjdesc").val(); 
	postdata['PROJECT_LOCATION']= $("#i_loc").val();
	postdata['PROJECT_ACTIVITY']= $("#i_act").val();
	postdata['KODE_PELAKSANA']= $("#i_prjtypepelaksana").val();
	postdata['SPK']= $("#i_pk").val();
    postdata['PROJECT_START']=$("#i_start").val();
    postdata['PROJECT_END'] = $("#i_end").val(); 
	postdata['PROJECT_QTY']=$("#i_qty").val();
	postdata['PROJECT_UOM']=$("#i_uom").val();
	postdata['PROJECT_VALUE']=$("#i_val").val();
	postdata['PROJECT_PPN']=$("#i_ppn").val();
	postdata['PROJECT_NETTVAL']=$("#i_nett").val();
	var isActive = $("#i_active").is(':checked');
    if(isActive==true) {
        isActive=1;
    } else {
        isActive=0;
    }
	
	postdata['PROJECT_STATUS']= isActive;
    postdata['INACTIVE']=isActive;
    postdata['TGL_TERBIT'] = $("#i_terbit").val();   
    
    if (mode == "GET") {
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
		$("#progressbar").dialog('open');
		
        $.post( url+"/EditData/"+$("#i_prjid").val(), 
        postdata,
        function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
                  	$("#load").hide();
				  	document.getElementById('msg').innerHTML= message;
				  	$("#cok").attr("disabled", false);
               } else { 
				  	$("#load").hide();
				  	$("#cok").attr("disabled", false);
				  	document.getElementById('msg').innerHTML= 'data tersimpan';
				  	gridReload();
                	$("#prj_form").dialog('close'); 
               };  
         });
    } else if (mode == "POST") {
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
		$("#progressbar").dialog('open');
        $.post(  url+"AddNew/", postdata,
        function(message) { 
            if(message.replace(/\s/g,"") != 0 ) { 
                  	$("#load").hide();
				  	document.getElementById('msg').innerHTML= message;
				  	$("#cok").attr("disabled", false);
               } else { 
				  	$("#load").hide();
				  	$("#cok").attr("disabled", false);
				  	document.getElementById('msg').innerHTML= 'data tersimpan';
				  	gridReload();
                	$("#prj_form").dialog('close'); 
               };   
        });     
    }
}

function Edit()
{ 
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (null==ids || ''==ids)
    {
        alert ("harap pilih data!!!");
    }
    else
    {
        init_prj();
        
		$("#i_prjid").val(data.PROJECT_ID); 
        $("#i_afd").val(data.AFD); 
        $("#i_prjtype").val(data.PROJECT_TYPE); 
		$.post(url+'/LoadChain/'+$("#i_prjtype").val(), 
          $("#i_prjtype").val(),
            function(datapost) 
            { 
				$('#i_prjsubtype').empty();
                $("#i_prjsubtype").get(0).add(new Option(
                            " -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++)
                {
                    $("#i_prjsubtype").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#i_prjsubtype").attr('style','display:inline; width:250px;'); 
				$("#i_prjsubtype").val(data.PROJECT_SUBTYPE);
            },"json");
		document.getElementById("i_prjsubact").value = '';
		$.post(url+'/LoadChain2/'+data.PROJECT_SUBTYPE, 
          $("#i_prjsubtype").val(),
            function(datapost) 
            { 
				$('#i_prjsubact').empty();
                $("#i_prjsubact").get(0).add(new Option(
                            " -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++)
                {
                    $("#i_prjsubact").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#i_prjsubact").attr('style','display:inline; width:250px;');
				$("#i_prjsubact").val(data.PROJECT_SUB_ACTIVITY);                     
            },"json");
			
        $("#i_prjdesc").val(data.PROJECT_DESC); 
		$("#i_loc").val(data.PROJECT_LOCATION); 
		$("#i_act").val(data.PROJECT_ACTIVITY); 
		$("#i_prjtypepelaksana").val(data.KODE_PELAKSANA); 
		$("#i_pk").val(data.SPK); 
        $("#i_start").val(data.PROJECT_START);
        $("#i_end").val(data.PROJECT_END); 
		$("#i_qty").val(data.PROJECT_QTY); 
		document.getElementById("i_uom").value = data.PROJECT_UOM;
		$("#i_val").val(data.PROJECT_VALUE); 
		$("#i_ppn").val(data.PROJECT_PPN); 
		$("#i_nett").val(data.PROJECT_NETTVAL); 
		$("#i_terbit").val(data.TGL_TERBIT); 
		var isActive = data.PROJECT_STATUS;
        if (isActive==1) {
            $("#i_active").attr('checked',true);
        } else {
            $("#i_active").attr('checked',false);    
        }
        $("#i_prjid").attr('disabled','disabled');
        
        $("#prj_form").dialog('open'); 
        $("#form_mode").val("GET");      
    }
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
    if (desc == ""){ subtype = "-"; }

    jQuery("#list").setGridParam
    ({url:url+"SearchData/"+id+"/"+afd+"/"+type+"/"+desc}).trigger("reloadGrid");        
}
//########################### END SEARCH FUNCTION #############################    
</script>

<div id"gridSearch">
    <div class="teks_"></div>  
    <div>
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr>
                <tr><td colspan="8"><p>Pencarian Berdasarkan :</p><br/>
          </td></tr>
                <td width="80px">No Project </td>
                <td>:</td>
                <td width="120px">
                <input type="text" class="input" id="search_id" maxlength="25" style="width:80px" onkeydown="doSearch(arguments[0]||event)" />
                </td>
                <td width="40px">Afd</td>
                <td>:</td>
                <td width="140px">
               <!--  <input type="text" class="input" id="search_afd" maxlength="5" onkeydown="doSearch(arguments[0]||event)" /> -->
               			<? if(isset($safd)){ echo $safd; } ?>
                </td>
                <td width="80px">Type</td>
                <td>:</td>
                <td width="120px">
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
        </table><br/>
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
<input type="button"  id="add" value="Tambah" class='basicBtn' onclick="TambahData()">
<input type="button"  id="edit" value="Ubah" class='basicBtn' onclick="Edit()">
<input type="button"  id="delete" value="Hapus" class='basicBtn' onclick="delData()">
</div>

<div id="prj_form">
    <table width="100%" class="teks_">
        <tr>
                <tr>
                    <td align="left" width="125">Nomor Project</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_prjid" class="input" tabindex="1" maxlength="15"/></td>
                </tr>
                <tr>
                    <td align="left">Estate / Afd</td>
                    <td align="left">:</td>
                    <td><? if(isset($afd)) { echo $afd; } ?></td>
                </tr>
                <tr>
                    <td align="left">Tipe Project</td>
                    <td align="left">:</td>
                    <td>
                    <select name='i_prjtype' class='select' id="i_prjtype" tabindex="6" style="width:160px">
                        <option value=""> -- pilih -- </option>
                          <option value="IF">Infrastuktur</option>
                          <option value="OP">Oil Palm (OP)</option>
                          <option value="NS">Persiapan Bibitan</option>
                          <option value="PB">Pabrik Kelapa Sawit</option>
                    </select></td>
                </tr>
                <tr>
                    <td align="left">Subtipe Project</td>
                    <td align="left">:</td>
                    <td>
                    	<select tabindex="3" name='i_prjsubtype' class='select' id="i_prjsubtype" style="width:250px;">
						</select>
                    </td>
                </tr>
                <tr>
                    <td align="left">Sub Aktivitas Project</td>
                    <td align="left">:</td>
                    <td>
                    	<select tabindex="3" name='i_prjsubact' class='select' id="i_prjsubact" style="width:250px;">
						</select>
                    </td>
                </tr>
                <tr>
                    <td align="left">Deskripsi</td>
                    <td align="left">:</td>
                    <td>
                    <textarea rows="5" cols="25" id="i_prjdesc" style="height:50px; width:200px;" class="input" tabindex="5" maxlength="200"></textarea> 
                    </td>
                </tr>
                
                <tr>
                    <td align="left">Kode Lokasi</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_loc" class="input" tabindex="6" maxlength="50" style="width:200px"/></td>
                </tr>
                
                <tr>
                    <td align="left">Kode Aktivitas</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_act" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                <tr>
                    <td align="left">Pelaksana</td>
                    <td align="left">:</td>
                    <td>
                    <select name='i_prjtypepelaksana' class='select' id="i_prjtypepelaksana" tabindex="6" style="width:160px">
                        	<option value=""> -- pilih -- </option>
                          	<option value="swakelola">Swakelola</option>
                          	<option value="kontraktor">Kontraktor</option>
                    </select></td>
                    
                    </td>
                </tr>
                
                <tr>
                    <td align="left">No. PK</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_pk" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                <tr>
                    <td align="left">Mulai Project</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_start" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">Selesai Project</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_end" class="input" tabindex="7" maxlength="50"/></td>
                </tr>
                
                 <tr>
                    <td align="left">Qty</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_qty" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                <tr>
                    <td align="left">Satuan</td>
                    <td align="left">:</td>
                    <td><select name='i_uom' class='select' id="i_uom" tabindex="6" style="width:160px">
                                <option value=""> -- pilih -- </option>
                                <option value="UNIT">Unit</option>
                                <option value="M">Meter</option>
                                <option value="HA">Hektar</option>
                        </select>
                    </td>
                </tr>
				
                 <tr>
                    <td align="left">Nilai</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_val" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                 <tr>
                    <td align="left">PPN</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_ppn" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                 <tr>
                    <td align="left">Nilai Nett</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_nett" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                 <tr>
                    <td align="left">Tgl Terbit</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_terbit" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
                <tr>
                    <td align="left">Aktif</td>
                    <td align="left">:</td>
                    <td><input type="checkbox" id="i_active" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                
               
            <td colspan="5">
                <input type="hidden" id="form_mode">
            </td>
        </tr>
    </table>
</div>

<div id="frmaktivitas">
		 <table width="100%" class="teks_">
                <tr>
                    <td align="left" width="125">Nomor Project</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_prjiddet" name="i_prjiddet" class="input" tabindex="1" maxlength="15" /></td>
                </tr>
                <tr>
                    <td align="left">Kode Aktivitas</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_actdet" name="i_actdet" class="input" tabindex="1" maxlength="15" /></td>
                </tr>
                 <tr>
                    <td align="left">Deskripsi Aktivitas</td>
                    <td align="left">:</td>
                    <td>
                    <textarea tabindex="19" style="height:45px; width:200px;" class="input" id="i_actdesc" name="i_actdesc"></textarea>
                    <input type="hidden" id="projectnum"/> 
                    <input type="hidden" id="form_mode_dtl"></td>
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
</body>
