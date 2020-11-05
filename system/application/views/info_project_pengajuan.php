<?php
    header('expire-header');
    $template_path = base_url().$this->config->item('template_path');
    //$template_path = "http://localhost/lhm/public/";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>

    <link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />

</head>

<body>

<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
<script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
<script type="text/javascript">
//####### variable definition########
var gridimgpath = '<?= $template_path ?>themes/basic/images'; //definisi imagepath pada jgrid
var url = "<?= base_url().'index.php/' ?>"; //definisi url untuk jGrid 
var timeoutHnd; 
var flAuto = false; 
//var slides=0;
//###################################

jQuery(document).ready(function()
{
document.getElementById("search_afd").value = "";
document.getElementById("search_name").value = "";
document.getElementById("search_type").value = "";

$(function(){
                $('ul.jd_menu').jdMenu({    onShow: loadMenu
                                            });
                $('ul.jd_menu_vertical').jdMenu({onShow: loadMenu, onHide: unloadMenu, offset: 1, onAnimate: onAnimate});
            });

            function onAnimate(show) {
                //$(this).fadeIn('slow').show();
                if (show) {
                    $(this)
                        .css('visibility', 'hidden').show()
                            .css('width', $(this).innerWidth())
                        .hide().css('visibility', 'visible')
                    .fadeIn('normal');
                } else {
                    $(this).fadeOut('fast');
                }
            }

            var MENU_COUNTER = 1;
            function loadMenu() {
                if (this.id == 'dynamicMenu') {
                    $('> ul > li', this).remove();
            
                    var ul = $('<ul></ul>');
                    var t = MENU_COUNTER + 10;
                    for (; MENU_COUNTER < t; MENU_COUNTER++) {
                        $('> ul', this).append('<li>Item ' + MENU_COUNTER + '</li>');
                    }
                }
            }

            function unloadMenu() {
                if (MENU_COUNTER >= 30) {
                    MENU_COUNTER = 1;
                }
            }

            // We're passed a UL
            function onHideCheckMenu() {
                return !$(this).parent().is('.LOCKED');
            }

            // We're passed a LI
            function onClickMenu() {
                $(this).toggleClass('LOCKED');
                return true;
            }

    /*end menu*/
    
    
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
    

});

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



//------------- definisi colModel dan colNames -------------    
var grid_pts = null;
var colNamesT_pts = new Array(); //definisi colNames untuk jGrid
var colModelT_pts = new Array(); //definisi colModel untuk jGrid

colNamesT_pts.push('id');
colModelT_pts.push({name:'no_prj',index:'no_prj', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts.push('PROJECT ID');
colModelT_pts.push({name:'PROJECT_ID',index:'PROJECT_ID', 
editable: true,hidden:false, width: 50, align:'left'});

colNamesT_pts.push('AFD');
colModelT_pts.push({name:'AFD',index:'AFD', 
editable: true,hidden:false, width: 30, align:'center'});

colNamesT_pts.push('Tipe');
colModelT_pts.push({name:'PROJECT_TYPE',index:'PROJECT_TYPE', 
editable: true,hidden:false, width: 30, align:'center'});

colNamesT_pts.push('Subtype');
colModelT_pts.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE', 
editable: true,hidden:false, width: 100, align:'left'});

colNamesT_pts.push('Pekerjaan');
colModelT_pts.push({name:'PROJECT_DESC',index:'PROJECT_DESC', 
editable: true,hidden:true, width: 200, align:'left'});

colNamesT_pts.push('PROJECT START');
colModelT_pts.push({name:'PROJECT_START',index:'PROJECT_START', 
editable: true,hidden:false, width: 50, align:'left'});
    
colNamesT_pts.push('PROJECT END');
colModelT_pts.push({name:'PROJECT_END',index:'PROJECT_END', 
editable: true,hidden:false, width: 50, align:'left'});    

colNamesT_pts.push('Status');
colModelT_pts.push({name:'PROJECT_STATUS',index:'PROJECT_STATUS', 
editable: true,hidden:true, width: 20, align:'left'});  

//------------- end definisi colModel dan colNames -------------    
    
