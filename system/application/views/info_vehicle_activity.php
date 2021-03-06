<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){

$("#bulan").change(function() {
  gridReload();
});

$("#tahun").change(function() {
  gridReload();
});

$(function () {
	document.getElementById("kode_kend").value = "";
	document.getElementById("jns_kend").value = "";
	document.getElementById("no_pol").value = "";
	document.getElementById("sat_prestasi").value = "";
});

function gridReload(){
	var bln = $("#bulan").val();
	var thn = $("#tahun").val();
	var kend = $("#kode_kend").val();			
	   
	jQuery("#list_va").setGridParam({url:url+'p_vehicle_activity/grid_vehicle_activity/'+kend+'/'+bln+'/'+thn}).trigger("reloadGrid");}
         
$(function () {
		$("#kode_kend").autocomplete( url+"p_vehicle_activity/kode_kend/", {
			dataType: 'ajax', width:350, multiple: false, limit:20, parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                });
			}, formatItem: function(item) {
                 return (typeof(item) == 'object')?item.res_dl :'';
            }
          }).result(function(e, item) {
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

function giveAct(){        
    var ids = jQuery("#list_va").getGridParam('selrow'); 
    var rets = jQuery("#list_va").getRowData(ids); 
    var type = rets.ACTIVITY_CODE;
    return type;
} 
    
function giveLocCode(){        
    var ids = jQuery("#list_va").getGridParam('selrow'); 
    var rets = jQuery("#list_va").getRowData(ids); 
    var type = rets.LOCATION_CODE;
    return type;
}

function giveTdate(){
	var ids = jQuery("#list_va").getGridParam('selrow'); 
    var rets = jQuery("#list_va").getRowData(ids); 
    var type = rets.TGL_AKTIVITAS;
    return type;
}
        
var url = "<?= base_url().'index.php/' ?>";

var jGrid_va = null;
var colNamesT_va = new Array();
var colModelT_va = new Array();

colNamesT_va.push('no');
colModelT_va.push({name:'no_va',index:'no_va', sortable:false, resizable:true, editable: false,
				  hidden:true, width: 30, align:'center'});

colNamesT_va.push('id');
colModelT_va.push({name:'ID',index:'ID', editable: false,hidden:true, width: 30, align:'center'});
       

colNamesT_va.push('kode_kendaraan');
colModelT_va.push({name:'KODE_KENDARAAN',index:'KODE_KENDARAAN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Bulan');
colModelT_va.push({name:'BULAN',index:'BULAN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Tahun');
colModelT_va.push({name:'TAHUN',index:'TAHUN', editable: false,hidden:true, width: 70, align:'center'});

colNamesT_va.push('Tgl');
colModelT_va.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: true, hidden:false, width: 75, align:'center'});

/* colNamesT_va.push('Berangkat');
colModelT_va.push({name:'JAM_BERANGKAT',index:'JAM_BERANGKAT', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, editable: true,hidden:true, width: 80, align:'center'});       

colNamesT_va.push('Kembali');
colModelT_va.push({name:'JAM_KEMBALI',index:'JAM_KEMBALI', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 0}, editable: true,hidden:true, width: 80, align:'center'}); */       

colNamesT_va.push('Jam Kerja');
colModelT_va.push({name:'JAM_KERJA',index:'JAM_KERJA', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false, width: 70, align:'center'});       

colNamesT_va.push('km/hm berangkat');
colModelT_va.push({name:'KMHM_BERANGKAT',index:'KMHM_BERANGKAT', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false, width: 100, align:'center'}); 

colNamesT_va.push('km/hm kembali');
colModelT_va.push({name:'KMHM_KEMBALI',index:'KMHM_KEMBALI', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false, width: 100, align:'center'}); 

colNamesT_va.push('km/hm jumlah');
colModelT_va.push({name:'KMHM_JUMLAH',index:'KMHM_JUMLAH', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: false,hidden:true, width: 70, align:'center'}); 

colNamesT_va.push('Type');
colModelT_va.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true,
        edittype: "text", editoptions:{
                size:12,
                maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete(["IF":"IF2", "OP", "GC", "NS", "PJ", "SA", "VH", "MA"],{
                        	dataType: 'ajax',  width: 320, max: 5,
                       		highlight: false, multiple: false, scroll: true, scrollHeight: 300
                    }).result(function(e, item) {$("#LOCATION_TYPE_CODE").val(item.res_name );
                    var id = jQuery("#list_va").getGridParam('selrow');
                        if (id){ 
                            var ret = jQuery("#list_va").getRowData(id);
                            jQuery("#list_va").setRowData(id,{LOCATION_CODE:""});
                            jQuery("#list_va").setRowData(id,{ACTIVITY_CODE:""});
                        }
				});
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Lokasi');
colModelT_va.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true
,async: false,edittype: "text",editoptions:{
                size:64, maxlength:255, dataInit:function (elem) { 
                $(elem).autocomplete( 
                    url+"p_vehicle_activity/location/"+giveLocType(), {
                      dataType: 'ajax', multiple: false, autoFill: false,
                      mustMatch: true, matchContains: false,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_name } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl :'';
                      }
                    }
                  ).result(function(e, item) {$("#LOCATION_TYPE_CODE").val(item.res_name );
                     var id = jQuery("#list_va").getGridParam('selrow');
                        if (id){ 
                            var ret = jQuery("#list_va").getRowData(id);
                            jQuery("#list_va").setRowData(id,{ACTIVITY_CODE:""});
                        }					
                  });
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Aktivitas');
colModelT_va.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,
        editoptions:{
                size:64, maxlength:255,
                dataInit:function (elem) { // the moment of magic Wink
                $(elem).autocomplete(
                    url+"p_vehicle_activity/activity/"+giveLocType()+"/"+giveLocCode(), {
                      dataType: 'ajax', multiple: false,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_d, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: '', unit1:'', unit2:''};
                        });
                      },formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_d :'';
                      }
                    }
                  )
                  .result(function(e, item) {
                    $("#LOCATION_TYPE_CODE").val(item.res_name );
					var id = jQuery("#list_va").getGridParam('selrow');
                    if (id){ 
                        var ret = jQuery("#list_va").getRowData(id);
                        jQuery("#list_va").setRowData(id,{PRESTASI_SAT:item.res_sat1});
						jQuery("#list_va").setRowData(id,{PRESTASI_SAT2:item.res_sat2});
					}					
                  });
                  }}, width: 70, align:'center'}); 

