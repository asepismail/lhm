<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';

jQuery(document).ready(function(){
    $('#form_progress').hide();
    $('#forms_lhm').show();
	
	$("#submitdata").hide(); $("#updatedata").hide();
	$("#bmaterial").hide(); $("#copy").hide();
	$("#bprogress").hide(); $("#delete").hide();    
	$("#add").hide(); $("#status_entri").hide(); 
	
	CheckAfdeling();
	var userty = '<?php echo $user_level; ?>' ; 
	/* dialog progress */	
	$("#progressbar").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 160,
                width: 220, modal: true
	}); 
	/* end dialog */
	
	/* dialog form grid material */
	$("#formg_material").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 480,
                width: 720, modal: true, title : 'Entry Data Pemakaian Material LHM'
	}); 
	/* end form grid material */
	
	/* dialog input material */
	$("#formi_material").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 250,
                width: 450, modal: true, title : 'Entry Data Pemakaian Material LHM', 
				buttons: {
					Tutup: function()  {	initmaterial();  $("#formi_material").dialog('close'); },
					Simpan: function() {	submitmaterial();	}     
				}
	}); 
	/* end dialog input material */
	
	$(function () {
        $("#GC_FROM").autocomplete( 
            url+"m_gang_activity_detail/gangc/", {
            dataType: 'ajax', width:350, multiple: false, limit:20,
            parse: function(data) { // parsing json input
                      return $.map(eval(data), function(row) {
                      return (typeof(row) == 'object')
                      ? { data: row, value: row.res_id, result: row.res_id } : { data: row, value: '',result: ''};
                    });
            }, formatItem: function(item) { return (typeof(item) == 'object')?item.res_dl :''; }
        })
    });
    
	/* entry kode mandor 1 */
    $(function () {
	    $("#NIK_MANDOR1").autocomplete( 
	         url+"m_gang_activity_detail/search_nik/", {
	         dataType: 'ajax', width:350, multiple: false, limit:20,
	         parse: function(data) { // parsing json input
	                  return $.map(eval(data), function(row) {
	                  return (typeof(row) == 'object')
	                  ? { data: row, value: row.res_id, result: row.res_id } : { data: row, value: '',result: ''};
	                 });
	             }, formatItem: function(item) { return (typeof(item) == 'object')?item.res_dl :''; }
	         }).result(function(e, item) { $("#NM_MANDOR1").val(item.res_name); });
    });
	/* end entry kode mandor 1 */
	
	/* entry kode mandor */
    $(function () {
	     $("#NIK_MANDOR").autocomplete( 
	         url+"m_gang_activity_detail/search_nik/", {
	         dataType: 'ajax', width:350, multiple: false, limit:20,
	         parse: function(data) { // parsing json input
	                 return $.map(eval(data), function(row) {
	                 return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_id, result: row.res_id } : { data: row, value: '',result: ''};
	                    });
	                },formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_dl :'';
	               }
			}).result(function(e, item) { $("#NM_MANDOR").val(item.res_name); });
    });
	/* end entry kode mandor */
	
	/* entry kode mandor */
    $(function () {
	            $("#KD_KERANI")
	              .autocomplete( 
	                url+"m_gang_activity_detail/search_nik/", {
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
	              ).result(function(e, item) {
	                $("#NM_KERANI").val(item.res_name);
			 	});
         });
	/* end entry kode mandor */

    $(function () {
        $("#GC_TO").autocomplete(url+"m_gang_activity_detail/gangc/", {
             dataType: 'ajax', width:350, multiple: false,
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
    
    $(function() { $("#LHM_DATE").datepicker({dateFormat:"yy-mm-dd"}); });
	
    /* if ( userty == 'RVW' ) {
        $("#submitdata").hide();
        $("#updatedata").hide();
        $("#bprogress").show();
        $("#delete").hide();    
        $("#add").hide();
        $("#status_entri").hide();
	} else {
		$("#updatedata").hide();
    	$("#bprogress").show();
    	$("#delete").hide();
    	$("#status_entri").hide();
    	$("#add").show();
	} */
	
    post_pinjam();
    $.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
    var maintab =jQuery('#tabs','#RightPane').tabs({
        add: function(e, ui) {
            $(ui.tab).parents('li:first')
                .append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
                .find('span.ui-tabs-close')
                .click(function() {
                    maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
                });
            maintab.tabs('select', '#' + ui.panel.id);
        }
    });
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

/* close progress bar */
function closewin(){
		$("#progressbar").dialog('close');
}
/* end close progress bar */
	       
function CheckAfdeling() {
    var jumlah = {}; var closing = {}; var postdata = {}; 
    
    postdata['GANG_CODE'] = $("#GANG_CODE").val();
        //check company-------------        
    var gc = document.getElementById("GANG_CODE").value;
    var tdate = document.getElementById("LHM_DATE").value;
    var tgl = tdate.replace(/-/gi, "");
    var usertype = '<?php echo $user_level; ?>' ; 
    var username = '<?php echo $login_id; ?>' ;
        					
    $.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_gad/'+tgl+'/'+gc, postdata, function(data){
           for (var i = 0; i < data.length ; i++ ){
             var d = data.split('~');
             jumlah = d[0];
             closing = d[1];
           }
                
        $.post('<?= base_url()?>index.php/m_gang_activity_detail/cek_mandor/'+gc+'/'+tgl, postdata, function(data){
            for (var i = 0; i < data.length ; i++ ){
                document.getElementById("AFD").value = data[i].name;
				document.getElementById("NIK_MANDOR1").value = data[i].mandore1;
                document.getElementById("NM_MANDOR1").value = data[i].nm_m1;
                document.getElementById("NIK_MANDOR").value = data[i].mandore;
                document.getElementById("NM_MANDOR").value = data[i].nm_m;
                document.getElementById("KD_KERANI").value = data[i].kerani;
                document.getElementById("NM_KERANI").value = data[i].nm_k;
                
                if (jumlah != 0){ /* cek jumlah */
					if (closing != 0) { /* cek closing */
						if ( usertype == 'SAD' || usertype == 'SAS' || usertype == 'KOREKSI') { 
							$("#submitdata").hide(); $("#updatedata").show();
							$("#bmaterial").show(); $("#copy").show();
							$("#bprogress").show(); $("#delete").show();
							$("#add").show(); $("#status_entri").hide();
						} else {
							$("#submitdata").hide(); $("#updatedata").hide();
							$("#bmaterial").hide(); $("#copy").hide();
							$("#bprogress").hide(); $("#delete").hide();    
							$("#add").hide(); $("#status_entri").show(); 
						}
					} else {
					 	if ( usertype == 'RVW' ) {
						   $("#submitdata").hide(); $("#updatedata").hide();
						   $("#bprogress").show(); $("#delete").hide();    
						   $("#add").hide(); $("#status_entri").hide();
						} else {
							$("#submitdata").hide(); $("#updatedata").show();
							$("#bmaterial").show(); $("#copy").show();
							$("#bprogress").show(); $("#delete").show();
							$("#add").show(); $("#status_entri").hide();
						}
					}
                jQuery("#list_lhm").setGridParam({url:url+"m_gang_activity_detail/read_exist_gad/"+tgl+"/"+gc}).trigger("reloadGrid");        
               	var pnn = gc.substring(0,2);
                $('#add_progress').hide();
                jQuery("#list_progress").setGridParam({url:url+'m_gang_activity_detail/read_progress_curr/'+tgl+'/'+gc}).trigger("reloadGrid"); 

                } else {
					if (closing != 0){
						$("#submitdata").hide(); $("#updatedata").hide();
						$("#bmaterial").hide(); $("#copy").hide();
                        $("#bprogress").hide(); $("#delete").hide();    
                        $("#add").hide(); $("#status_entri").show();
					} else {
						if ( usertype == 'RVW' ) {
						   $("#submitdata").hide(); $("#updatedata").hide();
						   $("#bmaterial").hide(); $("#copy").hide();
						   $("#bprogress").show(); $("#delete").hide();    
						   $("#add").hide(); $("#status_entri").hide();
						} else { 
							$("#status_entri").hide(); $("#submitdata").show();
							$("#bmaterial").show(); $("#copy").show();
							$("#updatedata").hide(); $("#bprogress").show();
							$("#delete").hide(); $("#add").show();   
						}
					}
					
					jQuery("#list_lhm").setGridParam({url:url+"m_gang_activity_detail/cek_anggota_pinjam/"+tgl+"/"+gc}).trigger("reloadGrid");    
					var pnn = gc.substring(0,2);
					$('#add_progress').hide(); 
					jQuery("#list_progress").setGridParam({url:url+'m_gang_activity_detail/read_progress_curr/'+tgl+'/'+gc}).trigger("reloadGrid"); 
					
                } 
                    
                }                        
              }, "json"
        	); //end post
     	});
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
        
        function giveLocCode(){        
            var ids = jQuery("#list_lhm").getGridParam('selrow'); 
            var rets = jQuery("#list_lhm").getRowData(ids); 
            var type = rets.LOCATION_CODE;
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
                          matchContains: true,
                        highlightItem: false,
                          parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },
                      formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_name :'';
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
        colModelT_lhm.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true ,async: false,edittype: "text",
			editoptions:{
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
                      parse: function(data) { 
					  	  return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },
                      formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl.replace(/[\r\n]+/g, "") :'';
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
                    url+"m_gang_activity_detail/activity/"+giveLocType()+"/"+giveLocCode(), {
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
					  
					  if(item.res_id == "8601003"){
						  var id = jQuery("#list_lhm").getGridParam('selrow');
                    	  var ret = jQuery("#list_lhm").getRowData(id);
						  var company = '<?=$this->session->userdata('DCOMPANY'); ?>'
						  if( company != "SCK"){
						  		var sa=jQuery("#list_lhm").setRowData(id,{HSL_KERJA_UNIT:'JANJANG'});
						  }
					  }
                   	 	//$("#LOCATION_TYPE_CODE").val(item.res_name );
						
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
                        return (typeof(item) == 'object')?item.res_id :'';
                      }
                    }
                  )
                  .result(function(e, item) {
                    $("#HSL_KERJA_UNIT").val(item.res_id );
                  });
          }}, width: 70, align:'center'});
       colNamesT_lhm.push('Hsl Kerja');
       colModelT_lhm.push({name:'HSL_KERJA_VOLUME',index:'HSL_KERJA_VOLUME', editrules:{number:true}, editable: true, width: 70, align:'center'});

       colNamesT_lhm.push('Tarif/Satuan');
       colModelT_lhm.push({name:'TARIF_SATUAN',index:'TARIF_SATUAN', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, editable: true, width: 90, align:'right'});
        
       colNamesT_lhm.push('Premi');
       colModelT_lhm.push({name:'PREMI',index:'PREMI',editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, editable: true, width: 70, align:'right'});
                
       colNamesT_lhm.push('Jam Lembur');
       colModelT_lhm.push({name:'LEMBUR_JAM',index:'LEMBUR_JAM',editrules:{number:true, maxValue:14}, editable: true, width: 80, align:'center'});
    
       colNamesT_lhm.push('Penalti');
       colModelT_lhm.push({name:'PENALTI',index:'PENALTI', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, editable: true, width: 70, align:'right'});
		
       colNamesT_lhm.push('company');
       colModelT_lhm.push({name:'COMPANY_CODE',index:'COMPANY_CODE', hidden:true, editable: false, width: 100, align:'left'});
		
	   colNamesT_lhm.push('Kontanan');
       colModelT_lhm.push({name:'KONTANAN',index:'KONTANAN', hidden:false, editable: true, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : false}, width: 60, align:'center'});
       
		   colNamesT_lhm.push('Catatan');
		   colModelT_lhm.push({name:'NOTE',index:'NOTE', 
							  <?php if($this->session->userdata('DCOMPANY') == 'TPAI' || $this->session->userdata('DCOMPANY') == 'SCK') {
								  echo "hidden:false"; } else { echo "hidden:true"; } ?>
							 , editable: true, width: 100, align:'left'});
	   
	   var lastsel; var jdesc1;
       var lRow; var lCol; var i = 0;
       
	   //var tdate = document.getElementById("LHM_DATE").value;
       //var tgl = tdate.replace(/-/gi, ""); 
       var loadView_lhm = function()
        {
            jGrid_lhm = jQuery("#list_lhm").jqGrid(
            {
                url:url+'m_gang_activity_detail/read_exist_gad/xx/xx',
                mtype : "POST", datatype: "json",
                colNames: colNamesT_lhm , colModel: colModelT_lhm , sortname: colModelT_lhm[2].name,
                rownumbers: true,  rowNum: 400, height: 320,
                imgpath: gridimgpath,  pager:jQuery("#pager_lhm"), sortorder: "asc",
                cellEdit: true, cellsubmit: 'clientArray', forceFit : true,
                onCellSelect : function(iCol){
                    var id = jQuery("#list_lhm").getGridParam('selrow');
                    var ret = jQuery("#list_lhm").getRowData(id);
                    
                    if (ret.TYPE_ABSENSI == "KJO")    {
                        
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
                        if( ret.NIK == "" ) { alert("NIK karyawan tidak boleh kosong."); }     
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
			
			jGrid_lhm.navButtonAdd('#pager_lhm',{
               caption:"Cetak PDF", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
                var tdate = document.getElementById("LHM_DATE").value;
                var gc = document.getElementById("GANG_CODE").value;
                var tgl = tdate.replace(/-/gi, "");
                window.location = url+'m_gang_activity_detail/cetak_pdf/'+ tgl + '/' + gc;
               }, 
                position:"left",
            });

            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_lhm").ready(loadView_lhm);
        
                        
        function post() {
           var postdata = {}; 
            postdata['LHM_DATE'] = $("#LHM_DATE").val() ; 
            postdata['GANG_CODE'] = $("#GANG_CODE").val() ; 
			postdata['MANDORE1_CODE'] = $("#NIK_MANDOR1").val(); 
            postdata['MANDORE_CODE'] = $("#NIK_MANDOR").val(); 
            postdata['KERANI_CODE'] = $("#KD_KERANI").val(); 
            postdata['STATUS_POST'] = "insert";
            // Data dari grid
            
	     /* validasi tanggal */
	     var tdate = new Date();
	     var dd = tdate.getDate(); //yields day
	     var MM = tdate.getMonth(); //yields month
	     var yyyy = tdate.getFullYear(); //yields year
	     var tglCurrent = yyyy + "-" + ('0'+(MM+1)).slice(-2) + "-" + dd;
           
	     if( $("#LHM_DATE").val() > tglCurrent){
		   alert("Tanggal input LHM tidak boleh lebih dari tanggal transaksi berjalan");
	     } else {
	     /* submit transaksi */

	     i=0;
            s = $("#list_lhm").getDataIDs(); 
            postdata['jumlah'] = s;
        
               $.each(s, function(n, rowid) { 
                var data = $("#list_lhm").getRowData(rowid) ; 
				alert(data);
                i=i+1;
                postdata['ATT_DATE'+i] = $("#LHM_DATE").val() ; 
               	postdata['GANG_CODE'+i] = $("#GANG_CODE").val() ;
                postdata['LHM_DATE'+i] = $("#LHM_DATE").val() ; 
                if(data.ID!== null){ postdata['ID'+i] = data.ID ; }
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
				postdata['KONTANAN'+i] = data.KONTANAN;
				postdata['NOTE'+i] = data.NOTE;
               }); 
        
           // Post it all
		  $("#cok").attr("disabled", true);
		  $("#load").show();
		  document.getElementById('msg').innerHTML= "Mohon menunggu..... penyimpanan transaksi sedang diproses...";
		  $("#progressbar").dialog('open');
			         
          	$.post( url+'m_gang_activity_detail/create_gad', postdata,function(status) 		  { 
              var status = new String(status);
                   	if(status.replace(/\s/g,"") != "") { 
                         	$("#load").hide();
				document.getElementById('msg').innerHTML= status;
				$("#cok").attr("disabled", false);
                  	} else { 
				$("#load").hide();
				$("#cok").attr("disabled", false);
				document.getElementById('msg').innerHTML= 'data tersimpan';
                       	$("#submitdata").hide();
                        	$("#updatedata").show();
                        	$("#bprogress").show();
                        	$("#delete").show();
                        	CheckAfdeling();        
                   	};  
             	});
	    }
	    /* end validasi tanggal */ 
        }
        
        /* function hapus(tgl, gc) {
        var postdata = {}; 
            $.post( url+'m_gang_activity_detail/delete/'+tgl+'/'+gc, postdata,function(message,status) { 
                if(status !== 'success') { 
                        alert('berhasil'); 
                  } else { 
                        var i=1;
                  }; 
              } );
        }*/        
        
        function update() {
            var postdata = {}; 
            // Data dari form
            postdata['LHM_DATE'] = $("#LHM_DATE").val() ; 
            postdata['GANG_CODE'] = $("#GANG_CODE").val() ; 
			postdata['MANDORE1_CODE'] = $("#NIK_MANDOR1").val();
            postdata['MANDORE_CODE'] = $("#NIK_MANDOR").val(); 
            postdata['KERANI_CODE'] = $("#KD_KERANI").val(); 
            postdata['jumlah'] = jQuery('#list_lhm').getGridParam('records');
            // Data dari grid
            
/* validasi tanggal */
		var tdate = new Date();
		var dd = tdate.getDate(); //yields day
		var MM = tdate.getMonth(); //yields month
		var yyyy = tdate.getFullYear(); //yields year
		var tglCurrent = yyyy + "-" + ('0'+(MM+1)).slice(-2) + "-" + dd;
           
		
	     i=0;
            s = $("#list_lhm").getDataIDs(); 
            $.each(s, function(n, rowid) { 
                var data = $("#list_lhm").getRowData(rowid) ; 
                i=i+1;
               	postdata['ATT_DATE'+i] = $("#LHM_DATE").val() ;         
               	postdata['GANG_CODE'+i] = $("#GANG_CODE").val() ;
                	postdata['LHM_DATE'+i] = $("#LHM_DATE").val() ; 
                	postdata['NIK'+i] = data.NIK ; 
                	postdata['ID'+i] = data.ID ;
                	var absen = data.TYPE_ABSENSI;
                	var location_type = data.LOCATION_TYPE_CODE
                	var location = data.LOCATION_CODE
                	var activity = data.ACTIVITY_CODE
                	var hk = data.HK_JUMLAH
                 
                	if (absen.lenght < 2 ){
                    		if (absen = " "){ absen.replace(" ", ""); }    
                	}
                
                	if (location_type.lenght < 2 ){
                    		if (location_type = " "){ location_type.replace(" ", ""); }
                	}
                
                	if (location.lenght < 2 ){
                    		if (location = " "){ location.replace(" ", ""); }
                	}
                
                	if (activity.lenght < 2 ){
                    		if (activity = " "){ activity.replace(" ", ""); }
                	}
                
                	if (hk.lenght < 2 ){
                    		if (hk = " "){ hk.replace(" ", ""); }
                	}
                
                	postdata['TYPE_ABSENSI'+i] = absen;
                	postdata['LOCATION_TYPE_CODE'+i] = location_type;
                	postdata['LOCATION_CODE'+i] = location; 
                	postdata['ACTIVITY_CODE'+i] = activity; 
                	postdata['HSL_KERJA_UNIT'+i] = data.HSL_KERJA_UNIT.replace(/\s/g,""); 
                	postdata['HSL_KERJA_VOLUME'+i] = data.HSL_KERJA_VOLUME; 
                	postdata['HK_JUMLAH'+i] = hk; 
                	postdata['LEMBUR_JAM'+i] = data.LEMBUR_JAM;  
                	postdata['TARIF_SATUAN'+i] = data.TARIF_SATUAN; 
                	postdata['PREMI'+i] = data.PREMI; 
                	postdata['PENALTI'+i] = data.PENALTI; 
                	postdata['COMPANY_CODE'+i] = '<?php echo $company_code; ?>'; 
		  	postdata['KONTANAN'+i] = data.KONTANAN;
		  	postdata['NOTE'+i] = data.NOTE;
            
            }); 
        	
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu..... penyimpanan transaksi sedang diproses...";
			$("#progressbar").dialog('open');
			
            		var gc = document.getElementById("GANG_CODE").value;
            		var tdate = document.getElementById("LHM_DATE").value;
            		var ccode = document.getElementById("ccode").value;
            		var tgl = tdate.replace(/-/gi, "");
			
           		$.post( url+'m_gang_activity_detail/edit_gad/'+tgl+'/'+gc, postdata,function(status) {
                	var status = new String(status);
                         if(status.replace(/\s/g,"") != "") { 
				$("#load").hide();
				document.getElementById('msg').innerHTML= status;
				$("#cok").attr("disabled", false);
                          } else { 
                          	$("#load").hide();
				$("#cok").attr("disabled", false);
				document.getElementById('msg').innerHTML= 'data tersimpan';
                            $("#submitdata").hide();
                            $("#updatedata").show();
                            $("#bprogress").show();
                            $("#delete").show();
                            CheckAfdeling();        
                          }; 
            		});                                 
        }
        
    	 //---------------------------------------------------------------- Modal Dialog
        function gangc() {
                $("#GANG_CODE").autocomplete( 
                    url+"m_gang_activity_detail/gangc/", {
                    dataType: 'ajax', width:350, multiple: false,
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
                    'simpan    ': function() {
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
                            $.post( url+'m_gang_activity_detail/insert_pinjaman', postdata2,function(message,status) { 
                                        if(status !== 'success') { 
                                            alert('data tidak tersimpan'); 
                                        } else { 
                                            alert('data berhasil tersimpan');
                                            $(this).dialog('close');    
                                            init_pinjam()                     
                                          }; 
                              } ); 
                            },
                                    'Tutup': function() {
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
                        $.get(url+'m_gang_activity_detail/cek_kmandoran_asal/'+ tgl + '/' + ret.NIK, function(data) {                            $("#GC_FROM").val(data);
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
            if (gc != ""){
                i=i+1;    
                var datArr = {};
                if (i>1){
                      var datArr = {GANG_CODE:jdesc1};
                }
				
                var su=jQuery("#list_lhm").addRowData(i,datArr,"last");
                var sa=jQuery("#list_lhm").setRowData(i,{no:i});
				var sa=jQuery("#list_lhm").setRowData(i,{KONTANAN:0});
            } else {
                alert('kode kemandoran tidak boleh kosong!');                            
       		}
        }
		
		function copyrow(){
            var gc = document.getElementById("GANG_CODE").value;
            var ids = jQuery("#list_lhm").getDataIDs();
            var i = ids.length;
			
			var ids = jQuery("#list_lhm").getGridParam('selrow'); 
            var data = $("#list_lhm").getRowData(ids) ;
			var nik = data.NIK; var nm_k = data.NM_K; var absen = data.TYPE_ABSENSI;
			var ltc = data.LOCATION_TYPE_CODE; var lc = data.LOCATION_CODE;
			var ac = data.ACTIVITY_CODE; var hsl = data.HSL_KERJA_UNIT;
			var vol = data.HSL_KERJA_VOLUME; var hkj = data.HK_JUMLAH;
			var lembur = data.LEMBUR_JAM; var tarif = data.TARIF_SATUAN;
			var premi = data.PREMI; var penalti = data.PENALTI;
			var company = data.COMPANY_CODE;	
				
            if (gc != ""){
                i=i+1;    
                var datArr = {};
                if (i>1){
                      var datArr = {GANG_CODE:jdesc1};
                }
				var status = "";
				var arr = [ nik, absen, ltc, lc, ac, hsl, vol, hkj, lembur, tarif, premi, penalti ];
				jQuery.each(arr, function() {
				  	if(this.length > 50){
						status = "mohon tutup kotak yang terbuka terlebih dahulu";
						alert(status)
					}
				});

				if(status == "") {
					var su=jQuery("#list_lhm").addRowData(i,datArr,"last");
					var sa=jQuery("#list_lhm").setRowData(i,{no:i});
					jQuery("#list_lhm").setRowData(i,{NIK:nik});
					jQuery("#list_lhm").setRowData(i,{NM_K:nm_k});
					jQuery("#list_lhm").setRowData(i,{TYPE_ABSENSI:absen});
					jQuery("#list_lhm").setRowData(i,{LOCATION_TYPE_CODE:ltc});
					jQuery("#list_lhm").setRowData(i,{LOCATION_CODE:lc});
					jQuery("#list_lhm").setRowData(i,{ACTIVITY_CODE:ac});
					jQuery("#list_lhm").setRowData(i,{HSL_KERJA_UNIT:hsl});
					jQuery("#list_lhm").setRowData(i,{HSL_KERJA_VOLUME:vol});
					jQuery("#list_lhm").setRowData(i,{HK_JUMLAH:hkj});
					jQuery("#list_lhm").setRowData(i,{LEMBUR_JAM:lembur});
					jQuery("#list_lhm").setRowData(i,{TARIF_SATUAN:tarif});
					jQuery("#list_lhm").setRowData(i,{PREMI:premi});
					jQuery("#list_lhm").setRowData(i,{PENALTI:penalti});
					jQuery("#list_lhm").setRowData(i,{COMPANY_CODE:company});
					jQuery("#list_lhm").setRowData(i,{KONTANAN:0});
					jQuery("#list_lhm").setRowData(i,{NOTE:''});
				}
			} else {
                alert('kode kemandoran tidak boleh kosong!');                            
       		}
        }
        
        function hapus_lhm(){
            
            var postdata = {}; 
            var tdate = document.getElementById("LHM_DATE").value;
            var tgl = tdate.replace(/-/gi, "");
            var ids = jQuery("#list_lhm").getGridParam('selrow'); 
            var data = $("#list_lhm").getRowData(ids) ; 
            var gc = document.getElementById("GANG_CODE").value;
            postdata['GANG_CODE'] = gc;
            postdata['ID'] = data.ID; 
            postdata['NIK'] = data.NIK;
            postdata['TGL'] = tgl;
            if(data.NIK == undefined){
                alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
            } else {
                var answer = confirm ("Hapus Data Dari NIK : " + data.NIK + "?" )
                if (answer){
                   $.post( url+'m_gang_activity_detail/delete_currlhm', postdata,function(status) { 
				   		var status = new String(status);
                   		if( status.replace(/\s/g,"") != "" ) { 
                            alert(status); 
                   		} else { 
                            alert('data berhasil terhapus.')
                            jQuery("#list_lhm").setGridParam({url:url+'m_gang_activity_detail/read_exist_gad/'+tgl+'/'+gc}).trigger("reloadGrid"); 
                       	};  
                   });
                }    
            }    
        }
        /* pinjaman grid */
        
        var jGrid_pjm = null;
        var colNamesT_pjm = new Array();
        var colModelT_pjm = new Array();

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
                url:url+'m_gang_activity_detail/read_exist_gad/xx/xx',
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
                height: 360,
                width: 690,
                position: ['top','left'],
                modal: true,
                resizable:true,
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
        
		/* ### Material ### */
		function smaterial() {
            var tdate = document.getElementById("LHM_DATE").value;
            var gc = document.getElementById("GANG_CODE").value;
            var pnn = gc.substring(0,2);
            var tgl = tdate.replace(/-/gi, "");
			
			if(gc == "" || tdate == "") {
				alert('silakan pilih kode kemandoran dan tanggal terlebih dahulu..');
			} else {
				reload_material();
				$('#formg_material').dialog('open');
			}
        }
		/* ### End Material ### */
		
        /* ####################### progress ######################## */
        function sprogress() {
            var tdate = document.getElementById("LHM_DATE").value;
            var gc = document.getElementById("GANG_CODE").value;
            var pnn = gc.substring(0,2);
            var tgl = tdate.replace(/-/gi, "");
            $('#form_progress').show();
           // if( pnn == "PN") { $('#add_progress').show(); } else { $('#add_progress').hide(); }
           $('#add_progress').hide(); 
            jQuery("#list_progress").setGridParam({url:url+'m_gang_activity_detail/read_progress_curr/'+tgl+'/'+gc}).trigger("reloadGrid"); 
            $('#forms_lhm').hide();
        }
        
        function kembali(){
            $('#form_progress').hide();
            $("#forms_lhm").show();
        }
        
        var jGrid_progress = null;
        var colNamesT_progress = new Array();
        var colModelT_progress = new Array();    

// ##################### PROGRES GRID ###########################                                                                         
colNamesT_progress.push('Kemandoran');
colModelT_progress.push({name:'IDP',index:'IDP', editable: false, hidden:true, width: 90, align:'center'});
 
colNamesT_progress.push('Nama Kemandoran');
colModelT_progress.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:true, width: 200, align:'left'});
 
colNamesT_progress.push('Tanggal');
colModelT_progress.push({name:'LHM_DATE',index:'LHM_DATE', editable: false, hidden:false, width: 90, align:'center'});

colNamesT_progress.push('Kode');
colModelT_progress.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, hidden:false,
       editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { // the moment of magic Wink
                $(elem)
                  .autocomplete( // for more info check the autocomplete plugin docs
                    url+"m_gang_activity_detail/activity_pn/", {
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
                    $("#ACTIVITY_CODE").val(item.res_name );
                    //$("#COA_DESCRIPTION").val(item.res_name );
                    var id = jQuery("#list_progress").getGridParam('selrow');
                    if (id) 
                    { 
                        var ret = jQuery("#list_progress").getRowData(id);
                        ret.COA_DESCRIPTION = (item.res_name);
                        jQuery("#list_progress").setRowData(id,{COA_DESCRIPTION:ret.COA_DESCRIPTION});
                    }
                  });
                  }}, width: 70, align:'center'});

colNamesT_progress.push('Deskripsi');
colModelT_progress.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false, hidden:false, width: 350, align:'left'});

colNamesT_progress.push('Lokasi');
colModelT_progress.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false
        ,async: false,edittype: "text",editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                $(elem)
                  .autocomplete( 
                    url+"m_gang_activity_detail/location/OP", {
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
                    $("#LOCATION_CODE").val(item.res_name );
                  });
          }}, width: 140, align:'center'});
 