var loadView_pb = function()
{
    jgrid_pb = jQuery("#list").jqGrid(
    {
        url:url+'project_pengajuan/LoadData/',  //loaddata untuk jGrid ->dari controller ->ke model
        datatype: 'json', 
        mtype: 'POST', 
        colNames:colNamesT_pts,
        colModel:colModelT_pts,
        pager: jQuery('#pager'), 
        rownumbers: true, 
          rowNum: 20,
        width:1000, 
        height: 350,
        sortorder: "asc",
        forceFit : true,
        rowList:[10,20,30], 
        multiple:true,
        sortname: colNamesT_pts[1], 
        sortorder: "desc", 
        viewrecords: true, 
        //imgpath: 'themes/basic/images',
        caption: 'Deskripsi Project',
        editurl:url+'project_pengajuan/LoadData/'    
    });
    jgrid_pb.navGrid('#pager',{edit:false,del:false,add:false, search: false, refresh: true});
    //jgrid_pb.filterToolbar({stringResult: true,searchOnEnter : false});
    
    jgrid_pb.navButtonAdd('#pager',{
       caption:"Export ke Excell", 
       buttonicon:"ui-icon-add", 
       onClickButton: function(){ window.location = url+'project_pengajuan/create_excel/';},
       position:"left",
    });
    

    $("#alertmod").remove();//FIXME         
}
jQuery("#list").ready(loadView_pb);


//------------- definisi colModel dan colNames -------------    
var grid_pts2 = null;
var colNamesT_pts2 = new Array(); //definisi colNames untuk jGrid
var colModelT_pts2 = new Array(); //definisi colModel untuk jGrid

colNamesT_pts2.push('No');
colModelT_pts2.push({name:'no_prj',index:'no_prj', 
editable: true,hidden:true, width: 50, align:'center'});

colNamesT_pts2.push('PROJECT ID');
colModelT_pts2.push({name:'MASTER_PROJECT_ID',index:'MASTER_PROJECT_ID', 
editable: true,hidden:true, width: 50, align:'left'});

colNamesT_pts2.push('Pekerjaan');
colModelT_pts2.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE', 
editable: true,hidden:false, width: 150, align:'left'});

colNamesT_pts2.push('Lokasi/Afdeling');
colModelT_pts2.push({name:'PROJECT_LOCATION',index:'PROJECT_LOCATION', 
editable: true,hidden:false, width: 120, align:'left'});

colNamesT_pts2.push('PROJECT_ACTIVITY');
colModelT_pts2.push({name:'PROJECT_ACTIVITY',index:'PROJECT_ACTIVITY', 
editable: true,hidden:true, width: 100, align:'left'});

colNamesT_pts2.push('Qty');
colModelT_pts2.push({name:'PROJECT_QTY',index:'PROJECT_QTY', 
editable: true,hidden:false, width: 75, align:'left'});

colNamesT_pts2.push('Sat');
colModelT_pts2.push({name:'PROJECT_SAT',index:'PROJECT_SAT', 
editable: true,hidden:false, width: 75, align:'left'});

colNamesT_pts2.push('Biaya Satuan(Rp)');
colModelT_pts2.push({name:'PROJECT_VALUE',index:'PROJECT_VALUE', 
editable: true,hidden:false, width: 100, align:'left'});
    
colNamesT_pts2.push('Total Biaya*(Rp)');
colModelT_pts2.push({name:'TotalBiaya',index:'TotalBiaya', 
editable: true,hidden:false, width: 100, align:'left'});    
   
var loadView_detail = function()
    {   
        var ids=jQuery("#list").getGridParam('selrow');
        var data=$("#list").getRowData(ids);
        var prjID=data.PROJECT_ID;
        var urls = url+'project_pengajuan/load_detail_prj/'+prjID;
        jgrid_pb = jQuery("#list_dtl").jqGrid(
        {
            url:urls,  //loaddata untuk jGrid ->dari controller ->ke model
            datatype: 'json', 
            mtype: 'POST', 
            colNames:colNamesT_pts2,
            colModel:colModelT_pts2,
            pager: jQuery('#pager_dtl'), 
            rownumbers: true, 
            rowNum: 250,
            width:700, 
            height: 100,
            sortorder: "asc",
            forceFit : true,
            rowList:[10,20,30], 
            multiple:true,
            sortname: colNamesT_pts2[1], 
            sortorder: "desc", 
            viewrecords: true, 
            //imgpath: 'themes/basic/images',
            caption: 'Deskripsi Project',
            editurl:url+'project_pengajuan/load_detail_prj/'+prjID    
        }); 

        $("#alertmod").remove();//FIXME         
    }


$(function() 
{
    $("#form_pengajuan_project").dialog({
        bgiframe: true,
        autoOpen: false,
        height: 450,
        width: 800,
        modal: true,
        title: "Tambah Deskripsi Project",
        resizable: false,
        moveable: true,
        buttons: {
            'Tutup': function() 
            {
                init_project();   
                $("#form_pengajuan_project").dialog('close');     
            },
            'Simpan': function() 
            {
                init_project();        
            }
        } 
    }); 
});

