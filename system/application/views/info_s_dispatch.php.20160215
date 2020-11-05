<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div> -->
        <table border="0" class="teks_"  cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td class="labelcell">:</td>
                    <td class="fieldcell">
                    <? echo $periode; ?>
                    <!-- <input class="input" type="text" id='s_tgl_periode' onchange="get_data()" > -->
                </td>
            </tr>
        </table>
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_timbangan" class="scroll"></table> 
            <div id="pager_timbangan" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <!-- <button class="testBtn" type="submit" id="add_kontraktor" onclick="capture_wb()">Capture Data</button>&nbsp; -->
        <button class="testBtn" type="submit" id="add_kontraktor" onclick="add_timbangan()">Input Data Timbangan</button>&nbsp;
    </div>

</div> 

<div id="frm_import_wb">
    <div id="tabs_nab">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>File Capture/Import</span></a></li>
            <!--<li><a href="#fragment-2"><span></span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        
        <div id="fragment-1" class="panes">
            <form name="form" action="" method="POST" enctype="multipart/form-data">
            <table  width="100%" border="0" id="input_table">
            <tr>
                <td class="labelcell">Pilih File</td><td>:</td>
                <td class="fieldcell" colspan="4">
                    <?php //if (isset($error)) echo $error;?>
                    <?php //echo form_open_multipart('',array('id'=>'uploadForm'));?> 
                    <input type="file" name="fileToUpload" size="20" id='fileToUpload' />                
                </td> 
            </tr>
            <tr>
                <td class="labelcell">Periode Timbang</td><td>:</td>
                <td class="fieldcell">
                    <input type="text" id='s_tgl_periode_tbg' >                
                </td>
                <td>-</td>
                <td class="fieldcell">
                    <input type="text" id='s_tgl_periode_tbg_to' >                
                </td>
            </tr>
            <tr>
                <td class="labelcell"></td>
                <td></td>
                <td colspan="4" class="fieldcell"><button class="button" id="buttonUpload" onClick="return ajaxFileUpload();">Upload</button></td>
            </tr>
            </table>
            </form>
        </div>
    </div>

</div>

<div id="frm_nab">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>NAB / SPB - List</span></a></li>
            <!--<li><a href="#fragment-2"><span></span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        
        <div id="fragment-1" class="panes">
            <table  width="100%" border="0" class="teks_" id="input_table">
                <tr>
                    <td class="labelcell">No NAB/SPB</td><td>:</td>
                    <td class="fieldcell">       
                        <input tabindex="1" type="text" id="txt_nabSrc" name="nt_input" maxlength="50" onkeydown="doSearch(arguments[0]||event)"/>
                    </td>     
                </tr>
                
                <tr>
                    <td colspan="3" id="grid_td">
                    <div id="notaGrid">
                        <table id="list_notaangkut" class="scroll"></table> 
                        <div id="pager_notaangkut" class="scroll" style="text-align:center;"></div>
                    </div>
                    </td>
                    
                </tr>
                <tr>
                
                <td class="fieldcell"><button class="button" id="buttonUpload" onclick="get_nab()">Set SPB</button></td>
                </tr>
            </table>   
        </div>
    </div>
</div>

