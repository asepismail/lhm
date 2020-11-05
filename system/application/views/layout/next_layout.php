<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
        header('expire-header');
        $template_path = base_url().$this->config->item('template_path');

        global $template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <title>PENCATATAN DAN PERHITUNGAN PANEN</title>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>NEWUI/NEWGRID/themes/jquery-ui-1.8.10.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>NEWUI/src/css/nota.css"  />
    
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/jquery-1.4.4.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/jquery.ui.all.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/jquery.layout.js"></script>  
</head>
<body>

<div id="wrapper">
     
    <div id="header">
        <strong><?php echo strtoupper($company_dest);?><br><? echo $judul_header; ?><br/></strong>
    </div>
    
    <div id="prov_menu">
        <ul class="jd_menu jd_menu_slate">
             <?php echo $menu; ?>
            <li >
            <a href="<?= base_url()?>index.php/c_dashboard">Dashboard</a> 
            </li> 
            <li id="logout_menu" >
            <a href="<?= base_url()?>index.php/login/Dologout">&nbsp;&nbsp;&nbsp;Logged as, <?php echo strtoupper($login_id); ?> &nbsp; | &nbsp; Logout</a> 
            </li>
        </ul>
    </div>

    <div id="MyContent" class="container_16">
        <!-- load CONTENT nya MyContent-->
        <?php $this->load->view($view);    ?>
    </div>
</div>

</body>
</html> 

<script type='text/javascript' src='<?= $template_path ?>NEWUI/jquery.jdMenu.js'></script>
<script type='text/javascript' src='<?= $template_path ?>NEWUI/jquery.jqChart.js'></script>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/NEWGRID/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/NEWGRID/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/NEWGRID/jquery.autocomplete.min.js"></script>
<script type="text/javascript" src="<?= $template_path ?>NEWUI/NEWGRID/jqgrid.smart.search.js"></script>

<script type="text/javascript">
jQuery(document).ready(function()
{
    //$('#switcher').themeswitcher();
    
    /*menu*/
        var url = '<?= base_url().'index.php/' ?>';
        $(function(){
            $('ul.jd_menu').jdMenu({    onShow: loadMenu
                                        //onHideCheck: onHideCheckMenu,
                                        //onHide: onHideMenu, 
                                        //onClick: onClickMenu, 
                                        //onAnimate: onAnimate
                                        });
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