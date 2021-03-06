<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<br>
<div id='main_form'>
    <div id"gridSearch">  
        <!--<div><?php //echo $search; ?></div> -->
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
                <td class="labelcell">BLOCK</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input tabindex="2" class="input" type="hidden" id="txt_blockSrc" name="knt_input" maxlength="100" onkeydown="doSearch(arguments[0]||event)"/>
                </td> 
            </tr>
            
            <!--<tr>
                <td>Base BJR periode yang digunakan</td><td>:</td>
                <td><?php echo $bjr_periode; ?></td>
            </tr>-->   
        </table>       
    </div>

    <div id='transaction_frame'>
    
        <div id="mainGrid">
            <table id="list_bjr" class="scroll"></table> 
            <div id="pager_bjr" class="scroll" style="text-align:center;"></div>
        </div>
        
        <div id="btn_section">
           <!-- <button class="testBtn" type="submit" id="set_bjrPeriode" onclick="set_bjr_periode()">Set BJR Periode</button>&nbsp; -->
           <button class="testBtn" type="submit" id="approve" onclick="approve()">Approve</button> 
        </div>
    </div>
</div> 

<div id="frm_bjr">
    <div id="tabs">
        <ul class="tabs">
            <li><a href="#fragment-1"><span>Detail Kontraktor</span></a></li>
            <!--<li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
            <li><a href="#fragment-3"><span>History</span></a></li> -->
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
                <tr>
                    <td class="labelcell">AFD</td><td>:</td>
                    <td class="fieldcell"><input style="width: 150px;" tabindex="1" type="text" id="txt_bjrAFD" name="bjr_input" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell">BLOCK</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" id="txt_bjrBlock" name="bjr_input" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell">Value BJR</td><td>:</td>
                    <td class="fieldcell"><input tabindex="3" type="text" id="txt_bjrValue" name="bjr_input" maxlength="100"/></td>
                </tr>
                
                
            </table>
            <br>
            <button class="testBtn" type="submit" id="cmd_newkontraktor" onclick="update_bjr()">Simpan</button>    
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

<div id="frm_bjr_set">

    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
        <tr><td colspan="8">Pilih Periode BJR :</td></tr>
        <tr>
            <td>Periode</td><td>:</td><td><? echo $periode_bjr; ?></td>
        </tr> 
    </table>
    
</div>
<div id="frm_load">
    Wait...
</div>

		<div id="myForm">     
            Perusahaan : </span><?php echo $company_dest; ?> <p />
            <span>Periode BJR : </span><?php echo $periode;?> <p />
            <p>
			*Format upload CSV : BLOCK, VALUE 
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
</body>

