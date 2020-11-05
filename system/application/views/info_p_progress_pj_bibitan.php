<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
	
jQuery(document).ready(function(){
$(function () {
});

$(function() {
	$("#PROGRESS_TGL_BIBITAN").datepicker({dateFormat:"yy-mm-dd"});
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
	var tdate = document.getElementById("PROGRESS_TGL_BIBITAN").value;
	var tgl = tdate.replace(/-/gi, "");
	$.post('<?= base_url()?>index.php/p_progress_pj_bibitan/cek_pb/'+tgl, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
		jQuery("#list_pjb").setGridParam({url:url+"p_progress_pj_bibitan/read_exist_act/"+tgl}).trigger("reloadGrid")	
				} else {
	jQuery("#list_pjb").setGridParam({url:url+"p_progress_pj_bibitan/read_act/"+tgl}).trigger("reloadGrid")
					}
	
	});
}
		function giveActCode(){		
			var ids = jQuery("#list_pjb").getGridParam('selrow'); 
			var rets = jQuery("#list_pjb").getRowData(ids); 
			var type = rets.ACTIVITY_CODE;
			return type;
		} 
		
//-----------------------------------------------------grid-------------------------------------------
		var grid_pb = null;
        var colNamesT_pjb = new Array();
        var colModelT_pjb = new Array();
		
		colNamesT_pjb.push('no');
        colModelT_pjb.push({name:'ID_PROGRESS_PJBIBITAN',index:'ID_PROGRESS_PJBIBITAN', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_pjb.push('TANGGAL');
        colModelT_pjb.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
				
		colNamesT_pjb.push('KODE');
        colModelT_pjb.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true,
		edittype: "text", 
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_pj_bibitan/get_active_act/"+ document.getElementById("PROGRESS_TGL_BIBITAN").value.replace(/-/gi, ""), {
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
					var id = jQuery("#list_pjb").getGridParam('selrow');
					
					if (id) 
					{ 
						var ret = jQuery("#list_pjb").getRowData(id);
						ret.ACTIVITY_DESC = (item.res_name);
						ret.SATUAN = (item.res_unit);
						jQuery("#list_pjb").setRowData(id,{ACTIVITY_DESC:ret.ACTIVITY_DESC});
						jQuery("#list_pjb").setRowData(id,{SATUAN:ret.SATUAN});
					}
	              });
          }}, width: 70, align:'center'});
		
		colNamesT_pjb.push('AKTIVITAS');
        colModelT_pjb.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 390, align:'left'});
		
		colNamesT_pjb.push('BLOK');
        colModelT_pjb.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,
		edittype: "text", 
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_pj_bibitan/get_active_loc/"+ document.getElementById("PROGRESS_TGL_BIBITAN").value.replace(/-/gi, "") + "/"+giveActCode(), {
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
          }}, width: 70, align:'center'});

		colNamesT_pjb.push('NILAI');
        colModelT_pjb.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width:70, align:'center'});
		
		colNamesT_pjb.push('UNIT');
        colModelT_pjb.push({name:'SATUAN',index:'SATUAN', editable: false,hidden:false, width:60, align:'center'});
				
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_pb = function()
        {
            jgrid_pb = jQuery("#list_pjb").jqGrid(
            {
				url:url+'p_progress_pj_bibitan/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_pjb ,
                colModel: colModelT_pjb ,
               	sortname: colNamesT_pjb[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_pjb"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jgrid_pb.navGrid('#pager_pjb',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_pjb").ready(loadView_pb);
		
		// Post it all 	   
		function post(){
		
		var postdata = {}; 
		    
			//Data dari form untuk gang_activity	  	
		    postdata['PROGRESS_TGL_BIBITAN'] = $("#PROGRESS_TGL_BIBITAN").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_pjb").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_pjb').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_pjb").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_RAWAT!== null){
					postdata['ID_PROGRESS_PJBIBITAN'+i] = data.ID_PROGRESS_PJBIBITAN ;
				}
				postdata['PROGRESS_TGL_BIBITAN'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['TGL_PROGRESS'+i] = data.TGL_PROGRESS ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 
			
					
		       }); 
			   	
		$.post( url+'p_progress_pj_bibitan/create_pb', postdata,function(status) { 
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
			var tgl = document.getElementById("PROGRESS_TGL_BIBITAN").value;
			var ids = jQuery("#list_pjb").getDataIDs();
			var i = ids.length;				
				if (tgl == ""){
					alert('tanggal progres tidak boleh kosong!');
				} else {	
					i=i+1;	
					var datArr = {};
					if (i>1){
						var datArr = {ID_PROGRESS_PJBIBITAN:jdesc1};
					}
					sv = tgl;
				   	var su=jQuery("#list_pjb").addRowData(i,datArr,"last");
					var sa=jQuery("#list_pjb").setRowData(i,{no:i});
					jQuery("#list_pjb").setRowData(i,{TGL_PROGRESS:sv})
					jQuery("#list_pjb").jqGrid('editRow',i,true);				
				} 
		}		
		
		function hapus(){
			
		    var postdata = {}; 
			var tdate = document.getElementById("PROGRESS_TGL_BIBITAN").value;
			var tgl = tdate.replace(/-/gi, "");
			var ids = jQuery("#list_pjb").getGridParam('selrow'); 
			var data = $("#list_pjb").getRowData(ids) ; 
		    
			postdata['ID'] = data.ID_PROGRESS_PJBIBITAN; 
			postdata['AFD'] = data.LOCATION_CODE; 
		    postdata['TGL'] = tgl;
			
			if(data.ID_PROGRESS_PJBIBITAN == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada aktivitas!!')
			} else {
				
			
				var answer = confirm ("Hapus Data " + data.ACTIVITY_CODE + " - " + data.ACTIVITY_DESC + " tgl " + data.TGL_PROGRESS  + " ?" )
				if (answer)
				{
				   $.post( url+'p_progress_pj_bibitan/delete', postdata,function(message,status) { 
			       if(status !== 'success') { 
			            	alert('data gagal terhapus.'); 
			          } else { 
							alert('data berhasil terhapus.')
							jQuery("#list_pjb").setGridParam({url:url+'p_progress_pj_bibitan/read_exist_act/'+tgl}).trigger("reloadGrid"); 
			        
							
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

<form id="p_bibitan">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" onchange="cek_act_block()" size=15 id="PROGRESS_TGL_BIBITAN"/></td></tr>
</table>

<br/>

 <table id="list_pjb" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_pjb" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>&nbsp;
<div id="add" class="scroll" style="float:left;"><input type="button"  id="adddata" value="tambah" onclick="addrow()"></div>&nbsp;
<div id="del" class="scroll" style="float:left;"><input type="button"  id="deldata" value="hapus" onclick="hapus()"></div></form>
</form>
</body>