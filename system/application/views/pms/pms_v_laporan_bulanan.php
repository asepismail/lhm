<? 
    $template_path = base_url().$this->config->item('template_path');  
?>   
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<script type="text/javascript">
var url = "<?= base_url().'index.php/pms/' ?>"; 

jQuery(document).ready(function(){	
}); 

function genRptBAProject(){
				
	var postdata = {};
	var urls;
	var company = jQuery("#company1").val();
	var periode = jQuery("#ftahun").val() + jQuery("#fbulan").val();		
	urls = url+"pms_c_laporan_bulanan/newCetakBulanan/"+company+"/"+periode;
	
	
	if( company == "" ){
		alert("mohon pilih perusahaan");
	} else {
		jQuery("#frmLaporanPJ").attr("src",urls); 
	}
}
			
jQuery( "#rptBaPjBtn" ).click(function() {
		genRptBAProject();
});
</script>

<body>

<p><span>Perusahaan : </span><?php if(isset($dropcompany)){ echo $dropcompany; }?> <p />
<p><span>Periode Laporan : </span><?php if(isset($periode)){ echo $periode; }?> <p />
<p><span><input type="button" id="rptBaPjBtn" value="generate" class="basicbtn" onclick=""/><p />
<br />
<iframe id="frmLaporanPJ" src="" frameborder="no" width="100%" height="450px"></iframe>

</body>
</html>