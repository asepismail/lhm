<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_so" class="scroll"></table> 
            <div id="pager_po" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="open_poadem()">Sinkron SO Adempiere</button>&nbsp;
        <button class="testBtn" type="submit" onclick="add_do()">Input DO Cangkang</button>&nbsp;
    </div>
</div>

<div id="frm_do">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Delivery Order</span></a></li>

        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">Nomor DO</td><td>:</td>
                    <td class="fieldcell"> 
                        <input style="width: 275px;" tabindex="3" type="text" id="txt_doNumber2" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">CBPARTNER</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_cbpartner2" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Customer Name</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 275px;" tabindex="1" type="text" id="txt_customer2" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Customer Address</td><td>:</td>
                    <td class="fieldcell">
                    	<textarea disabled="disabled"  tabindex="6" name="po_input" id="txt_customeraddr2" cols="45" rows="4"></textarea>
                        
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Qty Ordered</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_doQtyOrdered2" name="po_input" maxlength="25"/></td>
                </tr>
                
                <tr>
                    <td class="labelcell">Nomor SO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" tabindex="2" type="text" id="txt_soNumber2" name="po_input" maxlength="100"/>
                    </td>
                </tr>                
                <tr>
                    <td class="labelcell">Jenis</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="5" type="text" id="txt_doJenis2" name="po_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Company</td><td>:</td>
                    <td class="fieldcell"> 
                        <input disabled="disabled"  tabindex="3" type="text" id="txt_company_code2" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">ID Jenis</td><td>:</td>
                    <td class="fieldcell"> 
                        <input disabled="disabled" tabindex="3" type="text" id="txt_idJenis2" name="po_input" maxlength="50"/>
                    </td>
                </tr>
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_sinkPO" onclick="sync_po()">Sinkron</button>    
        </div>
        
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_po">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail DO</span></a></li>

        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
            	<input tabindex="5" type="hidden" id="txt_doID" name="nt_input" maxlength="100"/>
                <input tabindex="5" type="hidden" id="txt_company_code" name="nt_input" maxlength="100"/>
                <input tabindex="5" type="hidden" id="txt_idJenis" name="nt_input" maxlength="100"/>
                <tr>
                    <td class="labelcell">Nomor DO</td><td>:</td>
                    <td class="fieldcell"> 
                        <input style="width: 275px;" tabindex="3" type="text" id="txt_doNumber" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Customer Name</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 275px;" tabindex="1" type="text" id="txt_customer" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">CBPARTNER</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_cbpartner" name="po_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Customer Address</td><td>:</td>
                    <td class="fieldcell">
                    <textarea disabled="disabled"  tabindex="6" name="po_input" id="txt_customeraddr" cols="45" rows="4"></textarea>
                        
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Qty Ordered</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="4" type="text" id="txt_doQtyOrdered" name="po_input" maxlength="25"/></td>
                </tr>
                
                <tr>
                    <td class="labelcell">Nomor SO</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 275px;" tabindex="2" type="text" id="txt_soNumber" name="po_input" maxlength="50"/>
                    </td>
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
            <li><a href="#fragment-1"><span>Daftar SO Adempiere</span></a></li>

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

<div id="search_so"></div>
</body>

