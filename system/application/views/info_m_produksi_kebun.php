<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/ajaxfileupload.js"></script>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <fieldset id="filters">
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td class="labelcell">:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr>
            <tr>
                <td class="labelcell">AFD</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="2" class="input" type="text" id="txt_afdSrc" name="knt_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td> 
            </tr>
            <tr>
                <td class="labelcell">BLOCK</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="2" class="input" type="hidden" id="txt_blockSrc" name="knt_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td> 
            </tr>
        </table>  
         </fieldset>     
    </div>

    <div id='transaction_frame'>
    
        <div id="mainGrid">
        	<fieldset id="grid">
                <table>
                    <tr>
                        <td>LHM<table id="list_produksi_kebun" class="scroll"></table>
                        GKM<table id="list_produksi_kebun_gkm" class="scroll"></table> </td>
                        <td>ADEMPIERE<table id="list_produksi_kebun_adem" class="scroll"></table></td>
                    </tr>           
                </table>            
            </fieldset>
            <!-- 
            <div id="pager_produksi_kebun" class="scroll" style="text-align:center;"></div>
            <div id="pager_produksi_kebun_adem" class="scroll" style="text-align:center;"></div>
            -->
        </div>
        
        <div id="btn_section">
           <button class="testBtn" type="submit" id="delete_all" onclick="sinkron()">Sinkron LHM ke Adempiere</button> 
        </div>
    </div>
</div> 

<div id="frm_produksi_kebun">
    <div id="tabs">
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_produksi_kebun_set">

    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
        <tr><td colspan="8">Pilih Periode :</td></tr>
        <tr>
            <td>Periode</td><td>:</td><td><? echo $periode_produksi_kebun; ?></td>
        </tr> 
    </table>
    
</div>
<div id="frm_load">
    Wait...
</div>

<script type="text/javascript">
var that = this;
$(function(){
    $("#txt_afdSrc").val('');
    //$("#txt_blockSrc").val('');     
});

jQuery(document).ready(function(){
    $('#tahun').change(function() {
        reloadGrid();
		reloadGridAdem();
		reloadGridGKM();
    });
    
    $('#bulan').change(function() {
        reloadGrid(); 
		reloadGridAdem();
		reloadGridGKM();
    });
});

jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
   
   $('input').val('');
    
   $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
   });
   
   $("#search_form").dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center',
        onShow: function(dlg){
            $(dlg.container).css('height','auto')
        },
        close: function(event, ui){
            $(this).dialog('destroy');
            setDialogWindows('#search_form');
        },
        open:function(){
            jQuery("#list_produksi_kebun").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_produksi_kebun/search_data_2');
        }
    });
   
   $(function() {
		 $("#myForm").dialog({
			bgiframe: true, autoOpen: false, height: 300, width: 550,
			modal: true, title: "Upload data Hama Penyakit Tanaman", resizable: false, 
			moveable: true, closeOnEscape: false,
			open: function() { $(".ui-dialog-titlebar-close").hide(); },
			buttons: { 'Tutup': function() {
						  $("#myForm").dialog("close");     
				   }
			   } 
		 }); 
	});
   
   jQuery( "#bUpload" ).click(function() {
		  startUpload();
	  });
   
   $("#frm_produksi_kebun_set").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 150,
        width: 300,
        modal: true,
        title: "Penetapan Periode Hama Penyakit Tanaman",
        resizable: true,
        moveable: true,
        buttons: {
            'Set Periode': function() 
                    {
                        set_produksi_kebun_periode_do();        
                    }
           
        } 
    });
    
   $("#frm_produksi_kebun").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Hama Penyakit Tanaman",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        clear_form_elements(this.form);        
                    }
            
        } 
    });
    
    $("#txt_afdSrc").autocomplete( 
            url+"m_produksi_kebun/get_afdeling", {
              dataType: 'ajax',
              width:350,
              multiple: false,
              limit:20,
              parse: function(data) { // parsing json input
                  return $.map(eval(data), function(row) {
                  return (typeof(row) == 'object')
                    ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                    : { data: row, value: '',result: ''};
                });
              },
              formatItem: function(item) {
                return (typeof(item) == 'object')?item.res_dl :'';
              }
            }
    ).result(function(e, item){
    });
    
    $("#txt_produksi_kebunAFD").autocomplete( 
            url+"m_produksi_kebun/get_afdeling", {
              dataType: 'ajax',
              width:350,
              multiple: false,
              limit:20,
              parse: function(data) { // parsing json input
                  return $.map(eval(data), function(row) {
                  return (typeof(row) == 'object')
                    ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                    : { data: row, value: '',result: ''};
                });
              },
              formatItem: function(item) {
                return (typeof(item) == 'object')?item.res_dl :'';
              }
            }
    ).result(function(e, item){

    });
    
    $("#txt_produksi_kebunBlock").keydown(function(){
        var AFD = $("#txt_produksi_kebunAFD").val();
        $("#txt_produksi_kebunBlock").autocomplete( 
                url+"m_produksi_kebun/get_block/"+AFD, {
                  dataType: 'ajax',
                  width:350,
                  multiple: false,
                  limit:20,
                  parse: function(data) { // parsing json input
                      return $.map(eval(data), function(row) {
                      return (typeof(row) == 'object')
                        ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                        : { data: row, value: '',result: ''};
                    });
                  },
                  formatItem: function(item) {
                    return (typeof(item) == 'object')?item.res_dl :'';
                  }
                }
            )
    });
    
    
     
});
</script>
 
