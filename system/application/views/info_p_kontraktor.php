<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';

jQuery(document).ready(function(){
    //document.getElementById("kode_kontraktor").value = "";
    //document.getElementById("nama_kontraktor").value = ""; 
	//$("#chk_istbs").attr('checked',false);
	
	reloads();
	
	$("#MAT_TGL_ACTIVITY").datepicker({dateFormat:"yy-mm-dd"});
	 /* dialog input material */
	$("#formi_material").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 310,
                width: 720, modal: true, title : 'Entry Data Pemakaian Material Kontraktor', 
				buttons: {
					Tutup: function()  {	
						initmaterial();  
						$("#formi_material").dialog('close'); 
					},
					Simpan: function() {	
						submitmaterial();	
						}     
				}
	}); 
	
	/* dialog form grid material */
	$("#formg_material").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 490,
                width: 980, modal: true, title : 'Entry Data Pemakaian Material Kontraktor'
	}); 
	/* end form grid material */
	
	/* dialog progress */	
	$("#progressbar").dialog({
                bgiframe: true, autoOpen: false,
				resizable: true, draggable: true,
				closeOnEscape:false, height: 160,
                width: 220, modal: true
	}); 
	/* end dialog */
	
	/* end dialog input material */   
   
    $("#kode_kontraktor").autocomplete( url+"p_kontraktor/load_kode_kontraktor/", {
          dataType: 'ajax',
          width:400,
          multiple: false,
          limit:20,
          parse: function(data) { 
              return $.map(eval(data), function(row) {
              return (typeof(row) == 'object')
                ? { data: row, value: row.res_id, result: row.res_id }
                : { data: row, value: '',result: ''};
            });
          },
          formatItem: function(item) {
            return (typeof(item) == 'object')?item.res_dl:'';
          }
        }
      ).result(function(e, item){    
	  	  var bln = $("#bulan").val();
          var thn = $("#tahun").val();
          var jumlah = {};
          var postdata = {};
          var id=item.res_id;
          var name =item.res_name;
		  var istbs = item.isktbs;
		  
		  if (istbs==1) {
            $("#chk_istbs").attr('checked',true);
          } else {
            $("#chk_istbs").attr('checked',false);    
          }
          $("#kode_kontraktor").val(id);
          $("#nama_kontraktor").val(name);        
         reloads()
      });
	  
	  
	  function reloads(){
		 
	  	  var bln =  document.getElementById("bulan").value;
          var thn =  document.getElementById("tahun").value;
		  var id = document.getElementById("kode_kontraktor").value;
          jQuery("#list_kontraktor").setGridParam({url:url+'p_kontraktor/load_data/'+id+'/'+thn+bln}).trigger("reloadGrid"); 
	  }
	  
	 
});

var url = "<?= base_url().'index.php/' ?>";

var jGrid_kontraktor = null;
var colNamesT_kontraktor = new Array();
var colModelT_kontraktor = new Array();
var gridimgpath = '<?= $template_path ?>themes/basic/images'; 

colNamesT_kontraktor.push('no');
colModelT_kontraktor.push({name:'no_ma',index:'no_ma', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});
                                                           
colNamesT_kontraktor.push('ID_KONTRAK');
colModelT_kontraktor.push({name:'ID_KONTRAK',index:'ID_KONTRAK', editable: false, hidden:true, width: 20, align:'center'});

colNamesT_kontraktor.push('Tgl');
colModelT_kontraktor.push({name:'TGL_KONTRAK',index:'TGL_KONTRAK', editable: true, hidden:false, width: 70, align:'center'});

colNamesT_kontraktor.push('No Kendaraan');
colModelT_kontraktor.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: true, hidden:false, width: 90, align:'center',editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                
                $(elem).autocomplete( 
                      url+"p_kontraktor/vehicle/"+$("#kode_kontraktor").val()+"/", {
                      dataType: 'ajax',
                      multiple: false,
                      autoFill: false,
                      mustMatch: true,
                      matchContains: false,
                
                      parse: function(data) {
                          return $.map(eval(data), function(row) {
                          return (typeof(row) == 'object')
                            ? { data: row, value: row.res_id, result: row.res_name } 
                            : { data: row, value: '',result: ''};
                        });
                      },
                      formatItem: function(item) {
                        return (typeof(item) == 'object')?item.res_dl :'';
                      }
                    }
                  ).result(function(e, item) {
              });
          }}, align:'center'});