colNamesT_progress.push('Nilai');
colModelT_progress.push({name:'NILAI',index:'NILAI', editable: true, editrules:{number:true}, hidden:false, width: 80, align:'center'});    
colNamesT_progress.push('Unit');
colModelT_progress.push({name:'UNIT1',index:'UNIT1', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_progress.push('Nilai 2');
colModelT_progress.push({name:'NILAI2',index:'NILAI2', editable: true, editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2},hidden:false, width: 80, align:'center'});    
colNamesT_progress.push('Unit 2');
colModelT_progress.push({name:'UNIT2',index:'UNIT2', editable: false, hidden:false, editoptions:{
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
                        return (typeof(item) == 'object')?item.res_id :'';
                      }
                    }
                  )
                  .result(function(e, item) {
                    $("#UNIT2").val(item.res_id );
                  });
          }}, width: 60, align:'center'});

colNamesT_progress.push('HK');
colModelT_progress.push({name:'HK',index:'HK', editable: false, hidden:false, width: 80, align:'center'});    

colNamesT_progress.push('Realisasi');
colModelT_progress.push({name:'REALISASI',index:'REALISASI', editable: false, hidden:false, width: 80, align:'right'});    
    
    
    var loadView_progress = function()
    {
       var gc = jQuery("#search_gc").val(); 
       if (gc == ""){ gc = "-"; }
       jGrid_progress = jQuery("#list_progress").jqGrid(
       {
          url:url+'m_gang_activity_detail/read_progress_curr/xx/xx',
          mtype : "POST",  datatype: "json",  colNames: colNamesT_lhm ,
          colModel: colModelT_lhm , sortname: colNamesT_lhm[2],
          rownumbers: true, rowNum: 400, height: 280,
          imgpath: gridimgpath, pager:jQuery("#pager_lhm"),
          sortorder: "asc", cellEdit: true, cellsubmit: 'clientArray',
          forceFit : true,  colNames: colNamesT_progress, colModel: colModelT_progress ,
          rownumbers:true,  viewrecords: true, multiselect: false, 
          caption: "Data Progress <?php echo $company_dest;?>", 
          rowList:[10,20,30], multiple:true,
	   onCellSelect: function() {
			 		var ids = jQuery("#list_progress").getDataIDs(); 
					var id = jQuery("#list_progress").getGridParam('selrow'); 
					var rets = jQuery("#list_progress").getRowData(id); 
					var act = "";
					if(rets.ACTIVITY_CODE.length > 7){
						act = getCellValue(rets.no, 'ACTIVITY_CODE');
					} else {
						act = rets.ACTIVITY_CODE
					}
					
					if(act == "8601003"){
						disableGridCell('LOCATION_CODE');
						disableGridCell('ACTIVITY_CODE');
						disableGridCell('NILAI');
						disableGridCell('NILAI2');
						disableGridCell('UNIT2');
					} 
			  	},
          loadComplete: function(){ }, 
          imgpath: gridimgpath, pager: jQuery('#pager_progress'), sortname: 'GANG_CODE'
        });
        jGrid_progress.navGrid('#pager_progress',{edit:false,add:false,del:false, search: false, refresh: true});
    }
        
     jQuery("#list_progress").ready(loadView_progress);
 // ##################### END PROGRES GRID ########################### 
                  
        function subm_progress() {
            
            var postdata = {}; 
            postdata['TGL_PROGRESS'] = $("#LHM_DATE").val() ; 
            postdata['GANG_CODE'] = $("#GANG_CODE").val() ;  
            // Data dari grid
            i=0;
            s = $("#list_progress").getDataIDs(); 
            postdata['jumlah'] = s;
            postdata['jumlahdt'] = jQuery('#list_progress').getGridParam('records');
        
               $.each(s, function(n, rowid) { 
                var data = $("#list_progress").getRowData(rowid) ; 
                i=i+1;
                postdata['IDP'+i] = data.IDP ;
                postdata['PLOCATION_CODE'+i] = data.LOCATION_CODE ; 
                postdata['PACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
                postdata['PHASIL_KERJA'+i] = data.NILAI; 
                postdata['PSATUAN'+i] = data.UNIT1;
		  /* update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */
		  postdata['PHASIL_KERJA2'+i] = data.NILAI2; 
                postdata['PSATUAN2'+i] = data.UNIT2;
		  /* end update tambah hasil kerja dan satuan 2 untuk panen #ridhu : 2013-06-13 */ 
                postdata['HK'+i] = data.HK;
                postdata['REALISASI'+i] = data.REALISASI;
            
               }); 
        
           // Post it all        
          $.post( url+'m_gang_activity_detail/submit_progress', postdata,function(status) { 
                   //alert(status)
                var status = new String(status);
                  if(status.replace(/\s/g,"") != "") { 
                         alert(status); 
                  } else { 
                          alert('data tersimpan');
                        sprogress();        
                   };  
             } ); 
        }
        
        //hapus progress
        function hapus_progress(){
            var postdata = {}; 
            var tdate = document.getElementById("LHM_DATE").value;
            var tgl = tdate.replace(/-/gi, "");
            var ids = jQuery("#list_progress").getGridParam('selrow'); 
            var data = $("#list_progress").getRowData(ids) ; 
            var gc = document.getElementById("GANG_CODE").value;
            postdata['GANG_CODE'] = gc;
            postdata['TGL'] = tdate;
            postdata['IDP'] = data.IDP; 
            postdata['ACT'] = data.ACTIVITY_CODE;
            postdata['LC'] = data.LOCATION_CODE;
            
            if(data.IDP == undefined){
                alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
            } else {
                var answer = confirm ("Hapus Data Dari progress : " + data.COA_DESCRIPTION + " : " + data.LOCATION_CODE + ", tgl :" + tdate + " ?" )    
                if (answer)
                {
                   $.post( url+'m_gang_activity_detail/delete_progress', postdata,function(status) { 
                   		if(status.replace(/\s/g,"") != "") { 
                         	alert(status); 
                  		} else {  
                            alert('data berhasil terhapus.')
                            jQuery("#list_progress").setGridParam({url:url+'m_gang_activity_detail/read_progress_curr/'+tgl+'/'+gc}).trigger("reloadGrid"); 
                            
                       };  
                  } );
    
                }    
            }    
        }
        
        
        //tambah progress
        function addrowp(){
             var gc = document.getElementById("GANG_CODE").value;
             var tgl = document.getElementById("LHM_DATE").value;
             var ids = jQuery("#list_progress").getDataIDs();
             var i = ids.length;
             if (gc != ""){
                 i=i+1;    
                 var datArr = {};
                 if (i>1){  var datArr = {GANG_CODE:jdesc1}; }
                 sv = tgl;
                 var su=jQuery("#list_progress").addRowData(i,datArr,"last");
                 var sa=jQuery("#list_progress").setRowData(i,{no:i});
                 jQuery("#list_progress").setRowData(i,{LHM_DATE:sv})
                 jQuery("#list_progress").setRowData(i,{UNIT1:"Kg"})
                 jQuery("#list_progress").jqGrid('editRow',i,true);
             } else { alert('kode kemandoran tidak boleh kosong!'); }
        }
        
		/* ##################### MATERIAL GRID ######################### */ 
	var jGrid_material = null;
    var colNamesT_material = new Array();
    var colModelT_material = new Array();    
                                                                     
	colNamesT_material.push('LHM_MATERIAL_ID');
	colModelT_material.push({name:'LHM_MATERIAL_ID',index:'LHM_MATERIAL_ID',editable: 
								false,hidden:true,width:90,align:'center'});
	 
	colNamesT_material.push('GANG_CODE');
	colModelT_material.push({name:'GANG_CODE',index:'GANG_CODE',editable: false,hidden:true,width: 200,align:'left'});
	 
	colNamesT_material.push('LHM_DATE');
	colModelT_material.push({name:'LHM_DATE',index:'LHM_DATE',editable:false,hidden:true,width:90,align:'center'});
	
	colNamesT_material.push('Kode Material');
	colModelT_material.push({name:'MATERIAL_CODE',index:'MATERIAL_CODE',editable:false,
								hidden:false,width:90,align:'center'});
	
	colNamesT_material.push('Deskripsi');
	colModelT_material.push({name:'MATERIAL_NAME',index:'MATERIAL_NAME',editable:false,hidden:false,
								width:180,align:'left'});
	
	colNamesT_material.push('Kuantitas');
	colModelT_material.push({name:'MATERIAL_QTY',index:'MATERIAL_QTY', editable: false, editrules:{number:true}, 
								hidden:false, width: 80, align:'center'}); 
	
	colNamesT_material.push('Satuan');
	colModelT_material.push({name:'MATERIAL_UOM',index:'MATERIAL_UOM', editable:false, 
								hidden:false, width: 80, align:'center'});
			
	colNamesT_material.push('Lokasi');
	colModelT_material.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, width: 140, align:'center'});
	
	colNamesT_material.push('Kode Aktivitas');
	colModelT_material.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, 
								hidden:false, width: 100, align:'center'});
								
	colNamesT_material.push('No SKB');
	colModelT_material.push({name:'MATERIAL_BPB_NO',index:'MATERIAL_BPB_NO', editable: false, 
								hidden:true, width: 100, align:'center'});	
	
	colNamesT_material.push('COMPANY_CODE');
	colModelT_material.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, 
								hidden:true, width: 80, align:'right'});    
		
		
	var loadView_material = function(){
	var gc = jQuery("#search_gc").val(); 
	if (gc == ""){ gc = "-"; }
	jGrid_material = jQuery("#list_material").jqGrid({
		url:url+'m_gang_activity_detail/read_material/xx/xx',
		mtype : "POST",  datatype: "json", rownumbers: true, rowNum: 40, height: 320,
		imgpath: gridimgpath, pager:jQuery("#pager_lhm"), sortorder: "asc", rownumbers:true,  viewrecords: true,
		forceFit : true,  colNames: colNamesT_material, colModel: colModelT_material , sortname: colModelT_material[2].name,
		rowList:[10,20,30], multiple:false,
		loadComplete: function(){ }, 
		imgpath: gridimgpath, pager: jQuery('#pager_material'), sortname: 'GANG_CODE'});
		jGrid_material.navGrid('#pager_material',{edit:false,add:false,del:false, search: false, refresh: true});
	}
	jQuery("#list_material").ready(loadView_material);

 	// ##################### END MATERIAL GRID ########################### 	
	function reload_material(){
		 var gc = document.getElementById("GANG_CODE").value;
         var tdate = document.getElementById("LHM_DATE").value;
         var tgl = tdate.replace(/-/gi, "");
		 jQuery("#list_material").setGridParam({url:url+"m_gang_activity_detail/read_material/"+tgl+"/"+gc}).trigger("reloadGrid");
	}
		 
	function getActMaterial(){
		var gc = document.getElementById("GANG_CODE").value;
		var tdate = document.getElementById("LHM_DATE").value;
		var tgl = tdate.replace(/-/gi, "");
		$("#MAT_ACTIVITY").autocomplete( url+"m_gang_activity_detail/getActMaterial/"+gc+"/"+tgl, {  dataType: 'ajax',
			width:350, multiple: false,  limit:20,
			parse: function(data) { // parsing json input
				  return $.map(eval(data), function(row) {
				  return (typeof(row) == 'object')
					  ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
					  : { data: row, value: '',result: ''};
				  });
				}, formatItem: function(item) {
				   return (typeof(item) == 'object')?item.res_dl :'';
				}
			 }).result(function(e, item) {
				//$("#i_actdesc").val(item.res_name );
		 });
	};

	function getActActivity(){
		var gc = document.getElementById("GANG_CODE").value;
		var act = document.getElementById("MAT_ACTIVITY").value;
		var tdate = document.getElementById("LHM_DATE").value;
		var tgl = tdate.replace(/-/gi, "");
		$("#MAT_LOCATION").autocomplete( url+"m_gang_activity_detail/getLocMaterial/"+gc+"/"+tgl+"/"+act, {  dataType: 'ajax',
			width:350, multiple: false,  limit:20,
			parse: function(data) { // parsing json input
				  return $.map(eval(data), function(row) {
				  return (typeof(row) == 'object')
					  ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
					  : { data: row, value: '',result: ''};
				  });
				}, formatItem: function(item) {
				   return (typeof(item) == 'object')?item.res_dl :'';
				}
			 }).result(function(e, item) {
				//$("#i_actdesc").val(item.res_name );
		 });
	};
	
	function getMaterial(){
		
		$("#MAT_MATERIAL").autocomplete( url+"m_gang_activity_detail/getMaterial/", {  dataType: 'ajax',
			width:350, multiple: false,  limit:20,
			parse: function(data) { // parsing json input
				  $("#MAT_DMATERIAL").val('');
				  $("#MAT_UOM").val('');
				  return $.map(eval(data), function(row) {
				  return (typeof(row) == 'object')
					  ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
					  : { data: row, value: '',result: ''};
				  });
				}, formatItem: function(item) {
				   return (typeof(item) == 'object')?item.res_dl :'';
				}
			 }).result(function(e, item) {
				$("#MAT_DMATERIAL").val(item.res_name );
				$("#MAT_UOM").val(item.uom );
		 });
	};
	
	function addmaterial(){
		initmaterial();
		$("#form_mode").val("POST");
		var gc = document.getElementById("GANG_CODE").value;
        if (gc != ""){
            $('#formi_material').dialog('open');
        } else {
            alert('kode kemandoran tidak boleh kosong!');                            
       	}
	}
	
	function editmaterial(){
		var ids = jQuery("#list_material").getGridParam('selrow'); 
		var data = $("#list_material").getRowData(ids) ; 
		if (null==ids || ''==ids) {
			alert ("harap pilih data!!!");
		} else {
			initmaterial();
			$("#form_mode").val("GET");
			var gc = document.getElementById("GANG_CODE").value;
			if (gc != ""){
				$("#LHM_MATERIAL_ID").val(data.LHM_MATERIAL_ID); 
				$("#MAT_ACTIVITY").val(data.ACTIVITY_CODE); 
				$("#MAT_LOCATION").val(data.LOCATION_CODE); 
				$("#MAT_QTY").val(data.MATERIAL_QUANTITY); 
				$("#MAT_MATERIAL").val(data.MATERIAL_CODE);
				$("#MAT_DMATERIAL").val(data.MATERIAL_NAME); 
				$("#MAT_QTY").val(data.MATERIAL_QTY);
				$("#MAT_UOM").val(data.MATERIAL_UOM);
				$("#MAT_BPB").val(data.MATERIAL_BPB_NO); 
				$('#formi_material').dialog('open');
			} else {
				alert('kode kemandoran tidak boleh kosong!');                            
			}
		}
	}
	
	function submitmaterial(){
		var postdata={};
		var ids = jQuery("#list_material").getGridParam('selrow'); 
		var data = $("#list_material").getRowData(ids) ; 
		var mode = $("#form_mode").val();
			
		postdata['MODE'] = mode ;
		postdata['LHM_MATERIAL_ID'] = $("#LHM_MATERIAL_ID").val() ;
		postdata['GANG_CODE'] = $("#GANG_CODE").val() ; 
		postdata['LHM_DATE'] = $("#LHM_DATE").val() ; 
		postdata['ACTIVITY_CODE'] = $("#MAT_ACTIVITY").val() ; 
		postdata['LOCATION_CODE']= $("#MAT_LOCATION").val();
		postdata['MATERIAL_QUANTITY']= $("#MAT_QTY").val();
		postdata['MATERIAL_CODE'] = $("#MAT_MATERIAL").val(); 
		postdata['MATERIAL_BPB_NO']= $("#MAT_BPB").val();
			
		if( $("#GANG_CODE").val() == "" || $("#LHM_DATE").val() == "" ||
			$("#MAT_ACTIVITY").val() == "" || $("#MAT_LOCATION").val() == "" ){
					alert('Data masih kosong, mohon diisi terlebih dahulu!');
		} else {
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
			$("#progressbar").dialog('open');
			$.post( url+"m_gang_activity_detail/submit_material/", 
			postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					$("#load").hide();
					document.getElementById('msg').innerHTML= message;
					$("#cok").attr("disabled", false);
				} else { 
					$("#load").hide();
					$("#cok").attr("disabled", false);
					document.getElementById('msg').innerHTML= 'data tersimpan';
					$("#progressbar").dialog('close');
					$('#formi_material').dialog('close');
					reload_material();
					initmaterial();
					closewin();
				};  
			});
		}
	}
	
	function deletematerial(){
	  var postdata={};
	  var ids = jQuery("#list_material").getGridParam('selrow'); 
	  var data = $("#list_material").getRowData(ids);
	  postdata['LHM_MATERIAL_ID'] = data.LHM_MATERIAL_ID ;
	  postdata['GANG_CODE'] = data.GANG_CODE; 
	  postdata['LHM_DATE'] = data.LHM_DATE; 
  	  postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
	  postdata['LOCATION_CODE']= data.LOCATION_CODE;
	  postdata['MATERIAL_CODE'] = data.MATERIAL_CODE; 
	  if( ids != null ){
		var answer = confirm ("Hapus Data Material : " + data.MATERIAL_NAME + " untuk kode aktivitas :" + data.ACTIVITY_CODE + "?" )
			if (answer) {
				$("#cok").attr("disabled", true);
				$("#load").show();
				document.getElementById('msg').innerHTML= "Mohon menunggu... Proses menghapus data...";
				$("#progressbar").dialog('open');
				
				$.post(url+"m_gang_activity_detail/delete_material", postdata,
				function(message) {
						if(message.replace(/\s/g,"") != 0 ) { 
							$("#load").hide();
							document.getElementById('msg').innerHTML= message;
							$("#cok").attr("disabled", false);
					   } else { 
							$("#load").hide();
							$("#cok").attr("disabled", false);
							document.getElementById('msg').innerHTML= 'data berhasil terhapus';
							$("#prj_form").dialog('close'); 
					   		$("#progressbar").dialog('close');
							reload_material();
							closewin();
					   };  
				  } );
			}
		}
		else { alert("Pilih data material yang akan dihapus!"); }
	}
	
	function initmaterial(){
		$("#MAT_ACTIVITY").val('');
		$("#MAT_LOCATION").val('');
		$("#MAT_MATERIAL").val('');
		$("#MAT_DMATERIAL").val('');
		$("#MAT_QTY").val('');
		$("#MAT_UOM").val('');
		$("#LHM_MATERIAL_ID").val('') ;
		$("#MAT_BPB").val('') ;
	}
	
	/* fungsi baru get value kotak yang kebuka : ridhu - 2014-02-13 */
	function getCellValue(rowId, cellId) {
		var cell = jQuery('#' + rowId + '_' + cellId);        
		var val = cell.val();
		return val;
	}
	
	/* fungsi grid baru */
	function disableGridCell(cellName) {
		var cell = $('[name="' + cellName + '"]');
		cell.css('display', 'none');
	
		var div = $("<div>")
			//.css('width', '100%')
			//.css('height', '100%')
			//.css('border', '1px solid #000')
			//.css('background-color', '#CCC')
			.css('disabled', 'disabled');
	
		cell.parent().append(div);
	}  
        
