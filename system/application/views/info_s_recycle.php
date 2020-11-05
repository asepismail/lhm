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
        <button class="testBtn" type="submit" id="add_kontraktor" onclick="add_timbangan()">Input Data Recycle</button>&nbsp;
    </div>

</div> 

<div id="frm_timbang">
    <div id="tabs_timbang">
        <ul class="tabs_timbang">
            <li><a href="#fragment-1"><span>Data Timbangan</span></a></li>
            <li><a href="#fragment-2"><span>BA Recycle</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
            	<tr>               
                    <td class="labelcell">Tanggal</td><td>:</td>
                    <td class="fieldcell"><input tabindex="1" type="text" id="txt_tgl" name="tbg_input"/> *</td>
                </tr>
                <tr>
                    <td class="labelcell">ID Recycle</td><td>:</td>
                    <td class="fieldcell">
                    	<input tabindex="2" type="text" id="txt_idRecycle" name="tbg_input" /> *
                    </td>   
                </tr>  
                
                <tr>                	
                    <td class="labelcell">Jenis</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_jenis" name="tbg_input"/> *</td>
                </tr> 
                <tr> 
                    <td class="labelcell">ID Komoditas</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_idKomoditas" name="tbg_input" disabled="disabled"/> *</td>
                </tr>
                <tr>
                    <td class="labelcell">Tanggal Masuk</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_tglMasuk" name="tbg_input" disabled="disabled"/> *</td>
                </tr> 
                <tr> 
                    <td class="labelcell">Tangal Keluar</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" type="text" id="txt_tglKeluar" name="tbg_input" disabled="disabled"/> *</td>
                </tr>
               
                <tr>
                    <td class="labelcell">Jam Masuk</td><td>:</td>
                    <td class="fieldcell"><input tabindex="7" type="text" id="txt_jamMasuk" name="tbg_input" disabled="disabled"/> *</td>
                </tr> 
                <tr> 
                    <td class="labelcell">Jam Keluar</td><td>:</td>
                    <td class="fieldcell"><input tabindex="8" type="text" id="txt_jamKeluar" name="tbg_input" disabled="disabled"/> *</td>
                </tr>
                
  <tr>
                    <td class="labelcell">No Kendaraan</td><td>:</td>
                    <td class="fieldcell"><input tabindex="9" type="text" id="txt_noKendaraan" name="tbg_input" disabled="disabled"/> *</td>
                    </tr> 
                    <tr> 
                    <td class="labelcell">Driver</td><td>:</td>
          			<td class="fieldcell"><input tabindex="10" type="text" id="txt_driver" name="tbg_input" disabled="disabled"/> *</td>
                </tr>

                <tr>
                    <td class="labelcell">Berat Isi</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_beratIsi" name="tbg_input" disabled="disabled"/> *</td>
                    </tr> 
                    <tr> 
                    <td class="labelcell">Berat Kosong</td><td>:</td>
                    <td class="fieldcell"><input tabindex="12" type="text" id="txt_beratKosong" name="tbg_input" disabled="disabled"/> *</td>
                </tr>
                <tr>
                    <td class="labelcell">Berat Bersih</td><td>:</td>
                    <td class="fieldcell"><input tabindex="13" type="text" id="txt_beratBersih" name="tbg_input" disabled="disabled"/> *</td>
                    </tr> 
                    <tr> 
                    <td class="labelcell">No SIM</td><td>:</td>
                    <td class="fieldcell"><input tabindex="14" type="text" id="txt_sim" name="tbg_input" /></td>
                </tr>
            </table>
            <br>                
        </div>

        <div id="fragment-2">
        	<table width="100%" border="0" class="teks_" id="input_table" >
             <tr>
                    <td class="labelcell">ID Dispatch</td><td>:</td>
                    <td class="fieldcell"><input tabindex="15" type="text" id="txt_idDispatch" name="tbg_input"/> *No Tiket Kirim</td>
                </tr>
                <tr>
                    <td class="labelcell">ID DO</td><td>:</td>
                    <td class="fieldcell"><input tabindex="16" type="text" id="txt_idDO" name="tbg_input" disabled="disabled"/> *</td>
                </tr>
            	<tr>
                    <td class="labelcell">No BA</td><td>:</td>
                    <td class="fieldcell"><input tabindex="17" type="text" id="txt_noBA" name="tbg_input"/> *No Berita Acara yang diterbitkan oleh site</td>
                </tr>
            	<tr>
                    <td class="labelcell">Dirty</td><td>:</td>
                    <td class="fieldcell"><input tabindex="18" type="text" id="txt_dirt" name="tbg_input" /> *</td>
                </tr>
                <tr>
                    <td class="labelcell">Moist</td><td>:</td>
                    <td class="fieldcell"><input tabindex="19" type="text" id="txt_moist" name="tbg_input"/> *</td>
                </tr>
                <tr>
                    <td class="labelcell">Broken/FFA</td><td>:</td>
                    <td class="fieldcell"><input tabindex="20" type="text" id="txt_broken" name="tbg_input"/> *</td>
                </tr>
                <tr>
                    <td class="labelcell">Note</td><td>:</td>
                    <td class="fieldcell"><textarea tabindex="21" name="tbg_input" id="txt_desc" cols="45" rows="4"></textarea>
                    *Alasan recycle</td> 
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
	$("#txt_tgl").change(resetAutocomplete);
	
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $('input').val('');

    $("#frm_timbang").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 550,
        width: 610,
        modal: true,
        title: "Recyle CPO Kernel",
        resizable: true,
        moveable: true,
        buttons: {
			Simpan: function(){
                        simpan_timbangan();     
                    },
			Tutup: function(){
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
            jQuery("#list_timbangan").smartSearchPanel('#search_form', {dialog:{width: 530}},'s_recycle/search_data');
        }
    });
	
	function resetAutocomplete(){
		$("#txt_idRecycle").autocomplete( 
				url+"s_recycle/get_no_tiket/"+$("#txt_tgl").val(), {
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
				$("#txt_tglMasuk").val(item.res_dTglM );
				$("#txt_tglKeluar").val(item.res_dTglK );
				$("#txt_jamMasuk").val(item.res_dWaktuM );
				$("#txt_jamKeluar").val(item.res_dWaktuK );
				$("#txt_beratBersih").val(item.res_dNetto );
				$("#txt_beratKosong").val(item.res_dTara );
				$("#txt_beratIsi").val(item.res_dBruto );
				$("#txt_driver").val(item.res_dName );
				$("#txt_noKendaraan").val(item.res_dKendaraan );
		});   
		
		$("#txt_idDispatch")
          .autocomplete( 
		  	url+"s_recycle/get_doc/"+$("#txt_tgl").val(), {
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
				$("#txt_idDispatch").val(item.res_id);
				$("#txt_idDO").val(item.res_do);
		}); 
	}
  	
    $("#txt_jenis")
          .autocomplete( 
            url+"s_recycle/get_jenis/"+$("#txt_jenis").val(), {
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
				$("#txt_idKomoditas").val(item.res_id);
				$("#txt_jenis").val(item.res_Jenis);
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
	/*
    var $tabs_nab = $('#tabs_nab').tabs();
    var selected_nab = $tabs_nab.tabs('option', 'selected'); // => 0                 
    $("#tabs_nab").tabs();
    */
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
}
</script>

<script type="text/javascript">
$(function() {
	$("#txt_tgl").datepicker({dateFormat:"yy-mm-dd"});
});

function get_periode(){
    var periode = $("#s_tgl_periode").val();
    return periode;
}
</script> 

<script type="text/javascript">

var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var colNamesN = new Array();
var colModelN = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';   

colNamesN.push('No');
colModelN.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 5, align:'center'});

