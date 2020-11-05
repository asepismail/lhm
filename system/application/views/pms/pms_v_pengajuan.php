<? 
    $template_path = base_url().$this->config->item('template_path');  
?> 
<link rel="stylesheet" type="text/css" href="<?= $template_path ?>js/uploadify/uploadify.css" />
	<script type="text/javascript" language="javascript" src="<?= $template_path ?>js/uploadify/swfobject.js"></script>
    <script type="text/javascript" language="javascript" src="<?= $template_path ?>js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>  
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 

/* init tabs */
$('#tabs-addPengajuan').smartTab({autoProgress: false,stopOnFocus:true,transitionEffect:'vSlide', 
						     onShowTab: function(){}
});

$('#tabs-inPutPengajuan').smartTab({autoProgress: false,stopOnFocus:true,transitionEffect:'vSlide', 
						     onShowTab: function(){}
});
/* end tabs */

/*####### dialog ###### */
$(function() {
     $("#form_input").dialog({
        bgiframe: true, autoOpen: false, height: 650, width: 1280,
        modal: true, title: "Tambah Detail Pengajuan", resizable: false, moveable: true,
		
     }); 
});

$(function() {
     $("#sblok").dialog({
        bgiframe: true, autoOpen: false, height: 350, width: 550, zIndex: -3999,
        modal: false, title: "Master Blok Tanah", resizable: false, moveable: true,
		buttons: {
					'Batal': function() {
						$("#sblok").dialog("close");
						//$('#sblok').dialog('destroy').remove();
                    }
           } 
     }); 
});

$(function() {
     $("#input_detail").dialog({
        bgiframe: true, autoOpen: false, height: 250, width: 500,
        modal: true, title: "Tambah Detail Aktivitas", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						$("#input_detail").dialog('close');
						init_formdetail();
                    },
					'Simpan': function() {
						submit_detail_activity();
						reloadGridActivity();
						init_formdetail();
                    }
                    
           } 
     }); 
});
/*####### end dialog ###### */

/* fungsi button penambahan detail pengajuan */  
$( "#simpanDetailPengajuan" ).click(function() {
	var inisial = 'simpan';
	submit_detailppj(inisial);
	$("#form_input").dialog("close");
	init_forminput();
	inisialisasi(inisial);
});

$( "#batalDetailPengajuan" ).click(function() {
	init_forminput();
	cancel_detailppj();
	$("#form_input").dialog('close');
});

$( "#tutupDetailPengajuan" ).click(function() {
	$("#i_typepj").empty();
	$("#i_subtypepj").empty();
	$("#i_if_type").empty();
	$("#i_aktivitas").empty();
	$("#i_subaktivitas").empty();
	$("#form_input").dialog('close');
});
/* end fungsi button */


function hideRow(){
	var row2 = document.getElementById("if2");
	var row1 = document.getElementById("if1");
	row2.style.display = 'none';
	row1.style.display = 'none';
}

function displayRow(){
	var row2 = document.getElementById("if2");
	var row1 = document.getElementById("if1");
	row2.style.display = '';
	row1.style.display = '';
}
var lrtcomplete = "Data project sebelumnya belum dicomplete,\nMohon selesaikan atau batalkan transaksi sebelumnya untuk membuat pengajuan baru";


function giveNoPJS(){
	var postdata = {};
	$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
		var d = data.split('~');
		return $.trim(d[1]);
	});
}

