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
	$("#TGL_PROGRESS_PANEN").datepicker({dateFormat:"yy-mm-dd"});
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
	var tdate = document.getElementById("TGL_PROGRESS_PANEN").value;
	var tgl = tdate.replace(/-/gi, "");
	
	$.post('<?= base_url()?>index.php/p_progress_panen/cek_pp/'+tgl+'/'+lc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
				jQuery("#list_pp").setGridParam({url:url+"p_progress_panen/read_exist_act/"+tgl+'/'+lc}).trigger("reloadGrid")	
				} else {
	jQuery("#list_pp").setGridParam({url:url+"p_progress_panen/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
			}
	});
}

//-----------------------------------------------------grig-------------------------------------------
		var grid_pp = null;
        var colNamesT_pp = new Array();
        var colModelT_pp = new Array();
		
		colNamesT_pp.push('no');
        colModelT_pp.push({name:'ID_PROGRESS_PANEN',index:'ID_PROGRESS_PANEN', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_pp.push('TANGGAL');
        colModelT_pp.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_pp.push('KODE');
        colModelT_pp.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_pp.push('AKTIVITAS');
        colModelT_pp.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 320, align:'left'});

		colNamesT_pp.push('BLOK');
        colModelT_pp.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,
		editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_panen/get_active_block/"+document.getElementById("TGL_PROGRESS_PANEN").value.replace(/-/gi, ""), {
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
	                $("#LOCATION_CODE").val(item.res_id );
	              });
          }},
		width: 80, align:'center'});
		
		colNamesT_pp.push('NILAI');
        colModelT_pp.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width: 80, align:'center'});
		
		colNamesT_pp.push('UNIT');
        colModelT_pp.push({name:'SATUAN',index:'SATUAN', editable: false,hidden:false, width: 80, align:'center'});
		
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
				
		var loadView_pp = function()
        {
            jGrid_pp = jQuery("#list_pp").jqGrid(
            {
				url:url+'p_progress_panen/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_pp ,
                colModel: colModelT_pp ,
               	sortname: colNamesT_pp[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_pp"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_pp.navGrid('#pager_pp',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_pp").ready(loadView_pp);
		
		
		// Post it all 	   
		function post(){
		
		var postdata = {}; 
		    
			//Data dari form untuk gang_activity
		  	
		    postdata['TGL_PROGRESS_PNN'] = $("#TGL_PROGRESS_PANEN").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_pp").getDataIDs(); 
			postdata['jumlah_data'] = s;
			postdata['jumlahdt'] = jQuery('#list_pp').getGridParam('records');
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_pp").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_PANEN!== null){
					postdata['ID_PROGRESS_PANEN'+i] = data.ID_PROGRESS_PANEN;
				}
				postdata['TGL_PROGRESS_PANEN'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 
				
									
		       }); 
			   
			   
				 	   	
		$.post( url+'p_progress_panen/create_pp', postdata,function(status) { 
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
						var tgl = document.getElementById("TGL_PROGRESS_PANEN").value;
						var afd = document.getElementById("afd").value;
						var ids = jQuery("#list_pp").getDataIDs();
						var i = ids.length;
						//var i = jQuery('#list_lhm').getGridParam('records');
						
						/* if (tgl == ""){
							alert('tanggal progres tidak boleh kosong!');
						} else if (afd == ""){
							alert('kode afdeling tidak boleh kosong!');
						} else {	*/
							i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {ID_PROGRESS_PANEN:jdesc1};
							}
							
							sv = tgl;
				 		   	var su=jQuery("#list_pp").addRowData(i,datArr,"last");
							var sa=jQuery("#list_pp").setRowData(i,{no:i});
							jQuery("#list_pp").setRowData(i,{TGL_PROGRESS:sv})
							jQuery("#list_pp").setRowData(i,{ACTIVITY_CODE:"8601003"})
							jQuery("#list_pp").setRowData(i,{ACTIVITY_DESC:"PANEN"})
							//jQuery("#list_pp").setRowData(i,{SATUAN:"Kg"})
							jQuery("#list_pp").setRowData(i,{SATUAN:"Kg"})
							jQuery("#list_pp").jqGrid('editRow',i,true);

							//jQuery("#list_pp").editRow(ids,true);							
														
							
						//} 
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

<form id="p_panen">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="TGL_PROGRESS_PANEN" onChange="cek_act_block()"/></td></tr>
<tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?></td></tr>
</select>
</table>

<br/>

 <table id="list_pp" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_pp" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onClick="post()"></div>&nbsp;<div id="add" class="scroll" style="float:left;"><input type="button"  id="adddata" value="tambah" onClick="addrow()"></div>
</form>
</body>