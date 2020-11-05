<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_du/generate' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td> Aktivitas </td><td>:</td><td>
<select name="activity" class="select" id="activity" style="width:200px;">
<option value=''> -- Aktivitas -- </option>
<option value='all'> -- Semua -- </option>
<option value='rwt'>Rawat</option>
<option value='pnn'>Panen</option>
<option value='tpnn'>Transport Panen</option>
<option value='bbt'>Bibitan</option>
<option value='ssp'>Sisip</option>
<option value='pjtnm'>Project Tanam</option>
<option value='pjbbt'>Project Bibitan</option>
<option value='pjif'>Project Infrastruktur</option>
<option value='umum'>Umum</option>
<option value='vmw'>Kendaraan, Workshop, Mesin</option>
</select>
</td></tr>
<tr><td>Periode</td><td>:</td><td><input type="text" name="FROM" class="input" id="FROM"/> &nbsp; - <input type="text" name="TO" class="input" id="TO"/></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_laporan" class="select" id="jns_laporan">
<option value=''> -- pilih -- </option>
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select></td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" class="button" id="submitdata" value="Generate"></td></tr>
</table>
<br />
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>

</form>
