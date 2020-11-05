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
                <td class="labelcell">ID Storage</td><td>:</td>
                <td class="fieldcell">
                    <input tabindex="1" class="input" type="text" id="txt_kodeSrc" name="strg_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr>
            <tr>
                <td class="labelcell">Periode</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input class="input" type="text" id='s_tgl_periode' onchange="doSearch(arguments[0]||event)" >
                </td>
            </tr 
        </table> -->
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_sounding" class="scroll"></table> 
            <div id="pager_sounding" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" id="add_kontraktor" onclick="input_sounding()">Input Sounding</button>&nbsp;    
    </div>
</div> 

<div id="frm_sounding">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Storage Reading (Sounding)</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr style="display: none;">
                    <td class="labelcell">ID SOUNDING</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_sndID" name="snd_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">ID TANKI</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" tabindex="2" type="text" id="txt_sndStorageID" name="snd_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">TANGGAL</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_sndDate" name="snd_input" maxlength="50"/> *yyyy-mm-dd</td>
                </tr>
                <tr>
                    <td class="labelcell">JAM</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_sndTime" name="snd_input" maxlength="50"/> *hh:mm:ss</td>
                </tr>
                <tr>
                    <td class="labelcell">TINGGI TABUNG (M)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_sndHeight" name="snd_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">TINGGI KERUCUT (M)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_sndHeight2" name="snd_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">BERAT TAMBAHAN (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="8" type="text" id="txt_extraWeight" name="snd_input" maxlength="25"/>* field tambahan Kg kernel jika bunker full</td> 
                </tr>
                <!--<tr>
                    <td class="labelcell">TEMPERATURE</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_sndTemperature" name="snd_input" maxlength="25"/></td>
                </tr>-->
                <tr style="display: none;">
                    <td class="labelcell">VOLUME</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_sndVolume" name="snd_input" maxlength="25" disabled="disabled"/></td>
                </tr>
                <tr>
                    <td class="labelcell">WEIGHT (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="8" type="text" id="txt_sndWeight" name="snd_input" maxlength="25" disabled="disabled"/></td>
                </tr>
                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_saveFfa" onclick="proses_sounding()">Simpan</button>    
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
     jQuery("#list_sounding").setGridParam({url:url+'s_catat_sounding_kernel/LoadData/'}).trigger("reloadGrid");    
}

function get_periode(){
    var periode = $("#s_tgl_periode").val();
    return periode;
}

function input_sounding(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    $('input').val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_sounding").dialog('open');
}

function proses_sounding(){
    var frmMode = $("#txt_frmMode").val();
    if (frmMode=='ADD'){
        simpan_sounding();    
    }else if(frmMode='EDIT'){
        update_sounding();
    }else{
        alert("Command Unknown");
    }
}
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

colNamesT.push('ID_SOUNDING');
colModelT.push({name:'ID_SOUNDING_KERNEL',index:'ID_SOUNDING_KERNEL', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('ID STORAGE');
colModelT.push({name:'ID_STORAGE',index:'ID_STORAGE', editable: true
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"s_catat_sounding_kernel/get_storage/", {
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
                    //$("#LOCATION_TYPE_CODE").val(item.res_name );
                  });
          }}, width: 90, align:'center'}); 

colNamesT.push('TANGGAL');
colModelT.push({name:'DATE',index:'DATE', editable: true, hidden:false, width: 90, align:'center',formatter: 'date'});

colNamesT.push('JAM');
colModelT.push({name:'TIME',index:'TIME', editable: true, hidden:false, width: 90, align:'center'});

colNamesT.push('TINGGI TABUNG (M)');
colModelT.push({name:'HEIGHT',index:'HEIGHT', editable: true, hidden:false, width: 150, align:'right'});

colNamesT.push('TINGGI KERUCUT (M)');
colModelT.push({name:'HEIGHT2',index:'HEIGHT2', editable: true, hidden:false, width: 150, align:'right'});

//colNamesT.push('VOLUME');
//colModelT.push({name:'VOLUME',index:'VOLUME', editable: false, hidden:false, width: 100, align:'right'});

colNamesT.push('BERAT (Kg)');
colModelT.push({name:'WEIGHT',index:'WEIGHT', editable: true, hidden:false, width: 100, align:'right'});

