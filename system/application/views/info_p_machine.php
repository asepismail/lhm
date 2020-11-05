<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){

$(function () {
document.getElementById("kode_ma").value = "";
document.getElementById("sat_unit_ma").value = "";
});

/*menu*/

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

/*end menu*/


$(function () {
	            $("#kode_ma")
	              .autocomplete( 
	                url+"p_machine/kode_mesin/", {
	                  dataType: 'ajax',
					  width:400,
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
		
	                $("#kode_ma").val(item.res_id);
	               					
			
				jQuery("#list_ma").setGridParam({url:url+'p_machine/grid_p_machine/'+item.res_id+'/'+bln+'/'+thn}).trigger("reloadGrid");	
			

				  });
	              
          });
		  

});

function giveLocType(){		
		var ids = jQuery("#list_ma").getGridParam('selrow'); 
		var rets = jQuery("#list_ma").getRowData(ids); 
		var type = rets.LOCATION_TYPE_CODE;
		return type;
} 

function giveLocCode(){        
        var ids = jQuery("#list_ma").getGridParam('selrow'); 
        var rets = jQuery("#list_ma").getRowData(ids); 
        var type = rets.LOCATION_CODE;
        return type;
    }
	
var url = "<?= base_url().'index.php/' ?>";

var jGrid_ma = null;
var colNamesT_ma = new Array();
var colModelT_ma = new Array();

colNamesT_ma.push('no');
colModelT_ma.push({name:'no_ma',index:'no_ma', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});

colNamesT_ma.push('id');
colModelT_ma.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});

colNamesT_ma.push('KODE_MESIN');
colModelT_ma.push({name:'KODE_MESIN',index:'KODE_MESIN', editable: true,hidden:true, width: 100, align:'center'});

colNamesT_ma.push('SATUAN_PRESTASI');
colModelT_ma.push({name:'SATUAN_PRESTASI',index:'SATUAN_PRESTASI', editable: true,hidden:true, width: 100, align:'center'});

colNamesT_ma.push('bulan');
colModelT_ma.push({name:'BULAN',index:'BULAN', editable: false,hidden:true, width: 30, align:'center'});

colNamesT_ma.push('tahun');
colModelT_ma.push({name:'TAHUN',index:'TAHUN', editable: false,hidden:true, width: 30, align:'center'});

colNamesT_ma.push('Tgl');
colModelT_ma.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: true,hidden:false, width: 100, align:'center'});

colNamesT_ma.push('Tipe');
colModelT_ma.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true,edittype: "text", editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete(["IF", "OP", "GC", "NS","SA", "PJ"],
				  {
				  		dataType: 'ajax',
						width: 320,
						max: 5,
						highlight: false,
						multiple: false,
						scroll: true,
						scrollHeight: 300
					} 
	              )
	             
          }}, width: 100, align:'center'});

colNamesT_ma.push('Location');
colModelT_ma.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,async: false,edittype: "text",editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { 
				
	            $(elem)
	              .autocomplete( 
				  	
	                url+"p_machine/location/"+giveLocType(), {
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
          }}, width: 100, align:'center'});

colNamesT_ma.push('Activity');
colModelT_ma.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,
				editoptions:{
				size:64,
	            maxlength:255,
	            dataInit:function (elem) { // the moment of magic Wink
	            $(elem)
	              .autocomplete( // for more info check the autocomplete plugin docs
	                url+"p_machine/activity/"+giveLocType()+"/"+giveLocCode(), {
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
	              });
				  }}, width: 100, align:'center'});


colNamesT_ma.push('Meter Pemakaian');
colModelT_ma.push({name:'METER_PEMAKAIAN',index:'METER_PEMAKAIAN', editable: true,hidden:false, width: 130, align:'center'});

colNamesT_ma.push('Jam Kerja');
colModelT_ma.push({name:'JAM_KERJA',index:'JAM_KERJA', editable: true,hidden:false, width: 80, align:'center'});

colNamesT_ma.push('keterangan');
colModelT_ma.push({name:'KETERANGAN',index:'KETERANGAN', editable: true,hidden:true, width: 130, align:'center'});

