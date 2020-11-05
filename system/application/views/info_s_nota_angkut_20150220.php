<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div> -->
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td>:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr> 
            <tr>
                <td class="labelcell">No Kendaraan</td><td>:</td>
                <td class="fieldcell">       
                    <input tabindex="2" type="text" id="txt_kendaraanSrc" name="nt_input" maxlength="50" onkeydown="doSearch(arguments[0]||event)"/>
                </td>     
            </tr>
            <tr>
                <td class="labelcell">No NAB</td><td>:</td>
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
        <button class="testBtn" type="submit" onclick="add_nota()">Buat Nota Baru</button>&nbsp;
    </div>
</div> 

<div id="frm_nota">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Data Nota Angkut Buah</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">Tanggal</td><td>:</td>
                    <td class="fieldcell">
                        <input tabindex="1" type="text" id="txt_tglNota" name="nt_input" maxlength="50"/>
                        <input tabindex="11" type="hidden" id="txt_idNota" name="nt_input" maxlength="50"/> <!-- ID nota untuk flag -->
                    </td>
                    <td class="labelcell"> Centang untuk NON TIMBANGAN <input type="checkbox" name="checkbox1" id="checkboxOne" onclick="enableDisable()" /> </td>
                </tr>
                <tr>
                    <td class="labelcell">No NAB</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_noSpb" name="nt_input" maxlength="100"/></td>
                    <td class="fieldcell"></td>  
                    <!--<td align="right"><button class="testBtn" type="submit" id="cmd_generateSpb" onclick="generate_spb()">generate spb</button></td>-->  
                    
                </tr>
                <tr>
                    <td class="labelcell">No Kendaraan</td><td>:</td>
                    <td class="fieldcell" colspan="2"><input tabindex="3" type="text" id="txt_noKendaraan" name="nt_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Nama Supir</td><td>:</td>
                    <td class="fieldcell" colspan="2"><input tabindex="4" type="text" id="txt_driverName" name="nt_input" maxlength="100"/></td>
                </tr>
                
                <input tabindex="5" type="hidden" id="txt_netto" name="nt_input" maxlength="100"/>
                <!--<tr>
                    <td class="labelcell">No Tiket</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_noTiket" name="nt_input" maxlength="100"/></td>
                    <td align="right"><button class="testBtn" type="submit" id="cmd_generateTiket" onclick="generate_tiket()">generate tiket</button></td>
                </tr>-->
                
            </table>
            <br>
            <div id="notaGrid">
                <table id="list_notaDetail" class="scroll"></table> 
                <div id="pager_notaDetail" class="scroll" style="text-align:center;"></div>
            </div>
            <div id="btn_section">
                <button class="testBtn" type="submit" id="cmd_newNote" onclick="simpan_nota()">Simpan</button>&nbsp;
            </div>
                
        </div>
        <div id="fragment-2">
        </div>
        <div id="fragment-3">
            
        </div>
    </div>
    <input type="hidden" id="txt_frmMode">
    <!--<input tabindex="17" type="button" id="submitdata" value="Simpan" onclick="submit()">   
    <div class="">
    <button class="fg-button ui-state-default ui-corner-all" type="submit">Simpan</button>  
    <button class="fg-button ui-state-default ui-corner-all" type="submit">Tutup</button> 
    </div>--> 
</div>

<div id="frm_load">
    Wait...
</div>

<div id="frm_report"></div>
<div id="search_form"></div>
</body>
<!--
<script type="text/javascript">
function enableDisable(){    
     var cb1 = document.getElementById('checkboxOne').checked;
	 document.getElementById('txt_noSpb').disabled = cb1;
	 document.getElementById('txt_noKendaraan').disabled = cb1;
	 document.getElementById('txt_driverName').disabled = cb1;
	 if (cb1==true){
		document.getElementById('txt_noSpb').value = '';
	 	document.getElementById('txt_noKendaraan').value = '';
	 	document.getElementById('txt_driverName').value = ''; 
	 }
}
</script>
-->
<script type="text/javascript">      
jQuery(document).ready(function()
{ 
    jQuery("#txt_tglNota").datepicker({dateFormat:"yy-mm-dd"});

    $('input').val('');

    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $("#frm_nota").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 480,
        width: 900,
        modal: true,
        title: "Nota Angkut Buah",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        $('input').val('');
                        $('textarea').val('');
                        $("#frm_nota").dialog('close');        
                    }/*,
            Simpan: function() 
                    {
                        submit();        
                    } */    
        } 
    });
    
    $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
    });
    
    $("#frm_report").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 300,
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
            //setDialogWindows('#search_form');
        },
        open:function(){
            jQuery("#list_notaangkut").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_nota_angkut/search_data');
        }
    });
    
});

