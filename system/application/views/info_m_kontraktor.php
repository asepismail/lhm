<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div> 
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Nama Kontraktor</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input class="input" type="text" name="txt_src" id="text_namaKontraktor" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr>
        </table>-->
    </div>
    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="<?= $grid_name; ?>" class="scroll"></table> 
            <div id="<?= $grid_pager; ?>" class="scroll" style="text-align:center;"></div>
        </div> 
    </div>
    
    <div id="btn_section">  
        <button class="testBtn" type="submit" id="add_kontraktor" onclick="add_kontraktor()">Tambah</button>&nbsp; 
    </div>
</div>

<div id="frm_kontraktor">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Kontraktor</span></a></li>
            <li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <!--<li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">Kode Kontraktor</td><td>:</td>
                    <td class="fieldcell"><input style="width: 150px;" disabled="disabled" tabindex="1" type="text" id="txt_kodeKontraktors" name="knt_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Kode Inisial</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_kodeInit" name="knt_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Nama Kontraktor</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_namaKontraktor" name="knt_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Nama Kontak</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" type="text" id="txt_namaKontak" name="knt_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">No Kontak</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" type="text" id="txt_noKontak" name="knt_input" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell">Alamat</td><td>:</td>
                    <td class="fieldcell">
                        <textarea tabindex="6" name="txt_alamat" id="txt_alamat" cols="45" rows="5"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="labelcell">Kota</td><td>:</td>
                    <td class="fieldcell"><input style="width: 125px;" tabindex="7" type="text" id="txt_kota" name="knt_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Provinsi</td><td>:</td>
                    <td class="fieldcell"><input style="width: 125px;" tabindex="8" type="text" id="txt_provinsi" name="knt_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Kode POS</td><td>:</td>
                    <td class="fieldcell"><input style="width: 75px;" tabindex="9" type="text" id="txt_kodePos" name="knt_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Telepon</td><td>:</td>
                    <td class="fieldcell"><input style="width: 75px;" tabindex="10" type="text" id="txt_telepon" name="knt_input" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Email</td><td>:</td>
                    <td class="fieldcell"><input tabindex="11" type="text" id="txt_email" name="knt_input" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell">Bank</td><td>:</td>
                    <td class="fieldcell"><input tabindex="12" type="text" id="txt_bank" name="knt_input" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell">No.Rekening</td><td>:</td>
                    <td class="fieldcell"><input tabindex="13" type="text" id="txt_noRekening" name="knt_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">NPWP</td><td>:</td>
                    <td class="fieldcell"><input tabindex="14" type="text" id="txt_npwp" name="knt_input" maxlength="100"/></td>
                </tr>
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_newkontraktor" onclick="simpan_kontraktor()">Simpan</button>    
        </div>
        <div id="fragment-2">
            
            <div id="kendaraanGrid">
                <table id="list_Kendaraan" class="scroll"></table> 
                <div id="pager_Kendaraan" class="scroll" style="text-align:center;"></div>
            </div>
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

<div id="search_form"></div>  
</body>

