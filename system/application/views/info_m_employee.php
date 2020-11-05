<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/base/images'; 
var url = "<?= base_url().'index.php/' ?>";   
/* generate NIK */
$(function()
{
    $("#search_nik,#search_nama,#search_dept").val('');//clear search field
	
    var emp_type = document.getElementById("i_type").value;
    $('#i_type').chainSelect_text('#i_nik' ,url+'m_employee/cek_emp/'+ $('#i_type').val(),
    { 
        before:function (target) //before request hide the target combobox and display the loading message
        { 
            $("#loader").css("display","inline");
        },
        after:function (target) //after request show the target combobox and hide the loading message
        { 
            $("#loader").css("display","none");
        }
    });
        
    $("#emp_form").dialog({
        dialogClass : 'dialog1', bgiframe: true,
        autoOpen: false, height: 600, width: 940,
        modal: true, title: "Karyawan",
        resizable: false, moveable: true,
        buttons: {
            Tutup: function() {
                        init_emp();        
                    },
            Simpan: function() {
                        submit();        
                    }     
        } 
    });
        
	$("#i_pangkat").change(function() {
        var product = $(this).val();
        if (product != 0) {
			  $("#i_jabatan").empty();
			  var cDept = $("#i_dept").val();
			  if (cDept==null) {  cDept="-"; }
			  $.post(url+'m_employee/LoadChain/'+$("#i_pangkat").val()+'/'+cDept+'/', 
			  $("#i_pangkat").val(),
				function(datapost) 
				{ 
					$("#i_jabatan").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
					for (var i=0; i<datapost.length; i++)
					{
						$("#i_jabatan").get(0).add(new Option(
						datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
					$("#i_jabatan").attr('style','display:inline; width:230px;');
						
				},"json");
				//document.getElementById("i_act").value = '';
        } else {
            $("#i_jabatan").attr('style','display:inline;');
        }
    });
	
	$("#i_dept").change(function() {
        var product = $(this).val();
        if (product != 0) {
			  $("#i_jabatan").empty();
			  var cPangkat = $("#i_pangkat").val();
			 // alert(cPangkat);
			  if (cPangkat==null) {  cPangkat="-"; }
			  $.post(url+'m_employee/LoadChain/'+cPangkat+'/'+$("#i_dept").val()+'/', 
			  $("#i_dept").val(),
				function(datapost) 
				{ 
					$("#i_jabatan").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
					for (var i=0; i<datapost.length; i++)
					{
						$("#i_jabatan").get(0).add(new Option(
						datapost[i].kt,datapost[i].kt2), document.all ? i : null);
					}
					$("#i_jabatan").attr('style','display:inline; width:250px;');
						
				},"json");
				//document.getElementById("i_act").value = '';
        } else {
            $("#i_jabatan").attr('style','display:inline;');
        }
    }); 
	
    $("#i_tgl_lahir").datepicker({dateFormat:"yy-mm-dd", changeMonth: true, changeYear: true, yearRange: '1950:2010' });
    $("#i_tglmsk").datepicker({dateFormat:"yy-mm-dd" });
	$("#i_tglpromosi").datepicker({dateFormat:"yy-mm-dd" });
});
    

    /*search*/
var timeoutHnd; 
var flAuto = false; 

function doSearch(ev){ 

// var elem = ev.target||ev.srcElement; 
if(timeoutHnd) 
    clearTimeout(timeoutHnd) 
    timeoutHnd = setTimeout(gridReload,500) 
} 

function gridReload(){ 
    var nik = jQuery("#search_nik").val();
    var nama = jQuery("#search_nama").val();  
    var dept = jQuery("#search_dept").val();
    var inactive = $("#search_status").val(); 

    if (nik == ""){
        nik = "-";
    }
    if (nama == ""){
        nama = "-";
    } 
    if (dept == ""){
        dept = "-";
    }
    jQuery("#list_employee").setGridParam({url:url+"m_employee/search_emp/"+nik+"/"+nama+"/"+dept+"/"+inactive}).trigger("reloadGrid");} 

/*grid*/

var jGrid_employee = null;
var colNamesT_employee = new Array();
var colModelT_employee = new Array();
                                                           
colNamesT_employee.push('NIK');
colModelT_employee.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_employee.push('Nama');
colModelT_employee.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 140, align:'left'});

colNamesT_employee.push('Tipe Kary.');
colModelT_employee.push({name:'TYPE_KARYAWAN',index:'TYPE_KARYAWAN', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_employee.push('Gaji');
colModelT_employee.push({name:'GP',index:'GP', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_employee.push('HK');
colModelT_employee.push({name:'HK',index:'HK', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_employee.push('Pangkat');
colModelT_employee.push({name:'PANGKAT',index:'PANGKAT', editable: false, hidden:true, width: 80, align:'left'});

colNamesT_employee.push('Jabatan');
colModelT_employee.push({name:'JABATAN',index:'JABATAN', editable: false, hidden:false, width: 110, align:'left'});

colNamesT_employee.push('Cost Center');
colModelT_employee.push({name:'COST_CENTER',index:'COST_CENTER', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_employee.push('Departemen');
colModelT_employee.push({name:'DEPT_CODE',index:'DEPT_CODE', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_employee.push('Estate');
colModelT_employee.push({name:'ESTATE_CODE',index:'ESTATE_CODE', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Tanggal Masuk');
colModelT_employee.push({name:'DATE_JOIN',index:'DATE_JOIN', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_employee.push('Tanggal Promosi');
colModelT_employee.push({name:'DATE_PROMOTION',index:'DATE_PROMOTION', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_employee.push('Status');
colModelT_employee.push({name:'FAMILY_STATUS',index:'FAMILY_STATUS', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Pendidikan');
colModelT_employee.push({name:'LAST_EDUCATION',index:'LAST_EDUCATION', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Alamat');
colModelT_employee.push({name:'ALAMAT',index:'ALAMAT', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Telepon');
colModelT_employee.push({name:'PHONE',index:'PHONE', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_employee.push('Status Pajak');
colModelT_employee.push({name:'TAX_STATUS',index:'TAX_STATUS', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('No Jamsostek');
colModelT_employee.push({name:'NO_JAMSOSTEK',index:'NO_JAMSOSTEK', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('No npwp');
colModelT_employee.push({name:'NO_NPWP',index:'NO_NPWP', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Divisi');
colModelT_employee.push({name:'DIVISION_CODE',index:'DIVISION_CODE', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_employee.push('No identitas');
colModelT_employee.push({name:'NO_IDENTITAS',index:'NO_IDENTITAS', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Agama');
colModelT_employee.push({name:'RELIGION',index:'RELIGION', editable: false, hidden:true, width: 40, align:'center'});

colNamesT_employee.push('Jenis Kelamin');
colModelT_employee.push({name:'SEX',index:'SEX', editable: false, hidden:true, width: 90, align:'center'});

colNamesT_employee.push('Tanggal Lahir');
colModelT_employee.push({name:'TANGGAL_LAHIR',index:'TANGGAL_LAHIR', editable: false, hidden:true, width: 90, align:'center'});

colNamesT_employee.push('INACTIVE');
colModelT_employee.push({name:'INACTIVE',index:'INACTIVE', editable: false, hidden:true, width: 25, align:'center'});

colNamesT_employee.push('NOTE');
colModelT_employee.push({name:'NOTE',index:'NOTE', editable: false, hidden:true, width: 25, align:'center'});

colNamesT_employee.push('NAMA_SEKOLAH');
colModelT_employee.push({name:'NAMA_SEKOLAH',index:'NAMA_SEKOLAH', editable: false, hidden:true, width: 25, align:'center'});
colNamesT_employee.push('JURUSAN');
colModelT_employee.push({name:'JURUSAN',index:'JURUSAN', editable: false, hidden:true, width: 40, align:'center'});
colNamesT_employee.push('ALAMAT_SEKOLAH');
colModelT_employee.push({name:'ALAMAT_SEKOLAH',index:'ALAMAT_SEKOLAH', editable: false, hidden:true, width: 25, align:'center'});
colNamesT_employee.push('ISBPJS_KETENAGAKERJAAN');
colModelT_employee.push({name:'ISBPJS_KETENAGAKERJAAN',index:'ISBPJS_KETENAGAKERJAAN', editable: false, hidden:true, width: 40, align:'center'});
colNamesT_employee.push('NO_REG_BPJS_TNG');
colModelT_employee.push({name:'NO_REG_BPJS_TNG',index:'NO_REG_BPJS_TNG', editable: false, hidden:true, width: 40, align:'center'});
colNamesT_employee.push('ISBPJS_KESEHATAN');
colModelT_employee.push({name:'ISBPJS_KESEHATAN',index:'ISBPJS_KESEHATAN', editable: false, hidden:true, width: 40, align:'center'});
colNamesT_employee.push('NO_REG_BPJS_KES');
colModelT_employee.push({name:'NO_REG_BPJS_KES',index:'NO_REG_BPJS_KES', editable: false, hidden:true, width: 40, align:'center'});
colNamesT_employee.push('');
colModelT_employee.push({name:'act',index:'act', editable: false, hidden:false, width: 50, align:'center'}); 
     
var loadView_employee = function()
        {
            var nik = jQuery("#search_nik").val();
            var nama = jQuery("#search_nama").val(); 
            var dept = jQuery("#search_dept").val(); 
            var inactive = $("#search_status").val();
             
        if (nik == ""){
            nik = "-";
        }
        if (nama == ""){
            nama = "-";
        }
        if (dept == ""){
            dept = "-";
        }
        
        jGrid_employee = jQuery("#list_employee").jqGrid(
        {
            url:url+'m_employee/search_emp/'+nik+'/'+nama+'/'+dept+'/'+inactive,
            mtype : "POST", datatype: "json",
            colNames: colNamesT_employee , colModel: colModelT_employee ,
            rownumbers:true, viewrecords: true, multiselect: false, 
            caption: "Data Karyawan <?php echo $company_dest;?>", 
            rowNum:20, rowList:[10,20,30], multiple:true,
            height: 350, cellEdit: false,
            loadComplete: function(){ 
                var ids = jQuery("#list_employee").getDataIDs(); 
                for(var i=0;i<ids.length;i++) { 
                    var cl = ids[i];
                    ce = "<a href='#' onclick=\"lihat('"+cl+"');\"/ style='cursor:pointer'>detail</a>";
                    jQuery("#list_employee").setRowData(ids[i],{act:ce}) 
                }
            }, imgpath: gridimgpath, pager: jQuery('#pager_employee'), sortname: colModelT_employee[0].name
		});
        
		jGrid_employee.navGrid('#pager_employee',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
	jQuery("#list_employee").ready(loadView_employee);
        

	function getDialogButton( dialog_selector, button_name )
	{
		  var buttons = $( dialog_selector + ' .ui-dialog-buttonpane button' );
		  for ( var i = 0; i < buttons.length; ++i ) {
			 var jButton = $( buttons[i] );
			 if ( jButton.text() == button_name ) {
				 return jButton;
			 }
		  }
		  return null;
	}        
     
	//---------------------------------------------------------------- button submit data       
	function submit() 
	{
		var postdata = {}; 
		var ids = jQuery("#list_employee").getGridParam('selrow'); 
		var data = $("#list_employee").getRowData(ids) ; 
		var mode = $.trim($("#form_mode").val());
		var tmpNik =data.NIK; //nik sebelumnya
	
		if (mode == "GET") {
			var iNik = $("#i_nik").val();
		} else if(mode == "POST" || mode=="MTS") {
			if($("#i_nik").val()!="") {
				var iNik = $("#i_nik").val();  //nik baru
			} else {
				var iNik = $("#i_nik_ot").val(); //nik baru
			}
		}
		
		postdata['NIK'] = iNik ; //nik baru
		postdata['NAMA'] = $("#i_nama").val() ; 
		postdata['TANGGAL_LAHIR'] = $("#i_tgl_lahir").val() ; 
		postdata['TYPE_KARYAWAN'] = $("#i_type").val();
		postdata['GP'] = $("#i_gp").val();
		postdata['PANGKAT'] = $("#i_pangkat").val(); 
		postdata['JABATAN'] = $("#i_jabatan").val(); 
		postdata['COST_CENTER'] = $("#i_cc").val(); 
		postdata['DEPT_CODE'] = $("#i_dept").val();  
		postdata['ESTATE_CODE'] = $("#i_afd").val(); 
		postdata['DATE_JOIN'] = $("#i_tglmsk").val();
		postdata['DATE_PROMOTION'] = $("#i_tglpromosi").val();
		postdata['FAMILY_STATUS'] = $("#i_famstat").val();
		postdata['LAST_EDUCATION'] = $("#i_education").val();
		postdata['TAX_STATUS'] = $("#i_taxstat").val(); 
		postdata['ALAMAT'] = $("#i_alamat").val(); 
		postdata['PHONE'] = $("#i_telp").val(); 
		postdata['NO_JAMSOSTEK'] = $("#i_jamsostek").val(); 
		postdata['NO_NPWP'] = $("#i_npwp").val();
		postdata['DIVISION_CODE'] = $("#i_divisi").val();
		postdata['NO_IDENTITAS'] = $("#i_identitas").val(); 
		postdata['RELIGION'] = $("#i_religion").val(); 
		postdata['SEX'] = $("#i_sex").val(); 
		postdata['COMPANY_CODE'] = '<?php echo $company_code; ?>';
		
		var isActive = $("#i_active").is(':checked');
		if(isActive==true) {
			isActive=1;
		} else {
			isActive=0;
		}
		var bpjskes = $("#i_bpjskes").is(':checked');
		if(bpjskes==true) {
			bpjskes=1;
		} else {
			bpjskes=0;
		}
		var bpjstng = $("#i_bpjstng").is(':checked');
		if(bpjstng==true) {
			bpjstng=1;
		} else {
			bpjstng=0;
		}
		postdata['NAMA_SEKOLAH'] = $("#i_namasekolah").val();
		postdata['JURUSAN'] = $("#i_jurusan").val();
		postdata['ALAMAT_SEKOLAH'] = $("#i_alamatsekolah").val();
		postdata['ISBPJS_KETENAGAKERJAAN']=bpjstng;
		postdata['NO_REG_BPJS_TNG'] = $("#i_nobpjstng").val();
		postdata['ISBPJS_KESEHATAN']=bpjskes;
		postdata['NO_REG_BPJS_KES'] = $("#i_nobpjskes").val();
		postdata['INACTIVE']=isActive;
		postdata['NOTE']=$("#i_note").val(); 
		 
		if (iNik =='' && $("#i_nik").val()=="") {
			alert ("NIK tidak boleh kosong");
		} else {
			if (mode == "GET") {
				$.post( url+'m_employee/update/'+$("#i_nik").val(), postdata,function(status) { 
					var status = new String(status);
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							gridReload();
							alert('data berhasil terupdate.')
							$("#emp_form").dialog('close');    
						 } else {
							gridReload();
							$("#emp_form").dialog('close');    
						 } 
					} else { 
						gridReload();
						alert('data berhasil terupdate.')
						gridReload
					};
				});
			} else if (mode == "POST") {
				$.post(  url+'m_employee/create', postdata,function(status) { 
					var status = new String(status);
					if(status.replace(/\s/g,"") != "") { 
						 if(status==0){
							gridReload();
							alert('data berhasil tersimpan.') 
							$("#emp_form").dialog('close');      
						 } else {
							gridReload();
							alert(status);    
						 } 
					} else { 
						gridReload();
						alert('data berhasil tersimpan.')
						$("#emp_form").dialog('close');        
				   };  
			   });     
			} else if (mode=="MTS") {
			   postdata['TMP_NIK']=tmpNik;//simpan nik sebelumnya
			   $.post(  url+'m_employee/mutasi', postdata,function(status) { 
					var status = new String(status);
					if(status.replace(/\s/g,"") != "") { 
						alert(status); 
					} else { 
						gridReload();
						alert('data berhasil tersimpan.')
						$("#emp_form").dialog('close');        
				   };  
			  });
		   }
		}
	}

 function hapus() 
 {
   var postdata = {};
   var ids = jQuery("#list_employee").getGridParam('selrow'); 
   var data = $("#list_employee").getRowData(ids) ;
   var nik=data.NIK;
   postdata['NIK'] = nik;  
   if(data.NIK == undefined){
        alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
   } else {
        var answer = confirm ("Hapus Data Dari NIK : " + nik + ":" + data.NAMA + "?" )
        if (answer) {
           $.post( url+'m_employee/delete/'+nik+'/', postdata,function(message,status) { 
           if(status !== 'success') { 
                alert('data untuk tanggal ini sudah terisi.'); 
           } else { 
                alert('data berhasil terhapus.')
                gridReload();
           };  
       });
     }
   }
 }

function tambah()
{ 
    init_emp();
    var type=document.getElementById("i_type");
    type.disabled=false;    
	$('#i_jabatan').empty();
	$("#loadbutton").css("display","inline");
    $("#emp_form").dialog('open');
    $("#form_mode").val("POST");
    $('#i_nik_ot').removeAttr('disabled');
}

function ubah(){
    var ids = jQuery("#list_employee").getGridParam('selrow'); 
    var data = $("#list_employee").getRowData(ids) ;
    if (ids ==null) {
        alert("harap pilih data");
    } else {
        init_emp();
		$("#loadbutton").css("display","none");
        var type=document.getElementById("i_type");
        type.disabled=true;
        $('#i_type').attr('disabled','true')
        $("#tabs > ul").tabs({ selected: 1 });  
        $('#i_nik_ot').attr('disabled','true');

        $("#i_type").val(data.TYPE_KARYAWAN);
        $("#i_nik").val(data.NIK);
        $("#i_nama").val(data.NAMA);
        $("#i_tgl_lahir").val(data.TANGGAL_LAHIR);
        $("#i_dept").val(data.DEPT_CODE);
        $("#i_gp").val(data.GP);
		$("#i_pangkat").val(data.PANGKAT);
		$.post(url+'m_employee/LoadChain/'+$("#i_pangkat").val()+'/'+$("#i_dept").val()+'/', $("#i_pangkat").val(),
			function(datapost) 
			{ 
				$('#i_jabatan').empty();
				$("#i_jabatan").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				for (var i=0; i<datapost.length; i++) {
					$("#i_jabatan").get(0).add(new Option(
					datapost[i].kt,datapost[i].kt2), document.all ? i : null);
				}
				$("#i_jabatan").val(data.JABATAN);
				$("#i_jabatan").attr('style','display:inline; width:230px;');
		},"json");
        $("#i_cc").val(data.COST_CENTER); 
        $("#i_afd").val(data.ESTATE_CODE); 
        $("#i_famstat").val(data.FAMILY_STATUS);
		$("#i_education").val(data.LAST_EDUCATION);
        $("#i_taxstat").val(data.TAX_STATUS);
        $("#i_religion").val(data.RELIGION);
        $("#i_tglmsk").val(data.DATE_JOIN);
		$("#i_tglpromosi").val(data.DATE_PROMOTION); 
        $("#i_alamat").val(data.ALAMAT);  
        $("#i_telp").val(data.PHONE); 
        $("#i_jamsostek").val(data.NO_JAMSOSTEK);
		$("#i_npwp").val(data.NO_NPWP);
		$("#i_divisi").val(data.DIVISION_CODE);
		$("#i_identitas").val(data.NO_IDENTITAS);
        $("#i_sex").val(data.SEX);
        var isActive = data.INACTIVE;
        if (isActive==1) {
            $("#i_active").attr('checked',true);
        } else {
            $("#i_active").attr('checked',false);    
        }
		var isbpjstng = data.ISBPJS_KETENAGAKERJAAN;
        if (isbpjstng==1) {
            $("#i_bpjstng").attr('checked',true);
        } else {
            $("#i_bpjstng").attr('checked',false);    
        }
		var isbpjskes = data.ISBPJS_KESEHATAN;
        if (isbpjskes==1) {
            $("#i_bpjskes").attr('checked',true);
        } else {
            $("#i_bpjskes").attr('checked',false);    
        }
		$("#i_namasekolah").val(data.NAMA_SEKOLAH); 
        $("#i_jurusan").val(data.JURUSAN);
		$("#i_alamatsekolah").val(data.ALAMAT_SEKOLAH);
		$("#i_nobpjskes").val(data.NO_REG_BPJS_KES);
		$("#i_nobpjstng").val(data.NO_REG_BPJS_TNG);
        $("#i_note").val(data.NOTE);
        $("#emp_form").dialog('open');
        $("#form_mode").val("GET");            
    }
}

function mutasi()
{
    var ids = jQuery("#list_employee").getGridParam('selrow'); 
    var data = $("#list_employee").getRowData(ids) ;
	var aktif = $("#search_status").val();
    if (ids ==null) {
        alert("harap pilih data");
    } else {
		if ( aktif == '1' ){
			alert("pengangkatan tidak bisa dilakukan kepada karyawan yang inaktif!!!")
		} else {
			
        init_emp();
        $("#i_nik,#i_nama,#i_tgl_lahir,#i_dept,#i_gp,#i_jabatan,#i_pangkat,#i_cc,#i_afd,#i_famstat,#i_taxstat,#i_religion,#i_tglmsk,#i_tglpromosi,#i_alamat,#i_telp,#i_jamsostek,#i_npwp,#i_identitas,#i_sex")
        .attr('disabled','true');
        $("#i_type,#i_nik_ot").removeAttr('disabled');
        $("#tabstatus").attr('style','display:none');
         
        $("#i_type").val(data.TYPE_KARYAWAN);
        $("#i_nik").val(data.NIK);
        $("#i_nama").val(data.NAMA);
        $("#i_tgl_lahir").val(data.TANGGAL_LAHIR);
        $("#i_dept").val(data.DEPT_CODE);
        $("#i_gp").val(data.GP);
		$("#i_pangkat").val(data.PANGKAT);
        $("#i_jabatan").val(data.JABATAN);
        $("#i_cc").val(data.COST_CENTER); 
        $("#i_afd").val(data.ESTATE_CODE); 
        $("#i_famstat").val(data.FAMILY_STATUS);
		$("#i_education").val(data.LAST_EDUCATION);
        $("#i_taxstat").val(data.TAX_STATUS);
        $("#i_religion").val(data.RELIGION);
        $("#i_tglmsk").val(data.DATE_JOIN);
		$("#i_tglpromosi").val(data.DATE_PROMOTION);
        $("#i_alamat").val(data.ALAMAT);  
        $("#i_telp").val(data.PHONE); 
        $("#i_jamsostek").val(data.NO_JAMSOSTEK);
		$("#i_npwp").val(data.NO_NPWP);
		$("#i_divisi").val(data.DIVISION_CODE);
		$("#i_identitas").val(data.NO_IDENTITAS);
        $("#i_sex").val(data.SEX);
        var isActive = data.INACTIVE;
        if (isActive=="true")
        {
            $("#i_active").attr('checked',true);
        }
		var isbpjstng = data.ISBPJS_KETENAGAKERJAAN;
        if (isbpjstng==1) {
            $("#i_bpjstng").attr('checked',true);
        } else {
            $("#i_bpjstng").attr('checked',false);    
        }
		var isbpjskes = data.ISBPJS_KESEHATAN;
        if (isbpjskes==1) {
            $("#i_bpjskes").attr('checked',true);
        } else {
            $("#i_bpjskes").attr('checked',false);    
        }
		$("#i_namasekolah").val(data.NAMA_SEKOLAH); 
        $("#i_jurusan").val(data.JURUSAN);
		$("#i_alamatsekolah").val(data.ALAMAT_SEKOLAH);
		$("#i_nobpjskes").val(data.NO_REG_BPJS_KES);
		$("#i_nobpjstng").val(data.NO_REG_BPJS_TNG);
        $("#i_note").val(data.NOTE);
        $("#emp_form").dialog('open');
        $("#form_mode").val("MTS");  
		}
    }    
}
        
function lihat(nik)
{     
    var data = $("#list_employee").getRowData(nik) ;
    if (nik ==null) {
        alert("harap pilih data");
    } else {
        init_emp();
        var button = getDialogButton( '.dialog1', 'Simpan' );
        if ( button ) {
          button.attr('disabled', 'disabled' ).addClass( 'ui-state-disabled' );
        }
		
		$("#loadbutton").css("display","none");
        $("#i_nik,#i_nama,#i_tgl_lahir,#i_dept,#i_gp,#i_jabatan,#i_pangkat,#i_education,#i_cc,#i_afd,#i_famstat,#i_taxstat,#i_religion,#i_tglmsk,#i_tglpromosi,#i_alamat,#i_telp,#i_jamsostek,#i_npwp,#i_identitas,#i_sex,#i_type,#i_active,#i_note")
        .attr('disabled','true');
		
        $("#i_type").val(data.TYPE_KARYAWAN);
        $("#i_nik").val(data.NIK);
        $("#i_nama").val(data.NAMA);
        $("#i_tgl_lahir").val(data.TANGGAL_LAHIR);
        $("#i_dept").val(data.DEPT_CODE);
        $("#i_gp").val(data.GP); 
		$("#i_pangkat").val(data.PANGKAT);
        $.post(url+'m_employee/LoadChain/'+$("#i_pangkat").val()+'/'+$("#i_dept").val()+'/', $("#i_pangkat").val(),
			function(datapost) 
			{ 
				$('#i_jabatan').empty();
				$("#i_jabatan").get(0).add(new Option( " -- pilih -- ",""), document.all ? i : null);
				for (var i=0; i<datapost.length; i++) {
					$("#i_jabatan").get(0).add(new Option(
					datapost[i].kt,datapost[i].kt2), document.all ? i : null);
				}
				$("#i_jabatan").val(data.JABATAN);
				$("#i_jabatan").attr('style','display:inline; width:230px;');
		},"json");
        $("#i_cc").val(data.COST_CENTER); 
        $("#i_afd").val(data.ESTATE_CODE); 
        $("#i_famstat").val(data.FAMILY_STATUS);
		$("#i_education").val(data.LAST_EDUCATION);
        $("#i_taxstat").val(data.TAX_STATUS);
        $("#i_religion").val(data.RELIGION);
        $("#i_tglmsk").val(data.DATE_JOIN); 
		$("#i_tglpromosi").val(data.DATE_PROMOTION); 
        $("#i_alamat").val(data.ALAMAT);  
        $("#i_telp").val(data.PHONE); 
        $("#i_jamsostek").val(data.NO_JAMSOSTEK); 
		$("#i_npwp").val(data.NO_NPWP);
		$("#i_divisi").val(data.DIVISION_CODE); 
		$("#i_identitas").val(data.NO_IDENTITAS); 
        $("#i_sex").val(data.SEX);
        var isActive = data.INACTIVE;
        if (isActive=="true") {
            $("#i_active").attr('checked',true);
        }
		var isbpjstng = data.ISBPJS_KETENAGAKERJAAN;
        if (isbpjstng==1) {
            $("#i_bpjstng").attr('checked',true);
        } else {
            $("#i_bpjstng").attr('checked',false);    
        }
		var isbpjskes = data.ISBPJS_KESEHATAN;
        if (isbpjskes==1) {
            $("#i_bpjskes").attr('checked',true);
        } else {
            $("#i_bpjskes").attr('checked',false);    
        }
		$("#i_namasekolah").val(data.NAMA_SEKOLAH); 
        $("#i_jurusan").val(data.JURUSAN);
		$("#i_alamatsekolah").val(data.ALAMAT_SEKOLAH);
		$("#i_nobpjskes").val(data.NO_REG_BPJS_KES);
		$("#i_nobpjstng").val(data.NO_REG_BPJS_TNG);
        $("#i_note").val(data.NOTE);
        $("#emp_form").dialog('open');
        $("#form_mode").val("MTS");            
    }    
}

function init_emp()
{
    var button = getDialogButton( '.dialog1', 'Simpan' );
    if ( button ) {
        button.attr('disabled', '' ).removeClass( 'ui-state-disabled' );
    }
    
    $("#i_nama,#i_tgl_lahir,#i_dept,#i_gp,#i_jabatan,#i_pangkat,#i_education,#i_cc,#i_afd,#i_famstat,#i_taxstat,#i_religion,#i_tglmsk,#i_tglpromosi,#i_alamat,#i_telp,#i_jamsostek,#i_npwp,#i_identitas,#i_divisi,#i_sex,#i_active,#i_note").removeAttr('disabled');
    $("#tabstatus").attr('style','display:inline');  

    $("#i_type").val("");
    $("#i_nik").val("");
    $("#i_nik_ot").val("");
    $("#i_nama").val("");
    $("#i_tgl_lahir").val("");
    $("#i_dept").val("");
    $("#i_gp").val(""); 
    $("#i_pangkat").val("");
	$("#i_jabatan").val("");
	$("#i_education").val("");
    $("#i_cc").val(""); 
    $("#i_afd").val(""); 
    $("#i_famstat").val("");
    $("#i_taxstat").val("");
    $("#i_religion").val("");
    $("#i_tglmsk").val("");
	$("#i_tglpromosi").val("");
    $("#i_alamat").val("");  
    $("#i_telp").val(""); 
    $("#i_jamsostek").val("");
	$("#i_npwp").val("");
	$("#i_divisi").val("");
	$("#i_identitas").val("");
    $("#i_sex").val("");
    $("#i_active").attr('checked',false);
    $("#i_note").val("");
    
	$("#i_namasekolah").val("");
	$("#i_jurusan").val("");
	$("#i_alamatsekolah").val("");
	$("#i_nobpjskes").val("");
	$("#i_nobpjstng").val("");
	$("#i_bpjskes").attr('checked',false);
	$("#i_bpjstng").attr('checked',false);
    $("#emp_form").dialog('close');
    $("#form_mode").val('');   
}
      
	function testt()
	{
		$("#i_nik").val(""); 
	}
	
	function test12()
	{
		$("#i_nik_ot").val("");
	}
	
	function regenerateNIK(){
		$("#loader").attr('style','display:inline');
		if($('#i_type').val() != "" ) {
			$.post(  url+'m_employee/cek_emp/'+ $('#i_type').val() +'/reload', $("#i_nik"),function(status) { 
					var status = new String(status);
					$("#i_nik").val(status)  
					$("#i_nik_ot").val('')
					$("#loader").attr('style','display:none');
			  });
		} else {
			alert("mohon pilih tipe karyawan");
			$("#i_nik").val('');
			$("#i_nik_ot").val('');
			$("#loader").attr('style','display:none');
		}
	}
</script>

<script language="JavaScript" type="text/javascript"> 
$(document).ready(function() {
    $("#tabs").tabs();
	$("#loader").attr('style','display:none');
    //$("#form_natura").hide();
});

</script>
<br/>
<div id="form_employee">
    <table border="0" class="teks_" cellpadding="2" cellspacing="4">
        <tr><td colspan="8">Pencarian Berdasarkan :</td></tr>
        <tr>
        <td>NIK</td>
        <td>:</td>
        <td>
        <input type="text" class="input" id="search_nik" onkeydown="doSearch(arguments[0]||event)" />
        </td>

        <td>Nama</td>
        <td>:</td>
        <td>
        <input type="text" class="input" id="search_nama" onkeydown="doSearch(arguments[0]||event)" />
        </td>

        <td style="padding-left:15px;"></td>
        <td>Departemen</td>
        <td>:</td>
        <td><input type="text" class="input" id="search_dept" onkeydown="doSearch(arguments[0]||event)" />
        </td>

        <td style="padding-left:15px;">In-Aktif</td>
        <td>:</td>
        <td>
        <select tabindex="1" name='search_status' class='select' id='search_status' onchange="doSearch(arguments[0]||event)">
        <option value="0">AKTIF</option>
        <option value="1">IN-AKTIF</option>
        </select>
        </td>
        <td><?php //echo $glob_src; ?></td>
        </tr>
    </table>
    
    <table id="list_employee" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
    <div id="pager_employee" class="scroll"></div>
    <br/>
    
    <div id="save" class="scroll" style="float:left;">
        <input type="button"  id="add_data" value="Tambah" onclick="tambah()">&nbsp; 
    </div> 
    <div id="update" class="scroll" style="float:left;">
        <input type="button"  id="update_data" value="Ubah" onclick="ubah()">&nbsp;
    </div>
    <div id="delete" class="scroll" style="float:left;">
        <input type="button"  id="delete_data" value="Hapus" onclick="hapus()">&nbsp;
    </div>

    <?php if( $user_level == 'SAD' || 'ADM' ) { ?>  
    <div id="mutasi" class="scroll" style="float:left;">
        <input type="button"  id="mutasi_data" value="Pengangkatan Karyawan" onclick="mutasi()">&nbsp;
    </div>
    <?php } ?>
    
    <?php //if( $user_level == 'SAD' || 'ADM' ) { ?>
    <!-- <div id="natura" class="scroll" style="float:left;">
        <input type="button"  id="natura_view" value="Natura" onclick="natura()">
    </div> -->
    <?php //} ?>
</div>

<div id="emp_form">
<div id="tabs">
    <ul class="tabs">
        <li><a href="#fragment-1"><span>Detail Karyawan</span></a></li>
        <li id="tabstatus"><a href="#fragment-2"><span>Status</span></a></li>
        <li><a href="#fragment-3"><span>History</span></a></li>
    </ul>
    <div id="fragment-1" class="panes">
        <table width="100%" border="0" class="teks_" >
            <tr><td width="264">Type Karyawan</td>
            <td width="8">:</td>
            <td width="254">
            <select tabindex="1" name='emp_input' class='select' id='i_type' onchange="test12()">
            <option value=""> -- pilih -- </option>
            <option value="BULANAN">BULANAN</option>
            <option value="SKU">SKU</option>
            <option value="KDMP">KDMP</option>
            <option value="BHL">BHL</option>
            </select>
            </td>
              <td width="23">&nbsp;</td>
              <td width="224">Departemen</td>
              <td width="5">:</td>
              <td width="259"><? if(isset($dept)){ echo $dept; } ?></td>
            </tr>
            <tr>
            <td width="212">NIK</td>
            <td>:</td>
            <td>
            <input tabindex="10" type="text" style="text-transform: uppercase;" id="i_nik" class="input_disable" disabled="true" name="emp_input" maxlength="25"/>
            <div id="reloader" >
            	<img id="loadbutton" src="<?= $template_path ?>themes/base/images/Reloader.png" onclick="regenerateNIK()" />
            	<img id="loader" src="<?= $template_path ?>themes/base/images/loading.gif" height="15" width="15"/>
            </div> 
            </td>
              <td>&nbsp;</td>
              <td>Pangkat</td>
              <td>:</td>
              <td><? if(isset($level)){ echo $level; } ?></td>
            </tr>
            <tr>
                <td width="212"></td>
                <td>:</td>
                <td><input tabindex="2" type="text" style="text-transform: uppercase;" id="i_nik_ot"  class="input" onkeypress="testt()" name="i_nik_ot" maxlength="25"/></td>
                  <td width="23"></td>
                <td>Jabatan</td>
                <td>:</td>
                <td><select tabindex="12" name='i_jabatan' class='select' id='i_jabatan' style="width:230px;"></select></td>
                
          </tr>
            <tr><td>Nama</td><td>:</td>
            <td><input type="text" tabindex="3" style="text-transform: uppercase; width:170px;" class="input" id="i_nama" name="i_nama" maxlength="100"/></td>
              <td>&nbsp;</td>
              <td>Afdeling</td>
              <td>:</td>
              <td><? if(isset($afd)){ echo $afd; } ?></td>
            </tr>
            <tr><td>Tanggal Lahir</td><td>:</td><td><input tabindex="4" type="text"  style="width:100px;" class="input" id="i_tgl_lahir" name="i_tgl_lahir" maxlength="25"/></td>
              <td>&nbsp;</td>
              <td>Cost Center</td>
              <td>:</td>
              <td><? if(isset($costcenter)){ echo $costcenter; } ?></td>
            </tr>
            <tr>
              <td>Jenis Kelamin</td>
              <td>:</td>
              <td><select tabindex="5" name='emp_input' class='select' id='i_sex'>
                <option value=""> -- pilih -- </option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select></td>
              <td>&nbsp;</td>
              <td>Divisi</td>
              <td>:</td>
              <td><input tabindex="16" type="text" style="text-transform: uppercase;" class="input" id="i_divisi" name="i_divisi" maxlength="50"/></td>
            </tr>
            <tr><td>Agama</td><td>:</td>
            <td>
            <select tabindex="6" name='i_religion' class='select' id="i_religion">
              <option value=""> -- pilih -- </option>
              <option value="IS">Islam</option>
              <option value="KP">Kristen Protestan</option>
              <option value="KK">Katolik</option>
              <option value="HI">Hindu</option>
              <option value="BD">Buddha</option>
            </select>
            </td>
              <td>&nbsp;</td>
              <td>No. Identitas</td>
              <td>:</td>
              <td><input tabindex="16" type="text" style="text-transform: uppercase;" class="input" id="i_identitas" name="i_identitas" maxlength="50"/></td>
            </tr>
            <tr><td>Telepon</td><td>:</td><td><input tabindex="7" type="text" class="input" id="i_telp" name="i_telp" maxlength="50"/></td>

              <td>&nbsp;</td>
              <td>No. Jamsostek</td>
              <td>:</td>
              
              <td><input tabindex="17" type="text" class="input" id="i_jamsostek" name="i_jamsostek"/></td>
            </tr>
            <tr>
              <td>Status Keluarga</td>
              <td>:</td><td><? if(isset($famstat)){ echo $famstat; } ?></td>
              <td>&nbsp;</td>
              <td>No. NPWP</td>
              <td>:</td>
              <td><input tabindex="16" type="text" style="text-transform: uppercase; width:200px;" class="input" id="i_npwp" name="i_npwp" maxlength="50"/></td>
            </tr>
            <tr>
              <td>Status Pajak</td>
              <td>:</td><td><? if(isset($taxstat)){ echo $taxstat; } ?></td>
              <td>&nbsp;</td>
              <?php 
			  $usrole = $this->session->userdata('USER_LEVEL');
			  if($usrole == 'SAD' || $usrole == 'SAS'  || $usrole == 'PAYROLL' ||  $usrole == 'ADMSITE') {?>
              <td>BPJS Kesehatan</td>
              <td>:</td>
              <td><input type="checkbox" tabindex="18" id="i_bpjskes" style="margin-left: 5px;" name="i_bpjskes"/></td> <? } ?>
            </tr>
            <tr><td>Tingkat Pendidikan Terakhir</td><td>:</td>
            <td>
            <?php if(isset($education)){ echo $education; }?>
            </td>
              <td></td>
              <td>No. BPJS Kesehatan</td>
              <td>:</td>
              <td><input tabindex="10" type="text" style="width:150px;" class="input" id="i_nobpjskes" name="i_nobpjskes"/></td>
            </tr>
            <tr><td>Nama Sekolah Terakhir</td><td>:</td>
            <td>
            <input tabindex="10" type="text" style="width:100px;" class="input" id="i_namasekolah" name="i_namasekolah"/>
            </td>
              <td></td>
              <td>BPJS Ketenagakerjaan</td>
              <td>:</td>
              <td><input type="checkbox" tabindex="18" id="i_bpjstng" style="margin-left: 5px;" name="i_bpjstng"/></td>
            </tr>
            <tr><td>Jurusan</td><td>:</td>
            <td>
           <input tabindex="10" type="text" style="width:100px;" class="input" id="i_jurusan" name="i_jurusan"/>
            </td>
              <td></td>
              <td>No. BPJS Ketenagakerjaan</td>
              <td>:</td>
              <td><input tabindex="10" type="text" style="width:150px;" class="input" id="i_nobpjstng" name="i_nobpjstng"/></td>
            </tr>
            <tr><td>Alamat Sekolah</td><td>:</td>
            <td>
           <textarea tabindex="11" style="height:65px; width:270px;" class="input" id="i_alamatsekolah" name="i_alamatsekolah"></textarea>
            </td>
              <td></td>
              <td>Gaji Pokok</td>
              <td>:</td>
              <td><input tabindex="18" type="text" class="input" id="i_gp" name="i_gp" maxlength="50"/></td>
            </tr>
            <tr><td>Tanggal Masuk</td><td>:</td><td colspan="5"><input tabindex="10" type="text" style="width:100px;" class="input" id="i_tglmsk" name="i_tglmsk"/></td>
            </tr>
             <tr><td>Tanggal Pengangkatan</td><td>:</td><td colspan="5"><input tabindex="10" type="text" style="width:100px;" class="input" id="i_tglpromosi" name="i_tglpromosi"/></td>
            </tr>
            <tr><td>Alamat Tinggal</td><td>:</td><td colspan="5"><textarea tabindex="11" style="height:65px; width:270px;" class="input" id="i_alamat" name="i_alamat"></textarea>
            </td></tr>

            <!-- <tr><td>Deskripsi Kerja</td><td>:</td><td><input type="text" class="input" id="i_jobfunc"/></td></tr> -->
            <!-- <tr><td>Status</td><td>:</td><td><input type="checkbox" title="Aktif" class="input" id="i_status" value="1" /></td></tr> -->
            <tr><td></td><td></td>
            <td colspan="5"><input type="hidden" id="form_mode">
            
            </td>
            </tr>
        </table>    
    </div>
    <div id="fragment-2">
        <div id='inactive'>
        <table class="teks_">
            <tr>
                <td width="80px">In-Aktif</td><td>:</td>
                <td ><input type="checkbox" tabindex="18" id="i_active" style="margin-left: 5px;" name="emp_input"/></td>
            </tr>
            <tr>
            <td style="vertical-align: top">Catatan</td>
            <td style="vertical-align: top">:</td>
            <td colspan="5"><textarea tabindex="19" style="height:65px; width:300px;" class="input" id="i_note" name="i_note"></textarea>
            </td>
            </tr>
        </table>
        </div>   
    </div>
    <div id="fragment-3">
        
    </div>
    </div>
  
</div>
</body>