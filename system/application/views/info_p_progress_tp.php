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
	$("#TGL_PROGRESS_TP").datepicker({dateFormat:"yy-mm-dd"});
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
	var tdate = document.getElementById("TGL_PROGRESS_TP").value;
	var tgl = tdate.replace(/-/gi, "");
	
	$.post('<?= base_url()?>index.php/p_progress_tp/cek_tp/'+tgl+'/'+lc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
					
					jQuery("#list_tp").setGridParam({url:url+"p_progress_tp/read_exist_act/"+tgl}).trigger("reloadGrid")	
				
				} else {
					jQuery("#list_tp").setGridParam({url:url+"p_progress_tp/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
			}
	});
}

//-----------------------------------------------------grig-------------------------------------------
		var grid_tp = null;
        var colNamesT_tp = new Array();
        var colModelT_tp = new Array();
		
		colNamesT_tp.push('no');
        colModelT_tp.push({name:'ID_PROGRESS_TP',index:'ID_PROGRESS_TP', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_tp.push('TANGGAL');
        colModelT_tp.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_tp.push('KODE');
        colModelT_tp.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_tp.push('AKTIVITAS');
        colModelT_tp.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 320, align:'left'});

		colNamesT_tp.push('BLOK');
        colModelT_tp.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: true,hidden:false, 
				editoptions:{
				size:12,
	            maxlength:40,
	            dataInit:function (elem) {
	            $(elem)
	              .autocomplete( 
	                url+"p_progress_tp/get_active_block/"+document.getElementById("TGL_PROGRESS_TP").value.replace(/-/gi, ""), {
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
          }}, width: 80, align:'center'});
		
		colNamesT_tp.push('NILAI');
        colModelT_tp.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width: 80, align:'center'});
		
		colNamesT_tp.push('UNIT');
        colModelT_tp.push({name:'SATUAN',index:'SATUAN', editable: false,hidden:false, width: 80, align:'center'});
		
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_tp = function()
        {
            jGrid_tp = jQuery("#list_tp").jqGrid(
            {
				url:url+'p_progress_tp/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_tp ,
                colModel: colModelT_tp ,
               	sortname: colNamesT_tp[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_tp"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_tp.navGrid('#pager_tp',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
        }
        jQuery("#list_tp").ready(loadView_tp);
		
		
		// Post it all 	   
		function post(){
		
		var postdata = {}; 
		    
			//Data dari form untuk gang_activity
		  	postdata['AFD'] = $("#afd").val() ; 
		    postdata['TGL_PROGRESS_TP'] = $("#TGL_PROGRESS_TP").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_tp").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_tp').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_tp").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_TP!== null){
					postdata['ID_PROGRESS_TP'+i] = data.ID_PROGRESS_TP;
				}
				postdata['TGL_PROGRESS'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 							
		       }); 


		$.post( url+'p_progress_tp/create_tp', postdata,function(status) { 
		   		//alert(status)
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
						var tgl = document.getElementById("TGL_PROGRESS_TP").value;
						var afd = document.getElementById("afd").value;
						var ids = jQuery("#list_tp").getDataIDs();
						var i = ids.length;
						i=i+1;	
							var datArr = {};
							if (i>1){
								var datArr = {ID_PROGRESS_PANEN:jdesc1};
							}
							
							sv = tgl;
				 		   	var su=jQuery("#list_tp").addRowData(i,datArr,"last");
							var sa=jQuery("#list_tp").setRowData(i,{no:i});
							jQuery("#list_tp").setRowData(i,{TGL_PROGRESS:sv})
							jQuery("#list_tp").setRowData(i,{ACTIVITY_CODE:"8602002"})
							jQuery("#list_tp").setRowData(i,{ACTIVITY_DESC:"LANGSIR MANUAL"})
							jQuery("#list_tp").setRowData(i,{SATUAN:"Jjg"})
							jQuery("#list_tp").jqGrid('editRow',i,true);

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
<div id="loading">Loading ...</div>

<form id="p_tp">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="TGL_PROGRESS_TP" onchange="cek_act_block()"/></td></tr>
<tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?></td></tr>
<select class="select" name="block" id="block" onchange="cek_act_block()" style="display:none"></select>
</table>

<br/>

 <table id="list_tp" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_tp" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>
<div id="add" class="scroll" style="float:left;"><input type="button"  id="adddata" value="tambah" onclick="addrow()"></div>
</form>
</body>