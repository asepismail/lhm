<? 
    $template_path = base_url().$this->config->item('template_path');  
?>

<br>
<div id='main_form'>
    <div id"gridSearch">  
        <div><?php //echo $search; ?></div> 
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <!-- <tr>
                <td class="labelcell">Kode Storage</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="1" class="input" type="text" id="txt_kodeSrc" name="strg_input" maxlength="50" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr> -->
            <tr>
                <td class="labelcell">Periode</td><td>:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr>
            <tr>
                <td class="labelcell">Jenis Barang</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <? echo $jenis; ?>
                </td>
            </tr>
            <tr>
                <td class="labelcell">No. Tiket</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="3" class="input" type="text" id="txt_soSrc" name="strg_input" maxlength="25" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr>
        </table>
    </div>

    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_dispatch" class="scroll"></table> 
            <div id="pager_dispatch" class="scroll" style="text-align:center;"></div>
        </div>
    </div>
    <div id="btn_section">
        <button class="testBtn" type="submit" onclick="tambah_dispatch()">Buat Transaksi Baru</button>&nbsp; 
    </div>
</div> 

<div id="frm_dispatch">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Data Dispatch Asal</span></a></li>
            <li><a href="#fragment-2"><span>Data Dispatch Tujuan</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
        	<table width="100%" border="0" class="teks_" id="input_table" >
            	<tr>
                    <td class="labelcell">TANGGAL KIRIM</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_dptchPeriodeKirim" name="dptch_input" maxlength="50"/>*</td>
                    </tr>
            	<tr>
                    <td class="labelcell">NO TIKET KIRIM</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_tiket_asal" name="dptch_input" maxlength="50"/>*</td>
                    </tr>
                <tr>
                    <td class="labelcell">JENIS MUATAN</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_dptchJenisBrg" name="dptch_input" maxlength="50" disabled="disabled"/>*</td>
                    
                <tr>
                	<td class="labelcell"></td><td></td>
                    <td class="fieldcell"><input type="text" id="txt_dptchJenBrg" name="dptch_input" maxlength="50" disabled="disabled"/>*</td>
                </tr>
                <tr>
                    <td class="labelcell">ID DO</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_do" name="dptch_input" maxlength="50" disabled="disabled"/>*</td>
               </tr>
                <tr>
                    <td class="labelcell">DRIVER PENGIRIM</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_driver" name="dptch_input" maxlength="50" disabled="disabled"/>*</td>
                </tr>
                <tr>
                    <td class="labelcell">NO KENDARAAN</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_dptchNoKend" name="dptch_input" maxlength="50" disabled="disabled"/>*</td>
                </tr>
            </table>
            <br>
            <p class="labelcell"> * Wajib diisi </p>
            <!-- <button class="testBtn" type="submit" id="cmd_saveDptch" onclick="simpan_dispatch()">Simpan</button>    -->
        </div>
        <div id="fragment-2" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
            	<tr>
                    <td class="labelcell">NO TIKET</td><td>:</td>
                    <td class="fieldcell"><input  style="width: 150px;" type="text" id="txt_dptchID" name="dptch_input" maxlength="50"/>
*</td>                    
                </tr>
                               <tr>
                    <td class="labelcell">TANGGAL MASUK</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_dptchPeriode" name="dptch_input" maxlength="50"/>*</td>                    
                </tr>
                <tr>
                    <td class="labelcell">TANGGAL KELUAR</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_dptchPeriodeKeluar" name="dptch_input" maxlength="50"/>*</td>                    
                </tr>
                <tr>
                    <td class="labelcell">JAM MASUK</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_jam_masuk" name="dptch_input" maxlength="50"/>*</td>                    
                </tr>
                <tr>
                    <td class="labelcell">JAM KELUAR</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_jam_keluar" name="dptch_input" maxlength="50"/>*</td>                    
                </tr>
                <tr>
                    <td class="labelcell">BERAT KOSONG</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_dptchBerKos" name="dptch_input" maxlength="25"/>*</td>
                </tr>
                <tr>
                    <td class="labelcell">BERAT ISI</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_dptchBerIsi" name="dptch_input" maxlength="25"/>*</td>
                </tr>
                
                </table>
                <br>
                <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">BROKEN/FFA</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_broken" name="dptch_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">DIRTY</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_dirty" name="dptch_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">MOIST</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_moist" name="dptch_input" maxlength="25"/></td>
                </tr>
            </table>
            <br>
            <p class="labelcell"> * Wajib diisi</p>
        </div>
        
    </div>
    <input type="hidden" id="txt_id"/>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_load">
    Wait...