function init_project()
{
    $("#newIdProject").val("");
    $("#newAfdProject").val("");
    $("#newPekerjaanProject").val("");
    $("#newTglStartProject").val("");
    $("#newTglEndProject").val("");
    
    $("PRJ_Pekerjaan").val("");
    $("PRJ_LokasiAfd").val("");
    $("PRJ_Qty").val("");
    $("PRJ_SAT").val("");
    $("PRJ_BiayaSatuan").val("");
    $("PRJ_TotalBiaya").val("");
}

function TambahData()
{
    $("#addDetail").attr('value','Tambah Detail');
    init_project();
    //$("#form_pengajuan_project").dialog('open');
    loadDtl();
}

function EditData()
{
    var ids=jQuery("#list").getGridParam('selrow');
    var data=$("#list").getRowData(ids);
    if(null==ids || ''==ids)
    {
        alert ("harap pilih data!!!");
    }
    else 
    {
        $("#addDetail").attr('value','Edit Detail')
        init_project();
        loadDtl(); 
    }
}
function loadDtl()
{
    var ids=jQuery("#list").getGridParam('selrow');
    var data=$("#list").getRowData(ids);
    
    jQuery("#list_dtl").setGridParam
    jQuery("#list_dtl").ready(loadView_detail);
    $("#detailProject").attr('style','display:none'); 
     
    $("#form_pengajuan_project").dialog('open');//open detail form
    
    var prjID=data.PROJECT_ID;
    var urls = url+'project_pengajuan/load_detail_prj/'+prjID;
    jQuery("#list_dtl").setGridParam({url:urls}).trigger("reloadGrid");
}


function trim(str)
{
    return str.replace(/^\s+|\s+$/g,'');
}
function doSearch(ev){ 
    
    // var elem = ev.target||ev.srcElement; 
    
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
}
function gridReload()
{ 
    var afd = jQuery("#search_afd").val(); 
    var name = jQuery("#search_name").val(); 
    var type = jQuery("#search_type").val(); 

    if (trim(afd) == "")
    {
        afd = "-";
    } 
    if (trim(name)=="")
    {
        name = "-";
    }
    if (trim(type)=="")
    {
        type = "-";
    }
    
     var urls = url+"project_pengajuan/search_prj/"+afd+"/"+name+"/"+type;
     jQuery("#list").setGridParam({url:url+"project_pengajuan/search_prj/"+afd+"/"+name+"/"+type}).trigger("reloadGrid");
    /*if (regExpIs_valid(afd)==true && regExpIs_valid(name)==true && regExpIs_valid(type)==true)
    {
        var urls = url+"project_pengajuan/search_prj/"+afd+"/"+name+"/"+type;
        jQuery("#list").setGridParam({url:urls}).trigger("reloadGrid");
    }
    else
    {
        alert ("invalid input");
    }*/
            
}
function regExpIs_valid(text)
{
   var pattern= new RegExp(/^[a-z0-9A-Z-\s]+$/);
   return pattern.test(text);
}

$(document).ready(function(){
    $("#addDetail").click(function(){
        $("#detailProject").slideDown('slow');
       // $("#detailProject").attr('style','display:inline');
        /*if (0==slides)
        {
            $("#detailProject").slideUp('slow');
            slides=1;
        }
       else if (1==slides)
        {
            $("#detailProject").slideDown('slow');
            slides=0;
        }*/
    });
});
    
$(function() 
{
    $("#newTglStartProject").datepicker({dateFormat:"yy-mm-dd"});
    $("#newTglEndProject").datepicker({dateFormat:"yy-mm-dd"});
});     
</script>


