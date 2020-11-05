<? 
    $template_path = base_url().$this->config->item('template_path');  
	
?>   
<script type="text/javascript">
jQuery(document).ready(function(){
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $("#frm_kontraktor").dialog({
        dialogClass : 'dialog1',
        autoOpen: false, height: 520, width: 620, modal: true,
        title: "Kontraktor", resizable: false, moveable: true,
        buttons: {
			Tutup: function(){
                        $('input').val(''); 
                        $('textarea').val('');
                        $("#frm_kontraktor").dialog('close');                  
            },
			Simpan: function(){
                       simpan_kontraktor();                  
            }
        } 
    });
	
	$("#AdemGrid").dialog({
        dialogClass : 'dialog1', id : 'AdemGrid', position: ['center', 'top'],
        autoOpen: false, height: 480, width: 960, modal: true,
        title: "Data Master Vendor Kontraktor Adempiere", resizable: false, moveable: true,
        buttons: {
			Tutup: function(){
				$("#AdemGrid").dialog('close');                  
            }
        } 
    });
	
});

$(document).ready(function() {
    $('input').val('');
    $('textarea').val('');
});

function reloadGrid(){    
     var company_code='<?php echo $company_code;?>'; 
     jQuery("#"+"<?= $grid_name; ?>").setGridParam({url:url+'m_kontraktor_lhm/LoadData/'}).trigger("reloadGrid");    
}

$(document).ready(function() {
    var $tabs = $('#tabs').tabs();
    var selected = $tabs.tabs('option', 'selected'); // => 0
    $("#tabs").tabs();
	
	//######################## START CHAIN FUNCTION #############################
	
    $("#txt_provinsi").change(function(){
        var product = $(this).val();
        if (product != 0){
          $("#txt_kota").empty();
          var cType = $("#txt_kota").val();
          if (cType==null){
            cType="-";
          }
          $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/LoadChain/'+$("#txt_provinsi").val()+'/'+cType+'/', 
          $("#txt_provinsi").val(),
            function(datapost){ 
                $("#txt_kota").get(0).add(new Option(" -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++){
                    $("#txt_kota").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#txt_kota").attr('style','display:inline; width:250px;');
			},"json")
        } else {
            $("#txt_kota").attr('style','display:inline; width:250px;');
        }
    });    
	
	/* get kecamatan */
	$("#txt_kota").change(function(){
        var product = $(this).val();
        if (product != 0){
          $("#txt_kecamatan").empty();
          var cType = $("#txt_kecamatan").val();
          if (cType==null){
            cType="-";
          }
          $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/LoadChain2/'+$("#txt_provinsi").val()+'/'+$("#txt_kota").val()+'/'+cType+'/', 
          $("#txt_kecamatan").val(),
            function(datapost){ 
                $("#txt_kecamatan").get(0).add(new Option(" -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++){
                    $("#txt_kecamatan").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#txt_kecamatan").attr('style','display:inline; width:250px;');
			},"json")
        } else {
            $("#txt_kecamatan").attr('style','display:inline; width:250px;');
        }
    }); 
//######################## END CHAIN FUNCTION ###############################
});

function add_kontraktor(){
	var accessrole = "<?= $this->session->userdata('USER_LEVEL'); ?>";
	if( accessrole == "SAD" || accessrole == "ADMHO" || accessrole == "MTBUDGET" ){
		//#### SET DEFAULT TABS INDEX
		$("#tabs").tabs();
		$("#tabs").tabs('select',0);
		//###########################
		
		$('input').val('');
		$('textarea').val('');
		jQuery("#list_Kendaraan").clearGridData();
		
		$("#txt_frmMode").val("ADD");
		$("#frm_kontraktor").dialog('open'); 
	} else {
		alert("Tidak memiliki akses untuk penambahan data!!!");
	}
}

function simpan_kontraktor (){
	var url = "";
    if ($("#txt_frmMode").val()=='ADD'){
        var answer = confirm ("Tambah data kontraktor ?" ) 
    }else if($("#txt_frmMode").val()=='EDIT'){
        var answer = confirm ("Ubah data kontraktor ?" )
    }else{
        var answer = confirm ("Tambah/Ubah data kontraktor ?" )
    }
    
    if (answer){
        var postdata_id = {};
        var ids = jQuery("#"+"<?= $grid_name; ?>").getGridParam('selrow'); 
        var data = $("#"+"<?= $grid_name; ?>").getRowData(ids) ;
		
		postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
        postdata_id['KODE_INISIAL'] = $("#txt_kodeInit").val();
        postdata_id['NAMA_KONTRAKTOR'] = $("#txt_namaKontraktor").val();
		var isActive = $("#knt_tbs").is(':checked');
		var isActive2 = $("#knt_active").is(':checked');

		if(isActive==true) {
			isActive=1;
		} else {
			isActive=0;
		}
		
		if(isActive2==true) {
			isActive2=1;
		} else {
			isActive2=0;
		}


		postdata_id['IS_KONTRAKTOR_TBS'] = isActive;
        postdata_id['NAMA_CONTACT'] =  $("#txt_namaKontak").val();
        postdata_id['NO_CONTACT'] =  $("#txt_noKontak").val();
        postdata_id['ALAMAT'] =  $("#txt_alamat").val();
        postdata_id['KOTA'] =  $("#txt_kota").val();
        postdata_id['KODE_POS'] =  $("#txt_kodePos").val();
        postdata_id['PROPINSI'] =  $("#txt_provinsi").val();
		postdata_id['KECAMATAN'] =  $("#txt_kecamatan").val();
        postdata_id['TELEPON'] =  $("#txt_telepon").val();
        postdata_id['EMAIL'] =  $("#txt_email").val();
        postdata_id['BANK'] =  $("#txt_bank").val();
		postdata_id['NPWP'] =  $("#txt_npwp").val();
        	postdata_id['NO_REKENING'] =  $("#txt_noRekening").val();
        	postdata_id['IS_KONTRAKTOR_TBS'] =  isActive;
		postdata_id['ACTIVE'] =  $("#knt_active").val();

		if ($("#txt_frmMode").val()=='ADD'){
			url = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/create_kontraktor/';
		}else if($("#txt_frmMode").val()=='EDIT'){
			postdata_id['KODE_KONTRAKTOR'] = data.KODE_KONTRAKTOR;
			url = '<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/update_kontraktor/'+ data.KODE_KONTRAKTOR;
		}else{
			var answer = confirm ("Tambah/Ubah data kontraktor ?" )
		}
		
        postdata_id['CRUD'] =  $("#txt_frmMode").val(); 
        $.post(url, postdata_id,function(message,status){ 
              var message = new String(message);
			  
              if(message.replace(/\s/g,"") != "1") { 
                    alert(message); 
              } else { 
                    alert('data berhasil tersimpan.');
                    $("#frm_kontraktor").dialog('close'); 
					gridReload();
              }; 
        }); 
    }
}

function hapus_data(cl){
    var ids = cl; 
    var data = $("#"+"<?= $grid_name; ?>").getRowData(ids) ;
    var answer = confirm ("Inaktifkan data Kontraktor : " + data.NAMA_KONTRAKTOR + " ?" )
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
        postdata_id['KODE_KONTRAKTOR'] = data.KODE_KONTRAKTOR;
        postdata_id['CRUD'] =  'DELKTR';

        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/delete_kontraktor/', postdata_id,function(message,status){ 
              var status = new String(status);
              if(status.replace(/\s/g,"") != "success") { 
                    alert('data gagal dinonaktifkan'); 
              } else { 
                    alert('data berhasil dinonaktifkan.');
                    $("#frm_kontraktor").dialog('close'); 
					gridReload();
              };  
        }); 
    }
}

