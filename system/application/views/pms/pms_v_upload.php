<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
        header('expire-header');
        $template_path = base_url().$this->config->item('template_path');
        global $template;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>jQuery Uploadify Demo</title>
<link rel="stylesheet" type="text/css" href="<?= $template_path ?>js/uploadify/uploadify.css" />
<script type="text/javascript" language="javascript" src="<?= $template_path ?>js/uploadify/swfobject.js"></script>
<script src="<?= $template_path ?>js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="<?= $template_path ?>js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>

	<script type="text/javascript" language="javascript">
		$(document).ready(function(){
										
					$("#upload").uploadify({
							uploader: '<?= $template_path ?>js/uploadify/uploadify.swf',
							script: '<?php echo site_url('pms/pms_c_upload/upload2/');?>',
							cancelImg: '<?= $template_path ?>js/uploadify/cancel.png',
							folder: '/uploads',
							scriptAccess: 'always',
							multi: true,
							'onError' : function (a, b, c, d) {
								 if (d.status == 404)
									alert('Could not find upload script.');
								 else if (d.type === "HTTP")
									alert('error '+d.type+": "+d.status);
								 else if (d.type ==="File Size")
									alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
								 else
									alert('error '+d.type+": "+d.text);
								},
							'onComplete'   : function (event, queueID, fileObj, response, data) {
												//Post response back to controller
												$.post('<?php echo site_url('pms/pms_c_upload/upload2/');?>',{filearray: response},function(info){
													$("#target").append(info);  //Add response returned by controller																		  
												});								 			
							}
					});				
		});
	</script>
</head>

<body>
<h1>Uploadify Example</h1>
	
	<?php echo form_open_multipart('upload/index');?>
    
    <p>
    	<label for="Filedata">Choose a File</label><br/>
        <?php echo form_upload(array('name' => 'Filedata', 'id' => 'upload'));?>
        <a href="javascript:$('#upload').uploadifyUpload();">Upload File(s)</a>
    </p>
    
    
    <?php echo form_close();?>
    
	<div id="target">
	
	</div>
</body>
</html>
