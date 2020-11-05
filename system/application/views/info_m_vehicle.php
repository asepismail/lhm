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
jQuery(document).ready(function(){
    document.getElementById("search_nik").value="";
    $("#search_desc").val("");
    $("#search_appr").val("");

    var grid_pts = null;
    var colNamesT_pts = new Array(); //definisi colNames untuk jGrid
    var colModelT_pts = new Array(); //definisi colModel untuk jGrid

    //------------- definisi colModel dan colNames -------------    
    colNamesT_pts.push('id');
    colModelT_pts.push({name:'no_va',index:'no_va', 
    editable: true,hidden:true, width: 30, align:'center'});

    colNamesT_pts.push('Kode Kendaraan');
    colModelT_pts.push({name:'VEHICLECODE',index:'VEHICLECODE', 
    editable: true,hidden:false, width:90, align:'left'});
            
    colNamesT_pts.push('No. Kendaraan');
    colModelT_pts.push({name:'REGISTRATIONNO',index:'REGISTRATIONNO',
    editable: true,hidden:false, width: 90, align:'left'});
            
    colNamesT_pts.push('Deskripsi');
    colModelT_pts.push({name:'DESCRIPTION',index:'DESCRIPTION', 
    editable: true,hidden:false, width: 200, align:'left'});

    colNamesT_pts.push('Kepemilikan');
    colModelT_pts.push({name:'OWNERSHIP',index:'OWNERSHIP', 
    editable: true,hidden:false, width:80, align:'center'});

    colNamesT_pts.push('PIC');
    colModelT_pts.push({name:'CONTACTNAME',index:'CONTACTNAME', 
    editable: true,hidden:false, width:60, align:'center'});

    colNamesT_pts.push('Sat. Prestasi');
    colModelT_pts.push({name:'SATUAN_PRESTASI',index:'SATUAN_PRESTASI', 
    editable: true,hidden:false, width: 85, align:'center'});

    colNamesT_pts.push('Pengesahan');
    colModelT_pts.push({name:'Approved',index:'Approved', 
    editable: true,hidden:true, width: 30, align:'center'});

    colNamesT_pts.push('Disahkan Oleh');
    colModelT_pts.push({name:'Approved_BY',index:'Approved_BY', 
    editable: true,hidden:true, width: 50, align:'center'});

    colNamesT_pts.push('Tanggal Pengesahan');
    colModelT_pts.push({name:'Approved_DATE',index:'Approved_DATE', 
    editable: true,hidden:true, width: 50, align:'center'});
    
    colNamesT_pts.push('');
    colModelT_pts.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});


    //------------- end definisi colModel dan colNames -------------    
        
    var loadView_pb = function()
    {
        jgrid_pb = jQuery("#list").jqGrid(
        {    
            url:"m_vehicle/LoadData/",  //loaddata untuk jGrid ->dari controller ->ke model
            datatype: 'json', 
            mtype: 'POST', 
            colNames:colNamesT_pts,
            colModel:colModelT_pts,
            pager: jQuery('#pageri'), 
            rownumbers: true, 
            rowNum: 20,
            width:800,
            height:300, 
            sortorder: "asc",
            forceFit : true,
            rowList:[10,20,30], 
            sortname: colNamesT_pts[1], 
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
            caption: 'List Vehicle',
            editurl:"m_vehicle/LoadData/"    
        });
        jgrid_pb.navGrid('#pageri',{edit:false,del:false,add:false, search: false, refresh: true});
        //jgrid_pb.filterToolbar({stringResult: true,searchOnEnter : false});
        jgrid_pb.navButtonAdd('#pageri',{
           caption:"Export ke Excell", 
           buttonicon:"ui-icon-add", 
           onClickButton: function(){ 
        
            window.location = "m_vehicle/create_excel/";
           
           },
           position:"left",
        });
        $("#alertmod").remove();//FIXME         
    }
    jQuery("#list").ready(loadView_pb);
    
    $("#i_date").datepicker({dateFormat:"yy-mm-dd"});
    
    $("#vhc_form").dialog({
        bgiframe: true,
        autoOpen: false,
        height: 450,
        width: 500,
        modal: true,
        title: "Kendaraan",
        resizable: false,
        moveable: true,
        buttons: {
            Tutup    : function() 
            {
                init_vhc();        
            },
        } 
    });              
});
var timeoutHnd;
function gridReload()
{ 
    jQuery("#list").setGridParam
    ({url:"m_vehicle/LoadData/"}).trigger("reloadGrid");        
} 
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
    var appr = jQuery("#search_appr").val(); 

    if (code == ""){
    code = "-";
    } 
    if (desc == ""){
    desc = "-";
    } 

    jQuery("#list").setGridParam
    ({url:"m_vehicle/SearchData/"+code+"/"+desc+"/"+appr}).trigger("reloadGrid");        
}


