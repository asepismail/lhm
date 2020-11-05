<script type="text/javascript">
jQuery(document).ready(function(){
var url = '<?= base_url().'index.php/' ?>';
			
			$(function()
			{
				var modul = document.getElementById('modul').value;
				$('#modul').chainSelect('#submodul',url+'open_close_period/dropdownmod/'+ $('#modul').val(),
				{ 
					before:function (target) 
					{ 
						$('#loading').css('display','block');
					},
					after:function (target) 
					{ 
						$('#loading').css('display','none');
					}
				});
			});
		});

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
<br/>
<form id="gen_x" action="<?= base_url().'index.php/open_close_period' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Status</td><td>:</td><td style="font-size: 11px;">
<select name="status" class="select" id="modul">
<option value=''> -- pilih -- </option>
<option value='open'>Open</option>
<option value='close'>Close</option>
</select>
</td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode ?></td></tr>
<tr><td>Modul</td><td>:</td><td><select name="modul" class="select" id="modul" style="width:140px" onChange="cek()">
<option value=''> -- pilih -- </option>
<option value='LHM'>LHM</option>
<option value='BK'>Buku Kendaraan</option>
<option value='BM'>Buku Mesin</option>
<option value='BW'>Buku Workshop</option>
<option value='PROG'>Progress</option>
</select>
</td></tr>
<tr><td>Sub</td><td>:</td><td><select name="submodul" class="select" id="submodul" style="width:100px">
<option value=''> -- pilih -- </option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" id="submitdata" value="Generate"></td></tr>
</table>

</form>