colNamesT.push('BERAT TAMBAHAN (Kg)');
colModelT.push({name:'EXTRA_WEIGHT',index:'EXTRA_WEIGHT', editable: true, hidden:false, width: 100, align:'right'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_sounding").jqGrid(
            {
                url:url+'s_catat_sounding_kernel/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_sounding"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_sounding").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_sounding('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_sounding('"+cl+"');\"/>";
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_sounding").setRowData(ids[i],{act:be+ce+pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_sounding").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_sounding',{edit:false,del:false,add:false, search: false, refresh: true});
            /*jGrid_va.navButtonAdd('#pager_sounding',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });*/
            jGrid_va.navButtonAdd('#pager_sounding',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_sounding").ready(loadView);
 
function search_test(){
    jQuery("#list_sounding").jqGrid('searchGrid', {sopt:['cn','bw','eq','ne','lt','gt','ew']} );    
}

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
    jQuery("#list_sounding").setGridParam({url:url+"s_catat_sounding_kernel/search_data/"+kode+"/"+get_periode()}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function addrow(){
    var rowCount = $("#list_sounding").getGridParam("reccount");
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
    
    jQuery('#list_sounding').saveCell(i); 
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_sounding("+i+")'; />"; 
    var su=jQuery("#list_sounding").addRowData(i,datArr,'last');
    var act=jQuery("#list_sounding").setRowData(i,{act:sv});  
}

function simpan_sounding(){
    //var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    //var data = $("#list_sounding").getRowData(ids) ;
    //jQuery('#list_sounding').saveCell(ids); 
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        //postdata_id['ID_FFA'] = data.ID_FFA;
        postdata_id['ID_STORAGE'] = $("#txt_sndStorageID").val();
        postdata_id['DATE'] = $("#txt_sndDate").val();
        postdata_id['TIME'] = $("#txt_sndTime").val();
        postdata_id['HEIGHT'] =  $("#txt_sndHeight").val();
		postdata_id['HEIGHT2'] =  $("#txt_sndHeight2").val();
        postdata_id['WEIGHT'] =  $("#txt_sndWeight").val();
		postdata_id['EXTRA_WEIGHT'] =  $("#txt_extraWeight").val();
        //postdata_id['VOLUME'] =  data.VOLUME;
        postdata_id['CRUD'] =  'ADD';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_catat_sounding_kernel/CRUD_METHOD',
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
						$("#frm_sounding").dialog('close'); 
                    }    
                }
           });
    }
}

function update_sounding(){
    
    var answer = confirm ("Update Data Sounding dengan ID : " + $("#txt_sndID").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        postdata_id['ID_SOUNDING_KERNEL'] = $("#txt_sndID").val();
        postdata_id['ID_STORAGE'] = $("#txt_sndStorageID").val();
        postdata_id['DATE'] = $("#txt_sndDate").val();
        postdata_id['TIME'] = $("#txt_sndTime").val();
        postdata_id['HEIGHT'] =  $("#txt_sndHeight").val();
		postdata_id['HEIGHT2'] =  $("#txt_sndHeight2").val();
        postdata_id['WEIGHT'] =  $("#txt_sndWeight").val();
		postdata_id['EXTRA_WEIGHT'] =  $("#txt_extraWeight").val();
       // postdata_id['VOLUME'] = $("#txt_sndVolume").val();
        
        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_catat_sounding_kernel/CRUD_METHOD',
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
                        $("#frm_sounding").dialog('close');    
                    }    
                }
               });    
    }
}

