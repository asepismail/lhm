<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div> 
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td  colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labecell">Kode Produk</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="1" class="input" type="text" id="txt_kodeSrc" name="strg_input" maxlength="100" onchange="doSearch(arguments[0]||event)"/>
                </td>
            </tr> 
            <tr>
                <td class="labecell">Periode Catat</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="1" class="input" type="text" id="txt_periodeSrc" name="strg_input" maxlength="100" onchange="doSearch(arguments[0]||event)"/>
                </td>
            </tr>
        </table>-->
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_ffa" class="scroll"></table> 
            <div id="pager_ffa" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
         <button class="testBtn" type="submit" id="add_kontraktor" onclick="input_ffa()">Input FFA</button>&nbsp; 
    </div>
</div> 

<div id="frm_ffa">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail FFA</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr style="display: none;">
                    <td class="labelcell">ID FFA</td><td>:</td>
                    <td class="fieldcell">
                        <input disabled="disabled" style="width: 150px;" tabindex="1" type="text" id="txt_ffaID" name="ffa_input" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">ID Storage</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" tabindex="2" type="text" id="txt_ffaStorageID" name="ffa_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">DATE</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_ffaDate" name="ffa_input" maxlength="50"/> *yyyy-mm-dd</td>
                </tr>
                <tr>
                    <td class="labelcell">FFA (%)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_ffa" name="ffa_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">MOISTURE (%)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_ffaMoisture" name="ffa_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">DIRT (%)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_ffaDirt" name="ffa_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">WATER CONTENT (%)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_ffaWaterContent" name="ffa_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">REMARKS</td><td>:</td>
                    <td class="fieldcell">
                        <textarea tabindex="8" style="height:65px; width:300px;" class="input" id="txt_ffaRemarks" name="ffa_input"></textarea>
                    </td>
                </tr>
                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_saveFfa" onclick="proses_ffa()">Simpan</button>    
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
     jQuery("#list_ffa").setGridParam({url:url+'s_catat_ffa/LoadData/'}).trigger("reloadGrid");    
}

$(function() {
    //################# DATE PICKER SETTING #####################
    $("#txt_periodeSrc").datepicker({dateFormat:"yy-mm-dd"});
    $("#txt_ffaDate").datepicker({dateFormat:"yy-mm-dd"});
});

function input_ffa(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    $('input').val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_ffa").dialog('open');
}

function proses_ffa(){
    var frmMode = $("#txt_frmMode").val();
    if (frmMode=='ADD'){
        simpan_ffa();    
    }else if(frmMode='EDIT'){
        update_ffa();
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
  
function clear_form_elements(ele) 
{
    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
    //$("#frm_nota").dialog('close');
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

colNamesT.push('ID_FFA');
colModelT.push({name:'ID_FFA',index:'ID_FFA', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('ID_STORAGE');
colModelT.push({name:'ID_STORAGE',index:'ID_STORAGE', editable: true
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"s_catat_ffa/get_storage/", {
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
          }}, width: 70, align:'center'}); 

colNamesT.push('TANGGAL');
colModelT.push({name:'DATE',index:'DATE', editable: true, hidden:false, width: 140, align:'center', formatter:'date'});

colNamesT.push('FFA (%)');
colModelT.push({name:'FFA',index:'FFA', editable: true, hidden:false, width: 120, align:'left'});

colNamesT.push('MOISTURE (%)');
colModelT.push({name:'MOISTURE',index:'MOISTURE', editable: true, hidden:false, width: 120, align:'left'});

colNamesT.push('DIRT (%)');
colModelT.push({name:'DIRT',index:'DIRT', editable: true, hidden:false, width: 120, align:'left'});

colNamesT.push('WATER_CONTENT (%)');
colModelT.push({name:'WATER_CONTENT',index:'WATER_CONTENT', editable: true, hidden:false, width: 120, align:'left'});

colNamesT.push('REMARKS');
colModelT.push({name:'REMARKS',index:'REMARKS', editable: true, hidden:false, width: 120, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_ffa").jqGrid(
            {
                url:url+'s_catat_ffa/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_ffa"),
                rowNum: 400,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_ffa").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_ffa('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;  title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_ffa('"+cl+"');\"/>";
                            pr = "<img title='Print' src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_ffa").setRowData(ids[i],{act:be+ce+pr}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='DATE')
                      { jQuery("#"+iRow+"_DATE","#list_ffa").datepicker({dateFormat:"yy-mm-dd"});} 
                    }
            });
            jGrid_va.navGrid('#pager_ffa',{edit:false,del:false,add:false, search: false, refresh: true});
            /*jGrid_va.navButtonAdd('#pager_ffa',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });*/
            jGrid_va.navButtonAdd('#pager_ffa',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });   
         }
jQuery("#list_ffa").ready(loadView); 
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false;
 
function get_periode(){
    var periode = $("#txt_periodeSrc").val();
    return periode;
}

function get_data(){
    var periode = $("#txt_periodeSrc").val();    
    jQuery("#list_ffa").setGridParam({url:url+"s_catat_ffa/LoadData/"}).trigger("reloadGrid");
  
}

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    /*var kode = jQuery("#txt_kodeSrc").val();
    var periode = jQuery("#txt_periodeSrc").val();
    if (kode == ""){
        kode = "-";
    }
    if (periode == ""){
        periode = "-";
    }*/
 

    jQuery("#list_ffa").setGridParam({url:url+"s_catat_ffa/LoadData/"}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function addrow(){
    var rowCount = $("#list_ffa").getGridParam("reccount");
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
    
    jQuery('#list_ffa').saveCell(i);
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_ffa("+i+")'; />"; 
    var su=jQuery("#list_ffa").addRowData(i,datArr,'last');
    var act=jQuery("#list_ffa").setRowData(i,{act:sv});  
}

function simpan_ffa(){
    //var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    //var data = $("#list_ffa").getRowData(ids) ;
    
    //jQuery('#list_ffa').saveCell(ids);
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open')
        var postdata_id = {};
        //postdata_id['ID_FFA'] = data.ID_FFA;
        postdata_id['ID_STORAGE'] = $("#txt_ffaStorageID").val();
        postdata_id['DATE'] = $("#txt_ffaDate").val();
        postdata_id['FFA'] = $("#txt_ffa").val();
        postdata_id['MOISTURE'] =  $("#txt_ffaMoisture").val();
        postdata_id['DIRT'] =  $("#txt_ffaDirt").val();
        postdata_id['WATER_CONTENT'] = $("#txt_ffaWaterContent").val();
        postdata_id['REMARKS'] = $("#txt_ffaRemarks").val();
        postdata_id['CRUD'] =  'ADD';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_catat_ffa/CRUD_METHOD',
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
                            $("#frm_ffa").dialog('close');   
                        } 
                }
        });
    }
}