<script type="text/javascript">
function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_produksi_kebun").setGridParam({url:url+'m_produksi_kebun/LoadData/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
}

function reloadGridAdem(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_produksi_kebun_adem").setGridParam({url:url+'m_produksi_kebun/LoadData_Adem/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
}

function reloadGridGKM(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_produksi_kebun_gkm").setGridParam({url:url+'m_produksi_kebun/LoadDataGKM/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
}
</script>

<script type="text/javascript">  
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';

colNamesT.push('PT');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 30, align:'left'});

colNamesT.push('AFD');
colModelT.push({name:'AFDELING',index:'AFDELING', editable: false, hidden:false, width: 70, align:'left'});

colNamesT.push('PRODUKSI TM');
colModelT.push({name:'PRODUKSI_TM',index:'PRODUKSI_TM', editable: false, hidden:false, width: 70, align:'right'});

colNamesT.push('PRODUKSI TBM');
colModelT.push({name:'PRODUKSI_TBM',index:'PRODUKSI_TBM', editable: false, hidden:false, width: 70, align:'right'});

colNamesT.push('PERIODE');
colModelT.push({name:'dateacct',index:'dateacct', editable: false, hidden:false, width: 75, align:'center', formatter:'date:yyyy-mm-dd'}); 

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_produksi_kebun").jqGrid(
            {
                url:url+'m_produksi_kebun/LoadData/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				//multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[0].name,
                pager:jQuery("#pager_produksi_kebun"),
                rowNum: 100,
                rownumbers: true,
                height: 500,
                width: 600,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_produksi_kebun").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/arrow_refresh.png' width='12px' height='13px' onclick=\"sinkron_percompany('"+cl+"');\" />"; 
                            jQuery("#list_produksi_kebun").setRowData(ids[i],{act:be}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_produksi_kebun',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_produksi_kebun',{
               caption:"Cetak Excel", 
               buttonicon:"ui-icon-print", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         
         }
jQuery("#list_produksi_kebun").ready(loadView); 

</script>

<script type="text/javascript">  
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesTG = new Array();
var colModelTG = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';

colNamesTG.push('PT');
colModelTG.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 30, align:'left'});

colNamesTG.push('AFD');
colModelTG.push({name:'AFDELING',index:'AFDELING', editable: false, hidden:false, width: 70, align:'left'});

colNamesTG.push('PRODUKSI TM');
colModelTG.push({name:'PRODUKSI_TM',index:'PRODUKSI_TM', editable: false, hidden:false, width: 70, align:'right'});

colNamesTG.push('PRODUKSI TBM');
colModelTG.push({name:'PRODUKSI_TBM',index:'PRODUKSI_TBM', editable: false, hidden:false, width: 70, align:'right'});

colNamesTG.push('PERIODE');
colModelTG.push({name:'dateacct',index:'dateacct', editable: false, hidden:false, width: 75, align:'center', formatter:'date:yyyy-mm-dd'}); 

colNamesTG.push('');
colModelTG.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_produksi_kebun_gkm").jqGrid(
            {
                url:url+'m_produksi_kebun/LoadDataGKM/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				//multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesTG ,
                colModel: colModelTG,
                sortname: colModelTG[0].name,
                pager:jQuery("#pager_produksi_kebun_gkm"),
                rowNum: 100,
                rownumbers: true,
                height: 300,
                width: 600,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids_gkm = jQuery("#list_produksi_kebun_gkm").getDataIDs();
                    for(var i=0;i<ids_gkm.length;i++)
                        { 
                            var cl = ids_gkm[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/arrow_refresh.png' width='12px' height='13px' onclick=\"sinkron_percompany('"+cl+"');\" />"; 

                            jQuery("#list_produksi_kebun_gkm").setRowData(ids_gkm[i],{act:be}) 							
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_produksi_kebun_gkm',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_produksi_kebun_gkm',{
               caption:"Cetak Excel", 
               buttonicon:"ui-icon-print", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         
         }
jQuery("#list_produksi_kebun_gkm").ready(loadView); 

</script>

<script type="text/javascript">  
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesTA = new Array();
var colModelTA = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';

colNamesTA.push('ORGANISASI');
colModelTA.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 30, align:'left'});

colNamesTA.push('AFD');
colModelTA.push({name:'AFDELING',index:'AFDELING', editable: false, hidden:false, width: 70, align:'left'});

colNamesTA.push('c_bpartner_id');
colModelTA.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:false, width: 70, align:'left'});

