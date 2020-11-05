<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
	/* function semua */
	var gridimgpath = '<?= $template_path ?>themes/basic/images'; 
	var url = "<?= base_url().'index.php/' ?>"; 
	var ntimeoutHnd; 
	var nflAuto = false; 
	var ttimeoutHnd; 
	var tflAuto = false; 
		
	$(function() {  $( "#tabs" ).tabs();  });
	jQuery(document).ready(function(){
		$(".positive").format({precision: 2,allow_negative:false,autofix:true});
		/* dialog progress */	
		$("#progressbar").dialog({
					bgiframe: true, autoOpen: false,
					resizable: true, draggable: true,
					closeOnEscape:false, height: 160,
					width: 220, modal: true
		}); 
		/* end dialog */
		
		/* dialog tunjangan potongan */
		$("#edit_tunpot").dialog({
			dialogClass : 'dialog1', bgiframe: true, autoOpen: false,
			height: 320, width: 400, modal: true, title: "Form Tunjangan & Potongan",
			resizable: false, moveable: true,
			buttons: {
				Tutup: function()  {	init_tunpot();  },
				Simpan: function() {	submit_tunpot();	}     
			} 
		});
		/* end dialog tunjangan potongan */
		
		/* lookup NIK Tunjangan Potongan */
		$(function () {
                $("#i_nik_tunpot")
                  .autocomplete( 
                    url+"m_employee_tp/lookup_employee/", {
                      dataType: 'ajax', width:350, multiple: false, limit:20,
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
                  ).result(function(e, item) {  $("#i_nama_tunpot").val(item.res_name);  });
        });
		/* end lookup NIK Tunjangan Potongan */
		
		$("#bulan").change(function() {
			var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
			if (periode == ""){
				periode = "-";
			}
		  	tgridReload();
			ngridReload();
			jQuery("#list_tunpot").setGridParam({url:url+'m_employee_tp/read_grid_tunpot/'+periode}).trigger("reloadGrid");
			jQuery("#list_kontanan").setGridParam({url:url+"m_employee_tp/read_grid_kontanan/"+periode}).trigger("reloadGrid"); 
			jQuery("#list_lembur").setGridParam({url:url+"m_employee_tp/read_grid_kontanan/"+periode}).trigger("reloadGrid"); 
			jQuery("#list_bpjs").setGridParam({url:url+"m_employee_tp/read_grid_bpjs/-/"+periode}).trigger("reloadGrid"); 
		});
		
		$("#tahun").change(function() {
			var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
			if (periode == ""){
				periode = "-";
			}
		  	tgridReload();
			ngridReload();
			jQuery("#list_tunpot").setGridParam({url:url+'m_employee_tp/read_grid_tunpot/'+periode}).trigger("reloadGrid");
			jQuery("#list_kontanan").setGridParam({url:url+"m_employee_tp/read_grid_kontanan/"+periode}).trigger("reloadGrid");
			jQuery("#list_lembur").setGridParam({url:url+"m_employee_tp/read_grid_kontanan/"+periode}).trigger("reloadGrid"); 
			jQuery("#list_bpjs").setGridParam({url:url+"m_employee_tp/read_grid_bpjs/-/"+periode}).trigger("reloadGrid");
		});
		/* dialog natura */
		$("#edit_natura").dialog({
			dialogClass : 'dialog1', bgiframe: true, autoOpen: false,
			height: 200, width: 300, modal: true, title: "Data Natura Karyawan",
			resizable: false, moveable: true,
			buttons: {
				Tutup: function()  {	init_natura();  },
				Simpan: function() {	submit_natura();	}     
			} 
		});
		/* end dialog natura */
		
		/* lookup NIK Natura */
		$(function () {
                $("#i_nik_natura")
                  .autocomplete( 
                    url+"m_natura/lookup_employee/", {
                      dataType: 'ajax', width:350, multiple: false, limit:20,
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
                  ).result(function(e, item) {  $("#i_nama_natura").val(item.res_name);  });
          });
		/* end lookup NIK Natura */
	});
	
	/* close progress bar */
	function closewin(){
			$("#progressbar").dialog('close');
	}
	/* end close progress bar */
	/*search*/
	function doSearchTp(ev){ 
		if(ttimeoutHnd) 
		clearTimeout(ttimeoutHnd) 
		ttimeoutHnd = setTimeout(tgridReload,500) 
	} 
	
	function tgridReload(){ 
		var nik = jQuery("#tsearch_nik").val();
		var nama = jQuery("#tsearch_nama").val();  
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
		if (nik == ""){  nik = "-"; }
		if (nama == ""){  nama = "-"; } 
		jQuery("#list_tunpot").setGridParam({url:url+"m_employee_tp/read_grid_tunpot/"+periode+"/"+nik+"/"+nama}).trigger("reloadGrid");        
	}
	
	/*search*/
	function doSearchNat(ev){ 
		// var elem = ev.target||ev.srcElement; 
		if(ntimeoutHnd) 
		clearTimeout(ntimeoutHnd) 
		ntimeoutHnd = setTimeout(ngridReload,500) 
	} 
	
	function ngridReload(){ 
		var ds = jQuery("#nsearch").val();
		//var nama = jQuery("#nsearch_nama").val(); 
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        } 
		if (ds == ""){  ds = "-"; }
		
		jQuery("#list_natura").setGridParam({url:url+"m_natura/load_data_natura/"+ds+"/"+periode}).trigger("reloadGrid");        
	} 
	
	function reloadGrid(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
		var nik = jQuery("#nsearch").val();
		//var nama = jQuery("#nsearch_nama").val();  
		if (nik == ""){  nik = "-"; }
		
		
	}
	/* end semua */
	
	/* untuk lembur */
	function glembursingle(ids){
		var confirmsg = confirm("Generate data? ");
        if(confirmsg){
                var postdata = {}; 
                var ids = jQuery("#list_lembur").getGridParam('selrow'); 
                var data = $("#list_lembur").getRowData(ids) ; 
                postdata['LID'] = data.ID; 
                postdata['LPERIODE'] = $("#tahun").val() + $("#bulan").val();
                postdata['LTGL'] = data.LHM_DATE; 
                postdata['LNIK'] = data.EMPLOYEE_CODE; 
				postdata['LGANGCODE'] = data.GANG_CODE; 
				postdata['LABSEN'] = data.TYPE_ABSENSI; 
				postdata['LLOCATION'] = data.LOCATION_CODE; 
				postdata['LACTIVITY'] = data.ACTIVITY_CODE; 
                postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
                
                $.post( url+'m_employee_tp/glembursingle/', postdata,function(status) { 
                   var status = new String(status);
                });     
         } 
	}
	
	function lemburtoxls(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		urls = url + 'm_employee_tp/lembur_xls/'+periode,
		$.download(urls,''); 
	}
	
	var jGrid_lembur = null;
	var colNamesT_lembur = new Array();
	var colModelT_lembur = new Array();
	colNamesT_lembur.push('ID');
	colModelT_lembur.push({name:'ID',index:'ID', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_lembur.push('NIK');
	colModelT_lembur.push({name:'EMPLOYEE_CODE',index:'EMPLOYEE_CODE', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_lembur.push('Nama');
	colModelT_lembur.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 170, align:'left'});
	colNamesT_lembur.push('Kemandoran');
	colModelT_lembur.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 85, align:'center'});
	colNamesT_lembur.push('Tgl Transaksi');
	colModelT_lembur.push({name:'LHM_DATE',index:'LHM_DATE', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_lembur.push('Absen');
	colModelT_lembur.push({name:'TYPE_ABSENSI',index:'TYPE_ABSENSI', editable: false, hidden:false, width: 50, align:'center'});
	colNamesT_lembur.push('Tipe');
	colModelT_lembur.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false, hidden:false, width: 50, align:'center'});
	colNamesT_lembur.push('Lokasi');
	colModelT_lembur.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, hidden:false, width: 100, align:'center'});
	colNamesT_lembur.push('Kode Aktivitas');
	colModelT_lembur.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, hidden:false, width: 100, align:'center'});
	colNamesT_lembur.push('Jam Lembur');
	colModelT_lembur.push({name:'LEMBUR_JAM',index:'LEMBUR_JAM', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_lembur.push('Rp. Lembur');
	colModelT_lembur.push({name:'LEMBUR_RUPIAH',index:'LEMBUR_RUPIAH', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 90, align:'center'});
	colNamesT_lembur.push('COMPANY_CODE');
	colModelT_lembur.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_lembur.push('Action');
	colModelT_lembur.push({name:'act',index:'act', editable: false, hidden:false, width: 60, align:'center'}); 
		 
	var loadView_lembur = function()
    {
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
        
            jGrid_lembur = jQuery("#list_lembur").jqGrid(
            {
                url:url+'m_employee_tp/read_grid_lembur/'+periode,
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_lembur ,
                colModel: colModelT_lembur ,
                rownumbers:true,
                viewrecords: true, 
                multiselect: false, 
                caption: "Data Lembur Karyawan <?php echo $company_dest;?>", 
                rowNum:20,
                rowList:[10,20,30], 
                multiple:true,
                height: 300,
                //cellEdit: false,
                loadComplete: function(){ 
                  var ids = jQuery("#list_lembur").getDataIDs(); 
                  for(var i=0;i<ids.length;i++){ 
                      var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>Generate</a>";
                      be = "<input style='height:22px;width:60px; font-size:9px;' class='button' type='button' value='Generate' onclick=\"glembursingle('"+cl+"')\" />"; 
				 	  jQuery("#list_lembur").setRowData(ids[i],{act:be}) 
                     }
                  },
                imgpath: gridimgpath,
				editurl: url+'m_employee_tp/read_grid_lembur/',
                pager: jQuery('#pager_lembur'),
                sortname: colNamesT_lembur[0]
            });
            jGrid_lembur.navGrid('#pager_lembur',{edit:false,add:false,del:false, search: false, refresh: true});
			jGrid_lembur.navButtonAdd('#pager_lembur',{
               caption:"Excell", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){  lemburtoxls();  }, position:"left"
            });
        }
	jQuery("#list_lembur").ready(loadView_lembur);
	/* end lembur */
	
	/* kontanan */
	
	function kontanantoxls(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		urls = url + 'm_employee_tp/kontanan_xls/'+periode,
		$.download(urls,''); 
	}
	
	var jGrid_kontanan = null;
	var colNamesT_kontanan = new Array();
	var colModelT_kontanan = new Array();
	colNamesT_kontanan.push('ID');
	colModelT_kontanan.push({name:'ID',index:'ID', editable: false, hidden:true, width: 80, align:'center'});
	colNamesT_kontanan.push('NIK');
	colModelT_kontanan.push({name:'EMPLOYEE_CODE',index:'EMPLOYEE_CODE', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_kontanan.push('Nama');
	colModelT_kontanan.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 170, align:'left'});
	colNamesT_kontanan.push('Kemandoran');
	colModelT_kontanan.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 85, align:'center'});
	colNamesT_kontanan.push('Tgl Transaksi');
	colModelT_kontanan.push({name:'LHM_DATE',index:'LHM_DATE', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_kontanan.push('Absen');
	colModelT_kontanan.push({name:'TYPE_ABSENSI',index:'TYPE_ABSENSI', editable: false, hidden:false, width: 50, align:'center'});
	colNamesT_kontanan.push('Tipe');
	colModelT_kontanan.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false, hidden:false, width: 50, align:'center'});
	colNamesT_kontanan.push('Lokasi');
	colModelT_kontanan.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, hidden:false, width: 100, align:'center'});
	colNamesT_kontanan.push('Kode Aktivitas');
	colModelT_kontanan.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, hidden:false, width: 100, align:'center'});
	colNamesT_kontanan.push('Kontanan');
	colModelT_kontanan.push({name:'KONTANAN',index:'KONTANAN', editable: false, edittype:'checkbox', editoptions: { value:"1:0"},
  				formatter: "checkbox", formatoptions: {disabled : true}, width: 70, align:'center'});
	colNamesT_kontanan.push('Rp. Kontanan');
	colModelT_kontanan.push({name:'POTONGAN_KONTANAN',index:'POTONGAN_KONTANAN', editable: false, editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, 
				hidden:false, width: 100, align:'center'});
	colNamesT_kontanan.push('COMPANY_CODE');
	colModelT_kontanan.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_kontanan.push('');
	colModelT_kontanan.push({name:'act',index:'act', editable: false, hidden:true, width: 60, align:'center'}); 
		 
	var loadView_kontanan = function()
    {
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
            jGrid_kontanan = jQuery("#list_kontanan").jqGrid(
            {
                url:url+'m_employee_tp/read_grid_kontanan/'+periode,
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_kontanan ,
                colModel: colModelT_kontanan ,
                rownumbers:true,
                viewrecords: true, 
                multiselect: false, 
                caption: "Data Kontanan Karyawan <?php echo $company_dest;?>", 
                rowNum:20,
                rowList:[10,20,30], 
                multiple:true,
                height: 300,
                cellEdit: false,
                imgpath: gridimgpath,
                pager: jQuery('#pager_kontanan'),
                sortname: colNamesT_kontanan[0]
            });
            jGrid_kontanan.navGrid('#pager_kontanan',{edit:false,add:false,del:false, search: false, refresh: true});
			jGrid_kontanan.navButtonAdd('#pager_kontanan',{
               caption:"Excell", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){  kontanantoxls(); }, position:"left"
            });
        }
	jQuery("#list_kontanan").ready(loadView_kontanan);
	/* end kontanan */
	
	/* tunjangan potongan */
	
	function gpph(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		var postdata = {}; 
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu..... Proses Generate Natura sedang diproses...";
		$("#progressbar").dialog('open');
		$.post( url+'m_employee_tp/execute/'+periode, postdata,function(status) 		  { 
            var status = new String(status);
               if(status.replace(/\s/g,"") != "") { 
                  $("#load").hide();
				  document.getElementById('msg').innerHTML= status;
				  $("#cok").attr("disabled", false);
               } else { 
				  $("#load").hide();
				  $("#cok").attr("disabled", false);
				  document.getElementById('msg').innerHTML= 'data tersimpan';
               };  
         }); 
	}
	
	function add_tunpot()
	{ 
		init_tunpot();
		$("#form_mode_tunpot").val("POST");
		$("#edit_tunpot").dialog('open');
	}
	
	function edit_tunpot()
	{
		var ids = jQuery("#list_tunpot").getGridParam('selrow'); 
		var data = $("#list_tunpot").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined)
		{
			alert("harap pilih data untuk di edit...");
		}else{
			init_tunpot();
			$("#form_mode_tunpot").val("GET");
			$("#edit_tunpot").dialog('open');
			$("#i_nik_tunpot").attr('disabled','disabled');
			$("#i_nama_tunpot").attr('disabled','disabled'); 
			$("#i_nik_tunpot").val(data.NIK);  
			$("#i_nama_tunpot").val(data.NAMA); 
			$("#i_tunjab").val(data.TUNJANGAN_JABATAN);
			$("#i_tuncuti").val(data.TUNJANGAN_CUTI);
			$("#i_komcuti").val(data.KOMPENSASI_CUTI);			
			$("#i_potlain").val(data.POTONGAN_LAIN);
			$("#i_subkend").val(data.SUBSIDI_KENDARAAN);
			$("#i_tuntrans").val(data.TUNJ_TRANSPORT);
			$("#i_rapel").val(data.RAPEL);
			$("#i_thr").val(data.THR);
			$("#i_pph").val(data.PPH_21);
			$("#i_ket").val(data.KETERANGAN);	
		} 
	}
	
	function submit_tunpot()
	{
		var postdata = {};
		var mode= $("#form_mode_tunpot").val();
		postdata['NIK'] =  $("#i_nik_tunpot").val();
		postdata['NAMA'] =  $("#i_nama_tunpot").val(); 
		postdata['TUNJAB'] = $("#i_tunjab").val();
		postdata['TUNJANGAN_CUTI'] = $("#i_tuncuti").val();
		postdata['KOMPENSASI_CUTI'] = $("#i_komcuti").val();
		postdata['POTLAIN'] = $("#i_potlain").val();
		postdata['SUBKEND'] = $("#i_subkend").val();
		postdata['TUNTRANS'] = $("#i_tuntrans").val();
		postdata['RAPEL'] = $("#i_rapel").val();
		postdata['THR'] = $("#i_thr").val();
		postdata['PPH'] = $("#i_pph").val();
		postdata['KETERANGAN'] = $("#i_ket").val();		
		
		if (mode == "GET")
			{
				var ids = jQuery("#list_tunpot").getGridParam('selrow'); 
				var data = $("#list_tunpot").getRowData(ids) ;
				var periode = postdata['PERIODE'] = data.PERIODE;
	
				$.post( url+'m_employee_tp/update_tunpot/'+ periode, postdata,function(status)
				{ 
					var status = new String(status);
					
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							tunpotReload(periode);
							alert('data berhasil terupdate.') 
							$("#edit_tunpot").dialog('close');      
						 }else{
							//tunpotReload(); 
							alert(status);    
						 } 
					} else { 
						tunpotReload(periode); 
						alert('data berhasil terupdate.')
						$("#edit_tunpot").dialog('close');        
				   };
				  });
			} 
			else if (mode == "POST") 
			{
				var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
				$.post(  url+'m_employee_tp/create_tunpot/'+periode, postdata,function(status) 
				{ 
					var status = new String(status);
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							tunpotReload(periode);
							alert('data berhasil tersimpan.') 
							$("#edit_tunpot").dialog('close');      
						 } else {
							//naturaReload();
							alert(status); 
						 } 
					} else { 
						tunpotReload(periode); 
						alert('data berhasil tersimpan.')
						$("#edit_tunpot").dialog('close');        
				   };  
				 } );     
	
			}       
	}
	
	function init_tunpot()
	{
		$("#i_nik_tunpot,#i_nama_tunpot").removeAttr('disabled');
		$("#i_nik_tunpot,#i_nama_tunpot").val('');
		$("#i_tunjab").val('')
		$("#i_potlain").val('')
		$("#i_subkend").val('')
		$("#i_tuntrans").val('')
		$("#i_rapel").val('')
		$("#i_thr").val('')
		$("#i_pph").val('')
		$("#i_ket").val('')
		$("#form_mode_tunpot").val('');
		$("#i_tuncuti").val('');
		$("#i_komcuti").val('');
		$("#edit_tunpot").dialog('close');    
	}
	
	function tunpottoxls(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		urls = url + 'm_employee_tp/tunpot_xls/'+periode,
		$.download(urls,''); 
	}
	
	function tunpotReload(periode){ 
		jQuery("#list_tunpot").setGridParam({url:url+'m_employee_tp/read_grid_tunpot/'+periode}).trigger("reloadGrid");        
	}
	
	var jGrid_tunpot = null;
	var colNamesT_tunpot = new Array();
	var colModelT_tunpot = new Array();
	colNamesT_tunpot.push('NIK');
	colModelT_tunpot.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 80, align:'center'});
	colNamesT_tunpot.push('Nama');
	colModelT_tunpot.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 170, align:'left'});
	colNamesT_tunpot.push('Periode');
	colModelT_tunpot.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:false, width: 85, align:'center'});
	colNamesT_tunpot.push('Tunjangan Jabatan');
	colModelT_tunpot.push({name:'TUNJANGAN_JABATAN',index:'TUNJANGAN_JABATAN', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:true, width: 110, align:'center'});
	colNamesT_tunpot.push('Tunjangan Cuti');
	colModelT_tunpot.push({name:'TUNJANGAN_CUTI',index:'TUNJANGAN_CUTI', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:false, width: 110, align:'center'});
	colNamesT_tunpot.push('Kompensasi Cuti');
	colModelT_tunpot.push({name:'KOMPENSASI_CUTI',index:'KOMPENSASI_CUTI', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:false, width: 110, align:'center'});
	colNamesT_tunpot.push('Potongan Lain');
	colModelT_tunpot.push({name:'POTONGAN_LAIN',index:'POTONGAN_LAIN', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:false, width: 90, align:'center'});
	colNamesT_tunpot.push('Subsidi Kendaraan');
	colModelT_tunpot.push({name:'SUBSIDI_KENDARAAN',index:'SUBSIDI_KENDARAAN', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:false, width: 110, align:'center'});
	colNamesT_tunpot.push('Tunj. Transport');
	colModelT_tunpot.push({name:'TUNJ_TRANSPORT',index:'TUNJ_TRANSPORT', editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_tunpot.push('Rapel');
	colModelT_tunpot.push({name:'RAPEL',index:'RAPEL', editable: false, editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				hidden:false, width: 60, align:'center'});
	colNamesT_tunpot.push('THR');
	colModelT_tunpot.push({name:'THR',index:'THR', editable: false, editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				width: 60, align:'center'});
	colNamesT_tunpot.push('PPh 21');
	colModelT_tunpot.push({name:'PPH_21',index:'PPH_21', editable: false, editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, 
				hidden:false, width: 80, align:'center'});
	colNamesT_tunpot.push('Keterangan');
	colModelT_tunpot.push({name:'KETERANGAN',index:'KETERANGAN', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_tunpot.push('COMPANY_CODE');
	colModelT_tunpot.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_tunpot.push('');
	colModelT_tunpot.push({name:'act',index:'act', editable: false, hidden:true, width: 60, align:'center'}); 
		 
	var loadView_tunpot = function()
    {
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
        
		jGrid_tunpot = jQuery("#list_tunpot").jqGrid({
			url:url+'m_employee_tp/read_grid_tunpot/'+periode,
            mtype : "POST", datatype: "json",
            colNames: colNamesT_tunpot , colModel: colModelT_tunpot ,
            rownumbers:true, viewrecords: true, multiselect: false, 
            caption: "Data Tunjangan Potongan Karyawan <?php echo $company_dest;?>", 
            rowNum:20, rowList:[10,20,30], multiple:true,
            height: 250, cellEdit: false, imgpath: gridimgpath,
            pager: jQuery('#pager_tunpot'), sortname: colNamesT_tunpot[0]
         });
         jGrid_tunpot.navGrid('#pager_tunpot',{edit:false,add:false,del:false, search: false, refresh: true});
		 jGrid_tunpot.navButtonAdd('#pager_tunpot',{
            caption:"Excell", buttonicon:"ui-icon-add", 
            onClickButton: function(){ tunpottoxls(); }, position:"left"
          });
		  jGrid_tunpot.navButtonAdd('#pager_tunpot',{
             caption:"Tambah", buttonicon:'ui-icon-newwin',
             onClickButton: function(){  add_tunpot(); }, position:"left"
          });
		  jGrid_tunpot.navButtonAdd('#pager_tunpot',{
            caption:"Ubah", buttonicon:'ui-icon-newwin',
            onClickButton: function(){ edit_tunpot(); }, position:"left"
          });
		  jGrid_tunpot.navButtonAdd('#pager_tunpot',{
            caption:"Import Data", buttonicon:'ui-icon-newwin',
            onClickButton: function(){ 
               		// edit_tunpot();
            }, position:"left"
         });
     }
	jQuery("#list_tunpot").ready(loadView_tunpot);
	
	/* end tunjangan potongan */
	
	/* natura */
		
	function add_natura()
	{ 
		init_natura();
		$("#form_mode_natura").val("POST");
		$("#edit_natura").dialog('open');
	}
	
	function edit_natura()
	{
		var ids = jQuery("#list_natura").getGridParam('selrow'); 
		var data = $("#list_natura").getRowData(ids) ;
		if (ids=="" || ids==null || ids==undefined)
		{
			alert("harap pilih data untuk di edit...");
		}else{
			init_natura();
			$("#form_mode_natura").val("GET");
			$("#edit_natura").dialog('open');
			$("#i_nik_natura").attr('disabled','disabled');
			$("#i_nama_natura").attr('disabled','disabled'); 
			
			$("#i_nik_natura").val(data.NIK);  
			$("#i_nama_natura").val(data.NAMA); 
			$("#i_val_natura").val(data.NATURA);  
		} 
	}
	
	function submit_natura()
	{
		var postdata = {};
		var mode= $("#form_mode_natura").val();
		postdata['NIK'] =  $("#i_nik_natura").val();
		postdata['NAMA'] =  $("#i_nama_natura").val(); 
		postdata['NATURA'] = $("#i_val_natura").val();
		postdata['PERIODE'] = $("#tahun").val() + $("#bulan").val();
		
		if (mode == "GET")
			{
				var ids = jQuery("#list_natura").getGridParam('selrow'); 
				var data = $("#list_natura").getRowData(ids) ;
				postdata['PERIODE'] = data.PERIODE;
	
				$.post( url+'m_natura/update_natura/', postdata,function(status)
				{ 
					var status = new String(status);
					//alert (status);
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							naturaReload();
							alert('data berhasil terupdate.') 
							$("#edit_natura").dialog('close');      
						 }else{
							naturaReload(); 
							alert(status);    
						 } 
					} else { 
						naturaReload(); 
						alert('data berhasil terupdate.')
						$("#edit_natura").dialog('close');        
				   };
				  });
			} 
			else if (mode == "POST") 
			{
				$.post(  url+'m_natura/create_natura', postdata,function(status) 
				{ 
					var status = new String(status);
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							naturaReload();
							alert('data berhasil tersimpan.') 
							$("#edit_natura").dialog('close');      
						 } else {
							//naturaReload();
							alert(status); 
							//$("#edit_natura").dialog('close');   
						 } 
					} else { 
						naturaReload(); 
						alert('data berhasil tersimpan.')
						$("#edit_natura").dialog('close');        
				   };  
				 } );     
	
			}       
	}
	
	function init_natura()
	{
		$("#i_nik_natura,#i_nama_natura").removeAttr('disabled');
		$("#form_mode_natura").val('');
		$("#i_nik_natura").val('');  
		$("#i_nama_natura").val(''); 
		$("#i_val_natura").val('');
		$("#edit_natura").dialog('close');    
	}
	
	function naturaReload(){ 
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
		jQuery("#list_natura").setGridParam({url:url+'m_natura/load_data_natura/-/'+periode}).trigger("reloadGrid");        
	}
	
	function nattoxls(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		urls = url + 'm_natura/natura_xls/'+periode,
		$.download(urls,''); 
	}
	
	function gnatura(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		var postdata = {}; 
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu..... Proses Generate Natura sedang diproses...";
		$("#progressbar").dialog('open');
		$.post( url+'m_natura/generate_natura/-/-/'+periode, postdata,function(status) 		  { 
            var status = new String(status);
               if(status.replace(/\s/g,"") != "") { 
                  $("#load").hide();
				  document.getElementById('msg').innerHTML= status;
				  $("#cok").attr("disabled", false);
               } else { 
				  $("#load").hide();
				  $("#cok").attr("disabled", false);
				  document.getElementById('msg').innerHTML= 'data tersimpan';
               };  
         }); 
	}
	
	var jGrid_natura = null;
	var colNamesT_natura = new Array();
	var colModelT_natura = new Array();
	colNamesT_natura.push('NIK');
	colModelT_natura.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 90, align:'center'});
	colNamesT_natura.push('Nama');
	colModelT_natura.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 160, align:'left'});
	colNamesT_natura.push('Type Karyawan');
	colModelT_natura.push({name:'TYPE_KARYAWAN',index:'TYPE_KARYAWAN', editable: false, hidden:false, width: 130, align:'center'});
	colNamesT_natura.push('Status Keluarga');
	colModelT_natura.push({name:'FAMILY_STATUS',index:'FAMILY_STATUS', editable: false, hidden:false, width: 120, align:'center'});
	colNamesT_natura.push('Periode');
	colModelT_natura.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:false, width: 90, align:'center'});
	colNamesT_natura.push('Natura (Rp.)');
	colModelT_natura.push({name:'NATURA',index:'NATURA', editable: false, editrules:{number:true}, formatter:'number', 			
				formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0},
				hidden:false, width: 100, align:'right'});
	var loadView_natura = function()
    {
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
		
       jGrid_natura = jQuery("#list_natura").jqGrid({
           url:url+'m_natura/load_data_natura/-/'+periode,
           mtype : "POST", datatype: "json",
           colNames: colNamesT_natura ,  colModel: colModelT_natura ,
           rownumbers:true, viewrecords: true, multiselect: false, 
           caption: "Natura Karyawan: "+"<?php echo $company_dest;?>", 
           rowNum:15, rowList:[10,20,30], multiple:true,
           height: 280, cellEdit: false, imgpath: gridimgpath,
           pager: jQuery('#pager_natura'), sortname: colNamesT_natura[0] 
        });
        jGrid_natura.navGrid('#pager_natura',{edit:false,add:false,del:false, search: false, refresh: true});
		jGrid_natura.navButtonAdd('#pager_natura',{
            caption:"Excell", buttonicon:'ui-icon-newwin',
            onClickButton: function(){  nattoxls();  }, position:"left"
        });
		jGrid_natura.navButtonAdd('#pager_natura',{
            caption:"Tambah", buttonicon:'ui-icon-newwin',
            onClickButton: function(){  add_natura(); }, position:"left"
        });
		jGrid_natura.navButtonAdd('#pager_natura',{
            caption:"Ubah",  buttonicon:'ui-icon-newwin',
            onClickButton: function(){  edit_natura(); }, position:"left"
         });
    }
	jQuery("#list_natura").ready(loadView_natura);
	/* end natura */
	/* start bpjs */
	var jGrid_bpjs = null;
	var colNamesT_bpjs = new Array();
	var colModelT_bpjs = new Array();
	colNamesT_bpjs.push('NIK');
	colModelT_bpjs.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 90, align:'center'});
	colNamesT_bpjs.push('Nama');
	colModelT_bpjs.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 160, align:'left'});
	colNamesT_bpjs.push('Type Karyawan');
	colModelT_bpjs.push({name:'TYPE_KARYAWAN',index:'TYPE_KARYAWAN', editable: false, hidden:false, width: 100, align:'center'});
	colNamesT_bpjs.push('Periode');
	colModelT_bpjs.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:true, width: 100, align:'center'});
	colNamesT_bpjs.push('No. BPJS Kesehatan');
	colModelT_bpjs.push({name:'NO_REG_BPJS_KES',index:'NO_REG_BPJS_KES', editable: false, hidden:false, width: 130, align:'center'});
	colNamesT_bpjs.push('Tunj. BPJS Kes.');
	colModelT_bpjs.push({name:'TUNJANGAN_BPJS_KES',index:'TUNJANGAN_BPJS_KES', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 100, align:'right'});
	colNamesT_bpjs.push('Pot. BPJS Kes.');
	colModelT_bpjs.push({name:'POTONGAN_BPJS_KES',index:'POTONGAN_BPJS_KES', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 100, align:'right'});
	colNamesT_bpjs.push('No. BPJS Ketenagakerjaan');
	colModelT_bpjs.push({name:'NO_REG_BPJS_TNG',index:'NO_REG_BPJS_TNG', editable: false, hidden:false, width: 170, align:'center'});
	colNamesT_bpjs.push('Tunj. BPJS Tng');
	colModelT_bpjs.push({name:'TUNJANGAN_BPJS_TNG',index:'TUNJANGAN_BPJS_TNG', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 100, align:'right'});
	colNamesT_bpjs.push('Pot. BPJS Tng');
	colModelT_bpjs.push({name:'POTONGAN_BPJS_TNG',index:'POTONGAN_BPJS_TNG', editable: false, editrules:{number:true}, 
				formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', 
				decimalPlaces: 0}, hidden:false, width: 100, align:'right'});
	colNamesT_bpjs.push('Perusahaan');
	colModelT_bpjs.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 90, align:'center'});
	var loadView_bpjs = function()
    {
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
        if (periode == ""){
            periode = "-";
        }
       jGrid_bpjs = jQuery("#list_bpjs").jqGrid({
           url:url+'m_employee_tp/read_grid_bpjs/-/'+periode,
           mtype : "POST", datatype: "json",
           colNames: colNamesT_bpjs ,  colModel: colModelT_bpjs ,
           rownumbers:true, viewrecords: true, multiselect: false, 
           caption: "Data BPJS Karyawan: "+"<?php echo $company_dest;?>", 
           rowNum:15, rowList:[10,20,30], multiple:true,
           height: 280, cellEdit: false, imgpath: gridimgpath,
           pager: jQuery('#pager_bpjs'), sortname: colNamesT_bpjs[0] 
        });
        jGrid_bpjs.navGrid('#pager_bpjs',{edit:false,add:false,del:false, search: false, refresh: true});
		jGrid_bpjs.navButtonAdd('#pager_bpjs',{
            caption:"Excell", buttonicon:'ui-icon-newwin',
            onClickButton: function(){  nattoxls();  }, position:"left"
        });
		jGrid_bpjs.navButtonAdd('#pager_bpjs',{
            caption:"Tambah", buttonicon:'ui-icon-newwin',
            onClickButton: function(){  add_natura(); }, position:"left"
        });
		jGrid_bpjs.navButtonAdd('#pager_bpjs',{
            caption:"Ubah",  buttonicon:'ui-icon-newwin',
            onClickButton: function(){  edit_natura(); }, position:"left"
         });
    }
	jQuery("#list_bpjs").ready(loadView_bpjs);
	/* end bpjs */
	function gbpjs(){
		var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
		var postdata = {}; 
		$("#cok").attr("disabled", true);
		$("#load").show();
		document.getElementById('msg').innerHTML= "Mohon menunggu..... Proses Generate BPJS sedang diproses...";
		$("#progressbar").dialog('open');
		$.post( url+'m_employee_tp/generate_bpjs_kes/'+periode, postdata,function(status) 		  { 
            var status = new String(status);
               if(status.replace(/\s/g,"") != "") { 
                  $("#load").hide();
				  document.getElementById('msg').innerHTML= status;
				  $("#cok").attr("disabled", false);
               } else { 
				  $("#load").hide();
				  $("#cok").attr("disabled", false);
				  document.getElementById('msg').innerHTML= 'data selesai digenerate';
				  jQuery("#list_bpjs").setGridParam({url:url+"m_employee_tp/read_grid_bpjs/-/"+periode}).trigger("reloadGrid");
               };  
         }); 
	}
    </script>


