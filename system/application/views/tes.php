<? 
	$template_path = base_url().$this->config->item('template_path');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>K P I - Management</title>
  
<!-- dialog -->	
	<link type="text/css" href="<?= $template_path ?>themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="<?= $template_path ?>js/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/ui.core.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/ui.draggable.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/ui.resizable.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/ui.dialog.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/effects.core.js"></script>
	<script type="text/javascript" src="<?= $template_path ?>js/ui/effects.highlight.js"></script>
<!-- end dialog --->

<!--untuk datepicker-->  


<link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>themes/ui.datepicker.css" />
<script src="<?= $template_path ?>js/ui.datepicker.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/date.format.js" type="text/javascript"></script>

<!--end untuk datepicker-->  
<link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>themes/lightness/ui.all.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>themes/stylef.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?= $template_path ?>themes/redmond/jquery-ui-1.7.1.custom.css" />
<style type="text/css">@import "js/jquery.datepick.css";</style>


<style>
html, body {
	margin: 0;			/* Remove body margin/padding */
	padding: 0;
	overflow: hidden;	/* Remove scroll bars on browser window */	
    font-size: 75%;
}
/*Splitter style */


#LeftPane {
	/* optional, initial splitbar position */
	overflow: auto;
}
/*
 * Right-side element of the splitter.
*/
#RightPane {
	padding: 2px;
	overflow: auto;
}
.ui-tabs-nav li {position: relative;}
.ui-tabs-selected a span {padding-right: 10px;}
.ui-tabs-close {display: none;position: absolute;top: 3px;right: 0px;z-index: 800;width: 16px;height: 14px;font-size: 10px; font-style: normal;cursor: pointer;}
.ui-tabs-selected .ui-tabs-close {display: block;}
.ui-layout-west .ui-jqgrid tr.jqgrow td { border-bottom: 0px none;}
.ui-datepicker {z-index:1200;}
</style>


<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.layout.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/grid.locale-en.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.tablednd.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jquery.contextmenu.js" type="text/javascript"></script>



<script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>

<script src="<?= $template_path ?>js/jquery.jqGrid.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jqModal.js" type="text/javascript"></script>
<script src="<?= $template_path ?>js/jqDnR.js" type="text/javascript"></script>
<script language="javascript" src="<?= $template_path ?>js/jquery.form.js"></script>
<script type="text/javascript" src="<?= $template_path.'js/FusionCharts.js' ?>"></script>
<script type="text/javascript" src="<?= $template_path.'js/FusionChartsDOM.js' ?>"></script>

<script type="text/javascript">
var gridimgpath = '<?= $template_path ?>themes/basic/images';
jQuery(document).ready(function(){
    //$('#switcher').themeswitcher();

	$('body').layout({
		resizerClass: 'ui-state-default',
        west__onresize: function (pane, $Pane) {
            jQuery("#west-grid").setGridWidth($Pane.innerWidth()-2);
		}
	});
	$.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
	var maintab =jQuery('#tabs','#RightPane').tabs({
        add: function(e, ui) {
            // append close thingy
            $(ui.tab).parents('li:first')
                .append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
                .find('span.ui-tabs-close')
                .click(function() {
                    maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
                });
            // select just added tab
            maintab.tabs('select', '#' + ui.panel.id);
        }
    });
    jQuery("#west-grid").jqGrid({
        url: "<?= base_url()?>index.php/m_user_menu/menu_list2",
        datatype: "xml",
        height: "auto",
        pager: false,
        loadui: "disable",
        colNames: ["id","Items","url"],
        colModel: [
            {name: "id",width:1,hidden:true, key:true},
            {name: "menu", width:150, resizable: false, sortable:false},
            {name: "url",width:1,hidden:true}
        ],
        treeGrid: true,
		caption: "K P I Management",
        ExpandColumn: "menu",
        autowidth: true,
        //width: 180,
        rowNum: 200,
        ExpandColClick: true,
        treeIcons: {leaf:'ui-icon-document-b'},
        onSelectRow: function(rowid) {
            var treedata = $("#west-grid").getRowData(rowid);
            if(treedata.isLeaf=="true") {
                //treedata.url
                var st = "#t"+treedata.id;
				if($(st).html() != null ) {
					maintab.tabs('select',st);
				} else {
					maintab.tabs('add',st, treedata.menu);
					$(st,"#tabs").load(treedata.url);
				}
            }
        }
    });
	
// end splitter

});
</script>

</head>
<body>

<form action="#" method="post">
  	<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
    <div style="background-image:url(<?= $template_path ?>/themes/base/images/logo.png); background-repeat:no-repeat; height:200;">
    <br/><br/><br/> <br/>
	<div id="mydiv">
	Logged as, <?php echo $login_id; ?>
			<br/>
			<?php echo $company_name; ?>
	</div>
	<br/>

	</div>
		<table id="west-grid"></table><br/>
		<span style="align:center; padding-left:40%;"><a href="http://localhost/lhm/index.php/login/Dologout">Logout</a></span>
	</div> <!-- #LeftPane -->

	

	<div id="RightPane" class="ui-layout-center ui-helper-reset ui-widget-content" ><!-- Tabs pane -->	
    <div id="switcher"></div>
		<div id="tabs" class="jqgtabs">
			<ul>
				<li><a href="#tabs-1">KPI</a></li>
			</ul>
            <div id="tabs-1" style="font-size:12px;">
				<img src="<?= $template_path ?>/themes/provident.jpg" align="center">
			
			</div>
			
			
		</div>
	</div> <!-- #RightPane -->
	</form>	
</body>

</html>