colNamesT_kontraktor.push('KODE_KONTRAKTOR');
colModelT_kontraktor.push({name:'KODE_KONTRAKTOR',index:'KODE_KONTRAKTOR', editable: false, hidden:true, width: 20, align:'center'});

colNamesT_kontraktor.push('Tipe');
colModelT_kontraktor.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: true, hidden:false, align:'center', editoptions:{
                size:12,
                maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete(["IF", "OP", "GC", "NS","PJ"], {
                    dataType: 'ajax',
                        width: 50,
                        max: 5,
                        highlight: false,
                        multiple: false,
                        scroll: true,
                        scrollHeight: 300
                    } 
                  )
                 
          }}, width: 50, align:'center'});

colNamesT_kontraktor.push('Lokasi');
colModelT_kontraktor.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true, hidden:false, editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { 
                $(elem).autocomplete( 
                      url+"p_kontraktor/location/"+giveLocType(), {
                      dataType: 'ajax', multiple: false,
                      autoFill: false, mustMatch: true,
                      matchContains: false,
                
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
                  .result(function(e, item) {
                    $("#LOCATION_CODE").val(item.res_id);
                    var id = jQuery("#list_kontraktor").getGridParam('selrow');
                    if (id){ 
                        var ret = jQuery("#list_kontraktor").getRowData(id);
                        ret.LOCATION_DESC = (item.res_name);
                        jQuery("#list_kontraktor").setRowData(id,{LOCATION_DESC:ret.LOCATION_DESC});
                    }
                  });
          }}, width: 100, align:'center'});

colNamesT_kontraktor.push('LOCATION_DESC');
colModelT_kontraktor.push({name:'LOCATION_DESC',index:'LOCATION_DESC', editable: false, hidden:true, width: 10, align:'left'});

colNamesT_kontraktor.push('Kode Aktivitas');
colModelT_kontraktor.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true, hidden:false,editoptions:{
                size:64,
                maxlength:255,
                dataInit:function (elem) { // the moment of magic Wink
                $(elem)
                  .autocomplete( // for more info check the autocomplete plugin docs
				  url+"p_kontraktor/activity/"+giveLocType()+"/"+giveLocCode(), {
                   // url+"p_kontraktor/activity/"+giveLocType(), {
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
                    $("#ACTIVITY_CODE").val(item.res_id );
                    var id = jQuery("#list_kontraktor").getGridParam('selrow');
                    if (id) 
                    { 
                        var ret = jQuery("#list_kontraktor").getRowData(id);
                        ret.ACTIVITY_DESC = (item.res_name);
                        jQuery("#list_kontraktor").setRowData(id,{ACTIVITY_DESC:ret.ACTIVITY_DESC});
						jQuery("#list_kontraktor").setRowData(id,{HSL_SATUAN:item.res_sat1});
						jQuery("#list_kontraktor").setRowData(id,{HSL_SATUAN2:item.res_sat2});
                    }
                  });
                  }}, width: 90, align:'center'});

colNamesT_kontraktor.push('Deskripsi');
colModelT_kontraktor.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false, hidden:true, width: 10, align:'center'});

colNamesT_kontraktor.push('Muatan');
colModelT_kontraktor.push({name:'MUATAN',index:'MUATAN', editable: true, hidden:false, editoptions:{
                size:55, maxlength:200,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_kontraktor/muatan/", {
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
                    $("#MUATAN").val(item.res_id);
               });
          }}, width: 60, align:'center'});

colNamesT_kontraktor.push('Jarak');
colModelT_kontraktor.push({name:'JARAK',index:'JARAK', editable: true, hidden:false, editoptions:{
                size:12,
                maxlength:25}, width: 60, align:'center'});

colNamesT_kontraktor.push('Satuan');
colModelT_kontraktor.push({name:'HSL_SATUAN',index:'HSL_SATUAN', editable: true, hidden:false, editoptions:{
                size:12, maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_kontraktor/satuan/", {
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
                    $("#HSL_SATUAN").val(item.res_id );
                  });
          }}, width: 60, align:'center'});

colNamesT_kontraktor.push('Hasil');
colModelT_kontraktor.push({name:'HSL_VOLUME',index:'HSL_VOLUME', formatter:'number', formatoptions:{decimalPlaces: 2, decimalSeparator:".", thousandsSeparator: ","}, editable: true, hidden:false, width: 70, align:'right'});