<div id="frm_timbang">
    <div id="tabs_timbang">
        <ul class="tabs_timbang">
            <li><a href="#fragment-1"><span>Data Timbangan</span></a></li>
            <li><a href="#fragment-2"><span>Data Grading</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">No Tiket</td><td>:</td>
                    <td class="fieldcell"><input tabindex="1" type="text" id="txt_noTiket" name="tbg_input" maxlength="100"/>
                    <input tabindex="30" type="hidden" id="txt_idTimbang" name="tbg_input" maxlength="50"/></td>                    
                    <!-- <td align="right"><button class="testBtn" type="submit" id="cmd_generateTiket" onclick="generate_tiket()">generate tiket</button></td> -->
                    <td class="fieldcell"></td>
                </tr>
                <tr>                	
                    <td class="labelcell">Jenis Muatan</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_jenisMuatan" name="tbg_input" maxlength="100"/></td>
                    <td class="labelcell">Janjang</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_jjg" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tanggal Masuk</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_tglMasuk" name="tbg_input" maxlength="100"/></td>
                    <td class="labelcell">Tangal Keluar</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_tglKeluar" name="tbg_input" maxlength="100"/></td>
                </tr>
               
                <tr>
                    <td class="labelcell">Jam Masuk</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_jamMasuk" name="tbg_input" maxlength="100"/> *hh:mm</td>
                    <td class="labelcell">Jam Keluar</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_jamKeluar" name="tbg_input" maxlength="100"/> *hh:mm</td>
                </tr>
                
  <tr>
                    <td class="labelcell">No Kendaraan</td><td>:</td>
                    <td class="fieldcell"><input tabindex="8" type="text" id="txt_noKendaraan" name="tbg_input" maxlength="100"/></td>
                    <td class="labelcell">Type Kendaraan</td><td>:</td>
          <td align="left"><input type="checkbox" tabindex="9" id="chk_wVehicle" name="tbg_input">Check jika kendaraan milik sendiri</td>
                </tr>
                <tr>
                	<td class="labelcell">Nama Driver</td><td>:</td>
                    <td class="fieldcell"><input tabindex="10" type="text" id="txt_driver" name="tbg_input" maxlength="100"/></td>                   
                </tr>
                <tr>
                    <td class="labelcell">Type Buah</td><td>:</td>
                    <td class="fieldcell"><?php echo $type_buah;  ?></td>
                    <td class="labelcell">Type Timbang</td><td>:</td>
                    <td class="fieldcell"><?php echo $type_timbang;  ?></td>
                </tr>
                <tr>
                    
                </tr> 
                <tr>
                    <td class="labelcell">Berat Isi/Gross (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="9" type="text" id="txt_beratIsi" name="tbg_input" maxlength="100"/></td>
                    <td class="labelcell">Berat Kosong/Tara (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="10" type="text" id="txt_beratKosong" name="tbg_input" maxlength="100"/></td>
                </tr>
                   
                <tr>
                    <td class="labelcell">No NAB</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_noSpb" name="tbg_input" maxlength="100"/></td>
                    <td class="labelcell">AFD</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_afd" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Catatan</td><td>:</td>
                    <td class="fieldcell">
                        <textarea tabindex="12" name="tbg_input" id="txt_noteTbg" cols="45" rows="5"></textarea>
                    </td>
                    <td class="labelcell">SUPPLIER</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_supplier" name="tbg_input" maxlength="100"/></td>
                </tr>
            </table>
            <br>
            <div id="btn_section">
                <button class="testBtn" type="submit" id="cmd_newTimbang" onclick="simpan_timbangan()">Simpan</button>&nbsp;
            </div>
                
        </div>
        <div id="fragment-2">
        	<table width="100%" border="0" class="teks_" id="input_table" >
            	<tr>
                    <td class="labelcell">Buah Mentah (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="10" type="text" id="txt_grd_buahmentah" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Buah Busuk (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="13" type="text" id="txt_grd_buahbusuk" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Buah Kecil (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="14" type="text" id="txt_grd_buahkecil" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tangkai Panjang (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="15" type="text" id="txt_grd_tangkaipanjang" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Brondolan (Kg)</td><td>:</td>
                    <td class="fieldcell"><input tabindex="16" type="text" id="txt_grd_brondolan" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Lainnya </td><td>:</td>
                    <td class="fieldcell"><input tabindex="17" type="text" id="txt_grd_lainnya" name="tbg_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Total Grading :  </td><td>:</td>
                    <td class="fieldcell"><input tabindex="17" type="text" id="txt_berat_grading" name="tbg_input" maxlength="100"/>*</td>
                </tr>
            </table>
        </div>
        
    </div>
    <input type="hidden" id="txt_frmMode">
</div>

<div id="frm_load">
    <img id="loading" src="<?= $template_path ?>themes/base/images/loading.gif" style="display:none;"> Please Wait...
</div>

<div id="search_form"></div> 
</body>

<script type="text/javascript">
jQuery(document).ready(function(){
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $('input').val('');
    $('#chk_wVehicle').attr('checked',false)

    $("#frm_import_wb").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Import data timbangan",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        //clear_form_elements(this.form);
                        $("#frm_import_wb").dialog('close');        
                    }
           
        } 
    });
    
    $("#frm_nab").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Link NAB/SPB dengan Data Timbangan",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        //clear_form_elements(this.form);
                        $('input').val('');
                        $("#frm_nab").dialog('close');        
                    }
           
        } 
    });
    
    $("#frm_timbang").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 610,
        width: 710,
        modal: true,
        title: "Timbangan (Proses Manual)",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        //clear_form_elements(this.form);
                        $('input').val('');
                        $("#frm_timbang").dialog('close');        
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
            jQuery("#list_timbangan").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_data_timbangan/search_data');
        }
    });
    
})

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
    $('#tahun').change(function() {
        //get_periode();
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        //get_periode();
        reloadGrid(); 
    });
})
</script>
<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0                 
    $("#tabs").tabs();
    
    //############# NAB TABS ##############
    var $tabs_nab = $('#tabs_nab').tabs();
    var selected_nab = $tabs_nab.tabs('option', 'selected'); // => 0                 
    $("#tabs_nab").tabs();
    
    //############# TIMBANGAN TABS ##############
    var $tabs_nab = $('#tabs_timbang').tabs();
    var selected_nab = $tabs_nab.tabs('option', 'selected'); // => 0                 
    $("#tabs_timbang").tabs();
  });
  