<div class="teks_headline"><strong><?php echo $company_dest;?><br>Pengajuan Project<br/></strong></div>
<hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
<div style="float:right">
<ul class="jd_menu jd_menu_slate">
        <li><a href="#" class="accessible">Transaksi</a>
            <ul>
                <li><a href="<?= base_url()?>index.php/m_gang_activity_detail">Laporan harian mandor</a></li>
                <li><a href="<?= base_url()?>index.php/p_machine">Buku mesin</a></li>
                <li><a href="<?= base_url()?>index.php/p_vehicle_activity">Buku Kendaraan</a></li>
                <li><a href="<?= base_url()?>index.php/p_workshop_activity">Buku Workshop</a></li>
                <hr>    
                <li><a href="#">Entry Progress</a>
                    <ul>
                        <li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_panen">Entry Progress Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_tp">Entry Progress Transport Panen</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_sisip">Entry Progress Sisip</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_bibitan">Entry Progress Bibitan</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_tanam">Entry Progress Tanam</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_rawat_if">Entry Progress Rawat Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_infrastruktur">Entry Progress Project Infrastruktur</a></li>
                        <li><a href="<?= base_url()?>index.php/p_progress_pj_bibitan">Entry Progress Project Bibitan</a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url()?>index.php/rpt_absensi">Absensi Karyawan</a></li>
                <!-- <li><a href="<?= base_url()?>index.php/p_progress_rawat">Entry Progress Rawat</a></li>
                <li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li> -->
            </ul>
        </li>
        <?php if( $user_level == 'SAD' || $user_level == 'ADM') { ?>
        <li><a href="#" class="accessible">Master Data</a>
            <ul>
                <li><a href="<?= base_url()?>index.php/m_employee">Karyawan</a></li>
                <li><a href="<?= base_url()?>index.php/m_gang">Kemandoran</a></li>
                <li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li>
                <li><a href="<?= base_url()?>index.php/m_vehicle">Kendaraan</a></li>
                <li><a href="<?= base_url()?>index.php/m_Machine">Mesin</a></li>
                <li><a href="<?= base_url()?>index.php/m_bloktanam">Blok Tanam</a></li>
                <li><a href="<?= base_url()?>index.php/m_workshop">Workshop</a></li>
                <li><a href="<?= base_url()?>index.php/m_nursery">Nursery</a></li>
                <li><a href="<?= base_url()?>index.php/m_infras">Infrastruktur</a></li>
                <li><a href="<?= base_url()?>index.php/m_user">User</a></li>
            </ul>
        </li>
        <?php } ?>
        <?php if( $user_level == 'SAD' || $user_level == 'ADM') { ?>
        <li><a href="#" class="accessible">Project</a>
            <ul>
                <li><a href="#">Pengajuan Project</a></li>
                <li><a href="#">Pengajuan Revisi Project</a></li>
            </ul>
        </li>
        <?php } ?>
        <?php if( $user_level == 'SAD' ) { ?>
        <li><a href="#" class="accessible" style="height:20px;">Reporting</a>
            <ul style="width: 200px;">
                                
                    <li><a href="#">Daftar Upah </a>
                        <ul>
                            <li><a href="<?= base_url()?>index.php/rpt_du">Daftar Upah Per Kemandoran</a></li>
                             <li><a href="<?= base_url()?>index.php/rpt_du/du_afd">Daftar Upah Per Divisi / Bagian</a></li>
                              <li><a href="<?= base_url()?>index.php/rpt_du_act">Daftar Upah Per Aktivitas</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Berita Acara</a>
                        <ul>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat">Berita Acara Gaji Rawat</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_panen">Berita Acara Gaji Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_transportpanen">Berita Acara Gaji Transport Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_bibitan">Berita Acara Gaji Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_sisip">Berita Acara Gaji Sisip</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjtanam">Berita Acara Hasil Kerja Project Tanam</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjbibitan">Berita Acara Hasil Kerja Project Persiapan Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjinfrastruktur">Berita Acara Gaji Project Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat_infrastruktur">Berita Acara Rawat Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_umum">Berita Acara Gaji Umum</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_vmw">Berita Acara Gaji Kendaraan, Workshop, Mesin</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_tunpot">Berita Acara Tunjangan dan Potongan</a></li>
                    <hr />
                    <li><a href="#">Komparasi DU dan BA</a>
                     <ul>
                        <li><a href="<?= base_url()?>index.php/rpt_rekonbadu">Rekonsiliasi BA & DU</a></li>
                     </ul>
                    </li>
                        </ul>
                    </li>
                    <li><a href="<?= base_url()?>index.php/rpt_progress/progress">Progress</a>
                    </li>    
                <hr>
                    <li><a href="<?= base_url()?>index.php/rpt_lhm">Export Data</a>
                    </li>        
            </ul>
            
        </li>
        <?php } ?>
        <li style="float:right;">&nbsp;&nbsp;&nbsp;Logged as, <?php echo $login_id; ?> &nbsp; | &nbsp; <a href="<?= base_url()?>index.php/login/Dologout">Logout</a> </li>
    </ul>
</div>