colNamesT_kontraktor.push('Satuan 2');
colModelT_kontraktor.push({name:'HSL_SATUAN2',index:'HSL_SATUAN2', editable: true, hidden:false, editoptions:{
                size:12, maxlength:40,
                dataInit:function (elem) {
                $(elem).autocomplete( 
                    url+"p_kontraktor/satuan/", {
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
                    $("#HSL_SATUAN2").val(item.res_id );
                  });
          }}, width: 70, align:'center'});

colNamesT_kontraktor.push('Hasil 2');
colModelT_kontraktor.push({name:'HSL_VOLUME2',index:'HSL_VOLUME2', formatter:'number', formatoptions:{decimalPlaces: 2, decimalSeparator:".", thousandsSeparator: ","}, editable: true, hidden:false, width: 70, align:'right'});

colNamesT_kontraktor.push('Tarif / Satuan');
colModelT_kontraktor.push({name:'TARIF_SATUAN',index:'TARIF_SATUAN', formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ","}, editable: true, hidden:false,editoptions:{
                size:12,
                maxlength:10}, width: 90, align:'right'});

colNamesT_kontraktor.push('Nilai');
colModelT_kontraktor.push({name:'NILAI',index:'NILAI', formatter:'number', formatoptions:{decimalSeparator:".", thousandsSeparator: ","}, editable: false, hidden:false,editoptions:{
                size:12,
                maxlength:10}, width: 80, align:'right'});

colNamesT_kontraktor.push('');
colModelT_kontraktor.push({name:'action',index:'action', editable: false, hidden:false, width: 45, align:'left'});

var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;   
var loadView_kontraktor = function()
        {
            jGrid_kontraktor = jQuery("#list_kontraktor").jqGrid(
            {
                url:url+'p_kontraktor/load_data/xx',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_kontraktor ,
                colModel: colModelT_kontraktor , 
                sortname: colNamesT_kontraktor[2].name,
                pager:jQuery("#pager_kontraktor"),
                rowNum: 40, 
                rownumbers: true,
                height: 370, width: 1060, 
                sortorder: "asc",
                cellEdit: true,
                cellsubmit: 'clientArray',
                forceFit : false,
                loadComplete: function(){ 
					var isActive = $("#chk_istbs").is(':checked');
					if(isActive == true){
						jQuery("#list_kontraktor").showCol("NO_KENDARAAN");
						jQuery("#list_kontraktor").showCol("MUATAN");
						jQuery("#list_kontraktor").showCol("JARAK");
						jQuery("#list_kontraktor").setGridWidth(1060, true);
					} else {
						jQuery("#list_kontraktor").hideCol("NO_KENDARAAN");
						jQuery("#list_kontraktor").hideCol("MUATAN");
						jQuery("#list_kontraktor").hideCol("JARAK");
						jQuery("#list_kontraktor").setGridWidth(960, true);
					}
									
                    var ids = jQuery("#list_kontraktor").getDataIDs(); 
                    for(var i=0;i<ids.length;i++)
                        { 
                            var cl = ids[i];
                            var rets = jQuery("#list_kontraktor").getRowData(cl);
                            var kd=rets.KODE_KONTRAKTOR;
                             
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus('"+cl+"','"+kd+"');\"/>";
                            jQuery("#list_kontraktor").setRowData(ids[i],{action:be+ce}) 
                        }
                                            
                    },
                afterEditCell: function (id,name,val,iRow,iCol)
                    {             
                     if(name=='TGL_KONTRAK') 
                        { jQuery("#"+iRow+"_TGL_KONTRAK","#list_kontraktor").datepicker({dateFormat:"yy-mm-dd"}); } 
                    },
                imgpath: gridimgpath,
                pager: jQuery('#pager_kontraktor'),
                sortname: colNamesT_kontraktor[0]
                
                
            });
            jGrid_kontraktor.navGrid('#pager_kontraktor',{edit:false,add:false,del:false, search: false, refresh: true});
            
            jGrid_kontraktor.navButtonAdd('#pager_kontraktor',{
               caption:"Tambah Data", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
                        addrow()
               }, 
               position:"last"
            }); 
			
			jGrid_kontraktor.navButtonAdd('#pager_kontraktor',{
               caption:"Salin Data", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
                        copyrow();
               }, 
               position:"last"
            });
			
			jGrid_kontraktor.navButtonAdd('#pager_kontraktor',{
               caption:"Transaksi Material", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){ 
                        smaterial();
               }, 
               position:"last"
            });            
        }
        
        jQuery("#list_kontraktor").ready(loadView_kontraktor);

