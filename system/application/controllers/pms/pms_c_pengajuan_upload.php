<?php
class pms_c_upload extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
        $this->load->library('global_func');
		$this->load->helper('form');
        $this->load->helper('language');
        $this->load->helper('url');
        $this->load->helper('object2array');
        $this->load->library('session');
		$this->load->database();
	}
	
	/*
	*	Handles JSON returned from /js/uploadify/upload.php
	*/
	public function uploadify()
	{
		
		//Decode JSON returned by /js/uploadify/upload.php
		$file = $this->input->post('filearray');
		$data['json'] = json_decode($file);
		
		$this->load->view('pms/pms_v_upload',$data);
	}
	
	/* baru */
	function set_filename($path, $filename, $file_ext, $encrypt_name = FALSE)
	{
		if ($encrypt_name == TRUE)
		{		
			mt_srand();
			$filename = md5(uniqid(mt_rand())).$file_ext;	
		}
	
		if ( ! file_exists($path.$filename))
		{
			return $filename;
		}
	
		$filename = str_replace($file_ext, '', $filename);
		
		$new_filename = '';
		for ($i = 1; $i < 100; $i++)
		{			
			if ( ! file_exists($path.$filename.$i.$file_ext))
			{
				$new_filename = $filename.$i.$file_ext;
				break;
			}
		}

		if ($new_filename == '')
		{
			return FALSE;
		}
		else
		{
			return $new_filename;
		}
	}
	
	function prep_filename($filename) {
	   if (strpos($filename, '.') === FALSE) {
		  return $filename;
	   }
	   $parts = explode('.', $filename);
	   $ext = array_pop($parts);
	   $filename    = array_shift($parts);
	   foreach ($parts as $part) {
		  $filename .= '.'.$part;
	   }
	   $filename .= '.'.$ext;
	   return $filename;
	}
	
	function get_extension($filename) {
	   $x = explode('.', $filename);
	   return '.'.end($x);
	} 

	
	function doUpload(){
		echo $this->uri->segment(4);
		/* if (!empty($_FILES)) {
				  $path = '../uploads/';
				 $file_temp = $_FILES['Filedata']['tmp_name'];
				 $file_name = date("Y-m-d_H-i-s").$this->prep_filename($_FILES['Filedata']['name']);
				 $file_ext = $this->get_extension($_FILES['Filedata']['name']);
				 $real_name = $file_name;
				 $newf_name = $this->set_filename($path, $file_name, $file_ext);
				 $file_size = round($_FILES['Filedata']['size']/1024, 2);
				 $file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['Filedata']['type']);
				 $file_type = strtolower($file_type);
				 $targetFile =  str_replace('//','/',$path) . $newf_name;
				 if ( ! is_dir($path)) {
					 mkdir($path);
				 }
				  move_uploaded_file($file_temp, 'uploads/'.$_FILES['Filedata']['name']);
			  
				 $filearray = array();
				 $filearray['file_name'] = $newf_name;
				 $filearray['real_name'] = $real_name;
				 $filearray['file_ext'] = $file_ext;
				 $filearray['file_size'] = $file_size;
				 $filearray['file_path'] = $targetFile;
				 $filearray['file_temp'] = $file_temp;
		
		   		 $json_array = json_encode($filearray);
			     echo $json_array;
		}else{
				echo "file kosong";	
		} */
	}
}
/* End of File /application/controllers/upload.php */