<? 
    $template_path = base_url().$this->config->item('template_path');  
	$session = $this->session->userdata('LOGINID');
?>   
<script type="text/javascript">
var url = "<?= base_url().'index.php/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/base/images';

$(function() { $("#trans_date").datepicker({dateFormat:"yy-mm-dd"}); });
jQuery(document).ready(function(){
	$("#form_input").dialog({
		bgiframe: true, autoOpen: false, resizable: true, draggable: true,
		closeOnEscape:false, height: 330, title: "Input Data Pokok Mati", width: 480, modal: true,buttons: { 
			'Tutup	': function(){
				$("#form_input").dialog("close");
				initForm();
			}, 'Batal': function(){
				submit_addTrans("hapus");
			}, 'Simpan': function(){
				submit_addTrans("simpan");
			}, 'Ubah': function(){
				submit_addTrans("ubah");
			}
		}
	}); 
			
	$("#bulan").change(function() {
		var periode = $("#tahun").val() + $("#bulan").val();
		jQuery("#list_PokokMati").setGridParam({url:url+'p_block_transaction/search_pokok_mati/'+periode+'/'}).trigger("reloadGrid");
	});
	
	$("#tahun").change(function() {
		var periode = $("#tahun").val() + $("#bulan").val();
		jQuery("#list_PokokMati").setGridParam({url:url+'p_block_transaction/search_pokok_mati/'+periode+'/'}).trigger("reloadGrid");
	});

	/* Grid Pokok Mati*/
	var jGrid_PokokMati = null;
	var colNamesT_PokokMati = new Array();
	var colModelT_PokokMati = new Array();
						
	colNamesT_PokokMati.push('TRANS_ID');
	colModelT_PokokMati.push({name:'TRANS_ID',index:'TRANS_ID', hidden:true, width: 80, align:'center'});
	
	colNamesT_PokokMati.push('Tanggal');
	colModelT_PokokMati.push({name:'TRANS_DATE',index:'TRANS_DATE', editable: false, hidden:false, 
							 width: 80, align:'left'});
	
	colNamesT_PokokMati.push('Afd');
	colModelT_PokokMati.push({name:'AFD',index:'AFD', editable: false, hidden:false, width: 50, align:'left'});
		
	colNamesT_PokokMati.push('Blok');
	colModelT_PokokMati.push({name:'BLOCK',index:'BLOCK', hidden:false, width: 80, align:'left'});
	
	colNamesT_PokokMati.push('Blok Tanam');
	colModelT_PokokMati.push({name:'BLOCK_TANAM',index:'BLOCK_TANAM', hidden:false, width: 100, align:'center'});
	
	colNamesT_PokokMati.push('Qty Pokok Mati');
	colModelT_PokokMati.push({name:'QTY',index:'QTY', editable: false, hidden:false, width: 100, align:'right'});
		
	colNamesT_PokokMati.push('No Dokumen');
	colModelT_PokokMati.push({name:'DOCUMENT_NUMBER',index:'DOCUMENT_NUMBER', hidden:false, width: 150, align:'center'});
	
	colNamesT_PokokMati.push('Catatan');
	colModelT_PokokMati.push({name:'NOTE',index:'NOTE', editable: false, hidden:false, width: 150, align:'left'});
		
	colNamesT_PokokMati.push('Perusahaan');
	colModelT_PokokMati.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, width: 140, align:'center'});
	
	colNamesT_PokokMati.push('Action');
	colModelT_PokokMati.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'});
	
	var lastsel; var jdesc1;
	var lRow; var lCol; var i = 0;
	var bln = $("#bulan").val();
	var thn = $("#tahun").val();
	
	var loadView_PokokMati = function(){
	jGrid_PokokMati = jQuery("#list_PokokMati").jqGrid({
		 url:url+'p_block_transaction/search_pokok_mati/'+thn+bln,
		 mtype : "POST", datatype: "json",
		 colNames: colNamesT_PokokMati , colModel: colModelT_PokokMati ,
		 rownumbers:true, viewrecords: true, multiselect: false, 
		 rowNum:20, height: 350, rowList:[10,20,30], imgpath: gridimgpath, rownumbers:true,
		 pager: jQuery('#pager_PokokMati'), sortname: colModelT_PokokMati[2].name, viewrecords: true,
		 caption:"Transaksi Pokok Mati",
		 onSelectRow: function(){
		 }, loadComplete: function(){ 
				var ids = jQuery("#list_PokokMati").getDataIDs(); 
				for(var i=0;i<ids.length;i++) { 
					var cl = ids[i];
					ce = "<a href='#' onclick=\"view('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
					jQuery("#list_PokokMati").setRowData(ids[i],{act:ce}) 
				}
		 }, imgpath: gridimgpath, pager: jQuery('#pager_PokokMati'), sortname: colModelT_PokokMati[1].name
	  });
	  jGrid_PokokMati.navGrid('#pager_User',{edit:false,add:false,del:false, search: true, refresh: true});
	  jGrid_PokokMati.navButtonAdd('#pager_PokokMati',{
		caption:"Tambah data",  buttonicon:"ui-icon-add", 
		onClickButton: function(){ 
			$("#form_input").dialog("open");
			$(":button:contains('Ubah')").hide();
			$(":button:contains('Batal')").hide();
		}, position:"left",
	  });
	}
	jQuery("#list_PokokMati").ready(loadView_PokokMati);
	
	/* CRUD */
	function submit_addTrans(method){
		var postdata={};
		postdata['TRANS_ID'] = $("#trans_id").val() ;
		postdata['TRANS_DATE'] = $("#trans_date").val() ;
		postdata['AFD'] = $("#i_afd").val() ; 
		postdata['BLOCK'] = $("#i_block").val() ;
		postdata['BLOCK_TANAM'] = $("#i_blocktanam").val() ; 
		postdata['QTY'] = $("#i_qty").val() ;
		postdata['DOCUMENT_NUMBER'] = $("#i_refdoc").val() ; 
		postdata['NOTE'] = $("#i_note").val() ;
			 
		var urls = ""
		if(method == "simpan"){
			urls = "p_block_transaction/insertData/";
		} else if(method == "ubah"){
			urls = "p_block_transaction/updateData/"+$("#trans_id").val();
		} else if(method == "hapus"){
			urls = "p_block_transaction/deleteData/"+$("#trans_id").val();
		}
			
		$.post( url+urls, postdata, function(status) {
			var status = new String(status);
			if(status.replace(/\s/g,"") != "") { 
					alert(status); 
			} else { 
					jQuery("#list_Menu").setGridParam({url:url+'syst_c_menu/search_menu/'}).trigger("reloadGrid");
					if(method == "simpan"){
						alert('data berhasil tersimpan.');
					} else if(method == "ubah"){
						alert('data berhasil terupdate.');
					} else if(method == "hapus"){
						alert('data berhasil dibatalkan.');
					}
					gridReload();
					initForm();        
					$("#form_input").dialog("close");
			};  
		});
	}
});

