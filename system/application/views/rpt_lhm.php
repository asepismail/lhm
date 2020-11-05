<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_lhm/generate' ?>" method="post">
<fieldset style="width:450px;">
<legend class="teks_" style="padding:1em; padding-left:1em;"> Generate Report </legend>
<table border="0" class="teks_" style="padding:7px;">
<tr><td width="130px" style="padding-left:1em;">Jenis Laporan</td><td width="20px">:</td><td>
<? if(isset($d_export_data)){ echo $d_export_data; } ?>
<!-- <select id="jns_rpt" class="select" style="width:190px;">
<?php if($login_id == "adipranoto") { ?>
<option value="bk">Buku Kendaraan</option>
<option value="bm">Buku Mesin</option>
<option value="bw">Buku Workshop</option>
<option value="bkt">Buku Kontraktor</option>
<?php } else { ?>
<option value="lhm">LHM</option>
<option value="bk">Buku Kendaraan</option>
<option value="bm">Buku Mesin</option>
<option value="bw">Buku Workshop</option>
<option value="bkt">Buku Kontraktor</option>
<option value="prg">Progress</option>
<option value="ba">Berita Acara Gaji</option>
<option value="rlhm">Detail Biaya Lhm</option>
<option value="rekaplhm">Rekap Biaya Lhm</option>
<option value="emp">Buku Karyawan</option>
<option value="prj">Master Data Project</option>
<?php } ?>
</select> --> </td></tr>
<tr><td style="padding-left:1em;">Periode</td><td>:</td>
<td>
<input class="input" type="text" style="width:7em;" size=10 id="RPTFROM" /> - <input class="input" style="width:7em;" type="text" size=10 id="RPTTO" />
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" id="submitdata" value="Generate"></td></tr>
</table>
<br />

</fieldset>
</form>
</body>