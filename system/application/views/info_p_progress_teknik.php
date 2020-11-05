<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
	
jQuery(document).ready(function(){

$(function() {
	$("#TGLPROGRESS").datepicker({dateFormat:"yy-mm-dd"});
});
	
});


function cek_act_block(){
	var postdata = {}; 
	var tdate = document.getElementById("TGLPROGRESS").value;
	var tgl = tdate.replace(/-/gi, "");
	jQuery("#list_progress_teknik").setGridParam({url:url+"p_progress_teknik/read_act/"+tgl}).trigger("reloadGrid")

}
		function giveActCode(){		
			var ids = jQuery("#list_progress_teknik").getGridParam('selrow'); 
			var rets = jQuery("#list_progress_teknik").getRowData(ids); 
			var type = rets.ACTIVITY_CODE;
			return type;
		} 
		
//-----------------------------------------------------grid-------------------------------------------
		var grid_progress_teknik = null;
        var colNamesT_pt = new Array();
        var colModelT_pt = new Array();
		
		colNamesT_pt.push('no');
        colModelT_pt.push({name:'IDP',index:'IDP', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_pt.push('TANGGAL');
        colModelT_pt.push({name:'TGL_AKTIVITAS',index:'TGL_AKTIVITAS', editable: false,hidden:false, width: 80, align:'center'});
				
		colNamesT_pt.push('KODE');
        colModelT_pt.push({name:'ACTIVITY_CODE',index:'ACTIVITY_CODE', editable: false, width: 70, align:'center'});
		
		colNamesT_pt.push('AKTIVITAS');
        colModelT_pt.push({name:'COA_DESCRIPTION',index:'COA_DESCRIPTION', editable: false,hidden:false, width: 390, align:'left'});
		
		colNamesT_pt.push('BLOK');
        colModelT_pt.push({name:'LOCATION_CODE',index:'LOCATION_CODE', editable: false, width: 70, align:'center'});

		colNamesT_pt.push('NILAI');
        colModelT_pt.push({name:'NILAI',index:'NILAI', editable: true,hidden:false, width:70, align:'center'});
		
		colNamesT_pt.push('UNIT');
        colModelT_pt.push({name:'UNIT1',index:'UNIT1', editable: false,hidden:false, width:60, align:'center'});
				
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_pb = function()
        {
            jgrid_progress_teknik = jQuery("#list_progress_teknik").jqGrid(
            {
				url:url+'p_progress_teknik/read_act/xx/xx',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_pt ,
                colModel: colModelT_pt ,
               	sortname: colNamesT_pt[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_progress_teknik"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jgrid_progress_teknik.navGrid('#pager_progress_teknik',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_progress_teknik").ready(loadView_pb);
		
		// Post it all 	   
		function post(){
		
		var postdata = {}; 
		    
			//Data dari form untuk gang_activity	  	
		    postdata['TGLPROGRESS'] = $("#TGLPROGRESS").val() ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_progress_teknik").getDataIDs(); 
			postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_progress_teknik').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_progress_teknik").getRowData(rowid) ; 
		        i=i+1;
			
				if(data.IDP!== null){
					postdata['IDP'+i] = data.IDP ;
				}
				postdata['TGL'+i] = data.TGL_PROGRESS ; 
				postdata['LC'+i] = data.LOCATION_CODE ; 
		        postdata['ACT'+i] = data.ACTIVITY_CODE ; 
				postdata['NILAI'+i] = data.NILAI; 
				postdata['UNIT'+i] = data.UNIT1; 
			
		   }); 
			   	
		$.post( url+'p_progress_teknik/create_pb', postdata,function(status) { 
				var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
				  		alert('data tersimpan');
						cek_act_block();		
		           };  
			} ); 
		
		}	
		
		function hapus(){
			
		    var postdata = {}; 
			var tdate = document.getElementById("TGLPROGRESS").value;
			var tgl = tdate.replace(/-/gi, "");
			var ids = jQuery("#list_progress_teknik").getGridParam('selrow'); 
			var data = $("#list_progress_teknik").getRowData(ids) ; 
		    
			postdata['ID'] = data.IDP; 
			postdata['LC'] = data.LOCATION_CODE; 
			postdata['ACT'] = data.ACTIVITY_CODE; 
   			postdata['TGL'] = tdate;
			
			if(data.IDP == undefined){
				alert('silakan pilih salah satu baris data yang ingin dihapus dengan mengklik pada aktivitas!!')
			} else {
				
			
				var answer = confirm ("Hapus Data " + data.LOCATION_CODE + " - " + data.COA_DESCRIPTION + " tgl " + tdate  + " ?" )
				if (answer)
				{
				   $.post( url+'p_progress_teknik/delete', postdata,function(message,status) { 
			       if(status !== 'success') { 
			            	alert('data gagal terhapus.'); 
			          } else { 
							alert('data berhasil terhapus.')
							jQuery("#list_progress_teknik").setGridParam({url:url+'p_progress_teknik/read_act/'+tgl}).trigger("reloadGrid"); 
			        
							
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
<form id="p_teknik">
<table class="teks_">
<tr><td>Tanggal</td><td>:</td><td><input class="input" type="text" onchange="cek_act_block()" size=15 id="TGLPROGRESS"/></td></tr>
</table>
<br/>
<table id="list_progress_teknik" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_progress_teknik" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>&nbsp;
<div id="del" class="scroll" style="float:left;"><input type="button"  id="deldata" value="hapus" onclick="hapus()"></div></form>
</form>
</body>