/* ############# fungsi fire event untuk dropdown ########## */
/* ############# tipe PJ ########## */
function ddtypepj(){
	var type = $("#i_typepj").val();
    if (type != 0){ $("#i_subtypepj").empty(); var cType = $("#i_subtypepj").val();
    if (cType==null){ cType="-"; }
    	$.post(url+'pms_c_pengajuan/getSubtipe/'+$("#i_typepj").val()+'/'+cType+'/', $("#i_subtypepj").val(), function(datapost){ 
	   	  $("#i_subtypepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
          	for (var i=0; i<datapost.length; i++){
                 $("#i_subtypepj").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
             }
          },"json")
    } else {
            $("#i_subtypepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
    }
}

/* ############# Subtipe PJ ########## */
function ddsubtypepj(){
	var type = $("#i_subtypepj").val();
    if (type != 0){ $("#i_if_type").empty(); var cType = $("#i_if_type").val();
     	if (cType==null){ cType="-"; }
			$.post(url+'pms_c_pengajuan/LoadChain/'+$("#i_subtypepj").val()+'/'+cType+'/', $("#i_if_type").val(),function(datapost){ 
	      		$("#i_if_type").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
          		for (var i=0; i<datapost.length; i++){
			  	 $("#i_if_type").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
             	}
          	},"json")
		   	$("#i_aktivitas").empty();
			var cType = $("#i_aktivitas").val();
		if (cType==null){ cType="-"; }
			$.post(url+'pms_c_pengajuan/get_aktivitas/'+$("#i_subtypepj").val()+'/'+cType+'/', $("#i_aktivitas").val(), 
				function(datapost){ 
					  for (var i=0; i<datapost.length; i++){
							 $("#i_aktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					  }
			},"json")
	} else {
        $("#i_if_type").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
	} 
}

function ddpjactivity(){
	var type = $("#i_typepj").val();
	var subtype = $("#i_subtypepj").val();
    if (type != 0){ 
		$("#det_aktivitas").empty(); 
		var cType = $("#det_aktivitas").val();
		if (cType==null){ cType="-"; }
			$.post(url+'pms_c_pengajuan/get_activity_pj/'+type+'/'+subtype+'/'+cType+'/', $("#det_aktivitas").val(), 
				function(datapost){ 
					  for (var i=0; i<datapost.length; i++){
							 $("#det_aktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					  }
			},"json")
	} else {
        $("#det_aktivitas").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
	} 
}

function ddsubtypeif(){
	var type = $("#i_if_type").val();
    if (type != 0){
       $("#i_subaktivitas").empty();
	   var cType = $("#i_subaktivitas").val();
	   if (cType==null){ cType="-"; }
			$.post(url+'pms_c_pengajuan/get_subaktivitas/'+$("#i_if_type").val()+'/'+cType+'/', $("#i_subaktivitas").val(), 
				function(datapost){ 
					$("#i_subaktivitas").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);	
					for (var i=0; i<datapost.length; i++){
						$("#i_subaktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
				},"json")
     } else {
            $("#i_subaktivitas").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
     }
}

/* ############# end fungsi fire event untuk dropdown ########## */
$(document).ready(function() {
	var inisial="";
	$("#search").hide();
	$("#mainPengajuan").hide();
	$("#loaderMainPengajuan").show();
	
	inisialisasi(inisial);					   
	//alert( $.trim($("#i_no_ppj").val()) )		
	//$("#detailgrid").hide();
	$(function() {  $( "#tabs" ).tabs();  });
	hideRow();
	setTypeLokasi();
	 //$("#i_tgl_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_tgl_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	//$("#i_target_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_start").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_end").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
		
	/* cekbox detail */
	/* $("#isdetail").change(function() {
		var cek = $(this).is(":checked");
		if(cek == true){
			$("#detailgrid").show();
		} else {
			$("#detailgrid").hide();
		}
	}); */
	/* subtype pj */
 	$("#i_typepj").change(function() { ddtypepj(); });   
	
	/* subtype PJ */
 	$("#i_subtypepj").change(function() { ddsubtypepj(); });
	
	/* subtype IF */
	$("#i_if_type").change(function() { ddsubtypeif(); });	
	
	/* hitung rupiah total saat entri qty dan rp satuan */
	$("#i_qty").keyup(function() {
        if($("#i_qty").val() != "" ){
			if( $("#i_rpsat").val() != "" ){
				var total = $("#i_qty").val() * $("#i_rpsat").val();
				$("#i_rptotal").val(total)
			}
		}
    });  
	
	$("#i_rpsat").keyup(function() {
        if($("#i_qty").val() != "" ){
			if( $("#i_rpsat").val() != "" ){
				var total = $("#i_qty").val() * $("#i_rpsat").val();
				$("#i_rptotal").val(total)
			}
		}
    });
	
	/* ####### hitung rupiah di detail ######## */
	$("#det_qty").keyup(function() {
        if($("#det_qty").val() != "" ){
			if( $("#det_rpsat").val() != "" ){
				var total = $("#det_qty").val() * $("#det_rpsat").val();
				$("#det_nett").val(total)
			}
		}
    });  
	
	$("#det_rpsat").keyup(function() {
        if($("#det_rpsat").val() != "" ){
			if( $("#det_qty").val() != "" ){
				var total = $("#det_qty").val() * $("#det_rpsat").val();
				$("#det_nett").val(total)
			}
		}
    });

	/* ####### end hitung rupiah di detail ######## */
	/* end hitung rupiah total saat entri qty dan rp satuan */
	/* fungsi upload */
	$("#Pjupload").uploadify({
							uploader: '<?= $template_path ?>js/uploadify/uploadify.swf',
							script: '<?php echo site_url('pms/pms_c_upload/upload2/');?>',
							cancelImg: '<?= $template_path ?>js/uploadify/cancel.png',
							folder: '/uploads',
							scriptAccess: 'always',
							scriptData    : {'projectID': $("#i_pjsementara").val() },
							multi: true,
							'onError' : function (a, b, c, d) {
								 if (d.status == 404)
									alert('Could not find upload script.');
								 else if (d.type === "HTTP")
									alert('error '+d.type+": "+d.status);
								 else if (d.type ==="File Size")
									alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
								 else
									alert('error '+d.type+": "+d.text);
								},
							'onComplete'   : function (event, queueID, fileObj, response, data) {
												//Post response back to controller
												$.post('<?php echo site_url('pms/pms_c_upload/upload2/');?>',{filearray: response},function(info){
													$("#target").append(info);  //Add response returned by controller																		  
												});								 			
							}
					});	
		/* end fungsi upload */
});	
/* #### fungsi cek sudah selesai atau belum */
function inisialisasi(inisial){
	var postdata = {};
	$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
	/* kalau transaksi belum complete */																 
			var d = data.split('~');
			if(d[0] > 0) {
				if(inisial !== "simpan"){
					var pjs = '';
					$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
						var d = data.split('~');
						pjs = $.trim(d[1]);
						jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(pjs)}).trigger("reloadGrid");
					});
				 	alert(lrtcomplete);
				}
				 $("#i_no_ppj").val($.trim(d[1])); $("#i_no_ppj").attr('disabled','true');
				 $("#i_tgl_ppj").val(d[2]); $("#i_tgl_ppj").attr('disabled','true');
				 $("#i_pelaksana").val(d[3]); $("#i_pelaksana").attr('disabled','true');
				 $("#i_dept").val(d[4]); $("#i_dept").attr('disabled','true');
				 //$("#i_target_ppj").val(d[5]); $("#i_target_ppj").attr('disabled','true');
				jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(d[1])}).trigger("reloadGrid");
				$("#mainPengajuan").show();
				$("#loaderMainPengajuan").hide();
			
			} else {
				$("#i_tgl_ppj").attr('disabled',''); $("#i_pelaksana").attr('disabled','');
				//$("#i_dept").attr('disabled',''); $("#i_target_ppj").attr('disabled','');
				/* kalau transaksi baru */
				$.post(url+'pms_c_pengajuan/ext_genNoPengajuan/', postdata, function(data){
					$("#i_no_ppj").val($.trim(data));
					$("#i_tgl_ppj").datepicker("setDate",new Date());
					jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(data)}).trigger("reloadGrid");
					$("#mainPengajuan").show();
					$("#loaderMainPengajuan").hide();
				});
			}
	});
}
/* #### end fungsi #### */
/*grid*/

var jGrid_pengajuan = null;
var colNamesT_pengajuan = new Array();
var colModelT_pengajuan = new Array();

colNamesT_pengajuan.push('ID');
colModelT_pengajuan.push({name:'PROJECT_PROP_ID',index:'PROJECT_PROP_ID', hidden:true, width: 80, align:'center'});

colNamesT_pengajuan.push('No PPJ');
colModelT_pengajuan.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:true, width: 80, align:'center'});

colNamesT_pengajuan.push('Complete');
colModelT_pengajuan.push({name:'ISCOMPLETE',index:'ISCOMPLETE', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pengajuan.push('Company');
colModelT_pengajuan.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 80, align:'center'});

