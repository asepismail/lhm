<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<div id="import_form" class="panes">
    <form name="form" action="" method="POST" enctype="multipart/form-data">
            <table  width="100%" border="0" id="input_table">
                <tr>
                    <td class="labelcell">Pilih File</td><td>:</td>
                    <td class="fieldcell" colspan="4">
                        <?php //if (isset($error)) echo $error;?>
                        <?php //echo form_open_multipart('',array('id'=>'uploadForm'));?> 
                        <input type="file" name="fileToImport" size="20" id='fileToImport' />                
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

<div id="frm_load">
    <img id="loading" src="<?= $template_path ?>themes/base/images/loading.gif" style="display:none;"> Please Wait...
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
    
    $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
    });
    
})

</script>

<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>";  
</script>

<script type="text/javascript">
    function ajaxFileUpload(){
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
                    url:url+'c_importdata/do_import/',
                    secureuri:false,
                    fileElementId:'fileToImport',
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
          
        return false;
    }
</script>



