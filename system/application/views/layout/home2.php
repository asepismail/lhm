    
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
        header('expire-header');
        $template_path = base_url().$this->config->item('template_path');

        global $template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> 
 
<head> 
    <title>Plantation System</title> 
    <link rel="shortcut icon" href="<?= $template_path ?>themes/gembok2.png">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

    <!-- dialog -->    
    <link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
    <!-- end dialog --->
    
    <script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
    <script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
    
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
    <script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/jquery.MultiFile.min.js'></script>
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/ajaxfileupload.js"></script> 

    <? if(isset($script)) { echo $script; }?>
</head> 
<body>
<script type="text/javascript">
jQuery(document).ready(function()
{
    //$('#switcher').themeswitcher();
    
    /*menu*/
        var url = '<?= base_url().'index.php/' ?>';
        $(function(){
            $('ul.jd_menu').jdMenu({    onShow: loadMenu });
            $('ul.jd_menu_vertical').jdMenu({onShow: loadMenu, onHide: unloadMenu, offset: 1, onAnimate: onAnimate});
        });

        function onAnimate(show) {
            //$(this).fadeIn('slow').show();
            if (show) {
                $(this)
                    .css('visibility', 'hidden').show()
                        .css('width', $(this).innerWidth())
                    .hide().css('visibility', 'visible')
                .fadeIn('normal');
            } else {
                $(this).fadeOut('fast');
            }
        }

        var MENU_COUNTER = 1;
        function loadMenu() {
            if (this.id == 'dynamicMenu') {
                $('> ul > li', this).remove();
        
                var ul = $('<ul></ul>');
                var t = MENU_COUNTER + 10;
                for (; MENU_COUNTER < t; MENU_COUNTER++) {
                    $('> ul', this).append('<li>Item ' + MENU_COUNTER + '</li>');
                }
            }
        }

        function unloadMenu() {
            if (MENU_COUNTER >= 30) {
                MENU_COUNTER = 1;
            }
        }

        // We're passed a UL
        function onHideCheckMenu() {
            return !$(this).parent().is('.LOCKED');
        }

        // We're passed a LI
        function onClickMenu() {
            $(this).toggleClass('LOCKED');
            return true;
        }
        
        $(function() {
            $("#TGL").datepicker({dateFormat:"yy-mm-dd"});
            $("#TO").datepicker({dateFormat:"yy-mm-dd"});
        });

        <? if(isset($js)) { echo $js; }?>
            
});
/*end menu*/
</script>

<div id="wrapper"> 
    <div class="teks_headline">
        <strong><?php echo strtoupper($company_dest);?><br><? echo $judul_header; ?><br/></strong>
    </div>
    <hr style="border: none 0;border-top: 1px dashed #000;width: 100%;height: 1px; padding-bottom:4px;"/>
    <div id="prov_menu" style="float:right;">
        <ul class="jd_menu jd_menu_slate">
             <?php echo $menu; ?> 
            <li style="float:right;">
            <a href="<?= base_url()?>index.php/login/Dologout">&nbsp;&nbsp;&nbsp;Logged as, <?php echo strtoupper($login_id); ?> &nbsp; | &nbsp; Logout</a> 
            </li>
        </ul>
    </div>
    
    <div id="MyContent">
        <!-- load CONTENT nya -->
        <?php $this->load->view($view);    ?>
    </div>
</div>

</body>
</html>