function chainBlok(){
		var type = $("#i_afd").val();
		if (type != 0){ 
			$("#i_block").empty(); 
			$("#i_blocktanam").empty(); 
			var cType = $("#i_block").val();
			if (cType==null){ cType="-"; }
				$.post(url+'p_block_transaction/get_block/'+type+'/', $("#i_block").val(), 
				function(datapost){ 
					for (var i=0; i<datapost.length; i++){
					   $("#i_block").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
				},"json")
		} else {
			$("#i_block").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
		} 
		chainBlokTanam();
	}

	function chainBlokTanam(){
		var afd = $("#i_afd").val();
		var block = $("#i_block").val();
		if (block != 0 && afd != 0){ 
			$("#i_blocktanam").empty(); 
			var cType = $("#i_blocktanam").val();
			if (cType==null){ cType="-"; }
				$.post(url+'p_block_transaction/get_block_tanam/'+afd+'/'+block+'/', $("#i_blocktanam").val(), 
				function(datapost){ 
					for (var i=0; i<datapost.length; i++){
					   $("#i_blocktanam").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), 
																						document.all ? i : null);
					}
				},"json")
		} else {
			$("#i_blocktanam").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
		} 
	}
	
	
function view(ids){
		var ids = jQuery("#list_PokokMati").getGridParam('selrow'); 
		var data = $("#list_PokokMati").getRowData(ids) ; 
		if (ids!=null ){
			initForm();
			$("#form_input").dialog("open");
			$(":button:contains('Ubah')").show();
			$(":button:contains('Batal')").show();
			$(":button:contains('Simpan')").hide();
			$("#trans_id").val(data.TRANS_ID);
			$("#trans_date").val(data.TRANS_DATE);
			$("#i_afd").val(data.AFD);
			$("#i_block").val(data.BLOCK);
			$.post(url+'p_block_transaction/get_block/'+data.AFD+'/', $("#i_block").val(), 
				function(datapost){ 
					for (var i=0; i<datapost.length; i++){
					   $("#i_block").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
				},"json");
			$("#i_blocktanam").val(data.BLOCK_TANAM);
			$.post(url+'p_block_transaction/get_block_tanam/'+data.AFD+'/'+data.BLOCK+'/', 
				$("#i_blocktanam").val(), function(datapost){ 
					for (var i=0; i<datapost.length; i++){
					   $("#i_blocktanam").get(0).add(new Option(datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
				},"json");
			$("#i_qty").val(data.QTY);
			$("#i_refdoc").val(data.DOCUMENT_NUMBER);
			$("#i_note").val(data.NOTE);
		} else {
			alert("harap pilih data untuk di edit");
		}                
	}
	
	function initForm(){
		 $("#trans_date").val("") ; $("#i_afd").val("") ; $("#i_block").val(""); 
		 $("#i_blocktanam").val("") ; $("#i_qty").val("") ;  $("#i_refdoc").val("") ; 
		 $("#i_note").val("") ;  $("#trans_id").val("") ;
	}
	
	function gridReload(){
		var periode = $("#tahun").val() + $("#bulan").val();
		jQuery("#list_PokokMati").setGridParam({url:url+'p_block_transaction/search_pokok_mati/'+periode+'/'}).trigger("reloadGrid");
	}

</script>
<br/><br/>
<table class="teks_">
<tr><td>Periode</td><td>:</td><td><? echo $periode; ?></td></tr>
</table>
<div id="user_grid">
     <table id="list_PokokMati" class="scroll" cellpadding="0" cellspacing="0"></table>
     <div id="pager_PokokMati" class="scroll" style="text-align:center;"></div>
</div>

<div id="form_input">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="trans_date" disa /></td></tr>
<tr><td>Afdeling</td><td>:</td><td><?php if(isset($afd)){ echo $afd; }?></td></tr>
<tr><td>Blok</td><td>:</td><td><select tabindex="3" name='i_block' class='select' onchange="chainBlokTanam()" id="i_block" style="width:80px;"></select></td></tr>
<tr><td>Blok Tanam</td><td>:</td><td><select tabindex="4" name='i_blocktanam' class='select' id="i_blocktanam" style="width:280px;"></select></td></tr>
<tr><td>Jumlah Pokok Mati</td><td>:</td><td><input style="text-transform: uppercase; width:150px ;" class="positive" type="text" size=15 id="i_qty" /></td></tr>
<tr><td>No. Dokumen / BA</td><td>:</td><td><input class="input" type="text" size=15 id="i_refdoc" /></td></tr>
<tr><td>Catatan</td><td>:</td><td>
<textarea class="input" style="height:60px; width:240px;" id="i_note" ></textarea>
<input type="hidden" id="trans_id" />
</table>
</div>