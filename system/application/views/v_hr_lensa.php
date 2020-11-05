<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_lhm/generate' ?>" method="post">
<fieldset style="width:450px;">
<legend class="teks_" style="padding:1em; padding-left:1em;"> Generate Report </legend>
<table border="0" class="teks_" style="padding:7px;">
<tr><td width="130px" style="padding-left:1em;">Perusahaan</td><td width="20px">:</td><td>
<?php if(isset($dropcompany)) { echo $dropcompany; }  ?>
<tr><td style="padding-left:1em;">Kriteria</td><td>:</td><td><select name='jns_kriteria' class='select' id='jns_kriteria' style='width:190px;' >
		<option value=''> -- Pilih -- </option>
        <option value='1'> -- Data Karyawan -- </option>
        <option value='2'> -- Data Gaji -- </option>
        <option value='3'> -- Eksport ESPT Karyawan Bulanan -- </option>
        <option value='4'> -- Eksport ESPT Karyawan BHL -- </option>
	 <option value='5'> -- Eksport Data Bonus Site -- </option>
	 <option value='6'> -- Eksport Data ESPT Bulanan + Bonus-- </option>

        </select></td></tr>
<tr><td style="padding-left:1em;">Periode</td><td>:</td>
<td>
<?php if(isset($periode)) { echo $periode; }  ?>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br />
<input type="button" id="submitdata" value="Generate">
<input type="button" id="complete" value="Complete">
</td></tr>
</table>
<br />

</fieldset>
</form>
</body>