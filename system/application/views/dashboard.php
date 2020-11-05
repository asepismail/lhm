<? 
    $template_path = base_url().$this->config->item('template_path');  
?> 

    <input type="hidden" id="txt_dbPeriode">
    <div class="column_4" style="width:1200px">
    <table width="100%" border="0" align="center">
      <tr>
        <td><?php
                    echo $graph_produksi_all ;     
                ?></td>
        <td><?php
                    echo $graph_produksi_forday ;     
                ?> </td>
      </tr>
    </table>                 
    </div>
    <br>
    <br>
    <div class="column_3">
        <div class="portlet">
            <div class="portlet-header">Daily Production</div>
            <div class="portlet-content">
                <?php
                    echo $graph_produksi ;     
                ?>   
            </div>
        </div>
    </div>
    
     
	
<script type="text/javascript">
    $(function() {
        /*$( ".columns" ).sortable({
            connectWith: ".columns"
        });*/

        $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
            .find( ".portlet-header" )
                .addClass( "ui-widget-header ui-corner-all" )    
                .prepend( "<span class='ui-icon ui-icon-refresh'></span>")
                .prepend( "<span class='ui-icon ui-icon-wrench'></span>")
                .prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
                .end()
            .find( ".portlet-content" );

        $( ".portlet-header .ui-icon-minusthick" ).click(function() {
            $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
            $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
        });
        
        $( ".portlet-header .ui-icon-refresh" ).click(function() {
            alert('Under Construction!!!');    
        });
        
        $( ".portlet-header .ui-icon-wrench" ).click(function() {
            alert('Under Construction!!!');
        });

        $( ".portlet-header .ui-icon-close" ).click(function() {
            // Remove portlet div
            $("#"+portlet).remove(); 
        });
        
        $( ".column" ).disableSelection();
    });
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
    
    $("#txt_dbPeriode").datepicker({dateFormat:"yy-mm-dd"});
});
</script>