function edit_sounding(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_sounding").getRowData(ids) ;
    jQuery('#list_sounding').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        
        $('input').val('');
        var nDate= formatDate(new Date(getDateFromFormat(data.DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
        $("#txt_frmMode").val("EDIT");
        $("#txt_sndID").val(data.ID_SOUNDING_KERNEL);
        $("#txt_sndStorageID").val(data.ID_STORAGE);
        $("#txt_sndDate").val(nDate);
        $("#txt_sndTime").val(data.TIME)
        $("#txt_sndHeight").val(data.HEIGHT);
		$("#txt_sndHeight2").val(data.HEIGHT2);
        $("#txt_sndWeight").val(data.WEIGHT);
		$("#txt_extraWeight").val(data.EXTRA_WEIGHT);
        //$("#txt_sndVolume").val(data.VOLUME);
    
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_sounding").dialog('open');
    } 
}

function hapus_sounding(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_sounding").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Sounding dengan ID : " + data.ID_SOUNDING_KERNEL + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    
            postdata_id['ID_SOUNDING_KERNEL'] = data.ID_SOUNDING_KERNEL;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_catat_sounding_kernel/CRUD_METHOD',
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
                            $("#frm_sounding").dialog('close');    
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
    
   $("#frm_sounding").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 400,
        width: 610,
        modal: true,
        title: "Product Storage Reading (Sounding)",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $("#frm_sounding").dialog('close');            
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
            jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
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
jQuery(document).ready(function(){
    var url = "<?= base_url().'index.php/' ?>";
    $("#txt_sndStorageID")
          .autocomplete( 
            url+"s_catat_sounding_kernel/get_storage/", {
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
          );
    
     $("#txt_kodeSrc")
          .autocomplete( 
            url+"s_catat_sounding_kernel/get_storage/", {
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
          );
})

$(function() {
    $("#txt_sndDate").datepicker({dateFormat:"yy-mm-dd"});
});
var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');
function LZ(x) {return(x<0||x>9?"":"0")+x}

// ------------------------------------------------------------------
// formatDate (date_object, format)
// Returns a date in the output format specified.
// The format string uses the same abbreviations as in getDateFromFormat()
// ------------------------------------------------------------------
function formatDate(date,format) {
    format=format+"";
    var result="";
    var i_format=0;
    var c="";
    var token="";
    var y=date.getYear()+"";
    var M=date.getMonth()+1;
    var d=date.getDate();
    var E=date.getDay();
    var H=date.getHours();
    var m=date.getMinutes();
    var s=date.getSeconds();
    var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
    // Convert real date parts into formatted versions
    var value=new Object();
    if (y.length < 4) {y=""+(y-0+1900);}
    value["y"]=""+y;
    value["yyyy"]=y;
    value["yy"]=y.substring(2,4);
    value["M"]=M;
    value["MM"]=LZ(M);
    value["MMM"]=MONTH_NAMES[M-1];
    value["NNN"]=MONTH_NAMES[M+11];
    value["d"]=d;
    value["dd"]=LZ(d);
    value["E"]=DAY_NAMES[E+7];
    value["EE"]=DAY_NAMES[E];
    value["H"]=H;
    value["HH"]=LZ(H);
    if (H==0){value["h"]=12;}
    else if (H>12){value["h"]=H-12;}
    else {value["h"]=H;}
    value["hh"]=LZ(value["h"]);
    if (H>11){value["K"]=H-12;} else {value["K"]=H;}
    value["k"]=H+1;
    value["KK"]=LZ(value["K"]);
    value["kk"]=LZ(value["k"]);
    if (H > 11) { value["a"]="PM"; }
    else { value["a"]="AM"; }
    value["m"]=m;
    value["mm"]=LZ(m);
    value["s"]=s;
    value["ss"]=LZ(s);
    while (i_format < format.length) {
        c=format.charAt(i_format);
        token="";
        while ((format.charAt(i_format)==c) && (i_format < format.length)) {
            token += format.charAt(i_format++);
            }
        if (value[token] != null) { result=result + value[token]; }
        else { result=result + token; }
        }
    return result;
    }
    
// ------------------------------------------------------------------
// Utility functions for parsing in getDateFromFormat()
// ------------------------------------------------------------------
function _isInteger(val) {
    var digits="1234567890";
    for (var i=0; i < val.length; i++) {
        if (digits.indexOf(val.charAt(i))==-1) { return false; }
        }
    return true;
    }
function _getInt(str,i,minlength,maxlength) {
    for (var x=maxlength; x>=minlength; x--) {
        var token=str.substring(i,i+x);
        if (token.length < minlength) { return null; }
        if (_isInteger(token)) { return token; }
        }
    return null;
    }
    
// ------------------------------------------------------------------
// getDateFromFormat( date_string , format_string )
//
// This function takes a date string and a format string. It matches
// If the date string matches the format string, it returns the 
// getTime() of the date. If it does not match, it returns 0.
// ------------------------------------------------------------------
function getDateFromFormat(val,format) {
    val=val+"";
    format=format+"";
    var i_val=0;
    var i_format=0;
    var c="";
    var token="";
    var token2="";
    var x,y;
    var now=new Date();
    var year=now.getYear();
    var month=now.getMonth()+1;
    var date=1;
    var hh=now.getHours();
    var mm=now.getMinutes();
    var ss=now.getSeconds();
    var ampm="";
    
    while (i_format < format.length) {
        // Get next token from format string
        c=format.charAt(i_format);
        token="";
        while ((format.charAt(i_format)==c) && (i_format < format.length)) {
            token += format.charAt(i_format++);
            }
        // Extract contents of value based on format token
        if (token=="yyyy" || token=="yy" || token=="y") {
            if (token=="yyyy") { x=4;y=4; }
            if (token=="yy")   { x=2;y=2; }
            if (token=="y")    { x=2;y=4; }
            year=_getInt(val,i_val,x,y);
            if (year==null) { return 0; }
            i_val += year.length;
            if (year.length==2) {
                if (year > 70) { year=1900+(year-0); }
                else { year=2000+(year-0); }
                }
            }
        else if (token=="MMM"||token=="NNN"){
            month=0;
            for (var i=0; i<MONTH_NAMES.length; i++) {
                var month_name=MONTH_NAMES[i];
                if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
                    if (token=="MMM"||(token=="NNN"&&i>11)) {
                        month=i+1;
                        if (month>12) { month -= 12; }
                        i_val += month_name.length;
                        break;
                        }
                    }
                }
            if ((month < 1)||(month>12)){return 0;}
            }
        else if (token=="EE"||token=="E"){
            for (var i=0; i<DAY_NAMES.length; i++) {
                var day_name=DAY_NAMES[i];
                if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
                    i_val += day_name.length;
                    break;
                    }
                }
            }
        else if (token=="MM"||token=="M") {
            month=_getInt(val,i_val,token.length,2);
            if(month==null||(month<1)||(month>12)){return 0;}
            i_val+=month.length;}
        else if (token=="dd"||token=="d") {
            date=_getInt(val,i_val,token.length,2);
            if(date==null||(date<1)||(date>31)){return 0;}
            i_val+=date.length;}
        else if (token=="hh"||token=="h") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<1)||(hh>12)){return 0;}
            i_val+=hh.length;}
        else if (token=="HH"||token=="H") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<0)||(hh>23)){return 0;}
            i_val+=hh.length;}
        else if (token=="KK"||token=="K") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<0)||(hh>11)){return 0;}
            i_val+=hh.length;}
        else if (token=="kk"||token=="k") {
            hh=_getInt(val,i_val,token.length,2);
            if(hh==null||(hh<1)||(hh>24)){return 0;}
            i_val+=hh.length;hh--;}
        else if (token=="mm"||token=="m") {
            mm=_getInt(val,i_val,token.length,2);
            if(mm==null||(mm<0)||(mm>59)){return 0;}
            i_val+=mm.length;}
        else if (token=="ss"||token=="s") {
            ss=_getInt(val,i_val,token.length,2);
            if(ss==null||(ss<0)||(ss>59)){return 0;}
            i_val+=ss.length;}
        else if (token=="a") {
            if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="AM";}
            else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="PM";}
            else {return 0;}
            i_val+=2;}
        else {
            if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
            else {i_val+=token.length;}
            }
        }
    // If there are any trailing characters left in the value, it doesn't match
    if (i_val != val.length) { return 0; }
    // Is date valid for month?
    if (month==2) {
        // Check for leap year
        if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
            if (date > 29){ return 0; }
            }
        else { if (date > 28) { return 0; } }
        }
    if ((month==4)||(month==6)||(month==9)||(month==11)) {
        if (date > 30) { return 0; }
        }
    // Correct hours value
    if (hh<12 && ampm=="PM") { hh=hh-0+12; }
    else if (hh>11 && ampm=="AM") { hh-=12; }
    var newdate=new Date(year,month-1,date,hh,mm,ss);
    return newdate.getTime();
    }
</script>

