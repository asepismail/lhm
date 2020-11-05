	
	$(function () {
	            $("#GC_FROM")
	              .autocomplete( 
	                url+"m_gang_activity_detail/gangc/", {
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
	
	
			$(function () {
	            $("#GC_TO").autocomplete(url+"m_gang_activity_detail/gangc/", {
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
	
	$(function() {
	$("#LHM_DATE").datepicker({dateFormat:"yy-mm-dd"});
	});
	
	$("#updatedata").hide();
	
	post_pinjam();
	
	$.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
	var maintab =jQuery('#tabs','#RightPane').tabs({
        add: function(e, ui) {
            // append close thingy
            $(ui.tab).parents('li:first')
                .append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
                .find('span.ui-tabs-close')
                .click(function() {
                    maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
                });
            // select just added tab
            maintab.tabs('select', '#' + ui.panel.id);
        }
    });
    



$(function(){
	$("#ajax_display").ajaxStart(function(){
			$('#htmlExampleTarget').hide();             
			$(this).html("<img alt='' src='themes/wait.gif' ><br >Waiting ...");
		});
		
	$("#ajax_display").ajaxSuccess(function(){
   		$(this).html('');
 	});
	$("#ajax_display").ajaxError(function(url){
   		alert('jQuery ajax is error ');
 	});
});

function CheckPinjaman() {
		var gc = document.getElementById("GANG_CODE").value;
		var tdate = document.getElementById("LHM_DATE").value;
		var tgl = tdate.replace(/-/gi, "");
		var postdata = {}; 
		
		$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_pinjaman/'+tgl+'/'+gc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					pinjam = data;
				}
				
		});
		
		return pinjam;
}

			
function CheckAfdeling() {
	
		var jumlah = {};
		var postdata = {}; 
		postdata['GANG_CODE'] = $("#GANG_CODE").val();
		
		//check company-------------		
 		
		var gc = document.getElementById("GANG_CODE").value;
		var tdate = document.getElementById("LHM_DATE").value;
		var tgl = tdate.replace(/-/gi, "");
		
		$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_gad/'+tgl+'/'+gc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				
				$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_mandor/'+gc, postdata, function(data){
			for (var i = 0; i < data.length ; i++ ){
				document.getElementById("AFD").value = data[i].name;
				document.getElementById("NIK_MANDOR").value = data[i].mandore;
				//document.getElementById("NM_KEMANDORAN").value = data[i].nm_kemandoran;
				document.getElementById("NM_MANDOR").value = data[i].nm_m;
				document.getElementById("KD_KERANI").value = data[i].kerani;
				document.getElementById("NM_KERANI").value = data[i].nm_k;
				
				if (jumlah != 0){
					$("#submitdata").hide();
					$("#updatedata").show();
					jQuery("#list_lhm").setGridParam({url:url+"m_gang_activity_detail/read_exist_gad/"+tgl+"/"+gc}).trigger("reloadGrid");		
				} else {
					$("#submitdata").show();
					$("#updatedata").hide();
						
					jQuery("#list_lhm").setGridParam({url:url+"m_gang_activity_detail/cek_anggota_pinjam/"+tgl+"/"+gc}).trigger("reloadGrid");	

				}
						
						
				}						
	  		}, "json"
		); //end post
		
			});

	} //end function

function CheckAnggotaGang() {
	
		var postdata = {}; 
		postdata['GANG_CODE'] = $("#GANG_CODE").val();
		
		//check company-------------		
 		
		 var gc = document.getElementById("GANG_CODE").value;
		 var tdate = document.getElementById("LHM_DATE").value;
		 var tgl = tdate.replace(/-/gi, "");
		 
		 jQuery("#list_lhm").setGridParam({url:url+"m_gang_activity_detail/cek_anggota/"+tgl+"/"+gc}).trigger("reloadGrid"); 
		/*$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_anggota/'+gc, postdata, function(data){
			for (var i = 0; i < data.length ; i++ ){
				document.getElementById("AFD").value = data[i].name;
				}						
	  		}, "json"
		); */ //end post
	} //end function
	
function CheckKaryawan() {
	
	//var usr = document.getElementById("uname");
	
		
		var postdata = {}; 
		postdata['EMPLOYEE_CODE'] = $("#EMPLOYEE_CODE").val();

		//check company-------------		
 		
		var ec = document.getElementById("EMPLOYEE_CODE").value;
		$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_karyawan/'+ec, postdata, function(data){
			for (var i = 0; i < data.length ; i++ ){
				document.getElementById("NM_EMPLOYEE").value = data[i].name;
				document.getElementById("TYPE_EMPLOYEE").value = data[i].type;
				}						
	  		}, "json"
		); //end post
	} //end function