<script type="text/javascript">
jQuery(document).ready(function()
{
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $("#frm_kontraktor").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 500,
        width: 620,
        modal: true,
        title: "Kontraktor",
        resizable: false,
        moveable: true,
        buttons: {
            Tutup: function(){
                        //clear_form_elements(this.form);
                        $('input').val(''); 
                        $('textarea').val('');
                        $("#frm_kontraktor").dialog('close');                  
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
            jQuery("#"+"<?= $grid_name; ?>").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_kontraktor/search_data');
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
$(document).ready(function() {
    $('input').val('');
    $('textarea').val('');
});

function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#"+"<?= $grid_name; ?>").setGridParam({url:url+'m_kontraktor/LoadData/'}).trigger("reloadGrid");    
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
var gridimgpath = '<?= $template_path ?>themes/basic/images'; 
 
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var colNamesT_Detail = new Array();
var colModelT_Detail = new Array();

colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('KODE KONTRAKTOR');
colModelT.push({name:'KODE_KONTRAKTOR',index:'KODE_KONTRAKTOR', editable: false, hidden:true, width:10, align:'left'});

colNamesT.push('KODE INISIAL');
colModelT.push({name:'KODE_INISIAL',index:'KODE_INISIAL', editable: false, hidden:false, width:100, align:'left'});

colNamesT.push('NAMA KONTRAKTOR');
colModelT.push({name:'NAMA_KONTRAKTOR',index:'NAMA_KONTRAKTOR', editable: false, hidden:false, width: 200, align:'left'});

colNamesT.push('NAMA KONTAK');
colModelT.push({name:'NAMA_CONTACT',index:'NAMA_CONTACT', editable: false, hidden:false, width: 125, align:'center'});

colNamesT.push('Nmr KONTAK');
colModelT.push({name:'NO_CONTACT',index:'NO_CONTACT', editable: false, hidden:false, width: 80, align:'center'});

colNamesT.push('ALAMAT');
colModelT.push({name:'ALAMAT',index:'ALAMAT', editable: false, hidden:false, width: 200, align:'left'});

colNamesT.push('KOTA');
colModelT.push({name:'KOTA',index:'KOTA', editable: false, hidden:false, width: 120, align:'left'});

colNamesT.push('KODE_POS');
colModelT.push({name:'KODE_POS',index:'KODE_POS', editable: false, hidden:false, width: 80, align:'left'});

colNamesT.push('TELEPON');
colModelT.push({name:'TELEPON',index:'TELEPON', editable: false, hidden:false, width: 80, align:'left'});

colNamesT.push('EMAIL');
colModelT.push({name:'EMAIL',index:'EMAIL', editable: false, hidden:false, width: 80, align:'left'});

colNamesT.push('BANK');
colModelT.push({name:'BANK',index:'BANK', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('NO_REKENING');
colModelT.push({name:'NO_REKENING',index:'NO_REKENING', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('NPWP');
colModelT.push({name:'NPWP',index:'NPWP', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
            jGrid_va = jQuery("#"+"<?= $grid_name; ?>").jqGrid({
                url:url+'m_kontraktor/LoadData/',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#"+"<?= $grid_pager; ?>"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,   
                loadComplete: function(){ 
                    var ids = jQuery("#"+"<?= $grid_name; ?>").getDataIDs();
                    for(var i=0;i<ids.length;i++){ 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

                            jQuery("#"+"<?= $grid_name; ?>").setRowData(ids[i],{act:be+ce}) 
                    }                           
                }, 
                
                 
            });
            jGrid_va.navGrid("#"+"<?= $grid_pager; ?>",{edit:false,del:false,add:false, search: false, refresh: true});
             //######## UPDATE 15 Desember 2010 #########
            jGrid_va.navButtonAdd("#"+"<?= $grid_pager; ?>",{
                   caption:"Export ke Excell", 
                   buttonicon:"ui-icon-add", 
                   onClickButton: function(){ 
                        exportToExcell()
                   }, 
                    position:"left",
            });
            jGrid_va.navButtonAdd("#"+"<?= $grid_pager; ?>",{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            });
             //######## END UPDATE 15 Desember 2010 #########    
         }
         

jQuery("#"+"<?= $grid_name; ?>").ready(loadView); 


</script>

<script type="text/javascript">
function add_kontraktor(){
    //#### SET DEFAULT TABS INDEX
    $("#tabs").tabs();
    $("#tabs").tabs('select',0);
    //###########################
    
    $('input').val('');
    $('textarea').val('');
    jQuery("#list_Kendaraan").clearGridData();
    
    $("#txt_frmMode").val("ADD");
    $("#frm_kontraktor").dialog('open'); 
}

function simpan_kontraktor (){
    if ($("#txt_frmMode").val()=='ADD'){
        var answer = confirm ("Tambah data kontraktor ?" )    
    }else if($("#txt_frmMode").val()=='EDIT'){
        var answer = confirm ("Ubah data kontraktor ?" )
    }else{
        var answer = confirm ("Tambah/Ubah data kontraktor ?" )
    }
    
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        var ids = jQuery("#"+"<?= $grid_name; ?>").getGridParam('selrow'); 
        var data = $("#"+"<?= $grid_name; ?>").getRowData(ids) ;
        
        postdata_id['KODE_KONTRAKTOR'] = data.KODE_KONTRAKTOR;
        postdata_id['KODE_INISIAL'] = $("#txt_kodeInit").val();
        postdata_id['NAMA_KONTRAKTOR'] = $("#txt_namaKontraktor").val();
        postdata_id['NAMA_CONTACT'] =  $("#txt_namaKontak").val();
        postdata_id['NO_CONTACT'] =  $("#txt_noKontak").val();
        postdata_id['ALAMAT'] =  $("#txt_alamat").val();
        postdata_id['KOTA'] =  $("#txt_kota").val();
        postdata_id['KODE_POS'] =  $("#txt_kodePos").val();
        postdata_id['PROPINSI'] =  $("#txt_provinsi").val();
        postdata_id['TELEPON'] =  $("#txt_telepon").val();
        postdata_id['EMAIL'] =  $("#txt_email").val();
        postdata_id['BANK'] =  $("#txt_bank").val();
        postdata_id['NO_REKENING'] =  $("#txt_noRekening").val();
        postdata_id['NPWP'] =  $("#txt_npwp").val();

        postdata_id['CRUD'] =  $("#txt_frmMode").val(); 
        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_kontraktor/CRUD_METHOD',
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
                                $("#frm_kontraktor").dialog('close');  
                            }
                }
        });
    }
}

function update_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#"+"<?= $grid_name; ?>").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $('input').val('');
        $('textarea').val('');
        $("#txt_kodeKontraktors").val(data.KODE_KONTRAKTOR);
        $("#txt_kodeInit").val(data.KODE_INISIAL);
        $("#txt_namaKontraktor").val(data.NAMA_KONTRAKTOR);
        $("#txt_namaKontak").val(data.NAMA_CONTACT);
        $("#txt_noKontak").val(data.NO_CONTACT);
        $("#txt_alamat").val(data.ALAMAT);
        $("#txt_kota").val(data.KOTA);
        $("#txt_kodePos").val(data.KODE_POS);
        $("#txt_provinsi").val(data.PROPINSI);
        $("#txt_telepon").val(data.TELEPON);
        $("#txt_email").val(data.EMAIL);
        $("#txt_bank").val(data.BANK);
        $("#txt_noRekening").val(data.NO_REKENING);
        $("#txt_npwp").val(data.NPWP);
        
        $("#txt_frmMode").val("EDIT");

        jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor/LoadData_Kendaraan/'+data.KODE_KONTRAKTOR}).trigger("reloadGrid");
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_kontraktor").dialog('open');
    } 
}

function hapus_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#"+"<?= $grid_name; ?>").getRowData(ids) ;
    var answer = confirm ("Hapus Data KONTRAKTOR dengan KODE : " + data.KODE_KONTRAKTOR + ", dan NAMA: " + data.NAMA_KONTRAKTOR + " ?" )
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
    
        postdata_id['KODE_KONTRAKTOR'] = data.KODE_KONTRAKTOR;
        postdata_id['CRUD'] =  'DELKTR';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_kontraktor/CRUD_METHOD',
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
</script>

<script type="text/javascript">
function addrow()
{
    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {WEIGHT:jdesc1};
    }
    
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_kendaraan("+i+")'; />"; 
    //alert (rowCount);
    var su=jQuery("#list_Kendaraan").addRowData(i,datArr,'last');
    var act=jQuery("#list_Kendaraan").setRowData(i,{act:sv})  

}

function simpan_kendaraan(i){
    jQuery('#list_Kendaraan').saveCell(ids);
    var answer = confirm ("Tambah data Kendaraan ?" );
    if (answer){
        $("#frm_load").dialog('open');
        
        var postdata_id = {};
        var ids = jQuery("#list_Kendaraan").getGridParam('selrow'); 
        var data = $("#list_Kendaraan").getRowData(ids) ;
        
        postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
        postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
        postdata_id['DESKRIPSI'] = data.DESKRIPSI;
        postdata_id['NOTE'] = data.NOTE;
        
        postdata_id['CRUD'] =  'ADDVH'; 
        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_kontraktor/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status);
                            $("#frm_load").dialog('close');
                        }else{
                            alert(obj.status);
                            $("#frm_load").dialog('close');
                            jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");  
                        }
                }
        });
    }            
}

