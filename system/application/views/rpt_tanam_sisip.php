<? $template_path = base_url().$this->config->item('template_path'); ?>
<script type="text/javascript">
$(document).ready(function() {
var url = "<?= base_url().'index.php/' ?>";
	$('#FROM').datepicker({dateFormat:'yy-mm-dd'});
    $('#TO').datepicker({dateFormat:'yy-mm-dd'});
	document.getElementById("hr").style.display = 'none';
	document.getElementById("bl").style.display = 'none';
	
	$("#jns_periode").change(function() {
		if($("#jns_periode").val() == "harian"){
			hideBulanan();
		} else {
			hideHarian();	
		}
	});
	
	jQuery('#submitdata').click(function (){
		var afd = $('#afd').val();
		var jns_laporan = $('#jns_laporan').val();	
		var jns_periode = $('#jns_periode').val();
		var afdeling = $('#s_afd').val()
		var urls = "";
		if(jns_periode == "bulanan"){
			periode = $('#tahun').val() + $('#bulan').val();
		} else {
			periode = $('#FROM').val() + '/' + $('#TO').val()
		}
		
		if ( jns_laporan == 'html'){
			urls = url + 'rpt_tanam_sisip/tnm_sisip_preview/' + jns_periode + '/' + periode + '/' + afdeling; 
			$('#frame').attr('src',urls);  
		} else if ( jns_laporan == 'excell'){
			urls = url + 'rpt_tanam_sisip/tnm_sisip_xls/' + jns_periode + '/' + periode + '/' + afdeling;
			$.download(urls,'');
		} else if ( jns_laporan == 'pdf'){
			urls = url + 'rpt_tanam_sisip/tnm_sisip_pdf/' + jns_periode + '/' + periode + '/' + afdeling;
			$('#frame').attr('src',urls);  
		}
	});
});
			
function hideHarian(){
	var rowHarian = document.getElementById("hr");
	var rowBulanan = document.getElementById("bl");
	rowHarian.style.display = 'none';
	rowBulanan.style.display = '';
}

function hideBulanan(){
	var rowHarian = document.getElementById("hr");
	var rowBulanan = document.getElementById("bl");
	rowHarian.style.display = '';
	rowBulanan.style.display = 'none';
}
</script>

<br/>
<br/>
<table border="0" class="teks_" style="padding:7px;">
<tr><td>Jenis Periode</td><td>:</td><td>
<select name="jns_periode" class="select" id="jns_periode">
<option value='harian'>Harian</option>
<option value='bulanan'>Bulanan</option>
</select>
</td></tr>
<tr id="hr"><td>Periode Harian &nbsp; &nbsp;</td><td>:</td><td>
<input type="text" name="FROM" class="input" id="FROM"/> &nbsp; - <input type="text" name="TO" class="input" id="TO"/>
</td></tr>
<tr id="bl"><td>Periode Bulanan &nbsp; &nbsp; </td><td>:</td><td><? echo $periode ?></td></tr>
<tr><td>Afdeling &nbsp; &nbsp; </td><td>:</td><td><? echo $afdeling ?></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_laporan" class="select" id="jns_laporan">
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br />
<input type="button" id="submitdata" value="Generate"> &nbsp;
<input type="button" id="regenerate" value="Regenerate Laporan">
</td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>