<script type="text/javascript">
var that = this;
$(function(){
    $("#txt_afdSrc").val('');
    //$("#txt_blockSrc").val('');     
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
            setDialogWindows('#search_form');
        },
        open:function(){
            jQuery("#list_bjr").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_bjr/search_data_2');
        }
    });
   
   $(function() {
		 $("#myForm").dialog({
			bgiframe: true, autoOpen: false, height: 300, width: 550,
			modal: true, title: "Upload data master BJR", resizable: false, 
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
   
   $("#frm_bjr_set").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 150,
        width: 300,
        modal: true,
        title: "Penetapan Periode BJR",
        resizable: true,
        moveable: true,
        buttons: {
            'Set Periode': function() 
                    {
                        set_bjr_periode_do();        
                    }
           
        } 
    });
    
   $("#frm_bjr").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "BJR",
        resizable: true,
        moveable: true,
        buttons: {
            Tutup: function() 
                    {
                        clear_form_elements(this.form);        
                    }
            
        } 
    });
    
    $("#txt_afdSrc").autocomplete( 
            url+"m_bjr/get_afdeling", {
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
    
    $("#txt_bjrAFD").autocomplete( 
            url+"m_bjr/get_afdeling", {
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
    
    $("#txt_bjrBlock").keydown(function(){
        var AFD = $("#txt_bjrAFD").val();
        $("#txt_bjrBlock").autocomplete( 
                url+"m_bjr/get_block/"+AFD, {
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
            )
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
			  //$("#progress").show();
		$("#message").show();
		return ajaxFileUpload();
			  //$("#bUpload").attr("disabled", true);
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
				url:url+'m_bjr/do_import/'+get_bulan()+'/'+get_tahun(),
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
     jQuery("#list_bjr").setGridParam({url:url+'m_bjr/LoadData/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
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
   
//var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';   
colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT.push('ID_BJR');
colModelT.push({name:'ID_BJR',index:'ID_BJR', editable: false, hidden:true, width: 140, align:'left'});

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

colNamesT.push('BLOCK');
colModelT.push({name:'BLOCK',index:'BLOCK', editable: true ,width: 140
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete(
                        url+"m_bjr/get_block/"+giveLocType(), {
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
                      ).result(function(e, item) {
                        //$("#i_txt_block").val(item.res_id);
                        //$("#i_nama_natura").val(item.res_name );
                      });    
          }}, width: 70, align:'center'});

colNamesT.push('BJR Value');
colModelT.push({name:'VALUE',index:'VALUE', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('STATUS');
colModelT.push({name:'STATUS',index:'STATUS', editable: false, hidden:false, width: 100, align:'center'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_bjr").jqGrid(
            {
                url:url+'m_bjr/LoadData/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_bjr"),
                rowNum: 100,
                rownumbers: true,
                height: 300,
                width: 625,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_bjr").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

                            jQuery("#list_bjr").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_bjr',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_bjr',{
               caption:"Tambah", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         jGrid_va.navButtonAdd('#pager_bjr',{
               caption:"Cari", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
               		$("#search_form").dialog('open');
               }, 
               position:"left"
            });
		 jGrid_va.navButtonAdd('#pager_bjr',{
			   caption:"Upload", buttonicon:'ui-icon-newwin',
	   		   onClickButton: function(){  
			   		OpenUploadForm(); 
				}, position:"left" });            
         }
jQuery("#list_bjr").ready(loadView); 
</script>
 

<script type="text/javascript">
function OpenUploadForm(){
	/*
	$("#myfile").val("");
	$("#message").hide();
	
	$("#company2").val( $("#company1").val() );
	$("#tahun2").val( $("#tahun1").val() );
	$("#company2").attr("disabled", true);
	$("#tahun2").attr("disabled", true);
	*/
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
    var ids = jQuery("#list_bjr").getGridParam('selrow'); 
    var rets = jQuery("#list_bjr").getRowData(ids); 
    var type = rets.AFD;
    return type;
}

function getAfdType(){
    var afd = $("#txt_bjrAFD").val();
    return afd;
}

function addrow(){
    var rowCount = $("#list_bjr").getGridParam("reccount");
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
    

    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_bjr("+i+")'; />"; 
    var su=jQuery("#list_bjr").addRowData(i,datArr,'last');
    var act=jQuery("#list_bjr").setRowData(i,{act:sv});  
}
</script>

<script type="text/javascript">
function update_bjr(){
    var postdata_id = {};
    
    postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
    postdata_id['AFD'] =  $("#txt_bjrAFD").val();
    postdata_id['BLOCK'] =  $("#txt_bjrBlock").val();
    postdata_id['VALUE'] = $("#txt_bjrValue").val();
    postdata_id['CRUD'] =   $("#txt_frmMode").val();

    var data = {
                  id:postdata_id
                };
    data = JSON.stringify(data);
    $.ajax({
            type:           'post',
            cache:          false,
            url:            url+'m_bjr/CRUD_METHOD',
            data:           {myJson:  data} ,
            success: function(msg){
                var obj = jQuery.parseJSON(msg);    
                if(obj.error===true){
                    alert(obj.status)
                    $("#frm_bjr").dialog('close');
                }else{
                    alert(obj.status)
                    $("#frm_bjr").dialog('close');
                    reloadGrid();  
                }
            }
    });    
}

function hapus_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_bjr").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        var answer = confirm ("Hapus Data BJR untuk AFD : " + data.AFD + ", dan BLOCK: " + data.BLOCK + ", untuk periode yang dipilih ?" )
        if (answer){
            $('#frm_load').dialog('open');
            var postdata_id = {};
    
            postdata_id['BULAN'] = $("#bulan").val();
            postdata_id['TAHUN'] = $("#tahun").val();
            postdata_id['AFD'] =  data.AFD;
            postdata_id['BLOCK'] =  data.BLOCK;
            postdata_id['CRUD'] =  'DEL';

            var data = {
                          id:postdata_id
                        };
            data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_bjr/CRUD_METHOD',
                    data:           {myJson:  data} ,
                    success: function(msg){
                        var obj = jQuery.parseJSON(msg);    
                        if(obj.error===true){
                            alert(obj.status)
                            $('#frm_load').dialog('close');
                            $("#frm_bjr").dialog('close');
                        }else{
                            alert(obj.status)
                            $('#frm_load').dialog('close');
                            $("#frm_bjr").dialog('close');
                            reloadGrid();  
                        }
                    }
            });        
        }
        
    }     
}

function update_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_bjr").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $("#txt_bjrAFD").val(data.AFD);
        $("#txt_bjrBlock").val(data.BLOCK);
        $("#txt_bjrValue").val(data.VALUE);
        
        $("#txt_frmMode").val("EDIT");
     
        //#### SET DEFAULT TABS INDEX
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        //###########################
        $("#frm_bjr").dialog('open');
    } 
}

function simpan_bjr(cl){
    var ids = cl;//jQuery("#list_bjr").getGridParam('selrow'); 
    var data = $("#list_bjr").getRowData(ids) ;
    jQuery('#list_bjr').saveCell(ids);
    $('#frm_load').dialog('open');
    var postdata_id = {};
    
    postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
    postdata_id['AFD'] =  data.AFD;
    postdata_id['BLOCK'] =  data.BLOCK;
    postdata_id['VALUE'] = data.VALUE;
    postdata_id['CRUD'] =  'ADD';

    var data = {
                  id:postdata_id
                };
    data = JSON.stringify(data);
    $.ajax({
            type:           'post',
            cache:          false,
            url:            url+'m_bjr/CRUD_METHOD',
            data:           {myJson:  data} ,
            success: function(msg){
                var obj = jQuery.parseJSON(msg);    
                if(obj.error===true){
                    alert(obj.status)
                    $('#frm_load').dialog('close');
                    $("#frm_bjr").dialog('close');
                }else{
                    alert(obj.status)
                    $('#frm_load').dialog('close');
                    $("#frm_bjr").dialog('close');
                    reloadGrid();  
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

    jQuery("#list_bjr").setGridParam({url:url+"m_bjr/search_data/"+get_bulan()+"/"+get_tahun()+"/"+afd+"/"+block}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">

function set_bjr_periode(){
    clear_form_elements(this.form)
    $("#frm_bjr_set").dialog('open');    
}

function approve(){
	var postdata_detail = {};
	var postdata_id = {};
    var i=0;
	var blok = "";

	s = jQuery("#list_bjr").jqGrid('getGridParam','selarrrow');
	if (s == ""){
		alert ("Data BJR yang akan di Approve belum dipilih");
	}else{
		postdata_id['CRUD'] =  'APP';
		$.each(s, function(n, rowid){ 
			var str = jQuery("#list_bjr").jqGrid('getGridParam','selarrrow');
			//alert(str[i]);
			var data = $("#list_bjr").getRowData(str[i]) ;		
			i=i+1;
			postdata_detail['BULAN'+i] = $("#bulan").val();
		    postdata_detail['TAHUN'+i] = $("#tahun").val();
			postdata_detail['ID_BJR'+i] = data.ID_BJR;
			postdata_detail['AFD'+i] = data.AFD;
			postdata_detail['BLOCK'+i] = data.BLOCK;
			blok = data.BLOCK + " "+blok;
			//alert (blok);
			postdata_detail['VALUE'+i] = data.VALUE;	
			
			});
		var answer = confirm  ("Yakin anda akan Approve data BJR untuk Blok : " + blok + " ?");

		if (answer){
			var data = {id:postdata_id, detail:postdata_detail};
        	data = JSON.stringify(data);
        	$.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_bjr/CRUD_METHOD',
                data:           {myJson:  data} ,
                success: function(msg){
                    var obj = jQuery.parseJSON(msg);    
                    if(obj.error===true){
                        alert(obj.status)
                        $("#frm_load").dialog('close');
                    }else{
                        alert(obj.status)
                        reloadGrid();
                        $("#frm_load").dialog('close');
                        $("#frm_bjr").dialog('close');    
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

</script>