<? 
    $template_path = base_url().$this->config->item('template_path');  
?>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/ajaxfileupload.js"></script>
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
                <td><?php echo $rawat_rutin_periode; ?></td>
            </tr>-->   
        </table>       
    </div>

    <div id='transaction_frame'>
    
        <div id="mainGrid">
            <table id="list_rawat_rutin" class="scroll"></table> 
            <div id="pager_rawat_rutin" class="scroll" style="text-align:center;"></div>
        </div>
        
        <div id="btn_section">
           <!-- <button class="testBtn" type="submit" id="set_bjrPeriode" onclick="set_bjr_periode()">Set BJR Periode</button>&nbsp; -->
           <button class="testBtn" type="submit" id="delete_all" onclick="delete_all()">Hapus Data</button> 
           <button class="testBtn" type="submit" id="print_all" onclick="print_all()">Cetak Per Blok</button> 
        </div>
    </div>
</div> 

<div id="frm_rawat_rutin">
    <div id="tabs">
    </div>
    <input type="hidden" id="txt_frmMode">  
</div>

<div id="frm_rawat_rutin_set">

    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
        <tr><td colspan="8">Pilih Periode :</td></tr>
        <tr>
            <td>Periode</td><td>:</td><td><? echo $periode_rawat_rutin; ?></td>
        </tr> 
    </table>
    
</div>
<div id="frm_load">
    Wait...
