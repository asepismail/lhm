<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td class="labelcell">Periode</td><td class="labelcell">:</td><td class="fieldcell"><? echo $periode; ?></td>
            </tr>
            <tr>
                <td class="labelcell">AFD</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="2" class="input" type="text" id="txt_afdSrc" name="knt_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td> 
            </tr>
            <tr style=" display: none;">
                <td class="labelcell">Tanggal</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="2" class="input" type="hidden" id="txt_blockSrc" name="knt_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td> 
            </tr>
        </table>       
    </div>

    <div id='transaction_frame'>
    
        <div id="mainGrid">
            <table id="list_curah_hujan" class="scroll"></table> 
            <div id="pager_curah_hujan" class="scroll" style="text-align:center;"></div>
        </div>
        
        <div id="btn_section">
           <button class="testBtn" type="submit" id="hapus" onclick="hapus()">Hapus</button> 
        </div>
    </div>
</div> 

<div id="frm_curah_hujan">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Kontraktor</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">AFD</td><td>:</td>
                    <td class="fieldcell"><input style="width: 150px;" tabindex="1" type="text" id="txt_AFD" name="curah_hujan_input" maxlength="50"  disabled="disabled"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Tanggal</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_tanggal" name="curah_hujan_input" maxlength="100" disabled="disabled"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Curah Hujan</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_curah_hujan" name="curah_hujan_input" maxlength="100"/></td>
                </tr>
                
                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_newkontraktor" onclick="update_curah_hujan()">Simpan</button>    
        </div>
        <div id="fragment-2">
            
            <div id="kendaraanGrid">
                <table id="list_Kendaraan" class="scroll"></table> 
                <div id="pager_Kendaraan" class="scroll" style="text-align:center;"></div>
            </div>
        </div>
        <div id="fragment-3">
            
        </div>
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_load">
    Wait...
</div>

		<div id="myForm">     
            Perusahaan : </span><?php echo $company_dest; ?> <p />
            <span>Periode Curah Hujan : </span><?php echo $periode;?> <p />
            <p>
			*Format upload CSV : AFD, TANGGAL, CURAH_HUJAN, COMPANY_CODE 
            </p>
    	<div>
  		<input type="file" size="60" name="myfile" id="myfile">
        <button id="bUpload" type="submit">Mulai Upload Data</button> 
        </div>
        
        <div id="progress">
            <img class='loading' src='<?= base_url() ?>public/themes_pms/img/loader14.gif' alt='loading...' />
        </div>
        
        <div id="message">
        </div>
       
        
        <input id="upStatus" type="hidden" value="0">
</div>  
<div id="search_form"></div>
</body>

<script type="text/javascript">
var that = this;
$(function(){
    $("#txt_afdSrc").val('');  
});

jQuery(document).ready(function(){
    $('#tahun').change(function() {
        reloadGrid();
    });
    
    $('#bulan').change(function() {
        reloadGrid(); 
    });
});