colNamesTA.push('PRODUKSI TM');
colModelTA.push({name:'PRODUKSI_TM',index:'PRODUKSI_TM', editable: false, hidden:false, width: 70, align:'right'});

colNamesTA.push('PRODUKSI TBM');
colModelTA.push({name:'PRODUKSI_TBM',index:'PRODUKSI_TBM', editable: false, hidden:false, width: 70, align:'right'});

colNamesTA.push('DATEACCT');
colModelTA.push({name:'dateacct',index:'dateacct', editable: false, hidden:false, width: 75, align:'center', formatter:'date:yyyy-mm-dd'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_produksi_kebun_adem").jqGrid(
            {
                url:url+'m_produksi_kebun/LoadData_Adem/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				//multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesTA ,
                colModel: colModelTA,
                sortname: colModelTA[1].name,
                pager:jQuery("#pager_produksi_kebun_adem"),
                rowNum: 100,
                rownumbers: true,
                height: 800,
                width: 600,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_produksi_kebun_adem").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

                            jQuery("#list_produksi_kebun_adem").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_produksi_kebun_adem',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_produksi_kebun_adem',{
               caption:"Cetak Excel", 
               buttonicon:"ui-icon-print", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         
         }
jQuery("#list_produksi_kebun_adem").ready(loadView); 

</script>
 

<script type="text/javascript">
function OpenUploadForm(){
	$("#progress").hide();
	$("#myForm").dialog("open");
}
  
function get_bulan(){
    var lPeriode = $("#bulan").val();
    return lPeriode;    
}

function get_tahun(){
    var lPeriode = $("#tahun").val();
    return lPeriode;    
}
</script>

<script type="text/javascript">
function sinkron(){

	var postdata_id = {};
	postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();

		var answer = confirm  ("Yakin anda akan melakukan sinkron data produksi kebun dari LHM ke Adempiere?");
		$("#frm_load").dialog('open');
		if (answer){
			var data = {id:postdata_id};
        	data = JSON.stringify(data);
        	$.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_produksi_kebun/sinkron',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);                   
                        
                        alert(obj.status)
                        reloadGrid();
						reloadGridAdem();
						reloadGridGKM();
                        $("#frm_load").dialog('close');
                        //$("#frm_produksi_kebun").dialog('close');        
                    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                }    
			});
		}// end answer 
	
}
function sinkron_percompany(cl){	
    var postdata_id = {};
	postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
	postdata_id['COMPANY'] = cl;

		var answer = confirm  ("Yakin anda akan melakukan sinkron data produksi kebun PT. "+cl+" dari LHM ke Adempiere?");
		$("#frm_load").dialog('open');
		if (answer){
			var data = {id:postdata_id};
        	data = JSON.stringify(data);
        	$.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_produksi_kebun/sinkron_percompany',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);                   
                        
                        alert(obj.status)
                        reloadGrid();
						reloadGridAdem();
						//reloadGridGKM();
                        $("#frm_load").dialog('close');
                        //$("#frm_produksi_kebun").dialog('close');        
                    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                }    
			});
		}// end answer 
		
}
</script>

<script type="text/javascript">

function gridReload(){ 
    var afd = jQuery("#txt_afdSrc").val();
    var block = jQuery("#txt_blockSrc").val();  

    if (afd == ""){
        afd = "-";
    }
    if (block == ""){
        block = "-";
    } 

    jQuery("#list_produksi_kebun").setGridParam({url:url+"m_produksi_kebun/search_data/"+get_bulan()+"/"+get_tahun()+"/"+afd+"/"+block}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">

function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}

function set_produksi_kebun_periode(){
    clear_form_elements(this.form)
    $("#frm_produksi_kebun_set").dialog('open');    
}

function print_all(){
	var answer = confirm ("Anda ingin mencetak excel data Hama Penyakit Tanaman per blok tanah periode : " + get_periode() + " ?" );
	if (answer){

	var postdata_id = {};
    var data = {
					id:postdata_id,
               };
        data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_produksi_kebun/xls_month/'+ get_periode(),
                    data:           {myJson:  data} ,
                    success: function(msg){
						var win=window.open(url+'m_produksi_kebun/xls_month/'+ get_periode(), '_blank');
  						win.focus();
                    }
            });        
        
    }             
}

</script>