/* close progress bar */
	function closewin(){
			$("#progressbar").dialog('close');
	}
	/* end close progress bar */


function addrow()
{
    var kc = document.getElementById("kode_kontraktor").value;
    var i = jQuery('#list_kontraktor').getGridParam('records');
    var ids = jQuery("#list_kontraktor").getGridParam('selrow');
                           
    if (kc != "")
    {
        var id = jQuery("#list_kontraktor").getGridParam('selrow');
        var dat = jQuery("#list_kontraktor").getRowData(id);
            
        i=i+1;    
        var datArr = {};
        if (i>1){
            var datArr = {kode_kontraktor:jdesc1};
        }

        sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 

        var su=jQuery("#list_kontraktor").addRowData(i,datArr,'last');
        var no=jQuery("#list_kontraktor").setRowData(i,{no_ma:i});    
        var act=jQuery("#list_kontraktor").setRowData(i,{action:sv})
        var act=jQuery("#list_kontraktor").setRowData(i,{KODE_KONTRAKTOR:kc}) 
    } else {
        alert('Pilih kode kontraktor terlebih dahulu!');                            
    }
} 

function copyrow(){
    var kk = document.getElementById("kode_kontraktor").value;
    var ids = jQuery("#list_kontraktor").getDataIDs();
    var i = ids.length;
			
    var ids = jQuery("#list_kontraktor").getGridParam('selrow'); 
    var data = $("#list_kontraktor").getRowData(ids) ;
    var kode_kontraktor = data.ID_KONTRAKTOR; 
	var tgl = data.TGL_KONTRAK; 
	var no_kendaraan = data.NO_KENDARAAN;
	var ltc = data.LOCATION_TYPE_CODE; 
	var lc = data.LOCATION_CODE;
	var lcd = data.LOCATION_DESC;
	var ac = data.ACTIVITY_CODE;
	var acd = data.ACTIVITY_DESC;  
	var muatan = data.MUATAN;
	var jarak = data.JARAK; 
	var satuan = data.HSL_SATUAN;
	var volume = data.HSL_VOLUME; 
	var satuan2 = data.HSL_SATUAN2;
	var volume2 = data.HSL_VOLUME2; 
	var tarif = data.TARIF_SATUAN;
	var nilai = data.NILAI; 
	var sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='post("+i+")'; />"; 
	
    if (kk != ""){
      	i=i+1;    
        var datArr = {};
        if (i>1){
                var datArr = {ID_KONTRAKTOR:jdesc1};
        }
		var status = "";
		var arr = [ kode_kontraktor, tgl, no_kendaraan, ltc, lc,lcd, ac ,acd, muatan, jarak, satuan, volume, tarif, nilai];
		jQuery.each(arr, function() {
		if(this.length > 60){
			status = "mohon tutup kotak yang terbuka terlebih dahulu";
			alert(status)
		}
	});

	if(status == "") {
			var su=jQuery("#list_kontraktor").addRowData(i,datArr,"last");
			var sa=jQuery("#list_kontraktor").setRowData(i,{no:i});
			jQuery("#list_kontraktor").setRowData(i,{ID_KONTRAKTOR:kode_kontraktor});
			jQuery("#list_kontraktor").setRowData(i,{TGL_KONTRAK:tgl});
			jQuery("#list_kontraktor").setRowData(i,{NO_KENDARAAN:no_kendaraan});
			jQuery("#list_kontraktor").setRowData(i,{LOCATION_TYPE_CODE:ltc});
			jQuery("#list_kontraktor").setRowData(i,{LOCATION_CODE:lc});
			jQuery("#list_kontraktor").setRowData(i,{LOCATION_DESC:lcd});
			jQuery("#list_kontraktor").setRowData(i,{ACTIVITY_CODE:ac});
			jQuery("#list_kontraktor").setRowData(i,{ACTIVITY_DESC:acd});
			jQuery("#list_kontraktor").setRowData(i,{MUATAN:muatan});
			jQuery("#list_kontraktor").setRowData(i,{JARAK:jarak});
			jQuery("#list_kontraktor").setRowData(i,{HSL_SATUAN:satuan});
			jQuery("#list_kontraktor").setRowData(i,{HSL_VOLUME:volume});
			jQuery("#list_kontraktor").setRowData(i,{HSL_SATUAN2:satua2n});
			jQuery("#list_kontraktor").setRowData(i,{HSL_VOLUME2:volume2});
			jQuery("#list_kontraktor").setRowData(i,{TARIF_SATUAN:tarif});
			jQuery("#list_kontraktor").setRowData(i,{NILAI:nilai});
			jQuery("#list_kontraktor").setRowData(i,{action:sv});
		}
   } else {
      alert('kode kontraktor tidak boleh kosong!');                            
   }
}
		