colNamesT_va.push('Sub Aktivitas');
colModelT_va.push({name:'SUB_ACTIVITY_CODE',index:'SUB_ACTIVITY_CODE', editable: true,
        editoptions:{
                size:64, maxlength:255,
                dataInit:function (elem) { // the moment of magic Wink
                $(elem).autocomplete(
                    url+"p_vehicle_activity/subactivity/"+giveLocType()+"/"+giveLocCode()+"/"+giveAct(), {
                      dataType: 'ajax', multiple: false,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_d, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_d :'';
                      }
                    }
                  )
                  .result(function(e, item) {
                    $("#LOCATION_TYPE_CODE").val(item.res_name );
                  });
                  }}, width: 70, align:'center'}); 

colNamesT_va.push('Vol. Prestasi');
colModelT_va.push({name:'PRESTASI_VOL',index:'PRESTASI_VOL', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false,width: 90, align:'center'}); 

colNamesT_va.push('Sat. Prestasi');
colModelT_va.push({name:'PRESTASI_SAT',index:'PRESTASI_SAT', editable: false,hidden:false, 
editoptions:{
                size:12, maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_vehicle_activity/satuan/", {
                      dataType: 'ajax', multiple: false,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_name, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      }, formatItem: function(item) {
                        	return (typeof(item) == 'object')?item.res_name :'';
                      }
                    }
                  ).result(function(e, item) {
                    $("#HSL_KERJA_UNIT").val(item.res_id );
                  });
          }},width: 90, align:'center'}); 

