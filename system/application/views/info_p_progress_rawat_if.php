<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
	
jQuery(document).ready(function(){
$(function () {
});

$(function() {
	$("#PROGRESS_DATE").datepicker({dateFormat:"yy-mm-dd"});
});


	
/*menu*/

$(function(){
				$('ul.jd_menu').jdMenu({	onShow: loadMenu	});
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
});


function cek_act_block(){
	var postdata = {}; 
	var lc =  $('#afd').val();
	var tdate = document.getElementById("PROGRESS_DATE").value;
	var tgl = tdate.replace(/-/gi, "");
	$.post('<?= base_url()?>index.php/p_progress_rawat_if/cek_prif/'+tgl+'/'+lc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
					jQuery("#list_prif").setGridParam({url:url+"p_progress_rawat_if/read_exist_act/"+tgl+"/"+lc}).trigger("reloadGrid")	
				} else {
					jQuery("#list_prif").setGridParam({url:url+"p_progress_rawat_if/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
				}
	
	});
}

		function giveActCode(){		
			var ids = jQuery("#list_prif").getGridParam('selrow'); 
			var rets = jQuery("#list_prif").getRowData(ids); 
			var type = rets.ACTIVITY_CODE;
			return type;
		} 
//-----------------------------------------------------grig-------------------------------------------
		var grid_prif = null;
        var colNamesT_prif = new Array();
        var colModelT_prif = new Array();
		
		colNamesT_prif.push('no');
        colModelT_prif.push({name:'ID_PROGRESS_RAWAT_IF',index:'ID_PROGRESS_RAWAT_IF', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_prif.push('TANGGAL');
        colModelT_prif.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
				
		colNamesT_prif.push('KODE');
        colModelT_prif.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,  
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_rawat_if/get_active_act/"+ document.getElementById("afd").value + "/" + document.getElementById("PROGRESS_DATE").value.replace(/-/gi, ""), {
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
	                $("#ACTIVITY_DESC").val(item.res_name );
					var id = jQuery("#list_prif").getGridParam('selrow');
					
					if (id) 
					{ 
						var ret = jQuery("#list_prif").getRowData(id);
						ret.ACTIVITY_DESC = (item.res_name);
						ret.SATUAN = (item.res_unit);
						jQuery("#list_prif").setRowData(id,{ACTIVITY_DESC:ret.ACTIVITY_DESC});
						jQuery("#list_prif").setRowData(id,{SATUAN:ret.SATUAN});
					}
	              });
          }}, width: 80, align:'center'});
		
		colNamesT_prif.push('AKTIVITAS');
        colModelT_prif.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 240, align:'left'});
		
		colNamesT_prif.push('LOKASI');
        colModelT_prif.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,
		edittype: "text", 
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_rawat_if/get_active_loc/"+ document.getElementById("PROGRESS_DATE").value.replace(/-/gi, "") + "/"+giveActCode(), {
	                  dataType: 'ajax',
	                  multiple: false,
	                  parse: function(data) { // parsing json input
	                      return $.map(eval(data), function(row) {
	                      return (typeof(row) == 'object')
	                        ? { data: row, value: row.res_id, result: row.res_id } // same in the serverside
	                        : { data: row, value: '',result: ''};
	                    });
	                  },
	                  formatItem: function(item) {
	                    return (typeof(item) == 'object')?item.res_id :'';
	                  }
	                }
	              )
	              .result(function(e, item) {
	                
	              });
          }}, width: 150, align:'left'});

		colNamesT_prif.push('NILAI');
        colModelT_prif.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width:80, align:'right'});
		
		colNamesT_prif.push('UNIT');
        colModelT_prif.push({name:'SATUAN',index:'SATUAN', editable: false,hidden:false, width:60, align:'center'});
				
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_prif = function()
        {
            jGrid_prif = jQuery("#list_prif").jqGrid(
            {
				url:url+'p_progress_rawat_if/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_prif ,
                colModel: colModelT_prif ,
               	sortname: colNamesT_prif[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_prif"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_prif.navGrid('#pager_prif',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_prif").ready(loadView_prif);
		
		/* simpan */
		function post() {
			
		   var postdata = {}; 
		  //Data dari form untuk gang_activity
		  	postdata['AFD'] = $("#afd").val() ; 
		    postdata['PROGRESS_DATE'] = $("#PROGRESS_DATE").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_prif").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_prif').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_prif").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_RAWAT_IF!== null){
					postdata['ID_PROGRESS_RAWAT_IF'+i] = data.ID_PROGRESS_RAWAT_IF ;
				}
				postdata['TGL_PROGRESS'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 
									
		       }); 
		
		   // Post it all 	   
		  $.post( url+'p_progress_rawat_if/create_prif', postdata,function(status) { 
				var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
				  		alert('data tersimpan');
						cek_act_block();		
		           };  
		     } ); 
		}
		
		function addrow(){
			var tgl = document.getElementById("PROGRESS_DATE").value;
			var afd = document.getElementById("afd").value;
			var ids = jQuery("#list_prif").getDataIDs();
			var i = ids.length;				
			if (tgl == ""){
				alert('tanggal progres tidak boleh kosong!');
			} else if (afd == ""){
				alert('kode sub tipe lokasi tidak boleh kosong!');
			} else {	
				i=i+1;	
				var datArr = {};
					if (i>1){
						var datArr = {ID_PROGRESS_RAWAT_IF:jdesc1};
					}
					sv = tgl;
		 		   	var su=jQuery("#list_prif").addRowData(i,datArr,"last");
					var sa=jQuery("#list_prif").setRowData(i,{no:i});
					jQuery("#list_prif").setRowData(i,{TGL_PROGRESS:sv})
					jQuery("#list_prif").jqGrid('editRow',i,true);				
			} 
		}
		
		function hapus(){
			
		    var postdata = {}; 
			var tdate = document.getElementById("PROGRESS_DATE").value;
			var tgl = tdate.replace(/-/gi, "");
			var lc =  $('#afd').val();
			var ids = jQuery("#list_prif").getGridParam('selrow'); 
			var data = $("#list_prif").getRowData(ids) ; 
		    
			postdata['ID'] = data.ID_PROGRESS_RAWAT_IF; 
			postdata['AFD'] = data.LOCATION_CODE; 
		    postdata['TGL'] = tgl;
			
			if(data.ID_PROGRESS_RAWAT_IF == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada aktivitas!!')
			} else {
				
			
				var answer = confirm ("Hapus Data " + data.ACTIVITY_CODE + " - " + data.ACTIVITY_DESC + " tgl " + data.TGL_PROGRESS  + " ?" )
				if (answer)
				{
				   $.post( url+'p_progress_rawat_if/delete', postdata,function(message,status) { 
			       if(status !== 'success') { 
			            	alert('data gagal terhapus.'); 
			          } else { 
							alert('data berhasil terhapus.')
							jQuery("#list_prif").setGridParam({url:url+'p_progress_rawat_if/read_exist_act/'+tgl+'/'+lc}).trigger("reloadGrid"); 
			        
							
			           };  
			      } );
	
				}	
			
			}	
		}
			
			
</script>
<style>
#loading
{
	position:absolute;
	top:0px;
	right:0px;
	background:#ff0000;
	color:#fff;
	font-size:14px;
	font-family:Verdana,Arial,sans-serif;
	padding:2px;
	display:none;
}
</style>
<div id="loading">Loading ...</div>
<form id="p_rawat_if">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="PROGRESS_DATE" onchange="cek_act_block()"/></td></tr>
<tr><td>Sub Type Infrastruktur</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?>
<select class="select" name="block" id="block" style="display:none" onchange="cek_act_block()"></select>
</td></tr>

</table>

<br/>

 <table id="list_prif" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_prif" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>&nbsp;
<div id="add" class="scroll" style="float:left;"><input type="button"  id="adddata" value="tambah" onclick="addrow()"></div>&nbsp;
<div id="del" class="scroll" style="float:left;"><input type="button"  id="deldata" value="hapus" onclick="hapus()"></div></form>
</form>
</body>