</script>

<form id="form_lhm" name="form_lhm">
<table width="550" class="teks_">
<tr><td width="135">Tanggal</td><td width="9">:</td><td colspan="5"><input class="input" type="text" size=15 id="LHM_DATE" onChange="CheckAfdeling()" /><input type="hidden" id="ccode" value="<?php echo $company_code;?>"></td></tr>
<tr><td>Kode Kemandoran</td><td>:</td><td width="97" style="font-size: 11px;"><!-- <input type="text" id="GANG_CODE" class="input" onkeypress="gangc()"> --><?php echo $GANG_CODE; ?><!-- &nbsp; - &nbsp;<input type="text" size=25 id="NM_KEMANDORAN" class="input_disable" disabled="true" /> --></td>
  <td width="14" style="font-size: 11px;"></td>
  <td width="94" style="font-size: 11px;">Afd / Bagian</td>
  <td width="3" style="font-size: 11px;">:</td>
  <td width="166" style="font-size: 11px;"><input type="text" size="15" class="input_disable" id="AFD" disabled="disabled" name="AFD" /></td>
</tr>
<tr>
  <td>Mandor 1</td><td>:</td><td colspan="5"><input type="text" size="12" class="input" id="NIK_MANDOR1" />&nbsp; - &nbsp;<input type="text" size=25 id="NM_MANDOR1" class="input_disable" disabled="true"/></td></tr>