function clear_form_elements(ele) 
{
    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'file':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
    //$("#frm_import_wb").dialog('close');
}
</script>

<script type="text/javascript">
$(function() {
    $("#s_tgl_periode").datepicker({dateFormat:"yy-mm-dd"});
    $("#s_tgl_periode_tbg").datepicker({dateFormat:"yy-mm-dd"});
    $("#s_tgl_periode_tbg_to").datepicker({dateFormat:"yy-mm-dd"});
    
    $("#txt_tglKeluar").datepicker({dateFormat:"yy-mm-dd"});
    $("#txt_tglMasuk").datepicker({dateFormat:"yy-mm-dd"});
});

function get_periode(){
    var periode = $("#s_tgl_periode").val();
    return periode;
}
</script> 

<script type="text/javascript">
$(document).ready(function () 
{
    $("#kode_kend")
      .autocomplete( 
        url+"s_data_timbangan/get_no_mesin/"+$("#s_tgl_periode").val(), {
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
        var periode = $("#s_tgl_periode").val();
        var vc = $("#kode_kend").val();
        
        jQuery("#list_timbangan").setGridParam({url:url+
            's_data_timbangan/grid_data_timbangan/'+vc+'/'+periode}).trigger("reloadGrid");    

      }); 

});

var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var colNamesN = new Array();
var colModelN = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   

colNamesN.push('No');
colModelN.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 5, align:'center'});

colNamesN.push('ID_TIMBANGAN');
colModelN.push({name:'ID_TIMBANGAN',index:'ID_TIMBANGAN', editable: false, hidden:true, width: 100, align:'left'});

colNamesN.push('TANGGAL');
colModelN.push({name:'TANGGALM',index:'TANGGALM', editable: true, hidden:false, width: 150, align:'left',formatter:'date'});//formatter:{date:{srcformat: 'Y-m-d'}}

colNamesN.push('NO TIKET');
colModelN.push({name:'NO_TIKET',index:'NO_TIKET', editable: true, hidden:false, width: 120, align:'left'});

/*colNamesN.push('spb');
colModelN.push({name:'NO_SPB',index:'NO_SPB', editable: false, hidden:true, width: 10, align:'left'}); */

colNamesN.push('NO KENDARAAN');
colModelN.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: true, hidden:false, width: 150, align:'left',sortable:true});

