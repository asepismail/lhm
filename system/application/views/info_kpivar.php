<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
var url = "<?= base_url().'index.php/' ?>";
	
jQuery(document).ready(function(){


});	

function cek_kpi(){
	var postdata = {}; 
	var tipe = document.getElementById("KPIP_DESC").value;
	var bulan = document.getElementById("bulan").value;
	var tahun = document.getElementById("tahun").value;
	jQuery("#list_kpi").setGridParam({url:url+"kpivar/read_grid_kpi/"+tipe+"/"+tahun+bulan}).trigger("reloadGrid")
}
//-----------------------------------------------------grid-------------------------------------------
		var grid_kpi = null;
        var colNamesT_kpi = new Array();
        var colModelT_kpi = new Array();
		
		colNamesT_kpi.push('KPIT_ID');
        colModelT_kpi.push({name:'KPIT_ID',index:'KPIT_ID', editable: false,hidden:true, width: 40, align:'center'});
		
		colNamesT_kpi.push('PERIODE');
        colModelT_kpi.push({name:'PERIODE',index:'PERIODE', editable: false, hidden:true, width: 80, align:'center'});
				
		colNamesT_kpi.push('KPIV_ID');
        colModelT_kpi.push({name:'KPIV_ID',index:'KPIV_ID', editable: false, hidden:true, width: 70, align:'center'});
		
		colNamesT_kpi.push('MEASUREMENT');
        colModelT_kpi.push({name:'KPIV_DESC',index:'KPIV_DESC', editable: false,hidden:false, width: 450, align:'left'});
		
		colNamesT_kpi.push('SATUAN');
        colModelT_kpi.push({name:'KPIV_UOM',index:'KPIV_UOM', editable: false, width: 70, align:'center'});

		colNamesT_kpi.push('NILAI');
        colModelT_kpi.push({name:'KPIV_VALUE',index:'KPIV_VALUE', editable: true,hidden:false, width:90, align:'center'});
		
		colNamesT_kpi.push('KETERANGAN');
        colModelT_kpi.push({name:'KETERANGAN',index:'KETERANGAN', editable: true,hidden:false, width:200, align:'center'});
		
		colNamesT_kpi.push('COMPANY_CODE');
        colModelT_kpi.push({name:'COMPANY_CODE',index:'COMPANY_CODE', editable: false,hidden:true, width:60, align:'center'});
				
		var lastsel; var jdesc1;
		var lRow; var lCol; var i = 0;
		
		var loadView_pb = function()
        {
            jgrid_kpi = jQuery("#list_kpi").jqGrid(
            {
				url:url+'kpivar/read_grid_kpi/',
				mtype : "POST",
                datatype: "json",
                colNames: colNamesT_kpi ,
                colModel: colModelT_kpi ,
               	sortname: colNamesT_kpi[2],
				rownumbers: true, 
              	rowNum: 400,
                height: 320,
                imgpath: gridimgpath,
				pager:jQuery("#pager_kpi"),
            	sortorder: "asc",
				cellEdit: true,
				cellsubmit: 'clientArray',
				forceFit : true,
				onCellSelect : function(iCol){
										
				}
				
            });
            jgrid_kpi.navGrid('#pager_kpi',{edit:false,del:false,add:false, search: false, refresh: true});
			
            $("#alertmod").remove();//FIXME
			
			 
        }
        jQuery("#list_kpi").ready(loadView_pb);
		
		// Post it all 	   
		function post(){
		
		var postdata = {}; 
		    
			var bulan = document.getElementById("bulan").value;
			var tahun = document.getElementById("tahun").value;  	
		  	postdata['PERIODE'] = tahun+bulan ; 
		   	
		    // Data dari grid
		    i=0;
		    s = $("#list_kpi").getDataIDs(); 
			//postdata['jumlah'] = s;
			postdata['jumlahdt'] = jQuery('#list_kpi').getGridParam('records');
		
			   $.each(s, function(n, rowid) { 
		        var data = $("#list_kpi").getRowData(rowid) ; 
		        i=i+1;
				
				if(data.IDP!== null){
					postdata['KPIT_ID'+i] = data.KPIT_ID ;
				}
				postdata['KPIV_ID'+i] = data.KPIV_ID ; 
		        postdata['KPIV_VALUE'+i] = data.KPIV_VALUE ; 
				postdata['KETERANGAN'+i] = data.KETERANGAN; 
				
		   }); 
			   	
		$.post( url+'kpivar/create_kpi', postdata,function(status) { 
				var status = new String(status);
		       	if(status.replace(/\s/g,"") != "") { 
		         		alert(status); 
		          } else { 
				  		alert('data tersimpan');
						cek_kpi();		
		           };  
			} ); 
		
		}	
		
		function hapus(){
			
		    var postdata = {}; 
			var tdate = document.getElementById("TGLKPI").value;
			var tgl = tdate.replace(/-/gi, "");
			var ids = jQuery("#list_kpi").getGridParam('selrow'); 
			var data = $("#list_kpi").getRowData(ids) ; 
		    
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
							jQuery("#list_kpi").setGridParam({url:url+'p_progress_teknik/read_act/'+tgl}).trigger("reloadGrid"); 
			        
							
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
<form id="p_kpi">
<table class="teks_">
<tr><td>Periode</td><td>:</td><td><?  echo $periode; ?></td></tr>
<tr><td>Filter</td><td>:</td><td><? echo $KPIP_DESC; ?></td></tr>
</table>
<br/>
<table id="list_kpi" class="scroll" style="margin-top:0px;" cellpadding="0" cellspacing="0"></table>
                <div id="pager_kpi" class="scroll"></div><br/>
<div id="save" class="scroll" style="float:left;"><input type="button"  id="submitdata" value="simpan" onclick="post()"></div>&nbsp;
<div id="del" class="scroll" style="float:left;"><input type="button"  id="deldata" value="hapus" onclick="hapus()"></div></form>
</form>
</body>