jQuery(document).ready(function(){
   $( "#dialog:ui-dialog" ).dialog( "destroy" );
   
   $('input').val('');
    
   $("#frm_load").dialog({
        dialogClass : 'dialog1',
        autoOpen: false,
        height: 100,
        width: 200,
        modal: true
   });
   
   $("#search_form").dialog({
        bgiframe: true,
        dialogClass : 'dialog1',
        autoOpen: false,
        width: 530,
        modal: true,
        position: 'center',
        onShow: function(dlg){
            $(dlg.container).css('height','auto')
        },
        close: function(event, ui){
            $(this).dialog('destroy');
            //setDialogWindows('#search_form');
        },
        open:function(){
            jQuery("#list_curah_hujan").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_curah_hujan/search_data_2');
        }
    });
   
   $(function() {
		 $("#myForm").dialog({
			bgiframe: true, autoOpen: false, height: 300, width: 550,
			modal: true, title: "Upload data master Curah Hujan", resizable: false, 
			moveable: true, closeOnEscape: false,
			open: function() { $(".ui-dialog-titlebar-close").hide(); },
			buttons: { 'Tutup': function() {
						  $("#myForm").dialog("close");     
				   }
			   } 
		 }); 
	});
   
   jQuery( "#bUpload" ).click(function() {
		  startUpload();
	  });
   
   $("#frm_curah_hujan_set").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 150,
        width: 300,
        modal: true,
        title: "Penetapan Periode curah hujan",
        resizable: true,
        moveable: true,
        buttons: {
            'Set Periode': function() 
                    {
                        set_curah_hujan_periode_do();        
                    }
           
        } 
    });
    
   $("#frm_curah_hujan").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "curah hujan",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        clear_form_elements(this.form);
						$("#frm_curah_hujan").dialog('close');        
                    }
            
        } 
    });
    
    $("#txt_afdSrc").autocomplete( 
            url+"m_curah_hujan/get_afdeling", {
              dataType: 'ajax',
              width:350,
              multiple: false,
              limit:20,
              parse: function(data) { // parsing json input
                  return $.map(eval(data), function(row) {
                  return (typeof(row) == 'object')
                    ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                    : { data: row, value: '',result: ''};
                });
              },
              formatItem: function(item) {
                return (typeof(item) == 'object')?item.res_dl :'';
              }
            }
    ).result(function(e, item){
            //$("#i_nik_natura").val(item.res_id);
            //$("#i_nama_natura").val(item.res_name );
    });
    
    $("#txt_AFD").autocomplete( 
            url+"m_curah_hujan/get_afdeling", {
              dataType: 'ajax',
              width:350,
              multiple: false,
              limit:20,
              parse: function(data) { // parsing json input
                  return $.map(eval(data), function(row) {
                  return (typeof(row) == 'object')
                    ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                    : { data: row, value: '',result: ''};
                });
              },
              formatItem: function(item) {
                return (typeof(item) == 'object')?item.res_dl :'';
              }
            }
    ).result(function(e, item){

    });
    jQuery("#txt_tanggal").datepicker({dateFormat:"yy-mm-dd"});	
    $("#txt_tanggal").keydown(function(){
		
    });
    
    
     
});
</script>
 
<script type="text/javascript">
$(document).ready(function() {
    $('input').val('');
});

function startUpload(){
	var status = $("#upStatus").val();
	var theFile = $("#myfile").val();  

  	if (status == 0){
		$("#message").html("Mohon menunggu proses upload data sedang berlangsung");
		$("#message").show();
		return ajaxFileUpload();
	}else{
		$("#bUpload").removeAttr("disabled");
		$("#progress").hide();
		$("#message").hide();
	}	
}

function ajaxFileUpload(){
 	$("#loading")
	$("#progress").ajaxStart(function(){
		  $(this).show();
	  }).ajaxComplete(function(){
		  $(this).hide();
	  });
	  

		$.ajaxFileUpload
		(
			{
				url:url+'m_curah_hujan/do_import/',
				secureuri:false,
				fileElementId:'myfile',
				dataType: 'json',
				data:{name:'logan', id:'id'},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
						  alert(data.msg);
						  $("#message").html(data.error);
						  $("#message").show();
						  $("#progress").hide();
						}else
						{
							alert(data.msg);
							$("#message").html(data.error);
						  $("#message").show();
						  $("#progress").hide();
						}
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		
		return false;
}	
  
function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#list_curah_hujan").setGridParam({url:url+'m_curah_hujan/LoadData/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
}

function clear_form_elements(ele) {
    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
    $("#frm_nota").dialog('close');
}
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
</script>

<script type="text/javascript">  
var url = "<?= base_url().'index.php/' ?>";  
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var gridimgpath = '<?= $template_path ?>themes/basic/images';
 
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_CURAH_HUJAN');
colModelT.push({name:'ID_CURAH_HUJAN',index:'ID_CURAH_HUJAN', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('AFD');
colModelT.push({name:'AFD',index:'AFD', editable: true,width: 50
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"m_bjr/get_afdeling", {
                      dataType: 'ajax',
                      multiple: false,
                      autoFill: false,
                      mustMatch: true,
                      matchContains: false,
                
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },
                      formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl :'';
                      }
                    }
                  )
                  .result(function(e, item) {
                    $("#BLOCK").val(item.res_name );
                  });
          }}, width: 70, align:'center'}); 

colNamesT.push('TANGGAL');
colModelT.push({name:'TANGGAL',index:'TANGGAL', editable: true, hidden:false, width: 75, align:'center', formatter:'date:yyyy-mm-dd'}); 

