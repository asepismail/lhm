<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
//###################### OTHER FORM FUNCTION #######################
var timeoutHnd;  

function stringFunction(str)
{
    this.str=str;
    this.strToLower = function (){
         return (str+'').toLowerCase();
    }
    this.trim = function(){
        return str.replace(/^\s+|\s+$/g,'');
    }
    this.regExpIs_valid = function(){
        var pattern= new RegExp(/^[a-z0-9A-Z-\s]+$/);
        return pattern.test(str);
    }
}
           
function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement;      
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 
function gridReload()
{ 
    $.ajax({
        type:"POST",
        url:"<?php echo $_SERVER['PHP_SELF']; ?>/delete_approve/"+$("#i_vhcode").val(),
        data:false,
        success:false
    });

    /*var afd = jQuery("#search_afd").val(); 
    var name = jQuery("#search_name").val(); 
    var type = jQuery("#search_type").val(); 

    var strFunction = new stringFunction(afd);   
    if (strFunction.trim() == "")
    {
        afd = "-";
    } 
    if (strFunction.trim()=="")
    {
        name = "-";
    }
    if (strFunction.trim()=="")
    {
        type = "-";
    }  
    
     var urls = url+"Project/Prj_Pengajuan/search_prj/"+afd+"/"+name+"/"+type;
     jQuery("#"+gridNames+"").setGridParam({url:url+"Project/Prj_Pengajuan/search_prj/"+afd+"/"+name+"/"+type}).trigger("reloadGrid");    */
}

</script>

<script type="text/javascript">
//###################### GRID FORM FUNCTION ####################### 
var url="<?= base_url().'index.php/' ?>";
var urls="project/prj_pengajuan/loadData/";
var gridNames = "jsGrid";
//var gridimgpath = '<?= $template_path ?>themes/basic/images'; //definisi imagepath pada jgrid

