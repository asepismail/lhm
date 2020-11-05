<? 
    $template_path = base_url().$this->config->item('template_path');  
?>

<br>
<div id='main_form'>
    <div id"gridSearch">  
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_production" class="scroll"></table> 
            <div id="pager_production" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="add_production()">Input Data Produksi</button>&nbsp; 
    </div>
</div> 

<div id="frm_production">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Data Produksi Pabrik</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="fieldcell"><input  style="width: 150px;" type="hidden" id="txt_idProduction" name="production_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">TANGGAL</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" tabindex="1" type="text" id="txt_productionDate" name="txt_productionDate" maxlength="50"/></td>                 
                </tr>
                </tr>
                <tr>
                    <td class="labelcell">JENIS BARANG</td><td>:</td>
                    <td class="fieldcell">
                        <input tabindex="2" type="text" id="txt_IDcommodity" name="production_input" maxlength="50"/>
                    </td>
                    <td class="fieldcell">
                        <input type="text" id="txt_commodity" name="production_input" maxlength="50" disabled="disabled"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">BERAT (kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_weight" name="dptch_input" maxlength="25"/></td>
                </tr>                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_saveProduction" onclick="save_production()" tabindex="4">Simpan</button>    
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
    $('input').val('');
    
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    $("#frm_production").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 320,
        width: 570,
        modal: true,
        title: "DATA PRODUKSI",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() {
                        $('input').val('');
                        $("#frm_production").dialog('close');        
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
            jQuery("#list_production").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_production/search_data');
        }
    });
});

$(function() {
    
    $("#txt_productionDate").datepicker({dateFormat:"yy-mm-dd"}); 
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
    //var url = "<?= base_url().'index.php/' ?>";
     /*$("#txt_IDcommodity")
          .autocomplete( 
            url+"s_production/get_commodity/", {
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
          );*/
    
    $("#txt_IDcommodity")
          .autocomplete( 
            url+"s_production/get_commodity/", {
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
                $('#txt_commodity').val(item.res_name);
               
          }); 
})

</script>

<script type="text/javascript">
function reloadGrid(){
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_production").setGridParam({url:url+'s_production/LoadData/'}).trigger("reloadGrid");    
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
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    //var kode = jQuery("#txt_kodeSrc").val();
    var periode = jQuery("#txt_productionDate").val();
    var jenis = jQuery("#txt_commodity").val();
    if (kode == ""){
        kode = "-";
    }
    if (periode == ""){
        periode = "-";
    }
    if (jenis == ""){
        jenis = "-";
    }
 

    //jQuery("#list_production").setGridParam({url:url+"s_production/search_data/"+kode+"/"+periode+"/"+jenis}).trigger("reloadGrid");        
}
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

colNamesT.push('ID_PRODUCTION');
colModelT.push({name:'ID_PRODUCTION',index:'ID_PRODUCTION', editable: true, hidden:true, width: 20, align:'left'});

colNamesT.push('TGL PRODUKSI');
colModelT.push({name:'PRODUCTION_DATE',index:'PRODUCTION_DATE', editable: false, hidden:false, width: 130, align:'center',formatter: 'date'});

colNamesT.push('ID KOMODITAS');
colModelT.push({name:'ID_COMMODITY',index:'ID_COMMODITY', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('JENIS BARANG');
colModelT.push({name:'DESKRIPSI',index:'DESKRIPSI', editable: false, hidden:false, width: 250, align:'left'});

colNamesT.push('BERAT (Kg)');
colModelT.push({name:'WEIGHT',index:'WEIGHT', editable: false, hidden:false, width: 100, align:'right'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_production").jqGrid(
            {
                url:url+'s_production/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_production"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_production").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img alt='Edit data' style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_production('"+cl+"');\" />"; 
                            ce = "<img alt='Hapus data' style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"delete_production('"+cl+"');\"/>";
                            //pr = "<img src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_production").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
            //jGrid_va.navGrid('#pager_dispatch',{edit:false,del:false,add:false, refresh: true},{},{},{},{multipleSearch:true});
            jGrid_va.navGrid('#pager_production',{edit:false,del:false,add:false,search:false,refresh:true});
            jGrid_va.navButtonAdd('#pager_production',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });
           
         }
jQuery("#list_production").ready(loadView);
</script>

<script type="text/javascript">
function add_production(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    $('input').val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_production").dialog('open');    
}

function save_production(){
    var answer;
    if ($("#txt_frmMode").val()=='ADD'){
        answer = confirm ("Tambah data produksi ?" )    
    }else if($("#txt_frmMode").val()=='EDIT'){
        answer = confirm ("Ubah data produksi ?" )
    }else{
        answer = confirm ("Tambah/Ubah data produksi ?" )
    }
    
    if (answer){
        $("#frm_load").dialog('open');
        
        var postdata_id = {};
        postdata_id['ID_PRODUCTION'] = $("#txt_idProduction").val();
        postdata_id['PRODUCTION_DATE'] = $("#txt_productionDate").val();
        postdata_id['ID_COMMODITY'] =  $("#txt_IDcommodity").val();
        postdata_id['WEIGHT'] =  $("#txt_weight").val();
        postdata_id['CRUD'] =  $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_production/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        $("#frm_production").dialog('close');
                        reloadGrid();  
                    }
                }
           });
    }
}

function edit_production(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_production").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
		var nDate= formatDate(new Date(getDateFromFormat(data.PRODUCTION_DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
        $('input').val('');
        $("#txt_frmMode").val("EDIT");
     
        $("#txt_idProduction").val(data.ID_PRODUCTION);
        $("#txt_productionDate").val(nDate);
        $("#txt_IDcommodity").val(data.ID_COMMODITY);
		$("#txt_commodity").val(data.DESKRIPSI);
        $("#txt_weight").val(data.WEIGHT);
        
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_production").dialog('open');
    } 
}

function delete_production(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_production").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus production dengan ID : " + data.ID_PRODUCTION + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_PRODUCTION'] = data.ID_PRODUCTION;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_production/CRUD_METHOD',
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

