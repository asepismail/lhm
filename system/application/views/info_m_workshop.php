<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
$(function(){
    $("#ajax_display").ajaxStart(function(){
            $('#htmlExampleTarget').hide();             
            $(this).html("<img alt='' src='themes/wait.gif' ><br >Waiting ...");
        });
        
    $("#ajax_display").ajaxSuccess(function(){
           $(this).html('');
     });
    $("#ajax_display").ajaxError(function(url){
           alert('jQuery ajax is error ');
     });
});
//###################### END MENU FORM FUNCTION #######################
</script>
<script type="text/javascript">

var grid_pts = null;
var colNamesT_pts = new Array(); //definisi colNames untuk jGrid
var colModelT_pts = new Array(); //definisi colModel untuk jGrid

//------------- definisi colModel dan colNames -------------	
colNamesT_pts.push('id');
colModelT_pts.push({name:'no_va',index:'no_va', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('Kode');
colModelT_pts.push({name:'WORKSHOPCODE',index:'WORKSHOPCODE', 
editable: true,hidden:false, width: 50, align:'left'});
		
colNamesT_pts.push('Deskripsi');
colModelT_pts.push({name:'DESCRIPTION',index:'DESCRIPTION', 
editable: true,hidden:false, width: 200, align:'left'});

colNamesT_pts.push('Pengesahan');
colModelT_pts.push({name:'Approved',index:'Approved', 
editable: true,hidden:false, width: 50, align:'center'});

colNamesT_pts.push('Disahkan Oleh');
colModelT_pts.push({name:'Approved_By',index:'Approved_By', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('Tanggal Pengesahan');
colModelT_pts.push({name:'Approved_Date',index:'Approved_Date', 
editable: true,hidden:true, width: 50, align:'center'});
//------------- end definisi colModel dan colNames -------------	
	
	var loadView_pb = function()
        {
            jgrid_pb = jQuery("#list").jqGrid(
            {
				url:'m_workshop/LoadData/',  //loaddata untuk jGrid ->dari controller ->ke model
				datatype: 'json', 
				mtype: 'POST', 
				colNames:colNamesT_pts,
				colModel:colModelT_pts,
				
				pager: jQuery('#pager'), 
				rownumbers: true, 
              	rowNum: 400,
				width:800,
                height:300, 
				sortorder: "asc",
				forceFit : true,
				rowList:[10,20,30], 
				sortname: colNamesT_pts[1], 
				sortorder: "desc", 
				viewrecords: true, 
				//imgpath: 'themes/basic/images',
				caption: 'List Workshop',
				editurl:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/LoadData/'	
            });
            jgrid_pb.navGrid('#pager',{edit:false,del:false,add:false, search: false, refresh: true});
			//jgrid_pb.filterToolbar({stringResult: true,searchOnEnter : false});
			jgrid_pb.navButtonAdd('#pager',{
			   caption:"Export ke Excell", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 

			   window.location = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/create_excel/';
			   
			   },
			   position:"left",
			});

            $("#alertmod").remove();//FIXME		 
        }
        jQuery("#list").ready(loadView_pb);
		
function init_mac()
{
	$("#i_wrkscode").val("");
	$("#i_desc").val("");
	
    $("#i_approv").val("");
    $("#i_approv_by").val("");
    $("#i_approv_date").val("");
    
	$("#wrks_form").dialog('close');
	$("#form_mode").val('');	
}
$(function() 
{
	$("#wrks_form").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 450,
		width: 550,
		modal: true,
		title: "Tambah Workshop",
		resizable: false,
		moveable: true,
		buttons: {
			'Tutup	': function() 
			{
				init_mac();		
			}
		} 
	}); 
    $("#i_date").datepicker({dateFormat:"yy-mm-dd"});
});

function gridReload()
{ 
    jQuery("#list").setGridParam
    ({url:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/LoadData'}).trigger("reloadGrid");		
} 
		  
var timeoutHnd; 
function doSearch(ev)
{ 
    if(timeoutHnd) 
	clearTimeout(timeoutHnd) 
	timeoutHnd = setTimeout(srcReload,500) 
} 

function srcReload()
{ 
    var code = jQuery("#search_nik").val(); 
    var desc = jQuery("#search_desc").val(); 

    if (code == ""){
    code = "-";
} 

    jQuery("#list").setGridParam
    ({url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/SearchData/"+code+"/"+desc}).trigger("reloadGrid");		
} 
</script>

<script type="text/javascript">
//###################### START BUTTON FUNCTION ###########################
function delData()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
      var data = $("#list").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Data Mesin : " + data.WORKSHOPCODE + ":" + data.DESCRIPTION + "?" )
        if (answer)
        {
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/DeleteData/'+data.WORKSHOPCODE, postdata,function(message,status) 
            { 
              if(status !== 'success') 
              { 
                 alert('data untuk tanggal ini sudah terisi.'); 
              } 
              else 
              {             
                alert('data berhasil terhapus.')
                gridReload();
              };  
            } );
        }
    }
    else { alert("Please Select Row to delete!"); }
}
function TambahData()
{                           
    init_mac();
    $("#btnApproval").attr('disabled','disabled') ;
    $("#i_wrkscode").removeAttr('disabled'); 
    $("#wrks_form").dialog('open');
    $("#form_mode").val("POST");
}
function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();
    postdata['WORKSHOPCODE'] = $("#i_wrkscode").val() ; 
    postdata['DESCRIPTION'] = $("#i_desc").val() ; 
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    
    if (mode == "GET")
    {
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/EditData/', postdata,function(message,status) { 
              if(status !== 'success') 
            { 
               alert('data untuk kendaraan ini sudah terisi.'); 
            } 
            else 
            { 
                $.ajax({
                            type:"POST",
                            url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/delete_approve/"+$("#i_wrkscode").val(),
                            data:false,
                            success:false
                        });
                gridReload();
                alert('data berhasil terupdate.')
                $("#wrks_app").dialog('close');
                $("#wrks_form").dialog('close');    
            };
          } );
    } 
    else if (mode == "POST") 
    {    
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/AddNew/', postdata,function(message,status) 
        { 
            var status = new String(status);  
            if(status.replace(/\s/g,"") != "") 
            { 
                if (message!="")
                {
                   alert(message); 
                } 
                gridReload();      
            } 
            else 
            { 
                gridReload();
                alert('data berhasil tersimpan.')
                $("#wrks_app").dialog('close');
                $("#wrks_form").dialog('close');        
            };  
        } );     
    }
  }
