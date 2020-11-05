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
        <button class="testBtn" type="submit" onclick="open_poadem()">Sinkron DO Adempiere</button>&nbsp;
    </div>
</div> 

<div id="frm_po">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail DO</span></a></li>

        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">ID DO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_doID" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Nomor SO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 175px;" tabindex="2" type="text" id="txt_soNumber" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Nomor DO</td><td>:</td>
                    <td class="fieldcell"> 
                        <input style="width: 175px;" tabindex="3" type="text" id="txt_doNumber" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Qty Ordered</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="4" type="text" id="txt_doQtyOrdered" name="po_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Jenis</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="5" type="text" id="txt_doJenis" name="po_input" maxlength="25"/></td>
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
            <li><a href="#fragment-1"><span>Daftar DO Adempiere</span></a></li>

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
     jQuery("#list_po").setGridParam({url:url+'s_do_tbs/LoadData/'}).trigger("reloadGrid");    
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

colNamesT.push('SO_NUMBER');
colModelT.push({name:'SO_NUMBER',index:'SO_NUMBER', editable: true, hidden:false, width: 150, align:'center'});
          
colNamesT.push('ID_DO');
colModelT.push({name:'ID_DO',index:'ID_DO', editable: false, hidden:false, width: 175, align:'left'});

colNamesT.push('CUSTOMER_NAME');
colModelT.push({name:'CUSTOMER_NAME',index:'CUSTOMER_NAME', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('QTY_CONTRACT');
colModelT.push({name:'QTY_CONTRACT',index:'QTY_CONTRACT', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('JENIS');
colModelT.push({name:'JENIS',index:'JENIS', editable: false, hidden:false, width: 100, align:'left'});

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
                url:url+'s_do_tbs/LoadData/',
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

colNamesT_poadem.push('ad_org_id');
colModelT_poadem.push({name:'ad_org_id',index:'ad_org_id', editable: false, hidden:true, width: 10, align:'left'});
          
colNamesT_poadem.push('name');
colModelT_poadem.push({name:'name',index:'name', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_poadem.push('documentno');
colModelT_poadem.push({name:'documentno',index:'documentno', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_poadem.push('c_bpartner_id');
colModelT_poadem.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:true, width: 170, align:'left'});

colNamesT_poadem.push('Nama Customer');
colModelT_poadem.push({name:'customer',index:'customer', editable: false, hidden:false, width: 125, align:'left'});

colNamesT_poadem.push('Alamat');
colModelT_poadem.push({name:'address1',index:'address1', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('line');
colModelT_poadem.push({name:'line',index:'line', editable: false, hidden:true, width: 195, align:'left'});

colNamesT_poadem.push('m_product_id');
colModelT_poadem.push({name:'m_product_id',index:'m_product_id', editable: false, hidden:true, width: 195, align:'left'});

colNamesT_poadem.push('Jenis');
colModelT_poadem.push({name:'namaproduct',index:'namaproduct', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('productCategory');
colModelT_poadem.push({name:'productcategory',index:'productcategory', editable: false, hidden:true, width: 195, align:'left'});

colNamesT_poadem.push('qtyordered');
colModelT_poadem.push({name:'qtyordered',index:'qtyordered', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_poadem.push('Sinkron');
colModelT_poadem.push({name:'act',index:'act', editable: false, hidden:false, width: 25, align:'center'}); 

var lCol2;
var loadView = function()
        {
        jGrid_va = jQuery("#list_poadem").jqGrid(
            {
                url:url+'s_do_tbs/LoadData_DOAdem/',
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
    
    jQuery("#list_poadem").setGridParam({url:url+"s_do_tbs/LoadData_DOAdem/"}).trigger("reloadGrid"); 
    
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
    var answer = confirm ("Sinkron DO Adempiere dengan Nomor: "+ data.documentno +" ? ")
    if (answer){
        $("#frm_load").dialog('open')
        var postdata_id = {};
        //postdata_id['ID_FFA'] = data.ID_FFA;
        postdata_id['SO_NUMBER'] = data.documentno;
        postdata_id['C_BPARTNER_ID'] = data.c_bpartner_id;
        postdata_id['CUSTOMER_NAME'] = data.customer; 
        postdata_id['CUSTOMER_ADDRESS'] = data.address1;
        postdata_id['ID_JENIS'] = data.m_product_id;  
        postdata_id['QTY_CONTRACT'] = data.qtyordered;
        
        postdata_id['CRUD'] =  'ADDDOADEM';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_do_tbs/CRUD_METHOD',
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
    var answer = confirm ("Update Data PO dengan Nomor : " + $("#txt_soNumber").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        alert ("Data PO yang sudah tersinkron sebelumnya, harus disinkron kembali...");
         
        postdata_id['SO_NUMBER'] = $("#txt_soNumber").val();
        postdata_id['ID_DO'] = $("#txt_doNumber").val();

        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_do_tbs/CRUD_METHOD',
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
        
        $("#txt_frmMode").val("EDIT");
        $("#txt_doID").val(data.ID_ANON);
        $("#txt_soNumber").val(data.SO_NUMBER);
        $("#txt_doNumber").val(data.ID_DO);
        $("#txt_doQtyOrdered").val(data.QTY_CONTRACT);
        $("#txt_doJenis").val(data.JENIS);
        
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
        var answer = confirm ("Hapus DO dengan Nomor SO: " + data.SO_NUMBER + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    
            postdata_id['SO_NUMBER'] = data.SO_NUMBER;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_do_tbs/CRUD_METHOD',
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
        title: "Delivery Order Detail",
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
        title: "Data DO (Delivery Order) Adempiere",
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