colNamesT_va.push('Vol. Prestasi 2 ');
colModelT_va.push({name:'PRESTASI_VOL2',index:'PRESTASI_VOL2', editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false, width: 90, align:'center'}); 

colNamesT_va.push('Sat. Prestasi 2');
colModelT_va.push({name:'PRESTASI_SAT2',index:'PRESTASI_SAT2', editable: false,hidden:false, 
editoptions:{
                size:12, maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_vehicle_activity/satuan/", {
                      dataType: 'ajax', multiple: false,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_name, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      }, formatItem: function(item) {
                        	return (typeof(item) == 'object')?item.res_name :'';
                      }
                    }
                  ).result(function(e, item) {
                    $("#HSL_KERJA_UNIT").val(item.res_id );
                  });
          }},width: 90, align:'center'}); 


colNamesT_va.push('Jenis Muatan');
colModelT_va.push({name:'MUATAN_JENIS',index:'MUATAN_JENIS', editable: true,hidden:false, 
editoptions:{
                size:55, maxlength:200,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_vehicle_activity/muatan/", {
                      dataType: 'ajax', multiple: false, limit:50,
                      parse: function(data) { // parsing json input
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_name, result: row.res_id } // same in the serverside
                            : { data: row, value: '',result: ''};
                        });
                      },formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl :'';
                      }
                    }
                  ).result(function(e, item) {
                    $("#MUATAN_JENIS").val(item.res_id);
                    var id = jQuery("#list_va").getGridParam('selrow');
                    if (id){ 
                        var ret = jQuery("#list_va").getRowData(id);
                        jQuery("#list_va").setRowData(id,{MUATAN_SAT:item.res_sat});
					}
               });
          }}, width: 70, align:'center'}); 