function update_kendaraan(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_Kendaraan").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di edit...");
    }else{
        jQuery('#list_Kendaraan').saveCell(ids);
        var answer = confirm ("Update data Kendaraan dengan NOPOL: "+data.TMP_NO+" ?" );
        if (answer){
            $("#frm_load").dialog('open');
            var postdata_id = {};
            var ids = jQuery("#list_Kendaraan").getGridParam('selrow'); 
            var data = $("#list_Kendaraan").getRowData(ids) ;
            
            postdata_id['ID_KENDARAAN_KONTRAKTOR'] = data.ID_KENDARAAN_KONTRAKTOR;
            postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
            postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
            postdata_id['DESKRIPSI'] = data.DESKRIPSI;
            postdata_id['NOTE'] = data.NOTE;
            
            postdata_id['CRUD'] =  'EDITVH'; 
            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_kontraktor/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $("#frm_load").dialog('close');
                        }else{
                            alert(msg);
                            $("#frm_load").dialog('close');
                            jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");    
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

function hapus_kendaraan(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_Kendaraan").getRowData(ids) ;
    var answer = confirm ("Hapus Data KENDARAAN kontraktor dengan NOPOL : " + data.NO_KENDARAAN + " ?" )
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
    
        postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
        postdata_id['NO_KENDARAAN'] = data.TMP_NO;
        postdata_id['CRUD'] =  'DELKTR';

        var data = {
                      id:postdata_id
                    };
        data = JSON.stringify(data);
        $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_kontraktor/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                            var obj = jQuery.parseJSON(msg);    
                            if(obj.error===true){
                                alert(obj.status);
                                $("#frm_load").dialog('close');
                            }else{
                                alert(obj.status);
                                $("#frm_load").dialog('close');
                                jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");
                            }
                }
        });
    }    
}
//colNamesT_Detail.push('no');
//colModelT_Detail.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_Detail.push('ID');
colModelT_Detail.push({name:'ID_KENDARAAN_KONTRAKTOR',index:'ID_KENDARAAN_KONTRAKTOR', editable: false, hidden:true, width:75, align:'left'}); 

