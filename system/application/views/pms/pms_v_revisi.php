<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
/* init tabs */
$('#tabs-revisiPengajuan').smartTab({autoProgress: false,stopOnFocus:true,transitionEffect:'vSlide', 
						     onShowTab: function(){}
});
/* end tabs */

/*####### dialog ###### */
/* $(function() {
     $("#form_input_revisi").dialog({
        bgiframe: true, autoOpen: false, height: 670, width: 850,
        modal: true, title: "Tambah Detail Pengajuan", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						$("#i_Revtypepj").empty();
						$("#i_RevSubtypepj").empty();
						$("#i_RevIf_type").empty();
						$("#i_RevAktivitas").empty();
						$("#i_RevSubaktivitas").empty();
                        $("#form_input_revisi").dialog('close');       
               		},
					'Batalkan Pengajuan': function() {
						init_forminput();
						cancel_detailppj();
						$("#form_input_revisi").dialog('close');
                    },
					'Simpan': function() {
						var inisial = 'simpan';
						submit_detailppj(inisial);
						$("#form_input_revisi").dialog("close");
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
	$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
		var d = data.split('~');
		return $.trim(d[1]);
	});
}

$(document).ready(function() {
	var inisial="";
	//$("#search").hide();
	$("#mainForm").hide();
	$("#loaderRevisiPengajuan").hide();
	
	//inisialisasiRevisi(inisial);					   
	//alert( $.trim($("#i_no_Revppj").val()) )		
	//$("#RevDetailGrid").hide();
	$(function() {  $( "#tabs" ).tabs();  });
	//hideRow();
	
	$("#i_tgl_RevPpj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_tgl_RevPpj2").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_target_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_RevStart").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_RevEnd").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
		
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
	$("#i_RevQty").keyup(function() {
        if($("#i_RevQty").val() != "" ){
			if( $("#i_RevRpsat").val() != "" ){
				var total = $("#i_RevQty").val() * $("#i_RevRpsat").val();
				$("#i_RevRptotal").val(total)
			}
		}
    });  
	
	$("#i_RevRpsat").keyup(function() {
        if($("#i_RevQty").val() != "" ){
			if( $("#i_RevRpsat").val() != "" ){
				var total = $("#i_RevQty").val() * $("#i_RevRpsat").val();
				$("#i_RevRptotal").val(total)
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
function inisialisasiRevisi(inisial){
	var postdata = {};
	$.post(url+'pms_c_revisi/cekNotComplete/', postdata, function(data){
	/* kalau transaksi belum complete */																 
			var d = data.split('~');
			if(d[0] > 0) {
				if(inisial !== "simpan"){
				}
				 $("#i_no_Revppj").val($.trim(d[1])); $("#i_no_Revppj").attr('disabled','true');
				 $("#i_tgl_RevPpj").val(d[2]); $("#i_tgl_RevPpj").attr('disabled','true');
				 $("#i_pelaksana").val(d[3]); $("#i_pelaksana").attr('disabled','true');
				 $("#i_dept").val(d[4]); $("#i_dept").attr('disabled','true');
				 $("#i_target_ppj").val(d[5]); $("#i_target_ppj").attr('disabled','true');
				jQuery("#list_pengajuan_revisi").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(d[1])}).trigger("reloadGrid"); 			
			} else {
				$("#i_tgl_RevPpj").attr('disabled',''); 				
				/* kalau transaksi baru */
				$("#loaderRevisiPengajuan").show();
				$("#form_input_revisi").hide();
				$.post(url+'pms_c_revisi/ext_genNoPengajuan/', postdata, function(data){
					$("#i_no_Revppj").val($.trim(data));
					$("#i_no_Revppj2").val($.trim(data));
					$("#i_tgl_RevPpj").datepicker("setDate",new Date());
					$("#i_tgl_RevPpj2").datepicker("setDate",new Date());
					jQuery("#list_pengajuan_revisi").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(data)}).trigger("reloadGrid");
				});
				$("#loaderRevisiPengajuan").hide();
				$("#form_input_revisi").show();
			}
	});
}
/* #### end fungsi #### */
/*grid*/

var jGrid_pengajuanRevisi = null;
var colNamesT_pRevisi = new Array();
var colModelT_pRevisi = new Array();

