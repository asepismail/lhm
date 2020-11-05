<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_po" class="scroll"></table> 
            <div id="pager_po" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="open_poadem()">Sinkron PO Adempiere</button>&nbsp;
    </div>
</div> 

<div id="frm_po">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail PO</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">ID PO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_poID" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Nomor PO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 175px;" tabindex="2" type="text" id="txt_poNumber" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Kode Supplier</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="3" type="text" id="txt_poSuplierCode" name="po_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Qty Ordered</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="4" type="text" id="txt_poQtyOrdered" name="po_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Price List</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="5" type="text" id="txt_poPriceList" name="po_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tanggal Mulai</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_poDateStart" name="po_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tanggal Selesai</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_poDateEnd" name="po_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Keterangan</td><td>:</td>
                    <td class="fieldcell"><textarea disabled="disabled" tabindex="8" style="height:65px; width:300px;" class="input" id="txt_poDescription" name="po_input"></textarea></td>
                </tr>
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_savePO" onclick="update_po()">Simpan</button>    
        </div>
        
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_poadem">
    <div id="tabspo">
        <ul class="tabspo">
            <li><a href="#fragment-1"><span>Daftar PO Adempiere</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            
            <div id="poademGrid">
                <table id="list_poadem" class="scroll"></table> 
                <div id="pager_poadem" class="scroll" style="text-align:center;"></div>
            </div>
                
        </div>
        <div id="fragment-2">
        </div>
        <div id="fragment-3">
            
        </div>
    </div>
    <input type="hidden" id="txt_frmMode">
</div>

<div id="frm_load">
    Wait...
</div>

<div id="search_form"></div>
</body>

<script type="text/javascript">
$(document).ready(function() {
    $('input').val('');
});

function reloadGrid(){   
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_po").setGridParam({url:url+'s_po_tbs/LoadData/'}).trigger("reloadGrid");    
}

$(function() {
    //################# DATE PICKER SETTING #####################
    $("#txt_poDateStart").datepicker({dateFormat:"yy-mm-dd"});
    $("#txt_poDateEnd").datepicker({dateFormat:"yy-mm-dd"});
});
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
  
$(document).ready(function() {
    var $tabspo = $('#tabspo').tabs();
    var selected = $tabspo.tabs('option', 'selected'); // => 0
    $("#tabspo").tabs();
  });
</script>
   