function update_data(cl){
    var ids = cl; 
    var data = $("#list_kontraktor").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined)
    {
        alert("harap pilih data untuk di edit...");
    }else{
        $('input').val('');
        $('textarea').val('');
	 var isActive = data.IS_KONTRAKTOR_TBS;
	 var isActive2 = data.ACTIVE;

        if (isActive==1) {
            $("#knt_tbs").attr('checked',true);
        } else {
            $("#knt_tbs").attr('checked',false);    
        }

	  if (isActive2==1) {
            $("#knt_active").attr('checked',true);
        } else {
            $("#knt_active").attr('checked',false);    
        }

        $("#txt_kodeKontraktors").val(data.KODE_KONTRAKTOR);
        $("#txt_kodeInit").val(data.KODE_INISIAL);
        $("#txt_namaKontraktor").val(data.NAMA_KONTRAKTOR);
        $("#txt_namaKontak").val(data.NAMA_CONTACT);
        $("#txt_noKontak").val(data.NO_CONTACT);
        $("#txt_alamat").val(data.ALAMAT);
        $("#txt_kodePos").val(data.KODE_POS);
        $("#txt_provinsi").val(data.PROPINSI);
		$.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/LoadChain/'+$("#txt_provinsi").val(), 
          $("#txt_provinsi").val(),
            function(datapost){ 
				$('#txt_kota').empty();
                $("#txt_kota").get(0).add(new Option(" -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++){
                    $("#txt_kota").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
                $("#txt_kota").val(data.KOTA);
			},"json");
		$.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/LoadChain2/'+data.PROPINSI+'/'+data.KOTA, 
          $("#txt_kecamatan").val(),
            function(datapost){ 
				$('#txt_kecamatan').empty();
                $("#txt_kecamatan").get(0).add(new Option(" -- pilih -- ",""), document.all ? i : null);
                for (var i=0; i<datapost.length; i++){
                    $("#txt_kecamatan").get(0).add(new Option(
                    datapost[i].kt,datapost[i].kt2), document.all ? i : null);
                }
				$("#txt_kecamatan").val(data.KECAMATAN);
			},"json");
		$("#txt_telepon").val(data.TELEPON);
        $("#txt_email").val(data.EMAIL);
        $("#txt_bank").val(data.BANK);
        $("#txt_noRekening").val(data.NO_REKENING);
        $("#txt_npwp").val(data.NPWP);
        
        $("#txt_frmMode").val("EDIT");

        jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor_lhm/LoadData_Kendaraan/'+data.KODE_KONTRAKTOR}).trigger("reloadGrid");
        $("#tabs").tabs();
        $("#tabs").tabs('select',0);
        $("#frm_kontraktor").dialog('open');
    } 
}

