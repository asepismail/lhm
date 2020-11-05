<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<br />
<br />
<div id"gridSearch">  
    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
    <tr>
        <td>Cari Kode Asset</td>
        <td>:</td>
        <td>
        <input type="text" class="input" id="search_nik" onkeydown="doSearch(arguments[0]||event)" />
        </td>
    </tr>
    </table>   
</div>
<div id="import_form">
    <form name="form" action="" method="POST" enctype="multipart/form-data">
            <table width="100%" border="0" id="input_table" class="teks_" style="padding:7px;">
                <tr>
                    <td>Pilih File</td><td>:</td>
                    <td class="fieldcell" colspan="4">
                        <?php //if (isset($error)) echo $error;?>
                        <?php //echo form_open_multipart('',array('id'=>'uploadForm'));?> 
                        <input type="file"  class="inputfile" name="fileToImport" size="20" id='fileToImport' />                
                    </td> 
                </tr>
                <tr>
                    <td class="labelcell"></td>
                    <td></td>
                    <td colspan="4" class="fieldcell">
                <button class="button" id="buttonUpload" onClick="ajaxFileUpload();">Upload</button></td>
                </tr>
            </table>
    </form>
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table> 
    <div id="pager" class="scroll" style="text-align:center;"></div>
</div>
<div id="frm_load">
    <img id="loading" src="<?= $template_path ?>themes/base/images/loading.gif" style="display:none;"> 
    <span id="msg" style="text-align:justify"></span>
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
	
	$("#import_form").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 150,
        width: 350,
        modal: true
    });   
})

</script>

<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>";  
</script>

<script type="text/javascript">
	/* GRIDNYA */
	var grid_pts = null;
	var colNamesT_pts = new Array(); //definisi colNames untuk jGrid
	var colModelT_pts = new Array(); //definisi colModel untuk jGrid
	
	//------------- definisi colModel dan colNames -------------
	
	colNamesT_pts.push('no');
	colModelT_pts.push({name:'no',index:'no', 
	editable: true,hidden:true, width: 20, align:'center'});
		
	colNamesT_pts.push('transactid');
	colModelT_pts.push({name:'transactid',index:'transactid', 
	editable: false,hidden:true, width: 20, align:'center'});
	
	colNamesT_pts.push('Kode Asset');
	colModelT_pts.push({name:'kode',index:'kode', 
	editable: false,hidden:false, width: 80, align:'left'});
	
	colNamesT_pts.push('Biaya');
	colModelT_pts.push({name:'cost',index:'cost', 
	editable: false,hidden:false, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, width: 40, align:'right'});
	
	colNamesT_pts.push('Periode');
	colModelT_pts.push({name:'periode',index:'periode', 
	editable: false,hidden:false, width: 50, align:'center'});
	
	colNamesT_pts.push('Perusahaan');
	colModelT_pts.push({name:'company_code',index:'company_code', 
	editable: false,hidden:false, width: 40, align:'center'});
	
	var loadView_pb = function()
	{
		jgrid_pb = jQuery("#list").jqGrid(
		{
			url:'c_importdata/LoadData/',  //loaddata untuk jGrid ->dari controller ->ke model
			datatype: 'json',  mtype: 'POST', 
			colNames:colNamesT_pts, colModel:colModelT_pts,
			pager: jQuery('#pager'),  rownumbers: true, 
			rowNum: 40,  width:600,  height: 300,
			sortorder: "asc", forceFit : true, rowList:[10,20,30], 
			multiple:false, sortname: colNamesT_pts[1], 
			viewrecords: true, 
			caption: 'Import Data Biaya',
			editurl:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>c_importdata/LoadData/'	
		});
		jgrid_pb.navGrid('#pager',{edit:false,del:false,add:false, search: false, refresh: true});
		jgrid_pb.navButtonAdd('#pager',{
		   caption:"Excell", 
		   buttonicon:"ui-icon-add", 
		   onClickButton: function(){ 
		   window.location = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>c_importdata/create_excel/';
		   },
		   position:"left",
		});
		jgrid_pb.navButtonAdd('#pager',{
		   caption:"Hapus", 
		   buttonicon:"ui-icon-add", 
		   onClickButton: function(){ 
		 		hapusData()
			},
		   position:"left",
		});
		jgrid_pb.navButtonAdd('#pager',{
		   caption:"Import", 
		   buttonicon:"ui-icon-add", 
		   onClickButton: function(){ 
		 				 $("#import_form").dialog('open');
		   },
		   position:"left",
		});
		$("#alertmod").remove();//FIXME		 
	}
	jQuery("#list").ready(loadView_pb);
	
	/* END GRID */
	
	
	function hapusData() {
       var postdata = {}; 
	    var answer = confirm ("Hapus Seluruh Data ?" )
                if (answer)
                {
						 $("#loading")
						.ajaxStart(function(){
							$(this).show();
						})
						.ajaxComplete(function(){
							$(this).hide();
						});
						$("#frm_load").dialog('open');
						
					   $.ajax({
						 	type: 'POST',
						  	url: url+'c_importdata/doDeleteImport/',
						  	data: postdata,
						  	dataType: 'json',
							success: function (data, status)
							{
								if(typeof(data.error) != 'undefined')
								{
									if(data.error != '')
									{
										document.getElementById('msg').innerHTML= data.msg;
									} else {
										document.getElementById('msg').innerHTML= data.msg;
										jQuery("#list").setGridParam({url:'c_importdata/LoadData/'}).trigger("reloadGrid"); 
									}
								}
							},
							error: function (data, status, e)
							{
								document.getElementById('msg').innerHTML= data.msg;
							}
						});
				}
    }       
		
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
                        if(typeof(data.error) != 'undefined') {
							if(data.error != ''){
								document.getElementById('msg').innerHTML= data.msg;
							} else {
								document.getElementById('msg').innerHTML= data.msg;
								jQuery("#list").setGridParam({url:'c_importdata/LoadData/'}).trigger("reloadGrid"); 
							}
						}
                    },
                    error: function (data, status, e)
                    {
                       document.getElementById('msg').innerHTML= data.msg;
                    }
                }
            )    
          
        return false;
    }
</script>