colNamesN.push('ID RECYCLE');
colModelN.push({name:'ID_RECYCLE',index:'ID_RECYCLE', editable: false, hidden:false, width: 150, align:'left'});

colNamesN.push('ID DISPATCH');
colModelN.push({name:'ID_DISPATCH',index:'ID_DISPATCH', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('NO KEND');
colModelN.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: true, hidden:false, width: 80, align:'left',sortable:true});

colNamesN.push('TGL');
colModelN.push({name:'TANGGAL',index:'TANGGAL', editable: true, hidden:false, width: 90, align:'left',formatter:'date'});

colNamesN.push('ID KOMODITAS');
colModelN.push({name:'ID_KOMODITAS',index:'ID_KOMODITAS', editable: true, hidden:true, width: 10, align:'left',sortable:true});

colNamesN.push('BERAT KOSONG');
colModelN.push({name:'BERAT_KOSONG',index:'BERAT_KOSONG', editable: true, hidden:true, width: 10, align:'right'});

colNamesN.push('BERAT ISI');
colModelN.push({name:'BERAT_ISI',index:'BERAT_ISI', editable: true, hidden:true, width: 10, align:'right'});

colNamesN.push('Kg');
colModelN.push({name:'BERAT_BERSIH',index:'BERAT_BERSIH', editable: true, hidden:false, width: 50, align:'right'});

