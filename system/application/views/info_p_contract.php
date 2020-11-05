<? 
 	header('expire-header');

	$template_path = base_url().$this->config->item('template_path');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
<!-- dialog -->	
	<link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
<!-- end dialog --->
</head>

<body>

<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
<script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
<script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
<script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>

<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){

$(function () {
document.getElementById("kode_kend").value = "";
document.getElementById("jns_kend").value = "";
document.getElementById("no_pol").value = "";
document.getElementById("sat_prestasi").value = "";
});


	$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu
											//onHideCheck: onHideCheckMenu,
											//onHide: onHideMenu, 
											//onClick: onClickMenu, 
											//onAnimate: onAnimate
											});
				$('ul.jd_menu_vertical').jdMenu({onShow: loadMenu, onHide: unloadMenu, offset: 1, onAnimate: onAnimate});
			});

			function onAnimate(show) {
				//$(this).fadeIn('slow').show();
				if (show) {
					$(this)
						.css('visibility', 'hidden').show()
							.css('width', $(this).innerWidth())
						.hide().css('visibility', 'visible')
					.fadeIn('normal');
				} else {
					$(this).fadeOut('fast');
				}
			}

			var MENU_COUNTER = 1;
			function loadMenu() {
				if (this.id == 'dynamicMenu') {
					$('> ul > li', this).remove();
			
					var ul = $('<ul></ul>');
					var t = MENU_COUNTER + 10;
					for (; MENU_COUNTER < t; MENU_COUNTER++) {
						$('> ul', this).append('<li>Item ' + MENU_COUNTER + '</li>');
					}
				}
			}

			function unloadMenu() {
				if (MENU_COUNTER >= 30) {
					MENU_COUNTER = 1;
				}
			}

			// We're passed a UL
			function onHideCheckMenu() {
				return !$(this).parent().is('.LOCKED');
			}

			// We're passed a LI
			function onClickMenu() {
				$(this).toggleClass('LOCKED');
				return true;
			}
			
$(function () {
	            $("#kode_kend")
	              .autocomplete( 
	                url+"p_vehicle_activity/kode_kend/", {
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
				    
				  	var bln = $("#bulan").val();
				  	var thn = $("#tahun").val();
					var jumlah = {};
					var postdata = {}; 
		
	                $("#kode_kend").val(item.res_id);
	                $("#jns_kend").val(item.res_name );
	                $("#sat_prestasi").val(item.sat_pres );
					
			
				jQuery("#list_va").setGridParam({url:url+'p_vehicle_activity/grid_vehicle_activity/'+item.res_id+'/'+bln+'/'+thn}).trigger("reloadGrid");	
			

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
});

	function giveLocType(){		
		var ids = jQuery("#list_va").getGridParam('selrow'); 
		var rets = jQuery("#list_va").getRowData(ids); 
		var type = rets.LOCATION_TYPE_CODE;
		return type;
	} 
		
var url = "<?= base_url().'index.php/' ?>";

var jGrid_va = null;
var colNamesT_va = new Array();
var colModelT_va = new Array();

colNamesT_va.push('no');
colModelT_va.push({name:'no_va',index:'no_va', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_va.push('id');
colModelT_va.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});
       

colNamesT_va.push('kode_kendaraan');
colModelT_va.push({name:'KODE_KENDARAAN',index:'KODE_KENDARAAN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Bulan');
colModelT_va.push({name:'BULAN',index:'BULAN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Tahun');
colModelT_va.push({name:'TAHUN',index:'TAHUN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Tgl');
colModelT_va.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: true, hidden:false, width: 70, align:'center'});

colNamesT_va.push('Berangkat');
colModelT_va.push({name:'JAM_BERANGKAT',index:'JAM_BERANGKAT', editable: true,hidden:false, width: 70, align:'center'});       

colNamesT_va.push('Kembali');
colModelT_va.push({name:'JAM_KEMBALI',index:'JAM_KEMBALI', editable: true,hidden:false, width: 70, align:'center'});       

colNamesT_va.push('Jam Kerja');
colModelT_va.push({name:'JAM_KERJA',index:'JAM_KERJA', editable: false,hidden:true, width: 70, align:'center'});       

colNamesT_va.push('km/hm berangkat');
colModelT_va.push({name:'KMHM_BERANGKAT',index:'KMHM_BERANGKAT', editrules:{number:true}, editable: true,hidden:false, width: 90, align:'center'}); 

colNamesT_va.push('km/hm kembali');
colModelT_va.push({name:'KMHM_KEMBALI',index:'KMHM_KEMBALI', editrules:{number:true}, editable: true,hidden:false, width: 90, align:'center'}); 

colNamesT_va.push('km/hm jumlah');
colModelT_va.push({name:'KMHM_JUMLAH',index:'KMHM_JUMLAH', editable: false,hidden:true, width: 70, align:'center'}); 

colNamesT_va.push('Type');
colModelT_va.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true,
		edittype: "text", editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_machine/location_type", {
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
	              });
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Lokasi');
colModelT_va.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true
,async: false,edittype: "text",editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { 
				
	            $(elem)
	              .autocomplete( 
				  	
	                url+"p_vehicle_activity/location/"+giveLocType(), {
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
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Aktivitas');
colModelT_va.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,
				editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+"p_vehicle_activity/activity/"+giveLocType(), {
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
				  }}, width: 70, align:'center'}); 

colNamesT_va.push('Jenis Muatan');
colModelT_va.push({name:'MUATAN_JENIS',index:'MUATAN_JENIS', editable: true,hidden:false, 
editoptions:{
				size:35,
	            maxlength:100,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_vehicle_activity/muatan/", {
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
	                    return (typeof(item) == 'object')?item.res_dl :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                $("#MUATAN_JENIS").val(item.res_id);
					var id = jQuery("#list_va").getGridParam('selrow');
					
					if (id) 
					{ 
						var ret = jQuery("#list_va").getRowData(id);
						jQuery("#list_va").setRowData(id,{MUATAN_SAT:item.res_sat});

					}
	              });
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Vol Muatan');
colModelT_va.push({name:'MUATAN_VOL',index:'MUATAN_VOL', editable: true,editrules:{number:true},hidden:false, width: 70, align:'center'}); 

colNamesT_va.push('Sat. Muatan');
colModelT_va.push({name:'MUATAN_SAT',index:'MUATAN_SAT', editable: true,edittype: "text", width: 70, align:'center'}); 

colNamesT_va.push('Vol. Prestasi Alat');
colModelT_va.push({name:'PRESTASI_VOL',index:'PRESTASI_VOL', editable: true,hidden:false,editrules:{number:true}, width: 70, align:'center'}); 

colNamesT_va.push('Sat. Prestasi Alat');
colModelT_va.push({name:'PRESTASI_SAT',index:'PRESTASI_SAT', editable: true,hidden:false, 
editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_vehicle_activity/satuan/", {
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
          }},width: 70, align:'center'}); 

