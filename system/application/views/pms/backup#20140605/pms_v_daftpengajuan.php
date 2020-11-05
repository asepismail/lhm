<?php 
    $template_path = base_url().$this->config->item('template_path');  
	$session = $this->session->userdata('GROUP_USER'); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';
var company = "<?=$company_code ?>";
/*####### dialog ###### */
$(function() {
  	$("#appr_tglappr").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
  	$("#confdate").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
 	$("#confdate").datepicker("setDate",new Date());
  	$("#search").hide();
  
    $("#sblok").dialog({
        bgiframe: true, autoOpen: false, height: 350, width: 550, zIndex: -3999,
        modal: false, title: "Master Blok Tanah", resizable: false, moveable: true,
		buttons: {
					'Batal': function() {
						jQuery("#i_lokasi").val("")
						$("#sblok").dialog("close");
						//$('#sblok').dialog('destroy').remove();
                    }
           } 
    }); 
	
	$("#input_dokumen").dialog({
        bgiframe: true, autoOpen: false, height: 200, width: 450, zIndex: -3999,
        modal: false, title: "Input Data Dokumen Pendukung", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						$("#input_dokumen").dialog('close');
					}, 'Simpan Dokumen': function() {
						submit_detail_document('tambah')
                    }
           } 
    });

    $("#input_detail_act").dialog({
        bgiframe: true, autoOpen: false, height: 250, width: 500,
		/* ngilangin tombol x di sisi kanan dialog */
		closeOnEscape: false, open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
        modal: true, title: "Tambah Detail Aktivitas", resizable: false, moveable: true,
		buttons: {
					'Batal': function() {
						$("#input_detail_act").dialog('close');
						init_formdetail();
                    },
					'Simpan': function() {
						submit_detail_activity();
						reloadGridActivity();
						init_formdetail();
                    }
                    
           } 
    }); 

    $("#input_detail").dialog({
        bgiframe: true, autoOpen: false, height: 670, width: 850,
		closeOnEscape: false, open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
        modal: true, title: "Detail Pengajuan", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						 deleteLog();
	                     $("#input_detail").dialog('close');       
               		},
					<?php if( $session != 'userpms' ) { ?>
						'Persetujuan Pengajuan': function() {
							<?php if( $session == '1004' ) { ?>
								$("#tpappr").val("appr_kebun");
								if( $("#appr_kebun").val() == 0 ){
									$("#appr_tglappr").datepicker("setDate",new Date());
									$("#appr_pjsementara").val($("#i_pjsementara").val());
									$("#appr_tglppj").val($("#i_tgl_ppj").val());
									$("#approval").dialog('open'); 
								} else {
									alert("data sudah disetujui sebelumnya..");
								} 
							<?php } else if( $session == '1003' ) { ?>
								$("#tpappr").val("appr0");
								if( $("#appr_kebun").val() != 0 ){
									if( $("#appr0").val() == 0 ){
										$("#appr_tglappr").datepicker("setDate",new Date());
										$("#appr_pjsementara").val($("#i_pjsementara").val());
										$("#appr_tglppj").val($("#i_tgl_ppj").val());
										$("#approval").dialog('open'); 
									} else {
										alert("data sudah disetujui sebelumnya..");
									}
								} else {
									alert("Pengajuan project belum disetujui KTU / Kapro / Administratur Site..");
								}
							<?php } else if( $session == '1002' ) { ?>
								$("#tpappr").val("appr1");
								if( $("#appr0").val() != 0 ){
									if( $("#appr1").val() == 0 ){
										$("#appr_tglappr").datepicker("setDate",new Date());
										$("#appr_pjsementara").val($("#i_pjsementara").val());
										$("#appr_tglppj").val($("#i_tgl_ppj").val());
										$("#approval").dialog('open'); 
									} else {
										alert("data sudah disetujui sebelumnya..");
									}
								} else {
									alert("Pengajuan project belum disetujui koordinator / direktur area..");
								}
							<?php } else if( $session == '1007' ) { ?>
								$("#tpappr").val("appr2");
								if( $("#appr0").val() != 0 ){
									if( $("#appr1").val() != 0 ){
										if( $("#appr2").val() == 0 ){
											$("#appr_tglappr").datepicker("setDate",new Date());
											$("#appr_pjsementara").val($("#i_pjsementara").val());
											$("#appr_tglppj").val($("#i_tgl_ppj").val());
											$("#approval").dialog('open'); 
										} else {
											alert("data sudah disetujui sebelumnya..");
										}
									} else {
										alert("Pengajuan project belum disetujui departemen terkait..");
									}
								} else {
									alert("Pengajuan project belum disetujui koordinator area dan departemen terkait..");
								}
							<?php } ?>
						},
					<?php }  ?>
					'Konfirmasi Data Pendukung': function() {
						//if( $("#appr0").val() == 0 ){
							$("#confdate").datepicker("setDate",new Date());
							$("#confpjs").val($("#i_pjsementara").val());
							$("#konfirmasi").dialog('open'); 
							reloadGridDocument($("#i_pjsementara").val());
							$(":button:contains('Konfirmasi Data Pendukung')").show();
						//} else {
						//	alert("data sudah dikonfirmasi sebelumnya..");
						//} 
                    },
					<?php  ?>
					'Cetak Form': function() {
						var ids = jQuery("#list_daftpengajuan").getGridParam('selrow'); 
						var data = $("#list_daftpengajuan").getRowData(ids) ;
						if (ids=="" || ids==null || ids==undefined){
							alert("harap pilih data terlebih dahulu...");
						} else {
							cetak(data.PROJECT_ID, 'baru', data.COMPANY_CODE);
						}
	                }, 'Koreksi': function() {
						if( $("#appr2").val() != 0 ){
							alert("Pengajuan project sudah selesai dan disetujui, pengajuan tidak dapat dikoreksi lagi !!");
						} else {
							insertLog();
							$(":button:contains('Simpan')").show();
							$(":button:contains('Batal')").show();
							$(":button:contains('Koreksi')").hide();
							$(":button:contains('Cetak Form')").hide();
							$(":button:contains('Konfirmasi Data Pendukung')").hide();
							$(":button:contains('Persetujuan Pengajuan')").hide();
							doKoreksi();
							$("#search").show();
						}
                    }, 'Batal': function() {
						deleteLog();
						$('#hist_list2').hide();
						$(":button:contains('Cetak Form')").show();
						$(":button:contains('Konfirmasi Data Pendukung')").show();
						$(":button:contains('Persetujuan Pengajuan')").show();
						$(":button:contains('Koreksi')").show();
						$(":button:contains('Simpan')").hide();
						$(":button:contains('Batal')").hide();
						$("#i_afd").attr('disabled','true'); $("#i_typepj").attr('disabled','true'); 
						$("#i_subtypepj").attr('disabled','true');
						$("#i_if_type").attr('disabled','true'); $("#i_lokasi").attr('disabled','true');
						$("#i_aktivitas").attr('disabled','true'); $("#i_subaktivitas").attr('disabled','true');
						$("#i_deskripsi").attr('disabled','true'); $("#i_start").attr('disabled','true');
						$("#i_end").attr('disabled','true'); $("#i_qty").attr('disabled','true');
						$("#i_satuan").attr('disabled','true'); $("#i_rpsat").attr('disabled','true');
						$("#i_rptotal").attr('disabled','true'); $("#i_catatan").attr('disabled','true');
						$("#isdetail").attr('disabled','true'); 
						$("#search").hide();
                    }, 'Simpan': function() {
						updateLog();
						submit_detailppj("selesai");
						doKoreksi();
						$(":button:contains('Cetak Form')").hide();
						$(":button:contains('Konfirmasi Data Pendukung')").hide();
						$(":button:contains('Persetujuan Pengajuan')").hide();
                    }
           } 
    });
	 
	$("#ppj").dialog({
        bgiframe: true, autoOpen: false, height: 670, width: 850,
        modal: true, title: "Detail Pengajuan", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
	                     $("#ppj").dialog('close');       
               		},
					<?php if( $this->session->userdata('LOGINID') <> 'userpms' ) {?>
					'Persetujuan Pengajuan': function() {
						
                    },
					<?php } ?>
					'Cetak Laporan': function() {
						
	                }
           } 
    });
	 
	$("#konfirmasi").dialog({
        bgiframe: true, autoOpen: false, height: 380, width: 600,
        modal: true, title: "Form Konfirmasi Data Pendukung", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
	                     init_konfirmasi();  
						 $("#konfirmasi").dialog('close'); 
               		},
					'Simpan Konfirmasi': function() {
						simpan_konfirmasi();
	                }
           } 
    });
	  
	$("#approval").dialog({
        bgiframe: true, autoOpen: false, height: 270, width: 550,
        modal: true, title: "Form Persetujuan Project", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
	                     $("#approval").dialog('close');       
               		},
					'Setujui Pengajuan': function() {	
						simpan_approval($("#tpappr").val());
                    }
           		} 
    }); 
	
	$("#cetak").dialog({
        bgiframe: true, autoOpen: false, height: 800, width: 1024,
        modal: true, title: "Cetak Permohonan Project", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
	                     $("#cetak").dialog('close');       
               		}
           } 
    }); 
});

