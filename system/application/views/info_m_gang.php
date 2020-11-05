<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){
    //$('#switcher').themeswitcher();
    $('.Table_Middle').hide(); 
    $('#form_empgang').hide();
    
	$("#bulan").change(function() {
	  gridReload_emp();
	});
	
	$("#tahun").change(function() {
	  gridReload_emp();
	});
    /*menu*/
        document.getElementById("search_gc").value = "";
        var url = "<?= base_url().'index.php/' ?>";
        
        /*-----------------------------lihat nik mandor--------------------------------------*/
        $(function () {
                $("#i_nikmandor")
                  .autocomplete( 
                    url+"m_gang/search_mandor/", {
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
                        
                    $("#i_namamandor").val(item.res_name);
                                   
                  });
                  
          });
          
         /*-----------------------------lihat nik kerani--------------------------------------*/
        $(function () {
                $("#i_nikkerani")
                  .autocomplete( 
                    url+"m_gang/search_kerani/", {
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
                                       
                  });
                  
          });

        /*-----------------------------lihat nik mandor 1--------------------------------------*/
        $(function () {
                $("#i_nikmandor1")
                  .autocomplete( 
                    url+"m_gang/search_mandori/", {
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
                                       
                  });
                  
          });
          
          /*-------------------lihat nik karyawan yang belum ada kemandoran----------------------------------*/
        $(function () {
                $("#i_nik_empgang")
                  .autocomplete( 
                    url+"m_gang/search_nik_kosong/"+jQuery("#tahun").val() + jQuery("#bulan").val(), {
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
                        
                    $("#i_nm_empgang").val(item.res_name);
                                   
                  });
                  
          });
});
    
    var url = "<?= base_url().'index.php/' ?>";
    
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
        var gc = jQuery("#search_gc").val(); 
            
        if (gc == ""){
            gc = "-";
        } 
        jQuery("#list_gang").setGridParam({url:url+"m_gang/search_gang/"+gc}).trigger("reloadGrid");        
    } 
	
    function doSearch_emp(ev){ 
        
        // var elem = ev.target||ev.srcElement; 
        if(timeoutHnd) 
            clearTimeout(timeoutHnd) 
            timeoutHnd = setTimeout(gridReload_emp,500) 
    } 
    
    function gridReload_emp(){ 
        var gc = $("#gc2").val();
        var nik=$("#search_nik").val();
        var name=$("#search_name").val();
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
           
        if (gc == ""){
            gc = "-";
        }
        if (nik == ""){
            nik = "-";
        }
        if (name == ""){
            name = "-";
        } 
        jQuery("#list_empgang").setGridParam({url:url+"m_empgang/search_empgang_detail/"+nik+"/"+name+"/"+gc+"/"+periode}).trigger("reloadGrid");        
    }
     
    function lihat_daftar(gc){
        $('#form_empgang').show();
        $("#gang").hide();
        $("#cari_kemandoran").hide();        
        $("#gc2").val(gc);
        $("#search_nik").val("");
        $("#search_name").val("");
    
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();
       
        jQuery("#list_empgang").setGridParam({url:url+"m_empgang/search_empgang/"+gc+"/"+periode}).trigger("reloadGrid");        
    }
    
    function kembali(){
        $('#form_empgang').hide();
        $("#gang").show();
        $("#cari_kemandoran").show();
        //jQuery("#list_empgang").setGridParam({url:url+"m_empgang/search_empgang/"+gc+"/"+periode}).trigger("reloadGrid");        
    }
/*grid*/

        var jGrid_gang = null;
        var colNamesT_gang = new Array();
        var colModelT_gang = new Array();    
                                                                         
colNamesT_gang.push('Kemandoran');
colModelT_gang.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 90, align:'center'});
 
colNamesT_gang.push('Nama Kemandoran');
colModelT_gang.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:false, width: 200, align:'left'});
 
