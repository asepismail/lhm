<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
	
jQuery(document).ready(function(){
$(function () {
//document.getElementById("kode_ma").value = "";
//document.getElementById("sat_unit_ma").value = "";
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
	$.post('<?= base_url()?>index.php/p_progress_rawat/cek_pr/'+tgl+'/'+lc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
					jQuery("#list_pr").setGridParam({url:url+"p_progress_rawat/read_exist_act/"+tgl+"/"+lc}).trigger("reloadGrid")	
				} else {
					jQuery("#list_pr").setGridParam({url:url+"p_progress_rawat/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
				}
	
	});
}

	function giveActCode(){		
		var ids = jQuery("#list_pr").getGridParam('selrow'); 
		var rets = jQuery("#list_pr").getRowData(ids); 
		var type = rets.ACTIVITY_CODE;
		return type;
	} 
//-----------------------------------------------------grig-------------------------------------------
		var grid_pr = null;
        var colNamesT_pr = new Array();
        var colModelT_pr = new Array();
		
		colNamesT_pr.push('no');
        colModelT_pr.push({name:'ID_PROGRESS_RAWAT',index:'ID_PROGRESS_RAWAT', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_pr.push('TANGGAL');
        colModelT_pr.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
				
		colNamesT_pr.push('KODE');
		colModelT_pr.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: true, edittype: "text", 
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_rawat/get_active_act/"+ document.getElementById("afd").value + "/" + document.getElementById("PROGRESS_DATE").value.replace(/-/gi, ""), {
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
					var id = jQuery("#list_pr").getGridParam('selrow');
					
					if (id) 
					{ 
						var ret = jQuery("#list_pr").getRowData(id);
						ret.ACTIVITY_DESC = (item.res_name);
						jQuery("#list_pr").setRowData(id,{ACTIVITY_DESC:ret.ACTIVITY_DESC});

					}
	              });
          }},
		width: 80, align:'center'});
      		
		colNamesT_pr.push('AKTIVITAS');
        colModelT_pr.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 240, align:'left'});
		
		colNamesT_pr.push('BLOK');
        colModelT_pr.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,
		edittype: "text", 
		editoptions:{
				size:50,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_rawat/get_active_loc/"+ document.getElementById("afd").value + "/" + document.getElementById("PROGRESS_DATE").value.replace(/-/gi, "") + "/"+giveActCode(), {
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
          }},
		  hidden:false, width: 80, align:'center'});

		colNamesT_pr.push('NILAI');
        colModelT_pr.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width:80, align:'right'});
		
		colNamesT_pr.push('UNIT');
        colModelT_pr.push({name:'SATUAN',index:'SATUAN', editable: true,hidden:false, width:60, align:'center'});
				
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_pr = function()
        {
            jGrid_pr = jQuery("#list_pr").jqGrid(
            {
				url:url+'p_progress_rawat/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_pr ,
                colModel: colModelT_pr ,
               	sortname: colNamesT_pr[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_pr"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_pr.navGrid('#pager_pr',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_pr").ready(loadView_pr);
		
		/* simpan */
		function post() {
			
		   var postdata = {}; 
		  //Data dari form untuk gang_activity
		  	postdata['AFD'] = $("#afd").val() ; 
		    postdata['PROGRESS_DATE'] = $("#PROGRESS_DATE").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_pr").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_pr').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_pr").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_RAWAT!== null){
					postdata['ID_PROGRESS_RAWAT'+i] = data.ID_PROGRESS_RAWAT ;
				}
				postdata['TGL_PROGRESS'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 
									
		       }); 
		
		   // Post it all 	   
		  $.post( url+'p_progress_rawat/create_pr', postdata,function(status) { 
		   		//alert(status)
				var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
				  		alert('data tersimpan');
						//$("#submitdata").hide();
						//$("#updatedata").show();
						//$("#delete").show();
						cek_act_block();		
		           };  
		     } ); 
		}
		
		function addrow(){
						var tgl = document.getElementById("PROGRESS_DATE").value;
						var afd = document.getElementById("afd").value;
						var ids = jQuery("#list_pr").getDataIDs();
						var i = ids.length;
							
						if (tgl == ""){
							alert('tanggal progres tidak boleh kosong!');
						} else if (afd == ""){
							alert('kode afdeling tidak boleh kosong!');
						} else {	
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {ID_PROGRESS_RAWAT:jdesc1};
							}
							
							sv = tgl;
				 		   	var su=jQuery("#list_pr").addRowData(i,datArr,"last");
							var sa=jQuery("#list_pr").setRowData(i,{no:i});
							jQuery("#list_pr").setRowData(i,{TGL_PROGRESS:sv})
							jQuery("#list_pr").jqGrid('editRow',i,true);

							//jQuery("#list_pp").editRow(ids,true);							
														
							
						} 
		}
		
		function hapus(){
			
		    var postdata = {}; 
			var tdate = document.getElementById("PROGRESS_DATE").value;
			var tgl = tdate.replace(/-/gi, "");
			var lc =  $('#afd').val();
			var ids = jQuery("#list_pr").getGridParam('selrow'); 
			var data = $("#list_pr").getRowData(ids) ; 
		    
			postdata['ID'] = data.ID_PROGRESS_RAWAT; 
			postdata['AFD'] = lc; 
		    postdata['TGL'] = tgl;
			
			if(data.ID_PROGRESS_RAWAT == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada aktivitas!!')
			} else {
				
			
				var answer = confirm ("Hapus Data " + data.ACTIVITY_CODE + " - " + data.ACTIVITY_DESC + " tgl " + data.TGL_PROGRESS  + " ?" )
				if (answer)
				{
				   $.post( url+'p_progress_rawat/delete', postdata,function(message,status) { 
			       if(status !== 'success') { 
			            	alert('data gagal terhapus.'); 
			          } else { 
							alert('data berhasil terhapus.')
							jQuery("#list_pr").setGridParam({url:url+'p_progress_rawat/read_exist_act/'+tgl+'/'+lc}).trigger("reloadGrid"); 
			        
							
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

<form id="p_rawat">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="PROGRESS_DATE" onchange="cek_act_block()"/></td></tr>
<tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?>
<select class="select" name="block" id="block" style="display:none" onchange="cek_act_block()"></select>
</td></tr>

</table>

<br/>

 <table id="list_pr" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_pr" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>&nbsp;
<div id="add" class="scroll" style="float:left;"><input type="button"  id="adddata" value="tambah" onclick="addrow()"></div>&nbsp;
<div id="del" class="scroll" style="float:left;"><input type="button"  id="deldata" value="hapus" onclick="hapus()"></div>
</form>
</body>