function initFormInput(){
	$("#knt_tbs").attr('checked',false);    
	$("#txt_kodeKontraktors").val("");
	$("#txt_kodeInit").val("");
	$("#txt_namaKontraktor").val("");
	$("#txt_namaKontak").val("");
	$("#txt_noKontak").val("");
	$("#txt_alamat").val("");
	$("#txt_kodePos").val("");
	$("#txt_provinsi").val("");
	$('#txt_kota').val("");
	$("#txt_kecamatan").val("");
	$("#txt_telepon").val("");
	$("#txt_email").val("");
	$("#txt_bank").val("");
	$("#txt_noRekening").val("");
	$("#txt_npwp").val("");
	$("#txt_frmMode").val("");
	$("#knt_tbs").attr('checked',false);
}

var url = "<?= base_url().'index.php/' ?>"; 
var gridimgpath = '<?= $template_path ?>themes/basic/images'; 
 
var jGrid = null;
var colNamesT = new Array();
var colModelT = new Array();

var colNamesT_Detail = new Array();
var colModelT_Detail = new Array();

colNamesT.push('no');
colModelT.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});
 
colNamesT.push('KODE KONTRAKTOR');
colModelT.push({name:'KODE_KONTRAKTOR',index:'KODE_KONTRAKTOR', editable: false, hidden:true, width:10, align:'left'});

colNamesT.push('SEARCH KEY');
colModelT.push({name:'KODE_INISIAL',index:'KODE_INISIAL', editable: false, hidden:false, width:100, align:'center'});

colNamesT.push('KONTRAKTOR TBS');
colModelT.push({name:'IS_KONTRAKTOR_TBS',index:'IS_KONTRAKTOR_TBS', editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, hidden:false, width:130, align:'center'});

colNamesT.push('NAMA KONTRAKTOR');
colModelT.push({name:'NAMA_KONTRAKTOR',index:'NAMA_KONTRAKTOR', editable: false, hidden:false, width: 250, align:'left'});

colNamesT.push('NAMA KONTAK');
colModelT.push({name:'NAMA_CONTACT',index:'NAMA_CONTACT', editable: false, hidden:false, width: 205, align:'center'});

colNamesT.push('NO KONTAK');
colModelT.push({name:'NO_CONTACT',index:'NO_CONTACT', editable: false, hidden:true, width: 80, align:'center'});

colNamesT.push('ALAMAT');
colModelT.push({name:'ALAMAT',index:'ALAMAT', editable: false, hidden:false, width: 280, align:'left'});

