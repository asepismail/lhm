<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_ppk" class="scroll"></table> 
            <div id="pager_ppk" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <!--<input type="button"  id="add_storage" value="Buat Storage baru" class="testBtn" onClick="add_storage()">&nbsp;--> 
    </div>
</div> 

<div id="frm_ppk">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Cost per-KG per-AFD</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table_ppkdetail" >
                <tr>
                    <td class="labelcell">AFD</td><td>:</td>
                    <td class="fieldcell">
                        <input style="width: 150px;" tabindex="1" type="text" id="txt_bcostAfd" name="bcost_input" maxlength="25"/>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Cost</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_bcostCost" name="bcost_input" maxlength="25"/></td>
                </tr>
                
                <tr>
                    <td class="labelcell">Status</td><td>:</td>
                    <td class="fieldcell"><input tabindex="16" type="text" id="txt_bcostStatus" name="bcost_input" maxlength="25"/></td>
                    <td class="fieldcell">
                        <!-- Login Starts Here -->
                        <div id="loginContainer">
                            <a href="#" id="loginButton"><span>Ubah</span><em></em></a>
                            <div style="clear:both"></div>
                            <div id="loginBox">                
                               <form id="loginForm">
                                    <fieldset id="body">
                                        <fieldset>
                                            <label for="checkbox"><input type="checkbox" id="checkbox1" name="chkActive"/>Active</label>
                                            <label for="checkbox"><input type="checkbox" id="checkbox2" name="chkClose"/>In-Active</label>
                                            <button type="submit" onclick="setActive()">Active</button>
                                            <button type="submit" onclick="setClose()">In-Active</button>
                                        </fieldset>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <!-- Login Ends Here -->
                    </td>
                </tr>
            </table>
                
        </div>

        <br>
        
    </div>
    <!--<button class="testBtn" type="submit" id="cmd_savePPK" onclick="simpan_ppk()">Simpan</button>-->
    <input type="hidden" id="txt_frmMode">
    <input type="hidden" id="txt_idanon"> 
    <input type="hidden" id="txt_bcostStatusEdit"> 
</div>
<div id="frm_load">
    Wait...
</div>

<div id="search_form"></div>
</body>

<script type="text/javascript">
$(function() {
    var button = $('#loginButton');
    var box = $('#loginBox');
    var form = $('#loginForm');
    button.removeAttr('href');
    button.mouseup(function(login) {
        box.toggle();
        button.toggleClass('active');
    });
    form.mouseup(function() { 
        return false;
    });
    $(this).mouseup(function(login) {
        if(!($(login.target).parent('#loginButton').length > 0)) {
            button.removeClass('active');
            box.hide();
        }
    });
});