colNamesT_pengajuan.push('Type');
colModelT_pengajuan.push({name:'PROJECT_PROP_TYPE',index:'PROJECT_PROP_TYPE', hidden:false, width: 40, align:'center'});

colNamesT_pengajuan.push('Subtype');
colModelT_pengajuan.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('IF Type');
colModelT_pengajuan.push({name:'PROJECT_PROP_IFTYPE',index:'PROJECT_PROP_IFTYPE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('No PJ.');
colModelT_pengajuan.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pengajuan.push('AFD');
colModelT_pengajuan.push({name:'PROJECT_PROP_AFD',index:'PROJECT_PROP_AFD', editable: false, hidden:false, width: 40, align:'center'});

colNamesT_pengajuan.push('Keterangan');
colModelT_pengajuan.push({name:'PROJECT_PROP_DESC',index:'PROJECT_PROP_DESC', editable: false, hidden:false, width: 180, align:'center'});

colNamesT_pengajuan.push('Lokasi');
colModelT_pengajuan.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 120, align:'center'});

colNamesT_pengajuan.push('Aktivitas');
colModelT_pengajuan.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_pengajuan.push('Sub Aktivitas');
colModelT_pengajuan.push({name:'PROJECT_PROP_SUBACTIVITY',index:'PROJECT_PROP_SUBACTIVITY', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_pengajuan.push('Qty');
colModelT_pengajuan.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_pengajuan.push('Satuan');
colModelT_pengajuan.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('Harga Satuan');
colModelT_pengajuan.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pengajuan.push('Total');
colModelT_pengajuan.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pengajuan.push('Start');
colModelT_pengajuan.push({name:'PROJECT_PROP_START',index:'PROJECT_PROP_START', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('End');
colModelT_pengajuan.push({name:'PROJECT_PROP_END',index:'PROJECT_PROP_END', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('detail');
colModelT_pengajuan.push({name:'ISDETAIL',index:'ISDETAIL', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('');
colModelT_pengajuan.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_pengajuan = function(){
    jGrid_pengajuan = jQuery("#list_pengajuan").jqGrid({
        url:url+'pms_c_pengajuan/read_ppj/'+giveNoPJS(),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pengajuan , colModel: colModelT_pengajuan ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Pengajuan <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 180, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_pengajuan").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan'), sortname: colModelT_pengajuan[0].name
		});
		jGrid_pengajuan.navGrid('#pager_pengajuan',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_pengajuan").ready(loadView_pengajuan);

	/* function insert header project */
	function submit_headerppj(){
		if($("#i_dept").val() == "") {
			alert("Mohon pilih departemen yang mengajukan..")
		} else if ($("#i_no_ppj").val() == "" ) {
			alert("Mohon menunggu no ppj digenerate sistem ..")
		} else if ($("#i_tgl_ppj").val() == "" ) { 
			alert("Mohon isi tanggal pengajuan ..")
		} else if ($("#i_pelaksana").val() == "" ) { 
			alert("Mohon pilih pelaksana project ..")
		} else {
				var postdata={};
				postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
				postdata['PROJECT_DEPT'] = $("#i_dept").val() ; 
				postdata['PROJECT_PROPNUM_DATE'] = $("#i_tgl_ppj").val() ; 
				//postdata['PROJECT_FINISH_TARGET'] = $("#i_target_ppj").val() ; 
				postdata['PROJECT_PROPNUM_PELAKSANA']= $("#i_pelaksana").val();
				
				$.post( url+"pms_c_pengajuan/insert_header/", postdata, function(message) {
					if(message.replace(/\s/g,"") != 0 ) { 
						alert("data gagal tersimpan");
					} else { 
						$("#i_pjsementara").val("");
						$("#i_dept").attr('disabled','TRUE');
						reloadGridActivity();
						init_forminput();
						addPengajuan();
					};  
				});
		}
	}
	
	function cancel_headerppj(){
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
		
		$.post( url+"pms_c_pengajuan/cancel_header/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasi(inisial);
					$("#i_dept").attr('disabled','');
				};  
			});
	}
	
	/* function pengecekan dropdown */
	function setTypeLokasi(){
		var dept = $("#i_dept").val();
		if (dept != 0) {
			if(dept == "TNM"){
				hideRow();
				$("#search").show();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "OP","OP"), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "NS","NS"), document.all ? i : null);
				$("#i_lokasi").attr('disabled','true');
				$("#isdetail").attr('disabled','');
			} else if (dept == "TEK"){
				$("#search").hide();
				displayRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "IF","IF"), document.all ? i : null);
				$("#i_lokasi").attr('disabled','');
				$("#isdetail").attr('disabled','true');
			} else if (dept == "PAB"){
				$("#search").hide();
				hideRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "PKS","PKS"), document.all ? i : null);
				$("#i_lokasi").attr('disabled','');
				$("#isdetail").attr('disabled','true');
			}
        }  
	}

function givepjType(){        
            var type = $('#i_typepj').val();
            return type;
        } 

function givepjSubType(){        
            var type = $('#i_subtypepj').val();
            return type;
        } 

/* detail aktivitas PJ */
var jGrid_pengajuan_detail = null;
var colNamesT_pengajuan_detail = new Array();
var colModelT_pengajuan_detail = new Array();

colNamesT_pengajuan_detail.push('No PPJ');
colModelT_pengajuan_detail.push({name:'PROJECT_PROPDET_ID',index:'PROJECT_PROPDET_ID', hidden:true, width: 80, align:'center'});

colNamesT_pengajuan_detail.push('Kode Project');
colModelT_pengajuan_detail.push({name:'DPROJECT_ID',index:'DPROJECT_ID', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pengajuan_detail.push('Aktivitas');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_ACTIVITY',index:'DPROJECT_PROP_ACTIVITY', hidden:false, 
		editable: true, edittype: "text", width: 120, align:'center'});

colNamesT_pengajuan_detail.push('Deskripsi');
colModelT_pengajuan_detail.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', hidden:false, width: 200, align:'center'});

colNamesT_pengajuan_detail.push('Qty');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_QTY',index:'DPROJECT_PROP_QTY', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'center'});

colNamesT_pengajuan_detail.push('Satuan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_UOM',index:'DPROJECT_PROP_UOM', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pengajuan_detail.push('Rp Satuan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_VALUE',index:'DPROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pengajuan_detail.push('Total');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_TVALUE',index:'DPROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pengajuan_detail.push('action');
colModelT_pengajuan_detail.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_pengajuan_detail = function(){
    jGrid_pengajuan_detail = jQuery("#list_pengajuan_detail").jqGrid({
        url:url+'pms_c_pengajuan/read_detail_ppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pengajuan_detail , colModel: colModelT_pengajuan_detail ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        loadComplete: function(){ 
			var ids = jQuery("#list_pengajuan_detail").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                   var cl = ids[i]; 
						 
						ce = "<img style=';cursor:pointer;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"delete_detailpengajuan('"+cl+"');\"/>";
						jQuery("#list_pengajuan_detail").setRowData(ids[i],{act:ce}) 
                }
               
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_detail'), sortname: colModelT_pengajuan_detail[0].name
		});
		jGrid_pengajuan_detail.navGrid('#pager_pengajuan_detail',{edit:false,add:false,del:false, search: false, refresh: true});
        jGrid_pengajuan_detail.navButtonAdd('#pager_pengajuan_detail',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){
				var inisial="tambahdata";
				submit_detailppj(inisial); 
			}, position:"left"
          }); 
        }
	jQuery("#list_pengajuan_detail").ready(loadView_pengajuan_detail);
	
	/* function insert detail activity */
	function submit_detailppj(inisial){
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ; 
		postdata['PROJECT_PROP_AFD'] = $("#i_afd").val() ;
		postdata['PROJECT_PROP_TYPE'] = $("#i_typepj").val() ; 
		postdata['PROJECT_PROP_SUBTYPE'] = $("#i_subtypepj").val() ;
		postdata['PROJECT_PROP_IFTYPE'] = $("#i_if_type").val() ;  
		postdata['PROJECT_PROP_LOCATION'] = $("#i_lokasi").val() ;  
		postdata['PROJECT_PROP_ACTIVITY'] = $("#i_aktivitas").val() ; 
		postdata['PROJECT_PROP_SUBACTIVITY'] = $("#i_subaktivitas").val() ;  
		postdata['PROJECT_PROP_DESC'] = $("#i_deskripsi").val() ;  
		postdata['PROJECT_PROP_START'] = $("#i_start").val() ;  
		postdata['PROJECT_PROP_END'] = $("#i_end").val() ;  
		postdata['PROJECT_PROP_QTY'] = $("#i_qty").val() ;  
		postdata['PROJECT_PROP_UOM'] = $("#i_satuan").val() ;  
		postdata['PROJECT_PROP_VALUE'] = $("#i_rpsat").val() ;  
		postdata['PROJECT_PROP_TVALUE'] = $("#i_rptotal").val() ; 
		var detail = $("#isdetail").is(':checked');
		if(detail==true) {
			detail=1;
		} else {
			detail=0;
		}
		postdata['ISDETAIL'] = detail;
		if( $("#i_ino_pengajuan").val() == "" || $("#i_pjsementara").val() == "" || $("#i_afd").val() == "" || $("#i_typepj").val() == "" || $("#i_subtypepj").val() == "" || $("#i_lokasi").val() == "" || $("#i_aktivitas").val() == "" || $("#i_qty").val() == "" || $("#i_satuan").val() == "" ){
			alert("mohon lengkapi data pengajuan terlebih dahulu!!!");
		} else {
			$.post( url+"pms_c_pengajuan/insert_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					var pjs = '';
					$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
						var d = data.split('~');
						pjs = $.trim(d[1]);
						var urls = url+'pms_c_pengajuan/read_ppj/'+$.trim(pjs);
						jQuery("#list_pengajuan").setGridParam({url:urls}).trigger("reloadGrid");
					});
					//addPengajuan();
					
					//init_forminput();
					if(inisial == "simpan"){
						$("#form_input").dialog("close");
						init_forminput();
						inisialisasi(inisial);
					} else {
						addrow_detailpengajuan();
					}
				};  
			}); 
		}
	}
	/* function insert detail project */
	function submit_detail_activity(){
		var postdata={};
		postdata['DPROJECT_ID'] = $("#det_nopj").val() ;
		postdata['DPROJECT_PROP_ACTIVITY'] = $("#det_aktivitas").val() ; 
		postdata['DPROJECT_PROP_QTY'] = $("#det_qty").val() ;
		postdata['DPROJECT_PROP_UOM'] = $("#d_satuan").val() ; 
		postdata['DPROJECT_PROP_VALUE'] = $("#det_rpsat").val() ;
		postdata['DPROJECT_PROP_TVALUE'] = $("#det_nett").val() ;  
		
		$.post( url+"pms_c_pengajuan/insert_detail_act/", postdata, function(message) {
			reloadGridActivity();
			init_formdetail();
			//alert(message);
			var qty = $("#i_qty").val()
			var rpsat = $.trim(message)/$.trim(qty);
			var total =  $.trim(message);
			$("#i_rpsat").val(rpsat);
			$("#i_rptotal").val(total);
		}); 
	}
	
	function cancel_detailppj(){
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ;
		
		$.post( url+"pms_c_pengajuan/cancel_detail/", postdata, function(message) {
			if(message.replace(/\s/g,"") != 0 ) { 
				alert("data gagal tersimpan");
			} else { 
				inisialisasi(inisial);
			};  
		});
	}
	
	/* fungsi tambah header pengajuan */
	function addPengajuan(){
		var postdata = {};
		//init_forminput();
		//$("#i_pjsementara").val("");
		$("#i_ino_pengajuan").val($("#i_no_ppj").val());
		$("#i_end").val();
		$("#i_ipelaksana").val($("#i_pelaksana").val());
		$("#i_dept").val();
		//$("#i_target_ppj").val();
		$("#form_input").dialog('open');
		setTypeLokasi();
		if( $("#i_pjsementara").val() == "") {
			$.post(url+'pms_c_pengajuan/ext_genPJS/', postdata, function(data){
					$("#i_pjsementara").val($.trim(data));
			});
		}
	}
	
	/* fungsi klik detail pengajuan di grid */
	function vwDetailPengajuan(cl){
		var ids = cl; 
		var data = $("#list_pengajuan").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined){
			alert("harap pilih data terlebih dahulu...");
		}else{
			init_forminput();
			setTypeLokasi();
			$("#form_input").dialog('open');
			$("#i_ino_pengajuan").val(data.PROJECT_PROPNUM_NUMID);	
			$("#i_pjsementara").val(data.PROJECT_ID);
			$("#i_ipelaksana").val($("#i_pelaksana").val());
			$("#i_afd").val(data.PROJECT_PROP_AFD);
			$("#i_typepj").val(data.PROJECT_PROP_TYPE);
			$.post(url+'pms_c_pengajuan/getSubtipe/'+$("#i_typepj").val(), $("#i_typepj").val(), 
			/* ##### regenerate tipe ##### */
			function(datapost){ 
			  $('#i_subtypepj').empty();
			  $("#i_subtypepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);			  
			  for (var i=0; i<datapost.length; i++){
					 $("#i_subtypepj").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
			  }
			  $("#i_subtypepj").val(data.PROJECT_PROP_SUBTYPE);
			},"json");
			/* ##### end regenerate tipe ##### */
			
			/* ##### regenerate dropdown subtype ##### */
			$.post(url+'pms_c_pengajuan/LoadChain/'+data.PROJECT_PROP_SUBTYPE+'/', $("#i_subtypepj").val(),function(datapost){ 
				$('#i_if_type').empty();
	      		$("#i_if_type").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
          		for (var i=0; i<datapost.length; i++){
			  	 	$("#i_if_type").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
             	}
				$("#i_if_type").val(data.PROJECT_PROP_IFTYPE);
          	},"json")
			/* ##### end regenerate ################## */
			
			/* ##### regenerate aktivitas ####################### */
			$.post(url+'pms_c_pengajuan/get_aktivitas/'+data.PROJECT_PROP_SUBTYPE, $("#i_subtypepj").val(), 
				function(datapost){ 
					  $('#i_aktivitas').empty();
					  for (var i=0; i<datapost.length; i++){
							 $("#i_aktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					  }
					  $("#i_aktivitas").val(data.PROJECT_PROP_ACTIVITY);
			},"json")
			/* ##### end regenerate aktivitas ################### */
			/* ##### regenerate sub aktivitas ################### */
			$.post(url+'pms_c_pengajuan/get_subaktivitas/'+data.PROJECT_PROP_IFTYPE, $("#i_if_type").val(), 
				function(datapost){ 
					$('#i_subaktivitas').empty();
					$("#i_subaktivitas").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);	
					for (var i=0; i<datapost.length; i++){
						$("#i_subaktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
					$("#i_subaktivitas").val(data.PROJECT_PROP_SUBACTIVITY);
				},"json")
			/* ##### end regenerate sub aktivitas ################### */
			$("#i_subtypepj").val(data.PROJECT_PROP_SUBTYPE);
			$("#i_lokasi").val(data.PROJECT_PROP_LOCATION);
			$("#i_deskripsi").val(data.PROJECT_PROP_DESC);
			$("#i_start").val(data.PROJECT_PROP_START);
			$("#i_end").val(data.PROJECT_PROP_END);
			$("#i_qty").val(data.PROJECT_PROP_QTY);
			$("#i_satuan").val(data.PROJECT_PROP_UOM);
			$("#i_rpsat").val(data.PROJECT_PROP_VALUE);
			$("#i_rptotal").val(data.PROJECT_PROP_TVALUE);
			var detail = data.ISDETAIL;
			if (detail==1) {
				$("#isdetail").attr('checked',true);
				//$("#detailgrid").show("fast");
			} else {
				$("#isdetail").attr('checked',false);
				//$("#detailgrid").hide("fast");
			}
			reloadGridActivity();
		} 
	}
	
	function init_forminput(){
		$("#i_ipelaksana").val(""); $("#i_afd").val("");
		$("#i_typepj").val(""); $("#i_subtypepj").val(""); $("#i_if_type").val("");
		$("#i_lokasi").val(""); $("#i_aktivitas").val(""); $("#i_subaktivitas").val("");
		$("#i_deskripsi").val(""); $("#i_start").val(""); $("#i_end").val("");
		$("#i_qty").val(""); $("#i_satuan").val(""); $("#i_rpsat").val("");
		$("#i_rptotal").val(""); $("#i_catatan").val(""); $("#i_typepj").empty();
		$("#i_subtypepj").empty(); $("#i_if_type").empty(); $("#i_aktivitas").empty();
		$("#i_subaktivitas").empty();
	}
	
	function init_formdetail(){
		$("#det_aktivitas").val(""); $("#det_deskripsi").val("");
		$("#det_qty").val(""); $("#det_satuan").val(""); $("#det_rpsat").val("");
		$("#det_total").val(""); $("#det_ppn").val(""); $("#det_nett").val("");
		$("#dtypedetail").val(""); 
	}
	
	function addrow_detailpengajuan(){
			init_formdetail();
            var ppj = document.getElementById("i_ino_pengajuan").value;
            var ids = jQuery("#list_pengajuan_detail").getDataIDs();
            var i = ids.length;
            if (ppj != ""){
				$("#det_nopj").val($("#i_pjsementara").val());
				$("#dtypedetail").val($("#i_subtypepj").val()); 
				ddpjactivity();
				reloadGridActivity();
				$("#input_detail").dialog('open');
            } else {
                alert('No Pengajuan kosong\n, silakan mengklik pengajuan baru untuk menggenerate no pengajuan project!');       						
		}
    }
	
	function delete_detailpengajuan(id){
		var postdata={};
		
		if(id==null || id == ''){
			alert("harap pilih data terlebih dahulu !!!")
	   } else {
		 var ids = id; 
		 var data = $("#list_pengajuan_detail").getRowData(ids) ;
		 $.post( url+"pms_c_pengajuan/delete_detail_act/"+id+'/'+data.DPROJECT_ID, postdata, function(message, status) {
			var qty = $("#i_qty").val()
			var rpsat = $.trim(message)/$.trim(qty);
			var total =  $.trim(message);
			$("#i_rpsat").val(rpsat);
			$("#i_rptotal").val(total);
			reloadGridActivity();
		 });
	   }
	}
		
	function reloadGridActivity(){
		var urls = url+'pms_c_pengajuan/read_detail_ppj/'+$.trim($("#i_pjsementara").val());
		jQuery("#list_pengajuan_detail").setGridParam({url:urls}).trigger("reloadGrid"); 
	}
		
function selesai(){
	if($("#i_dept").val() == "") {
		alert("Mohon pilih departemen yang mengajukan..")
	} else if ($("#i_no_ppj").val() == "" ) {
		alert("Mohon menunggu no ppj digenerate sistem ..")
	} else if ($("#i_tgl_ppj").val() == "" ) { 
		alert("Mohon isi tanggal pengajuan ..")
	} else if ($("#i_pelaksana").val() == "" ) { 
		alert("Mohon pilih pelaksana project ..")
	} else {
		var urls = url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_no_ppj").val());
		var dept = $("#i_dept").val();
		var nopengajuan = $("#i_no_ppj").val();
		var tglpengajuan = $("#i_tgl_ppj").val();
		//var tglselesai = $("#i_target_ppj").val();
		var pelaksana = $("#i_pelaksana").val();
		s = $("#list_pengajuan").getDataIDs();
		if( dept=='' || nopengajuan=='' || tglpengajuan=='' || pelaksana=='' || s==''){
			alert("mohon lengkapi data pengajuan terlebih dahulu!!!");
		} else {
			var answer = confirm ("Selesai pengajuan project : " + $("#i_no_ppj").val() + "?" )
			if (answer) {
				var postdata={};
				var inisial="";
				postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
				$.post(url+'pms_c_pengajuan/selesai/', postdata, function(message){
						if(message.replace(/\s/g,"") != 0 ) { 
							alert("data gagal tersimpan");
						} else { 
							alert("data tersimpan");
							inisialisasi(inisial);
							$("#i_dept").attr('disabled','');
							jQuery("#list_pengajuan").setGridParam({url:urls}).trigger("reloadGrid");
						};  
				});
			}
		}
	}
}

/* cari blok */
	var timeoutBlok; 
    var flAuto = false; 
    
    function doSearchBlock(ev){ 
        if(timeoutBlok) 
            clearTimeout(timeoutBlok) 
            timeoutBlok = setTimeout(gridBlokReload,500) 
    } 
    
    function gridBlokReload(){ 
        var afd = jQuery("#i_afd").val();
		var q = jQuery("#isblok").val();
        if (q == ""){ q = "-"; } 
        jQuery("#list_sblok").setGridParam({url:url+"pms_c_pengajuan/read_blok/"+afd+"/"+q}).trigger("reloadGrid");        
    } 

/* detail aktivitas PJ */
var jGrid_sblok = null;
var colNamesT_sblok = new Array();
var colModelT_sblok = new Array();

colNamesT_sblok.push('ID');
colModelT_sblok.push({name:'BID',index:'BID', hidden:true, width: 80, align:'center'});

colNamesT_sblok.push('Kode Blok');
colModelT_sblok.push({name:'BLOCKID',index:'BLOCKID', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_sblok.push('Afd');
colModelT_sblok.push({name:'ESTATECODE',index:'ESTATECODE', hidden:false, 
		editable: false, edittype: "text", width: 80, align:'center'});

colNamesT_sblok.push('Deskripsi');
colModelT_sblok.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, width: 200, align:'center'});

colNamesT_sblok.push('Company');
colModelT_sblok.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 90, align:'left'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_sblok = function(){
    jGrid_sblok = jQuery("#list_sblok").jqGrid({
        url:url+'pms_c_pengajuan/read_blok/'+$.trim($("#i_pjsementara").val()),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_sblok , colModel: colModelT_sblok ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
         ondblClickRow: function(){
			 	var id = jQuery("#list_sblok").getGridParam('selrow');
				if (id)	{
						var ret = jQuery("#list_sblok").getRowData(id);
						jQuery("#i_lokasi").val(ret.BLOCKID)
						$("#sblok").dialog("close");
						//$('#sblok').dialog('destroy');
				}
		},
        loadComplete: function(){ 
                var ids = jQuery("#list_sblok").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_sblok").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_sblok'), sortname: colModelT_sblok[0].name
		});
		jGrid_sblok.navGrid('#pager_sblok',{edit:false,add:false,del:false, search: false, refresh: true});
              
        }
	jQuery("#list_sblok").ready(loadView_sblok);
	
	function searchblok(){
		var afd = $("#i_afd").val();
		if( afd == ""){
			alert("silakan pilih kode afdeling terlebih dahulu..!!!")
		} else {
			$("#sblok").dialog("open");
			gridBlokReload();
		}
	}
	
	/* attachment */
var jGrid_lampiran = null;
var colNamesT_lampiran = new Array();
var colModelT_lampiran = new Array();

colNamesT_lampiran.push('No Lampiran');
colModelT_lampiran.push({name:'ATTACHMENT_ID',index:'ATTACHMENT_ID', hidden:false, width: 120, align:'center'});

colNamesT_lampiran.push('No Pengajuan');
colModelT_lampiran.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:false, width: 100, align:'center'});

colNamesT_lampiran.push('No Project');
colModelT_lampiran.push({name:'PROJECT_ID',index:'PROJECT_ID', hidden:false, width: 90, align:'center'});

colNamesT_lampiran.push('Keterangan');
colModelT_lampiran.push({name:'ATTACHMENT_DESC',index:'ATTACHMENT_DESC', editable: false, hidden:false, width: 120, align:'left'});

colNamesT_lampiran.push('File');
colModelT_lampiran.push({name:'ATTACHMENT_LOCATION',index:'ATTACHMENT_LOCATION', hidden:false, width: 80, align:'center'});

colNamesT_lampiran.push('Dientri Oleh');
colModelT_lampiran.push({name:'INPUTBY',index:'INPUTBY', hidden:false, width: 90, align:'center'});

colNamesT_lampiran.push('Tgl Entri');
colModelT_lampiran.push({name:'INPUTDATE',index:'INPUTDATE', editable: false, hidden:false, width: 90, align:'center'});

colNamesT_lampiran.push('');
colModelT_lampiran.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_lampiran = function(){
    jGrid_lampiran = jQuery("#list_lampiran").jqGrid({
        url:url+'pms_c_pengajuan/read_attachment/'+giveNoPJS(),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_lampiran , colModel: colModelT_lampiran ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Lampiran Pengajuan", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 180, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_lampiran").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_lampiran").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_lampiran'), sortname: colModelT_lampiran[0].name
		});
		jGrid_lampiran.navGrid('#pager_lampiran',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_lampiran").ready(loadView_lampiran);
	/* end attachmetn */
</script>

<div id="tabs-addPengajuan" style="min-height: 480px; height:400px;">
  			<ul>
  				<li><a id="li-1" href="#tabs-addPengajuan-1">Pengajuan Project Baru</a></li>  				
  			</ul>
            
	<div id="tabs-addPengajuan-1" style="min-height:480px; padding:10px;">
    <h2>Daftar Pengajuan Project</h2>
     <br/>
     <div id="loaderMainPengajuan">
     		<img src="<?= $template_path ?>themes_pms/img/loader6.gif" />
     </div>
     <div id="mainPengajuan">	
     <table width="95%">
      <tr>
        <td><table width="95%" >
          <tr>
            <td>Departemen</td>
            <td width="20">:</td>
            <td><? if(isset($dept)) { echo $dept; } ?>
            &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr>
            <td width="150">No. Pengajuan</td>
            <td width="20">:</td>
            <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disable" id="i_no_ppj" name="i_no_ppj" />&nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr>
            <td>Tanggal Pengajuan</td>
            <td>:</td>
            <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_ppj" />
            &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <!-- <tr>
            <td width="150">Target Penyelesaian</td>
            <td width="20">:</td>
            <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_target_ppj" />
            &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr> -->
          <tr>
            <td width="150">Pelaksana</td>
            <td width="20">:</td>
            <td><select tabindex="4" class="validate[required] select" name="i_pelaksana" id="i_pelaksana" cols="45" rows="5">
              <option value=""> -- pilih -- </option>
              <option value="swakelola">Swakelola</option>
              <option value="kontraktor">Kontraktor</option>
            </select> &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr>
            <td colspan="3"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
          </tr>
          <tr>
              <td colspan="3" style="padding:10px;">
                 
                      <table id="list_pengajuan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
                      </table>
                      <div id="pager_pengajuan" class="scroll"></div>
                  
              </td>
          </tr>
          <tr>
            <td colspan="3" style="padding-right:10px;" align="right">
            
                <button class="ui-state-default ui-corner-all" id="tambahPengajuanProject" type="button" 
                          style="height:28px; padding:2px;" onclick="submit_headerppj()">
                    Tambah Data Detail
                </button>
                <button class="ui-state-default ui-corner-all" id="selesaiPengajuanProject" type="button" 
                          style="height:28px; padding:2px;" onclick="selesai()">
                    Selesaikan Pengajuan
                </button>
                <button class="ui-state-default ui-corner-all" id="batalPengajuanProject" type="button" 
                          style="height:28px; padding:2px;" onclick="cancel_headerppj()">
                    Batalkan Pengajuan
                </button>
              
            </td>
          </tr>
        </table></td>
      </tr>
      </table>
      </div>
  </div>
    
  <!-- end grid --->
  
    
 <!--  <div id="tabs-addPengajuan-2" style="min-height:480px;">
    	<h2>Lampiran Detail Pengajuan</h2>       	
    	
  </div>  -->                   
  
 
</div>  


<div id="form_input">
<!-- start tab input pengajuan -->
<div style="margin-top:20px; display:block"></div>
<div id="tabs-inPutPengajuan" style="height: 680px; ">
  			<ul>
  				<li><a id="liPut-1" href="#tabs-inPutPengajuan-1">Pengajuan Project Baru</a></li>
                <li><a id="liPut-2" href="#tabs-inPutPengajuan-2">Lampiran</a></li>  				
  			</ul>
            
<div id="tabs-inPutPengajuan-1" style="height:620px; padding:10px;">

<!-- form untuk input -->
<div style="height:620px;">
        <table width="90%" style="color: #666666; font-size:95%; min-height:620px;
  			font-family: "Segoe UI",Tahoma,arial,sans-serif; width:100%;">
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22" width="155">No. Pengajuan </td>
            <td colspan="2">
              <input tabindex="1" type="text" style="width:120px;" id="i_ino_pengajuan" disabled="disabled" class="input"/></td>
            <td width="20">&nbsp;</td>
            <td width="172">No. Project</td>
            <td width="278"><input tabindex="2" type="text" style="width:120px;" id="i_pjsementara" disabled="disabled" class="input"/></td>
          </tr>
         <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Pelaksana</td>
            <td colspan="2"><input tabindex="3" type="text" style="width:90px;" id="i_ipelaksana" disabled="disabled" class="input"/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">AFD</td> 
            <td colspan="2"><? if(isset($afd)){ echo $afd; } ?>
        &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
            <td>&nbsp;</td>
            <td>Inti / Plasma</td>
            <td><select tabindex="5" name='i_typepj' class='select' id="i_intiplasma" style="width:110px;">
              <option value=""> -- pilih -- </option>
              <option value="inti"> -- Inti -- </option>
              <option value="plasma"> -- Plasma -- </option>
            </select></td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Type Project</td>
            <td colspan="2">
            <select tabindex="5" name='i_typepj' class='select' id="i_typepj" style="width:90px;">
              <option value=""> -- pilih -- </option>
            </select>
        &nbsp; <span style="font-size:9px; color:#F00">* </span></td> 
            <td>&nbsp;</td>
            <td >Subtype Project</td>
            <td><select tabindex="6" name='i_subtypepj' class='select' id="i_subtypepj" style="width:240px;">
              <option value=""> -- pilih -- </option>
            </select>
              &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Sub Tipe Infrastruktur</td>
            <td colspan="2"><select tabindex="8" name='i_if_type' class='select' id="i_if_type" style="width:250px;">
            </select>
              &nbsp; <span style="font-size:9px; color:#F00">* </span></td> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Lokasi</td> 
            <td colspan="2"><input tabindex="9" type="text" style="width:150px;" id="i_lokasi" name="i_lokasi" class="input"/>
                <div id="search" style=" position:relative; margin-left:180px; margin-top:-16px;">
                    <img id="loadPengajuanBarubutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="searchblok()" />
                </div>                <strong></strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>  
          <tr style="border-bottom: 1px solid #DDD;" id="if2">
                <td height="22">Aktivitas Utama</td>
                <td colspan="2"><select tabindex="10" name='i_aktivitas' class='select' id="i_aktivitas" style="width:150px;">
                </select></td> 
                <td>&nbsp;</td>
                <td>Sub Aktivitas</td>
                <td id="if1"><select tabindex="11" name='i_subaktivitas' class='select' id="i_subaktivitas" style="width:230px;">
                </select>
                  <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Deskripsi</td>
            <td colspan="5"><input tabindex="12" type="text" style="width:250px;" id="i_deskripsi" class="input"/></td> 
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Tgl Mulai</td>
            <td colspan="2"><input tabindex="13" type="text" style="width:100px;" id="i_start" class="input"/>
        &nbsp; <span style="font-size:9px; color:#F00">* </span></td> 
            <td>&nbsp;</td>
            <td>Tgl Penyelesaian</td>
            <td><input tabindex="14" type="text" style="width:100px;" id="i_end" class="input"/>
        &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Qty</td>
            <td colspan="2"><input tabindex="15" type="text" style="width:100px;" id="i_qty" class="input"/>
        &nbsp; <span style="font-size:9px; color:#F00">* </span></td>  
            <td>&nbsp;</td>
            <td>Satuan</td>
            <td><? if(isset($satuan)){ echo $satuan; } ?>
              &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
          </tr>
           <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Rupiah Per Satuan</td>
            <td colspan="2"><input tabindex="17" type="text" style="width:120px;" id="i_rpsat" class="input"/></td>
            <td>&nbsp;</td>
            <td>Rupiah Total</td>
            <td><input tabindex="18" type="text" style="width:120px;" id="i_rptotal" class="input"/></td>
           </tr>
          <tr style="border-bottom: 1px solid #DDD;"> 
            <td height="22">Catatan</td>
            <td colspan="2"><input tabindex="19" type="text" style="width:120px;" id="i_catatan" class="input"/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr style="border-bottom: 1px solid #DDD;">
            <td height="22">Detail Aktivitas</td>
            <td colspan="2"><input tabindex="20" type="checkbox" value="1" id="isdetail" class="input"/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="6"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
            </tr>
          <tr>
            <td>&nbsp;</td>
            <td width="122">&nbsp;</td>
            <td width="188">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="6"><div id="detailgrid">
              <table id="list_pengajuan_detail" class="scroll" cellpadding="0" cellspacing="0">
              </table>
              <div id="pager_pengajuan_detail" class="scroll"></div>
            </div></td>
            </tr>
          <tr>
            <td colspan="6">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="6" align="right">
                <button class="ui-state-default ui-corner-all" type="button" id="simpanDetailPengajuan"
                            style="height:28px; padding:2px;" > Simpan Data </button>
                <button class="ui-state-default ui-corner-all" type="button" id="batalDetailPengajuan" 
                            style="height:28px; padding:2px;">Batal Penambahan Detail </button>
                <button class="ui-state-default ui-corner-all" type="button" id="tutupDetailPengajuan"  
                            style="height:28px; padding:2px;"> Tutup </button>
              </td>
          </tr>
                
        </table>
        </div>
</div>

<div id="tabs-inPutPengajuan-2" style="min-height:620px; padding:10px;">
<!-- form upload -->
<?php echo form_open_multipart('upload/index');?>
    
<p>
    <label for="Filedata">Choose a File</label><br/>
    <?php echo form_upload(array('name' => 'Filedata', 'id' => 'Pjupload'));?>
    <a href="javascript:$('#Pjupload').uploadifyUpload();">Upload File(s)</a>
</p>


<?php echo form_close();?>


<table id="list_lampiran" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
</table>
<div id="pager_lampiran" class="scroll"></div>

<div id="target">

</div>
<!-- end form upload -->
</div>
<!-- end form input -->
</div>
</div>
<!-- end tab input pengajuan -->
</div>

<div id="input_detail">
<table width="100%" border="1">
  <tr>
    <td width="378">No. Project </td>
    <td width="8">:</td>
    <td width="589"><input type="hidden" id="dtypedetail" value="" />
    	<input tabindex="1" type="text" style="width:120px;" id="det_nopj" disabled="disabled" class="input"/></td>
  </tr>
 <tr>
    <td>Kode Aktivitas</td>
    <td>:</td>
    <td><select tabindex="10" name='det_aktivitas' class='select' id="det_aktivitas" style="width:250px;">
		</select>&nbsp; <span style="font-size:9px; color:#F00">* </span></td>
  </tr>
  <tr>
    <td>Qty</td> 
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" id="det_qty" class="input"/>
    &nbsp; <span style="font-size:9px; color:#F00">* </span></td>
  </tr>
	<tr>
    <td>Satuan</td> 
    <td>:</td> 
    <td><? if(isset($dsatuan)){ echo $dsatuan; }?>&nbsp; <span style="font-size:9px; color:#F00">* </span></td>
  </tr>
 
  <tr>
    <td>Rupiah Per Satuan</td> 
    <td>:</td> 
    <td><input tabindex="3" type="text" style="width:90px;" id="det_rpsat" class="input"/>
    &nbsp; <span style="font-size:9px; color:#F00">*</span></td>
  </tr>
 <!--  <tr>
    <td>PPN</td> 
    <td>:</td>
    <td><input tabindex="3" type="checkbox" style="width:90px;" value="1" id="det_cekppn" class="input"/></td>
  </tr>
  <tr>
    <td>Rupiah PPN</td> 
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" disabled="disabled" id="det_ppn" class="input"/></td>
  </tr> -->
  <tr>
    <td>Total</td>
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" disabled="disabled" id="det_nett" class="input"/></td>
  </tr>
  <tr>
    <td colspan="3"><span style="font-size:9px; color:#F00">* tidak boleh kosong</span></td>
    </tr>
</table>
</div>





<div id="sblok">
<table width="100%" border="1">
	<tr>
    	<td colspan="3" valign="middle">
    		<span> Cari Blok : </span> <input type="text" id="isblok" class="input" onkeydown="doSearchBlock(arguments[0]||event)" />
        </td>
  	</tr>
	<tr>
    	<td colspan="3">
        	<br />
    		<table id="list_sblok" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_sblok" class="scroll"></div>
    	</td>
  	</tr>
</table>
</div>