function Edit()
{
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (ids!=null )
    {
        init_mac();
        $("#btnApproval").attr('disabled','disabled') ;
        $("#i_wrkscode").val(data.WORKSHOPCODE);         
        $("#i_desc").val(data.DESCRIPTION);
        
        $("#i_wrkscode").attr('disabled','disabled');
        $("#btnApproval").removeAttr('disabled') ;
        if(1==data.Approved)
        {
            $("#i_approv").val("APPROVED");
        }
        $("#i_approv_by").val(data.Approved_By);
        $("#i_approv_date").val(data.Approved_Date);
        
        $("#wrks_form").dialog('open');
        $("#form_mode").val("GET");
    }
    else
    {
        alert("harap pilih data untuk di edit");
    }                
} 
//###################### END BUTTON FUNCTION ########################### 
</script>

<script type="text/javascript">
//########################### START APPROVAL FUNCTION ############################# 
function approval()
{
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 

    if (data.Approved=="1")
    {
        alert("Data sudah di approve");
    }
      else if ("0"==data.Approved || ''==data.Approved || null==data.Approved)
    {
        $("#i_approved").attr('disabled','true');
        $("#wrks_app").dialog('open');
    }
}

jQuery(document).ready(function(){
    $("#wrks_app").dialog({
                bgiframe: true,
                autoOpen: false,
                height: 220,
                width: 290,
                modal: true,
                title: "Pengesahan",
                resizable: false,
                moveable: true,
                buttons: {
                    'Tutup    ': function() 
                    {
                        $("#wrks_app").dialog('close');            
                    },
                    'Simpan ': function()
                    {
                        if($("#i_ck_approve").attr('checked')==true)
                        {
                            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/cek_approve/','' ,function(message,status) 
                            { 
                                  if(message>0)
                                {    
                                    var ids = jQuery("#list").getGridParam('selrow'); 
                                    var data = $("#list").getRowData(ids);
                                    
                                    $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/update_approve/'+data.WORKSHOPCODE,'' ,function(message,status) 
                                    {
                                        alert("update approve berhasil!!!");
                                        $("#i_ck_approve").removeAttr('checked');
                                        $("#wrks_app").dialog('close');
                                        $("#wrks_form").dialog('close');
                                        gridReload(); 
                                    });
                                }
                                else
                                {
                                    alert("anda tidak memiliki hak pengesahan");
                                    $("#i_ck_approve").attr('checked','false');
                                    $("#i_ck_approve").removeAttr('checked');
                                }
                            });
                        }
                        
                    }
                } 
            });
});
//########################### END APPROVAL FUNCTION ############################# 
</script>
<div id"gridSearch">  
<table border="0" class="teks_" cellpadding="2" cellspacing="4">
<tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
<tr>
    <td>Kode</td>
    <td>:</td>
    <td>
    <input type="text" class="input" id="search_nik" onkeydown="doSearch(arguments[0]||event)" />
    </td>
    <td>Deskripsi</td>
    <td>:</td>
    <td>
    <input type="text" class="input" id="search_desc" onkeydown="doSearch(arguments[0]||event)" />
    </td>