jQuery('document').ready(function()
{
    //------------- definisi colModel dan colNames -------------    
    var grid_pts = null;
    var colNames = new Array(); //definisi colNames untuk jGrid
    var colModel = new Array(); //definisi colModel untuk jGrid
    
    //alert(strFunction.strToLower())
    /*defineGridCol(["no_prj","PROJECT_ID","AFD","PROJECT_TYPE","PROJECT_SUBTYPE","PROJECT_DESC","PROJECT_START","PROJECT_END","PROJECT_STATUS"],
                    ["id","PROJECT ID","AFD","Tipe","Subtype","Pekerjaan","PROJECT START","PROJECT END","STATUS"],
                    ["20,true,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left",
                    "50,false,left"]);
    
                   
    function defineGridCol(nColModel,nColNames,nColAttr)//nColWidth,nColHidden,nColAllign
    {
        for (var i=0; i<=nColNames.length-1; i++)
        {
            nColAttrArr=nColAttr[i].split(',');
            nColWidth= nColAttrArr[0];
            nColHidden= nColAttrArr[1];
            nColAllign= nColAttrArr[2];

            colNames.push(nColNames[i]);
            //colModel.push({name:nColModel[i],index:nColModel[i], 
            //editable: true,hidden:nColHidden, width:nColWidth, align:nColAllign});
            var strFunction = new stringFunction(nColNames[i]);
            if('id'==strFunction.strToLower())
            {
                 colModel.push({name:nColModel[i],index:nColModel[i], 
                 editable: true,hidden:true, width: 50, align:'left'});
            }
            else
            {
                 colModel.push({name:nColModel[i],index:nColModel[i], 
                 editable: true,hidden:false, width: 50, align:'left'});
            }
              
        }
        //alert (nColNames.length-1);
    }*/
    colNames.push('id');
    colModel.push({name:'no_prj',index:'no_prj', 
    editable: true,hidden:true, width: 30, align:'center'});

    colNames.push('PROJECT ID');
    colModel.push({name:'PROJECT_ID',index:'PROJECT_ID', 
    editable: true,hidden:false, width: 30, align:'center'});
            
    colNames.push('AFD');
    colModel.push({name:'AFD',index:'AFD',
    editable: true,hidden:false, width: 20, align:'center'});
            
    colNames.push('Tipe');
    colModel.push({name:'PROJECT_TYPE',index:'PROJECT_TYPE', 
    editable: true,hidden:false, width: 20, align:'center'});

    colNames.push('Subtipe');
    colModel.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE', 
    editable: true,hidden:false, width:30, align:'left'});

    colNames.push('Deskripsi');
    colModel.push({name:'PROJECT_DESC',index:'PROJECT_DESC', 
    editable: true,hidden:false, width:100, align:'left'});

    colNames.push('Projek Mulai');
    colModel.push({name:'PROJECT_START',index:'PROJECT_START', 
    editable: true,hidden:false, width: 35, align:'center'});

    colNames.push('Projek Selesai');
    colModel.push({name:'PROJECT_END',index:'PROJECT_END', 
    editable: true,hidden:false, width: 30, align:'center'});

    colNames.push('Status');
    colModel.push({name:'PROJECT_STATUS',index:'PROJECT_STATUS', 
    editable: true,hidden:true, width: 50, align:'center'});
    
    colNames.push('Pengesahan');
    colModel.push({name:'Approved',index:'Approved', 
    editable: true,hidden:false, width: 30, align:'center'});

    colNames.push('Disahkan Oleh');
    colModel.push({name:'Approved_BY',index:'Approved_BY', 
    editable: true,hidden:true, width: 50, align:'center'});

    colNames.push('Tanggal Pengesahan');
    colModel.push({name:'Approved_DATE',index:'Approved_DATE', 
    editable: true,hidden:true, width: 50, align:'center'});

     
    //------------- end definisi colModel dan colNames -------------    
    
    $.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
    var maintab =jQuery('#tabs','#RightPane').tabs({
        add: function(e, ui) {
            // append close thingy
            $(ui.tab).parents('li:first')
                .append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
                .find('span.ui-tabs-close')
                .click(function() {
                    maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
                });
            // select just added tab
            maintab.tabs('select', '#' + ui.panel.id);
        }
    });
        
    var loadView_pb = function()
    {
        jgrid_pb = jQuery("#list").jqGrid(
        {
            url:url+urls,  //loaddata untuk jGrid ->dari controller ->ke model
            datatype: 'json', 
            mtype: 'POST', 
            colNames:colNames,
            colModel:colModel,
            pager: jQuery('#GridPager'), 
            rownumbers: true, 
              rowNum: 20,
            width:800, 
            height: 300,
            sortorder: "asc",
            forceFit : true,
            rowList:[10,20,30], 
            multiple:true,
            sortname: colNames[1], 
            sortorder: "desc", 
            viewrecords: true, 
            //imgpath: 'themes/basic/images',
            caption: 'Deskripsi Project',
            editurl:url+urls    
        });
        jgrid_pb.navGrid('#GridPager',{edit:false,del:false,add:false, search: false, refresh: true});
        //jgrid_pb.filterToolbar({stringResult: true,searchOnEnter : false});
        
        jgrid_pb.navButtonAdd('#GridPager',{
           caption:"Export ke Excell", 
           buttonicon:"ui-icon-add", 
           onClickButton: function(){ window.location = url+urls;},
           position:"left",
        });
        

        $("#alertmod").remove();//FIXME         
    }
    jQuery("#list").ready(loadView_pb);
    
    $("#i_start").datepicker({dateFormat:"yy-mm-dd"});
    $("#i_end").datepicker({dateFormat:"yy-mm-dd"}); 
});

function init_prj()
{
    $("#i_prjid").val(""); 
    $("#i_afd").val(""); 
    $("#i_prjtype").val(""); 
    $("#i_prjsubtype").val("");
    $("#i_prjdesc").val(""); 
    $("#i_start").val("");
    $("#i_end").val(""); 
    $("#i_stat").val(""); 
    $("#form_mode").val("");
    
    $("#prj_form").dialog('close');  
}
</script>

<script type="text/javascript">
//############################ START BUTTON FUNCTION ################################# 
jQuery(document).ready(function(){
   $("#prj_form").dialog({
        bgiframe: true,
        autoOpen: false,
        height: 325,
        width: 400,
        modal: true,
        title: "Project",
        resizable: false,
        moveable: true,
        buttons: {
            'Tutup    ': function() 
            {
                init_prj();        
            }
        } 
    });  
});

