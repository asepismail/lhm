<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_ba_bibitan' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Inisial Perusahaan</td><td>:</td><td><? if(isset($inisial)) { echo $inisial; } ?></td></tr>
<tr><td>Periode</td><td>:</td><td><? if(isset($periode)) { echo $periode; } ?></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_rpt" class="select" id="jns_rpt" style="width:270px">
<!-- <option value='ws'>Running Workshop</option>
<option value='vh1'>Running Kendaraan Tahap 1 (Langsir)</option>
<option value='vh2'>Running Kendaraan Tahap 2</option>
<option value='ma'>Running Mesin</option> -->
<option value='pay'>Export Data Running Gaji</option>
<option value='hkn'>Export Data Running Gaji (HKNE)</option>
<option value='ast'>Export Data Running Astek</option>
<option value='pph'>Export Data Running PPh 21</option>
<option value='zis'>Export Data Running Zakat</option>
</select>
</td></tr>
<tr><td>Jenis Export</td><td>:</td><td><select name="jns_eksport" class="select" id="jns_eksport">
<option value='excell'>excell</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input class="button" type="button" id="submitdata" name="submitdata" value="Generate"></td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>


</form>