</tr>
</table>   
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table> 
    <div id="pager" class="scroll" style="text-align:center;"></div>
</div>
<br>
<div id="save" class="scroll" style="float:left;">
<input type="button"  id="add" value="Tambah" onclick="TambahData()">
<input type="button"  id="edit" value="Ubah" onclick="Edit()">
<input type="button"  id="delete" value="Hapus" onclick="delData()">
</div>


<div id="wrks_form">
<table border="0" width="100%" border="0" class="teks_">
	<tr>
		<td>
			<tr>
				<td align="left" width="100">Kode Workshop</td>
				<td>:</td>
				<td><input type="text" id="i_wrkscode" class="input" tabindex="1"/></td>
			</tr>
			
			<tr>
				<td align="left">Deskripsi</td>
				<td>:</td>
				<td><textarea rows="5" cols="25" id="i_desc" style="height:50px; width:200px;" class="input" tabindex="2">
				</textarea> 
				</td>
			</tr>
            
            <tr>
                    <td align="left">Pengesahan</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_approv" class="input_disable" disabled="true" /></td>
                </tr>
                <tr>
                    <td align="left">Disahkan Oleh</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_approv_by" class="input_disable" disabled="true" /></td>
                </tr>
                <tr>
                    <td align="left">Tanggal Pengesahan</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_approv_date" class="input_disable" disabled="true" /></td>
                </tr>
		</td>
	
		<td colspan="5"><input type="hidden" id="form_mode">
		    <input type="button" id="submitdata" value="Simpan" onclick="submit()" tabindex="3"> 
            <input type="button" id="btnApproval" value="Pengesahan" onclick="approval()" tabindex="4">
        </td>
	</tr>
</table>
</div> 

<div id="wrks_app">
 <table width="100%" class="teks_">
        <tr>
         <tr>
         <td align="left" width="125">Approve</td>
         <td align="left">:</td>
         <td><input name="i_ck_approve" type="checkbox" id="i_ck_approve"/></td>
         </tr>
        </tr>
    </table>   
</div>