function cetak(nopj, status, company){
	$("#cetak").dialog('open');
	urls = url+'pms_c_cetak/cetak_pengajuan/' + nopj + '/' + status  + '/' + company,  
	$('#frame').attr('src',urls); 
}

function hideRow(){
	var row2 = document.getElementById("if2");
	row2.style.display = 'none';
}

function displayRow(){
	var row2 = document.getElementById("if2");
	row2.style.display = '';
}

/* function pengecekan dropdown */
function setTypeLokasi(dept){
	if (dept != 0) {
			if(dept == "TNM"){
				hideRow()
				$("#search").hide();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "OP","OP"), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "NS","NS"), document.all ? i : null);
			} else if (dept == "TEK"){
				$("#search").hide();
				displayRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "IF","IF"), document.all ? i : null);
			} else if (dept == "PAB"){
				$("#search").hide();
				hideRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "PKS","PKS"), document.all ? i : null);
		}
    }  
}
/* ############## grid PPJ ################ */
/*grid*/

var jGrid_daftpengajuan = null;
var colNamesT_pengajuan = new Array();
var colModelT_pengajuan = new Array();

colNamesT_pengajuan.push('No');
colModelT_pengajuan.push({name:'PROJECT_PROP_ID',index:'PROJECT_PROP_ID', hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('No PPJ');
colModelT_pengajuan.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:false, width: 100, align:'center'});
	
colNamesT_pengajuan.push('Tgl Pengajuan');
colModelT_pengajuan.push({name:'PROJECT_PROPNUM_DATE',index:'PROJECT_PROPNUM_DATE', editable: false, 
						 hidden:false, width: 90, align:'center'});

colNamesT_pengajuan.push('Pelaksana');
colModelT_pengajuan.push({name:'PROJECT_PROPNUM_PELAKSANA',index:'PROJECT_PROPNUM_PELAKSANA', editable: false, 
						 hidden:true, width: 90, align:'center'});

colNamesT_pengajuan.push('Afd');
colModelT_pengajuan.push({name:'PROJECT_PROP_AFD',index:'PROJECT_PROP_AFD', editable: false, 
						 hidden:true, width: 40, align:'center'});

colNamesT_pengajuan.push('Tipe');
colModelT_pengajuan.push({name:'PROJECT_PROP_TYPE',index:'PROJECT_PROP_TYPE', editable: false, 
						 hidden:false, width: 40, align:'center'});

colNamesT_pengajuan.push('Kode Project');
colModelT_pengajuan.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:false, width: 90, align:'center'});

colNamesT_pengajuan.push('Subtype');
colModelT_pengajuan.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:true, width: 120, align:'center'});

colNamesT_pengajuan.push('Lokasi');
colModelT_pengajuan.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_pengajuan.push('Aktivitas');
colModelT_pengajuan.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:false, width: 65, align:'center'});