function giveLocType(){        
        var ids = jQuery("#list_kontraktor").getGridParam('selrow'); 
        var rets = jQuery("#list_kontraktor").getRowData(ids); 
        var type = rets.LOCATION_TYPE_CODE;
        return type;
}

function giveLocCode(){        
        var ids = jQuery("#list_kontraktor").getGridParam('selrow'); 
        var rets = jQuery("#list_kontraktor").getRowData(ids); 
        var type = rets.LOCATION_CODE;
        return type;
}

function post(i) 
{         
    var postdata = {}; 
    var ids = jQuery("#list_kontraktor").getGridParam('selrow'); 
    var data = $("#list_kontraktor").getRowData(ids) ; 
    
    //var confirmsg = confirm("Tambah data? ");
    //if(confirmsg)
    //{
        postdata['ID_KONTRAK']=data.ID_KONTRAK;
        postdata['TGL_KONTRAK'] =data.TGL_KONTRAK; 
        postdata['NO_KENDARAAN'] =data.NO_KENDARAAN; 
        postdata['KODE_KONTRAKTOR']=data.KODE_KONTRAKTOR;  
        postdata['LOCATION_TYPE_CODE'] =data.LOCATION_TYPE_CODE;  
        postdata['LOCATION_CODE']=data.LOCATION_CODE;  
        postdata['LOCATION_DESC']=data.LOCATION_DESC;  
        postdata['ACTIVITY_CODE']=data.ACTIVITY_CODE;  
        postdata['ACTIVITY_DESC']=data.ACTIVITY_DESC;  
		postdata['MUATAN']=data.MUATAN;
		postdata['JARAK']=data.JARAK;
        postdata['HSL_SATUAN']=data.HSL_SATUAN;  
        postdata['HSL_VOLUME']=data.HSL_VOLUME;  
		postdata['HSL_SATUAN2']=data.HSL_SATUAN2;  
        postdata['HSL_VOLUME2']=data.HSL_VOLUME2;  
        postdata['TARIF_SATUAN']=data.TARIF_SATUAN;  
        postdata['NILAI']=data.NILAI;  
                
        $.post( url+'p_kontraktor/create', postdata,function(status) { 
              var status = new String(status);
               if(status.replace(/\s/g,"") != "") { 
                     if(status==0){
                        reloadGrid();
                        alert('data berhasil tersimpan.')    
                     }else{
                        //reloadGrid();
                        alert(status);    
                     }
                      
              } else { 
                    reloadGrid();
                    alert('data berhasil tersimpan.')
               };
          } );     
    //}else{
    //    reloadGrid();   
    //}   
    
}

function update(id) 
{    
    var postdata = {}; 
    var ids = jQuery("#list_kontraktor").getGridParam('selrow'); 
    var data = $("#list_kontraktor").getRowData(ids) ; 
    
    var confirmsg = confirm("Update data kontraktor dengan id: "+id+" ?");
    if(confirmsg)
    {
        postdata['ID_KONTRAK']=data.ID_KONTRAK;
        postdata['TGL_KONTRAK'] =data.TGL_KONTRAK;  
        postdata['NO_KENDARAAN'] =data.NO_KENDARAAN;
        postdata['KODE_KONTRAKTOR']=data.KODE_KONTRAKTOR;  
        postdata['LOCATION_TYPE_CODE'] =data.LOCATION_TYPE_CODE;  
        postdata['LOCATION_CODE']=data.LOCATION_CODE;  
        postdata['LOCATION_DESC']=data.LOCATION_DESC;  
        postdata['ACTIVITY_CODE']=data.ACTIVITY_CODE;  
        postdata['ACTIVITY_DESC']=data.ACTIVITY_DESC; 
		postdata['MUATAN']=data.MUATAN;
		postdata['JARAK']=data.JARAK; 
        postdata['HSL_SATUAN']=data.HSL_SATUAN;  
        postdata['HSL_VOLUME']=data.HSL_VOLUME;  
	 	postdata['HSL_SATUAN2']=data.HSL_SATUAN2;  
        postdata['HSL_VOLUME2']=data.HSL_VOLUME2;  
        postdata['TARIF_SATUAN']=data.TARIF_SATUAN;  
        postdata['NILAI']=data.NILAI;  
        postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>'; 
        
        $.post( url+'p_kontraktor/update/'+id, postdata,function(status) { 
          var status = new String(status);
          if(status.replace(/\s/g,"") != "") { 
                     alert(status); 
              } else { 
                    reloadGrid();
                    alert('data berhasil terupdate.')
               };
          } );     
    }     
}

