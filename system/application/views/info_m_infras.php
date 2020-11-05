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
colModelT_pts.push({name:'no_va',index:'no_va', editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('Kode Infrastruktur');
colModelT_pts.push({name:'IFCODE',index:'IFCODE', editable: true,hidden:false, width: 140, align:'left'});

colNamesT_pts.push('Deskripsi');
colModelT_pts.push({name:'IFNAME',index:'IFNAME', editable: true,hidden:false, width: 320, align:'left'});

colNamesT_pts.push('Tipe');
colModelT_pts.push({name:'IFTYPE',index:'IFTYPE', editable: true, hidden:true, width: 80, align:'left'});

colNamesT_pts.push('Deskripsi Tipe');
colModelT_pts.push({name:'IFTYPE_NAME',index:'IFTYPE_NAME', editable: true, hidden:false, width: 240, align:'center'});

colNamesT_pts.push('Sub Tipe');
colModelT_pts.push({name:'IFSUBTYPE',index:'IFSUBTYPE', editable: true,hidden:true, width: 80, align:'left'});

colNamesT_pts.push('Deskripsi Sub Tipe');
colModelT_pts.push({name:'IFSUBTYPE_NAME',index:'IFSUBTYPE_NAME', editable: true, hidden:false, width: 140, align:'center'});

colNamesT_pts.push('Afd');
colModelT_pts.push({name:'ESTATE',index:'ESTATE', editable: true,hidden:false, width: 60, align:'center'});
	
colNamesT_pts.push('Lokasi');
colModelT_pts.push({name:'IF_LOCATION',index:'IF_LOCATION', editable: true,hidden:false, width: 70, align:'center'});


colNamesT_pts.push('IFLENGTH');
colModelT_pts.push({name:'IFLENGTH',index:'IFLENGTH', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('IFWIDTH');
colModelT_pts.push({name:'IFWIDTH',index:'IFWIDTH', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('UOM');
colModelT_pts.push({name:'UOM',index:'UOM', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('INSTALLDATE');
colModelT_pts.push({name:'INSTALLDATE',index:'INSTALLDATE', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('VOLUME');
colModelT_pts.push({name:'VOLUME',index:'VOLUME', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('DEVELOPMENT_COST');
colModelT_pts.push({name:'DEVELOPMENT_COST',index:'DEVELOPMENT_COST', editable: true,hidden:true, width: 70, align:'center'});

colNamesT_pts.push('Active');
colModelT_pts.push({name:'ACTIVE',index:'ACTIVE', hidden:false, 
			editable: false, edittype:'checkbox', editoptions: { value:"0:1" }, formatter: "checkbox", 
			formatoptions: {disabled : true}, width: 50, align:'center'});

colNamesT_pts.push('Aktif RM');
colModelT_pts.push({name:'ISAPPR_RM',index:'ISAPPR_RM', hidden:false, 
			editable: false, edittype:'checkbox', editoptions: { value:"0:1" }, formatter: "checkbox", 
			formatoptions: {disabled : true}, width: 50, align:'center'});
						
colNamesT_pts.push('Perusahaan');
colModelT_pts.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true,hidden:true, width: 50, align:'center'});

//------------- end definisi colModel dan colNames -------------	
	
var loadView_pb = function()
{
    jgrid_pb = jQuery("#list").jqGrid(
    {
		url:'m_infras/LoadData/',  //loaddata untuk jGrid ->dari controller ->ke model
		datatype: 'json',  mtype: 'POST', 
		colNames:colNamesT_pts, colModel:colModelT_pts,
		pager: jQuery('#pager'),  rownumbers: true, 
        rowNum: 40,  width:1100,  height: 300,
		sortorder: "asc", forceFit : true, rowList:[10,20,30], 
		multiple:false, sortname: colNamesT_pts[1], 
		sortorder: "desc",  viewrecords: true, 
		caption: 'Daftar Infrastuktur',
		editurl:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadData/'	
    });
    jgrid_pb.navGrid('#pager',{edit:false,del:false,add:false, search: false, refresh: true});
	jgrid_pb.navButtonAdd('#pager',{
	   caption:"Export ke Excell", 
	   buttonicon:"ui-icon-add", 
	   onClickButton: function(){ 
	   window.location = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/create_excel/';
	   },
	   position:"left",
	});
    $("#alertmod").remove();//FIXME		 
}
jQuery("#list").ready(loadView_pb);
		
function init_infras()
{
	$("#i_if_code").removeAttr('disabled');
	$("#i_if_type").attr('style','display:none;');
	$("#i_if_subtype").attr('style','display:none;');
	$("#i_if_code").val("");
	$("#i_if_facode").val("");
	$("#i_if_type").val("");
	$("#i_if_subtype").val("");
	$("#i_if_name").val("");
	$("#i_if_estate").val("");
	$("#i_if_location").val("");
	$("#i_if_estate").val("");
	$("#i_if_location").val("");
	$("#i_if_len").val("");
	$("#i_if_width").val("");
	$("#i_if_uom").val("");
	$("#i_if_installdate").val("");
	$("#i_if_value").val("");
	$("#i_if_volume").val("");
	$("#i_active").attr('checked',false);
	$("#i_active_rm").attr('checked',false);
	
	$("#if_form").dialog('close');
	$("#form_mode").val('');	
}

$(function() 
{
	$("#if_form").dialog({
		bgiframe: false, autoOpen: false,
		height: 450, width: 550,
		modal: true, title: "Tambah Infrasturktur",
		resizable: false,
		moveable: true,
		buttons: { 
			'Tutup	': function() 
			{
				init_infras();		
			},
			'Simpan	': function() 
			{
				submit();		
			}
		} 
	});
	
    $("#i_date").datepicker({dateFormat:"yy-mm-dd"});
    $("#i_date2").datepicker({dateFormat:"yy-mm-dd"});
	$("#i_if_installdate").datepicker({dateFormat:"yy-mm-dd"});  
	
});

function gridReload()
{ 
    jQuery("#list").setGridParam
    ({url:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadData'}).trigger("reloadGrid");		
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
    ({url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/SearchData/"+code+"/"+desc}).trigger("reloadGrid");		
} 
  
//######################## START CHAIN FUNCTION #############################
$().ready(function()
{
    $("#i_if_facode").change(function() 
    {
        var product = $(this).val();
        if (product != 0) 
        {
          $("#i_if_type").empty();
          var cType = $("#i_if_type").val();
          if (cType==null)
          {
            cType="-";
          }
          $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadChain/'+$("#i_if_facode").val()+'/'+cType+'/', 
          $("#i_if_facode").val(),
            function(datapost) 
            { 
                $("#i_if_type").get(0).add(new Option(
                            " -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++)
                {
                    $("#i_if_type").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#i_if_type").attr('style','display:inline; width:250px;');
                    
            },"json")
        }
        else
        {
            $("#i_if_type").attr('style','display:none;');
        }
    });    
})
//######################## END CHAIN FUNCTION ###############################
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
        var answer = confirm ("Hapus Data Blok Tanam : " + data.IFCODE + ":" + data.IFNAME + "?" )
        if (answer)
        {
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/DeleteData/'+data.IFCODE, postdata,function(message,status) 
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
    init_infras();
    $("#btnApproval").attr('disabled','disabled') ;
    $("#if_form").dialog('open');
    $("#form_mode").val("POST");
}

function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();
	var inActive=0;
	var activerm=0;
    postdata['IFCODE'] = $("#i_if_code").val() ; 
    postdata['IFNAME']=$("#i_if_name").val();
    postdata['FIXEDASSETCODE']=$("#i_if_facode").val() ;
    postdata['IFTYPE']=$("#i_if_facode").val() ; 
    postdata['IFSUBTYPE']=$("#i_if_type").val() ; 
    postdata['ESTATE']=$("#i_if_estate").val() ; 
    postdata['IF_LOCATION']=$("#i_if_location").val() ; 
	postdata['IF_LENGTH']=$("#i_if_len").val() ; 
	postdata['IF_WIDTH']=$("#i_if_width").val() ; 
	postdata['IF_UOM']=$("#i_if_uom").val() ; 
	postdata['IF_INSTALLDATE']=$("#i_if_installdate").val() ; 
	postdata['DEVELOPMENT_COST']=$("#i_if_value").val(); 
	postdata['VOLUME']=$("#i_if_volume").val(); 
	
	var active = $("#i_active").is(':checked');
	
	if(active==false) {
			inActive=1;
	} else {
			inActive=0;
	}
	
	var activerm = $("#i_active_rm").is(':checked');
	
	if(activerm==true) {
			activerm=1;
	} else {
			activerm=0;
	}
	
   	postdata['INACTIVE']=inActive;
   	postdata['ISAPPR_RM']=activerm;
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    if  ($("#i_if_code").val() !="")
    {
        if (mode == "GET")
        {
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/EditData/'+$("#i_if_code").val(), postdata,function(message,status) { 
                if(status !== 'success') 
                { 
                   alert('data untuk infrastruktur ini sudah terisi.'); 
                } 
                else 
                { 
                   /* $.ajax({
                                    type:"POST",
                                    url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/delete_approve/"+$("#i_if_code").val(),
                                    data:false,
                                    success:false
                                }); */
                    gridReload();
                    alert('data berhasil terupdate.')
                    $("#if_app").dialog('close');
                    $("#if_form").dialog('close');    
                };
              } );
        } 
        else if (mode == "POST") 
        {
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/AddNew/'+$("#i_if_code").val(), postdata,function(message,status) 
            { 
                //alert(status)
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
                    $("#if_app").dialog('close');
                    $("#if_form").dialog('close');    
                };  
            } );     
        }    
    }
    else
    {
        alert("Harap isi IFCODE");
    }
    
}
function Edit()
{
    //var type=document.getElementById("i_type");
    //type.disabled=true;
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (ids!=null )
    {
		
        init_infras();
        $("#i_if_code").attr('disabled','true');
		
        $("#i_if_code").val(data.IFCODE);
        $("#i_if_facode").val(data.IFTYPE);
		
		
		$.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadChain/'+data.IFTYPE+'/'+data.IFTYPE+'/', 
		data.IFTYPE,
		  function(datapost) 
		  { 
			  $("#i_if_type").get(0).add(new Option(
						  " -- pilih -- ",""), document.all ? i : null);
			  for (var i=0; i<datapost.length; i++)
			  {
				  $("#i_if_type").get(0).add(new Option(
				  datapost[i].kt,datapost[i].kt2), document.all ? i : null);
			  }
			  $("#i_if_type").attr('style','display:inline; width:250px;');
			$("#i_if_type").val(data.IFSUBTYPE);	  
		  },"json")
					
        $("#i_if_type").val(data.IFTYPE);
        $("#i_if_subtype").val(data.IFSUBTYPE);
        $("#i_if_name").val(data.IFNAME);
        $("#i_if_estate").val(data.ESTATE);
        $("#i_if_location").val(data.IF_LOCATION);
		$("#i_if_len").val(data.IFLENGTH); 
		$("#i_if_width").val(data.IFWIDTH);
		$("#i_if_uom").val(data.UOM);
		$("#i_if_installdate").val(data.INSTALLDATE);
		$("#i_if_value").val(data.DEVELOPMENT_COST);
		$("#i_if_volume").val(data.VOLUME);

		if(data.ACTIVE==0){
		   $("#i_active").attr('checked',true);
		}else{
		   	$("#i_active").attr('checked',false);
		}
        $("#btnApproval").removeAttr('disabled') ;
        //alert(data.ISAPPR_RM);
        if(data.ISAPPR_RM==0){
		   $("#i_active_rm").attr('checked',true);
		}else{
		   	$("#i_active_rm").attr('checked',false);
		}
        $("#btnApproval").removeAttr('disabled') ;
        
                
        $("#if_form").dialog('open');
        $("#form_mode").val("GET");
    }
    else
    {
        alert("harap pilih data untuk di edit");
    }                
}

/* grid dari pengajuan RM */

var grid_rm = null;
var colNamesT_rm = new Array(); //definisi colNames untuk jGrid
var colModelT_rm = new Array(); //definisi colModel untuk jGrid

//------------- definisi colModel dan colNames -------------	
colNamesT_rm.push('id');
colModelT_rm.push({name:'no_va',index:'no_va', editable: true,hidden:true, width: 50, align:'center'});

colNamesT_rm.push('Kode Pengajuan');
colModelT_rm.push({name:'RM_PENGAJUAN_ID',index:'RM_PENGAJUAN_ID', editable: false,hidden:false, width: 70, align:'center'});

colNamesT_rm.push('Tgl Pengajuan');
colModelT_rm.push({name:'RM_TGL_PENGAJUAN',index:'RM_TGL_PENGAJUAN', editable: false,hidden:false, width: 65, align:'center'});

colNamesT_rm.push('Periode');
colModelT_rm.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:false, width: 50, align:'center'});

colNamesT_rm.push('Kode Infras');
colModelT_rm.push({name:'IFCODE',index:'IFCODE', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_rm.push('Keterangan');
colModelT_rm.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false,hidden:false, width: 200, align:'left'});

colNamesT_rm.push('Status Pengajuan');
colModelT_rm.push({name:'PENGAJUAN_STATUS',index:'PENGAJUAN_STATUS', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_rm.push('Persetujuan Kebun');
colModelT_rm.push({name:'ISAPPR1',index:'ISAPPR1', hidden:false, 
			editable: false, edittype:'checkbox', editoptions: { value:"0:1" }, formatter: "checkbox", 
			formatoptions: {disabled : true}, width: 80, align:'center'});

colNamesT_rm.push('Persetujuan HO');
colModelT_rm.push({name:'ISAPPR2',index:'ISAPPR2', hidden:false, 
			editable: false, edittype:'checkbox', editoptions: { value:"0:1" }, formatter: "checkbox", 
			formatoptions: {disabled : true}, width: 80, align:'center'});
									
colNamesT_rm.push('Perusahaan');
colModelT_rm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true,hidden:true, width: 50, align:'center'});

//------------- end definisi colModel dan colNames -------------	

var loadView_rm = function()
{
    jgrid_rm = jQuery("#listRM").jqGrid(
    {
		url:'m_infras/LoadDataRM/'+jQuery("#tahun").val() + jQuery("#bulan").val(),  //loaddata untuk jGrid ->dari controller ->ke model
		datatype: 'json',  mtype: 'POST', 
		colNames:colNamesT_rm, colModel:colModelT_rm,
		pager: jQuery('#pagerRM'),  rownumbers: true, 
        rowNum: 40,  width:1000,  height: 320,
		sortorder: "asc", forceFit : true, rowList:[10,20,30], 
		multiple:false, sortname: colModelT_rm[1].name, 
		sortorder: "desc",  viewrecords: true, 
		caption: 'Daftar Pengajuan RM Infrastuktur',
		editurl:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadDataRM/'	
    });
    jgrid_pb.navGrid('#pagerRM',{edit:false,del:false,add:false, search: false, refresh: true});
	jgrid_pb.navButtonAdd('#pagerRM',{
	   caption:"Export ke Excell", 
	   buttonicon:"ui-icon-add", 
	   onClickButton: function(){ 
	   window.location = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/create_excel/';
	   },
	   position:"left",
	});
    $("#alertmod").remove();//FIXME		 
}
jQuery("#listRM").ready(loadView_rm);

$(function(){
   	$("#bulan").change(function(){
       reloadGridRM()
	});
	
	$("#tahun").change(function(){
       reloadGridRM()
	}); 
	
	$("#sRM").click(function(){
		 var x = jQuery(this).position().left + jQuery(this).outerWidth();
    	var y = jQuery(this).position().top - jQuery(document).scrollTop();
    	jQuery("#rm_grid").dialog('option', 'position', [x-600,y-520]);
    
		$("#rm_grid").dialog('open');
		
		
	});
	
	$("#sAdem").click(function(){
		sinkronInfras();
	});
	 
	
	/* dialog rm */
	
	$("#rm_grid").dialog({
		bgiframe: false, autoOpen: false,
		height: 550, width: 1050,
		modal: true, title: "Pengajuan RM",
		resizable: false,
		moveable: true,
		buttons: { 
			'Tutup	': function() 
			{
				
			}
		} 
	});
	
	/* end dialog rm */
	
	/* dialog progress */	
	$("#progressbar").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 160,
                width: 220, modal: true
	}); 
	/* end dialog */
});

function sinkronInfras(){
	var postdata={};
	
	var answer = confirm ("Sinkron data ?" )
	
	if (answer == true){
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu..... Proses sinkronisasi master data ...";
		$("#progressbar").dialog('open');
			$.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/SyncDataInfras/',  postdata, function(status){ 
				var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
			            if( status > 0 ){
							$("#load").hide();
							$("#cok").attr("disabled", false);
							document.getElementById('msg').innerHTML= status + ' data berhasil tersinkron';
			                gridReload();      
			                   	  
			             
						} else {
							$("#load").hide();
							document.getElementById('msg').innerHTML= 'data gagal tersinkron';
							$("#cok").attr("disabled", false);
						}      
					} else { 
						
						$("#load").hide();
						document.getElementById('msg').innerHTML= status;
						$("#cok").attr("disabled", false);
					}	
			});
	 }
}

/* close progress bar */
function closewin(){
		$("#progressbar").dialog('close');
}
/* end close progress bar */

function reloadGridRM(){
	var periode = jQuery("#tahun").val() + jQuery("#bulan").val(); 
	jQuery("#listRM").setGridParam
    ({url:'<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_infras/LoadDataRM/'+periode}).trigger("reloadGrid");	
   
}

	
function load(url){
		var x = new XMLHttpRequest()
		x.open('GET', url, true);
		x.send();
}; 

/* end grid pengajuan RM */

//###################### END BUTTON FUNCTION ########################### 
</script>

<div id"gridSearch">  
    <table width="479" border="0" cellpadding="2" cellspacing="4" class="teks_">
    <tr><td height="34" colspan="8">Pencarian Berdasarkan :</td></tr>
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
</div> <br/>

<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table> 
    <div id="pager" class="scroll" style="text-align:center;"></div>
</div>
<br>

<div id="save" class="scroll" style="float:left;">
<input type="button" class="basicBtn" id="add" value="Tambah" onclick="TambahData()">
<input type="button" class="basicBtn" id="edit" value="Ubah" onclick="Edit()">
<input type="button" class="basicBtn" id="delete" value="Hapus" onclick="delData()">

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<input type="button" class="greenBtn" id="sAdem" value="Sinkron Master Data Adempiere" onclick="">
<input type="button" class="greenBtn" id="sRM" value="Sinkron Pengajuan RM" onclick="">

</div>

<div id="if_form">
<table border="0" width="100%" class="teks_">
	<tr>
    	<td colspan="3"></td>
			<tr>
				<td align="left">Tipe Infrastruktur</td>
				<td>:</td>
				<td>
				<? 
				if(isset($jenisaktiva)) {
					echo $jenisaktiva;
				}
				?>
				</td>
			</tr>
			<tr>
				<td align="left">Sub Tipe Infrastruktur</td>
				<td>:</td>
				<td>
				<select tabindex="3" name='i_if_type' class='select' id="i_if_type" style="display:none; width:250px;">
					
				</select>
				</td>
			</tr>
			<tr>
				<td align="left">Kode Infrastruktur</td>
				<td>:</td>
				<td><input type="text" id="i_if_code" class="input" style="width:160px;"/></td>
			</tr>
			<tr>
				<td align="left">Deskripsi</td>
				<td>:</td>
				<td><textarea rows="4" cols="30" id="i_if_name" style="height:40px; width:250px;" class="input">
					</textarea> 
				</td>
			</tr>
			<tr>
				<td align="left">Afd</td>
				<td>:</td>
				<td>
                <? 
				if(isset($afd)) {
					echo $afd;
				}
				?>
                </td>
			</tr>
			<tr>
				<td align="left">Lokasi</td>
				<td>:</td>
				<td><input type="text" id="i_if_location" class="input"/></td>
			</tr>
            <tr>
				<td align="left">Panjang</td>
				<td>:</td>
				<td><input type="text" id="i_if_len" class="input" style="width:50px;"/></td>
			</tr>
             <tr> 
				<td align="left">Lebar</td>
				<td>:</td>
				<td><input type="text" id="i_if_width" class="input" style="width:50px;"/></td>
			</tr>
             <tr>
				<td align="left">Satuan (unit)</td>
				<td>:</td>
				<td><input type="text" id="i_if_uom" class="input"/></td>
			</tr>
            <tr>
				<td align="left">Volume</td>
				<td>:</td>
				<td><input type="text" id="i_if_volume" class="input"/></td>
			</tr>
            <tr> 
				<td align="left">Tgl Selesai Pembangunan</td>
				<td>:</td>
				<td><input type="text" id="i_if_installdate" class="input"/></td>
			</tr>
            <tr>
				<td align="left">Biaya Pembangunan</td>
				<td>:</td>
				<td><input type="text" id="i_if_value" class="input"/></td>
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
                 <tr>
                    <td height="27" align="left">Active</td>
                    <td align="left">:</td>
                    <td><input type="checkbox" id="i_active" class="input_disable" /></td>
                </tr>
                
                <tr>
                    <td height="27" align="left">Aktif RM</td>
                    <td align="left">:</td>
                    <td><input type="checkbox" id="i_active_rm" class="input_disable" /> <input type="hidden" id="form_mode"></td>
                </tr>
		
		<tr>
		  <td>                
	
		 <!-- <td colspan="5">
		    <input tabindex="17" type="button" id="submitdata" value="Simpan" onclick="submit()">
           <input tabindex="17" type="button" id="btnApproval" value="Pengesahan" onclick="approval()"> 
        </td> 
	</tr> -->
</table>
</div>


<div id="rm_grid">
		<?php
		if(isset($periode)){
			echo $periode;
		}
		?>
		<div style="padding-top: 10px;"></div>
	    <table id="listRM" class="scroll"></table> 
	    <div id="pagerRM" class="scroll" style="text-align:center;"></div>
		<div style="padding-top: 10px;">
			<input type="button" style="font-size: 10px; font-weight: normal;" class="greenBtn" id="doProcessRM" value="Proses Sinkronisasi RM" onclick=""></div>
</div>


<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 
<!-- end progress bar -->

<!-- <div id="if_app">
 <table width="100%" class="teks_">
        <tr>
            
            <tr>
                <td align="left" width="125">Approve</td>
                <td align="left">:</td>
                <td><input name="i_ck_approve" type="checkbox" id="i_ck_approve"/></td>
            </tr>
                    
        
        </tr>
    </table>   
</div> -->
</form>
