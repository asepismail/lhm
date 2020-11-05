<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
//###################### START MENU FORM FUNCTION ####################### 
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

colNamesT_pts.push('Blok Tanam');
colModelT_pts.push({name:'FIELDCODE',index:'FIELDCODE', editable: true,hidden:false, width: 90, align:'center'});

colNamesT_pts.push('Blok');
colModelT_pts.push({name:'BLOCKID',index:'BLOCKID', editable: true,hidden:false, width: 50, align:'center'});

colNamesT_pts.push('AFD');
colModelT_pts.push({name:'ESTATECODE',index:'ESTATECODE', editable: true,hidden:false, width: 50, align:'center'});
        
colNamesT_pts.push('Deskripsi');
colModelT_pts.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: true,hidden:false, width: 200, align:'right'});

colNamesT_pts.push('Planted');
colModelT_pts.push({name:'HECTPLANTED',index:'HECTPLANTED', editable: true,hidden:false, width: 100, align:'right'});

colNamesT_pts.push('Plantable');
colModelT_pts.push({name:'HECTPLANTABLE',index:'HECTPLANTABLE', editable: true,hidden:false, width: 100, align:'left'});

colNamesT_pts.push('STATUS');
colModelT_pts.push({name:'CROPSSTATUS',index:'CROPSSTATUS', editable: true,hidden:false, width: 100, align:'left'});

colNamesT_pts.push('Jumlah Pokok');
colModelT_pts.push({name:'NUMPLANTATION',index:'NUMPLANTATION', editable: true,hidden:false, width: 100, align:'right'});

colNamesT_pts.push('Tahun Tanam');
colModelT_pts.push({name:'YEARREPLANT',index:'YEARREPLANT', editable: true,hidden:false, width: 50, align:'center'});

/* ------------- end definisi colModel dan colNames ------------- */
      
var loadView_pb = function(){
jgrid_pb = jQuery("#list").jqGrid({
		url:'m_bloktanam/LoadData/',  //loaddata untuk jGrid ->dari controller ->ke model
		datatype: 'json', mtype: 'POST', colNames:colNamesT_pts, colModel:colModelT_pts,
		pager: jQuery('#pageri'), rownumbers: true, rowNum: 40, width:800, height:300,
		sortorder: "asc", forceFit : true, rowList:[10,20,30], sortname: colNamesT_pts[1], 
		sortorder: "desc", viewrecords: true, caption: 'List Blok Tanam',
		editurl:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/LoadData/'    
	});
	jgrid_pb.navGrid('#pageri',{edit:false,del:false,add:false, search: false, refresh: true});
	jgrid_pb.navButtonAdd('#pageri',{
		caption:"Export ke Excell", 
		buttonicon:"ui-icon-add", 
		onClickButton: function(){ 
			window.location = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_bloktanam/create_excel/';
		}, position:"left",
	});
	$("#alertmod").remove();//FIXME         
}
jQuery("#list").ready(loadView_pb);
    
function init_btanam(){
    $("#i_btnm_code").val("");
    $("#i_btnm_codeconv").val("");
    $("#i_btnm_bid").val("");
    $("#i_btnm_estatecode").val("");
    $("#i_btnm_desc").val("");
    $("#i_btnm_hplanted").val("");
    $("#i_btnm_hplantable").val("");
    $("#i_btnm_cropsstatus").val("");
    $("#i_btnm_numplantation").val("");
    $("#i_btnm_yearreplant").val("");
    $("#i_approv").val("");
    $("#i_approv_by").val("");
    $("#i_approv_date").val("");
    $("#btnm_form").dialog('close');
    $("#form_mode").val('');    
}

$(function(){
    $("#btnm_form").dialog({
        bgiframe: false, autoOpen: false, height: 450, width: 550,
        modal: true, title: "Tambah Blok Tanam",
        resizable: false,
        moveable: true,
        buttons: {
            'Tutup    ': function(){
                init_btanam();        
            }
        } 
    });
    $("#i_btnm_yearreplant").datepicker({dateFormat:"yy"}); 
});