colNamesT_pRevisi.push('ID');
colModelT_pRevisi.push({name:'ID',index:'PROJECT_PROP_ID', hidden:true, width: 80, align:'center'});

colNamesT_pRevisi.push('No PPJ');
colModelT_pRevisi.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:true, width: 80, align:'center'});

colNamesT_pRevisi.push('Complete');
colModelT_pRevisi.push({name:'ISCOMPLETE',index:'ISCOMPLETE', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pRevisi.push('Company');
colModelT_pRevisi.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 80, align:'center'});

colNamesT_pRevisi.push('Type');
colModelT_pRevisi.push({name:'PROJECT_PROP_TYPE',index:'PROJECT_PROP_TYPE', hidden:false, width: 40, align:'center'});

colNamesT_pRevisi.push('Subtype');
colModelT_pRevisi.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pRevisi.push('IF Type');
colModelT_pRevisi.push({name:'PROJECT_PROP_IFTYPE',index:'PROJECT_PROP_IFTYPE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pRevisi.push('No PJ.');
colModelT_pRevisi.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pRevisi.push('AFD');
colModelT_pRevisi.push({name:'PROJECT_PROP_AFD',index:'PROJECT_PROP_AFD', editable: false, hidden:false, width: 40, align:'center'});

colNamesT_pRevisi.push('Keterangan');
colModelT_pRevisi.push({name:'PROJECT_PROP_DESC',index:'PROJECT_PROP_DESC', editable: false, hidden:false, width: 180, align:'center'});

colNamesT_pRevisi.push('Lokasi');
colModelT_pRevisi.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 120, align:'center'});

