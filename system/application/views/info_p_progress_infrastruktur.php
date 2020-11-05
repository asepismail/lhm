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
	$("#LHM_DATE").datepicker({dateFormat:"yy-mm-dd"});
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


$(function()
{
	var afd = document.getElementById("afd").value;
	$('#afd').chainSelect('#block',url+'p_progress_infrastruktur/dropdownlist_block/'+ $('#afd').val(),
	{ 
		before:function (target) //before request hide the target combobox and display the loading message
		{ 
			$("#loading").css("display","block");
			//$(target).css("display","none");
			
		},
		after:function (target) //after request show the target combobox and hide the loading message
		{ 
			$("#loading").css("display","none");
			//$(target).css("display","inline");
			
		}
	});
});

function cek_act_block(){
	var postdata = {}; 
	var lc =  $('#block').val();
	var tdate = document.getElementById("LHM_DATE").value;
	var tgl = tdate.replace(/-/gi, "");
	
	jQuery("#list_pr").setGridParam({url:url+"p_progress_infrastruktur/read_act/"+tgl+"/"+lc}).trigger("reloadGrid")
}

//-----------------------------------------------------grig-------------------------------------------
		var grid_pr = null;
        var colNamesT_pr = new Array();
        var colModelT_pr = new Array();
		
		colNamesT_pr.push('no');
        colModelT_pr.push({name:'ID',index:'ID', editable: false,hidden:false, width: 40, align:'center'});
		
		colNamesT_pr.push('tanggal');
        colModelT_pr.push({name:'LHM_DATE',index:'LHM_DATE', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_pr.push('blok');
        colModelT_pr.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false,hidden:false, width: 80, align:'center'});
		
		colNamesT_pr.push('kode aktivitas');
        colModelT_pr.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false,hidden:false, width: 120, align:'center'});
		
		colNamesT_pr.push('aktivitas');
        colModelT_pr.push({name:'DESCR',index:'DESCR', editable: false,hidden:false, width: 180, align:'center'});
		
		colNamesT_pr.push('Nilai');
        colModelT_pr.push({name:'HK',index:'HK', editable: false,hidden:false, width:80, align:'center'});
		
		colNamesT_pr.push('Unit');
        colModelT_pr.push({name:'HK',index:'HK', editable: false,hidden:false, width:80, align:'center'});
		
		colNamesT_pr.push('HK');
        colModelT_pr.push({name:'HK',index:'HK', editable: false,hidden:false, width:80, align:'center'});
		
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_pr = function()
        {
            jGrid_pr = jQuery("#list_pr").jqGrid(
            {
				url:url+'p_progress_infrastruktur/read_act/xx/xx',
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

<form id="p_infrastruktur">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" size=15 id="LHM_DATE"/></td></tr>
<tr><td>Kode Afdeling</td><td>:</td><td style="font-size: 11px;"><?php echo $AFD; ?></td></tr>
<tr><td>Blok</td><td>:</td><td><select class="select" name="block" id="block" onchange="cek_act_block()"></select></td></tr>
</table>

<br/>

 <table id="list_pr" class="scroll" style="margin-top:8px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_pr" class="scroll"></div><br/>
</form>
</body>