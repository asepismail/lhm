<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
        header('expire-header');
        $template_path = base_url().$this->config->item('template_path');
        global $template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Site Plantation System | Modul Pengajuan Project</title>
    <link rel="shortcut icon" href="<?= $template_path ?>themes/gembok2.png">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/reset.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/text.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/grid.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/layout.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/nav.css" media="screen" />
    
    <link rel="stylesheet" type="text/css" href="<?= $template_path ?>themes_pms/css/smart_tab_vertical.css" media="screen" />
    
    <link href="<?= $template_path ?>themes_pms/css/table/demo_page.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="<?= $template_path ?>themes/base/ui.css" rel="stylesheet" />
    <!-- upload -->
    
    <!-- end upload -->
    <!-- BEGIN: load jquery -->
    <script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
    <script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
    <script src="<?= $template_path ?>js/jquery.ui.all.js" type="text/javascript"></script>
    <script src="<?= $template_path ?>js/jquery.form.js"></script>
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_grid.js"></script>
    <script language="javascript" src="<?= $template_path ?>js/js_compile/prov_jqAll.js"></script>
    <script type='text/javascript' src='<?= $template_path ?>js/thickbox-compressed.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/js_compile/prov_autocomp.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/timepicker.js'></script>
    <script type='text/javascript' src='<?= $template_path ?>js/jquery.MultiFile.min.js'></script>
    <script type="text/javascript" src="<?= $template_path ?>NEWUI/ajaxfileupload.js"></script>
    <script type="text/javascript" src="<?= $template_path ?>themes_pms/js/jquery.smartTab.min.js"></script>

     
    <script src="<?= $template_path ?>themes_pms/js/setup.js" type="text/javascript"></script>
    
    <? if(isset($script)) { echo $script; }?>
    
    <script type="text/javascript">
    $(document).ready(function () {
            //setupLeftMenu();
		goUrl('main_c_pms');
		setSidebarHeight();
		
		
    });
    </script>
    <script>
	jQuery.ajaxSetup ({
		cache: true
	});
	var ajax_load = "<img class='loading' src='<?= base_url() ?>public/themes_pms/img/loader10.gif' alt='loading...' />";
	
//	load() functions
	function goUrl(url){
		var loadUrl = "<?= base_url().'pms/' ?>";
		var url = loadUrl + url;
	//$("#load_basic").click(function(){
		jQuery("#results").empty();
		jQuery("#results").html(ajax_load).load(url);
	//});
	}	
</script>
</head>
<body>
    <div class="container_12">
        <div class="grid_12 header-repeat">
            <div id="branding">
                <div class="floatleft" style="margin-top:-10px; margin-left:10px;">
                  <span style="font-size:15px; color:#FFF; font-weight:bold">Provident-Agro : Modul Pengajuan Project</span><br/>
                  <span style="font-size:15px; color:#FFF; font-weight:bold"><?=$this->session->userdata('DCOMPANY_NAME');?></span>
                 </div>
            <div class="floatright">
                    <div class="floatleft">
                        <img src="<?= $template_path ?>themes_pms/img/img-profile.jpg" alt="Profile Pic" /></div>
                    <div class="floatleft marginleft10">
                        <ul class="inline-ul floatleft">
                            <li>Hello <?=$this->session->userdata('LOGINID');  ?></li>
                            <li><a href="#">Config</a></li>
                            <li><a href="<?= base_url()?>index.php/login/Dologout">Logout</a></li>
                        </ul>
                        <br />
                        <span class="small grey">Dept : <?=$this->session->userdata('USER_DEPT');  ?>, Last Login: 3 hours ago</span>
                    </div>
                </div>
                <div class="clear">
                </div>
            </div>
        </div>
        <div class="clear">
        </div>
       <div class="grid_12">
           <ul class="nav main">
                <li class="ic-dashboard"><a href="dashboard.html"><span>Monitoring Project</span></a>
                	<ul>
                        <li><a href="#" onclick="goUrl('pms_c_daftpengajuan')">Monitoring Pengajuan</a> </li>
                        <li><a href="#" onclick="goUrl('pms_c_laporan_bulanan')">Berita Acara Bulanan PJ</a> </li>
                    </ul>
                </li>
                <li class="ic-form-style"><a href="javascript:"><span>Data Master</span></a>
                    <ul>
                        <li><a href="#" onclick="goUrl('main_c_pms')">Daftar Project Aktif</a> </li>
                        <li><a href="#" onclick="goUrl('pms_c_master_budget')">Data Master Budget</a> </li>
                    </ul>
                </li>
                <? if($company_code != "PAG") { ?>
				<li class="ic-grid-tables"><a href="#"><span>Pengajuan Project</span></a>
                	<ul>
                        <li><a href="#" onclick="goUrl('pms_c_pengajuan')">Pengajuan Baru</a> </li>
                        <li><a href="#" onclick="goUrl('pms_c_revisi')">Pengajuan Revisi</a> </li>
                        <li><a href="#" onclick="goUrl('pms_c_closing')">Pengajuan Penutupan Project</a> </li>
                    </ul>
                </li>
                <li class="ic-charts"><a href="charts.html"><span>Pengajuan RM</span></a>
                	<ul>
                        <li><a href="#" onclick="goUrl('pms_c_monitoring_rm')">Monitoring Pengajuan Rawat</a></li>
                        <li><a href="#" onclick="goUrl('pms_c_pengajuan_rm')">Pengajuan Rawat</a> </li>
					</ul>
                </li>
                <!-- <li class="ic-grid-tables"><a href="table.html"><span>Data Table</span></a></li>
                <li class="ic-gallery dd"><a href="javascript:"><span>Image Galleries</span></a>
               		 <ul>
                        <li><a href="image-gallery.html">Pretty Photo</a> </li>
                        <li><a href="gallery-with-filter.html">Gallery with Filter</a> </li>
                    </ul>
                </li> -->
                <li class="ic-notifications"><a href="notifications.html"><span>Notifikasi</span></a></li>
				 <? } ?>
            </ul>
        </div>
        <div class="clear">
        </div>
        
        <div class="grid_10">
            <div class="box round first grid">
              <div class="block"> 
              <div id="results" class="contentinner content-dashboard" style="padding-left:5px;">
              		 <?php $this->load->view($view);    ?>
              </div><!--contentinner-->
          
              <?php //$this->load->view($view);    ?>
              </div>
            </div>
        </div>
        <!-- <div class="clear">
        </div>
        <div id="site_info" style="margin-bottom:-22px;">
        <p>
            Provident-Agro : Plantation System  |  Modul Pengajuan Project
        </p>
    	</div> -->
    </div>   
</body>
</html>