colNamesN.push('BERAT ISI');
colModelN.push({name:'BERAT_ISI',index:'BERAT_ISI', editable: true, hidden:false, width: 120, align:'right'});

colNamesN.push('BERAT KOSONG');
colModelN.push({name:'BERAT_KOSONG',index:'BERAT_KOSONG', editable: true, hidden:false, width: 120, align:'right'});

colNamesN.push('BERAT BERSIH');
colModelN.push({name:'BERAT_BERSIH',index:'BERAT_BERSIH', editable: true, hidden:false, width: 150, align:'right'});

colNamesN.push('TPBUAH');
colModelN.push({name:'TYPE_BUAH',index:'TYPE_BUAH', editable: true, hidden:true, width: 5, align:'left'});

colNamesN.push('SUPPLIER');
colModelN.push({name:'SUPPLIERCODE',index:'SUPPLIERCODE', editable: true, hidden:true, width: 150, align:'left'});

colNamesN.push('AFD');
colModelN.push({name:'AFD',index:'AFD', editable: true, hidden:true, width: 50, align:'left'});

colNamesN.push('NO NAB');
colModelN.push({name:'NO_SPB',index:'NO_SPB', editable: true, hidden:false, width: 150, align:'left'});

colNamesN.push('TANGGALK');
colModelN.push({name:'TANGGALK',index:'TANGGALK', editable: true, hidden:true, width: 10, align:'left',formatter:'date'});//formatter:{date:{srcformat: 'Y-m-d'}}

