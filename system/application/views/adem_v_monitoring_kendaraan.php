<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br>
<br>
<div id='main_form'>
	<? if(isset($periode)) {  echo $periode;  } ?>
    <br />
    <br />
    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="list_monitoringvh" class="scroll"></table> 
            <div id="pager_monitoringvh" class="scroll" style="text-align:center;"></div>
        </div>
    </div>

</div> 

<div id="frm_load">
    <img id="loading" src="<?= $template_path ?>themes/base/images/loading.gif" style="display:none;"> Please Wait...
</div>

<div id="search_form"></div> 
</body>

<script type="text/javascript">
jQuery(document).ready(function(){
    
	$("#bulan").change(function() {
	  gridReload();
	});
	
	$("#tahun").change(function() {
	  gridReload();
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
            jQuery("#list_monitoringvh").smartSearchPanel('#search_form', {dialog:{width: 530}},'adem_c_monitoring_kendaraan/search_data');
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
</script>


<script type="text/javascript">

var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesN = new Array();
var colModelN = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   

colNamesN.push('no');
colModelN.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 5, align:'center'});

colNamesN.push('Kode Kendaraan');
colModelN.push({name:'KODE_KENDARAAN',index:'KODE_KENDARAAN', editable: false, hidden:false, width: 65, align:'center'});

colNamesN.push('Periode');
colModelN.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: false, hidden:false, width: 55, align:'center'});

colNamesN.push('Tipe Lokasi');
colModelN.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false, hidden:false, width: 55, align:'center'});

colNamesN.push('Kode Lokasi');
colModelN.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, hidden:false, width: 75, align:'center'});

colNamesN.push('');
colModelN.push({name:'act',index:'act', editable: false, hidden:true, width: 75, align:'left'});

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var sIDgrid; 

var loadView = function()
        {
        jGrid = jQuery("#list_monitoringvh").jqGrid(
            {
                url:url+'adem_c_monitoring_kendaraan/LoadData/'+ jQuery("#tahun").val() + jQuery("#bulan").val(),
                mtype : "POST",
                datatype: "json",
                colNames: colNamesN ,
                colModel: colModelN,
                sortname: colModelN[1].name,
                pager:jQuery("#pager_monitoringvh"),
                rowNum: 20,
                rownumbers: true,
                height: 300,
                width: 560,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){}
                 
            });
            jGrid.navGrid('#pager_monitoringvh',{edit:false,del:false,add:false, search: false, refresh: true});           
         }
         

jQuery("#list_monitoringvh").ready(loadView);
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
	 var periode = jQuery("#tahun").val() + jQuery("#bulan").val()
     jQuery("#list_monitoringvh").setGridParam({url:url+'adem_c_monitoring_kendaraan/LoadData/'+periode}).trigger("reloadGrid");    
} 

</script>