<script type="text/javascript">
/*
$(document).ready(function() {
	$("#txt_customer").change(resetAutocomplete);
});
*/
function reloadGrid(){   
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_so").setGridParam({url:url+'s_do_dispatch/LoadData/'}).trigger("reloadGrid");    
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
          
colNamesT.push('ID DO');
colModelT.push({name:'ID_DO',index:'ID_DO', editable: false, hidden:false, width: 175, align:'left'});

colNamesT.push('CBPARTNER');
colModelT.push({name:'C_BPARTNER_ID',index:'C_BPARTNER_ID', editable: false, hidden:false, width: 50, align:'left'});

colNamesT.push('CUSTOMER NAME');
colModelT.push({name:'CUSTOMER_NAME',index:'CUSTOMER_NAME', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('CUSTOMER ADDRESS');
colModelT.push({name:'CUSTOMER_ADDRESS',index:'CUSTOMER_ADDRESS', editable: false, hidden:false, width: 200, align:'left'});

colNamesT.push('QTY CONTRACT');
colModelT.push({name:'QTY_CONTRACT',index:'QTY_CONTRACT', editable: false, hidden:false, width: 50, align:'right'});

colNamesT.push('COMPANY CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('ID_JENIS');
colModelT.push({name:'ID_JENIS',index:'ID_JENIS', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('JENIS');
colModelT.push({name:'JENIS',index:'JENIS', editable: false, hidden:false, width: 50, align:'left'});

colNamesT.push('SO NUMBER');
colModelT.push({name:'SO_NUMBER',index:'SO_NUMBER', editable: false, hidden:false, width: 150, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 30, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_so").jqGrid(
            {
                url:url+'s_do_dispatch/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[7].name,
                pager:jQuery("#pager_po"),
                rownumbers: true,
                height: 300,
                width:1150,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_so").getDataIDs();
                    
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            var rowData = jQuery(this).getRowData(cl); 
                            var colData = rowData['SINKRON_STATUS'];
                            
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_do('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_po('"+cl+"');\"/>";                            
                            if(colData!=0){
                                jQuery("#list_so").setRowData(ids[i],{act:be+ce})    
                            }else{
                                jQuery("#list_so").setRowData(ids[i],{act:be+ce+sy})
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
jQuery("#list_so").ready(loadView); 
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
colModelT_poadem.push({name:'name',index:'name', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_poadem.push('SO NO');
colModelT_poadem.push({name:'documentno',index:'documentno', editable: false, hidden:false, width: 200, align:'left'});

colNamesT_poadem.push('CBPARTNER');
colModelT_poadem.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('Nama Customer');
colModelT_poadem.push({name:'customer',index:'customer', editable: false, hidden:false, width: 175, align:'left'});

colNamesT_poadem.push('Alamat');
colModelT_poadem.push({name:'address1',index:'address1', editable: false, hidden:false, width: 200, align:'left'});

colNamesT_poadem.push('line');
colModelT_poadem.push({name:'line',index:'line', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_poadem.push('m_product_id');
colModelT_poadem.push({name:'m_product_id',index:'m_product_id', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_poadem.push('Jenis');
colModelT_poadem.push({name:'namaproduct',index:'namaproduct', editable: false, hidden:false, width: 75, align:'left'});

colNamesT_poadem.push('productCategory');
colModelT_poadem.push({name:'productcategory',index:'productcategory', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_poadem.push('QTY');
colModelT_poadem.push({name:'qtyordered',index:'qtyordered', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_poadem.push('Sinkron');
colModelT_poadem.push({name:'act',index:'act', editable: false, hidden:false, width: 25, align:'center'}); 

var lCol2;
var loadView = function()
        {
        jGrid_va = jQuery("#list_poadem").jqGrid(
            {
                url:url+'s_do_dispatch/LoadData_DOAdem/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_poadem ,
                colModel: colModelT_poadem,
                sortname: colModelT_poadem[1].name,
                pager:jQuery("#pager_poadem"),
                rowNum: 20,
                rownumbers: true,
                height: 250,
                width:900,
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
			
			jGrid_va.navButtonAdd('#pager_poadem',{
               caption:"Cari Data SO", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
               		$("#search_so").dialog('open');
               }, 
               position:"left"
            }); 
              
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
    jQuery("#list_so").setGridParam({url:url+"s_do_dispatch/LoadData/"}).trigger("reloadGrid");
  
}

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    jQuery("#list_so").setGridParam({url:url+"s_do_dispatch/LoadData/"}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function open_poadem(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    jQuery("#list_poadem").setGridParam({url:url+"s_do_dispatch/LoadData_DOAdem/"}).trigger("reloadGrid"); 
    
    $('input').val('');
    $("#frm_poadem").dialog('open');
}

function addrow(){
    var rowCount = $("#list_so").getGridParam("reccount");
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
    
    jQuery('#list_so').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_po("+i+")'; />"; 
    var su=jQuery("#list_so").addRowData(i,datArr,'last');
    var act=jQuery("#list_so").setRowData(i,{act:sv});  
}

function sinkron_poadem(cl){
	var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_poadem").getRowData(ids) ;
    jQuery('#list_poadem').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        
        $("#txt_frmMode").val("ADD");
		$("#txt_company_code2").val(data.ad_org_id);
		$("#txt_idJenis2").val(data.m_product_id);
		
		$("#txt_doNumber2").val(data.ID_DO);
		$("#txt_cbpartner2").val(data.c_bpartner_id);
		$("#txt_customer2").val(data.customer);
		$("#txt_customeraddr2").val(data.address1);
		$("#txt_doQtyOrdered2").val(data.qtyordered);
		$("#txt_doJenis2").val(data.namaproduct);		
        $("#txt_soNumber2").val(data.documentno);     
		
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_do").dialog('open');
    }     
}

function update_po(){
    var answer = confirm ("Simpan Data DO dengan Nomor : " + $("#txt_doNumber").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};        
        postdata_id['SO_NUMBER'] = $("#txt_soNumber").val();
        postdata_id['ID_DO'] = $("#txt_doNumber").val();
        postdata_id['CRUD'] =   $("#txt_frmMode").val();
		if($("#txt_frmMode").val()=='CREATE'){
			postdata_id['C_BPARTNER_ID'] = $("#txt_cbpartner").val();
	        postdata_id['CUSTOMER_NAME'] =   $("#txt_customer").val();
			postdata_id['CUSTOMER_ADDRESS'] =   $("#txt_customeraddr").val();
			postdata_id['QTY_CONTRACT'] =   $("#txt_doQtyOrdered").val();
			postdata_id['JENIS'] =   $("#txt_doJenis").val();
		}

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_do_dispatch/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error==true){
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

function edit_do(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_so").getRowData(ids) ;
    jQuery('#list_so').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit");
    }else{
        
        $("#txt_frmMode").val("EDIT");
        $("#txt_doID").val(data.ID_ANON);
		$("#txt_company_code").val(data.COMPANY_CODE);
		$("#txt_idJenis").val(data.ID_JENIS);
		
		$("#txt_doNumber").val(data.ID_DO);
		$("#txt_cbpartner").val(data.C_BPARTNER_ID);
		$("#txt_customer").val(data.CUSTOMER_NAME);
		$("#txt_customeraddr").val(data.CUSTOMER_ADDRESS);
		$("#txt_doQtyOrdered").val(data.QTY_CONTRACT);
		$("#txt_doJenis").val(data.JENIS);		
        $("#txt_soNumber").val(data.SO_NUMBER);     
		
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_po").dialog('open');
    } 
}

function resetAutocomplete(){
    $("#txt_customer").autocomplete( 
            url+"s_do_dispatch/get_cbpartner/"+$("#txt_customer").val(), {
              dataType: 'ajax',
              width:350,
              multiple: false,
              limit:20,
              parse: function(data) {
                  return $.map(eval(data), function(row) {
                  return (typeof(row) == 'object')
                    ? { data: row, value: row.res_id, result: row.res_id }
                    : { data: row, value: '',result: ''};
                });
              },
              formatItem: function(item) {
                return (typeof(item) == 'object')?item.res_dl :'';
              }
            }
    ).result(function(e, item) {
            $("#txt_cbpartner").val(item.res_name);
            $("#txt_customeraddr").val(item.res_dName );
    });   
}

function add_do(cl){        
	$("#txt_frmMode").val("CREATE");	
	$("#txt_doNumber").val('');
	$("#txt_cbpartner").val('');
	$("#txt_customer").val('');
	$("#txt_customeraddr").val('');
	$("#txt_doQtyOrdered").val('');
	$("#txt_soNumber").val('');
	$("#txt_doJenis").val('CANGKANG');
	
	$("#txt_customer").removeAttr('disabled');
	$("#txt_doQtyOrdered").removeAttr('disabled');
	
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    $("#frm_po").dialog('open');
}

function hapus_po(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_so").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus DO dengan Nomor SO: " + data.SO_NUMBER + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
			postdata_id['ID_DO'] = data.ID_DO;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_do_dispatch/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error==true){
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
	var answer = confirm ("Sinkron Data SO Adempire dengan Nomor : " + $("#txt_soNumber2").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};  		
		postdata_id['ID_DO'] = $("#txt_doNumber2").val();
		postdata_id['C_BPARTNER_ID'] = $("#txt_cbpartner2").val();
		postdata_id['CUSTOMER_NAME'] = $("#txt_customer2").val();
		postdata_id['CUSTOMER_ADDRESS'] = $("#txt_customeraddr2").val();
		postdata_id['QTY_CONTRACT'] = $("#txt_doQtyOrdered2").val();
		postdata_id['ID_JENIS'] = $("#txt_idJenis2").val();
		postdata_id['JENIS'] = $("#txt_doJenis2").val();
        postdata_id['SO_NUMBER'] = $("#txt_soNumber2").val();
		postdata_id['COMPANY_CODE'] = $("#txt_company_code2").val();
        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_do_dispatch/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error==true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                            reloadGrid();
                            $("#frm_do").dialog('close');   
							$("#frm_poadem").dialog('close'); 							
                        } 
                }
        });    
    }
	/*
    var ids = cl;
    var data = $("#list_so").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di Sinkron...");
    }else{
        var answer = confirm ("Sinkron DO dengan Nomor : " + data.PO_NUMBER + ", ke Database Timbangan ?" );
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
	*/
}
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
   $("#txt_customer").change(resetAutocomplete);
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
                        $("#frm_po").dialog('close');        
                    }
            
        } 
    });
   
   $("#frm_do").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 500,
        width: 580,
        modal: true,
        title: "Sinkron SO Adempire",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $("#frm_do").dialog('close');        
                    }
            
        } 
    });
    
    $("#frm_poadem").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 1000,
        modal: true,
        title: "Data SO (Sales Order) Adempiere",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $("#frm_poadem").dialog('close');         
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
            jQuery("#list_so").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_do_dispatch/search_data');
        }
    });
	
	$("#search_so").dialog({
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
            setDialogWindows('#search_so');
        },
        open:function(){
            jQuery("#list_poadem").smartSearchPanel('#search_so', {dialog:{width: 530}},'s_do_dispatch/search_so');
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

function setDialogWindows($element) {
$('#search_so').dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center'
    }); 
}
</script>

