<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_du/generate' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Periode</td><td>:</td><td><input type="text" name="FROM" class="input" id="FROM"/> &nbsp; - <input type="text" name="TO" class="input" id="TO"/></td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" class="button" id="submitdata" value="Generate"></td></tr>
</table>
<br />
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>

</form>