<tr>
  <td>Mandor</td><td>:</td><td colspan="5"><input type="text" size=12 class="input" id="NIK_MANDOR" />&nbsp; - &nbsp;<input type="text" size=25 id="NM_MANDOR" class="input_disable" disabled="true"/></td></tr>
<tr><td>Kerani</td><td>:</td><td colspan="5"><input type="text" size=12 class="input" id="KD_KERANI" />&nbsp; - &nbsp;<input type="text" size=25 id="NM_KERANI" class="input_disable" disabled="true" /></td></tr>

</table>

<div id="status_entri" style="color:#F00;">Data untuk kemandoran dan tanggal ini sudah ditutup!!</div>
<div id="forms_lhm">
 <table id="list_lhm" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_lhm" class="scroll"></div><br/>
                <div id="save" class="scroll" style="float:left; padding-right:4px;"><input type="button" id="submitdata" value="simpan" onClick="post()" class="basicBtn"></div>
                <div id="update" class="scroll" style="float:left;  padding-right:4px;"><input type="button"  id="updatedata" value="update" onClick="update()" class="basicBtn"></div>
                <input style="padding-right:4px;" type="button" class="basicBtn" id="add" value="Tambah" onClick="addrow()">
                <input style="padding-right:4px;" type="button" class="basicBtn" id="copy" value="Salin" onClick="copyrow()">
                <input style="padding-right:4px;" type="button" class="basicBtn" id="delete" value="Hapus" onClick="hapus_lhm()">
                &nbsp;
                <div id="progress" class="scroll" style="float:left;padding-right:4px;"><input type="button" class="basicBtn" id="bprogress" value="progres" onClick="sprogress()"></div>
				<div id="material" class="scroll" style="float:left;padding-right:4px;"><input type="button" class="basicBtn" id="bmaterial" value="material" onClick="smaterial()"></div>
