<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_du/generate' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td> Kemandoran </td><td>:</td><td><? echo $dept; ?> 
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