</div>

<div id="search_form"></div> 
</body>

<script type="text/javascript">
jQuery(document).ready(function(){
	$("#txt_dptchPeriodeKirim").change(resetAutocomplete);
    $('input').val('');
    
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    $("#frm_dispatch").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "DISPATCH",
        resizable: true,
        moveable: true,
        buttons: {
			Simpan: function() { 
						simpan_dispatch();      
                    },
            Tutup: function() {
                        $('input').val('');
                        $("#frm_dispatch").dialog('close');        
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
            jQuery("#list_dispatch").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_catat_dispatch/search_data');
        }
    });
});

function resetAutocomplete(){
    $("#txt_tiket_asal").autocomplete( 
            url+"s_catat_dispatch/get_no_tiket/"+$("#txt_dptchPeriodeKirim").val(), {
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
            $("#txt_dptchJenisBrg").val(item.res_komoditas);
            $("#txt_dptchJenBrg").val(item.res_jenis );
			$("#txt_do").val(item.res_do );
			$("#txt_driver").val(item.res_dName );
			$("#txt_dptchNoKend").val(item.res_dKendaraan );
    });   
}

$(function() {
	/*
    $("#txt_periodeSrc").datepicker({
        dateFormat:"yy-mm-dd",
        onSelect: function(dateText, inst){
           doSearch(arguments[0]||event);
        }
    });
	*/
    
    $("#txt_dptchPeriode").datepicker({dateFormat:"yy-mm-dd"}); 
	$("#txt_dptchPeriodeKeluar").datepicker({dateFormat:"yy-mm-dd"}); 
	$("#txt_dptchPeriodeKirim").datepicker({dateFormat:"yy-mm-dd"}); 
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
function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}

function get_jenis(){
    var ljenis = $("#jenis").val();
    return ljenis;    
}
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
    //var url = "<?= base_url().'index.php/' ?>";
    $("#txt_strgID")
          .autocomplete( 
            url+"s_catat_dispatch/get_storage/", {
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
    
    $("#txt_dptchJenisBrg")
          .autocomplete( 
            url+"s_catat_dispatch/get_komoditi/", {
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
                $('#txt_dptchJenBrg').val(item.res_name);
               
          });

    $('#tahun').change(function() {
        //get_periode();
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        //get_periode();
        reloadGrid(); 
    });
	
	$('#jenis').change(function() {
        //get_periode();
        doSearch(); 
    });
})

</script>

<script type="text/javascript">
function reloadGrid(){
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_dispatch").setGridParam({url:url+'s_catat_dispatch/LoadData/'+get_periode()}).trigger("reloadGrid");    
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
    var kode = jQuery("#txt_soSrc").val();
    //var periode = jQuery("#txt_periodeSrc").val();
    var jenis = jQuery("#txt_jnsSrc").val();
    if (kode == ""){
        kode = "-";
    }

    if (jenis == ""){
        jenis = "-";
    }
 

    jQuery("#list_dispatch").setGridParam({url:url+"s_catat_dispatch/search_data/"+kode+"/"+get_periode()+"/"+get_jenis()}).trigger("reloadGrid");        
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

colNamesT.push('ID');
colModelT.push({name:'ID',index:'ID', editable: false, hidden:true, width: 150, align:'left'});

colNamesT.push('NO TIKET');
colModelT.push({name:'ID_DISPATCH',index:'ID_DISPATCH', editable: false, hidden:false, width: 150, align:'left'});

colNamesT.push('NO KENDARAAN');
colModelT.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: false, hidden:false, width: 120, align:'left'});

