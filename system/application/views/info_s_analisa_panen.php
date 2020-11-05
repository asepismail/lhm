<style type="text/css">
    #dialog label, #dialog input { display:block; }
    #dialog label { margin-top: 0.5em; }
    #dialog input, #dialog textarea { width: 95%; }
    #tabs { margin-top: 1em; }
    #tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
    #add_tab { cursor: pointer; }
</style> 

<div id='main_form'>
    <div id"gridSearch">  
        <table border="0" class="teks_"  cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan : </td></tr>
            <tr>
                <td class="labelcell">Periode</td><td>:</td>
                <td class="fieldcell"><input type="text" id='s_tgl_periode' ></td>
                <td class="fieldcell" id="periode_to">- <input type="text" id='s_tgl_periode_to' ></td>
                 
            </tr>
            <tr>
                <td class="labelcell">Jenis Laporan</td><td>:</td>
                <td class="fieldcell">
                    <select name="jns_laporan" class="select" id="jns_laporan" onChange="check()">
                    <option value=''> -- pilih -- </option>
                    <option value='pnperblock'>Laporan Produksi Kebun (Panen)</option>
                    <option value='sumperblock'>Summary Produksi Kebun (Panen)</option>
                    <option value='monitortonase'>Summary Tonase TBS</option>
                    <!-- <option value='tbs'>Laporan Produksi Pabrik</option> -->
                    <option value='bjr'>Laporan BJR</option>
                    <option value='nabdist'>Laporan Distribusi NAB</option>
                    <option value='jjgakt'>Laporan Janjang Angkut (NAB)</option>
                    <!--<option value='tbg'>Timbangan</option>-->
                    <option value='ademdpc'>Adem Import (Dispatch All)</option>
                    <option value='tbsin'>Adem Import (TBS IN)</option>
                    <option value='tbsout'>Adem Import (TBS OUT)</option>
                    <option value='tbsplasma'>Adem Import (TBS PLASMA)</option>
                    <option value='tbsluar'>Adem Import (TBS LUAR)</option>
		      <option value='tbsaff'>Adem Import (TBS AFILIASI)</option>
                    <option value='cpoin'>Adem Import - CPO (Produksi CPO)</option>
                    <option value='pkin'>Adem Import - PK (Produksi PK)</option>
                    <!--<option value='pkin'>Adem Import (KERNEL IN)</option>-->
                    <option value='nabdt'>Export - NAB</option> 
                    <option value='tbgdt'>Export - BERAT EMPIRIS DAN REAL PKS TIMBANGAN KEBUN</option>
		      <option value='tbgdtpksluar'>Export - BERAT EMPIRIS DAN REAL PKS TIMBANGAN LUAR</option>
                    <option value='tbgdtkebun'>Export - BERAT EMPIRIS DAN REAL KEBUN</option>
                    <option value='tbgdtluar'>Export - TBG (Buah Luar)</option>
                    <option value='tbgkebun'>Export - TBG KEBUN (MSS, ASL, SSS)</option>
                    <option value='bjrttp'>Export - BJR Ditetapkan</option>
                    <option value='snd'>Export - Sounding</option>
                    <option value='tbgbatpanen'>TBG - BA Transport Panen</option>
                    <option value='tbglampbatpanen'>TBG - Lampiran BA Transport Panen</option>
                    <option value='dttbgbatpanen'>TBG - Detail BA Transport Panen</option>
                    <option value='titipolah'>Titip Olah GKM</option>
		      <option value='titipolahsmi'>Titip Olah SMI</option>
                    <option value='restan'>Laporan Restan</option>
                    <option value='afkir'>Laporan Afkir</option>
                    <option value='scrap'>Export - Scrap</option>
		      <option value='nabnul'>Maintain NAB</option>
                    </select>
                </td>
                <td><?php if(isset($kontraktorbuah)){ echo $kontraktorbuah; } ?></td>
                <td><?php if(isset($afd)){ echo $afd; } ?></td> 
            </tr>
            <tr>
                <td class="labelcell">Format Laporan</td><td>:</td>
                <td class="fieldcell">
                    <select name="fr_laporan" class="select" id="fr_laporan">
                    <option value=''> -- pilih -- </option>
                    <option value='html'>preview</option>
                    <option value='xls'>excell</option>
                    <option value='pdf'>PDF</option>
                    <option value='csv'>CSV</option>
                    </select>
                </td>
            </tr>
            <tr><td>
                <div id="btn_section">
                     <button class="testBtn" type="submit" id="add_kontraktor" onclick="generate_lhm_nab()">Generate</button>&nbsp; 
                     <!--<button class="testBtn" type="submit" id="testpg" onclick="test_pg()">test</button>&nbsp;-->  
                </div>
                </td>
            </tr>
            
        </table>
    </div>
    
    
    <div id="tabs">
        <ul>

        </ul>   
    </div>