colNamesT_pRevisi.push('Aktivitas');
colModelT_pRevisi.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_pRevisi.push('Sub Aktivitas');
colModelT_pRevisi.push({name:'PROJECT_PROP_SUBACTIVITY',index:'PROJECT_PROP_SUBACTIVITY', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_pRevisi.push('Qty');
colModelT_pRevisi.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_pRevisi.push('Satuan');
colModelT_pRevisi.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_pRevisi.push('Harga Satuan');
colModelT_pRevisi.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pRevisi.push('Total');
colModelT_pRevisi.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pRevisi.push('Start');
colModelT_pRevisi.push({name:'PROJECT_PROP_START',index:'PROJECT_PROP_START', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pRevisi.push('End');
colModelT_pRevisi.push({name:'PROJECT_PROP_END',index:'PROJECT_PROP_END', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pRevisi.push('detail');
colModelT_pRevisi.push({name:'ISDETAIL',index:'ISDETAIL', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pRevisi.push('');
colModelT_pRevisi.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_pengajuan = function(){
    jGrid_pengajuanRevisi = jQuery("#list_pengajuan_revisi").jqGrid({
        url:url+'pms_c_pengajuan/read_ppj/'+giveNoPJS(),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pRevisi , colModel: colModelT_pRevisi ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Pengajuan <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 160, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_pengajuan_revisi").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan_revisi").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_revisi'), sortname: colModelT_pRevisi[0].name
		});
		jGrid_pengajuanRevisi.navGrid('#pager_pengajuan_revisi',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_pengajuan_revisi").ready(loadView_pengajuan);

	/* function insert header project */
	function submit_headerppj(){
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Revppj").val() ;
		postdata['PROJECT_DEPT'] = $("#i_dept").val() ; 
		postdata['PROJECT_PROPNUM_DATE'] = $("#i_tgl_RevPpj").val() ; 
		postdata['PROJECT_FINISH_TARGET'] = $("#i_target_ppj").val() ; 
		postdata['PROJECT_PROPNUM_PELAKSANA']= $("#i_pelaksana").val();
		
		$.post( url+"pms_c_pengajuan/insert_header/", postdata, function(message) {
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
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Revppj").val() ;
		
		$.post( url+"pms_c_pengajuan/cancel_header/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasiRevisi(inisial);
				};  
			});
	}
	

/* detail aktivitas PJ */
var jGrid_pengajuanRevisi_detail = null;
var colNamesT_pRevisi_detail = new Array();
var colModelT_pRevisi_detail = new Array();

colNamesT_pRevisi_detail.push('No PPJ');
colModelT_pRevisi_detail.push({name:'PROJECT_PROPDET_ID',index:'PROJECT_PROPDET_ID', hidden:true, width: 80, align:'center'});

colNamesT_pRevisi_detail.push('Kode Project');
colModelT_pRevisi_detail.push({name:'DPROJECT_ID',index:'DPROJECT_ID', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_pRevisi_detail.push('Aktivitas');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_ACTIVITY',index:'DPROJECT_PROP_ACTIVITY', hidden:false, 
		editable: true, edittype: "text", width: 120, align:'center'});

colNamesT_pRevisi_detail.push('Deskripsi');
colModelT_pRevisi_detail.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', hidden:false, width: 200, align:'center'});

colNamesT_pRevisi_detail.push('Qty');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_QTY',index:'DPROJECT_PROP_QTY', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'center'});

colNamesT_pRevisi_detail.push('Satuan');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_UOM',index:'DPROJECT_PROP_UOM', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pRevisi_detail.push('Rp Satuan');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_VALUE',index:'DPROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pRevisi_detail.push('Total');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_TVALUE',index:'DPROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pRevisi_detail.push('Perusahaan');
colModelT_pRevisi_detail.push({name:'DPROJECT_PROP_COMPANY',index:'DPROJECT_PROP_COMPANY', editable: false, hidden:true, width: 120, align:'center'});

colNamesT_pRevisi_detail.push('');
colModelT_pRevisi_detail.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_pengajuan_detail = function(){
    jGrid_pengajuanRevisi_detail = jQuery("#list_pengajuan_revisi_detail").jqGrid({
        url:url+'pms_c_pengajuan/read_detail_ppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pRevisi_detail , colModel: colModelT_pRevisi_detail ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        onCellSelect : function(iCol){
			
		},
        loadComplete: function(){ 
                var ids = jQuery("#list_pengajuan_revisi_detail").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan_revisi_detail").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_revisi_detail'), sortname: colModelT_pRevisi_detail[0].name
		});
		jGrid_pengajuanRevisi_detail.navGrid('#pager_pengajuan_revisi_detail',{edit:false,add:false,del:false, search: false, refresh: true});
        jGrid_pengajuanRevisi_detail.navButtonAdd('#pager_pengajuan_revisi_detail',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){
				var inisial="tambah";
				submit_detailppj(inisial); 
			}, position:"left"
          });               
        }
	jQuery("#list_pengajuan_revisi_detail").ready(loadView_pengajuan_detail);
	
	/* function insert detail activity */
	function submit_detailppj(inisial){
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ; 
		postdata['PROJECT_PROP_AFD'] = $("#i_RevAfd").val() ;
		postdata['PROJECT_PROP_TYPE'] = $("#i_Revtypepj").val() ; 
		postdata['PROJECT_PROP_SUBTYPE'] = $("#i_RevSubtypepj").val() ;
		postdata['PROJECT_PROP_IFTYPE'] = $("#i_RevIf_type").val() ;  
		postdata['PROJECT_PROP_LOCATION'] = $("#i_RevLokasi").val() ;  
		postdata['PROJECT_PROP_ACTIVITY'] = $("#i_RevAktivitas").val() ; 
		postdata['PROJECT_PROP_SUBACTIVITY'] = $("#i_RevSubaktivitas").val() ;  
		postdata['PROJECT_PROP_DESC'] = $("#i_RevDeskripsi").val() ;  
		postdata['PROJECT_PROP_START'] = $("#i_RevStart").val() ;  
		postdata['PROJECT_PROP_END'] = $("#i_RevEnd").val() ;  
		postdata['PROJECT_PROP_QTY'] = $("#i_RevQty").val() ;  
		postdata['PROJECT_PROP_UOM'] = $("#i_RevSatuan").val() ;  
		postdata['PROJECT_PROP_VALUE'] = $("#i_RevRpsat").val() ;  
		postdata['PROJECT_PROP_TVALUE'] = $("#i_RevRptotal").val() ; 
		var detail = $("#isdetail").is(':checked');
		if(detail==true) {
			detail=1;
		} else {
			detail=0;
		}
		postdata['ISDETAIL'] = detail;
		
		$.post( url+"pms_c_revisi/insert_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					jQuery("#list_pengajuan_revisi").setGridParam({url:url+'pms_c_revisi/read_revproject/'+$.trim($("#i_ino_pengajuan").val())}).trigger("reloadGrid");
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
		
		$.post( url+"pms_c_pengajuan/insert_detail_act/", postdata, function(message) {
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
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Revppj").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ;
		
		$.post( url+"pms_c_pengajuan/cancel_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasiRevisi(inisial);
				};  
			});
	}
	
	/* fungsi tambah header pengajuan */
	function addPengajuan(){
		var postdata = {};
		init_forminput();
		$("#i_ino_pengajuan").val($("#i_no_Revppj").val());
		$("#i_RevEnd").val($("#i_target_ppj").val());
		$("#i_revPelaksana").val($("#i_pelaksana").val());
		$("#i_dept").val();
		$("#i_target_ppj").val();
		//$("#form_input_revisi").dialog('open');
		
		if( $("#i_pjsementara").val() == "") {
			$.post(url+'pms_c_pengajuan/ext_genPJS/', postdata, function(data){
					$("#i_pjsementara").val($.trim(data));
			});
		}
	}
	
	/* fungsi klik detail pengajuan di grid */
	function vwDetailPengajuan(cl){
		var ids = cl; 
		var data = $("#list_pengajuan_revisi").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined){
			alert("harap pilih data terlebih dahulu...");
		}else{
			init_forminput();
			
			//$("#form_input_revisi").dialog('open');
			$("#i_ino_pengajuan").val(data.PROJECT_PROPNUM_NUMID);	
			$("#i_pjsementara").val(data.PROJECT_ID);
			$("#i_revPelaksana").val($("#i_pelaksana").val());
			$("#i_RevAfd").val(data.PROJECT_PROP_AFD);
			$("#i_Revtypepj").val(data.PROJECT_PROP_TYPE);
					
			
			$("#i_RevSubtypepj").val(data.PROJECT_PROP_SUBTYPE);
			$("#i_RevLokasi").val(data.PROJECT_PROP_LOCATION);
			$("#i_RevDeskripsi").val(data.PROJECT_PROP_DESC);
			$("#i_RevStart").val(data.PROJECT_PROP_START);
			$("#i_RevEnd").val(data.PROJECT_PROP_END);
			$("#i_RevQty").val(data.PROJECT_PROP_QTY);
			$("#i_RevSatuan").val(data.PROJECT_PROP_UOM);
			$("#i_RevRpsat").val(data.PROJECT_PROP_VALUE);
			$("#i_RevRptotal").val(data.PROJECT_PROP_TVALUE);
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
		$("#i_ino_pengajuan").val(""); $("#i_revPelaksana").val(""); $("#i_RevAfd").val("");
		$("#i_Revtypepj").val(""); $("#i_RevSubtypepj").val(""); $("#i_RevIf_type").val("");
		$("#i_RevLokasi").val(""); $("#i_RevAktivitas").val(""); $("#i_RevSubaktivitas").val("");
		$("#i_RevDeskripsi").val(""); $("#i_RevStart").val(""); $("#i_RevEnd").val("");
		$("#i_RevQty").val(""); $("#i_RevSatuan").val(""); $("#i_RevRpsat").val("");
		$("#i_RevRptotal").val(""); $("#i_RevCatatan").val(""); $("#i_Revtypepj").empty();
		$("#i_RevSubtypepj").empty(); $("#i_RevIf_type").empty(); $("#i_RevAktivitas").empty();
		$("#i_RevSubaktivitas").empty();
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
            var ids = jQuery("#list_pengajuan_revisi_detail").getDataIDs();
            var i = ids.length;
            if (ppj != ""){
				$("#det_nopj").val($("#i_pjsementara").val());
				$("#dtypedetail").val($("#i_RevSubtypepj").val()); 
				ddpjactivity();
				reloadGridActivity();
				$("#input_detail").dialog('open');
            } else {
                alert('No Pengajuan kosong\n, silakan mengklik pengajuan baru untuk menggenerate no pengajuan project!');       						
			}
        }
		function reloadGridActivity(){
			 jQuery("#list_pengajuan_revisi_detail").setGridParam({url:url+'pms_c_pengajuan/read_detail_ppj/'+$.trim($("#i_pjsementara").val())}).trigger("reloadGrid"); 
		}

function AjukanRevisi(){
	var answer = confirm ("Pengajuan untuk revisi project : " + $("#sRevProject").val() + "?" )
    if (answer) {
		var inisial="";
		inisialisasiRevisi(inisial);
		
		
		
		var idRevProject = jQuery("#list_sRevProject").getGridParam('selrow');
		if (idRevProject)	{
			var retRevProject = jQuery("#list_sRevProject").getRowData(idRevProject);
			jQuery("#RevAfd").val(retRevProject.AFD);
			jQuery("#i_Revtypepj").val(retRevProject.PROJECT_TYPE);
			jQuery("#i_RevSubtypepj").val(retRevProject.PROJECT_SUBTYPE);
			jQuery("#i_RevDeskripsi").val(retRevProject.PROJECT_DESC);
			jQuery("#i_RevLokasi").val(retRevProject.PROJECT_LOCATION);
			jQuery("#i_revPelaksana").val(retRevProject.KODE_PELAKSANA);
			jQuery("#i_RevAktivitas").val(retRevProject.PROJECT_ACTIVITY);
			jQuery("#i_RevRptotal").val(retRevProject.PROJECT_VALUE);
			jQuery("#i_RevQty").val(retRevProject.PROJECT_QTY);
			jQuery("#i_RevSatuan").val(retRevProject.PROJECT_UOM);
			var rpSat = retRevProject.PROJECT_VALUE / retRevProject.PROJECT_QTY
			jQuery("#i_RevRpsat").val(rpSat);
			$('#tabs-revisiPengajuan').smartTab('showTab',1);
				//jQuery("#sRevProject").val(ret.PROJECT_ID)
			//$("#sFormProject").dialog("close");
			//showMainForm();
		}
	}
}
		
function selesai(){
	var answer = confirm ("Selesai pengajuan project : " + $("#i_no_Revppj").val() + "?" )
    if (answer) {
		var postdata={};
		var inisial="";
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_Revppj").val() ;
		$.post(url+'pms_c_pengajuan/selesai/', postdata, function(message){
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					alert("data tersimpan");
					inisialisasiRevisi(inisial);
					jQuery("#list_pengajuan_revisi").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_no_Revppj").val())}).trigger("reloadGrid");
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
        var afd = jQuery("#i_RevAfd").val();
		var q = jQuery("#isblok").val();
        if (q == ""){ q = "-"; } 
        jQuery("#list_RevSblok").setGridParam({url:url+"pms_c_pengajuan/read_blok/"+afd+"/"+q}).trigger("reloadGrid");        
    } 
	
	function doSearchProject(ev){ 
        if(timeoutProject) 
            clearTimeout(timeoutProject) 
            timeoutProject = setTimeout(gridProjectReload,500) 
    } 
    
    function gridProjectReload(){ 
       var q = jQuery("#isRevProject").val();
       jQuery("#list_sRevProject").setGridParam({url:url+"pms_c_revisi/listProject/"+q}).trigger("reloadGrid");        
    } 
	
	/* detail aktivitas PJ */
	var jGridrevisi_sblok = null;
	var colNamesT_sRevblok = new Array();
	var colModelT_sRevblok = new Array();
	
	colNamesT_sRevblok.push('ID');
	colModelT_sRevblok.push({name:'BID',index:'BID', hidden:true, width: 80, align:'center'});
	
	colNamesT_sRevblok.push('Kode Blok');
	colModelT_sRevblok.push({name:'BLOCKID',index:'BLOCKID', editable: false, hidden:false, width: 90, align:'left'});
	
	colNamesT_sRevblok.push('Afd');
	colModelT_sRevblok.push({name:'ESTATECODE',index:'ESTATECODE', hidden:false, 
			editable: false, edittype: "text", width: 80, align:'center'});
	
	colNamesT_sRevblok.push('Deskripsi');
	colModelT_sRevblok.push({name:'DESCRIPTION',index:'DESCRIPTION', hidden:false, width: 200, align:'center'});
	
	colNamesT_sRevblok.push('Company');
	colModelT_sRevblok.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:false, width: 90, align:'left'});
	
	var lastsel; var jdesc1;
	var lRow; var lCol; var i = 0;
	var loadView_sblok = function(){
    jGridrevisi_sblok = jQuery("#list_RevSblok").jqGrid({
        url:url+'pms_c_pengajuan/read_blok/'+$.trim($("#i_pjsementara").val()),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_sRevblok , colModel: colModelT_sRevblok ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        ondblClickRow: function(){
			var id = jQuery("#list_RevSblok").getGridParam('selrow');
			if (id)	{
					var ret = jQuery("#list_RevSblok").getRowData(id);
					jQuery("#i_RevLokasi").val(ret.BLOCKID)
					$("#sblok").dialog("close");
			}
		}, loadComplete: function(){ 
                var ids = jQuery("#list_RevSblok").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_RevSblok").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_RevSblok'), sortname: colModelT_sRevblok[0].name
		});
		jGridrevisi_sblok.navGrid('#pager_RevSblok',{edit:false,add:false,del:false, search: false, refresh: true});
        jGridrevisi_sblok.navButtonAdd('#pager_RevSblok',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){ 
				var inisial = "tambah";
				submit_detailppj(inisial); 
			}, position:"left"
          });               
        }
	jQuery("#list_RevSblok").ready(loadView_sblok);
	
	function searchRevBlok(){
		var afd = $("#i_RevAfd").val();
		if( afd == ""){
			alert("silakan pilih kode afdeling terlebih dahulu..!!!")
		} else {
			$("#sblok").dialog("open");
			gridBlokReload();
		}
	}
	
	/* ######### cari project ################## */
 	var grid_sRevProject = null;
    var colNames_sRevProject = new Array();
    var colModel_sRevProject = new Array(); 
    
    colNames_sRevProject.push('id');
    colModel_sRevProject.push({name:'ID',index:'ID', editable: true,hidden:true, width: 30, align:'center'});

    colNames_sRevProject.push('Kode Project');
    colModel_sRevProject.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: true,hidden:false, width: 40, align:'center'});
            
    colNames_sRevProject.push('Afd');
    colModel_sRevProject.push({name:'AFD',index:'AFD', editable: true,hidden:false, width: 20, align:'center'});
            
    colNames_sRevProject.push('Tipe');
    colModel_sRevProject.push({name:'PROJECT_TYPE',index:'PROJECT_TYPE', editable: true,hidden:false, width: 20, align:'center'});

    colNames_sRevProject.push('Subtipe');
    colModel_sRevProject.push({name:'PROJECT_SUBTYPE',index:'PROJECT_SUBTYPE',  editable: true,hidden:true, width:30, align:'left'});
	
	colNames_sRevProject.push('Deskripsi');
    colModel_sRevProject.push({name:'PROJECT_DESC',index:'PROJECT_DESC', editable: true,hidden:false, width:120, align:'left'});
	
	colNames_sRevProject.push('Lokasi');
    colModel_sRevProject.push({name:'PROJECT_LOCATION',index:'PROJECT_LOCATION', editable: true,hidden:false, 
						   width:60, align:'center'});

   	colNames_sRevProject.push('Aktivitas');
    colModel_sRevProject.push({name:'PROJECT_ACTIVITY',index:'PROJECT_ACTIVITY', editable: true,hidden:false, 
						   width: 35, align:'center'});
	
	colNames_sRevProject.push('Aktif');
    colModel_sRevProject.push({name:'PROJECT_STATUS',index:'PROJECT_STATUS', 
    			hidden:false, editable: false, edittype:'checkbox', editoptions: { value:"1:0"},
  				formatter: "checkbox", formatoptions: {disabled : true}, width: 20, align:'center'});
	
	colNames_sRevProject.push('Tgl Terbit');
    colModel_sRevProject.push({name:'TGL_TERBIT',index:'TGL_TERBIT', editable: true,hidden:false, width: 40, align:'center'});
	
	colNames_sRevProject.push('COMPANY_CODE');
    colModel_sRevProject.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sRevProject.push('AFD');
    colModel_sRevProject.push({name:'AFD',index:'AFD', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sRevProject.push('KODE_PELAKSANA');
    colModel_sRevProject.push({name:'KODE_PELAKSANA',index:'KODE_PELAKSANA', editable: true,hidden:true, width: 50, align:'center'});
	colNames_sRevProject.push('PROJECT_QTY');
    colModel_sRevProject.push({name:'PROJECT_QTY',index:'PROJECT_QTY', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sRevProject.push('PROJECT_UOM');
    colModel_sRevProject.push({name:'PROJECT_UOM',index:'PROJECT_UOM', editable: true,hidden:true, width: 50, align:'center'});
	
	colNames_sRevProject.push('PROJECT_VALUE');
    colModel_sRevProject.push({name:'PROJECT_VALUE',index:'PROJECT_VALUE', editable: true,hidden:true, width: 50, align:'center'});
	var loadView_sRevProject = function(){
        jgrid_sRevProject = jQuery("#list_sRevProject").jqGrid({
            url:url+'pms_c_revisi/listProject/',  
            datatype: 'json',  mtype: 'POST', colNames:colNames_sRevProject, colModel:colModel_sRevProject,
            pager: jQuery('#pager_sRevProject'),  rownumbers: true, rowNum: 20, width:650, 
            height: 200, sortorder: "asc", forceFit : true, rowList:[10,20,30], 
            multiple:false, ondblClickRow: function(){
				var id = jQuery("#list_sRevProject").getGridParam('selrow');
				if (id)	{
					var ret = jQuery("#list_sRevProject").getRowData(id);
					jQuery("#sRevProject").val(ret.PROJECT_ID)
					jQuery("#sRevProject2").val(ret.PROJECT_ID)
					$("#sFormProject").dialog("close");
					showMainForm();
				}
			}, caption: 'Daftar Project', editurl:url+'pms_c_revisi/listProject/',
			loadComplete: function(){}, sortname: colModel_sRevProject[1].name,  sortorder: "desc",  viewrecords: true    
        });
        jgrid_sRevProject.navGrid('#pager_sRevProject',{edit:false,del:false,add:false, search: false, refresh: true});
        $("#alertmod").remove();//FIXME         
    }
    jQuery("#list_sRevProject").ready(loadView_sRevProject);
	
	function OpenSearchProject(){
		 $("#sFormProject").dialog('open');  
		 $("#mainForm").hide(1000);
	}
	
	function showMainForm(){
		//inisialisasiRevisi("simpan");
		$("#mainForm").slideDown(1500);
	}
	/* ######### end cari projcet ############## */
</script>


<div id="tabs-revisiPengajuan" style="min-height: 480px;">
    <ul>
        <li><a id="liRev-1" href="#tabs-revisiPengajuan-1">Cari Project</a></li>
        <li><a id="liRev-2" href="#tabs-revisiPengajuan-2">Detail Pengajuan Revisi</a></li>
    </ul>
	<div id="tabs-revisiPengajuan-1" style="min-height:480px; padding:10px;">
    <h2>Daftar Pengajuan Project</h2>
    	<br />
    	<span>
          Kode Project : <input tabindex="2" type="text" style="width:100px;" class="input" disabled="disable" id="sRevProject" />
          <div id="searchRevProject" style=" position:relative; margin-left:205px; margin-top:-16px;">
            <img id="searchRevisiButton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="OpenSearchProject()" />&nbsp;&nbsp;
            <img id="loadRevisibutton" src="<?= $template_path ?>themes/base/images/Reloader.png" style="cursor:pointer;" onclick="" />
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
              <td width="150">No. Pengajuan Revisi</td>
              <td width="20">:</td>
              <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disable" id="i_no_Revppj" /></td>
            </tr>
            <tr>
              <td>Tanggal Pengajuan Revisi</td>
              <td>:</td>
              <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_RevPpj" /><br/><br/></td>
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
                    <table id="list_pengajuan_revisi" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
                    </table>
                   <div id="pager_pengajuan_revisi" class="scroll"></div>
        
                </div>
                <!-- ###################### end konten ###################### --></td>
            </tr>
            <tr>
              <td colspan="3" style="padding-right:10px; padding-top:10px;" align="right">
                <input type="button"  id="pRevAjukan" value="Pengajuan Revisi" onclick="AjukanRevisi()" 
                        class="ui-state-default ui-corner-all" style="height:28px; padding:2px;"/>
                <input type="button"  id="pRevBatal" value="Batal"  style="height:28px; padding:2px;" 
                        class="ui-state-default ui-corner-all" onclick="cancel_headerppj()" /></td>
            </tr>
          </table></td>
        </tr>
        </table>
        
        
       
        <div>
    </div>
    
    <div id="tabs-revisiPengajuan-2" style="min-height:620px;">
    <h2>Detail Pengajuan</h2>
    <div id="loaderRevisiPengajuan">
     		<img src="<?= $template_path ?>themes_pms/img/loader6.gif" />
    </div>
     
     <div id="form_input_revisi" style="padding:10px;">
<table width="95%" border="1" style="color: #666666; font-size:95%; ">
  <tr style="border-bottom: 1px solid #DDD;">
    <td  height="22">No. Project Revisi</td>
    <td><input tabindex="2" type="text" style="width:100px;" class="input" disabled="disabled" id="sRevProject2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td width="187"  height="22">No. Pengajuan Revisi</td>
    <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disabled" id="i_no_Revppj2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Tanggal Pengajuan Revisi</td>
    <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_RevPpj2" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Pelaksana</td> 
    <td width="300">
    <input type="text" tabindex="4" "i_revPelaksana" id="i_revPelaksana" rows="5" class="input" disabled="disabled" />
              
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
    <td><input type="text" class="input" tabindex="5" name='i_Revtypepj' id="i_Revtypepj" style="width:90px;" disabled="disabled" />
    </td>
    <td>&nbsp;</td>
    <td>Subtype Project</td>
    <td><input type="text" class="input" tabindex="6" name='i_RevSubtypepj' id="i_RevSubtypepj" style="width:240px;" disabled="disabled" />
      </td> 
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Sub Tipe Infrastruktur</td> 
    <td><input type="text" tabindex="8" name='i_RevIf_type' class='input' id="i_RevIf_type" style="width:250px;" disabled="disabled">
    </select></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>  
  <tr style="border-bottom: 1px solid #DDD;">
  		<td height="22">Lokasi</td>
        <td><input tabindex="9" type="text" style="width:180px;" id="i_RevLokasi" name="i_RevLokasi" class="input" disabled="disabled"/>
      <!-- <div id="searchRevBlok" style=" position:relative; margin-left:200px; margin-top:-16px;">
        <img id="loadRevisiBlokbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="searchRevBlok()" /> 
      </div>--></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td> 
        </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Aktivitas Utama</td>
    <td><input type="text" tabindex="10" name='i_RevAktivitas' class='input' id="i_RevAktivitas" style="width:150px;" disabled="disabled" /></td>
    <td>&nbsp;</td>
    <td>Sub Aktivitas</td>
    <td><input type="text" tabindex="11" name='i_RevSubaktivitas' class='input' id="i_RevSubaktivitas" style="width:250px;" disabled="disabled">
    </td> 
  </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Deskripsi</td>
    <td><input tabindex="12" type="text" style="width:250px;" id="i_RevDeskripsi" class="input"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>  
    </tr>
   <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Tgl Mulai</td>
    <td><input tabindex="13" type="text" style="width:100px;" id="i_RevStart" class="input"/></td>
    <td>&nbsp;</td>
    <td>Tgl Penyelesaian</td>
    <td><input tabindex="14" type="text" style="width:100px;" id="i_RevEnd" class="input"/></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;"> 
    <td height="22">Qty</td>
    <td><input tabindex="15" type="text" style="width:100px;" id="i_RevQty" class="input"/></td>
    <td>&nbsp;</td>
    <td>Satuan</td>
    <td><? if(isset($RevSatuan)){ echo $RevSatuan; } ?></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Rupiah Per Satuan</td>
    <td><input tabindex="17" type="text" style="width:120px;" id="i_RevRpsat" class="input"/></td>
    <td>&nbsp;</td>
    <td>Rupiah Total</td>
    <td><input tabindex="18" type="text" style="width:120px;" id="i_RevRptotal" class="input"/></td>
    </tr>
  <tr style="border-bottom: 1px solid #DDD;">
    <td height="22">Catatan</td>
    <td><input tabindex="19" type="text" style="width:120px;" id="i_RevCatatan" class="input"/></td>
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
    		<table id="list_pengajuan_revisi_detail" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_pengajuan_revisi_detail" class="scroll"></div>
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
    		<table id="list_RevSblok" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_RevSblok" class="scroll"></div>
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
    		<span> Cari Project : </span> <input type="text" id="isRevProject" class="input" onkeydown="doSearchProject(arguments[0]||event)" />
        </td>
  	</tr>
	<tr>
    	<td colspan="3">
        	<br />
    		<table id="list_sRevProject" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_sRevProject" class="scroll"></div>
    	</td>
  	</tr>
</table>
</div>