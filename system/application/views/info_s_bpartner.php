<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_bp" class="scroll"></table> 
            <div id="pager_bp" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="open_bpadem()">Sinkron Business Partner</button>&nbsp;
    </div>
</div> 

<div id="frm_bp">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Business Partner</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">ID Business Partner</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_idBP" name="bp_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Nama</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 175px;" tabindex="2" type="text" id="txt_name" name="bp_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Kode</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="3" type="text" id="txt_code" name="bp_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">CP</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="4" type="text" id="txt_cp" name="bp_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">NPWP</td><td>:</td>
                    <td class="fieldcell"><input disabled="disabled" tabindex="5" type="text" id="txt_npwpt" name="bp_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Business Partner Group</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_group" name="bp_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Business Partner Type</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_type" name="bp_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Alamat</td><td>:</td>
                    <td class="fieldcell"><textarea disabled="disabled"  tabindex="8" style="height:65px; width:300px;" class="input" id="txt_alamat" name="bp_input"></textarea></td>
                </tr>
            </table>
           
        </div>
        
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_bpadem">
    <div id="tabsbp">
        <ul class="tabsbp">
            <li><a href="#fragment-1"><span>Daftar Business Partner Adempiere</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            
            <div id="bpademGrid">
                <table id="list_bpadem" class="scroll"></table> 
                <div id="pager_bpadem" class="scroll" style="text-align:center;"></div>
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
     jQuery("#list_bp").setGridParam({url:url+'s_bpartner/LoadData/'}).trigger("reloadGrid");    
}
/*
$(function() {
    //################# DATE PICKER SETTING #####################
    $("#txt_poDateStart").datepicker({dateFormat:"yy-mm-dd"});
    $("#txt_poDateEnd").datepicker({dateFormat:"yy-mm-dd"});
});
*/
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
  
$(document).ready(function() {
    var $tabsbp = $('#tabsbp').tabs();
    var selected = $tabsbp.tabs('option', 'selected'); // => 0
    $("#tabsbp").tabs();
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
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 10, align:'center'});

colNamesT.push('BP CODE');
colModelT.push({name:'SUPPLIERCODE',index:'SUPPLIERCODE', editable: false, hidden:false, width: 50, align:'left'});

colNamesT.push('BUSINESS PARTNER');
colModelT.push({name:'C_BPARTNER_ID',index:'C_BPARTNER_ID', editable: true, hidden:false, width: 50, align:'left'});
          
colNamesT.push('NAMA');
colModelT.push({name:'SUPPLIERNAME',index:'SUPPLIERNAME', editable: false, hidden:false, width: 175, align:'left'});