colNamesT.push('PROPINSI');
colModelT.push({name:'PROPINSI',index:'PROPINSI', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('KOTA');
colModelT.push({name:'KOTA',index:'KOTA', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('KECAMATAN');
colModelT.push({name:'KECAMATAN',index:'KECAMATAN', editable: false, hidden:true, width: 120, align:'left'});

colNamesT.push('KODE_POS');
colModelT.push({name:'KODE_POS',index:'KODE_POS', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('TELEPON');
colModelT.push({name:'TELEPON',index:'TELEPON', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('EMAIL');
colModelT.push({name:'EMAIL',index:'EMAIL', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('BANK');
colModelT.push({name:'BANK',index:'BANK', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('NO_REKENING');
colModelT.push({name:'NO_REKENING',index:'NO_REKENING', editable: false, hidden:true, width: 80, align:'left'});

colNamesT.push('NPWP');
colModelT.push({name:'NPWP',index:'NPWP', editable: false, hidden:false, width: 140, align:'left'});

colNamesT.push('COMPANY_CODE');
colModelT.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, align:'left'});

colNamesT.push('AKTIF');
colModelT.push({name:'ACTIVE',index:'ACTIVE', hidden:false, editable: false, edittype:'checkbox', editoptions: { value:"1:0"}, 
  formatter: "checkbox", formatoptions: {disabled : true}, width: 60, align:'center'});
colNamesT.push('EDIT');
colModelT.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
            jGrid_va = jQuery("#"+"<?= $grid_name; ?>").jqGrid({
                url:url+'m_kontraktor_lhm/LoadData/', mtype : "POST", datatype: "json",
                colNames: colNamesT, colModel: colModelT, sortname: colModelT[1].name,
                pager:jQuery("#"+"<?= $grid_pager; ?>"), rowNum: 20, rownumbers: true,
                height: 410, imgpath: gridimgpath, sortorder: "asc",
                cellEdit: false, cellsubmit: 'clientArray', forceFit : true,   
                loadComplete: function(){ 
                    var ids = jQuery("#"+"<?= $grid_name; ?>").getDataIDs();
                    for(var i=0;i<ids.length;i++){ 
                            var cl = ids[i];
                            //ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                            be = "<img style='cursor:pointer;' alt='Ubah Data' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" /><span>&nbsp;</span>"; 
                            ce = "<img style='cursor:pointer;' alt='Hapus Data' src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";

						jQuery("#"+"<?= $grid_name; ?>").setRowData(ids[i],{act:be+ce}) 
                    }                           
                }, 
            });
            jGrid_va.navGrid("#"+"<?= $grid_pager; ?>",{edit:false,del:false,add:false, search: false, refresh: true});
             //######## UPDATE 15 Desember 2010 #########
            jGrid_va.navButtonAdd("#"+"<?= $grid_pager; ?>",{
                   caption:"Tambah Data", 
                   buttonicon:"ui-icon-add", 
                   onClickButton: function(){ 
                        add_kontraktor()
                   }, 
                    position:"left",
            });
			jGrid_va.navButtonAdd("#"+"<?= $grid_pager; ?>",{
                   caption:"Export ke Excell", 
                   buttonicon:"ui-icon-add", 
                   onClickButton: function(){ 
                        exportToExcell()
                   }, 
                    position:"left",
            });
            /* jGrid_va.navButtonAdd("#"+"<?= $grid_pager; ?>",{
               caption:"Cari Data", 
               buttonicon:"ui-icon-search", 
               onClickButton: function(){ 
                        $("#search_form").dialog('open');
               }, 
               position:"left"
            }); */
             //######## END UPDATE 15 Desember 2010 #########    
         }
         

jQuery("#"+"<?= $grid_name; ?>").ready(loadView); 


colNamesT_Detail.push('ID');
colModelT_Detail.push({name:'ID_KENDARAAN_KONTRAKTOR',index:'ID_KENDARAAN_KONTRAKTOR', editable: false, hidden:true, width:75, align:'left'}); 

colNamesT_Detail.push('tmpno');
colModelT_Detail.push({name:'TMP_NO',index:'TMP_NO', editable: false, hidden:true, width:75, align:'left'});

colNamesT_Detail.push('No Kendaraan');
colModelT_Detail.push({name:'NO_KENDARAAN',index:'NO_KENDARAAN', editable: true, hidden:false, width:75, align:'left'});

colNamesT_Detail.push('DESKRIPSI');
colModelT_Detail.push({name:'DESKRIPSI',index:'DESKRIPSI', editable: true, hidden:false, width: 200, align:'left'});

colNamesT_Detail.push('NOTE');
colModelT_Detail.push({name:'NOTE',index:'NOTE', editable: true, hidden:false, width: 125, align:'center'});

colNamesT_Detail.push('');
colModelT_Detail.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 

var company_code='<?php echo $company_code;?>';
var lastsel; var jdesc1;
var lRow; var lCol; var i = 0;
var loadView = function(){
            jGrid_va = jQuery("#list_Kendaraan").jqGrid({
                url:url+'m_kontraktor_lhm/LoadData_Kendaraan/xx', mtype : "POST", datatype: "json",
                colNames: colNamesT_Detail, colModel: colModelT_Detail, sortname: colModelT_Detail[0].name,
                pager:jQuery("#pager_Kendaraan"), rowNum: 20, rownumbers: true, height: 150,
                width:500, imgpath: gridimgpath, sortorder: "asc", cellsubmit: 'clientArray',
                cellEdit: true, forceFit : true,
                loadComplete: function(){ 
                    var ids = jQuery("#list_Kendaraan").getDataIDs();
                    for(var i=0;i<ids.length;i++){ 
                            var cl = ids[i];
                            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_kendaraan('"+cl+"');\" />"; 
                            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_kendaraan('"+cl+"');\"/>";

                            jQuery("#list_Kendaraan").setRowData(ids[i],{act:be+ce}) 
                    }                           
                }  
            });
            jGrid_va.navGrid("#pager_Kendaraan",{edit:false,del:false,add:false, search: false, refresh: true});

            jGrid_va.navButtonAdd('#pager_Kendaraan',{
               caption:"Tambah Data", 
               buttonicon:"ui-icon-add", 
               onClickButton: function(){
                        addrow()
               }, 
               position:"left"
            });
             
         }
jQuery("#list_Kendaraan").ready(loadView); 

function addrow()
{
    i=i+1;    
    var datArr = {};
    if (i>1){
        var datArr = {WEIGHT:jdesc1};
    }
    sv = "<img src='<?= $template_path ?>themes/base/images/disc.png' width='12px' height='13px' onclick='simpan_kendaraan("+i+")'; />"; 
    var su=jQuery("#list_Kendaraan").addRowData(i,datArr,'last');
    var act=jQuery("#list_Kendaraan").setRowData(i,{act:sv})  

}

function simpan_kendaraan(i){
    jQuery('#list_Kendaraan').saveCell(ids);
    $("#frm_load").dialog('open');
        var postdata_id = {};
        var ids = jQuery("#list_Kendaraan").getGridParam('selrow'); 
        var data = $("#list_Kendaraan").getRowData(ids) ;
        
        postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
        postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
        postdata_id['DESKRIPSI'] = data.DESKRIPSI;
        postdata_id['NOTE'] = data.NOTE;
        
        postdata_id['CRUD'] =  'ADDVH'; 
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/create_kendaraan/', postdata_id,function(message,status){ 
              var status = new String(status);
              if(status.replace(/\s/g,"") != "success") { 
                     alert(obj.status);
                     $("#frm_load").dialog('close'); 
              } else { 
                   	$("#frm_load").dialog('close');
					jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor_lhm/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");
              };  
        });
}

function update_kendaraan(cl){
    var ids = cl;
    var data = $("#list_Kendaraan").getRowData(ids) ;
    if (ids=="" || ids==null || ids==undefined){
        alert("harap pilih data untuk di edit...");
    }else{
        jQuery('#list_Kendaraan').saveCell(ids);
            $("#frm_load").dialog('open');
            var postdata_id = {};
            var ids = jQuery("#list_Kendaraan").getGridParam('selrow'); 
            var data = $("#list_Kendaraan").getRowData(ids) ;
            
            postdata_id['ID_KENDARAAN_KONTRAKTOR'] = data.ID_KENDARAAN_KONTRAKTOR;
            postdata_id['NO_KENDARAAN'] = data.NO_KENDARAAN;
            postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
            postdata_id['DESKRIPSI'] = data.DESKRIPSI;
            postdata_id['NOTE'] = data.NOTE;
            
            postdata_id['CRUD'] =  'EDITVH'; 
            $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/update_kendaraan/', postdata_id,function(message,status){ 
              var status = new String(status);
              if(status.replace(/\s/g,"") != "success") { 
                     alert(obj.status);
                     $("#frm_load").dialog('close'); 
              } else { 
                   	$("#frm_load").dialog('close');
					jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor_lhm/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");
              };  
       		});
    }     
}

function hapus_kendaraan(cl){
    var ids = cl;
    var data = $("#list_Kendaraan").getRowData(ids) ;
    var answer = confirm ("Hapus Data KENDARAAN kontraktor dengan NOPOL : " + data.NO_KENDARAAN + " ?" )
    if (answer){
        $("#frm_load").dialog('open');
        var postdata_id = {};
    
        postdata_id['KODE_KONTRAKTOR'] = $("#txt_kodeKontraktors").val();
        postdata_id['NO_KENDARAAN'] = data.TMP_NO;
        postdata_id['CRUD'] =  'DELKTR';

        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']); ?>/m_kontraktor_lhm/delete_kendaraan/', postdata_id,function(message,status){ 
              var status = new String(status);
              if(status.replace(/\s/g,"") != "success") { 
                     alert(obj.status);
                     $("#frm_load").dialog('close'); 
              } else { 
                   	$("#frm_load").dialog('close');
					jQuery("#list_Kendaraan").setGridParam({url:url+'m_kontraktor_lhm/LoadData_Kendaraan/'+$("#txt_kodeKontraktors").val()}).trigger("reloadGrid");
              };  
        });
    }    
}
</script>

<script type="text/javascript">
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 
// var elem = ev.target||ev.srcElement; 
    if(timeoutHnd) 
        clearTimeout(timeoutHnd) 
        timeoutHnd = setTimeout(gridReload,500) 
}  

function gridReload(){ 
    var nama_kontraktor = jQuery("#text_namaKontraktor").val(); 

    if (nama_kontraktor == ""){
        nama_kontraktor = "-";
    }
    
    jQuery("#"+"<?= $grid_name; ?>").setGridParam({url:url+"m_kontraktor_lhm/search_data/"+nama_kontraktor}).trigger("reloadGrid");        
} 

/* get data adempiere */

var jGrid_Adem = null;
var colNamesT_Adem = new Array();
var colModelT_Adem = new Array();
			
colNamesT_Adem.push('no');
colModelT_Adem.push({name:'no',index:'no', sortable:false, resizable:true, editable: false,hidden:true, width: 30, align:'center'});
 
colNamesT_Adem.push('ID BP');
colModelT_Adem.push({name:'c_bpartner_id',index:'c_bpartner_id', editable: false, hidden:false, width:60, align:'left'});

colNamesT_Adem.push('SEARCH KEY');
colModelT_Adem.push({name:'inisal',index:'inisal', editable: false, hidden:false, width:90, align:'center'});

colNamesT_Adem.push('NAMA KONTRAKTOR');
colModelT_Adem.push({name:'nama',index:'nama', editable: false, hidden:false, width:180, align:'left'});

colNamesT_Adem.push('KETERANGAN');
colModelT_Adem.push({name:'ket',index:'ket', editable: false, hidden:true, width: 100, align:'left'});

colNamesT_Adem.push('ALAMAT');
colModelT_Adem.push({name:'alamat',index:'alamat', editable: false, hidden:false, width: 125, align:'center'});

colNamesT_Adem.push('KODE POS');
colModelT_Adem.push({name:'postal',index:'postal', editable: false, hidden:true, width: 75, align:'center'});

colNamesT_Adem.push('NO KONTAK');
colModelT_Adem.push({name:'phone',index:'phone', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_Adem.push('FAX');
colModelT_Adem.push({name:'fax',index:'fax', editable: false, hidden:true, width: 200, align:'left'});

colNamesT_Adem.push('AD ORG ID');
colModelT_Adem.push({name:'ad_org_id',index:'ad_org_id', editable: false, hidden:true, width: 120, align:'left'});

colNamesT_Adem.push('NPWP');
colModelT_Adem.push({name:'npwp',index:'npwp', editable: false, hidden:false, width: 120, align:'left'});

colNamesT_Adem.push('PERUSAHAAN');
colModelT_Adem.push({name:'name',index:'name', editable: false, hidden:false, width: 120, align:'left'});

colNamesT_Adem.push('Aktif');
colModelT_Adem.push({name:'isactive',index:'isactive', hidden:false, editable: false, edittype:'checkbox', 
			  editoptions: { value:"Y:N"}, formatter: "checkbox", formatoptions: {disabled : true}, width: 60, 
			  align:'center'});

var loadViewAdem = function(){
jGrid_Adem = jQuery("#"+"<?= $grid_adem_name; ?>").jqGrid({
    url:url+'m_kontraktor_lhm/LoadDataAdem/', mtype : "POST", datatype: "json",
    colNames: colNamesT_Adem, colModel: colModelT_Adem, sortname: colModelT_Adem[1].name,
    pager:jQuery("#"+"<?= $grid_adem_pager; ?>"), rowNum: 20, rownumbers: true,
    height: 300, imgpath: gridimgpath, sortorder: "asc",
    cellEdit: false, cellsubmit: 'clientArray', forceFit : true,   
    loadComplete: function(){ 
        /* var ids = jQuery("#"+"<?= $grid_adem_pager; ?>").getDataIDs();
        for(var i=0;i<ids.length;i++){ 
            var cl = ids[i];
            be = "<img style='padding-right:6px;' src='<?= $template_path ?>themes/base/images/file_edit.png' width='12px' height='13px' onclick=\"update_data('"+cl+"');\" />"; 
            ce = "<img src='<?= $template_path ?>themes/base/images/file_delete.png' width='12px' height='13px' onclick=\"hapus_data('"+cl+"');\"/>";
			jQuery("#"+"<?= $grid_adem_name; ?>").setRowData(ids[i],{act:be+ce}) 
        } */                          
    }, ondblClickRow: function(){
	   var postdata={};
	   var id = jQuery("#"+"<?= $grid_adem_name; ?>").getGridParam('selrow');
	   if (id){
		  var ret = jQuery("#"+"<?= $grid_adem_name; ?>").getRowData(id);
		  initFormInput();
		  $("#knt_tbs").attr('checked',false);    
		  $("#txt_kodeInit").val(ret.inisal);
		  
		  var idLHM = "<?= $this->session->userdata('DCOMPANY'); ?>"+ret.inisal; 
		  $("#txt_kodeKontraktors").val(idLHM);
		  $("#txt_namaKontraktor").val(ret.nama);
		  $("#txt_namaKontak").val(ret.nama);
		  $("#txt_noKontak").val(ret.phone);
		  $("#txt_alamat").val(ret.alamat);
		  $("#txt_kodePos").val(ret.postal);
		  $("#txt_telepon").val(ret.phone);
		  $("#txt_npwp").val(ret.npwp);
		  var isActive = ret.isactive;
		  if(isActive=='Y') {
			  $("#knt_active").attr('checked',true);
		  } else {
			  $("#knt_active").attr('checked',false);
		  }
			
		  $("#frm_kontraktor").dialog('close');	
		  $("#AdemGrid").dialog('close');
		  $("#frm_kontraktor").dialog('open');
		  //$("#AdemGrid").find('iframe').remove();
		  //$("#AdemGrid").dialog('destroy').remove();

		  $("#txt_frmMode").val("ADD");
		  //$("#frm_kontraktor").dialog('open');
	   }
		 /* 
		 if (id){
			var ret = jQuery("#list_Menu").getRowData(id);
			postdata['LOGINID'] = $("#m_login").val() ;
			postdata['MENU_ID'] = ret.MENU_ID	
			$.post( url+'syst_c_user/insertUserMenu/', postdata, function(status) {
				var status = new String(status);
				if(status.replace(/\s/g,"") != "") { 
					alert(status); 
				} else { 
					var id = $("#m_login").val(); 
					jQuery("#list_uMenu").setGridParam({url:url+'syst_c_user/search_user_menu/'+id}).trigger("reloadGrid");
					$("#gridMenu").dialog("close");
				};  
			});
		 } */
	 }
});
jGrid_Adem.navGrid("#"+"<?= $grid_adem_pager; ?>",{edit:false,del:false,add:false, search: false, refresh: true});

}
jQuery("#"+"<?= $grid_adem_name; ?>").ready(loadViewAdem); 


function gridAdemReload(){ 
  jQuery("#"+"<?= $grid_adem_name; ?>").setGridParam({url:url+"m_kontraktor_lhm/LoadDataAdem/"}).trigger("reloadGrid");
}

function gridAdemSearch(){ 
	var k_Adem = jQuery("#txtSearchAdem").val(); 
    jQuery("#"+"<?= $grid_adem_name; ?>").setGridParam({url:url+"m_kontraktor_lhm/LoadDataAdem/"+k_Adem}).trigger("reloadGrid");
}

function openAdemGrid(){
	var accessrole = "<?= $this->session->userdata('USER_LEVEL'); ?>";
	if( accessrole == "SAD" || accessrole == "ADMHO" || accessrole == "MTBUDGET" ){
		gridAdemReload();
		$("#AdemGrid").dialog('open');
		$('#mydialog').parent().position({my:'center',of:'center',collison:'fit'});
	} else {
		alert("Tidak memiliki akses untuk penambahan data!!!");
	}
}	
 
/* end get adempiere */
</script>
<div id='main_form'>
    <div id"gridSearch">  
        <table border="0" class="teks_" cellpadding="2" cellspacing="4">
            <tr><td height="20" colspan="8">Pencarian Berdasarkan :</td></tr>
            <tr>
                <td height="28" class="labelcell">Kontraktor</td><td class="labelcell">:</td>
                <td class="fieldcell">
                    <input class="input" type="text" name="txt_src" id="text_namaKontraktor" onkeydown="doSearch(arguments[0]||event)"/>
                </td>
            </tr>
        </table> 
    </div>
    <div id='transaction_frame'>
        <div id="mainGrid">
            <table id="<?= $grid_name; ?>" class="scroll"></table> 
            <div id="<?= $grid_pager; ?>" class="scroll" style="text-align:center;"></div>
        </div> 
		
		<br />
		<div id="AdemGrid">
        	Cari BP ( Search Key Adempiere ) : &nbsp; 
            <input class="input" type="text" name="txtSearchAdem" id="txtSearchAdem" onchange="gridAdemSearch()"/><br/> <span style="font-size:9px"> Tekan tombol enter pada keyboard setelah isi kotak pencarian jika ingin memuat ulang data </span> <br/>
            <table id="<?= $grid_adem_name; ?>" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table> 
            <div id="<?= $grid_adem_pager; ?>" class="scroll" style="text-align:center;"></div>
        </div> 
    </div>
    
</div>

<div id="frm_kontraktor">
    <div id="tabs">
        <ul>
            <li><a href="#fragment-1"><span>Detail Kontraktor</span></a></li>
            <li><a href="#fragment-2"><span>Data Kendaraan Kontraktor</span></a></li>
        </ul>
        <div id="fragment-1" class="panes">
            <table width="100%" border="0" class="teks_" id="input_table" >
				<tr>
                    <td class="labelcell" height="20">Search Key</td><td>:</td>
                    <td class="fieldcell"><input tabindex="2" type="text" class="input" id="txt_kodeInit" 
                    			disabled="disabled" name="txt_kodeInit" maxlength="100"/>
								
					
					    <img id="searchButton" src="<?= $template_path ?>themes/base/images/Search.png" style="cursor:pointer;" onclick="openAdemGrid()" />&nbsp;&nbsp;
					    
					
					</td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Kode Kontraktor</td><td>:</td>
                    <td class="fieldcell"><input style="width: 150px;" class="input" disabled="disabled" tabindex="1" 
                    			type="text" id="txt_kodeKontraktors" name="txt_kodeKontraktors" maxlength="50"/>
					
					<!-- <img id="loadbutton" src="<?= $template_path ?>themes/base/images/Reloader.png" style="cursor:pointer;" onclick="" />-->		
					</td>
                </tr>
                
                <tr>
                    <td class="labelcell" height="20">Kontraktor TBS</td><td>:</td>
                    <td class="fieldcell">
                                <input type="checkbox" id="knt_tbs" disabled="disabled" name="knt_tbs" class="input" tabindex="3" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Nama Kontraktor</td><td>:</td>
                    <td class="fieldcell"><input tabindex="4" disabled="disabled" class="input" type="text" id="txt_namaKontraktor" name="txt_namaKontraktor" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Nama Kontak</td><td>:</td>
                    <td class="fieldcell"><input tabindex="5" disabled="disabled" class="input" type="text" id="txt_namaKontak" name="txt_namaKontak" maxlength="100"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">No Telp PIC</td><td>:</td>
                    <td class="fieldcell"><input tabindex="6" class="input" type="text" id="txt_noKontak" name="txt_noKontak" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell" height="20">Alamat</td><td>:</td>
                    <td class="fieldcell">
                        <textarea tabindex="7" class="input" name="txt_alamat" id="txt_alamat" cols="45" rows="5" style="height:40px; width:250px;"></textarea>
                    </td>
                </tr>
                 <tr>
                    <td class="labelcell" height="20">Provinsi</td><td>:</td>
                    <td class="fieldcell"><? if(isset($propinsi)) { echo $propinsi; } ?></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Kota / Kabupaten</td><td>:</td>
                    <td class="fieldcell">
                    <select tabindex="9" name='txt_kota' class='select' id="txt_kota" style="width:250px;">
					</select></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Kecamatan</td><td>:</td>
                    <td class="fieldcell"><select tabindex="10" name='txt_kecamatan' class='select' id="txt_kecamatan" style="width:250px;">
					</select></td>
                </tr>              
                <tr>
                    <td class="labelcell" height="20">Kode POS</td><td>:</td>
                    <td class="fieldcell"><input style="width: 75px;" class="input" tabindex="11" type="text" id="txt_kodePos" name="txt_kodePos" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Telepon</td><td>:</td>
                    <td class="fieldcell"><input style="width: 75px;" class="input" tabindex="12" type="text" id="txt_telepon" name="txt_telepon" maxlength="25"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">Email</td><td>:</td>
                    <td class="fieldcell"><input tabindex="13" class="input" type="text" id="txt_email" name="txt_email" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell" height="20">Bank</td><td>:</td>
                    <td class="fieldcell"><input tabindex="14" class="input" type="text" id="txt_bank" name="txt_email" maxlength="50"/></td>
                </tr> 
                <tr>
                    <td class="labelcell" height="20">No.Rekening</td><td>:</td>
                    <td class="fieldcell"><input tabindex="15" class="input" type="text" id="txt_noRekening" name="txt_noRekening" maxlength="50"/></td>
                </tr>
                <tr>
                    <td class="labelcell" height="20">NPWP</td><td>:</td>
                    <td class="fieldcell">
						<input tabindex="16" class="input" type="text" id="txt_npwp" name="txt_npwp" style="width:200px;"/></td>
                </tr>
                
                
                <tr>
                    <td class="labelcell" height="20">Aktif</td><td>:</td>
                    <td class="fieldcell">
                                <input type="checkbox" id="knt_active" disabled="disabled" name="knt_active" class="input" tabindex="3" maxlength="50"/></td>
                </tr>
            </table> 
        </div>
        <div id="fragment-2">
            <div id="kendaraanGrid">
                <table id="list_Kendaraan" class="scroll"></table> 
                <div id="pager_Kendaraan" class="scroll" style="text-align:center;"></div>
            </div>
        </div>
        <div id="fragment-3">
            
        </div>
    </div>
    <input type="hidden" id="txt_frmMode">
</div>

<div id="search_form"></div>  
</body>