</div>

		<div id="myForm">     
            Perusahaan : </span><?php echo $company_dest; ?> <p />
            <span>Periode Rawat Rutin : </span><?php echo $periode;?> <p />
            <p style='font-style:oblique; color:#F00'>
			*Format file upload : 
            <br>
             1. CSV
             <br>
             2. Nama kolom : BLOK_TANAH, CIRCLE, GAWANGAN, PATH, TPH, PRUNING, LALANG, INITIAL_BLOK, BULAN,	TAHUN 
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
            jQuery("#list_rawat_rutin").smartSearchPanel('#search_form', {dialog:{width: 530}},'m_rawat_rutin/search_data_2');
        }
    });
   
   $(function() {
		 $("#myForm").dialog({
			bgiframe: true, autoOpen: false, height: 300, width: 550,
			modal: true, title: "Upload data rawat rutin", resizable: false, 
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
   
   $("#frm_rawat_rutin_set").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 150,
        width: 300,
        modal: true,
        title: "Penetapan Periode Rawat Rutin",
        resizable: true,
        moveable: true,
        buttons: {
            'Set Periode': function() 
                    {
                        set_rawat_rutin_periode_do();        
                    }
           
        } 
    });
    
   $("#frm_rawat_rutin").dialog({
        dialogClass : 'dialog1',

        autoOpen: false,
        height: 450,
        width: 620,
        modal: true,
        title: "Rawat Rutin",
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
            url+"m_rawat_rutin/get_afdeling", {
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
    
    $("#txt_rawat_rutinAFD").autocomplete( 
            url+"m_rawat_rutin/get_afdeling", {
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
    
    $("#txt_rawat_rutinBlock").keydown(function(){
        var AFD = $("#txt_rawat_rutinAFD").val();
        $("#txt_rawat_rutinBlock").autocomplete( 
                url+"m_rawat_rutin/get_block/"+AFD, {
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
				url:url+'m_rawat_rutin/do_import/',
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
     jQuery("#list_rawat_rutin").setGridParam({url:url+'m_rawat_rutin/LoadData/'+get_bulan()+'/'+get_tahun()}).trigger("reloadGrid");    
}
/*
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
*/
</script>

<script language="JavaScript" type="text/javascript"> 
/*
$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
  });
*/
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

colNamesT.push('ID_RAWAT_RUTIN');
colModelT.push({name:'ID_RAWAT_RUTIN',index:'ID_rawat_rutin', editable: false, hidden:true, width: 140, align:'left'});

colNamesT.push('AFD');
colModelT.push({name:'AFD',index:'AFD', editable: true,width: 40
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete( 
                      
                    url+"m_rawat_rutin/get_afdeling", {
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
                    $("#BLOK_TANAH").val(item.res_name );
                  });
          }}, width: 70, align:'center'}); 

colNamesT.push('BLOK TANAH');
colModelT.push({name:'BLOK_TANAH',index:'BLOK_TANAH', editable: true ,width: 160
,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem)
                  .autocomplete(
                        url+"m_rawat_rutin/get_block/"+giveLocType(), {
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

colNamesT.push('CIRCLE');
colModelT.push({name:'CIRCLE',index:'CIRCLE', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('GAWANGAN');
colModelT.push({name:'GAWANGAN',index:'GAWANGAN', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('PATH');
colModelT.push({name:'PATH',index:'PATH', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('TPH/KASTRASI');
colModelT.push({name:'TPH',index:'TPH', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('PRUNING/SANITASI');
colModelT.push({name:'PRUNING',index:'PRUNING', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('LALANG');
colModelT.push({name:'LALANG',index:'LALANG', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('INITIAL BLOK');
colModelT.push({name:'INITIAL_BLOK',index:'INITIAL_BLOK', editable: true, hidden:false, width: 70, align:'right'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 70, align:'left'});

colNamesT.push('');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
        jGrid_va = jQuery("#list_rawat_rutin").jqGrid(
            {
                url:url+'m_rawat_rutin/LoadData/'+get_bulan()+'/'+get_tahun(),
                mtype : "POST",
                datatype: "json",
				multiselect:true,
				multiselectWidth: 50,
                colNames: colNamesT ,
                colModel: colModelT,
                sortname: colModelT[1].name,
                pager:jQuery("#pager_rawat_rutin"),
                rowNum: 100,
                rownumbers: true,
                height: 300,
                width: 1000,
                imgpath: gridimgpath,
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : true,
                onCellSelect : function(iCol){},
                loadComplete: function(){ 
                    var ids = jQuery("#list_rawat_rutin").getDataIDs();
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

                            jQuery("#list_rawat_rutin").setRowData(ids[i],{act:be+ce}) 
                        }
                                            
                    }
            });
         jGrid_va.navGrid('#pager_rawat_rutin',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_rawat_rutin',{
               caption:"Tambah", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
         jGrid_va.navButtonAdd('#pager_rawat_rutin',{
               caption:"Cari", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){
               		$("#search_form").dialog('open');
               }, 
               position:"left"
            });
		 jGrid_va.navButtonAdd('#pager_rawat_rutin',{
			   caption:"Upload", buttonicon:'ui-icon-newwin',
	   		   onClickButton: function(){  
			   		OpenUploadForm(); 
				}, position:"left" });            
         }
jQuery("#list_rawat_rutin").ready(loadView); 
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
    var ids = jQuery("#list_rawat_rutin").getGridParam('selrow'); 
    var rets = jQuery("#list_rawat_rutin").getRowData(ids); 
    var type = rets.AFD;
    return type;
}

function getAfdType(){
    var afd = $("#txt_rawat_rutinAFD").val();
    return afd;
}

function addrow(){
    var rowCount = $("#list_rawat_rutin").getGridParam("reccount");
    var i;
    if(rowCount==null || rowCount==0){
        i=i+1;    
    }else{
        i=rowCount+1;
    }
        
    var datArr = {};
    if (i>1){
        var datArr = {ID_RAWAT_RUTIN:jdesc1};
    }
    

    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_rawat_rutin("+i+")'; />"; 
    var su=jQuery("#list_rawat_rutin").addRowData(i,datArr,'last');
    var act=jQuery("#list_rawat_rutin").setRowData(i,{act:sv});  
}
</script>

<script type="text/javascript">
/*
function update_rawat_rutin(){
    var postdata_id = {};
    
    postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
    postdata_id['AFD'] =  $("#txt_rawat_rutinAFD").val();
    postdata_id['BLOCK'] =  $("#txt_rawat_rutinBlock").val();
    postdata_id['VALUE'] = $("#txt_rawat_rutinValue").val();
    postdata_id['CRUD'] =   $("#txt_frmMode").val();

    var data = {
                  id:postdata_id
                };
    data = JSON.stringify(data);
    $.ajax({
            type:           'post',
            cache:          false,
            url:            url+'m_rawat_rutin/CRUD_METHOD',
            data:           {myJson:  data} ,
            success: function(msg){
                var obj = jQuery.parseJSON(msg);    
                if(obj.error===true){
                    alert(obj.status)
                    $("#frm_rawat_rutin").dialog('close');
                }else{
                    alert(obj.status)
                    $("#frm_rawat_rutin").dialog('close');
                    reloadGrid();  
                }
            }
    });    
}
*/
function hapus_data(cl){
    var ids = cl;//jQuery("#list_notaangkut").getGridParam('selrow'); 
    var data = $("#list_rawat_rutin").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di hapus...");
    }else{
        var answer = confirm ("Hapus Data rawat rutin untuk AFD : " + data.AFD + ", dan blok tanah: " + data.BLOK_TANAH + ", untuk periode yang dipilih ?" )
        if (answer){
            $('#frm_load').dialog('open');
            var postdata_id = {};
    
            postdata_id['BULAN'] = $("#bulan").val();
            postdata_id['TAHUN'] = $("#tahun").val();
            postdata_id['AFD'] =  data.AFD;
            postdata_id['BLOK_TANAH'] =  data.BLOK_TANAH;
            postdata_id['CRUD'] =  'DEL';

            $.post( url+'m_rawat_rutin/CRUD_METHOD', postdata_id,function(status) 		  { 
              var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                  	} else { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                   	};  
      		});       
        }
        
    }     
}

function update_data(cl){

	//alert("simpan");
    var ids = cl;//jQuery("#list_rawat_rutin").getGridParam('selrow'); 
    var data = $("#list_rawat_rutin").getRowData(ids) ;
    jQuery('#list_rawat_rutin').saveCell(ids);
    $('#frm_load').dialog('open');
    var postdata_id = {};
    
    postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
	postdata_id['ID_RAWAT_RUTIN'] =  data.ID_RAWAT_RUTIN;
    postdata_id['AFD'] =  data.AFD;
    postdata_id['BLOK_TANAH'] =  data.BLOK_TANAH;
    postdata_id['CIRCLE'] = data.CIRCLE;
	postdata_id['GAWANGAN'] = data.GAWANGAN;
	postdata_id['PATH'] = data.PATH;
	postdata_id['TPH'] = data.TPH;
	postdata_id['PRUNING'] = data.PRUNING;
	postdata_id['LALANG'] = data.LALANG;
	postdata_id['INITIAL_BLOK'] = data.INITIAL_BLOK;
    postdata_id['CRUD'] =  'EDIT';

    $.post( url+'m_rawat_rutin/CRUD_METHOD', postdata_id,function(status) 		  { 
              var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                  	} else { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                   	};  
      });


}

function simpan_rawat_rutin(cl){
	//alert("simpan");
    var ids = cl;//jQuery("#list_rawat_rutin").getGridParam('selrow'); 
    var data = $("#list_rawat_rutin").getRowData(ids) ;
    jQuery('#list_rawat_rutin').saveCell(ids);
    $('#frm_load').dialog('open');
    var postdata_id = {};
    
    postdata_id['BULAN'] = $("#bulan").val();
    postdata_id['TAHUN'] = $("#tahun").val();
    postdata_id['AFD'] =  data.AFD;
    postdata_id['BLOK_TANAH'] =  data.BLOK_TANAH;
    postdata_id['CIRCLE'] = data.CIRCLE;
	postdata_id['GAWANGAN'] = data.GAWANGAN;
	postdata_id['PATH'] = data.PATH;
	postdata_id['TPH'] = data.TPH;
	postdata_id['PRUNING'] = data.PRUNING;
	postdata_id['LALANG'] = data.LALANG;
	postdata_id['INITIAL_BLOK'] = data.INITIAL_BLOK;
    postdata_id['CRUD'] =  'ADD';

    $.post( url+'m_rawat_rutin/CRUD_METHOD', postdata_id,function(status) 		  { 
              var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                  	} else { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                   	};  
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

    jQuery("#list_rawat_rutin").setGridParam({url:url+"m_rawat_rutin/search_data/"+get_bulan()+"/"+get_tahun()+"/"+afd+"/"+block}).trigger("reloadGrid");        
}
</script>

<script type="text/javascript">

function get_periode(){
    var lPeriode = $("#tahun").val() + $("#bulan").val();
    return lPeriode;    
}

function set_rawat_rutin_periode(){
    clear_form_elements(this.form)
    $("#frm_rawat_rutin_set").dialog('open');    
}

function print_all(){
	var answer = confirm ("Anda ingin mencetak excel data rawat rutin per blok tanah periode : " + get_periode() + " ?" );
	if (answer){

	var postdata_id = {};
    var data = {
					id:postdata_id,
               };
        data = JSON.stringify(data);
            $.ajax({
                    type:           'post',
                    cache:          false,
                    url:            url+'m_rawat_rutin/xls_month/'+ get_periode(),
                    data:           {myJson:  data} ,
                    success: function(msg){
						var win=window.open(url+'m_rawat_rutin/xls_month/'+ get_periode(), '_blank');
  						win.focus();
                    }
            });        
        
    }             
}

function delete_all(){
	var postdata_detail = {};
	var postdata_id = {};
    var i=0;
	var blok = "";

	s = $("#list_rawat_rutin").getGridParam('selarrrow'); 
	if (s == ""){
		alert ("Data rawat_rutin yang akan di Approve belum dipilih");
	}else{
		postdata_id['CRUD'] =  'DELL_ALL';
		$.each(s, function(n, rowid){ 
			//var str = jQuery("#list_rawat_rutin").jqGrid('getGridParam','selarrrow');
			//alert(str[i]);
			var data = $("#list_rawat_rutin").getRowData(rowid) ; 		
			i=i+1;
			postdata_detail['BULAN'+i] = $("#bulan").val();
		    postdata_detail['TAHUN'+i] = $("#tahun").val();
			postdata_detail['ID_RAWAT_RUTIN'+i] = data.ID_RAWAT_RUTIN;
			postdata_detail['AFD'+i] = data.AFD;
			postdata_detail['BLOK_TANAH'+i] = data.BLOK_TANAH;
			blok = data.BLOK_TANAH + " "+blok;
			//alert (blok);
			postdata_detail['CIRCLE'+i] = data.CIRCLE;	
			postdata_detail['GAWANGAN'+i] = data.GAWANGAN;
			postdata_detail['PATH'+i] = data.PATH;
			postdata_detail['TPH'+i] = data.TPH;
			postdata_detail['PRUNING'+i] = data.PRUNING;
			postdata_detail['LALANG'+i] = data.LALANG;
			postdata_detail['INITIAL_BLOK'+i] = data.INITIAL_BLOK;
			
			});
		var answer = confirm  ("Yakin anda akan menghapus data rawat_rutin untuk Blok : " + blok + " ?");

		if (answer){
			$('#frm_load').dialog('open');
			$.post( url+'m_rawat_rutin/delete_all_data', postdata_detail, function(status) { 
              var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                  	} else { 
								alert(status);
								$('#frm_load').dialog('close'); 
								reloadGrid();
                   	};  
      			});
		}// end answer 
	}
}

/*
function set_rawat_rutin_periode_do(){
    var answer = confirm ("Tetapkan Periode rawat_rutin ? " )
    if (answer){
        var answer_2 = confirm ("semua perhitungan hasil timbangan akan menggunakan referensi rawat_rutin periode: "+$("#tahun_rawat_rutin").val()+$("#bulan_rawat_rutin").val() )
        if (answer){
            var rawat_rutin_periode = $("#tahun_rawat_rutin").val()+$("#bulan_rawat_rutin").val();
            $.ajax({
                type:           'post',
                cache:          false,
                url:            url+'m_rawat_rutin/set_rawat_rutin_periode/'+rawat_rutin_periode,
                success: function(msg){
                    alert(msg);
                    $("#frm_rawat_rutin_set").dialog('close'); 
                }
            });
        }
        
    }    
}
*/

</script>