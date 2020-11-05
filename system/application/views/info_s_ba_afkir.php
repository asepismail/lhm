<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td>:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr> 
            <tr>
                <td class="labelcell">No BA</td><td>:</td>
                <td class="fieldcell">       
                    <input tabindex="2" type="text" id="txt_spbSrc" name="nt_input" maxlength="50" onkeydown="doSearch(arguments[0]||event)"/>
                </td>     
            </tr>
        </table>
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_notaangkut" class="scroll"></table> 
            <div id="pager_notaangkut" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="add_nota()">Input BA Afkir</button>&nbsp;
    </div>
</div> 

<div id="frm_nota">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Data BA Janjang Afkir</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">Tanggal BA</td><td>:</td>
                    <td class="fieldcell">
                        <input tabindex="1" type="text" id="txt_tglBa" name="nt_input" maxlength="50"/>
                        <input tabindex="4" type="hidden" id="txt_idBa" name="nt_input" maxlength="50"/> 
                        <input tabindex="5" type="hidden" id="txt_status" name="nt_input" maxlength="50"/> 
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">No BA</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_noBa" name="nt_input" maxlength="100"/></td>
                    <td class="fieldcell"></td>  
                </tr>  
                <tr>
                    <td class="labelcell">Keterangan BA</td><td>:</td>
                    <td class="fieldcell">       
                        <textarea tabindex="3" name="nt_input" id="txt_desc" cols="100" rows="4"></textarea>
                    </td>     
                </tr>                                              
            </table>
            <br>
            <div id="notaGrid">
                <table id="list_notaDetail" class="scroll"></table> 
                <div id="pager_notaDetail" class="scroll" style="text-align:center;"></div>
            </div>
            <div id="btn_section">
                <button class="testBtn" type="submit" id="cmd_newNote" onclick="simpan_nota()">Simpan</button>&nbsp;
                <button class="testBtn" type="submit" id="cmd_appNote" onclick="approve()">Approve</button>&nbsp;
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

<div id="frm_report"></div>
<div id="search_form"></div>
</body>

<script type="text/javascript">      
jQuery(document).ready(function()
{ 
    jQuery("#txt_tglBa").datepicker({dateFormat:"yy-mm-dd"});

    $('input').val('');

    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $("#frm_nota").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 480,
        width: 900,
        modal: true,
        title: "BERITA ACARA JANJANG AFKIR",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $('textarea').val('');
                        $("#frm_nota").dialog('close');        
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
        },
        open:function(){
            jQuery("#list_notaangkut").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_ba_afkir/search_data');
        }
    });
    
});

jQuery(document).ready(function(){
    $('#tahun').change(function() {
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        reloadGrid(); 
    });
})

function getTglSpb(){
        var tanggal=$("#txt_tglBa").val();
        return tanggal;
    }

function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}
</script>

<script type="text/javascript">
function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_notaangkut").setGridParam({url:url+'s_ba_afkir/LoadData/'+get_periode()}).trigger("reloadGrid");    
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

var colNamesT_Detail = new Array();
var colModelT_Detail = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';