function init_vhc()
{
    $("#i_vhcode").val("");
    $("#i_regno").val("");
    $("#i_vhcgrpcode").val("");
    $("#i_desc").val("");
    $("#i_own").val(""); 
    $("#i_contact").val("");
    $("#i_uom").val("");
    $("#i_approv").val("");
    $("#i_approv_by").val("");
    $("#i_approv_date").val("");
    
    $("#fragment_2").attr('style','display:inline') ;
    $("#fragment-2").attr('style','display:inline') ; 
        
    $("#i_ck_approve").removeAttr('checked');
    $("#submitapprove,#add,#edit,#delete,#submitdata,#updatedata").removeAttr('disabled');
    
    $("#vhc_form").dialog('close');
    $("#form_mode").val('');    
}
</script>

<script type="text/javascript">
//############################ START BUTTON FUNCTION #################################
function delData()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Data Kendaraan : " + data.VEHICLECODE + ":" + data.DESCRIPTION + "?" )
        if (answer)
        {
            $.post('m_vehicle/DelData/'+data.VEHICLECODE, postdata,function(message) 
            { 
              if(message !=0) 
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

function Edit()
{   
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    if (null==ids || ''==ids)
    {
        alert ("harap pilih data!!!");
    }
    else
    {
        init_vhc();
        $("#updatedata").removeAttr('disabled') ;
        $("#submitdata").attr('disabled','disabled') ;
         
        $('i_nik').addClass('disable');
        $('i_nik_ot').addClass('disable');
        
        $("#i_vhcode").val(data.VEHICLECODE) ; 
        $("#i_regno").val(data.REGISTRATIONNO) ; 
        $("#i_vhcgrpcode").val(data.VEHICLEGROUPNO) ; 
        $("#i_desc").val(data.DESCRIPTION);
        $("#i_own").val(data.OWNERSHIP); 
        $("#i_contact").val(data.CONTACTNAME);
        $("#i_uom").val(data.SATUAN_PRESTASI); 
        
        $("#i_vhcode").attr('disabled','disabled');
        //$("#approval").removeAttr('disabled') ;        
        $("#i_approv").val(data.Approved);
        test=data.Approved;
        if(1==data.Approved || test.toUpperCase()=='APPROVED')
        {
            $("#i_ck_approve").attr('checked','true');    
        }
        $("#i_approv_by").val(data.Approved_BY);
        $("#i_approv_date").val(data.Approved_DATE);
        $("#vhc_form").dialog('open');
        $("#form_mode").val("GET");        
        }
}         
function TambahData()
{   
    init_vhc();
    $("#i_vhcode").removeAttr('disabled'); 
    $("#fragment_2").attr('style','display:none') ;
    $("#fragment-2").attr('style','display:none') ;
    $("#updatedata").attr('disabled','disabled') ; 
    $("#vhc_form").dialog('open');
    $("#form_mode").val("POST");
}

function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();
    postdata['VEHICLECODE'] = $("#i_vhcode").val() ; 
    postdata['REGISTRATIONNO'] = $("#i_regno").val() ; 
    postdata['VEHICLEGROUPCODE'] = $("#i_vhcgrpcode").val() ; 
    postdata['DESCRIPTION']= $("#i_desc").val();
    postdata['OWNERSHIP'] = $("#i_own").val(); 
    postdata['CONTACTNAME']=$("#i_contact").val();
    postdata['SATUAN_PRESTASI'] = $("#i_uom").val(); 
    
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    
    
    if (mode == "GET")
    {
        var confirmsg = confirm("Update data? ");
        if(confirmsg)
        {
            $.post( "m_vehicle/EditData/"+$("#i_vhcode").val(), 
            postdata,
            function(message) { 
                if(message !=0) 
                { 
                   alert('data untuk kendaraan ini sudah terisi.'); 
                } 
                else 
                { 
                    $.ajax({
                                type:"POST",
                                url:"<?php echo $_SERVER['PHP_SELF']; ?>/delete_approve/"+$("#i_vhcode").val(),
                                data:false,
                                success:false
                            });
                    alert('data berhasil ter-Update.')
                    gridReload();
                    $("#vhc_form").dialog('close'); 
                      
                };
              } );
        }
        
    } 
    else if (mode == "POST") 
    {
        var confirmsg = confirm("Tambah data? ");
        if(confirmsg)
        {
            $.post(  "m_vehicle/AddNew/", 
            postdata,
            function(message) 
            { 
                if(message != 0 ) 
                { 
                    alert(message); 
                    gridReload();  
                } 
                else 
                { 
                    alert('data berhasil tersimpan.')
                    gridReload();
                    $("#vhc_form").dialog('close');
                    $("#vhc_app").dialog('close');        
                };  
            } );
        }
             
    }
}

function save_approval()
{
    var confirmsg = confirm("Approve data? ");
    if(confirmsg)
    {
        if($("#i_ck_approve").attr('checked')==true)
        {
            var ids = jQuery("#list").getGridParam('selrow'); 
            var data = $("#list").getRowData(ids);
            var approval_id=$("#i_vhcode").val();
            $.post('m_vehicle/update_approve/'+approval_id,'' ,function(status) 
            {
                var status = new String(status);
                if(status==1)
                {
                    alert("update approve berhasil!!!");
                    $("#i_ck_approve").removeAttr('checked');
                    $("#vhc_form").dialog('close');
                    gridReload();    
                }else{
                    alert (status);   
                }   
            });
        }
        else if($("#i_ck_approve").attr('checked')==false)
        {
            $.post('m_vehicle/delete_approve/'+$('#i_vhcode').val(),'' ,function(status) 
            {
                var status = new String(status);
                if(status==1)
                {
                    alert("update approve berhasil!!!");
                    $("#vhc_form").dialog('close');
                    gridReload();    
                }else{
                    alert (status);   
                }   
            });
        }
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
        init_vhc();
        //$("#updatedata").removeAttr('disabled') ;
        $("#submitdata,#updatedata,#submitapprove").attr('disabled','disabled') ;
         
        $('i_nik').addClass('disable');
        $('i_nik_ot').addClass('disable');
        
        $("#i_vhcode").val(data.VEHICLECODE) ; 
        $("#i_regno").val(data.REGISTRATIONNO) ; 
        $("#i_vhcgrpcode").val(data.VEHICLEGROUPNO) ; 
        $("#i_desc").val(data.DESCRIPTION);
        $("#i_own").val(data.OWNERSHIP); 
        $("#i_contact").val(data.CONTACTNAME);
        $("#i_uom").val(data.SATUAN_PRESTASI); 
        
        $("#i_vhcode").attr('disabled','disabled');
        //$("#approval").removeAttr('disabled') ;        
        $("#i_approv").val(data.Approved);
        test=data.Approved;
        if(1==data.Approved || test.toUpperCase()=='APPROVED')
        {
            $("#i_ck_approve").attr('checked','true');    
        }
        $("#i_approv_by").val(data.Approved_BY);
        $("#i_approv_date").val(data.Approved_DATE);
        $("#vhc_form").dialog('open');
        $("#form_mode").val("");        
    } 
    
}

//############################ END BUTTON FUNCTION ################################   
</script>

<script type="text/javascript">
//########################### START APPROVAL FUNCTION ############################# 
/*function approval()
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
        $("#vhc_app").dialog('open');
    }
}*/
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
<div id"gridSearch">  
    <!--<div><?php //echo $search; ?></div> -->
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
            <td>Pengesahan</td>
            <td>:</td>
            <td>
            <input type="text" class="input" id="search_appr" onkeydown="doSearch(arguments[0]||event)" />
            </td>
        </tr>
    </table>
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table> 
    <div id="pageri" class="scroll" style="text-align:center;"></div>
</div>
<br />
<div style="float:left;">
    <input type="button"  id="btn_add" value="Tambah" onclick="TambahData()">
    <input type="button"  id="btn_edit" value="Ubah" onclick="Edit()">
    <input type="button"  id="btn_delete" value="Hapus" onclick="delData()">
</div> 
<div id="vhc_form">
    <div id="tabs">
        <ul>
            <li><a href="#fragment-1"><span>Detail Kendaraan</span></a></li>
            <?php echo $htmlapprove[0]; ?>
            <li><a href="#fragment-3"><span>History</span></a></li>
        </ul>
        <div id="fragment-1">
            <table width="100%" class="teks_">
                <tr>
                    <td align="left" width="125">Kode Kendaraan</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_vhcode" class="input" tabindex="1" maxlength="15"/></td>
                </tr>
                <tr>
                    <td align="left">No Registrasi</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_regno" class="input" tabindex="2" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">Deskripsi</td>
                    <td align="left">:</td>
                    <td>
                    <textarea rows="5" cols="25" id="i_desc" style="height:50px; width:200px;" class="input" tabindex="3" maxlength="200"></textarea> 
                    </td>
                </tr>
                <tr>
                    <td align="left">Kepemilikan</td>
                    <td align="left">:</td>
                    <td>
                    <select name='i_own' class='select' id="i_own" tabindex="4">
                        <option value=""> -- pilih -- </option>
                          <option value="R">Rental</option>
                          <option value="I">Inventaris</option>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td align="left">PIC</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_contact" class="input" tabindex="5" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">UOM</td>
                    <td align="left">:</td>
                    <td>
                    <select name='i_uom' class='select' id="i_uom" tabindex="6">
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
                    <td colspan="5">
                        <hr>
                        <div align="right">
                            <input type="button" id="submitdata" value="Simpan" onclick="submit()" tabindex="7">
                            <input type="button" id="updatedata" value="Update" onclick="submit()" tabindex="7">
                        </div> 
                        <!--<input type="button" id="approval" value="Pengesahan" onclick="approval()" tabindex="8">-->
                    </td>
                </tr>
            </table>           
        </div>
        <?php echo $htmlapprove[1]; ?>
        <div id="fragment-3">
            
        </div>
    </div>
<input type="text" id="form_mode" style="display: none;">  
</div>

</body>