function setActive(){
        $('input[name=chkActive]').attr('checked','checked');
        $("#txt_bcostStatusEdit").val('1');
        $("#txt_bcostStatus").val('Active'); 
        if ($('input[name=chkClose]').is(':checked')==true) {
            $('input[name=chkClose]').removeAttr('checked');    
        }  
}
function setClose(){
        $("#txt_bcostStatusEdit").val('0');
        $("#txt_bcostStatus").val('In-Active');
        $('input[name=chkClose]').attr('checked','checked'); 
        if ($('input[name=chkActive]').is(':checked')==true) {
            $('input[name=chkActive]').removeAttr('checked');    
        }   
}
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
   $("#frm_ppk").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 400,
        width: 600,
        modal: true,
        title: "PPK",
        resizable: true,
        moveable: true,
        buttons: {
            Simpan: function() 
                    {
                        simpan_ppk();        
                    },
            Tutup: function() 
                    {
                        clear_form_elements(this.form);      
                        $("#frm_ppk").dialog('close');  
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
            jQuery("#list_ppk").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_bjr_cost/search_data');
        }
    });
    
    $("#txt_bcostAfd").autocomplete( 
            url+"s_bjr_cost/get_afdeling", {
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
     jQuery("#list_ppk").setGridParam({url:url+'s_bjr_cost/LoadData/'}).trigger("reloadGrid");    
}

$(function() {
    //################# DATE PICKER SETTING #####################
    $("#txt_ppkTanggal").datepicker({dateFormat:"yy-mm-dd"});

});
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
  
function clear_form_elements(ele) 
{
    $('input[name=chkActive]').removeAttr('checked');
    $('input[name=chkClose]').removeAttr('checked');
    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
                $(this).val('');
                break;
            case 'textarea':
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
    //$("#frm_nota").dialog('close');
}
</script>
   
<script type="text/javascript">
var url = "<?= base_url() ?>";  
var jGrid_ppk = null;
var colNamesT = new Array();
var colModelT = new Array();

var colNamesN = new Array();
var colModelN = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_ANON');
colModelT.push({name:'ID_ANON',index:'ID_ANON', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('AFD');
colModelT.push({name:'AFD',index:'AFD', editable: true, hidden:false, width: 125, align:'center', formatter:'date Y-m-d'});

colNamesT.push('Cost (Rp)');
colModelT.push({name:'COST',index:'COST', editable: true, hidden:false, width: 175, align:'left'});

colNamesT.push('Status');
colModelT.push({name:'ACTIVE2',index:'ACTIVE2', editable: true, hidden:false, width: 175, align:'left'});

colNamesT.push('stat');
colModelT.push({name:'ACTIVE',index:'ACTIVE', editable: true, hidden:true, width: 175, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_ppk = jQuery("#list_ppk").jqGrid(
            {
                url:url+'s_bjr_cost/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_ppk"),
                rowNum: 400,
                rownumbers: true,
                height: 250,
                width: 625,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_ppk").getDataIDs();
                    for(var i=0;i<ids.length;i++){ 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                             be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_ppk('"+cl+"');\" />";     
                             ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_ppk('"+cl+"');\"/>";   
                            jQuery("#list_ppk").setRowData(ids[i],{act:be+ce})                    
                    }
                                            
                }
            });
            jGrid_ppk.navGrid('#pager_ppk',{edit:false,del:false,add:false,refresh: true,search:false});
            jGrid_ppk.navButtonAdd('#pager_ppk',{
               caption:"Tambah Detail", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        //addrow()
                        add_ppk();
               }, 
               position:"left"
            });
            jGrid_ppk.navButtonAdd('#pager_ppk',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });
         } 
jQuery("#list_ppk").ready(loadView); 

</script>


<script type="text/javascript">
var timeoutHnd; 
var flAuto = false;
 
function get_periode(){
    var periode = $("#txt_periodeSrc").val();
    return periode;
}

/*function get_data(){
    var periode = $("#txt_periodeSrc").val();    
    jQuery("#list_ppk").setGridParam({url:url+"s_bjr_cost/LoadData/"}).trigger("reloadGrid");
  
}*/

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
 
    jQuery("#list_ppk").setGridParam({url:url+"s_bjr_cost/LoadData/"}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
function add_ppk(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    jQuery("#list_ppkDetail").setGridParam({url:url+"s_bjr_cost/LoadData_Detail/"}).trigger("reloadGrid"); 
    
    $('input').val('');
    clear_form_elements(this.form);
    $("#txt_frmMode").val("ADD");
    $("#txt_ppkStatusEdit").val("1");
    setActive();
    $("#frm_ppk").dialog('open');
}

function addrow(){
    var rowCount = $("#list_ppkDetail").getGridParam("reccount");
    var i;
    var method = 'ADD';
    if(rowCount==null || rowCount==0){
        i=i+1;    
    }else{
        i=rowCount+1;
    }
        
    var datArr = {};
    if (i>1){
        var datArr = {PR_NUMBER:jdesc1};
    }
    
    //jQuery('#list_prDetail').saveCell(i);
    sv = "<img src='<?= $template_path ?>NEWGRID/themes/images/disc.png' width='12px' height='13px' onclick='simpan_ppkDetail("+i+")'; />"; 
    var su=jQuery("#list_ppkDetail").addRowData(i,datArr,'last');
    var act=jQuery("#list_ppkDetail").setRowData(i,{act:sv});
}

function simpan_ppk(cl){
    if ($("#txt_frmMode").val()=='ADD'){
        var answer = confirm ("Tambah data Cost BJR ?" )    
    }else if($("#txt_frmMode").val()=='EDIT'){
        var answer = confirm ("Ubah data PPK ?" )
    }else{
        var answer = confirm ("Tambah/Ubah data PPK ?" )
    }
    
    if (answer){
        $("#frm_load").dialog('open')
        var postdata_id = {};
        
        if($("#txt_frmMode").val()=='EDIT'){
            postdata_id['ID_ANON'] = $("#txt_idanon").val();
            postdata_id['ACTIVE'] = $("#txt_bcostStatusEdit").val();
        }
        postdata_id['AFD'] = $("#txt_bcostAfd").val();
        postdata_id['COST'] = $("#txt_bcostCost").val();

        postdata_id['CRUD'] =  $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_bjr_cost/CRUD_METHOD',
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


function edit_ppk(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ppk").getRowData(ids) ;
    jQuery('#list_ppk').saveCell(ids);
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        clear_form_elements(this.form);
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#txt_frmMode").val("EDIT");
        $("#txt_bcostStatusEdit").val(data.ACTIVE)
        
        jQuery("#list_ppkDetail").setGridParam({url:url+"s_bjr_cost/LoadData_Detail/"+data.ID_ANON}).trigger("reloadGrid");
        
        if (data.ACTIVE==1){
            $('input[name=chkActive]').attr('checked','checked');     
        } else if(data.ACTIVE==0){
            $('input[name=chkClose]').attr('checked','checked'); 
        }
        $("#txt_idanon").val(data.ID_ANON);
        $("#txt_bcostAfd").val(data.AFD);
        $("#txt_bcostCost").val(data.COST);

        $("#txt_bcostStatus").val(data.ACTIVE2);  
        $("#frm_ppk").dialog('open');
    } 
}

function hapus_ppk(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_ppk").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Bjr Cost Untuk AFD : " + data.AFD + " ?" );
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
                    url:            url+'s_bjr_cost/CRUD_METHOD',
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