colNamesT.push('No.');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID');
colModelT.push({name:'ID_BA',index:'ID_BA', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('NO BA');
colModelT.push({name:'NO_BA',index:'NO_BA', editable: false, hidden:false, width: 120, align:'left'});

colNamesT.push('TANGGAL');
colModelT.push({name:'BA_DATE',index:'BA_DATE', editable: false, hidden:false, width: 60, align:'left', formatter:'date'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('KETERANGAN');
colModelT.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:false, width: 250, align:'left'});

colNamesT.push('STATUS');
colModelT.push({name:'STATUS',index:'STATUS', editable: false, hidden:false, width: 100, align:'center'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_notaangkut").jqGrid(
            {
                url:url+'s_ba_afkir/LoadData/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_notaangkut"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
				width: 900,
                imgpath: gridimgpath,
                sortorder: "asc",
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_notaangkut").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
							ap = "<img style='padding-right:6px;' title='Approve' src='<?= $template_path ?>themes/base/images/row_edit.gif' width='12px' height='13px' onclick=\"approve_ba('"+cl+"');\" />";
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_nota('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_nota('"+cl+"');\"/>";
                            pr_adb = "<img src='<?= $template_path ?>themes/base/images/adobe-print.png' width='12px' height='13px' onclick=\"print_nota_adb('"+cl+"');\"/> &nbsp;";
                            pr_xls = "<img src='<?= $template_path ?>themes/base/images/xls-print.png' width='12px' height='13px' onclick=\"print_nota_xls('"+cl+"');\"/>"; 
                            jQuery("#list_notaangkut").setRowData(ids[i],{act:ap+be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_notaangkut',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_notaangkut',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });  
         }
jQuery("#list_notaangkut").ready(loadView); 
</script>

<script type="text/javascript">
function giveLocType(){        
    var ids = jQuery("#list_notaDetail").getGridParam('selrow'); 
    var rets = jQuery("#list_notaDetail").getRowData(ids); 
    var type = rets.AFD;
    return type;
}

function giveDate(){        
    var ids = jQuery("#list_notaDetail").getGridParam('selrow'); 
    var rets = jQuery("#list_notaDetail").getRowData(ids); 
    var type = rets.TANGGAL_PANEN;
    return type;
}
        
colNamesT_Detail.push('no');
colModelT_Detail.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_Detail.push('ID');
colModelT_Detail.push({name:'ID',index:'ID', sortable:false, resizable:true, editable: false,hidden:true, width: 10, align:'center'});

colNamesT_Detail.push('ID_BA');
colModelT_Detail.push({name:'ID_BA',index:'ID_BA', sortable:false, resizable:true, editable: false,hidden:true, width: 10, align:'center'});

colNamesT_Detail.push('AFD');
colModelT_Detail.push({name:'AFD',index:'AFD', editable: true,hidden:false,width: 10
,async: false,edittype: "text",editoptions:{
                size:20,
                maxlength:20,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"s_ba_afkir/get_afdeling", {
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
                  .result(function(e, item) {
                    $("#BLOCK").val(item.res_name );
                  });
          }}, width: 20, align:'center'}); 

colNamesT_Detail.push('TGL PANEN');
colModelT_Detail.push({name:'TANGGAL_PANEN',index:'TANGGAL_PANEN', editable: true,hidden:false, width: 45, align:'center', formatter:'date:yyyy-mm-dd'}); 

colNamesT_Detail.push('BLOCK');
colModelT_Detail.push({name:'BLOCK',index:'BLOCK', editable: true 
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete(
						url+"s_ba_afkir/get_block/"+giveLocType()+"/"+giveDate(), { 
                          dataType: 'ajax',
                          width:350,
                          multiple: false,
                          limit:200,
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
                      });    
          }}, width: 40, align:'left'}); 

colNamesT_Detail.push('JJG');
colModelT_Detail.push({name:'JANJANG',index:'JANJANG', editable: true, hidden:false, width: 50, align:'right'});

colNamesT_Detail.push('KETERANGAN');
colModelT_Detail.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true, hidden:false, width: 90, align:'left'});

var loadView_detail = function()
        {
        jGrid_va = jQuery("#list_notaDetail").jqGrid(
            {
                url:url+'s_ba_afkir/LoadData_Detail/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_Detail ,
                colModel: colModelT_Detail,
                sortname: colModelT_Detail[1].name,
                pager:jQuery("#pager_notaDetail"),
                rowNum: 20,
                rownumbers: true,
                height: 100,
                width:830,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellsubmit : 'clientArray',
                cellEdit: true,
                beforeSubmitCell: function() {
                  
                },  
                forceFit : true , 
                
                afterEditCell: function (id,name,val,iRow,iCol){             
                     if(name=='TANGGAL_PANEN') 
                        { jQuery("#"+iRow+"_TANGGAL_PANEN","#list_notaDetail").datepicker({dateFormat:"yy-mm-dd"}); } 
                }
            });
         jGrid_va.navGrid('#pager_notaDetail',{edit:false,del:false,add:false, search: false, refresh: true});
            
         jGrid_va.navButtonAdd('#pager_notaDetail',{
               caption:"Tambah", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
		 
		 jGrid_va.navButtonAdd('#pager_notaDetail',{
               caption:"Hapus", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        deleterow()
               }, 
               position:"left"
            });
         
         }
         jQuery("#list_notaDetail").ready(loadView_detail);


function remove_row(){
    var rowID = jQuery("#list_notaDetail").getGridParam('selrow');

    if( rowID != null ) jQuery("#list_notaDetail").delGridRow(rowID,{reloadAfterSubmit:false}); 
    else alert("Please Select Row to delete!");
}

function deleterow(){
            
            var postdata = {}; 
            var ids = jQuery("#list_notaDetail").getGridParam('selrow'); 
            var data = $("#list_notaDetail").getRowData(ids) ;
			var mag = data.ID_BA;
			
			if (data.ID_BA == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
			}else{
				var answer = confirm ("Hapus Detail dengan Afd : " + data.AFD + " ,Block : " + data.BLOCK + " ?" );
				if (answer){			
					//start	
					var postdata = {};
					postdata['ID_BA'] = data.ID_BA;
					postdata['TANGGAL'] = $("#txt_tglBa").val();
					postdata['CRUD'] =  'DELD';
		
					$("#frm_load").dialog('open');
					
					var postdata_detail = {};					
					i=0;
					s = $("#list_notaDetail").getDataIDs();
			
					$.each(s, function(n, rowid) 
					{ 
						var data = $("#list_notaDetail").getRowData(rowid) ; 
						i=i+1;
						postdata_detail['ID_BA'+i] = data.ID_BA;
						postdata_detail['TANGGAL_PANEN'+i] = data.TANGGAL_PANEN;
						postdata_detail['BLOCK'+i] = data.BLOCK;
						postdata_detail['JANJANG'+i] = data.JANJANG;
						postdata_detail['DESCRIPTION'+i] =  data.DESCRIPTION;						
					});
					
					var postdata_id = {};
					postdata_id['ID'] = data.ID;
			        postdata_id['ID_BA'] = data.ID_BA;
					var data = {
								  id:postdata,
								  detail:postdata_id,
								  detail2:postdata_detail
								};
            		data = JSON.stringify(data);
			
			
					$.ajax({
							type:           'post',
							cache:          false,
							url:            url+'s_ba_afkir/CRUD_METHOD',
							data:           {myJson:  data} ,
							success: function(msg){
								var obj = jQuery.parseJSON(msg);    
								if(obj.error==true){
									alert(obj.status);
									$("#frm_load").dialog('close');
								}else{									
									alert(obj.status);																
									jQuery("#list_notaDetail").setGridParam({url:url+"s_ba_afkir/LoadData_Detail"+"/"+mag}).trigger("reloadGrid");									
									$("#frm_load").dialog('close');
									
								}
							}
					}); 
					///end
				}
			}   
        }

function add_nota(){
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);    
    $('input').val('');
	$('textarea').val('');
	jQuery("#list_notaDetail").setGridParam({url:url+"s_ba_afkir/LoadData_Detail/"}).trigger("reloadGrid"); 
    $("#txt_frmMode").val("ADD");
    $("#frm_nota").dialog('open');
	$("#cmd_appNote").hide();
	$("#cmd_newNote").show();
}

function approve_ba(cl){
    var ids = cl;
    var data = $("#list_notaangkut").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("Harap pilih data untuk diapprove");
    }else{
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
       
        jQuery("#list_notaDetail").setGridParam({url:url+"s_ba_afkir/LoadData_Detail"+"/"+data.ID_BA}).trigger("reloadGrid");  
        
        var nDate= formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
		
		$("#cmd_appNote").show();
		$("#cmd_newNote").hide();
		$("#txt_idBa").val(data.ID_BA);
        $("#txt_noBa").val(data.NO_BA);
        $("#txt_tglBa").val(nDate);
        $("#txt_desc").val(data.DESCRIPTION);
		$("#txt_status").val(data.STATUS);
        
        $("#txt_frmMode").val("APPROVE");
        
        $("#frm_nota").dialog('open');		
    } 
}