</div>
<div id="pjm">
<table id="list_pjm" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_pjm" class="scroll"></div><br/>
</div>
                
<div id="peminjaman" title="Form Peminjaman Pegawai">
<table border="0">
<input type="hidden" size=15 id="GC_PINDAH" name="GC_PINDAH" />
<input type="hidden" size=15 id="STATUS_PINJAM" name="STATUS_PINJAM" />
<tr><td style="font-size: 11px;">NIK</td><td>:</td><td><input type="text" class="input_disable" style="font-size: 11px;" id="NIK_PINDAH" disabled="true" name="NIK_PINDAH" /></td></tr>

<tr><td style="font-size: 11px;">Nama</td><td>:</td><td><input type="text" class="input_disable" style="font-size: 11px;" id="NM_PINDAH" disabled="true" name="NM_PINDAH" /></td></tr>

<tr><td style="font-size: 11px;">Tanggal</td><td>:</td><td><input type="text" style=" font-size: 11px;" class="input_disable" id="TGL_PINDAH" disabled="true" name="TGL_PINDAH" /></td></tr>
<tr><td style="font-size: 11px;" >Dari Kemandoran</td><td>:</td><td style="font-size: 11px;">
<input type="text" id="GC_FROM" class="input">
<!-- <input type="text" style=" font-size: 11px;" class="input" id="GC_FROM" disabled="true" name="GC_FROM" />-->
</td></tr> 
<tr><td style="font-size: 11px;" >Ke Kemandoran</td><td>:</td><td style="font-size: 11px;">
<input type="text" id="GC_TO" class="input">
<?php //echo $GC_TO; ?></td></tr>
<tr><td style="font-size: 11px;" >Catatan</td><td>:</td><td style="font-size: 11px;"><textarea class="input" name="remark" id="REMARK" cols=18 rows=2></textarea>
</td></tr>
</table>
</div>