colNamesT_va.push('');
colModelT_va.push({name:'action',index:'action', align:'center', resizable:false, sortable:false, editable: false,hidden:false, width: 30});
		
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
        var loadView_va = function()
        {
		jGrid_va = jQuery("#list_va").jqGrid(
            {
				url:url+'p_vehicle_activity/grid_vehicle_activity/xx/',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_va ,
                colModel: colModelT_va ,
               	sortname: colNamesT_va[2],
				pager:jQuery("#pager_va"),
              	rowNum: 400,
				rownumbers: true,
                height: 370,
                imgpath: gridimgpath,
				sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){},
				loadComplete: function(){ 
				var ids = jQuery("#list_va").getDataIDs(); 
				var id = jQuery("#list_va").getGridParam('selrow'); 
				var rets = jQuery("#list_va").getRowData(id); 
			
				for(var i=0;i<ids.length;i++)
					{ 
						var cl = ids[i]; 
						
				    	be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"');\"/>";
						jQuery("#list_va").setRowData(ids[i],{action:be+ce}) 
					}
										
				}, 
				
				afterEditCell: function (id,name,val,iRow,iCol)
					{ 			
					 if(name=='TGL_AKTIVITAS') 
						{ jQuery("#"+iRow+"_TGL_AKTIVITAS","#list_va").datepicker({dateFormat:"yy-mm-dd"}); } 
					 if(name=='JAM_BERANGKAT')
					  { jQuery("#"+iRow+"_JAM_BERANGKAT","#list_va").datepicker({dateFormat:"yy-mm-dd",time24h: true,duration: '',showTime:true,constrainInput: true});}	
					 	if(name=='JAM_KEMBALI')
					  { jQuery("#"+iRow+"_JAM_KEMBALI","#list_va").datepicker({dateFormat:"yy-mm-dd",time24h: true,duration: '',showTime:true,constrainInput: true});}	
					} 
			});
		 jGrid_va.navGrid('#pager_va',{edit:false,del:false,add:false, search: false, refresh: true});
		 
		 
		 jGrid_va.navButtonAdd('#pager_va',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
						addrow()
			   }, 
			   position:"last"
			});
			
		 }
		 jQuery("#list_va").ready(loadView_va);
		 
		 function post(i) {
			
		    var postdata = {}; 
			var ids = jQuery("#list_va").getGridParam('selrow'); 
			
		    var data = $("#list_va").getRowData(ids) ; 
		       
		    postdata['KODE_KENDARAAN'] = $("#kode_kend").val() ; 
		    postdata['SATUAN_PRESTASI'] = $("#sat_prestasi").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['JAM_BERANGKAT'] = data.JAM_BERANGKAT; 
		    postdata['JAM_KEMBALI'] = data.JAM_KEMBALI; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		    postdata['KMHM_BERANGKAT'] = data.KMHM_BERANGKAT; 
		    postdata['KMHM_KEMBALI'] = data.KMHM_KEMBALI; 
		    postdata['KMHM_JUMLAH'] = data.KMHM_JUMLAH; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		    postdata['MUATAN_JENIS'] = data.MUATAN_JENIS; 
		    postdata['MUATAN_SAT'] = data.MUATAN_SAT; 
		    postdata['MUATAN_VOL'] = data.MUATAN_VOL; 
		    postdata['PRESTASI_VOL'] = data.PRESTASI_VOL; 
		    postdata['PRESTASI_SAT'] = data.PRESTASI_SAT; 
			postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			
			$.post( url+'p_vehicle_activity/create_va', postdata,function(message,status) { 
		      	 if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						reloadGrid();
						alert('data berhasil tersimpan.')
		           };
		      } ); 
		}
		
		
		 function update(id) {
			
		    var postdata = {}; 
			var ids = jQuery("#list_va").getGridParam('selrow'); 
			
		    var data = $("#list_va").getRowData(ids) ; 
		       
		    postdata['KODE_KENDARAAN'] = $("#kode_kend").val() ; 
		    postdata['SATUAN_PRESTASI'] = $("#sat_prestasi").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['JAM_BERANGKAT'] = data.JAM_BERANGKAT; 
		    postdata['JAM_KEMBALI'] = data.JAM_KEMBALI; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		    postdata['KMHM_BERANGKAT'] = data.KMHM_BERANGKAT; 
		    postdata['KMHM_KEMBALI'] = data.KMHM_KEMBALI; 
		    postdata['KMHM_JUMLAH'] = data.KMHM_JUMLAH; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		    postdata['MUATAN_JENIS'] = data.MUATAN_JENIS; 
		    postdata['MUATAN_SAT'] = data.MUATAN_SAT; 
		    postdata['MUATAN_VOL'] = data.MUATAN_VOL; 
		    postdata['PRESTASI_VOL'] = data.PRESTASI_VOL; 
		    postdata['PRESTASI_SAT'] = data.PRESTASI_SAT; 
			postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			
			$.post( url+'p_vehicle_activity/update_va/'+id, postdata,function(message,status) { 
		        if(status != 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						reloadGrid();
						alert('data berhasil diubah.')
		           }; 
		      } ); 
		}
				
		function hapus(ids) {
		   var postdata = {}; 
		   $.post( url+'p_vehicle_activity/delete/'+ids, postdata,function(message,status) { 
		       if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						reloadGrid();
						alert('data berhasil terhapus.')
		           };  
		      } );
		}	
		 
		 function reloadGrid(){
		 var vc = document.getElementById("kode_kend").value; 	
		 var bln = document.getElementById("bulan").value;	
		 var thn = document.getElementById("tahun").value;
		 jQuery("#list_va").setGridParam({url:url+'p_vehicle_activity/grid_vehicle_activity/'+vc+'/'+bln+'/'+thn}).trigger("reloadGrid");	
		 }
							
		 function addrow(){
						var gc = document.getElementById("kode_kend").value;
						var i = jQuery('#list_va').getGridParam('records');
						var ids = jQuery("#list_va").getGridParam('selrow');
												
						if (gc != ""){
							var id = jQuery("#list_va").getGridParam('selrow');
				 			var dat = jQuery("#list_va").getRowData(id);
								
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {jns_kend:jdesc1};
							}
							
							sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 

				 		   	var su=jQuery("#list_va").addRowData(i,datArr,'last');
							var no=jQuery("#list_va").setRowData(i,{no_va:i});	
							var act=jQuery("#list_va").setRowData(i,{action:sv}) 
							//var satuan_prestasi = document.getElementById("sat_prestasi").value;
							//var sat=jQuery("#list_va").setRowData(i,{PRESTASI_SAT:satuan_prestasi}) 

						} else {
							alert('Pilih kode kendaraan terlebih dahulu!');							
						}
		}
		
		
		function removerow(){
						var gc = document.getElementById("kode_kend").value;
						var i = jQuery('#list_va').getGridParam('records');
						var ids = jQuery("#list_va").getGridParam('selrow'); 
						
						if (gc != ""){
							var id = jQuery("#list_va").getGridParam('selrow');
				 			var dat = jQuery("#list_va").getRowData(id);
								
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {jns_kend:jdesc1};
							}
							
							sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 

				 		   	var su=jQuery("#list_va").addRowData(i,datArr,"last");
					
						} else {
							alert('Pilih kode kendaraan terlebih dahulu!');							
						}
		}
		