function approve(){
	var answer = confirm("Approve berita acara?");
    if (answer){
        var postdata_id = {};

        $("#frm_load").dialog('open');
		postdata_id['CRUD'] =  $("#txt_frmMode").val();	
		postdata_id['ID_BA'] =  $("#txt_idBa").val(); 
        postdata_id['BA_DATE'] =  $("#txt_tglBa").val();
		postdata_id['STATUS'] =  1;			
				
        var data = {
                      id:postdata_id,
                    };
        data = JSON.stringify(data);
        
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_ba_afkir/CRUD_METHOD',
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
                        $("#frm_nota").dialog('close');
                    }    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                    }    
               });
    }	
}

function edit_nota(cl){
    var ids = cl;
    var data = $("#list_notaangkut").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
       
        jQuery("#list_notaDetail").setGridParam({url:url+"s_ba_afkir/LoadData_Detail"+"/"+data.ID_BA}).trigger("reloadGrid");  
        
        var nDate= formatDate(new Date(getDateFromFormat(data.BA_DATE,'dd/MM/yyyy')),'yyyy-MM-dd');
		
		$("#cmd_appNote").hide();
		$("#cmd_newNote").show();
		$("#txt_idBa").val(data.ID_BA);
        $("#txt_noBa").val(data.NO_BA);
        $("#txt_tglBa").val(nDate);
        $("#txt_desc").val(data.DESCRIPTION);
		$("#txt_status").val(data.STATUS);
        
        $("#txt_frmMode").val("EDIT");
        
        $("#frm_nota").dialog('open');
    } 
}

