<? 
  	$template_path = base_url().$this->config->item('template_path');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?= $template_path ?>themes/gembok2.png">
<title>Provident  Agro Plantation System</title>

</script>
	<link rel="stylesheet" href="<?= $template_path ?>js/login/css/validationEngine.jquery.css" type="text/css">
	<link rel="stylesheet" href="<?= $template_path ?>js/login/css/template.css" type="text/css">
	<script src="<?= $template_path ?>js/login/jquery-1.6.min.js" type="text/javascript"></script>
	<script src="<?= $template_path ?>js/login/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8">
    </script>
	<script src="<?= $template_path ?>js/login/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?= $template_path ?>/js/login/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8">
	</script>
	
	<script>
		var url = "<?= base_url() ?>index.php/";
		var session = "<?= $session ?>";
		
		function checksession(){
			if(session !== ""){
				document.referrer; 
			} else {
				window.location = url + "login";
			}
		}
		
		function beforeCall(form, options){
			if (window.console) 
			console.log("Right before the AJAX form validation call");
			return true;
		}
            
		// Called once the server replies to the ajax form validation request
		function ajaxValidationCallback(status, form, json, options){
			if (window.console) 
			console.log(status);
			if (status === true) {
				window.location = url + "login";
				//alert("the form is valid!");
			}
		}
        		
		jQuery(document).ready(function(){
			jQuery("#formID").validationEngine({
				ajaxFormValidation: true,
				onAjaxFormComplete: ajaxValidationCallback,
				promptPosition : "centerRight", 
				autoPositionUpdate : true
			});
		});
		
	</script>
    
</head>
<body>
<div class="line"></div>
<form id="formID" class="formular" action="<?= base_url()?>index.php/login/doLogin" method="post">
		<fieldset>
			<legend>
				Login Form
			</legend>
			<label>
				<span>Nama Pengguna : </span>
				<input value="" class="validate[required, ajax[ajaxUserCallPhp]] text-input" type="text" name="uname" id="uname" />
			</label>
			<label>
				<span>Password :</span>
                <input type="password" class="validate[required,ajax[ajaxPassCallPhp]] text-input" name="upass" id="upass" />
			</label>
			<br/>
		</fieldset>
			
		<fieldset>
			<legend>
				Site & Module
			</legend>
			<label>
				<div> Pilih site dan module yang akan diakses </div>
                <br/>
            </label>
            <label>
					<span>Perusahaan (Site) :</span>
                	<? if(isset($company)){ echo $company; }?>
			</label>
			<label>
            		<span>Modul Sistem :</span>
					<? if(isset($module)){ echo $module; }?>
            </label>
		</fieldset>
			
		<input class="submit" type="submit" name="Submit" value="Login"/><hr/>
	</form>
    
    
<center class="formular"><p>&copy; 2012<a> Plantation System ver. 2.0</a> -  PT. Provident Agro</p></center>
 </div>
 </div>
<!-- </div>
</div> -->

</body>

</html>