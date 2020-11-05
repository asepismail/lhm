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
                <td class="fieldcell">-</td>
                <td class="fieldcell"><input type="text" id='s_tgl_periode_to' ></td> 
            </tr>
            <tr>
                <td class="labelcell">Jenis Proses</td><td>:</td>
                <td class="fieldcell">
                    <select name="proses_type" class="select" id="proses_type">
                    <option value=''> -- pilih -- </option>
                    <option value='gen'>Generate</option>
                    <option value='cls'>Close</option>
                    <option value='sync'>Synchronize</option>
                    </select>
                </td>
            </tr>
            <tr><td>
                <div id="btn_section">
                     <button class="testBtn" type="submit" id="cl_bjr" onclick="close_bjr()">Generate</button>&nbsp; 
                </div>
                </td>
            </tr>
            
        </table>
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

function close_bjr(){
    var jns_transaksi = $('#proses_type').val();
    if (jns_transaksi != ''){
        $("#frm_load").dialog('open');
        $.ajax({
                type:'post',
                cache:false,
                url:"<?= base_url().'index.php/' ?>"+'s_close_bjr/close_bjr/'+$("#s_tgl_periode").val()+'/'+$("#s_tgl_periode_to").val()+'/'+jns_transaksi,
                success:function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
                    alert(obj.status);
                    $("#frm_load").dialog('close');
                    }else{
                        alert(obj.status);
                        $("#frm_load").dialog('close'); 
                    }
                }
        });    
    }else{
        alert("Harap pilih salah satu jenis transaksi...");
    }
    
     
   // urls = url+'s_analisa_panen/generate_lhm_nab/'+$("#s_tgl_periode").val() , $('#frame').attr('src',urls);    
}  
</script>