colNamesT_ma.push('');
colModelT_ma.push({name:'action',index:'action', editable: false,hidden:false, width: 30, align:'center'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function()
        {
            jGrid = jQuery("#list_ma").jqGrid(
            {
                url:url+'p_machine/grid_p_machine/xx/xx/xx',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_ma ,
                colModel: colModelT_ma ,
               	sortname: colNamesT_ma[2],
				pager:jQuery("#pager_ma"),
              	rowNum: 400,
				rownumbers: true,
                height: 370,
                imgpath: gridimgpath,
				sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				loadComplete: function(){ 
				var ids = jQuery("#list_ma").getDataIDs(); 
				var id = jQuery("#list_ma").getGridParam('selrow'); 
				var rets = jQuery("#list_ma").getRowData(id); 
			
				for(var i=0;i<ids.length;i++)
					{ 
						var cl = ids[i]; 
						
				    	be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
						ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"');\"/>";
						jQuery("#list_ma").setRowData(ids[i],{action:be+ce}) 
					}
										
				}, 
				afterEditCell: function (id,name,val,iRow,iCol)
					{ 			
					 if(name=='TGL_AKTIVITAS') 
						{ jQuery("#"+iRow+"_TGL_AKTIVITAS","#list_ma").datepicker({dateFormat:"yy-mm-dd"}); } 
					}
            } ); /* tutup jgrid */
			
            jGrid.navGrid('#pager_ma',{edit:false,add:false,del:false, search: false, refresh: true});
			
			 jGrid.navButtonAdd('#pager_ma',{
			   caption:"Add", 
			   buttonicon:"ui-icon-add", 
			   onClickButton: function(){ 
						addrow()
			   }, 
			   position:"last"
			});
        }; /* tutup loadview */
		
        jQuery("#list_ma").ready(loadView);	
			
		function addrow(){
						var mc = document.getElementById("kode_ma").value;
						var i = jQuery('#list_ma').getGridParam('records');
						var ids = jQuery("#list_ma").getGridParam('selrow');
												
						if (mc != ""){
							var id = jQuery("#list_ma").getGridParam('selrow');
				 			var dat = jQuery("#list_ma").getRowData(id);
								
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {kode_ma:jdesc1};
							}
							
							sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 

				 		   	var su=jQuery("#list_ma").addRowData(i,datArr,'last');
							var no=jQuery("#list_ma").setRowData(i,{no_ma:i});	
							var act=jQuery("#list_ma").setRowData(i,{action:sv}) 
							//var satuan_prestasi = document.getElementById("sat_prestasi").value;
							//var sat=jQuery("#list_va").setRowData(i,{PRESTASI_SAT:satuan_prestasi}) 

						} else {
							alert('Pilih kode mesin terlebih dahulu!');							
						}
		}	
		
		 function post(i) {
			
		    var postdata = {}; 
			var ids = jQuery("#list_ma").getGridParam('selrow'); 
			
		    var data = $("#list_ma").getRowData(ids) ; 
		       
		    postdata['KODE_MESIN'] = $("#kode_ma").val() ; 
		    postdata['SATUAN_PRESTASI'] = $("#sat_unit_ma").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		    postdata['METER_PEMAKAIAN'] = data.METER_PEMAKAIAN; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		 	postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			
			$.post( url+'p_machine/create_ma', postdata,function(status) { 
		      	var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
						reloadGrid();
						alert('data berhasil tersimpan.')
		           };
		      } ); 
		}
		
		 function update(id) {
			
		    var postdata = {}; 
			var ids = jQuery("#list_ma").getGridParam('selrow'); 
			
		    var data = $("#list_ma").getRowData(ids) ; 
		       
		    postdata['KODE_MESIN'] = $("#kode_ma").val() ; 
		    postdata['SATUAN_PRESTASI'] = $("#sat_unit_ma").val() ; 
		    postdata['BULAN'] = $("#bulan").val();
		    postdata['TAHUN'] = $("#tahun").val(); 
		    postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
		    postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE; 
		    postdata['LOCATION_CODE'] = data.LOCATION_CODE; 
		    postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE; 
		  	postdata['METER_PEMAKAIAN'] = data.METER_PEMAKAIAN; 
		    postdata['JAM_KERJA'] = data.JAM_KERJA; 
		   	postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
			
			$.post( url+'p_machine/update_ma/'+id, postdata,function(status) { 
		      var status = new String(status);
			  if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
						reloadGrid();
						alert('data berhasil tersimpan.')
		           };
		      } ); 
		}
		
		function hapus(ids) {
		   var postdata = {}; 
		   $.post( url+'p_machine/delete/'+ids, postdata,function(message,status) { 
		       if(status !== 'success') { 
		            	alert('data untuk tanggal ini sudah terisi.'); 
		          } else { 
						reloadGrid();
						alert('data berhasil terhapus.')
		           };  
		      } );
		}	
			
	 function reloadGrid(){
		 var mc = document.getElementById("kode_ma").value; 	
		 var bln = document.getElementById("bulan").value;	
		 var thn = document.getElementById("tahun").value;
		 jQuery("#list_ma").setGridParam({url:url+'p_machine/grid_p_machine/'+mc+'/'+bln+'/'+thn}).trigger("reloadGrid");	
		 }
		 
		 
		 				
</script>

<form id="form_va" name="form_va">
<table class="teks_">
<tr><td>Kode Mesin</td><td>:</td><td><input type="text" style="text-transform: uppercase; font-size: 11px;" id="kode_ma" class="input"></td></tr>
<tr><td>Satuan Prestasi</td><td>:</td><td><input type="text" id="sat_unit_ma" style="text-transform: uppercase; font-size: 11px;" class="input_disable" disabled="true"></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode; ?></td></tr>
</table>

 <table id="list_ma" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_ma" class="scroll" style="text-align:center;"></div>

</form>

</body>