colNamesN.push('WAKTUM');
colModelN.push({name:'WAKTUM',index:'WAKTUM', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('WAKTUK');
colModelN.push({name:'WAKTUK',index:'WAKTUK', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('JENIS_MUATAN');
colModelN.push({name:'JENIS_MUATAN',index:'JENIS_MUATAN', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('TYPE_TIMBANG');
colModelN.push({name:'TYPE_TIMBANG',index:'TYPE_TIMBANG', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('NOTE');
colModelN.push({name:'NOTE',index:'NOTE', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('TYPE_KENDARAAN');
colModelN.push({name:'TYPE_KENDARAAN',index:'TYPE_KENDARAAN', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('DRIVER_NAME');
colModelN.push({name:'DRIVER_NAME',index:'DRIVER_NAME', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_BUAHMENTAH');
colModelN.push({name:'GRD_BUAHMENTAH',index:'GRD_BUAHMENTAH', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_BUAHBUSUK');
colModelN.push({name:'GRD_BUAHBUSUK',index:'GRD_BUAHBUSUK', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_BUAHKECIL');
colModelN.push({name:'GRD_BUAHKECIL',index:'GRD_BUAHKECIL', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_TANGKAIPANJANG');
colModelN.push({name:'GRD_TANGKAIPANJANG',index:'GRD_TANGKAIPANJANG', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_BRONDOLAN');
colModelN.push({name:'GRD_BRONDOLAN',index:'GRD_BRONDOLAN', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('GRD_LAINNYA');
colModelN.push({name:'GRD_LAINNYA',index:'GRD_LAINNYA', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('BERAT_GRADING');
colModelN.push({name:'BERAT_GRADING',index:'BERAT_GRADING', editable: true, hidden:false, width: 150, align:'left'});

colNamesN.push('JJG');
colModelN.push({name:'JJG',index:'JJG', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('FLAG_TIMBANGAN');
colModelN.push({name:'FLAG_TIMBANGAN',index:'FLAG_TIMBANGAN', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('');
colModelN.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_timbangan").jqGrid(
            {
                url:url+'s_data_timbangan/LoadData/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesN ,
                colModel: colModelN,
                sortname: colModelN[1].name,
                pager:jQuery("#pager_timbangan"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                width: 900,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
				
                loadComplete: function(){ 
                    var ids = jQuery("#list_timbangan").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];                            
                            be = "<img style='padding-right:6px;' title='Edit' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_timbangan('"+cl+"');\" />"; 
                            ce = "<img style='padding-right:6px;' title='Hapus' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_timbangan('"+cl+"');\"/>";                           
                            jQuery("#list_timbangan").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_timbangan',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_timbangan',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });  
         }
		 
jQuery("#list_timbangan").ready(loadView);
</script>

<script type="text/javascript">
function reloadGrid(){ 
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_timbangan").setGridParam({url:url+'s_data_timbangan/LoadData/'+get_periode()}).trigger("reloadGrid");    
}

function addrow(){
    /*var i = jQuery('#'+sIDgrid).getGridParam('records');
    var ids = jQuery('#'+sIDgrid).getGridParam('selrow');
    var rowCount = $('#'+sIDgrid).getGridParam("reccount"); 
    var back=null; 

    var id = jQuery('#'+sIDgrid).getGridParam('selrow');
    var dat = jQuery('#'+sIDgrid).getRowData(id);

    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {NO_KENDARAAN:"1",WEIGHT:jdesc1};
    }
    
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />";
    //alert (rowCount);
    var su=jQuery('#'+sIDgrid).addRowData(i,datArr,'last');
    var act=jQuery('#'+sIDgrid).setRowData(i,{act:sv})
    var mydata2 = [
    {ID_TIMBANGAN:"12345",JENIS_MUATAN:"Desktop Computer",BERAT_ISI:"note",BERAT_KOSONG:"Yes"} ,
    {ID_TIMBANGAN:"12345",JENIS_MUATAN:"Desktop Computer",BERAT_ISI:"note",BERAT_KOSONG:"Yes"}
    
    ];
      for(var i=0;i<mydata2.length;i++)
      {
        jQuery('#'+sIDgrid).addRowData(mydata2[i].id,mydata2[i]);
      }*/
    var rowCount = $("#list_timbangan").getGridParam("reccount");
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
    
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_timbangan("+i+")'; />"; 
    
    var su=jQuery("#list_timbangan").addRowData(i,datArr,'last');
    var act=jQuery("#list_timbangan").setRowData(i,{act:sv});
     
    //var ids = jQuery("#list_timbangan").jqGrid('getDataIDs');
    //jQuery('#list_timbangan').editCell(ids, true);
}

function get_data(){
    var periode = $("#s_tgl_periode").val();    
    jQuery("#list_timbangan").setGridParam({url:url+'s_data_timbangan/LoadData/'+periode}).trigger("reloadGrid");    
}

function capture_wb(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    
    $("#tabs_nab").tabs();
    $("#tabs_nab").tabs('select',0);
    //###########################

    clear_form_elements(this.form)
    $("#frm_import_wb").dialog('open'); 

}

function linknab(cl){
    var periode = $("#s_tgl_periode").val();
    //var ids = jQuery("#list_timbangan").getGridParam('selrow'); 
    var data = $("#list_timbangan").getRowData(cl) ;
    
    jQuery("#list_notaangkut").setGridParam({url:url+'s_data_timbangan/load_nota_info/'+periode+'/'+data.NO_KENDARAAN}).trigger("reloadGrid");  
    $("#frm_nab").dialog('open');     
}

</script>

<script type="text/javascript">
    function ajaxFileUpload()
    {
        var periode = $('#s_tgl_periode_tbg').val();
        var periode_to = $('#s_tgl_periode_tbg_to').val();
        if (periode==null || periode ==''){
            alert("Harap isi periode timbang");
        }else{
            $("#loading")
            .ajaxStart(function(){
                $(this).show();
            })
            .ajaxComplete(function(){
                $(this).hide();
            });
            $("#frm_load").dialog('open');
            $.ajaxFileUpload
            (
                {
                    url:url+'s_data_timbangan/do_upload/'+periode+'/'+periode_to,
                    secureuri:false,
                    fileElementId:'fileToUpload',
                    dataType: 'json',
                    success: function (data, status)
                    {
                        if(typeof(data.error) != 'undefined')
                        {
                            if(data.error != '')
                            {
                                alert(data.error);
                                $("#frm_load").dialog('close');
                            }else
                            {
                                alert(data.msg);
                                $("#frm_load").dialog('close');
                            }
                        }
                    },
                    error: function (data, status, e)
                    {
                        alert(data.msg);
                    }
                }
            )    
        }   
        return false;
    }
</script>

<script type="text/javascript">
function get_periode(){
    //var periode = $('#s_tgl_periode').val();
	//alert(periode);
	var periode = $("#tahun").val() + $("#bulan").val();
    return periode;    
}

var jGrid_nab = null;
var colNamesT_nab = new Array();
var colModelT_nab = new Array();
jQuery(document).ready(function() {   
colNamesT_nab.push('no');
colModelT_nab.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_nab.push('ID_NT_AB');
colModelT_nab.push({name:'ID_NT_AB',index:'ID_NT_AB', editable: false, hidden:true, width: 140, align:'left'});

colNamesT_nab.push('Tanggal.');
colModelT_nab.push({name:'TANGGAL',index:'TANGGAL', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_nab.push('Nmr Kendaraan');
colModelT_nab.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: false, hidden:false, width: 140, align:'center'});

colNamesT_nab.push('No Tiket');
colModelT_nab.push({name:'NO_TIKET',index:'NO_TIKET', editable: false, hidden:true, width: 120, align:'left'});

colNamesT_nab.push('No SPB');
colModelT_nab.push({name:'NO_SPB',index:'NO_SPB', editable: false, hidden:false, width: 120, align:'left'});

colNamesT_nab.push('');
//colModelT_nab.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 
colModelT_nab.push({name: 'act', index: 'act', width: 60, align: 'center', editable: true, edittype:"checkbox", 
            editoptions:{value:"True:False"}, formatter:"checkboxFormatter", formatoptions:{disabled:false},sortable:false});

var loadView = function()
        {
        jGrid_nab= jQuery("#list_notaangkut").jqGrid(
            {
                url:url+'s_data_timbangan/load_nota_info/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_nab ,
                colModel: colModelT_nab,
                sortname: colNamesT_nab[1],
                pager:jQuery("#pager_notaangkut"),
                rowNum: 400,
                rownumbers: true,
                height: 150,
                imgpath: gridimgpath,
                sortorder: "asc",
                multiselect:true,
                viewrecords: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){}
            });
         jGrid_nab.navGrid('#pager_notaangkut',{edit:false,del:false,add:false, search: false, refresh: true});
            
         }
jQuery("#list_notaangkut").ready(loadView);
})
$(function(){
    function checkboxFormatter(el, cval, opts) {
              cval = cval + ""; cval = cval.toLowerCase();
              var bchk = cval.search(/(false|0|no|off|n)/i) < 0 ? "checked=\"checked\"" : "";
              $(el).html("<input type='checkbox' onclick=\"ajaxSave('" +opts.rowId + "', this);\" " + bchk + " value='" + cval + "'offval='no' />");
              alert("yoo");
    }    
}) 


function ajaxSave(rowid, curCheckbox) {
          //ajax Save code
} 

function get_nab(){
    var s; 
    s = jQuery("#list_notaangkut").jqGrid('getGridParam','selarrrow');
    var data_nab = $("#list_notaangkut").getRowData(s); 
    if(data_nab.NO_KENDARAAN === 'undefined' || data_nab.NO_KENDARAAN===null){
        alert ("Hanya 1 SPB yang diizinkan !!");
    }else{
        $("#frm_load").dialog('open');
        var ids = jQuery("#list_timbangan").getGridParam('selrow'); 
        var data = $("#list_timbangan").getRowData(ids) ;

        var postdata_id = {};
        postdata_id['ID_TIMBANGAN'] = data.ID_TIMBANGAN;
        postdata_id['NO_SPB'] = data_nab.NO_SPB;
        postdata_id['NO_KENDARAAN'] =  data_nab.NO_KENDARAAN;
        //postdata_id['NO_TIKET']=data.NO_TIKET;
        postdata_id['ID_NT_AB'] = data_nab.ID_NT_AB;
        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_data_timbangan/update_spb_timbangan',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        $("#frm_nab").dialog('close');
                    }else{
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        $("#frm_nab").dialog('close');
                        reloadGrid();
                        //$("#frm_nota").dialog('close');    
                    }    
                    
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

function gridReload(){ 
    var spb = jQuery("#txt_nabSrc").val();
    var no_kend = jQuery("#txt_kendaraanSrc").val();   

    if (spb == ""){
        spb = "-";
    }
    if (no_kend == ""){
        no_kend = "-";
    } 
    
    jQuery("#list_notaangkut").setGridParam({url:url+"s_data_timbangan/search_spb/"+spb+"/"+no_kend+"/"+get_periode()}).trigger("reloadGrid");        
} 
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
    $("#txt_noKendaraan")
          .autocomplete( 
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
			$("#txt_driver").val(item.res_name);
            //$("#i_nama_natura").val(item.res_name );
          });
})

jQuery(document).ready(function(){
    $("#txt_supplier")
          .autocomplete( 
            url+"s_nota_angkut/get_supplier/", {
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
			$("#txt_supplier").val(item.res_id);
            //$("#i_nama_natura").val(item.res_name );
          });
})

jQuery(document).ready(function(){
    $("#txt_afd")
          .autocomplete( 
            url+"s_nota_angkut/get_afdeling/", {
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
			$("#txt_afd").val(item.res_name);
            //$("#i_nama_natura").val(item.res_name );
          });
})

function generate_tiket(){
    $.ajax({
        type:           'post',
        cache:          false,
        url:            url+'s_data_timbangan/generate_tiket',
        success: function(msg){
            $("#txt_noTiket").val(msg);
        }
    });    
}

function add_timbangan(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs_timbang").tabs();
    $("#tabs_timbang").tabs('select',0);
    //###########################
    
    $('input').val('');
    $('#chk_wVehicle').attr('checked',false) 
    $("#txt_frmMode").val("ADD");
    $("#frm_timbang").dialog('open');
    
    //#### SET DEFAULT DROPDOWN VALUE 
    $("#TYPE_TIMBANG").val('');
    $("#TYPE_BUAH").val(''); 
}

function edit_timbangan(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_timbangan").getRowData(ids) ;

    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //##########################       
        
        
        var nDate= formatDate(new Date(getDateFromFormat(data.TANGGALM,'dd/MM/yyyy')),'yyyy-MM-dd');
		var kDate= formatDate(new Date(getDateFromFormat(data.TANGGALK,'dd/MM/yyyy')),'yyyy-MM-dd');
				
		$("#txt_idTimbang").val(data.ID_TIMBANGAN);
        $("#txt_noTiket").val(data.NO_TIKET);
        $("#txt_tglMasuk").val(nDate);
        $("#txt_tglKeluar").val(kDate);
		$("#txt_jamMasuk").val(data.WAKTUM);
        $("#txt_jamKeluar").val(data.WAKTUK);
		$("#txt_noKendaraan").val(data.NO_KENDARAAN);
		$("#txt_jenisMuatan").val(data.JENIS_MUATAN);
		$("#txt_beratIsi").val(data.BERAT_ISI);
		$("#txt_beratKosong").val(data.BERAT_KOSONG);
		$("#txt_noSpb").val(data.NO_SPB);
		$("#TYPE_BUAH").val(data.TYPE_BUAH);
		$("#TYPE_TIMBANG").val(data.TYPE_TIMBANG);
		$("#txt_noteTbg").val(data.NOTE);
		if(data.FLAG_TIMBANGAN==1){
			$('#chk_wVehicle').attr('checked',true);
		}
		$("#txt_driver").val(data.DRIVER_NAME);
		$("#txt_jjg").val(data.JJG);
		$("#txt_grd_buahmentah").val(data.GRD_BUAHMENTAH);
		$("#txt_grd_buahbusuk").val(data.GRD_BUAHBUSUK);
		$("#txt_grd_buahkecil").val(data.GRD_BUAHKECIL);
		$("#txt_grd_tangkaipanjang").val(data.GRD_TANGKAIPANJANG);
		$("#txt_grd_brondolan").val(data.GRD_BRONDOLAN);
		$("#txt_grd_lainnya").val(data.GRD_LAINNYA);
		$("#txt_berat_grading").val(data.BERAT_GRADING);
		$("#txt_afd").val(data.AFD);
		$("#txt_supplier").val(data.SUPPLIERCODE);
        $("#txt_frmMode").val("EDIT");
        
        $("#frm_timbang").dialog('open');
    } 
}

function simpan_timbangan(){
    //alert($('#chk_wVehicle').attr('checked'));
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
       	postdata_id['ID_TIMBANGAN'] = $("#txt_idTimbang").val();
        postdata_id['NO_TIKET']= $("#txt_noTiket").val(); 
        postdata_id['TANGGALM'] = $("#txt_tglMasuk").val();
        postdata_id['TANGGALK']= $("#txt_tglKeluar").val();
        postdata_id['WAKTUM'] = $("#txt_jamMasuk").val();
        postdata_id['WAKTUK']= $("#txt_jamKeluar").val();
        postdata_id['NO_KENDARAAN'] = $("#txt_noKendaraan").val();
        postdata_id['JENIS_MUATAN']= $("#txt_jenisMuatan").val();  
        postdata_id['BERAT_ISI'] = $("#txt_beratIsi").val();
        postdata_id['BERAT_KOSONG']= $("#txt_beratKosong").val();
        postdata_id['NO_SPB']= $("#txt_noSpb").val();
        postdata_id['TYPE_BUAH']= $("#TYPE_BUAH").val();
        postdata_id['TYPE_TIMBANG']= $("#TYPE_TIMBANG").val();
        postdata_id['NOTE']= $("#txt_noteTbg").val();
        postdata_id['TYPE_KENDARAAN']=$('#chk_wVehicle').attr('checked');
		//start: Added By Asep, 20130827
		postdata_id['DRIVER_NAME']= $("#txt_driver").val(); 
		postdata_id['JJG']= $("#txt_jjg").val();
		postdata_id['AFD']= $("#txt_afd").val(); 
		postdata_id['SUPPLIERCODE']= $("#txt_supplier").val();

		postdata_id['GRD_BUAHMENTAH']= $("#txt_grd_buahmentah").val();
		postdata_id['GRD_BUAHBUSUK']= $("#txt_grd_buahbusuk").val();
		postdata_id['GRD_BUAHKECIL']= $("#txt_grd_buahkecil").val();
		postdata_id['GRD_TANGKAIPANJANG']= $("#txt_grd_tangkaipanjang").val();
		postdata_id['GRD_BRONDOLAN']= $("#txt_grd_brondolan").val();
		postdata_id['GRD_LAINNYA']= $("#txt_grd_lainnya").val();
		postdata_id['BERAT_GRADING']= $("#txt_berat_grading").val();
		//end: Added By Asep, 20130827
        postdata_id['CRUD'] =  $("#txt_frmMode").val(); 
        
        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_data_timbangan/CRUD_METHOD',
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
                        $("#frm_timbang").dialog('close');    
                    }    
                    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                }    
               });
    }
}

function hapus_timbangan(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_timbangan").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus timbangan dengan NO TIKET : " + data.NO_TIKET + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_TIMBANGAN'] = data.ID_TIMBANGAN;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_data_timbangan/CRUD_METHOD',
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

function tbg_xls(){
    var answer = confirm ("Export data timbangan ?" );
    if (answer){
        // open pdf files in new window
        var postdata_id = {};
        var periode = $("#s_tgl_periode").val(); 
        
        postdata_id['TANGGALM'] = periode;
        postdata_id['CRUD'] =  'XLS';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_data_timbangan/CRUD_METHOD/',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);  
                    if(obj.error===true){
                        alert(obj.status);
                        alert(msg);
                        location.href = obj.status;                        
                    }else{
                        alert(msg);
                        location.href = obj.status;
                        //$("#frm_report").dialog('open');                  
                    }
                }
        });        
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

