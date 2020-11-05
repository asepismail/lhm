<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';
var company = "<?=$company_code ?>";
/* ############## grid PPJ ################ */
/*grid*/

var jGrid_monpengajuan = null;
var colNamesT_monpengajuan = new Array();
var colModelT_monpengajuan = new Array();

colNamesT_monpengajuan.push('No');
colModelT_monpengajuan.push({name:'PROJECT_PROP_ID',index:'PROJECT_PROP_ID', hidden:true, width: 80, align:'center'});

colNamesT_monpengajuan.push('No PPJ');
colModelT_monpengajuan.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:false, width: 100, align:'center'});
	
colNamesT_monpengajuan.push('Tgl Pengajuan');
colModelT_monpengajuan.push({name:'PROJECT_PROPNUM_DATE',index:'PROJECT_PROPNUM_DATE', editable: false, 
						 hidden:false, width: 100, align:'center'});

colNamesT_monpengajuan.push('Pelaksana');
colModelT_monpengajuan.push({name:'PROJECT_PROPNUM_PELAKSANA',index:'PROJECT_PROPNUM_PELAKSANA', editable: false, 
						 hidden:true, width: 90, align:'center'});

colNamesT_monpengajuan.push('No PJ.');
colModelT_monpengajuan.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:true, width: 90, align:'left'});

colNamesT_monpengajuan.push('Subtype');
colModelT_monpengajuan.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:false, width: 130, align:'center'});

colNamesT_monpengajuan.push('Lokasi');
colModelT_monpengajuan.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_monpengajuan.push('Aktivitas');
colModelT_monpengajuan.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:true, width: 50, align:'center'});

colNamesT_monpengajuan.push('Qty');
colModelT_monpengajuan.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 2}, hidden:false, width: 50, align:'center'});

colNamesT_monpengajuan.push('Satuan');
colModelT_monpengajuan.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:false, width: 50, align:'center'});

colNamesT_monpengajuan.push('Harga Satuan');
colModelT_monpengajuan.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_monpengajuan.push('Total');
colModelT_monpengajuan.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_monpengajuan.push('Approval Kebun');
colModelT_monpengajuan.push({name:'ISAPPR_LVL0',index:'ISAPPR_LVL0', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : false}, hidden:false, width: 105, align:'center'});

colNamesT_monpengajuan.push('Approval Dept Head');
colModelT_monpengajuan.push({name:'ISAPPR_LVL1',index:'ISAPPR_LVL1', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : false}, hidden:false, width: 105, align:'center'});

colNamesT_monpengajuan.push('Approval Direksi');
colModelT_monpengajuan.push({name:'ISAPPR_LVL2',index:'ISAPPR_LVL2', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 		formatter: "checkbox", formatoptions: {disabled : false},hidden:false, width: 110, align:'center'});

colNamesT_monpengajuan.push('Revisi');
colModelT_monpengajuan.push({name:'ISREVISED',index:'ISREVISED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : false}, hidden:true, width: 60, align:'center'});

colNamesT_monpengajuan.push('Close Pengajuan');
colModelT_monpengajuan.push({name:'ISCLOSED',index:'ISCLOSED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : false}, hidden:true, width: 60, align:'center'});

colNamesT_monpengajuan.push('Status');
colModelT_monpengajuan.push({name:'PROP_STATUS',index:'PROP_STATUS', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_monpengajuan.push('Company');
colModelT_monpengajuan.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 80, align:'center'});

colNamesT_monpengajuan.push('Action');
colModelT_monpengajuan.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_monpengajuan = function(){
    jGrid_monpengajuan = jQuery("#list_monpengajuan").jqGrid({
        url:url+'pms_c_monitoring/read_ppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_monpengajuan , colModel: colModelT_monpengajuan ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Monitoring Pengajuan Project <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 180, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_monpengajuan").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_monpengajuan").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_monpengajuan'), sortname: colModelT_monpengajuan[2].name
		});
		jGrid_monpengajuan.navGrid('#pager_monpengajuan',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_monpengajuan").ready(loadView_monpengajuan);
/* ############## end grid PPJ ############ */
</script>


<div style="margin-top:-15px;"><strong>Monitoring Pengajuan Project Baru</strong></div>

<?
	if($company_code=="PAG"){
?>
<div id="fcompany">
<span>Filter berdasarkan Perusahaan : <? if(isset($company)) { echo $company; } ?></span>
</div>
<br/>
<?
	}
?>


<table id="list_monpengajuan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
            </table>
            <div id="pager_monpengajuan" class="scroll"></div>