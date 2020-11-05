<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
jQuery(document).ready(function(){

	$("#bulan").change(function() {
	  reloadGridWs();
	});
	
	$("#tahun").change(function() {
	  reloadGridWs();
	});
	
	$(function () {
		document.getElementById("kd_ws").value = "";
	});

}); 

function reloadGridWs(){
	var bln = $("#bulan").val();
	var thn = $("#tahun").val();
	var ws = $("#kd_ws").val();
	jQuery("#list_wa").setGridParam({url:url+'p_workshop_activity/grid_p_workshop/'+ws+'/'+bln+'/'+thn}).trigger("reloadGrid");
}
	
function giveLocType(){		
	var ids = jQuery('#list_wa').getGridParam('selrow'); 
	var rets = jQuery('#list_wa').getRowData(ids); 
	var type = rets.LOCATION_TYPE_CODE;
	return type;
} 
		
function giveLocCode(){		
	var ids = jQuery("#list_wa").getGridParam('selrow'); 
	var rets = jQuery("#list_wa").getRowData(ids); 
	var type = rets.LOCATION_CODE;
	return type;
}

var jGrid_wa = null;
var colNamesT_wa = new Array();
var colModelT_wa = new Array();

colNamesT_wa.push('no');
colModelT_wa.push({name:'no_wa',index:'no_wa', sortable:false, resizable:true, 
				  editable: false,hidden:true, width: 30, align:'center'});

colNamesT_wa.push('id');
colModelT_wa.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});

colNamesT_wa.push('kode_workshop');
colModelT_wa.push({name:'KODE_WORKSHOP',index:'KODE_WORKSHOP', editable: true, 
				  hidden:true, width: 90, align:'center'});

colNamesT_wa.push('Tgl_Aktivitas');
colModelT_wa.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: true,
				  hidden:false, width: 100, align:'center'});

colNamesT_wa.push('Tipe Lokasi');
colModelT_wa.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true,edittype: 'text', editoptions:{
				size:10, maxlength:20,
	            dataInit:function (elem) { $(elem).autocomplete(['GC', 'MA', 'VH','SA'],{
				  		dataType: 'ajax', width: 320, max: 5,
						highlight: false, multiple: false, scroll: true, scrollHeight: 300
				}).result(function(e, item) {
					var id = jQuery("#list_wa").getGridParam('selrow');
                    if (id) { 
                       var ret = jQuery("#list_wa").getRowData(id);
                       jQuery("#list_wa").setRowData(id,{LOCATION_CODE:""});
                       jQuery("#list_wa").setRowData(id,{ACTIVITY_CODE:""});
                    }
	            });
	      }}, width: 130, align:'center'});

colNamesT_wa.push('Lokasi');
colModelT_wa.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,async: false,edittype: 'text',editoptions:{
				size:20, maxlength:125,
	            dataInit:function (elem) { 
	            	$(elem).autocomplete( 
				  	  url+'p_workshop_activity/location/'+giveLocType(), {
	                  dataType: 'ajax', width: 320, max: 15,
	                  multiple: false, autoFill: false, mustMatch: true, matchContains: false,
					  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  }, formatItem: function(item) {
	                    	return (typeof(item) == 'object')?item.res_dl :'';
	                  }
	                }).result(function(e, item) {
	                	$('#LOCATION_CODE').val(item.res_name );
						var id = jQuery("#list_wa").getGridParam('selrow');
						if (id) { 
						   var ret = jQuery("#list_wa").getRowData(id);
						   jQuery("#list_wa").setRowData(id,{ACTIVITY_CODE:""});
						}
	              	});
          }}, width: 130, align:'center'});

colNamesT_wa.push('Kode Aktivitas');
colModelT_wa.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,hidden:false, 
				editoptions:{
				size:64, maxlength:255, dataInit:function (elem) { 
	            	$(elem).autocomplete( // for more info check the autocomplete plugin docs
	                	url+"p_workshop_activity/activity/"+giveLocType()+"/"+giveLocCode(), {
	                  	dataType: 'ajax', multiple: false,
	                  	parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_d, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  }, formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_d :'';
	                  }
	                }).result(function(e, item) {
	                	$("#LOCATION_TYPE_CODE").val(item.res_name );
	              	});
				  }}, width: 100, align:'center'});

colNamesT_wa.push('Jam Kerja');
colModelT_wa.push({name:'JAM_KERJA',index:'JAM_KERJA', editable: true,hidden:false, width: 120, align:'center'});
colNamesT_wa.push('company');
colModelT_wa.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,
				  hidden:true, width: 90, align:'center'});		