colNamesT_gang.push('GANG_TYPE');
colModelT_gang.push({name:'GANG_TYPE',index:'GANG_TYPE', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_gang.push('MANDORE1_CODE');
colModelT_gang.push({name:'MANDORE1_CODE',index:'MANDORE1_CODE', editable: false, hidden:true, width: 70, align:'center'});

colNamesT_gang.push('Mandor');
colModelT_gang.push({name:'MANDORE_CODE',index:'MANDORE_CODE', editable: false, hidden:false, width: 70, align:'center'});

colNamesT_gang.push('Nama Mandor');
colModelT_gang.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 140, align:'left'});
 
colNamesT_gang.push('Kerani');
colModelT_gang.push({name:'KERANI_CODE',index:'KERANI_CODE', editable: false, hidden:false, width: 70, align:'center'});    
colNamesT_gang.push('Departemen');
colModelT_gang.push({name:'DEPARTEMEN_CODE',index:'DEPARTEMEN_CODE', editable: false, hidden:false, width: 100, align:'center'});    

colNamesT_gang.push('Divisi');
colModelT_gang.push({name:'DIVISION_CODE',index:'DIVISION_CODE', editable: false, hidden:false, width: 80, align:'center'});

colNamesT_gang.push('FUNCTION_CODE');
colModelT_gang.push({name:'FUNCTION_CODE',index:'FUNCTION_CODE', editable: false, hidden:true, width: 80, align:'center'});    
colNamesT_gang.push('GA_CODE');
colModelT_gang.push({name:'GA_CODE',index:'GA_CODE', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_gang.push('COMPANY_CODE');
colModelT_gang.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false, hidden:true, width: 80, align:'center'});