function CheckLokasi() {
	
		var postdata = {}; 
		postdata['LOCATION_TYPE_CODE'] = $("#LOCATION_TYPE_CODE").val();

		//check company-------------		
  		var location = document.getElementById("LOCATION_CODE");  
		var len = location.length		
		//alert (len);
  		if (len > 0)
  		{
			for (var ii = 0; ii < len ; ii++ ){
				//alert ("remove data ke : "+ii);
				location.remove(location.length - 1);
		      	}
		}
		
		var option = document.createElement("option");  
		option.text = "Select Location Type...";  
		option.value = "";  
		try {  
				location.add(option, null); //Standard  
		  	}catch(error) {  
		    		 location.add(option); // IE 
		 };  //end try				
 		
		var lc = document.getElementById("LOCATION_TYPE_CODE").value;
		$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_lokasi/'+lc, postdata, function(data){
			for (var i = 0; i < data.length ; i++ ){
					//alert("id = "+data[i].id+"  comp = "+data[i].name);
					var location = document.getElementById("LOCATION_CODE");  
					var option = document.createElement("option");  
					
					option.text = data[i].name;  
					option.value = data[i].code;
					  
						try {  
							location.add(option, null); //Standard  
					  	}catch(error) {  
					    		 location.add(option); // IE 
					 };  //end try
				}; //end for
										
	  		}, "json"
		); //end post
	} //end function
	
	function CheckActivity() {
	
	//var usr = document.getElementById("uname");
	
		
		var postdata = {}; 
		postdata['ACTIVITY_CODE'] = $("#ACTIVITY_CODE").val();

		//check company-------------		
 		
		var ac = document.getElementById("ACTIVITY_CODE").value;
		$.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_activity/'+ac, postdata, function(data){
			for (var i = 0; i < data.length ; i++ ){
				document.getElementById("ACTIVITY_DESC").value = data[i].name;
				}						
	  		}, "json"
		); //end post
	} //end function
	