colNamesT_wa.push('');
colModelT_wa.push({name:'action',index:'action', align:'center', resizable:false, 
				  sortable:false, editable: false,hidden:false, width: 30});	  
				  
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		var loadView = function(){
            jGrid = jQuery('#list_wa').jqGrid({
                url:url+'p_workshop_activity/grid_p_workshop/xx/xx/xx',
                mtype : 'POST', datatype: 'json', colNames: colNamesT_wa ,
                colModel: colModelT_wa , sortname: colNamesT_wa[2], pager:jQuery('#pager_wa'),
              	rowNum: 400, rownumbers: true, height: 370, imgpath: gridimgpath,
				sortorder: 'asc', cellEdit: true, cellsubmit: 'clientArray',
				forceFit : true, loadComplete: function(){ 
					var ids = jQuery('#list_wa').getDataIDs(); 
					var id = jQuery('#list_wa').getGridParam('selrow'); 
					var rets = jQuery('#list_wa').getRowData(id); 
					for(var i=0;i<ids.length;i++){ 
						var cl = ids[i]; 
				    	be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"');\"/>";
						jQuery("#list_wa").setRowData(ids[i],{action:be+ce}) 
				}}, 
				afterEditCell: function (id,name,val,iRow,iCol){ 			
					 if(name=='TGL_AKTIVITAS') { 
					 	jQuery('#'+iRow+'_TGL_AKTIVITAS','#list_wa').datepicker({dateFormat:'yy-mm-dd'}); } 
					  }
            }); /* tutup jgrid */
			
            jGrid.navGrid('#pager_wa',{edit:false,add:false,del:false, search: false, refresh: true});
			jGrid.navButtonAdd('#pager_wa',{
			   caption:'Tambah Data', 
			   buttonicon:'ui-icon-add', 
			   onClickButton: function(){ 
						addrow()
			   }, position:'last'
			});
			/* jGrid.navButtonAdd('#pager_wa',{
			   caption:'Export Data', 
			   buttonicon:'ui-icon-add', 
			   onClickButton: function(){ 
						addrow()
			   }, position:'last'
			}); */
        }; /* tutup loadview */
		
        jQuery('#list_wa').ready(loadView);	
	
		function post(i) {
			var postdata = {}; 
			var ids = jQuery("#list_wa").getGridParam('selrow'); 
		    var data = $("#list_wa").getRowData(ids) ; 
		    postdata['KODE_WORKSHOP'] = $("#kd_ws").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE;
			postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		 				
			$.post( url+'p_workshop_activity/create_wa', postdata,function(status) { 
		      	var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
						//alert('data berhasil tersimpan.')
						reloadGrid();
		           };
		     }); 
		}
		
		function update(id) {
		    var postdata = {}; 
			var ids = jQuery("#list_wa").getGridParam('selrow'); 
		    var data = $("#list_wa").getRowData(ids) ; 
		    postdata['KODE_WORKSHOP'] = $("#kd_ws").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE; 
			postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		 					
			$.post( url+'p_workshop_activity/update_wa/'+id, postdata,function(status) { 
		       var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
						reloadGrid();
						//alert('data berhasil tersimpan.')
		           };
		      } ); 
		}
				
		function hapus(ids) {
		   var postdata = {}; 
		   $.post( url+'p_workshop_activity/delete/'+ids, postdata,function(message,status) { 
		       if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						reloadGrid();
						alert('data berhasil terhapus.')
		           };  
		      } );
		}	
		
		function reloadGrid(){
		 	 var kd_ws = document.getElementById("kd_ws").value; 	
			 var bln = document.getElementById("bulan").value;	
			 var thn = document.getElementById("tahun").value;
			 jQuery("#list_wa").setGridParam({url:url+'p_workshop_activity/grid_p_workshop/'+kd_ws+'/'+bln+'/'+thn}).trigger("reloadGrid");	
			 }	
									
			
		function addrow(){
						var kd_ws = document.getElementById("kd_ws").value;
						var i = jQuery('#list_wa').getGridParam('records');
						var ids = jQuery("#list_wa").getGridParam('selrow');
												
						if (kd_ws != ""){
							var id = jQuery("#list_wa").getGridParam('selrow');
				 			var dat = jQuery("#list_wa").getRowData(id);
								
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {kd_ws:jdesc1};
							}
							
	sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />";  

				 		   	var su=jQuery("#list_wa").addRowData(i,datArr,'last');
							var no=jQuery("#list_wa").setRowData(i,{no_wa:i});	
							var act=jQuery("#list_wa").setRowData(i,{action:sv}) 
							

						} else {
							alert('Pilih workshop terlebih dahulu!');							
						}
		}		 				
</script>

<form id="form_wa" name="form_wa">
<table border="0" class="teks_" style="padding:7px;">
<tr><td>Kode Workshop</td><td>:</td><td style="font-size: 11px;"><!-- <input type="text" style="text-transform: uppercase; font-size: 11px;" id="kd_ws" class="input"> --> <?php if(isset($WS)){ echo $WS; }?></td></tr>
<!-- <tr><td>Nama Workshop</td><td>:</td><td style="font-size: 11px;"><input type="text" style="text-transform: uppercase; font-size: 11px; width:250px;" id="nm_ws" disabled="true" class="input_disable"></td></tr> -->
<tr><td>Periode</td><td>:</td><td><? echo $periode ?></td></tr>
</table>
<table id="list_wa" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_wa" class="scroll" style="text-align:center;"></div>


</form>
</body>