</div>

<div id="frm_load">
    Wait...
</div>
<br />

<script type="text/javascript">
$(document).ready(function() {
    $('input').val('');
});

$(function() {
    $("#s_tgl_periode").datepicker({dateFormat:"yy-mm-dd"});
    $("#s_tgl_periode_to").datepicker({dateFormat:"yy-mm-dd"});
});

function get_periode(){
    var periode = $("#s_tgl_periode").val();
    return periode;
}
function get_periode_to(){
    var periode = $("#s_tgl_periode_to").val();
    return periode;
}
function check() {
    var el = document.getElementById("jns_laporan");
    var str = el.options[el.selectedIndex].value;
    if(str == "sumperblock" || str == "bjrttp" ) {
		hide();        
    }else {
     	show();   
    }

}
function hide(){
    document.getElementById('periode_to').style.visibility='hidden';
}
function show(){
    document.getElementById('periode_to').style.visibility='visible';
}
</script>

<script type="text/javascript">
$(document).ready(function(){
    $("#i_kontraktor").hide();
    $("#i_afd").hide(); 
    $("#jns_laporan").change(function() {
        var jenis = $(this).val();
        if (jenis =='tbglampbatpanen' || jenis=='tbgbatpanen') {
              /*$("#i_jabatan").empty();
              var cDept = $("#i_dept").val();
              if (cDept==null) {  cDept="-"; }
              $.post(url+'m_employee/LoadChain/'+$("#i_pangkat").val()+'/'+cDept+'/', 
              $("#i_pangkat").val(),
                function(datapost) 
                { 
                    $("#i_jabatan").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
                    for (var i=0; i<datapost.length; i++)
                    {
                        $("#i_jabatan").get(0).add(new Option(
                        datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                    }
                    $("#i_jabatan").attr('style','display:inline; width:230px;');
                        
                },"json"); */
            $("#i_kontraktor").show();
            $("#i_afd").hide(); 
        }else if(jenis =='dttbgbatpanen') {
            $("#i_afd").show();
            $("#i_kontraktor").hide();    
        }else{
            $("#i_kontraktor").hide();
            $("#i_afd").hide();
        }
        
        
    });   
})
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
    });
});
</script>

<script type="text/javascript">
var url;
var data_content;

function generate_lhm_nab(){
    //$("#frm_load").dialog('open');
    $.ajax({
            type:           'post',
            cache:          false,
            success: function(msg){
                data_content = msg;
                var format = trim($('#fr_laporan').val());
                var jenis = trim($('#jns_laporan').val());
                if(jenis.toLowerCase()=='pnperblock'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_lhm_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='pdf'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_pdf_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    }   
				}else if(jenis.toLowerCase()=='sumperblock'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_sum_lhm_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='pdf'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_sum_pdf_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_sum_xls_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    }
				}else if(jenis.toLowerCase()=='monitortonase'){
					if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_monitor_tonase/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_monitor_tonase_xls/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    }                    
                }else if (jenis.toLowerCase()=='tbs'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_lhm_produksi_tbs/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_produksi_tbs/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();         
                    }      
                }else if (jenis.toLowerCase()=='snd'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_lhm_sounding/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_sounding/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();         
                    }      
                }else if (jenis.toLowerCase()=='bjr'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_lhm_bjr/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_bjr/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();         
                    }
                    
                }else if (jenis.toLowerCase()=='jjgakt'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/get_jjg_angkut/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/get_xls_jjg_angkut/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();         
                    }
                    
                }else if (jenis.toLowerCase()=='tbg'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_lhm_tbg/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_tbg/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();         
                    }
                    
                }else if (jenis.toLowerCase()=='ademdpc'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_dispatch/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='cpoin'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_produksi/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                }else if (jenis.toLowerCase()=='nabnul'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_nab_null/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                }else if (jenis.toLowerCase()=='pkin'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_pkin/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbsin'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_tbsin/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbsout'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_tbsout/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbsplasma'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_tbsplasma/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbsluar'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_tbsluar/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbsaff'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_adem_tbsafiliasi/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='nabdt'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_nab/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='tbgdt'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_tbg/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
		  }else if (jenis.toLowerCase()=='tbgdtpksluar'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_tbg_pks_luar/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                }else if (jenis.toLowerCase()=='tbgdtkebun'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_tbg_kebun/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                        
                }else if (jenis.toLowerCase()=='tbgdtluar'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_tbgluar/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                }else if (jenis.toLowerCase()=='tbgkebun'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_tbgkebun/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='nabdist'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/get_nabdist/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if (jenis.toLowerCase()=='scrap'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/get_scrap/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                }else if (jenis.toLowerCase()=='bjrttp'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/export_bjrttp/'+$("#s_tgl_periode").val();
                        addTab();    
                    }
                    
                }else if(jenis.toLowerCase()=='tbgbatpanen'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_htm_tbgbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val();
                        addTab();    
                    }else if(format.toLowerCase()=='pdf'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_pdf_tbgbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val();
                        addTab();             
                    }
                }else if(jenis.toLowerCase()=='tbglampbatpanen'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_htm_tbglampbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val();
                        addTab();    
                    }else if(format.toLowerCase()=='pdf'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_pdf_tbglampbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val();
                        addTab();             
                    }
                }else if(jenis.toLowerCase()=='dttbgbatpanen'){
                    if(format.toLowerCase()=='xls'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_xls_dttbgbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val()+'/'+$("#i_afd").val();
                        addTab();    
                    }else if(format.toLowerCase()=='pdf'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_pdf_dttbgbatpanen/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+$("#i_kontraktor").val()+'/'+$("#i_afd").val();
                        addTab();             
                    }
                }else if(jenis.toLowerCase()=='titipolah'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_titip_olah/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
				}else if (jenis.toLowerCase()=='restan'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_restan/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
                    
                }else if(jenis.toLowerCase()=='titipolahsmi'){
                    if(format.toLowerCase()=='html'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_titip_olah_smi/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }
		  }else if (jenis.toLowerCase()=='afkir'){
                    if(format.toLowerCase()=='csv'){       
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_afkir/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();    
                    }else if(format.toLowerCase()=='xls'){   
                        url= "<?= base_url().'index.php/' ?>"+'s_analisa_panen/generate_afkir_xls/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val();
                        addTab();             
                    } 
                    
                }else{
                    alert("Unknown Report !!");
                    $("#frm_load").dialog('close');
                }
                          
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(xhr.status);
                alert(thrownError);
            }       
    });
     
   // urls = url+'s_analisa_panen/generate_lhm_nab/'+$("#s_tgl_periode").val() , $('#frame').attr('src',urls);    
}  
</script>