<div id="form_progress">
<a href="#" onClick="kembali()" style='cursor:pointer; margin-top:10px; font-size:13px;'>kembali ke lhm</a><br/>
<table id="list_progress" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_progress" class="scroll"></div><br/>
<div id="simpan" class="scroll" style="float:left;"><input type="button" id="sub_progress" value="simpan" onClick="subm_progress()" class="basicBtn">&nbsp;</div> 
<div id="ptambah" class="scroll" style="float:left;"><input type="button" id="add_progress" value="tambah" onClick="addrowp()" class="basicBtn">&nbsp;</div>
<div id="phapus" class="scroll" style="float:left;"><input type="button" id="dlt_progress" value="hapus" onClick="hapus_progress()" class="basicBtn"></div>
<br/>
</div>        

<!-- input material -->
<div id="formg_material">

<table id="list_material" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_material" class="scroll"></div><br/>
	<div id="pmtambah" class="scroll" style="float:left;">
		<input type="button"  id="add_material" value="tambah" onClick="addmaterial()" class="basicBtn">
    </div>
<div id="pmupdate" class="scroll" style="float:left;">
		<input type="button"  id="upd_material" value="Ubah" onClick="editmaterial()" class="basicBtn">
    </div>
	<div id="pmhapus" class="scroll" style="float:left;">
    	<input type="button"  id="dlt_material" value="hapus" onClick="deletematerial()" class="basicBtn">
    </div>

