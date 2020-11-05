<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_progress/generate' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Tanggal</td><td>:</td>
<td>
<input class="input" type="text" style="height:18" size=15 id="TGL" /> &nbsp; -
<input class="input" type="text" style="height:18" size=15 id="TO" />
</td>
</tr>
<tr><td>Jenis Progress</td><td>:</td><td>
<select name="jns_laporan" class="select" style="width:180" id="jns_laporan">
<option value=''> -- pilih -- </option>
<option value='rawat'>Rawat</option>
<option value='panen'>Panen</option>
<option value='trans_panen'>Transport Panen</option>
<option value='sisip'>Sisip</option>
<option value='bibitan'>Bibitan</option>
<option value='tanam'>Tanam</option>
<option value='rwtif'>Rawat Infrastruktur</option>
<option value='pj_inf'>Project Infrastruktur</option>
<option value='pj_bibitan'>Project Bibitan</option>
</select>
</td></tr>
<tr><td>AFD</td><td>:</td><td><? if(isset($AFD)){ echo $AFD;  } ?></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="tipe_laporan" class="select" id="tipe_laporan">
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