function hapus_nota(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_notaangkut").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus BA Afkir dengan No : " + data.NO_BA + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_BA'] = data.ID_BA;
            postdata_id['NO_BA'] = data.NO_BA;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_ba_afkir/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error==true){
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

function addrow(){
	//start: added by Asep,20130614
	var ids = jQuery("#list_notaDetail").getDataIDs();
    var i = ids.length;
	//end: added by Asep,20130614
    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {WEIGHT:jdesc1};
    }
    
    var su=jQuery("#list_notaDetail").addRowData(i,datArr,'last');
    var act=jQuery("#list_notaDetail").setRowData(i,{no:i});
    
    ce = "";
	jQuery("#list_notaDetail").setRowData(i,{act:ce});  

}

function simpan_nota(){	
    var answer = confirm ("Simpan Data ? ")
    if (answer){		
        $("#frm_load").dialog('open');
        var postdata_id = {};

        postdata_id['ID_BA'] = $("#txt_idBa").val();
        postdata_id['BA_DATE'] = $("#txt_tglBa").val();
        postdata_id['NO_BA'] =  $("#txt_noBa").val();
		postdata_id['DESCRIPTION'] =  $("#txt_desc").val();
        postdata_id['CRUD'] =  $("#txt_frmMode").val();		
        
        var postdata_detail = {};
        i=0;
        s = $("#list_notaDetail").getDataIDs();
		
        $.each(s, function(n, rowid) 
        { 
            var data = $("#list_notaDetail").getRowData(rowid) ; 
			
			var block= data.BLOCK;
            i=i+1;
			postdata_detail['ID_BA'+i] = $("#txt_idBa").val();
            //postdata_detail['AFD'+i] = block.substring(0,2);
            postdata_detail['TANGGAL_PANEN'+i] = data.TANGGAL_PANEN;
            postdata_detail['BLOCK'+i] = data.BLOCK;
			postdata_detail['JANJANG'+i] = data.JANJANG;
			postdata_detail['DESCRIPTION'+i] = data.DESCRIPTION;
        }); 

        var data = {
                      id:postdata_id,
                      detail:postdata_detail
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_ba_afkir/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error==true){
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{
                        alert(obj.status)
                        reloadGrid();
                        $("#frm_load").dialog('close');
                        $("#frm_nota").dialog('close');    
                    }    
                    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                }    
               });
    }
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

</script>

<script type="text/javascript">

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
