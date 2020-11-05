<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
$(document).ready(function() {
	
});	

/* ###### end dialog ##### */
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
var url = "<?= base_url().'index.php/pms/' ?>";   

/*grid*/

var jGrid_ppj_revisi = null;
var colNamesT_ppj_revisi = new Array();
var colModelT_ppj_revisi = new Array();
                                                           
colNamesT_ppj_revisi.push('ID');
colModelT_ppj_revisi.push({name:'PPID',index:'PPID', hidden:true, width: 80, align:'center'});

colNamesT_ppj_revisi.push('No Pengajuan');
colModelT_ppj_revisi.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', editable: false, hidden:false, width: 110, align:'center'});

colNamesT_ppj_revisi.push('Tgl Pengajuan');
colModelT_ppj_revisi.push({name:'PROJECT_PROPNUM_DATE',index:'PROJECT_PROPNUM_DATE', hidden:false, width: 90, align:'center'});

colNamesT_ppj_revisi.push('Pelaksana');
colModelT_ppj_revisi.push({name:'PROJECT_PROPNUM_PELAKSANA',index:'PROJECT_PROPNUM_PELAKSANA', hidden:false, width: 70, align:'center'});

colNamesT_ppj_revisi.push('Departemen');
colModelT_ppj_revisi.push({name:'PROJECT_DEPT',index:'PROJECT_DEPT', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_ppj_revisi.push('Tgl Target');
colModelT_ppj_revisi.push({name:'PROJECT_FINISH_TARGET',index:'PROJECT_FINISH_TARGET', editable: false, hidden:false, width: 100, align:'left'});

colNamesT_ppj_revisi.push('Selesai Pengajuan');
colModelT_ppj_revisi.push({name:'ISCOMPLETE',index:'ISCOMPLETE', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 120, align:'center'});

colNamesT_ppj_revisi.push('Persetujuan Dept');
colModelT_ppj_revisi.push({name:'ISAPPR_LVL1',index:'ISAPPR_LVL1', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 120, align:'center'});

colNamesT_ppj_revisi.push('Persetujuan Direksi');
colModelT_ppj_revisi.push({name:'ISAPPR_LVL2',index:'ISAPPR_LVL2', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 130, align:'center'});

colNamesT_ppj_revisi.push('Revisi');
colModelT_ppj_revisi.push({name:'ISREVISED',index:'ISREVISED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 40, align:'center'});

colNamesT_ppj_revisi.push('Closed');
colModelT_ppj_revisi.push({name:'ISCLOSED',index:'ISCLOSED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 60, align:'center'});

colNamesT_ppj_revisi.push('Perusahaan');
colModelT_ppj_revisi.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_ppj_revisi.push('');
colModelT_ppj_revisi.push({name:'act',index:'act', editable: false, hidden:false, width: 30, align:'center'}); 
     
var loadView_ppj_revisi = function(){
        jGrid_ppj_revisi = jQuery("#list_ppj_revisi").jqGrid({
            url:url+'pms_c_revisi/read_ppj_revisi/',
            mtype : "POST", datatype: "json",
            colNames: colNamesT_ppj_revisi , colModel: colModelT_ppj_revisi ,
            rownumbers:true, viewrecords: true, multiselect: false, 
            caption: "Revisi Pengajuan Project <?php echo $company_dest;?>", 
            rowNum:20, rowList:[10,20,30], multiple:true,
            height: 320, cellEdit: false,
            loadComplete: function(){ 
                var ids = jQuery("#list_ppj_revisi").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_ppj_revisi").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_ppj_revisi'), sortname: colModelT_ppj_revisi[0].name
		});
		jGrid_ppj_revisi.navGrid('#pager_ppj_revisi',{edit:false,add:false,del:false, search: false, refresh: true});           
        }
	jQuery("#list_ppj_revisi").ready(loadView_ppj_revisi);
</script>

<table id="list_ppj_revisi" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    <div id="pager_ppj_revisi" class="scroll"></div>