<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
    <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
    <tr>
    <td>AFD</td>
    <td>:</td>
    <td>
    <input type="text" class="input" id="search_afd" onkeydown="doSearch(arguments[0]||event)" />
    </td>

    <td style="padding-left:15px;"></td>
    <td>projectType</td>
    <td>:</td>
    <td><input type="text" class="input" id="search_type" onkeydown="doSearch(arguments[0]||event)" /></td>

    <td style="padding-left:15px;"></td>
    <td>Subtype</td>
    <td>:</td>
    <td><input type="text" class="input" id="search_name" onkeydown="doSearch(arguments[0]||event)" /></td>

    </tr>
    </table>

    <table id="list" class="scroll" border="1"></table> 
    <div id="pager" class="scroll" style="text-align:center;" align="center"></div>

    <div id="save" style="margin-left: 841px;">
        <input type="button"  id="add" value="Tambah" onclick="TambahData()">
        <input type="button"  id="edit" value="Ubah" onclick="EditData()">
        <input type="button"  id="delete" value="Hapus" onclick="delData()">
    </div>
</div>



<div id="form_pengajuan_project">
    <div id="newProject" style="margin-bottom: 15px;">
        <table  width="100%" border="1">
            <tr>
                <td style="font-size: 11px;">ID Project</td>
                <td>:</td>
                <td><input type="text" class="input" style="font-size: 11px; width: 50px;" id="newIdProject"   /></td>
            </tr>
            <tr>
                <td style="font-size: 11px;">Lokasi/afdeling</td>
                <td>:</td>
                <td ><input type="text" class="input" style="font-size: 11px; width: 100px;" id="newAfdProject"   /></td>
            </tr>
            <tr>
                <td style="font-size: 11px;" >Pekerjaan</td>
                <td>:</td>
                <td><input type="text" class="input" style="font-size: 11px; width: 230px;" id="newPekerjaanProject"   /></td>
                
            </tr>
            <tr>
                <td style="font-size: 11px;" >Tanggal Mulai</td>
                <td>:</td>
                <td><input type="text" class="input" style="font-size: 11px; width: 80px;" id="newTglStartProject"/>
                Tanggal Selesai :<input type="text" class="input" style="font-size: 11px; width: 80px;" id="newTglEndProject"   />
                </td>
                
            </tr>
            <tr>
                <td colspan="3"><input type="button"  id="addDetail" value="Tambah Detail"></td>
            </tr>
        </table>
    </div>
   
    <div id="detailProject" style="display: none;">
        <strong>DETAIL PROJECT</strong> 
        <table border="1" width="100%" style="border-collapse: collapse;">
            <tr>
                <td style="font-size: 11px;">Pekerjaan</td>
                <td>:</td>
                <td><input type="text" class="input" style="font-size: 11px; width: 230px;" id="PRJ_Pekerjaan"  name="PRJ_Pekerjaan" /></td>
            </tr>
            <tr>
                <td style="font-size: 11px;">Lokasi/afdeling</td>
                <td>:</td>
                <td><input type="text" class="input" style="font-size: 11px; width: 200px;" id="PRJ_LokasiAfd"  name="PRJ_LokasiAfd" /></td>
            </tr>
            <tr>
                <td style="font-size: 11px;">Qty</td>
                <td>:</td>
                <td><input type="text" style="font-size: 11px; width: 75px;" class="input" id="PRJ_Qty"  name="PRJ_Qty" /></td>
            </tr>
            <tr>
                <td style="font-size: 11px;" >SAT</td>
                <td>:</td>
                <td style="font-size: 11px;"><input type="text" id="PRJ_SAT" style="font-size: 11px; width: 75px;" class="input"></td>
            </tr> 
            <tr>
                <td style="font-size: 11px;" >Biaya Satuan(Rp)</td>
                <td>:</td>
                <td style="font-size: 11px;"><input type="text" id="PRJ_BiayaSatuan" style="font-size: 11px; width: 130px;" class="input"></td>
            </tr>
            <tr>
                <td style="font-size: 11px;" >Total Biaya*(Rp)</td>
                <td>:</td>
                <td style="font-size: 11px;">
                <input type="text" class="input" style="font-size: 11px; width: 130px;" name="PRJ_TotalBiaya" id="PRJ_TotalBiaya" >
                
                </td>

                <!--<td style="font-size: 11px;" >Total Biaya*(Rp)</td>
                <td>:</td>
                <td style="font-size: 11px;"><textarea class="input" style="font-size: 11px; width: 100px;" name="remark" id="REMARK" cols=18 rows=2></textarea></td>-->
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><input type="button"  id="insertDetail" value="Tambah"></td>
            </tr>
            <tr>
            <td colspan="3">
                <div style="margin-left: 10px; width: 50%;" id="detGrid">
                    <table id="list_dtl" class="scroll"></table> 
                    <div id="pager_dtl" class="scroll" style="text-align:center;" align="center"></div>
                </div>
            </td>
            </tr>
        </table>
    </div>
    
</div>
</body>