</script>

<div class="teks_headline"><strong class="accessible"><?php echo $company_dest;?><br>Rekapitulasi Laporan Progres Kontrak<br/></strong></div><hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
<div style="float:right">

	<ul class="jd_menu jd_menu_slate">
		<li><a href="#" class="accessible">Transaksi</a>
			<ul>
				<li><a href="<?= base_url()?>index.php/m_gang_activity_detail">Laporan harian mandor</a></li>
				<li><a href="<?= base_url()?>index.php/p_machine">Buku mesin</a></li>
				<li><a href="<?= base_url()?>index.php/p_vehicle_activity">Buku Kendaraan</a></li>
				<li><a href="#">Mutasi Karyawan</a></li>
			</ul>
		</li>
		<?php if( $user_level == 'SAD' || $user_level == 'ADM') { ?>
		<li><a href="#" class="accessible">Master Data</a>
			<ul>
				<li><a href="<?= base_url()?>index.php/m_employee">Karyawan</a></li>
				<li><a href="<?= base_url()?>index.php/m_gang">Kemandoran</a></li>
				<li><a href="<?= base_url()?>index.php/p_empcopy">Mutasi Karyawan</a></li>
				<li><a href="#">Kendaraan</a></li>
				<li><a href="#">Mesin</a></li>
				<li><a href="#">Blok Tanam</a></li>
				<li><a href="#">Workshop</a></li>
			</ul>
		</li>
        <?php } ?>
	<?php if( $user_level == 'SAD' ) { ?>
		<li><a href="#" class="accessible" style="height:20px;">Reporting</a>
			<ul style="width: 200px;">
								
					<li><a href="#">Daftar Upah </a>
                    	<ul>
                        	<li><a href="<?= base_url()?>index.php/rpt_du">Daftar Upah Per Kemandoran</a></li>
                         	<li><a href="<?= base_url()?>index.php/rpt_du/du_afd">Daftar Upah Per Divisi / Bagian</a></li>
                          	<li><a href="<?= base_url()?>index.php/rpt_du_act">Daftar Upah Per Aktivitas</a></li>
                        </ul>
                    </li>
					<li><a href="#">Berita Acara</a>
						<ul>
					<li><a href="<?= base_url()?>index.php/rpt_ba_rawat">Berita Acara Gaji Rawat</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_panen">Berita Acara Gaji Panen</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_transportpanen">Berita Acara Gaji Transport Panen</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_bibitan">Berita Acara Gaji Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_sisip">Berita Acara Gaji Sisip</a></li>
					<li><a href="<?= base_url()?>index.php/rpt_ba_pjtanam">Berita Acara Hasil Kerja Project Tanam</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjbibitan">Berita Acara Hasil Kerja Project Persiapan Bibitan</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_pjinfrastruktur">Berita Acara Gaji Project Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_rawat_infrastruktur">Berita Acara Rawat Infrastruktur</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_umum">Berita Acara Gaji Umum</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_vmw">Berita Acara Gaji Kendaraan, Workshop, Mesin</a></li>
                    <li><a href="<?= base_url()?>index.php/rpt_ba_tunpot">Berita Acara Tunjangan dan Potongan</a></li>
                    <hr />
                    <li><a href="#">Komparasi DU dan BA</a>
                     <ul>
						<li><a href="<?= base_url()?>index.php/rpt_rekonbadu">Rekonsiliasi BA & DU</a></li>
					 </ul>
                    </li>
						</ul>
					</li>
					<li><a href="<?= base_url()?>index.php/rpt_progress/progress">Progress</a>
					</li>	
				<hr>
					<li><a href="<?= base_url()?>index.php/rpt_lhm">Export Data</a>
					</li>	
			</ul>
			
		</li>
		<?php } ?>
		<li style="float:right;">&nbsp;&nbsp;&nbsp;Logged as, <?php echo $login_id; ?> &nbsp; | &nbsp; <a href="<?= base_url()?>index.php/login/Dologout">Logout</a> </li>
	</ul>
 
 
 </div>



<form id="form_va" name="form_va">
<table class="teks_">
<tr>
<td>Kode Kendaraan</td><td>:</td><td><input type="text" style="text-transform: uppercase; font-size: 11px;" id="kode_kend" class="input"></td>
<td width="20px"></td>
<td>Jenis kendaraan</td><td>:</td><td><input type="text" style="width:200px;" class="input_disable" id="jns_kend" disabled="true"></td></tr>
<tr><td>Nomor Polisi</td><td>:</td><td><input type="text" class="input_disable" id="no_pol" disabled="true"></td><td></td><td>Satuan Unit</td><td>:</td><td><input type="text" id="sat_prestasi" style="text-transform: uppercase; font-size: 11px;" class="input_disable" disabled="true"></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode; ?></td><td></td><td></td><td></td><td></td></tr>
</table>

 <table id="list_va" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_va" class="scroll" style="text-align:center;"></div><br/>
</form>


</body>