colNamesT.push('CURAH HUJAN');
colModelT.push({name:'CURAH_HUJAN',index:'CURAH_HUJAN', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_curah_hujan").jqGrid(
            {
                url:url+'m_curah_hujan/LoadData/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_curah_hujan"),
                rowNum: 100,
                rownumbers: true,
                height: 300,
                width: 625,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
				afterEditCell: function (id,name,val,iRow,iCol){             
                     if(name=='TANGGAL') 
                        { jQuery("#"+iRow+"_TANGGAL","#list_curah_hujan").datepicker({dateFormat:"yy-mm-dd"}); } 
                },
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_curah_hujan").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

                            jQuery("#list_curah_hujan").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_curah_hujan',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_curah_hujan',{
               caption:"Tambah", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         jGrid_va.navButtonAdd('#pager_curah_hujan',{
               caption:"Cari", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
               		$("#search_form").dialog('open');
               }, 
               position:"left"
            });
		 jGrid_va.navButtonAdd('#pager_curah_hujan',{
			   caption:"Upload", buttonicon:'ui-icon-newwin',
	   		   onClickButton: function(){  
			   		OpenUploadForm(); 
				}, position:"left" });            
         }
jQuery("#list_curah_hujan").ready(loadView); 
</script>
 

<script type="text/javascript">
function OpenUploadForm(){
	$("#progress").hide();
	$("#myForm").dialog("open");
}
  
function get_bulan(){
    var lPeriode = $("#bulan").val();
    return lPeriode;    
}

function get_tahun(){
    var lPeriode = $("#tahun").val();
    return lPeriode;    
}

function giveLocType(){        
    var ids = jQuery("#list_curah_hujan").getGridParam('selrow'); 
    var rets = jQuery("#list_curah_hujan").getRowData(ids); 
    var type = rets.AFD;
    return type;
}

function getAfdType(){
    var afd = $("#txt_curah_hujanAFD").val();
    return afd;
}

function addrow(){
    var rowCount = $("#list_curah_hujan").getGridParam("reccount");
    var i;
    if(rowCount==null || rowCount==0){
        i=i+1;    
    }else{
        i=rowCount+1;
    }
        
    var datArr = {};
    if (i>1){
        var datArr = {ID_BJR:jdesc1};
    }
    

    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_curah_hujan("+i+")'; />"; 
    var su=jQuery("#list_curah_hujan").addRowData(i,datArr,'last');
    var act=jQuery("#list_curah_hujan").setRowData(i,{act:sv});  
}
</script>

<script type="text/javascript">
function update_curah_hujan(){
    var postdata_id = {};
 
    postdata_id['AFD'] =  $("#txt_AFD").val();
    postdata_id['TANGGAL'] =  $("#txt_tanggal").val();
    postdata_id['CURAH_HUJAN'] = $("#txt_curah_hujan").val();
    postdata_id['CRUD'] =   $("#txt_frmMode").val();

    var data = {
                  id:postdata_id
                };
    data = JSON.stringify(data);
    $.ajax({
            type:           'post',
            cache:          false,
            url:            url+'m_curah_hujan/CRUD_METHOD',
            data:           {myJson:  data} ,
            success: function(msg){
                var obj = jQuery.parseJSON(msg);    
                if(obj.error==false){				
                    alert(obj.status)
                    $("#frm_curah_hujan").dialog('close');
					reloadGrid();  
                }else{
                    alert(obj.status)
                    $("#frm_curah_hujan").dialog('close');                    
                }
            }
    });    
}

function hapus_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_curah_hujan").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Data Curah Hujan untuk AFD : " + data.AFD + ", Tanggal: " + data.TANGGAL + "?" )
        if (answer){
            $('#frm_load').dialog('open');
            var postdata_id = {};
    
            postdata_id['AFD'] =  data.AFD;
            postdata_id['TANGGAL'] =  data.TANGGAL;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_curah_hujan/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error==false){
                            alert(obj.status)
                            $('#frm_load').dialog('close');
                            $("#frm_curah_hujan").dialog('close');
							reloadGrid(); 
                        }else{
                            alert(obj.status)
                            $('#frm_load').dialog('close');
                            $("#frm_curah_hujan").dialog('close');                             
                        }
                    }
            });        
        }
        
    }     
}

function update_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_curah_hujan").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $("#txt_AFD").val(data.AFD);
        $("#txt_tanggal").val(data.TANGGAL);
        $("#txt_curah_hujan").val(data.CURAH_HUJAN);
        
        $("#txt_frmMode").val("EDIT");
     
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_curah_hujan").dialog('open');
    } 
}