function delData()
{
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ;
    if( ids != null ) 
    {
        var answer = confirm ("Hapus Data Project : " + data.PROJECT_ID + ":" + data.PROJECT_DESC + "?" )
        if (answer)
        {
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/DelData/'+data.PROJECT_ID, 
            '',
            function(message) 
            { 
              if(message !=0) 
              { 
                 alert(message); 
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
    init_prj();
       
    $("#i_prjid").removeAttr('disabled'); 
    $("#approval").attr('disabled','disabled') ; 
    
    $("#prj_form").dialog('open');
    $("#form_mode").val("POST"); 
}
function submit()
{
    var postdata={};
    var ids = jQuery("#list").getGridParam('selrow'); 
    var data = $("#list").getRowData(ids) ; 
    var mode = $("#form_mode").val();

    postdata['PROJECT_ID'] = $("#i_prjid").val() ; 
    postdata['AFD'] = $("#i_afd").val() ; 
    postdata['PROJECT_TYPE'] = $("#i_prjtype").val() ; 
    postdata['PROJECT_SUBTYPE']= $("#i_prjsubtype").val();
    postdata['PROJECT_DESC'] = $("#i_prjdesc").val(); 
    postdata['PROJECT_START']=$("#i_start").val();
    postdata['PROJECT_END'] = $("#i_end").val(); 
    postdata['PROJECT_STATUS'] = $("#i_stat").val();   
    postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
    
    if (mode == "GET")
    {
        $.post( "<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/EditData/"+$("#i_prjid").val(), 
        postdata,
        function(message) {

            if(message !=0) 
            { 
               alert(message); 
            } 
            else 
            { 
                $.ajax({
                            type:"POST",
                            url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/delete_approve/"+$("#i_prjid").val(),
                            data:false,
                            success:false
                        });
                alert('data berhasil ter-Update.')
                gridReload();
                $("#prj_form").dialog('close'); 
                  
            };
          } );
    } 
    else if (mode == "POST") 
    {
        $.post(  "<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/AddNew/", 
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
                $("#prj_form").dialog('close');
                $("#prj_app").dialog('close');        
            };  
        } );     
    }
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
        init_prj();
        
        
        $("#i_prjid").val(data.PROJECT_ID); 
        $("#i_afd").val(data.AFD); 
        $("#i_prjtype").val(data.PROJECT_TYPE); 
        $("#i_prjsubtype").val(data.PROJECT_SUBTYPE);
        $("#i_prjdesc").val(data.PROJECT_DESC); 
        $("#i_start").val(data.PROJECT_START);
        $("#i_end").val(data.PROJECT_END); 
        $("#i_stat").val(data.PROJECT_STATUS);
        
        $("#i_prjid").attr('disabled','disabled');
        $("#approval").removeAttr('disabled') ;
        
        $("#i_approv").val(data.Approved);
        $("#i_approv_by").val(data.Approved_BY);
        $("#i_approv_date").val(data.Approved_DATE);
        
        $("#prj_form").dialog('open'); 
        $("#form_mode").val("GET");      
    }
}   

//############################ END BUTTON FUNCTION ################################   
</script>

<script type="text/javascript">
//########################### START SEARCH FUNCTION #############################    
var timeoutHnd;
function gridReload()
{ 
    jQuery("#list").setGridParam
    ({url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/LoadData/"}).trigger("reloadGrid");        
} 
function doSearch(ev)
{ 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(srcReload,500) 
} 
function srcReload()
{ 
    var id = $("#search_id").val();
    var afd = $("#search_afd").val(); 
    var type = $("#search_prjtype").val(); 
    var desc = $("#search_prjdesc").val(); 

    if (id == ""){
    id = "-";
    }
    if (afd == ""){
    afd = "-";
    } 
    if (type == ""){
    type = "-";
    } 
    if (desc == ""){
    subtype = "-";
    }

    jQuery("#list").setGridParam
    ({url:"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/SearchData/"+id+"/"+afd+"/"+type+"/"+desc}).trigger("reloadGrid");        
}
//########################### END SEARCH FUNCTION #############################    
</script>
<script type="text/javascript">
//########################### START APPROVAL FUNCTION ############################# 
jQuery(document).ready(function(){
        $("#prj_app").dialog({
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
                    $("#prj_app").dialog('close');            
                },
                'Simpan ': function()
                {
                    if($("#i_ck_approve").attr('checked')==true)
                    {
                        $.post( '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/cek_approve/',
                        '' ,
                        function(message,status) 
                        { 
                            if(message>0)
                            {    
                                var ids = jQuery("#list").getGridParam('selrow'); 
                                var data = $("#list").getRowData(ids);
                                
                                $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/update_approve/'+data.PROJECT_ID,'' ,function(message,status) 
                                {
                                    alert("update approve berhasil!!!");
                                    $("#i_ck_approve").removeAttr('checked');
                                    $("#prj_app").dialog('close');
                                    $("#prj_form").dialog('close');
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
        $("#prj_app").dialog('open');
    }
}
//########################### END APPROVAL FUNCTION ############################# 
</script>
<div id"gridSearch">
    <div class="teks_"></div>  
    <div>
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr>
                <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
                <td>ID</td>
                <td>:</td>
                <td>
                <input type="text" class="input" id="search_id" maxlength="25" onkeydown="doSearch(arguments[0]||event)" />
                </td>
                <td>Afd</td>
                <td>:</td>
                <td>
                <input type="text" class="input" id="search_afd" maxlength="5" onkeydown="doSearch(arguments[0]||event)" />
                </td>
                <td>Project Type</td>
                <td>:</td>
                <td>
                <input type="text" class="input" id="search_prjtype" maxlength="25" onkeydown="doSearch(arguments[0]||event)" />
                </td>
                <td>Deskripsi</td>
                <td>:</td>
                <td>
                <input type="text" class="input" id="search_prjdesc" maxlength="25" onkeydown="doSearch(arguments[0]||event)" />
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table id="list" class="scroll"></table>
    <div id="GridPager" class="scroll" style="text-align:center;" align="center"></div>
</div>
<br>
<div id="save" class="scroll" style="float:left;">
<input type="button"  id="add" value="Tambah" onclick="TambahData()">
<input type="button"  id="edit" value="Ubah" onclick="Edit()">
<input type="button"  id="delete" value="Hapus" onclick="delData()">
</div>

<div id="prj_form">
    <table width="100%" class="teks_">
        <tr>
                <tr>
                    <td align="left" width="125">Kode Projek</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_prjid" class="input" tabindex="1" maxlength="15"/></td>
                </tr>
                <tr>
                    <td align="left">AFD</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_afd" class="input" tabindex="2" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">Tipe Projek</td>
                    <td align="left">:</td>
                    <td>
                    <select name='i_prjtype' class='select' id="i_prjtype" tabindex="6">
                        <option value=""> -- pilih -- </option>
                          <option value="KM">IF</option>
                          <option value="HM">HM</option>
                    </select></td>
                </tr>
                <tr>
                    <td align="left">Projek Subtipe</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_prjsubtype" class="input" tabindex="4" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">Deskripsi</td>
                    <td align="left">:</td>
                    <td>
                    <textarea rows="5" cols="25" id="i_prjdesc" style="height:50px; width:200px;" class="input" tabindex="5" maxlength="200"></textarea> 
                    </td>
                </tr>
                
                <tr>
                    <td align="left">Projek Start</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_start" class="input" tabindex="6" maxlength="50"/></td>
                </tr>
                <tr>
                    <td align="left">Projek End</td>
                    <td align="left">:</td>
                    <td><input type="text" id="i_end" class="input" tabindex="7" maxlength="50"/></td>
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
            <td colspan="5">
                <input type="hidden" id="form_mode">
                <input type="button" id="submitdata" value="Simpan" onclick="submit()" tabindex="8">
                <input type="button" id="approval" value="Pengesahan" onclick="approval()" tabindex="9">
            </td>
        </tr>
    </table>
</div>

<div id="prj_app">
    <table width="100%" class="teks_">
        <tr>            
            <tr>
                <td align="left" width="125">Approve</td>
                <td align="left">:</td>
                <td><input name="i_ck_approve" type="checkbox" id="i_ck_approve" tabindex="1"/></td>
            </tr>                    
            <td colspan="5">
            <hr>
            <!--<input tabindex="17" type="button" id="saveapproval" value="Simpan" onclick="" style="display:none"> -->
            </td>
        </tr>
    </table>
</div> 
</body>
