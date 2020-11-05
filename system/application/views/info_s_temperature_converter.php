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
            <table id="list_temperature" class="scroll"></table> 
            <div id="pager_temperature" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <!--<input type="button"  id="add_storage" value="Buat Storage baru" class="testBtn" onClick="add_storage()">&nbsp;--> 
    </div>
</div> 

<div id="frm_temperature">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Temperature Converter</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">ID Storage</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" tabindex="1" type="text" id="txt_strgID" name="tmp_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Temperature</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_tmpTemperature" name="tmp_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">BJ</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_tmpBj" name="tmp_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">BJ Correction</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_tmpBjCorrection" name="tmp_input" maxlength="100"/></td>
                </tr>
                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_saveTemp" onclick="update_temperature()">Simpan</button>    
        </div>
        
    </div>
    <input type="hidden" id="txt_frmMode">
    <input type="hidden" id="txt_id">  
</div>

<div id="frm_load">
    Wait...
</div>

<div id="search_form"></div>
</body>

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_temperature").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Temperature Converter",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $("#frm_temperature").dialog('close');        
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
            jQuery("#list_temperature").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_temperature_converter/search_data');
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

jQuery(document).ready(function(){
    $("#txt_strgID")
          .autocomplete( 
            url+"s_temperature_converter/get_storage/", {
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
          ).result(function(e, item) {
            //$("#i_nik_natura").val(item.res_id);
            //$("#i_nama_natura").val(item.res_name );
          });
})
</script>

<script type="text/javascript">
function reloadGrid(){
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_temperature").setGridParam({url:url+'s_temperature_converter/LoadData/'}).trigger("reloadGrid");    
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
colModelT.push({name:'ID_STORAGE',index:'ID_STORAGE', editable: true,width: 140
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                $(elem)
                  .autocomplete(   
                    url+"s_temperature_converter/get_storage", {
                      dataType: 'ajax',
                      multiple: false,
                      autoFill: false,
                      mustMatch: true,
                      matchContains: false,
                
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },
                      formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl :'';
                      }
                    }
                  )
                  
          }}, width: 70, align:'center'}); 

colNamesT.push('Temperature');
colModelT.push({name:'TEMPERATURE',index:'TEMPERATURE', editable: true, hidden:false, width: 75, align:'center'});

colNamesT.push('BJ');
colModelT.push({name:'BJ',index:'BJ', editable: true, hidden:false, width: 140, align:'center'});

colNamesT.push('BJ Correction');
colModelT.push({name:'BJ_CORRECTION',index:'BJ_CORRECTION', editable: true, hidden:false, width: 100, align:'center'});


colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('id');
colModelT.push({name:'ID_ANON',index:'ID_ANON', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_temperature").jqGrid(
            {
                url:url+'s_temperature_converter/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_temperature"),
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
                    var ids = jQuery("#list_temperature").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_temperature('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_temperature('"+cl+"');\"/>";
                            //pr = "<img src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_temperature").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
            jGrid_va.navGrid('#pager_temperature',{edit:false,del:false,add:false, search: false, refresh: true});
            jGrid_va.navButtonAdd('#pager_temperature',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
            jGrid_va.navButtonAdd('#pager_temperature',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_temperature").ready(loadView); 
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
 

    jQuery("#list_temperature").setGridParam({url:url+"s_temperature_converter/search_data/"+kode}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function addrow(){
    var rowCount = $("#list_temperature").getGridParam("reccount");
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
    
    jQuery('#list_temperature').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_temperature("+i+")'; />"; 
    var su=jQuery("#list_temperature").addRowData(i,datArr,'last');
    var act=jQuery("#list_temperature").setRowData(i,{act:sv});  
}

function simpan_temperature(cl){
    jQuery('#list_temperature').saveCell(ids);
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open');
        
        var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
        var data = $("#list_temperature").getRowData(ids) ;
        var postdata_id = {};
        postdata_id['ID_STORAGE'] = data.ID_STORAGE;
        postdata_id['TEMPERATURE']=data.TEMPERATURE;
        postdata_id['BJ']=data.BJ;
        postdata_id['BJ_CORRECTION']=data.BJ_CORRECTION;
        postdata_id['CRUD'] =  'ADD';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_temperature_converter/CRUD_METHOD',
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

function update_temperature(){   
    var answer = confirm ("Update Temperature Converter dengan Temp : " + $("#txt_tmpTemperature").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
         
        postdata_id['TEMPERATURE']=$("#txt_tmpTemperature").val();
        postdata_id['BJ']=$("#txt_tmpBj").val();
        postdata_id['BJ_CORRECTION']=$("#txt_tmpBjCorrection").val();
        postdata_id['ID_ANON']=$("#txt_id").val();

        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_temperature_converter/CRUD_METHOD',
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
                        $("#frm_temperature").dialog('close');  
                    }
                    
                }
               });    
    }
}

function edit_temperature(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_temperature").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        
        
        $("#txt_frmMode").val("EDIT");

        $("#txt_strgID").val(data.ID_STORAGE);
        $("#txt_tmpTemperature").val(data.TEMPERATURE);
        $("#txt_tmpBj").val(data.BJ);
        $("#txt_tmpBjCorrection").val(data.BJ_CORRECTION);
        $("#txt_id").val(data.ID_ANON);
        
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_temperature").dialog('open');
    } 
}

function hapus_temperature(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_temperature").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Temperature Converter dengan Temp : " + data.TEMPERATURE + "c ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_ANON'] = data.ID_ANON;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_temperature_converter/CRUD_METHOD',
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