colNamesT.push('CP');
colModelT.push({name:'CONTACTNAME',index:'CONTACTNAME', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('ADDRESS');
colModelT.push({name:'ADDRESS',index:'ADDRESS', editable: false, hidden:false, width: 175, align:'left'});

colNamesT.push('BP GROUP');
colModelT.push({name:'SUPPLIERTYPE',index:'SUPPLIERTYPE', editable: false, hidden:false, width: 75, align:'left'});

colNamesT.push('NPWP');
colModelT.push({name:'NPWP',index:'NPWP', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BP TYPE');
colModelT.push({name:'KODE_PENGIRIM',index:'KODE_PENGIRIM', editable: false, hidden:false, width: 50, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_bp").jqGrid(
            {
                url:url+'s_bpartner/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_bp"),
                //rowNum: 20,
                rownumbers: true,
                height: 300,
                width:1100,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_bp").getDataIDs();
                    
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //var rowData = jQuery(this).getRowData(cl); 
                            //var colData = rowData['SINKRON_STATUS'];
                            
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_bp('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_bp('"+cl+"');\"/>";
                            sy = "<img style='padding-right:6px;' title='Sinkron' src='<?= $template_path ?>themes/base/images/arrow_refresh.png' width='12px' height='13px' onclick=\"sync_bp('"+cl+"');\"/>";
                            
                            //if(colData!=0){
                                //jQuery("#list_bp").setRowData(ids[i],{act:be+ce})    
                            //}else{
                                jQuery("#list_bp").setRowData(ids[i],{act:be+ce+sy})
                            //}
                             
                        }                         
                    }
            });
            jGrid_va.navGrid('#pager_bp',{edit:false,del:false,add:false, search: false, refresh: true});
            jGrid_va.navButtonAdd('#pager_bp',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
               		$("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_bp").ready(loadView); 
</script>

<script type="text/javascript">
var jGrid_bpadem = null;
var colNamesT_bpadem = new Array();
var colModelT_bpadem = new Array();
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT_bpadem.push('no');
colModelT_bpadem.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_bpadem.push('ID BP');
colModelT_bpadem.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:false, width: 75, align:'left'});
          
colNamesT_bpadem.push('COMPANY');
colModelT_bpadem.push({name:'company_code',index:'company_code', editable: false, hidden:false, width: 70, align:'left'});

colNamesT_bpadem.push('GROUP');
colModelT_bpadem.push({name:'group',index:'group', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_bpadem.push('KODE');
colModelT_bpadem.push({name:'kode',index:'kode', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_bpadem.push('NAMA');
colModelT_bpadem.push({name:'name',index:'name', editable: false, hidden:false, width: 175, align:'left'});

colNamesT_bpadem.push('NPWP');
colModelT_bpadem.push({name:'taxid',index:'taxid', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_bpadem.push('ALAMAT');
colModelT_bpadem.push({name:'address',index:'address', editable: false, hidden:false, width: 195, align:'left'});

colNamesT_bpadem.push('CP');
colModelT_bpadem.push({name:'cp',index:'cp', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_bpadem.push('DESC');
colModelT_bpadem.push({name:'description',index:'description', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_bpadem.push('Sinkron');
colModelT_bpadem.push({name:'act',index:'act', editable: false, hidden:false, width: 25, align:'center'}); 

var lCol2;
var loadView = function()
        {
        jGrid_va = jQuery("#list_bpadem").jqGrid(
            {
                url:url+'s_bpartner/LoadData_BPAdem/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_bpadem ,
                colModel: colModelT_bpadem,
                sortname: colModelT_bpadem[1].name,
                pager:jQuery("#pager_bpadem"),
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
                    var ids = jQuery("#list_bpadem").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"sinkron_bpadem('"+cl+"');\" />"; 
                            jQuery("#list_bpadem").setRowData(ids[i],{act:be}) 
                        }                         
                    } 
            });
            jGrid_va.navGrid('#pager_bpadem',{edit:false,del:false,add:false, search: false, refresh: true});
              
         }
jQuery("#list_bpadem").ready(loadView); 
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false;
 
function get_periode(){
    var periode = $("#txt_periodeSrc").val();
    return periode;
}

function get_data(){
    jQuery("#list_bp").setGridParam({url:url+"s_bpartner/LoadData/"}).trigger("reloadGrid");
  
}

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    jQuery("#list_bp").setGridParam({url:url+"s_bpartner/LoadData/"}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function open_bpadem(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    jQuery("#list_bpadem").setGridParam({url:url+"s_bpartner/LoadData_BPAdem/"}).trigger("reloadGrid"); 
    
    $('input').val('');
    $("#frm_bpadem").dialog('open');
}

function addrow(){
    var rowCount = $("#list_bp").getGridParam("reccount");
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
    
    jQuery('#list_bp').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_bp("+i+")'; />"; 
    var su=jQuery("#list_bp").addRowData(i,datArr,'last');
    var act=jQuery("#list_bp").setRowData(i,{act:sv});  
}

function sinkron_bpadem(cl){
    var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    var data = $("#list_bpadem").getRowData(ids) ;
    
    jQuery('#list_bpadem').saveCell(ids);
    var answer = confirm ("Sinkron BP Adempiere dengan kode: "+ data.kode +" ? ")
    if (answer){
        $("#frm_load").dialog('open')
        var postdata_id = {};
        postdata_id['C_BPARTNER_ID'] = data.c_bpartner_id;		
        postdata_id['SUPPLIERCODE'] = data.kode;		
        postdata_id['SUPPLIERNAME'] = data.name; 
		postdata_id['CONTACTNAME'] = data.cp; 		
        postdata_id['ADDRESS'] = data.address;		
        postdata_id['SUPPLIERTYPE'] = 'LUAR';		  
        postdata_id['NPWP'] = data.taxid;
        
        postdata_id['CRUD'] =  'ADDBPADEM';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_bpartner/CRUD_METHOD',
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

function update_bp(){
    var answer = confirm ("Update Data Business Partner dengan kode : " + $("#txt_code").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        alert ("Data Business Partner yang sudah tersinkron sebelumnya, harus disinkron kembali...");	
		postdata_id['C_BPARTNER_ID'] = $("#txt_idBP").val();
        postdata_id['SUPPLIERCODE'] = $("#txt_code").val();
        postdata_id['SUPPLIERNAME'] = $("#txt_name").val();
        postdata_id['CONTACTNAME'] = $("#txt_cp").val();
        postdata_id['ADDRESS'] = $("#txt_alamat").val();
        postdata_id['SUPPLIERTYPE'] = $("#txt_group").val();
        postdata_id['NPWP'] = $("#txt_npwpt").val();
		postdata_id['KODE_PENGIRIM'] = $("#txt_type").val();
        
        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_bpartner/CRUD_METHOD',
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
                            $("#frm_bp").dialog('close');    
                        } 
                }
        });    
    }
}

function edit_bp(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_bp").getRowData(ids) ;
    jQuery('#list_bp').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        
        $("#txt_frmMode").val("EDIT");
        $("#txt_idBP").val(data.C_BPARTNER_ID);		
        $("#txt_name").val(data.SUPPLIERNAME);
		$("#txt_code").val(data.SUPPLIERCODE);		
        $("#txt_cp").val(data.CONTACTNAME);		
        $("#txt_npwpt").val(data.NPWP);		
        $("#txt_group").val(data.SUPPLIERTYPE);		
        $("#txt_type").val(data.KODE_PENGIRIM);		
        $("#txt_alamat").val(data.ADDRESS);

        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_bp").dialog('open');
    } 
}

function hapus_bp(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_bp").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Business Partner dengan Nomor : " + data.PO_NUMBER + " ?" );
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
                    url:            url+'s_bpartner/CRUD_METHOD',
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

function sync_bp(cl){
    var ids = cl;
    var data = $("#list_bp").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di Sinkron...");
    }else{
        var answer = confirm ("Sinkron Business Partner dengan Nomor : " + data.SUPPLIERCODE + ", ke Database Timbangan ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    		postdata_id['SUPPLIERCODE'] = data.SUPPLIERCODE;		
        	postdata_id['SUPPLIERNAME'] = data.SUPPLIERNAME; 
			postdata_id['CONTACTNAME'] = data.CONTACTNAME; 		
        	postdata_id['ADDRESS'] = data.ADDRESS;		
        	postdata_id['SUPPLIERTYPE'] = data.SUPPLIERTYPE;		  
        	postdata_id['NPWP'] = data.NPWP;
			postdata_id['KODE_PENGIRIM'] = data.KODE_PENGIRIM;;            
            postdata_id['CRUD'] =  'SYNC';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_bpartner/CRUD_METHOD',
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
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_bp").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Purchase Order Detail",
        resizable: true,
        moveable: true,
        buttons: {
			Simpan: function() 
                    {
                        //clear_form_elements(this.form);  
			   			update_bp();      
                    },
            Tutup: function() 
                    {
                        //clear_form_elements(this.form);  
			   			$('input').val('');
                        $("#frm_bp").dialog('close');          
                    }
            
        } 
    });
    
    $("#frm_bpadem").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 950,
        modal: true,
        title: "Data Business Partner Adempiere",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        //$('input').val('');
                        $("#frm_bpadem").dialog('close');           
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
            jQuery("#list_bp").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_bpartner/search_data');
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
