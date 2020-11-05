<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div>
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">ID Storage</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="1" class="input" type="text" id="txt_kodeSrc" name="strg_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr> 
        </table> -->
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_storage" class="scroll"></table> 
            <div id="pager_storage" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <!--<input type="button"  id="add_storage" value="Buat Storage baru" class="testBtn" onClick="add_storage()">&nbsp;--> 
    </div>
</div> 

<div id="frm_storage">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Storage</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">ID Storage</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" tabindex="1" type="text" id="txt_strgID" name="strg_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Kode Produk</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_strgProdCode" name="strg_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Kapasitas Maksimal \ Volume Bersih</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_strgMaxCap" name="strg_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Diameter (mm)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_strgDiameter" name="strg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Jenis Atap</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_strgJnsAtap" name="strg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tinggi Meja Ukur (mm)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_strgZCap" name="strg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Keterangan</td><td>:</td>
                    <td class="fieldcell"><textarea cols="45" rows="7" tabindex="6" id="txt_strgDesc" name="strg_input"></textarea></td>
                </tr>
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_saveStorage" onclick="update_storage()">Simpan</button>    
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
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_storage").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "STORAGE",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $("#frm_storage").dialog('close');        
                    }
            
        } 
    });
    
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
            jQuery("#list_storage").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_storage/search_data');
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

<script type="text/javascript">
function reloadGrid(){
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_storage").setGridParam({url:url+'m_storage/LoadData/'}).trigger("reloadGrid");    
}

$(document).ready(function() {
    $('input').val('');
    $('textarea').val('');
});
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
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

colNamesT.push('ID Storage');
colModelT.push({name:'ID_STORAGE',index:'ID_STORAGE', editable: true, hidden:false, width: 50, align:'center'});

colNamesT.push('Kode Produk');
colModelT.push({name:'PRODUCT_CODE',index:'PRODUCT_CODE', editable: true, hidden:false, width: 75, align:'center'});

colNamesT.push('Keterangan');
colModelT.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true, hidden:false, width: 140, align:'center'});

colNamesT.push('Volume Bersih (Liter)');
colModelT.push({name:'MAXCAPACITY',index:'MAXCAPACITY', editable: true, hidden:false, width: 100, align:'center'});

colNamesT.push('Diameter (mm)');
colModelT.push({name:'DIAMETER',index:'DIAMETER', editable: true, hidden:false, width: 100, align:'center'});

colNamesT.push('Jenis Atap');
colModelT.push({name:'JENIS_ATAP',index:'JENIS_ATAP', editable: true, hidden:false, width: 75, align:'left'});

colNamesT.push('Tinggi meja ukur (mm)');
colModelT.push({name:'ZERO_CAPACITY',index:'ZERO_CAPACITY', editable: true, hidden:false, width: 100, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_storage").jqGrid(
            {
                url:url+'m_storage/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_storage"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                width: 800,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_storage").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_storage('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_storage('"+cl+"');\"/>";
                            //pr = "<img src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_storage").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
            jGrid_va.navGrid('#pager_storage',{edit:false,del:false,add:false, search: false, refresh: true});
            jGrid_va.navButtonAdd('#pager_storage',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
            jGrid_va.navButtonAdd('#pager_storage',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_storage").ready(loadView); 
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    var kode = jQuery("#txt_kodeSrc").val();

    if (kode == ""){
        kode = "-";
    }
 

    jQuery("#list_storage").setGridParam({url:url+"m_storage/search_data/"+kode}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function addrow(){
    var rowCount = $("#list_storage").getGridParam("reccount");
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
    
    jQuery('#list_storage').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_storage("+i+")'; />"; 
    var su=jQuery("#list_storage").addRowData(i,datArr,'last');
    var act=jQuery("#list_storage").setRowData(i,{act:sv});  
}

function simpan_storage(cl){
    jQuery('#list_storage').saveCell(ids);
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open');
        
        var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
        var data = $("#list_storage").getRowData(ids) ;
        var postdata_id = {};
        postdata_id['ID_STORAGE'] = data.ID_STORAGE;
        postdata_id['PRODUCT_CODE'] = data.PRODUCT_CODE;
        postdata_id['DESCRIPTION'] =  data.DESCRIPTION;
        postdata_id['MAXCAPACITY'] =  data.MAXCAPACITY;
        postdata_id['DIAMETER'] =  data.DIAMETER;
        postdata_id['JENIS_ATAP'] =  data.JENIS_ATAP;
        postdata_id['ZERO_CAPACITY'] =  data.ZERO_CAPACITY;
        postdata_id['CRUD'] =  'ADD';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_storage/CRUD_METHOD',
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

function update_storage(){   
    var answer = confirm ("Update Data Storage dengan ID : " + $("#txt_strgID").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
         
        postdata_id['ID_STORAGE'] = $("#txt_strgID").val();
        postdata_id['PRODUCT_CODE'] = $("#txt_strgProdCode").val();
        postdata_id['DESCRIPTION'] =  $("#txt_strgDesc").val();
        postdata_id['MAXCAPACITY'] =  $("#txt_strgMaxCap").val();
        postdata_id['DIAMETER'] =  $("#txt_strgDiameter").val();
        postdata_id['JENIS_ATAP'] =  $("#txt_strgJnsAtap").val();
        postdata_id['ZERO_CAPACITY'] =  $("#txt_strgZCap").val();

        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_storage/CRUD_METHOD',
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
                        $("#frm_storage").dialog('close');  
                    }
                    
                }
               });    
    }
}

function edit_storage(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_storage").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        
        
        $("#txt_frmMode").val("EDIT");
     
        $("#txt_strgID").val(data.ID_STORAGE);
        $("#txt_strgProdCode").val(data.PRODUCT_CODE);
        $("#txt_strgDesc").val(data.DESCRIPTION);
        $("#txt_strgMaxCap").val(data.MAXCAPACITY);
        $("#txt_strgDiameter").val(data.DIAMETER);
        $("#txt_strgJnsAtap").val(data.JENIS_ATAP);
        $("#txt_strgZCap").val(data.ZERO_CAPACITY);
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_storage").dialog('open');
    } 
}

function hapus_storage(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_storage").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Storage dengan ID : " + data.ID_STORAGE + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_STORAGE'] = data.ID_STORAGE;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_storage/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status);
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status);
                            $("#frm_load").dialog('close');
                            reloadGrid();  
                        }
                    }
                   });        
        }
        
    }     
}
</script>

