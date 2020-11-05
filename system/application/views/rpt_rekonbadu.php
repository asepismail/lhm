<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_ba_bibitan' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Periode</td><td>:</td><td><input type="text" name="FROM" class="input" id="FROM"/> &nbsp; - <input type="text" name="TO" class="input" id="TO"/></td></tr>
<tr><td></td><td></td><td style="padding-left:5px;padding-top:5px;"><input type="checkbox" id="newwindow" value="1" name="newwindow"> Buka di jendela baru </td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input class="button" type="button" id="submitdata" value="Generate"></td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>


</form>
