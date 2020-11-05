<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
/* init tabs */
$('#tabs-closePengajuan').smartTab({autoProgress: false,stopOnFocus:true,transitionEffect:'vSlide', 
						     onShowTab: function(){}
});
/* end tabs */

/*####### dialog ###### */
/* $(function() {
     $("#form_input_close").dialog({
        bgiframe: true, autoOpen: false, height: 670, width: 850,
        modal: true, title: "Tambah Detail Pengajuan", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						$("#i_Closetypepj").empty();
						$("#i_CloseSubtypepj").empty();
						$("#i_CloseIf_type").empty();
						$("#i_CloseAktivitas").empty();
						$("#i_CloseSubaktivitas").empty();
                        $("#form_input_close").dialog('close');       
               		},
					'Batalkan Pengajuan': function() {
						init_forminput();
						cancel_detailppj();
						$("#form_input_close").dialog('close');
                    },
					'Simpan': function() {
						var inisial = 'simpan';
						submit_detailppj(inisial);
						$("#form_input_close").dialog("close");
						init_forminput();
						inisialisasi(inisial);
                    }
           } 
     }); 
}); */

$(function() {
     $("#sblok").dialog({
        bgiframe: true, autoOpen: false, height: 350, width: 550, zIndex: -3999,
        modal: false, title: "Master Blok Tanah", resizable: false, moveable: true,
		buttons: {	'Batal': function() {
					$("#sblok").dialog("close");
              }
          } 
     }); 
});

$(function() {
     $("#sFormProject").dialog({
        bgiframe: true, autoOpen: false, height: 450, width: 700, zIndex: -3999,
        modal: false, title: "Master Blok Tanah", resizable: false, moveable: true,
		buttons: {	'Batal': function() {
					$("#sFormProject").dialog("close");
              }
          } 
     }); 
});