colNamesT_va.push('Vol Muatan');
colModelT_va.push({name:'MUATAN_VOL',index:'MUATAN_VOL',editrules:{number:true}, formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", defaultValue: '', decimalPlaces: 2}, editable: true,hidden:false, width: 70, align:'center'}); 

colNamesT_va.push('Sat. Muatan');
colModelT_va.push({name:'MUATAN_SAT',index:'MUATAN_SAT', editable: true,edittype: "text", width: 70, align:'center'}); 
		  
colNamesT_va.push('');
colModelT_va.push({name:'action',index:'action', align:'center', resizable:false, sortable:false, editable: false,hidden:false, width: 30});
        
        var lastsel; var jdesc1; var lRow; var lCol; var i = 0;
		var loadView_va = function(){
        jGrid_va = jQuery("#list_va").jqGrid({
                url:url+'p_vehicle_activity/grid_vehicle_activity/xx/',
                mtype : "POST", datatype: "json", colNames: colNamesT_va , colModel: colModelT_va ,
                sortname: colNamesT_va[2], pager:jQuery("#pager_va"), rowNum: 400, rownumbers: true,
                height: 370, imgpath: gridimgpath, sortorder: "asc", cellEdit: true,
                cellsubmit: 'clientArray', forceFit : true, onCellSelect : function(iCol){},
                loadComplete: function(){ 
					var ids = jQuery("#list_va").getDataIDs(); 
					var id = jQuery("#list_va").getGridParam('selrow'); 
					var rets = jQuery("#list_va").getRowData(id); 
				 		
					for(var i=0;i<ids.length;i++){ 
							var cl = ids[i]; 
							be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
							ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"');\"/>";
							jQuery("#list_va").setRowData(ids[i],{action:be+ce}) 
						}
                }, 
                
                afterEditCell: function (id,name,val,iRow,iCol){             
					if(name=='TGL_AKTIVITAS') { 
						jQuery("#"+iRow+"_TGL_AKTIVITAS","#list_va").datepicker({dateFormat:"yy-mm-dd"});
					} 
					
					/* if(name=='JAM_BERANGKAT'){ 
						jQuery("#"+iRow+"_JAM_BERANGKAT","#list_va").datepicker({dateFormat:"yy-mm-dd",time24h: true,duration: '',showTime:true,constrainInput: true});
					}
					
					if(name=='JAM_KEMBALI'){ 
						jQuery("#"+iRow+"_JAM_KEMBALI","#list_va").datepicker({dateFormat:"yy-mm-dd",time24h: true,duration: '',showTime:true,constrainInput: true});
					}  */   
                } 
            });
         jGrid_va.navGrid('#pager_va',{edit:false,del:false,add:false, search: false, refresh: true});
         jGrid_va.navButtonAdd('#pager_va',{
               caption:"Tambah Baris", buttonicon:"ui-icon-add", 
               onClickButton: function(){ addrow() }, 
               position:"left"
          });
         //######## UPDATE 23 OKTOBER 2012 : RIDHU ####### //
		  jGrid_va.navButtonAdd('#pager_va',{
               caption:"Salin Baris", buttonicon:"ui-icon-add", 
               onClickButton: function(){ copyrow() }, 
               position:"left"
          });
         //######## UPDATE 15 Desember 2010 #########
         jGrid_va.navButtonAdd('#pager_va',{
               caption:"Export ke Excell", buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
                    exportToExcell()
               }, position:"left"
            });
         //######## END UPDATE 15 Desember 2010 #########    
         }
         jQuery("#list_va").ready(loadView_va);
         
         function post(i) 
         {
            //var confirmsg = confirm("Tambah data? ");
            //if(confirmsg) {
                var postdata = {}; 
                var ids = jQuery("#list_va").getGridParam('selrow'); 
                var data = $("#list_va").getRowData(ids) ;
                postdata['KODE_KENDARAAN'] = $("#kode_kend").val() ; 
                postdata['SATUAN_PRESTASI'] = $("#sat_prestasi").val() ; 
                postdata['BULAN'] = $("#bulan").val();
                postdata['TAHUN'] = $("#tahun").val(); 
                postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 

                //postdata['JAM_BERANGKAT'] = data.JAM_BERANGKAT.replace(" ",""); 
                //postdata['JAM_KEMBALI'] = data.JAM_KEMBALI.replace(" ",""); 
                postdata['JAM_KERJA'] = data.JAM_KERJA.replace(" ",""); 
                postdata['KMHM_BERANGKAT'] = data.KMHM_BERANGKAT.replace(" ",""); 
                postdata['KMHM_KEMBALI'] = data.KMHM_KEMBALI.replace(" ",""); 
                postdata['KMHM_JUMLAH'] = data.KMHM_JUMLAH; 
				/*if(data.LOCATION_TYPE_CODE == "-"){
					postdata['LOCATION_TYPE_CODE'] = "BD";
				} else { */
                postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE.replace(" ","");
				//}
                postdata['LOCATION_CODE'] = data.LOCATION_CODE.replace(" ",""); 
               	if(data.LOCATION_TYPE_CODE == "-"){
                	postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE;
		} else {
			postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE.replace(" ","");
		}

                postdata['SUB_ACTIVITY_CODE'] = data.SUB_ACTIVITY_CODE.replace(" ","");;
                postdata['MUATAN_JENIS'] = data.MUATAN_JENIS.replace(" ",""); 
                postdata['MUATAN_SAT'] = data.MUATAN_SAT.replace(" ",""); 
                postdata['MUATAN_VOL'] = data.MUATAN_VOL.replace(" ",""); 
                postdata['PRESTASI_VOL'] = data.PRESTASI_VOL.replace(" ",""); 
                postdata['PRESTASI_SAT'] = data.PRESTASI_SAT.replace(" ",""); 
		postdata['PRESTASI_VOL2'] = data.PRESTASI_VOL2.replace(" ",""); 
                postdata['PRESTASI_SAT2'] = data.PRESTASI_SAT2.replace(" ","");
                postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
                
                $.post( url+'p_vehicle_activity/create_va', postdata,function(status) { 
                        var status = new String(status);
                        if(status.replace(/\s/g,"") != "") {
                             /*if(status==0){
                                reloadGrid();
                             }else{
                                alert(status);    
                             }*/
                             alert(status); 
                        } else {
                            reloadGrid();
                        };
                  } ); 
            // } else {
            //    reloadGrid();    
            //}   
        }
        
        function update(id) { 
            //var confirmsg = confirm("Update data? ");
            //if(confirmsg) {
                var postdata = {}; 
                var ids = jQuery("#list_va").getGridParam('selrow'); 
                
                var data = $("#list_va").getRowData(ids) ; 
                postdata['KODE_KENDARAAN'] = $("#kode_kend").val() ; 
                postdata['SATUAN_PRESTASI'] = $("#sat_prestasi").val() ; 
                postdata['BULAN'] = $("#bulan").val();
                postdata['TAHUN'] = $("#tahun").val(); 
                postdata['TGL_AKTIVITAS'] = data.TGL_AKTIVITAS; 
                postdata['JAM_KERJA'] = data.JAM_KERJA.replace(" ",""); 
                postdata['KMHM_BERANGKAT'] = data.KMHM_BERANGKAT.replace(" ",""); 
                postdata['KMHM_KEMBALI'] = data.KMHM_KEMBALI.replace(" ",""); 
                postdata['KMHM_JUMLAH'] = data.KMHM_JUMLAH.replace(" ",""); 
                /* if(data.LOCATION_TYPE_CODE == "-"){
					postdata['LOCATION_TYPE_CODE'] = "BD";
				} else { */
                	postdata['LOCATION_TYPE_CODE'] = data.LOCATION_TYPE_CODE.replace(" ",""); 
				//}
                postdata['LOCATION_CODE'] = data.LOCATION_CODE.replace(" ",""); 
				if(data.LOCATION_TYPE_CODE == "-"){
                	postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE;
				} else {
					postdata['ACTIVITY_CODE'] = data.ACTIVITY_CODE.replace(" ","");
				}
				postdata['SUB_ACTIVITY_CODE'] = data.SUB_ACTIVITY_CODE.replace(" ","");;
                postdata['MUATAN_JENIS'] = data.MUATAN_JENIS.replace(" ",""); 
                postdata['MUATAN_SAT'] = data.MUATAN_SAT.replace(" ",""); 
                postdata['MUATAN_VOL'] = data.MUATAN_VOL.replace(" ",""); 
                postdata['PRESTASI_VOL'] = data.PRESTASI_VOL.replace(" ",""); 
                postdata['PRESTASI_SAT'] = data.PRESTASI_SAT.replace(" ",""); 
				postdata['PRESTASI_VOL2'] = data.PRESTASI_VOL2.replace(" ",""); 
                postdata['PRESTASI_SAT2'] = data.PRESTASI_SAT2.replace(" ",""); 
                postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
                
                $.post( url+'p_vehicle_activity/update_va/'+id, postdata,function(status) { 
                   var status = new String(status);
                      if(status.replace(/\s/g,"") != "") { 
                            if(status==1){
                                reloadGrid();
                             } else {
                                alert(status);    
                             }  
                      } else { 
                            reloadGrid();
                       };
                  } );     
            //}   
        }
                
        function hapus(ids) {
            var confirmsg = confirm("Hapus data kendaraan dengan kode -"+ids+" ?");
            if(confirmsg)
            {
			   var id = jQuery("#list_va").getGridParam('selrow'); 
			   var rets = jQuery("#list_va").getRowData(ids);
			  
               var postdata = {};
                postdata['TGL_AKTIVITAS'] = rets.TGL_AKTIVITAS.replace(" ","");
               $.post( url+'p_vehicle_activity/delete/'+ids+'/'+rets.TGL_AKTIVITAS, postdata,function(status) { 
			   		var status = new String(status);
                   if(status.replace(/\s/g,"") != "") {  
                            alert(status); 
                       } else { 
                            reloadGrid();
                            //alert('data berhasil terhapus.')
                       };  
                  } );
            }
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
            var rowCount = $("#list_va").getGridParam("reccount"); 
            var back=null; 
            if (gc != "")
            {
                var id = jQuery("#list_va").getGridParam('selrow');
                var dat = jQuery("#list_va").getRowData(id);

                i=i+1;    
                var datArr = {};
                if (i>1){
                    var datArr = {jns_kend:jdesc1};
                }
                
                sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />";
                //alert (rowCount);
                if(rowCount>=1)
                {  
                    var ids = jQuery("#list_va").getDataIDs(); 
                    var rets = jQuery("#list_va").getRowData(ids[rowCount-1]); 
                    var opt=1;
                    $.post( url+'p_vehicle_activity/get_latest_kmhm/'+gc+'/'+rets.TGL_AKTIVITAS+'/'+rets.LOCATION_TYPE_CODE+'/'+rets.LOCATION_CODE
                        +'/'+rets.ACTIVITY_CODE+'/'+opt, '',function(status) 
                    { 
                        var status = new String(status);
                        //back =status; 
                        var su=jQuery("#list_va").addRowData(i,datArr,'last');
                        var no=jQuery("#list_va").setRowData(i,{no_va:i});
                        var act=jQuery("#list_va").setRowData(i,{action:sv})
                        var kmback=jQuery("#list_va").setRowData(i,{KMHM_BERANGKAT:status.trim()});
                    });
                         
                }
                else if(rowCount==0 || rowCount==null){
                    var ids = jQuery("#list_va").getDataIDs(); 
                    var rets = jQuery("#list_va").getRowData(ids[rowCount-1]); 
                    var opt=2;
                     
                    $.post( url+'p_vehicle_activity/get_latest_kmhm/'+gc+'/'+$("#tahun").val()+$("#bulan").val()+'/'+'-'+'/'+'-'+'/'+'-'+'/'+opt, 
                    '',function(status) 
                    { 
                        var status = new String(status);
                        //back =status; 
                        var su=jQuery("#list_va").addRowData(i,datArr,'last');
                        var no=jQuery("#list_va").setRowData(i,{no_va:i});
                        var act=jQuery("#list_va").setRowData(i,{action:sv})
                        var kmback=jQuery("#list_va").setRowData(i,{KMHM_BERANGKAT:status.trim()});
                    }); 
                }
                  
            } else {
                alert('Pilih kode kendaraan terlebih dahulu!');                            
            }
        }
        
        function copyrow(){
            var gc = document.getElementById("kode_kend").value;
            var ids = jQuery("#list_va").getDataIDs();
            var i = ids.length;
			var ids = jQuery("#list_va").getGridParam('selrow'); 
            var data = $("#list_va").getRowData(ids) ;
			var tgl = data.TGL_AKTIVITAS; 
			//var brgkt = data.JAM_BERANGKAT.replace(" ",""); 
			//var kembali = data.JAM_KEMBALI.replace(" ","");
			var jk = data.JAM_KERJA.replace(" ",""); 
			var kmhmb = data.KMHM_BERANGKAT.replace(" ",""); 
			var kmhmk = data.KMHM_KEMBALI.replace(" ","");
			var kmhmj = data.KMHM_JUMLAH.replace(" ",""); var ltc = data.LOCATION_TYPE_CODE.replace(" ","");
			var lc = data.LOCATION_CODE.replace(" ",""); var act = data.ACTIVITY_CODE.replace(" ","");
			var mj = data.MUATAN_JENIS.replace(" ",""); var ms = data.MUATAN_SAT.replace(" ",""); 
			var mv = data.MUATAN_VOL.replace(" ","");  var pv = data.PRESTASI_VOL.replace(" ",""); 
			var ps = data.PRESTASI_SAT.replace(" ",""); var company = data.COMPANY_CODE;
			var pv2 = data.PRESTASI_VOL2.replace(" ",""); 
			var ps2 = data.PRESTASI_SAT2.replace(" ","");	
            var sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />";
			if (gc != ""){
                i=i+1;    
                var datArr = {};
                if (i>1){
                      var datArr = {GANG_CODE:jdesc1};
                }
				var status = "";
				var arr = [ tgl, jk, kmhmb, kmhmk, kmhmj, ltc, lc, act, mj, ms, mv, pv, ps, pv2, ps2, company];
				jQuery.each(arr, function() {
				  	if(this.length > 50){
						status = "mohon tutup kotak yang terbuka terlebih dahulu";
						alert(status)
					}
				});

				if(status == "") {
					var su=jQuery("#list_va").addRowData(i,datArr,"last");
					var sa=jQuery("#list_va").setRowData(i,{no:i});
					jQuery("#list_va").setRowData(i,{TGL_AKTIVITAS:tgl});
					//jQuery("#list_va").setRowData(i,{JAM_BERANGKAT:brgkt});
					//jQuery("#list_va").setRowData(i,{JAM_KEMBALI:kembali});
					jQuery("#list_va").setRowData(i,{JAM_KERJA:jk});
					jQuery("#list_va").setRowData(i,{KMHM_BERANGKAT:kmhmb});
					jQuery("#list_va").setRowData(i,{KMHM_KEMBALI:kmhmk});
					jQuery("#list_va").setRowData(i,{KMHM_JUMLAH:kmhmj});
					jQuery("#list_va").setRowData(i,{LOCATION_TYPE_CODE:ltc});
					jQuery("#list_va").setRowData(i,{LOCATION_CODE:lc});
					jQuery("#list_va").setRowData(i,{ACTIVITY_CODE:act});
					jQuery("#list_va").setRowData(i,{MUATAN_JENIS:mj});
					jQuery("#list_va").setRowData(i,{MUATAN_SAT:ms});
					jQuery("#list_va").setRowData(i,{MUATAN_VOL:mv});
					jQuery("#list_va").setRowData(i,{PRESTASI_VOL:pv});
					jQuery("#list_va").setRowData(i,{PRESTASI_SAT:ps});
					jQuery("#list_va").setRowData(i,{PRESTASI_VOL2:pv2});
					jQuery("#list_va").setRowData(i,{PRESTASI_SAT2:ps2});
					jQuery("#list_va").setRowData(i,{COMPANY_CODE:company});
					jQuery("#list_va").setRowData(i,{action:sv})
				}
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
<script type="text/javascript">
function exportToExcell()
{
    var vc = $("#kode_kend").val();     
    var bln = $("#bulan").val();    
    var thn = $("#tahun").val();
    if(vc==null || vc=='')
    {
        alert("Harap pilih kode kendaraan terlebih dahulu");
    }else{
         window.location = url+'p_vehicle_activity/create_excel/'+ vc + '/' + bln + '/' + thn;     
    }
   
}
</script>
<form id="form_va" name="form_va" action="">
<table class="teks_">
<tr>
<td>Kode Kendaraan</td><td>:</td><td><input type="text" style="text-transform: uppercase; font-size: 11px;" id="kode_kend" name="kode_kend" class="input" /></td>
<td width="20px"></td>
<td>Jenis kendaraan</td><td>:</td><td><input type="text" style="width:200px;" class="input_disable" id="jns_kend" disabled="true"></td></tr>
<tr><td>Nomor Polisi</td><td>:</td><td><input type="text" class="input_disable" id="no_pol" disabled="true"></td><td></td><td>Satuan Unit</td><td>:</td><td><input type="text" id="sat_prestasi" style="text-transform: uppercase; font-size: 11px;" class="input_disable" disabled="true"></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode; ?></td><td></td><td></td><td></td><td></td></tr>
</table>

<table id="list_va" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_va" class="scroll" style="text-align:center;"></div><br/>
</form>


</body>
