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
	
	$.post('<?= base_url()?>index.php/p_progress_lc/cek_lc/'+tgl+'/'+lc, postdata, function(data){
				for (var i = 0; i < data.length ; i++ ){
					jumlah = data;
				}
				if (jumlah != 0){
		jQuery("#list_lc").setGridParam({url:url+"p_progress_lc/read_exist_act/"+tgl+"/"+lc}).trigger("reloadGrid")
				} else {
		jQuery("#list_lc").setGridParam({url:url+"p_progress_lc/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
				}
		});

}

//-----------------------------------------------------grig-------------------------------------------
		var grid_lc = null;
        var colNamesT_lc = new Array();
        var colModelT_lc = new Array();
		
		colNamesT_lc.push('no');
        colModelT_lc.push({name:'ID_PROGRESS_LC',index:'ID_PROGRESS_LC', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_lc.push('TANGGAL');
        colModelT_lc.push({name:'TGL_PROGRESS',index:'TGL_PROGRESS', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_lc.push('KODE');
        colModelT_lc.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 120, align:'center'});
		
		colNamesT_lc.push('AKTIVITAS');
        colModelT_lc.push({name:'ACTIVITY_DESC',index:'ACTIVITY_DESC', editable: false,hidden:false, width: 320, align:'left'});
		
		colNamesT_lc.push('LOKASI');
        colModelT_lc.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false,hidden:false, width: 100, align:'center'});
			
		colNamesT_lc.push('NILAI');
        colModelT_lc.push({name:'HASIL_KERJA',index:'HASIL_KERJA', editable: true,hidden:false, width:80, align:'center'});
		
		colNamesT_lc.push('UNIT');
        colModelT_lc.push({name:'SATUAN',index:'SATUAN', editable: false,hidden:false, width:70, align:'center'});
		
	//	colNamesT_lc.push('NILAI2');
     //   colModelT_lc.push({name:'HASIL_KERJA2',index:'HASIL_KERJA2', editable: true,hidden:false, width:80, align:'center'});
		
	//	colNamesT_lc.push('UNIT2');
     //   colModelT_lc.push({name:'SATUAN2',index:'SATUAN2', editable: false,hidden:false, width:70, align:'center'});
		
		colNamesT_lc.push('AFD');
        colModelT_lc.push({name:'AFD',index:'AFD', editable: false,hidden:false, width:70, align:'center'});
			
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_lc = function()
        {
            jGrid_lc = jQuery("#list_lc").jqGrid(
            {
				url:url+'p_progress_lc/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_lc ,
                colModel: colModelT_lc ,
               	sortname: colNamesT_lc[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_lc"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jGrid_lc.navGrid('#pager_lc',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_lc").ready(loadView_lc);
		
		function post() {
			
		   var postdata = {}; 
		  	postdata['AFD'] = $("#afd").val() ; 
		    postdata['PROGRESS_DATE'] = $("#PROGRESS_DATE").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_lc").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_lc').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_lc").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.ID_PROGRESS_LC!== null){
					postdata['ID_PROGRESS_LC'+i] = data.ID_PROGRESS_LC ;
				}
				postdata['TGL_PROGRESS_LC'+i] = data.TGL_PROGRESS ; 
				postdata['LOCATION_CODE'+i] = data.LOCATION_CODE ; 
		        postdata['ACTIVITY_CODE'+i] = data.ACTIVITY_CODE ;
				postdata['ACTIVITY_DESC'+i] = data.ACTIVITY_DESC; 
				postdata['HASIL_KERJA'+i] = data.HASIL_KERJA; 
				postdata['SATUAN'+i] = data.SATUAN; 
				//postdata['HASIL_KERJA2'+i] = data.HASIL_KERJA2; 
				//postdata['SATUAN2'+i] = data.SATUAN2; 
				postdata['AFD'+i] = data.AFD; 
			
		       }); 
		
		   // Post it all 	   
		  $.post( url+'p_progress_lc/create_lc', postdata,function(status) { 
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
<form id="p_lc">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="PROGRESS_DATE" onchange="cek_act_block()"/></td></tr>
<tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?></td></tr>
<select class="select" name="block" id="block" style="display:none;" onchange="cek_act_block()"></select>
</table>

<br/>

 <table id="list_lc" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_lc" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>
</form>
</body>