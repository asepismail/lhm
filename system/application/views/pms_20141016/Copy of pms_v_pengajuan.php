<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images'; 

/*####### dialog ###### */
$(function() {
     $("#form_input").dialog({
        bgiframe: true, autoOpen: false, height: 670, width: 850,
        modal: true, title: "Tambah Detail Pengajuan", resizable: false, moveable: true,
		buttons: {
					'Tutup': function() {
						$("#i_typepj").empty();
						$("#i_subtypepj").empty();
						$("#i_if_type").empty();
						$("#i_aktivitas").empty();
						$("#i_subaktivitas").empty();
                        $("#form_input").dialog('close');       
               		},
					'Batalkan Pengajuan': function() {
						init_forminput();
						cancel_detailppj();
						$("#form_input").dialog('close');
                    },
					'Simpan': function() {
						var inisial = 'simpan';
						submit_detailppj(inisial);
						$("#form_input").dialog("close");
						init_forminput();
						inisialisasi(inisial);
                    }
                    
           } 
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

function hideRow(){
	var row2 = document.getElementById("if2");
	row2.style.display = 'none';
}

function displayRow(){
	var row2 = document.getElementById("if2");
	row2.style.display = '';
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
	inisialisasi(inisial);					   
	//alert( $.trim($("#i_no_ppj").val()) )		
	$("#detailgrid").hide();
	$(function() {  $( "#tabs" ).tabs();  });
	hideRow();
	setTypeLokasi();
	 //$("#i_tgl_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_tgl_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_target_ppj").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_start").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
	$("#i_end").datepicker({dateFormat:"yy-mm-dd" , changeMonth: true, changeYear: true, yearRange: '2011:2015' });
		
	/* cekbox detail */
	$("#isdetail").change(function() {
		var cek = $(this).is(":checked");
		if(cek == true){
			$("#detailgrid").show();
		} else {
			$("#detailgrid").hide();
		}
	});
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
	
});	
/* #### fungsi cek sudah selesai atau belum */
function inisialisasi(inisial){
	var postdata = {};
	$.post(url+'pms_c_pengajuan/cekNotComplete/', postdata, function(data){
	/* kalau transaksi belum complete */																 
			var d = data.split('~');
			if(d[0] > 0) {
				if(inisial !== "simpan"){
				 	//alert(lrtcomplete);
				}
				 $("#i_no_ppj").val($.trim(d[1])); $("#i_no_ppj").attr('disabled','true');
				 $("#i_tgl_ppj").val(d[2]); $("#i_tgl_ppj").attr('disabled','true');
				 $("#i_pelaksana").val(d[3]); $("#i_pelaksana").attr('disabled','true');
				 $("#i_dept").val(d[4]); $("#i_dept").attr('disabled','true');
				 $("#i_target_ppj").val(d[5]); $("#i_target_ppj").attr('disabled','true');
				jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(d[1])}).trigger("reloadGrid");
			
			} else {
				$("#i_tgl_ppj").attr('disabled',''); $("#i_pelaksana").attr('disabled','');
				$("#i_dept").attr('disabled',''); $("#i_target_ppj").attr('disabled','');
				/* kalau transaksi baru */
				$.post(url+'pms_c_pengajuan/ext_genNoPengajuan/', postdata, function(data){
					$("#i_no_ppj").val($.trim(data));
					$("#i_tgl_ppj").datepicker("setDate",new Date());
					jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim(data)}).trigger("reloadGrid");
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
		var postdata={};
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
		postdata['PROJECT_DEPT'] = $("#i_dept").val() ; 
		postdata['PROJECT_PROPNUM_DATE'] = $("#i_tgl_ppj").val() ; 
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
		postdata['PROJECT_PROPNUM_NUMID'] = $("#i_no_ppj").val() ;
		
		$.post( url+"pms_c_pengajuan/cancel_header/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					inisialisasi(inisial);
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
			} else if (dept == "TEK"){
				$("#search").hide();
				displayRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "IF","IF"), document.all ? i : null);
				$("#i_lokasi").attr('disabled','false');
			} else if (dept == "PAB"){
				$("#search").hide();
				hideRow();
				$("#i_typepj").empty();
				$("#i_typepj").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				$("#i_typepj").get(0).add(new Option( "PKS","PKS"), document.all ? i : null);
				$("#i_lokasi").attr('disabled','false');
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

colNamesT_pengajuan_detail.push('Perusahaan');
colModelT_pengajuan_detail.push({name:'DPROJECT_PROP_COMPANY',index:'DPROJECT_PROP_COMPANY', editable: false, hidden:true, width: 120, align:'center'});

colNamesT_pengajuan_detail.push('');
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
        onCellSelect : function(iCol){
			
		},
        loadComplete: function(){ 
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
				var inisial="tambah";
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
		
		$.post( url+"pms_c_pengajuan/insert_detail/", postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert("data gagal tersimpan");
				} else { 
					jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_ino_pengajuan").val())}).trigger("reloadGrid");
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
		init_forminput();
		$("#i_ino_pengajuan").val($("#i_no_ppj").val());
		$("#i_end").val($("#i_target_ppj").val());
		$("#i_ipelaksana").val($("#i_pelaksana").val());
		$("#i_dept").val();
		$("#i_target_ppj").val();
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
				$("#detailgrid").show("fast");
			} else {
				$("#isdetail").attr('checked',false);
				$("#detailgrid").hide("fast");
			}
			reloadGridActivity();
		} 
	}
	
	function init_forminput(){
		$("#i_ino_pengajuan").val(""); $("#i_ipelaksana").val(""); $("#i_afd").val("");
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
		
		/* function reloadGridDetail(){
		 	jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_no_ppj").val())}).trigger("reloadGrid"); 
		} */
		
		function reloadGridActivity(){
			 jQuery("#list_pengajuan_detail").setGridParam({url:url+'pms_c_pengajuan/read_detail_ppj/'+$.trim($("#i_pjsementara").val())}).trigger("reloadGrid"); 
		}
		
function selesai(){
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
					jQuery("#list_pengajuan").setGridParam({url:url+'pms_c_pengajuan/read_ppj/'+$.trim($("#i_no_ppj").val())}).trigger("reloadGrid");
				};  
		});
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
        jGrid_sblok.navButtonAdd('#pager_sblok',{
            caption:"Tambah Data", buttonicon:"ui-icon-add", 
            onClickButton: function(){ 
				var inisial = "tambah";
				submit_detailppj(inisial); 
			}, position:"left"
          });               
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
</script>
<table width="95%">
<tr>
  <td><table width="95%" >
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
            <table id="list_pengajuan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0">
            </table>
           <div id="pager_pengajuan" class="scroll"></div>

        </div>
        <!-- ###################### end konten ###################### --></td>
    </tr>
    <tr>
      <td colspan="3" style="padding-right:10px; padding-top:10px;" align="right"><input type="button"  id="ptambah" value="Tambah" onclick="submit_headerppj()" />
        <input type="button"  id="pcomplete" value="Selesai" onclick="selesai()" />
        <input type="button"  id="pbatal" value="Batal" onclick="cancel_headerppj()" /></td>
    </tr>
  </table></td>
</tr>
</table>
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
<div id="form_input">
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
    <td><select tabindex="5" name='i_typepj' class='select' id="i_typepj" style="width:90px;">
    	<option value=""> -- pilih -- </option>
      </select></td>
  </tr>
  <tr>
    <td>Subtype Project</td> 
    <td>:</td>
    <td><select tabindex="6" name='i_subtypepj' class='select' id="i_subtypepj" style="width:240px;">
    	<option value=""> -- pilih -- </option>
        </select></td>
  </tr>  
  <tr id="if2">
  		<td>Sub Tipe Infrastruktur</td>
        <td>:</td> 
        <td><select tabindex="8" name='i_if_type' class='select' id="i_if_type" style="width:250px;">
			</select></td>
  </tr>
  <tr>
    <td>Lokasi</td>
    <td>:</td> 
    <td><input tabindex="9" type="text" style="width:180px;" id="i_lokasi" name="i_lokasi" class="input"/>
    	<div id="search" style=" position:fixed; margin-left:200px; margin-top:-16px;">
        	<img id="loadbutton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="searchblok()" />
        </div></td>
  </tr>
  <tr>
    <td>Aktivitas Utama</td>
    <td>:</td> 
    <td><select tabindex="10" name='i_aktivitas' class='select' id="i_aktivitas" style="width:150px;">
		</select></td>
  </tr>
  <tr>
    <td>Sub Aktivitas</td>
    <td>:</td>  
    <td><select tabindex="11" name='i_subaktivitas' class='select' id="i_subaktivitas" style="width:250px;">
		</select></td>
  </tr>
   <tr>
    <td>Deskripsi</td>
    <td>:</td>
    <td><input tabindex="12" type="text" style="width:250px;" id="i_deskripsi" class="input"/></td>
  </tr>
  <tr> 
    <td>Tgl Mulai</td>
    <td>:</td>
    <td><input tabindex="13" type="text" style="width:100px;" id="i_start" class="input"/></td>
  </tr>
  <tr>
    <td>Tgl Penyelesaian</td>
    <td>:</td>
    <td><input tabindex="14" type="text" style="width:100px;" id="i_end" class="input"/></td>
  </tr>
  <tr>
    <td>Qty</td>
    <td>:</td>
    <td><input tabindex="15" type="text" style="width:100px;" id="i_qty" class="input"/></td>
  </tr>
  <tr>
    <td>Satuan</td>
    <td>:</td>
    <td><? if(isset($satuan)){ echo $satuan; } ?></td>
  </tr>
  <tr>
    <td>Rupiah Per Satuan</td>
    <td>:</td>
    <td><input tabindex="17" type="text" style="width:120px;" id="i_rpsat" class="input"/></td>
  </tr>
  <tr>
    <td>Rupiah Total</td>
    <td>:</td>
    <td><input tabindex="18" type="text" style="width:120px;" id="i_rptotal" class="input"/></td>
  </tr>
  <tr>
    <td>Catatan</td>
    <td>:</td>
    <td><input tabindex="19" type="text" style="width:120px;" id="i_catatan" class="input"/></td>
  </tr>
  <tr>
    <td>Detail Aktivitas</td>
    <td>:</td>
    <td><input tabindex="20" type="checkbox" value="1" id="isdetail" class="input"/></td>
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