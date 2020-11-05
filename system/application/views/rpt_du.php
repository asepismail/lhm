<? $template_path = base_url().$this->config->item('template_path'); ?>
<br/>
<form id="gen_x" action="<?= base_url().'index.php/rpt_du/generate' ?>" method="post">

<table border="0" class="teks_" style="padding:7px;">
<tr><td>Kemandoran</td><td>:</td><td>
<?php echo $GANG_CODE; ?></td></tr>
<!-- <tr><td>Periode</td><td>:</td><td><? echo $periode ?></td></tr> -->
<tr><td>Periode</td><td>:</td><td><input type="text" name="FROM" class="input" id="FROM"/> &nbsp; - <input type="text" name="TO" class="input" id="TO"/></td></tr>
<tr><td>Jenis laporan</td><td>:</td><td><select name="jns_laporan" class="select" id="jns_laporan">
<option value=''> -- pilih -- </option>
<option value='html'>preview</option>
<option value='excell'>excell</option>
<option value='pdf'>PDF</option>
</select></td></tr>
<tr><td style="padding-left:1em;"></td><td></td><td><br /><input type="button" class="button" id="submitdata" value="Generate"><?php 
$usrole = $this->session->userdata('USER_LEVEL');
if($usrole == 'SAD' || $usrole == 'SAS'  || $usrole == 'PAYROLL' || $usrole == 'ADMSITE') {?>
&nbsp; <input type="button" class="button" id="regenerategc" value="Generate Kemandoran">
<?php } ?></td></tr>
</table>
<br />
<iframe id="frame" src="" frameborder="no" width="100%" height="400px"></iframe>

</form>

<!-- progress bar -->    
<div id="progressbar">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><img id="load" src="<?= $template_path ?>themes/base/images/ani_loading.gif" align="middle" /></td></tr>
<tr><td align="center"><span id="msg" style="text-align:justify"></span></td></tr></table>
</div> 
<!-- end progress bar -->