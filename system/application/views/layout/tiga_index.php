<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
        header('expire-header');
        $template_path = base_url().$this->config->item('template_path');
        global $template;
		error_reporting(E_ALL);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
    <link href="<?= $template_path ?>themes_tiga/css/main.css" rel="stylesheet" type="text/css" />
    <link href="<?= $template_path ?>themes_tiga/css/freeow/freeow.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="<?= $template_path ?>themes/base/ui_custom.css" rel="stylesheet" />
    <link type="text/css" href="<?= $template_path ?>js/menu/menu.css" rel="stylesheet" />
    <link type="text/css" href="<?= $template_path ?>js/pace/pace-theme-flash.css" rel="stylesheet" />
    <link rel="shortcut icon" href="<?= $template_path ?>themes/gembok2.png">
    <script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
    <script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
   
	<!-- <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/flot/excanvas.min.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/flot/jquery.flot.categories.js"></script> 
	<script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/flot/jquery.flot.pie.js"></script>-->
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
    <script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/jquery.MultiFile.min.js'></script>
   	<script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/custom.js"></script>
    <script type='text/javascript' src='<?= $template_path ?>js/menu/jquery.hoverIntent.minified.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/menu/jquery.dcmegamenu.1.3.3.js'></script>
    <script type="text/javascript" src="<?= $template_path ?>js/menu/menu.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/jquery.collapsible.min.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/jquery.ToTop.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/jquery.listnav.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_tiga/js/jquery.freeow.min.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>js/pace/pace.min.js"></script>

    <? if(isset($script)) { echo $script; }?>
	<script type="text/javascript">
		jQuery(document).ready(function()
		{       
				 var url = '<?= base_url().'index.php/' ?>';
				$(function() {
					$("#TGL").datepicker({dateFormat:"yy-mm-dd"});
					$("#TO").datepicker({dateFormat:"yy-mm-dd"});
				});
				
				<? if(isset($js)) { echo $js; }?>
		});
	</script>
</head>

<body>

<!-- Top navigation bar -->
<div id="topNav">
    <div class="fixed">
        <div class="wrapper" style="margin-left:10% auto;">
            <div class="welcome"><a href="#" title=""><img src="<?= $template_path ?>themes_tiga/images/userPic.png" alt="" /></a><span><?php echo strtoupper($company_dest);?></span></div>
            <div class="userNav">
            <div id="menu">
    <ul class="menu">
   
     <?php echo $menu; ?> 
     <li class="last"><a href="<?= base_url()?>index.php/login/Dologout" title=""><img src="<?= $template_path ?>themes_tiga/images/icons/topnav/logout.png" alt="" /><span>Logout</span></a></li>
    </ul>
</div>
            </div>
            <div class="fix"></div>
        </div>
    </div>
</div>

<!-- Header -->
<div id="header" class="wrapper">   
</div>


<!-- Content wrapper -->
<div class="wrapperMain">
	<!-- start content themes -->
<div class="contentLHM" style="margin-top:-20px;">

<div class="widget first">
<div class="head">
	<h5 class="iList"><? echo $judul_header; ?></h5>
</div>
<div style="padding:8px; min-height:505px; min-width:1154px; ">
	 <?php $this->load->view($view);    ?>
 </div>    
     </div>
    
</div> 


</body>
</html>