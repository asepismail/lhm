<br/>
<form id="gen_x" action="" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td class="labelcell">Periode</td><td>:</td><td class="fieldcell"><? echo $periode ?></td></tr>
<tr><td class="labelcell">Format laporan</td><td>:</td><td class="fieldcell"><select name="jns_laporan" class="select" id="jns_laporan">
<option value='html'>preview</option>
<option value='pdf'>PDF</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"><br />
<input type="button" class="testBtn" id="submitdata" value="Generate"></td><td></td><td></td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="500px"></iframe>


</form>