colNamesT_Detail.push('tmpno');
colModelT_Detail.push({name:'TMP_NO',index:'TMP_NO', editable: false, hidden:true, width:75, align:'left'});

colNamesT_Detail.push('No Kendaraan');
colModelT_Detail.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: true, hidden:false, width:75, align:'left'});

colNamesT_Detail.push('DESKRIPSI');
colModelT_Detail.push({name:'DESKRIPSI',index:'DESKRIPSI', editable: true, hidden:false, width: 200, align:'left'});

colNamesT_Detail.push('NOTE');
colModelT_Detail.push({name:'NOTE',index:'NOTE', editable: true, hidden:false, width: 125, align:'center'});

colNamesT_Detail.push('');
colModelT_Detail.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
            jGrid_va = jQuery("#list_Kendaraan").jqGrid({
                url:url+'m_kontraktor/LoadData_Kendaraan/xx',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_Detail ,
                colModel: colModelT_Detail,
                sortname: colModelT_Detail[0].name,
                pager:jQuery("#pager_Kendaraan"),
                rowNum: 20,
                rownumbers: true,
                height: 150,
                width:500,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellsubmit: 'clientArray',
                cellEdit: true,
                forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_Kendaraan").getDataIDs();
                    for(var i=0;i<ids.length;i++){ 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_kendaraan('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_kendaraan('"+cl+"');\"/>";

                            jQuery("#list_Kendaraan").setRowData(ids[i],{act:be+ce}) 
                    }                           
                }  
            });
            jGrid_va.navGrid("#pager_Kendaraan",{edit:false,del:false,add:false, search: false, refresh: true});

            jGrid_va.navButtonAdd('#pager_Kendaraan',{
               caption:"Tambah Data", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
             
         }
jQuery("#list_Kendaraan").ready(loadView); 
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
    var nama_kontraktor = jQuery("#text_namaKontraktor").val(); 

    if (nama_kontraktor == ""){
        nama_kontraktor = "-";
    }
    
    jQuery("#"+"<?= $grid_name; ?>").setGridParam({url:url+"m_kontraktor/search_data/"+nama_kontraktor}).trigger("reloadGrid");        
} 
</script>