function simpan_curah_hujan(cl){
    var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    var data = $("#list_curah_hujan").getRowData(ids) ;
    jQuery('#list_curah_hujan').saveCell(ids);
    $('#frm_load').dialog('open');
    var postdata_id = {};
    
    postdata_id['AFD'] =  data.AFD;
    postdata_id['TANGGAL'] =  data.TANGGAL;
    postdata_id['CURAH_HUJAN'] = data.CURAH_HUJAN;
    postdata_id['CRUD'] =  'ADD';

    var data = {
                  id:postdata_id
                };
    data = JSON.stringify(data);
    $.ajax({
            type:           'post',
            cache:          false,
            url:            url+'m_curah_hujan/CRUD_METHOD',
            data:           {myJson:  data} ,
            success: function(msg){
                var obj = jQuery.parseJSON(msg);   
                if(obj.error==false){
                    alert(obj.status);
                    $('#frm_load').dialog('close');
                    $("#frm_curah_hujan").dialog('close');
					reloadGrid(); 
                }else{
                    alert(obj.status);
                    $('#frm_load').dialog('close');
                    $("#frm_curah_hujan").dialog('close');
                }  
            }
    });

}
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 
    // var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    var afd = jQuery("#txt_afdSrc").val();
    var block = jQuery("#txt_blockSrc").val();  

    if (afd == ""){
        afd = "-";
    }
    if (block == ""){
        block = "-";
    } 

    jQuery("#list_curah_hujan").setGridParam({url:url+"m_curah_hujan/search_data/"+get_bulan()+"/"+get_tahun()+"/"+afd+"/"+block}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">
/*
function set_bjr_periode(){
    clear_form_elements(this.form)
    $("#frm_bjr_set").dialog('open');    
}
*/
function hapus(){
	var postdata_detail = {};
	var postdata_id = {};
    var i=0;
	var afd = "";

	s = jQuery("#list_curah_hujan").jqGrid('getGridParam','selarrrow');
	if (s == ""){
		alert ("Data curah hujan yang akan di hapus belum dipilih");
	}else{
		postdata_id['CRUD'] =  'DEL_ALL';
		$.each(s, function(n, rowid){ 
			var str = jQuery("#list_curah_hujan").jqGrid('getGridParam','selarrrow');
			//alert(str[i]);
			var data = $("#list_curah_hujan").getRowData(str[i]) ;		
			i=i+1;
			postdata_detail['ID_CURAH_HUJAN'+i] = data.ID_CURAH_HUJAN;
			postdata_detail['AFD'+i] = data.AFD;
			postdata_detail['TANGGAL'+i] = data.TANGGAL;
			afd = data.AFD +"-" +data.TANGGAL + " "+afd;
			//alert (blok);
			//postdata_detail['VALUE'+i] = data.VALUE;	
			
			});
		var answer = confirm  ("Yakin anda akan menghapus data curah hujan untuk AFD : " + afd + " ?");

		if (answer){
			var data = {id:postdata_id, detail:postdata_detail};
        	data = JSON.stringify(data);
        	$.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_curah_hujan/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error==false){
						alert(obj.status);
						$('#frm_load').dialog('close');
						reloadGrid(); 
                    }else{
                        alert(obj.status)
                        //reloadGrid();
                        $("#frm_load").dialog('close');
                        //$("#frm_curah_hujan").dialog('close');    
                    }    
                    
                },
                error:function (xhr, ajaxOptions, thrownError){
                        alert(xhr.status);
                        alert(thrownError);
                }    
			});
		}// end answer 
	}
}

/*
function set_bjr_periode_do(){
    var answer = confirm ("Tetapkan Periode BJR ? " )
    if (answer){
        var answer_2 = confirm ("semua perhitungan hasil timbangan akan menggunakan referensi BJR periode: "+$("#tahun_bjr").val()+$("#bulan_bjr").val() )
        if (answer){
            var bjr_periode = $("#tahun_bjr").val()+$("#bulan_bjr").val();
            $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_bjr/set_bjr_periode/'+bjr_periode,
                success: function(msg){
                    alert(msg);
                    $("#frm_bjr_set").dialog('close'); 
                }
            });
        }
        
    }    
}
*/
</script>