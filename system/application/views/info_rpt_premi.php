<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

    header('expire-header');
    $template_path = base_url().$this->config->item('template_path');
?>
<br>
<form id="gen_x" action="" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Kemandoran</td><td>:</td><td style="font-size: 11px;"><? echo $GANG_CODE; ?></td></tr>
<tr><td>Periode</td><td>:</td><td><? echo $periode ?></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_laporan" class="select" id="jns_laporan">
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select>
</td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" id="submitdata" value="Generate"></td></tr>
</table>
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>


</form>