colNamesT_pengajuan.push('Sub Aktivitas');
colModelT_pengajuan.push({name:'PROJECT_PROP_SUBACTIVITY',index:'PROJECT_PROP_SUBACTIVITY', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('desc');
colModelT_pengajuan.push({name:'PROJECT_PROP_DESC',index:'PROJECT_PROP_DESC', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('start');
colModelT_pengajuan.push({name:'PROJECT_PROP_START',index:'PROJECT_PROP_START', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('end');
colModelT_pengajuan.push({name:'PROJECT_PROP_END',index:'PROJECT_PROP_END', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('Qty');
colModelT_pengajuan.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 2}, hidden:true, width: 70, align:'center'});

colNamesT_pengajuan.push('Satuan');
colModelT_pengajuan.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('Harga Satuan');
colModelT_pengajuan.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:true, width: 90, align:'right'});

colNamesT_pengajuan.push('Total');
colModelT_pengajuan.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_pengajuan.push('Detail');
colModelT_pengajuan.push({name:'ISDETAIL',index:'ISDETAIL', hidden:true, width: 80, align:'center'});

colNamesT_pengajuan.push('Kebun');
colModelT_pengajuan.push({name:'ISAPPR_ADM',index:'ISAPPR_ADM', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('Dir. Area');
colModelT_pengajuan.push({name:'ISAPPR_LVL0',index:'ISAPPR_LVL0', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('Dept');
colModelT_pengajuan.push({name:'ISAPPR_LVL1',index:'ISAPPR_LVL1', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 		formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('Direksi');
colModelT_pengajuan.push({name:'ISAPPR_LVL2',index:'ISAPPR_LVL2', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 		formatter: "checkbox", formatoptions: {disabled : true},hidden:false, width: 60, align:'center'});

colNamesT_pengajuan.push('Revisi');
colModelT_pengajuan.push({name:'ISREVISED',index:'ISREVISED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : false}, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('Close Pengajuan');
colModelT_pengajuan.push({name:'ISCLOSED',index:'ISCLOSED', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : false}, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('Status');
colModelT_pengajuan.push({name:'PROP_STATUS',index:'PROP_STATUS', editable: false, hidden:false, width: 90, align:'center'});

colNamesT_pengajuan.push('Dept');
colModelT_pengajuan.push({name:'PROJECT_DEPT',index:'PROJECT_DEPT', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('IF TYPE');
colModelT_pengajuan.push({name:'PROJECT_PROP_IFTYPE',index:'PROJECT_PROP_IFTYPE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_pengajuan.push('Site');
colModelT_pengajuan.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:false, width: 50, align:'center'});

colNamesT_pengajuan.push('Action');
colModelT_pengajuan.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
	var loadView_daftpengajuan = function(){
    jGrid_daftpengajuan = jQuery("#list_daftpengajuan").jqGrid({
        url:url+'pms_c_daftpengajuan/read_dppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pengajuan , colModel: colModelT_pengajuan ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Daftar Pengajuan Project <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 280, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_daftpengajuan").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"vwDetailPengajuan('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_daftpengajuan").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_daftpengajuan'), sortname: colModelT_pengajuan[2].name
		});
		jGrid_daftpengajuan.navGrid('#pager_daftpengajuan',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
jQuery("#list_daftpengajuan").ready(loadView_daftpengajuan);
/* ############## end grid PPJ ############ */

/* ################ form ################## */
/* fungsi klik detail pengajuan di grid */
function vwDetailPengajuan(cl){
		$('#hist_list2').hide();
		/* hidden button di dialog */
		$(":button:contains('Cetak Form')").show();
		$(":button:contains('Konfirmasi Data Pendukung')").show();
		$(":button:contains('Persetujuan Pengajuan')").show();
		$(":button:contains('Koreksi')").show();
		$(":button:contains('Simpan')").hide();
		$(":button:contains('Batal')").hide();
		
		$("#i_afd").attr('disabled','true'); $("#i_typepj").attr('disabled','true'); 
		$("#i_subtypepj").attr('disabled','true');
		$("#i_if_type").attr('disabled','true'); $("#i_lokasi").attr('disabled','true');
		$("#i_aktivitas").attr('disabled','true'); $("#i_subaktivitas").attr('disabled','true');
		$("#i_deskripsi").attr('disabled','true'); $("#i_start").attr('disabled','true');
		$("#i_end").attr('disabled','true'); $("#i_qty").attr('disabled','true');
		$("#i_satuan").attr('disabled','true'); $("#i_rpsat").attr('disabled','true');
		$("#i_rptotal").attr('disabled','true'); $("#i_catatan").attr('disabled','true');
		$("#isdetail").attr('disabled','true'); 
		
		var ids = cl; 
		var data = $("#list_daftpengajuan").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined){
			alert("harap pilih data terlebih dahulu...");
		}else{
			setTypeLokasi(data.PROJECT_DEPT);
			$("#i_afd").attr('disabled','true');
			$("#i_satuan").attr('disabled','true');
			$("#input_detail").dialog('open');
			$("#i_ino_pengajuan").val(data.PROJECT_PROPNUM_NUMID);	
			$("#i_pjsementara").val(data.PROJECT_ID);
			$("#appr_kebun").val(data.ISAPPR_ADM);
			$("#appr0").val(data.ISAPPR_LVL0);
			$("#appr1").val(data.ISAPPR_LVL1);
			$("#appr2").val(data.ISAPPR_LVL2);
			$("#comp").val(data.COMPANY_CODE);
			$("#i_ipelaksana").val(data.PROJECT_PROPNUM_PELAKSANA);
			$("#i_afd").val(data.PROJECT_PROP_AFD);
			$("#i_typepj").val(data.PROJECT_PROP_TYPE);
			$.post(url+'pms_c_daftpengajuan/getSubtipe/'+$("#i_typepj").val(), $("#i_typepj").val(), 
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
			$.post(url+'pms_c_daftpengajuan/LoadChain/'+data.PROJECT_PROP_SUBTYPE+'/', $("#i_subtypepj").val(),function(datapost){ 
				$('#i_if_type').empty();
	      		$("#i_if_type").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
          		for (var i=0; i<datapost.length; i++){
			  	 	$("#i_if_type").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
             	}
				$("#i_if_type").val(data.PROJECT_PROP_IFTYPE);
          	},"json")
			/* ##### end regenerate ################## */
			
			/* ##### regenerate aktivitas ####################### */
			$.post(url+'pms_c_daftpengajuan/get_aktivitas/'+data.PROJECT_PROP_SUBTYPE, $("#i_subtypepj").val(), 
				function(datapost){ 
					  $('#i_aktivitas').empty();
					  for (var i=0; i<datapost.length; i++){
							 $("#i_aktivitas").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					  }
					  $("#i_aktivitas").val(data.PROJECT_PROP_ACTIVITY);
			},"json")
			/* ##### end regenerate aktivitas ################### */
			/* ##### regenerate sub aktivitas ################### */
			$.post(url+'pms_c_daftpengajuan/get_subaktivitas/'+data.PROJECT_PROP_IFTYPE, $("#i_if_type").val(), 
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
			} else {
				$("#isdetail").attr('checked',false);
			}
			reloadGridActivity(data.PROJECT_ID);
		} 
}
	
function init_forminput(){
		$("#i_ino_pengajuan").val(""); $("#i_ipelaksana").val(""); $("#i_afd").val("");
		$("#appr_kebun").val(""); $("#appr0").val(""); $("#appr1").val(""); $("#appr2").val(""); $("#comp").val("");
		$("#i_typepj").val(""); $("#i_subtypepj").val(""); $("#i_if_type").val("");
		$("#i_lokasi").val(""); $("#i_aktivitas").val(""); $("#i_subaktivitas").val("");
		$("#i_deskripsi").val(""); $("#i_start").val(""); $("#i_end").val("");
		$("#i_qty").val(""); $("#i_satuan").val(""); $("#i_rpsat").val("");
		$("#i_rptotal").val(""); $("#i_catatan").val(""); $("#i_typepj").empty();
		$("#i_subtypepj").empty(); $("#i_if_type").empty(); $("#i_aktivitas").empty();
		$("#i_subaktivitas").empty();
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
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_QTY',index:'DPROJECT_PROP_QTY', editable: false, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
decimalPlaces: 0}, hidden:false, width: 90, align:'center'});

colNamesT_pengajuan_detail.push('Satuan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_UOM',index:'DPROJECT_PROP_UOM', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_pengajuan_detail.push('Rp Satuan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_VALUE',index:'DPROJECT_PROP_VALUE', editable: false, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pengajuan_detail.push('Total');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_TVALUE',index:'DPROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
decimalPlaces: 0}, width: 100, align:'center'});

colNamesT_pengajuan_detail.push('Perusahaan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_COMPANY',index:'DPROJECT_PROP_COMPANY', editable: false, hidden:true, width: 120, align:'center'});

colNamesT_pengajuan_detail.push('');
colModelT_pengajuan_detail.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView_pengajuan_detail = function(){
    jGrid_pengajuan_detail = jQuery("#list_pengajuan_detail").jqGrid({
        url:url+'pms_c_pengajuan/read_detail_ppj/'+$.trim($("#i_pjsementara").val()),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_pengajuan_detail , colModel: colModelT_pengajuan_detail ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Aktivitas Project", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        onCellSelect : function(iCol){
			
		},
        loadComplete: function(){ 
				$('#hist_list2').hide();
                var ids = jQuery("#list_pengajuan_detail").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_pengajuan_detail").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan_detail'), sortname: colModelT_pengajuan_detail[0].name
		});
		jGrid_pengajuan_detail.navGrid('#pager_pengajuan_detail',{edit:false,add:false,del:false, search: false, refresh: true});
        jGrid_pengajuan_detail.navButtonAdd('#pager_pengajuan_detail',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){ 
				submit_detailppj("simpan"); 
			}, position:"left", id:"hist_list2"
          });               
        }
	jQuery("#list_pengajuan_detail").ready(loadView_pengajuan_detail);
	
	function reloadGridActivity(ppj){
		jQuery("#list_pengajuan_detail").setGridParam({url:url+'pms_c_pengajuan/read_detail_ppj/'+ppj}).trigger("reloadGrid"); 
}
	
	/* ############ baca header pengajuan ################# */
	
	/*grid*/

var jGrid_vpengajuan = null;
var colNamesT_vpengajuan = new Array();
var colModelT_vpengajuan = new Array();

colNamesT_vpengajuan.push('ID');
colModelT_vpengajuan.push({name:'PROJECT_PROP_ID',index:'PROJECT_PROP_ID', hidden:true, width: 80, align:'center'});

colNamesT_vpengajuan.push('No PPJ');
colModelT_vpengajuan.push({name:'PROJECT_PROPNUM_NUMID',index:'PROJECT_PROPNUM_NUMID', hidden:true, width: 80, align:'center'});

colNamesT_vpengajuan.push('Complete');
colModelT_vpengajuan.push({name:'ISCOMPLETE',index:'ISCOMPLETE', editable: false, hidden:true, width: 70, align:'left'});

colNamesT_vpengajuan.push('Company');
colModelT_vpengajuan.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 80, align:'center'});

colNamesT_vpengajuan.push('Type');
colModelT_vpengajuan.push({name:'PROJECT_PROP_TYPE',index:'PROJECT_PROP_TYPE', hidden:false, width: 40, align:'center'});

colNamesT_vpengajuan.push('Subtype');
colModelT_vpengajuan.push({name:'PROJECT_PROP_SUBTYPE',index:'PROJECT_PROP_SUBTYPE', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_vpengajuan.push('IF Type');
colModelT_vpengajuan.push({name:'PROJECT_PROP_IFTYPE',index:'PROJECT_PROP_IFTYPE', editable: false, hidden:true, width: 60, align:'center'});

colNamesT_vpengajuan.push('No PJ.');
colModelT_vpengajuan.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:false, width: 90, align:'left'});

colNamesT_vpengajuan.push('AFD');
colModelT_vpengajuan.push({name:'PROJECT_PROP_AFD',index:'PROJECT_PROP_AFD', editable: false, hidden:false, width: 40, align:'center'});

colNamesT_vpengajuan.push('Keterangan');
colModelT_vpengajuan.push({name:'PROJECT_PROP_DESC',index:'PROJECT_PROP_DESC', editable: false, hidden:false, width: 180, align:'center'});

colNamesT_vpengajuan.push('Lokasi');
colModelT_vpengajuan.push({name:'PROJECT_PROP_LOCATION',index:'PROJECT_PROP_LOCATION', editable: false, hidden:false, width: 120, align:'center'});

colNamesT_vpengajuan.push('Aktivitas');
colModelT_vpengajuan.push({name:'PROJECT_PROP_ACTIVITY',index:'PROJECT_PROP_ACTIVITY', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_vpengajuan.push('Sub Aktivitas');
colModelT_vpengajuan.push({name:'PROJECT_PROP_SUBACTIVITY',index:'PROJECT_PROP_SUBACTIVITY', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_vpengajuan.push('Qty');
colModelT_vpengajuan.push({name:'PROJECT_PROP_QTY',index:'PROJECT_PROP_QTY', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_vpengajuan.push('Satuan');
colModelT_vpengajuan.push({name:'PROJECT_PROP_UOM',index:'PROJECT_PROP_UOM', editable: false, hidden:false, width: 60, align:'center'});

colNamesT_vpengajuan.push('Harga Satuan');
colModelT_vpengajuan.push({name:'PROJECT_PROP_VALUE',index:'PROJECT_PROP_VALUE', editable: false, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_vpengajuan.push('Total');
colModelT_vpengajuan.push({name:'PROJECT_PROP_TVALUE',index:'PROJECT_PROP_TVALUE', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'right'});

colNamesT_vpengajuan.push('Start');
colModelT_vpengajuan.push({name:'PROJECT_PROP_START',index:'PROJECT_PROP_START', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_vpengajuan.push('End');
colModelT_vpengajuan.push({name:'PROJECT_PROP_END',index:'PROJECT_PROP_END', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_vpengajuan.push('detail');
colModelT_vpengajuan.push({name:'ISDETAIL',index:'ISDETAIL', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_vpengajuan.push('');
colModelT_vpengajuan.push({name:'act',index:'act', editable: false, hidden:false, width: 40, align:'center'}); 
     
var loadView_vpengajuan = function(){
    jGrid_vpengajuan = jQuery("#list_vpengajuan").jqGrid({
        url:url+'pms_c_pengajuan/read_ppj/',
        mtype : "POST", datatype: "json",
        colNames: colNamesT_vpengajuan , colModel: colModelT_vpengajuan ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Detail Pengajuan <?php echo $company_dest;?>", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 180, cellEdit: false,
        loadComplete: function(){ 
                var ids = jQuery("#list_vpengajuan").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"inisialisasi('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_vpengajuan").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_pengajuan'), sortname: colModelT_vpengajuan[0].name
		});
		jGrid_vpengajuan.navGrid('#pager_pengajuan',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
jQuery("#list_vpengajuan").ready(loadView_vpengajuan);
	
	
	/* #### fungsi cek sudah selesai atau belum */
	function inisialisasi(cl){
		var postdata = {};
		$.post(url+'pms_c_daftpengajuan/cekPPJ/'+cl, postdata, function(data){
			var d = data.split('~');
				if(d[0] > 0) {
					 $("#i_no_ppj").val($.trim(d[1])); $("#i_no_ppj").attr('disabled','true');
					 $("#i_tgl_ppj").val(d[2]); $("#i_tgl_ppj").attr('disabled','true');
					 $("#i_pelaksana").val(d[3]); $("#i_pelaksana").attr('disabled','true');
					 $("#i_dept").val(d[4]); $("#i_dept").attr('disabled','true'); $("#i_afd").attr('disabled','true');
					 $("#i_satuan").attr('disabled','true');
					 $("#i_target_ppj").val(d[5]); $("#i_target_ppj").attr('disabled','true');
					jQuery("#list_vpengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(d[1])}).trigger("reloadGrid");
				}
		});
	}
	/* #### end fungsi #### */
	/* #### kirim konfirmasi #### */
	function simpan_konfirmasi(){
		var postdata={};
		postdata['PROJECT_ID'] = $("#confpjs").val() ;
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['TGL_KONFIRMASI'] = $("#confdate").val() ; 
		$.post( url+"pms_c_daftpengajuan/simpan_konfirmasi/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					//alert("data gagal tersimpan");
				} else { 
					alert("Data pengajuan berhasil dikonfirmasi");
					init_konfirmasi();
					$("#konfirmasi").dialog('close'); 
					$("#input_detail").dialog('close');
					jQuery("#list_daftpengajuan").setGridParam({url:url+'pms_c_daftpengajuan/read_dppj/'}).trigger("reloadGrid");
				};  
			});
	}
	/* #### end kirim konfirmasi #### */
	
	function init_konfirmasi(){
		$("#confpjs").val("") ;
		$("#confdate").val("") ;
	}
	
	/* #### persetujuan #### */
	function simpan_approval(type){
		var postdata={}; 
		postdata['type'] = type ;
		postdata['company'] = $("#comp").val() ;
		postdata['PROJECT_ID'] = $("#appr_pjsementara").val() ;
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['TGL_KONFIRMASI'] = $("#appr_tglappr").val() ; 
		postdata['KETERANGAN'] = $("#appr_catatan").val() ; 
		$.post( url+"pms_c_daftpengajuan/simpan_approval/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					alert("Data pengajuan berhasil disetujui");
					init_konfirmasi();
					$("#konfirmasi").dialog('close'); 
					$("#input_detail").dialog('close');
					jQuery("#list_daftpengajuan").setGridParam({url:url+'pms_c_daftpengajuan/read_dppj/'}).trigger("reloadGrid");
				};  
			});
	}
	/* #### end persetujuan #### */
	
	function init_approval(){
		$("#appr_pjsementara").val("") ;
		$("#appr_tglppj").val("") ;
		$("#appr_tglappr").val("") ; 
		$("#appr_catatan").val("") ; 
		$("#tpappr").val("")
	}
	
	/* ############### update nya ############## */
	function doKoreksi(){
		$('#hist_list2').show();
		$("#i_afd").attr('disabled',''); $("#i_typepj").attr('disabled',''); 
		$("#i_subtypepj").attr('disabled','');
		$("#i_if_type").attr('disabled',''); $("#i_lokasi").attr('disabled','');
		$("#i_aktivitas").attr('disabled',''); $("#i_subaktivitas").attr('disabled','');
		$("#i_deskripsi").attr('disabled',''); $("#i_start").attr('disabled','');
		$("#i_end").attr('disabled',''); $("#i_qty").attr('disabled','');
		$("#i_satuan").attr('disabled',''); $("#i_rpsat").attr('disabled','');
		$("#i_rptotal").attr('disabled',''); $("#i_catatan").attr('disabled','');
		$("#isdetail").attr('disabled',''); 
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
	
	/* ################# fungsi send detail dari input ke log ############################# */
	function insertLog(){
		var postdata={};
		$("#idlog").val("") ;
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ;
		postdata['PROJECT_PROP_PELAKSANA'] = $("#i_ipelaksana").val() ;
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
		if(detail==true) { detail=1;
		} else { detail=0; }
		postdata['ISDETAIL'] = detail;
		$.post( url+"pms_c_pengajuan/insert_log_koreksi/", postdata, function(message) {
			$("#idlog").val(message) ; 
		}); 
	}
	
	/* fungsi send detail dari input ke log */
	function updateLog(){
		var postdata={};
		postdata['IDLOG'] =$("#idlog").val() ;
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_ino_pengajuan").val() ;
		postdata['PROJECT_ID'] = $("#i_pjsementara").val() ;
		postdata['PROJECT_PROP_PELAKSANA'] = $("#i_ipelaksana").val() ;
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
		if(detail==true) { detail=1;
		} else { detail=0; }
		postdata['ISDETAIL'] = detail;
		$.post( url+"pms_c_pengajuan/update_log_koreksi/", postdata, function(message) {
			$("#idlog").val("") ; 
		}); 
	}
	
	/* fungsi send detail dari input ke log */
	function deleteLog(){
		var postdata={};
		postdata['IDLOG'] =$("#idlog").val() ;
		$.post( url+"pms_c_pengajuan/delete_log_koreksi/", postdata, function(message) {
			$("#idlog").val("") ; 
		}); 
	}
	
	/* ################## end log ############################ */
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
		if(detail==true) { detail=1;
		} else { detail=0; }
		postdata['ISDETAIL'] = detail;
		
		$.post( url+"pms_c_pengajuan/insert_detail/", postdata, function(message) {
			if(message.replace(/\s/g,"") != 0 ) { 
				alert("data gagal tersimpan");
			} else { 
				if(inisial=="selesai") {
					var urls = url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_ino_pengajuan").val());
					jQuery("#list_pengajuan").setGridParam({url:urls}).trigger("reloadGrid");
					$('#hist_list2').hide();
					$(":button:contains('Cetak Form')").show();
					$(":button:contains('Konfirmasi Data Pendukung')").show();
					$(":button:contains('Persetujuan Pengajuan')").show();
					$(":button:contains('Koreksi')").show();
					$(":button:contains('Simpan')").hide();
					$(":button:contains('Batal')").hide();
					$("#i_afd").attr('disabled','true'); $("#i_typepj").attr('disabled','true'); 
					$("#i_subtypepj").attr('disabled','true');
					$("#i_if_type").attr('disabled','true'); $("#i_lokasi").attr('disabled','true');
					$("#i_aktivitas").attr('disabled','true'); $("#i_subaktivitas").attr('disabled','true');
					$("#i_deskripsi").attr('disabled','true'); $("#i_start").attr('disabled','true');
					$("#i_end").attr('disabled','true'); $("#i_qty").attr('disabled','true');
					$("#i_satuan").attr('disabled','true'); $("#i_rpsat").attr('disabled','true');
					$("#i_rptotal").attr('disabled','true'); $("#i_catatan").attr('disabled','true');
					$("#isdetail").attr('disabled','true'); 
					$("#search").hide();
					jQuery("#list_daftpengajuan").setGridParam({url:url+'pms_c_daftpengajuan/read_dppj/'}).trigger("reloadGrid");
				} else if(inisial == "simpan"){ 
					addrow_detailpengajuan(); 
					init_formdetail();
					$("#input_detail_act").dialog('open');
				}
			};  
		}); 
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
				$("#input_detail_act").dialog('open');
            } else {
                alert('No Pengajuan kosong\n, silakan mengklik pengajuan baru untuk menggenerate no pengajuan project!');       						
			}
    }
		
	function init_formdetail(){
		$("#det_aktivitas").val(""); $("#det_deskripsi").val("");
		$("#det_qty").val(""); $("#det_satuan").val(""); $("#det_rpsat").val("");
		$("#det_total").val(""); $("#det_ppn").val(""); $("#det_nett").val("");
		$("#dtypedetail").val(""); 
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
	
	function reloadGridActivity(){
		jQuery("#list_pengajuan_detail").setGridParam({url:url+'pms_c_pengajuan/read_detail_ppj/'+$.trim($("#i_pjsementara").val())}).trigger("reloadGrid"); 
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
				};  
		}); 
	}
	/* ################## fungsi insert koreksi ############## */
	
	/* ################## dokumen pendukung ################## */
	/* detail aktivitas PJ */
	
	var jGrid_doc = null;
	var colNamesT_doc = new Array();
	var colModelT_doc = new Array();
	
	colNamesT_doc.push('ID');
	colModelT_doc.push({name:'ID_KONFIRMASI_DATA',index:'ID_KONFIRMASI_DATA', hidden:true, width: 80, align:'center'});
	
	colNamesT_doc.push('Kode Project');
	colModelT_doc.push({name:'PROJECT_ID',index:'PROJECT_ID', editable: false, hidden:true, width: 90, align:'left'});
	
	colNamesT_doc.push('Id konfirmasi');
	colModelT_doc.push({name:'ID_KONFIRMASI',index:'ID_KONFIRMASI', editable: false, hidden:true, width: 90, align:'left'});
	
	colNamesT_doc.push('Tgl Konfirmasi');
	colModelT_doc.push({name:'TGL_KONFIRMASI',index:'TGL_KONFIRMASI', hidden:true, 
			editable: false, edittype: "text", width: 80, align:'center'});
	
	colNamesT_doc.push('Jenis Data');
	colModelT_doc.push({name:'JNS_DATA',index:'JNS_DATA', hidden:false, width: 150, align:'center'});
	
	colNamesT_doc.push('Deskripsi');
	colModelT_doc.push({name:'DESKRIPSI',index:'DESKRIPSI', hidden:false, width: 250, align:'center'});
	
	colNamesT_doc.push('Valid');
	colModelT_doc.push({name:'ISVALID',index:'ISVALID', hidden:false,  editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, formatter: "checkbox", formatoptions: {disabled : true}, width: 60, align:'center'});
	
	colNamesT_doc.push('Action');
	colModelT_doc.push({name:'act',index:'act', editable: false, hidden:false, width: 70, align:'center'});
	
	var lastsel; var jdesc1;
	var lRow; var lCol; var i = 0;
	var loadView_doc = function(){
    jGrid_doc = jQuery("#list_doc").jqGrid({
        url:url+'pms_c_daftpengajuan/read_pendukung/'+$("#confid").val(),
        mtype : "POST", datatype: "json",
        colNames: colNamesT_doc , colModel: colModelT_doc ,
        rownumbers:true, viewrecords: true, multiselect: false, 
        caption: "Konfirmasi Kirim Dokumen", 
        rowNum:20, rowList:[10,20,30], multiple:true,
        height: 100, cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
        loadComplete: function(){ 
                var ids = jQuery("#list_doc").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                   var cl = ids[i]; 
						be = "<img style='padding-right:6px;cursor:pointer;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"updatedoc('"+cl+"');\" />"; 
						ce = "<img style='padding-right:6px;cursor:pointer;' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapusdoc('"+cl+"');\"/>";
						jQuery("#list_doc").setRowData(ids[i],{act:be+ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_doc'), sortname: colModelT_doc[0].name
		});
		jGrid_doc.navGrid('#pager_doc',{edit:false,add:false,del:false, search: false, refresh: true});
         jGrid_doc.navButtonAdd('#pager_doc',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){
				var inisial="tambah";
				submit_header_document(inisial); 
			}, position:"left"
          });               
	}
	jQuery("#list_doc").ready(loadView_doc);
	
	function addrow_dokumen(){
			init_dokumen();
            var pjs = document.getElementById("confpjs").value;
            $(":button:contains('Simpan Dokumen')").show();
			$("#input_dokumen").dialog('open');
			
    }
	
	function submit_header_document(inisial){
		var postdata={};
		postdata['PROJECT_ID'] = $("#confpjs").val() ;
		postdata['TGL_KONFIRMASI'] = $("#confdate").val() ; 
		$("#confid").val("");
		$.post( url+"pms_c_daftpengajuan/insert_header_document/", postdata, function(message) {
			if(inisial !== "simpan"){
				addrow_dokumen();
				$("#confid").val(message) ;
			}
		}); 
	}
	
	function submit_detail_document(inisial){
		var postdata={};
		postdata['PROJECT_ID'] = $("#confpjs").val() ;
		postdata['TGL_KONFIRMASI'] = $("#confdate").val() ; 
		postdata['ID_KONFIRMASI'] = $("#confid").val() ;
		postdata['JNS_DATA'] = $("#jns_data").val() ;
		postdata['DESKRIPSI'] = $("#confdesc").val() ;
		$.post( url+"pms_c_daftpengajuan/insert_detail_document/", postdata, function(message, status) {
			if(inisial !== "simpan"){
				//alert('dokumen berhasil tersimpan');
				$("#input_dokumen").dialog('close');
				init_dokumen();
				$("#input_dokumen").dialog('open');
				reloadGridDocument($("#confpjs").val());
				//alert(message);
				//addrow_dokumen();
				//$("#confid").val(message) ;
			}
				//addPengajuan();
		}); 
	}
	
	function updatedoc(){
		init_dokumen();
		var ids = jQuery("#list_doc").getGridParam('selrow');
		if(ids==null || ids==''){
			alert("harap pilih data terlebih dahulu !!!")
		} else {
			$(":button:contains('Simpan Dokumen')").show();
			var data = $("#list_doc").getRowData(ids) ;
			$("#confid").val(data.ID_KONFIRMASI) ;
			$("#jns_data").val(data.JNS_DATA) ;
			$("#confdesc").val(data.DESKRIPSI) ;
			isvalid = data.ISVALID ;
		 	if (isvalid==1) {
				$("#isvalid").attr('checked',true);
			} else {
				$("#isvalid").attr('checked',false);
			}
		 	$("#input_dokumen").dialog('open');
		}
	}
	
	function hapusdoc(id){
		var postdata={};
		if(id==null || id == ''){
			alert("harap pilih data terlebih dahulu !!!")
	   } else {
		 $.post( url+"pms_c_daftpengajuan/delete_detail_document/"+id, postdata, function(message, status) {
				reloadGridDocument($("#confpjs").val());
		 });
	   }
	}
	
	function reloadGridDocument(pjs){
		jQuery("#list_doc").setGridParam({url:url+'pms_c_daftpengajuan/read_pendukung/'+pjs}).trigger("reloadGrid"); 
	}

	function init_dokumen(){
		$("#confpjs").val(""); $("#confdate").val("");
		$("#jns_data").val(""); $("#confdesc").val("");
	}
	
	function init_dokumen(){
		$("#jns_data").val(""); $("#confdesc").val("");
		$("#isvalid").val(""); 
	}
	/* ################## end dokumen pendukung ############## */
</script>
<body>
<div style="margin-top:-25px;"><strong>Daftar Pengajuan Project Baru</strong></div>
<br/>
<?
	if($company_code=="PAG"){
?>
<div id="fcompany">
<span>Filter berdasarkan Perusahaan : <? if(isset($company)) { echo $company; } ?></span>
</div>
<br/>
<?
	}
?>

<table id="list_daftpengajuan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
            </table>
            <div id="pager_daftpengajuan" class="scroll"></div>

<!-- detail pengajuan --->

<div id="input_detail">
<table width="608" border="1">
  <tr>
    <td width="167">No. Pengajuan </td>
    <td width="4">:</td>
    <td width="415"><input tabindex="1" type="text" style="width:120px;" id="i_ino_pengajuan" disabled="disabled" class="input"/></td>
</tr>
<tr>
    <td>No. Project Sementara</td>
    <td>:</td>
    <td><input tabindex="2" type="text" style="width:120px;" id="i_pjsementara" disabled="disabled" class="input"/></td>
</tr>
<tr>
    <td>Pelaksana</td> 
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" id="i_ipelaksana" disabled="disabled" class="input"/></td>
</tr>
<tr>
    <td>AFD</td>
    <td>:</td> 
    <td><? if(isset($afd)){ echo $afd; } ?></td>
  </tr>
  <tr>
    <td>Type Project</td>
    <td>:</td> 
    <td><select tabindex="5" name='i_typepj' class='select' id="i_typepj" disabled="disabled" style="width:90px;">
    	<option value=""> -- pilih -- </option>
      </select></td>
  </tr>
  <tr>
    <td>Subtype Project</td> 
    <td>:</td>
    <td><select tabindex="6" name='i_subtypepj' class='select' id="i_subtypepj" disabled="disabled" style="width:240px;">
    	<option value=""> -- pilih -- </option>
        </select></td>
  </tr>  
  <tr id="if2">
  		<td>Sub Tipe Infrastruktur</td>
        <td>:</td> 
        <td><select tabindex="8" name='i_if_type' class='select' disabled="disabled" id="i_if_type" style="width:250px;">
			</select></td>
  </tr>
  <tr>
    <td>Lokasi</td>
    <td>:</td> 
    <td><input tabindex="9" type="text" style="width:180px;" disabled="disabled" id="i_lokasi" name="i_lokasi" class="input"/>
    <div id="search" style=" position:relative; margin-left:200px; margin-top:-16px;">
        	<img id="loadbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="searchblok()" />
        </div></td>
  </tr>
  <tr>
    <td>Aktivitas Utama</td>
    <td>:</td> 
    <td><select tabindex="10" name='i_aktivitas' disabled="disabled" class='select' id="i_aktivitas" style="width:150px;">
		</select></td>
  </tr>
  <tr>
    <td>Sub Aktivitas</td>
    <td>:</td>  
    <td><select tabindex="11" name='i_subaktivitas' disabled="disabled" class='select' id="i_subaktivitas" style="width:250px;">
		</select></td>
  </tr>
   <tr>
    <td>Deskripsi</td>
    <td>:</td>
    <td><input tabindex="12" type="text" disabled="disabled" style="width:250px;" id="i_deskripsi" class="input"/></td>
  </tr>
  <tr> 
    <td>Tgl Mulai</td>
    <td>:</td>
    <td><input tabindex="13" type="text" disabled="disabled" style="width:100px;" id="i_start" class="input"/></td>
  </tr>
  <tr>
    <td>Tgl Penyelesaian</td>
    <td>:</td>
    <td><input tabindex="14" type="text" disabled="disabled" style="width:100px;" id="i_end" class="input"/></td>
  </tr>
  <tr>
    <td>Qty</td>
    <td>:</td>
    <td><input tabindex="15" type="text" disabled="disabled" style="width:100px;" id="i_qty" class="input"/></td>
  </tr>
  <tr>
    <td>Satuan</td>
    <td>:</td>
    <td><? if(isset($satuan)){ echo $satuan; } ?></td>
  </tr>
  <tr>
    <td>Rupiah Per Satuan</td>
    <td>:</td>
    <td><input tabindex="17" type="text" disabled="disabled" style="width:120px;" id="i_rpsat" class="input"/></td>
  </tr>
  <tr>
    <td>Rupiah Total</td>
    <td>:</td>
    <td><input tabindex="18" type="text" disabled="disabled" style="width:120px;" id="i_rptotal" class="input"/></td>
  </tr>
  <tr>
    <td>Catatan</td>
    <td>:</td>
    <td><input tabindex="19" type="text" disabled="disabled" style="width:120px;" id="i_catatan" class="input"/>
    	</td>
  </tr>
  <tr>
    <td>Detail Aktivitas</td>
    <td>:</td>
    <td><input tabindex="20" disabled="disabled" type="checkbox" value="1" id="isdetail" class="input"/>
    <input type="hidden" id="appr_kebun" name="appr_kebun" value="" />
    <input type="hidden" id="appr0" name="appr0" value="" />
    <input type="hidden" id="appr1" name="appr1" value="" />
    <input type="hidden" id="appr2" name="appr2" value="" />
    <input type="hidden" id="comp" name="comp" value="" />
    <input type="hidden" id="idlog" name="idlog" value="" />
    </td>
  </tr>
  <tr>
    <td colspan="3">
    	<div id="detailgrid">
    		<table id="list_pengajuan_detail" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_pengajuan_detail" class="scroll"></div>
    	</div>
    </td>
  </tr>
</table>
</div>

<div id="cetak">
	<iframe id="frame" src="" frameborder="no" width="100%" height="100%"></iframe>
</div>

<div id="approval">
<table width="100%" border="1">
<tr>
    <td width="181">No. Project Sementara</td>
    <td width="8">:</td>
    <td width="397"><input tabindex="1" type="text" style="width:120px;" id="appr_pjsementara" disabled="disabled" class="input"/></td>
</tr>
<!-- <tr>
    <td>Tgl Pengajuan</td>  
    <td>:</td>
    <td><input tabindex="2" type="text" style="width:90px;" id="appr_tglppj" disabled="disabled" class="input"/></td>
</tr> -->
<tr>
    <td>Tgl Persetujuan</td> 
    <td>:</td>
    <td><input tabindex="3" type="text" style="width:90px;" id="appr_tglappr" disabled="disabled" class="input"/></td>
</tr>
<tr>
    <td>Catatan</td> 
    <td>:</td>
    <td><textarea tabindex="4" type="text" style="width:250px; height:70px" id="appr_catatan" class="input"></textarea>
    	<input type="hidden" id="tpappr" value="" /></td>
</tr>
</table>
</div>

<div id="ppj">
<table width="95%" >
    <tr>
      <td width="150">Departemen</td>
      <td width="20">:</td>
      <td><? if(isset($dept)) { echo $dept; } ?></td>
    </tr>
    <tr>
      <td width="150">No. Pengajuan</td>
      <td width="20">:</td>
      <td><input tabindex="2" type="text" style="width:180px;" class="input" disabled="disable" id="i_no_ppj" /></td>
    </tr>
    <tr>
      <td>Tanggal Pengajuan</td>
      <td>:</td>
      <td><input tabindex="3" type="text" style="width:120px;" class="input" id="i_tgl_ppj" /></td>
    </tr>
    <tr>
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
    </tr>
    <tr>
      <td colspan="3"><!-- ###################### isi konten ###################### -->
        <div id="tabs" style="width:99%" >
          <ul>
            <li><a href="#tabs-1">Detail Project</a></li>
            <!-- <li><a href="#tabs-2">Kontanan</a></li>
            <li><a href="#tabs-3">Tunjangan dan Potongan</a></li>
            <li><a href="#tabs-4">Natura</a></li> -->
          </ul>
          <div id="tabs-1" style="margin-left:5px;">
            <table id="list_vpengajuan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
            </table>
           <div id="pager_vpengajuan" class="scroll"></div>

        </div>
        <!-- ###################### end konten ###################### --></td>
    </tr>
    <tr>
      <td colspan="3" style="padding-right:10px; padding-top:10px;" align="right"></td>
    </tr>
  </table>
</div>

<div id="catatan">
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

<div id="input_detail_act">
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


<div id="konfirmasi">
<table width="95%" >
	 <tr>
      <td width="150">Kode Project</td>
      <td width="20">:</td>
      <td><input type="text" id="confpjs" class="input" disabled="disabled" /></td>
    </tr>
    <tr>
      <td width="150">Tanggal Konfirmasi</td>
      <td width="20">:</td>
      <td><input type="text" id="confdate" class="input" disabled="disabled" /></td>
    </tr>
    <tr>
    	<td colspan="3">
        	<table id="list_doc" class="scroll" cellpadding="0" cellspacing="0"></table>
   			<div id="pager_doc" class="scroll"></div>
    	</td>
    </tr>
</table>
</div>

<div id="input_dokumen">
<table width="100%" border="1">
  <tr>
    <td width="378">Jenis Data</td>
    <td width="8">:</td>
    <td width="589"><input type="hidden" id="confid" name="confid" />
    	<select tabindex="1" name='jns_data' class='select' id="jns_data" style="width:200px;">
            <option value=""> -- pilih -- </option>
            <option value="peta"> Peta </option>
            <option value="rab"> RAB </option>
            <option value="blueprint"> Blueprint </option>
            <option value="lain"> Lain-lain </option>
        </select>
    </td>
  </tr>
 <tr>
    <td>Deskripsi</td>
    <td>:</td>
    <td><input tabindex="2" type="text" style="width:240px;" id="confdesc" class="input"/></td>
  </tr>
  <tr>
    <td>Valid</td> 
    <td>:</td>
    <td><input tabindex="3" disabled="disabled" type="checkbox" value="1" id="isvalid" class="input"/></td>
  </tr>
</table>
</div>

</body>
</html>