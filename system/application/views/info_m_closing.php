<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

    header('expire-header');
    $template_path = base_url().$this->config->item('template_path');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plantation System</title>
    <?php echo $CSSPath; ?> 
</head>
<body>
<?php echo $JSPath."\n"; ?>
<script type="text/javascript">
//###################### MENU FORM FUNCTION ####################### 
jQuery(document).ready(function()
{
    $(function(){
        $('ul.jd_menu').jdMenu({    onShow: loadMenu});
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
    /*end menu*/
});

$(function(){
    $("#ajax_display").ajaxStart(function(){
            $('#htmlExampleTarget').hide();             
            $(this).html("<img alt='' src='themes/wait.gif' ><br >Waiting ...");
        });
        
    $("#ajax_display").ajaxSuccess(function(){
           $(this).html('');
     });
    $("#ajax_display").ajaxError(function(url){
           alert('jQuery ajax is error ');
     });
});
//###################### END MENU FORM FUNCTION #######################
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
    $("#TGLStart").datepicker({dateFormat:"yy-mm-dd"}); 
    $("#TGLEnd").datepicker({dateFormat:"yy-mm-dd"}); 
});
</script>

<script type="text/javascript">
//############################ START BUTTON FUNCTION ################################# 
function procClosing()
{
    var closingmsg="proses ini akan menutup transaksi yang sudah dibuat, sehingga transaksi tidak bisa dirubah lagi.";
    alert (closingmsg);
    
    var confirmsg = confirm("Proses Closing");
    if(confirmsg)
    {
        var datapost={};
        var tfrom = $("#TGLStart").val();
        var elem = tfrom.split('-');
        from = elem[0]+elem[1]+elem[2];
                                
        var tto = $("#TGLEnd").val();
        var elem2 = tto.split('-');
        to = elem2[0]+elem2[1]+elem2[2];
        
        var tipe = $("#jns_laporan").val();
        
        datapost['STARTDATE']=from;
        datapost['ENDDATE']=to;
        datapost['TIPE']=tipe;
        $.post('<?php echo htmlentities($_SERVER['PHP_SELF']) ?>/update_closing/',
            datapost,
            function(message) {
                alert (message); 
                /*if(message !=0) 
                { 
                    alert('update closing gagal'); 
                } 
                else 
                { 
                    alert('data transaksi berhasil ter-closing')   
                }; */
              });    
    }
    
}
//############################ END BUTTON FUNCTION ################################   
</script>

<script type="text/javascript">
//########################### START APPROVAL FUNCTION ############################# 

//########################### END APPROVAL FUNCTION ############################# 
</script>
<div id="head">
    <?php
        echo $head."\n";
    ?>
</div>
<div id="menu" style='float:right'>
    <?php
        echo $menu."\n";
    ?>
</div>
<br>
<div id"gridSearch">  
    <!--<div><?php //echo $search; ?></div> -->
    
</div>
<div id="mainGrid" style=" margin-right: auto; width: 100%;">
    
</div> 
<div id="_form">
<table border="0" class="teks_" style="padding:7px;">
<tr><td>Tanggal</td><td>:</td>
<td>
<input class="input" type="text" style="height:18" size=15 id="TGLStart" /> &nbsp; -
<input class="input" type="text" style="height:18" size=15 id="TGLEnd" />
</td>
</tr>
<tr>
    <td>Jenis Closing</td>
    <td>:</td>
    <td>
        <?php echo $CLOSING_TYPE; ?>
    </td>
</tr>
<tr>
    <td></td><td></td><td><br /><input style="margin-left:5px; " type="button" class="button" id="submitdata" value="Proses Closing" onclick="procClosing()"></td>
</tr>
</table>  
</div>


</body>