<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_ANON');
colModelT.push({name:'ID_ANON',index:'ID_ANON', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('Nomor PO');
colModelT.push({name:'PO_NUMBER',index:'PO_NUMBER', editable: true, hidden:false, width: 150, align:'center'});

colNamesT.push('Kode Supplier');
colModelT.push({name:'SUPPLIERCODE',index:'SUPPLIERCODE', editable: true,width: 100
,async: false,edittype: "text",editoptions:{
                size:100,
                maxlength:50,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"s_po_tbs/get_supplier", {
                      dataType: 'ajax',
                      multiple: false,
                      autoFill: false,
                      mustMatch: true,
                      matchContains: false,
                
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
                  .result(function(e, item) {
                    $("#SUPPLIERNAME").val(item.res_name );
                    var id = jQuery("#list_po").getGridParam('selrow');
                    
                    if (id){ 
                        var ret = jQuery("#list_po").getRowData(id);
                        ret.SUPPLIERNAME = (item.res_name);
                        jQuery("#list_po").setRowData(id,{SUPPLIERNAME:ret.SUPPLIERNAME});
                    }
                  });
          }}, align:'center'});
          
colNamesT.push('Nama Supplier');
colModelT.push({name:'SUPPLIERNAME',index:'SUPPLIERNAME', editable: false, hidden:false, width: 175, align:'left'});

colNamesT.push('Qty Ordered');
colModelT.push({name:'QTYORDERED',index:'QTYORDERED', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('Price List');
colModelT.push({name:'PRICELIST',index:'PRICELIST', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('Tanggal Mulai');
colModelT.push({name:'TANGGALM',index:'TANGGALM', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('Tanggal Selesai');
colModelT.push({name:'TANGGALK',index:'TANGGALK', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('Desc');
colModelT.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('SinkronStatus');
colModelT.push({name:'SINKRON_STATUS',index:'SINKRON_STATUS', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('cb');
colModelT.push({name:'C_BPARTNER_ID',index:'C_BPARTNER_ID', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_po").jqGrid(
            {
                url:url+'s_po_tbs/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[7].name,
                pager:jQuery("#pager_po"),
                //rowNum: 20,
                rownumbers: true,
                height: 300,
                width:850,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_po").getDataIDs();
                    
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            var rowData = jQuery(this).getRowData(cl); 
                            var colData = rowData['SINKRON_STATUS'];
                            
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_po('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_po('"+cl+"');\"/>";
                            sy = "<img style='padding-right:6px;' title='Sinkron' src='<?= $template_path ?>themes/base/images/arrow_refresh.png' width='12px' height='13px' onclick=\"sync_po('"+cl+"');\"/>";
                            
                            if(colData!=0){
                                jQuery("#list_po").setRowData(ids[i],{act:be+ce})    
                            }else{
                                jQuery("#list_po").setRowData(ids[i],{act:be+ce+sy})
                            }
                             
                        }                         
                    },
                afterEditCell: function (id,name,val,iRow,iCol){             
                        if(name=='TANGGALM'){ 
                            jQuery("#"+iRow+"_TANGGALM","#list_po").datepicker({dateFormat:"yy-mm-dd"});
                         }
                        if(name=='TANGGALK'){ 
                            jQuery("#"+iRow+"_TANGGALK","#list_po").datepicker({dateFormat:"yy-mm-dd"});
                         }
                    }
            });
            jGrid_va.navGrid('#pager_po',{edit:false,del:false,add:false, search: false, refresh: true});
            /*jGrid_va.navButtonAdd('#pager_po',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            }); */
            jGrid_va.navButtonAdd('#pager_po',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_po").ready(loadView); 
</script>

<script type="text/javascript">
var jGrid_poadem = null;
var colNamesT_poadem = new Array();
var colModelT_poadem = new Array();
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT_poadem.push('no');
colModelT_poadem.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_poadem.push('c_bpartner_id');
colModelT_poadem.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:true, width: 10, align:'left'});
          
colNamesT_poadem.push('Kode Supplier');
colModelT_poadem.push({name:'value',index:'value', editable: false, hidden:false, width: 70, align:'left'});

colNamesT_poadem.push('Nama Supplier');
colModelT_poadem.push({name:'name',index:'name', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_poadem.push('PO Number');
colModelT_poadem.push({name:'documentno',index:'documentno', editable: false, hidden:false, width: 170, align:'left'});

colNamesT_poadem.push('Qty ordered');
colModelT_poadem.push({name:'qtyordered',index:'qtyordered', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('Harga (/Kg)');
colModelT_poadem.push({name:'pricelist',index:'pricelist', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('Deskripsi');
colModelT_poadem.push({name:'description',index:'description', editable: false, hidden:false, width: 195, align:'left'});

colNamesT_poadem.push('Sinkron');
colModelT_poadem.push({name:'act',index:'act', editable: false, hidden:false, width: 25, align:'center'}); 

var lCol2;
var loadView = function()
        {
        jGrid_va = jQuery("#list_poadem").jqGrid(
            {
                url:url+'s_po_tbs/LoadData_POAdem/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_poadem ,
                colModel: colModelT_poadem,
                sortname: colModelT_poadem[1].name,
                pager:jQuery("#pager_poadem"),
                rowNum: 20,
                rownumbers: true,
                height: 250,
                width:850,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true ,
                onCellSelect : function(iCol2){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_poadem").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"sinkron_poadem('"+cl+"');\" />"; 
                            jQuery("#list_poadem").setRowData(ids[i],{act:be}) 
                        }                         
                    } 
            });
            jGrid_va.navGrid('#pager_poadem',{edit:false,del:false,add:false, search: false, refresh: true});
              
         }
jQuery("#list_poadem").ready(loadView); 
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false;
 
function get_periode(){
    var periode = $("#txt_periodeSrc").val();
    return periode;
}

function get_data(){
    jQuery("#list_po").setGridParam({url:url+"s_po_tbs/LoadData/"}).trigger("reloadGrid");
  
}

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    jQuery("#list_po").setGridParam({url:url+"s_po_tbs/LoadData/"}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function open_poadem(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    jQuery("#list_poadem").setGridParam({url:url+"s_po_tbs/LoadData_POAdem/"}).trigger("reloadGrid"); 
    
    $('input').val('');
    $("#frm_poadem").dialog('open');
}

function addrow(){
    var rowCount = $("#list_po").getGridParam("reccount");
    var i;
    var method = 'ADD';
    if(rowCount==null || rowCount==0){
        i=i+1;    
    }else{
        i=rowCount+1;
    }
        
    var datArr = {};
    if (i>1){
        var datArr = {ID_BJR:jdesc1};
    }
    
    jQuery('#list_po').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_po("+i+")'; />"; 
    var su=jQuery("#list_po").addRowData(i,datArr,'last');
    var act=jQuery("#list_po").setRowData(i,{act:sv});  
}

function sinkron_poadem(cl){
    var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    var data = $("#list_poadem").getRowData(ids) ;
    
    jQuery('#list_poadem').saveCell(ids);
    var answer = confirm ("Sinkron PO Adempiere dengan Nomor: "+ data.documentno +" ? ")
    if (answer){
        $("#frm_load").dialog('open')
        var postdata_id = {};
        //postdata_id['ID_FFA'] = data.ID_FFA;
        postdata_id['PO_NUMBER'] = data.documentno;
        postdata_id['SUPPLIERCODE'] = data.value;
        postdata_id['C_BPARTNER_ID'] = data.c_bpartner_id; 
        postdata_id['QTYORDERED'] = data.qtyordered;
        postdata_id['PRICELIST'] = data.pricelist;  
        postdata_id['DESCRIPTION'] = data.description;
        
        postdata_id['CRUD'] =  'ADDPOADEM';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_po_tbs/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                            reloadGrid();   
                        } 
                }
        });
    }
}

function update_po(){
    var answer = confirm ("Update Data PO dengan Nomor : " + $("#txt_poNumber").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        alert ("Data PO yang sudah tersinkron sebelumnya, harus disinkron kembali...");
         
        postdata_id['ID_ANON'] = $("#txt_poID").val();
        postdata_id['PO_NUMBER'] = $("#txt_poNumber").val();
        postdata_id['TANGGALM'] = $("#txt_poDateStart").val();
        postdata_id['TANGGALK'] = $("#txt_poDateEnd").val();
        postdata_id['PRICELIST'] = $("#txt_poPriceList").val();
        postdata_id['DESCRIPTION'] = $("#txt_poDescription").val();
        
        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_po_tbs/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                            reloadGrid();
                            $("#frm_po").dialog('close');    
                        } 
                }
        });    
    }
}

function edit_po(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_po").getRowData(ids) ;
    jQuery('#list_po').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $("#txt_poPriceList").removeAttr('disabled');
        
        $("#txt_frmMode").val("EDIT");
        $("#txt_poID").val(data.ID_ANON);
        $("#txt_poNumber").val(data.PO_NUMBER);
        $("#txt_poSuplierCode").val(data.SUPPLIERCODE);
        $("#txt_poQtyOrdered").val(data.QTYORDERED);
        $("#txt_poPriceList").val(data.PRICELIST);
        $("#txt_poDateStart").val(data.TANGGALM);
        $("#txt_poDateEnd").val(data.TANGGALK);
        $("#txt_poDescription").val(data.DESCRIPTION);
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_po").dialog('open');
    } 
}

function hapus_po(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_po").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus PO dengan Nomor : " + data.PO_NUMBER + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    
            postdata_id['ID_ANON'] = data.ID_ANON;
            postdata_id['PO_NUMBER'] = data.PO_NUMBER;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_po_tbs/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                            reloadGrid();  
                        } 
                    }
            });        
        }
        
    }     
}

function sync_po(cl){
    var ids = cl;
    var data = $("#list_po").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di Sinkron...");
    }else{
        var answer = confirm ("Sinkron PO dengan Nomor : " + data.PO_NUMBER + ", ke Database Timbangan ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    
            postdata_id['ID_ANON'] = data.ID_ANON;
            postdata_id['PO_NUMBER'] = data.PO_NUMBER;
            postdata_id['SUPPLIERCODE']=data.SUPPLIERCODE ;
            postdata_id['SUPPLIERNAME']=data.SUPPLIERNAME ;
            postdata_id['QTYORDERED']=data.QTYORDERED ;
            postdata_id['PRICELIST']=data.PRICELIST ;
            postdata_id['TANGGALM']=data.TANGGALM ;
            postdata_id['TANGGALK']=data.TANGGALK ;
            postdata_id['DESCRIPTION']=data.DESCRIPTION ;
            postdata_id['SINKRON_STATUS']=data.SINKRON_STATUS ;
            postdata_id['C_BPARTNER_ID']=data.C_BPARTNER_ID ;
            
            postdata_id['CRUD'] =  'SYNC';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_po_tbs/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                            reloadGrid();  
                        } 
                    }
            });        
        }
        
    }         
}
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_po").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Purchase Order Detail",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        clear_form_elements(this.form);        
                    }
            
        } 
    });
    
    $("#frm_poadem").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 950,
        modal: true,
        title: "Data PO (Purchase Order) Adempiere",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        clear_form_elements(this.form);        
                    }
            
        } 
    });
    
    $("#frm_load").dialog({
        dialogClass : 'alert',
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
            jQuery("#list_po").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_po_tbs/search_data');
        }
    });
});

function setDialogWindows($element) {
$('#search_form').dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center'
    }); 
}
</script>