//-------------------------------------------------------- current time
		
		function current_date(){
			var currentTime = new Date()
			var month = currentTime.getMonth() + 1
			var day = currentTime.getDate()
			var year = currentTime.getFullYear()
			var time = year + "/" + month + "/" + day
			
			return time;
		}
		
	  	//--------------------------------------------------------- url global js
		var url = "<?= base_url().'index.php/' ?>";
				
		function gc_kodeabsen(){
             var absen = jQuery.ajax({url:url+"/m_gang_activity_detail/kode_absen",formatter:"select", async: false}).responseText;
			 return absen;
             
    	}
		
		function gc_loctype(){
             var loctype = jQuery.ajax({url:url+"/m_gang_activity_detail/location_type",formatter:"select", async: false}).responseText;
			 return loctype;
             
    	}
		
		function gc_location(loc){
             var loctype = jQuery.ajax({url:url+"/m_gang_activity_detail/location/"+loc,formatter:"select", async: false}).responseText;
			 return loctype;
             
    	}
		
		function gc_activity(){
             var loctype = jQuery.ajax({url:url+"/m_gang_activity_detail/activity",formatter:"select", async: false}).responseText;
			 return loctype;
             
    	}
		
		function giveLocType(){		
		var ids = jQuery("#list_lhm").getGridParam('selrow'); 
		var rets = jQuery("#list_lhm").getRowData(ids); 
		var type = rets.LOCATION_TYPE_CODE;
		return type;
		} 
				
		var jGrid_lhm = null;
        var colNamesT_lhm = new Array();
        var colModelT_lhm = new Array();
		
		colNamesT_lhm.push('no');
        colModelT_lhm.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});
       								
		colNamesT_lhm.push('NIK');
		colModelT_lhm.push({name:'NIK',index:'NIK', 
			editable: true,  
			edittype: "text",
			editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+"m_gang_activity_detail/search_nik/", {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_dl, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_dl :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#NIK").val(item.res_name );
	                var id = jQuery("#list_lhm").getGridParam('selrow');
					
					if (id) 
					{ 
						var ret = jQuery("#list_lhm").getRowData(id);
						ret.NM_K = (item.res_name);
						jQuery("#list_lhm").setRowData(id,{NM_K:ret.NM_K});

					}
				});
				  },
				  // dataevents
				 }, width: 80, align:'center'});					
        colNamesT_lhm.push('GANG_CODE');
        colModelT_lhm.push({name:'GANG_CODE',index:'GANG_CODE', editable: false,hidden:true, width: 70, align:'center'});
		
        colNamesT_lhm.push('Tanggal');
        colModelT_lhm.push({name:'LHM_DATE',index:'LHM_DATE', editable: false, hidden:true, width: 80, align:'center'});
		

        colNamesT_lhm.push('Nama');
		colModelT_lhm.push({name:'NM_K',index:'NM_K', editable: false, width: 120, align:'left'});
		
		colNamesT_lhm.push('Absensi');
        colModelT_lhm.push({name:'TYPE_ABSENSI',index:'TYPE_ABSENSI', 
			editable: true,  
			edittype: "text",
			editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+"m_gang_activity_detail/type_absensi/", {
	                  dataType: 'ajax',
	                  autoFill: false,
						mustMatch: true,
					matchContains: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_dl, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_dl :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#TYPE_ABSENSI").val(item.res_name );
					var a = item.res_id;
					if (a == 'KJI'){
						karyawan_in();
					} else {
						if (a == 'KJO')
						{
							karyawan_out();
						}
					}
	              });
				  }}, width: 80, align:'center'}); 
			
		
      	colNamesT_lhm.push('Tipe');
     	colModelT_lhm.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE',editable: true, 
				edittype: "text", editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"m_gang_activity_detail/location_type", {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_name :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#LOCATION_TYPE_CODE").val(item.res_name );
					 var id = jQuery("#list_lhm").getGridParam('selrow');
					
						if (id) 
						{ 
							var ret = jQuery("#list_lhm").getRowData(id);
							jQuery("#list_lhm").setRowData(id,{LOCATION_CODE:""});
							jQuery("#list_lhm").setRowData(id,{ACTIVITY_CODE:""});
	
						}
	              });
          }}, width: 50, align:'center'});

      	colNamesT_lhm.push('lokasi');
        colModelT_lhm.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true
		,async: false,edittype: "text",editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { 
				
	            $(elem)
	              .autocomplete( 
				  	
	                url+"m_gang_activity_detail/location/"+giveLocType(), {
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
	                $("#LOCATION_TYPE_CODE").val(item.res_name );
	              });
          }}, width: 100, align:'center'});
        colNamesT_lhm.push('Aktivitas');
        colModelT_lhm.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true, edittype: "text", 
		editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+"m_gang_activity_detail/activity/"+giveLocType(), {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_d, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_d :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#LOCATION_TYPE_CODE").val(item.res_name );
	              });
				  }}, width: 80, align:'center'});
		
		colNamesT_lhm.push('HK');
        colModelT_lhm.push({name:'HK_JUMLAH',index:'HK_JUMLAH', editable: true, editrules:{number:true, maxValue:1}, width: 60, align:'center'});

		colNamesT_lhm.push('Satuan');
        colModelT_lhm.push({name:'HSL_KERJA_UNIT',index:'HSL_KERJA_UNIT', editable: true,
		edittype: "text", editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"m_gang_activity_detail/satuan/", {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_name, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_name :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#HSL_KERJA_UNIT").val(item.res_id );
	              });
          }}, width: 70, align:'center'});
 		colNamesT_lhm.push('Volume');
        colModelT_lhm.push({name:'HSL_KERJA_VOLUME',index:'HSL_KERJA_VOLUME', editrules:{number:true}, editable: true, width: 70, align:'center'});

 		colNamesT_lhm.push('Tarif / Satuan');
        colModelT_lhm.push({name:'TARIF_SATUAN',index:'TARIF_SATUAN', editrules:{number:true}, editable: true, width: 90, align:'center'});
		
		colNamesT_lhm.push('Premi');
        colModelT_lhm.push({name:'PREMI',index:'PREMI',editrules:{number:true}, editable: true, width: 70, align:'center'});
				
		colNamesT_lhm.push('Jam Lembur');
        colModelT_lhm.push({name:'LEMBUR_JAM',index:'LEMBUR_JAM',editrules:{number:true, maxValue:14}, editable: true, width: 80, align:'left'});
	
		colNamesT_lhm.push('Penalti');
        colModelT_lhm.push({name:'PENALTI',index:'PENALTI', editrules:{number:true}, editable: true, width: 70, align:'center'});
		colNamesT_lhm.push('company');
        colModelT_lhm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, editable: false, width: 100, align:'left'});
 		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
        
		var loadView_lhm = function()
        {
            jGrid_lhm = jQuery("#list_lhm").jqGrid(
            {
				url:url+'m_gang_activity_detail/read_exist_gad/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_lhm ,
                colModel: colModelT_lhm ,
               	sortname: colNamesT_lhm[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_lhm"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
					var id = jQuery("#list_lhm").getGridParam('selrow');
					var ret = jQuery("#list_lhm").getRowData(id);
					//var tm = array["CL","CT","H1", "H2"]
					
					if (ret.TYPE_ABSENSI == "KJO")	{
						
							if( ret.LOCATION_TYPE_CODE != "" || ret.LOCATION_CODE != "" || ret.ACTIVITY_CODE != "" || ret.HSL_KERJA_UNIT != "" || ret.HSL_KERJA_VOLUME != "" || ret.HK_JUMLAH != "" || ret.LEMBUR_JAM != "" || ret.TARIF_SATUAN != "" || ret.PREMI != "" || ret.PENALTI != "" )
							{
								 alert("data karyawan yang keluar tidak boleh diisi.");
							} 
						
					} else if (ret.TYPE_ABSENSI in {'CL':'','CT':'','H1':'','H2':'','H2':'','H2':''}) {
							if( ret.LOCATION_TYPE_CODE != "" || ret.LOCATION_CODE != "" || ret.ACTIVITY_CODE != "" || ret.HSL_KERJA_UNIT != "" || ret.HSL_KERJA_VOLUME != "" || ret.HK_JUMLAH != "" || ret.LEMBUR_JAM != "" || ret.TARIF_SATUAN != "" || ret.PREMI != "" || ret.PENALTI != "" )
							{
								 alert("karyawan tidak masuk, data tidak dapat diisi.");
							} 
					}else {
						if( ret.NIK == "" )
							{
								 alert("NIK karyawan tidak boleh kosong.");
							} 	
					}	
				}
				
            });
            jGrid_lhm.navGrid('#pager_lhm',{edit:false,del:false,add:false, search: false, refresh: true});
			
			//----------------------------------------------------------------button add di grid		
			jGrid_lhm.navButtonAdd('#pager_lhm',{
			   caption:"Lihat daftar pinjaman karyawan  ", 
			   buttonicon:"ui-icon-add", 
			  
			   onClickButton: function(){ 
			  		var gc = document.getElementById("GANG_CODE").value;
					var tdate = document.getElementById("LHM_DATE").value;
					var tgl = tdate.replace(/-/gi, "");
					
					if (gc != ""){
						if (tdate != ""){
							jQuery("#list_pjm").setGridParam({url:url+'m_gang_activity_detail/read_pinjaman/'+tgl+'/'+gc}).trigger("reloadGrid"); 
					$('#pjm').dialog('open');
						}	
					} else {
						alert("tanggal dan kemandoran tidak boleh kosong!");
					}
			   }, 
			    position:"left",
			});
			
			jGrid_lhm.navButtonAdd('#pager_lhm',{
			   caption:"Export ke Excell", 
			   buttonicon:"ui-icon-add", 
			  
			   onClickButton: function(){ 
			
				var tdate = document.getElementById("LHM_DATE").value;
				var gc = document.getElementById("GANG_CODE").value;
				var tgl = tdate.replace(/-/gi, "");
				window.location = url+'m_gang_activity_detail/create_excel/'+ tgl + '/' + gc;
			   
			   }, 
			    position:"left",
			});

            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_lhm").ready(loadView_lhm);
		
						
		function post() {
			
		   var postdata = {}; 
		  //Data dari form untuk gang_activity
		  	postdata['LHM_DATE'] = $("#LHM_DATE").val() ; 
		    postdata['GANG_CODE'] = $("#GANG_CODE").val() ; 
		    postdata['MANDORE_CODE'] = $("#NIK_MANDOR").val(); 
		    postdata['KERANI_CODE'] = $("#KD_KERANI").val(); 
			
			
		    // Data dari grid
		    i=0;
		    s = $("#list_lhm").getDataIDs(); 
			postdata['jumlah'] = s;
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_lhm").getRowData(rowid) ; 
		        i=i+1;
				//untuk GA
				postdata['ATT_DATE'+i] = $("#LHM_DATE").val() ; 
				
				//untuk GAD
				postdata['GANG_CODE'+i] = $("#GANG_CODE").val() ;
				postdata['LHM_DATE'+i] = $("#LHM_DATE").val() ; 
		        postdata['NIK'+i] = data.NIK ; 
		        postdata['TYPE_ABSENSI'+i] = data.TYPE_ABSENSI ;
		        postdata['LOCATION_TYPE_CODE'+i] = data.LOCATION_TYPE_CODE; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE; 
				postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE; 
				postdata['HSL_KERJA_UNIT'+i] = data.HSL_KERJA_UNIT; 
				postdata['HSL_KERJA_VOLUME'+i] = data.HSL_KERJA_VOLUME; 
				postdata['HK_JUMLAH'+i] = data.HK_JUMLAH; 
				postdata['LEMBUR_JAM'+i] = data.LEMBUR_JAM;  
				postdata['TARIF_SATUAN'+i] = data.TARIF_SATUAN; 
				postdata['PREMI'+i] = data.PREMI; 
				postdata['PENALTI'+i] = data.PENALTI; 
				postdata['COMPANY_CODE'+i] = '<?php echo $company_code; ?>'; 
					
		       }); 

		   // Post it all 	   
		  $.post( url+'m_gang_activity_detail/insert', postdata,function(message,status) { 
		   		
		        if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						alert('data berhasil tersimpan.')
						$("#submitdata").hide();
						$("#updatedata").show();
						CheckAfdeling();
							
		           };
		      } ); 
		}
		
		function hapus(tgl, gc) {
		var postdata = {}; 
			$.post( url+'m_gang_activity_detail/delete/'+tgl+'/'+gc, postdata,function(message,status) { 
		        if(status !== 'success') { 
		            alert('berhasil'); 
		          } else { 
			
				var i=1;
		            }; 
		      } );
		}		
		
		function update() {
			
			//var regExp = /,/g ; 
		    var postdata = {}; 
		    // Data dari form
		    postdata['LHM_DATE'] = $("#LHM_DATE").val() ; 
		    postdata['GANG_CODE'] = $("#GANG_CODE").val() ; 
		    postdata['MANDORE_CODE'] = $("#NIK_MANDOR").val(); 
		    postdata['KERANI_CODE'] = $("#KD_KERANI").val(); 
			
			postdata['jumlah'] = jQuery('#list_lhm').getGridParam('records')
		    // Data dari grid
		    i=0;
		    s = $("#list_lhm").getDataIDs(); 
		    $.each(s, function(n, rowid) { 
		        var data = $("#list_lhm").getRowData(rowid) ; 
		        i=i+1;

				//untuk GA
				postdata['ATT_DATE'+i] = $("#LHM_DATE").val() ; 		
				//untuk GAD
				postdata['GANG_CODE'+i] = $("#GANG_CODE").val() ;
				postdata['LHM_DATE'+i] = $("#LHM_DATE").val() ; 
		        postdata['NIK'+i] = data.NIK ; 
		        postdata['ID'+i] = data.ID ;
		        postdata['TYPE_ABSENSI'+i] = data.TYPE_ABSENSI ;
		        postdata['LOCATION_TYPE_CODE'+i] = data.LOCATION_TYPE_CODE; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE; 
				postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE; 
				postdata['HSL_KERJA_UNIT'+i] = data.HSL_KERJA_UNIT; 
				postdata['HSL_KERJA_VOLUME'+i] = data.HSL_KERJA_VOLUME; 
				postdata['HK_JUMLAH'+i] = data.HK_JUMLAH; 
				postdata['LEMBUR_JAM'+i] = data.LEMBUR_JAM;  
				postdata['TARIF_SATUAN'+i] = data.TARIF_SATUAN; 
				postdata['PREMI'+i] = data.PREMI; 
				postdata['PENALTI'+i] = data.PENALTI; 
				postdata['COMPANY_CODE'+i] = '<?php echo $company_code; ?>'; 
		    
			}); 
		
			var gc = document.getElementById("GANG_CODE").value;
			var tdate = document.getElementById("LHM_DATE").value;
			var ccode = document.getElementById("ccode").value;
			var tgl = tdate.replace(/-/gi, "");
			$.post( url+'m_gang_activity_detail/delete/'+tgl+'/'+gc, postdata,function(data,status) {
				if(status !== 'success') { 
				   } else { 
						if(data != 0){
							$.post( url+'m_gang_activity_detail/update/'+gc, postdata,function(message,status) { 
					        if(status !== 'success') { 
					            	alert('data untuk tanggal ini sudah terisi.'); 
					          } else { 
							  	
									alert('data berhasil tersimpan.')
									$("#submitdata").hide();
									$("#updatedata").show();
									CheckAfdeling();	
					           }; 
					      	} );
						}
				    };
			} );
						  			
		}
		
	//---------------------------------------------------------------- Modal Dialog
		
		function gangc() {
	            $("#GANG_CODE")
	              .autocomplete( 
	                url+"m_gang_activity_detail/gangc/", {
	                  dataType: 'ajax',
					  width:350,
	                  multiple: false,
					  limit:20,
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
	                $("#GANG_CODE").val(item.res_id );
					CheckAfdeling();
	              });
          }
		  
		function post_pinjam () {
			$("#peminjaman").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 230,
				width: 340,
				modal: true,
				buttons: {
					'simpan	': function() {
							
						var postdata2 = {}; 
						  // Data dari form
						    postdata2['TGL_PINDAH'] = $("#TGL_PINDAH").val() ; 
						    postdata2['GC_PINDAH'] = $("#GC_PINDAH").val() ; 
						    postdata2['NIK_PINDAH'] = $("#NIK_PINDAH").val(); 
						    postdata2['GC_FROM'] = $("#GC_FROM").val(); 
						    postdata2['GC_TO'] = $("#GC_TO").val(); 
						    postdata2['REMARK'] = $("#REMARK").val(); 
						    postdata2['STATUS_PINJAM'] = $("#STATUS_PINJAM").val(); 
						    
						   // Post it all 
						   
						    $.post( url+'m_gang_activity_detail/insert_pinjaman', postdata2,function(message,status) 									{ 
							        	if(status !== 'success') { 
								            alert('data tidak tersimpan'); 
							        	} else { 
											alert('data berhasil tersimpan');
											$(this).dialog('close');	
											init_pinjam() 					
							          	}; 
						      } ); 
									
												
								},
							'Tutup	': function() {
									$(this).dialog('close');
									init_pinjam();
											
							}
					
				} 
			}); 
		}
		
		function init_pinjam(){
				
				$("#peminjaman").dialog('');
				$("#GC_PINDAH").val(''); 
				$("#TGL_PINDAH").val(''); 
				$("#NIK_PINDAH").val('');
				$("#GC_FROM").val(''); 
				$("#GC_TO").val(''); 
				$("#REMARK").val('');	
				$("#STATUS_PINJAM").val('');	
					
		}
		
		function karyawan_in()
		{
			var gc = document.getElementById("GANG_CODE").value;
			var tdate = document.getElementById("LHM_DATE").value;
			var ccode = document.getElementById("ccode").value;
			var id = jQuery("#list_lhm").getGridParam('selrow');
			
			var tgl = tdate.replace(/-/gi, "");
					//$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_lhm").getRowData(id); 
						
						$("#peminjaman").dialog('open');
						$("#GC_PINDAH").val(gc); 
						$("#TGL_PINDAH").val(tdate); 
						$("#NIK_PINDAH").val(ret.NIK);
						$.get(url+'m_gang_activity_detail/cek_kmandoran_asal/'+ tgl + '/' + ret.NIK, function(data) {							$("#GC_FROM").val(data);
						});
						
						$("#NM_PINDAH").val(ret.NM_K);  
						$("#GC_TO").val(gc); 
						$("#STATUS_PINJAM").val("KJO"); 
					}
			
		}
		
		function karyawan_out()
		{
			var gc = document.getElementById("GANG_CODE").value;
			var tdate = document.getElementById("LHM_DATE").value;
			var ccode = document.getElementById("ccode").value;
			var id = jQuery("#list_lhm").getGridParam('selrow');
					//$("#form_mode").val('GET');
					 
					if (id) 
					{ 
						var ret = jQuery("#list_lhm").getRowData(id); 
						
						$("#peminjaman").dialog('open');
						$("#GC_PINDAH").val(gc); 
						$("#TGL_PINDAH").val(tdate); 
						$("#NIK_PINDAH").val(ret.NIK); 
						$("#NM_PINDAH").val(ret.NM_K); 
						$("#GC_FROM").val(gc); 
						$("#STATUS_PINJAM").val("KJI"); 
					}
			
		}
		
		function addrow(){
						var gc = document.getElementById("GANG_CODE").value;
						var ids = jQuery("#list_lhm").getDataIDs();
						var i = ids.length;
						//var i = jQuery('#list_lhm').getGridParam('records');
						if (gc != ""){
								
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {GANG_CODE:jdesc1};
							}
				 		   	var su=jQuery("#list_lhm").addRowData(i,datArr,"last");
							var sa=jQuery("#list_lhm").setRowData(id,{no:i});
					
						} else {
							alert('kode kemandoran tidak boleh kosong!');							
						}
		}
		
		/* pinjaman grid */
		
		var jGrid_pjm = null;
        var colNamesT_pjm = new Array();
        var colModelT_pjm = new Array();
        //var gridimgpath = 'http://localhost/vehicle/resources/jqGrid/themes/basic/images';
                       
        colNamesT_pjm.push('no');
        colModelT_pjm.push({name:'no',index:'no', editable: false, hidden:true, width: 40, align:'center'});
		
        colNamesT_pjm.push('Kode kemandoran');
        colModelT_pjm.push({name:'GANG_CODE',index:'GANG_CODE', hidden:true, editable: false, width: 100, align:'center'});
        colNamesT_pjm.push('Tanggal Peminjaman');
        colModelT_pjm.push({name:'BDATE',index:'BDATE', editable: false, width: 135, align:'center'});
		colNamesT_pjm.push('NIK');
        colModelT_pjm.push({name:'NIK',index:'NIK', editable: false, width: 100, align:'left'});
		colNamesT_pjm.push('Nama');
        colModelT_pjm.push({name:'NAMA',index:'NAMA', editable: false, width: 130, align:'left'});
      	colNamesT_pjm.push('Dari');
        colModelT_pjm.push({name:'FROM',index:'FROM', editable: true, width: 80, align:'left'});
      	colNamesT_pjm.push('Ke');
        colModelT_pjm.push({name:'TO',index:'TO', editable: true,width: 80, align:'left'});
      	colNamesT_pjm.push('Status');
        colModelT_pjm.push({name:'STATUS',index:'STATUS', editable: false, width: 80, align:'left'});
      	colNamesT_pjm.push('perusahaan');
        colModelT_pjm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 100, align:'left'});

		colNamesT_pjm.push('');
		colModelT_pjm.push({name:'action',index:'action', align:'center', resizable:false, sortable:false, editable: false,hidden:false, width: 30});
	
		
        var loadView_pjm = function()
        {
            jGrid_pjm = jQuery("#list_pjm").jqGrid(
            {
                url:url+'/m_gang_activity_detail/xx/xx',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_pjm ,
                colModel: colModelT_pjm ,
				rownumbers:true,
                rowNum:20,
                height: 240,
				cellEdit: true,
				cellsubmit: 'clientArray',
                imgpath: gridimgpath,
                pager: jQuery('#pager_pjm'),
                sortname: colNamesT_pjm[0],
                viewrecords: false,
				loadComplete: function(){ 
				var ids = jQuery("#list_pjm").getDataIDs(); 
				var id = jQuery("#list_pjm").getGridParam('selrow'); 
				var rets = jQuery("#list_pjm").getRowData(id); 
			
				for(var i=0;i<ids.length;i++)
					{ 
						var cl = ids[i]; 
						
				    	be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick='update_pjm("+ ids[i] +")' />"; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick='hapus_pjm("+ ids[i] +")' />"; 
						jQuery("#list_pjm").setRowData(ids[i],{action:be+ce}) 
					}
										
				}, 
				onSelectRow: function(){
                    var id = jQuery("#list_pjm").getGridParam('selrow'); 
                }
            });
            jGrid_pjm.navGrid('#pager_pjm',{edit:false,add:false,del:false, search: false, refresh: true});
        }
        jQuery("#list_pjm").ready(loadView_pjm);
		
		$(function() {
			$("#pjm").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 345,
				width: 685,
				modal: true,
				resizable:false,
				title:"daftar peminjaman",				
			}); 
		});
		
		 function update_pjm(id) {
			
		    var postdata = {}; 
			var gc = document.getElementById("GANG_CODE").value;
			var tdate = document.getElementById("LHM_DATE").value;
			var tgl = tdate.replace(/-/gi, "");
			var ids = jQuery("#list_pjm").getGridParam('selrow'); 
			
		    var data = $("#list_pjm").getRowData(ids) ; 
		       
		    postdata['GANG_CODE'] = data.GANG_CODE; 
		    postdata['NIK'] = data.NIK; 
		    postdata['BDATE'] = data.BDATE;
		    postdata['FROM'] = data.FROM; 
		    postdata['TO'] = data.TO; 
		 	postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			
			$.post( url+'m_gang_activity_detail/update_pjm/'+id, postdata,function(message,status) { 
		        if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						alert('data berhasil diubah.')
						jQuery("#list_pjm").setGridParam({url:url+'m_gang_activity_detail/read_pinjaman/'+tgl+'/'+gc}).trigger("reloadGrid"); 
		           }; 
		      } ); 
		}
				
		function hapus_pjm(id) {
			var postdata = {};
			var gc = document.getElementById("GANG_CODE").value;
			var tdate = document.getElementById("LHM_DATE").value;
			var tgl = tdate.replace(/-/gi, "");
			var ids = jQuery("#list_pjm").getGridParam('selrow'); 
			
		    var data = $("#list_pjm").getRowData(ids) ; 
			
			postdata['GANG_CODE'] = data.GANG_CODE; 
		    postdata['NIK'] = data.NIK; 
		    postdata['BDATE'] = data.BDATE;
			postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			 			 
		    $.post( url+'m_gang_activity_detail/delete_pjm/'+id, postdata,function(message,status) { 
		       if(status !== 'success') { 
		            	alert('data gagal terhapus.'); 
		          } else { 
						alert('data berhasil terhapus.')
						jQuery("#list_pjm").setGridParam({url:url+'m_gang_activity_detail/read_pinjaman/'+tgl+'/'+gc}).trigger("reloadGrid"); 
		        
						
		           };  
		      } ); 
		}