<p style="padding-top:30px;"/>
<div class="demo">
<? if(isset($periode)) {  echo $periode;  } ?>
<br />
<br />
<div id="tabs" style="width:auto;">
	<ul>
		<li><a href="#tabs-1">Lembur</a></li>
		<li><a href="#tabs-2">Kontanan</a></li>
		<li><a href="#tabs-3">Tunjangan dan Potongan</a></li>
        <li><a href="#tabs-4">Natura</a></li>
        <li><a href="#tabs-5">BPJS</a></li>
	</ul>
	<div id="tabs-1">
		<p> 
        	<table id="list_lembur" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    		<div id="pager_lembur" class="scroll"></div>
            
    	</p>
	</div>
	<div id="tabs-2">
		<p>
        	<table id="list_kontanan" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    		<div id="pager_kontanan" class="scroll"></div>
        </p>
	</div>
	<div id="tabs-3">
		<p>
       		<table border="0" class="teks_" cellpadding="2" cellspacing="4">
                <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
                <tr><td>NIK</td><td>:</td><td>
                <input type="text" class="input" id="tsearch_nik" onkeydown="doSearchTp(arguments[0]||event)" />
                </td><td>Nama</td><td>:</td><td>
                <input type="text" class="input" id="tsearch_nama" onkeydown="doSearchTp(arguments[0]||event)" />
                </td><td style="padding-left:15px;"></td></tr>
            </table>
        	<table id="list_tunpot" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    		<div id="pager_tunpot" class="scroll"></div>
            <div id="gpph" class="scroll" style="float:left; padding-top:5px;">
            <input type="button"  id="gnatura" value="generate pph21" class="button" onClick="gpph()" style=" font-size:10px;">
            </div><br/>
            <!-- form tunjangan & potongan -->
            <div id="edit_tunpot">
                <table width="100%" border="0" class="teks_" >
                    <tr>
                        <td width="150">NIK</td><td>:</td><td>
                        <input tabindex="1" type="text" style="text-transform: uppercase; width:100px;" id="i_nik_tunpot" class="input" maxlength="20"/></td>
                    </tr>
                    <tr>
                        <td width="150">Nama</td><td>:</td><td>
                        <input tabindex="2" type="text" style="text-transform: uppercase; width:200px;" id="i_nama_tunpot" class="input" maxlength="20"/></td>
                    </tr>
                    <tr>
                        <td width="150">Tunjangan Jabatan</td><td>:</td><td> 
                        <input tabindex="3" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_tunjab" maxlength="25" /></td>
                    </tr>
                    <tr>
                        <td width="150">Tunjangan Cuti Tahunan</td><td>:</td><td> 
                        <input tabindex="3" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_tuncuti" maxlength="25" /></td>
                    </tr>
                    <tr>
                        <td width="150">Kompensasi Cuti 5 Tahun</td><td>:</td><td> 
                        <input tabindex="3" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_komcuti" maxlength="25" /></td>
                    </tr>
                    <tr>
                        <td width="150">Potongan Lain</td><td>:</td><td>
                        <input tabindex="4" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_potlain" maxlength="25"/></td>
                    </tr>
                     <tr>
                        <td width="150">Subsidi Kendaraan</td> <td>:</td><td>
                        <input tabindex="5" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_subkend" maxlength="25"/></td>
                    </tr>
                    <tr>
                        <td width="150">Tunjangan Transport</td> <td>:</td><td>
                        <input tabindex="6" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_tuntrans" maxlength="25"/></td>
                    </tr>
                    <tr>
                        <td width="150">Rapel</td> <td>:</td><td>
                        <input tabindex="7" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_rapel" maxlength="25"/></td>
                    </tr>
                    <tr>
                        <td width="150">THR</td> <td>:</td><td>
                        <input tabindex="8" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_thr" maxlength="25"/></td>
                    </tr>
                     <tr>
                        <td width="150">PPH 21</td><td>:</td><td>
                        <input tabindex="9" type="text" style="text-transform: uppercase; width:150px ;" class="positive" id="i_pph" maxlength="25"/></td>
                    </tr>
                     <tr>
                        <td width="150">Keterangan</td><td>:</td><td>
                        <textarea tabindex="10" type="text" id="i_ket" class="input" style="width:170px" maxlength="25"/></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><input type="hidden" id="form_mode_tunpot"></td>
                    </tr>    
                </table>
                </div>
            <!-- end form tunjangan & potongan -->
        </p>
	</div>
    <div id="tabs-4">
    	<p>
         <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
             <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td>NIK / Nama</td><td>:</td><td>
            <input type="text" class="input" id="nsearch" onkeydown="doSearchNat(arguments[0]||event)" />
            </td><td></td><td></td><td>
            <!-- <input type="text" class="input" id="nsearch_nama" onkeydown="doSearchNat(arguments[0]||event)" /> -->
            </td><td style="padding-left:15px;"></td></tr>
             <tr><td colspan="8">&nbsp;</td></tr>
        </table>
        	<table id="list_natura" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
            <div id="pager_natura" class="scroll"></div>
            <div id="glembur" class="scroll" style="float:left; padding-top:5px;"><input type="button"  id="gnatura" value="generate natura" class="button" onClick="gnatura()"  style=" font-size:10px;"></div><br/>
          <!-- form edit natura -->  
            <div id="edit_natura">
                <table width="100%" border="0" class="teks_" >
                    <tr>
                        <td width="100" height="24">NIK</td>
                        <td>:</td>
                        <td>
                        <input tabindex="10" type="text" style="text-transform: uppercase; width:100px ; border-width: 1px; border-style: solid; font-family:Verdana,Arial,sans-serif; font-size: 11px; font-weight: normal; vertical-align: middle; margin-left: 5px; padding-top: 2px; padding-bottom: 2px; padding-left: 7px; border-color: #C5C5C5;" id="i_nik_natura" maxlength="20"/>
                        </td>
                        
                    </tr>
                    <tr>
                        <td width="100" height="26">Nama</td>
                        <td>:</td>
                        <td>
                        <input disabled="disabled" tabindex="10" type="text" style="text-transform: uppercase; width:200px ; border-width: 1px; border-style: solid; font-family:Verdana,Arial,sans-serif; font-size: 11px; font-weight: normal; vertical-align: middle; margin-left: 5px; padding-top: 2px; padding-bottom: 2px; padding-left: 7px; border-color: #C5C5C5;" id="i_nama_natura" maxlength="20"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="100"> Natura</td>
                        <td>:</td>
                        <td>
                        <input tabindex="10" type="text" style="text-transform: uppercase; width:150px ; border-width: 1px; border-style: solid; font-family:Verdana,Arial,sans-serif; font-size: 11px; font-weight: normal; vertical-align: middle; margin-left: 5px; padding-top: 2px; padding-bottom: 2px; padding-left: 7px; border-color: #C5C5C5;" class="positive" id="i_val_natura" maxlength="25"/>
                        
                    </tr>
                    <tr>
                        <td><input type="hidden" id="form_mode_natura"></td>
                    </tr>    
                </table>
                </div>
            <!-- end form edit natura -->
        </p>
    </div>
    
    <div id="tabs-5">
    	<p>
         <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
             <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td>NIK / Nama</td><td>:</td><td>
            <input type="text" class="input" id="nsearchbpjs" onkeydown="doSearchBPJS(arguments[0]||event)" />
            </td><td></td><td></td><td>
            <!-- <input type="text" class="input" id="nsearch_nama" onkeydown="doSearchNat(arguments[0]||event)" /> -->
            </td><td style="padding-left:15px;"></td></tr>
             <tr><td colspan="8">&nbsp;</td></tr>
        </table>
        	<table id="list_bpjs" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
            <div id="pager_bpjs" class="scroll"></div>
            <div id="gbpjs" class="scroll" style="float:left; padding-top:5px;"><input type="button"  id="btnBpjs" value="generate BPJS" class="button" onClick="gbpjs()"  style=" font-size:10px;"></div><br/>
            <!-- end form edit natura -->
        </p>
    </div>
</div>
<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 
<!-- end progress bar -->
</div><!-- End demo -->

