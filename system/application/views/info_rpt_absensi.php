<br/>
<form id="gen_x" action="" method="post">

<table border="0" class="teks_">
<tr><td height="20">Kemandoran</td><td>:</td><td style="font-size: 11px;"><? echo $GANG_CODE; ?></td></tr>
<tr><td height="21">Periode</td><td>:</td><td><? echo $periode ?></td></tr>
<tr><td height="20">Absensi Berdasarkan</td><td>:</td><td>
<select name="jns_absensi" id="jns_absensi" class="select">
<option value='hk'>Jumlah HK</option>
<option value='tp'>Type Absensi</option>
</select>
</td></tr>
<tr><td>Jenis laporan</td><td>:</td><td>
<select name="jns_laporan" class="select" id="jns_laporan">
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" class="button" id="submitdata" value="Generate"></td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="500px"></iframe>


</form>