function hapus(ids,kode) 
{
    var confirmsg = confirm("Delete data kontraktor dengan id: "+ids+" ?");
    if(confirmsg)
    {
        var postdata = {}; 
        $.post( url+'p_kontraktor/delete/'+ids+'/'+kode, postdata,function(message,status) { 
           if(status !== 'success') { 
                    alert('data untuk tanggal ini sudah terisi.'); 
              } else { 
                    reloadGrid();
                    alert('data berhasil terhapus.')
               };  
          } );    
    } 
}    
    
function reloadGrid()
{
     var mc = document.getElementById("kode_kontraktor").value;     
     var bln = document.getElementById("bulan").value;    
     var thn = document.getElementById("tahun").value;
     jQuery("#list_kontraktor").setGridParam({url:url+'p_kontraktor/load_data/'+mc+'/'+thn+bln}).trigger("reloadGrid");    
 }
 
 /* ### Material ### */
function smaterial() {
	var vperiode = document.getElementById("tahun").value + document.getElementById("bulan").value;
	var mc = document.getElementById("kode_kontraktor").value;
	
	if(mc == "" || vperiode == "") {
		alert('silakan pilih kode kontraktor terlebih dahulu..');
	} else {
		reload_material();
		$('#formg_material').dialog('open');
	}
}