</div>        
<!-- end material -->

<!-- form material -->
<div id="formi_material" class="teks_" >
<table width="100%">
<tr>
	<td>Aktivitas</td>
    <td>:</td>
    <td><input type="text" id="MAT_ACTIVITY" class="input" onkeypress="getActMaterial()"></td>
</tr>
<tr>
	<td>Lokasi</td>
    <td>:</td>
    <td><input type="text" id="MAT_LOCATION" class="input" onkeypress="getActActivity()"></td>
</tr>
<tr>
	<td>Kode Material</td>
    <td>:</td>
    <td><input type="text" id="MAT_MATERIAL" class="input" onkeypress="getMaterial()"></td>
</tr>
<tr>
	<td>Deskripsi Material</td>
    <td>:</td>
    <td><input type="text" id="MAT_DMATERIAL" class="input_disable" disabled="disabled" style="width:180px"></td>
</tr>
<tr>
	<td>Kuantitas</td>
    <td>:</td>
    <td><input type="text" id="MAT_QTY" style="width:80px" class="positive" ></td>
</tr>
<tr>
	<td>Satuan</td>
    <td>:</td>
    <td><input type="text" id="MAT_UOM" class="input_disable" disabled="disabled">
    <input type="hidden" id="form_mode"></td>
</tr>
<tr>
	<td>No. Bon Permintaan Barang ( BPB )</td>
    <td>:</td>
    <td><input type="text" id="MAT_BPB" class="input" style="width:100px" >
    <input type="hidden" id="form_mode">
    <input type="hidden" id="LHM_MATERIAL_ID"></td>
</tr>
</table>
</div>
<!-- end form material -->

<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 
<!-- end progress bar -->

</form> 

</body>