$(function() {
     $("#input_detail").dialog({
        bgiframe: true, autoOpen: false, height: 250, width: 500,
        modal: true, title: "Tambah Detail Aktivitas", resizable: false, moveable: true,
		buttons: {
					'Batal': function() {
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

var lrtcomplete = "Data project sebelumnya belum dicomplete,\nMohon selesaikan atau batalkan transaksi sebelumnya untuk membuat pengajuan baru";


function giveNoPJS(){
	var postdata = {};
	$.post(url+'pms_c_closing/cekNotComplete/', postdata, function(data){
		var d = data.split('~');
		return $.trim(d[1]);
	});
}

$(document).ready(function() {
	var inisial="";
	//$("#search").hide();
	$("#mainForm").hide();
	$("#loaderClosePengajuan").hide();
	
	//inisialisasiClose(inisial);					   
	//alert( $.trim($("#i_no_Closeppj").val()) )		
	//$("#RevDetailGrid").hide();
	$(function() {  $( "#tabs" ).tabs();  });
	//hideRow();
	
	$("#i_tgl_ClosePpj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_tgl_ClosePpj2").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_target_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_CloseStart").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_CloseEnd").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
		
	/* cekbox detail */
	/* $("#isdetail").change(function() {
		var cek = $(this).is(":checked");
		if(cek == true){
			$("#RevDetailGrid").show();
		} else {
			$("#RevDetailGrid").hide();
		}
	}); */
		
	/* hitung rupiah total saat entri qty dan rp satuan */
	$("#i_CloseQty").keyup(function() {
        if($("#i_CloseQty").val() != "" ){
			if( $("#i_CloseRpsat").val() != "" ){
				var total = $("#i_CloseQty").val() * $("#i_CloseRpsat").val();
				$("#i_CloseRptotal").val(total)
			}
		}
    });  
	
	$("#i_CloseRpsat").keyup(function() {
        if($("#i_CloseQty").val() != "" ){
			if( $("#i_CloseRpsat").val() != "" ){
				var total = $("#i_CloseQty").val() * $("#i_CloseRpsat").val();
				$("#i_CloseRptotal").val(total)
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
	
});	
/* #### fungsi cek sudah selesai atau belum */
function inisialisasiClose(inisial){
	var postdata = {};
	$.post(url+'pms_c_closing/cekNotComplete/', postdata, function(data){
	/* kalau transaksi belum complete */																 
			var d = data.split('~');
			if(d[0] > 0) {
				if(inisial !== "simpan"){
				}
				 $("#i_no_Closeppj").val($.trim(d[1])); $("#i_no_Closeppj").attr('disabled','true');
				 $("#i_tgl_ClosePpj").val(d[2]); $("#i_tgl_ClosePpj").attr('disabled','true');
				 $("#i_pelaksana").val(d[3]); $("#i_pelaksana").attr('disabled','true');
				 $("#i_dept").val(d[4]); $("#i_dept").attr('disabled','true');
				 $("#i_target_ppj").val(d[5]); $("#i_target_ppj").attr('disabled','true');
				jQuery("#list_pengajuan_close").setGridParam({url:url+'pms_c_closing/read_ppj_close/'+$.trim(d[1])}).trigger("reloadGrid"); 			
			} else {
				$("#i_tgl_ClosePpj").attr('disabled',''); 				
				/* kalau transaksi baru */
				$("#loaderClosePengajuan").show();
				$("#form_input_close").hide();
				$.post(url+'pms_c_closing/ext_genNoPengajuan/', postdata, function(data){
					$("#i_no_Closeppj").val($.trim(data));
					$("#i_no_Closeppj2").val($.trim(data));
					$("#i_tgl_ClosePpj").datepicker("setDate",new Date());
					$("#i_tgl_ClosePpj2").datepicker("setDate",new Date());
					jQuery("#list_pengajuan_close").setGridParam({url:url+'pms_c_closing/read_ppj_close/'+$.trim(data)}).trigger("reloadGrid");
				});
				$("#loaderClosePengajuan").hide();
				$("#form_input_close").show();
			}
	});
}
/* #### end fungsi #### */
/*grid*/

var jGrid_pengajuanClose = null;
var colNamesT_pClose = new Array();
var colModelT_pClose = new Array();

colNamesT_pClose.push('ID');
colModelT_pClose.push({name:'ID',index:'PROJECT_PROP_ID', hidden:true, width: 80, align:'center'});

colNamesT_pClose.push('No PPJ');
colModelT_pClose.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:true, width: 80, align:'center'});

colNamesT_pClose.push('Complete');
colModelT_pClose.push({name:'ISCOMPLETE',index:'ISCOMPLETE', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pClose.push('Company');
colModelT_pClose.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 80, align:'center'});

colNamesT_pClose.push('Type');
colModelT_pClose.push({name:'PROJECT_PROP_TYPE',index:'PROJECT_PROP_TYPE', hidden:false, width: 40, align:'center'});

colNamesT_pClose.push('Subtype');
colModelT_pClose.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pClose.push('IF Type');
colModelT_pClose.push({name:'PROJECT_PROP_IFTYPE',index:'PROJECT_PROP_IFTYPE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pClose.push('No PJ.');
colModelT_pClose.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pClose.push('AFD');
colModelT_pClose.push({name:'PROJECT_PROP_AFD',index:'PROJECT_PROP_AFD', editable: false, hidden:false, width: 40, align:'center'});

colNamesT_pClose.push('Keterangan');
colModelT_pClose.push({name:'PROJECT_PROP_DESC',index:'PROJECT_PROP_DESC', editable: false, hidden:false, width: 180, align:'center'});

colNamesT_pClose.push('Lokasi');
colModelT_pClose.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 120, align:'center'});

colNamesT_pClose.push('Aktivitas');
colModelT_pClose.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_pClose.push('Sub Aktivitas');
colModelT_pClose.push({name:'PROJECT_PROP_SUBACTIVITY',index:'PROJECT_PROP_SUBACTIVITY', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_pClose.push('Qty');
colModelT_pClose.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_pClose.push('Satuan');
colModelT_pClose.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pClose.push('Harga Satuan');
colModelT_pClose.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pClose.push('Total');
colModelT_pClose.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pClose.push('Start');
colModelT_pClose.push({name:'PROJECT_PROP_START',index:'PROJECT_PROP_START', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pClose.push('End');
colModelT_pClose.push({name:'PROJECT_PROP_END',index:'PROJECT_PROP_END', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pClose.push('detail');
colModelT_pClose.push({name:'ISDETAIL',index:'ISDETAIL', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pClose.push('');
colModelT_pClose.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_pengajuan = function(){
    jGrid_pengajuanClose = jQuery("#list_pengajuan_close").jqGrid({
        url:url+'pms_c_closing/read_ppj_close/'+giveNoPJS(),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pClose , colModel: colModelT_pClose ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Pengajuan <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 160, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_pengajuan_close").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan_close").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_close'), sortname: colModelT_pClose[0].name
		});
		jGrid_pengajuanClose.navGrid('#pager_pengajuan_close',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_pengajuan_close").ready(loadView_pengajuan);

	/* function insert header project */
	function submit_headerppj(){
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Closeppj").val() ;
		postdata['PROJECT_DEPT'] = $("#i_dept").val() ; 
		postdata['PROJECT_PROPNUM_DATE'] = $("#i_tgl_ClosePpj").val() ; 
		postdata['PROJECT_FINISH_TARGET'] = $("#i_target_ppj").val() ; 
		postdata['PROJECT_PROPNUM_PELAKSANA']= $("#i_pelaksana").val();
		
		$.post( url+"pms_c_closing/insert_header/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					addPengajuan();
				};  
			});
	}
	
	function cancel_headerppj(){
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Closeppj").val() ;
		
		$.post( url+"pms_c_closing/cancel_header/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasiClose(inisial);
				};  
			});
	}
	

/* detail aktivitas PJ */
var jGrid_pengajuanClose_detail = null;
var colNamesT_pClose_detail = new Array();
var colModelT_pClose_detail = new Array();

colNamesT_pClose_detail.push('No PPJ');
colModelT_pClose_detail.push({name:'PROJECT_PROPDET_ID',index:'PROJECT_PROPDET_ID', hidden:true, width: 80, align:'center'});

colNamesT_pClose_detail.push('Kode Project');
colModelT_pClose_detail.push({name:'DPROJECT_ID',index:'DPROJECT_ID', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pClose_detail.push('Aktivitas');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_ACTIVITY',index:'DPROJECT_PROP_ACTIVITY', hidden:false, 
		editable: true, edittype: "text", width: 120, align:'center'});

colNamesT_pClose_detail.push('Deskripsi');
colModelT_pClose_detail.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', hidden:false, width: 200, align:'center'});

colNamesT_pClose_detail.push('Qty');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_QTY',index:'DPROJECT_PROP_QTY', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'center'});

colNamesT_pClose_detail.push('Satuan');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_UOM',index:'DPROJECT_PROP_UOM', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pClose_detail.push('Rp Satuan');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_VALUE',index:'DPROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pClose_detail.push('Total');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_TVALUE',index:'DPROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pClose_detail.push('Perusahaan');
colModelT_pClose_detail.push({name:'DPROJECT_PROP_COMPANY',index:'DPROJECT_PROP_COMPANY', editable: false, hidden:true, width: 120, align:'center'});

colNamesT_pClose_detail.push('');
colModelT_pClose_detail.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_pengajuan_detail = function(){
    jGrid_pengajuanClose_detail = jQuery("#list_pengajuan_close_detail").jqGrid({
        url:url+'pms_c_closing/read_detail_ppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pClose_detail , colModel: colModelT_pClose_detail ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        onCellSelect : function(iCol){
			
		},
        loadComplete: function(){ 
                var ids = jQuery("#list_pengajuan_close_detail").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan_close_detail").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_close_detail'), sortname: colModelT_pClose_detail[0].name
		});
		jGrid_pengajuanClose_detail.navGrid('#pager_pengajuan_close_detail',{edit:false,add:false,del:false, search: false, refresh: true});
        jGrid_pengajuanClose_detail.navButtonAdd('#pager_pengajuan_close_detail',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){
				var inisial="tambah";
				submit_detailppj(inisial); 
			}, position:"left"
          });               
        }
	jQuery("#list_pengajuan_close_detail").ready(loadView_pengajuan_detail);
	
	/* function insert detail activity */
	function submit_detailppj(inisial){
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ; 
		postdata['PROJECT_PROP_AFD'] = $("#i_CloseAfd").val() ;
		postdata['PROJECT_PROP_TYPE'] = $("#i_Closetypepj").val() ; 
		postdata['PROJECT_PROP_SUBTYPE'] = $("#i_CloseSubtypepj").val() ;
		postdata['PROJECT_PROP_IFTYPE'] = $("#i_CloseIf_type").val() ;  
		postdata['PROJECT_PROP_LOCATION'] = $("#i_CloseLokasi").val() ;  
		postdata['PROJECT_PROP_ACTIVITY'] = $("#i_CloseAktivitas").val() ; 
		postdata['PROJECT_PROP_SUBACTIVITY'] = $("#i_CloseSubaktivitas").val() ;  
		postdata['PROJECT_PROP_DESC'] = $("#i_CloseDeskripsi").val() ;  
		postdata['PROJECT_PROP_START'] = $("#i_CloseStart").val() ;  
		postdata['PROJECT_PROP_END'] = $("#i_CloseEnd").val() ;  
		postdata['PROJECT_PROP_QTY'] = $("#i_CloseQty").val() ;  
		postdata['PROJECT_PROP_UOM'] = $("#i_CloseSatuan").val() ;  
		postdata['PROJECT_PROP_VALUE'] = $("#i_CloseRpsat").val() ;  
		postdata['PROJECT_PROP_TVALUE'] = $("#i_CloseRptotal").val() ; 
		var detail = $("#isdetail").is(':checked');
		if(detail==true) {
			detail=1;
		} else {
			detail=0;
		}
		postdata['ISDETAIL'] = detail;
		
		$.post( url+"pms_c_closing/insert_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					jQuery("#list_pengajuan_close").setGridParam({url:url+'pms_c_closing/read_Closeproject/'+$.trim($("#i_ino_pengajuan").val())}).trigger("reloadGrid");
					if(inisial !== "simpan"){
						addrow_detailpengajuan();
					}
					//addPengajuan();
				};  
			}); 
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
		
		$.post( url+"pms_c_closing/insert_detail_act/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					reloadGridActivity();
					init_formdetail();
					//addrow_detailpengajuan();
					//addPengajuan();
				};  
			}); 
	}
	
	function cancel_detailppj(){
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Closeppj").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ;
		
		$.post( url+"pms_c_closing/cancel_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasiClose(inisial);
				};  
			});
	}
	
	/* fungsi tambah header pengajuan */
	function addPengajuan(){
		var postdata = {};
		init_forminput();
		$("#i_ino_pengajuan").val($("#i_no_Closeppj").val());
		$("#i_CloseEnd").val($("#i_target_ppj").val());
		$("#i_ClosePelaksana").val($("#i_pelaksana").val());
		$("#i_dept").val();
		$("#i_target_ppj").val();
		//$("#form_input_close").dialog('open');
		
		if( $("#i_pjsementara").val() == "") {
			$.post(url+'pms_c_closing/ext_genPJS/', postdata, function(data){
					$("#i_pjsementara").val($.trim(data));
			});
		}
	}
	
	/* fungsi klik detail pengajuan di grid */
	function vwDetailPengajuan(cl){
		var ids = cl; 
		var data = $("#list_pengajuan_close").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined){
			alert("harap pilih data terlebih dahulu...");
		}else{
			init_forminput();
			
			//$("#form_input_close").dialog('open');
			$("#i_ino_pengajuan").val(data.PROJECT_PROPNUM_NUMID);	
			$("#i_pjsementara").val(data.PROJECT_ID);
			$("#i_ClosePelaksana").val($("#i_pelaksana").val());
			$("#i_CloseAfd").val(data.PROJECT_PROP_AFD);
			$("#i_Closetypepj").val(data.PROJECT_PROP_TYPE);
					
			
			$("#i_CloseSubtypepj").val(data.PROJECT_PROP_SUBTYPE);
			$("#i_CloseLokasi").val(data.PROJECT_PROP_LOCATION);
			$("#i_CloseDeskripsi").val(data.PROJECT_PROP_DESC);
			$("#i_CloseStart").val(data.PROJECT_PROP_START);
			$("#i_CloseEnd").val(data.PROJECT_PROP_END);
			$("#i_CloseQty").val(data.PROJECT_PROP_QTY);
			$("#i_CloseSatuan").val(data.PROJECT_PROP_UOM);
			$("#i_CloseRpsat").val(data.PROJECT_PROP_VALUE);
			$("#i_CloseRptotal").val(data.PROJECT_PROP_TVALUE);
			var detail = data.ISDETAIL;
			/* if (detail==1) {
				$("#isdetail").attr('checked',true);
				$("#RevDetailGrid").show("fast");
			} else {
				$("#isdetail").attr('checked',false);
				$("#RevDetailGrid").hide("fast");
			} */
			reloadGridActivity();
		} 
	}
	
	function init_forminput(){
		$("#i_ino_pengajuan").val(""); $("#i_ClosePelaksana").val(""); $("#i_CloseAfd").val("");
		$("#i_Closetypepj").val(""); $("#i_CloseSubtypepj").val(""); $("#i_CloseIf_type").val("");
		$("#i_CloseLokasi").val(""); $("#i_CloseAktivitas").val(""); $("#i_CloseSubaktivitas").val("");
		$("#i_CloseDeskripsi").val(""); $("#i_CloseStart").val(""); $("#i_CloseEnd").val("");
		$("#i_CloseQty").val(""); $("#i_CloseSatuan").val(""); $("#i_CloseRpsat").val("");
		$("#i_CloseRptotal").val(""); $("#i_CloseCatatan").val(""); $("#i_Closetypepj").empty();
		$("#i_CloseSubtypepj").empty(); $("#i_CloseIf_type").empty(); $("#i_CloseAktivitas").empty();
		$("#i_CloseSubaktivitas").empty();
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
            var ids = jQuery("#list_pengajuan_close_detail").getDataIDs();
            var i = ids.length;
            if (ppj != ""){
				$("#det_nopj").val($("#i_pjsementara").val());
				$("#dtypedetail").val($("#i_CloseSubtypepj").val()); 
				ddpjactivity();
				reloadGridActivity();
				$("#input_detail").dialog('open');
            } else {
                alert('No Pengajuan kosong\n, silakan mengklik pengajuan baru untuk menggenerate no pengajuan project!');       						
			}
        }
		function reloadGridActivity(){
			 jQuery("#list_pengajuan_close_detail").setGridParam({url:url+'pms_c_closing/read_detail_ppj/'+$.trim($("#i_pjsementara").val())}).trigger("reloadGrid"); 
		}

function AjukanClose(){
	var answer = confirm ("Pengajuan untuk close project : " + $("#sCloseProject").val() + "?" )
    if (answer) {
		var inisial="";
		inisialisasiClose(inisial);
		
		var idRevProject = jQuery("#list_sCloseProject").getGridParam('selrow');
		if (idRevProject)	{
			var retRevProject = jQuery("#list_sCloseProject").getRowData(idRevProject);
			jQuery("#RevAfd").val(retRevProject.AFD);
			jQuery("#i_Closetypepj").val(retRevProject.PROJECT_TYPE);
			jQuery("#i_CloseSubtypepj").val(retRevProject.PROJECT_SUBTYPE);
			jQuery("#i_CloseDeskripsi").val(retRevProject.PROJECT_DESC);
			jQuery("#i_CloseLokasi").val(retRevProject.PROJECT_LOCATION);
			jQuery("#i_ClosePelaksana").val(retRevProject.KODE_PELAKSANA);
			jQuery("#i_CloseAktivitas").val(retRevProject.PROJECT_ACTIVITY);
			jQuery("#i_CloseRptotal").val(retRevProject.PROJECT_VALUE);
			jQuery("#i_CloseQty").val(retRevProject.PROJECT_QTY);
			jQuery("#i_CloseSatuan").val(retRevProject.PROJECT_UOM);
			var rpSat = retRevProject.PROJECT_VALUE / retRevProject.PROJECT_QTY
			jQuery("#i_CloseRpsat").val(rpSat);
			$('#tabs-closePengajuan').smartTab('showTab',1);
				//jQuery("#sCloseProject").val(ret.PROJECT_ID)
			//$("#sFormProject").dialog("close");
			//showMainForm();
		}
	}
}
		
function selesai(){
	var answer = confirm ("Selesai pengajuan project : " + $("#i_no_Closeppj").val() + "?" )
    if (answer) {
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Closeppj").val() ;
		$.post(url+'pms_c_closing/selesai/', postdata, function(message){
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					alert("data tersimpan");
					inisialisasiClose(inisial);
					jQuery("#list_pengajuan_close").setGridParam({url:url+'pms_c_closing/read_ppj_close/'+$.trim($("#i_no_Closeppj").val())}).trigger("reloadGrid");
				};  
		});
	}
}

/* cari blok */
	var timeoutBlok; var flAuto = false; var timeoutProject;
    
    function doSearchBlock(ev){ 
        if(timeoutBlok) 
            clearTimeout(timeoutBlok) 
            timeoutBlok = setTimeout(gridBlokReload,500) 
    } 
    
    function gridBlokReload(){ 
        var afd = jQuery("#i_CloseAfd").val();
		var q = jQuery("#isblok").val();
        if (q == ""){ q = "-"; } 
        jQuery("#list_RevSblok").setGridParam({url:url+"pms_c_closing/read_blok/"+afd+"/"+q}).trigger("reloadGrid");        
    } 
	
	function doSearchProject(ev){ 
        if(timeoutProject) 
            clearTimeout(timeoutProject) 
            timeoutProject = setTimeout(gridProjectReload,500) 
    } 
    
    function gridProjectReload(){ 
       var q = jQuery("#isCloseProject").val();
       jQuery("#list_sCloseProject").setGridParam({url:url+"pms_c_closing/listProject/"+q}).trigger("reloadGrid");        
    } 
	
	/* detail aktivitas PJ */
	var jGridclose_sblok = null;
	var colNamesT_sCloseblok = new Array();
	var colModelT_sCloseblok = new Array();
	
	colNamesT_sCloseblok.push('ID');
	colModelT_sCloseblok.push({name:'BID',index:'BID', hidden:true, width: 80, align:'center'});
	
	colNamesT_sCloseblok.push('Kode Blok');
	colModelT_sCloseblok.push({name:'BLOCKID',index:'BLOCKID', editable: false, hidden:false, width: 90, align:'left'});
	
	colNamesT_sCloseblok.push('Afd');
	colModelT_sCloseblok.push({name:'ESTATECODE',index:'ESTATECODE', hidden:false, 
			editable: false, edittype: "text", width: 80, align:'center'});
	
	colNamesT_sCloseblok.push('Deskripsi');
	colModelT_sCloseblok.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, width: 200, align:'center'});
	
	colNamesT_sCloseblok.push('Company');
	colModelT_sCloseblok.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 90, align:'left'});
	
	var lastsel; var jdesc1;
	var lRow; var lCol; var i = 0;
	var loadView_sblok = function(){
    jGridclose_sblok = jQuery("#list_RevSblok").jqGrid({
        url:url+'pms_c_closing/read_blok/'+$.trim($("#i_pjsementara").val()),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_sCloseblok , colModel: colModelT_sCloseblok ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        ondblClickRow: function(){
			var id = jQuery("#list_RevSblok").getGridParam('selrow');
			if (id)	{
					var ret = jQuery("#list_RevSblok").getRowData(id);
					jQuery("#i_CloseLokasi").val(ret.BLOCKID)
					$("#sblok").dialog("close");
			}
		}, loadComplete: function(){ 
                var ids = jQuery("#list_RevSblok").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_RevSblok").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_RevSblok'), sortname: colModelT_sCloseblok[0].name
		});
		jGridclose_sblok.navGrid('#pager_RevSblok',{edit:false,add:false,del:false, search: false, refresh: true});
        jGridclose_sblok.navButtonAdd('#pager_RevSblok',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){ 
				var inisial = "tambah";
				submit_detailppj(inisial); 
			}, position:"left"
          });               
        }
	jQuery("#list_RevSblok").ready(loadView_sblok);
	
	function searchRevBlok(){
		var afd = $("#i_CloseAfd").val();
		if( afd == ""){
			alert("silakan pilih kode afdeling terlebih dahulu..!!!")
		} else {
			$("#sblok").dialog("open");
			gridBlokReload();
		}
	}
	
	/* ######### cari project ################## */
 	var grid_sCloseProject = null;
    var colNames_sCloseProject = new Array();
    var colModel_sCloseProject = new Array(); 
    
    colNames_sCloseProject.push('id');
    colModel_sCloseProject.push({name:'ID',index:'ID', editable: true,hidden:true, width: 30, align:'center'});

    colNames_sCloseProject.push('Kode Project');
    colModel_sCloseProject.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: true,hidden:false, width: 40, align:'center'});
            
    colNames_sCloseProject.push('Afd');
    colModel_sCloseProject.push({name:'AFD',index:'AFD', editable: true,hidden:false, width: 20, align:'center'});
            
    colNames_sCloseProject.push('Tipe');
    colModel_sCloseProject.push({name:'PROJECT_TYPE',index:'PROJECT_TYPE', editable: true,hidden:false, width: 20, align:'center'});

    colNames_sCloseProject.push('Subtipe');
    colModel_sCloseProject.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE',  editable: true,hidden:true, width:30, align:'left'});
	
	colNames_sCloseProject.push('Deskripsi');
    colModel_sCloseProject.push({name:'PROJECT_DESC',index:'PROJECT_DESC', editable: true,hidden:false, width:120, align:'left'});
	
	colNames_sCloseProject.push('Lokasi');
    colModel_sCloseProject.push({name:'PROJECT_LOCATION',index:'PROJECT_LOCATION', editable: true,hidden:false, 
						   width:60, align:'center'});

   	colNames_sCloseProject.push('Aktivitas');
    colModel_sCloseProject.push({name:'PROJECT_ACTIVITY',index:'PROJECT_ACTIVITY', editable: true,hidden:false, 
						   width: 35, align:'center'});
	
	colNames_sCloseProject.push('Aktif');
    colModel_sCloseProject.push({name:'PROJECT_STATUS',index:'PROJECT_STATUS', 
    			hidden:false, editable: false, edittype:'checkbox', editoptions: { value:"1:0"},
  				formatter: "checkbox", formatoptions: {disabled : true}, width: 20, align:'center'});
	
	colNames_sCloseProject.push('Tgl Terbit');
    colModel_sCloseProject.push({name:'TGL_TERBIT',index:'TGL_TERBIT', editable: true,hidden:false, width: 40, align:'center'});
	
	colNames_sCloseProject.push('COMPANY_CODE');
    colModel_sCloseProject.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sCloseProject.push('AFD');
    colModel_sCloseProject.push({name:'AFD',index:'AFD', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sCloseProject.push('KODE_PELAKSANA');
    colModel_sCloseProject.push({name:'KODE_PELAKSANA',index:'KODE_PELAKSANA', editable: true,hidden:true, width: 50, align:'center'});
	colNames_sCloseProject.push('PROJECT_QTY');
    colModel_sCloseProject.push({name:'PROJECT_QTY',index:'PROJECT_QTY', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sCloseProject.push('PROJECT_UOM');
    colModel_sCloseProject.push({name:'PROJECT_UOM',index:'PROJECT_UOM', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sCloseProject.push('PROJECT_VALUE');
    colModel_sCloseProject.push({name:'PROJECT_VALUE',index:'PROJECT_VALUE', editable: true,hidden:true, width: 50, align:'center'});
	var loadView_sCloseProject = function(){
        jgrid_sCloseProject = jQuery("#list_sCloseProject").jqGrid({
            url:url+'pms_c_closing/listProject/',  
            datatype: 'json',  mtype: 'POST', colNames:colNames_sCloseProject, colModel:colModel_sCloseProject,
            pager: jQuery('#pager_sCloseProject'),  rownumbers: true, rowNum: 20, width:650, 
            height: 200, sortorder: "asc", forceFit : true, rowList:[10,20,30], 
            multiple:false, ondblClickRow: function(){
				var id = jQuery("#list_sCloseProject").getGridParam('selrow');
				if (id)	{
					var ret = jQuery("#list_sCloseProject").getRowData(id);
					jQuery("#sCloseProject").val(ret.PROJECT_ID)
					jQuery("#sCloseProject2").val(ret.PROJECT_ID)
					$("#sFormProject").dialog("close");
					showMainForm();
				}
			}, caption: 'Daftar Project', editurl:url+'pms_c_closing/listProject/',
			loadComplete: function(){}, sortname: colModel_sCloseProject[1].name,  sortorder: "desc",  viewrecords: true    
        });
        jgrid_sCloseProject.navGrid('#pager_sCloseProject',{edit:false,del:false,add:false, search: false, refresh: true});
        $("#alertmod").remove();//FIXME         
    }
    jQuery("#list_sCloseProject").ready(loadView_sCloseProject);
	
	function OpenSearchProject(){
		 $("#sFormProject").dialog('open');  
		 $("#mainForm").hide(1000);
	}
	
	function showMainForm(){
		//inisialisasiClose("simpan");
		$("#mainForm").slideDown(1500);
	}
	/* ######### end cari projcet ############## */
</script>


<div id="tabs-closePengajuan" style="min-height: 480px;">
    <ul>
        <li><a id="liRev-1" href="#tabs-closePengajuan-1">Cari Project</a></li>
        <li><a id="liRev-2" href="#tabs-closePengajuan-2"> Pengajuan Penutupan Project</a></li>
    </ul>
	<div id="tabs-closePengajuan-1" style="min-height:480px; padding:10px;">
    <h2>Daftar Pengajuan Penutupan Project</h2>
    	<br />
    	<span>
          Kode Project : <input tabindex="2" type="text" style="width:100px;" class="input" disabled="disable" id="sCloseProject" />
          <div id="searchRevProject" style=" position:relative; margin-left:205px; margin-top:-16px;">
            <img id="searchCloseButton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="OpenSearchProject()" />&nbsp;&nbsp;
            <img id="loadClosebutton" src="<?= $template_path ?>themes/base/images/Reloader.png" style="cursor:pointer;" onclick="" />
          </div>
        </span>
        <div id="mainForm">
        <table width="95%">
        <tr>
          <td><table width="95%" >
            <!-- <tr>
              <td width="150">Departemen</td>
              <td width="20">:</td>
              <td><? if(isset($dept)) { echo $dept; } ?></td>
            </tr> --> 
            <tr>
              <td width="150">No. Pengajuan Close</td>
              <td width="20">:</td>
              <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disable" id="i_no_Closeppj" /></td>
            </tr>
            <tr>
              <td>Tanggal Pengajuan Close</td>
              <td>:</td>
              <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_ClosePpj" /><br/><br/></td>
            </tr>
            <!-- <tr>
              <td width="150">Target Penyelesaian</td>
              <td width="20">:</td>
              <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_target_ppj" /></td>
            </tr>
            <tr>
              <td width="150">Pelaksana</td>
              <td width="20">:</td>
              <td><select tabindex="4" class="validate[required] select" name="i_pelaksana" id="i_pelaksana" cols="45" rows="5">
                <option value=""> -- pilih -- </option>
                <option value="swakelola">Swakelola</option>
                <option value="kontraktor">Kontraktor</option>
              </select></td>
            </tr> -->
            <tr>
              <td colspan="3"><!-- ###################### isi konten ###################### -->
                <div id="tabs" style="width:99%" >
                  <ul>
                    <li><a href="#tabs-1">Detail Project</a></li>
                   
                  </ul>
                  <div id="tabs-1" style="margin-left:5px;">
                    <table id="list_pengajuan_close" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
                    </table>
                   <div id="pager_pengajuan_close" class="scroll"></div>
        
                </div>
                <!-- ###################### end konten ###################### --></td>
            </tr>
            <tr>
              <td colspan="3" style="padding-right:10px; padding-top:10px;" align="right">
                <input type="button"  id="pRevAjukan" value="Pengajuan Close" onclick="AjukanClose()" 
                        class="ui-state-default ui-corner-all" style="height:28px; padding:2px;"/>
                <input type="button"  id="pRevBatal" value="Batal"  style="height:28px; padding:2px;" 
                        class="ui-state-default ui-corner-all" onclick="cancel_headerppj()" /></td>
            </tr>
          </table></td>
        </tr>
        </table>
        
        
       
        <div>
    </div>
    
    <div id="tabs-closePengajuan-2" style="min-height:620px;">
    <h2>Detail Pengajuan</h2>
    <div id="loaderClosePengajuan">
     		<img src="<?= $template_path ?>themes_pms/img/loader6.gif" />
    </div>
     
     <div id="form_input_close" style="padding:10px;">
<table width="95%" border="1" style="color: #666666; font-size:95%; ">
  <tr style="border-bottom: 1px solid #DDD;">
    <td  height="22">No. Project Closing</td>
    <td><input tabindex="2" type="text" style="width:100px;" class="input" disabled="disabled" id="sCloseProject2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td width="187"  height="22">No. Pengajuan Closing</td>
    <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disabled" id="i_no_Closeppj2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Tanggal Pengajuan Closing</td>
    <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_ClosePpj2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Pelaksana</td> 
    <td width="300">
    <input type="text" tabindex="4" "i_ClosePelaksana" id="i_ClosePelaksana" rows="5" class="input" disabled="disabled" />
              
    </td>
    <td width="71">&nbsp;</td>
    <td width="143">&nbsp;</td>
    <td width="285">&nbsp;</td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">AFD</td>
    <td><? if(isset($RevAfd)){ echo $RevAfd; } ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td> 
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Type Project</td>
    <td><input type="text" class="input" tabindex="5" name='i_Closetypepj' id="i_Closetypepj" style="width:90px;" disabled="disabled" />
    </td>
    <td>&nbsp;</td>
    <td>Subtype Project</td>
    <td><input type="text" class="input" tabindex="6" name='i_CloseSubtypepj' id="i_CloseSubtypepj" style="width:240px;" disabled="disabled" />
      </td> 
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Sub Tipe Infrastruktur</td> 
    <td><input type="text" tabindex="8" name='i_CloseIf_type' class='input' id="i_CloseIf_type" style="width:250px;" disabled="disabled">
    </select></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>  
  <tr style="border-bottom: 1px solid #DDD;">
  		<td height="22">Lokasi</td>
        <td><input tabindex="9" type="text" style="width:180px;" id="i_CloseLokasi" name="i_CloseLokasi" class="input" disabled="disabled"/>
      <!-- <div id="searchRevBlok" style=" position:relative; margin-left:200px; margin-top:-16px;">
        <img id="loadRevisiBlokbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="searchRevBlok()" /> 
      </div>--></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td> 
        </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Aktivitas Utama</td>
    <td><input type="text" tabindex="10" name='i_CloseAktivitas' class='input' id="i_CloseAktivitas" style="width:150px;" disabled="disabled" /></td>
    <td>&nbsp;</td>
    <td>Sub Aktivitas</td>
    <td><input type="text" tabindex="11" name='i_CloseSubaktivitas' class='input' id="i_CloseSubaktivitas" style="width:250px;" disabled="disabled">
    </td> 
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Deskripsi</td>
    <td><input tabindex="12" type="text" style="width:250px;" id="i_CloseDeskripsi" class="input"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>  
    </tr>
   <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Tgl Mulai</td>
    <td><input tabindex="13" type="text" style="width:100px;" id="i_CloseStart" class="input"/></td>
    <td>&nbsp;</td>
    <td>Tgl Penyelesaian</td>
    <td><input tabindex="14" type="text" style="width:100px;" id="i_CloseEnd" class="input"/></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;"> 
    <td height="22">Qty</td>
    <td><input tabindex="15" type="text" style="width:100px;" id="i_CloseQty" class="input"/></td>
    <td>&nbsp;</td>
    <td>Satuan</td>
    <td><? if(isset($RevSatuan)){ echo $RevSatuan; } ?></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Rupiah Per Satuan</td>
    <td><input tabindex="17" type="text" style="width:120px;" id="i_CloseRpsat" class="input"/></td>
    <td>&nbsp;</td>
    <td>Rupiah Total</td>
    <td><input tabindex="18" type="text" style="width:120px;" id="i_CloseRptotal" class="input"/></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Catatan</td>
    <td><input tabindex="19" type="text" style="width:120px;" id="i_CloseCatatan" class="input"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td height="22">Detail Aktivitas</td>
    <td><input tabindex="20" type="checkbox" value="1" id="isdetail" class="input"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">
    	<div id="RevDetailGrid">
    		<table id="list_pengajuan_close_detail" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_pengajuan_close_detail" class="scroll"></div>
    	</div>
    </td>
  </tr>
  <tr>
    <td colspan="5" align="right">
    	 <br /> 
    	 <button class="ui-state-default ui-corner-all" type="button" id="simpanDetailPengajuanRev"
                    	style="height:28px; padding:2px;" > Simpan Data </button>
         <button class="ui-state-default ui-corner-all" type="button" id="batalDetailPengajuanRev" 
                        style="height:28px; padding:2px;">Batal Penambahan Detail </button>
         <button class="ui-state-default ui-corner-all" type="button" id="tutupDetailPengajuanRev"  
                        style="height:28px; padding:2px;"> Tutup </button>
                      
    </td>
  </tr>
</table>
</div>

	</div>
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
		</select></td>
  </tr>
  <tr>
    <td>Qty</td> 
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" id="det_qty" class="input"/></td>
  </tr>
	<tr>
    <td>Satuan</td> 
    <td>:</td> 
    <td><? if(isset($dsatuan)){ echo $dsatuan; }?></td>
  </tr>
 
  <tr>
    <td>Rupiah Per Satuan</td> 
    <td>:</td> 
    <td><input tabindex="3" type="text" style="width:90px;" id="det_rpsat" class="input"/></td>
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
    		<table id="list_CloseSblok" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_CloseSblok" class="scroll"></div>
    	</td>
  	</tr>
</table>
</div>

</div>
</div>


<div id="sFormProject">
<table width="100%" border="1">
	<tr>
    	<td colspan="3" valign="middle">
    		<span> Cari Project : </span> <input type="text" id="isCloseProject" class="input" onkeydown="doSearchProject(arguments[0]||event)" />
        </td>
  	</tr>
	<tr>
    	<td colspan="3">
        	<br />
    		<table id="list_sCloseProject" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_sCloseProject" class="scroll"></div>
    	</td>
  	</tr>
</table>
</div>