/* ##################### MATERIAL GRID ######################### */ 
	var jGrid_material = null;
    var colNamesT_material = new Array();
    var colModelT_material = new Array();    
                                                                     
	colNamesT_material.push('BKT_MATERIAL_ID');
	colModelT_material.push({name:'BKT_MATERIAL_ID',index:'BKT_MATERIAL_ID',editable: false,hidden:true,width:90,align:'center'});
	 
	colNamesT_material.push('Kode Kontraktor');
	colModelT_material.push({name:'KODE_KONTRAKTOR',index:'KODE_KONTRAKTOR',editable: false,hidden:true,width: 200,align:'left'});
	 
	colNamesT_material.push('Tgl Aktivitas');
	colModelT_material.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS',editable:false,hidden:true,width:90,align:'center'});
	
	colNamesT_material.push('No SKB');
	colModelT_material.push({name:'MATERIAL_SKB_NO',index:'MATERIAL_SKB_NO',editable:false, hidden:false,width:80,align:'center'});
	
	colNamesT_material.push('No BPB');
	colModelT_material.push({name:'MATERIAL_BPB_NO',index:'MATERIAL_BPB_NO',editable:false,hidden:false,
								width:80,align:'left'});
	
	colNamesT_material.push('Kode Material');
	colModelT_material.push({name:'MATERIAL_CODE',index:'MATERIAL_CODE',editable:false,hidden:false,
								width:110,align:'left'});
								
	colNamesT_material.push('Deskripsi');
	colModelT_material.push({name:'MATERIAL_NAME',index:'MATERIAL_NAME',editable:false,hidden:false,
								width:190,align:'left'});								
								
	colNamesT_material.push('Qty');
	colModelT_material.push({name:'MATERIAL_QTY',index:'MATERIAL_QTY', editable: false, editrules:{number:true}, 
								hidden:false, width: 70, align:'center'}); 
	
	colNamesT_material.push('Satuan');
	colModelT_material.push({name:'MATERIAL_UOM',index:'MATERIAL_UOM', editable:false, 
								hidden:false, width: 70, align:'center'});
	
	/* colNamesT_material.push('Type Lokasi');
	colModelT_material.push({name:'LOCATION_TYPE_CODE',index:'LOCATION_TYPE_CODE', editable: false, width: 80, align:'center'}); */
			
	colNamesT_material.push('Lokasi');
	colModelT_material.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, width: 120, align:'center'});
	
	colNamesT_material.push('Kode Aktivitas');
	colModelT_material.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, 
								hidden:false, width: 100, align:'center'});
	
	colNamesT_material.push('COMPANY_CODE');
	colModelT_material.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, 
								hidden:true, width: 80, align:'right'});    
		
		
	var loadView_material = function(){
	var gc = jQuery("#search_gc").val(); 
	if (gc == ""){ gc = "-"; }
	jGrid_material = jQuery("#list_material").jqGrid({
		url:url+'p_kontraktor/read_material/xx/xx',
		mtype : "POST",  datatype: "json", rownumbers: true, rowNum: 40, height: 320,
		imgpath: gridimgpath, pager:jQuery("#pager_lhm"), sortorder: "asc", rownumbers:true,  viewrecords: true,
		forceFit : true,  colNames: colNamesT_material, colModel: colModelT_material , sortname: colModelT_material[2].name,
		rowList:[10,20,30], multiple:false,
		loadComplete: function(){ }, 
		imgpath: gridimgpath, pager: jQuery('#pager_material'), sortname: 'KODE_KONTRAKTOR'});
		jGrid_material.navGrid('#pager_material',{edit:false,add:false,del:false, search: false, refresh: true});
	}
	jQuery("#list_material").ready(loadView_material);

 	// ##################### END MATERIAL GRID ########################### 	
	function reload_material(){
		 var vperiode = document.getElementById("tahun").value + document.getElementById("bulan").value;
		 var mc = document.getElementById("kode_kontraktor").value;
		 jQuery("#list_material").setGridParam({url:url+"p_kontraktor/read_material/"+vperiode+"/"+mc}).trigger("reloadGrid");
	}
		 
	function getActMaterial(){
		var gc = document.getElementById("kode_kontraktor").value;
		var tdate = document.getElementById("MAT_TGL_ACTIVITY").value;
		var tgl = tdate.replace(/-/gi, "");
		$("#MAT_ACTIVITY").autocomplete( url+"p_kontraktor/getActMaterial/"+gc+"/"+tgl, {  dataType: 'ajax',
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
		var gc = document.getElementById("kode_kontraktor").value;
		var act = document.getElementById("MAT_ACTIVITY").value;
		var tdate = document.getElementById("MAT_TGL_ACTIVITY").value;
		var tgl = tdate.replace(/-/gi, "");
		$("#MAT_LOCATION").autocomplete( url+"p_kontraktor/getLocMaterial/"+gc+"/"+tgl+"/"+act, {  dataType: 'ajax',
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
		
		$("#MAT_MATERIAL").autocomplete( url+"p_kontraktor/getMaterial/", {  dataType: 'ajax',
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
		var gc = document.getElementById("kode_kontraktor").value;
	
        if (gc != ""){
			$("#MAT_KD_KONTRAKTOR").val( $("#kode_kontraktor").val() ) ;
			$("#MAT_NM_KONTRAKTOR").val( $("#nama_kontraktor").val() );
            $('#formi_material').dialog('open');
        } else {
            alert('kode kontraktor tidak boleh kosong!');                            
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
			var gc = document.getElementById("kode_kontraktor").value;
			if (gc != ""){
				$("#BKT_MATERIAL_ID").val(data.BKT_MATERIAL_ID); 
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
		postdata['BKT_MATERIAL_ID'] = $("#BKT_MATERIAL_ID").val() ;
		postdata['KODE_KONTRAKTOR'] = $("#kode_kontraktor").val() ; 
		postdata['TGL_AKTIVITAS'] = $("#MAT_TGL_ACTIVITY").val() ; 
		postdata['ACTIVITY_CODE'] = $("#MAT_ACTIVITY").val() ; 
		postdata['LOCATION_CODE']= $("#MAT_LOCATION").val();
		postdata['MATERIAL_QUANTITY']= $("#MAT_QTY").val();
		postdata['MATERIAL_CODE'] = $("#MAT_MATERIAL").val(); 
		postdata['MATERIAL_BPB_NO']= $("#MAT_BPB").val();
		postdata['MATERIAL_SKB_NO']= $("#MAT_SKB").val();
			
		if( $("#kode_kontraktor").val() == "" 
			|| $("#MAT_TGL_ACTIVITY").val() == "" 
			|| $("#MAT_ACTIVITY").val() == "" 
			|| $("#MAT_LOCATION").val() == ""
			|| $("#MAT_QTY").val() == "" )
		{
			alert('Data masih kosong, mohon diisi terlebih dahulu!');
		} else {
			$("#cok").attr("disabled", true);
			$("#load").show();
			document.getElementById('msg').innerHTML= "Mohon menunggu... Proses penyimpanan data...";
			$("#progressbar").dialog('open');
			$.post( url+"p_kontraktor/submit_material/", 
			postdata, function(message) {
				if(message.replace(/\s/g,"") != 0 ) { 
					alert(message);
					$("#progressbar").dialog('close');
					$('#formi_material').dialog('close');
					$('#formi_material').dialog('open');
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
	  postdata['BKT_MATERIAL_ID'] = data.BKT_MATERIAL_ID ;
	  postdata['KODE_KONTRAKTOR'] = data.KODE_KONTRAKTOR; 
	  postdata['MAT_TGL_ACTIVITY'] = data.TGL_AKTIVITAS; 
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
				
				$.post(url+"p_kontraktor/delete_material", postdata,
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
		$("#BKT_MATERIAL_ID").val('') ;
		$("#MAT_BPB").val('') ;
		$("#MAT_SKB").val('') ;
	}
/* ### End Material ### */            
</script>
<table width="607" class="teks_">
<tr><td width="140" height="21">ID Kontraktor</td><td width="12">:</td>
<td width="439"><input type="text" style="text-transform: uppercase; font-size: 11px; width: 150px;" id="kode_kontraktor" class="input"> -
<input type="text" style="text-transform: uppercase; font-size: 11px; width: 200px;" disabled="disabled" id="nama_kontraktor" class="input">
</td>
<tr><td height="21">Periode</td><td>:</td><td><? echo $periode; ?></td></tr>
<tr>
  <td>Kontraktor TBS</td>
  <td>:</td>
  <td><input type="checkbox" id="chk_istbs" disabled="disabled" class="input" /></td>
</tr>
</table>
<br/>
 <table id="list_kontraktor" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
 <div id="pager_kontraktor" class="scroll" style="text-align:center;"></div>





<!-- form material -->
<div id="formi_material" class="teks_" >
<table width="100%">
<tr>
	<td>Kode Kontraktor</td>
    <td>:</td>
    <td><input type="text" id="MAT_KD_KONTRAKTOR" tabindex="1" class="input" disabled="disabled"></td>
</tr>
<tr>
	<td>Nama Kontraktor</td>
    <td>:</td>
    <td><input type="text" id="MAT_NM_KONTRAKTOR" tabindex="2" class="input" disabled="disabled" style="width:220px;"></td>
</tr>
<tr>
	<td>Tanggal</td>
    <td>:</td>
    <td><input type="text" id="MAT_TGL_ACTIVITY" tabindex="3" class="input" onkeypress="getActMaterial()"></td>
</tr>
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
  <td>No. Surat Keluar Barang ( SKB )</td>
  <td>&nbsp;</td>
  <td><input type="text" id="MAT_SKB" class="input" style="width:100px" /></td>
</tr>
<tr>
	<td>No. Bon Permintaan Barang ( BPB )</td>
    <td>:</td>
    <td><input type="text" id="MAT_BPB" class="input" style="width:100px" >
    <input type="hidden" id="form_mode">
    <input type="hidden" id="BKT_MATERIAL_ID"></td>
</tr>
</table>

<!-- input material -->
<div id="formg_material">

<table id="list_material" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_material" class="scroll"></div><br/>
	<div id="pmtambah" class="scroll" style="float:left;">
		<input type="button"  id="add_material" value="tambah" onClick="addmaterial()" class="basicBtn" style="font-size:10px;"> &nbsp;
    </div>
<div id="pmupdate" class="scroll" style="float:left;">
		<input type="button"  id="upd_material" value="Ubah" onClick="editmaterial()" class="basicBtn" style="font-size:10px;"> &nbsp;
    </div>
	<div id="pmhapus" class="scroll" style="float:left;">
    	<input type="button"  id="dlt_material" value="hapus" onClick="deletematerial()" class="basicBtn" style="font-size:10px;">
    </div>

</div>        
<!-- end material -->
</div>


<!-- end form material -->
<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr>
<tr><td align="center"><input type="button" id="cok" name="cok" width="100" value="Tutup" onclick="closewin()" disabled="disabled"/></td></tr></table>
</div> 
<!-- end progress bar -->
</body>
