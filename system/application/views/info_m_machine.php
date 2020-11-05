<? $template_path = base_url().$this->config->item('template_path'); ?>

</style>
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

colNamesT_pts.push('Kode Mesin');
colModelT_pts.push({name:'MACHINECODE',index:'MACHINECODE', 
editable: true,hidden:false, width: 35, align:'left'});
        
colNamesT_pts.push('Deskripsi');
colModelT_pts.push({name:'DESCRIPTION',index:'DESCRIPTION', 
editable: true,hidden:false, width: 200, align:'left'});

colNamesT_pts.push('Kepemilikan');
colModelT_pts.push({name:'OWNERSHIP',index:'OWNERSHIP', 
editable: true,hidden:false, width: 30, align:'center'});

colNamesT_pts.push('Satuan Prestasi');
colModelT_pts.push({name:'SATUAN_PRESTASI',index:'SATUAN_PRESTASI', 
editable: true,hidden:false, width: 30, align:'center'});

colNamesT_pts.push('Pengesahan');
colModelT_pts.push({name:'Approved',index:'Approved', 
editable: true,hidden:false, width: 30, align:'center'});

colNamesT_pts.push('Disahkan Oleh');
colModelT_pts.push({name:'Approved_By',index:'Approved_By', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('Tanggal Pengesahan');
colModelT_pts.push({name:'Approved_Date',index:'Approved_Date', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('');
colModelT_pts.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
  

//------------- end definisi colModel dan colNames -------------    
    
    var loadView_pb = function()
        {
            jgrid_pb = jQuery("#list").jqGrid(
            {
                url:"m_machine/LoadData/",  //loaddata untuk jGrid ->dari controller ->ke model
                datatype: 'json', 
                mtype: 'POST', 
                colNames:colNamesT_pts,
                colModel:colModelT_pts,
                pager: jQuery('#pager'), 
                rownumbers: true, 
                  rowNum: 40,
                width:800, 
                height:300,
                sortorder: "asc",
                forceFit : true,
                rowList:[10,20,30], 
                //sortname: colNamesT_pts[1], 
                sortorder: "desc",
                loadComplete: function(){ 
                    var ids = jQuery("#list").getDataIDs(); 
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i]; 
                            ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            jQuery("#list").setRowData(ids[i],{act:ce}) 
                        }
                                            
                    }, 
                viewrecords: true, 
                //imgpath: 'themes/basic/images',
                caption: 'List Machine',
                editurl:'m_machine/LoadData/'    
            });
            jgrid_pb.navGrid('#pager',{edit:false,del:false,add:false, search: false, refresh: true});
            //jgrid_pb.filterToolbar({stringResult: true,searchOnEnter : false});
            jgrid_pb.navButtonAdd('#pager',{
               caption:"Export ke Excell", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
            
                window.location = 'm_machine/create_excel/';
               
               },
               position:"left",
            });
            
            $("#alertmod").remove();//FIXME         
        }
        jQuery("#list").ready(loadView_pb);
        
        function init_mac()
        {
            $("#i_macode").val("");
            $("#i_desc").val("");
            $("#i_own").val("");
            $("#i_uom").val("");
            
            $("#i_approv").val("");
            $("#i_approv_by").val("");
            $("#i_approv_date").val("");
            
            $("#fragment_2").attr('style','display:inline') ;
            $("#fragment-2").attr('style','display:inline') ;
            $("#i_ck_approve").removeAttr('checked');
            $("#submitapprove,#add,#edit,#delete,#submitdata,#updatedata").removeAttr('disabled'); 
            
            $("#mac_form").dialog('close');
            $("#form_mode").val('');    
        }
        
        $(function() 
        {
            $("#mac_form").dialog({
                bgiframe: true,
                autoOpen: false,
                height: 450,
                width: 500,
                modal: true,
                title: "Tambah Mesin",
                resizable: false,
                moveable: true,
                buttons: {
                    'Tutup    ': function() 
                    {
                        init_mac();        
                    }
                } 
            }); 
        });
        $(function() 
        {
            $("#i_date").datepicker({dateFormat:"yy-mm-dd"});
        });
        
          
      function gridReload()
      { 
        jQuery("#list").setGridParam
        ({url:'m_machine/LoadData'}).trigger("reloadGrid");        
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
        ({url:"m_machine/SearchData/"+code+"/"+desc}).trigger("reloadGrid");        
      } 
</script>
<script type="text/javascript"0>
//###################### START BUTTON FUNCTION ###########################
function delData()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
      var data = $("#list").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Data Mesin : " + data.MACHINECODE + ":" + data.DESCRIPTION + "?" )
        if (answer)
        {
            $.post( 'm_machine/DelData/'+data.MACHINECODE, postdata,function(message,status) 
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
    //var type=document.getElementById("i_type");
    //type.disabled=false;    
    init_mac();
    $("#fragment_2").attr('style','display:none') ;
    $("#fragment-2").attr('style','display:none') ;
    
    $("#mac_form").dialog('open');
    $("#i_macode").removeAttr('disabled'); 
    $("#btnApproval").attr('disabled','disabled') ;
    $("#form_mode").val("POST");
}
function Edit()
{
      //var type=document.getElementById("i_type");
    //type.disabled=true;
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (ids!=null )
    {
        init_mac();
        
        $("#i_macode").val(data.MACHINECODE);         
        $("#i_desc").val(data.DESCRIPTION);
        $("#i_own").val(data.OWNERSHIP);
        $("#i_uom").val(data.SATUAN_PRESTASI);
        
        $("#i_macode").attr('disabled','disabled');
        $("#btnApproval").removeAttr('disabled') ;
        
        test=data.Approved;
        if(1==data.Approved || test.toUpperCase()=='APPROVED')
        {
            $("#i_ck_approve").attr('checked','true'); 
            $("#i_approv").val("APPROVED");   
        }
        
        $("#i_approv_by").val(data.Approved_By);
        $("#i_approv_date").val(data.Approved_Date);
        $("#mac_form").dialog('open');
        $("#form_mode").val("GET");
    }
    else
    {
        alert("harap pilih data untuk di edit");
    }                
}

function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();
    postdata['MACHINECODE'] = $("#i_macode").val() ; 
    postdata['DESCRIPTION'] = $("#i_desc").val() ; 
    postdata['OWNERSHIP'] = $("#i_own").val() ;
    postdata['SATUAN_PRESTASI'] = $("#i_uom").val() ;
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    
    if (mode == "GET")
    {
        $.post( 'm_machine/EditData/'+$("#i_macode").val(), 
        postdata,
        function(message,status) { 
            if(status !== 'success') 
            { 
               alert('data untuk kendaraan ini sudah terisi.'); 
            } 
            else 
            { 
                $.ajax({
                            type:"POST",
                            url:"m_machine/delete_approve/"+$("#i_macode").val(),
                            data:false,
                            success:false
                        });
                gridReload();
                alert('data berhasil terupdate.')
                //$("#mac_app").dialog('close');
                $("#mac_form").dialog('close');    
            };
          } );
    } 
    else if (mode == "POST") 
    {
        $.post('m_machine/AddNew/'+$("#i_macode").val(), 
        postdata,
        function(status) 
        { 
            var status = new String(status);
            if(status.replace(/\s/g,"") != "") 
            { 
                if(status==1){
                    gridReload();
                    alert('data berhasil tersimpan.') 
                    $("#mac_form").dialog('close');      
                 }else{
                    gridReload();
                    alert(status);    
                 }  
            } 
            else 
            { 
                gridReload();
                alert('data berhasil tersimpan.')
                //$("#mac_app").dialog('close');
                $("#mac_form").dialog('close');    
            };  
        } );     
    }
}

function lihat(kode)
{     
    var data = $("#list").getRowData(kode) ; 
    if (null==kode || ''==kode)
    {
        alert ("harap pilih data!!!");
    }
    else
    {
        init_mac();
        
        $("#submitdata,#submitapprove").attr('disabled','disabled') ; 
        $("#i_macode").val(data.MACHINECODE);         
        $("#i_desc").val(data.DESCRIPTION);
        $("#i_own").val(data.OWNERSHIP);
        $("#i_uom").val(data.SATUAN_PRESTASI);
        
        $("#i_macode").attr('disabled','disabled');
        
        test=data.Approved;
        if(1==data.Approved || test.toUpperCase()=='APPROVED')
        {
            $("#i_ck_approve").attr('checked','true'); 
            $("#i_approv").val("APPROVED");   
        }
        
        $("#i_approv_by").val(data.Approved_By);
        $("#i_approv_date").val(data.Approved_Date);
        $("#mac_form").dialog('open');
        $("#form_mode").val("");       
    } 
    
}
//###################### END BUTTON FUNCTION ###########################
</script>
<script type="text/javascript">
//########################### START APPROVAL FUNCTION #############################
function save_approval()
{
    var confirmsg = confirm("Approve data? ");
    if(confirmsg)
    {
        if($("#i_ck_approve").attr('checked')==true)
        {
            var ids = jQuery("#list").getGridParam('selrow'); 
            var data = $("#list").getRowData(ids);
            var approval_id=$("#i_macode").val();
            $.post('m_machine/update_approve/'+approval_id,'' ,function(status) 
            {
                var status = new String(status);
                if(status==1)
                {
                    alert("update approve berhasil!!!");
                    $("#i_ck_approve").removeAttr('checked');
                    $("#mac_form").dialog('close');
                    gridReload();    
                }else{
                    alert (status);   
                }   
            });
        }
        else if($("#i_ck_approve").attr('checked')==false)
        {
            $.post('m_machine/delete_approve/'+$('#i_macode').val(),'' ,function(status) 
            {
                var status = new String(status);
                if(status==1)
                {
                    alert("update approve berhasil!!!");
                    $("#mac_form").dialog('close');
                    gridReload();    
                }else{
                    alert (status);   
                }   
            });
        }
    }   
}

//########################### END APPROVAL FUNCTION #############################
</script>
<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
</script>

<br>
<div id="Main">

<div id"gridSearch" >
    <table border="0" class="teks_" cellpadding="100" cellspacing="4">
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

<div id="mac_form">
    <div id="tabs">
        <ul>
            <li><a href="#fragment-1"><span>Detail Kendaraan</span></a></li>
            <?php echo $htmlapprove[0]; ?>
            <li><a href="#fragment-3"><span>History</span></a></li>
        </ul>
        <div id="fragment-1">
            <table border="0" width="100%" border="0" class="teks_">
                
                        <tr>
                            <td align="left" width="115">Kode Mesin</td>
                            <td>:</td>
                            <td><input type="text" id="i_macode" class="input" tabindex="1" maxlength="15"/></td>
                        </tr>
                        
                        <tr>
                            <td align="left">Deskripsi</td>
                            <td>:</td>
                            <td><textarea rows="5" cols="25" id="i_desc" style="height:50px; width:200px;" class="input" tabindex="2" maxlength="50">
                             </textarea> </td>
                        </tr>
                        
                        <tr>
                            <td align="left">Kepemilikan</td>
                            <td align="left">:</td>
                            <td>
                            <select name='i_own' class='select' id="i_own" tabindex="3">
                                <option value=""> -- pilih -- </option>
                                  <option value="R">Rental</option>
                                  <option value="I">Inventaris</option>
                            </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="left">Satuan Prestasi</td>
                            <td align="left">:</td>
                            <td>
                            <select name='i_uom' class='select' id="i_uom" tabindex="4">
                                <option value=""> -- pilih -- </option>
                                  <option value="KM">KM</option>
                                  <option value="HM">HM</option>
                            </select>

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
                                 
                    <tr>
                     
                        <td colspan="5"><input type="hidden" id="form_mode">
                        <div align="right">
                            <hr>
                            <input type="button" id="submitdata" value="Simpan" onclick="submit()" tabindex="5">
                        </div>
                        
                        </td>
                    </tr>
            </table>
        </div>
        
        <?php echo $htmlapprove[1]; ?>
        <div id="fragment-3">
            
        </div>
    </div>
</div>
</div>