colNamesT_gang.push('');
colModelT_gang.push({name:'action',index:'action', editable: false, hidden:false, width: 100, align:'center'});    

    
    var loadView_gang = function()
        {
            var gc = jQuery("#search_gc").val(); 
        
            if (gc == ""){
                gc = "-";
            }
        
            jGrid_gang = jQuery("#list_gang").jqGrid(
            {
                url:url+'m_gang/search_gang/'+gc,
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_gang ,
                colModel: colModelT_gang ,
                rownumbers:true,
                viewrecords: true, 
                multiselect: false, 
                caption: "Data Kemandoran <?php echo $company_dest;?>", 
                //multikey: "ctrlKey", 
                rowNum:20,
                rowList:[10,20,30], 
                multiple:true,
                height: 320,
                cellEdit: false,
                loadComplete: function(){ 
                var ids = jQuery("#list_gang").getDataIDs(); 
                var id = jQuery("#list_gang").getGridParam('selrow'); 
                var rets = jQuery("#list_gang").getRowData(id); 
            
                for(var i=0;i<ids.length;i++)
                    { 
                        var cl = ids[i]; 
                        var desc = rets.DESCRIPTION;
                        ce = "<a href='#' onclick=\"lihat_daftar('"+cl+"','"+desc+"');\"/ style='cursor:pointer'>daftar karyawan</a>";
                        jQuery("#list_gang").setRowData(ids[i],{action:ce}) 
                    }
                                        
                }, 
                imgpath: gridimgpath,
                pager: jQuery('#pager_gang'),
                sortname: 'GANG_CODE'
                
                
            });
            jGrid_gang.navGrid('#pager_gang',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
        
        jQuery("#list_gang").ready(loadView_gang);
        
        $(function() {
            $("#gang_form").dialog({
                bgiframe: true, autoOpen: false,
                height: 320, width: 500,
                modal: true, title: "Form Entri Kemandoran",
                resizable: false, moveable: true,
                buttons: {
                     'Simpan Data Kemandoran': function() {
                            submit();        
					  },
					 'Tutup': function() {
							init_gang();        
                      }
                } 
            }); 
        });    

        function submit() {
            
            var postdata = {}; 
            var ids = jQuery("#list_gang").getGridParam('selrow'); 
            var data = $("#list_gang").getRowData(ids) ; 
            var mode = $("#form_mode").val();       
                    
            postdata['GANG_CODE'] = $("#i_gangcode").val() ; 
            postdata['DESCRIPTION'] = $("#i_gangname").val() ; 
            postdata['MANDORE_CODE'] = $("#i_nikmandor").val() ; 
            postdata['MANDORE1_CODE'] = $("#i_nikmandor1").val();
            postdata['KERANI_CODE'] = $("#i_nikkerani").val(); 
            postdata['DEPARTEMEN'] = $("#i_departemen").val(); 
            postdata['DIVISION'] = $("#i_divisi").val(); 
            
            if (mode == "GET"){
                $.post( url+'m_gang/update/'+$("#i_gangcode").val(), postdata,function(message,status) { 
                   if(status !== 'success') { 
                        alert('data untuk kemandoran ini sudah terisi.'); 
                  } else { 
                        gridReload();
                        alert('data berhasil tersimpan.')
                        
                   };
                  } );
            } else if (mode == "POST") {
                $.post( url+'m_gang/create', postdata,function(message,status) { 
                   if(status !== 'success') { 
                        alert('data untuk karyawan ini sudah terisi.'); 
                  } else { 
                        gridReload();
                        alert('data berhasil tersimpan.')
                        
                   };
                  } );
            }
             
        }
        
        function tambah(){
            init_gang();
            $("#gang_form").dialog('open');
            $("#form_mode").val("POST");
        }
        
        function ubah(){
            /* initiate data */        
            var ids = jQuery("#list_gang").getGridParam('selrow'); 
            var data = $("#list_gang").getRowData(ids) ; 
            $("#i_gangcode").val(data.GANG_CODE);
            $("#i_gangname").val(data.DESCRIPTION);
            $("#i_nikmandor").val(data.MANDORE_CODE);
            $("#i_namamandor").val(data.NAMA);
            $("#i_nikkerani").val(data.KERANI_CODE);
            $("#i_nikmandor1").val(data.MANDORE1_CODE);
            $("#i_departemen").val(data.DEPARTEMEN_CODE); 
            $("#i_divisi").val(data.DIVISION_CODE);
            $("#gang_form").dialog('open');
            $("#form_mode").val("GET");            
        }
        
        function init_gang(){
            $("#i_gangcode").val("");
            $("#i_gangname").val("");
            $("#i_nikmandor").val("");
            $("#i_namamandor").val("");
            $("#i_nikkerani").val("");
            $("#i_nikmandor1").val("");
            $("#i_departemen").val(""); 
            $("#i_divisi").val("");
            $("#gang_form").dialog('close');
            $("#form_mode").val('');
            
        }
        
        function hapus() {
            
           var postdata = {};
           var ids = jQuery("#list_gang").getGridParam('selrow'); 
           var data = $("#list_gang").getRowData(ids) ;
           postdata['GANG_CODE'] = data.GANG_CODE;  
           if(data.GANG_CODE == undefined){
                alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
            } else {
                
            
                var answer = confirm ("Hapus Data Dari Kemandoran : " + data.GANG_CODE + ":" + data.DESCRIPTION + "?" )
                if (answer)
                {
                       $.post( url+'m_gang/delete_gang/', postdata,function(message,status) { 
                           if(status !== 'success') { 
                                    alert('data untuk tanggal ini sudah terisi.'); 
                              } else { 
                                    
                                    alert('data berhasil terhapus.')
                                    gridReload();
                               };  
                          } );
                          }
                  }
        }    
        
        /* empgang */
        function gridReload_empgang(){ 
        var gc = jQuery("#gc2").val(); 
        var periode = jQuery("#tahun").val() + jQuery("#bulan").val();            
        if (gc == ""){
            gc = "-";
        } 
        
        jQuery("#list_empgang").setGridParam({url:url+"m_empgang/search_empgang/"+gc+"/"+periode}).trigger("reloadGrid");        } 


/*grid*/

        var jGrid_empgang = null;
        var colNamesT_empgang = new Array();
        var colModelT_empgang = new Array();    

colNamesT_empgang.push('no');
colModelT_empgang.push({name:'no',index:'no', editable: false, hidden:true, width: 90, align:'center'});
                                                                         
colNamesT_empgang.push('Kemandoran');
colModelT_empgang.push({name:'GANG_CODE',index:'GANG_CODE', editable: false, hidden:false, width: 90, align:'center'});
 
colNamesT_empgang.push('Nama Kemandoran');
colModelT_empgang.push({name:'DESCRIPTION',index:'DESCRIPTION', editable: false, hidden:false, width: 220, align:'left'});
 
colNamesT_empgang.push('NIK Anggota');
colModelT_empgang.push({name:'NIK',index:'NIK', editable: false, hidden:false, width: 100, align:'center'});

colNamesT_empgang.push('Nama');
colModelT_empgang.push({name:'NAMA',index:'NAMA', editable: false, hidden:false, width: 150, align:'left'});

colNamesT_empgang.push('');
colModelT_empgang.push({name:'',index:'', editable: false, hidden:true, width: 50, align:'center'});


    
    var loadView_empgang = function()
        {
            var gc = jQuery("#search_gc").val(); 
            var periode = jQuery("#tahun").val() + jQuery("#bulan").val(); 
        
            if (gc == ""){
                gc = "-";
            }
        
            jGrid_empgang = jQuery("#list_empgang").jqGrid(
            {
                url:url+'m_empgang/search_empgang/xx/xx',
                mtype : "POST",
                datatype: "json",
                colNames: colNamesT_empgang ,
                colModel: colModelT_empgang ,
                rownumbers:true,
                viewrecords: true, 
                multiselect: false, 
                caption: "Data Kemandoran <?php echo $company_dest;?>, Bulan : "+jQuery("#bulan").val()+" Tahun : "+ jQuery("#tahun").val(), 
                //multikey: "ctrlKey", 
                rowNum:20, rowList:[10,20,30], multiple:true,
                height: 300, width: 740, cellEdit: false,
                //cellsubmit: 'clientArray',
                imgpath: gridimgpath,
                pager: jQuery('#pager_empgang'),
                sortname: 'GANG_CODE'
            });
            jGrid_empgang.navGrid('#pager_empgang',{edit:false,add:false,del:false, search: false, refresh: true});
                        
        }
        
        jQuery("#list_empgang").ready(loadView_empgang);
                
        $(function() {
            $("#input_empgang").dialog({
                bgiframe: true,
                autoOpen: false,
                height: 250,
                width: 400,
                modal: true,
                title: "Karyawan Kemandoran",
                resizable: false,
                moveable: true,
                buttons: {
                    'Tutup': function() {
                                    init_empgang();        
                                }
                    
                } 
            }); 
        });    

        function submit_empgang() {
            
            var postdata = {}; 
            var ids = jQuery("#list_empgang").getGridParam('selrow'); 
            var data = $("#list_empgang").getRowData(ids) ; 
            var mode = $("#form_mode2").val();       
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
                    
            postdata['GANG_CODE'] = $("#i_gangcode2").val() ; 
            postdata['EMPLOYEE_CODE'] = $("#i_nik_empgang").val() ; 
            postdata['MONTH'] = bulan ; 
            postdata['YEAR'] = tahun;
           
            
            if (mode == "GET"){
                $.post( url+'m_empgang/edit_empgang/', postdata,function(message,status) { 
                   if(status !== 'success') { 
                        alert('data untuk kemandoran ini sudah terisi.'); 
                  } else { 
                        gridReload_empgang();
                        alert('data berhasil tersimpan.')
                        $("#input_empgang").dialog('close'); 
                   };
                  } );
            } else if (mode == "POST") {
                $.post( url+'m_empgang/create_empgang', postdata,function(message,status) { 
                   if(status !== 'success') { 
                        alert('data untuk karyawan ini sudah terisi.'); 
                  } else { 
                        gridReload_empgang();
                        alert('data berhasil tersimpan.')
                        $("#input_empgang").dialog('close');   
                   };
                  } );
            }
        }
        
        function tambah_empgang(){
            init_empgang();
            var gc = document.getElementById("gc2").value;
            $("#input_empgang").dialog('open');
            $("#i_gangcode2").val(gc);
            
            $("#i_gangcode2").attr('disabled','disabled');
            $("#form_mode2").val("POST");
        }
        
        function ubah_empgang(){
            /* initiate data */        
            var ids = jQuery("#list_empgang").getGridParam('selrow'); 
            var data = $("#list_empgang").getRowData(ids) ; 
            $("#i_gangcode2").val(data.GANG_CODE);
            $("#i_gangname2").val(data.DESCRIPTION);
            $("#i_nik_empgang").val(data.NIK); 
            $("#i_nm_empgang").val(data.NAMA);
            $("#input_empgang").dialog('open');
            $("#form_mode2").val("GET");            
        }
        
        function init_empgang(){
            $("#i_gangcode2").val("");
            $("#i_gangname2").val("");
            $("#i_nik_empgang").val("");
            $("#i_nm_empgang").val("");
            $("#form_mode2").val('');        
            $("#input_empgang").dialog('close')        
        }
        
        function hapus_empgang() {
            
           var postdata = {};
           var ids = jQuery("#list_empgang").getGridParam('selrow'); 
           var data = $("#list_empgang").getRowData(ids) ;
           var bulan = $("#bulan").val();
           var tahun = $("#tahun").val();

           postdata['GANG_CODE'] = data.GANG_CODE;
           postdata['EMPLOYEE_CODE'] = data.NIK;
           postdata['MONTH'] = bulan;
           postdata['YEAR'] = tahun;
             
           if(data.GANG_CODE == undefined){
                alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada nama!!')
            } else {
                
            
                var answer = confirm ("Hapus Data Dari Kemandoran : " + data.GANG_CODE + ":" + data.NAMA + "?" )
                if (answer)
                {
                       $.post( url+'m_empgang/delete_empgang/', postdata,function(message,status) { 
                           if(status !== 'success') { 
                                    alert('data untuk tanggal ini sudah terisi.'); 
                              } else { 
                                    
                                    alert('data berhasil terhapus.')
                                    gridReload_empgang();
                               };  
                          } );
                          }
                  }
        }    
</script>

<table id="cari_kemandoran" border="0" class="teks_" cellpadding="2" cellspacing="4">
<tr>
  <td width="150px">Cari Kode Kemandoran</td><td width="20px">:</td><td width="68%"><input type="text" class="input" id="search_gc" onkeydown="doSearch(arguments[0]||event)" /></td></tr>
</table>

 
<div id="gang" style="padding-top:10px;">
<table id="list_gang" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_gang" class="scroll"></div><br/>        
<div id="save" class="scroll" style="float:left;"><input type="button" class="basicBtn"  id="add" value="Tambah" onclick="tambah()">&nbsp; </div> <div id="update" class="scroll" style="float:left;"><input type="button"  id="update_data" class="basicBtn" value="Ubah" onclick="ubah()">&nbsp;</div><div id="delete" class="scroll" style="float:left;"><input type="button" class="basicBtn"  id="delete_data" value="Hapus" onclick="hapus()"></div> 

</div>
<div id="gang_form">
<table width="100%" border="0" class="teks_">
<tr><td height="18">Kode Kemandoran</td><td>:</td><td><input tabindex="1" type="text" style="text-transform: uppercase;" class="input" id="i_gangcode"/></td></tr>
<tr><td height="18">Nama Kemandoran</td><td>:</td><td><input tabindex="2" type="text" style="width:170px;text-transform: uppercase;" class="input" id="i_gangname"/></td></tr>
<tr><td height="18">NIK Mandor</td><td>:</td><td><input tabindex="3" type="text" style="text-transform: uppercase;" class="input" id="i_nikmandor"/> </td> </tr>
<tr><td height="18">Nama Mandor</td><td>:</td><td><input tabindex="4" type="text" disabled="true" style="text-transform: uppercase;" class="input_disable" id="i_namamandor"/></td></tr>
<tr><td height="18">NIK Kerani</td><td>:</td><td><input tabindex="5" type="text" style="text-transform: uppercase;" class="input" id="i_nikkerani"/></td></tr>
<tr><td height="18">NIK Mandor 1</td><td>:</td><td><input tabindex="7" type="text" style="text-transform: uppercase;" class="input" id="i_nikmandor1"/></td></tr>
<tr><td height="18">Departemen</td><td>:</td><td><input tabindex="8" type="text" style="text-transform: uppercase;" class="input" id="i_departemen"/></td></tr>
<tr><td height="18">Divisi / Afdeling</td><td>:</td><td><input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_divisi"/></td></tr>

<tr><td height="18">Fungsi</td><td>:</td><td><input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_function"/></td></tr>
<tr><td height="18">Inaktif</td><td>:</td><td><input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_inactive"/></td></tr>

<tr><td height="18">Input</td><td>:</td>
<td>
	<input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_inputby" disabled="disabled"/> &nbsp;&nbsp;
    <input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_inputdate" disabled="disabled"/>
</td></tr>
<tr><td height="18">Update</td><td>:</td>
<td>
	<input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_update"disabled="disabled" /> &nbsp;&nbsp;
    <input tabindex="9" type="text" style="text-transform: uppercase;" class="input" id="i_updatedate" disabled="disabled"/>
	<input type="hidden" id="form_mode">
</td></tr>

<tr><td></td><td></td></tr>

</table>

</div>

<!-- KARYAWAN KEMANDORAN -->
<div id="form_empgang">
<a href="#" onclick="kembali()" style='cursor:pointer; font-size:13px'>kembali ke kemandoran</a>
<div id"gridSearch" style="padding-bottom:10px; padding-top:10px;">  
    <!--<div><?php //echo $search; ?></div> -->
    
    <table border="0" class="teks_"cellpadding="2" cellspacing="4">
    	<tr>
        <td colspan="9" height="15px">Pencarian Berdasarkan :</td>
        </tr>
        <tr>
        <td width="60px">Kode</td>
        <td width="10px">:</td>
        <td width="160px"><input type="text" class="input" id="search_nik" onkeydown="doSearch_emp(arguments[0]||event)" /></td>
        <td width="60px">Nama</td>
        <td width="10px">:</td>
        <td width="160px"> <input type="text" class="input" id="search_name" onkeydown="doSearch_emp(arguments[0]||event)" /></td>
        <td width="60px">Periode</td>
        <td width="10px">:</td>
          <td width="220px">
           <? echo $periode; ?>
            </td>
        </tr>
    </table>
</div>

<table id="list_empgang" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_empgang" class="scroll"></div><br/>
<div id="save_empgang" class="scroll" style="float:left;">
<input type="button"  id="add_empgang" value="Tambah" class="basicBtn" onclick="tambah_empgang()">&nbsp;
</div>&nbsp;
<div id="delete_empgang" class="scroll" style="float:left;">
<input type="button" id="delete_data_empgang" class="basicBtn"  value="Hapus" onclick="hapus_empgang()"></div> 
</div>
<div id="input_empgang">
<table width="100%" border="0" class="teks_">
<tr><td>Kode Kemandoran</td><td>:</td><td><input tabindex="1" type="text" style="text-transform: uppercase;" class="input" id="i_gangcode2"/></td></tr>
<tr><td>Nama Kemandoran</td><td>:</td><td><input disabled="ttue" tabindex="1" type="text" style="text-transform: uppercase;" class="input_disable" id="i_gangname2"/></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $speriode; ?></td></tr>
<tr><td>Kode Karyawan</td><td>:</td><td><input tabindex="1" type="text" style="text-transform: uppercase;" class="input" id="i_nik_empgang"/></td></tr>
<tr><td>Nama Karyawan</td><td>:</td><td><input tabindex="1" type="text" style="text-transform: uppercase;" class="input" id="i_nm_empgang"/></td></tr>
<tr><td></td><td></td><td colspan="3"><input type="hidden" id="form_mode2"><input type="hidden" id="gc2"><input type="hidden" id="gc2_name">
<input tabindex="17" type="button" id="submitdata_empgang" value="Simpan" onclick="submit_empgang()"></td></tr>

</table>

</div>


</body>