colNamesT.push('NO TIKET KIRIM');
colModelT.push({name:'ID_DISPATCH_KIRIM',index:'ID_DISPATCH_KIRIM', editable: false, hidden:false, width: 150, align:'left'});

colNamesT.push('TGL KIRIM');
colModelT.push({name:'TANGGAL_KIRIM',index:'TANGGAL_KIRIM', editable: false, hidden:false, width: 100, align:'center',formatter: 'date'});

colNamesT.push('JENIS');
colModelT.push({name:'ID_KOMODITAS',index:'ID_KOMODITAS', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BERAT KOSONG');
colModelT.push({name:'BERAT_KOSONG',index:'BERAT_KOSONG', editable: false, hidden:false, width: 120, align:'right'});

colNamesT.push('BERAT ISI');
colModelT.push({name:'BERAT_ISI',index:'BERAT_ISI', editable: false, hidden:false, width: 120, align:'right'});

colNamesT.push('BERAT BERSIH');
colModelT.push({name:'BERAT_BERSIH',index:'BERAT_BERSIH', editable: false, hidden:false, width: 120, align:'right'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('ID_DO');
colModelT.push({name:'ID_DO',index:'ID_DO', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('TGL TERIMA');
colModelT.push({name:'TANGGALM',index:'TANGGALM', editable: false, hidden:false, width: 90, align:'center',formatter: 'date'});

colNamesT.push('TANGGALK');
colModelT.push({name:'TANGGALK',index:'TANGGALK', editable: false, hidden:true, width: 10, align:'center',formatter: 'date'});

colNamesT.push('WAKTUM');
colModelT.push({name:'WAKTUM',index:'WAKTUM', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('WAKTUK');
colModelT.push({name:'WAKTUK',index:'WAKTUK', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('BROKEN');
colModelT.push({name:'BROKEN',index:'BROKEN', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('DIRTY');
colModelT.push({name:'DIRTY',index:'DIRTY', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('MOIST');
colModelT.push({name:'MOIST',index:'MOIST', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('DRIVER_NAME');
colModelT.push({name:'DRIVER_NAME',index:'DRIVER_NAME', editable: false, hidden:true, width: 10, align:'left'});

colNamesT.push('JENIS');
colModelT.push({name:'JENIS',index:'JENIS', editable: false, hidden:false, width: 50, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 75, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_dispatch").jqGrid(
            {
                url:url+'s_catat_dispatch/LoadData/'+get_periode(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_dispatch"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_dispatch").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img title='Edit' style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"edit_dispatch('"+cl+"');\" />"; 
                            ce = "<img title='Hapus' style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_dispatch('"+cl+"');\"/>";
                            //pr = "<img src='<?= $template_path ?>themes/base/images/print.png' width='12px' height='13px' onclick=\"print_nota('"+cl+"');\"/>";
                            jQuery("#list_dispatch").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
            //jGrid_va.navGrid('#pager_dispatch',{edit:false,del:false,add:false, refresh: true},{},{},{},{multipleSearch:true});
            jGrid_va.navGrid('#pager_dispatch',{edit:false,del:false,add:false,search:false,refresh:true});
            jGrid_va.navButtonAdd('#pager_dispatch',{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });
           
         }
jQuery("#list_dispatch").ready(loadView);
</script>

<script type="text/javascript">
function tambah_dispatch(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    $('input').val('');
	$("#txt_tiket_asal").attr('disabled','');
	$("#txt_dptchPeriodeKirim").attr('disabled',''); 
    $("#txt_frmMode").val("ADD");
    $("#frm_dispatch").dialog('open');    
}

function simpan_dispatch(){
    var answer;
    if ($("#txt_frmMode").val()=='ADD'){
        answer = confirm ("Tambah data Transaksi ?" )    
    }else if($("#txt_frmMode").val()=='EDIT'){
        answer = confirm ("Ubah data Transaksi ?" )
    }else{
        answer = confirm ("Tambah/Ubah data Transaksi ?" )
    }
    
    if (answer){
        $("#frm_load").dialog('open');
        
        var postdata_id = {};
		postdata_id['TANGGAL_KIRIM'] = $("#txt_dptchPeriodeKirim").val();
		postdata_id['ID_DISPATCH_KIRIM'] = $("#txt_tiket_asal").val();
		postdata_id['ID_KOMODITAS'] =  $("#txt_dptchJenisBrg").val();
		postdata_id['JENIS'] =  $("#txt_dptchJenBrg").val();
		postdata_id['ID_DO'] =  $("#txt_do").val();
		postdata_id['DRIVER_NAME'] =  $("#txt_driver").val();
		postdata_id['NO_KENDARAAN'] =  $("#txt_dptchNoKend").val();
		
		postdata_id['ID'] = $("#txt_id").val();
        postdata_id['ID_DISPATCH'] = $("#txt_dptchID").val();
        postdata_id['TANGGALM'] = $("#txt_dptchPeriode").val();
        postdata_id['TANGGALK'] = $("#txt_dptchPeriodeKeluar").val();
		postdata_id['WAKTUM'] = $("#txt_jam_masuk").val();
		postdata_id['WAKTUK'] = $("#txt_jam_keluar").val();		
        postdata_id['BERAT_KOSONG'] =  $("#txt_dptchBerKos").val();
        postdata_id['BERAT_ISI'] =  $("#txt_dptchBerIsi").val();
		
		postdata_id['BROKEN'] = $("#txt_broken").val();		
        postdata_id['DIRTY'] =  $("#txt_dirty").val();
        postdata_id['MOIST'] =  $("#txt_moist").val();
		
        postdata_id['CRUD'] =  $("#txt_frmMode").val();

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_catat_dispatch/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                        $("#frm_dispatch").dialog('close');
                        reloadGrid();  
                    }
                }
           });
    }
}

function edit_dispatch(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_dispatch").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
		var kDate= formatDate(new Date(getDateFromFormat(data.TANGGAL_KIRIM,'dd/MM/yyyy')),'yyyy-MM-dd');
		var mDate= formatDate(new Date(getDateFromFormat(data.TANGGALM,'dd/MM/yyyy')),'yyyy-MM-dd');
		var klDate= formatDate(new Date(getDateFromFormat(data.TANGGALK,'dd/MM/yyyy')),'yyyy-MM-dd');
		
        $('input').val('');
        $("#txt_frmMode").val("EDIT");
		$("#txt_tiket_asal").attr('disabled','true');
		$("#txt_dptchPeriodeKirim").attr('disabled','true'); 
		    
        $("#txt_tiket_asal").val(data.ID_DISPATCH_KIRIM);
		$("#txt_dptchPeriodeKirim").val(kDate);
		$("#txt_dptchJenisBrg").val(data.ID_KOMODITAS);
		$("#txt_dptchJenBrg").val(data.JENIS);
		$("#txt_do").val(data.ID_DO);
		$("#txt_driver").val(data.DRIVER_NAME);
		$("#txt_dptchNoKend").val(data.NO_KENDARAAN);
		
		$("#txt_id").val(data.ID);
		$("#txt_dptchID").val(data.ID_DISPATCH);		
        $("#txt_dptchPeriode").val(mDate);
        $("#txt_dptchPeriodeKeluar").val(klDate);
        $("#txt_jam_masuk").val(data.WAKTUM);
		$("#txt_jam_keluar").val(data.WAKTUK);		
        $("#txt_dptchBerKos").val(data.BERAT_KOSONG);
        $("#txt_dptchBerIsi").val(data.BERAT_ISI);
		
		$("#txt_broken").val(data.BROKEN);
		$("#txt_dirty").val(data.DIRTY);
		$("#txt_moist").val(data.MOIST);
        
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_dispatch").dialog('open');
    } 
}

function hapus_dispatch(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_dispatch").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Transaksi dengan ID : " + data.ID_DISPATCH + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_DISPATCH'] = data.ID_DISPATCH;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_catat_dispatch/CRUD_METHOD',
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