jQuery(document).ready(function(){
    $('#tahun').change(function() {
        //get_periode();
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        //get_periode();
        reloadGrid(); 
    });
})

function getTglSpb(){
        var tanggal=$("#txt_tglNota").val();
        return tanggal;
    }
    
function resetAutocomplete(){
	var cb1 = document.getElementById('checkboxOne').checked;
    $("#txt_noSpb").autocomplete( 
            url+"s_nota_angkut/get_no_spb/"+$("#txt_tglNota").val()+"/"+cb1, {
			//url+"s_nota_angkut/get_no_spb/"+$("#txt_tglNota").val(), {
              dataType: 'ajax',
              //extraParams: $("#txt_tglNota").val(),
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
            $("#txt_noKendaraan").val(item.res_name);
            $("#txt_driverName").val(item.res_dName );
			$("#txt_netto").val(item.res_dNetto );

			$("#checkboxOne").attr('checked',false);
			if(item.res_dFlag==1){
				$("#checkboxOne").attr('checked',true);
			}
    });   
}

jQuery(document).ready(function(){
    $("#txt_tglNota").change(resetAutocomplete);
    

    $("#txt_noKendaraan").autocomplete( 
            url+"s_nota_angkut/get_no_kendaraan/", {
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

function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}
</script>

<script type="text/javascript">
function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_notaangkut").setGridParam({url:url+'s_nota_angkut/LoadData/'+get_periode()}).trigger("reloadGrid");    
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
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_NT_AB');
colModelT.push({name:'ID_NT_AB',index:'ID_NT_AB', editable: false, hidden:false, width: 140, align:'left'});

colNamesT.push('Tanggal');
colModelT.push({name:'TANGGAL',index:'TANGGAL', editable: false, hidden:false, width: 90, align:'left', formatter:'date'});

colNamesT.push('Nmr Kendaraan');
colModelT.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: false, hidden:false, width: 140, align:'center'});

colNamesT.push('Nama Supir');
colModelT.push({name:'NAMA_SUPIR',index:'NAMA_SUPIR', editable: false, hidden:false, width: 100, align:'left'});

colNamesT.push('No Tiket');
colModelT.push({name:'NO_TIKET',index:'NO_TIKET', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('No NAB');
colModelT.push({name:'NO_SPB',index:'NO_SPB', editable: false, hidden:false, width: 120, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('BERAT_BERSIH');
colModelT.push({name:'BERAT_BERSIH',index:'BERAT_BERSIH', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('FLAG_TIMBANGAN');
colModelT.push({name:'FLAG_TIMBANGAN',index:'FLAG_TIMBANGAN', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('BLOCK');
colModelT.push({name:'BLOCK',index:'BLOCK', editable: false, hidden:true, width: 80, align:'center'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_notaangkut").jqGrid(
            {
                url:url+'s_nota_angkut/LoadData/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_notaangkut"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_notaangkut").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_nota('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_nota('"+cl+"');\"/>";
                            pr_adb = "<img src='<?= $template_path ?>themes/base/images/adobe-print.png' width='12px' height='13px' onclick=\"print_nota_adb('"+cl+"');\"/> &nbsp;";
                            pr_xls = "<img src='<?= $template_path ?>themes/base/images/xls-print.png' width='12px' height='13px' onclick=\"print_nota_xls('"+cl+"');\"/>"; 
                            jQuery("#list_notaangkut").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_notaangkut',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_notaangkut',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        //jQuery("#list_sounding").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_sounding_kernel/search_data');
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
        
colNamesT_Detail.push('no');
colModelT_Detail.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_Detail.push('id');
colModelT_Detail.push({name:'ID_ANON',index:'ID_ANON', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_Detail.push('idnt');
colModelT_Detail.push({name:'ID_NT_AB',index:'ID_NT_AB', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_Detail.push('AFD');
colModelT_Detail.push({name:'AFD',index:'AFD', editable: true,hidden:true,width: 140
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"s_nota_angkut/get_afdeling", {
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
          }}, width: 70, align:'center'}); 

colNamesT_Detail.push('BLOCK');
colModelT_Detail.push({name:'BLOCK',index:'BLOCK', editable: true 
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete(
                        //url+"s_nota_angkut/get_block/"+giveLocType(), { Remarked by Asep, 20130822
						url+"s_nota_angkut/get_block/", {	
                          dataType: 'ajax',
                          width:350,
                          multiple: false,
                          limit:200,
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
                        //$("#i_txt_block").val(item.res_id);
                        //$("#i_nama_natura").val(item.res_name );
                      });    
          }}, width: 40, align:'left'}); 


colNamesT_Detail.push('TGL PANEN');
colModelT_Detail.push({name:'TANGGAL_PANEN',index:'TANGGAL_PANEN', editable: true,hidden:false, width: 45, align:'center', formatter:'date:yyyy-mm-dd'}); 

colNamesT_Detail.push('JJG NORMAL');
colModelT_Detail.push({name:'JANJANG',index:'JANJANG', editable: true, hidden:false, width: 50, align:'right'});

colNamesT_Detail.push('JJG OVERRIPE');
colModelT_Detail.push({name:'OVERRIPE',index:'OVERRIPE', editable: true, hidden:false, width: 50, align:'right'});

colNamesT_Detail.push('JJG AFKIR');
colModelT_Detail.push({name:'AFKIR',index:'AFKIR', editable: true, hidden:false, width: 40, align:'right'});

colNamesT_Detail.push('BRONDOLAN (Kg)');
colModelT_Detail.push({name:'BRONDOLAN',index:'BRONDOLAN', editable: true, hidden:false, width: 60, align:'right'});

colNamesT_Detail.push('KETERANGAN');
colModelT_Detail.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true, hidden:false, width: 90, align:'left'});

colNamesT_Detail.push('Tonase');
colModelT_Detail.push({name:'TONASE',index:'TONASE', editable: true, hidden:true, width: 0, align:'right'});

colNamesT_Detail.push('');
colModelT_Detail.push({name:'act',index:'act', editable: false, hidden:false, width: 10, align:'center'});

var loadView_detail = function()
        {
        jGrid_va = jQuery("#list_notaDetail").jqGrid(
            {
                url:url+'s_nota_angkut/LoadData_Detail/',
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
                    //rowid, cellname, value, iRow, iCol
                    //alert(arguments[2]);
                },  
                forceFit : true , 
                /*loadComplete: function(){ 
                    //var id = jQuery("#list_notaDetail").getGridParam('selrow'); 
                    var ids = jQuery("#list_notaDetail").getDataIDs(); 
                    for(var i=0;i<ids.length;i++){ 
                        var cl = ids[i];                            
                        ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_nota_detail('"+cl+"');\"/>"; 
                        jQuery("#list_notaDetail").setRowData(ids[i],{act:ce}) 
                    }
                },*/
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

    //if( rowID != null ) jQuery("#list_notaDetail").jqGrid('delGridRow',rowID,{url:url+"s_nota_angkut/LoadData_Detail/"+$("#txt_noSpb").val()+"/"+$("#txt_idNota").val(),reloadAfterSubmit:false});
    if( rowID != null ) jQuery("#list_notaDetail").delGridRow(rowID,{reloadAfterSubmit:false}); 
    else alert("Please Select Row to delete!");
}

function deleterow(){
            
            var postdata = {}; 
            var ids = jQuery("#list_notaDetail").getGridParam('selrow'); 
            var data = $("#list_notaDetail").getRowData(ids) ;
			var mag = $("#txt_idNota").val();
			
			if (data.ID_NT_AB == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
			}else{
				var answer = confirm ("Hapus Detail dengan Afd : " + data.AFD + " ,Block : " + data.BLOCK + " ?" );
				if (answer){			
					//start	
					var postdata = {};
					postdata['NO_SPB'] = $("#txt_noSpb").val();
					postdata['TANGGAL'] = $("#txt_tglNota").val();
					postdata['NO_KENDARAAN'] =  $("#txt_noKendaraan").val();
					postdata['BERAT_BERSIH'] =  $("#txt_netto").val();
					postdata['NAMA_SUPIR'] =  $("#txt_driverName").val();
					postdata['ID_NT_AB'] =  $("#txt_idNota").val();
					postdata['CRUD'] =  'DELD';
					postdata['FLAG_SPB'] =  document.getElementById('checkboxOne').checked;
		
					$("#frm_load").dialog('open');
					
					var postdata_detail = {};					
					i=0;
					s = $("#list_notaDetail").getDataIDs();
			
					$.each(s, function(n, rowid) 
					{ 
						var data = $("#list_notaDetail").getRowData(rowid) ; 
						i=i+1;
						postdata_detail['ID_ANON'+i] = data.ID_ANON;
						postdata_detail['NO_SPB'+i] = $("#txt_noSpb").val();
						postdata_detail['AFD'+i] = data.AFD;
						postdata_detail['BLOCK'+i] = data.BLOCK;
						postdata_detail['JANJANG'+i] = data.JANJANG;
						postdata_detail['TANGGAL_PANEN'+i] = data.TANGGAL_PANEN;
						postdata_detail['TONASE'+i] =  data.JANJANG;						
					});
					
					var postdata_id = {};
					postdata_id['ID_NT_AB'] = data.ID_NT_AB;
			        postdata_id['ID_ANON'] = data.ID_ANON;
					var data = {
								  id:postdata,
								  detail:postdata_id,
								  detail2:postdata_detail
								};
            		data = JSON.stringify(data);
			
			
					$.ajax({
							type:           'post',
							cache:          false,
							url:            url+'s_nota_angkut/CRUD_METHOD',
							data:           {myJson:  data} ,
							success: function(msg){
								var obj = jQuery.parseJSON(msg);    
								if(obj.error===true){
									alert(obj.status);
									$("#frm_load").dialog('close');
								}else{									
									alert(obj.status);									
									//$("#frm_load").reloadGrid(); 							
									jQuery("#list_notaDetail").setGridParam({url:url+"s_nota_angkut/LoadData_Detail"+"/"+mag}).trigger("reloadGrid");									
									//update_nota();
									$("#frm_load").dialog('close');
									
								}
							}
					}); 
					///end
				}
			}   
        }

function add_nota(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    jQuery("#list_notaDetail").setGridParam({url:url+"s_nota_angkut/LoadData_Detail/"}).trigger("reloadGrid"); 
    
    $('input').val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_nota").dialog('open');
}

function edit_nota(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_notaangkut").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        //jQuery("#list_notaDetail").setGridParam({url:url+"s_nota_angkut/LoadData_Detail/"+data.NO_SPB+"/"+data.ID_NT_AB}).trigger("reloadGrid"); 
        jQuery("#list_notaDetail").setGridParam({url:url+"s_nota_angkut/LoadData_Detail"+"/"+data.ID_NT_AB}).trigger("reloadGrid");  
        
        var nDate= formatDate(new Date(getDateFromFormat(data.TANGGAL,'dd/MM/yyyy')),'yyyy-MM-dd');
		
		$("#checkboxOne").attr('checked',false)
		if(data.FLAG_TIMBANGAN==1){
			//document.getElementById('checkboxOne').checked;
			$("#checkboxOne").attr('checked',true);
		}
		
        $("#txt_idNota").val(data.ID_NT_AB);
        $("#txt_tglNota").val(nDate);
        $("#txt_noKendaraan").val(data.NO_KENDARAAN);
		$("#txt_netto").val(data.BERAT_BERSIH);
        $("#txt_driverName").val(data.NAMA_SUPIR);
        $("#txt_noTiket").val(data.NO_TIKET);
        $("#txt_noSpb").val(data.NO_SPB);
        
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
        var answer = confirm ("Hapus NOTA dengan NAB : " + data.NO_SPB + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_NT_AB'] = data.ID_NT_AB;
            postdata_id['NO_SPB'] = data.NO_SPB;
            postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_nota_angkut/CRUD_METHOD',
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

function update_nota_detail(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_notaDetail").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di edit...");
    }else{
        jQuery('#list_notaDetail').saveCell(ids);
        var answer = confirm ("Update Detail Nota Untuk AFD: "+data.AFD+" dan BLOCK: "+ data.BLOCK +" ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
            var ids = jQuery("#list_notaDetail").getGridParam('selrow'); 
            var data = $("#list_notaDetail").getRowData(ids) ;
            
            postdata_id['ID_ANON'] = data.ID_ANON;
            postdata_id['ID_NT_AB'] = data.ID_NT_AB;
            postdata_id['AFD'] = data.AFD;
            postdata_id['BLOCK'] = data.BLOCK;
            postdata_id['JANJANG'] = data.JANJANG;
            postdata_id['TANGGAL_PANEN'] = data.TANGGAL_PANEN;
			postdata_id['TONASE'] = data.TONASE;
            
            postdata_id['CRUD'] =  'EDITD'; 
            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_nota_angkut/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(msg);
                            jQuery("#list_notaDetail").setGridParam({url:url+"s_nota_angkut/LoadData_Detail/"+
                                                    $("#txt_noSpb").val()+"/"+$("#txt_idNota").val()}).trigger("reloadGrid");
                            $("#frm_load").dialog('close');    
                        }    
                        
                    },
                    error:function (xhr, ajaxOptions, thrownError){
                            alert(xhr.status);
                            alert(thrownError);
                    }
            });  
        }
    }     
}

function hapus_nota_detail(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_notaDetail").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Detail dengan Afd : " + data.AFD + " ,Block : " + data.BLOCK + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_NT_AB'] = data.ID_NT_AB;
            postdata_id['ID_ANON'] = data.ID_ANON;
            postdata_id['CRUD'] =  'DELD';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_nota_angkut/CRUD_METHOD',
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
    
    //sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 
    //alert (rowCount);
    var su=jQuery("#list_notaDetail").addRowData(i,datArr,'last');
    var act=jQuery("#list_notaDetail").setRowData(i,{no:i});
    
    ce = "";//"<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"remove_row();\"/>";
    //de = "<button style='height:22px;width:20px;' type='button' title='Delete' onclick=\"jQuery('#list_notaDetail').jqGrid('delGridRow','"+i+"',{reloadAfterSubmit:false });\" >D</button>";

    jQuery("#list_notaDetail").setRowData(i,{act:ce});  

}

function simpan_nota(){	
    var answer = confirm ("Simpan Data ? ")
    if (answer){		
        $("#frm_load").dialog('open');
        var postdata_id = {};
       // postdata_id['NO_TIKET'] = $("#txt_noTiket").val();
        postdata_id['NO_SPB'] = $("#txt_noSpb").val();
        postdata_id['TANGGAL'] = $("#txt_tglNota").val();
        postdata_id['NO_KENDARAAN'] =  $("#txt_noKendaraan").val();
		postdata_id['BERAT_BERSIH'] =  $("#txt_netto").val();
        postdata_id['NAMA_SUPIR'] =  $("#txt_driverName").val();
        postdata_id['ID_NT_AB'] =  $("#txt_idNota").val();
		postdata_id['FLAG_SPB'] =  document.getElementById('checkboxOne').checked; //alert(document.getElementById('checkboxOne').checked);
        postdata_id['CRUD'] =  $("#txt_frmMode").val();
		
        
        var postdata_detail = {};
        i=0;
        s = $("#list_notaDetail").getDataIDs();
		
        $.each(s, function(n, rowid) 
        { 
            var data = $("#list_notaDetail").getRowData(rowid) ; 
			
			var block= data.BLOCK;
            i=i+1;
            postdata_detail['NO_SPB'+i] = $("#txt_noSpb").val();
            postdata_detail['AFD'+i] = block.substring(0,2);
            postdata_detail['BLOCK'+i] = data.BLOCK;
            postdata_detail['JANJANG'+i] = data.JANJANG;
			postdata_detail['OVERRIPE'+i] = data.OVERRIPE;
			postdata_detail['AFKIR'+i] = data.AFKIR;
			postdata_detail['BRONDOLAN'+i] = data.BRONDOLAN;
			postdata_detail['DESCRIPTION'+i] = data.DESCRIPTION;
            postdata_detail['TANGGAL_PANEN'+i] = data.TANGGAL_PANEN;
			if(data.OVERRIPE==''){
				data.OVERRIPE = 0;
			}
			if(data.AFKIR==''){
				data.AFKIR = 0;
			}
			postdata_detail['TONASE'+i] =  parseInt(data.JANJANG)+parseInt(data.OVERRIPE)+parseInt(data.AFKIR);
			postdata_detail['JJG_TOTAL'+i] =  parseInt(data.JANJANG)+parseInt(data.OVERRIPE)+parseInt(data.AFKIR);
        }); 
        // {data:"id="+postdata_id+"&detail="+postdata_detail}

        var data = {
                      id:postdata_id,
                      detail:postdata_detail
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_nota_angkut/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
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

function update_nota(){
        var postdata_id = {};
        postdata_id['NO_SPB'] = $("#txt_noSpb").val();
        postdata_id['TANGGAL'] = $("#txt_tglNota").val();
        postdata_id['NO_KENDARAAN'] =  $("#txt_noKendaraan").val();
		postdata_id['BERAT_BERSIH'] =  $("#txt_netto").val();
        postdata_id['NAMA_SUPIR'] =  $("#txt_driverName").val();
        postdata_id['ID_NT_AB'] =  $("#txt_idNota").val();
        postdata_id['CRUD'] =  $("#txt_frmMode").val();
        
        var postdata_detail = {};
        i=0;
        s = $("#list_notaDetail").getDataIDs();

        $.each(s, function(n, rowid) 
        { 
            var data = $("#list_notaDetail").getRowData(rowid) ; 
            i=i+1;
            postdata_detail['NO_SPB'+i] = $("#txt_noSpb").val();
            postdata_detail['AFD'+i] = data.AFD;
            postdata_detail['BLOCK'+i] = data.BLOCK;
            postdata_detail['JANJANG'+i] = data.JANJANG;
			postdata_detail['OVERRIPE'+i] = data.OVERRIPE;
			postdata_detail['AFKIR'+i] = data.AFKIR;
			postdata_detail['BRONDOLAN'+i] = data.BRONDOLAN;
			postdata_detail['DESCRIPTION'+i] = data.DESCRIPTION;
            postdata_detail['TANGGAL_PANEN'+i] = data.TANGGAL_PANEN;
			postdata_detail['TONASE'+i] =  data.JANJANG;
        }); 

        var data = {
                      id:postdata_id,
                      detail:postdata_detail
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_nota_angkut/CRUD_METHOD',
                data:           {myJson:  data} 
               });
		$("#frm_load").dialog('close');		
}

function generate_tiket(){
    $.ajax({
        type:           'post',
        cache:          false,
        url:            url+'s_nota_angkut/generate_tiket',
        success: function(msg){
            $("#txt_noTiket").val(msg);
        }
    });    
}

function generate_spb(){
    $.ajax({
        type:           'post',
        cache:          false,
        url:            url+'s_nota_angkut/generate_spb',
        success: function(msg){
            $("#txt_noSpb").val(msg);
        }
    });    
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
    var no_kend = jQuery("#txt_kendaraanSrc").val(); 
    var no_spb = jQuery("#txt_spbSrc").val();  

    if (no_spb == ""){
        no_spb = "-";
    }
    if (no_kend == ""){
        no_kend = "-";
    } 
    
    jQuery("#list_notaangkut").setGridParam({url:url+"s_nota_angkut/search_spb/"+no_spb+"/"+no_kend+"/"+get_periode()}).trigger("reloadGrid");        
} 
</script>

<script type="text/javascript">
function print_nota_adb(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_notaangkut").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di Print...");
    }else{
        var answer = confirm ("Print NOTA dengan NAB : " + data.NO_SPB + " ?" );
        if (answer){
            // open pdf files in new window
            var postdata_id = {};
    
            postdata_id['ID_NT_AB'] = data.ID_NT_AB;
            postdata_id['NO_SPB'] = data.NO_SPB;
            postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
            postdata_id['CRUD'] =  'PRINT';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_nota_angkut/CRUD_METHOD/'+'PDF',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);  
                        if(obj.error===true){
                            alert(obj.status);
                            
                        }else{
                            alert(obj.status);
                            $("#frm_report").dialog('open');
                              
                        }
                    }
            });        
        }
        
    }             
}

function print_nota_xls(){
    alert ("In Progress...!");
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