colNamesN.push('COMPANY CODE');
colModelN.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true, hidden:true, width: 5, align:'left'});

colNamesN.push('ID_DO');
colModelN.push({name:'ID_DO',index:'ID_DO', editable: true, hidden:false, width: 210, align:'left'});

colNamesN.push('TANGGALM');
colModelN.push({name:'TANGGALM',index:'TANGGALM', editable: true, hidden:true, width: 10, align:'left',formatter:'date'});

colNamesN.push('TANGGALK');
colModelN.push({name:'TANGGALK',index:'TANGGALK', editable: true, hidden:true, width: 10, align:'left',formatter:'date'});

colNamesN.push('WAKTUM');
colModelN.push({name:'WAKTUM',index:'WAKTUM', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('WAKTUK');
colModelN.push({name:'WAKTUK',index:'WAKTUK', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('BROKEN');
colModelN.push({name:'BROKEN',index:'BROKEN', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('DIRTY');
colModelN.push({name:'DIRTY',index:'DIRTY', editable: true, hidden:false, width: 50, align:'left'});

colNamesN.push('MOIST');
colModelN.push({name:'MOIST',index:'MOIST', editable: true, hidden:false, width: 50, align:'left'});

colNamesN.push('DRIVER');
colModelN.push({name:'DRIVER_NAME',index:'DRIVER_NAME', editable: true, hidden:false, width: 90, align:'left'});

colNamesN.push('JENIS');
colModelN.push({name:'JENIS',index:'JENIS', editable: true, hidden:false, width: 50, align:'left'});

colNamesN.push('NO_SIM');
colModelN.push({name:'NO_SIM',index:'NO_SIM', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('DESCRIPTION');
colModelN.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('NO_BA');
colModelN.push({name:'NO_BA',index:'NO_BA', editable: true, hidden:true, width: 10, align:'left'});

colNamesN.push('');
colModelN.push({name:'act',index:'act', editable: false, hidden:false, width: 80, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_timbangan").jqGrid(
            {
                url:url+'s_recycle/LoadData/'+get_periode(),
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
     jQuery("#list_timbangan").setGridParam({url:url+'s_recycle/LoadData/'+get_periode()}).trigger("reloadGrid");    
}

function get_data(){
    var periode = $("#s_tgl_periode").val();    
    jQuery("#list_timbangan").setGridParam({url:url+'s_recycle/LoadData/'+periode}).trigger("reloadGrid");    
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
                    url:url+'s_recycle/do_upload/'+periode+'/'+periode_to,
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
	var periode = $("#tahun").val() + $("#bulan").val();
    return periode;    
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
/*
function gridReload(){ 
    var spb = jQuery("#txt_nabSrc").val();
    var no_kend = jQuery("#txt_kendaraanSrc").val();   

    if (spb == ""){
        spb = "-";
    }
    if (no_kend == ""){
        no_kend = "-";
    } 
    
    jQuery("#list_notaangkut").setGridParam({url:url+"s_recycle/search_spb/"+spb+"/"+no_kend+"/"+get_periode()}).trigger("reloadGrid");        
} 
*/
</script>

<script type="text/javascript">
function add_timbangan(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs_timbang").tabs();
    $("#tabs_timbang").tabs('select',0);
    //###########################
    
    $("#txt_idRecycle").val('');
	$("#txt_idDispatch").val('');
	$("#txt_noKendaraan").val('');
	$("#txt_tgl").val('');
	$("#txt_idKomoditas").val('');
	$("#txt_beratKosong").val('');
	$("#txt_beratIsi").val('');
	$("#txt_beratBersih").val('');
	$("#txt_idDO").val('');		
    $("#txt_tglMasuk").val('');
    $("#txt_tglKeluar").val('');
	$("#txt_jamMasuk").val('');
    $("#txt_jamKeluar").val('');		
	$("#txt_broken").val('');
	$("#txt_dirt").val('');
	$("#txt_moist").val('');	
	$("#txt_driver").val('');
	$("#txt_jenis").val('');
	$("#txt_sim").val('');		
	$("#txt_noBA").val('');		
	$("#txt_desc").val('');
    $("#txt_frmMode").val("ADD");
    $("#frm_timbang").dialog('open');    
    //#### SET DEFAULT DROPDOWN VALUE 
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
        
        var tDate= formatDate(new Date(getDateFromFormat(data.TANGGAL,'dd/MM/yyyy')),'yyyy-MM-dd');
        var nDate= formatDate(new Date(getDateFromFormat(data.TANGGALM,'dd/MM/yyyy')),'yyyy-MM-dd');
		var kDate= formatDate(new Date(getDateFromFormat(data.TANGGALK,'dd/MM/yyyy')),'yyyy-MM-dd');
				
		$("#txt_idRecycle").val(data.ID_RECYCLE);
		$("#txt_idDispatch").val(data.ID_DISPATCH);
		$("#txt_noKendaraan").val(data.NO_KENDARAAN);
		$("#txt_tgl").val(tDate);
		$("#txt_idKomoditas").val(data.ID_KOMODITAS);
		$("#txt_beratKosong").val(data.BERAT_KOSONG);
		$("#txt_beratIsi").val(data.BERAT_ISI);
		$("#txt_beratBersih").val(data.BERAT_BERSIH);
		$("#txt_idDO").val(data.ID_DO);		
        $("#txt_tglMasuk").val(nDate);
        $("#txt_tglKeluar").val(kDate);
		$("#txt_jamMasuk").val(data.WAKTUM);
        $("#txt_jamKeluar").val(data.WAKTUK);		
		$("#txt_broken").val(data.BROKEN);
		$("#txt_dirt").val(data.DIRTY);
		$("#txt_moist").val(data.MOIST);	
		$("#txt_driver").val(data.DRIVER_NAME);
		$("#txt_jenis").val(data.JENIS);
		$("#txt_sim").val(data.NO_SIM);		
		$("#txt_desc").val(data.DESCRIPTION);
		$("#txt_noBA").val(data.NO_BA);	
		
        $("#txt_frmMode").val("EDIT");
        
        $("#frm_timbang").dialog('open');
    } 
}

function simpan_timbangan(){
    var answer = confirm ("Tambah Data ? ")
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};

       	postdata_id['ID_RECYCLE'] = $("#txt_idRecycle").val();
        postdata_id['ID_DISPATCH']= $("#txt_idDispatch").val(); 
        postdata_id['NO_KENDARAAN'] = $("#txt_noKendaraan").val();		
        postdata_id['TANGGAL']= $("#txt_tgl").val();
        postdata_id['ID_KOMODITAS'] = $("#txt_idKomoditas").val();		
        postdata_id['BERAT_KOSONG']= $("#txt_beratKosong").val();		
        postdata_id['BERAT_ISI'] = $("#txt_beratIsi").val();		
        postdata_id['BERAT_BERSIH']= $("#txt_beratBersih").val(); 		
        postdata_id['ID_DO'] = $("#txt_idDO").val();		
        postdata_id['TANGGALM']= $("#txt_tglMasuk").val();		
        postdata_id['TANGGALK']= $("#txt_tglKeluar").val();		
        postdata_id['WAKTUM']= $("#txt_jamMasuk").val();
        postdata_id['WAKTUK']= $("#txt_jamKeluar").val();		
        postdata_id['BROKEN']= $("#txt_broken").val();
        postdata_id['DIRTY']=$('#txt_dirt').val();
		postdata_id['MOIST']= $("#txt_moist").val(); 
		postdata_id['DRIVER_NAME']= $("#txt_driver").val();
		postdata_id['JENIS']= $("#txt_jenis").val(); 
		postdata_id['NO_SIM']= $("#txt_sim").val();
		postdata_id['DESCRIPTION']= $("#txt_desc").val();
		postdata_id['NO_BA']= $("#txt_noBA").val();
        postdata_id['CRUD'] =  $("#txt_frmMode").val(); 
        
        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'s_recycle/CRUD_METHOD',
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
        var answer = confirm ("Hapus timbangan dengan NO TIKET : " + data.ID_RECYCLE + " ?" );
        if (answer){
            $("#frm_load").dialog('open');
            
            var postdata_id = {};
    
            postdata_id['ID_RECYCLE'] = data.ID_RECYCLE;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'s_recycle/CRUD_METHOD',
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