$(function(){
    $("#sync_form").dialog({
        bgiframe: false, autoOpen: false, height: 300, width: 400,
        modal: true, title: "Sinkron Blok Tanam Adempiere",
        resizable: false,
        moveable: true,
        buttons: {
            'Tutup    ': function(){
				$("#sync_form").dialog('close');
            }
        } 
    });
});

function gridReload()
{ 
    jQuery("#list").setGridParam
    ({url:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_bloktanam/LoadData'}).trigger("reloadGrid");        
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
    ({url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/SearchData/"+code+"/"+desc}).trigger("reloadGrid");        
} 
         
</script>
<script>
function handleClick(myradio){
	if ($("#r_afd").is(':checked')){ 
		$("#r_block").attr('checked',false);  
		$("#r_all").attr('checked',false); 
		
		
		$("#data_afd").attr('disabled',false); 
		//$("#data_all").attr('disabled',true);  
		$("#data_block").attr('disabled',true); 
		
		$("#data_all").attr('checked',false);		
		$("#data_block").val("");
	}else if($("#r_block").is(':checked')){
		$("#r_afd").attr('checked',false);  
		$("#r_all").attr('checked',false);
		
		$("#data_block").attr('disabled',false); 
		$("#data_afd").attr('disabled',true);  
		
		$("#data_all").attr('checked',false);	
		$("#data_afd").val("");
	}else if ($("#r_all").is(':checked')){
		$("#r_afd").attr('checked',false);  
		$("#r_block").attr('checked',false); 
		
		$("#data_all").attr('checked',true);
		$("#data_afd").attr('disabled',true); 		
		$("#data_block").attr('disabled',true); 
		
		$("#data_afd").val("");
		$("#data_block").val("");
	}
}
</script>

<script type="text/javascript">
//###################### START BUTTON FUNCTION ###########################
function delData()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
      var data = $("#list").getRowData(ids) ;
    if( ids != null ){
        var answer = confirm ("Hapus Data Blok Tanam : " + data.FIELDCODE + ":" + data.DESCRIPTION + "?" )
        if (answer){
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/DelData/'+data.FIELDCODE, 
            postdata,
            function(message,status){ 
				if(status !== 'success'){ 
					  alert('data untuk tanggal ini sudah terisi.'); 
				} else {             
					  alert('data berhasil terhapus.')
					  gridReload();
				};  
            });
        }
    } else { 
		alert("Please Select Row to delete!"); 
	}
}

function TambahData()
{
    init_btanam();
    $("#btnm_form").dialog('open');
    $("#i_btnm_code").removeAttr('disabled');
    $("#btnApproval").attr('disabled','disabled') ;
    $("#form_mode").val("POST");
}

function syncData(){
    $("#r_afd").attr('checked',false);    
	$("#r_block").attr('checked',false);  
	$("#r_all").attr('checked',false); 
    $("#sync_form").dialog('open');
}

function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();
    postdata['FIELDCODE'] = $("#i_btnm_code").val() ; 
    postdata['FIELDCODECONV']=$("#i_btnm_codeconv").val();
    postdata['BLOCKID']=$("#i_btnm_bid").val();
    postdata['ESTATECODE']=$("#i_btnm_estatecode").val();
    postdata['DESCRIPTION']=$("#i_btnm_desc").val();
    postdata['HECTPLANTED']=$("#i_btnm_hplanted").val();
    postdata['HECTPLANTABLE']=$("#i_btnm_hplantable").val();
    postdata['CROPSSTATUS']=$("#i_btnm_cropsstatus").val();
    postdata['NUMPLANTATION']=$("#i_btnm_numplantation").val();
    postdata['YEARREPLANT']=$("#i_btnm_yearreplant").val();
   
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    if (mode == "GET")
    {
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/EditData/'+$("#i_btnm_code").val(), 
        postdata,
        function(message,status) { 
            if(status !== 'success') { 
               alert('data untuk blok tanam ini sudah terisi.'); 
            } else { 
                $.ajax({
					type:"POST",
					url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/delete_approve/"+$("#i_btnm_code").val(),
					data:false, success:false
                });
                gridReload();
                alert('data berhasil terupdate.');
                $("#btnm_form").dialog('close');    
            };
          } );
    } 
    else if (mode == "POST") 
    {
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/AddNew/'+$("#i_btnm_code").val(), postdata,function(message,status) 
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
                alert('data berhasil tersimpan.');  
                $("#btnm_form").dialog('close');   
            };  
        } );     
    }
}
function sinkron(){
	var postdata={};
	
	var $afd = $("#data_afd").val();
	var $block = $("#data_block").val();
	var $all = $("#data_all").is(':checked');
	var $check = true;
	if ($afd==''){
		$afd = null;
	}
	if ($block==''){
		$block = null;
	}
	
	if ($("#r_afd").is(':checked') == true && $afd==null){
		alert("AFD harus diisi");	
		$check = false;
	}
	
	if ($("#r_block").is(':checked') == true && $block==null){
		alert("Blok harus diisi");	
		$check = false;
	}
	
	if ($check == true){
	
		var answer = confirm ("Sinkron data ?" )
		if (answer){
			$.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_bloktanam/SyncData/'+$block+'/'+$afd+'/'+$all, 
			postdata,
			function(message,status){ 
				if(status !== 'success'){ 
					alert('data untuk tanggal ini sudah terisi.'); 
				}else{             
					alert(status)
					gridReload();
				};  
			});
		}
	}
}
function Edit()
{
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (ids!=null )
    {
        init_btanam();
        $("#i_btnm_code").val(data.FIELDCODE);
        $("#i_btnm_codeconv").val(data.FIELDCODECONV);
        $("#i_btnm_bid").val(data.BLOCKID);
        $("#i_btnm_estatecode").val(data.ESTATECODE);
        $("#i_btnm_desc").val(data.DESCRIPTION);
        $("#i_btnm_hplanted").val(data.HECTPLANTED);
        $("#i_btnm_hplantable").val(data.HECTPLANTABLE);
        $("#i_btnm_cropsstatus").val(data.CROPSSTATUS);
        $("#i_btnm_numplantation").val(data.NUMPLANTATION);
        $("#i_btnm_yearreplant").val(data.YEARREPLANT);
        
        $("#i_btnm_code").attr('disabled','disabled');
        $("#btnApproval").removeAttr('disabled') ;
        if(1==data.Approved)
        {
            $("#i_approv").val("APPROVED");
        }
        $("#i_approv_by").val(data.Approved_By);
        $("#i_approv_date").val(data.Approved_Date);
        
        $("#btnm_form").dialog('open');
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
        $("#btnm_app").dialog('open');
    }
}