<script language="JavaScript" type="text/javascript"> 
var $tab_title_input = $('#tab_title'), $tab_content_input = $('#tab_content');
var tab_counter = 1;
  
$(function() {
        // tabs init with a custom tab template and an "add" callback filling in the content
    var $tabs = $('#tabs').tabs({
        tabTemplate: '<li><a href="#{href}">#{label}</a> <span class="ui-icon ui-icon-close">Remove Tab</span></li>',
        add: function(event, ui) {
            var tab_content = $tab_content_input.val() || data_content;
            var urls=url; 
            $(ui.tab.hash).append('<iframe id="rptFrame" frameborder="no" width="100%" height="400px" src="'+urls+'" frameBorder="no"></iframe>');
            
            var iframe = $("#rptFrame");
            iframe.load( function() {
                alert($("#rptFrame").contents().val());
                //$("#frm_load").dialog('close');
            });
            //$(ui.panel).append(data_content);
        }
    });
    
    // close icon: removing the tab on click
    // note: closable tabs gonna be an option in the future - see http://dev.jqueryui.com/ticket/3924
    var $tabs = $('#tabs').tabs();
    $('#tabs span.ui-icon-close').live('click', function() {
        var index = $('li',$tabs).index($(this).parent());
        $tabs.tabs('remove', index);
    });

});

function addTab(){
    var $tabs = $('#tabs').tabs();
    var tab_title = $tab_title_input.val() || 'Panen Periode ('+get_periode()+') - '+$('#jns_laporan').val()+'';
    //$tabs.tabs({ panelTemplate: '<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>' });
    $tabs.tabs('add', '#tabs-'+tab_counter, tab_title);
     
    tab_counter++;        
}

</script>

<script type="text/javascript">
function trim (str) {
    str = str.replace(/^\s+/, '');
    for (var i = str.length - 1; i >= 0; i--) {
        if (/\S/.test(str.charAt(i))) {
            str = str.substring(0, i + 1);
            break;
        }
    }
    return str;
}

function test_pg(){
    $.ajax({
            type:           'post',
            cache:          false,
            url:            "<?= base_url().'index.php/' ?>"+'s_analisa_panen/get_adem_sales',
            success: function(msg){
                var obj = jQuery.parseJSON(msg);    
                if(obj.error===true){
                    alert(obj.status)
                }else{
                    alert(obj.status)
                    reloadGrid();
                }    
                
            },
            error:function (xhr, ajaxOptions, thrownError){
                    alert(xhr.status);
                    alert(thrownError);
            }    
           });
}
</script>

<!--<div id='main_form'>
<img src="<?php //echo base_url()."$graph"; ?>"/>
</div>--> 