function update_ffa(){
    var answer = confirm ("Update Data FFA dengan ID : " + $("#txt_ffaID").val() + " ?");
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        
        postdata_id['ID_FFA'] = $("#txt_ffaID").val();
        postdata_id['ID_STORAGE'] = $("#txt_ffaStorageID").val();
        postdata_id['DATE'] = $("#txt_ffaDate").val();
        postdata_id['FFA'] = $("#txt_ffa").val();
        postdata_id['MOISTURE'] =  $("#txt_ffaMoisture").val();
        postdata_id['DIRT'] =  $("#txt_ffaDirt").val();
        postdata_id['WATER_CONTENT'] = $("#txt_ffaWaterContent").val();
        postdata_id['REMARKS'] = $("#txt_ffaRemarks").val();
        
        postdata_id['CRUD'] =   $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_catat_ffa/CRUD_METHOD',
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
                            $("#frm_ffa").dialog('close');    
                        } 
                },
					error:function (xhr, ajaxOptions, thrownError){
							alert(xhr.status);
							alert(thrownError);
						} 
        });    
    }
}

function edit_ffa(cl){
	
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ffa").getRowData(ids) ;
    jQuery('#list_ffa').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $('input').val('');
        var nDate= formatDate(new Date(getDateFromFormat(data.DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
        $("#txt_frmMode").val("EDIT");
        $("#txt_ffaID").val(data.ID_FFA);
        $("#txt_ffaStorageID").val(data.ID_STORAGE);
        $("#txt_ffaDate").val(nDate);
        $("#txt_ffa").val(data.FFA);
        $("#txt_ffaMoisture").val(data.MOISTURE);
        $("#txt_ffaDirt").val(data.DIRT);
        $("#txt_ffaWaterContent").val(data.WATER_CONTENT);
        $("#txt_ffaRemarks").val(data.REMARKS);
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_ffa").dialog('open');
    } 
}

function hapus_ffa(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ffa").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus FFA dengan ID : " + data.ID_FFA + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
    
            postdata_id['ID_FFA'] = data.ID_FFA;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_catat_ffa/CRUD_METHOD',
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

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_ffa").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Product Quality (FFA)",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $(this).dialog('close');       
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
            jQuery("#list_ffa").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_ffa/search_data');
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
    $("#txt_ffaStorageID")
          .autocomplete( 
            url+"s_catat_ffa/get_storage/", {
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
</script>