jQuery(document).ready(function(){
    $("#btnm_app").dialog({
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
                        $("#btnm_app").dialog('close');            
                    },
                    'Simpan ': function()
                    {
                        if($("#i_ck_approve").attr('checked')==true)
                        {
                            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/cek_approve/','' ,function(message,status) 
                            { 
                                if(message>0)
                                {    
                                    var ids = jQuery("#list").getGridParam('selrow'); 
                                    var data = $("#list").getRowData(ids);
                                    
                                    $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_blocktanam/update_approve/'+data.FIELDCODE,
                                    '' ,
                                    function(message,status) 
                                    {
                                        alert("update approve berhasil!!!");
                                        $("#i_ck_approve").removeAttr('checked');
                                        $("#btnm_app").dialog('close');
                                        $("#btnm_form").dialog('close');
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
<script type="text/javascript">
$(document).ready(function() { 
   	$("#data_all").attr('checked',false);	
   	$("#data_afd").attr('disabled',true); 		
	$("#data_block").attr('disabled',true); 
		
	$("#data_afd").val("");
	$("#data_block").val("");
});
</script>
<div id"gridSearch">
    <table height="61" border="0" cellpadding="2" cellspacing="4" class="teks_">
        <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
        <tr>
            <td>Kode Tanam</td>
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
    <div id="pageri" class="scroll" style="text-align:center;"></div>
</div>
<br>
<div id="save" class="scroll" style="float:left;">
<input type="button"  id="add" value="Tambah" onclick="TambahData()">
<input type="button"  id="edit" value="Ubah" onclick="Edit()">
<input type="button"  id="delete" value="Hapus" onclick="delData()">
<input type="button"  id="sync" value="Sinkron Adempiere" onclick="syncData()">
</div>

<div id="sync_form">


	<table width="100%" class="teks_">
      <tr>
        <td width="25%"><input id="r_afd" name="myradio" type="radio" value="afd" onclick="handleClick(this)" />AFD</td>
        <td width="75%"><input name="data_afd" type="text" id="data_afd"/></td>
      </tr>
      <tr>
        <td width="25%"><input id="r_block" name="myradio" type="radio" value="block" onclick="handleClick()" />BLOCK</td>
        <td width="75%"><input name="data_block" type="text" id="data_block"/></td>
      </tr>
      <tr>
        <td width="25%"><input id="r_all" name="myradio" type="radio" value="all" onclick="handleClick()"/>ALL</td>
        <td width="75%"><input name="data_all" type="checkbox" id="data_all" disabled="disabled"/></td>
      </tr>
      <tr>
      	<td colspan="2">
            <input type="button" id="submitdata" value="Sinkron" onclick="sinkron()" tabindex="11">
        </td>
      </tr>
	</table>

</div>

<div id="btnm_form">
<table border="0" width="100%" class="teks_">
    <tr>
        <td>
            <tr>
                <td align="left" width="125">Blok Tanam</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_code" class="input" tabindex="1" maxlength="21"/></td>
            </tr>
            <tr>
                <td align="left" >Kode Conv</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_codeconv" class="input" tabindex="2" maxlength="21"/></td>
            </tr>
            <tr>
                <td align="left">Blok</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_bid" class="input" tabindex="3" maxlength="15"/>
                </td>
            </tr>
            <tr>
                
                <td><input type="text" id="i_btnm_estatecode" style="display:none" tabindex="4"/></td>
            </tr>
        
            <tr>
                <td align="left">Deskripsi</td>
                <td>:</td>
                <td><textarea rows="5" cols="25" id="i_btnm_desc" style="height:50px; width:200px;" class="input" tabindex="5" maxlength="200">
                    </textarea> 
                </td>
            </tr>
            <tr>
                <td align="left">Planted</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_hplanted" class="input" tabindex="6" maxlength="10"/></td>
            </tr>
            <tr>
                <td align="left">Plantable</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_hplantable" class="input" tabindex="7" maxlength="10"/></td>
            </tr>
            <tr>
                <td align="left">Status</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_cropsstatus" class="input" tabindex="8" maxlength="15"/></td>
            </tr>
            <tr>
                <td align="left">Jumlah Pokok</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_numplantation" class="input" tabindex="9" maxlength="10"/></td>
            </tr>
            <tr>
                <td align="left">Tahun Tanam</td>
                <td>:</td>
                <td><input type="text" id="i_btnm_yearreplant" class="input" tabindex="10"/></td>
            </tr>
            <!-- <tr>
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
                </tr> -->
        </td>
    
        <td colspan="5"><input type="hidden" id="form_mode">
            <input type="button" id="submitdata" value="Simpan" onclick="submit()" tabindex="11">
            <input type="button" id="btnApproval" value="Pengesahan" onclick="approval()" tabindex="12">
        </td>
    </tr>
</table>
</div>

<div id="btnm_app">
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

