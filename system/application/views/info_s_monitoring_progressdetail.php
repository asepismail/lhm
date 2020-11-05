<?php 
	header('expire-header');

	$template_path = base_url().$this->config->item('template_path');
	//$template_path = "http://localhost/lhm/public/";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
	<link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
</head>

<body>

<? if(isset($absen)){ echo $absen; }?>
<div id="elhm">
<table id="list_elhm" style="font-size: 10px;" cellpadding="0" cellspacing="0"></table>
<div id="pager_elhm" class="scroll"></div>
</div>



</body>
